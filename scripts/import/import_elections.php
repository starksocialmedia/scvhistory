/**
 * The Santa Clarita City Council elections, 1987 to 2024: the nine City Clerk
 * documents as document records, then every election, candidacy and source
 * fault they carry, then the office holdings the certified results support.
 *
 * Reads inventory/elections/elections.json (parse_election_results.py) and the
 * nine PDFs in inventory/elections/, each checked against the SHA-256 recorded
 * when they were first fetched (inventory/legacy/elections-manifest.json).
 *
 * THE RULES (Nathan, agreed 25 and 29 September 2026)
 *   - Only Resolution 12-9 declares winners. Its two, Bob Kellar and TimBen
 *     Boydston, are elected on certified evidence and the other three are not
 *     elected, certified. Every other election's outcomes are read from the
 *     vote order and a seat count no document states: elected or not elected,
 *     outcomeEvidence uncited, until the declaring resolutions are obtained.
 *     1987's five are retrospective: the City's own profile says the first
 *     council had five members.
 *   - Decimal points printed as thousands separators are source faults, each a
 *     record, the reading beside the printed figure, never silently corrected.
 *   - Candidates match person records on the full first name and the surname,
 *     never on initials: "Clyde Smyth" is not "Cameron Smyth". A first name
 *     that is only an initial matches nothing; a nickname printed in quotes
 *     ("Buck") counts as a first name. The 2014 results print surnames only,
 *     so none of that year's candidates is linked to a person.
 *   - A candidate becomes a person record only on the two triggers (stood more
 *     than once, or already in the archive). Two are created because the
 *     certified 2012 terms need them: Bob Kellar and TimBen Boydston, public
 *     facts only.
 *
 * THE OFFICE HOLDINGS. The certified results support two terms: Kellar and
 * Boydston, April 2012, "for the full term of four years". The four Smyth terms
 * written on 29 September claimed certified starts from the clerk's vote
 * summary; the summary declares no one, so those starts are corrected to
 * retrospective (the obituary, the City's biography), which is what they rest on.
 *
 * THE TITLE. The Election type has no title field in its layout, so it takes a
 * title format, "{electionDate} election" (a schema change: project config).
 *
 * Idempotent: documents by source URL, elections by date, candidacies by
 * election and printed name, faults by record and printed figure. Dry run by
 * default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_elections.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$root = \Craft::getAlias('@root');
$data = json_decode(file_get_contents("$root/inventory/elections/elections.json"), true);
$manifest = json_decode(file_get_contents("$root/inventory/legacy/elections-manifest.json"), true);
if (!$data || !$manifest) { echo 'REFUSING: elections.json or the manifest is missing (run parse_election_results.py)' . PHP_EOL; return; }
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
foreach (['elections', 'candidacies', 'sourceFaults', 'officeHoldings', 'documents'] as $h) { if (!$svc->getSectionByHandle($h)) { echo "REFUSING: section $h does not exist" . PHP_EOL; return; } }
$CITY = 394; $COUNCIL = 18327;
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);

/* ------------------------------------------------ the nine documents */
$DOCMETA = [
    'historical-results-7.pdf' => ['title' => 'General Municipal Elections: Historical Election Results, 1987 to 2012', 'by' => 'City of Santa Clarita, City Clerk', 'city' => true, 'date' => '', 'edtf' => '2012/..'],
    'resolutionNo129-6.pdf' => ['title' => 'Resolution No. 12-9: the results of the General Municipal Election of April 10, 2012', 'by' => 'City of Santa Clarita, City Council', 'city' => true, 'date' => 'April 2012', 'edtf' => '2012-04'],
    '2014ElectionResultsbyPreci-5.pdf' => ['title' => '2014 Election Results by Precinct', 'by' => 'City of Santa Clarita, City Clerk', 'city' => true, 'date' => 'April 2014', 'edtf' => '2014-04'],
    '2016StatementofVotesCast-4.pdf' => ['title' => 'Statement of Votes Cast, General Election, November 8, 2016: Santa Clarita City Council', 'by' => 'County of Los Angeles, Registrar-Recorder/County Clerk', 'city' => false, 'date' => 'November 2016', 'edtf' => '2016-11'],
    'LACountyFinalVoteCount-3.pdf' => ['title' => 'Final Vote Count, General Election, November 6, 2018: Santa Clarita City Council', 'by' => 'County of Los Angeles, Registrar-Recorder/County Clerk', 'city' => false, 'date' => 'November 2018', 'edtf' => '2018-11'],
    'Final-Election-Canvass-Res-2.pdf' => ['title' => 'Final Election Canvass, General Election, November 3, 2020: Santa Clarita City Council', 'by' => 'County of Los Angeles, Registrar-Recorder/County Clerk; exhibit to a City resolution', 'city' => false, 'date' => 'November 2020', 'edtf' => '2020-11'],
    'Final-Certficate-of-Canvas-1.pdf' => ['title' => 'Final Certificate of Canvass, General Election, November 8, 2022: Santa Clarita City Council', 'by' => 'County of Los Angeles, Registrar-Recorder/County Clerk', 'city' => false, 'date' => 'November 2022', 'edtf' => '2022-11'],
    '10880.pdf' => ['title' => 'Statement of Votes Cast, General Election, November 5, 2024: Santa Clarita City Council, District 1', 'by' => 'County of Los Angeles, Registrar-Recorder/County Clerk', 'city' => false, 'date' => 'November 2024', 'edtf' => '2024-11'],
    'LOCAL-APPT-LIST_-082625.pdf' => ['title' => 'Local Appointments List: boards, commissions and committees', 'by' => 'City of Santa Clarita, City Clerk', 'city' => true, 'date' => 'August 2025', 'edtf' => '2025-08'],
];
$docs = []; $docPlan = [];
echo 'DOCUMENTS' . PHP_EOL;
foreach ($manifest['documents'] as $m) {
    $file = basename($m['url']); $meta = $DOCMETA[$file] ?? null;
    if (!$meta) { echo "   REFUSING: no description for $file" . PHP_EOL; return; }
    $path = "$root/inventory/elections/$file";
    if (!is_file($path) || hash_file('sha256', $path) !== $m['sha256']) { echo "   REFUSING: $file is missing or differs from the copy fetched on 25 September" . PHP_EOL; return; }
    $e = Entry::find()->section('documents')->status(null)->sourcePath($m['url'])->one();
    $docs[$file] = $e;
    echo '   ' . ($e ? '#' . $e->id . ' exists: ' : 'create ') . $meta['title'] . PHP_EOL;
    if (!$e) { $docPlan[$file] = [$m, $meta, $path]; }
}
$docByKey = fn($key) => $docs[$data['documents'][$key]] ?? null;

