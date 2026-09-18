$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$a = \craft\elements\Entry::find()->section('articles')->slug('editors-notes')->status(null)->one();
$leon = \craft\elements\Entry::find()->section('persons')->slug('leon-worden')->status(null)->one();
$perk = \craft\elements\Entry::find()->section('persons')->slug('arthur-b-perkins')->status(null)->one();
if (!$a || !$leon || !$perk) { echo 'missing: ' . ($a?'':'article ') . ($leon?'':'leon ') . ($perk?'':'perkins') . PHP_EOL; return; }
echo 'writtenBy now: ' . implode(', ', array_map(fn($p) => $p->title, $a->getFieldValue('writtenBy')->all()) ?: ['none']) . PHP_EOL;
echo 'would set: writtenBy Leon Worden, subjectPerson Arthur B. Perkins' . PHP_EOL;
if (!$APPLY) { echo 'DRY RUN' . PHP_EOL; return; }
$a->setFieldValue('writtenBy', [$leon->id]);
$a->setFieldValue('subjectPerson', [$perk->id]);
echo ($el->saveElement($a) ? 'saved' : 'FAILED') . PHP_EOL;
