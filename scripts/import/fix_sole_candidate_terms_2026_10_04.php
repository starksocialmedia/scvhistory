/**
 * One wording, one selection method and the right statute for every office term filled because the nominees did
 * not outnumber the seats, so that no election was held (inventory/review/council-transition-2026-10-04.md,
 * sections 5 and 6 and "What in the archive is wrong").
 *
 * Nathan's decisions, 4 October 2026:
 *  - One wording for every such term: "Appointed as the sole candidate; no election held" (where there were several
 *    nominees for several seats: "Appointed without an election; the candidates did not outnumber the seats"). "This
 *    follows the statute's verb ("appoint") and states both facts."
 *  - "Split each run of terms that contains such a term ... at that term, so the in-lieu term is its own holding with
 *    the right selection method, and the terms before and after keep theirs. Use each body's actual term boundaries
 *    ... do not guess: where a boundary is not documented, keep the year precision the sources give and say so in
 *    the footnote."
 *  - The school-board footnotes that cite Elections Code 10515 cite Education Code 5326 and 5328 instead, with the
 *    statute sentence in the report. Section 10515 is the Uniform District Election Law; school boards fall under the
 *    Education Code, a city council under Elections Code 10229.
 *  - The variant phrasings ("returned without a contest", "filled without a vote", "no vote was taken", "Appointed in
 *    lieu of election; no contest was held") retire in favour of the single wording and the report's template: the
 *    County list's own line quoted, and the statute sentence by kind of body.
 *
 * WHAT IT DOES
 *  0. selectionMethod gains "sole-candidate", labelled "Appointed as the sole candidate; no election held". ERRORLOG
 *     (4 October 2026): a schema save followed by element saves in one request can fail validation and leave project
 *     config unwritten. So an apply run that has to add the option adds it, re-saves the field so project config is
 *     written, and stops ("run again"); values are set only on a run that finds the option already there, in the
 *     field and in project config.
 *  1. FIX: holdings that are one term already. The report's group A (the term filled without an election) get
 *     "sole-candidate" and the template footnote; the 10515 sentence goes. Group C holdings, whose footnotes speak of
 *     how the term ended, get the single wording in place of the variants and of the "whether X continued is not
 *     known" note that contradicted them. Two undated "present term" holdings that group C shows to be in lieu
 *     (DeFigueiredo, Sulphur Springs Area 2, from 2020; MacDonald, Area 5, from 2018) get the method, the start the
 *     earlier term's end gives (startEvidence "derived", filled only where empty) and the evidence.
 *  2. SPLIT: the report's group B runs, and Garibay's (group C, Love's footnote: the 2024 Area 1 unexpired term), cut
 *     at each change of method. The original record keeps its id as the first segment; the others are new records
 *     with the same person, office and body. Every footnote of the original goes to the segment it is about, or is
 *     replaced by the new wording; an original footnote the plan does not account for refuses the run.
 *
 * TERM BOUNDARIES. School terms run from December after the election (Education Code 5017; decision 1 of
 * inventory/review/board-terms-decisions-2026-10-03.md), and the move from odd to even years follows decision 3
 * there (2013 classes to December 2018; the 2015 classes, elected by trustee area, to December 2020, the next
 * election for those areas on the County's 2020 list). The Newhall County Water District's 2011 term is written to
 * the year: the only source, Leon Worden's article of 16 November 2011, gives "four more years" and the day the
 * supervisors certified the appointment, not the day the term began.
 *
 * Where a run's later terms were also filled without an election (Pearson 2018 and 2022, Weinstein 2020 and 2024,
 * Solomon 2020), they are in the in-lieu segment, on the County's lists and a source naming the person; Solomon's
 * 2024 term (contested, CEDA) and Trunkey's 2018 and 2022 terms (contested, CEDA) are elected; Messina's 2018 term
 * (contested, CEDA) is elected. Terms before the County lists held here (2007, 2011) are not split; see the report.
 *
 * Idempotent: every value is compared with what is there and a record is saved only when something differs. A second
 * run finds the new segments by person, body, start and this script's name in recordProvenance and is a no-op.
 * Dry run by default. Set $APPLY = true to write. Writes project config (an option).
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_sole_candidate_terms_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$SCRIPT = 'fix_sole_candidate_terms_2026_10_04.php';
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fs = Craft::$app->getFields();
$SM = 'sole-candidate'; $SM_LABEL = 'Appointed as the sole candidate; no election held';

/* ---------- sources ---------- */
$RR = 'County of Los Angeles, Registrar-Recorder/County Clerk';
$L = [ /* the County's final lists of cancelled elections: [citation, the list's heading in sentence case] */
    2013 => ["$RR, \"Local and Municipal Consolidated Elections, November 5, 2013: Final List, Cancelled Elections (Appointment in Lieu of Election Because of an Insufficient Number of Candidates)\", https://www.lavote.gov/Documents/Election_Info/11052013_canc_elec.pdf", 'Appointment in lieu of election because of an insufficient number of candidates'],
    2015 => ["$RR, \"Local and Municipal Consolidated Elections, November 3, 2015: Final List of Cancelled Elections (Appointment in Lieu of Election Due to Insufficiency of Candidates)\", https://www.lavote.gov/Documents/Election_Info/11032015-CancelledElections.pdf", 'Appointment in lieu of election due to insufficiency of candidates'],
    2016 => ["$RR, \"General Election, November 8, 2016: Final List of Cancelled Elections, Appointment in Lieu of Election Due to Insufficiency of Candidates\", https://www.lavote.gov/Documents/Election_Info/11082016-Cancelled-Elections.pdf", 'Appointment in lieu of election due to insufficiency of candidates'],
    2018 => ["$RR, \"General Election November 6, 2018, Final List of Cancelled Elections: Appointment in Lieu of Election Due to Insufficiency of Candidates\", https://www.lavote.gov/docs/rrcc/Election-Info/11082018_final-list-cancelled-elections.pdf", 'Appointment in lieu of election due to insufficiency of candidates'],
    2020 => ["$RR, \"General Election November 3, 2020, Final List of Cancelled Elections: Appointment in Lieu of Election Due to Insufficiency of Candidates\", https://www.lavote.gov/docs/rrcc/election-info/11032020_cancelled-elections.pdf", 'Appointment in lieu of election due to insufficiency of candidates'],
    2022 => ["$RR, \"General Election November 8, 2022, Final List of Cancelled Elections: Appointment in Lieu of Election Due to Insufficiency of Candidates\", https://www.lavote.gov/docs/rrcc/election-info/11082022_final-list-of-cancelled-elections.pdf", 'Appointment in lieu of election due to insufficiency of candidates'],
    2024 => ["$RR, \"General Election November 5, 2024, Final List of Cancelled Elections: Appointment in Lieu of Election Due to Insufficiency of Candidates\", https://content.lavote.gov/docs/rrcc/documents/cancelled-elections-november-2024.pdf", 'Appointment in lieu of election due to insufficiency of candidates'],
];
$N = [ /* the County's lists of candidates whose names would not appear on the ballot, which name the appointees */
    2013 => "$RR, \"Local and Municipal Consolidated Elections, November 5, 2013: Final List of Qualified Candidates Whose Name Will Not Appear on the Ballot\", August 20, 2013, https://www.lavote.gov/Documents/Election_Info/11052013_final_list_of_qualified_candidates_whose_name_will_not_appear_on_the_ballot.pdf",
    2015 => "$RR, \"Local and Municipal Consolidated Elections, November 3, 2015: Final List of Qualified Candidates Whose Name Will Not Appear on the Ballot\", September 1, 2015, https://www.lavote.gov/Documents/Election_Info/11032015_CandidatesNotOnBallot.pdf",
    2016 => "$RR, \"2016 General Election: Final List of Qualified Candidates Whose Name Will Not Appear on the Ballot\", September 2, 2016, https://www.lavote.gov/Documents/Election_Info/11082016-final-list-qualified-candidates-who-will-not-appear-on-the-ballot.pdf",
];
$SIG18 = 'The Signal, "Few challengers in school board races," August 13, 2018, https://web.archive.org/web/20180813192749/https://signalscv.com/2018/08/few-challengers-in-school-board-races/';
$SCVN20 = 'Raychel Stewart, "School Board Elections in 3 SCV Districts Canceled," SCVNews.com, 2 October 2020, https://scvnews.com/school-board-elections-in-3-scv-districts-canceled';
$SIG24 = 'Tyler Wainfeld, The Signal, "Candidates for SCV elementary school boards make their cases," October 31, 2024, https://web.archive.org/web/20241106214109/https://signalscv.com/2024/10/candidates-for-scv-elementary-school-boards-make-their-cases/';
$BURK21 = '"CUSD Board Appoints Mayreen Burk as New Board President," SCVNews.com, 15 January 2021, https://scvnews.com/cusd-board-appoints-mayreen-burk-as-new-board-president/';
$CASTAIC18 = 'Perry Smith, The Signal, "Castaic Union to hold elections for four seats in November", April 16, 2018, https://signalscv.com/2018/04/castaic-union-to-hold-elections-for-four-seats-in-november/';
$PLAMBECK11 = 'Leon Worden, SCVNews.com, "4 More Years for Plambeck, Mortensen on Water Board", November 16, 2011, https://scvnews.com/4-more-years-for-plambeck-mortensen-on-water-board/';
$CITY_NOTICE = 'City Clerk, City of Santa Clarita, "Notice That There Are Not More Candidates Than Offices To Be Elected," 9 August 2024, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2024/08/Combined-Notice-That-There-Are-Not-More-Candidates-Than-Offices-To-Be-Electe.pdf';
$CITY_LIST = 'City Clerk, City of Santa Clarita, "List of Qualified Candidates," 10 August 2024, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2024/08/List-of-Qualified-Candidates-Memo.pdf';
$SIG_GIBBS = 'The Signal, "Council names Gibbs to District 3 seat," August 2024, https://web.archive.org/web/2025id_/https://signalscv.com/2024/08/council-names-gibbs-to-district-3-seat';
$HART_PAGE = 'William S. Hart Union High School District, "Governing Board Member Info," https://www.hartdistrict.org/apps/pages/governing-board-members, read 4 October 2026';

