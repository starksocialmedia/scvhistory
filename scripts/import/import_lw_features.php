/**
 * The LW illustrated features, from inventory/legacy/lw-features.json.
 *
 * These are the "SCV History In Pictures" pages: a scan, a caption, a scan
 * credit and a source note, one per page, about 1,661 of them. They are
 * photographs rather than articles, and the photograph entry type was built
 * for them.
 *
 * The inventory file is not here yet. Until it is, this runs as a mapping
 * report: it prints what every incoming key would be written to, and exercises
 * the two parsers, the scan credit and the page title, against worked examples
 * so the reading can be checked before 1,661 records land. When the file
 * appears the same run becomes a real dry run over the real pages, and the
 * mapping table stays at the top of it.
 *
 * ------------------------------------------------------------- the scan credit
 *
 * Leon's scan line reads, in the shape we have been given:
 *
 *     9600 dpi jpeg from original print | source & current location unknown
 *
 * which is four facts and a provenance note:
 *
 *     creditDpi      9600                   the resolution he scanned at
 *     creditProcess  jpeg                   what he made
 *     creditKind     original print         what he put on the glass
 *     creditName     source & current...    where it came from and where it is
 *
 * creditRaw keeps the whole line exactly as the page prints it, always, parsed
 * or not. The parse is a convenience laid over the source, never a replacement
 * for it, and a line this script cannot read fully still arrives whole.
 *
 * Only the one shape above has actually been seen. Every part is therefore
 * optional in the parser and a line that yields nothing is not an error, it is
 * a line in a shape we have not met. Those are counted and printed by shape so
 * the grammar can be widened once against real data rather than guessed at
 * five times.
 *
 * -------------------------------------------------------------- the page title
 *
 * A title reads "LW0060 | Castaic | Scallop Shell Fossils": an accession code,
 * a topic, then the caption. 1,394 of the 1,508 LW pages the crawl reached open
 * with a code. The code goes to photoSourceCode, the caption becomes the title,
 * and the topic becomes the community only where it names one the archive
 * already has; "People" and "Early California" are topics, not places, and are
 * left alone rather than forced into a category.
 *
 * 51 of those pages never had a title written and read "Santa Clarita Valley
 * History In Pictures - LW0071". Those are listed rather than imported with a
 * placeholder for a name.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_lw_features.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$SOURCE   = 'inventory/legacy/lw-features.json';
$SECTION  = 'photographs';
$TYPE     = 'photograph';
$SHOW     = 25;     /* how many pages to print in full in the dry run */

/* Relations are off, and the dry run says why every time it runs. Matching a
   mention against a record title exactly catches 1,339 of 29,842 person
   mentions, 263 of 2,325 places and 0 of 3,378 organizations, and the person
   list is largely not people: "Hart Films", "Publicity Photos", "Drone Video".
   Wiring that up writes a few hundred right relations and leaves 34,000
   mentions looking handled when they are not. The archive already has a
   reconciliation pipeline with alias fields and a review screen, and that is
   where these belong. Set it true to see the numbers, not to apply them. */
$RELATE = false;

/* Every incoming key, and where it goes. This array is the mapping: the report
   below prints it rather than describing it, so the two cannot drift apart. */
