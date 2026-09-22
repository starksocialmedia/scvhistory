/**
 * Exports every distinct entity name across the legacy extraction to
 * web/review/entities.json, with the pairs that may be the same thing.
 * Read only: it never writes to the database and never merges anything.
 *
 * This looks at the extraction index, not at Craft records. Almost nothing has
 * been promoted to a record yet, so the duplicates that matter are the ones
 * between names Grok found: "Dr. Cephas R. Bard", "Cephas R. Bard" and
 * "Dr. Bard" are one man, and deciding that once here is worth deciding it
 * ninety-nine times on the relations screen.
 *
 * Each name carries its total mention count, the articles it appears on, the
 * inventories it came from, the flags Grok raised, and any Craft record that
 * already matches it by title or alias.
 *
 * A name and a count are not enough to decide "Don Ygnacio" against "Senor
 * Ygnacio", so each name also carries up to three sentences from body_text
 * showing it in use, one per article where the articles allow it, with the
 * matched text recorded so the screen can emphasise it. A pair that shares an
 * article carries a sentence from that shared article on each side as well,
 * because two names used in one piece is the strongest signal there is.
 *
 * Each pair also carries the other names in its surname block, so a reviewer
 * can see that "Don Ygnacio", "Senor Ygnacio", "Ygnacio del Valle" and "Don
 * Ygnacio del Valle" are all in play rather than deciding one pair blind.
 *
 * One shape is not a guess at all: the source states it. Leon's text writes
 * "Barbara Sitzman (Mrs. Paul Cook)" and "Nicolene Cheney (Mrs. Wayne Graham)",
 * the maiden name followed by the married form. That names three things at
 * once: the woman, her alias, and her husband. Those are recorded as
 * stated_married_name pairs with the sentence they came from, so the screen can
 * present them as fact rather than as a judgement, and so a woman who would
 * otherwise appear only as her husband's name gets a record under her own.
 *
 * "Russell (nee Pearl Pardee)" is the same fact written the other way round and
 * is read the same way.
 *
 * One shape is detected the other way round, as a reason NOT to merge.
 * "Mrs. George LeBrun" beside "George LeBrun" is a married woman named by her
 * husband's name, which is how nineteenth century sources name most women. The
 * two are identical once the honorific is stripped, so every similarity test
 * proposes them as one person, and accepting that erases her from the archive.
 * Those pairs carry probable_spouse and say so on the card.
 *
 * A pair is proposed on one rule only, within one kind: the two names are the
 * same name once the honorific and the initials are stripped, AND each side
 * already resolves to a record, AND those are two different records.
 *
 * It used to propose five ways, including on a shared surname. Against a
 * columnist whose byline sits on 228 articles that rule proposed merging Sol
 * Taylor with Liz Taylor, Mabel Taylor, Archie Taylor and every other Taylor in
 * the corpus, and the queue it produced was 3,765 decisions holding one real
 * question. The rule now refuses:
 *
 *   neither side is a record   there is nothing to merge. Whether either should
 *                              become a record is the relations queue's
 *                              question and it already asks it.
 *   one side is a record       an alias, not a merge. Recorded under 'aliases'
 *                              so the name can be attached to the record it
 *                              matches without inventing a second one.
 *   both resolve to the same   "Dr. Sol Taylor" against "Sol Taylor" where both
 *   record                     already point at #2582. Nothing to do.
 *
 * Suffix and spelling are gone entirely. "Dr. Bard" against "Cephas R. Bard"
 * and "Herrington" against "Harrington" may well be the same person, but a
 * script does not get to decide that, and a merge cannot be unmerged.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_entity_candidates.php'))"
 */

$root = \Craft::getAlias('@root');

/* TWO CORPORA, ONE SWITCH
 *
 * entities.json is the four inventories that have been read closely;
 * entities-full.json is every inventory, and feeds records-full.json. Both were
 * produced from this file, the second by editing the output path and the
 * inventory list by hand and remembering to put them back.
 *
 * That is how entities-full.json came to be four hours older than its sibling,
 * and how a fix applied here reached records.json and not records-full.json:
 * the suffix was passed to export_missing_records.php, which dutifully rebuilt
 * the full queue from a stale full source and reported success.
 *
 * So the suffix now picks the inventory list too. The two cannot disagree,
 * because there is only one thing to set.
 *
 *   ddev craft exec "eval(file_get_contents('scripts/import/export_entity_candidates.php'))"
 *   ddev craft exec "\$ENTITIES_SUFFIX='-full'; eval(file_get_contents('scripts/import/export_entity_candidates.php'))"
 */
$ESUFFIX = $ENTITIES_SUFFIX ?? '';
$out = \Craft::getAlias('@webroot') . '/review/entities' . $ESUFFIX . '.json';

$INVENTORIES = $ESUFFIX === '-full'
    ? ['perkins', 'reynolds-full', 'reynolds', 'warmemorial', 'worden', 'coins',
       'oldtownnewhall', 'mentryville', 'media', 'loose-pages']
    : ['perkins', 'reynolds-full', 'reynolds', 'warmemorial'];
$KINDS = ['people' => 'person', 'places' => 'place', 'organizations' => 'organization'];
$SECTION_FOR = ['person' => 'persons', 'place' => 'places', 'organization' => 'organizations'];
$ALIAS_FOR = ['person' => 'personAliases', 'place' => 'placeAliases', 'organization' => 'orgAliases'];

/* Civic titles strip like military ones. A man is a councilman for four years
   and a name for the rest of his life, and the corpus names him both ways in
   the same paragraph. The title never becomes part of the record's name: the
   titled form stays as an alias so the text still finds him.
   
   There is no roles field on person yet. Until there is, the title survives in
   the alias and nowhere else, which is a loss worth naming: "Congressman
   McKeon" tells a reader what he was, and an alias does not say when. */
$HONORIFICS = 'mr|mrs|ms|miss|dr|fr|father|capt|captain|col|colonel|gen|general|lt|lieutenant|rev|reverend|'
            . 'sgt|sergeant|maj|major|don|dona|senor|senora|sister|brother|judge|gov|governor|prof|professor|sr|st|saint|'
            . 'congressman|congresswoman|councilman|councilwoman|councilmember|mayor|supervisor|sheriff|'
            . 'senator|assemblyman|assemblywoman|deputy|chief|president|secretary|commissioner';

