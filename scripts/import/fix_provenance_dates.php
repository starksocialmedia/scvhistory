/**
 * Corrects a wrong date this session wrote into recordProvenance. The work of
 * build_profiles_batch4.php, build_profiles_batch5.php and
 * retire_305_add_chico_lopez.php was done on 3 October 2026 (the commits say
 * so); the strings said "4 Oct 2026". recordProvenance is internal; no public
 * text carried the wrong date. The same correction was made in CHANGELOG.md,
 * the scripts and the hash files in the same commit.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_provenance_dates.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$FROM = ['build_profiles_batch4.php, 4 Oct 2026', 'build_profiles_batch5.php, 4 Oct 2026', 'retire_305_add_chico_lopez.php, 4 Oct 2026'];
$TO = ['build_profiles_batch4.php, 3 Oct 2026', 'build_profiles_batch5.php, 3 Oct 2026', 'retire_305_add_chico_lopez.php, 3 Oct 2026'];
$plan = [];
foreach (Entry::find()->section('persons')->status(null)->limit(null)->all() as $e) {
    $v = (string)$e->recordProvenance; $n = str_replace($FROM, $TO, $v);
    if ($n !== $v) { $plan[] = [$e, $n]; echo "   #{$e->id} {$e->title}" . PHP_EOL; }
}
echo 'records to correct: ' . count($plan) . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$short = [];
foreach ($plan as [$e, $n]) { $e->setFieldValue('recordProvenance', $n); if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#{$e->id}"; } }
$left = 0; foreach (Entry::find()->section('persons')->status(null)->limit(null)->all() as $e) { foreach ($FROM as $f) { if (str_contains((string)$e->recordProvenance, $f)) { $left++; } } }
echo "READ-BACK wrong dates left: $left" . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_provenance_dates.php', count($plan), ($left || $short) ? 'SHORT' : 'verified', 'recordProvenance "4 Oct 2026" corrected to 3 Oct 2026 on ' . count($plan) . ' records');
