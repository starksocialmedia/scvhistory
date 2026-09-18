/**
 * Sets communityLat and communityLng on the Communities category terms
 * (category group handle `neighborhood`).
 *
 * Two sources, recorded per term in $COORDS below:
 *   polygon  the area weighted centroid of that community's polygon in
 *            web/data/communities.geojson
 *   wikipedia  the coordinates on the linked Wikipedia article, for the
 *            communities that have no polygon
 *
 * Nothing here is estimated by hand. Six communities have neither a polygon
 * nor an article carrying coordinates and are left alone; three more are
 * areas rather than points and are skipped on purpose. Both lists are at the
 * bottom of this file.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_community_coords.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$COORDS = [
    // slug => [lat, lng, source, reference]

    // ---- Centroid of the community polygon in web/data/communities.geojson ----
    'acton'                   => [34.481364, -118.216119, 'polygon', 'LA County CSA polygon centroid'],
    'agua-dulce'              => [34.497957, -118.323819, 'polygon', 'LA County CSA polygon centroid'],
    'bouquet-canyon'          => [34.595507, -118.363412, 'polygon', 'LA County CSA polygon centroid'],
    'canyon-country'          => [34.401997, -118.383483, 'polygon', 'Census ZCTA polygon centroid'],
    'castaic'                 => [34.610170, -118.628356, 'polygon', 'LA County CSA polygon centroid'],
    'lake-hughes'             => [34.661877, -118.429078, 'polygon', 'LA County CSA polygon centroid'],
    'newhall'                 => [34.364773, -118.492248, 'polygon', 'Census ZCTA polygon centroid'],
    'placerita-canyon'        => [34.382359, -118.471335, 'polygon', 'LA County CSA polygon centroid'],
    'san-francisquito-canyon' => [34.532341, -118.434100, 'polygon', 'LA County CSA polygon centroid'],
    'sand-canyon'             => [34.400323, -118.402651, 'polygon', 'LA County CSA polygon centroid'],
    'santa-clarita'           => [34.417488, -118.496486, 'polygon', 'LA County CSA polygon centroid'],
    'saugus'                  => [34.430335, -118.502539, 'polygon', 'Census ZCTA polygon centroid'],
    'stevenson-ranch'         => [34.376784, -118.607189, 'polygon', 'LA County CSA polygon centroid'],
    'val-verde'               => [34.444054, -118.673429, 'polygon', 'LA County CSA polygon centroid'],
    'valencia'                => [34.436709, -118.573269, 'polygon', 'Census ZCTA polygon centroid'],

    // ---- Wikipedia article coordinates, for communities with no polygon ----
    'camulos'                 => [34.406667, -118.756667, 'wikipedia', 'https://en.wikipedia.org/wiki/Rancho_Camulos'],
    'castaic-junction'        => [34.443056, -118.610833, 'wikipedia', 'https://en.wikipedia.org/wiki/Castaic_Junction,_California'],
    'fillmore'                => [34.401389, -118.917778, 'wikipedia', 'https://en.wikipedia.org/wiki/Fillmore,_California'],
    'frazier-park'            => [34.822778, -118.944722, 'wikipedia', 'https://en.wikipedia.org/wiki/Frazier_Park,_California'],
    'hasley-canyon'           => [34.481667, -118.666667, 'wikipedia', 'https://en.wikipedia.org/wiki/Hasley_Canyon,_California'],
    'lebec'                   => [34.842342, -118.865558, 'wikipedia', 'https://en.wikipedia.org/wiki/Lebec,_California'],
    'mentryville'             => [34.379000, -118.611000, 'wikipedia', 'https://en.wikipedia.org/wiki/Mentryville,_California'],
    'pico-canyon'             => [34.369444, -118.630278, 'wikipedia', 'https://en.wikipedia.org/wiki/Pico_Canyon_Oilfield'],
    'piru'                    => [34.407222, -118.799722, 'wikipedia', 'https://en.wikipedia.org/wiki/Piru,_California'],
    'soledad-canyon'          => [34.424167, -118.541389, 'wikipedia', 'https://en.wikipedia.org/wiki/Soledad_Canyon'],
    'tejon'                   => [34.802778, -118.876667, 'wikipedia', 'https://en.wikipedia.org/wiki/Tejon_Pass'],
];

$SKIP = [
    'mojave-desert'    => 'A region spanning several counties, not a point in the valley.',
    'saugus-valencia'  => 'A compound of two communities that each have their own term and their own coordinates.',
    'soledad-township' => 'A historic township covering much of the valley, not a point.',
];

$UNRESOLVED = [
    'fair-oaks-ranch',
    'haskell-canyon',
    'mint-canyon',
    'potrero-canyon',
    'ravenna',
    'towsley-canyon',
];

$categories = Craft::$app->getCategories();
$elements = Craft::$app->getElements();

$group = $categories->getGroupByHandle('neighborhood');
if (!$group) {
    echo 'ERROR: category group `neighborhood` not found' . PHP_EOL;
    return;
}

echo ($APPLY ? '=== APPLYING ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL;

$written = 0; $unchanged = 0; $missing = [];

foreach ($COORDS as $slug => $row) {
    [$lat, $lng, $source, $ref] = $row;

    $term = \craft\elements\Category::find()
        ->group('neighborhood')
        ->slug($slug)
        ->status(null)
        ->one();

    if (!$term) {
        $missing[] = $slug;
        echo sprintf('  %-24s TERM NOT FOUND', $slug) . PHP_EOL;
        continue;
    }

    $curLat = $term->communityLat;
    $curLng = $term->communityLng;

    if ($curLat !== null && $curLng !== null
        && abs((float)$curLat - $lat) < 0.000001
        && abs((float)$curLng - $lng) < 0.000001) {
        $unchanged++;
        echo sprintf('  %-24s already set  %.6f, %.6f', $slug, $lat, $lng) . PHP_EOL;
        continue;
    }

    echo sprintf('  %-24s %-10s %.6f, %.6f   %s', $slug, $source, $lat, $lng, $ref) . PHP_EOL;

    if ($APPLY) {
        $term->setFieldValues(['communityLat' => $lat, 'communityLng' => $lng]);
        if (!$elements->saveElement($term)) {
            echo '      FAILED: ' . json_encode($term->getErrors()) . PHP_EOL;
            continue;
        }
        $written++;
    }
}

echo PHP_EOL . '--- skipped on purpose, these are areas not points ---' . PHP_EOL;
foreach ($SKIP as $slug => $why) {
    echo sprintf('  %-24s %s', $slug, $why) . PHP_EOL;
}

echo PHP_EOL . '--- no coordinates found, left alone ---' . PHP_EOL;
foreach ($UNRESOLVED as $slug) {
    echo sprintf('  %-24s no polygon, and no Wikipedia article carrying coordinates', $slug) . PHP_EOL;
}

$total = \craft\elements\Category::find()->group('neighborhood')->status(null)->count();
echo PHP_EOL . sprintf(
    'Terms %d. Coordinates listed %d, written %d, already correct %d. Skipped %d. Unresolved %d.',
    $total, count($COORDS), $written, $unchanged, count($SKIP), count($UNRESOLVED)
) . PHP_EOL;

if (!$APPLY) {
    echo 'Nothing was written. Set $APPLY = true and run again.' . PHP_EOL;
}
