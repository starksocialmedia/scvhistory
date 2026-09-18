/**
 * Imports the Reynolds series (inventory/legacy/reynolds-full.json, 80 pages) into Craft.
 *
 * Craft already holds 23 of these from the WordPress import, so pass 1 matches an
 * existing Article before creating anything. Matching tries, in order: legacyKey,
 * legacyUrl, the tail of legacyUrl (one WordPress row lost its /scvhistory prefix),
 * then the normalised title among articles already in the collection (four rows have
 * no legacyUrl at all). Anything still unmatched is created.
 *
 * Body is never overwritten. Where an existing body differs from the legacy text the
 * difference is reported and the record is left alone, because we do not yet know
 * which version is authoritative.
 *
 * Pass 2 sets partOfCollection on every page and rewrites the collection's
 * articlesInCollection to the full TOC order. Pass 3 writes
 * inventory/legacy/reynolds-images.json; no image is downloaded.
 *
 * No Person, Place or Organization is created. entity_index needs adjudication.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_reynolds.php'))"
 */

$APPLY = false;
$OVERWRITE = false;          /* fills only empty fields on an existing article; body is excluded either way */
$INCLUDE_ALSO_BY = true;     /* the four Also-by essays the TOC lists but meta calls separate works */

$root = \Craft::getAlias('@root');
$path = $root . '/inventory/legacy/reynolds-full.json';
if (!file_exists($path)) {
    echo 'ERROR: ' . $path . ' not found' . PHP_EOL;
    return;
}
$data = json_decode(file_get_contents($path), true);
if (!is_array($data) || !isset($data['pages'])) {
    echo 'ERROR: reynolds-full.json has no pages' . PHP_EOL;
    return;
}

$elements = Craft::$app->getElements();
$COLLECTION_SLUG = 'history-of-the-santa-clarita-valley';
$IMAGES_OUT = $root . '/inventory/legacy/reynolds-images.json';
$ALSO_BY = ['reynolds-castaicethnography', 'reynolds121484', 'reynolds_firstpresbyterian_0686', 'dispatch1501reynolds'];

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
    if (is_object($v)) { return trim(strip_tags((string)$v)) === ''; }
    return false;
};

/* "Chapter 10: Solitary Hiker" and "10. Solitary Hiker" are the same chapter. */
$normTitle = function (string $t): string {
    $t = mb_strtolower(trim($t));
    $t = preg_replace('/^chapter\s+/u', '', $t);
    $t = str_replace([':', '.', ',', "\u{2019}", "'"], [' ', ' ', ' ', '', ''], $t);
    $t = preg_replace('/[^a-z0-9 ]+/u', ' ', $t);
    return trim(preg_replace('/\s+/', ' ', $t));
};

$words = function (string $s): array {
    $s = preg_replace('/\s+/u', ' ', trim(strip_tags($s)));
    $s = str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{2014}", "\u{2013}", "\u{00A0}"],
                     ["'", "'", '"', '"', ' ', ' ', ' '], $s);
    $s = mb_strtolower($s);
    $s = preg_replace('/[^a-z0-9 ]+/u', ' ', $s);
    $out = preg_split('/\s+/', trim($s), -1, PREG_SPLIT_NO_EMPTY);
    return $out ?: [];
};

$MONTHS = [
    'jan' => 1, 'january' => 1, 'feb' => 2, 'february' => 2, 'mar' => 3, 'march' => 3,
    'apr' => 4, 'april' => 4, 'may' => 5, 'jun' => 6, 'june' => 6, 'jul' => 7, 'july' => 7,
    'aug' => 8, 'august' => 8, 'sep' => 9, 'sept' => 9, 'september' => 9,
    'oct' => 10, 'october' => 10, 'nov' => 11, 'november' => 11, 'dec' => 12, 'december' => 12,
];

