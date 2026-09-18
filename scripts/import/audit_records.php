/**
 * Measures every record against RECORD-CHECKLIST.md and reports what is missing.
 * Read only. Writes web/review/audit.json for the review screen.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_records.php'))"
 */

$common = [
    'required' => ['body', 'sourcePath', 'legacyKey', 'legacyUrl'],
    'expected' => ['neighborhood', 'historicalEra'],
];

$bySection = [
    'articles' => [
        'required' => ['writtenBy', 'originalPublishDate'],
        'expected' => ['partOfCollection', 'subjectPerson', 'depictsPlace', 'publishedBy'],
    ],
    'persons' => [
        'required' => [],
        'expected' => ['occupation', 'birthDate', 'deathDate'],
    ],
    'places' => [
        'required' => ['placeLat', 'placeLng'],
        'expected' => ['dateEstablished'],
    ],
    'organizations' => [
        'required' => [],
        'expected' => ['orgLat', 'orgLng', 'dateFounded'],
    ],
    'warMemorials' => [
        'required' => ['wmBranch', 'wmConflict', 'deathDate'],
        'expected' => ['wmHomeOfRecord', 'wmRank', 'wmUnit', 'wmNarrative', 'wmIncidentDate', 'wmIncidentLocation'],
    ],
    'collections' => [
        'required' => ['writtenBy', 'articlesInCollection'],
        'expected' => ['collectionParts', 'bandImage'],
    ],
];

$out = [];
$tally = [];

foreach (array_keys($bySection) as $handle) {
    $req = array_merge($common['required'], $bySection[$handle]['required']);
    $exp = array_merge($common['expected'], $bySection[$handle]['expected']);
    $entries = \craft\elements\Entry::find()->section($handle)->status(null)->orderBy('title asc')->all();
    if (!count($entries)) { continue; }

    $missRequired = []; $missExpected = [];

    foreach ($entries as $e) {
        $layout = [];
        foreach ($e->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }

        $empty = function (string $h) use ($e, $layout): bool {
            if (!in_array($h, $layout, true)) { return false; }
            try { $v = $e->getFieldValue($h); } catch (\Throwable $x) { return false; }
            if (is_object($v) && method_exists($v, 'count')) { return $v->count() === 0; }
            if (is_array($v)) { return count($v) === 0; }
            return trim((string)$v) === '';
        };

        $r = array_values(array_filter($req, $empty));
        $x = array_values(array_filter($exp, $empty));
        foreach ($r as $h) { $missRequired[$h] = ($missRequired[$h] ?? 0) + 1; }
        foreach ($x as $h) { $missExpected[$h] = ($missExpected[$h] ?? 0) + 1; }

        if (count($r) || count($x)) {
            $out[] = [
                'section' => $handle,
                'id' => $e->id,
                'title' => $e->title,
                'url' => $e->getCpEditUrl(),
                'missing_required' => $r,
                'missing_expected' => $x,
            ];
        }
    }

    arsort($missRequired); arsort($missExpected);
    $tally[$handle] = ['total' => count($entries), 'required' => $missRequired, 'expected' => $missExpected];
}

foreach ($tally as $handle => $t) {
    echo PHP_EOL . strtoupper($handle) . '  (' . $t['total'] . ' records)' . PHP_EOL;
    if (count($t['required'])) {
        echo '  missing REQUIRED:' . PHP_EOL;
        foreach ($t['required'] as $h => $n) { echo '    ' . str_pad((string)$n, 5, ' ', STR_PAD_LEFT) . '  ' . $h . PHP_EOL; }
    } else { echo '  no required fields missing' . PHP_EOL; }
    if (count($t['expected'])) {
        echo '  missing expected:' . PHP_EOL;
        foreach ($t['expected'] as $h => $n) { echo '    ' . str_pad((string)$n, 5, ' ', STR_PAD_LEFT) . '  ' . $h . PHP_EOL; }
    }
}

$path = \Craft::getAlias('@webroot') . '/review';
if (!is_dir($path)) { mkdir($path, 0775, true); }
file_put_contents($path . '/audit.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo PHP_EOL . 'records with at least one gap: ' . count($out) . PHP_EOL;
echo 'written to web/review/audit.json' . PHP_EOL;
