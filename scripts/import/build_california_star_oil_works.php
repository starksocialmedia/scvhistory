/**
 * California Star Oil Works #16039, built out; D. G. Scofield's person record;
 * the Pioneer Oil Refinery place #20131 given its location, so the organization
 * page carries a map; the leftover disabled organization #16201 removed.
 *
 * Nathan, 29 September 2026: a test of whether the archive can be more
 * thorough than Wikipedia on a local subject. The claims he listed were checked
 * against contemporary papers (California Digital Newspaper Collection, article
 * ids in the footnotes), the 1963 National Park Service survey of Pico Well
 * No. 4, the landmark texts and the 2020 draft National Register nomination.
 * Several did not survive, and the body says what the sources say instead:
 *
 *   - Scofield did not found the company and did not hire Mentry. The 1876
 *     incorporation notices name other directors; the Star Oil Works partners
 *     employed Mentry in July 1875; Scofield's first contemporary appearance
 *     found is May 1877.
 *   - The Newhall refinery was built in 1877, per the papers of that year and
 *     the 1963 survey. 1876, on the plaque, the landmark and the ASME brochure,
 *     is the year of the company's Lyons Station and Ventura works. Both dates
 *     are given, with their sources.
 *   - Standard Oil bought the Pacific Coast Oil Company in 1900, not this
 *     company, which was by then its subsidiary.
 *   - Scofield was vice president of Standard Oil Company (California) from
 *     1906 and president from December 1911.
 *   - "The oldest surviving refinery in the world" is made by no landmark body;
 *     it appears in City tourism copy and online encyclopedias, uncited. Laid
 *     out the way Mentry's priority claim is: who says what, in what scope.
 *
 * Wikipedia is an external link on the place and the person, never a footnote.
 * The Firefly image Nathan supplied is not used: its manifest declares it
 * generated (scan_content_credentials.py).
 *
 * NOT DONE HERE: lw3555 is not imported. It is on the mirror, and Reggie is not
 * mounted; the live site is fetched only with Nathan's approval.
 *
 * Fills empty fields only; appends to relations and aliases without removing
 * anything. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_california_star_oil_works.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$ORG = 16039; $PLACE = 20131; $OLD_ORG = 16201; $MENTRY = 18648; $EULOGY = 20104;
$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$cdnc = fn(string $id) => 'https://cdnc.ucr.edu/?a=d&d=' . $id;

$SNELL = 'Charles W. Snell, National Park Service, National Survey of Historic Sites and Buildings, Pico Well No. 4, 1963, https://npgallery.nps.gov/NRHP/GetAsset/NHLS/66000212_text, drawing on Gerald T. White, Formative Years in the Far West (1962)';
$ELSMERE = 'Elsmere Canyon, "Pioneer Oil Refinery: History," https://elsmerecanyon.com/pioneerrefinery/history/history.htm, which quotes the 1876-1877 papers and White (1962), p. 46';
$ASME = 'American Society of Mechanical Engineers, landmark no. 8, Pioneer Oil Refinery, California Star Oil Works, designated 27 September 1975: https://www.asme.org/about-asme/engineering-history/landmarks/8-pioneer-oil-refinery-california-star-oil-works, and the landmark brochure of 1975';
$ETHW = 'Engineering and Technology History Wiki, https://ethw.org/ASME-Landmark:Pioneer_Oil_Refinery_California_Star_Oil_Works';
$CHL = 'California Historical Landmark No. 172, registered 6 March 1935, Office of Historic Preservation, https://ohp.parks.ca.gov/ListedResources/Detail/172';
$NRHP = 'Pioneer Oil Refinery, National Register of Historic Places nomination, draft, GPA Consulting, 2020, https://web.archive.org/web/20201017002254id_/https://ohp.parks.ca.gov/pages/1067/files/CA_Los%20Angeles_Pioneer%20Oil%20Refinery_DRAFT.pdf';
$CALL_1917 = 'San Francisco Call, 30 July 1917, ' . $cdnc('SFC19170730.2.2');

/* ------------------------------------------------ the organization */
$ORG_FIELDS = [
    'dateFounded' => '1876',
    'dateFoundedEdtf' => '1876',
    'body' => implode("\n\n", [
        'The California Star Oil Works Company was a San Francisco firm that refined the oil of Pico Canyon. It grew out of the Star Oil Works, a partnership of D. C. Scott, R. C. McPherson and J. J. Baker, which leased the idle refinery at Lyons Station and in July 1875 employed Charles Alexander Mentry to drill for it.[1] In May 1876 the Star Oil Works Company filed articles of incorporation with the Los Angeles County Clerk, giving San Francisco as its place of business, and in July the California Star Oil Works Company, with a capital of $1,000,000, filed with the Secretary of State.[2] The date often given for the incorporation, 16 June 1876, has not been found in a source of the time.[2]',
        'Demetrius G. Scofield is often named as the company\'s founder and as the man who hired Mentry. The directors named in 1876 do not include him, and the earliest mention of him with the company found in the papers is at Ventura in May 1877; the 1963 National Park Service survey has him and F. B. Taylor coming into the business after it was formed. By January 1877 Taylor was its general manager.[3]',
        'The company refined at Lyons Station and, from September 1876, at a new works at Ventura.[4] In January 1877 it was selling kerosene under two names, Lustre, of 150 degrees fire test, and Prime White, of 110 degrees; Prime White was also a trade grade that other refiners sold.[5] In May 1877 it began a larger refinery at Andrews Station, in what became Newhall; the brickwork was finished in July, and oil was being refined there in August.[6] Its products that year were illuminating oil in two grades, the better sold as Lustre, a heavier mineral oil, light and dark lubricating oils, and some naphtha.[7] Later accounts list benzene; the papers of 1877 say naphtha.',
        'The 1930 plaque on the refinery, California Historical Landmark No. 172 and the ASME landmark brochure of 1975 all give the Newhall refinery the year 1876. The papers of 1877 and the 1963 survey give 1877, and the ASME brochure\'s own account of 1876 describes the Ventura works.[8] Kerosene refining at Newhall ended in the later 1880s; the dates given run from 1884 to 1890.[9]',
        'What the refinery was first at depends on who is speaking. The 1930 plaque calls it "California\'s first oil refinery operated on a commercial scale"; Landmark No. 172, "the first commercial oil refinery in California"; ASME, "the first successful commercial oil refinery in the US West"; the draft National Register nomination of 2020, "one of the oldest remaining oil refineries in the state, if not the nation."[10] That it is the oldest surviving refinery in the world is said in City of Santa Clarita tourism copy and in online encyclopedias, with no source; none of the landmark bodies says it.[11] Refineries worked earlier elsewhere, at Pittsburgh from 1853, in Poland and Romania from 1856, and at Salzbergen in Germany from 1860, where one still operates; whether any structure older than Newhall\'s survives has not been established. In California itself, the company\'s Lyons Station and Ventura works both refined before Newhall did, so "first" there means the first to last.[11]',
        'The Pacific Coast Oil Company, incorporated in 1879, took a controlling interest in the California Star Oil Works that year, and kept it as a subsidiary.[12] In December 1900 Standard Oil bought the Pacific Coast Oil Company; the California Star Oil Works was not the company it bought.[13] Standard Oil Company (California) was incorporated in 1906. Scofield, by then long with the Pacific Coast company, was its vice president from 1906 and its president from December 1911.[14] Standard restored the refinery in 1930, and it was given to the City of Santa Clarita in the late 1990s.[15]',
    ]),
    'footnotes' => $fn([
        $SNELL . ': the Star Oil Works partners "employed C. A. Mentry ... in July 1875." ' . $ASME . ': Mentry "was engaged by the former leasers of the refinery at Lyons Station."',
        'Los Angeles Herald, 25 May 1876, ' . $cdnc('LAH18760525.2.14') . ': the Star Oil Works Company "on Tuesday filed its articles of incorporation." Sacramento Daily Union, 10 July 1876, ' . $cdnc('SDU18760710.2.16') . ': the California Star Oil Works Company, filed with the Secretary of State, directors A. J. Bryant, Mark L. McDonald, Reuben Denton, R. C. Page and Charles Jones. ' . $SNELL . ': "reorganized their company in June 1876." No source of the time found gives 16 June. The articles themselves would be in the Secretary of State corporation files at the California State Archives.',
        'Ventura Free Press, 5 May 1877, ' . $cdnc('VFPW18770505.2.17') . ': "Mr. Scofield of the Star Oil Works Company, arrived here to-day." ' . $SNELL . ' on Taylor and Scofield joining. Ventura Signal, 27 January 1877, ' . $cdnc('VS18770127.2.8') . ', quoting F. B. Taylor as general manager. ' . $ASME . ' calls Scofield "an investor in California Star."',
        'Daily Alta California, 6 September 1876, ' . $cdnc('DAC18760906.2.9') . ', on the Ventura works starting; Daily Alta California, 27 December 1876, ' . $cdnc('DAC18761227.2.55.2') . ', the company\'s notice giving its works at Lyons Station and Ventura, offices at 312 California Street, San Francisco.',
        'Ventura Signal, 27 January 1877, ' . $cdnc('VS18770127.2.8') . ': "the celebrated Lustre Oil," 150 degrees, and "Prime White, 110 degree fire test." Los Angeles Herald, 15 May 1878: another refiner\'s "PRIME WHITE, 120 to 170 test."',
        'Santa Barbara Morning Press, 26 May 1877, ' . $cdnc('MP18770526.2.6') . ': "commenced the erection of a new large refinery at Andrew\'s Station." Ventura Signal, 21 July 1877, brickwork "completed," and 18 August 1877, "J. A. Scott is refining at Andrews Station," both as quoted by ' . $ELSMERE . '. ' . $SNELL . ': the site chosen "In 1877," built "during the summer."',
        'San Francisco Commercial, reprinted in the Santa Barbara Morning Press, 17 September 1877, ' . $cdnc('MP18770917.2.13') . '. Ventura Free Press, 6 October 1877, on Andrews Station: "some naptha is also being made," as quoted by ' . $ELSMERE . '. ' . $ASME . ', ' . $ETHW . ' and ' . $NRHP . ' say benzene.',
        '1876: the 1930 Standard Oil plaque ("Erected 1876"), ' . $CHL . ', ' . $ASME . ' ("completed in August 1876"), and ' . $NRHP . ', which follows ASME. 1877: the papers in notes 6 and 7, and ' . $SNELL . '.',
        '1884, online encyclopedias, uncited; April 1885, ' . $NRHP . '; about 1888, ' . $ASME . '; March 1890, White (1962), as quoted by ' . $ELSMERE . '. ASME quotes Scofield\'s own testimony that it "never averaged more than 750 gallons per day"; the testimony itself has not been found.',
        'The 1930 plaque, as transcribed by ' . $ELSMERE . '. ' . $CHL . '. ' . $ASME . ', web page; the brochure\'s cover says "The First Successful Refinery in California." ' . $NRHP . '.',
        'City of Santa Clarita, Old Town Newhall walking tour: "believed to be the oldest existing refinery in the world," no source given. The claim is not made by ' . $CHL . ', ' . $ASME . ', ' . $ETHW . ' or ' . $NRHP . '. The Salzbergen refinery, Lower Saxony, opened in 1860 and still operating, is the usual rival for oldest working refinery. On Lyons Station and Ventura, notes 4 and 5.',
        'Sacramento Daily Union, 20 February 1879, on the Pacific Coast Oil Company\'s articles. ' . $ASME . ': it "acquired a controlling interest" in 1879. White (1962), as quoted by ' . $ELSMERE . ', has the Star company liquidated by the Pacific Coast company in 1901.',
        'San Francisco Call, 11 December 1900, ' . $cdnc('SFC19001211.2.27') . ': Standard "acquires all of the interests of the Pacific Coast Oil Company," for "in the neighborhood of" $1,000,000.',
        'Santa Barbara Morning Press, 6 November 1906, ' . $cdnc('MPE19061106.2.6') . ': Standard Oil Company (California) "only recently incorporated at Sacramento." ' . $CALL_1917 . ': Scofield "became vice president ... in 1906, and was elected president in 1911." San Francisco Call, 6 December 1911: "D. G. Scofield, former vice president, was elected president."',
        $ELSMERE . '. The year of the gift to the City is given as 1997 and as 1998.',
    ]),
];

