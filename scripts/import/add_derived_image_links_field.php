/**
 * Adds derivedImageLinks, the field that keeps derived relations apart from
 * curated ones.
 *
 * On the legacy site a thumbnail often opened a different page rather than a
 * larger scan. 14,023 of those resolve to a record we hold, and relating them
 * is right: it is the only navigation the legacy page offered between two
 * records and it should not be thrown away.
 *
 * What is not right is putting them where the curated review puts its
 * decisions. link_images_to_records.php was written to fill relatedArticles and
 * relatedPlaces, which is exactly where apply_article_links.php and
 * apply_place_links.php write, and photoPeople, photoPlaces and
 * photoOrganizations, which _partials/head/schema.twig publishes as JSON-LD. A
 * relation somebody sat and confirmed and a relation inferred from an href
 * would have been indistinguishable in the control panel, in the graph and in
 * the structured data, and there would have been no way back.
 *
 * So they get their own field, and the field is the provenance: anything in
 * derivedImageLinks came from a legacy image link and nothing else writes to
 * it. No extra column, no parallel table, no convention to remember.
 *
 * There is no migration. link_images_to_records.php has never been applied, so
 * there is no mixed data to separate. This only has to exist before it runs.
 *
 * Idempotent. It creates nothing that is already there and adds nothing to a
 * layout that already carries it.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_derived_image_links_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'derivedImageLinks';
$NAME   = 'Derived Image Links';
$INSTR  =
    'Records this one pointed at through an image on the legacy page, resolved '
    . 'from the image\'s link target. Written by scripts/import/'
    . 'link_images_to_records.php and not by hand. These are derived, not '
    . 'curated: the archive has not confirmed the relationship, only that the '
    . 'old page linked one to the other. Anything you confirm belongs in the '
    . 'curated relation field for its type, and can then be removed from here.';

/* Every type that carries a legacyKey and can therefore be a source or a
   target. Measured against the layouts rather than assumed. */
$TYPES = ['article', 'collection', 'document', 'event', 'group', 'obituary',
          'organization', 'page', 'person', 'photograph', 'place',
          'warMemorial', 'militaryProfile'];

/* Sit at the end of the relations, after the curated fields, so the control
   panel reads curated first and derived second. */
$NEIGHBOURS = ['relatedPhotographs', 'relatedArticles', 'relatedPlaces', 'relatedPersons',
               'photoArticles', 'photoGroups', 'placeArticles', 'orgEvents'];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
if ($field === null) {
    echo $HANDLE . '  would create, Entries, "' . $NAME . '", no limit' . PHP_EOL;
    if ($APPLY) {
        $new = new \craft\fields\Entries();
        $new->name = $NAME;
        $new->handle = $HANDLE;
        $new->instructions = $INSTR;
        $new->allowSelfRelations = false;
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

echo PHP_EOL;
$added = 0; $skipped = 0; $missing = [];

foreach ($TYPES as $handle) {
    $type = $svc->getEntryTypeByHandle($handle);
    if (!$type) { $missing[] = $handle; continue; }

    $layout = $type->getFieldLayout();
    $present = [];
    foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
    if (in_array($HANDLE, $present, true)) {
        echo str_pad($handle, 18) . 'already carries it' . PHP_EOL;
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
    echo str_pad($handle, 18) . 'would add to "' . $tabName . '"'
        . ($at === null ? ' at the end' : ' after the curated relations') . PHP_EOL;
    $added++;

    if ($APPLY && $field !== null) {
        $els = $tabs[$tabIdx]->getElements();
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($field)]);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo '   added' . PHP_EOL; }
        else { echo '   FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
    }
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'layouts to change: ' . $added . ', already done: ' . $skipped . PHP_EOL;
if ($missing) { echo 'entry types not found: ' . implode(', ', $missing) . PHP_EOL; }
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
echo 'Then run scripts/import/link_images_to_records.php.' . PHP_EOL;
