/**
 * Adds three landmark identifier fields to the place entry type.
 *
 *   scvhlNumber     Santa Clarita Historic Landmark designation number
 *   nrhpReference   National Register of Historic Places reference number
 *   nrhpListedDate  the date it was listed on the National Register
 *
 * The place type already carries placeChlNumber for the California Historical
 * Landmark and a placeScvhlCheckbox lightswitch that says a place is an SCV
 * landmark without saying which one. The checkbox answers "is it", the number
 * answers "which", and a reader chasing a designation needs the number. These
 * three sit beside placeChlNumber and gnisId so the identifiers are together.
 *
 * All three are plain text. A designation number is an opaque identifier, not a
 * quantity, and a number field would eat a leading zero. nrhpListedDate is
 * plain text for the same reason the other dates here are: the source says
 * "listed 1971" as often as it gives a day, and a date field would refuse both.
 *
 * Idempotent. It creates nothing that is already there and adds nothing to a
 * layout that already carries it.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_place_landmark_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$DEFS = [
    'scvhlNumber' => [
        'SCVHL Number',
        'Santa Clarita Historic Landmark designation number, digits only, e.g. 12. '
        . 'The Place SCV Landmark switch says a place is one; this says which. Leave empty '
        . 'where the designation is not known, rather than guessing from the switch.',
    ],
    'nrhpReference' => [
        'NRHP Reference Number',
        'National Register of Historic Places reference number, e.g. 75000426. Eight digits, '
        . 'leading zeros kept. Find it at https://npgallery.nps.gov/NRHP. Leave empty where the '
        . 'place is not listed; not every landmark is on the National Register.',
    ],
    'nrhpListedDate' => [
        'NRHP Listed Date',
        'The date this place was listed on the National Register, as the source gives it: '
        . '"May 13, 1971", or just "1971" where that is all that is known. Only meaningful '
        . 'alongside a reference number.',
    ],
];

/* Sit with the identifiers already on the layout. */
$NEIGHBOURS = ['placeChlNumber', 'gnisId', 'wikidataId'];
$TYPE = 'place';

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }

$created = 0; $added = 0;
$fieldObjects = [];

foreach ($DEFS as $handle => [$name, $instructions]) {
    $f = $fs->getFieldByHandle($handle);
    if ($f === null) {
        echo str_pad($handle, 18) . 'would create, PlainText, "' . $name . '"' . PHP_EOL;
        echo str_pad('', 18) . mb_substr($instructions, 0, 96) . '…' . PHP_EOL;
        $created++;
        if ($APPLY) {
            $new = new \craft\fields\PlainText();
            $new->name = $name;
            $new->handle = $handle;
            $new->instructions = $instructions;
            if (!$fs->saveField($new)) {
                echo str_pad('', 18) . 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
                continue;
            }
            $f = $fs->getFieldByHandle($handle);
            echo str_pad('', 18) . 'created' . PHP_EOL;
        }
    } else {
        echo str_pad($handle, 18) . 'exists already, left alone' . PHP_EOL;
    }
    if ($f) { $fieldObjects[$handle] = $f; }
}

/* ------------------------------------------------------------- the layout */

echo PHP_EOL;
$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
$missing = array_values(array_diff(array_keys($DEFS), $present));

if (!$missing) {
    echo 'the place layout already carries all three' . PHP_EOL;
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
    echo 'would add ' . implode(', ', $missing) . ' to "' . $tabName . '"'
        . ($at === null ? ' at the end' : ' beside ' . implode(' and ', $NEIGHBOURS)) . PHP_EOL;

    if ($APPLY) {
        $els = $tabs[$tabIdx]->getElements();
        $insert = [];
        foreach ($missing as $h) {
            if (isset($fieldObjects[$h])) { $insert[] = new \craft\fieldlayoutelements\CustomField($fieldObjects[$h]); }
        }
        if ($at === null || $at > count($els)) { $els = array_merge($els, $insert); }
        else { array_splice($els, $at, 0, $insert); }
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        echo '  ' . ($svc->saveEntryType($type) ? 'added' : 'FAILED: ' . implode('; ', $type->getFirstErrors())) . PHP_EOL;
        $added = count($missing);
    }
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'fields to create: ' . $created . ', to add to the layout: ' . count($missing) . PHP_EOL;
if (!$APPLY) {
    echo 'nothing written.' . PHP_EOL;
    echo '_partials/head/schema.twig already reads all three, so sameAs and identifier' . PHP_EOL;
    echo 'start carrying them as soon as a record holds a value.' . PHP_EOL;
} else {
    echo 'applied. This is a project config change, so it lands in config/project/.' . PHP_EOL;
}
