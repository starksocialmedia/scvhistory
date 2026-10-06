/**
 * Pete Knight #29314: the editorial profile (Nathan, 6 October 2026: "write him a full profile ... Lead with the archive's
 * own source, not the biography"). Reads inventory/review/pete-knight-profile-draft-2026-10-06.json, made by
 * scripts/import/draft_knight_profile_2026_10_06.py, where every quotation is checked against its source.
 *
 * WHAT IT WRITES
 *  - #29314 Pete Knight: body (leads with his last interview, document #31412, imported by
 *    import_knight_newsmaker_2026_10_06.php), footnotes (the one existing note, Record of State Senators note 113, is
 *    replaced by a fuller note that keeps its quotation), bodyAuthorship editorial-2026; and where empty: birthDate,
 *    birthDateEdtf, birthEvidence (retrospective: the date is in his own official biography and his obituaries, not on a
 *    certificate the archive holds), birthplace, occupation, wikidataId Q974431 (its English Wikipedia sitelink and its
 *    dates match), personWikipediaUrl. Adds the aliases Wm. J. "Pete" Knight and Sen. Pete Knight, the role State
 *    Assemblymember #18313 beside State Senator, and relatedPersons George Runner #18747 (the Record of State Senators
 *    joins them: "Succeeded by George Runner").
 *  - #29328 Steve Knight: childOf #29314, and one footnote naming Pete Knight as his father, on Steve Knight's record
 *    because that is where the page's test looks (templates/_partials/record/historical.twig, publicKin(holder, other):
 *    the holder is the child on both pages). Both men hold office holdings, so the link publishes (DATA-MODEL, Kinship
 *    on the page). Added only if his footnotes do not already say it.
 *  - Office holdings #29338 and #29357: one note each on who succeeded him, appended if not there.
 *  - NOT his portrait (handled separately), not his office holdings' dates (they stand, from the Statements of Vote and
 *    the Records of Members).
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/pete-knight-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_knight_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/pete-knight-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$PETE = 29314; $STEVE = 29328; $RUNNER = 18747; $ASM = 18313; $SEN = 18387; $DOC = 31412;
$bad = []; $out = [];
foreach ([$PETE => 'Pete Knight', $STEVE => 'Steve Knight', $RUNNER => 'George Runner', $ASM => 'State Assemblymember', $SEN => 'State Senator'] as $id => $t) {
    if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; }
}
$doc = $get($DOC); if (!$doc || (string)$doc->legacyKey !== 'sg042504') { $bad[] = "#$DOC is not the Newsmaker interview"; }
$p = $get($PETE); $s = $get($STEVE);
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$hasRows = fn($rows) => (bool)array_filter((array)$rows, fn($r) => trim((string)($r['note'] ?? '')) !== '');
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);

/* 1. Pete Knight */
$pl = $lay($p); $set = [];
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = '#29314 has a different body already'; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
$F = $D['fields'];
foreach (['bodyAuthorship', 'birthDate', 'birthDateEdtf', 'birthEvidence', 'birthplace', 'occupation', 'wikidataId'] as $h) {
    if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; }
}
if ($isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
$aliases = array_values(array_filter(array_map('trim', explode("\n", (string)$p->personAliases))));
$addA = array_values(array_diff($F['aliasesAdd'], $aliases)); if ($addA) { $set['personAliases'] = implode("\n", array_merge($aliases, $addA)); }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids()); $addR = array_values(array_diff($F['rolesAdd'], $roles)); if ($addR) { $set['roles'] = array_merge($roles, $addR); }
$rel = array_map('intval', $p->relatedPersons->status(null)->ids()); $addP = array_values(array_diff($F['relatedPersonsAdd'], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . '; build_knight_profile_2026_10_06.php, 6 October 2026: editorial profile, birth, authority ids, aliases, Assembly role, George Runner related'); }

