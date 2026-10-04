/**
 * Four portraits waiting in inventory/incoming (Nathan, 4 October 2026: "brianwalters.png has been in
 * inventory/incoming since yesterday and his portrait still is not on his record ... Check and fix it";
 * "Anna Griese's portrait is in inventory/incoming"; Patti Rasmussen, "Patti-Rasmussen.jpg is in
 * inventory/incoming"; George Runner, "his empty record gives the portrait somewhere to go", from the
 * bodies' own websites).
 *
 * Walters's profile was applied by build_walters_profile.php on 3 October, which had no portrait step,
 * so the file was never imported: missed, not failed.
 *
 *   brianwalters.png      Brian Walters #25391    supplied by Nathan Imhoff; no source recorded with it
 *   anna-griese.jpg       Anna Griese #28314      photograph by Lindsay Schlick of SchlickArt, 25 April
 *                                                 2022, by the file's own notice
 *   Patti-Rasmussen.jpg   Patti Rasmussen #2591   supplied by Nathan Imhoff; no source recorded with it
 *   George-Runner.jpg     George Runner #18747    from the website of a body he served, retrieved 4
 *                                                 October 2026 (Nathan Imhoff); the page not recorded
 * Licence unknown for all four: no permission to republish is established.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_portraits_2026_10_04b.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$P = [
    ['brianwalters.png', 25391, 'Brian Walters', 'brian-walters.png', 'Brian Walters, portrait', ['source' => 'Supplied by Nathan Imhoff on 3 October 2026. Where the photograph was first published is not recorded with it, and permission to republish is not established.']],
    ['anna-griese.jpg', 28314, 'Anna Griese', 'anna-griese-schlickart-2022.jpg', 'Anna Griese, portrait by Lindsay Schlick, 2022', ['source' => 'Supplied by Nathan Imhoff on 4 October 2026. A photograph by Lindsay Schlick of SchlickArt, taken 25 April 2022, as the file\'s own notice records. Permission to republish is not established.', 'rightsHolder' => 'Lindsay Schlick (SchlickArt)', 'creditName' => 'Lindsay Schlick, SchlickArt', 'rightsNote' => 'Photograph by Lindsay Schlick of SchlickArt, who holds the copyright by the file\'s own notice. No permission to republish is established.']],
    ['Patti-Rasmussen.jpg', 2591, 'Patti Rasmussen', 'patti-rasmussen.jpg', 'Patti Rasmussen, portrait', ['source' => 'Supplied by Nathan Imhoff on 4 October 2026. Where the photograph was first published is not recorded with it, and permission to republish is not established.']],
    ['George-Runner.jpg', 18747, 'George Runner', 'george-runner.jpg', 'George Runner, official portrait', ['source' => 'From the website of a public body he served, retrieved 4 October 2026 (Nathan Imhoff); which page is not recorded. Permission to republish is not established.']],
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
$applyLog('import_portraits_2026_10_04b.php', $n, $short ? 'SHORT' : 'verified', 'Walters (missed on 3 October), Griese, Rasmussen, George Runner: portraits, licence unknown');
if ($short) { throw new \RuntimeException('import_portraits_2026_10_04b: read-back short'); }
