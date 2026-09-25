/**
 * Sorts the entity and relation review queues into three buckets.
 *
 * 4,346 items is not a queue, it is a wall. Nobody starts a 4,346-item review
 * and the ones that matter most are buried among the ones that matter least. The
 * point of triage is not to decide things automatically; it is to make the
 * pile that needs a person small enough that a person will start it.
 *
 * THE THREE BUCKETS
 *
 *   accept   an exact name match to exactly ONE existing record, with the
 *            collection or era consistent. Written with provenance auto/exact,
 *            and reversible from the review screen, because a confident rule
 *            applied 900 times will be wrong some of those times and the only
 *            acceptable answer to that is an undo.
 *
 *   reject   no record anywhere, and mentioned ONCE in the whole corpus.
 *            Parked rather than deleted: a name that appears once today may
 *            appear five more times when Worden and the Old Town runs import,
 *            and a parked list can be re-triaged where a deleted one cannot.
 *
 *   review   everything else, sorted by how many distinct articles the name
 *            appears in, most first. That ordering is the whole value: a name
 *            in eleven articles with no record is a missing record, and it is
 *            worth more than eleven names in one article each.
 *
 * WHAT MAKES AN ACCEPT SAFE
 *
 * Exactly one match, on the normalised full name, in the right section. Two
 * matches is ambiguity and goes to review. Zero is either a rejection or a
 * missing record. A near-match is never an accept: "Henry Mayo Newhall" and
 * "Henry M. Newhall" may be the same man and a script does not get to decide
 * that.
 *
 * Era and collection consistency: where the candidate names an article that
 * belongs to a collection, and the matched record is already related to a
 * DIFFERENT collection, that is a coincidence of names rather than a match, and
 * it goes to review.
 *
 * Read only. It writes a report and a proposed bucketing and changes nothing.
 * There is no $APPLY, because applying is a separate decision from sorting.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/triage_review_queues.php'))"
 */

$REVIEW = \Craft::getAlias('@review');

/* Which pair of queue files to sort. Empty is the live queue; '-full' is the
   whole-corpus export, which is kept beside it so a full run never overwrites
   the queue a person is part way through.

   Set it on the command line:
     ddev craft exec "\$TRIAGE_SUFFIX='-full'; eval(file_get_contents('scripts/import/triage_review_queues.php'))" */
$SUFFIX = $TRIAGE_SUFFIX ?? '';
$OUT    = $REVIEW . '/triage' . $SUFFIX . '.json';
$REPORT = $REVIEW . '/triage' . $SUFFIX . '.md';

$norm = function (string $s): string {
    $s = mb_strtolower(trim($s), 'UTF-8');
    $s = preg_replace('/[^a-z0-9 ]/', '', $s);
    return trim(preg_replace('/\s+/', ' ', $s));
};

/* A rank is not a different man. "Dr. Sol Taylor" is on 224 articles and record
   #2582 is titled "Sol Taylor", and matching on the exact string reports the
   biggest missing record in the archive for a record the archive already holds.
   The entity exporter has stripped honorifics for this reason from the start;
   the triage matching without doing so is the two halves disagreeing.
 *
 * MRS, MISS, MS and SISTER are deliberately NOT stripped. "Mrs. George LeBrun"
 * is how the nineteenth century names a woman by her husband, and stripping it
 * matches her to his record and links her articles to him. That is not a near
 * miss, it is erasing her, and it is the one case where the looser rule does
 * real damage. Those keep their honorific and stay distinct. */
$STRIPPABLE = 'mr|dr|doctor|fr|father|capt|captain|col|colonel|gen|general|lt|lieutenant|rev|reverend|'
            . 'sgt|sergeant|maj|major|don|senor|judge|gov|governor|prof|professor|sheriff|deputy|chief|mayor';
$matchKey = function (string $s) use ($norm, $STRIPPABLE): string {
    $s = $norm($s);
    do { $b = $s; $s = preg_replace('~^(' . $STRIPPABLE . ')\s+~', '', $s); } while ($s !== $b);
    return trim($s);
};

