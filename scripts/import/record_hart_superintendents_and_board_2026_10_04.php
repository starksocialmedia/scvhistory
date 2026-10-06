/**
 * The Hart district's superintendents and its present board, from the district's own pages
 * (Nathan, 4 October 2026: "The superintendent box shows H. Clyde Smyth as if he were current. He
 * left the Hart district in 1992 and has been dead for about two decades ... Use them to record the
 * current superintendent and the current board, and check the board page against what we show").
 *
 * Read 4 October 2026 and saved in inventory/news/hart-district-2026-10-04/ (html and text):
 *   superintendent.html            sha256 3ab55a301aea22e4b7b0a94e6876dd7daeb134731609fe07e254b8baf98c0f53
 *   governing-board-members.html   sha256 98c3a2d7b36eebe3cdf95ba63b1a99c6357bf7e604dbd99df78394c97fe0dc6e
 *
 * 1. Michael Vierra: a person record (living: public facts only, from the district's page) and an
 *    affiliation, Superintendent, serving, certified. His start is not on the page and is left blank.
 * 2. H. Clyde Smyth #15985: an affiliation, Superintendent, 1974 or 1975 to 1992, retired. The four
 *    sources on his record disagree on the start (obituary 1975, Man of the Year biography 1974, the
 *    City's "more than 16 years", Leon Worden's "17-year stint") and agree on the end.
 * 3. The board, checked against the district's page:
 *    Ahuja #26969     the page's "current term 2024 - 2028", already in its footnote: the end set.
 *    Messina #28245   one record from 2009 with no end and no seat, though its own footnotes quote
 *                     the page's Trustee Area 5 and "current term 2022 - 2026": ended at December
 *                     2022, and a new holding for Area 5, 2022 to 2026, serving.
 *    Moore            the page says she has served "since 2017"; the archive held only her 2022
 *                     term, so the page said "on the board since 2022". A holding from 2017 to
 *                     December 2022, appointed (her 2022 holding records she stood as the
 *                     incumbent by an earlier appointment).
 *    Jensen, Wilson   agree with the page; nothing changed.
 * Requires add_affiliations.php applied first. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_hart_superintendents_and_board_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$rows = fn($e) => array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? 'editorial-2026')], $e->footnotes ?? []);
$bad = [];

/* The saved pages, checked before anything is taken from them. */
$dir = "$root/inventory/news/hart-district-2026-10-04";
$SHA = ['superintendent.html' => '3ab55a301aea22e4b7b0a94e6876dd7daeb134731609fe07e254b8baf98c0f53', 'governing-board-members.html' => '98c3a2d7b36eebe3cdf95ba63b1a99c6357bf7e604dbd99df78394c97fe0dc6e'];
foreach ($SHA as $f => $h) { if (!is_file("$dir/$f") || hash_file('sha256', "$dir/$f") !== $h) { $bad[] = "$f is missing or changed"; } }
$sup = preg_replace('~\s+~', ' ', (string)@file_get_contents("$dir/superintendent.txt"));
$brd = preg_replace('~\s+~', ' ', (string)@file_get_contents("$dir/governing-board-members.txt"));
$QS = ['it is an honor to serve as Superintendent after serving as both Assistant Superintendent Human Resources and Deputy Superintendent, Educational Services in the District', 'Michael Vierra, Ph.D. Superintendent'];
foreach ($QS as $q) { if (!str_contains($sup, $q)) { $bad[] = "the superintendent page does not read: $q"; } }
$QB = ['Trustee Area No. 1 representative', 'Trustee Area No. 5 representative President', 'current term 2022 - 2026', 'currently serving on the William S. Hart Union High School District Board of Trustees since 2017'];
foreach ($QB as $q) { if (!str_contains($brd, $q)) { $bad[] = "the board page does not read: $q"; } }
$SUP_URL = 'https://www.hartdistrict.org/apps/pages/superintendent';
$BRD_URL = 'https://www.hartdistrict.org/apps/pages/governing-board-members';
$SUP_CITE = "William S. Hart Union High School District, \"Superintendent's Office,\" $SUP_URL, read 4 October 2026: \"it is an honor to serve as Superintendent after serving as both Assistant Superintendent Human Resources and Deputy Superintendent, Educational Services in the District\"; signed \"Michael Vierra, Ph.D. Superintendent.\"";

$aSec = $svc->getSectionByHandle('affiliations'); $aType = $svc->getEntryTypeByHandle('affiliation');
if (!$aSec || !$aType) { $bad[] = 'the affiliations section does not exist yet (apply add_affiliations.php first)'; }
$HART = $get(21588); $ROLE = Entry::find()->section('roles')->status(null)->title('School Superintendent')->one(); $RS = Entry::find()->section('roles')->status(null)->title('School Board Member')->one();
$TA5 = $get(25323); $SM = $get(15985);
if (!$HART || !$ROLE || !$RS || $TA5?->slug !== 'william-s-hart-union-high-school-district-trustee-area-5' || $SM?->title !== 'H. Clyde Smyth') { $bad[] = 'Hart, the roles, Area 5 or Smyth are not where expected'; }

