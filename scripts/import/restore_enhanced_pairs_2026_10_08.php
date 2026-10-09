/**
 * Seventeen enhanced portraits back to the enhanced pair (Nathan, 8 October 2026: "Restore the 17 to the enhanced pair. Dry
 * run then apply."). The 8 October rule on Firefly edits (pull_firefly_edits_2026_10_08.php) took them off and made each
 * record's original its portrait; it overrode the enhanced-pair rule of 5 October (DATA-MODEL) without saying so (ERRORLOG).
 * Every one is Nathan's own work in Adobe Firefly, made from the original the record holds.
 * For each: the enhanced asset is the portrait again, its enhancedFrom points at the original again (so the page says
 * "Enhanced from the original. See the original", in the band and the sidebar), the original is among the related images,
 * and the 8 October removal note in the enhanced asset's source becomes a line saying it was taken off and put back.
 * Not touched: the seven with text-prompt steps (Nathan deciding), the generated portraits, the no-original cases.
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$reads = require "$root/scripts/import/_reads.php";
$reads([['record', 'Craft fields source, enhancedFrom and filename on each enhanced asset, and each record\'s featuredImage and recordImages', 'the image files', 'not read: the files were matched by eye on 6 and 8 October (pull_firefly_edits_2026_10_08.php); here only the links between them change']]);
/* person id => enhanced asset id */
$E = [333 => 27381, 323 => 27387, 21584 => 27383, 279 => 27396, 29316 => 31447, 307 => 28814, 15808 => 31427, 18726 => 31395,
  15919 => 31472, 16140 => 31449, 30219 => 31404, 2585 => 31423, 15477 => 31398, 20224 => 31387, 18702 => 31408, 321 => 31406, 16432 => 31391];
$NOTE = ' Taken off its record on 8 October 2026 under a rule on Firefly edits, and put back the same day on Nathan\'s word (the enhanced-pair rule of 5 October 2026).';
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($E as $pid => $eid) {
  $e = Entry::find()->id($pid)->status(null)->one(); $a = Asset::find()->id($eid)->one();
  $src = (string)$a->getFieldValue('source');
  if (!preg_match('/Made from asset #(\d+)/', $src, $m)) { $from = $a->getFieldValue('enhancedFrom')->ids(); $oid = $from[0] ?? null; } else { $oid = (int)$m[1]; }
  $feat = $e->featuredImage->status(null)->ids(); $imgs = $e->recordImages->status(null)->ids();
  if (!$oid) { echo "#$pid {$e->title}: no original named, refused" . PHP_EOL; continue; }
  if ($feat !== [$oid] && $feat !== [$eid]) { echo "#$pid {$e->title}: portrait is " . json_encode($feat) . ", neither the original #$oid nor the enhanced #$eid; refused" . PHP_EOL; continue; }
  $o = Asset::find()->id($oid)->one();
  $newSrc = trim(preg_replace('/\s*Taken off every record on 8 October 2026.*$/s', '', $src));
  if (!str_contains($newSrc, 'put back the same day')) { $newSrc = mb_substr($newSrc . $NOTE, 0, 500); }
  $done = $feat === [$eid] && $a->getFieldValue('enhancedFrom')->ids() === [$oid] && in_array($oid, $imgs) && $newSrc === $src;
  echo "#$pid {$e->title}: " . ($done ? 'restored already' : "portrait #$eid {$a->filename} (enhanced), from #$oid {$o->filename}, the original " . (in_array($oid, $imgs) ? 'already in' : 'into') . ' related images') . PHP_EOL;
  if (!$APPLY || $done) { continue; }
  $a->setFieldValues(['enhancedFrom' => [$oid], 'source' => $newSrc]);
  if (!$el->saveElement($a)) { throw new \RuntimeException("#$eid " . json_encode($a->getFirstErrors())); } $n++;
  $e->setFieldValue('featuredImage', [$eid]);
  $e->setFieldValue('recordImages', array_values(array_unique(array_merge([$oid], array_diff($imgs, [$eid])))));
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$pid " . json_encode($e->getFirstErrors())); } $n++;
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('restore_enhanced_pairs_2026_10_08.php', $n, 'verified', '17 enhanced portraits back to the enhanced pair'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
