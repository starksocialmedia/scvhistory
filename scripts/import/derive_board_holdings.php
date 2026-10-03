/**
 * An office holding for every school board and water board win (Nathan, 3 October
 * 2026: "as you did for the council"), on the 16 decisions he accepted as
 * recommended the same day (inventory/review/board-terms-decisions-2026-10-03.md),
 * with his notes: bridging terms (#5) and next-election ends (#7) are "derived" at
 * both ends, so a reader can tell inference from record; the first-win incumbents
 * go on a research list (#9); Hart Area 2 in 2022 from the County (#15, updated).
 *
 * THE BODIES. Hart, Saugus Union, Newhall, Sulphur Springs and Castaic Union (the
 * elections and candidacies import_ceda_school_boards.php made from CEDA), and
 * Castaic Lake Water Agency (2016) and Santa Clarita Valley Water (2020 to 2024)
 * from import_water_boards.php. The data is read from those records, not from
 * files: every win is a candidacy marked elected.
 *
 * THE RULES, by decision number
 *  #1  School terms run from December of the election year to December of the
 *      next election for the seat, month precision. Education Code 5017: the term
 *      begins on the second Friday in December after the election (the first
 *      Friday until AB 2449, effective 2019).
 *  #2  Water terms run from January after the November election (SCV Water's own
 *      list of directors). CLWA's 2016 winners: January 2017 to 31 December 2017,
 *      "left": the agency was merged into SCV Water on 1 January 2018.
 *  #2b Plambeck's SCV Water holding (#28219) ends January 2023, not December 2022.
 *  #3  The move from odd to even years. Hart held 2015 and moved after it: a term
 *      due in 2017 ran to 2018, in 2019 to 2020. Saugus skipped 2015: due 2015 ran
 *      to 2016, 2017 to 2018. Newhall, Sulphur Springs, Castaic: due 2017 ran to
 *      2018; due 2015 ran to 2015 or 2016, written "2016-12?" with howEnded
 *      "unknown" and a footnote naming both.
 *  #4  holdingDistrict and seatLabel only when the winning contest names the
 *      trustee area or division. At-large terms carry neither.
 *  #5  Bridging: when nobody contested the seat at the next election and the same
 *      person later stands as the incumbent, the missing term(s) are created,
 *      selectionMethod "appointed" (in lieu of election, Elections Code 10515),
 *      start and end "derived". Also bridged by a board page: Watson, Saugus
 *      Area 4, 2024 (the County's 2024 list has that seat filled without a vote).
 *  #6  "appointed" for a seat filled without a vote; the footnote says in lieu.
 *  #7  No recorded next election and no later appearance: the term ends at the
 *      next election for the seat ("derived" at both ends), howEnded "unknown".
 *  #8  A term inside an existing holding for the same person and body is skipped;
 *      tenure holdings are kept, not split.
 *  #9  A first win as the incumbent gets a footnote; those not covered by an
 *      existing holding are printed as a research list. No holding is made.
 *  #10 Webb (Hart Area 4, 2020), Love (Saugus Area 1, 2022), Atkins (SCV Water
 *      Division 3, 2020): end blank, howEnded "unknown", the successor in a
 *      footnote. Petersen's appointment (September 2022 to January 2025) from the
 *      agency's list, "roster", without asserting whose seat it was.
 *  #11 The four CLWA 2016 winners each also get a "succeeded" SCV Water term,
 *      1 January 2018 to January 2023 (SB 634 extended terms due in 2020 by two
 *      years). NCWD is not touched. The other founding directors wait.
 *  #12 startEvidence copies the candidacy's outcomeEvidence; endEvidence is
 *      "derived", or "roster" where Hart's page or SCV Water's list gives that end.
 *  #13 howEnded: reelected (the same person takes the seat next, contested or
 *      bridged), expired (lost, or did not stand), serving (the term runs past
 *      today), unknown otherwise.
 *  #14 A person record for every winner without one: public facts only (title,
 *      full name, printings as aliases, role, historical era). The officeHolding
 *      is the evidence (DATA-MODEL, inclusion of officeholders). Four printings
 *      decided as one person each: Robert N. Jensen, Jr. and Bob Jensen (the
 *      County prints him "BOB JENSEN JR" in 2022); Philip C. Ellis and Philip C.
 *      Ellis, Jr.; J. Micheal McGrath and John Michael McGrath; Denis F. De
 *      Figueiredo and Denis F. DeFigueiredo. personOrganizations is not touched.
 *      Eras by the assign_person_eras.php rule: the era holding two thirds of the
 *      years in office and races; otherwise left for that pass.
 *  #15 Hart Area 2, 8 November 2022: contested, and CEDA omits it. The County's
 *      Statement of Votes Cast (contest WM S. HART UNION HIGH-TR 2) gives BOB
 *      JENSEN JR 11,638 and ANDREW TABAN 5,736. His 2022 holding is "elected",
 *      startEvidence "certified". No election or candidacy record is made here:
 *      that is a follow-up for the water/school import.
 *  #16 College of the Canyons: not in scope.
 *
 * HART FROM THE ROSTER (Nathan, 3 October 2026: "Hold Hart ... then rebuild Hart from the
 * roster ... Leon's own record beats our inference"). Leon Worden's term-by-term roster of the
 * Hart board, 1945 to the board of 12-11-2013 to 11-30-2014 (hartschoolboardmembers.htm,
 * extracted verbatim to inventory/legacy/hart-board-roster.json by extract_hart_roster.py), is
 * read as a second, independent record beside CEDA, on the council ledger's pattern:
 *  - a CEDA term the roster agrees with (start, end and how it ended) is "roster" at both
 *    ends, with a footnote quoting the rows; an end the roster does not reach stays "derived";
 *  - roster service CEDA lacks (pre-1995 terms, appointments, the 2013 seats filled without a
 *    vote) becomes a holding, "roster", only for a person who has or will have a record
 *    (an existing person tied to Hart, or a 1995+ winner); nobody is created from the roster;
 *  - where the two disagree, nothing is resolved: both are footnoted and the case is printed;
 *  - #8 still holds: terms inside an existing tenure are skipped, and roster against tenure
 *    conflicts are printed.
 *
 * Idempotent: a holding for the same person, body and termStartEdtf is skipped;
 * people by title. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/derive_board_holdings.php'))"
 * Hart only: ddev craft exec '$ONLY_BODIES = [21588]; eval(file_get_contents("scripts/import/derive_board_holdings.php"));'
 */

use craft\elements\{Entry, Category};

$APPLY = false;
/* $HOLD_BODIES are never written (Hart held while the six others were applied, 3 October 2026).
   $ONLY_BODIES, when given, writes only those bodies and releases them from the hold. */
$HOLD_BODIES = [21588];
$ONLY_BODIES = $ONLY_BODIES ?? [];
if ($ONLY_BODIES) { $HOLD_BODIES = array_values(array_diff($HOLD_BODIES, $ONLY_BODIES)); }
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$bad = [];
$PROV = 'derive_board_holdings.php, 3 October 2026';
$NOW = date('Y-m');

/* ------------------------------------------------ bodies, offices, sources */
$SCHOOL = [21588 => 'Hart', 21592 => 'Saugus Union', 21590 => 'Newhall', 21594 => 'Sulphur Springs', 21596 => 'Castaic Union'];
$CLWA = 26563; $SCVW = 402;
$WATER = [$CLWA => 'CLWA', $SCVW => 'SCV Water'];
$ALLB = $SCHOOL + $WATER;
$RS = 26964; $RW = 27420;
foreach ([21588 => 'William S. Hart Union High School District', 21592 => 'Saugus Union School District', 21590 => 'Newhall School District', 21594 => 'Sulphur Springs Union School District',
    21596 => 'Castaic Union School District', 26563 => 'Castaic Lake Water Agency', 402 => 'Santa Clarita Valley Water', 26964 => 'School Board Member', 27420 => 'Water Board Director'] as $id => $t) {
    if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; }
}
$bodyTitle = fn($id) => $get($id)->title;
$hartPage = json_decode(@file_get_contents("$root/inventory/elections/hart-board-2026-10-01.json"), true);
$saugusPage = json_decode(@file_get_contents("$root/inventory/elections/saugus-board-2026-10-03.json"), true);
$scvList = json_decode(@file_get_contents("$root/inventory/elections/scv-water-directors-2026-09-30.json"), true);
if (!$hartPage || !$saugusPage || !$scvList) { $bad[] = 'a board page or the SCV Water list is missing'; }
$svcMan = json_decode(@file_get_contents("$root/inventory/elections/county/svc-manifest.json"), true);
$HART22 = 'WM_S._HART_UNION_HIGH-TR_2_11-08-22_by_Precinct_PUBLIC_4300-8494.xls';
if (!isset($svcMan['contests'][$HART22]) || !is_file("$root/inventory/elections/county/svc/$HART22") || hash_file('sha256', "$root/inventory/elections/county/svc/$HART22") !== $svcMan['contests'][$HART22]['sha256']) { $bad[] = 'the Hart Area 2 2022 spreadsheet is missing or differs from the manifest'; }

$C_HART = 'William S. Hart Union High School District, "Governing Board Member Info", https://www.hartdistrict.org/governing-board, read 1 October 2026';
$C_SAUGUS = 'Saugus Union School District, "Governing Board", https://www.saugususd.org/governing-board, read 3 October 2026';
$C_SCVL = 'Santa Clarita Valley Water Agency, "Board of Directors", https://www.yourscvwater.com/governance/board-directors, read 30 September 2026';
$C_5017 = 'California Education Code, section 5017: a board member elected at a regular election holds office "for a term of four years commencing on the second Friday in December next succeeding his or her election" (the first Friday until AB 2449 took effect in 2019).';
$C_10515 = 'Elections Code, section 10515: where no more candidates file than there are seats, the Board of Supervisors appoints them, and they serve exactly as if elected. The County calls this appointment in lieu of election.';
$C_SB634 = 'SB 634 (2017) seated the sitting elected directors of the Castaic Lake Water Agency and the Newhall County Water District as the first board of the Santa Clarita Valley Water Agency on January 1, 2018, and "Extends, by two years, the terms for the initial Board members whose terms expire in 2018 and 2020" (Assembly Committee on Local Government, analysis of SB 634, https://alcl.assembly.ca.gov/sites/alcl.assembly.ca.gov/files/SB%20634%20analysis.pdf).';
$C_HART22 = 'County of Los Angeles, Registrar-Recorder/County Clerk, Statement of Votes Cast, General Election, November 8, 2022: Wm. S. Hart Union High, Trustee Area 2 (' . ($svcMan['zips']['4300']['url'] ?? '') . '): Bob Jensen Jr 11,638, Andrew Taban 5,736, of 21,103 ballots cast in the area. The California Elections Data Archive (CEDA) omits this contest.';
$CANCEL = [   /* the County's final lists of cancelled elections, as read 29 September and 3 October 2026 */
    2018 => ['url' => 'https://www.lavote.gov/docs/rrcc/Election-Info/11082018_final-list-cancelled-elections.pdf', 'date' => 'November 6, 2018',
        'b' => [21596 => ['B', 'D', 'E', 'C'], 21590 => ['1', '3', '4'], 21594 => ['3', '4', '5'], 21588 => ['3']]],
    2020 => ['url' => 'https://www.lavote.gov/docs/rrcc/election-info/11032020_cancelled-elections.pdf', 'date' => 'November 3, 2020',
        'b' => [21596 => ['A', 'C'], 21590 => ['4', '5'], 21594 => ['1', '2', '3']]],
    2022 => ['url' => 'https://www.lavote.gov/docs/rrcc/election-info/11082022_final-list-of-cancelled-elections.pdf', 'date' => 'November 8, 2022',
        'b' => [21596 => ['B', 'D'], 21590 => ['3'], 21594 => ['3', '4', '5']]],
    2024 => ['url' => 'https://content.lavote.gov/docs/rrcc/documents/cancelled-elections-november-2024.pdf', 'date' => 'November 5, 2024',
        'b' => [21596 => ['C', 'D'], 21590 => ['4'], 21592 => ['4', '1'], 21594 => ['1', '2']]],
];
$cancelCite = fn(int $y) => 'County of Los Angeles, Registrar-Recorder/County Clerk, "General Election ' . $CANCEL[$y]['date'] . ', Final List of Cancelled Elections: Appointment in Lieu of Election Due to Insufficiency of Candidates", ' . $CANCEL[$y]['url'];

