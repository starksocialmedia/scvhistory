/**
 * #18869 becomes "Remi Nadeau (grandson)" (Nathan, 29 September 2026), so the
 * archive no longer holds two records titled Remi Nadeau, with an editor note
 * saying why. Retitle it properly when a source gives his middle name or dates.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retitle_nadeau_grandson.php'))"
 */
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$ID = 18869; $TITLE = 'Remi Nadeau (grandson)';
$NOTE = ['heading' => 'Two men named Remi Nadeau', 'position' => 'bottom',
    'note' => 'This is the grandson of the Los Angeles freighter Remi Allen Nadeau (1821-1887, record #339). The grandson owned the Soledad Canyon ranch and the deer park photographed about 1929 (records #2155, #3695). Accounts of the valley, Jerry Reynolds\' among them, often run the two men together; the records that mean the freighter now point at #339. The grandson\'s full name and his dates are not established: this record will be retitled when a source gives them.'];
$e = \craft\elements\Entry::find()->id($ID)->status(null)->one();
if (!$e || !in_array($e->title, ['Remi Nadeau', $TITLE], true)) { echo "REFUSING: #$ID is not the grandson record" . PHP_EOL; return; }
$notes = array_values(array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
$hasNote = (bool)array_filter($notes, fn($r) => ($r['heading'] ?? '') === $NOTE['heading']);
if ($e->title === $TITLE && $hasNote) { echo 'already done' . PHP_EOL; return; }
echo "#$ID \"{$e->title}\" -> \"$TITLE\"" . ($hasNote ? '' : '; editor note: ' . $NOTE['note']) . PHP_EOL;
$others = \craft\elements\Entry::find()->section('persons')->status(null)->title('Remi Nadeau*')->all();
echo 'records titled Remi Nadeau...: ' . implode(', ', array_map(fn($x) => '#' . $x->id . ' ' . $x->title, $others)) . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$e->title = $TITLE;
if (!$hasNote) { $notes[] = $NOTE; $e->setFieldValue('editorNotes', $notes); }
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('retitle_nadeau_grandson: save failed'); }
$b = \craft\elements\Entry::find()->id($ID)->status(null)->one();
$ok = $b->title === $TITLE && array_filter($b->editorNotes ?? [], fn($r) => ($r['heading'] ?? '') === $NOTE['heading']);
echo 'READ-BACK ' . ($ok ? 'OK; page: ' . $b->url : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('retitle_nadeau_grandson.php', 1, $ok ? 'verified' : 'SHORT', 'Remi Nadeau (grandson), with the conflation note');
