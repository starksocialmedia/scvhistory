/**
 * Gives the document entry type a publisher, a date and a subject.
 *
 * The documents section holds no records yet and its layout has no way to say
 * when a document was published, by whom, or who it is about. The first
 * document the archive wants is the 1874 Tiburcio Vasquez sketch, which is
 * exactly those three things: published in Los Angeles, in 1874, about a man
 * the archive already has a record for.
 *
 * No new fields. Every one of these exists and is in use on articles, so a
 * document says these things the same way an article does, and the exports and
 * the schema.org mapping already know what they mean:
 *
 *   originallyPublishedTitle  the headline as printed
 *   originalPublishDate       the date as printed
 *   originalPublishDateEdtf   the EDTF form where it parses
 *   sourceLine                the credit line, verbatim
 *   publishedBy               Entries, organizations only
 *   subjectPerson             Entries, persons only
 *
 * A note on publishedBy: it accepts organization records only. V. Wolfenstein
 * of Los Angeles was a person publishing under his own name, and inventing an
 * organization record for him would be a claim the source does not make. The
 * Vasquez record therefore carries the credit verbatim in sourceLine and
 * leaves publishedBy empty until somebody decides whether Wolfenstein gets a
 * record of his own. The field is added because the next document will need it.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_document_publishing_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

$TYPE = 'document';
$ADD = ['originallyPublishedTitle', 'originalPublishDate', 'originalPublishDateEdtf', 'sourceLine', 'publishedBy', 'subjectPerson'];
$AFTER = 'body';

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }
$layout = $type->getFieldLayout();
$present = array_map(fn($c) => $c->handle, $layout->getCustomFields());

$missingField = []; $toAdd = [];
foreach ($ADD as $h) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) { $missingField[] = $h; continue; }
    if (in_array($h, $present, true)) { echo str_pad($h, 26) . 'already on the document layout' . PHP_EOL; continue; }
    $toAdd[] = $f;
    echo str_pad($h, 26) . 'would add, ' . (new \ReflectionClass($f))->getShortName()
       . (is_array($f->sources ?? null) ? ' (' . count($f->sources) . ' source)' : '') . PHP_EOL;
}
foreach ($missingField as $h) { echo str_pad($h, 26) . 'FIELD DOES NOT EXIST' . PHP_EOL; }
if ($missingField) { echo PHP_EOL . 'stopping: this script adds existing fields only.' . PHP_EOL; return; }

echo PHP_EOL . 'documents in the section: ' . \craft\elements\Entry::find()->section('documents')->status(null)->count() . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 74) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}
if (!$toAdd) { echo 'nothing to add.' . PHP_EOL; return; }

$tabs = $layout->getTabs();
$tabIdx = 0; $at = null;
foreach ($tabs as $ti => $tab) {
    foreach ($tab->getElements() as $ei => $el) {
        if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->getField()->handle === $AFTER) { $tabIdx = $ti; $at = $ei + 1; }
    }
}
$els = $tabs[$tabIdx]->getElements();
$new = array_map(fn($f) => new \craft\fieldlayoutelements\CustomField($f), $toAdd);
array_splice($els, $at ?? count($els), 0, $new);
$tabs[$tabIdx]->setElements($els);
$layout->setTabs($tabs);
$type->setFieldLayout($layout);
if (!$svc->saveEntryType($type)) { echo 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; return; }

$t2 = $svc->getEntryTypeByHandle($TYPE);
$now = array_map(fn($c) => $c->handle, $t2->getFieldLayout()->getCustomFields());
$missing = array_values(array_diff($ADD, $now));
echo PHP_EOL . 'READ-BACK ' . ($missing ? 'FAIL' : 'OK') . PHP_EOL;
echo '   document layout now carries: ' . implode(', ', array_intersect($ADD, $now)) . PHP_EOL;
if ($missing) { echo '   missing: ' . implode(', ', $missing) . PHP_EOL; throw new \RuntimeException('add_document_publishing_fields read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_document_publishing_fields.php', count($toAdd), 'verified: ' . count($ADD) . ' fields on the document layout',
    'existing fields reused; publishedBy left empty for Wolfenstein');
