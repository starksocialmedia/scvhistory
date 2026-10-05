/**
 * Records for the agencies the fallen officers served that had none (Nathan, 5 October 2026: "records for the CHP, LAPD,
 * Burbank Police and the county constables. An officer's record should link to the body he served, and a name in plain text
 * is a dead end. Keep them minimal unless the archive holds more").
 * Each says in a sentence what the archive holds of it, cited to the same sources as its officers' records
 * (inventory/review/fallen-officers-draft-2026-10-05.json), and nothing about the agency the archive does not hold.
 * Idempotent: matched by title. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_officer_agencies_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/fallen-officers-draft-2026-10-05.json"), true)['records'];
$n1 = function ($who) use ($D) { foreach ($D as $r) { if (str_contains($r['title'], $who)) { $x = $r['footnotes'][0]; return is_array($x) ? $x['note'] : $x; } } throw new \RuntimeException("no $who"); };
$A = [
  ['California Highway Patrol', 'state', 'The California Highway Patrol is the state\'s highway police. Four officers of its Newhall Area office, Walter C. Frago, Roger D. Gore, James E. Pence Jr. and George M. Alleyn, were killed on the night of April 5 and 6, 1970, in what is called the Newhall Incident; they are among the archive\'s fallen officers.[1]', [$n1('Frago')]],
  ['Los Angeles Police Department', '', 'The Los Angeles Police Department is the police force of the City of Los Angeles. Its motor officer Clarence Wayne Dean was killed at the Route 14 and Interstate 5 interchange in the Newhall Pass in the earthquake of January 17, 1994, on his way to work; he is among the archive\'s fallen officers.[1]', [$n1('Dean')]],
  ['Burbank Police Department', '', 'The Burbank Police Department is the police force of the City of Burbank. Its officer Matthew Pavelka, who lived in Canyon Country, was killed on duty in Burbank in 2003; he is among the archive\'s fallen officers.[1]', [$n1('Pavelka')]],
  ['Los Angeles County Township Constables', 'county', 'Los Angeles County\'s townships had their own constables and deputy constables. Three who served the Newhall Township were killed on duty: Deputy Constable Charles A. De Moranville in 1909, Deputy Constable J. Edward Brown in 1924 and Constable John S. Pilcher in 1925; they are among the archive\'s fallen officers.[1][2][3]', [$n1('Moranville'), $n1('Ed" Brown'), $n1('Pilcher')]],
  ['Ventura County Township Constables', 'county', 'Ventura County\'s townships had their own constables. McCoy Pyle, the constable at Fillmore, was killed on duty at Castaic Junction in 1897; he is among the archive\'s fallen officers.[1]', [$n1('Pyle')]],
];
$os = Craft::$app->getEntries()->getSectionByHandle('organizations'); $n = 0;
foreach ($A as [$t, $lvl, $body, $notes]) {
  $e = Entry::find()->section('organizations')->title($t)->status(null)->one();
  echo "$t: " . ($e ? "#{$e->id} exists" : 'create') . PHP_EOL;
  if (!$APPLY || $e) { continue; }
  $e = new Entry(); $e->sectionId = $os->id; $e->setTypeId($os->getEntryTypes()[0]->id); $e->title = $t;
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  /* Each is a government. Whether it is on Public bodies is civicRole (add_civic_role_2026_10_05.php): LAPD, Burbank Police and
     Ventura County's constables do not serve this valley. */
  $v = ['orgType' => 'government', 'civicRole' => in_array($t, ['Los Angeles Police Department', 'Burbank Police Department', 'Ventura County Township Constables'], true) ? 'none' : 'polices', 'orgLevel' => $lvl, 'body' => $body,
    'footnotes' => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($notes), $notes),
    'recordProvenance' => 'create_officer_agencies_2026_10_05.php, 5 October 2026: the agency a fallen officer served'];
  $e->setFieldValues(array_intersect_key($v, array_flip($h)));
  if (!$el->saveElement($e)) { throw new \RuntimeException("$t " . json_encode($e->getFirstErrors())); } $n++;
  echo "   #{$e->id}" . PHP_EOL;
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_officer_agencies_2026_10_05.php', $n, 'verified', 'five agencies the fallen officers served'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
