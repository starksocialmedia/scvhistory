/**
 * Cameron Smyth #16380: his portrait, as featuredImage.
 *
 * THE IMAGE AND ITS RIGHTS (Nathan, 29 September 2026). Nathan Imhoff took the
 * photograph, holds the copyright, and licenses it to the archive. So: creator
 * and rightsHolder Nathan Imhoff, licence scvhistory, provenanceKind outside,
 * acquiredDate the day it was received, in the site's timezone (America/Los_Angeles,
 * as Craft records it; it was already 29 September on Nathan's Mac). The file carries no date taken (no
 * EXIF at all, only a JFIF header), so dateAsPrinted and dateEdtf stay empty
 * until Nathan supplies one; nothing is guessed.
 *
 * Provenance is recorded at the point of receipt, because Craft re-encodes an
 * image on import and the stored copy will not be this file: the source field
 * names the file as received and its SHA-256, and the script refuses any other
 * file.
 *
 * The file was scanned for content credentials and generative-AI markers
 * before import (scan_content_credentials.py): none.
 *
 * Maria Gutzeit's file in inventory/incoming is not touched: it is campaign
 * material, the rights are not established, and it waits for her permission.
 *
 * Idempotent: an asset with this filename is found and not imported twice, and
 * a featuredImage someone already set is never replaced.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_smyth_portrait.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$PERSON = 16380;
$TITLE = 'Cameron Smyth';
$FILE = \Craft::getAlias('@root') . '/inventory/incoming/Smyth.jpg';
$SHA256 = '1d9f787a6c5d15f08f399f29d91cb99c49af224615015e67bcab8cf3ce6ae212';
$FILENAME = 'cameron-smyth-nathan-imhoff.jpg';
$FOLDER = 'outside';
$ASSET = [
    'title' => 'Cameron Smyth',
    'alt' => 'Portrait of Cameron Smyth',
    'fields' => [
        'creator' => 'Nathan Imhoff',
        'rightsHolder' => 'Nathan Imhoff',
        'license' => 'scvhistory',
        'provenanceKind' => 'outside',
        'acquiredDate' => '2026-09-28',
        'source' => 'Photograph by Nathan Imhoff, who holds the copyright and licenses it to SCVHistory. '
            . 'Received from him on 28 September 2026 as Smyth.jpg, SHA-256 ' . '1d9f787a6c5d15f08f399f29d91cb99c49af224615015e67bcab8cf3ce6ae212'
            . '; the stored copy is re-encoded on import and differs from the file as received.',
    ],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();

/* ------------------------------------------------ the person */
$p = \craft\elements\Entry::find()->id($PERSON)->status(null)->one();
if (!$p || $p->title !== $TITLE || $p->section->handle !== 'persons') { echo 'REFUSING: #' . $PERSON . ' is not the persons record ' . $TITLE . PHP_EOL; return; }
$has = [];
foreach ($p->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
if (!isset($has['featuredImage'])) { echo 'REFUSING: the person layout lacks featuredImage' . PHP_EOL; return; }
$current = $p->featuredImage->one();
if ($current && $current->filename === $FILENAME) { echo 'already done: #' . $PERSON . ' has the portrait (asset #' . $current->id . '). Nothing to write.' . PHP_EOL; return; }
if ($current) { echo 'REFUSING: featuredImage is already set to another asset (#' . $current->id . ' ' . $current->filename . '); a value someone set is never replaced' . PHP_EOL; return; }
$inbound = (int)\craft\elements\Entry::find()->status(null)->relatedTo(['targetElement' => $PERSON])->count();
echo 'person #' . $PERSON . ' ' . $p->title . ': featuredImage (empty) -> the portrait; inbound links, unchanged: ' . $inbound . PHP_EOL;

/* ------------------------------------------------ the file */
if (!is_file($FILE)) { echo 'REFUSING: ' . $FILE . ' not found' . PHP_EOL; return; }
$sha = hash_file('sha256', $FILE);
echo PHP_EOL . 'file ' . basename($FILE) . ': ' . filesize($FILE) . ' bytes, sha256 ' . $sha . ($sha === $SHA256 ? ' (the file received)' : ' DOES NOT MATCH ' . $SHA256) . PHP_EOL;
if ($sha !== $SHA256) { echo 'REFUSING: not the file whose rights were established' . PHP_EOL; return; }
[$w, $h] = getimagesize($FILE);
echo '   ' . $w . ' x ' . $h . PHP_EOL;

$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$layout = $volume->getFieldLayout();
$absent = array_values(array_filter(array_keys($ASSET['fields']), fn($f) => !$layout->getFieldByHandle($f)));
if ($absent) { echo 'REFUSING: the asset layout lacks ' . implode(', ', $absent) . PHP_EOL; return; }
$lic = array_map(fn($o) => $o['value'], Craft::$app->getFields()->getFieldByHandle('license')->options);
if (!in_array($ASSET['fields']['license'], $lic, true)) { echo 'REFUSING: license has no option ' . $ASSET['fields']['license'] . PHP_EOL; return; }
$existing = \craft\elements\Asset::find()->volumeId($volume->id)->filename($FILENAME)->one();
$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => $FOLDER . '/']);
if (!$folder) { echo 'REFUSING: folder ' . $FOLDER . '/ does not exist' . PHP_EOL; return; }
echo '   asset: ' . ($existing ? 'already held as #' . $existing->id : 'import as archiveMedia/' . $FOLDER . '/' . $FILENAME) . PHP_EOL;
echo '   ' . str_pad('title', 16) . $ASSET['title'] . PHP_EOL . '   ' . str_pad('alt', 16) . $ASSET['alt'] . PHP_EOL;
foreach ($ASSET['fields'] as $k => $v) { echo '   ' . str_pad($k, 16) . $v . PHP_EOL; }
echo '   ' . str_pad('dateEdtf', 16) . '(empty: no date taken is known; Nathan can supply one)' . PHP_EOL;
echo '   ' . str_pad('legacySourcePath', 16) . '(empty: not from the mirror)' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* ------------------------------------------------ apply */
$asset = $existing;
if (!$asset) {
    $tmp = sys_get_temp_dir() . '/' . $FILENAME;
    if (!copy($FILE, $tmp)) { throw new \RuntimeException('import_smyth_portrait: could not stage the file'); }
    $asset = new \craft\elements\Asset();
    $asset->tempFilePath = $tmp;
    $asset->setFilename($FILENAME);
    $asset->newFolderId = $folder->id;
    $asset->setVolumeId($volume->id);
    $asset->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
    $asset->avoidFilenameConflicts = false;
    if (!$elements->saveElement($asset)) { throw new \RuntimeException('import_smyth_portrait: asset ' . json_encode($asset->getFirstErrors())); }
}
/* Reloaded before the fields are set: a create-scenario asset refuses a second save. */
$asset = \craft\elements\Asset::find()->id($asset->id)->one();
$asset->title = $ASSET['title'];
$asset->alt = $ASSET['alt'];
$asset->setFieldValues($ASSET['fields']);
if (!$elements->saveElement($asset)) { throw new \RuntimeException('import_smyth_portrait: asset fields ' . json_encode($asset->getFirstErrors())); }

