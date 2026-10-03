/**
 * Chris Trunkey's portrait, on the Ahuja precedent (Nathan, 1 October 2026: campaign
 * material, rights not established, recorded as such; "I would rather have it
 * labelled honestly than absent").
 *
 * inventory/incoming/Trunkey-Chris-scaled.jpg, 2560 x 2406, SHA-256
 * 0e5ec9d6aca808d793697cfdd07a38bd57bb441e9aee401fc3e45945dba1de6f. Its embedded metadata names the
 * photographer and rights holder: Artist "Jennifer Emery", Copyright "(c)Jennifer
 * Emery", description "Christopher Trunkey Campaign", created 12 July 2016. So the
 * rights holder is recorded, and the licence stays unknown: no permission to
 * publish is established. The "-scaled" name is WordPress's, so the file as
 * received is a web copy.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_trunkey_portrait.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$FILE = \Craft::getAlias('@root') . '/inventory/incoming/Trunkey-Chris-scaled.jpg'; $SHA = '0e5ec9d6aca808d793697cfdd07a38bd57bb441e9aee401fc3e45945dba1de6f'; $FILENAME = 'chris-trunkey-campaign-image.jpg';
if (!is_file($FILE) || hash_file('sha256', $FILE) !== $SHA) { echo 'REFUSING: the file is missing or changed' . PHP_EOL; return; }
$p = Entry::find()->id(25409)->status(null)->one();
if (!$p || $p->title !== 'Chris Trunkey') { echo 'REFUSING: #25409 is not Chris Trunkey' . PHP_EOL; return; }
$have = Asset::find()->filename($FILENAME)->one(); $cur = $p->featuredImage->one();
if ($cur && (!$have || $cur->id !== $have->id)) { echo 'REFUSING: #25409 already has a portrait, #' . $cur->id . PHP_EOL; return; }
echo ($have ? "#{$have->id} exists" : "import $FILENAME") . ($cur ? ', already the portrait' : ', set as the portrait of #25409') . '; license unknown, campaign material, rights holder Jennifer Emery' . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$el = Craft::$app->getElements();
if (!$have) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
    $tmp = sys_get_temp_dir() . '/' . $FILENAME; copy($FILE, $tmp);
    $have = new Asset(); $have->tempFilePath = $tmp; $have->setFilename($FILENAME); $have->newFolderId = $folder->id; $have->setVolumeId($volume->id); $have->setScenario(Asset::SCENARIO_CREATE); $have->avoidFilenameConflicts = false;
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
    $have = Asset::find()->id($have->id)->one();
    $have->title = 'Chris Trunkey, campaign image, 2016'; $have->alt = 'Portrait of Chris Trunkey';
    $vals = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-03', 'rightsHolder' => 'Jennifer Emery', 'creditName' => 'Jennifer Emery',
        'rightsNote' => 'Photograph by Jennifer Emery, who holds the copyright by the file\'s own notice. Made for his campaign in July 2016. No permission to publish is established.',
        'source' => 'Campaign material, supplied by Nathan Imhoff: a photograph by Jennifer Emery for his campaign, July 2016, as the file\'s own notice records. Permission to publish is not established.', 'sourceChecksum' => 'sha256:' . $SHA];
    $ah = array_map(fn($f) => $f->handle, $have->getFieldLayout()->getCustomFields()); $have->setFieldValues(array_intersect_key($vals, array_flip($ah)));
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
}
$p = Entry::find()->id(25409)->status(null)->one(); $p->setFieldValue('featuredImage', [$have->id]);
if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$ok = (Entry::find()->id(25409)->status(null)->one()->featuredImage->one()?->id === $have->id);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_trunkey_portrait.php', 1, $ok ? 'verified' : 'SHORT', 'Chris Trunkey campaign image (Jennifer Emery), rights not established');
if (!$ok) { throw new \RuntimeException('import_trunkey_portrait: read-back failed'); }
