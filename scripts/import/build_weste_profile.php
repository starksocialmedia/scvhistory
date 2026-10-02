/**
 * Laurene Weste #15929, Santa Clarita City Council since 1998 (Nathan,
 * 1 October 2026: "the longest service in the archive, so her record should be
 * substantial"). Living and sitting: public life only, two citations where a
 * second source exists, nothing from opinion pieces stated as fact.
 *
 * THE SOURCES (inventory/sources/laurene-weste-2026-10-02.json)
 *   [CITY]     The City's biography, read 1 October 2026.
 *   [ARCHIVE]  Her nine candidacies, 1996 to 2022, and seven terms.
 *   [CONGRESS] Her biography as a witness before the House Committee on Natural
 *              Resources, 2 April 2019: the strongest of these, but her own
 *              statement, and given as hers.
 *   [CAMPAIGN] laureneweste.com: campaign claims, attributed as such.
 *   Ballotpedia rendered no text and is not cited.
 *
 * KEPT VISIBLE: the City lists 2025 among her election years and a seventh
 * mayoralty in 2025; the archive holds no 2025 election and her last recorded
 * term began in December 2022. The profile says so. "The first oil town in
 * California" (Mentryville) is the City's phrase and is not repeated.
 *
 * No portrait has been supplied; her banner is already registered. The claim of
 * longest service is computed here from the council's office holdings, and the
 * script refuses if anyone has served longer.
 *
 * Fills an empty body only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_weste_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 15929; $CITY_BODY = 394;
$p = Entry::find()->id($ID)->status(null)->one();
$src = json_decode((string)@file_get_contents("$root/inventory/sources/laurene-weste-2026-10-02.json"), true)['sources'] ?? [];
$bad = [];
if (!$p || $p->title !== 'Laurene Weste') { $bad[] = '#15929 is not Laurene Weste'; }
$MUST = [
    'city' => ['Years Elected: 1998, 2002, 2006, 2010, 2014, 2018, 2022, 2025', 'Mayoral Terms: 7 (2001, 2006, 2010, 2014, 2018, 2022, 2025)', 'overseen the establishment of numerous parks, the preservation of thousands of acres of open space, and the construction of a cross-town trail system', 'Santa Monica Mountains Conservancy Advisory Board Member, Laurene spearheaded the drive to save historic 800-acre Mentryville', 'Director of the Santa Clarita Valley Committee on Aging'],
    'congress' => ['Mayor 2001; 2006; 2010; 2014; 2018', 'City Parks & Recreation Commissioner Board Member/Board Chair 1987 - 1998', 'San Gabriel Mountains Community Collaborative 2019 (new formation)', 'Los Angeles County Sanitation District Board Member/Board Chair 2000-present', 'Los Angeles County Animal Care & Control Board Member/Board Chair 1997-present', 'Friends of Hart Park Board Member/Board Chair 1985-present', 'City Formation Committee Instrumental in the formation of the City of Santa Clarita', 'Spearheaded the successful effort to create 1st Open Space Preservation District Helped to preserve over 11,000 acres', 'Santa Clarita Valley Trails Action Committee-founding chair', 'Towsley Canyon Task Force founding member Helped to create Ed Davis Park and the 8,000 acre Santa Clarita Woodlands Park consisting of Towsley, Rice, Ed Davis and Mentryville', 'Santa Clara River Preservation Plan'],
    'campaign' => ['Spearheaded the transfer of the 160-acre William S. Hart Park and Museum from Los Angeles County to the City of Santa Clarita', '720-acre Haskell Canyon Open Space', 'Helped defeat the LA County prison proposed for Saugus', 'State courthouse proposed near McBean Parkway'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k]['passage'] ?? '', $ph)) { $bad[] = "$k does not read \"$ph\""; } } }
/* The returns, as the text gives them. */
$VOTES = ['1996-04-09' => [3104, 'not-elected', 4, 13], '1998-04-14' => [5770, 'elected', 3, 15], '2002-04-09' => [5516, 'elected', 3, 12], '2006-04-11' => [5241, 'elected', 3, 11],
    '2010-04-13' => [6698, 'elected', 2, 11], '2014-04-08' => [6210, 'elected', 1, 13], '2018-11-06' => [25603, 'elected', 1, 15], '2022-11-08' => [32886, 'elected', 1, 9]];
foreach (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'candidacyPerson'])->all() as $c) {
    $e = $c->candidacyElection->one(); $all = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->orderBy('votes desc')->ids();
    $got = [(int)$c->votes, (string)$c->outcome->value, array_search($c->id, $all) + 1, count($all)];
    if (($VOTES[$e->electionDateEdtf] ?? null) !== $got) { $bad[] = "the {$e->electionDateEdtf} candidacy reads " . json_encode($got); }
    unset($VOTES[$e->electionDateEdtf]);
}
if ($VOTES) { $bad[] = 'no candidacy for ' . implode(', ', array_keys($VOTES)); }
/* Longest service: total years on the council, from the office holdings. */
$years = [];
foreach (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $CITY_BODY, 'field' => 'holdingBody'])->all() as $h) {
    if ($h->selectionMethod?->value === 'rotated') { continue; }
    $who = $h->holdingPerson->one(); if (!$who) { continue; }
    $s = (int)substr((string)$h->termStartEdtf, 0, 4); $e = $h->termEndEdtf ? (int)substr((string)$h->termEndEdtf, 0, 4) : (int)date('Y');
    $years[$who->title] = ($years[$who->title] ?? 0) + max(0, $e - $s);
}
arsort($years); $top = array_key_first($years);
if ($top !== 'Laurene Weste') { $bad[] = "longest service is $top's (" . $years[$top] . ' years), not hers'; }
echo 'longest council service: ' . implode(', ', array_map(fn($n, $y) => "$n $y", array_slice(array_keys($years), 0, 3), array_slice($years, 0, 3))) . PHP_EOL;

