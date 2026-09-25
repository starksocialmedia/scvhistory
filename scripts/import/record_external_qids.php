/**
 * Writes the Wikidata ids found for the External numismatists into the name
 * canon and the decisions file.
 *
 * Four of the eight names ruled External carried no identifier, because the
 * authority pass never ran against them: they were ruled out before they were
 * ever matched. The ruling stands either way -- none of them has a Santa
 * Clarita Valley connection the articles document -- but a ruling with no id
 * leaves the prose linker pointing the name at nothing, which is the one thing
 * External was supposed to avoid.
 *
 * Searched on Wikidata by name, and accepted only where the item's own
 * description says numismatist. "Bill Fivaz" matching a person is not enough;
 * the corpus is a coin column, and a coin dealer is what the name has to be.
 *
 *   Abe Kosoff      Q122854885  American numismatist
 *   Russell Rulau   Q7381756    American numismatist
 *   Bill Fivaz      Q105625539  American numismatist and author
 *   Aubrey Bebee    none        no item exists
 *
 * Bebee keeps the ruling and gains a note. Wikidata has no person of that name
 * -- a search returns a surname, a West Virginia community, an Oklahoma oil
 * field and a social network -- and inventing an id, or attaching the nearest
 * thing to it, would be worse than the empty field: the field would claim the
 * name had been resolved.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_external_qids.php'))"
 */

$APPLY = false;

$FOUND = [
    'Abe Kosoff'    => ['qid' => 'Q122854885', 'desc' => 'American numismatist'],
    'Russell Rulau' => ['qid' => 'Q7381756',   'desc' => 'American numismatist'],
    'Bill Fivaz'    => ['qid' => 'Q105625539', 'desc' => 'American numismatist and author'],
];
$NOT_FOUND = [
    'Aubrey Bebee' => 'no Wikidata item; a search by name returns a surname, a community, '
                    . 'an oil field and a social network, and none of them is the man',
];

$norm = fn(string $v) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $v)));

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

/* ------------------------------------------------------------- the canon */

$CANON = \Craft::getAlias('@root') . '/inventory/legacy/name-canon.json';
$canon = json_decode(file_get_contents($CANON), true);
$canonHits = 0; $canonMiss = [];
foreach ($canon['canon'] as $i => $c) {
    $n = (string)($c['canonical'] ?? '');
    if (isset($FOUND[$n])) {
        printf("   canon   %-18s %-14s %s\n", $n, $FOUND[$n]['qid'],
            ($c['wikidataId'] ?? '') === '' ? 'was empty' : 'was ' . $c['wikidataId']);
        if ($APPLY) { $canon['canon'][$i]['wikidataId'] = $FOUND[$n]['qid']; }
        $canonHits++;
    } elseif (isset($NOT_FOUND[$n])) {
        printf("   canon   %-18s %-14s %s\n", $n, '(none)', 'note recorded');
        if ($APPLY) { $canon['canon'][$i]['wikidataNote'] = $NOT_FOUND[$n]; }
        $canonHits++;
    }
}
foreach (array_merge(array_keys($FOUND), array_keys($NOT_FOUND)) as $n) {
    $seen = false;
    foreach ($canon['canon'] as $c) { if (($c['canonical'] ?? '') === $n) { $seen = true; } }
    if (!$seen) { $canonMiss[] = $n; }
}
if ($canonMiss) { echo '   NOT IN THE CANON: ' . implode(', ', $canonMiss) . PHP_EOL; }

/* -------------------------------------------------------- the decisions */

$FILE = \Craft::getAlias('@review') . '/records-decided.json';
$doc = json_decode(file_get_contents($FILE), true) ?: [];
$rows = $doc['decisions'] ?? [];
$decHits = 0;
foreach ($rows as $i => $r) {
    if (($r['type'] ?? '') === 'pair') { continue; }
    $n = (string)($r['name'] ?? '');
    foreach ($FOUND as $name => $f) {
        if ($norm($n) !== $norm($name)) { continue; }
        printf("   decision %-17s %-14s action=%s\n", $name, $f['qid'], $r['action'] ?? '?');
        if ($APPLY) { $rows[$i]['wikidataId'] = $f['qid']; }
        $decHits++;
    }
    foreach ($NOT_FOUND as $name => $why) {
        if ($norm($n) !== $norm($name)) { continue; }
        printf("   decision %-17s %-14s action=%s\n", $name, '(none)', $r['action'] ?? '?');
        if ($APPLY) { $rows[$i]['wikidataNote'] = $why; }
        $decHits++;
    }
}

echo PHP_EOL . 'canon entries touched: ' . $canonHits . '   decisions touched: ' . $decHits . PHP_EOL;
if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

file_put_contents($CANON, json_encode($canon, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
@chmod($CANON, 0644);

$doc['decisions'] = array_values($rows);
$tmp = $FILE . '.tmp';
file_put_contents($tmp, json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
rename($tmp, $FILE);
@chmod($FILE, 0644);

$backC = json_decode(file_get_contents($CANON), true);
$okC = 0;
foreach ($backC['canon'] as $c) {
    if (isset($FOUND[$c['canonical'] ?? '']) && ($c['wikidataId'] ?? '') === $FOUND[$c['canonical']]['qid']) { $okC++; }
    if (isset($NOT_FOUND[$c['canonical'] ?? '']) && ($c['wikidataNote'] ?? '') !== '') { $okC++; }
}
$backD = json_decode(file_get_contents($FILE), true);
$okD = 0;
foreach ($backD['decisions'] as $r) {
    $n = (string)($r['name'] ?? '');
    foreach ($FOUND as $name => $f) { if ($norm($n) === $norm($name) && ($r['wikidataId'] ?? '') === $f['qid']) { $okD++; } }
    foreach ($NOT_FOUND as $name => $w) { if ($norm($n) === $norm($name) && ($r['wikidataNote'] ?? '') !== '') { $okD++; } }
}

echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-24s %-16s %s\n", 'canon', $okC . ' of ' . $canonHits, $okC === $canonHits ? 'pass' : 'FAIL');
printf("   %-24s %-16s %s\n", 'decisions', $okD . ' of ' . $decHits, $okD === $decHits ? 'pass' : 'FAIL');
printf("   %-24s %-16s %s\n", 'decisions in the file', (string)count($backD['decisions']),
    count($backD['decisions']) === count($rows) ? 'pass' : 'FAIL');

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('record_external_qids.php', $canonHits + $decHits,
    ($okC === $canonHits && $okD === $decHits ? 'verified: ' : 'FAILED: ')
        . 'canon ' . $okC . '/' . $canonHits . ', decisions ' . $okD . '/' . $decHits,
    '3 ids found on Wikidata, 1 has no item');
