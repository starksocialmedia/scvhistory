/**
 * Builds the era and theme review queue: web/review/eras.json.
 *
 * WHY THIS IS A QUEUE AND NOT A DERIVATION
 *
 * Deriving the era from a date was tried and does not work. The only date an
 * article carries is when it was printed, and against the nineteen articles
 * that have an era set by hand, the publication year falls inside that era
 * ZERO times out of nineteen: Reynolds wrote about Spanish Colonial in 1976.
 * The era describes the subject and the date describes the printing.
 *
 * Deriving it from the years mentioned in the text does better and is still not
 * good enough: on the six of those nineteen it was willing to commit to, it got
 * four right and two wrong, because a history chapter names the period before
 * the one it is about and lands early.
 *
 * Two thirds is a fine basis for a suggestion and a poor basis for a write. So
 * this proposes, with the evidence in view, and a person rules -- the same
 * shape as the records queue.
 *
 * WHAT EACH ROW CARRIES
 *
 *   years        every four-digit year in the title and body, minus the year it
 *                was printed in, which is about the printing
 *   eraGuess     the era whose span contains all of them, where exactly one
 *                does. Where several do, all of them, as a question
 *   eraWhy       the sentence the earliest and latest year came from, so the
 *                guess can be checked against the prose rather than a number
 *   themes       every theme whose pattern the text matches, each with the
 *                sentence that matched, because a theme asserted with no
 *                sentence behind it is a guess wearing a label
 *
 * Read only. Writes one JSON file and changes nothing.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_era_queue.php'))"
 */

$OUT = \Craft::getAlias('@webroot') . '/review/eras.json';

/* The same patterns the theme list was measured with. Kept here rather than in
   the schema script because this is the file that has to stay true to the
   corpus; the schema script only creates the terms once. */
$THEME_PATTERNS = [
    'Incorporation & Cityhood'  => '~incorporat|cityhood|city council|city of santa clarita~i',
    'Schools & Education'       => '~school district|superintendent|high school|elementary|\bpupils\b~i',
    'Film & Television'         => '~\b(movie|film|studio|western|location shoot|melody ranch|filmed)\b~i',
    'Railroad'                  => '~\brailroad|railway|southern pacific|depot|locomotive|\btrain\b~i',
    'Ranching & Agriculture'    => '~\brancho\b|ranching|cattle|orchard|crops|farming|livestock~i',
    'Mining & Gold'             => '~gold (rush|discovery|mining)|\bmine\b|placer|prospector~i',
    'Development & Growth'      => '~subdivision|shopping (mall|center)|master.planned|tract homes|developer~i',
    'Oil'                       => '~\boil (well|field|company|boom|derrick)|petroleum|refinery|drilling~i',
    'Water & Aqueduct'          => '~aqueduct|water (district|company|supply)|reservoir|castaic lake~i',
    'Stagecoach & Early Roads'  => '~stagecoach|stage (station|route|line)|butterfield|beale\'?s cut~i',
    'Native Peoples'            => '~tataviam|chumash|native (american|people)|indian village~i',
    'Fire & Flood'              => '~\bwildfire|brush fire|\bflood(ing|ed|s)?\b|firestorm~i',
    'Aerospace'                 => '~aerospace|rocket|santa susana|jet propulsion|missile~i',
    'St. Francis Dam'           => '~st\.?\s*francis dam|dam disaster|dam collapse~i',
    'Northridge Earthquake'     => '~northridge (earthquake|quake)|1994 (earthquake|quake)~i',
];

/* Ranges are parsed from the category titles, so this works before the
   partition is applied and after it. Until it is applied the overlaps are real
   and show up as several candidates, which is the honest thing to show. */
$eras = [];
foreach (\craft\elements\Category::find()->group('historicalEra')->status(null)->limit(null)->all() as $c) {
    $t = (string)$c->title;
    $from = null; $to = null;
    if (preg_match('~\(\s*(?:to|before)\s*(\d{4})~i', $t, $m)) { $from = 0; $to = (int)$m[1]; }
    elseif (preg_match('~\(\s*Pre-(\d{4})~i', $t, $m)) { $from = 0; $to = (int)$m[1] - 1; }
    elseif (preg_match('~\((\d{4})\s*[–-]\s*(\d{4})~u', $t, $m)) { $from = (int)$m[1]; $to = (int)$m[2]; }
    elseif (preg_match('~\((\d{4})\s*[–-]\s*(present|now)~iu', $t, $m)) { $from = (int)$m[1]; $to = 2100; }
    if ($from === null) { continue; }
    $eras[] = ['id' => $c->id, 'title' => $t, 'from' => $from, 'to' => $to];
}
usort($eras, fn($a, $b) => $a['from'] <=> $b['from']);
echo 'eras parsed: ' . count($eras) . PHP_EOL;

