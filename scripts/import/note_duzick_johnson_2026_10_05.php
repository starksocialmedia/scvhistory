/**
 * Sharlene Duzick and Sharlene Rose Johnson (Nathan, 5 October 2026: "note the identification as probable on both records,
 * with the evidence, and leave the candidacies unlinked"). Duzick has no person record (a losing candidate gets none); her
 * record is her two Saugus Union candidacies, #25725 (2018) and #25755 (2022). Each gets an editor's note pointing to Johnson's
 * record (#30263), which carries the same note from build_coc_trustee_profiles_2026_10_05.php. Nothing is linked.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/note_duzick_johnson_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$NOTE = 'This candidate is probably Sharlene Rose Johnson, a trustee of the Santa Clarita Community College District from 2022 (person record #30263). Her district biography and SCVNews reports about Sharlene Duzick give the same presidency of JCI Santa Clarita Valley, the same seat on the College of the Canyons Foundation board (from July 2019) and the same work in real estate. No source names both, so the identification is probable, not established, and this candidacy is not linked to her record.';
$n = 0;
foreach ([25725, 25755] as $id) {
  $c = Entry::find()->section('candidacies')->id($id)->status(null)->one();
  $rows = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($c->editorNotes ?? [])), fn($r) => $r['note'] !== ''));
  $has = in_array($NOTE, array_column($rows, 'note'), true);
  echo "#$id {$c->nameAsPrinted}: " . ($has ? 'noted already' : 'add note') . PHP_EOL;
  if (!$APPLY || $has) { continue; }
  $rows[] = ['heading' => 'Probably Sharlene Rose Johnson', 'note' => $NOTE, 'position' => 'bottom'];
  $c->setFieldValue('editorNotes', $rows); if (!Craft::$app->getElements()->saveElement($c)) { throw new \RuntimeException("#$id"); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('note_duzick_johnson_2026_10_05.php', $n, 'verified', 'Duzick candidacies: probably Sharlene Rose Johnson, not linked'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
