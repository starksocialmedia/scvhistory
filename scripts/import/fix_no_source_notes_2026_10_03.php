/**
 * The "no source" notes of 26 September to 3 October 2026: correct the wrong
 * ones and record the search behind every one (Nathan, 3 October 2026,
 * approving "the 16-note correction script"; and his rule, docs/PROFILES.md, "A
 * note that says no source exists records the search").
 *
 * WHY. A faulty search (the agent shell's grep, which silently skips the
 * mirror's latin-1 pages) put false "no source" notes on public records this
 * week. Every such note was re-searched on 3 October with a Python script
 * reading all 84,849 text files of the mirror as cp1252, and against the
 * archive's records (inventory/review/no-source-notes-2026-10-03.md and .json):
 * 80 notes; 59 survive, 7 were wrong, 9 partly wrong, 5 concern outside federal
 * registers the mirror cannot test.
 *
 * WHAT IT DOES
 *   1. Corrects 14 of the 16 wrong or partly wrong notes, each now citing the
 *      source found (exact-match replacement: a note or sentence that is not
 *      exactly as read on 3 October is refused). With #339's, the same name
 *      error on #18869 (the freighter called "Remi Allen Nadeau"). The other
 *      two are done by their own scripts: Couts (#323) by widen_couts_profile.php,
 *      the California Battalion (#946) by restore_california_battalion.php.
 *   2. The search behind every one of the 80 notes is NOT written to the
 *      records: it is in inventory/source-searches.json, keyed by record (Nathan,
 *      3 October 2026: "The note says what is not known, the file says what was
 *      searched and how"). The public notes keep their plain sentences.
 *   2b. Date and evidence fields on Rodolfo Acosta (#343) and Andrés Pico
 *      (#317), as Nathan approved (3 October 2026), each guarded on its value.
 *   3. removed-claims.json: the search beside the three retirements that rested
 *      on "nothing found" (De Anza Expedition, the two missions), and an entry
 *      for Ward Connerly (#16411, removed 1 October), whose removal reason ("the
 *      archive's only mention") was wrong though the decision stands. Written
 *      only on apply, after the database commits.
 *   4. Lists, without touching them, other notes on this week's records that
 *      say something was not found and have no entry in source-searches.json.
 * Quotations are checked against byte copies of the mirror pages
 * (_source_texts.php) or the archive records they cite.
 * One transaction; read-back; apply log. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_no_source_notes_2026_10_03.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $els = Craft::$app->getElements();
$SRC = require "$root/scripts/import/_source_texts.php"; $ws = $SRC['ws'];
$bad = $SRC['bad'];
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$rowsOf = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$fnOf = fn($e) => array_values(array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? 'editorial-2026')], array_filter($e->footnotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$DATA = json_decode(<<<'JSON'
{
 "reg": {
  "942": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"De Anza Expedition\", \"Anza Expedition\", and \"Anza\" within 300 characters of \"Santa Clara\", \"Newhall\", \"Castaic\", \"Tejon\", \"San Fernando\", \"Soledad\" or \"valley\". Anza Drive, the Anza trail on trail maps and a library title; Reynolds has the 1776 expedition at the Colorado River only. Nothing puts it in the valley.",
  "16515": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"Mission San Francisco de Asís\", \"Mission Dolores\" within 400 characters of \"Santa Clar\", \"Newhall\", \"San Fernando\", \"Castaic\", \"Tataviam\", \"Camulos\". A list of missions and a library title only.",
  "16517": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"Mission Santa Cruz\". One page, about Santa Cruz County remains. Nothing ties it to the valley."
 },
 "connerly": {
  "record": 16411,
  "title": "Ward Connerly",
  "section": "persons",
  "why": "Removed 1 October 2026 (Nathan: \"remove unless he has a valley connection\"). Four of Leon Worden's 1996 columns mention him, all on Proposition 209 (January 31, September 4, October 9 and November 13), and none places him in the valley. The removal script's reason, that the January 31 column was the archive's only mention, was wrong; the decision stands.",
  "removed": "2026-10-01",
  "by": "scripts/import/remove_insignificant_persons.php; this entry added by scripts/import/fix_no_source_notes_2026_10_03.php",
  "search": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"Connerly\". Six pages: the four columns and two copies of the column index. None places him in the valley."
 }
}
JSON, true);

/* 1. The corrections. 'note' replaces a substring of one editorNotes row,
   'body' a substring of the body, 'fnadd' appends a footnote, 'fn' replaces a
   substring of one footnote. */
