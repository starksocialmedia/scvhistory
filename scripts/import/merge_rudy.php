$APPLY = true;
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
$el->deleteElement($drop);
echo 'dropped 877' . PHP_EOL;
echo 'war memorial count: ' . \craft\elements\Entry::find()->section('warMemorials')->status(null)->count() . PHP_EOL;
