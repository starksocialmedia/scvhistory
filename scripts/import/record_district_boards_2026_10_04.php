/**
 * The sitting boards and executives of the four elementary districts and SCV Water, and the Hart
 * board's offices, as each body's own website gives them on 4 October 2026 (Nathan: "Create the seven
 * sitting members with no record, and the five executives under affiliations"; "Record the board
 * offices as the body gives them, dated, per the decision we took"; Weinstein: "check the district's
 * own page for how she is known before merging"; Jensen and Moore: "public life only").
 *
 * The pages, saved with their checksums: inventory/news/district-boards-2026-10-04/ and
 * inventory/news/hart-district-2026-10-04/; the comparison, inventory/review/district-boards-check-2026-10-04.md.
 *
 *  1. Rochelle Weinstein #25433 is known as Shelley: the district prints "Shelley Weinstein", and the
 *     County's ballots printed Rochelle "Shelley" (2011, 2015) and Rochelle "Shelly" (2003). Retitled
 *     Shelley Weinstein (the title is the full-name field); Rochelle Weinstein kept as an alias. Lori Macdonald #28356
 *     retitled Lori MacDonald, as the district prints it.
 *  2. Six sitting trustees with no record, created with their present terms: Isaiah Talley (Newhall,
 *     Area 4, with his appointment of 2016 and election of 2020), Ernesto Smith (Newhall, Area 3),
 *     Patti Garibay (Saugus, Area 1, appointed 2023), Paola Jellings (Sulphur Springs, Area 3, since
 *     June 2020), Fred Malcomb (Castaic, Area C), Vincent Titiriga (Castaic, Area D, a two-year term).
 *  3. Three sitting Sulphur Springs trustees whose records held no present term: Ken Chase (Area 4),
 *     Denis DeFigueiredo (Area 2), Lori MacDonald (Area 5). The district prints no term years, so
 *     each present term has a seat and no dates.
 *  4. The five executives, as persons and affiliations (serving, start not given): Leticia
 *     Hernandez (Newhall), Robert Hernandez (Saugus), Catherine Kawaguchi (Sulphur Springs), Bob
 *     Brauneisen (Castaic), superintendents; Stephen Cole, general manager of SCV Water.
 *  5. Hart: Jensen's and Moore's public-life profiles from the district's page (no family); Jensen's
 *     board seat at the SCV Chamber of Commerce (#396) as an affiliation.
 *  6. Saugus #21592: its redistricting of 1 February 2022, a date row and a note, from its page.
 *  7. The board offices, dated, in templates/_data/board-officers.json (decision 4 of the body-hub
 *     proposal: shown as the body gives them, not stored by year), shown on the board cards.
 * Not done here (listed in the check file): the seats and terms of Solomon, Trunkey, Pearson and Gary
 * Martin, and the open terms of Love, Shapiro, Christy Smith, Strickland and Atkins.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_district_boards_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$person = fn($t) => Entry::find()->section('persons')->status(null)->title($t)->one();
$place = fn($slug) => Entry::find()->section('places')->status(null)->slug($slug)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$bad = [];

/* The pages, checked, and their wording, checked before any of it is quoted. */
$D = "$root/inventory/news/district-boards-2026-10-04"; $H = "$root/inventory/news/hart-district-2026-10-04";
$PAGES = [
    'newhall' => ["$D/newhall-board", 'c7b199ff56fb9611a3236d858dcf9afb0df6a9c735671317d88bcdfc750b6df4', 'Newhall School District, "Governing Board Members," https://www.newhallschooldistrict.com/governing-board-members, read 4 October 2026'],
    'newhallSup' => ["$D/newhall-superintendent", '3bdb95aca7384b35da313b065b9f32fe70e757716bd267ba3565a13621a8ec3e', 'Newhall School District, "Office of the Superintendent," https://www.newhallschooldistrict.com/office-of-the-superintendent, read 4 October 2026'],
    'saugus' => ["$D/saugus-board", '0cf3990b977da7acfcf8b30b92dcf043edff39072edaffe1a0d5ced37c4ea9b9', 'Saugus Union School District, "Governing Board," https://www.saugususd.org/governing-board, read 4 October 2026'],
    'saugusSup' => ["$D/saugus-superintendent", '5ba2a714b782981f006d9107dbfa80a7981f80ca073eb0ac12dfad2d663c4378', 'Saugus Union School District, "Our Administration," https://www.saugususd.org/our-administration, read 4 October 2026'],
    'sulphur' => ["$D/sulphur-springs-board", null, 'Sulphur Springs Union School District, "Members," https://www.sssd.k12.ca.us/members, read 4 October 2026'],
    'sulphurSup' => ["$D/sulphur-springs-superintendent", null, 'Sulphur Springs Union School District, "Superintendent Message," https://www.sssd.k12.ca.us/superintendent-message, read 4 October 2026'],
    'castaic' => ["$D/castaic-board", null, 'Castaic Union School District, "Meet Our Board of Trustees," https://www.castaicusd.com/apps/pages/index.jsp?uREC_ID=799367&type=d&pREC_ID=1188877, read 4 October 2026'],
    'castaicSup' => ["$D/castaic-superintendent", null, 'Castaic Union School District, "Superintendent\'s Office," https://www.castaicusd.com/apps/pages/index.jsp?uREC_ID=799493&type=d, read 4 October 2026'],
    'scvw' => ["$D/scv-water-board", null, 'Santa Clarita Valley Water, "Board of Directors," https://yourscvwater.com/governance/board-directors, read 4 October 2026'],
    'scvwGm' => ["$D/scv-water-general-manager", null, 'Santa Clarita Valley Water, "Executive Management," https://yourscvwater.com/who-we-are/executive-management, read 4 October 2026'],
    'hart' => ["$H/governing-board-members", '98c3a2d7b36eebe3cdf95ba63b1a99c6357bf7e604dbd99df78394c97fe0dc6e', 'William S. Hart Union High School District, "Governing Board Member Info," https://www.hartdistrict.org/apps/pages/governing-board-members, read 4 October 2026'],
];
$TXT = [];
foreach ($PAGES as $k => [$base, $sha, $cite]) {
    if (!is_file("$base.html") || ($sha && hash_file('sha256', "$base.html") !== $sha)) { $bad[] = "$k page missing or changed"; continue; }
    $TXT[$k] = preg_replace('~\s+~u', ' ', (string)file_get_contents("$base.txt"));
}
$q = function (string $k, string $quote) use (&$bad, $TXT, $PAGES): string {
    if (!str_contains($TXT[$k] ?? '', $quote)) { $bad[] = "$k does not read: $quote"; }
    return $PAGES[$k][2] . ': "' . $quote . '"';
};

