/**
 * Nathan, 8 October 2026:
 *   "Set both portraits. Pete Knight's interview frame is a real photograph of him from the original site. Vasquez's 1874
 *   portrait is real and unedited, and the thin provenance goes on the record as what we know rather than being a reason
 *   to show nothing."
 *   "Yes, Wicks and Kellar as you propose. Legacy file back in the asset, enlargement as its own linked asset, López
 *   pattern."
 *
 * - Pete Knight #29314: asset #31240 (gif/sg042504.jpg) his portrait.
 * - Tiburcio Vasquez #285: asset #16 his portrait, its source saying what is known: the file came with the earlier
 *   WordPress build, uploaded there in March 2026, and nothing before that is recorded.
 * - Randy Wicks (#31252) and Bob Kellar (#27852): on 6 October each asset's file was replaced in place by an Adobe Firefly
 *   enlargement. As with Chico López (#31220 and #31474): the asset gets back the original site's file, as a web copy made
 *   from the master on Reggie (wicks_kellar_copies_2026_10_08.sh, checked against the manifest) with the master's
 *   checksum; the enlargement becomes its own asset, from the file Nathan supplied (inventory/incoming/done), with
 *   provenanceKind outside, its own checksum, enhancementMethod, the content credential read from that file, and
 *   enhancedFrom the original. On each person record the enlargement is the portrait and the original goes to the
 *   related images, as on López's. Kellar's photograph record #27853 keeps the original: it is the photograph.
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the two web copies made from the masters on Reggie', "$root/storage/runtime/photo-import/restore"],
  ['file', 'the two Firefly files Nathan supplied, hashed here', "$root/inventory/incoming/done"],
  ['record', 'Craft fields sourceChecksum, legacySourcePath, provenanceKind, enhancementMethod, contentCredentials, enhancedFrom, filename', 'the stored files', 'read'],
  ['record', 'the content credentials (Adobe manifests)', 'the two supplied files', 'not read: read from the files with scan_content_credentials.py on 8 October and written in below'],
]);
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$save = function ($e) use ($el) { if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); } };
$hasOn = fn($e, $h) => (bool)$e->getFieldLayout()->getFieldByHandle($h);

/* Portraits */
$VAS = "Uploaded to the archive's earlier WordPress build in March 2026 (wp-content/uploads/2026/03/tiburcio-vasquez.jpg) and brought into this archive with it. Where the file came from before then is not recorded: no photographer, studio, date or holding institution is known to the archive.";
foreach ([[29314, 31240, null], [285, 16, $VAS]] as [$eid, $aid, $src]) {
  $e = Entry::find()->id($eid)->status(null)->one(); $a = Asset::find()->id($aid)->one();
  $feat = $e->featuredImage->status(null)->ids();
  echo "#$eid {$e->title}: portrait " . ($feat === [$aid] ? 'set already' : ($feat ? 'has ' . json_encode($feat) . ', refused' : "#$aid {$a->filename}")) . PHP_EOL;
  if ($src !== null) { echo "  #$aid source " . ((string)$a->getFieldValue('source') === $src ? 'written already' : "now: $src") . PHP_EOL; }
  if (!$APPLY) continue;
  if ($src !== null && (string)$a->getFieldValue('source') !== $src) { $a->setFieldValue('source', $src); $save($a); $n++; }
  if (!$feat) { $e->setFieldValue('featuredImage', [$aid]); $save($e); $n++; }
}

