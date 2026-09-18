/**
 * Builds templates/_data/calendar.json, the On This Day index.
 *
 * Walks every record's recordDates rows, keeps only the rows a person has
 * ticked Confirmed whose precision is day or month, and writes one generated
 * JSON file keyed by MM-DD. Same pattern as templates/_data/community-neighbors.json:
 * a generated file the templates read with Twig's source(), never edited by hand.
 *
 * Year and circa rows are left out on purpose. A year alone has no day to file
 * it under, and an approximate date would put a record on a calendar day the
 * source does not actually claim.
 *
 * Reads only. It never touches the database and never modifies a recordDates
 * row. Rerun it after each round of confirmations; it rewrites the file whole,
 * so it is safe to run as often as you like.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_calendar_index.php'))"
 */

$OUT = CRAFT_BASE_PATH . '/templates/_data/calendar.json';
$KEEP = ['day', 'month'];

$entriesService = Craft::$app->getEntries();

$index = [];
$stats = ['records' => 0, 'rows' => 0, 'confirmed' => 0, 'kept' => 0,
          'skippedGranularity' => 0, 'skippedNoDate' => 0, 'skippedNoUrl' => 0];
$byGranularity = [];

foreach ($entriesService->getAllSections() as $section) {
    $sectionName = $section->name;

    foreach (\craft\elements\Entry::find()->section($section->handle)->status(null)->all() as $entry) {

        // recordDates is on every entry type today, but check the layout rather
        // than trusting that: "is defined" reports true for a field that is not
        // there and then throws when it is read.
        $has = false;
        foreach ($entry->getFieldLayout()->getCustomFields() as $f) {
            if ($f->handle === 'recordDates') { $has = true; break; }
        }
        if (!$has) { continue; }

        try {
            $rows = $entry->getFieldValue('recordDates');
        } catch (\Throwable $e) {
            continue;
        }
        if (!is_array($rows) || !$rows) { continue; }

        $stats['records']++;

        $image = null;
        foreach (['featuredImage', 'recordImages'] as $h) {
            if ($image !== null) { break; }
            $hasImg = false;
            foreach ($entry->getFieldLayout()->getCustomFields() as $f) {
                if ($f->handle === $h) { $hasImg = true; break; }
            }
            if (!$hasImg) { continue; }
            try {
                $asset = $entry->getFieldValue($h)->one();
                if ($asset) { $image = $asset->getUrl(); }
            } catch (\Throwable $e) {
                // no asset we can read; leave the image null
            }
        }

        foreach ($rows as $row) {
            $stats['rows']++;

            if (empty($row['confirmed'])) { continue; }
            $stats['confirmed']++;

            $gran = $row['granularity'] ?? '';
            $byGranularity[$gran] = ($byGranularity[$gran] ?? 0) + 1;
            if (!in_array($gran, $KEEP, true)) { $stats['skippedGranularity']++; continue; }

            $iso = $row['iso'] ?? null;
            if (!$iso instanceof \DateTimeInterface) {
                if (is_string($iso) && $iso !== '') {
                    try { $iso = new \DateTime($iso); } catch (\Throwable $e) { $iso = null; }
                } else {
                    $iso = null;
                }
            }
            if (!$iso) { $stats['skippedNoDate']++; continue; }

            // Craft hands these back in the site timezone, which for a date-only
            // value shifts the day backwards. Read it in UTC so June 19 stays
            // June 19 rather than becoming June 18.
            $utc = (clone $iso)->setTimezone(new \DateTimeZone('UTC'));

            $url = $entry->getUrl();
            if (!$url) { $stats['skippedNoUrl']++; continue; }

            $key = $utc->format('m-d');

            $index[$key][] = [
                'title'    => $entry->title ?: $entry->slug,
                'url'      => $entry->uri ? '/' . ltrim($entry->uri, '/') : $url,
                'section'  => $sectionName,
                'iso'      => $utc->format('Y-m-d'),
                'year'     => (int)$utc->format('Y'),
                'printed'  => trim((string)($row['printed'] ?? '')),
                'label'    => trim((string)($row['label'] ?? '')),
                'granularity' => $gran,
                'image'    => $image,
            ];
            $stats['kept']++;
        }
    }
}

// oldest first within a day, so a day reads as a little chronology
foreach ($index as $k => $rows) {
    usort($rows, function ($a, $b) { return $a['iso'] <=> $b['iso']; });
    $index[$k] = $rows;
}
ksort($index);

$payload = [
    '_generated' => 'Built by scripts/import/build_calendar_index.php. Do not edit by hand.',
    '_source'    => 'The recordDates table field, confirmed rows only, precision day or month.',
    '_built'     => (new \DateTime('now', new \DateTimeZone('UTC')))->format('c'),
    '_counts'    => [
        'records_with_rows' => $stats['records'],
        'rows_total'        => $stats['rows'],
        'rows_confirmed'    => $stats['confirmed'],
        'rows_indexed'      => $stats['kept'],
        'days_with_entries' => count($index),
    ],
    'days' => $index,
];

$dir = dirname($OUT);
if (!is_dir($dir)) { mkdir($dir, 0775, true); }
file_put_contents($OUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo 'Records carrying recordDates rows : ' . $stats['records'] . PHP_EOL;
echo 'Rows seen                         : ' . $stats['rows'] . PHP_EOL;
echo 'Rows confirmed                    : ' . $stats['confirmed'] . PHP_EOL;
foreach ($byGranularity as $g => $n) {
    echo '    confirmed, precision ' . str_pad($g ?: '(none)', 8) . ': ' . $n . PHP_EOL;
}
echo 'Rows indexed (day or month)       : ' . $stats['kept'] . PHP_EOL;
echo 'Skipped, wrong precision          : ' . $stats['skippedGranularity'] . PHP_EOL;
echo 'Skipped, no usable date           : ' . $stats['skippedNoDate'] . PHP_EOL;
echo 'Skipped, record has no URL        : ' . $stats['skippedNoUrl'] . PHP_EOL;
echo 'Days with at least one entry      : ' . count($index) . PHP_EOL;
echo PHP_EOL . 'Wrote ' . $OUT . PHP_EOL;

if ($stats['kept'] === 0) {
    echo PHP_EOL . 'Nothing is indexed yet. The file is valid and empty, and On This Day' . PHP_EOL;
    echo 'will say so until rows are ticked Confirmed in the control panel.' . PHP_EOL;
}
