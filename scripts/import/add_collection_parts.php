/**
 * Creates the collectionParts table field, adds it to the collection entry type,
 * and populates the four parts of History of the Santa Clarita Valley.
 *
 * A major collection groups its Contents by these parts, always in
 * articlesInCollection order. A collection with no parts renders one ungrouped
 * list in sequence order, so this field is optional everywhere.
 *
 * Columns: label, note, start. "start" is the 1-based position in
 * articlesInCollection where the part begins; a part runs until the next one
 * starts. Positions, not article ids, so re-ordering the collection does not
 * silently break the grouping.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_collection_parts.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'collectionParts';
$SLUG = 'history-of-the-santa-clarita-valley';

/* Parts by sequence position in articlesInCollection.
   1-7   Preface, Prologue, Chapters 1 to 5
   8-15  Chapters 6 to 13
   16-20 Chapters 14 to 18
   21-23 Chapters 19 to 21 */
$ROWS = [
    ['label' => "PART ONE \u{00B7} BEFORE THE SPANISH",   'note' => 'Preface to Chapter 5', 'start' => 1],
    ['label' => "PART TWO \u{00B7} MISSION AND RANCHO",   'note' => "Chapters 6\u{2013}13",  'start' => 8],
    ['label' => "PART THREE \u{00B7} THE AMERICAN RANCHO", 'note' => "Chapters 14\u{2013}18", 'start' => 16],
    ['label' => "PART FOUR \u{00B7} A VALLEY DISCOVERED",  'note' => "Chapters 19\u{2013}21", 'start' => 21],
];

$COLUMNS = [
    'col1' => ['heading' => 'Part label', 'handle' => 'label', 'width' => '', 'type' => 'singleline'],
    'col2' => ['heading' => 'Note',       'handle' => 'note',  'width' => '', 'type' => 'singleline'],
    'col3' => ['heading' => 'Starts at #', 'handle' => 'start', 'width' => '', 'type' => 'number'],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$fs = Craft::$app->getFields();
$field = $fs->getFieldByHandle($HANDLE);

if ($field === null) {
    echo 'field ' . $HANDLE . ': would create (Table, 3 columns)' . PHP_EOL;
    if ($APPLY) {
        $field = new \craft\fields\Table();
        $field->name = 'Parts';
        $field->handle = $HANDLE;
        $field->instructions = 'Optional. Groups the Contents of a major collection. "Starts at #" is the position in Articles in Collection where the part begins; a part runs until the next one starts. Leave empty for one ungrouped list.';
        $field->columns = $COLUMNS;
        $field->defaults = [];
        $field->addRowLabel = 'Add a part';
        if (!$fs->saveField($field)) {
            echo 'FAILED to create field: ' . json_encode($field->getErrors()) . PHP_EOL;
            return;
        }
        $field = $fs->getFieldByHandle($HANDLE);
        echo 'field ' . $HANDLE . ': created' . PHP_EOL;
    }
} else {
    echo 'field ' . $HANDLE . ': exists' . PHP_EOL;
}

$svc = Craft::$app->getEntries();
$type = $svc->getEntryTypeByHandle('collection');
if (!$type) {
    echo 'ERROR: collection entry type not found' . PHP_EOL;
    return;
}

$layout = $type->getFieldLayout();
$inLayout = false;
foreach ($layout->getCustomFields() as $c) {
    if ($c->handle === $HANDLE) { $inLayout = true; }
}

if ($inLayout) {
    echo 'layout: collection already has it' . PHP_EOL;
} elseif (!$APPLY) {
    echo 'layout: would add ' . $HANDLE . ' to the collection entry type' . PHP_EOL;
} else {
    $tabs = $layout->getTabs();
    $els = $tabs[0]->getElements();
    $els[] = new \craft\fieldlayoutelements\CustomField($field);
    $tabs[0]->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo 'layout: ' . ($svc->saveEntryType($type) ? 'added' : 'FAILED ' . json_encode($type->getErrors())) . PHP_EOL;
}

$entry = \craft\elements\Entry::find()->section('collections')->slug($SLUG)->status(null)->one();
if (!$entry) {
    echo 'ERROR: collection ' . $SLUG . ' not found, no rows written' . PHP_EOL;
    return;
}

$total = $entry->articlesInCollection->count();
echo 'collection #' . $entry->id . ' "' . $entry->title . '" holds ' . $total . ' articles' . PHP_EOL;

$existing = [];
if ($inLayout) {
    try { $existing = $entry->getFieldValue($HANDLE) ?: []; }
    catch (\Throwable $e) { $existing = []; }
}

foreach ($ROWS as $i => $r) {
    $end = $ROWS[$i + 1]['start'] ?? ($total + 1);
    echo sprintf('  %-38s %-22s positions %d to %d', $r['label'], $r['note'], $r['start'], $end - 1) . PHP_EOL;
    if ($r['start'] > $total) {
        echo '    WARNING: start is past the end of the collection' . PHP_EOL;
    }
}

if (count($existing)) {
    echo 'rows: collection already has ' . count($existing) . ', left alone' . PHP_EOL;
} elseif (!$APPLY) {
    echo 'rows: would write ' . count($ROWS) . PHP_EOL;
} else {
    try { $entry->setFieldValue($HANDLE, $ROWS); }
    catch (\Throwable $e) { echo 'FAILED to set rows: ' . $e->getMessage() . PHP_EOL; return; }
    if (Craft::$app->getElements()->saveElement($entry)) {
        echo 'rows: wrote ' . count($ROWS) . PHP_EOL;
    } else {
        echo 'FAILED to save collection: ' . json_encode($entry->getErrors()) . PHP_EOL;
    }
}

echo 'A collection with no parts renders one ungrouped list, so this is safe to leave empty elsewhere.' . PHP_EOL;
