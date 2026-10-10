/**
 * The held photographs of the title plan (inventory/review/overnight-2026-10-08/held-import-2026-10-08.md, section 2), on
 * Nathan's word (9 October 2026, evening: "The 13 held photographs and the 15 held records: apply, since you say none needs
 * a ruling"). The 15 held records were done on 8 October; nothing to apply there.
 * Under the 7 October rule, Leon's catalogue entry goes into catalogueCaption first: the legacy page's title tag, minus the
 * site name, read here from the page on Reggie (as #4909 in batch 2). Then:
 *  - retitled to the headline the scan or page prints: #4861 (the press release's head), #3023 and #2987 (their pages are on
 *    Reggie as lw2248a.htm and lw2214a.htm; the census looked for the legacyUrl's name and called them missing);
 *  - catalogue entry only, title kept as a description: #4859 (a letter), #5671 and #4791 (two envelopes, no headline).
 * Held: #5377 (its printed date is one of Nathan's title calls), #2913 (no catalogue entry of Leon's found: the 2002 capture
 * of its page carries a generic title tag; the title's "</" waits with it). Already right or done: #4909, #5735, #5737,
 * #5739, #4475.
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\Entry;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$R = '/mnt/reggie/scvhistory.com/scvhistory/';
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the legacy pages on Reggie, for their title tags', $R],
  ['record', 'Craft fields title and catalogueCaption on six photograph records', 'the scans', 'not read: the printed heads were read from the scans for held-import-2026-10-08.md; this writes what that report found'],
]);
/* id => [page on Reggie, title now, new title or null to keep] */
$P = [
  4861 => ['lw3060.htm', 'City Formation Committee Kicks Off Voter Registration Program to Get More Funds From State, 12-1-1987.', 'City Formation Committee Kicks-off Voter Registration Program to Get More Funds From State'],
  3023 => ['lw2248a.htm', 'Photo Gallery: 1876 Golden Spike.', '1876 Lang Station Golden Spike'],
  2987 => ['lw2214a.htm', "Photo Gallery: Sandberg's Summit Hotel Site, 2006.", "Sandberg's Summit Hotel Site"],
  4859 => ['lw3059.htm', 'Arthur Young CPAs Predict 22% Budget Windfall for Proposed City of Santa Clarita, 9-18-1987.', null],
  5671 => ['lw3808.htm', 'Interrupted Mail: Letter (Envelope) Recovered from Fatal Plane Crash, 11-18-1930.', null],
  4791 => ['lw2998.htm', 'Interrupted Mail: Letter (Envelope) Recovered from Fatal Plane Crash, 11-18-1930.', null],
];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($P as $id => [$page, $from, $to]) {
  $e = Entry::find()->id($id)->section('photographs')->status(null)->one(); if (!$e) { echo "#$id missing" . PHP_EOL; continue; }
  $html = (string)@file_get_contents($R . $page);
  if (!preg_match('~<title>(.*?)</title>~is', $html, $m)) { echo "#$id: no title tag in $page; refused" . PHP_EOL; continue; }
  $cat = trim(preg_replace(['/\s+/', '/^SCVHistory\.com\s*(\|\s*)?/'], [' ', ''], html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5)));
  $msg = []; $dirty = false;
  $now = trim((string)$e->catalogueCaption);
  if ($now === '') { $e->setFieldValue('catalogueCaption', $cat); $msg[] = "catalogue entry = \"$cat\""; $dirty = true; }
  elseif ($now !== $cat) { $msg[] = "catalogue entry already \"$now\", left"; }
  if ($to !== null && $e->title !== $to) {
    if ($e->title !== $from) { echo "#$id holds \"{$e->title}\", not \"$from\"; left" . PHP_EOL; continue; }
    $e->title = $to; $msg[] = "title \"$from\" -> \"$to\""; $dirty = true;
  }
  echo "#$id ($page): " . ($msg ? implode('; ', $msg) : 'done already') . PHP_EOL;
  if ($APPLY && $dirty) { if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('held_photos_titles_2026_10_09.php', $n, 'verified', "$n held photographs: catalogue entry, and the printed head where there is one"); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
