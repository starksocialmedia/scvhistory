/**
 * Adds a source column to the footnotes table, and backfills it to editor.
 *
 * Every footnote has a source and the page should be able to say which. On this
 * archive almost all of them are Leon Worden annotating a piece somebody else
 * wrote: Reynolds wrote chapter 5 in 1976, and the note correcting Juan Jose
 * Fustero's date of death against the county certificate is the editor's, not
 * his. Printing that under a heading reading "Notes" on a piece bylined
 * Reynolds attributes it to Reynolds.
 *
 * The values, and what each one means:
 *
 *   editor     the archive annotating the text. The default, and what every
 *              existing row becomes.
 *   author     the original author's own note, part of the piece as published.
 *              Renders inside the About the Author box, under the bio.
 *   webmaster  a note that arrived in a webmasterNote field and was moved here
 *              by convert_note_footnotes.php.
 *   source     a note that was in the source document itself, a citation the
 *              original printing carried.
 *
 * The backfill is safe and the reason is worth stating rather than assuming.
 * Every row in the table today came from one of two conversions: the [mfn]
 * shortcode blocks, which are Leon's annotations on the previous site, and the
 * webmaster note lists, which are his by definition. Nothing in either was
 * written by the piece's author. So setting all of them to editor is not a
 * guess, it is what they are. Anything later found to be the author's is one
 * cell to change.
 *
 * A row's source only has to be set where it is not editor, because an empty
 * cell reads as editor everywhere it is rendered. That keeps the control panel
 * quiet and means a record imported before this column existed still behaves.
 *
 * Idempotent. The column is added once and rows already carrying a value are
 * left alone.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_footnote_source_column.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE  = 'footnotes';
$COLUMN  = 'source';
$DEFAULT = 'editor';

$OPTIONS = [
    ['label' => 'Editor (Leon Worden)', 'value' => 'editor',    'default' => true],
    ['label' => 'Original author',      'value' => 'author',    'default' => false],
    ['label' => 'Webmaster note',       'value' => 'webmaster', 'default' => false],
    ['label' => 'In the source document', 'value' => 'source',  'default' => false],
];

$fs = Craft::$app->getFields();
$elements = Craft::$app->getElements();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
if (!$field) { echo 'field ' . $HANDLE . ' NOT FOUND. Run add_footnotes_fields.php first.' . PHP_EOL; return; }

$columns = $field->columns ?? [];
$have = [];
foreach ($columns as $k => $c) { $have[$c['handle'] ?? $k] = $k; }

if (isset($have[$COLUMN])) {
    echo 'the ' . $COLUMN . ' column exists already, left alone' . PHP_EOL;
} else {
    /* After number, before note, because it qualifies the note rather than
       following it, and a reader of the control panel should see whose it is
       before they read it. */
    $next = 'col' . (count($columns) + 1);
    echo 'would add column ' . $next . ' "' . $COLUMN . '", select, '
       . count($OPTIONS) . ' options, default ' . $DEFAULT . PHP_EOL;
    foreach ($OPTIONS as $o) { echo '   ' . str_pad($o['value'], 12) . $o['label'] . PHP_EOL; }

    if ($APPLY) {
        $columns[$next] = [
            'heading' => 'Source',
            'handle'  => $COLUMN,
            'width'   => '16%',
            'type'    => 'select',
            'options' => $OPTIONS,
        ];
        $field->columns = $columns;
        if (!$fs->saveField($field)) {
            echo 'FAILED: ' . implode('; ', $field->getFirstErrors()) . PHP_EOL;
            return;
        }
        echo 'column added' . PHP_EOL;
    }
}

/* ---------------------------------------------------------- the backfill */

/* WHY THIS WRITES col3 AND NOT source.
 *
 * A Table field stores its rows keyed by column, col1, col2, col3. The element
 * hands them back keyed BOTH ways, so a row read from Craft looks like
 *
 *   {"col1":"1","col2":"the note","col3":null,
 *    "number":"1","note":"the note","source":null}
 *
 * Setting only the handle and saving looks like it works. The element reports
 * success, the script prints "saved: 5", and nothing persists, because Craft's
 * normalisation prefers the column key and col3 is still null. The run before
 * this one wrote the handle five times and the database kept five nulls.
 *
 * So rows are reduced to column keys before they go back, and the write is read
 * back from the database afterwards and counted. */
