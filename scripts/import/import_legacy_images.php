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
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$PROBE_SIZES = false;
/* Set to a slug to print that record's planned body, for reading before applying. */
$SHOW_BODY_FOR = '';

/* Crawl conduct, per GROK-CONTRACT.md: one request at a time, one second apart,
   a User-Agent naming the project and a contact address. Put a real mailbox in
   $CONTACT before running this against the live site. */
$CONTACT = 'nathan@starksocial.com';
$USER_AGENT = 'SCVHistory-Legacy-Images/1.0 (+https://scvhistory.com; contact: ' . $CONTACT . ')';
$DELAY_MS = 1000;

/* ------------------------------------------------------- batching and resume

   The three inventories here are 583 images. lw-features-images.json is
   expected to be around 40,000, and at the one request per second the crawl
   contract requires that is eleven hours of downloading. A run that either
   finishes or does not is no use at that length: a dropped connection, a
   laptop lid or a restart loses the whole thing.

   So the work is batched and the progress is on disk. $BATCH caps how many
   files one run fetches. Every outcome is written to the ledger as it happens,
   not at the end, so an interruption costs at most the one file in flight.
   The next run reads the ledger, subtracts what is done, and carries on.

   Outcomes are remembered separately because they are not the same:
     done       fetched and saved. Never attempted again.
     skipped    already in the volume, or navigation furniture. Same.
     gone       404 or 410. Permanent, and retrying it forty times is rude.
     failed     a timeout, a 5xx, a short read. Transient, retried next run
                until $MAX_TRIES, then left alone and reported.

   A page is only tokenised once every image it needs is in hand. Half a page's
   pictures placed and the rest waiting for the next batch would leave a body
   that the next run cannot safely add to. */
$BATCH = 0;          /* 0 means no limit, which is the old behaviour */
$MAX_TRIES = 3;
$LEDGER = \Craft::getAlias('@storage') . '/legacy-images-progress.json';
$RESET_LEDGER = false;   /* true forgets everything and starts over */
/* Relate what is already in the volume and fetch nothing. This is what repairs
   an asset that was downloaded on one run and left related to nothing. */
$RELATE_ONLY = false;

/* --------------------------------------------------------------- the drive

   DRIVE.md: read images from the drive, not from the live site. Leon's server
   is thirty years old and on someone else's hosting, and Reggie is the archival
   copy. The network is the exception and every use of it is reported, so the
   gap between the mirror and the pages is visible rather than papered over.

   $DRIVE_REQUIRED is the guard that makes that real. With the drive unmounted
   or unreadable, a run would otherwise fetch every file over the network and
   look like it worked. It refuses instead. Set it false only when fetching from
   the live site is what you actually mean to do. */
/* Where the mirror is, on the host and inside the container. The first of
   these that can actually be listed wins, so the same script works run either
   way and nobody has to remember to edit a path. */
$DRIVE_CANDIDATES = ['/Volumes/Reggie/SCVHistory', '/mnt/reggie', '/mnt/reggie/SCVHistory'];
$DRIVE = $DRIVE_CANDIDATES[0];
$DRIVE_REQUIRED = true;
/* Reported when the drive is missing a file the pages ask for, which is a hole
   in the mirror and belongs in the ledger. */
$FALLBACK_LIMIT = 0;   /* 0 means no cap on network fallbacks; set it to stop early */

$INVENTORIES = ['perkins-images', 'reynolds-images', 'warmemorial-images', 'lw-features-images'];
/* Where the page HTML lives, for recovering where each image actually sat. */
$SOURCE_OF = [
    'perkins-images' => 'perkins',
    'reynolds-images' => 'reynolds-full',
    'warmemorial-images' => 'warmemorial',
    'lw-features-images' => 'lw-features',
];

