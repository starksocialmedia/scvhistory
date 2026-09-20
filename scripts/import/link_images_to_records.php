/**
 * The images that were links to another page, not to a larger copy of themselves.
 *
 * On the legacy site a thumbnail usually opened the full scan, but often it
 * opened a different page: lw3749t.jpg linked to lw3749.htm, which is a record
 * in its own right. 26,563 of the 29,036 image references that carry a link
 * point at a page rather than at an image file, across 1,575 source records.
 * Sending those to the lightbox is wrong twice over: it shows a thumbnail
 * enlarged to nothing, and it throws away the only navigation the legacy page
 * offered between two records.
 *
 * The target key is the page's filename without .htm, which is the legacyKey we
 * already store. So resolution needs no guessing and no crawling.
 *
 * Two outputs, and they are deliberately separate.
 *
 *   The relation. Written onto the record through whichever relation field the
 *   pair of types actually has. This is a database change and is what $APPLY
 *   governs.
 *
 *   The render layer, templates/_data/image-links/<id>.json, one file per
 *   record, mapping an asset filename to the URL it should link to. This is
 *   only a file, so it is always written. It follows the pattern
 *   _data/prose-links already uses for text: the body is never rewritten, the
 *   layer can be regenerated or deleted without touching a record, and the
 *   templates go quiet if it is absent.
 *
 * A link to a page we do not hold is counted and dropped rather than written as
 * a dead link to scvhistory.com. The legacy URL is already on the record.
 *
 * Dry run by default for the relations. The render layer is written either way.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/link_images_to_records.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write relations to the database' . PHP_EOL; }

$root     = \Craft::getAlias('@root');
$LAYER_DIR = $root . '/templates/_data/image-links';
$REPORT    = \Craft::getAlias('@webroot') . '/review/image-links.md';

/* Which relation field to use for a target, in order of preference. The first
   one the source record actually carries wins. */
$FIELDS_BY_SECTION = [
    'photographs'   => ['relatedPhotographs', 'photoArticles', 'relatedArticles'],
    'articles'      => ['relatedArticles', 'photoArticles'],
    'persons'       => ['photoPeople', 'relatedPersons'],
    'places'        => ['photoPlaces', 'relatedPlaces'],
    'organizations' => ['photoOrganizations'],
    'events'        => ['photoEvents', 'relatedEvents'],
    'groups'        => ['photoGroups'],
];

/* ------------------------------------------------- the legacy link graph */

$files = glob($root . '/inventory/legacy/*-images.json');
sort($files);
$links = [];   /* source legacy_key => [ filename => target legacy_key ] */
$refs = 0; $toImage = 0;

foreach ($files as $f) {
    $data = json_decode(file_get_contents($f), true);
    foreach (($data['images'] ?? []) as $r) {
        $to = trim((string)($r['links_to'] ?? ''));
        if ($to === '') { continue; }
        $refs++;

        $tail = strtolower(basename(parse_url($to, PHP_URL_PATH) ?: $to));
        if (preg_match('/\.(jpe?g|png|gif|webp)$/', $tail)) { $toImage++; continue; }
        if (!preg_match('/^(.+)\.s?html?$/', $tail, $m)) { continue; }

        $src = strtolower(trim((string)($r['legacy_key'] ?? '')));
        $fn  = strtolower(basename(parse_url((string)($r['src_raw'] ?? ''), PHP_URL_PATH) ?: ''));
        if ($src === '' || $fn === '') { continue; }

        $links[$src][$fn] = $m[1];
    }
}

echo 'image references carrying a link: ' . $refs . PHP_EOL;
echo '  to an image file, left to the lightbox: ' . $toImage . PHP_EOL;
echo '  to a page: ' . ($refs - $toImage) . PHP_EOL;
echo '  source records with page links: ' . count($links) . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

/* --------------------------------------------------------- the records */

$byKey = [];
foreach (\craft\elements\Entry::find()->limit(null)->status(null)->all() as $e) {
    foreach ($e->getFieldLayout()->getCustomFields() as $f) {
        if ($f->handle === 'legacyKey') {
            $k = strtolower(trim((string)$e->getFieldValue('legacyKey')));
            if ($k !== '') { $byKey[$k] = $e; }
        }
    }
}
echo 'records with a legacyKey: ' . count($byKey) . PHP_EOL;