$MAP = [
    'title'            => ['title, photoSourceCode, neighborhood', 'Masthead off, then the code, the topic, the caption. title_topic is preferred where the file gives it.'],
    'title_topic'      => ['neighborhood, where it names one', '126 of the topics are not communities: they are places, people and events the archive holds. See the list in the run.'],
    'lw_kind'          => ['(not used)', 'illustrated_place_feature on all 1,661. It is why this file exists, not a field.'],
    'subtitle'         => ['photoCaptionExt', 'Empty on all 1,661 pages, so nothing is written. Kept in the map so its absence is visible.'],
    'body_text'        => ['body', 'Verbatim, as every other importer here. Cleanup is clean_legacy_bodies.php\'s job.'],
    'date_raw'         => ['photoDate', 'As printed. Plain text, because "~1926" and "n.d." are both common and both true.'],
    'scan_credit_raw'  => ['creditRaw + creditDpi, creditProcess, creditKind, creditName', 'Raw always; the four parts where the line parses.'],
    'source_note_raw'  => ['photoCredit, where it is a credit', '204 of 389 are body prose the extraction picked up by mistake and are written nowhere.'],
    'byline_raw'       => ['(dropped)', 'These pages are not bylined pieces. Reported if any page carries one.'],
    'fine_print_raw'   => ['webmasterNoteBottom', 'The rights and reuse line at the foot of the page.'],
    'editor_notes'     => ['webmasterNoteTop, webmasterNoteBottom', 'By position, as import_perkins.php splits them.'],
    'legacy_key'       => ['legacyKey', ''],
    'legacy_path'      => ['legacyUrl', 'The join to the migration ledger, so an imported page stops reading as missing.'],
    'source_url'       => ['sourcePath', ''],
    'people_mentioned' => ['(counted, not written)', 'Exact title matching catches 1,339 of 29,842. Left to the reconciliation pipeline.'],
    'places_mentioned' => ['(counted, not written)', '263 of 2,325. Same.'],
    'orgs_mentioned'   => ['(counted, not written)', '0 of 3,378, because the org names live in orgAliases. Same.'],
    'communities_mentioned' => ['neighborhood', 'Merged with the community read out of the title.'],
    'community_inferred'    => ['neighborhood', 'Used only where the title and the mentions give nothing.'],
    'dates_mentioned'  => ['recordDates', 'printed, iso, granularity, unconfirmed. Same shape as the articles.'],
    'images'           => ['(a second pass)', 'Files are fetched by import_legacy_images.php, which needs lw-features-images.json.'],
    'links_out'        => ['(not used here)', 'export_article_links.php already reads these.'],
    'needs_review'     => ['(reported, not stored)', 'Printed per page so a doubtful extraction is visible before it lands.'],
    'series_position'  => ['photoSequence', 'The order within a run of pages sharing a code, e.g. LW2154a..g.'],
];

$UNDECIDED =
    "source_note_raw was the open question and the real file answers it: the key holds two different\n"
    . "things. Some are credits, \"Source: City of Santa Clarita\", \"(H. Carey Collection)\". 204 begin\n"
    . "mid-sentence, \"at 5675 W. Washington Blvd. in Culver City...\", which is body prose the\n"
    . "extraction's selector caught. Only the first kind is written, to photoCredit. The second kind is\n"
    . "listed at the end of this run and is a bug to send back to the extraction, not data to massage.";

/* ------------------------------------------------------------ the parsers */

/**
 * Reads Leon's scan line. Returns the four parts; any of them may be empty,
 * and the caller keeps the raw line regardless.
 */
