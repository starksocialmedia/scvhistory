/**
 * The five World War II records the sourcing has not yet sourced, each with a
 * note saying what was searched and not found (Nathan, 2 October 2026: "note
 * on each record now what has been searched and not found, so the gap is
 * visible rather than silent"). Korea, Vietnam and the War on Terror come
 * first; these five are returned to after.
 * Idempotent: a note already present is not added again. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/note_wm_searched_not_found.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$H = 'Sources, 2026';
$N = [
    1395 => ['James Robert "Jimmie" Ball', 'Not yet sourced. Searched 2 October 2026 and not found: the Navy\'s 1946 State Summary of War Casualties for California (National Archives NAID 305189), in both the dead and the missing sections, which list men by their next of kin\'s state. ABMC\'s Burial and Memorialization Directory, which should list him on the Courts of the Missing at Honolulu, did not open his record. Next to try: the Navy lists for other states, and ABMC again.'],
    580 => ['Thomas Milton Ross Jr.', 'Not yet sourced. Searched 2 October 2026 and not found: the Navy\'s 1946 State Summary of War Casualties for California (National Archives NAID 305189), dead and missing, which lists men by their next of kin\'s state. Next to try: the Navy lists for other states, and ABMC\'s Tablets of the Missing.'],
    514 => ['Lawrence E. Kenaston', 'Not yet sourced. Searched 2 October 2026 and not found: the Navy\'s 1946 State Summary of War Casualties for California (National Archives NAID 305189), which covers the Marine Corps, dead and missing, by next of kin\'s state. Next to try: the lists for other states, the Marine Corps muster rolls, and ABMC.'],
    576 => ['Robert Russell Cone', 'Not yet sourced. Searched 2 October 2026 and not found: the War Department\'s 1946 Honor List for California (National Archives NAID 305280), Los Angeles County, where he would be listed if he enlisted as a county resident. Next to try: other counties and states in the Honor List, NARA\'s enlistment records once his service number is known, and ABMC.'],
    518 => ['Augustus A. (August) Rubel', 'Not yet sourced. He served in the American Field Service, a civilian volunteer ambulance corps, so he is in none of the military casualty lists read so far (the War Department\'s Honor List and the Navy\'s State Summary). Next to try: the American Field Service\'s own roll of its dead and ABMC, which commemorates some civilians.'],
];
$plan = []; $bad = [];
foreach ($N as $id => [$title, $note]) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || $e->title !== $title) { $bad[] = "#$id is not $title"; continue; }
    if (in_array($note, array_column($e->editorNotes ?? [], 'note'), true)) { echo "#$id already noted" . PHP_EOL; continue; }
    $plan[$id] = $note; echo "#$id $title: " . mb_substr($note, 0, 110) . PHP_EOL;
}
if (preg_match('~\x{2014}~u', implode('', array_column($N, 1)))) { $bad[] = 'an em dash'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => $note) {
    $e = Entry::find()->id($id)->status(null)->one();
    $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
    $rows[] = ['heading' => $H, 'position' => 'bottom', 'note' => $note];
    $e->setFieldValue('editorNotes', $rows);
    if (!Craft::$app->getElements()->saveElement($e) || !in_array($note, array_column(Entry::find()->id($id)->status(null)->one()->editorNotes ?? [], 'note'), true)) { $short[] = "#$id"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('note_wm_searched_not_found.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial: what was searched and not found, on the five unsourced World War II records');
if ($short) { throw new \RuntimeException('note_wm_searched_not_found: ' . implode(', ', $short)); }