$FIX = [
 ['WRONG', 339, 'note', 'This record formerly called him Remi Allen Nadeau. No source for the middle name Allen has been found; the sources call him Remi or Rémi Nadeau.',
   'This record formerly called him Remi Allen Nadeau. That name belongs to his great-great-grandson, the historian Remi Allen Nadeau, born in 1920 (Leon Worden, caption to record #3695); no source in the archive gives it to the freighter, whom the sources call Remi or Rémi Nadeau.'],
 ['WRONG (same error)', 18869, 'note', 'the grandson of the Los Angeles freighter Remi Allen Nadeau (1821-1887), who is linked here.', 'the grandson of the Los Angeles freighter Rémi Nadeau (1821-1887), who is linked here.'],
 ['WRONG', 315, 'body', 'No source in this archive places him in the Santa Clarita Valley.',
   'Leon Worden wrote in 1998 that Frémont and Carson "rode through the area in the mid-1840s," meaning the pass between the San Fernando and Santa Clarita valleys; he gives no date, and no other source in the archive places Carson in the valley.[3]'],
 ['WRONG', 315, 'fnadd', '3', 'Leon Worden, "Beale\'s Cut, parade, the \'Marsha question\'," The Signal, June 24, 1998, article #12334 in this archive: "Explorers John C. Fremont and Kit Carson rode through the area in the mid-1840s."'],
 ['PARTLY', 315, 'note', 'No source has been found for the day of his death, May 23, 1868, or for his burial place.',
   'Dr. Alan Pollack gives his death at Fort Lyons, Colorado, on May 23, 1868, and his burial near his old home at Taos, New Mexico, where the grave is in Kit Carson Memorial State Park (note 1).'],
 ['PARTLY', 343, 'body', 'No source in this archive places him in the Santa Clarita Valley.',
   'Leon Worden notes that a few of the films and television episodes he appeared in were made in the Santa Clarita Valley, among them Apache Warrior (1957), which used Vasquez Rocks; no source in the archive places him here otherwise.[3]'],
 ['PARTLY', 343, 'fnadd', '3', 'Leon Worden, "Rodolfo Acosta Co-stars in \'Apache Warrior\' (1957)," LW3399, as carried on SCVHistory.com, /scvhistory/lw3399.htm: "A few of the motion pictures and television episodes that featured Acosta were made in the Santa Clarita Valley"; "According to his Texas birth certificate, Rodolfo entered the world as a U.S. citizen on July 29, 1920"; "Acosta succumbed to cancer November 7, 1974, at the Motion Picture Home in Woodland Hills."'],
 ['PARTLY', 343, 'note', 'This record formerly gave his birth as July 29, 1920, in El Paso, Texas, and his death as November 7, 1974. The source here gives the years only. No source has been found for his burial place.',
   'This record\'s dates follow Leon Worden, who cites his Texas birth certificate for his birth on July 29, 1920, in El Paso, and gives his death on November 7, 1974, at the Motion Picture Home in Woodland Hills (note 3); the archive does not hold the certificate. No source has been found for his burial place.'],
 ['WRONG', 2588, 'body', 'No other source in the archive describes him, so these columns are this record\'s only source.',
   'Pauline Harte\'s columns of 1997 call him the managing editor of The Signal, and his byline is on Signal news stories in the archive from 1990 to 2003; the introduction to The Citizen of 1988 on SCVHistory.com recalls him joining The Signal as a cub reporter, and notes he was back there in 2018.[3][4]'],
 ['WRONG', 2588, 'fnadd', '3', 'Pauline Harte, "Little glitches make the best memories," July 22, 1997, article #12745 in this archive: Tim Whyte, "the managing editor of The Mighty Signal"; and her column of April 29, 1997, article #12757: "the illustrious, multi-talented managing editor of this provocatively unique newspaper."'],
 ['WRONG', 2588, 'fnadd', '4', 'The Santa Clarita Valley Citizen, September 18, 1988, with SCVHistory.com\'s introduction, as carried there, /scvhistory/citizen19880918.htm: "a cub reporter named Tim Whyte"; "Tim is back as of 2018 after a decade-long vacation." His Signal bylines in the archive: May 2, 1990 (/scvhistory/jd9002.htm), September 24 and 29, 1991 (/scvhistory/hs_vtc_art_1992.htm), November 18, 1991 (/scvhistory/gt8703.htm), and October 29, 2003 (/scvhistory/1003-fire-index.htm).'],
 ['WRONG', 285, 'note', 'No source has been found for his burial place, given here as Santa Clara. The New York Tribune reported only that his body was given to his friends.',
   'Dick Cox (Real West, November 1965, as carried on SCVHistory.com, /scvhistory/vasquez-cox.htm) writes that his body was taken to Santa Clara and lies in "the Catholic Cemetery at Santa Clara," where his sister guarded the grave for nearly a week. The New York Tribune reported only that his body was given to his friends.'],
 ['PARTLY', 297, 'note', 'No source has been found for the day of his death, January 1, 1782, or for his burial place.',
   'Leon Worden\'s galleries of the Carmel Mission basilica (as carried on SCVHistory.com, /gif/galleries/lw2654/ and lw2655/) give his death on January 1, 1782, and his entombment with Serra and Lasuén in crypts beneath the headstones next to the altar.'],
 ['WRONG', 299, 'note', 'No source in this archive gives his burial place.',
   'Leon Worden\'s galleries of the Carmel Mission basilica (as carried on SCVHistory.com, /gif/galleries/lw2654/ and lw2655/) record that "at his request" he "was buried beside Padre Crespí before the main altar," where Serra, Crespí and Lasuén lie in crypts beneath the headstones.'],
 ['PARTLY', 317, 'note', 'No source has yet been found for his dates of birth and death, his burial place or his later public offices, so the profile does not repeat them.',
   'Leon Worden\'s timeline gives his death at his home at 203 Main Street, Los Angeles, on February 14, 1876, and Vernette Snyder Ripley (1948) quotes the Los Angeles Evening Express\'s editorial on his death that day; Laurance Landreth Hill\'s La Reina (1929) calls him "Senator Andres Pico" in 1859. The sources here say he was born in San Diego but give no date, and none gives his burial place.'],
 ['PARTLY', 16039, 'body', 'That it is the oldest surviving refinery in the world is said in City of Santa Clarita tourism copy and in online encyclopedias, with no source; none of the landmark bodies says it.[11]',
   'That it is the oldest surviving refinery in the world is said in the City of Santa Clarita\'s General Plan of 1991 and its tourism copy, in captions on SCVHistory.com ("believed to be the oldest existing refinery in the world") and in online encyclopedias, none with a source; none of the landmark bodies says it.[11]'],
 ['PARTLY', 16039, 'fn', '11', 'City of Santa Clarita, Old Town Newhall walking tour: "believed to be the oldest existing refinery in the world," no source given.',
   'City of Santa Clarita, General Plan, Open Space and Conservation Element, Historic Resources, adopted June 25, 1991, as carried on SCVHistory.com, /scvhistory/city-historic-resources-91.htm: "This is the oldest existing oil refinery in the world." City of Santa Clarita, Old Town Newhall walking tour: "believed to be the oldest existing refinery in the world," no source given. Leon Worden, caption to AP2522 and others, /scvhistory/ap2522.htm: "believed to be the oldest existing refinery in the world."'],
 ['PARTLY', 562, 'note', 'no source tying him to the valley has been found beyond the legacy page, which does not give his home.',
   'a Jack Harland is among the graduates in the Newhall School\'s commencement program of 1929 (as carried on SCVHistory.com, /scvhistory/ku2902a.htm); that he is this man, who was 28 in 1944, is likely but not established.'],
 ['PARTLY', 562, 'note', 'No connection to the Santa Clarita Valley has been found in the archive.',
   'The only possible connection found in the archive is the Newhall School\'s commencement program of 1929, which lists a Jack Harland among its graduates.'],
];
$QUOTES = [
 ['lw2449', 'the author Remi Allen Nadeau (aka Remi Nadeau III), born Aug. 30, 1920, is the great-great-grandson of the L.A. freighter'],
 ['lw062498', 'Explorers John C. Fremont and Kit Carson rode through the area in the mid-1840s'], ['lw062498', 'the mountains separating the San Fernando and Santa Clarita valleys'],
 ['pollack1114kitcarson', 'Carson died of a ruptured aortic aneurysm at Fort Lyons, Colo., on May 23, 1868'], ['pollack1114kitcarson', 'His remains were taken for burial near his old home at Taos'], ['pollack1114kitcarson', 'his grave can be visited at Kit Carson Memorial State Park'],
 ['lw3399', 'A few of the motion pictures and television episodes that featured Acosta were made in the Santa Clarita Valley'], ['lw3399', 'which used Vasquez Rocks'],
 ['lw3399', 'According to his Texas birth certificate , Rodolfo entered the world as a U.S. citizen on July 29, 1920'], ['lw3399', 'El Paso'], ['lw3399', 'Acosta succumbed to cancer November 7, 1974, at the Motion Picture Home in Woodland Hills'],
 ['ph072297', 'the managing editor of The Mighty Signal'], ['ph042997', 'The illustrious, multi-talented managing editor of this provocatively unique newspaper'],
 ['citizen19880918', 'a cub reporter named Tim Whyte'], ['citizen19880918', 'Tim is back as of 2018 after a decade-long vacation'],
 ['jd9002', 'By Tim Whyte, Signal staff writer. The Signal | Friday, May 2, 1990'], ['hs_vtc_art_1992', 'By Tim Whyte. The Newhall Signal & Saugus Enterprise | Tuesday, September 24, 1991'], ['hs_vtc_art_1992', 'Sunday, September 29, 1991'],
 ['gt8703', 'By Tim Whyte. The Newhall Signal and Saugus Enterprise | Monday, November 18, 1991'], ['fire-index-2003', 'By Tim Whyte | The Signal, 10-29-2003'],
 ['vasquez-cox', 'Since then Vasquez\'s corpse has been allowed to rest peacefully in the Catholic Cemetery at Santa Clara'], ['vasquez-cox', 'His sister, Maria'], ['vasquez-cox', 'She guarded the grave night and day for nearly a week'], ['vasquez-cox', 'Real West magazine, November 1965'],
 ['lw2655', 'On Jan. 1, 1782, Padre Juan Crespí, friend and co-worker with Serra, passed to his reward'], ['lw2654', 'Frs. Serra, Crespi and Lasuen are entombed in crypts beneath the headstones next to the altar'],
 ['lw2655', 'at his request was buried beside Padre Crespí before the main altar'],
 ['timeline', 'February 14: Gen. Andres Pico dies at his home at 203 Main St., Los Angeles'], ['ripley14', '1876, February 14. Los Angeles Evening Express. Editorial: "General Andres Pico. On the death of Don Andres Pico'],
 ['lareina1929-p42', 'in 1859 Senator Andres Pico'], ['glossary', 'San Diego-born Gen. Andres Pico'],
 ['city-historic-resources-91', 'This is the oldest existing oil refinery in the world'], ['city-historic-resources-91', 'Adopted by the City Council June 25, 1991'], ['ap2522', 'believed to be the oldest existing refinery in the world'],
 ['ku2902a', 'Harland, Jack'], ['ku2902a', 'Commencement Program, 1929'], ['ww2_jackharland', 'Age at Loss: 28'],
 ['lw013196', 'Ward Connerly'], ['lw090496', 'Ward Connerly'], ['lw100996', 'Ward Connerly'], ['lw111396', 'Ward Connerly'],
];
foreach ($QUOTES as [$k, $q]) { $ok = $SRC['has']($k, $q); echo ($ok ? 'quote ok   ' : 'QUOTE MISSING ') . "$k: \"" . mb_substr($q, 0, 70) . '"' . PHP_EOL; if (!$ok) { $bad[] = "$k does not read \"" . mb_substr($q, 0, 60) . '"'; } }
foreach ([[3695, 'Remi Allen Nadeau'], [12334, 'Explorers John C. Fremont and Kit Carson rode through the area in the mid-1840s'], [12745, 'the managing editor of The Mighty Signal'], [12757, 'multi-talented managing editor']] as [$rid, $q]) {
    $r = $get($rid); $t = ''; if ($r) { foreach (['body', 'photoCaptionExt', 'webmasterNoteTop', 'webmasterNoteBottom'] as $h) { if ($r->getFieldLayout()->getFieldByHandle($h)) { $t .= ' ' . strip_tags((string)$r->getFieldValue($h)); } } }
    $ok = str_contains($ws($t), $ws($q)); echo ($ok ? 'record ok  ' : 'RECORD MISSING ') . "#$rid: \"$q\"" . PHP_EOL; if (!$ok) { $bad[] = "#$rid does not read \"$q\""; }
}

