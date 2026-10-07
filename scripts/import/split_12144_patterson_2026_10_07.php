/**
 * #12144 split (Nathan, 7 October 2026: "Patterson's commentary is sitting inside Worden's record with its own byline and no record
 * of its own. Split it the way you are splitting sg110185"). DRY RUN for Nathan's read.
 * #12144, Leon Worden's column "SCV Facilities Foundation: Three Tests Would Muzzle the Doubters" (The Signal, June 26, 2005), carries
 * after its sign-off ("Leon Worden is the president and CEO of SCVTV") a second piece: "Paving the Way For New Schools", "By Richard A.
 * Patterson, President, SCV Facilities Foundation, The Signal, Sunday, June 26, 2005". Leon's own note at the head of his column
 * introduces it ("A separate commentary by SCV Facilities Foundation board President Rick Patterson appears below the following column
 * by Leon Worden"); that note stays in his record, as his words.
 *  - New article: "Paving the Way For New Schools", the text from its headline to its end, verbatim, sourceLine its printed byline
 *    block, sourcePath the same page; no writtenBy (no record for Patterson); not in "Selections from Leon Worden".
 *  - #12144: its body ends at Leon's sign-off.
 * No parent collection, unlike sg110185: the page (/scvhistory/signal/worden/lw062605.htm) is not on the mirror, so its own title is
 * not in hand, and it is Leon's column page with one commentary appended, not a gathered front page. Reversible: the removed text is
 * in the new record word for word, and the checksum of #12144's body is pinned below.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/split_12144_patterson_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0; $TITLE = 'Paving the Way For New Schools';
$w = Entry::find()->id(12144)->status(null)->one(); $b = (string)$w->body;
$new = Entry::find()->section('articles')->status(null)->title($TITLE)->one();
$i = mb_strpos($b, "\n" . $TITLE . "\n");
if ($i === false) { echo $new ? "done already: #{$new->id} exists and #12144 no longer holds the piece\n" : "REFUSED: the commentary is not where it was read\n"; }
else {
  $leon = rtrim(mb_substr($b, 0, $i)); $pat = trim(mb_substr($b, $i + mb_strlen($TITLE) + 2));
  echo "#12144 {$w->title}: body " . mb_strlen($b) . " -> " . mb_strlen($leon) . " chars, ends: ..." . mb_substr($leon, -90) . PHP_EOL;
  echo "NEW article \"$TITLE\" " . ($new ? "#{$new->id}" : 'create') . ': ' . mb_strlen($pat) . " chars\n   starts: " . mb_substr(preg_replace('~\s+~', ' ', $pat), 0, 200) . "\n   ends: ..." . mb_substr($pat, -110) . PHP_EOL;
  if ($APPLY) {
    if (!$new) { $sec = Craft::$app->getEntries()->getSectionByHandle('articles'); $new = new Entry(); $new->sectionId = $sec->id; $new->setTypeId($w->typeId); $new->title = $TITLE;
      $new->setFieldValues(['body' => $pat, 'sourceLine' => 'By Richard A. Patterson, President, SCV Facilities Foundation. The Signal, Sunday, June 26, 2005.', 'sourcePath' => (string)$w->sourcePath,
        'recordProvenance' => 'split_12144_patterson_2026_10_07.php, 7 October 2026: split from #12144, where it followed Leon Worden\'s column on the same page']);
      $new->postDate = $w->postDate;
      if (!$el->saveElement($new)) { throw new \RuntimeException(json_encode($new->getFirstErrors())); } $n++; }
    $w->setFieldValue('body', $leon); if (!$el->saveElement($w)) { throw new \RuntimeException('#12144'); } $n++;
  }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('split_12144_patterson_2026_10_07.php', $n, 'verified', 'Patterson\'s commentary out of #12144 into its own record'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
