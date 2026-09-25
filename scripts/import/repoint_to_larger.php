/**
 * Repoints inline image references at a larger copy found by perceptual hash.
 *
 * Input is review/larger-by-hash.json from find_larger_by_hash.php. Only
 * pairs at "certain" are eligible by default; likely and possible are printed
 * and skipped, because a 120x90 thumbnail carries so little information that a
 * difference hash on one is barely better than a guess at distance 9 or more.
 *
 * WHAT IT CHANGES
 *
 * The article's recordImages relation: the small asset is swapped for the large
 * one in place, so [image:N] keeps its position and the caption stays with it.
 * The small asset is not deleted and not altered. If the swap is wrong it is
 * undone by swapping back, which is only true because nothing is destroyed.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/repoint_to_larger.php'))"
 */

$APPLY = false;
$MIN_CONFIDENCE = $REPOINT_CONFIDENCE ?? 'certain';   /* certain | likely | possible */

$RANK = ['certain' => 3, 'likely' => 2, 'possible' => 1];
$floor = $RANK[$MIN_CONFIDENCE] ?? 3;

$REPORT = \Craft::getAlias('@review') . '/larger-by-hash.json';
if (!file_exists($REPORT)) { echo 'no larger-by-hash.json. Run find_larger_by_hash.php first.' . PHP_EOL; return; }
$doc = json_decode(file_get_contents($REPORT), true) ?: [];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . '   accepting: ' . $MIN_CONFIDENCE . ' and better' . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$plan = []; $skipped = [];

foreach (($doc['best'] ?? []) as $p) {
    if (($RANK[$p['confidence']] ?? 0) < $floor) { $skipped[] = $p; continue; }

    $small = \craft\elements\Asset::find()->id((int)$p['needleId'])->status(null)->one();
    if (!$small) { $skipped[] = $p + ['why' => 'the small asset is gone']; continue; }

    /* The larger file has to be an asset Craft knows about, not just a file on
       disk: the relation points at an element. */
    $large = \craft\elements\Asset::find()->filename(basename($p['matchPath']))->status(null)->all();
    $large = array_values(array_filter($large, fn($a) => $a->width >= $small->width * 1.5));
    if (!$large) { $skipped[] = $p + ['why' => 'the larger file is not an indexed asset']; continue; }
    $large = $large[0];

    $users = [];
    foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->all() as $e) {
        foreach ($e->recordImages->all() as $a) {
            if ($a->id === $small->id) { $users[] = $e; break; }
        }
    }
    if (!$users) { $skipped[] = $p + ['why' => 'nothing references it inline any more']; continue; }

    $plan[] = ['small' => $small, 'large' => $large, 'pair' => $p, 'users' => $users];
}

foreach ($plan as $x) {
    printf("%-28s #%-6d %sx%s\n", $x['small']->filename, $x['small']->id, $x['small']->width, $x['small']->height);
    printf("   -> %-25s #%-6d %sx%s   distance %d, %s\n",
        $x['large']->filename, $x['large']->id, $x['large']->width, $x['large']->height,
        $x['pair']['distance'], $x['pair']['confidence']);
    foreach ($x['users'] as $e) { printf("      in %s\n", $e->slug); }
}

echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
echo 'would repoint: ' . count($plan) . PHP_EOL;
echo 'skipped: ' . count($skipped) . PHP_EOL;
foreach ($skipped as $s) {
    printf("   %-28s %-10s %s\n", $s['needle'], $s['confidence'], $s['why'] ?? 'below the confidence floor');
}

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$ok = 0; $touched = 0;
foreach ($plan as $x) {
    foreach ($x['users'] as $e) {
        $ids = [];
        foreach ($e->recordImages->all() as $a) { $ids[] = $a->id === $x['small']->id ? $x['large']->id : $a->id; }
        $e->setFieldValue('recordImages', $ids);
        if (\Craft::$app->elements->saveElement($e)) { $touched++; }
    }
    $ok++;
}

$verified = 0;
foreach ($plan as $x) {
    foreach ($x['users'] as $e) {
        $c = \craft\elements\Entry::find()->id($e->id)->status(null)->one();
        $has = false;
        foreach ($c->recordImages->all() as $a) { if ($a->id === $x['large']->id) { $has = true; } }
        if ($has) { $verified++; }
    }
}
echo PHP_EOL . 'repointed: ' . $ok . ' assets across ' . $touched . ' articles, verified ' . $verified . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('repoint_to_larger.php', $ok, 'verified ' . $verified . ' of ' . $touched, $MIN_CONFIDENCE . ' and better');
