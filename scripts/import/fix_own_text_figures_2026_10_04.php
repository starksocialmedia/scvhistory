/**
 * The archive's own prose repeated two live errors as fact, and a correction note
 * counted one account twice (Nathan, 3 October 2026: "Rewrite both. Attribute the
 * figures to their source, or drop them where a source contradicts them. The del
 * Valle acreage should say what Reynolds gives, note that the figures do not sum,
 * and leave it there rather than picking a total"; "reword the Camulos note to say
 * Perkins and Reynolds are one account, unconfirmed beyond them").
 *
 *   del Valle Family #915 and Rancho San Francisco #16446: the partition figures
 *     (13,599 / 21,307 / 4,684 each) are Jerry Reynolds's (chapter 15, #851),
 *     repeated in Leon Worden's profile of Ygnacio (#293). Six times 4,684 plus
 *     the other two is 63,010 acres, more than the rancho was patented at
 *     (48,611.88 acres, cited on Antonio del Valle's record #291). Attributed, the
 *     sum noted, no total chosen. #915's body was written on 3 October against the
 *     Reynolds rule (docs/PROFILES.md); it said the figures as fact.
 *   Beale #327 and Beale's Cut #932: the five thousand dollars from the supervisors
 *     is Reynolds's alone (chapter 28, #2081). The Los Angeles Star of 1863, as
 *     Perkins transcribes it, puts "the additional work" at $16,000 to $18,000,
 *     which may measure something else (live error CE52, held), so nothing
 *     contradicts it yet: attributed, not dropped.
 *   The Camulos correction note of 2 October (D5), on every record that carries
 *     it: its 1839 grant date rests on Perkins and on Reynolds, who follows him,
 *     which the Perkins rule (docs/PROFILES.md) counts as one account.
 * Each change is an exact replacement; the script refuses unless the stored text
 * is as read. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_own_text_figures_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$el = Craft::$app->getElements(); $get = fn($id) => Entry::find()->id($id)->status(null)->one();
$bad = [];
/* The sum and the patent, checked where they come from. */
$sum = 13599 + 21307 + 6 * 4684;
if ($sum !== 63010) { $bad[] = 'the sum is not 63,010'; }
$a291 = $get(291); $t291 = (string)$a291?->body . json_encode($a291?->footnotes ?? []);
if (!str_contains($t291, '48,611.88')) { $bad[] = '#291 does not give the patent as 48,611.88 acres'; }
$r851 = preg_replace('~\s+~', ' ', strip_tags((string)$get(851)?->body));
if (!str_contains($r851, 'awarded 13,599 acres to Ygnacio del Valle, while Doña Jacoba got 21,307 acres and each of her six children received 4,684 acres')) { $bad[] = '#851 does not read the figures'; }
$R15 = 'Jerry Reynolds, "Chapter 15. Family Squabbles," History of the Santa Clarita Valley, article #851 in this archive. Leon Worden\'s profile of Ygnacio del Valle (person #293) gives the same three figures. Together they come to 63,010 acres (13,599, 21,307, and six times 4,684); the rancho was patented at 48,611.88 acres (Antonio del Valle\'s record, person #291).';

