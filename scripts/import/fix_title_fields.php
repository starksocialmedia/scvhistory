/**
 * Ensures every entry type has the native Title field in its field layout.
 * Without it, Craft silently discards titles on save.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_title_fields.php'))"
 */

$svc = Craft::$app->getEntries();

$titleClass = null;
foreach ([
    'craft\\fieldlayoutelements\\entries\\EntryTitleField',
    'craft\\fieldlayoutelements\\TitleField',
] as $candidate) {
    if (class_exists($candidate)) {
        $titleClass = $candidate;
        break;
    }
}

if ($titleClass === null) {
    echo 'ERROR: no title field layout element class found' . PHP_EOL;
    return;
}

echo 'Using: ' . $titleClass . PHP_EOL;

foreach ($svc->getAllEntryTypes() as $type) {
    $layout = $type->getFieldLayout();
    $hasTitle = false;
    foreach ($layout->getTabs() as $tab) {
        foreach ($tab->getElements() as $el) {
            if ($el instanceof $titleClass) {
                $hasTitle = true;
                break 2;
            }
        }
    }

    $needsFlag = !$type->hasTitleField && ($type->titleFormat === null || $type->titleFormat === '');

    if ($hasTitle && !$needsFlag) {
        echo str_pad($type->handle, 20) . 'ok' . PHP_EOL;
        continue;
    }

    if ($type->titleFormat !== null && $type->titleFormat !== '' && $type->titleFormat !== '{title}') {
        echo str_pad($type->handle, 20) . 'skipped, title comes from format ' . $type->titleFormat . PHP_EOL;
        continue;
    }

    $tabs = $layout->getTabs();
    if (!count($tabs)) {
        $tab = new \craft\models\FieldLayoutTab([
            'layout' => $layout,
            'name' => 'Content',
            'sortOrder' => 1,
        ]);
        $tab->setElements([]);
        $tabs[] = $tab;
    }

    if (!$hasTitle) {
        $first = $tabs[0];
        $elements = $first->getElements();
        array_unshift($elements, new $titleClass());
        $first->setElements($elements);
        $tabs[0] = $first;
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
    }

    $type->hasTitleField = true;
    if ($type->titleFormat === '{title}') {
        $type->titleFormat = null;
    }

    if ($svc->saveEntryType($type)) {
        echo str_pad($type->handle, 20) . 'FIXED (title element added: ' . ($hasTitle ? 'no' : 'yes') . ')' . PHP_EOL;
    } else {
        echo str_pad($type->handle, 20) . 'FAILED ' . json_encode($type->getErrors()) . PHP_EOL;
    }
}
