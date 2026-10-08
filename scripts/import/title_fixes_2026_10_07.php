/**
 * Three titles fixed on Nathan's word (7 October 2026, night), from inventory/review/titles-vs-scans-2026-10-07.md:
 * #32724 "School Chiefs", as the Signal clipping prints it (the title was taken from Leon's page, which reads "Schools");
 * #4607 and #4751 back to their titles before today's retitle, which matched the scan better (the retitle dropped the printed
 * "Funscoming" and "Bonelli Stadium"). Slugs do not change. Idempotent. Dry run by default. Set $APPLY = true.
 */
$APPLY = false;
$plan = [
  32724 => ['Schools Chiefs React with Shock, Anger', 'School Chiefs React with Shock, Anger'],
  4607 => ['Magic Mountain Pre-Opening Brochure', 'Funscoming Spring 1971: Magic Mountain Pre-Opening Brochure.'],
  4751 => ['Midget Auto Racing Program', 'Bonelli Stadium Midget Auto Racing Program, 7-24-1948.'],
];
$n = 0;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($plan as $id => [$from, $to]) {
  $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
  if (!$e) { echo "#$id missing" . PHP_EOL; continue; }
  if ($e->title === $to) { echo "#$id already \"$to\"" . PHP_EOL; continue; }
  if ($e->title !== $from) { echo "#$id holds \"{$e->title}\", not \"$from\": left" . PHP_EOL; continue; }
  echo "#$id \"$from\" -> \"$to\"" . PHP_EOL;
  if ($APPLY) { $e->title = $to; if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('title_fixes_2026_10_07.php', $n, 'verified', '#32724 School Chiefs; #4607 and #4751 reverted to their pre-retitle titles'); }
