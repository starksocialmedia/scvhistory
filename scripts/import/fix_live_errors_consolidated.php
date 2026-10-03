/**
 * The live errors from Grok's consolidated list of 69 that are in Craft and
 * not yet corrected (Nathan, 3 October 2026; the status of each is in
 * inventory/review/live-errors-consolidated-status.md, built by
 * build_live_errors_consolidated.php).
 *
 * As on 2 October (fix_live_errors_in_craft.php): the published text is not
 * rewritten, Leon Worden's or any other author's (AGENTS.md). Each error in a
 * text gets a dated, sourced Correction note on the record. Errors in data the
 * archive derived are fixed in the field:
 *   CE23  #3003 photoSourceCode AP0828 -> LW2232. Read from the legacy title
 *         tag, which carries the wrong code; the page's image is lw2232.jpg and
 *         its credit line reads LW2232. The code is what finds the record's
 *         image, and AP0828 finds a different photograph.
 *   CE25  #3215 photoSourceCode LW2311l -> LW2311m. The title tag repeats page
 *         12's code; #3213 is page 12.
 * Not here: the 17 entries corrected on 2 October (none gets a second note:
 * a target already carrying that day's note for the same error is refused),
 * the HOLD items, the slips kept as printed, and the archive's own prose that
 * repeats an error (#915, #16446 the del Valle acreage; #327, #932 the $5,000),
 * which is to be rewritten, not noted.
 *
 * Sources. A note names its source as a reader would find it. Phrases the
 * notes rely on are checked in the archive's records where it holds them
 * ($EVIDENCE). Those only on the legacy site were read there on 3 October:
 * Pen Pictures 1889 on Ygnacio del Valle ("in 1850 he was alcalde"); LW2052's
 * list of his city offices ("City Council: Member Elected: May 04, 1852",
 * "Elected: May 07, 1856 • Resigned: December 15, 1856"); MU8901, Sitton
 * ("In February 1921, Hart purchased lots 19 and 20 of Tract 1059");
 * sg051703, Worden 2003 (Oakwood Cemetery, 29 March; "an oft-repeated local
 * legend that hasn't been refuted"); the Tejon Ranch timeline (its 1865 entry,
 * "Beale purchases Rancho el Tejon"); Pollack 2014 ("graduated from the Naval School
 * in Philadelphia in 1842").
 * The Perkins rule (docs/PROFILES.md): no note cites Perkins and Reynolds as
 * two sources for one claim; a note naming both is refused. CE41, whose
 * correction rests on the two together, is held.
 *
 * Every note is checked against check_note_wording.php's pattern, and for em
 * dashes and repository paths. Idempotent: a note already present is not added
 * again, a field already right is not written, and a record with nothing to
 * do is not saved. One transaction, read back before commit.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_live_errors_consolidated.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$ws = fn($s) => preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{00A0}"], ["'", ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5)));
$HEADING = 'Correction, 2026';
$PROV = 'correction notes from the live errors list, fix_live_errors_consolidated.php, 3 Oct 2026';

$SERIES = 'Perkins\'s "The Story of Our Valley" ran weekly in The Signal from April 1954, not in 1962: the introduction to its web edition gives April 1954 to January 1955, and the Los Angeles Times, on 2 January 1977, April to December 1954.';
$DV_ASSEMBLY = 'Ygnacio del Valle was elected to the Assembly from the 2nd District on 3 September 1851 and sat in the 1852 session; he was not in the Legislature at statehood in 1850 (JoinCalifornia, "Ignacio Del Valle"; not yet confirmed in the Assembly Journal of 1852).';
$DEFIANCE = 'Fort Defiance was in New Mexico Territory, in what is now Arizona, not in Texas: Beale\'s wagon road of 1857 ran "from Fort Defiance, New Mexico Territory, to the Colorado River" (SCVHistory.com LW3491).';
$acres = 13599 + 21307 + 6 * 4684;
$ACRES = 'These figures cannot all be right. 13,599 acres for Ygnacio, 21,307 for Jacoba and 4,684 for each of six children add up to ' . number_format($acres) . ' acres, but the whole rancho was patented in 1875 at 48,611.88 acres. The heirs held it in undivided shares until it was partitioned in 1870 (United States patent to Rancho San Francisco, 12 February 1875; A.B. Perkins, "Rancho San Francisco," 1957).';
$OFFICES = 'the City of Los Angeles list of his offices on LW2052, SCVHistory.com';

/* [code, record ids, the wrong text as it reads in the record, the note]. */
$NOTES = [
    ['CE01', [27368], 'In 1962 he wrote a series of articles', 'A.B. ' . $SERIES],
    ['CE01 CE23', [3003], 'Oustanding Citizen 1964', 'A.B. ' . $SERIES . ' The title\'s "Oustanding" is a misprint for "Outstanding."'],
    ['CE09', [2131], 'Hart had purchased the 254-acre Horseshoe Ranch from Babcock Smith', 'Hart did not buy a 254-acre Horseshoe Ranch from Babcock Smith in February 1921. He had leased George Babcock Smith\'s property since 1918; in February 1921 he bought two of its lots, and he assembled the rest of the ranch deed by deed until October 1933 (Tom Sitton, survey of the deeds to Hart Park for the Natural History Museum of Los Angeles County, February 1989, at SCVHistory.com MU8901).'],
    ['CE18', [293, 851], '4,684 acres', $ACRES],
    ['CE19', [293], 'where he was mayor', 'Ygnacio del Valle was alcalde of Los Angeles in 1850, not mayor, and later sat on the city council, elected in May 1852 and May 1856 (Pen Pictures from the Garden of the World, 1889; ' . $OFFICES . ').'],
    ['CE20', [5617], 'Legislature at statehood in 1850', $DV_ASSEMBLY],
    ['CE22', [2983], 'Fort Defiance, Texas', $DEFIANCE],
    ['CE34', [1438], 'Mary Pickford and William S. Hart filmed "Rags"', 'William S. Hart was not in Rags (1915), a Mary Pickford picture: the cast lists for it in the AFI Catalog do not include him.'],
    ['CE35', [1446], 'When the hotel burned in 1887', 'The Southern Hotel burned on 23 October 1888, not in 1887 (Leon Worden\'s note to Perkins\'s "The Birth of Newhall," and LW2273, at SCVHistory.com).'],
    ['CE37', [1434, 27374], 'Served as assemblyman in 1852 and 1856.', 'JoinCalifornia records one election of Ygnacio del Valle to the Assembly, on 3 September 1851, for the session of 1852. In 1856 he sat on the Los Angeles city council, elected on 7 May and resigning on 15 December (' . $OFFICES . ').'],
    ['CE45', [1430], 'had married one of the daughters of Andres Pico', 'Dr. Vincent Gelcich married a niece of Andrés Pico, not a daughter: María Petra Celestina Pico y Bernal, daughter of Antonio María Pico, in 1863 at San Francisco (Leon Worden\'s note c to Perkins\'s "History of Pico Canyon Oil Production," at SCVHistory.com).'],
    ['CE49', [2075], 'The mayor of the town, Don Ygnacio del Valle', 'Ygnacio del Valle was not mayor of Los Angeles in 1853. He had been alcalde in 1850, and he sat on the city council from May 1852 to May 1853 and again in 1856 (Pen Pictures from the Garden of the World, 1889; ' . $OFFICES . ').'],
    ['CE50', [2077], 'He had already sold Rancho Tejon to General Beale', 'Beale did not own Rancho El Tejon by 1861. He bought it in 1865, with Rancho los Alamos y Agua Caliente, and Rancho de Castac in 1866 (Tejon Ranch Company, historical timeline, at SCVHistory.com).'],
    ['CE51', [2081], 'appointed to the U.S. Naval Academy by President Andrew Jackson', 'Andrew Jackson could not have appointed Beale to the Naval Academy, which opened at Annapolis in 1845, after his presidency. Beale trained at the Naval School in Philadelphia and graduated in 1842 (Alan Pollack, "Kit Carson, Ned Beale Cross Paths and Alter California\'s Future," Heritage Junction Dispatch, November and December 2014, at SCVHistory.com).'],
    ['CE59', [2133], 'carrying the unnamed boy\'s broken body to the Ruiz family cemetery', 'The boy was not buried at the Ruiz family cemetery. Relatives were said to have identified him, and on 29 March 1928 he was buried at Oakwood Cemetery in Chatsworth; the procession to the Ruiz cemetery went on without him. That Hart dressed him in a cowboy outfit is, in Leon Worden\'s words, "an oft-repeated local legend that hasn\'t been refuted" (Leon Worden, "Requiem to a Little Soldier," The Signal, 17 May 2003, from the Newhall Signal of 29 March and 5 April 1928).'],
    ['CE66', [12542, 12574], 'in 1962 when he wrote', $SERIES],
];
/* [code, record, field, wrong value, right value]. */
$FIELDS = [
    ['CE23', 3003, 'photoSourceCode', 'AP0828', 'LW2232'],
    ['CE25', 3215, 'photoSourceCode', 'LW2311l', 'LW2311m'],
];
/* What the data fixes rest on: [record, field, value it must hold]. */
$FIELD_EVIDENCE = [[3003, 'legacyKey', 'lw2232'], [3003, 'creditRaw', 'LW2232: 9600 dpi'], [3215, 'legacyKey', 'lw2311m'], [3213, 'photoSourceCode', 'LW2311l']];
/* Phrases the notes rely on, in the archive's own records: [record id or legacyKey, phrase]. */
$EVIDENCE = [
    [1418, 'between April 1954 and January 1955'],
    [333, 'Los Angeles Times, 2 January 1977'],
    [16356, 'leasing the property of George Babcock Smith'],
    [16356, 'recorded on 21 October 1933'],
    [291, '48,611.88 acres'],
    [869, 'the Southern Hotel burned down October 23, 1888'],
    [3151, 'HOTEL BURNS DOWN 10-23-1888'],
    [1440, 'Gelcich married María Petra Celestina Pico y Bernal, a niece of Andres Pico'],
    [1440, 'daughter to Antonio Maria Pico'],
    [16356, 'the cast lists for Rags (1915) in the AFI Catalog do not include Hart'],
    [16356, 'buried at Oakwood Cemetery in Chatsworth'],
    [327, 'graduated from the Naval School in 1842'],
    ['lw3491', 'Fort Defiance, New Mexico Territory'],
];
/* Notes put on on 2 October, by a phrase of each: a target carrying one for the
   same error would be a duplicate. */
