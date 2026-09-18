/**
 * Adds recordImages and recordDocuments to the Communities category group
 * (handle `neighborhood`).
 *
 * Community terms are the only content type in the archive without them, so a
 * community page cannot carry photos or documents while every entry type can.
 * Both fields already exist; this only puts them on the group's field layout.
 *
 * Safe to run twice: a field already on the layout is left alone, and the
 * existing layout elements are preserved.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_community_media.php'))"
 */

$APPLY = false;

$GROUP_HANDLE = 'neighborhood';
$WANTED = ['recordImages', 'recordDocuments'];

$categories = Craft::$app->getCategories();
$fields     = Craft::$app->getFields();

echo ($APPLY ? '=== APPLYING ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL;

$group = $categories->getGroupByHandle($GROUP_HANDLE);
if (!$group) {
    echo 'ERROR: category group `' . $GROUP_HANDLE . '` not found' . PHP_EOL;
    return;
}
echo 'Group: ' . $group->name . ' (handle ' . $group->handle . ')' . PHP_EOL;

$layout = $group->getFieldLayout();
$onLayout = [];
foreach ($layout->getCustomFields() as $f) { $onLayout[] = $f->handle; }
echo 'Layout now: ' . implode(', ', $onLayout) . PHP_EOL;

$toAdd = [];
foreach ($WANTED as $h) {
    if (in_array($h, $onLayout, true)) {
        echo '  ' . $h . ': already on the layout' . PHP_EOL;
        continue;
    }
    $field = $fields->getFieldByHandle($h);
    if (!$field) {
        echo '  ' . $h . ': FIELD DOES NOT EXIST, skipping' . PHP_EOL;
        continue;
    }
    echo '  ' . $h . ': would add' . PHP_EOL;
    $toAdd[] = $field;
}

if (!$toAdd) {
    echo PHP_EOL . 'Nothing to add.' . PHP_EOL;
    return;
}

if ($APPLY) {
    try {
        $tabs = $layout->getTabs();
        $tab = null;
        foreach ($tabs as $t) {
            if (strtolower($t->name) === 'content') { $tab = $t; break; }
        }
        if ($tab === null) { $tab = $tabs[0] ?? null; }
        if ($tab === null) {
            $tab = new \craft\models\FieldLayoutTab([
                'layout' => $layout, 'name' => 'Content', 'sortOrder' => 1,
            ]);
            $tab->setElements([]);
            $tabs[] = $tab;
        }

        $els = $tab->getElements();
        foreach ($toAdd as $field) {
            $els[] = new \craft\fieldlayoutelements\CustomField($field);
        }
        $tab->setElements($els);
        $layout->setTabs($tabs);
        $group->setFieldLayout($layout);

        if (!$categories->saveGroup($group)) {
            echo 'FAILED to save group: ' . json_encode($group->getErrors()) . PHP_EOL;
            return;
        }
    } catch (\Throwable $e) {
        echo 'FAILED: ' . $e->getMessage() . PHP_EOL;
        return;
    }

    $check = $categories->getGroupByHandle($GROUP_HANDLE);
    $after = [];
    foreach ($check->getFieldLayout()->getCustomFields() as $f) { $after[] = $f->handle; }
    echo PHP_EOL . 'Layout after: ' . implode(', ', $after) . PHP_EOL;
    echo 'Now run project-config/write if you keep config in files, then commit config/project/.' . PHP_EOL;
} else {
    echo PHP_EOL . 'Nothing was written. Set $APPLY = true and run again.' . PHP_EOL;
}
