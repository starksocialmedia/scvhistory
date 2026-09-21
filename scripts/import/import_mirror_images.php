/**
 * Brings the pictures in from the Reggie mirror, in four passes.
 *
 * The mount is /mnt/reggie/scvhistory.com, read-only, and gif/ holds 22,043
 * files flat: stem plus extension, thumbnails a trailing t on the same stem.
 * We are not importing all of it and we are not importing 658 GB. Four passes,
 * in this order, because each one unlocks something specific:
 *
 *   1 enlarge targets the _large files. The magnifier renders only where there
 *                     is something bigger to open, and right now there never
 *                     is, so no tile on the Perkins pages or the Reynolds
 *                     chapters has one. This pass turns a dead control back on,
 *                     which is why it goes first.
 *   2 plates          one picture per photograph record. 1,541 of the 1,544
 *                     currently say "the image is not in the archive yet".
 *   3 captioned       every file the extraction holds a real caption for.
 *                     apply_extracted_captions.php can currently reach 89 of
 *                     its 5,220 records because the other files are not here.
 *   4 better copies   files we already hold, where the mirror's copy is larger.
 *                     Ours were fetched from the live site, which recompresses;
 *                     the mirror has what was uploaded.
 *
 * The passes overlap and the union is deduplicated: a file wanted by two passes
 * is imported once, by the earlier one.
 *
 * WHAT EVERY ASSET CARRIES
 *
 *   legacySourcePath  /gif/lw2184.jpg, the path on the legacy site, which is
 *                     also its path under the mirror root. Provenance, and it
 *                     must not change when the stored file is later replaced by
 *                     an enhanced version.
 *   photoSourceCode   lw2184, the stem. What the plate resolver matches on.
 *
 * REPLACEMENT KEEPS THE ASSET
 *
 * Pass 4 replaces the bytes of an existing asset rather than adding a second
 * file. The asset id, its relations, its caption, its alt text and every record
 * that points at it are untouched; only the file underneath changes. Adding a
 * second file is what produced perkins_ab_2026-09-18-071146_nitl.jpg, and two
 * assets for one picture is worse than a smaller picture.
 *
 * For the same reason avoidFilenameConflicts is off. A name collision is
 * resolved by replacing the file on the existing asset, never by letting Craft
 * invent a suffix.
 *
 * Dry run by default: it copies nothing, creates nothing and writes a report
 * with counts and a twenty-file sample per pass.
 *
 * Needs legacySourcePath on the asset layout. Run
 * scripts/import/fix_asset_provenance.php first, which creates it.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_mirror_images.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will copy files and write to the database' . PHP_EOL; }

$MIRROR  = '/mnt/reggie/scvhistory.com';
$VOLUME  = 'archiveMedia';
$FOLDER  = 'legacy/';
$REPORT  = \Craft::getAlias('@webroot') . '/review/mirror-import.md';
$SAMPLE  = 20;
$LIMIT   = 0;          /* files per pass; 0 for all. Useful for a first apply. */

if (!is_dir($MIRROR)) { echo 'mirror not mounted at ' . $MIRROR . PHP_EOL; return; }

$root = \Craft::getAlias('@root');
$elements = Craft::$app->getElements();
$assetsSvc = Craft::$app->getAssets();

/* ------------------------------------------------------- the mirror index */

echo 'indexing the mirror' . PHP_EOL;
$mirror = [];
foreach (['gif', 'orig', 'icons', 'pico', 'mentryville', 'oldtownnewhall', 'warmemorial'] as $dir) {
    $base = $MIRROR . '/' . $dir;
    if (!is_dir($base)) { continue; }
    $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if (!$f->isFile()) { continue; }
        if (!preg_match('/\.(jpe?g|png|gif|webp)$/i', $f->getFilename())) { continue; }
        $fn = strtolower($f->getFilename());
        $size = $f->getSize();
        if (!isset($mirror[$fn]) || $size > $mirror[$fn]['size']) {
            $mirror[$fn] = ['size' => $size, 'path' => $f->getPathname(),
                            'rel' => '/' . ltrim(str_replace($MIRROR, '', $f->getPathname()), '/')];
        }
    }
}
echo 'distinct image filenames on the mirror: ' . count($mirror) . PHP_EOL;

/* --------------------------------------------------------- what we hold */

$held = [];
foreach (\craft\elements\Asset::find()->limit(null)->all() as $a) { $held[strtolower($a->filename)] = $a; }
echo 'assets in the volume: ' . count($held) . PHP_EOL;

