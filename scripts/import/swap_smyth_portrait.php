/**
 * Cameron Smyth #16380: a new portrait (Nathan, 3 October 2026: "swap it for
 * Cameron-Smyth-2017-820x1024-1.jpg").
 *
 * inventory/incoming/Cameron-Smyth-2017-820x1024-1.jpg, 820 x 1024, SHA-256
 * d171e97d1f7cd64f12c31eb562fe917990f0ebff2a047e9b935a76f85aaa87cb. It carries
 * no metadata at all: no artist, copyright, caption or date. The name ("2017",
 * "-820x1024-1") is WordPress's for a resized web copy, so it was taken from a
 * web page, which one is not recorded. Licence unknown, photographer and rights
 * holder not recorded; "2017" in the name is not taken as a date.
 *
 * The portrait it replaces, #21579 (Nathan Imhoff's photograph, licensed to the
 * archive), is kept as an asset; only featuredImage changes. The script refuses
 * if the current portrait is anything other than #21579 or the new file.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/swap_smyth_portrait.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$FILE = \Craft::getAlias('@root') . '/inventory/incoming/Cameron-Smyth-2017-820x1024-1.jpg'; $SHA = 'd171e97d1f7cd64f12c31eb562fe917990f0ebff2a047e9b935a76f85aaa87cb'; $FILENAME = 'cameron-smyth-2017.jpg'; $OLD = 21579;
if (!is_file($FILE) || hash_file('sha256', $FILE) !== $SHA) { echo 'REFUSING: the file is missing or changed' . PHP_EOL; return; }
$p = Entry::find()->id(16380)->status(null)->one();
if (!$p || $p->title !== 'Cameron Smyth') { echo 'REFUSING: #16380 is not Cameron Smyth' . PHP_EOL; return; }
$have = Asset::find()->filename($FILENAME)->one(); $cur = $p->featuredImage->one();
if ($cur && $have && $cur->id === $have->id) { echo 'already done: the portrait is #' . $have->id . PHP_EOL; return; }
if (!$cur || $cur->id !== $OLD) { echo 'REFUSING: the current portrait is not #21579, ' . ($cur ? '#' . $cur->id : 'none') . PHP_EOL; return; }
echo ($have ? "#{$have->id} exists" : "import $FILENAME") . "; featuredImage #$OLD -> the new file; #$OLD kept; licence unknown, no metadata" . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$el = Craft::$app->getElements();
if (!$have) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
    $tmp = sys_get_temp_dir() . '/' . $FILENAME; copy($FILE, $tmp);
    $have = new Asset(); $have->tempFilePath = $tmp; $have->setFilename($FILENAME); $have->newFolderId = $folder->id; $have->setVolumeId($volume->id); $have->setScenario(Asset::SCENARIO_CREATE); $have->avoidFilenameConflicts = false;
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
    $have = Asset::find()->id($have->id)->one();
    $have->title = 'Cameron Smyth'; $have->alt = 'Portrait of Cameron Smyth';
    $vals = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-03', 'sourceChecksum' => 'sha256:' . $SHA,
        'source' => 'Supplied by Nathan Imhoff, a resized copy from a web page; which page, the photographer and the rights holder are not recorded.'];
    $ah = array_map(fn($f) => $f->handle, $have->getFieldLayout()->getCustomFields()); $have->setFieldValues(array_intersect_key($vals, array_flip($ah)));
    if (!$el->saveElement($have)) { throw new \RuntimeException(json_encode($have->getFirstErrors())); }
}
$p = Entry::find()->id(16380)->status(null)->one(); $p->setFieldValue('featuredImage', [$have->id]);
if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$ok = Entry::find()->id(16380)->status(null)->one()->featuredImage->one()?->id === $have->id && Asset::find()->id($OLD)->exists();
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('swap_smyth_portrait.php', 1, $ok ? 'verified' : 'SHORT', 'Cameron Smyth: new portrait, rights not recorded; #21579 kept');
if (!$ok) { throw new \RuntimeException('swap_smyth_portrait: read-back failed'); }
