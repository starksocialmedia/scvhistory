/**
 * districtPlan on officeHolding: the redistricting plan whose lines were in force for the term (Nathan,
 * 4 October 2026, approving the model in inventory/review/legislative-districts-2026-10-04.md: "the seat is
 * the Assembly district covering the Santa Clarita Valley, continuous; each term records the number in force
 * and the redistricting cycle that set it"). The number in force goes in seatLabel ("38th Assembly District");
 * the plan here. Used for the Assembly, the State Senate and the House; a seat whose lines are not redrawn
 * by these plans leaves it empty.
 * Writes project config (a new field, and the officeHolding layout). Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_district_plan_field_2026_10_04.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries();
$HANDLE = 'districtPlan'; $NAME = 'Redistricting plan';
$INSTR = 'The redistricting plan whose lines were in force for this term. The district number in force goes in Seat label.';
$OPTIONS = [
    ['label' => '', 'value' => '', 'default' => true],
    ['label' => '1991 lines (drawn by the court\'s Special Masters)', 'value' => '1991', 'default' => false],
    ['label' => '2001 lines (drawn by the Legislature)', 'value' => '2001', 'default' => false],
    ['label' => '2011 lines (drawn by the Citizens Redistricting Commission)', 'value' => '2011', 'default' => false],
    ['label' => '2021 lines (drawn by the Citizens Redistricting Commission)', 'value' => '2021', 'default' => false],
    ['label' => '2025 congressional lines (Proposition 50)', 'value' => '2025', 'default' => false],
];
$field = $fs->getFieldByHandle($HANDLE);
echo $HANDLE . ': ' . ($field ? 'exists' : 'create, Dropdown, "' . $NAME . '"') . PHP_EOL;
$type = $svc->getEntryTypeByHandle('officeHolding'); $layout = $type->getFieldLayout();
$present = array_map(fn($c) => $c->handle, $layout->getCustomFields());
echo 'officeHolding layout: ' . (in_array($HANDLE, $present, true) ? 'carries it' : 'add after seatLabel') . PHP_EOL;
if (!$APPLY) { return; }
if (!$field) {
    $field = new \craft\fields\Dropdown(); $field->name = $NAME; $field->handle = $HANDLE; $field->instructions = $INSTR; $field->options = $OPTIONS;
    if (!$fs->saveField($field)) { throw new \RuntimeException(json_encode($field->getFirstErrors())); }
    $field = $fs->getFieldByHandle($HANDLE);
}
if (!in_array($HANDLE, $present, true)) {
    $tabs = $layout->getTabs(); $ti = 0; $at = null;
    foreach ($tabs as $i => $tab) { foreach ($tab->getElements() as $j => $e) { if ($e instanceof \craft\fieldlayoutelements\CustomField && $e->getField()->handle === 'seatLabel') { $ti = $i; $at = $j + 1; } } }
    $els = $tabs[$ti]->getElements(); array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($field)]);
    $tabs[$ti]->setElements($els); $layout->setTabs($tabs); $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { throw new \RuntimeException(json_encode($type->getFirstErrors())); }
}
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_district_plan_field_2026_10_04.php', 2, 'verified', 'districtPlan on officeHolding');
echo 'done' . PHP_EOL;
