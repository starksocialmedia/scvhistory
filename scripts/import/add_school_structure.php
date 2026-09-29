/**
 * Schools: the five Santa Clarita Valley public school districts, which grades
 * each teaches, which feeds which, and the schools the archive already holds
 * nested under their districts.
 *
 * WHAT THE VALLEY'S SCHOOLING LOOKS LIKE. Four elementary districts teach the
 * early grades and one high school district takes their pupils on:
 *
 *   Newhall School District              K-6   feeds the Hart district at grade 7
 *   Saugus Union School District         K-6   feeds the Hart district at grade 7
 *   Sulphur Springs Union School District K-6  feeds the Hart district at grade 7
 *   Castaic Union School District        K-8   feeds the Hart district at grade 9
 *   William S. Hart Union High School District  7-12
 *
 * The grade spans are read from the National Center for Education Statistics
 * Common Core of Data, 2022-23: each district's lowest and highest grade, and
 * for the Hart district each of its sixteen schools, all of which run 7-8,
 * 9-12 or 7-12. NCES gives the Hart district as a whole a span from
 * kindergarten, which none of its schools teaches; the district is recorded
 * 7-12 and the difference is footnoted, not hidden.
 *
 * THE SCHEMA. Two fields on the organization type:
 *   gradeSpan  PlainText, "K-6", "7-12", "9-12". On districts and schools.
 *   feedsInto  Entries (organizations): the district, or school, a body's
 *              pupils go on to. Held on the feeder, read from both ends.
 * The grade at which pupils move on is not stored: it is the feeder's top
 * grade plus one, and a second copy of it could only disagree.
 *
 * THE FEEDS WAIT FOR A SOURCE. That these four districts feed the Hart
 * district is common knowledge and follows from the spans and the map, but it
 * is a claim, and the archive holds claims to a source. Until $FEEDS_SOURCE
 * names one (an official statement, being looked for on 29 September), the
 * feedsInto relations are planned and HELD, and the dry run says so.
 *
 * NESTING. parentOrganization, which the organization page already reads,
 * nests William S. Hart High School (#16052) and Valencia High School
 * (#15837) under the Hart district, and Newhall Elementary School (#15958)
 * under the Newhall School District, per the NCES school directory. Felton
 * School (#18827), a school of 1929, is not nested: no source read says which
 * district it belonged to, and the districts of 1929 are not today's.
 *
 * IDENTIFIERS. ncesId and cdsCode fill only where empty. Newhall Elementary
 * already holds ncesId 062718009956, which matches no school in the NCES
 * directory for 2015 or 2022-23; the directory's Newhall Elementary is
 * 062718004095. It is reported and kept unless $FIX_NEWHALL_NCES is set.
 *
 * Titles are each district's name as it calls itself, the NCES and CDE forms
 * kept as aliases, since NCES writes the Hart district as "William S. Hart
 * Union High", the name of the high school too.
 *
 * Idempotent: districts are found by ncesId, then title. Fills empty fields
 * only. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_school_structure.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$FIX_NEWHALL_NCES = false;
$FEEDS_SOURCE = null;   /* the footnote text for the feeder claim, once an official source is read */
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$ccdD = fn(string $leaid) => 'National Center for Education Statistics, Common Core of Data, district directory 2022-23, NCES district ID ' . $leaid
    . ', https://nces.ed.gov/ccd/districtsearch/district_detail.asp?ID2=' . $leaid . ', read through the Urban Institute Education Data API, https://educationdata.urban.org/api/v1/school-districts/ccd/directory/2022/?leaid=' . $leaid . ', on 29 September 2026';
$ccdS = fn(string $id) => 'National Center for Education Statistics, Common Core of Data, school directory 2022-23, NCES school ID ' . $id
    . ', https://nces.ed.gov/ccd/schoolsearch/school_detail.asp?ID=' . $id . ', read through https://educationdata.urban.org/api/v1/schools/ccd/directory/2022/?ncessch=' . $id . ', on 29 September 2026';
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);

