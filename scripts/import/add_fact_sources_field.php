/**
 * factSources: a table on war memorial records saying where each fact comes
 * from (Nathan, 2 October 2026, approving inventory/review/war-memorial-
 * sourcing-2026-10-02.md: "every fact field traced to a source", with
 * disagreements shown, not silently chosen).
 *
 * Columns: fact (the label, e.g. "Date of death"), value (as the record holds
 * it), notes (the footnote numbers that support it, "1, 3"), agreement (what
 * the sources say where they differ; empty where they agree).
 *
 * Schema only, its own request. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_fact_sources_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fields = Craft::$app->getFields(); $entries = Craft::$app->getEntries();
$f = $fields->getFieldByHandle('factSources');
echo 'factSources: ' . ($f ? 'exists' : 'would create (table: fact, value, notes, agreement)') . PHP_EOL;
$type = null;
foreach ($entries->getAllEntryTypes() as $t) { if ($t->handle === 'warMemorial') { $type = $t; } }
if (!$type) { foreach ($entries->getSectionByHandle('warMemorials')->getEntryTypes() as $t) { $type = $t; } }
$has = $type && $f && $type->getFieldLayout()->getFieldByHandle('factSources');
echo 'layout ' . ($type ? $type->handle : '?') . ': ' . ($has ? 'has it' : 'would add') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$f) {
    $f = new \craft\fields\Table();
    $f->name = 'Facts and their sources'; $f->handle = 'factSources';
    $f->instructions = 'One row per fact on the record: the fact, its value, the footnote numbers that support it, and what the sources say where they disagree.';
    $f->addRowLabel = 'Add a fact';
    $f->columns = [
        'col1' => ['heading' => 'Fact', 'handle' => 'fact', 'width' => '', 'type' => 'singleline'],
        'col2' => ['heading' => 'Value', 'handle' => 'value', 'width' => '', 'type' => 'singleline'],
        'col3' => ['heading' => 'Notes', 'handle' => 'notes', 'width' => '', 'type' => 'singleline'],
        'col4' => ['heading' => 'Where the sources differ', 'handle' => 'agreement', 'width' => '', 'type' => 'multiline'],
    ];
    if (!$fields->saveField($f)) { throw new \RuntimeException('factSources: ' . json_encode($f->getErrors())); }
    $f = $fields->getFieldByHandle('factSources');
}
if (!$type->getFieldLayout()->getFieldByHandle('factSources')) {
    $layout = $type->getFieldLayout(); $tabs = $layout->getTabs();
    $els = $tabs[0]->getElements(); $els[] = new \craft\fieldlayoutelements\CustomField($f); $tabs[0]->setElements($els);
    $layout->setTabs($tabs); $type->setFieldLayout($layout);
    if (!$entries->saveEntryType($type)) { throw new \RuntimeException('layout: ' . json_encode($type->getErrors())); }
}
$ok = (bool)Craft::$app->getEntries()->getEntryTypeById($type->id)->getFieldLayout()->getFieldByHandle('factSources');
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_fact_sources_field.php', 1, $ok ? 'verified' : 'SHORT', 'factSources table on war memorial records');
if (!$ok) { throw new \RuntimeException('add_fact_sources_field: read-back failed'); }
