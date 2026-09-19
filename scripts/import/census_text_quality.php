/**
 * A census of what is wrong with the stored text, by class.
 *
 * The point is one clean-up pass rather than six. Every previous pass was
 * written after somebody noticed one thing: the emphasis splitting sentences,
 * the unparsed header block on Bowers Cave, the chrome at the head and foot.
 * Each was found by reading, each fixed one class, and each left the others
 * sitting there to be found later. This reads every stored body once and sorts
 * everything it finds into classes, so the next pass can be specified whole.
 *
 * Read only. It writes web/review/text-quality.json and touches nothing in
 * Craft. There is no $APPLY here because there is nothing to apply: a census
 * that could also write would be a cleanup pass, which is the next thing, not
 * this thing.
 *
 * Every class carries a verdict, and the verdict is the useful part:
 *
 *   mechanical  A pass can fix it with no judgement. Trailing spaces, tabs,
 *               runs of blank lines. Safe to do in bulk on every record.
 *
 *   review      Something is wrong and a person has to decide what. A legacy
 *               heading left in the prose, a subheading fused into the sentence
 *               after it. A script can find these and must not fix them.
 *
 *   intended    Present, correct, and the thing a naive pass would destroy.
 *               [image:2] is the rendered image layer, <em> is the emphasis
 *               layer, the em dashes are Reynolds writing. Listed here so the
 *               cleanup pass is written knowing what not to touch, which is the
 *               half of the job that is easy to get wrong.
 *
 *   outlier     One instance against a settled convention. A single curly
 *               quote among 1,096 straight ones is not a style, it is a typo.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/census_text_quality.php'))"
 */

$OUT = \Craft::getAlias('@webroot') . '/review/text-quality.json';

/* Every field that holds prose. Titles and one-line fields are left out: they
   have their own problems and they are not what a body cleanup pass touches. */
$FIELDS = ['body', 'hauntedAccount', 'hauntedSource', 'finePrint', 'webmasterNoteTop',
           'webmasterNoteBottom', 'subheadline', 'authorBio', 'photoCaptionExt',
           'culturalSensitivityNote', 'orgAliases', 'personAliases'];

