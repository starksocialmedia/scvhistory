/**
 * Evidence rates the claim, not the document that carries it.
 *
 * Nathan's ruling, 28 September 2026:
 *   A document is certified only for what its certifier attests; every other
 *   claim on it is rated by who supplied it and how long after the event. And
 *   certified requires a certificate the archive holds: a certificate someone
 *   else cites is retrospective until the document is in the archive.
 *
 * The field instructions already said it ("How good is the claim, not what it
 * says"); two values did not follow it:
 *
 *   Mentry #18648 birthEvidence certified -> retrospective. His death
 *     certificate (#20102) attests his death. The birth date on it is an
 *     informant's statement 53 years after the fact, and the certificate's own
 *     arithmetic contradicts it. deathEvidence stays certified: the certificate
 *     is held and a death is what it attests.
 *   Jenkins #20226 deathEvidence certified -> retrospective. Leon's page cites
 *     "Death Cert 10-19-1916"; the archive does not hold it.
 *
 * THE SAME TEST, EVERYWHERE. The script audits every certified evidence value
 * in the archive, every run, and reports each one with the certificate it
 * rests on. A certified value with no held certificate, or one the certificate
 * does not attest, is listed as failing the test, so a new one cannot slip in
 * unexamined. On 28 September the three values above were the only certified
 * values in the archive.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_evidence_levels.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* record id => [field => [from, to, why]] */
$CHANGES = [
    18648 => ['birthEvidence' => ['certified', 'retrospective',
        'the death certificate attests the death, not the birth; the birth date is an informant\'s statement, contradicted by the certificate\'s own age at death']],
    20226 => ['deathEvidence' => ['certified', 'retrospective',
        'the certificate is cited on the legacy page (ap2219), not held in the archive']],
];
/* Certified values that pass the test: record id => [field => certificate document id]. */
$PASS = [
    18648 => ['deathEvidence' => 20102],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();
$FIELDS = ['birthEvidence', 'deathEvidence', 'burialEvidence', 'startEvidence', 'endEvidence'];

$audit = function () use ($FIELDS, $PASS, $CHANGES): array {
    $rows = [];
    foreach (\craft\elements\Entry::find()->status(null)->each() as $e) {
        $layout = $e->getFieldLayout();
        if (!$layout) { continue; }
        foreach ($FIELDS as $f) {
            if (!$layout->getFieldByHandle($f) || (string)$e->getFieldValue($f)->value !== 'certified') { continue; }
            $cert = $PASS[$e->id][$f] ?? null;
            $held = $cert && \craft\elements\Entry::find()->id($cert)->section('documents')->status(null)->exists();
            $rows[] = [$e, $f, $held ? "passes: certificate #$cert is held and attests this" : (isset($CHANGES[$e->id][$f]) ? 'fails: to change below' : 'FAILS THE TEST: no held certificate named for it')];
        }
    }
    return $rows;
};

echo 'every certified evidence value in the archive:' . PHP_EOL;
$before = $audit();
foreach ($before as [$e, $f, $v]) { echo '   #' . $e->id . ' ' . str_pad($e->title, 30) . str_pad($f, 16) . $v . PHP_EOL; }
$unexamined = array_filter($before, fn($r) => str_starts_with($r[2], 'FAILS'));

$ops = [];
foreach ($CHANGES as $id => $set) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    foreach ($set as $f => [$from, $to, $why]) {
        $cur = (string)$e->getFieldValue($f)->value;
        if ($cur === $to) { echo 'skip #' . $id . ' ' . $f . ': already ' . $to . PHP_EOL; continue; }
        if ($cur !== $from) { echo 'REFUSE #' . $id . ' ' . $f . ': reads "' . $cur . '", expected "' . $from . '"' . PHP_EOL; continue; }
        echo 'set #' . $id . ' ' . $e->title . ': ' . $f . ' ' . $from . ' -> ' . $to . '   (' . $why . ')' . PHP_EOL;
        $ops[] = [$id, $f, $to];
    }
}
if ($unexamined) { echo PHP_EOL . count($unexamined) . ' certified value(s) fail the test and are not in $CHANGES: decide them first.' . PHP_EOL; }

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($unexamined) { return; }

$short = [];
foreach ($ops as [$id, $f, $to]) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    $e->setFieldValue($f, $to);
    if (!$elements->saveElement($e)) { $short[] = "#$id $f: save failed " . json_encode($e->getFirstErrors()); continue; }
    $got = (string)\craft\elements\Entry::find()->id($id)->status(null)->one()->getFieldValue($f)->value;
    if ($got !== $to) { $short[] = "#$id $f reads \"$got\", expected \"$to\""; }
}
$after = array_filter($audit(), fn($r) => !str_starts_with($r[2], 'passes'));
foreach ($after as [$e, $f, $v]) { $short[] = "#{$e->id} $f is still certified without a held certificate"; }

echo 'READ-BACK ' . ($short ? 'SHORT' : 'OK') . ': ' . (count($ops) - count($short)) . ' of ' . count($ops) . '; certified values left, all passing: ' . count($audit()) . PHP_EOL;
foreach ($short as $s) { echo '   FAIL ' . $s . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('set_evidence_levels.php', count($ops), $short ? 'SHORT: ' . implode('; ', $short) : 'verified ' . count($ops) . ' of ' . count($ops) . '; every remaining certified value rests on a held certificate',
    'Mentry birth and Jenkins death to retrospective; evidence rates the claim, not the document');
if ($short) { throw new \RuntimeException('set_evidence_levels: ' . implode('; ', $short)); }
