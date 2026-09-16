<?php

use craft\elements\Entry;

$elements = Craft::$app->getElements();

function entry(string $section, string $slug): ?Entry
{
    return Entry::find()->section($section)->slug($slug)->status(null)->one();
}

function mergeIds(Entry $entry, string $field, array $addSlugs, string $addSection): array
{
    $ids = [];
    foreach ($entry->{$field}->all() as $rel) {
        $ids[$rel->id] = true;
    }
    foreach ($addSlugs as $slug) {
        $other = entry($addSection, $slug);
        if ($other) {
            $ids[$other->id] = true;
        } else {
            echo "MISSING {$addSection}:{$slug} for {$entry->slug}.{$field}\n";
        }
    }
    return array_map('intval', array_keys($ids));
}

function saveField(Entry $entry, string $field, array $ids): void
{
    $entry->setFieldValue($field, $ids);
    if (!Craft::$app->getElements()->saveElement($entry)) {
        echo "FAILED {$entry->slug} {$field} " . json_encode($entry->getErrors()) . "\n";
        return;
    }
    echo "OK {$entry->section->handle}:{$entry->slug}.{$field} n=" . count($ids) . "\n";
}

$placePeople = [
    'newhall' => ['henry-mayo-newhall', 'arthur-b-perkins', 'jerry-reynolds', 'leon-worden', 'john-gifford'],
    'saugus' => ['henry-mayo-newhall'],
    'placerita-canyon' => ['francisco-lopez', 'abel-stearns'],
    'rancho-camulos' => ['ygnacio-del-valle', 'juventino-del-valle'],
    'rancho-san-francisco' => ['antonio-del-valle', 'ygnacio-del-valle', 'juventino-del-valle', 'henry-mayo-newhall'],
    'pico-canyon' => ['henry-clay-wiley', 'jerry-reynolds'],
    'mentryville' => ['leon-worden'],
    'beales-cut' => ['edward-fitzgerald-beale'],
    'vasquez-rocks' => ['tiburcio-vasquez'],
    'fort-tejon' => ['edward-fitzgerald-beale'],
];

$placeOrgs = [
    'rancho-camulos' => ['rancho-camulos'],
    'rancho-san-francisco' => ['rancho-san-francisco'],
    'tejon-ranch' => ['rancho-el-tejon'],
    'santa-clarita' => ['city-of-santa-clarita'],
];

$relatedPlaces = [
    'mentryville' => ['pico-canyon'],
    'pico-canyon' => ['mentryville'],
];

$personOrgs = [
    'ygnacio-del-valle' => ['rancho-camulos', 'rancho-san-francisco'],
    'antonio-del-valle' => ['rancho-camulos', 'rancho-san-francisco'],
    'juventino-del-valle' => ['rancho-camulos', 'rancho-san-francisco'],
    'henry-mayo-newhall' => ['rancho-san-francisco'],
    'leon-worden' => ['scvhistory-com-santa-clarita-valley-history'],
];

foreach ($placePeople as $slug => $people) {
    $place = entry('places', $slug);
    if (!$place) {
        echo "MISSING place:{$slug}\n";
        continue;
    }
    saveField($place, 'placePeople', mergeIds($place, 'placePeople', $people, 'persons'));
}

foreach ($placeOrgs as $slug => $orgs) {
    $place = entry('places', $slug);
    if (!$place) {
        echo "MISSING place:{$slug}\n";
        continue;
    }
    saveField($place, 'placeOrganizations', mergeIds($place, 'placeOrganizations', $orgs, 'organizations'));
}

foreach ($relatedPlaces as $slug => $others) {
    $place = entry('places', $slug);
    if (!$place) {
        echo "MISSING place:{$slug}\n";
        continue;
    }
    saveField($place, 'relatedPlaces', mergeIds($place, 'relatedPlaces', $others, 'places'));
}

foreach ($personOrgs as $slug => $orgs) {
    $person = entry('persons', $slug);
    if (!$person) {
        echo "MISSING person:{$slug}\n";
        continue;
    }
    saveField($person, 'personOrganizations', mergeIds($person, 'personOrganizations', $orgs, 'organizations'));
    foreach ($orgs as $orgSlug) {
        $org = entry('organizations', $orgSlug);
        if (!$org) {
            continue;
        }
        saveField($org, 'orgAssociatedPersons', mergeIds($org, 'orgAssociatedPersons', [$slug], 'persons'));
    }
}
