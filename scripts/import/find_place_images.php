/**
 * Searches Wikimedia Commons for a freely licensed image of each Place that
 * has none, and reports candidates with licence and author. Nothing is
 * downloaded or attached in dry run. Set $APPLY = true to download the top
 * candidate, create the asset, and set featuredImage.
 * Asset title becomes the caption; alt becomes the credit line.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/find_place_images.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$OK = ['cc0', 'cc-by', 'cc-by-sa', 'public domain', 'pd-us', 'pd-old'];

$elements = Craft::$app->getElements();
$volume = Craft::$app->getVolumes()->getAllVolumes()[0] ?? null;
if (!$volume) { echo 'ERROR: no asset volume' . PHP_EOL; return; }
$root = Craft::$app->getAssets()->getRootFolderByVolumeId($volume->id);

$get = function (string $url) {
    $ctx = stream_context_create(['http' => ['header' => "User-Agent: SCVHistory.com archive build (nathan@starksocial.com)\r\n", 'timeout' => 20]]);
    $raw = @file_get_contents($url, false, $ctx);
    return $raw === false ? null : json_decode($raw, true);
};

$api = 'https://commons.wikimedia.org/w/api.php';

foreach (\craft\elements\Entry::find()->section('places')->status(null)->orderBy('title asc')->all() as $place) {
    $has = false;
    try { $has = $place->getFieldValue('featuredImage')->count() > 0; } catch (\Throwable $e) {}
    if ($has) { echo str_pad($place->slug, 36) . 'has an image' . PHP_EOL; continue; }

    $term = $place->title . ' California';
    $search = $get($api . '?action=query&format=json&generator=search&gsrnamespace=6&gsrlimit=5&gsrsearch=' . rawurlencode($term)
        . '&prop=imageinfo&iiprop=url|extmetadata|size&iiurlwidth=1600');
    $pages = $search['query']['pages'] ?? [];
    if (!count($pages)) { echo str_pad($place->slug, 36) . 'no Commons results' . PHP_EOL; continue; }

    $pick = null;
    foreach ($pages as $p) {
        $ii = $p['imageinfo'][0] ?? null;
        if (!$ii) { continue; }
        $meta = $ii['extmetadata'] ?? [];
        $lic = strtolower(strip_tags($meta['LicenseShortName']['value'] ?? ''));
        $free = false;
        foreach ($OK as $tok) { if (str_contains($lic, $tok)) { $free = true; break; } }
        if (!$free) { continue; }
        if (($ii['width'] ?? 0) < 800) { continue; }
        $pick = [
            'title' => $p['title'],
            'url' => $ii['thumburl'] ?? $ii['url'],
            'lic' => strip_tags($meta['LicenseShortName']['value'] ?? ''),
            'author' => trim(strip_tags($meta['Artist']['value'] ?? '')),
            'desc' => trim(strip_tags($meta['ImageDescription']['value'] ?? '')),
            'page' => 'https://commons.wikimedia.org/wiki/' . rawurlencode($p['title']),
        ];
        break;
    }

    if (!$pick) { echo str_pad($place->slug, 36) . 'no freely licensed candidate' . PHP_EOL; continue; }

    echo str_pad($place->slug, 36) . $pick['lic'] . ' | ' . mb_substr($pick['title'], 5, 60) . PHP_EOL;
    echo str_pad('', 36) . $pick['page'] . PHP_EOL;

    if (!$APPLY) { continue; }

    $ctx = stream_context_create(['http' => ['header' => "User-Agent: SCVHistory.com archive build (nathan@starksocial.com)\r\n", 'timeout' => 40]]);
    $bytes = @file_get_contents($pick['url'], false, $ctx);
    if ($bytes === false || strlen($bytes) < 5000) { echo str_pad('', 36) . 'download failed' . PHP_EOL; continue; }

    $filename = $place->slug . '.' . (pathinfo(parse_url($pick['url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg');
    $temp = Craft::$app->getPath()->getTempPath() . '/' . $filename;
    file_put_contents($temp, $bytes);

    $asset = new \craft\elements\Asset();
    $asset->tempFilePath = $temp;
    $asset->setFilename($filename);
    $asset->newFolderId = $root->id;
    $asset->setVolumeId($volume->id);
    $asset->avoidFilenameConflicts = true;
    $asset->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
    $asset->title = $pick['desc'] !== '' ? mb_substr($pick['desc'], 0, 200) : $place->title;
    $asset->alt = trim(($pick['author'] !== '' ? $pick['author'] . '. ' : '') . $pick['lic'] . '. Wikimedia Commons.');
    if (!$elements->saveElement($asset)) { echo str_pad('', 36) . 'asset failed' . PHP_EOL; continue; }

    $place->setFieldValue('featuredImage', [$asset->id]);
    echo str_pad('', 36) . ($elements->saveElement($place) ? 'ATTACHED' : 'entry save failed') . PHP_EOL;
}

echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . '. Review every candidate before applying; Commons search is not always right.' . PHP_EOL;
