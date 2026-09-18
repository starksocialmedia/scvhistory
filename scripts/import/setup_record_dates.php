/**
 * Creates the recordDates table field and adds it to every entry type.
 * Columns: date text as printed, ISO date, granularity, label, and whether
 * the row is confirmed by a human.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/setup_record_dates.php'))"
 */

$APPLY = true;

$fieldsSvc = Craft::$app->getFields();
$entriesSvc = Craft::$app->getEntries();

$field = $fieldsSvc->getFieldByHandle('recordDates');
if (!$field) {
    $field = new \craft\fields\Table();
    $field->name = 'Dates in this record';
    $field->handle = 'recordDates';
    $field->instructions = 'Dates this record is about, for the on-this-day index. Enter the date as it is printed in the source, then the ISO date and how precise it is. Tick Confirmed once a person has checked the row.';
    $field->addRowLabel = 'Add a date';
    $field->minRows = null;
    $field->maxRows = null;
    $field->columns = [
        'col1' => ['heading' => 'As printed', 'handle' => 'printed', 'width' => '', 'type' => 'singleline'],
        'col2' => ['heading' => 'ISO date', 'handle' => 'iso', 'width' => '', 'type' => 'date'],
        'col3' => ['heading' => 'Precision', 'handle' => 'granularity', 'width' => '', 'type' => 'select', 'options' => [
            ['label' => 'Day', 'value' => 'day', 'default' => true],
            ['label' => 'Month', 'value' => 'month', 'default' => false],
            ['label' => 'Year', 'value' => 'year', 'default' => false],
            ['label' => 'Approximate', 'value' => 'circa', 'default' => false],
        ]],
        'col4' => ['heading' => 'What happened', 'handle' => 'label', 'width' => '', 'type' => 'singleline'],
        'col5' => ['heading' => 'Confirmed', 'handle' => 'confirmed', 'width' => '', 'type' => 'checkbox'],
    ];
    echo 'recordDates: ' . ($APPLY ? ($fieldsSvc->saveField($field) ? 'created' : 'FAILED ' . json_encode($field->getErrors())) : 'would create') . PHP_EOL;
    if (!$APPLY) { echo PHP_EOL . 'DRY RUN. Nothing written.' . PHP_EOL; return; }
    $field = $fieldsSvc->getFieldByHandle('recordDates');
} else {
    echo 'recordDates: exists' . PHP_EOL;
}

foreach ($entriesSvc->getAllEntryTypes() as $type) {
    $layout = $type->getFieldLayout();
    foreach ($layout->getCustomFields() as $f) {
        if ($f->handle === 'recordDates') { echo str_pad($type->handle, 18) . 'has it' . PHP_EOL; continue 2; }
    }
    if (!$APPLY) { echo str_pad($type->handle, 18) . 'would add' . PHP_EOL; continue; }
    $tabs = $layout->getTabs();
    $target = null;
    foreach ($tabs as $t) { if ($t->name === 'Media') { $target = $t; break; } }
    if (!$target) { $target = $tabs[0]; }
    $els = $target->getElements();
    $els[] = new \craft\fieldlayoutelements\CustomField($field);
    $target->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo str_pad($type->handle, 18) . ($entriesSvc->saveEntryType($type) ? 'ADDED' : 'FAILED ' . json_encode($type->getErrors())) . PHP_EOL;
}
echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