/* ------------------------------------------------------- the gallery rail

   lw-features-images.json carries 29,056 references to 5,973 distinct files,
   and 3,507 of those files end in t.jpg for 26,004 of the references. They are
   a gallery rail: the same strip of thumbnails repeated down the side of page
   after page.

   The rail's links_to does not point at the full-size picture. On 25,430 of
   25,992 thumbnail references it points at a page, an .htm, and on 83 more at
   an .html. Only 38 distinct thumbnails link to an image. So following the link
   recovers the picture on those 38 and on nothing else; the rest are navigation
   to other records and the picture they advertise belongs to the page they
   point at, not to this one.

   The rule, then:
     a thumbnail whose links_to is a page       skipped, it is furniture
     a thumbnail whose links_to is an image     the target is taken instead
     a thumbnail with no links_to at all        kept, because it is the only
                                                copy of that picture here

   That last case is six files. They are listed every run.

   Scoped to the inventories that actually have a rail. The three earlier ones
   were imported under the chrome rules above, which caught their furniture a
   different way, and applying this one to them retroactively would skip 286
   files that are already in the volume and were judged content at the time.
   A new rule belongs to the data it was written for. */
$THUMB_SHAPE = '~t\.jpe?g$~i';
$RAIL_INVENTORIES = ['lw-features-images'];
$VOLUME = 'archiveMedia';
$SUBFOLDER = 'legacy';
$CHROME_PAGE_THRESHOLD = 5;      /* more than this many pages means navigation */
/* The service seals on the war memorial pages are chrome however few pages carry
   them. Named rather than caught by a lower page threshold, because a threshold
   of 3 would take real content off a short series. */
$CHROME_FILENAMES = [
    'armylogo', 'navylogo', 'marinelogo', 'marineslogo', 'airforcelogo',
    'coastguardlogo', 'nationalguardlogo', 'merchantmarinelogo',
];
/* And anything else of that shape: a word, then "logo", and nothing else. */
$CHROME_SHAPE = '~^[a-z][a-z0-9-]*logo\.(png|gif|jpe?g)$~i';
$NO_TOKENS_IN = ['warMemorials'];
$IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

$root = \Craft::getAlias('@root');
$elements = Craft::$app->getElements();

/* ------------------------------------------------------------ the ledger */

$ledger = ['meta' => [], 'urls' => []];
if (!$RESET_LEDGER && file_exists($LEDGER)) {
    $decoded = json_decode(file_get_contents($LEDGER), true);
    if (is_array($decoded) && isset($decoded['urls'])) { $ledger = $decoded; }
}
$ledgerDirty = false;