/* ------------------------------------------------------------ pass lists */

/* Files the series imports will need. Worden, Making Cents and the five Old
   Town Newhall runs reference pictures by name, and a body that renders an
   [image:N] against an asset that is not here shows nothing. Each dry run
   writes its list, so pass 3 covers them without anybody keeping a tally by
   hand. */
$wanted = [];
foreach (glob(\Craft::getAlias('@webroot') . '/review/images-wanted-*.json') as $wf) {
    foreach ((json_decode(file_get_contents($wf), true) ?: []) as $fn) {
        $wanted[strtolower($fn)] = true;
    }
}
if ($wanted) { echo 'files the series imports reference: ' . count($wanted) . PHP_EOL; }

/* 1. every file with a real caption, on the same terms apply_extracted_captions
      uses: a caption that is not navigation. */
$NAV = '/^\s*(click|download|enlarge|mouse\s*over|scroll\s+down)\b/iu';
$captioned = [];
foreach (glob($root . '/inventory/legacy/*-images.json') as $f) {
    $d = json_decode(file_get_contents($f), true);
    foreach (($d['images'] ?? []) as $r) {
        $c = trim((string)($r['caption'] ?? ''));
        if ($c === '') { continue; }
        $kept = [];
        foreach (preg_split('/\s*\|\s*/u', preg_replace('/\s+/u', ' ', $c)) as $seg) {
            if (trim($seg) === '' || preg_match($NAV, $seg)) { continue; }
            $kept[] = $seg;
        }
        if (!$kept) { continue; }
        $fn = strtolower(basename(parse_url((string)($r['src_raw'] ?? ''), PHP_URL_PATH) ?: ''));
        if ($fn !== '') { $captioned[$fn] = true; }
    }
}

/* 2. one plate per photograph record. */
$plates = [];
foreach (\craft\elements\Entry::find()->section('photographs')->status(null)->limit(null)->all() as $e) {
    $code = strtolower(trim((string)($e->photoSourceCode ?: $e->legacyKey)));
    if ($code === '') { continue; }
    foreach (['.jpg', '.jpeg', '.png', '.gif'] as $ext) {
        if (isset($mirror[$code . $ext])) { $plates[$code . $ext] = true; break; }
    }
}

/* 3. the _large targets. */
$targets = [];
$enlPath = $root . '/templates/_data/enlarge.json';
if (file_exists($enlPath)) {
    foreach (json_decode(file_get_contents($enlPath), true) as $src => $row) {
        $t = strtolower($row['file'] ?? '');
        if ($t !== '') { $targets[$t] = true; }
    }
}

/* 4. files we hold whose mirror copy is larger. Bytes, which is a proxy: some
      of this is compression rather than resolution, and replacing a file with
      the same pixels at higher quality is still the right way round. */
$better = [];
foreach ($held as $fn => $a) {
    if (isset($mirror[$fn]) && $mirror[$fn]['size'] > $a->size * 1.05) { $better[$fn] = true; }
}

/* Enlarge targets first. Until they land no tile on the Perkins pages or the
   Reynolds chapters has a magnifier at all, because the rule is that the
   control renders only where there is something bigger to open and right now
   there never is. Everything else improves a page that already works; this one
   turns a dead control back on. */
$PASSES = [
    ['key' => 'enlarge',   'label' => 'Enlarge targets', 'want' => $targets],
    ['key' => 'plates',    'label' => 'One plate per photograph record', 'want' => $plates],
    ['key' => 'captioned', 'label' => 'Files with a caption we hold', 'want' => $captioned],
    ['key' => 'series',    'label' => 'Referenced by the Worden, OTN and coins imports', 'want' => $wanted],
    ['key' => 'better',    'label' => 'Better copies of files we already hold', 'want' => $better],
];

/* One pass at a time. Set to a key above to run only that pass; empty runs all
   four in order. A pass claims its files, so running them one at a time and
   running them together give the same result. */
$ONLY = 'enlarge';
if ($ONLY !== '') {
    $PASSES = array_values(array_filter($PASSES, fn($p) => $p['key'] === $ONLY));
    if (!$PASSES) { echo 'no pass called ' . $ONLY . PHP_EOL; return; }
    echo 'running one pass only: ' . $ONLY . PHP_EOL;
}

/* ------------------------------------------------------------- the plan */

