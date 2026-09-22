/**
 * Quality pass 1 of 5: nav junk.
 *
 * Removes legacy navigation from the head and tail of collection pieces, using
 * the matchers in _legacy_chrome_matchers.php (the four added on 22 September
 * and the older breadcrumb rule). Head is the first five non-blank lines, tail
 * the last three. Nothing in the middle of the prose is touched; the "Click
 * here" sentences found there are listed for a person instead.
 *
 * Dry run by default, with before and after counts from the quality report.
 * Run:   ddev craft exec "eval(file_get_contents('scripts/import/quality_nav_junk.php'))"
 * Apply: ddev craft exec '$QUALITY_APPLY = true; eval(file_get_contents("scripts/import/quality_nav_junk.php"));'
 */

$APPLY = false;
if (!empty($QUALITY_APPLY)) { $APPLY = true; echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

require \Craft::getAlias('@root') . '/scripts/import/_legacy_chrome_matchers.php';
$run = require \Craft::getAlias('@root') . '/scripts/import/_quality_pass.php';

$run([
    'script' => 'quality_nav_junk.php',
    'label' => 'Nav junk',
    'apply' => $APPLY,
    'classes' => ['nav'],
    'propose' => function (\craft\elements\Entry $e, int $cid, array &$listed) use ($isBreadcrumb, $stripNavBar, $isDisqus, $isClickHereNav, $qpRemoveLines): ?array {
        $body = (string)$e->getFieldValue('body');
        $L = explode("\n", $body);
        $idx = [];
        foreach ($L as $i => $l) { if (trim($l) !== '' && !preg_match('~^\[/?lines\]$~', trim($l))) { $idx[] = $i; } }
        $head = array_slice($idx, 0, 5); $tail = array_slice($idx, -3);

        $drop = []; $show = []; $replace = [];
        foreach ($idx as $i) {
            $l = trim($L[$i]);
            $edge = in_array($i, $head, true) || in_array($i, $tail, true);
            if (!$edge) {
                if (preg_match('~click here~i', $l)) { $listed[] = 'mid-prose link sentence kept: "' . mb_substr($l, 0, 80) . '"'; }
                continue;
            }
            if (in_array($i, $head, true) && ($isBreadcrumb($l) || $isClickHereNav($l))) { $drop[$i] = true; $show[] = '- ' . $l; continue; }
            if (in_array($i, $tail, true) && $isDisqus($l)) { $drop[$i] = true; $show[] = '- ' . $l; continue; }
            $s = $stripNavBar($l);
            if ($s !== $l) {
                if ($s === '') { $drop[$i] = true; $show[] = '- ' . $l; }
                else { $replace[$i] = $s; $show[] = '- ' . $l; $show[] = '+ ' . $s; }
            }
        }
        if (!$drop && !$replace) { return null; }
        foreach ($replace as $i => $s) { $L[$i] = $s; }
        return ['set' => ['body' => $qpRemoveLines(implode("\n", $L), $drop)], 'show' => $show];
    },
]);
