<?php
/**
 * The legacy chrome matchers, shared.
 *
 * These were inline in clean_legacy_bodies.php. They are here so that a dry run
 * against an inventory file uses exactly the code the real run uses against the
 * database: a prediction made with a copy of the rules is worth much less than
 * one made with the rules.
 *
 * Expects $SERIES_HEADINGS and $FOOTER_LINKS to be set by the caller; defaults
 * are supplied if they are not. Defines only closures and two constants, and
 * touches nothing.
 */

if (!isset($SERIES_HEADINGS)) {
    $SERIES_HEADINGS = [
        'THE STORY OF OUR VALLEY BY A.B. PERKINS',
        'HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS',
    ];
}
if (!isset($FOOTER_LINKS)) {
    $FOOTER_LINKS = [
        'RETURN TO TOP', 'RETURN TO MAIN INDEX', 'MAIN INDEX',
        'PHOTO CREDITS', 'BIBLIOGRAPHY', 'BOOKS FOR SALE',
    ];
}

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

/* A breadcrumb trail that does not start the line.
   ww2-augustrubel #518 carried its whole page as one line: the title, then
   "> WAR MEMORIAL HOME > WORLD WAR I > WORLD WAR II > ...", then the service
   record. The rule above needs the > at the start of a line and could not see
   any of it.

   No extraction in hand produces this shape. I reported 317 LW pages carrying
   it and that was wrong: the regex I measured with let \s match a newline, so
   it was counting ordinary line-start breadcrumbs whose previous line happened
   to end in a non-space. Measured within a line, the count is zero everywhere.

   So this is a guard rather than a repair. It excises the trail and keeps what
   sits on either side, because either side may be real text and the walk above
   will judge it. Two segments are required: one "> Something" is a quotation
   as often as it is navigation. */
/* Each segment must be closed by the next ">", so the run can only ever eat
   text that sits between two markers. An unclosed final segment is left alone.
   The first version of this had an open-ended last segment and on #518 it ate
   "Augustus" off the name that followed the trail: a rule meant to remove
   furniture took forty characters of a man's record with it. A guard that can
   damage prose is worse than no guard. */
