/**
 * contentCredentials on archiveMedia: what a file's C2PA manifest says was done
 * to it (Nathan, 1 October 2026, correcting the rule on generated images).
 *
 * An edited photograph enters the archive with the edit recorded. The record of
 * an edit was already there: enhancedFrom (the original, when the archive holds
 * it), enhancementMethod (what was changed), enhancedBy (who) and enhancedDate
 * (when), with the original's source in source and sourceUrl. What was missing
 * is that the file carries content credentials and what they declare: the
 * manifest's address and each recorded step (opened, edited with Generate Fill,
 * Remove Object, upscaled), as scan_content_credentials.py reads it. A
 * credential is evidence of an edit, not a disqualification.
 *
 * Schema only: no asset is changed. Run in its own request before any import
 * that sets the field. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_content_credentials_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields();
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$have = (bool)$fs->getFieldByHandle('contentCredentials');
$onLayout = in_array('contentCredentials', array_map(fn($f) => $f->handle, $vol->getFieldLayout()->getCustomFields()), true);
echo '   contentCredentials  ' . ($have ? 'exists' : 'create PlainText, multi-line') . '; archiveMedia layout ' . ($onLayout ? 'has it' : 'add after enhancedDate') . PHP_EOL;
if ($have && $onLayout) { echo 'nothing to do' . PHP_EOL; return; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

if (!$have) {
    $f = new \craft\fields\PlainText(['name' => 'Content credentials', 'handle' => 'contentCredentials', 'multiline' => true, 'initialRows' => 3,
        'instructions' => 'When the file carries a C2PA content credential: the manifest\'s address, then each step it records, with its time (UTC) and tool. Empty when the file carries none. A credential records an edit; it does not by itself mean the image was generated.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('contentCredentials: ' . json_encode($f->getFirstErrors())); }
}
if (!$onLayout) {
    $layout = $vol->getFieldLayout(); $tabs = $layout->getTabs(); $ti = 0; $at = null;
    foreach ($tabs as $i => $tab) { foreach (array_values($tab->getElements()) as $j => $el) { if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->attribute() === 'enhancedDate') { $ti = $i; $at = $j + 1; } } }
    $els = array_values($tabs[$ti]->getElements());
    array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('contentCredentials'))]);
    $tabs[$ti]->setElements($els); $layout->setTabs($tabs); $vol->setFieldLayout($layout);
    if (!Craft::$app->getVolumes()->saveVolume($vol)) { throw new \RuntimeException('layout: ' . json_encode($vol->getFirstErrors())); }
}
$ok = (bool)Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia')->getFieldLayout()->getFieldByHandle('contentCredentials');
echo 'READ-BACK ' . ($ok ? 'OK: contentCredentials on archiveMedia' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_content_credentials_field.php', 1, $ok ? 'verified' : 'SHORT', 'contentCredentials on archiveMedia');
if (!$ok) { throw new \RuntimeException('add_content_credentials_field: read-back failed'); }
