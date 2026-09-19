$fields = Craft::$app->fields->getAllFields();
$hits = [];
foreach ($fields as $f) {
    if (preg_match('~legacy|source|provenance|clean|review~i', $f->handle)) {
        $hits[] = $f->handle . '  [' . get_class($f) . ']';
    }
}
sort($hits);
echo "-- provenance-ish fields --\n" . implode("\n", $hits) . "\n\n";
echo "-- sections --\n";
foreach (Craft::$app->entries->getAllSections() as $s) {
    $n = \craft\elements\Entry::find()->section($s->handle)->status(null)->count();
    echo str_pad($s->handle, 22) . $n . "\n";
}
echo "\n-- entry types --\n";
foreach (Craft::$app->entries->getAllEntryTypes() as $t) {
    echo str_pad($t->handle, 24) . $t->id . "\n";
}
