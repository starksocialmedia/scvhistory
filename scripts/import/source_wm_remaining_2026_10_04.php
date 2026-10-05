/**
 * War memorial sourcing, the remaining records (Nathan, 4 October 2026: "The war memorial sourcing for the records still
 * unsourced: Ball, Ross, Kenaston, Cone, Rubel, and the eight War on Terror records. The VA Nationwide Gravesite Locator is
 * approved for Wilson, Todd, Conant and Colley").
 * Research: inventory/review/war-memorial-sourcing-2026-10-04.md and .json, with the pages saved in
 * inventory/news/war-memorial-2026-10-04/ (manifest.json). Footnotes are read from that JSON, each with its page's address.
 * On the pattern of source_wm_terror.php: footnotes, a fact row for each fact a note covers (the record's value, the notes,
 * and any difference a source shows), and a "Sources, 2026" editor note in place of "Not yet sourced". Nothing on a record
 * is changed to match a source: where a source differs, the row says so, and the change waits on Nathan (Cone's branch and
 * date of death, the ranks at death of Todd, Sellen, Gelig and Acosta, Acosta's age, Suter's home, Rubel's service years).
 * Dean Todd's "Home, 2026" note, which said no tie to the valley had been found, is replaced: Stars and Stripes gives
 * Canyon Country and Canyon High School.
 * NOT FOUND, unchanged: #514 Kenaston, #528 Wilson.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_remaining_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$J = json_decode((string)file_get_contents("$root/inventory/review/war-memorial-sourcing-2026-10-04.json"), true)['records'];
$LO = 'From the legacy page only.';
$R = [
 1395 => ['title' => 'James Robert "Jimmie" Ball', 'facts' => [
   ['Branch', 'United States Navy Reserve', '1, 7', ''],
   ['Rank', 'Seaman First Class', '1, 2, 7', ''],
   ['Assignment', 'USS Gudgeon (SS-211), submarine', '2, 3', ''],
   ['Born', 'November 18, 1922', '4', ''],
   ['Home', 'Newhall (1322 Newhall Avenue)', '4, 5', ''],
   ['Selective Service', 'Draft card undated; about 1941', '4', 'The card is on the form revised June 1, 1942, and gives his age as 19, so he registered between mid-1942 and November 17, 1942.'],
   ['Loss', 'Presumed aerial bombardment; submarine sunk', '2, 3', 'The Navy\'s 1949 report gives a bombing claim of April 18, 1944 only as a possibility; the Navy\'s ship history says how she was lost is not known.'],
   ['Date of death', 'January 15, 1946 (determination)', '8', 'The Navy declared Gudgeon overdue and presumed lost on June 7, 1944 (note 3).'],
   ['Memorial', 'Courts of the Missing, Honolulu Memorial', '7', ''],
  ], 'editor' => 'This record meets the archive\'s rule for a memorial record: the Navy\'s 1946 casualty list for Idaho, where his wife lived, records his death (note 1), and his draft card and the Newhall Signal of September 22, 1944 tie him to Newhall (notes 4 and 5).'],
 580 => ['title' => 'Thomas Milton Ross Jr.', 'facts' => [
   ['Branch', 'U.S. Navy', '1, 2', ''],
   ['Rank', 'First Class Gunners Mate', '1, 2', ''],
   ['Date of death', 'Missing in action, October 1943', '2', 'The American Battle Monuments Commission gives September 30, 1944, missing in action (note 1).'],
   ['Home', 'Soledad Township (SCV)', '2', $LO],
  ], 'editor' => 'This record meets half of the archive\'s rule for a memorial record. The American Battle Monuments Commission lists a Thomas M. Ross, Gunner\'s Mate First Class, U.S. Navy, from California, missing in action and named on the Walls of the Missing at Manila (note 1); the match rests on his name, rating and state, since the Commission gives no hometown. His tie to the valley rests on the legacy page.'],
 576 => ['title' => 'Robert Russell Cone', 'facts' => [
   ['Branch', 'U.S. Army', '4', 'The American Battle Monuments Commission and the Navy\'s 1946 casualty list both give the U.S. Navy, Seaman Second Class (notes 1 and 2), as the legacy page\'s own service line does.'],
   ['Date of death', 'March 13, 1945', '4', 'The American Battle Monuments Commission gives August 10, 1943, missing in action (note 1); the legacy page\'s own text gives August 9, 1942.'],
   ['Name', 'Robert Russell Cone', '3', 'The Commission and the Navy\'s list spell his middle name Russel (notes 1 and 2); his father\'s name is spelled both ways.'],
   ['Home', 'Soledad Township (SCV)', '3, 4', 'His family\'s Saugus Cafe ties him to the valley (note 3); the Navy\'s 1946 list gives his father\'s address in Glendora (note 2).'],
  ], 'editor' => 'This record meets the archive\'s rule for a memorial record: the American Battle Monuments Commission and the Navy\'s 1946 casualty list record his death (notes 1 and 2), and his brother\'s obituary ties the family to the valley (note 3). Both federal sources give him as a sailor, not a soldier, and a different date of death; the record\'s service and date are shown against them here and have not been changed.'],
 518 => ['title' => 'Augustus A. (August) Rubel', 'facts' => [
   ['Service', 'American Field Service', '2', ''],
   ['Rank', 'Trainer', '3', 'The American Field Service calls him a driver (note 2).'],
   ['Born', 'July 6, 1899', '2', ''],
   ['Length of service', '1917-1919, 1939-1943', '2, 3', 'The American Field Service gives his reenlistment as November 1942 (note 2).'],
   ['Date of death', 'April 28, 1943', '1, 2', 'The American Field Service\'s history puts the night his party set out as April 17 to 18 (note 2); April 28 may be the date recorded rather than the night of the explosion.'],
   ['Burial', 'North Africa American Cemetery, Carthage, Tunisia', '1', 'The Commission gives Plot B, Row 3, Grave 2.'],
   ['Home', 'Rancho Camulos (Piru)', '2, 3', ''],
  ], 'editor' => 'This record meets the archive\'s rule for a memorial record: the American Battle Monuments Commission lists his grave (note 1), and the American Field Service\'s archive records his death and his home at enlistment, Piru (note 2).'],
 542 => ['title' => 'Dean Glenn Todd Jr', 'facts' => [
   ['Name', 'Dean Glenn Todd Jr', '2', 'Stars and Stripes names him Dean Todd-Eckard (note 1).'],
   ['Rank', 'Sergeant', '2', 'Stars and Stripes gives Specialist (note 1).'],
   ['Unit', '307th Signal Battalion', '1, 2', 'Stars and Stripes adds Headquarters and Headquarters Company, 1st Signal Brigade.'],
   ['Specialty', 'Satellite communications', '2', 'Stars and Stripes gives communications and electronics maintainer (note 1).'],
   ['Date of death', 'August 31, 2004', '1, 2', 'Stars and Stripes says he was found in his room on the morning of Tuesday, August 31; the legacy page\'s text gives August 30.'],
   ['Home', 'Not given on this record', '1', 'Stars and Stripes gives his hometown as Canyon Country and says he graduated from Canyon High School in 2001 (note 1).'],
  ], 'editor' => 'This record meets half of the archive\'s rule for a memorial record. Stars and Stripes records his death in service and gives his hometown as Canyon Country (note 1); no federal record of his death is in the archive.',
  'home' => 'Stars and Stripes gave his hometown as Canyon Country and reported that he graduated from Canyon High School in 2001 (note 1). The legacy page gives no home.'],
 540 => ['title' => 'Dennis Lee Sellen Jr', 'facts' => [
   ['Rank', 'Sergeant', '2', 'The Defense Department gives Specialist (note 1).'],
   ['Unit', '1st Battalion, 185th Infantry Regiment', '1, 2', ''],
   ['Date of death', 'February 11, 2007', '1, 2', ''],
   ['Place', 'Umm Qasr, Iraq', '1, 2', ''],
   ['Home', 'Newhall', '1, 2', ''],
   ['Burial', 'Forest Lawn Memorial Park, Glendale', '2', $LO],
  ], 'editor' => 'This record meets the archive\'s rule for a memorial record in one federal document: the Defense Department\'s release records his death and gives his home as Newhall (note 1).'],
 538 => ['title' => 'Ian Timothy D. Gelig', 'facts' => [
   ['Rank', 'Sergeant', '2', 'The Defense Department gives Specialist (note 1).'],
   ['Unit', '782nd Brigade Support Battalion, 4th Brigade Combat Team, 82nd Airborne Division', '1, 2', ''],
   ['Date of death', 'March 1, 2010', '1, 2', ''],
   ['Place', 'Kandahar Province, Afghanistan', '1, 2', ''],
   ['Casualty', 'A suicide bomber drove into his convoy', '2', 'The Defense Department says enemy forces attacked his vehicle with an improvised explosive device (note 1).'],
   ['Home', 'Stevenson Ranch', '1, 2', ''],
   ['Burial', 'San Fernando Mission Cemetery, Mission Hills', '2', $LO],
  ], 'editor' => 'This record meets the archive\'s rule for a memorial record in one federal document: the Defense Department\'s release records his death and gives his home as Stevenson Ranch (note 1).'],
 536 => ['title' => 'Jake William Suter', 'facts' => [
   ['Rank', 'Private First Class', '1, 3', ''],
   ['Unit', '3rd Battalion, 3rd Marine Regiment, 3rd Marine Division, III Marine Expeditionary Force', '1, 3', ''],
   ['Date of death', 'May 29, 2010', '1, 3', ''],
   ['Place', 'Helmand province, Afghanistan', '1, 3', ''],
   ['Home', 'Stevenson Ranch', '2, 3', 'The Defense Department\'s release gives Los Angeles (note 1).'],
   ['Burial', 'Veterans Memorial Park, Bluffdale, Utah', '3', $LO],
  ], 'editor' => 'This record meets the archive\'s rule for a memorial record: the Defense Department\'s release records his death (note 1), and the Honolulu Advertiser, citing the Defense Department, gives his home as Stevenson Ranch (note 2). The release itself gives Los Angeles.'],
 534 => ['title' => 'John Michael Conant', 'facts' => [
   ['Branch', 'U.S. Army', '1, 2', ''],
   ['Rank', 'Sergeant', '1, 2', ''],
   ['Born', 'July 12, 1971', '1, 2', ''],
   ['Date of death', 'April 10, 2008', '1, 2', ''],
   ['Home', 'Saugus', '2', $LO],
   ['Burial', 'Punchbowl National Cemetery, Honolulu', '2', 'From the legacy page only. The Department of Veterans Affairs\' gravesite locator, which covers that cemetery, lists no John Conant who died in 2008.'],
  ], 'editor' => 'This record does not yet meet the archive\'s rule for a memorial record. His grave marker, photographed for SCVHistory.com, confirms his rank and dates (note 1); no federal record or newspaper account of his death is in the archive.'],
 526 => ['title' => 'Rudy Alexander Acosta', 'facts' => [
   ['Rank', 'Specialist 4th Class (SP4)', '3', 'The Defense Department and the Governor give Private First Class at his death (notes 1 and 2).'],
   ['Age at loss', '20', '3', 'The Defense Department and the Governor give 19 (notes 1 and 2), as his date of birth bears out.'],
   ['Unit', '4th Squadron, 2nd Stryker Cavalry Regiment', '1, 3', ''],
   ['Date of death', 'March 19, 2011', '1, 2, 3', ''],
   ['Place', 'Kandahar province, Afghanistan', '1, 2, 3', ''],
   ['Home', 'Canyon Country', '1, 2, 3', ''],
   ['Burial', 'Eternal Valley Cemetery, Newhall', '3', $LO],
  ], 'editor' => 'This record meets the archive\'s rule for a memorial record in one federal document: the Defense Department\'s release records his death and gives his home as Canyon Country (note 1).'],
 524 => ['title' => 'Stephen Edward Colley', 'facts' => [
   ['Rank', 'Specialist Fourth Class', '1, 3', 'The Department of Veterans Affairs gives SPC, Specialist; his father and the Army\'s investigation, as NPR quotes them, call him a private first class (note 2).'],
   ['Born', 'March 10, 1985', '1, 3', ''],
   ['Date of death', 'May 16, 2007', '1, 2, 3', ''],
   ['Specialty', 'Helicopter mechanic', '2, 3', ''],
   ['Burial', 'Central Texas State Veterans Cemetery, Killeen (Section 18, Row E, Site 57)', '1, 3', ''],
   ['Home', 'Valencia', '3', $LO],
  ], 'editor' => 'This record meets half of the archive\'s rule for a memorial record. The Department of Veterans Affairs\' burial record confirms his service in the Iraq war period, his dates and his grave (note 1), and NPR\'s account confirms the circumstances of his death (note 2); his tie to the valley rests on the legacy page.'],
];
$bad = []; $plan = [];
foreach ($R as $id => $r) {
  $e = Entry::find()->id($id)->status(null)->one();
  if (!$e || $e->title !== $r['title']) { $bad[] = "#$id is not {$r['title']}"; continue; }
  if (!empty($e->factSources)) { echo "#$id {$r['title']}: already sourced" . PHP_EOL; continue; }
  $notes = [];
  foreach ($J[(string)$id]['proposedFootnotes'] as $f) { $n = trim($f['note']); if (!empty($f['url']) && !str_contains($n, $f['url'])) { $n .= ' ' . $f['url']; } $notes[] = $n; }
  if (!str_contains(end($notes), '(' . $e->legacyUrl . ')')) { $bad[] = "#$id: the last note is not its legacy page {$e->legacyUrl}"; }
  foreach ($r['facts'] as [$fa, $v, $n]) { foreach (array_map('intval', explode(',', $n)) as $i) { if ($i < 1 || $i > count($notes)) { $bad[] = "#$id fact $fa cites note $i"; } } }
  $plan[$id] = [$r, $notes];
  echo "#$id {$r['title']}: " . count($notes) . ' notes, ' . count($r['facts']) . ' facts (' . count(array_filter($r['facts'], fn($x) => $x[3] !== '')) . ' with a difference)' . PHP_EOL;
}
if (preg_match('~\x{2014}~u', json_encode([$R, $plan], JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
  foreach ($plan as $id => [$r, $notes]) {
    $e = Entry::find()->id($id)->status(null)->one();
    $ed = [];
    foreach ($e->editorNotes ?? [] as $x) {
      $h = (string)($x['heading'] ?? ''); $t = trim((string)($x['note'] ?? '')); if ($t === '') { continue; }
      if ($h === 'Sources, 2026' && str_starts_with($t, 'Not yet sourced')) { continue; }
      if ($h === 'Home, 2026' && isset($r['home'])) { $t = $r['home']; }
      $ed[] = ['heading' => $h, 'position' => (string)($x['position'] ?? 'bottom') ?: 'bottom', 'note' => $t];
    }
    $ed[] = ['heading' => 'Sources, 2026', 'position' => 'bottom', 'note' => $r['editor']];
    $e->setFieldValues([
      'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($notes), $notes),
      'factSources' => array_map(fn($x) => ['fact' => $x[0], 'value' => $x[1], 'notes' => $x[2], 'agreement' => $x[3]], $r['facts']),
      'editorNotes' => $ed,
    ]);
    if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
  }
  $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK: ' . $t->getMessage() . PHP_EOL; throw $t; }
foreach ($plan as $id => [$r, $notes]) { $b = Entry::find()->id($id)->status(null)->one(); if (count($b->factSources) !== count($r['facts']) || count($b->footnotes) !== count($notes)) { throw new \RuntimeException("#$id not read back"); } }
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('source_wm_remaining_2026_10_04.php', $n, 'verified', 'war memorial: Ball, Ross, Cone, Rubel and seven War on Terror records sourced; differences shown, not changed; Kenaston and Wilson not found');
echo "done: $n records" . PHP_EOL;
