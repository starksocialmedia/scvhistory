/**
 * Brings the legacy site's images into Craft and puts them back where Leon had
 * them in the prose.
 *
 * Reads the three image inventories written by the page importers. For each row
 * it resolves src_raw against the page it came from, prefers the larger version
 * when links_to points at an image rather than another page, downloads once at
 * one request per second, creates an asset, and relates it to the owning record
 * through recordImages in position_in_body order.
 *
 * Then, for everything except a war memorial, it inserts an [image:N] token on
 * its own line in the body so _partials/prose floats the image where it sat on
 * the legacy page. N is the 1-based index into recordImages, which is what that
 * partial reads.
 *
 * Placement does not come from position_in_body. That field is the image's
 * ordinal on the page, 1..n, not an offset into the text: every page in all
 * three inventories numbers its images exactly 1..n. Using it as an offset would
 * stack the first n paragraphs with figures. The real position is recovered from
 * the source inventory instead, by taking the text that precedes each <img> in
 * body_html and finding that anchor in the stored body. An image with no
 * recoverable anchor is related but not placed, and is reported.
 *
 * War memorial bodies are left alone. Eighteen of those records carry the
 * service record duplicated as prose beneath the fields, so a token dropped into
 * that text would land in the middle of a table. Their images are related and
 * the template's Photos section renders them.
 *
 * Skipped: a filename already in the volume, so a second run is a no-op; and the
 * navigation rail, taken as any src_raw appearing on more than five different
 * pages within one inventory.
 *
 * Dry run by default. Set $APPLY = true to download and write.
 * Set $PROBE_SIZES = true to ask the server for content lengths during a dry run;
 * it is off by default because it is one request per image against Leon's live
 * site for a number the dry run does not otherwise need.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_legacy_images.php'))"
 */

$APPLY = false;
$PROBE_SIZES = false;
/* Set to a slug to print that record's planned body, for reading before applying. */
$SHOW_BODY_FOR = '';

/* Crawl conduct, per GROK-CONTRACT.md: one request at a time, one second apart,
   a User-Agent naming the project and a contact address. Put a real mailbox in
   $CONTACT before running this against the live site. */
$CONTACT = 'https://github.com/starksocialmedia/scvhistory';
$USER_AGENT = 'SCVHistory-Legacy-Images/1.0 (+' . $CONTACT . ')';
$DELAY_MS = 1000;

$INVENTORIES = ['perkins-images', 'reynolds-images', 'warmemorial-images'];
/* Where the page HTML lives, for recovering where each image actually sat. */
$SOURCE_OF = [
    'perkins-images' => 'perkins',
    'reynolds-images' => 'reynolds-full',
    'warmemorial-images' => 'warmemorial',
];
$VOLUME = 'archiveMedia';
$SUBFOLDER = 'legacy';
$CHROME_PAGE_THRESHOLD = 5;      /* more than this many pages means navigation */
$NO_TOKENS_IN = ['warMemorials'];
$IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

$root = \Craft::getAlias('@root');
$elements = Craft::$app->getElements();

/* ---------------------------------------------------------------- helpers */

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) {
        if ($f->handle === $handle) { return true; }
    }
    return false;
};

/** Resolves a possibly relative src against the page it appeared on. */
$absolutise = function (string $src, string $base): ?string {
    $src = trim($src);
    if ($src === '') { return null; }
    if (preg_match('~^https?://~i', $src)) { return $src; }
    if (str_starts_with($src, '//')) { return 'https:' . $src; }

    $b = parse_url($base);
    if (!$b || empty($b['host'])) { return null; }
    $scheme = $b['scheme'] ?? 'https';
    $host = $b['host'];
    if (str_starts_with($src, '/')) { return $scheme . '://' . $host . $src; }

    $dir = rtrim(dirname($b['path'] ?? '/'), '/');
    $path = $dir . '/' . $src;
    /* collapse ../ and ./ */
    $out = [];
    foreach (explode('/', $path) as $seg) {
        if ($seg === '' || $seg === '.') { continue; }
        if ($seg === '..') { array_pop($out); continue; }
        $out[] = $seg;
    }
    return $scheme . '://' . $host . '/' . implode('/', $out);
};

