/**
 * Adds the two footnote fields to every entry type that carries prose.
 *
 * 229 of our records print a footnote marker. 2 of them carry the note text,
 * 6 can point at a notes record we hold, and 221 are orphans: the marker is in
 * the prose and the note it refers to was never on the page. Grok's survey of
 * the legacy site found the same shape, 347 pages with markers and 331 orphans,
 * because the notes were kept on one shared notes.html per series and most of
 * those files did not survive.
 *
 * So the schema has to hold three states, not one.
 *
 *   footnotes    a table, number and note. Rows only where we hold the text.
 *                A record with no rows is not a record with no footnotes; it is
 *                a record whose notes are somewhere else or nowhere.
 *   footnotesOn  an entries field, one record. Set where the notes for this
 *                piece live on another record, which for this archive means a
 *                series notes page. The marker then links there.
 *
 * Both empty and the markers are orphans, and the template says so in one line
 * under the piece rather than linking to nothing. That is the case for 221 of
 * the 229 and will stay the case unless the note text is recovered.
 *
 * The number column is text, not a number. The legacy pages number notes 1, 2,
 * 3 but also 1a and *, and a numeric column cannot hold either. It also has to
 * match the marker in the prose exactly, since that is what the anchor is
 * built from.
 *
 * Idempotent. It creates nothing that is already there and adds nothing to a
 * layout that already carries it.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_footnotes_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* Every type that holds prose a marker could appear in. Measured, not guessed:
   these are the types among the 229 records that carry one. */
$TYPES = ['article', 'collection', 'document', 'event', 'group', 'obituary',
          'organization', 'page', 'person', 'photograph', 'place',
          'warMemorial', 'militaryProfile'];

/* Put them after the prose, which is where a reader of the control panel will
   look for them. */
$NEIGHBOURS = ['body', 'wmNarrative', 'mpNarrative', 'finePrint'];

$TABLE_HANDLE = 'footnotes';
$TABLE_NAME   = 'Footnotes';
$TABLE_INSTR  =
    'The notes for this piece, where we hold the text. Number must match the '
    . 'marker in the prose exactly, including a letter if the source used one: '
    . 'a marker reading [12a] needs a row numbered 12a. Leave the table empty '
    . 'where the notes live on another record and set "Notes are on" instead, '
    . 'and leave both empty where the note text is not in the archive. An empty '
    . 'table is not a claim that the piece has no notes.';

$COLUMNS = [
    'col1' => ['heading' => 'No.',  'handle' => 'number', 'type' => 'singleline', 'width' => '8%'],
    'col2' => ['heading' => 'Note', 'handle' => 'note',   'type' => 'multiline',  'width' => '92%'],
];

$REL_HANDLE = 'footnotesOn';
$REL_NAME   = 'Notes Are On';
$REL_INSTR  =
    'The record that holds this piece\'s notes, where they were published apart '
    . 'from it. The legacy site kept one notes page for a whole series. Setting '
    . 'this makes every marker in the prose link there. Leave it empty if the '
    . 'notes are on this record, or if they are not in the archive at all.';

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* ------------------------------------------------------------- the fields */

$table = $fs->getFieldByHandle($TABLE_HANDLE);
if ($table === null) {
    echo $TABLE_HANDLE . '  would create, Table, "' . $TABLE_NAME . '"' . PHP_EOL;
    foreach ($COLUMNS as $col => $def) {
        echo '   ' . str_pad($col, 7) . str_pad($def['handle'], 10)
            . str_pad($def['type'], 12) . $def['heading'] . PHP_EOL;
    }
    if ($APPLY) {
        $new = new \craft\fields\Table();
        $new->name = $TABLE_NAME;
        $new->handle = $TABLE_HANDLE;
        $new->instructions = $TABLE_INSTR;
        $new->columns = $COLUMNS;
        $new->addRowLabel = 'Add a note';
        $new->defaults = [];
        if (!$fs->saveField($new)) {
            echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
            return;
        }
        $table = $fs->getFieldByHandle($TABLE_HANDLE);
        echo 'created' . PHP_EOL;
    }
} else {
    echo $TABLE_HANDLE . '  exists already, left alone' . PHP_EOL;
}

$rel = $fs->getFieldByHandle($REL_HANDLE);
if ($rel === null) {
    echo $REL_HANDLE . '  would create, Entries, "' . $REL_NAME . '", limit 1' . PHP_EOL;
    if ($APPLY) {
        $new = new \craft\fields\Entries();
        $new->name = $REL_NAME;
        $new->handle = $REL_HANDLE;
        $new->instructions = $REL_INSTR;
        $new->maxRelations = 1;
        $new->allowSelfRelations = false;
        if (!$fs->saveField($new)) {
            echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
            return;
        }
        $rel = $fs->getFieldByHandle($REL_HANDLE);
        echo 'created' . PHP_EOL;
    }
} else {
    echo $REL_HANDLE . '  exists already, left alone' . PHP_EOL;
}

/* ------------------------------------------------------------ the layouts */

echo PHP_EOL;
$added = 0; $skipped = 0; $missing = [];

foreach ($TYPES as $handle) {
    $type = $svc->getEntryTypeByHandle($handle);
    if (!$type) { $missing[] = $handle; continue; }

    $layout = $type->getFieldLayout();
    $present = [];
    foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

    $want = [];
    if (!in_array($TABLE_HANDLE, $present, true)) { $want[] = [$TABLE_HANDLE, $table]; }
    if (!in_array($REL_HANDLE, $present, true))   { $want[] = [$REL_HANDLE, $rel]; }

    if (!$want) {
        echo str_pad($handle, 18) . 'already carries both' . PHP_EOL;
        $skipped++;
        continue;
    }

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
    echo str_pad($handle, 18) . 'would add ' . implode(' and ', array_column($want, 0))
        . ' to "' . $tabName . '"'
        . ($at === null ? ' at the end' : ' after the prose') . PHP_EOL;
    $added++;

    if ($APPLY) {
        if (array_filter($want, fn($w) => $w[1] === null)) {
            echo '   skipped, the field does not exist yet' . PHP_EOL;
            continue;
        }
        $els = $tabs[$tabIdx]->getElements();
        $insert = array_map(fn($w) => new \craft\fieldlayoutelements\CustomField($w[1]), $want);
        array_splice($els, $at ?? count($els), 0, $insert);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo '   added' . PHP_EOL; }
        else { echo '   FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
    }
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'layouts to change: ' . $added . ', already done: ' . $skipped . PHP_EOL;
if ($missing) { echo 'entry types not found, check the handles: ' . implode(', ', $missing) . PHP_EOL; }
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
echo 'Then run scripts/import/survey_footnotes.php to see the three states.' . PHP_EOL;
