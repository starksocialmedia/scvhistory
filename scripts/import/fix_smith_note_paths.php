/**
 * Christy Smith #25389: build_smith_profile.php (applied 3 October 2026) ended
 * five footnotes with the repository path of the saved return, "(inventory/
 * elections/sos/<file>)". check_render's note wording check fails on it: a
 * public note does not name the archive's machinery. The path is dropped; the
 * Secretary of State's URL stays. The script now writes them without it.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_smith_note_paths.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$p = Entry::find()->id(25389)->status(null)->one();
if (!$p || $p->title !== 'Christy Smith') { echo 'REFUSING: #25389 is not Christy Smith' . PHP_EOL; return; }
$rows = $p->footnotes ?? []; $out = []; $n = 0;
foreach ($rows as $r) { $note = preg_replace('~ \(inventory/elections/sos/[^)]+\)\.$~', '.', (string)$r['note'], -1, $c); $n += $c; $out[] = ['number' => $r['number'], 'note' => $note, 'source' => $r['source']]; }
echo "$n notes carry the path" . PHP_EOL;
if (!$n || !$APPLY) { echo 'nothing was written.' . ($APPLY ? '' : ' Set $APPLY = true to apply.') . PHP_EOL; return; }
$p->setFieldValue('footnotes', $out);
if (!Craft::$app->getElements()->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$left = array_filter(Entry::find()->id(25389)->status(null)->one()->footnotes ?? [], fn($r) => str_contains((string)$r['note'], 'inventory/'));
echo 'READ-BACK ' . ($left ? 'SHORT' : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_smith_note_paths.php', $n, $left ? 'SHORT' : 'verified', 'Christy Smith: repository paths out of five footnotes');
if ($left) { throw new \RuntimeException('fix_smith_note_paths: read-back failed'); }