/* ------------------------------------------------ the place: location and landmark */
$PLACE_FIELDS = [
    'placeAddress' => '23864 Pine Street, Newhall',
    'placeLat' => '34.369659',
    'placeLng' => '-118.519869',
    'placeChlNumber' => '172',
    'placeChlUrl' => 'https://ohp.parks.ca.gov/ListedResources/Detail/172',
    'placeWikipediaUrl' => 'https://en.wikipedia.org/wiki/Pioneer_Oil_Refinery',
    'footnotes' => $fn([
        'Address: ' . $ASME . '; ' . $CHL . '. Other listings give 23802 Pine Street. Coordinates: ' . $NRHP . '. Other published coordinates differ by up to about 90 metres.',
    ]),
];

/* ------------------------------------------------ Scofield */
$SCOFIELD = 'Demetrius G. Scofield';
$SCOFIELD_FIELDS = [
    'fullName' => 'Demetrius G. Scofield',
    'personAliases' => "D. G. Scofield\nD.G. Scofield",
    'occupation' => 'Oil company executive',
    'birthplace' => 'New York City',
    'birthDate' => 'about 1843',
    'birthDateEdtf' => '1842-07-31/1843-07-30',
    'birthEvidence' => 'contemporary',
    'deathDate' => 'July 30, 1917',
    'deathDateEdtf' => '1917-07-30',
    'deathEvidence' => 'contemporary',
    'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/D._G._Scofield',
    'bodyAuthorship' => 'editorial-2026',
    'recordProvenance' => 'editorial-2026, build_california_star_oil_works.php, 29 September 2026, from the sources in its footnotes',
    'body' => implode("\n\n", [
        'Demetrius G. Scofield was an oil man with the California Star Oil Works Company and the Pacific Coast Oil Company, and from 1911 president of Standard Oil Company (California). He was born in New York City.[1] His age at death, 74, puts his birth between mid-1842 and mid-1843; later accounts give 28 January 1843, which has not been found in a record.[1]',
        'The earliest mention of him with the Star Oil Works found in the papers is at Ventura in May 1877.[2] He was vice president of Standard Oil Company (California) from 1906 and was elected its president on 5 December 1911.[3] After Charles Alexander Mentry died in October 1900, Scofield delivered the eulogy the archive holds as record #20104.[4] He died in Oakland on 30 July 1917.[5]',
        'Later accounts, including the note on the eulogy\'s legacy page, call him the founder of the California Star Oil Works and the first president of Standard Oil of California. The company\'s directors of 1876 do not include him, and in 1906 he was its vice president.[6]',
    ]),
    'footnotes' => $fn([
        $CALL_1917 . ': "born in New York City." San Jose Mercury News, 31 July 1917: "74 years old." 28 January 1843 is given by Find a Grave and by online encyclopedias, citing no record.',
        'Ventura Free Press, 5 May 1877, ' . $cdnc('VFPW18770505.2.17') . '.',
        $CALL_1917 . '; San Francisco Call, 6 December 1911. Letters of 1908 and May 1911 are signed "D. G. Scofield, Vice President."',
        'Record #20104, "Demetrius Scofield\'s Eulogy to Charles Alexander Mentry."',
        $CALL_1917 . '; Santa Rosa Press Democrat, 31 July 1917, dateline "Oakland, July 30"; San Jose Mercury News, 31 July 1917.',
        'Los Angeles Herald, 25 May 1876, ' . $cdnc('LAH18760525.2.14') . '; Sacramento Daily Union, 10 July 1876, ' . $cdnc('SDU18760710.2.16') . '; ' . $SNELL . '. On 1906, note 3.',
    ]),
    'editorNotes' => [[
        'heading' => 'Not yet established',
        'note' => 'His middle name, given elsewhere as Gustavus, has not been found in a record of his life. The 1917 Call gives his coming to the coast as 1870, which may be a misreading of the scan; other accounts say about 1875.',
        'position' => 'bottom',
    ]],
];

