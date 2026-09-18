$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$y = \craft\elements\Entry::find()->section('persons')->slug('ygnacio-del-valle')->status(null)->one();
$a = \craft\elements\Entry::find()->section('persons')->slug('antonio-del-valle')->status(null)->one();
$j = \craft\elements\Entry::find()->section('persons')->slug('juventino-del-valle')->status(null)->one();
if (!$y || !$a || !$j) { echo 'missing a record' . PHP_EOL; return; }
echo 'Reynolds, Chapter 15: "Senorita Carrillo bore him a son, Juventino"' . PHP_EOL;
echo 'Reynolds, Chapter 15: "Seven months after Don Antonio died ... Ygnacio"' . PHP_EOL . PHP_EOL;
echo 'Ygnacio parents now:  ' . implode(', ', array_map(fn($p) => $p->title, $y->getFieldValue('childOf')->all()) ?: ['none']) . PHP_EOL;
echo 'Ygnacio siblings now: ' . implode(', ', array_map(fn($p) => $p->title, $y->getFieldValue('siblingOf')->all()) ?: ['none']) . PHP_EOL;
echo 'Juventino parents now: ' . implode(', ', array_map(fn($p) => $p->title, $j->getFieldValue('childOf')->all()) ?: ['none']) . PHP_EOL . PHP_EOL;
echo 'would set: Ygnacio childOf Antonio, siblings cleared' . PHP_EOL;
echo 'would set: Juventino childOf Ygnacio' . PHP_EOL;
if (!$APPLY) { echo PHP_EOL . 'DRY RUN' . PHP_EOL; return; }
$y->setFieldValue('childOf', [$a->id]);
$y->setFieldValue('siblingOf', []);
echo 'Ygnacio: ' . ($el->saveElement($y) ? 'saved' : 'FAILED') . PHP_EOL;
$j->setFieldValue('childOf', [$y->id]);
echo 'Juventino: ' . ($el->saveElement($j) ? 'saved' : 'FAILED') . PHP_EOL;
