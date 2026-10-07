/**
 * faultRecord takes every section (Nathan, 7 October 2026: "Widen the source-fault link to every section. Six of thirteen links
 * already point outside what the field accepts, and a save in the control panel silently dropping one is exactly the class of
 * fault we have been digging out all week."). Its settings accepted elections, candidacies and documents; scripts had linked
 * faults to persons, events, photographs and a fallen officer.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/widen_fault_record_2026_10_07.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $n = 0; $f = $fs->getFieldByHandle('faultRecord');
echo 'faultRecord sources: ' . json_encode($f->sources) . ($f->sources === '*' ? ' (all)' : ' -> all') . PHP_EOL;
if ($APPLY && $f->sources !== '*') { $f->sources = '*'; if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++; }
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('widen_fault_record_2026_10_07.php', $n, 'verified', 'faultRecord takes every section'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
