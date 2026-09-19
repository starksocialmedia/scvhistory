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

/* Every incoming key, and where it goes. This array is the mapping: the report
   below prints it rather than describing it, so the two cannot drift apart. */
$MAP = [
    'title'            => ['title, photoSourceCode, neighborhood', 'Split on the bars: the code, the topic, the caption. See the title parser.'],
    'subtitle'         => ['photoCaptionExt', 'The second line under a picture, where the page carries one.'],
    'body_text'        => ['body', 'Verbatim, as every other importer here. Cleanup is clean_legacy_bodies.php\'s job.'],
    'date_raw'         => ['photoDate', 'As printed. Plain text, because "~1926" and "n.d." are both common and both true.'],
    'scan_credit_raw'  => ['creditRaw + creditDpi, creditProcess, creditKind, creditName', 'Raw always; the four parts where the line parses.'],
    'source_note_raw'  => ['photoCredit', 'NEEDS A DECISION. See the note under the table.'],
    'byline_raw'       => ['(dropped)', 'These pages are not bylined pieces. Reported if any page carries one.'],
    'fine_print_raw'   => ['webmasterNoteBottom', 'The rights and reuse line at the foot of the page.'],
    'editor_notes'     => ['webmasterNoteTop, webmasterNoteBottom', 'By position, as import_perkins.php splits them.'],
    'legacy_key'       => ['legacyKey', ''],
    'legacy_path'      => ['legacyUrl', 'The join to the migration ledger, so an imported page stops reading as missing.'],
    'source_url'       => ['sourcePath', ''],
    'people_mentioned' => ['photoPeople', 'Only where the name matches a person record exactly. The rest are reported.'],
    'places_mentioned' => ['photoPlaces', 'Same rule.'],
    'orgs_mentioned'   => ['photoOrganizations', 'Same rule.'],
    'communities_mentioned' => ['neighborhood', 'Merged with the community read out of the title.'],
    'community_inferred'    => ['neighborhood', 'Used only where the title and the mentions give nothing.'],
    'dates_mentioned'  => ['recordDates', 'printed, iso, granularity, unconfirmed. Same shape as the articles.'],
    'images'           => ['(a second pass)', 'Files are fetched by import_legacy_images.php, which needs lw-features-images.json.'],
    'links_out'        => ['(not used here)', 'export_article_links.php already reads these.'],
    'needs_review'     => ['(reported, not stored)', 'Printed per page so a doubtful extraction is visible before it lands.'],
    'series_position'  => ['photoSequence', 'The order within a run of pages sharing a code, e.g. LW2154a..g.'],
];

$UNDECIDED =
    "source_note_raw -> photoCredit is the one mapping here that is a guess. creditRaw and its four\n"
    . "parts are the scan: resolution, format, what was scanned, where it came from. photoCredit is the\n"
    . "other kind of credit, the one that belongs to the picture rather than to the scanner. If\n"
    . "source_note_raw turns out to be the museum or collection line, photoCredit is right. If it is a\n"
    . "webmaster's aside about the page, webmasterNoteBottom is right and this should change before it\n"
    . "runs. One look at a real value settles it.";

/* ------------------------------------------------------------ the parsers */

/**
 * Reads Leon's scan line. Returns the four parts; any of them may be empty,
 * and the caller keeps the raw line regardless.
 */
$readCredit = function (string $raw): array {
    $out = ['dpi' => '', 'process' => '', 'kind' => '', 'name' => '', 'left' => '', 'shape' => ''];
    $line = trim(preg_replace('~\s+~u', ' ', $raw));
    if ($line === '') { return $out; }

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
    $out['shape'] = ($out['dpi'] !== '' ? 'D' : '-') . ($out['process'] !== '' ? 'P' : '-')
                  . ($out['kind'] !== '' ? 'K' : '-') . ($out['name'] !== '' ? 'N' : '-');
    return $out;
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

$byShape = []; $noTitle = []; $topics = []; $unmatched = ['people' => [], 'places' => [], 'orgs' => []];
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
    if ($t['problem'] !== '') { $noTitle[] = $key . '  ' . ($p['title'] ?? ''); continue; }

    if ($t['topic'] !== '' && !$t['community']) { $topics[$t['topic']] = ($topics[$t['topic']] ?? 0) + 1; }
    $credit = $readCredit((string)($p['scan_credit_raw'] ?? ''));
    $byShape[$credit['shape'] ?: '(no credit line)'][] = $key;

    $rel = [];
    foreach ([['people_mentioned', $people, 'photoPeople', 'people'],
              ['places_mentioned', $places, 'photoPlaces', 'places'],
              ['orgs_mentioned', $orgs, 'photoOrganizations', 'orgs']] as [$src, $idx, $handle, $bucket]) {
        foreach (($p[$src] ?? []) as $name) {
            $n = is_array($name) ? ($name['name'] ?? '') : $name;
            $k = mb_strtolower(trim((string)$n));
            if ($k === '') { continue; }
            if (isset($idx[$k])) { $rel[$handle][] = $idx[$k]; }
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
        'photoCredit' => (string)($p['source_note_raw'] ?? ''),
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

foreach ($unmatched as $bucket => $names) {
    if (!$names) { continue; }
    arsort($names);
    echo PHP_EOL . 'named on a page and not a record, ' . $bucket . ': ' . count($names) . ' distinct' . PHP_EOL;
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
