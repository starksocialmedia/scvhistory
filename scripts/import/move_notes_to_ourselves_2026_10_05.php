/**
 * Notes to ourselves off the page (Nathan, 5 October 2026: "Check whether anything already in editor notes across the whole
 * archive belongs there, not only the hedged ones: anything that is a note to ourselves rather than to a reader"). All 274
 * distinct editor's-note texts were read (inventory/review/editor-notes-ourselves-2026-10-05.md): 260 are for a reader; these
 * are the rest, but Mentry's, which became the work it described (source_fault_mentry_birth_2026_10_05.php). A work item, a record's reason for being made, an edit history or a held-back fact moves to researchLeads,
 * with the heading it was under; the reader keeps what tells them something. Two headings that named the archive's work are
 * renamed, and one citation whose URL was cut to "https" is restored from the script that wrote it
 * (record_district_boards_2026_10_04.php). Exact text, once, or refused. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/move_notes_to_ourselves_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements();
/* [record id, heading, old text, replacement, the lead for researchLeads ('' for none), new heading (null to keep)] */
$M = [
  [29282, 'Why this record exists', "This record was made so the archive's memorials to sheriff's deputies can name the department they served in.", '',
    'The record was made so the memorials to fallen deputies can name the department they served in.', null],
  [27534, 'Elections', "The district's board elections from 1983 to 2009 are in the County's records and are not yet in the archive.", '',
    "The district's board elections of 1983 to 2009 are in the County's returns (CEDA) and are not yet entered as elections and candidacies.", null],
  [26999, 'How this article was checked', " Its first draft said the board-to-council ladder was never climbed. When Buck McKeon's years on the Hart board were added to his record on 1 October 2026, the check caught that draft, and the article was rewritten around him.", '',
    "The first draft said the board-to-council ladder was never climbed; when Buck McKeon's Hart board years were added to his record on 1 October 2026, the figure check caught it and the article was rewritten around him.", null],
  [21582, 'Held for the elections work', 'One source each, held back: final president of the Newhall County Water District and SCV Water vice president from January 2018 (SCV Water press release, 17 January 2018); lost the Division 3 seat in 2020 (preliminary news figures only). ', '',
    'One source each, held back from the profile: final president of the Newhall County Water District and SCV Water vice president from January 2018 (SCV Water press release, 17 January 2018); lost the Division 3 seat in 2020 (preliminary news figures only). Settled by a second source for the presidency and the certified 2020 canvass, entered with her office holdings.', 'Year of birth'],
  [946, 'Restored, 3 October 2026', "This record was briefly merged into John C. Frémont's on 3 October 2026, on the mistaken view that no source names a California Battalion. Three sources in the archive name it, and two place it in this valley, so it has its own record again.", '',
    "Merged into John C. Frémont's record on 3 October 2026 on the mistaken view that no source names a California Battalion, and restored the same day: three sources in the archive name it, and two place it in this valley.", null],
  [16380, 'Held for a second source, and for the elections work', '', '', '', 'His mayoral years and year of birth'],
  [21592, 'Trustee areas', '(Saugus Union School District, "Governing Board," https.)', '(Saugus Union School District, "Governing Board," https://www.saugususd.org/governing-board, read 4 October 2026.)', '', null],
];
$n = 0; $bad = [];
foreach ($M as [$id, $head, $old, $new, $lead, $newHead]) {
  $e = Entry::find()->id($id)->status(null)->one(); if (!$e) { $bad[] = "no #$id"; continue; }
  $rows = array_values(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes ?? [])));
  $leads = trim((string)$e->researchLeads); $haveLead = $lead === '' || str_contains($leads, $lead);
  $hit = array_keys(array_filter($rows, fn($r) => $r['heading'] === $head && ($old === '' || substr_count($r['note'], $old) === 1)));
  $done = array_filter($rows, fn($r) => $r['heading'] === ($newHead ?? $head) && ($new === '' || str_contains($r['note'], $new)));
  if (!$hit && $haveLead && ($done || ($newHead === null && $new === ''))) { echo "#$id [$head]: done already\n"; continue; }
  if (count($hit) !== 1) { $bad[] = "#$id [$head]: the text is not in the note once"; continue; }
  $i = $hit[0]; $after = $old === '' ? $rows[$i]['note'] : trim(str_replace($old, $new, $rows[$i]['note']));
  echo "#$id {$e->title} [$head" . ($newHead ? " -> $newHead" : '') . "]\n  note becomes: " . ($after === '' ? '(row removed)' : $after) . "\n" . ($lead !== '' ? "  lead: $lead\n" : '');
  if (!$APPLY) { continue; }
  if ($after === '') { unset($rows[$i]); } else { $rows[$i]['note'] = $after; if ($newHead) { $rows[$i]['heading'] = $newHead; } }
  $e->setFieldValue('editorNotes', array_values($rows));
  if (!$haveLead) { $e->setFieldValue('researchLeads', trim($leads . "\n\n" . $lead . " (From the editor's note \"$head\", 5 October 2026.)")); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('move_notes_to_ourselves_2026_10_05.php', $n, 'verified', 'notes to ourselves into researchLeads; two headings; one citation'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
