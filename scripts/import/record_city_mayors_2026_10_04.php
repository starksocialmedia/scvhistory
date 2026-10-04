/**
 * The mayoralties of the sitting council and of Cameron Smyth, and the present Mayor Pro Tem, from the
 * City's own council pages (Nathan, 4 October 2026: "Record them all, and show the mayoral count on each
 * person's profile ... Mayor Pro Tem is a real office too, so record it the same way").
 *
 * The pages, read 4 October 2026 and saved in inventory/news/city-council-2026-10-04/ (html, text):
 *   Laurene Weste   "Mayoral Terms: 7 (2001, 2006, 2010, 2014, 2018, 2022, 2025)"; the page heads her
 *                   "Mayor", so the 2025 mayoralty is the present one.
 *   Marsha McLean   "Mayoral Terms: 4 (2007, 2011, 2015, 2019)"
 *   Bill Miranda    "Mayoral Terms: 2 (2021, 2024)" in its list, "He served as Mayor in 2021 and 2025"
 *                   in its text: the second recorded as 2024 or 2025.
 *   Cameron Smyth   "Mayoral Terms: 5 (2003, 2005, 2017, 2020, 2024)"
 *   Jason Gibbs     his profile gives 2023 from the City's page as read on 1 October 2026; the page read on
 *                   4 October leaves "Mayoral Terms" blank. Recorded on the 1 October reading, with that said.
 *   Patsy Ayala     "Mayor Pro Tem Patsy Ayala": Mayor Pro Tem, serving.
 * The council chooses its mayor in December, and the pages are not consistent about whether a year is the
 * one the mayor was chosen in or the one served (Miranda's page gives both 2024 and 2025 for one term), so
 * each mayoralty is held to its year as printed, with that said in its footnote.
 * Also: Ayala's term (#23401) gets its seat, District 1 (her election of 5 November 2024 was for it);
 * Weste's record gets the Mentryville community, for the Conservancy work her profile already describes.
 * Not recorded, for Nathan: the City's page lists 2025 among Weste's election years; the archive holds no
 * City election in 2025.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_city_mayors_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one(); $person = fn($t) => Entry::find()->section('persons')->status(null)->title($t)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$bad = []; $D = "$root/inventory/news/city-council-2026-10-04";
$SHA = ['laurene-weste' => '', 'marsha-mclean' => '', 'bill-miranda' => '', 'cameron-smyth' => '', 'patsy-ayala' => '', 'jason-gibbs' => ''];
$T = []; foreach (array_keys($SHA) as $m) { if (!is_file("$D/$m.txt")) { $bad[] = "$m page missing"; continue; } $T[$m] = preg_replace('~\s+~u', ' ', file_get_contents("$D/$m.txt")); $SHA[$m] = hash_file('sha256', "$D/$m.html"); }
$TITLE = ['laurene-weste' => 'Mayor Laurene Weste', 'marsha-mclean' => 'Councilmember Marsha McLean', 'bill-miranda' => 'Councilmember Bill Miranda', 'cameron-smyth' => 'Councilmember Cameron Smyth', 'patsy-ayala' => 'Mayor Pro Tem Patsy Ayala', 'jason-gibbs' => 'Councilmember Jason Gibbs'];
$cite = function (string $m, string $quote) use (&$bad, $T): string {
    if (!str_contains($T[$m] ?? '', $quote)) { $bad[] = "$m does not read: $quote"; }
    return "City of Santa Clarita, its council page for this member, https://santaclarita.gov/city-council/$m/, read 4 October 2026: \"$quote\".";
};
$YEARNOTE = 'The City gives each mayoralty by a single year. The council chooses its mayor in December, and the City\'s pages are not consistent about whether that year is the one in which the mayor was chosen or the one served.';
$M = [
    ['Laurene Weste', 'laurene-weste', 'Mayoral Terms: 7 (2001, 2006, 2010, 2014, 2018, 2022, 2025)', ['2001', '2006', '2010', '2014', '2018', '2022', '2025'], '2025'],
    ['Marsha McLean', 'marsha-mclean', 'Mayoral Terms: 4 (2007, 2011, 2015, 2019)', ['2007', '2011', '2015', '2019'], null],
    ['Cameron Smyth', 'cameron-smyth', 'Mayoral Terms: 5 (2003, 2005, 2017, 2020, 2024)', ['2003', '2005', '2017', '2020', '2024'], null],
    ['Bill Miranda', 'bill-miranda', 'Mayoral Terms: 2 (2021, 2024)', ['2021', '2024 or 2025'], null],
];
$MAYOR = Entry::find()->section('roles')->status(null)->title('Mayor')->one(); $MPT = Entry::find()->section('roles')->status(null)->title('Mayor Pro Tem')->one();
if (!$MAYOR) { $bad[] = 'no Mayor role'; }
echo 'role Mayor Pro Tem: ' . ($MPT ? 'exists' : 'create') . PHP_EOL;
$have = fn($p, $office) => $p && $office ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'holdingPerson'], ['targetElement' => $office, 'field' => 'holdingOffice'], ['targetElement' => 394, 'field' => 'holdingBody']])->all() : [];
$plan = [];
foreach ($M as [$name, $slug, $quote, $years, $current]) {
    $p = $person($name); if (!$p) { $bad[] = "$name has no record"; continue; }
    $c = $cite($slug, $quote); $held = array_map(fn($h) => (string)$h->termStart, $have($p, $MAYOR));
    foreach ($years as $y) {
        if (in_array($y, $held, true)) { echo "$name $y: held" . PHP_EOL; continue; }
        $notes = [$c, $YEARNOTE];
        if ($y === '2024 or 2025') { $notes[0] .= ' The same page\'s text says: "' . 'He served as Mayor in 2021 and 2025' . '."'; $cite($slug, 'He served as Mayor in 2021 and 2025'); }
        if ($y === $current) { $notes[] = 'The City\'s page heads her as Mayor when read: the present mayoralty.'; }
        $plan[] = ['p' => $p, 'office' => 'Mayor', 'printed' => $y, 'edtf' => $y === '2024 or 2025' ? '[2024,2025]' : $y, 'ended' => $y === $current ? 'serving' : 'expired', 'notes' => $notes];
        echo "$name: Mayor $y" . ($y === $current ? ' (present)' : '') . PHP_EOL;
    }
}
/* Gibbs, on the 1 October reading his profile cites. */
$G = $person('Jason Gibbs');
if ($G && !in_array('2023', array_map(fn($h) => (string)$h->termStart, $have($G, $MAYOR)), true)) {
    if (!str_contains(strip_tags((string)$G->body), 'he was the city\'s mayor in 2023')) { $bad[] = 'Gibbs\'s profile no longer gives 2023'; }
    $plan[] = ['p' => $G, 'office' => 'Mayor', 'printed' => '2023', 'edtf' => '2023', 'ended' => 'expired', 'notes' => ['City of Santa Clarita, "Councilmember Jason Gibbs," https://santaclarita.gov/city-council/jason-gibbs/, read 1 October 2026, which gave his mayoralty as 2023; the same page read on 4 October 2026 leaves its "Mayoral Terms" line blank.', $YEARNOTE]];
    echo 'Jason Gibbs: Mayor 2023 (the 1 October reading)' . PHP_EOL;
}
/* Ayala: Mayor Pro Tem, and her seat. */
$A = $person('Patsy Ayala'); $cA = $cite('patsy-ayala', 'Patsy Ayala serves as Mayor Pro Tem of the City of Santa Clarita');
$aHas = $MPT && $have($A, $MPT);
if (!$aHas) { $plan[] = ['p' => $A, 'office' => 'Mayor Pro Tem', 'printed' => '', 'edtf' => '', 'ended' => 'serving', 'notes' => [$cA . ' The page gives no year for it.']]; echo 'Patsy Ayala: Mayor Pro Tem, serving' . PHP_EOL; }
$AH = $get(23401); $D1 = Entry::find()->section('places')->status(null)->slug('santa-clarita-city-council-district-1')->one();
if ($AH?->holdingPerson->one()?->id !== $A?->id || !$D1) { $bad[] = '#23401 or District 1 not as expected'; }
$aSeat = $AH && $AH->holdingDistrict->one()?->id === $D1?->id;
echo '#23401 Ayala\'s term: ' . ($aSeat ? 'seat already District 1' : 'seat District 1') . PHP_EOL;
/* Weste and Mentryville. */
$W = $person('Laurene Weste'); $MV = \craft\elements\Category::find()->group('neighborhood')->slug('mentryville')->one();
$wHas = $W && $MV && in_array($MV->id, $W->neighborhood->ids());
if (!$MV || !str_contains(strip_tags((string)$W->body), 'Mentryville')) { $bad[] = 'Mentryville or Weste\'s profile not as expected'; }
echo 'Laurene Weste: ' . ($wHas ? 'Mentryville already' : 'community Mentryville added') . PHP_EOL;
echo 'NOT RECORDED: the City\'s page lists 2025 among Weste\'s election years; the archive holds no City election in 2025.' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$PROV = 'record_city_mayors_2026_10_04.php, 4 October 2026: from the City\'s council pages';
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$MPT) { $rs = $svc->getSectionByHandle('roles'); $MPT = new Entry(); $MPT->sectionId = $rs->id; $MPT->setTypeId($rs->getEntryTypes()[0]->id); $MPT->title = 'Mayor Pro Tem'; if (!$el->saveElement($MPT)) { throw new \RuntimeException('role: ' . json_encode($MPT->getFirstErrors())); } $n++; }
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($plan as $x) {
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($ot->id);
        $v = ['holdingPerson' => [$x['p']->id], 'holdingOffice' => [($x['office'] === 'Mayor' ? $MAYOR : $MPT)->id], 'holdingBody' => [394], 'selectionMethod' => 'rotated', 'howEnded' => $x['ended'], 'startEvidence' => 'certified', 'footnotes' => $fn($x['notes']), 'recordProvenance' => $PROV];
        if ($x['printed'] !== '') { $v['termStart'] = $x['printed']; $v['termStartEdtf'] = $x['edtf']; }
        $h->setFieldValues($v); if (!$el->saveElement($h)) { throw new \RuntimeException($x['p']->title . ': ' . json_encode($h->getFirstErrors())); } $n++;
    }
    if (!$aSeat) { $AH->setFieldValues(['holdingDistrict' => [$D1->id], 'seatLabel' => 'District 1']); if (!$el->saveElement($AH)) { throw new \RuntimeException('#23401: ' . json_encode($AH->getFirstErrors())); } $n++; }
    if (!$wHas) { $W->setFieldValue('neighborhood', array_values(array_unique(array_merge($W->neighborhood->ids(), [$MV->id])))); if (!$el->saveElement($W)) { throw new \RuntimeException('Weste: ' . json_encode($W->getFirstErrors())); } $n++; }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$mayors = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $MAYOR, 'field' => 'holdingOffice'], ['targetElement' => 394, 'field' => 'holdingBody']])->count();
$ok = $mayors >= 20 && $get(23401)->holdingDistrict->one()?->id === $D1->id;
echo 'READ-BACK ' . ($ok ? "OK: $n writes; $mayors mayoralties held" : "SHORT ($mayors mayoralties)") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('record_city_mayors_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'mayoralties of Weste, McLean, Smyth, Miranda, Gibbs and Ayala as Mayor Pro Tem, from the City\'s pages; Ayala\'s District 1 seat; Weste and Mentryville');
if (!$ok) { throw new \RuntimeException('record_city_mayors: read-back short'); }
