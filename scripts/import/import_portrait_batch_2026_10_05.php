/**
 * The portrait batch of 5 October 2026: Nathan's rulings on the portrait census
 * (inventory/review/portrait-census-2026-10-05.md). "Import as a batch, with provenance for each": the clear matches
 * from the legacy mirror, the links to images already in Craft, two crops kept with their originals, a source fault
 * for Pavelka's misprinted caption, and the "Likeness" note for Chico López.
 *
 * Every item is in inventory/review/portrait-batch-2026-10-05.json, which this script only reads:
 *   import         a file copied unchanged from the mirror into inventory/incoming; becomes an archiveMedia/legacy/ asset
 *                  with its provenance (source page, caption and credit as printed, legacy code, sha256 of the file as
 *                  taken, acquired 5 October 2026, licence unknown) and goes into the record's featuredImage (the field
 *                  the person, fallen officer and war memorial templates read) or recordImages.
 *   link           an asset already in Craft (census: Dean #14804, Lyon #2316, Hon #14933, Connie Worden-Roberts
 *                  #30549, Ruth Newhall's #11344, Ward's #1973); empty provenance fields are filled, never overwritten.
 *   import-edited  a crop made by the archive on 5 October 2026 for Nathan Imhoff (Ward, from the double portrait with
 *                  his wife; Bowman, from her obituary clipping), recorded as an edited image: enhancedFrom names the
 *                  original, which is kept (Nathan, 4 October 2026: "keep both and record which is which").
 * Pavelka: the caption prints an end of watch in 2013; he died in 2003. The caption is not carried onto the image; the
 * source fault records it. Chico López: the note is held when the data file says so (his own legacy page is a portrait).
 *
 * Refuses the whole run on any mismatch: a record title not as expected, a file missing or not the file taken (sha256),
 * pixel size changed, a stored filename already in Craft without this checksum, a value over its field's limit, or a
 * record that already has a different portrait (never overwritten).
 * Idempotent: an asset is found by its sourceChecksum, a relation already set is skipped, the fault and note are found
 * before they are made. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_portrait_batch_2026_10_05.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fields = Craft::$app->getFields();
$D = json_decode((string)file_get_contents("$root/inventory/review/portrait-batch-2026-10-05.json"), true);
if (!$D || empty($D['items'])) { throw new \RuntimeException('portrait-batch-2026-10-05.json missing or unreadable'); }
$IN = "$root/inventory/incoming";
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$assetHandles = array_map(fn($f) => $f->handle, $vol->getFieldLayout()->getCustomFields());
$val = function ($el, $h) { try { $v = $el->getFieldValue($h); } catch (\Throwable $t) { return ''; }
    if ($v instanceof \craft\fields\data\SingleOptionFieldData) { return (string)$v->value; }
    if ($v instanceof \DateTime) { return $v->format('Y-m-d'); }
    if (is_object($v) && method_exists($v, 'ids')) { return implode(',', $v->status(null)->ids()); }
    return is_scalar($v) ? (string)$v : ''; };
$has = function ($el, $h) { foreach ($el->getFieldLayout()->getCustomFields() as $f) { if ($f->handle === $h) { return true; } } return false; };
$bad = []; $plan = []; $sheet = []; $byKey = []; $n = ['import' => 0, 'import-edited' => 0, 'link' => 0, 'already' => 0, 'fill' => 0];

/* Check every value against its field: limit, dropdown option, the field being on the asset layout. */
$checkValues = function (string $key, array $v) use ($fields, $assetHandles, &$bad) {
    foreach ($v as $h => $x) {
        if (!in_array($h, $assetHandles, true)) { $bad[] = "$key: $h is not an archiveMedia field"; continue; }
        $f = $fields->getFieldByHandle($h);
        if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen((string)$x) > $f->charLimit) { $bad[] = "$key: $h is " . mb_strlen((string)$x) . " characters, over its limit of {$f->charLimit}"; }
        if ($f instanceof \craft\fields\Dropdown && !in_array($x, array_column($f->options, 'value'), true)) { $bad[] = "$key: $h \"$x\" is not an option"; }
    }
};

