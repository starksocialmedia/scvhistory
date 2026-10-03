/**
 * The County's lists of cancelled elections for November 2013, November 2015 and November 2016, applied to
 * the valley's school board holdings (Nathan, 3 October 2026: "the cancelled-election lists for 2013, 2015
 * and 2016 ... settles several of the uncontested seats we marked derived").
 *
 * THE SOURCES. For each consolidated election the Registrar-Recorder/County Clerk published a "Final List of
 * Cancelled Elections" (appointment in lieu of election, insufficiency of candidates), which names the
 * districts and areas but not the people, and a "Final List of Qualified Candidates Whose Name Will Not
 * Appear on the Ballot", which names them contest by contest. Both are read here from the County's PDFs
 * (fetched 3 October 2026, inventory/elections/county/, sha256 in manifest.json, text by pdftotext in
 * inventory/elections/text/county/). Grok Bot's transcription (inventory/review/cancelled-elections-
 * 2013-2015-2016.md) agrees with them on every valley seat. March 2013 and March 2015 had no cancelled
 * elections; June 2016 cancelled party committees only.
 *
 * WHAT THE LISTS SETTLE, and nothing else is changed:
 *  Upgrades (an existing holding the lists speak to)
 *   - Victor Torres, Castaic Union, 2009 to 2013 (#7 "unknown"): named for 2013, so the term ended in
 *     reelection; end "derived" -> "certified"; the #7 footnote is replaced by the County's.
 *   - Denis DeFigueiredo, Sulphur Springs, 2011 (#3 "2016-12?", "unknown"): named for Trustee Area 2 in
 *     November 2015, so the term ended December 2015, in reelection; end "certified". The #3 transition
 *     is settled for Sulphur Springs, Newhall and Castaic: their odd-year class was last filled in 2015.
 *   - Bob Jensen, Hart, 2013 to 2018 (#5 bridge, "derived"): named for 2013, start "certified".
 *  New holdings (decision 6: "appointed", footnote "appointed in lieu of election; no contest was held")
 *   - Victor Torres, Castaic Union, December 2013 to December 2018 (#3, 2013 class), end #7 "unknown".
 *   - Denis DeFigueiredo, Sulphur Springs Trustee Area 2, December 2015 to December 2020 (#3: due 2019, ran
 *     to 2020; the County's 2020 list has Area 2 cancelled, unnamed), end #7 "unknown".
 *   - Only with $NEW_APPOINTEES (a decision for Nathan, #14 extended to appointees in lieu): Stacy Dobbs and
 *     Michael Owen Lambarth (Castaic Union, at large, 2015 to 2020) and David C. Powell (Saugus Trustee Area
 *     4, 2016 to 2020, did not stand in 2020), each with a person record, public facts only.
 *  Footnotes only (#8: tenures that already cover the seat are kept, not split): Christopher, Pearson
 *   (Castaic 2013), Messina, Fall (Hart 2013), Shapiro, Solomon (Newhall 2015), Weinstein (Sulphur
 *   Springs 2015). Trunkey (Saugus 2016): his third footnote, "Trustee Area 5 ... since the 2018 election",
 *   is corrected, since the County appointed him to the Area 5 term in 2016.
 *  Not settled here: Robert Hall (Hart; the 2013 list names Chris Fall, who resigned after filing); the
 *   College of the Canyons seats (decision 16, no body record); CLWA and NCWD (on no list).
 *
 * Idempotent: holdings by person, body and termStartEdtf; footnotes by the County's URL; people by title.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_cancelled_2013_2016.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$NEW_APPOINTEES = false;   /* creates Dobbs, Lambarth and Powell with their holdings; off until Nathan decides */
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($NEW_APPOINTEES ? ', with the new appointees' : '') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$bad = [];
$PROV = 'apply_cancelled_2013_2016.php, 3 October 2026';
$RS = 26964;

