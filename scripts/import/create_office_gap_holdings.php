/**
 * Office holdings the civic records lacked (Nathan, 3 October 2026: "The gaps in
 * our election records matter more than the profiles: missing water board
 * terms, appointments, resignations, and seats held with no election row.
 * Those are factual errors in the civic data ... Fix them as you go").
 *
 * The gaps came from the recount of the 42 (inventory/review/office-only-recount-
 * 2026-10-03.md); the facts and their sources from inventory/news/office-gaps-
 * 2026-10-03/facts.json, 43 saved pages, every quote an exact phrase of its
 * saved text. One holding per continuous tenure, as Trunkey's and Ahuja's are:
 * the sources give tenures ("since 1999", "served through December of 2017"),
 * not the term-by-term dates the council's holdings carry, and splitting them
 * would invent dates. The term-by-term derivation for every board, from the
 * election results, is a separate task for Nathan to scope.
 *
 * WHAT THE SOURCES CORRECTED
 *   Plambeck: not continuous on the Newhall County Water District; she lost in
 *     1997 and returned in 1999 (SCV Water Resolution SCV-321). Two holdings.
 *   Martin: SCV Water board president from 2020, not 2021.
 *   Mercado-Fortine: CEDA's 1997 win and 2001 loss are right; "2000 to 2016" is
 *     not. Two Hart holdings, 1997 to 2001 and 2003 to 2015. Her Castaic Union
 *     years (ten or twelve, by the source) have no dates and are not created.
 *   Messina: continuous from 2009; his 2013 seat was uncontested, hence no row.
 *   Arrowsmith: resigned September 2022, after filing closed, so her name stayed
 *     on the ballot she lost; the CEDA row is hers.
 *   Bryce: the sources disagree on his start (12 years, "more than 14 years",
 *     "fourth term" in December 2013): about 2001, marked approximate. An
 *     earlier tenure (he stood in 1997 as "Incumbent") has no dates and is not
 *     created.
 *
 * SCV WATER'S FIRST BOARD was neither elected nor appointed: SB 634 seated the
 * sitting directors of the merged agencies. selectionMethod "succeeded", with a
 * footnote saying so.
 *
 * Koscielny (1995 to 2016) is added from the obituaries confirmed today
 * (inventory/news/koscielny-obituary-2026-10-03.json). Walters comes with his
 * profile. Offices within a tenure (president, clerk) are in the footnotes'
 * quotes; the archive does not model them as holdings.
 *
 * Idempotent: a person with a holding on the same body starting the same year
 * is skipped. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_office_gap_holdings.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $D = "$root/inventory/news/office-gaps-2026-10-03";
$svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], (string)$s)));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$facts = json_decode(file_get_contents("$D/facts.json"), true);
$BODY = ['Newhall County Water District' => 27534, 'Castaic Lake Water Agency' => 26563, 'Santa Clarita Valley Water Agency' => 402,
    'Saugus Union School District' => 21592, 'Newhall School District' => 21590, 'William S. Hart Union High School District' => 21588,
    'Sulphur Springs Union School District' => 21594, 'Castaic Union School District' => 21596];
$WATER = [27534, 26563, 402]; $RW = $get(27420); $RS = $get(26964);
$bad = [];
if (!$RW || $RW->title !== 'Water Board Director' || !$RS || $RS->title !== 'School Board Member') { $bad[] = 'a role is not where expected'; }
foreach ($BODY as $t => $id) { if ($get($id)?->title !== ($id === 402 ? 'Santa Clarita Valley Water' : $t)) { $bad[] = "#$id is not $t"; } }

/* Each source: its saved file's hash, and its text for the quotes. */
$text = []; $cite = [];
$date = fn($d) => preg_match('~^(\d{4})-(\d{2})-(\d{2})~', (string)$d, $m) ? (new \DateTime("$m[1]-$m[2]-$m[3]"))->format('F j, Y') : (string)$d;
foreach ($facts['sources'] as $k => $s) {
    $f = "$D/" . ($s['file'] ?? "$k.html");
    if (!is_file($f) || hash_file('sha256', $f) !== $s['htmlSha256']) { $bad[] = "source $k: the saved file is missing or changed"; continue; }
    $text[$k] = $ws(@file_get_contents("$D/$k.txt"));
    $cite[$k] = trim(implode(', ', array_filter([$s['byline'] ?? '', '"' . trim($s['title']) . '"', $s['published'] ? $date($s['published']) : '', $s['url']])), ', ');
}
$rows = [];
foreach ($facts['people'] as $p) {
    foreach ($p['holdings'] as $h) {
        $notes = [];
        foreach ($h['evidence'] as $e) {
            if (!isset($text[$e['source']])) { $bad[] = "{$p['name']}: unknown source {$e['source']}"; continue; }
            if (!str_contains($text[$e['source']], $ws($e['quote']))) { $bad[] = "{$p['name']}: {$e['source']} does not read \"" . mb_substr($e['quote'], 0, 50) . '"'; continue; }
            $notes[] = $cite[$e['source']] . ': "' . $ws($e['quote']) . '"';
        }
        $rows[] = ['id' => (int)$p['id'], 'name' => $p['name'], 'body' => $BODY[$h['body']] ?? null, 'bodyTitle' => $h['body'], 'start' => $h['start'], 'startEdtf' => $h['startEdtf'], 'sel' => $h['selection'],
            'end' => $h['end'] ?? null, 'endEdtf' => $h['endEdtf'] ?? null, 'how' => $h['howEnded'], 'notes' => array_values(array_unique($notes)), 'conf' => $h['confidence']];
    }
}
/* Koscielny, from the two obituaries. */
$ko = json_decode(file_get_contents("$root/inventory/news/koscielny-obituary-2026-10-03.json"), true)['articles'];
$kq = 'her 20 years of public service when she stepped down from the board in late 2016';
foreach ($ko as $a) { if (!str_contains($ws($a['text']), $kq)) { $bad[] = 'a Koscielny obituary does not read the 2016 sentence'; } }
$rows[] = ['id' => 25397, 'name' => 'Rosemarie Koscielny', 'body' => 21592, 'bodyTitle' => 'Saugus Union School District', 'start' => 'December 1995', 'startEdtf' => '1995-12', 'sel' => 'elected', 'end' => 'late 2016', 'endEdtf' => '2016-12~', 'how' => 'left', 'conf' => 'two sources',
    'notes' => ['KHTS (hometownstation.com), "Rosemarie Koscielny, Former Saugus Union School District Trustee, Dies At 74," August 20, 2026, ' . $ko[0]['url'] . ': "honored ... for ' . $kq . '."', 'SCVNews.com, "Rosemarie Koscielny, Former Saugus Union School District Trustee, Dies at 74," August 20, 2026, ' . $ko[1]['url'] . '.', 'First elected on November 7, 1995 (California Elections Data Archive, CEDA1995Data), and re-elected in 1999 and 2011.']];

