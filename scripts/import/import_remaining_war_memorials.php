<?php

use craft\elements\Entry;

$jsonPath = Craft::getAlias('@root') . '/inventory/extracts/war-memorial-remaining-11.json';
if (!is_file($jsonPath)) {
    echo "STOP: missing {$jsonPath}\n";
    return;
}
$data = json_decode(file_get_contents($jsonPath), true);
if (!is_array($data) || empty($data['persons'])) {
    echo "STOP: JSON has no records\n";
    return;
}

$entries = Craft::$app->getEntries();
$section = $entries->getSectionByHandle('warMemorials');
$type = $entries->getEntryTypeByHandle('warMemorial');
if ($section === null || $type === null) {
    echo "STOP: warMemorials missing\n";
    return;
}

$created = 0;
$skipped = 0;
$failed = 0;

foreach ($data['persons'] as $row) {
    $slug = $row['slug'] ?? '';
    if ($slug === '') {
        $failed++;
        continue;
    }
    $existing = Entry::find()->section('warMemorials')->slug($slug)->status(null)->one();
    if ($existing) {
        $skipped++;
        echo "SKIP {$slug}\n";
        continue;
    }
    $entry = new Entry();
    $entry->sectionId = $section->id;
    $entry->typeId = $type->id;
    $entry->title = $row['title'] ?? $slug;
    $entry->slug = $slug;
    $entry->setFieldValues([
        'body' => $row['body'] ?? '',
        'wmBranch' => $row['wmBranch'] ?? '',
        'wmRank' => $row['wmRank'] ?? '',
        'wmUnit' => $row['wmUnit'] ?? '',
        'wmConflict' => $row['wmConflict'] ?? 'World War II',
        'deathDate' => $row['deathDate'] ?? '',
        'wmHomeOfRecord' => $row['wmHomeOfRecord'] ?? '',
        'legacyKey' => $row['legacyKey'] ?? '',
        'legacyUrl' => $row['legacyUrl'] ?? '',
        'sourcePath' => $row['sourcePath'] ?? '',
        'legacyHtml' => $row['legacyHtml'] ?? '',
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
