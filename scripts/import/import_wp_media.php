/**
 * Downloads featured images from the WordPress site, creates Craft assets,
 * sets featuredImage on entries, and restores collection order.
 * Needs inventory/wp_media_order.json.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_wp_media.php'))"
 */

$APPLY = false;
$VOLUME = null;

$path = \Craft::getAlias('@root') . '/inventory/wp_media_order.json';
if (!file_exists($path)) { echo 'ERROR: ' . $path . ' not found' . PHP_EOL; return; }
$data = json_decode(file_get_contents($path), true);

$volumes = Craft::$app->getVolumes()->getAllVolumes();
if (!count($volumes)) { echo 'ERROR: no asset volumes exist' . PHP_EOL; return; }
if ($VOLUME === null) { $VOLUME = $volumes[0]->handle; }
$volume = Craft::$app->getVolumes()->getVolumeByHandle($VOLUME);
if (!$volume) { echo 'ERROR: volume ' . $VOLUME . ' not found' . PHP_EOL; return; }
echo 'Volumes available: ';
foreach ($volumes as $v) { echo $v->handle . ' '; }
echo PHP_EOL . 'Using volume: ' . $volume->handle . PHP_EOL;

$sectionFor = ['article'=>'articles','person'=>'persons','place'=>'places','organization'=>'organizations',
  'collection'=>'collections','event'=>'events','obituary'=>'obituaries','scv_group'=>'groups','military_profile'=>'warMemorials'];

$elements = Craft::$app->getElements();
$assetsSvc = Craft::$app->getAssets();
$root = $assetsSvc->getRootFolderByVolumeId($volume->id);

$done = 0; $skipped = 0; $noField = [];

echo '=== featured images ===' . PHP_EOL;
foreach ($data['featured'] as $slug => $info) {
    $section = $sectionFor[$info['type']] ?? null;
    if (!$section) { continue; }
    $entry = \craft\elements\Entry::find()->section($section)->slug($slug)->status(null)->one();
    if (!$entry) { echo 'no entry: ' . $slug . PHP_EOL; $skipped++; continue; }

    $has = false;
    foreach ($entry->getFieldLayout()->getCustomFields() as $f) {
        if ($f->handle === 'featuredImage') { $has = true; break; }
    }
    if (!$has) { $noField[$info['type']] = true; $skipped++; continue; }

    try { $existing = $entry->getFieldValue('featuredImage')->count(); } catch (\Throwable $e) { $existing = 0; }
    if ($existing) { $skipped++; continue; }

    $url = $info['image']['url'];
    $filename = basename(parse_url($url, PHP_URL_PATH));

    $asset = \craft\elements\Asset::find()->volumeId($volume->id)->filename($filename)->one();

    echo str_pad($slug, 44) . $filename . ($asset ? ' (asset exists)' : '') . PHP_EOL;

    if (!$APPLY) { $done++; continue; }

    if (!$asset) {
        $temp = \Craft::$app->getPath()->getTempPath() . '/' . $filename;
        $ctx = stream_context_create(['http' => ['header' => "User-Agent: Mozilla/5.0\r\n", 'timeout' => 30]]);
        $bytes = @file_get_contents($url, false, $ctx);
        if ($bytes === false || strlen($bytes) < 100) { echo '  download failed' . PHP_EOL; $skipped++; continue; }
        file_put_contents($temp, $bytes);
        $asset = new \craft\elements\Asset();
        $asset->tempFilePath = $temp;
        $asset->setFilename($filename);
        $asset->newFolderId = $root->id;
        $asset->setVolumeId($volume->id);
        $asset->avoidFilenameConflicts = true;
        $asset->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
        if ($info['image']['alt']) { $asset->alt = $info['image']['alt']; }
        if (!$elements->saveElement($asset)) { echo '  asset save failed: ' . json_encode($asset->getErrors()) . PHP_EOL; $skipped++; continue; }
    }

    $entry->setFieldValue('featuredImage', [$asset->id]);
    if (!$elements->saveElement($entry)) { echo '  entry save failed' . PHP_EOL; $skipped++; continue; }
    $done++;
}

echo '=== collection order ===' . PHP_EOL;
foreach ($data['order'] as $colSlug => $slugs) {
    if (!count($slugs)) { echo $colSlug . ': no order recorded' . PHP_EOL; continue; }
    $col = \craft\elements\Entry::find()->section('collections')->slug($colSlug)->status(null)->one();
    if (!$col) { echo 'no collection: ' . $colSlug . PHP_EOL; continue; }
    $ids = [];
    foreach ($slugs as $s) {
        $a = \craft\elements\Entry::find()->section('articles')->slug($s)->status(null)->one();
        if ($a) { $ids[] = $a->id; }
    }
    echo $colSlug . ': ' . count($ids) . ' articles in order' . PHP_EOL;
    if ($APPLY && count($ids)) {
        $col->setFieldValue('articlesInCollection', $ids);
        if (!$elements->saveElement($col)) { echo '  save failed: ' . json_encode($col->getErrors()) . PHP_EOL; }
    }
}

echo '=== summary ===' . PHP_EOL;
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
echo 'images handled: ' . $done . PHP_EOL;
echo 'skipped: ' . $skipped . PHP_EOL;
if (count($noField)) { echo 'entry types missing a featuredImage field: ' . implode(', ', array_keys($noField)) . PHP_EOL; }
