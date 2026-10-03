/**
 * The WordPress-origin bodies outside the persons section, withheld (Nathan,
 * 3 October 2026: "audit every WordPress-origin record the same way, sentence
 * by sentence, and withhold the suspect ones").
 *
 * inventory/review/wordpress-records-2026-10-03.md (audit_wordpress_records.py)
 * found 25 live records outside persons whose body is still the WordPress text
 * and is a copy of no legacy page: 6 organizations, 8 places, 9 groups, one
 * event, one war memorial record. Their sentence grades cannot clear them: a
 * sentence "found" means a page holds its dates and names, not that it agrees
 * (Rancho El Tejon's 1855 Beale claim grades found and contradicts Reynolds).
 * They are withheld as the 21 person bodies are: the text moves to
 * withheldBody, which no template reads, and body is emptied. Nothing is
 * deleted. Rudy Acosta's memorial keeps Leon Worden's own narrative
 * (wmNarrative). The five organization copies of the place records
 * (#382 #386 #388 #398 #400) are disabled and are not touched.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/withhold_wordpress_bodies.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$IDS = [378, 380, 390, 392, 396, 402, 16446, 16511, 16515, 16517, 16506, 926, 932, 934, 913, 915, 938, 940, 942, 944, 946, 948, 950, 875, 526];
$bad = []; $plan = [];
foreach ($IDS as $id) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || !$e->getFieldLayout()->getFieldByHandle('withheldBody')) { $bad[] = "#$id missing or without withheldBody"; continue; }
    $b = trim((string)$e->body); $w = trim((string)$e->withheldBody);
    if ($b === '' && $w !== '') { echo "#$id {$e->title}: already withheld\n"; continue; }
    if ($b === '') { $bad[] = "#$id has no body"; continue; }
    if ($w !== '' && $w !== $b) { $bad[] = "#$id withheldBody already holds other text"; continue; }
    $plan[] = $id; echo "#$id {$e->section->handle} {$e->title}: " . str_word_count(strip_tags($b)) . " words -> withheldBody\n";
}
echo count($plan) . ' to withhold' . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id) {
    $e = Entry::find()->id($id)->status(null)->one(); $b = (string)$e->body;
    $e->setFieldValues(['withheldBody' => $b, 'body' => '']);
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id " . json_encode($e->getFirstErrors()); continue; }
    $r = Entry::find()->id($id)->status(null)->one();
    if (trim((string)$r->body) !== '' || (string)$r->withheldBody !== $b) { $short[] = "#$id read-back"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' withheld') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('withhold_wordpress_bodies.php', count($plan), $short ? 'SHORT' : 'verified', 'WordPress-origin bodies outside persons withheld (moved to withheldBody): 6 organizations, 8 places, 9 groups, an event, a war memorial');
if ($short) { throw new \RuntimeException('withhold_wordpress_bodies: ' . implode(', ', $short)); }
