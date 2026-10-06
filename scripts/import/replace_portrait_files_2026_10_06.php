/**
 * Portraits whose file name does not say "enhanced" (Nathan, 6 October 2026: "If it does not, the touch-up was not worth noting: just
 * replace the image, no pair, no note"). Where the archive already holds the same image, the better file replaces the old one inside
 * the existing asset (Craft's replace-file), so every record that shows it, a photograph record included, gets the better copy and
 * nothing is duplicated (Nathan: sc1310 and sc9611 "are better scans of images we hold rather than new ones"). The asset's checksum
 * follows the new file, and its source says the file was replaced.
 * Jerry Gladbach (#28336): retitled from "E. G. Gladbach" (Nathan: "retitle it to Jerry Gladbach and keep E. G. Gladbach as an alias,
 * plus a search name so both find him"), with a note on how the sources name him, and his portrait, new.
 * Idempotent: a replaced asset whose checksum already matches its file is skipped. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/replace_portrait_files_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $as = Craft::$app->getAssets(); $n = 0; $IN = "$root/inventory/incoming";
/* [incoming file, asset to replace, the record it heads (to check)] */
$R = [
  ['lw2427_large.jpg', 31242, 29284], ['randywicks1995_karzinphoto_large.jpg', 31252, 18663], ['sc1310.jpg', 27852, 21944], ['sc9611.jpg', 27865, 15737],
  ['danhon.jpg', 14933, 18616], ['darrylmanzer2020.jpg', 31254, 2579], ['sk5003_large.jpg', 31255, 28675], ['stroup_clara.jpg', 31243, 28713],
  ['sg19720614claffey01_zoom.jpg', 31236, 30211],
];
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
/* Jerry Gladbach */
$g = Entry::find()->id(28336)->status(null)->one(); if (!$g) { throw new \RuntimeException('no #28336'); }
$NOTE = 'The County\'s returns print his name as "E G GLADBACH" (the general election of November 8, 2016). The archive\'s other sources call him Jerry: SCV Water announced "the passing of Board Vice President Jerry Gladbach" (news release, July 18, 2022); the City\'s 2016 results on SCVHistory.com print "E.G. \'Jerry\' Gladbach"; and Leon Worden wrote in 1996 that "the water board candidate is Jerry Gladbach. Gerald, his middle name" (SCVHistory.com, /scvhistory/signal/worden/old/lw102396.htm).';
$v = [];
if ((string)$g->fullName !== 'Jerry Gladbach') { $v['fullName'] = 'Jerry Gladbach'; }
if (!str_contains((string)$g->personAliases, 'E. G. Gladbach')) { $v['personAliases'] = trim((string)$g->personAliases . "\nE. G. Gladbach"); }
if (!str_contains((string)$g->personSearchNames, 'E G Gladbach')) { $v['personSearchNames'] = trim((string)$g->personSearchNames . "\nE G Gladbach\nE.G. Gladbach\nEdward G. Gladbach"); }
$rows = array_values(array_filter(array_map(fn($x) => ['heading' => (string)$x['heading'], 'note' => (string)$x['note'], 'position' => (string)$x['position'] ?: 'bottom'], iterator_to_array($g->editorNotes ?? [])), fn($x) => $x['note'] !== ''));
if (!in_array($NOTE, array_column($rows, 'note'), true)) { $rows[] = ['heading' => 'His name', 'note' => $NOTE, 'position' => 'bottom']; $v['editorNotes'] = $rows; }
echo '#28336 ' . $g->title . ': ' . ($v ? implode(', ', array_keys($v)) : 'done already') . PHP_EOL;
$pa = Asset::find()->filename('jerry-gladbach-portrait.jpg')->one(); echo 'jerry-gladbach-portrait.jpg: ' . ($pa ? "exists #{$pa->id}" : 'import') . PHP_EOL;
if ($APPLY) {
  if ($v) { $g->setFieldValues($v); if (!$el->saveElement($g)) { throw new \RuntimeException('#28336 ' . json_encode($g->getFirstErrors())); } $n++; }
  if (!$pa) {
    $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = $as->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
    $tmp = sys_get_temp_dir() . '/jerry-gladbach-portrait.jpg'; copy("$IN/jerry-gladbach-portrait.jpg", $tmp);
    $pa = new Asset(); $pa->tempFilePath = $tmp; $pa->setFilename('jerry-gladbach-portrait.jpg'); $pa->newFolderId = $folder->id; $pa->setVolumeId($vol->id); $pa->setScenario(Asset::SCENARIO_CREATE); $pa->avoidFilenameConflicts = false;
    if (!$el->saveElement($pa)) { throw new \RuntimeException(json_encode($pa->getFirstErrors())); }
    $pa = Asset::find()->id($pa->id)->one(); $pa->title = 'Jerry Gladbach, portrait'; $pa->alt = 'Portrait of Jerry Gladbach';
    $pv = ['provenanceKind' => 'outside', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$IN/jerry-gladbach-portrait.jpg"),
      'source' => 'Supplied by Nathan Imhoff on October 6, 2026. Where the photograph was first published is not recorded with it.'];
    $ah = array_map(fn($f) => $f->handle, $pa->getFieldLayout()->getCustomFields()); $pa->setFieldValues(array_intersect_key($pv, array_flip($ah)));
    if (!$el->saveElement($pa)) { throw new \RuntimeException(json_encode($pa->getFirstErrors())); } $n++;
  }
  $g = Entry::find()->id(28336)->status(null)->one();
  if ($g->featuredImage->one()?->id !== $pa->id) { $g->setFieldValue('featuredImage', [$pa->id]); if (!$el->saveElement($g)) { throw new \RuntimeException('#28336 portrait'); } $n++; }
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('replace_portrait_files_2026_10_06.php', $n, 'verified', 'better copies of nine portraits; Jerry Gladbach retitled, with his portrait'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