$BODY = [
    915 => ['A judge divided the estate: the westernmost section, 13,599 acres, to his son Ygnacio, 21,307 acres to his widow, Jacoba, and 4,684 acres to each of her six children.',
            'Jerry Reynolds writes that a judge divided the estate: the westernmost section, 13,599 acres, to his son Ygnacio, 21,307 acres to his widow, Jacoba, and 4,684 acres to each of her six children. The figures do not sum: together they come to more than the whole rancho, so they cannot all be right.[5]', $R15],
    16446 => ['A judge gave his son Ygnacio the westernmost section, 13,599 acres, which included Camulos; 21,307 acres to Antonio\'s widow, Jacoba; and 4,684 acres to each of her six children.',
              'Jerry Reynolds writes that a judge gave his son Ygnacio the westernmost section, 13,599 acres, which included Camulos; 21,307 acres to Antonio\'s widow, Jacoba; and 4,684 acres to each of her six children. The figures do not sum: together they come to more than the whole rancho, so they cannot all be right.[4]', $R15],
    327 => ['and got five thousand dollars from the Los Angeles supervisors to do the work.[2]', 'and, by Jerry Reynolds\'s account, got five thousand dollars from the Los Angeles supervisors to do the work.[2]', null],
    932 => ['with five thousand dollars to do the work,', 'with, by Jerry Reynolds\'s account, five thousand dollars to do the work,', null],
];
$plan = [];
foreach ($BODY as $id => [$old, $new, $addNote]) {
    $e = $get($id); $b = (string)$e?->body;
    if (str_contains($b, $new)) { echo "#$id already rewritten" . PHP_EOL; continue; }
    if (substr_count($b, $old) !== 1) { $bad[] = "#$id body does not read the sentence exactly once"; continue; }
    $notes = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? 'editorial-2026')], $e->footnotes ?? []);
    if ($addNote) {
        $want = (int)preg_replace('~\D~', '', substr($new, strrpos($new, '[')));
        if (count($notes) + 1 !== $want) { $bad[] = "#$id would add note [$want] but has " . count($notes) . ' notes'; continue; }
        $notes[] = ['number' => (string)$want, 'note' => $addNote, 'source' => 'editorial-2026'];
    }
    $plan[$id] = [$e, str_replace($old, $new, $b), $notes];
    echo "#$id {$e->title}:\n   OLD: $old\n   NEW: $new" . ($addNote ? "\n   + note [" . count($notes) . "]: $addNote" : '') . PHP_EOL;
}
$D5_OLD = 'And the del Valles\' ownership dates from the grant of 22 January 1839, not from the end of Mexico\'s war of independence in 1821 (Perkins; Jerry Reynolds, part 14).';
$D5_NEW = 'And the del Valles\' ownership dates from the grant of 22 January 1839, not from the end of Mexico\'s war of independence in 1821. That date rests on Perkins and on Jerry Reynolds, who follows him (part 14): one account, not yet confirmed beyond them.';
$d5 = [];
foreach (Entry::find()->status(null)->limit(null)->all() as $e) {
    if (!$e->getFieldLayout() || !$e->getFieldLayout()->getFieldByHandle('editorNotes')) { continue; }
    $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], $e->editorNotes ?? []));
    $hit = false; foreach ($rows as $i => $r) { if (str_contains($r['note'], $D5_OLD)) { $rows[$i]['note'] = str_replace($D5_OLD, $D5_NEW, $r['note']); $hit = true; } }
    if ($hit) { $d5[$e->id] = [$e, $rows]; }
}
echo 'Camulos note (D5) reworded on ' . count($d5) . ' records: ' . implode(', ', array_map(fn($i) => "#$i", array_keys($d5))) . PHP_EOL . "   NEW: $D5_NEW" . PHP_EOL;
if (!$d5) { $done = Entry::find()->status(null)->search('"one account, not yet confirmed beyond them"')->count(); echo "   ($done already reworded)" . PHP_EOL; }
if (preg_match('~\x{2014}|inventory/~u', implode('', array_map(fn($p) => $p[1], $plan)) . $D5_NEW . $R15)) { $bad[] = 'an em dash or a repository path'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $id => [$e, $body, $notes]) { $e->setFieldValues(['body' => $body, 'footnotes' => $notes]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } $n++; }
    foreach ($d5 as $id => [$e, $rows]) { $e->setFieldValue('editorNotes', $rows); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id D5: " . json_encode($e->getFirstErrors())); } $n++; }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$short = [];
foreach ($plan as $id => [$e, $body]) { if ((string)$get($id)->body !== $body) { $short[] = "#$id"; } }
foreach ($d5 as $id => $x) { if (!str_contains(json_encode($get($id)->editorNotes), 'one account, not yet confirmed')) { $short[] = "#$id D5"; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_own_text_figures_2026_10_04.php', $n, $short ? 'SHORT' : 'verified', 'del Valle acreage and Beale\'s $5,000 attributed to Reynolds, the acreage sum noted; the Camulos note reworded as one account');
if ($short) { throw new \RuntimeException('fix_own_text_figures: ' . implode(', ', $short)); }
