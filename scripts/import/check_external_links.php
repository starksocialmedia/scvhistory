/**
 * Requests every external link the archive holds and reports what answers.
 *
 * Read only in both directions: it writes nothing to Craft, and its own output
 * goes to web/review/link-check.json. Nothing here changes a record, ever.
 *
 * Eight Find A Grave URLs turned out to serve a different person entirely,
 * because Find A Grave resolves by the memorial number and ignores the slug, so
 * /memorial/1819/christopher-houston-carson serves Harry Chapin's grave and
 * answers 200 while doing it. A status code is therefore not enough. For a
 * memorial page this reads the name, dates and cemetery the page actually shows
 * and compares the surname against the record the link is attached to; for
 * Wikipedia and Wikidata it compares the page title or label the same way.
 * Anything that does not look like the record it hangs off is reported under
 * "does not match the record".
 *
 * Built to be run periodically. A previous link-check.json is read back in, and
 * a link checked within $RECHECK_AFTER_DAYS is carried over rather than
 * re-requested, so a weekly run costs a few minutes rather than an hour and a
 * link that has just broken still surfaces. Delete the file to force a full
 * pass, or set $RECHECK_AFTER_DAYS to 0.
 *
 * One request a second, one at a time, with a User-Agent naming the project and
 * a contact address, and slower still for a host that asks for room. Nothing
 * here is a crawl; it is a few hundred requests against pages we already link
 * to.
 *
 * A 429 or a 5xx is not a verdict on the link, it is the far end declining to
 * answer today. Those are counted as "could not check", reported apart from
 * broken links, and never carried over, so the next run tries them again.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/check_external_links.php'))"
 */

$RECHECK_AFTER_DAYS = 7;
$DELAY_SECONDS      = 1;
/* Find A Grave answers 429 under a steady one-a-second, and a run that reports
   twelve throttled requests as twelve broken links is worse than useless. Hosts
   that ask for room get it. Six seconds still drew a 429 on half of them, so it
   is fifteen; there are only eighteen memorial links in the archive, and a run
   that has to come back for a few of them next week is no disaster either. */
$HOST_DELAY = ['www.findagrave.com' => 15];
$TIMEOUT            = 25;
$MAX                = 0;   /* 0 for all; set a number to sample while testing */
$UA = 'SCVHistory-LinkCheck/1.0 (+https://scvhistory.com; contact: nathan@starksocial.com)';

$root = \Craft::getAlias('@root');
$out  = \Craft::getAlias('@webroot') . '/review/link-check.json';

$LEGACY_HOST = rtrim((string)(Craft::$app->getConfig()->getCustom()->legacyHost ?? 'https://scvhistory.com'), '/');

