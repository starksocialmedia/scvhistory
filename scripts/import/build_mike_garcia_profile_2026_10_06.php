/**
 * Mike Garcia #29334: the editorial profile (Nathan, 6 October 2026, on the Wikipedia census: "Import the 12 and build
 * profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone"). Reads
 * inventory/review/mike-garcia-profile-draft-2026-10-06.json, made by scripts/import/draft_mike_garcia_profile_2026_10_06.py,
 * where every quotation is checked against its saved source (inventory/sources/mike-garcia-2026-10-06/, manifest.json there,
 * and inventory/sources/legislative-districts-2026-10-04/) and every vote count against its Statement of Vote.
 *
 * WHAT IT WRITES
 *  - #29334 Mike Garcia: body (leads with his representation of the whole valley, 25th and 27th districts, May 2020 to
 *    January 2025), footnotes (editorial-2026), bodyAuthorship editorial-2026; and where empty: occupation, wikidataId
 *    Q94236068 and personWikipediaUrl (finding aids from the census), bioguideId G000061. relatedPersons gains Katie Hill
 *    #29332 (predecessor) and George Whitesides #29336 (successor).
 *  - NOT his portrait (Part A of the census work is separate), NOT birth fields (living), NOT his office holdings.
 * Fills an empty body only; refuses a body that differs. Every PlainText value is checked against its field's charLimit.
 * Idempotent: a second run finds nothing to set and saves nothing. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/mike-garcia-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_mike_garcia_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

/* ---- this person ---- */
$SCRIPT = 'build_mike_garcia_profile_2026_10_06.php';
$SLUG = 'mike-garcia';
$PID = 29334; $NAME = 'Mike Garcia';
$MUST = [29332 => 'Katie Hill', 29336 => 'George Whitesides', 18409 => 'Congressman'];
$HOLDINGS = [29380 => '25th Congressional District', 29382 => '27th Congressional District'];
$ROLE = 18409;
$PROV = "$SCRIPT, 6 October 2026: editorial profile; ids; Hill and Whitesides related";
$SUMMARY = 'Mike Garcia: editorial profile, Wikidata and Bioguide ids, Katie Hill and George Whitesides related';
/* ---- end ---- */

$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fieldsSvc = Craft::$app->getFields();
$D = json_decode(file_get_contents("$root/inventory/review/$SLUG-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$bad = []; $out = [];
if ((int)$D['person']['id'] !== $PID) { $bad[] = 'the draft is for #' . $D['person']['id']; }
$p = $get($PID);
if ($p?->title !== $NAME) { throw new \RuntimeException("#$PID is not $NAME"); }
foreach ($MUST as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }
foreach ($HOLDINGS as $hid => $seat) {
    $h = $get($hid);
    if (!$h || $h->holdingPerson->status(null)->one()?->id !== $PID || (string)$h->seatLabel !== $seat) { $bad[] = "holding #$hid is not #$PID's $seat term"; }
}
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$shown = fn($e, $h) => (string)(is_object($e->$h) && property_exists($e->$h, 'value') ? $e->$h->value : $e->$h);
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);

$pl = $lay($p); $set = []; $kept = [];
foreach (['body', 'footnotes', 'bodyAuthorship', 'occupation', 'wikidataId', 'bioguideId', 'personWikipediaUrl', 'roles', 'relatedPersons', 'recordProvenance'] as $h) {
    if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; }
}
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$PID has a different body already"; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
$newNoteText = array_column($D['footnotes'], 'note');
foreach ($oldNotes as $r) {
    if ($cur === '' && !in_array(trim((string)$r['note']), array_map('trim', $newNoteText), true)) { $bad[] = "#$PID has a footnote the draft does not carry: " . mb_substr((string)$r['note'], 0, 80); }
}
foreach (($D['keepNotes'] ?? []) as $k) { if (!in_array($k, $newNoteText, true)) { $bad[] = 'a note to keep is not in the draft: ' . mb_substr($k, 0, 80); } }
$kin = in_array('childOf', $pl, true) ? array_map(fn($e) => "#{$e->id} {$e->title}", $p->childOf->status(null)->all()) : [];
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
$F = $D['fields'];
foreach (['bodyAuthorship', 'occupation', 'wikidataId', 'bioguideId'] as $h) {
    if (!isset($F[$h])) { continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; } else { $kept[$h] = $shown($p, $h); }
}
if ($isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; } else { $kept['personWikipediaUrl'] = (string)$p->personWikipediaUrl?->getUrl(); }
/* living: no birth or death fields; report any already there rather than touch them */
foreach (['birthDate', 'birthDateEdtf', 'birthplace', 'deathDate', 'deathDateEdtf'] as $h) { if (in_array($h, $pl, true) && !$isEmpty($p, $h)) { $kept[$h] = (string)$p->$h; } }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$roles = array_map('intval', $p->roles->status(null)->ids());
if (!in_array($ROLE, $roles, true)) { $bad[] = "#$PID lacks role #$ROLE"; }
$addR = array_values(array_diff($F['rolesAdd'] ?? [], $roles)); if ($addR) { $set['roles'] = array_merge($roles, $addR); }
$rel = array_map('intval', $p->relatedPersons->status(null)->ids());
$addP = array_values(array_diff($F['relatedPersonsAdd'] ?? [], $rel)); if ($addP) { $set['relatedPersons'] = array_merge($rel, $addP); }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . '; ' . $PROV); }

