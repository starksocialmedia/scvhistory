/**
 * The mechanical half of the text quality census, applied.
 *
 * census_text_quality.php sorts every defect it finds into four verdicts. This
 * fixes exactly one of them: the classes where there is no judgement to make.
 * Trailing whitespace, tabs, runs of blank lines, invisible soft hyphens, one
 * stray HTML entity, and the separator in the rights line.
 *
 * It does not touch the review classes, and it does not touch the intended
 * ones. [image:2] is the illustration layer, [sic: ...] is the archive
 * correcting its sources, <em> and <a> are the emphasis layer, and the 703 em
 * dashes are Reynolds writing. A pass like this one is exactly what destroys
 * them, so they are counted before and after and a field whose count moved is
 * not saved.
 *
 * ------------------------------------------------------------- the rights line
 *
 * "©1998 SANTA CLARITA VALLEY HISTORICAL SOCIETY · RIGHTS RESERVED" occupies
 * one character slot with four different characters across 43 records:
 *
 *     U+00B7 middle dot      28
 *     U+2022 bullet           7
 *     U+00AD soft hyphen      6
 *     U+007C vertical bar     2
 *
 * All four become the middle dot, the majority. This runs before the soft
 * hyphen rule on purpose: delete the soft hyphens first and six of these lines
 * lose their separator and keep the two spaces around it, which is a worse line
 * than the one we started with.
 *
 * Only the all-capitals form is touched. Seven other records carry a different
 * rights line in mixed case, with different wording and sometimes a different
 * owner, and those are a consistency question for a person, not a substitution.
 *
 * ---------------------------------------------------------------- the invariant
 *
 * Every rule here is either a whitespace change or one of five named character
 * substitutions. So after the transform, the text with all whitespace stripped
 * must equal the original with those five substitutions applied and all
 * whitespace stripped. If it does not, a rule ate something it should not have,
 * and the field is skipped and reported rather than saved. That check is the
 * reason this is safe to run across every record at once.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Take a database backup first: ddev export-db --file=/tmp/before-mechanical.sql.gz
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/clean_text_mechanical.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$FIELDS = ['body', 'hauntedAccount', 'hauntedSource', 'finePrint', 'personFinePrint',
           'webmasterNoteTop', 'webmasterNoteBottom', 'subheadline', 'authorBio',
           'photoCaptionExt', 'culturalSensitivityNote'];

/* The five character substitutions this pass is licensed to make. Anything not
   on this list and not whitespace must survive untouched. */
$charRules = function (string $s): string {
    /* 1. the rights line, before anything eats the separator */
    $s = preg_replace('~(SOCIETY)[ ]?[\x{00B7}\x{2022}\x{00AD}\x{007C}][ ]?(RIGHTS RESERVED)~u',
        "$1 \u{00B7} $2", $s);
    /* 2. the named entities that leaked into plain text */
    $s = str_replace(['&nbsp;', '&amp;', '&quot;', '&lt;', '&gt;', '&rsquo;', '&ldquo;', '&rdquo;'],
                     [' ', '&', '"', '<', '>', "'", '"', '"'], $s);
    /* 3. whatever soft hyphens are left, now that the separator ones are gone */
    $s = str_replace("\u{00AD}", '', $s);
    return $s;
};

/* Whitespace only. Nothing here may change a character that is not whitespace. */
$spaceRules = function (string $s): string {
    /* a tab in prose, with whatever spaces are around it, is one space */
    $s = preg_replace('~[ ]*\t[ ]*~', ' ', $s);
    /* trailing whitespace on every line */
    $s = preg_replace('~[ \t]+(?=\n)~', '', $s);
    /* Three or more blank lines become one. Four newlines, not three: a
       paragraph break here is two newlines and one blank line, and three
       newlines is two blank lines, which is spacing somebody may have meant.
       The census said three or more blank lines and this pass does exactly
       that and no more. */
    $s = preg_replace('~(\n[ \t]*){4,}~', "\n\n", $s);
    /* and the edges of the field */
    return trim($s);
};

/* What must come through unchanged. */
$INVARIANTS = [
    'image token'    => '~\[image:\d+\]~',
    'editor bracket' => '~\[sic[:\s][^\]]*\]~i',
    'inline markup'  => '~</?(em|strong|i|b|a)\b[^>]*>~i',
    'em dash'        => "~\u{2014}~u",
    'footnote'       => '~\[\s*\d{1,3}\s*\]~',
    'sentence gap'   => '~(?<=[.!?])  +(?=[A-Z"\x27])~',
];

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

$stripws = fn(string $s): string => preg_replace('~\s+~u', '', $s);

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 88) . PHP_EOL;

$tally = ['rights separator' => 0, 'html entity' => 0, 'soft hyphen' => 0,
          'tab' => 0, 'trailing whitespace' => 0, 'blank line run' => 0, 'field edge' => 0];
