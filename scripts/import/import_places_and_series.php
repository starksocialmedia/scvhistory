<?php

use craft\elements\Category;
use craft\elements\Entry;
use craft\models\Section_SiteSettings;

$root = Craft::getAlias('@root');
$entries = Craft::$app->getEntries();
$elements = Craft::$app->getElements();
$site = Craft::$app->getSites()->getPrimarySite();

function setSectionTemplate($entries, $handle, $template, $siteId): void
{
    $section = $entries->getSectionByHandle($handle);
    if ($section === null) {
        echo "STOP: section {$handle} missing\n";
        return;
    }
    $settings = $section->getSiteSettings();
    $row = $settings[$siteId] ?? reset($settings);
    if (!$row instanceof Section_SiteSettings) {
        echo "STOP: no site settings for {$handle}\n";
        return;
    }
    $row->template = $template;
    $settings[$siteId] = $row;
    $section->setSiteSettings($settings);
    if (!$entries->saveSection($section)) {
        echo "STOP: could not save {$handle}: " . json_encode($section->getErrors()) . "\n";
        return;
    }
    echo "template {$handle} -> {$template}\n";
}

setSectionTemplate($entries, 'places', 'places/_entry', $site->id);
setSectionTemplate($entries, 'collections', 'collections/_entry', $site->id);

$placeSection = $entries->getSectionByHandle('places');
$placeType = $entries->getEntryTypeByHandle('place');
$json = json_decode(file_get_contents($root . '/places-candidates.json'), true);
$created = 0;
$skipped = 0;
$failed = 0;
foreach ($json['candidates'] as $row) {
    $slug = $row['slug'];
    if (Entry::find()->section('places')->slug($slug)->status(null)->one()) {
        $skipped++;
        continue;
    }
    $hoodIds = [];
    $hood = Category::find()->group('neighborhood')->slug($slug)->one();
    if ($hood) {
        $hoodIds[] = $hood->id;
    }
    $entry = new Entry();
    $entry->sectionId = $placeSection->id;
    $entry->typeId = $placeType->id;
    $entry->title = $row['title'];
    $entry->slug = $slug;
    $entry->setFieldValues([
        'body' => $row['title'] . ' is a named place in the Santa Clarita Valley historical archive.',
        'legacyUrl' => $row['legacyUrl'] ?? '',
        'sourcePath' => $row['sourcePath'] ?? '',
        'legacyCategory' => $row['legacyCategory'] ?? '',
        'neighborhood' => $hoodIds,
    ]);
    if ($elements->saveElement($entry)) {
        $created++;
        echo "PLACE {$slug}\n";
    } else {
        $failed++;
        echo "FAILED place {$slug} " . json_encode($entry->getErrors()) . "\n";
    }
}
echo "places created={$created} skipped={$skipped} failed={$failed}\n";

$series = [
    ['title' => 'Perkins', 'slug' => 'perkins', 'legacyUrl' => '/scvhistory/signal/perkins/index.html', 'sourcePath' => 'scvhistory.com/scvhistory/signal/perkins/index.html'],
    ['title' => 'Reynolds', 'slug' => 'reynolds', 'legacyUrl' => '/scvhistory/signal/reynolds/index.html', 'sourcePath' => 'scvhistory.com/scvhistory/signal/reynolds/index.html'],
    ['title' => 'Worden', 'slug' => 'worden', 'legacyUrl' => '/scvhistory/signal/worden/index.htm', 'sourcePath' => 'scvhistory.com/scvhistory/signal/worden/index.htm'],
    ['title' => 'Boston', 'slug' => 'boston', 'legacyUrl' => '/scvhistory/signal/boston/jbindex.htm', 'sourcePath' => 'scvhistory.com/scvhistory/signal/boston/jbindex.htm'],
    ['title' => 'Manzer', 'slug' => 'manzer', 'legacyUrl' => '/scvhistory/signal/manzer/index.htm', 'sourcePath' => 'scvhistory.com/scvhistory/signal/manzer/index.htm'],
    ['title' => 'Newsmaker', 'slug' => 'newsmaker', 'legacyUrl' => '/scvhistory/signal/newsmaker/index.htm', 'sourcePath' => 'scvhistory.com/scvhistory/signal/newsmaker/index.htm'],
    ['title' => 'Coins', 'slug' => 'coins', 'legacyUrl' => '', 'sourcePath' => 'scvhistory.com/scvhistory/signal/coins/'],
    ['title' => 'Iraq', 'slug' => 'iraq', 'legacyUrl' => '/scvhistory/signal/iraq/index.htm', 'sourcePath' => 'scvhistory.com/scvhistory/signal/iraq/index.htm'],
    ['title' => 'Old Town Newhall Gazette', 'slug' => 'otn-gazette', 'legacyUrl' => '', 'sourcePath' => 'scvhistory.com/oldtownnewhall/gazette/'],
    ['title' => 'Patti', 'slug' => 'otn-patti', 'legacyUrl' => '/oldtownnewhall/patti/index.html', 'sourcePath' => 'scvhistory.com/oldtownnewhall/patti/index.html'],
    ['title' => 'Pauline', 'slug' => 'otn-pauline', 'legacyUrl' => '/oldtownnewhall/pauline/index.htm', 'sourcePath' => 'scvhistory.com/oldtownnewhall/pauline/index.htm'],
    ['title' => 'Rioux', 'slug' => 'otn-rioux', 'legacyUrl' => '/oldtownnewhall/rioux/index.htm', 'sourcePath' => 'scvhistory.com/oldtownnewhall/rioux/index.htm'],
    ['title' => 'Whyte', 'slug' => 'otn-whyte', 'legacyUrl' => '/oldtownnewhall/whyte/index.html', 'sourcePath' => 'scvhistory.com/oldtownnewhall/whyte/index.html'],
];

$colSection = $entries->getSectionByHandle('collections');
$colType = $entries->getEntryTypeByHandle('collection');
$cCreated = 0;
$cSkipped = 0;
$cFailed = 0;
foreach ($series as $row) {
    if (Entry::find()->section('collections')->slug($row['slug'])->status(null)->one()) {
        $cSkipped++;
        continue;
    }
    $entry = new Entry();
    $entry->sectionId = $colSection->id;
    $entry->typeId = $colType->id;
    $entry->title = $row['title'];
    $entry->slug = $row['slug'];
    $entry->setFieldValues([
        'body' => $row['title'] . ' is a series in the Santa Clarita Valley historical archive. Articles attach later.',
        'legacyUrl' => $row['legacyUrl'],
        'sourcePath' => $row['sourcePath'],
    ]);
    if ($elements->saveElement($entry)) {
        $cCreated++;
        echo "SERIES {$row['slug']}\n";
    } else {
        $cFailed++;
        echo "FAILED series {$row['slug']} " . json_encode($entry->getErrors()) . "\n";
    }
}
echo "series created={$cCreated} skipped={$cSkipped} failed={$cFailed}\n";
