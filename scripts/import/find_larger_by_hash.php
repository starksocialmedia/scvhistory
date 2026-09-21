<?php
/**
 * Finds a larger copy of an image by what it looks like, not what it is called.
 *
 * WHY
 *
 * The better-copies pass paired files by name and so missed every pair whose
 * names disagree. reynolds_jerry.jpg is 150x200 and sits on the preface;
 * jerry-reynolds.jpg is 720x1066 and sits in the same volume, and no name rule
 * connects them because the words are in the other order. A great many of the
 * legacy thumbnails are xxxt.jpg against xxx.jpg and a name rule would catch
 * those, which is exactly why a name rule feels like it works.
 *
 * HOW
 *
 * Difference hash. Each image is reduced to 9x8 greyscale and each pixel
 * compared with the one to its right, giving 64 bits that describe the
 * gradients rather than the pixels. It survives rescaling, recompression and
 * mild cropping, which is what separates a thumbnail from its original.
 *
 * Distance is the Hamming distance between two hashes, out of 64.
 *
 *    0-4    the same picture. Reported as "certain".
 *    5-8    almost certainly the same, differently cropped or adjusted.
 *           Reported as "likely" and wants an eye.
 *    9-12   reported as "possible" and mostly wrong. Included because a heavy
 *           crop lands here and those are the ones worth finding.
 *    13+    discarded.
 *
 * A candidate must also be MEANINGFULLY LARGER: at least 1.5x the width. A
 * match the same size is the same file under another name, which is a
 * duplicate and a different problem.
 *
 * WHAT IT SEARCHES
 *
 * The needles are the inline images with no enlargement available. The
 * haystack is every image in the Craft volume plus, where the mirror is
 * mounted, the legacy site's own image directories.
 *
 * Read only. Writes one JSON report and changes nothing.
 *
 * Run on the HOST, not in the container: the mirror is mounted here.
 *   php scripts/import/find_larger_by_hash.php
 *   php scripts/import/find_larger_by_hash.php --mirror
 */

$OPTS = getopt('', ['mirror', 'limit::']);
$USE_MIRROR = isset($OPTS['mirror']);
$LIMIT = isset($OPTS['limit']) ? (int)$OPTS['limit'] : 0;

$ROOT = dirname(__DIR__, 2);
$UPLOADS = $ROOT . '/web/uploads';
$MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com';
$NEEDLES = $ROOT . '/web/review/hash-needles.json';
$OUT = $ROOT . '/web/review/larger-by-hash.json';

if (!file_exists($NEEDLES)) {
    fwrite(STDERR, "no hash-needles.json. Generate it from Craft first.\n");
    exit(1);
}

/* ------------------------------------------------------------- the hash */

function dhash(string $path): ?string
{
    $sz = @getimagesize($path);
    if (!$sz) { return null; }
    $im = match ($sz[2]) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
        IMAGETYPE_PNG  => @imagecreatefrompng($path),
        IMAGETYPE_GIF  => @imagecreatefromgif($path),
        default => null,
    };
    if (!$im) { return null; }

    $small = imagecreatetruecolor(9, 8);
    imagecopyresampled($small, $im, 0, 0, 0, 0, 9, 8, imagesx($im), imagesy($im));

    $bits = '';
    for ($y = 0; $y < 8; $y++) {
        for ($x = 0; $x < 8; $x++) {
            $a = imagecolorat($small, $x, $y);
            $b = imagecolorat($small, $x + 1, $y);
            $ga = (($a >> 16 & 255) * 299 + ($a >> 8 & 255) * 587 + ($a & 255) * 114) / 1000;
            $gb = (($b >> 16 & 255) * 299 + ($b >> 8 & 255) * 587 + ($b & 255) * 114) / 1000;
            $bits .= $ga > $gb ? '1' : '0';
        }
    }
    return $bits;
}

function distance(string $a, string $b): int
{
    $d = 0;
    for ($i = 0; $i < 64; $i++) { if ($a[$i] !== $b[$i]) { $d++; } }
    return $d;
}

/* ---------------------------------------------------------- the needles */

$needles = json_decode(file_get_contents($NEEDLES), true) ?: [];
if ($LIMIT) { $needles = array_slice($needles, 0, $LIMIT); }
echo 'needles: ' . count($needles) . PHP_EOL;

$nHash = [];
foreach ($needles as $n) {
    $p = $UPLOADS . '/' . ltrim($n['path'], '/');
    if (!file_exists($p)) { continue; }
    $h = dhash($p);
    if ($h) { $nHash[$n['id']] = $n + ['hash' => $h, 'file' => $p]; }
}
echo 'needles hashed: ' . count($nHash) . PHP_EOL;

/* --------------------------------------------------------- the haystack */

$hay = [];
$scan = function (string $dir, string $label) use (&$hay, &$scan): bool {
    /* The mirror lives on an external volume that macOS will not let a
       command-line process read without an explicit grant, so this reports the
       refusal rather than dying on it: a volume-only run is still useful and a
       fatal error is not. */
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST);
    } catch (\Throwable $e) {
        echo '  cannot read ' . $dir . ': ' . $e->getMessage() . PHP_EOL;
        return false;
    }
    foreach ($it as $f) {
        if (!$f->isFile()) { continue; }
        /* Craft's transform cache lives in directories whose names begin with
           an underscore, and it is full of the needles themselves blown up:
           a 120x90 thumbnail rendered at 560 wide is a perfect hash match for
           the 120x90 thumbnail, four times the size, and completely worthless.
           The first run of this reported 98 certain matches and 98 of them
           were that. */
        if (preg_match('~/_[^/]+/~', $f->getPathname())) { continue; }
        $ext = strtolower($f->getExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) { continue; }
        if ($f->getSize() < 4096) { continue; }        /* a 4KB image is a thumbnail or a spacer */
        $hay[] = ['path' => $f->getPathname(), 'label' => $label];
    }
    return true;
};

