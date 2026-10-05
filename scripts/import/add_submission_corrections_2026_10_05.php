/**
 * The send form takes corrections as well as photographs (Nathan, 5 October 2026: "Point the old submit page at the new
 * form, and have the form cover both photographs and corrections. One route in, not two").
 *   submissionKind        gains "correction"
 *   submissionCorrection  what is wrong and how the sender knows (new, multiline)
 *   submissionPage        the page it is about, when it is not a record (new)
 * Both new fields join the submission entry type after submissionWho. Writes project config. Safe to run twice.
 * Dry run by default; set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_submission_corrections_2026_10_05.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0;
$k = $fs->getFieldByHandle('submissionKind');
$has = in_array('correction', array_column($k->options, 'value'), true);
echo 'submissionKind: ' . ($has ? 'has correction' : 'add correction') . PHP_EOL;
if ($APPLY && !$has) { $o = $k->options; $o[] = ['label' => 'Correction', 'value' => 'correction', 'default' => false]; $k->options = $o; if (!$fs->saveField($k)) { throw new \RuntimeException('kind'); } $n++; }
foreach (['submissionCorrection' => ['Submission: the correction', true], 'submissionPage' => ['Submission: page it is about', false]] as $h => [$name, $multi]) {
  $f = $fs->getFieldByHandle($h); echo "$h: " . ($f ? 'exists' : 'create') . PHP_EOL;
  if ($APPLY && !$f) { $f = new \craft\fields\PlainText(['name' => $name, 'handle' => $h, 'multiline' => $multi, 'searchable' => false]); if (!$fs->saveField($f)) { throw new \RuntimeException($h); } $n++; }
}
$et = $es->getEntryTypeByHandle('submission'); $layout = $et->getFieldLayout(); $tab = $layout->getTabs()[0];
$inLayout = array_map(fn($f) => $f->handle, $layout->getCustomFields());
$missing = array_values(array_diff(['submissionCorrection', 'submissionPage'], $inLayout));
echo 'layout: ' . ($missing ? 'add ' . implode(', ', $missing) : 'complete') . PHP_EOL;
if ($APPLY && $missing) {
  $els = [];
  foreach ($tab->getElements() as $e) { $els[] = $e; if ($e instanceof \craft\fieldlayoutelements\CustomField && $e->getField()->handle === 'submissionWho') { foreach ($missing as $h) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); } } }
  $tab->setElements($els); $layout->setTabs([$tab]); $et->setFieldLayout($layout);
  if (!$es->saveEntryType($et)) { throw new \RuntimeException('entry type ' . json_encode($et->getErrors())); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_submission_corrections_2026_10_05.php', $n, 'verified', 'submissions take corrections: a kind, the correction and the page'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
