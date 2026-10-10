/**
 * Titles against the scans, batches 3 and 4 of 4 (inventory/review/titles-batch-3-4-2026-10-08.md). NOT YET RULED.
 * Written by Claude overnight, 8 October 2026, as a dry run only: the 27 proposed titles as the batch file proposes
 * them, before Nathan's rulings. Edit $P to his rulings before any apply; the calls marked "your call" in the batch
 * file (#4567, #21936, #5481, #20099, and the printed dates on #5377, #3191, #4943) may change.
 * The ten kept (#5683, #5283, #4539, #4517, #4267, #4155, #4145, #3245, #2767, #28299) are not touched.
 *
 * Leon's headline: on an article or document it goes to legacyHeadline, from the legacy page's title tag minus the site
 * name, as in batches 1 and 2. On a photograph it is in catalogueCaption already; #5377's catalogue entry is empty, so
 * it gets its page's title tag (minus the site name) there, as #4909 did in batch 2. #21936 has no legacy page (an
 * election import), so no legacyHeadline; its full printed title goes to originallyPublishedTitle, which is empty.
 * Every title written here is listed with the one it replaces, so this reverses. Slugs do not change.
 * Applied 9 October 2026, evening, on Nathan's word ("Apply titles 3 and 4's 27 retitles and the 10 keeps. Hold the 2
 * unsure."), less $HOLD: his four calls and the printed-date question, which he asked to have sent to him that morning, and
 * #4519, whose record holds the wrong file. The morning summary's "2 unsure" named no records; these are what is held.
 * Idempotent. Dry run by default. Set $APPLY = true only after Nathan rules.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/titles_batch_3_4_2026_10_08.php'))"
 */
