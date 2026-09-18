/**
 * Adds gnisId to the place entry type: the USGS Geographic Names Information
 * System identifier.
 *
 * Canyons, creeks, passes, stations and springs are most of the place list, and
 * GNIS is the authority that actually covers them. Wikidata has an item for
 * Beale's Cut; it does not have one for every unnamed draw in Soledad Canyon,
 * and GNIS does. Once the field holds a value, _partials/head/schema.twig emits
 * it in sameAs as
 *   https://edits.nationalmap.gov/apps/gaz-domestic/public/summary/{id}
 * so a scholar following the graph lands on the federal record rather than on
 * our page about it.
 *
 * Plain text rather than a number: a GNIS feature ID is an opaque identifier,
 * and treating it as a number invites a leading zero to be eaten.
 *
 * Idempotent. It creates nothing that is already there and adds nothing to a
 * layout that already carries it, so a second run reports "exists" and stops.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_gnis_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'gnisId';
$NAME   = 'GNIS ID';
$INSTR  = 'The USGS Geographic Names Information System feature ID, digits only, '
        . 'e.g. 1652814. Find it at https://edits.nationalmap.gov/apps/gaz-domestic/public/search. '
        . 'GNIS is the federal authority for canyons, creeks, passes, springs and '
        . 'settlements, and covers the small features Wikidata does not. Leave empty '
        . 'where no GNIS feature matches; a wrong identifier is worse than none.';
$TYPES  = ['place'];

/* Sit next to the identifier fields already on the layout, so the CP groups
   them the way a cataloguer expects. */
$NEIGHBOURS = ['wikidataId', 'placeChlNumber'];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('-', 70) . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
if ($field === null) {
    echo 'field ' . $HANDLE . ': would create, PlainText, name "' . $NAME . '"' . PHP_EOL;
    echo '  instructions: ' . $INSTR . PHP_EOL;
    if ($APPLY) {
        $f = new \craft\fields\PlainText();
        $f->name = $NAME;
        $f->handle = $HANDLE;
        $f->instructions = $INSTR;
        if (!$fs->saveField($f)) {
            echo '  FAILED: ' . implode('; ', $f->getFirstErrors()) . PHP_EOL;
            return;
        }
        $field = $fs->getFieldByHandle($HANDLE);
        echo '  created' . PHP_EOL;
    }
} else {
    echo 'field ' . $HANDLE . ': exists already, left alone' . PHP_EOL;
}

foreach ($TYPES as $th) {
    $type = $svc->getEntryTypeByHandle($th);
    if (!$type) { echo 'entry type ' . $th . ': NOT FOUND' . PHP_EOL; continue; }

    $layout = $type->getFieldLayout();
    $already = false;
    foreach ($layout->getCustomFields() as $c) { if ($c->handle === $HANDLE) { $already = true; break; } }
    if ($already) { echo 'entry type ' . $th . ': already carries ' . $HANDLE . PHP_EOL; continue; }

    /* Where it would land: after the last identifier field on the layout, or at
       the end of the first tab when none of them is there. */
    $tabs = $layout->getTabs();
    $tabIdx = 0;
    $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && in_array($el->getField()->handle, $NEIGHBOURS, true)) {
                $tabIdx = $ti;
                $at = $ei + 1;
            }
        }
    }
    $tabName = $tabs[$tabIdx]->name ?? ('tab ' . ($tabIdx + 1));
    echo 'entry type ' . $th . ': would add ' . $HANDLE . ' to "' . $tabName . '"'
        . ($at === null ? ' at the end' : ' after ' . implode(' or ', $NEIGHBOURS)) . PHP_EOL;

    if (!$APPLY || !$field) { continue; }

    $els = $tabs[$tabIdx]->getElements();
    $new = new \craft\fieldlayoutelements\CustomField($field);
    if ($at === null || $at > count($els)) { $els[] = $new; }
    else { array_splice($els, $at, 0, [$new]); }
    $tabs[$tabIdx]->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo '  ' . ($svc->saveEntryType($type) ? 'added' : 'FAILED: ' . implode('; ', $type->getFirstErrors())) . PHP_EOL;
}

echo str_repeat('=', 70) . PHP_EOL;
if ($APPLY) {
    echo 'applied. This is a project config change, so it lands in config/project/' . PHP_EOL;
    echo 'and deploys with the branch. Nothing else was touched.' . PHP_EOL;
} else {
    echo 'nothing written.' . PHP_EOL;
    echo 'The schema partial already reads gnisId where the layout carries it, so' . PHP_EOL;
    echo 'sameAs starts emitting the GNIS URL as soon as a record holds a value.' . PHP_EOL;
}