$scan($UPLOADS, 'volume');
echo 'haystack, volume: ' . count($hay) . PHP_EOL;

if ($USE_MIRROR && is_dir($MIRROR)) {
    $before = count($hay);
    $any = false;
    foreach (['gif', 'images', 'oldtownnewhall', 'scvhistory'] as $sub) {
        if (is_dir($MIRROR . '/' . $sub)) { $any = $scan($MIRROR . '/' . $sub, 'mirror') || $any; }
    }
    if (!$any) { echo '  the mirror is mounted but unreadable from here; volume only.' . PHP_EOL; }
    echo 'haystack, mirror:  ' . (count($hay) - $before) . PHP_EOL;
} elseif ($USE_MIRROR) {
    echo 'mirror requested but not mounted at ' . $MIRROR . PHP_EOL;
}
echo 'haystack total:    ' . count($hay) . PHP_EOL . PHP_EOL;

/* ----------------------------------------------------------- the search */

$pairs = [];
$done = 0;
foreach ($hay as $h) {
    if (++$done % 500 === 0) { fwrite(STDERR, '  ' . $done . '/' . count($hay) . "\r"); }
    $sz = @getimagesize($h['path']);
    if (!$sz) { continue; }
    [$w, $ht] = $sz;

    /* Only worth hashing if it could be an upgrade for anything. */
    $couldServe = false;
    foreach ($nHash as $n) { if ($w >= $n['width'] * 1.5) { $couldServe = true; break; } }
    if (!$couldServe) { continue; }

    $hh = dhash($h['path']);
    if (!$hh) { continue; }

    foreach ($nHash as $id => $n) {
        if ($w < $n['width'] * 1.5) { continue; }
        if (realpath($h['path']) === realpath($n['file'])) { continue; }
        $d = distance($n['hash'], $hh);
        if ($d > 12) { continue; }
        $pairs[] = [
            'needleId' => $id,
            'needle' => $n['filename'],
            'needleSize' => $n['width'] . 'x' . $n['height'],
            'match' => basename($h['path']),
            'matchPath' => $h['path'],
            'source' => $h['label'],
            'matchSize' => $w . 'x' . $ht,
            'scale' => round($w / max($n['width'], 1), 1),
            'distance' => $d,
            'confidence' => $d <= 4 ? 'certain' : ($d <= 8 ? 'likely' : 'possible'),
            'sameName' => strtolower(pathinfo($n['filename'], PATHINFO_FILENAME))
                === strtolower(pathinfo(basename($h['path']), PATHINFO_FILENAME)),
        ];
    }
}
fwrite(STDERR, "\n");

/* Best match per needle first, then the rest. */
usort($pairs, fn($a, $b) => [$a['needleId'], $a['distance'], -$a['scale']]
                        <=> [$b['needleId'], $b['distance'], -$b['scale']]);

$best = [];
foreach ($pairs as $p) { if (!isset($best[$p['needleId']])) { $best[$p['needleId']] = $p; } }

file_put_contents($OUT, json_encode([
    'generated' => date('c'),
    'method' => 'difference hash, 9x8 greyscale, 64 bits, Hamming distance',
    'thresholds' => ['certain' => '0-4', 'likely' => '5-8', 'possible' => '9-12'],
    'minimum_scale' => 1.5,
    'searched_mirror' => $USE_MIRROR,
    'needles' => count($nHash),
    'haystack' => count($hay),
    'pairs' => count($pairs),
    'needles_matched' => count($best),
    'best' => array_values($best),
    'all' => $pairs,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

$byConf = [];
foreach ($best as $p) { $byConf[$p['confidence']] = ($byConf[$p['confidence']] ?? 0) + 1; }

echo 'needles with a larger match: ' . count($best) . ' of ' . count($nHash) . PHP_EOL;
foreach (['certain', 'likely', 'possible'] as $c) {
    if (isset($byConf[$c])) { echo '  ' . str_pad($c, 10) . $byConf[$c] . PHP_EOL; }
}
$diffName = count(array_filter($best, fn($p) => !$p['sameName']));
echo 'found despite a different name: ' . $diffName . PHP_EOL . PHP_EOL;

printf("%-30s %-10s %-30s %-10s %-5s %-5s %s\n",
    'NEEDLE', 'SIZE', 'LARGER', 'SIZE', 'DIST', 'x', 'CONFIDENCE');
foreach (array_slice(array_values($best), 0, 40) as $p) {
    printf("%-30s %-10s %-30s %-10s %-5d %-5s %s%s\n",
        substr($p['needle'], 0, 29), $p['needleSize'],
        substr($p['match'], 0, 29), $p['matchSize'],
        $p['distance'], $p['scale'] . 'x', $p['confidence'],
        $p['sameName'] ? '' : '  (different name)');
}
echo PHP_EOL . 'wrote ' . $OUT . PHP_EOL;
