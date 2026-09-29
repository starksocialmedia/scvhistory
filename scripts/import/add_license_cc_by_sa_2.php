/**
 * The licence option CC BY-SA 2.0, added because an image carrying it arrived:
 * the City Hall photograph from Flickr (29 September 2026). A version is added
 * when an image needs it, never guessed (add_asset_provenance_fields.php).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_license_cc_by_sa_2.php'))"
 */
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$fs = Craft::$app->getFields();
$lic = $fs->getFieldByHandle('license');
$have = array_map(fn($o) => $o['value'], $lic->options);
if (in_array('cc-by-sa-2.0', $have, true)) { echo 'already there' . PHP_EOL; return; }
echo 'license: add cc-by-sa-2.0, Creative Commons Attribution-ShareAlike 2.0 (keeps ' . count($have) . ' options)' . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$lic->options = array_merge($lic->options, [['label' => 'Creative Commons Attribution-ShareAlike 2.0 (CC BY-SA 2.0)', 'value' => 'cc-by-sa-2.0', 'default' => false]]);
if (!$fs->saveField($lic)) { throw new \RuntimeException('add_license_cc_by_sa_2: ' . json_encode($lic->getFirstErrors())); }
$now = array_map(fn($o) => $o['value'], $fs->getFieldByHandle('license')->options);
$ok = in_array('cc-by-sa-2.0', $now, true) && !array_diff($have, $now);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_license_cc_by_sa_2.php', 1, $ok ? 'verified' : 'SHORT', 'licence option CC BY-SA 2.0');
