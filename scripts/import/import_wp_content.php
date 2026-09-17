/**
 * Imports the WordPress export (inventory/wp_content.json) into Craft.
 * Pass 1 creates or updates entries and sets plain fields and categories.
 * Pass 2 wires relations, so entities exist before anything points at them.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_wp_content.php'))"
 */

$APPLY = true;

$path = \Craft::getAlias('@root') . '/inventory/wp_content.json';
if (!file_exists($path)) {
    echo 'ERROR: ' . $path . ' not found' . PHP_EOL;
    return;
}
$data = json_decode(file_get_contents($path), true);
$posts = $data['posts'];

$elements = Craft::$app->getElements();
$entriesSvc = Craft::$app->getEntries();

$sectionFor = [
    'article' => ['articles', 'article'],
    'person' => ['persons', 'person'],
    'place' => ['places', 'place'],
    'organization' => ['organizations', 'organization'],
    'collection' => ['collections', 'collection'],
    'event' => ['events', 'event'],
    'obituary' => ['obituaries', 'obituary'],
    'scv_group' => ['groups', 'group'],
    'military_profile' => ['warMemorials', 'warMemorial'],
];

$fieldMap = [
    'article' => [
        'subheadline' => 'subheadline', 'originally_published_title' => 'originallyPublishedTitle',
        'original_publish_date' => 'originalPublishDate', 'source_line' => 'sourceLine',
        'legacy_url' => 'legacyUrl', 'fine_print' => 'finePrint',
        'webmaster_note_bottom' => 'webmasterNoteBottom',
    ],
    'person' => [
        'full_name' => 'fullName', 'birth_date' => 'birthDate', 'birthplace' => 'birthplace',
        'death_date' => 'deathDate', 'burial_place' => 'burialPlace', 'occupation' => 'occupation',
        'author_bio' => 'authorBio', 'legacy_url' => 'personLegacyUrl',
        'fine_print' => 'personFinePrint', 'webmaster_note_bottom' => 'personWebmasterNoteBottom',
    ],
    'place' => [
        'address' => 'placeAddress', 'latitude' => 'placeLat', 'longitude' => 'placeLng',
        'date_established' => 'dateEstablished', 'place_aliases' => 'placeAliases',
        'place_chl_number' => 'placeChlNumber', 'legacy_url' => 'placeLegacyUrl',
    ],
    'organization' => [
        'address' => 'orgAddress', 'aliases' => 'orgAliases', 'date_founded' => 'dateFounded',
        'org_lat' => 'orgLat', 'org_lng' => 'orgLng', 'legacy_url' => 'orgLegacyUrl',
    ],
    'collection' => [
        'legacy_url' => 'legacyUrl', 'original_publish_date' => 'originalPublishDate',
        'collection_note' => 'body',
    ],
    'event' => [
        'event_date' => 'eventDate', 'event_date_start' => 'eventDateStart',
        'event_significance' => 'eventSignificance',
    ],
    'obituary' => [
        'date_of_death' => 'obitDateOfDeath', 'published_in' => 'obitPublishedIn',
        'legacy_url' => 'obitLegacyUrl', 'webmaster_note_top' => 'obitWebmasterNoteTop',
        'webmaster_note_bottom' => 'obitWebmasterNoteBottom',
    ],
    'scv_group' => [
        'aliases' => 'groupAliases', 'date_start' => 'groupDateStart',
        'date_end' => 'groupDateEnd', 'legacy_url' => 'groupLegacyUrl',
    ],
    'military_profile' => [
        'branch' => 'wmBranch', 'rank' => 'wmRank', 'unit' => 'wmUnit',
        'home_of_record' => 'wmHomeOfRecord', 'high_school' => 'wmHighSchool',
        'specialty' => 'wmSpecialty', 'base' => 'wmBase',
        'combat_operations' => 'wmCombatOperations', 'incident_date' => 'wmIncidentDate',
        'incident_location' => 'wmIncidentLocation', 'age_at_loss' => 'wmAgeAtLoss',
        'awards' => 'wmAwards', 'date_of_birth' => 'wmDateOfBirth',
        'burial_place' => 'burialPlace', 'date_of_death' => 'deathDate',
        'legacy_url' => 'legacyUrl',
    ],
];

$relationMap = [
    'article' => [
        'written_by' => 'writtenBy', 'edited_by' => 'editedBy', 'published_by' => 'publishedBy',
        'subject_person' => 'subjectPerson', 'subject_organization' => 'subjectOrganization',
        'subject_group' => 'subjectGroup', 'depicts_place' => 'depictsPlace',
        'part_of_collection' => 'partOfCollection', 'related_articles' => 'relatedArticles',
    ],
    'person' => [
        'parent_of' => 'parentOf', 'child_of' => 'childOf', 'sibling_of' => 'siblingOf',
        'organizations' => 'personOrganizations', 'groups' => 'personGroups',
        'articles_about' => 'articlesAbout', 'obituaries' => 'personObituaries',
    ],
    'place' => [
        'place_people' => 'placePeople', 'place_events' => 'placeEvents',
        'articles_about' => 'placeArticles',
    ],
    'organization' => [
        'founded_by' => 'orgFoundedBy', 'associated_persons' => 'orgAssociatedPersons',
        'organization_events' => 'orgEvents',
    ],
    'collection' => [
        'articles_in_collection' => 'articlesInCollection', 'written_by' => 'writtenBy',
        'edited_by' => 'editedBy', 'published_by' => 'publishedBy',
    ],
    'event' => ['event_places' => 'eventPlaces'],
    'obituary' => ['obit_subject' => 'obitSubject'],
    'scv_group' => ['associated_persons' => 'groupPersons'],
    'military_profile' => [],
];

