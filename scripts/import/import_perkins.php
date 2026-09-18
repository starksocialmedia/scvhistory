/**
 * Imports the wave 0 Perkins extraction (inventory/legacy/perkins.json) into Craft.
 *
 * Pass 1 creates or updates one Article per page in series_pages and related_pages,
 * matched on legacyKey. Pass 2 wires the series to the "Story of Our Valley"
 * collection in series_position order. Pass 3 writes inventory/legacy/perkins-images.json
 * so the images can be matched against the drive later; no image is downloaded.
 *
 * No Person, Place or Organization record is created. entity_index needs human
 * adjudication first, per GROK-CONTRACT.md.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_perkins.php'))"
 *
 * $OVERWRITE = false fills only empty fields on an article that already exists, so a
 * curated body or a hand-written note is never clobbered and a second run is a no-op.
 * Set it true to push every non-empty extracted value over what is there.
 * An empty extracted value never overwrites anything in either mode, because under the
 * contract an empty string means "not found", not "blank".
 */

$APPLY = false;
$OVERWRITE = false;

$root = \Craft::getAlias('@root');
$path = $root . '/inventory/legacy/perkins.json';
if (!file_exists($path)) {
    echo 'ERROR: ' . $path . ' not found' . PHP_EOL;
    return;
}
$data = json_decode(file_get_contents($path), true);
if (!is_array($data) || !isset($data['series_pages']) || !isset($data['related_pages'])) {
    echo 'ERROR: perkins.json has no series_pages / related_pages' . PHP_EOL;
    return;
}

$elements = Craft::$app->getElements();
$COLLECTION_TITLE = 'Story of Our Valley';
$IMAGES_OUT = $root . '/inventory/legacy/perkins-images.json';

/* The closed community list from GROK-CONTRACT.md. A name outside it is a place,
   not a community, and is reported rather than created. */
$CLOSED_COMMUNITIES = [
    'Acton', 'Agua Dulce', 'Bouquet Canyon', 'Camulos', 'Canyon Country', 'Castaic',
    'Castaic Junction', 'Fair Oaks Ranch', 'Fillmore', 'Frazier Park', 'Haskell Canyon',
    'Hasley Canyon', 'Lake Hughes', 'Lebec', 'Mentryville', 'Mint Canyon', 'Mojave Desert',
    'Newhall', 'Pico Canyon', 'Piru', 'Placerita Canyon', 'Potrero Canyon', 'Ravenna',
    'San Francisquito Canyon', 'Sand Canyon', 'Santa Clarita', 'Saugus', 'Saugus-Valencia',
    'Soledad Canyon', 'Soledad Township', 'Stevenson Ranch', 'Tejon', 'Towsley Canyon',
    'Val Verde', 'Valencia',
];

/* ---------------------------------------------------------------- helpers */

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) {
        if ($f->handle === $handle) { return true; }
    }
    return false;
};

$readField = function (\craft\base\ElementInterface $el, string $handle) use ($hasField) {
    if (!$hasField($el, $handle)) { return null; }
    try { return $el->getFieldValue($handle); }
    catch (\Throwable $e) { return null; }
};

$isEmptyValue = function ($v): bool {
    if ($v === null) { return true; }
    if (is_string($v)) { return trim($v) === ''; }
    if (is_array($v)) { return count($v) === 0; }
    if ($v instanceof \craft\elements\db\ElementQuery) { return $v->count() === 0; }
    if (is_object($v) && method_exists($v, 'getRawContent')) {
        return trim(strip_tags((string)$v->getRawContent())) === '';
    }
    if (is_object($v)) { return trim(strip_tags((string)$v)) === ''; }
    return false;
};

$MONTHS = [
    'jan' => 1, 'january' => 1, 'feb' => 2, 'february' => 2, 'mar' => 3, 'march' => 3,
    'apr' => 4, 'april' => 4, 'may' => 5, 'jun' => 6, 'june' => 6, 'jul' => 7, 'july' => 7,
    'aug' => 8, 'august' => 8, 'sep' => 9, 'sept' => 9, 'september' => 9,
    'oct' => 10, 'october' => 10, 'nov' => 11, 'november' => 11, 'dec' => 12, 'december' => 12,
];

/**
 * Parses a printed date into [iso, granularity], or null when it cannot be read.
 * Nothing is inferred: a year is only returned when a year is printed.
 */