/* 1. Names. */
$W = $get(25433); $M = $get(28356);
if ($W?->title !== 'Rochelle Weinstein' && $W?->title !== 'Shelley Weinstein') { $bad[] = '#25433 is not Weinstein'; }
if ($M?->title !== 'Lori Macdonald' && $M?->title !== 'Lori MacDonald') { $bad[] = '#28356 is not Lori MacDonald'; }
$q('sulphur', 'Board President: Shelley Weinstein'); $q('sulphur', 'Lori MacDonald Board Member Trustee Area No. 5');
echo '#25433 ' . ($W?->title === 'Shelley Weinstein' ? 'already Shelley Weinstein' : 'Rochelle Weinstein -> Shelley Weinstein (alias Rochelle Weinstein)') . PHP_EOL;
echo '#28356 ' . ($M?->title === 'Lori MacDonald' ? 'already Lori MacDonald' : 'Lori Macdonald -> Lori MacDonald') . PHP_EOL;

/* 2 and 3. Trustees and their terms. [person, body id, seat slug, seat label, terms[[start printed, start edtf, end printed, end edtf, how chosen, how ended, quotes]]] */
$NSD = 21590; $SUSD = 21592; $SSUSD = 21594; $CUSD = 21596;
$T = [
    ['Isaiah Talley', $NSD, 'newhall-school-district-trustee-area-4', 'Trustee Area 4', [
        ['September 6, 2016', '2016-09-06', 'December 2020', '2020-12', 'appointed', 'reelected', [$q('newhall', 'Mr. Isaiah Talley was first appointed to the Newhall School Board on September 6, 2016, and re-elected for a second term on November 3, 2020.')], false],
        ['December 2020', '2020-12', 'December 2024', '2024-12', 'elected', 'reelected', [$q('newhall', 'Mr. Isaiah Talley was first appointed to the Newhall School Board on September 6, 2016, and re-elected for a second term on November 3, 2020.')], false],
        ['December 2024', '2024-12', 'December 2028', '2028-12', '', 'serving', [$q('newhall', 'Mr. Isaiah Talley Governing Board Clerk Pro Tem Trustee Area 4 Trustee Area 4 Current Term: 2024 - 2028')], true]]],
    ['Ernesto Smith', $NSD, 'newhall-school-district-trustee-area-3', 'Trustee Area 3', [
        ['December 2022', '2022-12', 'December 2026', '2026-12', '', 'serving', [$q('newhall', 'Mr. Ernesto Smith Governing Board Member Trustee Area 3 Trustee Area 3 Current Term: 2022 - 2026')], true]]],
    ['Patti Garibay', $SUSD, 'saugus-union-school-district-trustee-area-1', 'Trustee Area 1', [
        ['2023', '2023', '', '', 'appointed', 'serving', [$q('saugus', 'Patti Garibay Trustee Area 1'), $q('saugus', 'Patti Garibay was appointed to the Saugus Union School District Board of Trustees in 2023.')], true]]],
    ['Paola Jellings', $SSUSD, 'sulphur-springs-union-school-district-trustee-area-3', 'Trustee Area 3', [
        ['June 2020', '2020-06', '', '', '', 'serving', [$q('sulphur', 'Paola Jellings Board Member Trustee Area No. 3 Mrs. Jellings has been a Sulphur Springs Union School Board member since June 2020.')], true]]],
    ['Fred Malcomb', $CUSD, 'castaic-union-school-district-trustee-area-c', 'Trustee Area C', [
        ['November 2024', '2024-11', 'December 2028', '2028-12', '', 'serving', [$q('castaic', 'Fred Malcomb, Trustee Trustee Area C Term: November 2024 to December 2028')], true]]],
    ['Vincent Titiriga', $CUSD, 'castaic-union-school-district-trustee-area-d', 'Trustee Area D', [
        ['November 2024', '2024-11', 'December 2026', '2026-12', '', 'serving', [$q('castaic', 'Vincent Titiriga, Trustee Trustee Area D Term: November 2024 to December 2026')], true]]],
    ['Ken Chase', $SSUSD, 'sulphur-springs-union-school-district-trustee-area-4', 'Trustee Area 4', [
        ['', '', '', '', '', 'serving', [$q('sulphur', 'Ken Chase Ken Chase Clerk Trustee Area No. 4'), 'The district\'s page gives no years for the present term.'], true]]],
    ['Denis DeFigueiredo', $SSUSD, 'sulphur-springs-union-school-district-trustee-area-2', 'Trustee Area 2', [
        ['', '', '', '', '', 'serving', [$q('sulphur', 'Denis DeFigueiredo Denis DeFigueiredo Board Member Trustee Area No. 2'), 'The district\'s page gives no years for the present term.'], true]]],
    ['Lori MacDonald', $SSUSD, 'sulphur-springs-union-school-district-trustee-area-5', 'Trustee Area 5', [
        ['', '', '', '', '', 'serving', [$q('sulphur', 'Lori MacDonald Lori MacDonald Board Member Trustee Area No. 5'), $q('sulphur', 'Mrs. MacDonald has been honored to serve on the Sulphur Springs Union School Board since 2012.') . ' The page gives no years for the present term.'], true]]],
];
$RS = Entry::find()->section('roles')->status(null)->title('School Board Member')->one(); $SUP = Entry::find()->section('roles')->status(null)->title('School Superintendent')->one();
if (!$RS || !$SUP) { $bad[] = 'the roles are not where expected'; }
$newPeople = []; $newHold = [];
foreach ($T as [$name, $body, $seatSlug, $seatLabel, $terms]) {
    $p = $person($name) ?? ($name === 'Lori MacDonald' ? $M : null);
    $seat = $place($seatSlug); if (!$seat) { $bad[] = "seat $seatSlug missing"; continue; }
    if (!$p) { $newPeople[$name] = ['roles' => [$RS?->id]]; echo "create person $name" . PHP_EOL; }
    foreach ($terms as [$sp, $se, $ep, $ee, $how, $end, $notes, $hasSeat]) {
        $exists = $p ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'holdingPerson'], ['targetElement' => $body, 'field' => 'holdingBody']])->all() : [];
        $dup = $exists ? array_filter($exists, fn($h) => (string)$h->termStartEdtf === $se && ($se !== '' || $h->howEnded->value === 'serving' && $h->holdingDistrict->one()?->id === $seat->id)) : [];
        if ($dup) { echo "  $name $se: holding exists" . PHP_EOL; continue; }
        $newHold[] = compact('name', 'body', 'seat', 'seatLabel', 'sp', 'se', 'ep', 'ee', 'how', 'end', 'notes', 'hasSeat');
        echo "  $name: " . ($se ?: 'start not given') . ' to ' . ($ee ?: ($end === 'serving' ? 'serving' : '?')) . ($hasSeat ? ", $seatLabel" : '') . ($how ? ", $how" : '') . PHP_EOL;
    }
}