$readCredit = function (string $raw): array {
    $out = ['code' => '', 'dpi' => '', 'process' => '', 'kind' => '', 'name' => '', 'left' => '', 'shape' => ''];
    $line = trim(preg_replace('~\s+~u', ' ', $raw));
    if ($line === '') { return $out; }

    /* The real lines all open with the accession code and a colon, in whatever
       case Leon typed it that day: "LW0070:", "lw2278:", "Lw2107:". It is not
       part of the credit, and it is a free cross-check against the code read
       out of the title, so it is taken off and kept. */
    if (preg_match('~^([A-Za-z]{2,4}\d{3,6}[a-z]?)\s*:\s*(.*)$~', $line, $m)) {
        $out['code'] = strtoupper(substr($m[1], 0, 2)) . substr($m[1], 2);
        $line = trim($m[2]);
    }

    /* The bar separates the scan from where the thing came from. Only the last
       bar, so a source note with a bar in it survives on the right. */
    $left = $line;
    if (str_contains($line, '|')) {
        $at = strrpos($line, '|');
        $left = trim(substr($line, 0, $at));
        $out['name'] = trim(substr($line, $at + 1));
    }

    /* "9600 dpi", wherever in the left half it sits, so "Scanned at 9600 dpi"
       reads the same as "9600 dpi". */
    if (preg_match('~(\d{2,6})\s*dpi\b~i', $left, $m)) {
        $out['dpi'] = $m[1];
        $rest = trim(substr($left, strpos($left, $m[0]) + strlen($m[0])));
    } else {
        $rest = $left;
    }

    /* "... from <kind>". The last "from", so "jpeg from a copy print from the
       Ruiz family" keeps the whole kind. */
    if (preg_match('~^(.*?)\bfrom\b\s+(.+)$~i', $rest, $m)) {
        $out['process'] = trim($m[1], " ,.;");
        $out['kind'] = trim($m[2], " ,.;");
    } else {
        $out['process'] = trim($rest, " ,.;");
    }

    /* With no dpi and no "from", the line is not a scan credit at all: it is a
       source note that happens to sit in the scan line. It goes to the name
       rather than being called a process. */
    if ($out['dpi'] === '' && $out['kind'] === '' && $out['name'] === '') {
        $out['name'] = $out['process'];
        $out['process'] = '';
    }

    $out['left'] = $left;
    $out['shape'] = ($out['code'] !== '' ? 'C' : '-') . ($out['dpi'] !== '' ? 'D' : '-')
                  . ($out['process'] !== '' ? 'P' : '-')
                  . ($out['kind'] !== '' ? 'K' : '-') . ($out['name'] !== '' ? 'N' : '-');
    return $out;
};

/**
 * source_note_raw is two different things in one key, and only one of them is a
 * credit. 389 pages carry it. Some are what the name promises, "Source: City of
 * Santa Clarita", "(H. Carey Collection)", "News story courtesy of Tricia Lemon
 * Putnam." Others begin mid-sentence, "at 5675 W. Washington Blvd. in Culver
 * City...", "The hotel fell into disrepair and was officially closed on...",
 * which is body prose the extraction's selector picked up by mistake.
 *
 * Writing the second kind into photoCredit would put a sentence fragment in a
 * credit field on a couple of hundred records, so the two are told apart here
 * and only the first is mapped. The rest are reported, because they are an
 * extraction bug to send back rather than data to massage.
 */
$readSourceNote = function (string $raw): array {
    $s = trim(preg_replace('~\s+~u', ' ', $raw));
    if ($s === '') { return ['', '']; }

    /* A credit names a source. It opens with one of the words people use to do
       that, or is a parenthetical collection, and it is short. */
    $credit = preg_match('~^(source\b|courtesy\b|photo(graph)? (by|courtesy)\b|collection of\b|from the\b|[\x{2022}\-]\s*\S)~iu', $s)
        || preg_match('~^\(.*\)\.?$~u', $s)
        || preg_match('~\b(collection|archives?|courtesy of|society)\b~i', $s);

    /* And a fragment gives itself away by starting mid-sentence: a lowercase
       word that is not a known opener, or no terminal stop at all. */
    $fragment = preg_match('~^[a-z]~u', $s) && !preg_match('~^(source|courtesy|from|photo)~i', $s);

    if ($fragment || mb_strlen($s) > 240) { return ['', $s]; }
    if ($credit) { return [$s, '']; }
    return ['', $s];
};

/* The communities the archive actually has, matched loosely enough to let
   "San Francisquito" find "San Francisquito Canyon" and no looser. */
$communities = [];
foreach (\craft\elements\Category::find()->group('neighborhood')->status(null)->all() as $c) {
    $key = fn(string $s) => preg_replace('~[^a-z0-9]~', '', mb_strtolower($s));
    $communities[$key($c->title)] = $c;
    $communities[$key(preg_replace('~\s+canyon$~i', '', $c->title))] = $c;
    $communities[$key($c->slug)] = $c;
}

$PLACEHOLDER = '~^santa clarita valley history in pictures\b~i';

/**
 * Splits "LW0060 | Castaic | Scallop Shell Fossils".
 * Returns code, community (a category or null), title, and a problem if any.
 */
