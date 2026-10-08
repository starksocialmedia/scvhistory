/**
 * Two photograph codes corrected, as Nathan ruled on 8 October 2026 ("fix #2939 and #2741 to LW2158 and LW2042"). Leon's
 * title tags on lw2158.htm and lw2042.htm reuse codes that belong to other pages (LW2157a, LW2382a), and the import carried
 * them into photoSourceCode and the head of the catalogue entry. Each record's own page, legacyKey and picture are lw2158
 * and lw2042. Checked first: no other record (revisions aside) holds LW2158 or LW2042.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/photo_codes_2939_2741_2026_10_08.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$n = 0;
foreach ([2939 => ['LW2157a', 'LW2158', 'lw2158'], 2741 => ['LW2382a', 'LW2042', 'lw2042']] as $id => [$old, $new, $key]) {
  $e = Entry::find()->id($id)->status(null)->one();
  if ($e->getFieldValue('legacyKey') !== $key) { echo "#$id legacyKey is not $key: left" . PHP_EOL; continue; }
  $other = Entry::find()->section('photographs')->status(null)->id(['not', $id])->photoSourceCode($new)->ids();
  if ($other) { echo "#$id REFUSED: $new already held by #" . implode(', #', $other) . PHP_EOL; continue; }
  $msg = []; $code = (string)$e->getFieldValue('photoSourceCode'); $cap = (string)$e->getFieldValue('catalogueCaption');
  if ($code === $old) { $e->setFieldValue('photoSourceCode', $new); $msg[] = "photoSourceCode $old -> $new"; }
  if (str_starts_with($cap, "$old |")) { $cap2 = $new . substr($cap, strlen($old)); $e->setFieldValue('catalogueCaption', $cap2); $msg[] = "catalogue entry \"$cap\" -> \"$cap2\""; }
  echo "#$id " . ($msg ? implode('; ', $msg) : 'done already') . PHP_EOL;
  if ($APPLY && $msg) { if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('photo_codes_2939_2741_2026_10_08.php', $n, 'verified', '#2939 LW2158, #2741 LW2042'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
