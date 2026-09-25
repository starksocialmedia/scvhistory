/**
 * Puts partOfCollection on the document layout.
 *
 * A topic collection lists articles, photographs AND documents
 * (templates/collections/_topic.twig groups all three), and it finds them
 * through partOfCollection. Articles and photographs carry that field;
 * documents never did, and articlesInCollection accepts articles only. So a
 * document could not be a member of any collection: the eight Mentry documents
 * that pico.htm lists had nowhere to go.
 *
 * Schema only: the existing field goes on the layout, beside the other
 * relations. No field is created and no entry is touched.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_document_collection_field.php'))"
 * Then: ddev craft project-config/write, and commit config/project.
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$svc = Craft::$app->getEntries();
$type = $svc->getEntryTypeByHandle('document');
$field = Craft::$app->getFields()->getFieldByHandle('partOfCollection');
if (!$type || !$field) { echo 'missing: ' . (!$type ? 'document type ' : '') . (!$field ? 'partOfCollection' : '') . PHP_EOL; return; }

$layout = $type->getFieldLayout();
if ($layout->getFieldByHandle('partOfCollection')) {
    echo 'partOfCollection is already on the document layout; nothing to do.' . PHP_EOL;
    return;
}
/* Beside subjectPerson, on whichever tab holds it: that is where a document's
   relations live. */
$tabs = $layout->getTabs();
$target = $tabs[0];
foreach ($tabs as $tab) {
    foreach ($tab->getElements() as $el) {
        if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->attribute() === 'subjectPerson') { $target = $tab; }
    }
}
echo 'would add partOfCollection to the document layout, tab "' . $target->name . '"' . PHP_EOL;
if (!$APPLY) { echo 'DRY RUN' . PHP_EOL; return; }

$els = $target->getElements();
$els[] = new \craft\fieldlayoutelements\CustomField($field);
$target->setElements($els);
$layout->setTabs($tabs);
$type->setFieldLayout($layout);
if (!$svc->saveEntryType($type)) {
    throw new \RuntimeException('add_document_collection_field: ' . json_encode($type->getErrors()));
}

$fresh = $svc->getEntryTypeByHandle('document');
$ok = (bool)$fresh->getFieldLayout()->getFieldByHandle('partOfCollection');
echo 'READ-BACK ' . ($ok ? 'OK: partOfCollection is on the document layout' : 'FAIL: not on the layout after save') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_document_collection_field.php', 1, $ok ? 'verified: on the document layout' : 'FAILED: not on the layout',
    'schema only; documents can now join a collection');
if (!$ok) { throw new \RuntimeException('add_document_collection_field: read-back failed'); }