/* ------------------------------------------------ the County's documents, checked against the manifest */
$man = json_decode(@file_get_contents("$root/inventory/elections/county/manifest.json"), true);
$DOC = [
    'L13' => '11052013_canc_elec.pdf',
    'N13' => '11052013_final_list_of_qualified_candidates_whose_name_will_not_appear_on_the_ballot.pdf',
    'L15' => '11032015-CancelledElections.pdf',
    'N15' => '11032015_CandidatesNotOnBallot.pdf',
    'L16' => '11082016-Cancelled-Elections.pdf',
    'N16' => '11082016-final-list-qualified-candidates-who-will-not-appear-on-the-ballot.pdf',
];
$TXT = [];
foreach ($DOC as $k => $f) {
    $m = $man['files'][$f] ?? null; $pdf = "$root/inventory/elections/county/$f"; $txt = "$root/inventory/elections/text/county/" . substr($f, 0, -4) . '.txt';
    if (!$m || !is_file($pdf) || hash_file('sha256', $pdf) !== $m['sha256']) { $bad[] = "$f is missing or differs from the manifest"; continue; }
    if (!is_file($txt)) { $bad[] = "the text of $f is missing"; continue; }
    $TXT[$k] = file_get_contents($txt);
}
$URL = fn($k) => $man['files'][$DOC[$k]]['url'] ?? '';
$RR = 'County of Los Angeles, Registrar-Recorder/County Clerk, ';
$CITE = [
    'L13' => $RR . '"Local and Municipal Consolidated Elections, November 5, 2013: Final List, Cancelled Elections (Appointment in Lieu of Election Because of an Insufficient Number of Candidates)", ' . $URL('L13'),
    'N13' => $RR . '"Local and Municipal Consolidated Elections, November 5, 2013: Final List of Qualified Candidates Whose Name Will Not Appear on the Ballot", August 20, 2013, ' . $URL('N13'),
    'L15' => $RR . '"Local and Municipal Consolidated Elections, November 3, 2015: Final List of Cancelled Elections (Appointment in Lieu of Election Due to Insufficiency of Candidates)", ' . $URL('L15'),
    'N15' => $RR . '"Local and Municipal Consolidated Elections, November 3, 2015: Final List of Qualified Candidates Whose Name Will Not Appear on the Ballot", September 1, 2015, ' . $URL('N15'),
    'L16' => $RR . '"General Election, November 8, 2016: Final List of Cancelled Elections, Appointment in Lieu of Election Due to Insufficiency of Candidates", ' . $URL('L16'),
    'N16' => $RR . '"2016 General Election: Final List of Qualified Candidates Whose Name Will Not Appear on the Ballot", September 2, 2016, ' . $URL('N16'),
];
$both = fn(int $y) => $CITE['L' . substr((string)$y, 2)] . '; and ' . $CITE['N' . substr((string)$y, 2)];
$C2018 = $RR . '"General Election November 6, 2018, Final List of Cancelled Elections: Appointment in Lieu of Election Due to Insufficiency of Candidates", https://www.lavote.gov/docs/rrcc/Election-Info/11082018_final-list-cancelled-elections.pdf';
$C2020 = $RR . '"General Election November 3, 2020, Final List of Cancelled Elections: Appointment in Lieu of Election Due to Insufficiency of Candidates", https://www.lavote.gov/docs/rrcc/election-info/11032020_cancelled-elections.pdf';
$C_5017 = 'California Education Code, section 5017: a board member elected at a regular election holds office "for a term of four years commencing on the second Friday in December next succeeding his or her election" (the first Friday until AB 2449 took effect in 2019).';
$C_10515 = 'Elections Code, section 10515: where no more candidates file than there are seats, the Board of Supervisors appoints them, and they serve exactly as if elected. The County calls this appointment in lieu of election.';
$INLIEU = 'Appointed in lieu of election; no contest was held.';

/* Each seat as the lists print it: the district on the cancelled list, and the name under its contest
   heading (the text from the nearest "Vote for" line above the name must hold the heading and the area). */
$SEATS = [
    ['L13', 'Castaic Union', 'N13', '*VICTOR M. TORRES', 'CASTAIC UNION SCHOOL DISTRICT', 'Governing Board Member'],
    ['L13', 'Castaic Union', 'N13', '*SUSAN M. CHRISTOPHER', 'CASTAIC UNION SCHOOL DISTRICT', 'Governing Board Member'],
    ['L13', 'Castaic Union', 'N13', '*LAURA L. PEARSON', 'CASTAIC UNION SCHOOL DISTRICT', 'Governing Board Member'],
    ['L13', 'William S. Hart Union High', 'N13', '*BOB JENSEN', 'WILLIAM S. HART UNION HIGH SCHOOL DISTRICT', 'Governing Board Member'],
    ['L13', 'William S. Hart Union High', 'N13', '*JOE MESSINA', 'WILLIAM S. HART UNION HIGH SCHOOL DISTRICT', 'Governing Board Member'],
    ['L13', 'William S. Hart Union High', 'N13', '*CHRIS A. FALL', 'WILLIAM S. HART UNION HIGH SCHOOL DISTRICT', 'Governing Board Member'],
    ['L15', 'Castaic Union School District', 'N15', 'STACY DOBBS', '3799-CASTAIC UNION SCHOOL', 'GOVERNING BOARD MEMBER'],
    ['L15', 'Castaic Union School District', 'N15', 'MICHAEL OWEN LAMBARTH', '3799-CASTAIC UNION SCHOOL', 'GOVERNING BOARD MEMBER'],
    ['L15', 'Newhall School District (Trustee Area # 4 and 5)', 'N15', '*MICHAEL R. SHAPIRO', '3929-NEWHALL SCHOOL', 'GOV BD MEMBER TR AREA 4'],
    ['L15', 'Newhall School District (Trustee Area # 4 and 5)', 'N15', '*SUE SOLOMON', '3930-NEWHALL SCHOOL', 'GOV BD MEMBER TR AREA 5'],
    ['L15', 'Sulphur Springs Union School (Trustee Area # 1 and 2)', 'N15', '*ROCHELLE "SHELLEY" WEINSTEIN', '3878-SULPHUR SPRINGS UNION SCH', 'GOV BD MEMBER TR AREA 1'],
    ['L15', 'Sulphur Springs Union School (Trustee Area # 1 and 2)', 'N15', '*DENIS F. DEFIGUEIREDO', '3879-SULPHUR SPRINGS UNION SCH', 'GOV BD MEMBER TR AREA 2'],
    ['L16', 'Saugus Union School District (Trustee Areas 4 and 5 U/T ending 12/18)', 'N16', '*DAVID C. POWELL', '4809-SAUGUS UNION SCHOOL', 'GOV BD MEMBER TR AREA 004'],
    ['L16', 'Saugus Union School District (Trustee Areas 4 and 5 U/T ending 12/18)', 'N16', '*CHRIS TRUNKEY', '4810-SAUGUS UNION SCHOOL', 'TRUSTEE 005 TERM ENDS 12/18'],
];
$seg = function (string $t, string $name) {
    $p = strpos($t, $name); if ($p === false || strpos($t, $name, $p + 1) !== false) { return null; }
    $q = strrpos(substr($t, 0, $p), 'Vote for'); if ($q === false) { return null; }
    $ls = strrpos(substr($t, 0, $q), "\n"); return substr($t, $ls === false ? 0 : $ls, $p - ($ls ?: 0) + strlen($name));
};
$squash = fn($s) => preg_replace('~\s+~', ' ', $s);
foreach ($SEATS as [$lk, $dist, $nk, $name, $head, $area]) {
    if (!isset($TXT[$lk], $TXT[$nk])) { continue; }
    if (!str_contains($squash($TXT[$lk]), $dist)) { $bad[] = "the $lk list does not read \"$dist\""; }
    $s = $seg($TXT[$nk], $name);
    if ($s === null || !str_contains($s, $head) || !str_contains($s, $area)) { $bad[] = "$name is not under $head, $area in the $nk list (or appears twice)"; }
}
if (isset($TXT['N13']) && !str_contains($TXT['N13'], '8/20/2013')) { $bad[] = 'the 2013 candidate list is not dated 8/20/2013'; }
if (isset($TXT['N15']) && !str_contains($TXT['N15'], '9/1/2015')) { $bad[] = 'the 2015 candidate list is not dated 9/1/2015'; }
if (isset($TXT['N16']) && !str_contains($TXT['N16'], '9/2/2016')) { $bad[] = 'the 2016 candidate list is not dated 9/2/2016'; }

