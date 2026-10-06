/**
 * Provenance and notes in a reader's words (the rule of 3 October 2026, enforced by check_note_wording.php, which failed the
 * predeploy of 6 October). Today's imports wrote "Copied from the legacy mirror on ..." into the source of the portrait-batch,
 * Chico López and Tom Frew II assets, and Ward's crop names a checksum ("sha256 ...") and a field ("(enhancedFrom)"); three war
 * memorial notes begin a sentence "Searched 4 and 5 October 2026 and not found", which reads as the archive's process. The
 * facts stay (the search is recorded, as a no-source note must); the words change:
 *   "the legacy mirror" -> "the original site's files"; "Copied from" -> "Taken from"; the checksum and field name out of the
 *   prose (the checksum is in sourceChecksum); "Searched 4 and 5 October 2026 and not found:" -> "On 4 and 5 October 2026 these
 *   were searched, and he was not found in them:".
 * Every text field of an asset or record the check names. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/provenance_wording_2026_10_06.php'))"
 */
use craft\elements\{Asset, Entry};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0; $k = 0;
$REP = ['Copied from the legacy mirror on' => 'Taken from the original site\'s files on', 'copied from the legacy mirror' => 'taken from the original site\'s files', 'the legacy mirror' => 'the original site\'s files', 'legacy mirror' => 'original site\'s files'];
foreach (Asset::find()->limit(null)->all() as $a) {
  $vals = [];
  foreach ($a->getFieldLayout()?->getCustomFields() ?? [] as $f) { if (!$f instanceof \craft\fields\PlainText) { continue; } $v = (string)$a->getFieldValue($f->handle); if ($v === '') { continue; }
    $new = strtr($v, $REP); $new = preg_replace('~ \(sha256 [0-9a-f]+\.*\)~', '', $new); $new = str_replace(' (enhancedFrom)', '', $new);
    if ($new !== $v) { $vals[$f->handle] = $new; } }
  if (!$vals) { continue; } $k++;
  if ($k <= 3) { foreach ($vals as $h => $v) { echo "asset #{$a->id} {$a->filename} $h: " . mb_substr($v, 0, 220) . "\n"; } }
  if ($APPLY) { $a->setFieldValues($vals); if (!$el->saveElement($a)) { throw new \RuntimeException("asset #{$a->id} " . json_encode($a->getFirstErrors())); } $n++; }
}
echo "$k assets reworded\n";
$OLD = 'Searched 4 and 5 October 2026 and not found:'; $NEW = 'On 4 and 5 October 2026 these were searched, and he was not found in them:';
foreach ([542, 534, 528] as $id) {
  $e = Entry::find()->id($id)->status(null)->one();
  $rows = array_values(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes)));
  $ch = false; foreach ($rows as &$r) { if (str_contains($r['note'], $OLD)) { $r['note'] = str_replace($OLD, $NEW, $r['note']); $ch = true; } } unset($r);
  echo "#$id {$e->title}: " . ($ch ? 'reworded' : 'done already') . PHP_EOL;
  if ($APPLY && $ch) { $e->setFieldValue('editorNotes', array_values(array_filter($rows, fn($r) => $r['note'] !== ''))); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id"); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('provenance_wording_2026_10_06.php', $n, 'verified', 'provenance and three notes in a reader\'s words'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
