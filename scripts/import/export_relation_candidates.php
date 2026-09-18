/**
 * Exports the relation candidates for every article to web/review/relations.json
 * for the review screen. Read only: it never writes to the database.
 *
 * A candidate is an entity the extraction found on that article's legacy page.
 * The join is the page URL: entity_index carries a pages list per entity, and an
 * article carries the legacy path it came from, so an entity is a candidate for
 * an article when that article's source URL is in its pages list.
 *
 * Each candidate carries what the reviewer needs to decide without leaving the
 * screen: the name, kind and mention count, the pages it appears on, whether a
 * record already exists in Craft by title or alias, which promotion rule from
 * GROK-CONTRACT.md it satisfies, and the flags the extraction set.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_relation_candidates.php'))"
 */

$root = \Craft::getAlias('@root');
$out = \Craft::getAlias('@webroot') . '/review/relations.json';

$INVENTORIES = ['perkins', 'reynolds-full', 'warmemorial'];
$KINDS = ['people' => 'person', 'places' => 'place', 'organizations' => 'organization'];
$SECTION_FOR = ['person' => 'persons', 'place' => 'places', 'organization' => 'organizations'];
$ALIAS_FOR = ['person' => 'personAliases', 'place' => 'placeAliases', 'organization' => 'orgAliases'];
$FIELD_FOR = ['person' => 'subjectPerson', 'place' => 'depictsPlace', 'organization' => 'subjectOrganization'];

$norm = function (string $s): string {
    $s = mb_strtolower(trim($s));
    $s = str_replace(["\u{2019}", "\u{2018}", "\u{00E9}", "\u{00E8}", "\u{00ED}", "\u{00F3}", "\u{00FA}", "\u{00E1}", "\u{00F1}"],
                     ["'", "'", 'e', 'e', 'i', 'o', 'u', 'a', 'n'], $s);
    $s = preg_replace('~\b(mr|mrs|ms|miss|dr|fr|father|capt|captain|col|colonel|gen|general|lt|lieutenant|rev|reverend|sgt|sergeant|maj|major|don|dona|senor|sister|brother|judge)\.?\s+~u', '', $s);
    $s = preg_replace('~[^a-z0-9 ]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};
$surname = function (string $s) use ($norm): string {
    $parts = preg_split('~\s+~', $norm($s), -1, PREG_SPLIT_NO_EMPTY);
    return $parts ? end($parts) : '';
};

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/* ---- existing records, indexed by normalised title and by every alias ---- */

$records = [];      /* kind => [normalised name => ['id','title','url']] */
$bySurname = [];    /* kind => [surname => [ ['id','title'] ]] */
foreach ($SECTION_FOR as $kind => $section) {
    $records[$kind] = []; $bySurname[$kind] = [];
    foreach (\craft\elements\Entry::find()->section($section)->status(null)->all() as $e) {
        $row = ['id' => $e->id, 'title' => (string)$e->title, 'url' => (string)$e->url];
        $names = [(string)$e->title];
        $aliasHandle = $ALIAS_FOR[$kind];
        if ($hasField($e, $aliasHandle)) {
            try {
                foreach (preg_split("~\r\n|\n|\r~", (string)$e->getFieldValue($aliasHandle)) as $a) {
                    if (trim($a) !== '') { $names[] = trim($a); }
                }
            } catch (\Throwable $ex) {}
        }
        foreach ($names as $n) {
            $k = $norm($n);
            if ($k !== '' && !isset($records[$kind][$k])) { $records[$kind][$k] = $row + ['matchedOn' => $n]; }
        }
        $sn = $surname((string)$e->title);
        if ($sn !== '') { $bySurname[$kind][$sn][] = $row; }
    }
}
echo 'existing records: ';
foreach ($records as $k => $r) { echo $k . ' ' . count($r) . ' names  '; }
echo PHP_EOL;

/* ---- articles, indexed by the absolute legacy URL of their source page ---- */

$articleByUrl = [];
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->all() as $e) {
    $lu = $hasField($e, 'legacyUrl') ? trim((string)$e->legacyUrl) : '';
    if ($lu === '') { continue; }
    $path = parse_url($lu, PHP_URL_PATH) ?: $lu;
    $articleByUrl[$path] = $e;
}
echo 'articles with a legacy path: ' . count($articleByUrl) . PHP_EOL;

/* ---- flags the extraction set, keyed by the page they were raised on ---- */

$flagsByPage = [];
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
}

/* ---- walk the entity index and build a row per article and candidate ---- */

$rows = [];
$stats = ['candidates' => 0, 'matched' => 0, 'unmatched' => 0, 'noArticle' => 0];
$byKind = []; $byRule = []; $byFlag = [];

