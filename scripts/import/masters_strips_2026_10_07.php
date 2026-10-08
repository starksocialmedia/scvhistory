/**
 * Follow-on to masters_off_web_root_2026_10_07.php, same night. The 2,400-pixel long-side cap made 34 tall strips, mostly
 * newspaper columns, unreadable: lat19320522piru_large.jpg went from 1600 x 7785 to 493 x 2400, and lw3030c_large.jpg came out
 * smaller than the picture it enlarges, so its magnifier dropped out of the index. A strip more than twice as long as it is
 * wide now gets the pixel count of a 2,400 by 1,800 picture instead (lat19320522piru: 942 x 4585), made from the file in
 * storage/masters into storage/runtime/photo-import/web-strips (list in strips.json beside it). The asset keeps its id; its
 * size and dimensions are updated and its transforms cleared. Rebuild enlarge.json after.
 * Idempotent: a file whose web copy already has the planned dimensions is done. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/masters_strips_2026_10_07.php'))"
 */
use craft\elements\Asset;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $WEB = "$root/web/uploads/archive-media/legacy"; $NEW = "$root/storage/runtime/photo-import/web-strips";
$c = ['strips' => 0, 'done already' => 0, 'replace' => 0, 'refused' => 0];
foreach (json_decode(file_get_contents("$root/storage/runtime/photo-import/strips.json"), true) as $r) {
  $c['strips']++; $f = $r['file']; $a = Asset::find()->filename($f)->folderPath('legacy/')->one();
  if (!$a || !is_file("$NEW/$f") || !is_file("$root/storage/masters/archive-media/legacy/$f")) { $c['refused']++; echo "  refused: $f" . PHP_EOL; continue; }
  if ((int)$a->width === (int)$r['W'] && (int)$a->height === (int)$r['H']) { $c['done already']++; continue; }
  $c['replace']++; echo "  $f {$a->width} x {$a->height} -> {$r['W']} x {$r['H']}" . PHP_EOL;
  if (!$APPLY) { continue; }
  if (!copy("$NEW/$f", "$WEB/$f")) { throw new \RuntimeException("copy $f"); }
  [$w, $h] = getimagesize("$WEB/$f"); Craft::$app->getImageTransforms()->deleteAllTransformData($a);
  $a->setWidth($w); $a->setHeight($h); $a->size = filesize("$WEB/$f"); $a->dateModified = new \DateTime();
  if (!$el->saveElement($a)) { throw new \RuntimeException("#{$a->id} " . json_encode($a->getFirstErrors())); }
}
foreach ($c as $k => $v) { echo str_pad($k, 14) . $v . PHP_EOL; }
if ($APPLY && $c['replace']) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('masters_strips_2026_10_07.php', $c['replace'], 'verified', '34 tall strips: web copies by pixel count, not long side'); }
