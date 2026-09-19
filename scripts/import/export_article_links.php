/**
 * Proposes article-to-article relations from the links the writers themselves
 * put in the text.
 *
 * Every extraction file carries links_out per page. Leon Worden in particular
 * cross-references constantly: a phrase in one piece links to the piece that
 * explains it. That is his own scholarly apparatus, already collected by the
 * extraction and never used, and it is better evidence than any similarity
 * measure because a person wrote it on purpose.
 *
 * What this does with each link:
 *   - resolves the href against the page it sits on, because 489 of the 1,329
 *     links are relative and reading them literally loses most of them;
 *   - keeps only links to another page on the legacy host ending .htm or .html;
 *   - drops the navigation furniture, which is every anchor beginning ">" and
 *     every "BACK", and drops self-links;
 *   - matches the target path against the legacy URL of a record we hold;
 *   - pulls the sentence the anchor sits in out of the source page's body_text,
 *     because links_out carries href, anchor and a flag and no context at all.
 *
 * The sentence extraction masks abbreviations before splitting. "Mrs." and an
 * initial both end in a full stop and both cut a sentence in half at exactly
 * the name it is about.
 *
 * Read only. Writes web/review/article-links.json for the review screen and
 * touches nothing in Craft.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_article_links.php'))"
 */

$root = \Craft::getAlias('@root');
$out  = \Craft::getAlias('@webroot') . '/review/article-links.json';
$INVENTORIES = ['perkins', 'reynolds-full', 'reynolds', 'warmemorial', 'worden'];

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/* No urljoin in PHP. Enough of one for hrefs on a flat legacy site. */
$resolve = function (string $base, string $href): string {
    $href = trim($href);
    if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'mailto:')) { return ''; }
    if (preg_match('~^https?://~i', $href)) { return $href; }
    if (str_starts_with($href, '//')) { return 'https:' . $href; }
    $bp = parse_url($base);
    $scheme = $bp['scheme'] ?? 'https';
    $host = $bp['host'] ?? 'scvhistory.com';
    if (str_starts_with($href, '/')) { return $scheme . '://' . $host . $href; }
    $dir = rtrim(dirname($bp['path'] ?? '/'), '/');
    $parts = array_filter(explode('/', $dir), fn($p) => $p !== '');
    foreach (explode('/', $href) as $seg) {
        if ($seg === '' || $seg === '.') { continue; }
        if ($seg === '..') { array_pop($parts); continue; }
        $parts[] = $seg;
    }
    return $scheme . '://' . $host . '/' . implode('/', $parts);
};

/* The sentence an anchor sits in, out of the page body. */
$sentenceFor = function (string $body, string $needle): string {
    $flat = preg_replace('~\s+~u', ' ', $body);
    $at = stripos($flat, $needle);
    if ($at === false) { return ''; }
    $winFrom = max(0, $at - 320);
    $win = substr($flat, $winFrom, 320 + strlen($needle) + 240);
    $rel = $at - $winFrom;

    $ABBR = ['Mrs.', 'Mr.', 'Dr.', 'Jr.', 'Sr.', 'St.', 'Col.', 'Gen.', 'Capt.', 'Rev.',
             'Hon.', 'Lt.', 'Sgt.', 'Maj.', 'Prof.', 'Ave.', 'No.', 'Co.', 'Inc.'];
    $masked = $win;
    foreach ($ABBR as $a) { $masked = str_replace($a, rtrim($a, '.') . "\x01", $masked); }
    $masked = preg_replace('~\b([A-Z])\.~', '$1' . "\x01", $masked);

    $before = substr($masked, 0, $rel);
    $start = 0;
    foreach (['. ', '! ', '? '] as $mark) {
        $e = strrpos($before, $mark);
        if ($e !== false && $e + 2 > $start) { $start = $e + 2; }
    }
    $tail = substr($masked, $start);
    $cut = preg_split('~(?<=[.!?])\s~u', $tail);
    $sent = trim($cut[0] ?? $tail);
    return str_replace("\x01", '.', $sent);
};

/* ------------------------------------------------- what we hold, by legacy path */

