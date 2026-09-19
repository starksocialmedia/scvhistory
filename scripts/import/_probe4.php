foreach (Craft::$app->volumes->getAllVolumes() as $v) {
    $n = \craft\elements\Asset::find()->volume($v->handle)->status(null)->count();
    echo str_pad($v->handle,20).str_pad((string)$n,8).$v->getFs()->getRootPath()."\n";
}
echo "\n-- asset field layout handles --\n";
$vs = Craft::$app->volumes->getAllVolumes();
foreach ($vs as $v) {
    $h = array_map(fn($f)=>$f->handle, $v->getFieldLayout()->getCustomFields());
    echo str_pad($v->handle,20).implode(', ',$h)."\n";
}
echo "\n-- sample assets --\n";
foreach (\craft\elements\Asset::find()->status(null)->limit(8)->all() as $a) {
    echo str_pad($a->filename,46).' vol='.$a->getVolume()->handle.' path='.$a->getPath()."\n";
}