/* ------------------------------------------------ people: matching and creating */
$norm = fn($s) => strtolower(trim(preg_replace('~[^A-Za-z -]~', '', \craft\helpers\StringHelper::toAscii($s))));
$keysOf = function (string $name) use ($norm): array {
    $name = preg_replace('~,?\s*\b(Jr|Sr|II|III|IV)\b\.?~', '', $name);
    $nicks = []; if (preg_match_all('~[“"]([^”"]+)[”"]~u', $name, $m)) { $nicks = $m[1]; }
    $plain = preg_replace(['~[“"][^”"]+[”"]~u', '~\([^)]*\)~'], ' ', $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($plain)), fn($w) => $w !== ''));
    if (count($t) < 2) { return []; }
    $last = $norm(end($t));
    $firsts = array_values(array_filter(array_slice($t, 0, -1), fn($w) => !preg_match('~^[A-Za-z]\.?$~', $w)));   /* never an initial */
    $keys = [];
    if ($firsts) { $keys[] = $norm($firsts[0]) . ' ' . $last; }
    foreach ($nicks as $n) { $keys[] = $norm($n) . ' ' . $last; }
    return array_unique($keys);
};
$index = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) {
    $names = [$p->title, (string)$p->fullName];
    foreach (preg_split('~[\n]~', (string)$p->personAliases) as $a) { if (trim($a)) { $names[] = trim($a); } }
    foreach ($names as $n) { foreach ($keysOf($n) as $k) { $index[$k][$p->id] = $p; } }
}
$NEW_PEOPLE = [
    'Bob Kellar' => ['aliases' => "Bob Kellar", 'occupation' => 'City councilman'],
    'TimBen Boydston' => ['aliases' => "Timothy Ben Boydston\nTimBen Boydston", 'occupation' => 'City councilman'],
];
foreach ($NEW_PEOPLE as $t => $p) {
    $exists = Entry::find()->section('persons')->status(null)->title($t)->one();
    if ($exists) { continue; }
    foreach ([$t, ...explode("\n", $p['aliases'])] as $n) { foreach ($keysOf($n) as $k) { $index[$k]['new:' . $t] = 'new:' . $t; } }
}
$match = function (string $name) use ($keysOf, $index) {
    $hits = [];
    foreach ($keysOf($name) as $k) { foreach ($index[$k] ?? [] as $id => $p) { $hits[$id] = $p; } }
    return count($hits) === 1 ? reset($hits) : (count($hits) > 1 ? 'AMBIGUOUS' : null);
};