$parseDate = function (string $rawIn) use ($MONTHS): ?array {
    $t = trim(preg_replace('/\s+/', ' ', $rawIn));
    $t = rtrim($t, " .,;:!?)]");
    $t = ltrim($t, " ([");
    if ($t === '') { return null; }

    $circa = (bool)preg_match('/\b(c\.|ca\.|circa|about|around|approximately|roughly|early|late|mid)\b/i', $t);

    /* Strip leading positional and qualifying words until nothing more comes off. */
    $lead = '/^(in|on|of|by|from|until|till|to|since|after|before|during|about|around|approximately|roughly|circa|c\.|ca\.|early|late|mid|the|year|that|this)\s+/i';
    do { $before = $t; $t = preg_replace($lead, '', $t); } while ($t !== $before);
    $t = trim($t);
    if ($t === '') { return null; }

    /* 4th -> 4 */
    $t = preg_replace('/\b(\d{1,2})(st|nd|rd|th)\b/i', '$1', $t);

    $ok = function (int $y, int $m, int $d): bool {
        return $y >= 1000 && $y <= 2100 && checkdate($m, $d, $y);
    };
    $g = $circa ? 'circa' : null;

    /* Month D, YYYY */
    if (preg_match('/^([A-Za-z]+)\.? (\d{1,2}),? (\d{4})$/', $t, $m)) {
        $mon = $MONTHS[strtolower($m[1])] ?? null;
        if ($mon && $ok((int)$m[3], $mon, (int)$m[2])) {
            return [sprintf('%04d-%02d-%02d', (int)$m[3], $mon, (int)$m[2]), 'day'];
        }
    }
    /* D Month YYYY */
    if (preg_match('/^(\d{1,2}) ([A-Za-z]+)\.?,? (\d{4})$/', $t, $m)) {
        $mon = $MONTHS[strtolower($m[2])] ?? null;
        if ($mon && $ok((int)$m[3], $mon, (int)$m[1])) {
            return [sprintf('%04d-%02d-%02d', (int)$m[3], $mon, (int)$m[1]), 'day'];
        }
    }
    /* M/D/YYYY and M-D-YYYY */
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $t, $m)) {
        if ($ok((int)$m[3], (int)$m[1], (int)$m[2])) {
            return [sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[1], (int)$m[2]), 'day'];
        }
    }
    /* Month YYYY */
    if (preg_match('/^([A-Za-z]+)\.?,? (\d{4})$/', $t, $m)) {
        $mon = $MONTHS[strtolower($m[1])] ?? null;
        $y = (int)$m[2];
        if ($mon && $y >= 1000 && $y <= 2100) {
            return [sprintf('%04d-%02d-01', $y, $mon), 'month'];
        }
    }
    /* Bare year */
    if (preg_match('/^(\d{4})$/', $t)) {
        $y = (int)$t;
        if ($y >= 1000 && $y <= 2100) {
            return [sprintf('%04d-01-01', $y), $g ?: 'year'];
        }
    }
    return null;
};

/* Neighborhood terms, looked up once and matched on exact title. */
$terms = [];
foreach (\craft\elements\Category::find()->group('neighborhood')->status(null)->all() as $c) {
    $terms[$c->title] = $c->id;
}

/* ---------------------------------------------------------------- pass 1 */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($OVERWRITE ? ' (OVERWRITE ON)' : '') . PHP_EOL;
echo 'neighborhood terms available: ' . count($terms) . PHP_EOL;
echo '=== pass 1: articles ===' . PHP_EOL;

$section = Craft::$app->getEntries()->getSectionByHandle('articles');
$type = Craft::$app->getEntries()->getEntryTypeByHandle('article');
if (!$section || !$type) {
    echo 'ERROR: articles section or article entry type missing' . PHP_EOL;
    return;
}

$pages = [];
foreach ($data['series_pages'] as $p) { $p['_group'] = 'series'; $pages[] = $p; }
foreach ($data['related_pages'] as $p) { $p['_group'] = 'related'; $pages[] = $p; }

$created = 0; $updated = 0; $adopted = 0; $failed = 0;
$byKey = [];                 /* legacy_key => entry id, for pass 2 */
$unmatchedCommunities = [];  /* name => [pages] */
$offListCommunities = [];
$unparsedDates = [];         /* "key | text" */
$dateStats = ['parsed' => 0, 'skipped' => 0, 'rows' => 0, 'deduped' => 0];
$noteStats = ['top' => 0, 'bottom' => 0, 'other' => 0];
$emptySource = [];           /* field => count of pages where the extraction had nothing */

