/**
 * The California Battalion (group #946): restore it from the trash as its own
 * record (Nathan, 3 October 2026, approving the re-search: "restoring the
 * California Battalion").
 *
 * WHY. fold_and_retire_records.php (applied 3 October, 09:23) trashed #946 and
 * folded it into John C. Frémont (#307) because "no source here names a
 * California Battalion". The search behind that ran through the agent shell's
 * grep, which skips the mirror's latin-1 pages. Three sources name it, and two
 * put it in this valley:
 *   Dr. Alan Pollack, "Kit Carson, Ned Beale Cross Paths and Alter California's
 *     Future" (Heritage Junction Dispatch, 2014): "Fremont's men, now referred to
 *     as the California Battalion, met up with U.S. Commodore Robert Stockton in
 *     Monterey."
 *   NorthLake Specific Plan Draft Supplemental EIR (May 2017), 5.3 Cultural
 *     Resources, and Tesoro del Valle Supplemental Draft EIR (February 2018), 5.5
 *     Cultural Resources: "Fremont's battalion marched through the Santa Clara
 *     River Valley and south to Mission San Fernando."
 *   Reynolds, chapter 18 (#857), already in the archive: Frémont's "buckskin
 *     battalion" at Castaic Junction on January 9, 1847, and Edwin Bryant "a
 *     member of the battalion".
 *
 * WHAT IT DOES
 *   1. Restores #946 from Craft's trash (the same element id, slug and fields),
 *      and writes a short body from those sources, each quotation checked against
 *      its page (_source_texts.php) or its archive record.
 *   2. Puts back the links the fold took off: personGroups on Frémont (#307),
 *      Carson (#315) and Bryant (#313), subjectGroup on Reynolds ch. 18 (#857),
 *      which keeps Frémont as subjectPerson. Andrés Pico (#317), who fought the
 *      battalion, was a member in the WordPress data; he is not put back, and
 *      #946's own groupPersons drops him (Nathan, 3 October 2026: "Pico off the
 *      Battalion: yes").
 *   3. Takes 'groups/california-battalion' out of config/redirects.php (and the
 *      battalion out of the comment above it) and #946 out of removedRecords in
 *      removed-claims.json, which would otherwise fail check_removed_claims.php.
 *      Both files are edited only on apply, after the database commits.
 *   4. editorNotes: one row for readers, saying why the record is back. The
 *      search is not on the record: it is in inventory/source-searches.json
 *      under groups:946 (docs/PROFILES.md).
 * Left as they are: withheldBody (the WordPress text, unsourced) and recordDates
 * (WordPress-derived). The group layout has no recordProvenance and no
 * groupKind field.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/restore_california_battalion.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $els = Craft::$app->getElements();
$SRC = require "$root/scripts/import/_source_texts.php"; $ws = $SRC['ws'];
$bad = $SRC['bad'];
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 946;

$BODY = 'The California Battalion was the force John C. Frémont commanded in the American conquest of California. Dr. Alan Pollack writes that in the summer of 1846 Frémont\'s men, "now referred to as the California Battalion," met Commodore Robert Stockton at Monterey.[1] In January 1847 the battalion came south through this valley. Jerry Reynolds has Frémont\'s hundred-man "buckskin battalion" arriving at Castaic Junction on the evening of January 9, 1847, and quotes Edwin Bryant, "a member of the battalion," on the rancho where it camped.[2] Two environmental impact reports prepared for projects in the valley, NorthLake (2017) and Tesoro del Valle (2018), say that "Fremont\'s battalion marched through the Santa Clara River Valley and south to Mission San Fernando."[3][4]';
$NOTES = [
    'Alan Pollack, "Kit Carson, Ned Beale Cross Paths and Alter California\'s Future," Heritage Junction Dispatch, November-December 2014, as carried on SCVHistory.com, /scvhistory/pollack1114kitcarson.htm: "The Mexican-American War began in April 1846. Three months later, Fremont\'s men, now referred to as the California Battalion, met up with U.S. Commodore Robert Stockton in Monterey."',
    'Jerry Reynolds, "Chapter 18. The Pathfinder," History of the Santa Clarita Valley, article #857 in this archive.',
    'County of Los Angeles, NorthLake Specific Plan Project, Draft Supplemental Environmental Impact Report, May 2017, section 5.3, Cultural Resources, as carried on SCVHistory.com, /scvhistory/northlakehills_deir_0517.htm: "Fremont in command of the California Battalion for the United States."',
    'Tesoro del Valle Phases A-B-C, Supplemental Draft Environmental Impact Report, February 2018, section 5.5, Cultural Resources, as carried on SCVHistory.com, /scvhistory/tesoro_sdeir0218.htm.',
];
$RESTORED = 'This record was briefly merged into John C. Frémont\'s on 3 October 2026, on the mistaken view that no source names a California Battalion. Three sources in the archive name it, and two place it in this valley, so it has its own record again.';
$ROWS = [['heading' => 'Restored, 3 October 2026', 'position' => 'bottom', 'note' => $RESTORED]];

/* Quotations, against their pages and records. */
foreach ([['pollack1114kitcarson', 'Three months later, Fremont\'s men, now referred to as the California Battalion, met up with U.S. Commodore Robert Stockton in Monterey'],
          ['northlake-deir-2017-p341', 'Fremont\'s battalion marched through the Santa Clara River Valley and south to Mission San Fernando'], ['northlake-deir-2017-p341', 'Fremont in command of the California Battalion for the United States'],
          ['tesoro-sdeir-2018-p486', 'Fremont\'s battalion marched through the Santa Clara River Valley and south to Mission San Fernando'], ['tesoro-sdeir-2018-p486', 'Fremont in command of the California Battalion'],
          ['reynolds-part18', 'Edwin Bryant, a member of the battalion'], ['ripley16', 'Fremont\'s divided battalion']] as [$k, $q]) {
    $ok = $SRC['has']($k, $q); echo ($ok ? 'quote ok   ' : 'QUOTE MISSING ') . "$k: \"" . mb_substr($q, 0, 70) . '"' . PHP_EOL; if (!$ok) { $bad[] = "$k does not read \"" . mb_substr($q, 0, 60) . '"'; }
}
$a857 = $get(857);
foreach (['Frémont and his hundred-man "buckskin battalion" marched southward along the coast, arriving at Castaic Junction on the evening of January 9, 1847', 'Edwin Bryant, a member of the battalion'] as $q) {
    $ok = $a857 && str_contains($ws(strip_tags((string)$a857->body)), $ws($q)); echo ($ok ? 'record ok  ' : 'RECORD MISSING ') . "#857: \"" . mb_substr($q, 0, 70) . '"' . PHP_EOL; if (!$ok) { $bad[] = "#857 does not read \"" . mb_substr($q, 0, 60) . '"'; }
}

