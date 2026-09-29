/**
 * Two profiles filled from research of 29 September 2026, after Nathan's review.
 *
 * REMI NADEAU #18869, the freighter's grandson, was a name and nothing else
 * ("no bio, DOB-DOD, grave: pretty much worthless"). He is Remi Nadeau
 * (1867-1941), son of Joseph Frye Nadeau, the freighter's eldest son: the
 * father's obituary of 1908 names "George J., Remi, Walter and Amos." His
 * dates and grave are from Find a Grave, a secondary and user-contributed
 * source, and where another account disagrees (Grove Lake or Rice County,
 * 25 or 27 November) both are given. The deer farm of 1927 is reported by a
 * news summary quoting the LA Times, not read in the Times, and is attributed
 * as such. The claim that his brothers and sisters inherited the ranch is left
 * out: four of the five had died before him.
 *
 * MARIA GUTZEIT #21582: her candidacies, which Nathan remembered as council
 * 2013 and Congress 2014. The records say council 2008 and 2014 (the City held
 * no council election in 2013), and a 2016 congressional campaign filed with
 * the FEC in May 2015 and withdrawn before the ballot; she did not stand for
 * Congress in 2014. Official records for each.
 *
 * Replaces only what it wrote before or what is empty; Nadeau's title and
 * relation to his grandfather are set by apply_review_fixes_0929.php, which
 * runs first. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_nadeau_and_gutzeit_runs.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fn = fn(array $notes, int $from = 1): array => array_map(fn($i, $n) => ['number' => (string)($i + $from), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$elements = Craft::$app->getElements();

/* ------------------------------------------------ Remi Nadeau, the grandson */
$HERALD = 'Los Angeles Herald, 4 May 1908, "Pioneer Dies at Long Beach Home," the obituary of Joseph F. Nadeau, as transcribed on Find a Grave, memorial 37401079, https://www.findagrave.com/memorial/37401079';
$FAG = 'Find a Grave, memorial 100527069, https://www.findagrave.com/memorial/100527069/remi-nadeau (secondary, user-contributed)';
$HTS = 'Hometown Station, "Today in SCV History," 27 November 1941, read as a search summary (the page was blocked)';
$NAD = [
    'fullName' => 'Remi Nadeau',
    'occupation' => 'Rancher',
    'birthDate' => 'May 3, 1867', 'birthDateEdtf' => '1867-05-03', 'birthEvidence' => 'retrospective', 'birthplace' => 'Minnesota',
    'deathDate' => 'November 25, 1941', 'deathDateEdtf' => '1941-11-25', 'deathEvidence' => 'retrospective',
    'burialPlace' => 'Angelus Rosedale Cemetery, Los Angeles', 'burialEvidence' => 'retrospective',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/100527069/remi-nadeau',
    'bodyAuthorship' => 'editorial-2026',
    'recordProvenance' => 'created from review, 2026-09-21; profile editorial-2026, build_nadeau_and_gutzeit_runs.php, 29 September 2026',
    'body' => implode("\n\n", [
        'Remi Nadeau was a grandson of the Los Angeles freighter Remi Nadeau, and the son of the freighter\'s eldest son, Joseph Frye Nadeau.[1] He was born in Minnesota on 3 May 1867, and died on 25 November 1941 in Glendale; he is buried at Angelus Rosedale Cemetery in Los Angeles, in the lot where his father lies.[2]',
        'In the Santa Clarita Valley he owned a ranch in Soledad Canyon, on the north side of the canyon road near Whites Canyon,[3] and there kept a deer park, photographed about 1929 (record #3695). A later summary of the Los Angeles Times reports that he opened it in 1927 as a deer farm, at a cost of $40,000, stocked with mule deer from the Kaibab Forest, elk from Yellowstone and buffalo from Arizona.[4]',
    ]),
    'footnotes' => $fn([
        $HERALD . ': his father "Remi Nadeau," owner of the Cudahy ranch; Joseph "leaves five sons," and "George J., Remi, Walter and Amos live in Florence." The freighter\'s own memorial, 9433218, lists Joseph Frye as his eldest son.',
        $FAG . ': born 3 May 1867, Rice County, Minnesota; died 25 November 1941, Glendale, aged 74; buried Angelus Rosedale Cemetery, Section C, lot 212, the lot of his father. ' . $HTS . ' gives Grove Lake, Minnesota, and 27 November.',
        'Record #2155, Jerry Reynolds, History of the Santa Clarita Valley, ed. Worden, 1998, chapter 65: "The Soledad Canyon ranch of Remi Nadeau, grandson of the famous" freighter. ' . $HTS . ': the north side of Soledad Canyon Road at Whites Canyon Road.',
        $HTS . ', quoting the Los Angeles Times: the deer farm opened in 1927, $40,000, deer from the Kaibab Forest, elk from Yellowstone, buffalo from a zoo in Mesa, Arizona. The Times articles themselves have not been read.',
    ]),
];
$n = Entry::find()->id(18869)->status(null)->one();
echo 'REMI NADEAU #18869 (' . $n->title . ')' . PHP_EOL;
$setN = [];
foreach ($NAD as $h => $v) {
    if (!$n->getFieldLayout()->getFieldByHandle($h)) { echo "   REFUSING $h: not on the layout" . PHP_EOL; continue; }
    $cur = $n->getFieldValue($h);
    $empty = $cur === null || (is_array($cur) ? !array_filter($cur, fn($r) => is_array($r) && array_filter($r)) : (is_object($cur) && property_exists($cur, 'value') ? (string)$cur->value === '' : ($cur instanceof \craft\fields\data\LinkData ? (string)$cur->getUrl() === '' : trim((string)$cur) === '')));
    if ($empty || $h === 'recordProvenance') { $setN[$h] = $v; echo '   ' . str_pad($h, 16) . (is_array($v) ? count($v) . ' footnotes' : (mb_strlen($v) > 100 ? mb_substr($v, 0, 100) . '...' : $v)) . PHP_EOL; }
}

