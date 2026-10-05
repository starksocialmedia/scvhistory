/**
 * The Saugus High School shooting, step 3 of the order of work: the entities before the sources (Nathan, 5 October 2026,
 * D1 and D10 approved as recommended: "Central Park as a place before the event ... give Saugus High School its community").
 * inventory/review/saugus-high-2019-survey-2026-10-05.md.
 * - Santa Clarita Central Park, a place: a City park on Castaic Lake Water Agency land in Saugus, from Leon Worden's text on
 *   photograph #4961 (LW3141), which the place relates to. The vigil of 17 November 2019 was held there; that tie is made by
 *   the event, from its sources, not here.
 * - The Los Angeles Times, an organization (media), as the publisher of eight of the pieces (D5: those stay unpublished until
 *   the rights are settled). Wikidata Q188515, checked 5 October 2026. Minimal: what the archive needs to cite it.
 * - Saugus High School (#21777) gets its community, Saugus (#205), which it lacked.
 * - SCVTV, an organization (media), as the publisher of two pieces and the videos; the extract shows the pieces credited
 *   "SCVTV" and "SCVTV/SCVNews.com", so SCVNews.com is an alias. No Wikidata item found; none given.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/saugus_2019_entities_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $es = Craft::$app->getEntries(); $n = 0;
$photo = Entry::find()->id(4961)->status(null)->one(); $city = Entry::find()->id(394)->one(); $clwa = Entry::find()->id(26563)->status(null)->one();
$saugus = \craft\elements\Category::find()->id(205)->one(); $shs = Entry::find()->id(21777)->status(null)->one();
foreach ([[$photo, 'Future Santa Clarita Central Park Site, 1998.'], [$city, 'The City of Santa Clarita'], [$clwa, 'Castaic Lake Water Agency'], [$shs, 'Saugus High School']] as [$x, $t]) {
  if (!$x || $x->title !== $t) { throw new \RuntimeException("expected \"$t\""); } }
if (!$saugus || $saugus->title !== 'Saugus') { throw new \RuntimeException('expected the category Saugus'); }
$NOTE = 'Leon Worden, caption to his photographs of the park site, 1998 to 2013 (LW3141), archive record #4961: "The former cow pasture below the Castaic Lake Water Agency\'s Rio Vista Water Treatment Facility on Bouquet Canyon Road ... before the city of Santa Clarita turned it into Central Park"; "an agreement to lease 130 acres of CLWA property to the city for $100 a year" (1997); "Construction was completed in February 2000, and the park opened"; "Subsequent years brought additional phases and amenities such as a community garden and a dog park."';
$BODY = 'A City of Santa Clarita park on Bouquet Canyon Road in Saugus, below the Castaic Lake Water Agency\'s Rio Vista Water Treatment Facility, on 130 acres the agency leased to the City in 1997 for $100 a year. The site had been a cow pasture. Its first phase, with lighted softball fields and sports fields, was finished in February 2000, and later phases added a community garden and a dog park.[1]';

/* Central Park */
$cp = Entry::find()->section('places')->status(null)->title('Santa Clarita Central Park')->one();
echo 'Santa Clarita Central Park: ' . ($cp ? "exists #{$cp->id}" : 'create') . "\n  $BODY\n  [1] $NOTE\n";
if ($APPLY && !$cp) {
  $sec = $es->getSectionByHandle('places'); $cp = new Entry(); $cp->sectionId = $sec->id; $cp->setTypeId($sec->getEntryTypes()[0]->id);
  $cp->title = 'Santa Clarita Central Park'; $cp->slug = 'santa-clarita-central-park';
  $cp->setFieldValues(['body' => $BODY, 'placeAliases' => 'Central Park', 'placeType' => 'park', 'dateEstablished' => 'February 2000', 'dateEstablishedEdtf' => '2000-02',
    'placeOrganizations' => [$city->id, $clwa->id], 'neighborhood' => [$saugus->id],
    'footnotes' => [['number' => '1', 'note' => $NOTE, 'source' => 'editorial-2026']],
    'recordProvenance' => 'saugus_2019_entities_2026_10_05.php, 5 October 2026']);
  if (!$el->saveElement($cp)) { throw new \RuntimeException(json_encode($cp->getFirstErrors())); } $n++;
}
$linked = $cp && in_array($cp->id, $photo->photoPlaces->status(null)->ids(), true);
echo 'photograph #4961 to the park: ' . ($linked ? 'linked' : 'link') . "\n";
if ($APPLY && $cp && !$linked) { $photo->setFieldValue('photoPlaces', array_merge($photo->photoPlaces->status(null)->ids(), [$cp->id])); if (!$el->saveElement($photo)) { throw new \RuntimeException('#4961'); } $n++; }

/* The Los Angeles Times */
$lat = Entry::find()->section('organizations')->status(null)->title('Los Angeles Times')->one();
echo 'Los Angeles Times: ' . ($lat ? "exists #{$lat->id}" : 'create (media, Q188515)') . "\n";
if ($APPLY && !$lat) {
  $sec = $es->getSectionByHandle('organizations'); $lat = new Entry(); $lat->sectionId = $sec->id; $lat->setTypeId($sec->getEntryTypes()[0]->id);
  $lat->title = 'Los Angeles Times'; $lat->slug = 'los-angeles-times';
  $lat->setFieldValues(['orgType' => 'media', 'wikidataId' => 'Q188515', 'civicRole' => 'none', 'recordProvenance' => 'saugus_2019_entities_2026_10_05.php, 5 October 2026: a publisher of sources']);
  if (!$el->saveElement($lat)) { throw new \RuntimeException(json_encode($lat->getFirstErrors())); } $n++;
}

/* SCVTV */
$tv = Entry::find()->section('organizations')->status(null)->title('SCVTV')->one();
echo 'SCVTV: ' . ($tv ? "exists #{$tv->id}" : 'create (media, alias SCVNews.com)') . "\n";
if ($APPLY && !$tv) {
  $sec = $es->getSectionByHandle('organizations'); $tv = new Entry(); $tv->sectionId = $sec->id; $tv->setTypeId($sec->getEntryTypes()[0]->id);
  $tv->title = 'SCVTV'; $tv->slug = 'scvtv';
  $tv->setFieldValues(['orgType' => 'media', 'orgAliases' => 'SCVNews.com', 'civicRole' => 'none', 'recordProvenance' => 'saugus_2019_entities_2026_10_05.php, 5 October 2026: a publisher of sources']);
  if (!$el->saveElement($tv)) { throw new \RuntimeException(json_encode($tv->getFirstErrors())); } $n++;
}

/* Saugus High School's community */
$has = in_array($saugus->id, $shs->neighborhood->ids(), true);
echo 'Saugus High School community: ' . ($has ? 'Saugus already' : 'set to Saugus') . "\n";
if ($APPLY && !$has) { $shs->setFieldValue('neighborhood', [$saugus->id]); if (!$el->saveElement($shs)) { throw new \RuntimeException('#21777'); } $n++; }

if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('saugus_2019_entities_2026_10_05.php', $n, 'verified', 'Central Park, the Los Angeles Times, SCVTV, Saugus High School\'s community'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