/* ------------------------------------------------ the records, exactly as read on 3 October 2026 */
$rowsOf = fn($h) => array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? 'editorial-2026')], $h->footnotes ?? []);
$expect = function (int $id, int $pid, int $body, string $se) use ($get, &$bad) {
    $h = $get($id);
    if (!$h || ($h->holdingPerson->status(null)->ids()[0] ?? null) !== $pid || ($h->holdingBody->status(null)->ids()[0] ?? null) !== $body || (string)$h->termStartEdtf !== $se) { $bad[] = "#$id is not the holding expected (person #$pid, body #$body, start $se)"; return null; }
    return $h;
};
$P = ['torres' => 28376, 'defig' => 28330, 'jensen' => 28322];
foreach ([28376 => 'Victor Torres', 28330 => 'Denis DeFigueiredo', 28322 => 'Bob Jensen'] as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }
foreach ([21588 => 'William S. Hart Union High School District', 21592 => 'Saugus Union School District', 21590 => 'Newhall School District', 21594 => 'Sulphur Springs Union School District', 21596 => 'Castaic Union School District', $RS => 'School Board Member',
    25347 => 'Sulphur Springs Union School District, Trustee Area 2', 25331 => 'Saugus Union School District, Trustee Area 4'] as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }

$changes = [];   /* each: id, label, fields [handle => [old, new]], rows (new footnotes) or null */
$noteChange = function (array $rows, int $n, string $old, string $new, string $label) use (&$bad) {
    /* replace footnote $n when it reads $old exactly; already $new: nothing to do */
    $i = $n - 1;
    if (($rows[$i]['note'] ?? null) === $new) { return [$rows, false]; }
    if (($rows[$i]['note'] ?? null) !== $old) { $bad[] = "$label: footnote $n does not read as expected"; return [$rows, false]; }
    $rows[$i]['note'] = $new; return [$rows, true];
};
$addNote = function (array $rows, string $new, string $key) {
    foreach ($rows as $r) { if (str_contains($r['note'], $key)) { return [$rows, false]; } }
    $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $new, 'source' => 'editorial-2026']; return [$rows, true];
};
$fieldDiff = function ($h, array $want) {
    $d = [];
    foreach ($want as $k => $v) { $cur = $h->getFieldValue($k); $cur = is_object($cur) && property_exists($cur, 'value') ? (string)$cur->value : (string)$cur; if ($cur !== $v) { $d[$k] = [$cur, $v]; } }
    return $d;
};