$themeCats = [];
foreach (\craft\elements\Category::find()->group('theme')->status(null)->limit(null)->all() as $c) {
    $themeCats[(string)$c->title] = $c->id;
}
echo 'theme categories: ' . count($themeCats) . ($themeCats ? '' : ' (not applied yet; ids will be empty)') . PHP_EOL;

$sentencesOf = function (string $body): array {
    $b = preg_replace('~\s+~', ' ', strip_tags($body));
    return preg_split('~(?<=[.!?])\s+~', $b, -1, PREG_SPLIT_NO_EMPTY) ?: [];
};

$rows = []; $counts = ['one' => 0, 'several' => 0, 'spans' => 0, 'none' => 0];
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->orderBy('title asc')->limit(null)->all() as $a) {
    $h = [];
    foreach ($a->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; }

    $pub = isset($h['originalPublishDateEdtf']) ? trim((string)$a->originalPublishDateEdtf) : '';
    $pubY = preg_match('~(\d{4})~', $pub, $m) ? (int)$m[1] : null;

    $text = (string)$a->title . '. ' . (string)$a->body;
    $sents = $sentencesOf($text);

    preg_match_all('~\b(1[5-9]\d{2}|20[0-2]\d)\b~', preg_replace('~\s+~', ' ', strip_tags($text)), $mm);
    $years = array_values(array_unique(array_map('intval', $mm[1])));
    if ($pubY !== null) { $years = array_values(array_filter($years, fn($y) => $y !== $pubY)); }
    sort($years);

    $cands = [];
    if ($years) {
        $lo = $years[0]; $hi = $years[count($years) - 1];
        $cands = array_values(array_filter($eras, fn($e) => $lo >= $e['from'] && $hi <= $e['to']));
    }
    $kind = !$years ? 'none' : (count($cands) === 1 ? 'one' : (count($cands) > 1 ? 'several' : 'spans'));
    $counts[$kind]++;

    /* The sentence each end of the range came from. A number on its own does
       not let anyone check the guess. */
    $why = [];
    foreach ([$years[0] ?? null, $years[count($years) - 1] ?? null] as $y) {
        if ($y === null) { continue; }
        foreach ($sents as $s) {
            if (str_contains($s, (string)$y)) { $why[$y] = mb_substr(trim($s), 0, 260); break; }
        }
    }

    $themes = [];
    foreach ($THEME_PATTERNS as $t => $re) {
        $ev = '';
        foreach ($sents as $s) { if (preg_match($re, $s)) { $ev = mb_substr(trim($s), 0, 240); break; } }
        if ($ev === '') { continue; }
        $themes[] = ['name' => $t, 'id' => $themeCats[$t] ?? null, 'why' => $ev];
    }

    $curEra = isset($h['historicalEra']) ? $a->historicalEra->one() : null;
    $curThemes = isset($h['articleThemes'])
        ? array_map(fn($c) => (string)$c->title, $a->articleThemes->all()) : [];

    $rows[] = [
        'id' => $a->id,
        'title' => (string)$a->title,
        'url' => (string)$a->url,
        'published' => $pub,
        'years' => $years,
        'span' => $years ? $years[0] . '–' . $years[count($years) - 1] : '',
        'kind' => $kind,
        'eraCandidates' => array_map(fn($e) => ['id' => $e['id'], 'title' => $e['title']], $cands),
        'eraWhy' => $why,
        'themes' => $themes,
        'currentEra' => $curEra ? ['id' => $curEra->id, 'title' => (string)$curEra->title] : null,
        'currentThemes' => $curThemes,
    ];
}

/* Most decidable first: one candidate, then several, then the rest. Within
   each, the ones with the most themes, because a rich row is quick to judge. */
$ord = ['one' => 0, 'several' => 1, 'spans' => 2, 'none' => 3];
usort($rows, fn($a, $b) => [$ord[$a['kind']], -count($a['themes'])]
                       <=> [$ord[$b['kind']], -count($b['themes'])]);

file_put_contents($OUT, json_encode([
    'generated' => (new DateTime())->format('c'),
    'generated_by' => 'scripts/import/export_era_queue.php',
    'eras' => $eras,
    'themes' => array_keys($THEME_PATTERNS),
    'themeIds' => $themeCats,
    'counts' => $counts,
    'note' => 'eraCandidates is every era whose span contains all the years found, after the '
        . 'publication year is removed. One candidate is a suggestion, not an answer; several '
        . 'is a question. Nothing here is written to any record.',
    'articles' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
@chmod($OUT, 0644);

echo PHP_EOL;
foreach ($counts as $k => $v) { printf("   %-10s %d\n", $k, $v); }
printf("   %-10s %d\n", 'themed', count(array_filter($rows, fn($r) => $r['themes'])));
echo PHP_EOL . 'wrote ' . $OUT . PHP_EOL;
