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
 *      /article/henry-clay-wiley/                    -> /articles/henry-clay-wiley
 *      /article/surveyors-map-showing-lyons-station/ -> /articles/surveyors-map-showing-lyons-station
 *      /person/tiburcio-vasquez/                     -> /persons/tiburcio-vasquez
 *    Where a target does not exist the anchor is stripped and the words kept.
 *    Replacements are root relative, so they survive a move between local,
 *    staging and production.
 *
 * 4. events/northridge-earthquake carries two links to scvhistory.com. Those
 *    are left alone and reported, since a reference to the legacy site may be
 *    deliberate. Nathan decides.
 *
 * Field access is guarded twice, the way import_wp_media.php and
 * clean_bodies.php do it: the handle is checked against that entry's own field
 * layout first, and every get and set is still wrapped in try/catch. The first
 * version called getFieldValue() for every handle in a fixed list, which threw
 * from Element->normalizeFieldValue() on the first entry whose layout did not
 * carry one of them, and the script died before applying anything.
 *
 * Idempotent: every rewrite is anchored on the exact stored URL, so once a link
 * is fixed the pattern no longer matches and a second run reports nothing to do.
 *
 * Dry run by default; it prints all five planned changes and every handle it
 * had to skip. Set $APPLY = true to write.
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

$skippedHandles = [];   // "slug" => ['handle', ...], handles not on that entry's layout
$skipErrors = [];       // handles that were on the layout but still threw

/** Handles actually on this element's field layout. */
$layoutHandles = function ($element) {
    $out = [];
    try {
        $layout = $element->getFieldLayout();
        if ($layout) {
            foreach ($layout->getCustomFields() as $f) { $out[] = $f->handle; }
        }
    } catch (\Throwable $e) {
        // no layout we can read; treat as carrying nothing
    }
    return $out;
};

/** Read a field only when the layout carries it. Returns null otherwise. */
$readField = function ($element, $handle, $handles) use (&$skippedHandles, &$skipErrors) {
    $who = $element->slug ?: (string)$element->id;
    if (!in_array($handle, $handles, true)) {
        $skippedHandles[$who][$handle] = true;
        return null;
    }
    try {
        return $element->getFieldValue($handle);
    } catch (\Throwable $e) {
        $skipErrors[] = $handle . ' threw on ' . $who . ': ' . $e->getMessage();
        return null;
    }
};

