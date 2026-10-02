/**
 * Two records with source labels pasted into their text, and SCV Water's
 * founding date (2 October 2026).
 *
 * LABELS. Wilk's body had a chatbot's source labels pasted inline ("...38th
 * District. Wikipedia. He served"); Nathan had them removed and every living
 * person checked. A sweep of every body not migrated from the legacy site (the
 * legacy text is Leon's and is not touched) found the same on two more:
 *   #402 Santa Clarita Valley Water   "Yourscvwater" (6), "LinkedIn" (2)
 *   #934 Newhall Pass interchange     "Wikipedia" (4)
 * A label is removed where it stands between sentences, ends the text, or (one
 * case) sits mid-sentence before a lowercase word; the dry run prints every
 * sentence before and after.
 *
 * FOUNDED. SCV Water's dateFounded read "1913", the year its oldest
 * predecessor, the Newhall Water System, began. SCV Water itself was created on
 * 1 January 2018 by Senate Bill 634 (its own history and "Who We Are", inventory/
 * sources/scv-water-history-2026-10-02.json; the Newhall County Water District
 * record's notes). The field is corrected, with a Correction note; the text,
 * which tells the 1913 story, is left as written apart from the labels.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_pasted_labels_and_scv_founded.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$TARGETS = [402 => ['Santa Clarita Valley Water', 'Yourscvwater|LinkedIn'], 934 => ['Newhall Pass interchange', 'Wikipedia']];
$bad = []; $plan = [];
foreach ($TARGETS as $id => [$title, $labels]) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || $e->title !== $title) { $bad[] = "#$id is not $title"; continue; }
    $body = (string)$e->body;
    $re = '~(?<=[.!?"”])[ \t]+(?:' . $labels . ')(?:[ \t]*[.—–-](?=[ \t]|$))?(?=[ \t]+[A-Z\p{Lu}]|[ \t]*$|[ \t]*\n)|[ \t]+(?:' . $labels . ')[ \t]*$~mu';
    $new = preg_replace($re, '', $body, -1, $n);
    /* One label sits mid-sentence ("established in 1913 Yourscvwater by H. Clay Needham"); none of these labels is an ordinary word. */
    $new = preg_replace('~[ \t]+(?:' . $labels . ')(?=[ \t]+[a-z])~u', '', $new, -1, $n2); $n += $n2;
    $left = preg_match_all('~\b(' . $labels . ')\b~', strip_tags($new));
    echo "#$id $title: $n labels removed" . ($left ? ", $left mention(s) left as ordinary words" : '') . PHP_EOL;
    $old = preg_split('~(?<=[.!?])\s+~', preg_replace('~\s+~', ' ', strip_tags($body))); $nw = preg_split('~(?<=[.!?])\s+~', preg_replace('~\s+~', ' ', strip_tags($new)));
    foreach (array_diff($old, $nw) as $s) { echo '   - ' . mb_substr($s, 0, 150) . PHP_EOL; }
    foreach (array_diff($nw, $old) as $s) { echo '   + ' . mb_substr($s, 0, 150) . PHP_EOL; }
    if ($n) { $plan[$id]['body'] = $new; }
}
/* SCV Water's founding date. */
$h = preg_replace('~\s+~u', ' ', json_decode((string)@file_get_contents("$root/inventory/sources/scv-water-history-2026-10-02.json"), true)['sources']['history']['passage'] ?? '');
if (!str_contains($h, 'Formed in 2018 (Senate Bill 634) by an act of the State Legislature')) { $bad[] = 'the SCV Water history does not read "Formed in 2018"'; }
$n2 = Entry::find()->id(27534)->status(null)->one();
if (!str_contains(implode(' ', array_column($n2->footnotes ?? [], 'note')), 'created January 1, 2018 by Senate Bill 634')) { $bad[] = 'the NCWD record no longer quotes "created January 1, 2018"'; }
$scv = Entry::find()->id(402)->status(null)->one();
$NOTE = 'Santa Clarita Valley Water was created on January 1, 2018, by Senate Bill 634, not founded in 1913 as this record gave it. 1913 is when its oldest predecessor, the Newhall Water System, began; SCV Water\'s own history says it was "Formed in 2018 (Senate Bill 634) by an act of the State Legislature" and its "Who We Are" page that it was "created January 1, 2018" (both read 2 October 2026; see Newhall County Water District).';
if (trim((string)$scv->dateFounded) === '1913') {
    $plan[402]['dateFounded'] = 'January 1, 2018'; $plan[402]['dateFoundedEdtf'] = '2018-01-01';
    echo '#402 dateFounded "1913" -> "January 1, 2018", with a Correction note' . PHP_EOL;
} elseif (trim((string)$scv->dateFounded) !== 'January 1, 2018') { $bad[] = '#402 dateFounded is "' . $scv->dateFounded . '"'; }
if (preg_match('~\x{2014}~u', $NOTE)) { $bad[] = 'an em dash in the note'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => $set) {
    $e = Entry::find()->id($id)->status(null)->one();
    if ($id === 402 && isset($set['dateFounded'])) {
        $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
        if (!in_array($NOTE, array_column($rows, 'note'), true)) { $rows[] = ['heading' => 'Correction, 2026', 'position' => 'bottom', 'note' => $NOTE]; }
        $set['editorNotes'] = $rows;
    }
    $e->setFieldValues($set);
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id"; continue; }
    $b = Entry::find()->id($id)->status(null)->one();
    if (isset($set['body']) && (string)$b->body !== $set['body']) { $short[] = "#$id body"; }
    if (isset($set['dateFounded']) && (string)$b->dateFounded !== $set['dateFounded']) { $short[] = "#$id dateFounded"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('fix_pasted_labels_and_scv_founded.php', count($plan), $short ? 'SHORT' : 'verified', 'pasted source labels removed from SCV Water and the Newhall Pass interchange; SCV Water founded January 1, 2018, with a correction note');
if ($short) { throw new \RuntimeException('fix_pasted_labels_and_scv_founded: ' . implode(', ', $short)); }
