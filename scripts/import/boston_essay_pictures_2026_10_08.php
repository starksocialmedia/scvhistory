/**
 * The pictures printed beside John Boston's essays, linked as related images where the archive holds the same file (Nathan,
 * 8 October 2026: "The eight picture boxes are other records' photographs, so linking them as related rather than importing
 * them is right"). Nine boxes on the original pages (boston-essays-2026-10-08.json, picturesSetAside). Two are in Craft under
 * the file name the page used: ch1070.jpg (Alex Mentry) and sd2401_large.jpg (the enlargement of sd2401.jpg, Deputy Constable
 * Ed Brown). The other seven (delvalle_ygnacio, tsd0200, pardee_ed, pilcher_jack1, sd2402, as2501, rn3001a) are not assets;
 * the archive's other del Valle and Pilcher pictures are different files, and are not linked on a guess.
 * Adds to recordImages; never removes. Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$reads = require "$root/scripts/import/_reads.php";
$reads([['record', 'assets found by filename (ch1070.jpg, sd2401_large.jpg) and the essays\' recordImages', 'the pictures on the original pages', 'not read: matched by the file name the page used; both were looked at when imported']]);
$L = [38519 => 'ch1070.jpg', 38513 => 'sd2401_large.jpg'];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($L as $aid => $file) {
  $art = Entry::find()->id($aid)->status(null)->one(); $a = Asset::find()->filename($file)->one();
  if (!$art || !$a) { echo "#$aid or $file missing, left" . PHP_EOL; continue; }
  $imgs = $art->recordImages->status(null)->ids();
  echo "#$aid {$art->title}: related image #{$a->id} $file " . (in_array($a->id, $imgs) ? 'linked already' : 'to link') . PHP_EOL;
  if (!$APPLY || in_array($a->id, $imgs)) { continue; }
  $art->setFieldValue('recordImages', array_merge($imgs, [$a->id]));
  if (!$el->saveElement($art)) { throw new \RuntimeException("#$aid " . json_encode($art->getFirstErrors())); } $n++;
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('boston_essay_pictures_2026_10_08.php', $n, 'verified', 'two pictures beside Boston\'s essays linked as related images'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
