/**
 * BJ Atkins #28316: the profile, and his water-board service as the sources give it (Nathan, 6 October 2026:
 * "Lead with the valley, which for him is the water board ... Living person: public life only. Leave the business
 * narrative out except for what he is, an environmental consultant"). Reads
 * inventory/review/bj-atkins-profile-draft-2026-10-06.json, made by scripts/import/draft_atkins_profile_2026_10_06.py,
 * where every quotation is checked against the saved copy of its source.
 *
 * WHAT IT WRITES
 *  - #28316 BJ Atkins: body, footnotes (14), bodyAuthorship editorial-2026; occupation "Environmental consultant" if
 *    empty; aliases "B. J. Atkins" and "B.J. Atkins" added.
 *  - Five new office holdings (Water Board Director #27420): Newhall County Water District #27534, elected Dec 2005,
 *    Dec 2009, Dec 2013 (the last ending 31 Dec 2017, "left"); Castaic Lake Water Agency #26563, appointed, Jan 2009 to
 *    31 Dec 2017 ("left"); Santa Clarita Valley Water #402, Division 3 #25369, founding director 1 Jan 2018 to Jan 2021
 *    ("succeeded", "reelected"). A holding is skipped if he already holds one at that body starting the same year.
 *  - #28415 (SCV Water, 2021 to 2022): note 2 (stale: "when this term ended, and how, is not recorded") and note 4
 *    (its appointment date uncited) are rewritten; notes 1 and 3, the dates, howEnded and evidence stand.
 *  - NOT his portrait (inventory/incoming/BJ-Atkins.jpg is handled separately).
 * Fills an empty body only; refuses a body that differs. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Prints inventory/review/bj-atkins-profile-dry-run-2026-10-06.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_atkins_profile_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$D = json_decode(file_get_contents("$root/inventory/review/bj-atkins-profile-draft-2026-10-06.json"), true);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$P = 28316; $ROLE = 27420; $H = 28415;
$bad = []; $out = [];
if ($D['quoteCheck'] !== 'every quotation found in its saved source') { $bad[] = 'the draft failed its quotation check'; }
foreach ([$P => 'BJ Atkins', $ROLE => 'Water Board Director', 27534 => 'Newhall County Water District', 26563 => 'Castaic Lake Water Agency', 402 => 'Santa Clarita Valley Water', 25369 => 'Santa Clarita Valley Water, Division 3'] as $id => $t) {
    if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; }
}
$p = $get($P);
$lay = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$isEmpty = function ($e, $h) { $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return !$v->status(null)->exists(); }
    if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value === ''; }
    return trim((string)$v) === ''; };
$live = fn($rows) => array_values(array_filter((array)$rows, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
$rows = fn(array $notes) => array_map(fn($n, $i) => ['number' => (string)($i + 1), 'note' => is_array($n) ? $n['note'] : $n, 'source' => 'editorial-2026'], $notes, array_keys($notes));

/* 1. the person */
$pl = $lay($p); $set = [];
foreach (['body', 'footnotes', 'bodyAuthorship', 'occupation', 'personAliases', 'recordProvenance'] as $h) { if (!in_array($h, $pl, true)) { $bad[] = "$h not on the person layout"; } }
$cur = trim((string)$p->body);
if ($cur !== '' && $cur !== trim($D['body'])) { $bad[] = '#28316 has a different body already'; }
if ($cur !== trim($D['body'])) { $set['body'] = $D['body']; $set['footnotes'] = $rows($D['footnotes']); }
if ($live($p->footnotes) && $cur === '') { $bad[] = '#28316 has footnotes but no body; look before replacing them'; }
foreach (['bodyAuthorship', 'occupation'] as $h) { if (in_array($h, $pl, true) && $isEmpty($p, $h)) { $set[$h] = $D['fields'][$h]; } }
$aliases = array_values(array_filter(array_map('trim', explode("\n", (string)$p->personAliases))));
$addA = array_values(array_diff($D['fields']['aliasesAdd'], $aliases)); if ($addA) { $set['personAliases'] = implode("\n", array_merge($aliases, $addA)); }
if ($set) { $set['recordProvenance'] = trim((string)$p->recordProvenance . '; build_atkins_profile_2026_10_06.php, 6 October 2026: editorial profile (public life only), occupation, aliases'); }

