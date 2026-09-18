/**
 * Attaches the six reviewed Wikimedia Commons images to their Places.
 * Every file below is public domain and was checked by hand.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/attach_place_images.php'))"
 */

$APPLY = true;

$picks = [
    'fort-tejon' => ['Castaic Fort Tejon Rancho Castac Castec San Jose Public Library CALIFORNIA ROOM 1862 United States Survey.jpg', 'United States Survey map showing Fort Tejon and Rancho Castac, 1862.'],
    'harry-carey-ranch' => ['GENERAL VIEW OF HARRY CAREY MAIN RANCH COMPLEX, MAIN HOUSE TO RIGHT OF PALM TREE; CAMERA FACING WEST - Harry Carey Ranch, 28515 San Francisquito Canyon Road, Saugus, Los Angeles HABS CAL,19-SAUG,1-1.tif', 'Harry Carey Ranch main house, San Francisquito Canyon Road, Saugus. Historic American Buildings Survey.'],
    'lake-hughes' => ['Lake Hughes-kmf.JPG', 'Lake Hughes.'],
    'lang' => ['CHS-31101.4 View of William Crocker as he drives a rail spike at the recreation of the Southern Pacific Railroad line\'s completion at Lang Station, September 1926.jpg', 'William Crocker drives a rail spike at the re-enactment of the Southern Pacific completion at Lang Station, September 1926.'],
    'rancho-camulos' => ['Rancho Camulos.jpg', 'Rancho Camulos.'],
    'ridge-route' => ['Ridge Route Sandberg Inn.jpg', 'Sandberg Inn on the Ridge Route.'],
];

$elements = Craft::$app->getElements();
$volume = Craft::$app->getVolumes()->getAllVolumes()[0] ?? null;
if (!$volume) { echo 'ERROR: no asset volume' . PHP_EOL; return; }
$root = Craft::$app->getAssets()->getRootFolderByVolumeId($volume->id);

$ctx = stream_context_create(['http' => ['header' => "User-Agent: SCVHistory.com archive build (nathan@starksocial.com)\r\n", 'timeout' => 60]]);

foreach ($picks as $slug => [$file, $caption]) {
    $place = \craft\elements\Entry::find()->section('places')->slug($slug)->status(null)->one();
    if (!$place) { echo str_pad($slug, 24) . 'place not found' . PHP_EOL; continue; }
    try { if ($place->getFieldValue('featuredImage')->count()) { echo str_pad($slug, 24) . 'already has an image' . PHP_EOL; continue; } } catch (\Throwable $e) {}

    $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($file) . '?width=1600';
    echo str_pad($slug, 24) . ($APPLY ? 'downloading' : 'would download') . PHP_EOL;
    if (!$APPLY) { continue; }

    $bytes = @file_get_contents($url, false, $ctx);
    if ($bytes === false || strlen($bytes) < 5000) { echo str_pad('', 24) . 'download failed' . PHP_EOL; continue; }

    $ext = 'jpg';
    $temp = Craft::$app->getPath()->getTempPath() . '/' . $slug . '.' . $ext;
    file_put_contents($temp, $bytes);

    $asset = new \craft\elements\Asset();
    $asset->tempFilePath = $temp;
    $asset->setFilename($slug . '.' . $ext);
    $asset->newFolderId = $root->id;
    $asset->setVolumeId($volume->id);
    $asset->avoidFilenameConflicts = true;
    $asset->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
    $asset->title = $caption;
    $asset->alt = 'Public domain. Wikimedia Commons.';
    if (!$elements->saveElement($asset)) { echo str_pad('', 24) . 'asset failed: ' . json_encode($asset->getErrors()) . PHP_EOL; continue; }

    $place->setFieldValue('featuredImage', [$asset->id]);
    echo str_pad('', 24) . ($elements->saveElement($place) ? 'ATTACHED' : 'entry save failed') . PHP_EOL;
}
echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
