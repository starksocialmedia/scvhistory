/**
 * Three held items, applied after the staging refresh of 3 October 2026 (Nathan:
 * "Smyth's old portrait attached, and Jensen's certified 11,639 with the missing
 * Hart 2022 Area 2 election and candidacy records").
 *
 * 1. Cameron Smyth #16380: his former portrait, #21579 (Nathan Imhoff's
 *    photograph), joins his record's images, under the standing rule that a
 *    replaced portrait is kept as a related image (swap_smyth_portrait.php kept
 *    the asset but did not attach it).
 * 2. Bob Jensen's Hart Trustee Area 2 holding of 2022 (#28584): the certified
 *    count. The County's Final Official Statement of Votes Cast for 8 November
 *    2022 (page 169, certified 5 December 2022; read by Grok Bot,
 *    inventory/review/hart-district-ta2-2022.json) gives Bob Jensen 11,639, Andrew
 *    Taban 5,736, 21,104 ballots cast, 36,496 registered. The County's precinct
 *    spreadsheet, summed, gives 11,638 and 21,103; the footnote keeps both, and
 *    the certified statement is the figure stated.
 * 3. The contest itself, which CEDA omits: an election record (Hart district,
 *    Trustee Area 2, 8 November 2022) and two candidacies, Bob Jensen (#28322,
 *    elected) and Andrew Taban (not elected; a losing candidate is a name on the
 *    ballot, not a record), on certified evidence.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_hart_2022_area2_and_smyth.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$bad = [];

/* Grok's reading of the certified statement, checked before use. */
$g = json_decode(file_get_contents("$root/inventory/review/hart-district-ta2-2022.json"), true);
$res = array_column($g['contest']['results'] ?? [], null, 'candidate');
if (($res['Bob Jensen']['votes'] ?? 0) !== 11639 || ($res['Andrew Taban']['votes'] ?? 0) !== 5736 || ($g['contest']['ballots_cast'] ?? 0) !== 21104 || ($g['contest']['registration'] ?? 0) !== 36496 || ($g['election']['certified_date'] ?? '') !== '2022-12-05') { $bad[] = 'the certified figures in hart-district-ta2-2022.json are not as read'; }
$SOV = 'County of Los Angeles, Registrar-Recorder/County Clerk, Final Official Statement of Votes Cast, General Election, November 8, 2022, page 169 (certified December 5, 2022), https://content.lavote.gov/docs/rrcc/svc/4300_final_svc_countywide.pdf: William S. Hart Union High School District, Governing Board Member, Trustee Area No. 2: Bob Jensen 11,639 (66.99 per cent), Andrew Taban 5,736 (33.01 per cent); 36,496 registered, 21,104 ballots cast.';
$SVC = 'The County\'s precinct spreadsheet for the contest (Statement of Votes Cast, zip 4300, WM_S._HART_UNION_HIGH-TR_2), its rows summed, gives Bob Jensen Jr 11,638 and 21,103 ballots: one fewer of each than the certified statement, which is the figure stated.';
$CEDA = 'The California Elections Data Archive (CEDA) omits this contest.';

/* 1. Smyth. */
$sm = $get(16380); $old = Asset::find()->id(21579)->one();
if (!$sm || $sm->title !== 'Cameron Smyth' || !$old || $sm->featuredImage->one()?->filename !== 'cameron-smyth-2017.jpg') { $bad[] = 'Smyth\'s record or portraits are not as expected'; }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$smDone = $sm && in_array(21579, $sm->recordImages->status(null)->ids());
echo 'Cameron Smyth: #21579 ' . ($smDone ? 'already in his images' : '-> his record\'s images (the portrait stays cameron-smyth-2017.jpg)') . PHP_EOL;

/* 2. Jensen's holding. */
$h = $get(28584); $OLDN = 'Bob Jensen Jr 11,638, Andrew Taban 5,736, of 21,103 ballots cast in the area. The California Elections Data Archive (CEDA) omits this contest.';
$rows = $h ? array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? 'editorial-2026')], $h->footnotes ?? []) : [];
$hDone = $rows && str_contains($rows[0]['note'], '11,639');
if (!$h || $h->holdingPerson->one()?->id !== 28322 || $h->termStartEdtf !== '2022-12') { $bad[] = '#28584 is not Jensen\'s 2022 Hart holding'; }
elseif (!$hDone && !str_contains($rows[0]['note'] ?? '', $OLDN)) { $bad[] = '#28584 footnote 1 is not as read'; }
$NEW1 = 'Elected on November 8, 2022, first of 2 candidates for Trustee Area 2. ' . $SOV . ' ' . $SVC . ' ' . $CEDA;
echo '#28584 Jensen 2022: ' . ($hDone ? 'already certified' : "footnote 1 -> $NEW1") . PHP_EOL;

