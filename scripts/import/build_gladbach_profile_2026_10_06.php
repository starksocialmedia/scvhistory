/**
 * Jerry Gladbach #28336: the editorial profile (Nathan, 6 October 2026: "Treat it as campaign material: attribute rather
 * than state, and cut the promotional language entirely ... Lead with the valley ... leave the wife, children and
 * grandchildren out ... Note on the record that the biography is campaign material and that I wrote it").
 * Reads inventory/review/jerry-gladbach-profile-draft-2026-10-06.json, made by
 * scripts/import/draft_gladbach_profile_2026_10_06.py, where every quotation is checked against its saved source
 * (inventory/sources/jerry-gladbach-2026-10-06/manifest.json).
 *
 * WHAT IT WRITES
 *  - #28336: body (leads with the Castaic Lake Water Agency and SCV Water, then the LADWP, ACWA, ACWA/JPIA, LAFCO,
 *    CALAFCO and NWRA; what only his campaign biography says is attributed to it), footnotes (the one existing note,
 *    SCV Water's release and Resolution SCV-329, is replaced by fuller notes that keep both quotations), two editor's
 *    notes ("About the sources": the biography is campaign material written by Nathan Imhoff, the archive's editor;
 *    "His name in the sources"), occupation where empty. Replaces only the one-sentence body derive_board_holdings.php
 *    wrote; refuses any other body that differs.
 *  - Two office holdings the archive lacks, Water Board Director, Castaic Lake Water Agency: January 1985 to December
 *    2012 (elected; one run, the County's returns before 2016 not held) and January 2013 to December 2016 (Division 2,
 *    elected unopposed, the County's list of 2012). #28553 (2016) and #28427 (SCV Water, died in office) are not changed.
 *  - On apply only: the search behind "no other source found" into inventory/source-searches.json (docs/PROFILES.md).
 *  - NOT his title, aliases or portrait (another step retitles him today).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/jerry-gladbach-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_gladbach_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$D = json_decode(file_get_contents("$root/inventory/review/jerry-gladbach-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$P = 28336; $ROLE = 27420; $CLWA = 26563; $DIV2 = 26568; $H16 = 28553; $HSCV = 28427; $CAND = 26613;
$bad = []; $out = [];
$p = $get($P);
if (!$p || $p->section->handle !== 'persons' || !preg_match('/Gladbach/', (string)$p->title)) { $bad[] = "#$P is not Gladbach"; }
foreach ([$ROLE => 'Water Board Director', $CLWA => 'Castaic Lake Water Agency'] as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }
if (!str_contains((string)$get($DIV2)?->title, 'Division 2')) { $bad[] = "#$DIV2 is not Division 2"; }
foreach ([$H16, $HSCV] as $hid) { if ($get($hid)?->holdingPerson->status(null)->one()?->id !== $P) { $bad[] = "holding #$hid is not his"; } }
if (!str_starts_with((string)$get($CAND)?->title, 'E G GLADBACH')) { $bad[] = "candidacy #$CAND does not print E G GLADBACH, which his name note says"; }
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . implode('', array_column($D['editorNotes'], 'note')) . json_encode(array_column($D['holdings'], 'notes'), JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in our own text'; }
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$rows = fn(array $notes) => array_map(fn($n, $i) => ['number' => (string)($i + 1), 'note' => is_array($n) ? $n['note'] : $n, 'source' => 'editorial-2026'], $notes, array_keys($notes));
$filled = fn($rs, $k = 'note') => array_values(array_filter(iterator_to_array($rs ?? []), fn($r) => trim((string)($r[$k] ?? '')) !== ''));

/* 1. the person */
$pl = $p ? $lay($p) : []; $set = [];
foreach (['body', 'footnotes', 'editorNotes', 'occupation', 'bodyAuthorship', 'recordProvenance'] as $h) { if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; } }
$cur = trim((string)$p?->body); $new = trim($D['body']);
if ($cur !== $new && $cur !== '' && $cur !== trim($D['replacesBody'])) { $bad[] = "#$P has a body other than the derived sentence; refusing to replace it"; }
$oldNotes = $p ? $filled($p->footnotes) : [];
if ($cur !== $new) { $set['body'] = $D['body']; $set['footnotes'] = array_map(fn($f) => ['number' => $f['number'], 'note' => $f['note'], 'source' => 'editorial-2026'], $D['footnotes']); }
$en = $p ? $filled($p->editorNotes) : [];
$haveEn = array_column($en, 'heading');
/* the retitling step of today wrote its own "His name" note; ours on names is then left for Nathan, not added beside it */
$nameNoteThere = (bool)array_filter($haveEn, fn($h) => str_starts_with((string)$h, 'His name'));
$addEn = array_values(array_filter($D['editorNotes'], fn($n) => !in_array($n['heading'], $haveEn, true) && !($nameNoteThere && str_starts_with($n['heading'], 'His name'))));
if ($addEn) { $set['editorNotes'] = array_merge(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position']], $en), $addEn); }
if ($p && trim((string)$p->occupation) === '') { $set['occupation'] = $D['fields']['occupation']; }
if ($p && (string)$p->bodyAuthorship?->value !== 'editorial-2026') { $set['bodyAuthorship'] = 'editorial-2026'; }
/* recordProvenance holds 255 characters; the newest entry goes first and the oldest is cut (6 October 2026). */
if ($set) { $set['recordProvenance'] = mb_substr(trim('build_gladbach_profile_2026_10_06.php, 6 October 2026: profile, campaign biography attributed, two earlier CLWA terms; ' . (string)$p->recordProvenance, '; '), 0, 255); }

/* 2. the two earlier Castaic Lake holdings */
$have = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $P, 'field' => 'holdingPerson'])->all();
$plan = [];
foreach ($D['holdings'] as $h) {
    $dup = array_filter($have, fn($x) => (string)$x->termStartEdtf === $h['termStartEdtf'] && $x->holdingBody->status(null)->one()?->id === $CLWA);
    /* no overlap with what he holds at the agency already */
    foreach ($have as $x) { if ($x->holdingBody->status(null)->one()?->id === $CLWA && !$dup && strcmp((string)$x->termStartEdtf, $h['termEndEdtf']) <= 0 && strcmp($h['termStartEdtf'], substr((string)$x->termEndEdtf, 0, 7)) <= 0) { $bad[] = "$h[key] overlaps #{$x->id}"; } }
    $plan[] = $h + ['dup' => (bool)$dup];
}
$opts = fn($handle) => array_column(Craft::$app->getFields()->getFieldByHandle($handle)->options, 'value');
foreach ($plan as $h) { foreach (['selectionMethod', 'howEnded', 'startEvidence', 'endEvidence'] as $f) { if (!in_array($h[$f], $opts($f), true)) { $bad[] = "$h[key]: $f '$h[$f]' is not an option"; } } }