foreach ($pages as $p) {
    $key = trim((string)$p['legacy_key']);
    if ($key === '') { echo 'SKIP page with no legacy_key: ' . $p['source_url'] . PHP_EOL; continue; }

    /* Match on legacyKey. Fall back to legacyUrl for pages imported before legacyKey
       was being set, so an existing article is adopted instead of duplicated. */
    $entry = \craft\elements\Entry::find()->section('articles')->status(null)->legacyKey($key)->one();
    $matchedBy = 'legacyKey';
    if (!$entry) {
        $entry = \craft\elements\Entry::find()->section('articles')->status(null)
            ->legacyUrl($p['legacy_path'])->one();
        if ($entry) { $matchedBy = 'legacyUrl'; $adopted++; }
    }
    $isNew = ($entry === null);

    if ($isNew) {
        $entry = new \craft\elements\Entry();
        $entry->sectionId = $section->id;
        $entry->setTypeId($type->id);
        $entry->title = $p['title'] !== '' ? $p['title'] : $key;
        $created++;
    } else {
        $updated++;
    }

    /* editor_notes by position. Anything that is neither top nor bottom is left in the
       body, where the extraction already placed it, and counted for the report. */
    $noteTop = []; $noteBottom = [];
    foreach ($p['editor_notes'] as $n) {
        $pos = strtolower(trim((string)($n['position'] ?? '')));
        $txt = trim((string)($n['text'] ?? ''));
        if ($txt === '') { continue; }
        if ($pos === 'top') { $noteTop[] = $txt; $noteStats['top']++; }
        elseif ($pos === 'bottom') { $noteBottom[] = $txt; $noteStats['bottom']++; }
        else { $noteStats['other']++; }
    }

    /* communities_mentioned -> neighborhood, exact title match only */
    $catIds = [];
    foreach ($p['communities_mentioned'] as $name) {
        $name = trim((string)$name);
        if ($name === '') { continue; }
        if (!in_array($name, $CLOSED_COMMUNITIES, true)) {
            $offListCommunities[$name][] = $key;
        }
        if (isset($terms[$name])) { $catIds[] = $terms[$name]; }
        else { $unmatchedCommunities[$name][] = $key; }
    }
    $catIds = array_values(array_unique($catIds));

    /* dates_mentioned -> recordDates */
    $rows = []; $seen = [];
    foreach ($p['dates_mentioned'] as $dm) {
        $printed = trim((string)($dm['text_raw'] ?? ''));
        if ($printed === '') { continue; }
        $parsed = $parseDate($printed);
        if ($parsed === null) {
            $dateStats['skipped']++;
            $unparsedDates[] = $key . ' | ' . $printed;
            continue;
        }
        $dateStats['parsed']++;
        [$iso, $gran] = $parsed;
        $dedupe = $printed . '|' . $iso . '|' . $gran;
        if (isset($seen[$dedupe])) { $dateStats['deduped']++; continue; }
        $seen[$dedupe] = true;
        $rows[] = [
            'printed' => $printed,
            'iso' => $iso . ' 00:00:00',
            'granularity' => $gran,
            'label' => mb_substr(trim(preg_replace('/\s+/', ' ', (string)($dm['context'] ?? ''))), 0, 255),
            'confirmed' => false,
        ];
    }
    $dateStats['rows'] += count($rows);

    /* Everything this page offers. An empty value is dropped, never written. */
    $candidate = [
        'body' => (string)$p['body_text'],
        'subheadline' => (string)$p['subtitle'],
        'originalPublishDate' => (string)$p['date_raw'],
        'legacyUrl' => (string)$p['legacy_path'],
        'sourcePath' => (string)$p['source_url'],
        'finePrint' => (string)$p['fine_print_raw'],
        'webmasterNoteTop' => implode("\n\n", $noteTop),
        'webmasterNoteBottom' => implode("\n\n", $noteBottom),
        'legacyKey' => $key,
        'neighborhood' => $catIds,
        'recordDates' => $rows,
    ];
    foreach (['subtitle' => 'subheadline', 'date_raw' => 'originalPublishDate', 'fine_print_raw' => 'finePrint'] as $src => $handle) {
        if (trim((string)$p[$src]) === '') { $emptySource[$handle] = ($emptySource[$handle] ?? 0) + 1; }
    }

    $sets = [];
    foreach ($candidate as $handle => $value) {
        if ($isEmptyValue($value)) { continue; }
        if (!$hasField($entry, $handle)) {
            echo '  no such field on article: ' . $handle . PHP_EOL;
            continue;
        }
        if (!$isNew && !$OVERWRITE) {
            $existing = $readField($entry, $handle);
            if (!$isEmptyValue($existing)) { continue; }
        }
        $sets[$handle] = $value;
    }

    $label = str_pad($key, 24) . str_pad($p['_group'], 8);
    if ($isNew) {
        echo 'NEW    ' . $label . implode(', ', array_keys($sets)) . PHP_EOL;
    } else {
        echo 'UPDATE ' . $label . '#' . $entry->id . ' via ' . $matchedBy . ' -> '
            . (count($sets) ? implode(', ', array_keys($sets)) : 'nothing to fill') . PHP_EOL;
    }

    if ($APPLY && ($isNew || count($sets))) {
        foreach ($sets as $handle => $value) {
            try { $entry->setFieldValue($handle, $value); }
            catch (\Throwable $e) { echo '  field skip ' . $handle . ' on ' . $key . ': ' . $e->getMessage() . PHP_EOL; }
        }
        if (!$elements->saveElement($entry)) {
            echo 'SAVE FAILED ' . $key . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
            $failed++;
            continue;
        }
    }
    if ($entry->id) { $byKey[$key] = $entry->id; }
}