$plan = [];
$refused = [];
$scanned = 0;

foreach (Craft::$app->entries->getAllSections() as $section) {
    foreach (\craft\elements\Entry::find()->section($section->handle)->status(null)->limit(null)->all() as $e) {
        foreach ($FIELDS as $fh) {
            if (!$hasField($e, $fh)) { continue; }
            $old = $e->getFieldValue($fh);
            if (!is_string($old) || trim($old) === '') { continue; }
            $scanned++;

            $new = $spaceRules($charRules($old));
            if ($new === $old) { continue; }

            /* the invariant */
            if ($stripws($charRules($old)) !== $stripws($new)) {
                $refused[] = $section->handle . '/' . $e->slug . '  ' . $fh
                    . '  the text changed in a way this pass is not licensed to make';
                continue;
            }
            $bad = [];
            foreach ($INVARIANTS as $name => $pat) {
                $a = preg_match_all($pat, $old);
                $b = preg_match_all($pat, $new);
                if ($a !== $b) { $bad[] = $name . ' ' . $a . ' -> ' . $b; }
            }
            if ($bad) {
                $refused[] = $section->handle . '/' . $e->slug . '  ' . $fh . '  ' . implode('; ', $bad);
                continue;
            }

            /* what changed, for the report */
            $what = [];
            if (preg_match('~SOCIETY[ ]?[\x{2022}\x{00AD}\x{007C}][ ]?RIGHTS RESERVED~u', $old)) {
                $what['rights separator'] = 1;
            }
            if (($n = preg_match_all('~&(nbsp|amp|quot|lt|gt|rsquo|ldquo|rdquo);~i', $old))) { $what['html entity'] = $n; }
            $softAll = substr_count($old, "\u{00AD}");
            $softSep = $what['rights separator'] ?? 0;
            if ($softAll - $softSep > 0) { $what['soft hyphen'] = $softAll - $softSep; }
            if (($n = substr_count($old, "\t"))) { $what['tab'] = $n; }
            if (($n = preg_match_all('~[ \t]+(?=\n)~', $old))) { $what['trailing whitespace'] = $n; }
            if (($n = preg_match_all('~(\n[ \t]*){4,}~', $old))) { $what['blank line run'] = $n; }
            if (trim($old) !== $old) { $what['field edge'] = 1; }
            foreach ($what as $k => $n) { $tally[$k] += $n; }

            $plan[] = ['entry' => $e, 'field' => $fh, 'new' => $new,
                       'label' => $section->handle . '/' . $e->slug, 'what' => $what,
                       'delta' => strlen($old) - strlen($new)];
        }
    }
}

echo 'scanned ' . $scanned . ' text fields, ' . count($plan) . ' would change' . PHP_EOL . PHP_EOL;
printf("%-46s %-18s %s\n", 'record', 'field', 'what');
echo str_repeat('-', 88) . PHP_EOL;
foreach ($plan as $p) {
    $bits = [];
    foreach ($p['what'] as $k => $n) { $bits[] = $k . ($n > 1 ? ' x' . $n : ''); }
    printf("%-46s %-18s %s\n", mb_substr($p['label'], 0, 46), $p['field'], implode(', ', $bits));
}

echo PHP_EOL . str_repeat('-', 88) . PHP_EOL;
echo 'fixed, by class:' . PHP_EOL;
foreach ($tally as $k => $n) { if ($n) { printf("   %-24s %d\n", $k, $n); } }
printf("   %-24s %d\n", 'TOTAL', array_sum($tally));

if ($refused) {
    echo PHP_EOL . 'REFUSED, ' . count($refused) . ' fields. The invariant did not hold and nothing was written:' . PHP_EOL;
    foreach ($refused as $r) { echo '   ' . $r . PHP_EOL; }
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 88) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    echo 'Take a backup first: ddev export-db --file=/tmp/before-mechanical.sql.gz' . PHP_EOL;
    return;
}

$elements = Craft::$app->getElements();
$saved = 0; $failed = 0;
$byEntry = [];
foreach ($plan as $p) { $byEntry[$p['entry']->id]['entry'] = $p['entry']; $byEntry[$p['entry']->id]['sets'][$p['field']] = $p['new']; }
foreach ($byEntry as $id => $row) {
    $row['entry']->setFieldValues($row['sets']);
    if ($elements->saveElement($row['entry'])) {
        $saved++;
        echo 'saved #' . $id . '  ' . implode(', ', array_keys($row['sets'])) . PHP_EOL;
    } else {
        $failed++;
        echo 'FAILED #' . $id . ': ' . json_encode($row['entry']->getErrors()) . PHP_EOL;
    }
}
echo PHP_EOL . 'saved ' . $saved . ' records, ' . $failed . ' failed.' . PHP_EOL;
echo 'Re-run census_text_quality.php: the mechanical classes should now read zero.' . PHP_EOL;