$HART_D = '0642510';
$DISTRICTS = [
    '0642510' => ['title' => 'William S. Hart Union High School District', 'cds' => '19651360000000', 'span' => '7-12',
        'aliases' => "Hart District\nWilliam S. Hart Union High\nWILLIAM S. HART UNION HIGH",
        'notes' => ['Grades: ' . $ccdD('0642510') . '. NCES gives the district as a whole a span from kindergarten (lowest grade 0) to 12; none of its sixteen schools in the same directory teaches below grade 7: seven junior high schools, 7-8; eight high schools, 9-12; one alternative school, 7-12. Recorded here as 7-12.']],
    '0627180' => ['title' => 'Newhall School District', 'cds' => '19648320000000', 'span' => 'K-6',
        'aliases' => "Newhall Elementary School District\nNEWHALL ELEMENTARY",
        'notes' => ['Grades: ' . $ccdD('0627180') . ': lowest grade kindergarten, highest 6; ten schools.']],
    '0635970' => ['title' => 'Saugus Union School District', 'cds' => '19649980000000', 'span' => 'K-6',
        'aliases' => "Saugus Union Elementary\nSAUGUS UNION ELEMENTARY",
        'notes' => ['Grades: ' . $ccdD('0635970') . ': lowest grade kindergarten, highest 6; fifteen schools.']],
    '0638220' => ['title' => 'Sulphur Springs Union School District', 'cds' => '19650450000000', 'span' => 'K-6',
        'aliases' => "Sulphur Springs Union Elementary\nSULPHUR SPRINGS UNION ELEMENTA",
        'notes' => ['Grades: ' . $ccdD('0638220') . ': lowest grade kindergarten, highest 6; ten schools.']],
    '0607740' => ['title' => 'Castaic Union School District', 'cds' => '19643450000000', 'span' => 'K-8',
        'aliases' => "Castaic Union\nCASTAIC UNION",
        'notes' => ['Grades: ' . $ccdD('0607740') . ': lowest grade kindergarten, highest 8; five schools.']],
];
/* existing school id => [district leaid, ncesId, cdsCode, gradeSpan] */
$SCHOOLS = [
    16052 => ['0642510', '064251006959', '19651361933902', '9-12', 'William S. Hart High School'],
    15837 => ['0642510', '064251003264', '19651361995802', '9-12', 'Valencia High School'],
    15958 => ['0627180', '062718004095', '19648326020796', 'K-6', 'Newhall Elementary School'],
];

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$elements = Craft::$app->getElements();
$type = $svc->getEntryTypeByHandle('organization');
$parentField = $fs->getFieldByHandle('parentOrganization');

/* ------------------------------------------------ schema */
echo 'SCHEMA' . PHP_EOL;
$newFields = [];
if (!$fs->getFieldByHandle('gradeSpan')) { $newFields['gradeSpan'] = 'create PlainText'; }
if (!$fs->getFieldByHandle('feedsInto')) { $newFields['feedsInto'] = 'create Entries (organizations), same sources as parentOrganization'; }
foreach ($newFields as $h => $w) { echo '   ' . str_pad($h, 12) . $w . PHP_EOL; }
$onLayout = array_map(fn($f) => $f->handle, $type->getFieldLayout()->getCustomFields());
$toLayout = array_values(array_diff(['gradeSpan', 'feedsInto'], $onLayout));
if ($toLayout) { echo '   layout      add ' . implode(', ', $toLayout) . ' to organization, after schoolLevel' . PHP_EOL; }
if (!$newFields && !$toLayout) { echo '   in place' . PHP_EOL; }
$schemaReady = !$newFields && !$toLayout;

/* ------------------------------------------------ districts */
echo PHP_EOL . 'DISTRICTS' . PHP_EOL;
$existing = [];
foreach ($DISTRICTS as $leaid => $d) {
    $e = Entry::find()->section('organizations')->status(null)->ncesId($leaid)->one() ?? Entry::find()->section('organizations')->status(null)->title($d['title'])->one();
    $existing[$leaid] = $e;
    echo '   ' . ($e ? '#' . $e->id . ' exists: ' : 'create ') . $d['title'] . '  school/district, grades ' . $d['span'] . ', NCES ' . $leaid . ', CDS ' . $d['cds'] . PHP_EOL;
    echo '      aliases: ' . str_replace("\n", ' / ', $d['aliases']) . PHP_EOL;
}

/* ------------------------------------------------ feeds */
echo PHP_EOL . 'FEEDS INTO' . PHP_EOL;
foreach ($DISTRICTS as $leaid => $d) {
    if ($leaid === $HART_D) { continue; }
    $at = $d['span'] === 'K-8' ? 9 : 7;
    echo '   ' . $d['title'] . ' (' . $d['span'] . ') -> ' . $DISTRICTS[$HART_D]['title'] . ', at grade ' . $at . ($FEEDS_SOURCE ? '' : '   HELD: no official source for the feed yet') . PHP_EOL;
}