/* ---------- the wording (report, section 6, with Nathan's lead sentence) ---------- */
$ONE = 'Appointed as the sole candidate; no election held.';
$SEV = 'Appointed without an election; the candidates did not outnumber the seats.';
$ED = 'Under Education Code sections 5326 and 5328, when the election is not held the nominee "shall be seated at the organizational meeting of the board ... as if elected at a district election."';
$UDEL = 'Under Elections Code section 10515 the supervising authority appoints the persons who filed, and the appointee "shall qualify and take office and serve exactly as if elected at a general district election for the office."';
$CITY = 'The City Council appointed him on 19 August 2024 under Elections Code section 10229(a)(1), which lets the council "Appoint to the office the person who has been nominated"; the appointee "shall qualify and take office and serve exactly as if elected at a municipal election for the office."';
$lists = fn(int $y, string $line, string $what = 'the seat') => sprintf('The County lists %s among its "%s" (%s): "%s".', $what, $L[$y][1], $L[$y][0], $line);
$named = fn(int $y, string $who) => "Its list of the candidates whose names would not appear on the ballot names $who ({$N[$y]}).";

$CA13 = "$SEV The three incumbents, Victor M. Torres, Susan M. Christopher and Laura L. Pearson, were the only qualified candidates for the district's three at-large seats, so the election of 5 November 2013 was not held. " . $lists(2013, 'Castaic Union', 'the district') . ' ' . $named(2013, 'the three') . " $ED";
$HA13 = "$SEV Bob Jensen and Joe Messina, the incumbents, and Chris A. Fall, an appointed incumbent, were the only qualified candidates for the district's three at-large seats, so the election of 5 November 2013 was not held. " . $lists(2013, 'William S. Hart Union High', 'the district') . ' ' . $named(2013, 'the three') . " $ED";
$CA15 = fn(string $names) => "$SEV $names, neither of them an incumbent, were the only qualified candidates for the district's two at-large seats, so the election of 3 November 2015 was not held. " . $lists(2015, 'Castaic Union School District', 'the district') . ' ' . $named(2015, 'the two') . " $ED";
$SS15 = fn(string $name, string $area) => "$ONE $name, the incumbent, was the only qualified candidate for Trustee Area $area, so the election of 3 November 2015 for the seat was not held. " . $lists(2015, 'Sulphur Springs Union School (Trustee Area # 1 and 2)') . ' ' . $named(2015, "$name, the incumbent, for Trustee Area $area") . " $ED";
$NW15 = fn(string $name, string $area) => "$ONE $name, the incumbent, was the only qualified candidate for Trustee Area $area, so the election of 3 November 2015 for the seat was not held. " . $lists(2015, 'Newhall School District (Trustee Area # 4 and 5)') . ' ' . $named(2015, "$name, the incumbent, for Trustee Area $area") . " $ED";
$SS20 = $lists(2020, 'Sulphur Springs Union Elementary (Trustee Area 1, 2, and 3 U/T ending 12/22)') . " SCVNews reported that \"Shelley Weinstein (Area No. 1); Denis DeFigueiredo (Area No. 2); and Paola Jellings (Area No. 3) all are expected to remain on the board\" ($SCVN20). The same report says that \"no one filed to run for the three seats\"; read with the sentence naming the three, that means no one filed against them, though it does not say so in terms.";
$SS24 = "\"Both seats up for election in the Sulphur Springs Union School District are uncontested, as incumbents Rochelle Weinstein and Denis Defigueiredo are seeking to continue to represent areas 1 and 2, respectively\" ($SIG24). " . $lists(2024, 'Sulphur Springs Union School (Trustee Area 1 and 2)');
$SAUGUS24 = $lists(2024, 'Saugus Union School (Trustee Area 4 and 1 U/T ending 12/26)');
$ALL6 = "The Signal reported that \"All six candidates in the Castaic Union and Sulphur Springs school districts will claim their seats without the need for an election due to a lack of challengers\" ($SIG18).";

/* Notes: a string is new text; ['k' => '...'] keeps the existing note that contains that text, verbatim. */
$k = fn(string $s) => ['k' => $s];

