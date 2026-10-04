/**
 * Antonio del Valle's death and Magdalena's mother, from the mission registers (Nathan, 4 October
 * 2026: "Change his record to 'died on or before 3 June 1841', sourced to the burial register, with
 * a note that both earlier dates are impossible and where each came from ... Magdalena was not
 * Jacoba's daughter ... Record what the baptism says, nothing beyond it").
 *
 * Grok Bot's reading (inventory/review/a-b-perkins-sources.json K14, ygnacio-del-valle-sources.json
 * C3 and C4; the two index pages checked against screenshots on 4 October):
 *   Mission San Fernando burial 02257 (ECPP 72898): "Antonio Valle", buried 3 June 1841, married,
 *     spouse "Lopez [Feliz], Jacoba", no death date. So he died on or before 3 June 1841, and both
 *     21 June (Reynolds, part 14, and most of the old site) and 12 June (Perkins, 1957) are after
 *     his burial.
 *   Santa Barbara Presidio baptism 00917 (ECPP 976), 4 July 1831: "Maria Magdalena Antonia", born
 *     the evening before; father "Antonio Valles", of Guadalajara, alferez, widower; mother "Maria
 *     Policarpa Lopez"; legitimacy code "n".
 * The three caveats Nathan set, carried on the record: the ECPP is a typed index of the registers,
 * not the register pages; that "Antonio Valle(s)" is Antonio del Valle is an inference (for the
 * baptism, from town, rank and family); the legitimacy code is given as the ECPP's guide defines it
 * and no further.
 *
 *   #291 Antonio del Valle   body: the death sentence rewritten, the baptism added; footnotes 7 and
 *                            8; deathDate "on or before 3 June 1841", evidence contemporary; the
 *                            21 June date row becomes the burial, 3 June 1841.
 *   #333 Arthur B. Perkins   body (the archive's own): the sentence on the two dates says the burial
 *                            rules both out; footnote 9.
 *   #27374 Perkins 1957      the archive's reading note corrected; correction notes on the date
 *                            and on Magdalena. #1434, the same text as an article, the same two.
 *   #847 Reynolds part 14    correction notes on the date and on "newly-born Magdalena".
 *   #293 Ygnacio             Leon Worden's text: a correction note on the date.
 *   #3991 to #3999           Leon Worden's branding-iron captions (LW2526a to e): a correction note.
 *   #293, #915               the 21 June date rows taken off the calendar (rejected), not deleted.
 * Leon Worden's prose and the old texts are not changed. LO3401, LW2052 and SG090199a are not
 * records in the archive; they stay on the list for Leon.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_del_valle_burial_and_magdalena_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$el = Craft::$app->getElements(); $get = fn($id) => Entry::find()->id($id)->status(null)->one();
$bad = []; $plan = [];

$BURIAL = 'Mission San Fernando, burial register, entry 02257: "Antonio Valle," buried in the church on 3 June 1841, married, his wife given as "Lopez [Feliz], Jacoba"; the entry gives no date of death. As indexed by the Early California Population Project, record 72898, https://ecpp.ucr.edu/ecpp/app/user/view/records/death/72898, read 4 October 2026. The index is a typed transcription of the registers, not the register page, which has not been seen; that this Antonio Valle is Antonio del Valle is an inference from the mission, the date and his wife\'s name.';
$BAPTISM = 'Santa Barbara Presidio, baptismal register, entry 00917, 4 July 1831: "Maria Magdalena Antonia," born the evening before; her father "Antonio Valles," of the city of Guadalajara, alférez, a widower ("viudo"); her mother "Maria Policarpa Lopez," of the presidio; godparents Luis Lugo and María Magdalena Lugo. The entry classes the birth with the code "n", which the project\'s Guide for Users gives as "natural", one of the two ways the Franciscans classed a birth, legitimate or not; nothing further is read into it here. As indexed by the Early California Population Project, record 976, https://ecpp.ucr.edu/ecpp/app/user/view/records/baptismal/976, read 4 October 2026: a typed transcription of the register, not the register page. That this Antonio Valles is Antonio del Valle is an inference from his town, his rank and his family.';
$BUR_SHORT = 'the mission\'s burial register, entry 02257, as indexed by the Early California Population Project, record 72898';
$BAP_SHORT = 'the presidio\'s baptismal register, entry 00917, as indexed by the Early California Population Project, record 976';
$DATE_NOTE = fn($d) => "Antonio del Valle was buried at Mission San Fernando on 3 June 1841 ($BUR_SHORT), so he died on or before that day; the $d given here is after his burial and cannot be right.";
$MAG_PERKINS = "Magdalena was not born before Antonio del Valle came to California, nor to his first wife: she was baptized at the Santa Barbara Presidio on 4 July 1831, born the day before, and the register names her mother as María Policarpa López and her father as a widower ($BAP_SHORT; the index is a transcription, not the register page).";
$MAG_REYNOLDS = "Magdalena was not newly born in 1841: she was baptized at the Santa Barbara Presidio on 4 July 1831, and the register names her mother as María Policarpa López, not Jacoba Feliz ($BAP_SHORT; the index is a transcription, not the register page).";
$H = 'Correction, 2026';

$noteRows = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$fnRows = fn($e) => array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? 'editorial-2026')], $e->footnotes ?? []);
$addNotes = function (int $id, array $notes, string $expectTitle) use ($get, $noteRows, $H, &$plan, &$bad) {
    $e = $get($id); if (!$e || !str_contains($e->title, $expectTitle)) { $bad[] = "#$id is not $expectTitle"; return; }
    $rows = $plan[$id]['editorNotes'] ?? $noteRows($e); $added = 0;
    foreach ($notes as $n) { if (array_filter($rows, fn($r) => $r['note'] === $n)) { continue; } $rows[] = ['heading' => $H, 'position' => 'bottom', 'note' => $n]; $added++; }
    if ($added) { $plan[$id]['e'] = $e; $plan[$id]['editorNotes'] = $rows; echo "#$id {$e->title}: $added correction note(s)" . PHP_EOL; }
    else { echo "#$id {$e->title}: notes already there" . PHP_EOL; }
};

/* #291 */
$a = $get(291); $body = (string)$a->body; $fns = $fnRows($a);
$OLD_D = 'He died in June 1841 without a will: on 21 June according to Reynolds, on 12 June according to Perkins, and no record of the death itself has been seen.[2][1]';
$NEW_D = 'He died without a will on or before 3 June 1841, the day he was buried at Mission San Fernando.[7] Jerry Reynolds gives his death as 21 June and Perkins as 12 June; both are after the burial, so neither can be right.[2][1]';
$OLD_W = 'His second wife was Jacoba Feliz.';
$NEW_W = 'His second wife was Jacoba Feliz. A daughter, María Magdalena Antonia, was baptized at the Santa Barbara Presidio on 4 July 1831, born the day before; the register names her mother as María Policarpa López and her father, "Antonio Valles," as a widower.[8]';
if (str_contains($body, $NEW_D)) { echo '#291: already rewritten' . PHP_EOL; }
elseif (substr_count($body, $OLD_D) !== 1 || substr_count($body, $OLD_W) !== 1 || count($fns) !== 6) { $bad[] = '#291 body or footnotes are not as read'; }
else {
    $fns[] = ['number' => '7', 'note' => $BURIAL, 'source' => 'editorial-2026'];
    $fns[] = ['number' => '8', 'note' => $BAPTISM, 'source' => 'editorial-2026'];
    $rd = array_map(function ($r) {
        if (($r['printed'] ?? '') === 'June 21, 1841') { $r['printed'] = $r['col1'] = 'June 3, 1841'; $r['iso'] = $r['col2'] = '1841-06-03'; $r['granularity'] = $r['col3'] = 'day'; $r['label'] = $r['col4'] = 'buried at Mission San Fernando; he died on or before this day'; }
        return $r; }, $a->recordDates ?? []);
    $plan[291] = ['e' => $a, 'body' => str_replace([$OLD_D, $OLD_W], [$NEW_D, $NEW_W], $body), 'footnotes' => $fns, 'deathDate' => 'on or before 3 June 1841', 'deathEvidence' => 'contemporary', 'recordDates' => $rd];
    echo "#291 Antonio del Valle:\n   - $OLD_D\n   + $NEW_D\n   + (after the second wife) " . substr($NEW_W, strlen($OLD_W) + 1) . "\n   + footnotes 7 (burial) and 8 (baptism); deathDate 'on or before 3 June 1841', evidence contemporary; the 21 June date row becomes the burial" . PHP_EOL;
}

