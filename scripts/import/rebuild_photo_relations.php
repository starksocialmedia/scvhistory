/**
 * Rebuilds the photograph relation layer, which is empty.
 *
 * 1,544 photograph records carry no photoPeople, photoPlaces,
 * photoOrganizations, photoGroups, photoEvents or photoArticles at all: the
 * relations table holds not one row for any of them. Everything the archive
 * knows about who and what is in a picture is sitting in the crawl and in the
 * captions, unread. This reads it.
 *
 * WHAT COUNTS AS EVIDENCE
 *
 * The picture's own words: its title and its caption. Nothing else writes.
 *
 * It read the page's prose too, by two routes — the crawl's mention lists and
 * the record's body — and wrote where they agreed. They are not two routes.
 * Every one of the crawl's mention names appears in that page's own body_text,
 * measured at 100% over 4,599 mentions, because the mention lists were
 * extracted from the prose the body was imported from. Agreement between them
 * was one piece of evidence counted twice, which is why the agreed set was
 * always exactly the crawl's set: 1,116 and 1,116.
 *
 * The prose is the wrong evidence anyway. A name in the body says the article
 * mentions it, not that the picture shows it. Sampled on 23 September:
 *
 *   Tom Mix        0 of 5 were pictures of him. A Hart lobby card, a rodeo
 *                  ribbon and two ticket stubs, each naming him in passing:
 *                  "stars such as William S. Hart, Harry Carey, Tom Mix".
 *   Elizabeth Lake 1 of 5. The rest were Munz Lakes Resort on Elizabeth Lake
 *                  ROAD, and gallery navigation lists.
 *
 * Where the name is in the title or the caption, the picture is of the thing.
 * So that is the rule, and it writes 171 relations across 156 photographs
 * rather than 2,632 across 1,129. The prose evidence is not kept as a weaker
 * signal: it is queued, which is where "Tom Mix was also a star" belongs.
 *
 * photoArticles is separate and unaffected: it comes from the image-link layer,
 * which records that an article's thumbnail points at this photograph. That is
 * a fact about the link, not an inference about a subject. Only layers sitting
 * on an article are read; most of that layer is one photograph pointing at
 * another, which is a gallery link.
 *
 * WHAT THE NUMBERS MEAN, FOR ANYONE READING THEM LATER
 *
 * The 31 photoPeople relations this wrote on 23 September are a floor, not a
 * finished job. They are bounded by two things that will move: there are 83
 * person records in the archive to match against, and the captions rarely name
 * anybody. Re-run this after the person records grow — the editorial profiles
 * track adds them — and expect the number to rise without anything here
 * changing.
 *
 * A high count is not photographic presence. John C. Frémont ends up with 8,
 * and all 8 are scans of one object: "Circle of Friends Medal No. 9: John C.
 * Fremont". The medal carries his portrait, so the relation is sound, but the
 * archive holds no photograph of the man. Read a person's count as "pictures
 * whose own title or caption names them", which is what it is.
 *
 * Read the queue at web/review/photo-links.json.
 *
 * Idempotent: a relation already on the record is left alone and never
 * duplicated; a record whose field already holds something is added to, never
 * replaced.
 *
 * Dry run by default.
 * Run:   ddev craft exec "eval(file_get_contents('scripts/import/rebuild_photo_relations.php'))"
 * Apply: ddev craft exec '$PHOTO_APPLY = true; eval(file_get_contents("scripts/import/rebuild_photo_relations.php"));'
 */