/* 1 and 2: the superintendents. */
$V = Entry::find()->section('persons')->status(null)->title('Michael Vierra')->one();
echo 'Michael Vierra: ' . ($V ? "exists #{$V->id}" : 'create (person; role School Superintendent; one paragraph from the district\'s page)') . PHP_EOL;
$affOf = fn($pid) => $aSec ? Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $pid, 'field' => 'affiliationPerson'], ['targetElement' => 21588, 'field' => 'affiliationBody']])->one() : null;
$smNotes = array_slice($rows($SM), 0, 4);
if (count($smNotes) !== 4 || !str_contains($smNotes[0]['note'], '1975-1992') || !str_contains($smNotes[1]['note'], '"1974,"') || !str_contains($smNotes[3]['note'], '17-year stint')) { $bad[] = 'Smyth\'s four sources are not as read'; }
$SM_AFF = ['Superintendent, 1974 or 1975 to 1992, retired', array_map(fn($r) => $r['note'], $smNotes)];
echo 'Smyth affiliation: ' . ($SM && $affOf($SM->id) ? 'exists' : 'create: ' . $SM_AFF[0] . ', from the four sources on his record') . PHP_EOL;
echo 'Vierra affiliation: ' . ($V && $affOf($V->id) ? 'exists' : 'create: Superintendent, serving, start not given') . PHP_EOL;

