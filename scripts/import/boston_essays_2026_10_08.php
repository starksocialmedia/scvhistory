/**
 * John Boston's six essays on the original site, as articles in his collection #667 (Nathan, 8 October 2026: "Import Boston's
 * six essays and put them in collection #667, which currently has his name on it and nothing in it. Dry run first ... Link him
 * as author on all six, basis printed byline or whatever each page actually shows.").
 * Text from inventory/review/boston-essays/boston-essays-2026-10-08.json (boston_essays_parse_2026_10_08.py, read from Reggie):
 * verbatim paragraphs; the title is the printed headline, the deck goes to subheadline, the page's title tag to
 * legacyHeadline where it differs, Leon Worden's webmaster's note (Mentry) to webmasterNoteTop.
 * Authorship, as each page shows it:
 *  - the three parts of "Laying Down the Law in Early Santa Clarita" print no byline: series attribution, from the heading
 *    "JOHN BOSTON: LAYING DOWN THE LAW IN EARLY SANTA CLARITA" and the closing "(c)2000 JOHN BOSTON & SCV HISTORICAL SOCIETY";
 *    their dates are the series index's, the pages print none;
 *  - the other three print "By John Boston" and a date under the headline: printed byline.
 * No paper is named on any page, so publishedBy stays empty. The picture boxes beside the text are other records' photographs
 * and are not imported with the text (listed in the JSON).
 * Idempotent: an article is found by its legacy URL. Dry run by default. Set $APPLY = true.
 */
use craft\elements\Entry;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$reads = require "$root/scripts/import/_reads.php";
$reads([['file', 'the essays as parsed from the pages on Reggie', "$root/inventory/review/boston-essays/boston-essays-2026-10-08.json"]]);
$D = json_decode(file_get_contents("$root/inventory/review/boston-essays/boston-essays-2026-10-08.json"), true);
$TITLETAG = ['jb061800.htm' => 'Laying Down the Law in Early Santa Clarita (1)', 'jb062500.htm' => 'Laying Down the Law in Early Santa Clarita (2).',
  'jb070900.htm' => 'Laying Down the Law in Early Santa Clarita (3)', 'sg082803.htm' => "Santa Clarita Valley's Earthquake History",
  'jb100401b.htm' => 'How did Alex Mentry Die?', 'jb070200.htm' => 'Ode to Ruth Newhall on her 90th Birthday'];
$PART = ['jb061800.htm' => 1, 'jb062500.htm' => 2, 'jb070900.htm' => 3];
$boston = Entry::find()->id(2576)->status(null)->one(); $coll = Entry::find()->id(667)->status(null)->one();
if ($boston?->title !== 'John Boston' || !str_contains((string)$coll?->title, 'John Boston')) { throw new \RuntimeException('#2576 or #667 not as expected'); }
$sec = Craft::$app->getEntries()->getSectionByHandle('articles');
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
foreach ($D as $e) {
  $f = $e['file']; $date = $e['dateline'] ?: $e['indexDate'];
  $series = isset($PART[$f]);
  $line = $series
    ? "John Boston, \"Laying Down the Law in Early Santa Clarita,\" part {$PART[$f]}. $date (the date the series index gives; the page prints none). {$e['copyright']}."
    : "{$e['byline']}. $date. {$e['copyright']}.";
  $basis = $series ? 'series-attribution' : 'printed-byline';
  $note = $series
    ? 'The series heading on the original page reads "JOHN BOSTON: LAYING DOWN THE LAW IN EARLY SANTA CLARITA", and the page ends "' . mb_strtoupper($e['copyright']) . ' | RIGHTS RESERVED.". No byline under the headline. Read from the original page.'
    : 'Printed byline "' . $e['byline'] . "\" under the headline, dated $date. Read from the original page.";
  $legacyHead = $TITLETAG[$f] !== $e['title'] ? $TITLETAG[$f] : '';
  $wm = $e['webmasterNote'] ? preg_replace('/Click here\.$/', 'Click here (/scvhistory/as0001.htm).', $e['webmasterNote']) : '';
  $art = Entry::find()->section('articles')->status(null)->legacyUrl($e['legacyUrl'])->one();
  echo ($art ? "#{$art->id} exists" : 'create') . " \"{$e['title']}\"" . ($e['subheadline'] ? " | deck: {$e['subheadline']}" : '') . " | $date | " . count($e['paragraphs']) . " paragraphs | writtenBy #2576, $basis | in #667"
    . ($legacyHead ? " | legacyHeadline \"$legacyHead\"" : '') . ($wm ? " | webmaster's note: $wm" : '') . PHP_EOL . "    sourceLine: $line" . PHP_EOL . "    basis note: $note" . PHP_EOL;
  if (!$APPLY) { continue; }
  if (!$art) { $art = new Entry(); $art->sectionId = $sec->id; $art->setTypeId($sec->getEntryTypes()[0]->id); }
  $art->title = $e['title'];
  $art->setFieldValues(['body' => implode("\n\n", $e['paragraphs']), 'subheadline' => $e['subheadline'], 'sourceLine' => $line,
    'originalPublishDate' => (new \DateTime($date))->format('Y-m-d'), 'legacyUrl' => $e['legacyUrl'], 'sourcePath' => 'https://scvhistory.com' . $e['legacyUrl'],
    'legacyHeadline' => $legacyHead, 'webmasterNoteTop' => $wm, 'writtenBy' => [2576], 'authorshipBasis' => $basis, 'authorshipBasisNote' => $note,
    'partOfCollection' => [667], 'recordProvenance' => "boston_essays_2026_10_08.php, 8 October 2026: John Boston's essay, from the original site ({$e['legacyUrl']})"]);
  if (!$el->saveElement($art)) { throw new \RuntimeException("$f " . json_encode($art->getFirstErrors())); } $n++;
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('boston_essays_2026_10_08.php', $n, 'verified', "John Boston's six essays as articles in #667, written by him"); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