/* Plan every record's corrections. */
$plan = []; $nFix = 0; $nFixDone = 0;
$ids = array_values(array_unique(array_column($FIX, 1)));
foreach ($ids as $id) {
    $e = $get($id); if (!$e) { $bad[] = "#$id not found"; continue; }
    $L = $e->getFieldLayout(); $body = $L->getFieldByHandle('body') ? (string)$e->body : null; $rows = $rowsOf($e); $fns = $fnOf($e); $changed = [];
    foreach (array_filter($FIX, fn($f) => $f[1] === $id && $f[2] !== 'fn') as [$cls, , $kind, $old, $new]) {
        $nFix++;
        if ($kind === 'body') {
            if (str_contains($body, $new)) { $nFixDone++; continue; }
            if (substr_count($body, $old) !== 1) { $bad[] = "#$id body: the sentence is not exactly as read"; continue; }
            $body = str_replace($old, $new, $body); $changed['body'] = true; echo "#$id {$e->title} [$cls] BODY" . PHP_EOL . "  OLD: $old" . PHP_EOL . "  NEW: $new" . PHP_EOL;
        } elseif ($kind === 'note') {
            $hits = array_keys(array_filter($rows, fn($r) => str_contains($r['note'], $old)));
            if (array_filter($rows, fn($r) => str_contains($r['note'], $new))) { $nFixDone++; continue; }
            if (count($hits) !== 1 || substr_count($rows[$hits[0]]['note'], $old) !== 1) { $bad[] = "#$id editorNotes: the note is not exactly as read (\"" . mb_substr($old, 0, 50) . '")'; continue; }
            $i = $hits[0]; echo "#$id {$e->title} [$cls] EDITOR NOTE \"{$rows[$i]['heading']}\"" . PHP_EOL . '  OLD: ' . $rows[$i]['note'] . PHP_EOL;
            $rows[$i]['note'] = str_replace($old, $new, $rows[$i]['note']); $changed['editorNotes'] = true; echo '  NEW: ' . $rows[$i]['note'] . PHP_EOL;
        } elseif ($kind === 'fnadd') {
            $at = array_filter($fns, fn($r) => $r['number'] === $old);
            if ($at) { if (array_values($at)[0]['note'] === $new) { $nFixDone++; continue; } $bad[] = "#$id already has a different footnote $old"; continue; }
            if (count($fns) !== (int)$old - 1) { $bad[] = "#$id has " . count($fns) . " footnotes, expected " . ((int)$old - 1); continue; }
            $fns[] = ['number' => $old, 'note' => $new, 'source' => 'editorial-2026']; $changed['footnotes'] = true; echo "#$id {$e->title} [$cls] FOOTNOTE ADD [$old] $new" . PHP_EOL;
        }
    }
    foreach (array_filter($FIX, fn($f) => $f[1] === $id && $f[2] === 'fn') as [$cls, , , $num, $o, $n]) {
        $nFix++; $k = array_keys(array_filter($fns, fn($r) => $r['number'] === $num));
        if ($k && str_contains($fns[$k[0]]['note'], $n)) { $nFixDone++; continue; }
        if (count($k) !== 1 || substr_count($fns[$k[0]]['note'], $o) !== 1 || !str_starts_with($fns[$k[0]]['note'], $o)) { $bad[] = "#$id footnote $num is not exactly as read"; continue; }
        echo "#$id {$e->title} [$cls] FOOTNOTE [$num]" . PHP_EOL . "  OLD: $o" . PHP_EOL . "  NEW: $n" . PHP_EOL;
        $fns[$k[0]]['note'] = str_replace($o, $n, $fns[$k[0]]['note']); $changed['footnotes'] = true;
    }
    if ($changed) { $plan[$id] = ['body' => isset($changed['body']) ? $body : null, 'editorNotes' => isset($changed['editorNotes']) ? $rows : null, 'footnotes' => isset($changed['footnotes']) ? $fns : null]; }
}

