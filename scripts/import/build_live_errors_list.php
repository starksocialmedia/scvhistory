/**
 * READ ONLY against the database. One work list of the live errors Grok's
 * dossiers found, which were scattered across four files (Nathan, 29 September
 * 2026). Writes inventory/review/live-errors.json and live-errors.md.
 *
 * For each error: the page, the wrong text, the correction, the source, and
 * where it lives now, found rather than assumed:
 *   IN CRAFT          a Craft record carries the page, and the wrong text is
 *                     in that record's content
 *   RECORD, NOT FOUND a Craft record carries the page, but the quoted text was
 *                     not found in it (reworded, or an index or caption Craft
 *                     rebuilds): check by eye
 *   LEGACY ONLY       no Craft record carries the page: an index, a legacy
 *                     title tag, or a page not yet migrated. Fix it in the
 *                     migration, or on the live site
 *   ARCHIVE DATA      not a page at all: an error in the archive's own records
 * Grok's dossiers are final (its quota ran out), so this is the whole list.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_live_errors_list.php'))"
 */

$root = \Craft::getAlias('@root');
$DOSSIERS = ['william-s-hart' => 'Hart', 'edward-f-beale' => 'Beale', 'ygnacio-del-valle' => 'del Valle', 'jerry-reynolds' => 'Reynolds'];
$CARE = ['RL8' => 'TRIBAL CONSULTATION FIRST. The Bowers Cave column tells readers how to find an archaeological site. It was already imported, as article #2177, live at /articles/bowers-cave: disable it (disable_bowers_cave.php) until consultation; do not quote the locational passage anywhere (Nathan, 29 September 2026).'];