/* The record, in the trash or already back. */
$live = $get($ID);
$trashed = $live ? null : Entry::find()->id($ID)->status(null)->trashed(true)->one();
$g = $live ?: $trashed;
if (!$g || $g->title !== 'California Battalion' || $g->section->handle !== 'groups') { echo 'REFUSING: #946 is not the California Battalion, live or trashed' . PHP_EOL; return; }
echo '#946 California Battalion: ' . ($live ? 'live already' : 'in the trash since ' . $trashed->dateDeleted->format('Y-m-d H:i') . ', slug ' . $trashed->slug . '; restore the same element') . PHP_EOL;
if (Entry::find()->section('groups')->slug($g->slug)->status(null)->id(['not', $ID])->exists()) { $bad[] = "another group now holds the slug {$g->slug}"; }
$curBody = trim((string)$g->body);
if ($curBody !== '' && $curBody !== $BODY) { $bad[] = '#946 has a body that is not this one; refusing to replace it'; }
echo 'BODY OLD: ' . ($curBody === '' ? '(empty)' : $curBody) . PHP_EOL . 'BODY NEW: ' . $BODY . PHP_EOL;
foreach ($NOTES as $i => $n) { echo '  [' . ($i + 1) . "] $n" . PHP_EOL; }
$curRows = array_values(array_filter($g->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
$addRows = array_values(array_filter($ROWS, fn($r) => !array_filter($curRows, fn($c) => ($c['note'] ?? '') === $r['note'])));
foreach ($addRows as $r) { echo "EDITOR NOTE ADD \"{$r['heading']}\": {$r['note']}" . PHP_EOL; }
$memOld = $g->groupPersons->ids(); $MEM = [307, 315, 313];
echo 'groupPersons OLD ' . json_encode($memOld) . ' -> NEW ' . json_encode($MEM) . ' (Pico #317 not put back)' . PHP_EOL;
$LINK = [307 => 'personGroups', 315 => 'personGroups', 313 => 'personGroups', 857 => 'subjectGroup'];
$linkPlan = [];
foreach ($LINK as $sid => $h) {
    $s = $get($sid); if (!$s || !$s->getFieldLayout()->getFieldByHandle($h)) { $bad[] = "#$sid has no $h"; continue; }
    $ids = $s->getFieldValue($h)->ids(); $has = in_array($ID, $ids, true);
    echo "  #$sid {$s->title} $h " . json_encode($ids) . ($has ? ' holds #946' : ' -> add #946') . PHP_EOL;
    if (!$has) { $linkPlan[$sid] = [$h, array_merge($ids, [$ID])]; }
}
$log = json_decode((string)@file_get_contents("$root/inventory/source-searches.json"), true)['records']['groups:946'] ?? [];
if (!$log) { $bad[] = 'inventory/source-searches.json has no search for groups:946'; }
echo 'Search log: inventory/source-searches.json, groups:946, ' . count($log) . ' entries' . PHP_EOL;
echo 'groupKind: ' . (Craft::$app->getFields()->getFieldByHandle('groupKind') ? 'exists, not set by this script' : 'no such field yet; nothing to set') . PHP_EOL;

/* The two files, shown exactly; written only on apply. */
$RF = "$root/config/redirects.php"; $redir = (string)file_get_contents($RF);
$R_OLD = "    /* Folded 3 October 2026 (fold_and_retire_records.php): the California\n       Battalion into Frémont, who holds his \"buckskin battalion\"; the\n       Catalonian Volunteers into the Portolá Expedition they marched with. */\n    'groups/california-battalion'                 => 'persons/john-c-fremont',\n";
$R_NEW = "    /* Folded 3 October 2026 (fold_and_retire_records.php): the Catalonian\n       Volunteers into the Portolá Expedition they marched with. The California\n       Battalion, folded the same day, was restored as its own record\n       (restore_california_battalion.php). */\n";
$redirDone = !str_contains($redir, "'groups/california-battalion'");
if (!$redirDone && substr_count($redir, $R_OLD) !== 1) { $bad[] = 'config/redirects.php does not hold the fold\'s rule exactly as written on 3 October'; }
echo 'config/redirects.php: ' . ($redirDone ? 'no battalion rule (done)' : 'REPLACE' . PHP_EOL . '--- old' . PHP_EOL . $R_OLD . '+++ new' . PHP_EOL . $R_NEW) . PHP_EOL;
$REGF = "$root/scripts/import/removed-claims.json"; $reg = json_decode((string)file_get_contents($REGF), true);
$entry = array_values(array_filter($reg['removedRecords'] ?? [], fn($x) => ($x['record'] ?? 0) === $ID));
echo 'removed-claims.json removedRecords: ' . ($entry ? 'REMOVE ' . json_encode($entry[0], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : 'no #946 entry (done)') . PHP_EOL;
if (preg_match('~\x{2014}|inventory/|\.json~u', $BODY . implode('', $NOTES) . $RESTORED)) { $bad[] = 'an em dash or a repository path in the text'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); if (array_values(array_unique(array_map('intval', $m[1]))) !== range(1, count($NOTES))) { $bad[] = 'notes are not used once each, in order'; }
echo 'Mirror mounted for a live hash check: ' . ($SRC['mirror'] ? 'yes' : 'no (checked against the byte copies only)') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($trashed && !$els->restoreElement($trashed)) { throw new \RuntimeException('restore #946 failed'); }
    $g = $get($ID);
    $g->setFieldValues(['body' => $BODY, 'footnotes' => $fn($NOTES), 'editorNotes' => array_merge($curRows, $addRows), 'groupPersons' => $MEM]);
    if (!$els->saveElement($g)) { throw new \RuntimeException('#946: ' . json_encode($g->getFirstErrors())); }
    foreach ($linkPlan as $sid => [$h, $ids]) { $s = $get($sid); $s->setFieldValue($h, $ids); if (!$els->saveElement($s)) { throw new \RuntimeException("#$sid $h: " . json_encode($s->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
if (!$redirDone) { file_put_contents($RF, str_replace($R_OLD, $R_NEW, $redir)); }
if ($entry) { $reg['removedRecords'] = array_values(array_filter($reg['removedRecords'], fn($x) => ($x['record'] ?? 0) !== $ID)); file_put_contents($REGF, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"); }
$r = $get($ID);
$ok = $r && trim((string)$r->body) === $BODY && $r->groupPersons->ids() == $MEM
    && !array_filter(array_keys($LINK), fn($sid) => !in_array($ID, $get($sid)->getFieldValue($LINK[$sid])->ids(), true))
    && !str_contains((string)file_get_contents($RF), "'groups/california-battalion'")
    && !array_filter(json_decode((string)file_get_contents($REGF), true)['removedRecords'], fn($x) => ($x['record'] ?? 0) === $ID);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('restore_california_battalion.php', 1 + count($linkPlan), $ok ? 'verified' : 'SHORT', 'California Battalion #946 restored from the trash: body from Pollack and the NorthLake and Tesoro EIRs; links back; redirect and registry entry removed');
if (!$ok) { throw new \RuntimeException('restore_california_battalion: read-back failed'); }