/* ---------------------------------------------------------------- pass 2 */

echo '=== pass 2: Story of Our Valley ===' . PHP_EOL;

$collection = \craft\elements\Entry::find()->section('collections')->status(null)
    ->title($COLLECTION_TITLE)->one();

if (!$collection) {
    echo 'ERROR: collection "' . $COLLECTION_TITLE . '" not found, no relations wired' . PHP_EOL;
} else {
    $ordered = $data['series_pages'];
    usort($ordered, function ($a, $b) { return $a['series_position'] <=> $b['series_position']; });

    $ids = []; $missing = [];
    foreach ($ordered as $p) {
        $k = trim((string)$p['legacy_key']);
        if (isset($byKey[$k])) { $ids[] = $byKey[$k]; }
        else { $missing[] = $k; }
    }

    echo 'collection #' . $collection->id . ' articlesInCollection -> ' . count($ids) . ' articles in series order' . PHP_EOL;
    if ($missing) { echo '  no entry id yet for: ' . implode(', ', $missing) . ' (dry run creates no ids)' . PHP_EOL; }

    foreach ($ordered as $p) {
        $k = trim((string)$p['legacy_key']);
        echo '  ' . str_pad((string)$p['series_position'], 3) . str_pad($k, 24)
            . 'partOfCollection -> #' . $collection->id . PHP_EOL;
    }
    echo '  related_pages get no collection: '
        . implode(', ', array_map(function ($p) { return $p['legacy_key']; }, $data['related_pages'])) . PHP_EOL;

    if ($APPLY) {
        foreach ($ordered as $p) {
            $k = trim((string)$p['legacy_key']);
            if (!isset($byKey[$k])) { continue; }
            $e = \craft\elements\Entry::find()->id($byKey[$k])->status(null)->one();
            if (!$e || !$hasField($e, 'partOfCollection')) { continue; }
            $cur = $readField($e, 'partOfCollection');
            $curIds = $cur ? array_map(function ($x) { return $x->id; }, $cur->all()) : [];
            if (in_array($collection->id, $curIds, true)) { continue; }
            try { $e->setFieldValue('partOfCollection', array_values(array_unique(array_merge($curIds, [$collection->id])))); }
            catch (\Throwable $ex) { echo '  partOfCollection skip on ' . $k . PHP_EOL; continue; }
            if (!$elements->saveElement($e)) {
                echo '  SAVE FAILED partOfCollection ' . $k . ': ' . json_encode($e->getErrors()) . PHP_EOL;
            }
        }
        $curCol = $readField($collection, 'articlesInCollection');
        $curColIds = $curCol ? array_map(function ($x) { return $x->id; }, $curCol->all()) : [];
        if ($ids && $curColIds === $ids) {
            echo '  articlesInCollection already in series order, left alone' . PHP_EOL;
        } elseif ($ids && $hasField($collection, 'articlesInCollection')) {
            try { $collection->setFieldValue('articlesInCollection', $ids); }
            catch (\Throwable $ex) { echo '  articlesInCollection skip: ' . $ex->getMessage() . PHP_EOL; }
            if (!$elements->saveElement($collection)) {
                echo '  SAVE FAILED collection: ' . json_encode($collection->getErrors()) . PHP_EOL;
            }
        }
    }
}

