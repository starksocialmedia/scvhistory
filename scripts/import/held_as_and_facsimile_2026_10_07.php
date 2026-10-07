/**
 * One identity, with the scan recorded as how we hold it (Nathan, 7 October 2026: "Add the form field (clipping, magazine pages,
 * transcription only, web) and the facsimile role marking an image as a copy of the record rather than an illustration. That is
 * what makes one identity work.").
 *  - heldAs, a dropdown on the article and document types: the form the thing survives in here. Empty means not recorded.
 *  - assetRole gains "facsimile": the file is a copy of the record that holds it (a scan of the page), not an illustration.
 * Nothing is set on any record here; the retyping dry run (inventory/review/retype-dry-run-2026-10-07.md) is where values come in.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/held_as_and_facsimile_2026_10_07.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0;
$OPT = [['label' => '', 'value' => '', 'default' => true], ['label' => 'Clipping', 'value' => 'clipping', 'default' => false],
  ['label' => 'Magazine pages', 'value' => 'magazine-pages', 'default' => false], ['label' => 'Transcription only', 'value' => 'transcription-only', 'default' => false],
  ['label' => 'Web', 'value' => 'web', 'default' => false]];
$f = $fs->getFieldByHandle('heldAs'); echo 'field heldAs: ' . ($f ? 'exists' : 'create (clipping, magazine pages, transcription only, web)') . PHP_EOL;
if ($APPLY && !$f) { $f = new \craft\fields\Dropdown(['handle' => 'heldAs', 'name' => 'Held as', 'options' => $OPT,
    'instructions' => 'The form this record survives in here. It is how we hold the thing, not what it is: a newspaper article held as a scanned clipping is an article, held as a clipping. Empty means not recorded.']);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++; }
foreach (['article' => 'sourceLine', 'document' => 'sourceLine'] as $th => $after) {
  $t = $es->getEntryTypeByHandle($th); $layout = $t->getFieldLayout();
  $has = in_array('heldAs', array_map(fn($x) => $x->handle, $layout->getCustomFields()), true);
  echo "type $th: " . ($has ? 'has heldAs' : "add heldAs after $after") . PHP_EOL;
  if (!$APPLY || $has || !$f) { continue; }
  $done = false; foreach ($layout->getTabs() as $tab) { $els = [];
    foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === $after) { $els[] = new \craft\fieldlayoutelements\CustomField($f); $done = true; } }
    $tab->setElements($els); }
  if (!$done) { $tabs = $layout->getTabs(); $els = $tabs[0]->getElements(); $els[] = new \craft\fieldlayoutelements\CustomField($f); $tabs[0]->setElements($els); }
  $layout->setTabs($layout->getTabs()); $t->setFieldLayout($layout);
  if (!$es->saveEntryType($t)) { throw new \RuntimeException(json_encode($t->getFirstErrors())); } $n++;
}
$r = $fs->getFieldByHandle('assetRole'); $vals = array_map(fn($o) => $o['value'] ?? '', $r->options);
echo 'assetRole: ' . (in_array('facsimile', $vals, true) ? 'has facsimile' : 'add "A copy of the record (facsimile)"') . PHP_EOL;
if ($APPLY && !in_array('facsimile', $vals, true)) { $o = $r->options; $o[] = ['label' => 'A copy of the record that holds it (facsimile: a scan of the page)', 'value' => 'facsimile', 'default' => false];
  $r->options = $o; if (!$fs->saveField($r)) { throw new \RuntimeException(json_encode($r->getFirstErrors())); } $n++; }
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('held_as_and_facsimile_2026_10_07.php', $n, 'verified', 'heldAs on articles and documents; assetRole facsimile'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
