/**
 * Retires the militaryProfiles section (Nathan, 5 October 2026: "Retire /military-profiles. A section that has never held
 * a record is a dead end"). It has never held an entry; its templates and every reference to it go in the same commit.
 * Its mp* fields are left in place, unused, to be removed with any other unused fields in one pass.
 * Refuses if the section holds any entry, in any status. Writes project config. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retire_military_profiles_2026_10_05.php'))"
 */
$APPLY = false;
$es = Craft::$app->getEntries(); $s = $es->getSectionByHandle('militaryProfiles');
$n = $s ? craft\elements\Entry::find()->section('militaryProfiles')->status(null)->trashed(null)->count() : 0;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ': militaryProfiles ' . ($s ? "exists, $n entries" : 'already gone') . PHP_EOL;
if (!$APPLY || !$s) { return; }
if ($n > 0) { echo "REFUSED: it holds $n entries\n"; return; }
if (!$es->deleteSection($s)) { throw new \RuntimeException('delete failed'); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('retire_military_profiles_2026_10_05.php', 1, 'verified', 'the militaryProfiles section retired: it never held a record');
echo "done\n";