$readTitle = function (string $raw, string $legacyKey) use ($communities, $PLACEHOLDER): array {
    $out = ['code' => '', 'sequence' => '', 'community' => null, 'topic' => '', 'title' => '', 'problem' => ''];
    $t = trim(preg_replace('~\s+~u', ' ', $raw));
    /* Every title in the file opens with the site's masthead. */
    $t = preg_replace('~^[A-Za-z0-9][A-Za-z0-9.\-]*\.(?:com|net|org)\s*\|?\s*~i', '', $t, 1);

    /* The pages whose title was never written. The code is still in there, and
       the filename has it too, but there is no caption to use as a name. */
    if ($t === '' || preg_match($PLACEHOLDER, $t)) {
        $out['problem'] = 'the page has no title of its own, only the site\'s default';
    }

    $parts = array_map('trim', explode('|', $t));
    if ($parts && preg_match('~^([A-Z]{2,4}\d{3,6})([a-z]?)$~', $parts[0], $m)) {
        $out['code'] = $m[1] . $m[2];
        $out['sequence'] = $m[2];
        array_shift($parts);
    }
    /* No code in the title: the filename carries it. lw2154g -> LW2154g. */
    if ($out['code'] === '' && preg_match('~^([a-z]{2,4})(\d{3,6})([a-z]?)$~i', $legacyKey, $m)) {
        $out['code'] = strtoupper($m[1]) . $m[2] . $m[3];
        $out['sequence'] = $m[3];
    }

    /* The middle segment is a topic: a community on most pages, but on plenty
       of them a subject instead, "People" or "Early California" or "Tataviam
       Culture". It comes off the title either way, because it is a shelf label
       rather than part of the caption, and only becomes a community where it
       names one the archive already has. The ones that do not are counted and
       printed, so the list can be looked at rather than guessed at. */
    $key = fn(string $s) => preg_replace('~[^a-z0-9]~', '', mb_strtolower($s));
    if (count($parts) > 1 && mb_strlen($parts[0]) <= 34 && !preg_match('~[:?]~', $parts[0])) {
        $out['topic'] = array_shift($parts);
        if (isset($communities[$key($out['topic'])])) { $out['community'] = $communities[$key($out['topic'])]; }
    }

    $out['title'] = trim(implode(' | ', $parts));
    if ($out['problem'] !== '') { $out['title'] = ''; }
    return $out;
};

/* ------------------------------------------------------------- the report */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 96) . PHP_EOL;
echo 'FIELD MAP: inventory/legacy/lw-features.json -> the photograph entry type' . PHP_EOL;
echo str_repeat('=', 96) . PHP_EOL;
printf("%-24s %-52s %s\n", 'incoming key', 'Craft field', '');
echo str_repeat('-', 96) . PHP_EOL;
foreach ($MAP as $key => [$target, $why]) {
    printf("%-24s %-52s\n", $key, $target);
    if ($why !== '') { printf("%-24s %s\n", '', $why); }
}
echo str_repeat('-', 96) . PHP_EOL;
echo $UNDECIDED . PHP_EOL;

/* Fields on the photograph type that nothing here fills, so their being empty
   is a decision rather than an oversight. */
$type = Craft::$app->entries->getEntryTypeByHandle($TYPE);
if (!$type) { echo PHP_EOL . 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }
$layoutHandles = [];
foreach ($type->getFieldLayout()->getCustomFields() as $f) { $layoutHandles[] = $f->handle; }
$targets = [];
foreach ($MAP as [$target, $_]) {
    foreach (preg_split('~[,+]~', $target) as $t) {
        $t = trim($t);
        if ($t !== '' && $t[0] !== '(') { $targets[] = $t; }
    }
}
$unfilled = array_values(array_diff($layoutHandles, array_unique($targets)));
echo PHP_EOL . 'left empty by this importer, ' . count($unfilled) . ' fields:' . PHP_EOL;
echo '  ' . implode(', ', $unfilled) . PHP_EOL;