$p = \craft\elements\Entry::find()->id($PERSON)->status(null)->one();
$p->setFieldValue('featuredImage', [$asset->id]);
if (!$elements->saveElement($p)) { throw new \RuntimeException('import_smyth_portrait: person ' . json_encode($p->getFirstErrors())); }

/* ------------------------------------------------ read back */
$short = [];
$a = \craft\elements\Asset::find()->id($asset->id)->one();
if (!$a) { $short[] = 'asset missing'; }
else {
    foreach ($ASSET['fields'] as $k => $v) {
        $got = $a->getFieldValue($k);
        $got = is_object($got) && property_exists($got, 'value') ? (string)$got->value : trim((string)$got);
        if ($got !== $v) { $short[] = "asset $k reads \"" . mb_substr($got, 0, 40) . '"'; }
    }
    if (trim((string)$a->getFieldValue('legacySourcePath')) !== '') { $short[] = 'asset legacySourcePath is set; it must be empty'; }
}
$b = \craft\elements\Entry::find()->id($PERSON)->status(null)->one();
if (($b->featuredImage->one()->id ?? null) !== $asset->id) { $short[] = 'featuredImage is not the portrait'; }
$after = (int)\craft\elements\Entry::find()->status(null)->relatedTo(['targetElement' => $PERSON])->count();
if ($after !== $inbound) { $short[] = "inbound reads $after, expected $inbound"; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: portrait #' . $asset->id . ' with its rights, featured on #' . $PERSON . '; ' . $inbound . ' inbound intact') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_smyth_portrait.php', 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified: portrait and featuredImage',
    'asset #' . $asset->id . ' by Nathan Imhoff, licence scvhistory, outside; featured on #16380 Cameron Smyth');
if ($short) { throw new \RuntimeException('import_smyth_portrait: ' . implode('; ', $short)); }
