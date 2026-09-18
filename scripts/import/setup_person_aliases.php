/**
 * Creates the personAliases field and puts it on the person entry type.
 *
 * The entity reconciliation workflow expects it: export_entity_candidates.php,
 * web/review/entities.html and apply_entity_merges.php all map persons to
 * personAliases already. Until the field exists the screen warns that a person
 * merge would lose the other title, and the apply script reports the field as
 * missing and skips the alias write. Both start working the moment this runs.
 *
 * Multiline plain text, one name per line, to match how the apply script joins
 * names for this field.
 *
 * Safe to run twice: an existing field or layout entry is left alone.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/setup_person_aliases.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'personAliases';

$fields  = Craft::$app->getFields();
$entries = Craft::$app->getEntries();

echo ($APPLY ? '=== APPLYING ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL;

$field = $fields->getFieldByHandle($HANDLE);
if ($field) {
    echo 'field `' . $HANDLE . '` already exists' . PHP_EOL;
} else {
    echo 'would create field `' . $HANDLE . '`, multiline plain text' . PHP_EOL;
    if ($APPLY) {
        $field = new \craft\fields\PlainText();
        $field->name = 'Also Known As';
        $field->handle = $HANDLE;
        $field->multiline = true;
        $field->initialRows = 3;
        $field->instructions = 'Other names this person appears under, one per line. Merging two records appends the losing title here.';
        if (!$fields->saveField($field)) {
            echo 'FAILED to save field: ' . json_encode($field->getErrors()) . PHP_EOL;
            return;
        }
        echo 'created field `' . $HANDLE . '`' . PHP_EOL;
    }
}

$type = $entries->getEntryTypeByHandle('person');
if (!$type) { echo 'ERROR: entry type `person` not found' . PHP_EOL; return; }

$layout = $type->getFieldLayout();
$on = [];
foreach ($layout->getCustomFields() as $f) { $on[] = $f->handle; }

if (in_array($HANDLE, $on, true)) {
    echo 'person layout already carries ' . $HANDLE . PHP_EOL;
} else {
    echo 'would add ' . $HANDLE . ' to the person Content tab' . PHP_EOL;
    if ($APPLY) {
        if (!$field) { echo 'field missing, cannot add to the layout' . PHP_EOL; return; }
        try {
            $tabs = $layout->getTabs();
            $tab = null;
            foreach ($tabs as $t) { if (strtolower($t->name) === 'content') { $tab = $t; break; } }
            if ($tab === null) { $tab = $tabs[0] ?? null; }
            if ($tab === null) {
                $tab = new \craft\models\FieldLayoutTab(['layout' => $layout, 'name' => 'Content', 'sortOrder' => 1]);
                $tab->setElements([]);
                $tabs[] = $tab;
            }
            $els = $tab->getElements();
            $els[] = new \craft\fieldlayoutelements\CustomField($field);
            $tab->setElements($els);
            $layout->setTabs($tabs);
            $type->setFieldLayout($layout);
            if (!$entries->saveEntryType($type)) {
                echo 'FAILED: ' . json_encode($type->getErrors()) . PHP_EOL;
                return;
            }
            echo 'added ' . $HANDLE . ' to the person layout' . PHP_EOL;
        } catch (\Throwable $e) {
            echo 'FAILED: ' . $e->getMessage() . PHP_EOL;
            return;
        }
    }
}

if ($APPLY) {
    echo PHP_EOL . 'Now run project-config/write, commit config/project/, then re-run' . PHP_EOL;
    echo 'export_entity_candidates.php so the review screen picks the field up.' . PHP_EOL;
} else {
    echo PHP_EOL . 'Nothing was written. Set $APPLY = true and run again.' . PHP_EOL;
}
