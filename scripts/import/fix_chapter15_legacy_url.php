/**
 * Corrects the legacy path on Chapter 15 of the Reynolds history.
 *
 * Its legacyUrl reads /signal/reynolds/part15.html where every one of its
 * seventy-nine siblings reads /scvhistory/signal/reynolds/partNN.html. The
 * /scvhistory/ segment is missing, so the URL 404s on the legacy site and no
 * entity mention on that page could be joined to the record. That is the whole
 * reason 10 relation candidates had no article to attach to.
 *
 * sourcePath carries the same defect, https://scvhistory.com/signal/... , and
 * is corrected with it: it is the provenance line a reader would follow.
 *
 * Both are written only while they still hold exactly the broken value, and the
 * corrected URL was requested and answers 200 before this script was written.
 * A second run finds them already right and does nothing.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_chapter15_legacy_url.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$FIX = [
    ['legacyUrl',  '/signal/reynolds/part15.html',
                   '/scvhistory/signal/reynolds/part15.html'],
    ['sourcePath', 'https://scvhistory.com/signal/reynolds/part15.html',
                   'https://scvhistory.com/scvhistory/signal/reynolds/part15.html'],
];

$elements = Craft::$app->getElements();
$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

$e = \craft\elements\Entry::find()->section('articles')->slug('chapter-15-family-squabbles')->status(null)->one();
if (!$e) { echo 'chapter-15-family-squabbles not found' . PHP_EOL; return; }

echo $e->title . '  #' . $e->id . PHP_EOL;

/* What the siblings look like, so the shape of the fix is visible rather than
   asserted. */
$siblings = 0; $matching = 0;
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->all() as $a) {
    if (!$hasField($a, 'legacyUrl')) { continue; }
    $lu = trim((string)$a->legacyUrl);
    if (preg_match('~/signal/reynolds/part\d+\.html$~', $lu)) {
        $siblings++;
        if (str_starts_with($lu, '/scvhistory/signal/reynolds/')) { $matching++; }
    }
}
echo 'Reynolds chapters with a part URL: ' . $siblings . ', of which ' . $matching
    . ' already carry the /scvhistory/ segment' . PHP_EOL . PHP_EOL;

$changed = 0;
foreach ($FIX as [$handle, $was, $now]) {
    if (!$hasField($e, $handle)) { echo str_pad($handle, 12) . 'no such field' . PHP_EOL; continue; }
    $current = trim((string)$e->getFieldValue($handle));

    if ($current === $now) { echo str_pad($handle, 12) . 'already corrected' . PHP_EOL; continue; }
    if ($current !== $was) {
        echo str_pad($handle, 12) . 'SKIP, holds "' . $current . '"' . PHP_EOL;
        echo str_pad('', 12) . 'which is neither the broken value nor the fix; decide by hand' . PHP_EOL;
        continue;
    }
    echo str_pad($handle, 12) . 'was:  ' . $current . PHP_EOL;
    echo str_pad('', 12) . 'now:  ' . $now . PHP_EOL;
    $changed++;
    if ($APPLY) { $e->setFieldValue($handle, $now); }
}

if ($APPLY && $changed) {
    echo PHP_EOL . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
    echo 'Now re-run export_relation_candidates.php so the 10 orphaned candidates attach.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 72) . PHP_EOL;
echo 'fields that would change: ' . $changed . PHP_EOL;
if (!$APPLY) { echo 'nothing written.' . PHP_EOL; }
