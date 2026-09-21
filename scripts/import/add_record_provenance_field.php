/**
 * Adds recordProvenance to person, place and organization.
 *
 * Records are about to start arriving in batches. The missing-records screen
 * creates fifty at a time from names the corpus uses, and a record made that
 * way is a different thing from one Leon wrote: it is a machine's reading of a
 * name in an article, approved quickly, and some proportion of any batch will
 * be wrong. "Post Office Box" was in the top fifty by article count.
 *
 * So each one has to say where it came from. The value has to be queryable,
 * because "everything one batch created on one afternoon" is exactly the set
 * somebody will want to inspect, audit or undo, and finding that set by reading
 * prose in a notes field is not finding it.
 *
 * WHY A NEW FIELD RATHER THAN AN EXISTING ONE
 *
 * webmasterNoteTop and editorNotes both print on the page. Provenance is
 * housekeeping; a reader looking up Sanford Lyon should not be told which
 * script made the entry. sourcePath, legacyUrl and legacyCategory already mean
 * something precise, which is where the thing came from on the legacy site, and
 * a record created from a name in an article has no legacy page. Overloading
 * one of them would cost it the meaning it has and make "is this from the old
 * site" unanswerable.
 *
 * NOT RENDERED
 *
 * The field is added to the layouts and to nothing else. No template reads it.
 * If it ever needs to be shown it will be a decision taken then, not a default
 * inherited from today.
 *
 * Values it will carry: "created from review, 2026-09-21" from
 * create_records_from_review.php. Records made by hand leave it empty, and
 * empty means a person made it, which is the right default for an archive
 * that was hand-built for thirty years.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_record_provenance_field.php'))"
 */

$APPLY = false;

$HANDLE = 'recordProvenance';
$NAME   = 'Record Provenance';
$TYPES  = ['person', 'place', 'organization'];

/* Put it beside the other housekeeping rather than in with the content, so an
   editor opening a record does not meet it before the name. */
$NEIGHBOURS = ['legacyCategory', 'sourcePath', 'legacyUrl', 'legacyKey'];

$INSTR =
    'Where this record came from, when it was not made by hand. Scripts write a '
    . 'phrase and a date here, such as "created from review, 2026-09-21", so that '
    . 'everything one batch created can be found again and inspected or undone. '
    . 'Empty means a person made it. Nothing on the site renders this field.';

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* ------------------------------------------------------------- the field */

$field = $fs->getFieldByHandle($HANDLE);
if ($field === null) {
    echo $HANDLE . '  would create, PlainText, "' . $NAME . '"' . PHP_EOL;
    if ($APPLY) {
        $new = new \craft\fields\PlainText();
        $new->name = $NAME;
        $new->handle = $HANDLE;
        $new->instructions = $INSTR;
        $new->multiline = false;
        $new->charLimit = 255;
        $new->searchable = true;
        if (!$fs->saveField($new)) {
            echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
            return;
        }
        $field = $fs->getFieldByHandle($HANDLE);
        echo 'created' . PHP_EOL;
    }
} else {
    echo $HANDLE . '  exists already, left alone' . PHP_EOL;
}

/* ----------------------------------------------------------- the layouts */

echo PHP_EOL;
foreach ($TYPES as $handle) {
    $type = $svc->getEntryTypeByHandle($handle);
    if (!$type) { echo str_pad($handle, 16) . 'ENTRY TYPE NOT FOUND' . PHP_EOL; continue; }

    $layout = $type->getFieldLayout();
    $present = [];
    foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

    if (in_array($HANDLE, $present, true)) {
        echo str_pad($handle, 16) . 'already carries ' . $HANDLE . PHP_EOL;
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
    echo str_pad($handle, 16) . 'would add to "' . $tabName . '"'
       . ($at === null ? ', at the end' : ', after the legacy fields') . PHP_EOL;

    if ($APPLY && $field !== null) {
        $els = $tabs[$tabIdx]->getElements();
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($field)]);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo str_pad('', 16) . 'added' . PHP_EOL; }
        else { echo str_pad('', 16) . 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
    }
}

/* ---------------------------------------------------------- the read-back */

if ($APPLY) {
    echo PHP_EOL . 'read-back:' . PHP_EOL;
    foreach ($TYPES as $handle) {
        $section = ['person' => 'persons', 'place' => 'places', 'organization' => 'organizations'][$handle];
        $e = \craft\elements\Entry::find()->section($section)->status(null)->one();
        if (!$e) { echo str_pad($handle, 16) . 'no entry to check against' . PHP_EOL; continue; }
        $ok = false;
        foreach ($e->getFieldLayout()->getCustomFields() as $f) {
            if ($f->handle === $HANDLE) { $ok = true; break; }
        }
        echo str_pad($handle, 16) . ($ok ? 'present on a live entry' : 'NOT PRESENT, the save did not take') . PHP_EOL;
    }
    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $applyLog('add_record_provenance_field.php', count($TYPES), 'field present on a live entry', 'schema only');
    echo PHP_EOL . 'config/project will be dirty. Commit it before deploying: see docs/DEPLOY.md step 1.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
