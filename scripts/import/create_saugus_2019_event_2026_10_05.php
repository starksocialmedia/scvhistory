/**
 * The Saugus High School shooting, step 6: the event record (survey inventory/review/saugus-high-2019-survey-2026-10-05.md,
 * order of work item 6; Nathan's rulings of 5 October 2026). Last, after the 16 source records, which it cites.
 *
 * - Refuses until the sources exist: every one of the 16 documents is resolved by legacyKey at run time
 *   (create_saugus_2019_sources_2026_10_05.php makes them). The dry run still prints the full plan, with "#?" where an
 *   archive number is not known yet, so the prose can be read now.
 * - Every sentence of the body is footnoted to a source record by title and archive number. Each supporting phrase is
 *   checked against the verbatim extract (inventory/legacy/saugus-high-2019-sources.json) and the run refuses if one is
 *   missing. Nothing after April 2020 and nothing from general knowledge (D14).
 * - D2: the shooter is never named in the archive's own words, titles or structured data; the reasoning is an editor's
 *   note, so it reads as a decision. D3: Gracie Muehlberger and Dominic Blackwell named in the text. D4: the wounded not
 *   named. D7: the content advisory, top. D9: no eventPersons. D10: eventPlaces Santa Clarita Central Park #30515; no place
 *   record exists for the campus, so Saugus High School #21777 is related as an organization.
 * - No event-to-document relation exists: documents have no event field, eventArticles takes Articles only, and
 *   sourceDocuments is on the Election type only; footnotesOn means "the notes were published on another record"
 *   (templates/_partials/record/footnotes.twig), not a citation. So the documents are cited in the footnotes, and a bottom
 *   editor's note lists all 16 so none is orphaned. The eight Los Angeles Times records are saved disabled (D5), so their
 *   numbers render unlinked until Nathan settles the rights; their citations carry author, paper and date regardless.
 * - legacyUrl and eventLegacyUrl are left empty: where sg20191114shs.htm points is D12, not ruled.
 * Idempotent: matched on legacyKey "saugus-high-school-shooting-2019" and on title; an existing record is reported, never
 * rewritten. Prints inventory/review/saugus-high-2019-event-dry-run-2026-10-05.md. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_saugus_2019_event_2026_10_05.php'))"
 */
use craft\elements\{Entry, Category};
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root');
$J = json_decode(file_get_contents("$root/inventory/legacy/saugus-high-2019-sources.json"), true);
$bad = [];

/* The source records, by legacyKey; the title each will carry is computed exactly as the sources script computes it. */
$AUTH = ['sg20191114-signal-holt' => 'Jim Holt', 'lat20191115-gerber' => 'Marisa Gerber and others', 'scvtv20191115-press-conference' => '',
  'scvtv20191115-fund-peeples' => 'Stephen K. Peeples', 'lat20191116a-shining-light' => 'Colleen Shalby and others', 'lat20191116b-victims-identified' => 'Alejandra Reyes-Velarde and Colleen Shalby',
  'lat20191116c-firearms-seized' => 'Hannah Fry and others', 'lat20191116d-search-for-answers' => 'Brittny Mejia and others', 'lat20191116e-banks-commentary' => 'Sandy Banks',
  'sg20191117-signal-alvarenga' => 'Emily Alvarenga', 'bryanmuehlberger20191117-letter' => 'the Muehlberger family', 'lat20191118a-thousands-mourn' => 'Sandy Banks and Laura Newberry',
  'lat20191118b-new-wave-of-grief' => 'Marisa Gerber', 'sg20191119-signal-murga' => 'Tammy Murga', 'hd20200112-district-kuhlman' => 'Mike Kuhlman', 'hd20200406-district-ferry' => ''];
