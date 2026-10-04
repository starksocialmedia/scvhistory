/**
 * Portraits of the Hart district's four sitting trustees without one and of its superintendent, from
 * the district's own website (Nathan, 4 October 2026: "all nine came from the bodies' own websites, at
 * larger sizes than the 100x150 thumbnails. Record them as retrieved from each body's site on 4
 * October 2026"). The district publishes them; no permission to republish is established, so the
 * licence stays unknown, on the Trunkey and Ahuja precedent.
 *
 *   inventory/incoming/Erin-Wilson.jpg          Erin Wilson #28560      the board page
 *   inventory/incoming/Cherise-Moore.jpg        Cherise Moore #28558    the board page
 *   inventory/incoming/Bob-Jenson.jpg           Bob Jensen #28322       the board page (the file
 *                                               name misspells him; the district prints Jensen)
 *   inventory/incoming/Joe-Messina.jpg          Joe Messina #26549      the board page
 *   inventory/incoming/michael_vierra_2025.jpg  Michael Vierra          the superintendent's page
 * Each file's SHA-256 is checked before use and recorded in sourceChecksum, not in the public
 * sentence. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_hart_portraits_2026_10_04.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$BOARD = 'https://www.hartdistrict.org/apps/pages/governing-board-members';
$SUPT = 'https://www.hartdistrict.org/apps/pages/superintendent';
$vierra = Entry::find()->section('persons')->status(null)->title('Michael Vierra')->one();
$P = [
    ['Erin-Wilson.jpg', 'c029439ff3ed63aa', 28560, 'Erin Wilson', 'erin-wilson-hart-district.jpg', $BOARD, 'board page'],
    ['Cherise-Moore.jpg', '67120a7ef1d6fde0', 28558, 'Cherise Moore', 'cherise-moore-hart-district.jpg', $BOARD, 'board page'],
    ['Bob-Jenson.jpg', 'faf5a7dfda3df225', 28322, 'Bob Jensen', 'bob-jensen-hart-district.jpg', $BOARD, 'board page'],
    ['Joe-Messina.jpg', '9ae5eab53112befe', 26549, 'Joe Messina', 'joe-messina-hart-district.jpg', $BOARD, 'board page'],
    ['michael_vierra_2025.jpg', 'f83eb96bccd21035', $vierra?->id, 'Michael Vierra', 'michael-vierra-hart-district.jpg', $SUPT, 'superintendent\'s page'],
];
$bad = []; $todo = [];
foreach ($P as [$file, $shaStart, $pid, $name, $fname, $url, $page]) {
    $path = "$root/inventory/incoming/$file"; $sha = is_file($path) ? hash_file('sha256', $path) : '';
    if (!$sha || !str_starts_with($sha, $shaStart)) { $bad[] = "$file is missing or changed"; continue; }
    $p = $pid ? Entry::find()->id($pid)->status(null)->one() : null;
    if (!$p || $p->title !== $name) { $bad[] = "$name's record is not where expected"; continue; }
    $have = Asset::find()->filename($fname)->one(); $cur = $p->featuredImage->one();
    if ($cur && (!$have || $cur->id !== $have->id)) { $bad[] = "$name already has a portrait (#{$cur->id})"; continue; }
    [$w, $h] = getimagesize($path);
    echo "$name #{$p->id}: " . ($have ? "#{$have->id} exists" : "import $fname ({$w} x {$h})") . ($cur ? ', already the portrait' : ', set as the portrait') . " ; from the district's $page" . PHP_EOL;
    $todo[] = compact('path', 'sha', 'p', 'name', 'fname', 'url', 'page', 'have', 'cur');
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
        $a = Asset::find()->id($a->id)->one();
        $a->title = $t['name'] . ', William S. Hart Union High School District portrait'; $a->alt = 'Portrait of ' . $t['name'];
        $vals = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'creditName' => 'William S. Hart Union High School District',
            'rightsNote' => 'An official portrait the William S. Hart Union High School District publishes on its website. No permission to republish it is established.',
            'source' => 'William S. Hart Union High School District, its ' . $t['page'] . ', ' . $t['url'] . ', retrieved 4 October 2026, at a larger size than the page displays. Permission to republish is not established.',
            'sourceChecksum' => 'sha256:' . $t['sha']];
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($vals, array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException($t['name'] . ': ' . json_encode($a->getFirstErrors())); }
    }
    if (!$t['cur']) { $p = $t['p']; $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException($t['name'] . ': ' . json_encode($p->getFirstErrors())); } }
    $n++;
}
$short = array_filter($todo, fn($t) => !Entry::find()->id($t['p']->id)->status(null)->one()->featuredImage->exists());
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', array_map(fn($t) => $t['name'], $short)) : "OK: $n portraits") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('import_hart_portraits_2026_10_04.php', $n, $short ? 'SHORT' : 'verified', 'Wilson, Moore, Jensen, Messina, Vierra: the Hart district\'s own portraits, licence unknown');
if ($short) { throw new \RuntimeException('import_hart_portraits: read-back short'); }
