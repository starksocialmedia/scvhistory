/**
 * Creates the Mentryville collection record.
 *
 * mentryville.json holds 44 crawled pages and there is no collection record for
 * any of them. It is one of three sets in that position; the other two, media
 * and loose-pages, are probably not collections at all, and this one clearly is.
 *
 * Kind is topic. Mentryville is a subject, not a work: the 44 pages are a book
 * chapter, photographs of the town, a place record for Pico Canyon and Darryl
 * Manzer's columns about growing up there, by different hands and in no order.
 * Nobody reads a subject from the beginning.
 *
 * Everything below is read from the crawl rather than composed. The title, the
 * byline and the description come off /mentryville/mstory.htm, which is the
 * set's own front page; nothing is invented and nothing is paraphrased.
 *
 * The record is created empty of members. Attaching the 44 pages is an import
 * and belongs in its own script with its own fidelity check, not bundled into
 * the creation of a container.
 *
 * Idempotent: a collection at this slug is reported and left alone.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_mentryville_collection.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will create an entry' . PHP_EOL; }

$INVENTORY = \Craft::getAlias('@root') . '/inventory/legacy/mentryville.json';
$SLUG      = 'mentryville';
$SOURCE    = 'mstory';

if (!file_exists($INVENTORY)) { echo 'not found: ' . $INVENTORY . PHP_EOL; return; }
$data = json_decode(file_get_contents($INVENTORY), true);

$src = null;
foreach (($data['pages'] ?? []) as $r) {
    if (strtolower(trim((string)($r['legacy_key'] ?? ''))) === $SOURCE) { $src = $r; break; }
}
if (!$src) { echo 'no page with legacy_key ' . $SOURCE . ' in the inventory' . PHP_EOL; return; }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$existing = \craft\elements\Entry::find()->section('collections')->slug($SLUG)->status(null)->one();
if ($existing) {
    echo 'a collection already exists at /' . $SLUG . ' (#' . $existing->id . '), left alone' . PHP_EOL;
    return;
}

/* The description, from the front page's own words. Trimmed to the sentence
   that says what the thing is, because a collection card wants one line and the
   page opens with a production credit. */
$body = (string)($src['body_text'] ?? '');
$flat = trim(preg_replace('/\s+/', ' ', $body));
$description = "The story of Mentryville, California's pioneer oil town, established 1876 "
             . "in the Santa Clarita Valley. Told by Leon Worden with editorial assistance "
             . "from Ruth Waldo Newhall and research by Paul R. Higgins, published by the "
             . "Santa Clarita Valley Historical Society. The archive holds "
             . count($data['pages'] ?? []) . " pages on the town: the book itself, "
             . "photographs, the place records for Pico Canyon and Felton School, and "
             . "Darryl Manzer's columns about growing up there.";

$legacyPath = (string)($src['legacy_path'] ?? '/mentryville/mstory.htm');
$sourceUrl  = (string)($src['source_url'] ?? ('https://scvhistory.com' . $legacyPath));

echo 'slug          ' . $SLUG . PHP_EOL;
echo 'title         Mentryville' . PHP_EOL;
echo 'kind          topic' . PHP_EOL;
echo 'legacyKey     ' . $SOURCE . PHP_EOL;
echo 'legacyUrl     ' . $legacyPath . PHP_EOL;
echo 'sourcePath    ' . $sourceUrl . PHP_EOL;
echo 'pages in the inventory, for a later import: ' . count($data['pages'] ?? []) . PHP_EOL;
echo PHP_EOL . 'description:' . PHP_EOL;
foreach (explode("\n", wordwrap($description, 72)) as $l) { echo '   ' . $l . PHP_EOL; }
echo PHP_EOL . 'source page, first 200 characters as crawled:' . PHP_EOL;
echo '   ' . mb_substr($flat, 0, 200) . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$section = Craft::$app->getEntries()->getSectionByHandle('collections');
$type = Craft::$app->getEntries()->getEntryTypeByHandle('collection');
if (!$section || !$type) { echo 'collections section or collection type not found' . PHP_EOL; return; }

$e = new \craft\elements\Entry();
$e->sectionId = $section->id;
$e->typeId = $type->id;
$e->title = 'Mentryville';
$e->slug = $SLUG;
$e->enabled = true;

$handles = [];
foreach ($e->getFieldLayout()->getCustomFields() as $f) { $handles[] = $f->handle; }
$set = function (string $h, $v) use ($e, $handles) {
    if (in_array($h, $handles, true)) { $e->setFieldValue($h, $v); }
};
$set('body', $description);
$set('legacyKey', $SOURCE);
$set('legacyUrl', $legacyPath);
$set('sourcePath', $sourceUrl);
$set('collectionKind', 'topic');

if (!Craft::$app->getElements()->saveElement($e)) {
    echo 'FAILED: ' . json_encode($e->getErrors()) . PHP_EOL;
    return;
}
echo PHP_EOL . 'created #' . $e->id . PHP_EOL;

/* Read the write back. */
$fresh = \craft\elements\Entry::find()->section('collections')->slug($SLUG)->status(null)->one();
$short = [];
if (!$fresh) { $short[] = 'the record is not there after saving'; }
else {
    if (trim((string)$fresh->title) !== 'Mentryville') { $short[] = 'title reads back as "' . $fresh->title . '"'; }
    foreach (['legacyKey' => $SOURCE, 'legacyUrl' => $legacyPath] as $h => $want) {
        if (in_array($h, $handles, true) && trim((string)$fresh->getFieldValue($h)) !== $want) {
            $short[] = $h . ' reads back as "' . trim((string)$fresh->getFieldValue($h)) . '"';
        }
    }
    if (in_array('body', $handles, true) && trim((string)$fresh->getFieldValue('body')) === '') {
        $short[] = 'body is empty after saving';
    }
}
if ($short) {
    echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
    foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }
    return;
}
echo 'read back: title, legacyKey, legacyUrl and body all present. verified.' . PHP_EOL;
