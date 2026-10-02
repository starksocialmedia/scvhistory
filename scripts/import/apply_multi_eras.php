/**
 * Multi-era assignment (Nathan, 2 October 2026: "a person is assigned every era
 * in which they did something the archive records, and the first-listed is their
 * primary era"; answers of the same day: Hart gets World War II from the pass and
 * Postwar Boom by hand for the legacy; McKeon keeps Incorporation Struggle;
 * Tataviam and Native Peoples by hand only, never by the pass).
 *
 * An era qualifies when it holds at least one year of an office, one race
 * stood, or two other dated acts within the life. The pass never sets or moves a
 * primary: the first era stays first and the qualifying ones follow in time
 * order. Never assigned by the pass: the event eras (St. Francis Dam, Northridge
 * Recovery) and Tataviam & Native Peoples, since a year cannot say where
 * something happened. Additions only; nothing is removed.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_multi_eras.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$BY_HAND = [16356 => [166]];


use craft\elements\{Entry, Category};

$NOW = (int)date('Y'); $eras = [];
foreach (Category::find()->group('historicalEra')->all() as $c) {
    if (!preg_match('~\((?:to )?(\d{4})?[^\d]*(\d{4}|present)\)~u', $c->title, $m)) { continue; }
    $eras[$c->id] = ['t' => preg_replace('~\s*\(.*\)$~', '', $c->title), 'from' => str_contains($c->title, 'to ') || $m[1] === '' ? -10000 : (int)$m[1], 'to' => $m[2] === 'present' ? $NOW : (int)$m[2], 'event' => in_array($c->id, [157, 163, 169], true)];
}
$eraOf = function (int $y) use ($eras) { foreach ($eras as $id => $e) { if (!$e['event'] && $y >= $e['from'] && $y <= $e['to']) { return $id; } } return null; };
$year = fn($s) => preg_match('~\b(1[5-9]\d\d|20\d\d)\b~', (string)$s, $m) ? (int)$m[1] : null;
$DECIDED = [21944, 18791, 16380, 15808, 18726, 25445, 15737, 15874, 16140, 25401, 25431, 15929, 23087, 25157, 23085, 25407, 26597, 18663, 18869, 25433, 343, 25405, 25441, 16356, 20226];
$auto = array_map('intval', explode(',', (string)@file_get_contents(\Craft::getAlias('@root') . '/storage/runtime/auto-era-ids.txt')));
$rows = []; $stats = ['unchanged' => 0, 'gains' => 0, 'eras added' => 0];
foreach (Entry::find()->section('persons')->status(null)->orderBy('title')->all() as $p) {
    $cur = $p->historicalEra->ids(); if (!$cur) { continue; }
    $group = in_array($p->id, $DECIDED, true) ? 'decided' : (in_array($p->id, $auto, true) ? 'auto' : 'earlier');
    $bY = $year($p->birthDateEdtf ?: $p->birthDate); $dY = $year($p->deathDateEdtf ?: $p->deathDate);
    $in = fn($y) => $y && (!$bY || $y >= $bY + 15) && (!$dY || $y <= $dY);
    $official = []; $other = [];
    foreach (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'holdingPerson'])->all() as $h) {
        if (!($y = $year($h->termStartEdtf))) { continue; } $e2 = $year($h->termEndEdtf) ?? (($h->howEnded?->value === 'serving') ? $NOW : $y);
        for ($k = $y; $k <= max($y, $e2); $k++) { if ($in($k) && ($e = $eraOf($k))) { $official[$e] = ($official[$e] ?? 0) + 1; } } }
    foreach (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'candidacyPerson'])->all() as $c) { $y = $year($c->candidacyElection->one()?->electionDateEdtf); if ($in($y) && ($e = $eraOf($y))) { $official[$e] = ($official[$e] ?? 0) + 1; } }
    foreach (($p->recordDates ?? []) as $r) { if (!is_array($r) || preg_match('~\b(born|dies|died|death|birth)\b~i', (string)($r['label'] ?? ''))) { continue; } $iso = $r['iso'] ?? null; $y = $iso instanceof \DateTimeInterface ? (int)$iso->format('Y') : $year(is_array($iso) ? ($iso['date'] ?? '') : (string)$iso); if ($in($y) && ($e = $eraOf($y))) { $other[$e] = ($other[$e] ?? 0) + 1; } }
    foreach (Entry::find()->section('photographs')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'photoPeople'])->all() as $ph) { $y = $year($ph->photoDateEdtf); if ($in($y) && ($e = $eraOf($y))) { $other[$e] = ($other[$e] ?? 0) + 1; } }
    foreach (Entry::find()->section('events')->status(null)->relatedTo($p)->all() as $ev) { $y = $year($ev->eventDateEdtf); if ($in($y) && ($e = $eraOf($y))) { $other[$e] = ($other[$e] ?? 0) + 1; } }
    $qual = [];
    foreach (array_keys($eras) as $e) { if (($official[$e] ?? 0) >= 1 || ($other[$e] ?? 0) >= 2) { $qual[] = $e; } }
    $primary = $cur[0];
    $rest = array_values(array_filter($qual, fn($e) => $e !== $primary));
    usort($rest, fn($a, $b) => $eras[$a]['from'] <=> $eras[$b]['from']);
    $new = array_merge([$primary], $rest);
    foreach ($BY_HAND[$p->id] ?? [] as $hand) { if (!in_array($hand, $new, true)) { $new[] = $hand; } }
    /* Never drop an era a person already has. */
    foreach ($cur as $c0) { if (!in_array($c0, $new, true)) { $new[] = $c0; } }
    $gain = array_values(array_diff($new, $cur));
    if ($gain) { $stats['gains']++; $stats['eras added'] += count($gain); } else { $stats['unchanged']++; }
    $rows[] = [$group, $p, $cur, $new, $gain];
    if ($gain) { echo str_pad($p->title, 34) . '+ ' . implode(', ', array_map(fn($e) => $eras[$e]['t'], $gain)) . PHP_EOL; }
}
$n = fn($ids) => implode(', ', array_map(fn($e) => $eras[$e]['t'] ?? "#$e", $ids));
$md = ['# Multi-era assignment, ' . date('j F Y'), '', 'Generated by scripts/import/preview_multi_eras.php, read-only. Primary first (unchanged); the rest in time order. An era qualifies with an office year, a race, or two other dated acts within the life.', ''];
foreach (['decided' => 'The 25 Nathan decided', 'auto' => 'The 47 assigned by the pass', 'earlier' => 'Assigned before the pass (for reference)'] as $g => $head) {
    $md[] = "## $head"; $md[] = ''; $md[] = '| Person | Now | Under the new rule | Added |'; $md[] = '|---|---|---|---|';
    foreach ($rows as [$gr, $p, $cur, $new, $gain]) { if ($gr !== $g) { continue; } $md[] = "| [{$p->title}]({$p->url}) | " . $n($cur) . ' | **' . $n([$new[0]]) . '**' . (count($new) > 1 ? ', ' . $n(array_slice($new, 1)) : '') . ' | ' . ($gain ? $n($gain) : 'none') . ' |'; }
    $md[] = '';
}
echo json_encode($stats) . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$short = []; $done = 0;
foreach ($rows as [$g, $p, $cur, $new, $gain]) {
    if (!$gain) { continue; }
    $e = Entry::find()->id($p->id)->status(null)->one(); $e->setFieldValue('historicalEra', $new);
    if (!Craft::$app->getElements()->saveElement($e) || Entry::find()->id($p->id)->status(null)->one()->historicalEra->ids() !== $new) { $short[] = "#{$p->id}"; } else { $done++; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $done people, primaries unchanged") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('apply_multi_eras.php', $done, $short ? 'SHORT' : 'verified', 'every qualifying era added after the primary; Hart Postwar Boom by hand; Tataviam era by hand only');
if ($short) { throw new \RuntimeException('apply_multi_eras: ' . implode(', ', $short)); }
