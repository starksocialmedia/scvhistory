/**
 * Cameron Smyth #16380 and his father Clyde Smyth #15985, filled out as
 * profiles, the Mentry and Gutzeit shape: a sourced body, footnotes, what is
 * not yet established held in editorNotes.
 *
 * CAMERON is living: public-life facts only, each on two sources, no birth
 * year (no official source gives one). Wikipedia and the city biography were
 * given as finding aids; every fact below was read in an official record or a
 * published one, and the city biography is cited only where a second source
 * stands with it.
 *
 * CLYDE died on 22 January 2012, so he is historical under
 * _partials/record/historical.twig once his death date is on the record. His
 * facts rest on his obituary and the City Clerk's historical results; where the
 * obituary and other accounts disagree (the year he became superintendent, the
 * year he came to Newhall), both are given.
 *
 * THE FAMILY LINK. #16380 childOf #15985 is stored and stays hidden by the
 * family rule while Cameron is living. The exception Nathan asked to have
 * proposed is not built here; it is proposed in the report.
 *
 * OFFICE TERMS are not recorded as officeHolding: that section holds nothing
 * yet, and the elections work will write the terms from the canvasses. What
 * the body says of offices is dated to the year and sourced.
 *
 * Fills empty fields only. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_smyth_profiles.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$CAMERON = 16380; $CLYDE = 15985; $CITY = 394;
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);

$CLERK = 'City of Santa Clarita, City Clerk, General Municipal Elections: Historical Election Results, 1987 to 2012, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/historical-results-7.pdf';
$CITY_BIO = 'City of Santa Clarita, "Cameron Smyth," https://santaclarita.gov/city-council/cameron-smyth/, read 29 September 2026';
$SOV_2016 = 'Los Angeles County, Statement of Votes Cast, 8 November 2016, posted by the City Clerk, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/2016StatementofVotesCast-4.pdf';
$OBIT = 'Obituary of H. Clyde Smyth, Dignity Memorial, 2012, https://www.dignitymemorial.com/obituaries/newhall-ca/h-smyth-4971797';
$SCVMW = 'Santa Clarita Valley Man and Woman of the Year, "Clyde Smyth," https://scvmw.org/previous-award-winners/past-winners-biographies/clyde-smyth/';
$EDFDN = 'SCV Education Foundation, Hall of Fame, https://www.scveducationfoundation.org/hall-of-fame';

$PLAN = [
    $CLYDE => ['title' => 'Clyde Smyth', 'fields' => [
        'fullName' => 'H. Clyde Smyth',
        'personAliases' => "H. Clyde Smyth\nDr. Clyde Smyth\nMayor Clyde Smyth",
        'occupation' => 'School superintendent; city councilman',
        'birthDate' => 'about 1931',
        'birthDateEdtf' => '1931-01-23/1932-01-22',
        'birthEvidence' => 'contemporary',
        'deathDate' => 'January 22, 2012',
        'deathDateEdtf' => '2012-01-22',
        'deathEvidence' => 'contemporary',
        'bodyAuthorship' => 'editorial-2026',
        'recordProvenance' => 'editorial-2026, build_smyth_profiles.php, 29 September 2026, from the sources in its footnotes',
        'body' => implode("\n\n", [
            'H. Clyde Smyth was superintendent of the William S. Hart Union High School District for eighteen years, until 1992, and a member of the Santa Clarita City Council from 1994 to 1998. His obituary dates the superintendency from 1975; two later accounts date it from 1974.[1][2]',
            'He came to the Santa Clarita Valley as an educator. His obituary says he moved to Newhall in 1971; the Man and Woman of the Year biography has him principal of Placerita Junior High School from 1969.[1][2]',
            'He was elected to the City Council on 12 April 1994, third of the candidates, with 3,804 votes, sixteen more than Jill Klajic.[3] He served one term, to 1998, and was mayor in 1997.[1][2] According to his obituary he presided over the council\'s first meeting in December 1987, when he was not a member of it.[1]',
            'His son Cameron Smyth was elected to the same council in 2000.[4] Clyde Smyth died on 22 January 2012, at 80.[1]',
        ]),
        'footnotes' => $fn([
            $OBIT . ': "Superintendent of the William S. Hart Union High School District from 1975-1992"; moved to Newhall in 1971; "elected to the City Council in 1994"; "a term as Mayor in 1997"; "presided over the inaugural City Council meeting"; died "January 22, 2012," aged 80.',
            $SCVMW . ': "In 1974, he took the helm as Superintendent"; principal of Placerita Junior High from 1969; on the council in the mid-1990s and mayor in 1997. ' . $EDFDN . ': superintendent from 1974.',
            $CLERK . ', April 12, 1994: "Clyde Smyth 3,804," Jill Klajic 3,788. He is not among the candidates of April 1998.',
            $CLERK . ', April 11, 2000: Cameron Smyth first of eleven. ' . $OBIT . ' names his son "Assemblyman Cameron Smyth."',
        ]),
    ]],
    $CAMERON => ['title' => 'Cameron Smyth', 'fields' => [
        'occupation' => 'City councilman; state assemblyman',
        'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Cameron_Smyth',
        'bodyAuthorship' => 'editorial-2026',
        'recordProvenance' => 'created from review, 2026-09-21; profile editorial-2026, build_smyth_profiles.php, 29 September 2026',
        'body' => implode("\n\n", [
            'Cameron Smyth served on the Santa Clarita City Council from 2000 to 2006 and again from 2016 to 2024, and in the California State Assembly for the 38th District from 2006 to 2012. His father, Clyde Smyth, sat on the same council from 1994 to 1998.[1][2]',
            'He was elected to the council in April 2000, first of eleven candidates, and re-elected in April 2004, again first.[3][4] In November 2006 he was elected to the Assembly, and was re-elected in 2008 and 2010; he chaired the Assembly Local Government Committee from 2010.[5][4][6] He returned to the council in the election of November 2016 and was re-elected in November 2020.[7][4][8] He left the council in December 2024, when the change to district elections put his area\'s seat outside that year\'s ballot.[9][10]',
        ]),
        'footnotes' => $fn([
            $OBIT . ' names his son "Assemblyman Cameron Smyth." ' . $SCVMW . ' on Clyde Smyth\'s council service. ' . $CLERK . ', April 12, 1994 and April 11, 2000.',
            'Scvelite Magazine, "A Legacy of Leadership: Cameron Smyth\'s Unwavering Commitment to Santa Clarita," https://scvelitemagazine.com/a-legacy-of-leadership-cameron-smyths-unwavering-commitment-to-santa-clarita/: "His father, Clyde Smyth, ... City Council member, and Mayor."',
            $CLERK . ': April 11, 2000, "Cameron Smyth 5.461" (sic), first of eleven; April 13, 2004, 7,164, first of three. He stood first in April 1998 and lost, fourth, with 4,826.',
            $CITY_BIO . ': "In 2000, Smyth was elected to the Santa Clarita City Council and served six years"; elected 2000, 2016 and 2020; "Served in California State Assembly 2006-2012"; "named Chairman of the Assembly Local Government Committee."',
            'California Secretary of State, Statement of Vote: 2006 general, https://elections.cdn.sos.ca.gov/sov/2006-general/assembly.pdf, District 38, Smyth 70,193; 2008 general, https://elections.cdn.sos.ca.gov/sov/2008-general/sov_complete.pdf, 103,761; 2010 general, https://elections.cdn.sos.ca.gov/sov/2010-general/73-state-assembly.pdf, 83,854. California State Senate, Record of Members of the Assembly 1849-2017, https://archive.senate.ca.gov/sites/archive.senate.ca.gov/files/rep/assembly_service_and_officers_1849-2017.pdf: "Smyth, Cameron R ... 2007-2012," counting by session.',
            'Ballotpedia, https://ballotpedia.org/Cameron_Smyth: chair of the Local Government Committee. Secondary.',
            $SOV_2016 . ': Smyth second, 30,109, behind Bob Kellar, 32,216.',
            'Ballotpedia, https://ballotpedia.org/Cameron_Smyth: 3 November 2020, first, 56,919 (31.3%). Secondary; the City Clerk\'s 2020 results are a scanned image without a text layer.',
            'City of Santa Clarita, City Council meeting of 10 December 2024, https://santaclarita.gov/city-council/blog/2024/12/11/december-10-2024/: "Outgoing Mayor Cameron Smyth was honored with presentations."',
            'The Signal, "\'And with that, I\'m out\': Smyth bids adieu to City Council, Ayala sworn in," December 2024, read as a search summary only, the page being blocked; on the district change, the same. Held to one reading until the article is read in full.',
        ]),
        'editorNotes' => [[
            'heading' => 'Held for a second source, and for the elections work',
            'note' => 'Mayor in 2003, 2005, 2017, 2020 and 2024, per ' . $CITY_BIO . ' alone; SCVNews reported the fifth term in December 2023 (headline read only). Exact dates of his council and Assembly terms appear only in Wikipedia and will come from the canvasses and the Assembly journal with the officeHolding records. No year of birth: no official source gives one.',
            'position' => 'bottom',
        ]],
    ]],
];

$elements = Craft::$app->getElements();
$isEmpty = function ($e, $h) {
    $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if (is_array($v) || $v === null) { return !array_filter((array)$v, fn($r) => is_array($r) && array_filter($r, fn($c) => $c !== null && $c !== '' && $c !== false)); }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    return trim((string)$v) === '';
};
$sets = [];
foreach ($PLAN as $id => $p) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || $e->title !== $p['title']) { echo "REFUSING: #$id is not {$p['title']}" . PHP_EOL; return; }
    echo PHP_EOL . "#$id {$p['title']}" . PHP_EOL;
    $set = [];
    foreach ($p['fields'] as $h => $v) {
        if (!$e->getFieldLayout()->getFieldByHandle($h)) { echo "   REFUSING field $h, not on the layout" . PHP_EOL; continue; }
        if ($h === 'recordProvenance' || $isEmpty($e, $h)) { $set[$h] = $v; echo '   ' . str_pad($h, 20) . (is_array($v) ? count($v) . ' row(s)' : (mb_strlen($v) > 110 ? mb_substr($v, 0, 110) . '... (' . mb_strlen($v) . ' chars)' : $v)) . PHP_EOL; }
        else { echo '   ' . str_pad($h, 20) . '(already set, kept)' . PHP_EOL; }
    }
    $orgs = array_map('intval', $e->personOrganizations->status(null)->ids());
    $addOrg = $id === $CLYDE ? [$CITY, Entry::find()->section('organizations')->ncesId('0642510')->status(null)->one()?->id] : [$CITY];
    $addOrg = array_values(array_filter($addOrg, fn($x) => $x && !in_array($x, $orgs, true)));
    if ($addOrg) { $set['personOrganizations'] = array_merge($orgs, $addOrg); echo '   personOrganizations + ' . implode(', ', array_map(fn($x) => '#' . $x, $addOrg)) . PHP_EOL; }
    $sets[$id] = $set;
}
echo PHP_EOL . 'NOT DONE: the family link stays hidden (the exception is proposed, not built); no officeHolding records.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$short = [];
foreach ($sets as $id => $set) {
    if (!$set) { continue; }
    $e = Entry::find()->id($id)->status(null)->one();
    $e->setFieldValues($set);
    if (!$elements->saveElement($e)) { $short[] = "#$id save failed: " . json_encode($e->getFirstErrors()); continue; }
    $b = Entry::find()->id($id)->status(null)->one();
    if (isset($set['body']) && trim((string)$b->body) !== trim($set['body'])) { $short[] = "#$id body differs"; }
}
$c = Entry::find()->id($CLYDE)->status(null)->one();
if (trim((string)$c->deathDateEdtf) !== '2012-01-22') { $short[] = 'Clyde death date reads ' . $c->deathDateEdtf; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: both profiles') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('build_smyth_profiles.php', count(array_filter($sets)), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Cameron and Clyde Smyth profiles');
if ($short) { throw new \RuntimeException('build_smyth_profiles: ' . implode('; ', $short)); }
