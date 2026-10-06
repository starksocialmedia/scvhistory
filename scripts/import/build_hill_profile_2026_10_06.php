/**
 * Katie Hill #29332: the editorial profile (Nathan, 6 October 2026: "Lead with the valley ... The resignation belongs
 * in the record, stated plainly and sourced ... She is living, so public life only"). Reads
 * inventory/review/katie-hill-profile-draft-2026-10-06.json, made by scripts/import/draft_hill_profile_2026_10_06.py,
 * where every quotation is checked against its source (copies in inventory/sources/katie-hill-2026-10-06/).
 *
 * WHAT IT WRITES
 *  - #29332 Katie Hill: body, footnotes, bodyAuthorship editorial-2026; and where empty: occupation, wikidataId
 *    Q58416634 (its English Wikipedia sitelink is Katie Hill and its Bioguide id P1157 is H001087), bioguideId H001087,
 *    personWikipediaUrl (a finding aid only). Adds the aliases Rep. Katie Hill and Katherine Hill (the name on her
 *    court filing of 22 December 2020).
 *  - Office holding #29378: one note citing her resignation letter in the Congressional Record, appended if no note
 *    there cites H8727. The holding's dates stand (3 January 2019 to 3 November 2019, as the letter gives the date).
 *  - NOT her portrait, NOT birth fields (living: left to Nathan), no relations, no kinship.
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/katie-hill-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_hill_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = json_decode(file_get_contents("$root/inventory/review/katie-hill-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$HILL = 29332; $HOLD = 29378;
$bad = []; $out = [];
$k = $get($HILL);
if ($k?->title !== 'Katie Hill') { $bad[] = "#$HILL is not Katie Hill"; }
$h = $get($HOLD);
if (!$h || $h->holdingPerson->status(null)->one()?->id !== $HILL) { $bad[] = "holding #$HOLD is not Katie Hill's"; }
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $hd) { $v = $e->getFieldValue($hd);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl() === ''; }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$rows = fn(array $notes) => array_map(fn($n) => ['number' => (string)$n['number'], 'note' => $n['note'], 'source' => 'editorial-2026'], $notes);

/* 1. Katie Hill */
$set = [];
if ($k) {
    $kl = $lay($k);
    $cur = trim((string)$k->body);
    if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = "#$HILL has a different body already"; }
    $oldNotes = array_values(array_filter((array)$k->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if ($oldNotes && $cur === '') { $bad[] = "#$HILL has footnotes without a body: " . count($oldNotes); }
    if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
    $F = $D['fields'];
    foreach (['bodyAuthorship', 'occupation', 'wikidataId', 'bioguideId'] as $hd) {
        if (!in_array($hd, $kl, true)) { $bad[] = "$hd not on the person layout"; continue; }
        if ($isEmpty($k, $hd)) { $set[$hd] = $F[$hd]; }
        elseif (trim((string)$k->getFieldValue($hd)) !== $F[$hd] && $hd !== 'occupation') { $bad[] = "$hd already holds " . $k->getFieldValue($hd); }
    }
    if (in_array('personWikipediaUrl', $kl, true) && $isEmpty($k, 'personWikipediaUrl')) { $set['personWikipediaUrl'] = ['type' => 'url', 'value' => $F['personWikipediaUrl']]; }
    $aliases = array_values(array_filter(array_map('trim', explode("\n", (string)$k->personAliases))));
    $addA = array_values(array_diff($F['aliasesAdd'], $aliases)); if ($addA) { $set['personAliases'] = implode("\n", array_merge($aliases, $addA)); }
    /* another record already holding these authority ids would be a duplicate person */
    foreach (['wikidataId' => $F['wikidataId'], 'bioguideId' => $F['bioguideId']] as $hd => $val) {
        $dup = Entry::find()->section('persons')->status(null)->id(['not', $HILL])->{$hd}($val)->ids();
        if ($dup) { $bad[] = "$val is on another record: #" . implode(', #', $dup); }
    }
    if ($set) { $set['recordProvenance'] = trim((string)$k->recordProvenance . '; build_hill_profile_2026_10_06.php, 6 October 2026: editorial profile, authority ids, aliases'); }
}

/* 2. the resignation note on her holding */
$hset = [];
if ($h) {
    $hr = array_values(array_filter((array)$h->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (!array_filter($hr, fn($r) => str_contains((string)$r['note'], 'H8727'))) {
        $hset = array_merge(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => $r['note'], 'source' => $r['source'] ?? ''], $hr),
            [['number' => (string)(count($hr) + 1), 'note' => $D['holdingNotes'][(string)$HOLD], 'source' => 'editorial-2026']]);
    }
    if ((string)$h->termEndEdtf !== '2019-11-03') { $bad[] = "holding #$HOLD ends " . $h->termEndEdtf . ', not 2019-11-03 as the letter gives'; }
}

/* our own text: no em dash, nothing the brief keeps out */
$own = $D['body'] . implode('', $D['holdingNotes']);
if (preg_match('~\x{2014}~u', $own)) { $bad[] = 'an em dash in our own text'; }
foreach (['RedState', 'Daily Mail', 'Mail Media', 'Salem', 'Van Laar', 'Heslep', 'Messina', 'nude', 'naked'] as $w) {
    if (stripos($own . implode('', array_column($D['footnotes'], 'note')), $w) !== false) { $bad[] = "\"$w\" appears in the text or notes"; }
}

/* the report */
$show = fn($v) => is_array($v) ? (isset($v['value']) ? $v['value'] : (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', $v))) : (mb_strlen($v) > 120 ? mb_substr($v, 0, 120) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# Katie Hill #29332: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/katie-hill-profile-draft-2026-10-06.md.\n";
$out[] = "## #29332 Katie Hill\n\n" . ($set ? implode("\n", array_map(fn($hd, $v) => "- $hd: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n";
$out[] = '- the existing footnotes: ' . (empty($oldNotes) ? 'none (one empty row)' : count($oldNotes)) . "\n- featuredImage: not touched (#" . ($k?->featuredImage->status(null)->one()?->id ?? 'none') . ")\n- birth fields: not touched (living)\n";
$out[] = "## Office holding #29378\n\n- term: " . ($h ? $h->termStartEdtf . ' to ' . $h->termEndEdtf . ', ' . $h->howEnded : '?') . "\n" . ($hset ? '- add note ' . count($hset) . ': ' . end($hset)['note'] : '- nothing to add') . "\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = "## Notes as they will read\n\n" . implode("\n", array_map(fn($n) => '[' . $n['number'] . '] ' . $n['note'], $D['footnotes'])) . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/katie-hill-profile-dry-run-2026-10-06.md", implode("\n", $out));
echo '#29332: ' . count($set) . ' fields; holding #29378: ' . ($hset ? 'one note' : 'nothing') . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/katie-hill-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if ($set) { $e = $get($HILL); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException('#29332: ' . json_encode($e->getFirstErrors())); } }
    if ($hset) { $e = $get($HOLD); $e->setFieldValues(['footnotes' => $hset]); if (!$el->saveElement($e)) { throw new \RuntimeException('#29378: ' . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($HILL);
$ok = trim((string)$r->body) === trim($D['body']) && count(array_filter((array)$r->footnotes, fn($x) => trim((string)($x['note'] ?? '')) !== '')) === count($D['footnotes']);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_hill_profile_2026_10_06.php', ($set ? 1 : 0) + ($hset ? 1 : 0), $ok ? 'verified' : 'SHORT', 'Katie Hill: editorial profile, authority ids, aliases; resignation note on her holding');
if (!$ok) { throw new \RuntimeException('build_hill_profile: read-back failed'); }
