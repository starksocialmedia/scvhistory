/**
 * Three source faults from the disaster figures audit (inventory/review/disaster-figures-audit-2026-10-07.md). Nathan, 7 October 2026:
 * "Panhorst's $14.6 million belongs to a different flood and Buckweed's 'nearly one million' belongs to the whole siege. Log both as
 * source faults", and "the Northridge USGS numbers repeating in the navigation box of 29 pages is a fault to log". Each is a figure a
 * page prints for a wider or a different event than the page is about: the regional-read-as-local trap the audit found.
 *  - F.W. Panhorst, "Role of Highways in Recent California Floods" (1938), on SCVHistory.com under "Great Flood of 1938"
 *    (/scvhistory/panhorst0838.htm): "$14,600,000" is his estimate for the northern California flood of December 1937 ("this
 *    flood"), printed just before he turns to "In southern California, the floods came in March". On the Great Flood of 1938 event,
 *    which cites the page.
 *  - CAL FIRE et al., California Fire Siege 2007 (photograph #5275, /scvhistory/lw3443.htm, headed "incl. Buckweed, Ranch, Magic Fires"):
 *    "displaced nearly one million residents" and "took the lives of 10 people" are the whole October 2007 siege, statewide.
 *  - The USGS summary of the Northridge earthquake ("Sixty people were killed ... Losses were estimated at $20 billion"), four counties,
 *    repeated in the text of 28 photograph records of the 1994 series (the navigation box of their pages); the valley's own figures are
 *    one death and $430,796,227.35 (the City, December 1994). On the Northridge Earthquake event.
 * Idempotent (matched on asPrinted). Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/disaster_source_faults_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0; $sec = Craft::$app->getEntries()->getSectionByHandle('sourceFaults');
$F = [
  [31904, 'sources', 'The preliminary estimate of damage resulting from this flood is $14,600,000',
    '$14,600,000 is the northern California flood of December 1937, not the southern California flood of March 1938',
    'F.W. Panhorst\'s paper, carried on SCVHistory.com under the heading "Great Flood of 1938", gives "$14,600,000" as the preliminary estimate for "this flood": the flood in northern California he has just described. His next sentence turns south: "In southern California, the floods came in March." A reader of the page, or of this record, could take the figure for the 1938 flood. This record does not use it.',
    'F.W. Panhorst, "Role of Highways in Recent California Floods," Civil Engineering, August 1938, as carried on SCVHistory.com, /scvhistory/panhorst0838.htm: "The preliminary estimate of damage resulting from this flood is $14,600,000, of which $4,500,000 is for highways, roads, and streets. In southern California, the floods came in March."'],
  [5275, 'body', 'The fires displaced nearly one million residents, destroyed thousands of homes, and sadly took the lives of 10 people.',
    'the whole October 2007 fire siege, statewide; not the Buckweed Fire',
    'The report\'s abstract, carried with this photograph under a heading that names the Buckweed Fire, counts the whole siege of October 2007 across Southern California. Neither the displaced nor the dead are the Buckweed Fire\'s, and neither is this valley\'s.',
    'California Department of Forestry and Fire Protection (CAL FIRE), U.S. Forest Service and California Office of Emergency Services, California Fire Siege 2007, abstract, as carried on SCVHistory.com, /scvhistory/lw3443.htm: "In October of 2007, a series of large wildfires ignited and burned hundreds of thousands of acres in Southern California. The fires displaced nearly one million residents, destroyed thousands of homes, and sadly took the lives of 10 people."'],
  [875, 'body', 'Sixty people were killed, more than 7,000 injured, 20,000 homeless',
    'four counties\' figures (USGS); the valley\'s own: one death and $430,796,227.35',
    'The United States Geological Survey\'s summary of the earthquake, for four counties, is repeated in the text beside 28 photograph records of the 1994 series, so it is the figure a reader meets first on almost every page about the earthquake here. The valley\'s own figures are one death and the City of Santa Clarita\'s estimate of December 1994. The summary is right for what it counts; it is not this valley\'s toll.',
    'U.S. Geological Survey, "USGS Response to an Urban Earthquake: Northridge \'94 (Summary)," as carried on SCVHistory.com with the photographs of the 1994 series, for example /scvhistory/lw2749.htm: "Sixty people were killed, more than 7,000 injured, 20,000 homeless and more than 40,000 buildings damaged in Los Angeles, Ventura, Orange and San Bernardino Counties as a result of the Northridge earthquake of January 17, 1994. Losses were estimated at $20 billion."'],
];
foreach ($F as [$rid, $field, $as, $reading, $basis, $fn]) {
  $r = Entry::find()->id($rid)->status(null)->one(); if (!$r) { throw new \RuntimeException("#$rid missing"); }
  $have = null; foreach (Entry::find()->section('sourceFaults')->status(null)->relatedTo(['targetElement' => $rid, 'field' => 'faultRecord'])->all() as $x) { if ((string)$x->asPrinted === $as) { $have = $x; } } /* compared here: a query parameter reads commas as a list */
  echo "#$rid {$r->title}: " . ($have ? "logged already #{$have->id}" : 'log') . "\n   as printed: " . mb_substr($as, 0, 110) . "\n   reading: $reading\n";
  if (!$APPLY || $have) { continue; }
  $f = new Entry(); $f->sectionId = $sec->id; $f->setTypeId($sec->getEntryTypes()[0]->id);
  $f->setFieldValues(['asPrinted' => $as, 'reading' => $reading, 'basis' => $basis, 'faultField' => $field, 'faultRecord' => [$rid], 'decidedBy' => 'disaster_source_faults_2026_10_07.php, 7 October 2026',
    'footnotes' => [['number' => '1', 'note' => $fn, 'source' => 'editorial-2026']], 'recordProvenance' => 'disaster_source_faults_2026_10_07.php, 7 October 2026, from the disaster figures audit']);
  if (!$el->saveElement($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('disaster_source_faults_2026_10_07.php', $n, 'verified', 'three source faults: Panhorst 1937, the 2007 siege, the Northridge USGS summary'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
