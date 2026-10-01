/**
 * Aakash Ahuja's portrait (Nathan, 1 October 2026: "import it. Source is
 * campaign material, rights not established, recorded as such, same as
 * Gutzeit. I would rather have it labelled honestly than absent").
 *
 * inventory/incoming/Candidate-Dr.AakashAhuja.jpg, 840 x 1001, SHA-256
 * 0a44d553a1a62de146e76cf1691f99d91bbe01ae4d5ef3e1de42f8a9f956069a: an export from Adobe Photoshop 25.11 with no author,
 * copyright or caption and no content credentials. License unknown, and the
 * source line says so, as on Maria Gutzeit's #21581.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_ahuja_portrait.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$FILE = \Craft::getAlias('@root') . '/inventory/incoming/Candidate-Dr.AakashAhuja.jpg'; $SHA = '0a44d553a1a62de146e76cf1691f99d91bbe01ae4d5ef3e1de42f8a9f956069a'; $FILENAME = 'aakash-ahuja-campaign-image.jpg';
if (!is_file($FILE) || hash_file('sha256', $FILE) !== $SHA) { echo 'REFUSING: the file is missing or changed' . PHP_EOL; return; }
$p = Entry::find()->id(25449)->status(null)->one();
if (!$p || $p->title !== 'Aakash Ahuja') { echo 'REFUSING: #25449 is not Aakash Ahuja' . PHP_EOL; return; }
$have = Asset::find()->filename($FILENAME)->one(); $cur = $p->featuredImage->one();
if ($cur && (!$have || $cur->id !== $have->id)) { echo 'REFUSING: #25449 already has a portrait, #' . $cur->id . PHP_EOL; return; }
echo ($have ? "#{$have->id} exists" : "import $FILENAME") . ($cur ? ', already the portrait' : ', set as the portrait of #25449') . '; license unknown, campaign material' . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$el = Craft::$app->getElements();
if (!$have) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
    $tmp = sys_get_temp_dir() . '/' . $FILENAME; copy($FILE, $tmp);
    $have = new Asset(); $have->tempFilePath = $tmp; $have->setFilename($FILENAME); $have->newFolderId = $folder->id; $have->setVolumeId($volume->id); $have->setScenario(Asset::SCENARIO_CREATE); $have->avoidFilenameConflicts = false;
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
    $have = Asset::find()->id($have->id)->one();
    $have->title = 'Aakash Ahuja, campaign image'; $have->alt = 'Portrait of Aakash Ahuja';
    $have->setFieldValues(['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-01',
        'source' => 'Campaign material. Received from Nathan Imhoff on 1 October 2026 as Candidate-Dr.AakashAhuja.jpg, SHA-256 ' . $SHA . ', an export from Adobe Photoshop with no author, copyright or caption. The photographer, the rights holder and any permission to publish are not established. The stored copy is re-encoded on import and differs from the file as received.']);
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
}
$p = Entry::find()->id(25449)->status(null)->one(); $p->setFieldValue('featuredImage', [$have->id]);
if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$ok = (Entry::find()->id(25449)->status(null)->one()->featuredImage->one()?->id === $have->id);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_ahuja_portrait.php', 1, $ok ? 'verified' : 'SHORT', 'Aakash Ahuja campaign image, rights not established');
if (!$ok) { throw new \RuntimeException('import_ahuja_portrait: read-back failed'); }