/* ---------------------------------------------------------------- pass 3 */

echo '=== pass 3: image inventory ===' . PHP_EOL;

$imageRows = [];
foreach ($pages as $p) {
    foreach ($p['images'] as $img) {
        $imageRows[] = [
            'legacy_key' => $p['legacy_key'],
            'group' => $p['_group'],
            'source_url' => $p['source_url'],
            'position_in_body' => $img['position_in_body'] ?? 0,
            'src_raw' => (string)($img['src_raw'] ?? ''),
            'links_to' => (string)($img['links_to'] ?? ''),
            'caption' => (string)($img['caption'] ?? ''),
            'alt' => (string)($img['alt'] ?? ''),
            'credit_raw' => (string)($img['credit_raw'] ?? ''),
        ];
    }
}
usort($imageRows, function ($a, $b) {
    return [$a['legacy_key'], $a['position_in_body']] <=> [$b['legacy_key'], $b['position_in_body']];
});

$out = [
    'meta' => [
        'section' => 'perkins',
        'source' => 'inventory/legacy/perkins.json',
        'crawled' => $data['meta']['crawled'] ?? '',
        'generated_by' => 'scripts/import/import_perkins.php',
        'image_count' => count($imageRows),
        'note' => 'src_raw is the path exactly as written in the legacy HTML. No image was downloaded. Match these against the drive.',
    ],
    'images' => $imageRows,
];
$json = json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($IMAGES_OUT, $json);
echo 'wrote ' . count($imageRows) . ' images to inventory/legacy/perkins-images.json' . PHP_EOL;

/* ---------------------------------------------------------------- report */

echo '=== summary ===' . PHP_EOL;
echo 'pages in file:        ' . count($pages) . ' (' . count($data['series_pages']) . ' series, ' . count($data['related_pages']) . ' related)' . PHP_EOL;
echo 'articles to create:   ' . $created . PHP_EOL;
echo 'articles to update:   ' . $updated . ' (' . $adopted . ' adopted on legacyUrl because legacyKey was empty)' . PHP_EOL;
if ($failed) { echo 'saves failed:         ' . $failed . PHP_EOL; }
echo 'images inventoried:   ' . count($imageRows) . PHP_EOL;
echo 'recordDates rows:     ' . $dateStats['rows'] . ' from ' . $dateStats['parsed'] . ' parsed, '
    . $dateStats['deduped'] . ' duplicates collapsed' . PHP_EOL;
echo 'dates not parsed:     ' . $dateStats['skipped'] . PHP_EOL;
echo 'editor notes:         top ' . $noteStats['top'] . ', bottom ' . $noteStats['bottom']
    . ', neither ' . $noteStats['other'] . ' (left in the body)' . PHP_EOL;

foreach ($emptySource as $handle => $n) {
    echo 'empty in extraction:  ' . $handle . ' on ' . $n . ' of ' . count($pages) . ' pages' . PHP_EOL;
}

if ($offListCommunities) {
    echo 'communities NOT on the closed list (not created):' . PHP_EOL;
    foreach ($offListCommunities as $name => $keys) {
        echo '  ' . $name . '  on ' . count($keys) . ' page(s): ' . implode(', ', array_unique($keys)) . PHP_EOL;
    }
} else {
    echo 'communities off the closed list: none' . PHP_EOL;
}

if ($unmatchedCommunities) {
    echo 'communities with no matching neighborhood term (not created):' . PHP_EOL;
    foreach ($unmatchedCommunities as $name => $keys) {
        echo '  ' . $name . '  on ' . count($keys) . ' page(s): ' . implode(', ', array_unique($keys)) . PHP_EOL;
    }
} else {
    echo 'communities with no matching term: none' . PHP_EOL;
}

if ($unparsedDates) {
    echo 'dates that could not be parsed:' . PHP_EOL;
    $counts = array_count_values($unparsedDates);
    ksort($counts);
    foreach ($counts as $line => $n) { echo '  ' . $line . ($n > 1 ? '  x' . $n : '') . PHP_EOL; }
}

echo 'NOTE: no Person, Place or Organization was created. entity_index is for adjudication.' . PHP_EOL;
