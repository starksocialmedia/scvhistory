/**
 * Quality pass 2 of 5: the byline-and-date line.
 *
 * Most column pieces open with the line the legacy page used as a dateline,
 * "Pauline Harte · September 16, 1997", or with "By Leon Worden" over
 * "Saturday, Sept. 4, 2004". The record already holds both in fields, so the
 * line renders twice. This removes it, and only on an exact match.
 *
 * In order, for a dateline in the first or last three lines:
 *
 *   1. Parse the date on the line. A line that doesn't parse is left alone.
 *   2. If the piece has no originalPublishDate, fill it from the line as
 *      printed, and originalPublishDateEdtf from the parsed date. This happens
 *      whatever the rest of the rule decides: the date is unambiguous.
 *   3. Remove the line only when its date equals the date field (compared as
 *      parsed dates, so "Sept. 4, 2004" matches "September 4, 2004") and any
 *      name on it is the author, on the piece or on its collection.
 *      Nicknames in quotes are ignored for the comparison, so 'Richard "Doc"
 *      Rioux' matches Richard Rioux. A "By Leon Worden" line directly above a
 *      removed date line goes with it on the same terms.
 *   4. Before a line naming the author goes, if the piece has no writtenBy it
 *      is set to the collection's author. On the column pieces the line is the
 *      only record of the author, and the page's byline reads writtenBy, so
 *      without this the byline would disappear with the line.
 *
 * Everything else is listed for a person: a different date on the line and in
 * the field, a name that isn't the author, a date field holding prose. The
 * OTN columns carry no writtenBy on the piece, so for them the collection's
 * author is what the name is checked against. The Gazette collection has no
 * author and its bylines are all kept.
 *
 * Dry run by default, with before and after counts from the quality report.
 * Run:   ddev craft exec "eval(file_get_contents('scripts/import/quality_byline_dates.php'))"
 * Apply: ddev craft exec '$QUALITY_APPLY = true; eval(file_get_contents("scripts/import/quality_byline_dates.php"));'
 */

