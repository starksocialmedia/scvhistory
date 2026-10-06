/**
 * Chico López's portrait, and the confusion it causes said on both López records (Nathan, 6 October 2026: "import US8502 as
 * Chico López's portrait ... his own page carries a California Historical Society portrait identified by the Society's card
 * file, and it is the photograph often mistaken for the gold discoverer. Say that on both records ... Withdraw the no-likeness
 * note"). The note was held, never written; nothing to withdraw in the data.
 * The file: gif/us8502_orig.jpg (2149 by 2749), the scan linked from /scvhistory/us8502.htm, whose caption reads "Half-tone
 * print in the California Historical Society collection, sourced to Title Insurance and Trust and C.C. Pierce Photography
 * Collection, 1860-1960" and quotes the Society's card file, which identifies him as the father of Marie S. Lopez de Cummings
 * and grandson of Claudio Lopez. Copied to inventory/incoming/us8502_orig.jpg, checksum matching the mirror.
 * - Asset us8502_orig.jpg in legacy/, provenance as the portrait batch of 5 October; Chico López's (#28132) portrait.
 * - Both "Not to be confused with" notes say which record the portrait belongs to; the gold discoverer's (#18834) says that no
 *   likeness of him is known and what was searched (the portrait census of 5 October 2026).
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/chico_lopez_portrait_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$FILE = "$root/inventory/incoming/us8502_orig.jpg"; $SHA = 'e005221f49b0610c46aa';
$C = Entry::find()->id(28132)->status(null)->one(); $D = Entry::find()->id(18834)->status(null)->one();
if ($C?->title !== 'Francisco "Chico" López' || $D?->title !== 'Francisco Lopez' || !is_file($FILE) || !str_starts_with(hash_file('sha256', $FILE), $SHA)) { throw new \RuntimeException('records or file not as expected'); }
$CAP = 'Francisco "Chico" Lopez, Cousin of Gold Discoverer. Half-tone print in the California Historical Society collection, sourced to Title Insurance and Trust and C.C. Pierce Photography Collection, 1860-1960. CHS card file entry reads: "He was the father of Marie S. Lopez de Cummings and grandson of Claudio Lopez who came to mission San Gabriel as assistant to Father Salordio Juan." This photograph has often been wrongly identified as Francsico Lopez, the gold discoverer.';
$a = Asset::find()->filename('us8502_orig.jpg')->one();
echo 'asset us8502_orig.jpg: ' . ($a ? "exists #{$a->id}" : 'import (2149 x 2749)') . PHP_EOL;
if ($APPLY && !$a) {
  $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
  $tmp = sys_get_temp_dir() . '/us8502_orig.jpg'; copy($FILE, $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename('us8502_orig.jpg'); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = 'Francisco "Chico" López, portrait'; $a->alt = 'Portrait of Francisco "Chico" López';
  $v = ['provenanceKind' => 'legacy-mirror', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established.', 'rightsHolder' => 'California Historical Society',
    'legacySourcePath' => 'gif/us8502_orig.jpg', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $FILE),
    'source' => 'SCVHistory.com, gif/us8502_orig.jpg, the scan linked from the photograph on /scvhistory/us8502.htm: "US8502: 9600 dpi jpeg from digital image in USC Digital Library; California Historical Society catalog No. CHS-8502." Copied from the legacy mirror on 6 October 2026.',
    'photoCaptionExt' => $CAP, 'photoCredit' => 'California Historical Society, CHS-8502, via the USC Digital Library', 'photoSourceCode' => 'US8502', 'courtesyOf' => 'California Historical Society'];
  $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++;
}
$cur = $C->featuredImage->one(); echo "#28132 portrait: " . ($cur ? ($a && $cur->id === $a->id ? 'set already' : "has #{$cur->id}, refused") : 'set') . PHP_EOL;
if ($APPLY && !$cur) { $C->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($C)) { throw new \RuntimeException('#28132'); } $n++; }
$NC = 'Not to be confused with his kinsman Francisco López (born 1802), who discovered gold in Placerita Canyon in 1842, person #18834. The portrait on this record, a half-tone print in the California Historical Society\'s collection (CHS-8502), is identified as Chico by the Society\'s card file, which names him the father of Marie S. Lopez de Cummings and grandson of Claudio Lopez (SCVHistory.com, US8502, /scvhistory/us8502.htm). It is the photograph most often published as the gold discoverer, and Jerry Reynolds gives the discoverer Chico\'s nickname.';
$ND = 'Not to be confused with his kinsman Francisco "Chico" López (about 1820-1900), who kept a stock ranch at Elizabeth Lake, person #28132. The portrait most often published as the gold discoverer, a half-tone print in the California Historical Society\'s collection (CHS-8502), is Chico\'s: the Society\'s card file identifies him, and it is the portrait on his record (SCVHistory.com, US8502, /scvhistory/us8502.htm). No likeness of the gold discoverer is known: searched on 5 October 2026 were his own legacy page, every page on SCVHistory.com that names him, the site\'s image files by name, and the archive\'s own images; the photographs of the 1959 Placerita pageant show an actor playing him. Jerry Reynolds gives the discoverer Chico\'s nickname.';
foreach ([[$C, $NC], [$D, $ND]] as [$e, $new]) {
  $rows = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes)), fn($r) => $r['note'] !== ''));
  $i = array_search('Not to be confused with', array_column($rows, 'heading'), true);
  if ($i === false) { throw new \RuntimeException("#{$e->id}: no note to replace"); }
  if ($rows[$i]['note'] === $new) { echo "#{$e->id}: note done already\n"; continue; }
  echo "#{$e->id} {$e->title}: note now\n  $new\n"; $rows[$i]['note'] = $new;
  if ($APPLY) { $e->setFieldValue('editorNotes', $rows); if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id}"); } $n++; }
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('chico_lopez_portrait_2026_10_06.php', $n, 'verified', 'US8502 as Chico López\'s portrait; both López notes'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
