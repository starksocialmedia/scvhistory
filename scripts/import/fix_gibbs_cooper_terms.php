/**
 * Two term ends that rested on "per Nathan Imhoff" (Nathan, 3 October 2026:
 * "Replace 'per Nathan Imhoff' with a public source").
 *
 *   Bill Cooper, SCV Water Division 1 (#27422): the board's own page,
 *     https://yourscvwater.com/governance/board-directors, read 3 October
 *     2026: "Division: 1 / Elected: January 2018 / Term Expires: January 2027";
 *     "The term of office for all elected Directors is four years." The end is
 *     January 2027.
 *   Jason Gibbs, City Council (#27410): no page states his term's end. The
 *     City's council page (read 3 October 2026) lists him as "Councilmember
 *     Jason Gibbs, District 3"; its district elections page says "two district
 *     seats were voted upon in November 2024, so the remaining three district
 *     seats will be up for election starting in November 2026". His at-large
 *     term of 2020 ended in December 2024 and he sits for District 3, so his
 *     seat was one of the two elected in November 2024; a four-year term from
 *     December 2024 ends in December 2028. The footnote says it is derived.
 *     The District 3 canvass is not yet in the archive.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_gibbs_cooper_terms.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$T = [
    27422 => ['who' => 'Bill Cooper', 'end' => ['January 2027', '2027-01'],
        'old' => 'per Nathan Imhoff, the term runs to January 2027.',
        'new' => 'The board\'s own page gives "Division: 1 ... Term Expires: January 2027" and says "The term of office for all elected Directors is four years" (Santa Clarita Valley Water, Board of Directors, https://yourscvwater.com/governance/board-directors, read 3 October 2026).'],
    27410 => ['who' => 'Jason Gibbs', 'end' => ['December 2028', '2028-12'],
        'old' => 'Per Nathan Imhoff, 2 October 2026, who gives this term as running to 2028; the City of Santa Clarita lists Gibbs as a sitting member (https://santaclarita.gov/city-council/jason-gibbs/, read 1 October 2026). The 2024 election for his seat is not yet in the archive.',
        'new' => 'The term\'s end is derived. The City lists him as "Councilmember Jason Gibbs, District 3" (City of Santa Clarita, City Council, https://santaclarita.gov/city-council/, read 3 October 2026) and says that "two district seats were voted upon in November 2024, so the remaining three district seats will be up for election starting in November 2026" (City of Santa Clarita, District Elections, https://santaclarita.gov/districtelections, read 3 October 2026). His at-large term of 2020 ended in December 2024, so his District 3 seat was one of the two elected that November; four years from December 2024 is December 2028. The District 3 canvass is not yet in the archive.'],
];
$bad = []; $plan = [];
foreach ($T as $id => $t) {
    $h = Entry::find()->id($id)->status(null)->one();
    if (!$h || $h->holdingPerson->one()?->title !== $t['who']) { $bad[] = "#$id is not {$t['who']}'s"; continue; }
    $notes = array_column($h->footnotes ?? [], 'note'); $all = implode(' ', $notes);
    $done = str_contains($all, $t['new']) && (string)$h->termEndEdtf === $t['end'][1];
    if (!$done && !str_contains($all, $t['old'])) { $bad[] = "#$id footnote not as read"; continue; }
    if (!$done && trim((string)$h->termEndEdtf) !== '' && (string)$h->termEndEdtf !== $t['end'][1]) { $bad[] = "#$id term end is \"{$h->termEndEdtf}\""; }
    if (!$done) { $plan[$id] = $t; }
    echo "#$id {$t['who']}: " . ($done ? 'already done' : "term end -> {$t['end'][0]}; \"per Nathan Imhoff\" replaced with the public source") . PHP_EOL;
}
if (preg_match('~\x{2014}~u', implode('', array_column($T, 'new')))) { $bad[] = 'an em dash'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => $t) {
    $h = Entry::find()->id($id)->status(null)->one();
    $fn = array_values(array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => str_replace($t['old'], $t['new'], (string)($r['note'] ?? '')), 'source' => (string)($r['source'] ?? '')], array_filter($h->footnotes ?? [], 'is_array')));
    $h->setFieldValues(['termEnd' => $t['end'][0], 'termEndEdtf' => $t['end'][1], 'footnotes' => $fn]);
    if (!Craft::$app->getElements()->saveElement($h)) { $short[] = "#$id " . json_encode($h->getFirstErrors()); continue; }
    $r = Entry::find()->id($id)->status(null)->one();
    if ((string)$r->termEndEdtf !== $t['end'][1] || str_contains(implode(' ', array_column($r->footnotes, 'note')), 'Nathan Imhoff')) { $short[] = "#$id read-back"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' terms') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_gibbs_cooper_terms.php', count($plan), $short ? 'SHORT' : 'verified', 'Gibbs and Cooper term ends sourced to the City and SCV Water, replacing a personal attribution');
if ($short) { throw new \RuntimeException('fix_gibbs_cooper_terms: ' . implode(', ', $short)); }
