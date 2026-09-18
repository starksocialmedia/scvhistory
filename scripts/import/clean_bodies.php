/**
 * Cleans WordPress leftovers out of body text on every entry:
 * [caption] and other shortcodes, escaped HTML, Gutenberg comments, editor classes.
 * Keeps em, strong, and links. Paragraph breaks become blank lines for the prose partial.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/clean_bodies.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$FIELDS = ['body', 'authorBio', 'wmNarrative'];

$elements = Craft::$app->getElements();

$clean = function (string $s): string {
    if (trim($s) === '') { return $s; }

    if (str_contains($s, '&lt;') || str_contains($s, '&amp;')) {
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    $s = preg_replace('/<!--\s*\/?wp:[^>]*-->/', '', $s);

    $s = preg_replace('/\[caption[^\]]*\]/i', '', $s);
    $s = str_ireplace('[/caption]', '', $s);
    $s = preg_replace('/\[\/?(gallery|embed|audio|video|playlist|vc_[a-z_]+)[^\]]*\]/i', '', $s);

    $s = preg_replace('#<(p|div|h[1-6])\b[^>]*>#i', '', $s);
    $s = preg_replace('#</(p|div|h[1-6])>#i', "\n\n", $s);
    $s = preg_replace('#<br\s*/?>#i', "\n", $s);

    $s = preg_replace('#<img[^>]*>#i', '', $s);
    $s = preg_replace('#<(figure|figcaption|span|font)\b[^>]*>#i', '', $s);
    $s = preg_replace('#</(figure|figcaption|span|font)>#i', '', $s);

    $s = strip_tags($s, '<em><i><strong><b><a>');
    $s = preg_replace('/\s+class="[^"]*"/i', '', $s);
    $s = preg_replace('/\s+style="[^"]*"/i', '', $s);

    $s = str_replace("\xc2\xa0", ' ', $s);
    $s = preg_replace('/[ \t]+/', ' ', $s);
    $s = preg_replace('/ *\n */', "\n", $s);
    $s = preg_replace('/\n{3,}/', "\n\n", $s);

    return trim($s);
};

$sections = [];
foreach (Craft::$app->getEntries()->getAllSections() as $s) { $sections[] = $s->handle; }

$changed = 0; $looked = 0; $samples = [];

foreach ($sections as $handle) {
    foreach (\craft\elements\Entry::find()->section($handle)->status(null)->all() as $entry) {
        $sets = [];
        foreach ($FIELDS as $f) {
            try { $val = $entry->getFieldValue($f); } catch (\Throwable $e) { continue; }
            if (!is_string($val) || trim($val) === '') { continue; }
            $looked++;
            $new = $clean($val);
            if ($new !== $val) { $sets[$f] = $new; }
        }
        if (!count($sets)) { continue; }
        $changed++;
        if (count($samples) < 5) {
            $f = array_key_first($sets);
            $samples[] = $entry->slug . ' [' . $f . ']: ' . mb_substr($sets[$f], 0, 110);
        }
        if ($APPLY) {
            foreach ($sets as $f => $v) { $entry->setFieldValue($f, $v); }
            if (!$elements->saveElement($entry)) {
                echo 'SAVE FAILED ' . $entry->slug . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
            }
        }
    }
}

echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
echo 'fields inspected: ' . $looked . PHP_EOL;
echo 'entries to change: ' . $changed . PHP_EOL;
echo '--- samples ---' . PHP_EOL;
foreach ($samples as $s) { echo $s . PHP_EOL . PHP_EOL; }