$extOf = function (string $url): string {
    $p = parse_url($url, PHP_URL_PATH) ?: $url;
    return strtolower(pathinfo($p, PATHINFO_EXTENSION));
};

$filenameOf = function (string $url): string {
    $p = parse_url($url, PHP_URL_PATH) ?: $url;
    return basename($p);
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($PROBE_SIZES ? ' (probing sizes)' : '') . PHP_EOL;
echo 'user agent: ' . $USER_AGENT . PHP_EOL;
if ($CONTACT === 'https://github.com/starksocialmedia/scvhistory') {
    echo 'NOTE: $CONTACT is the repository URL. Put a mailbox there before running this for real.' . PHP_EOL;
}

$volume = Craft::$app->volumes->getVolumeByHandle($VOLUME);
if (!$volume) { echo 'ERROR: volume ' . $VOLUME . ' not found' . PHP_EOL; return; }
$rootFolder = Craft::$app->assets->getRootFolderByVolumeId($volume->id);
$folder = Craft::$app->assets->findFolder(['volumeId' => $volume->id, 'path' => $SUBFOLDER . '/']);
echo 'volume: ' . $volume->name . ', target folder: ' . $SUBFOLDER . '/' . ($folder ? '' : '  (would be created)') . PHP_EOL;

/* Filenames already held, so a second run does nothing. */
$existingFilenames = [];
foreach (\craft\elements\Asset::find()->volume($VOLUME)->all() as $a) {
    $existingFilenames[strtolower($a->filename)] = $a->id;
}
echo 'assets already in the volume: ' . count($existingFilenames) . PHP_EOL;

/* ------------------------------------------------------- image placement */

/* For each page, the text immediately before each <img> in the original HTML.
   That anchor is what locates the image in the stored body. */
$anchors = [];
foreach ($SOURCE_OF as $imgInv => $srcName) {
    $p = $root . '/inventory/legacy/' . $srcName . '.json';
    if (!file_exists($p)) { continue; }
    $d = json_decode(file_get_contents($p), true);
    $pages = $d['pages'] ?? array_merge($d['series_pages'] ?? [], $d['related_pages'] ?? []);
    foreach ($pages as $page) {
        $html = (string)($page['body_html'] ?? '');
        if ($html === '') { continue; }
        $n = 0;
        if (preg_match_all('~<img[^>]+src="[^"]*"~i', $html, $m, PREG_OFFSET_CAPTURE)) {
            foreach ($m[0] as $hit) {
                $n++;
                $before = preg_replace('~<[^>]+>~', ' ', substr($html, 0, $hit[1]));
                $before = trim(preg_replace('~\s+~u', ' ', html_entity_decode($before, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                $anchors[$imgInv . '|' . $page['legacy_key']][$n] = mb_substr($before, -60);
            }
        }
    }
}
echo 'anchors recovered from page HTML: ' . array_sum(array_map('count', $anchors)) . PHP_EOL;

/* ---------------------------------------------------------------- plan */

$plan = [];          /* pageKey => ['entry'=>Entry, 'rows'=>[...]] */
$chromeSkipped = []; /* src => pages */
$dupSkipped = [];
$unresolvedPages = [];
$badRows = [];
$seenUrl = [];       /* absolute url => first filename, so one file downloads once */

foreach ($INVENTORIES as $inv) {
    $path = $root . '/inventory/legacy/' . $inv . '.json';
    if (!file_exists($path)) { echo 'WARNING: ' . $inv . '.json not found' . PHP_EOL; continue; }
    $rows = json_decode(file_get_contents($path), true)['images'] ?? [];

    /* The navigation rail: the same src on many different pages. */
    $pagesPerSrc = [];
    foreach ($rows as $r) { $pagesPerSrc[$r['src_raw']][$r['legacy_key']] = true; }
    $chrome = [];
    foreach ($pagesPerSrc as $src => $pages) {
        if (count($pages) > $CHROME_PAGE_THRESHOLD) { $chrome[$src] = count($pages); }
    }
    foreach ($chrome as $src => $n) { $chromeSkipped[$inv . '  ' . $src] = $n; }

    foreach ($rows as $r) {
        if (isset($chrome[$r['src_raw']])) { continue; }

        /* Prefer links_to only when it is an image; it usually points at another
           page, in which case the thumbnail is all there is. */
        $src = $absolutise((string)$r['src_raw'], (string)$r['source_url']);
        $link = trim((string)($r['links_to'] ?? ''));
        $linkAbs = $link !== '' ? $absolutise($link, (string)$r['source_url']) : null;
        $useLarger = false;
        $url = $src;
        if ($linkAbs && in_array($extOf($linkAbs), $IMAGE_EXT, true) && $linkAbs !== $src) {
            $url = $linkAbs; $useLarger = true;
        }
        if (!$url || !in_array($extOf($url), $IMAGE_EXT, true)) {
            $badRows[] = $inv . '  ' . $r['legacy_key'] . '  no usable image url from ' . $r['src_raw'];
            continue;
        }

        $pagePath = parse_url((string)$r['source_url'], PHP_URL_PATH) ?: '';
        $pageKey = $inv . '|' . $r['legacy_key'] . '|' . $pagePath;

        if (!isset($plan[$pageKey])) {
            /* legacyUrl first: legacy_key alone is ambiguous, part06 exists in
               both the Perkins and the Reynolds inventories. */
            $entry = \craft\elements\Entry::find()->status(null)->legacyUrl($pagePath)->one();
            if (!$entry) { $entry = \craft\elements\Entry::find()->status(null)->legacyKey($r['legacy_key'])->one(); }
            if (!$entry) {
                $unresolvedPages[$pageKey] = true;
                $plan[$pageKey] = ['entry' => null, 'rows' => []];
            } else {
                $plan[$pageKey] = ['entry' => $entry, 'rows' => []];
            }
        }
        if (!$plan[$pageKey]['entry']) { continue; }

        $filename = $filenameOf($url);
        if ($filename === '') { $badRows[] = $inv . '  ' . $r['legacy_key'] . '  empty filename from ' . $url; continue; }

        if (isset($existingFilenames[strtolower($filename)])) {
            $dupSkipped[$filename] = true;
            continue;
        }

        $plan[$pageKey]['rows'][] = [
            'url' => $url,
            'filename' => $filename,
            'pos' => (int)($r['position_in_body'] ?? 0),
            'caption' => trim((string)($r['caption'] ?? '')),
            'credit' => trim((string)($r['credit_raw'] ?? '')),
            'larger' => $useLarger,
            'inv' => $inv,
            'key' => $r['legacy_key'],
            'anchor' => $anchors[$inv . '|' . $r['legacy_key']][(int)($r['position_in_body'] ?? 0)] ?? '',
        ];
        $seenUrl[$url] = $filename;
    }
}

/* One file downloads once even when several pages show it. */
$toDownload = [];
foreach ($plan as $pk => $p) {
    foreach ($p['rows'] as $row) { $toDownload[$row['url']] = $row['filename']; }
}

echo '=== plan ===' . PHP_EOL;
echo 'pages with a Craft record:   ' . count(array_filter($plan, fn($p) => $p['entry'] !== null)) . PHP_EOL;
echo 'pages with no Craft record:  ' . count($unresolvedPages) . PHP_EOL;
echo 'images to download:          ' . count($toDownload) . PHP_EOL;
echo 'skipped, already in volume:  ' . count($dupSkipped) . PHP_EOL;
echo 'skipped as navigation:       ' . count($chromeSkipped) . PHP_EOL;
echo 'rows with no usable url:     ' . count($badRows) . PHP_EOL;

/* ---------------------------------------------------------------- sizes */

$client = Craft::createGuzzleClient(['timeout' => 20, 'headers' => ['User-Agent' => $USER_AGENT]]);
$totalBytes = 0; $notFound = []; $sizeUnknown = 0;

if ($PROBE_SIZES && !$APPLY) {
    echo '=== probing content lengths, one per second ===' . PHP_EOL;
    foreach ($toDownload as $url => $fn) {
        try {
            $res = $client->head($url, ['http_errors' => false]);
            $code = $res->getStatusCode();
            if ($code === 404) { $notFound[] = $url; }
            elseif ($code >= 200 && $code < 300) {
                $len = (int)($res->getHeaderLine('Content-Length') ?: 0);
                if ($len) { $totalBytes += $len; } else { $sizeUnknown++; }
            } else { $notFound[] = $url . '  (HTTP ' . $code . ')'; }
        } catch (\Throwable $e) {
            $notFound[] = $url . '  (' . $e->getMessage() . ')';
        }
        usleep($DELAY_MS * 1000);
    }
}

/* ---------------------------------------------------------------- download */

$assetIdByUrl = [];
if ($APPLY) {
    if (!$folder) {
        $folder = new \craft\models\VolumeFolder([
            'parentId' => $rootFolder->id,
            'volumeId' => $volume->id,
            'name' => $SUBFOLDER,
            'path' => $SUBFOLDER . '/',
        ]);
        Craft::$app->assets->createFolder($folder);
        echo 'created folder ' . $SUBFOLDER . '/' . PHP_EOL;
    }
    echo '=== downloading, one per second ===' . PHP_EOL;
    foreach ($toDownload as $url => $filename) {
        $tmp = \craft\helpers\Assets::tempFilePath(pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg');
        try {
            $res = $client->get($url, ['sink' => $tmp, 'http_errors' => false]);
            $code = $res->getStatusCode();
            if ($code !== 200) {
                $notFound[] = $url . '  (HTTP ' . $code . ')';
                @unlink($tmp);
                usleep($DELAY_MS * 1000);
                continue;
            }
        } catch (\Throwable $e) {
            $notFound[] = $url . '  (' . $e->getMessage() . ')';
            @unlink($tmp);
            usleep($DELAY_MS * 1000);
            continue;
        }
        $bytes = @filesize($tmp) ?: 0;
        $totalBytes += $bytes;

        $asset = new \craft\elements\Asset();
        $asset->tempFilePath = $tmp;
        $asset->setFilename($filename);
        $asset->newFolderId = $folder->id;
        $asset->setVolumeId($volume->id);
        $asset->avoidFilenameConflicts = true;
        $asset->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
        if ($elements->saveElement($asset)) {
            $assetIdByUrl[$url] = $asset->id;
            $existingFilenames[strtolower($asset->filename)] = $asset->id;
            echo '  ' . str_pad($asset->filename, 46) . number_format($bytes) . ' bytes' . PHP_EOL;
        } else {
            echo '  SAVE FAILED ' . $filename . ': ' . json_encode($asset->getErrors()) . PHP_EOL;
        }
        usleep($DELAY_MS * 1000);
    }
}

/* ---------------------------------------------------------------- relate and place */

echo '=== records ===' . PHP_EOL;
$bodiesChanged = 0; $tokensInserted = 0; $relatedTotal = 0; $skippedHero = 0;
$noAnchor = []; $notLocated = [];

foreach ($plan as $pageKey => $p) {
    $entry = $p['entry'];
    if (!$entry || !count($p['rows'])) { continue; }
    if (!$hasField($entry, 'recordImages')) {
        echo '  no recordImages field on ' . $entry->section->handle . '/' . $entry->slug . PHP_EOL;
        continue;
    }

    $rows = $p['rows'];
    usort($rows, fn($a, $b) => $a['pos'] <=> $b['pos']);

    $existing = [];
    try { foreach ($entry->recordImages->all() as $a) { $existing[] = $a->id; } } catch (\Throwable $e) {}

    /* Assets that must not be placed inline: the band artwork and the social image. */
    $reserved = [];
    foreach (['featuredImage', 'bandImage'] as $h) {
        if (!$hasField($entry, $h)) { continue; }
        try { foreach ($entry->$h->all() as $a) { $reserved[$a->id] = true; } } catch (\Throwable $e) {}
    }

    $newIds = [];
    foreach ($rows as $row) {
        $id = $assetIdByUrl[$row['url']] ?? null;
        if ($id) { $newIds[] = $id; }
    }
    $finalIds = array_values(array_unique(array_merge($existing, $newIds)));
    $relatedTotal += count($newIds);

    /* Token numbers are 1-based into recordImages, which is what prose reads. */
    $tokenPlan = [];
    foreach ($rows as $row) {
        $id = $assetIdByUrl[$row['url']] ?? null;
        if ($id && isset($reserved[$id])) { $skippedHero++; continue; }
        $n = $id ? (array_search($id, $finalIds, true) + 1) : (count($existing) + count($tokenPlan) + 1);
        $tokenPlan[] = ['n' => $n, 'anchor' => $row['anchor'], 'filename' => $row['filename'], 'pos' => $row['pos']];
    }

    $body = (string)$entry->body;
    $newBody = $body;
    $inserted = 0;
    $unplaced = 0;

    $section = $entry->section->handle;
    $noTokens = in_array($section, $NO_TOKENS_IN, true);

    if (!$noTokens && trim($body) !== '') {
        /* Locate each anchor first, against the untouched body, so one insertion
           cannot move the ground under the next. Then splice from the bottom up. */
        $placements = [];
        foreach ($tokenPlan as $t) {
            $token = '[image:' . $t['n'] . ']';
            if (mb_strpos($body, $token) !== false) { continue; }
            $anchor = trim((string)$t['anchor']);
            if (mb_strlen($anchor) < 25) {
                $unplaced++;
                $noAnchor[] = $entry->slug . '  ' . $t['filename'] . '  (no text before it on the page)';
                continue;
            }
            /* Match the anchor allowing any whitespace between its words. */
            $words = preg_split('~\s+~u', $anchor, -1, PREG_SPLIT_NO_EMPTY);
            $pattern = '~' . implode('\s+', array_map(fn($w) => preg_quote($w, '~'), $words)) . '~su';
            if (!preg_match($pattern, $body, $m, PREG_OFFSET_CAPTURE)) {
                $unplaced++;
                $notLocated[] = $entry->slug . '  ' . $t['filename'] . '  (anchor not found in the stored body)';
                continue;
            }
            $charAt = $m[0][1] + strlen($m[0][0]);
            $placements[] = ['line' => substr_count(substr($body, 0, $charAt), "\n"), 'token' => $token];
        }

        usort($placements, fn($a, $b) => $b['line'] <=> $a['line']);
        $lines = preg_split("~\r\n|\n|\r~", $body);
        foreach ($placements as $pl) {
            array_splice($lines, min(count($lines), $pl['line'] + 1), 0, ['', $pl['token'], '']);
            $inserted++;
        }
        if ($inserted) {
            $newBody = trim(preg_replace("~\n{3,}~", "\n\n", implode("\n", $lines)));
        }
    } elseif ($noTokens) {
        $unplaced = count($tokenPlan);
    }

    if ($SHOW_BODY_FOR !== '' && $entry->slug === $SHOW_BODY_FOR) {
        echo '--- planned body for ' . $entry->slug . ' ---' . PHP_EOL;
        echo $newBody . PHP_EOL;
        echo '--- end ---' . PHP_EOL;
    }

    $willChangeBody = (!$noTokens && $inserted > 0 && $newBody !== $body);
    if ($willChangeBody) { $bodiesChanged++; $tokensInserted += $inserted; }

    echo str_pad($section, 14) . ' ' . str_pad($entry->slug, 50) . ' '
        . str_pad(count($rows) . ' img', 8) . ' '
        . ($noTokens
            ? 'no tokens, war memorial'
            : ($inserted . ' token(s) placed' . ($unplaced ? ', ' . $unplaced . ' related only' : '')))
        . PHP_EOL;

    if ($APPLY) {
        $sets = [];
        if ($finalIds !== $existing) { $sets['recordImages'] = $finalIds; }
        if ($willChangeBody) { $sets['body'] = $newBody; }
        if (!count($sets)) { continue; }
        foreach ($sets as $h => $v) {
            try { $entry->setFieldValue($h, $v); }
            catch (\Throwable $e) { echo '  set ' . $h . ' failed on ' . $entry->slug . ': ' . $e->getMessage() . PHP_EOL; }
        }
        if (!$elements->saveElement($entry)) {
            echo '  SAVE FAILED ' . $entry->slug . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
        }
    }
}

/* ---------------------------------------------------------------- report */

echo '=== summary ===' . PHP_EOL;
echo 'images to download:          ' . count($toDownload) . PHP_EOL;
echo 'skipped, already in volume:  ' . count($dupSkipped) . PHP_EOL;
echo 'skipped as navigation:       ' . count($chromeSkipped) . PHP_EOL;
echo 'relations to add:            ' . ($APPLY ? $relatedTotal : 'known once downloaded') . PHP_EOL;
echo 'bodies to change:            ' . $bodiesChanged . PHP_EOL;
echo 'tokens to insert:            ' . $tokensInserted . PHP_EOL;
echo 'skipped, hero or band image: ' . $skippedHero . PHP_EOL;
if ($PROBE_SIZES || $APPLY) {
    echo 'total bytes:                 ' . number_format($totalBytes) . PHP_EOL;
    if ($sizeUnknown) { echo '  (' . $sizeUnknown . ' gave no Content-Length)' . PHP_EOL; }
} else {
    echo 'total bytes:                 unknown, set $PROBE_SIZES = true for a paced HEAD pass' . PHP_EOL;
}

if ($chromeSkipped) {
    echo '=== navigation, more than ' . $CHROME_PAGE_THRESHOLD . ' pages ===' . PHP_EOL;
    foreach ($chromeSkipped as $src => $n) { echo '  ' . str_pad((string)$n, 4) . $src . PHP_EOL; }
}
if ($unresolvedPages) {
    echo '=== no Craft record for these pages, images left alone ===' . PHP_EOL;
    foreach (array_keys($unresolvedPages) as $k) { echo '  ' . $k . PHP_EOL; }
}
if ($badRows) {
    echo '=== rows with no usable image url ===' . PHP_EOL;
    foreach (array_slice($badRows, 0, 20) as $b) { echo '  ' . $b . PHP_EOL; }
    if (count($badRows) > 20) { echo '  ... and ' . (count($badRows) - 20) . ' more' . PHP_EOL; }
}
if ($noAnchor) {
    echo '=== related but not placed: nothing precedes the image on the page ===' . PHP_EOL;
    foreach (array_slice($noAnchor, 0, 25) as $r) { echo '  ' . $r . PHP_EOL; }
    if (count($noAnchor) > 25) { echo '  ... and ' . (count($noAnchor) - 25) . ' more' . PHP_EOL; }
}
if ($notLocated) {
    echo '=== related but not placed: anchor not found in the stored body ===' . PHP_EOL;
    foreach (array_slice($notLocated, 0, 25) as $r) { echo '  ' . $r . PHP_EOL; }
    if (count($notLocated) > 25) { echo '  ... and ' . (count($notLocated) - 25) . ' more' . PHP_EOL; }
}
if ($notFound) {
    echo '=== did not fetch ===' . PHP_EOL;
    foreach ($notFound as $u) { echo '  ' . $u . PHP_EOL; }
}
echo 'A second run is a no-op: every filename it created is then in the volume, and' . PHP_EOL;
echo 'a body already holding a token is not given another.' . PHP_EOL;
