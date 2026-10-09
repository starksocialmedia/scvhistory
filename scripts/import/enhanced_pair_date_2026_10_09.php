/**
 * The date of the enhanced-pair rule corrected in the source sentence of the 17 restored enhanced portraits (Nathan,
 * 9 October 2026: "correct it to 6 October on all 17 assets. The wrong date came from my brief"). The rule is of 6 October
 * (docs/DATA-MODEL.md; commit 5c4b99c, 6 October 2026 08:41); restore_enhanced_pairs_2026_10_08.php wrote "5 October".
 * Changes only those words in each asset's source field. Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\Asset;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$reads = require "$root/scripts/import/_reads.php";
$reads([['record', 'the source sentence and filename on each of the 17 enhanced assets', 'the enhanced files', 'not read: the filename only labels each line; only the sentence about the rule\'s date changes, and nothing is said about the files']]);
$IDS = [27381, 27387, 27383, 27396, 31447, 28814, 31427, 31395, 31472, 31449, 31404, 31423, 31398, 31387, 31408, 31406, 31391];
$OLD = 'the enhanced-pair rule of 5 October 2026'; $NEW = 'the enhanced-pair rule of 6 October 2026';
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL; $c = ['to correct' => 0, 'correct already' => 0, 'neither (truncated or absent)' => 0];
foreach (Asset::find()->id($IDS)->all() as $a) {
  $s = (string)$a->source;
  $k = str_contains($s, $OLD) ? 'to correct' : (str_contains($s, $NEW) ? 'correct already' : 'neither (truncated or absent)'); $c[$k]++;
  echo "#{$a->id} {$a->filename}: $k" . ($k[0] === 'n' ? ' | ends: ' . mb_substr($s, -90) : '') . PHP_EOL;
  if (!$APPLY || $k !== 'to correct') { continue; }
  $a->setFieldValue('source', str_replace($OLD, $NEW, $s));
  if (!$el->saveElement($a)) { throw new \RuntimeException("#{$a->id} " . json_encode($a->getFirstErrors())); } $n++;
}
echo json_encode($c) . PHP_EOL;
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('enhanced_pair_date_2026_10_09.php', $n, 'verified', 'enhanced-pair rule date corrected to 6 October on the restored assets'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
