/**
 * Edits missed on import, found by the full content-credentials scan of 4 October 2026 (Nathan: "Re-run the full
 * content-credential scan across every image imported before today"). storage/runtime/asset_origins.json had last been
 * exported on 28 September, so the portraits handed over since were never scanned against their originals; the export
 * now matches each asset's sourceChecksum to inventory/incoming and done/, and the scan reads those files.
 * Seven portraits carry Adobe content credentials recording a Firefly edit (a creation step in Adobe Firefly, or the
 * creative upsampler) on 4 October 2026, read here from each file's manifest. The edit is recorded the way it is on the
 * marks (enhancementMethod, enhancedDate, contentCredentials). enhancedBy is left empty: the manifest does not name who
 * made the edit.
 * Not here: Patti Rasmussen (#29122) and Brian Walters (#29118), whose manifests record text_to_image steps, which may
 * mean a generated likeness rather than an edited photograph. Held for Nathan.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_missed_credentials_2026_10_04.php'))"
 */
use craft\elements\Asset;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements();
$M = 'https://cai-manifests.adobe.com/manifests/';
// asset id => [manifest URN as served, steps as the manifest records them (UTC), the method]
$FF = 'Edited with Adobe Firefly (generative; the credential records the file as edited from an original with generative steps)';
$UP = 'Upscaled with Adobe Firefly\'s creative upsampler (generative; the credential records the file as edited from an original with generative steps)';
$A = [
  29330 => ['urn-c2pa-a8eff4c9-4c20-4e9a-b294-8cee97935746-adobe', '2026-10-04 08:07 created (Adobe Firefly)', $FF],
  29326 => ['urn-c2pa-1de26f5e-23e9-4390-879a-6cb40bc21d9b-adobe', '2026-10-04 08:06 created (Adobe Firefly)', $FF],
  29124 => ['urn-c2pa-6b0b6f7c-d208-4316-9954-28ade937f9bd-adobe', '2026-10-04 08:04 created (Adobe Firefly)', $FF],
  28978 => ['urn-c2pa-3ab9fee6-53a4-44eb-a319-a3c7df133c57-adobe', '2026-10-04 07:56 created (Adobe Firefly)', $FF],
  28976 => ['urn-c2pa-02505cba-2c22-40b8-8717-e8d83396a642-adobe', '2026-10-04 07:58 created (Adobe Firefly)', $FF],
  28974 => ['urn-c2pa-721ce27f-cc05-48ee-917d-6f4d90a32501-adobe', '2026-10-04 07:59 created (creative upsampler)', $UP],
  28972 => ['urn-c2pa-ce6bdeb3-8079-4a5a-8f8b-c0805c909f25-adobe', '2026-10-04 08:01 created (creative upsampler)', $UP],
];
$todo = [];
foreach ($A as $id => [$urn, $steps, $method]) {
  $a = Asset::find()->id($id)->one();
  $state = !$a ? 'missing' : (trim((string)$a->contentCredentials) !== '' ? 'recorded already' : 'record');
  echo "#$id " . ($a?->filename ?? '?') . ": $state" . PHP_EOL;
  if ($state === 'record') { $todo[$id] = [$a, $urn, $steps, $method]; }
}
if (!$APPLY) { return; }
$n = 0;
foreach ($todo as $id => [$a, $urn, $steps, $method]) {
  $a->setFieldValues(['enhancementMethod' => $method, 'enhancedDate' => '2026-10-04',
    'contentCredentials' => "Adobe content credential (C2PA), $M$urn\nSteps recorded (UTC): $steps. Read from the file as handed over; the copy stored here has lost the manifest."]);
  if (!$el->saveElement($a)) { throw new \RuntimeException("#$id " . json_encode($a->getFirstErrors())); } $n++;
}
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('record_missed_credentials_2026_10_04.php', $n, 'verified', 'Firefly edits recorded on seven portraits imported since 28 September (the scan had not read their originals)');
echo "done: $n" . PHP_EOL;
