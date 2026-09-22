/**
 * Quality pass 3 of 5: drop capitals.
 *
 * The legacy pages set the first letter of a column as a drop cap in its own
 * element, and the extraction kept it as a line of its own:
 *
 *     R
 *
 *     [lines]
 *     emember Angel's Flight? Red Cars?
 *
 * This puts the letter back on its word. The blank line and a [lines] marker
 * between them are kept where they are; only the letter moves. Only in the
 * first six prose lines.
 *
 * How the letter joins:
 *
 *   next line starts lowercase    a fragment of the letter's word: R + emember.
 *                                 A and I can stand alone, so for them the
 *                                 archive's vocabulary decides: "I" over "have
 *                                 a dream" keeps its space, "I" over "f
 *                                 British" becomes "If", and where both read
 *                                 (A + part) it is listed.
 *   next line starts in capitals  joined only when the result is an
 *                                 all-capitals word the archive uses: N + OTES.
 *   anything else                 listed for a person.
 *
 * Two earlier versions got this wrong, and the cases are why the rule is shaped
 * this way: joining on any capital gave "TA 10x glass", and joining on the
 * vocabulary alone gave "TOne" and "I Red Book editor". A capital
 * continuation starts a word of its own and is not evidence of a drop cap.
 *
 * Dry run by default, with before and after counts from the quality report.
 * The report has no drop-cap column, so the pass prints its own count.
 * Run:   ddev craft exec "eval(file_get_contents('scripts/import/quality_drop_caps.php'))"
 * Apply: ddev craft exec '$QUALITY_APPLY = true; eval(file_get_contents("scripts/import/quality_drop_caps.php"));'
 */

$APPLY = false;
if (!empty($QUALITY_APPLY)) { $APPLY = true; echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* The vocabulary: lowercased words across every article body, with counts.
   Only words in the middle of a line, after a space. A split drop cap leaves
   its fragment at the start of a line, "merica" and "n" among them, and
   counting those would make the fragments look like words. */
$VOCAB = [];
foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->each() as $x) {
    if (preg_match_all("~(?<=[ \t])\p{L}[\p{L}'\x{2019}]*~u", mb_strtolower((string)$x->getFieldValue('body')), $m)) {
        foreach ($m[0] as $w) { $VOCAB[$w] = ($VOCAB[$w] ?? 0) + 1; }
    }
}
echo 'vocabulary: ' . count($VOCAB) . ' words' . PHP_EOL;

$run = require \Craft::getAlias('@root') . '/scripts/import/_quality_pass.php';

$run([
    'script' => 'quality_drop_caps.php',
    'label' => 'Drop capitals',
    'apply' => $APPLY,
    'classes' => [],
    'propose' => function (\craft\elements\Entry $e, int $cid, array &$listed) use ($qpRemoveLines, $VOCAB): ?array {
        $body = (string)$e->getFieldValue('body');
        $L = explode("\n", $body);
        $idx = [];
        foreach ($L as $i => $l) { if (trim($l) !== '' && !preg_match('~^\[/?(lines|table)\]$~', trim($l))) { $idx[] = $i; } }
        foreach (array_slice($idx, 0, 6) as $k => $i) {
            $letter = trim($L[$i]);
            if (!preg_match('~^[A-Z]$~', $letter)) { continue; }
            $j = $idx[$k + 1] ?? null;
            if ($j === null) { continue; }
            $next = ltrim($L[$j]);
            if (!preg_match("~^(\p{L}[\p{L}'\x{2019}]*)~u", $next, $t)) {
                $listed[] = 'single capital "' . $letter . '" over "' . mb_substr($next, 0, 40) . '": no word follows, kept';
                return null;
            }
            $frag = mb_strtolower($t[1]);
            $joined = mb_strtolower($letter) . $frag;
            $isWord = fn(string $w): bool => ($VOCAB[$w] ?? 0) >= 2;
            /* A fragment that could stand as a word after A or I: two letters or
               more before any apostrophe. "n", "t" and "t's" are in the
               vocabulary through mojibake ("don\u{FFFD}t") and are not words. */
            $standsAlone = fn(string $w): bool => (bool)preg_match('~^\p{L}{2,}~u', $w) && $isWord($w);
            $new = null; $why = '';
            if (preg_match('~^\p{Ll}~u', $next)) {
                /* A lowercase continuation is a word fragment: the letter can't
                   stand alone, except A and I. */
                if (!in_array($letter, ['A', 'I'], true)) { $new = $letter . $next; }
                elseif ($isWord($joined) && $standsAlone($frag)) { $why = '"' . $letter . $frag . '" and "' . $letter . ' ' . $frag . '" both read'; }
                elseif ($standsAlone($frag)) { $new = $letter . ' ' . $next; }
                else { $new = $letter . $next; }
            } elseif (preg_match('~^\p{Lu}{2,}\b~u', $t[1]) && $isWord($joined)) {
                /* An all-capitals word: N + OTES. */
                $new = $letter . $next;
            } else {
                $why = 'the next line starts a word of its own';
            }
            if ($new === null) {
                $listed[] = 'single capital "' . $letter . '" over "' . mb_substr($next, 0, 40) . '": ' . $why . ', kept';
                return null;
            }
            $L[$j] = $new;
            return ['set' => ['body' => $qpRemoveLines(implode("\n", $L), [$i => true])],
                    'show' => ['- ' . $letter, '- ' . mb_substr($next, 0, 70), '+ ' . mb_substr($new, 0, 72)]];
        }
        return null;
    },
]);
