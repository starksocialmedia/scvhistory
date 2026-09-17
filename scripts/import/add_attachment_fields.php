/**
 * Creates recordImages (images) and recordDocuments (pdf/docs) Assets fields
 * and adds them on a Media tab to every entry type that lacks them.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_attachment_fields.php'))"
 */

$fieldsSvc = Craft::$app->getFields();
$entriesSvc = Craft::$app->getEntries();

$volume = Craft::$app->getVolumes()->getAllVolumes()[0] ?? null;
if (!$volume) { echo 'ERROR: no asset volume' . PHP_EOL; return; }
$src = 'volume:' . $volume->uid;

$defs = [
    'recordImages' => ['name' => 'Images', 'kinds' => ['image'], 'instructions' => 'Photos and clippings for this record. Caption comes from the asset title; credit and source from the asset alt text. Drag to order.'],
    'recordDocuments' => ['name' => 'Documents', 'kinds' => ['pdf', 'word', 'text'], 'instructions' => 'PDFs and scans to offer for download. Label comes from the asset title.'],
];

$fields = [];
foreach ($defs as $handle => $d) {
    $f = $fieldsSvc->getFieldByHandle($handle);
    if ($f) { echo $handle . ': exists' . PHP_EOL; $fields[$handle] = $f; continue; }
    $f = new \craft\fields\Assets();
    $f->name = $d['name'];
    $f->handle = $handle;
    $f->instructions = $d['instructions'];
    $f->sources = [$src];
    $f->defaultUploadLocationSource = $src;
    $f->restrictFiles = true;
    $f->allowedKinds = $d['kinds'];
    $f->allowUploads = true;
    $f->viewMode = 'large';
    echo $handle . ': ' . ($fieldsSvc->saveField($f) ? 'created' : 'FAILED ' . json_encode($f->getErrors())) . PHP_EOL;
    $fields[$handle] = $fieldsSvc->getFieldByHandle($handle);
}

foreach ($entriesSvc->getAllEntryTypes() as $type) {
    $layout = $type->getFieldLayout();
    $have = [];
    foreach ($layout->getCustomFields() as $cf) { $have[] = $cf->handle; }
    $missing = array_values(array_diff(array_keys($fields), $have));
    if (!count($missing)) { echo str_pad($type->handle, 18) . 'has both' . PHP_EOL; continue; }

    $tabs = $layout->getTabs();
    $media = null;
    foreach ($tabs as $t) { if ($t->name === 'Media') { $media = $t; break; } }
    if (!$media) {
        $media = new \craft\models\FieldLayoutTab(['layout' => $layout, 'name' => 'Media', 'sortOrder' => count($tabs) + 1]);
        $media->setElements([]);
        $tabs[] = $media;
    }
    $els = $media->getElements();
    foreach ($missing as $h) { $els[] = new \craft\fieldlayoutelements\CustomField($fields[$h]); }
    $media->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo str_pad($type->handle, 18) . ($entriesSvc->saveEntryType($type) ? 'added ' . implode(', ', $missing) : 'FAILED ' . json_encode($type->getErrors())) . PHP_EOL;
}