$PUBNAME = ['The Signal' => 'The Signal', 'Los Angeles Times' => 'Los Angeles Times', 'SCVTV' => 'SCVTV', 'SCVTV/SCVNews.com' => 'SCVNews.com', 'William S. Hart Union High School District' => 'William S. Hart Union High School District'];
$P = []; $DOC = []; $missing = [];
foreach ($J['pieces'] as $p) {
  if ($p['kind'] === 'video-introduction') { continue; }
  $k = $p['key']; $P[$k] = $p;
  $isLetter = $p['kind'] === 'letter';
  $head = preg_replace('~\.(["\x{201D}]?)$~u', '$1', trim($p['headline']));
  $who = array_filter([$AUTH[$k] ?? '', $isLetter ? '' : ($PUBNAME[$p['publisher']] ?? '')]);
  $title = $head . ' (' . implode(', ', array_merge($who, [(new \DateTime($p['dateIso']))->format('F j, Y')])) . ')';
  $d = Entry::find()->section('documents')->status(null)->legacyKey($k)->one();
  if (!$d) { $missing[] = $k; }
  elseif ($d->title !== $title) { $bad[] = "$k: document #{$d->id} is titled \"{$d->title}\", expected \"$title\""; }
  $DOC[$k] = ['id' => $d?->id, 'title' => $d ? $d->title : $title, 'enabled' => $d ? (bool)$d->enabled : ($p['publisher'] !== 'Los Angeles Times'), 'lat' => $p['publisher'] === 'Los Angeles Times'];
}
if (count($DOC) !== 16) { $bad[] = count($DOC) . ' source pieces in the extract, not 16'; }
if ($missing) { $bad[] = 'the source records do not exist yet (' . count($missing) . ' of 16 missing): run scripts/import/create_saugus_2019_sources_2026_10_05.php first'; }
$num = fn($k) => '#' . ($DOC[$k]['id'] ?? '?');
$cite = fn($k) => '"' . $DOC[$k]['title'] . ',"' . ' document ' . $num($k) . ' in this archive.';

/* Each claim, and the words in the source that carry it. The run refuses if a phrase is not in the verbatim text. */
$CHECK = [
  'bryanmuehlberger20191117-letter' => ['at 7:38am', 'Gracie was a freshman in high school at Saugus High School', 'would always extend a hand to those that needed a friend', 'Gracie Anne Muehlberger was born on October 10th, 2004'],
  'sg20191114-signal-holt' => ['first call was received at 7:38 a.m.', 'withdraw a handgun from his backpack, shoot and wound five people, and then shoot himself', 'The 16-year-old girl died at 9:23 a.m.', 'The boy was reported deceased around 11:40 a.m.', 'hundreds of students gathered at Central Park', 'waiting to be interviewed and checked out before being released to their parents', 'Two people were killed and four others, all students, were wounded', 'found six gunshot victims in the campus quad'],
  'lat20191116b-victims-identified' => ['16-second attack', 'Gracie Anne Muehlberger, 15, died at Henry Mayo Newhall Hospital in Valencia at 9:23 a.m.', 'Dominic Blackwell, 14, died Thursday.'],
  'sg20191117-signal-alvarenga' => ['Thousands gathered in Central Park Sunday night', 'Saugus Principal Vince Ferry had spoken publicly', 'Dominic guided his three brothers', 'fellow ROTC cadet', 'Riley said', 'wrote a letter that was read at the vigil', 'ASB President'],
  'lat20191116c-firearms-seized' => ['died Friday of his injuries', 'wounding three others in an attack that lasted 16 seconds'],
  'sg20191119-signal-murga' => ['three teenagers dead and three others wounded returned home Monday evening', 'Saugus High students returned to campus on Tuesday to recover belongings left behind last week and connect with counseling resources', 'classes are scheduled to resume Dec. 2 after the previously scheduled Thanksgiving break'],
  'lat20191118a-thousands-mourn' => ['Saugus High School will remain closed until Dec. 2.'],
  'hd20200112-district-kuhlman' => ['Director of the National Center for School Crisis and Bereavement at USC', 'counseling parents, teachers and administrators', "we've accelerated the introduction of wellness centers across the District, we've hired additional counselors", 'During the Spring Semester 2020, the Hart District will consult with three individuals', "Evaluate the Hart District's existing plans, policies and procedures", 'City of Santa Clarita'],
];
foreach ($CHECK as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($P[$k]['body'] ?? '', $ph)) { $bad[] = "$k does not read \"" . mb_substr($ph, 0, 70) . '"'; } } }
if (!str_contains((string)($P['bryanmuehlberger20191117-letter']['siteIntroduction'] ?? ''), '#SaugusStrong Vigil')) { $bad[] = 'the letter\'s introduction does not name the #SaugusStrong Vigil'; }