/* 3. the search record */
$ssPath = "$root/inventory/source-searches.json"; $ss = json_decode(file_get_contents($ssPath), true);
$ssKey = array_key_first($D['sourceSearch']); $ssAdd = !array_filter($ss['records'][$ssKey] ?? [], fn($r) => ($r['note'] ?? '') === $D['sourceSearch'][$ssKey]['note']);

/* the report */
$out[] = "# Jerry Gladbach #28336: the profile, dry run (6 October 2026)\n\n" . ($APPLY ? 'APPLIED.' : 'Nothing is written by a dry run.') . " The prose, every note and the two holdings' notes are in inventory/review/jerry-gladbach-profile-draft-2026-10-06.md; " . $D['quotationsChecked'] . " quotations there were checked against their saved sources.\n";
$out[] = "## #28336 " . $p?->title . "\n\n- title, aliases (" . str_replace("\n", ', ', (string)$p?->personAliases) . ") and portrait: not touched\n- body now: " . ($cur === '' ? '(empty)' : $cur) . "\n- body after: " . mb_strlen($D['body']) . " characters, " . count($D['footnotes']) . " notes" . ($cur === $new ? ' (already in place)' : '') . "\n";
$out[] = '- the existing note, replaced: ' . ($oldNotes ? implode(' | ', array_map(fn($r) => $r['note'], $oldNotes)) : 'none') . "\n  (its two quotations are kept: the release in note " . (array_search('REL', array_column($D['footnotes'], 'key')) + 1) . ", the resolution in note " . (array_search('RES', array_column($D['footnotes'], 'key')) + 1) . ")\n";
$out[] = '- existing editor\'s notes: ' . ($en ? implode(' | ', array_map(fn($r) => "$r[heading]: $r[note]", $en)) : 'none');
if ($nameNoteThere) { $out[] = '- our "His name in the sources" is NOT added: a "His name" note is already on the record (the retitling step). Ours is in the draft .md for Nathan to merge or drop.'; }
foreach (['editorNotes', 'occupation', 'bodyAuthorship', 'recordProvenance'] as $h) { if (isset($set[$h])) { $out[] = "- $h: " . (is_array($set[$h]) ? implode(' | ', array_map(fn($r) => "$r[heading]: $r[note]", $set[$h])) : $set[$h]); } }
$out[] = "\n## New office holdings (Water Board Director, Castaic Lake Water Agency #$CLWA)\n";
foreach ($plan as $h) { $out[] = "- $h[termStart] to $h[termEnd] ($h[termStartEdtf]/$h[termEndEdtf]): selectionMethod $h[selectionMethod], howEnded $h[howEnded], evidence $h[startEvidence]/$h[endEvidence]" . ($h['district'] ? ", district #$h[district] $h[seatLabel]" : '') . ', ' . count($h['notes']) . ' notes' . ($h['dup'] ? ' (ALREADY HELD, skipped)' : ''); }
$out[] = "- unchanged: #$H16 (2017, from the 2016 election) and #$HSCV (SCV Water, 2018 to his death in office on 13 July 2022)\n";
$out[] = "## inventory/source-searches.json\n\n- $ssKey: " . ($ssAdd ? 'add the search behind "' . $D['sourceSearch'][$ssKey]['note'] . '" (on apply only)' : 'already there') . "\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes\n\n" . implode("\n", array_map(fn($f) => "$f[number]. $f[note]", $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/jerry-gladbach-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo "#$P: " . count($set) . ' fields; holdings to create: ' . count(array_filter($plan, fn($h) => !$h['dup'])) . '; search record: ' . ($ssAdd ? 'add' : 'there') . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/jerry-gladbach-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$made = 0; $os = $svc->getSectionByHandle('officeHoldings'); $type = $svc->getEntryTypeByHandle('officeHolding');
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($P); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException("#$P: " . json_encode($e->getFirstErrors())); } }
    foreach ($plan as $h) {
        if ($h['dup']) { continue; }
        $e = new Entry(); $e->sectionId = $os->id; $e->setTypeId($type->id);
        $v = ['holdingPerson' => [$P], 'holdingOffice' => [$ROLE], 'holdingBody' => [$CLWA], 'termStart' => $h['termStart'], 'termStartEdtf' => $h['termStartEdtf'],
            'termEnd' => $h['termEnd'], 'termEndEdtf' => $h['termEndEdtf'], 'selectionMethod' => $h['selectionMethod'], 'howEnded' => $h['howEnded'],
            'startEvidence' => $h['startEvidence'], 'endEvidence' => $h['endEvidence'], 'footnotes' => $rows($h['notes']),
            'recordProvenance' => 'build_gladbach_profile_2026_10_06.php, 6 October 2026: ' . ($h['selectionMethod'] === 'unopposed' ? "the County's list of 2012" : 'SCV Water Resolution SCV-329 and release of 18 July 2022')];
        if ($h['district']) { $v['holdingDistrict'] = [$h['district']]; $v['seatLabel'] = $h['seatLabel']; }
        $e->setFieldValues($v);
        if (!$el->saveElement($e)) { throw new \RuntimeException("$h[key]: " . json_encode($e->getFirstErrors())); }
        $made++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
if ($ssAdd) { $ss['records'][$ssKey][] = $D['sourceSearch'][$ssKey]; /* the file is Python's json.dump(indent=1, ensure_ascii=False): one space per level, slashes unescaped */
    $js = preg_replace_callback('/^( +)/m', fn($m) => str_repeat(' ', intdiv(strlen($m[1]), 4)), json_encode($ss, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    file_put_contents($ssPath, $js . "\n"); }
$r = $get($P);
$ok = trim((string)$r->body) === $new && count($filled($r->footnotes)) === count($D['footnotes'])
    && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $P, 'field' => 'holdingPerson'])->count() === count($have) + $made;
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_gladbach_profile_2026_10_06.php', $made + ($set ? 1 : 0), $ok ? 'verified' : 'SHORT', 'Jerry Gladbach: editorial profile, campaign biography attributed; two earlier Castaic Lake terms');
if (!$ok) { throw new \RuntimeException('build_gladbach_profile: read-back failed'); }