$TWO_OCT = ['CE20' => '3 September 1851', 'CE22' => 'Fort Defiance was in New Mexico Territory'];

/* The wording rules: check_note_wording.php's own pattern, read from it. */
$cnw = (string)file_get_contents("$root/scripts/import/check_note_wording.php");
$BAD = preg_match("~\\\$BAD = '((?:[^'\\\\]|\\\\.)*)';~", $cnw, $m) ? str_replace("\\'", "'", $m[1]) : null;
$bad = [];
if (!$BAD || @preg_match($BAD, '') === false) { $bad[] = 'cannot read the wording pattern from check_note_wording.php'; }

$recText = function (Entry $e) use ($ws) {
    $t = $e->title;
    foreach ($e->getFieldLayout()->getCustomFields() as $f) {
        if (in_array($f->handle, ['editorNotes', 'legacyHtml'], true)) { continue; }
        try { $v = $e->getFieldValue($f->handle); } catch (\Throwable $x) { continue; }
        if (is_array($v)) { array_walk_recursive($v, function ($x) use (&$t) { if (is_scalar($x)) { $t .= ' ' . $x; } }); }
        elseif (is_scalar($v) || (is_object($v) && method_exists($v, '__toString'))) { $t .= ' ' . $v; }
    }
    foreach ($e->editorNotes ?? [] as $r) { if (is_array($r)) { $t .= ' ' . ($r['note'] ?? ''); } }
    return $ws($t);
};
foreach ($EVIDENCE as [$key, $ph]) {
    $r = is_int($key) ? Entry::find()->id($key)->status(null)->one() : Entry::find()->status(null)->legacyKey($key)->one();
    if (!$r || !str_contains($recText($r), $ws($ph))) { $bad[] = "#$key does not read \"$ph\""; }
}
foreach ($FIELD_EVIDENCE as [$id, $fld, $want]) {
    $r = Entry::find()->id($id)->status(null)->one();
    if (!$r || !str_contains((string)$r->getFieldValue($fld), $want)) { $bad[] = "#$id $fld does not hold \"$want\""; }
}