/* Every record we hold, by normalised title and section, so a match is a
   lookup rather than a query per candidate. */
/* Titles AND aliases. A record answers to every name it is filed under, and
   indexing only the title reports "Dr. Sol Taylor" as a missing record on 224
   articles while #2582 sits there titled "Sol Taylor". The alias list is where
   the archive records that they are the same man, so the matcher has to read
   it or the alias does nothing.

   The field is read off the layout rather than tested with `is defined`, which
   reads as true on a type that does not have it and then throws. */
$ALIAS_FIELD = ['persons' => 'personAliases', 'places' => 'placeAliases', 'organizations' => 'orgAliases'];

$bySection = [];
$aliasCount = 0;
foreach (\craft\elements\Entry::find()->limit(null)->status(null)->all() as $e) {
    $sec = $e->section->handle;
    $t = $matchKey((string)$e->title);
    if ($t !== '') { $bySection[$sec][$t][] = $e; }

    $af = $ALIAS_FIELD[$sec] ?? null;
    if (!$af) { continue; }
    $has = false;
    foreach ($e->getFieldLayout()->getCustomFields() as $f) {
        if ($f->handle === $af) { $has = true; break; }
    }
    if (!$has) { continue; }
    try { $raw = (string)$e->getFieldValue($af); } catch (\Throwable $ex) { continue; }
    foreach (preg_split('~\r\n|\n|\r|,~', $raw) as $al) {
        $k = $matchKey($al);
        if ($k === '' || $k === $t) { continue; }
        if (isset($bySection[$sec][$k]) && in_array($e, $bySection[$sec][$k], true)) { continue; }
        $bySection[$sec][$k][] = $e;
        $aliasCount++;
    }
}
if ($aliasCount) { echo 'alias spellings indexed: ' . $aliasCount . PHP_EOL; }
$counts = [];
foreach ($bySection as $s => $m) { $counts[] = $s . ' ' . count($m); }
echo 'records indexed by title: ' . implode(', ', $counts) . PHP_EOL;

/* Which collection each article belongs to, for the consistency test. */
$collOf = [];
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->all() as $a) {
    foreach ($a->getFieldLayout()->getCustomFields() as $f) {
        if ($f->handle === 'partOfCollection') {
            $c = $a->partOfCollection->one();
            if ($c) { $collOf[$a->id] = $c->id; }
        }
    }
}

$KIND_SECTION = [
    'person' => 'persons', 'people' => 'persons',
    'place' => 'places', 'organization' => 'organizations',
    'group' => 'groups', 'event' => 'events',
];

/* SIMULATED RECORDS.
 *
 * "Re-run the triage after the top fifty" is a question about a database state
 * that does not exist yet and must not be created to answer it. $ASSUME_RECORDS
 * injects names as though their records had been made, so the shrink can be
 * measured before anything is written.
 *
 * They are marked simulated, and the collection-consistency test skips them: a
 * record that does not exist belongs to no collection, and treating its absence
 * as a mismatch would send every one of them to review and report the opposite
 * of the truth.
 *
 *   ddev craft exec "$ASSUME_RECORDS=json_decode(file_get_contents('/var/www/html/review/assume.json'),true); eval(...)" */
$simulated = 0;
foreach (($ASSUME_RECORDS ?? []) as $a) {
    $t = $matchKey((string)($a['name'] ?? ''));
    $sec = $KIND_SECTION[strtolower((string)($a['type'] ?? ''))] ?? null;
    if ($t === '' || !$sec) { continue; }
    $fake = new \stdClass();
    $fake->id = 0; $fake->url = ''; $fake->simulated = true;

    /* The title AND every alias the approval would write. A record titled
       Antonio, created by merging "Don Antonio", "Lt. Antonio" and "Lieutenant
       Antonio", is reachable from all three names once it exists, and a
       simulation that registers only the bare title reports it as matching
       nothing at all. */
    $spellings = array_merge([$a['name'] ?? ''], (array)($a['variants'] ?? []));
    $added = false;
    foreach ($spellings as $sp) {
        $k = $matchKey((string)$sp);
        if ($k === '' || isset($bySection[$sec][$k])) { continue; }
        $bySection[$sec][$k][] = $fake;
        $added = true;
    }
    if ($added) { $simulated++; }
}
if ($simulated) { echo 'simulated records injected: ' . $simulated . PHP_EOL; }