$COLKEY = null;
foreach (($field->columns ?? []) as $k => $c) {
    if (($c['handle'] ?? null) === $COLUMN) { $COLKEY = $k; }
}
if ($COLKEY === null && !$APPLY) { $COLKEY = 'col' . (count($field->columns ?? []) + 1); }
if ($COLKEY === null) { echo 'could not find the column key for ' . $COLUMN . PHP_EOL; return; }
echo 'the ' . $COLUMN . ' column is stored as ' . $COLKEY . PHP_EOL;

/* Column keys only. Handle keys in the same row are what caused the silent
   discard, so they are dropped rather than sent alongside. */
$rowToCols = function (array $r) use ($field): array {
    $out = [];
    foreach (($field->columns ?? []) as $k => $c) {
        $h = $c['handle'] ?? null;
        $out[$k] = $r[$k] ?? ($h !== null ? ($r[$h] ?? null) : null);
    }
    return $out;
};

echo PHP_EOL;
$records = 0; $rowsSeen = 0; $rowsToSet = 0; $already = 0;
$plan = [];

foreach (\craft\elements\Entry::find()->limit(null)->status(null)->all() as $e) {
    $layout = $e->getFieldLayout();
    if (!$layout) { continue; }
    $has = false;
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $HANDLE) { $has = true; } }
    if (!$has) { continue; }

    $rows = $e->getFieldValue($HANDLE);
    if (!is_array($rows) || !count($rows)) { continue; }
    $records++;

    $new = []; $changed = false;
    foreach ($rows as $r) {
        $rowsSeen++;
        /* Read either key. A Table row comes back from the element carrying
           both, col3 and source, for the same cell. */
        $val = trim((string)($r[$COLKEY] ?? $r[$COLUMN] ?? ''));
        if ($val !== '') { $already++; $new[] = $rowToCols($r); continue; }
        $r[$COLUMN] = $DEFAULT;
        $r[$COLKEY]  = $DEFAULT;
        $new[] = $rowToCols($r);
        $rowsToSet++;
        $changed = true;
    }
    if ($changed) { $plan[] = ['e' => $e, 'rows' => $new]; }
}

echo 'records carrying footnotes: ' . $records . PHP_EOL;
echo 'rows seen: ' . $rowsSeen . PHP_EOL;
echo 'rows already carrying a source: ' . $already . PHP_EOL;
echo 'rows to set to ' . $DEFAULT . ': ' . $rowsToSet . PHP_EOL;
foreach ($plan as $p) { echo '   ' . $p['e']->slug . '  ' . count($p['rows']) . ' rows' . PHP_EOL; }

$saved = 0; $failed = [];
if ($APPLY && $plan) {
    foreach ($plan as $p) {
        $p['e']->setFieldValue($HANDLE, $p['rows']);
        if ($elements->saveElement($p['e'])) { $saved++; }
        else { $failed[] = $p['e']->slug . ': ' . json_encode($p['e']->getErrors()); }
    }
    echo PHP_EOL . 'saved: ' . $saved . PHP_EOL;
    foreach ($failed as $f) { echo '  FAILED ' . $f . PHP_EOL; }

    /* ------------------------------------------------- read the writes back */

    /* A save that reports success and changes nothing is worse than a save that
       fails, because the counter says the work is done. Every row is read back
       from a freshly loaded element and counted. */
    $back = 0; $short = [];
    foreach ($plan as $p) {
        $fresh = \craft\elements\Entry::find()->id($p['e']->id)->status(null)->one();
        if (!$fresh) { $short[] = $p['e']->slug . ': gone after save'; continue; }
        $got = $fresh->getFieldValue($HANDLE);
        $set = 0;
        foreach ((is_array($got) ? $got : []) as $r) {
            if (trim((string)($r[$COLKEY] ?? $r[$COLUMN] ?? '')) !== '') { $set++; }
        }
        $want = count($p['rows']);
        if ($set < $want) { $short[] = $p['e']->slug . ': wrote ' . $want . ', read back ' . $set; }
        $back += $set;
    }
    echo 'read back: ' . $back . ' of ' . $rowsSeen . ' rows carry a ' . $COLUMN . PHP_EOL;
    if ($short) {
        echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
        foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }
        echo 'Nothing further has been done. Do not re-run until this is understood.' . PHP_EOL;
        return;
    }
    echo 'verified: every row read back carries its value.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
echo 'The templates read an empty source as editor, so they are correct either way.' . PHP_EOL;