/* U1. Torres, Castaic Union, 2009 to 2013 (#7). */
if ($h = $expect(28543, $P['torres'], 21596, '2009-12')) {
    $old = 'The seat was next due for election in 2013. No contest for it is recorded. Whether Victor Torres continued is not known.';
    $new = 'The seat was next due for election on November 5, 2013. The County cancelled that election for the district\'s three at-large seats for want of candidates, and its list of the candidates whose names would not appear on the ballot names Victor M. Torres, the incumbent, among the three: he continued in the seat, appointed in lieu of election (' . $both(2013) . ').';
    [$rows, $chg] = $noteChange($rowsOf($h), 2, $old, $new, '#28543 Torres');
    $f = $fieldDiff($h, ['howEnded' => 'reelected', 'endEvidence' => 'certified']);
    if ($f || $chg) { $changes[] = ['id' => 28543, 'label' => 'Victor Torres, Castaic Union, 2009-12 to 2013-12', 'fields' => $f, 'rows' => $chg ? $rows : null, 'notes' => $chg ? ["footnote 2 -> $new"] : []]; }
}
/* U2. DeFigueiredo, Sulphur Springs, 2011 (#3). */
if ($h = $expect(28499, $P['defig'], 21594, '2011-12')) {
    $old = 'The district moved its elections from odd to even years after 2013, and no contest for these seats is recorded in 2015 or 2016: the term, due to end in December 2015, ended in December 2015 or December 2016.';
    $new = 'The term was due to end in December 2015. The district\'s next election, on November 3, 2015, was by trustee area, and the County cancelled it for Trustee Areas 1 and 2 for want of candidates; its list of the candidates whose names would not appear on the ballot names Denis F. DeFigueiredo, the incumbent, for Trustee Area 2. The term ended in December 2015, and he continued, appointed in lieu of election (' . $both(2015) . ').';
    [$rows, $chg] = $noteChange($rowsOf($h), 2, $old, $new, '#28499 DeFigueiredo');
    $f = $fieldDiff($h, ['termEnd' => 'December 2015', 'termEndEdtf' => '2015-12', 'howEnded' => 'reelected', 'endEvidence' => 'certified']);
    if ($f || $chg) { $changes[] = ['id' => 28499, 'label' => 'Denis DeFigueiredo, Sulphur Springs, 2011-12 to 2016-12?', 'fields' => $f, 'rows' => $chg ? $rows : null, 'notes' => $chg ? ["footnote 2 -> $new"] : []]; }
}
/* U3. Jensen, Hart, the 2013 bridge (#5). */
if ($h = $expect(28580, $P['jensen'], 21588, '2013-12')) {
    if ((string)$h->selectionMethod->value !== 'appointed') { $bad[] = '#28580 is not "appointed"'; }
    $new = $INLIEU . ' The County cancelled the election of November 5, 2013 for the Hart district\'s three at-large seats for want of candidates; its list of the candidates whose names would not appear on the ballot names Bob Jensen, the incumbent, with Joe Messina and Chris A. Fall (' . $both(2013) . ').';
    [$rows, $chg] = $addNote($rowsOf($h), $new, $URL('L13'));
    $f = $fieldDiff($h, ['startEvidence' => 'certified']);
    if ($f || $chg) { $changes[] = ['id' => 28580, 'label' => 'Bob Jensen, Hart, 2013-12 to 2018-12 (bridge)', 'fields' => $f, 'rows' => $chg ? $rows : null, 'notes' => $chg ? ['footnote ' . count($rows) . " (new) -> $new"] : []]; }
}

/* F. Footnotes on the tenures that already cover the seat (#8), and Trunkey's correction. */
$TEN = [
    [28247, 25381, 21596, '2009-12', 'Susan Christopher', 2013, 'Susan M. Christopher, the incumbent, among the appointees for the district\'s three at-large seats'],
    [28241, 25379, 21596, '2005-12', 'Laura Pearson', 2013, 'Laura L. Pearson, the incumbent, among the appointees for the district\'s three at-large seats'],
    [28245, 26549, 21588, '2009-12', 'Joe Messina', 2013, 'Joe Messina, the incumbent, among the appointees for the district\'s three at-large seats, with Bob Jensen and Chris A. Fall'],
    [28249, 25387, 21590, '2003-12', 'Michael Shapiro', 2015, 'Michael R. Shapiro, the incumbent, for Trustee Area 4'],
    [28239, 25385, 21590, '1999-12', 'Suzan Solomon', 2015, 'Sue Solomon, the incumbent, for Trustee Area 5'],
    [28243, 25433, 21594, '2003-12', 'Rochelle Weinstein', 2015, 'Rochelle "Shelley" Weinstein, the incumbent, for Trustee Area 1'],
];
foreach ($TEN as [$id, $pid, $b, $se, $name, $y, $who]) {
    if (!($h = $expect($id, $pid, $b, $se))) { continue; }
    $date = $y === 2013 ? 'November 5, 2013' : 'November 3, 2015';
    $new = "The County cancelled the election of $date for want of candidates, and its list of the candidates whose names would not appear on the ballot names $who: appointed in lieu of election, no contest was held (" . $both($y) . ').';
    [$rows, $chg] = $addNote($rowsOf($h), $new, $URL('L' . substr((string)$y, 2)));
    if ($chg) { $changes[] = ['id' => $id, 'label' => "$name, " . $get($b)->title . ", tenure from $se", 'fields' => [], 'rows' => $rows, 'notes' => ['footnote ' . count($rows) . " (new) -> $new"]]; }
}
if ($h = $expect(28807, 28721, 21588, '2013-06-05')) {
    $new = 'The County\'s list of the candidates whose names would not appear on the ballot at the election of November 5, 2013, dated August 20, 2013, names Chris A. Fall, "Appointed Incumbent", filed August 7, 2013, among the three candidates for the Hart district\'s at-large seats, whose election the County cancelled for want of candidates (' . $both(2013) . '). The list is dated four days after the resignation the roster records.';
    [$rows, $chg] = $addNote($rowsOf($h), $new, $URL('L13'));
    if ($chg) { $changes[] = ['id' => 28807, 'label' => 'Chris Fall, Hart, 2013-06-05 to 2013-08-16', 'fields' => [], 'rows' => $rows, 'notes' => ['footnote ' . count($rows) . " (new) -> $new"]]; }
}
if ($h = $expect(28205, 25409, 21592, '2014-11-18')) {
    $old = 'Trustee Area 5 is the seat he has held since the 2018 election (CEDA 2018 and 2022, and the district\'s page); his 2014 appointment filled an at-large seat.';
    $new = 'Trustee Area 5 is the seat he has held since December 2016: the County cancelled the election of November 8, 2016 for the Area 5 term ending in December 2018 for want of candidates, and its list of the candidates whose names would not appear on the ballot names Chris Trunkey, "Appointed Incumbent", for it, appointed in lieu of election (' . $both(2016) . '). He won the seat at the elections of 2018 and 2022 (CEDA 2018 and 2022, and the district\'s page). His 2014 appointment filled an at-large seat.';
    [$rows, $chg] = $noteChange($rowsOf($h), 3, $old, $new, '#28205 Trunkey');
    if ($chg) { $changes[] = ['id' => 28205, 'label' => 'Christopher Trunkey, Saugus Union, from 2014-11-18', 'fields' => [], 'rows' => $rows, 'notes' => ["footnote 3 -> $new"]]; }
}

