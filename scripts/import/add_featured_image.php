/**
 * Adds the featuredImage field to every entry type that lacks it.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_featured_image.php'))"
 */

$entriesSvc = Craft::$app->getEntries();
$fi = Craft::$app->getFields()->getFieldByHandle('featuredImage');
if (!$fi) { echo 'ERROR: featuredImage field does not exist' . PHP_EOL; return; }

foreach ($entriesSvc->getAllEntryTypes() as $type) {
    $layout = $type->getFieldLayout();
    foreach ($layout->getCustomFields() as $f) {
        if ($f->handle === 'featuredImage') { echo str_pad($type->handle, 18) . 'has it' . PHP_EOL; continue 2; }
    }
    $tabs = $layout->getTabs();
    if (!count($tabs)) {
        $t = new \craft\models\FieldLayoutTab(['layout' => $layout, 'name' => 'Content', 'sortOrder' => 1]);
        $t->setElements([]);
        $tabs[] = $t;
    }
    $first = $tabs[0];
    $els = $first->getElements();
    $at = 0;
    foreach ($els as $i => $el) {
        if ($el instanceof \craft\fieldlayoutelements\BaseNativeField) { $at = $i + 1; }
    }
    array_splice($els, $at, 0, [new \craft\fieldlayoutelements\CustomField($fi)]);
    $first->setElements($els);
    $tabs[0] = $first;
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo str_pad($type->handle, 18) . ($entriesSvc->saveEntryType($type) ? 'ADDED' : 'FAILED ' . json_encode($type->getErrors())) . PHP_EOL;
}
