/**
 * The 1992 Ruiz Cemetery census: a table on the place, records for the few.
 *
 * Three things happen here, and the split between them is the decision this
 * script exists to carry out.
 *
 * 1. Every numbered plot becomes a row in graveCensus on Ruiz Cemetery. The
 *    census is a document and it stays one document.
 *
 * 2. Twelve of those plots also become person records: the eight who died on
 *    13 March 1928 when the St. Francis Dam failed, Niebes Ruiz, and the three
 *    LeBruns. The reason a record exists is that there is something to say
 *    about the person, and for these twelve there is. Everyone else stays a
 *    table row until an article, an image or another source gives them a
 *    second fact.
 *
 * 3. The place body gains a paragraph saying where the table came from and how
 *    far it can be trusted.
 *
 * ---------------------------------------------------------------- the dates
 *
 * This text is an OCR of a scanned 1992 typescript, and the OCR is bad. It
 * turns "Mar. 13, 1928" into "Mar . 1 3 , 1 928" and, twice, into
 * "Mar. 133, 1928". The rule followed everywhere below:
 *
 *   A date is parsed only where it can be read without guessing. Stray spaces
 *   inside a number are closed up, because closing them invents nothing. A day
 *   of 133 is not repaired, a two-digit year is not given a century, and a row
 *   with a single date is not assigned to birth or death by feel. In every
 *   such case the printed column keeps the source exactly and the parsed
 *   column stays empty, with the reason in Note.
 *
 * The same rule governs the person records. birthDate and deathDate are only
 * set from a date that parsed; the raw string always survives in recordDates,
 * where printed holds the scan, iso holds the parse or nothing, and confirmed
 * is false on every row. Nothing here has been checked against a stone.
 *
 * Two people get a death date the census could not give: Raymond C. Ruiz and
 * his sister, from Worden, who reports all six Ruiz dead that night. The
 * Erratchuos are not in Worden's count and do not get one.
 *
 * ---------------------------------------------------------------- the family
 *
 * Worden: "Parents Rosaria and Enrique perished, as did their four children,
 * ages eight to thirty." He does not name the children. The census gives, in
 * plots 01 to 06, exactly six Ruiz dead on that date: two adults born 1864 and
 * 1875, and four born 1898, 1908, 1917 and 1920, who were 29, 19, 11 and 8.
 * Worden's "eight to thirty" is those four. That is why childOf is set on them
 * and not on plot 07, whose two Erratchuos died the same night in the next
 * plot but are not in anybody's count of the family.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Needs graveCensus to exist: run add_grave_census_field.php first.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_ruiz_census.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$PLACE_SLUG   = 'ruiz-cemetery';
$SOURCE_FILE  = 'inventory/legacy/loose-pages.json';
$CENSUS_KEY   = 'scvhs_ruizcemeterycensus1992';
$CENSUS_FIELD = 'graveCensus';
$BURIAL       = 'Ruiz Cemetery, San Francisquito Canyon';
$COMMUNITY    = 'san-francisquito-canyon';

/* Only overwrite a body that is still the one this script wrote, or that has
   never had the note. Nathan's prose is never replaced. */
$NOTE_MARK = 'The grave table on this record is transcribed from';

$NOTE =
    "The grave table on this record is transcribed from the Ruiz Cemetery census of the "
    . "Santa Clarita Valley Historical Society, an Eagle Scout project led by Kyle Fotheringham "
    . "of Troop 497, Newhall, recorded in the summer of 1992 and dated 24 August 1992. The "
    . "archive holds it as a scan of the original typescript, and the table here is read from "
    . "that scan by machine. The transcription from the stones is Fotheringham's; the reading "
    . "of his typescript is not, and it is imperfect. Dates are given twice, as the page prints "
    . "them and as they parse, and a date that could not be read without guessing has been left "
    . "unparsed rather than repaired. Every date should be checked against the stone before it "
    . "is relied on.";

$root = \Craft::getAlias('@root');
$elements = Craft::$app->getElements();

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/* ---------------------------------------------- no family graph of the living

   A kinship relation is written only between two historical people. The site
   will not publish one otherwise (_partials/record/historical.twig), and a
   relation that is stored but can never be shown is a living person's family
   structure held in the archive for no reason. So the refusal happens here,
   before the write, not after it in a template.

   The test is the template's, in PHP: historical is a war memorial casualty, a
   death date with a year in it, a live obituary, or a birth year more than 120
   years ago. It FAILS CLOSED: undetermined is living.

   It is run twice. In the plan, against the dates this script is about to
   write, so a dry run shows which relations would be refused. And at write
   time, against the saved records as they read back from the database, which
   is what actually decides: a record this script found already existing may
   not carry the dates the plan assumed. */
$historical = function (\craft\elements\Entry $e) use ($hasField): array {
    if ($e->section && $e->section->handle === 'warMemorials') { return [true, 'war memorial casualty']; }
    foreach (['deathDate', 'deathDateEdtf', 'mpDateOfDeath'] as $h) {
        if ($hasField($e, $h) && preg_match('~\d{3}~', (string)$e->getFieldValue($h))) { return [true, $h]; }
    }
    if ($hasField($e, 'personObituaries') && $e->personObituaries->exists()) { return [true, 'obituary']; }
    if (\craft\elements\Entry::find()->section('obituaries')
        ->relatedTo(['targetElement' => $e, 'field' => 'obitSubject'])->exists()) { return [true, 'obituary subject']; }
    foreach (['birthDateEdtf', 'birthDate', 'wmDateOfBirth', 'mpDateOfBirth'] as $h) {
        if ($hasField($e, $h) && preg_match('~\b(1[5-9]\d\d|20\d\d)\b~', (string)$e->getFieldValue($h), $m)) {
            return (int)$m[1] <= (int)date('Y') - 120
                ? [true, 'born ' . $m[1]] : [false, 'born ' . $m[1] . ', no death date'];
        }
    }
    return [false, 'undetermined: no death date, no obituary, no birth year'];
};
/* The same test against a plan row, before anything is saved. */
$plannedHistorical = function (array $p): array {
    if (($p['deathDate'] ?? '') !== '' && preg_match('~\d{3}~', $p['deathDate'])) { return [true, 'deathDate ' . $p['deathDate']]; }
    if (preg_match('~\b(1[5-9]\d\d|20\d\d)\b~', (string)($p['birthDate'] ?? ''), $m) && (int)$m[1] <= (int)date('Y') - 120) {
        return [true, 'born ' . $m[1]];
    }
    return [false, 'undetermined in the plan: no death date and no birth year before ' . ((int)date('Y') - 120)];
};
$refused = [];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

