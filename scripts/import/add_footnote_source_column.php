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
        $val = trim((string)($r['source'] ?? ''));
        if ($val !== '') { $already++; $new[] = $r; continue; }
        $r['source'] = $DEFAULT;
        $new[] = $r;
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
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
echo 'The templates read an empty source as editor, so they are correct either way.' . PHP_EOL;
