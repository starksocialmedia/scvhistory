/**
 * Portraits whose file name does not say "enhanced" (Nathan, 6 October 2026: "If it does not, the touch-up was not worth noting: just
 * replace the image, no pair, no note"). Where the archive already holds the same image, the better file replaces the old one inside
 * the existing asset (Craft's replace-file), so every record that shows it, a photograph record included, gets the better copy and
 * nothing is duplicated (Nathan: sc1310 and sc9611 "are better scans of images we hold rather than new ones"). The asset's checksum
 * follows the new file, and its source says the file was replaced.
 * Jerry Gladbach (#28336): retitled from "E. G. Gladbach" (Nathan: "retitle it to Jerry Gladbach and keep E. G. Gladbach as an alias,
 * plus a search name so both find him"), with a note on how the sources name him, and his portrait, new.
 * Idempotent: a replaced asset whose checksum already matches its file is skipped. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/atkins_portrait_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $as = Craft::$app->getAssets(); $n = 0; $IN = "$root/inventory/incoming";
/* [incoming file, asset to replace, the record it heads (to check)] */
$R = [];
foreach ($R as [$file, $aid, $rid]) {
  $a = Asset::find()->id($aid)->one(); $r = Entry::find()->id($rid)->status(null)->one(); $path = "$IN/$file";
  if (!$a || !$r || !is_file($path)) { echo "REFUSED $file: asset, record or file missing\n"; continue; }
  if ($r->featuredImage->status(null)->one()?->id !== $aid) { echo "REFUSED $file: #$aid is not #$rid's portrait\n"; continue; }
  $sha = 'sha256:' . hash_file('sha256', $path);
  if ((string)$a->sourceChecksum === $sha) { echo "$file: done already (#$aid)\n"; continue; }
  [$w, $h] = getimagesize($path);
  echo "$file: replaces the file of #$aid {$a->filename} ({$a->width} x {$a->height} -> $w x $h), {$r->title}'s portrait\n";
  if (!$APPLY) { continue; }
  $tmp = sys_get_temp_dir() . '/' . $file; copy($path, $tmp);
  $as->replaceAssetFile($a, $tmp, $a->filename);
  $a = Asset::find()->id($aid)->one();
  $a->setFieldValues(['sourceChecksum' => $sha, 'source' => trim((string)$a->source . ' The file was replaced on October 6, 2026 by a better copy of the same image supplied by Nathan Imhoff.')]);
  if (!$el->saveElement($a)) { throw new \RuntimeException("#$aid " . json_encode($a->getFirstErrors())); } $n++;
}
/* B.J. Atkins (#28316): his portrait, new; under the plain rule (no "enhanced" in the name), no pair and no note. */
$IN2 = $IN; $pa = Asset::find()->filename('bj-atkins.jpg')->one(); $b = Entry::find()->id(28316)->status(null)->one();
if ($b?->title !== 'BJ Atkins') { throw new \RuntimeException('no #28316'); }
echo 'BJ-Atkins.jpg: ' . ($pa ? "exists #{$pa->id}" : 'import') . '; portrait: ' . ($b->featuredImage->one() ? 'set' : 'set it') . PHP_EOL;
if ($APPLY) {
  if (!$pa) {
    $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = $as->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
    $tmp = sys_get_temp_dir() . '/bj-atkins.jpg'; copy("$IN/BJ-Atkins.jpg", $tmp);
    $pa = new Asset(); $pa->tempFilePath = $tmp; $pa->setFilename('bj-atkins.jpg'); $pa->newFolderId = $folder->id; $pa->setVolumeId($vol->id); $pa->setScenario(Asset::SCENARIO_CREATE); $pa->avoidFilenameConflicts = false;
    if (!$el->saveElement($pa)) { throw new \RuntimeException(json_encode($pa->getFirstErrors())); }
    $pa = Asset::find()->id($pa->id)->one(); $pa->title = 'BJ Atkins, portrait'; $pa->alt = 'Portrait of BJ Atkins';
    $pv = ['provenanceKind' => 'outside', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$IN/BJ-Atkins.jpg"),
      'source' => 'Supplied by Nathan Imhoff on October 6, 2026. Where the photograph was first published is not recorded with it.'];
    $ah = array_map(fn($f) => $f->handle, $pa->getFieldLayout()->getCustomFields()); $pa->setFieldValues(array_intersect_key($pv, array_flip($ah)));
    if (!$el->saveElement($pa)) { throw new \RuntimeException(json_encode($pa->getFirstErrors())); } $n++;
  }
  if (!$b->featuredImage->one()) { $b->setFieldValue('featuredImage', [$pa->id]); if (!$el->saveElement($b)) { throw new \RuntimeException('#28316'); } $n++; }
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('atkins_portrait_2026_10_06.php', $n, 'verified', 'BJ Atkins: portrait'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