/* ------------------------------------------------------------------ sources */

$path = $root . '/' . $SOURCE_FILE;
if (!file_exists($path)) {
    echo 'not found: ' . $SOURCE_FILE . PHP_EOL;
    echo 'Bring it across with:' . PHP_EOL;
    echo '  git checkout origin/grok-bot -- ' . $SOURCE_FILE . PHP_EOL;
    return;
}
$doc = json_decode(file_get_contents($path), true);
$page = null;
foreach ($doc['pages'] ?? [] as $p) {
    if (($p['legacy_key'] ?? '') === $CENSUS_KEY) { $page = $p; break; }
}
if (!$page) { echo 'the census page is not in ' . $SOURCE_FILE . PHP_EOL; return; }

$place = \craft\elements\Entry::find()->section('places')->slug($PLACE_SLUG)->status(null)->one();
if (!$place) { echo 'place ' . $PLACE_SLUG . ' NOT FOUND' . PHP_EOL; return; }
echo 'place: ' . $place->title . ' #' . $place->id . PHP_EOL;

if (!$hasField($place, $CENSUS_FIELD)) {
    echo PHP_EOL . 'the place type does not carry ' . $CENSUS_FIELD . '.' . PHP_EOL;
    echo 'Run scripts/import/add_grave_census_field.php first. Parsing the census anyway' . PHP_EOL;
    echo 'so the reading can be checked, but nothing would be written to the table.' . PHP_EOL . PHP_EOL;
}

/* ------------------------------------------------------------- date reading */

/* Both languages. The typescript slips into Spanish for several of the older
   graves: "Febrero 25,1888", "Agosto 10, 1910". A three-letter key covers each
   month in both, since Marzo and Mar, Julio and Jul, Febrero and Feb all agree
   on their first three letters and the four that do not are listed as well. */
$MONTHS = [
    'jan' => 1, 'ene' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'abr' => 4,
    'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8, 'ago' => 8, 'sep' => 9,
    'oct' => 10, 'nov' => 11, 'dec' => 12, 'dic' => 12,
];

/* A month is recognised in one of two shapes, and the split is not pedantry.
   Plot 49 reads "Lebrun August 1884 1924", where August is the man's name; a
   rule that took any month word ate his name and both his dates. In this
   typescript English months are always abbreviated with a stop or a comma
   after them, and the only full English month words used are June and July.
   So an abbreviation must be followed by a stop or comma, and the full-word
   list contains only the forms that actually occur, which leaves August,
   March, April and January free to be names.

   May is in the full-word list, for "May 1875" at plot 12. Nobody in this
   census is named May; if one turns up in another cemetery, this is the line
   that will need looking at.

   Longest first in each alternation, or Mayo matches as May and leaves an o. */
$MONTH_ABBR = 'sept|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec';
$MONTH_WORD = 'septiembre|noviembre|diciembre|febrero|octubre|agosto|marzo|abril|enero|junio|julio|mayo|june|july|may';

/* "1 928" is one number the scan broke, not two. Closing a gap between digits
   invents nothing; every other repair would. */
$digits = fn(string $s): string => preg_replace('~\D~', '', $s);

/**
 * Reads one date span. Returns [iso, granularity, problem].
 * A non-empty problem means the span was left unparsed on purpose.
 */
$readDate = function (string $raw) use ($MONTHS, $digits, $MONTH_ABBR, $MONTH_WORD): array {
    $s = trim($raw);

    /* 2/24/60. The century is not in the source and will not be invented. */
    if (preg_match('~^\s*(\d{1,2})\s*/\s*(\d{1,2})\s*/\s*(\d{2,4})\s*$~', $s, $m)) {
        if (strlen($m[3]) === 4) {
            $y = (int)$m[3]; $mo = (int)$m[1]; $d = (int)$m[2];
            if ($mo >= 1 && $mo <= 12 && $d >= 1 && $d <= 31) {
                return [sprintf('%04d-%02d-%02d', $y, $mo, $d), 'day', ''];
            }
        }
        return ['', '', 'two-digit year, the century is not in the source'];
    }

    /* A bare year. */
    if (preg_match('~^\s*(1[6-9]\d{2}|20\d{2})\s*$~', $s, $m)) {
        return [$m[1], 'year', ''];
    }

    /* Month, then whatever digits, spaces, commas and stops follow it. */
    if (preg_match('~^\s*(' . $MONTH_WORD . '|' . $MONTH_ABBR . ')\s*[.,]?\s*(.*)$~iu', $s, $m)) {
        $mo = $MONTHS[strtolower(substr($m[1], 0, 3))] ?? 0;
        $tail = trim($m[2]);
        if ($mo === 0) { return ['', '', 'month not recognised']; }
        if ($tail === '') { return ['', '', 'month with no year']; }

        /* The year is after the last comma, or the last run of digits. */
        $day = ''; $year = '';
        if (str_contains($tail, ',')) {
            $at = strrpos($tail, ',');
            $day = $digits(substr($tail, 0, $at));
            $year = $digits(substr($tail, $at + 1));
        } else {
            $parts = preg_split('~\s+~', $tail);
            $year = $digits(array_pop($parts));
            $day = $digits(implode('', $parts));
        }

        if (strlen($year) !== 4 || (int)$year < 1600 || (int)$year > 2100) {
            return ['', '', 'the year does not read as a year'];
        }
        if ($day === '') {
            return [sprintf('%04d-%02d', (int)$year, $mo), 'month', ''];
        }
        if (strlen($day) > 2 || (int)$day < 1 || (int)$day > 31) {
            return ['', '', 'the day reads "' . $day . '", which is not a day of a month'];
        }
        return [sprintf('%04d-%02d-%02d', (int)$year, $mo, (int)$day), 'day', ''];
    }

    return ['', '', 'not a date this script recognises'];
};