/* ------------------------------------------------------- the parsers, tried */

echo PHP_EOL . str_repeat('=', 96) . PHP_EOL;
echo 'THE SCAN CREDIT PARSER, ON WORKED EXAMPLES' . PHP_EOL;
echo 'Only the first line has been seen. The rest are the shapes the parser is built to survive,' . PHP_EOL;
echo 'and they are guesses about the data, not evidence about it.' . PHP_EOL;
echo str_repeat('=', 96) . PHP_EOL;
$EXAMPLES = [
    '9600 dpi jpeg from original print | source & current location unknown',
    '9600 dpi jpeg from original print',
    'Scanned at 600 dpi TIFF from original negative | Santa Clarita Valley Historical Society',
    '1200 dpi jpeg from a copy print | Ruiz family collection, present whereabouts unknown',
    '9600 dpi jpeg | original print, collection of the photographer',
    'Courtesy of the Santa Clarita Valley Historical Society',
    '',
];
foreach ($EXAMPLES as $ex) {
    $c = $readCredit($ex);
    echo PHP_EOL . '  "' . ($ex === '' ? '(empty)' : $ex) . '"' . PHP_EOL;
    echo '     creditRaw      ' . ($ex === '' ? '(nothing written)' : $ex) . PHP_EOL;
    echo '     creditDpi      ' . ($c['dpi'] ?: '(empty)') . PHP_EOL;
    echo '     creditProcess  ' . ($c['process'] ?: '(empty)') . PHP_EOL;
    echo '     creditKind     ' . ($c['kind'] ?: '(empty)') . PHP_EOL;
    echo '     creditName     ' . ($c['name'] ?: '(empty)') . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 96) . PHP_EOL;
echo 'THE TITLE PARSER, ON REAL TITLES FROM THE CRAWL' . PHP_EOL;
echo str_repeat('=', 96) . PHP_EOL;
$TITLES = [
    ['LW0060 | Castaic | Scallop Shell Fossils', 'lw0060'],
    ['LW060502a | San Francisquito | Ruiz Cemetery on Fire, 2002', 'lw060502a'],
    ['LW2154g | San Francisquito Canyon | Ruiz Cemetery: Nick Rivera, Newhall Innkeeper', 'lw2154g'],
    ['LW102307a | Mojave Desert | Abandoned Tropico Gold Camp, Rosamond', 'lw102307a'],
    ['People | Ramon Perea: A Forgotten Name at Pico.', 'lw021704'],
    ['Canyon Country | Story of Sulphur Springs School', 'lw030597'],
    ['Early California | Have We Sucked the Life Out of Our River?', 'lw100604'],
    ['Santa Clarita Valley History In Pictures - LW0071', 'lw0071'],
    ['LW2745 | SunCal Project Extends Development Up San Francisquito; EIR Misidentifies Burial Site', 'lw2745'],
];
printf("%-11s %-4s %-18s %-24s %s\n", 'code', 'seq', 'topic', 'community', 'title');
echo str_repeat('-', 96) . PHP_EOL;
foreach ($TITLES as [$t, $k]) {
    $r = $readTitle($t, $k);
    printf("%-11s %-4s %-18s %-24s %s\n",
        $r['code'] ?: '-', $r['sequence'] ?: '-', $r['topic'] ?: '-',
        $r['community'] ? $r['community']->title : ($r['topic'] !== '' ? '(not a place we hold)' : '-'),
        $r['problem'] !== '' ? 'SKIPPED, ' . $r['problem'] : $r['title']);
}

/* ------------------------------------------------------------- the real run */

