/**
 * Audra Strickland #29450: the editorial profile (Nathan, 6 October 2026: "Audra Strickland ... She held the 37th
 * Assembly District from 2004 to 2010 ... She succeeded Tony Strickland in that seat and they were married, which is a
 * public fact about how the seat passed and belongs in the record ... Primary sources for the terms. Living person:
 * public life only"). Reads inventory/review/audra-strickland-profile-draft-2026-10-06.json, made by
 * scripts/import/draft_audra_strickland_profile_2026_10_06.py, where every quotation is checked against its saved source
 * (copies in inventory/sources/audra-strickland-2026-10-06/, with a manifest of their sha256).
 *
 * WHAT IT WRITES
 *  - #29450 Audra Strickland: body (leads with her representation of the valley: the 37th Assembly District under the
 *    2001 lines, 12.9 per cent of the valley by templates/_data/valley-districts.json), footnotes (13, editorial-2026;
 *    the record has none now), bodyAuthorship editorial-2026; and where empty: occupation, wikidataId Q4820075 (its
 *    English Wikipedia sitelink is Audra_Strickland), personWikipediaUrl (a finding aid only). relatedPersons gains
 *    Tony Strickland #29448 (her predecessor in the 37th) and Jeff Gorell #29452 (her successor).
 *  - Office holding #29501: one note on whom she succeeded and who succeeded her, appended if not there.
 *  - The marriage is stated in the text with its sources (Roll Call 2003, The Acorn 2009, VC Reporter 2010). NO spouseOf
 *    relation: the loader never sets one and refuses if the draft asks for one. NOT her portrait (Audra_Strickland.jpg
 *    stays in inventory/incoming), NOT birth fields (living), not the holdings' dates.
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/audra-strickland-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_audra_strickland_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/audra-strickland-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$AUDRA = 29450; $TONY = 29448; $GORELL = 29452; $ASM = 18313; $HOLD = 29501; $TONYH = 29499; $TONYS = 29517; $GORELLH = 29503;
$bad = []; $out = [];
foreach ([$AUDRA => 'Audra Strickland', $TONY => 'Tony Strickland', $GORELL => 'Jeff Gorell', $ASM => 'State Assemblymember'] as $id => $t) {
    if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; }
}
foreach ([$HOLD => [$AUDRA, '37th Assembly District'], $TONYH => [$TONY, '37th Assembly District'], $TONYS => [$TONY, '19th Senate District'], $GORELLH => [$GORELL, '37th Assembly District']] as $hid => [$pid, $seat]) {
    $h = $get($hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $pid || (string)$h->seatLabel !== $seat) { $bad[] = "holding #$hid is not #$pid's $seat term"; }
}
if (array_key_exists('spouseOf', $D['fields']) || array_key_exists('spouseOfAdd', $D['fields'])) { $bad[] = 'the draft asks for a spouse relation: none is made for living people'; }
$p = $get($AUDRA);
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);

/* 1. Audra Strickland */
$pl = $lay($p); $set = []; $kept = [];
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = '#29450 has a different body already'; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if ($oldNotes && $cur === '') { $bad[] = '#29450 has footnotes but no body: read them before replacing'; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$F = $D['fields'];
foreach (['bodyAuthorship', 'occupation', 'wikidataId'] as $h) {
    if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; } else { $kept[$h] = (string)(is_object($p->$h) && property_exists($p->$h, 'value') ? $p->$h->value : $p->$h); }
}
if ($isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
/* living: no birth or death fields; report any already there rather than touch them */
foreach (['birthDate', 'birthDateEdtf', 'birthplace', 'deathDate', 'deathDateEdtf'] as $h) { if (!$isEmpty($p, $h)) { $kept[$h] = (string)$p->$h; } }
$spouses = $p->spouseOf->status(null)->ids();
if ($spouses) { $bad[] = '#29450 has spouseOf ' . implode(',', $spouses) . ': read it before applying (it would not publish, but the brief is no spouse relation)'; }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids());
if (!in_array($ASM, $roles, true)) { $bad[] = '#29450 lacks the State Assemblymember role'; }
$rel = array_map('intval', $p->relatedPersons->status(null)->ids()); $addP = array_values(array_diff($F['relatedPersonsAdd'], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . '; build_audra_strickland_profile_2026_10_06.php, 6 October 2026: editorial profile, Wikidata id, Tony Strickland (predecessor) and Jeff Gorell (successor) related'); }

/* 2. the succession note on her holding */
$hset = [];
foreach ($D['holdingNotes'] as $hid => $note) {
    $h = $get((int)$hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $AUDRA) { $bad[] = "holding #$hid is not Audra Strickland's"; continue; }
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($hr, fn($r) => str_contains((string)$r['note'], 'Succeeded her husband at the time'))) { continue; }
    $hset[(int)$hid] = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr), [['number' => (string)(count($hr) + 1), 'note' => $note, 'source' => 'editorial-2026']]);
}
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')) . implode('', $D['holdingNotes']))) { $bad[] = 'an em dash in our own text'; }
/* every [n] in the body has a note, and every note is cited */
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_unique(array_map('intval', $m[1])); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# Audra Strickland #29450: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/audra-strickland-profile-draft-2026-10-06.md. She is living: public life only.\n";
$out[] = "## #29450 Audra Strickland\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) : 'none (the record has no notes)')
    . "\n- spouseOf: " . ($spouses ? implode(', ', $spouses) : 'empty, and not set (the marriage is stated in the text, notes 3, 5, 6 and 7)')
    . "\n- birth and death fields: not set (living)\n- featuredImage: not touched (" . ($p->featuredImage->status(null)->exists() ? 'has an image' : 'empty; Audra_Strickland.jpg stays in inventory/incoming') . ")\n";
$out[] = "## Office holdings\n\n" . ($hset ? implode("\n", array_map(fn($id, $r) => "- #$id: add note " . count($r) . ': ' . end($r)['note'], array_keys($hset), $hset)) : '- nothing to add') . "\n- #$HOLD, #$TONYH, #$TONYS, #$GORELLH: dates not touched\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/audra-strickland-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo '#29450: ' . count($set) . ' fields; holdings: ' . count($hset) . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/audra-strickland-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($AUDRA); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException('#29450: ' . json_encode($e->getFirstErrors())); } }
    foreach ($hset as $id => $r) { $e = $get($id); $e->setFieldValues(['footnotes' => $r]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($AUDRA);
$relNow = array_map('intval', $r->relatedPersons->status(null)->ids());
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && !array_diff($F['relatedPersonsAdd'], $relNow) && !$r->spouseOf->status(null)->exists() && (string)$r->wikidataId === $F['wikidataId'];
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_audra_strickland_profile_2026_10_06.php', count($hset) + ($set ? 1 : 0), $ok ? 'verified' : 'SHORT', 'Audra Strickland: editorial profile, Wikidata id, Tony Strickland and Jeff Gorell related; succession note on holding #29501');
if (!$ok) { throw new \RuntimeException('build_audra_strickland_profile: read-back failed'); }