/* ----------------------------------------------------------- the row parser */

$body = (string)$page['body_text'];
$at = strpos($body, 'Embedded census transcription');
if ($at === false) { echo 'the embedded transcription marker is not in the body' . PHP_EOL; return; }
$lines = preg_split('~\R~u', substr($body, $at));

/* The typescript's own furniture, repeated at the top of each scanned page. */
$FURNITURE = '~^(page no\.|\d{2}/\d{2}/\d{2}$|ruiz cemetery$|recorded tombstone|saugus, cali|'
    . 'recorded summer|map#|attached$|--- census page|=== )~i';

$rows = []; $skipped = 0; $orphanYes = 0;
foreach ($lines as $line) {
    $s = trim($line);
    if ($s === '') { continue; }
    if (preg_match($FURNITURE, $s)) { continue; }
    /* The Flip PDF page rail: a column of single digits down the side. */
    if (preg_match('~^\d$~', $s)) { continue; }
    /* A YES on a line of its own. The scan reads the map-number column and the
       comments column in separate passes and they come out interleaved, so
       which plot one of these belongs to is genuinely lost: the YES after plot
       20 cannot be 20's, because 20 already has one on its own line. Counted
       and reported rather than attached to whichever row happens to be next. */
    if (strcasecmp($s, 'YES') === 0) { $orphanYes++; continue; }
    if (!preg_match('~^(\d{1,2}[ab]?)(?![\d/])\s*(.*)$~', $s, $m)) { $skipped++; continue; }
    $rows[] = ['map' => $m[1], 'rest' => trim($m[2])];
}

echo 'rows read from the transcription: ' . count($rows) . PHP_EOL;
if ($orphanYes) {
    echo $orphanYes . ' comments marks sit on a line of their own, with the plot they belong to lost' . PHP_EOL;
    echo 'in the scan. Those plots keep an unticked box and say so in Note; set them by hand' . PHP_EOL;
    echo 'from the original if it matters.' . PHP_EOL;
}

/* ------------------------------------------------------- field by field */

$PARTICLES = ['de', 'del', 'la', 'las', 'los', 'van', 'von', 'da', 'di', 'oe'];

