/**
 * Bill Cooper's SCV Water term (#27422) began in January 2023, not December
 * 2022 (Nathan, 3 October 2026: "change Cooper's start to January 2023 and
 * note that our December 2022 was read from the count while the board page
 * says January"). The board's page gives "Term Expires: January 2027" and four-
 * year terms (fix_gibbs_cooper_terms.php); a term ending January 2027 began
 * January 2023. The footnote says what changed and why.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_cooper_term_start.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$h = Entry::find()->id(27422)->status(null)->one();
$OLD = 'the start is the month after, read from the count.';
$NEW = 'the term began in January 2023: the board\'s page gives four-year terms ending in January 2027. (This record first gave December 2022, the month after the election, read from the count.)';
$bad = [];
if (!$h || $h->holdingPerson->one()?->title !== 'Bill Cooper') { $bad[] = '#27422 is not Bill Cooper\'s'; }
$all = implode(' ', array_column($h->footnotes ?? [], 'note'));
$done = (string)$h->termStartEdtf === '2023-01' && str_contains($all, $NEW);
if (!$done && (!str_contains($all, $OLD) || (string)$h->termStartEdtf !== '2022-12')) { $bad[] = 'start "' . $h->termStartEdtf . '" or footnote not as read'; }
echo $done ? "already done\n" : "#27422 start 2022-12 -> 2023-01 (January 2023); footnote says why\n";
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($done) { return; }
$fn = array_values(array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => str_replace($OLD, $NEW, (string)($r['note'] ?? '')), 'source' => (string)($r['source'] ?? '')], array_filter($h->footnotes ?? [], 'is_array')));
$h->setFieldValues(['termStart' => 'January 2023', 'termStartEdtf' => '2023-01', 'footnotes' => $fn]);
$ok = Craft::$app->getElements()->saveElement($h) && (string)Entry::find()->id(27422)->status(null)->one()->termStartEdtf === '2023-01';
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_cooper_term_start.php', 1, $ok ? 'verified' : 'SHORT', 'Cooper SCV Water term start January 2023 (board page), not December 2022 (read from the count)');
if (!$ok) { throw new \RuntimeException('fix_cooper_term_start: read-back failed'); }
