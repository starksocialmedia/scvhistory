/**
 * Authors on documents, and the Ellis bylines cited (Nathan, 6 October 2026: "add writtenBy to documents. Apply the Ellis link,
 * with the two SCVNews pieces cited").
 * - The five documents whose own page prints a byline naming someone with a record take writtenBy (from
 *   inventory/review/author-links-2026-10-05.json): Connie Worden-Roberts's three (#28281 "By Connie Worden-Roberts."; #28285
 *   "Respectfully submitted, Connie Worden-Roberts"; #28287 her 2007 memoir), Perkins's 1957 study (#27374) and Scofield's eulogy
 *   (#20104). The two letters whose writer is named only in a title or note (John Lang's, #28057; Abel Stearns's, #26983) wait
 *   on Nathan; so do the bylines naming people with no record (Suomisto, Perry Smith) and #28295 (titled Lutz, printed Kay).
 * - The two Gazette pieces by "PHILIP ELLIS, Chairman, Newhall Redevelopment Committee" (#12645, #12613), linked to Philip Ellis Jr.
 *   (#28364) by link_authors_2026_10_05.php, get a bottom editor's note citing what joins the byline to him: SCVNews.com, "City
 *   Declares Newhall Redevelopment Committee Dissolved," March 1, 2012, read through the Wayback Machine (the site refuses direct
 *   requests). The second piece the census named (February 5, 2016) could not be found; the note cites the one in hand.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/document_authors_and_ellis_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$DOCS = [28281 => 16418, 28285 => 16418, 28287 => 16418, 27374 => 333, 20104 => 21584];
foreach ($DOCS as $id => $pid) {
  $d = Entry::find()->section('documents')->id($id)->status(null)->one(); $p = Entry::find()->id($pid)->status(null)->one();
  if (!$d || !$p) { throw new \RuntimeException("#$id or #$pid missing"); }
  $cur = $d->writtenBy->status(null)->ids();
  echo "#$id {$d->title}: " . ($cur === [$pid] ? 'linked already' : ($cur ? 'has another author, left alone' : "writtenBy {$p->title}")) . PHP_EOL;
  if ($APPLY && !$cur) { $d->setFieldValue('writtenBy', [$pid]); if (!$el->saveElement($d)) { throw new \RuntimeException("#$id"); } $n++; }
}
$NOTE = 'The byline prints "PHILIP ELLIS, Chairman, Newhall Redevelopment Committee." That chairman is the Philip Ellis of the Newhall School District board: "The committee has been chaired in recent years by Phil Ellis, who also serves on the Newhall School Board" (SCVNews.com, "City Declares Newhall Redevelopment Committee Dissolved," March 1, 2012, https://scvnews.com/city-declares-newhall-redevelopment-committee-dissolved/, read through the Wayback Machine capture of November 6, 2024).';
foreach ([12645, 12613] as $id) {
  $a = Entry::find()->section('articles')->id($id)->status(null)->one();
  if ($a?->writtenBy->status(null)->ids() !== [28364]) { throw new \RuntimeException("#$id is not linked to Philip Ellis Jr."); }
  $rows = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($a->editorNotes ?? [])), fn($r) => $r['note'] !== ''));
  if (in_array($NOTE, array_column($rows, 'note'), true)) { echo "#$id: noted already\n"; continue; }
  echo "#$id {$a->title}: author note added\n";
  if ($APPLY) { $rows[] = ['heading' => 'The author', 'note' => $NOTE, 'position' => 'bottom']; $a->setFieldValue('editorNotes', $rows); if (!$el->saveElement($a)) { throw new \RuntimeException("#$id"); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('document_authors_and_ellis_2026_10_06.php', $n, 'verified', 'five documents\' authors; the Ellis bylines cited'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
