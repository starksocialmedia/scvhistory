/**
 * Duplicates and attribution by folder (Nathan, 4 October 2026: "Work out which to keep, merge anything the
 * other holds, retire the loser, redirect it, and list it in the removed-records registry"; "Attribution
 * by file path is not attribution. Anything in that collection should have her byline on the page itself
 * or come out. Apply the same test to every collection built from a legacy folder").
 * The audits: inventory/review/duplicate-slugs-2026-10-04.md and folder-attribution-audit-2026-10-04.md.
 *
 *  1. The Country Fair pair. #12702 is kept (the clean slug); its legacy fields pointed at fair1197.htm, a
 *     redirect stub, and now point at the page itself, /oldtownnewhall/patti/fair.htm, taken from #12704.
 *     #12704 holds nothing else (same text, same relations) and is deleted (soft: it stays in Craft's
 *     trash), its address redirected (config/redirects.php) and listed by slug in the removed-records
 *     registry (scripts/import/removed-claims.json).
 *  2. Collections built from a legacy folder keep only pieces whose page carries the collection author's
 *     byline: 52 come out, 2 from Open Book (#679, the Country Fair pages), 40 from Making Cents (#673),
 *     10 from Richard 'Doc' Rioux At Large (#683). Removed from both ends of the membership
 *     (partOfCollection, articlesInCollection); the articles themselves stay. Where the page's byline names
 *     someone the archive holds a record for, writtenBy is set: Leon Worden (#279) on 35, Jo Anne Darcy
 *     (#16140) and Buck McKeon (#18791) on their tributes.
 *  3. Open Book's count: the 15 November 1997 column (#12656) carries Patti Rasmussen's byline on its page
 *     and gets it; the collection's description and her profile say 25 pieces.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_country_fair_and_folder_attribution_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $get = fn($id) => Entry::find()->id($id)->status(null)->one(); $bad = [];
$A = json_decode(file_get_contents("$root/inventory/review/folder-attribution-audit-2026-10-04.json"), true);
$COL = ['otn-patti' => 679, 'coins' => 673, 'otn-rioux' => 683];
$BY = ['Leon Worden' => 279, 'JO ANNE DARCY' => 16140, 'BUCK' => 18791];
$out = [];
foreach ($COL as $k => $cid) {
    $c = $get($cid); if (!$c) { $bad[] = "collection #$cid missing"; continue; }
    foreach ($A['collections'][$k]['lacking'] ?? [] as $r) {
        $who = null; foreach ($BY as $needle => $pid) { if (str_contains($r['pageIs'], $needle)) { $who = $pid; } }
        $out[] = ['id' => (int)$r['id'], 'col' => $cid, 'who' => $who, 'what' => $r['pageIs']];
    }
}
$byCol = array_count_values(array_map(fn($o) => $o['col'], $out)); $byWho = array_count_values(array_map(fn($o) => (string)$o['who'], array_filter($out, fn($o) => $o['who'])));
echo 'out of their collection: ' . count($out) . ' (' . implode(', ', array_map(fn($c, $n) => "#$c: $n", array_keys($byCol), $byCol)) . '); writtenBy set: ' . implode(', ', array_map(fn($p, $n) => "#$p: $n", array_keys($byWho), $byWho)) . PHP_EOL;
if (count($out) !== 52) { $bad[] = 'expected 52 articles, found ' . count($out); }
$K = $get(12702); $L = $get(12704);
$fairDone = !$L && $K && $K->legacyUrl === '/oldtownnewhall/patti/fair.htm';
if (!$fairDone && ($K?->title !== 'Santa Clarita Valley Country Fair' || $L?->title !== 'Santa Clarita Valley Country Fair' || $L->legacyUrl !== '/oldtownnewhall/patti/fair.htm')) { $bad[] = 'the Country Fair pair is not as read'; }
echo 'Country Fair: ' . ($fairDone ? 'done' : 'keep #12702 (legacy fields to fair.htm, fair1197.htm kept as its alias), delete #12704 (soft), redirect, registry') . PHP_EOL;
$OB = $get(679); $RA = $get(2591); $C15 = $get(12656);
$obOld = 'Twenty-six pieces sit in the tree'; $raOld = 'the archive holds 24 of her pieces';
echo 'Open Book description: ' . (str_contains((string)$OB->body, $obOld) ? '"Twenty-six" -> "Twenty-five"' : 'already') . '; Rasmussen profile: ' . (str_contains((string)$RA->body, $raOld) ? '"24" -> "25"' : 'already') . '; #12656 byline: ' . (in_array(2591, $C15->writtenBy->ids()) ? 'already' : 'Patti Rasmussen') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$fairDone) {
        if (!$el->deleteElement($L)) { throw new \RuntimeException('delete #12704'); } $n++;
        $K->setFieldValues(['legacyUrl' => '/oldtownnewhall/patti/fair.htm', 'sourcePath' => 'https://scvhistory.com/oldtownnewhall/patti/fair.htm', 'legacyKey' => '/oldtownnewhall/patti/fair.htm']);
        if (!$el->saveElement($K)) { throw new \RuntimeException('#12702: ' . json_encode($K->getFirstErrors())); } $n++;
    }
    $cols = [];
    foreach ($out as $o) {
        $a = $get($o['id']); if (!$a) { continue; }
        $v = ['partOfCollection' => array_values(array_diff($a->partOfCollection->ids(), [$o['col']]))];
        if ($o['who'] && !in_array($o['who'], $a->writtenBy->ids())) { $v['writtenBy'] = array_values(array_unique(array_merge($a->writtenBy->ids(), [$o['who']]))); }
        $a->setFieldValues($v); if (!$el->saveElement($a)) { throw new \RuntimeException("#{$o['id']}: " . json_encode($a->getFirstErrors())); } $n++;
        $cols[$o['col']][] = $o['id'];
    }
    foreach ($cols as $cid => $ids) { $c = $get($cid); $c->setFieldValue('articlesInCollection', array_values(array_diff($c->articlesInCollection->ids(), $ids))); if (!$el->saveElement($c)) { throw new \RuntimeException("collection #$cid: " . json_encode($c->getFirstErrors())); } $n++; }
    $OB = $get(679); if (str_contains((string)$OB->body, $obOld)) { $OB->setFieldValue('body', str_replace($obOld, 'Twenty-five pieces sit in the tree', (string)$OB->body)); if (!$el->saveElement($OB)) { throw new \RuntimeException('#679'); } $n++; }
    if (str_contains((string)$RA->body, $raOld)) { $RA->setFieldValue('body', str_replace($raOld, 'the archive holds 25 of her pieces', (string)$RA->body)); if (!$el->saveElement($RA)) { throw new \RuntimeException('#2591'); } $n++; }
    if (!in_array(2591, $C15->writtenBy->ids())) { $C15->setFieldValue('writtenBy', array_values(array_merge($C15->writtenBy->ids(), [2591]))); if (!$el->saveElement($C15)) { throw new \RuntimeException('#12656'); } $n++; }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
/* The registry, by slug. */
$regPath = "$root/scripts/import/removed-claims.json"; $reg = json_decode(file_get_contents($regPath), true);
if (!array_filter($reg['removedRecords'], fn($r) => ($r['slug'] ?? '') === 'santa-clarita-valley-country-fair-2')) {
    $reg['removedRecords'][] = ['record' => 12704, 'title' => 'Santa Clarita Valley Country Fair', 'slug' => 'santa-clarita-valley-country-fair-2', 'section' => 'articles',
        'why' => 'A duplicate of #12702 made by the import: the legacy folder held the page twice, once as a redirect stub (fair1197.htm) and once as the page (fair.htm), and both were imported with the same text. #12702 is kept and now points at fair.htm.',
        'removed' => '2026-10-04', 'by' => 'scripts/import/fix_country_fair_and_folder_attribution_2026_10_04.php; its address redirects to #12702 (config/redirects.php)'];
    file_put_contents($regPath, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
}
$left = Entry::find()->section('articles')->status(null)->relatedTo(['targetElement' => 673, 'field' => 'partOfCollection'])->count();
$ok = !$get(12704) && $get(12702)->legacyUrl === '/oldtownnewhall/patti/fair.htm' && $left === 219;
echo 'READ-BACK ' . ($ok ? "OK: $n writes; Making Cents now $left" : "SHORT (Making Cents $left)") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_country_fair_and_folder_attribution_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'Country Fair duplicate retired and redirected; 52 articles out of folder-built collections, bylines from the page; Open Book 25');
if (!$ok) { throw new \RuntimeException('fix_country_fair: read-back short'); }