/* Footnotes, numbered in order of first appearance. The letter first (Nathan: the most important document in the set). */
$ORDER = ['bryanmuehlberger20191117-letter', 'sg20191114-signal-holt', 'lat20191116b-victims-identified', 'sg20191117-signal-alvarenga',
  'lat20191116c-firearms-seized', 'sg20191119-signal-murga', 'lat20191118a-thousands-mourn', 'hd20200112-district-kuhlman'];
$NOTES = [
  $cite($ORDER[0]) . ' Written for the vigil of November 17, 2019, and released for publication by the family on November 19. It gives Gracie\'s birth date, October 10, 2004: she was 15. The first reports, of November 14 and 15, said 16, and those two records carry a correction.',
  $cite($ORDER[1]) . ' The first call at 7:38 a.m., from fire officials; the account of the surveillance video, from the Sheriff\'s Department\'s Homicide Bureau; the times of the two deaths; the gathering and release of students at Central Park.',
  $cite($ORDER[2]) . ' The coroner\'s identification of the two students, and the 16 seconds.',
  $cite($ORDER[3]) . ' The vigil at Central Park, its speakers, and the reading of the Muehlbergers\' letter.',
  $cite($ORDER[4]) . ' The death on November 15, and three wounded besides the two killed.',
  $cite($ORDER[5]) . ' The last of the wounded home on Monday, November 18; students back on campus on Tuesday, November 19, for belongings and counseling; classes to resume December 2.',
  $cite($ORDER[6]) . ' "Saugus High School will remain closed until Dec. 2."',
  $cite($ORDER[7]) . ' E-mailed by the Deputy Superintendent to the district\'s families on January 12, 2020, under three headings: remembrance, recovery and reinforcement.',
];

