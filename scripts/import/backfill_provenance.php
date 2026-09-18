/**
 * Backfills sourcePath, legacyKey and legacyUrl on records that came in from the
 * WordPress import, which carried no provenance. Without those three a record
 * cannot be re-imported, matched against a crawl, or cited.
 *
 * Nothing is ever guessed from a slug. A value is only written when a real
 * legacy path is in evidence, from one of these, in order:
 *
 *   1. craft      the record already holds a legacy URL, so the other two
 *                 fields are derived from it
 *   2. wp-meta    inventory/wp_content.json, the post's own legacy_url meta,
 *                 matched on slug
 *   3. wp-link    a scvhistory.com link in that post's body or another meta
 *                 field, matched on slug
 *   4. entity     a Person, Place or Organization in a legacy inventory's
 *                 entity_index with has_legacy_page true, matched on name
 *
 * sourcePath is the full URL with host, legacyUrl the root-relative path, and
 * legacyKey the filename without its extension. A legacy URL pointing at some
 * other host cannot yield a root-relative path for this archive, so it is
 * reported rather than mangled.
 *
 * Only empty fields are filled. Nothing is overwritten, so a second run is a
 * no-op.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/backfill_provenance.php'))"
 */

$APPLY = false;

$root = \Craft::getAlias('@root');
$elements = Craft::$app->getElements();

$configured = (string)(Craft::$app->config->custom->legacyHost ?? 'https://scvhistory.com');
$HOST = rtrim($configured, '/');
$OWN_HOSTS = ['scvhistory.com', 'www.scvhistory.com'];

/* Every legacy URL field, for reading. legacyUrl is what gets written, since
   every section that carries provenance has it. */
$URL_HANDLES = [
    'legacyUrl', 'personLegacyUrl', 'placeLegacyUrl', 'orgLegacyUrl',
    'obitLegacyUrl', 'groupLegacyUrl', 'eventLegacyUrl', 'mpLegacyUrl',
];

/* Which legacy inventories to read entity_index from. */
$INVENTORIES = ['perkins', 'reynolds-full', 'warmemorial'];

/* ---------------------------------------------------------------- helpers */

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) {
        if ($f->handle === $handle) { return true; }
    }
    return false;
};

$read = function (\craft\base\ElementInterface $el, string $handle) use ($hasField): string {
    if (!$hasField($el, $handle)) { return ''; }
    try { return trim((string)$el->getFieldValue($handle)); }
    catch (\Throwable $e) { return ''; }
};

/**
 * Turns whatever shape a legacy reference arrives in into the three fields.
 * Returns [sourcePath, legacyUrl, legacyKey] or null when the value cannot
 * yield a path on this archive.
 */
$derive = function (string $raw) use ($HOST, $OWN_HOSTS): ?array {
    $v = trim($raw);
    if ($v === '') { return null; }

    $path = null;
    if (preg_match('~^https?://~i', $v)) {
        $host = strtolower((string)(parse_url($v, PHP_URL_HOST) ?: ''));
        if (!in_array($host, $OWN_HOSTS, true)) { return null; }   // another site
        $parts = parse_url($v);
        $path = $parts['path'] ?? '';
    } elseif (str_starts_with($v, '//')) {
        $host = strtolower((string)(parse_url('https:' . $v, PHP_URL_HOST) ?: ''));
        if (!in_array($host, $OWN_HOSTS, true)) { return null; }
        $parts = parse_url('https:' . $v);
        $path = $parts['path'] ?? '';
    } elseif (str_starts_with($v, '/')) {
        $path = explode('#', explode('?', $v)[0])[0];
    } else {
        /* A bare value. Only a path if it actually looks like one: it must end
           in a page extension. A bare hostname is another site. */
        $first = strtolower(explode('/', $v)[0]);
        if (preg_match('~\.(com|org|net|edu|gov|us|info|co|io|tv)$~', $first)) { return null; }
        if (!preg_match('~\.(html?|php|asp|aspx)$~i', $v)) { return null; }
        $path = '/' . ltrim($v, '/');
    }

    if (!$path || $path === '/' ) { return null; }
    $base = basename($path);
    if ($base === '') { return null; }
    $key = preg_replace('~\.[A-Za-z0-9]+$~', '', $base);
    if ($key === '') { return null; }

    return [$HOST . $path, $path, $key];
};

