/**
 * Imports the War Memorial casualty pages (inventory/legacy/warmemorial.json, 54 pages).
 *
 * Craft holds 36 of these already. Pass 1 matches on legacyKey, falls back to
 * legacyUrl, and creates the rest. The existing 36 were hand-checked, so they are
 * gap-filled only: a non-empty field is never overwritten. The 18 new ones are
 * created in full.
 *
 * service_record keys are the labels exactly as printed on the legacy page. Mapped
 * labels go to their wm field; everything else goes to wmServiceExtra as a row of
 * label and value, both verbatim. Nothing is dropped.
 *
 * Where two labels map to the same field on one record, the first in $LABEL_MAP wins
 * and the other goes to overflow, which is the rule the brief sets for College.
 *
 * Values are never corrected. "Amry of the United States" is a typo on the legacy
 * site and imports verbatim; the affected records are reported.
 *
 * The pre-flight reports any duplicate in Craft but touches nothing: a row with no
 * legacyKey is never matched, so a duplicate stays exactly as it is for a human.
 *
 * Pass 2 writes inventory/legacy/warmemorial-images.json. No image is downloaded.
 *
 * No Person, Place or Organization is created. entity_index needs adjudication.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_warmemorial.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$root = \Craft::getAlias('@root');
$path = $root . '/inventory/legacy/warmemorial.json';
if (!file_exists($path)) { echo 'ERROR: ' . $path . ' not found' . PHP_EOL; return; }
$data = json_decode(file_get_contents($path), true);
if (!is_array($data) || !isset($data['pages'])) { echo 'ERROR: warmemorial.json has no pages' . PHP_EOL; return; }

$elements = Craft::$app->getElements();
$IMAGES_OUT = $root . '/inventory/legacy/warmemorial-images.json';

/* Ordered. Two labels hitting one field: the earlier entry wins, the later overflows. */
$LABEL_MAP = [
    'Home of Record' => 'wmHomeOfRecord',
    'Date of Birth' => 'wmDateOfBirth',
    'Birth Year' => 'wmDateOfBirth',
    'High School' => 'wmHighSchool',
    'College' => 'wmHighSchool',
    'Service' => 'wmBranch',
    'Branch' => 'wmBranch',
    'Rank' => 'wmRank',
    'Grade' => 'wmRank',
    'ID No' => 'wmServiceId',
    'Specialty' => 'wmSpecialty',
    'Specialty (MOS)' => 'wmSpecialty',
    'Length of Service' => 'wmLengthOfService',
    'Term of Enlistment' => 'wmLengthOfService',
    'Unit' => 'wmUnit',
    'Combat Organization' => 'wmUnit',
    'Start Tour' => 'wmStartTour',
    'Start Service' => 'wmStartTour',
    'Active Service Dates' => 'wmStartTour',
    'Based' => 'wmBase',
    'Supporting' => 'wmCombatOperations',
    'Incident Date' => 'wmIncidentDate',
    'Casualty Date' => 'deathDate',
    'Death Date' => 'deathDate',
    'Official Date of Death' => 'deathDate',
    'Age at Loss' => 'wmAgeAtLoss',
    'Age at (Loss)' => 'wmAgeAtLoss',
    'Location' => 'wmIncidentLocation',
    'Remains' => 'burialPlace',
    'Interment' => 'burialPlace',
    'Cemetery Name' => 'burialPlace',
    'Disposition' => 'burialPlace',
    'Narrative' => 'wmNarrative',
    'Casualty Reason' => 'wmCasualtyReason',
    'Casualty Type' => 'wmCasualtyType',
    'Vietnam Memorial Wall' => 'wmWallReference',
    'Official Memorial' => 'wmWallReference',
    'Monument' => 'wmWallReference',
    'Birthplace' => 'wmBirthplace',
    'Place of Birth' => 'wmBirthplace',
    'Nativity State or Country' => 'wmBirthplace',
    'Country of Birth' => 'wmBirthplace',
    'Casualty Detail' => 'wmCasualtyDetail',
    'Grade at Loss' => 'wmGradeAtLoss',
    'Grade at loss' => 'wmGradeAtLoss',
    'Notes' => 'wmNotes',
    'Note' => 'wmNotes',
    'U.S. Awards' => 'wmAwards',
    'Family' => 'wmFamily',
    'Selective Service Registration Date' => 'wmSelectiveServiceDate',
];

/* Letter fragments the extractor read as labels. Noise, not data: skipped rather
   than written to the overflow table, and reported. */
$NOISE_LABELS = ['ALSO', 'BELOW', 'RE', 'Dear Mrs. Ward', 'Dear Recipient'];

$CONFLICT = [
    'ww1' => 'World War I',
    'ww2' => 'World War II',
    'korea' => 'Korean War',
    'vietnam' => 'Vietnam War',
    'terror' => 'War on Terror',
];

