/**
 * Portraits the old site names but whose files the archive does not hold (Nathan, 6 October 2026: "note on each record that the
 * page names a portrait the mirror does not hold, so a later pass knows to ask Leon"). From the portrait search of 6 October
 * (inventory/review/portrait-search-2026-10-06.md). Written to researchLeads, which is internal and not shown on the page.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/missing_portrait_leads_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$L = [
  25399 => ['Gary Murr', '/scvhistory/sg062505.htm (his 2005 death notice) shows a portrait, gif/mugs/murr_gary.jpg, alt text "Gary Murr"'],
  2582 => ['Sol Taylor', '/scvhistory/signal/coins/worden-coinage0606.htm (Leon Worden\'s coin column, June 2006) shows gif/worden-coinage0606b.jpg, captioned "Sol Taylor. (Photo: Leon Worden)"'],
  28364 => ['Philip Ellis Jr.', '/oldtownnewhall/gazette/gazette1101-nrc.htm (Old Town Newhall Gazette, November and December 2005) shows oldtownnewhall/gif/mugs/ellis_phil.jpg, alt text "Phil Ellis", over the byline "By PHILIP ELLIS, Chairman, Newhall Redevelopment Committee"; that this is the same man as this record rests on the Ellis citation on his articles'],
];
foreach ($L as $id => [$name, $what]) {
  $p = Entry::find()->id($id)->status(null)->one(); if ($p?->title !== $name) { throw new \RuntimeException("#$id is not $name"); }
  $lead = "Portrait (6 October 2026): the page $what. The image file is not among the original site's files the archive holds. Ask Leon Worden for it.";
  $cur = (string)$p->researchLeads;
  echo "#$id $name: " . (str_contains($cur, 'Ask Leon Worden for it') ? 'noted already' : 'add the lead') . PHP_EOL;
  if ($APPLY && !str_contains($cur, 'Ask Leon Worden for it')) { $p->setFieldValue('researchLeads', trim($cur . "\n\n" . $lead)); if (!$el->saveElement($p)) { throw new \RuntimeException("#$id"); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('missing_portrait_leads_2026_10_06.php', $n, 'verified', 'three portraits named on the old site but not held: a lead on each'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
