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
 * SINCE 2 OCTOBER 2026 (Nathan: "teach the calendar to read existing date
 * fields"), it also reads the exact dates records already hold in their own
 * date fields, which were set from sources when the records were built and so
 * need no review: events, elections, deaths, foundings and dissolutions, places
 * established, office terms begun, photographs, war memorial deaths (read from
 * deathDate where no EDTF is set, as written: 4-27-1919, 04/27/1919, "Friday,
 * 04/18/1969", "June 13, 1942"), and births of historical people only (the
 * historical.twig test: a living or undetermined person's birth never goes on
 * the calendar). Each item carries a kind. Article and document publication
 * dates go in a separate list, published, which the page shows apart and
 * labels as publication, not event (Nathan: "as long as a reader can tell the
 * difference"). Rows ticked "Not for the calendar" are never read. Disabled
 * records are left out: their pages do not resolve.
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

    foreach (\craft\elements\Entry::find()->section($section->handle)->all() as $entry) {

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

            if (empty($row['confirmed']) || !empty($row['rejected'])) { continue; }
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
                'kind'     => 'record',
            ];
            $stats['kept']++;
        }
    }
}

// The date fields records already hold. One item per record and date: where a
// confirmed row and a field give the same date, the field's item is kept, since
// its label says what the date is ("Died") where the row's is a cut of prose.
$NOW = (int)date('Y');
$MONTHS = [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$FIELDS = [
    'eventDateEdtf'       => ['event', null],
    'electionDateEdtf'    => ['election', 'Election day'],
    'deathDateEdtf'       => ['died', 'Died'],
    'dateFoundedEdtf'     => ['founded', 'Founded'],
    'dateDissolvedEdtf'   => ['dissolved', 'Dissolved'],
    'dateEstablishedEdtf' => ['established', 'Established'],
    'termStartEdtf'       => ['office', 'Took office'],
    'photoDateEdtf'       => ['photographed', 'Photographed'],
    'birthDateEdtf'       => ['born', 'Born'],
];
$published = [];
$seen = [];
$stats['structured'] = 0; $stats['publishedItems'] = 0; $stats['skippedLiving'] = 0;
$printedOf = fn(string $iso) => $MONTHS[(int)substr($iso, 5, 2)] . ' ' . (int)substr($iso, 8, 2) . ', ' . substr($iso, 0, 4);
foreach (\craft\elements\Entry::find()->all() as $entry) {
    $h = [];
    foreach ($entry->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; }
    $get = function ($k) use ($entry, $h) { if (!isset($h[$k])) { return ''; } try { return trim((string)$entry->getFieldValue($k)); } catch (\Throwable $e) { return ''; } };
    $url = $entry->getUrl();
    if (!$url) { continue; }
    $sec = $entry->getSection()->handle;
    /* historical.twig, in PHP: fails closed, so an undetermined person is living. */
    $dead = $sec === 'warMemorials' || preg_match('~\d{3}~', $get('deathDate') . $get('deathDateEdtf') . $get('mpDateOfDeath'));
    $by = preg_match('~\b(1[6-9]\d\d|20\d\d)\b~', $get('birthDateEdtf') . ' ' . $get('birthDate'), $m) ? (int)$m[1] : null;
    $historical = $dead || ($by && $by < $NOW - 120) || (isset($h['personObituaries']) && $entry->personObituaries->exists());
    $dates = [];
    foreach ($FIELDS as $k => [$kind, $label]) {
        $v = $get($k);
        if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $v)) { continue; }
        if ($kind === 'born' && (!in_array($sec, ['persons', 'militaryProfiles'], true) || !$historical)) { $stats['skippedLiving']++; continue; }
        $dates[] = [$v, $kind, $label];
    }
    if ($sec === 'warMemorials' && !preg_match('~^\d{4}-\d{2}-\d{2}$~', $get('deathDateEdtf'))) {
        $d = preg_replace('~^[A-Za-z]+day,\s*~', '', $get('deathDate'));
        if (preg_match('~^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$~', $d, $m) && checkdate((int)$m[1], (int)$m[2], (int)$m[3])) { $dates[] = [sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]), 'died', 'Died']; }
        elseif (preg_match('~^([A-Z][a-z]+)\s+(\d{1,2}),\s*(\d{4})$~', $d, $m) && ($mo = array_search($m[1], $MONTHS)) && checkdate($mo, (int)$m[2], (int)$m[3])) { $dates[] = [sprintf('%04d-%02d-%02d', $m[3], $mo, $m[2]), 'died', 'Died']; }
    }
    $pub = $get('originalPublishDateEdtf');
    if (!$dates && !preg_match('~^\d{4}-\d{2}-\d{2}$~', $pub)) { continue; }
    $image = null;
    foreach (['featuredImage', 'recordImages'] as $ih) {
        if ($image !== null || !isset($h[$ih])) { continue; }
        try { $a = $entry->getFieldValue($ih)->one(); if ($a) { $image = $a->getUrl(); } } catch (\Throwable $e) {}
    }
    $rel = $entry->uri ? '/' . ltrim($entry->uri, '/') : $url;
    $item = fn($iso, $kind, $label) => ['title' => $entry->title ?: $entry->slug, 'url' => $rel, 'section' => $entry->getSection()->name, 'iso' => $iso, 'year' => (int)substr($iso, 0, 4), 'printed' => $printedOf($iso), 'label' => $label ?? '', 'granularity' => 'day', 'image' => $image, 'kind' => $kind];
    foreach ($dates as [$iso, $kind, $label]) {
        if (isset($seen[$rel . '|' . $iso])) { continue; }
        $seen[$rel . '|' . $iso] = true;
        if ($kind === 'event') { $label = trim((string)$get('eventSignificance')) ?: null; }
        $index[substr($iso, 5)][] = $item($iso, $kind, $label);
        $stats['structured']++;
    }
    if (preg_match('~^\d{4}-\d{2}-\d{2}$~', $pub) && !isset($seen[$rel . '|' . $pub])) {
        $published[substr($pub, 5)][] = $item($pub, 'published', null);
        $stats['publishedItems']++;
    }
}
foreach ($index as $k => $rows) {
    $index[$k] = array_values(array_filter($rows, fn($r) => ($r['kind'] ?? '') !== 'record' || !isset($seen[$r['url'] . '|' . $r['iso']])));
    if (!$index[$k]) { unset($index[$k]); }
}
foreach ($published as $k => $rows) { usort($rows, fn($a, $b) => $a['iso'] <=> $b['iso']); $published[$k] = $rows; }
ksort($published);

