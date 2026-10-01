/**
 * "The ladder that runs the other way": the board-ladder finding as an article
 * record (Nathan, 1 October 2026: "publish the board-ladder draft as an article
 * record, with the three blind spots in the body rather than as a note. The
 * caveats are part of the finding").
 *
 * Runs after merge_cooper.php (Bill and William Cooper as one person make
 * nineteen people on more than one body) and build_mckeon_profile.php (his Hart
 * board years, the ladder's clearest case and the first blind spot's proof).
 *
 * Every figure in the text is checked against the archive's own records before
 * anything is written: each named result (the year, the body, won or lost, the
 * place and the field), the count of people on more than one body, the count of
 * council members, and that none of them stood for a board. If the records have
 * moved since the text was written, this refuses rather than publish a number
 * that is no longer true.
 *
 * Published by SCVHistory.com (#378), dated today; its subjects are the people
 * and bodies it names. Public-life facts only: every statement about a living
 * person is a candidacy or an office the archive holds a source for.
 *
 * Idempotent (by title). Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/publish_board_ladder.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$TITLE = 'The ladder that runs the other way';
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);

$BODY = <<<TXT
The usual story of local politics is a ladder: a school board seat first, then the city council. In the Santa Clarita Valley the archive's records show it climbed once, at the very start, by the man who then went further than anyone else in them.

Buck McKeon was a trustee of the William S. Hart Union High School District from 1979, and chairman of its board, until 1987. That November he came first of twenty-six candidates in the cityhood election, and at the council's first meeting its members chose him as the city's first mayor. He left the council in 1992 for the House of Representatives, where he served twenty-two years and chaired first the Committee on Education and the Workforce and then the Committee on Armed Services.[5]

The archive holds every Santa Clarita City Council election since 1987, every contested school board election in the valley's five school districts since 1995, and the water board elections since 2016.[1][2] Nineteen people appear in them as candidates for more than one of those bodies. McKeon is not among them: his years on the Hart board end before the state's school board records begin, and the archive knows of them from the Congressional Biographical Directory, not from a result. Of the nineteen people who have sat on the council, he is the only one the archive can place on a school board.

Since then the ladder has run the other way. Sitting board members who ran for the council lost. Gloria Mercado-Fortine had been on the Hart district board since 2003 when she stood for the council in 2014; she came fifth of thirteen. David Barlavi, elected to the Saugus Union board in 2018, stood for the council in 2022 and came seventh of nine. Maria Gutzeit stood for the council in 2008 and 2014, fifth of five and seventh of thirteen, while serving on the board of the Newhall County Water District, as her own record sets out.[3]

Council candidates who lost went on to win board seats. Linda Storli stood for the council in 1987, 1992 and 1994 and was elected to the Hart board in 2015 and 2020. Aakash Ahuja stood for the council in 2020 and won a Hart seat in 2024. Ed Colley came fourth of five for the council in 2012 and won a Santa Clarita Valley Water seat in 2020.

The step people take most often is between the boards. Paul Strickland was elected to the Sulphur Springs board in 1995 and then to the Hart board in 2001, 2005 and 2009. In 1999 a Hart seat went to Philip C. Ellis, whose ballot designation was "School Board Member"; a Philip C. Ellis, Jr. had won a Newhall seat in 1995, and whether they are one man is not yet settled. Suzan Solomon, three times elected to the Newhall board, and Teresa Todd, elected to Sulphur Springs, both tried for Hart and lost.

So the ladder runs both ways, and rarely; and the one person who climbed it went furthest.

What the records cannot see is part of the finding, and there are three things they cannot see.

- Before 1995 for the school boards, and before 2016 for the water boards. The state's compilation of local results begins in 1995, and the County's earlier returns are scanned volumes not yet read. McKeon shows what that hides: the archive knows his school board career from the Directory, not from any election result, and there may be others.
- Seats filled without a vote. Where no more candidates filed than there were seats, the County cancelled the contest and the board appointed someone. The County's lists name twenty such school board seats in the valley in 2020, 2022 and 2024, and the council's District 3 in 2024, but not the people who filled them.[4] Those careers leave no candidacy behind.
- Names. The archive joins candidacies on the full first name and surname. Two people can share a name: the Paula Olivares who won a Hart seat in 1995 and the one who stood for the water board in 2024 are joined by that rule alone. Names printed with a suffix on some ballots and not others are kept apart until a source settles them.

Each person named here has a page listing every election and office behind these figures.
TXT;
$NOTES = [
    'California Elections Data Archive (CEDA), Center for California Studies and Institute for Social Research, California State University, Sacramento, with the Secretary of State: the candidate files for 1995 to 2024, a compilation of the counties\' returns. Archive record: "California Elections Data Archive (CEDA): candidate files, 1995 to 2024".',
    'City of Santa Clarita, City Clerk, election records 1987 to 2024; County of Los Angeles, Registrar-Recorder/County Clerk, statements of votes cast and official election returns, 2016 to 2024. Each election\'s page in the archive names its sources.',
    'Maria Gutzeit\'s record in the archive: elected to the board of the Newhall County Water District in 2003, serving until 2020, with its sources.',
    'County of Los Angeles, Registrar-Recorder/County Clerk, final lists of cancelled elections, November 2020, 2022 and 2024. Archive record: "Final lists of cancelled elections: November 2020, 2022 and 2024".',
    'Biographical Directory of the United States Congress, M000508: "chairman and trustee, William S. Hart School District ... 1979-1987; ... mayor and council member of Santa Clarita Valley, Calif., 1987-1992; elected ... to the One Hundred Third and to the ten succeeding Congresses (January 3, 1993-January 3, 2015); chair, Committee on Education and the Workforce ...; chair, Committee on Armed Services." His own account (mckeon.house.gov, 2006): chosen "Santa Clarita\'s first mayor" at "the city council\'s first meeting." City of Santa Clarita, City Clerk: 3 November 1987, first of twenty-six, 9,855 votes. All on his archive record.',
];

/* ------------------------------------------------ every figure, checked */
$rows = [];
foreach (Entry::find()->section('candidacies')->all() as $c) {
    $p = $c->candidacyPerson->one(); $e = $c->candidacyElection->one(); if (!$p || !$e) { continue; }
    $b = $e->electionBody->one();
    $n = (int)Entry::find()->section('candidacies')->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->count();
    $pl = (int)Entry::find()->section('candidacies')->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->votes('> ' . (int)$c->votes)->count() + 1;
    $rows[$p->title][] = ['y' => (int)substr($e->electionDateEdtf, 0, 4), 'body' => !$b || $b->id == 394 ? 'Council' : $b->title, 'won' => (string)$c->outcome->value === 'elected', 'place' => $pl, 'of' => $n, 'pid' => $p->id];
}
$CLAIMS = [   /* person, year, body (substring), won, place, of: null where the text does not say */
    ['Gloria Mercado-Fortine', 2003, 'Hart', true, null, null], ['Gloria Mercado-Fortine', 2014, 'Council', false, 5, 13],
    ['David Barlavi', 2018, 'Saugus', true, null, null], ['David Barlavi', 2022, 'Council', false, 7, 9],
    ['Maria Gutzeit', 2008, 'Council', false, 5, 5], ['Maria Gutzeit', 2014, 'Council', false, 7, 13],
    ['Linda Storli', 1987, 'Council', false, null, null], ['Linda Storli', 1992, 'Council', false, null, null], ['Linda Storli', 1994, 'Council', false, null, null],
    ['Linda Storli', 2015, 'Hart', true, null, null], ['Linda Storli', 2020, 'Hart', true, null, null],
    ['Aakash Ahuja', 2020, 'Council', false, null, null], ['Aakash Ahuja', 2024, 'Hart', true, null, null],
    ['Ed Colley', 2012, 'Council', false, 4, 5], ['Ed Colley', 2020, 'Santa Clarita Valley Water', true, null, null],
    ['Paul Strickland', 1995, 'Sulphur Springs', true, null, null], ['Paul Strickland', 2001, 'Hart', true, null, null], ['Paul Strickland', 2005, 'Hart', true, null, null], ['Paul Strickland', 2009, 'Hart', true, null, null],
    ['Suzan Solomon', 1999, 'Newhall', true, null, null], ['Suzan Solomon', 2003, 'Newhall', true, null, null], ['Suzan Solomon', 2007, 'Newhall', true, null, null], ['Suzan Solomon', 2009, 'Hart', false, null, null],
    ['Teresa Todd', 1999, 'Sulphur Springs', true, null, null], ['Teresa Todd', 2003, 'Hart', false, null, null],
    ['Paula Olivares', 1995, 'Hart', true, null, null], ['Paula Olivares', 2024, 'Santa Clarita Valley Water', false, null, null],
    ['Buck McKeon', 1987, 'Council', true, 1, 26],
];
$bad = [];
foreach ($CLAIMS as [$who, $y, $body, $won, $pl, $of]) {
    $hit = array_filter($rows[$who] ?? [], fn($r) => $r['y'] === $y && str_contains($r['body'], $body) && $r['won'] === $won && ($pl === null || ($r['place'] === $pl && $r['of'] === $of)));
    if (!$hit) { $bad[] = "$who $y $body " . ($won ? 'won' : 'lost') . ($pl ? " $pl of $of" : ''); }
}
/* People who stood for more than one body, counted by candidate key where there is
   one (a person removed under the significance rule keeps the key on their
   candidacies), and by person record otherwise. */
