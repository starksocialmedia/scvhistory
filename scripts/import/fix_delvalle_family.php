$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$y = \craft\elements\Entry::find()->section('persons')->slug('ygnacio-del-valle')->status(null)->one();
$a = \craft\elements\Entry::find()->section('persons')->slug('antonio-del-valle')->status(null)->one();
$j = \craft\elements\Entry::find()->section('persons')->slug('juventino-del-valle')->status(null)->one();
if (!$y || !$a) { echo 'missing ygnacio or antonio' . PHP_EOL; return; }
echo 'Ygnacio now: parents ' . implode(', ', array_map(fn($p) => $p->title, $y->getFieldValue('childOf')->all()) ?: ['none'])
   . ' | siblings ' . implode(', ', array_map(fn($p) => $p->title, $y->getFieldValue('siblingOf')->all()) ?: ['none']) . PHP_EOL;
echo 'would set: Ygnacio childOf Antonio, siblings cleared' . PHP_EOL;
if ($j) { echo 'NOTE: Juventino currently listed as parent. He was Ygnacio\'s son. Check before applying.' . PHP_EOL; }
if (!$APPLY) { echo 'DRY RUN' . PHP_EOL; return; }
$y->setFieldValue('childOf', [$a->id]);
$y->setFieldValue('siblingOf', []);
echo ($el->saveElement($y) ? 'saved' : 'FAILED') . PHP_EOL;
