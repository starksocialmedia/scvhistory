/**
 * The family test (Nathan, 3 October 2026: "Juventino: cut to a line. Camulos
 * is ten miles west, in Ventura County, and his only tie here is land his
 * father held. That is inherited, not his own ... Apply the same test to
 * anyone else whose connection is their father's or husband's rather than
 * their own").
 *
 *   Juventino del Valle (#303)  cut to a line: he ran Rancho Camulos from 1862
 *                               to 1886, and Camulos was part of the Rancho San
 *                               Francisco, the grant made to his grandfather
 *                               Antonio, in the part his father held. Leon
 *                               Worden's Ygnacio page (LW2052, #293) has it.
 *   Remi Nadeau (#18869)        his tie is his own (the Soledad Canyon deer
 *                               park), but the profile opened with his
 *                               grandfather. Same sentences and notes,
 *                               reordered to lead with the deer park.
 * Read across all 133 person records; the others whose tie may be borrowed
 * are reported to Nathan, not changed here.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/revise_inherited_ties.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $F = "$root/inventory/legacy/fetched";
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$VERIFIED = json_decode((string)@file_get_contents("$F/batch5-sha.json"), true)['files'] ?? [];
$read = function ($src) use ($ws, $F, $VERIFIED) {
    if (is_string($src)) { return isset($VERIFIED["$src.htm"]) && hash_file('sha256', "$F/$src.htm") === $VERIFIED["$src.htm"] ? $ws(@file_get_contents("$F/$src.txt")) : ''; }
    return $ws(Entry::find()->id($src)->status(null)->one()?->body);
};
$bad = []; $plan = [];

/* Juventino */
$J = Entry::find()->id(303)->status(null)->one();
$must = ['lw3664' => ['Juventino (1841-1919) was the eldest child of Ygnacio del Valle', 'he served as ranch manager from 1862-1886'],
    293 => ['Antonio was assigned to inventory former mission properties. He quit the army and petitioned Gov. Juan B. Alvarado to grant him some of the mission property: the 48,829-acre Rancho San Francisco', 'a judge awarded awarded 13,599 acres to Ygnacio (the westernmost section)', 'his property (the section known as Camulos, near Piru along today\'s State Route 126, about 10 miles west of Interstate 5)']];
foreach ($must as $src => $ps) { $t = $read($src); foreach ($ps as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = 'Juventino: ' . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
$jBody = 'Juventino del Valle, the eldest child of Ygnacio del Valle, ran Rancho Camulos as its manager from 1862 to 1886.[1] Camulos, near Piru in Ventura County, about ten miles west of today\'s Interstate 5, was part of the Rancho San Francisco, the grant made to his grandfather Antonio del Valle, and lay in the part his father held.[2]';
$jNotes = ['Leon Worden, "Juventino del Valle, Black Walnut Tree, (5) Important Camulos Views, 1910s," LW3664, as carried on SCVHistory.com, /scvhistory/lw3664.htm.', 'Leon Worden, "Ygnacio del Valle," LW2052, the profile of person #293 in this archive.'];
if (!$J || $J->title !== 'Juventino del Valle' || ($J->bodyAuthorship->value ?? '') !== 'editorial-2026') { $bad[] = 'Juventino: not the batch 3 record'; }
$plan[303] = ['body' => $jBody, 'notes' => $jNotes];

/* Nadeau the grandson: reorder, same text */
$N = Entry::find()->id(18869)->status(null)->one();
$nBody = 'Remi Nadeau owned a ranch in Soledad Canyon, on the north side of the canyon road near Whites Canyon,[1] and there kept a deer park, photographed about 1929 (record #3695). A later summary of the Los Angeles Times reports that he opened it in 1927 as a deer farm, at a cost of $40,000, stocked with mule deer from the Kaibab Forest, elk from Yellowstone and buffalo from Arizona.[2]'
    . "\n\n" . 'He was a grandson of the Los Angeles freighter Remi Nadeau, and the son of the freighter\'s eldest son, Joseph Frye Nadeau.[3] He was born in Minnesota on 3 May 1867, and died on 25 November 1941 in Glendale; he is buried at Angelus Rosedale Cemetery in Los Angeles, in the lot where his father lies.[4]';
$old = array_values($N->footnotes ?? []);
$was = 'Remi Nadeau was a grandson of the Los Angeles freighter Remi Nadeau';
$nDone = trim((string)$N->body) === $nBody;
if (!$nDone) {
    if (!str_starts_with(trim(strip_tags((string)$N->body)), $was) || count($old) !== 4) { $bad[] = 'Nadeau #18869: body is not the one this reorders'; }
    else {
        /* the same sentences: every sentence of the new body must be in the old */
        /* the one wording change: the opening clause names him, where the old
           second paragraph began "In the Santa Clarita Valley he owned", and the old opening
           "Remi Nadeau was a grandson" becomes "He was a grandson" */
        $strip = fn($s) => preg_replace('~\[\d+\]~', '', $ws($s));
        $oldText = str_replace(['In the Santa Clarita Valley he owned', 'Remi Nadeau was a grandson'], ['Remi Nadeau owned', 'He was a grandson'], $strip($N->body));
        foreach (preg_split('~(?<=[.;,])\s+~', $strip($nBody)) as $x) { if (trim($x) !== '' && !str_contains($oldText, trim($x))) { $bad[] = "Nadeau #18869: new text not in old: \"$x\""; } }
    }
}
$nNotes = $nDone ? null : array_map(fn($f) => (string)$f['note'], [$old[2] ?? [], $old[3] ?? [], $old[0] ?? [], $old[1] ?? []]);
$plan[18869] = ['body' => $nBody, 'notes' => $nNotes, 'done' => $nDone];
$plan[303]['done'] = trim((string)$J->body) === $jBody;

foreach ([303 => $J, 18869 => $N] as $id => $e) { echo str_pad($e->title, 22) . ($plan[$id]['done'] ? 'already revised' : str_word_count(strip_tags((string)$e->body)) . ' words -> ' . str_word_count($plan[$id]['body'])) . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$els = Craft::$app->getElements(); $n = 0; $short = [];
foreach ([303, 18869] as $id) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$plan[$id]['done']) {
        $e->setFieldValues(['body' => $plan[$id]['body'], 'footnotes' => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($plan[$id]['notes']), $plan[$id]['notes'])]);
        if (!$els->saveElement($e)) { $short[] = "#$id " . json_encode($e->getFirstErrors()); continue; }
    }
    $r = Entry::find()->id($id)->status(null)->one();
    $ok = trim((string)$r->body) === $plan[$id]['body']; $ok ? $n++ : $short[] = "#$id";
    echo ($ok ? 'OK    ' : 'SHORT ') . $r->title . ' ' . $r->url . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('revise_inherited_ties.php', $n, $short ? 'SHORT' : 'verified', 'Juventino cut to a line (inherited tie); Remi Nadeau the grandson reordered to lead with his deer park');
if ($short) { throw new \RuntimeException('revise_inherited_ties: ' . implode(', ', $short)); }