$parsed = [];
$problems = [];
foreach ($rows as $r) {
    $rest = $r['rest'];
    $note = [];

    /* The census's own last column. The header runs "MILITARY Comments
       ATTACHED" over two lines and the YES belongs to the comments: plot 01 is
       an eight-year-old girl and plot 53 an eight-month-old boy, both YES. */
    $commentsSheet = false;
    if (preg_match('~\bYES\b\s*$~i', $rest)) {
        $commentsSheet = true;
        $rest = trim(preg_replace('~(\s*\bYES\b)+\s*$~i', '', $rest));
    }
    $rest = trim($rest);
    if ($rest === '.') { $rest = ''; }

    if ($rest === '' || strcasecmp($rest, 'Memorial') === 0) {
        $parsed[] = [
            'map' => $r['map'], 'last' => '', 'first' => '', 'middle' => '',
            'birthPrinted' => '', 'birthIso' => '', 'deathPrinted' => '', 'deathIso' => '',
            'spouse' => '', 'commentsSheet' => $commentsSheet,
            'note' => $rest === ''
                ? ($commentsSheet ? '' : 'Empty plot in the census, or a stone that could not be read. '
                    . 'The scan also leaves some comments marks unattached, so an unticked box here is not a no.')
                : 'The census reads "Memorial" here rather than a name.',
        ];
        continue;
    }

    /* Find the date spans, then everything before the first one is the name. */
    $work = $rest;
    $found = [];

    /* A month, then exactly as much of what follows as belongs to it. Matching
       the tail with a regex is what swallowed "May 1875 1974" whole at plot 12,
       so the tail is walked here instead: digits, spaces, commas and stops up
       to the next letter, and then cut at the year. */
    $eat = function (string $subject) use (&$found, $digits): string {
        $out = $subject;
        foreach (['~\b(' . $GLOBALS['_MONTH_WORD'] . ')\b~iu',
                  '~\b(' . $GLOBALS['_MONTH_ABBR'] . ')\s*[.,]~iu'] as $monthPattern) {
            while (preg_match($monthPattern, $out, $m, PREG_OFFSET_CAPTURE)) {
                $start = $m[0][1];
                $after = $start + strlen($m[0][0]);
                $n = strlen($out);
                $j = $after;
                while ($j < $n && (ctype_digit($out[$j]) || $out[$j] === ' ' || $out[$j] === ',' || $out[$j] === '.')) { $j++; }
                $tail = substr($out, $after, $j - $after);

                $take = 0;
                if (str_contains($tail, ',')) {
                    /* day, then the year after the last comma, four digits of it */
                    $at = strrpos($tail, ',');
                    $acc = ''; $k = $at + 1;
                    while ($k < strlen($tail) && strlen($acc) < 4) {
                        $c = $tail[$k];
                        if (ctype_digit($c)) { $acc .= $c; }
                        elseif ($c !== ' ') { break; }
                        $k++;
                    }
                    $take = $k;
                } else {
                    /* whitespace groups. A first group of four digits is the
                       year and the date ends there; anything shorter is the day
                       and the next group is the year. */
                    preg_match_all('~\d+~', $tail, $gm, PREG_OFFSET_CAPTURE);
                    $g = $gm[0];
                    if (!$g) { $take = 0; }
                    elseif (strlen($g[0][0]) >= 4) { $take = $g[0][1] + strlen($g[0][0]); }
                    elseif (count($g) >= 2) { $take = $g[1][1] + strlen($g[1][0]); }
                    else { $take = $g[0][1] + strlen($g[0][0]); }
                }

                $span = rtrim(substr($out, $start, ($after - $start) + $take), ' ,.');
                $found[] = ['text' => trim($span), 'at' => $start];
                $out = substr($out, 0, $start) . str_repeat("\x02", strlen($span))
                     . substr($out, $start + strlen($span));
            }
        }
        return $out;
    };
    $GLOBALS['_MONTH_WORD'] = $MONTH_WORD;
    $GLOBALS['_MONTH_ABBR'] = $MONTH_ABBR;
    $work = $eat($work);

    foreach (['~\b\d{1,2}\s*/\s*\d{1,2}\s*/\s*\d{2,4}\b~', '~\b(?:1[6-9]\d{2}|20\d{2})\b~'] as $pat) {
        while (preg_match($pat, $work, $m, PREG_OFFSET_CAPTURE)) {
            $found[] = ['text' => trim($m[0][0]), 'at' => $m[0][1]];
            $work = substr($work, 0, $m[0][1]) . str_repeat("\x02", strlen($m[0][0]))
                  . substr($work, $m[0][1] + strlen($m[0][0]));
        }
    }

    usort($found, fn($a, $b) => $a['at'] <=> $b['at']);
    $ordered = array_column($found, 'text');

    /* Names sit before the first masked span; the spouse after the last. */
    $firstMask = strpos($work, "\x02");
    $namePart = trim($firstMask === false ? $work : substr($work, 0, $firstMask));
    $lastMask = strrpos($work, "\x02");
    $spousePart = $lastMask === false ? '' : trim(str_replace("\x02", '', substr($work, $lastMask)));
    $spousePart = trim(trim($spousePart), ',');

    /* Last, first, middle. A particle joins the surname; "De Lelong" is one
       name. A token with no letters in it is scan damage, not a name. */
    $tokens = preg_split('~\s+~', $namePart, -1, PREG_SPLIT_NO_EMPTY);
    $damaged = false;
    foreach ($tokens as $t) { if (!preg_match('~\p{L}~u', $t)) { $damaged = true; } }
    if ($damaged) {
        $note[] = 'The name as scanned reads "' . $namePart . '" and has been split as best it can be.';
        $tokens = array_values(array_filter($tokens, fn($t) => preg_match('~\p{L}~u', $t)));
    }
    $last = ''; $first = ''; $middle = '';
    if ($tokens) {
        $last = array_shift($tokens);
        if ($tokens && in_array(mb_strtolower(rtrim($last, '.')), $PARTICLES, true)) {
            $last .= ' ' . array_shift($tokens);
        }
        if ($tokens) { $first = array_shift($tokens); }
        if ($tokens) { $middle = implode(' ', $tokens); }
    }

    /* Birth then death, and only when there are two. A single date sits in a
       column the scan has lost, and calling it one or the other is a guess. */
    $birthPrinted = ''; $deathPrinted = '';
    if (count($ordered) >= 2) {
        $birthPrinted = $ordered[0];
        $deathPrinted = $ordered[1];
        if (count($ordered) > 2) {
            $note[] = 'The row carries ' . count($ordered) . ' dates: '
                . implode(' / ', $ordered) . '. The first two have been taken as birth and death.';
        }
    } elseif (count($ordered) === 1) {
        $note[] = 'The census gives one date here, "' . $ordered[0]
            . '", and the scan has lost which column it sat in. Left unassigned.';
    }

    [$birthIso, $birthGran, $birthProblem] = $birthPrinted !== '' ? $readDate($birthPrinted) : ['', '', ''];
    [$deathIso, $deathGran, $deathProblem] = $deathPrinted !== '' ? $readDate($deathPrinted) : ['', '', ''];
    if ($birthProblem !== '') { $note[] = 'Birth "' . $birthPrinted . '" not parsed: ' . $birthProblem . '.'; }
    if ($deathProblem !== '') { $note[] = 'Death "' . $deathPrinted . '" not parsed: ' . $deathProblem . '.'; }

    $row = [
        'map' => $r['map'], 'last' => $last, 'first' => $first, 'middle' => $middle,
        'birthPrinted' => $birthPrinted, 'birthIso' => $birthIso,
        'deathPrinted' => $deathPrinted, 'deathIso' => $deathIso,
        'spouse' => $spousePart, 'commentsSheet' => $commentsSheet,
        'note' => implode(' ', $note),
    ];
    $row['_birthGran'] = $birthGran;
    $row['_deathGran'] = $deathGran;
    $parsed[] = $row;
    if ($note) { $problems[] = $row; }
}

/* Deduplicate on the map number: the page rail and the date stamp can both
   produce a stray match, and a plot appears once. */
$byMap = [];
foreach ($parsed as $row) {
    $k = $row['map'];
    if (isset($byMap[$k])) {
        /* Keep whichever row says more. */
        $a = strlen($byMap[$k]['last'] . $byMap[$k]['birthPrinted'] . $byMap[$k]['deathPrinted']);
        $b = strlen($row['last'] . $row['birthPrinted'] . $row['deathPrinted']);
        if ($b <= $a) { continue; }
    }
    $byMap[$k] = $row;
}
uksort($byMap, function ($a, $b) {
    $na = (int)$a; $nb = (int)$b;
    return $na === $nb ? strcmp($a, $b) : $na <=> $nb;
});

$named = array_filter($byMap, fn($r) => $r['last'] !== '');
echo 'plots: ' . count($byMap) . ', of which ' . count($named) . ' carry a name' . PHP_EOL;
echo str_repeat('-', 78) . PHP_EOL;
printf("%-5s %-13s %-11s %-6s %-19s %-11s %-19s %-11s %-11s %s\n",
    'Map', 'Last', 'First', 'Mid', 'Birth as printed', 'parsed', 'Death as printed', 'parsed', 'Spouse', 'C');
foreach ($byMap as $r) {
    printf("%-5s %-13s %-11s %-6s %-19s %-11s %-19s %-11s %-11s %s\n",
        $r['map'], mb_substr($r['last'], 0, 13), mb_substr($r['first'], 0, 11), mb_substr($r['middle'], 0, 6),
        mb_substr($r['birthPrinted'], 0, 19), $r['birthIso'] ?: '-',
        mb_substr($r['deathPrinted'], 0, 19), $r['deathIso'] ?: '-',
        mb_substr($r['spouse'], 0, 11), $r['commentsSheet'] ? 'y' : '');
}