/* ------------------------------------------------ names */
$norm = fn($s) => strtolower(trim(preg_replace('~\s+~', ' ', preg_replace('~[^A-Za-z -]~', '', \craft\helpers\StringHelper::toAscii((string)$s)))));
$keysOf = function (string $name) use ($norm): array {
    /* Only "Jr" is set aside: the merges Nathan decided (Jensen, Ellis) differ by it. Sr, II, III and IV keep a
       name apart, since they can mark a father and a son (Lester M. Freeman 1995, Lester M. Freeman, III 1999). */
    $name = preg_replace(['~\.(?=[A-Za-z]{2})~', '~,?\s*\b(Jr|P\.E)\b\.?~i'], ['. ', ''], $name);
    $nicks = []; if (preg_match_all('~[“"]([^”"]+)[”"]~u', $name, $m)) { $nicks = $m[1]; }
    $plain = preg_replace(['~[“"][^”"]+[”"]~u', '~\([^)]*\)~'], ' ', $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($plain)), fn($w) => $w !== ''));
    if (count($t) < 2) { return []; }
    /* particles join the surname: "De Figueiredo", "De La Cerda" */
    $li = count($t) - 1; while ($li > 1 && preg_match('~^(de|la|del|van|von)$~i', $t[$li - 1])) { $li--; }
    $last = str_replace(' ', '', $norm(implode(' ', array_slice($t, $li))));
    $firsts = array_values(array_filter(array_slice($t, 0, $li), fn($w) => !preg_match('~^[A-Za-z]\.?$~', $w)));
    if (!$firsts) { return [$norm(implode(' ', $t))]; }   /* "E G GLADBACH": initials only */
    $keys = [$norm(rtrim($firsts[0], '.')) . ' ' . $last];
    foreach ($nicks as $n) { $keys[] = $norm($n) . ' ' . $last; }
    return array_unique($keys);
};
/* #14: the four printings decided as one person each. */
$MERGE = ['robert jensen' => 'bob jensen', 'micheal mcgrath' => 'john mcgrath'];
$canon = fn(string $k) => $MERGE[$k] ?? $k;
$TITLE = ['bob jensen' => 'Bob Jensen', 'philip ellis' => 'Philip Ellis Jr.', 'john mcgrath' => 'John Michael McGrath', 'denis defigueiredo' => 'Denis DeFigueiredo',
    'bj atkins' => 'BJ Atkins', 'rj kelly' => 'RJ Kelly', 'e g gladbach' => 'E. G. Gladbach'];
$tc = fn($s) => preg_replace_callback("~\\b(Mc|O')([a-z])~", fn($m) => $m[1] . strtoupper($m[2]), ucwords(strtolower($s), " -'"));
$titleOf = function (array $printings) {
    $mixed = array_values(array_filter($printings, fn($n) => $n !== strtoupper($n)));
    $use = $mixed ?: $printings;
    usort($use, fn($a, $b) => strlen($a) <=> strlen($b));
    $t = preg_replace(['~[“"][^”"]+[”"]~u', '~,?\s*\b(Jr|Sr|II|III|IV)\b\.?~', '~(?<=\w)\.(?=\s)~'], [' ', '', ''], $use[0]);
    $w = array_values(array_filter(preg_split('~\s+~', trim($t)), fn($x) => $x !== '' && !preg_match('~^[A-Za-z]\.?$~', $x)));
    return implode(' ', $w);
};
$pIndex = []; $allPeople = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) {
    $names = [$p->title, (string)$p->fullName]; foreach (preg_split('~\n~', (string)$p->personAliases) as $a) { if (trim($a)) { $names[] = trim($a); } }
    $allPeople[$p->id] = ['title' => $p->title, 'names' => array_values(array_unique(array_filter($names))), 'prov' => (string)$p->recordProvenance];
    foreach ($names as $n) { foreach ($keysOf($n) as $k) { $pIndex[$canon($k)][$p->id] = $p->title; } }
}

/* ------------------------------------------------ contests */
$printed = fn($d) => date('F j, Y', strtotime($d));
$areaOf = fn($place) => $place && preg_match('~(?:Trustee Area|Division) (\w+)$~', $place->title, $m) ? $m[1] : '';
$contests = [];   /* id => [...] */
foreach (Entry::find()->section('elections')->status(null)->relatedTo(['targetElement' => array_keys($ALLB), 'field' => 'electionBody'])->all() as $e) {
    $b = $e->electionBody->status(null)->ids()[0]; $pl = $e->electionDistrict->status(null)->one();
    $cs = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->all();
    usort($cs, fn($a, $b) => ($b->votes ?? 0) <=> ($a->votes ?? 0));
    $cands = [];
    foreach ($cs as $c) {
        $fnote = implode(' ', array_map(fn($r) => (string)($r['note'] ?? ''), $c->footnotes ?? []));
        $cands[] = ['id' => $c->id, 'name' => (string)$c->nameAsPrinted, 'votes' => (int)$c->votes, 'won' => (string)$c->outcome->value === 'elected', 'ev' => (string)$c->outcomeEvidence->value,
            'pid' => $c->candidacyPerson->status(null)->ids()[0] ?? null, 'inc' => str_contains($fnote, 'the incumbent'), 'row' => preg_match('~CEDA \d{4}, row (\d+)~', $fnote, $m) ? (int)$m[1] : null];
    }
    $slug = (string)$e->slug;
    $contests[$e->id] = ['id' => $e->id, 'b' => $b, 'date' => (string)$e->electionDateEdtf, 'y' => (int)substr((string)$e->electionDateEdtf, 0, 4), 'area' => $areaOf($pl), 'place' => $pl?->id,
        'seats' => (int)$e->seatsUp, 'part' => str_contains($slug, 'short-term') ? 'short' : (str_contains($slug, 'two-year') ? 'two-year' : 'full'),
        'doc' => $e->sourceDocuments->status(null)->one()?->title, 'cands' => $cands, 'county' => false];
}
/* #15: the Hart Area 2 contest of 2022, from the County's statement; no record is made for it. */
$hartA2 = Entry::find()->section('places')->status(null)->title('William S. Hart Union High School District, Trustee Area 2')->one();
$contests['hart22a2'] = ['id' => 'hart22a2', 'b' => 21588, 'date' => '2022-11-08', 'y' => 2022, 'area' => '2', 'place' => $hartA2?->id, 'seats' => 1, 'part' => 'full', 'doc' => null, 'county' => true,
    'cands' => [['id' => null, 'name' => 'BOB JENSEN JR', 'votes' => 11638, 'won' => true, 'ev' => 'certified', 'pid' => null, 'inc' => true, 'row' => null],
                ['id' => null, 'name' => 'ANDREW TABAN', 'votes' => 5736, 'won' => false, 'ev' => 'certified', 'pid' => null, 'inc' => false, 'row' => null]]];
$nWins = array_sum(array_map(fn($c) => count(array_filter($c['cands'], fn($x) => $x['won'])), $contests));
if (count($contests) !== 77 || $nWins !== 133) { $bad[] = 'expected 76 board elections and 132 wins, plus the 2022 Hart Area 2 contest; found ' . (count($contests) - 1) . ' and ' . ($nWins - 1); }

/* ------------------------------------------------ who: existing people, and the new */
$keyOfCand = function ($x) use ($keysOf, $canon) { $k = $keysOf($x['name']); return $k ? $canon($k[0]) : null; };
$newP = [];   /* key => [printings, bodies, cands] */
foreach ($contests as $cid => &$c) { foreach ($c['cands'] as &$x) {
    if ($x['pid']) { $x['who'] = (int)$x['pid']; continue; }
    $k = $keyOfCand($x); $x['key'] = $k;
    /* The County's 2022 Hart Area 2 contest has no candidacy record. Its winner is the Bob Jensen record once it
       exists (created by the first run), and a person to create before that, so a rerun stays idempotent. */
    if ($k === 'bob jensen' && $c['county']) { $jx = Entry::find()->section('persons')->status(null)->title('Bob Jensen')->one(); $x['who'] = $jx ? $jx->id : "new:$k"; if ($jx) { $x['pid'] = $jx->id; } continue; }
    if ($x['won'] && $k) { $newP[$k] ??= ['printings' => [], 'bodies' => [], 'wins' => [], 'cands' => []]; }
} unset($x); } unset($c);
/* Any candidacy, won or lost, on these boards that is a new person's. */
$links = [];
foreach ($contests as $cid => &$c) { foreach ($c['cands'] as &$x) {
    if ($x['pid'] || !($k = $x['key'] ?? null) || !isset($newP[$k])) { continue; }
    $x['who'] = "new:$k";
    $newP[$k]['printings'][] = $x['name']; $newP[$k]['bodies'][$c['b']] = true;
    $newP[$k]['cands'][] = $c['y'] . ' ' . $ALLB[$c['b']] . ($c['area'] !== '' ? ' ' . ($c['b'] === $SCVW || $c['b'] === $CLWA ? 'Div ' : 'Area ') . $c['area'] : '') . ': "' . $x['name'] . '" ' . ($x['won'] ? 'won' : 'lost') . ($c['county'] ? ' (County statement)' : '');
    if ($x['id']) { $links[] = [$x['id'], $k, $c['b']]; }
} unset($x); } unset($c);
/* People this script created on 3 October for another body (Bob Jensen, Philip Ellis Jr. at Newhall): their Hart
   candidacies were held, so they still read as new. Adopt the record; the held candidacies are linked to it. */
$adopted = [];
foreach (array_keys($newP) as $k) {
    $m = array_keys($pIndex[$k] ?? []);
    if (count($m) === 1 && str_starts_with($allPeople[$m[0]]['prov'], $PROV)) { $adopted[$k] = $m[0]; }
}
foreach ($contests as &$c) { foreach ($c['cands'] as &$x) { if (is_string($x['who'] ?? null) && isset($adopted[substr($x['who'], 4)])) { $x['who'] = $adopted[substr($x['who'], 4)]; } } unset($x); } unset($c);
$links = array_map(fn($l) => isset($adopted[$l[1]]) ? [$l[0], $l[1], $l[2], $adopted[$l[1]]] : $l, $links);
foreach ($adopted as $k => $id) { unset($newP[$k]); }
foreach ($newP as $k => &$np) {
    if (isset($pIndex[$k])) { $bad[] = "new person $k matches an existing person: " . implode(', ', $pIndex[$k]); }
    $np['title'] = $TITLE[$k] ?? $titleOf(array_values(array_unique($np['printings'])));
    if ($np['title'] === strtoupper($np['title'])) { $np['title'] = $tc($np['title']); }
    $al = array_map(fn($n) => $n === strtoupper($n) ? $tc($n) : $n, array_values(array_unique($np['printings'])));
    foreach (($scvList['directors'] ?? []) as $d) { if (in_array($k, array_map($canon, $keysOf($d['name'])), true)) { $al[] = preg_replace('~,\s*P\.E\.$~', '', $d['name']); } }
    /* An alias that differs from the title only in case or punctuation ("Bj Atkins", "E G Gladbach") is not a name anyone used. */
    $np['aliases'] = array_values(array_unique(array_filter($al, fn($a) => $norm($a) !== $norm($np['title']) && str_replace(' ', '', $norm($a)) !== str_replace(' ', '', $norm($np['title'])) && str_contains($a, ' '))));
    /* Nathan, 3 October 2026: "Yes, add 'William Pecsi' as an alias" (the CLWA release of January 10, 2017 names him so). */
    if ($np['title'] === 'Bill Pecsi') { $np['aliases'][] = 'William Pecsi'; }
} unset($np);
$whoName = fn($w) => is_string($w) ? $newP[substr($w, 4)]['title'] : $get($w)->title;

