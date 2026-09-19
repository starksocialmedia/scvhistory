/**
 * The fix list: a section, not a field.
 *
 * The ask was a field on every record, editorNote or similar. A field is the
 * wrong shape here, for four reasons, and the fourth is the one that decides it:
 *
 *   1. It cannot hold a note about a record that does not exist. "There should
 *      be a record for the Ruiz-Perea census" and "this legacy page was never
 *      imported" are the commonest things to notice while reading, and neither
 *      has a record to hang off.
 *   2. It holds one note. The second thing you notice overwrites the first, or
 *      gets appended and the two share a done flag.
 *   3. A done flag needs a second field, and marking something done means
 *      editing the record it is about, which is the thing you were avoiding.
 *   4. A field is edited in the control panel. Noting a problem in five seconds
 *      while reading means writing it from the page you are reading, and a
 *      field cannot be reached from there.
 *
 * So: one entry per note, in its own section.
 *
 *   fixNote        what is wrong, in whatever words
 *   fixStatus      open, done, not a problem
 *   fixRecord      the record it is about, if there is one
 *   fixLegacyUrl   the legacy page it is about, if there is no record
 *   fixSeenOn      the URL you were reading when you noticed, filled in for you
 *
 * fixRecord and fixLegacyUrl are both optional and a note may have neither. A
 * note about nothing in particular is still a note.
 *
 * The entry type has no title field: the title is generated from the note, so
 * there is nothing to fill in but the note itself.
 *
 * The section has no URLs. The list is at /admin-fixes and the capture form is
 * in the page footer for a logged-in admin; neither needs the entries to be
 * addressable in their own right.
 *
 * Safe to run twice. Dry run by default; set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_fixes_section.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$fieldsService  = Craft::$app->getFields();
$entriesService = Craft::$app->getEntries();
$sitesService   = Craft::$app->getSites();

$DEFS = [
    'fixNote' => ['Fix Note', 'multiline',
        'What is wrong, in whatever words come to hand. This is a note to yourself, '
        . 'not a ticket; "dates look wrong" is a perfectly good note.'],
    'fixLegacyUrl' => ['Fix Legacy URL', 'singleline',
        'The legacy page this is about, where there is no record yet. Leave empty '
        . 'when the note is about a record, which Fix Record holds.'],
    'fixSeenOn' => ['Fix Seen On', 'singleline',
        'The page you were reading when you noticed. Filled in by the capture form; '
        . 'there is no need to type it.'],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* ------------------------------------------------------------- the fields */

foreach ($DEFS as $handle => [$name, $mode, $instructions]) {
    if ($fieldsService->getFieldByHandle($handle)) {
        echo str_pad($handle, 18) . 'exists already, left alone' . PHP_EOL;
        continue;
    }
    echo str_pad($handle, 18) . 'would create, PlainText ' . $mode . PHP_EOL;
    if ($APPLY) {
        $f = new \craft\fields\PlainText();
        $f->name = $name;
        $f->handle = $handle;
        $f->instructions = $instructions;
        $f->multiline = ($mode === 'multiline');
        if ($f->multiline) { $f->initialRows = 3; }
        echo str_pad('', 18) . ($fieldsService->saveField($f) ? 'created' : 'FAILED: ' . implode('; ', $f->getFirstErrors())) . PHP_EOL;
    }
}

if (!$fieldsService->getFieldByHandle('fixStatus')) {
    echo str_pad('fixStatus', 18) . 'would create, Dropdown: open, done, not a problem' . PHP_EOL;
    if ($APPLY) {
        $f = new \craft\fields\Dropdown();
        $f->name = 'Fix Status';
        $f->handle = 'fixStatus';
        $f->instructions = 'Open until somebody has dealt with it. "Not a problem" is a real '
            . 'answer and closes the note without pretending work was done.';
        $f->options = [
            ['label' => 'Open', 'value' => 'open', 'default' => true],
            ['label' => 'Done', 'value' => 'done', 'default' => false],
            ['label' => 'Not a problem', 'value' => 'wontfix', 'default' => false],
        ];
        echo str_pad('', 18) . ($fieldsService->saveField($f) ? 'created' : 'FAILED: ' . implode('; ', $f->getFirstErrors())) . PHP_EOL;
    }
} else {
    echo str_pad('fixStatus', 18) . 'exists already, left alone' . PHP_EOL;
}

