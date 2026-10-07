/**
 * Footnotes and provenance in a reader's words (check_note_wording.php failed the predeploy of 6 October, evening). Today's
 * profiles named the archive's own files and process: "An earlier pass (inventory/review/...md) gave", "that file marks its
 * figures as superseded", "(saved in inventory/sources/...)", "not in the mirror"; the Commons portraits' source said "the stored
 * copy is re-encoded"; Adams's replaced file said "legacy mirror". The facts stay; the words change:
 *   the earlier figure is "an earlier count in this archive", "since superseded"; the saved-copy path goes; "not in the mirror"
 *   becomes "not among the original site's files"; "the stored copy is re-encoded" goes; "legacy mirror" becomes "original
 *   site's files".
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/wording_evening_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$fix = function (string $t): string {
  $t = preg_replace('~An earlier pass \(inventory/[^)]*\) gave~', 'An earlier count in this archive gave', $t);
  $t = preg_replace('~[;,] (and )?that file marks its figures as superseded~', ', since superseded', $t);
  $t = str_replace([' (saved in inventory/sources/katie-hill-2026-10-06/)', 'not in the mirror and not yet a record', '; the stored copy is re-encoded', 'the legacy mirror', 'legacy mirror'],
                   [', since superseded', '', 'not among the original site\'s files and not yet a record', '', 'the original site\'s files', 'original site\'s files'], $t);
  return $t;
};
foreach ([29450, 29328, 29316, 29314, 29284, 18747] as $id) {
  $e = Entry::find()->id($id)->status(null)->one();
  $rows = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? '')], iterator_to_array($e->footnotes));
  $new = array_map(fn($r) => ['note' => $fix($r['note'])] + $r, $rows); $ch = array_filter(array_keys($rows), fn($i) => $rows[$i]['note'] !== $new[$i]['note']);
  echo "#$id {$e->title}: " . ($ch ? count($ch) . ' footnote(s) reworded' : 'done already') . PHP_EOL;
  foreach (array_slice($ch, 0, 1) as $i) { echo '  e.g. ' . mb_substr($new[$i]['note'], max(0, mb_strpos($new[$i]['note'], 'earlier') ?: (mb_strpos($new[$i]['note'], 'original site') ?: 0) - 40), 200) . "\n"; }
  if ($APPLY && $ch) { $e->setFieldValue('footnotes', array_values(array_filter($new, fn($r) => $r['note'] !== ''))); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id"); } $n++; }
}
foreach ([31736, 31738, 31740, 31742, 31744, 31746, 31748, 31750, 31752, 31754, 31454, 31925] as $aid) {
  $a = Asset::find()->id($aid)->one(); $vals = [];
  foreach ($a->getFieldLayout()->getCustomFields() as $f) { if (!$f instanceof \craft\fields\PlainText) continue; $v = (string)$a->getFieldValue($f->handle); $w = $fix($v); if ($w !== $v) $vals[$f->handle] = $w; }
  echo "asset #$aid {$a->filename}: " . ($vals ? implode(', ', array_keys($vals)) : 'done already') . PHP_EOL;
  if ($APPLY && $vals) { $a->setFieldValues($vals); if (!$el->saveElement($a)) { throw new \RuntimeException("asset #$aid"); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('wording_evening_2026_10_06.php', $n, 'verified', 'footnotes and provenance in a reader\'s words'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
