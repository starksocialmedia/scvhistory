/**
 * Loads one real page of every kind and checks what comes back.
 *
 * Three times in one session a change has been committed that read correctly in
 * the diff and was wrong at render:
 *
 *   an SRI hash written from memory rather than computed, so the browser
 *   blocked the script and the page came up empty;
 *   a lookup keyed by field id, which Twig's merge filter silently renumbered,
 *   so every number on the page read zero;
 *   a Twig comment inside a hash literal, which is a syntax error and took
 *   every record page down with it.
 *
 * None of those is visible in a diff and none is caught by `php -l`. The only
 * check that catches them is on the output, so this requests the pages and
 * reads what the server actually sent.
 *
 * It asserts, per page: a 200, no Twig or PHP error signature in the body, a
 * <title>, and that every application/ld+json block parses as JSON. One URL per
 * section and per entry type, discovered from Craft rather than listed, plus
 * the indexes and the unlisted pages.
 *
 * Read only. Requests the local site; writes nothing anywhere.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/check_render.php'))"
 */

/* @web is derived from the request and is empty on the command line, so the
   base comes from the site itself. Getting that wrong made every constructed
   URL fail to connect while the entry URLs passed, which is its own small
   lesson about checking the output. */
$base = rtrim((string)Craft::$app->getSites()->getPrimarySite()->getBaseUrl(), '/');
if ($base === '') { echo 'the primary site has no base URL; cannot check anything' . PHP_EOL; return; }
echo 'site: ' . $base . PHP_EOL;

/* Compiled templates are cached, so a broken template can keep serving the last
   good render and fail later. Clear it or this check proves nothing. */
$compiled = \Craft::getAlias('@storage') . '/runtime/compiled_templates';
if (is_dir($compiled)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($compiled, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $f) { $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname()); }
    echo 'cleared the compiled template cache' . PHP_EOL;
}

/* ---------------------------------------------------------------- the list */

$urls = [];
foreach (Craft::$app->getEntries()->getAllSections() as $s) {
    foreach ($s->getEntryTypes() as $t) {
        $e = \craft\elements\Entry::find()->section($s->handle)->type($t->handle)->status(null)->one();
        if ($e && $e->url) { $urls[$e->url] = $s->handle . '/' . $t->handle; }
    }
}
foreach (Craft::$app->getCategories()->getAllGroups() as $g) {
    $c = \craft\elements\Category::find()->group($g->handle)->one();
    if ($c && $c->url) { $urls[$c->url] = 'category/' . $g->handle; }
}
foreach ([
    '' => 'home', 'articles' => 'index', 'persons' => 'index', 'places' => 'index',
    'collections' => 'index', 'war-memorial' => 'index', 'obituaries' => 'index',
    'on-this-day' => 'index', 'search?q=newhall' => 'search',
    /* The two indexes that 404ed for months while the menu linked to them. */
    'photographs' => 'index', 'photographs?view=all&page=2' => 'index', 'documents' => 'index', 'schools' => 'index',
    /* The unlisted pages are admin-only now, so an anonymous request gets a
       302 to the login screen. Checking them as 200 would fail every run; not
       checking them at all would miss a template that throws before the guard.
       So they are checked for the redirect, which proves the guard is there. */
    'admin-overview' => 'guarded', 'graph' => 'guarded', 'graph/data' => 'guarded',
    'admin-ledger' => 'guarded', 'admin-ledger/data' => 'guarded',
    'admin-fixes' => 'guarded', 'admin-quality' => 'guarded',
] as $path => $what) {
    $urls[$base . '/' . $path] = $what;
}

echo 'pages to check: ' . count($urls) . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

/* ---------------------------------------------------------------- the check */

$ERRORS = ['Twig\\Error', 'Twig Syntax Error', 'Unexpected character',
           'Calling unknown method', 'Fatal error', 'Uncaught Exception',
           'Variable "', 'Unknown "'];