/* A class is: what it matches, what it is, what a pass should do about it. */
$CLASSES = [

    /* ------------------------------------------------------------ mechanical */
    'trailing-whitespace' => ['mechanical', '~[ \t]+(?=\n)~',
        'Spaces or tabs at the end of a line.',
        'Strip them. Nothing renders them and nothing means them.'],

    'tab-character' => ['mechanical', '~\t~',
        'A tab inside prose, left by the extraction rather than typed.',
        'Replace with a single space, or strip where it is the whole indent.'],

    'blank-line-run' => ['mechanical', '~\n[ \t]*\n[ \t]*\n[ \t]*\n~',
        'Four or more newlines in a row, so three or more blank lines.',
        'Collapse to one blank line. A paragraph break is two newlines here.'],

    'soft-hyphen' => ['mechanical', "~\u{00ad}~u",
        'U+00AD, an invisible hyphen the source used for line breaking.',
        'Delete. It is invisible until it lands mid-word in a search index.'],

    'html-entity' => ['mechanical', '~&(amp|nbsp|quot|lt|gt|mdash|ndash|rsquo|ldquo|rdquo|#\d+);~i',
        'An HTML entity sitting in a field that is not HTML.',
        'Decode it. &nbsp; in particular reads as a stray character.'],

    'field-edge-blank' => ['mechanical', '~\A\s*\n|\n\s*\z~',
        'A blank line at the very start or end of the field.',
        'Trim the field.'],

    /* ---------------------------------------------------------------- review */
    'legacy-nav-in-body' => ['review', '~^\s*(>.*|MORE JERRY REYNOLDS|\[Click here[^\]]*\]|RETURN TO [A-Z ]+)\s*$~mu',
        'A line of the legacy site\'s navigation still inside the prose: a breadcrumb, '
        . 'a "MORE JERRY REYNOLDS" footer, a bracketed "Click here".',
        'Delete the line, after checking it is not the only pointer to something. '
        . 'clean_legacy_bodies.php removes these from the head and foot but stops at the '
        . 'first line it does not recognise, so anything further in survived.'],

    'fused-subheading' => ['review', '~(?<![\w\x27])([A-Z0-9][\w\x27.]*)\s+\1(?![\w\x27])~',
        'A word repeated immediately, in the same case. Almost always a subheading '
        . 'that lost its line break and ran into the sentence below it: '
        . '"Recent Mining Mining has always been", "Events of 1869 1869 opened cheerily".',
        'Restore the break and mark the subheading. A machine cannot tell where the '
        . 'heading ends, so this is a person\'s job, but the list is short.'],

    'bare-caps-line' => ['review', '~^[A-Z0-9][A-Z0-9 .,\x27&()-]{11,}$~mu',
        'A whole line in capitals with no markup: a subheading the extraction flattened.',
        'Decide whether these become headings in the body or are dropped. They are '
        . 'the same problem as fused-subheading, caught before the break was lost.'],

    'breadcrumb-trail-inline' => ['review', '~\S[^\n>]*(?:>[ \t]*[A-Z][^\n>]{2,40}){2,}~',
        'A navigation trail sitting after something else on the same line, rather than '
        . 'starting one: "Augustus A. Rubel, 1899-1943. > WAR MEMORIAL HOME > WORLD WAR I > ...". '
        . 'The legacy-nav-in-body class above cannot see this, because it anchors on > at '
        . 'the start of a line, and that is how ww2-augustrubel #518 went unreported.',
        'Cut the trail and keep what is on either side. clean_legacy_bodies.php now does '
        . 'this during the head walk.'],

    'date-placeholder' => ['review', '~^\s*date\?\s*$~imu',
        'The literal word "Date?" left where a date was not known.',
        'Fill it or remove the line. clean_legacy_bodies.php strips this at the head '
        . 'of a body; one survived further in.'],

    'mid-sentence-double-space' => ['review', '~(?<![.!?:])  +(?=\S)~',
        'Two or more spaces that are not after a sentence stop, so not the typescript\'s '
        . 'sentence gap but a scan artefact: "Aug.  24, 1878".',
        'Collapse to one. Separated from the sentence gap below on purpose, because '
        . 'collapsing both is a typographic decision rather than a repair.'],

    'bare-url-in-prose' => ['review', '~(?<![">])\bhttps?://[^\s<>")]+~',
        'A URL written out in the prose rather than linked.',
        'Either link it or move it to a field that holds a link.'],

    /* -------------------------------------------------------------- intended */
    'image-token' => ['intended', '~\[image:\d+\]~',
        'The rendered image layer. _partials/prose.twig turns these into pictures.',
        'Leave alone. A pass that strips bracketed lines destroys the illustrations.'],

    'editor-bracket' => ['intended', '~\[sic[:\s][^\]]*\]~i',
        'The editor\'s own interpolation, "[sic: Reynolds subsequently says 1878]".',
        'Leave alone. This is the archive correcting its sources in public.'],

    'inline-markup' => ['intended', '~</?(em|strong|i|b|a)\b[^>]*>~i',
        'Inline emphasis and links inside a plain-text body, rendered by prose.twig.',
        'Leave alone. This is the layer that stopped emphasis from splitting sentences.'],

    'em-dash' => ['intended', "~\u{2014}~u",
        'Reynolds and Worden both write with em dashes, and the bibliography uses a '
        . 'double em dash for a repeated author.',
        'Leave alone. This is the corpus\'s voice, not a defect.'],

    'sentence-gap' => ['intended', '~(?<=[.!?])  +(?=[A-Z"\x27])~',
        'Two spaces after a sentence stop: the typescript convention the sources were '
        . 'typed in.',
        'A decision, not a repair. Keeping it is defensible and so is collapsing it, '
        . 'but it should be done once, deliberately, and not as a side effect.'],

    'footnote-marker' => ['intended', '~^\s*\[\s*\d{1,3}\s*\]\s*$~mu',
        'A footnote number on a line of its own.',
        'Leave alone until footnotes are modelled properly.'],

    /* --------------------------------------------------------------- outlier */
    'curly-quote' => ['outlier', "~[\u{2018}\u{2019}\u{201c}\u{201d}]~u",
        'A curly quote or apostrophe in a corpus that is otherwise entirely straight.',
        'Straighten it, or decide to curl the other 1,000 and do that once.'],

    'ellipsis-character' => ['outlier', "~\u{2026}~u",
        'U+2026 in a corpus that otherwise writes three full stops.',
        'Pick one. Whichever it is, one instance against two hundred is the odd one.'],

    'rights-line-separator' => ['outlier', '~SOCIETY[ ]?[\x{2022}\x{00AD}\x{007C}][ ]?RIGHTS RESERVED~u',
        'The rights line reads "SANTA CLARITA VALLEY HISTORICAL SOCIETY · RIGHTS '
        . 'RESERVED" on most records, and the one character slot between SOCIETY and '
        . 'RIGHTS held four different characters across 43 of them: a middle dot on 28, '
        . 'a bullet on 7, a soft hyphen on 6 and a vertical bar on 2.',
        'Normalise to the middle dot, the majority. Scoped to this line rather than to '
        . 'the bullet character, because a bullet is a legitimate separator in prose: '
        . 'the del Valle record uses one in a list and must not be touched. '
        . 'clean_text_mechanical.php does this.'],
];

