/**
 * CSUN #16347: drop the alias "California State University", which names the
 * whole system, not the campus (Nathan, 29 September 2026). "CSUN" is already
 * added by build_csun_record.php. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_csun_alias.php'))"
 */
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$e = \craft\elements\Entry::find()->id(16347)->status(null)->one();
if (!$e || $e->title !== 'California State University, Northridge') { echo 'REFUSING: #16347 is not CSUN' . PHP_EOL; return; }
$lines = array_values(array_filter(array_map('trim', preg_split('~[\n,;]~', (string)$e->orgAliases))));
$keep = array_values(array_filter($lines, fn($l) => $l !== 'California State University'));
if (count($keep) === count($lines)) { echo 'already done: no "California State University" alias' . PHP_EOL; return; }
echo 'orgAliases: ' . implode(' / ', $lines) . PHP_EOL . '         -> ' . implode(' / ', $keep) . PHP_EOL;
if (!in_array('CSUN', $keep, true)) { echo 'NOTE: CSUN is not among the aliases; apply build_csun_record.php first' . PHP_EOL; }
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$e->setFieldValue('orgAliases', implode("\n", $keep));
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('fix_csun_alias: save failed'); }
$now = array_map('trim', explode("\n", (string)\craft\elements\Entry::find()->id(16347)->status(null)->one()->orgAliases));
$ok = !in_array('California State University', $now, true);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT: alias still present') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_csun_alias.php', 1, $ok ? 'verified' : 'SHORT', 'dropped the system name from CSUN aliases');
