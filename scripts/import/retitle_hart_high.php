/**
 * Hart High School is what it is called (Nathan, 30 September 2026): #16052 is
 * retitled "Hart High School", its slug follows, "William S. Hart High School"
 * becomes an alias, and /organizations/william-s-hart-high-school redirects
 * (config/redirects.php, same commit). The school map matches on the NCES id,
 * not the title, so nothing else moves.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retitle_hart_high.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$e = Entry::find()->id(16052)->status(null)->one();
if (!$e || !in_array($e->title, ['William S. Hart High School', 'Hart High School'], true)) { echo 'REFUSING: #16052 is not Hart High' . PHP_EOL; return; }
$aliases = array_values(array_filter(array_map('trim', preg_split('~[|\n]~', (string)$e->orgAliases))));
$want = array_values(array_unique(array_merge(['William S. Hart High School'], array_diff($aliases, ['Hart High School']))));
$done = $e->title === 'Hart High School' && $e->slug === 'hart-high-school' && $want == $aliases;
echo "#16052 \"{$e->title}\" ({$e->slug}) -> \"Hart High School\" (hart-high-school); aliases: " . implode(' | ', $aliases) . ' -> ' . implode(' | ', $want) . ($done ? ' (already done)' : '') . PHP_EOL;
if (Entry::find()->section('organizations')->slug('hart-high-school')->id('not 16052')->exists()) { echo 'REFUSING: another record has the slug hart-high-school' . PHP_EOL; return; }
if (!$APPLY || $done) { echo str_repeat('=', 78) . PHP_EOL . ($done ? 'nothing to do.' : 'nothing was written. Set $APPLY = true to apply.') . PHP_EOL; return; }
$e->title = 'Hart High School'; $e->slug = 'hart-high-school';
$e->setFieldValue('orgAliases', implode("\n", $want));
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); }
$r = Entry::find()->id(16052)->one();
$ok = $r->title === 'Hart High School' && $r->slug === 'hart-high-school' && str_contains((string)$r->orgAliases, 'William S. Hart High School');
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('retitle_hart_high.php', 1, $ok ? 'verified' : 'SHORT', 'William S. Hart High School -> Hart High School');
if (!$ok) { throw new \RuntimeException('retitle_hart_high: read-back failed'); }
