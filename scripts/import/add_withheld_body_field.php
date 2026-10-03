/**
 * withheldBody: where a body that may not be shown is kept (Nathan, 3 October
 * 2026: "audit every WordPress-origin record the same way, sentence by
 * sentence, and withhold the suspect ones").
 *
 * People have bodyAuthorship, which every template that shows a person's text
 * reads. Organizations, places, groups, events and war memorials have no such
 * gate, and their bodies feed index cards, meta descriptions and JSON-LD as
 * well as the page. Rather than gate each of those, a withheld body is moved
 * out of body into this field, which no template reads (field-display.json:
 * internal). The text is kept, not deleted, for the rewrite from sources.
 * Added to every entry type of those five sections. Schema only.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_withheld_body_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries();
$types = [];
foreach (['organizations', 'places', 'groups', 'events', 'warMemorials'] as $h) { foreach ($es->getSectionByHandle($h)->getEntryTypes() as $t) { $types[$t->id] = $t; } }
$has = (bool)$fs->getFieldByHandle('withheldBody');
$need = array_filter($types, fn($t) => !$t->getFieldLayout()->getFieldByHandle('withheldBody'));
echo ($has ? 'field exists' : 'create PlainText (multi-line) withheldBody') . '; add to ' . count($need) . ' entry types: ' . implode(', ', array_map(fn($t) => $t->handle, $need)) . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($has && !$need) { echo 'nothing to do' . PHP_EOL; return; }
if (!$has) {
    $f = new \craft\fields\PlainText(['name' => 'Withheld body', 'handle' => 'withheldBody', 'multiline' => true, 'initialRows' => 6,
        'instructions' => 'A body taken off the page because it has no source and came from a doubtful origin (the WordPress import, 3 October 2026). Kept for the rewrite from sources; no template shows it.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('withheldBody: ' . json_encode($f->getFirstErrors())); }
}
$f = $fs->getFieldByHandle('withheldBody');
foreach ($need as $t) {
    $layout = $t->getFieldLayout(); $tabs = $layout->getTabs(); $ti = count($tabs) - 1;
    $els = array_values($tabs[$ti]->getElements()); $els[] = new \craft\fieldlayoutelements\CustomField($f);
    $tabs[$ti]->setElements($els); $layout->setTabs($tabs); $t->setFieldLayout($layout);
    if (!$es->saveEntryType($t)) { throw new \RuntimeException("{$t->handle}: " . json_encode($t->getFirstErrors())); }
}
$short = [];
foreach (['organizations', 'places', 'groups', 'events', 'warMemorials'] as $h) { foreach ($es->getSectionByHandle($h)->getEntryTypes() as $t) { if (!$t->getFieldLayout()->getFieldByHandle('withheldBody')) { $short[] = $t->handle; } } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_withheld_body_field.php', count($need), $short ? 'SHORT' : 'verified', 'withheldBody (internal) on organizations, places, groups, events, war memorials');
if ($short) { throw new \RuntimeException('add_withheld_body_field: ' . implode(', ', $short)); }
