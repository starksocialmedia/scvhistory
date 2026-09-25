/**
 * What the archive can prove about the Santa Clarita City Council on its own.
 *
 * Read only. It writes review/council-roster.md and touches nothing in Craft.
 * There is no $APPLY because there is nothing to apply: this is the evidence
 * base a roster would be built from, gathered before anybody asks the City
 * Clerk for the certified list, so the two can be compared rather than one
 * trusted.
 *
 * WHAT IT READS. Every article whose body names the City Council. For each, it
 * looks for a person named in an office: Councilman, Councilwoman,
 * Councilmember, Council member, Mayor, Mayor Pro Tem, Vice Mayor — either
 * before the name or after it. A name with no office beside it is not
 * collected, because "the council heard from Jan Heidt" does not say she was on
 * it.
 *
 * WHAT IT CLAIMS. Nothing about terms. It records, per mention, the article's
 * date and the sentence, and sorts the sentence into what it appears to
 * establish:
 *
 *   elected     "was elected", "won", "sworn in", "takes office", "unseated"
 *   left        "resigned", "stepped down", "died", "termed out", "his last
 *               meeting", "outgoing"
 *   serving     everything else: the person held the office on the date the
 *               article was published, which is the weakest useful fact and
 *               the most common one.
 *
 * A serving mention is evidence of a date inside a term, not of the term's
 * ends, so under the officeHolding scheme it is contemporary evidence for a
 * holding whose start and end are still uncited. That is the honest reading and
 * the report says so per person.
 *
 * Names are matched to person records by title and by personAliases, both
 * normalised. A name that matches nothing is not a new person: it is a
 * candidate, and the entity review decides.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/survey_council_terms.php'))"
 */

$REPORT = \Craft::getAlias('@review') . '/council-roster.md';

$OFFICES = 'Council\s*(?:man|woman|member|men|women|members)|Council\s+member|Mayor\s+Pro\s+Tem(?:pore)?|Vice\s+Mayor|Mayor';
$NAME = '[A-Z][A-Za-z\'\x{2019}-]+(?:\s+(?:[A-Z]\.|[A-Z][A-Za-z\'\x{2019}-]+)){1,2}';
$NOT_A_NAME = '~\b(City|Council|Santa|Clarita|Valley|District|County|State|Committee|Commission|Department|Association|Company|School|Board|Water|Signal|Newhall|Saugus|Canyon|Valencia)\b~';

$norm = function (string $s): string {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
    return trim(preg_replace('~\s+~', ' ', preg_replace('~[^a-z ]~', ' ', mb_strtolower($s))));
};

/* Person records, by every spelling they answer to. */
$people = [];
foreach (\craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->each() as $p) {
    foreach (array_merge([$p->title], preg_split('~[;|\n]~', (string)$p->getFieldValue('personAliases'))) as $nm) {
        $n = $norm((string)$nm);
        if ($n !== '' && !isset($people[$n])) { $people[$n] = $p; }
    }
}

