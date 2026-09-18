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
 * Pairs are proposed five ways, within one kind only:
 *   honorific  the names match once an honorific is removed
 *   initials   same surname, and one side's initials expand to the other's
 *              given names, so "J. P. Harrington" meets "John P. Harrington"
 *   suffix     one name's words are the tail of the other's, so "Dr. Bard"
 *              meets "Cephas R. Bard"
 *   surname    same surname, different given names
 *   spelling   the normalised names are within one edit, which catches
 *              "Herrington" against "Harrington"
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_entity_candidates.php'))"
 */

$root = \Craft::getAlias('@root');
$out = \Craft::getAlias('@webroot') . '/review/entities.json';

$INVENTORIES = ['perkins', 'reynolds-full', 'reynolds', 'warmemorial'];
$KINDS = ['people' => 'person', 'places' => 'place', 'organizations' => 'organization'];
$SECTION_FOR = ['person' => 'persons', 'place' => 'places', 'organization' => 'organizations'];
$ALIAS_FOR = ['person' => 'personAliases', 'place' => 'placeAliases', 'organization' => 'orgAliases'];

$HONORIFICS = 'mr|mrs|ms|miss|dr|fr|father|capt|captain|col|colonel|gen|general|lt|lieutenant|rev|reverend|sgt|sergeant|maj|major|don|dona|senor|senora|sister|brother|judge|gov|governor|prof|professor|sr|st|saint';

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

$articleByPath = [];
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->all() as $e) {
    $lu = $hasField($e, 'legacyUrl') ? trim((string)$e->legacyUrl) : '';
    if ($lu === '') { continue; }
    $articleByPath[parse_url($lu, PHP_URL_PATH) ?: $lu] =
        ['id' => $e->id, 'title' => (string)$e->title, 'slug' => $e->slug, 'url' => (string)$e->url];
}

/* ---------------------------------------------------------- gather the names */

$flagsByPage = [];
$bodyByPath = [];
$pageTitleByPath = [];
$entities = ['person' => [], 'place' => [], 'organization' => []];

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
$sentenceFor = function (string $path, array $needles) use ($sentencesByPath, $pageTitleByPath): ?array {
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
            return ['path' => $path, 'title' => $pageTitleByPath[$path] ?? $path, 'text' => $text, 'match' => $needle];
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

/* ---------------------------------------------------------- shape the rows */

$rows = ['person' => [], 'place' => [], 'organization' => []];
foreach ($entities as $kind => $set) {
    foreach ($set as $name => $e) {
        $articles = [];
        foreach (array_keys($e['pages']) as $path) {
            if (isset($articleByPath[$path])) { $articles[$articleByPath[$path]['id']] = $articleByPath[$path]; }
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
            'context' => $contextFor($paths, $needles),
            'key' => $e['key'],
            'kind' => $kind,
            'mentions' => $e['mentions'],
            'pageCount' => count($e['pages']),
            'articles' => array_values($articles),
            'articleCount' => count($articles),
            'inventories' => array_keys($e['inventories']),
            'variants' => array_keys($e['variants']),
            'hasLegacyPage' => $e['hasLegacyPage'],
            'legacyPageUrl' => $e['legacyPageUrl'],
            'flags' => array_values($flags),
            'existing' => $records[$kind][$e['key']] ?? null,
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

                $reasons = [];
                if ($ka === $kb) { $reasons[] = 'honorific'; }
                if ($initialsMatch($ta, $tb)) { $reasons[] = 'initials'; }
                $n = min(count($ta), count($tb));
                if ($ka !== $kb && array_slice($ta, -$n) === array_slice($tb, -$n)) { $reasons[] = 'suffix'; }
                if (end($ta) === end($tb) && $ka !== $kb && !in_array('suffix', $reasons, true)) { $reasons[] = 'surname'; }
                if ($ka !== $kb && abs(strlen($ka) - strlen($kb)) <= 1 && levenshtein($ka, $kb) === 1) { $reasons[] = 'spelling'; }

                if (!$reasons) { continue; }
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

/* Strongest evidence first, then the ones that share an article. */
$weight = ['honorific' => 5, 'initials' => 4, 'suffix' => 3, 'spelling' => 3, 'surname' => 1];
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
    ],
    'entities' => $rows,
    'pairs' => $pairs,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo '=== summary ===' . PHP_EOL;
foreach ($rows as $kind => $list) {
    $withRec = count(array_filter($list, fn($r) => $r['existing'] !== null));
    echo str_pad($kind, 16) . str_pad((string)count($list), 7) . 'distinct names, ' . $withRec . ' already a record' . PHP_EOL;
}
echo 'pairs proposed:  ' . count($pairs) . '  from ' . $compared . ' comparisons' . PHP_EOL;
$byReason = [];
foreach ($pairs as $p) { foreach ($p['reasons'] as $r) { $byReason[$r] = ($byReason[$r] ?? 0) + 1; } }
arsort($byReason);
foreach ($byReason as $r => $n) { echo '  ' . str_pad($r, 14) . $n . PHP_EOL; }
echo 'wrote ' . $out . PHP_EOL;
