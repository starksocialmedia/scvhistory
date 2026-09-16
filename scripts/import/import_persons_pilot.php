<?php

use craft\elements\Entry;

$base = Craft::getAlias('@root');
$jsonPath = $base . '/inventory/extracts/persons-pilot-25.json';
if (!is_file($jsonPath)) {
    echo "STOP: missing {$jsonPath}\n";
    return;
}

$data = json_decode(file_get_contents($jsonPath), true);
if (!is_array($data) || empty($data['persons'])) {
    echo "STOP: JSON has no persons\n";
    return;
}

$entries = Craft::$app->getEntries();
$section = $entries->getSectionByHandle('persons');
$type = $entries->getEntryTypeByHandle('person');
if ($section === null || $type === null) {
    echo "STOP: persons section or person type missing\n";
    return;
}

$cap = 25;
$created = 0;
$skipped = 0;
$failed = 0;

foreach ($data['persons'] as $row) {
    if ($created >= $cap) {
        break;
    }
    $slug = $row['slug'] ?? '';
    if ($slug === '') {
        $failed++;
        echo "FAILED: empty slug\n";
        continue;
    }
    $existing = Entry::find()->section('persons')->slug($slug)->status(null)->one();
    if ($existing) {
        $skipped++;
        echo "SKIP existing slug: {$slug}\n";
        continue;
    }
    $entry = new Entry();
    $entry->sectionId = $section->id;
    $entry->typeId = $type->id;
    $entry->title = $row['title'] ?? $row['fullName'];
    $entry->slug = $slug;
    $entry->setFieldValues([
        'fullName' => $row['fullName'] ?? '',
        'birthDate' => $row['birthDate'] ?? '',
        'deathDate' => $row['deathDate'] ?? '',
        'birthplace' => $row['birthplace'] ?? '',
        'burialPlace' => $row['burialPlace'] ?? '',
        'occupation' => $row['occupation'] ?? '',
        'body' => $row['body'] ?? '',
        'personLegacyUrl' => $row['personLegacyUrl'] ?? '',
        'legacyUrl' => $row['legacyUrl'] ?? '',
        'legacyKey' => $row['legacyKey'] ?? '',
        'sourcePath' => $row['sourcePath'] ?? '',
        'legacyHtml' => $row['legacyHtml'] ?? '',
        'legacyCategory' => $row['legacyCategory'] ?? '',
    ]);
    if (Craft::$app->getElements()->saveElement($entry)) {
        $created++;
        echo "CREATED: {$slug}\n";
    } else {
        $failed++;
        echo "FAILED: {$slug} " . json_encode($entry->getErrors()) . "\n";
    }
}

echo "created={$created} skipped={$skipped} failed={$failed}\n";
