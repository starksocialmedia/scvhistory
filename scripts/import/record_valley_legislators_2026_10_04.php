/**
 * The valley's seat in the Assembly, the State Senate and the House, as continuous seats with their terms
 * (Nathan, 4 October 2026, approving inventory/review/legislative-districts-2026-10-04.md: "continuous seat
 * following the district holding most of the valley, partial districts listed on the body page without term
 * records, partial-district members getting terms only where they already have records. Build it").
 *
 *  1. Three seat places, one a chamber: "Assembly district covering the Santa Clarita Valley" and its Senate
 *     and House twins, tied to the body, districtKind assembly, senate or congressional, with no number:
 *     the number changes with the plans. Each term carries the number in force (seatLabel) and the plan
 *     (districtPlan).
 *  2. The terms, from scripts/import/data/valley-legislators-2026-10-04.json (build_valley_legislators_data.py):
 *     one holding per run of service under one number and one plan. Christy Smith's existing Assembly term
 *     (#28278) gains its seat and plan; McKeon's (#26980, 1993 to 2015) is cut at the plans' edges, 2003 and
 *     2013, into three, so each records the lines in force. No partial-district member had a record, so no
 *     partial terms are made.
 *  3. Person records for the main-seat members the archive lacked: Pete Knight, Keith Richman, Suzette
 *     Martinez Valladares, Pilar Schiavo, Don Rogers, Sharon Runner, Steve Knight, Katie Hill, Mike Garcia,
 *     George Whitesides. Public life only. Pete Knight and Sharon Runner died in office (Nathan: "Both need
 *     that on their person records, same as Gladbach and Murr"), from the Record of State Senators.
 *     Steve-Knight.jpg and Sharon-Runner.jpg, waiting in inventory/incoming for these records, become their
 *     portraits, recorded as George Runner's was.
 *  4. Patsy Ayala (Nathan: "Ayala was staff, not a member ... Correct her record: that is still public life
 *     and worth recording, but it is not a seat"): three affiliations, employed, and a paragraph on her
 *     record saying so, from KHTS/SCVNews of 2 June 2014 and her College of the Canyons biography. The
 *     City's wording is quoted as the City's and left as it is.
 *  5. United States Congress, with the House nested under it, for the Congress seal Nathan handed over.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_valley_legislators_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $bad = [];
$PROV = 'record_valley_legislators_2026_10_04.php, 4 October 2026';
$D = json_decode(file_get_contents("$root/scripts/import/data/valley-legislators-2026-10-04.json"), true)['terms'];
$BODY = ['assembly' => 28271, 'senate' => 28273, 'house' => 28269];
$ROLE = ['assembly' => 18313, 'senate' => 18387, 'house' => 18409];
$SEAT = [
    'assembly' => ['Assembly district covering the Santa Clarita Valley', 'assembly', 'The seat in the California State Assembly that represents most of the Santa Clarita Valley, followed across the redistricting plans: the 36th District under the 1991 lines, the 38th under the 2001 and 2011 lines, and the 40th under the 2021 lines. Each term records the district number in force and the plan that set it.'],
    'senate' => ['Senate district covering the Santa Clarita Valley', 'senate', 'The seat in the California State Senate that represents most of the Santa Clarita Valley, followed across the redistricting plans: the 17th District under the 1991 and 2001 lines, the 21st under the 2011 lines, and the 23rd under the 2021 lines. Each term records the district number in force and the plan that set it.'],
    'house' => ['Congressional district covering the Santa Clarita Valley', 'congressional', 'The seat in the United States House of Representatives that represents most of the Santa Clarita Valley, followed across the redistricting plans: the 25th District under the 1991, 2001 and 2011 lines, and the 27th under the 2021 lines and the 2025 congressional lines. Each term records the district number in force and the plan that set it.'],
];
$EXIST = ['Cameron Smyth' => 16380, 'Scott Wilk' => 335, 'Dante Acosta' => 341, 'Christy Smith' => 25389, 'Buck McKeon' => 18791, 'George Runner' => 18747];
$SEN_REC = 'Secretary of the Senate, Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88, read 4 October 2026';
$NEW = [
    'Pete Knight' => ['alias' => "William J. \"Pete\" Knight\nWilliam J. Knight", 'role' => 18387, 'death' => ['May 7, 2004', '2004-05-07', "$SEN_REC, note 113: \"Died in office May 7, 2004. Succeeded by George Runner.\""]],
    'Keith Richman' => ['role' => 18313], 'Suzette Martinez Valladares' => ['alias' => 'Suzette Valladares', 'role' => 18387], 'Pilar Schiavo' => ['role' => 18313],
    'Don Rogers' => ['role' => 18387],
    'Sharon Runner' => ['role' => 18387, 'death' => ['July 14, 2016', '2016-07-14', "$SEN_REC, note 189: re-elected at a special primary election, March 17, 2015, \"Died in office July 14, 2016.\""], 'portrait' => ['Sharon-Runner.jpg', 'sharon-runner.jpg', 'Sharon Runner, official portrait', 'From the website of a public body she served, retrieved 4 October 2026 (Nathan Imhoff); which page is not recorded. Permission to republish is not established.']],
    'Steve Knight' => ['role' => 18409, 'portrait' => ['Steve-Knight.jpg', 'steve-knight.jpg', 'Steve Knight, official portrait', 'From the website of a public body he served, retrieved 4 October 2026 (Nathan Imhoff); which page is not recorded. Permission to republish is not established.']],
    'Katie Hill' => ['role' => 18411], 'Mike Garcia' => ['role' => 18409], 'George Whitesides' => ['role' => 18409],
];
$person = fn($n) => isset($EXIST[$n]) ? Entry::find()->id($EXIST[$n])->status(null)->one() : Entry::find()->section('persons')->status(null)->title($n)->one();
foreach ($EXIST as $n => $id) { $p = $person($n); if (!$p || stripos($p->title, explode(' ', $n)[1]) === false) { $bad[] = "#$id is not $n"; } }
foreach ($BODY as $k => $id) { if (!Entry::find()->id($id)->status(null)->one()) { $bad[] = "body #$id missing"; } }
foreach ($NEW as $n => $c) { if (isset($c['portrait']) && !is_file("$root/inventory/incoming/{$c['portrait'][0]}") && !is_file("$root/inventory/incoming/done/{$c['portrait'][0]}")) { $bad[] = "{$c['portrait'][0]} missing"; } }
$smith = Entry::find()->id(28278)->status(null)->one(); $mck = Entry::find()->id(26980)->status(null)->one();
if ($smith?->holdingPerson->one()?->id !== 25389 || $mck?->holdingPerson->one()?->id !== 18791) { $bad[] = 'Smith #28278 or McKeon #26980 not as expected'; }
foreach ($SEAT as $k => [$t]) { echo "seat: $t: " . (Entry::find()->section('places')->status(null)->title($t)->exists() ? 'exists' : 'create') . PHP_EOL; }
foreach ($NEW as $n => $c) { echo "person: $n: " . ($person($n) ? 'exists' : 'create') . (isset($c['death']) ? ", died in office {$c['death'][0]}" : '') . (isset($c['portrait']) ? ", portrait {$c['portrait'][0]}" : '') . PHP_EOL; }
foreach ($D as $t) { echo "term: {$t['person']}, {$t['seatLabel']}, {$t['districtPlan']} lines, " . ($t['termStartEdtf'] ?? '(kept)') . ' to ' . ($t['termEndEdtf'] === null ? '(kept)' : ($t['termEndEdtf'] ?: 'now')) . ", {$t['howEnded']}, " . count($t['citations']) . ' citations' . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$n = 0; $fn = fn(array $notes) => array_values(array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($notes), $notes));
$tx = Craft::$app->getDb()->beginTransaction();
try {
    /* 1. seats */
    $placeSec = $svc->getSectionByHandle('places'); $placeType = $svc->getEntryTypeByHandle('place'); $seat = [];
    foreach ($SEAT as $k => [$t, $kind, $body]) {
        $s = Entry::find()->section('places')->status(null)->title($t)->one();
        if (!$s) { $s = new Entry(); $s->sectionId = $placeSec->id; $s->setTypeId($placeType->id); $s->title = $t;
            $s->setFieldValues(['districtKind' => $kind, 'placeOrganizations' => [$BODY[$k]], 'body' => $body, 'recordProvenance' => $PROV]);
            if (!$el->saveElement($s)) { throw new \RuntimeException("seat $t: " . json_encode($s->getFirstErrors())); } $n++; }
        $seat[$k] = $s->id;
    }
    /* 3. people */
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
    foreach ($NEW as $name => $c) {
        $p = $person($name);
        if (!$p) { $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id);
            $p->setFieldValues(array_filter(['fullName' => $name, 'personAliases' => $c['alias'] ?? '', 'roles' => [$c['role']], 'recordProvenance' => $PROV . ': a member for the valley\'s seat']));
            if (!$el->saveElement($p)) { throw new \RuntimeException("$name: " . json_encode($p->getFirstErrors())); } $n++; }
        if (isset($c['death']) && (string)$p->deathDateEdtf !== $c['death'][1]) {
            $p->setFieldValues(['deathDate' => $c['death'][0], 'deathDateEdtf' => $c['death'][1], 'deathEvidence' => 'certified', 'footnotes' => $fn([$c['death'][2] . '.'])]);
            if (!$el->saveElement($p)) { throw new \RuntimeException("$name death: " . json_encode($p->getFirstErrors())); } $n++; }
        if (isset($c['portrait']) && !$p->featuredImage->exists()) {
            [$file, $fname, $title, $src] = $c['portrait']; $path = is_file("$root/inventory/incoming/$file") ? "$root/inventory/incoming/$file" : "$root/inventory/incoming/done/$file";
            $a = Asset::find()->filename($fname)->one();
            if (!$a) { $tmp = sys_get_temp_dir() . "/$fname"; copy($path, $tmp);
                $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($fname); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
                if (!$el->saveElement($a)) { throw new \RuntimeException("$fname: " . json_encode($a->getFirstErrors())); }
                $a = Asset::find()->id($a->id)->one(); $a->title = $title; $a->alt = "Portrait of $name";
                $v = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'rightsNote' => 'No permission to republish is established.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $path), 'source' => $src];
                $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
                if (!$el->saveElement($a)) { throw new \RuntimeException("$fname fields: " . json_encode($a->getFirstErrors())); } $n++; }
            $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException("$name portrait"); } $n++;
        }
    }
    /* 2. terms */
    $hs = $svc->getSectionByHandle('officeHoldings'); $ht = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($D as $t) {
        $ch = $t['chamber']; $p = $person($t['person']);
        $v = ['holdingDistrict' => [$seat[$ch]], 'seatLabel' => $t['seatLabel'], 'districtPlan' => $t['districtPlan']];
        if ($t['person'] === 'Christy Smith') { $h = $smith; }
        elseif ($t['person'] === 'Buck McKeon' && $t['districtPlan'] === '1991') { $h = $mck; }
        else {
            $h = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'holdingPerson'], ['targetElement' => $BODY[$ch], 'field' => 'holdingBody']])->termStartEdtf($t['termStartEdtf'])->one();
            if (!$h) { $h = new Entry(); $h->sectionId = $hs->id; $h->setTypeId($ht->id); }
            $role = $t['person'] === 'Katie Hill' ? 18411 : $ROLE[$ch];
            $notes = $t['citations']; if ($t['note']) { $notes[] = $t['note']; }
            $v += ['holdingPerson' => [$p->id], 'holdingOffice' => [$role], 'holdingBody' => [$BODY[$ch]], 'selectionMethod' => 'elected', 'termStart' => $t['termStart'], 'termStartEdtf' => $t['termStartEdtf'],
                'howEnded' => $t['howEnded'], 'startEvidence' => 'certified', 'footnotes' => $fn($notes), 'recordProvenance' => $PROV];
            if ($t['termEnd']) { $v += ['termEnd' => $t['termEnd'], 'termEndEdtf' => $t['termEndEdtf'], 'endEvidence' => 'certified']; }
        }
        if ($h === $mck) {
            $cut = 'The term is recorded here to January 3, 2003, when the 2001 lines took effect; his service in the 25th District went on to January 3, 2015, in the terms that follow.';
            $old = array_values(array_filter(array_column($mck->footnotes, 'note'), fn($x) => !str_starts_with((string)$x, 'The term is recorded here to')));
            $v += ['termEnd' => $t['termEnd'], 'termEndEdtf' => $t['termEndEdtf'], 'howEnded' => 'reelected', 'endEvidence' => 'certified', 'footnotes' => $fn(array_merge($old, [$cut]))];
        }
        $h->setFieldValues($v); if (!$el->saveElement($h)) { throw new \RuntimeException("{$t['person']} {$t['seatLabel']}: " . json_encode($h->getFirstErrors())); } $n++;
    }
    /* 4. Ayala */
    $ay = Entry::find()->id(23093)->one(); $aSec = $svc->getSectionByHandle('affiliations'); $aType = $svc->getEntryTypeByHandle('affiliation');
    $KHTS = 'KHTS, "Ayala Joins Wilk Staff; Hough Now District Director," as published by SCVNews.com, 2 June 2014, https://scvnews.com/ayala-joins-wilk-staff-hough-now-district-director: Assemblyman Scott Wilk names Patsy Ayala field representative in his district office, for local media outreach.';
    $COC = 'College of the Canyons, Women\'s Conference, "Patsy Ayala," https://www.canyons.edu/community/womensconference/biopatsyayala.php, read 4 October 2026: "Senior Field Representative - Assemblywoman Suzette Valladares"; "She worked for the State of California Senate with Senator Scott Wilk\'s government team and previously during his term in the Assembly." The page is not dated.';
    $AFF = [
        [28271, 'Field Representative to Assemblyman Scott Wilk', '2014', '2014-06', 'contemporary', [$KHTS]],
        [28273, 'Staff of Senator Scott Wilk', '', '', 'retrospective', [$COC]],
        [28271, 'Senior Field Representative to Assemblywoman Suzette Martinez Valladares', '', '', 'retrospective', [$COC]],
    ];
    foreach ($AFF as [$b, $title, $s, $se, $ev, $notes]) {
        if (Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $ay, 'field' => 'affiliationPerson'], ['targetElement' => $b, 'field' => 'affiliationBody']])->affiliationTitle($title)->exists()) { continue; }
        $a = new Entry(); $a->sectionId = $aSec->id; $a->setTypeId($aType->id);
        $a->setFieldValues(array_filter(['affiliationPerson' => [$ay->id], 'affiliationBody' => [$b], 'affiliationKind' => 'employed', 'affiliationTitle' => $title, 'termStart' => $s, 'termStartEdtf' => $se,
            'affiliationEnded' => 'left', 'startEvidence' => $ev, 'footnotes' => $fn($notes), 'recordProvenance' => $PROV . ': staff, not a member (Nathan, 4 October 2026)']));
        if (!$el->saveElement($a)) { throw new \RuntimeException("Ayala $title: " . json_encode($a->getFirstErrors())); } $n++;
    }
    if (!str_contains((string)$ay->body, 'not as a member')) {
        $rows = $ay->footnotes; $k = count($rows);
        $para = "She served the Legislature as staff, not as a member: neither house's record of its members lists her.[" . ($k + 1) . "][" . ($k + 2) . "] In June 2014 Assemblyman Scott Wilk named her a field representative in his district office, for local media outreach.[" . ($k + 3) . "] A College of the Canyons biography later described her as senior field representative to Assemblywoman Suzette Martinez Valladares in the 38th District, having worked on Senator Wilk's staff and, before that, in his Assembly office.[" . ($k + 4) . "]";
        $add = ['Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79, read 4 October 2026: no member named Ayala.', $SEN_REC . ': no senator named Ayala.', $KHTS, $COC];
        foreach ($add as $i => $x) { $rows[] = ['number' => (string)($k + $i + 1), 'note' => $x, 'source' => 'editorial-2026']; }
        $ay->setFieldValues(['body' => rtrim((string)$ay->body) . "\n\n" . $para, 'footnotes' => $rows]);
        if (!$el->saveElement($ay)) { throw new \RuntimeException('Ayala body: ' . json_encode($ay->getFirstErrors())); } $n++;
    }
    /* 5. United States Congress */
    $os = $svc->getSectionByHandle('organizations'); $C = Entry::find()->section('organizations')->status(null)->title('United States Congress')->one();
    if (!$C) { $C = new Entry(); $C->sectionId = $os->id; $C->setTypeId($os->getEntryTypes()[0]->id); $C->title = 'United States Congress'; $C->setFieldValues(['orgType' => 'government', 'recordProvenance' => $PROV . ': for its seal, with the House under it']);
        if (!$el->saveElement($C)) { throw new \RuntimeException('Congress: ' . json_encode($C->getFirstErrors())); } $n++; }
    $H = Entry::find()->id(28269)->one(); if ($H->parentOrganization->one()?->id !== $C->id) { $H->setFieldValue('parentOrganization', [$C->id]); if (!$el->saveElement($H)) { throw new \RuntimeException('House parent'); } $n++; }
    $tx->commit();
} catch (\Throwable $e) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $e->getMessage() . PHP_EOL; throw $e; }
$count = fn($k) => Entry::find()->section('officeHoldings')->relatedTo(['targetElement' => $seat[$k], 'field' => 'holdingDistrict'])->count();
echo "READ-BACK: $n writes; terms on the seats: Assembly {$count('assembly')}, Senate {$count('senate')}, House {$count('house')}" . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('record_valley_legislators_2026_10_04.php', $n, 'verified', 'The valley\'s Assembly, Senate and House seats: three seats, 25 terms with plan and number, ten members, two deaths in office, Ayala as staff, United States Congress');
