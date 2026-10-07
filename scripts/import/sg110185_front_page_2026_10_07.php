/**
 * sg110185 as a parent and its eight pieces (Nathan, 7 October 2026: "build it as you propose. Parent collection ... eight article
 * children, the sidebar navigation discarded ... Link both [Scott and Ruth Newhall] as authors ... Dry run for my read").
 * The page, /scvhistory/sg110185.htm, is Leon Worden's "L.A. Mayor's Saugus State Prison Plan Fans Flames of Cityhood, 1985": his
 * webmaster's note, then seven pieces from The Signal of November 1, 1985 and one from the Los Angeles Times of November 5, 1985.
 * The pieces are read from inventory/review/sg110185/sg110185-pieces-2026-10-07.json (parsed from the original site's page; text
 * verbatim). The old site's sidebar ("Crazy stuff that didn't happen", "Saugus Drunk Farm" and the rest) is navigation and goes.
 *  - The parent: a new collection with Leon's title, kind "topic", his webmaster's note verbatim, the credit line, and the legacy
 *    URL, which moves to it from #28295.
 *  - The children: documents, not articles, so the Cityhood event's sourceDocuments link to #28295 stays valid (that field takes
 *    documents only). #28295 is Karina Lutz's "City Backers Join Prison Furor" already, title, byline and text agreeing with the
 *    page; it is kept as that child. Its "titled Lutz, printed Kay" flag was the byline census reading the page's first byline
 *    (Lauren Kay's, on "Bradley: Build Big House Here"), not a fault in the record. The other seven are created.
 *  - writtenBy: Scott Newhall (#31431) on the editorial ("Scott Newhall, Editor."), Ruth Newhall (#15477) on "Does L.A. Own the
 *    Land?". The other bylines name people with no record and stay as printed in each piece's source line.
 *  - The clipping scans beside each piece are not imported here (they are on the original site's files; a later pass).
 * The collection page lists documents as well as articles after the template change made with this build.
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
/* the children */
$docSec = $es->getSectionByHandle('documents'); $k = 0;
foreach ($D['pieces'] as $p) {
  $k++; [$paper, $date] = array_map('trim', explode('|', $p['dateline']));
  $date = preg_replace('~^[A-Z][a-z]+day, ~', '', rtrim($date, '.')); $name = preg_replace('~^By ~', '', rtrim(preg_replace('~, Editor\.$~', '', $p['byline']), '.'));
  $title = $p['title'] . " ($name, $paper, $date)";
  $body = implode("\n\n", $p['paragraphs']); $pid = $AUTH[$p['byline']] ?? null;
  $doc = str_starts_with($p['title'], 'City Backers Join Prison Furor') ? $old : Entry::find()->section('documents')->status(null)->title($title)->one();
  echo "CHILD $k" . ($p['kicker'] ? " ({$p['kicker']})" : '') . ": \"$title\" " . ($doc ? "#{$doc->id}" . ($doc->id === 28295 ? ' (kept; legacy URL and webmaster note move to the parent)' : '') : 'create') . " | {$p['byline']} | " . count($p['paragraphs']) . ' paragraphs' . ($pid ? " | writtenBy #$pid" : '') . PHP_EOL;
  echo '   first: ' . mb_substr($p['paragraphs'][0], 0, 110) . PHP_EOL . '   last:  ' . mb_substr(end($p['paragraphs']), -90) . PHP_EOL;
  if (!$APPLY) { continue; }
  if (!$doc) { $doc = new Entry(); $doc->sectionId = $docSec->id; $doc->setTypeId($docSec->getEntryTypes()[0]->id); $doc->title = $title;
    $doc->setFieldValues(['body' => $body, 'sourceLine' => $p['dateline'] . ' ' . $p['byline'], 'sourcePath' => $SRC, 'recordProvenance' => "sg110185_front_page_2026_10_07.php, 7 October 2026: piece $k of Leon Worden's page"]); }
  $v = ['partOfCollection' => [$par->id]]; if ($pid) { $v['writtenBy'] = [$pid]; }
  if ($doc->id === 28295) { $v['legacyUrl'] = ''; $v['webmasterNoteTop'] = ''; }
  $doc->setFieldValues($v); if (!$el->saveElement($doc)) { throw new \RuntimeException("child $k " . json_encode($doc->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('sg110185_front_page_2026_10_07.php', $n, 'verified', 'sg110185: Leon Worden\'s page as a collection and its eight pieces'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
