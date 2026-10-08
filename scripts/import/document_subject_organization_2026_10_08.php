/**
 * Documents that do not say whose they are (inventory/review/document-subject-organization-2026-10-08.md), approved by Nathan
 * on 8 October 2026 ("yes to adding Subject Organization to documents and filling all 14 from the election records").
 * Adds the existing field subjectOrganization (Entries, Organizations) to the document type after subjectPerson, then fills
 * it on every document that an election record links to, with the organizations those election records name (any Entries
 * field on the election that points into Organizations). Computed, not typed: #26573 gets Castaic Lake Water Agency (#26563).
 * Fills an empty field only; a document whose field already holds something is reported and left.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/document_subject_organization_2026_10_08.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$es = Craft::$app->getEntries(); $n = 0;
$f = Craft::$app->getFields()->getFieldByHandle('subjectOrganization');
if (!$f) { throw new \RuntimeException('field subjectOrganization missing'); }
$t = $es->getEntryTypeByHandle('document'); $layout = $t->getFieldLayout();
$has = in_array('subjectOrganization', array_map(fn($x) => $x->handle, $layout->getCustomFields()), true);
echo 'type document: ' . ($has ? 'has subjectOrganization' : 'add subjectOrganization after subjectPerson') . PHP_EOL;
if ($APPLY && !$has) {
  $done = false; foreach ($layout->getTabs() as $tab) { $els = [];
    foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'subjectPerson') { $els[] = new \craft\fieldlayoutelements\CustomField($f); $done = true; } }
    $tab->setElements($els); }
  if (!$done) { throw new \RuntimeException('subjectPerson not found in the document layout'); }
  $layout->setTabs($layout->getTabs()); $t->setFieldLayout($layout);
  if (!$es->saveEntryType($t)) { throw new \RuntimeException(json_encode($t->getFirstErrors())); } $n++; $has = true;
}
$docs = Entry::find()->section('documents')->status(null)->all(); $planned = 0;
foreach ($docs as $d) {
  $els = Entry::find()->type('election')->relatedTo(['targetElement' => $d])->status(null)->all(); if (!$els) { continue; }
  $orgs = [];
  foreach ($els as $x) { foreach ($x->getFieldLayout()->getCustomFields() as $cf) { if ($cf instanceof \craft\fields\Entries) {
    foreach ($x->getFieldValue($cf->handle)->status(null)->all() as $r) { if ($r->getSection()->handle === 'organizations') { $orgs[$r->id] = $r->title; } } } } }
  if (!$orgs) { echo "#{$d->id} {$d->title}: no organization on its " . count($els) . ' election records, left' . PHP_EOL; continue; }
  asort($orgs); $planned++;
  $cur = []; if ($has) { $d2 = Entry::find()->id($d->id)->status(null)->one(); $cur = $d2->getFieldValue('subjectOrganization')->status(null)->ids(); }
  $line = "#{$d->id} {$d->title} (" . count($els) . ' elections): ' . implode('; ', array_map(fn($k, $v) => "$v (#$k)", array_keys($orgs), $orgs));
  if ($cur) { echo "$line: " . (array_diff(array_keys($orgs), $cur) || array_diff($cur, array_keys($orgs)) ? 'HOLDS #' . implode(', #', $cur) . ', left' : 'done already') . PHP_EOL; continue; }
  echo $line . PHP_EOL;
  if ($APPLY) { $d2->setFieldValue('subjectOrganization', array_keys($orgs)); if (!Craft::$app->getElements()->saveElement($d2)) { throw new \RuntimeException("#{$d->id} " . json_encode($d2->getFirstErrors())); } $n++; }
}
echo "documents linked from elections: $planned" . PHP_EOL;
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('document_subject_organization_2026_10_08.php', $n, 'verified', 'subjectOrganization on documents; filled from election links'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
