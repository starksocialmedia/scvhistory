/**
 * Leads off the page (Nathan, 5 October 2026: "a lead or a working hypothesis published as an editor note" is the fault; the
 * lead goes to researchLeads, internal, and the reader is told only what is established). Each move takes a sentence out of a
 * public editor's note, by exact text that must occur once, and adds it to the record's researchLeads with the heading it was
 * under; a note left empty loses its row. Two groups: $APPROVED (Couts's Wikipedia lead, which Nathan named, and Tichenor's
 * "Jr." as an alias, one source using it) and the sweep's finds, written only with $APPLY_SWEEP. The sweep read all 421
 * public editor's notes with text (2,495 rows on 2,415 records) for hedged wording; the moves below are the ones that state
 * a suspected identity or fact, not the ones that say what is unknown or how a record's dates were reached.
 * Idempotent: a move whose sentence is gone and whose lead is present is skipped. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/move_leads_off_notes_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false; $APPLY_SWEEP = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($APPLY_SWEEP ? ' with the sweep' : '') . PHP_EOL;
$el = Craft::$app->getElements();
/* [record id, note heading, old text, replacement, the lead as it goes into researchLeads, approved] */
$M = [
  [323, 'Not yet covered', ' Wikipedia, used here only as a finding aid, reports that he was tried on several charges, including murder, and acquitted. No source in the archive speaks of the trials.', '',
    'Wikipedia reports that he was tried on several charges, including murder, and acquitted. No source in the archive speaks of the trials. Settled by a court record or a contemporary newspaper report of a trial.', true],
  [30373, 'How his service ended', ' Scott Wilk is said to have been appointed in August 2006 to the seat he then won as Seat No. 5, which would make it Gillis\'s, but no source here states it.', '',
    'Scott Wilk is said to have been appointed in August 2006 to the seat he then won as Seat No. 5, which would make it Gillis\'s; no source states it. Settled by the board minutes of 2006 or a Signal report of the appointment.', false],
  [30277, 'When and how he left the board', ' The election of November 6, 1973, at which Carl Boyer won a seat, may have filled his place; no source says which seat it was.', '',
    'The election of November 6, 1973, at which Carl Boyer won a seat, may have filled Fortine\'s place; no source says which seat it was. Settled by the 1973 return or a Signal or Canyon Call report naming the vacancy.', false],
  [554, 'Sources, 2026', ' He enlisted for World War II on the same day as Edward D. Contreras of Castaic, five serial numbers apart, which suggests but does not show that the two enlisted together.', '',
    'He enlisted on the same day as Edward D. Contreras of Castaic, five serial numbers apart: perhaps the two enlisted together, and perhaps that ties him to the valley. Settled by a newspaper report of the two enlisting, or a draft card giving a valley address.', false],
  [562, 'Sources, 2026', '; a Jack Harland is among the graduates in the Newhall School\'s commencement program of 1929 (as carried on SCVHistory.com, /scvhistory/ku2902a.htm); that he is this man, who was 28 in 1944, is likely but not established.', '.',
    'A Jack Harland is among the graduates in the Newhall School\'s commencement program of 1929 (SCVHistory.com, /scvhistory/ku2902a.htm); that he is this man, who was 28 in 1944, is likely but not established. Settled by the 1930 or 1940 census placing Jack Harland in Newhall, or a Signal report of his death.', false],
  [562, 'Home, 2026', ' The only possible connection found in the archive is the Newhall School\'s commencement program of 1929, which lists a Jack Harland among its graduates.', '',
    '', false],
  [16380, 'Held for a second source, and for the elections work', ' Exact dates of his council and Assembly terms appear only in Wikipedia and will come from the canvasses and the Assembly journal with the officeHolding records.', '',
    'The exact dates of his council and Assembly terms appear only in Wikipedia. Settled by the City canvasses and the Assembly journal.', false],
  [29854, 'A Stevenson Ranch council', 'A Stevenson Ranch Town Council was active in 1996, founded by Richard "Doc" Rioux, and a West Ranch Town Council in 2006. Whether they are one body, what it is in law, and whether it still meets are not settled by the sources read, so it has no record yet.', '',
    'A Stevenson Ranch Town Council was active in 1996, founded by Richard "Doc" Rioux, and a West Ranch Town Council in 2006. Whether they are one body, what it is in law, and whether it still meets are not settled. Settled by its bylaws or a County list of recognized town councils; if it governs or advises, it is a record and goes in Community councils.', false],
];
$n = 0; $bad = [];
foreach ($M as [$id, $head, $old, $new, $lead, $ok]) {
  $e = Entry::find()->id($id)->status(null)->one(); if (!$e) { $bad[] = "no #$id"; continue; }
  $rows = array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes ?? []));
  $hit = array_keys(array_filter($rows, fn($r) => $r['heading'] === $head && substr_count($r['note'], $old) === 1));
  $leads = trim((string)$e->researchLeads); $haveLead = $lead === '' || str_contains($leads, $lead);
  if (!$hit && $haveLead) { echo "#$id [$head]: already moved\n"; continue; }
  if (count($hit) !== 1) { $bad[] = "#$id [$head]: the text is not in the note once"; continue; }
  $i = $hit[0]; $after = trim(str_replace($old, $new, $rows[$i]['note']));
  echo ($ok ? 'APPROVED' : 'SWEEP') . " #$id {$e->title} [$head]\n  note becomes: " . ($after === '' ? '(row removed)' : $after) . "\n" . ($lead !== '' ? "  lead: $lead\n" : '');
  if (!$APPLY || (!$ok && !$APPLY_SWEEP)) { continue; }
  if ($after === '') { unset($rows[$i]); } else { $rows[$i]['note'] = $after; }
  $e->setFieldValue('editorNotes', array_values($rows));
  if (!$haveLead) { $e->setFieldValue('researchLeads', trim($leads . "\n\n" . $lead . " (From the editor's note \"$head\", 5 October 2026.)")); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
}
/* Tichenor: "Jr." in one source only (SCVNews, 2011), so an alias and not the title. */
$t = Entry::find()->id(30243)->status(null)->one(); $al = array_filter(array_map('trim', explode("\n", (string)$t->personAliases)));
if (!in_array('Ernest L. Tichenor Jr.', $al, true)) { echo "APPROVED #30243 alias: Ernest L. Tichenor Jr.\n";
  if ($APPLY) { $al[] = 'Ernest L. Tichenor Jr.'; $t->setFieldValue('personAliases', implode("\n", $al)); if (!$el->saveElement($t)) { throw new \RuntimeException('#30243'); } $n++; } }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('move_leads_off_notes_2026_10_05.php', $n, 'verified', 'leads off public notes into researchLeads' . ($APPLY_SWEEP ? ', with the sweep' : ', approved only')); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