/* 2. the five holdings */
$os = $svc->getSectionByHandle('officeHoldings'); $type = $svc->getEntryTypeByHandle('officeHolding');
if (!$os || !$type) { $bad[] = 'officeHoldings section or officeHolding type missing'; }
$plan = [];
foreach ($D['holdingsNew'] as $r) {
    $y = substr($r['termStartEdtf'], 0, 4);
    $dup = array_filter(Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $P, 'field' => 'holdingPerson'], ['targetElement' => $r['body'], 'field' => 'holdingBody']])->all(),
        fn($h) => substr((string)$h->termStartEdtf, 0, 4) === $y);
    $v = ['holdingPerson' => [$P], 'holdingOffice' => [$ROLE], 'holdingBody' => [$r['body']],
        'termStart' => $r['termStart'], 'termStartEdtf' => $r['termStartEdtf'], 'termEnd' => $r['termEnd'], 'termEndEdtf' => $r['termEndEdtf'],
        'selectionMethod' => $r['selectionMethod'], 'howEnded' => $r['howEnded'], 'startEvidence' => $r['startEvidence'], 'endEvidence' => $r['endEvidence'],
        'footnotes' => $rows($r['footnotes']), 'recordProvenance' => 'build_atkins_profile_2026_10_06.php, 6 October 2026: water-board service from the agency\'s resolution, Smart Voter, SCVNews and SB 634'];
    if ($r['district']) { $v['holdingDistrict'] = [$r['district']]; }
    if ($r['seatLabel'] !== '') { $v['seatLabel'] = $r['seatLabel']; }
    $plan[] = ['row' => $r, 'dup' => array_map(fn($h) => $h->id, $dup), 'v' => $v];
}
$test = new Entry(); if ($type) { $test->sectionId = $os->id; $test->setTypeId($type->id);
    foreach (['holdingPerson', 'holdingOffice', 'holdingBody', 'holdingDistrict', 'termStart', 'termStartEdtf', 'termEnd', 'termEndEdtf', 'selectionMethod', 'howEnded', 'startEvidence', 'endEvidence', 'seatLabel', 'footnotes', 'recordProvenance'] as $h) {
        if (!in_array($h, $lay($test), true)) { $bad[] = "$h not on the officeHolding layout"; } } }
/* the dropdown values must exist */
foreach (['selectionMethod', 'howEnded', 'startEvidence', 'endEvidence'] as $h) {
    $f = Craft::$app->getFields()->getFieldByHandle($h); $opts = $f && property_exists($f, 'options') ? array_column($f->options, 'value') : null;
    foreach ($plan as $x) { if ($opts !== null && !in_array($x['v'][$h], $opts, true)) { $bad[] = "$h has no option '{$x['v'][$h]}'"; } }
}

/* 3. #28415: notes 2 and 4 */
$h28 = $get($H); $hset = [];
if (!$h28 || $h28->holdingPerson->status(null)->one()?->id !== $P) { $bad[] = "#$H is not BJ Atkins's"; }
else {
    $hr = $live($h28->footnotes); $new = [];
    foreach ($hr as $r) { $n = (string)$r['number']; $new[] = ['number' => $n, 'note' => $D['holding28415']['replace'][$n] ?? $r['note'], 'source' => isset($D['holding28415']['replace'][$n]) ? 'editorial-2026' : ($r['source'] ?? '')]; }
    if (count($hr) !== 4) { $bad[] = "#$H has " . count($hr) . ' notes, expected 4'; }
    if (!str_contains((string)($hr[1]['note'] ?? ''), 'is not recorded') && ($hr[1]['note'] ?? '') !== $D['holding28415']['replace']['2']) { $bad[] = "#$H note 2 is not the stale note"; }
    if (array_column($new, 'note') !== array_column($hr, 'note')) { $hset = $new; }
    if ((string)$h28->termEndEdtf !== '2022-07-20' || (string)$h28->howEnded !== 'resigned') { $bad[] = "#$H end is not 2022-07-20 resigned"; }
}
if (preg_match('~\x{2014}~u', $D['body'] . json_encode($D['footnotes']) . json_encode($D['holdingsNew']) . json_encode($D['holding28415']))) { $bad[] = 'an em dash in our own text'; }

