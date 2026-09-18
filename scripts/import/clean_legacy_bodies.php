/**
 * Removes legacy site chrome from imported bodies.
 *
 * The extraction contract required verbatim text, so bodies imported from the
 * legacy site carry the page furniture around the prose: breadcrumbs, the series
 * heading, the headline repeated, the byline and dateline, bracketed navigation,
 * the copyright line, the footer link row and, at the end, the captions of an
 * unrelated thumbnail gallery. This is the reviewed cleanup pass.
 *
 * Two rules govern everything here:
 *
 *   Nothing is ever removed from the middle of the prose. The head is stripped
 *   only by walking down from the first line and stopping at the first line that
 *   does not match a known pattern; the tail only by walking up from the last.
 *
 *   A line that is not certain is never removed. It stops the walk and is listed
 *   under "not confident" for a human to look at.
 *
 * The copyright line and the rights statement are data worth keeping, so they
 * move into finePrint rather than being deleted, appended when finePrint already
 * has content and skipped when it already holds them.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/clean_legacy_bodies.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$SECTIONS = ['articles', 'warMemorials', 'obituaries'];

/* Series or section headings that appear as a line of body text. */
$SERIES_HEADINGS = [
    'THE STORY OF OUR VALLEY BY A.B. PERKINS',
    'HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS',
];

/* Footer link row. Deleted outright: these are navigation, not data. */
$FOOTER_LINKS = [
    'RETURN TO TOP', 'RETURN TO MAIN INDEX', 'MAIN INDEX',
    'PHOTO CREDITS', 'BIBLIOGRAPHY', 'BOOKS FOR SALE',
];

$elements = Craft::$app->getElements();

/* ---------------------------------------------------------------- matchers */

