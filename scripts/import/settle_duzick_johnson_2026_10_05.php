/**
 * Sharlene Duzick is Sharlene Rose Johnson (Nathan, 5 October 2026: "Tell me what would settle it ... If one source would do
 * it, that is worth a search rather than leaving her as two unlinked candidacies"). Settled by two sources that each give both
 * names for one person: the California Department of Real Estate's license record 01938103 ("Johnson, Sharlene Rose";
 * "Former Name(s): Duzick, Sharlene Rose"), and The Signal of August 5, 2022, announcing "Sharlene Johnson" for Saugus Union
 * seat No. 5 against Christopher Trunkey, whose one challenger on the County's returns was "Sharlene Rose Duzick". Saved in
 * inventory/news/johnson-duzick-2026-10-05/ (manifest.json); the research is inventory/review/johnson-duzick-2026-10-05.md.
 * The archive gives no reason for the change of name.
 * Links candidacies #25725 (2018) and #25755 (2022) to her record (#30263) with her candidateKey; replaces the "probably"
 * notes of note_duzick_johnson_2026_10_05.php, which also misdated her trusteeship to 2022 (it began in December 2024), with
 * a note that states the identification and its sources; takes the "(?)" off her Duzick aliases. Her profile draft (batch b)
 * carries the Saugus runs and the same sources. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/settle_duzick_johnson_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$p = Entry::find()->section('persons')->id(30263)->status(null)->one();
$key = (string)(Entry::find()->section('candidacies')->id(30084)->status(null)->one()->candidateKey ?? '');
$NOTE = 'Sharlene Rose Duzick is Sharlene Rose Johnson, under her former name; she was elected a trustee of the Santa Clarita Community College District in November 2024. The California Department of Real Estate\'s license record for her gives "Duzick, Sharlene Rose" as her former name (license 01938103, read October 5, 2026), and The Signal of August 5, 2022 announced "Sharlene Johnson" as the challenger for seat No. 5, the race in which the County\'s returns name Sharlene Rose Duzick.';
foreach ([25725, 25755] as $id) {
  $c = Entry::find()->section('candidacies')->id($id)->status(null)->one();
  $rows = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($c->editorNotes ?? [])),
    fn($r) => $r['note'] !== '' && $r['heading'] !== 'Probably Sharlene Rose Johnson'));
  $linked = (int)($c->candidacyPerson->status(null)->one()?->id ?? 0) === $p->id; $noted = in_array($NOTE, array_column($rows, 'note'), true) && count($rows) === count(array_filter(iterator_to_array($c->editorNotes ?? []), fn($r) => (string)$r['note'] !== ''));
  echo "#$id {$c->nameAsPrinted}: " . ($linked ? 'linked' : "link to #{$p->id}") . ', ' . ($noted ? 'noted' : 'replace the note') . ", key {$c->candidateKey} -> $key\n";
  if (!$APPLY || ($linked && $noted)) { continue; }
  if (!in_array($NOTE, array_column($rows, 'note'), true)) { $rows[] = ['heading' => 'Her name', 'note' => $NOTE, 'position' => 'bottom']; }
  $c->setFieldValues(['candidacyPerson' => [$p->id], 'editorNotes' => $rows] + ($key !== '' ? ['candidateKey' => $key] : []));
  if (!$el->saveElement($c)) { throw new \RuntimeException("#$id " . json_encode($c->getFirstErrors())); } $n++;
}
$al = (string)$p->personAliases; $al2 = str_replace(' (?)', '', $al);
echo "#30263 aliases: " . ($al === $al2 ? 'clean' : json_encode($al2)) . "\n";
if ($APPLY && $al !== $al2) { $p->setFieldValue('personAliases', $al2); if (!$el->saveElement($p)) { throw new \RuntimeException('#30263'); } $n++; }
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('settle_duzick_johnson_2026_10_05.php', $n, 'verified', 'Duzick candidacies linked to Sharlene Rose Johnson'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