$records = [];
foreach ($D['items'] as $it) {
    $k = $it['key']; $byKey[$k] = $it;
    $r = $records[$it['record']] ?? Entry::find()->id($it['record'])->status(null)->one();
    if (!$r || $r->title !== $it['expectTitle'] || $r->section->handle !== $it['section']) { $bad[] = "$k: #{$it['record']} is not {$it['expectTitle']} in {$it['section']} (found: " . ($r ? "{$r->title}, {$r->section->handle}" : 'nothing') . ')'; continue; }
    $records[$it['record']] = $r;
    foreach ($it['expect'] ?? [] as $h => $sub) { if (!$has($r, $h) || !str_contains($val($r, $h), $sub)) { $bad[] = "$k: {$it['expectTitle']}'s $h does not contain \"$sub\" (the wrong man?)"; } }
    if (!$has($r, $it['field'])) { $bad[] = "$k: {$r->section->handle} has no {$it['field']} field"; continue; }
    $a = null; $what = '';
    if ($it['action'] === 'link') {
        $a = Asset::find()->id($it['assetId'])->one();
        if (!$a || $a->filename !== $it['assetFilename']) { $bad[] = "$k: asset #{$it['assetId']} is not {$it['assetFilename']}" . ($a ? " (it is {$a->filename})" : ' (missing)'); continue; }
        $fill = [];
        foreach ($it['fillIfEmpty'] ?? [] as $h => $x) { if (trim($val($a, $h)) === '') { $fill[$h] = $x; } }
        if ($it['alt'] && trim((string)$a->alt) === '') { $fill['alt'] = $it['alt']; }
        $checkValues($k, array_diff_key($fill, ['alt' => 1]));
        $what = "link #{$a->id} {$a->filename} ({$a->width} x {$a->height})" . ($fill ? '; fill empty: ' . implode(', ', array_keys($fill)) : '; nothing to fill');
        $n['link']++; if ($fill) { $n['fill']++; }
        $plan[$k] = ['it' => $it, 'asset' => $a, 'fill' => $fill];
    } else {
        $path = "$IN/{$it['file']}";
        if (!is_file($path)) { $bad[] = "$k: inventory/incoming/{$it['file']} is missing"; continue; }
        $sha = hash_file('sha256', $path);
        if ($sha !== $it['sha256']) { $bad[] = "$k: {$it['file']} is not the file taken (sha256 $sha, expected {$it['sha256']})"; continue; }
        [$w, $h] = getimagesize($path);
        if ($w !== $it['width'] || $h !== $it['height']) { $bad[] = "$k: {$it['file']} is $w x $h, expected {$it['width']} x {$it['height']}"; }
        $a = Asset::find()->sourceChecksum('sha256:' . $sha)->one();
        if (!$a) {
            /* Not held by checksum: refuse if the stored name, or the mirror twin (with or without _large), is already in Craft. */
            $stem = preg_replace('~(_large)?\.(jpe?g|png)$~i', '', $it['assetFilename']);
            $twins = Asset::find()->filename([$it['assetFilename'], "$stem.jpg", "{$stem}_large.jpg", "$stem.png"])->all();
            foreach ($twins as $t) { $bad[] = "$k: {$t->filename} is already in Craft as #{$t->id} without this file's checksum: link it or say why not"; }
        }
        if (isset($it['enhancedFromAssetId']) && !Asset::find()->id($it['enhancedFromAssetId'])->exists()) { $bad[] = "$k: original asset #{$it['enhancedFromAssetId']} is missing"; }
        if (isset($it['enhancedFromKey']) && !isset($byKey[$it['enhancedFromKey']])) { $bad[] = "$k: original {$it['enhancedFromKey']} must come earlier in the data file"; }
        $checkValues($k, $it['fields']);
        $what = ($a ? "#{$a->id} {$a->filename} already imported" : "import as legacy/{$it['assetFilename']}") . " ($w x $h, " . number_format($it['bytes']) . ' bytes)';
        $n[$a ? 'already' : $it['action']]++;
        $plan[$k] = ['it' => $it, 'asset' => $a, 'path' => $path];
    }
    /* The record's field: featuredImage is never overwritten; recordImages is appended to. */
    $cur = $r->getFieldValue($it['field'])->status(null)->ids();
    $mine = $a ? $a->id : null;
    if ($it['field'] === 'featuredImage') {
        if ($cur && (!$mine || !in_array($mine, $cur))) { $bad[] = "$k: {$it['expectTitle']} already has a portrait (#" . implode(',', $cur) . '); refused, never overwritten'; }
        $set = $cur && $mine && in_array($mine, $cur) ? 'already the portrait' : 'set as the portrait (featuredImage)';
    } else {
        $set = $mine && in_array($mine, $cur) ? 'already among record images' : 'added to recordImages';
    }
    $plan[$k]['set'] = $set;
    echo str_pad("#{$it['record']} {$it['expectTitle']}", 46) . " $what; $set" . PHP_EOL;
    $sheet[] = '| ' . implode(' | ', array_map(fn($s) => str_replace('|', '/', (string)$s), [
        "#{$it['record']} {$it['expectTitle']}",
        $it['action'] === 'link' ? "asset #{$it['assetId']} `{$it['assetFilename']}`" : "`{$it['file']}`" . ($it['mirrorPath'] ? " (mirror `{$it['mirrorPath']}`)" : ''),
        $it['action'] === 'link' ? ($a ? "{$a->width} x {$a->height}" : '') : "{$it['width']} x {$it['height']}, " . number_format($it['bytes']) . ' bytes',
        ($it['captionAsPrinted'] !== '' ? '"' . $it['captionAsPrinted'] . '"' : '(none printed)') . (!empty($it['captionNote']) ? ' ' . $it['captionNote'] : ''),
        $it['creditAsPrinted'] ?? '',
        $it['action'] . ' into ' . $it['field'] . (!empty($it['note']) ? '. ' . $it['note'] : ''),
        $it['cropNote'] ?? '',
    ])) . ' |';
}

