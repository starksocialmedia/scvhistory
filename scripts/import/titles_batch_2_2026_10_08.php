/**
 * Titles against the scans, batch 2 of 4 (inventory/review/titles-batch-2-2026-10-08.md), as Nathan ruled on 8 October 2026.
 * The 15 proposed titles, with his three calls: #28291 takes what the scan prints ("If the headline is the luncheon and the
 * story is mostly Veluzat and Worden, that is worth a note on the record, not a different title"), so a note goes into
 * editorNotes; #5359 "the event name is the title", its theme line "All that Glitters is GOLD" going into the catalogue
 * entry; #5245 "Aggie" (that its pages may be out of order is logged in the batch file, and they are not reordered).
 * The five kept (#4429, #2181, #5363, #3185, #5475) are not touched.
 *
 * Leon's headline: on an article or document it goes to legacyHeadline, from the legacy page's title tag minus the site
 * name, as in batch 1. On a photograph it is in catalogueCaption already; #4909's catalogue entry is empty, so it gets its
 * page's title tag (minus the site name) there, the form every other catalogue entry has. #5199's "(Film News)" is a
 * translation, not printed, and moves to the catalogue entry.
 * Every title written here is listed with the one it replaces, so this reverses. Slugs do not change.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/titles_batch_2_2026_10_08.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$NOTE28291 = ['heading' => 'About the headline', 'position' => 'bottom',
  'note' => 'The printed headline announces the Chamber of Commerce luncheon at which Peter Pitchess was to speak. The photograph and most of the story are about Rene Veluzat and Connie Worden, named the 1975 Man and Woman of the Year.'];
/* id => [title now, new title, Leon's headline for a document or article, text for a photograph's catalogue entry, catalogue entry only if empty] */
$P = [
  4909 => ["Fort Oghora Built for NBC's '77th Bengal Lancers' | TV Guide 1957.", 'India Never Had It So Good', null, null, "LW3097 | Vasquez Rocks | Fort Oghora Built for NBC's '77th Bengal Lancers' | TV Guide 1957."],
  5145 => ["Commuting Soledad Canyon: Southern Pacific's Saugus Line", "Commuting: Southern Pacific's Saugus Line", null, null, null],
  3049 => ['White Oil Analysis, 1900', 'Petroleum Versus Petroleum', null, null, null],
  867 => ['Henry Clay Wiley.', 'Another Pioneer Passes Away', 'Obituary | Henry Clay Wiley, Early L.A. & SCV Pioneer, 1829-1898.', null, null],
  26983 => ['Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream', 'Bogus History', 'Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream.', null, null],
  28291 => ['Rene Veluzat, Connie Worden Named 1975 SCV Man, Woman of the Year', 'Peter Pitchess to Speak at Newhall CC Luncheon', 'Chamber of Commerce | Rene Veluzat, Connie Worden Named 1975 SCV Man, Woman of the Year.', null, null],
  5385 => ['Southern Pacific and T&NO Class M-4 2-6-0s (Moguls)', 'Southern Pacific and Texas & New Orleans Class M-4 2-6-0s', null, null, null],
  5383 => ['Course of the Month: Indian Dunes', 'Indian Dunes/Valencia, California', null, null, null],
  5245 => ["J.C. Agajanian, the Haberdasher's Best Friend", 'Aggie', null, null, null],
  5205 => ['Will Rogers and Charlie Russell', 'Will and Charlie', null, null, null],
  5199 => ['Filmnyheter (Film News)', 'Filmnyheter, 21 November 1921', null, 'Filmnyheter: "Film News."', null],
  5359 => ['47th Annual Benefit Auction Catalog', '47th Annual Benefit Auction', null, 'Printed on the cover: "All that Glitters is GOLD".', null],
  5357 => ["Tip's Sierra Highway Restaurant Menu", "Original Tip's", null, null, null],
  4877 => ['The Case for Santa Clarita Cityhood', 'A Presentation to the Local Agency Formation Commission', null, null, null],
  4255 => ['Santa Clarita Cityhood Petition No. 1', 'Petition for the Incorporation of the City of Santa Clarita', null, null, null],
];
$capLimit = Craft::$app->getFields()->getFieldByHandle('catalogueCaption')->charLimit ?? null;
foreach ($P as $id => [$from, $to, $head, $catAdd, $catIfEmpty]) {
  $e = Entry::find()->id($id)->status(null)->one(); if (!$e) { echo "#$id missing" . PHP_EOL; continue; }
  $h = array_map(fn($x) => $x->handle, $e->getFieldLayout()->getCustomFields()); $sec = $e->getSection()->handle; $dirty = false; $msg = [];
  if ($sec === 'photographs') {
    $cat = trim((string)$e->getFieldValue('catalogueCaption'));
    if ($cat === '' && $catIfEmpty) { $cat = $catIfEmpty; $msg[] = "catalogue entry (empty) = '$catIfEmpty'"; $e->setFieldValue('catalogueCaption', $cat); $dirty = true; }
    if ($cat === '') { echo "#$id REFUSED: photograph with an empty catalogue entry; its headline would be lost" . PHP_EOL; continue; }
    if ($catAdd && !str_contains($cat, $catAdd)) { $msg[] = "catalogue entry + '$catAdd'"; $cat = "$cat $catAdd"; $e->setFieldValue('catalogueCaption', $cat); $dirty = true; }
    if ($capLimit && mb_strlen($cat) > $capLimit) { echo "#$id REFUSED: catalogue entry over $capLimit characters" . PHP_EOL; continue; }
  } elseif ($head !== null) {
    if (!in_array('legacyHeadline', $h, true)) { echo "#$id REFUSED: no legacyHeadline field" . PHP_EOL; continue; }
    if (trim((string)$e->getFieldValue('legacyHeadline')) !== $head) { $msg[] = "legacyHeadline = \"$head\""; $e->setFieldValue('legacyHeadline', $head); $dirty = true; }
  }
  if ($id === 28291) {
    $rows = array_values(array_filter($e->getFieldValue('editorNotes') ?? [], fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (!array_filter($rows, fn($r) => ($r['heading'] ?? '') === $NOTE28291['heading'])) {
      $rows = array_map(fn($r) => ['heading' => $r['heading'] ?? '', 'note' => $r['note'] ?? '', 'position' => $r['position'] ?? ''], $rows);
      $rows[] = $NOTE28291; $e->setFieldValue('editorNotes', $rows); $dirty = true; $msg[] = "editorNotes + \"{$NOTE28291['heading']}\" (bottom): {$NOTE28291['note']}";
    }
  }
  if ($e->title !== $to) {
    if ($e->title !== $from) { echo "#$id holds \"{$e->title}\", not \"$from\": left" . PHP_EOL; continue; }
    $msg[] = "title \"$from\" -> \"$to\""; $e->title = $to; $dirty = true;
  }
  echo "#$id ($sec) " . ($msg ? implode('; ', $msg) : 'done already') . PHP_EOL;
  if ($APPLY && $dirty) { if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
echo 'not touched (kept): #4429, #2181, #5363, #3185, #5475' . PHP_EOL;
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('titles_batch_2_2026_10_08.php', $n, 'verified', 'titles batch 2 of 4 as ruled; #28291 note; #5359 theme line to the catalogue entry'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
