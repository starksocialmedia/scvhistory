/**
 * Titles against the scans, batch 1 of 4 (inventory/review/titles-batch-1-2026-10-07.md), as Nathan ruled on 7 October 2026:
 * newspapers and magazines take what the scan prints, Leon's page headline kept in its own field; ephemera take the printed
 * cover or masthead title; inner pages and forms keep a description. His three calls: #4735 "Ramona" ("If that is all the page
 * prints, that is the title"); #5541 keeps a description and its slide copy goes into the catalogue entry ("advertising copy
 * is not a title"); #26573 drops the agency ("The agency name belongs in fields, not after a colon in the title").
 *
 * New field legacyHeadline, "Headline on SCVHistory.com" (PlainText; Nathan: "It says exactly what it is"), on the article and
 * document types after heldAs: Leon's page title as the page's title tag gives it, minus the site name, every word, the same
 * form as a photograph's catalogue entry. Photographs already keep it in catalogueCaption, so a photograph is retitled only
 * when its catalogue entry holds something. #26573 has no legacy page, so no headline to keep. The agency its title named is
 * held in fields already: the four Castaic Lake Water Agency elections of 8 November 2016 link to it.
 * Every title written here is listed with the one it replaces, so this reverses. Slugs do not change.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/titles_batch_1_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $n = 0;
$f = $fs->getFieldByHandle('legacyHeadline'); echo 'field legacyHeadline: ' . ($f ? 'exists' : 'create "Headline on SCVHistory.com"') . PHP_EOL;
if ($APPLY && !$f) { $f = new \craft\fields\PlainText(['handle' => 'legacyHeadline', 'name' => 'Headline on SCVHistory.com', 'searchable' => true,
    'instructions' => "Leon Worden's headline for this record on SCVHistory.com, every word, kept where the title now prints what the scan prints. From the page's title tag, minus the site name."]);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++; }
foreach (['article', 'document'] as $th) {
  $t = $es->getEntryTypeByHandle($th); $layout = $t->getFieldLayout();
  $has = in_array('legacyHeadline', array_map(fn($x) => $x->handle, $layout->getCustomFields()), true);
  echo "type $th: " . ($has ? 'has legacyHeadline' : 'add legacyHeadline after heldAs') . PHP_EOL;
  if (!$APPLY || $has || !$f) { continue; }
  $done = false; foreach ($layout->getTabs() as $tab) { $els = [];
    foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'heldAs') { $els[] = new \craft\fieldlayoutelements\CustomField($f); $done = true; } }
    $tab->setElements($els); }
  if (!$done) { $tabs = $layout->getTabs(); $els = $tabs[0]->getElements(); $els[] = new \craft\fieldlayoutelements\CustomField($f); $tabs[0]->setElements($els); }
  $layout->setTabs($layout->getTabs()); $t->setFieldLayout($layout);
  if (!$es->saveEntryType($t)) { throw new \RuntimeException(json_encode($t->getFirstErrors())); } $n++;
}
/* id => [title now, new title or null, Leon's headline for a document or article, text to add to a photograph's catalogue entry] */
$P = [
  20107 => ['Death of Arthur Charles Mentry', 'Son of Pioneer Oilman Dies', 'Obituaries | Arthur Charles Mentry, Son of Oilman Alex Mentry, d. 1954.', null],
  28057 => ["John Lang's Letter on the Grizzly Bear, Los Angeles Herald, July 28, 1875", 'Death of a Monster Bear', 'People | History Revisited: John Lang and the 1,600-pound Grizzly Bear of 1875.', null],
  4735 => ['Serialization of Edwin Carewe\'s "Ramona" (Chapters 4-5)', 'Ramona', null, null],
  5541 => ['Hart-Westover Marriage on Lantern Slide', null, null, 'Printed on the slide: "Wm. S. Hart has won the hand of WINIFRED WESTOVER!"'],
  5337 => ['Acknowledgement of Royalties Received for Sound Recording', 'Statement of Royalty Due on Selections by William S. Hart', null, null],
  5299 => ["Tip's Santa Monica Restaurant", "Second Quarter Century: Tip's Restaurants", null, null],
  5135 => ['Air Force Pilots Sight UFO Near Lebec', 'Project Grudge: Incident near Camp Oak Flat, California', null, null],
  4801 => ['SCV School District Reorganization Plan (Failed)', 'Proposed Reorganization of the Hart Union High School District Area', null, null],
  4757 => ["Wooden Menu, Tip's Restaurant", "Thick Steaks, Tip's, Thin Pancakes", null, null],
  2747 => ["Walker's Placerita Camp Brochure", "Walker's Placerita Camp Brochure (Inside)", null, null],
  28055 => ['John Lang: Biography During Life', 'John Lang, in Pen Pictures from the Garden of the World', 'SCV Pioneers | John Lang: Biography During Life, with Bear Story (Pen Pictures L.A. County 1889).', null],
  26573 => ['Statement of Votes Cast and Official Election Returns, General Election, November 8, 2016: Castaic Lake Water Agency', 'Final Official Election Returns, November 08, 2016 General Election', null, null],
  28301 => ['William S. Hart Union High School District governing board members, 1974-1979', 'Roster of Board Members, 1948 to Present', 'Wm. S. Hart High School Board Members, 1945 to Date.', null],
  4783 => ['The Bogus Story of Tom Vernon and the Sweetwater Incident', 'Tragedy on the Sweetwater', null, null],
  5087 => ['The Winged Monster of Elizabeth Lake', "Our Country's Mysterious Monsters", null, null],
  4269 => ['Construction Begins on Castaic Dam', 'Castaic Dam Site Explodes Into Action', null, null],
  2963 => ['Lopez Gold Discovery in NY Observer, 1842', 'California Gold', null, null],
];
echo 'no change (description stays): #4543, #1436, #20102' . PHP_EOL;
foreach ($P as $id => [$from, $to, $head, $catAdd]) {
  $e = Entry::find()->id($id)->status(null)->one(); if (!$e) { echo "#$id missing" . PHP_EOL; continue; }
  $h = array_map(fn($x) => $x->handle, $e->getFieldLayout()->getCustomFields()); $sec = $e->getSection()->handle; $dirty = false; $msg = [];
  if ($sec === 'photographs') {
    $cat = trim((string)$e->getFieldValue('catalogueCaption'));
    if ($cat === '') { echo "#$id REFUSED: photograph with an empty catalogue entry; its headline would be lost" . PHP_EOL; continue; }
    if ($catAdd && !str_contains($cat, $catAdd)) { $msg[] = "catalogue entry + '$catAdd'"; $e->setFieldValue('catalogueCaption', "$cat $catAdd"); $dirty = true; }
  } elseif ($head !== null) {
    if (!in_array('legacyHeadline', $h, true)) { $msg[] = "legacyHeadline = \"$head\" (field added on apply)"; }
    elseif (trim((string)$e->getFieldValue('legacyHeadline')) !== $head) { $msg[] = "legacyHeadline = \"$head\""; $e->setFieldValue('legacyHeadline', $head); $dirty = true; }
  }
  if ($to !== null && $e->title !== $to) {
    if ($e->title !== $from) { echo "#$id holds \"{$e->title}\", not \"$from\": left" . PHP_EOL; continue; }
    $msg[] = "title \"$from\" -> \"$to\""; $e->title = $to; $dirty = true;
  }
  echo "#$id ($sec) " . ($msg ? implode('; ', $msg) : 'done already') . PHP_EOL;
  if ($APPLY && $dirty) { if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('titles_batch_1_2026_10_07.php', $n, 'verified', 'titles batch 1 of 4 as ruled; legacyHeadline on articles and documents'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
