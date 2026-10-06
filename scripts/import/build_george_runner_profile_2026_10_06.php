/**
 * George Runner #18747: the editorial profile (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
 * profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Reads
 * inventory/review/george-runner-profile-draft-2026-10-06.json, made by scripts/import/draft_george_runner_profile_2026_10_06.py,
 * where every quotation is checked against its saved source (copies in inventory/sources/george-runner-2026-10-06/, with a
 * manifest of their sha256; the archive's own pages read from the Reggie mirror).
 *
 * WHAT IT WRITES
 *  - #18747 George Runner: body (leads with his twelve years for the valley: the 36th Assembly District under the 1991 lines,
 *    83.7 per cent of the valley, and the 17th Senate District under the 2001 lines, 74.2 per cent, by
 *    templates/_data/valley-districts.json), footnotes (16, editorial-2026; the record has none now), bodyAuthorship
 *    editorial-2026; and where empty: occupation, wikidataId Q5544109 (its English Wikipedia sitelink is George_Runner),
 *    personWikipediaUrl (a finding aid only). Aliases George C. Runner and George Runner Jr. (the Statements of Vote).
 *    relatedPersons gains Pete Knight #29314 (his predecessor in both seats) and Sharon Runner #29324 (his successor in the 17th).
 *  - Office holding #29359: one note on his successor, appended if not there.
 *  - NO spouseOf relation (the marriage is stated in the text from the Signal's interview), NOT birth fields (living),
 *    NOT featuredImage, not the holdings' dates. Nothing rests on Wikipedia alone; what Wikipedia alone says is listed in the
 *    draft's .md and left out.
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/george-runner-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_george_runner_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$SCRIPT = 'build_george_runner_profile_2026_10_06.php'; $SLUG = 'george-runner';
$PID = 18747; $PTITLE = 'George Runner';
$CHECKS = [29314 => 'Pete Knight', 29324 => 'Sharon Runner', 18313 => 'State Assemblymember', 18387 => 'State Senator'];
$HOLDS = [29340 => [18747, '36th Assembly District'], 29359 => [18747, '17th Senate District'], 29361 => [29324, '17th Senate District']];
$ROLES = [18313, 18387];
$NOTE_MARK = 'Succeeded in the 17th District by Sharon Runner';
$PROV = 'build_george_runner_profile_2026_10_06.php, 6 October 2026: editorial profile, Wikidata id, aliases, Pete Knight and Sharon Runner related';

$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/$SLUG-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$bad = []; $out = [];
if ($get($PID)?->title !== $PTITLE) { throw new \RuntimeException("#$PID is not $PTITLE"); }
foreach ($CHECKS as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }
foreach ($HOLDS as $hid => [$pid, $seat]) {
    $h = $get($hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $pid || (string)$h->seatLabel !== $seat) { $bad[] = "holding #$hid is not #$pid's $seat term"; }
}
if (array_key_exists('spouseOf', $D['fields']) || array_key_exists('spouseOfAdd', $D['fields'])) { $bad[] = 'the draft asks for a spouse relation: none is made for living people'; }
$p = $get($PID);
$fields = []; foreach ($p->getFieldLayout()->getCustomFields() as $f) { $fields[$f->handle] = $f; }
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);

/* 1. the person */
$set = []; $kept = [];
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$PID has a different body already"; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if ($oldNotes && $cur === '') { $bad[] = "#$PID has footnotes but no body: read them before replacing"; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$F = $D['fields'];
foreach (['bodyAuthorship', 'occupation', 'wikidataId'] as $h) {
    if (!isset($fields[$h])) { $bad[] = "$h not on the person layout"; continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; } else { $kept[$h] = (string)(is_object($p->$h) && property_exists($p->$h, 'value') ? $p->$h->value : $p->$h); }
}
foreach (['body', 'footnotes', 'personAliases', 'relatedPersons', 'personWikipediaUrl', 'recordProvenance', 'spouseOf', 'roles'] as $h) { if (!isset($fields[$h])) { $bad[] = "$h not on the person layout"; } }
if ($isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
$aliases = array_values(array_filter(array_map('trim', explode("\n", (string)$p->personAliases))));
$addA = array_values(array_diff($F['aliasesAdd'] ?? [], $aliases)); if ($addA) { $set['personAliases'] = implode("\n", array_merge($aliases, $addA)); }
/* living: no birth or death fields; report any already there rather than touch them */
foreach (['birthDate', 'birthDateEdtf', 'birthplace', 'deathDate', 'deathDateEdtf'] as $h) { if (!$isEmpty($p, $h)) { $kept[$h] = (string)$p->$h; } }
$spouses = $p->spouseOf->status(null)->ids();
if ($spouses) { $bad[] = "#$PID has spouseOf " . implode(',', $spouses) . ': read it before applying'; }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids());
$rolesNote = array_diff($ROLES, $roles) ? 'lacks ' . implode(', ', array_map(fn($r) => '#' . $r . ' ' . $get($r)?->title, array_diff($ROLES, $roles))) . ' (not set here; report only)' : 'has them';
$rel = array_map('intval', $p->relatedPersons->status(null)->ids()); $addP = array_values(array_diff($F['relatedPersonsAdd'], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) { $set['recordProvenance'] = trim(trim((string)$p->recordProvenance) . '; ' . $PROV, '; '); }
/* every PlainText value against its field's limit */
foreach ($set as $h => $v) {
    $f = $fields[$h] ?? null;
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && is_string($v) && mb_strlen($v) > $f->charLimit) { $bad[] = "$h is " . mb_strlen($v) . " chars, over its limit of {$f->charLimit}"; }
}