/* ------------------------------------------------ checks and plan */
$org = $get($ORG); $place = $get($PLACE); $mentry = $get($MENTRY); $eulogy = $get($EULOGY);
if (!$org || $org->title !== 'California Star Oil Works') { echo 'REFUSING: #' . $ORG . ' is not California Star Oil Works' . PHP_EOL; return; }
if (!$place || $place->title !== 'Pioneer Oil Refinery' || $place->section->handle !== 'places') { echo 'REFUSING: #' . $PLACE . ' is not the place Pioneer Oil Refinery' . PHP_EOL; return; }
if (!$mentry || !$eulogy) { echo 'REFUSING: Mentry #' . $MENTRY . ' or the eulogy #' . $EULOGY . ' is missing' . PHP_EOL; return; }

$isEmpty = function ($el, $h) {
    $v = $el->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if (is_array($v)) { return !array_filter($v, fn($r) => is_array($r) && array_filter($r, fn($c) => $c !== null && $c !== '' && $c !== false)); }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    return trim((string)$v) === '';
};
$show = fn($v) => is_array($v) ? count($v) . ' row(s)' : (mb_strlen($v) > 100 ? mb_substr($v, 0, 100) . '... (' . mb_strlen($v) . ' chars)' : $v);
$fill = function ($el, array $fields, string $label) use ($isEmpty, $show) {
    $set = [];
    foreach ($fields as $h => $v) {
        if (!$el->getFieldLayout()->getFieldByHandle($h)) { echo "   $label: REFUSING field $h, not on the layout" . PHP_EOL; continue; }
        if ($isEmpty($el, $h)) { $set[$h] = $v; echo '   ' . str_pad($h, 20) . $show($v) . PHP_EOL; }
        else { echo '   ' . str_pad($h, 20) . '(already set, kept)' . PHP_EOL; }
    }
    return $set;
};

