/**
 * Prepares the warMemorial entry type for the casualty import.
 *
 * Adds the existing neighborhood category field to the layout, so a casualty's
 * home of record surfaces on the community page like every other record type,
 * and creates two plain text fields that were otherwise landing in the
 * wmServiceExtra overflow on enough records to be worth their own slot:
 * wmFamily (4 records) and wmSelectiveServiceDate (3 records).
 *
 * Run this before scripts/import/import_warmemorial.php.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_wm_community.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$TYPE_HANDLE = 'warMemorial';

/* Fields to create if they do not exist yet. neighborhood already exists and is
   shared with every other section, so it is only added to the layout. */
$NEW_FIELDS = [
    'wmFamily' => [
        'name' => 'Family',
        'instructions' => 'Marital and family status exactly as printed on the legacy page, for example "Married, 4 children".',
    ],
    'wmSelectiveServiceDate' => [
        'name' => 'Selective Service registration date',
        'instructions' => 'Draft registration date exactly as printed, including any qualifier such as "(draft card undated; ~1941)".',
    ],
];

$TO_LAYOUT = ['neighborhood', 'wmFamily', 'wmSelectiveServiceDate'];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

$type = $svc->getEntryTypeByHandle($TYPE_HANDLE);
if (!$type) {
    echo 'ERROR: entry type ' . $TYPE_HANDLE . ' not found' . PHP_EOL;
    return;
}

foreach ($NEW_FIELDS as $handle => $spec) {
    $field = $fs->getFieldByHandle($handle);
    if ($field !== null) {
        echo 'field ' . str_pad($handle, 24) . 'exists' . PHP_EOL;
        continue;
    }
    if (!$APPLY) {
        echo 'field ' . str_pad($handle, 24) . 'would create (PlainText, single line)' . PHP_EOL;
        continue;
    }
    $field = new \craft\fields\PlainText();
    $field->name = $spec['name'];
    $field->handle = $handle;
    $field->instructions = $spec['instructions'];
    $field->multiline = false;
    if (!$fs->saveField($field)) {
        echo 'FAILED to create ' . $handle . ': ' . json_encode($field->getErrors()) . PHP_EOL;
        return;
    }
    echo 'field ' . str_pad($handle, 24) . 'created' . PHP_EOL;
}

$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[$c->handle] = true; }

$toAdd = [];
foreach ($TO_LAYOUT as $handle) {
    if (isset($present[$handle])) {
        echo 'layout ' . str_pad($handle, 24) . 'already on ' . $TYPE_HANDLE . PHP_EOL;
        continue;
    }
    $field = $fs->getFieldByHandle($handle);
    if ($field === null) {
        if ($APPLY) {
            echo 'ERROR: field ' . $handle . ' does not exist, cannot add to the layout' . PHP_EOL;
        } else {
            echo 'layout ' . str_pad($handle, 24) . 'would add once the field exists' . PHP_EOL;
        }
        continue;
    }
    $toAdd[] = $field;
    echo 'layout ' . str_pad($handle, 24) . 'would add to ' . $TYPE_HANDLE . PHP_EOL;
}

if ($APPLY && count($toAdd)) {
    $tabs = $layout->getTabs();
    $els = $tabs[0]->getElements();
    foreach ($toAdd as $field) {
        $els[] = new \craft\fieldlayoutelements\CustomField($field);
    }
    $tabs[0]->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    if ($svc->saveEntryType($type)) {
        echo 'layout: added ' . count($toAdd) . ' field(s)' . PHP_EOL;
    } else {
        echo 'FAILED to save entry type: ' . json_encode($type->getErrors()) . PHP_EOL;
    }
}

echo 'Run scripts/import/import_warmemorial.php next; it fills all three.' . PHP_EOL;
