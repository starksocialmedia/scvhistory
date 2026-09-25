/**
 * Pico Canyon before Mentry: Sanford Lyon, William Wirt Jenkins, and the well
 * they are said to have drilled there with Henry Clay Wiley.
 *
 * Neither man had a record. "Sanford Lyon" is in 23 records by exact name and
 * 26 counting "Cyrus ... twin brother of Sanford"; Jenkins is in 2 by exact
 * name and 13 under any of his names. Wiley is #331.
 *
 * EVERY DATE SAYS HOW IT IS KNOWN. The partnership is known only from accounts
 * written later: Jenkins himself in 1906, Walling in 1934, a typed copy of the
 * 1869 agreement published by Kreider in 1952, Perkins in 1958. The 1860s
 * record itself shows Lyon skimming the Pico seeps in 1866 and Wiley's own
 * company tunnelling in Wiley Canyon from 1865, and does not mention Jenkins
 * and oil at all. So the event is dated 186X with retrospective evidence, and
 * the bodies say which sources are contemporary.
 *
 * WHERE THE SOURCES DISAGREE, BOTH ARE KEPT. Lyon's death: 1881, 1882 or 1885,
 * held as the EDTF set [1881,1882,1885] with each source in a footnote.
 * Jenkins's birth: 12 October 1833 or 1835, held as [1833-10-12,1835-10-12].
 * Perkins dates the well 1870, outside 186X; that is in the recordDates and
 * the body rather than hidden by the range.
 *
 * NO SIBLING RELATION FOR THE TWIN. Cyrus Lyon has no record, so the twinship
 * is stated in prose with its citation, not stored.
 *
 * The event needs startEvidence on its layout: add_event_evidence_field.php.
 *
 * Idempotent: a record with the same title in the same section is found and
 * left alone. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_pico_1860s_records.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$WILEY = 331;
$PICO_PLACE = 16163;
$LYONS_STATION = 926;

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$cat = fn(string $slug) => \craft\elements\Category::find()->group('neighborhood')->slug($slug)->one();
$castaic = $cat('castaic'); $newhall = $cat('newhall'); $pico = $cat('pico-canyon');
$wiley = \craft\elements\Entry::find()->id($WILEY)->section('persons')->status(null)->one();
$place = \craft\elements\Entry::find()->id($PICO_PLACE)->section('places')->status(null)->one();
$station = \craft\elements\Entry::find()->id($LYONS_STATION)->section('places')->status(null)->one();
if (!$castaic || !$newhall || !$pico || !$wiley || !$place || !$station) { echo 'a record or term this needs is missing' . PHP_EOL; return; }

$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'],
    array_keys($notes), $notes);
$row = fn(string $printed, string $iso, string $gran, string $label): array =>
    ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];

$PERKINS_1958 = 'A.B. Perkins, "History of Pico Canyon Oil Production," Historical Society of Southern California Quarterly, December 1958; record #1440';
$KREIDER = 'Kreider, "The Story of Ranger Bill Jenkins of Castaic, 1835-1916," 1952, /scvhistory/jenkins_kreider1952.htm';
$REYNOLDS_JENKINS = 'Jerry Reynolds, record #2107';

$plan = [];

$plan['lyon'] = ['section' => 'persons', 'type' => 'person', 'title' => 'Sanford Lyon', 'fields' => [
    'fullName' => 'Sanford Lyon',
    'personAliases' => "S. Lyon\nRobert (Sanford) Lyon\nSanford Lyons\nStanford Lyon",
    'birthDate' => 'November 20, 1831',
    'birthDateEdtf' => '1831-11-20',
    'birthEvidence' => 'retrospective',
    'birthplace' => 'Machias, Maine',
    'deathDateEdtf' => '[1881,1882,1885]',
    'deathEvidence' => 'retrospective',
    'occupation' => 'Stage station keeper; postmaster',
    'neighborhood' => [$newhall->id],
    'bodyAuthorship' => 'editorial-2026',
    'recordProvenance' => 'editorial-2026, create_pico_1860s_records.php, from the sources in its footnotes',
    'body' => implode("\n\n", [
        'Sanford Lyon kept the stage station on the road north of the San Fernando Pass that carried the family name, and from 1869 was postmaster of Petroleopolis, the post office there.[1] He and his twin brother Cyrus were born in Machias, Maine, on 20 November 1831.[2]',
        'He was at the Pico Canyon oil seeps by 1866, when a Los Angeles paper reported that "Mr. S. Lyon is busy dipping the oil from the holes."[3] Later accounts credit him with drilling the first well at Pico, with Henry Clay Wiley and William Wirt Jenkins, but they disagree on the year, 1869 or 1870, and on who his partners were.[4] A well of his was pumping by 1876.[5]',
        'The sources give three years for his death: 1881, 1882 and 1885.[6]',
    ]),
    'footnotes' => $fn([
        'Jerry Reynolds, record #2075: "Lyon\'s Station, operated chiefly by Sanford." Alan Pollack, 2012, /scvhistory/pollack0912lyon.htm: Petroleopolis post office from 1867, "Sanford Lyon acting as its postmaster beginning in 1869." Both retrospective.',
        'Pollack, 2012: "The Lyon brothers were twins born to Henry and Betsy Lyon in Machias, Maine, in 1831." Reynolds, record #2075: "Born on November 20, 1831, twin brothers Sanford and Cyrus Lyon of Machias, Maine." Cyrus was the Ranger captain; the two are often confused.',
        'Los Angeles Semi-Weekly News, 1 June 1866, quoted in ' . $PERKINS_1958 . ', note 16. Contemporary. It shows him skimming the seep pits at Pico Spring, not drilling.',
        'The agreement of 8 January 1869, signed "W.W. Jenkins, Sanford Lyons, H.C. Wiley," survives only as a typed copy in ' . $KREIDER . '. Walling, California Oil Fields, Division of Oil and Gas, 1934, quoted by Perkins in record #1430: "a spring-pole hole 140 feet deep in 1869," Lyon alone. ' . $PERKINS_1958 . ': 1870, and his informant John Saunders names "Sanford Lyon and Louis Hanscomb." All retrospective.',
        'San Francisco Chronicle, 28 May 1877, /scvhistory/sw_sfchronicle052877.htm: "one more well on this tract which was put down by Mr. Lyon, and which has been pumping fine oil for over a year." Contemporary.',
        '1885: Perkins, record #1426, "lies in the graveyard at Lyon Station, since 1885." 1882: Pollack, 2012, "the death of Sanford at age 51 in 1882." About 1881: the obituary of his son Addi Lyon, /scvhistory/obituary_addiwarrenlyon_tlp.htm, born 25 March 1873, "His father died when he was 8 years old."',
    ]),
    'recordDates' => [
        $row('November 20, 1831', '1831-11-20', 'day', 'born, per Reynolds; retrospective'),
        $row('1881', '1881-01-01', 'year', 'died, inferred from son Addi\'s obituary'),
        $row('1882', '1882-01-01', 'year', 'died, per Pollack 2012'),
        $row('1885', '1885-01-01', 'year', 'died, per Perkins'),
    ],
]];

$plan['jenkins'] = ['section' => 'persons', 'type' => 'person', 'title' => 'William Wirt Jenkins', 'fields' => [
    'fullName' => 'William Wirt Jenkins',
    'personAliases' => "William Willoby Jenkins\nW.W. Jenkins\nWilliam Jenkins\nWirt Jenkins\nBill Jenkins\nRanger Bill Jenkins",
    'birthDateEdtf' => '[1833-10-12,1835-10-12]',
    'birthEvidence' => 'retrospective',
    'birthplace' => 'Circleville, Ohio',
    'deathDate' => 'October 19, 1916',
    'deathDateEdtf' => '1916-10-19',
    'deathEvidence' => 'certified',
    'occupation' => 'California Ranger; undersheriff; rancher',
    'neighborhood' => [$castaic->id],
    'bodyAuthorship' => 'editorial-2026',
    'recordProvenance' => 'editorial-2026, create_pico_1860s_records.php, from the sources in its footnotes',
    'body' => implode("\n\n", [
        'William Wirt Jenkins was a California Ranger and later a county undersheriff, and from 1878 ranched on Castaic Creek.[1] His family Bible records him as William Willoby Jenkins; he went by Wirt.[2]',
        'He wrote in 1906 that he and Sanford Lyon had visited the Pico oil springs with Francisco Lopez in 1854.[3] In 1869, by an agreement that survives only as a typed copy, he joined Lyon and Henry Clay Wiley to drill a well at "Camp Pico."[4] No account written in the 1860s connects him with oil.',
        'He was born near Circleville, Ohio, on 12 October, in 1833 or 1835; the sources differ.[5] He died on 19 October 1916.[6]',
    ]),
    'footnotes' => $fn([
        'Reynolds, record #2135: "a California Ranger and later county undersheriff." ' . $REYNOLDS_JENKINS . ': "staked a claim on Castaic Creek in 1872 but did not settle there until 1878."',
        $KREIDER . ': "In the family Bible he was recorded as William Willoby Jenkins. William preferred to be called \'Wirt\'."',
        'W.W. Jenkins, "California Gold Discoveries Before (and After) Lopez 1842," 1906, /scvhistory/hssc1906jenkins.htm: "In 1854 W.W. Jenkins and Sanford Lyon, at the instance of and with Francisco Lopez, visited the oil springs." Written more than fifty years after the fact.',
        $KREIDER . ', "copied from original agreement by June Jenkins Kinler." Its postscript mentions Alex Mentry taking over, so as it survives it was written after 1875.',
        '1835: ' . $KREIDER . '. 1833: ' . $REYNOLDS_JENKINS . '. The 1910 census gives his age as 74, record #4363, which fits 1835 or 1836.',
        'The legacy page cites his death certificate: "Death Cert 10-19-1916," /scvhistory/ap2219.htm. The certificate itself is not held. ' . $REYNOLDS_JENKINS . ' has him dying five years after a shooting in 1916, which the certificate contradicts.',
    ]),
    'recordDates' => [
        $row('October 12, 1833', '1833-10-12', 'day', 'born, per Reynolds'),
        $row('October 12, 1835', '1835-10-12', 'day', 'born, per Kreider 1952'),
        $row('10-19-1916', '1916-10-19', 'day', 'died, per the death certificate as the legacy page cites it'),
    ],
]];

$plan['event'] = ['section' => 'events', 'type' => 'event', 'title' => 'Lyon, Wiley and Jenkins Drill at Pico Canyon', 'fields' => [
    'eventDate' => 'late 1860s',
    'eventDateEdtf' => '186X',
    'startEvidence' => 'retrospective',
    'eventPlaces' => [$place->id],
    'neighborhood' => [$pico->id],
    'body' => implode("\n\n", [
        'Three men from the Newhall area, Sanford Lyon, Henry Clay Wiley and William Wirt Jenkins, are said to have drilled a well together in Pico Canyon in the late 1860s, near where Alex Mentry would bring in Pico No. 4 in 1876.[1]',
        'Every account of the partnership was written later. The one dated document, an agreement of 8 January 1869 that names Lyon as overseer and the first well "Lyons No. 1," is known only as a typed copy of a copy, published in 1952, with a postscript written after Mentry arrived.[2] Walling, in 1934, credits Lyon alone with a 140-foot spring-pole hole in 1869. Perkins, in 1958, gives 1870 and names Lyon and Louis Hanscomb.[3]',
        'What the 1860s record does show is Lyon skimming oil from the Pico seeps in 1866, and Wiley\'s own company running tunnels and a well in Wiley Canyon from 1865.[4] The date is held as the 1860s, with the year uncertain, on retrospective evidence.',
    ]),
    'footnotes' => $fn([
        'Leon Worden, the Mentry biography on /scvhistory/ch1070.htm: "explored in the late 1860s by Newhall entrepreneurs Sanford Lyon, Henry Clay Wiley and Los Angeles lawman William Jenkins," and "four new wells (Pico No. 1-4) near the old Lyon-Wiley-Jenkins hole."',
        $KREIDER . ': "We agree to put up an equal amount of money to drill one well or more at Camp Pico ... Sanford to oversee operations ... Will call the first well Lyons No. 1 ... /s/ W.W. Jenkins Sanford Lyons H.C. Wiley Jan. 8, 1869. We went down about 50 ft., got about 9 barrels a day. When Alex Mentry took over operations ..."',
        'Walling, California Oil Fields, 1934, quoted in record #1430. ' . $PERKINS_1958 . '.',
        'Los Angeles Semi-Weekly News, 1 June 1866, quoted in record #1440. Christopher Leaming to Edward F. Beale, 3 July 1866, /scvhistory/sw_leaming070366.htm: "The largest work going on is upon the Wiley Springs; the company are running five tunnels and one well." Peckham, 1866, /scvhistory/sw_peckham1866.htm: the "Wylie Springs Oil Company ... Operations were commenced last August." All contemporary; none names Jenkins.',
    ]),
    'recordDates' => [
        $row('Jan. 8, 1869', '1869-01-08', 'day', 'agreement date, typed copy in Kreider 1952'),
        $row('1869', '1869-01-01', 'year', 'per Walling 1934, Lyon alone'),
        $row('1870', '1870-01-01', 'year', 'per Perkins 1958'),
    ],
]];

/* --------------------------------------------------------------- report */

