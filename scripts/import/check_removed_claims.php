/**
 * READ ONLY. A claim deleted as impossible must not come back (Nathan,
 * 3 October 2026: "so nobody restores it later"). Fails if any sentence in
 * scripts/import/removed-claims.json is found in any record's body, author bio
 * or caption, or if a person record listed under removedRecords is live again
 * under that name (Nathan, 3 October 2026, removing John Wayne).
 * Returns ['ok' => bool, 'fails' => [...]] for check_render.php.
 * Run alone: ddev craft exec "eval(file_get_contents('scripts/import/check_removed_claims.php'))"
 */

ini_set('memory_limit', '2048M');
$REG = json_decode((string)file_get_contents(\Craft::getAlias('@root') . '/scripts/import/removed-claims.json'), true);
$C = $REG['claims'] ?? []; $RR = $REG['removedRecords'] ?? [];
$fails = [];
if ($C) {
    $norm = fn($s) => preg_replace('~\s+~u', ' ', str_replace("\u{00A0}", ' ', html_entity_decode(strip_tags((string)$s), ENT_QUOTES)));
    foreach (\craft\elements\Entry::find()->status(null)->each(200) as $e) {
        $l = $e->getFieldLayout(); $t = '';
        foreach (['body', 'authorBio', 'photoCaptionExt', 'wmNarrative'] as $f) { if ($l->getFieldByHandle($f)) { $t .= ' ' . $e->getFieldValue($f); } }
        $t = $norm($t);
        foreach ($C as $c) { if (str_contains($t, $norm($c['claim']))) { $fails[] = "REMOVED CLAIM BACK  {$e->section->handle} #{$e->id} {$e->title}: \"" . mb_substr($c['claim'], 0, 80) . '"'; } }
    }
}
foreach ($RR as $r) {
    /* A record removed as a duplicate shares its title with the one kept, so it is listed by slug (4 October 2026). */
    $q = \craft\elements\Entry::find()->section($r['section'])->status(null); $q = !empty($r['slug']) ? $q->slug($r['slug']) : $q->title($r['title']);
    foreach ($q->all() as $e) { $fails[] = "REMOVED RECORD BACK  {$r['section']} #{$e->id} {$e->title}: removed {$r['removed']} (" . mb_substr($r['why'], 0, 60) . ')'; }
}
/* A review decision still "approved" for a record that is no longer live would
   be recreated by create_records_from_review.php (Nathan, 3 October 2026: John
   Wayne's row was still approved after his record was removed, and so were 13
   others). A merge must point at a live record. */
$DEC = \Craft::getAlias('@review') . '/records-decided.json';
$SEC = ['person' => 'persons', 'place' => 'places', 'organization' => 'organizations'];
$nDec = 0;
if (is_file($DEC)) {
    $live = [];
    foreach ($SEC as $t => $s) { foreach (\craft\elements\Entry::find()->section($s)->status(null)->limit(null)->all() as $e) { $live[$t][mb_strtolower(trim((string)$e->title))] = true; } }
    foreach (json_decode((string)file_get_contents($DEC), true)['decisions'] ?? [] as $r) {
        $t = (string)($r['type'] ?? ''); if (!isset($SEC[$t])) { continue; }
        $nDec++; $act = (string)($r['action'] ?? '');
        if ($act === 'approved' && empty($live[$t][mb_strtolower(trim((string)($r['name'] ?? '')))])) { $fails[] = "STALE DECISION  {$r['name']} ($t) is approved in review/records-decided.json but has no live record; a rerun of create_records_from_review.php would recreate it"; }
        if ($act === 'merged' && !empty($r['into']) && !\craft\elements\Entry::find()->id((int)$r['into'])->status(null)->exists()) { $fails[] = "STALE DECISION  {$r['name']} ($t) is merged into #{$r['into']}, which is not live"; }
    }
}
echo ($fails ? implode(PHP_EOL, $fails) : 'no removed claim, record or stale decision has come back (' . count($C) . ' claims, ' . count($RR) . ' records listed, ' . $nDec . ' decisions checked)') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails];
