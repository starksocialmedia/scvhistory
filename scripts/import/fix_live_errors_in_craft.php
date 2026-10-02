/**
 * The 18 live errors that are in Craft records now (Nathan, 2 October 2026;
 * inventory/review/live-errors.md, built from Grok's Hart, Beale, del Valle and
 * Reynolds dossiers).
 *
 * The published text is not rewritten: AGENTS.md, "never rewrite Leon Worden's
 * prose during migration," and the same holds for Reynolds and for the wire
 * cutlines. Each error in a text gets a Correction editor note on the record,
 * dated and sourced, as on Dante Acosta's record. Errors in data, not prose,
 * are fixed in the field:
 *   L7  #5333's photoDate held "LA LIST EHT 7/8/36", the AP slug misread as a
 *       date; it becomes 8 July 1936, with a note that the paper ran it on
 *       Sunday 12 July, not the 11th the cutline says.
 *   L2  is not an error in Craft: "Letter: Gift of Buffalo Coat to Rudy Vallee
 *       1936" survives only as label text from the legacy sidebar, with no
 *       link; the wrong link was on the legacy page. Nothing is changed.
 * Each note's source is the record or page named in it; phrases are checked in
 * the records they come from where the archive holds them.
 * Idempotent: a note already present is not added again. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_live_errors_in_craft.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$CAMULOS = [3743, 3745, 3747, 3749, 3751, 3753, 3755, 3757, 3759, 3761, 3763, 4301, 4303, 4305, 4307, 4309, 4311, 4313, 4315, 4317, 4319, 4321, 4323, 4325, 4327, 4329, 4331, 4333, 4335, 4337, 4389, 4391, 5675];
$NOTES = [
    'L1' => [[2759], 'operates the Hart Museum', 'The Natural History Museum no longer operates the Hart Museum. Since 14 July 2025 the City of Santa Clarita has owned and operated William S. Hart Park and the Hart Museum (City of Santa Clarita, "William S. Hart Park Officially Opens as City\'s 40th Park," 14 July 2025).'],
    'L12' => [[2131], 'seventeen-year-old', 'Winifred Westover was about 22 when she married Hart in December 1921, not seventeen: the Associated Press gave her age as 51 in February 1950 (SCVHistory.com LW2291), and LW3335 gives 22.'],
    'L13' => [[2131], 'battled ones that remained', 'The 1939 sound prologue to Tumbleweeds reads "I love the art," "yawning canyon" and "baffled ones that remain" in the transcript of the soundtrack (SCVHistory.com, tumbleweedsmonologue); the quotation here differs from it.'],
    'B6' => [[2941, 2943, 2945, 2947, 2949, 5285, 5491], 'between Ft. Defiance, in Texas, and the Colorado River', 'Fort Defiance was in New Mexico Territory, in what is now Arizona, not in Texas: Beale\'s wagon road of 1857 ran "from Fort Defiance, New Mexico Territory, to the Colorado River" (SCVHistory.com LW3491).'],
    'B7' => [[12290, 12370], 'Big Bill Hart took a crack at the famous director in his autobiography', 'Hart\'s autobiography, My Life East and West, was published in 1929, ten years before Stagecoach (1939), so the criticism of Ford cannot come from it. The column credits the anecdote to Edward Buscombe\'s book; where Hart said it has not been found.'],
    'D3' => [[293], 'When statehood came in 1850, Ygnacio served a short stint in the first Legislature', 'Ygnacio del Valle was elected to the Assembly from the 2nd District on 3 September 1851 and sat in the 1852 session, not in the first Legislature of 1849 and 1850 (JoinCalifornia, "Ignacio Del Valle"; the Assembly Journal of 1852 has not been checked).'],
    'D4' => [[4461], 'Upon Antonio\'s death two years later, the ranch was divided among his widow and children', 'The rancho was not divided at Antonio del Valle\'s death in 1841. His heirs held it in undivided shares; it was partitioned only in 1870, when Ygnacio\'s share was set off as Camulos (A.B. Perkins, "Rancho San Francisco," 1957; archive record "Rancho San Francisco: A Study of a California Land Grant, by A.B. Perkins (1957)").'],
    'D5' => [$CAMULOS, 'worked a deal to keep the 1,500-acre Camulos section', 'Camulos was about 1,340 acres when the rancho was partitioned in 1870, not 1,500 (A.B. Perkins, "Rancho San Francisco," 1957). And the del Valles\' ownership dates from the grant of 22 January 1839, not from the end of Mexico\'s war of independence in 1821 (Perkins; Jerry Reynolds, part 14).'],
    'D7' => [[2083], 'Management of the estate passed to his eldest surviving son, Reginaldo, who was then a state senator', 'In March 1880 Reginaldo del Valle was an assemblyman; he was nominated for the State Senate in 1882 (Pen Pictures from the Garden of the World, 1889, at SCVHistory.com). Nor was he the eldest surviving son: his brother Juventino, born in 1841, was older (SCVHistory.com LW2752).'],
    'R2' => [[2171], 'Hist. Society of Southern Calif., 1902.', 'Three entries in this bibliography need correcting. "Burrows, D.H." is H.D. Barrows, the Historical Society of Southern California\'s writer on the del Valles, whom A.B. Perkins cites (the year has not been checked against the Society\'s index). Williamson\'s Pacific Railroad Survey was published in the 1850s (Pacific Railroad Reports, volume 5, 1856), not in 1952. "Van Valkenberg, R." is Richard Van Valkenburgh, as the editor\'s note in part 4 spells it.'],
    'R5' => [[2117], 'rolled up to the Saugus Station', 'President Benjamin Harrison did not stop at Saugus: his train passed on 24 April 1891, as reported on the 25th, and the Santa Barbara delegation met him at Ventura. And the Saugus Eating House was started by James Herbert Tolfree, who died in 1897; Joseph H. Tolfree may have run it later (SCVHistory.com, the Saugus Cafe page, editor\'s notes 2, 4 and 5).'],
    'R9' => [[12168], 'Dec. 16, 1903', 'Reynolds wrote that Hi Jolly died on 16 December 1902, aged 74 (History of the Santa Clarita Valley, part 23), not in 1903 at 75 as quoted here.'],
];
/* Phrases from the archive's own records that the notes rely on. */
$EVIDENCE = [['lw3491', 'Fort Defiance, New Mexico Territory']];
$bad = []; $plan = [];
foreach ($EVIDENCE as [$key, $ph]) { $r = Entry::find()->status(null)->legacyKey($key)->one(); if (!$r || !str_contains($ws(strip_tags((string)$r->body)), $ph)) { $bad[] = "$key does not read \"$ph\""; } }
foreach ($NOTES as $code => [$ids, $phrase, $note]) {
    foreach ($ids as $id) {
        $e = Entry::find()->id($id)->status(null)->one();
        if (!$e) { $bad[] = "$code #$id missing"; continue; }
        $text = $ws(strip_tags((string)$e->body . ' ' . ($e->getFieldLayout()->getFieldByHandle('authorBio') ? $e->authorBio : '')));
        if (!str_contains($text, $ws($phrase))) { $bad[] = "$code #$id does not carry \"" . mb_substr($phrase, 0, 50) . '"'; continue; }
        $have = array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) === $note);
        if (!$have) { $plan[$id][] = $note; }
    }
    echo str_pad($code, 4) . count($ids) . ' record' . (count($ids) === 1 ? '' : 's') . PHP_EOL;
}
$p5333 = Entry::find()->id(5333)->status(null)->one();
$fix5333 = (string)$p5333->photoDate !== 'July 8, 1936';
$NOTE_5333 = 'The Associated Press sent this photograph on 8 July 1936 for use with a story on Sunday "July 11"; the Sunday was 12 July 1936, the day the paper ran it.';
if ($fix5333 || !array_filter($p5333->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) === $NOTE_5333)) { $plan[5333][] = $NOTE_5333; }
echo "L7  #5333 photoDate \"{$p5333->photoDate}\" -> \"July 8, 1936\" (1936-07-08), with a note" . PHP_EOL . 'L2  nothing to change in Craft' . PHP_EOL;
echo count($plan) . ' records get a Correction note' . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (preg_match('~\x{2014}~u', implode('', array_column($NOTES, 2)) . $NOTE_5333)) { $bad[] = 'an em dash in a note'; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = []; $done = 0;
foreach ($plan as $id => $notes) {
    $e = Entry::find()->id($id)->status(null)->one();
    $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
    foreach ($notes as $n) { $rows[] = ['heading' => 'Correction, 2026', 'position' => 'bottom', 'note' => $n]; }
    $vals = ['editorNotes' => $rows];
    if ($id === 5333) { $vals += ['photoDate' => 'July 8, 1936', 'photoDateEdtf' => '1936-07-08']; }
    $e->setFieldValues($vals);
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id"; continue; }
    $back = array_column(Entry::find()->id($id)->status(null)->one()->editorNotes ?? [], 'note');
    foreach ($notes as $n) { if (!in_array($n, $back, true)) { $short[] = "#$id note"; } }
    $done++;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $done records") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_live_errors_in_craft.php', $done, $short ? 'SHORT' : 'verified', 'the 18 live errors in Craft: correction notes, one date field; text not rewritten');
if ($short) { throw new \RuntimeException('fix_live_errors_in_craft: ' . implode(', ', $short)); }