/* N. New holdings. */
$pear = $get(28241); $pr = $pear ? $rowsOf($pear) : [];
$Q_TORRES = 'Allison Pari, Hometownstation.com, "Huffaker Takes Helm of Castaic School Board", December 13, 2013, https://scvnews.com/huffaker-takes-helm-of-castaic-school-board: "Board members Susan Christopher, Laura Pearson and Victor Torres, who were unopposed in this year\'s school board elections, were also sworn in for new four-year terms that end in 2017."';
$Q_DOBBS = 'Christina Cox, The Signal, "Castaic Governing Board elects new president, clerk", December 8, 2017, https://signalscv.com/2017/12/castaic-governing-board-elects-new-president-clerk/: "The Castaic Union School District elected Laura Pearson as its new president and Stacy Dobbs as its new clerk during its annual organizational meeting Thursday."';
if (($pr[2]['note'] ?? '') !== $Q_TORRES || ($pr[1]['note'] ?? '') !== $Q_DOBBS) { $bad[] = '#28241 Pearson: footnotes 2 and 3 are not the quotations expected'; }
$NEW = [];
$NEW[] = ['who' => $P['torres'], 'name' => 'Victor Torres', 'b' => 21596, 'place' => null, 'area' => '', 'sp' => 'December 2013', 'se' => '2013-12', 'ep' => 'December 2018', 'ee' => '2018-12', 'how' => 'unknown', 'notes' => [
    $INLIEU . ' The County cancelled the election of November 5, 2013 for the district\'s three at-large seats for want of candidates; its list of the candidates whose names would not appear on the ballot names Victor M. Torres, the incumbent, with Susan M. Christopher and Laura L. Pearson (' . $both(2013) . ').',
    $C_10515, $Q_TORRES,
    'The district moved its elections from odd to even years: this term, due to end in December 2017, ran to December 2018, the next election for the seat.',
    'The seat was next due for election in 2018. No contest for it is recorded, and the County lists seats of this board among those filled by appointment in lieu of election, without naming the appointees (' . $C2018 . '). Whether Victor Torres continued is not known.',
    $C_5017]];
$NEW[] = ['who' => $P['defig'], 'name' => 'Denis DeFigueiredo', 'b' => 21594, 'place' => 25347, 'area' => '2', 'sp' => 'December 2015', 'se' => '2015-12', 'ep' => 'December 2020', 'ee' => '2020-12', 'how' => 'unknown', 'notes' => [
    $INLIEU . ' The County cancelled the election of November 3, 2015 for Trustee Areas 1 and 2 for want of candidates; its list of the candidates whose names would not appear on the ballot names Denis F. DeFigueiredo, the incumbent, for Trustee Area 2 (' . $both(2015) . ').',
    $C_10515,
    'The district moved its elections from odd to even years: this term, due to end in December 2019, ran to December 2020, the next election for the seat.',
    'The seat was next due for election in 2020. No contest for it is recorded, and the County lists Trustee Area 2 among the seats filled by appointment in lieu of election, without naming the appointees (' . $C2020 . '). Whether Denis DeFigueiredo continued is not known.',
    $C_5017]];
/* The new appointees: only with $NEW_APPOINTEES. Powell's seat was contested in 2020; he did not stand. */
$sa20 = Entry::find()->section('elections')->status(null)->title('Saugus Union School District board election, Trustee Area 4, November 3, 2020')->one();
$sa20c = $sa20 ? array_map(fn($c) => (string)$c->nameAsPrinted, Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $sa20, 'field' => 'candidacyElection'])->all()) : null;
if (!$sa20c) { $bad[] = 'the Saugus Union Trustee Area 4 election of November 3, 2020 is not found'; }
elseif (preg_grep('~powell~i', $sa20c)) { $bad[] = 'Powell stood in 2020: his 2016 term does not simply expire'; }
$NEWP = [
    'Stacy Dobbs' => ['aliases' => [], 'printed' => 'STACY DOBBS'],
    'Michael Owen Lambarth' => ['aliases' => [], 'printed' => 'MICHAEL OWEN LAMBARTH'],
    'David Powell' => ['aliases' => ['David C. Powell'], 'printed' => '*DAVID C. POWELL'],
];
$castaic15 = fn($nm, $other) => [
    $INLIEU . ' The County cancelled the election of November 3, 2015 for the district\'s two at-large seats for want of candidates; its list of the candidates whose names would not appear on the ballot names ' . $nm . ' and ' . $other . ', neither of them an incumbent (' . $both(2015) . ').',
    $C_10515,
    'The district moved its elections from odd to even years: this term, due to end in December 2019, ran to December 2020, the next election for the seat.',
    'The seat was next due for election in 2020. No contest for it is recorded, and the County lists Castaic Union Trustee Areas A and C among the seats filled by appointment in lieu of election, without naming the appointees (' . $C2020 . '). Whether ' . $nm . ' continued is not known.',
    $C_5017];
