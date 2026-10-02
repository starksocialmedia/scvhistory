/**
 * The 16 war memorial records with no image, and Garry Wingfield's burial
 * (Nathan, 2 October 2026: "record that no likeness is known, using the
 * no-portrait wording. The tags being commented out on Leon's own pages means
 * he never had them either, which is a fact worth stating rather than a gap to
 * apologise for"; "The Wingfield correction goes in as a correcting note").
 *
 * LIKENESS. Each record gets an editor note, heading "Likeness". The evidence
 * is checked, not assumed: the legacy page (copied from the mirror to
 * inventory/legacy/fetched/warmemorial/) must carry the portrait tag inside
 * an HTML comment and no portrait outside one, and the record must have no
 * image. The file the tag names returned 404 on scvhistory.com on 2 October
 * 2026, and none is in the mirror; ABMC records carry no portraits.
 *
 * WINGFIELD. ABMC lists Pvt. Garry Wingfield, 19003636, as missing in action,
 * commemorated at the Manila American Cemetery: named on its Walls of the
 * Missing, not buried there, as the record's text and burialPlace say
 * (inventory/sources/abmc-war-memorial-2026-10-02.json). A "Correction, 2026"
 * note, the text not rewritten. burialPlace is a field, not prose, but it is
 * left as it is until the rest of his record is sourced in the pilot.
 *
 * Idempotent: a note already present is not added again. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/note_wm_no_likeness.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$DIR = "$root/inventory/legacy/fetched/warmemorial";
$plan = []; $bad = [];
foreach (Entry::find()->section('warMemorials')->status(null)->orderBy('title')->all() as $e) {
    if ($e->featuredImage->one() || $e->recordImages->count()) { continue; }
    $key = (string)$e->legacyKey;
    $f = "$DIR/$key.htm";
    if (!is_file($f)) { $bad[] = "#{$e->id} {$e->title}: no copy of $key.htm"; continue; }
    $html = file_get_contents($f);
    preg_match_all('~<!--(.*?)-->~s', $html, $c);
    $own = [];
    foreach ($c[1] as $block) { if (preg_match_all('~<img[^>]+src="([^"]+\.jpg)"~i', $block, $m)) { foreach ($m[1] as $src) { if (!str_contains($src, 'logo')) { $own[] = $src; } } } }
    $live = preg_replace('~<!--.*?-->~s', '', $html);
    $shown = preg_match_all('~<img[^>]+src="\.\./gif/(?!.*logo)[^"]*[^t]\.jpg"~i', $live);
    if (count($own) !== 1 || $shown) { $bad[] = "#{$e->id} {$e->title}: " . count($own) . " commented portrait tags, $shown shown"; continue; }
    $file = basename($own[0]);
    $note = 'No likeness of ' . $e->title . ' is known. Leon Worden\'s memorial page for him left a place for a portrait, but the image tag was commented out and the file it names, ' . $file . ', was never on SCVHistory.com: he did not have one either. Checked 2 October 2026 against the page in the mirror and the live site; the American Battle Monuments Commission, where it lists him, holds no portraits.';
    $plan[$e->id][] = ['Likeness', $note];
}
$W = Entry::find()->id(560)->status(null)->one();
$abmc = json_decode((string)@file_get_contents("$root/inventory/sources/abmc-war-memorial-2026-10-02.json"), true)['records']['560 Garry Wingfield'] ?? [];
if (($abmc['serviceNumber'] ?? '') !== '19003636' || ($abmc['missingStatus'] ?? '') !== 'Missing In Action' || ($abmc['cemeteryOrMemorial'] ?? '') !== 'Manila American Cemetery') { $bad[] = 'the ABMC reading for Wingfield is not as expected'; }
if (!$W || $W->title !== 'Garry Wingfield' || !str_contains(strip_tags((string)$W->body), 'He is buried in the Manila American Cemetery')) { $bad[] = '#560 is not Wingfield, or no longer says he is buried at Manila'; }
$plan[560][] = ['Correction, 2026', 'Garry Wingfield is not buried at the Manila American Cemetery. The American Battle Monuments Commission lists Pvt. Garry Wingfield, 19003636, 31st Infantry Regiment, as missing in action: he is named on the Walls of the Missing at Manila, which commemorate those whose remains were not recovered or identified (ABMC, Burial and Memorialization Directory, read 2 October 2026).'];
$todo = []; $have = 0;
foreach ($plan as $id => $notes) {
    $e = Entry::find()->id($id)->status(null)->one();
    $existing = array_map(fn($r) => trim((string)($r['note'] ?? '')), array_filter($e->editorNotes ?? [], 'is_array'));
    foreach ($notes as [$hd, $n]) { if (in_array($n, $existing, true)) { $have++; continue; } $todo[$id][] = [$hd, $n]; echo str_pad("#$id", 7) . str_pad(mb_substr($e->title, 0, 26), 28) . "$hd: " . mb_substr($n, 0, 90) . PHP_EOL; }
}
if (preg_match('~\x{2014}~u', json_encode($plan, JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in a note'; }
echo PHP_EOL . count(array_merge(...array_values($todo ?: [[]]))) . " notes to add on " . count($todo) . " records; $have already there" . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($todo as $id => $notes) {
    $e = Entry::find()->id($id)->status(null)->one();
    $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
    foreach ($notes as [$hd, $n]) { $rows[] = ['heading' => $hd, 'position' => 'bottom', 'note' => $n]; }
    $e->setFieldValue('editorNotes', $rows);
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id"; continue; }
    $back = array_column(Entry::find()->id($id)->status(null)->one()->editorNotes ?? [], 'note');
    foreach ($notes as [$hd, $n]) { if (!in_array($n, $back, true)) { $short[] = "#$id note"; } }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($todo) . ' records') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('note_wm_no_likeness.php', count($todo), $short ? 'SHORT' : 'verified', 'war memorial: no likeness known on 16 records (portrait tags commented out on the legacy pages); Wingfield missing in action, Walls of the Missing');
if ($short) { throw new \RuntimeException('note_wm_no_likeness: ' . implode(', ', $short)); }
