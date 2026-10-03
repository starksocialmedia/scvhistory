/**
 * Notes that narrate how the research was done, reworded to say what the
 * source holds (Nathan, 3 October 2026: "fix Nadeau's footnotes that describe
 * how the research was done, and check every footnote for the same, since
 * that is the public-wording problem again in a place the earlier sweep did
 * not look").
 *
 * "read as a search summary (the page was blocked)" becomes "known here only
 * from a summary"; "Searched 2 October 2026 and not found: X" becomes "He does
 * not appear in X"; "has not been read / checked" becomes what the reader can
 * weigh ("a secondary source", "not yet confirmed in"). The 20 notes, old and
 * new, are in research-wording-2026-10-04.json; each is matched by its old
 * text, so a note already reworded or since edited is left alone and reported.
 * check_note_wording.php now matches this wording too.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_research_wording.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$ROWS = json_decode(file_get_contents("$root/scripts/import/research-wording-2026-10-04.json"), true);
$by = []; foreach ($ROWS as $r) { $by[$r['id'] . '|' . $r['field']][] = $r; }
$plan = []; $held = 0; $miss = [];
foreach ($by as $k => $rs) {
    [$id, $fld] = explode('|', $k);
    $e = Entry::find()->id((int)$id)->status(null)->one();
    if (!$e) { $miss[] = "#$id gone"; continue; }
    $val = array_values(array_filter($e->getFieldValue($fld) ?? [], 'is_array')); $chg = false;
    foreach ($rs as $r) {
        $hit = false;
        foreach ($val as $i => $row) {
            if (($row['note'] ?? '') === $r['new']) { $held++; $hit = true; break; }
            if (($row['note'] ?? '') === $r['old']) { $val[$i]['note'] = $r['new']; $chg = $hit = true; echo "   #$id {$e->title} ($fld)" . PHP_EOL; break; }
        }
        if (!$hit) { $miss[] = "#$id {$e->title} ($fld): old text not found"; }
    }
    if ($chg) { $plan[] = [$e, $fld, $val]; }
}
echo 'entries to save: ' . count($plan) . '; notes already reworded: ' . $held . PHP_EOL . 'NOT FOUND: ' . ($miss ? implode('; ', $miss) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$els = Craft::$app->getElements(); $short = [];
foreach ($plan as [$e, $fld, $val]) {
    $keep = $fld === 'footnotes' ? ['number', 'note', 'source'] : ['heading', 'position', 'note'];
    $e->setFieldValue($fld, array_map(fn($r) => array_intersect_key($r, array_flip($keep)), $val));
    if (!$els->saveElement($e)) { $short[] = "#{$e->id} " . json_encode($e->getFirstErrors()); }
}
$ok = 0; foreach ($ROWS as $r) { $e = Entry::find()->id($r['id'])->status(null)->one(); foreach ($e->getFieldValue($r['field']) ?? [] as $row) { if (is_array($row) && ($row['note'] ?? '') === $r['new']) { $ok++; break; } } }
echo "READ-BACK $ok of " . count($ROWS) . ' notes carry the new wording' . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_research_wording.php', $ok, ($ok === count($ROWS) && !$short) ? 'verified' : 'SHORT', 'notes that narrated the research reworded to say what the source holds');
if ($short) { throw new \RuntimeException('fix_research_wording: ' . implode(', ', $short)); }
