/**
 * Connect articles and obituaries to the person who wrote them (Nathan, 5 October 2026: "go through every article,
 * obituary and document and connect its author ... A reader should be able to see everything a person wrote here, and a
 * researcher should be able to cite it."). The links that need no schema change: writtenBy on articles and obituaries.
 *
 * Reads inventory/review/author-links-2026-10-05.json, built from the authorship census
 * (inventory/review/authorship-census-2026-10-05.json) and checked against the database and the mirror the same day.
 * Each row is one piece whose own page (or, where the page is not on the mirror, the byline block kept in its body)
 * prints the person's name as author, and the person already has a record. Collection authorship is not inherited;
 * no person record is created; documents are not touched (they have no writtenBy; listed in the data file for Nathan).
 *
 * Idempotent: never overwrites an existing writtenBy. A piece that already has the same author is "done"; one that has a
 * different author is reported as a conflict and left alone. Nothing is saved when there is nothing to set.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/link_authors_2026_10_05.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$root = \Craft::getAlias('@root');
$data = json_decode(file_get_contents($root . '/inventory/review/author-links-2026-10-05.json'), true);
if (!$data || !isset($data['links'])) { throw new \RuntimeException('author-links-2026-10-05.json missing or unreadable'); }

$els = Craft::$app->getElements();
$count = ['set' => 0, 'done' => 0, 'conflict' => 0, 'refused' => 0];
$byAuthor = [];
$written = 0;

foreach ($data['links'] as $row) {
    $id = (int)$row['entryId']; $pid = (int)$row['personId'];
    $tag = str_pad('#' . $id, 8) . mb_substr($row['title'], 0, 50);

    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    if (!$e) { echo "$tag  REFUSED: entry not found" . PHP_EOL; $count['refused']++; continue; }
    if (!in_array($e->section->handle, ['articles', 'obituaries'], true)) { echo "$tag  REFUSED: section {$e->section->handle} is out of scope" . PHP_EOL; $count['refused']++; continue; }
    $has = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
    if (!isset($has['writtenBy'])) { echo "$tag  REFUSED: entry type has no writtenBy" . PHP_EOL; $count['refused']++; continue; }

    $p = \craft\elements\Entry::find()->id($pid)->section('persons')->status(null)->one();
    if (!$p) { echo "$tag  REFUSED: person #$pid not found in persons" . PHP_EOL; $count['refused']++; continue; }
    if ($p->title !== $row['personTitle']) { echo "$tag  REFUSED: person #$pid is now \"{$p->title}\", the data file says \"{$row['personTitle']}\"" . PHP_EOL; $count['refused']++; continue; }

    try { $current = $e->getFieldValue('writtenBy')->status(null)->ids(); }
    catch (\Throwable $t) { echo "$tag  REFUSED: writtenBy unreadable (" . $t->getMessage() . ')' . PHP_EOL; $count['refused']++; continue; }

    if ($current) {
        if ($current === [$pid]) { $count['done']++; continue; }
        echo "$tag  CONFLICT: already written by #" . implode(', #', $current) . "; the byline says {$p->title} (#$pid). Left alone." . PHP_EOL;
        $count['conflict']++; continue;
    }

    echo "$tag  writtenBy = {$p->title} (#$pid)  byline \"{$row['bylinePrinted']}\"  read from {$row['readFrom']}" . PHP_EOL;
    if (!empty($row['note'])) { echo '          note: ' . $row['note'] . PHP_EOL; }
    $count['set']++; $byAuthor[$p->title] = ($byAuthor[$p->title] ?? 0) + 1;

    if (!$APPLY) { continue; }
    $e->setFieldValue('writtenBy', [$pid]);
    if (!$els->saveElement($e)) { echo '          SAVE FAILED ' . json_encode($e->getErrors()) . PHP_EOL; continue; }
    $back = \craft\elements\Entry::find()->id($id)->status(null)->one()->getFieldValue('writtenBy')->status(null)->ids();
    if ($back === [$pid]) { $written++; } else { echo '          READ-BACK MISMATCH ' . json_encode($back) . PHP_EOL; }
}

echo PHP_EOL . 'By author (would set):' . PHP_EOL;
arsort($byAuthor);
foreach ($byAuthor as $name => $n) { echo '  ' . str_pad($name, 30) . $n . PHP_EOL; }
echo PHP_EOL . "Rows in data file: " . count($data['links']) . PHP_EOL;
echo "  " . ($APPLY ? 'set' : 'would set') . ": {$count['set']}" . PHP_EOL;
echo "  already done (same author): {$count['done']}" . PHP_EOL;
echo "  conflict (different author, left alone): {$count['conflict']}" . PHP_EOL;
echo "  refused at run time: {$count['refused']}" . PHP_EOL;
echo 'Not in this script, listed in the data file: ' . count($data['refused'] ?? []) . ' refused, '
    . count($data['documentsAwaitingField'] ?? []) . ' documents awaiting a writtenBy field, '
    . count($data['collectionsNotInThisStep'] ?? []) . ' collections.' . PHP_EOL;

if ($APPLY && $written) {
    $applyLog = require $root . '/scripts/import/_apply_log.php';
    $applyLog('link_authors_2026_10_05.php', $written, $written === $count['set'] ? 'verified' : "verified $written of {$count['set']}", 'writtenBy from printed bylines, existing persons only');
}
echo ($APPLY ? "done: $written saved and read back." : 'Nothing written.') . ' A second run sets nothing: rows already linked to the same person are skipped without a save.' . PHP_EOL;
