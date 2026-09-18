$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$find = function ($slug) {
    foreach (['persons', 'warMemorials'] as $s) {
        $e = \craft\elements\Entry::find()->section($s)->slug($slug)->status(null)->one();
        if ($e) { return $e; }
    }
    return null;
};
foreach (['rodolfo-acosta', 'dante-acosta', 'rudy-alexander-acosta', 'rudy-acosta'] as $s) {
    $e = $find($s);
    echo str_pad($s, 26) . ($e ? '#' . $e->id . '  ' . $e->section->handle . '  ' . $e->title : 'NOT FOUND') . PHP_EOL;
}
