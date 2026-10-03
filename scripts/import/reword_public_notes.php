/**
 * Notes are public: reworded for a reader who knows nothing of how the archive
 * was built (Nathan, 3 October 2026: "'Came with the WordPress import' means
 * nothing to them and names an internal process. Say 'unsourced' or 'no source
 * has been found' instead. Check all notes for the same problem").
 *
 * From inventory/review/note-wording-2026-10-03.md (audit_note_wording.php):
 *   - "as migrated to this record" off the 41 war memorial footnotes that cite
 *     their legacy page;
 *   - the WordPress notes (Acosta, Perkins, Couts, Pico, Stearns, Antonio del
 *     Valle) say what is unsourced, not how it arrived; Antonio del Valle's
 *     "Composilla" turns out to be Leon Worden's, on his timeline;
 *   - the obituary footnotes of 3 October lose the mirror, file path and hash;
 *   - file paths, scripts, checksums and repository commits out of the Smyth,
 *     CEDA, Scofield and ladder-article notes;
 *   - the sixteen "no likeness" notes lose the image tag, the file name and
 *     the mirror;
 *   - the memorial "not yet sourced" notes keep what was searched and lose the
 *     to-do ("Next to try", four of them "with Nathan's approval");
 *   - Reynolds: the day of his death is not unsourced. Leon Worden's preface,
 *     already cited, ends "February 26, 1996, was a sad day in Santa Clarita as
 *     cancer claimed the life of Jerry Reynolds," and his legacy page LW2184
 *     says the same. The note is removed, the profile gives the date with both
 *     sources, and the evidence is "recalled later".
 * Leon Worden's own notes are not touched. Each change is an exact replacement
 * and refuses if the text is not as read. Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/reword_public_notes.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
ini_set('memory_limit', '2048M');

$NEXT_VA = ' Next to try, with Nathan\'s approval: the Department of Veterans Affairs\' Nationwide Gravesite Locator, a federal record of burial.';
/* [id, field, find, replace]; find '' with replace '' deletes nothing; a row whose replace is already present is done */
$OPS = [
    [341, 'editorNotes', 'This record came from WordPress with "Sacramento, California," for which no source has been found; the City\'s 2013 biography', 'This record formerly gave his birthplace as Sacramento, California, for which no source has been found. The City\'s 2013 biography'],
    [333, 'editorNotes', 'This record came from WordPress as Arthur Burnett Perkins; no source for Burnett has been found', 'This record formerly gave his name as Arthur Burnett Perkins; no source for "Burnett" has been found'],
    [323, 'editorNotes', 'This record came from WordPress with 6 November 1821, Smith County, Tennessee, for which no source has been found.', 'This record formerly gave his birth as 6 November 1821, in Smith County, Tennessee, for which no source has been found.'],
    [323, 'editorNotes', 'The burial place came with the WordPress import and has no source in the archive.', 'No source has been found for the burial place.'],
    [317, 'editorNotes', 'His dates of birth and death, his burial place and his later public offices came with the WordPress import and have no source in the archive yet; they are not repeated in the profile.', 'No source has yet been found for his dates of birth and death, his burial place or his later public offices, so the profile does not repeat them.'],
    [309, 'editorNotes', 'The day, 9 February, came with the WordPress import and has not been found in a source.', 'No source has been found for the day, 9 February.'],
    [291, 'editorNotes', 'This record came from WordPress with "Composilla, Mexico," for which no source has been found; one genealogy gives Compostela, then in the same province.', 'Leon Worden\'s timeline of the valley\'s history gives "Composilla, Mexico," a place not otherwise identified; one genealogy gives Compostela, then in the same province.'],
    [18726, 'footnotes', 'as carried on SCVHistory.com, lw2802.htm, from the legacy mirror of 20 August 2026 (inventory/legacy/fetched/lw2802.htm, SHA-256 f56decf5fcce5579...).', 'as carried on SCVHistory.com, /scvhistory/lw2802.htm.'],
    [16140, 'footnotes', 'as carried on SCVHistory.com, obituary_joannedarcy.htm, from the legacy mirror of 20 August 2026 (inventory/legacy/fetched/obituary_joannedarcy.htm, SHA-256 083c59b103b2f919...).', 'as carried on SCVHistory.com, /scvhistory/obituary_joannedarcy.htm.'],
    [15808, 'footnotes', 'as carried on SCVHistory.com, obituary_carlboyer3rd.htm, from the legacy mirror of 20 August 2026 (inventory/legacy/fetched/obituary_carlboyer3rd.htm, SHA-256 b4f4b32f73c23d91...).', 'as carried on SCVHistory.com, /scvhistory/obituary_carlboyer3rd.htm.'],
    [15985, 'footnotes', ' The federal series begins in 1986. Archive file inventory/schools/nces-enrolment.json.', ' The federal series begins in 1986.'],
    [26999, 'editorNotes', 'Every figure in this article is checked against the archive\'s own records by the script that publishes it, which refuses to write a text the records contradict. Its first draft said the board-to-council ladder was never climbed. When Buck McKeon\'s years on the Hart board were added to his record on 1 October 2026, the check refused to publish that draft, and the article was rewritten around him. The guard caught the archive\'s own text, which is what it is for.',
        'Every figure in this article was checked against the archive\'s own records before it was published. Its first draft said the board-to-council ladder was never climbed. When Buck McKeon\'s years on the Hart board were added to his record on 1 October 2026, the check caught that draft, and the article was rewritten around him.'],
    [25146, 'editorNotes', 'as kept in the repository github.com/justindbk/ceda (Justin de Benedictis-Kessner and Rachel Bernhard), commit 38705c7, because the Sacramento State portal could not be downloaded from. That repository\'s corrections are a separate script and are not applied here; none concerns the Santa Clarita Valley. The checksums are in inventory/elections/ceda-manifest.json.',
        'as published by Justin de Benedictis-Kessner and Rachel Bernhard at github.com/justindbk/ceda, because the Sacramento State portal could not be downloaded from. Their later corrections are not applied here; none concerns the Santa Clarita Valley.'],
    [21584, 'editorNotes', ' Received from Commons on 28 September 2026 as a 374 x 477 file, SHA-1 3d95720019d02001ba6d14ba859758dd86148b1c, matching the Commons original. The stored copy is re-encoded on import and differs from the file as received.', ''],
    [1395, 'editorNotes', ' did not open his record. Next to try: the Navy lists for other states, and ABMC again.', ' did not return his record.'],
    [580, 'editorNotes', ' Next to try: the Navy lists for other states, and ABMC\'s Tablets of the Missing.', ''],
    [576, 'editorNotes', ' Next to try: other counties and states in the Honor List, NARA\'s enlistment records once his service number is known, and ABMC.', ''],
    [540, 'editorNotes', ' Next to try: the release archive by date, and ABMC.', ''],
    [538, 'editorNotes', ' Next to try: the release archive by date, and ABMC.', ''],
    [536, 'editorNotes', ' Next to try: the release archive by date, and ABMC.', ''],
    [526, 'editorNotes', ' Next to try: the release archive by date, and ABMC.', ''],
    [518, 'editorNotes', 'in none of the military casualty lists read so far (the War Department\'s Honor List and the Navy\'s State Summary). Next to try: the American Field Service\'s own roll of its dead and ABMC, which commemorates some civilians.', 'in none of the military casualty lists searched (the War Department\'s Honor List and the Navy\'s State Summary).'],
    [514, 'editorNotes', ' Next to try: the lists for other states, the Marine Corps muster rolls, and ABMC.', ''],
    [542, 'editorNotes', 'and no record read so far gives one.', 'and no record found gives one.'],
];
foreach ([542, 534, 528, 524] as $id) { $OPS[] = [$id, 'editorNotes', 'the two sources read so far (2 October 2026).' . $NEXT_VA, 'the two sources searched on 2 October 2026.']; }