$NEWA = [
    ['who' => 'new:Stacy Dobbs', 'name' => 'Stacy Dobbs', 'b' => 21596, 'place' => null, 'area' => '', 'sp' => 'December 2015', 'se' => '2015-12', 'ep' => 'December 2020', 'ee' => '2020-12', 'how' => 'unknown',
        'notes' => array_merge(array_slice($castaic15('Stacy Dobbs', 'Michael Owen Lambarth'), 0, 2), [$Q_DOBBS], array_slice($castaic15('Stacy Dobbs', 'Michael Owen Lambarth'), 2))],
    ['who' => 'new:Michael Owen Lambarth', 'name' => 'Michael Owen Lambarth', 'b' => 21596, 'place' => null, 'area' => '', 'sp' => 'December 2015', 'se' => '2015-12', 'ep' => 'December 2020', 'ee' => '2020-12', 'how' => 'unknown',
        'notes' => $castaic15('Michael Owen Lambarth', 'Stacy Dobbs')],
    ['who' => 'new:David Powell', 'name' => 'David Powell', 'b' => 21592, 'place' => 25331, 'area' => '4', 'sp' => 'December 2016', 'se' => '2016-12', 'ep' => 'December 2020', 'ee' => '2020-12', 'how' => 'expired', 'notes' => [
        $INLIEU . ' The County cancelled the election of November 8, 2016 for Trustee Area 4 for want of candidates; its list of the candidates whose names would not appear on the ballot names David C. Powell, "Appointed Incumbent", for it (' . $both(2016) . ').',
        $C_10515,
        'The County\'s list gives David C. Powell as the appointed incumbent: an earlier appointment to the board, whose date is not recorded here.',
        'David Powell did not stand at the next election for the seat, on November 3, 2020.',
        $C_5017]],
];
if ($NEW_APPOINTEES) { $NEW = array_merge($NEW, $NEWA); }
/* people: none may exist already under another record */
$pIndex = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) { foreach (array_merge([$p->title, (string)$p->fullName], preg_split('~\n~', (string)$p->personAliases)) as $n) { if (trim($n) !== '') { $pIndex[strtolower(trim($n))][] = $p; } } }
$peopleToMake = []; $peopleFound = [];
foreach ($NEWP as $t => $np) {
    $hits = array_merge($pIndex[strtolower($t)] ?? [], ...array_map(fn($a) => $pIndex[strtolower($a)] ?? [], $np['aliases']));
    $surname = strtolower(preg_replace('~^.* ~', '', $t));
    $near = array_filter(array_keys($pIndex), fn($k) => str_ends_with($k, " $surname"));
    if ($hits) {
        $h0 = $hits[0];
        if (str_starts_with((string)$h0->recordProvenance, $PROV)) { $peopleFound[$t] = $h0->id; } else { $bad[] = "a person titled or aliased $t exists (#{$h0->id}) and was not made here"; }
    } elseif ($near) { $bad[] = "$t: an existing person shares the surname (" . implode(', ', $near) . '); decide before creating'; }
    else { $peopleToMake[$t] = $np; }
}
$era = craft\elements\Category::find()->group('historicalEra')->id(171)->one();
if (!$era || !str_starts_with($era->title, 'Contemporary (2010')) { $bad[] = 'era #171 is not Contemporary (2010 to present)'; }

/* #8 and idempotency for the new holdings, against the holdings as they will stand after the upgrades. */
$existing = [];
foreach (Entry::find()->section('officeHoldings')->status(null)->all() as $h) {
    $e = (string)$h->termEndEdtf;
    foreach ($changes as $c) { if ($c['id'] === $h->id && isset($c['fields']['termEndEdtf'])) { $e = $c['fields']['termEndEdtf'][1]; } }
    $existing[] = ['id' => $h->id, 'pid' => $h->holdingPerson->status(null)->ids()[0] ?? null, 'b' => $h->holdingBody->status(null)->ids()[0] ?? null, 's' => (string)$h->termStartEdtf, 'e' => $e];
}
$yr = fn($s) => (int)substr((string)$s, 0, 4);
$plan = []; $held = [];
foreach ($NEW as $t) {
    $pid = is_int($t['who']) ? $t['who'] : ($peopleFound[substr($t['who'], 4)] ?? null);
    $same = $pid ? array_values(array_filter($existing, fn($x) => $x['pid'] == $pid && $x['b'] == $t['b'] && $x['s'] === $t['se'])) : [];
    if ($same) { $held[] = $t['name'] . ' ' . $t['se'] . ' #' . $same[0]['id']; continue; }
    $cov = $pid ? array_values(array_filter($existing, fn($x) => $x['pid'] == $pid && $x['b'] == $t['b'] && $yr($x['s']) <= $yr($t['se']) && ($x['e'] === '' || $yr($t['se']) < $yr($x['e'])))) : [];
    if ($cov) { $bad[] = $t['name'] . ' ' . $t['se'] . ': inside #' . $cov[0]['id'] . ' (#8); not expected'; continue; }
    $plan[] = $t;
}