/* 4. Executives. [name, body, title as the box reads it, quote key, quote, role] */
$EX = [
    ['Leticia Hernandez', $NSD, 'Superintendent', 'newhallSup', 'Leticia Hernandez, Ed.D Superintendent', true],
    ['Robert Hernandez', $SUSD, 'Superintendent', 'saugusSup', 'Robert Hernandez, Ed.D Superintendent', true],
    ['Catherine Kawaguchi', $SSUSD, 'Superintendent', 'sulphurSup', 'Dr. Catherine Kawaguchi Superintendent of Schools Sulphur Springs Union School District', true],
    ['Bob Brauneisen', $CUSD, 'Superintendent', 'castaicSup', 'Bob Brauneisen Superintendent', true],
    ['Stephen Cole', 402, 'General Manager', 'scvwGm', 'Stephen Cole is the General Manager of the Santa Clarita Valley Water Agency (SCV Water)', false],
];
$newAff = [];
foreach ($EX as [$name, $body, $title, $k, $quote, $isSup]) {
    $cite = $q($k, $quote); $p = $person($name);
    if (!$p) { $newPeople[$name] = ['roles' => $isSup ? [$SUP?->id] : []]; echo "create person $name" . PHP_EOL; }
    $has = $p && Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'affiliationPerson'], ['targetElement' => $body, 'field' => 'affiliationBody']])->exists();
    if ($has) { echo "  $name: affiliation exists" . PHP_EOL; continue; }
    $newAff[] = ['name' => $name, 'body' => $body, 'kind' => 'employed', 'title' => $title, 'ended' => 'serving', 'notes' => [$cite . ' The page does not say when the post was taken.']];
    echo "  $name: $title, " . $get($body)->title . ', serving' . PHP_EOL;
}