$bad = []; $plan = [];
foreach ($OPS as [$id, $fld, $find, $rep]) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e) { $bad[] = "#$id missing"; continue; }
    $rows = $e->getFieldValue($fld) ?? []; $hit = null; $done = false;
    foreach ($rows as $i => $r) { $t = (string)($r['note'] ?? ''); if (str_contains($t, $find)) { $hit = $i; break; } if ($rep !== '' && str_contains($t, $rep)) { $done = true; } }
    /* a removal is done when the text is gone from every row; a rewording when its new text is there */
    if ($hit === null && $rep === '') { $done = true; }
    if ($hit === null && !$done) { $bad[] = "#$id $fld: text not found: " . mb_substr($find, 0, 60); continue; }
    if ($hit === null) { continue; }
    $plan[] = [$id, $fld, $hit, $find, $rep];
    echo "#$id {$e->title} ($fld row $hit)" . PHP_EOL . '   - ' . mb_substr($find, 0, 140) . PHP_EOL . '   + ' . ($rep === '' ? '(removed)' : mb_substr($rep, 0, 140)) . PHP_EOL;
}
/* The 41 memorial footnotes. */
$mig = 0;
foreach (Entry::find()->section('warMemorials')->status(null)->all() as $e) {
    foreach ($e->footnotes ?? [] as $i => $r) { if (str_contains((string)($r['note'] ?? ''), ', as migrated to this record')) { $plan[] = [$e->id, 'footnotes', $i, ', as migrated to this record', '']; $mig++; } }
}
echo "war memorial footnotes, \", as migrated to this record\" removed: $mig" . PHP_EOL;
/* The sixteen "no likeness" notes: an image tag and a file name are not a reader's words. */
$LIKE = '~Leon Worden\'s memorial page for (him|her) left a place for a portrait, but the image tag was commented out and the file it names, \S+, was never on SCVHistory\.com: (he|she) did not have one either\. Checked 2 October 2026 against the page in the mirror and the live site;~';
$likes = 0;
foreach (Entry::find()->section('warMemorials')->status(null)->all() as $e) {
    foreach ($e->editorNotes ?? [] as $i => $r) {
        $t = (string)($r['note'] ?? '');
        if (preg_match($LIKE, $t, $m)) { $plan[] = [$e->id, 'editorNotes', $i, $m[0], "Leon Worden's memorial page for {$m[1]} left a place for a portrait, but {$m[2]} never had one to put there. Checked 2 October 2026 against the original page on SCVHistory.com;"]; $likes++; }
    }
}
echo "no-likeness notes reworded: $likes" . PHP_EOL;
/* Reynolds. */
$R = Entry::find()->id(281)->status(null)->one();
$OLD = 'He helped organize the Santa Clarita Valley Historical Society and was its first museum curator, and until his death in 1996 its only one.[1]';
$NEW = 'He helped organize the Santa Clarita Valley Historical Society and was its first museum curator, and its only one until his death, of cancer, on February 26, 1996.[1][3]';
$N3 = 'Leon Worden, "Jerry Reynolds, SCV Historian, 1937-1996," SCVHistory.com, /scvhistory/lw2184.htm: "a position he held until his death on Feb. 26, 1996, from cancer."';
$pref = preg_replace('~\s+~u', ' ', str_replace("\u{00A0}", ' ', html_entity_decode(strip_tags((string)Entry::find()->id(817)->one()->body), ENT_QUOTES)));
if (!str_contains($pref, 'February 26, 1996, was a sad day in Santa Clarita as cancer claimed the life of Jerry Reynolds')) { $bad[] = 'the Preface does not read as quoted'; }
$lw = preg_replace('~\s+~', ' ', (string)@file_get_contents("$root/inventory/legacy/fetched/lw2184.txt"));
if (!str_contains($lw, 'until his death on Feb. 26, 1996, from cancer')) { $bad[] = 'lw2184.txt missing or not as quoted'; }
$rDone = str_contains((string)$R->body, $NEW);
if (!$rDone && !str_contains((string)$R->body, $OLD)) { $bad[] = 'Reynolds body not as written'; }
echo 'Reynolds: ' . ($rDone ? 'already done' : 'death date into the profile with the Preface and LW2184; the "date of death" note removed; death evidence recalled later') . PHP_EOL;
if (preg_match('~\x{2014}~u', implode('', array_column($OPS, 3)) . $NEW . $N3)) { $bad[] = 'an em dash'; }
echo count($plan) . ' replacements' . PHP_EOL . 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$byEntry = [];
foreach ($plan as [$id, $fld, $i, $find, $rep]) { $byEntry[$id][$fld][] = [$i, $find, $rep]; }
$short = []; $n = 0;
$clean = fn($fld, $r) => $fld === 'footnotes' ? ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? '')] : ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')];
foreach ($byEntry as $id => $fields) {
    $e = Entry::find()->id($id)->status(null)->one(); $vals = [];
    foreach ($fields as $fld => $ops) {
        $rows = array_values(array_map(fn($r) => $clean($fld, $r), array_filter($e->getFieldValue($fld) ?? [], 'is_array')));
        foreach ($ops as [$i, $find, $rep]) { $rows[$i]['note'] = trim(str_replace($find, $rep, $rows[$i]['note'])); }
        $vals[$fld] = $rows;
    }
    $e->setFieldValues($vals);
    if (!$elements->saveElement($e)) { $short[] = "#$id " . json_encode($e->getFirstErrors()); continue; }
    $r = Entry::find()->id($id)->status(null)->one();
    foreach ($fields as $fld => $ops) { foreach ($ops as [$i, $find, $rep]) { if (str_contains(implode(' ', array_column($r->getFieldValue($fld) ?? [], 'note')), $find)) { $short[] = "#$id $fld"; } } }
    $n++;
}
if (!$rDone) {
    $R = Entry::find()->id(281)->status(null)->one();
    $fn = array_values(array_map(fn($r) => $clean('footnotes', $r), array_filter($R->footnotes ?? [], 'is_array')));
    if (!in_array($N3, array_column($fn, 'note'), true)) { $fn[] = ['number' => (string)(count($fn) + 1), 'note' => $N3, 'source' => 'editorial-2026']; }
    $ed = array_values(array_filter(array_map(fn($r) => $clean('editorNotes', $r), array_filter($R->editorNotes ?? [], 'is_array')), fn($r) => !str_starts_with($r['note'], 'The day of his death, February 26, 1996')));
    $R->setFieldValues(['body' => str_replace($OLD, $NEW, (string)$R->body), 'footnotes' => $fn, 'editorNotes' => $ed, 'deathEvidence' => 'retrospective']);
    if (!$elements->saveElement($R)) { $short[] = 'Reynolds ' . json_encode($R->getFirstErrors()); }
    $R = Entry::find()->id(281)->status(null)->one();
    if (!str_contains((string)$R->body, $NEW) || ($R->deathEvidence->value ?? '') !== 'retrospective' || count($R->footnotes) !== 3) { $short[] = 'Reynolds read-back'; } else { $n++; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('reword_public_notes.php', $n, $short ? 'SHORT' : 'verified', 'notes reworded for a public reader (import, mirror, scripts, to-dos out); Reynolds death date sourced to his preface and LW2184');
if ($short) { throw new \RuntimeException('reword_public_notes: ' . implode(', ', $short)); }
