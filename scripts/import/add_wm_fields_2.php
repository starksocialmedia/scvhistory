$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$defs = [
  'wmCasualtyReason' => 'Casualty Reason',
  'wmCasualtyType' => 'Casualty Type',
  'wmWallReference' => 'Vietnam Memorial Wall',
  'wmBirthplace' => 'Birthplace',
  'wmCasualtyDetail' => 'Casualty Detail',
  'wmGradeAtLoss' => 'Grade at Loss',
  'wmNotes' => 'Notes',
];
$made = [];
foreach ($defs as $h => $name) {
    $f = $fs->getFieldByHandle($h);
    if ($f === null) {
        $f = new \craft\fields\PlainText();
        $f->name = $name;
        $f->handle = $h;
        $f->multiline = in_array($h, ['wmCasualtyDetail','wmNotes'], true);
        $f->initialRows = 4;
        echo str_pad($h, 22) . ($fs->saveField($f) ? 'created' : 'FAILED') . PHP_EOL;
        $f = $fs->getFieldByHandle($h);
    } else { echo str_pad($h, 22) . 'exists' . PHP_EOL; }
    $made[$h] = $f;
}
$t = $fs->getFieldByHandle('wmServiceExtra');
if ($t === null) {
    $t = new \craft\fields\Table();
    $t->name = 'Other service record fields';
    $t->handle = 'wmServiceExtra';
    $t->instructions = 'Labels and values transcribed from the legacy page that have no field of their own. Kept exactly as printed.';
    $t->addRowLabel = 'Add a field';
    $t->columns = [
        'col1' => ['heading' => 'Label as printed', 'handle' => 'label', 'width' => '', 'type' => 'singleline'],
        'col2' => ['heading' => 'Value as printed', 'handle' => 'value', 'width' => '', 'type' => 'multiline'],
    ];
    echo str_pad('wmServiceExtra', 22) . ($fs->saveField($t) ? 'created' : 'FAILED') . PHP_EOL;
    $t = $fs->getFieldByHandle('wmServiceExtra');
} else { echo str_pad('wmServiceExtra', 22) . 'exists' . PHP_EOL; }
$made['wmServiceExtra'] = $t;

$type = $svc->getEntryTypeByHandle('warMemorial');
$l = $type->getFieldLayout();
$have = [];
foreach ($l->getCustomFields() as $c) { $have[] = $c->handle; }
$tabs = $l->getTabs();
$target = null;
foreach ($tabs as $tab) { if ($tab->name === 'Service Record') { $target = $tab; break; } }
if (!$target) { $target = $tabs[count($tabs) - 1]; }
$els = $target->getElements();
$added = [];
foreach ($made as $h => $f) {
    if (in_array($h, $have, true)) { continue; }
    $els[] = new \craft\fieldlayoutelements\CustomField($f);
    $added[] = $h;
}
if (!count($added)) { echo 'layout: nothing to add' . PHP_EOL; return; }
$target->setElements($els);
$l->setTabs($tabs);
$type->setFieldLayout($l);
echo 'layout: ' . ($svc->saveEntryType($type) ? 'added ' . implode(', ', $added) : 'FAILED') . PHP_EOL;
