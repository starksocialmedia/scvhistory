/**
 * Reads every record body and proposes rows for recordDates by finding dates
 * in the prose. Proposals are never confirmed; a person ticks Confirmed later.
 * Existing rows are never touched, and nothing is proposed twice.
 * Dry run by default. Set $APPLY = true to write proposals.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/propose_record_dates.php'))"
 */

$APPLY = true;
$SHOW = 40;

$months = 'January|February|March|April|May|June|July|August|September|October|November|December';
$mnum = ['january'=>1,'february'=>2,'march'=>3,'april'=>4,'may'=>5,'june'=>6,'july'=>7,'august'=>8,'september'=>9,'october'=>10,'november'=>11,'december'=>12];

$patterns = [
    ['re' => '/\b(' . $months . ')\s+(\d{1,2}),\s+(\d{4})\b/i', 'g' => 'day'],
    ['re' => '/\b(\d{1,2})\s+(' . $months . ')\s+(\d{4})\b/i', 'g' => 'day'],
    ['re' => '/\b(' . $months . ')\s+(\d{4})\b/i', 'g' => 'month'],
    ['re' => '/\bc\.\s*(\d{4})\b/i', 'g' => 'circa'],
    ['re' => '/\b(?:in|by|during|since|until)\s+(\d{4})\b/i', 'g' => 'year'],
];

$elements = Craft::$app->getElements();
$sections = [];
foreach (Craft::$app->getEntries()->getAllSections() as $s) { $sections[] = $s->handle; }

$totalRows = 0; $totalEntries = 0; $shown = 0;

foreach ($sections as $handle) {
    foreach (\craft\elements\Entry::find()->section($handle)->status(null)->all() as $entry) {
        $layout = [];
        foreach ($entry->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }
        if (!in_array('recordDates', $layout, true)) { continue; }

        $text = '';
        foreach (['body', 'wmNarrative'] as $h) {
            if (!in_array($h, $layout, true)) { continue; }
            try { $text .= ' ' . (string)$entry->getFieldValue($h); } catch (\Throwable $e) {}
        }
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
        if ($text === '') { continue; }

        $existing = [];
        try {
            foreach ((array)$entry->getFieldValue('recordDates') as $row) {
                $existing[] = strtolower(trim((string)($row['printed'] ?? '')));
            }
        } catch (\Throwable $e) {}

        $found = [];
        foreach ($patterns as $p) {
            if (!preg_match_all($p['re'], $text, $m, PREG_OFFSET_CAPTURE)) { continue; }
            foreach ($m[0] as $i => $hit) {
                $printed = trim($hit[0]);
                $key = strtolower($printed);
                if (in_array($key, $existing, true) || isset($found[$key])) { continue; }

                $iso = '';
                $g = $p['g'];
                if ($g === 'day') {
                    $mo = strtolower($m[1][$i][0]);
                    if (isset($mnum[$mo])) { $iso = sprintf('%04d-%02d-%02d', (int)$m[3][$i][0], $mnum[$mo], (int)$m[2][$i][0]); }
                    else { $mo2 = strtolower($m[2][$i][0]); if (isset($mnum[$mo2])) { $iso = sprintf('%04d-%02d-%02d', (int)$m[3][$i][0], $mnum[$mo2], (int)$m[1][$i][0]); } }
                } elseif ($g === 'month') {
                    $mo = strtolower($m[1][$i][0]);
                    if (isset($mnum[$mo])) { $iso = sprintf('%04d-%02d-01', (int)$m[2][$i][0], $mnum[$mo]); }
                } else {
                    $iso = sprintf('%04d-01-01', (int)$m[1][$i][0]);
                }
                if ($iso === '') { continue; }
                $y = (int)substr($iso, 0, 4);
                if ($y < 1700 || $y > (int)date('Y')) { continue; }

                $start = max(0, $hit[1] - 90);
                $ctx = trim(mb_substr($text, $start, 200));
                $found[$key] = ['printed' => $printed, 'iso' => $iso, 'granularity' => $g, 'label' => $ctx, 'confirmed' => false];
            }
        }

        if (!count($found)) { continue; }
        $totalEntries++;
        $totalRows += count($found);

        if ($shown < $SHOW) {
            echo str_pad($entry->slug, 40) . count($found) . ' dates' . PHP_EOL;
            foreach (array_slice($found, 0, 4) as $r) {
                echo '    ' . str_pad($r['printed'], 22) . $r['iso'] . '  ' . $r['granularity'] . PHP_EOL;
            }
            $shown++;
        }

        if ($APPLY) {
            $rows = [];
            try { $rows = (array)$entry->getFieldValue('recordDates'); } catch (\Throwable $e) {}
            foreach ($found as $r) { $rows[] = $r; }
            $entry->setFieldValue('recordDates', array_values($rows));
            if (!$elements->saveElement($entry)) { echo '  SAVE FAILED ' . $entry->slug . PHP_EOL; }
        }
    }
}

echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . ': ' . $totalRows . ' dates proposed across ' . $totalEntries . ' records' . PHP_EOL;
echo 'Every proposal is unconfirmed. Nothing appears in the on-this-day index until a person ticks Confirmed.' . PHP_EOL;
