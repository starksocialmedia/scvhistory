/**
 * Phineas Banning #18714: the editorial profile (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
 * profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Reads
 * inventory/review/phineas-banning-profile-draft-2026-10-06.json, made by scripts/import/draft_banning_profile_2026_10_06.py,
 * where every quotation is checked against its saved source (Bell 1881 and the Sacramento Daily Record-Union of 10 March
 * 1885 in inventory/sources/phineas-banning-2026-10-06/, with a manifest of their sha256) or the Reggie mirror (Perkins,
 * Worden, Pollack, the 1976 centennial program).
 *
 * WHAT IT WRITES
 *  - #18714 Phineas Banning: body (leads with his valley role: the first stagecoach over the San Fernando Pass, December
 *    1854, by Horace Bell's account; the road to Fort Tejon; Lang, 1876), footnotes (10, editorial-2026; the record has
 *    none now), bodyAuthorship editorial-2026; and where empty: birthplace (Bell 1881), deathDate, deathDateEdtf and
 *    deathEvidence contemporary (the Record-Union of Tuesday 10 March 1885: "died Sunday", 8 March), occupation,
 *    wikidataId Q7186337 and personWikipediaUrl (finding aids only; the article says nothing of the valley).
 *  - NO birth date (Wikipedia's 19 August 1830 is uncited), no portrait (Part A's script), no relations.
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/phineas-banning-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_banning_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/phineas-banning-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$PB = 18714; $SCRIPT = 'build_banning_profile_2026_10_06.php';
$bad = []; $out = [];
$p = $get($PB);
if ($p?->title !== 'Phineas Banning' || $p->section->handle !== 'persons') { throw new \RuntimeException("#$PB is not the person Phineas Banning"); }
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);
$fields = Craft::$app->getFields();

$pl = $lay($p); $set = []; $kept = [];
$F = $D['fields'];
foreach (array_merge(['body', 'footnotes', 'recordProvenance'], array_keys($F)) as $h) { if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; } }
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$PB has a different body already"; }
$oldNotes = array_values(array_filter((array)$p->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
if ($oldNotes && $cur === '') { $bad[] = "#$PB has footnotes but no body: read them before replacing"; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
foreach (['bodyAuthorship', 'birthplace', 'deathDate', 'deathDateEdtf', 'deathEvidence', 'occupation', 'wikidataId'] as $h) {
    if (!in_array($h, $pl, true)) { continue; }
    if ($isEmpty($p, $h)) { $set[$h] = $F[$h]; } else { $kept[$h] = (string)(is_object($p->$h) && property_exists($p->$h, 'value') ? $p->$h->value : $p->$h); }
}
/* a death date already there that disagrees is a refusal, not a silent keep */
if (isset($kept['deathDateEdtf']) && $kept['deathDateEdtf'] !== $F['deathDateEdtf']) { $bad[] = "#$PB has deathDateEdtf {$kept['deathDateEdtf']}, the sources give {$F['deathDateEdtf']}"; }
if (in_array('personWikipediaUrl', $pl, true) && $isEmpty($p, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . "; $SCRIPT, 6 October 2026: editorial profile, birthplace, death, Wikidata id"); }
if (preg_match('~\x{2014}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')))) { $bad[] = 'an em dash in our own text'; }
if (preg_match('~\.\.\.|\x{2026}~u', $D['body'] . implode('', array_column($D['footnotes'], 'note')))) { $bad[] = 'an ellipsis in our own text'; }
preg_match_all('~\[(\d+)\]~', $D['body'], $m); $cited = array_unique(array_map('intval', $m[1])); sort($cited);
if ($cited !== range(1, count($D['footnotes']))) { $bad[] = 'the body\'s markers and the notes do not match'; }
foreach ($set as $h => $v) {
    $f = $fields->getFieldByHandle($h);
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && is_string($v) && mb_strlen($v) > $f->charLimit) { $bad[] = "$h is " . mb_strlen($v) . " chars, over its limit of {$f->charLimit}"; }
    if ($f instanceof \craft\fields\Dropdown && is_string($v) && !in_array($v, array_column($f->options, 'value'), true)) { $bad[] = "$h: '$v' is not one of its options"; }
}

$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v)))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# Phineas Banning #$PB: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/phineas-banning-profile-draft-2026-10-06.md.\n";
$out[] = "## #$PB Phineas Banning\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- fields already filled, kept: ' . ($kept ? implode('; ', array_map(fn($h, $v) => "$h = $v", array_keys($kept), $kept)) : 'none') . "\n- existing footnotes replaced: " . ($oldNotes ? count($oldNotes) : 'none (the record has no notes)')
    . "\n- birthDate: not set (no source but Wikipedia)\n- featuredImage: not touched (" . ($p->featuredImage->status(null)->exists() ? 'has an image' : 'empty') . ")\n- recordProvenance: " . (isset($set['recordProvenance']) ? mb_strlen($set['recordProvenance']) . ' of 255 chars' : 'unchanged') . "\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => $n['number'] . '. ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/phineas-banning-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo "#$PB: " . count($set) . ' fields; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/phineas-banning-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$set) { echo 'nothing to do: done already' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $e = $get($PB); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException("#$PB: " . json_encode($e->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($PB);
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes'])
    && (string)$r->deathDateEdtf === $F['deathDateEdtf'] && (string)$r->wikidataId === $F['wikidataId'];
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog($SCRIPT, 1, $ok ? 'verified' : 'SHORT', 'Phineas Banning: editorial profile, birthplace, death 8 March 1885, Wikidata id');
if (!$ok) { throw new \RuntimeException("$SCRIPT: read-back failed"); }
