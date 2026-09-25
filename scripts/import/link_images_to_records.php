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
 *   The relation. Written to derivedImageLinks and to nothing else, so a
 *   derived relation is never mistaken for one the archive confirmed. This is a
 *   database change and is what $APPLY governs.
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
$REPORT    = \Craft::getAlias('@review') . '/image-links.md';

/* One field, derivedImageLinks, whatever the target is.
 *
 * This used to choose among relatedArticles, relatedPlaces, photoPeople and the
 * rest, which are the fields the curated entity review writes and the JSON-LD
 * publishes. A relation somebody confirmed and a relation inferred from an href
 * would have been indistinguishable in the control panel, in the graph and in
 * the structured data, with no way back. The field is the provenance now:
 * anything here came from a legacy image link and nothing else writes to it. */
$FIELD = 'derivedImageLinks';

/* The chip a tile prints. A photograph record is not always a photograph: the
   legacy section holds clippings, maps, documents and programmes, and the title
   is where the archive says which. Read from the record, not invented. */
$KIND = function (\craft\elements\Entry $t): string {
    $section = $t->section->handle;
    if ($section !== 'photographs') {
        return rtrim(ucfirst($section), 's') === 'Person' ? 'Person' : ucfirst(rtrim($section, 's'));
    }
    $title = mb_strtolower((string)$t->title, 'UTF-8');
    foreach ([
        'Map'       => ['map', 'diseño', 'diseno', 'survey', 'plat'],
        'Clipping'  => ['signal', 'times', 'herald', 'dispatch', 'newspaper', 'clipping', 'headline'],
        'Postcard'  => ['postcard'],
        'Letter'    => ['letter', 'correspondence', 'envelope', 'telegram'],
        'Document'  => ['directory', 'programme', 'program book', 'deed', 'census', 'certificate',
                        'report', 'brochure', 'catalog', 'ledger', 'roster', 'menu', 'ticket'],
        'Poster'    => ['lobby card', 'poster', 'press kit', 'handbill'],
    ] as $label => $words) {
        foreach ($words as $w) { if (str_contains($title, $w)) { return $label; } }
    }
    return 'Photograph';
};

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

        /* Enough for the templates to route and label the target without a
           query per tile: which side of the page it belongs on, what chip to
           print, and the date as the source printed it. */
        $th = [];
        foreach ($t->getFieldLayout()->getCustomFields() as $tf) { $th[] = $tf->handle; }
        $date = '';
        foreach (['photoDate', 'originalPublishDate', 'eventDate', 'dateEstablished', 'dateFounded'] as $dh) {
            if (in_array($dh, $th, true)) {
                $v = trim((string)$t->getFieldValue($dh));
                if ($v !== '') { $date = $v; break; }
            }
        }

        $layer[$filename] = [
            'id'      => $t->id,
            'url'     => $t->url,
            'title'   => $t->title,
            'section' => $t->section->handle,
            'type'    => $t->type->handle,
            'kind'    => $KIND($t),
            'date'    => $date,
        ];
        $targets[$t->id] = $t;
    }
    if (!$layer) { continue; }

    file_put_contents($LAYER_DIR . '/' . $e->id . '.json',
        json_encode(['record' => $e->slug, 'links' => $layer], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $layerFiles++;
    $layerLinks += count($layer);

    if (!in_array($FIELD, $have, true)) {
        $noField[$e->section->handle] = true;
        continue;
    }
    foreach ($targets as $t) { $relPlan[$e->id][$FIELD][$t->id] = true; }
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
if ($noField) {
    echo 'these sections have no ' . $FIELD . ' field yet, so their relations are not planned: '
       . implode(', ', array_keys($noField)) . PHP_EOL;
    echo 'Run scripts/import/add_derived_image_links_field.php first.' . PHP_EOL;
}

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

/* Read the write back. A save that reports success and changes nothing is worse
 * than one that fails. add_footnote_source_column.php printed "saved: 5" twice
 * and persisted nothing, because it set a Table row's handle key while Craft
 * stores the column key, and the column key was null so it won at serialisation.
 *
 * Rows built here are keyed by handle alone and never carry a col key, which is
 * the safe case: with nothing to lose to, the handle is used. The check is here
 * because "it should be fine" is what the last one said. */
    $back = 0; $short = [];
    foreach ($relPlan as $eid => $byField) {
        $fresh = \craft\elements\Entry::find()->id($eid)->status(null)->one();
        if (!$fresh) { $short[] = '#' . $eid . ': gone after save'; continue; }
        $have = $fresh->getFieldValue($FIELD)->ids();
        $want = array_keys($byField[$FIELD] ?? []);
        $miss = array_diff($want, $have);
        if ($miss) { $short[] = $fresh->slug . ': ' . count($miss) . ' of ' . count($want) . ' not stored'; }
        $back += count(array_intersect($want, $have));
    }
    echo 'read back: ' . $back . ' relations present on their records' . PHP_EOL;
    if ($short) {
        echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
        foreach (array_slice($short, 0, 10) as $m) { echo '  ' . $m . PHP_EOL; }
        echo 'Do not re-run until this is understood.' . PHP_EOL;
        return;
    }
    echo 'verified.' . PHP_EOL;
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
