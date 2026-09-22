/**
 * Adds collectionFrozen, a lightswitch on the collection entry type that says
 * "the scripts are finished with this collection".
 *
 * The quality phase has two kinds of fix. Importer fixes are re-runnable: a
 * script cleans every article in a pass, and running it again gives the same
 * result. Editorial fixes are made by a person in the control panel, one
 * record at a time. The two can't share a collection, because a re-run of any
 * importer or rebuild script writes the field again from the source and the
 * hand edit is lost without anyone being told.
 *
 * So the order is: run the importer fixes, freeze the collection, then edit
 * it by hand. Once frozen, a collection belongs to the control panel.
 *
 * The refusal itself isn't in this script. It lives in modules/collectionfreeze,
 * which stops any console save or delete of:
 *   - a collection entry whose collectionFrozen is on
 *   - an entry whose partOfCollection points at one
 *   - an entry listed in a frozen collection's articlesInCollection
 * Every script under scripts/import writes through ddev craft exec, which is a
 * console request, so one guard there covers them all. Control panel saves are
 * web requests and are not touched.
 *
 * Unfreezing is always allowed, from the control panel or the console:
 * scripts/import/set_collection_frozen.php does it and reads the switch back.
 *
 * This creates the field, off by default, and freezes nothing.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_collection_frozen_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'collectionFrozen';
$NAME   = 'Frozen';
$TYPE   = 'collection';
$NEIGHBOURS = ['collectionKind', 'collectionIsMajor'];

$INSTR =
    'On means the import and rebuild scripts are finished with this collection. '
    . 'They will refuse to write to it, or to any piece in it, so edits made here '
    . 'by hand are not overwritten by a re-run. Turn it off only to run an '
    . 'importer fix again, and expect that run to replace hand edits in the '
    . 'fields it writes.';

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
$created = 0;
if ($field === null) {
    echo $HANDLE . '  would create, Lightswitch, "' . $NAME . '", default off' . PHP_EOL;
    if ($APPLY) {
        $new = new \craft\fields\Lightswitch();
        $new->name = $NAME;
        $new->handle = $HANDLE;
        $new->instructions = $INSTR;
        $new->default = false;
        $new->onLabel = 'Frozen';
        $new->offLabel = 'Open to scripts';
        if (!$fs->saveField($new)) {
            echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
            return;
        }
        $field = $fs->getFieldByHandle($HANDLE);
        $created = 1;
        echo 'created' . PHP_EOL;
    }
} else {
    echo $HANDLE . '  exists already, ' . get_class($field) . ', left alone' . PHP_EOL;
}

echo PHP_EOL;
$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }

$layout = $type->getFieldLayout();
$present = array_map(fn($c) => $c->handle, $layout->getCustomFields());

if (in_array($HANDLE, $present, true)) {
    echo 'the collection layout already carries ' . $HANDLE . PHP_EOL;
} else {
    $tabs = $layout->getTabs();
    $tabIdx = 0; $at = null;
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
       . ($at === null ? ' at the end' : ' after collectionKind') . PHP_EOL;

    if ($APPLY && $field !== null) {
        $els = $tabs[$tabIdx]->getElements();
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($field)]);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo 'added to the layout' . PHP_EOL; }
        else { echo 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; return; }
    }
}

/* What the guard will see. Membership is counted the same two ways the guard
   checks it, so a collection whose two lists disagree shows up here first. */
echo PHP_EOL . 'Collections, and what freezing each would lock:' . PHP_EOL;
$collections = \craft\elements\Entry::find()->section('collections')->status(null)->limit(null)->all();
foreach ($collections as $c) {
    $partOf = \craft\elements\Entry::find()
        ->relatedTo(['targetElement' => $c, 'field' => 'partOfCollection'])
        ->status(null)->limit(null)->ids();
    $listed = $c->articlesInCollection->status(null)->limit(null)->ids();
    $all = array_unique(array_merge($partOf, $listed));
    printf("   #%-6d %-38s %4d pieces  (partOf %d, listed %d)\n",
        $c->id, $c->slug, count($all), count($partOf), count($listed));
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

/* Read-back, fresh from the services rather than the objects above. */
Craft::$app->getFields()->refreshFields();
$f2 = Craft::$app->getFields()->getFieldByHandle($HANDLE);
$t2 = Craft::$app->getEntries()->getEntryTypeByHandle($TYPE);
$inLayout = $t2 && in_array($HANDLE, array_map(fn($c) => $c->handle, $t2->getFieldLayout()->getCustomFields()), true);
$frozenNow = \craft\elements\Entry::find()->section('collections')->status(null)
    ->collectionFrozen(true)->count();

$ok = $f2 instanceof \craft\fields\Lightswitch && $inLayout && $frozenNow === 0;
echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'READ-BACK ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
echo '   field ' . ($f2 ? get_class($f2) : 'MISSING') . PHP_EOL;
echo '   in collection layout ' . ($inLayout ? 'yes' : 'NO') . PHP_EOL;
echo '   collections frozen ' . $frozenNow . ' (expected 0)' . PHP_EOL;
if (!$ok) {
    throw new \RuntimeException('add_collection_frozen_field read-back failed');
}

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_collection_frozen_field.php', $created,
    'verified: lightswitch, in layout, 0 frozen',
    'schema only; guard in modules/collectionfreeze');