echo "ORGANIZATION #$ORG California Star Oil Works" . PHP_EOL;
$setOrg = $fill($org, $ORG_FIELDS, 'org');
$aliases = array_values(array_filter(array_map('trim', preg_split('~[,\n;]~', (string)$org->orgAliases))));
$addAlias = in_array('Star Oil Works', $aliases, true) ? [] : ['Star Oil Works'];
if ($addAlias) { echo '   orgAliases          + Star Oil Works (keeps ' . implode(', ', $aliases) . ')' . PHP_EOL; }

echo PHP_EOL . "PLACE #$PLACE Pioneer Oil Refinery" . PHP_EOL;
$setPlace = $fill($place, $PLACE_FIELDS, 'place');
$placeOrgs = $place->placeOrganizations->status(null)->ids();
echo '   placeOrganizations  ' . (in_array($ORG, array_map('intval', $placeOrgs), true) ? 'already names #' . $ORG . ', so the organization page lists the place and takes its map' : 'add #' . $ORG) . PHP_EOL;

echo PHP_EOL . "PERSON $SCOFIELD" . PHP_EOL;
$others = array_filter(Entry::find()->section('persons')->status(null)->search('Scofield')->all(), fn($e) => $e->title !== $SCOFIELD);
if ($others) { echo 'REFUSING: other person records name Scofield: ' . implode(', ', array_map(fn($e) => '#' . $e->id . ' ' . $e->title, $others)) . PHP_EOL; return; }
$sco = Entry::find()->section('persons')->status(null)->title($SCOFIELD)->one();
echo '   ' . ($sco ? 'exists as #' . $sco->id : 'create') . PHP_EOL;
$scoLayout = $svc->getEntryTypeByHandle('person')->getFieldLayout();
foreach (array_keys($SCOFIELD_FIELDS) as $h) { if (!$scoLayout->getFieldByHandle($h)) { echo 'REFUSING: the person layout lacks ' . $h . PHP_EOL; return; } }
if ($sco) { $setSco = $fill($sco, $SCOFIELD_FIELDS, 'scofield'); }
else { $setSco = $SCOFIELD_FIELDS; foreach ($setSco as $h => $v) { echo '   ' . str_pad($h, 20) . $show($v) . PHP_EOL; } }
echo '   personOrganizations + #' . $ORG . ' California Star Oil Works' . PHP_EOL;
echo PHP_EOL . "ORGANIZATION #$ORG orgAssociatedPersons: + $SCOFIELD, + #$MENTRY Charles Alexander Mentry" . PHP_EOL;

