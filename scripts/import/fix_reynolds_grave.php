/**
 * Jerry Reynolds #281: the Find a Grave link (Nathan, 3 October 2026: "check it
 * and unlink if it does not match").
 *
 * Memorial 14716645, read 3 October 2026, is "Jerry Hugh Reynolds", born
 * October 20, 1947, in Contra Costa County, married to Marlene Joyce
 * Pangrazio. His Preface and LW2184 make him Gerald G. Reynolds, born July 16,
 * 1937, in Torrance, married to Myrna. The memorial's biography is plainly of
 * the historian (curator, "Pico Canyon Chronicles", cancer), but its name,
 * birth, spouse and age at death ("60"; Leon Worden gives 58, its own dates
 * 48-49) do not match. It is unlinked.
 *
 * The burial place, Eternal Valley Memorial Park, came from that memorial and
 * from nothing else: the mirror was searched for it near his name and holds
 * no other source. It stays on the record, marked unsourced, with a note.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_reynolds_grave.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$e = Entry::find()->id(281)->status(null)->one();
$URL = 'https://www.findagrave.com/memorial/14716645/jerry-hugh-reynolds';
$NOTE = 'His burial place, Eternal Valley Memorial Park in Newhall, has no reliable source. The only record that gives it is a Find a Grave memorial whose name, date and place of birth and spouse do not match his, so it is not linked here. No other source has been found.';
$bad = [];
if (!$e || $e->title !== 'Jerry Reynolds') { $bad[] = '#281 is not Jerry Reynolds'; }
$link = trim((string)$e->personGraveUrl); $notes = array_column($e->editorNotes ?? [], 'note');
if ($link !== '' && $link !== $URL) { $bad[] = "the grave link is \"$link\""; }
$done = $link === '' && in_array($NOTE, $notes, true) && ($e->burialEvidence->value ?? '') === 'uncited';
echo $done ? "already done\n" : "#281 personGraveUrl \"$link\" -> empty; burialEvidence -> uncited; note added\n";
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($done) { return; }
$rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
if (!in_array($NOTE, array_column($rows, 'note'), true)) { $rows[] = ['heading' => 'His burial place', 'position' => 'bottom', 'note' => $NOTE]; }
$e->setFieldValues(['personGraveUrl' => '', 'burialEvidence' => 'uncited', 'editorNotes' => $rows]);
$ok = Craft::$app->getElements()->saveElement($e);
$r = Entry::find()->id(281)->status(null)->one();
$ok = $ok && trim((string)$r->personGraveUrl) === '' && in_array($NOTE, array_column($r->editorNotes ?? [], 'note'), true);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_reynolds_grave.php', 1, $ok ? 'verified' : 'SHORT', 'Reynolds: Find a Grave link to a different-born Jerry Hugh Reynolds removed; burial place marked unsourced');
if (!$ok) { throw new \RuntimeException('fix_reynolds_grave: read-back failed'); }
