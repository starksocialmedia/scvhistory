/**
 * A step on the evidence scale between roster and uncited: "derived", a value
 * read from the count with the reasoning stated in the record's notes (Nathan,
 * 29 September 2026: record winners the arithmetic establishes as derived, not
 * certified, so the pages are useful now and upgrade when the resolutions come).
 *
 * Added to the four fields the council records use: outcomeEvidence,
 * seatsUpEvidence, startEvidence, endEvidence. The other evidence fields
 * (birth, death, burial, education) do not take it: nothing there is counted.
 *
 * Its own script, run before council_winners_and_terms.php, because a record
 * saved in the same request as the new option validates against the old list
 * and is refused: that is how Ruiz Cemetery missed its type on 29 September.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_derived_evidence.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to project config' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $todo = [];
foreach (['outcomeEvidence', 'seatsUpEvidence', 'startEvidence', 'endEvidence'] as $h) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) { echo "REFUSING: no field $h" . PHP_EOL; return; }
    $has = in_array('derived', array_map(fn($o) => $o['value'], $f->options), true);
    echo str_pad($h, 18) . ($has ? 'has derived' : 'add "Derived: read from the count" before uncited') . PHP_EOL;
    if (!$has) { $todo[] = $f; }
}
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
foreach ($todo as $f) {
    $opts = $f->options; $at = count($opts);
    foreach ($opts as $i => $o) { if ($o['value'] === 'uncited') { $at = $i; } }
    array_splice($opts, $at, 0, [['label' => 'Derived: read from the count', 'value' => 'derived', 'default' => false]]);
    $f->options = $opts;
    if (!$fs->saveField($f)) { throw new \RuntimeException($f->handle . ': ' . json_encode($f->getFirstErrors())); }
}
$short = [];
foreach (['outcomeEvidence', 'seatsUpEvidence', 'startEvidence', 'endEvidence'] as $h) {
    if (!in_array('derived', array_map(fn($o) => $o['value'], Craft::$app->getFields()->getFieldByHandle($h)->options), true)) { $short[] = $h; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_derived_evidence.php', count($todo), $short ? 'SHORT: ' . implode(', ', $short) : 'verified', '"derived" on the evidence scale for the council fields');
if ($short) { throw new \RuntimeException('add_derived_evidence: ' . implode(', ', $short)); }
