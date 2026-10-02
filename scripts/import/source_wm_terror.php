/**
 * War memorial sourcing, the War on Terror (Nathan, 2 October 2026). Cole
 * William Larsen was in the pilot.
 *
 * SOURCED, from NARA's DCAS Public Use File (theater deaths to May 2006):
 *   #546 Prosser, #532 Flores-Mejia, #530 Slocum
 * Prosser's home of record is Panorama Heights, near Frazier Park in Kern
 * County, outside the valley; the legacy page gives Frazier Park. Said, not
 * changed.
 * NOT YET SOURCED, each with a note saying what was searched:
 *   #540 Sellen, #538 Gelig, #536 Suter, #526 Acosta  died 2007 to 2011, after
 *        the DCAS file; their Defense Department releases could not be found
 *        by name
 *   #528 Wilson, #542 Todd, #534 Conant, #524 Colley  died outside a theater
 *        of war: in neither source; the VA gravesite locator would hold them
 * THE SOURCES: inventory/sources/wm-terror-2026-10-02.json.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_wm_terror.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$flat = json_encode(json_decode((string)@file_get_contents("$root/inventory/sources/wm-terror-2026-10-02.json"), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$bad = [];
foreach (['home of record PANARAMA HEIGHTS', 'home of record SANTA CLARITA', 'home of record SAUGUS'] as $ph) { if (!str_contains($flat, $ph)) { $bad[] = 'the sources file does not read ' . $ph; } }
$NF = [
    540 => ['Dennis Lee Sellen Jr', 'Not yet sourced. He was killed in Iraq in February 2007, after the National Archives\' casualty file ends (May 2006). The Defense Department\'s release of his death has not yet been found: defense.gov\'s release search begins in 2014, and its earlier releases are not indexed by name. Next to try: the release archive by date, and ABMC.'],
    538 => ['Ian Timothy D. Gelig', 'Not yet sourced. He was killed in Afghanistan in March 2010, after the National Archives\' casualty file ends (May 2006). The Defense Department\'s release of his death has not yet been found: defense.gov\'s release search begins in 2014, and its earlier releases are not indexed by name. Next to try: the release archive by date, and ABMC.'],
    536 => ['Jake William Suter', 'Not yet sourced. He was killed in Afghanistan in May 2010, after the National Archives\' casualty file ends (May 2006). The Defense Department\'s release of his death has not yet been found: defense.gov\'s release search begins in 2014, and its earlier releases are not indexed by name. Next to try: the release archive by date, and ABMC.'],
    526 => ['Rudy Alexander Acosta', 'Not yet sourced. He was killed in Afghanistan in March 2011, after the National Archives\' casualty file ends (May 2006). The Defense Department\'s release of his death has not yet been found: defense.gov\'s release search begins in 2014, and its earlier releases are not indexed by name. Next to try: the release archive by date, and ABMC.'],
    528 => ['Robert Michael Wilson', 'Not yet sourced. He died outside a theater of war, so he is in neither the National Archives\' war casualty file nor a Defense Department casualty release, the two sources read so far (2 October 2026). Next to try, with Nathan\'s approval: the Department of Veterans Affairs\' Nationwide Gravesite Locator, a federal record of burial.'],
    542 => ['Dean Glenn Todd Jr', 'Not yet sourced. He died outside a theater of war, so he is in neither the National Archives\' war casualty file nor a Defense Department casualty release, the two sources read so far (2 October 2026). Next to try, with Nathan\'s approval: the Department of Veterans Affairs\' Nationwide Gravesite Locator, a federal record of burial.'],
    534 => ['John Michael Conant', 'Not yet sourced. He died outside a theater of war, so he is in neither the National Archives\' war casualty file nor a Defense Department casualty release, the two sources read so far (2 October 2026). Next to try, with Nathan\'s approval: the Department of Veterans Affairs\' Nationwide Gravesite Locator, a federal record of burial.'],
    524 => ['Stephen Edward Colley', 'Not yet sourced. He died outside a theater of war, so he is in neither the National Archives\' war casualty file nor a Defense Department casualty release, the two sources read so far (2 October 2026). Next to try, with Nathan\'s approval: the Department of Veterans Affairs\' Nationwide Gravesite Locator, a federal record of burial.'],
];

$nf = [];
foreach ($NF as $id => [$t, $n]) { $x = Entry::find()->id($id)->status(null)->one(); if (!$x || $x->title !== $t) { $bad[] = "#$id is not $t"; continue; } if (!in_array($n, array_column($x->editorNotes ?? [], 'note'), true)) { $nf[$id] = $n; echo "#$id $t: searched-and-not-found note" . PHP_EOL; } }
$LEGACY = fn($path) => "SCVHistory.com, Santa Clarita Valley War Memorial, page for him ($path), as migrated to this record.";
$R = [
    546 => [
        'title' => 'Brian Cody Prosser', 'expect' => [],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Defense Casualty Analysis System Public Use File, 1950-2005 (Record Group 330): Prosser, Brian Cody, Staff Sergeant, Army, Regular; born July 17, 1973; home of record Panorama Heights ("Panarama Heights" as entered); died of wounds, December 5, 2001, Kahneh Gerdab, Afghanistan, an explosive device; D Company, 3rd Battalion, 5th Special Forces Group; Operation Enduring Freedom.',
            $LEGACY('/warmemorial/terror_brianprosser.htm'),
        ],
        'facts' => [
            ['Rank', 'Staff Sergeant', '1, 2', ''],
            ['Unit', 'D Company, 3rd Battalion, 5th Special Forces Group', '1, 2', ''],
            ['Born', 'July 17, 1973', '1, 2', ''],
            ['Date of death', 'December 5, 2001', '1, 2', ''],
            ['Casualty', 'Died of wounds: a bomb (the legacy page: a B-52\'s bomb called in on Taliban forces, friendly fire)', '1, 2', ''],
            ['Place', 'Kahneh Gerdab, Afghanistan', '1', 'The record gives Showli Kowt.'],
            ['Home', 'Frazier Park', '1, 2', 'The federal file gives Panorama Heights, near Frazier Park in Kern County; both are outside the Santa Clarita Valley.'],
            ['Burial', 'Arlington National Cemetery (Section 64, Site 7186)', '2', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets half of the archive\'s rule for a memorial record. The Defense Department\'s casualty file records his death (note 1), but gives his home as Panorama Heights, near Frazier Park in Kern County, as the legacy page gives Frazier Park; no source yet ties him to the Santa Clarita Valley itself.'],
        ],
    ],
    532 => [
        'title' => 'Jose Ricardo Flores-Mejia', 'expect' => [],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Defense Casualty Analysis System Public Use File, 1950-2005 (Record Group 330): Flores-Mejia, Jose Ricardo, Specialist, Army, Regular; born July 20, 1983; home of record Santa Clarita; killed in action, November 16, 2004, Mosul, Iraq, an explosive device; 25th Transportation Company, Schofield Barracks; Operation Iraqi Freedom.',
            $LEGACY('/warmemorial/terror_josefloresmejia.htm'),
        ],
        'facts' => [
            ['Rank', 'Private First Class', '2', 'The federal file gives Specialist.'],
            ['Unit', '25th Transportation Company, Schofield Barracks', '1, 2', ''],
            ['Born', 'July 20, 1983', '1, 2', ''],
            ['Date of death', 'November 16, 2004', '1, 2', ''],
            ['Casualty', 'Killed in action: an explosive device', '1', ''],
            ['Place', 'Near Mosul, Iraq', '1, 2', 'The record gives Qayyara, south of Mosul; the federal file gives Mosul.'],
            ['Home', 'Santa Clarita', '1, 2', ''],
            ['Burial', 'Eternal Valley Memorial Park, Newhall', '2', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Defense Department\'s casualty file records his death and gives his home of record as Santa Clarita (note 1).'],
        ],
    ],
    530 => [
        'title' => 'Richard Patrick Slocum', 'expect' => [],
        'set' => [],
        'fill' => [],
        'notes' => [
            'National Archives, Defense Casualty Analysis System Public Use File, 1950-2005 (Record Group 330): Slocum, Richard Patrick, Lance Corporal, Marine Corps, Regular; born February 2, 1985; home of record Saugus; non-hostile, accident, vehicle crash, October 24, 2004, Abu Gharib, Iraq: a turret gunner in a government Humvee; Weapons Company, 1st Battalion, 3rd Marines (RCT-7, 1st Marine Division); Operation Iraqi Freedom.',
            $LEGACY('/warmemorial/terror_richardslocum.htm'),
        ],
        'facts' => [
            ['Rank', 'Lance Corporal', '1, 2', ''],
            ['Unit', 'Weapons Company, 1st Battalion, 3rd Marines', '1, 2', 'The federal file adds RCT-7, 1st Marine Division.'],
            ['Born', 'February 2, 1985', '1, 2', ''],
            ['Date of death', 'October 24, 2004', '1, 2', ''],
            ['Casualty', 'Non-hostile: a vehicle crash, as a Humvee\'s turret gunner', '1, 2', ''],
            ['Place', 'Abu Ghraib, Iraq', '1, 2', ''],
            ['Home', 'Saugus', '1, 2', ''],
            ['Burial', 'Eternal Valley Memorial Park, Newhall', '2', 'From the legacy page only.'],
        ],
        'editor' => [
            ['Sources, 2026', 'This record meets the archive\'s rule for a memorial record in one federal document: the Defense Department\'s casualty file records his death and gives his home of record as Saugus (note 1).'],
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
    foreach ($nf as $id => $n) { $x = Entry::find()->id($id)->status(null)->one(); $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($x->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''))); $rows[] = ['heading' => 'Sources, 2026', 'position' => 'bottom', 'note' => $n]; $x->setFieldValue('editorNotes', $rows); if (!Craft::$app->getElements()->saveElement($x)) { $short[] = "#$id note"; } }
    echo 'NOT-FOUND NOTES: ' . count($nf) . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records sourced') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('source_wm_terror.php', count($plan) + count($nf), $short ? 'SHORT' : 'verified', 'war memorial sourcing, War on Terror: Prosser, Flores-Mejia, Slocum from NARA\'s DCAS file; searched-and-not-found notes on eight');
if ($short) { throw new \RuntimeException('source_wm_terror: ' . implode(', ', $short)); }