/* 5. Hart: Jensen and Moore. */
$J = $get(28322); $MO = $get(28558);
$hJ1 = $q('hart', 'Bob Jensen, CPA Trustee Area No. 2 representative Assistant Clerk');
$hJ2 = $q('hart', 'In 2009 he was elected to the William S. Hart Union High School District Governing Board. He was re-elected to the Hart Board in 2013 and 2018 and has served as President several times.');
$hJ3 = $q('hart', 'Bob also serves as a member of the Saugus-Hart School Facilities Financing Authority and as a member of the William S. Hart Joint School Financing Authority. He is a board member at a large university and has served as a board member of the Santa Clarita Valley Chamber of Commerce.');
$hJ4 = $q('hart', 'Bob Jensen is a Certified Public Accountant (CPA) who has practiced for over 30 years.');
$CANC = 'The County cancelled the district\'s at-large election of 5 November 2013 for want of candidates and appointed Joe Messina, Bob Jensen and Chris A. Fall in lieu of election (County of Los Angeles, Registrar-Recorder/County Clerk, "Local and Municipal Consolidated Elections, November 5, 2013: Final List, Cancelled Elections," https://www.lavote.gov/Documents/Election_Info/11052013_canc_elec.pdf).';
$JB = '<p>Bob Jensen, a certified public accountant, was elected to the board of the William S. Hart Union High School District in 2009, and the district gives him as re-elected in 2013 and 2018; the 2013 contest was cancelled for want of candidates and the incumbents appointed in lieu of election.[1][2][3] He holds the Trustee Area 2 seat for the term 2022 to 2026 and is the board\'s assistant clerk; the district says he has served as its president several times, without giving the years.[1][2]</p><p>He is a member of the Saugus-Hart School Facilities Financing Authority and of the William S. Hart Joint School Financing Authority, and has been a board member of the Santa Clarita Valley Chamber of Commerce.[4]</p>';
$hM1 = $q('hart', 'Dr. Cherise G. Moore believes in service to others above self. She has been an educator for over 30 years. In addition to currently serving on the William S. Hart Union High School District Board of Trustees since 2017, she also serves as an elected member of the Los Angeles County Committee on School District Organization as the immediate past chair and as a representative on the California School Boards Association Delegate Assembly.');
$hM2 = $q('hart', 'and currently serves at the national level with the U.S. Department of Education as a Principal Researcher with the American Institutes for Research');
$hM3 = $q('hart', 'Trustee Area No. 3 representative Member');
$MB = '<p>Cherise Moore has been a member of the board of the William S. Hart Union High School District since 2017 and holds its Trustee Area 3 seat for the term 2022 to 2026.[1][2] She is an elected member of the Los Angeles County Committee on School District Organization, of which she is the immediate past chair, and a representative on the California School Boards Association\'s Delegate Assembly.[1]</p><p>An educator for more than thirty years, she has worked in the Hart district and for the California Department of Education, and is a principal researcher with the American Institutes for Research.[1][3]</p>';
foreach ([[$J, 'Bob Jensen', $JB, [$hJ1, $hJ2, $CANC, $hJ3 . ' ' . $hJ4]], [$MO, 'Cherise Moore', $MB, [$hM1, $hM3, $hM2]]] as [$p, $n, $b, $notes]) {
    if ($p?->title !== $n) { $bad[] = "$n's record is not where expected"; continue; }
    echo "$n: " . (trim(strip_tags((string)$p->body)) ? 'has a profile already, left' : 'profile of ' . str_word_count(strip_tags($b)) . ' words, ' . count($notes) . ' footnotes; occupation set') . PHP_EOL;
}
$CH = $get(396); if ($CH?->title !== 'Santa Clarita Valley Chamber of Commerce') { $bad[] = '#396 is not the Chamber'; }
$jCh = $J && Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $J, 'field' => 'affiliationPerson'], ['targetElement' => 396, 'field' => 'affiliationBody']])->exists();
echo 'Jensen and the Chamber: ' . ($jCh ? 'affiliation exists' : 'affiliation, board member, years not given') . PHP_EOL;