/* ------------------------------------------------ the schools held */
echo PHP_EOL . 'SCHOOLS NESTED' . PHP_EOL;
$schoolPlan = [];
foreach ($SCHOOLS as $id => [$leaid, $nces, $cds, $span, $title]) {
    $s = Entry::find()->id($id)->status(null)->one();
    if (!$s || $s->title !== $title) { echo "   REFUSING: #$id is not $title" . PHP_EOL; continue; }
    $set = [];
    $parents = $s->parentOrganization->status(null)->ids();
    $line = "   #$id $title -> " . $DISTRICTS[$leaid]['title'];
    if ($parents && (!$existing[$leaid] || !in_array($existing[$leaid]->id, array_map('intval', $parents), true))) { $line .= '  (HAS ANOTHER PARENT #' . implode(',', $parents) . ', kept)'; }
    elseif (!$parents) { $set['parent'] = $leaid; }
    $curN = trim((string)$s->ncesId);
    if ($curN === '') { $set['ncesId'] = $nces; }
    elseif ($curN !== $nces) { $line .= PHP_EOL . "      ncesId holds $curN, which matches no school in the NCES directory; the directory's $title is $nces" . ($FIX_NEWHALL_NCES && $id === 15958 ? ': REPLACED' : ': kept, set $FIX_NEWHALL_NCES to replace'); if ($FIX_NEWHALL_NCES && $id === 15958) { $set['ncesId'] = $nces; } }
    if (trim((string)$s->cdsCode) === '') { $set['cdsCode'] = $cds; }
    $set['gradeSpan'] = $span;
    $set['footnoteSchool'] = $ccdS($nces) . ': grades ' . str_replace('K', 'kindergarten ', $span) . ', district NCES ' . $leaid . '.';
    echo $line . PHP_EOL . '      sets: ' . implode(', ', array_map(fn($k, $v) => $k === 'parent' ? 'parentOrganization' : ($k === 'footnoteSchool' ? 'a footnote, if footnotes are empty' : "$k $v"), array_keys($set), $set)) . PHP_EOL;
    $schoolPlan[$id] = $set;
}
echo PHP_EOL . 'NOT NESTED: #18827 Felton School (1929): no source says which district it belonged to.' . PHP_EOL;
echo 'NOTE: #16052 carries the alias "Hart Union High School", which reads as the district\'s name; kept, for Nathan.' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* ------------------------------------------------ apply: schema */
if (isset($newFields['gradeSpan'])) {
    $f = new \craft\fields\PlainText(['name' => 'Grades', 'handle' => 'gradeSpan', 'instructions' => 'The grades taught, as "K-6", "7-12", "9-12". From the NCES directory unless a footnote says otherwise.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('gradeSpan: ' . json_encode($f->getFirstErrors())); }
}
if (isset($newFields['feedsInto'])) {
    $f = new \craft\fields\Entries(['name' => 'Feeds into', 'handle' => 'feedsInto', 'sources' => $parentField->sources,
        'instructions' => 'The district or school this body\'s pupils go on to. Held on the feeder only; the page reads it from both ends. The grade of the move is the feeder\'s top grade plus one, and is not stored.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('feedsInto: ' . json_encode($f->getFirstErrors())); }
}
if ($toLayout) {
    $layout = $type->getFieldLayout();
    $tabs = $layout->getTabs(); $ti = 0; $at = null;
    foreach ($tabs as $i => $tab) { foreach (array_values($tab->getElements()) as $j => $el) { if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->attribute() === 'schoolLevel') { $ti = $i; $at = $j + 1; } } }
    $els = array_values($tabs[$ti]->getElements());
    foreach ($toLayout as $h) { array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h))]); if ($at !== null) { $at++; } }
    $tabs[$ti]->setElements($els); $layout->setTabs($tabs); $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { throw new \RuntimeException('layout: ' . json_encode($type->getErrors())); }
}