/* ------------------------------------------------------------ relations */

$relPath = $REVIEW . '/relations' . $SUFFIX . '.json';
$rel = file_exists($relPath) ? json_decode(file_get_contents($relPath), true) : ['candidates' => []];
$cands = $rel['candidates'] ?? [];

/* How often each name appears across the whole queue, which is the corpus-wide
   occurrence the reject rule needs. A name seen once in one article is a
   different thing from a name seen once in each of nine. */
$corpus = [];
foreach ($cands as $c) {
    $n = $norm((string)($c['name'] ?? ''));
    if ($n === '') { continue; }
    $corpus[$n]['rows'] = ($corpus[$n]['rows'] ?? 0) + 1;
    $corpus[$n]['articles'][(string)($c['entryId'] ?? '?')] = true;
    $corpus[$n]['mentions'] = ($corpus[$n]['mentions'] ?? 0) + (int)($c['mentionCount'] ?? 0);
}

$buckets = ['accept' => [], 'reject' => [], 'review' => []];
$why = [];

foreach ($cands as $c) {
    $name = trim((string)($c['name'] ?? ''));
    $n = $matchKey($name);
    if ($n === '') { continue; }
    $section = $KIND_SECTION[strtolower((string)($c['kind'] ?? ''))] ?? null;
    $matches = ($section && isset($bySection[$section][$n])) ? $bySection[$section][$n] : [];
    $articles = count($corpus[$n]['articles'] ?? []);
    $mentions = (int)($corpus[$n]['mentions'] ?? 0);

    $row = ['q' => 'relations', 'name' => $name, 'kind' => (string)($c['kind'] ?? ''),
            'field' => (string)($c['field'] ?? ''), 'entryId' => $c['entryId'] ?? null,
            'article' => (string)($c['articleTitle'] ?? ''),
            'articles' => $articles, 'mentions' => $mentions,
            'matchCount' => count($matches),
            'matchId' => count($matches) === 1 ? $matches[0]->id : null,
            'matchUrl' => count($matches) === 1 ? $matches[0]->url : null,
            'simulated' => count($matches) === 1 && isset($matches[0]->simulated)];

    if (count($matches) === 1) {
        /* Collection consistency: a match already tied to a different
           collection than the citing article is a name coincidence. */
        $ok = true;
        $m = $matches[0];
        if (!isset($m->simulated) && isset($collOf[$c['entryId'] ?? 0])) {
            foreach ($m->getFieldLayout()->getCustomFields() as $f) {
                if ($f->handle === 'partOfCollection') {
                    $mc = $m->partOfCollection->one();
                    if ($mc && $mc->id !== $collOf[$c['entryId']]) { $ok = false; }
                }
            }
        }
        if ($ok) {
            $row['provenance'] = 'auto/exact';
            $buckets['accept'][] = $row;
            $why['exact one match'] = ($why['exact one match'] ?? 0) + 1;
            continue;
        }
        $row['note'] = 'match belongs to a different collection';
        $buckets['review'][] = $row;
        $why['collection mismatch'] = ($why['collection mismatch'] ?? 0) + 1;
        continue;
    }

    if (count($matches) === 0 && $articles <= 1 && $mentions <= 1) {
        $row['note'] = 'no record, seen once in the corpus';
        $buckets['reject'][] = $row;
        $why['no record, single occurrence'] = ($why['no record, single occurrence'] ?? 0) + 1;
        continue;
    }

    $row['note'] = count($matches) > 1 ? (count($matches) . ' records share this name') : 'no record';
    $buckets['review'][] = $row;
    $why[count($matches) > 1 ? 'ambiguous, several records' : 'no record, recurs'] =
        ($why[count($matches) > 1 ? 'ambiguous, several records' : 'no record, recurs'] ?? 0) + 1;
}

