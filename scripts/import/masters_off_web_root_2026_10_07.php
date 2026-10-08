/**
 * The magnifier's masters off the web root (Nathan, 7 October 2026: "they should not be publicly fetchable at full resolution
 * by anyone walking the uploads directory, and the web root is not where masters belong").
 *
 * The 1,787 _large and _orig files in web/uploads/archive-media/legacy are the files the magnifier opens. They are not byte
 * copies of the originals: Craft re-saved each on upload (sc8801_large.jpg: the mirror's file matches the drive manifest, the
 * uploaded one does not). 1,785 have their original on Reggie under the same name (inventory/raw/scvhistory-manifest-
 * 2026-08-20.sha256); adrian-w-adams-hm7301_large.jpg came from Nathan (inventory/incoming), and johnwoodhouseaudubon_large.jpg
 * is on neither, so its file in storage/masters is the only copy held.
 *
 * For each: the file on the web root moves to storage/masters/archive-media/legacy/ (off the web root, out of git, nothing
 * deleted), and a web copy takes its place under the same name: 2,400 pixels on the long side at most, JPEG quality 82, made
 * with sips by storage/runtime/photo-import/make_web_copy.sh. Where the web copy came out no smaller (12 files, all at or
 * under 2,400 pixels), the file already there stays as the web copy. The asset keeps its id, so every relation and the
 * magnifier index hold; its size and dimensions are updated and its transforms cleared. legacySourcePath already names the
 * master on Reggie; sourceChecksum, where empty, gets the master's sha256 from the drive manifest (the file as received).
 * After the apply, rebuild templates/_data/enlarge.json (build_enlarge_index.php), which carries each target's dimensions.
 * Idempotent: a file already in storage/masters is done. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/masters_off_web_root_2026_10_07.php'))"
 */
use craft\elements\Asset;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$WEB = "$root/web/uploads/archive-media/legacy"; $OFF = "$root/storage/masters/archive-media/legacy"; $COPIES = "$root/storage/runtime/photo-import/web";
$rows = []; foreach (json_decode(file_get_contents("$root/storage/runtime/photo-import/masters.json"), true) as $r) { $rows[strtolower($r['file'])] = $r; }
$man = [];
$fh = fopen("$root/inventory/raw/scvhistory-manifest-2026-08-20.sha256", 'r');
while (($l = fgets($fh)) !== false) { $i = strpos($l, '  ./'); if ($i === false) { continue; } $b = strtolower(basename(rtrim(substr($l, $i + 4)))); if (isset($rows[$b])) { $man[$b][] = substr($l, 0, 64); } }
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$assets = Asset::find()->volumeId($vol->id)->folderPath('legacy/')->filename(['*_large.*', '*_orig.*'])->limit(null)->all();
$c = ['assets' => count($assets), 'done already' => 0, 'web copy' => 0, 'kept as web copy' => 0, 'checksum set' => 0, 'refused' => 0]; $bytes = [0, 0]; $refused = [];
if ($APPLY && !is_dir($OFF)) { mkdir($OFF, 0775, true); }
foreach ($assets as $a) {
  $k = strtolower($a->filename); $r = $rows[$k] ?? null; $web = "$WEB/{$a->filename}"; $off = "$OFF/{$a->filename}"; $copy = "$COPIES/{$a->filename}";
  if (is_file($off)) { $c['done already']++; continue; }
  if (!$r || !is_file($web) || !is_file($copy) || filesize($web) !== (int)$r['bytes']) { $c['refused']++; $refused[] = $a->filename . (!$r ? ' (not in masters.json)' : (!is_file($web) ? ' (no file on the web root)' : (!is_file($copy) ? ' (no web copy)' : ' (file changed since the check)'))); continue; }
  $keep = (int)$r['webBytes'] >= (int)$r['bytes'];
  $c[$keep ? 'kept as web copy' : 'web copy']++; $bytes[0] += (int)$r['bytes']; $bytes[1] += $keep ? (int)$r['bytes'] : (int)$r['webBytes'];
  $sum = trim((string)$a->getFieldValue('sourceChecksum')); $setSum = $sum === '' && count($man[$k] ?? []) === 1;
  if ($setSum) { $c['checksum set']++; }
  if (!$APPLY) { continue; }
  if (!rename($web, $off)) { throw new \RuntimeException("move {$a->filename}"); }
  if (!copy($keep ? $off : $copy, $web)) { rename($off, $web); throw new \RuntimeException("web copy {$a->filename}"); }
  [$w, $h] = getimagesize($web);
  Craft::$app->getImageTransforms()->deleteAllTransformData($a);
  $a->setWidth($w); $a->setHeight($h); $a->size = filesize($web); $a->dateModified = new \DateTime();
  if ($setSum) { $a->setFieldValue('sourceChecksum', 'sha256:' . $man[$k][0]); }
  if (!$el->saveElement($a)) { throw new \RuntimeException("#{$a->id} " . json_encode($a->getFirstErrors())); }
}
foreach ($c as $k => $v) { echo str_pad($k, 18) . $v . PHP_EOL; }
printf("on the web root: %.2f GB of masters -> %.2f GB of web copies%s", $bytes[0] / 1e9, $bytes[1] / 1e9, PHP_EOL);
foreach ($refused as $x) { echo "  refused: $x" . PHP_EOL; }
if ($APPLY && ($c['web copy'] + $c['kept as web copy'])) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('masters_off_web_root_2026_10_07.php', $c['web copy'] + $c['kept as web copy'], 'verified', 'magnifier masters to storage/masters; web copies at 2,400 px in their place'); }
