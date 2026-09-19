/**
 * Recovers landmark values from the WordPress export onto the matching places,
 * and reports every value in that export that has nowhere in Craft to go.
 *
 * The export is inventory/wp_content.json: 91 posts, 77 distinct meta keys with
 * a value. This walks the landmark keys onto the place records, and then walks
 * every other key to say whether it landed anywhere, so nothing can be dropped
 * without it being written down.
 *
 * Landmark keys handled here:
 *   place_chl_number       -> placeChlNumber
 *   place_chl_url          -> placeChlUrl
 *   place_scvhl_checkbox   -> placeScvhlCheckbox
 *   place_featured         -> placeFeatured
 *   place_date_built       -> reported only; see below
 *
 * Only an empty field is filled. A value already in Craft always wins, because
 * it has been looked at since and the export has not.
 *
 * Matching is by slug first, then by exact title, and a place that matches
 * neither is reported rather than guessed at.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/recover_wp_landmarks.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$root = \Craft::getAlias('@root');
$path = $root . '/inventory/wp_content.json';
if (!file_exists($path)) { echo 'not found: ' . $path . PHP_EOL; return; }
$wp = json_decode(file_get_contents($path), true);
if (!is_array($wp)) { echo 'could not parse ' . $path . PHP_EOL; return; }

$elements = Craft::$app->getElements();
$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/* meta key => [craft handle, how to read it] */
$LANDMARK = [
    'place_chl_number'     => ['placeChlNumber',     'text'],
    'place_chl_url'        => ['placeChlUrl',        'text'],
    'place_scvhl_checkbox' => ['placeScvhlCheckbox', 'bool'],
    'place_featured'       => ['placeFeatured',      'bool'],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'export: inventory/wp_content.json, ' . count($wp['posts'] ?? []) . ' posts' . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

$set = 0; $records = 0; $kept = []; $unmatched = []; $allFalse = [];

foreach (($wp['posts'] ?? []) as $post) {
    if (($post['type'] ?? '') !== 'place') { continue; }
    $meta = $post['meta'] ?? [];

    $e = \craft\elements\Entry::find()->section('places')->slug($post['slug'] ?? '')->status(null)->one();
    if (!$e) { $e = \craft\elements\Entry::find()->section('places')->title($post['title'] ?? '')->status(null)->one(); }
    if (!$e) {
        $unmatched[] = ($post['title'] ?? '?') . '  (slug ' . ($post['slug'] ?? '?') . ')';
        continue;
    }

    echo PHP_EOL . $e->title . '  (#' . $e->id . ', matched on '
        . (($e->slug === ($post['slug'] ?? '')) ? 'slug' : 'title') . ')' . PHP_EOL;

    $toSet = [];
    foreach ($LANDMARK as $key => [$handle, $kind]) {
        if (!array_key_exists($key, $meta)) { continue; }
        $raw = $meta[$key];
        if ($raw === null || $raw === '') { continue; }

        if ($kind === 'bool') {
            $on = in_array((string)$raw, ['1', 'true', 'yes', 'on'], true);
            if (!$on) {
                echo '  ' . str_pad($key, 24) . 'is "' . $raw . '", false in WordPress too, nothing to recover' . PHP_EOL;
                $allFalse[] = $e->title . ' ' . $key;
                continue;
            }
        }

        if (!$hasField($e, $handle)) {
            echo '  ' . str_pad($key, 24) . 'NO FIELD ' . $handle . ' on this entry type' . PHP_EOL;
            continue;
        }

        $current = '';
        try {
            $v = $e->getFieldValue($handle);
            $current = is_bool($v) ? ($v ? '1' : '') : trim((string)$v);
        } catch (\Throwable $ex) { $current = ''; }

        if ($current !== '') {
            echo '  ' . str_pad($key, 24) . 'already holds "' . $current . '", left alone' . PHP_EOL;
            $kept[] = $e->title . ' ' . $handle;
            continue;
        }

        $value = $kind === 'bool' ? true : (string)$raw;
        echo '  ' . str_pad($key, 24) . 'SET ' . $handle . ' = ' . ($kind === 'bool' ? 'true' : $value) . PHP_EOL;
        $toSet[$handle] = $value;
    }

    /* place_date_built has no field. Where dateEstablished already says the
       same thing there is nothing to recover; where it differs, say so. */
    if (!empty($meta['place_date_built'])) {
        $built = trim((string)$meta['place_date_built']);
        $est = $hasField($e, 'dateEstablished') ? trim((string)$e->dateEstablished) : '';
        echo '  ' . str_pad('place_date_built', 24)
            . ($est === $built
                ? 'is "' . $built . '", same as dateEstablished, nothing lost'
                : 'is "' . $built . '" and dateEstablished is "' . ($est ?: 'empty') . '"; no field for it')
            . PHP_EOL;
    }

    if ($toSet) {
        $records++;
        $set += count($toSet);
        if ($APPLY) {
            foreach ($toSet as $h => $v) {
                try { $e->setFieldValue($h, $v); }
                catch (\Throwable $ex) { echo '  set ' . $h . ' failed: ' . $ex->getMessage() . PHP_EOL; }
            }
            echo '  ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
        }
    }
}

echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
echo 'places in the export: ' . count(array_filter($wp['posts'] ?? [], fn($p) => ($p['type'] ?? '') === 'place')) . PHP_EOL;
echo 'records that would change: ' . $records . PHP_EOL;
echo 'fields that would be set:  ' . $set . PHP_EOL;
if ($allFalse) {
    echo PHP_EOL . 'switches that are false in WordPress as well, ' . count($allFalse) . '.' . PHP_EOL;
    echo 'These were not dropped at import; they were never on.' . PHP_EOL;
    foreach ($allFalse as $a) { echo '  ' . $a . PHP_EOL; }
}
if ($kept) {
    echo PHP_EOL . 'fields already holding a value, left alone:' . PHP_EOL;
    foreach ($kept as $k) { echo '  ' . $k . PHP_EOL; }
}
if ($unmatched) {
    echo PHP_EOL . 'WordPress places with no record in Craft:' . PHP_EOL;
    foreach ($unmatched as $u) { echo '  ' . $u . PHP_EOL; }
}

/* ------------------------------------- every other key, and where it went */

echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
echo 'EVERY META KEY IN THE EXPORT, AND WHETHER IT HAS A HOME' . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

$handles = [];
foreach (Craft::$app->getFields()->getAllFields() as $f) { $handles[strtolower($f->handle)] = $f->handle; }

/* Keys that did land, under a name the guesser cannot reach on its own. */
$RENAMED = [
    'latitude' => 'placeLat, orgLat', 'longitude' => 'placeLng, orgLng',
    'scv_era' => 'historicalEra, a category rather than a field',
    'description' => 'collection body', 'collection_note' => 'collection body',
    'branch_logo' => 'derived from wmBranch in the template, no field needed',
    'place_date_built' => 'dateEstablished carries the same year',
];

$camel = function (string $k): string {
    $parts = explode('_', $k);
    return $parts[0] . implode('', array_map('ucfirst', array_slice($parts, 1)));
};
$PREFIX = ['', 'place', 'org', 'event', 'mp', 'obit', 'wm', 'person', 'group', 'collection', 'article'];

$keys = [];
foreach (($wp['posts'] ?? []) as $p) {
    foreach (($p['meta'] ?? []) as $k => $v) {
        if ($v === null || $v === '' || $v === []) { continue; }
        $keys[$k]['n'] = ($keys[$k]['n'] ?? 0) + 1;
        $keys[$k]['types'][$p['type'] ?? '?'] = true;
        $keys[$k]['allZero'] = ($keys[$k]['allZero'] ?? true) && ((string)$v === '0');
        if (count($keys[$k]['sample'] ?? []) < 1) { $keys[$k]['sample'][] = is_scalar($v) ? (string)$v : json_encode($v); }
    }
}
ksort($keys);

$homeless = [];
foreach ($keys as $k => $info) {
    $base = $camel($k);
    $stripped = lcfirst((string)preg_replace('~^(place|org|event|mp|obit|wm|person|group|collection|article)~', '', $base));
    $found = null;
    foreach ($PREFIX as $pre) {
        foreach ([$base, $stripped] as $cand) {
            if ($cand === '') { continue; }
            $try = $pre === '' ? $cand : $pre . ucfirst($cand);
            if (isset($handles[strtolower($try)])) { $found = $handles[strtolower($try)]; break 2; }
        }
    }
    if (!$found && isset($RENAMED[$k])) { $found = $RENAMED[$k]; }
    if (!$found) { $homeless[$k] = $info; }
}

echo 'keys with a value: ' . count($keys) . ', of which ' . count($homeless) . ' have no home' . PHP_EOL . PHP_EOL;
foreach ($homeless as $k => $i) {
    $zero = !empty($i['allZero']);
    echo str_pad($k, 22) . str_pad((string)$i['n'], 4) . str_pad(implode(',', array_keys($i['types'])), 22)
        . ($zero ? 'every value is 0, nothing to lose' : 'e.g. ' . mb_substr($i['sample'][0] ?? '', 0, 56)) . PHP_EOL;
}
echo PHP_EOL . ($APPLY ? 'applied.' : 'nothing written.') . PHP_EOL;