/* ---------- 1. FIX: one-term holdings ---------- */
$FIX = [
    /* group A */
    ['id' => 27410, 'person' => 23091, 'body' => 394, 'group' => 'A', 'sm' => $SM,
     'notes' => [$k("The term's end is derived. The City lists him"),
        "$ONE Jason Gibbs was the only qualified candidate for District 3 at the close of nominations on 9 August 2024, so the election of 5 November 2024 for the seat was not held ($CITY_NOTICE; and $CITY_LIST). " . $lists(2024, 'Santa Clarita (Council District 3)') . " $CITY ($SIG_GIBBS)."],
     'drop' => ['so no vote was taken and he was appointed to the seat']],
    ['id' => 28489, 'person' => 28358, 'body' => 21592, 'group' => 'A', 'sm' => $SM,
     'notes' => ["$ONE Matthew Watson, the incumbent, was the only candidate for Trustee Area 4, so the election of 5 November 2024 for the seat was not held: \"incumbents Patti Garibay and Matt Watson, the current board president, are looking to once again represent areas 1 and 4, respectively\", in \"uncontested races\" ($SIG24). $SAUGUS24 $ED",
        $k("The district's board page lists Matthew Watson for Trustee Area 4"), $k('California Education Code, section 5017')],
     'drop' => ['the seat was filled without a vote. Elections Code, section 10515']],
    ['id' => 28889, 'person' => 28868, 'body' => 21592, 'group' => 'A', 'sm' => $SM,
     'notes' => ["$ONE David C. Powell, the appointed incumbent, was the only qualified candidate for Trustee Area 4, so the election of 8 November 2016 for the seat was not held. " . $lists(2016, 'Saugus Union School District (Trustee Areas 4 and 5 U/T ending 12/18)') . ' ' . $named(2016, 'David C. Powell, "Appointed Incumbent", for it') . " $ED",
        $k("The County's list gives David C. Powell as the appointed incumbent"), $k('David Powell did not stand at the next election'), $k('California Education Code, section 5017')],
     'drop' => ['Appointed in lieu of election; no contest was held', 'Elections Code, section 10515: where no more candidates']],
    ['id' => 28887, 'person' => 28866, 'body' => 21596, 'group' => 'A', 'sm' => $SM,
     'notes' => [$CA15('Michael Owen Lambarth and Stacy Dobbs'), $k('this term, due to end in December 2019, ran to December 2020'), $k('Whether Michael Owen Lambarth continued is not known'), $k('California Education Code, section 5017'), $k('Lambarth gave notice of his resignation')],
     'drop' => ['Appointed in lieu of election; no contest was held', 'Elections Code, section 10515: where no more candidates']],
    ['id' => 28885, 'person' => 28864, 'body' => 21596, 'group' => 'A', 'sm' => $SM,
     'notes' => [$CA15('Stacy Dobbs and Michael Owen Lambarth'), $k('Castaic Governing Board elects new president, clerk'), $k('this term, due to end in December 2019, ran to December 2020'), $k('Whether Stacy Dobbs continued is not known'), $k('California Education Code, section 5017'), $k('previously filled by Stacy Dobbs before Dobbs resigned')],
     'drop' => ['Appointed in lieu of election; no contest was held', 'Elections Code, section 10515: where no more candidates']],
    ['id' => 28883, 'person' => 28330, 'body' => 21594, 'group' => 'A', 'sm' => $SM,
     'notes' => [$SS15('Denis F. DeFigueiredo', '2'), $k('this term, due to end in December 2019, ran to December 2020'),
        "The seat was next due for election on 3 November 2020, and that election was not held either: Denis DeFigueiredo continued in Trustee Area 2 (see the next term). Appointed as the sole candidate; no election held. $SS20",
        $k('California Education Code, section 5017')],
     'drop' => ['Appointed in lieu of election; no contest was held', 'Elections Code, section 10515: where no more candidates', 'Whether Denis DeFigueiredo continued is not known', 'Returned without a contest in 2020']],
    ['id' => 28881, 'person' => 28376, 'body' => 21596, 'group' => 'A', 'sm' => $SM,
     'notes' => [$CA13, $k('Huffaker Takes Helm of Castaic School Board'), $k('this term, due to end in December 2017, ran to December 2018'), $k('Whether Victor Torres continued is not known'), $k('California Education Code, section 5017'), $k('CUSD Makes Provisional Appointment for Vacancy')],
     'drop' => ['Appointed in lieu of election; no contest was held', 'Elections Code, section 10515: where no more candidates']],
    ['id' => 28580, 'person' => 28322, 'body' => 21588, 'group' => 'A', 'sm' => $SM,
     'notes' => [$HA13, $k('California Education Code, section 5017'), $k('relected/unopposed 2013')],
     'drop' => ['No contest for the seat is recorded at the election of 2013: the seat was filled without a vote', 'Appointed in lieu of election; no contest was held']],
    ['id' => 28497, 'person' => 28330, 'body' => 21594, 'group' => 'A', 'sm' => $SM,
     'notes' => ["$SEV No contest for the seat is recorded at the election of 6 November 2007, so it is taken to have been filled without one; the County's list of the elections it cancelled that year is not held, so this rests on the missing contest alone. $ED",
        $k('had won the seat in 2003 and stood as the incumbent in 2011'), $k('California Education Code, section 5017')],
     'drop' => ['No contest for the seat is recorded at the election of 2007: the seat was filled without a vote']],
    /* group C: how the term ended */
    ['id' => 28543, 'person' => 28376, 'body' => 21596, 'group' => 'C',
     'notes' => [$k('Elected on November 3, 2009, first of 5 candidates'),
        "The seat was next due for election on 5 November 2013, which was not held. $SEV The County cancelled the election for the district's three at-large seats, and its list of the candidates whose names would not appear on the ballot names Victor M. Torres, the incumbent, among the three, so he continued in the seat ({$L[2013][0]}; and {$N[2013]}).",
        $k('California Education Code, section 5017')],
     'drop' => ['he continued in the seat, appointed in lieu of election']],
    ['id' => 28499, 'person' => 28330, 'body' => 21594, 'group' => 'C',
     'notes' => [$k('Elected on November 8, 2011, second of 3 candidates'),
        "The term ended in December 2015. The district's next election, on 3 November 2015, was by trustee area, and it was not held for Trustee Area 2. $ONE The County cancelled it for Trustee Areas 1 and 2, and its list of the candidates whose names would not appear on the ballot names Denis F. DeFigueiredo, the incumbent, for Trustee Area 2, so he continued ({$L[2015][0]}; and {$N[2015]}).",
        $k('California Education Code, section 5017')],
     'drop' => ['he continued, appointed in lieu of election']],
    ['id' => 28487, 'person' => 28358, 'body' => 21592, 'group' => 'C',
     'notes' => [$k('Elected on November 3, 2020, first of 2 candidates for Trustee Area 4'),
        "The seat was next due for election on 5 November 2024, which was not held: Matthew Watson continued in the seat, as the district's board page shows. Appointed as the sole candidate; no election held. $SAUGUS24",
        $k('California Education Code, section 5017')],
     'drop' => ['the County lists it among the seats filled without a vote']],
    ['id' => 28503, 'person' => 28356, 'body' => 21594, 'group' => 'C',
     'notes' => [$k('Elected on November 5, 2013, second of 4 candidates'), $k('this term, due to end in December 2017, ran to December 2018'),
        "The seat was next due for election on 6 November 2018, which was not held for this board. $ONE " . $lists(2018, 'Sulphur Springs Union School (Trustee Areas 3, 4, and 5)', 'the board') . " $ALL6 Lori MacDonald was one of them: the district says she has served on the board since 2012, and she holds Trustee Area 5 now (see the next term).",
        $k('California Education Code, section 5017')],
     'drop' => ['Whether Lori Macdonald continued is not known', 'Returned without a contest in 2018']],
    ['id' => 28501, 'person' => 28348, 'body' => 21594, 'group' => 'C',
     'notes' => [$k('Elected on November 5, 2013, first of 4 candidates'), $k('this term, due to end in December 2017, ran to December 2018'),
        "The seat was next due for election on 6 November 2018, which was not held for this board: each seat went to its only candidate. Appointed as the sole candidate; no election held. " . $lists(2018, 'Sulphur Springs Union School (Trustee Areas 3, 4, and 5)', 'the board') . " $ALL6 Whether Ken Chase was one of them is not stated in any source held here; he holds Trustee Area 4 now.",
        $k('California Education Code, section 5017')],
     'drop' => ['Whether Ken Chase continued is not known', 'Returned without a contest in 2018']],
    /* group C: present terms that are in lieu (start filled only where empty) */
    ['id' => 29024, 'person' => 28330, 'body' => 21594, 'group' => 'C', 'sm' => $SM,
     'fill' => ['termStart' => 'December 2020', 'termStartEdtf' => '2020-12', 'startEvidence' => 'derived'],
     'notes' => [$k('Denis DeFigueiredo Denis DeFigueiredo Board Member Trustee Area No. 2'), $k("The district's page gives no years for the present term."),
        "$ONE The election of 3 November 2020 for Trustee Area 2 was not held. $SS20 The term began in December 2020, at the board's organizational meeting, where his term of 2015 ended. $ED",
        "The election of 5 November 2024 for the seat was not held either: $SS24 For the term to December 2028, again: appointed as the sole candidate; no election held."],
     'drop' => []],
    ['id' => 29026, 'person' => 28356, 'body' => 21594, 'group' => 'C', 'sm' => $SM,
     'fill' => ['termStart' => 'December 2018', 'termStartEdtf' => '2018-12', 'startEvidence' => 'derived'],
     'notes' => [$k('Lori MacDonald Lori MacDonald Board Member Trustee Area No. 5'), $k('has been honored to serve on the Sulphur Springs Union School Board since 2012'),
        "$ONE Her at-large term of 2013 ran to December 2018, and the election of 6 November 2018 for the board's seats was not held. " . $lists(2018, 'Sulphur Springs Union School (Trustee Areas 3, 4, and 5)', 'the board') . " $ALL6 The district says she has served on the board since 2012, so she was one of them. Which trustee area she took in 2018 is not recorded here. $ED",
        "The election of 8 November 2022 for Trustee Area 5, the seat she holds, was not held either. " . $lists(2022, 'Sulphur Springs School (Trustee Area 3, 4, and 5)', 'the board') . " For the term to December 2026, again: appointed as the sole candidate; no election held."],
     'drop' => []],
];