/* ------------------------------------------------ existing holdings (#8) */
$existing = [];
foreach (Entry::find()->section('officeHoldings')->status(null)->all() as $h) {
    $existing[] = ['id' => $h->id, 'pid' => $h->holdingPerson->status(null)->ids()[0] ?? null, 'b' => $h->holdingBody->status(null)->ids()[0] ?? null,
        's' => (string)$h->termStartEdtf, 'e' => (string)$h->termEndEdtf, 'h' => $h];
}
$yr = fn($s) => (int)substr((string)$s, 0, 4);
$coveredBy = function ($who, int $b, string $s) use ($existing, $yr) {
    if (!is_int($who)) { return null; }
    foreach ($existing as $x) {
        if ($x['pid'] != $who || $x['b'] != $b) { continue; }
        if ($x['s'] === $s) { return $x['id']; }
        if ($yr($x['s']) <= $yr($s) && ($x['e'] === '' || $yr($s) < $yr($x['e']))) { return $x['id']; }
    }
    return null;
};

/* ------------------------------------------------ the seat model (#1, #3) */
$TRANS = [21588 => [2017 => 2018, 2019 => 2020], 21592 => [2015 => 2016, 2017 => 2018]];
$nextYear = function (int $b, int $y, string $part) use ($TRANS, $SCHOOL) {
    $n = $y + (in_array($part, ['short', 'two-year'], true) ? 2 : 4);
    if (!isset($SCHOOL[$b])) { return $n; }
    $t = $TRANS[$b] ?? [2015 => '2015|2016', 2017 => 2018];
    return $t[$n] ?? $n;
};
$isWater = fn($b) => isset($WATER[$b]);
$startOf = fn(int $b, int $y) => $isWater($b) ? ['January ' . ($y + 1), ($y + 1) . '-01'] : ['December ' . $y, $y . '-12'];
$endOf = fn(int $b, $n) => $isWater($b) ? ['January ' . ($n + 1), ($n + 1) . '-01'] : ['December ' . $n, $n . '-12'];
$past = fn(string $edtf) => substr($edtf, 0, 7) <= $NOW;
$byBodyYear = []; foreach ($contests as $c) { $byBodyYear[$c['b']][$c['y']][] = $c; }
$ord = fn($i) => ['first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth'][$i] ?? ($i + 1) . 'th';
$seatWord = fn($c) => ($c['area'] === '' ? ($c['seats'] === 1 ? 'the seat' : $c['seats'] . ' seats') . ' at large' : ($c['seats'] > 1 ? $c['seats'] . ' seats in ' : '') . ($isWater($c['b']) ? 'Division ' : 'Trustee Area ') . $c['area']);
$citeOf = function ($c, $x) {
    if ($c['county']) { return null; }
    if ($x['row']) { $y = $c['y']; return 'California Elections Data Archive (CEDA), CEDA' . $y . 'Data.xls' . ($y >= 2011 ? 'x' : '') . ', row ' . $x['row'] . ', a compilation of the County\'s returns'; }
    return 'County of Los Angeles, Registrar-Recorder/County Clerk, ' . $c['doc'];
};
/* The wins of each person on each body, for bridging and "stood again". */
$standings = [];   /* who|b => [ [c, x] ... ] */
foreach ($contests as $c) { foreach ($c['cands'] as $x) { if (isset($x['who'])) { $standings[(string)$x['who'] . '|' . $c['b']][] = [$c, $x]; } } }

/* Overrides. */
$MIDTERM = [   /* #10 */
    'james webb|21588|2020' => 'Erin Wilson stood for Trustee Area 4 at the election of November 5, 2024 as the incumbent (California Elections Data Archive (CEDA), CEDA2024Data.xlsx), so this term ended before then; when, and how, is not recorded.',
    'cassandra love|21592|2022' => 'Patti Garibay holds Trustee Area 1 on the district\'s board page (' . $C_SAUGUS . '), and the County\'s list of elections cancelled in November 2024 includes an unexpired term for Area 1 ending in December 2026 (' . $cancelCite(2024) . '). This term ended early; when, and how, is not recorded.',
    'bj atkins|402|2020' => 'He did not stand in 2024. The agency\'s list of directors gives Kenneth Petersen as appointed in Division 3 in September 2022 (' . $C_SCVL . '); when this term ended, and how, is not recorded.',
];
$ROSTER_BRIDGE = ['matthew watson|21592|4|2024' => 'The district\'s board page lists Matthew Watson for Trustee Area 4 (' . $C_SAUGUS . '), and the County\'s list of elections cancelled in November 2024 includes Saugus Union Trustee Area 4 (' . $cancelCite(2024) . ').'];
$hartTo = []; foreach (($hartPage['members'] ?? []) as $m) { foreach ($keysOf($m['name']) as $k) { $hartTo[$canon($k) . '|' . $m['area']] = $m['termTo']; } }
$scvTo = []; foreach (($scvList['directors'] ?? []) as $d) { foreach ($keysOf($d['name']) as $k) { $scvTo[$canon($k) . '|' . $d['division']] = $d['expires']; } }

/* ------------------------------------------------ the terms */
$T = [];   /* each: who, b, place, area, sp, se, ep, ee, sel, how, sev, eev, notes, kind */
$research = []; $before = []; $bridgeFail = [];
$transNote = function (int $b, int $y, $n) use ($SCHOOL) {
    $due = $y + 4;
    if ($n === '2015|2016') { return 'The district moved its elections from odd to even years after 2013, and no contest for these seats is recorded in 2015 or 2016: the term, due to end in December 2015, ended in December 2015 or December 2016.'; }
    if (is_int($n) && $n !== $due && isset($SCHOOL[$b]) && $y % 2 === 1 && $n % 2 === 0) { return "The district moved its elections from odd to even years: this term, due to end in December $due, ran to December $n, the next election for the seat."; }
    return null;
};
foreach ($contests as $c) {
    $b = $c['b']; $y = $c['y'];
    foreach ($c['cands'] as $i => $x) {
        if (!$x['won']) { continue; }
        $who = $x['who'] ?? null;
        if (!$who) { $bad[] = "no person for the $y {$ALLB[$b]} winner {$x['name']}"; continue; }
        $name = $whoName($who); $wkey = is_string($who) ? substr($who, 4) : ($keyOfCand($x) ?? '');
        [$sp, $se] = $startOf($b, $y);
        $notes = [];
        if ($c['county']) { $notes[] = 'Elected on November 8, 2022, first of 2 candidates for Trustee Area 2. ' . $C_HART22; }
        else { $notes[] = 'Elected on ' . $printed($c['date']) . ', ' . $ord($i) . ' of ' . count($c['cands']) . ' candidates for ' . $seatWord($c) . ($c['part'] === 'short' ? ' (a short term, the remainder of a vacant term)' : ($c['part'] === 'two-year' ? ' (a two-year term)' : '')) . ', with ' . number_format($x['votes']) . ' votes (' . $citeOf($c, $x) . ').'; }
        /* #9: a first win as the incumbent */
        $mine = $standings[(string)$who . '|' . $b] ?? [];
        $firstWin = min(array_map(fn($s) => $s[0]['y'], array_filter($mine, fn($s) => $s[1]['won'])));
        if ($x['inc'] && $y === $firstWin && !$c['county']) {
            $earlier = (bool)array_filter($existing, fn($e) => $e['pid'] == $who && $e['b'] == $b && $yr($e['s']) < $y);
            if ($y === 1995) { $notes[] = "$name stood as the incumbent, having served on the board before 1995, the first year of these election records."; if (!$earlier) { $before[] = [$name, $b]; } }
            else { $notes[] = "$name stood as the incumbent: an earlier appointment to the board, whose date is not recorded here."; if (!$earlier) { $research[] = [$name, $b, $y]; } }
        }
        /* the end */
        $n = $nextYear($b, $y, $c['part']);
        $how = null; $ep = null; $ee = null; $eev = 'derived'; $bridges = [];
        if ($b === $CLWA) {
            [$ep, $ee, $how] = ['December 31, 2017', '2017-12-31', 'left'];
            [$sp, $se] = ['January 2017', '2017-01'];
            $notes[] = 'The new board took office in January: it chose its officers at a special meeting on January 3, 2017 (Press Release (CLWA), "CLWA Elects Board President, Vice President", January 10, 2017, https://scvnews.com/clwa-elects-board-president-vice-president). The agency ended on January 1, 2018, when it merged into the Santa Clarita Valley Water Agency.';
        } elseif (isset($MIDTERM["$wkey|$b|$y"])) {
            $how = 'unknown'; $eev = null; $notes[] = $MIDTERM["$wkey|$b|$y"];
        } elseif ($n === '2015|2016') {
            [$ep, $ee, $how] = ['December 2016', '2016-12?', 'unknown'];
            $notes[] = $transNote($b, $y, $n);
        } else {
            [$ep, $ee] = $endOf($b, $n);
            if ($tn = $transNote($b, $y, $n)) { $notes[] = $tn; }
            $atN = $byBodyYear[$b][$n] ?? [];
            if ($c['area'] !== '') { $seatC = array_values(array_filter($atN, fn($k) => $k['area'] === $c['area'])); }
            elseif ($n < 2016 || !isset($SCHOOL[$b])) { $seatC = array_values(array_filter($atN, fn($k) => $k['area'] === '')); }
            else { $seatC = $atN; }
            $stoodAt = array_values(array_filter($mine, fn($s) => $s[0]['y'] === $n && ($c['area'] === '' || $s[0]['area'] === $c['area'])));
            $wonAt = array_values(array_filter($stoodAt, fn($s) => $s[1]['won']));
            $canc = $CANCEL[$n]['b'][$b] ?? [];
            if (!$past($ee)) { $how = 'serving'; }
            elseif ($wonAt) { $how = 'reelected'; $notes[] = "The seat was next filled at the election of " . $printed($wonAt[0][0]['date']) . ", which $name won."; }
            elseif ($stoodAt) { $how = 'expired'; $notes[] = "$name stood again at the election of " . $printed($stoodAt[0][0]['date']) . ' and lost.'; }
            elseif ($seatC && ($c['area'] !== '' || $n < 2016 || !isset($SCHOOL[$b]) || (array_sum(array_map(fn($k) => $k['seats'], $seatC)) >= $c['seats'] && !$canc))) {
                $how = 'expired'; $notes[] = "$name did not stand at the next election for the seat, on " . $printed($seatC[0]['date']) . '.';
            } else {
                /* #5: the same person later, as the incumbent */
                $later = array_values(array_filter($mine, fn($s) => $s[1]['won'] && $s[1]['inc'] && $s[0]['y'] > $n));
                usort($later, fn($a, $b2) => $a[0]['y'] <=> $b2[0]['y']);
                $rb = $ROSTER_BRIDGE["$wkey|$b|{$c['area']}|$n"] ?? null;
                if ($later || $rb) {
                    $how = 'reelected';
                    $m = $later ? $later[0][0]['y'] : null;
                    $k = $n; $guard = 0; $failed = false;
                    while (($m === null && $guard === 0) || ($m !== null && is_int($k) && $k < $m)) {
                        $kn = $nextYear($b, $k, 'full');
                        if (!is_int($kn)) { $bridgeFail[] = [$who, $b, $startOf($b, $k)[1], "$name ($ALLB[$b]): a bridging term from $k crosses the 2015/2016 switch, and no existing holding covers it"]; $failed = true; break; }
                        $hereC = array_values(array_filter($byBodyYear[$b][$k] ?? [], fn($q) => $c['area'] !== '' ? $q['area'] === $c['area'] : ($k < 2016 && $q['area'] === '')));
                        if ($hereC) { $bad[] = "$name ($ALLB[$b]): a contest for the seat is recorded in $k, so it cannot be bridged"; break; }
                        $bridges[] = [$k, $kn, $m === null ? $rb : null];
                        $k = $kn; $guard++;
                        if ($guard > 5) { $bad[] = "$name: bridging runs away"; break; }
                    }
                    if ($m !== null && $k !== $m && !$failed) { $bad[] = "$name ($ALLB[$b]): the bridge from $n does not reach $m"; }
                    $notes[] = "No contest for the seat is recorded at the next election, in $n" . (in_array($c['area'], $canc, true) ? ', and the County lists it among the seats filled without a vote (' . $cancelCite($n) . ')' : '') . ": $name continued in it, " . ($m ? "standing as the incumbent at the election of " . $printed($later[0][0]['date']) . '.' : 'as the district\'s board page shows.');
                } else {
                    $how = 'unknown';
                    $notes[] = "The seat was next due for election in $n. No contest for it is recorded" . (($canc && ($c['area'] === '' || in_array($c['area'], $canc, true))) ? ', and the County lists ' . ($c['area'] === '' ? 'seats of this board' : 'it') . ' among those filled by appointment in lieu of election, without naming the appointees (' . $cancelCite($n) . ')' : '') . ". Whether $name continued is not known.";
                }
            }
            if ($how === 'serving' || $how === 'reelected' || $how === 'expired' || $how === 'unknown') {
                $wk = $wkey . '|' . $c['area'];
                if (isset($SCHOOL[$b]) && $b === 21588 && isset($hartTo[$wk]) && (int)$hartTo[$wk] === $n) { $eev = 'roster'; $notes[] = "The district's board page gives this term as $y to $n ($C_HART)."; }
                if ($b === $SCVW && isset($scvTo[$wk]) && $scvTo[$wk] === $ep) { $eev = 'roster'; $notes[] = "The agency's list of directors gives the term as expiring in $ep ($C_SCVL)."; }
            }
            if ($how === 'unknown' || $bridges) { $eev = 'derived'; }
        }
        $notes[] = $isWater($b) ? ($b === $SCVW ? "The agency's directors take office in January after a November election, as its list of directors gives their terms ($C_SCVL)." : null) : $C_5017;
        $sev = $c['county'] ? 'certified' : ($x['ev'] ?: 'derived');
        $T[] = ['who' => $who, 'b' => $b, 'place' => $c['area'] !== '' ? $c['place'] : null, 'area' => $c['area'], 'sp' => $sp, 'se' => $se, 'ep' => $ep, 'ee' => $ee, 'sel' => 'elected', 'how' => $how,
            'sev' => $sev, 'eev' => $ep ? $eev : null, 'notes' => array_values(array_filter($notes)), 'kind' => 'win', 'y' => $y];
        /* the bridging terms */
        foreach ($bridges as $j => [$k, $kn, $rbNote]) {
            [$bsp, $bse] = $startOf($b, $k); [$bep, $bee] = $endOf($b, $kn);
            $bn = ["No contest for the seat is recorded at the election of $k" . (in_array($c['area'], $CANCEL[$k]['b'][$b] ?? [], true) ? ' (' . $cancelCite($k) . ')' : '') . ": the seat was filled without a vote. $C_10515"];
            $bn[] = $rbNote ?? ("$name had won the seat in $y and stood as the incumbent in " . ($later[0][0]['y'] ?? '') . '; this term is inferred from the two.');
            $bhow = !$past($bee) ? 'serving' : 'reelected';
            if ($j < count($bridges) - 1) { $bhow = 'reelected'; }
            $bn[] = $isWater($b) ? null : $C_5017;
            $T[] = ['who' => $who, 'b' => $b, 'place' => $c['area'] !== '' ? $c['place'] : null, 'area' => $c['area'], 'sp' => $bsp, 'se' => $bse, 'ep' => $bep, 'ee' => $bee, 'sel' => 'appointed', 'how' => $bhow,
                'sev' => 'derived', 'eev' => 'derived', 'notes' => array_values(array_filter($bn)), 'kind' => 'bridge', 'y' => $k];
        }
    }
}
/* #11: the CLWA 2016 winners on SCV Water's founding board. */
$e16 = array_filter($contests, fn($c) => $c['b'] === $CLWA);
foreach ($e16 as $c) { foreach ($c['cands'] as $x) { if (!$x['won']) { continue; }
    $who = $x['who']; $name = $whoName($who);
    $st22 = array_values(array_filter($standings[(string)$who . '|' . $SCVW] ?? [], fn($s) => $s[0]['y'] === 2022));
    $how = $st22 ? ($st22[0][1]['won'] ? 'reelected' : 'expired') : 'expired';
    $notes = [$C_SB634, "$name was elected to the Castaic Lake Water Agency on " . $printed($c['date']) . ' for a term due to end after the 2020 general election, which SB 634 extended to the 2022 general election; directors elected then took office in January 2023 (' . $C_SCVL . ').',
        $st22 ? ($st22[0][1]['won'] ? "$name won the seat again at the election of November 8, 2022." : "$name stood at the election of November 8, 2022 and lost.") : "$name did not stand at the election of November 8, 2022."];
    $T[] = ['who' => $who, 'b' => $SCVW, 'place' => null, 'area' => '', 'sp' => 'January 1, 2018', 'se' => '2018-01-01', 'ep' => 'January 2023', 'ee' => '2023-01', 'sel' => 'succeeded', 'how' => $how,
        'sev' => 'derived', 'eev' => 'derived', 'notes' => $notes, 'kind' => 'succeeded', 'y' => 2018];
} }
/* #10: Petersen's appointment, from the agency's list. */
$pet = array_values(array_filter($scvList['directors'] ?? [], fn($d) => str_starts_with($d['name'], 'Kenneth J. Petersen')));
$petWho = isset($newP['kenneth petersen']) ? 'new:kenneth petersen' : (count($pIndex['kenneth petersen'] ?? []) === 1 ? array_key_first($pIndex['kenneth petersen']) : null);
if (!$pet || $pet[0]['appointed'] !== 'September 2022' || !$petWho) { $bad[] = 'Petersen\'s appointment is not on the agency\'s list as expected'; }
else {
    $d3 = Entry::find()->section('places')->status(null)->title('Santa Clarita Valley Water, Division 3')->one();
    $T[] = ['who' => $petWho, 'b' => $SCVW, 'place' => $d3?->id, 'area' => '3', 'sp' => 'September 2022', 'se' => '2022-09', 'ep' => 'January 2025', 'ee' => '2025-01', 'sel' => 'appointed', 'how' => 'reelected',
        'sev' => 'roster', 'eev' => 'derived', 'kind' => 'appointed', 'y' => 2022,
        'notes' => ["The agency's list of directors gives Kenneth J. Petersen, Division 3, as appointed in September 2022 ($C_SCVL). Which director's seat he filled is not recorded.",
            'He won a two-year term for Division 3 at the election of November 5, 2024, beginning in January 2025.']];
}

