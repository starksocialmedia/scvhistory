/**
 * Read-only. Parses stored legacyHtml on warMemorials entries into structured JSON.
 * Writes: storage/war_memorials_parsed.json
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/parse_war_memorials.php'))"
 */

$labels = [
    'homeOfRecord'     => 'Home of Record',
    'dateOfBirth'      => 'Date of Birth',
    'highSchool'       => 'High School',
    'branch'           => 'Service',
    'rank'             => 'Rank',
    'serviceId'        => 'ID No',
    'specialty'        => 'Specialty',
    'lengthOfService'  => 'Length of Service',
    'unit'             => 'Unit',
    'startTour'        => 'Start Tour',
    'base'             => 'Based',
    'combatOperations' => 'Supporting',
    'incidentDate'     => 'Incident Date',
    'casualtyDate'     => 'Casualty Date',
    'ageAtLoss'        => 'Age at Loss',
    'incidentLocation' => 'Location',
    'remains'          => 'Remains',
    'awards'           => 'Awards',
    'narrative'        => 'Narrative',
];

$toText = function (string $html): string {
    $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', ' ', $html);
    $html = preg_replace('#<br\s*/?>#i', "\n", $html);
    $html = preg_replace('#</(p|div|tr|table|li|h[1-6])>#i', "\n", $html);
    $html = strip_tags($html);
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = str_replace("\xc2\xa0", ' ', $html);
    $html = preg_replace('/[ \t]+/', ' ', $html);
    $html = preg_replace('/\n{2,}/', "\n", $html);
    return trim($html);
};

$records = [];
$entries = \craft\elements\Entry::find()->section('warMemorials')->status(null)->limit(null)->all();

foreach ($entries as $e) {
    $html = (string)$e->getFieldValue('legacyHtml');
    $row = [
        'id'         => $e->id,
        'title'      => $e->title,
        'slug'       => $e->slug,
        'legacyUrl'  => (string)$e->getFieldValue('legacyUrl'),
        'legacyKey'  => (string)$e->getFieldValue('legacyKey'),
        'sourcePath' => (string)$e->getFieldValue('sourcePath'),
        'conflict'   => (string)$e->getFieldValue('wmConflict'),
        'images'     => [],
        'parsed'     => [],
        'missing'    => [],
    ];

    if ($html === '') {
        $row['missing'][] = 'legacyHtml empty';
        $records[] = $row;
        continue;
    }

    if (preg_match_all('#<img[^>]+src="([^"]+)"#i', $html, $m)) {
        foreach ($m[1] as $src) {
            if (stripos($src, 'logo') !== false || stripos($src, 'bgparch') !== false) {
                continue;
            }
            $row['images'][] = $src;
        }
        $row['images'] = array_values(array_unique($row['images']));
    }

    $text = $toText($html);
    $lines = explode("\n", $text);

    foreach ($labels as $key => $label) {
        $value = null;
        foreach ($lines as $line) {
            $line = trim($line);
            if (stripos($line, $label . ':') === 0) {
                $value = trim(substr($line, strlen($label) + 1));
                break;
            }
        }
        if ($value !== null && $value !== '') {
            $row['parsed'][$key] = $value;
        } else {
            $row['missing'][] = $key;
        }
    }

    if (!isset($row['parsed']['narrative'])) {
        if (preg_match('/Narrative:\s*(.+?)(?:\n\s*\n|$)/s', $text, $m)) {
            $n = trim(preg_replace('/\s+/', ' ', $m[1]));
            if ($n !== '') {
                $row['parsed']['narrative'] = $n;
                $row['missing'] = array_values(array_diff($row['missing'], ['narrative']));
            }
        }
    }

    $records[] = $row;
}

$path = \Craft::getAlias('@storage') . '/war_memorials_parsed.json';
file_put_contents($path, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$fieldCounts = [];
foreach ($records as $r) {
    foreach (array_keys($labels) as $k) {
        $fieldCounts[$k] = ($fieldCounts[$k] ?? 0) + (isset($r['parsed'][$k]) ? 1 : 0);
    }
}

echo 'Entries parsed: ' . count($records) . PHP_EOL;
echo 'Written to: ' . $path . PHP_EOL;
echo '--- field coverage (of ' . count($records) . ') ---' . PHP_EOL;
foreach ($fieldCounts as $k => $c) {
    echo str_pad($k, 18) . $c . PHP_EOL;
}
echo '--- conflicts ---' . PHP_EOL;
$byConflict = [];
foreach ($records as $r) {
    $c = $r['conflict'] !== '' ? $r['conflict'] : '(none)';
    $byConflict[$c] = ($byConflict[$c] ?? 0) + 1;
}
foreach ($byConflict as $c => $n) {
    echo $c . ': ' . $n . PHP_EOL;
}