/* ---------- 2. SPLIT: runs cut at each change of method ---------- */
/* 'from' is the original's state before the split (checked); segments[0] keeps the original id. */
$seg = fn(array $s, array $e, string $sm, string $how, string $se, ?string $ee, string $seat, array $dist, array $notes) => compact('s', 'e', 'sm', 'how', 'se', 'ee', 'seat', 'dist', 'notes');
$D = fn(string $y) => ["December $y", "$y-12"];
$SPLIT = [
    ['id' => 28245, 'person' => 26549, 'body' => 21588, 'name' => 'Joe Messina', 'from' => ['December 2009', '2009-12', 'December 2022', '2022-12', 'elected'],
     'segments' => [
        $seg($D('2009'), $D('2013'), 'elected', 'reelected', 'retrospective', 'certified', '', [], [$k('When he was elected in 2009, Messina promised'), $k('He assumed office in 2009.'),
            "The term ended in December 2013. The election of 5 November 2013 for the district's three at-large seats was not held (see the next term). Appointed without an election; the candidates did not outnumber the seats."]),
        $seg($D('2013'), $D('2018'), $SM, 'reelected', 'certified', 'derived', '', [], [$HA13, $k('The election has been canceled and will not appear on the official ballot'), $k('an oath of office will be administered Dec. 11'), $k('Chris Fall Quits Hart Board Due to Business Conflict'),
            "The district moved its elections from odd to even years, and the terms won in 2013, due to end in December 2017, ran to December 2018 ($SIG18: the districts \"moved its elections from odd-number years to even-number years\"). He stood again, as the incumbent, at the election of 6 November 2018."]),
        $seg($D('2018'), $D('2022'), 'elected', 'reelected', 'roster', 'certified', 'Trustee Area 5', [25323], [
            "Elected on 6 November 2018 for Trustee Area 5, with 10,236 votes (California Elections Data Archive (CEDA), a compilation of the County's returns), against Jeff Martin and Kelly Trunkey: \"Incumbent Joe Messina will be challenged by Jeff Martin and Kelly Trunkey\" ($SIG18).",
            "The district's page gives his present term as 2022 to 2026, for Trustee Area 5 ($HART_PAGE: \"Trustee Area No. 5 representative President\"; \"current term 2022 - 2026\"), so this term ends at that one's start."]),
     ],
     'drop' => ['Joe Messina Trustee Area No. 5 representative President"', '"current term 2022 - 2026"', 'among the appointees for the district\'s three at-large seats, with Bob Jensen and Chris A. Fall', 'It covers his at-large terms of 2009 and 2013']],
    ['id' => 28247, 'person' => 25381, 'body' => 21596, 'name' => 'Susan Christopher', 'from' => ['December 2009', '2009-12', 'December 2018', '2018-12', 'elected'],
     'segments' => [
        $seg($D('2009'), $D('2013'), 'elected', 'reelected', 'retrospective', 'certified', '', [], [$k('She has been a CUSD board member since 2009'),
            "The term ended in December 2013. The election of 5 November 2013 for the district's three at-large seats was not held (see the next term). Appointed without an election; the candidates did not outnumber the seats."]),
        $seg($D('2013'), $D('2018'), $SM, 'expired', 'certified', 'retrospective', '', [], [$CA13, $k('Huffaker replaced Christopher as president'), $k('who will have served nine years on the board'), $k('this term was actually extended so we could move our elections'), $k('has decided not to run for re-election for her seat in Area D')]),
     ],
     'drop' => ['names Susan M. Christopher, the incumbent, among the appointees']],
    ['id' => 28241, 'person' => 25379, 'body' => 21596, 'name' => 'Laura Pearson', 'from' => ['December 2005', '2005-12', '', '', 'elected'],
     'segments' => [
        $seg($D('2005'), $D('2013'), 'elected', 'reelected', 'retrospective', 'certified', '', [], [$k('first elected to the board in 2005'),
            "The term ended in December 2013. The election of 5 November 2013 for the district's three at-large seats was not held (see the next term). Appointed without an election; the candidates did not outnumber the seats."]),
        $seg($D('2013'), $D('2018'), $SM, 'reelected', 'certified', 'derived', '', [], [$CA13, $k('who were unopposed in this year\'s school board elections'), $k('Steve Teeman, replacing Pearson as clerk'), $k('elected Laura Pearson as its new president and Stacy Dobbs'),
            "The district moved its elections from odd to even years, and the terms won in 2013 ran to December 2018: \"this term was actually extended so we could move our elections to even-yeared terms\", as Susan Christopher, elected with her, put it ($CASTAIC18)."]),
        $seg($D('2018'), ['', ''], $SM, 'serving', 'derived', null, 'Trustee Area B', [25357], [
            "$ONE The election of 6 November 2018 for the board's seats was not held. " . $lists(2018, 'Castaic Union School (Trustee Area B, D, E, and C [U/T ending 12/20])', 'the board') . " $ALL6 She stayed on the board for Trustee Area B: in January 2021 \"Laura Pearson, representative for Trustee Area B, was appointed as district clerk\" ($BURK21). $ED",
            "The election of 8 November 2022 for Trustee Area B was not held either. " . $lists(2022, 'Castaic Union School (Trustee Area B and D)') . " Again: appointed as the sole candidate; no election held.",
            $k('Laura Pearson, President Trustee Area B Term: November 2022 to December 2026')]),
     ],
     'drop' => ['names Laura L. Pearson, the incumbent, among the appointees']],
    ['id' => 28239, 'person' => 25385, 'body' => 21590, 'name' => 'Suzan Solomon', 'from' => ['December 1999', '1999-12', '', '', 'elected'],
     'segments' => [
        $seg($D('1999'), $D('2015'), 'elected', 'reelected', 'retrospective', 'certified', '', [], [$k('Mrs. Solomon was first elected to the Newhall School Board in 1999'), $k('14-year veteran Suzan Solomon'),
            "The term ended in December 2015. The election of 3 November 2015, the district's first by trustee area, was not held for Trustee Area 5 (see the next term). Appointed as the sole candidate; no election held."]),
        $seg($D('2015'), $D('2024'), $SM, 'reelected', 'certified', 'roster', 'Trustee Area 5', [25343], [$NW15('Sue Solomon', '5'),
            "The district moved its elections to even years, and this seat next came up on 3 November 2020, when it was not contested either: \"Isaiah Talley and Sue Solomon were running unopposed for seats on the Newhall School District's board\", and the County wrote to the district that \"Only one person has been nominated each for trustee areas 4 and 5, full-term offices that were scheduled for election\" ($SCVN20). " . $lists(2020, 'Newhall Elementary (Trustee Area 4 and 5)') . " For the term to December 2024, again: appointed as the sole candidate; no election held."]),
        $seg($D('2024'), ['', ''], 'elected', 'serving', 'roster', null, 'Trustee Area 5', [25343], [$k('Current Term: 2024 - 2028'),
            "Elected on 5 November 2024 for Trustee Area 5, with 4,134 votes (California Elections Data Archive (CEDA), a compilation of the County's returns), against Mayra Cuellar: \"incumbent Sue Solomon, the current governing board president, is running for reelection to represent Trustee Area No. 5. Her opponent is Mayra Cuellar\" ($SIG24)."]),
     ],
     'drop' => ['names Sue Solomon, the incumbent, for Trustee Area 5']],
    ['id' => 28243, 'person' => 25433, 'body' => 21594, 'name' => 'Shelley Weinstein', 'from' => ['December 2003', '2003-12', '', '', 'elected'],
     'segments' => [
        $seg($D('2003'), $D('2015'), 'elected', 'reelected', 'retrospective', 'certified', '', [], [$k('Mrs. Weinstein has served on the Board of Trustees since 2003'), $k('Member Shelley Weinstein replaces Clegg'),
            "The term ended in December 2015. The election of 3 November 2015, the district's first by trustee area, was not held for Trustee Area 1 (see the next term). Appointed as the sole candidate; no election held."]),
        $seg($D('2015'), ['', ''], $SM, 'serving', 'certified', null, 'Trustee Area 1', [25345], [$SS15('Rochelle "Shelley" Weinstein', '1'), $k('Board President: Shelley Weinstein'),
            "The district moved its elections to even years: the term won in 2015, due to end in December 2019, ran to December 2020. The election of 3 November 2020 for the seat was not held either. $SS20 Again: appointed as the sole candidate; no election held.",
            "The election of 5 November 2024 for the seat was not held either: $SS24 For the term to December 2028, again: appointed as the sole candidate; no election held."]),
     ],
     'drop' => ['names Rochelle "Shelley" Weinstein, the incumbent, for Trustee Area 1']],
    ['id' => 28249, 'person' => 25387, 'body' => 21590, 'name' => 'Michael Shapiro', 'from' => ['December 2003', '2003-12', 'August 1, 2016', '2016-08-01', 'elected'],
     'segments' => [
        $seg($D('2003'), $D('2015'), 'elected', 'reelected', 'retrospective', 'certified', '', [], [$k('Shapiro has been a member of the board since 2003'), $k("Shapiro takes Walters' place as clerk"),
            "The term ended in December 2015. The election of 3 November 2015, the district's first by trustee area, was not held for Trustee Area 4 (see the next term). Appointed as the sole candidate; no election held."]),
        $seg($D('2015'), ['August 1, 2016', '2016-08-01'], $SM, 'resigned', 'certified', 'contemporary', 'Trustee Area 4', [25341], [$NW15('Michael R. Shapiro', '4'), $k('who stepped down from the board effective Aug. 1')]),
     ],
     'drop' => ['names Michael R. Shapiro, the incumbent, for Trustee Area 4']],
    ['id' => 28264, 'person' => 25391, 'body' => 21590, 'name' => 'Brian Walters', 'from' => ['December 2009', '2009-12', 'December 2022', '2022-12', 'appointed'],
     'segments' => [
        $seg($D('2009'), $D('2018'), 'appointed', 'reelected', 'retrospective', 'contemporary', '', [], [$k('"first appointed in December 2009"'), $k('Unique Situation After Election Results in Open Board Seat'),
            "The term ended in December 2018: the district moved its elections from odd to even years, and the terms won in 2013 ran to December 2018. The board was elected at large until then. At the election of 6 November 2018, for Trustee Area 1 (see the next term): appointed as the sole candidate; no election held."]),
        $seg($D('2018'), $D('2022'), $SM, 'expired', 'contemporary', 'contemporary', 'Trustee Area 1', [25335], [
            "$ONE Brian Walters was the only candidate for Trustee Area 1, so the election of 6 November 2018 for the seat was not held: \"In the Newhall School District, Brian Walters and Isaiah Talley will claim their seats in the wake of no opposition\" ($SIG18). " . $lists(2018, 'Newhall School (Trustee Areas 1, 3, and 4 [U/T ending 12/20])') . " $ED",
            $k('Donna Michelle Robert 2,028, Brian D. Walters 2,015'), $k('won the seat from long-time board member Brian Walters')]),
     ],
     'drop' => ['Newhall School, Trustee Areas 1, 3 and 4. He held Trustee Area 1 from 2018']],
    ['id' => 28205, 'person' => 25409, 'body' => 21592, 'name' => 'Christopher Trunkey', 'from' => ['November 18, 2014', '2014-11-18', '', '', 'appointed'],
     'segments' => [
        $seg(['November 18, 2014', '2014-11-18'], $D('2016'), 'appointed', 'reelected', 'contemporary', 'certified', '', [], [$k('named Chris Trunkey as the newest member of their governing board'), $k('appointed to the SUSD Board of Trustees in November 2014 and reelected in 2016, 2018, and 2022'),
            "The appointment filled an at-large seat and ran to the next regular election, of 8 November 2016, which was not held for the seat he sought, for Trustee Area 5 (see the next term). Appointed as the sole candidate; no election held."]),
        $seg($D('2016'), $D('2018'), $SM, 'reelected', 'certified', 'roster', 'Trustee Area 5', [25333], [
            "$ONE Chris Trunkey, the appointed incumbent, was the only qualified candidate for the Trustee Area 5 term ending in December 2018, so the election of 8 November 2016 for the seat was not held. " . $lists(2016, 'Saugus Union School District (Trustee Areas 4 and 5 U/T ending 12/18)') . ' ' . $named(2016, 'Chris Trunkey, "Appointed Incumbent", for it') . " $ED"]),
        $seg($D('2018'), ['', ''], 'elected', 'serving', 'roster', null, 'Trustee Area 5', [25333], [
            "Elected on 6 November 2018 for Trustee Area 5, with 3,561 votes, against Sharlene Duzick (\"Sharlene Duzick will challenge Christopher Trunkey, the current trustee of area No. 5\", $SIG18), and again on 8 November 2022, with 3,183 votes (California Elections Data Archive (CEDA), a compilation of the County's returns, 2018 and 2022)."]),
     ],
     'drop' => ['Trustee Area 5 is the seat he has held since December 2016']],
    ['id' => 28217, 'person' => 15897, 'body' => 27534, 'name' => 'Lynne Plambeck', 'from' => ['December 1999', '1999-12', 'December 2017', '2017-12', 'elected'],
     'segments' => [
        $seg($D('1999'), ['2011', '2011'], 'elected', 'reelected', 'retrospective', 'contemporary', '', [], [$k('was re-elected to the Newhall County Water District Board of Directors in November of 1999'), $k('Board Vice President in 2002'), $k('Plambeck and Mortensen were last elected in 2007'),
            "The term ended in 2011, when she was appointed to the next one without an election (see the next term). No source held gives the month the term changed, so the boundary is written to the year."]),
        $seg(['2011', '2011'], ['2015', '2015'], $SM, 'reelected', 'contemporary', 'derived', '', [], [
            "$SEV Lynne Plambeck and Daniel Mortensen, the incumbents, were the only candidates for the board's two seats due for election on 8 November 2011, so no election was held, and on 15 November 2011 the Los Angeles County Board of Supervisors \"certified the 'appointment in lieu of election' of Plambeck and Mortensen to another term\": \"four more years\" ($PLAMBECK11). No County list for 2011 is held. $UDEL The article quotes that rule, with the Board of Supervisors as the supervising authority; the district's principal act is not checked here. No source held gives the month the term began or ended, so its years are given without months."]),
        $seg(['2015', '2015'], $D('2017'), 'elected', 'left', 'derived', 'retrospective', '', [], [$k('was re-elected to the Newhall County Water District Board of Directors in November of 1999'),
            "The term won in 2011 ran four years, to 2015. How she kept the seat from 2015 to the district's end is not held: no contest for the board in 2015 is in the archive, and this record keeps the method the run had before it was split.",
            $k('The agency ended on January 1, 2018')]),
     ],
     'drop' => ['the "appointment in lieu of election" of Plambeck and Mortensen to another term']],
    /* group C: Love's footnote shows the 2024 Area 1 unexpired term inside Garibay's present holding */
    ['id' => 29014, 'person' => 28988, 'body' => 21592, 'name' => 'Patti Garibay', 'from' => ['2023', '2023', '', '', 'appointed'],
     'segments' => [
        $seg(['2023', '2023'], $D('2024'), 'appointed', 'reelected', 'certified', 'derived', 'Trustee Area 1', [25325], [$k('Patti Garibay was appointed to the Saugus Union School District Board of Trustees in 2023'),
            "She filled the Trustee Area 1 seat left when Cassandra Nicole Love resigned, effective 2 October 2023 (Perry Smith, The Signal, \"SUSD board member announces plan to step down,\" 18 September 2023, https://web.archive.org/web/20250617203926/https://signalscv.com/2023/09/susd-board-member-announces-plan-to-step-down/). The appointment ran to the next regular election, of 5 November 2024, when the rest of the term, to December 2026, was due to be filled; that election was not held (see the next term)."]),
        $seg($D('2024'), $D('2026'), $SM, 'serving', 'contemporary', 'certified', 'Trustee Area 1', [25325], [$k('"Patti Garibay Trustee Area 1"'),
            "$ONE Patti Garibay, the incumbent, was the only candidate for the rest of the Trustee Area 1 term, to December 2026, so the election of 5 November 2024 for it was not held: \"incumbents Patti Garibay and Matt Watson, the current board president, are looking to once again represent areas 1 and 4, respectively\", in \"uncontested races\" ($SIG24). $SAUGUS24 $ED"]),
     ],
     'drop' => []],
];

