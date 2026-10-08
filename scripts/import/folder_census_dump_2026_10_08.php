// Read-only dump for folder_census.py: every entry with a legacy page, the assets it carries, and every asset's provenance.
$assetHandles = [];
$entries = [];
foreach (craft\elements\Entry::find()->status(null)->site('*')->unique()->batch(500) as $batch) {
  foreach ($batch as $e) {
    $layout = $e->getFieldLayout(); if (!$layout) continue;
    $h = array_map(fn($f) => $f->handle, $layout->getCustomFields());
    $lu = in_array('legacyUrl', $h) ? trim((string)$e->getFieldValue('legacyUrl')) : '';
    $lk = in_array('legacyKey', $h) ? trim((string)$e->getFieldValue('legacyKey')) : '';
    $ids = [];
    foreach ($layout->getCustomFields() as $f) {
      if ($f instanceof craft\fields\Assets) { foreach ($e->getFieldValue($f->handle)->status(null)->ids() as $i) $ids[] = $i; }
    }
    $entries[] = ['id'=>$e->id,'section'=>$e->section ? $e->section->handle : null,'type'=>$e->type->handle,'title'=>$e->title,'legacyUrl'=>$lu,'legacyKey'=>$lk,'assets'=>array_values(array_unique($ids)),'enabled'=>$e->enabled];
  }
}
$assets = [];
foreach (craft\elements\Asset::find()->batch(1000) as $batch) {
  foreach ($batch as $a) {
    $h = array_map(fn($f) => $f->handle, $a->getFieldLayout() ? $a->getFieldLayout()->getCustomFields() : []);
    $assets[] = ['id'=>$a->id,'file'=>$a->filename,'folder'=>$a->folderPath,'lsp'=>in_array('legacySourcePath',$h) ? (string)$a->getFieldValue('legacySourcePath') : '','sum'=>in_array('sourceChecksum',$h) ? (string)$a->getFieldValue('sourceChecksum') : ''];
  }
}
file_put_contents('/var/www/html/storage/runtime/photo-import/folder-dump.json', json_encode(['entries'=>$entries,'assets'=>$assets]));
echo count($entries), " entries, ", count($assets), " assets\n";
