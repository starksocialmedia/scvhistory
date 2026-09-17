/**
 * Renames the "Neighborhood" category group to "Communities" (handle stays
 * `neighborhood`) and adds fields so community terms can hold prose, aliases,
 * and map coordinates. Safe to run twice.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/setup_communities.php'))"
 */

$fieldsService = Craft::$app->getFields();
$categoriesService = Craft::$app->getCategories();

$group = $categoriesService->getGroupByHandle('neighborhood');
if (!$group) {
    echo 'ERROR: category group `neighborhood` not found' . PHP_EOL;
    return;
}

$defs = [
    'communityAliases' => ['class' => 'text', 'name' => 'Also Known As', 'multiline' => false,
        'instructions' => 'Other or historic names, comma separated.'],
    'communityType'    => ['class' => 'text', 'name' => 'Community Type', 'multiline' => false,
        'instructions' => 'community, canyon, historic townsite, township, or census-designated place.'],
    'communityLat'     => ['class' => 'number', 'name' => 'Community Latitude'],
    'communityLng'     => ['class' => 'number', 'name' => 'Community Longitude'],
];

$created = [];
foreach ($defs as $handle => $def) {
    if ($fieldsService->getFieldByHandle($handle)) {
        continue;
    }
    if ($def['class'] === 'number') {
        $field = new \craft\fields\Number();
        $field->decimals = 6;
        $field->min = null;
        $field->max = null;
    } else {
        $field = new \craft\fields\PlainText();
        $field->multiline = $def['multiline'];
    }
    $field->name = $def['name'];
    $field->handle = $handle;
    if (isset($def['instructions'])) {
        $field->instructions = $def['instructions'];
    }
    if (!$fieldsService->saveField($field)) {
        echo 'FAILED to save field ' . $handle . ': ' . json_encode($field->getErrors()) . PHP_EOL;
        continue;
    }
    $created[] = $handle;
}

echo 'Fields created: ' . (count($created) ? implode(', ', $created) : 'none new') . PHP_EOL;

$wanted = ['body', 'communityAliases', 'communityType', 'communityLat', 'communityLng', 'culturalSensitivityNote'];

$layout = $group->getFieldLayout();
$onLayout = [];
foreach ($layout->getCustomFields() as $f) {
    $onLayout[] = $f->handle;
}

$toAdd = [];
foreach ($wanted as $handle) {
    if (in_array($handle, $onLayout, true)) {
        continue;
    }
    $field = $fieldsService->getFieldByHandle($handle);
    if (!$field) {
        echo 'WARNING: field ' . $handle . ' does not exist, skipping' . PHP_EOL;
        continue;
    }
    $toAdd[] = $field;
}

$group->name = 'Communities';

if (count($toAdd)) {
    $tabs = $layout->getTabs();
    $tab = $tabs[0] ?? null;
    if ($tab === null) {
        $tab = new \craft\models\FieldLayoutTab([
            'layout' => $layout,
            'name' => 'Content',
            'sortOrder' => 1,
        ]);
        $tab->setElements([]);
        $tabs[] = $tab;
    }
    $elements = $tab->getElements();
    foreach ($toAdd as $field) {
        $elements[] = new \craft\fieldlayoutelements\CustomField($field);
    }
    $tab->setElements($elements);
    $layout->setTabs($tabs);
    $group->setFieldLayout($layout);
}

if (!$categoriesService->saveGroup($group)) {
    echo 'FAILED to save group: ' . json_encode($group->getErrors()) . PHP_EOL;
    return;
}

$check = $categoriesService->getGroupByHandle('neighborhood');
$after = [];
foreach ($check->getFieldLayout()->getCustomFields() as $f) {
    $after[] = $f->handle;
}
echo 'Group name is now: ' . $check->name . ' (handle ' . $check->handle . ')' . PHP_EOL;
echo 'Layout fields: ' . implode(', ', $after) . PHP_EOL;

echo '--- term usage check ---' . PHP_EOL;
foreach (['saugus-valencia', 'camulos', 'fillmore'] as $slug) {
    $term = \craft\elements\Category::find()->group('neighborhood')->slug($slug)->status(null)->one();
    if (!$term) {
        echo $slug . ': term not found' . PHP_EOL;
        continue;
    }
    $count = \craft\elements\Entry::find()->relatedTo($term)->status(null)->count();
    echo $term->title . ': used by ' . $count . ' entries' . PHP_EOL;
}
