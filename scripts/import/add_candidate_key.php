/**
 * A candidate's identity without a public record (Nathan, 1 October 2026: "the
 * archive should know the same person stood twice without giving them a public
 * record. Build it before any delete").
 *
 *   candidateKey   PlainText on the Candidacy type. The same key on two
 *                  candidacies means one person stood both times. It is not
 *                  shown anywhere and has no page. A candidacy linked to a person
 *                  record carries that person's key too, so every count of who
 *                  stood more than once can read keys alone.
 *
 * remove_insignificant_persons.php fills the keys and then removes the records.
 * Its own script, run first. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_candidate_key.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to project config' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries();
$type = $svc->getEntryTypeByHandle('Candidacy');
$f = $fs->getFieldByHandle('candidateKey');
$inLayout = in_array('candidateKey', array_map(fn($x) => $x->handle, $type->getFieldLayout()->getCustomFields()), true);
echo 'candidateKey: ' . ($f ? 'exists' : 'create (PlainText)') . ($inLayout ? ', in the Candidacy layout' : ', add to the Candidacy layout') . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$f) {
    $f = new \craft\fields\PlainText(); $f->name = 'Candidate key'; $f->handle = 'candidateKey';
    $f->instructions = 'Not shown. The same key on two candidacies means one person stood both times, whether or not that person has a record.';
    if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getErrors())); }
    $fs->refreshFields(); $f = $fs->getFieldByHandle('candidateKey');
}
if (!$inLayout) {
    $type = $svc->getEntryTypeByHandle('Candidacy'); $layout = $type->getFieldLayout(); $tabs = $layout->getTabs();
    $els = $tabs[0]->getElements(); $els[] = new \craft\fieldlayoutelements\CustomField($f); $tabs[0]->setElements($els); $layout->setTabs($tabs); $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { throw new \RuntimeException(json_encode($type->getErrors())); }
}
$fs->refreshFields();
$ok = in_array('candidateKey', array_map(fn($x) => $x->handle, $svc->getEntryTypeByHandle('Candidacy')->getFieldLayout()->getCustomFields()), true);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_candidate_key.php', 1, $ok ? 'verified' : 'SHORT', 'candidateKey on Candidacy');
if (!$ok) { throw new \RuntimeException('add_candidate_key: read-back failed'); }
