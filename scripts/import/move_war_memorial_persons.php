<?php

use craft\elements\Entry;

$entries = Craft::$app->getEntries();
$elements = Craft::$app->getElements();
$section = $entries->getSectionByHandle('warMemorials');
$type = $entries->getEntryTypeByHandle('warMemorial');
if ($section === null || $type === null) {
    echo "STOP: warMemorials section or warMemorial type missing\n";
    return;
}

$people = Entry::find()->section('persons')->status(null)->all();
$moved = 0;
$skipped = 0;
$failed = 0;

function conflictFromPath(string $path): string
{
    $p = strtolower($path);
    if (str_contains($p, 'korea')) {
        return 'Korean War';
    }
    if (str_contains($p, 'terror')) {
        return 'War on Terror';
    }
    if (str_contains($p, 'ww2') || str_contains($p, 'wwii')) {
        return 'World War II';
    }
    return '';
}

function wmSlugFromLegacy(string $legacyUrl, string $sourcePath, string $fallback): string
{
    $path = $legacyUrl !== '' ? $legacyUrl : $sourcePath;
    $base = basename($path);
    $stem = pathinfo($base, PATHINFO_FILENAME);
    $stem = str_replace('_', '-', $stem);
    $stem = strtolower($stem);
    return $stem !== '' ? $stem : $fallback;
}

foreach ($people as $person) {
    $legacyUrl = (string) $person->legacyUrl;
    $sourcePath = (string) $person->sourcePath;
    $personLegacy = (string) $person->personLegacyUrl;
    $blob = $legacyUrl . ' ' . $sourcePath . ' ' . $personLegacy;
    if (stripos($blob, 'warmemorial') === false) {
        continue;
    }

    $wmSlug = wmSlugFromLegacy($legacyUrl, $sourcePath, $person->slug);
    $existingWm = Entry::find()->section('warMemorials')->slug($wmSlug)->status(null)->one();
    if ($existingWm) {
        $skipped++;
        echo "SKIP existing warMemorial slug: {$wmSlug}\n";
        continue;
    }

    $imageIds = [];
    foreach ($person->featuredImage->all() as $asset) {
        $imageIds[] = $asset->id;
    }

    $wm = new Entry();
    $wm->sectionId = $section->id;
    $wm->typeId = $type->id;
    $wm->title = $person->title;
    $wm->slug = $wmSlug;
    $wm->setFieldValues([
        'body' => (string) $person->body,
        'deathDate' => (string) $person->deathDate,
        'burialPlace' => (string) $person->burialPlace,
        'featuredImage' => $imageIds,
        'legacyKey' => (string) $person->legacyKey,
        'legacyUrl' => $legacyUrl,
        'sourcePath' => $sourcePath,
        'legacyHtml' => (string) $person->legacyHtml,
        'wmConflict' => conflictFromPath($blob),
        'wmHomeOfRecord' => '',
        'wmBranch' => '',
        'wmRank' => '',
        'wmUnit' => '',
    ]);

    if (!$elements->saveElement($wm)) {
        $failed++;
        echo "FAILED create {$wmSlug}: " . json_encode($wm->getErrors()) . "\n";
        continue;
    }

    if (!$elements->deleteElement($person, true)) {
        $failed++;
        echo "FAILED delete person {$person->slug} after creating {$wmSlug}\n";
        continue;
    }

    $moved++;
    echo "MOVED: {$person->slug} -> {$wmSlug}\n";
}

echo "moved={$moved} skipped={$skipped} failed={$failed}\n";