$ledgerWrite = function () use (&$ledger, &$ledgerDirty, $LEDGER, $APPLY) {
    if (!$APPLY || !$ledgerDirty) { return; }
    $ledger['meta']['updated'] = (new DateTime())->format('c');
    $ledger['meta']['written_by'] = 'scripts/import/import_legacy_images.php';
    $ledger['meta']['counts'] = array_count_values(array_column($ledger['urls'], 'state'));
    /* Written whole and renamed, so a kill in the middle cannot leave half a
       ledger, which would be worse than none. */
    $tmp = $LEDGER . '.tmp';
    file_put_contents($tmp, json_encode($ledger, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    rename($tmp, $LEDGER);
    $ledgerDirty = false;
};

$ledgerNote = function (string $url, string $state, string $detail = '') use (&$ledger, &$ledgerDirty) {
    $prev = $ledger['urls'][$url] ?? null;
    $ledger['urls'][$url] = [
        'state' => $state,
        'tries' => ($state === 'failed') ? (($prev['tries'] ?? 0) + 1) : ($prev['tries'] ?? 0),
        'at' => (new DateTime())->format('c'),
        'detail' => $detail,
    ];
    $ledgerDirty = true;
};

/* ----------------------------------------------------------- the drive */

$driveReadable = false;
foreach ($DRIVE_CANDIDATES as $cand) {
    if (is_dir($cand) && is_readable($cand) && @scandir($cand) !== false) {
        $DRIVE = $cand; $driveReadable = true; break;
    }
}
$driveMountRoot = '/Volumes';
echo 'drive: ' . ($driveReadable ? $DRIVE . '  readable'
    : implode(', ', $DRIVE_CANDIDATES) . '  NONE READABLE') . PHP_EOL;

if (!$driveReadable) {
    /* Three different causes, and they need three different fixes, so the
       diagnosis is worth getting right rather than saying "mount it". */
    echo PHP_EOL . 'The archive drive is not readable from here.' . PHP_EOL;
    if (!is_dir($driveMountRoot)) {
        echo PHP_EOL . 'There is no ' . $driveMountRoot . ' at all, so this is running inside the DDEV' . PHP_EOL;
        echo 'container, which only sees the project directory. Host volumes are not passed' . PHP_EOL;
        echo 'through by default and no amount of permission on the Mac changes that.' . PHP_EOL;
        echo PHP_EOL . 'Bind the drive in, in .ddev/docker-compose.drive.yaml:' . PHP_EOL;
        echo PHP_EOL . '  services:' . PHP_EOL;
        echo '    web:' . PHP_EOL;
        echo '      volumes:' . PHP_EOL;
        echo '        - "/Volumes/Reggie/SCVHistory:/mnt/reggie:ro"' . PHP_EOL;
        echo PHP_EOL . 'then ddev restart, and set $DRIVE = \'/mnt/reggie\' here. Read-only on purpose:' . PHP_EOL;
        echo 'nothing in this repository has any business writing to the archive drive.' . PHP_EOL;
        echo 'Or run this on the host with the plain php binary instead of through ddev.' . PHP_EOL;
    } elseif (is_dir(dirname($DRIVE)) || @stat($DRIVE) !== false) {
        echo PHP_EOL . 'The mount point exists but its contents cannot be listed. On macOS that is' . PHP_EOL;
        echo 'the privacy system withholding a removable volume, not a broken disk: a drive' . PHP_EOL;
        echo 'in that state still answers stat and mount while refusing to be read.' . PHP_EOL;
        echo PHP_EOL . 'System Settings, Privacy and Security, Full Disk Access, and add the program' . PHP_EOL;
        echo 'running this. Then check with: ls /Volumes/Reggie' . PHP_EOL;
    } else {
        echo 'It is not mounted. Plug it in, or point $DRIVE somewhere else.' . PHP_EOL;
    }

    /* The decision is deferred until the plan is built. Relating a file that is
       already in the volume needs no drive and no network, and stopping the
       whole run here would block that too. The guard fires below, only if there
       is something to fetch. */
    if (!$APPLY) {
        echo PHP_EOL . 'Planning anyway, because a dry run fetches nothing. Every file below will be' . PHP_EOL;
        echo 'reported as "drive unknown" rather than as present or missing.' . PHP_EOL;
    }
}

/** Where a page's image would sit on the mirror, or null if it is not there. */
$onDrive = function (string $url) use ($DRIVE, $driveReadable): ?string {
    if (!$driveReadable) { return null; }
    $p = parse_url($url);
    if (!$p || empty($p['path'])) { return null; }
    /* The mirror is the site root, so the url path is the path on the drive.
       Only scvhistory.com is mirrored; scvleon.com and scvtv.com are not. */
    $host = strtolower($p['host'] ?? '');
    if ($host !== '' && !str_ends_with($host, 'scvhistory.com')) { return null; }
    $file = $DRIVE . '/' . ltrim(rawurldecode($p['path']), '/');
    if (is_file($file)) { return $file; }
    /* HFS+ is case-insensitive, but the mount may not be, so try the folder. */
    $dir = dirname($file);
    if (is_dir($dir)) {
        $want = strtolower(basename($file));
        foreach (@scandir($dir) ?: [] as $f) {
            if (strtolower($f) === $want && is_file($dir . '/' . $f)) { return $dir . '/' . $f; }
        }
    }
    return null;
};

/** Has this url been settled, so a later run need not look at it again? */
$ledgerSettled = function (string $url) use (&$ledger, $MAX_TRIES): bool {
    $r = $ledger['urls'][$url] ?? null;
    if (!$r) { return false; }
    if (in_array($r['state'], ['done', 'skipped', 'gone'], true)) { return true; }
    return ($r['tries'] ?? 0) >= $MAX_TRIES;
};

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
if (strpos($CONTACT, '@') === false) {
    echo 'NOTE: $CONTACT is not a mailbox. Put one there before running this against the live site.' . PHP_EOL;
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
$railSkipped = []; $railFollowed = []; $railOnlyCopy = [];
$badRows = [];
/* Filled by the plan for a file already in the volume, and by the download loop
   for one that is not. Either way it is what the relation is built from. */
$assetIdByUrl = [];
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
        $base = basename(parse_url($src, PHP_URL_PATH) ?: $src);
        $stem = strtolower(pathinfo($base, PATHINFO_FILENAME));
        if (count($pages) > $CHROME_PAGE_THRESHOLD) {
            $chrome[$src] = count($pages) . ' pages';
        } elseif (in_array($stem, $CHROME_FILENAMES, true)) {
            $chrome[$src] = 'named service seal, on ' . count($pages) . ' page(s)';
        } elseif (preg_match($CHROME_SHAPE, $base)) {
            $chrome[$src] = 'matches the logo shape, on ' . count($pages) . ' page(s)';
        }
    }
    foreach ($chrome as $src => $why) { $chromeSkipped[$inv . '  ' . $src] = $why; }

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

        /* The gallery rail. A thumbnail that links to a page is furniture and
           the picture it advertises belongs to that page. A thumbnail with no
           link at all is the only copy here and is kept. */
        if ($src && in_array($inv, $RAIL_INVENTORIES, true)
            && preg_match($THUMB_SHAPE, parse_url($src, PHP_URL_PATH) ?: '')) {
            if ($useLarger) {
                $railFollowed[$src] = $url;
            } elseif ($link !== '') {
                $railSkipped[$src] = ($railSkipped[$src] ?? 0) + 1;
                continue;
            } else {
                $railOnlyCopy[$src][$r['legacy_key']] = true;
            }
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
            /* Already in the volume, so nothing to download. It still belongs
               in the plan.

               This was `continue`, and that is why 25 assets sit in the volume
               related to nothing. The relation is built from $assetIdByUrl,
               which was filled only by the download loop, so a file fetched on
               one run and not attached on that run could never be attached by
               any later run: the second run saw the filename, skipped the row,
               and the picture stayed orphaned. Downloading and relating are
               two different jobs and only one of them is done when the file
               is already here. */
            $dupSkipped[$filename] = true;
            $assetIdByUrl[$url] = $existingFilenames[strtolower($filename)];
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

if ($railSkipped || $railFollowed || $railOnlyCopy) {
    echo PHP_EOL . '=== the gallery rail ===' . PHP_EOL;
    echo 'thumbnails skipped as furniture:   ' . count($railSkipped)
        . ' files, ' . array_sum($railSkipped) . ' references' . PHP_EOL;
    echo 'thumbnails whose link is a picture: ' . count($railFollowed)
        . ', the target taken instead' . PHP_EOL;
    echo 'thumbnails with no link at all:     ' . count($railOnlyCopy)
        . ', kept because they are the only copy here' . PHP_EOL;
    foreach ($railOnlyCopy as $u => $pages) {
        echo '   ' . str_pad(basename(parse_url($u, PHP_URL_PATH) ?: $u), 34)
            . 'on ' . implode(', ', array_keys($pages)) . PHP_EOL;
    }
}

/* ------------------------------------------------- subtract what is settled */

$alreadySettled = 0; $retrying = 0;
foreach (array_keys($toDownload) as $u) {
    if ($ledgerSettled($u)) { unset($toDownload[$u]); $alreadySettled++; }
    elseif (isset($ledger['urls'][$u])) { $retrying++; }
}
$outstanding = count($toDownload);

if (!$driveReadable && $DRIVE_REQUIRED && $APPLY && $outstanding > 0) {
    echo PHP_EOL . 'Stopping. ' . $outstanding . ' file(s) would be fetched and the drive is not' . PHP_EOL;
    echo 'readable, so they would come from Leon\'s server. $DRIVE_REQUIRED is true so that' . PHP_EOL;
    echo 'cannot happen by accident. Set it false only if that is what you mean to do.' . PHP_EOL;
    echo PHP_EOL . 'Relating files already in the volume does not need the drive. To do only' . PHP_EOL;
    echo 'that, set $RELATE_ONLY = true.' . PHP_EOL;
    if (!$RELATE_ONLY) { return; }
    echo PHP_EOL . '$RELATE_ONLY is on: nothing will be fetched, relations only.' . PHP_EOL;
    $toDownload = [];
    $outstanding = 0;
}
$deferred = [];
if ($BATCH > 0 && $outstanding > $BATCH) {
    $deferred = array_slice($toDownload, $BATCH, null, true);
    $toDownload = array_slice($toDownload, 0, $BATCH, true);
}

if ($ledger['urls']) {
    echo PHP_EOL . '=== resume ===' . PHP_EOL;
    echo 'ledger: ' . basename($LEDGER) . ', ' . count($ledger['urls']) . ' urls remembered' . PHP_EOL;
    foreach (array_count_values(array_column($ledger['urls'], 'state')) as $st => $n) {
        echo '   ' . str_pad($st, 12) . $n . PHP_EOL;
    }
    echo 'settled and skipped this run: ' . $alreadySettled . PHP_EOL;
    if ($retrying) { echo 'retrying after an earlier failure: ' . $retrying . PHP_EOL; }
}
echo 'outstanding:                 ' . $outstanding . PHP_EOL;
if ($BATCH > 0) {
    echo 'this run will fetch:         ' . count($toDownload) . ' (batch limit ' . $BATCH . ')' . PHP_EOL;
    echo 'left for the next run:       ' . count($deferred) . PHP_EOL;
    $secs = count($toDownload) * ($DELAY_MS / 1000);
    echo 'at ' . $DELAY_MS . 'ms apart that is about ' . gmdate('H:i:s', (int)$secs) . PHP_EOL;
} elseif ($outstanding > 1000) {
    $secs = $outstanding * ($DELAY_MS / 1000);
    echo PHP_EOL . 'WARNING: ' . $outstanding . ' images at ' . $DELAY_MS . 'ms apart is about '
        . round($secs / 3600, 1) . ' hours in one run, and $BATCH is 0.' . PHP_EOL;
    echo 'Set $BATCH to something that finishes inside a sitting. The ledger makes a' . PHP_EOL;
    echo 'stopped run cost nothing; an unbatched one that dies at hour nine costs nine hours.' . PHP_EOL;
}

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

$fromDrive = []; $fromNetwork = []; $fellBack = [];
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

        /* The drive first, always. A copy from the mirror costs nothing and
           asks nobody's server for anything, so there is no pause after it. */
        $local = $onDrive($url);
        if ($local !== null) {
            if (@copy($local, $tmp)) {
                $fromDrive[] = $url;
                $bytes = @filesize($tmp) ?: 0;
                $totalBytes += $bytes;
                goto saveAsset;
            }
            $fellBack[$url] = 'on the drive but could not be read';
        } elseif ($driveReadable) {
            /* A file the pages ask for and the mirror does not have. DRIVE.md
               calls that a hole in the mirror, and it is reported as one. */
            $fellBack[$url] = 'not on the drive';
        } else {
            $fellBack[$url] = 'drive unreadable';
        }
        if ($FALLBACK_LIMIT > 0 && count($fromNetwork) >= $FALLBACK_LIMIT) {
            $deferred[$url] = $filename;
            continue;
        }
        $fromNetwork[] = $url;

        try {
            $res = $client->get($url, ['sink' => $tmp, 'http_errors' => false]);
            $code = $res->getStatusCode();
            if ($code !== 200) {
                $notFound[] = $url . '  (HTTP ' . $code . ')';
                /* 404 and 410 are answers, not failures. Asking again tomorrow
                   is both pointless and impolite. */
                $ledgerNote($url, in_array($code, [404, 410], true) ? 'gone' : 'failed', 'HTTP ' . $code);
                $ledgerWrite();
                @unlink($tmp);
                usleep($DELAY_MS * 1000);
                continue;
            }
        } catch (\Throwable $e) {
            $notFound[] = $url . '  (' . $e->getMessage() . ')';
            $ledgerNote($url, 'failed', mb_substr($e->getMessage(), 0, 120));
            $ledgerWrite();
            @unlink($tmp);
            usleep($DELAY_MS * 1000);
            continue;
        }
        $bytes = @filesize($tmp) ?: 0;
        $totalBytes += $bytes;

        saveAsset:
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
            /* Written now, not at the end of the loop. An interruption then
               costs the one file in flight instead of the whole run. */
            $ledgerNote($url, 'done', $asset->filename);
            $ledgerWrite();
            echo '  ' . ($local !== null ? 'drive  ' : 'network')
                . ' ' . str_pad($asset->filename, 44) . number_format($bytes) . ' bytes' . PHP_EOL;
        } else {
            $ledgerNote($url, 'failed', 'save: ' . mb_substr(json_encode($asset->getErrors()), 0, 120));
            $ledgerWrite();
            echo '  SAVE FAILED ' . $filename . ': ' . json_encode($asset->getErrors()) . PHP_EOL;
        }
        /* Only the network gets paced. */
        if ($local === null) { usleep($DELAY_MS * 1000); }
    }
}

