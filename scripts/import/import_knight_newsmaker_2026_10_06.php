/**
 * Pete Knight's final television interview, the Signal's "Newsmaker of the Week" taped on 1 April 2004 and printed on
 * Sunday 25 April 2004, as a document record (Nathan, 6 October 2026: "Lead with the archive's own source ... Import it").
 * Legacy page /scvhistory/signal/newsmaker/sg042504.htm, read from the Reggie mirror by
 * scripts/import/extract_knight_newsmaker_2026_10_06.py into inventory/legacy/pete-knight-newsmaker-2004.json (page sha256
 * checked against the mirror manifest; every paragraph checked as a contiguous word run in the page).
 * - body: the interview verbatim (the program's introduction, the questions and answers, the closing line), then the two
 *   boxed texts the page printed beside it, under their printed headings. The page's own slips are kept ("singed on as
 *   intervenors", "In may or June"); no [sic] added.
 * - webmasterNoteTop: the editor's note printed above it after his death, verbatim: the site's framing.
 * - webmasterNoteBottom: what the page printed besides, and what is not in this record (the two photographs, the video link).
 * - sourceLine: byline, date, taping note and copyright, as printed. publishedBy: The Signal #376, which printed it (its City
 *   Editor's byline, "Signal:" on every question, the Sunday date), and SCVTV #30520, whose copyright line it carries.
 *   writtenBy: Leon Worden #279, the interviewer. subjectPerson: Pete Knight #29314. partOfCollection: Newsmaker of the Week #671.
 * - originallyPublishedTitle stays empty: the page's slug ("William J. "Pete" Knight / State Senator") may be the paper's
 *   headline or the site's, and the page does not say which.
 * Idempotent: matched by legacyKey sg042504. Dry run by default. Set $APPLY = true.
 * Prints inventory/review/pete-knight-newsmaker-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_knight_newsmaker_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $sec = Craft::$app->getEntries()->getSectionByHandle('documents');
$J = json_decode(file_get_contents("$root/inventory/legacy/pete-knight-newsmaker-2004.json"), true);
$KEY = 'sg042504'; $SIGNAL = 376; $SCVTV = 30520; $WORDEN = 279; $KNIGHT = 29314; $COLL = 671;
$bad = [];
$expect = [$SIGNAL => 'The Santa Clarita Valley Signal', $SCVTV => 'SCVTV', $WORDEN => 'Leon Worden', $KNIGHT => 'Pete Knight', $COLL => 'Newsmaker of the Week'];
foreach ($expect as $id => $t) { $e = Entry::find()->id($id)->status(null)->one(); if (!$e || $e->title !== $t) { $bad[] = "#$id is not $t"; } }
if (($J['page']['manifestCheck'] ?? '') !== 'match') { $bad[] = 'page sha256 does not match the mirror manifest'; }
if (($J['verbatimCheck'] ?? '') === '') { $bad[] = 'extract not verbatim-checked'; }
$title = "Newsmaker of the Week: State Sen. William J. \"Pete\" Knight (Leon Worden, The Signal, April 25, 2004)";
$top = $J['editorsNote'];
$bottom = implode("\n\n", [
    'Beside the interview the page printed two boxed texts, "Knight\'s Military Background" and "Official Biography"; they follow the interview here under their printed headings. The page does not say who wrote them.',
    'The page also carried two photographs, captioned "Pete Knight on April 1, 2004" and "Pete Knight, 1965"; they are not part of this record. It linked the broadcast on SCVTV at ' . $J['videoLink'] . '.',
]);
$v = [
    'webmasterNoteTop' => $top, 'body' => $J['body'], 'originalPublishDate' => $J['dateline'], 'originalPublishDateEdtf' => '2004-04-25',
    'sourceLine' => implode('. ', [implode(', ', $J['byline']), $J['dateline'], '(' . $J['taped'] . ')', rtrim($J['copyright'], '.')]) . '.',
    'publishedBy' => [$SIGNAL, $SCVTV], 'writtenBy' => [$WORDEN], 'subjectPerson' => [$KNIGHT], 'partOfCollection' => [$COLL],
    'webmasterNoteBottom' => $bottom, 'legacyKey' => $KEY, 'legacyUrl' => $J['page']['legacyUrl'], 'sourcePath' => 'https://scvhistory.com' . $J['page']['legacyUrl'],
];
if (preg_match('~\x{2014}~u', $title . $bottom . $v['sourceLine'])) { $bad[] = 'an em dash in our own text'; }
$have = Entry::find()->section('documents')->status(null)->legacyKey($KEY)->one();
$byUrl = Entry::find()->status(null)->legacyUrl($J['page']['legacyUrl'])->one();
if (!$have && $byUrl) { $bad[] = "the page is already held as #{$byUrl->id} ({$byUrl->section->handle}) under another key"; }
$paras = explode("\n\n", $J['body']);
$md = "# Pete Knight's final interview: the document record, dry run\n\n" . ($have ? "exists #{$have->id}, nothing to do" : 'create, published') . "\n\n"
    . "- title: $title\n- published by: The Santa Clarita Valley Signal #$SIGNAL; SCVTV #$SCVTV\n- written by: Leon Worden #$WORDEN; about: Pete Knight #$KNIGHT; collection: Newsmaker of the Week #$COLL\n"
    . "- source line: {$v['sourceLine']}\n- date: {$v['originalPublishDate']} ({$v['originalPublishDateEdtf']}); taped April 1, 2004\n- old address: {$v['legacyUrl']}; page sha256 {$J['page']['sha256']} (manifest {$J['page']['manifestCheck']})\n"
    . "- body: " . count($paras) . " paragraphs, " . str_word_count($J['body']) . " words ({$J['interviewParagraphs']} interview, {$J['militaryParagraphs']} military background, {$J['officialParagraphs']} official biography)\n\n"
    . "Top note, verbatim: $top\n\nBottom note: " . str_replace("\n\n", ' ', $bottom) . "\n\n## The body, in full, as it will read\n\n" . $J['body'] . "\n";
file_put_contents("$root/inventory/review/pete-knight-newsmaker-dry-run-2026-10-06.md", $md);
echo ($have ? "exists #{$have->id}" : 'create') . '; ' . count($paras) . ' paragraphs; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "; printed to inventory/review/pete-knight-newsmaker-dry-run-2026-10-06.md\n";
if (!$APPLY || $have) { echo ($APPLY ? 'already held, nothing written' : 'nothing written') . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$d = new Entry(); $d->sectionId = $sec->id; $d->setTypeId($sec->getEntryTypes()[0]->id); $d->title = $title; $d->enabled = true;
$lay = array_map(fn($f) => $f->handle, $d->getFieldLayout()->getCustomFields());
$missing = array_diff(array_keys($v), $lay); if ($missing) { echo 'REFUSING: not on the document layout: ' . implode(', ', $missing) . PHP_EOL; return; }
$d->setFieldValues($v);
if (!$el->saveElement($d)) { throw new \RuntimeException('sg042504 ' . json_encode($d->getFirstErrors())); }
$r = Entry::find()->id($d->id)->status(null)->one();
$ok = (string)$r->body === $J['body'] && (string)$r->webmasterNoteTop === $top && $r->publishedBy->status(null)->ids() == [$SIGNAL, $SCVTV] && $r->subjectPerson->status(null)->ids() == [$KNIGHT];
echo 'READ-BACK ' . ($ok ? "OK: #{$r->id} {$r->url}" : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('import_knight_newsmaker_2026_10_06.php', 1, $ok ? 'verified' : 'SHORT', "Pete Knight's final interview, Newsmaker of the Week, 25 April 2004: document #{$r->id}");
if (!$ok) { throw new \RuntimeException('import_knight_newsmaker: read-back failed'); }
