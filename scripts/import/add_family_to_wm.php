$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$t = $svc->getEntryTypeByHandle('warMemorial');
$l = $t->getFieldLayout();
$have = [];
foreach ($l->getCustomFields() as $c) { $have[] = $c->handle; }
$want = ['childOf', 'siblingOf', 'spouseOf'];
$add = [];
foreach ($want as $h) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) { echo str_pad($h, 12) . 'FIELD MISSING' . PHP_EOL; continue; }
    if (in_array($h, $have, true)) { echo str_pad($h, 12) . 'already on type' . PHP_EOL; continue; }
    echo str_pad($h, 12) . 'would add' . PHP_EOL;
    $add[] = $f;
}
if (!count($add) || !$APPLY) { echo ($APPLY ? 'nothing to add' : 'DRY RUN') . PHP_EOL; return; }
$tabs = $l->getTabs();
$target = null;
foreach ($tabs as $tab) { if ($tab->name === 'Connections' || $tab->name === 'Family') { $target = $tab; break; } }
if (!$target) { $target = $tabs[0]; }
$els = $target->getElements();
foreach ($add as $f) { $els[] = new \craft\fieldlayoutelements\CustomField($f); }
$target->setElements($els);
$l->setTabs($tabs);
$t->setFieldLayout($l);
echo 'layout: ' . ($svc->saveEntryType($t) ? 'saved to ' . $target->name : 'FAILED') . PHP_EOL;