$norm = function (string $s): string {
    $s = mb_strtolower(trim($s));
    $s = str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{2013}", "\u{2014}"], ["'", "'", '"', '"', '-', '-'], $s);
    $s = preg_replace('~\((cont|cont\.|continued)\)~u', '', $s);
    $s = preg_replace('~[^a-z0-9 ]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};

$isBreadcrumb = function (string $l): bool {
    return (bool)preg_match('~^>\s*\S~u', $l);
};

$isBracketNav = function (string $l): bool {
    if (preg_match('~^[\[\]\s]+$~u', $l)) { return true; }                 // residue: [ ]  ][
    return (bool)preg_match('~^(\[\s*(NEXT|PREVIOUS|PREV|CONTENTS|INDEX|SEARCH|HOME|BACK)\s*\]\s*)+$~iu', $l);
};

$isSeriesHeading = function (string $l) use ($SERIES_HEADINGS, $norm): bool {
    $n = $norm($l);
    foreach ($SERIES_HEADINGS as $h) { if ($n === $norm($h)) { return true; } }
    return false;
};

$isByline = function (string $l): bool {
    if (preg_match('~^By\s+[A-Z(]~u', $l)) { return true; }
    if (preg_match('~^Originally published\b~iu', $l)) { return true; }
    if (preg_match('~^[A-Z][A-Za-z .\'\x{2019}-]{2,40}\s(Historian|Historical Society)\.?$~u', $l)) { return true; }
    return false;
};

$isDateline = function (string $l): bool {
    $M = '(January|February|March|April|May|June|July|August|September|October|November|December)';
    if (preg_match('~^' . $M . '\s+\d{1,2},?\s+\d{4}\.?$~u', $l)) { return true; }
    if (preg_match('~^(Mon|Tues|Wednes|Thurs|Fri|Satur|Sun)day,?\s+' . $M . '\s+\d{1,2},?\s+\d{4}\b~u', $l)) { return true; }
    /* "The Newhall Signal and Saugus Enterprise | January 2, 1947." */
    if (preg_match('~\|\s*' . $M . '\s+\d{1,2},?\s+\d{4}\.?$~u', $l)) { return true; }
    return false;
};

$isCopyright = function (string $l): bool {
    return mb_strpos($l, "\u{00A9}") !== false
        || preg_match('~\bRIGHTS RESERVED\b~iu', $l)
        || preg_match('~^\(c\)\s*\d{4}~iu', $l);
};

$isRights = function (string $l): bool {
    return (bool)preg_match('~^The site owner makes no assertions~iu', $l);
};

$isNonprofit = function (string $l): bool {
    return (bool)preg_match('~501\s*\(\s*c\s*\)~iu', $l)
        || (preg_match('~\bSCVTV\b~u', $l) && preg_match('~\bnon-?profit\b~iu', $l));
};

$isFooterLink = function (string $l) use ($FOOTER_LINKS): bool {
    $n = mb_strtoupper(trim($l, " \t.\u{2022}\u{00B7}"));
    return in_array($n, $FOOTER_LINKS, true);
};

/* A caption from the thumbnail gallery: short, and not a sentence. */
$isCaptionish = function (string $l): bool {
    if (mb_strlen($l) > 60) { return false; }
    if (preg_match('~[.!?]$~u', $l)) { return false; }
    return true;
};

/* An exhibit or section heading sitting above a gallery, all caps. */
$isExhibitHeading = function (string $l): bool {
    if (mb_strlen($l) < 10 || mb_strlen($l) > 120) { return false; }
    if (preg_match('~\p{Ll}~u', $l)) { return false; }
    return (bool)preg_match('~\p{Lu}~u', $l);
};

/* What may sit immediately above a trailing gallery and still let it be cut.
   The line itself is never removed; this only decides whether the run below it
   is furniture. Clear prose, a copyright line, an exhibit heading, or another
   caption, which is any short line whatever its punctuation. */
$isRunTerminator = function (string $l) use ($isCopyright, $isExhibitHeading): string {
    if (mb_strlen($l) >= 70 && preg_match('~[.!?"\x{201D}]$~u', $l)) { return 'prose'; }
    if ($isCopyright($l)) { return 'copyright line'; }
    if ($isExhibitHeading($l)) { return 'exhibit heading'; }
    if (mb_strlen($l) <= 90) { return 'another caption'; }
    return '';
};

/* ---------------------------------------------------------------- report */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'sections: ' . implode(', ', $SECTIONS) . PHP_EOL;

$changed = 0; $unchanged = 0; $failed = 0;
$uncertain = [];        /* slug => [lines] */
$galleryCut = [];       /* slug => [what was cut] */
$movedToFinePrint = 0;
$patternCounts = [];

foreach ($SECTIONS as $sectionHandle) {
    foreach (\craft\elements\Entry::find()->section($sectionHandle)->status(null)->all() as $e) {
        $layout = $e->getFieldLayout();
        if (!$layout) { continue; }
        $has = [];
        foreach ($layout->getCustomFields() as $f) { $has[$f->handle] = true; }
        if (!isset($has['body'])) { continue; }

        $original = (string)$e->body;
        if (trim($original) === '') { continue; }

        $lines = preg_split("~\r\n|\n|\r~", $original);
        $n = count($lines);
        $keepFine = [];
        $hit = function (string $what) use (&$patternCounts) { $patternCounts[$what] = ($patternCounts[$what] ?? 0) + 1; };

        /* ---- head: walk down, stop at the first line we are not sure about ---- */
        $start = 0;
        $titleSeen = false;
        while ($start < $n) {
            $raw = $lines[$start];
            $l = trim($raw);
            if ($l === '') { $start++; continue; }

            if ($isBreadcrumb($l))    { $hit('breadcrumb');     $start++; continue; }
            if ($isBracketNav($l))    { $hit('bracket-nav');    $start++; continue; }
            if ($isSeriesHeading($l)) { $hit('series-heading'); $start++; continue; }
            if (!$titleSeen && $norm($l) !== '' && $norm($l) === $norm((string)$e->title)) {
                $hit('own-title'); $titleSeen = true; $start++; continue;
            }
            if ($isByline($l))        { $hit('byline');   $start++; continue; }
            if ($isDateline($l))      { $hit('dateline'); $start++; continue; }
            break;
        }

        /* The walk stops at the first line it does not recognise, which is what
           keeps it from eating prose. That also means a byline sitting below an
           unrecognised line survives, so say so rather than leave it silent. */
        if ($start < $n) {
            $blocker = trim($lines[$start]);
            $peek = $start + 1; $seen = 0;
            while ($peek < $n && $seen < 4) {
                $pl = trim($lines[$peek]);
                if ($pl === '') { $peek++; continue; }
                $seen++;
                if ($isByline($pl) || $isDateline($pl)) {
                    $uncertain[$e->slug][] = 'byline or dateline left in place, because the line above it was not recognised: '
                        . mb_substr($blocker, 0, 70) . '  ||  ' . mb_substr($pl, 0, 60);
                    break;
                }
                $peek++;
            }
        }

        /* ---- tail ----
           The copyright and footer walk and the gallery run feed each other: once
           a gallery is cut, the copyright line that sat above it is at the end and
           can be moved. So the two run in a loop until neither moves. Both only
           ever walk up from the last line, so the middle stays unreachable. */
        $end = $n - 1;
        do {
            $moved = false;

            while ($end > $start) {
                $l = trim($lines[$end]);
                if ($l === '') { $end--; $moved = true; continue; }

                if ($isCopyright($l) || $isRights($l) || $isNonprofit($l)) {
                    $hit($isCopyright($l) ? 'copyright' : ($isRights($l) ? 'rights' : 'nonprofit'));
                    $keepFine[] = $l;
                    $end--; $moved = true; continue;
                }
                if ($isFooterLink($l)) { $hit('footer-link'); $end--; $moved = true; continue; }
                break;
            }

            /* A trailing run of four or more short non-sentence lines is the
               thumbnail gallery's captions. The line above it must be something
               the run can hang off; that line is kept either way. */
            $run = 0; $runStart = null; $probe = $end;
            while ($probe > $start) {
                $l = trim($lines[$probe]);
                if ($l === '') { $probe--; continue; }
                if ($isCaptionish($l)) { $run++; $runStart = $probe; $probe--; continue; }

                /* A short line that ends in punctuation is still part of the
                   gallery when what sits above it is more gallery. "New Boiler
                   1893?" and an all caps exhibit heading are captions; "R.I.P."
                   and a lettered footnote are not, because prose sits above
                   them. Look up three lines to tell the two apart. */
                if (mb_strlen($l) <= 90 && !$isCopyright($l)) {
                    $ahead = 0; $peek2 = $probe - 1;
                    while ($peek2 > $start && $ahead < 3) {
                        $pl2 = trim($lines[$peek2]);
                        if ($pl2 === '') { $peek2--; continue; }
                        if (!$isCaptionish($pl2)) { break; }
                        $ahead++; $peek2--;
                    }
                    if ($ahead >= 3) { $run++; $runStart = $probe; $probe--; continue; }
                }
                break;
            }
            if ($run >= 4 && $probe > $start) {
                $above = trim($lines[$probe]);
                $why = $isRunTerminator($above);
                if ($why !== '') {
                    $hit('gallery-captions');
                    $galleryCut[$e->slug][] = $run . ' caption line(s), kept the ' . $why . ' above: ' . mb_substr($above, 0, 60);
                    $end = $probe;
                    $moved = true;
                } else {
                    $uncertain[$e->slug][] = 'possible caption run of ' . $run . ' lines, but the line above it is none of prose, a copyright line, an exhibit heading or another caption: ' . mb_substr($above, 0, 80);
                }
            } elseif ($run > 0 && $run < 4) {
                $uncertain[$e->slug][] = 'short trailing run of ' . $run . ' non-sentence line(s), left alone: ' . mb_substr(trim($lines[$runStart] ?? ''), 0, 80);
            }
        } while ($moved);

        $kept = array_slice($lines, $start, $end - $start + 1);

        /* ---- rejoin a drop cap: "I" then "n presenting the ..." ---- */
        $out = [];
        for ($i = 0; $i < count($kept); $i++) {
            $l = trim($kept[$i]);
            if (preg_match('~^[A-Z]$~u', $l)) {
                $j = $i + 1;
                while ($j < count($kept) && trim($kept[$j]) === '') { $j++; }
                if ($j < count($kept) && preg_match('~^[a-z]~u', trim($kept[$j]))) {
                    $hit('drop-cap');
                    $out[] = $l . trim($kept[$j]);
                    $i = $j;
                    continue;
                }
            }
            $out[] = $kept[$i];
        }

        $cleaned = trim(preg_replace("~\n{3,}~", "\n\n", implode("\n", $out)));

        if ($cleaned === trim($original) && !count($keepFine)) { $unchanged++; continue; }
        if ($cleaned === '') {
            $uncertain[$e->slug][] = 'cleaning would empty the body, left alone';
            $unchanged++;
            continue;
        }

        /* ---- finePrint ---- */
        $fineSet = null;
        if (count($keepFine) && isset($has['finePrint'])) {
            $existing = trim((string)$e->finePrint);
            $add = [];
            foreach (array_reverse($keepFine) as $k) {
                if ($existing !== '' && mb_strpos($existing, $k) !== false) { continue; }
                $add[] = $k;
            }
            if (count($add)) {
                $fineSet = trim($existing === '' ? implode("\n", $add) : $existing . "\n" . implode("\n", $add));
                $movedToFinePrint += count($add);
            }
        } elseif (count($keepFine)) {
            $uncertain[$e->slug][] = 'no finePrint field on this type, so the copyright line would be lost: ' . mb_substr($keepFine[0], 0, 70);
        }

        $changed++;
        $removed = mb_strlen(trim($original)) - mb_strlen($cleaned);
        $flat = function (string $s): string { return preg_replace('~\s+~', ' ', trim($s)); };

        echo str_repeat('-', 78) . PHP_EOL;
        echo $sectionHandle . '  ' . $e->slug . '  #' . $e->id . '   removed ' . $removed . ' chars' . PHP_EOL;
        echo '  head before: ' . mb_substr($flat($original), 0, 120) . PHP_EOL;
        echo '  head after : ' . mb_substr($flat($cleaned), 0, 120) . PHP_EOL;
        echo '  tail before: ' . mb_substr($flat($original), -120) . PHP_EOL;
        echo '  tail after : ' . mb_substr($flat($cleaned), -120) . PHP_EOL;
        if ($fineSet !== null) { echo '  finePrint  : ' . mb_substr($flat($fineSet), 0, 120) . PHP_EOL; }

        if ($APPLY) {
            try { $e->setFieldValue('body', $cleaned); }
            catch (\Throwable $ex) { echo '  set body failed: ' . $ex->getMessage() . PHP_EOL; }
            if ($fineSet !== null) {
                try { $e->setFieldValue('finePrint', $fineSet); }
                catch (\Throwable $ex) { echo '  set finePrint failed: ' . $ex->getMessage() . PHP_EOL; }
            }
            if (!$elements->saveElement($e)) {
                echo '  SAVE FAILED: ' . json_encode($e->getErrors()) . PHP_EOL;
                $failed++;
            }
        }
    }
}

echo str_repeat('=', 78) . PHP_EOL;
echo 'records that would change: ' . $changed . PHP_EOL;
echo 'records already clean:     ' . $unchanged . PHP_EOL;
if ($failed) { echo 'saves failed:              ' . $failed . PHP_EOL; }
echo 'lines moved to finePrint:  ' . $movedToFinePrint . PHP_EOL;

echo '=== patterns matched ===' . PHP_EOL;
ksort($patternCounts);
foreach ($patternCounts as $k => $v) { echo '  ' . str_pad($k, 20) . $v . PHP_EOL; }
if (!count($patternCounts)) { echo '  none' . PHP_EOL; }

if ($galleryCut) {
    echo '=== trailing galleries cut ===' . PHP_EOL;
    foreach ($galleryCut as $slug => $notes) {
        echo '  ' . $slug . PHP_EOL;
        foreach ($notes as $note) { echo '      ' . $note . PHP_EOL; }
    }
}

echo '=== not confident, nothing removed ===' . PHP_EOL;
if (!count($uncertain)) {
    echo '  none' . PHP_EOL;
} else {
    foreach ($uncertain as $slug => $notes) {
        echo '  ' . $slug . PHP_EOL;
        foreach ($notes as $note) { echo '      ' . $note . PHP_EOL; }
    }
}
echo 'A second run changes nothing: every pattern above is gone once removed, and' . PHP_EOL;
echo 'a copyright line already present in finePrint is not appended again.' . PHP_EOL;
