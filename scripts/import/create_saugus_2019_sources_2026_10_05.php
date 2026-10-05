/**
 * The Saugus High School shooting, step 4: the source records (Nathan, 5 October 2026: "Approve the Saugus entity dry run.
 * Build the 16 source records"). From inventory/legacy/saugus-high-2019-sources.json (the verbatim extract, checked word for
 * word against the pages), the 16 text pieces become document records; the three video introductions wait for the videos (D11).
 * - body: the piece verbatim. In the SCVTV fund story the seven private contact details are already "[contact details
 *   withheld]" (D8), and webmasterNoteBottom says so.
 * - webmasterNoteTop: the site's own introduction, verbatim, where the page printed one (the letter's).
 * - editorNotes: the content advisory, top, on every record (DATA-MODEL, Content advisories); and on the two reports that give
 *   Gracie Muehlberger's age as 16, a correction: she was 15 (Nathan: "Her age is a fact about her and worth getting right").
 * - publishedBy: The Signal #376, the Los Angeles Times #30518, SCVTV #30520, the Hart district #21588. The letter has none:
 *   it is the family's own words, released by them.
 * - The eight Los Angeles Times pieces are saved disabled until the rights are settled (D5).
 * - No person, place or event relations: the event, last, cites these records (D3, D4, D9; DATA-MODEL, No connection the
 *   sources do not make). The shooter's name stays only inside the verbatim bodies (D2); titles never carry it.
 * The letter is made first, so the corrections can cite it by number. Idempotent: a record is matched by legacyKey.
 * Prints inventory/review/saugus-high-2019-sources-dry-run-2026-10-05.md. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_saugus_2019_sources_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $sec = Craft::$app->getEntries()->getSectionByHandle('documents');
$J = json_decode(file_get_contents("$root/inventory/legacy/saugus-high-2019-sources.json"), true);
$PUB = ['The Signal' => 376, 'Los Angeles Times' => 30518, 'SCVTV' => 30520, 'SCVTV/SCVNews.com' => 30520, 'William S. Hart Union High School District' => 21588];
foreach (array_unique($PUB) as $id) { if (!Entry::find()->id($id)->status(null)->exists()) { throw new \RuntimeException("no publisher #$id"); } }
/* Who wrote each piece, as the title gives it; the byline stays as printed in sourceLine and the body. */
$AUTH = ['sg20191114-signal-holt' => 'Jim Holt', 'lat20191115-gerber' => 'Marisa Gerber and others', 'scvtv20191115-press-conference' => '',
  'scvtv20191115-fund-peeples' => 'Stephen K. Peeples', 'lat20191116a-shining-light' => 'Colleen Shalby and others', 'lat20191116b-victims-identified' => 'Alejandra Reyes-Velarde and Colleen Shalby',
  'lat20191116c-firearms-seized' => 'Hannah Fry and others', 'lat20191116d-search-for-answers' => 'Brittny Mejia and others', 'lat20191116e-banks-commentary' => 'Sandy Banks',
  'sg20191117-signal-alvarenga' => 'Emily Alvarenga', 'bryanmuehlberger20191117-letter' => 'the Muehlberger family', 'lat20191118a-thousands-mourn' => 'Sandy Banks and Laura Newberry',
  'lat20191118b-new-wave-of-grief' => 'Marisa Gerber', 'sg20191119-signal-murga' => 'Tammy Murga', 'hd20200112-district-kuhlman' => 'Mike Kuhlman', 'hd20200406-district-ferry' => ''];