/* 2. Steve Knight: childOf and the footnote the page's test reads */
$sl = $lay($s); $sset = [];
foreach (['childOf', 'footnotes'] as $h) { if (!in_array($h, $sl, true)) { $bad[] = "$h not on Steve Knight's layout"; } }
$parents = array_map('intval', $s->childOf->status(null)->ids()); if (!in_array($PETE, $parents, true)) { $sset['childOf'] = array_merge($parents, [$PETE]); }
$sNotes = array_values(array_filter((array)$s->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
$kinNote = $D['kinship']['footnoteOnChild'];
$says = (bool)array_filter($sNotes, fn($r) => str_contains((string)$r['note'], 'Pete Knight') && preg_match('~\b(son|daughter|father|mother|brother|sister|child|parent)s?\b~i', (string)$r['note']));
if (!$says) { $sset['footnotes'] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $sNotes), [['number' => (string)(count($sNotes) + 1), 'note' => $kinNote, 'source' => 'editorial-2026']]); }
/* the test, as historical.twig runs it, on the record as it will stand */
$after = $says ? $sNotes : $sset['footnotes'];
$holds = fn($id) => Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $id, 'field' => 'holdingPerson'])->count();
$kinPass = $holds($PETE) > 0 && $holds($STEVE) > 0 && (bool)array_filter($after, fn($r) => preg_match('/Pete Knight/', (string)$r['note']) && preg_match('/\b(son|daughter|father|mother|brother|sister|child|parent)s?\b/i', (string)$r['note']));

/* 3. succession notes on his two holdings */
$hset = [];
foreach ($D['holdingNotes'] as $hid => $note) {
    $h = $get((int)$hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $PETE) { $bad[] = "holding #$hid is not Pete Knight's"; continue; }
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($hr, fn($r) => str_contains((string)$r['note'], 'Succeeded') && str_contains((string)$r['note'], 'Runner'))) { continue; }
    $hset[(int)$hid] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $note, 'source' => 'editorial-2026']]);
}
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . $kinNote . implode('', $D['holdingNotes']))) { $bad[] = 'an em dash in our own text'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# Pete Knight #29314: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/pete-knight-profile-draft-2026-10-06.md.\n";
$out[] = "## #29314 Pete Knight\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- the existing note, replaced: ' . ($oldNotes ? implode(' | ', array_map(fn($r) => $r['note'], $oldNotes)) : 'none') . " (its quotation is kept in the new note " . (array_search('LEGIS', array_column($D['footnotes'], 'key')) + 1) . ")\n- featuredImage: not touched\n";
$out[] = "## #29328 Steve Knight\n\n" . ($sset ? implode("\n", array_map(fn($h, $v) => "- $h: " . ($h === 'footnotes' ? 'add note ' . count($v) . ': ' . end($v)['note'] : $show($v)), array_keys($sset), $sset)) : '- nothing to change') . "\n";
$out[] = '- the kinship test (publicKin): Pete Knight holds ' . $holds($PETE) . ' office holdings, Steve Knight ' . $holds($STEVE) . '; a note on Steve Knight\'s record naming "Pete Knight" and the relation: ' . ($kinPass ? 'yes, so the link publishes on both pages' : 'NO, the link stays hidden') . "\n";
$out[] = "## Office holdings\n\n" . ($hset ? implode("\n", array_map(fn($id, $r) => "- #$id: add note " . count($r) . ': ' . end($r)['note'], array_keys($hset), $hset)) : '- nothing to add') . "\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/pete-knight-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo '#29314: ' . count($set) . ' fields; #29328: ' . count($sset) . ' fields; holdings: ' . count($hset) . '; kinship test ' . ($kinPass ? 'passes' : 'FAILS') . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/pete-knight-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($PETE); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException('#29314: ' . json_encode($e->getFirstErrors())); } }
    if ($sset) { $e = $get($STEVE); $e->setFieldValues($sset); if (!$el->saveElement($e)) { throw new \RuntimeException('#29328: ' . json_encode($e->getFirstErrors())); } }
    foreach ($hset as $id => $r) { $e = $get($id); $e->setFieldValues(['footnotes' => $r]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($PETE); $rs = $get($STEVE);
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && in_array($PETE, array_map('intval', $rs->childOf->status(null)->ids()), true);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_knight_profile_2026_10_06.php', count($hset) + ($set ? 1 : 0) + ($sset ? 1 : 0), $ok ? 'verified' : 'SHORT', 'Pete Knight: editorial profile; Steve Knight childOf with its note; succession notes on two holdings');
if (!$ok) { throw new \RuntimeException('build_knight_profile: read-back failed'); }