/* 6. Saugus's redistricting. */
$SU = $get($SUSD);
$RED = $q('saugus', 'On February 1, 2022, the governing board approved the recommendations given to them by Orbach, Huff & Henderson, LLP, and Cooperative Strategies. The recommended changes kept consistency within the current trustee areas (i.e., each current member represents the same schools) while meeting the requirements of Education Code 5019.2 in addressing the demographic changes to the community found in the 2020 census data.');
$RED0 = $q('saugus', 'As required by Education Code 5019.51, California school districts must review and make appropriate adjustments to their trustee areas based on the current national census.');
$RNOTE = 'On 1 February 2022 the board approved new trustee areas, redrawn after the 2020 census as Education Code section 5019.51 requires, on the recommendations of Orbach, Huff & Henderson and Cooperative Strategies; the district says the changes kept each sitting member representing the same schools while meeting section 5019.2. The area lines do not change the schools\' attendance boundaries. (' . substr($RED0, 0, strpos($RED0, ':')) . '.)';
$suDone = $SU && str_contains(json_encode($SU->editorNotes ?? []), 'On 1 February 2022 the board approved new trustee areas');
echo 'Saugus #21592: ' . ($suDone ? 'redistricting already recorded' : 'note "Trustee areas" and a date row, 1 February 2022') . PHP_EOL;

