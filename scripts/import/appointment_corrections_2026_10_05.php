/**
 * The appointment corrections (Nathan, 5 October 2026), from inventory/review/appointed-terms-audit-2026-10-05.md:
 * 1. Gibbs 2024 and Plambeck 2011 elected unopposed too ("The statutes differ but the fact is the same, and a reader should not
 *    need to know which code governs a seat. Use 'Elected unopposed; no election was held' with the statute and the appointing
 *    act in the footnote").
 * 2. Umeck, Walters, Dinsenbacher and Martin split at each election ("Split the four records at each election"): the record
 *    keeps its first span, the vacancy appointment, and a new office holding is made for each later term, from the elections the
 *    audit found (CEDA rows; Leon Worden's Hart roster; KHTS for Martin, whose 2014 count is not held).
 * 3. Messina's 2022 term elected (CEDA 2022, row 5281: 12,552 of 21,054).
 * 4. The four notes that read CEDA's incumbent flag as "an earlier appointment" (Emmons, Tannehill, Olsen, Wigdor) say instead
 *    that no source here establishes an appointment. What would settle each: the County's returns or the district's minutes for
 *    the years before the CEDA file begins or that it lacks (Emmons before 1995, Tannehill 2001, Olsen before 2016, Wigdor 1997).
 * 5. "appointed as the sole candidate; no election held" anywhere else in an office holding's notes becomes "elected unopposed;
 *    no election was held".
 * Idempotent: a split term is matched by person, body and start. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/appointment_corrections_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0; $sec = Craft::$app->getEntries()->getSectionByHandle('officeHoldings');
$rowsOf = fn($e, $h, $keys) => array_values(array_filter(array_map(fn($r) => array_combine($keys, array_map(fn($k) => (string)($r[$k] ?? ''), $keys)), iterator_to_array($e->getFieldValue($h) ?? [])), fn($r) => $r['note'] !== ''));
$FK = ['number', 'note', 'source']; $EK = ['heading', 'note', 'position'];
$save = function ($e, $vals, $why) use (&$n, $el, $APPLY) { echo "  $why\n"; if (!$APPLY) { return; } $e->setFieldValues($vals); if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); } $n++; };
$get = fn($id) => Entry::find()->section('officeHoldings')->id($id)->status(null)->one() ?? throw new \RuntimeException("no #$id");

/* 1. Gibbs and Plambeck */
$G = $get(27410); $P = $get(29659);
$gn = $rowsOf($G, 'footnotes', $FK); $gch = false;
foreach ($gn as &$r) { if (str_starts_with($r['note'], 'Appointed as the sole candidate; no election held.')) { $r['note'] = 'Elected unopposed; no election was held.' . substr($r['note'], strlen('Appointed as the sole candidate; no election held.')) . ' Under Elections Code section 10229 the City Council, when the nominees do not outnumber the offices, may "appoint to the office the person who has been nominated," and it did so in August 2024 (The Signal, "Council names Gibbs to District 3 seat," August 2024).'; $gch = true; } } unset($r);
echo "#27410 Gibbs: " . ((string)$G->selectionMethod->value === 'unopposed' && !$gch ? 'done already' : 'unopposed, the footnote reworded with Elections Code 10229 and the Council\'s appointment') . PHP_EOL;
if ($gch || (string)$G->selectionMethod->value !== 'unopposed') { $save($G, ['selectionMethod' => 'unopposed', 'footnotes' => $gn], 'Gibbs'); }
$pn = $rowsOf($P, 'footnotes', $FK); $pch = false;
foreach ($pn as &$r) { if (str_starts_with($r['note'], 'Appointed without an election; the candidates did not outnumber the seats.')) { $r['note'] = 'Elected unopposed; no election was held, the candidates not outnumbering the seats.' . substr($r['note'], strlen('Appointed without an election; the candidates did not outnumber the seats.')); $pch = true; } } unset($r);
echo "#29659 Plambeck: " . ((string)$P->selectionMethod->value === 'unopposed' && !$pch ? 'done already' : 'unopposed; the footnote already gives Elections Code 10515 and the Board of Supervisors\' certification') . PHP_EOL;
if ($pch || (string)$P->selectionMethod->value !== 'unopposed') { $save($P, ['selectionMethod' => 'unopposed', 'footnotes' => $pn], 'Plambeck'); }