/* the report */
$show = fn($v) => is_array($v) ? (isset($v[0]['note']) ? count($v) . ' notes' : implode(', ', array_map(fn($x) => '#' . $x . ' ' . ($get($x)?->title ?? '?'), $v))) : (mb_strlen($v) > 140 ? mb_substr($v, 0, 140) . '... (' . mb_strlen($v) . ' chars)' : $v);
$out[] = "# BJ Atkins #28316: the profile, dry run (6 October 2026)\n\nNothing is written by a dry run. The prose and every note are in inventory/review/bj-atkins-profile-draft-2026-10-06.md.\n";
$out[] = "## #28316 BJ Atkins\n\n" . ($set ? implode("\n", array_map(fn($h, $v) => "- $h: " . $show($v), array_keys($set), $set)) : '- nothing to change') . "\n- featuredImage: not touched\n";
$out[] = "## New office holdings\n\n" . implode("\n", array_map(fn($x) => ($x['dup'] ? '- HAVE (#' . implode(', #', $x['dup']) . ') ' : '- create ') . "{$x['row']['termStartEdtf']} to {$x['row']['termEndEdtf']}, " . ($get($x['row']['body'])?->title ?? '?') . ($x['row']['seatLabel'] ? ", {$x['row']['seatLabel']}" : '') . ", {$x['v']['selectionMethod']}, ended {$x['v']['howEnded']}, evidence {$x['v']['startEvidence']}/{$x['v']['endEvidence']}, " . count($x['v']['footnotes']) . ' notes', $plan)) . "\n";
$out[] = "## #28415\n\n" . ($hset ? implode("\n", array_map(fn($r) => "- note {$r['number']}: " . $r['note'], array_filter($hset, fn($r) => isset($D['holding28415']['replace'][$r['number']])))) : '- nothing to change') . "\n";
$out[] = "## Body as it will read\n\n" . $D['body'] . "\n";
$out[] = 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . "\n";
file_put_contents("$root/inventory/review/bj-atkins-profile-dry-run-2026-10-06.md", implode("\n", $out));
$toMake = count(array_filter($plan, fn($x) => !$x['dup']));
echo '#28316: ' . count($set) . ' fields; holdings to create: ' . $toMake . '; #28415 notes: ' . ($hset ? 'rewrite 2 and 4' : 'none') . '; REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL . 'printed to inventory/review/bj-atkins-profile-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction(); $made = 0;
try {
    if ($set) { $e = $get($P); $e->setFieldValues($set); if (!$el->saveElement($e)) { throw new \RuntimeException('#28316: ' . json_encode($e->getFirstErrors())); } }
    foreach ($plan as $x) {
        if ($x['dup']) { continue; }
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($type->id); $h->setFieldValues($x['v']);
        if (!$el->saveElement($h)) { throw new \RuntimeException("{$x['row']['termStartEdtf']}: " . json_encode($h->getFirstErrors())); }
        $made++;
    }
    if ($hset) { $e = $get($H); $e->setFieldValues(['footnotes' => $hset]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$H: " . json_encode($e->getFirstErrors())); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($P);
$n = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $P, 'field' => 'holdingPerson'])->count();
$ok = trim((string)$r->body) === trim($D['body']) && count($live($r->footnotes)) === count($D['footnotes']) && $n >= 6
    && str_contains((string)($live($get($H)->footnotes)[1]['note'] ?? ''), 'midnight on the 20th');
echo 'READ-BACK ' . ($ok ? "OK: $n holdings; " . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('build_atkins_profile_2026_10_06.php', $made + ($set ? 1 : 0) + ($hset ? 1 : 0), $ok ? 'verified' : 'SHORT', 'BJ Atkins: editorial profile; NCWD 2005/2009/2013, CLWA 2009-2017 and SCV Water founding holdings; #28415 notes 2 and 4');
if (!$ok) { throw new \RuntimeException('build_atkins_profile: read-back failed'); }
