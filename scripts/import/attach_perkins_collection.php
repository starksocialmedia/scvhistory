/**
 * Puts the Perkins run into its collection, and measures what is already there.
 *
 * THIS IS NOT AN IMPORT, AND THAT IS THE FINDING
 *
 * The brief was to import all 20 Perkins pages as articles. All 20 are already
 * in Craft as articles, with bodies, slugs, legacyKeys and relations. Running an
 * importer over them would create 20 duplicates and lose every relation the
 * originals carry. What is missing is not the records; it is the thread that
 * ties eight of them to the collection.
 *
 *   12 of the 13 series pages are in story-of-our-valley already.
 *   notes, the series' Editor's Notes, is attached to
 *     history-of-the-santa-clarita-valley. It is filed under the wrong book.
 *   all 7 related pages carry no collection at all.
 *
 * So this sets partOfCollection and appends to articlesInCollection, and does
 * nothing else. No body is touched, no record is created, no slug changes.
 *
 * Reading order. The series pages take the order perkins.json gives them, which
 * is the order the legacy contents page listed. The related pages are not part
 * of the series and are appended after it in the order the inventory has them,
 * because they are pieces by the same author about the same valley rather than
 * chapters. If they should be interleaved by date instead, that is one line and
 * a decision.
 *
 * FIDELITY, ON THE SAME TERMS AS THE PHOTOGRAPH REBUILD
 *
 * Before anything is attached, each record's stored body is compared with the
 * crawl's body_text for the same page, by words rather than by lines, because
 * the line count measures wrapping and not content. Any record that has lost
 * words is named. Attaching a damaged record to a collection makes the damage
 * more visible, not less, so it is worth knowing first.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/attach_perkins_collection.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write relations to the database' . PHP_EOL; }

$INVENTORY  = \Craft::getAlias('@root') . '/inventory/legacy/perkins.json';
$COLLECTION = 'story-of-our-valley';
$REPORT     = \Craft::getAlias('@webroot') . '/review/perkins-pilot.md';
$SAMPLE     = 10;

if (!file_exists($INVENTORY)) { echo 'not found: ' . $INVENTORY . PHP_EOL; return; }
$data = json_decode(file_get_contents($INVENTORY), true);

$pages = [];
foreach (['series_pages' => 'series', 'related_pages' => 'related'] as $list => $role) {
    foreach (($data[$list] ?? []) as $i => $r) {
        $k = strtolower(trim((string)($r['legacy_key'] ?? '')));
        if ($k === '') { continue; }
        $path = (string)($r['legacy_path'] ?? $r['source_url'] ?? '');
        $path = strtolower(preg_replace('#^https?://[^/]+#', '', $path));
        $pages[] = ['key' => $k, 'path' => $path, 'role' => $role, 'order' => count($pages),
                    'title' => (string)($r['title'] ?? ''),
                    'date'  => trim((string)($r['date_raw'] ?? '')),
                    'text'  => (string)($r['body_text'] ?? '')];
    }
}
echo 'pages in the inventory: ' . count($pages) . PHP_EOL;

$coll = \craft\elements\Entry::find()->slug($COLLECTION)->section('collections')->status(null)->one();
if (!$coll) { echo 'collection ' . $COLLECTION . ' NOT FOUND' . PHP_EOL; return; }

/* Matched on the legacy path, not the legacy key.
 *
 * The key is not unique and the dry run proved it: Perkins numbers his chapters
 * part01 to part06 and so does Reynolds, so a lookup by key matched six Reynolds
 * chapters and reported them as Perkins pages 96% destroyed. Applying that would
 * have moved six chapters of History of the Santa Clarita Valley into Story of
 * Our Valley and broken the book.
 *
 * There are 18 duplicated legacyKeys across the corpus. The path is unique,
 * because it is where the file actually was:
 *   /scvhistory/signal/reynolds/part01.html
 *   /scvhistory/signal/perkins/part01.html
 */
$byPath = [];
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->all() as $e) {
    foreach (['legacyUrl', 'sourcePath'] as $h) {
        $v = strtolower(trim((string)($e->$h ?? '')));
        if ($v === '') { continue; }
        $v = preg_replace('#^https?://[^/]+#', '', $v);
        if ($v !== '' && !isset($byPath[$v])) { $byPath[$v] = $e; }
    }
}

/* Words only, so wrapping and punctuation do not register as loss. */
$bag = function (string $t): array {
    $s = preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower($t, 'UTF-8'));
    $out = [];
    foreach (preg_split('/\s+/', trim($s)) as $w) { if ($w !== '') { $out[$w] = ($out[$w] ?? 0) + 1; } }
    return $out;
};

$rows = []; $missing = []; $toAttach = 0; $toMove = 0; $already = 0;
$srcTotal = 0; $ourTotal = 0; $lostTotal = 0;

foreach ($pages as $p) {
    if (!isset($byPath[$p['path']])) { $missing[] = $p['key'] . '  ' . $p['path']; continue; }
    $e = $byPath[$p['path']];

    $a = $bag($p['text']); $b = $bag((string)$e->body);
    $lost = 0; foreach ($a as $w => $n) { $d = $n - ($b[$w] ?? 0); if ($d > 0) { $lost += $d; } }
    $gain = 0; foreach ($b as $w => $n) { $d = $n - ($a[$w] ?? 0); if ($d > 0) { $gain += $d; } }
    $sw = array_sum($a); $ow = array_sum($b);
    $srcTotal += $sw; $ourTotal += $ow; $lostTotal += $lost;

    $cur = $e->partOfCollection->one();
    if ($cur && $cur->id === $coll->id)      { $state = 'already in'; $already++; }
    elseif ($cur)                            { $state = 'filed under ' . $cur->slug; $toMove++; }
    else                                     { $state = 'no collection'; $toAttach++; }

    $rows[] = ['e' => $e, 'p' => $p, 'state' => $state, 'was' => $cur,
               'src' => $sw, 'our' => $ow, 'lost' => $lost, 'gain' => $gain,
               'pct' => $sw ? round(100 * $lost / $sw, 1) : 0];
}