$fail = 0; $checked = 0; $ldTotal = 0;

/* ------------------------------------------------ every template compiles

   Rendering one page per kind catches a broken template only on the pages this
   list loads. A Twig comment inside an array or hash literal is a syntax error
   (the {# ... #} is read as code), and it took the site down three times: the
   record pages once, the site header twice, in September 2026. The header is on
   every page, so one comment in the wrong place is a whole-site outage.

   So every .twig file under templates/ is tokenized and parsed first, and a
   syntax error anywhere fails the check, whether or not a page below uses it.
   Parsing only: nothing is rendered or executed here. */
$view = Craft::$app->getView();
$view->setTemplateMode(\craft\web\View::TEMPLATE_MODE_SITE);
$twig = $view->getTwig();
$tplRoot = \Craft::getAlias('@templates');
$parsed = 0;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tplRoot, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    if (!str_ends_with($f->getFilename(), '.twig')) { continue; }
    $name = ltrim(substr($f->getPathname(), strlen($tplRoot)), '/');
    try {
        $twig->parse($twig->tokenize(new \Twig\Source((string)file_get_contents($f->getPathname()), $name, $f->getPathname())));
        $parsed++;
    } catch (\Twig\Error\SyntaxError $e) {
        $fail++;
        echo 'SYNTAX  ' . $name . ' line ' . $e->getTemplateLine() . ': ' . $e->getRawMessage() . PHP_EOL;
    }
}
echo 'parsed ' . $parsed . ' templates' . PHP_EOL;

foreach ($urls as $url => $what) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $body = (string)curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    $checked++;

    $problems = [];
    /* A guarded page is checked for the guard. An anonymous request must be
       turned away, and a 200 here would mean the guard is gone. A 500 still
       fails, because a template that throws before the guard throws for
       everybody. */
    $isGuarded = $what === 'guarded';
    if ($isGuarded) {
        if (!in_array($status, [302, 403, 404], true)) {
            $problems[] = 'status ' . $status . ', expected the guard to turn an anonymous request away';
        }
    } elseif ($status !== 200) { $problems[] = 'status ' . $status; }
    foreach ($ERRORS as $sig) {
        if (str_contains($body, $sig)) { $problems[] = 'body contains "' . $sig . '"'; break; }
    }

    $isJson = $what === 'json';
    if (!$isJson && !$isGuarded && $status === 200 && !preg_match('~<title>~i', $body)) { $problems[] = 'no <title>'; }

    if ($isGuarded) {
        /* nothing else to assert: there is no body to read */
    } elseif ($isJson) {
        if (json_decode($body, true) === null) { $problems[] = 'the response is not valid JSON'; }
    } else {
        if (preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $body, $m)) {
            foreach ($m[1] as $block) {
                $ldTotal++;
                if (json_decode($block, true) === null) { $problems[] = 'a JSON-LD block does not parse'; }
            }
        }
    }

    $path = parse_url($url, PHP_URL_PATH) . (parse_url($url, PHP_URL_QUERY) ? '?' . parse_url($url, PHP_URL_QUERY) : '');
    if ($problems) {
        $fail++;
        echo 'FAIL  ' . str_pad($what, 22) . $path . PHP_EOL;
        foreach ($problems as $p) { echo '        ' . $p . PHP_EOL; }
    } else {
        echo 'ok    ' . str_pad($what, 22) . $path . PHP_EOL;
    }
}

echo str_repeat('=', 76) . PHP_EOL;
echo 'checked ' . $checked . ' pages, ' . $ldTotal . ' JSON-LD blocks parsed' . PHP_EOL;

