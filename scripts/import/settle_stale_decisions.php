/**
 * Settles every review decision that would rebuild a record the archive has
 * since removed, merged or retitled (Nathan, 3 October 2026: "Check whether
 * any other removed or retired record has the same stale decision, since the
 * same rerun would bring them all back").
 *
 * create_records_from_review.php creates every "approved" row whose name is
 * not a live title. Its dry run on 4 October would have created 13 records,
 * and every one is stale, not pending:
 *
 *   removed as outside the valley, row still approved: Bill Clinton, Theodore
 *     Roosevelt, Ward Connerly (mark_external_people.php, 21 September, which
 *     marked only names with no record), Harvey Stack (fix_org_records.php and
 *     fix_stacks_city_alias_hart.php: a New York coin dealer). These become
 *     "external", with the Wikidata id the canon already holds. The deferred
 *     merge "Clinton" -> "Bill Clinton" goes the same way.
 *   folded into a fuller record and trashed, or retitled: Edward F. Beale
 *     (#327), Henry M. Newhall (#283), James Marshall (#319), Clyde Smyth
 *     (#15985), Bill Hart (#16356), Alex Mentry (#18648), William S. Hart High
 *     School (#16052), California Petroleum Company (#18862), Pioneer Oil
 *     Refinery (now the place #20131). These become "merged" into the live
 *     record, which on a rerun adds the name as an alias and creates nothing.
 *     The deferred merge "Henry Newhall" -> "Henry M. Newhall" is pointed at
 *     #283.
 * check_removed_claims.php now fails on any approved row with no live record,
 * so the next removal that forgets its decision is caught by check_render.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/settle_stale_decisions.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$DEC = \Craft::getAlias('@review') . '/records-decided.json';
$canon = json_decode(file_get_contents(\Craft::getAlias('@root') . '/inventory/legacy/name-canon.json'), true)['canon'];
$norm = fn(string $v) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $v)));
$qid = function ($n) use ($canon) { foreach ($canon as $c) { if ($c['canonical'] === $n && !empty($c['external'])) { return (string)($c['wikidataId'] ?? ''); } } return null; };

$EXTERNAL = [
    'Bill Clinton' => 'removed 21 September as outside the valley (mark_external_people.php); the record was deleted and this row was left approved',
    'Theodore Roosevelt' => 'removed 21 September as outside the valley (mark_external_people.php); the record was deleted and this row was left approved',
    'Ward Connerly' => 'removed as outside the valley (mark_external_people.php); the record was deleted and this row was left approved',
    'Clinton' => 'the deferred merge into Bill Clinton, who is external',
    'Harvey Stack' => 'a New York coin dealer with no valley connection; the record was deleted 25 September and its replacement, Stack\'s, removed 28 September (fix_org_records.php, fix_stacks_city_alias_hart.php)',
];
$EXTQID = ['Clinton' => 'Bill Clinton', 'Harvey Stack' => null];
$MERGED = [
    'Edward F. Beale' => [327, 'person'], 'Henry M. Newhall' => [283, 'person'], 'Henry Newhall' => [283, 'person'],
    'James Marshall' => [319, 'person'], 'Clyde Smyth' => [15985, 'person'], 'Bill Hart' => [16356, 'person'],
    'Alex Mentry' => [18648, 'person'], 'William S. Hart High School' => [16052, 'organization'],
    'California Petroleum Company' => [18862, 'organization'], 'Pioneer Oil Refinery' => [20131, 'place'],
];

$doc = json_decode(file_get_contents($DEC), true); $rows = $doc['decisions'];
$bad = []; $change = 0;
foreach ($rows as $i => $r) {
    if (($r['type'] ?? '') === 'pair') { continue; }
    $name = (string)($r['name'] ?? '');
    foreach ($EXTERNAL as $n => $why) {
        if ($norm($name) !== $norm($n)) { continue; }
        if (($r['action'] ?? '') === 'external') { echo "   held      $name: external\n"; continue 2; }
        $q = array_key_exists($n, $EXTQID) ? ($EXTQID[$n] === null ? '' : $qid($EXTQID[$n])) : $qid($n);
        if ($q === null) { $bad[] = "$n: no external canon entry"; continue 2; }
        echo "   external  $name (was {$r['action']})" . ($q ? " $q" : '') . PHP_EOL;
        $rows[$i]['action'] = 'external'; $rows[$i]['wikidataId'] = $q;
        unset($rows[$i]['articles'], $rows[$i]['aliases'], $rows[$i]['intoKey'], $rows[$i]['intoName']);
        $rows[$i]['settledBy'] = 'Nathan, 2026-10-03: stale decision settled; ' . $why . '.';
        $change++; continue 2;
    }
    foreach ($MERGED as $n => [$into, $type]) {
        if ($norm($name) !== $norm($n)) { continue; }
        $t = Entry::find()->id($into)->status(null)->one();
        if (!$t) { $bad[] = "$n: #$into is not live"; continue 2; }
        if (($r['action'] ?? '') === 'merged' && (int)($r['into'] ?? 0) === $into) { echo "   held      $name: merged into #$into\n"; continue 2; }
        echo "   merged    $name (was {$r['action']}, {$r['type']}) -> #$into {$t->title} ({$t->section->handle})" . PHP_EOL;
        $rows[$i]['action'] = 'merged'; $rows[$i]['type'] = $type; $rows[$i]['into'] = $into; $rows[$i]['intoName'] = $t->title;
        unset($rows[$i]['intoKey']);
        $rows[$i]['settledBy'] = 'Nathan, 2026-10-03: stale decision settled; the record was folded into or retitled as #' . $into . ' ' . $t->title . ', and an approved row would recreate it.';
        $change++; continue 2;
    }
}
echo "rows to change: $change" . PHP_EOL . 'REFUSED: ' . ($bad ? implode('; ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($change) {
    $doc['decisions'] = array_values($rows);
    file_put_contents("$DEC.tmp", json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX); rename("$DEC.tmp", $DEC); @chmod($DEC, 0644);
}
$left = 0; foreach (json_decode(file_get_contents($DEC), true)['decisions'] as $r) { if (($r['action'] ?? '') === 'approved' && in_array($r['name'] ?? '', array_merge(array_keys($EXTERNAL), array_keys($MERGED)), true)) { $left++; } }
echo "READ-BACK approved rows left among these: $left" . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('settle_stale_decisions.php', $change, $left ? 'SHORT' : 'verified', 'stale review decisions: 5 external, 10 merged into live records');
