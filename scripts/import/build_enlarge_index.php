/**
 * Which images have a bigger version, and where it is.
 *
 * On the legacy site a thumbnail sometimes opened a larger scan of itself and
 * sometimes opened a different page. The page links are handled by
 * link_images_to_records.php. This is the other kind: 1,806 image-to-image
 * links, of which 1,776 follow the same pattern, lw2184.jpg opening
 * lw2184_large.jpg.
 *
 * WHY THIS EXISTS
 *
 * The lightbox currently opens the same file the picture already shows. For an
 * inline image that is literally the same URL, so the magnifier promises an
 * enlargement and delivers the identical pixels. A control that does nothing is
 * worse than no control, because the reader tries it once and learns not to
 * trust the next one.
 *
 * So the rule is: the magnifier renders only where there is something bigger to
 * open. Bigger means strictly more pixels than the rendition on the page, which
 * is a different test for an inline image drawn at its natural size than for a
 * grid tile drawn from a 560px transform.
 *
 * WHAT IT WRITES
 *
 * templates/_data/enlarge.json, one object keyed by the source filename:
 *
 *   "lw2184.jpg": {"file":"lw2184_large.jpg","id":1234,"w":2400,"h":1600}
 *
 * id is present only where the target is already an asset in the volume. Where
 * it is not, the entry records that the file is on Reggie and waits. The
 * templates render a magnifier only for an entry carrying an id and a width
 * greater than what they are about to draw.
 *
 * Read only as far as the database is concerned: it writes one file and saves
 * nothing, so there is no $APPLY.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_enlarge_index.php'))"
 */

$root  = \Craft::getAlias('@root');
$OUT   = $root . '/templates/_data/enlarge.json';
$DRIVE = $root . '/inventory/raw/scvhistory-manifest-2026-08-20.sha256';
$REPORT = \Craft::getAlias('@webroot') . '/review/enlarge-targets.md';

/* ------------------------------------------------ the legacy image links */

$files = glob($root . '/inventory/legacy/*-images.json');
sort($files);
$pairs = []; $refs = 0; $toPage = 0; $selfRef = 0;

foreach ($files as $f) {
    $data = json_decode(file_get_contents($f), true);
    foreach (($data['images'] ?? []) as $r) {
        $to = trim((string)($r['links_to'] ?? ''));
        if ($to === '') { continue; }
        $refs++;
        $tail = strtolower(basename(parse_url($to, PHP_URL_PATH) ?: $to));
        if (!preg_match('/\.(jpe?g|png|gif|webp)$/', $tail)) { $toPage++; continue; }
        $src = strtolower(basename(parse_url((string)($r['src_raw'] ?? ''), PHP_URL_PATH) ?: ''));
        if ($src === '' || $tail === '') { continue; }
        /* A link to itself is not an enlargement. */
        if ($src === $tail) { $selfRef++; continue; }
        $pairs[$src] = $tail;
    }
}

echo 'image references carrying a link: ' . $refs . PHP_EOL;
echo '  to a page, handled elsewhere: ' . $toPage . PHP_EOL;
echo '  to the same file, not an enlargement: ' . $selfRef . PHP_EOL;
echo '  to a different image: ' . count($pairs) . PHP_EOL;

/* ------------------------------------------------------------- the drive */

$onDrive = [];
if (file_exists($DRIVE)) {
    $fh = fopen($DRIVE, 'r');
    while (($line = fgets($fh)) !== false) {
        $i = strpos($line, '  ./');
        if ($i === false) { continue; }
        $onDrive[strtolower(basename(rtrim(substr($line, $i + 4))))] = true;
    }
    fclose($fh);
    echo 'files listed on the drive manifest: ' . count($onDrive) . PHP_EOL;
} else {
    echo 'no drive manifest at ' . $DRIVE . ', the drive column will read unknown' . PHP_EOL;
}

/* ------------------------------------------------------------ the volume */

$assets = [];
foreach (\craft\elements\Asset::find()->limit(null)->all() as $a) {
    $assets[strtolower($a->filename)] = $a;
}
echo 'assets in the volume: ' . count($assets) . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

$index = [];
$haveBoth = 0; $targetMissing = 0; $targetOnDrive = 0; $notBigger = 0; $sourceNotHeld = 0;

foreach ($pairs as $src => $tgt) {
    if (!isset($assets[$src])) { $sourceNotHeld++; }

    $row = ['file' => $tgt];
    if (isset($assets[$tgt])) {
        $t = $assets[$tgt];
        $s = $assets[$src] ?? null;
        /* Bigger, or it is not an enlargement whatever the legacy page thought.
           A few of these point at a file the same size or smaller. */
        if ($s && $t->width && $s->width && $t->width <= $s->width) {
            $notBigger++;
            continue;
        }
        $row['id'] = (int)$t->id;
        $row['w']  = (int)$t->width;
        $row['h']  = (int)$t->height;
        $haveBoth++;
    } elseif (isset($onDrive[$tgt])) {
        $row['drive'] = true;
        $targetOnDrive++;
    } else {
        $targetMissing++;
    }
    $index[$src] = $row;
}

echo 'pairs where the larger file is already an asset: ' . $haveBoth . PHP_EOL;
echo 'pairs where it is on the drive and not yet fetched: ' . $targetOnDrive . PHP_EOL;
echo 'pairs where it is neither: ' . $targetMissing . PHP_EOL;
echo 'pairs where the target is not actually larger: ' . $notBigger . PHP_EOL;
echo 'pairs whose source we do not hold yet: ' . $sourceNotHeld . PHP_EOL;

@mkdir(dirname($OUT), 0775, true);
file_put_contents($OUT, json_encode($index, JSON_UNESCAPED_SLASHES));
echo PHP_EOL . 'index: ' . $OUT . ' (' . count($index) . ' entries)' . PHP_EOL;

$out = [];
$out[] = '# Enlarge targets: which pictures have a bigger version';
$out[] = '';
$out[] = 'Generated by scripts/import/build_enlarge_index.php on ' . date('Y-m-d H:i') . '.';
$out[] = 'Writes one file and saves nothing to the database.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| image references carrying a link | ' . $refs . ' |';
$out[] = '| to a page, handled by link_images_to_records | ' . $toPage . ' |';
$out[] = '| to the same file, not an enlargement | ' . $selfRef . ' |';
$out[] = '| to a different image | ' . count($pairs) . ' |';
$out[] = '| **larger file already an asset** | **' . $haveBoth . '** |';
$out[] = '| larger file on the drive, not yet fetched | ' . $targetOnDrive . ' |';
$out[] = '| larger file on neither | ' . $targetMissing . ' |';
$out[] = '| target not actually larger, dropped | ' . $notBigger . ' |';
$out[] = '';
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo 'report: ' . $REPORT . PHP_EOL;
