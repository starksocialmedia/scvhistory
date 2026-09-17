/**
 * Fixes the content level link problems found by the batch 5 link crawl.
 * These are data, not templates, so they are corrected here rather than
 * papered over in Twig.
 *
 * 1. organizations/mission-san-gabriel-arcangel has a website URL,
 *    sangabrielmission.org, sitting in its legacy URL field, which is for
 *    paths on the old scvhistory.com site. Moves it to orgWebsite and clears
 *    the legacy field.
 *
 * 2. articles/chapter-9-the-trail-blazer links to /person/pedro-fages/, a
 *    WordPress era path. persons/pedro-fages exists, so the href is rewritten.
 *
 * 3. Three links to the old WordPress host are rewritten to their Craft
 *    equivalents. All three targets were checked and all three exist:
 *      /article/henry-clay-wiley/                 -> /articles/henry-clay-wiley
 *      /article/surveyors-map-showing-lyons-station/ -> /articles/surveyors-map-showing-lyons-station
 *      /person/tiburcio-vasquez/                  -> /persons/tiburcio-vasquez
 *    Where a target did not exist the script strips the anchor and keeps the
 *    text; that path is implemented but is not needed for these three.
 *
 * 4. events/northridge-earthquake carries two links to scvhistory.com. Those
 *    are left alone and reported, since a reference to the legacy site may be
 *    deliberate. Nathan decides.
 *
 * Every rewrite is anchored on the exact stored URL, so running twice is a
 * no-op. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_bad_links.php'))"
 */

$APPLY = true;

$WP = 'https://wordpress-1656314-6593552.cloudwaysapps.com';

$REWRITES = [
    ['section' => 'articles', 'slug' => 'chapter-9-the-trail-blazer',
     'from' => '/person/pedro-fages/', 'target' => ['persons', 'pedro-fages']],

    ['section' => 'obituaries', 'slug' => 'in-memoriam-henry-clay-wiley-1829-1898',
     'from' => $WP . '/article/henry-clay-wiley/', 'target' => ['articles', 'henry-clay-wiley']],

    ['section' => 'obituaries', 'slug' => 'in-memoriam-henry-clay-wiley-1829-1898',
     'from' => $WP . '/article/surveyors-map-showing-lyons-station/', 'target' => ['articles', 'surveyors-map-showing-lyons-station']],

    ['section' => 'persons', 'slug' => 'remi-nadeau-i',
     'from' => $WP . '/person/tiburcio-vasquez/', 'target' => ['persons', 'tiburcio-vasquez']],
];

$TEXT_FIELDS = ['body', 'wmNarrative', 'mpNarrative', 'authorBio',
                'webmasterNoteTop', 'webmasterNoteBottom',
                'personWebmasterNoteTop', 'personWebmasterNoteBottom',
                'obitWebmasterNoteTop', 'obitWebmasterNoteBottom'];

$elements = Craft::$app->getElements();