$catFieldFor = [
    'historical_era' => ['historicalEra', 'historicalEra'],
    'historical_period' => ['historicalPeriod', 'historicalPeriod'],
    'neighborhood' => ['neighborhood', 'neighborhood'],
];

$termCache = [];
$findTerm = function (string $group, string $title) use (&$termCache) {
    $key = $group . '|' . strtolower($title);
    if (array_key_exists($key, $termCache)) {
        return $termCache[$key];
    }
    $t = \craft\elements\Category::find()->group($group)->title($title)->status(null)->one();
    $termCache[$key] = $t;
    return $t;
};

$entryFor = function (string $wpType, string $slug) use ($sectionFor) {
    if (!isset($sectionFor[$wpType])) {
        return null;
    }
    [$section] = $sectionFor[$wpType];
    return \craft\elements\Entry::find()->section($section)->slug($slug)->status(null)->one();
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo '=== pass 1: entries and fields ===' . PHP_EOL;

$created = 0; $updated = 0; $missingTerms = [];

foreach ($posts as $p) {
    $wpType = $p['type'];
    if (!isset($sectionFor[$wpType])) { continue; }
    [$sectionHandle, $typeHandle] = $sectionFor[$wpType];
    $slug = $p['slug'];
    if ($slug === '') { echo 'SKIP no slug: ' . $p['title'] . PHP_EOL; continue; }

    $entry = $entryFor($wpType, $slug);
    $isNew = ($entry === null);

    if ($isNew) {
        $section = Craft::$app->getEntries()->getSectionByHandle($sectionHandle);
        $type = Craft::$app->getEntries()->getEntryTypeByHandle($typeHandle);
        if (!$section || !$type) { echo 'ERROR section/type missing for ' . $wpType . PHP_EOL; continue; }
        $entry = new \craft\elements\Entry();
        $entry->sectionId = $section->id;
        $entry->setTypeId($type->id);
        $entry->slug = $slug;
        $created++;
    } else {
        $updated++;
    }

    $entry->title = $p['title'];

    $sets = [];
    $map = $fieldMap[$wpType] ?? [];
    foreach ($map as $wpKey => $handle) {
        if (!isset($p['meta'][$wpKey])) { continue; }
        $sets[$handle] = $p['meta'][$wpKey];
    }
    if ($p['body'] !== '' && !isset($sets['body'])) {
        $sets['body'] = $p['body'];
    }

    foreach (($p['terms'] ?? []) as $tax => $titles) {
        if (!isset($catFieldFor[$tax])) { continue; }
        [$handle, $group] = $catFieldFor[$tax];
        $ids = [];
        foreach ($titles as $tt) {
            $term = $findTerm($group, $tt);
            if ($term) { $ids[] = $term->id; }
            else { $missingTerms[$tax . ': ' . $tt] = true; }
        }
        if ($ids) { $sets[$handle] = $ids; }
    }

    if ($APPLY) {
        foreach ($sets as $handle => $value) {
            try { $entry->setFieldValue($handle, $value); }
            catch (\Throwable $e) { echo '  field skip ' . $handle . ' on ' . $slug . PHP_EOL; }
        }
        if (!$elements->saveElement($entry)) {
            echo 'SAVE FAILED ' . $slug . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
        }
    }
    echo ($isNew ? 'NEW    ' : 'UPDATE ') . str_pad($wpType, 17) . str_pad($slug, 38) . count($sets) . ' fields' . PHP_EOL;
}

echo '=== pass 2: relations ===' . PHP_EOL;
$relCount = 0;

foreach ($posts as $p) {
    $wpType = $p['type'];
    if (!isset($relationMap[$wpType]) || !count($p['relations'])) { continue; }
    $entry = $entryFor($wpType, $p['slug']);
    if (!$entry) { echo 'missing entry for relations: ' . $p['slug'] . PHP_EOL; continue; }

    $sets = [];
    foreach ($p['relations'] as $wpKey => $targets) {
        $handle = $relationMap[$wpType][$wpKey] ?? null;
        if ($handle === null) { continue; }
        $ids = [];
        foreach ($targets as $t) {
            if ($t['type'] === 'attachment') { continue; }
            $target = $entryFor($t['type'], $t['slug']);
            if ($target) { $ids[] = $target->id; }
        }
        if ($ids) { $sets[$handle] = $ids; }
    }
    if (!count($sets)) { continue; }

    echo str_pad($p['slug'], 38) . implode(', ', array_keys($sets)) . PHP_EOL;
    $relCount += count($sets);

    if ($APPLY) {
        foreach ($sets as $handle => $ids) {
            try { $entry->setFieldValue($handle, $ids); }
            catch (\Throwable $e) { echo '  relation skip ' . $handle . PHP_EOL; }
        }
        if (!$elements->saveElement($entry)) {
            echo 'SAVE FAILED ' . $p['slug'] . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
        }
    }
}

echo '=== summary ===' . PHP_EOL;
echo 'new entries: ' . $created . PHP_EOL;
echo 'updated entries: ' . $updated . PHP_EOL;
echo 'relation fields set: ' . $relCount . PHP_EOL;
if (count($missingTerms)) {
    echo 'missing category terms:' . PHP_EOL;
    foreach (array_keys($missingTerms) as $m) { echo '  ' . $m . PHP_EOL; }
}
echo 'NOTE: images, link fields (wikipedia, find a grave, chl), and tags are not imported yet.' . PHP_EOL;
