/**
 * Henry Stern #29462: the editorial profile (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
 * profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Reads
 * inventory/review/henry-stern-profile-draft-2026-10-06.json, made by scripts/import/draft_henry_stern_profile_2026_10_06.py,
 * where every quotation is checked against its saved source (copies in inventory/sources/henry-stern-2026-10-06/, with a
 * manifest of their sha256; the mirror pages read in place). Nothing in the body rests on Wikipedia alone; what does is
 * listed in inventory/review/henry-stern-profile-draft-2026-10-06.md and left out. He is living: public life only.
 *
 * WHAT IT WRITES
 *  - #29462 Henry Stern: body (leads with his Senate term for the valley's west side: the 27th District under the 2011
 *    lines, 20.3 per cent of the valley by templates/_data/valley-districts.json), footnotes (7, editorial-2026; the
 *    record has none now), bodyAuthorship editorial-2026; and where empty: occupation, wikidataId Q27967376 and
 *    personWikipediaUrl (finding aids only). relatedPersons gains Fran Pavley #29460, his predecessor in the 27th.
 *  - No holding note (the succession note sits on Pavley's holding #29519, written by her loader).
 *  - NOT his portrait (the parent's Part A handles featuredImage), NOT birth fields (living), NOT the holding (#29521's
 *    howEnded "reelected" is reported for Nathan in the draft .md, not changed).
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/henry-stern-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_henry_stern_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$SCRIPT = 'build_henry_stern_profile_2026_10_06.php'; $SLUG = 'henry-stern';
$D = json_decode(file_get_contents("$root/inventory/review/$SLUG-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$PID = 29462; $NAME = 'Henry Stern'; $ROLE = 18387; $LIVING = true;
$CHECKS = [29462 => 'Henry Stern', 29460 => 'Fran Pavley', 18387 => 'State Senator'];
$HOLDS = [29519 => [29460, '27th Senate District'], 29521 => [29462, '27th Senate District']];
$NOTE_MARK = 'Succeeded in the 27th District by'; /* no holding note in this draft */
$PROV = "$SCRIPT, 6 October 2026: editorial profile, Wikidata id, Fran Pavley (predecessor) related";
$bad = []; $out = [];
foreach ($CHECKS as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }
foreach ($HOLDS as $hid => [$pid, $seat]) {
    $h = $get($hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $pid || (string)$h->seatLabel !== $seat) { $bad[] = "holding #$hid is not #$pid's $seat term"; }
}
if ((int)$D['person']['id'] !== $PID) { $bad[] = 'the draft is for another person'; }
if (array_key_exists('spouseOf', $D['fields']) || array_key_exists('spouseOfAdd', $D['fields'])) { $bad[] = 'the draft asks for a spouse relation: none is made here'; }
$p = $get($PID);
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);

/* 1. the person */
$pl = $lay($p); $set = []; $kept = [];
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$PID has a different body already"; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if ($oldNotes && $cur === '') { $bad[] = "#$PID has footnotes but no body: read them before replacing"; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$F = $D['fields'];
$plain = ['bodyAuthorship', 'occupation', 'wikidataId'];
if (!$LIVING) { $plain = array_merge($plain, ['birthDate', 'birthDateEdtf', 'birthEvidence', 'birthplace', 'deathDate', 'deathDateEdtf', 'deathEvidence']); }
foreach ($plain as $h) {
    if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; continue; }
    if (!isset($F[$h])) { $bad[] = "the draft has no $h"; continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; } else { $v = $p->getFieldValue($h); $kept[$h] = (string)(is_object($v) && property_exists($v, 'value') ? $v->value : $v); }
}
if ($LIVING) { foreach (['birthDate', 'birthDateEdtf', 'birthplace', 'deathDate', 'deathDateEdtf'] as $h) { if (!$isEmpty($p, $h)) { $kept[$h] = (string)$p->getFieldValue($h); } } }
if (!in_array('personWikipediaUrl', $pl, true)) { $bad[] = 'personWikipediaUrl not on the person layout'; }
elseif ($isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids());
if (!in_array($ROLE, $roles, true)) { $bad[] = "#$PID lacks the role #$ROLE"; }
$rel = array_map('intval', $p->relatedPersons->status(null)->ids()); $addP = array_values(array_diff($F['relatedPersonsAdd'], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . '; ' . $PROV); }

/* 2. the succession note on the holding */
$hset = [];
foreach ($D['holdingNotes'] as $hid => $note) {
    $h = $get((int)$hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $PID) { $bad[] = "holding #$hid is not $NAME's"; continue; }
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($hr, fn($r) => str_contains((string)$r['note'], $NOTE_MARK))) { continue; }
    $hset[(int)$hid] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $note, 'source' => 'editorial-2026']]);
}

/* 3. our own text, and every PlainText value against its field's limit */
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . implode('', $D['holdingNotes']))) { $bad[] = 'an em dash in our own text'; }
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_unique(array_map('intval', $m[1])); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }
$fs = Craft::$app->getFields();
foreach ($set as $h => $v) {
    $f = $fs->getFieldByHandle($h);
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && is_string($v) && mb_strlen($v) > $f->charLimit) { $bad[] = "$h is " . mb_strlen($v) . " chars, over its limit of {$f->charLimit}"; }
    if ($f instanceof \craft\fields\Dropdown && is_string($v) && !in_array($v, array_map(fn($o) => (string)$o['value'], $f->options), true)) { $bad[] = "$h: '$v' is not one of its options"; }
}

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# $NAME #$PID: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/$SLUG-profile-draft-2026-10-06.md, with what rests on Wikipedia alone (left out). " . ($LIVING ? 'Living: public life only.' : 'Dead.') . "\n";
$out[] = "## #$PID $NAME\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) : 'none (the record has no notes)')
    . "\n- featuredImage: not touched (" . ($p->featuredImage->status(null)->exists() ? 'has an image' : 'empty; Part A, the Commons portraits, handles it') . ")\n";
$out[] = "## Office holdings\n\n" . ($hset ? implode("\n", array_map(fn($id, $r) => "- #$id: add note " . count($r) . ': ' . end($r)['note'], array_keys($hset), $hset)) : '- nothing to add') . "\n- dates not touched\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/$SLUG-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo "#$PID: " . count($set) . ' fields; holdings: ' . count($hset) . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . "printed to inventory/review/$SLUG-profile-dry-run-2026-10-06.md" . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$set && !$hset) { echo 'nothing to do: done already' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($PID); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException("#$PID: " . json_encode($e->getFirstErrors())); } }
    foreach ($hset as $id => $r) { $e = $get($id); $e->setFieldValues(['footnotes' => $r]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($PID);
$relNow = array_map('intval', $r->relatedPersons->status(null)->ids());
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && !array_diff($F['relatedPersonsAdd'], $relNow) && (string)$r->wikidataId === $F['wikidataId'];
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog($SCRIPT, count($hset) + ($set ? 1 : 0), $ok ? 'verified' : 'SHORT', "$NAME: editorial profile, Wikidata id, Fran Pavley related");
if (!$ok) { throw new \RuntimeException("$SCRIPT: read-back failed"); }
