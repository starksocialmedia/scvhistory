/**
 * Links the mentions of related people, places and organizations inside article
 * prose, without touching the prose. For every article, for every record already
 * related to it, every spelling that record answers to is looked for in the body
 * and the first occurrence in each paragraph is recorded as an offset. The body
 * is never rewritten: the links are a layer that _partials/prose.twig applies at
 * render time, so the text stays exactly as Perkins or Reynolds wrote it and the
 * linking can be re-run or dropped without a single character moving.
 *
 * The layer is one file per article at templates/_data/prose-links/<id>.json,
 * read by the prose partial through Twig's source(). Deleting that directory
 * reverts every link on the site and changes no record.
 *
 * Rules, in the order they bind:
 *   - Only a record the article is already related to. The relation decision
 *     governs; this script never introduces a connection nobody made.
 *   - Longest spelling first, so "Ygnacio del Valle" wins over "Ygnacio".
 *   - First occurrence per entity per paragraph, so a page does not turn blue.
 *   - Never inside an existing link, never inside an HTML tag, never in a
 *     heading. A webmaster's note is a separate field and is never seen here.
 *   - The words are never altered. The anchor wraps the text as written, so
 *     "Dr. Bard" links to Cephas L. Bard's record and still reads "Dr. Bard".
 *
 * Paragraphs are counted exactly as _partials/prose.twig counts them, including
 * its rejoin pass, and every span carries the text it expects to find. The
 * partial checks that text before wrapping, so if the two ever drift the links
 * quietly disappear instead of cutting through a sentence.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/link_mentions_in_prose.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* Stamped into every layer. Raise it whenever the matching rules change, so a
   file written by an older run is identifiable without reading its spans. */
$VERSION = '1.0';

/* Set to an article slug to print that article's layer as it would be written,
   without writing anything. Useful for checking one page before a whole run. */
$DUMP = '';

$root     = \Craft::getAlias('@root');
$outDir   = $root . '/templates/_data/prose-links';
$canonPath = $root . '/inventory/legacy/entity-canon.json';

/* The three fields a relation decision writes. writtenBy and editedBy name the
   author of the piece, not a subject of it, so they are deliberately not here. */
$RELATION_FIELDS = [
    'subjectPerson'       => ['section' => 'persons',       'alias' => 'personAliases'],
    'depictsPlace'        => ['section' => 'places',        'alias' => 'placeAliases'],
    'subjectOrganization' => ['section' => 'organizations', 'alias' => 'orgAliases'],
];
$KIND_OF = ['subjectPerson' => 'person', 'depictsPlace' => 'place', 'subjectOrganization' => 'organization'];

/* A spelling shorter than this is not safe to match on its own. */
$MIN_SPELLING = 4;

/* ---------------------------------------------------------------- helpers */

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

