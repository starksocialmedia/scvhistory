/**
 * Reads web/review/confirmed.json produced by the review screen and writes the
 * confirmations and edited labels back into recordDates.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_confirmed_dates.php'))"
 */

$APPLY = false;

$file = \Craft::getAlias('@webroot') . '/review/confirmed.json';
if (!file_exists($file)) { echo 'ERROR: web/review/confirmed.json not found. Download it from the review screen first.' . PHP_EOL; return; }
$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) { echo 'ERROR: confirmed.json is not valid JSON' . PHP_EOL; return; }

$byEntry = [];
foreach ($data as $r) {
    if (!isset($r['entryId'], $r['rowIndex'])) { continue; }
    $byEntry[(int)$r['entryId']][(int)$r['rowIndex']] = $r;
}

$elements = Craft::$app->getElements();
$confirmed = 0; $deleted = 0; $touched = 0;

foreach ($byEntry as $entryId => $changes) {
    $entry = \craft\elements\Entry::find()->id($entryId)->status(null)->one();
    if (!$entry) { echo 'entry ' . $entryId . ' not found' . PHP_EOL; continue; }
    $rows = [];
    try { $rows = (array)$entry->getFieldValue('recordDates'); } catch (\Throwable $e) { continue; }

    $drop = [];
    foreach ($changes as $i => $r) {
        if (!isset($rows[$i])) { continue; }
        $action = $r['action'] ?? 'skip';
        if ($action === 'delete') { $drop[] = $i; $deleted++; continue; }
        if ($action !== 'confirm') { continue; }
        $iso = $r['iso'] ?? $rows[$i]['iso'] ?? '';
        if ($iso instanceof \DateTime) { $iso = $iso->format('Y-m-d'); }
        $rows[$i]['iso'] = $iso;
        $rows[$i]['printed'] = $r['printed'] ?? $rows[$i]['printed'];
        $rows[$i]['granularity'] = $r['granularity'] ?? $rows[$i]['granularity'];
        $rows[$i]['label'] = $r['label'] ?? $rows[$i]['label'];
        $rows[$i]['confirmed'] = true;
        $confirmed++;
    }
    foreach (array_reverse($drop) as $i) { unset($rows[$i]); }
    if (!count($changes)) { continue; }
    $touched++;
    echo str_pad($entry->slug, 40) . count($changes) . ' rows' . PHP_EOL;
    if ($APPLY) {
        $entry->setFieldValue('recordDates', array_values($rows));
        if (!$elements->saveElement($entry)) { echo '  SAVE FAILED ' . json_encode($entry->getErrors()) . PHP_EOL; }
    }
}

echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . ': ' . $confirmed . ' confirmed, ' . $deleted . ' deleted, across ' . $touched . ' records' . PHP_EOL;
if ($APPLY) { echo 'Now run build_calendar_index.php to refresh the on-this-day index.' . PHP_EOL; }
