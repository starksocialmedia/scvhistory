/**
 * The disabled organization records behind the new /organizations groups (Nathan, 4 October 2026: "Porta Bella: drop
 * it ... Retire it and list it in the removed-records registry"; "enable the missions, ranchos and businesses so their
 * groups appear ... Any that would be a bare name on a page should be filled first or left disabled with a note").
 *
 *  1. Enabled, each with what it holds: Rancho San Francisco (#382) and Rancho El Tejon (#388), whose text predates
 *     the archive's sourcing and says so in a note; the three missions (#386 San Gabriel, #398 San Francisco de Asis,
 *     #400 Santa Cruz), whose one tie to the valley, Ygnacio del Valle's part in their secularization in 1834, is
 *     given with its two sources: Pen Pictures from the Garden of the World (1889) names Santa Cruz and Dolores, and
 *     Jerry Reynolds names San Gabriel, Dolores and Santa Cruz.
 *  2. Retired: the five organization records created from the review of 21 September and converted to place records
 *     on 25 September by convert_orgs_to_places.php, which left the organization behind, empty and disabled: Porta
 *     Bella (#18599, place #20152), Acton Hotel (#18876, place #20109), Southern Hotel (#16260, place #20118), Valencia
 *     Marketplace (#16425, place #20170), Felton School (#18827, place #2540). Each is soft-deleted, listed under
 *     removedRecords by slug, and its review decision is marked merged into the place, so no rerun recreates it.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/enable_ranchos_missions_retire_shells_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$UNSOURCED = 'This account is not yet sourced: it was written before the archive began citing every claim, and its statements are still to be checked against the records the archive holds.';
$DV = 'Ygnacio del Valle, later the owner of Rancho San Francisco in this valley, took part in the secularization of the missions in 1834. Pen Pictures from the Garden of the World (Lewis Publishing, 1889), pages 442 to 443, names him secularization commissioner for Missions Santa Cruz and Dolores; Jerry Reynolds, History of the Santa Clarita Valley, chapter 15, "Family Squabbles," names Missions San Gabriel, San Francisco Dolores and Santa Cruz.';
$ENABLE = [382 => ['Rancho San Francisco', $UNSOURCED], 388 => ['Rancho El Tejon', $UNSOURCED], 386 => ['Mission San Gabriel Arcángel', null], 398 => ['Mission San Francisco de Asís', null], 400 => ['Mission Santa Cruz', null]];
$SHELL = [18599 => ['Porta Bella', 20152], 18876 => ['Acton Hotel', 20109], 16260 => ['Southern Hotel', 20118], 16425 => ['Valencia Marketplace', 20170], 18827 => ['Felton School', 2540]];
$bad = [];
foreach ($ENABLE as $id => [$t]) { $e = Entry::find()->id($id)->status(null)->one(); if ($e?->title !== $t) { $bad[] = "#$id is not $t"; } else { echo "enable #$id $t (" . $e->getStatus() . ')' . PHP_EOL; } }
foreach ($SHELL as $id => [$t, $pl]) { $e = Entry::find()->id($id)->status(null)->section('organizations')->one(); $p = Entry::find()->id($pl)->status(null)->section('places')->one();
    if ($e && (trim(strip_tags((string)$e->body)) !== '' || Entry::find()->relatedTo($e)->status(null)->exists())) { $bad[] = "#$id $t is not empty"; }
    if ($p?->title !== $t) { $bad[] = "place #$pl is not $t"; }
    echo "retire org #$id $t" . ($e ? '' : ' (already retired)') . "; the place #$pl stays" . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
foreach ($ENABLE as $id => [$t, $note]) {
    $e = Entry::find()->id($id)->status(null)->one(); $chg = false;
    if (!$e->enabled) { $e->enabled = true; $chg = true; }
    if ($note) { $rows = array_values(array_filter($e->editorNotes ?? [], fn($r) => trim((string)($r['note'] ?? '')) !== '')); if (!in_array($note, array_column($rows, 'note'), true)) { $rows[] = ['heading' => 'Not yet sourced', 'position' => 'top', 'note' => $note]; $e->setFieldValue('editorNotes', $rows); $chg = true; } }
    else { $rows = array_values(array_filter($e->footnotes ?? [], fn($r) => trim((string)($r['note'] ?? '')) !== '')); if (!in_array($DV, array_column($rows, 'note'), true)) { $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $DV, 'source' => 'editorial-2026']; $e->setFieldValue('footnotes', $rows);
        $en = array_values(array_filter($e->editorNotes ?? [], fn($r) => trim((string)($r['note'] ?? '')) !== '')); $en[] = ['heading' => 'Why it is here', 'position' => 'top', 'note' => 'Its tie to this valley is Ygnacio del Valle, who took part in its secularization in 1834 and later held Rancho San Francisco; the sources are in the note below. The rest of the account is not yet sourced.']; $e->setFieldValue('editorNotes', $en); $chg = true; } }
    if ($chg) { if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
$regFile = "$root/scripts/import/removed-claims.json"; $reg = json_decode(file_get_contents($regFile), true);
$decFile = "$root/review/records-decided.json"; $dec = json_decode(file_get_contents($decFile), true);
foreach ($SHELL as $id => [$t, $pl]) {
    $e = Entry::find()->id($id)->status(null)->section('organizations')->one();
    $slug = $e?->slug ?? \craft\helpers\StringHelper::slugify($t);
    if ($e) { if (!$el->deleteElement($e)) { throw new \RuntimeException("retire #$id"); } $n++; }
    if (!in_array($id, array_column(array_filter($reg['removedRecords'], fn($r) => $r['section'] === 'organizations'), 'record'))) {
        $reg['removedRecords'][] = ['record' => $id, 'title' => $t, 'slug' => $slug, 'section' => 'organizations',
            'why' => $t === 'Porta Bella' ? 'An empty shell: no text, no type, no sources and no links, created from the review of 21 September and left behind when it was converted to place record #20152 on 25 September. Porta Bella was the development planned for the Whittaker-Bermite site; the place record is where it belongs.' : "An empty shell left behind when the record was converted to place record #$pl on 25 September (convert_orgs_to_places.php). The place is the record.",
            'removed' => '2026-10-04', 'by' => 'scripts/import/enable_ranchos_missions_retire_shells_2026_10_04.php; the review decision is marked merged into the place'];
    }
    foreach ($dec['decisions'] as &$r) { if (($r['type'] ?? '') === 'organization' && ($r['name'] ?? '') === $t && ($r['action'] ?? '') === 'approved') { $r['action'] = 'merged'; $r['into'] = $pl; $r['mergedNote'] = 'converted to a place record on 25 September 2026; the organization shell retired 4 October 2026'; } } unset($r);
}
file_put_contents($regFile, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
file_put_contents($decFile, json_encode($dec, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('enable_ranchos_missions_retire_shells_2026_10_04.php', $n, 'verified', 'Two ranchos and three missions enabled with notes; five empty organization shells retired (Porta Bella and four converted to places), listed in removedRecords');
echo "done: $n writes" . PHP_EOL;