if ($problems) {
    echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
    echo 'LEFT UNPARSED ON PURPOSE, ' . count($problems) . ' rows. Each keeps its printed value.' . PHP_EOL;
    foreach ($problems as $r) {
        echo '  plot ' . str_pad($r['map'], 5) . trim($r['last'] . ' ' . $r['first']) . PHP_EOL;
        echo '        ' . $r['note'] . PHP_EOL;
    }
}

/* ------------------------------------------------------- who becomes a record */

/* The eight who died that night, then the two the archive already talks about.
   Each carries the sentence that justifies it, and nothing is here without one. */
$PEOPLE = [
    '06' => ['Enrique R. Ruiz', 'Enrique (Henry) R. Ruiz',
        'A farmer in San Francisquito Canyon, where five generations of the Ruiz family lived. '
        . 'He and his wife Rosaria died with four of their children when the St. Francis Dam failed '
        . 'in the small hours of 13 March 1928, and the family is buried together in the cemetery on '
        . 'the hill above the creekbed. The archive holds a photograph of Enrique and Rosaria at the '
        . 'Ruiz ranch. Sources: Leon Worden, "Spooky Happenings at Ruiz Cemetery", The Signal, '
        . '8 May 1996; Jerry Reynolds, Chapter 30; the 1992 Ruiz Cemetery census, plot 06.'],
    '05' => ['Rosaria P. Ruiz', 'Rosaria P. Ruiz',
        'Wife of Enrique Ruiz and mother of the four Ruiz children who died with them when the '
        . 'St. Francis Dam failed on 13 March 1928. The census gives her husband\'s name in its own '
        . 'spouse column. The archive holds a photograph of the two of them at the Ruiz ranch. '
        . 'Sources: Leon Worden, "Spooky Happenings at Ruiz Cemetery", The Signal, 8 May 1996; '
        . 'the 1992 Ruiz Cemetery census, plot 05.'],
    '04' => ['Mary S. Ruiz', 'Mary S. Ruiz',
        'The eldest of the four Ruiz children who died with their parents when the St. Francis Dam '
        . 'failed on 13 March 1928. She was twenty-nine. Worden gives the four children as aged '
        . 'eight to thirty without naming them; the census names them, in the four plots beside '
        . 'their parents. Sources: Leon Worden, The Signal, 8 May 1996; the 1992 Ruiz Cemetery '
        . 'census, plot 04.'],
    '03' => ['Martin F. Ruiz', 'Martin F. Ruiz',
        'One of the four Ruiz children who died with their parents when the St. Francis Dam failed '
        . 'on 13 March 1928. He was nineteen. Sources: Leon Worden, The Signal, 8 May 1996; the '
        . '1992 Ruiz Cemetery census, plot 03.'],
    '02' => ['Raymond C. Ruiz', 'Raymond C. Ruiz',
        'One of the four Ruiz children who died with their parents when the St. Francis Dam failed '
        . 'on 13 March 1928. He was eleven. The census gives his death as "Mar. 133, 1928", which is '
        . 'a damaged reading; the date here is Worden\'s, who reports all six of the family dead that '
        . 'night. Sources: Leon Worden, The Signal, 8 May 1996; the 1992 Ruiz Cemetery census, plot 02.'],
    '01' => ['Susana B. Ruiz', 'Susana B. Ruiz',
        'The youngest of the four Ruiz children who died with their parents when the St. Francis Dam '
        . 'failed on 13 March 1928. She was eight, and hers is the age Worden gives as the youngest '
        . 'of the four. Sources: Leon Worden, The Signal, 8 May 1996; the 1992 Ruiz Cemetery census, '
        . 'plot 01.'],
    '07a' => ['Rosarita A. Erratchuo', 'Rosarita A. Erratchuo',
        'Died on the night the St. Francis Dam failed, 13 March 1928, and is buried in plot 7 of the '
        . 'Ruiz cemetery beside the six Ruiz dead. The census gives her husband as James J. Erratchuo. '
        . 'Whether she was a Ruiz daughter is not settled: Worden counts four Ruiz children and the '
        . 'census names four in the plots beside their parents, which would leave her outside the '
        . 'family, and nothing found so far says either way. Sources: the 1992 Ruiz Cemetery census, '
        . 'plot 07a; Leon Worden, The Signal, 8 May 1996.'],
    '07b' => ['Roland T. Erratchuo', 'Roland T. Erratchuo',
        'An infant, born 5 January 1927, buried in plot 7 with Rosarita Erratchuo. The census gives '
        . 'his death as "Mar. 133, 1928", a damaged reading of the night the St. Francis Dam failed; '
        . 'the date has been left unparsed here rather than repaired, since unlike the six Ruiz he is '
        . 'not in any second account of the dead. Source: the 1992 Ruiz Cemetery census, plot 07b.'],
    '58' => ['Niebes Ruiz', 'Niebes Ruiz',
        'Remembered as the longest-lived of those buried at the Ruiz cemetery. Worden, walking the '
        . 'ground in 1996, wrote: "Remembered is Niebes Ruiz, who lived from 1794 to 1904." The '
        . 'census gives the same two years. Sources: Leon Worden, "Spooky Happenings at Ruiz '
        . 'Cemetery", The Signal, 8 May 1996; the 1992 Ruiz Cemetery census, plot 58.'],
    '48' => ['Constant LeBrun', 'Constant LeBrun',
        'The earliest burial the 1992 census records at the Ruiz cemetery. Worden reports that the '
        . 'oldest marker there dates to 1888 and that some graves may be older; 1888 is the year the '
        . 'census gives for this grave, and no other row is earlier, so the two are probably the same '
        . 'stone. The LeBruns held a homestead further up San Francisquito Canyon: President Benjamin '
        . 'Harrison signed a grant to Frank LeBrun on 5 December 1890, and the ranch was sold to the '
        . 'City of Los Angeles in 1922 and became the site of the St. Francis Dam. Sources: Leon '
        . 'Worden, The Signal, 8 May 1996; Jerry Reynolds, Chapter 64; the 1992 Ruiz Cemetery census, '
        . 'plot 48.'],
    '47' => ['Dennie LeBrun', 'Dennie LeBrun',
        'Buried at the Ruiz cemetery in San Francisquito Canyon, in the row of three LeBrun graves. '
        . 'The family held the homestead granted to Frank LeBrun in 1890, sold to the City of Los '
        . 'Angeles in 1922 and afterwards the site of the St. Francis Dam. Sources: Jerry Reynolds, '
        . 'Chapter 64; the 1992 Ruiz Cemetery census, plot 47.'],
    '49' => ['August LeBrun', 'Augustine C. ("Gus") LeBrun',
        'Known as Gus. The archive holds a photograph of his grave marker at the Ruiz cemetery and a '
        . 'copy of his death certificate, both filed under the dates the census gives. The family '
        . 'held the San Francisquito Canyon homestead granted to Frank LeBrun in 1890, sold to the '
        . 'City of Los Angeles in 1922. Sources: SCVHistory.com LP2401 and TLP2401; Jerry Reynolds, '
        . 'Chapter 64; the 1992 Ruiz Cemetery census, plot 49.'],
];