/* ---------- helpers ---------- */
$bad = [];
$rowsOf = function (Entry $e): array {
    $out = [];
    foreach (($e->getFieldValue('footnotes') ?: []) as $r) { $out[] = ['note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? '')]; }
    return $out;
};
$number = fn(array $rows) => array_values(array_map(fn($r, $i) => ['number' => (string)($i + 1), 'note' => $r['note'], 'source' => $r['source']], $rows, array_keys($rows)));
/* resolve a notes spec against a pool of existing rows; returns [rows, errors, matchedPoolIndexes] */
$resolve = function (array $spec, array $pool) {
    $rows = []; $err = []; $used = [];
    foreach ($spec as $s) {
        if (is_string($s)) { $rows[] = ['note' => $s, 'source' => 'editorial-2026']; foreach ($pool as $i => $p) { if ($p['note'] === $s) { $used[] = $i; } } continue; }
        $hits = array_keys(array_filter($pool, fn($p) => mb_strpos($p['note'], $s['k']) !== false));
        $texts = array_unique(array_map(fn($i) => $pool[$i]['note'], $hits));
        if (count($texts) !== 1) { $err[] = (count($texts) ? 'more than one note' : 'no note') . " contains \"{$s['k']}\""; continue; }
        $rows[] = $pool[$hits[0]]; $used = array_merge($used, $hits);
    }
    return [$rows, $err, array_unique($used)];
};
$state = function (Entry $e): array {
    $v = fn($h) => $e->getFieldValue($h);
    return ['termStart' => (string)$v('termStart'), 'termStartEdtf' => (string)$v('termStartEdtf'), 'termEnd' => (string)$v('termEnd'), 'termEndEdtf' => (string)$v('termEndEdtf'),
        'selectionMethod' => (string)($v('selectionMethod')->value ?? ''), 'howEnded' => (string)($v('howEnded')->value ?? ''), 'startEvidence' => (string)($v('startEvidence')->value ?? ''),
        'endEvidence' => (string)($v('endEvidence')->value ?? ''), 'seatLabel' => (string)$v('seatLabel'), 'holdingDistrict' => $v('holdingDistrict')->status(null)->ids()];
};
$line = fn(array $s) => sprintf('%s [%s] to %s [%s] | %s | ended %s | evidence %s/%s | seat %s | district %s', $s['termStart'] ?: '-', $s['termStartEdtf'] ?: '-', $s['termEnd'] ?: '-', $s['termEndEdtf'] ?: '-',
    $s['selectionMethod'] ?: '-', $s['howEnded'] ?: '-', $s['startEvidence'] ?: '-', $s['endEvidence'] ?: '-', $s['seatLabel'] ?: '-', $s['holdingDistrict'] ? '#' . implode(',#', $s['holdingDistrict']) : '-');
$printNotes = function (array $before, array $after) {
    $bt = array_column($before, 'note'); $at = array_column($after, 'note');
    foreach ($before as $i => $r) { if (!in_array($r['note'], $at, true)) { echo '    - [' . ($i + 1) . '] ' . $r['note'] . PHP_EOL; } }
    foreach ($after as $i => $r) { echo '    ' . (in_array($r['note'], $bt, true) ? '=' : '+') . ' [' . ($i + 1) . '] ' . $r['note'] . PHP_EOL; }
};

/* ---------- 0. the option ---------- */
$field = $fs->getFieldByHandle('selectionMethod');
$hasOpt = in_array($SM, array_column($field->options, 'value'), true);
$pcHas = str_contains(json_encode(Craft::$app->getProjectConfig()->get('fields.' . $field->uid . '.settings.options')), '"' . $SM . '"');
echo "selectionMethod option \"$SM\" ($SM_LABEL): " . ($hasOpt ? 'in the field' : 'to add') . ', ' . ($pcHas ? 'in project config' : 'not in project config') . PHP_EOL;

$et = Craft::$app->getEntries()->getSectionByHandle('officeHoldings')->getEntryTypes()[0];
$need = ['holdingPerson', 'holdingOffice', 'holdingBody', 'holdingDistrict', 'termStart', 'termStartEdtf', 'termEnd', 'termEndEdtf', 'seatLabel', 'selectionMethod', 'howEnded', 'startEvidence', 'endEvidence', 'footnotes', 'recordProvenance'];
$have = array_map(fn($f) => $f->handle, $et->getFieldLayout()->getCustomFields());
foreach (array_diff($need, $have) as $h) { $bad[] = "officeHolding layout has no $h"; }

$load = function (int $id, int $person, int $body) use (&$bad) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || $e->section->handle !== 'officeHoldings') { $bad[] = "#$id is not an officeHolding"; return null; }
    if ($e->holdingPerson->status(null)->ids() !== [$person]) { $bad[] = "#$id person is not #$person"; return null; }
    if ($e->holdingBody->status(null)->ids() !== [$body]) { $bad[] = "#$id body is not #$body"; return null; }
    return $e;
};

