/**
 * Eight legacy portraits back to their own files (Nathan, 8 October 2026: "restore Schmidt's and Hon's unedited files from
 * Reggie"; "Where an unedited original exists, restore it as the portrait"). On 6 October each asset's file was replaced in
 * place by an Adobe Firefly output, recorded as "a better copy of the same image", with the Firefly file's checksum filed as
 * the master's. Each asset gets back the file the original site published, as a web copy made from the master on Reggie
 * (restore_legacy_copies_2026_10_08.sh, checksums checked against the drive manifest), and its record is put right: the
 * master's checksum, a source that says what happened, no enhancementMethod or contentCredentials (the file is unedited).
 * Earl Schmidt's and Dan Hon's were taken off their records this morning; they go back where they were. The Firefly outputs
 * remain in inventory/incoming/done (and Adams's in inventory/sole-copies).
 * Dry run by default; set $APPLY = true.
 */
$APPLY = false;
$R = [
  31454 => ['57d261063726ac95935640bd78bbb8a9cfe3c01dce652ee50b94833999eb28d5', []],
  31254 => ['277c1c40a31ec5f1cfb7bfd7a5fc40cdbb0b6f310fd913445be700f1019ccd30', []],
  31243 => ['b9913e5c5409b34c643564b4fdab2dd548564c01989a012740dc2f31ee3d70d1', []],
  31242 => ['f78f8b9603069d6693b74de982ec38b04cf4b2d51c8ba381df399d7e762a4ea8', []],
  31236 => ['ab43c334fac034614dc272a27222d5514106adca5b35a85c2ceecb79cd976a1d', []],
  27865 => ['218df63bb871bb55d0e57224666610978c2afedb9f07ab4a35d54742956ac091', []],
  31255 => ['22537d7583379965cb124606a6c43bb287a45fdb1ed63cea067f7e72e4d62d48', [[28675, 'featuredImage']]],
  14933 => ['dc9090495e3a5e197c563daf64f19a46eb0a36e66774da9fccdf59debbff25c8', [[18616, 'featuredImage'], [12480, 'recordImages']]],
];
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($R as $id => [$sum, $put]) {
  $a = craft\elements\Asset::find()->id($id)->one(); $l = $a->getFieldLayout();
  $new = "$root/storage/runtime/photo-import/restore/{$a->filename}";
  if (!is_file($new)) { echo "#$id no web copy: left" . PHP_EOL; continue; }
  $src = (string)$a->getFieldValue('source');
  $base = trim(preg_replace('/\s*The file was replaced on October 6, 2026.*$/s', '', $src));
  $base = trim(preg_replace('/\s*Taken off every record on 8 October 2026.*$/s', '', $base));
  $note = "$base Restored on 8 October 2026 to this file: on 6 October it had been replaced by an Adobe Firefly output with generative edits, recorded then as a better copy; that output is no longer used.";
  $done = (string)$a->getFieldValue('sourceChecksum') === "sha256:$sum";
  echo "#$id {$a->filename}: " . ($done ? 'already restored' : "file back to the original site's, checksum sha256:" . substr($sum, 0, 12)) . ($put ? '; back on ' . implode(', ', array_map(fn($p) => "#{$p[0]} {$p[1]}", $put)) : '') . PHP_EOL;
  if (!$APPLY) continue;
  if (!$done) {
    $tmp = sys_get_temp_dir() . '/' . $a->filename; copy($new, $tmp);
    Craft::$app->getAssets()->replaceAssetFile($a, $tmp, $a->filename);
    $a = craft\elements\Asset::find()->id($id)->one();
    $v = ['sourceChecksum' => "sha256:$sum", 'source' => mb_substr($note, 0, 500)];
    if ($l->getFieldByHandle('enhancementMethod')) $v['enhancementMethod'] = '';
    if ($l->getFieldByHandle('contentCredentials')) $v['contentCredentials'] = '';
    $a->setFieldValues($v);
    if (!$el->saveElement($a)) { throw new \RuntimeException("#$id " . json_encode($a->getFirstErrors())); }
  }
  foreach ($put as [$eid, $h]) {
    $e = craft\elements\Entry::find()->id($eid)->status(null)->one(); $ids = $e->getFieldValue($h)->status(null)->ids();
    if (in_array($id, $ids)) continue;
    $e->setFieldValue($h, $h === 'featuredImage' ? [$id] : array_merge($ids, [$id]));
    if (!$el->saveElement($e)) { throw new \RuntimeException("#$eid " . json_encode($e->getFirstErrors())); }
  }
  $n++;
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('restore_legacy_files_2026_10_08.php', $n, 'verified', "$n legacy portraits back to their own files"); }
