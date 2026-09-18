/**
 * Finds collections with the same title, keeps the one with articles,
 * copies the legacy URL across, and deletes the empty duplicate.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/merge_duplicate_collections.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$elements = Craft::$app->getElements();
$cols = \craft\elements\Entry::find()->section('collections')->status(null)->all();

$byTitle = [];
foreach ($cols as $c) {
    $key = strtolower(trim($c->title ?: $c->slug));
    $byTitle[$key][] = $c;
}

$counts = function ($c) {
    $n = 0;
    try { $n = $c->getFieldValue('articlesInCollection')->count(); } catch (\Throwable $e) { $n = 0; }
    if (!$n) {
        $n = \craft\elements\Entry::find()->section('articles')
            ->relatedTo(['targetElement' => $c, 'field' => 'partOfCollection'])->status(null)->count();
    }
    return $n;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo '--- all collections ---' . PHP_EOL;
foreach ($cols as $c) {
    echo str_pad($c->slug, 40) . str_pad((string)($c->title ?: '(no title)'), 40) . $counts($c) . ' articles' . PHP_EOL;
}

echo '--- duplicates ---' . PHP_EOL;
$deleted = 0;
foreach ($byTitle as $title => $group) {
    if (count($group) < 2) { continue; }
    usort($group, fn($a, $b) => $counts($b) <=> $counts($a));
    $keep = array_shift($group);
    echo 'KEEP   ' . str_pad($keep->slug, 40) . $counts($keep) . ' articles' . PHP_EOL;
    foreach ($group as $dupe) {
        $n = $counts($dupe);
        echo '  DROP ' . str_pad($dupe->slug, 40) . $n . ' articles' . ($n ? '  << has articles, NOT deleting' : '') . PHP_EOL;
        if ($n) { continue; }
        if ($APPLY) {
            $legacy = (string)$dupe->getFieldValue('legacyUrl');
            if ($legacy !== '' && (string)$keep->getFieldValue('legacyUrl') === '') {
                $keep->setFieldValue('legacyUrl', $legacy);
                $elements->saveElement($keep);
            }
            $elements->deleteElement($dupe);
        }
        $deleted++;
    }
}
echo '--- summary ---' . PHP_EOL;
echo 'duplicates removed: ' . $deleted . PHP_EOL;
echo 'collections remaining: ' . (count($cols) - ($APPLY ? $deleted : 0)) . PHP_EOL;