/* ------------------------------------------------------------- entities */

$entPath = $REVIEW . '/entities' . $SUFFIX . '.json';
$ent = file_exists($entPath) ? json_decode(file_get_contents($entPath), true) : ['pairs' => []];
$pairs = $ent['pairs'] ?? [];

foreach ($pairs as $p) {
    $a = trim((string)($p['a'] ?? '')); $b = trim((string)($p['b'] ?? ''));
    $shared = (int)($p['shared'] ?? 0);
    $aEx = !empty($p['aExisting']); $bEx = !empty($p['bExisting']);
    $arts = max((int)($p['aArticles'] ?? 0), (int)($p['bArticles'] ?? 0));

    $row = ['q' => 'entities', 'name' => $a . '  =  ' . $b, 'kind' => (string)($p['kind'] ?? ''),
            'articles' => $arts, 'mentions' => (int)($p['aMentions'] ?? 0) + (int)($p['bMentions'] ?? 0),
            'shared' => $shared, 'aExisting' => $aEx, 'bExisting' => $bEx];

    /* A merge is never auto-accepted. Merging two records is destructive and
       irreversible in a way a relation is not: the relation can be unlinked and
       the merge cannot be unmerged. Every pair goes to a person. */
    /* THE FOURTH RULE. Neither side is a record, so there is nothing to merge.
       Whether either name should become a record is a different question and
       the relations queue already asks it; answering it here as a merge would
       be answering it twice, in the harder of the two forms.

       This fires regardless of how many articles the names appear in. A name in
       ninety articles with no record is a missing record, not a merge, and
       sorting it by article count only buries the pairs that are real. On the
       full corpus this is 3,585 of 3,765 merge decisions. */
    /* Except for a marriage the source states outright. "Barbara Sitzman (Mrs.
       Paul Cook)" is not a proposal that two names might be one person; it is
       the text naming a woman, her married alias and her husband in one breath.
       Neither side being a record is the reason to act on it rather than the
       reason to park it: park it and she stays in the archive under her
       husband's name only, which is the outcome the pair exists to prevent. */
    $reasons = (array)($p['reasons'] ?? []);
    $stated = in_array('stated_married_name', $reasons, true);

    if (!$aEx && !$bEx && !$stated) {
        $row['note'] = 'neither side is a record, nothing to merge';
        $buckets['reject'][] = $row;
        $why['pair: neither is a record'] = ($why['pair: neither is a record'] ?? 0) + 1;
        continue;
    }
    if ($stated) {
        $row['note'] = 'the source states this marriage; creates two records and an alias';
        $row['stated'] = true;
        $buckets['review'][] = $row;
        $why['pair: stated marriage'] = ($why['pair: stated marriage'] ?? 0) + 1;
        continue;
    }
    $row['note'] = 'a merge is never automatic';
    $buckets['review'][] = $row;
    $why['pair: needs a person'] = ($why['pair: needs a person'] ?? 0) + 1;
}

/* GROUPED BY NAME, and this is the difference between a queue and a wall.
 *
 * The exporters emit one row per name per article, so "Santa Clara River" is 31
 * rows. As 31 rows it is 31 decisions; as one row it is one decision that
 * creates one record and links 31 articles. The ungrouped review pile was 2,224
 * items and almost none of them were distinct questions.
 *
 * Rejects are grouped too, for the same reason: a parked list of names is
 * re-triageable, a parked list of mentions is noise. */
$group = function (array $rows): array {
    $by = [];
    foreach ($rows as $r) {
        $k = strtolower($r['q'] . '|' . $r['kind'] . '|' . $r['name']);
        if (!isset($by[$k])) {
            $r['rows'] = 0;
            $r['inArticles'] = [];
            $by[$k] = $r;
        }
        $by[$k]['rows']++;
        if (!empty($r['entryId'])) { $by[$k]['inArticles'][$r['entryId']] = true; }
        $by[$k]['articles'] = max($by[$k]['articles'], $r['articles']);
        $by[$k]['mentions'] = max($by[$k]['mentions'], $r['mentions']);
    }
    foreach ($by as $k => $v) { $by[$k]['inArticles'] = count($v['inArticles']); }
    return array_values($by);
};