$mentions = [];
$elsewhere = [];
$articles = 0;
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->each() as $a) {
    $body = (string)$a->getFieldValue('body');
    if (stripos($body, 'City Council') === false) { continue; }
    $articles++;
    $printed = trim((string)$a->getFieldValue('originalPublishDate'));
    $edtf = trim((string)$a->getFieldValue('originalPublishDateEdtf'));
    $year = $edtf !== '' ? (int)substr($edtf, 0, 4) : (preg_match('~(18|19|20)\d\d~', $printed, $m) ? (int)$m[0] : 0);

    foreach (preg_split('~(?<=[.!?])\s+~u', preg_replace('~\s+~', ' ', $body)) as $sentence) {
        if (mb_strlen($sentence) > 400) { continue; }
        $found = [];
        /* office before the name: "Councilman Carl Boyer" */
        if (preg_match_all('~(' . $OFFICES . ')\s+(' . $NAME . ')~u', $sentence, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $hit) { $found[] = [$hit[2], $hit[1]]; }
        }
        /* office after the name: "Carl Boyer, a councilman," */
        if (preg_match_all('~(' . $NAME . '),\s+(?:the\s+|a\s+|an\s+)?(' . $OFFICES . ')~u', $sentence, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $hit) { $found[] = [$hit[1], $hit[2]]; }
        }
        /* a list: "council members Jan Heidt, Jo Anne Darcy and Carl Boyer" */
        if (preg_match('~(?:' . $OFFICES . ')\s+((?:' . $NAME . ')(?:\s*,\s*(?:and\s+)?(?:' . $NAME . '))+(?:\s+and\s+(?:' . $NAME . '))?)~u', $sentence, $lm)) {
            foreach (preg_split('~\s*,\s*|\s+and\s+~', $lm[1]) as $nm) {
                if (preg_match('~^' . $NAME . '$~u', trim($nm))) { $found[] = [trim($nm), 'council members']; }
            }
        }
        foreach ($found as [$name, $office]) {
            /* "Jill Klajic's decision" is Jill Klajic. */
            $name = preg_replace('~[\x{2019}\']s$~u', '', trim($name));
            if (preg_match($NOT_A_NAME, $name)) { continue; }
            /* An office belonging to somewhere else. "Lancaster Mayor Frank
               Roberts" is Lancaster's, and "Los Angeles County Mayor Michael
               Antonovich" is the county's; neither sat on this council. The
               test is what stands immediately before the office. */
            $pos = mb_strpos($sentence, $office);
            $before = $pos !== false ? trim(mb_substr($sentence, max(0, $pos - 34), min($pos, 34))) : '';
            if (preg_match('~\b(Lancaster|Palmdale|Burbank|Glendale|Pasadena|Los\s+Angeles|L\.A\.|County|state|California|United\s+States|Ventura|San\s+Fernando)\s*$~i', $before)) {
                $elsewhere[] = ['name' => $name, 'office' => $office, 'before' => $before, 'year' => $year];
                continue;
            }
            $kind = 'serving';
            if (preg_match('~\b(elected|re-elected|reelected|won|sworn in|takes office|took office|unseated|appointed)\b~i', $sentence)) { $kind = 'elected'; }
            if (preg_match('~\b(resigned|stepped down|died|termed out|outgoing|last meeting|leaves the council|left the council|vacates?)\b~i', $sentence)) { $kind = 'left'; }
            $mentions[] = ['name' => $name, 'office' => preg_replace('~\s+~', ' ', $office), 'kind' => $kind,
                           'year' => $year, 'printed' => $printed, 'article' => $a, 'sentence' => trim($sentence)];
        }
    }
}

/* Group by person. */
$byPerson = [];
foreach ($mentions as $m) {
    $key = $norm($m['name']);
    $byPerson[$key]['name'] = $byPerson[$key]['name'] ?? $m['name'];
    $byPerson[$key]['hits'][] = $m;
}
uasort($byPerson, fn($a, $b) => count($b['hits']) <=> count($a['hits']));

echo 'articles naming the City Council: ' . $articles . PHP_EOL;
echo 'mentions of a person in an office: ' . count($mentions) . PHP_EOL;
echo 'distinct people named: ' . count($byPerson) . PHP_EOL;
echo 'dropped as another jurisdiction\'s office: ' . count($elsewhere);
if ($elsewhere) {
    $ex = array_slice(array_map(fn($e) => $e['name'] . ' (' . trim(preg_replace('~\s+~', ' ', $e['before'])) . ' ' . $e['office'] . ')', $elsewhere), 0, 4);
    echo ' — ' . implode('; ', $ex);
}
echo PHP_EOL . PHP_EOL;