/* 3. Messina 2022 */
$M = $get(28936); $mn = $rowsOf($M, 'footnotes', $FK); $old = 'How the term was won (an election, or appointment in lieu of one) is not yet held.';
$mch = false; foreach ($mn as &$r) { if (str_contains($r['note'], $old)) { $r['note'] = str_replace($old, 'He won it at the election of November 8, 2022 for Trustee Area 5, with 12,552 of 21,054 votes against one other candidate (California Elections Data Archive (CEDA), CEDA2022Data.xlsx, row 5281, a compilation of the County\'s returns); the County\'s list of elections cancelled that November does not include the Hart district.', $r['note']); $mch = true; } } unset($r);
echo "#28936 Messina 2022: " . ((string)$M->selectionMethod->value === 'elected' && !$mch ? 'done already' : 'elected, with the count') . PHP_EOL;
if ($mch || (string)$M->selectionMethod->value !== 'elected') { $save($M, ['selectionMethod' => 'elected', 'footnotes' => $mn], 'Messina'); }

/* 4. The four notes */
foreach ([28531 => 'Nora Emmons', 28469 => 'Steven Tannehill', 28481 => 'Julie Olsen', 28511 => 'Sheldon Wigdor'] as $id => $name) {
  $h = $get($id); $vals = [];
  foreach (['footnotes' => $FK, 'editorNotes' => $EK] as $fh => $keys) { $rows = $rowsOf($h, $fh, $keys); $ch = false;
    foreach ($rows as &$r) { $o = "$name stood as the incumbent: an earlier appointment to the board, whose date is not recorded here.";
      if (str_contains($r['note'], $o)) { $r['note'] = str_replace($o, "$name stood as the incumbent. How $name first joined the board is not known: no source here records an appointment, and the County's returns for the earlier years are not held.", $r['note']); $ch = true; } } unset($r);
    if ($ch) { $vals[$fh] = $rows; } }
  echo "#$id $name: " . ($vals ? 'the note reworded' : 'done already') . PHP_EOL; if ($vals) { $save($h, $vals, $name); }
}