/* ------------------------------------------------ Hart from Leon Worden's roster */
$HART = 21588;
$R = json_decode(@file_get_contents("$root/inventory/legacy/hart-board-roster.json"), true);
$RHTM = "$root/inventory/legacy/fetched/hartschoolboardmembers.htm";
if (!$R || !is_file($RHTM) || hash_file('sha256', $RHTM) !== ($R['meta']['sha256'] ?? '') || empty($R['meta']['manifest_matched'])) { $bad[] = 'the Hart roster or its page is missing, or differs from the manifest'; $R = ['meta' => [], 'boards' => [], 'members' => []]; }
$C_ROSTER = 'Leon Worden, William S. Hart Union High School District Governing Board Members, SCVHistory.com, /scvhistory/hartschoolboardmembers.htm';
$RSTOP = '2014-11';   /* the last board printed ran 12-11-2013 to 11-30-2014 */
/* A roster name that matches an existing person with no other Hart tie is linked only when confirmed here. */
$RCONFIRM = [16418 => 'Connie Worden: her sources, imported 3 October 2026, include this roster for her 1974 to 1979 seat'];
$prE = function (?string $e) {
    if (!$e) { return ''; }
    $p = explode('-', $e);
    return count($p) === 1 ? $p[0] : (count($p) === 2 ? date('F Y', strtotime("$e-01")) : date('F j, Y', strtotime($e)));
};
$rm = []; $rLink = []; $rByWho = []; $rNameHit = []; $rKey = [];
foreach ($R['members'] as $m) {
    if ($m['student']) { continue; }
    $rm[$m['key']] = $m;
    $hits = [];
    foreach ($m['printings'] as $pn) { foreach ($keysOf($pn) as $k) { $k = $canon($k); $rKey[$k] = $m['key'];
        foreach (array_keys($pIndex[$k] ?? []) as $id) { $hits[$id] = true; }
        if (isset($newP[$k])) { $hits["new:$k"] = true; } } }
    $hits = array_keys($hits);
    if (count($hits) > 1) { $bad[] = 'roster member ' . $m['printings'][0] . ' matches more than one person: ' . implode(', ', $hits); continue; }
    if (!$hits) { continue; }
    $w = $hits[0];
    $tied = is_string($w) || isset($standings["$w|$HART"]) || array_filter($existing, fn($e) => $e['pid'] == $w && $e['b'] == $HART) || isset($RCONFIRM[$w]);
    if ($tied) { $rLink[$m['key']] = $w; $rByWho[(string)$w] = $m['key']; } else { $rNameHit[] = [$m, $w]; }
}
$boardAt = function (string $ym) use ($R) {
    foreach ($R['boards'] as $i => $b) { if (strlen($b['start']) >= 7 && substr($b['start'], 0, 7) <= $ym && $b['end'] && substr($b['end'], 0, 7) >= $ym) { return $i; } }
    return null;
};
$listedOn = fn(string $mk, ?int $bi) => $bi === null ? null : (array_values(array_filter($rm[$mk]['rows'], fn($r) => $r['board'] === $bi))[0] ?? null);
$firstRowFrom = fn(string $mk, string $ym) => array_values(array_filter($rm[$mk]['rows'], fn($r) => strlen($r['boardStart']) >= 7 && substr($r['boardStart'], 0, 7) >= substr($ym, 0, 7)))[0] ?? null;
$qr = fn(string $label, string $raw) => "on the board of $label as \"$raw\"";
$RLASTB = end($R['boards'])['label'] ?? '';
/* how the roster's end row reads: a reelection is printed on the next board, after the election */
$qEnd = fn(string $how, string $label, string $raw) => $how === 'reelected' ? ", and, after the next election, on the board of $label as \"$raw\"" : ', and last ' . "on the board of $label as \"$raw\"";
$RHOW = ['expired' => 'ended', 'reelected' => 'ended in reelection', 'resigned' => 'ended in resignation', 'unknown' => 'ended, how not stated', 'recalled' => 'ended in a recall'];

/* Each linked member's roster terms, cut where a CEDA term of the same person starts inside one (Aliano: appointed
   in 1994, then the 1995 short term). A cut end is "derived" (from the election); a cut start quotes the next row. */
$hartTs = fn($w) => array_filter($T, fn($t) => $t['b'] === $HART && $t['who'] === $w);
$pieces = [];   /* mk => [ term + basis ] */
foreach ($rLink as $mk => $w) {
    $starts = array_unique(array_map(fn($t) => $t['se'], $hartTs($w)));
    foreach ($rm[$mk]['terms'] as $rt) {
        $cuts = array_values(array_filter($starts, fn($s) => $s > $rt['start'] && $s <= $RSTOP && ($rt['end'] === null || $s < $rt['end'])));
        sort($cuts);
        $cur = $rt + ['endCut' => false, 'startCut' => false, 'endNote' => null, 'startNote' => null, 'unopposed' => false];
        foreach ($cuts as $s) {
            $pieces[$mk][] = array_merge($cur, ['end' => $s, 'howEnded' => 'reelected', 'endCut' => true, 'endNote' => null]);
            $row = $firstRowFrom($mk, $s);
            $cur = array_merge($rt, ['start' => $s, 'selection' => 'elected', 'startCut' => true, 'startBasis' => $row['raw'] ?? '', 'startBoard' => $row['boardLabel'] ?? '', 'startNote' => null, 'endCut' => false]);
        }
        $pieces[$mk][] = $cur;
    }
}