$held = 0; $unheld = 0; $rows = [];
printf("%-26s %5s %6s %-11s %-9s %s\n", 'name', 'hits', 'years', 'span', 'record', 'what the articles show');
echo str_repeat('-', 108) . PHP_EOL;
foreach ($byPerson as $key => $p) {
    $years = array_values(array_unique(array_filter(array_map(fn($h) => $h['year'], $p['hits']))));
    sort($years);
    $kinds = array_count_values(array_map(fn($h) => $h['kind'], $p['hits']));
    $record = $people[$key] ?? null;
    if ($record) { $held++; } else { $unheld++; }
    $what = [];
    foreach (['elected' => 'elected', 'left' => 'left', 'serving' => 'serving'] as $k => $lab) {
        if (!empty($kinds[$k])) { $what[] = $lab . ' ×' . $kinds[$k]; }
    }
    printf("%-26s %5d %6d %-11s %-9s %s\n", mb_substr($p['name'], 0, 26), count($p['hits']), count($years),
        $years ? (min($years) . '-' . max($years)) : '?', $record ? '#' . $record->id : 'none', implode(', ', $what));
    $rows[$key] = ['p' => $p, 'years' => $years, 'kinds' => $kinds, 'record' => $record];
}

echo PHP_EOL . 'already held as person records: ' . $held . ' | not held: ' . $unheld . PHP_EOL;

/* Coverage across the span. */
$byYear = [];
foreach ($mentions as $m) { if ($m['year']) { $byYear[$m['year']][$norm($m['name'])] = true; } }
ksort($byYear);
echo PHP_EOL . 'coverage, 1987 to 2026:' . PHP_EOL;
$blank = [];
for ($y = 1987; $y <= 2026; $y++) {
    $n = isset($byYear[$y]) ? count($byYear[$y]) : 0;
    if ($n === 0) { $blank[] = $y; }
}
foreach ($byYear as $y => $names) { printf("   %d  %s\n", $y, str_repeat('#', min(count($names), 40)) . ' ' . count($names)); }
echo PHP_EOL . 'years with nobody named in an office: ' . count($blank) . ' of 40' . PHP_EOL;
echo '   ' . implode(', ', $blank) . PHP_EOL;

/* The report. */
$out = ['# The City Council, from what the archive holds', '',
    'Generated by scripts/import/survey_council_terms.php on ' . date('Y-m-d H:i') . '. Read only.',
    '',
    'Articles naming the City Council: **' . $articles . '**. Mentions of a person in an office: **'
        . count($mentions) . '**. Distinct people: **' . count($byPerson) . '**, of whom **' . $held
        . '** are already person records and **' . $unheld . '** are not.',
    '',
    '| person | hits | span | record | elected | left | serving |', '|---|---:|---|---|---:|---:|---:|'];
foreach ($rows as $r) {
    $out[] = '| ' . $r['p']['name'] . ' | ' . count($r['p']['hits']) . ' | '
        . ($r['years'] ? min($r['years']) . '–' . max($r['years']) : '?') . ' | '
        . ($r['record'] ? '#' . $r['record']->id : '—') . ' | '
        . ($r['kinds']['elected'] ?? 0) . ' | ' . ($r['kinds']['left'] ?? 0) . ' | ' . ($r['kinds']['serving'] ?? 0) . ' |';
}
$out[] = '';
$out[] = '## What each mention says';
$out[] = '';
foreach ($rows as $r) {
    $out[] = '### ' . $r['p']['name'] . ($r['record'] ? ' — person #' . $r['record']->id : ' — no person record');
    $out[] = '';
    foreach (array_slice($r['p']['hits'], 0, 8) as $h) {
        $out[] = '- **' . ($h['printed'] ?: ($h['year'] ?: 'undated')) . '** (' . $h['kind'] . ', "' . $h['office'] . '") '
            . '[' . $h['article']->title . '](' . $h['article']->url . ')  ';
        $out[] = '  > ' . mb_substr($h['sentence'], 0, 300);
    }
    if (count($r['p']['hits']) > 8) { $out[] = '- … and ' . (count($r['p']['hits']) - 8) . ' more'; }
    $out[] = '';
}
$out[] = '## Years with nobody named in an office';
$out[] = '';
$out[] = implode(', ', $blank) ?: 'none';
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: review/council-roster.md' . PHP_EOL;