/* 2b. Date and evidence fields (Nathan, 3 October 2026: "Keeping a value and
   marking it uncited is the right pattern ... The Vasquez and Crespí precedents
   hold"). Each field must read exactly as below before it is changed. Rodolfo
   Acosta's dates follow Leon Worden's LW3399, which cites his Texas birth
   certificate (not held by the archive); Pico's death date follows Leon Worden's
   timeline (the Los Angeles Evening Express item is not held). */
$FIELDS = [
 [343, 'birthDate', '1920', 'July 29, 1920'], [343, 'birthDateEdtf', '1920', '1920-07-29'], [343, 'birthplace', '', 'El Paso, Texas'], [343, 'birthEvidence', 'retrospective', 'retrospective'],
 [343, 'deathDate', '1974', 'November 7, 1974'], [343, 'deathDateEdtf', '1974', '1974-11-07'], [343, 'deathEvidence', 'retrospective', 'retrospective'],
 [343, 'burialPlace', 'Forest Lawn Memorial Park, Hollywood Hills, California', 'Forest Lawn Memorial Park, Hollywood Hills, California'], [343, 'burialEvidence', 'uncited', 'uncited'],
 [317, 'deathDate', 'February 14, 1876', 'February 14, 1876'], [317, 'deathDateEdtf', '1876-02-14', '1876-02-14'], [317, 'deathEvidence', 'uncited', 'retrospective'],
 [317, 'birthDate', 'November 18, 1810', 'November 18, 1810'], [317, 'birthEvidence', 'uncited', 'uncited'],
 [317, 'burialPlace', 'Mission San Fernando Rey de España, Mission Hills, California', 'Mission San Fernando Rey de España, Mission Hills, California'], [317, 'burialEvidence', 'uncited', 'uncited'],
];
$fv = function ($e, $h) { $v = $e->getFieldValue($h); if ($v instanceof \DateTimeInterface) { return $v->format('Y-m-d'); } if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value; } return trim((string)$v); };
foreach (['birthEvidence', 'deathEvidence', 'burialEvidence'] as $h) {
    $f = Craft::$app->getFields()->getFieldByHandle($h); $opts = $f ? array_column($f->options, 'value') : [];
    foreach (['certified', 'contemporary', 'retrospective', 'roster', 'uncited'] as $o) { if (!in_array($o, $opts, true)) { $bad[] = "$h has no option \"$o\""; } }
}
foreach ([343 => [['lw3399', 'According to his Texas birth certificate , Rodolfo entered the world as a U.S. citizen on July 29, 1920'], ['lw3399', 'He was born in the family home at 609 E. 3rd Avenue in El Paso'], ['lw3399', 'Acosta succumbed to cancer November 7, 1974, at the Motion Picture Home in Woodland Hills']],
          317 => [['timeline', 'February 14: Gen. Andres Pico dies at his home at 203 Main St., Los Angeles'], ['ripley14', '1876, February 14. Los Angeles Evening Express']]] as $fid => $qs) {
    foreach ($qs as [$k, $q]) { if (!$SRC['has']($k, $q)) { $bad[] = "#$fid date source: $k does not read \"" . mb_substr($q, 0, 60) . '"'; } }
}
echo 'deathPlace field: ' . (Craft::$app->getFields()->getFieldByHandle('deathPlace') ? 'exists (not set here; the place is in the note)' : 'none; the place of death is in the note only') . PHP_EOL;
foreach ($FIELDS as [$id, $h, $old, $new]) {
    $e = $get($id); if (!$e->getFieldLayout()->getFieldByHandle($h)) { $bad[] = "#$id has no field $h"; continue; }
    $cur = $fv($e, $h);
    if ($cur === $new) { echo "#$id {$e->title} $h: \"$cur\"" . ($old === $new ? ' (kept)' : ' (already set)') . PHP_EOL; continue; }
    if ($cur !== $old) { $bad[] = "#$id $h reads \"$cur\", not \"$old\""; continue; }
    echo "#$id {$e->title} $h: \"$old\" -> \"$new\"" . PHP_EOL;
    $plan[$id] = $plan[$id] ?? ['body' => null, 'editorNotes' => null, 'footnotes' => null];
    $plan[$id]['fields'][$h] = $new;
}