/* A death date the census could not give, on a second source's authority.
   Only the six Ruiz: Worden reports six of the family dead and no more. */
$DEATH_FROM_WORDEN = ['02'];

/* Stated by the census's own spouse column, not inferred. */
$SPOUSES = [['05', '06']];

/* Inferred, and the reasoning is in this script's header and in each body. */
$CHILDREN = ['01' => ['05', '06'], '02' => ['05', '06'], '03' => ['05', '06'], '04' => ['05', '06']];

/* The iso column is a date column, so it needs a whole date even when the
   stone gives only a year. The archive's convention, already in recordDates on
   the articles, is the first of the period with granularity carrying the
   truth: 1794 is stored as 1794-01-01 and read as a year. */
$wholeDate = function (string $iso): string {
    if ($iso === '') { return ''; }
    $p = explode('-', $iso);
    return sprintf('%s-%s-%s 00:00:00', $p[0], $p[1] ?? '01', $p[2] ?? '01');
};

$prettyIso = function (string $iso): string {
    if ($iso === '') { return ''; }
    $p = explode('-', $iso);
    $names = [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July',
              'August', 'September', 'October', 'November', 'December'];
    if (count($p) === 1) { return $p[0]; }
    if (count($p) === 2) { return $names[(int)$p[1]] . ' ' . $p[0]; }
    return $names[(int)$p[1]] . ' ' . (int)$p[2] . ', ' . $p[0];
};

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
echo 'PERSON RECORDS' . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$community = \craft\elements\Category::find()->group('neighborhood')->slug($COMMUNITY)->one();
if (!$community) { echo 'WARNING: community ' . $COMMUNITY . ' not found, neighborhood will be left empty' . PHP_EOL; }

$plan = [];
foreach ($PEOPLE as $map => [$title, $fullName, $prose]) {
    if (!isset($byMap[$map])) {
        echo 'plot ' . $map . ' is not in the parsed census. SKIPPED, nothing invented.' . PHP_EOL;
        continue;
    }
    $r = $byMap[$map];
    $existing = \craft\elements\Entry::find()->section('persons')->title($title)->status(null)->one();

    $birth = $prettyIso($r['birthIso']);
    $death = $prettyIso($r['deathIso']);
    $deathSource = $r['deathIso'] !== '' ? 'census' : '';
    if ($death === '' && in_array($map, $DEATH_FROM_WORDEN, true)) {
        $death = 'March 13, 1928';
        $deathSource = 'Worden';
    }

    $dates = [];
    if ($r['birthPrinted'] !== '') {
        $dates[] = ['printed' => $r['birthPrinted'], 'iso' => $wholeDate($r['birthIso']),
                    'granularity' => $r['_birthGran'] ?: 'year', 'confirmed' => false,
                    'label' => 'Born, as the 1992 Ruiz Cemetery census prints it (plot ' . $map . ')'];
    }
    if ($r['deathPrinted'] !== '') {
        $dates[] = ['printed' => $r['deathPrinted'], 'iso' => $wholeDate($r['deathIso']),
                    'granularity' => $r['_deathGran'] ?: 'year', 'confirmed' => false,
                    'label' => 'Died, as the 1992 Ruiz Cemetery census prints it (plot ' . $map . ')'];
    }

    $plan[$map] = [
        'title' => $title, 'fullName' => $fullName, 'body' => $prose,
        'birthDate' => $birth, 'deathDate' => $death, 'deathSource' => $deathSource,
        'burialPlace' => $BURIAL, 'recordDates' => $dates,
        'existing' => $existing, 'row' => $r,
    ];

    echo PHP_EOL . ($existing ? 'EXISTS #' . $existing->id . '  ' : 'NEW       ') . $title
        . '   (plot ' . $map . ')' . PHP_EOL;
    echo '   fullName     ' . $fullName . PHP_EOL;
    echo '   birthDate    ' . ($birth !== '' ? $birth : '(empty: ' . ($r['birthPrinted'] !== '' ? 'the census reading is damaged' : 'the census gives none') . ')') . PHP_EOL;
    echo '   deathDate    ' . ($death !== '' ? $death . ($deathSource === 'Worden' ? '   [from Worden, not the census]' : '') : '(empty: the census reading is damaged and no second source gives it)') . PHP_EOL;
    echo '   burialPlace  ' . $BURIAL . PHP_EOL;
    foreach ($dates as $d) {
        echo '   recordDates  printed "' . $d['printed'] . '"  iso '
            . ($d['iso'] !== '' ? substr($d['iso'], 0, 10) . ', read as a ' . $d['granularity'] : '(empty)')
            . ', unconfirmed' . PHP_EOL;
    }
    echo '   body         ' . mb_substr($prose, 0, 96) . '...' . PHP_EOL;
}