$root = \Craft::getAlias('@root');
$path = $root . '/' . $SOURCE;
echo PHP_EOL . str_repeat('=', 96) . PHP_EOL;
if (!file_exists($path)) {
    echo $SOURCE . ' is not here yet.' . PHP_EOL;
    echo 'Nothing has been read and nothing would be written. When Grok lands the file, run this' . PHP_EOL;
    echo 'again: the mapping above stays at the top and a dry run over the real pages follows it.' . PHP_EOL;
    echo PHP_EOL . 'What it will need from the file, beyond the keys the other inventories carry:' . PHP_EOL;
    echo '  scan_credit_raw   the scan line, verbatim, including the bar and everything after it' . PHP_EOL;
    echo '  source_note_raw   the source note, verbatim' . PHP_EOL;
    echo 'and, for the pictures themselves, a companion lw-features-images.json in the shape of' . PHP_EOL;
    echo 'reynolds-images.json, or import_legacy_images.php has nothing to read.' . PHP_EOL;
    return;
}

$doc = json_decode(file_get_contents($path), true);
$pages = $doc['pages'] ?? array_merge($doc['series_pages'] ?? [], $doc['related_pages'] ?? []);
echo 'read ' . count($pages) . ' pages from ' . $SOURCE . PHP_EOL;

$missingKeys = [];
foreach (['scan_credit_raw', 'source_note_raw'] as $k) {
    $n = 0;
    foreach ($pages as $p) { if (array_key_exists($k, $p)) { $n++; } }
    if ($n === 0) { $missingKeys[] = $k; }
    echo '  ' . str_pad($k, 20) . $n . ' of ' . count($pages) . ' pages carry it' . PHP_EOL;
}
if ($missingKeys) {
    echo 'WARNING: ' . implode(' and ', $missingKeys) . ' is on no page. The credit fields would all be empty.' . PHP_EOL;
}

$byShape = []; $noTitle = []; $topics = []; $noteFragments = []; $codeMismatch = []; $matched = []; $unmatched = ['people' => [], 'places' => [], 'orgs' => []];
$existingByKey = [];
foreach (\craft\elements\Entry::find()->section($SECTION)->status(null)->limit(null)->all() as $e) {
    $existingByKey[(string)$e->legacyKey] = $e;
}
$index = function (string $section) {
    $out = [];
    foreach (\craft\elements\Entry::find()->section($section)->status(null)->limit(null)->all() as $e) {
        $out[mb_strtolower(trim((string)$e->title))] = $e->id;
    }
    return $out;
};
$people = $index('persons'); $places = $index('places'); $orgs = $index('organizations');

