/**
 * War memorial lead images (Nathan, 1 October 2026: "Many war dead have
 * portraits that are not showing on /war-memorial ... Each should carry the best
 * image available: a headshot, a photograph with family, or the grave marker.
 * Some currently show maps or letters, which should not be the lead image").
 *
 * The index shows featuredImage, and only 1 of 54 records had one; 37 had their
 * pictures only in recordImages. This picks the lead from recordImages by kind,
 * read from the filename and title, best first:
 *   3  a portrait: his surname in the file name, or a "mug" shot
 *   2  a photograph with family (two names, "and")
 *   1  the grave marker, headstone, or the name on a memorial wall
 *   0  never the lead: maps, draft cards, certificates, records, enlistment and
 *      interment papers, newspaper pages, triptychs, examples, thumbnails, and
 *      any photograph not identifiably his (a ship, a scene); and
 *      interment papers, newspaper pages, triptychs, examples, thumbnails
 * Ties go to the larger image. A record with nothing above 0 is left without a
 * lead and listed. Fills an empty featuredImage only; the images stay in
 * recordImages. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_wm_lead_images.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$NEVER = '~(map|draft|certificate|cert\b|deathcert|_record|enlistment|interment|roster|birth|tryptich|triptych|example|clipping|newspaper|_t\.jpg|t\.jpg$|^sg\d|^lat\d|^tlp_(?!.*mug)|bakersfield|miamidaily|colmar|anzio|argonne|cemetery\d|memorialgarden|foundation|times ?photo|dug)~i';
$GRAVE = '~(grave|headstone|marker|wall_|cenotaph|traveling vietnam memorial)~i';
$FAMILY = '~(_and_|[a-z]+_[a-z]+ward|christilarsen|dorothy|family| and )~i';
$score = function ($a, $e) use ($NEVER, $GRAVE, $FAMILY) {
    $s = strtolower($a->filename . ' ' . $a->title);
    /* A portrait has to be his: his surname in the file name, or a "mug" shot. A ship's launch is not a portrait. */
    $parts = preg_split('~[\s.,]+~', preg_replace('~\b(Jr|Sr|II|III)\b\.?|["\'(].*?["\')]~u', '', $e->title));
    $parts = array_values(array_filter($parts, fn($x) => trim($x) !== ''));
    $sur = strtolower(preg_replace('~[^a-z]~i', '', (string)end($parts)));
    if ($a->width && $a->width < 200) { return [0, 'thumbnail']; }
    if (preg_match($GRAVE, $s)) { return [1, 'grave or memorial']; }
    if (preg_match($NEVER, $a->filename) || preg_match('~(map|draft|certificate|record|newspaper)~i', (string)$a->title)) { return [0, 'document, map or clipping']; }
    if (preg_match($FAMILY, $s)) { return [2, 'with family']; }
    if (($sur !== '' && str_contains(strtolower($a->filename), $sur)) || str_contains($s, 'mug')) { return [3, 'portrait']; }
    return [0, 'not identifiably him'];
};
$plan = []; $none = []; $kept = 0;
foreach (Entry::find()->section('warMemorials')->status(null)->orderBy('title')->all() as $e) {
    if ($e->featuredImage->one()) { $kept++; continue; }
    $best = null;
    foreach ($e->recordImages->all() as $a) { [$sc, $why] = $score($a, $e); if ($sc > 0 && (!$best || $sc > $best[1] || ($sc === $best[1] && $a->width * $a->height > $best[0]->width * $best[0]->height))) { $best = [$a, $sc, $why]; } }
    if ($best) { $plan[$e->id] = $best; echo '   #' . str_pad($e->id, 5) . str_pad(mb_substr($e->title, 0, 34), 36) . "{$best[2]}: #{$best[0]->id} {$best[0]->filename}" . PHP_EOL; }
    else { $none[] = "#{$e->id} {$e->title}" . ($e->recordImages->count() ? ' (only documents, maps or clippings)' : ' (no images)'); }
}
echo PHP_EOL . count($plan) . ' leads to set; ' . $kept . ' already had one; ' . count($none) . ' without a suitable image:' . PHP_EOL . '   ' . implode(PHP_EOL . '   ', $none) . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => [$a, $sc, $why]) {
    $e = Entry::find()->id($id)->status(null)->one(); $e->setFieldValue('featuredImage', [$a->id]);
    if (!Craft::$app->getElements()->saveElement($e) || Entry::find()->id($id)->status(null)->one()->featuredImage->one()?->id !== $a->id) { $short[] = "#$id"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' leads set') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('set_wm_lead_images.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial lead images: portrait, family, then grave; never maps or papers');
if ($short) { throw new \RuntimeException('set_wm_lead_images: ' . implode(', ', $short)); }