/* ------------------------------------------------ elections, candidacies, faults */
$RES_CLAUSE = 'Resolution No. 12-9, Section 4: "Bob Kellar was elected as member of the City Council for the full term of four years; and TimBen Boydston was elected as member of the City Council for the full term of four years."';
echo PHP_EOL . 'ELECTIONS' . PHP_EOL;
$planE = []; $nCand = 0; $linked = []; $ambig = []; $sameSurname = [];
$surnames = [];
foreach ($index as $k => $v) { $surnames[explode(' ', $k)[1] ?? ''] = true; }
foreach ($data['elections'] as $e) {
    $existing = Entry::find()->section('elections')->status(null)->electionDateEdtf($e['date'])->one();
    $cands = [];
    foreach ($e['candidates'] as $c) {
        $p = !empty($c['surnameOnly']) ? null : $match($c['name']);
        if ($p === 'AMBIGUOUS') { $ambig[] = $c['name'] . ' (' . $e['date'] . ')'; $p = null; }
        if ($p) { $linked[] = $c['name'] . ' -> ' . (is_string($p) ? $p : '#' . $p->id . ' ' . $p->title); }
        elseif (empty($c['surnameOnly'])) { $sur = strtolower(preg_replace('~[^A-Za-z-]~', '', \craft\helpers\StringHelper::toAscii(explode(' ', trim(preg_replace('~,.*$~', '', $c['name'])))[count(explode(' ', trim(preg_replace('~,.*$~', '', $c['name'])))) - 1]))); if (isset($surnames[$sur])) { $sameSurname[] = $c['name'] . ' (' . $e['date'] . ')'; } }
        if ($e['date'] === '2012-04-10') { $out = in_array($c['name'], ['Bob Kellar', 'TimBen Boydston'], true) ? 'elected' : 'not-elected'; $ev = 'certified'; }
        else { $out = $c['topN'] ? 'elected' : 'not-elected'; $ev = $e['date'] === '1987-11-03' ? 'retrospective' : 'uncited'; }
        $cands[] = $c + ['person' => $p, 'outcome' => $out, 'evidence' => $ev];
    }
    $nCand += count($cands);
    $winners = array_map(fn($c) => $c['name'], array_filter($cands, fn($c) => $c['outcome'] === 'elected'));
    $turn = !empty($e['registeredValue']) ? sprintf('%.1f%%', $e['ballotsValue'] / $e['registeredValue'] * 100) : '';
    echo '   ' . ($existing ? '#' . $existing->id . ' ' : 'create ') . str_pad($e['printed'], 18) . str_pad(count($cands) . ' candidates', 15) . 'turnout ' . str_pad($turn ?: 'n/a', 7) . 'elected (' . $e['seatsEvidence'] . '): ' . implode(', ', $winners) . PHP_EOL;
    $planE[] = [$e, $cands, $existing];
}
echo PHP_EOL . 'SOURCE FAULTS (' . count($data['faults']) . ')' . PHP_EOL;
foreach ($data['faults'] as $f) { echo '   ' . $f['date'] . ' ' . ($f['name'] ?? $f['measure'] ?? $f['field']) . ': printed "' . $f['asPrinted'] . '", read ' . $f['reading'] . PHP_EOL; }
echo PHP_EOL . 'CANDIDATES LINKED TO PERSONS (' . count($linked) . '):' . PHP_EOL . '   ' . implode(PHP_EOL . '   ', $linked) . PHP_EOL;
echo 'NOT LINKED, a person of the same surname exists (' . count($sameSurname) . '), for Nathan to confirm by adding the printed name as an alias: ' . implode('; ', $sameSurname) . PHP_EOL;
if ($ambig) { echo 'AMBIGUOUS, not linked: ' . implode('; ', $ambig) . PHP_EOL; }
echo PHP_EOL . 'NEW PERSONS: ' . implode(', ', array_keys(array_filter($NEW_PEOPLE, fn($p, $t) => !Entry::find()->section('persons')->status(null)->title($t)->exists(), ARRAY_FILTER_USE_BOTH))) . PHP_EOL;
echo 'OFFICE HOLDINGS: Bob Kellar and TimBen Boydston, City Council Member, April 2012 to April 2016, certified (Resolution 12-9); the four Smyth starts corrected from certified to retrospective.' . PHP_EOL;
$elType = $svc->getEntryTypeByHandle('Election');
if (!$elType->titleFormat) { echo 'SCHEMA: Election type titleFormat -> "{electionDate} election"' . PHP_EOL; }
echo PHP_EOL . 'SUMMARY: ' . count($docPlan) . ' documents, ' . count(array_filter($planE, fn($x) => !$x[2])) . ' elections, ' . $nCand . ' candidacies, ' . count($data['faults']) . ' faults to write.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* ------------------------------------------------ apply */
if (!$elType->titleFormat) { $elType->titleFormat = '{electionDate} election'; if (!$svc->saveEntryType($elType)) { throw new \RuntimeException('Election titleFormat: ' . json_encode($elType->getErrors())); } }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $assets = Craft::$app->getAssets();
$folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => 'elections/']);
if (!$folder) { $root0 = $assets->getRootFolderByVolumeId($volume->id); $assets->createFolder(new \craft\models\VolumeFolder(['parentId' => $root0->id, 'name' => 'elections', 'volumeId' => $volume->id, 'path' => 'elections/'])); $folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => 'elections/']); }
$docType = $svc->getEntryTypeByHandle('document'); $docSec = $svc->getSectionByHandle('documents');
foreach ($docPlan as $file => [$m, $meta, $path]) {
    $tmp = sys_get_temp_dir() . '/' . $file; copy($path, $tmp);
    $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($file); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
    if (!$elements->saveElement($a)) { throw new \RuntimeException("document file $file: " . json_encode($a->getFirstErrors())); }
    $d = new Entry(); $d->sectionId = $docSec->id; $d->setTypeId($docType->id); $d->title = $meta['title'];
    $d->setFieldValues(['sourcePath' => $m['url'], 'sourceLine' => $meta['by'], 'originalPublishDate' => $meta['date'], 'originalPublishDateEdtf' => $meta['edtf'],
        'documentFiles' => [$a->id], 'publishedBy' => $meta['city'] ? [$CITY] : [],
        'recordProvenance' => 'import_elections.php, 29 September 2026: fetched from ' . $m['url'] . ' on 25 September 2026, SHA-256 ' . $m['sha256']]);
    if (!$elements->saveElement($d)) { throw new \RuntimeException("document $file: " . json_encode($d->getFirstErrors())); }
    $docs[$file] = $d;
}
$people = [];
foreach ($NEW_PEOPLE as $t => $p) {
    $e = Entry::find()->section('persons')->status(null)->title($t)->one();
    if (!$e) {
        $e = new Entry(); $e->sectionId = $svc->getSectionByHandle('persons')->id; $e->setTypeId($svc->getEntryTypeByHandle('person')->id); $e->title = $t;
        $e->setFieldValues(['fullName' => $t, 'personAliases' => $p['aliases'], 'occupation' => $p['occupation'], 'roles' => [$COUNCIL],
            'recordProvenance' => 'import_elections.php, 29 September 2026: a council candidate in several elections, elected in 2012 by Resolution 12-9']);
        if (!$elements->saveElement($e)) { throw new \RuntimeException("person $t: " . json_encode($e->getFirstErrors())); }
    }
    $people['new:' . $t] = $e;
}
$faultTargets = [];
$elSec = $svc->getSectionByHandle('elections'); $caSec = $svc->getSectionByHandle('candidacies'); $caType = $svc->getEntryTypeByHandle('Candidacy');
foreach ($planE as [$e, $cands, $el]) {
    $doc = $docByKey($e['doc']);
    $srcDocs = array_values(array_filter([$doc?->id, $e['date'] === '2012-04-10' ? ($docs['resolutionNo129-6.pdf']?->id ?? null) : null]));
    $notes = [($doc ? $doc->title : $e['doc']) . ($e['reading'] ?? '' ? ': ' . $e['reading'] : '') . '.'];
    if ($e['seatsEvidence'] === 'uncited') { $notes[] = 'Seats: ' . $e['seats'] . '. No document here states the number. It is derived from the council\'s four-year staggered terms, two and three seats in alternate elections, anchored on the two seats Resolution 12-9 certifies for 2012; the winners are read from the vote order and are uncited until the declaring resolution is obtained.'; }
    if ($e['date'] === '2012-04-10') { $notes[] = $RES_CLAUSE; }
    if (!$el) { $el = new Entry(); $el->sectionId = $elSec->id; $el->setTypeId($elType->id); }
    $el->setFieldValues(['electionDate' => $e['printed'], 'electionDateEdtf' => $e['date'], 'electionKind' => 'general', 'consolidatedWith' => $e['consolidatedWith'] . ($e['district'] ?? '' ? ($e['consolidatedWith'] ? '; ' : '') . 'Council ' . $e['district'] . ' only' : ''),
        'registeredVoters' => $e['registeredValue'] ?? null, 'ballotsCast' => $e['ballotsValue'] ?? null, 'votesByMail' => $e['absenteeValue'] ?? null, 'votesAtPrecinct' => $e['precinctValue'] ?? null,
        'seatsUp' => $e['seats'], 'seatsUpEvidence' => $e['seatsEvidence'], 'sourceDocuments' => $srcDocs,
        'ballotMeasures' => array_map(fn($m) => ['letter' => $m['letter'], 'subject' => $m['subject'], 'yes' => $m['yesValue'], 'no' => $m['noValue'], 'carried' => false], $e['measures']),
        'footnotes' => $fn($notes), 'recordProvenance' => 'import_elections.php, 29 September 2026']);
    if (!$elements->saveElement($el)) { throw new \RuntimeException('election ' . $e['date'] . ': ' . json_encode($el->getFirstErrors())); }
    $faultTargets['election:' . $e['date']] = $el;
    foreach ($cands as $c) {
        $ca = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el, 'field' => 'candidacyElection'])->nameAsPrinted($c['name'])->one();
        if (!$ca) { $ca = new Entry(); $ca->sectionId = $caSec->id; $ca->setTypeId($caType->id); }
        $pid = $c['person'] ? (is_string($c['person']) ? $people[$c['person']]->id : $c['person']->id) : null;
        $ca->setFieldValues(['candidacyElection' => [$el->id], 'candidacyPerson' => $pid ? [$pid] : [], 'nameAsPrinted' => $c['name'], 'votesAsPrinted' => $c['votes'], 'votes' => $c['value'],
            'outcome' => $c['outcome'], 'outcomeEvidence' => $c['evidence'], 'recordProvenance' => 'import_elections.php, 29 September 2026']);
        if (!$elements->saveElement($ca)) { throw new \RuntimeException('candidacy ' . $c['name'] . ': ' . json_encode($ca->getFirstErrors())); }
        $faultTargets['candidacy:' . $e['date'] . ':' . $c['name']] = $ca;
    }
}
$fSec = $svc->getSectionByHandle('sourceFaults'); $fType = $svc->getEntryTypeByHandle('SourceFault');
foreach ($data['faults'] as $f) {
    $target = $f['record'] === 'candidacy' ? $faultTargets['candidacy:' . $f['date'] . ':' . $f['name']] : $faultTargets['election:' . $f['date']];
    $field = $f['record'] === 'measure' ? 'ballotMeasures (' . $f['measure'] . ', ' . $f['field'] . ')' : ($f['record'] === 'election' ? ['ballots' => 'ballotsCast', 'registered' => 'registeredVoters', 'absentee' => 'votesByMail', 'precinct' => 'votesAtPrecinct'][$f['field']] : $f['field']);
    $have = Entry::find()->section('sourceFaults')->status(null)->relatedTo(['targetElement' => $target, 'field' => 'faultRecord'])->asPrinted($f['asPrinted'])->exists();
    if ($have) { continue; }
    $s = new Entry(); $s->sectionId = $fSec->id; $s->setTypeId($fType->id);
    $s->setFieldValues(['asPrinted' => $f['asPrinted'], 'reading' => $f['reading'], 'basis' => ucfirst($f['basis']) . '; the other figures in the same document use a comma or no separator, and the reading fits the totals beside it.',
        'decidedBy' => 'import_elections.php, 29 September 2026', 'faultRecord' => [$target->id], 'faultField' => $field]);
    if (!$elements->saveElement($s)) { throw new \RuntimeException('fault ' . $f['asPrinted'] . ': ' . json_encode($s->getFirstErrors())); }
}
/* The certified terms, and the Smyth starts corrected. */
$ohSec = $svc->getSectionByHandle('officeHoldings'); $ohType = $svc->getEntryTypeByHandle('officeHolding');
foreach (['new:Bob Kellar', 'new:TimBen Boydston'] as $k) {
    $p = $people[$k];
    if (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'holdingPerson'])->termStartEdtf('2012-04')->exists()) { continue; }
    $h = new Entry(); $h->sectionId = $ohSec->id; $h->setTypeId($ohType->id);
    $h->setFieldValues(['holdingPerson' => [$p->id], 'holdingOffice' => [$COUNCIL], 'holdingBody' => [$CITY], 'termStart' => 'April 2012', 'termStartEdtf' => '2012-04', 'termEnd' => 'April 2016', 'termEndEdtf' => '2016-04',
        'selectionMethod' => 'elected', 'howEnded' => 'expired', 'startEvidence' => 'certified', 'endEvidence' => 'certified',
        'footnotes' => $fn([$RES_CLAUSE . ' The resolution is document ' . ($docs['resolutionNo129-6.pdf']?->id ? '#' . $docs['resolutionNo129-6.pdf']->id : '') . '.']),
        'recordProvenance' => 'import_elections.php, 29 September 2026']);
    if (!$elements->saveElement($h)) { throw new \RuntimeException('holding ' . $k . ': ' . json_encode($h->getFirstErrors())); }
}
foreach (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => [16380, 15985], 'field' => 'holdingPerson'])->all() as $h) {
    if ((string)$h->startEvidence->value === 'certified' && str_contains((string)$h->recordProvenance, 'apply_review_fixes_0929')) { $h->setFieldValue('startEvidence', 'retrospective'); $elements->saveElement($h); }
}