/* 2. The splits. [record, its first span's end, new terms [start, startEdtf, end, endEdtf, method, evidence, howEnded, note]] */
$SPLIT = [
  28229 => ['first' => ['December 1997', '1997-12', 'reelected', 'derived'], 'person' => 'Judy Umeck', 'terms' => [
    ['December 1997', '1997-12', 'December 2001', '2001-12', 'elected', 'roster', 'unknown', 'She won the election of November 4, 1997 as the incumbent, with 1,350 votes (California Elections Data Archive (CEDA), CEDA1997Data.xls, row 1321, a compilation of the County\'s returns).'],
    ['December 2001', '2001-12', 'December 2005', '2005-12', '', 'derived', 'reelected', 'How this term was filled is not known: the board\'s 2001 contest is not in the CEDA file, and the County\'s return is not held. She was on the board throughout (the district\'s release of December 14, 2018: a member "since first being appointed to fill a vacancy in 1996").'],
    ['December 2005', '2005-12', 'December 2009', '2009-12', 'elected', 'roster', 'unknown', 'She won the election of November 8, 2005 as the incumbent, with 10,561 votes (CEDA, CEDA2005Data.xls, row 1663).'],
    ['December 2009', '2009-12', 'December 2013', '2013-12', '', 'derived', 'reelected', 'How this term was filled is not known: the board\'s 2009 contest is not in the CEDA file, and the County\'s return is not held. She was on the board throughout.'],
    ['December 2013', '2013-12', 'December 11, 2018', '2018-12-11', 'elected', 'roster', 'expired', 'She won the election of November 5, 2013 as the incumbent, as Judy Egan Umeck, with 2,944 votes (CEDA, CEDA2013Data.xlsx, row 1402). She stood for Trustee Area 2 on November 6, 2018 and lost, with 3,198 votes (CEDA2018Data.xlsx, row 4921), and left the board at its meeting of December 11, 2018.']]],
  28264 => ['first' => ['December 2013', '2013-12', 'reelected', 'derived'], 'person' => 'Brian Walters', 'terms' => [
    ['December 2013', '2013-12', 'December 2018', '2018-12', 'elected', 'roster', 'reelected', 'He won the election of November 5, 2013 at large as the incumbent, with 1,915 of 7,576 votes (California Elections Data Archive (CEDA), CEDA2013Data.xlsx, row 1350, a compilation of the County\'s returns). The district moved its elections to even years, so the term ran to December 2018.']]],
  28803 => ['first' => ['December 1989', '1989-12', 'reelected', 'roster'], 'person' => 'William Dinsenbacher', 'terms' => [
    ['December 1989', '1989-12', 'December 1993', '1993-12', 'elected', 'roster', 'reelected', 'Leon Worden\'s roster of the Hart board lists him on the board of 12-5-1989 to 11-30-1990 as "William S. Dinsenbacher, Clerk ([re]elected 1989)" (SCVHistory.com, /scvhistory/hartschoolboardmembers.htm). The return of 1989 is not held.'],
    ['December 1993', '1993-12', 'December 1997', '1997-12', 'elected', 'roster', 'expired', 'The roster lists him on the board of 12-8-1993 to 11-30-1994 as "William S. Dinsenbacher (reelected 1993)" and last on that of 12-11-1996 to 11-30-1997 as "(defeated 1997)". He stood on November 4, 1997 and lost: fourth of 4 candidates for 3 seats, with 3,661 votes (CEDA1997Data.xls, row 1388).']]],
  28225 => ['first' => ['December 2014', '2014-12', 'reelected', 'derived'], 'person' => 'Gary Martin', 'terms' => [
    ['December 2014', '2014-12', 'December 2017', '2017-12-31', 'elected', 'derived', 'left', 'He stood at the election of November 4, 2014 as an incumbent for a seat at large ("Fortner is expected to run against incumbents Gary Martin and Tom Campbell for a seat as a member at large": KHTS, "Candidates File For CLWA Governing Board Election," August 11, 2014, https://scvnews.com/candidates-file-for-clwa-governing-board-election) and served on to 2017, so he is read as having won; the County\'s return is not held. The agency ended on January 1, 2018, when it merged into the Santa Clarita Valley Water Agency.']]],
];
foreach ($SPLIT as $id => $s) {
  $h = $get($id); [$fEnd, $fEndE, $fHow, $fEv] = $s['first'];
  $person = $h->holdingPerson->status(null)->ids(); $body = $h->holdingBody->status(null)->ids(); $office = $h->holdingOffice->status(null)->ids(); $dist = in_array('holdingDistrict', array_map(fn($f) => $f->handle, $h->getFieldLayout()->getCustomFields()), true) ? $h->holdingDistrict->status(null)->ids() : [];
  $shortNow = (string)$h->termEndEdtf === $fEndE;
  echo "#$id {$s['person']}: " . ($shortNow ? 'first span ends ' . $fEnd . ' already' : "first span (appointed) now ends $fEnd, was {$h->termEnd}") . PHP_EOL;
  if (!$shortNow) { $save($h, ['termEnd' => $fEnd, 'termEndEdtf' => $fEndE, 'howEnded' => $fHow, 'endEvidence' => $fEv], 'shorten'); }
  foreach ($s['terms'] as [$ts, $tsE, $te, $teE, $meth, $ev, $how, $note]) {
    $have = array_filter(Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $person, 'field' => 'holdingPerson'], ['targetElement' => $body, 'field' => 'holdingBody']])->all(), fn($x) => (string)$x->termStartEdtf === $tsE);
    echo "  $ts to $te, " . ($meth ?: 'method not known') . ": " . ($have ? 'exists' : 'create') . PHP_EOL;
    if ($have || !$APPLY) { continue; }
    $x = new Entry(); $x->sectionId = $sec->id; $x->setTypeId($sec->getEntryTypes()[0]->id);
    $v = ['holdingPerson' => $person, 'holdingOffice' => $office, 'holdingBody' => $body, 'termStart' => $ts, 'termStartEdtf' => $tsE, 'termEnd' => $te, 'termEndEdtf' => $teE,
      'selectionMethod' => $meth, 'howEnded' => $how, 'startEvidence' => $ev, 'endEvidence' => $how === 'unknown' ? 'derived' : ($ev === 'roster' ? 'roster' : 'derived'),
      'footnotes' => [['number' => '1', 'note' => $note, 'source' => 'editorial-2026']], 'recordProvenance' => 'appointment_corrections_2026_10_05.php, 5 October 2026: split from #' . $id];
    if ($dist) { $v['holdingDistrict'] = $dist; }
    $x->setFieldValues($v); if (!$el->saveElement($x)) { throw new \RuntimeException("{$s['person']} $ts " . json_encode($x->getFirstErrors())); } $n++;
  }
}

/* 5. The phrase anywhere else */
$k = 0;
foreach (Entry::find()->section('officeHoldings')->status(null)->limit(null)->all() as $h) {
  $rows = $rowsOf($h, 'footnotes', $FK); $ch = false;
  foreach ($rows as &$r) { $new = str_ireplace(['appointed as the sole candidate; no election held'], ['elected unopposed; no election was held'], $r['note']); $new = preg_replace('~(^|\. )elected unopposed~', '$1Elected unopposed', $new); if ($new !== $r['note']) { $r['note'] = $new; $ch = true; } } unset($r);
  if ($ch) { $k++; echo "#{$h->id} {$h->title}: the phrase reworded\n"; if ($APPLY) { $h->setFieldValue('footnotes', $rows); if (!$el->saveElement($h)) { throw new \RuntimeException("#{$h->id}"); } $n++; } }
}
echo "$k more notes with the phrase\n";
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('appointment_corrections_2026_10_05.php', $n, 'verified', 'Gibbs, Plambeck unopposed; four records split at each election; Messina 2022 elected; four unsourced appointments reworded'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