/* #333, the archive's own sentence */
$p = $get(333); $pb = (string)$p->body; $pf = $fnRows($p);
$OLD_P = "His figures still need checking: his history of Rancho San Francisco (1957) dates Antonio del Valle's death 12 June 1841, where Jerry Reynolds gives 21 June.[6][7]";
$NEW_P = "His figures still need checking: his history of Rancho San Francisco (1957) dates Antonio del Valle's death 12 June 1841, where Jerry Reynolds gives 21 June, and the mission burial register shows del Valle buried on 3 June, before either.[6][7][9]";
if (str_contains($pb, $NEW_P)) { echo '#333: already rewritten' . PHP_EOL; }
elseif (substr_count($pb, $OLD_P) !== 1 || count($pf) !== 8) { $bad[] = '#333 body or footnotes are not as read'; }
else { $pf[] = ['number' => '9', 'note' => $BURIAL, 'source' => 'editorial-2026']; $plan[333] = ['e' => $p, 'body' => str_replace($OLD_P, $NEW_P, $pb), 'footnotes' => $pf]; echo "#333 Perkins: the dates sentence names the burial; footnote 9" . PHP_EOL; }

/* #27374, the archive's reading note on the document */
$d = $get(27374); $dn = $noteRows($d);
$OLD_R = "In this text he dates Antonio del Valle's death 12 June 1841, where Jerry Reynolds gives 21 June;";
$NEW_R = "In this text he dates Antonio del Valle's death 12 June 1841, where Jerry Reynolds gives 21 June, and the mission burial register shows del Valle buried on 3 June, before either;";
$hitR = array_keys(array_filter($dn, fn($r) => str_contains($r['note'], $OLD_R)));
if (array_filter($dn, fn($r) => str_contains($r['note'], $NEW_R))) { echo '#27374 reading note: already corrected' . PHP_EOL; }
elseif (count($hitR) !== 1) { $bad[] = '#27374 reading note is not as read'; }
else { $dn[$hitR[0]]['note'] = str_replace($OLD_R, $NEW_R, $dn[$hitR[0]]['note']); $plan[27374] = ['e' => $d, 'editorNotes' => $dn]; echo '#27374 reading note: the burial named' . PHP_EOL; }

