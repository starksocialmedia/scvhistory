/**
 * Leon's filing, out of the photograph bodies and into legacyCategory.
 *
 * Every legacy photograph page opened with the site's breadcrumb, the index
 * pages Leon filed it under: "> MOJAVE DESERT", "> RUBEL FAMILY PHOTOS > RANCHO
 * CAMULOS". The import kept that line as the first line of the body, so the
 * filing survives, but only as text. The /photographs index groups by it
 * (Nathan, 28 September 2026: lead with the topics), and parsing it out of 1,546
 * bodies on every page load is fragile, so it is copied once into
 * legacyCategory, which is on the photograph layout and empty on every record.
 *
 * VERBATIM. legacyCategory holds the breadcrumb exactly as printed, segments in
 * order, separated by " > ", capitals and all. It is Leon's filing, a mix of
 * places and subjects, and it is not tidied into a taxonomy. The body is not
 * touched: the breadcrumb stays where it was.
 *
 * A body with no breadcrumb gets nothing. An existing legacyCategory is never
 * overwritten.
 *
 * The dry run also prints the topic distribution, one topic per segment, so a
 * photograph filed under two topics counts in both.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_photo_legacy_category.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$elements = Craft::$app->getElements();
/* The breadcrumb: the opening line(s) made of "> SEGMENT" runs, before the first
   blank line or [lines] block. */
$read = function (string $body): string {
    $b = ltrim($body);
    if (!preg_match('~^((?:>\s*[^>\n]+?\s*)+)(?:\n|\[lines\]|$)~u', $b, $m)) { return ''; }
    $segs = array_values(array_filter(array_map('trim', preg_split('~\s*>\s*~u', trim($m[1], "> \t")))));
    return implode(' > ', $segs);
};

$plan = []; $none = 0; $kept = 0; $total = 0; $topics = [];
foreach (\craft\elements\Entry::find()->section('photographs')->status(null)->each() as $p) {
    $total++;
    $crumb = $read((string)$p->body);
    if ($crumb === '') { $none++; continue; }
    foreach (explode(' > ', $crumb) as $t) { $topics[$t] = ($topics[$t] ?? 0) + 1; }
    if (trim((string)$p->legacyCategory) !== '') { $kept++; continue; }
    $plan[$p->id] = $crumb;
}

arsort($topics);
$bands = ['50 or more' => 0, '20 to 49' => 0, '10 to 19' => 0, '3 to 9' => 0, '1 or 2' => 0];
$inBand = $bands;
foreach ($topics as $n) {
    $k = $n >= 50 ? '50 or more' : ($n >= 20 ? '20 to 49' : ($n >= 10 ? '10 to 19' : ($n >= 3 ? '3 to 9' : '1 or 2')));
    $bands[$k]++; $inBand[$k] += $n;
}
echo "photographs $total; with a breadcrumb " . ($total - $none) . "; without $none; legacyCategory already set $kept" . PHP_EOL;
echo 'to write: ' . count($plan) . PHP_EOL . PHP_EOL;
echo 'topics: ' . count($topics) . ' (a photograph counts once per topic it is filed under)' . PHP_EOL;
foreach ($bands as $k => $n) { echo '   ' . str_pad($k, 12) . str_pad($n . ' topics', 12) . $inBand[$k] . ' filings' . PHP_EOL; }
echo PHP_EOL . 'largest: ' . implode(', ', array_map(fn($t, $n) => "$t $n", array_slice(array_keys($topics), 0, 16), array_slice($topics, 0, 16))) . PHP_EOL;
echo PHP_EOL . 'sample:' . PHP_EOL;
foreach (array_slice($plan, 0, 6, true) as $id => $c) { echo '   #' . $id . '  ' . $c . PHP_EOL; }

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$short = [];
foreach ($plan as $id => $crumb) {
    $p = \craft\elements\Entry::find()->id($id)->status(null)->one();
    $p->setFieldValue('legacyCategory', $crumb);
    if (!$elements->saveElement($p)) { $short[] = "#$id save failed " . json_encode($p->getFirstErrors()); continue; }
    $got = trim((string)\craft\elements\Entry::find()->id($id)->status(null)->one()->legacyCategory);
    if ($got !== $crumb) { $short[] = "#$id legacyCategory reads \"$got\", expected \"$crumb\""; }
}
echo 'READ-BACK ' . ($short ? 'SHORT' : 'OK') . ': ' . (count($plan) - count($short)) . ' of ' . count($plan) . PHP_EOL;
foreach (array_slice($short, 0, 20) as $s) { echo '   FAIL ' . $s . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('set_photo_legacy_category.php', count($plan), $short ? 'SHORT: ' . count($short) . ' failed, first: ' . $short[0] : 'verified ' . count($plan) . ' of ' . count($plan),
    'breadcrumb copied verbatim into legacyCategory; bodies untouched; ' . count($topics) . ' topics');
if ($short) { throw new \RuntimeException('set_photo_legacy_category: ' . count($short) . ' read-back failures, first: ' . $short[0]); }
