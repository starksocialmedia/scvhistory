/**
 * Measures every record against RECORD-CHECKLIST.md and reports what is missing.
 * Read only. Writes web/review/audit.json for the review screen.
 *
 * Provenance is required on a migrated record and does not apply to one written
 * here. A record with sourcePath, legacyUrl and legacyKey all empty never had a
 * legacy page, so those three are not counted against it and it is reported
 * under born digital instead. One of the three filled and the others empty is a
 * migrated record with a gap, and is reported as a gap.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_records.php'))"
 */

$PROVENANCE = ['sourcePath', 'legacyKey', 'legacyUrl'];

$common = [
    'required' => ['body', 'sourcePath', 'legacyKey', 'legacyUrl'],
    'expected' => ['neighborhood', 'historicalEra'],
];

/* A page is a page of the site, not a record of the valley, so the taxonomy
   fields that place a record in the archive do not apply to it. */
$noCommonExpected = ['pages'];

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
    /* Both are almost entirely born digital, which is why they were not audited
       before: against the old rule they would have been nothing but false
       provenance gaps. They are worth measuring now that provenance is scoped
       to migrated records. */
    'groups' => [
        'required' => [],
        'expected' => ['groupDateStart', 'groupPersons'],
    ],
    'pages' => [
        'required' => [],
        'expected' => [],
    ],
];

$out = [];
$tally = [];

foreach (array_keys($bySection) as $handle) {
    $req = array_merge($common['required'], $bySection[$handle]['required']);
    $exp = in_array($handle, $noCommonExpected, true)
        ? $bySection[$handle]['expected']
        : array_merge($common['expected'], $bySection[$handle]['expected']);
    $entries = \craft\elements\Entry::find()->section($handle)->status(null)->orderBy('title asc')->all();
    if (!count($entries)) { continue; }

    $missRequired = []; $missExpected = []; $bornDigital = [];

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

        /* Born digital: none of the three provenance fields holds anything, so
           there was never a legacy page for them to describe. */
        $provEmpty = array_values(array_filter($PROVENANCE, $empty));
        $provPresent = array_values(array_filter($PROVENANCE, fn($h) => in_array($h, $layout, true) && !$empty($h)));
        $isBorn = count($provPresent) === 0 && count($provEmpty) > 0;

        $reqHere = $isBorn ? array_values(array_diff($req, $PROVENANCE)) : $req;

        $r = array_values(array_filter($reqHere, $empty));
        $x = array_values(array_filter($exp, $empty));

        if ($isBorn) {
            $bornDigital[] = ['id' => $e->id, 'title' => (string)$e->title];
        }
        foreach ($r as $h) { $missRequired[$h] = ($missRequired[$h] ?? 0) + 1; }
        foreach ($x as $h) { $missExpected[$h] = ($missExpected[$h] ?? 0) + 1; }

        if (count($r) || count($x)) {
            $out[] = [
                'section' => $handle,
                'id' => $e->id,
                'title' => $e->title,
                'url' => $e->getCpEditUrl(),
                'born_digital' => $isBorn,
                'missing_required' => $r,
                'missing_expected' => $x,
            ];
        }
    }

    arsort($missRequired); arsort($missExpected);
    $tally[$handle] = [
        'total' => count($entries),
        'required' => $missRequired,
        'expected' => $missExpected,
        'born' => $bornDigital,
    ];
}

$bornTotal = 0;
foreach ($tally as $t) { $bornTotal += count($t['born']); }

foreach ($tally as $handle => $t) {
    echo PHP_EOL . strtoupper($handle) . '  (' . $t['total'] . ' records'
        . (count($t['born']) ? ', ' . count($t['born']) . ' born digital' : '') . ')' . PHP_EOL;
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
echo PHP_EOL . str_repeat('=', 70) . PHP_EOL;
echo 'BORN DIGITAL, ' . $bornTotal . '. Written here rather than migrated, so' . PHP_EOL;
echo 'sourcePath, legacyUrl and legacyKey do not apply and are not counted' . PHP_EOL;
echo 'against them. Nothing should ever be written into those three to clear' . PHP_EOL;
echo 'an audit line.' . PHP_EOL;
foreach ($tally as $handle => $t) {
    if (!count($t['born'])) { continue; }
    echo PHP_EOL . '  ' . $handle . ', ' . count($t['born']) . ':' . PHP_EOL;
    foreach ($t['born'] as $b) { echo '    ' . $b['title'] . PHP_EOL; }
}

echo PHP_EOL . 'records with at least one gap: ' . count($out) . PHP_EOL;
echo 'born digital, provenance not counted: ' . $bornTotal . PHP_EOL;
echo 'written to web/review/audit.json' . PHP_EOL;
