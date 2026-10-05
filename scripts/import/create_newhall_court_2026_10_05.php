/**
 * The Newhall Municipal Court and its two courthouses (Nathan, 5 October 2026: "no Superior Court record. Yes to Newhall
 * Municipal Court, closed at unification on 22 January 2000, with the two courthouses as places. Judges attach to it ...
 * For now the municipal court is historical, so it needs no role at all"). From inventory/review/public-bodies-county-local-
 * 2026-10-05.md, the quotes checked against the pages on 5 October 2026.
 *   organization  Newhall Municipal Court: government, the valley, no civicRole (so not on Public bodies), dissolved 2000-01-22
 *   place         Santa Clarita Courthouse and Masonic Lodge, 24307 Railroad Avenue (1932; the court's until the move to Valencia)
 *   place         Santa Clarita Courthouse, 23747 West Valencia Boulevard (1972, by the Judicial Council; three dates in the sources)
 * Saved disabled, like the other new bodies, until Nathan has read them. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_newhall_court_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $es = Craft::$app->getEntries();
$ADAMS03 = 'Adrian W. Adams, "Tales of the Newhall Court," The Signal, April 12, 2003, as carried on SCVHistory.com, /scvhistory/sg041203.htm';
$UNIF = 'Judicial Council of California, "Effective Dates of Unification," https://courts.ca.gov/system/files/file/unidate.pdf: "Los Angeles 1/22/00."';
$SURVEY = 'City of Santa Clarita, survey of historic structures in Newhall, "24307 Railroad Avenue - Santa Clarita Courthouse: Masonic," as carried on SCVHistory.com, /scvhistory/newhall-historic-structures.htm';
$AA = 'Caption to "Judge Adrian W. Adams," AA7001, quoting The Signal of January 28, 1970, as carried on SCVHistory.com, /scvhistory/aa7001.htm: "Adrian W. Adams was appointed judge of the Newhall Municipal Court on Jan. 27, 1970, by Gov. Ronald Reagan"; "Newhall became a two-judge town yesterday with the appointment of Adrian Adams to the Newhall Municipal Court bench."';
$LW = 'Leon Worden, caption to LW2312a, a photograph of December 19, 1970, as carried on SCVHistory.com, /scvhistory/lw2312a.htm: the courthouse at Valencia Boulevard and Magic Mountain Parkway "was under construction in during 1970."';
$NOP = 'Judicial Council of California, Notice of Preparation, New Santa Clarita Courthouse, October 23, 2025, https://courts.ca.gov/system/files/file/aecom_sc_courthouse_nop_20251023.pdf, page 2: "Santa Clarita Courthouse was constructed in 1972"; existing court services in the City are "three courtrooms hearing only criminal misdemeanor case types."';
$R = [
 ['organizations', 'Newhall Municipal Court',
  "The Newhall Municipal Court was the Santa Clarita Valley's own court, the court of the Newhall Judicial District, until the trial courts of Los Angeles County were unified on January 22, 2000.[1] Adrian W. Adams, one of its judges, wrote in 2003: \"As of now, Newhall Municipal Court is only history. With the recent unification of the courts, it has become part of the Los Angeles County Superior Courts.\"[2]\n\nIt began as a justice court. \"Originally known as the Soledad Judicial District, the name was changed to the Newhall Judicial District in 1952 as a result of action by the state Legislature,\" Adams wrote, and \"Until the 1960s when the Justice Court became a Municipal Court, a layman presided over the local court.\"[2] The County's court had the ground floor of the Masonic building at 24307 Railroad Avenue in Newhall, which opened in 1932, until it moved the court to Valencia.[3] On January 27, 1970 Governor Ronald Reagan appointed Adams to a second judgeship: \"Newhall became a two-judge town yesterday,\" The Signal reported.[4] The new courthouse at Valencia Boulevard and Magic Mountain Parkway was under construction that year.[5][6]",
  [$UNIF, "$ADAMS03: \"Originally known as the Soledad Judicial District, the name was changed to the Newhall Judicial District in 1952 as a result of action by the state Legislature\"; \"Until the 1960s when the Justice Court became a Municipal Court, a layman presided over the local court\"; \"As of now, Newhall Municipal Court is only history. With the recent unification of the courts, it has become part of the Los Angeles County Superior Courts.\"",
   "$SURVEY: \"The County Courthouse occupied the ground floor and the Masonic Lodge the second floor\"; \"The County relocated the court to Valencia in 1968.\"", $AA, $LW, $NOP],
  [['heading' => 'When the court moved to Valencia', 'note' => 'The sources give three dates: the City\'s survey says the County moved the court to Valencia in 1968 (note 3); Leon Worden\'s caption has the new courthouse under construction in 1970 (note 5); the Judicial Council says it was built in 1972 (note 6). None is followed here.']],
  ['orgType' => 'government', 'orgLevel' => 'valley', 'civicRole' => '', 'dateDissolved' => 'January 22, 2000', 'dateDissolvedEdtf' => '2000-01-22']],
 ['places', 'Santa Clarita Courthouse and Masonic Lodge, 24307 Railroad Avenue',
  "The two-story building at 24307 Railroad Avenue in Newhall was the valley's courthouse and its Masonic lodge: the County's court had the ground floor and the lodge the second. The Newhall Masonic Building Company began it in 1931 and it opened in 1932.[1] The County moved the court to Valencia, by the City's survey in 1968, and the ground floor became offices; the City lists the building as a Point of Historical Interest.[1]",
  ["$SURVEY: \"Construction began in 1931 by the Newhall Masonic Building Company, Ltd.\"; \"it opened in 1932\"; \"The County Courthouse occupied the ground floor and the Masonic Lodge the second floor\"; \"The County relocated the court to Valencia in 1968 and the first floor was renovated for office uses\"; \"It is currently a City of Santa Clarita Point of Historical Interest.\""], [],
  ['placeType' => 'building', 'placeAddress' => '24307 Railroad Avenue, Newhall, CA', 'dateEstablished' => '1932', 'dateEstablishedEdtf' => '1932']],
 ['places', 'Santa Clarita Courthouse, Valencia Boulevard',
  "The Santa Clarita Courthouse, at 23747 West Valencia Boulevard in the County Civic Center, is the Superior Court's courthouse in the valley, and before unification in 2000 it was the Newhall Municipal Court's. Today it has three courtrooms, which hear only criminal misdemeanors; the Judicial Council proposes a larger courthouse to replace it.[1] It was under construction in 1970, by Leon Worden's caption, and the Judicial Council gives 1972 for its completion.[2][1]",
  [$NOP, $LW], [['heading' => 'When it was built', 'note' => 'The City\'s survey of historic structures says the County moved the court to Valencia in 1968; Leon Worden\'s caption has this courthouse under construction in 1970 (note 2); the Judicial Council says it was built in 1972 (note 1). None is followed here.']],
  ['placeType' => 'building', 'placeAddress' => '23747 West Valencia Boulevard, Santa Clarita, CA', 'dateEstablished' => '1972', 'dateEstablishedEdtf' => '1972']],
];
$n = 0; $court = null;
foreach ($R as [$sec, $t, $body, $notes, $ed, $extra]) {
  $e = Entry::find()->section($sec)->title($t)->status(null)->one(); echo "$sec: $t: " . ($e ? "#{$e->id} exists" : 'create') . ', ' . count($notes) . " notes\n";
  if (!$APPLY) { continue; }
  if (!$e) { $s = $es->getSectionByHandle($sec); $e = new Entry(); $e->sectionId = $s->id; $e->setTypeId($s->getEntryTypes()[0]->id); $e->title = $t; $e->enabled = false; }
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  $v = $extra + ['body' => $body, 'footnotes' => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($notes), $notes),
    'editorNotes' => array_map(fn($x) => $x + ['position' => 'bottom'], $ed), 'recordProvenance' => 'create_newhall_court_2026_10_05.php, 5 October 2026'];
  if ($sec === 'places' && $court) { $v['placeOrganizations'] = [$court->id]; }
  $e->setFieldValues(array_intersect_key($v, array_flip($h)));
  if (!$el->saveElement($e)) { throw new \RuntimeException("$t " . json_encode($e->getFirstErrors())); } $n++;
  if ($sec === 'organizations') { $court = $e; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('create_newhall_court_2026_10_05.php', $n, 'verified', 'Newhall Municipal Court and its two courthouses, disabled for Nathan'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