echo PHP_EOL . 'relations that would be set:' . PHP_EOL;
$planGate = function (string $a, string $b) use ($plan, $plannedHistorical): string {
    $out = [];
    foreach ([$a, $b] as $k) {
        [$ok, $why] = isset($plan[$k]) ? $plannedHistorical($plan[$k]) : [false, 'not in the plan'];
        if (!$ok) { $out[] = ($plan[$k]['title'] ?? $k) . ': ' . $why; }
    }
    return $out ? '   REFUSED, living or undetermined: ' . implode('; ', $out) : '';
};
foreach ($SPOUSES as [$a, $b]) {
    $gate = $planGate($a, $b);
    echo '   spouseOf   ' . ($plan[$a]['title'] ?? $a) . '  <->  ' . ($plan[$b]['title'] ?? $b)
        . '   [the census spouse column says so]' . $gate . PHP_EOL;
}
foreach ($CHILDREN as $child => $parents) {
    foreach ($parents as $p) {
        $gate = $planGate($child, $p);
        echo '   childOf    ' . ($plan[$child]['title'] ?? $child) . '  ->  ' . ($plan[$p]['title'] ?? $p)
            . '   [inferred: Worden\'s four children, aged eight to thirty]' . $gate . PHP_EOL;
    }
}

/* --------------------------------------------- who else has a second fact

   The rule for a record is that there is something to say. Twelve plots were
   named as having that. Looking for the rest afterwards found seven more that
   meet the same test, each with a page the archive knows about or a line in the
   corpus. They are reported and not created: the list above was given, this one
   was found, and extending it is a decision rather than a consequence. */

$CANDIDATES = [
    '08'  => ['Nick Rivera', 'SCVHistory.com LW2154g, "Ruiz Cemetery: Nick Rivera, Newhall Innkeeper". '
        . 'The census gives only the initials N. H.; the photo page gives him a name and a trade.'],
    '42'  => ['Ramon Perea', 'Three pages: "Ramon Perea, San Francisquito Canyon Rancher", LW021704 '
        . '"Ramon Perea: A Forgotten Name at Pico", and TS1915 of his grave marker with Antonia.'],
    '43'  => ['Antonia D. de Perea', 'SCVHistory.com TS1915, "Ruiz Cemetery: Ramon & Antonia Perea". '
        . 'The census surname reads OePerea, which is a scan of DePerea.'],
    '25a' => ['Frances Cooke', 'LW2430 of the Cooke grave marker, and LT0101, "Descendants of Chief '
        . 'Frances and Fred Cooke Gather at Ruiz Cemetery", which ties the family to the Tataviam.'],
    '25b' => ['Fred Cooke', 'The same two pages.'],
    '27'  => ['Harry S. Chacanaca', 'SCVHistory.com TS1964 of his grave marker, and an obituary page '
        . 'for Charles Clarence Chacanaca, 1923-1993, which is the same family.'],
    '09a' => ['Pablo Araujo', 'Jerry Reynolds, Chapter 30: "Farther up were the Arujos. Their son '
        . 'Pablo became a renowned mule" driver. Reynolds spells it Arujo, the census Araujo.'],
];

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
echo 'NOT CREATED, BUT THEY PASS THE SAME TEST' . PHP_EOL;
echo 'Seven more plots have a second fact already. Say the word and they join the twelve.' . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;
foreach ($CANDIDATES as $map => [$who, $why]) {
    if (!isset($byMap[$map])) { continue; }
    $r = $byMap[$map];
    echo PHP_EOL . '  plot ' . str_pad($map, 5) . $who
        . '   (census: ' . trim($r['last'] . ' ' . $r['first'] . ' ' . $r['middle'])
        . ', ' . ($r['birthIso'] ?: '?') . ' to ' . ($r['deathIso'] ?: '?') . ')' . PHP_EOL;
    echo '        ' . $why . PHP_EOL;
}

echo PHP_EOL . 'And one page the archive does not hold that would settle a good deal of this:' . PHP_EOL;
echo '  /scvhistory/ruiz-cemetery-census.htm  "Ruiz-Perea Family Cemetery: A Census."' . PHP_EOL;
echo '  A second census of the same ground, not an OCR of a typescript. Worth extracting before' . PHP_EOL;
echo '  anyone checks these dates against the stones.' . PHP_EOL;

/* -------------------------------------------------------------- the writing */

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$tableRows = [];
foreach ($byMap as $r) {
    $tableRows[] = [
        'map' => $r['map'], 'last' => $r['last'], 'first' => $r['first'], 'middle' => $r['middle'],
        'birthPrinted' => $r['birthPrinted'], 'birthIso' => $r['birthIso'],
        'deathPrinted' => $r['deathPrinted'], 'deathIso' => $r['deathIso'],
        'spouse' => $r['spouse'], 'commentsSheet' => $r['commentsSheet'], 'note' => $r['note'],
    ];
}
echo 'graveCensus on ' . $place->title . ': ' . count($tableRows) . ' rows would be written' . PHP_EOL;
$hasTable = $hasField($place, $CENSUS_FIELD);
if (!$hasTable) { echo '  BLOCKED: the field does not exist yet. Run add_grave_census_field.php.' . PHP_EOL; }
elseif (!$APPLY) { echo '  (the whole table is replaced, since the census is one document)' . PHP_EOL; }

$placeBody = (string)$place->body;
$noteNeeded = !str_contains($placeBody, $NOTE_MARK);
echo PHP_EOL . 'the provenance note on the place body: '
    . ($noteNeeded ? 'would be appended, ' . strlen($NOTE) . ' characters' : 'already there, left alone') . PHP_EOL;
if ($noteNeeded) { echo '  "' . mb_substr($NOTE, 0, 150) . '..."' . PHP_EOL; }

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

/* people first, so the relations have something to point at */
$made = [];
foreach ($plan as $map => $p) {
    $entry = $p['existing'];
    if (!$entry) {
        $entry = new \craft\elements\Entry();
        $entry->sectionId = Craft::$app->entries->getSectionByHandle('persons')->id;
        $entry->typeId = Craft::$app->entries->getEntryTypeByHandle('person')->id;
        $entry->title = $p['title'];
    }
    $set = [
        'fullName' => $p['fullName'],
        'body' => $p['body'],
        'burialPlace' => $p['burialPlace'],
        'recordDates' => $p['recordDates'],
    ];
    if ($p['birthDate'] !== '') { $set['birthDate'] = $p['birthDate']; }
    if ($p['deathDate'] !== '') { $set['deathDate'] = $p['deathDate']; }
    if ($community) { $set['neighborhood'] = [$community->id]; }
    /* Never overwrite something a person wrote. */
    foreach ($set as $h => $v) {
        if (!$hasField($entry, $h)) { unset($set[$h]); continue; }
        if ($p['existing']) {
            $cur = $entry->getFieldValue($h);
            if ($cur instanceof \craft\elements\db\ElementQuery) { $cur = $cur->ids(); }
            if (!(is_array($cur) ? count($cur) === 0 : trim((string)$cur) === '')) { unset($set[$h]); }
        }
    }
    $entry->setFieldValues($set);
    if ($elements->saveElement($entry)) {
        $made[$map] = $entry;
        echo ($p['existing'] ? 'updated ' : 'created ') . '#' . $entry->id . '  ' . $p['title']
            . '  -> ' . implode(', ', array_keys($set)) . PHP_EOL;
    } else {
        echo 'FAILED ' . $p['title'] . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
    }
}

