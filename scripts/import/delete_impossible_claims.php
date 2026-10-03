/**
 * Impossible claims are deleted, not withheld (Nathan, 3 October 2026:
 * "Anything in that category should be deleted rather than withheld, with a
 * note saying what it claimed and why it is impossible, so nobody restores it
 * later").
 *
 * Reads scripts/import/removed-claims.json. For each claim: the sentence comes
 * out of the field, and a note on the record ("Removed, 2026") quotes it and
 * says why it cannot be true. check_removed_claims.php, in check_render, fails
 * if the sentence ever comes back.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/delete_impossible_claims.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$R = json_decode((string)file_get_contents("$root/scripts/import/removed-claims.json"), true)['claims'] ?? [];
$note = fn($c) => 'An earlier version of this record said: "' . $c['claim'] . '" That cannot be true. ' . $c['why'] . ' The sentence has been removed.';
$bad = []; $plan = [];
foreach ($R as $c) {
    $e = Entry::find()->id($c['record'])->status(null)->one();
    if (!$e || $e->title !== $c['title']) { $bad[] = "#{$c['record']} is not {$c['title']}"; continue; }
    $v = (string)$e->getFieldValue($c['field']);
    $notes = array_column($e->editorNotes ?? [], 'note');
    $in = str_contains($v, $c['claim']); $noted = in_array($note($c), $notes, true);
    if (!$in && $noted) { echo "#{$c['record']} {$c['title']}: already removed\n"; continue; }
    /* Removed before, its note since reworded: the note is brought up to date. */
    $old = array_values(array_filter($notes, fn($n) => str_starts_with((string)$n, 'An earlier version of this record said: "' . $c['claim'] . '"')));
    if (!$in && $old) { $plan[] = $c + ['renote' => $old[0]]; echo "#{$c['record']} {$c['title']}: note reworded\n   note: " . $note($c) . PHP_EOL; continue; }
    if (!$in) { $bad[] = "#{$c['record']}: the claim is not in {$c['field']} and no note says it was removed"; continue; }
    $plan[] = $c;
    echo "#{$c['record']} {$c['title']}: remove \"{$c['claim']}\"\n   note: " . $note($c) . PHP_EOL;
}
if (preg_match('~\x{2014}~u', implode('', array_map($note, $R)))) { $bad[] = 'an em dash in a note'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($plan as $c) {
    $e = Entry::find()->id($c['record'])->status(null)->one();
    if (!empty($c['renote'])) {
        $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => ((string)($r['note'] ?? '')) === $c['renote'] ? $note($c) : (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
        $e->setFieldValue('editorNotes', $rows);
        if (!Craft::$app->getElements()->saveElement($e) || !in_array($note($c), array_column(Entry::find()->id($c['record'])->status(null)->one()->editorNotes ?? [], 'note'), true)) { $short[] = "#{$c['record']} renote"; }
        continue;
    }
    $v = (string)$e->getFieldValue($c['field']);
    $v = preg_replace('~\s{2,}~', ' ', str_replace($c['claim'], '', $v));
    $v = preg_replace('~ +\n~', "\n", $v);
    $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
    $rows[] = ['heading' => 'Removed, 2026', 'position' => 'bottom', 'note' => $note($c)];
    $e->setFieldValues([$c['field'] => trim($v), 'editorNotes' => $rows]);
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#{$c['record']} " . json_encode($e->getFirstErrors()); continue; }
    $r = Entry::find()->id($c['record'])->status(null)->one();
    if (str_contains((string)$r->getFieldValue($c['field']), $c['claim']) || !in_array($note($c), array_column($r->editorNotes ?? [], 'note'), true)) { $short[] = "#{$c['record']} read-back"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' claims removed') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('delete_impossible_claims.php', count($plan), $short ? 'SHORT' : 'verified', 'impossible claims deleted with a note (registry: removed-claims.json)');
if ($short) { throw new \RuntimeException('delete_impossible_claims: ' . implode(', ', $short)); }