/* ---------------------------------------------------------------- sources */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'legacy host: ' . $HOST . PHP_EOL;

/* WordPress export, indexed by slug. */
$wpBySlug = [];
$wpPath = $root . '/inventory/wp_content.json';
if (file_exists($wpPath)) {
    $wp = json_decode(file_get_contents($wpPath), true);
    foreach (($wp['posts'] ?? []) as $post) {
        $slug = trim((string)($post['slug'] ?? ''));
        if ($slug !== '') { $wpBySlug[$slug] = $post; }
    }
    echo 'wp_content.json: ' . count($wpBySlug) . ' posts indexed by slug' . PHP_EOL;
} else {
    echo 'WARNING: inventory/wp_content.json not found, sources 2 and 3 unavailable' . PHP_EOL;
}

/* entity_index entries that name a real legacy page, keyed on lowercased name. */
$entityByName = [];
foreach ($INVENTORIES as $inv) {
    $p = $root . '/inventory/legacy/' . $inv . '.json';
    if (!file_exists($p)) { echo 'WARNING: ' . $inv . '.json not found' . PHP_EOL; continue; }
    $data = json_decode(file_get_contents($p), true);
    $ei = $data['entity_index'] ?? [];
    $n = 0;
    foreach (['people', 'places', 'organizations'] as $kind) {
        foreach (($ei[$kind] ?? []) as $row) {
            if (empty($row['has_legacy_page'])) { continue; }
            $url = trim((string)($row['legacy_page_url'] ?? ''));
            $name = trim((string)($row['name_raw'] ?? ''));
            if ($url === '' || $name === '') { continue; }
            $entityByName[mb_strtolower($name)] = ['url' => $url, 'from' => $inv . '/' . $kind];
            $n++;
            foreach (($row['name_variants'] ?? []) as $alt) {
                $alt = trim((string)$alt);
                if ($alt !== '') { $entityByName[mb_strtolower($alt)] = ['url' => $url, 'from' => $inv . '/' . $kind]; }
            }
        }
    }
    echo 'entity_index ' . str_pad($inv, 16) . $n . ' entries with a legacy page' . PHP_EOL;
}

/**
 * The scvhistory.com links a WordPress post carries, body first then meta.
 */
$wpLinks = function (array $post): array {
    $out = [];
    $rx = '~https?://(?:www\.)?scvhistory\.com/[^\s"\'<>)\]]+~i';
    if (preg_match_all($rx, (string)($post['body'] ?? ''), $m)) {
        foreach ($m[0] as $u) { $out[] = ['u' => rtrim($u, '.,;'), 'where' => 'body']; }
    }
    foreach (($post['meta'] ?? []) as $k => $v) {
        if (!is_string($v) || $k === 'legacy_url') { continue; }
        if (preg_match_all($rx, $v, $m2)) {
            foreach ($m2[0] as $u) { $out[] = ['u' => rtrim($u, '.,;'), 'where' => 'meta.' . $k]; }
        }
    }
    return $out;
};

/* ---------------------------------------------------------------- walk */

echo '=== records ===' . PHP_EOL;

$filled = [];       /* section => source => count */
$fieldsWritten = [] /* section => handle => count */;
$noEvidence = [];   /* section => [ "slug  title" ] */
$foreignHost = [];  /* "section slug = value" */
$failed = 0;

