$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$keep = \craft\elements\Entry::find()->id(526)->status(null)->one();
$drop = \craft\elements\Entry::find()->id(877)->status(null)->one();
if (!$keep || !$drop) { echo 'one of them is gone' . PHP_EOL; return; }

$moves = [];
$dropBody = (string)$drop->getFieldValue('body');
$keepBody = (string)$keep->getFieldValue('body');
if (strlen($dropBody) > strlen($keepBody)) { $moves['body'] = $dropBody; }

$img = $drop->getFieldValue('featuredImage')->ids();
if (count($img) && !$keep->getFieldValue('featuredImage')->count()) { $moves['featuredImage'] = $img; }

$rank = (string)$drop->getFieldValue('wmRank');
if (str_contains($rank, 'SP4')) { $moves['wmRank'] = $rank; }

echo 'keep 526, drop 877' . PHP_EOL;
foreach ($moves as $h => $v) {
    echo '  move ' . str_pad($h, 16) . (is_array($v) ? count($v) . ' asset(s)' : mb_substr((string)$v, 0, 70)) . PHP_EOL;
}
if (!$APPLY) { echo PHP_EOL . 'DRY RUN' . PHP_EOL; return; }
foreach ($moves as $h => $v) { $keep->setFieldValue($h, $v); }
echo 'keep saved: ' . ($el->saveElement($keep) ? 'ok' : 'FAILED') . PHP_EOL;

/* Read the write back before deleting the other record, because a delete is not
 * reversible and a save that reported success is not proof that anything moved.
 * add_footnote_source_column.php printed "saved: 5" twice and persisted nothing.
 * Here that would have meant losing the only copy. */
$fresh = \craft\elements\Entry::find()->id($keep->id)->status(null)->one();
$short = [];
foreach ($moves as $h => $v) {
    $got = $fresh ? $fresh->getFieldValue($h) : null;
    if (is_array($v)) {
        $have = is_object($got) && method_exists($got, 'ids') ? $got->ids() : [];
        if (array_diff($v, $have)) { $short[] = $h . ': ' . count(array_diff($v, $have)) . ' of ' . count($v) . ' not stored'; }
    } else {
        if (trim((string)$got) !== trim((string)$v)) { $short[] = $h . ': stored value does not match'; }
    }
}
if ($short) {
    echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
    foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }
    echo 'NOT deleting the other record. Nothing has been lost.' . PHP_EOL;
    return;
}
echo 'verified: every moved field reads back.' . PHP_EOL;

$el->deleteElement($drop);
echo 'dropped 877' . PHP_EOL;
echo 'war memorial count: ' . \craft\elements\Entry::find()->section('warMemorials')->status(null)->count() . PHP_EOL;
