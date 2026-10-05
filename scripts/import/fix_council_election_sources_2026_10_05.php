/**
 * Measure U, and the source of the City Council elections (Nathan, 5 October 2026: "Measure U first: 'not carried' on 14,723
 * yes to 6,597 no is the vote that created the City").
 * 1. Measure U, Incorporation of City of Santa Clarita (election #21948, November 3, 1987): carried. import_elections.php
 *    wrote carried = false for every measure (line 199), reading nothing; U is the one measure whose count shows it passed,
 *    14,723 to 6,597, and the City was incorporated on December 15, 1987 (#394). The page prints the counts, not the flag,
 *    so this corrects the data; Measure V (7,905 to 11,166) stays not carried.
 * 2. The fourteen City Council elections of 1987 to 2014 cite their source as the bare key "summary." or "r2014.": the
 *    importer's document lookup returned nothing ($docByKey), so it printed the key, and linked no source document. Each
 *    footnote becomes the document's title and archive number, keeping the reading the extract records, and the document is
 *    added to its sources: #21939 "General Municipal Elections: Historical Election Results, 1987 to 2012" (City of Santa
 *    Clarita), #21933 "2014 Election Results by Precinct".
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_council_election_sources_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$J = json_decode(file_get_contents("$root/inventory/elections/elections.json"), true);
$DOC = ['summary' => Entry::find()->id(21939)->status(null)->one(), 'r2014' => Entry::find()->id(21933)->status(null)->one()];
if ($DOC['summary']?->title !== 'General Municipal Elections: Historical Election Results, 1987 to 2012' || $DOC['r2014']?->title !== '2014 Election Results by Precinct') { throw new \RuntimeException('documents not as expected'); }
$byDate = []; foreach ($J['elections'] as $x) { if (in_array($x['doc'] ?? '', ['summary', 'r2014'], true)) { $byDate[$x['date']] = $x; } }
foreach (Entry::find()->section('elections')->status(null)->all() as $e) {
  $rows = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], iterator_to_array($e->footnotes ?? []));
  $i = array_search(true, array_map(fn($r) => in_array(trim($r['note']), ['summary.', 'r2014.'], true) || preg_match('~^(summary|r2014): ~', trim($r['note'])), $rows), true);
  $mUpd = false; $ms = [];
  if ($e->id === 21948) { foreach ($e->ballotMeasures ?? [] as $m) { $m = ['letter' => $m['letter'], 'subject' => $m['subject'], 'yes' => $m['yes'], 'no' => $m['no'], 'carried' => (bool)$m['carried']]; if ($m['letter'] === 'U' && !$m['carried']) { $m['carried'] = true; $mUpd = true; } $ms[] = $m; } }
  if ($i === false && !$mUpd) { continue; }
  $date = (string)$e->electionDateEdtf; $x = $byDate[$date] ?? null; $key = $x['doc'] ?? (str_starts_with(trim($rows[$i]['note'] ?? ''), 'r2014') ? 'r2014' : 'summary'); $d = $DOC[$key];
  echo "#{$e->id} {$e->title}\n";
  if ($mUpd) { echo "  Measure U: carried\n"; }
  if ($i !== false) {
    $new = 'City of Santa Clarita, "' . $d->title . '" (archive document #' . $d->id . ')' . (($x['reading'] ?? '') !== '' ? ': ' . $x['reading'] : '') . '.';
    echo "  footnote {$rows[$i]['number']}: " . json_encode($rows[$i]['note']) . " -> $new\n"; $rows[$i]['note'] = $new; }
  $src = $e->sourceDocuments->status(null)->ids(); $addSrc = !in_array($d->id, $src, true); if ($addSrc && $i !== false) { echo "  source document: add #{$d->id}\n"; }
  if (!$APPLY) { continue; }
  $vals = []; if ($i !== false) { $vals['footnotes'] = array_values($rows); if ($addSrc) { $vals['sourceDocuments'] = array_merge($src, [$d->id]); } } if ($mUpd) { $vals['ballotMeasures'] = $ms; }
  $e->setFieldValues($vals); if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('fix_council_election_sources_2026_10_05.php', $n, 'verified', 'Measure U carried; the council elections\' source footnotes and documents'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
