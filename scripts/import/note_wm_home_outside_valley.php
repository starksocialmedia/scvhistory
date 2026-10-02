/**
 * Memorial men whose home was outside the valley, or is not known, said
 * plainly (Nathan, 2 October 2026: "Prosser stays. Removing a man from a war
 * memorial is not a records decision, and the memorial is Leon's as much as it
 * is a dataset. Record it plainly ... If there is a local connection beyond
 * that, find it; if not, the note says so. Check whether others on the memorial
 * are outside the valley on the same basis").
 *
 * The same basis as Prosser: the legacy page itself places him outside the
 * valley, or gives no home at all. Four records:
 *   #546 Prosser   Frazier Park; the federal file, Panorama Heights (Kern County)
 *   #518 Rubel     Rancho Camulos, near Piru (Ventura County); Camulos was set
 *                  off from the Rancho San Francisco in 1870 (Perkins, 1957),
 *                  the land grant that took in the Santa Clarita Valley
 *   #542 Todd      no home given
 *   #562 Harland   no home given
 * Not on this basis, because the legacy page places them in the valley and a
 * federal record places them elsewhere, a difference already shown in their
 * facts: Ward (San Diego County), Beall (Lincoln County, Oklahoma), Cherry
 * (parents at Inglewood), Fose (wife at Los Angeles).
 * The texts were searched for any valley place name; none was found in these
 * four. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/note_wm_home_outside_valley.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$H = 'Home, 2026';
$N = [
    546 => ['Brian Cody Prosser', 'Frazier Park', 'Brian Cody Prosser\'s home of record was Panorama Heights, near Frazier Park in Kern County (note 1), and the legacy page gives Frazier Park: both are outside the Santa Clarita Valley. He is on this memorial because the archive\'s memorial has always carried him. No other connection to the valley has been found in the archive.'],
    518 => ['Augustus A. (August) Rubel', 'Rancho Camulos (Piru)', 'August Rubel\'s home was Rancho Camulos, near Piru in Ventura County, outside the Santa Clarita Valley. Camulos was set off from the Rancho San Francisco when the grant was partitioned in 1870 (A.B. Perkins, "Rancho San Francisco," 1957, in this archive); that grant also took in the Santa Clarita Valley. He is on this memorial because the archive\'s memorial has always carried him.'],
    542 => ['Dean Glenn Todd Jr', '', 'No home is given for Dean Glenn Todd Jr. on the legacy page, and no record read so far gives one. No connection to the Santa Clarita Valley has been found in the archive. He is on this memorial because the archive\'s memorial has always carried him.'],
    562 => ['Jack Lewis Harland', '', 'No home is given for Jack Lewis Harland on the legacy page, and the War Department\'s Honor List places him only in Los Angeles County (note 1). No connection to the Santa Clarita Valley has been found in the archive. He is on this memorial because the archive\'s memorial has always carried him.'],
];
$plan = []; $bad = [];
$perkins = Entry::find()->id(4461)->status(null)->one();
if (!$perkins || !str_contains(implode(' ', array_column($perkins->editorNotes ?? [], 'note')), 'Ygnacio\'s share was set off as Camulos')) { $bad[] = 'the Camulos partition is not where it was read (#4461\'s correction note citing Perkins)'; }
foreach ($N as $id => [$title, $home, $note]) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || $e->title !== $title) { $bad[] = "#$id is not $title"; continue; }
    if (trim((string)$e->wmHomeOfRecord) !== $home) { $bad[] = "#$id home is \"{$e->wmHomeOfRecord}\", not \"$home\""; }
    if (in_array($note, array_column($e->editorNotes ?? [], 'note'), true)) { echo "#$id already noted\n"; continue; }
    $plan[$id] = $note; echo "#$id $title: " . mb_substr($note, 0, 120) . PHP_EOL;
}
if (preg_match('~\x{2014}~u', implode('', array_column($N, 2)))) { $bad[] = 'an em dash'; }
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
$applyLog('note_wm_home_outside_valley.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial: homes outside the valley or unknown, said plainly (Prosser, Rubel, Todd, Harland)');
if ($short) { throw new \RuntimeException('note_wm_home_outside_valley: ' . implode(', ', $short)); }
