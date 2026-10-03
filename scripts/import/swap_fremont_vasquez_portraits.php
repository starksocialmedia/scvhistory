/**
 * New portraits for John C. Frémont #307 and Tiburcio Vasquez #285 (Nathan,
 * 3 October 2026: "These are real photographs I upscaled. The California State
 * Library's Frémont and the 1874 Vasquez. Import both as portraits, old ones kept
 * as related images, and record that I upscaled them").
 *
 * THE RULE (DATA-MODEL, "Edited photographs"): the archivist decides; an edited
 * photograph enters on Nathan's word with its edit recorded; the content-
 * credentials scanner reports, it never blocks. Each asset records the original as
 * Nathan gives it, marked as his statement; that he upscaled it, and when; and the
 * credential itself in contentCredentials, since the stored copy loses it.
 *
 * THE STANDING RULE FOR SWAPS (Nathan, 3 October 2026): "a replaced portrait is
 * not deleted, it stays as a related image." The old portrait (#10, #16) is added
 * to the record's recordImages before featuredImage changes.
 *
 * enhancedFrom stays empty: whether the old assets are the originals these were
 * made from is not recorded.
 *
 * Each file is checked against the SHA-256 recorded here. Licence stays unknown.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/swap_fremont_vasquez_portraits.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$M = 'https://cai-manifests.adobe.com/manifests/';
$S = [
    ['person' => 307, 'name' => 'John C. Frémont', 'old' => 10, 'file' => 'john-c-fremont-portrait-california-state-library-scaled.jpg', 'as' => 'john-c-fremont-california-state-library-upscaled.jpg',
     'sha' => 'fa6475ec2eb95cd5a5a366e56d85260c2ca5d0bcbf7794cc2c091bcc4c09763a', 'title' => 'John C. Frémont, from the California State Library\'s photograph',
     'source' => 'The California State Library\'s photograph of Frémont, upscaled by Nathan Imhoff.',
     'cc' => "Adobe content credential (C2PA), {$M}urn-c2pa-4fbb5062-5c25-414e-8c13-99536b84f15e-adobe\nSteps recorded (UTC): 2026-10-03 20:31 created (Adobe Firefly, creative upsampler; digital source type trainedAlgorithmicMedia)\nRead on 3 October 2026. A note, not a refusal: Nathan Imhoff states it is the real photograph, upscaled."],
    ['person' => 285, 'name' => 'Tiburcio Vasquez', 'old' => 16, 'file' => 'tiburcio-vasquez.jpg', 'as' => 'tiburcio-vasquez-1874-upscaled.jpg',
     'sha' => '0a9c628b49529604b1ba074c8d25407cea4724c682d28a2d5e843f02a6a20f32', 'title' => 'Tiburcio Vasquez, from the 1874 photograph',
     'source' => 'The 1874 photograph of Vasquez, upscaled by Nathan Imhoff.',
     'cc' => "Adobe content credential (C2PA), {$M}urn-c2pa-bfdaba4d-49a5-4bdc-b5cc-5ad4932513d9-adobe\nSteps recorded (UTC): 2026-10-03 20:38 opened, edited and placed (Adobe Firefly Image); 2026-10-03 20:39 created (Adobe Firefly, creative upsampler; digital source type compositeWithTrainedAlgorithmicMedia). The manifest also names a Firefly text_to_image operation among its ingredients.\nRead on 3 October 2026. A note, not a refusal: Nathan Imhoff states it is the real 1874 photograph, upscaled."],
];
$bad = [];
foreach ($S as $s) {
    $f = "$root/inventory/incoming/{$s['file']}";
    if (!is_file($f) || hash_file('sha256', $f) !== $s['sha']) { $bad[] = "{$s['file']}: missing or changed"; continue; }
    $p = Entry::find()->id($s['person'])->status(null)->one();
    if (!$p || $p->title !== $s['name']) { $bad[] = "#{$s['person']} is not {$s['name']}"; continue; }
    $have = Asset::find()->filename($s['as'])->one(); $cur = $p->featuredImage->one();
    if ($cur && $have && $cur->id === $have->id) { echo "{$s['name']}: already done (#{$have->id}); old #{$s['old']} " . (in_array($s['old'], $p->recordImages->ids()) ? 'in related images' : 'NOT in related images') . PHP_EOL; continue; }
    if (!$cur || $cur->id !== $s['old']) { $bad[] = "{$s['name']}: the current portrait is not #{$s['old']}"; continue; }
    echo "{$s['name']}: import {$s['as']}; portrait #{$s['old']} -> the new file; #{$s['old']} kept in related images; licence unknown; upscaled by Nathan Imhoff, credential recorded" . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
$n = 0; $short = [];
foreach ($S as $s) {
    $p = Entry::find()->id($s['person'])->status(null)->one();
    $a = Asset::find()->filename($s['as'])->one();
    if (!$a) {
        $tmp = sys_get_temp_dir() . '/' . $s['as']; copy("$root/inventory/incoming/{$s['file']}", $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($s['as']); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException($s['as'] . ': ' . json_encode($a->getFirstErrors())); }
        $a = Asset::find()->id($a->id)->one(); $a->title = $s['title']; $a->alt = 'Portrait of ' . $s['name'];
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields());
        $a->setFieldValues(array_intersect_key(['license' => 'unknown', 'provenanceKind' => 'commissioned', 'acquiredDate' => '2026-10-03', 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => new \DateTime('2026-10-03'),
            'enhancementMethod' => 'Upscaled (Adobe Firefly creative upsampler)', 'contentCredentials' => $s['cc'], 'source' => $s['source'], 'sourceChecksum' => 'sha256:' . $s['sha']], array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException($s['as'] . ' fields: ' . json_encode($a->getFirstErrors())); }
    }
    if ($p->featuredImage->one()?->id !== $a->id) {
        $p->setFieldValue('recordImages', array_values(array_unique(array_merge($p->recordImages->ids(), [$s['old']]))));
        $p->setFieldValue('featuredImage', [$a->id]);
        if (!$el->saveElement($p)) { throw new \RuntimeException("#{$s['person']}: " . json_encode($p->getFirstErrors())); }
    }
    $r = Entry::find()->id($s['person'])->status(null)->one();
    if ($r->featuredImage->one()?->id === $a->id && in_array($s['old'], $r->recordImages->ids()) && Asset::find()->id($s['old'])->exists()) { $n++; echo "OK {$s['name']}: portrait #{$a->id}, #{$s['old']} in related images" . PHP_EOL; } else { $short[] = $s['name']; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('swap_fremont_vasquez_portraits.php', $n, $short ? 'SHORT' : 'verified', 'Frémont and Vasquez: portraits upscaled by Nathan Imhoff, credentials recorded; old portraits kept as related images');
if ($short) { throw new \RuntimeException('swap_fremont_vasquez_portraits: ' . implode(', ', $short)); }