foreach (Craft::$app->getEntries()->getAllSections() as $section) {
    foreach (\craft\elements\Entry::find()->section($section->handle)->status(null)->all() as $e) {
        if (!$hasField($e, 'sourcePath') && !$hasField($e, 'legacyKey') && !$hasField($e, 'legacyUrl')) {
            continue;   /* born digital: pages carries none of the three */
        }

        $needSource = $hasField($e, 'sourcePath') && $read($e, 'sourcePath') === '';
        $needKey    = $hasField($e, 'legacyKey')  && $read($e, 'legacyKey')  === '';
        $existingUrl = '';
        foreach ($URL_HANDLES as $h) {
            $v = $read($e, $h);
            if ($v !== '') { $existingUrl = $v; break; }
        }
        $needUrl = ($existingUrl === '') && $hasField($e, 'legacyUrl');

        if (!$needSource && !$needKey && !$needUrl) { continue; }

        /* Find a legacy reference, best evidence first. */
        $ref = null; $from = null;
        if ($existingUrl !== '') {
            $ref = $existingUrl; $from = 'craft';
        }
        if ($ref === null) {
            $post = $wpBySlug[$e->slug] ?? null;
            if ($post) {
                $lu = trim((string)(($post['meta'] ?? [])['legacy_url'] ?? ''));
                if ($lu !== '') { $ref = $lu; $from = 'wp-meta'; }
                if ($ref === null) {
                    foreach ($wpLinks($post) as $hit) {
                        $ref = $hit['u']; $from = 'wp-link:' . $hit['where']; break;
                    }
                }
            }
        }
        if ($ref === null) {
            $hit = $entityByName[mb_strtolower(trim((string)$e->title))] ?? null;
            if ($hit) { $ref = $hit['url']; $from = 'entity:' . $hit['from']; }
        }

        if ($ref === null) {
            $noEvidence[$section->handle][] = str_pad($e->slug, 46) . $e->title;
            continue;
        }

        $parts = $derive($ref);
        if ($parts === null) {
            $foreignHost[] = str_pad($section->handle, 16) . str_pad($e->slug, 42) . $ref . '  (via ' . $from . ')';
            $noEvidence[$section->handle][] = str_pad($e->slug, 46) . $e->title . '   [points at another host: ' . $ref . ']';
            continue;
        }
        [$sourcePath, $legacyUrl, $legacyKey] = $parts;

        $sets = [];
        if ($needSource) { $sets['sourcePath'] = $sourcePath; }
        if ($needKey)    { $sets['legacyKey']  = $legacyKey; }
        if ($needUrl)    { $sets['legacyUrl']  = $legacyUrl; }
        if (!count($sets)) { continue; }

        $filled[$section->handle][$from] = ($filled[$section->handle][$from] ?? 0) + 1;
        foreach ($sets as $h => $v) { $fieldsWritten[$section->handle][$h] = ($fieldsWritten[$section->handle][$h] ?? 0) + 1; }

        echo str_pad($section->handle, 15) . str_pad($e->slug, 44) . str_pad($from, 22)
            . implode(', ', array_keys($sets)) . '  -> ' . $legacyKey . PHP_EOL;

        if ($APPLY) {
            foreach ($sets as $h => $v) {
                try { $e->setFieldValue($h, $v); }
                catch (\Throwable $ex) { echo '  set failed ' . $h . ' on ' . $e->slug . PHP_EOL; }
            }
            if (!$elements->saveElement($e)) {
                echo 'SAVE FAILED ' . $e->slug . ': ' . json_encode($e->getErrors()) . PHP_EOL;
                $failed++;
            }
        }
    }
}

/* ---------------------------------------------------------------- report */

echo '=== filled, by type and source ===' . PHP_EOL;
$grand = 0;
foreach ($filled as $sec => $sources) {
    $n = array_sum($sources);
    $grand += $n;
    $bits = [];
    foreach ($sources as $src => $c) { $bits[] = $src . ' ' . $c; }
    echo str_pad($sec, 16) . str_pad((string)$n, 5) . implode(', ', $bits) . PHP_EOL;
    if (isset($fieldsWritten[$sec])) {
        $fb = [];
        foreach ($fieldsWritten[$sec] as $h => $c) { $fb[] = $h . ' ' . $c; }
        echo str_pad('', 16) . '      fields: ' . implode(', ', $fb) . PHP_EOL;
    }
}
echo 'records touched: ' . $grand . PHP_EOL;
if ($failed) { echo 'saves failed:    ' . $failed . PHP_EOL; }

if ($foreignHost) {
    echo '=== legacy reference points at another host, not written ===' . PHP_EOL;
    foreach ($foreignHost as $r) { echo '  ' . $r . PHP_EOL; }
}

echo '=== no derivable provenance ===' . PHP_EOL;
$none = 0;
foreach ($noEvidence as $sec => $rows) {
    $none += count($rows);
    echo $sec . ': ' . count($rows) . PHP_EOL;
    foreach ($rows as $r) { echo '  ' . $r . PHP_EOL; }
}
echo 'records with nothing to derive from: ' . $none . PHP_EOL;
echo 'These may be born-digital records that never had a legacy page. Nothing was invented for them.' . PHP_EOL;