/* every PlainText value against its field's limit */
foreach ($set as $h => $v) {
    $f = $fieldsSvc->getFieldByHandle($h);
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && is_string($v) && mb_strlen($v) > $f->charLimit) { $bad[] = "$h is " . mb_strlen($v) . " characters, over its limit of {$f->charLimit}"; }
}
if (preg_match('~\x{2014}~u', $D['body'] . implode('', $newNoteText))) { $bad[] = 'an em dash in our own text'; }
if (preg_match('~\.\.\.|\x{2026}~u', $D['body'] . implode('', $newNoteText))) { $bad[] = 'an ellipsis in our own text'; }
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_unique(array_map('intval', $m[1])); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# $NAME #$PID: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note, with what rests on Wikipedia alone, are in inventory/review/$SLUG-profile-draft-2026-10-06.md. Living: public life only.\n";
$out[] = "## #$PID $NAME\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) . ' (each carried verbatim into the new notes)' : 'none (the record has no notes)')
    . "\n- childOf: " . ($kin ? implode(', ', $kin) . ', not touched' : 'empty, not touched') . "\n- birth and death fields: not set (living)\n- featuredImage: not touched (" . ($p->featuredImage->status(null)->exists() ? 'has an image' : 'empty') . ")\n- office holdings " . implode(', ', array_map(fn($i) => "#$i", array_keys($HOLDINGS))) . ": not touched\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'recordProvenance after: ' . (isset($set['recordProvenance']) ? mb_strlen($set['recordProvenance']) . ' of 255 characters' : 'unchanged') . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/$SLUG-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo "#$PID $NAME: " . count($set) . ' fields (' . implode(', ', array_keys($set)) . '); REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . "printed to inventory/review/$SLUG-profile-dry-run-2026-10-06.md" . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$set) { echo 'nothing to set: done already' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $e = $get($PID); $e->setFieldValues($set);
    if (!$el->saveElement($e)) { throw new \RuntimeException("#$PID: " . json_encode($e->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($PID);
$relNow = array_map('intval', $r->relatedPersons->status(null)->ids());
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && !array_diff($F['relatedPersonsAdd'] ?? [], $relNow) && (string)$r->wikidataId !== ''
    && !array_diff($D['keepNotes'] ?? [], array_map(fn($x) => (string)($x['note'] ?? ''), (array)$r->footnotes));
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog($SCRIPT, 1, $ok ? 'verified' : 'SHORT', $SUMMARY);
if (!$ok) { throw new \RuntimeException("$SCRIPT: read-back failed"); }
