/**
 * The 23 term endings the survey found sourced and different from what the archive held (Nathan, 4 October
 * 2026: "Apply the 23 term endings. Ten resignations, two deaths in office and a corrected end are all
 * better than 'not recorded'. Gladbach and Murr dying in office should also appear on their person
 * records"). The survey, with every quote and the pages saved: inventory/review/term-endings-2026-10-04.md
 * and inventory/news/term-endings-2026-10-04/.
 *
 * Each holding gets its end, how it ended, the evidence for the end, and a footnote with the source.
 * Where a member served on past the term the archive held, the next terms are created: David Huffaker
 * (2007 to 2015), Michael Hogan (2005 to 2013, lost as the incumbent), Gary Murr (2001 to his death in June
 * 2005). John Michael McGrath's 2009 term ends at his resignation of 8 December 2009, before he served it.
 * Lester Freeman's term keeps its end; its footnote said he did not stand in 1999, and he did, and lost.
 * Gladbach's and Murr's deaths go on their person records with the same sources.
 * Idempotent: a holding already carrying its new ending is left. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_term_endings_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$bad = [];

$CEDA99 = 'California Elections Data Archive (CEDA), CEDA1999Data.xls, a compilation of the County\'s returns';
$SIG18 = 'The Signal, "3 hopefuls face off for 1 seat on board," August 2018, as captured by the Wayback Machine, https://web.archive.org/web/20250614172118/https://signalscv.com/2018/08/3-hopefuls-face-off-for-1-seat-on-board/';
$KHTS13 = 'Allison Pari, "Huffaker Takes Helm of Castaic School Board," KHTS, as carried on SCVNews.com, 13 December 2013, https://scvnews.com/huffaker-takes-helm-of-castaic-school-board';
$CANC18 = 'County of Los Angeles, Registrar-Recorder/County Clerk, "General Election November 6, 2018, Final List of Cancelled Elections," https://www.lavote.gov/docs/rrcc/Election-Info/11082018_final-list-cancelled-elections.pdf: "Sulphur Springs Union School (Trustee Areas 3, 4, and 5)", the seats filled by appointment in lieu of election.';
/* id => [end printed, end edtf, howEnded, endEvidence, footnote, replace footnote index (null to append)] */
$E = [
    28527 => [null, null, null, null, 'Lester Freeman stood again on 2 November 1999, as the incumbent, and was not elected: CEDA lists "Lester M. Freeman, III," police sergeant, 271 votes, sixth of six candidates for two seats (' . $CEDA99 . ').', 2],
    28537 => ['December 2007', '2007-12', 'expired', 'retrospective', 'He did not stand again in 2007. Asked as a candidate in 2024, he said he had stepped down from the board once his children had left the district: "I\'ve already served eight years" (The Signal, "Candidates for SCV elementary school boards make their cases," October 2024, as captured by the Wayback Machine, https://web.archive.org/web/20241106214109/https://signalscv.com/2024/10/candidates-for-scv-elementary-school-boards-make-their-cases/).', null],
    28517 => [null, null, 'reelected', 'contemporary', 'Re-elected in 2007 and again in 2011: "He was elected to the Board in 2003, 2007, and 2011"; "His term ends in December 2015" (' . $KHTS13 . ').', null],
    28881 => ['February 17, 2017', '2017-02-17', 'resigned', 'contemporary', '"On Jan. 23, Torres notified the Castaic Union School District Governing Board of his resignation effective Feb. 17. At the Feb. 28 meeting, the governing board ... made a provisional appointment, appointing Mayreen Burk to fill the vacancy" ("CUSD Makes Provisional Appointment for Vacancy," SCVNews.com, 1 March 2017, https://scvnews.com/cusd-makes-provisional-appointment-for-vacancy).', null],
    28887 => ['September 1, 2016', '2016-09-01', 'resigned', 'contemporary', '"Lambarth gave notice of his resignation on July 21 and was effective September 1. The CUSD Governing Board members appointed Malcomb to the seat on September 8" (KHTS and SCVNews.com, "Castaic Union School District Names New Board Member," 9 September 2016, https://scvnews.com/castaic-union-school-district-names-new-board-member).', null],
    28885 => ['2020, before August', '2020', 'resigned', 'contemporary', 'She resigned before the 2020 election: Trustee Area A was "previously filled by Stacy Dobbs before Dobbs resigned," according to the district ("School Board Elections in 3 SCV Districts Canceled," SCVNews.com, 2 October 2020, https://scvnews.com/school-board-elections-in-3-scv-districts-canceled). The date of the resignation is not given.', null],
    28249 => ['August 1, 2016', '2016-08-01', 'resigned', 'contemporary', 'Isaiah Talley "replaces Mike Shapiro, who stepped down from the board effective Aug. 1, having moved to another city" (Newhall School District, "School Board Fills Vacancy in Newhall, McGrath Attendance Areas," SCVNews.com, 12 September 2016, https://scvnews.com/school-board-fills-vacancy-in-newhall-mcgrath-attendance-areas/).', null],
    28208 => ['December 2018', '2018-12', 'expired', 'contemporary', 'She did not stand again in 2018: three candidates sought Area 2 "after Christy Smith announced her intent to run for the 18th District State Assembly seat" (' . $SIG18 . ').', null],
    28455 => ['December 8, 2009', '2009-12-08', 'resigned', 'contemporary', '"J. Michael McGrath, Jr. won re-election to the Newhall board even though he stated prior to the election that he would be unable to serve. He will submit a resignation effective December 8th, creating the open seat" (Newhall School District, "Unique Situation After Election Results in Open Board Seat," 4 November 2009, as carried by SCVTV, https://scvtv.com/npnews/nsd110409.html).', null],
    28461 => [null, null, 'expired', 'contemporary', 'He did not stand again in 2018: Ernesto Smith filed "after current board President Philip Ellis announced he would be not running" (' . $SIG18 . ').', null],
    28427 => ['July 13, 2022', '2022-07-13', 'died', 'certified', 'He died in office on Wednesday, 13 July 2022, as the board\'s vice president: "SCV Water is sad to report the passing of Board Vice President Jerry Gladbach on Wednesday" (SCV Water, news release, 18 July 2022, https://www.YourSCVWater.com/sites/default/files/SCVWA/newscenter/Press%20Release/2022/Press-Release_-Jerry-Gladbach_announcement.pdf); the board\'s Resolution SCV-329 records that he "served on the Santa Clarita Valley Water Agency (Agency) Board of Directors from January 2018 through July 2022."', null],
    28421 => ['January 2018', '2018-01', 'resigned', 'certified', 'He left the board in January 2018: "William Pecsi served on the Castaic Lake Water Agency Board of Directors from December 1998 to January 2018" (SCV Water Board, Resolution No. SCV-20, "Honoring and Commending William Pecsi," 20 February 2018, https://www.YourSCVWater.com/sites/default/files/SCVWA/approved-resolutions/SCV-Water-Approved-Resolution-022018-Resolution-SCV-20.pdf).', null],
    28415 => ['July 20, 2022', '2022-07-20', 'resigned', 'contemporary', '"My letter of resignation went in two days ago," he told The Signal; "It becomes effective midnight on the 20th" (The Signal, "Atkins to resign from SCV Water board," 13 July 2022, as captured by the Wayback Machine, https://web.archive.org/web/20250718005029/https://signalscv.com/2022/07/atkins-to-resign-from-scv-water-board/). The board appointed Ken Petersen to the seat on 29 August 2022.', null],
    28477 => [null, null, 'reelected', 'retrospective', 'He served on: he was the board\'s president when he died in June 2005 (Sarah Donner, "Gary Murr, Saugus School Board President," The Signal, 25 June 2005, as carried on SCVHistory.com, https://scvhistory.com/scvhistory/sg062505.htm).', null],
    28481 => ['August 16, 2020', '2020-08-16', 'resigned', 'contemporary', '"I announced that I\'ll be filing notice to the L.A. County Office of Education that my last day of service on the governing board for SUSD will be on Aug. 16," she wrote, on moving out of the district (The Signal, "Saugus Union School District board president steps down, cites moving out of district," 29 July 2020, as captured by the Wayback Machine, https://web.archive.org/web/20250709133137/https://signalscv.com/2020/07/saugus-union-school-district-board-president-steps-down-cites-moving-out-of-district/).', null],
    28473 => ['October 2, 2023', '2023-10-02', 'resigned', 'contemporary', 'The superintendent shared "a statement from Love announcing her intention to resign effective Oct. 2" (Perry Smith, The Signal, "SUSD board member announces plan to step down," 18 September 2023, as captured by the Wayback Machine, https://web.archive.org/web/20250617203926/https://signalscv.com/2023/09/susd-board-member-announces-plan-to-step-down/). Patti Garibay was appointed to the seat.', null],
    28233 => ['December 1999', '1999-12', 'expired', 'roster', 'He did not stand for the Sulphur Springs board in 1999; he stood that year for the Hart district\'s board instead and was not elected (' . $CEDA99 . ').', null],
    28509 => [null, null, 'reelected', 'roster', 'He served on: no contest for his seat is recorded in 2005 or 2009, and in 2013 he stood as the incumbent and was not elected (California Elections Data Archive, CEDA2013Data, "Michael. Hogan," incumbent).', null],
    28503 => [null, null, 'reelected', 'certified', 'Returned without a contest in 2018. ' . $CANC18, null],
    28501 => [null, null, 'reelected', 'certified', 'Returned without a contest in 2018. ' . $CANC18, null],
    28883 => [null, null, 'reelected', 'certified', 'Returned without a contest in 2020: the County\'s final list of cancelled elections for 3 November 2020 names "Sulphur Springs Union Elementary (Trustee Area 1, 2, and 3 U/T ending 12/22)" (County of Los Angeles, Registrar-Recorder/County Clerk, General Election November 3, 2020, Final List of Cancelled Elections).', null],
    28809 => ['February 1, 2017', '2017-02-01', 'resigned', 'contemporary', '"Hall submitted his resignation, effective February 1, 2017, to the Los Angeles County Board of Education on January 19, 2017" (William S. Hart Union High School District, "Hart District Votes To Appoint Hall\'s Replacement," SCVNews.com, 3 February 2017, https://scvnews.com/hart-district-votes-to-appoint-halls-replacement/). Cherise Moore was appointed to the seat in 2017.', null],
    28606 => ['May 2023', '2023-05', 'resigned', 'certified', '"Webb submitted his resignation to the Los Angeles County Board of Education on May 2, 2023. He is stepping down due to family obligations" (William S. Hart Union High School District, "Hart District Governing Board Selects Erin McKeon Wilson to Fill Vacant Seat," 28 June 2023, https://www.hartdistrict.org/apps/news/article/1782917).', null],
];
/* New terms: [person id from holding, body from holding, start printed, start edtf, end printed, end edtf, selection, howEnded, startEv, endEv, footnote] */
$NEW = [
    [28517, 'December 2007', '2007-12', 'December 2011', '2011-12', 'elected', 'reelected', 'contemporary', 'contemporary', '"He was elected to the Board in 2003, 2007, and 2011" (' . $KHTS13 . ').'],
    [28517, 'December 2011', '2011-12', 'December 2015', '2015-12', 'elected', 'expired', 'contemporary', 'contemporary', '"He was elected to the Board in 2003, 2007, and 2011"; "His term ends in December 2015" (' . $KHTS13 . ').'],
    [28509, 'December 2005', '2005-12', 'December 2013', '2013-12', '', 'expired', 'roster', 'roster', 'No contest for his seat is recorded in 2005 or 2009; in 2013 he stood as the incumbent and was not elected (California Elections Data Archive, CEDA2013Data, "Michael. Hogan," incumbent, not elected). Whether he held one term or two in these years is not recorded.'],
    [28477, 'December 2001', '2001-12', 'June 2005', '2005-06', '', 'died', 'retrospective', 'contemporary', '"Funeral services are scheduled Monday for Saugus Union School District board President Gary Murr, who died suddenly this week. He was 58" (Sarah Donner, "Gary Murr, Saugus School Board President," The Signal, 25 June 2005, as carried on SCVHistory.com, https://scvhistory.com/scvhistory/sg062505.htm).'],
];
$plan = [];
foreach ($E as $id => [$ep, $ee, $how, $ev, $note, $repl]) {
    $h = $get($id); if (!$h) { $bad[] = "#$id missing"; continue; }
    $done = $repl !== null ? str_contains(json_encode($h->footnotes), 'Lester M. Freeman, III') : str_contains(json_encode($h->footnotes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), mb_substr($note, 0, 60));
    echo "#$id " . $h->holdingPerson->one()?->title . ': ' . ($done ? 'already applied' : ($ee ? "end $ee" : 'end kept') . ($how ? ", $how" : '') . ($repl !== null ? ", footnote " . ($repl + 1) . ' replaced' : ', footnote added')) . PHP_EOL;
    if (!$done) { $plan[$id] = [$h, $ep, $ee, $how, $ev, $note, $repl]; }
}
$newPlan = [];
foreach ($NEW as $n) {
    $src = $get($n[0]); $p = $src?->holdingPerson->one(); $b = $src?->holdingBody->one(); $o = $src?->holdingOffice->one();
    $exists = $p && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'holdingPerson'], ['targetElement' => $b, 'field' => 'holdingBody']])->termStartEdtf($n[2])->exists();
    echo '  new term ' . ($p?->title ?? '?') . " {$n[2]} to {$n[4]}, {$n[6]}: " . ($exists ? 'exists' : 'create') . PHP_EOL;
    if (!$exists) { $newPlan[] = [$p, $b, $o, $n]; }
}
/* Deaths on the person records. */
$D = [
    ['E. G. Gladbach', 'July 13, 2022', '2022-07-13', 'contemporary', 'He died in office on 13 July 2022, the vice president of the Santa Clarita Valley Water board.', $E[28427][4]],
    ['Gary Murr', 'June 2005', '2005-06', 'contemporary', 'He died in office in June 2005, the president of the Saugus Union School District board, aged 58.', $NEW[3][9]],
];
$deathPlan = [];
foreach ($D as [$name, $dp, $de, $dev, $sentence, $note]) {
    $p = Entry::find()->section('persons')->status(null)->title($name)->one(); if (!$p) { $bad[] = "$name has no record"; continue; }
    $done = (string)$p->deathDateEdtf === $de;
    echo "$name #{$p->id}: " . ($done ? 'death already recorded' : "death $dp; body " . (trim(strip_tags((string)$p->body)) ? 'has text, a paragraph appended' : 'empty, the sentence becomes the body')) . PHP_EOL;
    if (!$done) { $deathPlan[] = [$p, $dp, $de, $dev, $sentence, $note]; }
}
if (preg_match('~inventory/|\x{2014}~u', json_encode([$E, $NEW, $D], JSON_UNESCAPED_UNICODE))) { $bad[] = 'a repository path or an em dash'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$PROV = 'apply_term_endings_2026_10_04.php, 4 October 2026';
$rowsOf = fn($e) => array_values(array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? 'editorial-2026')], array_filter($e->footnotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $id => [$h, $ep, $ee, $how, $ev, $note, $repl]) {
        $rows = $rowsOf($h);
        if ($repl !== null && isset($rows[$repl])) { $rows[$repl]['note'] = $note; } else { $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $note, 'source' => 'editorial-2026']; }
        $v = ['footnotes' => $rows];
        if ($ee) { $v['termEnd'] = $ep; $v['termEndEdtf'] = $ee; }
        if ($how) { $v['howEnded'] = $how; }
        if ($ev) { $v['endEvidence'] = $ev; }
        $h->setFieldValues($v); if (!$el->saveElement($h)) { throw new \RuntimeException("#$id: " . json_encode($h->getFirstErrors())); } $n++;
    }
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($newPlan as [$p, $b, $o, $x]) {
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($ot->id);
        $v = ['holdingPerson' => [$p->id], 'holdingOffice' => [$o->id], 'holdingBody' => [$b->id], 'termStart' => $x[1], 'termStartEdtf' => $x[2], 'termEnd' => $x[3], 'termEndEdtf' => $x[4], 'howEnded' => $x[6], 'startEvidence' => $x[7], 'endEvidence' => $x[8], 'footnotes' => [['number' => '1', 'note' => $x[9], 'source' => 'editorial-2026']], 'recordProvenance' => $PROV];
        if ($x[5]) { $v['selectionMethod'] = $x[5]; }
        $h->setFieldValues($v); if (!$el->saveElement($h)) { throw new \RuntimeException('new term ' . $p->title . ': ' . json_encode($h->getFirstErrors())); } $n++;
    }
    foreach ($deathPlan as [$p, $dp, $de, $dev, $sentence, $note]) {
        $rows = $rowsOf($p); $num = count($rows) + 1; $rows[] = ['number' => (string)$num, 'note' => $note, 'source' => 'editorial-2026'];
        $body = trim((string)$p->body); $body = ($body ? $body : '') . '<p>' . $sentence . '[' . $num . ']</p>';
        $v = ['deathDate' => $dp, 'deathDateEdtf' => $de, 'deathEvidence' => $dev, 'footnotes' => $rows, 'body' => $body];
        if (!trim((string)$p->bodyAuthorship)) { $v['bodyAuthorship'] = 'editorial-2026'; }
        $p->setFieldValues($v); if (!$el->saveElement($p)) { throw new \RuntimeException($p->title . ': ' . json_encode($p->getFirstErrors())); } $n++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = $get(28427)->howEnded->value === 'died' && $get(28606)->howEnded->value === 'resigned';
echo 'READ-BACK ' . ($ok ? "OK: $n writes" : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('apply_term_endings_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', '23 term endings from the survey: resignations, deaths in office, corrected ends; Huffaker, Hogan, Murr later terms; Gladbach and Murr deaths');
if (!$ok) { throw new \RuntimeException('apply_term_endings: read-back failed'); }
