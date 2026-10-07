/**
 * sg110185 as a parent and its eight pieces (Nathan, 7 October 2026: "build it as you propose. Parent collection ... eight article
 * children, the sidebar navigation discarded ... Link both [Scott and Ruth Newhall] as authors ... Dry run for my read").
 * The page, /scvhistory/sg110185.htm, is Leon Worden's "L.A. Mayor's Saugus State Prison Plan Fans Flames of Cityhood, 1985": his
 * webmaster's note, then seven pieces from The Signal of November 1, 1985 and one from the Los Angeles Times of November 5, 1985.
 * The pieces are read from inventory/review/sg110185/sg110185-pieces-2026-10-07.json (parsed from the original site's page; text
 * verbatim). The old site's sidebar ("Crazy stuff that didn't happen", "Saugus Drunk Farm" and the rest) is navigation and goes.
 *  - The parent: a new collection with Leon's title, kind "topic", his webmaster's note verbatim, the credit line, and the legacy
 *    URL, which moves to it from #28295.
 *  - The children: articles (Nathan, 7 October 2026: "seven articles titled with the printed headline, nothing appended";
 *    sourceDocuments now takes articles too, widen_source_links_2026_10_07.php). #28295 stays a document until Nathan rules on type. #28295 is Karina Lutz's "City Backers Join Prison Furor" already, title, byline and text agreeing with the
 *    page; it is kept as that child. Its "titled Lutz, printed Kay" flag was the byline census reading the page's first byline
 *    (Lauren Kay's, on "Bradley: Build Big House Here"), not a fault in the record. The other seven are created.
 *  - writtenBy: Scott Newhall (#31431) on the editorial ("Scott Newhall, Editor."), Ruth Newhall (#15477) on "Does L.A. Own the
 *    Land?". The other bylines name people with no record and stay as printed in each piece's source line.
 *  - The clipping scans beside each piece are not imported here (they are on the original site's files; a later pass).
 * A topic collection lists every record related to it, articles and documents alike (templates/collections/_topic.twig).
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/sg110185_front_page_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $es = Craft::$app->getEntries(); $n = 0;
$D = json_decode(file_get_contents("$root/inventory/review/sg110185/sg110185-pieces-2026-10-07.json"), true);
$LEGACY = '/scvhistory/sg110185.htm'; $SRC = 'https://scvhistory.com/scvhistory/sg110185.htm';
$old = Entry::find()->id(28295)->status(null)->one();
if (!$old || !str_starts_with($old->title, 'City Backers Join Prison Furor')) { throw new \RuntimeException('#28295 is not as expected'); }
$AUTH = ['Scott Newhall, Editor.' => 31431, 'By Ruth Newhall.' => 15477];
foreach ($AUTH as $by => $pid) { if (!Entry::find()->id($pid)->status(null)->exists()) { throw new \RuntimeException("author #$pid missing"); } }
/* the parent */
$par = Entry::find()->section('collections')->status(null)->title($D['leonTitle'])->one();
echo "PARENT collection \"{$D['leonTitle']}\": " . ($par ? "exists #{$par->id}" : 'create') . "; kind topic; legacy URL $LEGACY (from #28295); webmaster's note " . count($D['webmasterNote']) . ' paragraphs; credit: ' . $D['credit'] . PHP_EOL;
foreach ($D['webmasterNote'] as $p) { echo '   | ' . mb_substr($p, 0, 140) . (mb_strlen($p) > 140 ? '...' : '') . PHP_EOL; }
if ($APPLY && !$par) {
  $sec = $es->getSectionByHandle('collections'); $par = new Entry(); $par->sectionId = $sec->id; $par->setTypeId($sec->getEntryTypes()[0]->id); $par->title = $D['leonTitle'];
  $par->setFieldValues(['collectionKind' => 'topic', 'legacyUrl' => $LEGACY, 'sourcePath' => $SRC, 'webmasterNoteTop' => implode("\n\n", $D['webmasterNote']), 'webmasterNoteBottom' => $D['credit'],
    'sourceLine' => 'The Signal, November 1, 1985; the Los Angeles Times, November 5, 1985. Webmaster\'s note by Leon Worden.', 'recordProvenance' => 'sg110185_front_page_2026_10_07.php, 7 October 2026: Leon Worden\'s page as a collection of its eight pieces']);
  if (!$el->saveElement($par)) { throw new \RuntimeException('parent ' . json_encode($par->getFirstErrors())); } $n++;
}
/* the children: articles, titled with the printed headline and nothing appended (Nathan, 7 October 2026: "Title is what the
   paper printed, and the byline, paper and date are fields, not part of the name"). The byline, paper and date go in sourceLine
   and originalPublishDate, the paper in publishedBy. #28295 stays the document it is until Nathan rules on type. */