/* The CEDA terms (and bridges) the roster can speak to: start before the roster stops. */
$hartAgree = []; $hartDis = []; $rMatched = [];
$dropNine = '~stood as the incumbent(: an earlier appointment|, having served on the board before 1995)~';
foreach ($T as $i => &$t) {
    if ($t['b'] !== $HART || !in_array($t['kind'], ['win', 'bridge'], true) || $t['se'] > $RSTOP) { continue; }
    $w = $t['who']; $name = $whoName($w); $mk = $rByWho[(string)$w] ?? null;
    $pc = null;
    if ($mk) { foreach ($pieces[$mk] ?? [] as $j => $p) { if ($p['start'] === $t['se']) { $pc = $p; $rMatched["$mk|$j"] = true; } } }
    if (!$pc) {
        $row = $mk ? $firstRowFrom($mk, $t['se']) : null;
        $hartDis[] = "$name, " . $t['kind'] . ' ' . $t['se'] . ' to ' . ($t['ee'] ?: '(blank)') . ': ' . ($mk ? 'the roster has no term starting then' . ($row ? ' (next listed ' . $qr($row['boardLabel'], $row['raw']) . ')' : '') : 'the roster does not list this person') . '. Footnoted, not resolved.';
        $t['notes'][] = "Leon Worden's roster of the Hart board does not show a term for $name beginning in " . $prE($t['se']) . " ($C_ROSTER). The term is given here from the election returns.";
        continue;
    }
    /* #9 notes give way to the roster, which dates the earlier service */
    $hadNine = (bool)preg_grep($dropNine, $t['notes']);
    $t['notes'] = array_values(array_filter($t['notes'], fn($n) => !preg_match($dropNine, $n)));
    if ($hadNine) {
        $research = array_values(array_filter($research, fn($r) => !($r[0] === $name && $r[1] === $HART)));
        $before = array_values(array_filter($before, fn($r) => !($r[0] === $name && $r[1] === $HART)));
    }
    $startTxt = $pc['startBasis'] !== '' ? $qr($pc['startBoard'], $pc['startBasis']) : 'from ' . $prE($pc['start']);
    $sentence = "Leon Worden's roster of the Hart board lists $name $startTxt";
    $endAgrees = false; $endTxt = '';
    if ($pc['end'] === null) {
        $endTxt = $pc['startBoard'] === $RLASTB ? ', the last board it prints' : ", and on every board after it to the last it prints, $RLASTB";
        if ($t['ee'] && $t['ee'] <= $RSTOP) { $hartDis[] = "$name, {$t['se']}: CEDA's term ends {$t['ee']} ({$t['how']}), but the roster lists $name on the board of 12-11-2013 to 11-30-2014. Footnoted, not resolved."; $endTxt .= ', after the end given here'; }
    } elseif ($pc['endCut']) {
        $endTxt = '';
    } else {
        $endTxt = $qEnd((string)$pc['howEnded'], $pc['endBoard'], $pc['endBasis']) . (($pc['endNote'] ?? null) && str_starts_with($pc['endNote'], 'not listed') ? ' (the next board does not list ' . $name . ')' : '');
        $endAgrees = $pc['end'] === $t['ee'] && $pc['howEnded'] === $t['how'];
        if (!$endAgrees) {
            $cv = $coveredBy($w, $HART, $t['se']);
            $hartDis[] = "$name, {$t['se']}: the election returns give the end as {$t['ee']} ({$t['how']}); the roster gives {$pc['end']} ({$pc['howEnded']}): \"{$pc['endBasis']}\". " . ($cv ? "Skipped as inside #$cv (#8), which already ends as the roster does." : 'Footnoted, not resolved.');
            $endTxt .= '. The roster\'s end, ' . $prE($pc['end']) . ' (' . ($RHOW[$pc['howEnded']] ?? $pc['howEnded']) . '), differs from the one given here, ' . ($t['ee'] ? $prE($t['ee']) : 'blank') . ' (' . ($RHOW[$t['how']] ?? $t['how']) . '), which follows the election returns';
        }
    }
    $t['notes'][] = $sentence . $endTxt . " ($C_ROSTER).";
    /* a bridge the roster gives is no longer only an inference */
    if ($t['kind'] === 'bridge' && $pc['startBasis'] !== '') { $t['notes'] = array_values(array_filter($t['notes'], fn($n) => !str_contains($n, 'this term is inferred from the two'))); }
    if ($t['kind'] === 'win' || $pc['startBasis'] !== '') { $t['sev'] = 'roster'; }
    if ($endAgrees) { $t['eev'] = 'roster'; }
    $t['rosterAgree'] = $endAgrees ? 'both' : ($pc['end'] === null || $pc['endCut'] ? 'start' : 'start, end differs');
    $hartAgree[] = sprintf('%-24s %-7s %-10s -> %-10s %-10s roster: %s -> %s %s', $name, $t['kind'], $t['se'], $t['ee'] ?: '(blank)', $t['how'], $pc['start'], $pc['end'] ?? 'open', $t['rosterAgree']);
} unset($t);

/* CEDA's flags and losses against the roster, 1995 to 2013. */
$candDis = []; $candAgree = 0;
foreach ($contests as $c) {
    if ($c['b'] !== $HART || $c['y'] > 2013) { continue; }
    $bi = $boardAt($c['y'] . '-11');
    foreach ($c['cands'] as $x) {
        $mk = isset($x['who']) ? ($rByWho[(string)$x['who']] ?? null) : null;
        if (!$mk) { foreach ($keysOf($x['name']) as $k) { if (isset($rKey[$canon($k)])) { $mk = $rKey[$canon($k)]; break; } } }
        $row = $mk ? $listedOn($mk, $bi) : null;
        $lab = $bi !== null ? $R['boards'][$bi]['label'] : '?';
        if ($x['inc'] && !$row) {
            $candDis[] = "{$c['y']} \"{$x['name']}\" " . ($x['won'] ? 'won' : 'lost') . ": CEDA marks the incumbent; the roster does not list this name on the board then sitting ($lab)" . ($x['won'] ? '. Both footnoted on the term, not resolved' : '');
            if ($x['won'] && isset($x['who'])) { foreach ($T as &$t) { if ($t['b'] === $HART && $t['who'] === $x['who'] && $t['kind'] === 'win' && $t['y'] === $c['y']) {
                $t['notes'][] = 'The election returns compiled by CEDA mark ' . $whoName($x['who']) . " as the incumbent at this election; Leon Worden's roster of the Hart board does not list him on the board then sitting, $lab ($C_ROSTER).";
            } } unset($t); }
        }
        elseif (!$x['inc'] && $row) { $candDis[] = "{$c['y']} \"{$x['name']}\": on the sitting board ($lab, \"{$row['raw']}\") but CEDA does not mark the incumbent"; }
        else { $candAgree++; }
        if (!$x['won'] && $row) {
            $def = array_filter($row['events'], fn($e) => $e['type'] === 'defeated' && $e['date'] === (string)$c['y']);
            if ($def) { $candAgree++; } else { $candDis[] = "{$c['y']} \"{$x['name']}\" lost as a sitting member; the roster does not print \"defeated {$c['y']}\" (\"{$row['raw']}\")"; }
        }
    }
}
foreach ($rm as $mk => $m) { foreach ($m['rows'] as $r) { foreach ($r['events'] as $e) {
    $ey = (int)substr((string)$e['date'], 0, 4);
    if ($ey < 1995 || $ey > 2013) { continue; }
    $stood = array_filter($contests, fn($c) => $c['b'] === $HART && $c['y'] === $ey && array_filter($c['cands'], function ($x) use ($mk, $rByWho, $keysOf, $canon, $rKey) {
        if (isset($x['who']) && ($rByWho[(string)$x['who']] ?? null) === $mk) { return true; }
        foreach ($keysOf($x['name']) as $k) { if (($rKey[$canon($k)] ?? null) === $mk) { return true; } } return false; }));
    if ($e['type'] === 'did not seek reelection' && $stood) {
        $also = array_filter($m['rows'], fn($r2) => array_filter($r2['events'], fn($e2) => in_array($e2['type'], ['elected', 'reelected'], true) && (string)$e2['date'] === (string)$ey));
        $candDis[] = "$ey {$m['printings'][0]}: the roster prints \"{$r['raw']}\" on the board of {$r['boardLabel']}, but CEDA has a winning or losing candidacy in $ey" . ($also ? ', and the roster itself prints "' . array_values($also)[0]['raw'] . '" on the next board: the roster contradicts itself; its terms follow the reelection' : '');
    }
    if (in_array($e['type'], ['elected', 'reelected'], true) && !$stood) { $candDis[] = "$ey {$m['printings'][0]}: the roster prints \"{$e['text']}\"; CEDA has no Hart contest for this person in $ey (no contest recorded, so CEDA lacks it rather than contradicts it)"; }
} } }

/* Roster service CEDA lacks, for linked people: a holding from the roster. */
$rosterOnly = [];
foreach ($pieces as $mk => $ps) { foreach ($ps as $j => $p) {
    if (isset($rMatched["$mk|$j"])) { continue; }
    $w = $rLink[$mk]; $name = $whoName($w);
    /* Roster service already held by a Hart holding of this person (create_hart_early_members.php holds the
       tenures of those it created, Hall's open term among them): skipped, by date, not by year. */
    $pad = fn($d) => str_pad(rtrim((string)$d, '~?'), 10, $d && strlen(rtrim((string)$d, '~?')) === 4 ? '-01-01' : '-01');
    $held = is_int($w) ? array_filter($existing, fn($x) => $x['pid'] == $w && $x['b'] == 21588 && $pad($x['s']) <= $pad($p['start']) && ($x['e'] === '' || $pad($p['start']) <= str_pad(rtrim($x['e'], '~?'), 10, '-31'))) : [];
    if ($held) { $skipped[] = [$name, 21588, (string)$p['start'], array_values($held)[0]['id']]; continue; }
    if ($p['end'] === null) { $bad[] = "$name: an open roster term from {$p['start']} matches no term from the election returns"; continue; }
    $n = [];
    $n[] = "Leon Worden's roster of the Hart board lists $name " . $qr($p['startBoard'], $p['startBasis'])
        . ($p['endCut'] ? '' : ($p['endBasis'] !== $p['startBasis'] ? $qEnd((string)$p['howEnded'], $p['endBoard'], $p['endBasis']) : '')
        . (($p['endNote'] ?? null) && str_starts_with($p['endNote'], 'not listed') ? '; the next board, ' . preg_replace('~^not listed on the next board, ~', '', $p['endNote']) . ', does not list ' . $name : '')
        . (($p['endNote'] ?? null) && str_starts_with($p['endNote'], 'no date printed') ? ' (the roster prints no date for the resignation)' : '')) . " ($C_ROSTER).";
    if (($p['startNote'] ?? null) && $p['selection'] === 'appointed') { $n[] = 'The roster prints no date for the appointment' . (($p['predecessorRow'] ?? null) ? ', which completed the term of the member it lists as "' . $p['predecessorRow'] . '"; the year given here is that resignation\'s.' : '; the year given here is the first of the board that lists it.'); }
    if ($p['endCut']) {
        $nx = array_values(array_filter($T, fn($t) => $t['b'] === $HART && $t['who'] === $w && $t['se'] === $p['end']))[0] ?? null;
        $n[] = $p['selection'] === 'appointed' ? "The appointment ran to the election of " . ($nx ? 'November ' . $nx['y'] : $p['end']) . ", at which $name won the seat." : "The term ran to the election of " . ($nx ? 'November ' . $nx['y'] : $p['end']) . ", at which $name won the seat.";
    }
    if (strlen($p['start']) === 7 && $p['start'] >= '1979-12' && $p['selection'] === 'elected') { $n[] = $C_5017; }
    $rosterOnly[] = $T[] = ['who' => $w, 'b' => $HART, 'place' => null, 'area' => '', 'sp' => $prE($p['start']), 'se' => $p['start'], 'ep' => $prE($p['end']), 'ee' => $p['end'], 'sel' => $p['selection'],
        'how' => $p['howEnded'], 'sev' => 'roster', 'eev' => $p['endCut'] ? 'derived' : 'roster', 'notes' => $n, 'kind' => 'roster', 'y' => (int)substr($p['start'], 0, 4), 'rosterAgree' => 'roster only'];
} }