foreach ($plan as $k => &$p) {
    $p['sectionId'] = $svc->getSectionByHandle($p['section'])->id;
    $p['typeId'] = $svc->getEntryTypeByHandle($p['type'])->id;
    $layout = $svc->getEntryTypeByHandle($p['type'])->getFieldLayout();
    $p['absent'] = array_values(array_filter(array_keys($p['fields']), fn($h) => !$layout->getFieldByHandle($h)));
    $p['existing'] = \craft\elements\Entry::find()->section($p['section'])->status(null)->title($p['title'])->one();
    echo PHP_EOL . ($p['existing'] ? 'EXISTS #' . $p['existing']->id . ' ' : 'NEW       ') . $p['section'] . '  ' . $p['title'] . PHP_EOL;
    foreach ($p['fields'] as $h => $v) {
        if ($h === 'body') { foreach (explode("\n\n", $v) as $para) { echo '   | ' . wordwrap($para, 96, "\n   | ") . PHP_EOL; } continue; }
        if ($h === 'footnotes') { foreach ($v as $f) { echo '   [' . $f['number'] . '] ' . wordwrap($f['note'], 92, "\n       ") . PHP_EOL; } continue; }
        $show = is_array($v) ? json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$v;
        echo '   ' . str_pad($h, 18) . mb_substr(str_replace("\n", ' / ', $show), 0, 110) . PHP_EOL;
    }
    if ($p['absent']) { echo '   NOT ON THE LAYOUT: ' . implode(', ', $p['absent']) . PHP_EOL; }
}
unset($p);
echo PHP_EOL . 'relations: event -> Lyon, Jenkins (new) and Wiley #' . $WILEY . '; Lyon onto place #' . $LYONS_STATION . ' ' . $station->title . ' (placePeople, appended)' . PHP_EOL;

