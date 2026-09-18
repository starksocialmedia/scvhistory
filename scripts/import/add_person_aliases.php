$fs = Craft::$app->getFields();
$f = $fs->getFieldByHandle('personAliases');
if ($f === null) {
    $f = new \craft\fields\PlainText();
    $f->name = 'Other names';
    $f->handle = 'personAliases';
    $f->instructions = 'Other spellings and forms this person appears under, one per line. Merged records add their titles here.';
    $f->multiline = true;
    $f->initialRows = 3;
    echo 'field: ' . ($fs->saveField($f) ? 'created' : 'FAILED ' . json_encode($f->getErrors())) . PHP_EOL;
    $f = $fs->getFieldByHandle('personAliases');
} else {
    echo 'field: exists' . PHP_EOL;
}
$svc = Craft::$app->getEntries();
$t = $svc->getEntryTypeByHandle('person');
$l = $t->getFieldLayout();
foreach ($l->getCustomFields() as $c) {
    if ($c->handle === 'personAliases') { echo 'layout: has it' . PHP_EOL; return; }
}
$tabs = $l->getTabs();
$els = $tabs[0]->getElements();
$els[] = new \craft\fieldlayoutelements\CustomField($f);
$tabs[0]->setElements($els);
$l->setTabs($tabs);
$t->setFieldLayout($l);
echo 'layout: ' . ($svc->saveEntryType($t) ? 'ADDED' : 'FAILED ' . json_encode($t->getErrors())) . PHP_EOL;
