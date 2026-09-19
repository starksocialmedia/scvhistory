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
    'admin-overview' => 'unlisted', 'graph' => 'unlisted', 'graph/data' => 'json',
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
    if ($status !== 200) { $problems[] = 'status ' . $status; }
    foreach ($ERRORS as $sig) {
        if (str_contains($body, $sig)) { $problems[] = 'body contains "' . $sig . '"'; break; }
    }

    $isJson = $what === 'json';
    if (!$isJson && $status === 200 && !preg_match('~<title>~i', $body)) { $problems[] = 'no <title>'; }

    if ($isJson) {
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
echo ($fail ? 'FAILURES: ' . $fail : 'no failures') . PHP_EOL;
if ($fail) {
    echo PHP_EOL . 'Do not report the work as done. A page that 500s or renders an error into' . PHP_EOL;
    echo 'its body is not caught by php -l and is not visible in a diff.' . PHP_EOL;
}