$blocked = array_filter($plan, fn($p) => $p['absent'] && !$p['existing']);
if ($blocked) { echo PHP_EOL . 'BLOCKED: fields missing from a layout. Run add_event_evidence_field.php first.' . PHP_EOL; }
if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($blocked) { return; }

/* ---------------------------------------------------------------- apply */

$made = [];
foreach (['lyon', 'jenkins', 'event'] as $k) {
    $p = $plan[$k];
    if ($p['existing']) { $made[$k] = $p['existing']; continue; }
    $e = new \craft\elements\Entry();
    $e->sectionId = $p['sectionId'];
    $e->setTypeId($p['typeId']);
    $e->title = $p['title'];
    $v = $p['fields'];
    if ($k === 'event') { $v['eventPersons'] = [$made['lyon']->id, $made['jenkins']->id, $WILEY]; }
    $e->setFieldValues($v);
    if (!$elements->saveElement($e)) { throw new \RuntimeException('create_pico_1860s_records: ' . $k . ' ' . json_encode($e->getErrors())); }
    $made[$k] = $e;
    echo 'created #' . $e->id . '  ' . $p['title'] . PHP_EOL;
}
$people = $station->placePeople->status(null)->ids();
if (!in_array($made['lyon']->id, $people, true)) {
    $station->setFieldValue('placePeople', array_merge($people, [$made['lyon']->id]));
    if (!$elements->saveElement($station)) { throw new \RuntimeException('create_pico_1860s_records: place ' . json_encode($station->getErrors())); }
}