echo PHP_EOL . "LEFTOVER #$OLD_ORG" . PHP_EOL;
$old = $get($OLD_ORG);
$oldIn = (int)(new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->where(['r.targetId' => $OLD_ORG, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->count();
$deleteOld = false;
if (!$old) { echo '   already gone' . PHP_EOL; }
elseif ($old->title !== 'Pioneer Oil Refinery' || $old->section->handle !== 'organizations' || $old->enabled) { echo '   REFUSING: #' . $OLD_ORG . ' is not the disabled organization Pioneer Oil Refinery' . PHP_EOL; }
elseif ($oldIn) { echo '   REFUSING: ' . $oldIn . ' record(s) still point at it' . PHP_EOL; }
else { $deleteOld = true; echo '   disabled organization "Pioneer Oil Refinery", nothing points at it; to the trash (restorable). Its content was carried to place #' . $PLACE . ' by convert_orgs_to_places.php.' . PHP_EOL; }

echo PHP_EOL . 'NOT DONE: lw3555, which needs the mirror (Reggie is not mounted).' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* ------------------------------------------------ apply */
if (!$sco) {
    $sco = new Entry();
    $sco->sectionId = $svc->getSectionByHandle('persons')->id;
    $sco->setTypeId($svc->getEntryTypeByHandle('person')->id);
    $sco->title = $SCOFIELD;
}
$sco->setFieldValues($setSco);
$sco->setFieldValue('personOrganizations', array_values(array_unique(array_merge(array_map('intval', $sco->id ? $sco->personOrganizations->status(null)->ids() : []), [$ORG]))));
if (!$elements->saveElement($sco)) { throw new \RuntimeException('build_csow: Scofield ' . json_encode($sco->getFirstErrors())); }

$org = $get($ORG);
$org->setFieldValues($setOrg);
if ($addAlias) { $org->setFieldValue('orgAliases', implode(', ', array_merge($aliases, $addAlias))); }
$org->setFieldValue('orgAssociatedPersons', array_values(array_unique(array_merge(array_map('intval', $org->orgAssociatedPersons->status(null)->ids()), [$sco->id, $MENTRY]))));
if (!$elements->saveElement($org)) { throw new \RuntimeException('build_csow: org ' . json_encode($org->getFirstErrors())); }

$place = $get($PLACE);
$place->setFieldValues($setPlace);
$place->setFieldValue('placeOrganizations', array_values(array_unique(array_merge(array_map('intval', $place->placeOrganizations->status(null)->ids()), [$ORG]))));
if (!$elements->saveElement($place)) { throw new \RuntimeException('build_csow: place ' . json_encode($place->getFirstErrors())); }

if ($deleteOld && !$elements->deleteElement($get($OLD_ORG))) { throw new \RuntimeException('build_csow: could not trash #' . $OLD_ORG); }

/* ------------------------------------------------ read back */
$short = [];
$o = $get($ORG); $pl = $get($PLACE); $s = Entry::find()->section('persons')->status(null)->title($SCOFIELD)->one();
if (isset($setOrg['body']) && trim((string)$o->body) !== $ORG_FIELDS['body']) { $short[] = 'org body differs'; }
$nfo = count(array_filter($o->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if (isset($setOrg['footnotes']) && $nfo !== count($ORG_FIELDS['footnotes'])) { $short[] = "org footnotes read $nfo"; }
foreach ([$s->id, $MENTRY] as $pid) { if (!in_array($pid, array_map('intval', $o->orgAssociatedPersons->status(null)->ids()), true)) { $short[] = "org lacks associated person #$pid"; } }
foreach (['placeLat', 'placeLng', 'placeChlNumber'] as $h) { if (isset($setPlace[$h]) && trim((string)$pl->getFieldValue($h)) !== $setPlace[$h]) { $short[] = "place $h reads " . $pl->getFieldValue($h); } }
if (!$s) { $short[] = 'Scofield missing'; }
elseif (trim((string)$s->deathDateEdtf) !== '1917-07-30') { $short[] = 'Scofield death date reads ' . $s->deathDateEdtf; }
if ($deleteOld && Entry::find()->id($OLD_ORG)->status(null)->exists()) { $short[] = "#$OLD_ORG still live"; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: organization built out, place located, Scofield #' . $s->id . ', #' . $OLD_ORG . ' trashed') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('build_california_star_oil_works.php', 4, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'CSOW #16039 body and 15 footnotes; place #20131 location; Scofield created; #16201 trashed');
if ($short) { throw new \RuntimeException('build_california_star_oil_works: ' . implode('; ', $short)); }
