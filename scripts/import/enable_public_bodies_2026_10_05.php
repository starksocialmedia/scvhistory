/**
 * Publishes the public bodies made on 5 October 2026 once Nathan has read them (inventory/review/public-bodies-dry-run-2026-10-05.md):
 * the eleven from create_public_bodies_2026_10_05.php, the Newhall Municipal Court and its courthouses, and the college
 * district's elections and candidacies. An election's title is built from its body when it is saved, and these were saved
 * while the district was disabled (they read "City Council election ..."), so each election is saved again after the district
 * is enabled.
 * $ONLY: titles to publish, empty for all. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/enable_public_bodies_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false; $ONLY = [];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements();
$bodies = array_filter(array_merge(
  Entry::find()->section('organizations')->status(null)->all(), Entry::find()->section('places')->status(null)->all()),
  fn($e) => preg_match('~create_(public_bodies|newhall_court)_2026_10_05~', (string)$e->recordProvenance) && (!$ONLY || in_array($e->title, $ONLY, true)));
$elections = Entry::find()->section('elections')->status(null)->slug('scccd-*')->all();
$cands = $elections ? Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $elections, 'field' => 'candidacyElection'])->all() : [];
$coc = (bool)array_filter($bodies, fn($e) => $e->title === 'Santa Clarita Community College District');
foreach ($bodies as $e) { echo ($e->enabled ? 'on   ' : 'off  ') . $e->title . PHP_EOL; }
echo count($elections) . ' college elections and ' . count($cands) . ' candidacies ' . ($coc ? 'with the district' : '(the district is not in $ONLY, so they stay as they are)') . PHP_EOL;
if (!$APPLY) { return; }
$n = 0;
foreach ($bodies as $e) { if (!$e->enabled) { $e->enabled = true; if (!$el->saveElement($e)) { throw new \RuntimeException($e->title); } $n++; } }
if ($coc) {
  foreach ($elections as $x) { $x->enabled = true; if (!$el->saveElement($x)) { throw new \RuntimeException($x->slug); } $n++; }
  foreach ($cands as $c) { if (!$c->enabled) { $c->enabled = true; $el->saveElement($c); $n++; } }
  $bad = array_filter(Entry::find()->section('elections')->slug('scccd-*')->all(), fn($x) => !str_contains($x->title, 'College'));
  if ($bad) { throw new \RuntimeException(count($bad) . ' election titles did not rebuild, e.g. ' . reset($bad)->title); }
}
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('enable_public_bodies_2026_10_05.php', $n, 'verified', 'public bodies of 5 October published');
echo "done: $n\n";