$BODY = implode("\n\n", [
  'At 7:38 on the morning of Thursday, November 14, 2019, a 16-year-old student took a handgun from his backpack in the quad at Saugus High School, shot five other students and then shot himself.[1][2] It was over in 16 seconds.[3]',
  'Two of the five died that day. Gracie Anne Muehlberger, 15, was a freshman; her parents wrote that she "would always extend a hand to those that needed a friend."[1][2][3] Dominic Blackwell, 14, was an ROTC cadet and the oldest of four brothers.[2][3][4] The student who fired died of his wound the next day, November 15.[5] Three other students were wounded; the last of them came home from the hospital on the evening of November 18.[5][6]',
  'That morning students gathered at Santa Clarita Central Park, where they were released to their parents.[2] On the evening of Sunday, November 17, thousands came to the park for a vigil, #SaugusStrong. The principal, students and both families spoke, and the Muehlbergers\' letter was read.[1][4]',
  'The school stayed closed. Students went back on November 19 only to collect their belongings and meet counselors, and classes were to resume on December 2, after the Thanksgiving break.[6][7]',
  'In January 2020 the William S. Hart Union High School District told its families what it had done since. It had brought in a University of Southern California specialist in school crisis and bereavement, who was counseling parents, teachers and administrators; it had hired more counselors and opened wellness centers across the district sooner than planned; and it had engaged a panel of threat-assessment experts to review its safety plans that spring.[8]',
]);
$SIG = 'Two students were killed and three wounded at Saugus High School. Thousands mourned them at Central Park, and the Hart district expanded its counseling and put its school-safety planning under outside review.';
$ADVISORY = 'This record concerns a school shooting in which students were killed, and describes injuries and a suicide.';
$D2 = 'This record does not name the student who carried out the shooting, and the archive holds no record or photograph of him. Not naming the perpetrator is the settled convention in reporting on these events, and it is what a thoughtful editor would do. The newspaper reports and statements in this archive name him because that is how they were published, and they are kept as published: altering them would falsify the record. The archive does not repeat his name beyond them. (Nathan Imhoff, 5 October 2026.)';
$COUNT = 'The first reports were written on November 14, while the student who fired was still alive, and counted him among the victims: The Signal reported two killed and four wounded, and the Sheriff\'s Department said first responders found six gunshot victims in the quad (document ' . $num('sg20191114-signal-holt') . '). After his death on November 15, the Los Angeles Times gave three wounded besides the two killed (document ' . $num('lat20191116c-firearms-seized') . '), and The Signal counted three dead and three wounded (document ' . $num('sg20191119-signal-murga') . '). This record follows the later count: he shot five students, two of whom died, and three were wounded. The 2019 line of the old site\'s timeline, written from the first reports, says four wounded; it is not changed.';
$pub = array_filter(array_keys($DOC), fn($k) => !$DOC[$k]['lat']); $lat = array_filter(array_keys($DOC), fn($k) => $DOC[$k]['lat']);
$LIST = 'The sixteen texts the old site published on the shooting are each a record in this archive. The Muehlberger family\'s letter, document ' . $num('bryanmuehlberger20191117-letter') . '. The Signal: documents '
  . implode(', ', array_map($num, ['sg20191114-signal-holt', 'sg20191117-signal-alvarenga', 'sg20191119-signal-murga'])) . '. SCVTV and SCVNews.com: documents ' . implode(', ', array_map($num, ['scvtv20191115-press-conference', 'scvtv20191115-fund-peeples']))
  . '. The William S. Hart Union High School District: documents ' . implode(', ', array_map($num, ['hd20200112-district-kuhlman', 'hd20200406-district-ferry'])) . '. The eight Los Angeles Times pieces are held until their rights are settled.';
$NOTESED = [['heading' => '', 'note' => $ADVISORY, 'position' => 'top'],
  ['heading' => 'Why this record does not name the shooter', 'note' => $D2, 'position' => 'bottom'],
  ['heading' => 'The count of the wounded', 'note' => $COUNT, 'position' => 'bottom']
];
/* The sixteen are related through sourceDocuments and listed as SOURCES on the page (Nathan, 6 October 2026: "a reader should see what an event rests on"), so no note lists them. */

/* Relations: each joined by a source (DATA-MODEL, No connection the sources do not make). */
$PLACES = [30515 => 'Santa Clarita Central Park'];
$ORGS = [21777 => 'Saugus High School', 21588 => 'William S. Hart Union High School District', 29282 => 'Los Angeles County Sheriff\'s Department',
  29682 => 'Santa Clarita Valley Sheriff\'s Station', 380 => 'Henry Mayo Newhall Memorial Hospital', 394 => 'The City of Santa Clarita'];
$WHY = [30515 => 'the release of students and the vigil (Holt; Alvarenga)', 21777 => 'where it happened', 21588 => 'the January 2020 message (Kuhlman)',
  29282 => 'the investigation and briefings (Holt; press conference)', 29682 => 'Holt names the station; the press conference was held there', 380 => 'where Gracie Muehlberger died (Reyes-Velarde and Shalby)',
  394 => 'the district drew on the City for crisis counseling (Kuhlman)'];
foreach ($PLACES + $ORGS as $id => $t) { $e = Entry::find()->id($id)->status(null)->one(); if (!$e || $e->title !== $t) { $bad[] = "#$id is not \"$t\""; } }
$ERA = 171; $PERIOD = 184; $SAUGUS = 205;
if (!str_starts_with((string)Category::find()->id($ERA)->one()?->title, 'Contemporary') || Category::find()->id($PERIOD)->one()?->title !== '2010-2019' || Category::find()->id($SAUGUS)->one()?->title !== 'Saugus') { $bad[] = 'era, period or community ids moved'; }

