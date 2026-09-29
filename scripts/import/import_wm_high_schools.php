/**
 * The first alumni: the high schools the war memorial records already name.
 *
 * Nathan, 29 September 2026: start the education relation with the free-text
 * high school on the war memorial records (wmHighSchool), because it is real
 * data already held. Twelve casualties carry one; nine name a valley school:
 *
 *   William S. Hart High School   4   (#16052)
 *   Valencia High School          1   (#15837)
 *   Saugus High School            1   new record
 *   Canyon High School            1   new record
 *   West Ranch High School        1   new record
 *   Santa Clarita Christian School 1  new record
 *
 * The other three (San Fernando; Maricopa High School, Maricopa; John F.
 * Kennedy High, Granada Hills) are schools outside the valley and are
 * reported, not linked: the archive holds valley schools.
 *
 * The four schools are created as organizations, school, high: the three
 * public ones with their NCES and CDS codes from the NCES directory, grades
 * 9-12, nested under the William S. Hart Union High School District; Santa
 * Clarita Christian School is private, not in the public directory, and is
 * created with its name alone, no level, no parent, nothing guessed.
 *
 * Each education record cites the war memorial record it comes from, as
 * retrospective evidence: the memorial pages are compiled after the death.
 * "Class of 1951" fills classOf, not the outcome: a class names a year, not a
 * diploma. The outcome is "graduated" only where a source says so (Rudy
 * Acosta's record: "graduated from Santa Clarita Christian School in 2009"),
 * and "attended" otherwise.
 *
 * Needs add_education.php applied. Idempotent: a school is found by NCES code
 * or title, an education record by person and school. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_wm_high_schools.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$ccdS = fn(string $id) => 'National Center for Education Statistics, Common Core of Data, school directory 2022-23, NCES school ID ' . $id . ', https://nces.ed.gov/ccd/schoolsearch/school_detail.asp?ID=' . $id;
$NEW_SCHOOLS = [
    'saugus'   => ['title' => 'Saugus High School', 'aliases' => "Saugus High", 'nces' => '064251006961', 'cds' => '19651361931740', 'span' => '9-12', 'level' => 'high', 'hart' => true],
    'canyon'   => ['title' => 'Canyon High School', 'aliases' => "Canyon High", 'nces' => '064251006958', 'cds' => '19651361931492', 'span' => '9-12', 'level' => 'high', 'hart' => true],
    'westranch'=> ['title' => 'West Ranch High School', 'aliases' => "West Ranch High", 'nces' => '064251010914', 'cds' => '19651360102475', 'span' => '9-12', 'level' => 'high', 'hart' => true],
    'sccs'     => ['title' => 'Santa Clarita Christian School', 'aliases' => '', 'nces' => '', 'cds' => '', 'span' => '', 'level' => '', 'hart' => false],
];
/* wmHighSchool as printed => [school: an id or a NEW_SCHOOLS key, classOf] */
$MAP = [
    'Wm. S. Hart High'                     => [16052, ''],
    'Wm. S. Hart High, Class of 1951'      => [16052, '1951'],
    'Wm. S. Hart High School'              => [16052, ''],
    'Wm. S. Hart High, Class of 2002'      => [16052, '2002'],
    'Valencia High, Class of 2003'         => [15837, '2003'],
    'Saugus High School'                   => ['saugus', ''],
    'Canyon High School (Canyon Country)'  => ['canyon', ''],
    'West Ranch High School (Valencia)'    => ['westranch', ''],
    'Santa Clarita Christian School'       => ['sccs', ''],
];
/* Where a source says the person graduated: record id => [classOf, the words] */
$GRADUATED = [526 => ['2009', 'graduated from Santa Clarita Christian School in 2009']];

$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$schema = (bool)$svc->getSectionByHandle('educations');
if (!$schema) { echo 'NOTE: the educations section does not exist yet. Apply add_education.php first; this is the plan for after it.' . PHP_EOL . PHP_EOL; }
$hart = Entry::find()->section('organizations')->status(null)->ncesId('0642510')->one();
if (!$hart) { echo 'REFUSING: the Hart district record is missing' . PHP_EOL; return; }

echo 'SCHOOLS' . PHP_EOL;
$school = [];
foreach ($NEW_SCHOOLS as $k => $s) {
    $e = ($s['nces'] ? Entry::find()->section('organizations')->status(null)->ncesId($s['nces'])->one() : null) ?? Entry::find()->section('organizations')->status(null)->title($s['title'])->one();
    $school[$k] = $e;
    echo '   ' . ($e ? '#' . $e->id . ' exists: ' : 'create ') . $s['title'] . ($s['nces'] ? ', NCES ' . $s['nces'] . ', grades ' . $s['span'] . ', under ' . $hart->title : ', private: name only') . PHP_EOL;
}

