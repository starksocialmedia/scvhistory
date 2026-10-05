/**
 * Nathan, 5 October 2026, on the new public bodies:
 *  - "The fire arrangement is the opposite of the Sheriff one ... Say that plainly on both records." The fire district's record
 *    and the Sheriff's station's each say how the other service is arranged. The fire department's own list (Santa Clarita among
 *    its property-tax cities, not its contract cities) is the authority; the City's own papers speak of fire "contracts" too,
 *    which the fire record's editor note already shows. The Sheriff's side: the Board of Supervisors' agreement of June 25, 2024.
 *  - "The library split ... Put that on both records." Each library record says who serves where.
 *  - "State the nonprofit status on each record": Acton's says it in words.
 *  - "leave them unsettled and say so on the records": the Antelope Valley plan question and the Stevenson Ranch council, on
 *    the Department of Regional Planning's record (the One Valley One Vision date disagreement is already there).
 * Exact replacements and appended notes; refuses if the text has moved. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/public_bodies_contrasts_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $get = fn($id) => Entry::find()->id($id)->status(null)->one();
$note = fn($id, $n) => (string)iterator_to_array($get($id)->footnotes)[$n - 1]['note'];
$BOS = 'County of Los Angeles, Board of Supervisors, letter of the Sheriff\'s Department adopted June 25, 2024, with the Municipal Law Enforcement Services Agreement for July 1, 2024 to June 30, 2029, https://file.lacounty.gov/SDSInter/bos/supdocs/192471.pdf: the agreement provides "general law enforcement services within the corporate limits of the City"; each year the City signs a Service Level Authorization (form SH-AD 575) setting the units of service it buys.';
$FIRE1 = $note(29866, 1); $COUNTYAAD = $note(29868, 4); $SCPL1 = $note(29870, 1);
// [record, old text, new text, notes to append]
$E = [
  [29866, 'and on June 23, 1988 it approved its service agreements with the County, the fire services contracts among them.[11][12]',
   'and on June 23, 1988 it approved its service agreements with the County, the fire services contracts among them.[11][12]' . "\n\n" . 'The arrangement is the reverse of the City\'s policing. For police the City contracts with the County: under a five-year agreement with the Sheriff\'s Department, the present one running from July 1, 2024 to June 30, 2029, it signs each year for the units of patrol it buys.[13] For fire it buys no units; the department classes it among the cities inside the district whose service is paid for from their property taxes.[1]', [$BOS]],
  [29682, 'the captain since August 2025 is Brandon Barclay.[2][3]',
   'the captain since August 2025 is Brandon Barclay.[2][3] Under a five-year agreement, the present one running from July 1, 2024 to June 30, 2029, the City signs each year for the units of patrol it buys.[9] Fire service is arranged the other way: the City does not buy it by contract but lies inside the County\'s Consolidated Fire Protection District, which the fire department classes among the cities served from their property taxes.[10]', [$BOS, $FIRE1]],
  [29870, 'The unincorporated valley is served by the County\'s own branches at Castaic and Stevenson Ranch.[4][5]',
   'The unincorporated valley is served by the County\'s own branches at Castaic and Stevenson Ranch, and Agua Dulce by the County\'s Acton Agua Dulce Library.[4][5][14]', [$COUNTYAAD]],
  [29868, 'Inside the City of Santa Clarita the libraries have been the City\'s own since July 1, 2011.[6][7]',
   'Inside the City of Santa Clarita the libraries have been the City\'s own since July 1, 2011: the City Council voted in August 2010 to leave the County system, and the City runs three branches.[6][7][14]', [$SCPL1]],
  [29862, 'the Internal Revenue Service lists it as a 501(c)(4) organization.[1][3]',
   'it is a nonprofit, which the Internal Revenue Service lists as a 501(c)(4) organization.[1][3]', []],
];
$ED = [
  [29854, 'Agua Dulce and Acton', 'Whether Agua Dulce and Acton fall under this plan or under the County\'s Antelope Valley Area Plan is not settled by the sources read; it is left open here.'],
  [29854, 'A Stevenson Ranch council', 'A Stevenson Ranch Town Council was active in 1996, founded by Richard "Doc" Rioux, and a West Ranch Town Council in 2006. Whether they are one body, what it is in law, and whether it still meets are not settled by the sources read, so it has no record yet.'],
];
$bad = []; $plan = [];
foreach ($E as [$id, $old, $new, $add]) { $e = $get($id); $b = (string)$e->body;
  if (str_contains($b, $new)) { echo "#$id done already\n"; continue; }
  if (substr_count($b, $old) !== 1) { $bad[] = "#$id: text not found once"; continue; }
  $n = count(iterator_to_array($e->footnotes)); preg_match_all('~\[(\d+)\]~', $new, $m); $max = max(array_map('intval', $m[1]));
  if ($max > $n + count($add)) { $bad[] = "#$id: marker [$max] beyond " . ($n + count($add)) . " notes"; continue; }
  $plan[] = [$id, $old, $new, $add]; echo "#$id {$e->title}: body edit, " . count($add) . " note(s) added\n"; }
foreach ($ED as [$id, $h, $t]) { $e = $get($id); if (in_array($t, array_column(iterator_to_array($e->editorNotes ?? []), 'note'), true)) { echo "#$id note done already\n"; continue; } echo "#$id editor note: $h\n"; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$c = 0;
foreach ($plan as [$id, $old, $new, $add]) { $e = $get($id);
  $fn = array_values(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], iterator_to_array($e->footnotes)));
  foreach ($add as $a) { $fn[] = ['number' => (string)(count($fn) + 1), 'note' => $a, 'source' => 'editorial-2026']; }
  $e->setFieldValues(['body' => str_replace($old, $new, (string)$e->body), 'footnotes' => $fn]);
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$id"); } $c++; }
foreach ($ED as [$id, $h, $t]) { $e = $get($id); $rows = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes ?? [])), fn($r) => $r['note'] !== ''));
  if (in_array($t, array_column($rows, 'note'), true)) { continue; } $rows[] = ['heading' => $h, 'note' => $t, 'position' => 'bottom']; $e->setFieldValue('editorNotes', $rows); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id ed"); } $c++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('public_bodies_contrasts_2026_10_05.php', $c, 'verified', 'fire and Sheriff arrangements on both records; the library split on both; Acton a nonprofit; two open questions on Regional Planning');
echo "done: $c\n";