/* Pavelka: the source fault. */
$SF = $D['sourceFault']; $pav = Entry::find()->id($SF['record'])->status(null)->one(); $sfHave = null;
if (!$pav || $pav->title !== $SF['expectTitle'] || !str_contains($val($pav, 'deathDate'), $SF['expectDeathDate'])) { $bad[] = "source fault: #{$SF['record']} is not {$SF['expectTitle']} with a death in {$SF['expectDeathDate']}"; }
else {
    $sfHave = Entry::find()->section('sourceFaults')->status(null)->relatedTo(['targetElement' => $pav, 'field' => 'faultRecord'])->all();
    $sfHave = array_values(array_filter($sfHave, fn($f) => (string)$f->faultField === $SF['faultField']))[0] ?? null;
    echo str_pad("#{$SF['record']} {$SF['expectTitle']}", 46) . ' source fault ' . ($sfHave ? "#{$sfHave->id} exists" : 'create') . ": {$SF['title']}" . PHP_EOL . "    reading: {$SF['reading']}" . PHP_EOL . "    basis: {$SF['basis']}" . PHP_EOL;
}

/* Chico López: the Likeness note, unless the data file holds it. */
$EN = $D['editorNote']; $ch = Entry::find()->id($EN['record'])->status(null)->one(); $enHave = false;
if (!$ch || $ch->title !== $EN['expectTitle'] || !$has($ch, 'editorNotes')) { $bad[] = "note: #{$EN['record']} is not {$EN['expectTitle']} or has no editorNotes"; }
else {
    foreach ((array)$ch->editorNotes as $row) { if (($row['heading'] ?? $row['col1'] ?? '') === $EN['heading']) { $enHave = true; } }
    echo str_pad("#{$EN['record']} {$EN['expectTitle']}", 46) . ' editor note "' . $EN['heading'] . '": ' . ($enHave ? 'exists' : (!empty($EN['hold']) ? 'HELD, not written' : 'add (bottom)')) . PHP_EOL . "    {$EN['note']}" . PHP_EOL;
    if (!empty($EN['hold'])) { echo "    HELD: {$EN['holdReason']}" . PHP_EOL; }
}

echo str_repeat('-', 78) . PHP_EOL . "imports {$n['import']}, crops {$n['import-edited']}, links {$n['link']} (with empty fields to fill: {$n['fill']}), already imported {$n['already']}; source fault 1; note " . (!empty($EN['hold']) ? 'held' : '1') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
echo str_repeat('-', 78) . PHP_EOL . 'CONTACT SHEET' . PHP_EOL . '| Record | File | Size | Caption as printed | Credit as printed | Link or import | Crop |' . PHP_EOL . '|---|---|---|---|---|---|---|' . PHP_EOL . implode(PHP_EOL, $sheet) . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first; nothing was written' . PHP_EOL; return; }

