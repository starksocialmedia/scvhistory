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
 * Extended the same day (silent-faults audit): the five elections of 2016 to 2024 cite "sov2016:" to "sov2024:", the same
 * fault, which the first version's two-key match missed; every key in the extract is now resolved through its file.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_council_election_sources_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$J = json_decode(file_get_contents("$root/inventory/elections/elections.json"), true);
/* Every document key the extract names, resolved through its file to its document record; a key that resolves to nothing
   stops the script (the importer's fault was to print the key instead). */
$DOC = [];
foreach ($J['documents'] as $key => $file) {
  $a = \craft\elements\Asset::find()->filename($file)->one();
  $d = $a ? Entry::find()->section('documents')->status(null)->relatedTo(['targetElement' => $a, 'field' => 'documentFiles'])->one() : null;
  if (!$d) { throw new \RuntimeException("document key $key ($file): no document record"); }
  $DOC[$key] = $d;
}
$KEYS = implode('|', array_map(fn($k) => preg_quote($k, '~'), array_keys($DOC)));
$byDate = []; foreach ($J['elections'] as $x) { if (isset($DOC[$x['doc'] ?? ''])) { $byDate[$x['date']] = $x; } }
foreach (Entry::find()->section('elections')->status(null)->all() as $e) {
  $rows = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], iterator_to_array($e->footnotes ?? []));
  $i = array_search(true, array_map(fn($r) => (bool)preg_match('~^(' . $KEYS . ')(\.|: )~', trim($r['note'])), $rows), true);
  $mUpd = false; $ms = [];
  if ($e->id === 21948) { foreach ($e->ballotMeasures ?? [] as $m) { $m = ['letter' => $m['letter'], 'subject' => $m['subject'], 'yes' => $m['yes'], 'no' => $m['no'], 'carried' => (bool)$m['carried']]; if ($m['letter'] === 'U' && !$m['carried']) { $m['carried'] = true; $mUpd = true; } $ms[] = $m; } }
  if ($i === false && !$mUpd) { continue; }
  $date = (string)$e->electionDateEdtf; $x = $byDate[$date] ?? null; $key = $i !== false ? preg_replace('~^(' . $KEYS . ').*$~s', '$1', trim($rows[$i]['note'])) : ($x['doc'] ?? 'summary'); $d = $DOC[$key];
  echo "#{$e->id} {$e->title}\n";
  if ($mUpd) { echo "  Measure U: carried\n"; }
  if ($i !== false) {
    $pub = $d->publishedBy->status(null)->one()?->title; $rest = trim((string)preg_replace('~^(' . $KEYS . ')(\.|: )~', '', trim($rows[$i]['note'])));
    $new = ($pub ? "$pub, " : '') . '"' . $d->title . '" (archive document #' . $d->id . ')' . ($rest !== '' ? ': ' . rtrim($rest, '.') : (($x['reading'] ?? '') !== '' ? ': ' . $x['reading'] : '')) . '.';
    echo "  footnote {$rows[$i]['number']}: " . json_encode($rows[$i]['note']) . " -> $new\n"; $rows[$i]['note'] = $new; }
  $src = $e->sourceDocuments->status(null)->ids(); $addSrc = !in_array($d->id, $src, true); if ($addSrc && $i !== false) { echo "  source document: add #{$d->id}\n"; }
  if (!$APPLY) { continue; }
  $vals = []; if ($i !== false) { $vals['footnotes'] = array_values($rows); if ($addSrc) { $vals['sourceDocuments'] = array_merge($src, [$d->id]); } } if ($mUpd) { $vals['ballotMeasures'] = $ms; }
  $e->setFieldValues($vals); if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('fix_council_election_sources_2026_10_05.php', $n, 'verified', 'Measure U carried; the council elections\' source footnotes and documents'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
