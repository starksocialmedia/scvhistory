/**
 * What a body is and whether it belongs on Public bodies, kept apart (Nathan, 5 October 2026: "A record's type should say
 * what it is; whether it appears on /civic should be a separate judgement"; inventory/review/civic-audit-2026-10-05.md).
 *   civicRole   how the body stands to this valley: governs, represents, polices, advises, administers, or "none" (does not
 *               serve this valley). /civic shows a public body (a government, or a school district) unless its role is none.
 * Sets the role on every record /civic showed on 5 October, from the audit, and makes the five officer agencies' types honest:
 * the Los Angeles Police and Burbank Police Departments are governments (they had been filed "other" to keep them off the
 * page); they, and Ventura County's township constables, are role "none".
 * Writes project config (a field). Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_civic_role_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $n = 0;
$ROLE = [
  394 => 'governs', 21588 => 'governs', 21590 => 'governs', 21592 => 'governs', 21594 => 'governs', 21596 => 'governs', 29691 => 'governs',
  29279 => 'governs', 28275 => 'governs',
  28269 => 'represents', 28271 => 'represents', 28273 => 'represents', 29393 => 'represents',
  29282 => 'polices', 29682 => 'polices', 29792 => 'polices', 29798 => 'polices',
  15939 => 'advises', 29088 => 'advises', 29090 => 'advises', 16290 => 'advises',
  402 => 'administers', 26563 => 'administers', 27534 => 'administers', 29699 => 'administers',
  29794 => 'none', 29796 => 'none', 29800 => 'none'];
$TYPE = [29794 => 'government', 29796 => 'government'];
$f = $fs->getFieldByHandle('civicRole'); echo 'civicRole: ' . ($f ? 'exists' : 'create') . PHP_EOL;
if ($APPLY && !$f) {
  $f = new \craft\fields\Dropdown(['name' => 'Role toward this valley', 'handle' => 'civicRole',
    'instructions' => 'How the body stands to the Santa Clarita Valley. Public bodies (/civic) shows a government or school district unless this is "Does not serve this valley". It does not say what the body is; Organization type does.',
    'options' => [['label' => 'Not assessed', 'value' => '', 'default' => true], ['label' => 'Governs', 'value' => 'governs', 'default' => false], ['label' => 'Represents', 'value' => 'represents', 'default' => false],
      ['label' => 'Polices', 'value' => 'polices', 'default' => false], ['label' => 'Advises', 'value' => 'advises', 'default' => false], ['label' => 'Administers', 'value' => 'administers', 'default' => false],
      ['label' => 'Does not serve this valley', 'value' => 'none', 'default' => false]]]);
  if (!$fs->saveField($f)) { throw new \RuntimeException('civicRole ' . json_encode($f->getErrors())); } $n++;
}
$et = $es->getSectionByHandle('organizations')->getEntryTypes()[0]; $layout = $et->getFieldLayout();
$inLayout = in_array('civicRole', array_map(fn($x) => $x->handle, $layout->getCustomFields()), true);
echo 'layout: ' . ($inLayout ? 'has civicRole' : 'add civicRole after orgLevel') . PHP_EOL;
if ($APPLY && !$inLayout) {
  $tab = $layout->getTabs()[0]; $els = [];
  foreach ($tab->getElements() as $x) { $els[] = $x; if ($x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'orgLevel') { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('civicRole')); } }
  if (count($els) === count($tab->getElements())) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('civicRole')); }
  $tab->setElements($els); $layout->setTabs(array_merge([$tab], array_slice($layout->getTabs(), 1))); $et->setFieldLayout($layout);
  if (!$es->saveEntryType($et)) { throw new \RuntimeException('layout'); } $n++;
}
foreach ($ROLE as $id => $role) {
  $e = Entry::find()->section('organizations')->id($id)->status(null)->one(); if (!$e) { echo "#$id missing\n"; continue; }
  echo "#$id {$e->title}: " . ($e->orgType->value ?? '') . (isset($TYPE[$id]) ? " -> {$TYPE[$id]}" : '') . ", role $role" . PHP_EOL;
  if (!$APPLY) { continue; }
  $e = Entry::find()->section('organizations')->id($id)->status(null)->one();
  $e->setFieldValue('civicRole', $role); if (isset($TYPE[$id])) { $e->setFieldValue('orgType', $TYPE[$id]); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_civic_role_2026_10_05.php', $n, 'verified', 'civicRole: each body\'s role toward the valley, apart from its type; LAPD and Burbank Police typed government, role none'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