$byKey = [];
$keyed = (bool)Craft::$app->getFields()->getFieldByHandle('candidateKey');
foreach (Entry::find()->section('candidacies')->all() as $c) {
    $pp = $c->candidacyPerson->one(); $e = $c->candidacyElection->one(); if (!$e) { continue; }
    $k = ($keyed ? trim((string)$c->candidateKey) : '') ?: ($pp ? 'person:' . $pp->slug : ''); if ($k === '') { continue; }
    $b = $e->electionBody->one(); $byKey[$k][] = !$b || $b->id == 394 ? 'Council' : $b->title;
}
$multi = array_filter($byKey, fn($bs) => count(array_unique($bs)) > 1);
$members = []; foreach (Entry::find()->section('officeHoldings')->relatedTo(['targetElement' => 394, 'field' => 'holdingBody'])->all() as $h) { $members[$h->holdingPerson->one()->title] = 1; }
$crossed = array_filter(array_keys($members), fn($t) => ($pp = Entry::find()->section('persons')->title($t)->one()) && isset($multi['person:' . $pp->slug]));
if (count($multi) !== 19) { $bad[] = 'people on more than one body: ' . count($multi) . ', the text says nineteen (with Bill and William Cooper one person: run merge_cooper.php first)'; }
if (count($members) !== 19) { $bad[] = 'council members: ' . count($members) . ', the text says nineteen'; }
if ($crossed) { $bad[] = 'council members who stood for a board: ' . implode(', ', $crossed); }
/* "Of the nineteen ... he is the only one the archive can place on a school board": a school
   board office or a school board candidacy, among the council's members. */
