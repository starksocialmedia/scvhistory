/**
 * The eras pass (Nathan, asked four times; 1 October 2026: "the era they are
 * known for, not the one they were born in. Henry Mayo Newhall lived 1825 to
 * 1882 but belongs to the ranching and railroad era").
 *
 * THE RULE
 *   1. A person is placed by the dates of their public acts, not their birth:
 *      every year of every office term, every election stood (from the archive's own
 *      records), every dated row on their record (recordDates) that is not a
 *      birth or a death, and the dates of photographs of them and events tied
 *      to them (not documents: a publication date is not an act). A single
 *      anchor that is not an office or a race does not place anyone. Only dates within the life count, from
 *      age 15 to death. These are the anchors.
 *   2. Each anchor year falls in an era. The era with the most anchors is the
 *      one they are known for. It is assigned automatically when it holds at
 *      least two thirds of the anchors; otherwise the person needs a decision.
 *   3. With no anchors, the adult life is used instead: from age 25 to death, or
 *      to 65 if no death is recorded. It is assigned automatically only when at
 *      least two thirds of that span lies in one era.
 *   4. The two event eras (St. Francis Dam, 1926-28; Northridge Recovery,
 *      1994-2000) sit inside wider eras and are never assigned automatically.
 *   5. One era each. The field allows several; the pass sets one, the primary,
 *      and a second is a decision.
 *   6. A person who already has an era keeps it; the report says where the rule
 *      would disagree.
 *   7. The pass proposes; significance decides. Where years and significance
 *      disagree, significance wins (Nathan, 2 October 2026: McKeon in the
 *      Cityhood Era as the first mayor, Weste in Mall & Growth). DATA-MODEL, Eras.
 * Writes inventory/review/person-eras.md. Fills empty eras only. Idempotent.
 * Dry run by default. Set $APPLY = true to write the automatic assignments.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/assign_person_eras.php'))"
 */

use craft\elements\{Entry, Category};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$NOW = (int)date('Y');
$eras = [];
foreach (Category::find()->group('historicalEra')->all() as $c) {
    if (!preg_match('~\((?:to )?(\d{4})?[^\d]*(\d{4}|present)\)~u', $c->title, $m)) { continue; }
    $from = $m[1] !== '' ? (int)$m[1] : -10000; $to = $m[2] === 'present' ? $NOW : (int)$m[2];
    if (str_contains($c->title, 'to ')) { $from = -10000; }
    $eras[$c->id] = ['title' => $c->title, 'from' => $from, 'to' => $to, 'event' => in_array($c->id, [163, 169], true)];
}
$eraOf = function (int $y) use ($eras) { foreach ($eras as $id => $e) { if (!$e['event'] && $y >= $e['from'] && $y <= $e['to']) { return $id; } } return null; };
$year = fn($s) => preg_match('~\b(1[5-9]\d\d|20\d\d)\b~', (string)$s, $m) ? (int)$m[1] : null;
$rows = []; $count = ['auto-anchors' => 0, 'auto-life' => 0, 'decision' => 0, 'no-dates' => 0, 'kept' => 0, 'disagree' => 0];
foreach (Entry::find()->section('persons')->status(null)->orderBy('title')->all() as $p) {
    $anchors = [];
    /* An office counts every year it was held, so twenty-two years in Congress outweigh a year on a school board. */
    $official = 0;
    foreach (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'holdingPerson'])->all() as $h) {
        if (!($y = $year($h->termStartEdtf))) { continue; }
        $e2 = $year($h->termEndEdtf) ?? (($h->howEnded?->value === 'serving') ? $NOW : $y);
        for ($k = $y; $k <= max($y, $e2); $k++) { $anchors[] = $k; $official++; }
    }
    foreach (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'candidacyPerson'])->all() as $c) { if ($y = $year($c->candidacyElection->one()?->electionDateEdtf)) { $anchors[] = $y; $official++; } }
    foreach (($p->recordDates ?? []) as $r) { if (!is_array($r)) { continue; } $lab = strtolower((string)($r['label'] ?? '')); if (preg_match('~\b(born|dies|died|death|birth)\b~', $lab)) { continue; } $iso = $r['iso'] ?? null; $y = $iso instanceof \DateTimeInterface ? (int)$iso->format('Y') : $year(is_array($iso) ? ($iso['date'] ?? '') : (string)$iso); if ($y) { $anchors[] = $y; } }
    /* Photographs of the person and events tied to them, within the life. Not documents: a document's date is when it was published, and the 1885 printing of a letter about the gold of 1842 is not an act of Lopez's. */
    foreach (Entry::find()->section('photographs')->status(null)->relatedTo(['targetElement' => $p, 'field' => 'photoPeople'])->all() as $ph) { if ($y = $year($ph->photoDateEdtf)) { $anchors[] = $y; } }
    foreach (Entry::find()->section('events')->status(null)->relatedTo($p)->all() as $ev) { foreach (['eventDateEdtf', 'eventDate'] as $fh) { if ($ev->getFieldLayout()->getFieldByHandle($fh) && ($y = $year($ev->getFieldValue($fh)))) { $anchors[] = $y; break; } } }
    /* Only acts of the life: none before age 15 or after death, so a canonization or an anniversary does not place a person. */
    $bY = $year($p->birthDateEdtf ?: $p->birthDate); $dY = $year($p->deathDateEdtf ?: $p->deathDate);
    $anchors = array_values(array_filter($anchors, fn($y) => (!$bY || $y >= $bY + 15) && (!$dY || $y <= $dY)));
    $by = []; $basis = '';
    if ($anchors) {
        foreach ($anchors as $y) { if ($e = $eraOf($y)) { $by[$e] = ($by[$e] ?? 0) + 1; } }
        $basis = count($anchors) . ' anchor-year' . (count($anchors) === 1 ? '' : 's') . ' (' . min($anchors) . (count($anchors) > 1 ? '-' . max($anchors) : '') . ')';
    } else {
        $b = $year($p->birthDateEdtf ?: $p->birthDate); $d = $year($p->deathDateEdtf ?: $p->deathDate);
        if ($b || $d) {
            $s = $b ? $b + 25 : max(($d ?? $NOW) - 40, -10000); $t = $d ?? ($b ? min($b + 65, $NOW) : $NOW);
            if ($t < $s) { $t = $s; }
            for ($y = $s; $y <= $t; $y++) { if ($e = $eraOf($y)) { $by[$e] = ($by[$e] ?? 0) + 1; } }
            $basis = "adult life $s-$t";
        }
    }
    $cur = $p->historicalEra->ids();
    arsort($by); $top = array_key_first($by); $share = $by ? $by[$top] / array_sum($by) : 0;
    /* One undated-life anchor that is not an office or a race (a photograph) is not enough to place a person. */
    $auto = $top && $share >= 2 / 3 && !($anchors && count($anchors) === 1 && $official === 0);
    $kind = !$by ? 'no-dates' : ($auto ? ($anchors ? 'auto-anchors' : 'auto-life') : 'decision');
    if ($cur) { $count['kept']++; if ($top && !in_array($top, $cur)) { $count['disagree']++; } }
    else { $count[$kind]++; }
    $rows[] = ['p' => $p, 'cur' => $cur, 'top' => $top, 'share' => $share, 'kind' => $kind, 'basis' => $basis, 'by' => $by];
}
$name = fn($id) => $id ? preg_replace('~\s*\(.*\)$~', '', $eras[$id]['title'] ?? (string)$id) : '';
$md = ['# Eras for person records, ' . date('j F Y'), '', 'Generated by scripts/import/assign_person_eras.php. The rule is in the script\'s header: the era of a person\'s public acts, not their birth; two thirds of the anchors (or of the adult life) to assign automatically; event eras never automatic; one era each; existing eras kept.', '',
    '| | Count |', '|---|---|', "| Assigned automatically from public acts | {$count['auto-anchors']} |", "| Assigned automatically from the adult life | {$count['auto-life']} |", "| Need a decision (split between eras) | {$count['decision']} |", "| No dates at all | {$count['no-dates']} |", "| Already had an era (kept) | {$count['kept']} |", "| ... of which the rule would choose differently | {$count['disagree']} |", ''];