$artSec = $es->getSectionByHandle('articles'); $k = 0;
$PAPER = ['The Signal' => 376, 'Los Angeles Times' => 30518];
foreach ($PAPER as $nm => $oid) { $o = Entry::find()->id($oid)->status(null)->one(); if (!$o) { throw new \RuntimeException("publisher #$oid missing"); } }
foreach ($D['pieces'] as $p) {
  $k++; [$paper, $date] = array_map('trim', explode('|', $p['dateline'])); $date = rtrim($date, '.');
  $title = $p['title']; $pid = $AUTH[$p['byline']] ?? null; $body = implode("\n\n", $p['paragraphs']);
  $isOld = str_starts_with($title, 'City Backers Join Prison Furor');
  $art = $isOld ? $old : Entry::find()->section('articles')->status(null)->all();
  if (!$isOld) { $art = array_values(array_filter($art, fn($x) => $x->title === $title))[0] ?? null; }
  $line = ($p['kicker'] ? rtrim($p['kicker'], '.') . '. ' : '') . rtrim($p['byline'], '.') . '. ' . $paper . ', ' . $date . '.';
  echo "CHILD $k" . ($p['kicker'] ? " ({$p['kicker']})" : '') . ": \"$title\" " . ($art ? "#{$art->id}" . ($isOld ? ' (document, kept as it is; legacy URL and webmaster note move to the parent)' : ' exists') : 'create article') . " | sourceLine \"$line\" | date \"$date\" | publishedBy #{$PAPER[$paper]} | " . count($p['paragraphs']) . ' paragraphs' . ($pid ? " | writtenBy #$pid, printed byline" : '') . PHP_EOL;
  if (!$APPLY) { continue; }
  if (!$art) { $art = new Entry(); $art->sectionId = $artSec->id; $art->setTypeId($artSec->getEntryTypes()[0]->id); $art->title = $title;
    $art->setFieldValues(['body' => $body, 'sourceLine' => $line, 'originalPublishDate' => $date, 'publishedBy' => [$PAPER[$paper]], 'sourcePath' => $SRC,
      'recordProvenance' => "sg110185_front_page_2026_10_07.php, 7 October 2026: piece $k of Leon Worden's page"]);
    if ($pid) { $art->setFieldValues(['writtenBy' => [$pid], 'authorshipBasis' => 'printed-byline', 'authorshipBasisNote' => 'Printed byline "' . rtrim($p['byline'], '.') . '" under the headline, on Leon Worden\'s page of the November 1, 1985 coverage. Read from the original page.']); } }
  $v = ['partOfCollection' => [$par->id]];
  if ($isOld) { $v['legacyUrl'] = ''; $v['webmasterNoteTop'] = ''; }
  $art->setFieldValues($v); if (!$el->saveElement($art)) { throw new \RuntimeException("child $k " . json_encode($art->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('sg110185_front_page_2026_10_07.php', $n, 'verified', 'sg110185: Leon Worden\'s page as a collection and its eight pieces'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
