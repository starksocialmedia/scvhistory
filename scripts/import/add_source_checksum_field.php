/**
 * sourceChecksum: the file's checksum, out of the provenance sentence (Nathan,
 * 3 October 2026: "Keep the checksum in the data, not the sentence. A reader
 * wants to know where it came from, not that it was re-encoded").
 *
 * A PlainText field on the archiveMedia layout, after source, holding
 * "sha256:<hex>" or "sha1:<hex>" for the file as received. Internal: no
 * template shows it (field-display.json). The sentences are rewritten by
 * simplify_media_provenance.php.
 * Schema only: no asset is changed. Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_source_checksum_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields();
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$has = (bool)$fs->getFieldByHandle('sourceChecksum');
$on = in_array('sourceChecksum', array_map(fn($f) => $f->handle, $vol->getFieldLayout()->getCustomFields()), true);
echo ($has ? 'field exists' : 'create PlainText sourceChecksum') . '; ' . ($on ? 'on the layout' : 'add to archiveMedia after source') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($has && $on) { echo 'nothing to do' . PHP_EOL; return; }
if (!$has) {
    $f = new \craft\fields\PlainText(['name' => 'Source checksum', 'handle' => 'sourceChecksum',
        'instructions' => 'The checksum of the file as the archive received it, "sha256:<hex>" or "sha1:<hex>". For verification, not for readers: the provenance sentence says where the file came from.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('sourceChecksum: ' . json_encode($f->getFirstErrors())); }
}
if (!$on) {
    $layout = $vol->getFieldLayout(); $tabs = $layout->getTabs(); $ti = 0; $at = null;
    foreach ($tabs as $i => $tab) { foreach (array_values($tab->getElements()) as $j => $el) { if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->attribute() === 'source') { $ti = $i; $at = $j + 1; } } }
    $els = array_values($tabs[$ti]->getElements());
    array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('sourceChecksum'))]);
    $tabs[$ti]->setElements($els); $layout->setTabs($tabs); $vol->setFieldLayout($layout);
    if (!Craft::$app->getVolumes()->saveVolume($vol)) { throw new \RuntimeException('layout: ' . json_encode($vol->getFirstErrors())); }
}
$ok = (bool)Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia')->getFieldLayout()->getFieldByHandle('sourceChecksum');
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_source_checksum_field.php', 1, $ok ? 'verified' : 'SHORT', 'sourceChecksum on archiveMedia (internal)');
if (!$ok) { throw new \RuntimeException('add_source_checksum_field: read-back failed'); }
