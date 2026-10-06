/**
 * George Whitesides #29336: the editorial profile (Nathan, 6 October 2026, on the Wikipedia census: "Import the 12 and
 * build profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone").
 * Reads inventory/review/george-whitesides-profile-draft-2026-10-06.json, made by
 * scripts/import/draft_george_whitesides_profile_2026_10_06.py, where every quotation is checked against its saved
 * source (copies in inventory/sources/george-whitesides-2026-10-06/, with a manifest of their sha256) and every vote
 * figure against the saved Statement of Vote.
 *
 * WHAT IT WRITES
 *  - #29336 George Whitesides: body (leads with his seat for the whole valley, the 27th Congressional District under
 *    the 2021 lines, and its 88 per cent under the 2025 lines from January 2027), footnotes (editorial-2026; the
 *    record has none now), bodyAuthorship editorial-2026; and where empty: occupation, wikidataId Q3511804,
 *    personSearchNames (George T. Whitesides), personWikipediaUrl (a finding aid only). relatedPersons gains Mike
 *    Garcia #29334 (the incumbent he defeated in 2024).
 *  - No office-holding note: his holding #29384 is open, and Garcia's #29382 already names him.
 *  - NOT his portrait (Part A's script), NOT birth fields (living), not the holding's dates.
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/george-whitesides-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_george_whitesides_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$SCRIPT = 'build_george_whitesides_profile_2026_10_06.php'; $SLUG = 'george-whitesides';
$PID = 29336; $PTITLE = 'George Whitesides'; $ROLE = 18409;
/* records the draft names, with the title each must carry */
$EXPECT = [29336 => 'George Whitesides', 29334 => 'Mike Garcia', 18409 => 'Congressman'];
/* office holdings: id => [person, seatLabel] */
$HOLDINGS = [29384 => [29336, '27th Congressional District'], 29382 => [29334, '27th Congressional District']];
$HOLDMARK = '(none)';
$PROV = 'build_george_whitesides_profile_2026_10_06.php, 6 October 2026: editorial profile, Wikidata id, search name, Mike Garcia related';
$LOG = 'George Whitesides: editorial profile, Wikidata id, search name, Mike Garcia related';

$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fields = Craft::$app->getFields();
$D = json_decode(file_get_contents("$root/inventory/review/$SLUG-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$bad = []; $out = [];
foreach ($EXPECT as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }
foreach ($HOLDINGS as $hid => [$pid, $seat]) {
    $h = $get($hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $pid || (string)$h->seatLabel !== $seat) { $bad[] = "holding #$hid is not #$pid's $seat term"; }
}
if ((int)$D['person']['id'] !== $PID) { $bad[] = 'the draft is for another person'; }
$p = $get($PID);
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);
/* PlainText limits, checked on every plain value set below */
$limit = function ($handle, $value) use ($fields, &$bad) {
    $f = $fields->getFieldByHandle($handle);
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen((string)$value) > $f->charLimit) { $bad[] = "$handle is " . mb_strlen((string)$value) . " chars, over its limit of {$f->charLimit}"; }
};

/* 1. the person */
$pl = $lay($p); $set = []; $kept = [];
foreach (['body', 'footnotes', 'bodyAuthorship', 'occupation', 'wikidataId', 'personWikipediaUrl', 'relatedPersons', 'recordProvenance', 'roles'] as $h) { if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; } }
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$PID has a different body already"; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if ($oldNotes && $cur === '') { $bad[] = "#$PID has footnotes but no body: read them before replacing"; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$F = $D['fields'];
foreach (['bodyAuthorship', 'occupation', 'wikidataId', 'personSearchNames'] as $h) {
    if (!array_key_exists($h, $F)) { continue; }
    if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; $limit($h, $F[$h]); } else { $kept[$h] = (string)(is_object($p->$h) && property_exists($p->$h, 'value') ? $p->$h->value : $p->$h); }
}
if (in_array('personWikipediaUrl', $pl, true) && $isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
foreach (['birthDate', 'birthDateEdtf', 'birthplace', 'deathDate', 'deathDateEdtf'] as $h) { if (in_array($h, $pl, true) && !$isEmpty($p, $h)) { $kept[$h] = (string)$p->$h; } }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids());
if (!in_array($ROLE, $roles, true)) { $bad[] = "#$PID lacks role #$ROLE"; }
$rel = array_map('intval', $p->relatedPersons->status(null)->ids()); $addP = array_values(array_diff($F['relatedPersonsAdd'], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) {
    $prov = trim((string)$p->recordProvenance); $prov = $prov === '' ? $PROV : "$prov; $PROV";
    $set['recordProvenance'] = $prov; $limit('recordProvenance', $prov);
    if (mb_strlen($prov) > 255) { $bad[] = 'recordProvenance is ' . mb_strlen($prov) . ' chars, over 255'; }
}

/* 2. notes on the office holdings */
$hset = [];
foreach ($D['holdingNotes'] as $hid => $note) {
    $h = $get((int)$hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $PID) { $bad[] = "holding #$hid is not #$PID's"; continue; }
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($hr, fn($r) => str_contains((string)$r['note'], $HOLDMARK))) { continue; }
    $hset[(int)$hid] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $note, 'source' => 'editorial-2026']]);
}
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . implode('', $D['holdingNotes']))) { $bad[] = 'an em dash in our own text'; }
if (preg_match('~\.\.\.|\x{2026}~u', $D['body'])) { $bad[] = 'an ellipsis in the body'; }
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_unique(array_map('intval', $m[1])); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : str_replace("\n", ' | ', $v));
$out[] = "# $PTITLE #$PID: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/$SLUG-profile-draft-2026-10-06.md. Living: public life only.\n";
$out[] = "## #$PID $PTITLE\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) : 'none (the record has no notes)')
    . "\n- birth and death fields: not set (living)\n- featuredImage: not touched (" . ($p->featuredImage->status(null)->exists() ? 'has an image' : 'empty') . ")\n";
$out[] = "## Office holdings\n\n" . ($hset ? implode("\n", array_map(fn($id, $r) => "- #$id: add note " . count($r) . ': ' . end($r)['note'], array_keys($hset), $hset)) : '- nothing to add') . "\n- dates not touched\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/$SLUG-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo "#$PID: " . count($set) . ' fields; holdings: ' . count($hset) . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . "printed to inventory/review/$SLUG-profile-dry-run-2026-10-06.md" . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$set && !$hset) { echo 'nothing to do: already applied' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($PID); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException("#$PID: " . json_encode($e->getFirstErrors())); } }
    foreach ($hset as $id => $r) { $e = $get($id); $e->setFieldValues(['footnotes' => $r]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($PID);
$relNow = array_map('intval', $r->relatedPersons->status(null)->ids());
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && !array_diff($F['relatedPersonsAdd'], $relNow) && (string)$r->wikidataId !== '';
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog($SCRIPT, count($hset) + ($set ? 1 : 0), $ok ? 'verified' : 'SHORT', $LOG);
if (!$ok) { throw new \RuntimeException("$SCRIPT: read-back failed"); }
