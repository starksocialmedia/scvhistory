/**
 * Rewrites every legacy URL field to a root-relative path with no host.
 *
 * An absolute URL on the legacy host loses its scheme and host, a bare path gains
 * a leading slash, and anything already root-relative is left alone.
 *
 * A value pointing at some other host is NOT rewritten. Stripping the host off
 * https://sangabrielmission.org/x would leave /x, which then resolves against the
 * legacy archive and points at the wrong site. Those are reported for a human.
 *
 * sourcePath is provenance and keeps its full stored host, so it is not touched.
 * archiveUrl points at Archive.org, not the legacy archive, so it is not touched.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/normalise_legacy_urls.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLES = [
    'legacyUrl', 'eventLegacyUrl', 'groupLegacyUrl', 'mpLegacyUrl',
    'obitLegacyUrl', 'orgLegacyUrl', 'personLegacyUrl', 'placeLegacyUrl',
];

/* Hosts that belong to the legacy archive and may be stripped. */
$configured = (string)(Craft::$app->config->custom->legacyHost ?? 'https://scvhistory.com');
$legacyHostName = strtolower((string)(parse_url($configured, PHP_URL_HOST) ?: 'scvhistory.com'));
$OWN_HOSTS = array_unique([$legacyHostName, 'www.' . ltrim($legacyHostName, 'w.'), 'scvhistory.com', 'www.scvhistory.com']);

/* TLDs a bare value may end in before it counts as a host rather than a path.
   Without this, newhall.htm would read as the host "newhall" in the TLD "htm". */
$TLDS = ['com', 'org', 'net', 'edu', 'gov', 'mil', 'us', 'info', 'co', 'io', 'tv'];

$elements = Craft::$app->getElements();

$classify = function (string $v) use ($OWN_HOSTS, $TLDS): array {
    $v = trim($v);
    if ($v === '') { return ['empty', $v]; }

    $stripToPath = function (string $url): string {
        $parts = parse_url($url);
        $path = $parts['path'] ?? '';
        if ($path === '') { $path = '/'; }
        if ($path[0] !== '/') { $path = '/' . $path; }
        if (isset($parts['query'])) { $path .= '?' . $parts['query']; }
        if (isset($parts['fragment'])) { $path .= '#' . $parts['fragment']; }
        return $path;
    };

    if (preg_match('~^https?://~i', $v)) {
        $host = strtolower((string)(parse_url($v, PHP_URL_HOST) ?: ''));
        if (in_array($host, $OWN_HOSTS, true)) { return ['absolute-own-host', $stripToPath($v)]; }
        return ['absolute-foreign-host', $v];
    }

    if (str_starts_with($v, '//')) {
        $host = strtolower((string)(parse_url('https:' . $v, PHP_URL_HOST) ?: ''));
        if (in_array($host, $OWN_HOSTS, true)) { return ['protocol-relative-own-host', $stripToPath('https:' . $v)]; }
        return ['protocol-relative-foreign-host', $v];
    }

    if (str_starts_with($v, '/')) { return ['root-relative', $v]; }

    $firstSeg = strtolower(explode('/', $v)[0]);
    $tld = strtolower((string)(strrchr($firstSeg, '.') ?: ''));
    $tld = ltrim($tld, '.');
    if ($tld !== '' && in_array($tld, $TLDS, true)) {
        $host = preg_replace('~^www\.~i', '', $firstSeg);
        if (in_array($firstSeg, $OWN_HOSTS, true) || in_array($host, $OWN_HOSTS, true)) {
            return ['bare-own-host', $stripToPath('https://' . $v)];
        }
        return ['bare-foreign-host', $v];
    }

    return ['bare-path', '/' . $v];
};

$hasField = function (\craft\base\ElementInterface $el, string $handle) use ($HANDLES): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) {
        if ($f->handle === $handle) { return true; }
    }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'legacy host: ' . $configured . '  (strippable hosts: ' . implode(', ', $OWN_HOSTS) . ')' . PHP_EOL;
echo 'fields: ' . implode(', ', $HANDLES) . PHP_EOL;
echo 'not touched: sourcePath (provenance), archiveUrl (Archive.org)' . PHP_EOL;