$plan = []; $fieldPlan = []; $already = 0;
foreach ($NOTES as [$code, $ids, $phrase, $note]) {
    if ($BAD && preg_match($BAD, preg_replace('~https?://\S+~', ' ', $HEADING . ' ' . $note), $mm)) { $bad[] = "$code note names the machinery: \"{$mm[0]}\""; }
    if (preg_match('~\x{2014}~u', $note)) { $bad[] = "$code note has an em dash"; }
    if (preg_match('~inventory/|\.json|\.php|scripts?/|/Volumes|\bimport~i', $note)) { $bad[] = "$code note names a repository path"; }
    if (str_contains($note, 'Perkins') && str_contains($note, 'Reynolds')) { $bad[] = "$code note cites Perkins and Reynolds together (one source under the Perkins rule)"; }
    foreach ($ids as $id) {
        $e = Entry::find()->id($id)->status(null)->one();
        if (!$e) { $bad[] = "$code #$id missing"; continue; }
        if (!$e->getFieldLayout()->getFieldByHandle('editorNotes')) { $bad[] = "$code #$id has no editorNotes field"; continue; }
        if (!str_contains($recText($e), $ws($phrase))) { $bad[] = "$code #$id does not carry \"" . mb_substr($phrase, 0, 50) . '"'; continue; }
        $rows = array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '');
        foreach (explode(' ', $code) as $c) {
            if (isset($TWO_OCT[$c]) && array_filter($rows, fn($r) => str_contains((string)$r['note'], $TWO_OCT[$c]))) { $bad[] = "$code #$id already has the 2 October note"; }
        }
        if (array_filter($rows, fn($r) => trim((string)$r['note']) === $note)) { $already++; echo str_pad($code, 10) . "#$id already noted" . PHP_EOL; continue; }
        $plan[$id][] = $note;
        echo str_pad($code, 10) . "#$id \"" . mb_substr($e->title, 0, 50) . '": ' . mb_substr($note, 0, 110) . '...' . PHP_EOL;
    }
}
foreach ($FIELDS as [$code, $id, $fld, $wrong, $right]) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || !$e->getFieldLayout()->getFieldByHandle($fld)) { $bad[] = "$code #$id has no $fld"; continue; }
    $now = (string)$e->getFieldValue($fld);
    if ($now === $right) { $already++; echo str_pad($code, 10) . "#$id $fld already \"$right\"" . PHP_EOL; continue; }
    if ($now !== $wrong) { $bad[] = "$code #$id $fld is \"$now\", not \"$wrong\""; continue; }
    $other = Entry::find()->status(null)->photoSourceCode($right)->ids();
    if ($other) { $bad[] = "$code $right is already the code of #" . implode(', #', $other); continue; }
    $f = $e->getFieldLayout()->getFieldByHandle($fld);
    if (!empty($f->charLimit) && mb_strlen($right) > $f->charLimit) { $bad[] = "$code #$id $fld over {$f->charLimit}"; }
    $fieldPlan[$id][$fld] = $right;
    echo str_pad($code, 10) . "#$id \"" . mb_substr($e->title, 0, 50) . "\": $fld \"$wrong\" -> \"$right\"" . PHP_EOL;
}
/* Provenance, where the record has the field. */
$provPlan = [];
foreach (array_unique(array_merge(array_keys($plan), array_keys($fieldPlan))) as $id) {
    $e = Entry::find()->id($id)->status(null)->one();
    $pf = $e->getFieldLayout()->getFieldByHandle('recordProvenance');
    if (!$pf) { continue; }
    $p = trim((string)$e->recordProvenance);
    if (str_contains($p, 'fix_live_errors_consolidated.php')) { continue; }
    $new = $p === '' ? $PROV : $p . '; ' . $PROV;
    if (mb_strlen($new) > ($pf->charLimit ?: 255)) { $bad[] = "#$id recordProvenance would be " . mb_strlen($new) . ' characters'; continue; }
    $provPlan[$id] = $new;
    echo str_pad('', 10) . "#$id recordProvenance: \"$new\" (" . mb_strlen($new) . ')' . PHP_EOL;
}