/* ---------- plan FIX ---------- */
$plan = []; $counts = ['fix' => 0, 'fixSame' => 0, 'split' => 0, 'splitDone' => 0, 'new' => 0, 'notesNew' => 0, 'notesDropped' => 0];
echo PHP_EOL . '== 1. One-term holdings (group A: the term filled without an election; group C: how the term ended or a present term in lieu)' . PHP_EOL;
foreach ($FIX as $f) {
    $e = $load($f['id'], $f['person'], $f['body']); if (!$e) { continue; }
    $cur = $state($e); $pool = $rowsOf($e);
    [$rows, $err, $used] = $resolve($f['notes'], $pool);
    foreach ($pool as $i => $p) {
        if (in_array($i, $used, true)) { continue; }
        $dropped = false; foreach ($f['drop'] as $d) { if (mb_strpos($p['note'], $d) !== false) { $dropped = true; } }
        if (!$dropped) { $err[] = 'note [' . ($i + 1) . '] is not accounted for: ' . mb_substr($p['note'], 0, 90); }
    }
    if (isset($f['sm']) && !in_array($cur['selectionMethod'], ['appointed', '', $SM], true)) { $err[] = "selectionMethod is {$cur['selectionMethod']}, expected appointed or empty"; }
    foreach ($err as $x) { $bad[] = "#{$f['id']}: $x"; }
    $want = $cur; if (isset($f['sm'])) { $want['selectionMethod'] = $f['sm']; }
    $kept = [];
    foreach ($f['fill'] ?? [] as $h => $v) { if ($want[$h] === '') { $want[$h] = $v; } elseif ($want[$h] !== $v) { $kept[] = "$h kept as \"{$want[$h]}\", not overwritten with \"$v\""; } }
    $newRows = $number($rows); $same = $want === $cur && $newRows === $number($pool);
    echo PHP_EOL . "#{$f['id']} {$e->title} (group {$f['group']})" . ($same ? ': already done, nothing to save' : '') . PHP_EOL;
    echo '  before: ' . $line($cur) . PHP_EOL . '  after:  ' . $line($want) . PHP_EOL;
    foreach ($kept as $x) { echo "  $x" . PHP_EOL; }
    echo '  footnotes (- dropped, = kept, + new):' . PHP_EOL; $printNotes($pool, $newRows);
    $counts[$same ? 'fixSame' : 'fix']++;
    $counts['notesNew'] += count(array_diff(array_column($newRows, 'note'), array_column($pool, 'note')));
    $counts['notesDropped'] += count(array_diff(array_column($pool, 'note'), array_column($newRows, 'note')));
    if (!$same) { $plan[] = ['kind' => 'fix', 'el' => $e, 'want' => $want, 'rows' => $newRows]; }
}

