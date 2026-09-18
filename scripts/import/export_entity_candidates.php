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
$entities = ['person' => [], 'place' => [], 'organization' => []];

foreach ($INVENTORIES as $inv) {
    $p = $root . '/inventory/legacy/' . $inv . '.json';
    if (!file_exists($p)) { echo 'WARNING: ' . $inv . '.json not found' . PHP_EOL; continue; }
    $d = json_decode(file_get_contents($p), true);

    $pages = $d['pages'] ?? array_merge($d['series_pages'] ?? [], $d['related_pages'] ?? []);
    foreach ($pages as $page) {
        $path = parse_url((string)$page['source_url'], PHP_URL_PATH) ?: '';
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
        $rows[$kind][] = [
            'name' => $name,
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
    foreach ($list as $i => $r) {
        foreach ($blocks($tokens($r['name'])) as $bk) { $index[$bk][] = $i; }
    }
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
                $pairs[] = [
                    'kind' => $kind,
                    'a' => $A['name'], 'b' => $B['name'],
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