/* ---------------------------------------------------------------- relate and place */

echo '=== records ===' . PHP_EOL;
$bodiesChanged = 0; $tokensInserted = 0; $relatedTotal = 0; $skippedHero = 0;
$noAnchor = []; $notLocated = [];

$heldForNextRun = [];

foreach ($plan as $pageKey => $p) {
    $entry = $p['entry'];
    if (!$entry || !count($p['rows'])) { continue; }

    /* A page is placed only once every picture it needs is in hand. Tokenising
       three of a page's eight and leaving the rest for the next batch gives a
       body the next run cannot safely add to: the anchors it matched against
       have moved, and a second pass would either duplicate tokens or miss the
       positions. So a page with anything still outstanding waits, whole. */
    $waiting = 0;
    foreach ($p['rows'] as $r) {
        $u = $r['url'] ?? '';
        if ($u === '') { continue; }
        if (isset($assetIdByUrl[$u])) { continue; }
        if (isset($existingFilenames[strtolower($r['filename'] ?? '')])) { continue; }
        if (!$ledgerSettled($u)) { $waiting++; }
    }
    if ($waiting > 0) {
        $heldForNextRun[$entry->section->handle . '/' . $entry->slug] = $waiting;
        continue;
    }
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
if ($APPLY) {
    echo 'read from the drive:         ' . count($fromDrive) . PHP_EOL;
    echo 'fetched over the network:    ' . count($fromNetwork) . PHP_EOL;
    if ($fellBack) {
        $why = array_count_values($fellBack);
        foreach ($why as $reason => $n) { echo '   ' . str_pad($reason, 32) . $n . PHP_EOL; }
        $gaps = array_keys(array_filter($fellBack, fn($r) => $r === 'not on the drive'));
        if ($gaps) {
            echo PHP_EOL . 'ASKED FOR BY A PAGE AND NOT ON THE MIRROR, ' . count($gaps) . ':' . PHP_EOL;
            foreach (array_slice($gaps, 0, 20) as $g) { echo '   ' . $g . PHP_EOL; }
            if (count($gaps) > 20) { echo '   and ' . (count($gaps) - 20) . ' more' . PHP_EOL; }
        }
    }
}
echo 'relations to add:            ' . ($APPLY ? $relatedTotal : 'known once downloaded') . PHP_EOL;
echo 'bodies to change:            ' . $bodiesChanged . PHP_EOL;
echo 'tokens to insert:            ' . $tokensInserted . PHP_EOL;
echo 'skipped, hero or band image: ' . $skippedHero . PHP_EOL;

if ($heldForNextRun) {
    echo PHP_EOL . 'pages held whole until their pictures are all in: ' . count($heldForNextRun) . PHP_EOL;
    $i = 0;
    foreach ($heldForNextRun as $slug => $n) {
        echo '   ' . str_pad($slug, 52) . $n . ' still outstanding' . PHP_EOL;
        if (++$i >= 10) { echo '   and ' . (count($heldForNextRun) - 10) . ' more' . PHP_EOL; break; }
    }
}

$ledgerWrite();
if ($APPLY && ($deferred || $heldForNextRun)) {
    echo PHP_EOL . str_repeat('-', 70) . PHP_EOL;
    echo 'NOT FINISHED, AND THAT IS THE DESIGN.' . PHP_EOL;
    echo '   images left to fetch: ' . count($deferred) . PHP_EOL;
    echo '   pages left to place:  ' . count($heldForNextRun) . PHP_EOL;
    echo 'Run this again. It reads ' . basename($LEDGER) . ', subtracts what is done and' . PHP_EOL;
    echo 'carries on. Nothing already fetched is fetched twice.' . PHP_EOL;
} elseif ($APPLY) {
    echo PHP_EOL . 'Nothing outstanding. The ledger can be deleted, or left as a record of what was fetched.' . PHP_EOL;
}
if ($PROBE_SIZES || $APPLY) {
    echo 'total bytes:                 ' . number_format($totalBytes) . PHP_EOL;
    if ($sizeUnknown) { echo '  (' . $sizeUnknown . ' gave no Content-Length)' . PHP_EOL; }
} else {
    echo 'total bytes:                 unknown, set $PROBE_SIZES = true for a paced HEAD pass' . PHP_EOL;
}

if ($chromeSkipped) {
    echo '=== skipped as navigation ===' . PHP_EOL;
    foreach ($chromeSkipped as $src => $why) { echo '  ' . str_pad($why, 34) . $src . PHP_EOL; }
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