$parseDate = function (string $rawIn) use ($MONTHS): ?array {
    $t = trim(preg_replace('/\s+/', ' ', $rawIn));
    $t = rtrim($t, " .,;:!?)]");
    $t = ltrim($t, " ([");
    if ($t === '') { return null; }
    $circa = (bool)preg_match('/\b(c\.|ca\.|circa|about|around|approximately|roughly|early|late|mid)\b/i', $t);
    $lead = '/^(in|on|of|by|from|until|till|to|since|after|before|during|about|around|approximately|roughly|circa|c\.|ca\.|early|late|mid|the|year|that|this)\s+/i';
    do { $before = $t; $t = preg_replace($lead, '', $t); } while ($t !== $before);
    $t = trim($t);
    if ($t === '') { return null; }
    $t = preg_replace('/\b(\d{1,2})(st|nd|rd|th)\b/i', '$1', $t);
    $ok = function (int $y, int $m, int $d): bool { return $y >= 1000 && $y <= 2100 && checkdate($m, $d, $y); };
    $g = $circa ? 'circa' : null;
    if (preg_match('/^([A-Za-z]+)\.? (\d{1,2}),? (\d{4})$/', $t, $m)) {
        $mon = $MONTHS[strtolower($m[1])] ?? null;
        if ($mon && $ok((int)$m[3], $mon, (int)$m[2])) { return [sprintf('%04d-%02d-%02d', (int)$m[3], $mon, (int)$m[2]), 'day']; }
    }
    if (preg_match('/^(\d{1,2}) ([A-Za-z]+)\.?,? (\d{4})$/', $t, $m)) {
        $mon = $MONTHS[strtolower($m[2])] ?? null;
        if ($mon && $ok((int)$m[3], $mon, (int)$m[1])) { return [sprintf('%04d-%02d-%02d', (int)$m[3], $mon, (int)$m[1]), 'day']; }
    }
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $t, $m)) {
        if ($ok((int)$m[3], (int)$m[1], (int)$m[2])) { return [sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[1], (int)$m[2]), 'day']; }
    }
    if (preg_match('/^([A-Za-z]+)\.?,? (\d{4})$/', $t, $m)) {
        $mon = $MONTHS[strtolower($m[1])] ?? null;
        $y = (int)$m[2];
        if ($mon && $y >= 1000 && $y <= 2100) { return [sprintf('%04d-%02d-01', $y, $mon), 'month']; }
    }
    if (preg_match('/^(\d{4})$/', $t)) {
        $y = (int)$t;
        if ($y >= 1000 && $y <= 2100) { return [sprintf('%04d-01-01', $y), $g ?: 'year']; }
    }
    return null;
};

/**
 * Compares an existing body against the legacy text, ignoring the legacy page's
 * navigation chrome by anchoring on the first words of the existing body.
 */
$compareBody = function (array $legacy, array $craft): array {
    if (!count($craft)) { return ['empty-in-craft', 0, '', '']; }
    if (!count($legacy)) { return ['empty-in-legacy', 0, '', '']; }

    $anchorLen = min(8, count($craft));
    $anchor = array_slice($craft, 0, $anchorLen);
    $off = -1;
    $limit = count($legacy) - $anchorLen;
    for ($i = 0; $i <= $limit; $i++) {
        if (array_slice($legacy, $i, $anchorLen) === $anchor) { $off = $i; break; }
    }
    if ($off === -1) { return ['no-anchor', 0, implode(' ', array_slice($craft, 0, 12)), implode(' ', array_slice($legacy, 0, 12))]; }

    $slice = array_slice($legacy, $off, count($craft));
    if ($slice === $craft) { return ['same', 0, '', '']; }

    $n = min(count($slice), count($craft));
    $at = $n;
    for ($i = 0; $i < $n; $i++) {
        if ($slice[$i] !== $craft[$i]) { $at = $i; break; }
    }
    return [
        'differs',
        $at,
        implode(' ', array_slice($craft, max(0, $at - 4), 12)),
        implode(' ', array_slice($slice, max(0, $at - 4), 12)),
    ];
};

$terms = [];
foreach (\craft\elements\Category::find()->group('neighborhood')->status(null)->all() as $c) {
    $terms[$c->title] = $c->id;
}

/* ---------------------------------------------------------------- pass 1 */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($OVERWRITE ? ' (OVERWRITE ON)' : '') . PHP_EOL;

