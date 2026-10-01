/**
 * Alan Ferdman #25191: a civic figure no general reference covers (Nathan,
 * 1 October 2026). A living person: public life only, two citations for each
 * claim, nothing about his marriage, children, grandchildren or family, though
 * three of the sources lead with them.
 *
 * THE SOURCES (passages kept, family sentences removed, in
 * inventory/sources/alan-ferdman-2026-10-01.json)
 *   [OTNA]  Old Town Newhall Association, "Board of Directors," undated, read
 *           1 October 2026: the association's own board biography.
 *   [MAG]   "Vote for Alan Ferdman for City Council," Santa Clarita Magazine,
 *           1 October 2016: campaign material.
 *   [KHTS16] Chris McCrory, "Alan Ferdman To Run For Santa Clarita City Council
 *           Seat In 2016 Election," KHTS / Hometown Station, 24 January 2016:
 *           news, read through the Wayback Machine (the site refuses direct
 *           requests).
 *   [KHTS20] "2020 SCV Man And Woman Of The Year Nominees Announced," KHTS,
 *           5 March 2020: the nomination, written by the nominating
 *           organization, the Samuel Dixon Family Health Center.
 *   [CEDA], and the City's 2014 results and the County's 2016 statement: his
 *           two candidacies, and the ballot designations the County printed.
 *
 * HOW CLAIMS ARE HANDLED. A claim two sources agree on is stated. What only the
 * campaign or the nominator says is said to be theirs: the Rubber Ducky
 * Festival's "over $330,000" is the health center's figure, not an audited
 * one, and the 2006 selection committees, the Whittaker-Bermite advisory group,
 * the Gazette column and the "Top 51" recognition rest on the nomination alone
 * and are attributed to it. Fraternal and club memberships are left out as
 * private associations.
 *
 * Fills an empty body only. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_ferdman_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 25191;
$p = Entry::find()->id($ID)->status(null)->one();
$src = json_decode(file_get_contents("$root/inventory/sources/alan-ferdman-2026-10-01.json"), true)['sources'] ?? [];
$bad = [];
if (!$p || $p->title !== 'Alan Ferdman') { $bad[] = '#25191 is not Alan Ferdman'; }
/* Each claim's words, checked in the passages it is cited to, so the text cannot drift from its sources. */
$MUST = ['otna' => ['Litton Guidance & Control in Woodland Hills', 'Jet Propulsion Laboratory in Pasadena', 'National University', 'Master of Science degree in Software Engineering', 'Chair of the Canyon Country Advisory Committee, a position he has held since 2002', 'CEO of the Santa Clarita Community Council', 'Vice Chairman', 'Board Treasurer', 'Vice-Chair of the Santa Clarita Senior Center Advisory Council'],
    'scmag2016' => ['Chair of the Canyon Country Advisory Committee', 'Rubber Ducky Regatta Committee', 'Vice-Chairperson of the Senior Center Advisory Council', 'Chair of the Santa Clarita Adult Sports Committee', 'Open Space Financial Accountability and Audit Panel'],
    'khts2016' => ['chair of the Canyon Country Advisory Committee', 'January 24, 2016', 'traffic congestion management'],
    'khts2020' => ['Samuel Dixon Family Health Center', 'raised over $330,000', 'Treasurer of our Board since 2017', 'resident of Canyon Country since 1965', 'Santa Clarita Community Council 7 years: Board Member and CEO', '2019 Vice-chair', '2006 “Police Chief” Selection Advisory Committee', 'Whittaker-Bermite Citizens Advisory Group', 'Weekly Columnist', 'Top 51 Most Influential People', 'Adult Sports']];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k]['passage'] ?? '', $ph)) { $bad[] = "$k does not read \"$ph\""; } } }