$RECORD_FIELDS = ['legacyUrl', 'wmLegacyUrl', 'mpLegacyUrl', 'obitLegacyUrl'];
$byPath = [];
foreach (['articles', 'warMemorials', 'obituaries', 'militaryProfiles'] as $sec) {
    foreach (\craft\elements\Entry::find()->section($sec)->status(null)->limit(null)->all() as $e) {
        foreach ($RECORD_FIELDS as $h) {
            if (!$hasField($e, $h)) { continue; }
            $v = trim((string)$e->getFieldValue($h));
            if ($v === '') { continue; }
            $p = parse_url($v, PHP_URL_PATH) ?: $v;
            if (!isset($byPath[$p])) {
                $byPath[$p] = ['id' => $e->id, 'title' => (string)$e->title, 'url' => (string)$e->url,
                               'section' => $sec, 'slug' => $e->slug];
            }
        }
    }
}
echo 'records indexed by legacy path: ' . count($byPath) . PHP_EOL;

/* --------------------------------------------------------------- walk the links */

$stats = ['links' => 0, 'nav' => 0, 'noAnchor' => 0, 'offHost' => 0, 'notAPage' => 0,
          'self' => 0, 'noRecord' => 0, 'sourceNotHeld' => 0, 'kept' => 0];
$cand = [];
$unresolvedTargets = [];

foreach ($INVENTORIES as $inv) {
    $p = $root . '/inventory/legacy/' . $inv . '.json';
    if (!file_exists($p)) { echo 'WARNING: ' . $inv . '.json not found' . PHP_EOL; continue; }
    $d = json_decode(file_get_contents($p), true);
    $pages = $d['pages'] ?? array_merge($d['series_pages'] ?? [], $d['related_pages'] ?? []);

    foreach ($pages as $pg) {
        $srcUrl = (string)($pg['source_url'] ?? '');
        $srcPath = parse_url($srcUrl, PHP_URL_PATH) ?: '';
        $body = (string)($pg['body_text'] ?? '');
        $source = $byPath[$srcPath] ?? null;

        foreach (($pg['links_out'] ?? []) as $l) {
            $stats['links']++;
            $anchor = trim((string)($l['anchor_text'] ?? ''));

            if ($anchor === '') { $stats['noAnchor']++; continue; }
            if (str_starts_with($anchor, '>') || strtoupper($anchor) === 'BACK') { $stats['nav']++; continue; }

            $abs = $resolve($srcUrl, (string)($l['href_raw'] ?? ''));
            if ($abs === '') { continue; }
            $u = parse_url($abs);
            if (!str_contains((string)($u['host'] ?? ''), 'scvhistory.com')) { $stats['offHost']++; continue; }
            $tgtPath = $u['path'] ?? '';
            if (!preg_match('~\.html?$~i', $tgtPath)) { $stats['notAPage']++; continue; }
            if ($tgtPath === $srcPath) { $stats['self']++; continue; }

            $target = $byPath[$tgtPath] ?? null;
            if (!$target) {
                $stats['noRecord']++;
                $unresolvedTargets[$tgtPath] = ($unresolvedTargets[$tgtPath] ?? 0) + 1;
                continue;
            }
            if (!$source) { $stats['sourceNotHeld']++; continue; }
            if ($source['id'] === $target['id']) { $stats['self']++; continue; }

            /* Two different apparatuses share one mechanism. A bare number is a
               footnote marker pointing at the notes page; prose is a genuine
               cross-reference to another piece. Both are real relations and they
               are not the same thing, so they are labelled and the screen can
               take them separately. */
            $kind = preg_match('~^\[?\s*\d{1,3}\s*\]?$~u', $anchor) ? 'footnote' : 'crossref';

            $stats['kept']++;
            $stats[$kind] = ($stats[$kind] ?? 0) + 1;
            $key = $source['id'] . '->' . $target['id'];
            $sentence = $sentenceFor($body, $anchor);

            /* One article can link to another several times. Keep the instance
               with the most sentence around it, and count the rest. */
            if (!isset($cand[$key]) || mb_strlen($sentence) > mb_strlen($cand[$key]['sentence'])) {
                $cand[$key] = [
                    'source' => $source, 'target' => $target,
                    'anchor' => $anchor, 'sentence' => $sentence,
                    'kind' => $cand[$key]['kind'] ?? $kind,
                    'inventory' => $inv, 'times' => $cand[$key]['times'] ?? 0,
                ];
            }
            $cand[$key]['times'] = ($cand[$key]['times'] ?? 0) + 1;
            /* A pair with any prose anchor is a cross-reference, whatever else
               points the same way. */
            if ($kind === 'crossref') { $cand[$key]['kind'] = 'crossref'; }
        }
    }
}