$volume = Craft::$app->getVolumes()->getVolumeByHandle($VOLUME);
if (!$volume) { echo 'volume ' . $VOLUME . ' NOT FOUND' . PHP_EOL; return; }
$folder = $assetsSvc->findFolder(['volumeId' => $volume->id, 'path' => $FOLDER])
       ?: $assetsSvc->getRootFolderByVolumeId($volume->id);

$hasPathField = false;
foreach ($volume->getFieldLayout()->getCustomFields() as $f) {
    if ($f->handle === 'legacySourcePath') { $hasPathField = true; }
}
if (!$hasPathField) {
    echo PHP_EOL . 'legacySourcePath is not on the asset layout yet.' . PHP_EOL;
    echo 'Run scripts/import/fix_asset_provenance.php first; the path will not be' . PHP_EOL;
    echo 'recorded without it and provenance is the point of this import.' . PHP_EOL;
    if ($APPLY) { return; }
}

echo str_repeat('=', 74) . PHP_EOL;

$seen = []; $plan = []; $totals = [];
foreach ($PASSES as $p) {
    $new = 0; $replace = 0; $skipSame = 0; $notOnMirror = 0; $dup = 0; $rows = [];
    foreach (array_keys($p['want']) as $fn) {
        if (isset($seen[$fn])) { $dup++; continue; }
        if (!isset($mirror[$fn])) { $notOnMirror++; continue; }
        $seen[$fn] = $p['key'];

        $m = $mirror[$fn];
        $stem = pathinfo($fn, PATHINFO_FILENAME);
        $existing = $held[$fn] ?? null;

        if ($existing === null) {
            $action = 'create';
            $new++;
        } elseif ($m['size'] > $existing->size * 1.05) {
            $action = 'replace';
            $replace++;
        } else {
            /* Already here and not improved: still worth stamping provenance,
               which costs nothing and is what makes the plate resolver work. */
            $action = 'provenance only';
            $skipSame++;
        }

        if ($LIMIT && count($rows) >= $LIMIT) { continue; }
        $rows[] = ['fn' => $fn, 'action' => $action, 'rel' => $m['rel'], 'code' => $stem,
                   'size' => $m['size'], 'was' => $existing ? $existing->size : 0,
                   'id' => $existing ? $existing->id : null, 'path' => $m['path']];
    }
    $totals[$p['key']] = compact('new', 'replace', 'skipSame', 'notOnMirror', 'dup');
    $plan[$p['key']] = $rows;

    printf("%-10s %-40s new=%-5d replace=%-5d provenance=%-5d notOnMirror=%-5d alreadyInAnEarlierPass=%d\n",
        $p['key'], $p['label'], $new, $replace, $skipSame, $notOnMirror, $dup);
}

$grandNew = array_sum(array_column($totals, 'new'));
$grandRep = array_sum(array_column($totals, 'replace'));
echo str_repeat('-', 74) . PHP_EOL;
echo 'files to create: ' . $grandNew . PHP_EOL;
echo 'files to replace in place: ' . $grandRep . PHP_EOL;
echo 'assets afterwards: ' . (count($held) + $grandNew) . PHP_EOL;

/* ---------------------------------------------------------------- apply */

