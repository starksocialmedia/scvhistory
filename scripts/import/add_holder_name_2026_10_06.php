/**
 * A term keeps its holder's name when there is no person record (Nathan, 6 October 2026: "keep the office record on the body's
 * page and unlink it cleanly rather than orphaning it"). A term's title is generated from its linked person, so unlinking Love
 * and Lyon (remove_thin_persons_2026_10_06.php) left "Unknown person — ...". The candidacy already carries the name
 * (nameAsPrinted); the term did not. This adds holderName (plain text, searchable) to the office holding layout, after the
 * person, read only when no person is linked; the title format falls back on it; and the two unlinked terms take their names
 * back (from the provenance line the removal wrote). The same field is how any term can stand as a row without a record.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_holder_name_2026_10_06.php'))"
 */
use craft\elements\Entry;
use craft\fieldlayoutelements\CustomField;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $n = 0;
$f = $fs->getFieldByHandle('holderName');
echo 'field holderName: ' . ($f ? 'exists' : 'create') . PHP_EOL;
if ($APPLY && !$f) {
  $f = new \craft\fields\PlainText(['handle' => 'holderName', 'name' => 'Holder\'s name, when no person record',
    'instructions' => 'The holder\'s name as the sources give it, for a term whose holder has no person record. Read only when no person is linked.', 'searchable' => true]);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++;
}
$t = $es->getEntryTypeByHandle('officeHolding');
$TF = "{holdingPerson.one().title ?? (holderName ?: 'Unknown person')} — {holdingOffice.one().title ?? 'unknown office'}, {holdingBody.one().title ?? 'unknown body'}";
$layout = $t->getFieldLayout(); $has = in_array('holderName', array_map(fn($x) => $x->handle, $layout->getCustomFields()), true);
echo 'layout: ' . ($has ? 'has it' : 'add after Person') . '; title format: ' . ($t->titleFormat === $TF ? 'done' : 'fall back on holderName') . PHP_EOL;
if ($APPLY && $f && (!$has || $t->titleFormat !== $TF)) {
  if (!$has) {
    $tab = $layout->getTabs()[0]; $els = [];
    foreach ($tab->getElements() as $x) { $els[] = $x; if ($x instanceof CustomField && $x->getField()->handle === 'holdingPerson') { $els[] = new CustomField($f); } }
    $tab->setElements($els); $layout->setTabs($layout->getTabs()); $t->setFieldLayout($layout);
  }
  $t->titleFormat = $TF;
  if (!$es->saveEntryType($t)) { throw new \RuntimeException(json_encode($t->getFirstErrors())); } $n++;
}
foreach ([28473 => 'Cassandra Nicole Love', 30407 => 'Charles L. Lyon'] as $id => $name) {
  $h = Entry::find()->id($id)->status(null)->one();
  if (!str_contains((string)$h->recordProvenance, "$name's person record removed")) { throw new \RuntimeException("#$id provenance does not name $name"); }
  $cur = $f && $APPLY ? (string)$h->getFieldValue('holderName') : '';
  echo "#$id {$h->title}: " . ($cur === $name ? 'named already' : "holderName $name") . PHP_EOL;
  if ($APPLY && $cur !== $name) { $h = Entry::find()->id($id)->status(null)->one(); $h->setFieldValue('holderName', $name); if (!$el->saveElement($h)) { throw new \RuntimeException("#$id " . json_encode($h->getFirstErrors())); } $n++;
    echo '  now: ' . Entry::find()->id($id)->status(null)->one()->title . PHP_EOL; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_holder_name_2026_10_06.php', $n, 'verified', 'holderName on office holdings; Love\'s and Lyon\'s terms named again'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
