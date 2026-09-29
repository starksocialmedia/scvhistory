/**
 * Say the corrections plainly. Nathan, 29 September 2026, after
 * build_california_star_oil_works.php: Scofield founding the company and
 * hiring Mentry, and Standard buying the California Star Oil Works in 1900,
 * are wrong on Wikipedia and right in the archive's sources, and the body
 * should say so plainly enough that a reader sees the archive knows better.
 *
 * Wikipedia is named in the prose as the one making the claim, which is who
 * says it; it is still never a footnote. Every correction rests on the
 * footnotes already on the record, re-pointed here where a sentence now leans
 * on a second note.
 *
 * Replaces whole paragraphs, each matched exactly against the text written on
 * 29 September; a paragraph edited since is reported and left alone.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/sharpen_csow_corrections.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$EDITS = [
    16039 => [
        [
            'Demetrius G. Scofield is often named as the company\'s founder and as the man who hired Mentry. The directors named in 1876 do not include him, and the earliest mention of him with the company found in the papers is at Ventura in May 1877; the 1963 National Park Service survey has him and F. B. Taylor coming into the business after it was formed. By January 1877 Taylor was its general manager.[3]',
            'Wikipedia and other popular accounts say that Demetrius G. Scofield founded the company and hired Mentry. The sources of the time say he did neither. Mentry was hired in July 1875 by the partners of the Star Oil Works, before the company existed.[1] The five directors who filed its articles in 1876 do not include Scofield.[2] The earliest mention of him with the company in the papers is at Ventura in May 1877, and the 1963 National Park Service survey has him and F. B. Taylor coming into the business after it was formed. By January 1877 Taylor was its general manager.[3]',
        ],
        [
            'In December 1900 Standard Oil bought the Pacific Coast Oil Company; the California Star Oil Works was not the company it bought.[13] Standard Oil Company (California) was incorporated in 1906. Scofield, by then long with the Pacific Coast company, was its vice president from 1906 and its president from December 1911.[14]',
            'In December 1900 Standard Oil bought the Pacific Coast Oil Company.[13] Wikipedia and other accounts say that Standard bought the California Star Oil Works. It did not: by then the Star company belonged to the Pacific Coast company, and the Pacific Coast company is what Standard bought.[12][13] Standard Oil Company (California) was incorporated in 1906. Popular accounts make Scofield its first president that year; he was its vice president from 1906, and became president in December 1911.[14]',
        ],
    ],
    'Demetrius G. Scofield' => [
        [
            'Later accounts, including the note on the eulogy\'s legacy page, call him the founder of the California Star Oil Works and the first president of Standard Oil of California. The company\'s directors of 1876 do not include him, and in 1906 he was its vice president.[6]',
            'Wikipedia and other later accounts, including the note on the eulogy\'s legacy page, say that he founded the California Star Oil Works, hired Mentry, and was the first president of Standard Oil of California. The sources of the time say otherwise. The company\'s directors of 1876 do not include him; Mentry was hired in July 1875, by the partners of the Star Oil Works, before the company existed; and in 1906 Scofield was vice president of Standard Oil of California, not its president.[6]',
        ],
    ],
];
$SCOFIELD_NOTE_6_ADD = ' ' . 'Charles W. Snell, National Park Service, 1963, https://npgallery.nps.gov/NRHP/GetAsset/NHLS/66000212_text: the Star Oil Works partners "employed C. A. Mentry ... in July 1875."';

$elements = Craft::$app->getElements();
$plan = []; $held = []; $done = [];
foreach ($EDITS as $key => $pairs) {
    $e = is_int($key) ? Entry::find()->id($key)->status(null)->one() : Entry::find()->section('persons')->title($key)->status(null)->one();
    if (!$e) { $held[] = "$key not found"; continue; }
    $body = (string)$e->body; $new = $body; $n = 0;
    foreach ($pairs as $i => [$old, $rep]) {
        if (str_contains($new, $rep)) { $done[] = "#{$e->id} paragraph " . ($i + 1) . ' already sharpened'; continue; }
        if (substr_count($new, $old) !== 1) { $held[] = "#{$e->id} {$e->title}: paragraph " . ($i + 1) . ' is not as written on 29 September, left alone'; continue; }
        $new = str_replace($old, $rep, $new); $n++;
        echo PHP_EOL . "#{$e->id} {$e->title}, paragraph " . ($i + 1) . ':' . PHP_EOL . '   NOW: ' . $rep . PHP_EOL;
    }
    $notes = null;
    if (!is_int($key) && $n) {
        $notes = $e->footnotes;
        foreach ($notes as &$r) {
            if ((string)($r['number'] ?? '') === '6' && !str_contains((string)$r['note'], 'employed C. A. Mentry')) { $r['note'] .= $SCOFIELD_NOTE_6_ADD; echo '   footnote [6] gains the 1963 survey on Mentry\'s hiring' . PHP_EOL; }
        }
        unset($r);
    }
    if ($n) { $plan[] = [$e->id, $new, $notes]; }
}
echo PHP_EOL . 'ALREADY DONE: ' . ($done ? implode('; ', $done) : 'none') . PHP_EOL . 'LEFT ALONE: ' . ($held ? implode('; ', $held) : 'none') . PHP_EOL;
echo 'SUMMARY: ' . count($plan) . ' record(s) to update.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$plan) { echo 'nothing to do; a second run is a no-op' . PHP_EOL; return; }

$short = [];
foreach ($plan as [$id, $new, $notes]) {
    $e = Entry::find()->id($id)->status(null)->one();
    $e->setFieldValue('body', $new);
    if ($notes !== null) { $e->setFieldValue('footnotes', $notes); }
    if (!$elements->saveElement($e)) { $short[] = "#$id save failed"; continue; }
    if (trim((string)Entry::find()->id($id)->status(null)->one()->body) !== trim($new)) { $short[] = "#$id body reads back different"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . ' record(s)') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('sharpen_csow_corrections.php', count($plan), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'CSOW and Scofield: corrections to Wikipedia stated plainly');
if ($short) { throw new \RuntimeException('sharpen_csow_corrections: ' . implode('; ', $short)); }
