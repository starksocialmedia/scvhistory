/**
 * Kathryn Barger #29288: the editorial profile (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
 * profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Reads
 * inventory/review/kathryn-barger-profile-draft-2026-10-06.json, made by scripts/import/draft_barger_profile_2026_10_06.py,
 * where every quotation is checked against its saved source (copies in inventory/sources/kathryn-barger-2026-10-06/, with
 * a manifest of their sha256) and every vote figure against the County's Statements of Votes Cast.
 *
 * WHAT IT WRITES
 *  - #29288 Kathryn Barger: body (leads with her seat for the valley: supervisor for the 5th District since 2016, the
 *    elections of 2016, 2020 and 2024 from the Registrar-Recorder's statements, the City of Santa Clarita's count of 2016),
 *    footnotes (10, editorial-2026; the record has none now), bodyAuthorship editorial-2026; and where empty: occupation,
 *    wikidataId Q28086178, personWikipediaUrl (finding aids only). relatedPersons gains Michael D. Antonovich #29284 (her
 *    predecessor, LW3109).
 *  - Office holding #29290: one note with the election figures and the succession, appended if not there.
 *  - NOT her portrait (asset #31241, from LW3109), NOT birth fields (living), not the holding's dates.
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/kathryn-barger-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_barger_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/kathryn-barger-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$BARGER = 29288; $ANTON = 29284; $SUPV = 18443; $HOLD = 29290; $ANTONH = 29286;
$SCRIPT = 'build_barger_profile_2026_10_06.php';
$bad = []; $out = [];
foreach ([$BARGER => 'Kathryn Barger', $ANTON => 'Michael D. Antonovich', $SUPV => 'Supervisor'] as $id => $t) {
    if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; }
}
foreach ([$HOLD => $BARGER, $ANTONH => $ANTON] as $hid => $pid) {
    $h = $get($hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $pid || (string)$h->seatLabel !== '5th District') { $bad[] = "holding #$hid is not #$pid's 5th District term"; }
}
$p = $get($BARGER);
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);
$fields = Craft::$app->getFields();

/* 1. Kathryn Barger */
$pl = $lay($p); $set = []; $kept = [];
foreach (['body', 'footnotes', 'bodyAuthorship', 'occupation', 'wikidataId', 'personWikipediaUrl', 'relatedPersons', 'recordProvenance'] as $h) { if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; } }
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$BARGER has a different body already"; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if ($oldNotes && $cur === '') { $bad[] = "#$BARGER has footnotes but no body: read them before replacing"; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$F = $D['fields'];
foreach (['bodyAuthorship', 'occupation', 'wikidataId'] as $h) {
    if (!in_array($h, $pl, true)) { continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; } else { $kept[$h] = (string)(is_object($p->$h) && property_exists($p->$h, 'value') ? $p->$h->value : $p->$h); }
}
if (in_array('personWikipediaUrl', $pl, true) && $isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
/* living: no birth or death fields; report any already there rather than touch them */
foreach (['birthDate', 'birthDateEdtf', 'birthplace', 'deathDate', 'deathDateEdtf'] as $h) { if (in_array($h, $pl, true) && !$isEmpty($p, $h)) { $kept[$h] = (string)$p->$h; } }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids());
if (!in_array($SUPV, $roles, true)) { $bad[] = "#$BARGER lacks the Supervisor role"; }
$rel = array_map('intval', $p->relatedPersons->status(null)->ids()); $addP = array_values(array_diff($F['relatedPersonsAdd'], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . "; $SCRIPT, 6 October 2026: editorial profile, Wikidata id, Antonovich related"); }

/* 2. the note on her holding */
$hset = [];
foreach ($D['holdingNotes'] as $hid => $note) {
    $h = $get((int)$hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $BARGER) { $bad[] = "holding #$hid is not Kathryn Barger's"; continue; }
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($hr, fn($r) => str_contains((string)$r['note'], 'Elected on 8 November 2016'))) { continue; }
    $hset[(int)$hid] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $note, 'source' => 'editorial-2026']]);
}
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . implode('', $D['holdingNotes']))) { $bad[] = 'an em dash in our own text'; }
if (preg_match('~\.\.\.|\x{2026}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')))) { $bad[] = 'an ellipsis in our own text'; }
/* every [n] in the body has a note, and every note is cited */
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_unique(array_map('intval', $m[1])); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }
/* PlainText limits */
foreach ($set as $h => $v) {
    $f = $fields->getFieldByHandle($h);
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && is_string($v) && mb_strlen($v) > $f->charLimit) { $bad[] = "$h is " . mb_strlen($v) . " chars, over its limit of {$f->charLimit}"; }
}

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# Kathryn Barger #$BARGER: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/kathryn-barger-profile-draft-2026-10-06.md. She is living: public life only.\n";
$out[] = "## #$BARGER Kathryn Barger\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) : 'none (the record has no notes)')
    . "\n- birth and death fields: not set (living)\n- featuredImage: not touched (" . ($p->featuredImage->status(null)->exists() ? 'has an image' : 'empty') . ")\n- recordProvenance: " . (isset($set['recordProvenance']) ? mb_strlen($set['recordProvenance']) . ' of 255 chars' : 'unchanged') . "\n";
$out[] = "## Office holdings\n\n" . ($hset ? implode("\n", array_map(fn($id, $r) => "- #$id: add note " . count($r) . ': ' . end($r)['note'], array_keys($hset), $hset)) : '- nothing to add') . "\n- #$HOLD: dates not touched\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/kathryn-barger-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo "#$BARGER: " . count($set) . ' fields; holdings: ' . count($hset) . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/kathryn-barger-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($BARGER); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException("#$BARGER: " . json_encode($e->getFirstErrors())); } }
    foreach ($hset as $id => $r) { $e = $get($id); $e->setFieldValues(['footnotes' => $r]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($BARGER);
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && !array_diff($F['relatedPersonsAdd'], array_map('intval', $r->relatedPersons->status(null)->ids())) && (string)$r->wikidataId === $F['wikidataId'];
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog($SCRIPT, count($hset) + ($set ? 1 : 0), $ok ? 'verified' : 'SHORT', 'Kathryn Barger: editorial profile, Wikidata id, Antonovich related; election note on holding #29290');
if (!$ok) { throw new \RuntimeException("$SCRIPT: read-back failed"); }
