/**
 * Adds missing wm* fields to the warMemorial entry type and puts them on a
 * "Service Record" tab. Safe to run twice: existing fields are skipped.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_war_memorial_fields.php'))"
 */

$fieldsService = Craft::$app->getFields();
$entriesService = Craft::$app->getEntries();

$defs = [
    'wmDateOfBirth'     => ['Date of Birth', false],
    'wmHighSchool'      => ['High School', false],
    'wmServiceId'       => ['Service ID', false],
    'wmSpecialty'       => ['Specialty / MOS', false],
    'wmLengthOfService' => ['Length of Service', false],
    'wmStartTour'       => ['Start of Tour', false],
    'wmBase'            => ['Based', false],
    'wmCombatOperations'=> ['Combat Operations', false],
    'wmIncidentDate'    => ['Incident Date', false],
    'wmIncidentLocation'=> ['Incident Location', false],
    'wmAgeAtLoss'       => ['Age at Loss', false],
    'wmAwards'          => ['Awards', true],
    'wmNarrative'       => ['Narrative', true],
];

$created = [];
$existing = [];

foreach ($defs as $handle => [$name, $multiline]) {
    $field = $fieldsService->getFieldByHandle($handle);
    if ($field) {
        $existing[] = $handle;
        continue;
    }
    $field = new \craft\fields\PlainText();
    $field->name = $name;
    $field->handle = $handle;
    $field->multiline = $multiline;
    if ($multiline) {
        $field->initialRows = 6;
    }
    if (!$fieldsService->saveField($field)) {
        echo 'FAILED to save field ' . $handle . ': ' . json_encode($field->getErrors()) . PHP_EOL;
        continue;
    }
    $created[] = $handle;
}

echo 'Fields created: ' . (count($created) ? implode(', ', $created) : 'none') . PHP_EOL;
echo 'Fields already present: ' . (count($existing) ? implode(', ', $existing) : 'none') . PHP_EOL;

$entryType = $entriesService->getEntryTypeByHandle('warMemorial');
if (!$entryType) {
    echo 'ERROR: entry type warMemorial not found' . PHP_EOL;
    return;
}

$layout = $entryType->getFieldLayout();
$onLayout = [];
foreach ($layout->getCustomFields() as $f) {
    $onLayout[] = $f->handle;
}

$toAdd = [];
foreach (array_keys($defs) as $handle) {
    if (in_array($handle, $onLayout, true)) {
        continue;
    }
    $field = $fieldsService->getFieldByHandle($handle);
    if ($field) {
        $toAdd[] = $field;
    }
}

if (!count($toAdd)) {
    echo 'Layout already has every field. Nothing to change.' . PHP_EOL;
    return;
}

$tabs = $layout->getTabs();
$targetTab = null;
foreach ($tabs as $tab) {
    if ($tab->name === 'Service Record') {
        $targetTab = $tab;
        break;
    }
}

if ($targetTab === null) {
    $targetTab = new \craft\models\FieldLayoutTab([
        'layout' => $layout,
        'name' => 'Service Record',
        'sortOrder' => count($tabs) + 1,
    ]);
    $targetTab->setElements([]);
    $tabs[] = $targetTab;
}

$elements = $targetTab->getElements();
foreach ($toAdd as $field) {
    $elements[] = new \craft\fieldlayoutelements\CustomField($field);
}
$targetTab->setElements($elements);

$layout->setTabs($tabs);
$entryType->setFieldLayout($layout);

if (!$entriesService->saveEntryType($entryType)) {
    echo 'FAILED to save entry type: ' . json_encode($entryType->getErrors()) . PHP_EOL;
    return;
}

echo 'Added to layout: ' . implode(', ', array_map(fn($f) => $f->handle, $toAdd)) . PHP_EOL;

$check = $entriesService->getEntryTypeByHandle('warMemorial');
$after = [];
foreach ($check->getFieldLayout()->getCustomFields() as $f) {
    $after[] = $f->handle;
}
echo 'warMemorial layout now has ' . count($after) . ' fields: ' . implode(', ', $after) . PHP_EOL;
