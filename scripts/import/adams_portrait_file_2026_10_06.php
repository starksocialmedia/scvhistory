/**
 * Adrian W. Adams's portrait, a better file (Nathan, 6 October 2026: "One plain, so just replace the image with no pair and no
 * note: adrian-w-adams-hm7301_large.jpg"). The new file replaces the old one inside his portrait asset (#31454), as
 * replace_portrait_files_2026_10_06.php did for nine others. And the orphan: the failed save earlier today left
 * web/uploads/archive-media/legacy/hm7301_large.jpg, a re-encoded copy no asset points at (Nathan: "Clear that orphan"). It is
 * deleted only if no asset in the volume has that filename.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/adams_portrait_file_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$file = 'adrian-w-adams-hm7301_large.jpg'; $path = "$root/inventory/incoming/$file"; $aid = 31454;
$a = Asset::find()->id($aid)->one(); $r = Entry::find()->id(28667)->status(null)->one();
if (!$a || !$r || !is_file($path) || $r->featuredImage->one()?->id !== $aid) { throw new \RuntimeException('asset, record, file or portrait not as expected'); }
$sha = 'sha256:' . hash_file('sha256', $path); [$w, $h] = getimagesize($path);
if ((string)$a->sourceChecksum === $sha) { echo "$file: done already\n"; }
else {
  echo "$file: replaces the file of #$aid ({$a->width} x {$a->height} -> $w x $h), {$r->title}'s portrait\n";
  if ($APPLY) {
    $tmp = sys_get_temp_dir() . '/' . $file; copy($path, $tmp); Craft::$app->getAssets()->replaceAssetFile($a, $tmp, $a->filename);
    $a = Asset::find()->id($aid)->one(); $a->setFieldValues(['sourceChecksum' => $sha, 'source' => trim((string)$a->source . ' The file was replaced on October 6, 2026 by a better copy of the same image supplied by Nathan Imhoff.')]);
    if (!$el->saveElement($a)) { throw new \RuntimeException("#$aid " . json_encode($a->getFirstErrors())); } $n++;
  }
}
$orphan = "$root/web/uploads/archive-media/legacy/hm7301_large.jpg";
$held = Asset::find()->filename('hm7301_large.jpg')->exists();
echo 'orphan hm7301_large.jpg: ' . (!is_file($orphan) ? 'gone already' : ($held ? 'an asset has this filename: left alone' : 'no asset points at it: delete')) . PHP_EOL;
if ($APPLY && is_file($orphan) && !$held) { unlink($orphan); $n++; }
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('adams_portrait_file_2026_10_06.php', $n, 'verified', 'Adrian W. Adams: a better portrait file; the orphan hm7301_large.jpg removed'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
