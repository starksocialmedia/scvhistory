/**
 * Correcting notes beside Leon's caption errors, from
 * inventory/legacy/link-fixes/corrections.json (the "captions" list).
 *
 * Nathan, 28 September 2026: a correcting note, not a rewrite. Leon's words
 * stay exactly as printed and the correction sits beside them, the Cephas Bard
 * precedent (source_corrections.php): the error stays visible, and so does the
 * fact.
 *
 * Only pages already in Craft are touched. A correction for a page not yet
 * imported (lw2320 today) is listed as waiting; the importer applies it when
 * the page comes in.
 *
 * The note goes in editorNotes, position bottom, headed "Correction". It is
 * added once: a page that already carries a note with the same heading and
 * text is left alone.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_caption_corrections.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$SRC = \Craft::getAlias('@root') . '/inventory/legacy/link-fixes/corrections.json';
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$doc = json_decode((string)file_get_contents($SRC), true);
if (!$doc) { echo 'cannot read ' . $SRC . PHP_EOL; return; }
$elements = Craft::$app->getElements();

$ops = [];
foreach ($doc['captions'] as $c) {
    $key = $c['page']['legacy_key'];
    $craft = $c['page']['craft'];
    if (!$craft) { echo 'waiting  ' . str_pad($key, 8) . ' not in Craft; the importer applies it' . PHP_EOL; continue; }
    $e = \craft\elements\Entry::find()->id($craft['id'])->status(null)->one();
    if (!$e || !$e->getFieldLayout()->getFieldByHandle('editorNotes')) { echo 'REFUSE   ' . $key . ': record or editorNotes missing' . PHP_EOL; continue; }
    if (!str_contains((string)$e->body, $c['text_as_printed'])) {
        echo 'REFUSE   ' . $key . ' #' . $e->id . ': the body no longer reads "' . $c['text_as_printed'] . '"' . PHP_EOL; continue;
    }
    $note = 'The caption reads "' . $c['text_as_printed'] . '". ' . $c['correction'];
    $rows = array_values(array_filter((array)$e->editorNotes, fn($r) => trim((string)($r['note'] ?? $r['col2'] ?? '')) !== ''));
    if (array_filter($rows, fn($r) => trim((string)($r['note'] ?? $r['col2'] ?? '')) === $note)) { echo 'skip     ' . $key . ' #' . $e->id . ': already carries the note' . PHP_EOL; continue; }
    echo 'add      ' . $key . ' #' . $e->id . ' "' . $e->title . '"' . PHP_EOL . '         Correction: ' . $note . PHP_EOL;
    $ops[] = [$e->id, $note];
}

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$short = [];
foreach ($ops as [$id, $note]) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    $rows = array_values(array_filter((array)$e->editorNotes, fn($r) => trim((string)($r['note'] ?? $r['col2'] ?? '')) !== ''));
    $rows = array_map(fn($r) => ['heading' => $r['heading'] ?? $r['col1'] ?? '', 'note' => $r['note'] ?? $r['col2'] ?? '', 'position' => $r['position'] ?? $r['col3'] ?? 'bottom'], $rows);
    $rows[] = ['heading' => 'Correction', 'note' => $note, 'position' => 'bottom'];
    $e->setFieldValue('editorNotes', $rows);
    if (!$elements->saveElement($e)) { $short[] = "#$id: save failed " . json_encode($e->getFirstErrors()); continue; }
    $back = \craft\elements\Entry::find()->id($id)->status(null)->one();
    $has = array_filter((array)$back->editorNotes, fn($r) => trim((string)($r['note'] ?? '')) === $note);
    if (!$has) { $short[] = "#$id: the note does not read back"; }
    if (!str_contains((string)$back->body, 'Beery')) { $short[] = "#$id: the body changed"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT' : 'OK') . ': ' . (count($ops) - count($short)) . ' of ' . count($ops) . PHP_EOL;
foreach ($short as $s) { echo '   FAIL ' . $s . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_caption_corrections.php', count($ops), $short ? 'SHORT: ' . implode('; ', $short) : 'verified ' . count($ops) . ' of ' . count($ops) . '; captions unchanged',
    'correcting notes from link-fixes/corrections.json; lw2320 waits for its import');
if ($short) { throw new \RuntimeException('add_caption_corrections: ' . implode('; ', $short)); }