$ungrouped = ['accept' => count($buckets['accept']), 'reject' => count($buckets['reject']),
              'review' => count($buckets['review'])];
$buckets['review'] = $group($buckets['review']);
$buckets['reject'] = $group($buckets['reject']);

/* Review sorted by reach: the names in the most articles first, because those
   are the missing records. */
usort($buckets['review'], fn($x, $y) => ($y['articles'] <=> $x['articles']) ?: ($y['mentions'] <=> $x['mentions']));

$tot = count($cands) + count($pairs);
echo str_repeat('=', 74) . PHP_EOL;
printf("queue in: %s relation candidates + %s entity pairs = %s\n",
    number_format(count($cands)), number_format(count($pairs)), number_format($tot));
echo str_repeat('-', 74) . PHP_EOL;
printf("  %-8s %8s %10s\n", '', 'rows in', 'decisions');
foreach (['accept', 'reject', 'review'] as $k) {
    printf("  %-8s %8s %10s   %5.1f%% of rows\n", $k,
        number_format($ungrouped[$k]), number_format(count($buckets[$k])),
        $tot ? 100 * $ungrouped[$k] / $tot : 0);
}
printf("  %-8s %8s %10s\n", 'TOTAL', number_format($tot),
    number_format($ungrouped['accept'] + count($buckets['reject']) + count($buckets['review'])));
echo str_repeat('-', 74) . PHP_EOL;
arsort($why);
foreach ($why as $k => $v) { printf("    %-42s %6s\n", $k, number_format($v)); }

echo PHP_EOL . 'the review pile, by reach, top 15:' . PHP_EOL;
foreach (array_slice($buckets['review'], 0, 15) as $r) {
    printf("  %-44s %-14s %3d articles  %s\n",
        mb_substr($r['name'], 0, 44), $r['kind'], $r['articles'], $r['note'] ?? '');
}

file_put_contents($OUT, json_encode($buckets));
echo PHP_EOL . 'bucketing: ' . $OUT . PHP_EOL;

$out = [];
$out[] = '# Triage of the review queues';
$out[] = '';
$out[] = 'Generated by scripts/import/triage_review_queues.php on ' . date('Y-m-d H:i') . '. Read only.';
$out[] = '';
$out[] = 'Rows are what the exporters emit, one per name per article. Decisions are';
$out[] = 'what a person actually faces, because a name in 31 articles is one question,';
$out[] = 'not 31.';
$out[] = '';
$out[] = '| bucket | rows | decisions | share of rows |';
$out[] = '|---|---:|---:|---:|';
foreach (['accept', 'reject', 'review'] as $k) {
    $out[] = '| ' . $k . ' | ' . number_format($ungrouped[$k]) . ' | '
           . number_format(count($buckets[$k])) . ' | '
           . number_format($tot ? 100 * $ungrouped[$k] / $tot : 0, 1) . '% |';
}
$out[] = '| **total** | **' . number_format($tot) . '** | **'
       . number_format($ungrouped['accept'] + count($buckets['reject']) + count($buckets['review'])) . '** | |';
$out[] = '';
$out[] = '## Why each went where';
$out[] = '';
$out[] = '| reason | n |';
$out[] = '|---|---:|';
foreach ($why as $k => $v) { $out[] = '| ' . $k . ' | ' . number_format($v) . ' |'; }
$out[] = '';
$out[] = '## The review pile, by reach';
$out[] = '';
$out[] = '| name | kind | articles | rows it collapses | why |';
$out[] = '|---|---|---:|---:|---|';
foreach (array_slice($buckets['review'], 0, 60) as $r) {
    $out[] = '| ' . str_replace('|', '\\|', $r['name']) . ' | ' . $r['kind'] . ' | '
           . $r['articles'] . ' | ' . ($r['rows'] ?? 1) . ' | ' . ($r['note'] ?? '') . ' |';
}
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo 'report: ' . $REPORT . PHP_EOL;
