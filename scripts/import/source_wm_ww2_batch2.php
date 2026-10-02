/**
 * War memorial sourcing, World War II batch 2 (Nathan, 2 October 2026: "Yes,
 * fetch the Navy 1946 list and Oklahoma's Army list"). The pilot's pattern.
 *
 *   #510 Whitmore, #572 Cherry, #578 Fose      the Navy's 1946 California list
 *   #564 Redmond, #1407 Pineau                 the Army's California Honor List
 *   #520 Beall (batch 1)                       Oklahoma's Honor List added as
 *                                              note 4; his Sources note revised
 *
 * Not in this batch, and why: James Robert Ball, Lawrence E. Kenaston and
 * Thomas Milton Ross Jr. are not in California's Navy list (filed by next of
 * kin's state), and ABMC's directory would not open their records; Robert
 * Russell Cone is not in the Los Angeles County Army list; Augustus Rubel
 * served in the American Field Service, a civilian volunteer corps outside
 * both lists.
 *
 * FIELDS: Redmond's date of death February 17, 1945 -> December 21, 1944 (the
 * ABMC certificate in his record), with a correction note. Pineau: burialPlace
 * held his whole narrative (a migration parse; the narrative is in his body),
 * cut to the cemetery; his empty date of death filled from the legacy page's
 * own incident date. Redmond's empty service number filled from the Honor List.
 * THE SOURCES: inventory/sources/wm-ww2-batch2-2026-10-02.json.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_ww2_batch2.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$flat = json_encode(json_decode((string)@file_get_contents("$root/inventory/sources/wm-ww2-batch2-2026-10-02.json"), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$bad = [];
foreach (['1445 Walnut St., Newhall', 'CHERRY, Perry Leon, Seaman 2c, USN', 'FOSE, Robert Remy, Seaman 2c, USNR', 'REDMOND JAMES M 19179914 S SG KIA', 'PINEAU WILLIAM E 39693386 AV C DNB', 'BEALL ARCHIBALD K 38154662 PFC KIA', 'date of death December 21, 1944', 'Agua Dulce Canyon, Saugus'] as $ph) {
    if (!str_contains($flat, $ph)) { $bad[] = 'the sources file does not read ' . mb_substr($ph, 0, 50); }
}
$p = Entry::find()->id(1407)->status(null)->one();
if (!str_contains(strip_tags((string)$p->body), 'was born in Albuquerque on April 30, 1924')) { $bad[] = 'Pineau\'s body no longer holds his narrative; his burial field would lose it'; }
if (!str_starts_with(trim((string)$p->burialPlace), 'Los Angeles National Cemetery, 950 S. Sepulveda Blvd. Plot 100, C16') && trim((string)$p->burialPlace) !== 'Los Angeles National Cemetery, 950 S. Sepulveda Blvd., Plot 100, C16') { $bad[] = 'Pineau\'s burial field is not as read'; }
/* Beall: Oklahoma's Honor List as note 4, a casualty fact, the Sources note revised. */
$B = Entry::find()->id(520)->status(null)->one();
$BEALL_NOTE = 'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from Oklahoma (1946), Lincoln County, National Archives NAID 305311: "BEALL ARCHIBALD K 38154662 PFC KIA."';
$BEALL_SRC_OLD = 'This record does not yet meet the archive\'s rule for a memorial record. He enlisted in Oklahoma, so his federal record of death is in Oklahoma\'s 1946 Honor List, which has not been read; and his tie to the valley rests on the legacy page alone.';
$BEALL_SRC_NEW = 'This record meets half of the archive\'s rule for a memorial record. Oklahoma\'s 1946 Honor List records his death, under Lincoln County (note 4); his tie to the valley rests on the legacy page alone.';
$bealDo = !in_array($BEALL_NOTE, array_column($B->footnotes ?? [], 'note'), true);
echo '#520 Beall: ' . ($bealDo ? 'add note 4 (Oklahoma Honor List), a casualty fact, revise the Sources note' : 'already done') . PHP_EOL;
if ($bealDo && !in_array($BEALL_SRC_OLD, array_column($B->editorNotes ?? [], 'note'), true)) { $bad[] = 'Beall\'s Sources note is not the one expected'; }
$LEGACY = fn($path) => "SCVHistory.com, Santa Clarita Valley War Memorial, page for him ($path), as migrated to this record.";
$R = [
    510 => [
        'title' => 'Frank Pike Whitmore', 'expect' => ['deathDate' => '10-9-1943'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'Navy Department, State Summary of War Casualties from World War II for Navy, Marine Corps, and Coast Guard Personnel from California (1946), Dead, National Archives NAID 305189: "WHITMORE, Frank Pike, Soundman 3c, USN. Parents, Mr. and Mrs. George Fred Whitmore, 1445 Walnut St., Newhall."',
            $LEGACY('/warmemorial/ww2_frankwhitmore.htm'),
        ],
        'facts' => [
            ['Rank', 'Soundman', '1, 2', 'The Navy\'s list gives Soundman third class.'],
            ['Branch', 'U.S. Navy', '1, 2', ''],
            ['Ship', 'USS Buck, a destroyer', '2', ''],
            ['Date of death', 'October 9, 1943', '2', 'The Navy\'s list gives no date.'],
            ['Place', 'Off Salerno, Italy, when the Buck was sunk by a U-boat', '2', ''],
            ['Home', 'Soledad Township', '1, 2', 'The Navy\'s list gives his parents at 1445 Walnut St., Newhall.'],
            ['Age', '23', '2', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record, both ways in one document: the Navy\'s 1946 list records his death and gives his parents\' address as Newhall (note 1).'],
        ],
    ],
    572 => [
        'title' => 'Perry Leon Cherry', 'expect' => ['wmBirthplace' => 'Kansas'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'Navy Department, State Summary of War Casualties from World War II for Navy, Marine Corps, and Coast Guard Personnel from California (1946), Dead, National Archives NAID 305189: "CHERRY, Perry Leon, Seaman 2c, USN. Parents, Mr. and Mrs. Frank John Cherry, 3200 1/2 W. 109th St., Inglewood." The page is held in this archive.',
            $LEGACY('/warmemorial/ww2_leoncherry.htm'),
        ],
        'facts' => [
            ['Rank', 'Seaman Second Class', '1, 2', ''],
            ['Service number', '3816051', '2', 'The legacy page queries a second number, XC-3618457.'],
            ['Ship', 'USS Thatcher', '2', ''],
            ['Date of death', 'May 26, 1944', '2', 'The Navy\'s list gives no date.'],
            ['Place', 'Marshall Islands, in a gunnery accident', '2', ''],
            ['Born', 'July 8, 1925, Kansas', '2', ''],
            ['High school', 'San Fernando', '2', ''],
            ['Home', 'Newhall', '2', 'The Navy\'s list gives his parents at Inglewood.'],
            ['Burial', 'Probably at sea; a marker at Inglewood Park Cemetery', '2', 'The legacy page is uncertain of a burial at sea.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Navy\'s 1946 list records his death (note 1), with his parents at Inglewood; his tie to the valley rests on the legacy page.'],
        ],
    ],
    578 => [
        'title' => 'Robert Remy Fose', 'expect' => ['deathDate' => 'October 12, 1943'],
        'set' => [],
        'fill' => [],
        'notes' => [
            'Navy Department, State Summary of War Casualties from World War II for Navy, Marine Corps, and Coast Guard Personnel from California (1946), Dead, National Archives NAID 305189: "FOSE, Robert Remy, Seaman 2c, USNR. Wife, Mrs. Lucretia Maryan Fose, 2115 Valentine St., Los Angeles."',
            $LEGACY('/warmemorial/ww2_robertfose.htm'),
        ],
        'facts' => [
            ['Rank', 'Seaman Second Class', '1', ''],
            ['Branch', 'U.S. Naval Reserve', '1', 'The record gives U.S. Navy.'],
            ['Ship', 'USS Boise', '2', ''],
            ['Date of death', 'October 12, 1943', '2', 'The Navy\'s list gives no date.'],
            ['Place', 'Battle of Cape Esperance, near Guadalcanal', '2', ''],
            ['Age', '34', '2', ''],
            ['Home', 'Newhall', '2', 'The Navy\'s list gives his wife at Los Angeles.'],
            ['Burial', 'Valhalla Cemetery, Burbank', '2', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Navy\'s 1946 list records his death (note 1), with his wife at Los Angeles; his tie to the valley rests on the legacy page.'],
        ],
    ],
    564 => [
        'title' => 'James M. Redmond', 'expect' => ['deathDate' => 'February 17, 1945'],
        'set' => ['deathDate' => 'December 21, 1944', 'deathDateEdtf' => '1944-12-21'],
        'fill' => ['wmServiceId' => '19179914'],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "REDMOND JAMES M 19179914 S SG KIA."',
            'American Battle Monuments Commission, memorial certificate, held in this archive: James M. Redmond, Staff Sergeant, U.S. Army, 299th Engineer Combat Battalion; date of death December 21, 1944; Ardennes American Cemetery, Neupré, Belgium.',
            $LEGACY('/warmemorial/ww2_jamesredman.htm'),
        ],
        'facts' => [
            ['Service number', '19179914', '1', ''],
            ['Rank', 'Staff Sergeant', '1, 2, 3', ''],
            ['Unit', '299th Engineer Combat Battalion', '2, 3', ''],
            ['Casualty', 'Killed in action', '1, 3', ''],
            ['Date of death', 'December 21, 1944', '2', 'The record gave February 17, 1945; ABMC gives December 21, 1944, during the Battle of the Bulge.'],
            ['Place', 'Belgium', '2, 3', ''],
            ['Home', 'Soledad Township', '1, 3', 'The Honor List gives Los Angeles County.'],
            ['Burial', 'Ardennes American Cemetery, Neupré, Belgium (Plot D, Row 6, Grave 38)', '2, 3', 'The plot, row and grave are from the legacy page.'],
        ],
        'editor' => [
            ['Correction, 2026', 'James M. Redmond died on December 21, 1944, not February 17, 1945 as the record gave it: the American Battle Monuments Commission\'s certificate for him, held in this record, gives December 21, 1944 (note 2).'],
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Honor List and ABMC record his death (notes 1 and 2); his tie to the valley rests on the legacy page.'],
        ],
    ],
    1407 => [
        'title' => 'William Ernest Pineau', 'expect' => ['wmServiceId' => '39693386'],
        'set' => ['burialPlace' => 'Los Angeles National Cemetery, 950 S. Sepulveda Blvd., Plot 100, C16'],
        'fill' => ['deathDate' => 'May 11, 1944', 'deathDateEdtf' => '1944-05-11'],
        'notes' => [
            'War Department, World War II Honor List of Dead and Missing Army and Army Air Forces Personnel from California (1946), Los Angeles County, National Archives NAID 305280: "PINEAU WILLIAM E 39693386 AV C DNB."',
            'World War II draft registration card, William Ernest Pineau, June 30, 1942, Local Board No. 175, Palmdale (National Archives, Record Group 147), held in this archive: residence Agua Dulce Canyon, Saugus, Los Angeles County; mailing address Rt. 1, Box 158, Saugus; born April 30, 1924, Albuquerque, New Mexico.',
            $LEGACY('/scvhistory/ww2_williamernestpineau.htm'),
        ],
        'facts' => [
            ['Service number', '39693386', '1, 3', ''],
            ['Rank', 'Aviation Cadet', '1, 3', ''],
            ['Branch', 'U.S. Army Air Forces', '1, 3', 'The record gives Army Air Corps.'],
            ['Unit', 'Class 44-H, Squadron 26, 2nd Training Group, Minter Field', '3', ''],
            ['Born', 'April 30, 1924, Albuquerque, New Mexico', '2, 3', ''],
            ['Registered for the draft', 'June 30, 1942, Local Board 175, Palmdale', '2', 'The legacy page\'s fact list gives June 30, 1944; its narrative says 1942.'],
            ['Date of death', 'May 11, 1944', '3', 'The Honor List gives no date.'],
            ['Casualty', 'Died non-battle: the crash of a BT-13 trainer at Semitropic, Kern County', '1, 3', ''],
            ['Home', 'Agua Dulce Canyon, Saugus', '2, 3', ''],
            ['Burial', 'Los Angeles National Cemetery (Plot 100, C16)', '3', ''],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record: the Honor List records his death (note 1), and his draft card, a federal record, places him in Agua Dulce Canyon, Saugus (note 2).'],
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
if ($bealDo && !$short) {
    $B = Entry::find()->id(520)->status(null)->one();
    $fn = array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? '')], $B->footnotes ?? []);
    $fn[] = ['number' => (string)(count($fn) + 1), 'note' => $BEALL_NOTE, 'source' => 'editorial-2026'];
    $fs = array_map(fn($r) => ['fact' => (string)($r['fact'] ?? ''), 'value' => (string)($r['value'] ?? ''), 'notes' => (string)($r['notes'] ?? ''), 'agreement' => (string)($r['agreement'] ?? '')], $B->factSources ?? []);
    foreach ($fs as $i => $r) { if ($r['fact'] === 'Service number') { $fs[$i]['notes'] = '1, 4'; } if ($r['fact'] === 'Rank') { $fs[$i]['notes'] .= ', 4'; } }
    array_splice($fs, 2, 0, [['fact' => 'Casualty', 'value' => 'Killed in action', 'notes' => '4', 'agreement' => '']]);
    $ed = array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '') === $BEALL_SRC_OLD ? $BEALL_SRC_NEW : (string)($r['note'] ?? '')], array_filter($B->editorNotes ?? [], 'is_array'));
    $B->setFieldValues(['footnotes' => $fn, 'factSources' => $fs, 'editorNotes' => array_values($ed)]);
    $ok = Craft::$app->getElements()->saveElement($B) && in_array($BEALL_NOTE, array_column(Entry::find()->id(520)->status(null)->one()->footnotes ?? [], 'note'), true);
    echo 'BEALL ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
    if (!$ok) { throw new \RuntimeException('source_wm_ww2_batch2: Beall'); }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records sourced') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('source_wm_ww2_batch2.php', count($plan), $short ? 'SHORT' : 'verified', 'war memorial sourcing, World War II batch 2: Whitmore, Cherry, Fose from the Navy\'s 1946 list; Redmond and Pineau from the Army Honor List; Redmond\'s death corrected to December 21, 1944 from ABMC; Pineau\'s burial field cleaned');
if ($short) { throw new \RuntimeException('source_wm_ww2_batch2: ' . implode(', ', $short)); }