/* The archive's own words: no em dash, no ellipsis, and never the shooter's name. */
$ALL = $BODY . $SIG . implode('', $NOTES) . implode('', array_column($NOTESED, 'note')) . implode('', array_column($NOTESED, 'heading'));
if (preg_match('~\x{2014}~u', $ALL)) { $bad[] = 'an em dash in the archive\'s text'; }
if (preg_match('~\.\.\.|\x{2026}~u', $ALL)) { $bad[] = 'an ellipsis in the archive\'s text'; }
if (preg_match('~Berhow|Nathaniel~i', $ALL)) { $bad[] = 'the shooter\'s name in the archive\'s text'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); $used = array_unique(array_map('intval', $m[1]));
$first = []; foreach ($m[1] as $x) { if (!in_array((int)$x, $first, true)) { $first[] = (int)$x; } }
if ($first !== range(1, count($NOTES))) { $bad[] = 'footnote markers are not numbered in order of first appearance: ' . implode(',', $first); }

$TITLE = 'Saugus High School Shooting'; $KEY = 'saugus-high-school-shooting-2019';
$v = ['body' => $BODY, 'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($NOTES), $NOTES),
  'editorNotes' => $NOTESED, 'eventDate' => 'November 14, 2019', 'eventDateEdtf' => '2019-11-14', 'startEvidence' => 'contemporary', 'eventRecurring' => false,
  'eventSignificance' => $SIG, 'eventPlaces' => array_keys($PLACES), 'eventOrganizations' => array_keys($ORGS), 'eventPersons' => [], 'eventFallenOfficers' => [],
  'historicalEra' => [$ERA], 'historicalPeriod' => [$PERIOD], 'neighborhood' => [$SAUGUS], 'legacyKey' => $KEY,
  'sourceDocuments' => array_values(array_filter(array_merge([$DOC['bryanmuehlberger20191117-letter']['id'] ?? null], array_column(array_diff_key($DOC, ['bryanmuehlberger20191117-letter' => 1]), 'id'))))];
$sec = Craft::$app->getEntries()->getSectionByHandle('events'); $type = $sec->getEntryTypes()[0];
$lay = []; foreach ($type->getFieldLayout()->getCustomFields() as $f) { $lay[$f->handle] = $f; }
foreach (array_keys($v) as $h) { if (!isset($lay[$h])) { $bad[] = "no field $h on the event layout"; } }
foreach ($v as $h => $val) { if (is_string($val) && isset($lay[$h]) && $lay[$h] instanceof \craft\fields\PlainText && $lay[$h]->charLimit && mb_strlen($val) > $lay[$h]->charLimit) { $bad[] = "$h is " . mb_strlen($val) . " characters, over its limit of {$lay[$h]->charLimit}"; } }

$have = Entry::find()->section('events')->status(null)->legacyKey($KEY)->one() ?? Entry::find()->section('events')->status(null)->title($TITLE)->one();
$words = str_word_count($BODY);