echo PHP_EOL . 'EDUCATION RECORDS' . PHP_EOL;
$plan = []; $outside = [];
foreach (Entry::find()->section('warMemorials')->status(null)->all() as $w) {
    $v = trim((string)$w->wmHighSchool);
    if ($v === '') { continue; }
    if (!isset($MAP[$v])) { $outside[] = "#{$w->id} {$w->title}: \"$v\""; continue; }
    [$sk, $class] = $MAP[$v];
    $grad = $GRADUATED[$w->id] ?? null;
    if ($grad) {
        if (!str_contains((string)$w->body, $grad[1])) { echo "   REFUSING #{$w->id}: the words \"{$grad[1]}\" are not in its record" . PHP_EOL; continue; }
        $class = $class ?: $grad[0];
    }
    $sTitle = is_int($sk) ? Entry::find()->id($sk)->status(null)->one()->title : $NEW_SCHOOLS[$sk]['title'];
    $plan[] = ['w' => $w, 'school' => $sk, 'class' => $class, 'outcome' => $grad ? 'graduated' : 'attended', 'printed' => $v, 'grad' => $grad];
    echo '   #' . str_pad($w->id, 4) . str_pad($w->title, 28) . '-> ' . str_pad($sTitle, 32) . ($class ? 'class of ' . $class . ', ' : '') . ($grad ? 'graduated' : 'attended') . PHP_EOL;
}
echo PHP_EOL . 'OUTSIDE THE VALLEY, not linked (' . count($outside) . '):' . PHP_EOL . implode(PHP_EOL, array_map(fn($x) => '   ' . $x, $outside)) . PHP_EOL;
echo PHP_EOL . 'SUMMARY: ' . count(array_filter($school, fn($e) => !$e)) . ' schools to create, ' . count($plan) . ' education records.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$schema) { echo 'REFUSING: apply add_education.php first' . PHP_EOL; return; }

$orgSec = $svc->getSectionByHandle('organizations'); $orgType = $svc->getEntryTypeByHandle('organization');
foreach ($NEW_SCHOOLS as $k => $s) {
    if ($school[$k]) { continue; }
    $e = new Entry(); $e->sectionId = $orgSec->id; $e->setTypeId($orgType->id); $e->title = $s['title'];
    $vals = ['orgType' => 'school', 'recordProvenance' => 'import_wm_high_schools.php, 29 September 2026: named by a war memorial record' . ($s['nces'] ? '; codes from the NCES directory' : '')];
    if ($s['aliases']) { $vals['orgAliases'] = $s['aliases']; }
    if ($s['nces']) {
        $vals += ['schoolLevel' => $s['level'], 'ncesId' => $s['nces'], 'cdsCode' => $s['cds'], 'gradeSpan' => $s['span'],
            'parentOrganization' => [$hart->id], 'hasParentOrg' => true,
            'footnotes' => [['number' => '1', 'source' => 'editorial-2026', 'note' => $ccdS($s['nces']) . ': grades ' . $s['span'] . ', William S. Hart Union High School District.']]];
    }
    $e->setFieldValues($vals);
    if (!$elements->saveElement($e)) { throw new \RuntimeException('import_wm_high_schools: school ' . $s['title'] . ' ' . json_encode($e->getFirstErrors())); }
    $school[$k] = $e;
}
$eduSec = $svc->getSectionByHandle('educations'); $eduType = $svc->getEntryTypeByHandle('education');
$short = [];
foreach ($plan as $p) {
    $sid = is_int($p['school']) ? $p['school'] : $school[$p['school']]->id;
    $have = Entry::find()->section('educations')->status(null)->relatedTo(['and', ['targetElement' => $p['w'], 'field' => 'educationPerson'], ['targetElement' => $sid, 'field' => 'educationSchool']])->one();
    if ($have) { continue; }
    $e = new Entry(); $e->sectionId = $eduSec->id; $e->setTypeId($eduType->id);
    $note = 'War memorial record #' . $p['w']->id . ', ' . $p['w']->title . ', ' . $p['w']->url . ': high school "' . $p['printed'] . '".' . ($p['grad'] ? ' The same record: "' . $p['grad'][1] . '."' : '');
    $e->setFieldValues(['educationPerson' => [$p['w']->id], 'educationSchool' => [$sid], 'classOf' => $p['class'],
        'educationOutcome' => $p['outcome'], 'educationEvidence' => 'retrospective',
        'footnotes' => [['number' => '1', 'source' => 'editorial-2026', 'note' => $note]],
        'recordProvenance' => 'import_wm_high_schools.php, 29 September 2026, from wmHighSchool']);
    if (!$elements->saveElement($e)) { $short[] = '#' . $p['w']->id . ' ' . json_encode($e->getFirstErrors()); continue; }
    $b = Entry::find()->id($e->id)->status(null)->one();
    if (($b->educationSchool->one()->id ?? null) !== $sid || ($b->educationPerson->one()->id ?? null) !== $p['w']->id) { $short[] = '#' . $p['w']->id . ' did not read back'; }
}
$n = Entry::find()->section('educations')->status(null)->count();
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : "OK: $n education records") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_wm_high_schools.php', count($plan), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'nine casualties linked to their valley high schools; four schools created');
if ($short) { throw new \RuntimeException('import_wm_high_schools: ' . implode('; ', $short)); }
