/**
 * Gives the Communities category group (handle `neighborhood`) its own URLs so
 * each community term gets a page at /communities/{slug}.
 *
 * Sets, for every site the group is enabled on:
 *   hasUrls   = true
 *   uriFormat = communities/{slug}
 *   template  = communities/_entry
 *
 * Safe to run twice. Prints the settings before and after.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/setup_community_urls.php'))"
 */

$categoriesService = Craft::$app->getCategories();

$group = $categoriesService->getGroupByHandle('neighborhood');
if (!$group) {
    echo 'ERROR: category group `neighborhood` not found' . PHP_EOL;
    return;
}

echo 'Group: ' . $group->name . ' (handle ' . $group->handle . ')' . PHP_EOL;

$siteSettings = $group->getSiteSettings();
if (!$siteSettings) {
    echo 'ERROR: group has no site settings' . PHP_EOL;
    return;
}

echo '--- before ---' . PHP_EOL;
foreach ($siteSettings as $siteId => $settings) {
    $site = Craft::$app->getSites()->getSiteById($siteId);
    echo sprintf(
        '%s: hasUrls=%s uriFormat=%s template=%s' . PHP_EOL,
        $site ? $site->handle : $siteId,
        $settings->hasUrls ? 'true' : 'false',
        $settings->uriFormat ?? 'null',
        $settings->template ?? 'null'
    );
}

foreach ($siteSettings as $settings) {
    $settings->hasUrls = true;
    $settings->uriFormat = 'communities/{slug}';
    $settings->template = 'communities/_entry';
}

$group->setSiteSettings($siteSettings);

if (!$categoriesService->saveGroup($group)) {
    echo 'FAILED to save group: ' . json_encode($group->getErrors()) . PHP_EOL;
    return;
}

$check = $categoriesService->getGroupByHandle('neighborhood');

echo '--- after ---' . PHP_EOL;
foreach ($check->getSiteSettings() as $siteId => $settings) {
    $site = Craft::$app->getSites()->getSiteById($siteId);
    echo sprintf(
        '%s: hasUrls=%s uriFormat=%s template=%s' . PHP_EOL,
        $site ? $site->handle : $siteId,
        $settings->hasUrls ? 'true' : 'false',
        $settings->uriFormat ?? 'null',
        $settings->template ?? 'null'
    );
}

$total = \craft\elements\Category::find()->group('neighborhood')->status(null)->count();
echo '--- sample URLs (' . $total . ' terms) ---' . PHP_EOL;
$sample = \craft\elements\Category::find()->group('neighborhood')->status(null)->limit(5)->all();
foreach ($sample as $term) {
    echo $term->title . ': ' . ($term->getUrl() ?? 'no url') . PHP_EOL;
}

echo 'Done. Run project-config/write if you keep config in files, then commit config/project/.' . PHP_EOL;