/* 3: the board. */
$ah = $get(26969); $me = $get(28245);
$MO = Entry::find()->section('persons')->status(null)->title('Cherise Moore')->one();
if ($ah?->holdingPerson->one()?->title !== 'Aakash Ahuja' || !str_contains(json_encode($ah->footnotes), 'current term 2024 - 2028')) { $bad[] = '#26969 is not Ahuja\'s term with the district\'s quotation'; }
if ($me?->holdingPerson->one()?->title !== 'Joe Messina' || $me->termStartEdtf !== '2009-12') { $bad[] = '#28245 is not Messina\'s 2009 holding'; }
if (!$MO) { $bad[] = 'Cherise Moore has no record'; }
$ahDone = $ah && $ah->termEndEdtf === '2028';
$meDone = $me && $me->termEndEdtf === '2022-12';
$me2 = $me ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $me->holdingPerson->one(), 'field' => 'holdingPerson'], ['targetElement' => 21588, 'field' => 'holdingBody']])->termStartEdtf('2022-12')->one() : null;
$mo17 = $MO ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $MO, 'field' => 'holdingPerson'], ['targetElement' => 21588, 'field' => 'holdingBody']])->termStartEdtf('2017')->one() : null;
echo 'Ahuja #26969: ' . ($ahDone ? 'end already 2028' : 'end 2028 (the district\'s "current term 2024 - 2028")') . PHP_EOL;
echo 'Messina #28245: ' . ($meDone ? 'already ended December 2022' : 'end December 2022, re-elected') . '; Area 5 holding 2022 to 2026: ' . ($me2 ? "exists #{$me2->id}" : 'create, serving, certified') . PHP_EOL;
echo 'Moore: holding from 2017 to December 2022: ' . ($mo17 ? "exists #{$mo17->id}" : 'create, appointed, certified start') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$PROV = 'record_hart_superintendents_and_board_2026_10_04.php, 4 October 2026: from the Hart district\'s own pages';
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    if (!$V) {
        $pSec = $svc->getSectionByHandle('persons'); $V = new Entry(); $V->sectionId = $pSec->id; $V->setTypeId($svc->getEntryTypeByHandle('person')->id); $V->title = 'Michael Vierra';
        $V->setFieldValues(['fullName' => 'Michael Vierra', 'roles' => [$ROLE->id], 'occupation' => 'School superintendent', 'bodyAuthorship' => 'editorial-2026',
            'body' => '<p>Michael Vierra is the superintendent of the William S. Hart Union High School District. Before that he was the district\'s Assistant Superintendent for Human Resources and its Deputy Superintendent for Educational Services.[1]</p>',
            'footnotes' => $fn([$SUP_CITE]), 'recordProvenance' => $PROV]);
        if (!$el->saveElement($V)) { throw new \RuntimeException('Vierra: ' . json_encode($V->getFirstErrors())); } $n++;
    }
    $mk = function (array $v) use ($aSec, $aType, $el, &$n) { $a = new Entry(); $a->sectionId = $aSec->id; $a->setTypeId($aType->id); $a->setFieldValues($v); if (!$el->saveElement($a)) { throw new \RuntimeException('affiliation: ' . json_encode($a->getFirstErrors())); } $n++; };
    if (!$affOf($V->id)) {
        $mk(['affiliationPerson' => [$V->id], 'affiliationBody' => [21588], 'affiliationKind' => 'employed', 'affiliationTitle' => 'Superintendent', 'affiliationEnded' => 'serving',
            'footnotes' => $fn([$SUP_CITE . ' The page does not say when he took the post.']), 'recordProvenance' => $PROV]);
    }
    if (!$affOf($SM->id)) {
        $mk(['affiliationPerson' => [$SM->id], 'affiliationBody' => [21588], 'affiliationKind' => 'employed', 'affiliationTitle' => 'Superintendent',
            'termStart' => '1974 or 1975', 'termStartEdtf' => '[1974,1975]', 'termEnd' => '1992', 'termEndEdtf' => '1992', 'affiliationEnded' => 'retired',
            'startEvidence' => 'retrospective', 'endEvidence' => 'retrospective',
            'footnotes' => $fn(array_merge($SM_AFF[1], ['The sources disagree on the start: the obituary gives 1975, the Man and Woman of the Year biography 1974; the City\'s biography of 1998 says "more than 16 years" and Leon Worden\'s column of 1996 a "17-year stint". All four give his retirement in 1992.'])),
            'recordProvenance' => 'record_hart_superintendents_and_board_2026_10_04.php, 4 October 2026: from the four sources on Smyth\'s record']);
    }
    if (!$ahDone) { $ah->setFieldValues(['termEnd' => '2028', 'termEndEdtf' => '2028', 'endEvidence' => 'certified']); if (!$el->saveElement($ah)) { throw new \RuntimeException('#26969: ' . json_encode($ah->getFirstErrors())); } $n++; }
    if (!$meDone) {
        $r = $rows($me); $r[] = ['number' => (string)(count($r) + 1), 'note' => "The district's page gives his present term as 2022 to 2026, for Trustee Area 5 (William S. Hart Union High School District, \"Governing Board Member Info,\" $BRD_URL, read 4 October 2026: \"Trustee Area No. 5 representative President\"; \"current term 2022 - 2026\"), so this record, which ran from 2009 with no end, ends at that term's start. It covers his at-large terms of 2009 and 2013 and the term after; that term's seat and how it was won are not yet held.", 'source' => 'editorial-2026'];
        $me->setFieldValues(['termEnd' => 'December 2022', 'termEndEdtf' => '2022-12', 'howEnded' => 'reelected', 'endEvidence' => 'certified', 'footnotes' => $r]);
        if (!$el->saveElement($me)) { throw new \RuntimeException('#28245: ' . json_encode($me->getFirstErrors())); } $n++;
    }
    if (!$me2) {
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($ot->id);
        /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
        $h->setFieldValues(['holdingPerson' => [$me->holdingPerson->status(null)->one()->id], 'holdingOffice' => [$RS->id], 'holdingBody' => [21588], 'holdingDistrict' => [25323], 'seatLabel' => 'Trustee Area 5',
            'termStart' => 'December 2022', 'termStartEdtf' => '2022-12', 'termEnd' => 'December 2026', 'termEndEdtf' => '2026-12', 'howEnded' => 'serving', 'startEvidence' => 'certified',
            'footnotes' => $fn(["William S. Hart Union High School District, \"Governing Board Member Info,\" $BRD_URL, read 4 October 2026: \"Joe Messina Trustee Area No. 5 representative President\"; \"current term 2022 - 2026.\" How the term was won (an election, or appointment in lieu of one) is not yet held."]),
            'recordProvenance' => $PROV]);
        if (!$el->saveElement($h)) { throw new \RuntimeException('Messina 2022: ' . json_encode($h->getFirstErrors())); } $n++;
    }
    if (!$mo17) {
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($ot->id);
        $h->setFieldValues(['holdingPerson' => [$MO->id], 'holdingOffice' => [$RS->id], 'holdingBody' => [21588],
            'termStart' => '2017', 'termStartEdtf' => '2017', 'termEnd' => 'December 2022', 'termEndEdtf' => '2022-12', 'selectionMethod' => 'appointed', 'howEnded' => 'reelected', 'startEvidence' => 'certified', 'endEvidence' => 'roster',
            'footnotes' => $fn(["William S. Hart Union High School District, \"Governing Board Member Info,\" $BRD_URL, read 4 October 2026: \"currently serving on the William S. Hart Union High School District Board of Trustees since 2017.\" The month, the seat and the board action are not yet held.", 'She stood in 2022 as the incumbent by an earlier appointment (her 2022 term\'s record), and was elected that November.']),
            'recordProvenance' => $PROV]);
        if (!$el->saveElement($h)) { throw new \RuntimeException('Moore 2017: ' . json_encode($h->getFirstErrors())); } $n++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = $affOf($V->id) && $affOf(15985) && $get(26969)->termEndEdtf === '2028' && $get(28245)->termEndEdtf === '2022-12';
echo 'READ-BACK ' . ($ok ? "OK: $n writes" : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('record_hart_superintendents_and_board_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'Hart superintendents as affiliations (Vierra serving, Smyth to 1992); board checked against the district: Ahuja end, Messina Area 5, Moore from 2017');
if (!$ok) { throw new \RuntimeException('record_hart_superintendents_and_board: read-back failed'); }
