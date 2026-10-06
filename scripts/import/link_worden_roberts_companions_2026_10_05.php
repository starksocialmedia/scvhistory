/**
 * Connie Worden-Roberts's three pieces, linked to one another (Nathan, 5 October 2026: "If two, keep both and link them to each
 * other"). The formal obituary (#28045, /scvhistory/obituary_conniewordenroberts.htm), Carl Goldman's tribute (#28047,
 * khts081314.htm) and Perry Smith's KHTS obituary (document #28305, khts081214.htm): the three legacy pages list one another in
 * a shared sidebar, so the sources make the tie (DATA-MODEL, No connection the sources do not make). Each obituary gets the
 * other two in obitCompanions; the document type has no such field, and is reached from the obituaries.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/link_worden_roberts_companions_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$SET = [28045, 28047, 28305]; $n = 0;
foreach ($SET as $id) { if (!Entry::find()->id($id)->status(null)->exists()) { throw new \RuntimeException("no #$id"); } }
foreach ([28045, 28047] as $id) {
  $e = Entry::find()->id($id)->status(null)->one(); $want = array_values(array_diff($SET, [$id])); $have = $e->obitCompanions->status(null)->ids();
  $add = array_values(array_diff($want, $have)); echo "#$id {$e->title}: " . ($add ? 'add ' . implode(', ', array_map(fn($x) => "#$x", $add)) : 'linked') . PHP_EOL;
  if (!$APPLY || !$add) { continue; }
  $e->setFieldValue('obitCompanions', array_merge($have, $add)); if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id"); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('link_worden_roberts_companions_2026_10_05.php', $n, 'verified', 'Connie Worden-Roberts: the three pieces linked'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