echo ($APPLY ? '=== APPLYING ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL;

/* ── 1. the misplaced website URL ─────────────────────────────── */

echo PHP_EOL . '--- 1. mission-san-gabriel-arcangel website URL ---' . PHP_EOL;

$org = \craft\elements\Entry::find()->section('organizations')->slug('mission-san-gabriel-arcangel')->status(null)->one();
if (!$org) {
    echo '  entry not found, skipping' . PHP_EOL;
} else {
    $legacyHandle = null; $value = null;
    foreach (['orgLegacyUrl', 'legacyUrl'] as $h) {
        $v = $org->getFieldValue($h);
        if (is_string($v) && trim($v) !== '' && strpos(trim($v), '/') !== 0) {
            $legacyHandle = $h; $value = trim($v); break;
        }
    }
    if (!$legacyHandle) {
        echo '  nothing to move; the legacy field holds no bare domain' . PHP_EOL;
    } else {
        $site = $org->getFieldValue('orgWebsite');
        $siteHas = $site && (string)$site !== '';
        echo '  found "' . $value . '" in ' . $legacyHandle . PHP_EOL;
        echo '  orgWebsite currently: ' . ($siteHas ? (string)$site : 'empty') . PHP_EOL;
        echo '  would set orgWebsite = https://' . $value . ' and clear ' . $legacyHandle . PHP_EOL;
        if ($APPLY) {
            $vals = [$legacyHandle => ''];
            if (!$siteHas) {
                $vals['orgWebsite'] = 'https://' . $value;
            } else {
                echo '  orgWebsite already set, leaving it and only clearing the legacy field' . PHP_EOL;
            }
            $org->setFieldValues($vals);
            if ($elements->saveElement($org)) { echo '  saved' . PHP_EOL; }
            else { echo '  FAILED: ' . json_encode($org->getErrors()) . PHP_EOL; }
        }
    }
}

/* ── 2 and 3. the stale links ─────────────────────────────────── */

echo PHP_EOL . '--- 2 and 3. stale links in body text ---' . PHP_EOL;

$changed = 0;

foreach ($REWRITES as $r) {
    $entry = \craft\elements\Entry::find()->section($r['section'])->slug($r['slug'])->status(null)->one();
    if (!$entry) {
        echo '  ' . $r['section'] . '/' . $r['slug'] . ': entry not found' . PHP_EOL;
        continue;
    }

    [$targetSection, $targetSlug] = $r['target'];
    $target = \craft\elements\Entry::find()->section($targetSection)->slug($targetSlug)->status(null)->one();
    $to = $target ? $target->getUrl() : null;

    $vals = []; $hits = 0;
    foreach ($TEXT_FIELDS as $h) {
        $v = $entry->getFieldValue($h);
        if (!is_string($v) || $v === '' || strpos($v, $r['from']) === false) {
            continue;
        }
        if ($to) {
            $new = str_replace($r['from'], $to, $v);
            $how = 'rewritten to ' . $to;
        } else {
            // no Craft equivalent: drop the anchor, keep the words
            $quoted = preg_quote($r['from'], '#');
            $new = preg_replace('#<a\b[^>]*href=["\']' . $quoted . '["\'][^>]*>(.*?)</a>#is', '$1', $v);
            $how = 'anchor stripped, text kept';
        }
        if ($new !== $v) { $vals[$h] = $new; $hits++; echo '  ' . $r['section'] . '/' . $r['slug'] . ' [' . $h . '] ' . $how . PHP_EOL; }
    }

    if (!$hits) {
        echo '  ' . $r['section'] . '/' . $r['slug'] . ': "' . $r['from'] . '" not present, nothing to do' . PHP_EOL;
        continue;
    }
    if ($APPLY) {
        $entry->setFieldValues($vals);
        if ($elements->saveElement($entry)) { $changed++; echo '      saved' . PHP_EOL; }
        else { echo '      FAILED: ' . json_encode($entry->getErrors()) . PHP_EOL; }
    }
}

/* ── 4. left alone on purpose ─────────────────────────────────── */

echo PHP_EOL . '--- 4. left alone, for Nathan to decide ---' . PHP_EOL;

$ne = \craft\elements\Entry::find()->section('events')->slug('northridge-earthquake')->status(null)->one();
if ($ne) {
    $found = [];
    foreach ($TEXT_FIELDS as $h) {
        $v = $ne->getFieldValue($h);
        if (!is_string($v) || $v === '') { continue; }
        if (preg_match_all('#https?://(?:www\.)?scvhistory\.com[^\s"\'<>]*#i', $v, $m)) {
            foreach ($m[0] as $u) { $found[] = $h . ': ' . $u; }
        }
    }
    if ($found) {
        echo '  events/northridge-earthquake links to the legacy site:' . PHP_EOL;
        foreach ($found as $f) { echo '    ' . $f . PHP_EOL; }
        echo '  These may be deliberate references. Not touched.' . PHP_EOL;
    } else {
        echo '  events/northridge-earthquake: no scvhistory.com links found' . PHP_EOL;
    }
}

echo PHP_EOL . ($APPLY
    ? 'Done. Entries changed: ' . $changed
    : 'Nothing was written. Set $APPLY = true and run again.') . PHP_EOL;
