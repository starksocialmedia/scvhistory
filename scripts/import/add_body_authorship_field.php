/**
 * Adds bodyAuthorship, which says who wrote the prose already on a person
 * record, so 2026 editorial writing can never be mistaken for the archive's
 * own legacy text.
 *
 * WHY THIS IS THE FIRST STEP
 *
 * 33 person records carry a body and an authorBio. All 33 are byte-identical
 * to the text in inventory/wp_content.json, the old WordPress site, carried in
 * by the root persons_v3_batch*.php scripts. It is written narrative with no
 * citations: "Ysidora Bandini ... tumbled from a rooftop railing into his
 * arms". recordProvenance is empty on every one of them, and that field's own
 * instructions say empty means a person made it, so today the archive cannot
 * tell this prose from Leon's.
 *
 * Nothing new can be labelled trustworthy while the text beside it is
 * unlabelled: a byline on the new profile implies the old prose is the site's
 * own. So the old prose gets its label first.
 *
 *   legacy-leon                 the legacy SCVHistory site's own writing
 *   wordpress-import-unsourced  carried in from WordPress, no citations, author
 *                               not established. Hidden from the front end
 *   editorial-2026              written for SCVHistory in 2026, with citations
 *   mixed                       both, and not yet separated. Hidden
 *
 * Empty means nobody has looked yet, and is also hidden. Silence is not a
 * claim: an unclassified body is not published as though it were sound.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_body_authorship_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'bodyAuthorship';
$NAME   = 'Body authorship';
$TYPE   = 'person';
$NEIGHBOURS = ['recordProvenance', 'personFinePrint', 'authorBio'];

$INSTR =
    'Who wrote the prose in Body and Author Bio on this record. Empty means '
    . 'nobody has established it yet, and the prose stays off the front end '
    . 'until somebody does. Only "Legacy SCVHistory" and "SCVHistory editorial, '
    . '2026" publish.';

$OPTIONS = [
    ['label' => 'Legacy SCVHistory (Leon Worden\'s site)', 'value' => 'legacy-leon', 'default' => false],
    ['label' => 'WordPress import, unsourced',            'value' => 'wordpress-import-unsourced', 'default' => false],
    ['label' => 'SCVHistory editorial, 2026',             'value' => 'editorial-2026', 'default' => false],
    ['label' => 'Mixed, not yet separated',               'value' => 'mixed', 'default' => false],
];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
$created = 0;
if ($field === null) {
    echo $HANDLE . '  would create, Dropdown, "' . $NAME . '", no default' . PHP_EOL;
    foreach ($OPTIONS as $o) { echo '   ' . str_pad($o['value'], 28) . $o['label'] . PHP_EOL; }
    if ($APPLY) {
        $new = new \craft\fields\Dropdown();
        $new->name = $NAME;
        $new->handle = $HANDLE;
        $new->instructions = $INSTR;
        $new->options = $OPTIONS;
        if (!$fs->saveField($new)) { echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL; return; }
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
    echo 'the person layout already carries ' . $HANDLE . PHP_EOL;
} else {
    $tabs = $layout->getTabs();
    $tabIdx = 0; $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && in_array($el->getField()->handle, $NEIGHBOURS, true)) { $tabIdx = $ti; $at = $ei + 1; }
        }
    }
    echo 'would add ' . $HANDLE . ' to "' . ($tabs[$tabIdx]->name ?? ('tab ' . ($tabIdx + 1))) . '"' . PHP_EOL;
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

/* What the classification will face. */
$withBody = \craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->body(':notempty:')->count();
$withBio  = \craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->authorBio(':notempty:')->count();
echo PHP_EOL . 'person records carrying prose: ' . $withBody . ' with a body, ' . $withBio . ' with an author bio' . PHP_EOL;
echo 'classify them with scripts/import/classify_person_bodies.php' . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

Craft::$app->getFields()->refreshFields();
$f2 = Craft::$app->getFields()->getFieldByHandle($HANDLE);
$t2 = Craft::$app->getEntries()->getEntryTypeByHandle($TYPE);
$inLayout = $t2 && in_array($HANDLE, array_map(fn($c) => $c->handle, $t2->getFieldLayout()->getCustomFields()), true);
$set = \craft\elements\Entry::find()->section('persons')->status(null)->bodyAuthorship(':notempty:')->count();
$ok = $f2 instanceof \craft\fields\Dropdown && $inLayout && $set === 0;
echo PHP_EOL . 'READ-BACK ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
echo '   field ' . ($f2 ? get_class($f2) : 'MISSING') . PHP_EOL;
echo '   in person layout ' . ($inLayout ? 'yes' : 'NO') . PHP_EOL;
echo '   records already classified ' . $set . ' (expected 0)' . PHP_EOL;
if (!$ok) { throw new \RuntimeException('add_body_authorship_field read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_body_authorship_field.php', $created, 'verified: dropdown, in layout, 0 classified',
    'step 0 of the editorial profiles track');
