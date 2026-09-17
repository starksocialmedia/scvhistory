/**
 * Fills warMemorials entries from storage/war_memorials_parsed.json.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/populate_war_memorials.php'))"
 */

$APPLY = true;

$path = \Craft::getAlias('@storage') . '/war_memorials_parsed.json';
if (!file_exists($path)) {
    echo 'ERROR: ' . $path . ' not found. Run parse_war_memorials.php first.' . PHP_EOL;
    return;
}
$rows = json_decode(file_get_contents($path), true);

$branchMap = [
    'army of the united states' => 'U.S. Army',
    'amry of the united states' => 'U.S. Army',
    'united states navy' => 'U.S. Navy',
    'united states marine corps' => 'U.S. Marine Corps',
    'army national guard' => 'Army National Guard',
    'american field service' => 'American Field Service',
];

$map = [
    'wmRank'             => 'rank',
    'wmUnit'             => 'unit',
    'wmHomeOfRecord'     => 'homeOfRecord',
    'wmDateOfBirth'      => 'dateOfBirth',
    'wmHighSchool'       => 'highSchool',
    'wmServiceId'        => 'serviceId',
    'wmSpecialty'        => 'specialty',
    'wmLengthOfService'  => 'lengthOfService',
    'wmStartTour'        => 'startTour',
    'wmBase'             => 'base',
    'wmCombatOperations' => 'combatOperations',
    'wmIncidentDate'     => 'incidentDate',
    'wmIncidentLocation' => 'incidentLocation',
    'wmAgeAtLoss'        => 'ageAtLoss',
    'burialPlace'        => 'remains',
    'deathDate'          => 'casualtyDate',
];

$awardTerms = [
    'Medal of Honor', 'Distinguished Service Cross', 'Navy Cross', 'Silver Star',
    'Legion of Merit', 'Distinguished Flying Cross', 'Bronze Star', 'Purple Heart',
    'Air Medal', 'Combat Medical Badge', 'Combat Infantryman Badge',
    'Commendation Medal', 'Good Conduct Medal', 'Prisoner of War Medal',
];

$changed = 0;
$skipped = 0;
$report = [];

foreach ($rows as $row) {
    $entry = \craft\elements\Entry::find()->id($row['id'])->status(null)->one();
    if (!$entry) {
        echo 'MISSING entry ' . $row['id'] . ' (' . $row['title'] . ')' . PHP_EOL;
        $skipped++;
        continue;
    }

    $p = $row['parsed'];
    $sets = [];

    foreach ($map as $handle => $key) {
        if (!isset($p[$key]) || $p[$key] === '') {
            continue;
        }
        $current = (string)$entry->getFieldValue($handle);
        if ($current === '') {
            $sets[$handle] = $p[$key];
        }
    }

    if (isset($p['branch'])) {
        $norm = $branchMap[strtolower(trim($p['branch']))] ?? $p['branch'];
        if ((string)$entry->getFieldValue('wmBranch') === '') {
            $sets['wmBranch'] = $norm;
        }
    }

    $narrative = $p['narrative'] ?? '';
    if ($narrative !== '' && (string)$entry->getFieldValue('wmNarrative') === '') {
        $sets['wmNarrative'] = $narrative;
    }

    if ($narrative !== '' && (string)$entry->getFieldValue('wmAwards') === '') {
        $found = [];
        foreach ($awardTerms as $term) {
            if (stripos($narrative, $term) !== false) {
                $found[] = $term;
            }
        }
        if (count($found)) {
            $sets['wmAwards'] = implode('; ', array_unique($found));
        }
    }

    $body = (string)$entry->getFieldValue('body');
    if ($narrative !== '' && (str_contains($body, 'WAR MEMORIAL HOME') || $body === '')) {
        $sets['body'] = $narrative;
    }

    if (!count($sets)) {
        $skipped++;
        continue;
    }

    $report[] = $row['title'] . ' -> ' . implode(', ', array_keys($sets));

    if ($APPLY) {
        foreach ($sets as $handle => $value) {
            $entry->setFieldValue($handle, $value);
        }
        if (!\Craft::$app->getElements()->saveElement($entry)) {
            echo 'SAVE FAILED ' . $row['title'] . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
            continue;
        }
    }
    $changed++;
}

echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
echo 'Entries to change: ' . $changed . PHP_EOL;
echo 'Entries unchanged: ' . $skipped . PHP_EOL;
echo '--- detail ---' . PHP_EOL;
foreach ($report as $line) {
    echo $line . PHP_EOL;
}