/* Roster against the tenures #8 keeps. */
$tenureCmp = [];
foreach ($existing as $e) {
    if ($e['b'] !== $HART || !($mk = $rByWho[(string)$e['pid']] ?? null)) { continue; }
    $in = array_values(array_filter($pieces[$mk] ?? [], fn($p) => $yr($p['start']) >= $yr($e['s']) && ($e['e'] === '' || $yr($p['start']) < $yr($e['e']))));
    if (!$in) { $tenureCmp[] = "#{$e['id']} " . $whoName($e['pid']) . " {$e['s']} to " . ($e['e'] ?: 'open') . ': the roster shows no term inside it'; continue; }
    $first = $in[0]; $last = end($in);
    $h = $e['h']; $how = (string)$h->howEnded?->value;
    $diffs = [];
    if ($yr($first['start']) !== $yr($e['s'])) { $diffs[] = "start {$e['s']} against the roster's {$first['start']}"; }
    if ($last['end'] === null) { if ($e['e'] !== '' && $e['e'] <= $RSTOP) { $diffs[] = "end {$e['e']}, but the roster lists the member to its last board"; } }
    else {
        if ($yr($last['end']) !== $yr($e['e'])) { $diffs[] = "end {$e['e']} against the roster's {$last['end']}"; }
        if ($how !== $last['howEnded']) { $diffs[] = "howEnded \"$how\" against the roster's \"{$last['howEnded']}\" (\"{$last['endBasis']}\")"; }
    }
    $extra = [];
    foreach ($rm[$mk]['rows'] as $r) { foreach ($r['events'] as $ev) { if ($ev['type'] === 'did not assume office') { $extra[] = "the roster adds, on the board of {$r['boardLabel']}: \"{$r['raw']}\""; } } }
    $tenureCmp[] = "#{$e['id']} " . $whoName($e['pid']) . " {$e['s']} to " . ($e['e'] ?: 'open') . " ($how): " . ($diffs ? 'CONFLICT: ' . implode('; ', $diffs) : 'agrees with the roster (' . $first['start'] . ' to ' . ($last['end'] ?? 'its last board') . ')') . ($extra ? '; ' . implode('; ', $extra) : '');
}

/* Roster members with no record: not created (Nathan's call). */
$rNoRec = ['pre' => [], 'post' => []];
foreach ($rm as $mk => $m) {
    if (isset($rLink[$mk])) { continue; }
    $s = $m['terms'][0]['start'] ?? ''; $e = end($m['terms'])['end'] ?? null;
    $rNoRec[$s < '1995' ? 'pre' : 'post'][] = [$m, $s, $e];
}

/* #8 and idempotency */
$plan = []; $skipped = []; $exists = [];
foreach ($T as $t) {
    $cov = $coveredBy($t['who'], $t['b'], $t['se']);
    if ($cov) { (array_filter($existing, fn($e) => $e['id'] === $cov && $e['s'] === $t['se']) ? $exists[] = $t + ['by' => $cov] : $skipped[] = $t + ['by' => $cov]); continue; }
    $plan[] = $t;
}
foreach ($bridgeFail as [$w, $b, $se, $msg]) { if (!$coveredBy($w, $b, $se)) { $bad[] = $msg; } }
/* #2b */
$fixes = [];
$pl = $get(28219);
if ($pl && $pl->termEndEdtf === '2022-12') { $fixes[] = [28219, 'Lynne Plambeck, SCV Water: end December 2022 -> January 2023 (her successor took the seat in January 2023)']; }
elseif (!$pl || $pl->termEndEdtf !== '2023-01') { $bad[] = '#28219 is not Plambeck\'s SCV Water holding ending 2022-12'; }

/* ------------------------------------------------ checks on the wording and lengths */
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|next to try|to try next|with Nathan|Nathan\'s|Claude|image tag|commented out|read so far|search summary|page was blocked|ha(s|ve) not been (read|checked)|could not be read|[Ss]earched \d|sources searched|lists searched|release search)\b~i';
foreach ($plan as $t) { foreach ($t['notes'] as $n) {
    $u = preg_replace('~https?://\S+|\S+\.(?:com|org|gov|net)/\S*~', ' ', $n);
    if (preg_match($BAD, $u, $m) || preg_match('~\x{2014}|inventory/|\.json~u', $n, $m)) { $bad[] = $whoName($t['who']) . ' ' . $t['se'] . ": a footnote reads \"{$m[0]}\""; }
} }
$pProv = "$PROV: elected to a Santa Clarita Valley school or water board (decision 14); public facts only";
$hProv = fn($t) => "$PROV: " . ['win' => 'a term from the election results', 'bridge' => 'a seat filled without a vote, inferred (decision 5)', 'succeeded' => 'SCV Water founding board, SB 634 (decision 11)', 'appointed' => 'appointment on the agency\'s list (decision 10)', 'roster' => 'a term from Leon Worden\'s Hart board roster, hartschoolboardmembers.htm'][$t['kind']];
foreach (array_merge([$pProv], array_map($hProv, $plan)) as $s) { if (mb_strlen($s) > 255) { $bad[] = 'a recordProvenance runs over 255 characters'; break; } }

/* ------------------------------------------------ eras (#14), by the assign_person_eras.php rule */
$eras = [];
foreach (Category::find()->group('historicalEra')->all() as $cat) {
    if (in_array($cat->id, [163, 169], true) || !preg_match('~\((?:to )?(\d{4})?[^\d]*(\d{4}|present)\)~u', $cat->title, $m)) { continue; }
    $eras[$cat->id] = [$m[1] !== '' ? (int)$m[1] : -10000, $m[2] === 'present' ? (int)date('Y') : (int)$m[2], $cat->title];
}
$eraOf = function (int $y) use ($eras) { foreach ($eras as $id => [$f, $to]) { if ($y >= $f && $y <= $to) { return $id; } } return null; };
foreach ($newP as $k => &$np) {
    $anch = [];
    foreach ($T as $t) { if ($t['who'] === "new:$k") { $s = $yr($t['se']); $e = $t['ee'] ? $yr($t['ee']) : (int)date('Y'); for ($q = $s; $q <= max($s, $e); $q++) { $anch[] = $q; } } }
    foreach ($contests as $c) { foreach ($c['cands'] as $x) { if (($x['who'] ?? null) === "new:$k") { $anch[] = $c['y']; } } }
    $cnt = array_count_values(array_filter(array_map($eraOf, $anch)));
    arsort($cnt); $top = array_key_first($cnt);
    $np['era'] = ($top && $cnt[$top] * 3 >= count($anch) * 2) ? $top : null;
    $np['eraWhy'] = $top ? $eras[$top][2] . ' ' . $cnt[$top] . ' of ' . count($anch) : 'none';
    $np['roles'] = array_values(array_unique(array_map(fn($b) => $isWater($b) ? $RW : $RS, array_keys($np['bodies']))));
} unset($np);

/* ------------------------------------------------ the report */
$short = fn($b) => str_pad($ALLB[$b], 16);
echo PHP_EOL . '(1) PER BODY' . PHP_EOL;
printf("   %-16s %5s %8s %8s %7s %7s %7s %7s %6s %6s\n", 'body', 'wins', 'covered', 'derived', 'bridge', 'other', 'TOTAL', 'people', 'links', 'fixes');
$tot = array_fill(0, 9, 0);
foreach ($ALLB as $b => $lab) {
    $wins = count(array_filter($T, fn($t) => $t['b'] === $b && $t['kind'] === 'win'));
    $cov = count(array_filter(array_merge($skipped, $exists), fn($t) => $t['b'] === $b));
    $der = count(array_filter($plan, fn($t) => $t['b'] === $b && $t['kind'] === 'win' && $t['sev'] !== 'certified'));
    $bri = count(array_filter($plan, fn($t) => $t['b'] === $b && $t['kind'] === 'bridge'));
    $oth = count(array_filter($plan, fn($t) => $t['b'] === $b && (in_array($t['kind'], ['succeeded', 'appointed', 'roster'], true) || $t['sev'] === 'certified')));
    $all = count(array_filter($plan, fn($t) => $t['b'] === $b));
    $ppl = count(array_filter($newP, fn($np) => array_key_first($np['bodies']) === $b));
    $lnk = count(array_filter($links, fn($l) => $l[2] === $b));
    $fx = $b === $SCVW ? count($fixes) : 0;
    $row = [$wins, $cov, $der, $bri, $oth, $all, $ppl, $lnk, $fx];
    foreach ($row as $i => $v) { $tot[$i] += $v; }
    printf("   %-16s %5d %8d %8d %7d %7d %7d %7d %6d %6d\n", $lab, ...$row);
}
printf("   %-16s %5d %8d %8d %7d %7d %7d %7d %6d %6d\n", 'TOTAL', ...$tot);
echo '   wins: every candidacy marked elected, plus the 2022 Hart Area 2 contest (#15). covered: inside an existing holding (#8). derived: a win\'s own term. bridge: #5. other: the County-certified Hart 2022 term (#15), the four SB 634 founding terms (#11), Petersen\'s appointment (#10) and Hart terms from the roster alone. people: counted under the body of their first record.' . PHP_EOL;
$hw = array_count_values(array_map(fn($t) => $t['how'], $plan));
echo '   howEnded of the holdings to create: ' . implode(', ', array_map(fn($k, $v) => "$k $v", array_keys($hw), $hw)) . PHP_EOL;

echo PHP_EOL . 'HOLDINGS TO CREATE (' . count($plan) . ')' . PHP_EOL;
usort($plan, fn($a, $b) => [$a['b'], $whoName($a['who']), $a['se']] <=> [$b['b'], $whoName($b['who']), $b['se']]);
foreach ($plan as $t) {
    printf("   %s %-24s %-9s %-10s -> %-11s %-10s %-10s start %-9s end %-8s %s\n", $short($t['b']), mb_substr($whoName($t['who']), 0, 24) . (is_string($t['who']) ? '*' : ''), $t['area'] !== '' ? ($isWater($t['b']) ? 'Div ' : 'Area ') . $t['area'] : 'at large',
        $t['se'], $t['ee'] ?? '(blank)', $t['sel'], $t['how'], $t['sev'], $t['eev'] ?? '-', $t['kind'] === 'win' ? '' : $t['kind']);
}
echo '   (* a person to be created)' . PHP_EOL;
echo PHP_EOL . 'HART FOOTNOTES, as they would be written:' . PHP_EOL;
foreach ($plan as $t) { if ($t['b'] !== 21588) { continue; }
    echo '   ' . $whoName($t['who']) . ' ' . $t['se'] . ' to ' . ($t['ee'] ?: '(blank)') . ' [' . $t['kind'] . ']' . PHP_EOL;
    foreach ($t['notes'] as $i => $n) { echo '      ' . ($i + 1) . '. ' . $n . PHP_EOL; }
}
echo PHP_EOL . 'SKIPPED, inside an existing holding (#8): ' . count($skipped) . PHP_EOL;
foreach ($skipped as $t) { echo '   ' . $short($t['b']) . str_pad($whoName($t['who']), 24) . str_pad($t['se'], 10) . " inside #{$t['by']}" . PHP_EOL; }
if ($exists) { echo 'ALREADY HELD (same start): ' . implode('; ', array_map(fn($t) => $whoName($t['who']) . ' ' . $t['se'] . " #{$t['by']}", $exists)) . PHP_EOL; }
echo PHP_EOL . 'CORRECTIONS (#2b): ' . ($fixes ? implode('; ', array_column($fixes, 1)) : 'none') . PHP_EOL;

echo PHP_EOL . '(2) PEOPLE TO CREATE (' . count($newP) . ')' . PHP_EOL;
uasort($newP, fn($a, $b) => $a['title'] <=> $b['title']);
foreach ($newP as $k => $np) {
    echo '   ' . str_pad($np['title'], 24) . 'role ' . implode(', ', array_map(fn($r) => $r === $RS ? 'School Board Member' : 'Water Board Director', $np['roles'])) . '; era ' . ($np['era'] ? $eras[$np['era']][2] : 'left for assign_person_eras.php') . ' (' . $np['eraWhy'] . ')' . PHP_EOL;
    echo '      ' . implode(PHP_EOL . '      ', array_unique($np['cands'])) . PHP_EOL;
    if ($np['aliases']) { echo '      aliases: ' . implode('; ', $np['aliases']) . PHP_EOL; }
}