$TRAIL = '~(?:>[^>\n]{2,60}(?=>)){2,}~u';
$stripTrail = function (string $l) use ($TRAIL): string {
    if (!preg_match('~>~', $l)) { return $l; }
    $out = preg_replace($TRAIL, ' ', $l);
    return trim(preg_replace('~\s+~', ' ', $out));
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

/* "For The Signal", the publication line of a byline block. */
$isForPublication = function (string $l): bool {
    return (bool)preg_match('~^For\s+(The\s+)?[A-Z][A-Za-z.\x{2019}\' -]{2,44}\.?$~u', $l);
};

/* An extraction marker left behind with the series heading attached:
   "START:BYLINE-->HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS". */
$isExtractionMarker = function (string $l): bool {
    return (bool)preg_match('~(START|END):(BYLINE|CONTENT|STORY)~u', $l)
        || (bool)preg_match('~^<!--|-->$~u', $l);
};

/* A publication or a bare month and year, but only read inside a byline block:
   "The Santa Clarita Sentinel", "June 1986.", "Vol. 11 No. 6". Short, opens
   like a heading, and carries no sentence. */
$isBlockLine = function (string $l): bool {
    if (mb_strlen($l) > 90) { return false; }
    if (!preg_match('~^[\(\x{201C}"A-Z0-9]~u', $l)) { return false; }
    if (preg_match('~[.!?]\s+\p{Lu}~u', $l)) { return false; }
    return str_word_count(preg_replace('~[^A-Za-z ]~', ' ', $l)) <= 14;
};

/* A stray opening quote or bracket left by the extraction. */
$isStrayMark = function (string $l): bool {
    return (bool)preg_match('~^[\x{201C}\x{201D}"\x{2018}\x{2019}\'\[\]]{1,2}$~u', $l);
};

$MONTHS = '(January|February|March|April|May|June|July|August|September|October|November|December)';

$isMonthYear = function (string $l) use ($MONTHS): bool {
    return (bool)preg_match('~^' . $MONTHS . '\s+\d{4}\.?$~u', $l);
};

/* The date carried by a head line, which is often a publication and a date
   sharing one line: "The Santa Clarita Sentinel | April 28, 1965". */
$pickDate = function (string $l) use ($MONTHS): string {
    $part = $l;
    if (mb_strpos($l, '|') !== false) {
        $bits = explode('|', $l);
        $part = trim(end($bits));
    }
    if (preg_match('~' . $MONTHS . '\s+\d{1,2},?\s+\d{4}~u', $part, $m)) { return rtrim($m[0], '.'); }
    if (preg_match('~' . $MONTHS . '\s+\d{4}~u', $part, $m))               { return rtrim($m[0], '.'); }
    if (preg_match('~' . $MONTHS . '\s+\d{1,2},?\s+\d{4}~u', $l, $m))    { return rtrim($m[0], '.'); }
    return '';
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

/* A signature block at the foot of a piece.

   Reynolds signs the Prologue "JERRY REYNOLDS" on one line and "1985" on the
   next; Worden signs the Preface "— LEON WORDEN, 1998" on one. Both arrive
   wrapped in <strong>. It is the author and the date, which the record already
   has fields for, so it belongs in those and not in the prose. Left in the body
   it renders twice, once as the last paragraph and once as the record's own
   signature line.

   The year is required. Without it the pattern matches any short line of
   capitals at the end of a body, and across the inventories that is "MANY MORE"
   and "SEE HI JOLLY\'S TOMB", which are navigation. With the year required it
   matches two pages in 2,036 and both are real signatures.

   Returns [name, year, lines consumed] or null. */
$SIGNATURE_NAME = '~^[\x{2014}\x{2013}-]?\s*([A-Z][A-Z.\x{2019}\'-]*(?:\s+[A-Z][A-Z.\x{2019}\'-]*){0,4})\s*(?:,\s*((?:18|19|20)\d{2}))?\.?$~u';
$SIGNATURE_YEAR = '~^((?:18|19|20)\d{2})\.?$~';

$readSignature = function (array $lines, int $end, int $floor) use ($SIGNATURE_NAME, $SIGNATURE_YEAR): ?array {
    $text = fn(int $i): string => trim(html_entity_decode(strip_tags($lines[$i])));

    /* the last non-empty line, and the one above it */
    $last = null; $prev = null;
    for ($i = $end; $i >= $floor; $i--) {
        if (trim($lines[$i]) === '') { continue; }
        if ($last === null) { $last = $i; continue; }
        $prev = $i; break;
    }
    if ($last === null) { return null; }

    /* name on one line, year on the next */
    if ($prev !== null
        && preg_match($SIGNATURE_YEAR, $text($last), $y)
        && preg_match($SIGNATURE_NAME, $text($prev), $n)
        && empty($n[2])) {
        return [$n[1], $y[1], ($end - $prev) + 1];
    }
    /* name and year on one line */
    if (preg_match($SIGNATURE_NAME, $text($last), $n) && !empty($n[2])) {
        return [$n[1], $n[2], ($end - $last) + 1];
    }
    return null;
};

/* [ BACK ] anywhere in a body, including the middle of the prose.

   This is the one place the cleaner reaches past the head and the tail, and it
   is deliberate. Everywhere else the rule is that nothing is removed from the
   middle, because a line in the middle is prose until proved otherwise. A
   bracketed BACK is proved otherwise: it is the return link the legacy notes
   pages put after every numbered note, and editors-notes carries 34 of them.
   Stripping only the trailing one would leave 33 and make the body worse.

   Matched as a whole line, with or without its brackets on their own lines, so
   the shape

       [
       BACK
       ]

   goes as a unit and does not leave an orphan bracket behind. Nothing else on
   the line, ever: a sentence containing the word back is untouched. */
$BACK_LINE = '~^\s*\[?\s*BACK\s*\]?\s*$~iu';
$BRACKET_ONLY = '~^\s*[\[\]]\s*$~u';

$stripBackLinks = function (array $lines) use ($BACK_LINE, $BRACKET_ONLY): array {
    $drop = [];
    foreach ($lines as $i => $l) {
        if (!preg_match($BACK_LINE, $l)) { continue; }
        $drop[$i] = true;
        /* the bracket above, if it is alone on its line */
        for ($j = $i - 1; $j >= 0; $j--) {
            if (trim($lines[$j]) === '') { continue; }
            if (preg_match($BRACKET_ONLY, $lines[$j])) { $drop[$j] = true; }
            break;
        }
        /* and the one below */
        for ($j = $i + 1; $j < count($lines); $j++) {
            if (trim($lines[$j]) === '') { continue; }
            if (preg_match($BRACKET_ONLY, $lines[$j])) { $drop[$j] = true; }
            break;
        }
    }
    return $drop;
};

/* ------------------------------------------------ the quality phase, 22 Sep

   Four shapes the head and tail walks above did not know, found by the
   collection quality report. All measured on the 761 collection pieces:

     the Gazette link bars   35 pieces, first line, two fixed bars
     Disqus                  71 pieces, the last line, the comment widget's
                             "comments powered by Disqus" caught as text
     breadcrumbs             5 pieces, in the head but under a line the walk
                             stopped at, so the walk never reached them
     "Click here" heads      in the head only. The four "Click here" lines in
                             the middle of the prose are sentences that point
                             at a link we no longer have ("[Click here] to read
                             the press release.") and are left for a person.

   A bar is split on the pipes and only the cells named below are removed. A
   cell that is not named stays, so the one bar carrying a heading,
   '| Home | | Old Town Newhall, USA | "Images" | Biography | RICHARD "DOC"
   RIOUX', keeps RICHARD "DOC" RIOUX as its line. */
$NAV_BAR_CELLS = [
    'home', 'home to newhall', 'go home to newhall', 'more newhall news', 'gazette archive',
    'city of santa clarita', 'scvtv', 'scv history in pictures', 'july 4 parade', 'contact us',
    'old town newhall, usa', '"images"', 'images', 'biography',
];

/* Returns the line with its nav cells removed: '' when nothing else was in it,
   the line unchanged when it is not a bar of known cells. */
$stripNavBar = function (string $l) use ($NAV_BAR_CELLS): string {
    if (substr_count($l, '|') < 2) { return $l; }
    $cells = array_values(array_filter(array_map('trim', explode('|', $l)), 'strlen'));
    $keep = []; $navCount = 0;
    foreach ($cells as $c) {
        if (in_array(mb_strtolower($c), $NAV_BAR_CELLS, true)) { $navCount++; } else { $keep[] = $c; }
    }
    if ($navCount < 2) { return $l; }
    return implode(' | ', $keep);
};

$isDisqus = function (string $l): bool {
    return (bool)preg_match('~^comments powered by disqus\.?$~iu', trim($l));
};

/* A "Click here" line that is only a link: short, and nothing after the link
   text that reads as a sentence about something else. */
$isClickHereNav = function (string $l): bool {
    $l = trim($l);
    if (mb_strlen($l) > 70) { return false; }
    return (bool)preg_match('~^(>>|\[)?\s*click here\b[^.]*?(<<|\])?$~iu', $l);
};
