/**
 * The Bennett-Arcan Party (#948): attribute to Jerry Reynolds the figures only he
 * gives. build_blank_records_4.php (applied 3 October 2026, 10:29) stated as fact
 * that the party was "about twenty-five people" and that "thirteen adults and
 * seven children survived"; no second source in hand gives either, so by the
 * reliability rule (docs/PROFILES.md) they are his, and the text now says so.
 * The ages, March 1849 and October 7 rest on HS9401 and Leon Worden's chronology
 * as well, and are unchanged. Changes the second paragraph only.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_bennett_arcan_figures.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$OLD = 'Asahel Bennett had formed the wagon train with the Arcan family and others in March 1849, about twenty-five people bound west from Wisconsin. (Jerry Reynolds spells his name Asabel; Carl Wheat and Leon Worden, Asahel.) In southern Utah they took a shortcut called the Williams Road; the Jayhawker party, which came up alongside them on October 7, would not follow it. The Bennett-Arcan wagons broke down in the desert, and thirteen adults and seven children survived, sheltering under the wrecks and eating their oxen.[1][5][6]';
$NEW = 'Asahel Bennett had formed the wagon train with the Arcan family and others in March 1849, bound west from Wisconsin; Jerry Reynolds puts them at about twenty-five, and spells the name Asabel (Carl Wheat and Leon Worden write Asahel). In southern Utah they took a shortcut called the Williams Road, and the Jayhawker party, which met them on October 7, did not follow it. The Bennett-Arcan wagons broke down in the desert; by Reynolds\'s count thirteen adults and seven children survived, sheltering under the wrecks and eating their oxen.[1][5][6]';
$e = Entry::find()->id(948)->status(null)->one();
if (!$e || $e->title !== 'Bennett-Arcan Party') { echo 'REFUSING: #948 is not the Bennett-Arcan Party' . PHP_EOL; return; }
$body = (string)$e->body;
if (str_contains($body, $NEW)) { echo 'already attributed' . PHP_EOL; return; }
if (substr_count($body, $OLD) !== 1) { echo 'REFUSING: the second paragraph is not the one applied on 3 October' . PHP_EOL; return; }
echo 'replace paragraph 2: the party\'s size and the survivors attributed to Reynolds' . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$e->setFieldValue('body', str_replace($OLD, $NEW, $body));
$e->setFieldValue('recordProvenance', trim((string)$e->recordProvenance . '; fix_bennett_arcan_figures.php, 3 October 2026: Reynolds\'s figures attributed'));
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); }
$ok = str_contains((string)Entry::find()->id(948)->status(null)->one()->body, $NEW);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_bennett_arcan_figures.php', 1, $ok ? 'verified' : 'SHORT', 'Bennett-Arcan Party: Reynolds\'s figures attributed');
if (!$ok) { throw new \RuntimeException('fix_bennett_arcan_figures: read-back failed'); }