use craft\elements\Entry;
$reads = require \Craft::getAlias('@root') . '/scripts/import/_reads.php';
$reads([
  ['record', 'Craft fields title, catalogueCaption, legacyHeadline, originallyPublishedTitle of 27 records', 'the scans of those records',
   'not read: the scans were read by eye for titles-batch-3-4-2026-10-08.md; this script writes only what that file proposes'],
]);
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$OPT21936 = 'RESOLUTION NO. 12-9 / A RESOLUTION OF THE CITY COUNCIL OF THE CITY OF SANTA CLARITA, CALIFORNIA, RECITING THE FACT OF THE GENERAL MUNICIPAL ELECTION HELD ON TUESDAY, APRIL 10, 2012, DECLARING THE RESULT AND SUCH OTHER MATTERS AS PROVIDED BY LAW';
/* id => [title now, new title, Leon's headline for a document or article, catalogue entry only if empty, originallyPublishedTitle only if empty] */
$P = [
  5325 => ["Squadron of Giant Tillers Speed California's Castaic Dam", "Squadron Giant Tillers Speed California's Castaic Dam", null, null, null],
  5685 => ['Newhall-Saugus-Valencia-Canyon Country', 'Local City Directory for Newhall-Saugus-Valencia-Canyon Country', null, null, null],
  5639 => ['Common Standard Paint Colors for Buildings and Fences', 'Common Standard Paint Colors for Company Buildings and Fences', null, null, null],
  5523 => ['Program Book: 6th Annual Hoot Gibson-Golden State Ranch Rodeo', "Hoot Gibson's Golden State Ranch Rodeo", null, null, null],
  5481 => ['Screenplay: "A Private Little War."', 'A Private Little War', null, null, null],
  5447 => ['Call Sheet: "Wild Wild West" at Vasquez Rocks', 'Wild Wild West Feature Call Sheet', null, null, null],
  5427 => ['Filmprogrammheft: "Die Flamme von Arabien."', 'Die Flamme von Arabien', null, null, null],
  5419 => ['Lebec Hotel and Rancho Coffee Shop Menu', 'Lebec Hotel and Rancho Coffee Shop', null, null, null],
  5377 => ['Souvenir Program: Baker Ranch Rodeo, Under Direction of Hoot Gibson, 4-27-1930.', 'Baker Ranch Rodeo, April 27, 1930', null, 'LW3531 | Souvenir Program: Baker Ranch Rodeo, Under Direction of Hoot Gibson, 4-27-1930.', null],
  5297 => ['New Colonial Theatre (Beach Haven, N.J.) Program, Week of 7-28-1924', 'New Colonial Theatre: Program for Week of July 28, 1924', null, null, null],
  5287 => ['Lebec (Hotel?) Restaurant and Coffee Shop Menu', 'Lebec Restaurant and Coffee Shop', null, null, null],
  4985 => ['"The Cradle of Courage"', 'The Cradle of Courage', null, null, null],
  4943 => ['Bonelli Stadium Racing Program 5-26-1946', 'Bonelli Stadium Official Program, May 26, 1946', null, null, null],
  4863 => ['Dedication Ceremony of the Permanent Campus', 'Dedication Ceremony of the Permanent Campus, College of the Canyons', null, null, null],
  4753 => ['Program Book: 22nd Annual Newhall-Saugus Rodeo', '22nd Annual Newhall-Saugus Rodeo', null, null, null],
  4747 => ['Saugus Stadium Stock Car Racing Program', 'Speed: Saugus Stadium Official Program', null, null, null],
  4621 => ['Bonelli Stadium Racing Program Book', 'Bonelli Stadium Official Program', null, null, null],
  4591 => ['Santa Paula Mining & Reduction Co. Stock Cert', 'Santa Paula Mining & Reduction Co. Stock Certificate', null, null, null],
  4567 => ["William S. Hart in 'The Squaw Man'", 'The Squaw Man', null, null, null],
  4541 => ["Wooden Menu, Tip's Restaurants", "Tip's Restaurants", null, null, null],
  4519 => ['Santa Clarita Metrolink Station Grand Opening Dedication', 'Metrolink Grand Opening Dedication', null, null, null],
  4169 => ['Tentative Program for 1930 Dedication', 'Tentative Program for Placeritos', null, null, null],
  3191 => ['Baker Ranch Rodeo Program, 4-11-1926', 'Baker Ranch Rodeo, April 11, 1926', null, null, null],
  1442 => ['Picture Story of Hart High School (and District)', 'Picture Story of Hart High School', 'Picture Story of Hart High School (and District), May 1952.', null, null],
  18991 => ['A Brief Sketch of the Notorious Bandit', 'Tiburcio Vasquez! A Brief Sketch of the Notorious Bandit', 'JE4001 | People | Tiburcio Vasquez Biography, 1874.', null, null],
  20099 => ['C.A. Mentry, in Pen Pictures From the Garden of the World', 'C.A. Mentry, in An Illustrated History of Los Angeles County', 'People | Charles Alexander Mentry, Pico Oil Field Superintendent: Biography During Life (Pen Pictures L.A. County 1889).', null, null],
  21936 => ['Resolution No. 12-9: the results of the General Municipal Election of April 10, 2012', 'Resolution No. 12-9', null, null, $OPT21936],
];
/* Held for Nathan: the four calls, the three printed dates, and #4519's wrong file. */
$HOLD = [4567, 21936, 5481, 20099, 5377, 3191, 4943, 4519];
foreach ($HOLD as $id) { echo "#$id held for Nathan" . PHP_EOL; unset($P[$id]); }
$capLimit = Craft::$app->getFields()->getFieldByHandle('catalogueCaption')->charLimit ?? null;
foreach ($P as $id => [$from, $to, $head, $catIfEmpty, $optIfEmpty]) {
  $e = Entry::find()->id($id)->status(null)->one(); if (!$e) { echo "#$id missing" . PHP_EOL; continue; }
  $h = array_map(fn($x) => $x->handle, $e->getFieldLayout()->getCustomFields()); $sec = $e->getSection()->handle; $dirty = false; $msg = [];
  if ($sec === 'photographs') {
    $cat = trim((string)$e->getFieldValue('catalogueCaption'));
    if ($cat === '' && $catIfEmpty) { $cat = $catIfEmpty; $msg[] = "catalogue entry (empty) = '$catIfEmpty'"; $e->setFieldValue('catalogueCaption', $cat); $dirty = true; }
    if ($cat === '') { echo "#$id REFUSED: photograph with an empty catalogue entry; its headline would be lost" . PHP_EOL; continue; }
    if ($capLimit && mb_strlen($cat) > $capLimit) { echo "#$id REFUSED: catalogue entry over $capLimit characters" . PHP_EOL; continue; }
  } elseif ($head !== null) {
    if (!in_array('legacyHeadline', $h, true)) { echo "#$id REFUSED: no legacyHeadline field" . PHP_EOL; continue; }
    if (trim((string)$e->getFieldValue('legacyHeadline')) !== $head) { $msg[] = "legacyHeadline = \"$head\""; $e->setFieldValue('legacyHeadline', $head); $dirty = true; }
  }
  if ($optIfEmpty !== null) {
    if (!in_array('originallyPublishedTitle', $h, true)) { echo "#$id REFUSED: no originallyPublishedTitle field" . PHP_EOL; continue; }
    $opt = trim((string)$e->getFieldValue('originallyPublishedTitle'));
    if ($opt === '') { $msg[] = "originallyPublishedTitle (empty) = \"$optIfEmpty\""; $e->setFieldValue('originallyPublishedTitle', $optIfEmpty); $dirty = true; }
  }
  if ($e->title !== $to) {
    if ($e->title !== $from) { echo "#$id holds \"{$e->title}\", not \"$from\": left" . PHP_EOL; continue; }
    $msg[] = "title \"$from\" -> \"$to\""; $e->title = $to; $dirty = true;
  }
  echo "#$id ($sec) " . ($msg ? implode('; ', $msg) : 'done already') . PHP_EOL;
  if ($APPLY && $dirty) { if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
echo 'not touched (kept): #5683, #5283, #4539, #4517, #4267, #4155, #4145, #3245, #2767, #28299' . PHP_EOL;
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('titles_batch_3_4_2026_10_08.php', $n, 'verified', 'titles batches 3 and 4 of 4 as ruled'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
