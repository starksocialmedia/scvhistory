$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$map = [
  'story-of-our-valley' => 'arthur-b-perkins',
  'history-of-the-santa-clarita-valley' => 'jerry-reynolds',
];
foreach ($map as $colSlug => $personSlug) {
    $col = \craft\elements\Entry::find()->section('collections')->slug($colSlug)->status(null)->one();
    $person = \craft\elements\Entry::find()->section('persons')->slug($personSlug)->status(null)->one();
    if (!$col || !$person) { echo $colSlug . ': ' . ($col ? '' : 'collection ') . ($person ? '' : 'person ') . 'not found' . PHP_EOL; continue; }
    $arts = \craft\elements\Entry::find()->section('articles')
        ->relatedTo(['targetElement' => $col, 'field' => 'partOfCollection'])->status(null)->all();
    $n = 0;
    foreach ($arts as $a) {
        if ($a->getFieldValue('writtenBy')->count()) { continue; }
        $n++;
        if ($APPLY) { $a->setFieldValue('writtenBy', [$person->id]); $el->saveElement($a); }
    }
    echo str_pad($colSlug, 40) . $person->title . ': ' . $n . ' of ' . count($arts) . ' need an author' . PHP_EOL;
}
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
