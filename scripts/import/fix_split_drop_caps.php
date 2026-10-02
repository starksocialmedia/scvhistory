/**
 * Split drop caps (Nathan, 1 October 2026: "[lines] and [/lines] markers appear
 * in article bodies"). Most markers are correct: [lines] keeps a block's line
 * breaks, and the leaks were in the description and the lead, fixed in the
 * templates. But in 68 fields the import split an article's drop cap from its
 * word: a lone capital, a blank line, then a [lines] block starting "ew New..."
 * or "ttorney...". The page printed "N [lines] ew".
 *
 * Decisions are in inventory/review/split-drop-caps-2026-10-02.json, one per
 * case: join the letter to the word ("Attorney", "If", "Around 1942"), keep a
 * space ("A coin"), drop a letter the text already repeats ("A title"), or hold
 * one whose original could not be read. Each replacement is the exact string
 * found, so a body edited since is left alone and reported.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_split_drop_caps.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$cases = json_decode((string)file_get_contents(\Craft::getAlias('@root') . '/inventory/review/split-drop-caps-2026-10-02.json'), true)['cases'] ?? [];
$plan = []; $miss = []; $count = ['join' => 0, 'space' => 0, 'drop' => 0, 'hold' => 0];
foreach ($cases as $c) {
    $count[$c['action']]++;
    if ($c['action'] === 'hold') { echo "   hold #{$c['id']}: {$c['why']}" . PHP_EOL; continue; }
    $find = $c['letter'] . "\n\n[lines]\n" . $c['next'];
    $repl = "[lines]\n" . ($c['action'] === 'join' ? $c['letter'] . $c['next'] : ($c['action'] === 'space' ? $c['letter'] . ' ' . $c['next'] : $c['next']));
    $plan[$c['id']][$c['field']][] = [$find, $repl];
}
foreach ($plan as $id => $fields) {
    $e = Entry::find()->id($id)->status(null)->one();
    foreach ($fields as $h => $pairs) { foreach ($pairs as [$f, $r]) { if (!str_contains((string)$e->getFieldValue($h), $f)) { $miss[] = "#$id $h"; } } }
}
echo 'cases: ' . json_encode($count) . '; records to change: ' . count($plan) . PHP_EOL;
echo 'not found as recorded (already fixed or edited since): ' . ($miss ? implode(', ', $miss) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$done = 0; $short = [];
foreach ($plan as $id => $fields) {
    $e = Entry::find()->id($id)->status(null)->one(); $changed = false;
    foreach ($fields as $h => $pairs) {
        $v = (string)$e->getFieldValue($h);
        foreach ($pairs as [$f, $r]) { if (str_contains($v, $f)) { $v = str_replace($f, $r, $v); $changed = true; } }
        $e->setFieldValue($h, $v);
    }
    if (!$changed) { continue; }
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id"; continue; }
    $done++;
    foreach ($fields as $h => $pairs) { foreach ($pairs as [$f, $r]) { if (str_contains((string)Entry::find()->id($id)->status(null)->one()->getFieldValue($h), $f)) { $short[] = "#$id $h"; } } }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $done records") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_split_drop_caps.php', $done, $short ? 'SHORT' : 'verified', 'split drop caps rejoined in [lines] bodies; one held, 3 more found with an opening quotation mark');
if ($short) { throw new \RuntimeException('fix_split_drop_caps: ' . implode(', ', $short)); }