/* Footnotes written for the cases a quote alone does not explain. */
$EXTRA = [
    'Santa Clarita Valley Water Agency' => 'Seated on the founding board of the Santa Clarita Valley Water Agency on January 1, 2018 by SB 634 (2017), which made the sitting directors of the merged agencies its first board: neither an election nor an appointment.',
    'merger' => 'The agency ended on January 1, 2018, when it merged into the Santa Clarita Valley Water Agency.',
    25403 => 'The sources disagree on when he joined: "12 years" (2014), "more than 14 years" (2014), and a "fourth term" beginning in December 2013. About 2001 fits the first and third; the start is approximate. He had also served earlier, standing in 1997 as the incumbent and losing; that tenure\'s dates are not known.',
    25415 => 'She resigned in September 2022, after candidate filing had closed, so her name stayed on the November 2022 ballot; she lost to Anna Griese.',
    25445 => 'Her own account of "four terms (16 years)" matches 1997 to 2001 and 2003 to 2015.',
];
$sel = ['elected' => 'elected', 'appointed' => 'appointed', 'unknown' => null];
$plan = [];
foreach ($rows as $r) {
    $who = $get($r['id']);
    if (!$who || $ws($who->title) !== $ws(preg_replace('~ "Shelley"~', '', $r['name']))) { if (!($r['id'] === 25433 && $who?->title === 'Rochelle Weinstein')) { $bad[] = "#{$r['id']} is not {$r['name']} (" . ($who?->title ?? 'missing') . ')'; continue; } }
    if (!$r['body']) { $bad[] = "{$r['name']}: no record for {$r['bodyTitle']}"; continue; }
    $y = substr((string)$r['startEdtf'], 0, 4);
    $dup = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $r['id'], 'field' => 'holdingPerson'], ['targetElement' => $r['body'], 'field' => 'holdingBody']])->all();
    $dup = array_filter($dup, fn($h) => substr((string)$h->termStartEdtf, 0, 4) === $y);
    $notes = $r['notes'];
    $founding = $r['body'] === 402 && str_starts_with((string)$r['startEdtf'], '2018-01');
    if ($founding) { $notes[] = $EXTRA['Santa Clarita Valley Water Agency']; }
    if (in_array($r['body'], [27534, 26563]) && str_starts_with((string)$r['endEdtf'], '2017-12')) { $notes[] = $EXTRA['merger']; }
    if (isset($EXTRA[$r['id']]) && ($r['id'] !== 25445 || $y === '2003')) { $notes[] = $EXTRA[$r['id']]; }
    if (count($notes) < 1) { $bad[] = "{$r['name']}: no footnote survives"; }
    foreach ($notes as $n) { if (preg_match('~inventory/|\.json|\x{2014}~u', $n)) { $bad[] = "{$r['name']}: a footnote names machinery or has an em dash"; } }
    $how = $r['how'] === 'unknown' ? 'unknown' : $r['how'];
    $plan[] = $r + ['dup' => (bool)$dup, 'fnotes' => $notes, 'selM' => $founding ? 'succeeded' : ($sel[$r['sel']] ?? null), 'howM' => $how,
        'startEv' => $r['conf'] === 'conflict' ? 'retrospective' : 'retrospective', 'endEv' => $how === 'serving' ? 'roster' : ($how === 'resigned' ? 'contemporary' : ($how === 'unknown' ? null : 'retrospective'))];
}
$n = 0;
foreach ($plan as $r) {
    printf("%s %-32s %-44s %-9s %-10s -> %-10s %-9s %d notes%s\n", $r['dup'] ? 'have  ' : 'create', mb_substr($r['name'], 0, 32), mb_substr($r['bodyTitle'], 0, 44), $r['startEdtf'], $r['selM'] ?? '-', $r['endEdtf'] ?? '-', $r['howM'], count($r['fnotes']), $r['conf'] !== 'two sources' ? "  ({$r['conf']})" : '');
    if (!$r['dup']) { $n++; }
}
echo "$n holdings to create, " . (count($plan) - $n) . ' already held' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
$made = 0; $os = $svc->getSectionByHandle('officeHoldings'); $type = $svc->getEntryTypeByHandle('officeHolding');
$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $r) {
        if ($r['dup']) { continue; }
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($type->id);
        $v = ['holdingPerson' => [$r['id']], 'holdingOffice' => [in_array($r['body'], $WATER) ? $RW->id : $RS->id], 'holdingBody' => [$r['body']],
            'termStart' => (string)$r['start'], 'termStartEdtf' => (string)$r['startEdtf'], 'howEnded' => $r['howM'], 'startEvidence' => $r['startEv'],
            'footnotes' => $fn($r['fnotes']), 'recordProvenance' => 'create_office_gap_holdings.php, 3 October 2026'];
        if ($r['selM']) { $v['selectionMethod'] = $r['selM']; }
        if ($r['endEdtf']) { $v['termEnd'] = (string)$r['end']; $v['termEndEdtf'] = (string)$r['endEdtf']; if ($r['endEv']) { $v['endEvidence'] = $r['endEv']; } }
        $h->setFieldValues($v);
        if (!$el->saveElement($h)) { throw new \RuntimeException("{$r['name']}: " . json_encode($h->getFirstErrors())); }
        $made++;
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$after = (int)Entry::find()->section('officeHoldings')->status(null)->recordProvenance('create_office_gap_holdings.php*')->count();
$ok = $after === $made + 0 || $after >= $made;
echo "READ-BACK $after holdings carry this script's provenance; $made created now" . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('create_office_gap_holdings.php', $made, $ok ? 'verified' : 'SHORT', 'office holdings the civic records lacked: water boards, appointments, resignations, uncontested seats');