/* The report. */
$o = "# The Saugus High School shooting: the event record, dry run\n\nPrinted by `scripts/import/create_saugus_2019_event_2026_10_05.php` on " . date('j F Y') . ". Nothing is written by the dry run.\n\n"
  . '**Status:** ' . ($have ? "exists as #{$have->id}; nothing to do" : 'create, published') . ".\n\n**Refused:** " . ($bad ? "\n\n- " . implode("\n- ", $bad) : 'none') . "\n\n"
  . ($missing ? "The archive numbers below show `#?` until the sources script has run; every number is resolved by legacyKey when this script runs again.\n\n" : '')
  . "## Title\n\n$TITLE\n\n## Date\n\n`eventDate` November 14, 2019; `eventDateEdtf` 2019-11-14; `startEvidence` contemporary.\n\n"
  . "## Editor's note, top\n\n> $ADVISORY\n\n## Significance (`eventSignificance`)\n\n$SIG\n\n## Body ($words words)\n\n$BODY\n\n## Footnotes (source: editorial-2026)\n\n"
  . implode("\n", array_map(fn($i, $t) => ($i + 1) . ". $t", array_keys($NOTES), $NOTES)) . "\n\n## Editor's notes, bottom\n\n"
  . implode("\n\n", array_map(fn($x) => "**{$x['heading']}.** {$x['note']}", array_slice($NOTESED, 1))) . "\n\n## Relations\n\n"
  . "- `eventPlaces`: " . implode('; ', array_map(fn($id) => "#$id {$PLACES[$id]}, {$WHY[$id]}", array_keys($PLACES))) . "\n"
  . "- `eventOrganizations`:\n" . implode("\n", array_map(fn($id) => "  - #$id {$ORGS[$id]}: {$WHY[$id]}", array_keys($ORGS))) . "\n"
  . "- `eventPersons`, `eventFallenOfficers`: none (D2, D3, D4, D9; no officer was killed).\n- `historicalEra` #$ERA Contemporary, `historicalPeriod` #$PERIOD 2010-2019, `neighborhood` #$SAUGUS Saugus.\n"
  . "- `legacyKey` $KEY. `legacyUrl` and `eventLegacyUrl` empty: D12 (where sg20191114shs.htm points) is not ruled.\n- No `featuredImage` (D6).\n\n"
  . "## The source documents\n\nNo field relates an event to a document: documents have no event field, `eventArticles` takes Articles only, `sourceDocuments` is on the Election type only, and `footnotesOn` means the notes were published on another record. The documents are cited in the footnotes (8 of 16) and listed in the bottom note (all 16).\n\n"
  . "| legacyKey | Archive number | Status | Cited | Title |\n|---|---|---|---|---|\n"
  . implode("\n", array_map(fn($k) => "| $k | " . $num($k) . ' | ' . ($DOC[$k]['id'] ? ($DOC[$k]['enabled'] ? 'live' : 'disabled') : 'not yet; ' . ($DOC[$k]['lat'] ? 'will be disabled (D5)' : 'will be live')) . ' | ' . (($i = array_search($k, $ORDER, true)) !== false ? '[' . ($i + 1) . ']' : 'listed only') . ' | ' . $DOC[$k]['title'] . ' |', array_keys($DOC))) . "\n\n"
  . "## The claims checked against the verbatim text\n\n" . implode("\n", array_map(fn($k) => "- $k: " . implode('; ', array_map(fn($p) => '"' . $p . '"', $CHECK[$k])), array_keys($CHECK))) . "\n";
file_put_contents("$root/inventory/review/saugus-high-2019-event-dry-run-2026-10-05.md", $o);
echo ($have ? "#{$have->id} exists, nothing to do" : "create \"$TITLE\"") . "; $words words, " . count($NOTES) . ' footnotes, ' . count($NOTESED) . ' editor notes' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/saugus-high-2019-event-dry-run-2026-10-05.md' . PHP_EOL;
if (!$APPLY) { echo 'nothing written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: nothing written' . PHP_EOL; return; }
if ($have) { echo 'nothing to do: a second run is a no-op' . PHP_EOL; return; }
$e = new Entry(); $e->sectionId = $sec->id; $e->setTypeId($type->id); $e->title = $TITLE; $e->setFieldValues($v);
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('event: ' . json_encode($e->getFirstErrors())); }
$b = Entry::find()->id($e->id)->status(null)->one();
$ok = trim((string)$b->body) === trim($BODY) && $b->eventDateEdtf === '2019-11-14' && count($b->footnotes ?? []) === count($NOTES)
  && $b->eventOrganizations->status(null)->ids() == array_keys($ORGS) && $b->eventPlaces->status(null)->ids() == array_keys($PLACES) && count($b->eventPersons->status(null)->ids()) === 0;
echo 'READ-BACK ' . ($ok ? 'OK: ' . $b->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('create_saugus_2019_event_2026_10_05.php', 1, $ok ? 'verified' : 'SHORT', 'event: the Saugus High School shooting, 14 November 2019');
if (!$ok) { throw new \RuntimeException('create_saugus_2019_event: read-back failed'); }