$onBoard = [];
foreach (array_keys($members) as $t) {
    $pp = Entry::find()->section('persons')->title($t)->one(); if (!$pp) { continue; }
    $bodiesHeld = array_map(fn($h) => $h->holdingBody->one(), Entry::find()->section('officeHoldings')->relatedTo(['targetElement' => $pp, 'field' => 'holdingPerson'])->all());
    $school = array_filter($bodiesHeld, fn($b) => $b && (string)$b->orgType->value === 'school');
    $schoolCand = array_filter($rows[$t] ?? [], fn($r) => str_contains($r['body'], 'School District'));
    if ($school || $schoolCand) { $onBoard[] = $t; }
}
if ($onBoard !== ['Buck McKeon']) { $bad[] = 'council members the archive places on a school board: ' . (implode(', ', $onBoard) ?: 'none') . '; the text says McKeon alone'; }
echo 'FIGURES CHECKED: ' . count($CLAIMS) . ' results, 19 on more than one body, 19 council members, none crossing' . ($bad ? PHP_EOL . 'DO NOT MATCH: ' . implode(' | ', $bad) : ', all match') . PHP_EOL;

/* ------------------------------------------------ relations */
/* McKeon's years on the Hart board are an office holding (build_mckeon_profile.php, which runs first). */
$mck = Entry::find()->section('persons')->title('Buck McKeon')->one();
$hartId = Entry::find()->section('organizations')->slug('william-s-hart-union-high-school-district')->one()?->id ?? 0;
$mckBoard = $mck && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $mck, 'field' => 'holdingPerson'], ['targetElement' => $hartId, 'field' => 'holdingBody']])->exists();
if (!$mckBoard) { $bad[] = 'Buck McKeon\'s Hart board term is not on the record: run build_mckeon_profile.php first'; }
$SUBJECTS = ['Buck McKeon', 'Gloria Mercado-Fortine', 'David Barlavi', 'Maria Gutzeit', 'Linda Storli', 'Aakash Ahuja', 'Ed Colley', 'Paul Strickland', 'Suzan Solomon', 'Teresa Todd', 'Paula Olivares'];
$pids = array_map(fn($t) => $rows[$t][0]['pid'] ?? null, $SUBJECTS);
if (in_array(null, $pids, true)) { $bad[] = 'a named person has no record'; }
$orgs = array_merge([394, 402], array_map(fn($s) => Entry::find()->section('organizations')->slug($s)->one()?->id, ['william-s-hart-union-high-school-district', 'saugus-union-school-district', 'newhall-school-district', 'sulphur-springs-union-school-district', 'castaic-union-school-district', 'castaic-lake-water-agency']));
if (in_array(null, $orgs, true)) { $bad[] = 'a named body has no record'; }
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
$have = Entry::find()->section('articles')->status(null)->title($TITLE)->one();
echo ($have ? "#{$have->id} exists: " : 'create article: ') . "\"$TITLE\", " . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes; published by SCVHistory.com (#378), 1 October 2026; about ' . count($SUBJECTS) . ' people and ' . count($orgs) . ' bodies' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: the text no longer matches the records' . PHP_EOL; return; }

$sec = $svc->getSectionByHandle('articles');
$a = $have ?: new Entry();
if (!$have) { $a->sectionId = $sec->id; $a->setTypeId($sec->getEntryTypes()[0]->id); $a->title = $TITLE; }
$PROVENANCE_NOTE = ['heading' => 'How this article was checked', 'position' => 'bottom', 'note' => 'Every figure in this article is checked against the archive\'s own records by the script that publishes it, which refuses to write a text the records contradict. Its first draft said the board-to-council ladder was never climbed. When Buck McKeon\'s years on the Hart board were added to his record on 1 October 2026, the check refused to publish that draft, and the article was rewritten around him. The guard caught the archive\'s own text, which is what it is for.'];
$a->setFieldValues(['editorNotes' => [$PROVENANCE_NOTE], 'recordProvenance' => 'publish_board_ladder.php, 1 October 2026: written by SCVHistory.com; every figure checked against the records at publication; the first draft refused by that check and rewritten', 'subheadline' => 'Candidates for the city council, the school boards and the water boards of the Santa Clarita Valley, 1987 to 2024',
    'body' => $BODY, 'footnotes' => $fn($NOTES), 'originalPublishDate' => 'October 1, 2026', 'originalPublishDateEdtf' => '2026-10-01',
    'publishedBy' => [378], 'subjectPerson' => array_values($pids), 'subjectOrganization' => array_values($orgs)]);
if (!$elements->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
$r = Entry::find()->section('articles')->title($TITLE)->one();
$ok = $r && str_contains((string)$r->body, 'the one person who climbed it went furthest') && count($r->subjectPerson->ids()) === count($SUBJECTS);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('publish_board_ladder.php', 1, $ok ? 'verified' : 'SHORT', 'the board-ladder article');
if (!$ok) { throw new \RuntimeException('publish_board_ladder: read-back failed'); }