$elements = Craft::$app->getElements();
$handles = function (\craft\base\ElementInterface $el): array {
    $out = [];
    $layout = $el->getFieldLayout();
    if ($layout) { foreach ($layout->getCustomFields() as $f) { $out[] = $f->handle; } }
    return $out;
};

@mkdir($LAYER_DIR, 0775, true);

$layerFiles = 0; $layerLinks = 0; $missingTarget = 0; $noSource = 0;
$relPlan = []; $noField = [];

foreach ($links as $srcKey => $map) {
    if (!isset($byKey[$srcKey])) { $noSource++; continue; }
    $e = $byKey[$srcKey];
    $have = $handles($e);

    $layer = []; $targets = [];
    foreach ($map as $filename => $targetKey) {
        if (!isset($byKey[$targetKey])) { $missingTarget++; continue; }
        $t = $byKey[$targetKey];
        if ($t->id === $e->id) { continue; }
        $layer[$filename] = ['url' => $t->url, 'title' => $t->title];
        $targets[$t->id] = $t;
    }
    if (!$layer) { continue; }

    file_put_contents($LAYER_DIR . '/' . $e->id . '.json',
        json_encode(['record' => $e->slug, 'links' => $layer], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $layerFiles++;
    $layerLinks += count($layer);

    /* Group the targets by the field that can hold them. */
    foreach ($targets as $t) {
        $section = $t->section->handle;
        $candidates = $FIELDS_BY_SECTION[$section] ?? [];
        $field = null;
        foreach ($candidates as $c) { if (in_array($c, $have, true)) { $field = $c; break; } }
        if ($field === null) { $noField[$section . ' on ' . $e->section->handle] = true; continue; }
        $relPlan[$e->id][$field][$t->id] = true;
    }
}

echo 'render layer files written: ' . $layerFiles . PHP_EOL;
echo 'image links in the layer: ' . $layerLinks . PHP_EOL;
echo 'links to a page we do not hold, dropped: ' . $missingTarget . PHP_EOL;
echo 'source pages not in Craft: ' . $noSource . PHP_EOL;

$relCount = 0; $recCount = 0; $added = 0; $failed = [];
foreach ($relPlan as $eid => $byField) {
    $recCount++;
    foreach ($byField as $field => $ids) { $relCount += count($ids); }
}
echo 'records with relations to write: ' . $recCount . PHP_EOL;
echo 'relations to write: ' . $relCount . PHP_EOL;
if ($noField) { echo 'no relation field for: ' . implode('; ', array_keys($noField)) . PHP_EOL; }

if ($APPLY) {
    foreach ($relPlan as $eid => $byField) {
        $e = \craft\elements\Entry::find()->id($eid)->status(null)->one();
        if (!$e) { continue; }
        $touched = false;
        foreach ($byField as $field => $ids) {
            $current = $e->getFieldValue($field)->ids();
            $merged = array_values(array_unique(array_merge($current, array_keys($ids))));
            if (count($merged) === count($current)) { continue; }
            $e->setFieldValue($field, $merged);
            $added += count($merged) - count($current);
            $touched = true;
        }
        if ($touched && !$elements->saveElement($e)) { $failed[] = $e->slug; }
    }
    echo 'relations added: ' . $added . PHP_EOL;
    foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }
}

$out = [];
$out[] = '# Images that link to another record';
$out[] = '';
$out[] = 'Generated by scripts/import/link_images_to_records.php on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Relations: APPLIED' : 'Relations: DRY RUN, nothing written') . '. The render layer is written either way.';
$out[] = '';
$out[] = 'On the legacy site a thumbnail often opened a different page rather than a';
$out[] = 'larger copy of itself. Sending those to the lightbox shows a thumbnail enlarged';
$out[] = 'to nothing and throws away the only navigation the page offered between two';
$out[] = 'records. The target is the page filename without .htm, which is the legacyKey';
$out[] = 'we already store, so nothing here is guessed.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| image references carrying a link | ' . $refs . ' |';
$out[] = '| to an image file, left to the lightbox | ' . $toImage . ' |';
$out[] = '| to a page | ' . ($refs - $toImage) . ' |';
$out[] = '| render layer files written | ' . $layerFiles . ' |';
$out[] = '| image links in the layer | ' . $layerLinks . ' |';
$out[] = '| links to a page we do not hold, dropped | ' . $missingTarget . ' |';
$out[] = '| records with relations to write | ' . $recCount . ' |';
$out[] = '| relations to write | ' . $relCount . ' |';
$out[] = '';
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
