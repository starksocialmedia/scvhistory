$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$t = $svc->getEntryTypeByHandle('photograph');
$l = $t->getFieldLayout();
$have = [];
foreach ($l->getCustomFields() as $c) { $have[] = $c->handle; }
$want = ['bandImage', 'partOfCollection'];
$add = [];
foreach ($want as $h) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) { echo str_pad($h, 20) . 'FIELD DOES NOT EXIST' . PHP_EOL; continue; }
    if (in_array($h, $have, true)) { echo str_pad($h, 20) . 'already on photograph' . PHP_EOL; continue; }
    echo str_pad($h, 20) . 'would add' . PHP_EOL;
    $add[] = $f;
}
if (!count($add) || !$APPLY) { echo ($APPLY ? 'nothing to add' : 'DRY RUN') . PHP_EOL; return; }
$tabs = $l->getTabs();
$els = $tabs[0]->getElements();
foreach ($add as $f) { $els[] = new \craft\fieldlayoutelements\CustomField($f); }
$tabs[0]->setElements($els);
$l->setTabs($tabs);
$t->setFieldLayout($l);
echo 'layout: ' . ($svc->saveEntryType($t) ? 'saved' : 'FAILED') . PHP_EOL;
