/**
 * Where did the added text come from?
 *
 * 24 records carry lines the legacy page does not. Two possible origins, and
 * they need different answers:
 *
 *   in the WordPress body   an editor wrote it in WordPress before the Craft
 *                           migration. It is content, and the question becomes
 *                           who wrote it.
 *   not in the WordPress    it entered during one of our own passes, and that
 *   body either             is a bug, and it wants finding before 1,544
 *                           photographs import.
 *
 * Read only. Writes review/fidelity-investigation.md and touches nothing.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/investigate_added_text.php'))"
 */

$OUT = \Craft::getAlias('@review') . '/fidelity-investigation.md';
$root = \Craft::getAlias('@root');

$summary = json_decode(file_get_contents(\Craft::getAlias('@review') . '/fidelity-summary.json'), true);
$wp = json_decode(file_get_contents($root . '/inventory/wp_content.json'), true);
$wpBySlug = [];
foreach ($wp['posts'] ?? [] as $post) {
    if (!empty($post['slug'])) { $wpBySlug[$post['slug']] = $post; }
}

$fp = function (string $line): string {
    $s = mb_strtolower(trim($line));
    $s = preg_replace('~\[image:\d+\]~', ' ', $s);
    $s = preg_replace('~</?[a-z][^>]*>~i', ' ', $s);
    $s = preg_replace('~&[a-z]+;|&#\d+;~i', ' ', $s);
    $s = preg_replace('~[^\p{L}\p{N}]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};
/* A line is "in" a text when its words appear in it as a run. Compared on a
   flattened fingerprint of the whole body, because WordPress wraps paragraphs
   differently from the extraction and a line-for-line match would miss. */
$flat = function (string $text) use ($fp): string { return $fp(str_replace("\n", ' ', $text)); };

$inv = [];
foreach (['perkins', 'reynolds-full', 'warmemorial', 'worden'] as $name) {
    $p = $root . '/inventory/legacy/' . $name . '.json';
    if (!file_exists($p)) { continue; }
    $d = json_decode(file_get_contents($p), true);
    foreach (['pages', 'series_pages', 'related_pages'] as $list) {
        foreach ($d[$list] ?? [] as $pg) { $inv[$name][$pg['legacy_key']] = $pg; }
    }
}

$rows = [];
foreach ($summary['appeared'] as $slugPath => $a) {
    $entry = \craft\elements\Entry::find()->id($a['id'])->status(null)->one();
    if (!$entry) { continue; }
    $page = $inv[$a['inventory']][$a['legacyKey']] ?? null;
    $legacy = (string)($page['body_text'] ?? '');
    $craft = (string)$entry->body;

    $aSet = array_flip(array_filter(array_map($fp, preg_split('~\R~u', $legacy)), fn($l) => $l !== ''));
    $onlyB = array_values(array_filter(array_map($fp, preg_split('~\R~u', $craft)),
        fn($l) => $l !== '' && !isset($aSet[$l])));

    $post = $wpBySlug[$entry->slug] ?? null;
    $wpFlat = $post ? $flat((string)($post['body'] ?? '')) : null;

    $inWp = []; $notInWp = [];
    foreach ($onlyB as $line) {
        if ($line === '') { continue; }
        /* Short lines match by accident; require something to match on. */
        $probe = $line;
        if (str_word_count($probe) > 14) {
            $w = explode(' ', $probe);
            $probe = implode(' ', array_slice($w, 0, 14));
        }
        if ($wpFlat !== null && $probe !== '' && str_contains($wpFlat, $probe)) { $inWp[] = $line; }
        else { $notInWp[] = $line; }
    }

    $rows[] = [
        'slug' => $slugPath, 'id' => $entry->id, 'title' => (string)$entry->title,
        'inventory' => $a['inventory'], 'legacyKey' => $a['legacyKey'],
        'wpPost' => $post ? ($post['id'] ?? '?') . ' ' . ($post['title'] ?? '') : null,
        'lines' => count($onlyB), 'inWp' => $inWp, 'notInWp' => $notInWp,
    ];
}

/* ------------------------------------------------------------------ verdicts */

$allInWp = array_filter($rows, fn($r) => $r['inWp'] && !$r['notInWp']);
$allNot  = array_filter($rows, fn($r) => !$r['inWp'] && $r['notInWp']);
$mixed   = array_filter($rows, fn($r) => $r['inWp'] && $r['notInWp']);
$noPost  = array_filter($rows, fn($r) => $r['wpPost'] === null);

$totLines = array_sum(array_column($rows, 'lines'));
$totIn = array_sum(array_map(fn($r) => count($r['inWp']), $rows));
$totNot = array_sum(array_map(fn($r) => count($r['notInWp']), $rows));

$md = [];
$md[] = '# Where the added text came from';
$md[] = '';
$md[] = 'Generated ' . (new DateTime())->format('j F Y, g:ia')
    . ' by `scripts/import/investigate_added_text.php`. Read only.';
$md[] = '';
$md[] = '24 records carry lines the legacy page does not. Each of those lines is tested';
$md[] = 'against the body of the matching WordPress post in `inventory/wp_content.json`,';
$md[] = 'matched by slug. A line found there was written in WordPress before the Craft';
$md[] = 'migration and is content. A line found in neither entered during one of our own';
$md[] = 'passes and is a bug.';
$md[] = '';
$md[] = 'Comparison is on words alone, case, spacing and punctuation ignored, against the';
$md[] = 'whole WordPress body flattened to one line, because WordPress wraps paragraphs';
$md[] = 'differently from the extraction. Lines longer than fourteen words are probed on';
$md[] = 'their first fourteen, so a line the editor lightly rewrote still matches.';
$md[] = '';
$md[] = '## The answer';
$md[] = '';
$md[] = '| | records | lines |';
$md[] = '|---|---|---|';
$md[] = '| every added line is in the WordPress body | ' . count($allInWp) . ' | ' . array_sum(array_map(fn($r) => count($r['inWp']), $allInWp)) . ' |';
$md[] = '| no added line is in the WordPress body | ' . count($allNot) . ' | ' . array_sum(array_map(fn($r) => count($r['notInWp']), $allNot)) . ' |';
$md[] = '| some of each | ' . count($mixed) . ' | ' . array_sum(array_map(fn($r) => $r['lines'], $mixed)) . ' |';
$md[] = '| **total** | **' . count($rows) . '** | **' . $totLines . '** |';
$md[] = '';
$md[] = 'Lines in the WordPress body: **' . $totIn . '**. Lines in neither: **' . $totNot . '**.';
if ($noPost) {
    $md[] = '';
    $md[] = count($noPost) . ' of the records have no WordPress post at all under their slug, so for';
    $md[] = 'those the test can only say the text is not in WordPress, not that it never was:';
    foreach ($noPost as $r) { $md[] = '- `' . $r['slug'] . '` (' . $r['inventory'] . ')'; }
}

foreach ([['CONTENT: written in WordPress', $allInWp, 'inWp'],
          ['BUG: in neither the page nor WordPress', $allNot, 'notInWp'],
          ['BOTH: some of each', $mixed, null]] as [$head, $set, $which]) {
    $md[] = '';
    $md[] = '## ' . $head . ' — ' . count($set) . ' records';
    if (!$set) { $md[] = ''; $md[] = 'None.'; continue; }
    foreach ($set as $r) {
        $md[] = '';
        $md[] = '### `' . $r['slug'] . '` #' . $r['id'];
        $md[] = '';
        $md[] = '- legacy: `' . $r['inventory'] . ' / ' . $r['legacyKey'] . '`';
        $md[] = '- WordPress post: ' . ($r['wpPost'] ?? '**none under this slug**');
        $md[] = '- added lines: ' . $r['lines'] . ' (' . count($r['inWp']) . ' in WordPress, '
            . count($r['notInWp']) . ' in neither)';
        if ($r['inWp']) {
            $md[] = '';
            $md[] = 'In the WordPress body:';
            $md[] = '';
            foreach ($r['inWp'] as $l) { $md[] = '- ' . mb_substr($l, 0, 220); }
        }
        if ($r['notInWp']) {
            $md[] = '';
            $md[] = 'In neither:';
            $md[] = '';
            foreach ($r['notInWp'] as $l) { $md[] = '- ' . mb_substr($l, 0, 220); }
        }
    }
}

/* ------------------------------------------ what it means for the photographs */

/* The one line that is neither content nor reflow is the legacy navigation
   sitting inside a body. clean_legacy_bodies.php strips a breadcrumb by
   matching "^>" at the start of a line; where the trail sits after something
   else on the same line, it is invisible to that rule. Counting the shape
   across the pages that are about to import is the actionable part. */
$midline = '~\S\s*>\s*[A-Z][A-Z \x27&.-]{3,}~';
$startline = '~^\s*>\s*\S~m';
$shapes = [];
foreach (['lw-features', 'warmemorial', 'perkins', 'reynolds-full', 'worden'] as $name) {
    $p = $root . '/inventory/legacy/' . $name . '.json';
    if (!file_exists($p)) { continue; }
    $d = json_decode(file_get_contents($p), true);
    $pages = [];
    foreach (['pages', 'series_pages', 'related_pages'] as $list) {
        foreach ($d[$list] ?? [] as $pg) { $pages[] = $pg; }
    }
    $mid = 0; $start = 0;
    foreach ($pages as $pg) {
        $b = (string)($pg['body_text'] ?? '');
        if (preg_match($midline, $b)) { $mid++; }
        if (preg_match($startline, $b)) { $start++; }
    }
    $shapes[$name] = ['pages' => count($pages), 'mid' => $mid, 'start' => $start];
}

$cleanerSections = ['articles', 'warMemorials', 'obituaries'];

$md[] = '';
$md[] = '## What the one bug means for the 1,544 photographs';
$md[] = '';
$md[] = 'The single line that is neither content nor reflow is the legacy navigation trail';
$md[] = 'inside a body: `ww2-augustrubel` #518 is 500 characters and all of it is the page';
$md[] = 'title followed by `> WAR MEMORIAL HOME > WORLD WAR I > WORLD WAR II > ...`.';
$md[] = '';
$md[] = '`clean_legacy_bodies.php` strips a breadcrumb by matching `^>` at the start of a';
$md[] = 'line. Where the trail sits after something else on the same line it is invisible';
$md[] = 'to that rule, which is why this one survived. So did the census: its';
$md[] = '`legacy-nav-in-body` class uses the same anchored pattern.';
$md[] = '';
$md[] = 'That shape, counted across the inventories:';
$md[] = '';
$md[] = '| inventory | pages | trail mid-line, escapes the cleaner | trail at line start, caught |';
$md[] = '|---|---|---|---|';
foreach ($shapes as $name => $c) {
    $md[] = '| ' . $name . ' | ' . $c['pages'] . ' | ' . $c['mid'] . ' | ' . $c['start'] . ' |';
}
$md[] = '';
$md[] = 'Two things follow, and the second is the larger one.';
$md[] = '';
$md[] = '1. The mid-line breadcrumb needs adding to `clean_legacy_bodies.php` and to the';
$md[] = '   census class, or it stays invisible wherever it appears.';
$md[] = '';
$md[] = '2. `clean_legacy_bodies.php` runs over `' . implode('`, `', $cleanerSections) . '` only.';
$md[] = '   The LW features import as **photographs**, a section the cleaner does not touch,';
$md[] = '   so they would arrive with all their chrome and not merely this one shape.';
$md[] = '   That wants settling before the import, not after.';

file_put_contents($OUT, implode("\n", $md) . "\n");

echo 'records: ' . count($rows) . ', added lines: ' . $totLines . PHP_EOL;
echo 'in the WordPress body: ' . $totIn . '   in neither: ' . $totNot . PHP_EOL;
echo 'all in WP: ' . count($allInWp) . '   none in WP: ' . count($allNot) . '   mixed: ' . count($mixed) . PHP_EOL;
echo 'no WP post under the slug: ' . count($noPost) . PHP_EOL;
echo PHP_EOL . 'wrote review/fidelity-investigation.md' . PHP_EOL;