$APPLY = false;
if (!empty($PHOTO_APPLY)) { $APPLY = true; echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$root = \Craft::getAlias('@root');
$FIELD_FOR = ['persons' => 'photoPeople', 'places' => 'photoPlaces', 'organizations' => 'photoOrganizations',
              'groups' => 'photoGroups', 'events' => 'photoEvents'];
$QUEUE = \Craft::getAlias('@webroot') . '/review/photo-links.json';

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

/* ------------------------------------------------------------- the names */

$norm = function (string $s): string {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
    return trim(preg_replace('~\s+~', ' ', preg_replace('~[^a-z0-9 ]~', ' ', mb_strtolower($s))));
};

$byName = [];   /* normalised name => ['section'=>..,'id'=>..,'title'=>..] */
$records = 0;
foreach (array_keys($FIELD_FOR) as $section) {
    foreach (\craft\elements\Entry::find()->section($section)->status(null)->limit(null)->each() as $e) {
        $records++;
        $names = [$e->title];
        foreach (['personAliases', 'orgAliases'] as $h) {
            if ($e->getFieldLayout()?->getFieldByHandle($h)) {
                foreach (preg_split('~[;|\n]~', (string)$e->getFieldValue($h)) as $a) { $names[] = $a; }
            }
        }
        foreach ($names as $nm) {
            $n = $norm($nm);
            if (mb_strlen($n) >= 6 && !isset($byName[$n])) {
                $byName[$n] = ['section' => $section, 'id' => (int)$e->id, 'title' => $e->title];
            }
        }
    }
}

/* The canon folds spellings together; the splits are the names it refuses to
   fold, and this refuses them too. */
$ambiguous = [];
$canonPath = $root . '/inventory/legacy/name-canon.json';
$foldedIn = 0;
if (is_file($canonPath)) {
    $canon = json_decode((string)file_get_contents($canonPath), true);
    foreach (($canon['canon'] ?? []) as $c) {
        $target = $byName[$norm($c['canonical'] ?? '')] ?? null;
        if (!$target) { continue; }
        foreach (($c['aliases'] ?? []) as $a) {
            $n = $norm($a);
            if (mb_strlen($n) >= 6 && !isset($byName[$n])) { $byName[$n] = $target; $foldedIn++; }
        }
    }
    foreach (($canon['splits'] ?? []) as $sp) { $ambiguous[$norm($sp['name'] ?? '')] = $sp['note'] ?? 'listed as ambiguous in the canon'; }
}
echo 'records to match against: ' . $records . ' | spellings: ' . count($byName)
   . ' (' . $foldedIn . ' folded in from the canon) | names the canon refuses to fold: ' . count($ambiguous) . PHP_EOL;

/* ------------------------------------------------------------- the crawl */

$pathKey = function (string $s): string {
    $p = parse_url(trim($s), PHP_URL_PATH) ?: trim($s);
    return trim(mb_strtolower($p), '/');
};
$crawl = [];
foreach (glob($root . '/inventory/legacy/*.json') as $f) {
    if (filesize($f) > 200 * 1024 * 1024) { continue; }
    $d = json_decode((string)file_get_contents($f), true);
    if (!is_array($d)) { continue; }
    $pages = array_is_list($d) ? $d : array_merge($d['pages'] ?? [], $d['series_pages'] ?? [], $d['related_pages'] ?? []);
    foreach ($pages as $p) {
        if (!is_array($p)) { continue; }
        $k = $pathKey((string)($p['legacy_path'] ?? $p['source_url'] ?? ''));
        if ($k !== '' && !isset($crawl[$k])) { $crawl[$k] = $p; }
    }
}
echo 'crawled pages on file: ' . count($crawl) . PHP_EOL;

/* Route C: which article's thumbnail points at which photograph.
   The layer is keyed by the record the thumbnail sits on, and most of those
   records are photographs rather than articles: a gallery page pointing at the
   next picture. That is a link between photographs, not a photograph's article,
   and writing it into photoArticles would have put 13,779 relations in the
   wrong field. Only article sources are read here; the photograph-to-photograph
   links are counted and reported, for relatedPhotographs to decide on its own. */
$layers = glob($root . '/templates/_data/image-links/*.json');
$sourceIds = array_map(fn($f) => (int)basename($f, '.json'), $layers);
$articleIds = array_flip(array_map('intval', \craft\elements\Entry::find()
    ->section('articles')->status(null)->id($sourceIds)->limit(null)->ids()));
$articleFor = []; $photoToPhoto = 0;
foreach ($layers as $f) {
    $sourceId = (int)basename($f, '.json');
    $d = json_decode((string)file_get_contents($f), true);
    foreach (($d['links'] ?? []) as $link) {
        $target = is_array($link) ? (int)($link['id'] ?? 0) : (int)$link;
        if (!$target) { continue; }
        if (isset($articleIds[$sourceId])) { $articleFor[$target][$sourceId] = true; }
        else { $photoToPhoto++; }
    }
}
echo 'image-link layers: ' . count($layers) . ', of which ' . count($articleIds) . ' sit on an article' . PHP_EOL;
echo 'links from one photograph to another, not written here: ' . $photoToPhoto . PHP_EOL . PHP_EOL;

/* ------------------------------------------------------------- the plan */

$plan = []; $queue = []; $unmatched = []; $skippedAmbiguous = []; $creditQueue = [];
$statA = $statB = $statAgree = $statC = 0; $creditDropped = 0;

foreach (\craft\elements\Entry::find()->section('photographs')->status(null)->limit(null)->each() as $ph) {
    $layout = $ph->getFieldLayout();
    /* The credit is not the subject, and it is not always in a credit field.
       "LW9502: 19200 dpi jpeg from original photograph (BW film print) by Leon
       Worden" sits in the body; "Photos by Leon Worden" sits in the caption. A
       filter reading only the credit fields left Leon attached to 423
       photographs, a quarter of the archive, as though he were pictured in them.

       So the credit text is the credit fields plus the credit phrases found in
       the record's own words. The cues are specific on purpose: a bare "by"
       also appears in "the depot built by Southern Pacific", where Southern
       Pacific is the subject. A name found only this way is not dropped in
       silence either, it is queued as something that reads like a credit. */
    $creditFields = implode(' ', array_map(fn($h) => $layout?->getFieldByHandle($h) ? (string)$ph->getFieldValue($h) : '',
        ['photoCredit', 'creditRaw', 'creditName']));
    $ownWords = implode("\n", [
        $ph->title,
        $layout?->getFieldByHandle('photoCaptionExt') ? (string)$ph->getFieldValue('photoCaptionExt') : '',
        $layout?->getFieldByHandle('body') ? (string)$ph->getFieldValue('body') : '',
    ]);
    $CREDIT_CUES = '~(?:^|\n)\s*(?:by|photos? by)\s+[^\n.;|]{2,60}'
                 /* The cue can sit a long way from the "by": "LW3783: 9600 dpi
                    jpeg from original (1985 duplicate) color transparency
                    purchased 2021 by Leon Worden". */
                 . '|(?:photo|photos|photograph|photographed|image|scan|scanned|digitized|print|negative|transparency|slide|jpeg|copy|courtesy|collection|purchased|acquired|bought|donated|loaned)'
                 . '[^\n.;|]{0,90}?\bby\s+[^\n.;|]{2,60}'
                 /* A signature under a note: "— Leon Worden, 2012". */
                 . '|[\x{2014}\x{2013}-]\s*[A-Z][^\n.;|]{2,40},?\s*(?:19|20)\d\d'
                 . '|courtesy\s+of\s+[^\n.;|]{2,60}'
                 . '|collection\s+of\s+[^\n.;|]{2,60}'
                 . '|\bdonated\s+by\s+[^\n.;|]{2,60}'
                 . '|\bprovided\s+by\s+[^\n.;|]{2,60}'
                 . '|\x{00A9}\s*[^\n.;|]{2,60}~iu';
    $creditSpans = preg_match_all($CREDIT_CUES, $ownWords, $cm) ? implode(' ', $cm[0]) : '';
    $credit = $norm($creditFields . ' ' . $creditSpans);
    $creditPhraseOnly = $norm($creditSpans);

    $page = null;
    foreach (['legacyKey', 'legacyUrl', 'sourcePath'] as $h) {
        $k = $pathKey((string)$ph->getFieldValue($h));
        if ($k !== '' && isset($crawl[$k])) { $page = $crawl[$k]; break; }
    }

    /* The evidence that writes: the picture's own words. */
    $ownWords = ' ' . $norm($ph->title . ' '
        . ($layout?->getFieldByHandle('photoCaptionExt') ? (string)$ph->getFieldValue('photoCaptionExt') : '')) . ' ';
    $titleCaption = [];
    foreach ($byName as $n => $rec) {
        if (isset($ambiguous[$n])) { $skippedAmbiguous[$n] = ($skippedAmbiguous[$n] ?? 0) + 1; continue; }
        if ($credit !== '' && str_contains($credit, $n)) {
            $creditDropped++;
            if ($creditPhraseOnly !== '' && str_contains($creditPhraseOnly, $n)) { $creditQueue[$rec['title']] = ($creditQueue[$rec['title']] ?? 0) + 1; }
            continue;
        }
        if (str_contains($ownWords, ' ' . $n . ' ')) { $titleCaption[$rec['section'] . ':' . $rec['id']] = $rec + ['via' => $n]; }
    }

    /* The evidence that queues: the page's prose, by either reading of it. */
    $prose = ' ' . $norm($layout?->getFieldByHandle('body') ? (string)$ph->getFieldValue('body') : '') . ' ';
    $fromProse = [];
    foreach ($byName as $n => $rec) {
        if (isset($ambiguous[$n])) { continue; }
        if ($credit !== '' && str_contains($credit, $n)) { continue; }
        if (isset($titleCaption[$rec['section'] . ':' . $rec['id']])) { continue; }
        if (str_contains($prose, ' ' . $n . ' ')) { $fromProse[$rec['section'] . ':' . $rec['id']] = $rec + ['via' => $n]; }
    }
    foreach (['people_mentioned', 'places_mentioned', 'orgs_mentioned'] as $m) {
        foreach ((array)($page[$m] ?? []) as $it) {
            $raw = is_array($it) ? (string)($it['name_raw'] ?? '') : (string)$it;
            $n = $norm($raw);
            if ($n === '' || isset($ambiguous[$n])) { continue; }
            if ($credit !== '' && str_contains($credit, $n)) { continue; }
            $rec = $byName[$n] ?? null;
            if ($rec && !isset($titleCaption[$rec['section'] . ':' . $rec['id']])) {
                $fromProse[$rec['section'] . ':' . $rec['id']] = $rec + ['via' => $raw];
            } elseif (!$rec && mb_strlen($n) >= 6) {
                $unmatched[$raw] = ($unmatched[$raw] ?? 0) + 1;
            }
        }
    }

    $A = $titleCaption; $B = $fromProse;
    if ($A) { $statA++; }

    if ($B) { $statAgree++; }

    $write = [];
    foreach ($A as $rec) {
        $field = $FIELD_FOR[$rec['section']];
        if (!$layout?->getFieldByHandle($field)) { continue; }
        $write[$field][] = $rec['id'];
    }
    /* C, on its own evidence. */
    $articles = array_keys($articleFor[(int)$ph->id] ?? []);
    if ($articles && $layout?->getFieldByHandle('photoArticles')) { $write['photoArticles'] = $articles; $statC++; }

    /* Additive and idempotent: keep what is there, add what is new. */
    $set = [];
    foreach ($write as $field => $ids) {
        $have = $ph->getFieldValue($field)->status(null)->ids();
        $merged = array_values(array_unique(array_merge(array_map('intval', $have), array_map('intval', $ids))));
        if (count($merged) !== count($have)) { $set[$field] = $merged; }
    }
    if ($set) { $plan[(int)$ph->id] = ['id' => (int)$ph->id, 'title' => $ph->title, 'set' => $set]; }

    if ($B) {
        $queue[] = [
            'photograph' => (int)$ph->id, 'title' => $ph->title, 'url' => $ph->getCpEditUrl(),
            'namedInTheProseOnly' => array_values(array_map(
                fn($r) => ['record' => $r['section'] . ' #' . $r['id'], 'name' => $r['title'], 'saw' => $r['via']], $B)),
        ];
    }
}

$fieldTotals = []; $perTarget = [];
foreach ($plan as $p) {
    foreach ($p['set'] as $f => $ids) {
        $fieldTotals[$f] = ($fieldTotals[$f] ?? 0) + count($ids);
        if ($f === 'photoArticles') { continue; }
        foreach ($ids as $i) { $perTarget[$i] = ($perTarget[$i] ?? 0) + 1; }
    }
}
arsort($perTarget);

echo 'photographs: ' . \craft\elements\Entry::find()->section('photographs')->status(null)->count() . PHP_EOL;
echo '   named in the title or caption       ' . $statA . '  (this is what writes)' . PHP_EOL;
echo '   named only in the page prose        ' . $statAgree . '  (queued, never written)' . PHP_EOL;
echo '   photoArticles from the link layer   ' . $statC . PHP_EOL;
echo '   names dropped for being the credit  ' . $creditDropped
   . ' (' . count($creditQueue) . ' of them found by a credit phrase in the record\'s own words, queued)' . PHP_EOL;
echo '   ambiguous names refused             ' . count($skippedAmbiguous) . PHP_EOL;
echo PHP_EOL . 'records to write: ' . count($plan) . PHP_EOL;
foreach ($fieldTotals as $f => $n) { echo '   ' . str_pad($f, 22) . $n . ' relations' . PHP_EOL; }
/* Who ends up in the most pictures. A name at the top of this list is either
   the valley's most photographed figure or somebody's byline leaking through
   the filter, and the difference is worth seeing before anything is written. */
echo PHP_EOL . 'most-linked records, which is where a leak would show:' . PHP_EOL;
foreach (array_slice($perTarget, 0, 10, true) as $id => $n) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    echo '   ' . str_pad((string)$n, 6) . '#' . $id . ' ' . ($e?->title ?? '?') . ' (' . ($e?->section->handle ?? '?') . ')' . PHP_EOL;
}

