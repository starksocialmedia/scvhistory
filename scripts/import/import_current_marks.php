/**
 * Current marks (logos) for seven bodies, fetched from their own websites on
 * 3 October 2026 (Nathan: "Fetch the five school district logos from the
 * districts' own websites ... Then do the same for the other bodies that have
 * one"; "Yes to the separate currentMark field").
 *
 * Each is held as what it is: assetRole current-mark (not an archive record),
 * provenanceKind outside (where it came from), rightsHolder the body, license
 * identifying-use. The source sentence gives the page it appears on, the
 * retrieval date and the date the body's server gives the file. The file as
 * received is in inventory/marks/, with its SHA-256 and size in marks.json;
 * this script refuses any file that no longer matches.
 *
 *   Hart district  the colour version for light backgrounds from the
 *                  district's branding page. Its site restricts use of the
 *                  logo; rightsNote says so and on what footing it is shown
 *                  (Nathan: "Record that their site restricts use and that we
 *                  judged identification acceptable").
 *   SCV Historical Society  the Society's site shows the mark of the Santa
 *                  Clarita History Center, its museum at Heritage Junction; it
 *                  is captioned as the History Center's mark (Nathan, same day).
 *   Skipped: the Chamber (its only file is the 2023 centennial variant) and the
 *   City (its current mark is undecided; asset #41, the City's seal from the
 *   WordPress library, is a separate question).
 * Set on each record's currentMark, never its featured image.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_current_marks.php'))"
 */

use craft\elements\Asset;
use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $DIR = "$root/inventory/marks"; $FOLDER = 'marks';
$M = json_decode(file_get_contents("$DIR/marks.json"), true)['marks'];
$assets = Craft::$app->getAssets(); $els = Craft::$app->getElements();
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$HART_NOTE = 'The district\'s website states that its names and logos may be used only for official district business and as the district authorizes. The archive shows the logo only to identify the district on the district\'s own record, as a reference work does; it implies no endorsement.';
$bad = []; $plan = [];
foreach ($M as $m) {
    $f = "$DIR/{$m['file']}";
    if (!is_file($f) || hash_file('sha256', $f) !== $m['sha256']) { $bad[] = "{$m['file']}: missing or not the file as received"; continue; }
    $rec = Entry::find()->id($m['record'])->status(null)->one();
    if (!$rec || $rec->section->handle !== 'organizations') { $bad[] = "{$m['file']}: #{$m['record']} is not an organization"; continue; }
    $isHC = str_starts_with($m['file'], 'santa-clarita-history-center');
    $title = $isHC ? 'Logo of the Santa Clarita History Center' : 'Logo of the ' . $rec->title;
    $alt = $isHC ? 'Logo of the Santa Clarita History Center, the Santa Clarita Valley Historical Society\'s museum at Heritage Junction' : $title;
    $source = ($isHC
        ? 'The logo of the Santa Clarita History Center, the Society\'s museum at Heritage Junction, as shown on the Society\'s website'
        : 'The ' . preg_replace('~^The ~', '', $rec->title) . '\'s logo as shown on ' . $m['pageWords'])
        . ', ' . $m['page'] . ', retrieved on 3 October 2026; the file is dated ' . $m['serverLastModifiedWords'] . ' on the site.';
    $fields = ['assetRole' => 'current-mark', 'provenanceKind' => 'outside', 'sourceUrl' => $m['page'], 'acquiredDate' => $m['retrieved'], 'source' => $source,
        'sourceChecksum' => 'sha256:' . $m['sha256'], 'rightsHolder' => $m['body'], 'license' => 'identifying-use',
        'rightsNote' => str_starts_with($m['file'], 'hart-') ? $HART_NOTE : ''];
    $existing = Asset::find()->volumeId($volume->id)->filename($m['file'])->one();
    $held = $rec->currentMark->one();
    $plan[] = compact('m', 'rec', 'title', 'alt', 'fields', 'existing', 'held');
    echo str_pad($rec->title, 44) . ($existing ? "asset #{$existing->id}" : 'import') . '; currentMark ' . ($held ? ($existing && $held->id === $existing->id ? 'set' : "HOLDS #{$held->id}") : 'to set') . "; {$m['width']}x{$m['height']}" . ($m['small'] ? ' (small)' : '') . PHP_EOL;
    if ($held && (!$existing || $held->id !== $existing->id)) { $bad[] = "{$rec->title}: currentMark already holds another asset"; }
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => "$FOLDER/"]);
if (!$folder) {
    $rootF = $assets->getRootFolderByVolumeId($volume->id);
    $assets->createFolder(new \craft\models\VolumeFolder(['parentId' => $rootF->id, 'name' => $FOLDER, 'volumeId' => $volume->id, 'path' => "$FOLDER/"]));
    $folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => "$FOLDER/"]);
}
$short = [];
foreach ($plan as $p) {
    $a = $p['existing'];
    if (!$a) {
        $tmp = sys_get_temp_dir() . '/' . $p['m']['file']; copy("$DIR/{$p['m']['file']}", $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($p['m']['file']); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id);
        $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$els->saveElement($a)) { $short[] = $p['m']['file'] . ' ' . json_encode($a->getFirstErrors()); continue; }
        $a = Asset::find()->id($a->id)->one();
    }
    $a->title = $p['title']; $a->alt = $p['alt']; $a->setFieldValues($p['fields']);
    if (!$els->saveElement($a)) { $short[] = $p['m']['file'] . ' fields ' . json_encode($a->getFirstErrors()); continue; }
    $r = $p['rec']; $r->setFieldValue('currentMark', [$a->id]);
    if (!$els->saveElement($r)) { $short[] = $r->title . ' ' . json_encode($r->getFirstErrors()); }
}
$n = 0;
foreach ($plan as $p) {
    $r = Entry::find()->id($p['rec']->id)->status(null)->one(); $a = $r->currentMark->one();
    $ok = $a && $a->filename === $p['m']['file'] && ($a->assetRole->value ?? '') === 'current-mark' && ($a->license->value ?? '') === 'identifying-use' && (string)$a->sourceChecksum === 'sha256:' . $p['m']['sha256'];
    $ok ? $n++ : $short[] = $r->title;
    echo ($ok ? 'OK    ' : 'SHORT ') . $r->title . ($a ? " -> asset #{$a->id}" : '') . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n marks") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('import_current_marks.php', $n, $short ? 'SHORT' : 'verified', 'current marks for 5 school districts, SCV Water and the SCV Historical Society (History Center mark), on currentMark');
if ($short) { throw new \RuntimeException('import_current_marks: ' . implode(', ', $short)); }
