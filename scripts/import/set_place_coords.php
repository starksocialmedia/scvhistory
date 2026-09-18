/**
 * Sets placeLat/placeLng on Places from the approved design coordinates,
 * and merges the beales-cut stub into beales-cut-stagecoach-pass.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_place_coords.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$coords = [
    'sleepy-valley' => [34.489, -118.355],
    'beales-cut-stagecoach-pass' => [34.3444, -118.5074],
    'estancia' => [34.4275, -118.6125],
    'fort-tejon' => [34.8735, -118.8955],
    'harry-carey-ranch' => [34.463, -118.562],
    'heritage-junction' => [34.381, -118.5325],
    'lake-hughes' => [34.677, -118.438],
    'lang' => [34.444, -118.363],
    'lyons-station' => [34.369, -118.5225],
    'melody-ranch' => [34.383, -118.501],
    'newhall-pass-interchange' => [34.339, -118.504],
    'rancho-camulos' => [34.407, -118.753],
    'ridge-route' => [34.62, -118.73],
    'saugus-speedway' => [34.42, -118.545],
    'magic-mountain' => [34.425, -118.597],
    'vasquez-rocks' => [34.488, -118.32],
];

$elements = Craft::$app->getElements();
$find = fn($slug) => \craft\elements\Entry::find()->section('places')->slug($slug)->status(null)->one();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

echo '=== merge beales-cut into beales-cut-stagecoach-pass ===' . PHP_EOL;
$keep = $find('beales-cut-stagecoach-pass');
$dupe = $find('beales-cut');
if ($keep && $dupe) {
    $moves = [];
    foreach (['neighborhood', 'placePeople', 'placeOrganizations', 'placeEvents', 'placeArticles', 'relatedPlaces'] as $h) {
        try {
            $from = $dupe->getFieldValue($h)->ids();
            $to = $keep->getFieldValue($h)->ids();
        } catch (\Throwable $e) { continue; }
        $merged = array_values(array_unique(array_merge($to, $from)));
        if (count($merged) > count($to)) { $moves[$h] = $merged; }
    }
    foreach (['placeLegacyUrl', 'legacyUrl', 'sourcePath'] as $h) {
        try {
            $fv = (string)$dupe->getFieldValue($h);
            $tv = (string)$keep->getFieldValue($h);
        } catch (\Throwable $e) { continue; }
        if ($fv !== '' && $tv === '') { $moves[$h] = $fv; }
    }
    echo 'carry over: ' . (count($moves) ? implode(', ', array_keys($moves)) : 'nothing') . PHP_EOL;
    $inbound = \craft\elements\Entry::find()->relatedTo($dupe)->status(null)->count();
    echo 'entries pointing at the stub: ' . $inbound . PHP_EOL;
    if ($APPLY) {
        foreach ($moves as $h => $v) { $keep->setFieldValue($h, $v); }
        if (!$elements->saveElement($keep)) { echo 'SAVE FAILED keep: ' . json_encode($keep->getErrors()) . PHP_EOL; }
        $elements->deleteElement($dupe);
        echo 'stub deleted' . PHP_EOL;
    }
} else {
    echo ($keep ? '' : 'keep missing. ') . ($dupe ? '' : 'stub already gone.') . PHP_EOL;
}

echo '=== coordinates ===' . PHP_EOL;
$set = 0;
foreach ($coords as $slug => [$lat, $lng]) {
    $e = $find($slug);
    if (!$e) { echo str_pad($slug, 30) . 'NOT FOUND' . PHP_EOL; continue; }
    $curLat = $e->getFieldValue('placeLat');
    $curLng = $e->getFieldValue('placeLng');
    $has = false;
    echo str_pad($slug, 30) . ($has ? 'has ' . $curLat . ', ' . $curLng : 'set ' . $lat . ', ' . $lng) . PHP_EOL;
    if ($has) { continue; }
    if ($APPLY) {
        $e->setFieldValue('placeLat', $lat);
        $e->setFieldValue('placeLng', $lng);
        if (!$elements->saveElement($e)) { echo '  SAVE FAILED: ' . json_encode($e->getErrors()) . PHP_EOL; continue; }
    }
    $set++;
}
echo '=== summary ===' . PHP_EOL;
echo 'coordinates to set: ' . $set . PHP_EOL;
echo 'places after: ' . (\craft\elements\Entry::find()->section('places')->status(null)->count() - (($APPLY || !$dupe) ? 0 : 1)) . PHP_EOL;
