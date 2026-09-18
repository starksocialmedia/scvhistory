/**
 * Converts the 22 community Place entries into Community terms, removes three
 * Place entries that duplicate Organizations, and titles the keeper Places.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/convert_places_to_communities.php'))"
 */

$APPLY = false;

$elements = Craft::$app->getElements();
$categoriesService = Craft::$app->getCategories();
$group = $categoriesService->getGroupByHandle('neighborhood');
if (!$group) {
    echo 'ERROR: community group not found' . PHP_EOL;
    return;
}

$toCommunity = [
    'acton' => 'Acton',
    'agua-dulce' => 'Agua Dulce',
    'bouquet-canyon' => 'Bouquet Canyon',
    'canyon-country' => 'Canyon Country',
    'castaic' => 'Castaic',
    'hasley-canyon' => 'Hasley Canyon',
    'haskell-canyon' => 'Haskell Canyon',
    'lebec' => 'Lebec',
    'mentryville' => 'Mentryville',
    'mojave-desert' => 'Mojave Desert',
    'newhall' => 'Newhall',
    'pico-canyon' => 'Pico Canyon',
    'piru' => 'Piru',
    'placerita-canyon' => 'Placerita Canyon',
    'potrero-canyon' => 'Potrero Canyon',
    'san-francisquito-canyon' => 'San Francisquito Canyon',
    'saugus' => 'Saugus',
    'soledad-canyon' => 'Soledad Canyon',
    'tejon' => 'Tejon',
    'towsley-canyon' => 'Towsley Canyon',
    'val-verde' => 'Val Verde',
    'valencia' => 'Valencia',
];

$extraTerms = ['Ravenna', 'Frazier Park', 'Soledad Township'];

$deleteAsDuplicate = ['rancho-san-francisco', 'tejon-ranch', 'santa-clarita'];

$keepers = [
    'beales-cut'        => ["Beale's Cut Stagecoach Pass", 'transportation-corridor', 'Newhall'],
    'estancia'          => ['Estancia de San Francisco Xavier', 'mission-church', 'Castaic'],
    'fort-tejon'        => ['Fort Tejon', 'historic-site', 'Tejon'],
    'harry-carey-ranch' => ['Harry Carey Ranch', 'historic-site', 'Saugus'],
    'heritage-junction' => ['Heritage Junction Historic Park', 'historic-site', 'Newhall'],
    'lang'              => ['Lang Station', 'settlement-town', 'Soledad Canyon'],
    'magic-mountain'    => ['Six Flags Magic Mountain', 'historic-site', 'Valencia'],
    'melody-ranch'      => ['Melody Ranch Motion Picture Studio', 'historic-site', 'Newhall'],
    'rancho-camulos'    => ['Rancho Camulos', 'historic-site', 'Piru'],
    'ridge-route'       => ['Ridge Route', 'transportation-corridor', 'Castaic'],
    'saugus-speedway'   => ['Saugus Speedway', 'historic-site', 'Saugus'],
    'vasquez-rocks'     => ['Vasquez Rocks', 'natural-feature', 'Agua Dulce'],
];

$termBySlugCache = [];
$findTerm = function (string $title) use (&$termBySlugCache, $group) {
    $key = strtolower($title);
    if (isset($termBySlugCache[$key])) {
        return $termBySlugCache[$key];
    }
    $term = \craft\elements\Category::find()->group('neighborhood')->title($title)->status(null)->one();
    $termBySlugCache[$key] = $term;
    return $term;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo '--- 1. community terms ---' . PHP_EOL;

$wantTerms = array_values($toCommunity);
foreach ($extraTerms as $t) {
    $wantTerms[] = $t;
}

foreach ($wantTerms as $title) {
    $term = $findTerm($title);
    if ($term) {
        echo 'exists: ' . $title . PHP_EOL;
        continue;
    }
    echo 'CREATE term: ' . $title . PHP_EOL;
    if ($APPLY) {
        $cat = new \craft\elements\Category();
        $cat->groupId = $group->id;
        $cat->title = $title;
        if (!$elements->saveElement($cat)) {
            echo '  FAILED: ' . json_encode($cat->getErrors()) . PHP_EOL;
        } else {
            $termBySlugCache[strtolower($title)] = $cat;
        }
    }
}

echo '--- 2. delete community Place entries ---' . PHP_EOL;
$deleted = 0;
foreach ($toCommunity as $slug => $title) {
    $entry = \craft\elements\Entry::find()->section('places')->slug($slug)->status(null)->one();
    if (!$entry) {
        echo 'not found: ' . $slug . PHP_EOL;
        continue;
    }
    $rel = \craft\elements\Entry::find()->relatedTo($entry)->status(null)->count();
    echo 'DELETE place ' . $slug . ' (incoming relations: ' . $rel . ')' . PHP_EOL;
    if ($APPLY) {
        $elements->deleteElement($entry);
    }
    $deleted++;
}

echo '--- 3. delete duplicates of Organizations ---' . PHP_EOL;
foreach ($deleteAsDuplicate as $slug) {
    $entry = \craft\elements\Entry::find()->section('places')->slug($slug)->status(null)->one();
    if (!$entry) {
        echo 'not found: ' . $slug . PHP_EOL;
        continue;
    }
    $rel = \craft\elements\Entry::find()->relatedTo($entry)->status(null)->count();
    echo 'DELETE place ' . $slug . ' (incoming relations: ' . $rel . ')' . PHP_EOL;
    if ($APPLY) {
        $elements->deleteElement($entry);
    }
    $deleted++;
}

echo '--- 4. title and place the keepers ---' . PHP_EOL;
foreach ($keepers as $slug => [$title, $type, $community]) {
    $entry = \craft\elements\Entry::find()->section('places')->slug($slug)->status(null)->one();
    if (!$entry) {
        echo 'not found: ' . $slug . PHP_EOL;
        continue;
    }
    $sets = [];
    if (($entry->title ?: '') === '') {
        $sets[] = 'title=' . $title;
    }
    $term = $findTerm($community);
    $hasCommunity = false;
    try {
        $hasCommunity = (bool)$entry->getFieldValue('neighborhood')->count();
    } catch (\Throwable $e) {
        $hasCommunity = false;
    }
    if ($term && !$hasCommunity) {
        $sets[] = 'community=' . $community;
    }
    echo str_pad($slug, 20) . ($sets ? implode(', ', $sets) : 'no change') . ' [type: ' . $type . ']' . PHP_EOL;

    if ($APPLY && $sets) {
        if (($entry->title ?: '') === '') {
            $entry->title = $title;
        }
        if ($term && !$hasCommunity) {
            $entry->setFieldValue('neighborhood', [$term->id]);
        }
        if (!$elements->saveElement($entry)) {
            echo '  SAVE FAILED: ' . json_encode($entry->getErrors()) . PHP_EOL;
        }
    }
}

echo '--- summary ---' . PHP_EOL;
echo 'places to delete: ' . $deleted . PHP_EOL;
echo 'places remaining after: ' . (\craft\elements\Entry::find()->section('places')->status(null)->count() - ($APPLY ? 0 : $deleted)) . PHP_EOL;
echo 'NOTE: place type values are reported only. There is no placeType field yet.' . PHP_EOL;
echo 'NOTE: sleepy-valley and lake-hughes left untouched by request.' . PHP_EOL;
