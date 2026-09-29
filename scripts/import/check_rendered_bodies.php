/**
 * READ ONLY. Does the page show what the record holds?
 *
 * A record can be applied, read back and verified, and still be invisible: for
 * a week every person body classed legacy-leon or editorial-2026 was in the
 * database and never on a page, because the publish test in
 * _partials/body-authorship.twig compared a dropdown object with strings and
 * never matched (fixed 29 September 2026). Read-back proves the data landed,
 * not that the page shows it. So this fetches the page for every record with a
 * body and looks for a run of words from that body in what the page renders.
 *
 *   SHOWN       the words are on the page
 *   WITHHELD    not on the page, and a rule says it should not be: a person
 *               body not classed to publish (wordpress-import-unsourced,
 *               mixed, empty). Counted, not failed.
 *   MISSING     not on the page and no rule explains it: a failure
 *   NO PAGE     the page does not answer 200
 *
 * $SAMPLE limits the big sections (articles, photographs) to that many
 * records each; everything else is checked in full. 0 checks everything.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/check_rendered_bodies.php'))"
 * or with $SAMPLE set first: ddev craft exec '$SAMPLE = 0; eval(file_get_contents("scripts/import/check_rendered_bodies.php"));'
 */

use craft\elements\Entry;

$SAMPLE = $SAMPLE ?? 60;
$authorship = fn($e) => ($e->getFieldLayout()->getFieldByHandle('bodyAuthorship') ? (string)($e->getFieldValue('bodyAuthorship')->value ?? '') : null);
$PUBLISHES = ['legacy-leon', 'editorial-2026'];
$words = function (string $t): string {
    /* The first run of eight plain words, clear of markup, footnote markers and
       the [lines] convention, which the prose partial transforms. */
    $t = preg_replace('~\[/?lines\]|\[\d+\]|\{[^}]*\}|<[^>]+>~', ' ', $t);
    $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5);
    foreach (preg_split('~(?<=[.!?])\s+|\n+~', $t) as $sent) {
        if (preg_match('~(?:\b[A-Za-z]{2,}\b[ ,]+){7}\b[A-Za-z]{2,}\b~', $sent, $m)) { return trim(preg_replace('~\s+~', ' ', $m[0])); }
    }
    return '';
};
$plain = fn(string $html) => preg_replace('~\s+~', ' ', html_entity_decode(strip_tags(preg_replace('~<(script|style)\b.*?</\1>~is', ' ', $html)), ENT_QUOTES | ENT_HTML5));

$tally = []; $fails = [];
foreach (['persons', 'organizations', 'places', 'groups', 'events', 'documents', 'obituaries', 'collections', 'warMemorials', 'articles', 'photographs'] as $sec) {
    $q = Entry::find()->section($sec)->orderBy('id asc');
    $all = $q->all();
    if ($SAMPLE && in_array($sec, ['articles', 'photographs'], true) && count($all) > $SAMPLE) {
        $step = count($all) / $SAMPLE; $pick = [];
        for ($i = 0; $i < $SAMPLE; $i++) { $pick[] = $all[(int)floor($i * $step)]; }
        $all = $pick;
    }
    foreach ($all as $e) {
        if (!$e->url || !$e->getFieldLayout()->getFieldByHandle('body')) { continue; }
        $w = $words((string)$e->getFieldValue('body'));
        if ($w === '') { continue; }
        $ch = curl_init($e->url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
        $html = (string)curl_exec($ch); $st = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
        if ($st !== 200) { $k = 'NO PAGE'; }
        elseif (str_contains(strtolower($plain($html)), strtolower($w))) { $k = 'SHOWN'; }
        else {
            $a = $authorship($e);
            $k = ($a !== null && !in_array($a, $PUBLISHES, true)) ? 'WITHHELD' : 'MISSING';
        }
        $tally[$sec][$k] = ($tally[$sec][$k] ?? 0) + 1;
        if ($k === 'MISSING' || $k === 'NO PAGE') { $fails[] = "$k  $sec #{$e->id} {$e->title}  " . ($k === 'NO PAGE' ? "status $st" : '"' . mb_substr($w, 0, 60) . '"'); }
    }
}
foreach ($tally as $sec => $t) { echo str_pad($sec, 14) . json_encode($t) . PHP_EOL; }
echo PHP_EOL . ($fails ? count($fails) . ' FAILURES' . PHP_EOL . implode(PHP_EOL, $fails) : 'every checked body that should show, shows') . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails, 'tally' => $tally];
