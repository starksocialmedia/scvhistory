/**
 * Jason Gibbs's current term (Nathan, 2 October 2026: "He was elected November
 * 2020 and is a sitting member; his term runs to 2028. Fix the holding").
 *
 * His one holding, #23359, ran December 2020 to December 2024: the end was
 * derived (four years from the start), not read from a record, and it left him
 * off "The council today." The City lists him as a sitting member in 2026, and
 * Nathan gives his term as running to 2028, which is a second term from
 * December 2024. So #23359 closes as re-elected, and a new holding opens in
 * December 2024 with no end. Its start rests on Nathan's statement and the
 * City's listing, and is marked uncited: the 2024 election for his seat is not
 * yet in the archive, and the new holding says so in a footnote.
 *
 * Every other sitting member's current term was checked: Ayala, McLean, Weste
 * and Miranda each have an open term; no other end date is wrong.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_gibbs_current_term.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$GIBBS = 23091; $OLD = 23359;
$old = Entry::find()->id($OLD)->status(null)->one();
$bad = [];
if (!$old || $old->holdingPerson->one()?->id !== $GIBBS) { $bad[] = '#23359 is not Gibbs\'s holding'; }
$open = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $GIBBS, 'field' => 'holdingPerson'])->all();
$current = array_values(array_filter($open, fn($h) => !$h->termEnd));
echo '#23359 ' . $old?->termStart . ' to ' . ($old?->termEnd ?: 'open') . ', howEnded ' . $old?->howEnded?->value . ' -> howEnded reelected' . PHP_EOL;
echo ($current ? 'current term exists: #' . $current[0]->id : 'create: City Council Member, December 2024 to (open), elected, start uncited, with a footnote') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($old->howEnded?->value !== 'reelected') { $old->setFieldValue('howEnded', 'reelected'); if (!$elements->saveElement($old)) { throw new \RuntimeException('#23359: ' . json_encode($old->getFirstErrors())); } }
    if (!$current) {
        $n = new Entry(); $n->sectionId = $old->sectionId; $n->setTypeId($old->typeId);
        $h = array_map(fn($f) => $f->handle, $n->getFieldLayout()->getCustomFields());
        $vals = ['holdingPerson' => [$GIBBS], 'holdingOffice' => $old->holdingOffice->ids(), 'holdingBody' => $old->holdingBody->ids(), 'holdingDistrict' => $old->holdingDistrict->ids(),
            'termStart' => 'December 2024', 'termStartEdtf' => '2024-12', 'selectionMethod' => 'elected', 'howEnded' => 'serving', 'startEvidence' => 'uncited',
            'footnotes' => [['number' => '1', 'note' => 'Per Nathan Imhoff, 2 October 2026, who gives this term as running to 2028; the City of Santa Clarita lists Gibbs as a sitting member (https://santaclarita.gov/city-council/jason-gibbs/, read 1 October 2026). The 2024 election for his seat is not yet in the archive.', 'source' => 'editorial-2026']],
            'recordProvenance' => 'fix_gibbs_current_term.php, 2 October 2026'];
        $n->setFieldValues(array_intersect_key($vals, array_flip($h)));
        if (!$elements->saveElement($n)) { throw new \RuntimeException('new holding: ' . json_encode($n->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$cur = array_values(array_filter(Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $GIBBS, 'field' => 'holdingPerson'])->all(), fn($h) => !$h->termEnd));
$ok = count($cur) === 1 && $cur[0]->termStartEdtf === '2024-12';
echo 'READ-BACK ' . ($ok ? 'OK: #' . $cur[0]->id . ' ' . $cur[0]->title : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_gibbs_current_term.php', 2, $ok ? 'verified' : 'SHORT', 'Jason Gibbs: first term closed as re-elected; current term from December 2024, per Nathan Imhoff');
if (!$ok) { throw new \RuntimeException('fix_gibbs_current_term: read-back failed'); }

/* His profile said the archive's record of his office ended in December 2024; with the current term recorded, the sentence is corrected. */
$OLD_S = 'The archive\'s record of his office ends with that first term in December 2024, and it does not yet hold a record of his seat in the 2024 elections; the City lists him as a sitting member in 2026.[1][2]';
$NEW_S = 'He began a second term in December 2024, which runs to 2028; the archive does not yet hold the 2024 election for his seat, and the City lists him as a sitting member in 2026.[1][2]';
$g = Entry::find()->id($GIBBS)->status(null)->one();
if (str_contains((string)$g->body, $OLD_S)) {
    $g->setFieldValue('body', str_replace($OLD_S, $NEW_S, (string)$g->body));
    if (!$elements->saveElement($g)) { throw new \RuntimeException('#23091 body'); }
    echo 'profile sentence corrected' . PHP_EOL;
}
