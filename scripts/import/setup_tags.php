/**
 * Creates the Tags taxonomy and wires it into every entry type.
 *
 * Category group : handle `tag`, name Tags, uriFormat tags/{slug},
 *                  template tags/_entry, on every site
 * Field          : handle `recordTags`, name Tags, a Categories field
 *                  pointing at that group
 * Layouts        : the field is added to the Content tab of every entry type
 *                  that does not already carry it
 *
 * No terms are created. Tags are Nathan's to add, or an import's.
 *
 * Safe to run twice: an existing group, field or layout entry is left alone.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/setup_tags.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$GROUP_HANDLE = 'tag';
$FIELD_HANDLE = 'recordTags';

$categories = Craft::$app->getCategories();
$fields     = Craft::$app->getFields();
$entries    = Craft::$app->getEntries();
$sites      = Craft::$app->getSites();

echo ($APPLY ? '=== APPLYING ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL;

/* ── 1. the category group ────────────────────────────────────── */

echo PHP_EOL . '--- 1. category group ---' . PHP_EOL;

$group = $categories->getGroupByHandle($GROUP_HANDLE);
if ($group) {
    echo '  group `' . $GROUP_HANDLE . '` already exists' . PHP_EOL;
} else {
    echo '  would create group `' . $GROUP_HANDLE . '`: Tags, uriFormat tags/{slug}, template tags/_entry' . PHP_EOL;
    if ($APPLY) {
        $group = new \craft\models\CategoryGroup();
        $group->name = 'Tags';
        $group->handle = $GROUP_HANDLE;

        $siteSettings = [];
        foreach ($sites->getAllSites() as $site) {
            $siteSettings[$site->id] = new \craft\models\CategoryGroup_SiteSettings([
                'siteId' => $site->id,
                'hasUrls' => true,
                'uriFormat' => 'tags/{slug}',
                'template' => 'tags/_entry',
            ]);
        }
        $group->setSiteSettings($siteSettings);

        if (!$categories->saveGroup($group)) {
            echo '  FAILED to save group: ' . json_encode($group->getErrors()) . PHP_EOL;
            return;
        }
        echo '  created group `' . $GROUP_HANDLE . '`' . PHP_EOL;
    }
}

/* ── 2. the field ─────────────────────────────────────────────── */

echo PHP_EOL . '--- 2. field ---' . PHP_EOL;

$field = $fields->getFieldByHandle($FIELD_HANDLE);
if ($field) {
    echo '  field `' . $FIELD_HANDLE . '` already exists' . PHP_EOL;
} else {
    echo '  would create field `' . $FIELD_HANDLE . '`, a Categories field on the Tags group' . PHP_EOL;
    if ($APPLY) {
        if (!$group) {
            echo '  ERROR: the group is missing, cannot point the field at it' . PHP_EOL;
            return;
        }
        $field = new \craft\fields\Categories();
        $field->name = 'Tags';
        $field->handle = $FIELD_HANDLE;
        $field->instructions = 'Anything worth noting that does not have its own record. Never tag something that already has a record.';
        $field->source = 'group:' . $group->uid;
        $field->viewMode = 'list';
        $field->showSearchInput = true;
        $field->maintainHierarchy = false;

        if (!$fields->saveField($field)) {
            echo '  FAILED to save field: ' . json_encode($field->getErrors()) . PHP_EOL;
            return;
        }
        echo '  created field `' . $FIELD_HANDLE . '`' . PHP_EOL;
    }
}

/* ── 3. the layouts ───────────────────────────────────────────── */

echo PHP_EOL . '--- 3. entry type layouts ---' . PHP_EOL;

$added = 0; $already = 0;

foreach ($entries->getAllEntryTypes() as $type) {
    $layout = $type->getFieldLayout();

    $has = false;
    if ($layout) {
        foreach ($layout->getCustomFields() as $f) {
            if ($f->handle === $FIELD_HANDLE) { $has = true; break; }
        }
    }
    if ($has) {
        echo sprintf('  %-18s already carries %s', $type->handle, $FIELD_HANDLE) . PHP_EOL;
        $already++;
        continue;
    }

    echo sprintf('  %-18s would add %s to its Content tab', $type->handle, $FIELD_HANDLE) . PHP_EOL;

    if (!$APPLY) { continue; }
    if (!$field) { echo '      field missing, skipping' . PHP_EOL; continue; }

    try {
        $tabs = $layout->getTabs();
        $tab = null;
        foreach ($tabs as $t) {
            if (strtolower($t->name) === 'content') { $tab = $t; break; }
        }
        if ($tab === null) { $tab = $tabs[0] ?? null; }
        if ($tab === null) {
            $tab = new \craft\models\FieldLayoutTab([
                'layout' => $layout, 'name' => 'Content', 'sortOrder' => 1,
            ]);
            $tab->setElements([]);
            $tabs[] = $tab;
        }

        $els = $tab->getElements();
        $els[] = new \craft\fieldlayoutelements\CustomField($field);
        $tab->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);

        if (!$entries->saveEntryType($type)) {
            echo '      FAILED: ' . json_encode($type->getErrors()) . PHP_EOL;
            continue;
        }
        $added++;
    } catch (\Throwable $e) {
        echo '      FAILED: ' . $e->getMessage() . PHP_EOL;
    }
}

/* ── summary ──────────────────────────────────────────────────── */

$termCount = 0;
if ($group) {
    $termCount = \craft\elements\Category::find()->group($GROUP_HANDLE)->status(null)->count();
}

echo PHP_EOL . sprintf(
    'Entry types: %d already had the field, %d %s. Terms in the group: %d.',
    $already, $added, ($APPLY ? 'updated' : 'would be updated'), $termCount
) . PHP_EOL;
echo 'No terms are created by this script. Add them in the control panel or by import.' . PHP_EOL;

if ($APPLY) {
    echo 'Now run project-config/write if you keep config in files, then commit config/project/.' . PHP_EOL;
} else {
    echo 'Nothing was written. Set $APPLY = true and run again.' . PHP_EOL;
}