/* ------------------------------------------------------------------ the read */

$found = [];      /* class => ['fields' => n, 'records' => [], 'occ' => n, 'eg' => []] */
$perRecord = [];  /* "section/slug" => [class => n] */
$fieldsRead = 0;
$charsRead = 0;
$recordsRead = 0;

$fragment = function (string $text, int $at, int $len): string {
    $from = max(0, $at - 46);
    $frag = mb_strcut($text, $from, 46 + $len + 46);
    return trim(preg_replace('~\s+~u', ' ', $frag));
};

foreach (Craft::$app->entries->getAllSections() as $section) {
    foreach (\craft\elements\Entry::find()->section($section->handle)->status(null)->limit(null)->all() as $e) {
        $recordsRead++;
        $handles = [];
        foreach ($e->getFieldLayout()->getCustomFields() as $f) { $handles[] = $f->handle; }
        $label = $section->handle . '/' . $e->slug;

        foreach ($FIELDS as $fh) {
            if (!in_array($fh, $handles, true)) { continue; }
            $v = $e->getFieldValue($fh);
            if (!is_string($v)) { continue; }
            if (trim($v) === '') { continue; }
            $fieldsRead++;
            $charsRead += strlen($v);

            foreach ($CLASSES as $name => [$verdict, $pattern, $what, $todo]) {
                $n = preg_match_all($pattern, $v, $m, PREG_OFFSET_CAPTURE);
                if (!$n) { continue; }

                if (!isset($found[$name])) {
                    $found[$name] = ['verdict' => $verdict, 'what' => $what, 'todo' => $todo,
                                     'occ' => 0, 'fields' => 0, 'records' => [], 'eg' => []];
                }
                $found[$name]['occ'] += $n;
                $found[$name]['fields']++;
                $found[$name]['records'][$label] = true;
                $perRecord[$label][$name] = ($perRecord[$label][$name] ?? 0) + $n;

                if (count($found[$name]['eg']) < 8) {
                    $hit = $m[0][0][0];
                    $found[$name]['eg'][] = [
                        'record' => $label, 'field' => $fh,
                        'text' => mb_substr($fragment($v, $m[0][0][1], strlen($hit)), 0, 130),
                    ];
                }
            }
        }
    }
}

/* ---------------------------------------------------------------- the report */

echo 'read ' . number_format($charsRead) . ' characters across ' . $fieldsRead
    . ' text fields on ' . $recordsRead . ' records' . PHP_EOL;
echo str_repeat('=', 92) . PHP_EOL;

$ORDER = ['mechanical', 'review', 'outlier', 'intended'];
$HEAD = [
    'mechanical' => 'MECHANICAL. One pass fixes all of these, on every record, with no judgement.',
    'review'     => 'REVIEW. Something is wrong and a person has to decide what. Found, never fixed.',
    'outlier'    => 'OUTLIER. One instance against a settled convention, which makes it a typo.',
    'intended'   => 'INTENDED. Present and correct. This is the list the cleanup pass must not touch.',
];

