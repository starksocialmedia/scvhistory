/**
 * Six portraits that Nathan Imhoff edited from real photographs with Photoshop's
 * Firefly tools: cropping, upscaling, removing a bystander, cleaning backgrounds,
 * and Generative Fill used to fill cropped edges (Nathan, 1 October 2026: "These
 * are real photographs I edited. I am the archivist and I have told you what
 * they are. Record what I say, import them, move on.").
 *
 * Each asset records the original's source as Nathan gave it, marked as his
 * statement where his word is the evidence; that he edited it, what he did, and
 * when (the last step in the file's content credential); and the credential
 * itself, in contentCredentials. The scanner's reading is a note on the record,
 * never a refusal. Where the archive holds the original, enhancedFrom points at
 * it, so the page's JSON-LD describes the original photograph.
 *
 *   Perkins   from the archive's 1964 photograph, asset #6 (enhancedFrom)
 *   Scofield  upscaled from the Commons original on his record, #21761 (enhancedFrom)
 *   Pico      from Wikimedia Commons, per Nathan; his existing portrait, asset #4,
 *             moves to his record's images and is not deleted
 *   Couts     a U.S. Army portrait, per Nathan (record #323; the request said #16023)
 *   Cooper    from his campaign site or the SCV Water board page, per Nathan
 *   Ferdman   source per Nathan Imhoff
 *
 * Each file is checked against the SHA-256 recorded here before import. Licence
 * stays unknown unless the original's is established (Scofield's, public domain).
 * Idempotent: a file already imported is not imported again. Dry run by default.
 * Set $APPLY = true to write. Needs add_content_credentials_field.php first.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_edited_portraits.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$M = 'https://cai-manifests.adobe.com/manifests/urn-c2pa-';
$cc = fn(string $urn, string $steps) => "Adobe content credential (C2PA), $M$urn-adobe\nSteps recorded (UTC): $steps\nRead by scan_content_credentials.py, 1 October 2026: edited from an original. A note, not a refusal: Adobe writes a credential for any Firefly-assisted edit and labels an upscale as algorithmic media.";
$SIX = [
    ['file' => 'arthur-b-perkins-outstanding-citizen-newhall-1964-1.jpg', 'sha' => '0b41633190cec8c60fae3de103ec0b030d080a80faa7ac2a1c292ad3b4431ec5', 'person' => 333, 'name' => 'Arthur Buckingham Perkins',
        'as' => 'arthur-b-perkins-outstanding-citizen-1964-edited.jpg', 'from' => 6, 'license' => '', 'date' => '2026-10-01',
        'source' => 'Edited by Nathan Imhoff from the archive\'s own 1964 photograph of Perkins as Outstanding Citizen (asset #6, from the WordPress media library).',
        'method' => 'Cropped, a woman\'s shoulder at the edge removed, and upscaled (Nathan Imhoff\'s description; Firefly Remove Object and upscale in the credential).',
        'cc' => $cc('f515362f-9daa-4bcf-97db-238ee040d097', '2026-10-01 04:32 edited (Firefly Image 5); 04:33 edited (Remove Object); 04:34 upscaled (Firefly creative upsampler)')],
    ['file' => 'DemetriusGScofield.jpg', 'sha' => 'b509cea6f1387a935ee66bd97a6e9110b809fa8e7c47f4db70ce1ea8d594d131', 'person' => 21584, 'name' => 'Demetrius G. Scofield',
        'as' => 'demetrius-g-scofield-1911-edited.jpg', 'from' => 21761, 'license' => 'public-domain', 'date' => '2026-09-29',
        'source' => 'Edited by Nathan Imhoff from the Wikimedia Commons original on this record (asset #21761, File:DemetriusGScofield.jpg, published 1911). Public domain, as the original is.',
        'method' => 'Upscaled, with cropped edges filled and the background cleaned (Nathan Imhoff\'s description; Firefly edit, Generative Fill and upscale in the credential).',
        'cc' => $cc('28f53699-8e67-4285-b8d5-0bbcf525b101', '2026-09-29 04:49 edited (Firefly Image 5); 04:51 edited (Generate Fill); 04:51 upscaled (Firefly creative upsampler)')],
    ['file' => 'General_Andres_Pico.jpg', 'sha' => '9622db4eab8374d8f37cdb01f2411f06fbb6e0e4d1fb5ba38d290eeb09a830f7', 'person' => 317, 'name' => 'Andrés Pico',
        'as' => 'andres-pico-commons-edited.jpg', 'from' => null, 'license' => '', 'date' => '2026-10-01',
        'source' => 'Edited by Nathan Imhoff from a photograph on Wikimedia Commons, per Nathan Imhoff; the Commons file is not recorded.',
        'method' => 'Cropped, edges filled and upscaled (Nathan Imhoff\'s description of his edits; Firefly edit and upscale in the credential).',
        'cc' => $cc('1170cf2a-2b81-4ddc-b9c3-476790bb55ba', '2026-10-01 04:17 edited (Firefly Image 5); 04:26 upscaled (Firefly creative upsampler)')],
    ['file' => 'cave-johnson-couts-portrait-us-army.png', 'sha' => '7639b6e30ab172090fcaf2561a56e3d97f157fcc2d204a85e291feed0c9fac54', 'person' => 323, 'name' => 'Cave Johnson Couts',
        'as' => 'cave-johnson-couts-us-army-edited.png', 'from' => null, 'license' => '', 'date' => '2026-10-01',
        'source' => 'Edited by Nathan Imhoff from a U.S. Army portrait, per Nathan Imhoff; where the copy came from is not recorded.',
        'method' => 'Cropped and cleaned, edges filled (Nathan Imhoff\'s description of his edits; one Firefly edit in the credential).',
        'cc' => $cc('5a9a615a-55ca-4b8b-b52d-bb2d7a90070c', '2026-10-01 08:20 edited (Firefly Image 5)')],
    ['file' => 'bill-cooper.jpg', 'sha' => '4b2bbd0853ddbdaab569aacbf5344aa1430f73d9b188a3494b5756f784172c6c', 'person' => 26946, 'name' => 'Bill Cooper',
        'as' => 'bill-cooper-edited.jpg', 'from' => null, 'license' => '', 'date' => '2026-10-01',
        'source' => 'Edited by Nathan Imhoff from a photograph on Bill Cooper\'s campaign site or the SCV Water board page, per Nathan Imhoff (campaign or agency material); the page is not recorded.',
        'method' => 'Upscaled (Nathan Imhoff\'s description; Firefly upscale of a WebP in the credential).',
        'cc' => $cc('830f4344-17b8-472b-af2f-fbf762952cd2', '2026-10-01 08:39 upscaled (Firefly creative upsampler)')],
    ['file' => 'Alan-Ferdman.jpg', 'sha' => '7331c632da5731add0b35a1a3c58e76a93efa55307c46b5e667e7321c6516394', 'person' => 25191, 'name' => 'Alan Ferdman',
        'as' => 'alan-ferdman-edited.jpg', 'from' => null, 'license' => '', 'date' => '2026-10-01',
        'source' => 'Source per Nathan Imhoff. Edited by Nathan Imhoff.',
        'method' => 'Cropped edges filled and upscaled (Nathan Imhoff\'s description of his edits; Generative Fill and upscale in the credential).',
        'cc' => $cc('5bf6bb37-c980-44e4-b601-6494f71ee16b', '2026-10-01 03:29 edited (Generate Fill); 03:29 upscaled (Firefly creative upsampler)')],
];
$bad = []; $ready = (bool)Craft::$app->getFields()->getFieldByHandle('contentCredentials');
if (!$ready) { $bad[] = 'run add_content_credentials_field.php first'; }
foreach ($SIX as $i => $s) {
    $path = "$root/inventory/incoming/{$s['file']}";
    $p = Entry::find()->id($s['person'])->status(null)->one();
    if (!$p || $p->title !== $s['name']) { $bad[] = "#{$s['person']} is not {$s['name']}"; continue; }
    if (!is_file($path) || hash_file('sha256', $path) !== $s['sha']) { $bad[] = "{$s['file']} is missing or not the file received"; continue; }
    if ($s['from'] && !Asset::find()->id($s['from'])->exists()) { $bad[] = "original asset #{$s['from']} is missing"; }
    $have = Asset::find()->filename($s['as'])->one(); $cur = $p->featuredImage->one();
    $SIX[$i]['have'] = $have;
    echo str_pad($s['file'], 58) . ($have ? "#{$have->id} exists" : 'import as outside/' . $s['as']) . "; #{$s['person']} {$s['name']}: portrait " . ($cur && $have && $cur->id === $have->id ? 'already this' : ($cur ? "#{$cur->id} -> this" : 'none -> this')) . ($s['from'] ? "; enhancedFrom #{$s['from']}" : '') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($SIX as $s) {
        $a = $s['have'];
        if (!$a) {
            $tmp = sys_get_temp_dir() . '/' . $s['as']; copy("$root/inventory/incoming/{$s['file']}", $tmp);
            $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($s['as']); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
            /* Fields are set before the one save: a second save keeps the create scenario and fails on the moved temp file. */
            $a->setFieldValues(['license' => $s['license'], 'provenanceKind' => 'commissioned', 'acquiredDate' => $s['date'], 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => new \DateTime($s['date']),
                'enhancementMethod' => $s['method'], 'contentCredentials' => $s['cc'], 'enhancedFrom' => $s['from'] ? [$s['from']] : [],
                'source' => mb_substr($s['source'] . ' Received as ' . $s['file'] . ', SHA-256 ' . $s['sha'] . '; the stored copy is re-encoded on import.', 0, 500)]);
            if (!$elements->saveElement($a)) { throw new \RuntimeException($s['file'] . ': ' . json_encode($a->getFirstErrors())); }
        }
        $p = Entry::find()->id($s['person'])->status(null)->one(); $cur = $p->featuredImage->one();
        if ($cur && $cur->id === $a->id) { continue; }
        $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
        $p->setFieldValue('featuredImage', [$a->id]);
        /* A replaced portrait that is not the edit's own original stays on the record, among its images. */
        /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
        if ($cur && $cur->id !== $s['from'] && in_array('recordImages', $h)) { $p->setFieldValue('recordImages', array_values(array_unique(array_merge($p->recordImages->status(null)->ids(), [$cur->id])))); }
        if (!$elements->saveElement($p)) { throw new \RuntimeException("#{$s['person']}: " . json_encode($p->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$short = [];
foreach ($SIX as $s) {
    $a = Asset::find()->filename($s['as'])->one(); $p = Entry::find()->id($s['person'])->status(null)->one();
    if (!$a || $p->featuredImage->one()?->id !== $a->id || trim((string)$a->contentCredentials) === '' || $a->enhancedBy !== 'Nathan Imhoff') { $short[] = $s['name']; }
    if ($s['from'] && $a && $a->enhancedFrom->one()?->id !== $s['from']) { $short[] = $s['name'] . ' original'; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: six portraits, each with its edit and credential recorded') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('import_edited_portraits.php', count($SIX), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'six portraits edited by Nathan Imhoff, sources as he gave them');
if ($short) { throw new \RuntimeException('import_edited_portraits: ' . implode('; ', $short)); }