/* A trailing period that terminates one of these, or a single initial, is part of
   the name and is left alone. Everything else is a transcription artifact. */
$KEEP_PERIOD_AFTER = ['jr', 'sr', 'ii', 'iii', 'iv', 'st', 'mr', 'mrs', 'ms', 'dr', 'ph.d', 'm.d'];

$CLOSED_COMMUNITIES = [
    'Acton', 'Agua Dulce', 'Bouquet Canyon', 'Camulos', 'Canyon Country', 'Castaic',
    'Castaic Junction', 'Fair Oaks Ranch', 'Fillmore', 'Frazier Park', 'Haskell Canyon',
    'Hasley Canyon', 'Lake Hughes', 'Lebec', 'Mentryville', 'Mint Canyon', 'Mojave Desert',
    'Newhall', 'Pico Canyon', 'Piru', 'Placerita Canyon', 'Potrero Canyon', 'Ravenna',
    'San Francisquito Canyon', 'Sand Canyon', 'Santa Clarita', 'Saugus', 'Saugus/Valencia',
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

$trimTitle = function (string $t) use ($KEEP_PERIOD_AFTER): array {
    $t = trim($t);
    if ($t === '' || substr($t, -1) !== '.') { return [$t, false, '']; }
    $parts = preg_split('/\s+/', $t);
    $last = rtrim((string)end($parts), '.');
    $bare = strtolower(trim($last, '"\'' . "\u{201C}\u{201D}"));
    if (in_array($bare, $KEEP_PERIOD_AFTER, true)) { return [$t, false, 'ends in ' . $last . '.']; }
    if (mb_strlen($bare) === 1) { return [$t, false, 'ends in the initial ' . $last . '.']; }
    return [rtrim($t, '.'), true, ''];
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
    $t = ltrim($t, " ([~");
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

$terms = [];
foreach (\craft\elements\Category::find()->group('neighborhood')->status(null)->all() as $c) {
    $terms[$c->title] = $c->id;
}

/* ---------------------------------------------------------------- pass 1 */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$section = Craft::$app->getEntries()->getSectionByHandle('warMemorials');
$type = Craft::$app->getEntries()->getEntryTypeByHandle('warMemorial');
if (!$section || !$type) { echo 'ERROR: warMemorials section or warMemorial entry type missing' . PHP_EOL; return; }

/* Pre-flight: a row this script cannot reach, because something else already
   claims its legacy URL, is worth knowing about before anything is written. */
$byUrl = []; $noKey = [];
foreach (\craft\elements\Entry::find()->section('warMemorials')->status(null)->all() as $e) {
    $u = trim((string)$e->legacyUrl);
    if ($u !== '') { $byUrl[$u][] = '#' . $e->id . ' ' . $e->title; }
    if (trim((string)$e->legacyKey) === '') { $noKey[] = '#' . $e->id . ' ' . $e->title . ' (' . ($u ?: 'no legacyUrl') . ')'; }
}
foreach ($byUrl as $u => $rows) {
    if (count($rows) > 1) { echo 'DUPLICATE in Craft: ' . $u . ' -> ' . implode(' | ', $rows) . PHP_EOL; }
}
if ($noKey) { echo 'warMemorials rows with no legacyKey: ' . implode(' | ', $noKey) . PHP_EOL; }

$pages = $data['pages'];
usort($pages, function ($a, $b) { return strcmp($a['legacy_key'], $b['legacy_key']); });

$created = 0; $updated = 0; $failed = 0;
$matchCounts = ['legacyKey' => 0, 'legacyUrl' => 0];
$newNames = [];
$titleChanged = []; $titleKept = [];
$overflowLabels = []; $overflowRows = 0;
$displaced = [];
$noConflict = [];
$unmatchedCommunities = []; $offListCommunities = [];
$unparsedDates = []; $dateStats = ['parsed' => 0, 'skipped' => 0, 'rows' => 0, 'deduped' => 0];
$typoRecords = [];
$missingFields = [];
$noiseSkipped = [];

foreach ($pages as $p) {
    $key = trim((string)$p['legacy_key']);
    if ($key === '') { echo 'SKIP page with no legacy_key: ' . $p['source_url'] . PHP_EOL; continue; }

    $entry = \craft\elements\Entry::find()->section('warMemorials')->status(null)->legacyKey($key)->one();
    $matchedBy = 'legacyKey';
    if (!$entry) {
        $entry = \craft\elements\Entry::find()->section('warMemorials')->status(null)->legacyUrl($p['legacy_path'])->one();
        if ($entry) { $matchedBy = 'legacyUrl'; }
    }
    $isNew = ($entry === null);

    [$title, $wasTrimmed, $keepReason] = $trimTitle((string)$p['title']);
    if ($wasTrimmed) { $titleChanged[] = $key . '  ' . $p['title'] . '  ->  ' . $title; }
    elseif ($keepReason !== '') { $titleKept[] = $key . '  ' . $p['title'] . '  (' . $keepReason . ')'; }

    if ($isNew) {
        $entry = new \craft\elements\Entry();
        $entry->sectionId = $section->id;
        $entry->setTypeId($type->id);
        $entry->title = $title !== '' ? $title : $key;
        $created++;
        $newNames[] = $title;
    } else {
        $updated++;
        $matchCounts[$matchedBy]++;
    }

    /* conflict from the legacy_key prefix */
    $prefix = explode('_', $key)[0];
    $conflict = $CONFLICT[$prefix] ?? '';
    if ($conflict === '') { $noConflict[] = $key; }

    /* service_record: mapped fields first in $LABEL_MAP order, the rest to overflow */
    $record = $p['service_record'];
    $mapped = []; $usedLabels = [];
    foreach ($LABEL_MAP as $label => $handle) {
        if (!array_key_exists($label, $record)) { continue; }
        $value = trim((string)$record[$label]);
        if ($value === '') { $usedLabels[$label] = true; continue; }
        if (isset($mapped[$handle])) {
            $displaced[$handle . ': ' . $label][] = $key;
            continue;   /* falls through to overflow below */
        }
        $mapped[$handle] = $value;
        $usedLabels[$label] = true;
        if (stripos($value, 'Amry') !== false) { $typoRecords[] = $key . '  ' . $label . ' = ' . $value; }
    }

    $extra = [];
    foreach ($record as $label => $value) {
        if (isset($usedLabels[$label])) { continue; }
        if (in_array((string)$label, $NOISE_LABELS, true)) {
            $noiseSkipped[(string)$label][] = $key;
            continue;
        }
        $v = trim((string)$value);
        $extra[] = ['label' => (string)$label, 'value' => $v];
        $overflowLabels[(string)$label][] = $key;
        $overflowRows++;
        if (stripos($v, 'Amry') !== false) { $typoRecords[] = $key . '  ' . $label . ' = ' . $v; }
    }

    /* communities */
    $catIds = [];
    foreach ($p['communities_mentioned'] as $name) {
        $name = trim((string)$name);
        if ($name === '') { continue; }
        if (!in_array($name, $CLOSED_COMMUNITIES, true)) { $offListCommunities[$name][] = $key; }
        if (isset($terms[$name])) { $catIds[] = $terms[$name]; }
        else { $unmatchedCommunities[$name][] = $key; }
    }
    $catIds = array_values(array_unique($catIds));

    /* dates */
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

    $candidate = array_merge($mapped, [
        'wmConflict' => $conflict,
        'legacyUrl' => (string)$p['legacy_path'],
        'sourcePath' => (string)$p['source_url'],
        'legacyKey' => $key,
        'legacyHtml' => (string)$p['body_html'],
        'body' => (string)$p['body_text'],
        'neighborhood' => $catIds,
        'recordDates' => $rows,
        'wmServiceExtra' => $extra,
    ]);

    $sets = [];
    foreach ($candidate as $handle => $value) {
        if ($isEmptyValue($value)) { continue; }
        if (!$hasField($entry, $handle)) { $missingFields[$handle] = ($missingFields[$handle] ?? 0) + 1; continue; }
        if (!$isNew && !$isEmptyValue($readField($entry, $handle))) { continue; }
        $sets[$handle] = $value;
    }

    $label = str_pad($key, 26);
    if ($isNew) {
        echo 'NEW    ' . $label . str_pad($title, 32) . count($sets) . ' fields, ' . count($extra) . ' overflow' . PHP_EOL;
    } else {
        echo 'FILL   ' . $label . '#' . str_pad((string)$entry->id, 6) . 'via ' . str_pad($matchedBy, 11)
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
        }
    }
}

/* ---------------------------------------------------------------- pass 2 */

echo '=== pass 2: image inventory ===' . PHP_EOL;

$imageRows = [];
foreach ($pages as $p) {
    $prefix = explode('_', (string)$p['legacy_key'])[0];
    foreach ($p['images'] as $img) {
        $imageRows[] = [
            'legacy_key' => $p['legacy_key'],
            'group' => $CONFLICT[$prefix] ?? '',
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
        'section' => 'warmemorial',
        'source' => 'inventory/legacy/warmemorial.json',
        'crawled' => $data['meta']['crawled'] ?? '',
        'generated_by' => 'scripts/import/import_warmemorial.php',
        'image_count' => count($imageRows),
        'note' => 'src_raw is the path exactly as written in the legacy HTML. No image was downloaded. Match these against the drive.',
    ],
    'images' => $imageRows,
];
file_put_contents($IMAGES_OUT, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
echo 'wrote ' . count($imageRows) . ' images to inventory/legacy/warmemorial-images.json' . PHP_EOL;

/* ---------------------------------------------------------------- report */

echo '=== the 18 new casualties ===' . PHP_EOL;
foreach ($newNames as $n) { echo '  ' . $n . PHP_EOL; }

echo '=== titles ===' . PHP_EOL;
if ($titleChanged) {
    echo 'trailing period removed:' . PHP_EOL;
    foreach ($titleChanged as $t) { echo '  ' . $t . PHP_EOL; }
}
if ($titleKept) {
    echo 'trailing period kept, it is part of the name:' . PHP_EOL;
    foreach ($titleKept as $t) { echo '  ' . $t . PHP_EOL; }
}

echo '=== overflow into wmServiceExtra ===' . PHP_EOL;
echo $overflowRows . ' rows across ' . count($overflowLabels) . ' distinct labels' . PHP_EOL;
uasort($overflowLabels, function ($a, $b) { return count($b) <=> count($a); });
foreach ($overflowLabels as $lab => $keys) {
    echo '  ' . str_pad((string)count($keys), 4) . str_pad($lab, 40) . implode(', ', array_slice(array_unique($keys), 0, 4))
        . (count(array_unique($keys)) > 4 ? ' ...' : '') . PHP_EOL;
}

if ($displaced) {
    echo 'labels that overflowed because the field was already taken:' . PHP_EOL;
    foreach ($displaced as $what => $keys) {
        echo '  ' . str_pad((string)count($keys), 4) . str_pad($what, 44) . implode(', ', array_slice($keys, 0, 4)) . PHP_EOL;
    }
}

if ($noiseSkipped) {
    echo 'skipped as noise, not written anywhere:' . PHP_EOL;
    foreach ($noiseSkipped as $lab => $keys) {
        echo '  ' . str_pad((string)count($keys), 4) . str_pad($lab, 22) . implode(', ', array_unique($keys)) . PHP_EOL;
    }
}

if ($typoRecords) {
    echo '=== verbatim typos, not corrected ===' . PHP_EOL;
    foreach (array_unique($typoRecords) as $t) { echo '  ' . $t . PHP_EOL; }
}

echo '=== summary ===' . PHP_EOL;
echo 'pages in file:        ' . count($pages) . PHP_EOL;
echo 'records to create:    ' . $created . PHP_EOL;
echo 'records to gap-fill:  ' . $updated . PHP_EOL;
foreach ($matchCounts as $how => $n) { if ($n) { echo '  matched by ' . str_pad($how, 12) . $n . PHP_EOL; } }
if ($failed) { echo 'saves failed:         ' . $failed . PHP_EOL; }
if ($noConflict) { echo 'no conflict for prefix on: ' . implode(', ', $noConflict) . PHP_EOL; }
echo 'images inventoried:   ' . count($imageRows) . PHP_EOL;
foreach ($missingFields as $h => $n) {
    echo 'NOT on the warMemorial entry type, so dropped: ' . $h . ' (wanted on ' . $n . ' pages)' . PHP_EOL;
}
echo 'recordDates rows:     ' . $dateStats['rows'] . ' from ' . $dateStats['parsed'] . ' parsed, ' . $dateStats['deduped'] . ' duplicates collapsed' . PHP_EOL;
echo 'dates not parsed:     ' . $dateStats['skipped'] . PHP_EOL;

if ($offListCommunities) {
    echo 'communities NOT on the closed list (not created):' . PHP_EOL;
    foreach ($offListCommunities as $name => $keys) { echo '  ' . $name . ' x' . count($keys) . PHP_EOL; }
} else { echo 'communities off the closed list: none' . PHP_EOL; }
if ($unmatchedCommunities) {
    echo 'communities with no matching neighborhood term (not created):' . PHP_EOL;
    foreach ($unmatchedCommunities as $name => $keys) { echo '  ' . $name . ' x' . count($keys) . PHP_EOL; }
} else { echo 'communities with no matching term: none' . PHP_EOL; }

if ($unparsedDates) {
    $counts = array_count_values($unparsedDates);
    echo 'dates that could not be parsed: ' . count($counts) . ' distinct' . PHP_EOL;
    $shown = 0;
    foreach ($counts as $line => $n) {
        if ($shown++ >= 15) { echo '  ... and ' . (count($counts) - 15) . ' more' . PHP_EOL; break; }
        echo '  ' . $line . ($n > 1 ? '  x' . $n : '') . PHP_EOL;
    }
}

echo 'NOTE: no value was corrected and no Person, Place or Organization was created.' . PHP_EOL;