/* ------------------------------------------------ navigation coverage

   A nav link disappears when its page is empty (the rule in the header), but
   nothing made a link appear when a page filled: /schools had records for an
   hour before anyone could find it, and Photographs sat for weeks under a
   submenu. So every place a visitor could be sent is listed here from the site
   itself, not from memory: every section and category group with URLs and live
   records, every top-level index template, and every static page. Each must be
   linked from the header menu or the footer, or named in
   templates/_data/nav-exempt.json with a reason. Anything else fails the check,
   so new material cannot land unreachable and be reported as done. */
$fetch = function (string $u): string {
    $ch = curl_init($u);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
    $b = (string)curl_exec($ch); curl_close($ch); return $b;
};
$home = $fetch($base . '/');
$hdrEnd = strpos($home, '</header>') ?: 0;
$ftrStart = strrpos($home, '<footer') ?: strlen($home);
$pathsIn = function (string $html) use ($base): array {
    preg_match_all('~href="' . preg_quote($base, '~') . '/?([^"#]*)"~', $html, $m);
    return array_values(array_unique(array_map(fn($h) => trim(rawurldecode(parse_url('/' . $h, PHP_URL_PATH) ?? ''), '/'), $m[1])));
};
$inMenu = $pathsIn(substr($home, 0, $hdrEnd));
$inFooter = $pathsIn(substr($home, $ftrStart));
$top = fn(array $paths) => array_values(array_unique(array_map(fn($p) => explode('/', $p)[0], $paths)));
$menuTop = $top($inMenu); $footerTop = $top($inFooter);
$exempt = json_decode((string)@file_get_contents(\Craft::getAlias('@templates') . '/_data/nav-exempt.json'), true) ?: [];
unset($exempt['_about']);

$want = [];   /* path => what it is, only where there are records */
foreach (Craft::$app->getEntries()->getAllSections() as $s) {
    $n = \craft\elements\Entry::find()->section($s->handle)->count();
    if (!$n) { continue; }
    foreach ($s->getSiteSettings() as $ss) {
        if (!$ss->hasUrls) { continue; }
        $prefix = explode('/', trim($ss->uriFormat, '/'))[0];
        if (str_contains($prefix, '{')) {
            foreach (\craft\elements\Entry::find()->section($s->handle)->all() as $e) { $want[trim($e->uri, '/')] = 'page: ' . $e->title; }
        } else { $want[$prefix] = 'section ' . $s->handle . ' (' . $n . ')'; }
    }
}
foreach (Craft::$app->getCategories()->getAllGroups() as $g) {
    $n = \craft\elements\Category::find()->group($g->handle)->count();
    foreach ($g->getSiteSettings() as $ss) { if ($n && $ss->hasUrls) { $want[explode('/', trim($ss->uriFormat, '/'))[0]] = 'category group ' . $g->handle . ' (' . $n . ')'; } }
}
foreach (glob(\Craft::getAlias('@templates') . '/*/index.twig') as $f) {
    $dir = basename(dirname($f));
    if ($dir[0] === '_' || str_starts_with($dir, 'admin-') || $dir === 'graph' || isset($want[$dir])) { continue; }
    $n = match ($dir) {
        'schools' => \craft\elements\Entry::find()->section('organizations')->orgType('school')->count(),
        'on-this-day' => \craft\elements\Entry::find()->section(['articles', 'events'])->count(),
        'tags' => \craft\elements\Category::find()->group('tag')->count(),
        'military-profiles' => \craft\elements\Entry::find()->section('militaryProfiles')->count(),
        'elections' => Craft::$app->getEntries()->getSectionByHandle('elections') ? \craft\elements\Entry::find()->section('elections')->count() : 0,
        default => null,
    };
    if ($n === 0) { continue; }
    $want[$dir] = 'index template /' . $dir . ($n !== null ? ' (' . $n . ')' : '');
}
ksort($want);
$unreached = []; $footerOnly = [];
foreach ($want as $path => $what) {
    $t = explode('/', $path)[0];
    if (in_array($t, $menuTop, true)) { continue; }
    if (in_array($path, $inFooter, true) || in_array($t, $footerTop, true)) { $footerOnly[] = "/$path, $what"; continue; }
    if (isset($exempt[$t])) { continue; }
    $unreached[] = "/$path, $what";
}
echo 'NAV COVERAGE: ' . count($want) . ' destinations with records; ' . count($footerOnly) . ' in the footer only; ' . count($unreached) . ' reachable from neither' . PHP_EOL;
foreach ($footerOnly as $x) { echo '   footer only  ' . $x . PHP_EOL; }
foreach ($unreached as $x) { echo '   UNREACHABLE  ' . $x . PHP_EOL; }
/* And every link the header and footer carry must land: the header linked
   /donate, which had no page, on every page of the site. */
