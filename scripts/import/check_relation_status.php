<?php
/**
 * A relation rewritten from its own current value keeps unpublished targets
 * (silent-faults audit, 5 October 2026, finding 28).
 *
 * In Craft 5 a relation field's value is a query that defaults to enabled
 * elements only. A script that reads it with ->ids() or ->all() and writes the
 * result back with something added or taken away silently unlinks every
 * disabled target: the fourteen fallen officers were disabled for a day, and the
 * next batch will be too. The read must be ->status(null)->ids().
 *
 * Flags a read of a relation field (->handle->ids(), ->handle->all(),
 * getFieldValue(...)->ids() or ->all()) without ->status(null) when it feeds a
 * write:
 *   - the read is inside a setFieldValue or setFieldValues call;
 *   - the read sits inside array_merge, array_diff or array_unique as the value
 *     of a key ('handle' => ..., or $vals['handle'] = ...), with a
 *     setFieldValue(s) within ten lines either side (a values array built over
 *     several lines);
 *   - the read is assigned to a variable ($ids = ..., or $ids[] = ... in a
 *     foreach over it), and a setFieldValue(s) call on that line or the next
 *     forty passes that variable or one assigned from it.
 * A heuristic: it does not flag read-backs, displays or emptiness checks, and it
 * can miss a value passed through two variables or written far below the read.
 *
 * Read-only. Not yet called by check_render.php. Runnable alone:
 *   php scripts/import/check_relation_status.php
 *   ddev craft exec "eval(substr(file_get_contents('scripts/import/check_relation_status.php'), 5))"
 */
$root = class_exists('Craft', false) ? \Craft::getAlias('@root') : dirname(__DIR__, 2);
$read = '~(->[A-Za-z_]\w*|getFieldValue\([^()]*\))->(ids|all)\(\)~';
$write = '~->setFieldValues?\(~';
$fails = [];
foreach (glob("$root/scripts/import/*.php") as $path) {
    $file = basename($path);
    if ($file === 'check_relation_status.php') { continue; }
    $lines = preg_split('~\R~', (string)file_get_contents($path));
    foreach ($lines as $i => $line) {
        $t = ltrim($line);
        if ($t === '' || str_starts_with($t, '*') || str_starts_with($t, '//') || str_starts_with($t, '/*') || !preg_match($read, $line)) { continue; }
        $why = null;
        if (preg_match('~->setFieldValues?\([^;]*' . substr($read, 1, -1) . '~', $line)) { $why = 'read inside a write'; }
        if (!$why && preg_match('~(=>|\]\s*=)[^;]*array_(merge|diff|unique)\([^;]*' . substr($read, 1, -1) . '~', $line)) {
            foreach (array_slice($lines, max(0, $i - 10), 21) as $near) { if (preg_match($write, $near)) { $why = 'read inside a merge that is written'; break; } }
        }
        $var = null;
        if (preg_match('~\$(\w+)\s*=(?![=>])[^;]*' . substr($read, 1, -1) . '~', $line, $m)) { $var = $m[1]; }
        elseif (preg_match('~' . substr($read, 1, -1) . '[^;]*\$(\w+)\[\]\s*=~', $line, $m)) { $var = $m[3]; }
        elseif (preg_match('~foreach\s*\(' . substr($read, 1, -1) . '~', $line)) {
            foreach (array_slice($lines, $i + 1, 3) as $next) { if (preg_match('~\$(\w+)\[\]\s*=~', $next, $m)) { $var = $m[1]; break; } }
        }
        if (!$why && $var) {
            /* The variable, and anything assigned from it (one step: $merged = array_merge($current, ...)). */
            $after = array_merge([substr($line, (int)strpos($line, '$' . $var))], array_slice($lines, $i + 1, 40));
            $vars = [$var];
            foreach ($after as $next) {
                preg_match_all('~\$(\w+)(?:\[[^\]]*\])*\s*=(?![=>])[^;]*\$' . preg_quote($var, '~') . '\b~', $next, $m2);
                foreach ($m2[1] as $w) { if ($w !== $var) { $vars[] = $w; } }
            }
            foreach (array_unique($vars) as $v) {
                $uses = '~->setFieldValues?\([^;]*\$' . preg_quote($v, '~') . '\b~';
                foreach ($after as $next) { if (preg_match($uses, $next)) { $why = "read into \$$var" . ($v === $var ? '' : " (then \$$v)") . ', which is written'; break 2; } }
            }
        }
        if ($why) { $fails[] = "$file:" . ($i + 1) . " $why, without ->status(null): " . mb_substr(trim($line), 0, 140); }
    }
}
echo ($fails ? 'RELATION ' . implode(PHP_EOL . 'RELATION ', $fails) : 'Relation rewrites: every read that feeds a write keeps unpublished targets') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails];
