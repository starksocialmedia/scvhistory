/**
 * Gives the 80 role entries their titles back.
 *
 * add_roles_schema.php created the entry type without a title field. Craft 5
 * defaults hasTitleField to false, my assignment did not survive the second
 * save of the type, and the run reported "verified 32 of 32" because it checked
 * the relations and never looked at what it had named them. Eighty vocabulary
 * entries exist, carry the right Wikidata ids, and are called nothing.
 *
 * HOW THEY ARE MATCHED BACK
 *
 * By position. Entry ids ascend in exactly the order the vocabulary file lists,
 * and the 64-long sequence of Wikidata ids matches term for term, which is not
 * a coincidence at that length. The qid alone would not do: Congressman and
 * Congresswoman share Q18002923, as do Councilman and Councilwoman, and three
 * assembly terms share another.
 *
 * Every step checks the qid it expects before writing, so a mismatch stops the
 * run rather than titling the wrong term.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/repair_role_titles.php'))"
 */

$APPLY = false;

$svc = Craft::$app->getEntries();
$section = $svc->getSectionByHandle('roles');
if (!$section) { echo 'no roles section.' . PHP_EOL; return; }

$vocab = json_decode(file_get_contents(
    \Craft::getAlias('@root') . '/inventory/legacy/authorities/roles-wikidata.json'), true)['roles'] ?? [];
$rows = \craft\elements\Entry::find()->section('roles')->status(null)->orderBy('id asc')->limit(null)->all();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 70) . PHP_EOL;
echo 'entries: ' . count($rows) . '   vocabulary: ' . count($vocab) . PHP_EOL;

if (count($rows) !== count($vocab)) {
    echo 'COUNTS DIFFER. Position is no longer a safe key; stopping.' . PHP_EOL;
    return;
}

$type = null;
foreach ($section->getEntryTypes() as $t) { $type = $t; }
echo 'entry type ' . $type->handle . ': hasTitleField = ' . ($type->hasTitleField ? 'yes' : 'NO') . PHP_EOL;

$plan = []; $mismatch = [];
foreach ($rows as $i => $r) {
    $v = $vocab[$i];
    $have = trim((string)$r->roleWikidataId);
    $want = (string)($v['qid'] ?? '');
    if ($have !== $want) { $mismatch[] = '#' . $r->id . ' holds ' . ($have ?: '(none)') . ', position says ' . ($want ?: '(none)') . ' for ' . $v['term']; continue; }
    if (trim((string)$r->title) === $v['term']) { continue; }
    $plan[] = [$r, $v];
}

if ($mismatch) {
    echo PHP_EOL . 'POSITION DOES NOT LINE UP (' . count($mismatch) . '). Stopping rather than guessing:' . PHP_EOL;
    foreach (array_slice($mismatch, 0, 10) as $m) { echo '   ' . $m . PHP_EOL; }
    return;
}

echo 'titles to set: ' . count($plan) . PHP_EOL . PHP_EOL;
foreach (array_slice($plan, 0, 12) as [$r, $v]) {
    printf("   #%-6d -> %-32s %-11s %s\n", $r->id, $v['term'], $v['qid'] ?: '(local)', $v['confidence']);
}
if (count($plan) > 12) { echo '   ... and ' . (count($plan) - 12) . ' more' . PHP_EOL; }

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

if (!$type->hasTitleField) {
    $type->hasTitleField = true;
    if (!$svc->saveEntryType($type)) {
        echo 'FAILED to enable the title field: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL;
        return;
    }
    echo 'title field enabled on the entry type' . PHP_EOL;
    \Craft::$app->getElements()->invalidateAllCaches();
}

$ok = 0;
foreach ($plan as [$r, $v]) {
    $e = \craft\elements\Entry::find()->id($r->id)->status(null)->one();
    if (!$e) { continue; }
    $e->title = $v['term'];
    $e->slug = null;
    if (\Craft::$app->elements->saveElement($e)) { $ok++; }
    else { echo 'FAILED #' . $r->id . ': ' . json_encode($e->getErrors()) . PHP_EOL; }
}

$blank = 0; $verified = 0;
foreach (\craft\elements\Entry::find()->section('roles')->status(null)->orderBy('id asc')->limit(null)->all() as $i => $e) {
    if (trim((string)$e->title) === '') { $blank++; }
    if (isset($vocab[$i]) && trim((string)$e->title) === $vocab[$i]['term']) { $verified++; }
}
echo PHP_EOL . 'titled: ' . $ok . '  verified: ' . $verified . ' of ' . count($vocab) . '  still blank: ' . $blank . PHP_EOL;
if ($verified < count($vocab)) { echo 'READ-BACK SHORT. Treat this run as failed.' . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('repair_role_titles.php', $ok,
    ($verified < count($vocab) ? 'FAILED: ' : '') . 'verified ' . $verified . ' of ' . count($vocab),
    'titles were blank; the entry type had no title field');
