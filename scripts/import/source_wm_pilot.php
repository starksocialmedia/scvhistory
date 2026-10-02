/**
 * The war memorial sourcing pilot: five records, one per conflict (Nathan,
 * 2 October 2026: "Approve the memorial pilot, five records, one per conflict.
 * Read all three source sets ... two independent sources for each death, one
 * federal and one tying him to the valley").
 *
 *   World War I    #1384 Edward Guy North
 *   World War II   #568  Johnny Cordova
 *   Korea          #552  Henry Acuna
 *   Vietnam        #1360 Charles Clarence Smith Jr.
 *   War on Terror  #544  Cole William Larsen
 *
 * THE SOURCES are in inventory/sources/wm-pilot-2026-10-02.json, each with what
 * it says; this script checks the phrases it relies on there.
 *
 * WHAT IS WRITTEN, per record:
 *   footnotes     the sources, numbered (the records had none);
 *   factSources   one row per fact: value, the notes that support it, and
 *                 where the sources differ, said rather than settled;
 *   editorNotes   "Sources, 2026": whether the record meets the two-source
 *                 rule; for North, a "Correction, 2026" on his date of death.
 * DATA FIELDS changed only where the sources settle them, never the prose:
 *   North    deathDate 04/27/1919 -> April 7, 1919 (the Honor Roll card, and
 *            the Los Angeles Times of Wednesday April 9 saying "Monday"),
 *            with deathDateEdtf; the legacy text keeps 04/27/1919 and the
 *            correction note says why.
 *   Cordova  wmUnit "32 Inf.; 7 Inv. Div." -> "32 Inf.; 7 Inf. Div." (typo).
 *   Smith    wmAwards and wmHighSchool, empty, filled from the Signal.
 * Idempotent: a record whose factSources is already set is left alone.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_pilot.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$SRC = json_decode((string)@file_get_contents("$root/inventory/sources/wm-pilot-2026-10-02.json"), true) ?: [];
$flat = json_encode($SRC, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$bad = [];
foreach (['June 25th 1890', 'Newhall, California, USA', '"dateOfDeath":"April 7, 1919"', 'at Camp Kearny, Monday', 'CORDOVA JOHN 39730703 PVT DOW', 'the little Ruiz burying ground in San Francisquito Canyon', '"home":"Saugus, California"', 'USA - RA (Reg Army)', 'V Reserve', 'A 1967 graduate of Hart High', 'the Bronze Star and the Bronze Star with the \\"V\\" Device (First Oak Leaf Cluster)', 'Pfc. Cole W. Larsen, 19, of Canyon Country, Calif., died Nov. 13 in Baghdad, Iraq', 'Panel 26W, Line 4'] as $ph) {
    if (!str_contains($flat, $ph)) { $bad[] = 'the sources file does not read ' . mb_substr($ph, 0, 50); }
}
$LEGACY = fn($path) => "SCVHistory.com, Santa Clarita Valley War Memorial, page for him ($path), as migrated to this record.";
$R = [
    1384 => [
        'title' => 'Edward Guy North', 'expect' => ['deathDate' => '04/27/1919', 'wmServiceId' => '2285457'],
        'set' => ['deathDate' => 'April 7, 1919', 'deathDateEdtf' => '1919-04-07'],
        'notes' => [
            'World War I draft registration card, Edward Guy North, registered June 5, 1917, Nye County, Nevada (National Archives, Record Group 163), held in this archive: born June 25, 1890, at Newhall, California; living and working in Tonopah, Nevada.',
            'California War History Committee, State Council of Defense, "California\'s Honor Roll" card for Corp. Edward Guy North, completed by his mother, Mary E. North, September 20, 1920, held in this archive: serial number 2285457; Co. D, Machine Gun Battalion 347, 91st Division; died April 7, 1919, at Camp Kearny, of a gunshot wound, "shot while in charge of machine-gun in the fighting at Epmonville in the battle of Monfacon."',
            'Los Angeles Times, Wednesday, April 9, 1919, "For Argonne Hero," held in this archive: Corp. Edward G. North, Company D, Machine Gun Battalion 347, Ninety-first Division, "who died at Camp Kearny, Monday, from surgical shock."',
            $LEGACY('/scvhistory/ww1_edwardguynorth.htm'),
        ],
        'facts' => [
            ['Born', 'June 25, 1890', '1, 2', ''],
            ['Birthplace', 'Newhall, California', '1, 2', ''],
            ['Service number', '2285457', '2', ''],
            ['Rank', 'Corporal', '2, 3', ''],
            ['Unit', 'Co. D, Machine Gun Battalion 347, 91st Infantry Division', '2, 3', ''],
            ['Wounded', 'Epinonville, France, late September 1918, in the Meuse-Argonne offensive', '2, 4', 'The Honor Roll card spells the village Epmonville and names the battle of Montfaucon; the date is the legacy page\'s.'],
            ['Date of death', 'April 7, 1919', '2, 3', 'The legacy page gives 04/27/1919. The Honor Roll card gives April 7, 1919, and the Los Angeles Times of Wednesday, April 9, 1919, says he died "Monday," which was April 7.'],
            ['Place of death', 'Camp Kearny, San Diego County', '2, 3', ''],
            ['Cause of death', 'Gunshot wound; surgical shock after the operation', '2, 3', ''],
            ['Home, 1919', 'South Cummings Street, Los Angeles', '2, 3', 'The Honor Roll card gives number 441; the Times gives 414.'],
            ['Burial', 'Grand View Memorial Park, Glendale (Section M, Lot 162, Grave 13)', '4', 'From the legacy page and its grave photograph only.'],
        ],
        'editor' => [
            ['Correction, 2026', 'Edward Guy North died on April 7, 1919, not April 27 as the record above gives it. His Honor Roll card gives April 7, and the Los Angeles Times of Wednesday, April 9, 1919, reports that he died at Camp Kearny "Monday," which was April 7 (notes 2 and 3).'],
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. His tie to the valley is federal: his draft card gives his birthplace as Newhall. But no federal record of his death has been found online: he died in a camp in California, so he is not in the American Battle Monuments Commission\'s registers, and the War Department\'s burial case files are not online. His death rests on a State of California record and a newspaper.'],
        ],
    ],
    568 => [
        'title' => 'Johnny Cordova', 'expect' => ['wmServiceId' => '39730703', 'wmUnit' => '32 Inf.; 7 Inv. Div.'],
        'set' => ['wmUnit' => '32 Inf.; 7 Inf. Div.'],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "CORDOVA JOHN 39730703 PVT DOW" (died of wounds).',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39730703, entered as "Cardova, John": enlisted October 13, 1944, at Fort MacArthur, San Pedro; Los Angeles County; born 1923.',
            'The Newhall Signal, Thursday, March 24, 1949, "Soldier is borne to last resting place among own native hills," held in this archive: his reburial in "the little Ruiz burying ground in San Francisquito Canyon," with the Castaic Legion Post as bearers.',
            $LEGACY('/warmemorial/ww2_johnnycordova.htm'),
        ],
        'facts' => [
            ['Service number', '39730703', '1, 2', ''],
            ['Rank', 'Private First Class', '4', 'The Honor List and the enlistment record give Private. A promotion after October 1944 would explain the difference; no record of one has been seen.'],
            ['Casualty', 'Died of wounds', '1, 4', ''],
            ['Date of death', 'June 18, 1945', '4', 'The Honor List gives no date.'],
            ['Place', 'Okinawa, Japan', '4', ''],
            ['Born', 'June 13, 1923', '2, 4', 'The enlistment record gives the year only.'],
            ['Home', 'Castaic', '2, 3, 4', 'The enlistment record gives Los Angeles County.'],
            ['Unit', '32nd Infantry, 7th Infantry Division', '4', ''],
            ['Burial', 'Ruiz-Perea Cemetery, San Francisquito Canyon, March 1949', '3, 4', ''],
        ],
        'editor' => [['Sources, 2026', 'This record meets the archive\'s rule for a memorial record: the War Department\'s Honor List records his death of wounds (note 1), and the Newhall Signal records his burial in San Francisquito Canyon (note 3).']],
    ],
    552 => [
        'title' => 'Henry Acuna', 'expect' => ['wmServiceId' => 'RA-19-348-948', 'deathDate' => '7-31-1950'],
        'set' => [],
        'notes' => [
            'War Department, The Adjutant General\'s Office, Final Report of Death No. 5295, Henry Acuna, October 23, 1950, held in this archive: RA 19 348 948, PFC E-3, Infantry; home address Saugus, California; born July 13, 1929; killed in action in Korea, July 31, 1950; carried as missing in action from July 31 until October 14, 1950.',
            'National Archives, Korean War Casualty File (Record Group 407): RA19348948, Private First Class, Infantry, 29th Infantry Regiment; killed in action, previously missing in action, South Korea, July 31, 1950; Los Angeles County; Regular Army.',
            'National Archives, Korean Conflict Casualty File (Record Group 330): 19348948, PFC, died July 31, 1950, hostile; home of record Los Angeles, California; component given as Reserve.',
            $LEGACY('/warmemorial/korea_henryacuna.htm'),
        ],
        'facts' => [
            ['Service number', 'RA-19-348-948', '1, 2, 3', 'The Defense Department\'s file (note 3) lists him as a reservist; the Army\'s own file and the Report of Death give RA, Regular Army.'],
            ['Rank', 'Private First Class (E-3)', '1, 2, 3', ''],
            ['Unit', '29th Infantry Regiment', '1, 2', ''],
            ['Born', 'July 13, 1929', '1', 'The two National Archives files give the year, 1929.'],
            ['Home', 'Saugus', '1', 'The National Archives files give Los Angeles County.'],
            ['Entered service', 'February 10, 1949', '1', ''],
            ['Date of death', 'July 31, 1950', '1, 2, 3', ''],
            ['Casualty', 'Missing in action from July 31, 1950; killed in action, established October 14, 1950', '1, 2', ''],
            ['Place', 'Korea', '1, 2', ''],
            ['Burial', 'Glen Haven Memorial Park, Sylmar', '4', 'From the legacy page and its grave photograph only.'],
        ],
        'editor' => [['Sources, 2026', 'This record meets the archive\'s rule for a memorial record, both ways in one document: the War Department\'s Report of Death (note 1) is federal and gives his home as Saugus.']],
    ],
    1360 => [
        'title' => 'Charles Clarence Smith Jr.', 'expect' => ['wmServiceId' => '18862518', 'wmWallReference' => 'Panel 26W Line 004'],
        'fill' => ['wmAwards' => 'Bronze Star; Bronze Star with "V" device (First Oak Leaf Cluster)', 'wmHighSchool' => 'Hart High School (class of 1967)'],
        'set' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Smith, Charles Clarence Jr., 18862518, SP4, Armor Crewman (11E20); home of record Saugus, California; born August 31, 1949; tour began March 2, 1969; died April 18, 1969, Quang Tri Province, hostile, multiple fragmentation wounds; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): C Troop, 3rd Squadron, 5th Cavalry, 9th Infantry Division; home of record Saugus; Vietnam Veterans Memorial, Panel 26W, Line 4.',
            'The Signal, Friday, September 19, 1969, "Vietnam Victim Honored," held in this archive: Charles C. Smith Jr. of Saugus, killed April 18; the Bronze Star and the Bronze Star with "V" device (First Oak Leaf Cluster) presented to his parents; Troop C, Third Squadron, Fifth Cavalry, Quang Tri Province; "A 1967 graduate of Hart High."',
            $LEGACY('/scvhistory/vietnam_charlessmith.htm'),
        ],
        'facts' => [
            ['Service number', '18862518', '1, 2', ''],
            ['Rank', 'Specialist Four (E4)', '1, 2, 3', ''],
            ['Specialty', '11E20 Armor Crewman', '1, 2', ''],
            ['Unit', 'C Troop, 3rd Squadron, 5th Cavalry, 9th Infantry Division', '2, 3', ''],
            ['Born', 'August 31, 1949', '1, 2', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Tour began', 'March 2, 1969', '1, 2', ''],
            ['Date of death', 'April 18, 1969', '1, 2, 3', ''],
            ['Place', 'Quang Tri Province, South Vietnam', '1, 2, 3', ''],
            ['Cause', 'Hostile; multiple fragmentation wounds', '1, 2', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field holds "Body Recovered," which is the remains status in the federal file, not a place; where he is buried has not been found.'],
            ['Awards', 'Bronze Star; Bronze Star with "V" device (First Oak Leaf Cluster)', '3', ''],
            ['High school', 'Hart High School, class of 1967', '3', ''],
            ['Vietnam Veterans Memorial', 'Panel 26W, Line 4', '2, 4', ''],
        ],
        'editor' => [['Sources, 2026', 'This record meets the archive\'s rule for a memorial record: the federal casualty file records his death and gives his home of record as Saugus (note 1), and the Signal reported it in the valley (note 3).']],
    ],
    544 => [
        'title' => 'Cole William Larsen', 'expect' => ['wmUnit' => '272nd Military Police Company, 21st Theater Support Command', 'deathDate' => '11-13-2004'],
        'set' => [],
        'notes' => [
            'Department of Defense, News Release No. 1179-04, "DoD Identifies Army Casualty," November 17, 2004 (defenselink.mil, read in the Internet Archive copy of December 28, 2004): "Pfc. Cole W. Larsen, 19, of Canyon Country, Calif., died Nov. 13 in Baghdad, Iraq, when a civilian vehicle struck his military vehicle causing it to roll over. Larsen was assigned to the 272nd Military Police Company, 21st Theater Support Command, Mannheim, Germany."',
            'National Archives, Defense Casualty Analysis System Public Use File, 1950-2005 (Record Group 330): Larsen, Cole William, PFC, Army; born May 1, 1985; home of record Canyon Country, Los Angeles County; died November 13, 2004, Camp Volunteer, Iraq; non-hostile, vehicle crash; Operation Iraqi Freedom.',
            'Photograph of his cenotaph marker, held in this archive: "Cole William Larsen, PFC US Army Iraq, May [day obscured] 1985, Nov 13 2004, Bronze Star Medal, Operation Iraqi Freedom."',
            $LEGACY('/warmemorial/terror_colelarsen.htm'),
        ],
        'facts' => [
            ['Rank', 'Private First Class', '1, 2, 3', ''],
            ['Unit', '272nd Military Police Company, 21st Theater Support Command', '1, 2', ''],
            ['Base', 'Mannheim, Germany', '1', ''],
            ['Born', 'May 1, 1985', '2, 3', ''],
            ['Home', 'Canyon Country', '1, 2', ''],
            ['Date of death', 'November 13, 2004', '1, 2, 3', ''],
            ['Place', 'Baghdad, Iraq (Camp Volunteer)', '1, 2', ''],
            ['Circumstances', 'Non-hostile: a civilian vehicle struck his military vehicle, which rolled over', '1, 2', ''],
            ['Operation', 'Operation Iraqi Freedom', '1, 2, 3', ''],
            ['Awards', 'Bronze Star', '3, 4', ''],
            ['High school', 'Canyon High School, Canyon Country', '4', ''],
            ['Memorial', 'Cenotaph, Eternal Valley Cemetery, Newhall', '3, 4', 'The photograph shows the marker; its place is from the legacy page.'],
        ],
        'editor' => [['Sources, 2026', 'This record meets the archive\'s rule for a memorial record: the Defense Department\'s release and casualty file record his death and give his home as Canyon Country (notes 1 and 2), and his cenotaph is in the valley (note 3, with its place from the legacy page).']],
    ],
];
$plan = [];
foreach ($R as $id => $r) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || $e->title !== $r['title']) { $bad[] = "#$id is not {$r['title']}"; continue; }
    if (!empty($e->factSources)) { echo "#$id {$r['title']}: already sourced, nothing to do" . PHP_EOL; continue; }
    if (array_filter($e->footnotes ?? [], fn($x) => is_array($x) && trim((string)($x['note'] ?? '')) !== '')) { $bad[] = "#$id already has footnotes; they would need merging"; continue; }
    if (!str_contains(end($r['notes']), '(' . $e->legacyUrl . ')')) { $bad[] = "#$id cites a legacy page that is not its legacyUrl ({$e->legacyUrl})"; }
    foreach ($r['expect'] as $k => $v) { if (trim((string)$e->getFieldValue($k)) !== $v) { $bad[] = "#$id $k is \"" . $e->getFieldValue($k) . "\", not \"$v\""; } }
    $set = $r['set'];
    foreach ($r['fill'] ?? [] as $k => $v) { if (trim((string)$e->getFieldValue($k)) === '') { $set[$k] = $v; } }
    foreach ($r['facts'] as [$f, $v, $n, $a]) { foreach (array_map('intval', explode(',', $n)) as $i) { if ($i < 1 || $i > count($r['notes'])) { $bad[] = "#$id fact \"$f\" cites note $i, which does not exist"; } } }
    $plan[$id] = [$r, $set];
    echo "#$id {$r['title']}: " . count($r['notes']) . ' notes, ' . count($r['facts']) . ' facts (' . count(array_filter($r['facts'], fn($x) => $x[3] !== '')) . ' with a difference), ' . count($r['editor']) . ' editor notes' . ($set ? '; fields: ' . implode(', ', array_map(fn($k, $v) => "$k -> \"$v\"", array_keys($set), $set)) : '') . PHP_EOL;
}
if (preg_match('~\x{2014}~u', json_encode($R, JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
echo PHP_EOL . count($plan) . ' records to source' . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = []; $tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $id => [$r, $set]) {
        $e = Entry::find()->id($id)->status(null)->one();
        $ed = array_values(array_map(fn($x) => ['heading' => (string)($x['heading'] ?? ''), 'position' => (string)($x['position'] ?? 'bottom'), 'note' => (string)($x['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($x) => is_array($x) && trim((string)($x['note'] ?? '')) !== '')));
        foreach ($r['editor'] as [$h, $n]) { $ed[] = ['heading' => $h, 'position' => 'bottom', 'note' => $n]; }
        $e->setFieldValues($set + [
            'footnotes' => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($r['notes']), $r['notes']),
            'factSources' => array_map(fn($x) => ['fact' => $x[0], 'value' => $x[1], 'notes' => $x[2], 'agreement' => $x[3]], $r['facts']),
            'editorNotes' => $ed,
        ]);
        if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
foreach ($plan as $id => [$r, $set]) {
    $b = Entry::find()->id($id)->status(null)->one();
    if (count($b->factSources ?? []) !== count($r['facts']) || count($b->footnotes ?? []) !== count($r['notes'])) { $short[] = "#$id tables"; }
    foreach ($set as $k => $v) { if (trim((string)$b->getFieldValue($k)) !== $v) { $short[] = "#$id $k"; } }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records sourced') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('source_wm_pilot.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial sourcing pilot: North, Cordova, Acuna, Smith, Larsen; footnotes, facts and their sources, North\'s date of death corrected to April 7, 1919');
if ($short) { throw new \RuntimeException('source_wm_pilot: ' . implode(', ', $short)); }