$targets = [];
foreach (Craft::$app->getEntries()->getAllSections() as $s) {
    foreach (\craft\elements\Entry::find()->section($s->handle)->status(null)->all() as $e) {
        $targets[] = ['entry ' . $s->handle, $e];
    }
}
foreach (Craft::$app->categories->getAllGroups() as $g) {
    foreach (\craft\elements\Category::find()->group($g->handle)->status(null)->all() as $c) {
        $targets[] = ['category ' . $g->handle, $c];
    }
}

$byShape = [];      /* shape => ['changed' => n, 'left' => n, 'rows' => [] ] */
$seen = 0; $changed = 0; $failed = 0;

foreach ($targets as [$where, $el]) {
    $sets = [];
    foreach ($HANDLES as $h) {
        if (!$hasField($el, $h)) { continue; }
        try { $raw = (string)$el->getFieldValue($h); }
        catch (\Throwable $ex) { continue; }
        $v = trim($raw);
        if ($v === '') { continue; }
        $seen++;

        [$shape, $new] = $classify($v);
        if (!isset($byShape[$shape])) { $byShape[$shape] = ['changed' => 0, 'left' => 0, 'rows' => []]; }

        if ($new === $v) {
            $byShape[$shape]['left']++;
            if (count($byShape[$shape]['rows']) < 200) {
                $byShape[$shape]['rows'][] = sprintf('%-22s %-16s #%-5d %s', $where, $h, $el->id, $v);
            }
            continue;
        }

        $byShape[$shape]['changed']++;
        $changed++;
        $byShape[$shape]['rows'][] = sprintf('%-22s %-16s #%-5d %s  ->  %s', $where, $h, $el->id, $v, $new);
        $sets[$h] = $new;
    }

    if ($APPLY && count($sets)) {
        foreach ($sets as $h => $new) {
            try { $el->setFieldValue($h, $new); }
            catch (\Throwable $ex) { echo '  set failed ' . $h . ' on #' . $el->id . ': ' . $ex->getMessage() . PHP_EOL; }
        }
        if (!$elements->saveElement($el)) {
            echo 'SAVE FAILED #' . $el->id . ': ' . json_encode($el->getErrors()) . PHP_EOL;
            $failed++;
        }
    }
}

echo '=== by shape ===' . PHP_EOL;
$order = ['absolute-own-host', 'protocol-relative-own-host', 'bare-own-host', 'bare-path',
          'root-relative', 'absolute-foreign-host', 'protocol-relative-foreign-host', 'bare-foreign-host'];
foreach ($order as $shape) {
    if (!isset($byShape[$shape])) { continue; }
    $b = $byShape[$shape];
    $verb = $b['changed'] ? $b['changed'] . ' to rewrite' : '0 to rewrite';
    echo $shape . ': ' . $verb . ', ' . $b['left'] . ' left alone' . PHP_EOL;
    foreach (array_slice($b['rows'], 0, 25) as $r) { echo '  ' . $r . PHP_EOL; }
    if (count($b['rows']) > 25) { echo '  ... and ' . (count($b['rows']) - 25) . ' more' . PHP_EOL; }
}
foreach ($byShape as $shape => $b) {
    if (!in_array($shape, $order, true)) {
        echo $shape . ': ' . $b['changed'] . ' to rewrite, ' . $b['left'] . ' left alone' . PHP_EOL;
    }
}

echo '=== summary ===' . PHP_EOL;
echo 'elements scanned:     ' . count($targets) . PHP_EOL;
echo 'legacy URLs found:    ' . $seen . PHP_EOL;
echo 'values to rewrite:    ' . $changed . PHP_EOL;
if ($failed) { echo 'saves failed:         ' . $failed . PHP_EOL; }
$foreign = 0;
foreach (['absolute-foreign-host', 'protocol-relative-foreign-host', 'bare-foreign-host'] as $s) {
    $foreign += ($byShape[$s]['left'] ?? 0);
}
if ($foreign) {
    echo 'pointing at another host, left alone for a human: ' . $foreign . PHP_EOL;
}
echo 'Running again changes nothing: a root-relative value is already in its final shape.' . PHP_EOL;