echo ($APPLY ? '=== APPLYING ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL;

$planned = 0; $written = 0;

/* ── 1. the misplaced website URL ─────────────────────────────── */

echo PHP_EOL . '--- 1. mission-san-gabriel-arcangel website URL ---' . PHP_EOL;

$org = \craft\elements\Entry::find()->section('organizations')->slug('mission-san-gabriel-arcangel')->status(null)->one();
if (!$org) {
    echo '  entry not found, skipping' . PHP_EOL;
} else {
    $orgHandles = $layoutHandles($org);
    $legacyHandle = null; $value = null;

    foreach (['orgLegacyUrl', 'legacyUrl'] as $h) {
        $v = $readField($org, $h, $orgHandles);
        if (is_string($v) && trim($v) !== '' && strpos(trim($v), '/') !== 0) {
            $legacyHandle = $h; $value = trim($v); break;
        }
    }

    if (!$legacyHandle) {
        echo '  nothing to move; no legacy field holds a bare domain (already fixed, or not present)' . PHP_EOL;
    } elseif (!in_array('orgWebsite', $orgHandles, true)) {
        echo '  found "' . $value . '" in ' . $legacyHandle . ' but orgWebsite is not on this layout; leaving both alone' . PHP_EOL;
    } else {
        $site = $readField($org, 'orgWebsite', $orgHandles);
        $siteHas = $site !== null && (string)$site !== '';
        $planned++;
        echo '  PLAN: move "' . $value . '" out of ' . $legacyHandle . PHP_EOL;
        echo '        orgWebsite currently: ' . ($siteHas ? (string)$site : 'empty') . PHP_EOL;
        echo '        would set orgWebsite = https://' . $value . ($siteHas ? ' (skipped, already set)' : '') . PHP_EOL;
        echo '        would clear ' . $legacyHandle . PHP_EOL;

        if ($APPLY) {
            $vals = [$legacyHandle => ''];
            if (!$siteHas) { $vals['orgWebsite'] = 'https://' . $value; }
            try {
                $org->setFieldValues($vals);
                if ($elements->saveElement($org)) { $written++; echo '        saved' . PHP_EOL; }
                else { echo '        FAILED: ' . json_encode($org->getErrors()) . PHP_EOL; }
            } catch (\Throwable $e) {
                echo '        FAILED: ' . $e->getMessage() . PHP_EOL;
            }
        }
    }
}

/* ── 2 and 3. the stale links ─────────────────────────────────── */

echo PHP_EOL . '--- 2 and 3. stale links in body text ---' . PHP_EOL;

foreach ($REWRITES as $r) {
    $entry = \craft\elements\Entry::find()->section($r['section'])->slug($r['slug'])->status(null)->one();
    if (!$entry) {
        echo '  ' . $r['section'] . '/' . $r['slug'] . ': entry not found' . PHP_EOL;
        continue;
    }

    $handles = $layoutHandles($entry);

    [$targetSection, $targetSlug] = $r['target'];
    $target = \craft\elements\Entry::find()->section($targetSection)->slug($targetSlug)->status(null)->one();
    // Root relative on purpose. getUrl() would bake the current environment's
    // hostname into the stored body, so running this locally would leave
    // scvhistory.ddev.site links in content that then syncs to production.
    $to = ($target && $target->uri) ? '/' . ltrim($target->uri, '/') : null;

    $vals = []; $hits = 0;

    foreach ($TEXT_FIELDS as $h) {
        $v = $readField($entry, $h, $handles);
        if (!is_string($v) || $v === '' || strpos($v, $r['from']) === false) { continue; }

        if ($to) {
            $new = str_replace($r['from'], $to, $v);
            $how = 'rewrite to ' . $to;
        } else {
            $quoted = preg_quote($r['from'], '#');
            $new = preg_replace('#<a\b[^>]*href=["\']' . $quoted . '["\'][^>]*>(.*?)</a>#is', '$1', $v);
            $how = 'no Craft target; strip the anchor and keep the text';
        }

        if ($new !== $v) {
            $vals[$h] = $new; $hits++; $planned++;
            echo '  PLAN: ' . $r['section'] . '/' . $r['slug'] . ' [' . $h . '] ' . $how . PHP_EOL;
        }
    }

    if (!$hits) {
        echo '  ' . $r['section'] . '/' . $r['slug'] . ': "' . $r['from'] . '" not present, nothing to do' . PHP_EOL;
        continue;
    }

    if ($APPLY) {
        try {
            $entry->setFieldValues($vals);
            if ($elements->saveElement($entry)) { $written++; echo '        saved' . PHP_EOL; }
            else { echo '        FAILED: ' . json_encode($entry->getErrors()) . PHP_EOL; }
        } catch (\Throwable $e) {
            echo '        FAILED: ' . $e->getMessage() . PHP_EOL;
        }
    }
}

/* ── 4. left alone on purpose ─────────────────────────────────── */

echo PHP_EOL . '--- 4. left alone, for Nathan to decide ---' . PHP_EOL;

$ne = \craft\elements\Entry::find()->section('events')->slug('northridge-earthquake')->status(null)->one();
if (!$ne) {
    echo '  events/northridge-earthquake not found' . PHP_EOL;
} else {
    $neHandles = $layoutHandles($ne);
    $found = [];
    foreach ($TEXT_FIELDS as $h) {
        $v = $readField($ne, $h, $neHandles);
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

/* ── summary ──────────────────────────────────────────────────── */

echo PHP_EOL . '--- handles skipped, not on that entry\'s layout ---' . PHP_EOL;
if ($skippedHandles) {
    foreach ($skippedHandles as $who => $hs) {
        echo '  ' . str_pad($who, 44) . implode(', ', array_keys($hs)) . PHP_EOL;
    }
} else {
    echo '  none' . PHP_EOL;
}
if ($skipErrors) {
    echo PHP_EOL . '--- handles that were on the layout but still threw ---' . PHP_EOL;
    foreach ($skipErrors as $msg) { echo '  ' . $msg . PHP_EOL; }
}

echo PHP_EOL . sprintf('Changes planned %d, written %d.', $planned, $written) . PHP_EOL;
if (!$APPLY) {
    echo 'Nothing was written. Set $APPLY = true and run again.' . PHP_EOL;
} elseif ($planned === 0) {
    echo 'Nothing left to do; the fixes are already in place.' . PHP_EOL;
}