// oldest first within a day, so a day reads as a little chronology
foreach ($index as $k => $rows) {
    usort($rows, function ($a, $b) { return $a['iso'] <=> $b['iso']; });
    $index[$k] = $rows;
}
ksort($index);

$payload = [
    '_generated' => 'Built by scripts/import/build_calendar_index.php. Do not edit by hand.',
    '_source'    => 'Confirmed recordDates rows (day or month), and the exact dates records hold in their own date fields; publication dates apart, in published.',
    '_built'     => (new \DateTime('now', new \DateTimeZone('UTC')))->format('c'),
    '_counts'    => [
        'records_with_rows' => $stats['records'],
        'rows_total'        => $stats['rows'],
        'rows_confirmed'    => $stats['confirmed'],
        'rows_indexed'      => $stats['kept'],
        'days_with_entries' => count($index),
        'items_from_fields' => $stats['structured'],
        'published_items'   => $stats['publishedItems'],
        'days_with_published' => count($published),
        'days_with_either'  => count($index + $published),
    ],
    'days' => $index,
    'published' => $published,
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
echo 'Items from date fields            : ' . $stats['structured'] . PHP_EOL;
echo 'Births left out (living or undetermined): ' . $stats['skippedLiving'] . PHP_EOL;
echo 'Published items, on their own line: ' . $stats['publishedItems'] . ' over ' . count($published) . ' days' . PHP_EOL;
echo 'Days with anything at all         : ' . count($index + $published) . ' of 366' . PHP_EOL;
echo PHP_EOL . 'Wrote ' . $OUT . PHP_EOL;

if ($stats['kept'] === 0) {
    echo PHP_EOL . 'Nothing is indexed yet. The file is valid and empty, and On This Day' . PHP_EOL;
    echo 'will say so until rows are ticked Confirmed in the control panel.' . PHP_EOL;
}