/* ------------------------------------------------ wording and lengths */
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|with Nathan|Nathan\'s|Claude|Grok|pdftotext)\b~i';
$allNotes = array_merge(...array_map(fn($c) => $c['notes'], $changes), ...array_map(fn($t) => $t['notes'], $plan));
foreach ($allNotes as $n) {
    $u = preg_replace('~https?://\S+~', ' ', $n);
    if (preg_match($BAD, $u, $m) || preg_match('~\x{2014}|\x{2013}|inventory/|\.json|scripts/~u', $n, $m)) { $bad[] = "a footnote reads \"{$m[0]}\": " . mb_substr($n, 0, 60); }
}
$hProv = "$PROV: a seat filled by appointment in lieu of election, on the County's list of cancelled elections (decision 6)";
$pProv = "$PROV: appointed in lieu of election to a Santa Clarita Valley school board, on the County's list of cancelled elections; public facts only";
foreach ([$hProv, $pProv] as $s) { if (mb_strlen($s) > 255) { $bad[] = 'a recordProvenance runs over 255 characters: ' . mb_strlen($s); } }

/* ------------------------------------------------ the report */
echo PHP_EOL . 'SOURCES (sha256 checked against the manifest):' . PHP_EOL;
foreach ($DOC as $k => $f) { echo "   $k  " . ($man['files'][$f]['sha256'] ?? '?') . "  $f" . PHP_EOL; }
echo PHP_EOL . 'VALLEY SEATS ON THE LISTS (' . count($SEATS) . ', each found under its contest heading):' . PHP_EOL;
foreach ($SEATS as [$lk, $dist, $nk, $name, $head, $area]) { printf("   %-4s %-36s %-30s %s\n", $lk, $name, $head, $area); }
echo '   Also on the 2013 list, not a body held here: Santa Clarita Community College, Seats 1, 3 and 5 (Berger, Mac Gregor, Zimmer); on the 2016 list, its Trustee Area 3 (Zimmer). Decision 16.' . PHP_EOL;

echo PHP_EOL . 'CHANGES TO EXISTING HOLDINGS (' . count($changes) . '):' . PHP_EOL;
foreach ($changes as $c) {
    echo "   #{$c['id']} {$c['label']}" . PHP_EOL;
    foreach ($c['fields'] as $k => [$o, $n]) { echo "      $k: " . ($o === '' ? '(blank)' : $o) . " -> $n" . PHP_EOL; }
    foreach ($c['notes'] as $n) { echo "      $n" . PHP_EOL; }
}
echo PHP_EOL . 'HOLDINGS TO CREATE (' . count($plan) . '), selectionMethod "appointed", startEvidence "certified":' . PHP_EOL;
foreach ($plan as $t) {
    printf("   %-24s %-38s %-14s %s -> %s  howEnded %-8s end derived\n", $t['name'] . (is_string($t['who']) ? '*' : ''), $get($t['b'])->title, $t['area'] !== '' ? "Trustee Area {$t['area']}" : 'at large', $t['se'], $t['ee'], $t['how']);
    foreach ($t['notes'] as $i => $n) { echo '      ' . ($i + 1) . ". $n" . PHP_EOL; }
}
if ($held) { echo '   already held (same person, body and start): ' . implode('; ', $held) . PHP_EOL; }
echo PHP_EOL . ($NEW_APPOINTEES ? 'PEOPLE TO CREATE (' . count($peopleToMake) . '):' : 'NEW APPOINTEES, not written ($NEW_APPOINTEES is false; for Nathan to decide). Would create ' . count($peopleToMake) . ' people and ' . count($NEWA) . ' holdings:') . PHP_EOL;
foreach ($peopleToMake as $t => $np) { echo "   $t" . ($np['aliases'] ? ' (alias ' . implode('; ', $np['aliases']) . ')' : '') . ", role School Board Member, era {$era?->title}, printed \"{$np['printed']}\"" . PHP_EOL; }
if (!$NEW_APPOINTEES) { foreach ($NEWA as $t) { printf("   %-24s %-38s %-14s %s -> %s  howEnded %s\n", $t['name'] . '*', $get($t['b'])->title, $t['area'] !== '' ? "Trustee Area {$t['area']}" : 'at large', $t['se'], $t['ee'], $t['how']); } }
foreach ($peopleFound as $t => $id) { echo "   $t exists as #$id (made by this script)" . PHP_EOL; }