$made = 0; $swapped = 0; $stamped = 0; $failed = [];
if ($APPLY) {
    $tmpDir = sys_get_temp_dir() . '/mirror-import';
    @mkdir($tmpDir, 0775, true);

    foreach ($PASSES as $p) {
        foreach ($plan[$p['key']] as $r) {
            /* The mirror is read-only, so every file is copied to a temp path
               before Craft is allowed near it. */
            $tmp = $tmpDir . '/' . $r['fn'];
            if (!@copy($r['path'], $tmp)) { $failed[] = $r['fn'] . ': could not copy from the mirror'; continue; }

            if ($r['action'] === 'create') {
                $a = new \craft\elements\Asset();
                $a->tempFilePath = $tmp;
                $a->setFilename($r['fn']);
                $a->newFolderId = $folder->id;
                $a->setVolumeId($volume->id);
                $a->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
                /* Off deliberately. A conflict is resolved by replacing the
                   file on the asset that already has the name, never by
                   inventing a suffix. */
                $a->avoidFilenameConflicts = false;
                if (!$elements->saveElement($a)) { $failed[] = $r['fn'] . ': ' . json_encode($a->getErrors()); continue; }
                $made++;
                $asset = $a;
            } else {
                $asset = \craft\elements\Asset::find()->id($r['id'])->one();
                if (!$asset) { $failed[] = $r['fn'] . ': asset vanished'; continue; }
                if ($r['action'] === 'replace') {
                    $assetsSvc->replaceAssetFile($asset, $tmp, $asset->filename);
                    $swapped++;
                }
            }

            $asset->setFieldValue('photoSourceCode', $r['code']);
            if ($hasPathField) { $asset->setFieldValue('legacySourcePath', $r['rel']); }
            if ($elements->saveElement($asset)) { $stamped++; }
            else { $failed[] = $r['fn'] . ': provenance ' . json_encode($asset->getErrors()); }
            @unlink($tmp);
        }
    }

    echo PHP_EOL . 'created: ' . $made . PHP_EOL;
    echo 'replaced in place: ' . $swapped . PHP_EOL;
    echo 'provenance saved: ' . $stamped . PHP_EOL;
    foreach (array_slice($failed, 0, 12) as $f) { echo '  FAILED ' . $f . PHP_EOL; }

    /* ------------------------------------------------- read the writes back */

    $back = 0; $short = [];
    foreach ($PASSES as $p) {
        foreach ($plan[$p['key']] as $r) {
            $fresh = \craft\elements\Asset::find()->filename($r['fn'])->one();
            if (!$fresh) { $short[] = $r['fn'] . ': not in the volume after import'; continue; }
            if (trim((string)$fresh->getFieldValue('photoSourceCode')) !== $r['code']) {
                $short[] = $r['fn'] . ': photoSourceCode reads back as "'
                         . trim((string)$fresh->getFieldValue('photoSourceCode')) . '"';
                continue;
            }
            if ($hasPathField && trim((string)$fresh->getFieldValue('legacySourcePath')) !== $r['rel']) {
                $short[] = $r['fn'] . ': legacySourcePath reads back as "'
                         . trim((string)$fresh->getFieldValue('legacySourcePath')) . '"';
                continue;
            }
            if ($r['action'] === 'replace' && $fresh->size <= $r['was']) {
                $short[] = $r['fn'] . ': still ' . $fresh->size . ' bytes, the replacement did not take';
                continue;
            }
            $back++;
        }
    }
    echo 'read back: ' . $back . ' files carry their provenance and their bytes' . PHP_EOL;
    if ($short) {
        echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
        foreach (array_slice($short, 0, 12) as $m) { echo '  ' . $m . PHP_EOL; }
        echo 'Do not re-run until this is understood.' . PHP_EOL;
        return;
    }
    echo 'verified.' . PHP_EOL;
}

/* --------------------------------------------------------------- report */

$out = [];
$out[] = '# Importing the pictures from the Reggie mirror';
$out[] = '';
$out[] = 'Generated by scripts/import/import_mirror_images.php on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing copied and nothing written') . '.';
$out[] = '';
$out[] = 'Mirror `' . $MIRROR . '`, ' . number_format(count($mirror)) . ' distinct image filenames.';
$out[] = 'Volume `' . $VOLUME . '`, folder `' . $FOLDER . '`, ' . count($held) . ' assets before.';
$out[] = '';
$out[] = '| pass | create | replace in place | provenance only | not on the mirror | claimed earlier |';
$out[] = '|---|---:|---:|---:|---:|---:|';
foreach ($PASSES as $p) {
    $t = $totals[$p['key']];
    $out[] = '| **' . $p['key'] . '** ' . $p['label'] . ' | ' . $t['new'] . ' | ' . $t['replace']
           . ' | ' . $t['skipSame'] . ' | ' . $t['notOnMirror'] . ' | ' . $t['dup'] . ' |';
}
$out[] = '| | **' . $grandNew . '** | **' . $grandRep . '** | | | |';
$out[] = '';
$out[] = 'Assets afterwards: **' . number_format(count($held) + $grandNew) . '**.';
$out[] = '';
foreach ($PASSES as $p) {
    $rows = $plan[$p['key']];
    $out[] = '## ' . $p['key'] . ': ' . $p['label'];
    $out[] = '';
    $out[] = 'Sample of ' . min($SAMPLE, count($rows)) . ' from ' . count($rows) . '.';
    $out[] = '';
    $out[] = '| file | action | mirror path | code | mirror bytes | ours now |';
    $out[] = '|---|---|---|---|---:|---:|';
    foreach (array_slice($rows, 0, $SAMPLE) as $r) {
        $out[] = '| `' . $r['fn'] . '` | ' . $r['action'] . ' | `' . $r['rel'] . '` | `' . $r['code']
               . '` | ' . number_format($r['size']) . ' | ' . ($r['was'] ? number_format($r['was']) : '—') . ' |';
    }
    $out[] = '';
}
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
