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

/* The LW features import as photographs, and documents carry the same legacy
   chrome. A cleaner that covers three sections out of thirteen leaves the
   chrome wherever it is not looking. */
$SECTIONS = ['articles', 'warMemorials', 'obituaries', 'photographs', 'documents'];

/* ------------------------------------------------- dry run against a file

   Set this to an inventory name and the pass reads its bodies from
   inventory/legacy/<name>.json instead of from the database, and reports what
   it would do to them. Nothing is written whatever $APPLY says: there is
   nothing in Craft to write to yet, which is the point.

   The pages are loaded into unsaved Entry objects of the given type, so every
   matcher and both walks run on exactly the code path a real run uses. A
   prediction made with a copy of the rules is worth much less than one made
   with the rules.

   For the 1,544 LW features this answers the only question worth asking before
   they land: how much of the legacy page arrives with them. */
$FROM_INVENTORY = '';          /* e.g. 'lw-features' */
$FROM_TYPE      = 'photograph';
/* The section as well as the type: an unsaved entry with only a typeId returns
   a null field layout, so every body reads back empty and the pass reports that
   1,661 pages are clean. It took a run of zeroes to notice. */
$FROM_SECTION   = 'photographs';
$FROM_LIMIT     = 0;           /* 0 for all */

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

/* Name what is in a run rather than describing its shape.

   The uncertainty report used to say "a short trailing run of non-sentence
   lines" and "a possible caption run". Both are true and neither says that the
   run contains [image:2], which is the illustration layer and must not be
   touched. Three decisions were made on those descriptions and all three would
   have orphaned pictures. So the report names what it found. */
$describe = function (array $run): string {
    $text = implode("\n", $run);
    $found = [];
    if (($n = preg_match_all('~\[image:\d+\]~', $text))) { $found[] = $n . ' image token' . ($n > 1 ? 's' : ''); }
    if (preg_match('~\[sic[:\s]~i', $text)) { $found[] = 'an editor bracket'; }
    if (preg_match('~^\s*\[?\s*\d{1,3}\s*\]?\s*$~mu', $text)) { $found[] = 'a footnote marker'; }
    if (preg_match('~^\s*[A-Z][A-Z0-9 .,\x{2019}\'&()-]{6,}$~mu', $text)) { $found[] = 'an all-capitals line'; }
    if (preg_match('~</?[a-z][^>]*>~i', $text)) { $found[] = 'inline markup'; }
    if (!$found) { return ' (nothing the archive marks as its own)'; }
    return ' CONTAINING ' . implode(', ', $found);
};

/* ---------------------------------------------------------------- matchers */

/* The matchers live in their own file so a dry run against an inventory uses
   the same code as a real run against the database. */
require \Craft::getAlias('@root') . '/scripts/import/_legacy_chrome_matchers.php';


/* ---------------------------------------------------------------- report */

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'sections: ' . implode(', ', $SECTIONS) . PHP_EOL;

$changed = 0; $unchanged = 0; $failed = 0;
$uncertain = [];        /* slug => [lines] */
$galleryCut = [];       /* slug => [what was cut] */
$movedToFinePrint = 0;
$filledDate = []; $filledAuthor = []; $noAuthorRecord = []; $signatures = []; $backLinks = [];
$patternCounts = [];

/* Either the database, or an inventory file loaded into unsaved entries. */
$batches = [];
if ($FROM_INVENTORY !== '') {
    $APPLY = false;
    $p = \Craft::getAlias('@root') . '/inventory/legacy/' . $FROM_INVENTORY . '.json';
    if (!file_exists($p)) { echo 'not found: ' . $p . PHP_EOL; return; }
    $d = json_decode(file_get_contents($p), true);
    $type = Craft::$app->entries->getEntryTypeByHandle($FROM_TYPE);
    if (!$type) { echo 'entry type ' . $FROM_TYPE . ' not found' . PHP_EOL; return; }
    $sec = Craft::$app->entries->getSectionByHandle($FROM_SECTION);
    if (!$sec) { echo 'section ' . $FROM_SECTION . ' not found' . PHP_EOL; return; }
    $pages = [];
    foreach (['pages', 'series_pages', 'related_pages'] as $list) {
        foreach ($d[$list] ?? [] as $pg) { $pages[] = $pg; }
    }
    if ($FROM_LIMIT > 0) { $pages = array_slice($pages, 0, $FROM_LIMIT); }
    $made = [];
    foreach ($pages as $pg) {
        $x = new \craft\elements\Entry();
        $x->sectionId = $sec->id;
        $x->setTypeId($type->id);
        $x->title = (string)($pg['title'] ?? $pg['legacy_key']);
        $x->slug = (string)($pg['legacy_key'] ?? '');
        $x->setFieldValue('body', (string)($pg['body_text'] ?? ''));
        $made[] = $x;
    }
    $batches[$FROM_INVENTORY . ' (' . $FROM_TYPE . ', not imported)'] = $made;
    echo 'reading ' . count($made) . ' bodies from ' . $FROM_INVENTORY . '.json, writing nothing' . PHP_EOL;
} else {
    foreach ($SECTIONS as $sectionHandle) {
        $batches[$sectionHandle] = \craft\elements\Entry::find()->section($sectionHandle)->status(null)->all();
    }
}