$BODY = implode("\n\n", [
    'Laurene Weste has served on the Santa Clarita City Council since 1998, longer than anyone else in the archive\'s records of the council, and has been the city\'s mayor seven times: in 2001, 2006, 2010, 2014, 2018, 2022 and 2025.[1][2][3]',
    'She first stood in April 1996 and came fourth of thirteen candidates, with 3,104 votes. Elected in April 1998, third of fifteen with 5,770, she was re-elected in 2002, third of twelve with 5,516; in 2006, third of eleven with 5,241; in 2010, second of eleven with 6,698; in 2014, first of thirteen with 6,210; and, after the city moved its elections to November, in 2018, first of fifteen with 25,603, and in 2022, first of nine with 32,886.[2] The City lists 2025 among the years she was elected; the archive does not yet hold that election, and its record of her current term begins in December 2022.[1][2]',
    'Before the council, by her own account in a biography she submitted to Congress in 2019, she was on the City Formation Committee that helped bring the city into being, and a member and chair of the City\'s Parks and Recreation Commission from 1987 to 1998.[3] As a commissioner, the City says, she oversaw the establishment of parks, the preservation of open space and the building of the cross-town trail system.[1]',
    'Open space has been her cause. She told Congress that she led the effort to create the Santa Clarita Open Space Preservation District, the first such district, which has helped preserve more than 11,000 acres in and around the valley; that she was the founding chair of the Santa Clarita Valley Trails Action Committee; and that, as a founding member of the Towsley Canyon Task Force, she helped create Ed Davis Park and the 8,000-acre Santa Clarita Woodlands Park, which takes in Towsley, Rice and Ed Davis canyons and Mentryville. She also lists work on a preservation plan for the Santa Clara River.[3] As a member of the Santa Monica Mountains Conservancy\'s advisory board, the City and her own biography agree, she led the drive to save the 800 acres of Mentryville.[1][3]',
    'Her biography lists her on the board of the Friends of Hart Park since 1985, on the boards of Los Angeles County Animal Care and Control since 1997 and the county sanitation districts since 2000, as member and chair of each, and on the San Gabriel Mountains Community Collaborative from its formation in 2019. The City adds that she is a director of the Santa Clarita Valley Committee on Aging.[3][1]',
    'Her campaign credits her with leading the transfer of the 160-acre William S. Hart Park and Museum from Los Angeles County to the city, with the 720-acre Haskell Canyon Open Space, and with opposition to a county prison proposed for Saugus and a state courthouse proposed near McBean Parkway. These are her campaign\'s claims.[4]',
]);
$NOTES = [
    'City of Santa Clarita, "Mayor Laurene Weste," https://santaclarita.gov/city-council/laurene-weste/, read 1 October 2026: the City\'s own biography of a sitting member.',
    'Archive records: the City Council elections of 1996, 1998, 2002, 2006, 2010, 2014, 2018 and 2022, with their returns, and her office holdings for each term.',
    'Laurene Weste, biography submitted as a witness before the House Committee on Natural Resources, hearing of 2 April 2019, https://www.congress.gov/116/meeting/house/109217/witnesses/HHRG-116-II10-Bio-WesteL-20190402.pdf: her own statement of her service.',
    'Laurene Weste for City Council, https://www.laureneweste.com/, read 1 October 2026 (campaign material).',
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
$cur = trim((string)$p?->body);
if ($cur && $cur !== trim($BODY)) { $bad[] = '#15929 has a body already'; }
echo '#15929 body: ' . ($cur === trim($BODY) ? 'already written' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes') . '; ' . array_sum(array_map('count', $MUST)) . ' phrases and 8 returns checked; no portrait supplied' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if ($cur === trim($BODY)) { echo 'nothing to do' . PHP_EOL; return; }
$h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
$p->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'City council member; mayor',
    'recordProvenance' => trim((string)$p->recordProvenance . '; build_weste_profile.php, 2 Oct 2026: public-life profile', '; ')], array_flip($h)));
if (!$elements->saveElement($p)) { throw new \RuntimeException('#15929: ' . json_encode($p->getFirstErrors())); }
$ok = trim((string)Entry::find()->id($ID)->status(null)->one()->body) === trim($BODY);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $p->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_weste_profile.php', 1, $ok ? 'verified' : 'SHORT', 'Laurene Weste: public-life profile');
if (!$ok) { throw new \RuntimeException('build_weste_profile: read-back failed'); }
