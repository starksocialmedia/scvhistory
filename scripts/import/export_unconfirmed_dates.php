/**
 * Exports every unconfirmed recordDates row to review/dates.json for the
 * review screen. Read only.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_unconfirmed_dates.php'))"
 */

$out = [];
$sections = [];
foreach (Craft::$app->getEntries()->getAllSections() as $s) { $sections[] = $s->handle; }

foreach ($sections as $handle) {
    foreach (\craft\elements\Entry::find()->section($handle)->status(null)->all() as $entry) {
        $layout = [];
        foreach ($entry->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }
        if (!in_array('recordDates', $layout, true)) { continue; }
        $rows = [];
        try { $rows = (array)$entry->getFieldValue('recordDates'); } catch (\Throwable $e) { continue; }
        foreach ($rows as $i => $row) {
            if (!empty($row['confirmed'])) { continue; }
            $iso = $row['iso'] ?? '';
            if ($iso instanceof \DateTime) { $iso = $iso->format('Y-m-d'); }
            $out[] = [
                'entryId' => $entry->id,
                'rowIndex' => $i,
                'section' => $entry->section->name,
                'slug' => $entry->slug,
                'title' => $entry->title,
                'url' => $entry->url,
                'printed' => (string)($row['printed'] ?? ''),
                'iso' => (string)$iso,
                'granularity' => (string)($row['granularity'] ?? ''),
                'label' => (string)($row['label'] ?? ''),
            ];
        }
    }
}

usort($out, function ($a, $b) {
    return [substr($a['iso'], 5), $a['iso']] <=> [substr($b['iso'], 5), $b['iso']];
});

$path = \Craft::getAlias('@review');
if (!is_dir($path)) { mkdir($path, 0775, true); }
file_put_contents($path . '/dates.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$day = count(array_filter($out, fn($r) => $r['granularity'] === 'day'));
echo 'exported ' . count($out) . ' unconfirmed rows (' . $day . ' day-precision) to review/dates.json' . PHP_EOL;
echo 'open https://scvhistory.ddev.site/review/dates.html' . PHP_EOL;
