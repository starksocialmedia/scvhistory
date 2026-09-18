$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$f = $fs->getFieldByHandle('bandImage');
if ($f === null) {
    $vol = Craft::$app->getVolumes()->getAllVolumes()[0] ?? null;
    if (!$vol) { echo 'ERROR: no volume' . PHP_EOL; return; }
    $f = new \craft\fields\Assets();
    $f->name = 'Band image';
    $f->handle = 'bandImage';
    $f->instructions = 'Background image for the page band. Use a photograph or artwork with no lettering, composed so the subject sits at the right. The featured image, which may carry a title or cover art, is used for cards and tiles instead.';
    $f->sources = ['volume:' . $vol->uid];
    $f->defaultUploadLocationSource = 'volume:' . $vol->uid;
    $f->restrictFiles = true;
    $f->allowedKinds = ['image'];
    $f->maxRelations = 1;
    $f->viewMode = 'large';
    echo 'field: ' . ($fs->saveField($f) ? 'created' : 'FAILED ' . json_encode($f->getErrors())) . PHP_EOL;
    $f = $fs->getFieldByHandle('bandImage');
} else { echo 'field: exists' . PHP_EOL; }
foreach (['collection', 'article', 'person', 'place', 'organization', 'event', 'group', 'warMemorial'] as $h) {
    $t = $svc->getEntryTypeByHandle($h);
    if (!$t) { continue; }
    $l = $t->getFieldLayout();
    foreach ($l->getCustomFields() as $c) { if ($c->handle === 'bandImage') { echo str_pad($h, 16) . 'has it' . PHP_EOL; continue 2; } }
    $tabs = $l->getTabs();
    $target = null;
    foreach ($tabs as $tab) { if ($tab->name === 'Media') { $target = $tab; break; } }
    if (!$target) { $target = $tabs[0]; }
    $els = $target->getElements();
    $els[] = new \craft\fieldlayoutelements\CustomField($f);
    $target->setElements($els);
    $l->setTabs($tabs);
    $t->setFieldLayout($l);
    echo str_pad($h, 16) . ($svc->saveEntryType($t) ? 'ADDED' : 'FAILED') . PHP_EOL;
}
