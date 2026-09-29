/**
 * Take the Bowers Cave column off the site until tribal consultation.
 *
 * Jerry Reynolds' column of 14 December 1984, record #2177, tells readers how to
 * find an archaeological site. Grok flagged it for consultation before
 * migration (inventory/review/live-errors.md, RL8), and Nathan said not to
 * import it (29 September 2026), but it was already imported, and live at
 * /articles/bowers-cave. Disabling takes the page off the site and keeps the
 * record, its text and its relations intact, so the decision after
 * consultation is still open. Nothing is deleted.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/disable_bowers_cave.php'))"
 */
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$e = \craft\elements\Entry::find()->id(2177)->status(null)->one();
if (!$e || $e->title !== 'Bowers Cave' || !str_ends_with((string)$e->legacyUrl, 'reynolds121484.htm')) { echo 'REFUSING: #2177 is not the Bowers Cave column' . PHP_EOL; return; }
if (!$e->enabled) { echo 'already disabled' . PHP_EOL; return; }
echo '#2177 Bowers Cave (' . $e->url . '): enabled -> disabled, pending tribal consultation' . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$e->enabled = false;
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('disable_bowers_cave: save failed'); }
$ok = !\craft\elements\Entry::find()->id(2177)->status(null)->one()->enabled;
echo 'READ-BACK ' . ($ok ? 'OK: disabled' : 'SHORT: still enabled') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('disable_bowers_cave.php', 1, $ok ? 'verified' : 'SHORT', 'Bowers Cave column off the site pending tribal consultation');
