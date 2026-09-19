/**
 * Adds the graveCensus table field to the place entry type.
 *
 * A cemetery's census is neither prose nor a set of records. The 1992 Ruiz
 * Cemetery census carries 64 numbered plots, of which 41 carry a name; most of
 * those names will never have anything else said about them, and a person
 * record with one fact in it is a record that cannot be written. They belong
 * to the place, in a table, keyed by the map number that ties each line to the
 * cemetery map the Scouts drew.
 *
 * The columns come in pairs where a date is involved, and the pair is the whole
 * point of the field:
 *
 *   birthPrinted / birthIso, deathPrinted / deathIso
 *
 * The printed column holds exactly what the scan says, spaces, stray commas and
 * all. The iso column holds a date only where the printed one can be read
 * without guessing. "Mar. 133, 1928" is not a date; the printed column keeps it
 * and the iso column stays empty. A single column would force a choice between
 * losing the source and inventing a reading.
 *
 * iso is plain text rather than a date column on purpose. Half these graves
 * give a year and nothing else, and a date column cannot hold "1794". The
 * values are ISO 8601 at whatever precision the stone gives: 1794, 1928-03,
 * 1928-03-13.
 *
 * commentsSheet is the census's own last column. The header reads "MILITARY
 * Comments ATTACHED" across two lines and the YES underneath belongs to the
 * comments, not to the military service: plot 01 is an eight-year-old girl and
 * plot 53 an eight-month-old boy, and both are marked YES.
 *
 * Idempotent. It creates nothing that is already there and adds nothing to a
 * layout that already carries it.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_grave_census_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'graveCensus';
$NAME   = 'Grave Census';
$TYPE   = 'place';

/* Sit beside the other table on the layout. */
$NEIGHBOURS = ['recordDates', 'recordDocuments', 'recordImages'];

$INSTRUCTIONS =
    'A transcribed cemetery census, one row per plot, in the order the original '
    . 'gives them. Map # ties the row to the cemetery map. Enter the date columns '
    . 'in pairs: "as printed" is what the source actually says, "parsed" is the '
    . 'ISO date only where it can be read without guessing. Leave parsed empty '
    . 'rather than repairing a damaged reading, and say what is wrong in Note. '
    . 'Precision may be a year (1794), a month (1928-03) or a day (1928-03-13). '
    . 'Comments sheet records whether the original has a separate comments page '
    . 'for that grave.';

$COLUMNS = [
    'col1'  => ['heading' => 'Map #',               'handle' => 'map',           'type' => 'singleline', 'width' => '6%'],
    'col2'  => ['heading' => 'Last name',           'handle' => 'last',          'type' => 'singleline', 'width' => '11%'],
    'col3'  => ['heading' => 'First name',          'handle' => 'first',         'type' => 'singleline', 'width' => '10%'],
    'col4'  => ['heading' => 'Middle',              'handle' => 'middle',        'type' => 'singleline', 'width' => '6%'],
    'col5'  => ['heading' => 'Birth, as printed',   'handle' => 'birthPrinted',  'type' => 'singleline', 'width' => '12%'],
    'col6'  => ['heading' => 'Birth, parsed',       'handle' => 'birthIso',      'type' => 'singleline', 'width' => '9%'],
    'col7'  => ['heading' => 'Death, as printed',   'handle' => 'deathPrinted',  'type' => 'singleline', 'width' => '12%'],
    'col8'  => ['heading' => 'Death, parsed',       'handle' => 'deathIso',      'type' => 'singleline', 'width' => '9%'],
    'col9'  => ['heading' => 'Spouse',              'handle' => 'spouse',        'type' => 'singleline', 'width' => '10%'],
    'col10' => ['heading' => 'Comments sheet',      'handle' => 'commentsSheet', 'type' => 'checkbox',   'width' => '5%'],
    'col11' => ['heading' => 'Note',                'handle' => 'note',          'type' => 'multiline',  'width' => '10%'],
];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }

$field = $fs->getFieldByHandle($HANDLE);

if ($field === null) {
    echo $HANDLE . '  would create, Table, "' . $NAME . '"' . PHP_EOL;
    foreach ($COLUMNS as $col => $def) {
        echo '   ' . str_pad($col, 7) . str_pad($def['handle'], 15)
            . str_pad($def['type'], 12) . $def['heading'] . PHP_EOL;
    }
    if ($APPLY) {
        $new = new \craft\fields\Table();
        $new->name = $NAME;
        $new->handle = $HANDLE;
        $new->instructions = $INSTRUCTIONS;
        $new->columns = $COLUMNS;
        $new->addRowLabel = 'Add a grave';
        $new->defaults = [];
        if (!$fs->saveField($new)) {
            echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
            return;
        }
        $field = $fs->getFieldByHandle($HANDLE);
        echo 'created' . PHP_EOL;
    }
} else {
    echo $HANDLE . '  exists already, left alone' . PHP_EOL;
    $have = [];
    foreach (($field->columns ?? []) as $col => $def) { $have[] = $def['handle'] ?? $col; }
    $want = array_map(fn($d) => $d['handle'], array_values($COLUMNS));
    $missing = array_values(array_diff($want, $have));
    if ($missing) {
        echo '  WARNING: the existing field is missing these columns: '
            . implode(', ', $missing) . PHP_EOL;
        echo '  Add them in the control panel before importing, or the values are dropped.' . PHP_EOL;
    }
}

/* ------------------------------------------------------------- the layout */

echo PHP_EOL;
$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

if (in_array($HANDLE, $present, true)) {
    echo 'the place layout already carries ' . $HANDLE . PHP_EOL;
} elseif ($field === null) {
    echo 'would add ' . $HANDLE . ' to the place layout once the field exists' . PHP_EOL;
} else {
    $tabs = $layout->getTabs();
    $tabIdx = count($tabs) - 1; $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && in_array($el->getField()->handle, $NEIGHBOURS, true)) {
                $tabIdx = $ti; $at = $ei + 1;
            }
        }
    }
    $tabName = $tabs[$tabIdx]->name ?? ('tab ' . ($tabIdx + 1));
    echo 'would add ' . $HANDLE . ' to "' . $tabName . '"'
        . ($at === null ? ' at the end' : ' beside ' . implode(' and ', $NEIGHBOURS)) . PHP_EOL;

    if ($APPLY) {
        $els = $tabs[$tabIdx]->getElements();
        $insert = [new \craft\fieldlayoutelements\CustomField($field)];
        array_splice($els, $at ?? count($els), 0, $insert);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo 'added to the layout' . PHP_EOL; }
        else { echo 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
    }
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
echo 'Then run scripts/import/import_ruiz_census.php.' . PHP_EOL;
