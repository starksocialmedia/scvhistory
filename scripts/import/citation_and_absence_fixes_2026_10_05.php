/**
 * Nathan's answers of 5 October 2026 on the appointment and quotation audits.
 * 1. DeFigueiredo 2007 (#28497): "'No election held' resting only on a contest missing from the County data is an inference from
 *    absence ... It should say how he took the seat is not known, like Emmons and the other three." The method becomes empty
 *    (not known) and the note says why. The sweep for other terms whose method rests on a missing contest found none: the
 *    other notes that mention one either say "not known" already or rest on the County's cancelled-election lists.
 * 2. Measure V (#28940 row 1, #25409 row 7): the ellipsis joined the first sentence of the district's notice to the end of its
 *    third, so "approved by local voters in 2001 for $158 million" read as describing the bond funds rather than the measure.
 *    Two quotations instead (inventory/review/spliced-quotations-audit-2026-10-05.md, DB31).
 * 3. The City (#394 row 4): "In 2024 ... District 1 and District 3." was attributed to the City's District Elections page,
 *    which does not carry the words; they are Ordinance 23-4's (the new Municipal Code section 2.04.005, subdivision B, checked against the ordinance's PDF on 5 October 2026), cited earlier in the same note.
 * 4. The three quotations from the California Digital Newspaper Collection that could not be rechecked (#21584 rows 7 and 11,
 *    #16039 row 13): left as written, with why in each record's research leads.
 * Exact text, once, or refused. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/citation_and_absence_fixes_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0; $bad = [];
$FIX = [
  [28497, 0, 'Appointed without an election; the candidates did not outnumber the seats. No contest for the seat is recorded at the election of 6 November 2007, so it is taken to have been filled without one; the County\'s list of the elections it cancelled that year is not held, so this rests on the missing contest alone. Under Education Code sections 5326 and 5328, when the election is not held "the qualified person or persons nominated shall be seated at the organizational meeting of the board"; the board appoints only when no one, or too few, were nominated.',
    'How Denis DeFigueiredo took the seat in 2007 is not known. No contest for it is in the CEDA file for the election of 6 November 2007, but the file lacks the district in 2005 and 2009 as well, so the gap may be the file\'s; the County\'s list of the elections it cancelled that year, and the district\'s minutes, are not held.'],
  [28940, 1, 'the "Citizen\'s Oversight Committee formed to review expenditures of Measure V school bond funds ... approved by local voters in 2001 for $158 million."',
    'the "Citizen\'s Oversight Committee formed to review expenditures of Measure V school bond funds," which is to "assure that Measure V funds are spent in accordance with the measure approved by local voters in 2001 for $158 million."'],
  [25409, 7, 'the "Citizen\'s Oversight Committee formed to review expenditures of Measure V school bond funds ... approved by local voters in 2001 for $158 million."',
    'the "Citizen\'s Oversight Committee formed to review expenditures of Measure V school bond funds," which is to "assure that Measure V funds are spent in accordance with the measure approved by local voters in 2001 for $158 million."'],
  [394, 4, ' adding Santa Clarita Municipal Code section 2.04.005. City of Santa Clarita, District Elections, https://santaclarita.gov/district-elections/: "In 2024 ... District 1 and District 3."',
    ' adding Santa Clarita Municipal Code section 2.04.005, whose subdivision B reads: "In 2024 ... District 1 and District 3." The City\'s District Elections page (https://santaclarita.gov/district-elections/) describes the districts.'],
];
foreach ($FIX as [$id, $row, $old, $new]) {
  $e = Entry::find()->id($id)->status(null)->one(); if (!$e) { $bad[] = "no #$id"; continue; }
  $rows = array_values(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], iterator_to_array($e->footnotes)));
  $cur = $rows[$row]['note'] ?? '';
  if (str_contains($cur, $new)) { echo "#$id row $row: done already\n"; continue; }
  if (substr_count($cur, $old) !== 1) { $bad[] = "#$id row $row: the text is not there once"; continue; }
  $rows[$row]['note'] = str_replace($old, $new, $cur); echo "#$id {$e->title} row $row:\n  $new\n";
  $vals = ['footnotes' => $rows]; if ($id === 28497) { $vals['selectionMethod'] = ''; echo "  method: not known\n"; }
  if ($APPLY) { $e->setFieldValues($vals); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id"); } $n++; }
}
$LEAD = 'Not rechecked on 5 October 2026: the quotations in notes %s come from the California Digital Newspaper Collection (cdnc.ucr.edu), which refused the request (HTTP 403); no Wayback capture and no local copy exist. Each drops only a few words inside one phrase, so they stand as written until the page can be read.';
foreach ([21584 => '7 and 11', 16039 => '13'] as $id => $rowsTxt) {
  $e = Entry::find()->id($id)->status(null)->one(); $lead = sprintf($LEAD, $rowsTxt); $cur = trim((string)$e->researchLeads);
  if (str_contains($cur, 'cdnc.ucr.edu')) { echo "#$id: noted already\n"; continue; }
  echo "#$id {$e->title}: research lead added\n"; if ($APPLY) { $e->setFieldValue('researchLeads', trim($cur . "\n\n" . $lead)); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id"); } $n++; }
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('citation_and_absence_fixes_2026_10_05.php', $n, 'verified', 'DeFigueiredo 2007 not known; Measure V quotation; #394 citation; CDNC notes'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