$fold = function (string $s): string {
    $s = mb_strtolower(trim($s));
    $from = ["\u{00E1}","\u{00E0}","\u{00E2}","\u{00E4}","\u{00E9}","\u{00E8}","\u{00EA}","\u{00EB}","\u{00ED}","\u{00EC}","\u{00EE}","\u{00EF}","\u{00F3}","\u{00F2}","\u{00F4}","\u{00F6}","\u{00FA}","\u{00F9}","\u{00FB}","\u{00FC}","\u{00F1}","\u{00E7}","\u{2019}","\u{2018}"];
    $to   = ['a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','u','u','u','u','n','c','',''];
    $s = str_replace($from, $to, $s);
    $s = preg_replace('~[^a-z0-9 ]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};

/* ------------------------------------------------ paragraphs, as prose.twig
   builds them. A line with no letters rejoins the line above, so does a line
   that opens lowercase or with a comma, and so does a line that does not end in
   sentence punctuation whose next line opens that way. Image tokens neither
   join nor are joined to, and they keep their place in the index so a span's
   paragraph number means the same thing on both sides. */

$paragraphsOf = function (string $text): array {
    $TOKEN = '~^\[image:\d+\]$~';
    $ENDS  = '~[.!?:;"\x{201D}\x{2019})\x{2014}-]$~u';
    $CONT  = '~^[a-z,;:)]~u';

    $raw = [];
    foreach (preg_split("~\r\n|\n|\r~", trim($text)) as $p) {
        $p = trim($p);
        if ($p !== '') { $raw[] = $p; }
    }

    $lines = [];
    $n = count($raw);
    for ($i = 0; $i < $n; $i++) {
        $l   = $raw[$i];
        $nxt = $raw[$i + 1] ?? '';
        $isTok      = (bool)preg_match($TOKEN, $l);
        $afterTok   = $lines && preg_match($TOKEN, $lines[count($lines) - 1]);
        $letterless = (bool)preg_match('~^[^\p{L}]+$~u', $l);
        $continues  = (bool)preg_match($CONT, $l);
        $dangling   = !preg_match($ENDS, $l) && preg_match($CONT, $nxt);
        $joinable   = $lines && !$isTok && !$afterTok && ($letterless || $continues || $dangling);

        if ($joinable) {
            $glue = ($letterless || preg_match('~^[,;:)]~u', $l)) ? '' : ' ';
            $lines[count($lines) - 1] .= $glue . $l;
        } else {
            $lines[] = $l;
        }
    }
    return $lines;
};

/* A section heading rather than prose: the legacy text sets them as short lines
   with no sentence punctuation, "Basketry Varied" or "VILLAGE LOCATIONS". */
$isHeading = function (string $p): bool {
    if (preg_match('~^\[image:\d+\]$~', $p)) { return true; }
    if (mb_strlen($p) > 70) { return false; }
    if (preg_match('~[.!?]["\x{201D}]?$~u', $p)) { return false; }
    return true;
};

/* Character ranges no link may start in or overlap: every HTML tag, and
   everything inside an anchor that is already there. */
$forbiddenRanges = function (string $p): array {
    $out = [];
    if (preg_match_all('~<a\b[^>]*>.*?</a>~is', $p, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[0] as $hit) { $out[] = [$hit[1], $hit[1] + strlen($hit[0])]; }
    }
    if (preg_match_all('~<[^>]*>~s', $p, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[0] as $hit) { $out[] = [$hit[1], $hit[1] + strlen($hit[0])]; }
    }
    return $out;
};

/* ---------------------------------------------------------------- the canon */

$canonGroups = ['person' => [], 'place' => [], 'organization' => []];
$canonNames  = 0;
$canonStamp  = 'absent';
if (file_exists($canonPath)) {
    $c = json_decode(file_get_contents($canonPath), true);
    foreach (($c['canon'] ?? []) as $kind => $groups) {
        foreach ($groups as $g) {
            $names = array_values(array_filter(array_merge(
                [(string)($g['canonical'] ?? '')], array_map('strval', $g['variants'] ?? [])
            ), fn($v) => trim($v) !== ''));
            if (!$names) { continue; }
            $canonGroups[$kind][] = $names;
            $canonNames += count($names);
        }
    }
    $canonStamp = substr(sha1_file($canonPath), 0, 12) . '@' . date('Y-m-d', filemtime($canonPath));
    echo 'canon: ' . $canonNames . ' spellings in '
        . array_sum(array_map('count', $canonGroups)) . ' groups' . PHP_EOL;
} else {
    echo 'NOTE: inventory/legacy/entity-canon.json is not there yet, so the only' . PHP_EOL;
    echo '      spellings available are each record\'s title and its alias field.' . PHP_EOL;
    echo '      Run apply_entity_merges.php first for the full list.' . PHP_EOL;
}

/* A record answers to its title, its aliases, and every name in the canon group
   that any of those names belongs to. */
$canonIndex = [];
foreach ($canonGroups as $kind => $groups) {
    foreach ($groups as $gi => $names) {
        foreach ($names as $nm) { $canonIndex[$kind . '|' . $fold($nm)][] = $gi; }
    }
}

$spellingsFor = function (\craft\elements\Entry $rec, string $kind, string $aliasHandle)
    use ($hasField, $fold, $canonGroups, $canonIndex, $MIN_SPELLING): array {

    $names = [(string)$rec->title];
    if ($hasField($rec, $aliasHandle)) {
        try {
            foreach (preg_split('~\r\n|\n|\r|,~', (string)$rec->getFieldValue($aliasHandle)) as $a) {
                if (trim($a) !== '') { $names[] = trim($a); }
            }
        } catch (\Throwable $ex) {}
    }

    $groups = [];
    foreach ($names as $nm) {
        foreach (($canonIndex[$kind . '|' . $fold($nm)] ?? []) as $gi) { $groups[$gi] = true; }
    }
    foreach (array_keys($groups) as $gi) {
        foreach ($canonGroups[$kind][$gi] as $nm) { $names[] = $nm; }
    }

    $seen = [];
    $out = [];
    foreach ($names as $nm) {
        $nm = trim($nm);
        if (mb_strlen($nm) < $MIN_SPELLING) { continue; }
        $k = $fold($nm);
        if ($k === '' || isset($seen[$k])) { continue; }
        $seen[$k] = true;
        $out[] = $nm;
    }
    /* Longest first: the alternation below is leftmost-first, so at one position
       the longer spelling is the one that matches. */
    usort($out, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
    return $out;
};

/* ---------------------------------------------------------------- the walk */

$articles = \craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->all();

$plan = [];
$totalSpans = 0;
$articlesWithSpans = 0;
$inbound = [];
$skipped = ['no relation' => 0, 'no body' => 0, 'no spelling matched' => 0];
$headingsSkipped = 0;
$insideLink = 0;
$noShortSpelling = [];

foreach ($articles as $entry) {
    if (!$hasField($entry, 'body')) { continue; }
    $body = '';
    try { $body = (string)$entry->getFieldValue('body'); } catch (\Throwable $ex) { continue; }
    if (trim($body) === '') { $skipped['no body']++; continue; }

    /* Every record the article is already related to, with its spellings. */
    $targets = [];
    foreach ($RELATION_FIELDS as $handle => $cfg) {
        if (!$hasField($entry, $handle)) { continue; }
        try { $related = $entry->getFieldValue($handle)->status(null)->all(); }
        catch (\Throwable $ex) { continue; }
        foreach ($related as $rec) {
            $sp = $spellingsFor($rec, $KIND_OF[$handle], $cfg['alias']);
            if (!$sp) { $noShortSpelling[] = $entry->slug . '  ' . $rec->title; continue; }
            $targets[$rec->id] = ['rec' => $rec, 'kind' => $KIND_OF[$handle], 'spellings' => $sp];
        }
    }
    if (!$targets) { $skipped['no relation']++; continue; }

    /* One alternation for the whole article, longest spelling first across every
       target, so the longest wins wherever two records share a word. */
    $alts = [];
    foreach ($targets as $id => $t) {
        foreach ($t['spellings'] as $sp) { $alts[] = ['t' => $sp, 'id' => $id]; }
    }
    usort($alts, fn($a, $b) => mb_strlen($b['t']) <=> mb_strlen($a['t']));
    $ownerOf = [];
    $parts = [];
    foreach ($alts as $a) {
        $parts[] = preg_quote($a['t'], '~');
        $ownerOf[$a['t']] = $a['id'];
    }
    $re = '~(?<![\p{L}\p{N}])(' . implode('|', $parts) . ')(?![\p{L}\p{N}])~u';

    $paras = $paragraphsOf($body);
    $spans = [];
    foreach ($paras as $pi => $para) {
        if ($isHeading($para)) { $headingsSkipped++; continue; }
        $bad = $forbiddenRanges($para);
        $linkedHere = [];
        if (!preg_match_all($re, $para, $m, PREG_OFFSET_CAPTURE)) { continue; }
        $takenTo = -1;
        foreach ($m[1] as $hit) {
            [$text, $at] = $hit;
            if ($at < $takenTo) { continue; }
            $id = $ownerOf[$text] ?? null;
            if ($id === null || isset($linkedHere[$id])) { continue; }
            $end = $at + strlen($text);
            $blocked = false;
            foreach ($bad as [$bs, $be]) { if ($at < $be && $end > $bs) { $blocked = true; break; } }
            if ($blocked) { $insideLink++; continue; }

            $rec = $targets[$id]['rec'];
            /* Offsets are stored in characters, because Twig's slice counts
               characters. preg_match_all hands back bytes, so convert here and
               the two sides agree on any paragraph carrying an accent. */
            $spans[] = [
                'p' => $pi,
                's' => mb_strlen(substr($para, 0, $at)),
                'n' => mb_strlen($text),
                't' => $text,
                'u' => (string)$rec->url,
                'id' => (int)$id,
            ];
            $linkedHere[$id] = true;
            $takenTo = $end;
            $inbound[$id] = ($inbound[$id] ?? 0) + 1;
        }
    }

    if (!$spans) { $skipped['no spelling matched']++; continue; }
    usort($spans, fn($a, $b) => [$a['p'], $a['s']] <=> [$b['p'], $b['s']]);
    $plan[$entry->id] = ['entry' => $entry, 'spans' => $spans, 'paras' => count($paras)];
    $totalSpans += count($spans);
    $articlesWithSpans++;
}

/* ---------------------------------------------------------------- report */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'articles scanned: ' . count($articles) . PHP_EOL;
echo str_repeat('-', 78) . PHP_EOL;

foreach ($plan as $row) {
    $e = $row['entry'];
    $byRec = [];
    foreach ($row['spans'] as $s) { $byRec[$s['id']] = ($byRec[$s['id']] ?? 0) + 1; }
    echo str_pad($e->slug, 52) . str_pad((string)count($row['spans']), 5, ' ', STR_PAD_LEFT)
        . ' links in ' . $row['paras'] . ' paragraphs' . PHP_EOL;
    foreach ($row['spans'] as $i => $s) {
        if ($i >= 4) { echo '      ... and ' . (count($row['spans']) - 4) . ' more' . PHP_EOL; break; }
        echo '      p' . str_pad((string)$s['p'], 4) . '"' . $s['t'] . '" -> ' . $s['u'] . PHP_EOL;
    }
}

if ($DUMP !== '') {
    $hit = null;
    foreach ($plan as $row) { if ($row['entry']->slug === $DUMP) { $hit = $row; break; } }
    echo str_repeat('=', 78) . PHP_EOL;
    echo 'layer for ' . $DUMP . ':' . PHP_EOL;
    echo $hit === null ? '  no layer; this article earns no link' . PHP_EOL
        : json_encode(['meta' => ['entry' => (int)$hit['entry']->id, 'slug' => $hit['entry']->slug,
            'paragraphs' => $hit['paras']], 'spans' => $hit['spans']],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

echo str_repeat('=', 78) . PHP_EOL;
echo 'mentions that would be linked: ' . $totalSpans . PHP_EOL;
echo 'articles that would gain links: ' . $articlesWithSpans . PHP_EOL;
foreach ($skipped as $why => $n) { echo 'articles skipped, ' . $why . ': ' . $n . PHP_EOL; }
echo 'headings and image tokens passed over: ' . $headingsSkipped . PHP_EOL;
echo 'matches dropped for sitting inside a link or a tag: ' . $insideLink . PHP_EOL;

if ($inbound) {
    arsort($inbound);
    echo PHP_EOL . '=== ten entities gaining the most inbound links ===' . PHP_EOL;
    $i = 0;
    foreach ($inbound as $id => $n) {
        if ($i++ >= 10) { break; }
        $rec = \craft\elements\Entry::find()->id($id)->status(null)->one();
        echo '  ' . str_pad((string)$n, 5, ' ', STR_PAD_LEFT) . '  '
            . ($rec ? $rec->title . '  (' . $rec->section->handle . ')' : '#' . $id) . PHP_EOL;
    }
}

if ($noShortSpelling) {
    echo PHP_EOL . 'related records with no spelling long enough to match safely:' . PHP_EOL;
    foreach (array_slice(array_unique($noShortSpelling), 0, 20) as $r) { echo '  ' . $r . PHP_EOL; }
}

/* ---------------------------------------------------------------- write */

if (!$APPLY) {
    echo PHP_EOL . 'nothing written. templates/_data/prose-links is untouched.' . PHP_EOL;
    return;
}

if (!is_dir($outDir) && !mkdir($outDir, 0775, true) && !is_dir($outDir)) {
    echo 'could not create ' . $outDir . PHP_EOL;
    return;
}

$written = 0;
$removed = 0;
$keep = [];
foreach ($plan as $id => $row) {
    $file = $outDir . '/' . $id . '.json';
    $keep[$file] = true;
    $payload = json_encode([
        'meta' => [
            'generated_by' => 'scripts/import/link_mentions_in_prose.php',
            'version' => $VERSION,
            'generated' => (new DateTime())->format('c'),
            'canon' => $canonStamp,
            'entry' => (int)$id,
            'slug' => $row['entry']->slug,
            'paragraphs' => $row['paras'],
        ],
        'spans' => $row['spans'],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

    /* Idempotent: an unchanged layer is not rewritten, so a second run touches
       nothing and the generated stamp does not churn. */
    /* Idempotent on the spans and on the stamp. An unchanged layer written by
       this version against this canon is left alone, so a second run touches
       nothing; a layer from an older version or an older canon is rewritten
       even where its spans happen to be identical. */
    $old = file_exists($file) ? file_get_contents($file) : null;
    if ($old !== null) {
        $a = json_decode($old, true);
        if (($a['spans'] ?? null) === $row['spans']
            && ($a['meta']['version'] ?? null) === $VERSION
            && ($a['meta']['canon'] ?? null) === $canonStamp) { continue; }
    }
    file_put_contents($file, $payload);
    $written++;
}

/* An article that no longer earns a link loses its layer, so a removed relation
   removes its links too. */
foreach (glob($outDir . '/*.json') as $file) {
    if (!isset($keep[$file])) { unlink($file); $removed++; }
}

echo PHP_EOL . 'layers written or updated: ' . $written . PHP_EOL;
echo 'stale layers removed: ' . $removed . PHP_EOL;
echo 'no record was changed; the bodies are byte for byte what they were.' . PHP_EOL;