/* 2. the note on the holding */
$hset = [];
foreach ($D['holdingNotes'] as $hid => $note) {
    $h = $get((int)$hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $PID) { $bad[] = "holding #$hid is not $PTITLE's"; continue; }
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($hr, fn($r) => str_contains((string)$r['note'], $NOTE_MARK))) { continue; }
    $hset[(int)$hid] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $note, 'source' => 'editorial-2026']]);
}
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . implode('', $D['holdingNotes']))) { $bad[] = 'an em dash in our own text'; }
if (preg_match('~\.\.\.|\x{2026}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')))) { $bad[] = 'an ellipsis in our own text'; }
/* every [n] in the body has a note, and every note is cited */
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_values(array_unique(array_map('intval', $m[1]))); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : str_replace("\n", ' | ', $v));
$out[] = "# $PTITLE #$PID: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/$SLUG-profile-draft-2026-10-06.md. He is living: public life only.\n";
$out[] = "## #$PID $PTITLE\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) : 'none (the record has no notes)')
    . "\n- spouseOf: " . ($spouses ? implode(', ', $spouses) : 'empty, and not set (the marriage is stated in the text)')
    . "\n- roles: $rolesNote\n- birth and death fields: not set (living)\n- featuredImage: not touched (" . ($p->featuredImage->status(null)->exists() ? 'has an image' : 'empty') . ")\n";
$out[] = "## Office holdings\n\n" . ($hset ? implode("\n", array_map(fn($id, $r) => "- #$id: add note " . count($r) . ': ' . end($r)['note'], array_keys($hset), $hset)) : '- nothing to add') . "\n- dates not touched\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/$SLUG-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo "#$PID: " . count($set) . ' fields (' . implode(', ', array_keys($set)) . '); holdings: ' . count($hset) . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . "printed to inventory/review/$SLUG-profile-dry-run-2026-10-06.md" . PHP_EOL;
if (!$set && !$hset) { echo 'nothing to do: already applied' . PHP_EOL; return; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($PID); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException("#$PID: " . json_encode($e->getFirstErrors())); } }
    foreach ($hset as $id => $r) { $e = $get($id); $e->setFieldValues(['footnotes' => $r]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($PID);
$relNow = array_map('intval', $r->relatedPersons->status(null)->ids());
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && !array_diff($F['relatedPersonsAdd'], $relNow) && !$r->spouseOf->status(null)->exists() && (string)$r->wikidataId !== '';
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog($SCRIPT, count($hset) + ($set ? 1 : 0), $ok ? 'verified' : 'SHORT', "$PTITLE: editorial profile, Wikidata id, related persons; " . count($hset) . ' holding note(s)');
if (!$ok) { throw new \RuntimeException("$SCRIPT: read-back failed"); }