/* Every record's legacy identifiers, and its content for the quote search. */
$rows = (new \craft\db\Query())->select(['es.elementId', 'es.title', 'es.content', 'sec' => 's.handle', 'el.enabled'])
    ->from(['es' => '{{%elements_sites}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = es.elementId')
    ->innerJoin(['en' => '{{%entries}}'], 'en.id = es.elementId')->innerJoin(['s' => '{{%sections}}'], 's.id = en.sectionId')
    ->where(['el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->all();
$byKey = [];
foreach ($rows as $r) {
    $c = json_decode((string)$r['content'], true) ?: [];
    foreach ($c as $v) {
        if (!is_string($v) || strlen($v) > 300) { continue; }
        if (preg_match('~(?:^|/)([a-z][a-z0-9_\-]*?)(?:\.html?)?$~i', trim($v), $m) && preg_match('~(\.htm|/scvhistory/|^[a-z]{1,12}\d)~i', $v)) { $byKey[strtolower($m[1])][$r['elementId']] = $r; }
    }
}
$norm = fn(string $t) => strtolower(preg_replace('~\s+~', ' ', html_entity_decode(strip_tags(str_replace(['\\n', '\\"', '\\u2019', '\\u201c', '\\u201d'], [' ', '"', "'", '"', '"'], $t)))));

$out = []; $n = 0;
foreach ($DOSSIERS as $file => $who) {
    $d = json_decode(file_get_contents("$root/inventory/review/$file-sources.json"), true);
    foreach ($d['live_errors'] as $e) {
        $n++;
        /* The page identifiers named in the error. */
        preg_match_all('~(?:signal/[a-z]+/)?([a-z][a-z0-9_]*\d[a-z0-9_]*|bealescut|bealeafb|tejonranchtimeline|sauguscafe|bibliography|film|ridge|tataviam)(?:\.html?)?~i', $e['page'], $m);
        $keys = array_values(array_unique(array_map('strtolower', $m[1])));
        $recs = [];
        foreach ($keys as $k) { foreach ($byKey[$k] ?? [] as $id => $r) { $recs[$id] = $r; } }
        /* The wrong text: the longest quoted run, ellipses split out. */
        preg_match_all('~["\x{201C}\']([^"\x{201D}]{12,})["\x{201D}\']~u', $e['quote'], $q);
        $frags = [];
        foreach ($q[1] as $s) { foreach (preg_split('~\s*\.\.\.\s*|\s*…\s*~u', $s) as $f) { if (mb_strlen(trim($f)) >= 12) { $frags[] = trim($f); } } }
        usort($frags, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $frag = $frags[0] ?? '';
        $hits = [];
        if ($frag !== '') {
            /* A short fragment ("seventeen-year-old") also occurs in unrelated
               records, so under 25 characters only the page's own records count. */
            $pool = mb_strlen($frag) >= 25 ? $rows : array_values($recs);
            foreach ($pool as $r) { if (str_contains($norm((string)$r['content']), $norm($frag))) { $hits[$r['elementId']] = $r; } }
        }
        if (str_starts_with($e['page'], 'CC migration data')) { $where = 'ARCHIVE DATA'; }
        elseif ($hits) { $where = 'IN CRAFT'; }
        elseif ($recs) { $where = 'RECORD, NOT FOUND'; }
        else { $where = 'LEGACY ONLY'; }
        $craft = array_map(fn($r) => '#' . $r['elementId'] . ' ' . $r['sec'] . ($r['enabled'] ? '' : ' (disabled)'), $hits ?: $recs);
        $out[] = ['n' => $n, 'dossier' => $who, 'id' => $e['id'], 'page' => $e['page'], 'error' => $e['quote'], 'correction' => $e['correct'], 'source' => $e['evidence'],
            'where' => $where, 'craft' => array_values($craft), 'matched_text' => $hits ? $frag : '', 'care' => $CARE[$e['id']] ?? ''];
    }
}
$count = array_count_values(array_column($out, 'where'));
file_put_contents("$root/inventory/review/live-errors.json", json_encode(['built' => date('c'), 'by' => 'scripts/import/build_live_errors_list.php', 'total' => count($out), 'counts' => $count, 'errors' => $out], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$md = ["# Live errors: one work list", '', 'Built by `scripts/import/build_live_errors_list.php` from Grok\'s four final dossiers (Hart, Beale, del Valle, Reynolds; Perkins did not land). ' . count($out) . ' errors. Where each lives was found, not assumed: a Craft record carrying the page, and the wrong text searched for in every record.', '',
    '| where | count |', '| --- | --- |'];
foreach (['IN CRAFT', 'RECORD, NOT FOUND', 'LEGACY ONLY', 'ARCHIVE DATA'] as $k) { $md[] = "| $k | " . ($count[$k] ?? 0) . ' |'; }
$md[] = '';
$md[] = '**IN CRAFT**: the wrong text is in a Craft record now. **RECORD, NOT FOUND**: a Craft record carries the page but the text was not found in it; check by eye. **LEGACY ONLY**: no Craft record carries the page (an index, a title tag, a page not migrated): fix in the migration or on the live site. **ARCHIVE DATA**: an error in our own records.';
$md[] = '';
$md[] = '**CARE: RL8, Bowers Cave (Reynolds column of 14 December 1984).** The page tells readers how to find an archaeological site. Grok flagged it for tribal consultation. It was already imported, as article #2177; `disable_bowers_cave.php` takes it off the site until consultation. Do not quote the locational passage in any record, note or report.';
$md[] = '';
foreach ($out as $x) {
    $md[] = '## ' . $x['n'] . '. ' . $x['dossier'] . ' ' . $x['id'] . ': ' . $x['where'] . ($x['care'] ? ' **(CARE)**' : '');
    $md[] = '';
    $md[] = '- **Page:** ' . $x['page'];
    $md[] = '- **Error:** ' . ($x['care'] ? 'the column gives a locational description of an archaeological site (not repeated here)' : $x['error']);
    $md[] = '- **Correction:** ' . $x['correction'];
    $md[] = '- **Source:** ' . $x['source'];
    if ($x['craft']) { $md[] = '- **Craft:** ' . implode(', ', $x['craft']) . ($x['matched_text'] ? ' (text found: "' . mb_substr($x['matched_text'], 0, 80) . '")' : ''); }
    if ($x['care']) { $md[] = '- **CARE:** ' . $x['care']; }
    $md[] = '';
}
$md[] = '## Reprinted errors';
$md[] = '';
$md[] = 'Reynolds lists five errors that also sit on his chapter pages (the Hart L12 and L13, del Valle L1 and L7 entries above, and a set still unflagged: part54 Hap-A-Lan morgue and cowboy-suit burial; part28 "Naval Academy" and "$5,000"; part25 "mayor"; part26 the Tejon sale before 1861). They are the same errors, counted once above.';
file_put_contents("$root/inventory/review/live-errors.md", implode("\n", $md) . "\n");
echo 'total ' . count($out) . ' ' . json_encode($count) . PHP_EOL;
foreach ($out as $x) { echo str_pad($x['dossier'] . ' ' . $x['id'], 16) . str_pad($x['where'], 19) . implode(', ', $x['craft']) . PHP_EOL; }
