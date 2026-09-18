$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$term = function ($slug) {
    return \craft\elements\Category::find()->group('neighborhood')->slug($slug)->status(null)->one();
};
$map = [
  'city-of-santa-clarita' => ['newhall', 'saugus', 'valencia', 'canyon-country'],
  'santa-clarita-valley-chamber-of-commerce' => ['valencia'],
  'the-santa-clarita-valley-signal' => ['valencia'],
  'scvhistory-com-santa-clarita-valley-history' => ['newhall'],
];
foreach ($map as $slug => $terms) {
    $e = \craft\elements\Entry::find()->section('organizations')->slug($slug)->status(null)->one();
    if (!$e) { echo str_pad($slug, 46) . 'NOT FOUND' . PHP_EOL; continue; }
    if ($e->getFieldValue('neighborhood')->count()) { echo str_pad($e->title, 46) . 'already set' . PHP_EOL; continue; }
    $ids = [];
    foreach ($terms as $t) { $c = $term($t); if ($c) { $ids[] = $c->id; } else { echo '  missing term: ' . $t . PHP_EOL; } }
    echo str_pad($e->title, 46) . implode(', ', $terms) . PHP_EOL;
    if (!$APPLY) { continue; }
    $e->setFieldValue('neighborhood', $ids);
    echo '  ' . ($el->saveElement($e) ? 'saved' : 'FAILED') . PHP_EOL;
}
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
