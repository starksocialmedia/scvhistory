$el = Craft::$app->getElements();
$a = \craft\elements\Entry::find()->section('articles')->slug('introduction')->status(null)->one();
$leon = \craft\elements\Entry::find()->section('persons')->slug('leon-worden')->status(null)->one();
$perk = \craft\elements\Entry::find()->section('persons')->slug('arthur-b-perkins')->status(null)->one();
if (!$a || !$leon || !$perk) { echo 'missing: ' . ($a?'':'article ') . ($leon?'':'leon ') . ($perk?'':'perkins') . PHP_EOL; return; }
$a->setFieldValue('writtenBy', [$leon->id]);
$a->setFieldValue('subjectPerson', [$perk->id]);
echo ($el->saveElement($a) ? 'Introduction: written by Leon Worden, about Arthur B. Perkins' : 'FAILED') . PHP_EOL;
