$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$map = [
  'worden'      => ['Leon Worden', 'leon-worden'],
  'boston'      => ['John Boston', null],
  'manzer'      => ['Darryl Manzer', null],
  'coins'       => ['Sol Taylor', null],
  'otn-rioux'   => ['Richard Rioux', null],
  'otn-whyte'   => ['Tim Whyte', null],
  'otn-patti'   => ['Patti Rasmussen', null],
  'otn-pauline' => ['Pauline Harte', null],
];
foreach ($map as $slug => [$name, $personSlug]) {
    $c = \craft\elements\Entry::find()->section('collections')->slug($slug)->status(null)->one();
    if (!$c) { echo str_pad($slug, 14) . 'collection NOT FOUND' . PHP_EOL; continue; }
    if ($c->getFieldValue('writtenBy')->count()) { echo str_pad($slug, 14) . 'already has an author' . PHP_EOL; continue; }
    $p = $personSlug
        ? \craft\elements\Entry::find()->section('persons')->slug($personSlug)->status(null)->one()
        : \craft\elements\Entry::find()->section('persons')->title($name)->status(null)->one();
    echo str_pad($slug, 14) . $name . '  ' . ($p ? 'person #' . $p->id : 'NO PERSON RECORD, would create') . PHP_EOL;
    if (!$APPLY) { continue; }
    if (!$p) {
        $p = new \craft\elements\Entry();
        $p->sectionId = $svc->getSectionByHandle('persons')->id;
        $p->typeId = $svc->getEntryTypeByHandle('person')->id;
        $p->title = $name;
        $p->setFieldValue('fullName', $name);
        if (!$el->saveElement($p)) { echo '  person FAILED ' . json_encode($p->getErrors()) . PHP_EOL; continue; }
        echo '  person created #' . $p->id . PHP_EOL;
    }
    $c->setFieldValue('writtenBy', [$p->id]);
    echo '  ' . ($el->saveElement($c) ? 'saved' : 'FAILED') . PHP_EOL;
}
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
