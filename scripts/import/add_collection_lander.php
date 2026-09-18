$fs = Craft::$app->getFields();
$f = $fs->getFieldByHandle('collectionIsMajor');
if ($f === null) {
    $f = new \craft\fields\Lightswitch();
    $f->name = 'Major collection';
    $f->handle = 'collectionIsMajor';
    $f->instructions = 'Tick for a landmark series that deserves its own landing page rather than the standard collection layout.';
    echo 'field: ' . ($fs->saveField($f) ? 'created' : 'FAILED ' . json_encode($f->getErrors())) . PHP_EOL;
    $f = $fs->getFieldByHandle('collectionIsMajor');
} else {
    echo 'field: exists' . PHP_EOL;
}
$svc = Craft::$app->getEntries();
$t = $svc->getEntryTypeByHandle('collection');
$l = $t->getFieldLayout();
$has = false;
foreach ($l->getCustomFields() as $c) { if ($c->handle === 'collectionIsMajor') { $has = true; } }
if (!$has) {
    $tabs = $l->getTabs();
    $els = $tabs[0]->getElements();
    $els[] = new \craft\fieldlayoutelements\CustomField($f);
    $tabs[0]->setElements($els);
    $l->setTabs($tabs);
    $t->setFieldLayout($l);
    echo 'layout: ' . ($svc->saveEntryType($t) ? 'ADDED' : 'FAILED') . PHP_EOL;
} else {
    echo 'layout: has it' . PHP_EOL;
}
$el = Craft::$app->getElements();
$c = \craft\elements\Entry::find()->section('collections')->slug('history-of-the-santa-clarita-valley')->status(null)->one();
if ($c) { $c->setFieldValue('collectionIsMajor', true); echo 'Reynolds: ' . ($el->saveElement($c) ? 'flagged' : 'FAILED') . PHP_EOL; }
