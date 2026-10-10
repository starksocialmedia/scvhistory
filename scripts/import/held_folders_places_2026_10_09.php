/**
 * The 759 files the folder pass held: the five groups with an answer under rules already written (Nathan, 9 October 2026,
 * evening: "apply the five groups with obvious answers"; inventory/review/overnight-2026-10-08/folder-pass-held-2026-10-08.md).
 * Four of the five need nothing written: uc8901's 658 raw scans stay on Reggie, the -orig and -ebook PDFs stay on Reggie,
 * the 4 thumbnails stay out, LW3267's renders are held already. The fifth is a relation: #605 Heritage Junction and #613
 * Six Flags Magic Mountain claim the folders of photographs #5645 and #5647 only through a legacy link already known to be
 * wrong (place-legacy-links-dry-run-2026-10-05.md, waiting on Nathan, not touched here). No copies go on the places; each
 * photograph is related to its place through photoPlaces (DATA-ORGANIZATION.md: "Relate, don't tag. If a record exists,
 * link it.").
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\Entry;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$reads = require "$root/scripts/import/_reads.php";
$reads([['record', 'Craft field photoPlaces on #5645 and #5647, and the titles of #605 and #613', 'no file (relations only)', 'not read: nothing here concerns a file']]);
$L = [5645 => 605, 5647 => 613];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($L as $pid => $place) {
  $p = Entry::find()->id($pid)->section('photographs')->status(null)->one(); $pl = Entry::find()->id($place)->section('places')->status(null)->one();
  if (!$p || !$pl) { echo "#$pid or #$place missing; refused" . PHP_EOL; continue; }
  $now = $p->photoPlaces->status(null)->ids();
  echo "#$pid {$p->title}: photoPlaces " . json_encode($now) . (in_array($place, $now) ? ' (has it)' : " + #$place {$pl->title}") . PHP_EOL;
  if ($APPLY && !in_array($place, $now)) { $p->setFieldValue('photoPlaces', array_merge($now, [$place])); if (!$el->saveElement($p)) { throw new \RuntimeException("#$pid " . json_encode($p->getFirstErrors())); } $n++; }
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('held_folders_places_2026_10_09.php', $n, 'verified', 'two photographs related to their places'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
