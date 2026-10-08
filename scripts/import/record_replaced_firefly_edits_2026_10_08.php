/**
 * Eight portraits whose legacy file was replaced in place on 6 October 2026 by a Firefly output, recorded then as "a better
 * copy of the same image". Each file's content credential (read 8 October from Adobe's manifest server with
 * scan_content_credentials.py) records generative steps. The images stay in place for Nathan's decision one by one
 * (Nathan, 8 October: "hold them in place for now but list them"); this only makes each record say what was done.
 * Two more replaced the same way carried text_to_image steps and were taken off (pull_generated_portraits_2026_10_08.php).
 * Dry run by default; set $APPLY = true.
 */
$APPLY = false;
$E = [
  31454 => ['Generate Fill', 'https://cai-manifests.adobe.com/manifests/urn-c2pa-50e9f2c1-aa17-4ab2-b65d-9a647df85895-adobe'],
  31254 => ['Firefly Image 5 edit, then Generate Fill, enlarged with the creative upsampler', 'https://cai-manifests.adobe.com/manifests/urn-c2pa-21d128d9-74d9-43d5-9a4a-b9d75fa9253a-adobe'],
  31243 => ['Firefly Image 5 edit, then Generate Fill', 'https://cai-manifests.adobe.com/manifests/urn-c2pa-5bc84292-3cf3-4a39-851e-d3d3a31d0aa3-adobe'],
  27865 => ['Firefly Image 5 edit, then Generate Fill, enlarged with the creative upsampler', 'https://cai-manifests.adobe.com/manifests/urn-c2pa-cdaa7d14-0d82-4eb6-8190-27e4c1b21b06-adobe'],
  31242 => ['Firefly Image 5 edit', 'https://cai-manifests.adobe.com/manifests/urn-c2pa-d14cfcc5-d7e8-42ed-b825-9b36bba38984-adobe'],
  31236 => ['Firefly Image 5 edit, enlarged with the creative upsampler', 'https://cai-manifests.adobe.com/manifests/urn-c2pa-693b51aa-d2a7-4e9c-9109-d84697f25e54-adobe'],
  31252 => ['enlarged with the creative upsampler', null],
  27852 => ['enlarged with the creative upsampler', null],
];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL; $n = 0;
foreach ($E as $id => [$how, $cc]) {
  $a = craft\elements\Asset::find()->id($id)->one(); $l = $a->getFieldLayout();
  $src = (string)$a->getFieldValue('source');
  // The source said "a better copy of the same image"; it was a Firefly output. The phrase is corrected in place (the field
  // holds 500 characters), and what was done goes in enhancementMethod and contentCredentials.
  $new = str_replace('by a better copy of the same image supplied by Nathan Imhoff.', 'by an Adobe Firefly output supplied by Nathan Imhoff (corrected 8 October 2026; see enhancementMethod). The unedited image is the file at the path above.', $src);
  if (mb_strlen($new) > 500) { $new = str_replace(' The unedited image is the file at the path above.', '', $new); }
  if (mb_strlen($new) > 500) { $new = str_replace('by a better copy of the same image supplied by Nathan Imhoff.', 'by a Firefly output (see enhancementMethod).', $src); }
  if ($new === $src) { echo "#$id already corrected or phrase not found" . PHP_EOL; continue; }
  echo "#$id {$a->filename}: + $how" . PHP_EOL;
  if (!$APPLY) continue;
  $a->setFieldValue('source', $new);
  if ($l->getFieldByHandle('enhancementMethod') && (string)$a->getFieldValue('enhancementMethod') === '') $a->setFieldValue('enhancementMethod', "Adobe Firefly: $how (from the content credential; recorded 8 October 2026)");
  if ($cc && $l->getFieldByHandle('contentCredentials') && (string)$a->getFieldValue('contentCredentials') === '') $a->setFieldValue('contentCredentials', "$cc : EDITED from an original (generative steps present), by Adobe Firefly; $how");
  if (!Craft::$app->getElements()->saveElement($a)) { throw new \RuntimeException("#$id " . json_encode($a->getFirstErrors())); }
  $n++;
}
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('record_replaced_firefly_edits_2026_10_08.php', $n, 'verified', "$n replaced portraits now say they are Firefly outputs"); }