$totals = ['mechanical' => 0, 'review' => 0, 'outlier' => 0, 'intended' => 0];
foreach ($ORDER as $verdict) {
    $rows = array_filter($found, fn($c) => $c['verdict'] === $verdict);
    uasort($rows, fn($a, $b) => count($b['records']) <=> count($a['records']));
    echo PHP_EOL . $HEAD[$verdict] . PHP_EOL;
    echo str_repeat('-', 92) . PHP_EOL;
    if (!$rows) { echo '  nothing in this class' . PHP_EOL; continue; }
    foreach ($rows as $name => $c) {
        $totals[$verdict] += $c['occ'];
        printf("%-26s %4d records  %5d fields  %6d hits\n",
            $name, count($c['records']), $c['fields'], $c['occ']);
        echo '   ' . wordwrap($c['what'], 86, "\n   ") . PHP_EOL;
        echo '   -> ' . wordwrap($c['todo'], 86, "\n      ") . PHP_EOL;
        foreach (array_slice($c['eg'], 0, 3) as $eg) {
            echo '      ' . str_pad($eg['record'], 46) . str_pad($eg['field'], 16) . $eg['text'] . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

/* Classes that never fired are worth saying out loud: a census that only lists
   what it found cannot tell you what it looked for and did not find. */
$absent = array_values(array_diff(array_keys($CLASSES), array_keys($found)));
if ($absent) {
    echo str_repeat('-', 92) . PHP_EOL;
    echo 'looked for and not present: ' . implode(', ', $absent) . PHP_EOL;
}

/* -------------------------------------------------- the records worth opening */

$reviewClasses = array_keys(array_filter($CLASSES, fn($c) => $c[0] === 'review'));
$worst = [];
foreach ($perRecord as $label => $classes) {
    $n = 0;
    $which = [];
    foreach ($classes as $cls => $count) {
        if (in_array($cls, $reviewClasses, true)) { $n += $count; $which[] = $cls; }
    }
    if ($n) { $worst[$label] = ['n' => $n, 'classes' => $which]; }
}
uasort($worst, fn($a, $b) => $b['n'] <=> $a['n']);

echo PHP_EOL . str_repeat('=', 92) . PHP_EOL;
echo 'THE REVIEW QUEUE: ' . count($worst) . ' records carry something a person has to look at.' . PHP_EOL;
echo 'Worked in this order, the worst first, it is one sitting rather than six passes.' . PHP_EOL;
echo str_repeat('=', 92) . PHP_EOL;
$i = 0;
foreach ($worst as $label => $w) {
    printf("%-52s %3d  %s\n", $label, $w['n'], implode(', ', $w['classes']));
    if (++$i >= 25) { echo '   and ' . (count($worst) - 25) . ' more, all in the JSON.' . PHP_EOL; break; }
}

echo PHP_EOL . str_repeat('=', 92) . PHP_EOL;
printf("mechanical %d hits, review %d, outlier %d, intended %d (left alone)\n",
    $totals['mechanical'], $totals['review'], $totals['outlier'], $totals['intended']);
echo 'The mechanical total is what one pass removes without anyone reading anything.' . PHP_EOL;

/* --------------------------------------------------------------- the artefact */

$payload = ['meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/census_text_quality.php',
        'fields_scanned' => $FIELDS,
        'records_read' => $recordsRead,
        'text_fields_read' => $fieldsRead,
        'characters_read' => $charsRead,
        'totals' => $totals,
    ], 'classes' => [], 'reviewQueue' => []];
foreach ($found as $name => $c) {
    $payload['classes'][$name] = [
        'verdict' => $c['verdict'], 'what' => $c['what'], 'todo' => $c['todo'],
        'records' => count($c['records']), 'fields' => $c['fields'], 'hits' => $c['occ'],
        'recordList' => array_keys($c['records']), 'examples' => $c['eg'],
    ];
}
foreach ($worst as $label => $w) { $payload['reviewQueue'][$label] = $w; }
file_put_contents($OUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
echo PHP_EOL . 'wrote web/review/text-quality.json. Nothing in Craft was touched.' . PHP_EOL;
