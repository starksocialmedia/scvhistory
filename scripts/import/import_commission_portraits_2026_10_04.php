/**
 * Two commissioners' portraits from the City's Arts Commission page (Nathan, 4 October 2026: "Take the
 * members, terms and offices from these directly, and the portraits too. Record each as retrieved 4
 * October 2026"). Saved in inventory/news/city-commissions-2026-10-04/portraits/.
 *   638163769957930000.jpeg                     Susan Shapiro   597 x 747, the full file behind the page's thumbnail
 *   Arts-Commissioner-Jeri-Seratti-1-scaled.jpg  Jeri Seratti    1441 x 2560
 * Tim Burkhart's and Lisa Eichman's photographs on the Planning Commission page are 150 and 120 pixels
 * wide, too small for a portrait; they are left for Nathan to collect. Licence unknown: no permission to
 * republish is established. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_commission_portraits_2026_10_04.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$SRC = 'City of Santa Clarita, "Arts Commission," https://santaclarita.gov/commission-information/arts-commission/, retrieved 4 October 2026. Permission to republish is not established.';
$sh = Entry::find()->section('persons')->status(null)->title('Susan Shapiro')->one(); $se = Entry::find()->section('persons')->status(null)->title('Jeri Seratti')->one();
$P = [
    ['../news/city-commissions-2026-10-04/portraits/638163769957930000.jpeg', $sh?->id, 'Susan Shapiro', 'susan-shapiro-city-arts-commission.jpg', 'Susan Shapiro, City of Santa Clarita portrait', ['source' => $SRC, 'creditName' => 'City of Santa Clarita']],
    ['../news/city-commissions-2026-10-04/portraits/Arts-Commissioner-Jeri-Seratti-1-scaled.jpg', $se?->id, 'Jeri Seratti', 'jeri-seratti-city-arts-commission.jpg', 'Jeri Seratti, City of Santa Clarita portrait', ['source' => $SRC, 'creditName' => 'City of Santa Clarita']],
];
$bad = []; $todo = [];
foreach ($P as [$file, $pid, $name, $fname, $title, $vals]) {
    $path = "$root/inventory/incoming/$file";
    if (!is_file($path)) { $bad[] = "$file missing"; continue; }
    $p = $pid ? Entry::find()->id($pid)->status(null)->one() : null; if ($p?->title !== $name) { $bad[] = "#$pid is not $name"; continue; }
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
$applyLog('import_commission_portraits_2026_10_04.php', $n, $short ? 'SHORT' : 'verified', 'Shapiro and Seratti: the City\'s Arts Commission portraits, licence unknown');
if ($short) { throw new \RuntimeException('import_commission_portraits: read-back short'); }