$section = Craft::$app->getEntries()->getSectionByHandle('articles');
$type = Craft::$app->getEntries()->getEntryTypeByHandle('article');
$collection = \craft\elements\Entry::find()->section('collections')->slug($COLLECTION_SLUG)->status(null)->one();
if (!$section || !$type) { echo 'ERROR: articles section or article entry type missing' . PHP_EOL; return; }
if (!$collection) { echo 'ERROR: collection ' . $COLLECTION_SLUG . ' not found' . PHP_EOL; return; }

echo 'collection #' . $collection->id . ' currently holds ' . $collection->articlesInCollection->count() . ' articles' . PHP_EOL;

/* Title index over articles already in the collection, for the rows with no legacyUrl. */
$byTitle = [];
foreach ($collection->articlesInCollection->all() as $a) {
    $byTitle[$normTitle((string)$a->title)] = $a;
}

$pages = $data['pages'];
usort($pages, function ($a, $b) { return $a['series_position'] <=> $b['series_position']; });

$created = 0; $updated = 0; $failed = 0;
$matchCounts = ['legacyKey' => 0, 'legacyUrl' => 0, 'legacyUrl-tail' => 0, 'title' => 0];
$byKey = [];
$bodyReport = [];
$unmatchedCommunities = []; $offListCommunities = [];
$unparsedDates = []; $dateStats = ['parsed' => 0, 'skipped' => 0, 'rows' => 0, 'deduped' => 0];
$noteStats = ['top' => 0, 'bottom' => 0, 'other' => 0];