/* 3. The election and its candidacies. */
$J = $get(28322); $TA2 = $get(25317); $HART = $get(21588);
if (!$J || $J->title !== 'Bob Jensen' || !$TA2 || $TA2->title !== 'William S. Hart Union High School District, Trustee Area 2' || !$HART) { $bad[] = 'Jensen, Trustee Area 2 or the Hart district is not where expected'; }
$elec = Entry::find()->section('elections')->status(null)->electionDateEdtf('2022-11-08')->relatedTo(['and', ['targetElement' => 21588, 'field' => 'electionBody'], ['targetElement' => 25317, 'field' => 'electionDistrict']])->one();
echo 'Election, Hart Trustee Area 2, November 8, 2022: ' . ($elec ? "exists #{$elec->id}" : 'create (certified)') . '; candidacies Bob Jensen (elected, 11,639) and Andrew Taban (not elected, 5,736)' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$smDone) { $sm->setFieldValue('recordImages', array_values(array_unique(array_merge($sm->recordImages->status(null)->ids(), [21579])))); if (!$el->saveElement($sm)) { throw new \RuntimeException('Smyth: ' . json_encode($sm->getFirstErrors())); } }
    if (!$hDone) { $rows[0]['note'] = $NEW1; $h->setFieldValues(['footnotes' => $rows, 'startEvidence' => 'certified']); if (!$el->saveElement($h)) { throw new \RuntimeException('#28584: ' . json_encode($h->getFirstErrors())); } }
    if (!$elec) {
        $es = $svc->getSectionByHandle('elections'); $elec = new Entry(); $elec->sectionId = $es->id; $elec->setTypeId($es->getEntryTypes()[0]->id);
        $elec->slug = 'hart-district-board-election-november-8-2022-trustee-area-2';
        $elec->setFieldValues(['electionDate' => 'November 8, 2022', 'electionDateEdtf' => '2022-11-08', 'electionKind' => 'general', 'seatsUp' => 1, 'seatsUpEvidence' => 'certified',
            'registeredVoters' => 36496, 'ballotsCast' => 21104, 'electionBody' => [21588], 'electionDistrict' => [25317],
            'footnotes' => $fn([$SOV, $SVC . ' ' . $CEDA]), 'recordProvenance' => 'apply_hart_2022_area2_and_smyth.php, 3 October 2026: the contest CEDA omits, from the County\'s certified statement']);
        if (!$el->saveElement($elec)) { throw new \RuntimeException('election: ' . json_encode($elec->getFirstErrors())); }
    }
    $cs = $svc->getSectionByHandle('candidacies');
    foreach ([['BOB JENSEN JR', 11639, 'elected', [28322], 'person:bob-jensen', 'The incumbent.'], ['ANDREW TABAN', 5736, 'not-elected', [], 'person:andrew-taban', '']] as [$name, $v, $out, $pid, $key, $extra]) {
        if (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $elec, 'field' => 'candidacyElection'])->nameAsPrinted($name)->exists()) { continue; }
        $c = new Entry(); $c->sectionId = $cs->id; $c->setTypeId($cs->getEntryTypes()[0]->id);
        $c->setFieldValues(['candidacyElection' => [$elec->id], 'candidacyPerson' => $pid, 'nameAsPrinted' => $name, 'votesAsPrinted' => (string)$v, 'votes' => $v, 'outcome' => $out, 'outcomeEvidence' => 'certified',
            'candidacyDistrict' => [25317], 'candidateKey' => $key, 'footnotes' => $fn([trim('The County\'s certified Statement of Votes Cast, November 8, 2022, page 169: ' . number_format($v) . ' votes. ' . $extra)]),
            'recordProvenance' => 'apply_hart_2022_area2_and_smyth.php, 3 October 2026']);
        if (!$el->saveElement($c)) { throw new \RuntimeException("candidacy $name: " . json_encode($c->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$e2 = Entry::find()->section('elections')->status(null)->electionDateEdtf('2022-11-08')->relatedTo(['and', ['targetElement' => 21588, 'field' => 'electionBody'], ['targetElement' => 25317, 'field' => 'electionDistrict']])->one();
$nc = $e2 ? (int)Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e2, 'field' => 'candidacyElection'])->count() : 0;
$ok = $e2 && $nc === 2 && in_array(21579, $get(16380)->recordImages->ids()) && str_contains((string)($get(28584)->footnotes[0]['note'] ?? ''), '11,639');
echo 'READ-BACK ' . ($ok ? "OK: election #{$e2->id} ({$e2->title}), $nc candidacies; Jensen certified; Smyth's #21579 attached" : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('apply_hart_2022_area2_and_smyth.php', 5, $ok ? 'verified' : 'SHORT', 'Hart Trustee Area 2, 2022: election and candidacies (CEDA omits it), Jensen 11,639 certified; Smyth\'s former portrait attached');
if (!$ok) { throw new \RuntimeException('apply_hart_2022_area2_and_smyth: read-back failed'); }
