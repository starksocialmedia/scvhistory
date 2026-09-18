/**
 * Audits Find A Grave and Wikipedia links on Person records: reports which
 * have one, which do not, and whether each stored URL is shaped correctly.
 * Read only, writes nothing.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_grave_links.php'))"
 */

$has = []; $missing = []; $malformed = [];

foreach (\craft\elements\Entry::find()->section('persons')->status(null)->orderBy('title asc')->all() as $p) {
    $layout = [];
    foreach ($p->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }

    $read = function (string $h) use ($p, $layout): string {
        if (!in_array($h, $layout, true)) { return ''; }
        try { return trim((string)$p->getFieldValue($h)); } catch (\Throwable $e) { return ''; }
    };

    $grave = $read('personGraveUrl');
    $wiki = $read('personWikipediaUrl');
    $death = $read('deathDate');
    $burial = $read('burialPlace');

    if ($grave === '') {
        $missing[] = [$p->title, $death, $burial, $wiki !== '' ? 'wikipedia' : ''];
        continue;
    }
    if (!str_contains(strtolower($grave), 'findagrave.com')) {
        $malformed[] = [$p->title, $grave];
        continue;
    }
    if (preg_match('#findagrave\.com/memorial/\d+#i', $grave) === 0) {
        $malformed[] = [$p->title, $grave];
        continue;
    }
    $has[] = [$p->title, $grave];
}

echo '=== has a Find A Grave link (' . count($has) . ') ===' . PHP_EOL;
foreach ($has as $r) { echo '  ' . str_pad($r[0], 30) . $r[1] . PHP_EOL; }

echo PHP_EOL . '=== link present but wrong shape (' . count($malformed) . ') ===' . PHP_EOL;
foreach ($malformed as $r) { echo '  ' . str_pad($r[0], 30) . $r[1] . PHP_EOL; }

echo PHP_EOL . '=== no Find A Grave link (' . count($missing) . ') ===' . PHP_EOL;
foreach ($missing as $r) {
    echo '  ' . str_pad($r[0], 30)
        . ($r[1] !== '' ? 'd. ' . $r[1] : 'no death date')
        . ($r[2] !== '' ? ' | ' . $r[2] : '')
        . ($r[3] !== '' ? ' | has ' . $r[3] : '') . PHP_EOL;
}

echo PHP_EOL . 'Persons total: ' . \craft\elements\Entry::find()->section('persons')->status(null)->count() . PHP_EOL;