/* 7. Offices, dated. Person titles, resolved to ids at apply time. */
$OFF = [
    21588 => ['hart', null, ['Joe Messina' => 'President', 'Erin Wilson' => 'Clerk', 'Bob Jensen' => 'Assistant Clerk']],
    $NSD => ['newhall', null, ['Rachelle Haddoak' => 'Governing Board President', 'Suzan Solomon' => 'Governing Board Clerk', 'Isaiah Talley' => 'Governing Board Clerk Pro Tem']],
    $SUSD => ['saugus', null, ['Matthew Watson' => 'President', 'Katherine Cooper' => 'Clerk']],
    $SSUSD => ['sulphur', '2025-2026', ['Shelley Weinstein' => 'Board President', 'Ken Chase' => 'Clerk']],
    $CUSD => ['castaic', null, ['Laura Pearson' => 'President', 'Erik Richardson' => 'Clerk']],
    402 => ['scvw', null, ['Maria Gutzeit' => 'President', 'Bill Cooper' => 'Vice President', 'Gary Martin' => 'Vice President']],
];
$q('saugus', 'Watson MatthewMatthew Watson President, Trustee Area 4'); $q('saugus', 'Cooper KatherineKatherine Cooper Clerk, Trustee Area 3');
$q('sulphur', 'The Governing Board officers for 2025-2026 were elected'); $q('castaic', 'Laura Pearson, President Trustee Area B'); $q('castaic', 'Erik Richardson, Clerk Trustee Area A');
$q('hart', 'Trustee Area No. 5 representative President'); $q('hart', 'Trustee Area No. 4 representative Clerk');
echo 'board offices: ' . implode('; ', array_map(fn($b, $x) => $get($b)->title . ' (' . count($x[2]) . ')', array_keys($OFF), $OFF)) . ' -> templates/_data/board-officers.json' . PHP_EOL;