$APPLY = false;
if (!empty($QUALITY_APPLY)) { $APPLY = true; echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$run = require \Craft::getAlias('@root') . '/scripts/import/_quality_pass.php';

$MONTHS = ['jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
           'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12];
$DATE = '(?:(?:Mon|Tues|Wednes|Thurs|Fri|Satur|Sun)day,?\s+)?'
      . '((?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*)\.?\s+(\d{1,2}),?\s+((?:18|19|20)\d\d)';

/* A date anywhere in a string, as Y-m-d, or null. */
$parse = function (string $s) use ($DATE, $MONTHS): ?string {
    if (!preg_match('~' . $DATE . '~u', $s, $m)) { return null; }
    $mo = $MONTHS[strtolower(substr($m[1], 0, 3))] ?? null;
    if (!$mo || !checkdate($mo, (int)$m[2], (int)$m[3])) { return null; }
    return sprintf('%04d-%02d-%02d', $m[3], $mo, $m[2]);
};

$norm = fn(string $n): string => trim(preg_replace('~\s+~', ' ', preg_replace('~[^a-z ]~', ' ',
    mb_strtolower(preg_replace('~["\x{201C}\x{201D}][^"\x{201C}\x{201D}]*["\x{201C}\x{201D}]~u', ' ', preg_replace('~^By\s+~i', '', $n)))
)));

/* Authors, per collection, read once: normalised name => person id. */
$collAuthors = [];
foreach (\craft\elements\Entry::find()->section('collections')->status(null)->limit(null)->all() as $c) {
    foreach ($c->writtenBy->status(null)->all() as $p) { $collAuthors[(int)$c->id][$norm($p->title)] = (int)$p->id; }
}

$run([
    'script' => 'quality_byline_dates.php',
    'label' => 'Byline and date lines',
    'apply' => $APPLY,
    'classes' => ['dateProse', 'undated'],
    'propose' => function (\craft\elements\Entry $e, int $cid, array &$listed) use ($DATE, $parse, $norm, $collAuthors, $qpRemoveLines): ?array {
        $body = (string)$e->getFieldValue('body');
        $L = explode("\n", $body);
        $idx = [];
        foreach ($L as $i => $l) { if (trim($l) !== '' && !preg_match('~^\[/?(lines|table)\]$~', trim($l))) { $idx[] = $i; } }
        $edge = array_values(array_unique(array_merge(array_slice($idx, 0, 3), array_slice($idx, -3))));

        $field = trim((string)$e->getFieldValue('originalPublishDate'));
        $edtf  = trim((string)$e->getFieldValue('originalPublishDateEdtf'));
        $own = $e->writtenBy->status(null)->ids();
        $authors = array_merge(array_map(fn($p) => $norm($p->title), $e->writtenBy->status(null)->all()), array_keys($collAuthors[$cid] ?? []));
        /* Fill writtenBy before removing a line that names the author, for the
           same reason the date is filled first: on the column pieces that line
           is the only place the author is recorded, and the page takes its
           byline from writtenBy. */
        $fillAuthor = function (string $name) use (&$set, &$show, $own, $norm, $collAuthors, $cid): void {
            $pid = $collAuthors[$cid][$norm($name)] ?? null;
            if ($own || !$pid || isset($set['writtenBy'])) { return; }
            $set['writtenBy'] = [$pid];
            $show[] = '  writtenBy: (empty) -> #' . $pid . ' ' . $name;
        };

        $set = []; $show = []; $drop = [];
        foreach ($edge as $i) {
            $l = trim($L[$i]);
            if (mb_strlen($l) > 70) { continue; }
            $bare = trim($l, " ()\t");
            /* A bare date first, so "Saturday, February 23, 2008" is not read
               as a byline naming Saturday. */
            if (preg_match('~^(' . $DATE . ')\.?$~u', $bare, $d)) { $m = [0 => $bare, 1 => '', 2 => $d[1]]; }
            elseif (!preg_match('~^(?:(.{2,50}?)\s*(?:\x{00B7}|\x{2022}|\||,|\x{2014}|-)\s*)?(' . $DATE . ')\.?$~u', $bare, $m)) {
                /* The report's own test, so nothing it counts goes unaccounted. */
                if ($parse($l) && str_word_count(preg_replace('~[^\pL\s]~u', ' ', preg_replace('~' . $DATE . '~u', '', $l))) <= 4) {
                    $listed[] = 'dateline not in a byline shape, kept: "' . $l . '"';
                }
                continue;
            }
            $name = trim($m[1] ?? '');
            $printed = trim($m[2]);
            $iso = $parse($printed);
            if (!$iso) { $listed[] = 'date on the line does not parse, kept: "' . $l . '"'; continue; }

            /* 2. fill first */
            $fieldIso = $field !== '' ? $parse($field) : null;
            if ($field === '' && !isset($set['originalPublishDate'])) {
                $set['originalPublishDate'] = $printed;
                $show[] = '  originalPublishDate: (empty) -> ' . $printed;
                if ($edtf === '') { $set['originalPublishDateEdtf'] = $iso; $show[] = '  originalPublishDateEdtf: (empty) -> ' . $iso; }
                $fieldIso = $iso;
            } elseif ($field !== '' && (!$fieldIso || mb_strlen($field) > 60)) {
                $listed[] = 'date field does not hold one date ("' . mb_substr($field, 0, 50) . '"), line kept: "' . $l . '"';
                continue;
            }

            /* 3. strip only on an exact match */
            if ($fieldIso !== $iso) { $listed[] = 'line says ' . $iso . ', field says ' . $fieldIso . ', kept: "' . $l . '"'; continue; }
            if ($name !== '' && !in_array($norm($name), $authors, true)) {
                $listed[] = 'name on the line is not the author (' . ($authors ? implode(', ', array_unique($authors)) : 'none recorded') . '), kept: "' . $l . '"';
                continue;
            }
            if ($name !== '') { $fillAuthor($name); }
            $drop[$i] = true; $show[] = '- ' . $l;

            /* a "By <author>" line directly above goes with it */
            $pos = array_search($i, $idx, true);
            if ($pos > 0) {
                $j = $idx[$pos - 1];
                $above = trim($L[$j]);
                if (preg_match('~^By\s+(.{2,50})$~u', $above, $bm) && in_array($norm($bm[1]), $authors, true)) {
                    $fillAuthor($bm[1]);
                    $drop[$j] = true; $show[] = '- ' . $above;
                }
            }
        }
        if ($drop) { $set['body'] = $qpRemoveLines($body, $drop); }
        return $set ? ['set' => $set, 'show' => $show] : null;
    },
]);
