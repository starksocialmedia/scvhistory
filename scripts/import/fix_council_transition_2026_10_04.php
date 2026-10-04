/**
 * The council's terms through the 2024 transition, from the County's and the City's documents
 * (inventory/review/council-transition-2026-10-04.md; Nathan, 4 October 2026: "correct her mayoralty to 2026 and Miranda's to
 * 2025, and record the three at-large members until December 2026").
 *  - Weste #23385, McLean #23399, Miranda #23321 (2022 terms): seat "At large", ending December 2026, by Ordinance 23-4 section
 *    2.A.4 and the City's District Elections page.
 *  - Gibbs #23359 (2020 at large): seat "At large"; it ended in December 2024 (expired), when he took District 3; the footnote
 *    saying a District 3 canvass "is not held" was wrong: no election was held. His District 3 term (#27410) is corrected by
 *    fix_sole_candidate_terms_2026_10_04.php.
 *  - Mayors: Miranda #29080 chosen in December 2024, mayor for 2025, to 9 December 2025; Weste #29058 sworn in on 9 December 2025,
 *    mayor for 2026 (the City's "2025" is the year she was chosen).
 *  - Weste's profile: the sentence about a 2025 election the archive "does not yet hold" corrected.
 *  - Ayala #23401: the count now cited from the County's Statement of Votes Cast.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_council_transition_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ORD = 'City of Santa Clarita, Ordinance No. 23-4, section 2.A.4, https://santaclarita.gov/wp-content/uploads/2023/11/2023-06-13-ORDINANCES-BY-DISTRICT-ELECTIONS.pdf: each member elected at large "shall continue in office until the expiration of the term to which he or she was elected"; City of Santa Clarita, "District Elections," https://santaclarita.gov/district-elections/, read 4 October 2026: "Until the November 2026 election, those three Councilmembers remain at-large." The term\'s end, December 2026, follows the election of 3 November 2026.';
$SIG26 = 'Perry Smith, "Election year is near for Santa Clarita City Council," The Signal, December 26, 2025, read in the Wayback Machine\'s capture, https://web.archive.org/web/20260105182407/https://signalscv.com/2025/12/election-year-is-nigh-for-santa-clarita-city-council/: "Outgoing Santa Clarita Mayor Bill Miranda (left) watches as incoming Mayor Laurene Weste is sworn in at City Hall"; "the Santa Clarita City Council chose a familiar face as mayor for 2026."';
$SIG08 = 'Chris Torres, "Santa Clarita City Council to choose mayor," The Signal, December 8, 2025, read in the Wayback Machine\'s capture, https://web.archive.org/web/20260107091521/https://signalscv.com/2025/12/santa-clarita-city-council-to-choose-mayor/: on Miranda\'s nomination as pro tem at the 2023 organizational meeting, "which led to his latest turn as mayor."';
$SVC = 'County of Los Angeles Registrar-Recorder/County Clerk, Statement of Votes Cast by precinct, General Election, November 5, 2024, contest "SANTA CLARITA CITY-CNC 1," https://content.lavote.gov/docs/rrcc/svc/4324_final_svc_excel_v2.zip: Patsy Ayala 4,563, Bryce Jepsen 4,142, Tim Burkhart 4,108; 15,508 ballots cast of 23,054 registered. A City resolution declaring the result is not yet held.';
$plan = [];
foreach ([23385 => 'Laurene Weste', 23399 => 'Marsha McLean', 23321 => 'Bill Miranda'] as $id => $who) { $plan[] = [$id, $who, 'at-large']; }
$plan[] = [23359, 'Jason Gibbs', 'gibbs2020']; $plan[] = [29080, 'Bill Miranda', 'mayor2025']; $plan[] = [29058, 'Laurene Weste', 'mayor2026']; $plan[] = [23401, 'Patsy Ayala', 'ayala'];
$bad = []; foreach ($plan as [$id, $who]) { if ($get($id)?->holdingPerson->one()?->title !== $who) { $bad[] = "#$id is not $who's"; } }
$wp = Entry::find()->section('persons')->title('Laurene Weste')->one();
$W_OLD = '/[^.]*does not yet hold that election[^.]*\./'; $W_NEW = "The City's council page lists 2025 among her years elected, but no council election was held in 2025: her present term is the one won on 8 November 2022, her seventh, and the 2025 entry matches the council's naming her mayor on 9 December 2025, for 2026.";
echo 'Weste profile sentence: ' . (preg_match($W_OLD, (string)$wp->body, $m) ? 'replace "' . trim($m[0]) . '"' : 'not found (left)') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $notes = fn($e) => array_values(array_filter($e->footnotes ?? [], fn($r) => trim((string)($r['note'] ?? '')) !== ''));
$renum = fn(array $rows) => array_map(fn($i, $r) => ['number' => (string)($i + 1), 'note' => $r['note'], 'source' => $r['source'] ?? 'editorial-2026'], array_keys($rows), $rows);
foreach ($plan as [$id, $who, $what]) {
    $h = $get($id); $rows = $notes($h); $v = [];
    if ($what === 'at-large' && $h->seatLabel !== 'At large') { $rows[] = ['note' => $ORD]; $v = ['seatLabel' => 'At large', 'termEnd' => 'December 2026', 'termEndEdtf' => '2026-12', 'endEvidence' => 'certified']; }
    if ($what === 'gibbs2020' && $h->seatLabel !== 'At large') { $rows = array_map(fn($r) => str_contains($r['note'], 'District 3 canvass') ? ['note' => 'The term ended in December 2024, when he took the District 3 seat, to which the City Council appointed him as the sole candidate on 19 August 2024; no election was held for District 3. His at-large term was not renewed: Ordinance 23-4 put his seat into District 3, first filled in 2024.'] + $r : $r, $rows);
        $v = ['seatLabel' => 'At large', 'howEnded' => 'expired']; }
    if ($what === 'mayor2025' && (string)$h->termStartEdtf !== '2024-12') { $rows[] = ['note' => $SIG08 . ' ' . $SIG26 . ' The City\'s "2024" is the year he was chosen; he was mayor for 2025.']; $v = ['termStart' => 'December 2024', 'termStartEdtf' => '2024-12', 'termEnd' => 'December 9, 2025', 'termEndEdtf' => '2025-12-09', 'startEvidence' => 'contemporary', 'endEvidence' => 'contemporary']; }
    if ($what === 'mayor2026' && (string)$h->termStartEdtf !== '2025-12-09') { $rows[] = ['note' => $SIG26 . ' The City\'s "2025" is the year she was chosen; she is mayor for 2026.']; $v = ['termStart' => 'December 9, 2025', 'termStartEdtf' => '2025-12-09', 'startEvidence' => 'contemporary']; }
    if ($what === 'ayala' && !str_contains(json_encode($rows), '4324_final')) { $rows = array_map(fn($r) => str_starts_with($r['note'], 'No declaring document is held') ? ['note' => $SVC] + $r : $r, $rows); $v = ['startEvidence' => 'certified']; }
    if ($v) { $v['footnotes'] = $renum($rows); $h->setFieldValues($v); if (!$el->saveElement($h)) { throw new \RuntimeException("#$id " . json_encode($h->getFirstErrors())); } $n++; }
}
if (preg_match($W_OLD, (string)$wp->body)) { $wp->setFieldValue('body', preg_replace($W_OLD, $W_NEW, (string)$wp->body, 1)); if (!$el->saveElement($wp)) { throw new \RuntimeException('Weste'); } $n++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_council_transition_2026_10_04.php', $n, 'verified', 'Council through the 2024 transition: three at large to December 2026; Gibbs 2020 at large, expired 2024; Miranda mayor for 2025, Weste for 2026; Ayala from the County count');
echo "done: $n writes" . PHP_EOL;
