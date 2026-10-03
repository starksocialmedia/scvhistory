/**
 * Cave Johnson Couts (#323): say plainly how thin his tie is (Nathan,
 * 3 October 2026: "A drive ending at San Jose makes 'the Santa Clara' more
 * likely the northern valley, and quoting Reynolds without placing it is
 * honest. If that is the only tie he has, say so plainly on his record rather
 * than leaving a reader to infer he was here").
 *
 * It is the only tie: his 1849 drive went by the coast, and Reynolds does not
 * say whose herds crossed the Newhall Pass in 1850. One sentence is added to
 * the end of the opening paragraph; nothing else changes.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_couts_tie.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$e = \craft\elements\Entry::find()->id(323)->status(null)->one();
$anchor = 'Jerry Reynolds quotes the letter in his history of this valley; the drive reached San Jose on July 12.[1]';
$add = ' That letter is his only tie to this valley in the archive\'s sources, and it may not be one: "the Santa Clara" may as well be the valley around San Jose, where the drive ended, and no source here places Couts in the Santa Clarita Valley.';
$body = (string)$e->body;
if (str_contains($body, $anchor . $add)) { echo 'already says so' . PHP_EOL; return; }
if (substr_count($body, $anchor) !== 1) { echo 'REFUSING: the opening paragraph is not the one this extends' . PHP_EOL; return; }
echo 'add after the letter: ' . $add . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$e->setFieldValue('body', str_replace($anchor, $anchor . $add, $body));
$ok = Craft::$app->getElements()->saveElement($e) && str_contains((string)\craft\elements\Entry::find()->id(323)->status(null)->one()->body, $anchor . $add);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_couts_tie.php', 1, $ok ? 'verified' : 'SHORT', 'Couts: the 1852 letter is his only tie, and may not be one');