/* ---------------------------------------------------------------- helpers */

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};
$plain = function (\craft\base\ElementInterface $el, string $handle) use ($hasField): string {
    if (!$hasField($el, $handle)) { return ''; }
    try { return trim((string)$el->getFieldValue($handle)); } catch (\Throwable $ex) { return ''; }
};
$legacyAbs = function (string $v) use ($LEGACY_HOST): string {
    $v = trim($v);
    if ($v === '') { return ''; }
    if (str_starts_with($v, 'http://') || str_starts_with($v, 'https://')) { return $v; }
    if (str_starts_with($v, '//')) { return 'https:' . $v; }
    return $LEGACY_HOST . '/' . ltrim($v, '/');
};
$fold = function (string $s): string {
    $s = mb_strtolower(trim($s));
    $from = ["\u{00E1}","\u{00E0}","\u{00E2}","\u{00E4}","\u{00E9}","\u{00E8}","\u{00EA}","\u{00EB}","\u{00ED}","\u{00EC}","\u{00EE}","\u{00EF}","\u{00F3}","\u{00F2}","\u{00F4}","\u{00F6}","\u{00FA}","\u{00F9}","\u{00FB}","\u{00FC}","\u{00F1}","\u{00E7}","\u{2019}"];
    $to   = ['a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','u','u','u','u','n','c',''];
    $s = str_replace($from, $to, $s);
    $s = preg_replace('~[^a-z0-9 ]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};
$tag = function (string $html, string $pattern): string {
    if (!preg_match($pattern, $html, $m)) { return ''; }
    return trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
};

/* ------------------------------------------------- what each field becomes */

/* handle => [how to turn the stored value into a URL, what kind of check] */
$URL_FIELDS = [
    'personWikipediaUrl' => ['as_is',   'wikipedia'],
    'orgWikipediaUrl'    => ['as_is',   'wikipedia'],
    'placeWikipediaUrl'  => ['as_is',   'wikipedia'],
    'eventWikipediaUrl'  => ['as_is',   'wikipedia'],
    'mpWikipediaUrl'     => ['as_is',   'wikipedia'],
    'personGraveUrl'     => ['as_is',   'findagrave'],
    'mpFindAGraveUrl'    => ['as_is',   'findagrave'],
    'obitGraveUrl'       => ['as_is',   'findagrave'],
    'orgWebsite'         => ['as_is',   'plain'],
    'groupWebsite'       => ['as_is',   'plain'],
    'placeChlUrl'        => ['as_is',   'plain'],
    'placeScvhlUrl'      => ['as_is',   'plain'],
    'wikidataId'         => ['wikidata', 'wikidata'],
    'viafId'             => ['viaf',    'plain'],
    'gnisId'             => ['gnis',    'plain'],
    'archiveUrl'         => ['as_is',   'plain'],
    'sourcePath'         => ['as_is',   'legacy'],
    'legacyUrl'          => ['legacy',  'legacy'],
    'personLegacyUrl'    => ['legacy',  'legacy'],
    'wmLegacyUrl'        => ['legacy',  'legacy'],
    'mpLegacyUrl'        => ['legacy',  'legacy'],
    'obitLegacyUrl'      => ['legacy',  'legacy'],
    'orgLegacyUrl'       => ['legacy',  'legacy'],
    'placeLegacyUrl'     => ['legacy',  'legacy'],
    'groupLegacyUrl'     => ['legacy',  'legacy'],
    'eventLegacyUrl'     => ['legacy',  'legacy'],
];

$build = function (string $how, string $value) use ($legacyAbs): string {
    return match ($how) {
        'wikidata' => 'https://www.wikidata.org/wiki/' . rawurlencode($value),
        'viaf'     => 'https://viaf.org/viaf/' . rawurlencode($value) . '/',
        'gnis'     => 'https://edits.nationalmap.gov/apps/gaz-domestic/public/summary/' . rawurlencode($value),
        'legacy'   => $legacyAbs($value),
        default    => trim($value),
    };
};

/* ---------------------------------------------------------------- collect */

$SECTIONS = ['articles','persons','places','organizations','groups','events','collections',
             'warMemorials','militaryProfiles','obituaries','photographs','documents','pages'];

$links = [];
foreach ($SECTIONS as $sec) {
    foreach (\craft\elements\Entry::find()->section($sec)->status(null)->limit(null)->all() as $e) {
        foreach ($URL_FIELDS as $handle => [$how, $kind]) {
            $raw = $plain($e, $handle);
            if ($raw === '') { continue; }
            $url = $build($how, $raw);
            if ($url === '' || !preg_match('~^https?://~i', $url)) { continue; }
            $links[] = [
                'url' => $url, 'kind' => $kind, 'field' => $handle, 'stored' => $raw,
                'section' => $sec, 'id' => $e->id, 'title' => (string)$e->title,
                'record' => (string)$e->url,
            ];
        }
    }
}

/* sourcePath and legacyUrl usually hold the same page, so one record can offer
   the same URL twice. Request it once and remember both fields it came from. */
$merged = [];
foreach ($links as $l) {
    $k = $l['url'] . '|' . $l['id'];
    if (isset($merged[$k])) {
        $merged[$k]['field'] = $merged[$k]['field'] . ', ' . $l['field'];
        continue;
    }
    $merged[$k] = $l;
}
$duplicates = count($links) - count($merged);
$links = array_values($merged);

echo 'external links held by the archive: ' . count($links)
    . ($duplicates ? ', after folding ' . $duplicates . ' held twice on one record' : '') . PHP_EOL;
$byKind = [];
foreach ($links as $l) { $byKind[$l['kind']] = ($byKind[$l['kind']] ?? 0) + 1; }
ksort($byKind);
foreach ($byKind as $k => $n) { echo '  ' . str_pad($k, 12) . $n . PHP_EOL; }

/* ---------------------------------------------- carry over a recent result */

$previous = [];
if (file_exists($out)) {
    $prev = json_decode(file_get_contents($out), true);
    foreach (($prev['links'] ?? []) as $r) {
        if (!empty($r['url']) && !empty($r['checked'])) { $previous[$r['url'] . '|' . $r['id']] = $r; }
    }
    echo 'previous run: ' . count($previous) . ' results on file' . PHP_EOL;
}
$cutoff = $RECHECK_AFTER_DAYS > 0 ? (new DateTime('-' . $RECHECK_AFTER_DAYS . ' days')) : null;

/* ---------------------------------------------------------------- request */

$fetch = function (string $url) use ($UA, $TIMEOUT): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 6,
        CURLOPT_TIMEOUT        => $TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => $UA,
        CURLOPT_HTTPHEADER     => ['Accept-Language: en-US,en;q=0.9'],
        CURLOPT_ENCODING       => '',
    ]);
    $body = curl_exec($ch);
    $res = [
        'status' => (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE),
        'final'  => (string)curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
        'error'  => curl_error($ch) ?: null,
    ];
    curl_close($ch);
    return [$res, is_string($body) ? $body : ''];
};