/* A link both ways is a stronger signal than one way. */
foreach ($cand as $k => $c) {
    $back = $c['target']['id'] . '->' . $c['source']['id'];
    $cand[$k]['mutual'] = isset($cand[$back]);
}

$rows = array_values($cand);
usort($rows, function ($a, $b) {
    $ka = $a['kind'] === 'crossref' ? 1 : 0;
    $kb = $b['kind'] === 'crossref' ? 1 : 0;
    return [$kb, $b['mutual'], $b['times'], mb_strlen($b['sentence'])]
       <=> [$ka, $a['mutual'], $a['times'], mb_strlen($a['sentence'])];
});

/* ------------------------------------------------------------------- report */

echo str_repeat('=', 78) . PHP_EOL;
echo 'links in the extraction:        ' . $stats['links'] . PHP_EOL;
echo '  no anchor text, an image:     ' . $stats['noAnchor'] . PHP_EOL;
echo '  navigation furniture:         ' . $stats['nav'] . PHP_EOL;
echo '  off the legacy host:          ' . $stats['offHost'] . PHP_EOL;
echo '  not a page, image or pdf:     ' . $stats['notAPage'] . PHP_EOL;
echo '  a link to itself:             ' . $stats['self'] . PHP_EOL;
echo '  target not a record we hold:  ' . $stats['noRecord'] . PHP_EOL;
echo '  source not a record we hold:  ' . $stats['sourceNotHeld'] . PHP_EOL;
echo '  usable:                       ' . $stats['kept'] . PHP_EOL;
echo '    of those, cross-references: ' . ($stats['crossref'] ?? 0) . PHP_EOL;
echo '    of those, footnote markers: ' . ($stats['footnote'] ?? 0) . PHP_EOL;
echo 'distinct article pairs proposed: ' . count($rows) . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

foreach ($rows as $r) {
    echo PHP_EOL . '[' . $r['kind'] . '] ' . $r['source']['title'] . '  ->  ' . $r['target']['title']
        . ($r['mutual'] ? '   [links both ways]' : '')
        . ($r['times'] > 1 ? '   [' . $r['times'] . ' times]' : '') . PHP_EOL;
    echo '   anchor: "' . $r['anchor'] . '"' . PHP_EOL;
    if ($r['sentence'] !== '') { echo '   "' . mb_substr($r['sentence'], 0, 170) . '"' . PHP_EOL; }
    else { echo '   (the anchor text does not appear in the page body, so no sentence)' . PHP_EOL; }
}

arsort($unresolvedTargets);
$topMissing = array_slice($unresolvedTargets, 0, 12, true);
if ($topMissing) {
    echo PHP_EOL . 'MOST LINKED PAGES WE DO NOT HOLD, ' . count($unresolvedTargets) . ' distinct.' . PHP_EOL;
    echo 'These are what the writers pointed at most often and the archive has not imported:' . PHP_EOL;
    foreach ($topMissing as $p => $n) { echo '  ' . str_pad((string)$n, 4, ' ', STR_PAD_LEFT) . '  ' . $p . PHP_EOL; }
}

file_put_contents($out, json_encode([
    'meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/export_article_links.php',
        'inventories' => $INVENTORIES,
        'stats' => $stats,
        'pairs' => count($rows),
    ],
    'pairs' => $rows,
    'unheldTargets' => $topMissing,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo PHP_EOL . 'wrote web/review/article-links.json' . PHP_EOL;
echo 'Nothing in Craft was changed. Decide at /review/article-links.html' . PHP_EOL;