if (preg_match('~\x{2014}|inventory/|Grok|Nathan~u', $JB . $MB . $RNOTE . $CANC)) { $bad[] = 'an em dash, a path or a worker\'s name in the new text'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$PROV = 'record_district_boards_2026_10_04.php, 4 October 2026: from the body\'s own website';
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($W->title !== 'Shelley Weinstein') { $al = trim((string)$W->personAliases); $W->setFieldValues(['fullName' => 'Shelley Weinstein', 'personAliases' => str_contains($al, "\nRochelle Weinstein") ? $al : trim($al . "\nRochelle Weinstein")]); if (!$el->saveElement($W)) { throw new \RuntimeException('Weinstein: ' . json_encode($W->getFirstErrors())); } $n++; }
    if ($M->title !== 'Lori MacDonald') { $M->title = 'Lori MacDonald'; $M->setFieldValues(['fullName' => 'Lori MacDonald', 'personAliases' => trim(trim((string)$M->personAliases) . "\nLori Macdonald")]); if (!$el->saveElement($M)) { throw new \RuntimeException('MacDonald: ' . json_encode($M->getFirstErrors())); } $n++; }
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
    foreach ($newPeople as $name => $x) { $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $name; $p->setFieldValues(['fullName' => $name, 'roles' => array_values(array_filter($x['roles'])), 'recordProvenance' => $PROV]); if (!$el->saveElement($p)) { throw new \RuntimeException("person $name: " . json_encode($p->getFirstErrors())); } $n++; }
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($newHold as $h) {
        $p = $person($h['name']); $e = new Entry(); $e->sectionId = $os->id; $e->setTypeId($ot->id);
        $v = ['holdingPerson' => [$p->id], 'holdingOffice' => [$RS->id], 'holdingBody' => [$h['body']], 'howEnded' => $h['end'], 'footnotes' => $fn($h['notes']), 'recordProvenance' => $PROV,
            'termStart' => $h['sp'], 'termStartEdtf' => $h['se'], 'startEvidence' => $h['se'] ? 'certified' : ''];
        if ($h['hasSeat']) { $v['holdingDistrict'] = [$h['seat']->id]; $v['seatLabel'] = $h['seatLabel']; }
        if ($h['how']) { $v['selectionMethod'] = $h['how']; }
        if ($h['ee']) { $v['termEnd'] = $h['ep']; $v['termEndEdtf'] = $h['ee']; $v['endEvidence'] = 'certified'; }
        $e->setFieldValues(array_filter($v, fn($x) => $x !== '')); if (!$el->saveElement($e)) { throw new \RuntimeException('holding ' . $h['name'] . ': ' . json_encode($e->getFirstErrors())); } $n++;
    }
    $aSec = $svc->getSectionByHandle('affiliations'); $aType = $svc->getEntryTypeByHandle('affiliation');
    $mkA = function (array $v) use ($aSec, $aType, $el, &$n) { $a = new Entry(); $a->sectionId = $aSec->id; $a->setTypeId($aType->id); $a->setFieldValues($v); if (!$el->saveElement($a)) { throw new \RuntimeException('affiliation: ' . json_encode($a->getFirstErrors())); } $n++; };
    foreach ($newAff as $a) { $mkA(['affiliationPerson' => [$person($a['name'])->id], 'affiliationBody' => [$a['body']], 'affiliationKind' => $a['kind'], 'affiliationTitle' => $a['title'], 'affiliationEnded' => $a['ended'], 'footnotes' => $fn($a['notes']), 'recordProvenance' => $PROV]); }
    if (!$jCh) { $mkA(['affiliationPerson' => [$J->id], 'affiliationBody' => [396], 'affiliationKind' => 'nonprofit-board', 'affiliationTitle' => 'Board member', 'affiliationEnded' => 'unknown', 'footnotes' => $fn([$hJ3 . ' The page gives no years.']), 'recordProvenance' => $PROV]); }
    foreach ([[$J, $JB, [$hJ1, $hJ2, $CANC, $hJ3 . ' ' . $hJ4], 'Certified public accountant; school board member'], [$MO, $MB, [$hM1, $hM3, $hM2], 'Educator and researcher; school board member']] as [$p, $b, $notes, $occ]) {
        if (trim(strip_tags((string)$p->body))) { continue; }
        $p->setFieldValues(['body' => $b, 'footnotes' => $fn($notes), 'bodyAuthorship' => 'editorial-2026', 'occupation' => $occ]); if (!$el->saveElement($p)) { throw new \RuntimeException($p->title . ': ' . json_encode($p->getFirstErrors())); } $n++;
    }
    if (!$suDone) {
        $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($SU->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
        $rows[] = ['heading' => 'Trustee areas', 'position' => 'bottom', 'note' => $RNOTE];
        $rd = $SU->recordDates ?? []; $rd[] = ['printed' => 'February 1, 2022', 'iso' => '2022-02-01', 'granularity' => 'day', 'label' => 'the board approves its trustee areas, redrawn after the 2020 census', 'confirmed' => true, 'rejected' => false];
        $SU->setFieldValues(['editorNotes' => $rows, 'recordDates' => $rd]); if (!$el->saveElement($SU)) { throw new \RuntimeException('Saugus: ' . json_encode($SU->getFirstErrors())); } $n++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

/* The offices file, with the ids now that every person exists. */
$out = ['_about' => 'Board offices as each body\'s own website gives them, read on the date shown (the body-hub proposal, decision 4: shown as the body gives them, dated, not stored by year). Written by scripts/import/record_district_boards_2026_10_04.php; rewrite it when a body\'s page changes.', 'bodies' => []];
$miss = [];
foreach ($OFF as $b => [$k, $term, $map]) {
    $o = []; foreach ($map as $name => $office) { $p = $person($name); if (!$p) { $miss[] = $name; continue; } $o[(string)$p->id] = $office; }
    $out['bodies'][(string)$b] = ['read' => '4 October 2026', 'term' => $term, 'source' => $PAGES[$k][2], 'officers' => $o];
}
file_put_contents("$root/templates/_data/board-officers.json", json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
$ok = !$miss && $person('Isaiah Talley') && $person('Stephen Cole') && $get(25433)->title === 'Shelley Weinstein' && str_contains((string)$get(28322)->body, 'Financing Authority');
echo 'READ-BACK ' . ($ok ? "OK: $n writes; offices file written" : 'SHORT' . ($miss ? ' (no person: ' . implode(', ', $miss) . ')' : '')) . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('record_district_boards_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'six trustees and five executives created from the bodies\' pages; Weinstein and MacDonald named as the district prints them; Jensen and Moore profiles; Saugus redistricting; board offices file');
if (!$ok) { throw new \RuntimeException('record_district_boards: read-back short'); }