echo 'matched to an article: ' . count($rows) . PHP_EOL;
echo 'already in the collection: ' . $already . PHP_EOL;
echo 'to attach, no collection now: ' . $toAttach . PHP_EOL;
echo 'to move, filed elsewhere: ' . $toMove . PHP_EOL;
if ($missing) {
    echo 'NOT IN CRAFT, ' . count($missing) . ':' . PHP_EOL;
    foreach ($missing as $m) { echo '   ' . $m . PHP_EOL; }
}
echo str_repeat('-', 74) . PHP_EOL;
echo 'source words: ' . number_format($srcTotal) . ', ours: ' . number_format($ourTotal) . PHP_EOL;
echo 'source words missing from ours: ' . number_format($lostTotal)
   . ' (' . ($srcTotal ? number_format(100 * $lostTotal / $srcTotal, 1) : '0') . '%)' . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

foreach ($rows as $r) {
    printf("%-24s %-30s src=%-6d ours=%-6d missing=%-5d %4.1f%%  %s\n",
        $r['p']['key'], mb_substr($r['e']->slug, 0, 30), $r['src'], $r['our'], $r['lost'], $r['pct'], $r['state']);
}

/* --------------------------------------------------------------- writing */

$written = 0; $failed = [];
if ($APPLY) {
    $order = $coll->articlesInCollection->ids();
    foreach ($rows as $r) {
        if ($r['state'] === 'already in') { continue; }
        $r['e']->setFieldValue('partOfCollection', [$coll->id]);
        if (Craft::$app->getElements()->saveElement($r['e'])) { $written++; }
        else { $failed[] = $r['e']->slug . ': ' . json_encode($r['e']->getErrors()); }
        if (!in_array($r['e']->id, $order, true)) { $order[] = $r['e']->id; }
    }
    $coll->setFieldValue('articlesInCollection', $order);
    if (!Craft::$app->getElements()->saveElement($coll)) {
        $failed[] = 'collection: ' . json_encode($coll->getErrors());
    }
    echo PHP_EOL . 'records changed: ' . $written . ', reading order now ' . count($order) . PHP_EOL;
    foreach ($failed as $f) { echo '  FAILED ' . $f . PHP_EOL; }
}

/* ---------------------------------------------------------------- report */

$out = [];
$out[] = '# The Perkins pilot';
$out[] = '';
$out[] = 'Generated by scripts/import/attach_perkins_collection.php on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing written') . '.';
$out[] = '';
$out[] = '**This is not an import.** All 20 pages are already articles in Craft. An';
$out[] = 'importer would create 20 duplicates and lose the relations the originals';
$out[] = 'carry. What is missing is the thread to the collection.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| pages in the inventory | ' . count($pages) . ' |';
$out[] = '| matched to an existing article | ' . count($rows) . ' |';
$out[] = '| not in Craft at all | ' . count($missing) . ' |';
$out[] = '| already in the collection | ' . $already . ' |';
$out[] = '| to attach | ' . $toAttach . ' |';
$out[] = '| to move from another collection | ' . $toMove . ' |';
$out[] = '| source words | ' . number_format($srcTotal) . ' |';
$out[] = '| our words | ' . number_format($ourTotal) . ' |';
$out[] = '| source words missing | ' . number_format($lostTotal)
       . ' (' . ($srcTotal ? number_format(100 * $lostTotal / $srcTotal, 1) : '0') . '%) |';
$out[] = '';
$out[] = '## Every page';
$out[] = '';
$out[] = '| key | role | record | source words | ours | missing | state |';
$out[] = '|---|---|---|---:|---:|---:|---|';
foreach ($rows as $r) {
    $out[] = '| `' . $r['p']['key'] . '` | ' . $r['p']['role'] . ' | ['
           . $r['e']->title . '](' . $r['e']->url . ') | ' . $r['src'] . ' | ' . $r['our']
           . ' | ' . $r['lost'] . ' | ' . $r['state'] . ' |';
}
$out[] = '';
$out[] = '## Before and after, ' . min($SAMPLE, count($rows)) . ' records';
$out[] = '';
$out[] = 'Nothing in the body changes. Before and after here is the relation.';
$out[] = '';
foreach (array_slice($rows, 0, $SAMPLE) as $r) {
    $out[] = '### ' . $r['e']->title;
    $out[] = '';
    $out[] = '- `' . $r['p']['key'] . '`, ' . $r['p']['role'] . ', ' . $r['e']->url;
    $out[] = '- date as printed: `' . ($r['p']['date'] !== '' ? $r['p']['date'] : '(none in the source)') . '`';
    $out[] = '- **before**: partOfCollection = ' . ($r['was'] ? '`' . $r['was']->slug . '`' : '_none_')
           . ', in the reading order: ' . ($r['state'] === 'already in' ? 'yes' : 'no');
    $out[] = '- **after**: partOfCollection = `' . $COLLECTION . '`, appended to the reading order';
    $out[] = '- body: ' . $r['our'] . ' words against ' . $r['src'] . ' in the source, '
           . $r['lost'] . ' missing. Not touched by this script.';
    $out[] = '';
    $out[] = '> ' . mb_substr(preg_replace('/\s+/', ' ', (string)$r['e']->body), 0, 220) . '...';
    $out[] = '';
}
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