/* The gate, against the records as saved. Either party failing refuses the
   relation, loudly, and the refusal is counted so the summary cannot read as
   clean. */
$gate = function (\craft\elements\Entry $x, \craft\elements\Entry $y, string $rel) use ($historical, &$refused): bool {
    [$okX, $whyX] = $historical($x);
    [$okY, $whyY] = $historical($y);
    if ($okX && $okY) { return true; }
    $refused[] = $rel . '  #' . $x->id . ' ' . $x->title . ' [' . $whyX . ']  ->  #' . $y->id . ' ' . $y->title . ' [' . $whyY . ']';
    echo 'REFUSED ' . end($refused) . PHP_EOL;
    return false;
};
$wantRel = [];
foreach ($SPOUSES as [$a, $b]) {
    if (!isset($made[$a], $made[$b])) { continue; }
    if (!$gate($made[$a], $made[$b], 'spouseOf')) { continue; }
    foreach ([[$a, $b], [$b, $a]] as [$x, $y]) {
        $e = $made[$x];
        if (!$hasField($e, 'spouseOf')) { continue; }
        $ids = $e->spouseOf->ids();
        if (in_array($made[$y]->id, $ids, true)) { continue; }
        $e->setFieldValue('spouseOf', array_merge($ids, [$made[$y]->id]));
        $elements->saveElement($e);
        $wantRel[] = ['spouseOf', $e->id, $made[$y]->id];
        echo 'spouseOf  ' . $e->title . ' -> ' . $made[$y]->title . PHP_EOL;
    }
}

foreach ($CHILDREN as $child => $parents) {
    if (!isset($made[$child])) { continue; }
    $e = $made[$child];
    if (!$hasField($e, 'childOf')) { continue; }
    $ids = $e->childOf->ids();
    $added = [];
    foreach ($parents as $p) {
        if (!isset($made[$p]) || in_array($made[$p]->id, $ids, true)) { continue; }
        if (!$gate($e, $made[$p], 'childOf')) { continue; }
        $ids[] = $made[$p]->id;
        $added[] = $made[$p];
        $wantRel[] = ['childOf', $e->id, $made[$p]->id];
    }
    if (!$added) { continue; }
    $e->setFieldValue('childOf', $ids);
    $elements->saveElement($e);
    echo 'childOf   ' . $e->title . ' -> ' . implode(', ', array_map(fn($x) => $x->title, $added)) . PHP_EOL;
}

/* Read the relations back, and re-run the gate on what the database holds. */
$relShort = 0;
foreach ($wantRel as [$h, $src, $tgt]) {
    $fresh = \craft\elements\Entry::find()->id($src)->status(null)->one();
    $got = $fresh ? $fresh->getFieldValue($h)->status(null)->ids() : [];
    if (!in_array($tgt, $got, true)) { $relShort++; echo 'RELATION DID NOT PERSIST: ' . $h . ' #' . $src . ' -> #' . $tgt . PHP_EOL; }
}
echo 'relations read back: ' . (count($wantRel) - $relShort) . ' of ' . count($wantRel)
    . ($relShort ? '   SHORT' : '') . PHP_EOL;
echo 'relations refused, living or undetermined: ' . count($refused) . PHP_EOL;
foreach ($refused as $r) { echo '   ' . $r . PHP_EOL; }

if ($hasTable) {
    $place->setFieldValue($CENSUS_FIELD, $tableRows);
}
if ($noteNeeded) {
    $place->setFieldValue('body', rtrim($placeBody) . "\n\n" . $NOTE);
}
if ($elements->saveElement($place)) {
    echo 'place #' . $place->id . ' saved: ' . ($hasTable ? count($tableRows) . ' census rows' : 'no table')
        . ($noteNeeded ? ', provenance note appended' : '') . PHP_EOL;

/* Read the write back. A save that reports success and changes nothing is worse
 * than one that fails. add_footnote_source_column.php printed "saved: 5" twice
 * and persisted nothing, because it set a Table row's handle key while Craft
 * stores the column key, and the column key was null so it won at serialisation.
 *
 * Rows built here are keyed by handle alone and never carry a col key, which is
 * the safe case: with nothing to lose to, the handle is used. The check is here
 * because "it should be fine" is what the last one said. */
    if ($hasTable) {
        $fresh = \craft\elements\Entry::find()->id($place->id)->status(null)->one();
        $got = $fresh ? $fresh->getFieldValue($CENSUS_FIELD) : null;
        $n = is_array($got) ? count($got) : 0;
        $filled = 0;
        foreach ((is_array($got) ? $got : []) as $r) {
            if (trim((string)($r['col1'] ?? $r['map'] ?? '')) !== '') { $filled++; }
        }
        echo 'read back: ' . $n . ' rows, ' . $filled . ' carrying a map number' . PHP_EOL;
        if ($n !== count($tableRows) || $filled !== $n) {
            echo PHP_EOL . 'THE WRITE DID NOT PERSIST: wrote ' . count($tableRows)
               . ' rows, read back ' . $n . ' with ' . $filled . ' filled.' . PHP_EOL;
            echo 'Do not re-run until this is understood.' . PHP_EOL;
            return;
        }
        echo 'verified.' . PHP_EOL;
    }
} else {
    echo 'FAILED to save the place: ' . json_encode($place->getErrors()) . PHP_EOL;
}
