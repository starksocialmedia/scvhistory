/**
 * Writes the confirmed place relationships from the review screen.
 *
 * Reads review/place-links-decided.json, the download from
 * /review/place-links.html. Only "yes" is written; "no" is recorded in the file
 * so the same pair is not proposed again, and is otherwise ignored here.
 *
 * relatedPlaces is symmetric in meaning but stored one way round in Craft, so
 * each confirmed pair is written on both records. A relation already present is
 * left alone, which makes a second run a no-op.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_place_links.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$path = \Craft::getAlias('@review') . '/place-links-decided.json';
if (!file_exists($path)) {
    echo 'not found: ' . $path . PHP_EOL;
    echo 'Decide at /review/place-links.html and press Download first.' . PHP_EOL;
    return;
}
$decisions = json_decode(file_get_contents($path), true);
if (!is_array($decisions)) { echo 'could not parse the decisions file' . PHP_EOL; return; }

$elements = Craft::$app->getElements();
$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'decisions in the file: ' . count($decisions) . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

/* Gather every confirmed link per record first, so a place appearing in three
   pairs is saved once rather than three times. */
$add = [];
$yes = 0; $no = 0; $bad = [];
foreach ($decisions as $d) {
    $choice = (string)($d['choice'] ?? '');
    if ($choice === 'no') { $no++; continue; }
    if ($choice !== 'yes') { $bad[] = json_encode($d); continue; }
    $a = (int)($d['a'] ?? 0); $b = (int)($d['b'] ?? 0);
    if (!$a || !$b) { $bad[] = json_encode($d); continue; }
    $yes++;
    $add[$a][$b] = true;
    $add[$b][$a] = true;
}

echo 'confirmed: ' . $yes . ', rejected: ' . $no . PHP_EOL . PHP_EOL;

$changed = 0; $links = 0;
foreach ($add as $id => $targets) {
    $e = \craft\elements\Entry::find()->id($id)->section('places')->status(null)->one();
    if (!$e) { echo '#' . $id . ' is not a place record' . PHP_EOL; continue; }
    if (!$hasField($e, 'relatedPlaces')) { echo $e->title . ': no relatedPlaces field' . PHP_EOL; continue; }

    $current = $e->relatedPlaces->ids();
    $new = array_values(array_diff(array_keys($targets), $current));
    if (!$new) { echo str_pad($e->title, 36) . 'already carries all of them' . PHP_EOL; continue; }

    $names = [];
    foreach ($new as $t) {
        $to = \craft\elements\Entry::find()->id($t)->status(null)->one();
        $names[] = $to ? $to->title : ('#' . $t);
    }
    echo str_pad($e->title, 36) . '+ ' . implode(', ', $names) . PHP_EOL;
    $changed++; $links += count($new);

    if ($APPLY) {
        $e->setFieldValue('relatedPlaces', array_merge($current, $new));
        echo str_pad('', 36) . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
    }
}

if ($bad) {
    echo PHP_EOL . 'rows that made no sense, ignored:' . PHP_EOL;
    foreach ($bad as $b) { echo '  ' . $b . PHP_EOL; }
}

echo PHP_EOL . str_repeat('=', 72) . PHP_EOL;
echo 'records that would change: ' . $changed . PHP_EOL;
echo 'relations that would be added: ' . $links . ' (each pair counts twice, once on each record)' . PHP_EOL;
if (!$APPLY) { echo 'nothing written.' . PHP_EOL; }
else { echo 'Re-render /graph to see them.' . PHP_EOL; }
