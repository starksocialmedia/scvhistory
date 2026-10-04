/**
 * Two fixes on body records (Nathan, 4 October 2026).
 *
 *  1. The City's council districts. The archive held seat records for Districts 1 and 3 only, the two first
 *     elected by district in 2024, so the page counted "2 council districts" and the seat control offered two.
 *     Ordinance No. 23-4 (adopted June 2023; Municipal Code section 2.04.005) divides the City into five
 *     single-member districts: Districts 1 and 3 elected in 2024 and every four years after, Districts 2, 4 and
 *     5 in 2026 and every four years after. Nathan's note said four districts; the ordinance, the City's
 *     District Elections page and the County's layer all give five, so five are recorded. Districts 2, 4 and
 *     5 get seat records like 1 and 3; until their first election the three members not elected by district
 *     serve at large, as the City's page says. Jason Gibbs's present term (#27410), which the City lists as
 *     "Councilmember Jason Gibbs, District 3", is tied to the District 3 seat it was missing.
 *  2. "The mark identifies it, a building photograph does not": a body with a current mark no longer has a
 *     building photograph as its featured image. The City (City Hall, #21861) and CSUN (Oviatt Library,
 *     #21863) keep theirs as related images (recordImages).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_city_seats_and_body_headers_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$svc = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $bad = [];
$PROV = 'fix_city_seats_and_body_headers_2026_10_04.php, 4 October 2026';
$ORD = 'City of Santa Clarita, Ordinance No. 23-4, adding section 2.04.005 to the Municipal Code, adopted June 2023, https://santaclarita.gov/wp-content/uploads/2023/11/2023-06-13-ORDINANCES-BY-DISTRICT-ELECTIONS.pdf';
$PAGE = 'City of Santa Clarita, "District Elections," https://santaclarita.gov/district-elections/, read 4 October 2026';
$city = Entry::find()->id(394)->one(); if ($city?->title !== 'The City of Santa Clarita') { $bad[] = '#394 is not the City'; }
foreach ([2, 4, 5] as $n) { $t = "Santa Clarita City Council, District $n"; echo "$t: " . (Entry::find()->section('places')->status(null)->title($t)->exists() ? 'exists' : 'create') . PHP_EOL; }
$MOVE = [394 => 21861, 16347 => 21863];
foreach ($MOVE as $o => $a) { $e = Entry::find()->id($o)->status(null)->one(); echo "#$o {$e->title}: featured " . ($e->featuredImage->one()?->id ?? 'none') . ", mark " . ($e->currentMark->one()?->id ?? 'NONE') . "; photo #$a to related images" . PHP_EOL; if (!$e->currentMark->exists()) { $bad[] = "#$o has no mark"; } }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $placeSec = $svc->getSectionByHandle('places'); $placeType = $svc->getEntryTypeByHandle('place');
foreach ([2, 4, 5] as $d) {
    $t = "Santa Clarita City Council, District $d"; if (Entry::find()->section('places')->status(null)->title($t)->exists()) { continue; }
    $p = new Entry(); $p->sectionId = $placeSec->id; $p->setTypeId($placeType->id); $p->title = $t;
    $p->setFieldValues(['districtKind' => 'council-district', 'districtNumber' => $d, 'placeOrganizations' => [394], 'recordProvenance' => $PROV,
        'body' => "One of the five districts the Santa Clarita City Council is elected from under Ordinance No. 23-4. Its first election by district is on 3 November 2026, and every four years after. Until then the three members not yet elected by district, for Districts 2, 4 and 5, serve at large ($ORD; $PAGE)."]);
    if (!$el->saveElement($p)) { throw new \RuntimeException("$t: " . json_encode($p->getFirstErrors())); } $n++;
}
foreach ($MOVE as $o => $a) {
    $e = Entry::find()->id($o)->status(null)->one(); $ids = $e->recordImages->ids();
    $e->setFieldValues(['recordImages' => in_array($a, $ids) ? $ids : array_merge([$a], $ids), 'featuredImage' => []]);
    if (!$el->saveElement($e)) { throw new \RuntimeException("#$o: " . json_encode($e->getFirstErrors())); } $n++;
}
$g = Entry::find()->id(27410)->one();
if ($g->holdingPerson->one()?->id === 23091 && $g->holdingDistrict->one()?->id !== 25155) { $g->setFieldValues(['holdingDistrict' => [25155], 'seatLabel' => 'District 3']); if (!$el->saveElement($g)) { throw new \RuntimeException('Gibbs #27410'); } $n++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_city_seats_and_body_headers_2026_10_04.php', $n, 'verified', 'Council Districts 2, 4 and 5 (Ordinance 23-4); City Hall and Oviatt photographs from the header to related images');
echo "done: $n writes" . PHP_EOL;
