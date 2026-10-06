/**
 * Sharlene Rose Johnson's portrait (Nathan, 5 October 2026: "Sharlene-Duzick.jpg is in inventory/incoming, from the batch of nine
 * district portraits on 4 October. I told you to drop it then, because she had no record under the losing-candidate rule. That
 * is reversed. She is Johnson, a sitting college trustee, so import it as her portrait. There is also sharlene-headshot.jpg ...
 * Check both and use the better").
 *   sharlene-headshot.jpg   857 by 1200, dated 29 August 2018, no content credential: the photograph.
 *   Sharlene-Duzick.jpg     2815 by 3755, the same photograph cropped and enlarged by Adobe Firefly's creative upsampler on
 *                           4 October 2026 (its content credential, read by scan_content_credentials.py): a generative edit.
 * The original is the better for the archive: it is large enough for the page, and an upsampled face carries detail the
 * camera did not record. The edited copy is not imported, so no enhancedFrom pair is made; it stays in incoming, listed.
 * Supplied by Nathan Imhoff on 4 October 2026; where the photograph was first published is not recorded with it. Licence
 * unknown: no permission to republish is established.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_johnson_portrait_2026_10_05.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$P = [
    ['sharlene-headshot.jpg', 30263, 'Sharlene Rose Johnson', 'sharlene-rose-johnson.jpg', 'Sharlene Rose Johnson, portrait', ['source' => 'Supplied by Nathan Imhoff on 4 October 2026, with a copy enlarged by Adobe Firefly that the archive does not use. Where the photograph was first published is not recorded with it, and permission to republish is not established.']],
];
$bad = []; $todo = [];
foreach ($P as [$file, $pid, $name, $fname, $title, $vals]) {
    $path = "$root/inventory/incoming/$file";
    if (!is_file($path)) { $bad[] = "$file missing"; continue; }
    $p = Entry::find()->id($pid)->status(null)->one(); if ($p?->title !== $name) { $bad[] = "#$pid is not $name"; continue; }
    $have = Asset::find()->filename($fname)->one(); $cur = $p->featuredImage->one();
    if ($cur && (!$have || $cur->id !== $have->id)) { $bad[] = "$name already has a portrait (#{$cur->id})"; continue; }
    [$w, $h] = getimagesize($path); $sha = hash_file('sha256', $path);
    echo "$name #$pid: " . ($have ? "#{$have->id} exists" : "import $fname ($w x $h)") . ($cur ? ', already the portrait' : ', set as the portrait') . PHP_EOL;
    $todo[] = compact('path', 'p', 'name', 'fname', 'title', 'vals', 'have', 'cur', 'sha');
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
$n = 0;
foreach ($todo as $t) {
    $a = $t['have'];
    if (!$a) {
        $tmp = sys_get_temp_dir() . '/' . $t['fname']; copy($t['path'], $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($t['fname']); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException($t['name'] . ': ' . json_encode($a->getFirstErrors())); }
        $a = Asset::find()->id($a->id)->one(); $a->title = $t['title']; $a->alt = 'Portrait of ' . $t['name'];
        $v = array_merge(['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'rightsNote' => 'No permission to republish is established.', 'sourceChecksum' => 'sha256:' . $t['sha']], $t['vals']);
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException($t['name'] . ': ' . json_encode($a->getFirstErrors())); }
    }
    if (!$t['cur']) { $p = $t['p']; $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException($t['name'] . ': ' . json_encode($p->getFirstErrors())); } }
    $n++;
}
$short = array_filter($todo, fn($t) => !Entry::find()->id($t['p']->id)->status(null)->one()->featuredImage->exists());
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', array_map(fn($t) => $t['name'], $short)) : "OK: $n portraits") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('import_johnson_portrait_2026_10_05.php', $n, $short ? 'SHORT' : 'verified', 'Sharlene Rose Johnson: portrait, the unedited original, licence unknown');
if ($short) { throw new \RuntimeException('import_portraits_2026_10_04b: read-back short'); }