/* Read back. */
$short = [];
$nE = (int)Entry::find()->section('elections')->status(null)->count(); $nC = (int)Entry::find()->section('candidacies')->status(null)->count(); $nF = (int)Entry::find()->section('sourceFaults')->status(null)->count();   /* count() returns a string: cast, or the read-back fails on equal numbers (it did, 29 September) */
if ($nE !== count($data['elections'])) { $short[] = "elections $nE, expected " . count($data['elections']); }
if ($nC !== $nCand) { $short[] = "candidacies $nC, expected $nCand"; }
if ($nF !== count($data['faults'])) { $short[] = "faults $nF, expected " . count($data['faults']); }
$e12 = Entry::find()->section('elections')->status(null)->electionDateEdtf('2012-04-10')->one();
$won = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e12, 'field' => 'candidacyElection'])->outcome('elected')->all();
if (array_map(fn($c) => (string)$c->nameAsPrinted, $won) != ['Bob Kellar', 'TimBen Boydston'] && count($won) !== 2) { $short[] = '2012 winners read back wrong'; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : "OK: $nE elections, $nC candidacies, $nF faults; documents, people and holdings") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('import_elections.php', $nE + $nC + $nF, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'council elections 1987-2024 from the nine City Clerk documents');
if ($short) { throw new \RuntimeException('import_elections: ' . implode('; ', $short)); }