/* ---------- plan SPLIT ---------- */
echo PHP_EOL . '== 2. Runs split at each change of method (group B, and Garibay from group C)' . PHP_EOL;
foreach ($SPLIT as $p) {
    $e = $load($p['id'], $p['person'], $p['body']); if (!$e) { continue; }
    $cur = $state($e);
    /* the other segments, if an earlier run made them */
    $mine = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p['person'], 'field' => 'holdingPerson'], ['targetElement' => $p['body'], 'field' => 'holdingBody']])->limit(null)->all();
    $mine = array_values(array_filter($mine, fn($x) => $x->id !== $e->id && str_contains((string)$x->recordProvenance, $SCRIPT)));
    $found = [];
    foreach (array_slice($p['segments'], 1, null, true) as $i => $s) { foreach ($mine as $x) { if ((string)$x->termStartEdtf === $s['s'][1]) { $found[$i] = $x; } } }
    $nNew = count($p['segments']) - 1;
    if ($found && count($found) !== $nNew) { $bad[] = "#{$p['id']}: a partial split (" . count($found) . " of $nNew new segments exist); check by hand"; continue; }
    $done = count($found) === $nNew;
    if (!$done) {
        $orig = [$cur['termStart'], $cur['termStartEdtf'], $cur['termEnd'], $cur['termEndEdtf'], $cur['selectionMethod']];
        if ($orig !== $p['from']) { $bad[] = "#{$p['id']}: not in the state the plan was written for: " . json_encode($orig) . ' expected ' . json_encode($p['from']); continue; }
    }
    $pool = $rowsOf($e); foreach ($found as $x) { $pool = array_merge($pool, $rowsOf($x)); }
    $usedAll = []; $segRows = [];
    foreach ($p['segments'] as $i => $s) {
        [$rows, $err, $used] = $resolve($s['notes'], $pool);
        foreach ($err as $x) { $bad[] = "#{$p['id']} segment " . ($i + 1) . ": $x"; }
        $usedAll = array_merge($usedAll, $used); $segRows[$i] = $number($rows);
    }
    if (!$done) {
        foreach ($pool as $i => $r) {
            if (in_array($i, $usedAll, true)) { continue; }
            $dropped = false; foreach ($p['drop'] as $d) { if (mb_strpos($r['note'], $d) !== false) { $dropped = true; } }
            if (!$dropped) { $bad[] = "#{$p['id']}: note [" . ($i + 1) . '] is not accounted for: ' . mb_substr($r['note'], 0, 90); }
        }
    }
    echo PHP_EOL . "#{$p['id']} {$e->title}" . ($done ? ': split already made' : ': split into ' . count($p['segments'])) . PHP_EOL;
    echo '  before: ' . $line($cur) . PHP_EOL;
    echo '  footnotes before:' . PHP_EOL; foreach ($rowsOf($e) as $i => $r) { echo '    [' . ($i + 1) . '] ' . $r['note'] . PHP_EOL; }
    $counts[$done ? 'splitDone' : 'split']++;
    foreach ($p['segments'] as $i => $s) {
        $target = $i === 0 ? $e : ($found[$i] ?? null);
        $want = ['termStart' => $s['s'][0], 'termStartEdtf' => $s['s'][1], 'termEnd' => $s['e'][0], 'termEndEdtf' => $s['e'][1], 'selectionMethod' => $s['sm'], 'howEnded' => $s['how'],
            'startEvidence' => $s['se'], 'endEvidence' => (string)$s['ee'], 'seatLabel' => $s['seat'], 'holdingDistrict' => $s['dist']];
        $now = $target ? $state($target) : null; $nowRows = $target ? $number($rowsOf($target)) : [];
        $same = $target && $now === $want && $nowRows === $segRows[$i];
        echo '  segment ' . ($i + 1) . ($i === 0 ? " (keeps #{$e->id})" : ($target ? " (#{$target->id})" : ' (new record)')) . ($same ? ': nothing to save' : '') . ':' . PHP_EOL;
        echo '    ' . $line($want) . PHP_EOL;
        foreach ($segRows[$i] as $j => $r) { echo '    ' . (in_array($r['note'], array_column($rowsOf($e), 'note'), true) && !$done ? '=' : ($done ? ' ' : '+')) . ' [' . ($j + 1) . '] ' . $r['note'] . PHP_EOL; }
        if (!$target) { $counts['new']++; }
        if (!$same) { $plan[] = ['kind' => $target ? 'fix' : 'new', 'el' => $target, 'src' => $e, 'want' => $want, 'rows' => $segRows[$i], 'split' => $p['id']]; }
    }
    if (!$done) {
        $dropped = array_diff(array_column($rowsOf($e), 'note'), array_merge(...array_map(fn($r) => array_column($r, 'note'), $segRows)));
        foreach ($dropped as $d) { echo '  - dropped: ' . $d . PHP_EOL; $counts['notesDropped']++; }
        $prov = (string)$e->recordProvenance; $add = "; split at the in-lieu term by $SCRIPT, 4 October 2026";
        if (!str_contains($prov, $SCRIPT) && mb_strlen($prov . $add) > 255) { $bad[] = "#{$p['id']}: recordProvenance would pass 255 characters"; }
    }
}
$newProv = fn(int $id) => "$SCRIPT, 4 October 2026: split from #$id at the in-lieu term (Nathan's decision of 4 October 2026)";
foreach ($SPLIT as $p) { if (mb_strlen($newProv($p['id'])) > 255) { $bad[] = 'new recordProvenance over 255'; } }

