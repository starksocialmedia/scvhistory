/**
 * researchLeads: where a research lead goes, off the page (Nathan, 5 October 2026: "putting research leads in public editor
 * notes is wrong in both directions. A reader does not need to know we suspect Hoskinson is the 1999 Man of the Year, and a
 * later session needs to know we looked. Build a researchLeads field, internal, classed so the rendered-field check knows it
 * is deliberate").
 * A plain multiline field on every entry type that carries editorNotes, placed after them; classed "internal" in
 * scripts/import/field-display.json. A lead is an identity, a fact or a source the archive suspects and has not established;
 * an editor's note is for what a reader should know. Writes project config. Idempotent. Dry run by default. $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_research_leads_2026_10_05.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0;
$f = $fs->getFieldByHandle('researchLeads'); echo 'researchLeads: ' . ($f ? 'exists' : 'create') . PHP_EOL;
if ($APPLY && !$f) {
  $f = new \craft\fields\PlainText(['name' => 'Research leads', 'handle' => 'researchLeads', 'multiline' => true, 'initialRows' => 4, 'searchable' => false,
    'instructions' => 'Not shown on the page. Identities, facts and sources suspected and not established, each with where it was seen and what would settle it, so a later session can take them up. A reader is told only what is established, in the editor\'s notes.']);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getErrors())); } $n++;
}
foreach ($es->getAllSections() as $s) { foreach ($s->getEntryTypes() as $et) {
  $layout = $et->getFieldLayout(); $handles = array_map(fn($x) => $x->handle, $layout->getCustomFields());
  if (!in_array('editorNotes', $handles, true) || in_array('researchLeads', $handles, true)) { continue; }
  echo "add to {$s->handle}/{$et->handle}\n"; if (!$APPLY) { continue; }
  $tabs = $layout->getTabs(); $done = false;
  foreach ($tabs as $tab) { $els = []; foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'editorNotes') { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('researchLeads')); $done = true; } } $tab->setElements($els); }
  $layout->setTabs($tabs); $et->setFieldLayout($layout);
  if (!$es->saveEntryType($et)) { throw new \RuntimeException("{$et->handle} " . json_encode($et->getErrors())); } $n++;
} }
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_research_leads_2026_10_05.php', $n, 'verified', 'researchLeads, internal, on every entry type with editor notes'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
