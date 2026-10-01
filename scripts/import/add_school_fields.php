/**
 * What a reader expects of a school, in two kinds of claim (Nathan, 1 October
 * 2026: "your line between fact and branding is right").
 *
 *   FACTS, on the evidence scale
 *     enrolment        Table: one row per school year. The count, the year as
 *                      the school year ("2022-23") and as its fall ("2022"),
 *                      and the source. Filled from the NCES Common Core of Data
 *                      (import_nces_enrolment.php). A figure is always for a year.
 *     foundedEvidence  Dropdown, the evidence scale, for dateFounded, which
 *                      exists. Not filled from NCES: the Common Core begins in
 *                      1986, so the first year a school appears there is not the
 *                      year it opened (Hart High School appears in 1986 and
 *                      opened decades earlier).
 *   BRANDING, attributed, not graded
 *     schoolIdentity   Table: what the school says it is. kind (mascot, colours,
 *                      motto, nickname), value, from and to (EDTF, because they
 *                      change and the change is history), statedBy (the school,
 *                      the district, a yearbook, the CIF), source, readOn. Shown
 *                      as the school's own description, with its dates, never as
 *                      a finding.
 *   ALREADY THERE   orgWikipediaUrl, a link emitted as sameAs and never a footnote.
 *
 * Added to the organization type's layout (schools are organizations). Its own
 * script, before the import that fills it. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_school_fields.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to project config' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries();
$hart = Entry::find()->id(16052)->status(null)->one();
if (!$hart) { echo 'REFUSING: no #16052' . PHP_EOL; return; }
$type = $hart->getType();
$EVIDENCE = ['certified' => "Certified: the body's own record", 'contemporary' => 'Contemporary report', 'retrospective' => 'Retrospective account', 'roster' => 'Undated roster', 'derived' => 'Derived: read from the count', 'uncited' => 'Uncited'];
$NEW = [
    'enrolment' => ['Enrolment', 'table', 'Pupils enrolled, one row per school year, with the source. From the NCES Common Core of Data unless the source says otherwise.', [
        'col1' => ['heading' => 'School year', 'handle' => 'schoolYear', 'width' => '', 'type' => 'singleline'],
        'col2' => ['heading' => 'Fall of', 'handle' => 'fallYear', 'width' => '', 'type' => 'number'],
        'col3' => ['heading' => 'Enrolled', 'handle' => 'count', 'width' => '', 'type' => 'number'],
        'col4' => ['heading' => 'Source', 'handle' => 'source', 'width' => '', 'type' => 'singleline'],
    ], 'Add a year'],
    'schoolIdentity' => ['As the school describes itself', 'table', 'Branding, attributed rather than graded: mascot, colours, motto, nickname, as the school (or whoever is named) states it, with the years it applied and where it was read.', [
        'col1' => ['heading' => 'Kind', 'handle' => 'kind', 'width' => '', 'type' => 'singleline'],
        'col2' => ['heading' => 'Value', 'handle' => 'value', 'width' => '', 'type' => 'singleline'],
        'col3' => ['heading' => 'From (EDTF)', 'handle' => 'from', 'width' => '', 'type' => 'singleline'],
        'col4' => ['heading' => 'To (EDTF)', 'handle' => 'to', 'width' => '', 'type' => 'singleline'],
        'col5' => ['heading' => 'Stated by', 'handle' => 'statedBy', 'width' => '', 'type' => 'singleline'],
        'col6' => ['heading' => 'Source', 'handle' => 'source', 'width' => '', 'type' => 'singleline'],
        'col7' => ['heading' => 'Read on', 'handle' => 'readOn', 'width' => '', 'type' => 'singleline'],
    ], 'Add a description'],
    'foundedEvidence' => ['Evidence for the founding date', 'dropdown', 'How the founding date is known, on the archive\'s evidence scale.', $EVIDENCE, ''],
];
$inLayout = array_map(fn($f) => $f->handle, $type->getFieldLayout()->getCustomFields());
$todo = 0;
foreach ($NEW as $h => [$label, $kind]) {
    $f = $fs->getFieldByHandle($h);
    echo str_pad($h, 17) . ($f ? 'exists' : "create ($kind)") . (in_array($h, $inLayout, true) ? ", in the {$type->name} layout" : ", add to the {$type->name} layout") . PHP_EOL;
    if (!$f || !in_array($h, $inLayout, true)) { $todo++; }
}
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . "nothing was written ($todo changes). Set \$APPLY = true to apply." . PHP_EOL; return; }

foreach ($NEW as $h => [$label, $kind, $instr, $cfg, $addRow]) {
    if ($fs->getFieldByHandle($h)) { continue; }
    if ($kind === 'table') { $f = new \craft\fields\Table(); $f->columns = $cfg; $f->defaults = []; $f->addRowLabel = $addRow; }
    else { $f = new \craft\fields\Dropdown(); $f->options = array_map(fn($v, $l) => ['label' => $l, 'value' => $v, 'default' => false], array_keys($cfg), array_values($cfg)); }
    $f->name = $label; $f->handle = $h; $f->instructions = $instr;
    if (!$fs->saveField($f)) { throw new \RuntimeException("field $h: " . json_encode($f->getErrors())); }
}
$fs->refreshFields();
$type = $svc->getEntryTypeById($type->id);
$layout = $type->getFieldLayout(); $tabs = $layout->getTabs(); $els = $tabs[0]->getElements();
foreach (array_keys($NEW) as $h) { if (!in_array($h, array_map(fn($f) => $f->handle, $layout->getCustomFields()), true)) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); } }
$tabs[0]->setElements($els); $layout->setTabs($tabs); $type->setFieldLayout($layout);
if (!$svc->saveEntryType($type)) { throw new \RuntimeException('entry type: ' . json_encode($type->getErrors())); }
$fs->refreshFields();
$have = array_map(fn($f) => $f->handle, $svc->getEntryTypeById($type->id)->getFieldLayout()->getCustomFields());
$short = array_values(array_diff(array_keys($NEW), $have));
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_school_fields.php', $todo, $short ? 'SHORT: ' . implode(', ', $short) : 'verified', 'enrolment, schoolIdentity, foundedEvidence');
if ($short) { throw new \RuntimeException('add_school_fields: ' . implode(', ', $short)); }