$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
if (!$folder) { throw new \RuntimeException('archiveMedia legacy/ folder not found'); }
$writes = 0; $made = [];
$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $k => &$p) {
        $it = $p['it'];
        if ($it['action'] === 'link') {
            if ($p['fill']) {
                $a = $p['asset'];
                if (isset($p['fill']['alt'])) { $a->alt = $p['fill']['alt']; }
                $a->setFieldValues(array_diff_key($p['fill'], ['alt' => 1]));
                if (!$el->saveElement($a)) { throw new \RuntimeException("$k: " . json_encode($a->getFirstErrors())); } $writes++;
            }
            $made[$k] = $p['asset']->id; continue;
        }
        if ($p['asset']) { $made[$k] = $p['asset']->id; continue; }
        $v = $it['fields'];
        if (isset($v['enhancedDate'])) { $v['enhancedDate'] = new \DateTime($v['enhancedDate'], new \DateTimeZone('America/Los_Angeles')); }
        if (isset($it['enhancedFromAssetId'])) { $v['enhancedFrom'] = [$it['enhancedFromAssetId']]; }
        if (isset($it['enhancedFromKey'])) { $v['enhancedFrom'] = [$made[$it['enhancedFromKey']]]; }
        $tmp = sys_get_temp_dir() . '/' . $it['assetFilename']; copy($p['path'], $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($it['assetFilename']); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id);
        $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        /* Title, alt and fields before the one save: a second save keeps the create scenario and fails on the moved temp file. */
        $a->title = $it['assetTitle']; $a->alt = $it['alt']; $a->setFieldValues($v);
        if (!$el->saveElement($a)) { throw new \RuntimeException("$k: " . json_encode($a->getFirstErrors())); }
        $made[$k] = $a->id; $writes++;
    }
    unset($p);
    /* One save per record, all its items together. status(null) keeps unpublished targets when rewriting a relation. */
    $per = [];
    foreach ($plan as $k => $p) { $per[$p['it']['record']][] = $k; }
    foreach ($per as $rid => $keys) {
        $r = Entry::find()->id($rid)->status(null)->one(); $changed = false;
        foreach ($keys as $k) {
            $f = $plan[$k]['it']['field']; $ids = $r->getFieldValue($f)->status(null)->ids();
            if (in_array($made[$k], $ids)) { continue; }
            if ($f === 'featuredImage' && $ids) { throw new \RuntimeException("$k: #$rid gained a portrait since the plan"); }
            $r->setFieldValue($f, $f === 'featuredImage' ? [$made[$k]] : array_values(array_merge($ids, [$made[$k]]))); $changed = true;
        }
        if ($changed) { if (!$el->saveElement($r)) { throw new \RuntimeException("#$rid: " . json_encode($r->getFirstErrors())); } $writes++; }
    }
    if (!$sfHave) {
        $sec = Craft::$app->getEntries()->getSectionByHandle('sourceFaults'); $f = new Entry(); $f->sectionId = $sec->id; $f->setTypeId($sec->getEntryTypes()[0]->id);
        $f->title = $SF['title'];
        $f->setFieldValues(['asPrinted' => $SF['asPrinted'], 'reading' => $SF['reading'], 'basis' => $SF['basis'], 'faultRecord' => [$pav->id], 'faultField' => $SF['faultField'],
            'decidedBy' => 'import_portrait_batch_2026_10_05.php, 5 October 2026', 'footnotes' => [['number' => '1', 'note' => $SF['footnote'], 'source' => 'editorial-2026']]]);
        if (!$el->saveElement($f)) { throw new \RuntimeException('source fault: ' . json_encode($f->getFirstErrors())); } $writes++;
    }
    if (!$enHave && empty($EN['hold'])) {
        $rows = array_map(fn($row) => ['heading' => $row['heading'] ?? '', 'position' => $row['position'] ?? 'bottom', 'note' => $row['note'] ?? ''], (array)$ch->editorNotes);
        $rows[] = ['heading' => $EN['heading'], 'position' => $EN['position'], 'note' => $EN['note']];
        $ch->setFieldValue('editorNotes', $rows);
        if (!$el->saveElement($ch)) { throw new \RuntimeException('note: ' . json_encode($ch->getFirstErrors())); } $writes++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written to the database (files already copied into the volume may remain): ' . $t->getMessage() . PHP_EOL; throw $t; }

/* Read-back. */
$short = [];
foreach ($plan as $k => $p) {
    $it = $p['it']; $r = Entry::find()->id($it['record'])->status(null)->one();
    if (!in_array($made[$k], $r->getFieldValue($it['field'])->status(null)->ids())) { $short[] = "$k not in {$it['field']}"; }
    $a = Asset::find()->id($made[$k])->one();
    if ($it['action'] !== 'link' && (string)$a->sourceChecksum !== 'sha256:' . $it['sha256']) { $short[] = "$k checksum not recorded"; }
    if ($it['action'] === 'import-edited' && !$a->enhancedFrom->one()) { $short[] = "$k enhancedFrom not set"; }
    if ($it['action'] === 'import-edited' && trim((string)$a->enhancementMethod) === '') { $short[] = "$k edit not recorded"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . " items, $writes writes") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('import_portrait_batch_2026_10_05.php', $writes, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'portrait batch of 5 October 2026: legacy-mirror portraits, links, Ward and Bowman crops with originals, Pavelka source fault');
if ($short) { throw new \RuntimeException('import_portrait_batch_2026_10_05: read-back short'); }
echo 'A second run is a no-op: assets are found by checksum, relations already set are skipped.' . PHP_EOL;
