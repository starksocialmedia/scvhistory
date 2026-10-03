/**
 * Christy Smith's portrait (Nathan, 3 October 2026: "import both as you propose,
 * licence unknown, photographer named, reasoning in the rights note").
 *
 * inventory/incoming/Christy_Smith_CA_Assembly_official_photo.jpg, 1920 x 2688,
 * SHA-256 8f28b40b78e78fad463c12adcb769353ad1ca40f78d030226b90dffd07af4bdc: a reduced copy of
 * Wikimedia Commons' "Christy Smith CA Assembly official photo.jpg" (3082 x 4315,
 * uploaded 4 December 2018, source https://a38.asmdc.org/biography, author given
 * as the California State Assembly, tagged PD-CAGov). The file's embedded
 * notice reads "Copyright: Jeff Walters".
 *
 * Why the licence is unknown, not public domain: PD-CAGov rests on the California
 * Public Records Act, and the Act's "state agency" excludes "those agencies
 * provided for in Article IV" of the Constitution, the Legislature (Government
 * Code 7920.540(a)). And the photographer's own notice names him as holder.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_smith_portrait.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$FILE = \Craft::getAlias('@root') . '/inventory/incoming/Christy_Smith_CA_Assembly_official_photo.jpg'; $SHA = '8f28b40b78e78fad463c12adcb769353ad1ca40f78d030226b90dffd07af4bdc'; $FILENAME = 'christy-smith-assembly-portrait-2018.jpg';
if (!is_file($FILE) || hash_file('sha256', $FILE) !== $SHA) { echo 'REFUSING: the file is missing or changed' . PHP_EOL; return; }
$p = Entry::find()->id(25389)->status(null)->one();
if (!$p || $p->title !== 'Christy Smith') { echo 'REFUSING: #25389 is not Christy Smith' . PHP_EOL; return; }
$have = Asset::find()->filename($FILENAME)->one(); $cur = $p->featuredImage->one();
if ($cur && (!$have || $cur->id !== $have->id)) { echo 'REFUSING: #25389 already has a portrait, #' . $cur->id . PHP_EOL; return; }
echo ($have ? "#{$have->id} exists" : "import $FILENAME") . ($cur ? ', already the portrait' : ', set as the portrait of #25389') . '; license unknown, Assembly portrait, rights holder Jeff Walters by the file\'s notice' . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$el = Craft::$app->getElements();
if (!$have) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
    $tmp = sys_get_temp_dir() . '/' . $FILENAME; copy($FILE, $tmp);
    $have = new Asset(); $have->tempFilePath = $tmp; $have->setFilename($FILENAME); $have->newFolderId = $folder->id; $have->setVolumeId($volume->id); $have->setScenario(Asset::SCENARIO_CREATE); $have->avoidFilenameConflicts = false;
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
    $have = Asset::find()->id($have->id)->one();
    $have->title = 'Christy Smith, California State Assembly official portrait, 2018'; $have->alt = 'Portrait of Christy Smith';
    $vals = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-03', 'rightsHolder' => 'Jeff Walters, by the file\'s own notice', 'creditName' => 'Jeff Walters for the California State Assembly',
        'rightsNote' => 'The official Assembly portrait of 2018. Wikimedia Commons tags it public domain as a California state work (PD-CAGov), but that rests on the Public Records Act, which excludes the Legislature (Government Code 7920.540(a)), and the file names Jeff Walters as copyright holder. Public domain is not established; no permission to publish is in hand.',
        'source' => 'The California State Assembly\'s official portrait of 2018, from a reduced copy of the file on Wikimedia Commons ("Christy Smith CA Assembly official photo.jpg", taken from https://a38.asmdc.org/biography), supplied by Nathan Imhoff. The file names Jeff Walters as copyright holder.', 'sourceChecksum' => 'sha256:' . $SHA];
    $ah = array_map(fn($f) => $f->handle, $have->getFieldLayout()->getCustomFields()); $have->setFieldValues(array_intersect_key($vals, array_flip($ah)));
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
}
$p = Entry::find()->id(25389)->status(null)->one(); $p->setFieldValue('featuredImage', [$have->id]);
if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$ok = (Entry::find()->id(25389)->status(null)->one()->featuredImage->one()?->id === $have->id);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_smith_portrait.php', 1, $ok ? 'verified' : 'SHORT', 'Christy Smith Assembly portrait (Jeff Walters), public domain not established');
if (!$ok) { throw new \RuntimeException('import_smith_portrait: read-back failed'); }