foreach ($INVENTORIES as $inv) {
    $p = $root . '/inventory/legacy/' . $inv . '.json';
    if (!file_exists($p)) { continue; }
    $ei = json_decode(file_get_contents($p), true)['entity_index'] ?? [];

    foreach ($KINDS as $group => $kind) {
        foreach (($ei[$group] ?? []) as $ent) {
            $name = trim((string)($ent['name_raw'] ?? ''));
            if ($name === '') { continue; }
            $pages = $ent['pages'] ?? [];
            $variants = array_values(array_filter(array_map('trim', $ent['name_variants'] ?? [])));
            $hasLegacy = !empty($ent['has_legacy_page']);
            $legacyUrl = trim((string)($ent['legacy_page_url'] ?? ''));

            /* Promotion rule, from GROK-CONTRACT.md. */
            if ($hasLegacy) { $rule = 'has its own legacy page'; $promote = true; }
            elseif (count($pages) >= 3) { $rule = 'named on ' . count($pages) . ' pages'; $promote = true; }
            else { $rule = 'named on ' . count($pages) . ' page' . (count($pages) === 1 ? '' : 's') . ', stays a tag'; $promote = false; }

            /* Existing record, by title or by any alias. */
            $key = $norm($name);
            $match = $records[$kind][$key] ?? null;
            if (!$match) {
                foreach ($variants as $v) {
                    if (isset($records[$kind][$norm($v)])) { $match = $records[$kind][$norm($v)]; break; }
                }
            }

            /* Near matches: same surname, different full name. This is what
               surfaces "Anne Darcy" next to a record for "Jo Anne Darcy".
               The extraction does not carry a split_given_name flag, so it is
               derived here rather than read. */
            $near = [];
            if (!$match && $kind === 'person') {
                $sn = $surname($name);
                foreach (($bySurname[$kind][$sn] ?? []) as $cand) {
                    if ($norm($cand['title']) !== $key) { $near[] = $cand; }
                }
            }

            foreach ($pages as $pageUrl) {
                $path = parse_url((string)$pageUrl, PHP_URL_PATH) ?: '';
                $article = $articleByUrl[$path] ?? null;
                if (!$article) { $stats['noArticle']++; continue; }

                /* Flags raised on this page that name this entity. */
                $flags = [];
                foreach (($flagsByPage[$path] ?? []) as $fl) {
                    if (mb_stripos($fl['detail'], $name) !== false) {
                        $flags[] = $fl;
                        $byFlag[$fl['reason']] = ($byFlag[$fl['reason']] ?? 0) + 1;
                    }
                }
                if ($near) { $byFlag['near_match_in_craft'] = ($byFlag['near_match_in_craft'] ?? 0) + 1; }

                $rows[] = [
                    'entryId' => $article->id,
                    'articleTitle' => (string)$article->title,
                    'articleSlug' => $article->slug,
                    'articleUrl' => (string)$article->url,
                    'inventory' => $inv,
                    'kind' => $kind,
                    'field' => $FIELD_FOR[$kind],
                    'name' => $name,
                    'variants' => $variants,
                    'mentionCount' => (int)($ent['mention_count'] ?? 0),
                    'pageCount' => count($pages),
                    'pages' => array_slice($pages, 0, 12),
                    'hasLegacyPage' => $hasLegacy,
                    'legacyPageUrl' => $legacyUrl,
                    'rule' => $rule,
                    'promote' => $promote,
                    'existing' => $match,
                    'nearMatches' => $near,
                    'flags' => $flags,
                ];
                $stats['candidates']++;
                if ($match) { $stats['matched']++; } else { $stats['unmatched']++; }
                $byKind[$kind] = ($byKind[$kind] ?? 0) + 1;
                $byRule[$rule === 'has its own legacy page' ? 'legacy page' : ($promote ? 'three or more pages' : 'one or two pages')] =
                    ($byRule[$rule === 'has its own legacy page' ? 'legacy page' : ($promote ? 'three or more pages' : 'one or two pages')] ?? 0) + 1;
            }
        }
    }
}

/* Group by article, in reading order, so the screen can page through them. */
usort($rows, function ($a, $b) {
    return [$a['articleTitle'], $a['kind'], -$b['mentionCount']] <=> [$b['articleTitle'], $b['kind'], -$a['mentionCount']];
});

$articles = [];
foreach ($rows as $r) { $articles[$r['entryId']] = true; }

file_put_contents($out, json_encode([
    'meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/export_relation_candidates.php',
        'candidates' => count($rows),
        'articles' => count($articles),
    ],
    'candidates' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo '=== summary ===' . PHP_EOL;
echo 'candidates exported:   ' . count($rows) . PHP_EOL;
echo 'articles covered:      ' . count($articles) . PHP_EOL;
echo 'already match a record: ' . $stats['matched'] . PHP_EOL;
echo 'no record yet:         ' . $stats['unmatched'] . PHP_EOL;
echo 'on a page with no article in Craft: ' . $stats['noArticle'] . PHP_EOL;
echo 'by kind:' . PHP_EOL;
foreach ($byKind as $k => $n) { echo '  ' . str_pad($k, 16) . $n . PHP_EOL; }
echo 'by promotion rule:' . PHP_EOL;
foreach ($byRule as $k => $n) { echo '  ' . str_pad($k, 22) . $n . PHP_EOL; }
echo 'flags shown to the reviewer:' . PHP_EOL;
foreach ($byFlag as $k => $n) { echo '  ' . str_pad($k, 26) . $n . PHP_EOL; }
echo 'wrote ' . $out . PHP_EOL;
