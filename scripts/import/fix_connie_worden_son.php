/**
 * Connie Worden #16418: her son, said because sources say it (Nathan,
 * 3 October 2026: "make sure the profile says it because a source does, not
 * because it is known").
 *
 * Her obituary (obituary #28045) says "Connie is survived by her son, Leon
 * Worden"; that he founded SCVHistory.com rests on his profile in this archive
 * (person #279) and its source, the St. Francis Dam National Memorial
 * Foundation's biography of him (2020). The profile's sentence had cited the
 * obituary for both. It now cites each for what it says, and says that the
 * obituary, carried on the site her son founded, does not name its writer.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_connie_worden_son.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$p = Entry::find()->id(16418)->status(null)->one();
$OLD = 'She was the mother of Leon Worden, who built SCVHistory.com.[1]';
$NEW = 'Her son is Leon Worden, the founder of SCVHistory.com.[1][5] Her obituary on the site, which this profile follows, does not name its writer.[1]';
$N5 = 'Leon Worden\'s profile, person #279 in this archive, and its source: St. Francis Dam National Memorial Foundation, "Leon Worden Biography," 22 February 2020, https://stfrancisdammemorial.org/leon-worden-biography/.';
$bad = [];
$ob = Entry::find()->id(28045)->status(null)->one();
if (!$ob || !str_contains(preg_replace('~\s+~', ' ', (string)$ob->body), 'Connie is survived by her son, Leon Worden')) { $bad[] = 'obituary #28045 does not read as quoted'; }
$lw = Entry::find()->id(279)->one();
if (!$lw || !str_contains(strip_tags((string)$lw->body), 'founder and editor of SCVHistory.com') || !in_array(true, array_map(fn($r) => str_contains((string)($r['note'] ?? ''), 'Leon Worden Biography'), $lw->footnotes ?? []), true)) { $bad[] = 'person #279 does not read as quoted'; }
$done = str_contains((string)$p->body, $NEW);
if (!$done && !str_contains((string)$p->body, $OLD)) { $bad[] = 'the profile does not read as written'; }
echo $done ? "already done\n" : "#16418: \"$OLD\" -> \"$NEW\"; note 5 added\n";
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($done) { return; }
$fn = array_values(array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? '')], array_filter($p->footnotes ?? [], 'is_array')));
$fn[] = ['number' => (string)(count($fn) + 1), 'note' => $N5, 'source' => 'editorial-2026'];
$p->setFieldValues(['body' => str_replace($OLD, $NEW, (string)$p->body), 'footnotes' => $fn]);
$ok = Craft::$app->getElements()->saveElement($p) && str_contains((string)Entry::find()->id(16418)->status(null)->one()->body, $NEW);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_connie_worden_son.php', 1, $ok ? 'verified' : 'SHORT', 'Connie Worden: her son and his founding of the site each cited to what says it');
if (!$ok) { throw new \RuntimeException('fix_connie_worden_son: read-back failed'); }