$dead = [];
foreach (array_unique(array_merge($inMenu, $inFooter)) as $p) {
    $ch = curl_init($base . '/' . $p);
    curl_setopt_array($ch, [CURLOPT_NOBODY => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 20, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
    curl_exec($ch); $st = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
    if ($st !== 200) { $dead[] = "/$p ($st)"; }
}
if ($dead) { $fail++; echo 'NAV LINKS FAIL: ' . implode(', ', $dead) . PHP_EOL; } else { echo 'nav links: all ' . count(array_unique(array_merge($inMenu, $inFooter))) . ' land' . PHP_EOL; }
if ($unreached) { $fail++; echo 'NAV COVERAGE FAIL: link each from the menu or the footer, or name it in templates/_data/nav-exempt.json with a reason' . PHP_EOL; }

/* Read-back is not the page: a body can be in the database and absent from
   its page (DEPLOY-RUNBOOK section 9). A sample of every section here; run
   check_rendered_bodies.php with $SAMPLE = 0 for all of them. */
$SAMPLE = 15;
$rb = eval(file_get_contents(\Craft::getAlias('@root') . '/scripts/import/check_rendered_bodies.php'));
if (is_array($rb) && !($rb['ok'] ?? true)) {
    $known = [];   /* a failure Nathan has decided to accept goes here, with the date and the reason */
    $real = array_filter($rb['fails'], fn($f) => !array_filter($known, fn($k) => str_contains($f, $k)));
    if ($real) { $fail++; echo 'RENDERED BODIES FAIL' . PHP_EOL; }
}

/* Generated images (docs/DATA-MODEL.md): a banner is decoration, listed in
   templates/_data/banners.json, a file in web/banners and nothing else. It fails
   here if its file is missing or changed, if the same picture is in the
   archive's asset store, if its page does not carry the credit, or if the
   page's JSON-LD mentions it. Provenance not yet recorded is reported, not failed:
   the page says "not recorded". */
$bn = json_decode((string)file_get_contents(\Craft::getAlias('@root') . '/templates/_data/banners.json'), true) ?: [];
$bnBad = []; $bnOpen = [];
$assetHashes = null;
foreach ($bn as $key => $b) {
    if (str_starts_with($key, '_')) { continue; }
    $f = \Craft::getAlias('@root') . '/web/banners/' . ($b['file'] ?? '');
    if (($b['kind'] ?? '') !== 'ai-generated-illustration') { $bnBad[] = "$key: kind is not ai-generated-illustration"; continue; }
    if (!is_file($f) || hash_file('sha256', $f) !== ($b['sha256'] ?? '')) { $bnBad[] = "$key: web/banners/{$b['file']} is missing or changed"; continue; }
    [$sec] = explode(':', $key);
    $rec = \craft\elements\Entry::find()->section($sec)->id($b['record']['id'] ?? 0)->one();
    if (!$rec || $rec->title !== ($b['record']['title'] ?? null)) { $bnBad[] = "$key: the record is not {$b['record']['title']}"; continue; }
    if (\craft\elements\Asset::find()->filename([$b['file'], basename((string)($b['receivedAs'] ?? ''))])->exists()) { $bnBad[] = "$key: an asset has the banner's file name"; }
    if (!empty($b['formerAsset']) && \craft\elements\Asset::find()->id((int)$b['formerAsset'])->exists()) { $bnBad[] = "$key: the archive still holds it as asset #{$b['formerAsset']} (run detach_ai_collection_bands.php)"; }
    $ch = curl_init($rec->url); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
    $html = (string)curl_exec($ch); curl_close($ch);
    if (!str_contains($html, 'AI-generated illustration. Not a photograph.')) { $bnBad[] = "$key: {$rec->url} does not carry the credit"; }
    preg_match_all('~<script[^>]+application/ld\+json[^>]*>(.*?)</script>~s', $html, $ld);
    if (array_filter($ld[1], fn($j) => str_contains($j, 'banners/') || str_contains($j, $b['file']))) { $bnBad[] = "$key: the banner is in the page's JSON-LD"; }
    foreach (['tool', 'sourcePhotograph', 'generatedOn'] as $k) { if (empty($b[$k])) { $bnOpen[] = "$key $k"; } }
}
if ($bnBad) { $fail++; echo 'BANNERS FAIL: ' . implode('; ', $bnBad) . PHP_EOL; } else { echo 'banners: ' . count(array_filter(array_keys($bn), fn($k) => $k[0] !== '_')) . ' decoration only, credited, not in JSON-LD, not assets' . ($bnOpen ? '; provenance not yet recorded: ' . implode(', ', $bnOpen) : '') . PHP_EOL; }

/* Today's date, on the two pages that print it. On 1 October 2026 staging's On
   This Day read 3 September: the pages sent no Cache-Control, so a proxy or the
   browser could keep a stale copy. Each must show the date the check is run on,
   in the site's own time zone, and must say no-store. */
$today = new \DateTime('now', new \DateTimeZone(Craft::$app->getTimeZone()));
$dateBad = [];
foreach (['/on-this-day' => '~<h1>\s*([^<]+?)\s*</h1>~', '/' => '~<h2>On this day</h2>\s*<div class="hp-kick">\s*([^<]+?)\s*</div>~'] as $p => $re) {
    $ch = curl_init($base . $p);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
    $raw = (string)curl_exec($ch); $hs = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE); curl_close($ch);
    $head = substr($raw, 0, $hs); $html = substr($raw, $hs);
    $want = $p === '/' ? strtoupper($today->format('F j')) : $today->format('F j');
    $shown = preg_match($re, $html, $m) ? html_entity_decode(trim($m[1])) : '(no date found)';
    /* The home page's section is hidden on a day with no confirmed dates; then only the header is checked. */
    if ($p === '/' && $shown === '(no date found)' && !str_contains($html, '<h2>On this day</h2>')) { $shown = $want; }
    if ($shown !== $want) { $dateBad[] = "$p shows \"$shown\", today is \"$want\""; }
    if (!preg_match('~^cache-control:[^\r\n]*no-store~im', $head)) { $dateBad[] = "$p does not send Cache-Control: no-store"; }
}
if ($dateBad) { $fail++; echo 'TODAY FAIL: ' . implode('; ', $dateBad) . PHP_EOL; } else { echo 'today: /on-this-day and / both show ' . $today->format('j F Y') . ' and send no-store' . PHP_EOL; }

/* The data model has to keep up with the schema. A field added without
   regenerating docs/DATA-MODEL.md fails here, because a data model that drifts
   is consulted and believed. */
$dm = eval(file_get_contents(\Craft::getAlias('@root') . '/scripts/import/check_data_model.php'));
if (is_array($dm) && !($dm['ok'] ?? true)) { $fail++; }

echo ($fail ? 'FAILURES: ' . $fail : 'no failures') . PHP_EOL;
if ($fail) {
    echo PHP_EOL . 'Do not report the work as done. A page that 500s or renders an error into' . PHP_EOL;
    echo 'its body is not caught by php -l and is not visible in a diff.' . PHP_EOL;
}
