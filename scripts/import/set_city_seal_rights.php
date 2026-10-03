/**
 * Asset #41, the seal of the City of Santa Clarita, came from the WordPress
 * media library, not from the City; set_asset_provenance.php flagged it as the
 * City's rights and never filled the rights fields (Nathan, 3 October 2026:
 * "the earlier provenance script flagged it and never filled it"). This
 * records the City as rights holder. Its licence stays empty: an empty licence
 * is not permission, and no licence is known. Its role stays an archive record:
 * it is the copy the WordPress site held, not a mark fetched as current. The
 * City's current mark is a separate decision.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_city_seal_rights.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$a = \craft\elements\Asset::find()->id(41)->one();
if (!$a || $a->filename !== 'seal_of_santa_clarita_california.png') { echo 'REFUSING: #41 is not the City seal' . PHP_EOL; return; }
$have = trim((string)$a->rightsHolder);
echo "#41 {$a->filename}: rightsHolder " . ($have === '' ? 'empty; set to City of Santa Clarita' : "\"$have\" (left alone)") . PHP_EOL;
if (!$APPLY || $have !== '') { echo ($APPLY ? 'nothing to do' : 'nothing was written. Set $APPLY = true to apply.') . PHP_EOL; return; }
$a->setFieldValue('rightsHolder', 'City of Santa Clarita');
$ok = Craft::$app->getElements()->saveElement($a) && trim((string)\craft\elements\Asset::find()->id(41)->one()->rightsHolder) === 'City of Santa Clarita';
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('set_city_seal_rights.php', 1, $ok ? 'verified' : 'SHORT', 'asset #41 City seal: rightsHolder City of Santa Clarita');
