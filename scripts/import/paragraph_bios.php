/**
 * Adds paragraph breaks to person bios that are a single block of text.
 * Breaks only at sentence boundaries where a new thought starts (a date,
 * "After", "Following", "Later", "By", "He died", etc.). No words change.
 * Dry run by default: prints each result. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/paragraph_bios.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$MIN_LEN = 700;
$TARGET = 550;

$starters = '/^(In (the )?(early |late |mid-)?\d{4}|In (January|February|March|April|May|June|July|August|September|October|November|December)|By \d{4}|By the|After |Following |Later |During |Upon |When |Throughout |He died|She died|He was buried|She was buried|He is buried|She is buried|Acosta and|Elected |On (the )?(morning|night|afternoon) of|On (January|February|March|April|May|June|July|August|September|October|November|December)|The defining|Born )/';

$split = function (string $text) use ($starters, $TARGET): string {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    $sentences = preg_split('/(?<=[.!?]["\')\]]?)\s+(?=[A-Z"\'(])/', $text);
    $paras = [];
    $cur = '';
    foreach ($sentences as $s) {
        $s = trim($s);
        if ($s === '') { continue; }
        $startsNew = preg_match($starters, $s) === 1;
        if ($cur !== '' && (($startsNew && strlen($cur) > 220) || strlen($cur) > $TARGET * 1.6)) {
            $paras[] = $cur;
            $cur = $s;
        } else {
            $cur = $cur === '' ? $s : $cur . ' ' . $s;
        }
    }
    if ($cur !== '') { $paras[] = $cur; }
    return implode("\n\n", $paras);
};

$elements = Craft::$app->getElements();
$changed = 0;
foreach (\craft\elements\Entry::find()->section('persons')->status(null)->orderBy('title asc')->all() as $e) {
    $body = (string)$e->getFieldValue('body');
    if (strlen($body) < $MIN_LEN) { continue; }
    if (preg_match('/\n\s*\n/', $body)) { continue; }
    $new = $split($body);
    $n = count(explode("\n\n", $new));
    if ($n < 2) { echo str_pad($e->slug, 28) . 'no natural break found, left alone' . PHP_EOL; continue; }
    $changed++;
    echo '=== ' . $e->slug . ' -> ' . $n . ' paragraphs ===' . PHP_EOL;
    foreach (explode("\n\n", $new) as $i => $p) {
        echo '  [' . ($i + 1) . '] ' . mb_substr($p, 0, 90) . (mb_strlen($p) > 90 ? '…' : '') . PHP_EOL;
    }
    if ($APPLY) {
        $e->setFieldValue('body', $new);
        if (!$elements->saveElement($e)) { echo '  SAVE FAILED ' . json_encode($e->getErrors()) . PHP_EOL; }
    }
}
echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . ': ' . $changed . ' bios' . PHP_EOL;