echo PHP_EOL . 'LEFT OPEN, the lists do not settle them:' . PHP_EOL;
echo '   Robert Hall (#28809, Hart, from 2013-10-16, end blank): the 2013 list names Chris A. Fall for the at-large seat Hall filled; Fall resigned on August 16, 2013, before the list\'s date. Whether Hall\'s seat was the new term or Fall\'s unexpired one needs the board\'s minutes (the roster says the unexpired term; KHTS, October 16, 2013, says the four-year term).' . PHP_EOL;
echo '   Shapiro, Solomon, Weinstein, Pearson, Messina, Trunkey: open tenures, footnoted; the lists add no end.' . PHP_EOL;
echo '   The 2011 at-large holders at Newhall and Castaic Union, and Stephen Winkler (Saugus, 2011 to 2016, "unknown"): not named on any list.' . PHP_EOL;
echo '   The #9 research list: no one on it appears on these lists.' . PHP_EOL;
echo '   CLWA and NCWD: on no list (NCWD was on the 2015 ballot, CLWA on the 2016 ballot). College of the Canyons: decision 16.' . PHP_EOL;

$writes = count($changes) + count($plan) + ($NEW_APPOINTEES ? count($peopleToMake) : 0);
echo PHP_EOL . "SUMMARY: " . count($changes) . ' existing holdings changed (' . count(array_filter($changes, fn($c) => $c['fields'])) . ' with field changes, ' . count(array_filter($changes, fn($c) => !$c['fields'])) . ' footnotes only), ' . count($plan) . ' holdings to create, ' . ($NEW_APPOINTEES ? count($peopleToMake) : 0) . ' people to create. A second run writes nothing: ' . ($writes ? 'not yet applied' : 'already applied') . '.' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$writes) { echo 'Nothing to write.' . PHP_EOL; return; }

/* ------------------------------------------------ apply, in one transaction */
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ids = $peopleFound;
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($NEW_APPOINTEES) {
        $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
        foreach ($peopleToMake as $t => $np) {
            $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $t;
            $p->setFieldValues(['fullName' => $t, 'personAliases' => implode("\n", $np['aliases']), 'roles' => [$RS], 'historicalEra' => [171], 'recordProvenance' => $pProv]);
            if (!$elements->saveElement($p)) { throw new \RuntimeException("person $t: " . json_encode($p->getFirstErrors())); }
            $ids[$t] = $p->id;
        }
    }
    foreach ($changes as $c) {
        $h = $get($c['id']); $v = [];
        foreach ($c['fields'] as $k => [$o, $n]) { $v[$k] = $n; }
        if ($c['rows'] !== null) { $v['footnotes'] = $c['rows']; }
        $h->setFieldValues($v);
        if (!$elements->saveElement($h)) { throw new \RuntimeException("#{$c['id']}: " . json_encode($h->getFirstErrors())); }
    }
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($plan as $t) {
        $pid = is_int($t['who']) ? $t['who'] : $ids[substr($t['who'], 4)];
        if (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $pid, 'field' => 'holdingPerson'], ['targetElement' => $t['b'], 'field' => 'holdingBody']])->termStartEdtf($t['se'])->exists()) { continue; }
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($ot->id);
        $h->setFieldValues(['holdingPerson' => [$pid], 'holdingOffice' => [$RS], 'holdingBody' => [$t['b']], 'holdingDistrict' => $t['place'] ? [$t['place']] : [],
            'seatLabel' => $t['area'] !== '' ? 'Trustee Area ' . $t['area'] : '', 'selectionMethod' => 'appointed',
            'termStart' => $t['sp'], 'termStartEdtf' => $t['se'], 'termEnd' => $t['ep'], 'termEndEdtf' => $t['ee'], 'howEnded' => $t['how'],
            'startEvidence' => 'certified', 'endEvidence' => 'derived', 'footnotes' => $fn($t['notes']), 'recordProvenance' => $hProv]);
        if (!$elements->saveElement($h)) { throw new \RuntimeException('holding ' . $t['name'] . ' ' . $t['se'] . ': ' . json_encode($h->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $e) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $e->getMessage() . PHP_EOL; throw $e; }

/* Read back. */
$short = [];
foreach ($changes as $c) {
    $h = $get($c['id']);
    foreach ($c['fields'] as $k => [$o, $n]) { $cur = $h->getFieldValue($k); $cur = is_object($cur) && property_exists($cur, 'value') ? (string)$cur->value : (string)$cur; if ($cur !== $n) { $short[] = "#{$c['id']} $k"; } }
    if ($c['rows'] !== null && array_column($rowsOf($h), 'note') !== array_column($c['rows'], 'note')) { $short[] = "#{$c['id']} footnotes"; }
}
foreach ($plan as $t) {
    $pid = is_int($t['who']) ? $t['who'] : $ids[substr($t['who'], 4)];
    $h = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $pid, 'field' => 'holdingPerson'], ['targetElement' => $t['b'], 'field' => 'holdingBody']])->termStartEdtf($t['se'])->one();
    if (!$h || (string)$h->termEndEdtf !== $t['ee'] || (string)$h->selectionMethod->value !== 'appointed' || (string)$h->startEvidence->value !== 'certified') { $short[] = $t['name'] . ' ' . $t['se']; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($changes) . ' changed, ' . count($plan) . ' created' . ($NEW_APPOINTEES ? ', ' . count($peopleToMake) . ' people' : '')) . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('apply_cancelled_2013_2016.php', $writes, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'the County\'s cancelled-election lists of 2013, 2015 and 2016 on the school board holdings');
if ($short) { throw new \RuntimeException('apply_cancelled_2013_2016: ' . implode('; ', $short)); }
