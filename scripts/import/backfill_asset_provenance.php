/**
 * Puts the provenance back on the assets the mirror import failed to stamp.
 *
 * WHAT WENT WRONG, AND WHY THE FILES ARE FINE
 *
 * import_mirror_images.php creates an asset like this:
 *
 *     $a = new Asset();
 *     $a->tempFilePath = $tmp;
 *     $a->setScenario(Asset::SCENARIO_CREATE);
 *     saveElement($a);              // the file moves into the volume. Good.
 *     $a->setFieldValue('photoSourceCode', ...);
 *     saveElement($a);              // <- this one failed
 *
 * The second save reuses the same object, and that object is still in
 * SCENARIO_CREATE. Craft's create scenario requires either tempFilePath or
 * newLocation, because creating an asset means putting a file somewhere. The
 * first save consumed tempFilePath and nulled it, the file having arrived, so
 * the second save re-validated the create rules against an object that no
 * longer had a file to move and refused with "newLocation and tempFilePath
 * cannot be blank". The file was already in place and the element already
 * existed, so nothing was lost; only the two custom fields never got written.
 * fix_asset_provenance.php worked on #14 because it loaded the asset fresh from
 * the database, which arrives in the default scenario with nothing to validate.
 *
 * 3,616 of the 4,312 assets are missing both fields. 3,491 of those resolve to
 * a file on the mirror by filename and can be filled in from it.
 *
 * WHAT THIS WRITES
 *
 *   legacySourcePath  the path under the mirror root, which is also the path on
 *                     the legacy site: /gif/lw2184.jpg
 *   photoSourceCode   the filename stem, lw2184, which is what the plate
 *                     resolver matches on
 *
 * Both are provenance and neither changes when the stored file is later
 * replaced by a rescan or an enhancement.
 *
 * An asset whose filename is not on the mirror is left alone and counted. It
 * came from somewhere else, and inventing a mirror path for it would be worse
 * than leaving the field empty: an empty field is a known gap, a wrong one is a
 * false provenance.
 *
 * Idempotent: an asset that already carries legacySourcePath is skipped, so a
 * value set by hand survives.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/backfill_asset_provenance.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$MIRROR = '/mnt/reggie/scvhistory.com';
$DIRS   = ['gif', 'orig', 'icons', 'pico', 'mentryville', 'oldtownnewhall', 'warmemorial'];
$REPORT = \Craft::getAlias('@review') . '/asset-provenance-backfill.md';
$BATCH  = 250;   /* saved in batches so a failure does not lose the whole run */

if (!is_dir($MIRROR)) { echo 'mirror not mounted at ' . $MIRROR . PHP_EOL; return; }

echo 'indexing the mirror' . PHP_EOL;
$mirror = [];
foreach ($DIRS as $d) {
    $base = $MIRROR . '/' . $d;
    if (!is_dir($base)) { continue; }
    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)) as $f) {
        if (!$f->isFile() || !preg_match('/\.(jpe?g|png|gif|webp)$/i', $f->getFilename())) { continue; }
        $fn = strtolower($f->getFilename());
        /* The largest wins where a name repeats, which is the same rule the
           importer used to choose what to fetch. */
        $size = $f->getSize();
        if (!isset($mirror[$fn]) || $size > $mirror[$fn]['size']) {
            $mirror[$fn] = ['size' => $size,
                            'rel' => '/' . ltrim(str_replace($MIRROR, '', $f->getPathname()), '/')];
        }
    }
}
echo 'distinct image filenames on the mirror: ' . number_format(count($mirror)) . PHP_EOL;

$elements = Craft::$app->getElements();
$assets = \craft\elements\Asset::find()->limit(null)->all();
echo 'assets in the volume: ' . number_format(count($assets)) . PHP_EOL;

