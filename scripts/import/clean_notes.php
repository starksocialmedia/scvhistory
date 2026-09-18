/**
 * Cleans WordPress leftovers out of editor note fields and removes duplicates
 * where the same text was written to both the generic and the prefixed handle.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/clean_notes.php'))"
 */

$APPLY = true;

$pairs = [
    ['webmasterNoteTop', 'obitWebmasterNoteTop', 'personWebmasterNoteTop', 'mpWebmasterNoteTop'],
    ['webmasterNoteBottom', 'obitWebmasterNoteBottom', 'personWebmasterNoteBottom', 'mpWebmasterNoteBottom'],
];

$clean = function (string $s): string {
    if (trim($s) === '') { return ''; }
    if (str_contains($s, '&lt;') || str_contains($s, '&amp;')) {
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    $s = preg_replace('/<!--\s*\/?wp:[^>]*-->/', '', $s);
    $s = preg_replace('/\[caption[^\]]*\]/i', '', $s);
    $s = str_ireplace('[/caption]', '', $s);
    $s = preg_replace('#<img[^>]*>#i', '', $s);
    $s = preg_replace('#<a[^>]*href="https?://wordpress-\d+-\d+\.cloudwaysapps\.com[^"]*"[^>]*>(.*?)</a>#is', '$1', $s);
    $s = preg_replace('#<(p|div|h[1-6])\b[^>]*>#i', '', $s);
    $s = preg_replace('#</(p|div|h[1-6])>#i', "\n\n", $s);
    $s = preg_replace('#<br\s*/?>#i', "\n", $s);
    $s = strip_tags($s, '<em><i><strong><b><a>');
    $s = preg_replace('/\s+(class|style|width|height|longdesc|alt|referrer)="[^"]*"/i', '', $s);
    $s = str_replace("\xc2\xa0", ' ', $s);
    $s = preg_replace('/[ \t]+/', ' ', $s);
    $s = preg_replace('/ *\n */', "\n", $s);
    $s = preg_replace('/\n{3,}/', "\n\n", $s);
    return trim($s);
};

$key = fn(string $s) => preg_replace('/[^a-z0-9]/', '', strtolower(strip_tags($s)));

$elements = Craft::$app->getElements();
$sections = [];
foreach (Craft::$app->getEntries()->getAllSections() as $s) { $sections[] = $s->handle; }

$cleaned = 0; $deduped = 0;

foreach ($sections as $handle) {
    foreach (\craft\elements\Entry::find()->section($handle)->status(null)->all() as $entry) {
        $layout = [];
        foreach ($entry->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }
        $sets = [];

        foreach ($pairs as $group) {
            $present = array_values(array_intersect($group, $layout));
            $vals = [];
            foreach ($present as $h) {
                $raw = (string)$entry->getFieldValue($h);
                if (trim($raw) === '') { continue; }
                $vals[$h] = $clean($raw);
            }
            if (!count($vals)) { continue; }

            foreach ($vals as $h => $new) {
                if ($new !== (string)$entry->getFieldValue($h)) { $sets[$h] = $new; $cleaned++; }
            }

            if (count($vals) > 1) {
                $seen = [];
                foreach ($vals as $h => $new) {
                    $k = $key($new);
                    if ($k === '') { continue; }
                    if (isset($seen[$k])) { $sets[$h] = ''; $deduped++; }
                    else { $seen[$k] = $h; }
                }
            }
        }

        if (!count($sets)) { continue; }
        echo str_pad($entry->slug, 40) . implode(', ', array_map(fn($h) => $h . ($sets[$h] === '' ? ' (emptied)' : ' (cleaned)'), array_keys($sets))) . PHP_EOL;
        foreach ($sets as $h => $val) {
            if ($val !== '') { echo '    ' . mb_substr($val, 0, 100) . PHP_EOL; }
        }
        if ($APPLY) {
            foreach ($sets as $h => $val) { $entry->setFieldValue($h, $val); }
            if (!$elements->saveElement($entry)) { echo '  SAVE FAILED ' . json_encode($entry->getErrors()) . PHP_EOL; }
        }
    }
}

echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . ': ' . $cleaned . ' cleaned, ' . $deduped . ' duplicate notes emptied' . PHP_EOL;