/* ------------------------------------------------------------ read back */

$short = [];
foreach ($made as $k => $e) {
    if ($plan[$k]['existing']) { continue; }
    $f = \craft\elements\Entry::find()->id($e->id)->status(null)->one();
    foreach ($plan[$k]['fields'] as $h => $v) {
        $got = $f->getFieldValue($h);
        if ($got instanceof \craft\elements\db\ElementQuery) { if ($got->status(null)->ids() != $v) { $short[] = "$k.$h"; } }
        elseif (is_array($v)) { if (count((array)$got) !== count($v)) { $short[] = "$k.$h rows"; } }
        elseif (trim((string)($got instanceof \craft\fields\data\SingleOptionFieldData ? $got->value : $got)) !== trim((string)$v)) { $short[] = "$k.$h"; }
    }
}
$ev = \craft\elements\Entry::find()->id($made['event']->id)->status(null)->one();
if (count($ev->eventPersons->status(null)->ids()) !== 3) { $short[] = 'event.eventPersons'; }
$st = \craft\elements\Entry::find()->id($LYONS_STATION)->status(null)->one();
if (!in_array($made['lyon']->id, $st->placePeople->status(null)->ids(), true)) { $short[] = 'place.placePeople'; }
echo PHP_EOL . 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: 3 records, relations and footnotes') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('create_pico_1860s_records.php', 4, $short ? 'SHORT: ' . implode(', ', $short) : 'verified: 3 records and the place relation',
    'Lyon #' . $made['lyon']->id . ', Jenkins #' . $made['jenkins']->id . ', event #' . $made['event']->id . ' 186X retrospective');
if ($short) { throw new \RuntimeException('create_pico_1860s_records: read-back failed'); }