echo PHP_EOL . 'SUMMARY: ' . "$counts[fix] one-term holdings to change ($counts[fixSame] already done); $counts[split] runs to split ($counts[splitDone] already split), $counts[new] new holdings; "
    . count($plan) . " records to save; $counts[notesDropped] footnotes dropped or replaced, $counts[notesNew] new footnotes on the one-term holdings." . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$hasOpt) { echo 'The option is not in the field yet: an apply run adds it, re-saves the field and stops. Run again to set the values.' . PHP_EOL; }
if (!$APPLY || $bad) { echo ($APPLY ? 'Nothing written.' : 'Dry run: nothing written.') . ' A second run after an apply finds every value in place and saves nothing.' . PHP_EOL; return; }

/* ---------- apply ---------- */
$applyLog = require "$root/scripts/import/_apply_log.php";
if (!$hasOpt || !$pcHas) {
    if (!$hasOpt) { $o = $field->options; $o[] = ['label' => $SM_LABEL, 'value' => $SM, 'default' => false]; $field->options = $o; }
    if (!$fs->saveField($field)) { throw new \RuntimeException('selectionMethod: ' . json_encode($field->getFirstErrors())); }
    $f2 = $fs->getFieldByHandle('selectionMethod');
    $ok = str_contains(json_encode(Craft::$app->getProjectConfig()->get('fields.' . $f2->uid . '.settings.options')), '"' . $SM . '"');
    echo 'selectionMethod saved; project config ' . ($ok ? 'has' : 'does NOT have') . " the option. Run again to set the values." . PHP_EOL;
    $applyLog($SCRIPT, 1, $ok ? 'option in project config' : 'option NOT in project config', 'selectionMethod option sole-candidate added; values wait for the next run');
    return;
}
$db = Craft::$app->getDb(); $n = 0; $verified = 0;
$byId = [];
foreach ($plan as $x) { $byId[$x['split'] ?? ('f' . $x['el']->id)][] = $x; }
foreach ($byId as $group) {
    $tx = $db->beginTransaction();
    try {
        foreach ($group as $x) {
            if ($x['kind'] === 'new') {
                $src = $x['src']; $t = new Entry(); $t->sectionId = $src->sectionId; $t->setTypeId($src->typeId); $t->title = $src->title; $t->enabled = $src->enabled;
                $t->setFieldValues(['holdingPerson' => $src->holdingPerson->status(null)->ids(), 'holdingOffice' => $src->holdingOffice->status(null)->ids(), 'holdingBody' => $src->holdingBody->status(null)->ids(), 'recordProvenance' => $newProv($x['split'])]);
            } else {
                $t = Entry::find()->id($x['el']->id)->status(null)->one();
                if (isset($x['split']) && $t->id === $x['split'] && !str_contains((string)$t->recordProvenance, $SCRIPT)) { $t->setFieldValue('recordProvenance', $t->recordProvenance . "; split at the in-lieu term by $SCRIPT, 4 October 2026"); }
            }
            $w = $x['want'];
            $t->setFieldValues(['termStart' => $w['termStart'], 'termStartEdtf' => $w['termStartEdtf'], 'termEnd' => $w['termEnd'], 'termEndEdtf' => $w['termEndEdtf'], 'selectionMethod' => $w['selectionMethod'],
                'howEnded' => $w['howEnded'], 'startEvidence' => $w['startEvidence'], 'endEvidence' => $w['endEvidence'], 'seatLabel' => $w['seatLabel'], 'holdingDistrict' => $w['holdingDistrict'], 'footnotes' => $x['rows']]);
            if (!$el->saveElement($t)) { throw new \RuntimeException("#{$t->id} {$t->title}: " . json_encode($t->getFirstErrors())); }
            $n++;
            $r = Entry::find()->id($t->id)->status(null)->one();
            if ($state($r) === $w && $number($rowsOf($r)) === $x['rows']) { $verified++; } else { throw new \RuntimeException("#{$t->id}: read-back differs"); }
            echo ($x['kind'] === 'new' ? 'created' : 'saved') . " #{$t->id} {$t->title} ({$w['termStartEdtf']} to " . ($w['termEndEdtf'] ?: 'now') . ", {$w['selectionMethod']})" . PHP_EOL;
        }
        $tx->commit();
    } catch (\Throwable $ex) { $tx->rollBack(); throw $ex; }
}
$applyLog($SCRIPT, $n, "verified $verified of $n", 'sole-candidate on in-lieu terms; runs split at the in-lieu term; Education Code 5326 and 5328 for 10515 on school boards; the single wording in place of the variants');
echo "done: $n writes, $verified read back the same. A second run saves nothing." . PHP_EOL;
