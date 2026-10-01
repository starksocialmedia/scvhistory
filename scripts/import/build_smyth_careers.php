/**
 * Cameron Smyth #16380 and H. Clyde Smyth #15985, built out (Nathan, 1 October
 * 2026): the career rather than the dates, which the office holdings already
 * carry; Cameron's schooling, sourced, as an education record at Hart High
 * School, the school of the district his father ran; and for Clyde, where he was
 * educated and what can be said of his superintendency.
 *
 * CAMERON is living: public life only, two citations for each claim, no family
 * beyond the father and son, which is public record. Sources:
 *   [CITY]  City of Santa Clarita, "Cameron Smyth," santaclarita.gov, read live
 *           29 September 2026 and, the site now refusing requests, in the
 *           Wayback Machine's copy of 16 February 2026.
 *   [ELITE] "A Legacy of Leadership: Cameron Smyth's Unwavering Commitment to
 *           Santa Clarita," élite Magazine, 25 June 2024.
 *   [ASM]   California State Assembly, "Cameron Smyth Biography," his member
 *           page, Wayback Machine copy of 26 July 2011: official, and his own
 *           account of his work, so its claims of legislation and awards are
 *           attributed to it, not stated.
 *   and the election records already cited (the City Clerk, the Secretary of
 *   State, the County, CEDA). Wikipedia was a finding aid only.
 *   Hart High School has two sources: [CITY] "attended ... Hart High School"
 *   and [ELITE] "After graduating from Hart." The education record says
 *   graduated, retrospective, and that the City's page says attended.
 *   Mayor five times, 2003, 2005, 2017, 2020, 2024 ([CITY]; [ELITE] "five-time
 *   Mayor"); Wikipedia's four omits 2024.
 *
 * CLYDE died in 2012. Sources: his obituary; the Man and Woman of the Year
 * biography and the SCV Education Foundation's Hall of Fame, which share their
 * wording and count as one; the City of Santa Clarita's biography of 1998 as
 * carried on archive record #4533, which gives his full name, his birth date and
 * his three degrees; Leon Worden's column of 11 December 1996, record #12474; and
 * the NCES Common Core of Data for the district's enrolment. What he did as
 * superintendent the sources do not say; the page says so, and gives the one
 * measure the archive holds, the district's growth in his last six years.
 * His birth date goes from "about 1931" to 27 September 1931 (the 1998
 * biography), retrospective.
 *
 * Replaces the bodies written on 29 September only (refuses one edited since).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_smyth_careers.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$CAM = 16380; $CLYDE = 15985; $HARTHIGH = 16052;
$cam = $get($CAM); $clyde = $get($CLYDE); $hh = $get($HARTHIGH);
$bad = [];
if (!$cam || $cam->title !== 'Cameron Smyth' || !$clyde || $clyde->title !== 'H. Clyde Smyth' || !$hh || $hh->title !== 'Hart High School') { $bad[] = 'a record is not who it should be'; }
$CLERK = 'City of Santa Clarita, City Clerk, General Municipal Elections: Historical Election Results, 1987 to 2012, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/historical-results-7.pdf';
$OBIT = 'Obituary of H. Clyde Smyth, Dignity Memorial, 2012, https://www.dignitymemorial.com/obituaries/newhall-ca/h-smyth-4971797';

$CAM_BODY = implode("\n\n", [
    'Cameron Smyth served on the Santa Clarita City Council from 2000 to 2006 and from 2016 to 2024, five times as mayor, and represented the 38th District in the California State Assembly from 2006 to 2012. His father, Clyde Smyth, sat on the same council from 1994 to 1998.[1][2][4]',
    'Born and raised in Santa Clarita, he went to Peachland Elementary, Placerita Junior High and Hart High School, graduating from Hart while his father was superintendent of the Hart district.[1][2][4] He took a degree in rhetoric and communications at the University of California, Davis.[1][3] Back in the valley he coached high school volleyball and football, and joined the staff of Assemblyman Pete Knight, running Knight\'s successful campaign for the State Senate in 1996.[1][2]',
    'He was elected to the council in April 2000, first of eleven candidates, and again in April 2004, first again, and was mayor in 2003 and 2005.[5][1][3] The City\'s biography and élite Magazine both name public safety, a balanced budget, economic development and the parks and recreation program as the work of those years.[1][2]',
    'He won the Assembly seat in November 2006 and was re-elected in 2008 and 2010.[6] In 2010 he became chairman of the Assembly Local Government Committee, the only member of the minority party then chairing a major policy committee.[1][2][3] His Assembly biography also records that he chaired the Republican Caucus after the 2008 election, and credits him with the Surrogate Stalker Act of 2008, a part in passing the film production tax credit of 2009, and Legislator of the Year awards from the California Partnership to End Domestic Violence, the Humane Society of the United States and the California sheriffs; those are its own account.[3] Term limits ended his time in the Assembly in 2012, and he went into the private sector.[1][2]',
    'He returned to the council in November 2016, second of eleven, and was re-elected in November 2020, first of nine; he was mayor in 2017, 2020 and 2024.[7][1][2] He left the council in December 2024: the change to district elections put only two districts on that year\'s ballot, and neither was his.[2][8]',
]);
$CAM_NOTES = [
    'City of Santa Clarita, "Cameron Smyth," https://santaclarita.gov/city-council/cameron-smyth/, read 29 September 2026, and in the Wayback Machine\'s copy of 16 February 2026: "Mayoral Terms: 5 (2003, 2005, 2017, 2020, 2024)"; "attended all local schools (Peachland Elementary, Placerita Jr. High and Hart High School)"; "coaching high school volleyball and football"; "joining the staff of then-Assemblyman Pete Knight and running his successful campaign for the State Senate"; "named Chairman of the Assembly Local Government Committee, the only Republican to Chair a major policy committee."',
    '"A Legacy of Leadership: Cameron Smyth\'s Unwavering Commitment to Santa Clarita," élite Magazine, 25 June 2024, https://scvelitemagazine.com/a-legacy-of-leadership-cameron-smyths-unwavering-commitment-to-santa-clarita/: "a five-time Mayor"; "After graduating from Hart"; "with only two districts up for election this year, Cameron finds himself without a district to run in."',
    'California State Assembly, "Cameron Smyth Biography," http://arc.asm.ca.gov/member/38/?p=bio, in the Wayback Machine\'s copy of 26 July 2011: "his Bachelors Degree in Rhetoric and Communications from the University of California, Davis"; "served two terms as Mayor"; the committee chairs, legislation and awards. His office\'s own account.',
    'His father\'s superintendency: ' . $OBIT . ' ("from 1975-1992"); the City of Santa Clarita\'s 1998 biography of H. Clyde Smyth, as carried on archive record #4533 ("retired in 1992 as the Superintendent of the William S. Hart Union High School District").',
    $CLERK . ': April 11, 2000, first of eleven; April 13, 2004, first of three.',
    'California Secretary of State, Statements of Vote, 2006, 2008 and 2010 general elections, Assembly District 38.',
    'County of Los Angeles, Statement of Votes Cast, 8 November 2016 (second of eleven, 30,109 votes); California Elections Data Archive, 2020 candidates file (first of nine, 56,919 votes). Both are archive records.',
    'City of Santa Clarita, City Council meeting of 10 December 2024, https://santaclarita.gov/city-council/blog/2024/12/11/december-10-2024/: "Outgoing Mayor Cameron Smyth was honored with presentations."',
];

$CLYDE_BODY = implode("\n\n", [
    'Hamilton Clyde Smyth, known as Clyde, was superintendent of the William S. Hart Union High School District until 1992, a member of the Santa Clarita City Council from 1994 to 1998, and its mayor in 1997.[1][3]',
    'Raised in Pasadena, he served in the Army in Korea and stayed in the reserves while he began teaching.[2][4] He took a bachelor\'s degree in education and economics at the University of California, Santa Barbara, in 1951, a master\'s in special education at California State University, Los Angeles, in 1958, and a doctorate in education and management at Brigham Young University in 1975.[3][2] He taught and worked in the Pasadena schools in the 1950s and 1960s, and came to the valley in 1969 as principal of Placerita Junior High School.[2][4]',
    'The sources do not agree on when his superintendency began: his obituary says 1975, the Man and Woman of the Year biography 1974; the City\'s biography of 1998 says "more than 16 years," and Leon Worden\'s column of 1996 seventeen. All agree it ended with his retirement in 1992.[1][2][3][4] They say little of what he did in the post. The one measure the archive holds is the district\'s enrolment, which the federal count shows growing in his last six years, from 9,371 pupils in 1986-87 to 10,615 in 1991-92; the count begins in 1986.[6]',
    'He chaired the board of the Santa Clarita Valley Boys and Girls Club from 1987 to 1995, and sat on the boards of Henry Mayo Newhall Memorial Hospital and the Santa Clarita Valley Health Care Association; he was a Boy Scouts district chairman and president of the Newhall Rotary.[3][2] He was the valley\'s Man of the Year: for 1992 in the Man and Woman of the Year\'s own list, which the City\'s 1998 biography gives as the Chamber of Commerce award of 1993.[2][3]',
    'He was elected to the City Council on 12 April 1994, third of the candidates, with 3,804 votes, sixteen more than Jill Klajic.[5] Taking the mayor\'s chair in December 1996 he named his priorities as the redevelopment of Newhall, the central park on Bouquet Canyon Road, growth outside the city\'s limits, and keeping a landfill out of Elsmere Canyon, and described his way of working as "to work for compromise and find a middle path."[4] According to his obituary he had presided over the council\'s first meeting in December 1987, when he was not a member of it.[1] He served one term, to 1998, and afterward was a special assistant to Representative Howard "Buck" McKeon.[2]',
    'His son Cameron Smyth was elected to the same council in 2000.[7] Clyde Smyth died on 22 January 2012, at 80.[1]',
]);
$CLYDE_NOTES = [
    $OBIT . ': "Superintendent of the William S. Hart Union High School District from 1975-1992"; "elected to the City Council in 1994"; "a term as Mayor in 1997"; "presided over the inaugural City Council meeting"; died "January 22, 2012," aged 80.',
    'Santa Clarita Valley Man and Woman of the Year, "Clyde Smyth," https://scvmw.org/previous-award-winners/past-winners-biographies/clyde-smyth/, read 1 October 2026: "A native of Pasadena," army service "during the Korean Conflict," "a doctorate in Education from BYU," "worked in the Pasadena School district in the \'50s and \'60s," principal of Placerita Junior High "in 1969," superintendent from "1974," "named Santa Clarita Valley\'s Man of the Year in 1992," "a special assistant to U.S. Rep. Howard \'Buck\' McKeon." The SCV Education Foundation\'s Hall of Fame, https://www.scveducationfoundation.org/hall-of-fame, carries the same wording and is not a second source.',
    'City of Santa Clarita, biography of Hamilton Clyde Smyth, 1998, as carried on archive record #4533 ("H. Clyde Smyth, Ed.D., Superintendent"): born "Sept. 27, 1931"; the three degrees with their years; "retired in 1992 as the Superintendent ... having been in that position for more than 16 years"; the boards, with their years; "the 1993 Santa Clarita Valley Chamber of Commerce \'Man of the Year.\'"',
    'Leon Worden, "Mayor Clyde Smyth outlines priorities," 11 December 1996, archive record #12474: "Raised in Pasadena, he did a tour in Korea and stayed in the Reserves"; "hired as principal at Placerita Junior High in 1969 and retired in 1992 after a 17-year stint as superintendent"; the four priorities, and the quotation.',
    $CLERK . ', April 12, 1994: "Clyde Smyth 3,804," Jill Klajic 3,788.',
    'NCES Common Core of Data, enrolment of the William S. Hart Union High School District (NCES 0642510): 9,371 in fall 1986, 10,615 in fall 1991. The federal series begins in 1986. Archive file inventory/schools/nces-enrolment.json.',
    $CLERK . ', April 11, 2000: Cameron Smyth first of eleven.',
];

/* ------------------------------------------------ plan */
$camOld = str_starts_with(trim(strip_tags((string)$cam?->body)), 'Cameron Smyth served on the Santa Clarita City Council from 2000 to 2006 and again from 2016 to 2024');
$clydeOld = str_starts_with(trim(strip_tags((string)$clyde?->body)), 'H. Clyde Smyth was superintendent of the William S. Hart Union High School District for eighteen years');
$camNew = trim((string)$cam?->body) === trim($CAM_BODY); $clydeNew = trim((string)$clyde?->body) === trim($CLYDE_BODY);
if (!$camOld && !$camNew) { $bad[] = 'Cameron\'s body has been edited since 29 September'; }
if (!$clydeOld && !$clydeNew) { $bad[] = 'Clyde\'s body has been edited since 29 September'; }
$edu = Entry::find()->section('educations')->status(null)->relatedTo(['targetElement' => $CAM, 'field' => 'educationPerson'])->all();
$eduHart = array_filter($edu, fn($e) => in_array($HARTHIGH, $e->educationSchool->ids()));
$enrol = json_decode(file_get_contents(\Craft::getAlias('@root') . '/inventory/schools/nces-enrolment.json'), true)['records']['21588']['years'] ?? [];
if (($enrol['1986']['count'] ?? null) !== 9371 || ($enrol['1991']['count'] ?? null) !== 10615) { $bad[] = 'the NCES figures are not 9,371 and 10,615'; }
foreach ([$CAM_BODY, $CLYDE_BODY, implode('', $CAM_NOTES), implode('', $CLYDE_NOTES)] as $t) { if (preg_match('~\x{2014}~u', $t)) { $bad[] = 'an em dash in the text'; } }
if (preg_match('~\b(Lena|wife|children|Gavin|Rowan|Kenley|mother)\b~i', $CAM_BODY)) { $bad[] = 'family detail in Cameron\'s body'; }
echo '#16380 Cameron Smyth: body ' . ($camNew ? 'already written' : '-> ' . str_word_count($CAM_BODY) . ' words, ' . count($CAM_NOTES) . ' notes') . '; education at Hart High School: ' . ($eduHart ? 'exists' : 'create (graduated, retrospective)') . PHP_EOL;
echo '#15985 H. Clyde Smyth: body ' . ($clydeNew ? 'already written' : '-> ' . str_word_count($CLYDE_BODY) . ' words, ' . count($CLYDE_NOTES) . ' notes') . '; birth date "' . $clyde?->birthDate . '" -> September 27, 1931 (retrospective)' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $c = $get($CAM); $h = array_map(fn($f) => $f->handle, $c->getFieldLayout()->getCustomFields());
    $c->setFieldValues(array_intersect_key(['body' => $CAM_BODY, 'footnotes' => $fn($CAM_NOTES), 'bodyAuthorship' => 'editorial-2026'], array_flip($h)));
    if (!$elements->saveElement($c)) { throw new \RuntimeException('Cameron: ' . json_encode($c->getFirstErrors())); }
    if (!$eduHart) {
        $e = new Entry(); $s = $svc->getSectionByHandle('educations'); $e->sectionId = $s->id; $e->setTypeId($s->getEntryTypes()[0]->id);
        $e->setFieldValues(['educationPerson' => [$CAM], 'educationSchool' => [$HARTHIGH], 'educationOutcome' => 'graduated', 'educationEvidence' => 'retrospective',
            'footnotes' => $fn(['"A Legacy of Leadership," élite Magazine, 25 June 2024: "After graduating from Hart." City of Santa Clarita, "Cameron Smyth": "attended all local schools (Peachland Elementary, Placerita Jr. High and Hart High School)." The City\'s page says attended; the magazine, graduated. His father was superintendent of the Hart district at the time.']),
            'recordProvenance' => 'build_smyth_careers.php, 1 October 2026']);
        if (!$elements->saveElement($e)) { throw new \RuntimeException('education: ' . json_encode($e->getFirstErrors())); }
    }
    $d = $get($CLYDE); $h = array_map(fn($f) => $f->handle, $d->getFieldLayout()->getCustomFields());
    $d->setFieldValues(array_intersect_key(['body' => $CLYDE_BODY, 'footnotes' => $fn($CLYDE_NOTES), 'bodyAuthorship' => 'editorial-2026',
        'birthDate' => 'September 27, 1931', 'birthDateEdtf' => '1931-09-27', 'birthEvidence' => 'retrospective',
        'editorNotes' => array_merge(array_values(array_filter($d->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')),
            [['heading' => 'Birth date', 'position' => 'bottom', 'note' => 'Given as "about 1931" until 1 October 2026; the City\'s biography of 1998, on record #4533, gives 27 September 1931.']])], array_flip($h)));
    if (!$elements->saveElement($d)) { throw new \RuntimeException('Clyde: ' . json_encode($d->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = trim((string)$get($CAM)->body) === trim($CAM_BODY) && trim((string)$get($CLYDE)->body) === trim($CLYDE_BODY)
    && Entry::find()->section('educations')->status(null)->relatedTo(['targetElement' => $CAM, 'field' => 'educationPerson'])->exists();
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('build_smyth_careers.php', 3, $ok ? 'verified' : 'SHORT', 'Cameron and Clyde Smyth built out; Cameron at Hart High School');
if (!$ok) { throw new \RuntimeException('build_smyth_careers: read-back failed'); }
