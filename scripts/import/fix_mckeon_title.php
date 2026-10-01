/**
 * #18791's title back to "Buck McKeon" (Nathan, 1 October 2026: predeploy
 * failed, "the record is not Buck McKeon"). build_mckeon_profile.php set
 * fullName to 'Howard P. "Buck" McKeon', and a person's title follows fullName,
 * so the title changed everywhere and the banner check, rightly, no longer
 * matched. The archive titles a person by the name people use; the formal name
 * stays in personAliases. fullName goes back to "Buck McKeon".
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_mckeon_title.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$e = \craft\elements\Entry::find()->id(18791)->status(null)->one();
if (!$e || $e->slug !== 'buck-mckeon') { echo 'REFUSING: #18791 is not buck-mckeon' . PHP_EOL; return; }
echo "#18791 title [{$e->title}], fullName [{$e->fullName}] -> both \"Buck McKeon\"; aliases keep: " . str_replace("\n", ' | ', (string)$e->personAliases) . PHP_EOL;
if ($e->title === 'Buck McKeon' && (string)$e->fullName === 'Buck McKeon') { echo 'nothing to do' . PHP_EOL; return; }
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$e->setFieldValue('fullName', 'Buck McKeon'); $e->title = 'Buck McKeon';
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); }
$r = \craft\elements\Entry::find()->id(18791)->status(null)->one();
$ok = $r->title === 'Buck McKeon' && str_contains((string)$r->personAliases, 'Howard P. "Buck" McKeon');
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT: [' . $r->title . ']') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_mckeon_title.php', 1, $ok ? 'verified' : 'SHORT', '#18791 titled Buck McKeon again');
if (!$ok) { throw new \RuntimeException('fix_mckeon_title: read-back failed'); }
