/**
 * Puts sourceLine on the collection entry type and recovers the one value the
 * WordPress export holds for it.
 *
 * sourceLine already exists and is already on articles; this adds the same
 * field to collections rather than making a second one, so a citation means the
 * same thing wherever it is read.
 *
 * The export carries `source` on the Reynolds collection:
 *   "The Signal / Santa Clarita Valley Chamber of Commerce"
 * which names who published the series. It reached no field at import because
 * collections had nowhere to put it. That line belongs in the citation block on
 * the lander and in the JSON-LD as the publisher, and both read it from here.
 *
 * Two steps in one run, because the second needs the first: the field goes onto
 * the layout, then the value is written. Only an empty field is filled.
 *
 * Idempotent. A second run finds the field on the layout and the value in
 * place, and does nothing.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_collection_source_line.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'sourceLine';
$TYPE   = 'collection';
/* Sit with the other provenance on the layout, not at the end of the tab. */
$NEIGHBOURS = ['legacyUrl', 'archiveUrl', 'legacyKey'];

$root = \Craft::getAlias('@root');
$fs   = Craft::$app->getFields();
$svc  = Craft::$app->getEntries();
$elements = Craft::$app->getElements();

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* ------------------------------------------------------------ 1. the field */

$field = $fs->getFieldByHandle($HANDLE);
if (!$field) { echo 'field ' . $HANDLE . ' does not exist at all; expected it on articles' . PHP_EOL; return; }
echo 'field ' . $HANDLE . ': "' . $field->name . '", already exists, reused rather than duplicated' . PHP_EOL;

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }

$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

$addedToLayout = false;
if (in_array($HANDLE, $present, true)) {
    echo 'the collection layout already carries it' . PHP_EOL;
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
        . ($at === null ? ' at the end' : ' beside ' . implode(' or ', $NEIGHBOURS)) . PHP_EOL;

    if ($APPLY) {
        $els = $tabs[$tabIdx]->getElements();
        $new = new \craft\fieldlayoutelements\CustomField($field);
        if ($at === null || $at > count($els)) { $els[] = $new; }
        else { array_splice($els, $at, 0, [$new]); }
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo '  added' . PHP_EOL; $addedToLayout = true; }
        else { echo '  FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; return; }
    }
}

/* ------------------------------------------------------------ 2. the value */

echo PHP_EOL . str_repeat('-', 74) . PHP_EOL;

$path = $root . '/inventory/wp_content.json';
if (!file_exists($path)) { echo 'not found: ' . $path . PHP_EOL; return; }
$wp = json_decode(file_get_contents($path), true);

$found = 0; $wouldSet = 0;
foreach (($wp['posts'] ?? []) as $post) {
    if (($post['type'] ?? '') !== 'collection') { continue; }
    $source = trim((string)(($post['meta'] ?? [])['source'] ?? ''));
    if ($source === '') { continue; }
    $found++;

    $e = \craft\elements\Entry::find()->section('collections')->slug($post['slug'] ?? '')->status(null)->one();
    if (!$e) { $e = \craft\elements\Entry::find()->section('collections')->title($post['title'] ?? '')->status(null)->one(); }
    if (!$e) {
        echo ($post['title'] ?? '?') . ': no collection record matches, slug "' . ($post['slug'] ?? '') . '"' . PHP_EOL;
        continue;
    }

    echo ($e->title ?: $e->slug) . '  (#' . $e->id . ')' . PHP_EOL;

    /* In a dry run the field is not on the layout yet, so reading it would
       throw; say what would happen instead of pretending to read it. */
    if (!$hasField($e, $HANDLE)) {
        echo '  ' . str_pad('source', 12) . 'would set "' . $source . '"' . PHP_EOL;
        echo '  ' . str_pad('', 12) . '(the field is not on the layout yet; it is added above in the same run)' . PHP_EOL;
        $wouldSet++;
        continue;
    }

    $current = trim((string)$e->getFieldValue($HANDLE));
    if ($current === $source) { echo '  already holds it' . PHP_EOL; continue; }
    if ($current !== '') {
        echo '  holds "' . $current . '" already, left alone' . PHP_EOL;
        echo '  the export says "' . $source . '"' . PHP_EOL;
        continue;
    }

    echo '  ' . str_pad('sourceLine', 12) . 'SET "' . $source . '"' . PHP_EOL;
    $wouldSet++;
    if ($APPLY) {
        $e->setFieldValue($HANDLE, $source);
        echo '  ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
    }
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'collections in the export carrying a source: ' . $found . PHP_EOL;
echo 'values that would be set: ' . $wouldSet . PHP_EOL;
if (!$APPLY) {
    echo 'nothing written.' . PHP_EOL;
    echo 'The lander citation and the JSON-LD publisher already read sourceLine,' . PHP_EOL;
    echo 'so both start using it as soon as the value is there.' . PHP_EOL;
}