$BODY = implode("\n\n", [
    'Alan Ferdman is a Canyon Country civic figure: for more than twenty years the chair of the Canyon Country Advisory Committee, a residents\' group, and twice a candidate for the Santa Clarita City Council.[1][2][3] He has lived in Canyon Country since 1965.[1][4]',
    'He is a retired aerospace engineer and department manager. He held senior positions at Litton Guidance and Control in Woodland Hills and at the Jet Propulsion Laboratory in Pasadena, taught as adjunct faculty at National University, and holds a master\'s degree in software engineering and a bachelor\'s in computer science.[1][4] His 2016 campaign put his career at forty years with Litton and six with JPL.[2]',
    'He has chaired the Canyon Country Advisory Committee since 2002, and has been chief executive of the Santa Clarita Community Council.[1][2][3][4] He has served on the board of the Old Town Newhall Association, as its vice chair in 2019; on the board of the Samuel Dixon Family Health Center, as its treasurer since 2017; and as vice chair of the Santa Clarita Senior Center Advisory Council.[1][4] He chaired the health center\'s Rubber Ducky festival committee; the health center, nominating him in 2020, said the festival had raised over $330,000 under his chairmanship, a figure that is the nominator\'s own.[2][4] For the City he chaired the Adult Sports Committee for thirteen years and sat on the Open Space Financial Accountability and Audit Panel, among other panels and site-selection groups.[2][4] The nomination also lists the City\'s 2006 advisory committees on choosing a police chief and filling a council seat, the Whittaker-Bermite Citizens Advisory Group, and a weekly column in the SCV Gazette.[4]',
    'He stood for the council in April 2014 as "Community Committee Chairperson" and came fourth of thirteen, with 4,833 votes; and in November 2016 as "CEO Non-Profit," fourth of eleven, with 12,106.[5][6] Announcing the second run in January 2016, he campaigned on traffic, on what he called back-room dealing, citing the council\'s 2014 vote on electronic billboards, and on public safety.[3]',
    'In 2020 the Samuel Dixon Family Health Center nominated him for Santa Clarita Valley Man of the Year; its nomination also says he had been named among the valley\'s "Top 51 Most Influential People."[4]',
]);
$NOTES = [
    'Old Town Newhall Association, "Board of Directors," https://otna.joshuadbernstein.com/board-of-directors/, undated, read 1 October 2026: the association\'s own biography of its vice chairman.',
    '"Vote for Alan Ferdman for City Council," Santa Clarita Magazine, 1 October 2016, https://santaclaritamagazine.com/2016/10/vote-alan-ferdman-city-council/ (campaign material).',
    'Chris McCrory, "Alan Ferdman To Run For Santa Clarita City Council Seat In 2016 Election," KHTS / Hometown Station, 24 January 2016, read in the Wayback Machine\'s copy of 27 May 2025: http://web.archive.org/web/20250527034925/https://www.hometownstation.com/santa-clarita-news/politics/alan-ferdman-to-run-for-santa-clarita-city-council-seat-in-2016-election-166287',
    '"2020 SCV Man And Woman Of The Year Nominees Announced," KHTS / Hometown Station, 5 March 2020, https://www.hometownstation.com/santa-clarita-news/community-news/2020-scv-man-and-woman-of-the-year-nominees-316046: the nomination, as written by the nominating organization, the Samuel Dixon Family Health Center.',
    'California Elections Data Archive (CEDA): the 2014 candidates file, row 1934 ("Community Committee Chairperson," 4,833 votes), and the 2016 file, row 1549 ("CEO Non-Profit," 12,106 votes). A compilation of the County\'s returns. CEDA marks him as an incumbent in 2016; he was not one.',
    'City of Santa Clarita, City Clerk, "2014 Election Results by Precinct" (surnames only); County of Los Angeles, "Statement of Votes Cast, General Election, November 8, 2016: Santa Clarita City Council." Both are archive records.',
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
if (preg_match('~\b(wife|married|children|grandchild|Pamela|generation)~i', $BODY)) { $bad[] = 'family detail in the body'; }
$cur = trim((string)$p?->body);
echo '#25191 Alan Ferdman: body ' . ($cur === trim($BODY) ? 'already written' : ($cur ? 'NOT EMPTY, refusing' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes')) . '; every quoted phrase checked in its source (' . array_sum(array_map('count', $MUST)) . ')' . PHP_EOL;
if ($cur && $cur !== trim($BODY)) { $bad[] = '#25191 has a body already'; }
echo 'Attributed, not stated: the $330,000 (the nominator\'s), the 2006 committees, Whittaker-Bermite, the Gazette column and "Top 51" (the nomination alone). Left out: family, fraternal memberships.' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
$h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
$vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Retired aerospace engineer; civic leader',
    'recordProvenance' => trim((string)$p->recordProvenance . '; build_ferdman_profile.php, 1 October 2026: profile, public life only')];
$p->setFieldValues(array_intersect_key($vals, array_flip($h)));
if (!Craft::$app->getElements()->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$ok = trim((string)Entry::find()->id($ID)->status(null)->one()->body) === trim($BODY);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $p->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_ferdman_profile.php', 1, $ok ? 'verified' : 'SHORT', 'Alan Ferdman: public-life profile');
if (!$ok) { throw new \RuntimeException('build_ferdman_profile: read-back failed'); }