foreach ($pages as $p) {
    $key = trim((string)$p['legacy_key']);
    if ($key === '') { echo 'SKIP page with no legacy_key: ' . $p['source_url'] . PHP_EOL; continue; }

    $entry = \craft\elements\Entry::find()->section('articles')->status(null)->legacyKey($key)->one();
    $matchedBy = 'legacyKey';

    if (!$entry) {
        $entry = \craft\elements\Entry::find()->section('articles')->status(null)->legacyUrl($p['legacy_path'])->one();
        if ($entry) { $matchedBy = 'legacyUrl'; }
    }
    if (!$entry) {
        /* one WordPress row stored /signal/reynolds/part15.html without the /scvhistory prefix */
        $tail = preg_replace('~^/scvhistory~', '', $p['legacy_path']);
        if ($tail !== $p['legacy_path'] && $tail !== '') {
            $entry = \craft\elements\Entry::find()->section('articles')->status(null)->legacyUrl($tail)->one();
            if ($entry) { $matchedBy = 'legacyUrl-tail'; }
        }
    }
    if (!$entry) {
        $cand = $byTitle[$normTitle((string)$p['title'])] ?? null;
        if ($cand) { $entry = $cand; $matchedBy = 'title'; }
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
        $matchCounts[$matchedBy]++;
    }

    /* body: compare, never overwrite */
    $legacyBody = (string)$p['body_text'];
    $writeBody = false;
    if ($isNew) {
        $writeBody = true;
    } else {
        $existingBody = $readField($entry, 'body');
        if ($isEmptyValue($existingBody)) {
            $writeBody = true;
        } else {
            [$verdict, $at, $craftCtx, $legacyCtx] = $compareBody($words($legacyBody), $words((string)$existingBody));
            $bodyReport[] = [
                'key' => $key, 'id' => $entry->id, 'title' => (string)$entry->title,
                'verdict' => $verdict, 'at' => $at,
                'craftWords' => count($words((string)$existingBody)), 'legacyWords' => count($words($legacyBody)),
                'craftCtx' => $craftCtx, 'legacyCtx' => $legacyCtx,
            ];
        }
    }

    $noteTop = []; $noteBottom = [];
    foreach ($p['editor_notes'] as $n) {
        $pos = strtolower(trim((string)($n['position'] ?? '')));
        $txt = trim((string)($n['text'] ?? ''));
        if ($txt === '') { continue; }
        if ($pos === 'top') { $noteTop[] = $txt; $noteStats['top']++; }
        elseif ($pos === 'bottom') { $noteBottom[] = $txt; $noteStats['bottom']++; }
        else { $noteStats['other']++; }
    }

    $catIds = [];
    foreach ($p['communities_mentioned'] as $name) {
        $name = trim((string)$name);
        if ($name === '') { continue; }
        if (!in_array($name, $CLOSED_COMMUNITIES, true)) { $offListCommunities[$name][] = $key; }
        if (isset($terms[$name])) { $catIds[] = $terms[$name]; }
        else { $unmatchedCommunities[$name][] = $key; }
    }
    $catIds = array_values(array_unique($catIds));

    $rows = []; $seen = [];
    foreach ($p['dates_mentioned'] as $dm) {
        $printed = trim((string)($dm['text_raw'] ?? ''));
        if ($printed === '') { continue; }
        $parsed = $parseDate($printed);
        if ($parsed === null) { $dateStats['skipped']++; $unparsedDates[] = $key . ' | ' . $printed; continue; }
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

    $candidate = [
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
    if ($writeBody) { $candidate['body'] = $legacyBody; }

    $sets = [];
    foreach ($candidate as $handle => $value) {
        if ($isEmptyValue($value)) { continue; }
        if (!$hasField($entry, $handle)) { continue; }
        if (!$isNew && $handle !== 'body' && !$OVERWRITE) {
            if (!$isEmptyValue($readField($entry, $handle))) { continue; }
        }
        $sets[$handle] = $value;
    }

    $label = str_pad((string)$p['series_position'], 4) . str_pad($key, 34);
    if ($isNew) {
        echo 'NEW    ' . $label . implode(', ', array_keys($sets)) . PHP_EOL;
    } else {
        echo 'UPDATE ' . $label . '#' . $entry->id . ' via ' . str_pad($matchedBy, 15)
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

echo '=== pass 2: collection order ===' . PHP_EOL;

$ids = []; $missingIds = []; $skippedAlsoBy = [];
foreach ($pages as $p) {
    $k = trim((string)$p['legacy_key']);
    if (!$INCLUDE_ALSO_BY && in_array($k, $ALSO_BY, true)) { $skippedAlsoBy[] = $k; continue; }
    if (isset($byKey[$k])) { $ids[] = $byKey[$k]; }
    else { $missingIds[] = $k; }
}
echo 'articlesInCollection -> ' . count($ids) . ' articles in TOC order' . PHP_EOL;
if ($missingIds) { echo '  no entry id yet for ' . count($missingIds) . ' pages (a dry run creates no ids)' . PHP_EOL; }
if ($skippedAlsoBy) { echo '  Also-by essays excluded: ' . implode(', ', $skippedAlsoBy) . PHP_EOL; }

if ($APPLY) {
    foreach ($pages as $p) {
        $k = trim((string)$p['legacy_key']);
        if (!$INCLUDE_ALSO_BY && in_array($k, $ALSO_BY, true)) { continue; }
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
        echo '  articlesInCollection already in TOC order, left alone' . PHP_EOL;
    } elseif ($ids) {
        try { $collection->setFieldValue('articlesInCollection', $ids); }
        catch (\Throwable $ex) { echo '  articlesInCollection skip: ' . $ex->getMessage() . PHP_EOL; }
        if (!$elements->saveElement($collection)) {
            echo '  SAVE FAILED collection: ' . json_encode($collection->getErrors()) . PHP_EOL;
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
            'series_position' => $p['series_position'],
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
    return [$a['series_position'], $a['position_in_body']] <=> [$b['series_position'], $b['position_in_body']];
});
$out = [
    'meta' => [
        'section' => 'reynolds',
        'source' => 'inventory/legacy/reynolds-full.json',
        'crawled' => $data['meta']['crawled'] ?? '',
        'generated_by' => 'scripts/import/import_reynolds.php',
        'image_count' => count($imageRows),
        'note' => 'src_raw is the path exactly as written in the legacy HTML. No image was downloaded. Match these against the drive.',
    ],
    'images' => $imageRows,
];
file_put_contents($IMAGES_OUT, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
echo 'wrote ' . count($imageRows) . ' images to inventory/legacy/reynolds-images.json' . PHP_EOL;

/* ---------------------------------------------------------------- report */

echo '=== bodies already in Craft ===' . PHP_EOL;
$tally = [];
foreach ($bodyReport as $b) { $tally[$b['verdict']] = ($tally[$b['verdict']] ?? 0) + 1; }
foreach ($tally as $v => $n) { echo '  ' . str_pad($v, 16) . $n . PHP_EOL; }
foreach ($bodyReport as $b) {
    if ($b['verdict'] === 'same') { continue; }
    echo '  ' . str_pad($b['key'], 14) . '#' . str_pad((string)$b['id'], 6) . str_pad($b['verdict'], 16)
        . 'craft ' . $b['craftWords'] . 'w, legacy ' . $b['legacyWords'] . 'w' . PHP_EOL;
    echo '    title:  ' . $b['title'] . PHP_EOL;
    if ($b['verdict'] === 'differs') {
        echo '    diverges at word ' . $b['at'] . PHP_EOL;
        echo '    craft:  ... ' . $b['craftCtx'] . ' ...' . PHP_EOL;
        echo '    legacy: ... ' . $b['legacyCtx'] . ' ...' . PHP_EOL;
    } elseif ($b['verdict'] === 'no-anchor') {
        echo '    craft:  ' . $b['craftCtx'] . ' ...' . PHP_EOL;
        echo '    legacy: ' . $b['legacyCtx'] . ' ...' . PHP_EOL;
    }
}

echo '=== summary ===' . PHP_EOL;
echo 'pages in file:        ' . count($pages) . PHP_EOL;
echo 'articles to create:   ' . $created . PHP_EOL;
echo 'articles to update:   ' . $updated . PHP_EOL;
foreach ($matchCounts as $how => $n) { if ($n) { echo '  matched by ' . str_pad($how, 16) . $n . PHP_EOL; } }
if ($failed) { echo 'saves failed:         ' . $failed . PHP_EOL; }
echo 'bodies left alone:    ' . count($bodyReport) . ' (existing body never overwritten)' . PHP_EOL;
echo 'images inventoried:   ' . count($imageRows) . PHP_EOL;
echo 'recordDates rows:     ' . $dateStats['rows'] . ' from ' . $dateStats['parsed'] . ' parsed, ' . $dateStats['deduped'] . ' duplicates collapsed' . PHP_EOL;
echo 'dates not parsed:     ' . $dateStats['skipped'] . PHP_EOL;
echo 'editor notes:         top ' . $noteStats['top'] . ', bottom ' . $noteStats['bottom'] . ', neither ' . $noteStats['other'] . ' (left in the body)' . PHP_EOL;

if ($offListCommunities) {
    echo 'communities NOT on the closed list (not created):' . PHP_EOL;
    foreach ($offListCommunities as $name => $keys) { echo '  ' . $name . '  x' . count($keys) . PHP_EOL; }
} else { echo 'communities off the closed list: none' . PHP_EOL; }

if ($unmatchedCommunities) {
    echo 'communities with no matching neighborhood term (not created):' . PHP_EOL;
    foreach ($unmatchedCommunities as $name => $keys) { echo '  ' . $name . '  x' . count($keys) . ': ' . implode(', ', array_slice(array_unique($keys), 0, 8)) . PHP_EOL; }
} else { echo 'communities with no matching term: none' . PHP_EOL; }

if ($unparsedDates) {
    $counts = array_count_values($unparsedDates);
    echo 'dates that could not be parsed: ' . count($counts) . ' distinct' . PHP_EOL;
    $shown = 0;
    foreach ($counts as $line => $n) {
        if ($shown++ >= 20) { echo '  ... and ' . (count($counts) - 20) . ' more' . PHP_EOL; break; }
        echo '  ' . $line . ($n > 1 ? '  x' . $n : '') . PHP_EOL;
    }
}

echo 'NOTE: no Person, Place or Organization was created. entity_index is for adjudication.' . PHP_EOL;