foreach ($batches as $sectionHandle => $batchEntries) {
    foreach ($batchEntries as $e) {
        $layout = $e->getFieldLayout();
        if (!$layout) { continue; }
        $has = [];
        foreach ($layout->getCustomFields() as $f) { $has[$f->handle] = true; }
        if (!isset($has['body'])) { continue; }

        $original = (string)$e->body;
        if (trim($original) === '') { continue; }

        $lines = preg_split("~\r\n|\n|\r~", $original);

        /* The one rule that reaches into the middle. See the note on
           stripBackLinks in _legacy_chrome_matchers.php. */
        $backDrop = $stripBackLinks($lines);
        if ($backDrop) {
            $hit('back-link');
            $backLinks[$e->slug] = count($backDrop);
            $lines = array_values(array_filter($lines, fn($i) => !isset($backDrop[$i]), ARRAY_FILTER_USE_KEY));
        }

        $n = count($lines);
        $keepFine = [];
        $hit = function (string $what) use (&$patternCounts) { $patternCounts[$what] = ($patternCounts[$what] ?? 0) + 1; };

        /* ---- head: walk down, stop at the first line we are not sure about ----
           A byline block carries a date and an author that never reached the
           fields. Take them before the block is removed, and only where the
           field is empty; nothing already recorded is overwritten. */
        $start = 0;
        $titleSeen = false;
        $foundDate = ''; $foundAuthor = '';
        $bylineSeen = false; $blockTail = 0;
        while ($start < $n) {
            $raw = $lines[$start];
            $l = trim($raw);
            if ($l === '') { $start++; continue; }

            /* A trail that does not start the line. Excised here rather than in
               the body at large, because the head is where the walk is already
               entitled to judge a line; nothing is removed from the middle of
               the prose. What survives the excision is judged by the rules
               below, exactly as if it had arrived that way. */
            $stripped = $stripTrail($l);
            if ($stripped !== $l) {
                $hit('breadcrumb-trail');
                if ($stripped === '') { $start++; continue; }
                $lines[$start] = $stripped;
                $l = $stripped;
            }

            if ($isBreadcrumb($l))    { $hit('breadcrumb');     $start++; continue; }
            if ($isExtractionMarker($l)) {
                $hit('extraction-marker');
                if ($foundAuthor === '' && preg_match('~\bBY\s+([A-Z][A-Z. \x{2019}\'-]{3,40})$~u', $l, $m)) {
                    $foundAuthor = trim($m[1]);
                }
                $start++; continue;
            }
            if ($isBracketNav($l))    { $hit('bracket-nav');    $start++; continue; }
            if ($isStrayMark($l))     { $hit('stray-mark');     $start++; continue; }

            /* A bracketed index link whose opening bracket has already gone:
               an all caps label whose next non-empty line is a lone "]". */
            if (preg_match('~^[A-Z][A-Z0-9 .&\x{2019}\'-]{4,60}$~u', $l)) {
                $peekIdx = $start + 1;
                while ($peekIdx < $n && trim($lines[$peekIdx]) === '') { $peekIdx++; }
                if ($peekIdx < $n && trim($lines[$peekIdx]) === ']') {
                    $hit('index-link');
                    $start = $peekIdx + 1;
                    continue;
                }
            }

            if ($isForPublication($l)) { $hit('publication'); $start++; continue; }
            /* A placeholder the extraction left where the date should be. */
            if (preg_match('~^date\?$~iu', $l)) { $hit('date-placeholder'); $start++; continue; }
            if ($isSeriesHeading($l)) { $hit('series-heading'); $start++; continue; }
            if (!$titleSeen && $norm($l) !== '' && $norm($l) === $norm((string)$e->title)) {
                $hit('own-title'); $titleSeen = true; $start++; continue;
            }
            /* The shape the newly imported pages carry: one or more title lines,
               then "By X", then the publication, then the date. A title line on
               its own is indistinguishable from an opening sentence, so it is
               only taken when a byline follows it within four lines. */
            if (!$bylineSeen && $isBlockLine($l)) {
                $peekIdx = $start + 1; $seen = 0; $bylineAhead = false;
                while ($peekIdx < $n && $seen < 4) {
                    $pl = trim($lines[$peekIdx]);
                    if ($pl === '') { $peekIdx++; continue; }
                    if ($isByline($pl)) { $bylineAhead = true; break; }
                    if (!$isBlockLine($pl)) { break; }
                    $seen++; $peekIdx++;
                }
                if ($bylineAhead) { $hit('block-title'); $start++; continue; }
            }

            /* Below the byline, the publication and the date. Bounded at three
               lines so an unrecognised publication cannot run into the prose. */
            if ($bylineSeen && $blockTail < 3 && $isBlockLine($l) && !$isDateline($l)) {
                $hit('block-publication');
                if ($foundDate === '') { $foundDate = $pickDate($l); }
                $blockTail++; $start++; continue;
            }

            if ($isByline($l)) {
                $hit('byline');
                $bylineSeen = true;
                if ($foundAuthor === '' && preg_match('~^By\s+(.+?)[,.]?$~u', $l, $m)) {
                    $a = $m[1];
                    $a = preg_replace('~,?\s*[A-Za-z. ]*\b(Curator|Historian|Historical Society|Editor|Staff Writer|Publisher|Archivist)\b\.?$~iu', '', $a);
                    $foundAuthor = trim($a, " \t,.");
                }
                $start++; continue;
            }
            if ($isDateline($l)) {
                $hit('dateline');
                if ($foundDate === '') {
                    $picked = $pickDate($l);
                    $foundDate = ($picked !== '' && mb_strpos($l, '|') !== false) ? $picked : rtrim($l, '.');
                }
                $start++; continue;
            }
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

        /* The signature, before anything else touches the tail: it is the very
           last thing on the page and the copyright walk above it would not know
           what to make of a bare year. Taken once, into the fields that already
           exist for it. */
        $sig = $readSignature($lines, $end, $start);
        if ($sig !== null) {
            [$sigName, $sigYear, $sigLines] = $sig;
            $hit('signature');
            $end -= $sigLines;
            $signatures[] = $e->slug . '  ' . $sigName . ', ' . $sigYear;
            if ($foundAuthor === '') { $foundAuthor = $sigName; }
            if ($foundDate === '') { $foundDate = $sigYear; }
        }

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
                    $uncertain[$e->slug][] = 'run of ' . $run . ' lines at the end'
                        . $describe(array_slice($lines, $probe + 1, $end - $probe))
                        . ', and the line above is none of prose, a copyright line, an exhibit heading or another caption: ' . mb_substr($above, 0, 70);
                }
            } elseif ($run > 0 && $run < 4) {
                $uncertain[$e->slug][] = 'short trailing run of ' . $run . ' line(s), left alone'
                    . $describe(array_slice($lines, $runStart ?? $end, ($end - ($runStart ?? $end)) + 1))
                    . ': ' . mb_substr(trim($lines[$runStart] ?? ''), 0, 70);
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

        /* Fields the head block carried, where nothing is there already. */
        $fieldSets = [];
        if ($foundDate !== '' && isset($has['originalPublishDate'])) {
            $cur = '';
            try { $cur = trim((string)$e->getFieldValue('originalPublishDate')); } catch (\Throwable $ex) {}
            if ($cur === '') { $fieldSets['originalPublishDate'] = $foundDate; }
        }
        if ($foundAuthor !== '' && isset($has['writtenBy'])) {
            $curCount = -1;
            try { $curCount = $e->writtenBy->count(); } catch (\Throwable $ex) {}
            if ($curCount === 0) {
                $person = \craft\elements\Entry::find()->section('persons')->status(null)->title($foundAuthor)->one();
                $how = 'exact';
                /* "By A.B. Perkins" against the record "Arthur Burnett Perkins":
                   same surname, and every initial in the byline opens a given
                   name on the record, in order. Reported apart from an exact
                   match so the inference stays visible. */
                if (!$person && preg_match('~^((?:[A-Z]\.?\s*){1,3})([A-Z][a-z\x{2019}\'-]+)$~u', $foundAuthor, $m2)) {
                    $initials = preg_replace('~[^A-Z]~', '', $m2[1]);
                    $surname  = $m2[2];
                    foreach (\craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->all() as $cand) {
                        $parts = preg_split('~\s+~u', trim((string)$cand->title));
                        if (count($parts) < 2 || array_pop($parts) !== $surname) { continue; }
                        $candInitials = '';
                        foreach ($parts as $g) { $candInitials .= mb_strtoupper(mb_substr($g, 0, 1)); }
                        if (mb_strpos($candInitials, $initials) === 0) { $person = $cand; $how = 'initials'; break; }
                    }
                }
                if ($person) {
                    $fieldSets['writtenBy'] = [$person->id];
                    $filledAuthor[] = $e->slug . '  "' . $foundAuthor . '" -> ' . $person->title . '  #' . $person->id
                        . ($how === 'initials' ? '  (matched on initials and surname)' : '');
                }
                else { $noAuthorRecord[] = $e->slug . '  "' . $foundAuthor . '" has no person record, writtenBy left empty'; }
            }
        }
        if (isset($fieldSets['originalPublishDate'])) { $filledDate[] = $e->slug . '  ' . $foundDate; }

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
        foreach ($fieldSets as $h => $v) {
            echo '  ' . str_pad($h, 11) . ': ' . (is_array($v) ? 'person #' . $v[0] . ' (' . $foundAuthor . ')' : $v) . PHP_EOL;
        }

        if ($APPLY) {
            try { $e->setFieldValue('body', $cleaned); }
            catch (\Throwable $ex) { echo '  set body failed: ' . $ex->getMessage() . PHP_EOL; }
            if ($fineSet !== null) {
                try { $e->setFieldValue('finePrint', $fineSet); }
                catch (\Throwable $ex) { echo '  set finePrint failed: ' . $ex->getMessage() . PHP_EOL; }
            }
            foreach ($fieldSets as $h => $v) {
                try { $e->setFieldValue($h, $v); }
                catch (\Throwable $ex) { echo '  set ' . $h . ' failed: ' . $ex->getMessage() . PHP_EOL; }
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

if ($backLinks) {
    echo PHP_EOL . '=== bracketed BACK links removed, ' . count($backLinks) . ' records ===' . PHP_EOL;
    echo 'The one rule that reaches into the middle of a body. A return link after every' . PHP_EOL;
    echo 'numbered note is navigation wherever it sits, and removing only the last would' . PHP_EOL;
    echo 'leave the rest.' . PHP_EOL;
    arsort($backLinks);
    foreach ($backLinks as $slug => $n) { echo '  ' . str_pad($slug, 44) . $n . ' line(s)' . PHP_EOL; }
}

if ($signatures) {
    echo PHP_EOL . '=== signatures taken out of the body, ' . count($signatures) . ' ===' . PHP_EOL;
    echo 'Each is the author and the year. They render beneath the prose from the record\'s' . PHP_EOL;
    echo 'own fields, so leaving them in the body showed them twice.' . PHP_EOL;
    foreach ($signatures as $r) { echo '  ' . $r . PHP_EOL; }
}

if ($filledDate || $filledAuthor) {
    echo '=== fields filled from the byline block ===' . PHP_EOL;
    foreach ($filledDate as $r) { echo '  originalPublishDate  ' . $r . PHP_EOL; }
    foreach ($filledAuthor as $r) { echo '  writtenBy            ' . $r . PHP_EOL; }
}
if ($noAuthorRecord) {
    echo 'authors named in a byline with no person record:' . PHP_EOL;
    foreach ($noAuthorRecord as $r) { echo '  ' . $r . PHP_EOL; }
}

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
