/**
 * A website for a place.
 *
 * Places have official sites as routinely as organizations do: a mission, a
 * park, a museum, a historic ranch. The place layout had nowhere to hold one,
 * so convert_orgs_to_places.php had to keep three missions live as
 * organizations rather than lose their websites. Nathan's ruling, 25
 * September: add the field, then retire them.
 *
 * Creates placeWebsite, a URL link field configured like orgWebsite, and puts
 * it on the place layout after placeWikipediaUrl, where the other links are.
 * Maps to schema.org `url`; generate_data_model.php needs the mapping row.
 *
 * Schema only. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_place_website_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$type = $svc->getEntryTypeByHandle('place');
$field = $fs->getFieldByHandle('placeWebsite');
$onLayout = (bool)$type->getFieldLayout()->getFieldByHandle('placeWebsite');

echo 'field placeWebsite: ' . ($field ? 'exists' : 'would create (Link, URL only, like orgWebsite)') . PHP_EOL;
echo 'on the place layout: ' . ($onLayout ? 'yes' : 'would add after placeWikipediaUrl') . PHP_EOL;
if ($field && $onLayout) { echo 'nothing to do' . PHP_EOL; return; }
if (!$APPLY) { echo 'DRY RUN' . PHP_EOL; return; }

if (!$field) {
    $field = new \craft\fields\Link([
        'name' => 'Place Website',
        'handle' => 'placeWebsite',
        'instructions' => 'The place\'s own official site, where it has one. Not Wikipedia: that has its own field.',
        'searchable' => false,
        'types' => ['url'],
        'maxLength' => 255,
        'showLabelField' => false,
    ]);
    if (!$fs->saveField($field)) { throw new \RuntimeException('add_place_website_field: ' . json_encode($field->getErrors())); }
}

$layout = $type->getFieldLayout();
$tabs = $layout->getTabs();
$target = $tabs[0]; $at = null;
foreach ($tabs as $tab) {
    foreach (array_values($tab->getElements()) as $i => $el) {
        if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->attribute() === 'placeWikipediaUrl') { $target = $tab; $at = $i; }
    }
}
$els = array_values($target->getElements());
$new = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('placeWebsite'));
if ($at !== null) { array_splice($els, $at + 1, 0, [$new]); } else { $els[] = $new; }
$target->setElements($els);
$layout->setTabs($tabs);
$type->setFieldLayout($layout);
if (!$svc->saveEntryType($type)) { throw new \RuntimeException('add_place_website_field: ' . json_encode($type->getErrors())); }

$ok = (bool)$svc->getEntryTypeByHandle('place')->getFieldLayout()->getFieldByHandle('placeWebsite');
echo 'READ-BACK ' . ($ok ? 'OK: placeWebsite is on the place layout' : 'FAIL: not on the layout after save') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_place_website_field.php', 1, $ok ? 'verified: field created and on the place layout' : 'FAILED', 'schema only; lets the three missions retire');
if (!$ok) { throw new \RuntimeException('add_place_website_field: read-back failed'); }
