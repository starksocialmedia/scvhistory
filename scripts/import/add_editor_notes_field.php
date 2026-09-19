/**
 * editorNotes: a repeating annotation table, beside the two single notes.
 *
 * webmasterNoteTop and webmasterNoteBottom hold one note each at a fixed
 * position. What a chapter like Winds of Change carries is several separate
 * annotations, each about a different part of the piece, each wanting its own
 * heading and its own links. Today all of that is one 3,143 character string in
 * webmasterNoteBottom.
 *
 *   heading   what the annotation is about. Optional: the extraction does not
 *             record one, so every migrated row starts without it.
 *   note      the annotation. HTML allowed, because these frequently point at
 *             another page and a link has to be able to sit inside the text.
 *   position  top, bottom or inline.
 *
 * The two existing fields are left alone and keep working. See the report for
 * whether migrating them is worth doing; the short answer is that the
 * extraction records position but never a heading, so a migration produces
 * rows that still need a person to title them.
 *
 * Safe to run twice. Dry run by default; set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_editor_notes_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'editorNotes';
$NAME   = 'Editor\'s Notes';
/* Every type that carries a webmaster note today, plus photographs, which is
   where the 1,544 new records live. */
$TYPES = ['article', 'place', 'person', 'organization', 'group', 'event',
          'collection', 'obituary', 'photograph', 'document', 'warMemorial'];
$NEIGHBOURS = ['webmasterNoteBottom', 'webmasterNoteTop', 'body'];

$COLUMNS = [
    'col1' => ['heading' => 'Heading', 'handle' => 'heading', 'type' => 'singleline', 'width' => '22%'],
    'col2' => ['heading' => 'Note', 'handle' => 'note', 'type' => 'multiline', 'width' => '58%'],
    'col3' => ['heading' => 'Position', 'handle' => 'position', 'type' => 'select', 'width' => '20%',
               'options' => [
                   ['label' => 'Top', 'value' => 'top', 'default' => false],
                   ['label' => 'Inline', 'value' => 'inline', 'default' => true],
                   ['label' => 'Bottom', 'value' => 'bottom', 'default' => false],
               ]],
];

$INSTRUCTIONS =
    'Annotations by the archive, as many as the piece needs. One row per note. '
    . 'Heading is what the note is about and may be left empty. Note may contain '
    . 'HTML, so a link can sit inside the text. Position places it above the '
    . 'prose, below it, or inline where the extraction found it. '
    . 'These render as boxes, visibly the archive speaking rather than the author.';

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
if ($field === null) {
    echo $HANDLE . '  would create, Table' . PHP_EOL;
    foreach ($COLUMNS as $col => $def) {
        echo '   ' . str_pad($col, 7) . str_pad($def['handle'], 12) . $def['type'] . PHP_EOL;
    }
    if ($APPLY) {
        $f = new \craft\fields\Table();
        $f->name = $NAME;
        $f->handle = $HANDLE;
        $f->instructions = $INSTRUCTIONS;
        $f->columns = $COLUMNS;
        $f->addRowLabel = 'Add a note';
        $f->defaults = [];
        if (!$fs->saveField($f)) { echo 'FAILED: ' . implode('; ', $f->getFirstErrors()) . PHP_EOL; return; }
        $field = $fs->getFieldByHandle($HANDLE);
        echo 'created' . PHP_EOL;
    }
} else {
    echo $HANDLE . '  exists already, left alone' . PHP_EOL;
}

echo PHP_EOL;
$added = 0; $already = 0;
foreach ($TYPES as $th) {
    $type = $svc->getEntryTypeByHandle($th);
    if (!$type) { echo str_pad($th, 18) . 'no such entry type' . PHP_EOL; continue; }
    $present = [];
    foreach ($type->getFieldLayout()->getCustomFields() as $c) { $present[] = $c->handle; }
    if (in_array($HANDLE, $present, true)) { echo str_pad($th, 18) . 'already carries it' . PHP_EOL; $already++; continue; }
    echo str_pad($th, 18) . 'would add' . PHP_EOL;
    $added++;
    if ($APPLY && $field) {
        $layout = $type->getFieldLayout();
        $tabs = $layout->getTabs();
        $tabIdx = count($tabs) - 1; $at = null;
        foreach ($tabs as $ti => $tab) {
            foreach ($tab->getElements() as $ei => $el) {
                if ($el instanceof \craft\fieldlayoutelements\CustomField
                    && in_array($el->getField()->handle, $NEIGHBOURS, true)) { $tabIdx = $ti; $at = $ei + 1; }
            }
        }
        $els = $tabs[$tabIdx]->getElements();
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($field)]);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        echo str_pad('', 18) . ($svc->saveEntryType($type) ? 'added' : 'FAILED: ' . json_encode($type->getErrors())) . PHP_EOL;
    }
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'types that would gain it: ' . $added . ', already carrying it: ' . $already . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