$results = [];
$requested = 0;
$carried = 0;
$n = 0;

foreach ($links as $l) {
    $n++;
    if ($MAX > 0 && $n > $MAX) { break; }
    $key = $l['url'] . '|' . $l['id'];

    /* A result is only worth carrying over if it was actually a result. The
       status is read rather than the unchecked flag, because a file written by
       an older version of this script has no flag and would otherwise carry a
       throttled row forward for ever. */
    $prevStatus = (int)($previous[$key]['status'] ?? 0);
    $prevUsable = isset($previous[$key]) && $prevStatus !== 0 && $prevStatus !== 429 && $prevStatus < 500;
    if ($prevUsable && $cutoff !== null) {
        $when = \DateTime::createFromFormat(DateTime::ATOM, $previous[$key]['checked']) ?: null;
        if ($when && $when > $cutoff) { $results[] = $previous[$key]; $carried++; continue; }
    }

    [$res, $body] = $fetch($l['url']);
    $requested++;

    $row = $l;
    $row['status'] = $res['status'];
    $row['final'] = $res['final'];
    $row['error'] = $res['error'];
    $row['redirected'] = $res['final'] !== '' && rtrim($res['final'], '/') !== rtrim($l['url'], '/');
    $row['checked'] = (new DateTime())->format(DateTime::ATOM);
    $row['page'] = null;
    $row['mismatch'] = null;
    /* 429 is the host declining, 5xx is the host failing. Neither says anything
       about whether our link is right. */
    $row['unchecked'] = $res['status'] === 429 || $res['status'] >= 500 || $res['status'] === 0;

    /* Does the page look like the record it is attached to? A status code alone
       cannot answer that, and on Find A Grave it has already been wrong eight
       times. */
    if ($res['status'] === 200 && $body !== '') {
        $surname = '';
        $words = preg_split('~\s+~u', $fold($l['title']), -1, PREG_SPLIT_NO_EMPTY);
        if ($words) { $surname = end($words); }

        if ($l['kind'] === 'findagrave') {
            $page = [
                'name'     => $tag($body, '~id="bio-name"[^>]*>(.*?)</h1>~s'),
                'birth'    => $tag($body, '~id="birthDateLabel"[^>]*>(.*?)</~s'),
                'death'    => $tag($body, '~id="deathDateLabel"[^>]*>(.*?)</~s'),
                'cemetery' => $tag($body, '~id="cemeteryNameLabel"[^>]*>(.*?)</~s'),
            ];
            $page['name'] = trim(preg_replace('~\s*(VVeteran|Veteran|Famous memorial)\s*~u', ' ', $page['name']));
            $row['page'] = $page;
            if ($page['name'] !== '' && $surname !== '' && mb_strlen($surname) > 2
                && !str_contains($fold($page['name']), $surname)) {
                $row['mismatch'] = 'the memorial is for ' . $page['name']
                    . ($page['birth'] || $page['death'] ? ', ' . $page['birth'] . ' to ' . $page['death'] : '')
                    . ($page['cemetery'] ? ', ' . $page['cemetery'] : '');
            }
        } elseif ($l['kind'] === 'wikipedia') {
            $t = $tag($body, '~<title>(.*?)</title>~s');
            $t = trim(preg_replace('~\s*[-–]\s*Wikipedia$~u', '', $t));
            $row['page'] = ['name' => $t];
            if ($t !== '' && $surname !== '' && mb_strlen($surname) > 2 && !str_contains($fold($t), $surname)) {
                $row['mismatch'] = 'the article is "' . $t . '"';
            }
        } elseif ($l['kind'] === 'wikidata') {
            $t = $tag($body, '~<title>(.*?)</title>~s');
            $t = trim(preg_replace('~\s*-\s*Wikidata$~u', '', $t));
            $row['page'] = ['name' => $t];
            if ($t !== '' && $surname !== '' && mb_strlen($surname) > 2 && !str_contains($fold($t), $surname)
                && $l['section'] === 'persons') {
                $row['mismatch'] = 'the item is "' . $t . '"';
            }
        } elseif ($l['kind'] === 'legacy') {
            $row['page'] = ['name' => $tag($body, '~<title>(.*?)</title>~s')];
        }
    }

    $results[] = $row;
    echo str_pad((string)$row['status'], 5) . str_pad($l['kind'], 12) . str_pad(mb_substr($l['title'], 0, 30), 32)
        . mb_substr($l['url'], 0, 62)
        . ($row['mismatch'] ? '   MISMATCH'
            : ($row['unchecked'] ? '   not checked, try again'
            : ($row['redirected'] ? '   redirected' : ''))) . PHP_EOL;

    $host = parse_url($l['url'], PHP_URL_HOST) ?: '';
    $wait = $HOST_DELAY[$host] ?? $DELAY_SECONDS;
    if ($wait > 0) { sleep($wait); }
}