echo PHP_EOL . 'these counts are a floor: 83 person records to match against, and captions that rarely name'
   . ' anybody. A high count is not photographic presence — Frémont\'s are eight scans of one medal.' . PHP_EOL;

echo PHP_EOL . 'to the queue: ' . count($queue) . ' photographs named only in the prose, '
   . count($unmatched) . ' names matching no record, ' . count($skippedAmbiguous) . ' ambiguous' . PHP_EOL;

arsort($unmatched);
$out = [
    'meta' => [
        'generated' => date('c'),
        'generated_by' => 'scripts/import/rebuild_photo_relations.php',
        'mode' => $APPLY ? 'applied' : 'dry run',
        'rule' => 'written only where the picture\'s own title or caption names the record; a name in the page prose is queued here, unwritten',
        'theNumbersAreAFloor' => 'Bounded by 83 person records and by captions that rarely name people. Re-run as person records grow.',
        'countIsNotPresence' => 'A person\'s count is pictures whose title or caption names them. Frémont\'s 8 are eight scans of one medal bearing his portrait, not photographs of the man.',
    ],
    'namedOnlyInProse' => $queue,
    'namesWithNoRecord' => array_slice(array_map(fn($k, $v) => ['name' => $k, 'seen' => $v], array_keys($unmatched), $unmatched), 0, 1200),
    'ambiguous' => array_map(fn($k, $v) => ['name' => $k, 'seen' => $v, 'why' => $ambiguous[$norm($k)] ?? ''], array_keys($skippedAmbiguous), $skippedAmbiguous),
    'readAsCredit' => array_map(fn($k, $v) => ['name' => $k, 'seen' => $v,
        'why' => 'found in a credit phrase in the record\'s own words, not in a credit field'],
        array_keys($creditQueue), $creditQueue),
];
@mkdir(dirname($QUEUE), 0775, true);
file_put_contents($QUEUE, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo 'queue written to web/review/photo-links.json' . PHP_EOL;

echo PHP_EOL . 'sample of what would be written:' . PHP_EOL;
foreach (array_slice($plan, 0, 8, true) as $p) {
    $bits = [];
    foreach ($p['set'] as $f => $ids) { $bits[] = $f . ' ' . implode(',', array_map(fn($i) => '#' . $i, array_slice($ids, 0, 4))) . (count($ids) > 4 ? '…' : ''); }
    echo '   #' . $p['id'] . ' ' . mb_substr($p['title'], 0, 44) . ': ' . implode(' | ', $bits) . PHP_EOL;
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
    echo 'nothing was written. Pass $PHOTO_APPLY = true to apply.' . PHP_EOL;
    return;
}

$saved = 0; $failed = [];
foreach ($plan as $p) {
    $e = \craft\elements\Entry::find()->id($p['id'])->status(null)->one();
    foreach ($p['set'] as $f => $ids) { $e->setFieldValue($f, $ids); }
    if (Craft::$app->getElements()->saveElement($e)) { $saved++; }
    else { $failed[] = '#' . $p['id'] . ': ' . json_encode($e->getFirstErrors()); }
}
echo PHP_EOL . 'saved ' . $saved . ' of ' . count($plan) . PHP_EOL;
foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }

