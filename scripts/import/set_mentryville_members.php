/**
 * Mentryville #12135: its members, from the legacy index.
 *
 * THE INDEX IS THE AUTHORITY. Leon's Pico Canyon / Mentryville index page,
 * /scvhistory/pico.htm on the Reggie mirror, says what belongs to this
 * collection. The breadcrumb ("> PICO CANYON", "> MENTRYVILLE") says what a
 * page is filed under, which is a different thing: the Southern Oaks tract
 * photographs carry the breadcrumb and the index does not list them. So the
 * breadcrumb is used only to report the difference, never to add a member.
 *
 * The index is read fresh from the mirror each run, and its sha256 printed, so
 * the membership can always be traced to the exact page it came from.
 *
 * HOW MEMBERSHIP IS HELD. partOfCollection on the member, which is what
 * templates/collections/_topic.twig reads. An article is also listed in
 * articlesInCollection, which accepts articles only, to keep the pair the
 * other collections keep. A document can join only once
 * add_document_collection_field.php has put partOfCollection on its layout;
 * until then documents are reported as waiting, not skipped silently.
 *
 * Additive: nothing already in a member's partOfCollection is removed.
 * Refuses to run against a frozen collection.
 *
 * Needs the Reggie mount in the container (ddev restart after a remount).
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_mentryville_members.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$COLLECTION = 12135;
$INDEX = '/mnt/reggie/scvhistory.com/scvhistory/pico.htm';
$CHROME = ['scvhistory', 'key', 'bibliography', 'publications', 'index', 'pico'];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();
$col = \craft\elements\Entry::find()->id($COLLECTION)->section('collections')->status(null)->one();
if (!$col) { echo 'collection #' . $COLLECTION . ' not found' . PHP_EOL; return; }
if ($col->getFieldValue('collectionFrozen')) { echo 'collection #' . $COLLECTION . ' is frozen; refusing' . PHP_EOL; return; }
if (!is_file($INDEX)) { echo 'the index is not on the mount: ' . $INDEX . '. Run: ddev restart' . PHP_EOL; return; }

$raw = (string)file_get_contents($INDEX);
echo 'index ' . $INDEX . PHP_EOL . 'sha256 ' . hash('sha256', $raw) . PHP_EOL;
preg_match_all('~<a\s[^>]*?href\s*=\s*["\']?([^"\'\s>]+)["\']?[^>]*>~is', $raw, $m);
$keys = [];
$external = [];
foreach ($m[1] as $href) {
    if (!preg_match('~([A-Za-z0-9_\-]+)\.html?$~', $href, $k)) { continue; }
    $key = strtolower($k[1]);
    if (in_array($key, $CHROME, true) || isset($keys[$key])) { continue; }
    if (preg_match('~^https?://~', $href) && !str_contains($href, 'scvhistory.com')) { $external[$href] = true; continue; }
    $keys[$key] = $href;
}
echo 'index lists ' . count($keys) . ' scvhistory.com pages, and ' . count($external) . ' elsewhere (not records here)' . PHP_EOL;

$hasField = function ($el, string $h): bool { return (bool)$el->getFieldLayout()->getFieldByHandle($h); };

/* A legacy page is found by legacyKey, then by legacyUrl. sw_petermentre is one
   page and four documents; every record with that legacyUrl is a member. */
$members = []; $missing = [];
foreach ($keys as $key => $href) {
    $found = \craft\elements\Entry::find()->status(null)->legacyKey($key)->all();
    $found = array_merge($found, \craft\elements\Entry::find()->status(null)
        ->legacyUrl(['*/' . $key . '.htm', '*/' . $key . '.html'])->all());
    /* The collection's own page (mstory) is not a member of itself, and the
       person page (ch1070) is the person, who is not a collection member. */
    if (array_filter($found, fn($e) => $e->id === $COLLECTION || $e->section->handle === 'persons')) { continue; }
    if (!$found) { $missing[] = $key; continue; }
    foreach ($found as $e) { $members[$e->id] = [$e, $key]; }
}

$plan = []; $waiting = []; $already = 0;
foreach ($members as $id => [$e, $key]) {
    if (!$hasField($e, 'partOfCollection')) { $waiting[] = $e; continue; }
    $held = $e->partOfCollection->status(null)->ids();
    if (in_array($COLLECTION, $held, true)) { $already++; continue; }
    $plan[] = $e;
}
$artIds = $col->articlesInCollection->status(null)->ids();
$addArt = array_values(array_filter(array_map(fn($x) => $x[0], $members),
    fn($e) => $e->section->handle === 'articles' && !in_array($e->id, $artIds, true)));

echo PHP_EOL . 'members found in Craft: ' . count($members) . PHP_EOL;
echo '   already members: ' . $already . PHP_EOL;
echo '   to add through partOfCollection: ' . count($plan) . PHP_EOL;
foreach ($plan as $e) { echo '      #' . $e->id . ' ' . $e->section->handle . '  ' . $e->title . PHP_EOL; }
echo '   articles to list in articlesInCollection: ' . count($addArt) . PHP_EOL;
echo '   WAITING, the layout has no partOfCollection: ' . count($waiting) . PHP_EOL;
foreach ($waiting as $e) { echo '      #' . $e->id . ' ' . $e->section->handle . '  ' . $e->title . PHP_EOL; }
if ($waiting) { echo '      run add_document_collection_field.php first' . PHP_EOL; }
echo 'listed in the index, no record in Craft: ' . count($missing) . PHP_EOL;
echo '   ' . implode(' ', $missing) . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$failed = [];
foreach ($plan as $e) {
    $e->setFieldValue('partOfCollection', array_merge($e->partOfCollection->status(null)->ids(), [$COLLECTION]));
    if (!$elements->saveElement($e)) { $failed[] = '#' . $e->id . ' ' . json_encode($e->getFirstErrors()); }
}
if ($addArt) {
    $col->setFieldValue('articlesInCollection', array_merge($artIds, array_map(fn($e) => $e->id, $addArt)));
    if (!$elements->saveElement($col)) { $failed[] = 'collection ' . json_encode($col->getFirstErrors()); }
}

$short = [];
foreach ($plan as $e) {
    $f = \craft\elements\Entry::find()->id($e->id)->status(null)->one();
    if (!in_array($COLLECTION, $f->partOfCollection->status(null)->ids(), true)) { $short[] = '#' . $e->id; }
}
$c = \craft\elements\Entry::find()->id($COLLECTION)->status(null)->one();
foreach ($addArt as $e) { if (!in_array($e->id, $c->articlesInCollection->status(null)->ids(), true)) { $short[] = 'articlesInCollection #' . $e->id; } }
$bad = array_merge($failed, $short);
echo PHP_EOL . 'READ-BACK ' . ($bad ? 'SHORT' : 'OK') . ': ' . (count($plan) - count($short)) . ' of ' . count($plan) . ' members' . PHP_EOL;
foreach ($bad as $b) { echo '   FAIL ' . $b . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('set_mentryville_members.php', count($plan), $bad ? 'SHORT: ' . implode('; ', $bad) : 'verified ' . count($plan) . ' of ' . count($plan),
    'from pico.htm sha256 ' . substr(hash('sha256', $raw), 0, 12) . '; ' . count($waiting) . ' waiting on the document field; ' . count($missing) . ' not in Craft');
if ($bad) { throw new \RuntimeException('set_mentryville_members: read-back failed'); }