/* ---------------------------------------------------------------- report */

$ok = $notFound = $gone = $other = $failed = $redirects = 0;
$mismatches = []; $broken = []; $movedOff = []; $unchecked = [];

foreach ($results as $r) {
    $s = (int)$r['status'];
    if (!empty($r['unchecked'])) { $unchecked[] = $r; $failed++; }
    elseif ($s === 200) { $ok++; }
    elseif ($s === 404) { $notFound++; $broken[] = $r; }
    elseif ($s === 410) { $gone++; $broken[] = $r; }
    else { $other++; $broken[] = $r; }
    if (!empty($r['redirected'])) {
        $redirects++;
        $a = parse_url($r['url'], PHP_URL_HOST);
        $b = parse_url((string)$r['final'], PHP_URL_HOST);
        if ($a && $b && $a !== $b) { $movedOff[] = $r; }
    }
    if (!empty($r['mismatch'])) { $mismatches[] = $r; }
}

echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
echo 'checked ' . count($results) . ' links, ' . $requested . ' requested, ' . $carried . ' carried over' . PHP_EOL;
echo '  resolve (200):        ' . $ok . PHP_EOL;
echo '  not found (404):      ' . $notFound . PHP_EOL;
echo '  gone (410):           ' . $gone . PHP_EOL;
echo '  other status:         ' . $other . PHP_EOL;
echo '  could not check:      ' . $failed . '  (throttled or failing at the far end, not a verdict)' . PHP_EOL;
echo '  redirected:           ' . $redirects . ', of which ' . count($movedOff) . ' to another host' . PHP_EOL;
echo '  page is about someone else: ' . count($mismatches) . PHP_EOL;

if ($mismatches) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'DOES NOT MATCH THE RECORD, ' . count($mismatches) . '. A 200 here means nothing:' . PHP_EOL;
    echo 'the page answers, it is simply about a different subject.' . PHP_EOL;
    echo str_repeat('=', 76) . PHP_EOL;
    foreach ($mismatches as $r) {
        echo PHP_EOL . $r['title'] . '  (' . $r['section'] . ' #' . $r['id'] . ', ' . $r['field'] . ')' . PHP_EOL;
        echo '   link:  ' . $r['url'] . PHP_EOL;
        echo '   says:  ' . $r['mismatch'] . PHP_EOL;
    }
}

if ($unchecked) {
    echo PHP_EOL . 'COULD NOT CHECK, ' . count($unchecked) . '. The far end declined or failed; these say' . PHP_EOL;
    echo 'nothing about the link and are not carried over, so the next run tries again:' . PHP_EOL;
    foreach ($unchecked as $r) {
        echo '  ' . str_pad((string)$r['status'], 5) . str_pad(mb_substr($r['title'], 0, 28), 30) . $r['url'] . PHP_EOL;
    }
}

if ($broken) {
    echo PHP_EOL . 'LINKS THAT DO NOT RESOLVE, ' . count($broken) . ':' . PHP_EOL;
    foreach ($broken as $r) {
        echo '  ' . str_pad((string)$r['status'], 5) . str_pad($r['field'], 20)
            . str_pad(mb_substr($r['title'], 0, 28), 30) . $r['url']
            . ($r['error'] ? '  (' . $r['error'] . ')' : '') . PHP_EOL;
    }
}

if ($movedOff) {
    echo PHP_EOL . 'REDIRECTED TO ANOTHER HOST, ' . count($movedOff) . '. Worth reading: a link that' . PHP_EOL;
    echo 'leaves the site it was written for may no longer show what it promised:' . PHP_EOL;
    foreach ($movedOff as $r) {
        echo '  ' . str_pad(mb_substr($r['title'], 0, 28), 30) . $r['url'] . PHP_EOL;
        echo '  ' . str_pad('', 30) . '-> ' . $r['final'] . PHP_EOL;
    }
}

$payload = [
    'meta' => [
        'generated' => (new DateTime())->format(DateTime::ATOM),
        'generated_by' => 'scripts/import/check_external_links.php',
        'user_agent' => $UA,
        'recheck_after_days' => $RECHECK_AFTER_DAYS,
        'requested' => $requested,
        'carried_over' => $carried,
        'counts' => [
            'links' => count($results), 'ok' => $ok, 'not_found' => $notFound, 'gone' => $gone,
            'other' => $other, 'could_not_check' => $failed, 'redirected' => $redirects,
            'redirected_off_host' => count($movedOff), 'mismatch' => count($mismatches),
        ],
    ],
    'links' => $results,
];
file_put_contents($out, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
echo PHP_EOL . 'wrote web/review/link-check.json' . PHP_EOL;
echo 'No record was read for anything but its field values, and none was changed.' . PHP_EOL;