$ok = 0; $short = [];
foreach ($plan as $p) {
    $e = \craft\elements\Entry::find()->id($p['id'])->status(null)->one();
    $bad = [];
    foreach ($p['set'] as $f => $ids) {
        $back = array_map('intval', $e->getFieldValue($f)->status(null)->ids());
        if (array_diff($ids, $back)) { $bad[] = $f; }
    }
    if ($bad) { $short[] = '#' . $p['id'] . ' ' . implode(', ', $bad); } else { $ok++; }
}
$readback = ($short ? 'SHORT ' : 'verified ') . $ok . ' of ' . count($plan);
echo 'READ-BACK ' . $readback . PHP_EOL;
foreach (array_slice($short, 0, 10) as $m) { echo '  ' . $m . PHP_EOL; }

$total = 0;
foreach ($FIELD_FOR as $f) { $total += (new \craft\db\Query())->from('{{%relations}}')
    ->where(['fieldId' => Craft::$app->getFields()->getFieldByHandle($f)->id])->count(); }
echo 'relation rows across the photo fields now: ' . $total . ' (was 0)' . PHP_EOL;

$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('rebuild_photo_relations.php', $saved, $readback,
    'agreements only; ' . count($queue) . ' disagreements and ' . count($unmatched) . ' unmatched names queued in web/review/photo-links.json');
if ($short || $failed) { throw new \RuntimeException('rebuild_photo_relations: the write did not land as planned.'); }