$plan = []; $shown = 0;
foreach ($pages as $p) {
    $key = (string)($p['legacy_key'] ?? '');
    $t = $readTitle((string)($p['title'] ?? ''), $key);
    /* The extraction already split the topic out. Prefer its answer to ours. */
    if (array_key_exists('title_topic', $p) && trim((string)$p['title_topic']) !== '') {
        $t['topic'] = trim((string)$p['title_topic']);
        $tk = preg_replace('~[^a-z0-9]~', '', mb_strtolower($t['topic']));
        $t['community'] = $communities[$tk] ?? null;
    }
    if ($t['problem'] !== '') { $noTitle[] = $key . '  ' . ($p['title'] ?? ''); continue; }

    if ($t['topic'] !== '' && !$t['community']) { $topics[$t['topic']] = ($topics[$t['topic']] ?? 0) + 1; }
    $credit = $readCredit((string)($p['scan_credit_raw'] ?? ''));
    $byShape[$credit['shape'] ?: '(no credit line)'][] = $key;
    $srcNote = $readSourceNote((string)($p['source_note_raw'] ?? ''));
    if ($srcNote[1] !== '') { $noteFragments[$key] = mb_substr($srcNote[1], 0, 110); }
    if ($credit['code'] !== '' && $t['code'] !== '' && strcasecmp($credit['code'], $t['code']) !== 0) {
        $codeMismatch[$key] = $t['code'] . ' in the title, ' . $credit['code'] . ' in the credit';
    }

    $rel = [];
    foreach ([['people_mentioned', $people, 'photoPeople', 'people'],
              ['places_mentioned', $places, 'photoPlaces', 'places'],
              ['orgs_mentioned', $orgs, 'photoOrganizations', 'orgs']] as [$src, $idx, $handle, $bucket]) {
        if (!$RELATE) {
            foreach (($p[$src] ?? []) as $name) {
                $n = is_array($name) ? ($name['name_raw'] ?? '') : $name;
                if (trim((string)$n) !== '') {
                    $k = mb_strtolower(trim((string)$n));
                    if (isset($idx[$k])) { $matched[$bucket] = ($matched[$bucket] ?? 0) + 1; }
                    else { $unmatched[$bucket][$n] = ($unmatched[$bucket][$n] ?? 0) + 1; }
                }
            }
            continue;
        }
        foreach (($p[$src] ?? []) as $name) {
            /* The entity lists hold objects keyed name_raw, not name, and a
               plain string in communities_mentioned. Reading the wrong key is
               silent: every mention becomes an empty string and the whole
               relation pass reports nothing at all rather than failing. */
            $n = is_array($name) ? ($name['name_raw'] ?? $name['name'] ?? '') : $name;
            $k = mb_strtolower(trim((string)$n));
            if ($k === '') { continue; }
            if (isset($idx[$k])) { $rel[$handle][] = $idx[$k]; $matched[$bucket] = ($matched[$bucket] ?? 0) + 1; }
            else { $unmatched[$bucket][$n] = ($unmatched[$bucket][$n] ?? 0) + 1; }
        }
    }

    $set = [
        'body' => (string)($p['body_text'] ?? ''),
        'photoCaptionExt' => (string)($p['subtitle'] ?? ''),
        'photoDate' => (string)($p['date_raw'] ?? ''),
        'photoSourceCode' => $t['code'],
        'photoSequence' => (string)($p['series_position'] ?? $t['sequence']),
        'creditRaw' => (string)($p['scan_credit_raw'] ?? ''),
        'creditDpi' => $credit['dpi'],
        'creditProcess' => $credit['process'],
        'creditKind' => $credit['kind'],
        'creditName' => $credit['name'],
        'photoCredit' => $srcNote[0],
        'webmasterNoteBottom' => (string)($p['fine_print_raw'] ?? ''),
        'legacyKey' => $key,
        'legacyUrl' => (string)($p['legacy_path'] ?? ''),
        'sourcePath' => (string)($p['source_url'] ?? ''),
    ];
    if ($t['community']) { $set['neighborhood'] = [$t['community']->id]; }
    foreach ($rel as $h => $ids) { $set[$h] = array_values(array_unique($ids)); }
    $set = array_filter($set, fn($v) => is_array($v) ? count($v) > 0 : trim((string)$v) !== '');

    $plan[] = ['key' => $key, 'title' => $t['title'], 'existing' => $existingByKey[$key] ?? null, 'set' => $set,
               'needsReview' => $p['needs_review'] ?? []];

    if ($shown < $SHOW) {
        $shown++;
        $e = $existingByKey[$key] ?? null;
        echo PHP_EOL . ($e ? 'UPDATE #' . $e->id . '  ' : 'NEW       ') . $t['title'] . '   [' . $key . ']' . PHP_EOL;
        foreach ($set as $h => $v) {
            echo '   ' . str_pad($h, 22) . mb_substr(is_array($v) ? implode(', ', $v) : (string)$v, 0, 92) . PHP_EOL;
        }
        foreach (($p['needs_review'] ?? []) as $nr) {
            echo '   needs_review          ' . ($nr['reason'] ?? '?') . ': ' . mb_substr((string)($nr['detail'] ?? ''), 0, 80) . PHP_EOL;
        }
    }
}

echo PHP_EOL . str_repeat('=', 96) . PHP_EOL;
echo 'pages that would be imported: ' . count($plan) . PHP_EOL;
echo 'pages with no title of their own, held back: ' . count($noTitle) . PHP_EOL;
foreach (array_slice($noTitle, 0, 12) as $n) { echo '   ' . $n . PHP_EOL; }
if (count($noTitle) > 12) { echo '   and ' . (count($noTitle) - 12) . ' more' . PHP_EOL; }

