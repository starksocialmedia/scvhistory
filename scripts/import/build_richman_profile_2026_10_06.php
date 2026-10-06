/**
 * Keith Richman #29316: the editorial profile (Nathan, 6 October 2026: "Keith Richman: build his profile. He held the
 * 38th Assembly District 2000 to 2006 ... Find the primary sources"). Reads
 * inventory/review/keith-richman-profile-draft-2026-10-06.json, made by scripts/import/draft_richman_profile_2026_10_06.py,
 * where every quotation is checked against its saved source.
 *
 * WHAT IT WRITES
 *  - #29316 Keith Richman: body (leads with his representation of the valley: the 38th Assembly District, 16.3 per cent
 *    of the valley under the 1991 lines from December 2000, 87.1 per cent under the 2001 lines from December 2002, by
 *    templates/_data/valley-districts.json), footnotes (12, editorial-2026; the record has none now), bodyAuthorship
 *    editorial-2026; and where empty: birthDate, birthDateEdtf, birthEvidence (retrospective: his obituaries, not a
 *    certificate), birthplace, deathDate, deathDateEdtf, deathEvidence (contemporary: KHTS 3 August 2010, Daily News
 *    31 July 2010, Los Angeles Times 1 August 2010), occupation, wikidataId Q6384943 (its English Wikipedia sitelink and
 *    its dates match), personWikipediaUrl. Adds the aliases Keith Stuart Richman (the Statements of Vote), Keith S.
 *    Richman (the 2006 Statement of Vote, the Signal) and Dr. Keith Richman (the KHTS obituary, the City's 2007 book), and
 *    relatedPersons Cameron Smyth #16380 (his successor in the 38th; the KHTS obituary names him so).
 *  - Office holding #29342: one note on who succeeded him, appended if not there.
 *  - NOT his portrait (set 5 October 2026 from the obituary page, asset #31239), not his holdings' dates (they stand,
 *    from the Statements of Vote and the Record of Members).
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/keith-richman-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_richman_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/keith-richman-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$RICHMAN = 29316; $SMYTH = 16380; $ASM = 18313; $H1 = 29497; $H2 = 29342; $SMYTHH = 29344;
$bad = []; $out = [];
foreach ([$RICHMAN => 'Keith Richman', $SMYTH => 'Cameron Smyth', $ASM => 'State Assemblymember'] as $id => $t) {
    if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; }
}
foreach ([$H1 => $RICHMAN, $H2 => $RICHMAN, $SMYTHH => $SMYTH] as $hid => $pid) {
    $h = $get($hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $pid || (string)$h->seatLabel !== '38th Assembly District') { $bad[] = "holding #$hid is not #$pid's 38th District term"; }
}
$p = $get($RICHMAN);
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);

/* 1. Keith Richman */
$pl = $lay($p); $set = []; $kept = [];
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = '#29316 has a different body already'; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if ($oldNotes && $cur === '') { $bad[] = '#29316 has footnotes but no body: read them before replacing'; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$F = $D['fields'];
foreach (['bodyAuthorship', 'birthDate', 'birthDateEdtf', 'birthEvidence', 'birthplace', 'deathDate', 'deathDateEdtf', 'deathEvidence', 'occupation', 'wikidataId'] as $h) {
    if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; } else { $kept[$h] = (string)(is_object($p->$h) && property_exists($p->$h, 'value') ? $p->$h->value : $p->$h); }
}
if ($isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
$aliases = array_values(array_filter(array_map('trim', explode("\n", (string)$p->personAliases))));
$addA = array_values(array_diff($F['aliasesAdd'], $aliases)); if ($addA) { $set['personAliases'] = implode("\n", array_merge($aliases, $addA)); }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids()); $addR = array_values(array_diff($F['rolesAdd'], $roles)); if ($addR) { $set['roles'] = array_merge($roles, $addR); }
if (!in_array($ASM, $roles, true)) { $bad[] = '#29316 lacks the State Assemblymember role'; }
$rel = array_map('intval', $p->relatedPersons->status(null)->ids()); $addP = array_values(array_diff($F['relatedPersonsAdd'], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . '; build_richman_profile_2026_10_06.php, 6 October 2026: editorial profile, birth and death, authority ids, aliases, Cameron Smyth related'); }

/* 2. the succession note on his second holding */
$hset = [];
foreach ($D['holdingNotes'] as $hid => $note) {
    $h = $get((int)$hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $RICHMAN) { $bad[] = "holding #$hid is not Keith Richman's"; continue; }
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($hr, fn($r) => str_contains((string)$r['note'], 'Succeeded') && str_contains((string)$r['note'], 'Smyth'))) { continue; }
    $hset[(int)$hid] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $note, 'source' => 'editorial-2026']]);
}
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . implode('', $D['holdingNotes']))) { $bad[] = 'an em dash in our own text'; }
/* every [n] in the body has a note, and every note is cited */
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_unique(array_map('intval', $m[1])); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# Keith Richman #29316: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/keith-richman-profile-draft-2026-10-06.md.\n";
$out[] = "## #29316 Keith Richman\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) : 'none (the record has no notes)') . "\n- featuredImage: not touched (asset #31239, from the obituary page)\n";
$out[] = "## Office holdings\n\n" . ($hset ? implode("\n", array_map(fn($id, $r) => "- #$id: add note " . count($r) . ': ' . end($r)['note'], array_keys($hset), $hset)) : '- nothing to add') . "\n- #$H1 and #$H2: dates not touched\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/keith-richman-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo '#29316: ' . count($set) . ' fields; holdings: ' . count($hset) . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/keith-richman-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($RICHMAN); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException('#29316: ' . json_encode($e->getFirstErrors())); } }
    foreach ($hset as $id => $r) { $e = $get($id); $e->setFieldValues(['footnotes' => $r]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($RICHMAN);
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && in_array($SMYTH, array_map('intval', $r->relatedPersons->status(null)->ids()), true) && (string)$r->deathDateEdtf === '2010-07-30';
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_richman_profile_2026_10_06.php', count($hset) + ($set ? 1 : 0), $ok ? 'verified' : 'SHORT', 'Keith Richman: editorial profile, birth and death, Cameron Smyth related; succession note on holding #29342');
if (!$ok) { throw new \RuntimeException('build_richman_profile: read-back failed'); }
