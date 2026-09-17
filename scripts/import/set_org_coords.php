/**
 * Sets orgLat/orgLng on Organizations from the approved design coordinates.
 * Only fills empty values. Set $FORCE = true to overwrite.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_org_coords.php'))"
 */
$FORCE = false;
$coords = [
    'henry-mayo-newhall-memorial-hospital' => [34.3924, -118.5628],
    'historical-society-of-southern-california' => [34.0740, -118.2130],
    'los-angeles-herald' => [34.0510, -118.2470],
    'mission-san-francisco-de-asis' => [37.7644, -122.4270],
    'mission-san-gabriel-arcangel' => [34.0975, -118.1067],
    'mission-santa-cruz' => [36.9770, -122.0290],
    'rancho-camulos' => [34.4070, -118.7530],
    'rancho-el-tejon' => [34.9450, -118.7900],
    'rancho-san-francisco' => [34.4275, -118.6125],
    'santa-clarita-valley-chamber-of-commerce' => [34.4150, -118.5450],
    'santa-clarita-valley-water' => [34.4400, -118.5450],
    'scvhistory-com-santa-clarita-valley-history' => [34.3870, -118.5300],
    'the-city-of-santa-clarita' => [34.3917, -118.5426],
    'the-santa-clarita-valley-signal' => [34.4230, -118.5460],
];
$elements = Craft::$app->getElements();
$all = \craft\elements\Entry::find()->section('organizations')->status(null)->all();
$bySlug = [];
foreach ($all as $e) { $bySlug[$e->slug] = $e; }
$set = 0;
foreach ($coords as $slug => [$lat, $lng]) {
    $e = $bySlug[$slug] ?? null;
    if (!$e) {
        $hit = null;
        foreach ($all as $cand) { if (str_starts_with($cand->slug, substr($slug, 0, 14))) { $hit = $cand; break; } }
        if ($hit) { echo str_pad($slug, 44) . '-> matched ' . $hit->slug . PHP_EOL; $e = $hit; }
        else { echo str_pad($slug, 44) . 'NOT FOUND' . PHP_EOL; continue; }
    }
    $cur = $e->getFieldValue('orgLat');
    if (!$FORCE && $cur !== null && $cur !== '' && (float)$cur != 0.0) { echo str_pad($e->slug, 44) . 'has ' . $cur . PHP_EOL; continue; }
    $e->setFieldValue('orgLat', $lat);
    $e->setFieldValue('orgLng', $lng);
    echo str_pad($e->slug, 44) . ($elements->saveElement($e) ? 'set ' . $lat . ', ' . $lng : 'FAILED ' . json_encode($e->getErrors())) . PHP_EOL;
    $set++;
}
echo 'set: ' . $set . ' of ' . count($all) . ' organizations' . PHP_EOL;
echo 'all org slugs: ' . implode(', ', array_keys($bySlug)) . PHP_EOL;