echo PHP_EOL . 'scan credit lines by shape (D dpi, P process, K kind, N name):' . PHP_EOL;
uasort($byShape, fn($a, $b) => count($b) <=> count($a));
foreach ($byShape as $shape => $keys) {
    echo '   ' . str_pad($shape, 8) . str_pad((string)count($keys), 6) . 'e.g. ' . implode(', ', array_slice($keys, 0, 3)) . PHP_EOL;
}
echo 'Anything that is not DPKN or DPK- is worth a look before applying.' . PHP_EOL;

if ($topics) {
    arsort($topics);
    echo PHP_EOL . 'topics in the title that are not a community the archive holds, ' . count($topics) . ' distinct:' . PHP_EOL;
    echo 'These come off the title either way. Whether any of them deserves a category is a decision.' . PHP_EOL;
    $i = 0;
    foreach ($topics as $t => $c) { echo '   ' . str_pad((string)$c, 6) . $t . PHP_EOL; if (++$i >= 20) { break; } }
}

if ($noteFragments) {
    echo PHP_EOL . 'source_note_raw that is body prose rather than a credit: ' . count($noteFragments)
        . ' of ' . (count($plan) + count($noTitle)) . PHP_EOL;
    echo 'Not written anywhere. This is an extraction bug to send back, not data to massage.' . PHP_EOL;
    $i = 0;
    foreach ($noteFragments as $k => $frag) { echo '   ' . str_pad($k, 14) . $frag . PHP_EOL; if (++$i >= 8) { break; } }
    if (count($noteFragments) > 8) { echo '   and ' . (count($noteFragments) - 8) . ' more' . PHP_EOL; }
}

if ($codeMismatch) {
    echo PHP_EOL . 'accession code disagrees between the title and the scan credit: ' . count($codeMismatch) . PHP_EOL;
    $i = 0;
    foreach ($codeMismatch as $k => $why) { echo '   ' . str_pad($k, 14) . $why . PHP_EOL; if (++$i >= 10) { break; } }
    if (count($codeMismatch) > 10) { echo '   and ' . (count($codeMismatch) - 10) . ' more' . PHP_EOL; }
}

foreach ($unmatched as $bucket => $names) {
    if (!$names) { continue; }
    arsort($names);
    echo PHP_EOL . 'mentions of ' . $bucket . ': ' . ($matched[$bucket] ?? 0) . ' matched a record, '
        . array_sum($names) . ' did not (' . count($names) . ' distinct names)' . PHP_EOL;
    $i = 0;
    foreach ($names as $n => $c) { echo '   ' . str_pad((string)$c, 5) . $n . PHP_EOL; if (++$i >= 12) { break; } }
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 96) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$elements = Craft::$app->getElements();
$sectionId = Craft::$app->entries->getSectionByHandle($SECTION)->id;
$created = 0; $updated = 0; $failed = 0;
foreach ($plan as $row) {
    $entry = $row['existing'];
    $isNew = !$entry;
    if ($isNew) {
        $entry = new \craft\elements\Entry();
        $entry->sectionId = $sectionId;
        $entry->typeId = $type->id;
    }
    $entry->title = $row['title'];
    $set = $row['set'];
    /* Never overwrite something a person wrote. */
    if (!$isNew) {
        foreach ($set as $h => $v) {
            $cur = $entry->getFieldValue($h);
            if ($cur instanceof \craft\elements\db\ElementQuery) { $cur = $cur->ids(); }
            if (!(is_array($cur) ? count($cur) === 0 : trim((string)$cur) === '')) { unset($set[$h]); }
        }
    }
    $entry->setFieldValues($set);
    if ($elements->saveElement($entry)) {
        $isNew ? $created++ : $updated++;
    } else {
        $failed++;
        echo 'FAILED ' . $row['key'] . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
    }
}
echo PHP_EOL . 'created ' . $created . ', updated ' . $updated . ', failed ' . $failed . PHP_EOL;
