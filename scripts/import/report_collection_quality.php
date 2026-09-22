/**
 * The per-collection quality report, in the terminal. The same numbers as
 * /admin-quality, from the same class: modules/quality/QualityReport.php.
 *
 * Read only. There is no $APPLY because there is nothing to apply. It writes
 * nothing to Craft and no file.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/report_collection_quality.php'))"
 * Examples for one class: prefix with $QUALITY_EXAMPLES = 'nav';
 */

$r = (new \modules\quality\QualityReport())->build();
$short = ['layout' => 'layout', 'tags' => 'tags', 'nav' => 'nav', 'imgBroken' => 'img-brk',
          'imgMissing' => 'img-miss', 'undated' => 'undated', 'dateProse' => 'date-prs',
          'footnotes' => 'fnotes', 'noLinks' => 'no-p/p', 'noEra' => 'no-era', 'noThemes' => 'no-theme'];

echo 'Collection quality, built ' . $r['built'] . ', worst first' . PHP_EOL;
echo str_repeat('=', 160) . PHP_EOL;
printf('%-38s %6s %5s', 'collection', 'pieces', 'any');
foreach ($short as $s) { printf(' %9s', $s); }
echo PHP_EOL . str_repeat('-', 160) . PHP_EOL;
foreach ($r['rows'] as $row) {
    printf('%-38s %6d %5d', mb_substr(($row['frozen'] ? '* ' : '') . $row['slug'], 0, 38), $row['pieces'], $row['any']);
    foreach (array_keys($short) as $k) { printf(' %9s', $row['pieces'] ? $row['counts'][$k] : '-'); }
    echo PHP_EOL;
}
echo str_repeat('-', 160) . PHP_EOL;
printf('%-38s %6d %5d', 'all collections', $r['totals']['pieces'], $r['totals']['any']);
foreach (array_keys($short) as $k) { printf(' %9d', $r['totals'][$k]); }
echo PHP_EOL . PHP_EOL;
foreach ($r['notes'] as $n) { echo $n . PHP_EOL; }
echo '* frozen' . PHP_EOL;

if (!empty($QUALITY_EXAMPLES) && isset($r['classes'][$QUALITY_EXAMPLES])) {
    echo PHP_EOL . 'Examples, ' . $r['classes'][$QUALITY_EXAMPLES][0] . ':' . PHP_EOL;
    foreach ($r['rows'] as $row) {
        foreach ($row['examples'][$QUALITY_EXAMPLES] as $x) { printf("   %-20s #%-6d %s\n", $row['slug'], $x['id'], $x['title']); }
    }
}