/* Wicks and Kellar, the López pattern */
$CC = [
  31252 => 'https://cai-manifests.adobe.com/manifests/urn-c2pa-caab608d-eb99-4891-be8e-1e20f0fde9e4-adobe : EDITED from an original (generative steps present), by Adobe Firefly; steps: 2026-10-06 14:38 created (creative-upsampler)',
  27852 => 'https://cai-manifests.adobe.com/manifests/urn-c2pa-bace92da-e6eb-4b05-a03c-bd034ffee678-adobe : EDITED from an original (generative steps present), by Adobe Firefly; steps: 2026-10-06 14:22 created (creative-upsampler)',
];
$P = [
  31252 => ['randywicks1995_karzinphoto_large-copy-2026-10-06.jpg', '81353d9d60bc161b519e3c0ab7287d3522a26ff9441ad59478daae79f6b7c4e5', [18663]],
  27852 => ['sc1310.jpg', '054c6fbf904d9388f3c1d0bdfab8bd00d9348d0279271a34584b93196d6ae3f4', [21944]],
];
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
foreach ($P as $oid => [$supplied, $master, $persons]) {
  $o = Asset::find()->id($oid)->one();
  $sup = "$root/inventory/incoming/done/$supplied"; $web = "$root/storage/runtime/photo-import/restore/{$o->filename}";
  if (!is_file($sup) || !is_file($web)) { echo "#$oid: supplied file or web copy missing, left" . PHP_EOL; continue; }
  $supSum = hash_file('sha256', $sup);
  $encName = preg_replace('/\.jpg$/', '', $o->filename) . '-enhanced.jpg';
  $enh = Asset::find()->folderId($folder->id)->filename($encName)->one();
  $restored = (string)$o->getFieldValue('sourceChecksum') === "sha256:$master";
  echo "#$oid {$o->filename}: enlargement " . ($enh ? "exists #{$enh->id}" : "to its own asset $encName (sha256:" . substr($supSum, 0, 12) . ')') . '; original ' . ($restored ? 'restored already' : 'back to the original site\'s file, sha256:' . substr($master, 0, 12)) . PHP_EOL;
  if (!$APPLY) { foreach ($persons as $pid) echo "  #$pid: portrait the enlargement, original to related images" . PHP_EOL; continue; }
  if (!$enh) {
    $tmp = sys_get_temp_dir() . "/$encName"; copy($sup, $tmp);
    $enh = new Asset(); $enh->tempFilePath = $tmp; $enh->setFilename($encName); $enh->newFolderId = $folder->id; $enh->setVolumeId($vol->id);
    $enh->setScenario(Asset::SCENARIO_CREATE); $enh->avoidFilenameConflicts = false; $save($enh);
    $enh = Asset::find()->id($enh->id)->one();
    $enh->title = $o->title . ', enlarged'; $enh->alt = $o->alt;
    $v = ['provenanceKind' => 'outside', 'acquiredDate' => '2026-10-06', 'sourceChecksum' => "sha256:$supSum",
      'source' => 'Enhanced by Nathan Imhoff in Adobe Firefly on October 6, 2026, from the original held in the archive, and supplied by him the same day.',
      'enhancementMethod' => "Adobe Firefly: enlarged with Firefly's creative upsampler. Generative steps can add detail the photograph did not record.",
      'contentCredentials' => $CC[$oid], 'enhancedFrom' => [$oid]];
    foreach (['license', 'rightsHolder', 'rightsNote', 'photoCredit', 'courtesyOf', 'photoCaptionExt', 'photoSourceCode', 'photoDate'] as $h) {
      if ($hasOn($o, $h) && (string)$o->getFieldValue($h) !== '') { $v[$h] = $o->getFieldValue($h); }
    }
    $h = array_map(fn($f) => $f->handle, $enh->getFieldLayout()->getCustomFields()); $enh->setFieldValues(array_intersect_key($v, array_flip($h)));
    $save($enh); $n++;
  }
  if (!$restored) {
    $tmp = sys_get_temp_dir() . '/' . $o->filename; copy($web, $tmp);
    Craft::$app->getAssets()->replaceAssetFile($o, $tmp, $o->filename);
    $o = Asset::find()->id($oid)->one();
    $base = trim(preg_replace('/\s*The file was replaced on October 6, 2026.*$/s', '', (string)$o->getFieldValue('source')));
    $o->setFieldValues(['sourceChecksum' => "sha256:$master", 'enhancementMethod' => '', 'contentCredentials' => '',
      'source' => mb_substr("$base Restored on 8 October 2026 to this file: on 6 October it had been replaced in place by an Adobe Firefly enlargement, which is now its own asset, #{$enh->id}, linked to this one.", 0, 500)]);
    $save($o); $n++;
  }
  foreach ($persons as $pid) {
    $e = Entry::find()->id($pid)->status(null)->one();
    $feat = $e->featuredImage->status(null)->ids(); $imgs = $e->recordImages->status(null)->ids();
    if ($feat === [$enh->id] && in_array($oid, $imgs)) { continue; }
    $e->setFieldValue('featuredImage', [$enh->id]);
    $e->setFieldValue('recordImages', array_values(array_unique(array_merge([$oid], array_diff($imgs, [$enh->id])))));
    $save($e); $n++;
  }
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('portraits_and_lopez_pattern_2026_10_08.php', $n, 'verified', 'Pete Knight and Vasquez portraits; Wicks and Kellar to the López pattern'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
