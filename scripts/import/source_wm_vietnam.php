/**
 * War memorial sourcing, Vietnam (Nathan, 2 October 2026). Thirteen records;
 * Charles Clarence Smith Jr. was in the pilot.
 *
 * THE SOURCES (inventory/sources/wm-vietnam-2026-10-02.json): NARA's Combat
 * Area Casualties Current File and the Coffelt Database. Every one gives a
 * valley home of record (Newhall or Saugus), so each record meets the
 * two-source rule in one federal document, as Henry Acuna's did.
 *
 * BURIAL FIELD: on all fourteen Vietnam records burialPlace held "Body
 * recovered", the remains status the legacy pages took from the casualty file,
 * not a place. It is cleared, and the status is a fact row ("Remains"), so
 * the page no longer prints it under BURIAL. Smith's (#1360), sourced in the
 * pilot, is cleared here too.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_vietnam.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$flat = json_encode(json_decode((string)@file_get_contents("$root/inventory/sources/wm-vietnam-2026-10-02.json"), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$bad = [];
foreach (['"sn":"2349671"', '"prov":"Kien Hoa"', '"reason":"Misadventure"', '"home":"SAUGAS (as spelled; Coffelt gives SAUGUS)"', '"sn":"W3160264"'] as $ph) { if (!str_contains($flat, $ph)) { $bad[] = 'the sources file does not read ' . $ph; } }
/* burialPlace "Body recovered" on every Vietnam record, Smith's included. */
$burial = [];
foreach ([1356, 1358, 1360, 1362, 1364, 1366, 1368, 1370, 1372, 1374, 1376, 1378, 1380, 1382] as $id) { $x = Entry::find()->id($id)->status(null)->one(); if (strcasecmp(trim((string)$x->burialPlace), 'Body recovered') === 0) { $burial[] = $id; } }
echo count($burial) . ' burial fields hold "Body recovered" and will be cleared' . PHP_EOL;
$LEGACY = fn($path) => "SCVHistory.com, Santa Clarita Valley War Memorial, page for him ($path), as migrated to this record.";
$R = [
    1356 => [
        'title' => 'Bruce Wayne St. Louis', 'expect' => ['wmServiceId' => '2349671'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Bruce Wayne St. Louis, 2349671, USMC, PFC, Private First Class; hostile, killed, December 19, 1967; reason: explosive device (grenade, mine, booby trap); province: Quang Nam; home of record Newhall, California; born January 9, 1949; tour began November 20, 1967; component Regular; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit H Co, 2nd Bn, 5th Marines, 1st Marine Division; Vietnam Veterans Memorial, panel 32E, line 27.',
            $LEGACY('/scvhistory/vietnam_brucestlouis.htm'),
        ],
        'facts' => [
            ['Service number', '2349671', '1, 3', ''],
            ['Rank', 'Private First Class', '1, 3', ''],
            ['Unit', 'H Co, 2nd Bn, 5th Marines, 1st Marine Division', '2, 3', ''],
            ['Born', 'January 9, 1949', '1, 3', ''],
            ['Tour began', 'November 20, 1967', '1, 3', ''],
            ['Date of death', 'December 19, 1967', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Explosive device (grenade, mine, booby trap)', '1', ''],
            ['Place', 'Quang Nam Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Newhall', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 32E, line 27', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Newhall (note 1).'],
        ],
    ],
    1358 => [
        'title' => 'Carl Leonard Radtke', 'expect' => ['wmServiceId' => 'O5425777'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Carl Leonard Radtke, O5425777, Army, 1LT, First Lieutenant; hostile, killed, March 2, 1969; reason: artillery or rocket; province: Kien Hoa; home of record Newhall, California; born July 7, 1942; tour began May 4, 1968; component Reserve; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit 191st Aviation Company, 1st Aviation Brigade; Vietnam Veterans Memorial, panel 30W, line 15.',
            $LEGACY('/scvhistory/vietnam_carlradtke.htm'),
        ],
        'facts' => [
            ['Service number', 'O5425777', '1, 3', ''],
            ['Rank', 'First Lieutenant', '1, 3', ''],
            ['Unit', '191st Aviation Company, 1st Aviation Brigade', '2, 3', ''],
            ['Born', 'July 7, 1942', '1, 3', ''],
            ['Tour began', 'May 4, 1968', '1, 3', ''],
            ['Date of death', 'March 2, 1969', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Artillery or rocket', '1', 'The record gives a ground casualty.'],
            ['Place', 'Kien Hoa Province, South Vietnam', '1, 2, 3', 'The record gives Dinh Tuong Province; both federal files give Kien Hoa.'],
            ['Home', 'Newhall', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 30W, line 15', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Newhall (note 1).'],
        ],
    ],
    1362 => [
        'title' => 'David Lee Reeder', 'expect' => ['wmServiceId' => '19771585'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): David Lee Reeder, 19771585, Army, SGT, Sergeant; hostile, killed, November 19, 1965; reason: gunshot or small arms fire; province: not recorded; home of record Saugus, California; born August 14, 1944; tour began June 25, 1965; component Regular; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit A Co, 1st Bn, 18th Infantry, 1st Infantry Division; Vietnam Veterans Memorial, panel 03E, line 101.',
            $LEGACY('/scvhistory/vietnam_davidreeder.htm'),
        ],
        'facts' => [
            ['Service number', '19771585', '1, 3', ''],
            ['Rank', 'Sergeant', '1, 3', ''],
            ['Unit', 'A Co, 1st Bn, 18th Infantry, 1st Infantry Division', '2, 3', ''],
            ['Born', 'August 14, 1944', '1, 3', ''],
            ['Tour began', 'June 25, 1965', '1, 3', ''],
            ['Date of death', 'November 19, 1965', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Gunshot or small arms fire', '1', ''],
            ['Place', 'Not recorded in the federal files', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 03E, line 101', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1364 => [
        'title' => 'Elmer Robert Lee Ables Jr.', 'expect' => ['wmServiceId' => '19520522'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Elmer Robert Lee Ables Jr., 19520522, Army, SFC, Sergeant First Class; hostile, killed, October 26, 1967; reason: aircraft loss or crash not at sea; province: Phuoc Long; home of record Newhall, California; born October 25, 1937; tour began August 11, 1967; component Regular; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit Special Forces, B-345; Vietnam Veterans Memorial, panel 28E, line 71.',
            $LEGACY('/scvhistory/vietnam_elmerables.htm'),
        ],
        'facts' => [
            ['Service number', '19520522', '1, 3', ''],
            ['Rank', 'Sergeant First Class', '1, 3', ''],
            ['Unit', 'Special Forces, B-345', '2, 3', ''],
            ['Born', 'October 25, 1937', '1, 3', ''],
            ['Tour began', 'August 11, 1967', '1, 3', ''],
            ['Date of death', 'October 26, 1967', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Aircraft loss or crash not at sea', '1', ''],
            ['Place', 'Phuoc Long Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Newhall', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 28E, line 71', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Newhall (note 1).'],
        ],
    ],
    1366 => [
        'title' => 'Frank Dennis Ortega', 'expect' => ['wmServiceId' => '56705466'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Frank Dennis Ortega, 56705466, Army, PFC, Private First Class; hostile, killed, February 25, 1968; reason: multiple fragment wounds; province: Quang Nam; home of record Saugus, California; born April 28, 1946; tour began November 17, 1967; component Selective Service; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit D Co, 2nd Bn, 35th Infantry, 4th Infantry Division; Vietnam Veterans Memorial, panel 41E, line 26.',
            $LEGACY('/scvhistory/vietnam_frankortega.htm'),
        ],
        'facts' => [
            ['Service number', '56705466', '1, 3', ''],
            ['Rank', 'Private First Class', '1, 3', ''],
            ['Unit', 'D Co, 2nd Bn, 35th Infantry, 4th Infantry Division', '2, 3', ''],
            ['Born', 'April 28, 1946', '1, 3', ''],
            ['Tour began', 'November 17, 1967', '1, 3', ''],
            ['Date of death', 'February 25, 1968', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Multiple fragment wounds', '1', ''],
            ['Place', 'Quang Nam Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 41E, line 26', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1370 => [
        'title' => 'Gary Allen Turnbull', 'expect' => ['wmServiceId' => '550624453'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Gary Allen Turnbull, 550624453, Army, SGT, Sergeant; non-hostile, died of other causes, May 10, 1970; reason: aircraft loss or crash not at sea; province: not recorded; home of record Saugus, California; born March 7, 1946; tour began September 16, 1969; component Regular; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit C Trp, 7th Sqdn, 1st Cavalry, 1st Aviation Brigade; Vietnam Veterans Memorial, panel 10W, line 16.',
            $LEGACY('/scvhistory/vietnam_garyturnbull.htm'),
        ],
        'facts' => [
            ['Service number', '550624453', '1, 3', ''],
            ['Rank', 'Sergeant', '1, 3', ''],
            ['Unit', 'C Trp, 7th Sqdn, 1st Cavalry, 1st Aviation Brigade', '2, 3', ''],
            ['Born', 'March 7, 1946', '1, 3', ''],
            ['Tour began', 'September 16, 1969', '1, 3', ''],
            ['Date of death', 'May 10, 1970', '1, 3', ''],
            ['Casualty', 'Non-hostile, died of other causes', '1, 3', ''],
            ['Cause', 'Aircraft loss or crash not at sea', '1', ''],
            ['Place', 'Not recorded in the federal files', '1, 2, 3', 'The record gives Cambodia; the federal files do not record a province.'],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 10W, line 16', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1368 => [
        'title' => 'Gary Robert Monteleone', 'expect' => ['wmServiceId' => '569882478'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Gary Robert Monteleone, 569882478, Army, SP4, Specialist Four; non-hostile, died of other causes, May 10, 1972; reason: aircraft loss or crash not at sea; province: Bien Hoa; home of record Saugus, California; born July 27, 1952; tour began December 16, 1971; component Regular; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit D Co, 2nd Bn, 8th Cavalry, 1st Cavalry Division; Vietnam Veterans Memorial, panel 01W, line 20.',
            $LEGACY('/scvhistory/vietnam_garymonteleone.htm'),
        ],
        'facts' => [
            ['Service number', '569882478', '1, 3', ''],
            ['Rank', 'Specialist Four', '1, 3', ''],
            ['Unit', 'D Co, 2nd Bn, 8th Cavalry, 1st Cavalry Division', '2, 3', ''],
            ['Born', 'July 27, 1952', '1, 3', ''],
            ['Tour began', 'December 16, 1971', '1, 3', ''],
            ['Date of death', 'May 10, 1972', '1, 3', ''],
            ['Casualty', 'Non-hostile, died of other causes', '1, 3', ''],
            ['Cause', 'Aircraft loss or crash not at sea', '1', ''],
            ['Place', 'Bien Hoa Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 01W, line 20', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1372 => [
        'title' => 'Henry Chester Klinger', 'expect' => ['wmServiceId' => '550767036'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Henry Chester Klinger, 550767036, Army, SP4, Specialist Four; hostile, killed, January 1, 1970; reason: gunshot or small arms fire; province: Quang Tin; home of record Saugus, California; born August 30, 1949; tour began October 10, 1969; component Selective Service; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit A Co, 2nd Bn, 1st Infantry, 196th Light Infantry Brigade; Vietnam Veterans Memorial, panel 15W, line 114.',
            $LEGACY('/scvhistory/vietnam_henryklinger.htm'),
        ],
        'facts' => [
            ['Service number', '550767036', '1, 3', ''],
            ['Rank', 'Specialist Four', '1, 3', ''],
            ['Unit', 'A Co, 2nd Bn, 1st Infantry, 196th Light Infantry Brigade', '2, 3', ''],
            ['Born', 'August 30, 1949', '1, 3', ''],
            ['Tour began', 'October 10, 1969', '1, 3', ''],
            ['Date of death', 'January 1, 1970', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Gunshot or small arms fire', '1', ''],
            ['Place', 'Quang Tin Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 15W, line 114', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1374 => [
        'title' => 'John William Borders Jr.', 'expect' => ['wmServiceId' => '9656782'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): John William Borders Jr., 9656782, Navy, BUL2, Builder Second Class; hostile, killed, March 8, 1968; reason: artillery or rocket; province: Quang Tri; home of record SAUGAS (as spelled; Coffelt gives SAUGUS), California; born July 28, 1939; tour began not recorded; component Reserve; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit not recorded; Vietnam Veterans Memorial, panel 43E, line 51.',
            $LEGACY('/scvhistory/vietnam_johnborders.htm'),
        ],
        'facts' => [
            ['Service number', '9656782', '1, 3', ''],
            ['Rank', 'Builder Second Class', '1, 3', ''],
            ['Unit', 'As the legacy page gives it', '3', 'Coffelt records no unit.'],
            ['Born', 'July 28, 1939', '1, 3', ''],
            ['Tour began', 'Not recorded', '1', ''],
            ['Date of death', 'March 8, 1968', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Artillery or rocket', '1', ''],
            ['Place', 'Quang Tri Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', 'The Combat Area Casualties file spells it SAUGAS; Coffelt gives SAUGUS.'],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 43E, line 51', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1376 => [
        'title' => 'Joseph Samuel Godwin', 'expect' => ['wmServiceId' => '7973239'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Joseph Samuel Godwin, 7973239, Navy, HM3, Hospital Corpsman Third Class; hostile, died of wounds, April 7, 1966; reason: gunshot or small arms fire; province: Quang Nam; home of record Saugus, California; born September 6, 1945; tour began not recorded; component Regular; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit 7th Fleet, 1st Marine Aircraft Wing, MAG-16; Vietnam Veterans Memorial, panel 06E, line 92.',
            $LEGACY('/scvhistory/vietnam_josephgodwin.htm'),
        ],
        'facts' => [
            ['Service number', '7973239', '1, 3', ''],
            ['Rank', 'Hospital Corpsman Third Class', '1, 3', ''],
            ['Unit', '7th Fleet, 1st Marine Aircraft Wing, MAG-16', '2, 3', ''],
            ['Born', 'September 6, 1945', '1, 3', ''],
            ['Tour began', 'Not recorded', '1', ''],
            ['Date of death', 'April 7, 1966', '1, 3', ''],
            ['Casualty', 'Hostile, died of wounds', '1, 3', ''],
            ['Cause', 'Gunshot or small arms fire', '1', ''],
            ['Place', 'Quang Nam Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 06E, line 92', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1378 => [
        'title' => 'Michael Andrew Fay', 'expect' => ['wmServiceId' => '56397756'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Michael Andrew Fay, 56397756, Army, SP5, Specialist Fifth Class; hostile, killed, March 26, 1968; reason: multiple fragment wounds; province: Kontum; home of record Saugus, California; born May 4, 1945; tour began June 21, 1967; component Regular; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit HHC, 3rd Bn, 8th Infantry, 4th Infantry Division; Vietnam Veterans Memorial, panel 46E, line 32.',
            $LEGACY('/scvhistory/vietnam_michaelfay.htm'),
        ],
        'facts' => [
            ['Service number', '56397756', '1, 3', ''],
            ['Rank', 'Specialist Fifth Class', '1, 3', ''],
            ['Unit', 'HHC, 3rd Bn, 8th Infantry, 4th Infantry Division', '2, 3', ''],
            ['Born', 'May 4, 1945', '1, 3', ''],
            ['Tour began', 'June 21, 1967', '1, 3', ''],
            ['Date of death', 'March 26, 1968', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Multiple fragment wounds', '1', ''],
            ['Place', 'Kontum Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 46E, line 32', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
    ],
    1380 => [
        'title' => 'Stephen Russell Peterson', 'expect' => ['wmServiceId' => 'W3160264'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Stephen Russell Peterson, W3160264, Army, CWO, Chief Warrant Officer; non-hostile, died of other causes, April 23, 1969; reason: aircraft loss or crash not at sea; province: Binh Duong; home of record Newhall, California; born November 28, 1935; tour began May 2, 1968; component Reserve; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit B Co, 1st Aviation Battalion, 1st Infantry Division; Vietnam Veterans Memorial, panel 26W, line 34.',
            $LEGACY('/scvhistory/vietnam_stephenpeterson.htm'),
        ],
        'facts' => [
            ['Service number', 'W3160264', '1, 3', ''],
            ['Rank', 'Chief Warrant Officer', '1, 3', ''],
            ['Unit', 'B Co, 1st Aviation Battalion, 1st Infantry Division', '2, 3', ''],
            ['Born', 'November 28, 1935', '1, 3', ''],
            ['Tour began', 'May 2, 1968', '1, 3', ''],
            ['Date of death', 'April 23, 1969', '1, 3', ''],
            ['Casualty', 'Non-hostile, died of other causes', '1, 3', ''],
            ['Cause', 'Aircraft loss or crash not at sea', '1', ''],
            ['Place', 'Binh Duong Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Newhall', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 26W, line 34', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Newhall (note 1).'],
        ],
    ],
    1382 => [
        'title' => 'Terry Dale Gemas', 'expect' => ['wmServiceId' => '2198415'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Combat Area Casualties Current File (Record Group 330): Terry Dale Gemas, 2198415, USMC, PFC, Private First Class; hostile, killed, December 29, 1966; reason: misadventure; province: Quang Tri; home of record Saugus, California; born January 17, 1946; tour began not recorded; component Selective Service; body recovered.',
            'The Coffelt Database, December 2005 update (National Archives, Collection COFF): unit Hqs Btry, 3rd Bn, 12th Marines, 3rd Marine Division; Vietnam Veterans Memorial, panel 13E, line 97.',
            $LEGACY('/scvhistory/vietnam_terrygemas.htm'),
        ],
        'facts' => [
            ['Service number', '2198415', '1, 3', ''],
            ['Rank', 'Private First Class', '1, 3', ''],
            ['Unit', 'Hqs Btry, 3rd Bn, 12th Marines, 3rd Marine Division', '2, 3', ''],
            ['Born', 'January 17, 1946', '1, 3', ''],
            ['Tour began', 'Not recorded', '1', ''],
            ['Date of death', 'December 29, 1966', '1, 3', ''],
            ['Casualty', 'Hostile, killed', '1, 3', ''],
            ['Cause', 'Misadventure', '1', 'The record gives a hostile ground casualty, died outright; the federal file\'s reason is "misadventure," its term for an accidental death, which can include friendly fire.'],
            ['Place', 'Quang Tri Province, South Vietnam', '1, 2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', ''],
            ['Remains', 'Body recovered', '1', 'The record\'s burial field held this; where he is buried has not been found.'],
            ['Vietnam Veterans Memorial', 'Panel 13E, line 97', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Combat Area Casualties file records his death and gives his home of record as Saugus (note 1).'],
        ],
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
if (!$short) {
    foreach ($burial as $id) { $x = Entry::find()->id($id)->status(null)->one(); $x->setFieldValue('burialPlace', ''); if (!Craft::$app->getElements()->saveElement($x) || trim((string)Entry::find()->id($id)->status(null)->one()->burialPlace) !== '') { $short[] = "#$id burial"; } }
    echo 'BURIAL FIELDS cleared: ' . count($burial) . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records sourced') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('source_wm_vietnam.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial sourcing, Vietnam: thirteen records from the Combat Area Casualties Current File and the Coffelt Database, each with a valley home of record; "Body recovered" moved out of the burial field');
if ($short) { throw new \RuntimeException('source_wm_vietnam: ' . implode(', ', $short)); }
