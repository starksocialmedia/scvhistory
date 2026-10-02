/**
 * War memorial sourcing, World War II batch 1: ten Army records (Nathan,
 * 2 October 2026: "run the rest in batches of about ten on the same pattern.
 * You have the 88 Honor List pages for the World War II records, so start
 * there"). The pattern is source_wm_pilot.php's: footnotes, factSources with
 * differences said, a "Sources, 2026" note on the two-source rule, and a data
 * field corrected only where the sources settle it, with a note; the legacy
 * text is never rewritten.
 *
 *   #522 Moore, #520 Beall, #516 Contreras, #512 Darr, #560 Wingfield,
 *   #562 Harland, #566 Bartlett, #570 Ward, #1400 Balsz, #574 Smart
 *
 * THE SOURCES are in inventory/sources/wm-ww2-batch1-2026-10-02.json: the
 * Honor List lines read by eye from the page images, NARA enlistment records,
 * ABMC, and the archive's own clippings and letters.
 *
 * FIELDS: Bartlett's date of death April 6 -> April 20, 1944 (ABMC and the
 * record's own incident date), with a correction note. Contreras's "1944" ->
 * February 9, 1944 (ABMC). Filled where empty: Balsz's date of death (ABMC),
 * Wingfield's and Smart's service numbers, Bartlett's rank and unit (ABMC).
 *
 * The rule is met for two (Contreras and Balsz, each with a valley source);
 * seven meet half (a federal record of the death, the valley tie on the legacy
 * page alone); Beall meets neither yet (Oklahoma's Honor List is unread).
 * Idempotent: a record whose factSources is set is left alone. Dry run by
 * default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_ww2_batch1.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$flat = json_encode(json_decode((string)@file_get_contents("$root/inventory/sources/wm-ww2-batch1-2026-10-02.json"), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$bad = [];
foreach (['MOORE ALBERT L 39161137 PVT DNB', 'CONTRERAS EDWARD D 39225373 SGT KIA', 'DARR EUGENE E 39728618 SGT KIA', 'WINGFIELD GARRY 19003636 PVT DNB', 'HARLAND JACK L O-704928 2 LT DNB', 'BARTLETT JAMES A 39273316 SGT KIA', 'WARD JOHN A 39587129 PVT KIA', 'BALSZ JOSEPH B 39262290 PFC DNB', 'SMART OZAL R 19048795 SGT KIA', 'died February 9, 1944; Sicily-Rome American Cemetery, Plot J, Row 4, Grave 7', 'died April 20, 1944; missing in action; North Africa American Cemetery', 'died August 31, 1944; North Africa American Cemetery, Plot F, Row 2, Grave 12', 'Sgt. Edward D. Contreras, Newhall', 'a vehicle accident in Algiers on August 31', 'Archibald K. Beall, USA, Pfc., James Beall, Cushing Route 1', 'resident of Lincoln County, Oklahoma'] as $ph) {
    if (!str_contains($flat, $ph) && !str_contains($flat, str_replace('resident of ', '', $ph))) { $bad[] = 'the sources file does not read ' . mb_substr($ph, 0, 50); }
}
$LEGACY = fn($path) => "SCVHistory.com, Santa Clarita Valley War Memorial, page for him ($path), as migrated to this record.";
$R = [
    522 => [
        'title' => 'Albert Lee Moore', 'expect' => ['wmServiceId' => '39161137'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "MOORE ALBERT L 39161137 PVT DNB."',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39161137: Los Angeles County; enlisted at Los Angeles, July 9, 1941; born 1916.',
            $LEGACY('/warmemorial/ww2_albertmoore.htm'),
        ],
        'facts' => [
            ['Service number', '39161137', '1, 2', ''],
            ['Rank', 'Private', '1, 2', ''],
            ['Casualty', 'Died non-battle', '1, 3', ''],
            ['Enlisted', 'Los Angeles, July 9, 1941', '2', 'The legacy page says July 1941.'],
            ['Born', 'March 2, 1917', '2, 3', 'The legacy page marks the date uncertain; the enlistment record gives 1916.'],
            ['Date of death', 'June 28, 1943', '3', 'The Honor List gives no date.'],
            ['Unit', '25th Infantry Division', '3', ''],
            ['Home', 'Soledad Township', '2, 3', 'The enlistment record gives Los Angeles County.'],
            ['Burial', 'Forest Lawn Memorial Park, Glendale', '3', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The War Department\'s Honor List records his death (note 1); no source tying him to the valley has been found beyond the legacy page.'],
        ],
    ],
    520 => [
        'title' => 'Archibald K. \'Archie\' Beall', 'expect' => ['wmServiceId' => '38154662'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 38154662: resident of Lincoln County, Oklahoma; enlisted at Oklahoma City, July 8, 1942; born 1921 in Oklahoma.',
            'Miami (Okla.) Daily News-Record, November 23, 1947, "Army\'s List of Returning War Dead Is Announced," held in this archive: "Archibald K. Beall, USA, Pfc., James Beall, Cushing Route 1."',
            $LEGACY('/warmemorial/ww2_archibaldbeall.htm'),
        ],
        'facts' => [
            ['Service number', '38154662', '1', ''],
            ['Rank', 'Private First Class', '2, 3', ''],
            ['Born', 'March 25, 1921, Oklahoma', '1, 3', 'The enlistment record gives the year and the state.'],
            ['Enlisted', 'Oklahoma City, July 8, 1942', '1', ''],
            ['Unit', '394th Infantry Regiment', '3', ''],
            ['Date of death', 'March 13, 1945', '3', ''],
            ['Home', 'Newhall (1317 Chestnut St.)', '3', 'The enlistment record gives Lincoln County, Oklahoma, and his body was returned in 1947 to his father at Cushing, Oklahoma (note 2).'],
            ['Burial', 'Avery Memorial Cemetery, Avery, Lincoln County, Okla.', '2, 3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record does not yet meet the archive\'s rule for a memorial record. He enlisted in Oklahoma, so his federal record of death is in Oklahoma\'s 1946 Honor List, which has not been read; and his tie to the valley rests on the legacy page alone.'],
        ],
    ],
    516 => [
        'title' => 'Edward D. Contreras', 'expect' => ['wmServiceId' => '39225373', 'deathDate' => '1944'],
        'set' => ['deathDate' => 'February 9, 1944', 'deathDateEdtf' => '1944-02-09'],
        'fill' => [],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "CONTRERAS EDWARD D 39225373 SGT KIA."',
            'American Battle Monuments Commission, Burial and Memorialization Directory, read 2 October 2026: Sgt. Edward D. Contreras, 39225373, 30th Infantry Regiment, 3rd Infantry Division; died February 9, 1944; Sicily-Rome American Cemetery, Plot J, Row 4, Grave 7; Purple Heart.',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39225373: Los Angeles County; enlisted at Los Angeles, January 20, 1941; born 1912.',
            'Los Angeles Times, April 20, 1944, "Four Southland Men Listed as Killed in Action," held in this archive: "Sgt. Edward D. Contreras, Newhall."',
            $LEGACY('/warmemorial/ww2_edwardcontreras.htm'),
        ],
        'facts' => [
            ['Service number', '39225373', '1, 2, 3', ''],
            ['Rank', 'Sergeant', '1, 2, 4', ''],
            ['Unit', '30th Infantry Regiment, 3rd Infantry Division', '2, 5', ''],
            ['Born', '1912', '3, 5', ''],
            ['Date of death', 'February 9, 1944', '2, 5', 'The record\'s date field held only "1944"; ABMC gives February 9.'],
            ['Place', 'Anzio-Nettuno, Italy', '5', ''],
            ['Age', '26', '5', 'Born in 1912, he was 31 or 32.'],
            ['Home', 'Castaic', '4, 5', 'The Times gives Newhall.'],
            ['Awards', 'Purple Heart', '2, 5', ''],
            ['Burial', 'Sicily-Rome American Cemetery, Nettuno, Italy (Plot J, Row 4, Grave 7)', '2, 5', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record: the War Department\'s Honor List and ABMC record his death (notes 1 and 2), and the Los Angeles Times named him as a Newhall man (note 4).'],
        ],
    ],
    512 => [
        'title' => 'Eugene E. Darr', 'expect' => ['wmServiceId' => '39728618'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "DARR EUGENE E 39728618 SGT KIA."',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39728618: Los Angeles County; enlisted at Los Angeles, September 19, 1944; born 1923 in Oklahoma.',
            $LEGACY('/warmemorial/ww2_eugenedarr.htm'),
        ],
        'facts' => [
            ['Service number', '39728618', '1, 2', ''],
            ['Rank', 'Sergeant', '1, 3', ''],
            ['Casualty', 'Killed in action', '1, 3', ''],
            ['Born', '1923, Oklahoma', '2', ''],
            ['Enlisted', 'Los Angeles, September 19, 1944', '2', ''],
            ['Unit', '96th Infantry Division', '3', ''],
            ['Date of death', 'June 10, 1945', '3', 'The Honor List gives no date.'],
            ['Place', 'Okinawa', '3', ''],
            ['Home', 'Saugus', '2, 3', 'The enlistment record gives Los Angeles County.'],
            ['Burial', 'Not recorded', '3', 'He is not in ABMC\'s registers of those buried or missing overseas; where he is buried has not been found.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The War Department\'s Honor List records his death (note 1); no source tying him to the valley has been found beyond the legacy page.'],
        ],
    ],
    560 => [
        'title' => 'Garry Wingfield', 'expect' => [],
        'set' => [],
        'fill' => ['wmServiceId' => '19003636'],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "WINGFIELD GARRY 19003636 PVT DNB."',
            'American Battle Monuments Commission, Burial and Memorialization Directory, read 2 October 2026: Pvt. Garry Wingfield, 19003636, 31st Infantry Regiment; died June 13, 1942; missing in action; Manila American Cemetery (Walls of the Missing); Bronze Star.',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 19003636: Los Angeles County; enlisted at Los Angeles, August 26, 1941; born 1921 in California.',
            $LEGACY('/warmemorial/ww2_garrywingfield.htm'),
        ],
        'facts' => [
            ['Service number', '19003636', '1, 2, 3', ''],
            ['Rank', 'Private', '1, 2, 3', ''],
            ['Unit', '31st Infantry Regiment', '2, 4', ''],
            ['Born', '1921, California', '3', ''],
            ['Enlisted', 'Los Angeles, August 26, 1941', '3', ''],
            ['Date of death', 'June 13, 1942', '2, 4', ''],
            ['Casualty', 'Killed in action', '4', 'The Honor List gives died non-battle; ABMC gives missing in action.'],
            ['Home', 'Soledad Township', '3, 4', 'The enlistment record gives Los Angeles County.'],
            ['Awards', 'Bronze Star; Purple Heart', '2, 4', 'ABMC lists the Bronze Star only.'],
            ['Memorial', 'Walls of the Missing, Manila American Cemetery', '2', 'The legacy page says he is buried there.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Honor List and ABMC record his death (notes 1 and 2), and they disagree on how; no source tying him to the valley has been found beyond the legacy page.'],
        ],
    ],
    562 => [
        'title' => 'Jack Lewis Harland', 'expect' => ['wmServiceId' => '0-704928'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "HARLAND JACK L O-704928 2 LT DNB."',
            $LEGACY('/warmemorial/ww2_jackharland.htm'),
        ],
        'facts' => [
            ['Service number', 'O-704928', '1, 2', ''],
            ['Rank', 'Second Lieutenant', '1, 2', ''],
            ['Casualty', 'Died non-battle', '1, 2', ''],
            ['Date of death', 'March 31, 1944', '2', 'The Honor List gives no date.'],
            ['Place', 'Near Gate, Oklahoma, in a training collision of two B-24s', '2', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The War Department\'s Honor List records his death (note 1); no source tying him to the valley has been found beyond the legacy page, which does not give his home.'],
        ],
    ],
    566 => [
        'title' => 'James A. Bartlett', 'expect' => ['wmServiceId' => '39273316', 'deathDate' => 'April 6, 1944'],
        'set' => ['deathDate' => 'April 20, 1944', 'deathDateEdtf' => '1944-04-20'],
        'fill' => ['wmRank' => 'Sergeant', 'wmUnit' => '32nd Photographic Squadron, 5th Reconnaissance Group'],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "BARTLETT JAMES A 39273316 SGT KIA."',
            'American Battle Monuments Commission, Burial and Memorialization Directory, read 2 October 2026: Sgt. James A. Bartlett, 39273316, U.S. Army Air Forces, 32nd Photographic Squadron, 5th Reconnaissance Group; died April 20, 1944; missing in action; North Africa American Cemetery (Tunisia); Purple Heart.',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39273316: Los Angeles County; enlisted at Los Angeles, December 28, 1942; born 1915 in California; a fireman.',
            $LEGACY('/warmemorial/ww2_jimbartlett.htm'),
        ],
        'facts' => [
            ['Service number', '39273316', '1, 2, 3', ''],
            ['Rank', 'Sergeant', '1, 2', ''],
            ['Branch', 'U.S. Army Air Forces', '2', 'The record gives U.S. Army.'],
            ['Unit', '32nd Photographic Squadron, 5th Reconnaissance Group', '2', ''],
            ['Enlisted', 'Los Angeles, December 28, 1942', '3', 'The legacy page says January 1943.'],
            ['Born', '1915, California', '3', ''],
            ['Date of death', 'April 20, 1944', '2, 4', 'The record gave April 6, 1944; ABMC and the legacy page\'s own incident date give April 20, the day the transport was torpedoed.'],
            ['Place', 'Mediterranean Sea, aboard a transport struck by an aerial torpedo', '4', ''],
            ['Remains', 'Not recovered; named on the Tablets of the Missing, North Africa American Cemetery', '2', ''],
            ['Awards', 'Purple Heart', '2', ''],
            ['Home', 'Soledad Township', '3, 4', 'The enlistment record gives Los Angeles County.'],
        ],
        'editor' => [
            ['Correction, 2026', 'James A. Bartlett died on April 20, 1944, not April 6 as the record gave it. ABMC gives April 20, and so does this record\'s own incident date, the day his transport was torpedoed in the Mediterranean (note 2).'],
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Honor List and ABMC record his death (notes 1 and 2); no source tying him to the valley has been found beyond the legacy page.'],
        ],
    ],
    570 => [
        'title' => 'John Amos Ward', 'expect' => ['wmServiceId' => '39587129'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), San Diego County, National Archives NAID 305280: "WARD JOHN A 39587129 PVT KIA."',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39587129: San Diego County; enlisted at Fort MacArthur, May 27, 1944; born 1918.',
            'Letter, Col. Daniel B. Strickler, commanding the 110th Infantry, March 9, 1945, to Mrs. Dorothy M. Ward, Escondido, held in this archive: "Private John A. Ward, 39 587 129, Company B, 110th Infantry Regiment, was killed in action while serving with the 110th Infantry Regiment, 28th Infantry Division, U.S. Army, in France on 1 February 1945."',
            'Letter, Headquarters, San Francisco Port of Embarkation, December 30, 1948, to Mrs. Dorothy M. Ward, Valley Center, on the disposition of his remains, held in this archive.',
            $LEGACY('/warmemorial/ww2_johnward.htm'),
        ],
        'facts' => [
            ['Service number', '39587129', '1, 2, 3', ''],
            ['Rank', 'Private', '1, 2, 3', ''],
            ['Unit', 'Company B, 110th Infantry Regiment, 28th Infantry Division', '3, 5', ''],
            ['Born', 'June 22, 1918', '2, 5', 'The enlistment record gives the year.'],
            ['Enlisted', 'Fort MacArthur, May 27, 1944', '2', ''],
            ['Date of death', 'February 1, 1945', '3, 5', ''],
            ['Place', 'France', '3, 5', ''],
            ['Home', 'Newhall', '5', 'The Honor List and the enlistment record place him in San Diego County, and the Army wrote to his widow at Escondido in 1945 and Valley Center in 1948.'],
            ['Burial', 'An American military cemetery in France; returned 1949; Valhalla Cemetery, North Hollywood', '3, 4, 5', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Honor List and his regiment\'s letter record his death (notes 1 and 3); every federal record places him in San Diego County, and his tie to the valley rests on the legacy page.'],
        ],
    ],
    1400 => [
        'title' => 'Joseph B. Balsz', 'expect' => ['wmServiceId' => '39262290'],
        'set' => [],
        'fill' => ['deathDate' => 'August 31, 1944', 'deathDateEdtf' => '1944-08-31'],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "BALSZ JOSEPH B 39262290 PFC DNB."',
            'American Battle Monuments Commission, Burial and Memorialization Directory, read 2 October 2026: Pfc. Joseph B. Balsz, 39262290, 230th Military Police Company; died August 31, 1944; North Africa American Cemetery, Plot F, Row 2, Grave 12.',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39262290: Los Angeles County; enlisted at Los Angeles, October 24, 1942; born 1919 in California.',
            'The Newhall Signal and Saugus Enterprise, Friday, September 22, 1944, "Pfc. Balsz loses life in Algiers accident," held in this archive: his parents, Mr. and Mrs. Bart Balsz of Honby, were told he died in a vehicle accident in Algiers on August 31.',
            $LEGACY('/scvhistory/ww2_josephbbalsz.htm'),
        ],
        'facts' => [
            ['Service number', '39262290', '1, 2, 3', ''],
            ['Rank', 'Private First Class', '1, 2, 4', ''],
            ['Unit', '230th Military Police Company', '2, 5', ''],
            ['Born', 'November 29, 1919', '3, 5', 'The enlistment record gives the year.'],
            ['Enlisted', 'Los Angeles, October 24, 1942', '3, 5', ''],
            ['Date of death', 'August 31, 1944', '2, 4', 'The record\'s date field was empty.'],
            ['Casualty', 'Died non-battle: a vehicle accident', '1, 4', ''],
            ['Place', 'Oran, Algeria', '5', 'The Signal says Algiers.'],
            ['Home', 'Saugus (his parents at Honby)', '3, 4, 5', 'The enlistment record gives Los Angeles County.'],
            ['Burial', 'North Africa American Cemetery, Tunis (Plot F, Row 2, Grave 12)', '2, 5', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record: the Honor List and ABMC record his death (notes 1 and 2), and the Newhall Signal reported it, with his parents at Honby (note 4).'],
        ],
    ],
    574 => [
        'title' => 'Ozal R. Smart', 'expect' => [],
        'set' => [],
        'fill' => ['wmServiceId' => '19048795'],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "SMART OZAL R 19048795 SGT KIA."',
            'American Battle Monuments Commission, Burial and Memorialization Directory, read 2 October 2026: Sgt. Ozal R. Smart, 19048795, 155th Anti-Aircraft Battalion, 17th Airborne Division; died March 24, 1945; Netherlands American Cemetery, Plot D, Row 11, Grave 29; Purple Heart.',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 19048795: Los Angeles County; enlisted at Fort MacArthur, September 27, 1940; born 1914 in Oklahoma.',
            $LEGACY('/warmemorial/ww2_ozalsmart.htm'),
        ],
        'facts' => [
            ['Service number', '19048795', '1, 2, 3', ''],
            ['Rank', 'Sergeant', '1, 2, 4', ''],
            ['Unit', '155th Anti-Aircraft Battalion, 17th Airborne Division', '2', 'The record gives the division only.'],
            ['Enlisted', 'Fort MacArthur, September 27, 1940', '3, 4', ''],
            ['Born', '1914, Oklahoma', '3', ''],
            ['Date of death', 'March 24, 1945', '2, 4', ''],
            ['Place', 'Near Wesel, Germany', '4', ''],
            ['Age', '34', '4', 'Born in 1914, he was 30 or 31.'],
            ['Home', 'Castaic', '3, 4', 'The enlistment record gives Los Angeles County.'],
            ['Awards', 'Purple Heart', '2, 4', ''],
            ['Burial', 'Netherlands American Cemetery, Margraten (Plot D, Row 11, Grave 29)', '2, 4', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Honor List and ABMC record his death (notes 1 and 2); no source tying him to the valley has been found beyond the legacy page.'],
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
    if (array_diff_key($r['set'], $r['expect']) && array_filter(array_keys($r['set']), fn($k) => !str_ends_with($k, 'Edtf') && !array_key_exists($k, $r['expect']))) { $bad[] = "#$id sets a field it does not check first"; }
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
$applyLog('source_wm_ww2_batch1.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial sourcing, World War II batch 1: ten Army records from the 1946 Honor List, NARA enlistment records and ABMC; Bartlett, Contreras and Balsz death dates set from ABMC');
if ($short) { throw new \RuntimeException('source_wm_ww2_batch1: ' . implode(', ', $short)); }
