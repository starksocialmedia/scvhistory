/**
 * Two disagreements on Edward Guy North's record (#1384) that the layout
 * mockup showed and the pilot's table lacked (2 October 2026):
 *   Born      Leon Worden's text says April 25, 1890; the draft card and the
 *             Honor Roll card both say June 25 (notes 1 and 2).
 *   Entered   Leon's text says he enlisted at Tonopah on June 5, 1917; June 5,
 *             1917 is the date of his draft registration there (note 1). The
 *             Honor Roll card gives Camp Lewis as the camp he was assigned to.
 * The text is not rewritten; the table says it. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_north_differences.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$e = Entry::find()->id(1384)->status(null)->one();
$t = preg_replace('~\s+~', ' ', strip_tags((string)$e->body));
$bad = [];
foreach (['was born in Newhall on April 25, 1890', 'he enlisted in the Army at Tonopah on June 5, 1917'] as $ph) { if (!str_contains($t, $ph)) { $bad[] = "the text does not read \"$ph\""; } }
$BORN = 'Leon Worden\'s text says April 25. The draft card and the Honor Roll card both say June 25.';
$ROW = ['fact' => 'Entered service', 'value' => 'Camp Lewis, Washington', 'notes' => '1, 2', 'agreement' => 'Leon Worden\'s text says he enlisted at Tonopah on June 5, 1917. That is the date of his draft registration there (note 1); the Honor Roll card gives Camp Lewis as the camp he was assigned to (note 2).'];
$rows = array_map(fn($r) => ['fact' => (string)($r['fact'] ?? ''), 'value' => (string)($r['value'] ?? ''), 'notes' => (string)($r['notes'] ?? ''), 'agreement' => (string)($r['agreement'] ?? '')], $e->factSources ?? []);
$changed = false;
foreach ($rows as $i => $r) { if ($r['fact'] === 'Born' && $r['agreement'] === '') { $rows[$i]['agreement'] = $BORN; $changed = true; echo "Born: add the difference\n"; } }
if (!in_array('Entered service', array_column($rows, 'fact'), true)) {
    $at = array_search('Unit', array_column($rows, 'fact')); array_splice($rows, $at === false ? count($rows) : $at + 1, 0, [$ROW]); $changed = true; echo "Entered service: add the row after Unit\n";
}
if (!$changed) { echo "already done\n"; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if (!$changed) { return; }
$e->setFieldValue('factSources', $rows);
$ok = Craft::$app->getElements()->saveElement($e) && in_array('Entered service', array_column(Entry::find()->id(1384)->status(null)->one()->factSources ?? [], 'fact'), true);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_north_differences.php', 1, $ok ? 'verified' : 'SHORT', 'North: born and entered-service differences in the facts table');
if (!$ok) { throw new \RuntimeException('add_north_differences: read-back failed'); }