/* (3) the name-variant check */
$NICK = ['bob' => 'robert', 'rob' => 'robert', 'bobby' => 'robert', 'bill' => 'william', 'will' => 'william', 'billy' => 'william', 'chris' => 'christopher', 'dave' => 'david', 'mike' => 'michael', 'micheal' => 'michael',
    'kathy' => 'katherine', 'kathye' => 'katherine', 'kate' => 'katherine', 'katie' => 'katherine', 'kathryn' => 'katherine', 'catherine' => 'katherine', 'sue' => 'susan', 'suzan' => 'susan', 'suzanne' => 'susan', 'shelley' => 'rochelle', 'shelly' => 'rochelle',
    'ken' => 'kenneth', 'kenny' => 'kenneth', 'dan' => 'daniel', 'danny' => 'daniel', 'ed' => 'edward', 'eddie' => 'edward', 'tom' => 'thomas', 'steve' => 'steven', 'stephen' => 'steven', 'jim' => 'james', 'jimmy' => 'james',
    'joe' => 'joseph', 'jon' => 'jonathan', 'greg' => 'gregory', 'vic' => 'victor', 'liz' => 'elizabeth', 'beth' => 'elizabeth', 'pat' => 'patricia', 'patti' => 'patricia', 'phil' => 'philip', 'phillip' => 'philip',
    'larry' => 'lawrence', 'rose' => 'rosemarie', 'rosemary' => 'rosemarie', 'judy' => 'judith', 'cassie' => 'cassandra', 'matt' => 'matthew', 'denis' => 'dennis', 'doug' => 'douglas', 'rick' => 'richard', 'dick' => 'richard',
    'jeff' => 'jeffrey', 'tim' => 'timothy', 'andy' => 'andrew', 'drew' => 'andrew', 'gary' => 'gary', 'lori' => 'lorraine', 'les' => 'lester', 'bj' => 'bj', 'rj' => 'rj'];
$parts = function (string $name) use ($norm, $NICK) {
    $n = preg_replace(['~,?\s*\b(Jr|Sr|II|III|IV|P\.E|CPA|PhD|Dr)\b\.?~i', '~[“"]([^”"]+)[”"]~u'], ['', ' $1 '], $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($n)), fn($w) => $w !== ''));
    if (count($t) < 2) { return null; }
    $li = count($t) - 1; while ($li > 1 && preg_match('~^(de|la|del|van|von)$~i', $t[$li - 1])) { $li--; }
    $last = str_replace(' ', '', $norm(implode(' ', array_slice($t, $li))));
    $firsts = array_map(fn($w) => $norm(rtrim($w, '.')), array_slice($t, 0, $li));
    $full = array_values(array_filter($firsts, fn($w) => strlen($w) > 1));
    $f = $full[0] ?? ($firsts[0] ?? '');
    return ['last' => $last, 'lasts' => array_unique(array_merge([$last], explode('-', $last))), 'first' => $NICK[$f] ?? $f, 'init' => substr($f, 0, 1), 'allFirst' => array_map(fn($w) => $NICK[$w] ?? $w, $full), 'flat' => $f . ' ' . $last];
};
$verdict = function ($a, $b) {
    if (!$a || !$b) { return null; }
    if (!array_intersect($a['lasts'], $b['lasts'])) {
        /* a changed or misprinted surname: the same given name, surnames close */
        foreach ($a['lasts'] as $l1) { foreach ($b['lasts'] as $l2) { if ($a['first'] === $b['first'] && strlen($a['first']) > 1 && levenshtein($l1, $l2) <= 3) { return 'same given name, surnames close (a married name or a misprint?)'; } } }
        return null;
    }
    if ($a['first'] === $b['first']) { return 'same first name (nicknames folded) and surname'; }
    if (array_intersect($a['allFirst'], $b['allFirst'])) { return 'a shared given name and surname'; }
    if (levenshtein($a['first'], $b['first']) <= 1 && strlen($a['first']) > 3) { return 'given names one letter apart, same surname'; }
    if (strlen($a['first']) === 1 || strlen($b['first']) === 1) { return $a['init'] === $b['init'] ? 'an initial matching a given name, same surname' : null; }
    return null;
};
echo PHP_EOL . '(3) NAME VARIANTS: each new person against every existing person and every other new person' . PHP_EOL;
echo '   Decided as one person (#14), merged: Bob Jensen = Robert N. Jensen, Jr. (the County prints him "BOB JENSEN JR" in its 2022 Hart Area 2 statement, which supports the merge); Philip C. Ellis = Philip C. Ellis, Jr.; J. Micheal McGrath = John Michael McGrath; Denis F. De Figueiredo = Denis F. DeFigueiredo.' . PHP_EOL;
$flags = [];
foreach ($newP as $k => $np) {
    $mine = array_filter(array_map($parts, array_merge([$np['title']], $np['aliases'], $np['printings'])));
    $scores = [];
    foreach ($allPeople as $pid => $ap) {
        $best = 99; $why = null;
        foreach ($ap['names'] as $n) { $o = $parts($n); if (!$o) { continue; } foreach ($mine as $m) { $best = min($best, levenshtein($m['flat'], $o['flat'])); $why ??= $verdict($m, $o); } }
        $scores[] = [$best, '#' . $pid . ' ' . $ap['title'], $why];
        if ($why) { $flags[] = "{$np['title']} (new) and #$pid {$ap['title']}: $why"; }
    }
    usort($scores, fn($a, $b) => $a[0] <=> $b[0]);
    $others = [];
    foreach ($newP as $k2 => $np2) { if ($k2 === $k) { continue; }
        $best = 99; $why = null;
        foreach (array_filter(array_map($parts, array_merge([$np2['title']], $np2['printings']))) as $o) { foreach ($mine as $m) { $best = min($best, levenshtein($m['flat'], $o['flat'])); $why ??= $verdict($m, $o); } }
        $others[] = [$best, $np2['title'], $why];
        if ($why && $k < $k2) { $flags[] = "{$np['title']} (new) and {$np2['title']} (new): $why"; }
    }
    usort($others, fn($a, $b) => $a[0] <=> $b[0]);
    echo '   ' . str_pad($np['title'], 24) . 'closest existing: ' . implode('; ', array_map(fn($s) => "{$s[1]} ({$s[0]})", array_slice($scores, 0, 2))) . ' | closest new: ' . implode('; ', array_map(fn($s) => "{$s[1]} ({$s[0]})", array_slice($others, 0, 2))) . PHP_EOL;
}
echo '   FLAGGED, might be one person: ' . ($flags ? PHP_EOL . '      ' . implode(PHP_EOL . '      ', array_unique($flags)) : 'none') . PHP_EOL;
echo '   Also: SCV Water\'s 2016 CLWA winner "BILL PECSI" is "William Pecsi" in the CLWA press release of January 10, 2017 (a nickname, one person; the alias is not added without a decision).' . PHP_EOL;

/* (4) the research list (#9) */
echo PHP_EOL . '(4) RESEARCH LIST (#9): first recorded win as the incumbent, no holding covers the earlier service (' . count($research) . ')' . PHP_EOL;
foreach ($research as [$n, $b, $y]) {
    $from = $y - 4;
    echo '   ' . str_pad($n, 24) . str_pad($bodyTitle($b), 44) . "first recorded win $y. Settle with: the board's minutes, $from to $y (the appointment: date, seat, whom it replaced); the County Registrar's list of appointments in lieu of election for $from." . PHP_EOL;
}
echo '   Incumbents at the first election in these records (1995), earlier service not dated; not appointments by inference, so not on the list: ' . implode('; ', array_map(fn($r) => $r[0] . ' (' . $ALLB[$r[1]] . ')', $before)) . PHP_EOL;
echo PHP_EOL . 'FOLLOW-UPS: an election and candidacy records for Hart Trustee Area 2, November 8, 2022 (BOB JENSEN JR 11,638, ANDREW TABAN 5,736), which CEDA omits; this script makes only the holding.' . PHP_EOL;

/* (5) Hart from the roster */
echo PHP_EOL . '(5) HART FROM LEON WORDEN\'S ROSTER (hartschoolboardmembers.htm, sha256 ' . substr($R['meta']['sha256'] ?? '?', 0, 16) . '..., manifest matched: ' . json_encode($R['meta']['manifest_matched'] ?? null)
    . '; ' . ($R['meta']['board_count'] ?? 0) . ' boards, 1945 to 12-11-2013 to 11-30-2014; ' . ($R['meta']['member_count'] ?? 0) . ' members and ' . ($R['meta']['student_member_count'] ?? 0) . ' student members)' . PHP_EOL;
echo '   Linked to a person (' . count($rLink) . '): ' . implode('; ', array_map(fn($mk, $w) => $rm[$mk]['printings'][0] . ' = ' . (is_string($w) ? $whoName($w) . ' (new)' : "#$w " . $whoName($w)), array_keys($rLink), $rLink)) . PHP_EOL;
$hp = array_filter($plan, fn($t) => $t['b'] === $HART);
echo PHP_EOL . '   HART TERMS TO CREATE BY EVIDENCE (' . count($hp) . '; start/end):' . PHP_EOL;
$ev = []; foreach ($hp as $t) { $cls = $t['sev'] === 'certified' ? 'certified' : (($t['sev'] === 'roster' && $t['eev'] === 'roster') ? (isset($t['rosterAgree']) ? 'roster' : 'page') : 'derived');
    $ev[$cls][] = $whoName($t['who']) . ' ' . $t['se'] . ' (' . $t['kind'] . ', ' . $t['sev'] . '/' . ($t['eev'] ?? '-') . (isset($t['rosterAgree']) ? ', roster: ' . $t['rosterAgree'] : '') . ')'; }
foreach (['roster' => 'roster: Leon Worden\'s roster gives start and end (agreeing with CEDA, or the roster alone)', 'page' => 'roster: CEDA\'s win, and the district\'s own board page gives the end (after the roster stops)', 'derived' => 'derived: an end (or more) inferred from statute and the next election, or past the roster', 'certified' => 'certified: the County\'s statement'] as $k => $lab) {
    echo '   ' . $lab . ': ' . count($ev[$k] ?? []) . PHP_EOL . '      ' . implode(PHP_EOL . '      ', $ev[$k] ?? []) . PHP_EOL;
}
echo PHP_EOL . '   CEDA TERMS THE ROSTER SPEAKS TO (' . count($hartAgree) . '; "both" = start and end agree, "start" = the roster stops or the term was cut at a CEDA win):' . PHP_EOL . '      ' . implode(PHP_EOL . '      ', $hartAgree) . PHP_EOL;
echo PHP_EOL . '   ROSTER-ONLY TERMS (' . count($rosterOnly) . '), service CEDA lacks; inside an existing tenure = skipped (#8):' . PHP_EOL;
foreach ($rosterOnly as $t) { $cov = $coveredBy($t['who'], $t['b'], $t['se']);
    printf("      %-24s %-10s -> %-10s %-9s %-9s start %-6s end %-7s %s\n", $whoName($t['who']) . (is_string($t['who']) ? '*' : ''), $t['se'], $t['ee'], $t['sel'] ?? '(blank)', $t['how'], $t['sev'], $t['eev'], $cov ? "skipped, inside #$cov" : 'TO CREATE'); }
echo PHP_EOL . '   DISAGREEMENTS, terms (' . count($hartDis) . '):' . ($hartDis ? PHP_EOL . '      ' . implode(PHP_EOL . '      ', $hartDis) : ' none') . PHP_EOL;
echo '   DISAGREEMENTS AND GAPS, candidacies and roster notes 1995 to 2013 (' . count($candDis) . '; ' . $candAgree . ' checks agree):' . ($candDis ? PHP_EOL . '      ' . implode(PHP_EOL . '      ', $candDis) : ' none') . PHP_EOL;
echo PHP_EOL . '   ROSTER AGAINST EXISTING HART HOLDINGS (#8 keeps them):' . PHP_EOL . '      ' . implode(PHP_EOL . '      ', $tenureCmp) . PHP_EOL;
echo PHP_EOL . '   ROSTER NAMES MATCHING AN EXISTING PERSON WITH NO HART TIE, not linked: ' . ($rNameHit ? implode('; ', array_map(fn($h) => $h[0]['printings'][0] . ' = #' . $h[1] . ' ' . $whoName($h[1]), $rNameHit)) : 'none') . PHP_EOL;
$rv = [];
foreach ($rm as $mk => $m) { if (isset($rLink[$mk])) { continue; }
    foreach ($m['printings'] as $pn) { $a = $parts($pn); if (!$a) { continue; }
        foreach ($allPeople as $pid => $ap) { foreach ($ap['names'] as $nn) { if ($why = $verdict($a, $parts($nn))) { $rv["$mk|$pid"] = "$pn and #$pid {$ap['title']}: $why"; break; } } } } }