if (!$fieldsService->getFieldByHandle('fixRecord')) {
    echo str_pad('fixRecord', 18) . 'would create, Entries, every section, at most one' . PHP_EOL;
    if ($APPLY) {
        $f = new \craft\fields\Entries();
        $f->name = 'Fix Record';
        $f->handle = 'fixRecord';
        $f->instructions = 'The record this note is about. Leave empty for a note about a '
            . 'legacy page with no record, or about nothing in particular.';
        $f->maxRelations = 1;
        $f->sources = '*';
        echo str_pad('', 18) . ($fieldsService->saveField($f) ? 'created' : 'FAILED: ' . implode('; ', $f->getFirstErrors())) . PHP_EOL;
    }
} else {
    echo str_pad('fixRecord', 18) . 'exists already, left alone' . PHP_EOL;
}

/* --------------------------------------------------------- the entry type */

$ORDER = ['fixNote', 'fixStatus', 'fixRecord', 'fixLegacyUrl', 'fixSeenOn'];
$entryType = $entriesService->getEntryTypeByHandle('fix');
if ($entryType) {
    echo PHP_EOL . 'entry type `fix` already exists, left alone' . PHP_EOL;
} else {
    echo PHP_EOL . 'would create entry type `fix`: ' . implode(', ', $ORDER) . PHP_EOL;
    echo '   no title field; the title is generated from the note' . PHP_EOL;
    if ($APPLY) {
        $entryType = new \craft\models\EntryType();
        $entryType->name = 'Fix';
        $entryType->handle = 'fix';
        $entryType->hasTitleField = false;
        $entryType->titleFormat = '{fixNote|slice(0, 70)}';
        $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
        $tab = new \craft\models\FieldLayoutTab(['layout' => $layout, 'name' => 'Note', 'sortOrder' => 1]);
        $els = [];
        foreach ($ORDER as $h) {
            $f = $fieldsService->getFieldByHandle($h);
            if (!$f) { echo 'ERROR: field ' . $h . ' missing' . PHP_EOL; return; }
            $el = new \craft\fieldlayoutelements\CustomField($f);
            if ($h === 'fixNote') { $el->required = true; }
            $els[] = $el;
        }
        $tab->setElements($els);
        $layout->setTabs([$tab]);
        $entryType->setFieldLayout($layout);
        if (!$entriesService->saveEntryType($entryType)) {
            echo 'FAILED: ' . json_encode($entryType->getErrors()) . PHP_EOL;
            return;
        }
        echo 'created entry type `fix`' . PHP_EOL;
    }
}

/* ------------------------------------------------------------ the section */

$section = $entriesService->getSectionByHandle('fixes');
if ($section) {
    echo 'section `fixes` already exists, left alone' . PHP_EOL;
} else {
    echo 'would create section `fixes`: channel, no URLs' . PHP_EOL;
    if ($APPLY) {
        if (!$entryType) { echo 'ERROR: entry type `fix` missing' . PHP_EOL; return; }
        $section = new \craft\models\Section();
        $section->name = 'Fixes';
        $section->handle = 'fixes';
        $section->type = \craft\models\Section::TYPE_CHANNEL;
        $section->enableVersioning = false;
        $siteSettings = [];
        foreach ($sitesService->getAllSites() as $site) {
            $siteSettings[$site->id] = new \craft\models\Section_SiteSettings([
                'siteId' => $site->id,
                'enabledByDefault' => true,
                'hasUrls' => false,
            ]);
        }
        $section->setSiteSettings($siteSettings);
        $section->setEntryTypes([$entryType]);
        echo ($entriesService->saveSection($section)
            ? 'created section `fixes`'
            : 'FAILED: ' . json_encode($section->getErrors())) . PHP_EOL;
    }
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
echo 'Then the list is at /admin-fixes and the capture form appears in the footer' . PHP_EOL;
echo 'of every page for a logged-in admin.' . PHP_EOL;