$fold = function (string $s): string {
    $s = mb_strtolower(trim($s));
    $from = ["\u{00E1}","\u{00E0}","\u{00E2}","\u{00E4}","\u{00E9}","\u{00E8}","\u{00EA}","\u{00EB}","\u{00ED}","\u{00EC}","\u{00EE}","\u{00EF}","\u{00F3}","\u{00F2}","\u{00F4}","\u{00F6}","\u{00FA}","\u{00F9}","\u{00FB}","\u{00FC}","\u{00F1}","\u{00E7}","\u{2019}","\u{2018}"];
    $to   = ['a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','u','u','u','u','n','c','',''];
    $s = str_replace($from, $to, $s);
    $s = preg_replace('~[^a-z0-9 ]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};
$stripHon = function (string $s) use ($fold, $HONORIFICS): string {
    $s = $fold($s);
    do { $before = $s; $s = preg_replace('~^(' . $HONORIFICS . ')\s+~', '', $s); } while ($s !== $before);
    return trim($s);
};
$tokens = function (string $s) use ($stripHon): array {
    $t = preg_split('~\s+~', $stripHon($s), -1, PREG_SPLIT_NO_EMPTY);
    return $t ?: [];
};

/* THE MERGE KEY
 *
 * Two names are a merge candidate only if they are the same name once the
 * honorific and the initials are taken off. "Dr. Sol Taylor" and "Sol Taylor"
 * share a key. "Henry M. Newhall" and "Henry Newhall" share a key. "Sol Taylor"
 * and "Liz Taylor" do not, and that is the point: the old rule paired on the
 * surname alone, and against a columnist whose byline sits on 228 articles it
 * proposed merging him with every other Taylor in the corpus.
 *
 * Initials come off rather than being matched positionally, so that "J. B.
 * Smith" and "John Smith" land together without the rule having to decide that
 * B stands for anything. It is a coarser key than the old initialsMatch and
 * that is the right trade: a merge is destructive and unmergeable, so the rule
 * should propose few things and be right about them. */
$mergeKey = function (string $s) use ($tokens): string {
    $t = array_values(array_filter($tokens($s), fn($x) => strlen($x) > 1));
    return implode(' ', $t);
};

/* ---------------------------------------------------------- existing records */

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

$records = [];
foreach ($SECTION_FOR as $kind => $section) {
    $records[$kind] = [];
    foreach (\craft\elements\Entry::find()->section($section)->status(null)->all() as $e) {
        $row = ['id' => $e->id, 'title' => (string)$e->title, 'url' => (string)$e->url];
        $names = [(string)$e->title];
        if ($hasField($e, $ALIAS_FOR[$kind])) {
            try {
                foreach (preg_split('~\r\n|\n|\r|,~', (string)$e->getFieldValue($ALIAS_FOR[$kind])) as $a) {
                    if (trim($a) !== '') { $names[] = trim($a); }
                }
            } catch (\Throwable $ex) {}
        }
        foreach ($names as $n) {
            $k = $stripHon($n);
            if ($k !== '' && !isset($records[$kind][$k])) { $records[$kind][$k] = $row; }
        }
    }
}

/* ---------------------------------------------------------- articles by page */

/* The legacy host comes from the one place the site reads it, so a reviewer's
   "check the original" link cannot drift from the site's own legacy links. */
$LEGACY_HOST = rtrim((string)(Craft::$app->getConfig()->getCustom()->legacyHost ?? 'https://scvhistory.com'), '/');
$legacyAbs = function (string $v) use ($LEGACY_HOST): string {
    $v = trim($v);
    if ($v === '') { return ''; }
    if (str_starts_with($v, 'http://') || str_starts_with($v, 'https://')) { return $v; }
    if (str_starts_with($v, '//')) { return 'https:' . $v; }
    return $LEGACY_HOST . '/' . ltrim($v, '/');
};

$articleByPath = [];
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->all() as $e) {
    $lu = $hasField($e, 'legacyUrl') ? trim((string)$e->legacyUrl) : '';
    if ($lu === '') { continue; }
    $articleByPath[parse_url($lu, PHP_URL_PATH) ?: $lu] = [
        'id' => $e->id, 'title' => (string)$e->title, 'slug' => $e->slug,
        'url' => (string)$e->url, 'legacy' => $legacyAbs($lu),
    ];
}

/* ---------------------------------------------------------- gather the names */

$flagsByPage = [];
$bodyByPath = [];
$pageTitleByPath = [];
$entities = ['person' => [], 'place' => [], 'organization' => [], 'event' => []];

foreach ($INVENTORIES as $inv) {
    $p = $root . '/inventory/legacy/' . $inv . '.json';
    if (!file_exists($p)) { echo 'WARNING: ' . $inv . '.json not found' . PHP_EOL; continue; }
    $d = json_decode(file_get_contents($p), true);

    $pages = $d['pages'] ?? array_merge($d['series_pages'] ?? [], $d['related_pages'] ?? []);
    foreach ($pages as $page) {
        $path = parse_url((string)$page['source_url'], PHP_URL_PATH) ?: '';
        $bt = trim((string)($page['body_text'] ?? ''));
        if ($bt !== '' && !isset($bodyByPath[$path])) {
            $bodyByPath[$path] = $bt;
            $pageTitleByPath[$path] = trim((string)($page['title'] ?? '')) ?: $path;
        }
        foreach (($page['needs_review'] ?? []) as $nr) {
            $flagsByPage[$path][] = ['reason' => (string)($nr['reason'] ?? ''), 'detail' => (string)($nr['detail'] ?? '')];
        }
    }

    foreach ($KINDS as $group => $kind) {
        foreach (($d['entity_index'][$group] ?? []) as $ent) {
            $name = trim((string)($ent['name_raw'] ?? ''));
            if ($name === '') { continue; }
            $key = $stripHon($name);
            if ($key === '') { continue; }
            if (!isset($entities[$kind][$name])) {
                $entities[$kind][$name] = [
                    'name' => $name, 'key' => $key, 'mentions' => 0, 'pages' => [],
                    'inventories' => [], 'variants' => [], 'hasLegacyPage' => false, 'legacyPageUrl' => '',
                ];
            }
            $e =& $entities[$kind][$name];
            $e['mentions'] += (int)($ent['mention_count'] ?? 0);
            foreach (($ent['pages'] ?? []) as $pg) { $e['pages'][parse_url((string)$pg, PHP_URL_PATH) ?: ''] = true; }
            foreach (($ent['name_variants'] ?? []) as $v) { if (trim($v) !== '') { $e['variants'][trim($v)] = true; } }
            $e['inventories'][$inv] = true;
            if (!empty($ent['has_legacy_page'])) { $e['hasLegacyPage'] = true; $e['legacyPageUrl'] = (string)($ent['legacy_page_url'] ?? ''); }
            unset($e);
        }
    }
}

/* ------------------------------------------- married names the source states

   "Barbara Sitzman (Mrs. Paul Cook)". A name token is a capitalised word or one
   of the lowercase particles, so "Henry de Moss" is read whole; requiring every
   word to be capitalised silently dropped one of the three. */

$NM  = "[A-Z][A-Za-z'\x{2019}.\-]*(?:\s+(?:[A-Z][A-Za-z'\x{2019}.\-]*|de|del|la|van|von|di|du|den|der))" . '{0,3}';
$HON = 'Mrs\.?|Mme\.?|Madame|Se\x{00F1}ora|Senora|Sra\.?';

$statedMarriages = [];
$seenMarriage = [];
foreach ($bodyByPath as $path => $bodyRaw) {
    $flat = preg_replace('~\s+~u', ' ', $bodyRaw);

    /* maiden name first: Barbara Sitzman (Mrs. Paul Cook) */
    $shapes = [
        ['~\b(' . $NM . ')\s*,?\s*\((?:' . $HON . ')\s+(' . $NM . ')\)~u', 'wife-first'],
        /* married form first: Mrs. Cook (Barbara Sitzman) */
        ['~\b(?:' . $HON . ')\s+(' . $NM . ')\s*\((' . $NM . ')\)~u', 'husband-first'],
        /* Russell (nee Pearl Pardee) */
        ['~\b(' . $NM . ')\s*\(n[e\x{00E9}]e\s+(' . $NM . ')\)~u', 'nee'],
    ];

    foreach ($shapes as [$re, $shape]) {
        if (!preg_match_all($re, $flat, $ms, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) { continue; }
        foreach ($ms as $m) {
            $whole = $m[0][0];
            $at = $m[0][1];

            if ($shape === 'wife-first')   { $wife = trim($m[1][0]); $husbandForm = trim($m[2][0]); }
            elseif ($shape === 'husband-first') { $wife = trim($m[2][0]); $husbandForm = trim($m[1][0]); }
            else { $wife = trim($m[2][0]); $husbandForm = trim($m[1][0]); }

            /* A sentence can start on the match: "To Nicolene Cheney, (Mrs.
               Graham)" and "Informant, Mrs. H.B. Russell (nee Pearl Pardee)". */
            $wife = trim(preg_replace('~^(?:To|And|Of|But|In|At|For|With|Informant|The|Her|His)\s+~u', '', $wife));
            $husbandForm = trim(preg_replace('~^(?:' . $HON . ')\s+~u', '', $husbandForm));

            /* "the ranch house, nee the Asistencia" and "Lillie (named for Mrs.
               Needham)" both match the shape and are not marriages. A person's
               name here is two words or more on the woman's side. */
            if (count(preg_split('~\s+~u', $wife)) < 2) { continue; }
            if (preg_match('~\b(the|a|an|his|her|named|house|ranch)\b~i', $wife)) { continue; }

            $key = $fold($wife) . '|' . $fold($husbandForm);
            if (isset($seenMarriage[$key])) { continue; }
            $seenMarriage[$key] = true;

            /* The sentence it sits in, for the card.

               Two traps here, both of which produced nonsense first time.
               PREG_OFFSET_CAPTURE returns a byte offset, so every cut below is
               byte based; mixing in mb_substr sliced mid-character. And the
               phrase itself contains "Mrs.", so splitting on a full stop cut
               the sentence in half at the very word it is about. Abbreviations
               are masked before the split and restored after. */
            $winFrom = max(0, $at - 320);
            $win = substr($flat, $winFrom, 320 + strlen($whole) + 220);
            $rel = $at - $winFrom;

            $ABBR = ['Mrs.', 'Mr.', 'Dr.', 'Jr.', 'Sr.', 'St.', 'Col.', 'Gen.', 'Capt.',
                     'Rev.', 'Hon.', 'Lt.', 'Sgt.', 'Maj.', 'Prof.', 'Ave.', 'No.'];
            $masked = $win;
            foreach ($ABBR as $i => $a) { $masked = str_replace($a, rtrim($a, '.') . "\x01", $masked); }
            /* A lone initial, "H.B. Russell". */
            $masked = preg_replace('~\b([A-Z])\.~', '$1' . "\x01", $masked);

            $before = substr($masked, 0, $rel);
            $start = 0;
            foreach (['. ', '! ', '? '] as $mark) {
                $e = strrpos($before, $mark);
                if ($e !== false && $e + 2 > $start) { $start = $e + 2; }
            }
            $tail = substr($masked, $start);
            $cut = preg_split('~(?<=[.!?])\s~u', $tail);
            $sent = trim($cut[0] ?? $tail);
            $sent = str_replace("\x01", '.', $sent);

            $statedMarriages[] = [
                'wife' => $wife,
                'husband' => $husbandForm,
                'marriedName' => $shape === 'nee' ? '' : 'Mrs. ' . $husbandForm,
                'shape' => $shape,
                'phrase' => $whole,
                'sentence' => $sent,
                'path' => $path,
                'page' => $pageTitleByPath[$path] ?? $path,
            ];
        }
    }
}

/* The same marriage is often written twice, once in full and once short:
   "Nicolene Cheney (Mrs. Wayne Graham)" and later "(Mrs. Graham)". Keep the
   fullest husband name per woman; the short form adds nothing. */
$byWife = [];
foreach ($statedMarriages as $sm) {
    $k = $fold($sm['wife']);
    if (!isset($byWife[$k]) || mb_strlen($sm['husband']) > mb_strlen($byWife[$k]['husband'])) {
        $byWife[$k] = $sm;
    }
}
$statedMarriages = array_values($byWife);
usort($statedMarriages, fn($a, $b) => strcmp($a['wife'], $b['wife']));

echo 'married names the source states outright: ' . count($statedMarriages) . PHP_EOL;
foreach ($statedMarriages as $sm) {
    echo '  ' . str_pad($sm['shape'], 14) . str_pad($sm['wife'], 24) . 'wife of ' . $sm['husband']
        . '   on ' . mb_substr($sm['page'], 0, 30) . PHP_EOL;
}

/* ------------------------------------------------------- sentences in body */

/* Split once per page rather than once per name: 1,435 names across 177 pages
   would otherwise re-split the same bodies thousands of times. */
$sentencesByPath = [];
foreach ($bodyByPath as $path => $body) {
    $flat = preg_replace('~\s*\R\s*~u', ' ', $body);
    $parts = preg_split('~(?<=[.!?\x{201D}])\s+(?=[\x{201C}"\(A-Z0-9])~u', $flat, -1, PREG_SPLIT_NO_EMPTY);
    $sentencesByPath[$path] = $parts ?: [$flat];
}

/* One sentence showing $needle in $path, trimmed to something readable, with
   the text that actually matched kept so the screen can emphasise it. */
/* A sample sentence names the article it came from, and that name has to be a
   link. It is the whole mechanism by which a reviewer decides: read the
   sentence, open the piece, come back and choose. Without a URL the card shows
   a title the reader has to go and search for, which is why the sentences were
   there but not usable. */
$sentenceFor = function (string $path, array $needles) use ($sentencesByPath, $pageTitleByPath, &$articleByPath): ?array {
    foreach ($needles as $needle) {
        if (mb_strlen($needle) < 3) { continue; }
        foreach (($sentencesByPath[$path] ?? []) as $sent) {
            $at = mb_stripos($sent, $needle);
            if ($at === false) { continue; }
            $text = trim($sent);
            /* A very long sentence is trimmed around the match, never through it. */
            if (mb_strlen($text) > 260) {
                $at = mb_stripos($text, $needle);
                $from = max(0, $at - 110);
                $text = ($from > 0 ? "\u{2026}" : '') . mb_substr($text, $from, 250);
                if (mb_strlen($sent) > $from + 250) { $text .= "\u{2026}"; }
            }
            return ['path' => $path, 'title' => $pageTitleByPath[$path] ?? $path, 'text' => $text,
                    'match' => $needle, 'url' => $articleByPath[$path]['url'] ?? null];
        }
    }
    return null;
};

/* Up to three sentences for one name, spread across its articles first so the
   reviewer sees the name in more than one piece where the pages allow it. */
$contextFor = function (array $paths, array $needles, int $want = 3) use ($sentenceFor): array {
    $out = [];
    foreach ($paths as $path) {
        $c = $sentenceFor($path, $needles);
        if ($c) { $out[] = $c; }
        if (count($out) >= $want) { break; }
    }
    return $out;
};

/* ------------------------------------------------------ the curated canon

   inventory/legacy/name-canon.json is judgement the archive has already made
   and a script must never write to it. Two things live there.

   FOLDS. A canonical name and the spellings that are the same thing. The
   aliases are merged into the canonical row before anything is ranked, so
   "Newhall Land", "Newhall Land and Farming" and "Newhall Land and Farming
   Company" stop being three rows competing for the same articles and become one
   row carrying the union of them. A fold crosses kinds where it has to: "Hart
   High" arrived as a place and "Hart High School" as an organization, and one
   school cannot be both.

   SPLITS. A bare name that is several things. Pico is a canyon, a road and a
   general, and which one it is depends on the sentence. Each page is decided by
   the words nearest the name, in the order the canon lists, so the road is
   tested before the canyon because the road's name contains the canyon's. A
   page matching no cue is not guessed at: it goes to "Pico (ambiguous)" and a
   person decides it. Assigning it to whichever of the three is commonest would
   make the queue look finished while being wrong in a way nobody could see. */

$canonPath = $root . '/inventory/legacy/name-canon.json';
$canon = file_exists($canonPath) ? (json_decode(file_get_contents($canonPath), true) ?: []) : [];
$canonReport = ['folds' => [], 'splits' => [], 'missing' => []];

if ($canon) {
    /* Merge one aggregate into another: pages, mentions, variants, inventories. */
    $absorb = function (array &$dst, array $src, string $srcName) {
        foreach (array_keys($src['pages'] ?? []) as $p) { $dst['pages'][$p] = true; }
        foreach (array_keys($src['variants'] ?? []) as $v) { $dst['variants'][$v] = true; }
        foreach (array_keys($src['inventories'] ?? []) as $i) { $dst['inventories'][$i] = true; }
        $dst['mentions'] += (int)($src['mentions'] ?? 0);
        $dst['variants'][$srcName] = true;
        if (!empty($src['hasLegacyPage']) && empty($dst['hasLegacyPage'])) {
            $dst['hasLegacyPage'] = true;
            $dst['legacyPageUrl'] = (string)($src['legacyPageUrl'] ?? '');
        }
    };

    /* Find a name in any kind, folded, so "CSUN" matches however it was filed. */
    /* $honorifics loosens the match to the honorific-stripped form, which the
       splits need and the folds must not have.
     *
     * A split base can be absent from the corpus under its bare name and
     * present under a title. The only Newhall-alone name the extraction
     * produced is "Mr. Newhall"; the queue already strips the honorific and
     * shows it as "Newhall", but this lookup compared the full names, missed,
     * and reported "split base not in the corpus" -- so the five-way Newhall
     * split silently did nothing while saying so in a line nobody reads as an
     * error. Any base appearing only with a title had the same problem.
     *
     * The folds keep the strict comparison. A fold rewrites one name to
     * another, and honorific variants are listed there explicitly as aliases
     * precisely so each one is a decision somebody made. Loosening it would
     * fold "Dr. Bard" wherever "Bard" folds, which is a guess about a person. */
    $findAnywhere = function (string $needle, bool $honorifics = false) use (&$entities, $fold, $stripHon): array {
        $hits = [];
        $n = $fold($needle);
        $nh = $stripHon($needle);
        foreach ($entities as $k => $set) {
            foreach ($set as $nm => $_) {
                if ($fold($nm) === $n) { $hits[] = [$k, $nm]; continue; }
                if ($honorifics && $stripHon($nm) === $nh) { $hits[] = [$k, $nm]; }
            }
        }
        return $hits;
    };

    /* ---------------------------------------------------------------- folds */
    foreach (($canon['canon'] ?? []) as $c) {
        $target = trim((string)($c['canonical'] ?? ''));
        $tkind  = (string)($c['type'] ?? '');
        if ($target === '' || !isset($entities[$tkind])) { continue; }

        $folded = []; $before = 0;

        /* The canonical row may or may not already exist. Where it exists under
           another kind, it moves: the canon says what kind it is. */
        $existingHits = $findAnywhere($target);
        $movedFrom = null;
        if (!isset($entities[$tkind][$target])) {
            $seed = null;
            foreach ($existingHits as [$k, $nm]) {
                $seed = $entities[$k][$nm];
                if ($k !== $tkind) { $movedFrom = $k; }
                unset($entities[$k][$nm]);
                break;
            }
            $entities[$tkind][$target] = $seed ?? [
                'name' => $target, 'key' => $stripHon($target), 'mentions' => 0, 'pages' => [],
                'inventories' => [], 'variants' => [], 'hasLegacyPage' => false, 'legacyPageUrl' => '',
            ];
            $entities[$tkind][$target]['name'] = $target;
            $entities[$tkind][$target]['key'] = $stripHon($target);
        }
        $dst =& $entities[$tkind][$target];
        $before = count($dst['pages']);

        foreach (($c['aliases'] ?? []) as $alias) {
            $alias = trim((string)$alias);
            if ($alias === '' || $fold($alias) === $fold($target)) { continue; }
            $hits = $findAnywhere($alias);
            if (!$hits) { $canonReport['missing'][] = $target . ' <- ' . $alias . ' (not in the corpus)'; continue; }
            foreach ($hits as [$k, $nm]) {
                $absorb($dst, $entities[$k][$nm], $nm);
                $folded[] = $nm . ' [' . $k . ', ' . count($entities[$k][$nm]['pages']) . ' pages]';
                unset($entities[$k][$nm]);
            }
        }
        /* A parked canonical is folded and then marked. The aliases stop
           ranking as rows of their own, and the row they fold into says it must
           not become a record. "Downtown Newhall Specific Plan" is a document:
           the articles discuss it, so the corpus names it, but a record for it
           would sit in a section for things that exist in the valley. */
        /* A PARKED CANONICAL PARKS ITSELF.
         *
         * Two ways a canonical gets parked, and the second was missing. The
         * explicit one is park: true, for a thing that exists and should not be
         * a record, like a planning document. The implicit one is a type the
         * review screen cannot create: it makes people, places and
         * organizations, and an event is none of those.
         *
         * Without the second, the Cowboy Poetry and Music Festival folded its
         * five aliases together and then ranked as the seventh name in the
         * queue, guessed a person, on eighteen articles. The aliases were
         * parked and the canonical was not, which is the worst of both: one
         * confident wrong row where there had been five vague ones. */
        $CREATABLE = ['person' => 1, 'place' => 1, 'organization' => 1];
        if (!empty($c['park'])) {
            $dst['parked'] = (string)($c['parkReason'] ?? 'parked by the canon');
        } elseif (!isset($CREATABLE[$tkind])) {
            $dst['parked'] = 'a ' . $tkind . '. The review screen creates people, places and '
                . 'organizations only, so this waits for a screen that can make one, or a hand.';
        }

        /* The canon's type is a ruling, not a hint. Without carrying it the
           guesser re-decides from the name and gets "Rancho La Liebre" wrong as
           a person and "Lincoln Memorial" wrong as a site, which is the whole
           thing the canon line was written to settle. */
        $dst['canonType'] = $tkind;
        if (!empty($c['placeType'])) { $dst['canonPlaceType'] = (string)$c['placeType']; }

        if ($folded || $movedFrom !== null || !empty($c['park'])) {
            $canonReport['folds'][] = [
                'canonical' => $target, 'kind' => $tkind,
                'from' => $folded, 'pagesBefore' => $before, 'pagesAfter' => count($dst['pages']),
                'parked' => !empty($c['park']) || !isset(['person' => 1, 'place' => 1, 'organization' => 1][$tkind]),
                'movedFrom' => $movedFrom, 'toKind' => $tkind,
            ];
        }
        unset($dst);
    }

    /* --------------------------------------------------------------- splits */
    foreach (($canon['splits'] ?? []) as $sp) {
        $base = trim((string)($sp['name'] ?? ''));
        if ($base === '') { continue; }
        $hits = $findAnywhere($base, true);
        if (!$hits) { $canonReport['missing'][] = $base . ' (split base not in the corpus)'; continue; }

        $tally = []; $ambiguous = []; $defaulted = []; $resolved = [];
        foreach ($hits as [$k, $nm]) {
            $src = $entities[$k][$nm];
            foreach (array_keys($src['pages'] ?? []) as $path) {
                /* PER OCCURRENCE, not per page.
                 *
                 * The first version of this built one window per page out of
                 * every sentence the name appeared in, and decided the page
                 * once. On a page about the canyon that mentions the road once,
                 * the road's cue captured all of it: Pico Canyon Road came out
                 * with 215 mentions across 10 articles and its best sample
                 * sentence was "the local seeps in Wiley, Rice, Pico and other
                 * canyons", which is the canyon.
                 *
                 * A sentence is the unit. One page can feed several splits,
                 * which is what a page discussing the canyon and the road up it
                 * actually does. */
                $sents = [];
                foreach (($sentencesByPath[$path] ?? []) as $sent) {
                    if (mb_stripos($sent, $base) !== false) { $sents[] = $sent; }
                }
                if (!$sents) { $ambiguous[$path] = true; continue; }

                $pageResolved = false;
                foreach ($sents as $sent) {
                    $w = ' ' . $fold($sent) . ' ';

                    $chosen = null;
                    foreach (($sp['splits'] ?? []) as $cand) {
                        foreach (($cand['cues'] ?? []) as $cue) {
                            if (str_contains($w, ' ' . $fold($cue) . ' ')) { $chosen = $cand; break 2; }
                        }
                    }
                    /* A split may name a default. Pico has none, because its
                       three readings are balanced and guessing would be
                       inventing. Soledad and Placerita do: a bare mention is
                       the canyon in nearly every occurrence, and sending those
                       to review would bury the two that are genuinely unclear
                       under forty that are not. */
                    if ($chosen === null) {
                        foreach (($sp['splits'] ?? []) as $cand) {
                            if (!empty($cand['default'])) { $chosen = $cand; break; }
                        }
                        if ($chosen !== null) { $defaulted[$sp['name']] = ($defaulted[$sp['name']] ?? 0) + 1; }
                    }
                    if ($chosen === null) { continue; }

                    $cn = (string)$chosen['canonical'];
                    $ck = (string)$chosen['type'];
                    if (!isset($entities[$ck][$cn])) {
                        $entities[$ck][$cn] = [
                            'name' => $cn, 'key' => $stripHon($cn), 'mentions' => 0, 'pages' => [],
                            'inventories' => [], 'variants' => [], 'hasLegacyPage' => false, 'legacyPageUrl' => '',
                        ];
                    }
                    /* Recorded separately as well as written, because where a
                       split target IS the base name it already holds every
                       page, including the ones that belong to its siblings.
                       Gorman the town kept the sentence about Private James
                       Gorman and the tally showed it as having gained nothing.
                       The resolved set replaces the inherited one below. */
                    $resolved[$ck][$cn][$path] = true;
                    if (!isset($entities[$ck][$cn]['pages'][$path])) {
                        $entities[$ck][$cn]['pages'][$path] = true;
                    }
                    /* The base name is NOT an alias of a split target.
                     *
                     * This wrote it onto every target, so "Soledad" became an
                     * alias of Soledad Canyon AND of Soledad Canyon Road, and
                     * "Placerita" of the canyon, the road and the Nature
                     * Center. One alias pointing at three records is not an
                     * alias; it is the ambiguity the split exists to resolve,
                     * recorded as though it had been resolved. The prose linker
                     * reading it has no way to choose, and the whole point of
                     * deciding each occurrence by the words nearest it is lost
                     * at the last step.
                     *
                     * Where a target IS the base name -- Soledad the place,
                     * Gorman the town -- the base is its title, and a title is
                     * not its own alias. So this is skipped in every case; the
                     * comparison is kept to say that out loud rather than
                     * leaving a silent deletion. */
                    if ($cn === $base) {
                        /* the title, dropped later by the alias assembly anyway */
                    }
                    $entities[$ck][$cn]['canonType'] = $ck;
                    if (!empty($chosen['placeType'])) { $entities[$ck][$cn]['canonPlaceType'] = (string)$chosen['placeType']; }
                    foreach (array_keys($src['inventories'] ?? []) as $i) { $entities[$ck][$cn]['inventories'][$i] = true; }
                    $entities[$ck][$cn]['mentions'] += max(1, mb_substr_count($fold($sent), $fold($base)));
                    /* The sentence that decided it, kept so the review screen
                       shows the evidence for the split rather than whichever
                       sentence happens to contain the bare name. A card headed
                       "Pico Canyon Road" over a sentence about the canyon
                       invites the reviewer to reject a correct call. */
                    if (count($entities[$ck][$cn]['decided'] ?? []) < 3) {
                        $entities[$ck][$cn]['decided'][] = [
                            'path' => $path,
                            'url' => $articleByPath[$path]['url'] ?? null,
                            'title' => $pageTitleByPath[$path] ?? $path,
                            'text' => mb_strlen($sent) > 260 ? mb_substr($sent, 0, 250) . "\u{2026}" : trim($sent),
                            'match' => $base,
                        ];
                    }
                    $pageResolved = true;
                }
                /* A page where no sentence matched any cue is a page a person
                   has to read. One where some matched is already represented. */
                if (!$pageResolved) { $ambiguous[$path] = true; }
            }
            /* Unless a split target IS the base name. Gorman splits into James
               Gorman and Gorman, and deleting the base afterwards deleted the
               town that had just been built in its place: the run reported one
               page for the person and none for the town, from seven. */
            $targetNames = array_map(fn($c) => (string)$c['canonical'], $sp['splits'] ?? []);
            if (!in_array($nm, $targetNames, true)) { unset($entities[$k][$nm]); }
        }

        /* REPLACE THE BASE, UNION THE REST.
         *
         * A target whose name IS the base -- Gorman the town, Soledad the
         * township -- holds every page the bare name appeared on, including the
         * ones that belong to its siblings, because it and the base are one
         * entity. Its inherited set is wrong by construction and the resolved
         * set replaces it. That is what this block was written for.
         *
         * A target whose name is NOT the base is a separate entity with its own
         * extracted pages, and those have nothing to do with resolving the bare
         * name. Replacing them throws them away: Newhall Ranch was extracted on
         * twelve articles in its own right, three of which also mention a bare
         * Newhall, and a wholesale replacement cut it to those three. The nine
         * it lost were never in question.
         *
         * The four older splits did not show this because their targets came
         * into existence through the split and had almost nothing of their own
         * to lose. Newhall is the first split whose targets are real records
         * with real page sets, which is why it surfaced here. */
        foreach ($resolved as $ck => $set) {
            foreach ($set as $cn => $paths) {
                $entities[$ck][$cn]['pages'] = $fold($cn) === $fold($base)
                    ? $paths
                    : (($entities[$ck][$cn]['pages'] ?? []) + $paths);
                $tally[$cn] = count($entities[$ck][$cn]['pages']);
            }
        }

        if ($ambiguous) {
            $amb = $base . ' (ambiguous)';
            $kk = (string)(($sp['splits'][0]['type']) ?? 'place');
            if (isset($entities[$kk][$amb])) { $entities[$kk][$amb]['pages'] += $ambiguous; }
            else { $entities[$kk][$amb] = [
                'name' => $amb, 'key' => $stripHon($amb), 'mentions' => count($ambiguous),
                'pages' => $ambiguous, 'inventories' => [], 'variants' => [$base => true],
                'hasLegacyPage' => false, 'legacyPageUrl' => '',
            ]; }
            $tally[$amb] = count($ambiguous);
        }
        $canonReport['splits'][] = ['name' => $base, 'tally' => $tally,
                                    'defaulted' => $defaulted[$base] ?? 0];
    }

    /* ---------------------------------------------------------- the rules

       A pattern applied to every name after the folds. Ranchos and missions
       recur, and deciding each one by hand is deciding the same thing over and
       over. A rule overrides the guess and the extraction's kind, and moves the
       row between sections when it has to. */
    $ruled = [];
    foreach (($canon['rules'] ?? []) as $rule) {
        $pat = '~' . str_replace('~', '\~', (string)$rule['pattern']) . '~i';
        $tk  = (string)($rule['type'] ?? '');
        if (!isset($entities[$tk])) { continue; }
        foreach ($entities as $k => $set) {
            foreach (array_keys($set) as $nm) {
                if (!preg_match($pat, $nm)) { continue; }
                if ($k !== $tk) {
                    if (isset($entities[$tk][$nm])) {
                        $absorb($entities[$tk][$nm], $entities[$k][$nm], $nm);
                    } else {
                        $entities[$tk][$nm] = $entities[$k][$nm];
                    }
                    unset($entities[$k][$nm]);
                }
                $entities[$tk][$nm]['canonType'] = $tk;
                if (!empty($rule['placeType'])) { $entities[$tk][$nm]['canonPlaceType'] = (string)$rule['placeType']; }
                $ruled[] = ['name' => $nm, 'from' => $k, 'to' => $tk,
                            'placeType' => $rule['placeType'] ?? '', 'pattern' => $rule['pattern']];
            }
        }
    }
    $canonReport['ruled'] = $ruled;

    echo PHP_EOL . '=== name canon ===' . PHP_EOL;
    foreach ($canonReport['folds'] as $f) {
        printf("%-5s %-42s %d -> %d pages\n", $f['parked'] ? 'park' : 'fold',
            $f['canonical'], $f['pagesBefore'], $f['pagesAfter']);
        if ($f['movedFrom'] !== null) {
            echo '        moved from ' . $f['movedFrom'] . ' to ' . $f['toKind'] . PHP_EOL;
        }
        foreach ($f['from'] as $x) { echo '        <- ' . $x . PHP_EOL; }
    }
    foreach ($canonReport['splits'] as $s) {
        echo 'split ' . $s['name'] . ':' . PHP_EOL;
        foreach ($s['tally'] as $n => $c) { printf("        %-32s %d pages\n", $n, $c); }
        if (!empty($s['defaulted'])) { printf("        (%d resolved by the default rather than a cue)\n", $s['defaulted']); }
    }
    if ($ruled) {
        echo 'rules matched ' . count($ruled) . ' names:' . PHP_EOL;
        $byPat = [];
        foreach ($ruled as $r) { $byPat[$r['pattern']][] = $r; }
        foreach ($byPat as $pat => $list) {
            printf("   %-16s %d names -> %s%s\n", $pat, count($list), $list[0]['to'],
                $list[0]['placeType'] ? '/' . $list[0]['placeType'] : '');
            foreach (array_slice($list, 0, 6) as $r) {
                echo '      ' . str_pad($r['name'], 38) . ($r['from'] !== $r['to'] ? 'moved from ' . $r['from'] : 'already ' . $r['to']) . PHP_EOL;
            }
        }
    }
    foreach ($canonReport['missing'] as $m) { echo 'note  ' . $m . PHP_EOL; }
    echo PHP_EOL;
}

/* ---------------------------------------------------------- shape the rows */

$rows = ['person' => [], 'place' => [], 'organization' => [], 'event' => []];
foreach ($entities as $kind => $set) {
    foreach ($set as $name => $e) {
        $articles = [];
        $orphanPages = [];
        foreach (array_keys($e['pages']) as $path) {
            if (isset($articleByPath[$path])) { $articles[$articleByPath[$path]['id']] = $articleByPath[$path]; }
            elseif ($path !== '') { $orphanPages[$legacyAbs($path)] = true; }
        }
        $flags = [];
        foreach (array_keys($e['pages']) as $path) {
            foreach (($flagsByPage[$path] ?? []) as $fl) {
                if (mb_stripos($fl['detail'], $name) !== false) { $flags[$fl['reason'] . '|' . $fl['detail']] = $fl; }
            }
        }
        /* The name as written first, then the variants Grok recorded, then the
           name with its honorific removed. The first that appears wins. */
        $needles = array_values(array_unique(array_filter(array_merge(
            [$name], array_keys($e['variants']), [$e['key']]
        ), fn($v) => trim((string)$v) !== '')));
        $paths = array_keys($e['pages']);

        $rows[$kind][] = [
            'name' => $name,
            'needles' => $needles,
            'paths' => $paths,
            'context' => !empty($e['decided']) ? $e['decided'] : $contextFor($paths, $needles),
            'key' => $e['key'],
            'kind' => $kind,
            'mentions' => $e['mentions'],
            'pageCount' => count($e['pages']),
            'articles' => array_values($articles),
            'articleCount' => count($articles),
            'orphanPages' => array_slice(array_keys($orphanPages), 0, 6),
            'inventories' => array_keys($e['inventories']),
            'variants' => array_keys($e['variants']),
            'hasLegacyPage' => $e['hasLegacyPage'],
            'legacyPageUrl' => $e['legacyPageUrl'],
            'flags' => array_values($flags),
            'existing' => $records[$kind][$e['key']] ?? null,
            'parked' => $e['parked'] ?? null,
            'canonType' => $e['canonType'] ?? null,
            'canonPlaceType' => $e['canonPlaceType'] ?? null,
        ];
    }
    usort($rows[$kind], function ($a, $b) { return [$b['mentions'], $a['name']] <=> [$a['mentions'], $b['name']]; });
}

/* ---------------------------------------------------------- propose pairs */

/* Blocking: two names are only compared when they share a key. The surname, and
   the surname with one character removed, so a single-edit surname still meets
   its partner without comparing everything to everything. */
$blocks = function (array $t): array {
    if (!$t) { return []; }
    $sn = end($t);
    $keys = [$sn];
    for ($i = 0; $i < strlen($sn); $i++) { $keys[] = substr($sn, 0, $i) . substr($sn, $i + 1); }
    return array_unique($keys);
};

/* "Mrs. George LeBrun", "Mrs. J. LeBrun", "Sra. Ygnacio del Valle": a woman
   named by her husband's name. Returns what follows the honorific, or ''. */
$wifeOf = function (string $raw): string {
    $s = trim($raw);
    if (!preg_match('~^(Mrs\.?|Mme\.?|Madame|Se\x{00F1}ora|Senora|Sra\.?)\s+(.+)$~ui', $s, $m)) { return ''; }
    $rest = trim($m[2]);
    /* Needs a given name or an initial as well as a surname. "Mrs. LeBrun" on
       its own may well be her own name and is left alone. */
    $parts = preg_split('~\s+~u', $rest, -1, PREG_SPLIT_NO_EMPTY);
    if (count($parts) < 2) { return ''; }
    return $rest;
};

$initialsMatch = function (array $a, array $b): bool {
    /* one side's leading tokens are initials of the other's */
    if (count($a) < 2 || count($b) < 2) { return false; }
    if (end($a) !== end($b)) { return false; }
    $ga = array_slice($a, 0, -1); $gb = array_slice($b, 0, -1);
    $short = count($ga) <= count($gb) ? $ga : $gb;
    $long  = count($ga) <= count($gb) ? $gb : $ga;
    if (count($short) > count($long)) { return false; }
    foreach ($short as $i => $tok) {
        if (!isset($long[$i])) { return false; }
        if ($tok === $long[$i]) { continue; }
        if (strlen($tok) === 1 && $tok[0] === $long[$i][0]) { continue; }
        return false;
    }
    return true;
};

$pairs = [];
$compared = 0;
/* What the gate turned away, kept as counts so the shape of the rejected mass
   is visible rather than merely absent. */
$aliases = [];
$notMerges = ['neither_is_a_record' => 0, 'alias' => 0, 'same_record' => 0];
foreach ($rows as $kind => $list) {
    $index = [];
    $blocksFor = [];
    foreach ($list as $i => $r) {
        foreach ($blocks($tokens($r['name'])) as $bk) { $index[$bk][] = $i; $blocksFor[$i][] = $bk; }
    }

    /* Names in play around this one. The surname block alone is too narrow:
       "Don Ygnacio" blocks on "ygnacio" and "Ygnacio del Valle" on "valle", so
       the two never meet, and those are exactly the four names a reviewer needs
       to see together. So the surname block, then any name sharing a word,
       ranked by how rare the shared words are. "Ygnacio" is rare and pulls its
       family in; "John" is common and pulls in nobody. */
    $byToken = [];
    foreach ($list as $i => $r) {
        foreach (array_unique($tokens($r['name'])) as $t) { $byToken[$t][] = $i; }
    }
    $blockMates = function (int $i, array $exclude) use ($index, $blocksFor, $byToken, $list, $tokens): array {
        $score = [];
        foreach (($blocksFor[$i] ?? []) as $bk) {
            if (count($index[$bk]) > 60) { continue; }
            foreach ($index[$bk] as $j) { $score[$j] = ($score[$j] ?? 0) + 4.0; }
        }
        foreach (array_unique($tokens($list[$i]['name'])) as $t) {
            $df = count($byToken[$t] ?? []);
            if ($df < 2 || $df > 12) { continue; }
            foreach ($byToken[$t] as $j) { $score[$j] = ($score[$j] ?? 0) + 4.0 / $df; }
        }
        unset($score[$i]);
        foreach ($exclude as $j) { unset($score[$j]); }
        arsort($score);
        $out = [];
        foreach (array_slice(array_keys($score), 0, 8) as $j) {
            $out[] = ['name' => $list[$j]['name'], 'mentions' => $list[$j]['mentions']];
        }
        return $out;
    };

    $seen = [];
    foreach ($index as $bk => $members) {
        if (count($members) < 2 || count($members) > 60) { continue; }
        foreach ($members as $x) {
            foreach ($members as $y) {
                if ($x >= $y) { continue; }
                $pk = $x . ':' . $y;
                if (isset($seen[$pk])) { continue; }
                $seen[$pk] = true;
                $compared++;

                $A = $list[$x]; $B = $list[$y];
                $ta = $tokens($A['name']); $tb = $tokens($B['name']);
                if (!$ta || !$tb) { continue; }
                $ka = implode(' ', $ta); $kb = implode(' ', $tb);
                if ($ka === $kb && $A['name'] === $B['name']) { continue; }

                /* Checked before the similarity tests, because when it fires the
                   answer is the opposite of what they are about to say. */
                $spouse = false;
                $wifeA = $wifeOf($A['name']);
                $wifeB = $wifeOf($B['name']);
                if ($wifeA !== '' && $fold($wifeA) === $fold($B['name'])) { $spouse = true; }
                if ($wifeB !== '' && $fold($wifeB) === $fold($A['name'])) { $spouse = true; }

                /* The gate. Same name stripped of honorific and initials, and a
                   DIFFERENT existing record on each side. Anything else is not
                   a merge: two names where neither is a record is a question
                   about whether to create one, which the relations queue
                   already asks, and a name matching a record it is not yet
                   attached to is an alias rather than a merge. Both are
                   counted below and neither belongs in this pile. */
                $mka = $mergeKey($A['name']);
                $mkb = $mergeKey($B['name']);
                if ($mka === '' || $mka !== $mkb) { continue; }

                if (!$A['existing'] || !$B['existing']) {
                    $notMerges[($A['existing'] || $B['existing']) ? 'alias' : 'neither_is_a_record']++;
                    if ($A['existing'] || $B['existing']) {
                        $aliases[] = ['kind' => $kind, 'a' => $A['name'], 'b' => $B['name'],
                            'record' => $A['existing'] ?: $B['existing'],
                            'newName' => $A['existing'] ? $B['name'] : $A['name']];
                    }
                    continue;
                }
                if ((int)$A['existing']['id'] === (int)$B['existing']['id']) {
                    $notMerges['same_record']++;
                    continue;
                }

                $reasons = ['same_name_different_records'];
                if ($spouse) { $reasons[] = 'probable_spouse'; }
                if ($ka === $kb) { $reasons[] = 'honorific'; }
                if ($initialsMatch($ta, $tb)) { $reasons[] = 'initials'; }
                /* Proximity in one piece is the strongest signal there is, so a
                   sentence from a shared page leads on both sides. */
                $sharedPaths = array_values(array_intersect($A['paths'], $B['paths']));
                $aShared = null; $bShared = null;
                foreach ($sharedPaths as $sp) {
                    $aShared = $sentenceFor($sp, $A['needles']);
                    $bShared = $sentenceFor($sp, $B['needles']);
                    if ($aShared && $bShared) { break; }
                }

                $pairs[] = [
                    'kind' => $kind,
                    'a' => $A['name'], 'b' => $B['name'],
                    'spouseNote' => $spouse
                        ? 'A married woman named by her husband\'s name. These are two people. '
                        . 'Link them as spouses rather than merging.' : null,
                    'wife' => $spouse ? ($wifeA !== '' ? $A['name'] : $B['name']) : null,
                    'husband' => $spouse ? ($wifeA !== '' ? $B['name'] : $A['name']) : null,
                    'aShared' => $aShared, 'bShared' => $bShared,
                    'sharedPages' => count($sharedPaths),
                    'aBlock' => $blockMates($x, [$y]), 'bBlock' => $blockMates($y, [$x]),
                    'aMentions' => $A['mentions'], 'bMentions' => $B['mentions'],
                    'aArticles' => $A['articleCount'], 'bArticles' => $B['articleCount'],
                    'aExisting' => $A['existing'], 'bExisting' => $B['existing'],
                    'reasons' => $reasons,
                    'shared' => count(array_intersect(
                        array_column($A['articles'], 'id'), array_column($B['articles'], 'id'))),
                ];
            }
        }
    }
}

/* ---------------------------------- the stated marriages become their own pairs

   These are not proposals. The source says who she is, so the pair carries the
   sentence and the screen presents it as fact. The woman's own name is the
   survivor, the married form becomes her alias, and the husband is recorded as
   a separate person she is married to.

   A name the extraction never indexed is still emitted. Two of the four women
   are in that position, which is the whole point: without this they exist only
   as their husband's name. */

$knownNames = [];
foreach ($rows['person'] as $r) { $knownNames[$fold($r['name'])] = $r['name']; }

foreach ($statedMarriages as $sm) {
    $marriedForm = $sm['marriedName'] !== '' ? $sm['marriedName'] : 'Mrs. ' . $sm['husband'];
    $wife = $knownNames[$fold($sm['wife'])] ?? $sm['wife'];
    $alias = $knownNames[$fold($marriedForm)] ?? $marriedForm;

    $pairs[] = [
        'kind' => 'person',
        'a' => $alias,
        'b' => $wife,
        'statedMarriage' => [
            'wife' => $wife,
            'husband' => $sm['husband'],
            'marriedForm' => $marriedForm,
            'phrase' => $sm['phrase'],
            'sentence' => $sm['sentence'],
            'page' => $sm['page'],
            'wifeIndexed' => isset($knownNames[$fold($sm['wife'])]),
            'husbandIndexed' => isset($knownNames[$fold($sm['husband'])]),
            'aliasIndexed' => isset($knownNames[$fold($marriedForm)]),
        ],
        'spouseNote' => null,
        'wife' => $wife,
        'husband' => $sm['husband'],
        'aMentions' => 0, 'bMentions' => 0,
        'aArticles' => 0, 'bArticles' => 0,
        'aExisting' => $records['person'][$stripHon($alias)] ?? null,
        'bExisting' => $records['person'][$stripHon($wife)] ?? null,
        'reasons' => ['stated_married_name'],
        'shared' => 0,
        'sharedPages' => 0,
        'aShared' => null, 'bShared' => null,
        'aBlock' => [], 'bBlock' => [],
    ];
}

/* Strongest evidence first, then the ones that share an article. */
$weight = ['stated_married_name' => 10, 'probable_spouse' => 9, 'honorific' => 5, 'initials' => 4, 'suffix' => 3, 'spelling' => 3, 'surname' => 1];
usort($pairs, function ($a, $b) use ($weight) {
    $wa = 0; foreach ($a['reasons'] as $r) { $wa = max($wa, $weight[$r] ?? 0); }
    $wb = 0; foreach ($b['reasons'] as $r) { $wb = max($wb, $weight[$r] ?? 0); }
    return [$wb, $b['shared'], $b['aMentions'] + $b['bMentions']] <=> [$wa, $a['shared'], $a['aMentions'] + $a['bMentions']];
});

/* needles and paths exist only to build the pairs above; they would double the
   file for no one's benefit. */
foreach ($rows as $kind => $list) {
    foreach ($list as $i => $r) { unset($rows[$kind][$i]['needles'], $rows[$kind][$i]['paths']); }
}

file_put_contents($out, json_encode([
    'meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/export_entity_candidates.php',
        'inventories' => $INVENTORIES,
        'names' => array_sum(array_map('count', $rows)),
        'pairs' => count($pairs),
        'notMerges' => $notMerges,
    ],
    'entities' => $rows,
    'pairs' => $pairs,
    'aliases' => $aliases,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo '=== summary ===' . PHP_EOL;
foreach ($rows as $kind => $list) {
    $withRec = count(array_filter($list, fn($r) => $r['existing'] !== null));
    echo str_pad($kind, 16) . str_pad((string)count($list), 7) . 'distinct names, ' . $withRec . ' already a record' . PHP_EOL;
}
echo 'merge candidates: ' . count($pairs) . '  from ' . $compared . ' comparisons' . PHP_EOL;
echo 'turned away by the gate:' . PHP_EOL;
echo '  same name, neither side is a record  ' . $notMerges['neither_is_a_record'] . PHP_EOL;
echo '  same name, one side is a record      ' . $notMerges['alias'] . '  (alias, not a merge)' . PHP_EOL;
echo '  same name, both resolve to one record ' . $notMerges['same_record'] . PHP_EOL;
$byReason = [];
foreach ($pairs as $p) { foreach ($p['reasons'] as $r) { $byReason[$r] = ($byReason[$r] ?? 0) + 1; } }
arsort($byReason);
foreach ($byReason as $r => $n) { echo '  ' . str_pad($r, 14) . $n . PHP_EOL; }
echo 'wrote ' . $out . PHP_EOL;
