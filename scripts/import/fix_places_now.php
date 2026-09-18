/**
 * Deletes the Sleepy Valley stub and corrects the Lake Hughes title.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_places_now.php'))"
 */
$el = Craft::$app->getElements();

$sv = \craft\elements\Entry::find()->section('places')->slug('sleepy-valley')->status(null)->one();
if ($sv) {
    $in = \craft\elements\Entry::find()->relatedTo($sv)->status(null)->count();
    echo 'sleepy-valley: deleting (' . $in . ' inbound relations)' . PHP_EOL;
    $el->deleteElement($sv);
} else {
    echo 'sleepy-valley: already gone' . PHP_EOL;
}

foreach (\craft\elements\Entry::find()->section('places')->status(null)->all() as $e) {
    if (stripos($e->title, 'Lake Hug') === 0 && $e->title !== 'Lake Hughes') {
        echo 'was: ' . $e->title . PHP_EOL;
        $e->title = 'Lake Hughes';
        echo ($el->saveElement($e) ? 'now: Lake Hughes' : 'FAILED') . PHP_EOL;
    }
}
echo 'places remaining: ' . \craft\elements\Entry::find()->section('places')->status(null)->count() . PHP_EOL;
