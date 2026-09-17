/**
 * Creates the Pages channel, its entry type, and the eight site pages the
 * footer links to.
 *
 * Section  : Pages, handle `pages`, channel, uriFormat {slug},
 *            template pages/_entry
 * Entry type: page, with body, webmasterNoteTop, webmasterNoteBottom,
 *            featuredImage, recordImages, recordDocuments
 * Entries  : About, Contact, Permissions, Photo Credits, Newsletter,
 *            Submit a Photo or Article, Nonprofit, Privacy Policy
 *
 * All eight are created with empty bodies. Nothing here writes prose; the
 * copy is Nathan's to write in the control panel.
 *
 * Safe to run twice: an existing section, entry type or entry is left alone.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/setup_pages_section.php'))"
 */

$APPLY = true;

$PAGES = [
    ['About',                     'about'],
    ['Contact',                   'contact'],
    ['Permissions',               'permissions'],
    ['Photo Credits',             'photo-credits'],
    ['Newsletter',                'newsletter'],
    ['Submit a Photo or Article', 'submit'],
    ['Nonprofit',                 'nonprofit'],
    ['Privacy Policy',            'privacy'],
];

$FIELDS = ['body', 'webmasterNoteTop', 'webmasterNoteBottom', 'featuredImage', 'recordImages', 'recordDocuments'];

$entriesService = Craft::$app->getEntries();
$fieldsService  = Craft::$app->getFields();
$elements       = Craft::$app->getElements();
$sitesService   = Craft::$app->getSites();

echo ($APPLY ? '=== APPLYING ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL;

foreach ($FIELDS as $h) {
    if (!$fieldsService->getFieldByHandle($h)) {
        echo 'ERROR: field `' . $h . '` does not exist. Stopping.' . PHP_EOL;
        return;
    }
}
echo 'All six fields found.' . PHP_EOL;

$entryType = $entriesService->getEntryTypeByHandle('page');
if ($entryType) {
    echo 'Entry type `page` already exists.' . PHP_EOL;
} else {
    echo 'Would create entry type `page` with: ' . implode(', ', $FIELDS) . PHP_EOL;
    if ($APPLY) {
        $entryType = new \craft\models\EntryType();
        $entryType->name = 'Page';
        $entryType->handle = 'page';
        $entryType->hasTitleField = true;

        $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
        $tab = new \craft\models\FieldLayoutTab(['layout' => $layout, 'name' => 'Content', 'sortOrder' => 1]);
        $els = [new \craft\fieldlayoutelements\entries\EntryTitleField()];
        foreach ($FIELDS as $h) {
            $els[] = new \craft\fieldlayoutelements\CustomField($fieldsService->getFieldByHandle($h));
        }
        $tab->setElements($els);
        $layout->setTabs([$tab]);
        $entryType->setFieldLayout($layout);

        if (!$entriesService->saveEntryType($entryType)) {
            echo 'FAILED to save entry type: ' . json_encode($entryType->getErrors()) . PHP_EOL;
            return;
        }
        echo 'Created entry type `page`.' . PHP_EOL;
    }
}

$section = $entriesService->getSectionByHandle('pages');
if ($section) {
    echo 'Section `pages` already exists.' . PHP_EOL;
} else {
    echo 'Would create section `pages`: channel, uriFormat {slug}, template pages/_entry' . PHP_EOL;
    if ($APPLY) {
        if (!$entryType) {
            echo 'ERROR: entry type `page` missing, cannot create the section.' . PHP_EOL;
            return;
        }
        $section = new \craft\models\Section();
        $section->name = 'Pages';
        $section->handle = 'pages';
        $section->type = \craft\models\Section::TYPE_CHANNEL;
        $section->enableVersioning = true;

        $siteSettings = [];
        foreach ($sitesService->getAllSites() as $site) {
            $siteSettings[$site->id] = new \craft\models\Section_SiteSettings([
                'siteId' => $site->id,
                'enabledByDefault' => true,
                'hasUrls' => true,
                'uriFormat' => '{slug}',
                'template' => 'pages/_entry',
            ]);
        }
        $section->setSiteSettings($siteSettings);
        $section->setEntryTypes([$entryType]);

        if (!$entriesService->saveSection($section)) {
            echo 'FAILED to save section: ' . json_encode($section->getErrors()) . PHP_EOL;
            return;
        }
        echo 'Created section `pages`.' . PHP_EOL;
    }
}

echo '--- entries ---' . PHP_EOL;
$created = 0; $existing = 0;

foreach ($PAGES as [$title, $slug]) {
    $found = \craft\elements\Entry::find()->slug($slug)->status(null)->one();
    if ($found) {
        $sec = $found->getSection();
        echo sprintf('  %-26s exists already in `%s`', $slug, $sec ? $sec->handle : '?') . PHP_EOL;
        $existing++;
        continue;
    }
    echo sprintf('  %-26s would create "%s"', $slug, $title) . PHP_EOL;
    if ($APPLY) {
        if (!$section || !$entryType) {
            echo '      section or entry type missing, skipping' . PHP_EOL;
            continue;
        }
        $entry = new \craft\elements\Entry();
        $entry->sectionId = $section->id;
        $entry->typeId = $entryType->id;
        $entry->title = $title;
        $entry->slug = $slug;
        $entry->enabled = true;
        if (!$elements->saveElement($entry)) {
            echo '      FAILED: ' . json_encode($entry->getErrors()) . PHP_EOL;
            continue;
        }
        $created++;
    }
}

echo PHP_EOL . sprintf('Pages listed %d, created %d, already present %d.', count($PAGES), $created, $existing) . PHP_EOL;
if ($APPLY) {
    echo 'Now run project-config/write if you keep config in files, then commit config/project/.' . PHP_EOL;
} else {
    echo 'Nothing was written. Set $APPLY = true and run again.' . PHP_EOL;
}