/* ------------------------------------------------ Maria Gutzeit's candidacies */
$RUNS = 'She stood for the Santa Clarita City Council in April 2008 and again in April 2014, and was not elected either time.[8][9] In May 2015 she filed with the Federal Election Commission as a candidate for Congress in the 25th District, for the 2016 election, and withdrew in January 2016, before the primary; her name was not on the ballot.[10][11]';
$RUN_NOTES = [
    'City of Santa Clarita, City Clerk, Historical Election Results, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/historical-results-7.pdf, April 8, 2008: "Maria Gutzeit 2,800," fifth of five.',
    'City of Santa Clarita, City Clerk, 2014 Election Results by Precinct, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/2014ElectionResultsbyPreci-5.pdf: Gutzeit 4,472, seventh of thirteen. The City held no council election in 2013.',
    'Federal Election Commission, candidate H6CA25169, "GUTZEIT, MARIA," House, California 25, 2016, statement of candidacy filed 8 May 2015, https://www.fec.gov/data/candidate/H6CA25169/; committee C00578062, Maria Gutzeit for Congress, last report 14 April 2016. She is not among the candidates in the Secretary of State\'s statement of vote for the 2016 primary, https://elections.cdn.sos.ca.gov/sov/2016-primary/2016-complete-sov.pdf, nor in 2014.',
    'Ballotpedia, https://ballotpedia.org/Maria_Gutzeit: "withdrew in January 2016, prior to the filing deadline." Secondary.',
];
$g = Entry::find()->id(21582)->status(null)->one();
$gBody = trim((string)$g->body);
$doRuns = !str_contains($gBody, 'She stood for the Santa Clarita City Council');
$gNotes = array_values(array_filter($g->footnotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
echo PHP_EOL . 'MARIA GUTZEIT #21582' . PHP_EOL . ($doRuns ? '   body + ' . $RUNS . PHP_EOL . '   footnotes ' . count($gNotes) . ' -> ' . (count($gNotes) + 4) : '   already written') . PHP_EOL;
if ($doRuns && count($gNotes) !== 7) { echo '   REFUSING: her footnotes are not the seven written on 29 September; the new markers [8]-[11] would not line up' . PHP_EOL; $doRuns = false; }
$HELD = ['heading' => 'Held for the elections work', 'position' => 'bottom',
    'note' => 'One source each, held back: final president of the Newhall County Water District and SCV Water vice president from January 2018 (SCV Water press release, 17 January 2018); lost the Division 3 seat in 2020 (preliminary news figures only). No year of birth: no official source gives one.'];

echo PHP_EOL . 'SUMMARY: Nadeau ' . count($setN) . ' fields; Gutzeit ' . ($doRuns ? 'candidacies added' : 'unchanged') . '.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$short = [];
if ($setN) { $n = Entry::find()->id(18869)->status(null)->one(); $n->setFieldValues($setN); if (!$elements->saveElement($n)) { $short[] = 'Nadeau: ' . json_encode($n->getFirstErrors()); } }
if ($doRuns) {
    $g = Entry::find()->id(21582)->status(null)->one();
    $g->setFieldValue('body', $gBody . "\n\n" . $RUNS);
    $g->setFieldValue('footnotes', array_merge($gNotes, $fn($RUN_NOTES, 8)));
    $en = array_values(array_filter($g->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '' && ($r['heading'] ?? '') !== $HELD['heading']));
    $g->setFieldValue('editorNotes', array_merge($en, [$HELD]));
    if (!$elements->saveElement($g)) { $short[] = 'Gutzeit: ' . json_encode($g->getFirstErrors()); }
}
$n2 = Entry::find()->id(18869)->status(null)->one(); $g2 = Entry::find()->id(21582)->status(null)->one();
if (isset($setN['body']) && trim((string)$n2->body) !== trim($NAD['body'])) { $short[] = 'Nadeau body'; }
if ($doRuns && !str_contains((string)$g2->body, 'She stood for the Santa Clarita City Council')) { $short[] = 'Gutzeit body'; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('build_nadeau_and_gutzeit_runs.php', 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Remi Nadeau (1867-1941) profile; Gutzeit candidacies 2008, 2014, Congress 2016 withdrawn');
if ($short) { throw new \RuntimeException('build_nadeau_and_gutzeit_runs: ' . implode('; ', $short)); }