echo '   ROSTER NAMES THAT MIGHT BE AN EXISTING PERSON (name-variant check), not linked: ' . ($rv ? PHP_EOL . '      ' . implode(PHP_EOL . '      ', $rv) : 'none') . PHP_EOL;
$fmtM = fn($r) => $r[0]['printings'][0] . ' (' . $r[1] . ' to ' . ($r[2] ?? 'the roster\'s end') . ')';
echo PHP_EOL . '   NOT CREATED, no record (Nathan\'s call):' . PHP_EOL;
$pre = array_filter($rNoRec['pre'], fn($r) => ($r[2] ?? '9999') < '1995-12');
$span = array_filter($rNoRec['pre'], fn($r) => ($r[2] ?? '9999') >= '1995-12');
echo '      pre-1995-only members: ' . count($pre) . PHP_EOL . '         ' . implode('; ', array_map($fmtM, $pre)) . PHP_EOL;
echo '      served before and after 1995 but won no recorded election from 1995: ' . count($span) . ': ' . implode('; ', array_map($fmtM, $span)) . PHP_EOL;
echo '      appointed after 1995, no election win: ' . count($rNoRec['post']) . ': ' . implode('; ', array_map($fmtM, $rNoRec['post'])) . PHP_EOL;

/* HELD BODIES (Nathan, 3 October 2026: "Hold Hart. Add the switch, apply the other six, then rebuild Hart
   from the roster"). Nothing is written for a held body: its holdings, the candidacies on it, and any new
   person whose wins are all on it wait. $HOLD_BODIES and $ONLY_BODIES are set at the top. */
echo PHP_EOL . 'IDEMPOTENCE, the bodies applied on 3 October: ' . count(array_filter($plan, fn($t) => $t['b'] !== $HART)) . ' holdings, '
    . count(array_filter($links, fn($l) => $l[2] !== $HART)) . ' candidacy links, ' . count(array_filter($newP, fn($np) => (bool)array_diff(array_keys($np['bodies']), [$HART]))) . ' people and '
    . count($fixes) . ' corrections still to write (all should be 0); ' . count(array_filter(array_merge($exists, $skipped), fn($t) => $t['b'] !== $HART)) . ' of their terms found already held or covered.' . PHP_EOL;
if ($ONLY_BODIES) {
    $plan = array_values(array_filter($plan, fn($t) => in_array($t['b'], $ONLY_BODIES, true)));
    $links = array_values(array_filter($links, fn($l) => in_array($l[2], $ONLY_BODIES, true)));
    $newP = array_filter($newP, fn($np) => (bool)array_intersect(array_keys($np['bodies']), $ONLY_BODIES));
    if (!in_array($SCVW, $ONLY_BODIES, true)) { $fixes = []; }
    echo 'ONLY: ' . implode(', ', array_map(fn($b) => $ALLB[$b], $ONLY_BODIES)) . '. Writing ' . count($plan) . ' holdings, ' . count($newP) . ' people, ' . count($links) . ' candidacy links.' . PHP_EOL;
}
if ($HOLD_BODIES) {
    $plan = array_values(array_filter($plan, fn($t) => !in_array($t['b'], $HOLD_BODIES, true)));
    $links = array_values(array_filter($links, fn($l) => !in_array($l[2], $HOLD_BODIES, true)));
    $newP = array_filter($newP, fn($np) => (bool)array_diff(array_keys($np['bodies']), $HOLD_BODIES));
    $links = array_values(array_filter($links, fn($l) => isset($newP[$l[1]]) || isset($l[3])));
    echo PHP_EOL . 'HELD, not written this run: ' . implode(', ', array_map(fn($b) => $ALLB[$b], $HOLD_BODIES)) . '. Writing ' . count($plan) . ' holdings, ' . count($newP) . ' people, ' . count($links) . ' candidacy links.' . PHP_EOL;
    $byB = []; foreach ($plan as $t) { $byB[$ALLB[$t['b']]] = ($byB[$ALLB[$t['b']]] ?? 0) + 1; } echo '   holdings by body: ' . json_encode($byB) . PHP_EOL;
}
/* McKEON'S HART TENURE (#26978), corrected from Leon's roster when Hart is written (Nathan, 3 October 2026:
   "Yes, correct McKeon to resigned, 7 December 1987. Leon's roster is primary"). The roster: "McKeon (elected
   3-6-1979 to complete Wullschleger's unexpired term)" and "McKeon (resigned eff. 12-7-1987 upon election to
   Santa Clarita City Council)". The start gains the same line's precision. */
$mcFix = null;
if (in_array(21588, $ONLY_BODIES, true)) {
    $mc = $get(26978); $roster = json_encode(json_decode(file_get_contents("$root/inventory/legacy/hart-board-roster.json"), true), JSON_UNESCAPED_UNICODE);
    $q1 = "McKeon (elected 3-6-1979 to complete Wullschleger's unexpired term)"; $q2 = 'McKeon (resigned eff. 12-7-1987 upon election to Santa Clarita City Council)';
    if (!$mc || $mc->holdingPerson->one()?->id !== 18791 || $mc->holdingBody->one()?->id !== 21588) { $bad[] = '#26978 is not McKeon\'s Hart holding'; }
    elseif (!str_contains($roster, $q1) || !str_contains($roster, $q2)) { $bad[] = 'the roster does not read the McKeon lines'; }
    elseif ($mc->termEndEdtf !== '1987-12-07') {
        $mcFix = ['q1' => $q1, 'q2' => $q2];
        echo PHP_EOL . "CORRECTION #26978 McKeon, Hart: {$mc->termStartEdtf} to {$mc->termEndEdtf} ({$mc->howEnded}) -> 1979-03-06 (elected) to 1987-12-07 (resigned), roster evidence" . PHP_EOL;
    }
}
echo PHP_EOL . 'SUMMARY: ' . count($newP) . ' people, ' . count($plan) . ' holdings, ' . count($skipped) . ' skipped as covered, ' . count($links) . ' candidacies linked, ' . count($fixes) . ' correction.' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ------------------------------------------------ apply, in one transaction */
$tx = Craft::$app->getDb()->beginTransaction();
try {
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
    $ids = [];
    foreach ($newP as $k => $np) {
        $have = Entry::find()->section('persons')->status(null)->title($np['title'])->one();
        if ($have) { $ids["new:$k"] = $have->id; continue; }
        $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $np['title'];
        $p->setFieldValues(['fullName' => $np['title'], 'personAliases' => implode("\n", $np['aliases']), 'roles' => $np['roles'], 'recordProvenance' => $pProv] + ($np['era'] ? ['historicalEra' => [$np['era']]] : []));
        if (!$elements->saveElement($p)) { throw new \RuntimeException("person {$np['title']}: " . json_encode($p->getFirstErrors())); }
        $ids["new:$k"] = $p->id;
    }
    $pidOf = fn($w) => is_string($w) ? $ids[$w] : $w;
    foreach ($links as $l) { [$cid, $k] = $l;
        $c = $get($cid); if ($c->candidacyPerson->status(null)->ids()) { continue; }
        $c->setFieldValue('candidacyPerson', [$l[3] ?? $ids["new:$k"]]);
        if (!$elements->saveElement($c)) { throw new \RuntimeException("candidacy #$cid"); }
    }
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($plan as $t) {
        $pid = $pidOf($t['who']);
        if (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $pid, 'field' => 'holdingPerson'], ['targetElement' => $t['b'], 'field' => 'holdingBody']])->termStartEdtf($t['se'])->exists()) { continue; }
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($ot->id);
        $v = ['holdingPerson' => [$pid], 'holdingOffice' => [$isWater($t['b']) ? $RW : $RS], 'holdingBody' => [$t['b']], 'holdingDistrict' => $t['place'] ? [$t['place']] : [],
            'seatLabel' => $t['area'] !== '' ? ($isWater($t['b']) ? 'Division ' : 'Trustee Area ') . $t['area'] : '',
            'termStart' => $t['sp'], 'termStartEdtf' => $t['se'], 'howEnded' => $t['how'], 'startEvidence' => $t['sev'], 'footnotes' => $fn($t['notes']), 'recordProvenance' => $hProv($t)];
        if ($t['sel']) { $v['selectionMethod'] = $t['sel']; }
        if ($t['ee']) { $v['termEnd'] = $t['ep']; $v['termEndEdtf'] = $t['ee']; if ($t['eev']) { $v['endEvidence'] = $t['eev']; } }
        $h->setFieldValues($v);
        if (!$elements->saveElement($h)) { throw new \RuntimeException('holding ' . $whoName($t['who']) . ' ' . $t['se'] . ': ' . json_encode($h->getFirstErrors())); }
    }
    foreach ($fixes as [$id]) {
        $h = $get($id);
        $rows = array_map(fn($r) => ['number' => $r['number'] ?? '', 'note' => $r['note'] ?? '', 'source' => $r['source'] ?? ''], $h->footnotes ?? []);
        $rows[] = ['number' => (string)(count($rows) + 1), 'note' => "Her successor took the seat in January 2023: the agency's directors take office in January after a November election, as its list of directors gives their terms ($C_SCVL).", 'source' => 'editorial-2026'];
        $h->setFieldValues(['termEnd' => 'January 2023', 'termEndEdtf' => '2023-01', 'endEvidence' => 'derived', 'footnotes' => $rows]);
        if (!$elements->saveElement($h)) { throw new \RuntimeException("fix #$id"); }
    }
    if ($mcFix) {
        $mc = $get(26978);
        $rows = array_map(fn($r) => ['number' => $r['number'] ?? '', 'note' => $r['note'] ?? '', 'source' => $r['source'] ?? ''], $mc->footnotes ?? []);
        $rows[] = ['number' => (string)(count($rows) + 1), 'note' => 'Leon Worden, William S. Hart Union High School District Governing Board Members, SCVHistory.com, /scvhistory/hartschoolboardmembers.htm: "' . $mcFix['q1'] . '" and "' . $mcFix['q2'] . '"', 'source' => 'editorial-2026'];
        $mc->setFieldValues(['termStart' => 'March 6, 1979', 'termStartEdtf' => '1979-03-06', 'termEnd' => 'December 7, 1987', 'termEndEdtf' => '1987-12-07', 'selectionMethod' => 'elected', 'howEnded' => 'resigned', 'startEvidence' => 'roster', 'endEvidence' => 'roster', 'footnotes' => $rows]);
        if (!$elements->saveElement($mc)) { throw new \RuntimeException('#26978: ' . json_encode($mc->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $e) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $e->getMessage() . PHP_EOL; throw $e; }

/* Read back. */
$short = [];
foreach ($plan as $t) { $pid = $pidOf($t['who']);
    if (!Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $pid, 'field' => 'holdingPerson'], ['targetElement' => $t['b'], 'field' => 'holdingBody']])->termStartEdtf($t['se'])->exists()) { $short[] = $whoName($t['who']) . ' ' . $t['se']; } }
foreach ($links as $l) { [$cid, $k] = $l; if (($get($cid)->candidacyPerson->status(null)->ids()[0] ?? null) !== ($l[3] ?? $ids["new:$k"])) { $short[] = "link #$cid"; } }
if ($fixes && $get(28219)->termEndEdtf !== '2023-01') { $short[] = 'Plambeck end'; }
if ($mcFix && ($get(26978)->termEndEdtf !== '1987-12-07' || (string)$get(26978)->howEnded !== 'resigned')) { $short[] = 'McKeon #26978'; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . ' holdings, ' . count($newP) . ' people, ' . count($links) . ' links, ' . count($fixes) . ' correction') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('derive_board_holdings.php', count($plan) + count($newP) + count($links) + count($fixes), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'a holding for every school and water board term, on the 16 board-term decisions');
if ($short) { throw new \RuntimeException('derive_board_holdings: ' . implode('; ', $short)); }