$addNotes(27374, [$DATE_NOTE('12 June 1841'), $MAG_PERKINS], 'Rancho San Francisco');
$addNotes(1434, [$DATE_NOTE('12 June 1841'), $MAG_PERKINS], 'Rancho San Francisco');
$addNotes(847, [$DATE_NOTE('21 June 1841'), $MAG_REYNOLDS], 'Lord and Master');
$addNotes(293, [$DATE_NOTE('21 June 1841')], 'Ygnacio del Valle');
foreach ([3991, 3993, 3995, 3997, 3999] as $id) { $addNotes($id, [$DATE_NOTE('21 June 1841')], 'Branding Iron'); }

/* the 21 June date rows on #293 and #915: off the calendar */
foreach ([293, 915] as $id) {
    $e = $plan[$id]['e'] ?? $get($id); $n = 0;
    $rd = array_map(function ($r) use (&$n) { if (($r['printed'] ?? '') === 'June 21, 1841' && empty($r['rejected'])) { $r['rejected'] = $r['col6'] = true; $n++; } return $r; }, $e->recordDates ?? []);
    if ($n) { $plan[$id]['e'] = $e; $plan[$id]['recordDates'] = $rd; echo "#$id {$e->title}: the 21 June 1841 date row off the calendar" . PHP_EOL; }
}

if (preg_match('~\x{2014}|inventory/|Grok|Nathan~u', implode(' ', [$BURIAL, $BAPTISM, $NEW_D, $NEW_W, $NEW_P, $NEW_R, $DATE_NOTE('x'), $MAG_PERKINS, $MAG_REYNOLDS]))) { $bad[] = 'an em dash, a repository path or a worker\'s name in the new text'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $id => $x) { $e = $x['e']; unset($x['e']); $e->setFieldValues($x); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } $n++; }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$a2 = $get(291);
$ok = str_contains((string)$a2->body, 'on or before 3 June 1841') && $a2->deathDate === 'on or before 3 June 1841' && count($a2->footnotes) === 8
    && str_contains(json_encode($get(847)->editorNotes, JSON_UNESCAPED_UNICODE), 'Policarpa') && str_contains(json_encode($get(3999)->editorNotes), '02257');
echo 'READ-BACK ' . ($ok ? "OK: $n records" : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_del_valle_burial_and_magdalena_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'Antonio del Valle died on or before 3 June 1841 (burial register); Magdalena\'s 1831 baptism; correction notes on Perkins, Reynolds, Leon\'s captions');
if (!$ok) { throw new \RuntimeException('fix_del_valle_burial: read-back failed'); }