/* 3. removed-claims.json, shown; written only on apply. */
$REGF = "$root/scripts/import/removed-claims.json"; $reg = json_decode((string)file_get_contents($REGF), true); $regChanged = false;
foreach ($DATA['reg'] as $rid => $s) {
    $k = array_keys(array_filter($reg['removedRecords'], fn($x) => ($x['record'] ?? 0) === (int)$rid));
    if (count($k) !== 1) { $bad[] = "removed-claims.json has no single entry for #$rid"; continue; }
    if (($reg['removedRecords'][$k[0]]['search'] ?? '') === $s) { continue; }
    if (isset($reg['removedRecords'][$k[0]]['search'])) { $bad[] = "removed-claims.json #$rid has a different search"; continue; }
    $reg['removedRecords'][$k[0]]['search'] = $s; $regChanged = true; echo "removed-claims.json #$rid {$reg['removedRecords'][$k[0]]['title']}: ADD \"search\": $s" . PHP_EOL;
}
$C = $DATA['connerly'];
if (!array_filter($reg['removedRecords'], fn($x) => ($x['record'] ?? 0) === $C['record'])) {
    if (Entry::find()->section('persons')->status(null)->title('Ward Connerly')->exists()) { $bad[] = 'Ward Connerly is live; the registry entry would fail check_removed_claims.php'; }
    $reg['removedRecords'][] = $C; $regChanged = true; echo 'removed-claims.json ADD ' . json_encode($C, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
}

/* 4. Other notes this week saying something was not found, not covered here. */
$logged = array_map(fn($k) => (int)explode(':', $k)[1], array_keys(json_decode((string)@file_get_contents("$root/inventory/source-searches.json"), true)['records'] ?? []));
if (!$logged) { $bad[] = 'inventory/source-searches.json is missing or empty'; }
$covered = array_merge($ids, $logged, [323, 946]); $other = [];
foreach (Entry::find()->status(null)->dateUpdated('>= 2026-09-26')->limit(null)->each(200) as $e) {
    if (in_array($e->id, $covered, true) || !$e->getFieldLayout() || !$e->getFieldLayout()->getFieldByHandle('editorNotes')) { continue; }
    foreach ($rowsOf($e) as $r) { if (preg_match('~(has|have) (not )?been (found|seen)|no source|not found|no record of~i', $r['note']) && !preg_match('~^(Searched|Correction)~', $r['note'])) { $other[] = "#{$e->id} {$e->title} \"{$r['heading']}\": " . mb_substr(strip_tags($r['note']), 0, 160); } }
}
echo PHP_EOL . 'NOT COVERED (other notes this week that say something was not found; listed, not changed): ' . count($other) . PHP_EOL . ($other ? '  ' . implode(PHP_EOL . '  ', $other) . PHP_EOL : '');

$text = json_encode($FIX, JSON_UNESCAPED_UNICODE);
if (preg_match('~\x{2014}|inventory/|\.json~u', $text)) { $bad[] = 'an em dash or a repository path in the public text'; }
echo PHP_EOL . "Corrections: $nFix (" . ($nFix - $nFixDone) . ' to make, ' . $nFixDone . ' already made). Records to save: ' . count($plan) . '. Search log: inventory/source-searches.json (' . count(array_unique($logged)) . ' records). Registry: ' . ($regChanged ? 'to write' : 'unchanged') . '.' . PHP_EOL;
echo 'Mirror mounted for a live hash check: ' . ($SRC['mirror'] ? 'yes' : 'no (checked against the byte copies only)') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$PROV = '; fix_no_source_notes_2026_10_03.php, 3 Oct 2026: no-source notes re-searched';
$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $id => $v) {
        $e = $get($id); $L = $e->getFieldLayout();
        foreach (['body', 'editorNotes', 'footnotes'] as $h) { if ($v[$h] !== null) { $e->setFieldValue($h, $v[$h]); } }
        foreach ($v['fields'] ?? [] as $h => $val) { $e->setFieldValue($h, $val); }
        if ($L->getFieldByHandle('recordProvenance') && !str_contains((string)$e->recordProvenance, 'fix_no_source_notes_2026_10_03.php')) {
            $p = trim((string)$e->recordProvenance . $PROV); if (mb_strlen($p) <= 255) { $e->setFieldValue('recordProvenance', $p); }
        }
        if (!$els->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
if ($regChanged) { file_put_contents($REGF, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"); }
$short = [];
foreach ($plan as $id => $v) {
    $e = $get($id);
    if ($v['body'] !== null && (string)$e->body !== $v['body']) { $short[] = "#$id body"; }
    if ($v['editorNotes'] !== null && $rowsOf($e) != $v['editorNotes']) { $short[] = "#$id editorNotes"; }
    if ($v['footnotes'] !== null && count($fnOf($e)) !== count($v['footnotes'])) { $short[] = "#$id footnotes"; }
    foreach ($v['fields'] ?? [] as $h => $val) { if ($fv($e, $h) !== $val) { $short[] = "#$id $h"; } }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK, ' . count($plan) . ' records') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_no_source_notes_2026_10_03.php', count($plan), $short ? 'SHORT' : 'verified', "no-source notes: $nFix corrections; date fields on #343 and #317; registry " . ($regChanged ? 'updated' : 'unchanged'));
if ($short) { throw new \RuntimeException('fix_no_source_notes_2026_10_03: read-back failed'); }