$nNotes = array_sum(array_map('count', $plan));
echo str_repeat('-', 78) . PHP_EOL . "$nNotes notes on " . count($plan) . ' records; ' . count($fieldPlan) . ' field fixes; ' . count($provPlan) . " provenance lines; $already already done" . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if (!$plan && !$fieldPlan) { echo 'nothing to do' . PHP_EOL; return; }

$short = []; $done = 0;
$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach (array_unique(array_merge(array_keys($plan), array_keys($fieldPlan))) as $id) {
        $e = Entry::find()->id($id)->status(null)->one();
        $vals = $fieldPlan[$id] ?? [];
        if (isset($plan[$id])) {
            $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
            foreach ($plan[$id] as $n) { $rows[] = ['heading' => $HEADING, 'position' => 'bottom', 'note' => $n]; }
            $vals['editorNotes'] = $rows;
        }
        if (isset($provPlan[$id])) { $vals['recordProvenance'] = $provPlan[$id]; }
        $e->setFieldValues($vals);
        if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id save: " . implode(', ', $e->getFirstErrors()); continue; }
        $b = Entry::find()->id($id)->status(null)->one();
        foreach ($plan[$id] ?? [] as $n) { if (!in_array($n, array_column($b->editorNotes ?? [], 'note'), true)) { $short[] = "#$id note"; } }
        foreach ($fieldPlan[$id] ?? [] as $fld => $v) { if ((string)$b->getFieldValue($fld) !== $v) { $short[] = "#$id $fld"; } }
        $done++;
    }
    if ($short) { throw new \RuntimeException(implode(', ', $short)); }
    $tx->commit();
} catch (\Throwable $x) {
    $tx->rollBack();
    echo 'ROLLED BACK: ' . $x->getMessage() . PHP_EOL;
    $applyLog = require "$root/scripts/import/_apply_log.php";
    $applyLog('fix_live_errors_consolidated.php', 0, 'SHORT', 'rolled back: ' . $x->getMessage());
    return;
}
echo "READ-BACK OK: $done records" . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_live_errors_consolidated.php', $done, 'verified', "the consolidated live errors in Craft: $nNotes correction notes, " . count($fieldPlan) . ' photoSourceCode fixes; text not rewritten');
