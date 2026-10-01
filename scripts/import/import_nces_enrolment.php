/**
 * Enrolment from the NCES Common Core of Data into each school and district
 * record that carries an NCES id (Nathan, 1 October 2026: "fill enrolment from
 * NCES"). Reads inventory/schools/nces-enrolment.json (fetch_nces_enrolment.py).
 *
 * One row per school year: "2022-23", fall 2022, the count, and the source,
 * "NCES Common Core of Data". A record's NCES id must match the one the figures
 * were fetched for, or it is refused. Rows already there from another source
 * are kept; NCES rows are replaced as a set, so a re-run after a new fetch
 * brings the series up to date. Needs add_school_fields.php first.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_nces_enrolment.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$path = \Craft::getAlias('@root') . '/inventory/schools/nces-enrolment.json';
$data = is_file($path) ? json_decode(file_get_contents($path), true) : null;
if (!$data) { echo 'NOT YET: inventory/schools/nces-enrolment.json is missing (run fetch_nces_enrolment.py; the portal was rate-limiting on 1 October 2026)' . PHP_EOL; return; }
$ready = (bool)Craft::$app->getFields()->getFieldByHandle('enrolment');
if (!$ready) { echo 'NEEDS add_school_fields.php first (the plan below is still checked)' . PHP_EOL; }
$SRC = 'NCES Common Core of Data';
$plan = []; $refused = [];
foreach ($data['records'] as $id => $r) {
    $e = Entry::find()->id((int)$id)->status(null)->one();
    if (!$e || trim((string)$e->ncesId) !== $r['nces']) { $refused[] = "#$id: the record's NCES id is not {$r['nces']}"; continue; }
    $rows = [];
    foreach ($r['years'] as $y => $v) { $rows[] = ['schoolYear' => $y . '-' . substr((string)((int)$y + 1), 2), 'fallYear' => (int)$y, 'count' => (int)$v['count'], 'source' => $SRC]; }
    usort($rows, fn($a, $b) => $a['fallYear'] <=> $b['fallYear']);
    $plan[$id] = [$e, $rows];
    echo '   #' . str_pad($id, 6) . str_pad($e->title, 48) . count($rows) . ' years' . ($rows ? ', ' . $rows[0]['schoolYear'] . ' to ' . end($rows)['schoolYear'] . ', latest ' . number_format(end($rows)['count']) : '') . PHP_EOL;
}
echo 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused || !$ready) { echo 'REFUSING: ' . ($ready ? 'resolve the refusals first' : 'run add_school_fields.php first') . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => [$e, $rows]) {
    $e = Entry::find()->id((int)$id)->status(null)->one();
    $keep = array_values(array_filter($e->enrolment ?? [], fn($r) => ($r['source'] ?? '') !== $SRC && (string)($r['count'] ?? '') !== ''));
    $e->setFieldValue('enrolment', array_merge($keep, $rows));
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id"; continue; }
    $back = array_filter(Entry::find()->id((int)$id)->status(null)->one()->enrolment ?? [], fn($r) => ($r['source'] ?? '') === $SRC);
    if (count($back) !== count($rows)) { $short[] = "#$id reads back " . count($back) . ' of ' . count($rows); }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . ' records') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_nces_enrolment.php', count($plan), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'enrolment from the NCES Common Core of Data');
if ($short) { throw new \RuntimeException('import_nces_enrolment: ' . implode('; ', $short)); }