$PUBNAME = ['The Signal' => 'The Signal', 'Los Angeles Times' => 'Los Angeles Times', 'SCVTV' => 'SCVTV', 'SCVTV/SCVNews.com' => 'SCVNews.com', 'William S. Hart Union High School District' => 'William S. Hart Union High School District'];
$ADVISORY = 'This record concerns a school shooting in which students were killed, and describes injuries and a suicide.';
$pieces = array_values(array_filter($J['pieces'], fn($p) => $p['kind'] !== 'video-introduction'));
usort($pieces, fn($a, $b) => ($b['kind'] === 'letter') <=> ($a['kind'] === 'letter'));
if (count($pieces) !== 16) { throw new \RuntimeException(count($pieces) . ' pieces, not 16'); }
$perPage = array_count_values(array_column($J['pieces'], 'page'));
$out = []; $n = 0; $letter = null; $bad = [];
foreach ($pieces as $p) {
  $k = $p['key']; if (!array_key_exists($k, $AUTH)) { $bad[] = "$k: no author entry"; continue; }
  $isLetter = $p['kind'] === 'letter'; $isLat = $p['publisher'] === 'Los Angeles Times';
  $date = (new \DateTime($p['dateIso']))->format('F j, Y');
  $head = preg_replace('~\.(["\x{201D}]?)$~u', '$1', trim($p['headline']));
  $who = array_filter([$AUTH[$k], $isLetter ? '' : ($PUBNAME[$p['publisher']] ?? '')]);
  $title = $head . ' (' . implode(', ', array_merge($who, [$date])) . ')';
  if (preg_match('~[\x{2014}]~u', $title)) { $bad[] = "$k: em dash in title"; }
  $printedDate = trim(preg_replace('~^.*\|\s*~', '', rtrim($p['date'], '.')));
  $notes = [['heading' => '', 'note' => $ADVISORY, 'position' => 'top']];
  if (in_array($k, ['sg20191114-signal-holt', 'lat20191115-gerber'], true)) {
    $notes[] = ['heading' => 'Correction, 2026', 'note' => 'Gracie Muehlberger was 15, not 16. She was born on October 10, 2004, as her parents\' letter says (archive document #' . ($letter?->id ?? '(the letter)') . '), and the Los Angeles Times gave her age as 15 when the authorities identified her on November 15, 2019 ("Shooting Victims Identified").', 'position' => 'bottom'];
  }
  $bottom = [];
  if ($perPage[$p['page']] > 1) { $bottom[] = 'One of ' . $perPage[$p['page']] . ' items on the page ' . basename($p['page'], '.htm') . ' on SCVHistory.com; each is its own record.'; }
  if (count($p['redactions'] ?? [])) { $bottom[] = 'The ' . ([1 => 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'][count($p['redactions'])] ?? count($p['redactions'])) . ' private contact details this story printed, e-mail and LinkedIn addresses, one of them a 17-year-old student\'s, are replaced here with "[contact details withheld]" (Nathan Imhoff, 5 October 2026). The story is otherwise as published.'; }
  $v = ['webmasterNoteTop' => (string)($p['siteIntroduction'] ?? ''), 'body' => $p['body'], 'originallyPublishedTitle' => trim($p['headline']), 'originalPublishDate' => $printedDate,
    'originalPublishDateEdtf' => $p['dateIso'], 'sourceLine' => trim(implode(' ', array_filter([$p['byline'] ?? '', $p['date']]))), 'publishedBy' => isset($PUB[$p['publisher']]) ? [$PUB[$p['publisher']]] : [],
    'webmasterNoteBottom' => implode("\n\n", $bottom), 'editorNotes' => $notes, 'legacyKey' => $k, 'legacyUrl' => $p['page'], 'sourcePath' => 'https://scvhistory.com' . $p['page']];
  $have = Entry::find()->section('documents')->status(null)->legacyKey($k)->one();
  $out[] = "## $title\n\n" . ($have ? "exists #{$have->id}" : 'create') . ($isLat ? ', saved disabled (D5)' : ', published') . "\n\npublished by: " . ($v['publishedBy'] ? Entry::find()->id($v['publishedBy'][0])->status(null)->one()->title : 'none (the family)') . "; source line: {$v['sourceLine']}; old address: {$p['page']}\n\n"
    . implode("\n\n", array_map(fn($x) => '> Editor\'s note, ' . $x['position'] . ($x['heading'] ? ', "' . $x['heading'] . '"' : '') . ': ' . $x['note'], $notes)) . "\n\n"
    . ($v['webmasterNoteTop'] !== '' ? "Introduction, verbatim: {$v['webmasterNoteTop']}\n\n" : '') . ($v['webmasterNoteBottom'] !== '' ? "Bottom note: {$v['webmasterNoteBottom']}\n\n" : '')
    . ($isLetter ? "The letter, in full, as it will read:\n\n" . $p['body'] . "\n" : 'Body: ' . str_word_count($p['body']) . ' words, verbatim, opening: "' . mb_substr($p['body'], 0, 160) . '..."' . "\n");
  if (!$APPLY || $have) { if ($isLetter) { $letter = $have; } continue; }
  $d = new Entry(); $d->sectionId = $sec->id; $d->setTypeId($sec->getEntryTypes()[0]->id); $d->title = $title; $d->enabled = !$isLat;
  $lay = array_map(fn($f) => $f->handle, $d->getFieldLayout()->getCustomFields()); $d->setFieldValues(array_intersect_key($v, array_flip($lay)));
  if (!$el->saveElement($d)) { throw new \RuntimeException("$k " . json_encode($d->getFirstErrors())); } $n++;
  if ($isLetter) { $letter = $d; }
}
file_put_contents("$root/inventory/review/saugus-high-2019-sources-dry-run-2026-10-05.md", "# The Saugus High School shooting: the 16 source records, dry run\n\nThe letter first, in full; the others verbatim, shown by their opening. Nothing is written by the dry run.\n\n" . implode("\n", $out));
echo count($pieces) . " records; REFUSED: " . ($bad ? implode(' | ', $bad) : 'none') . "; printed to inventory/review/saugus-high-2019-sources-dry-run-2026-10-05.md\n";
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_saugus_2019_sources_2026_10_05.php', $n, 'verified', 'Saugus High School shooting: 16 source records, the 8 Los Angeles Times saved disabled'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