$hasField = function (\craft\elements\Asset $a, string $h): bool {
    $layout = $a->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

$plan = []; $already = 0; $notOnMirror = []; $noField = 0;
foreach ($assets as $a) {
    if (!$hasField($a, 'legacySourcePath')) { $noField++; continue; }
    if (trim((string)$a->getFieldValue('legacySourcePath')) !== '') { $already++; continue; }

    $fn = strtolower($a->filename);
    if (!isset($mirror[$fn])) { $notOnMirror[] = $a->filename; continue; }

    $plan[] = ['id' => $a->id, 'fn' => $a->filename,
               'rel' => $mirror[$fn]['rel'],
               'code' => pathinfo($fn, PATHINFO_FILENAME)];
}

echo str_repeat('=', 74) . PHP_EOL;
echo 'already carry a path, left alone: ' . number_format($already) . PHP_EOL;
echo 'ASSETS NEEDING THE BACKFILL: ' . number_format(count($plan)) . PHP_EOL;
echo 'not on the mirror, left empty: ' . number_format(count($notOnMirror)) . PHP_EOL;
if ($noField) { echo 'without legacySourcePath on the layout: ' . $noField . PHP_EOL; }

echo PHP_EOL . 'sample of what would be written:' . PHP_EOL;
foreach (array_slice($plan, 0, 12) as $r) {
    printf("   %-40s %-34s %s\n", $r['fn'], $r['rel'], $r['code']);
}
if ($notOnMirror) {
    echo PHP_EOL . 'left empty because the filename is not on the mirror:' . PHP_EOL;
    foreach (array_slice($notOnMirror, 0, 10) as $f) { echo '   ' . $f . PHP_EOL; }
    if (count($notOnMirror) > 10) { echo '   ...and ' . (count($notOnMirror) - 10) . ' more' . PHP_EOL; }
}

$out = [];
$out[] = '# Asset provenance backfill';
$out[] = '';
$out[] = 'Generated by scripts/import/backfill_asset_provenance.php on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing written') . '.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| assets in the volume | ' . number_format(count($assets)) . ' |';
$out[] = '| already carry a path | ' . number_format($already) . ' |';
$out[] = '| **needing the backfill** | **' . number_format(count($plan)) . '** |';
$out[] = '| not on the mirror, left empty | ' . number_format(count($notOnMirror)) . ' |';
$out[] = '';
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");

if (!$APPLY) {
    echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

/* ---------------------------------------------------------------- apply */

$saved = 0; $failed = [];
foreach (array_chunk($plan, $BATCH) as $i => $chunk) {
    foreach ($chunk as $r) {
        /* Loaded fresh, every time. This is the whole fix: an asset read from
           the database arrives in the default scenario, with no file move
           pending and nothing for the create rules to fail on. Reusing an
           object that was created in this run is what broke the import. */
        $a = \craft\elements\Asset::find()->id($r['id'])->one();
        if (!$a) { $failed[] = $r['fn'] . ': not found'; continue; }
        $a->setFieldValue('photoSourceCode', $r['code']);
        $a->setFieldValue('legacySourcePath', $r['rel']);
        if ($elements->saveElement($a)) { $saved++; }
        else { $failed[] = $r['fn'] . ': ' . json_encode($a->getFirstErrors()); }
    }
    echo '  batch ' . ($i + 1) . ': ' . $saved . ' saved so far' . PHP_EOL;
}

echo PHP_EOL . 'saved: ' . number_format($saved) . PHP_EOL;
foreach (array_slice($failed, 0, 10) as $f) { echo '  FAILED ' . $f . PHP_EOL; }

/* ------------------------------------------------- read the writes back */

$back = 0; $short = [];
foreach ($plan as $r) {
    $fresh = \craft\elements\Asset::find()->id($r['id'])->one();
    if (!$fresh) { $short[] = $r['fn'] . ': gone after save'; continue; }
    $p = trim((string)$fresh->getFieldValue('legacySourcePath'));
    $c = trim((string)$fresh->getFieldValue('photoSourceCode'));
    if ($p !== $r['rel']) { $short[] = $r['fn'] . ': path reads back as "' . $p . '"'; continue; }
    if ($c !== $r['code']) { $short[] = $r['fn'] . ': code reads back as "' . $c . '"'; continue; }
    $back++;
}
echo 'read back: ' . number_format($back) . ' of ' . number_format(count($plan)) . PHP_EOL;
if ($short) {
    echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
    foreach (array_slice($short, 0, 10) as $m) { echo '  ' . $m . PHP_EOL; }
    echo 'Do not re-run until this is understood.' . PHP_EOL;
    return;
}
echo 'verified.' . PHP_EOL;