foreach (['decision' => 'Need a decision', 'disagree' => 'Existing era differs from the rule', 'no-dates' => 'No dates', 'auto' => 'Assigned automatically'] as $k => $head) {
    $md[] = "## $head"; $md[] = ''; $md[] = '| Person | Basis | Eras by weight | Result |'; $md[] = '|---|---|---|---|';
    foreach ($rows as $r) {
        $show = match ($k) { 'decision' => !$r['cur'] && $r['kind'] === 'decision', 'disagree' => $r['cur'] && $r['top'] && !in_array($r['top'], $r['cur']), 'no-dates' => !$r['cur'] && $r['kind'] === 'no-dates', default => !$r['cur'] && str_starts_with($r['kind'], 'auto') };
        if (!$show) { continue; }
        $w = implode('; ', array_map(fn($e, $n) => $name($e) . " $n", array_keys($r['by']), $r['by']));
        $res = $k === 'disagree' ? 'has ' . implode(', ', array_map($name, $r['cur'])) . '; rule: ' . $name($r['top']) : ($r['top'] ? $name($r['top']) . ' (' . round($r['share'] * 100) . '%)' : '');
        $md[] = "| [{$r['p']->title}]({$r['p']->url}) | {$r['basis']} | $w | $res |";
    }
    $md[] = '';
}
file_put_contents(\Craft::getAlias('@root') . '/inventory/review/person-eras.md', implode(PHP_EOL, $md) . PHP_EOL);
echo json_encode($count) . PHP_EOL . 'wrote inventory/review/person-eras.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to assign the automatic ones.' . PHP_EOL; return; }
$done = 0; $short = [];
foreach ($rows as $r) {
    if ($r['cur'] || !str_starts_with($r['kind'], 'auto')) { continue; }
    $p = Entry::find()->id($r['p']->id)->status(null)->one(); $p->setFieldValue('historicalEra', [$r['top']]);
    if (!Craft::$app->getElements()->saveElement($p) || Entry::find()->id($p->id)->status(null)->one()->historicalEra->ids() !== [$r['top']]) { $short[] = "#{$p->id}"; } else { $done++; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $done eras assigned") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('assign_person_eras.php', $done, $short ? 'SHORT' : 'verified', 'person eras by public acts; decisions listed in inventory/review/person-eras.md');
if ($short) { throw new \RuntimeException('assign_person_eras: ' . implode(', ', $short)); }