/* ------------------------------------------------ apply: districts */
$ids = [];
foreach ($DISTRICTS as $leaid => $d) {
    $e = $existing[$leaid];
    if (!$e) {
        $e = new Entry();
        $e->sectionId = $svc->getSectionByHandle('organizations')->id;
        $e->setTypeId($type->id);
        $e->title = $d['title'];
    }
    $vals = ['orgType' => 'school', 'schoolLevel' => 'district', 'ncesId' => $leaid, 'cdsCode' => $d['cds'], 'gradeSpan' => $d['span'], 'orgAliases' => $d['aliases'],
        'footnotes' => $fn($d['notes']), 'recordProvenance' => 'editorial-2026, add_school_structure.php, 29 September 2026, from the NCES Common Core of Data'];
    foreach ($vals as $h => $v) {
        $cur = $e->id ? $e->getFieldValue($h) : null;
        $empty = $cur === null || (is_object($cur) && property_exists($cur, 'value') ? (string)$cur->value === '' : (is_array($cur) ? !array_filter($cur, fn($r) => is_array($r) && array_filter($r)) : trim((string)$cur) === ''));
        if ($empty) { $e->setFieldValue($h, $v); }
    }
    if (!$elements->saveElement($e)) { throw new \RuntimeException('district ' . $d['title'] . ': ' . json_encode($e->getFirstErrors())); }
    $ids[$leaid] = $e->id;
}
if ($FEEDS_SOURCE) {
    foreach ($DISTRICTS as $leaid => $d) {
        if ($leaid === $HART_D) { continue; }
        $e = Entry::find()->id($ids[$leaid])->status(null)->one();
        $e->setFieldValue('feedsInto', array_values(array_unique(array_merge(array_map('intval', $e->feedsInto->status(null)->ids()), [$ids[$HART_D]]))));
        $notes = array_values(array_filter($e->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
        if (!array_filter($notes, fn($r) => str_contains((string)$r['note'], $FEEDS_SOURCE))) { $notes[] = ['number' => (string)(count($notes) + 1), 'note' => 'Feeds the Hart district: ' . $FEEDS_SOURCE, 'source' => 'editorial-2026']; }
        $e->setFieldValue('footnotes', $notes);
        if (!$elements->saveElement($e)) { throw new \RuntimeException('feed ' . $d['title']); }
    }
}

/* ------------------------------------------------ apply: schools */
foreach ($schoolPlan as $id => $set) {
    $s = Entry::find()->id($id)->status(null)->one();
    if (isset($set['parent'])) { $s->setFieldValue('parentOrganization', [$ids[$set['parent']]]); $s->setFieldValue('hasParentOrg', true); }
    foreach (['ncesId', 'cdsCode', 'gradeSpan'] as $h) { if (isset($set[$h]) && ($h !== 'gradeSpan' || trim((string)$s->getFieldValue($h)) === '')) { $s->setFieldValue($h, $set[$h]); } }
    $notes = array_values(array_filter($s->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (!$notes) { $s->setFieldValue('footnotes', $fn([$set['footnoteSchool']])); }
    if (!$elements->saveElement($s)) { throw new \RuntimeException("school #$id: " . json_encode($s->getFirstErrors())); }
}

/* ------------------------------------------------ read back */
$short = [];
foreach ($DISTRICTS as $leaid => $d) {
    $e = Entry::find()->id($ids[$leaid])->status(null)->one();
    if ((string)$e->schoolLevel->value !== 'district') { $short[] = $d['title'] . ' is not level district'; }
    if (trim((string)$e->gradeSpan) !== $d['span']) { $short[] = $d['title'] . ' grades read ' . $e->gradeSpan; }
    if ($FEEDS_SOURCE && $leaid !== $HART_D && !in_array($ids[$HART_D], array_map('intval', $e->feedsInto->status(null)->ids()), true)) { $short[] = $d['title'] . ' does not feed the Hart district'; }
}
foreach ($schoolPlan as $id => $set) {
    if (!isset($set['parent'])) { continue; }
    $s = Entry::find()->id($id)->status(null)->one();
    if (!in_array($ids[$set['parent']], array_map('intval', $s->parentOrganization->status(null)->ids()), true)) { $short[] = "#$id is not nested"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: 5 districts, ' . count($schoolPlan) . ' schools nested' . ($FEEDS_SOURCE ? ', 4 feeds' : ', feeds held')) . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_school_structure.php', 5 + count($schoolPlan), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'gradeSpan, feedsInto; five districts; schools nested' . ($FEEDS_SOURCE ? '; feeds' : '; feeds held'));
if ($short) { throw new \RuntimeException('add_school_structure: ' . implode('; ', $short)); }
