/**
 * War memorial sourcing, Korea (Nathan, 2 October 2026: "Move on to Korea,
 * Vietnam and the War on Terror first"). The pilot's pattern; Henry Acuna was
 * in the pilot.
 *
 *   #558 Thomas, #556 Morissett, #554 Montenegro, #550 Kelly, #548 Whisler
 *
 * THE SOURCES (inventory/sources/wm-korea-2026-10-02.json): NARA's Korean
 * Conflict Casualty File (RG 330, every service) and Korean War Casualty File
 * (RG 407, Army), and Montenegro's World War II enlistment record.
 *
 * FIELDS: four dates of birth read "January 1", which the federal files do not
 * support (they give the year only) and which, falling on the same day for four
 * men, looks like the legacy source's placeholder for an unknown day. The
 * content rules say dates keep their real precision, so each is cut to the year
 * with a Correction note. Kelly's full date agrees with his federal record.
 * No record here has a source tying it to the valley beyond the legacy page.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_korea.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$flat = json_encode(json_decode((string)@file_get_contents("$root/inventory/sources/wm-korea-2026-10-02.json"), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$bad = [];
foreach (['THOMAS ALBERT E, 19338271, PFC', 'RA19338271, Private First Class, Signal Corps', 'MORISSETT DONALD E, 19368929', 'MONTENEGRO GILBERT, 39225368, MSGT', 'enlisted at Los Angeles, January 20, 1941', 'KELLY RAYMOND GENE, 508187, Ensign', 'born September 13, 1928', 'WHISLER ROBERT L, 19339675'] as $ph) {
    if (!str_contains($flat, $ph)) { $bad[] = 'the sources file does not read ' . mb_substr($ph, 0, 50); }
}
$LEGACY = fn($path) => "SCVHistory.com, Santa Clarita Valley War Memorial, page for him ($path), as migrated to this record.";
$R = [
    558 => [
        'title' => 'Albert Edward Thomas', 'expect' => ['wmServiceId' => '19338271', 'wmDateOfBirth' => 'January 1, 1930'],
        'set' => ['wmDateOfBirth' => '1930'],
        'fill' => [],
        'notes' => [
            'National Archives, Korean Conflict Casualty File (Record Group 330): Thomas, Albert E., 19338271, Private First Class, U.S. Army; hostile, died while missing, August 11, 1950; home of record Los Angeles, California; born 1930.',
            'National Archives, Korean War Casualty File (Record Group 407, Army): RA19338271, Private First Class, Signal Corps; South Korea, August 11, 1950; Los Angeles County; declared dead, previously missing in action; born 1930; Regular Army.',
            $LEGACY('/warmemorial/korea_albertthomas.htm'),
        ],
        'facts' => [
            ['Service number', '19338271', '1, 2', ''],
            ['Rank', 'Corporal', '3', 'Both federal files give Private First Class.'],
            ['Branch', 'U.S. Army, Signal Corps', '2, 3', ''],
            ['Unit', '8036th Signal Service Company', '2, 3', ''],
            ['Component', 'Regular Army', '2', 'The Defense Department\'s file (note 1) gives his component as Reserve; the Army\'s own file gives Regular Army.'],
            ['Born', '1930', '1, 2', 'The legacy page gave January 1; the federal files give the year only, and no source for the day has been found.'],
            ['Date of death', 'August 11, 1950', '1, 2, 3', ''],
            ['Casualty', 'Missing in action; declared dead', '1, 2, 3', 'The legacy page says he was presumed dead on December 31, 1953.'],
            ['Place', 'South Korea', '2, 3', ''],
            ['Home', 'Soledad Canyon', '1, 2, 3', 'The federal files give Los Angeles.'],
            ['Memorial', 'Courts of the Missing, Honolulu Memorial', '3', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Correction, 2026', 'Albert Edward Thomas\'s date of birth is given as 1930, not January 1, 1930: the federal casualty files give the year only (notes 1 and 2), and no source for the day has been found.'],
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. Both federal casualty files record his death (notes 1 and 2); his tie to the valley rests on the legacy page.'],
        ],
    ],
    556 => [
        'title' => 'Donald E. Morissett', 'expect' => ['wmServiceId' => '19368929', 'wmDateOfBirth' => 'January 1, 1932'],
        'set' => ['wmDateOfBirth' => '1932'],
        'fill' => [],
        'notes' => [
            'National Archives, Korean Conflict Casualty File (Record Group 330): Morissett, Donald E., 19368929, Private First Class, U.S. Army; hostile, killed, March 30, 1951; home of record Los Angeles, California; born 1932.',
            'National Archives, Korean War Casualty File (Record Group 407, Army): RA19368929, Private First Class, Infantry; South Korea, March 30, 1951; Los Angeles County; killed in action; born 1932; Light Weapons Infantryman, 25th Infantry Division; Regular Army.',
            $LEGACY('/warmemorial/korea_donaldmorissett.htm'),
        ],
        'facts' => [
            ['Service number', '19368929', '1, 2', ''],
            ['Rank', 'Private First Class', '1, 2, 3', ''],
            ['Unit', '35th Infantry Regiment, 25th Infantry Division', '2, 3', 'The Army\'s file gives the division only.'],
            ['Specialty', 'Light Weapons Infantryman', '2, 3', ''],
            ['Component', 'Regular Army', '2', 'The Defense Department\'s file (note 1) gives his component as Reserve; the Army\'s own file gives Regular Army.'],
            ['Born', '1932', '1, 2', 'The legacy page gave January 1; the federal files give the year only, and no source for the day has been found.'],
            ['Date of death', 'March 30, 1951', '1, 2, 3', ''],
            ['Casualty', 'Killed in action', '1, 2, 3', ''],
            ['Place', 'South Korea', '2, 3', ''],
            ['Home', 'Saugus', '1, 2, 3', 'The federal files give Los Angeles.'],
        ],
        'editor' => [
            ['Correction, 2026', 'Donald E. Morissett\'s date of birth is given as 1932, not January 1, 1932: the federal casualty files give the year only (notes 1 and 2), and no source for the day has been found.'],
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. Both federal casualty files record his death (notes 1 and 2); his tie to the valley rests on the legacy page.'],
        ],
    ],
    554 => [
        'title' => 'Gilbert D. Montenegro', 'expect' => ['wmServiceId' => '39225368', 'wmDateOfBirth' => 'January 1, 1915'],
        'set' => ['wmDateOfBirth' => '1915'],
        'fill' => [],
        'notes' => [
            'National Archives, Korean Conflict Casualty File (Record Group 330): Montenegro, Gilbert, 39225368, Master Sergeant, U.S. Army; hostile, killed, February 8, 1951; home of record Los Angeles, California; born 1915.',
            'National Archives, Korean War Casualty File (Record Group 407, Army): RA39225368, Master Sergeant, Infantry; South Korea, February 8, 1951; Los Angeles County; killed in action; born 1915; Light Weapons Infantry Leader, 24th Infantry Division; Regular Army.',
            'National Archives, World War II Army Enlistment Records (Record Group 64), serial number 39225368: Montenegro, Gilbert D.; Los Angeles County; enlisted at Los Angeles, January 20, 1941; born 1915 in California.',
            $LEGACY('/warmemorial/korea_gilbertmontenegro.htm'),
        ],
        'facts' => [
            ['Service number', '39225368', '1, 2, 3', ''],
            ['Rank', 'Master Sergeant', '1, 2, 4', ''],
            ['Unit', '19th Infantry Regiment, 24th Infantry Division', '2, 4', 'The Army\'s file gives the division only.'],
            ['Component', 'Regular Army', '2', 'The Defense Department\'s file (note 1) gives his component as Reserve; the Army\'s own file gives Regular Army.'],
            ['First enlisted', 'Los Angeles, January 20, 1941, for World War II', '3', ''],
            ['Born', '1915, California', '1, 2, 3', 'The legacy page gave January 1; the federal files give the year only, and no source for the day has been found.'],
            ['Date of death', 'February 8, 1951', '1, 2, 4', ''],
            ['Casualty', 'Killed in action', '1, 2, 4', ''],
            ['Place', 'South Korea', '2, 4', ''],
            ['Age', '36', '4', 'Born in 1915, he was 35 or 36.'],
            ['Home', 'Newhall', '1, 2, 3, 4', 'The federal files give Los Angeles.'],
            ['Awards', 'Purple Heart; Combat Infantryman Badge', '4', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Correction, 2026', 'Gilbert D. Montenegro\'s date of birth is given as 1915, not January 1, 1915: the federal records give the year only (notes 1 to 3), and no source for the day has been found.'],
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. Both federal casualty files record his death (notes 1 and 2); his tie to the valley rests on the legacy page. He enlisted for World War II on the same day as Edward D. Contreras of Castaic, five serial numbers apart, which suggests but does not show that the two enlisted together.'],
        ],
    ],
    550 => [
        'title' => 'Raymond Gene Kelly', 'expect' => ['wmServiceId' => '508187', 'wmDateOfBirth' => 'September 13, 1928'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Korean Conflict Casualty File (Record Group 330): Kelly, Raymond Gene, 508187, Ensign, U.S. Navy; hostile, killed, January 9, 1952; aircraft loss or crash not at sea, fixed wing, pilot; home of record Los Angeles, California; born September 13, 1928; component Reserve.',
            $LEGACY('/warmemorial/korea_raymondkelly.htm'),
        ],
        'facts' => [
            ['Service number', '508187', '1, 2', ''],
            ['Rank', 'Ensign', '1, 2', ''],
            ['Branch', 'U.S. Naval Reserve', '1', 'The record gives U.S. Navy.'],
            ['Unit', 'Fighter Squadron 54, USS Essex', '2', ''],
            ['Born', 'September 13, 1928', '1, 2', ''],
            ['Date of death', 'January 9, 1952', '1, 2', ''],
            ['Casualty', 'Killed: his aircraft lost over land, pilot', '1, 2', ''],
            ['Place', 'North Korea', '2', 'The Defense Department\'s file gives Korea.'],
            ['Home', 'Newhall', '1, 2', 'The Defense Department\'s file gives Los Angeles.'],
            ['Awards', 'Distinguished Flying Cross; Purple Heart; Air Medal', '2', 'From the legacy page only.'],
            ['Memorial', 'Courts of the Missing, Honolulu Memorial', '2', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Defense Department\'s casualty file records his death (note 1); his tie to the valley rests on the legacy page.'],
        ],
    ],
    548 => [
        'title' => 'Robert L. Whisler', 'expect' => ['wmServiceId' => '19339675', 'wmDateOfBirth' => 'January 1, 1931'],
        'set' => ['wmDateOfBirth' => '1931'],
        'fill' => [],
        'notes' => [
            'National Archives, Korean Conflict Casualty File (Record Group 330): Whisler, Robert L., 19339675, Private First Class, U.S. Army; hostile, killed, September 3, 1950; home of record Los Angeles, California; born 1931.',
            'National Archives, Korean War Casualty File (Record Group 407, Army): RA19339675, Private First Class, Infantry; South Korea, September 3, 1950; Los Angeles County; killed in action; born 1931; Food Service Apprentice, 1st Cavalry Division; Regular Army.',
            $LEGACY('/warmemorial/korea_robertwhisler.htm'),
        ],
        'facts' => [
            ['Service number', '19339675', '1, 2', ''],
            ['Rank', 'Private First Class', '1, 2, 3', ''],
            ['Unit', '8th Cavalry Regiment, 1st Cavalry Division', '2, 3', ''],
            ['Specialty', 'Food Service Apprentice', '2', 'The legacy page says he went straight to the front lines.'],
            ['Component', 'Regular Army', '2', 'The Defense Department\'s file (note 1) gives his component as Reserve; the Army\'s own file gives Regular Army.'],
            ['Born', '1931', '1, 2', 'The legacy page gave January 1; the federal files give the year only, and no source for the day has been found.'],
            ['Date of death', 'September 3, 1950', '1, 2, 3', ''],
            ['Casualty', 'Killed in action', '1, 2, 3', ''],
            ['Place', 'South Korea', '2, 3', ''],
            ['Home', 'Not given on the legacy page', '1, 2', 'The federal files give Los Angeles; the legacy page calls him a former Hart High School student.'],
        ],
        'editor' => [
            ['Correction, 2026', 'Robert L. Whisler\'s date of birth is given as 1931, not January 1, 1931: the federal casualty files give the year only (notes 1 and 2), and no source for the day has been found.'],
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. Both federal casualty files record his death (notes 1 and 2); his tie to the valley rests on the legacy page, which calls him a former Hart High School student.'],
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
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records sourced') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('source_wm_korea.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial sourcing, Korea: Thomas, Morissett, Montenegro, Kelly, Whisler from NARA\'s two Korean War casualty files; four January 1 birth dates cut to the year the federal files give');
if ($short) { throw new \RuntimeException('source_wm_korea: ' . implode(', ', $short)); }
