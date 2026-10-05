/**
 * Hart district, 1993 and 1994, from sources outside Leon Worden's roster (inventory/review/hart-pre1995-terms-2026-10-05.md;
 * the pages in inventory/news/hart-pre1995-2026-10-05/). Nathan, 5 October 2026: "apply Aliano's May 1994 appointment and
 * Warren's March 1994 resignation, with the roster's dates shown as the disagreement they are"; Loberg and King "are
 * candidacies rather than terms, so check whether the model already handles a loss".
 *  1. George Aliano (#28602): appointed May 1994 (the Los Angeles Times of May 17, 1994: chosen that weekend, to be sworn
 *     in the Wednesday). The roster prints no date for the appointment.
 *  2. Peter Warren (#28805): resigned March 1994 (the same article). The roster gives 6 April 1994; both are footnoted.
 *  3. The election of November 2, 1993, three seats at large, from the Times's final returns: Patricia Hanrion, Peter C.
 *     Warren and William Dinsenbacher elected; Sandra Loberg and Dennis King, the incumbents, not elected. The model holds a
 *     loss already (outcome "Not elected", shown on the person's page under Other races); the election was what was missing.
 *     The roster says Loberg and King "did not seek reelection in 1993"; their terms keep howEnded "expired" (as
 *     Dinsenbacher's 1997 defeat is held) and gain the returns and the disagreement.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_hart_1993_1994_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$ALGER = 'Douglas Alger, "Aliano to Join Hart School Board," Los Angeles Times, May 17, 1994, https://www.latimes.com/archives/la-xpm-1994-05-17-me-58785-story.html';
$RET = '"Final Election Returns," Los Angeles Times, November 4, 1993, https://www.latimes.com/archives/la-xpm-1993-11-04-me-53120-story.html';
$WRAP = 'Douglas Alger, "Election Wrapup: Conservatives Gain Board Seats in Santa Clarita School Districts," Los Angeles Times, November 4, 1993, https://www.latimes.com/archives/la-xpm-1993-11-04-me-53137-story.html';
$FILED = 'Jack Cheevers and John Chandler, "Deadlines Extended for Council, Board Races," Los Angeles Times, August 7, 1993, https://www.latimes.com/archives/la-xpm-1993-08-07-me-21422-story.html';
$RETQ = "$RET: \"William S. Hart Union High School District 3 Elected 100% Precincts Reporting: votes (%) Patricia Hanrion: 9,794 (22%) Peter C. Warren: 9,126 (21%) W. Dinsenbacher*: 8,943 (20%) Sandra L. Loberg*: 8,506 (19%) Dennis V. King*: 7,934 (18%)\" (an asterisk marks an incumbent).";
$fnRows = fn($e) => array_values(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], array_filter(iterator_to_array($e->footnotes ?? []), fn($r) => trim((string)$r['note']) !== '')));
$edRows = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], array_filter(iterator_to_array($e->editorNotes ?? []), fn($r) => trim((string)$r['note']) !== '')));
$add = function ($rows, $note) { if (in_array($note, array_column($rows, 'note'), true)) { return $rows; } $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $note, 'source' => 'editorial-2026']; return $rows; };
$H = [];
// 1. Aliano
$H[28602] = function ($h) use ($add, $fnRows, $ALGER) {
  $fn = $add($fnRows($h), "$ALGER: \"George Aliano was chosen from eight applicants interviewed this weekend by the four other board members to fill the William S. Hart Union High School District trustee post that Peter Warren vacated in March. ... Aliano is scheduled to be sworn in Wednesday. ... Trustees opted to fill the seat by appointment rather than pay for a special election.\" The Wednesday was May 18, 1994.");
  $fn = array_map(fn($r) => str_starts_with($r['note'], 'The roster prints no date for the appointment') ? ['note' => str_replace('; the year given here is that resignation\'s.', '. The date of the appointment is the Los Angeles Times\'s (note 4).', $r['note'])] + $r : $r, $fn);
  return ['termStart' => 'May 1994', 'termStartEdtf' => '1994-05', 'startEvidence' => 'contemporary', 'footnotes' => $fn];
};
// 2. Warren
$H[28805] = function ($h) use ($add, $fnRows, $edRows, $ALGER, $RETQ) {
  $fn = $add($fnRows($h), "$ALGER: \"Peter Warren vacated in March\"; \"Warren, 36, resigned in March, 105 days after he was elected.\"");
  $fn = $add($fn, $RETQ);
  $ed = $edRows($h); $n = 'Leon Worden\'s roster gives his resignation as 6 April 1994 (note 1); the Los Angeles Times reported in May 1994 that he resigned in March (note 3). The Times is followed here, and the two disagree.';
  if (!in_array($n, array_column($ed, 'note'), true)) { $ed[] = ['heading' => 'Resignation date', 'note' => $n, 'position' => 'bottom']; }
  return ['termEnd' => 'March 1994', 'termEndEdtf' => '1994-03', 'endEvidence' => 'contemporary', 'footnotes' => $fn, 'editorNotes' => $ed];
};
// 3. Loberg and King: the returns and the disagreement
foreach ([28801 => 'Sandra Loberg', 28592 => 'Dennis King'] as $hid => $who) {
  $H[$hid] = function ($h) use ($add, $fnRows, $edRows, $RETQ, $WRAP, $FILED, $who) {
    $fn = $add($fnRows($h), "$FILED: \"All three trustees for the William S. Hart Union High School District have filed: William Dinsenbacher, Dennis King and Sandra Loberg.\" $RETQ $WRAP: \"Warren and fellow newcomer Patricia Hanrion toppled two incumbents.\"");
    $ed = $edRows($h); $n = "Leon Worden's roster says $who did not seek reelection in 1993 (note 1). The Los Angeles Times reported that $who filed, stood at the election of November 2, 1993, and lost (note 3). The roster is wrong here, as it is once about Patricia Hanrion.";
    if (!in_array($n, array_column($ed, 'note'), true)) { $ed[] = ['heading' => 'The 1993 election', 'note' => $n, 'position' => 'bottom']; }
    return ['footnotes' => $fn, 'editorNotes' => $ed];
  };
}
$C = [ // name as printed, votes, outcome, person id
  ['Patricia Hanrion', 9794, 'elected', 25437], ['Peter C. Warren', 9126, 'elected', 28719], ['W. Dinsenbacher', 8943, 'elected', 28717],
  ['Sandra L. Loberg', 8506, 'not-elected', 28715], ['Dennis V. King', 7934, 'not-elected', 25439]];
$bad = [];
foreach ($H as $id => $f) { $h = Entry::find()->id($id)->status(null)->one(); if (!$h) { $bad[] = "#$id missing"; continue; } echo "#$id {$h->title}: {$h->termStartEdtf} to {$h->termEndEdtf}" . PHP_EOL; }
foreach ($C as [, , , $pid]) { if (!Entry::find()->section('persons')->id($pid)->status(null)->exists()) { $bad[] = "person #$pid missing"; } }
$elec = Entry::find()->section('elections')->status(null)->electionDateEdtf('1993-11-02')->relatedTo(['targetElement' => 21588, 'field' => 'electionBody'])->one();
echo 'Election, Hart district, November 2, 1993: ' . ($elec ? "exists #{$elec->id}" : 'create, 3 seats, 5 candidacies') . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
  foreach ($H as $id => $f) { $h = Entry::find()->id($id)->status(null)->one(); $h->setFieldValues($f($h)); if (!$el->saveElement($h)) { throw new \RuntimeException("#$id " . json_encode($h->getFirstErrors())); } $n++; }
  if (!$elec) {
    $es = $svc->getSectionByHandle('elections'); $elec = new Entry(); $elec->sectionId = $es->id; $elec->setTypeId($es->getEntryTypes()[0]->id);
    $elec->slug = 'hart-district-board-election-november-2-1993';
    $elec->setFieldValues(['electionDate' => 'November 2, 1993', 'electionDateEdtf' => '1993-11-02', 'electionKind' => 'general', 'seatsUp' => 3, 'seatsUpEvidence' => 'contemporary', 'electionBody' => [21588],
      'footnotes' => [['number' => '1', 'note' => $RETQ, 'source' => 'editorial-2026'], ['number' => '2', 'note' => "$WRAP: \"Warren and fellow newcomer Patricia Hanrion toppled two incumbents.\"", 'source' => 'editorial-2026']],
      'recordProvenance' => 'apply_hart_1993_1994_2026_10_05.php, 5 October 2026: the contest before the archive\'s CEDA years, from the Los Angeles Times']);
    if (!$el->saveElement($elec)) { throw new \RuntimeException('election ' . json_encode($elec->getFirstErrors())); } $n++;
  }
  $cs = $svc->getSectionByHandle('candidacies'); $rank = 0;
  foreach ($C as [$name, $v, $out, $pid]) { $rank++;
    if (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $elec, 'field' => 'candidacyElection'])->nameAsPrinted($name)->exists()) { continue; }
    $c = new Entry(); $c->sectionId = $cs->id; $c->setTypeId($cs->getEntryTypes()[0]->id);
    $c->setFieldValues(['candidacyElection' => [$elec->id], 'candidacyPerson' => [$pid], 'nameAsPrinted' => $name, 'votesAsPrinted' => number_format($v), 'votes' => $v, 'outcome' => $out, 'outcomeEvidence' => 'contemporary',
      'footnotes' => [['number' => '1', 'note' => "$RET: " . number_format($v) . " votes, $rank of 5 for three seats.", 'source' => 'editorial-2026']], 'recordProvenance' => 'apply_hart_1993_1994_2026_10_05.php, 5 October 2026']);
    if (!$el->saveElement($c)) { throw new \RuntimeException("candidacy $name " . json_encode($c->getFirstErrors())); } $n++;
  }
  $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK: ' . $t->getMessage() . PHP_EOL; throw $t; }
$nc = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $elec, 'field' => 'candidacyElection'])->count();
$a = Entry::find()->id(28602)->status(null)->one(); $w = Entry::find()->id(28805)->status(null)->one();
if ($nc !== 5 || $a->termStartEdtf !== '1994-05' || $w->termEndEdtf !== '1994-03') { throw new \RuntimeException("not read back: $nc candidacies"); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('apply_hart_1993_1994_2026_10_05.php', $n, 'verified', 'Hart: Aliano appointed May 1994, Warren resigned March 1994 (roster dates footnoted); the November 2, 1993 election with its five candidacies, Loberg and King not elected');
echo "done: $n writes (election #{$elec->id})" . PHP_EOL;
