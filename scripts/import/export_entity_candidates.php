/**
 * Exports every Person, Place and Organization record plus the candidate
 * duplicate pairs within each section, to web/review/entities.json for the
 * reconciliation screen. Read only.
 *
 * It never merges anything and never writes to the database. Every pair is a
 * proposal for a person to accept or reject at web/review/entities.html.
 *
 * Pairs are flagged three ways, within a section only:
 *   normalised   the titles match once punctuation, accents, honorifics and
 *                a leading "the" are removed
 *   initials     same surname, and one side's initials expand to the other's
 *                given names, so "H.M. Newhall" meets "Henry Mayo Newhall"
 *   surname      same surname, different given names
 *   substring    one title appears inside the other at a word boundary, so
 *                "Newhall" meets "Henry Mayo Newhall"
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_entity_candidates.php'))"
 */

// Alias field per section. Persons deliberately map to null: the type has no
// alias list, only fullName, which is the canonical name rather than a set of
// other names. apply_entity_merges.php uses the same map, so the screen never
// promises to record a title the apply step cannot store.
$SECTIONS = [
    'persons'       => null,
    'places'        => 'placeAliases',
    'organizations' => 'orgAliases',
];

$HONORIFICS = ['mr','mrs','ms','miss','dr','rev','fr','father','mother','sir','dame','don','dona',
               'sr','jr','ii','iii','iv','st','saint','gen','general','col','colonel','capt','captain',
               'lt','lieutenant','sgt','sergeant','pvt','private','maj','major','hon','prof','professor'];

$ACCENTS = ['á'=>'a','à'=>'a','ä'=>'a','â'=>'a','ã'=>'a','å'=>'a','é'=>'e','è'=>'e','ë'=>'e','ê'=>'e',
            'í'=>'i','ì'=>'i','ï'=>'i','î'=>'i','ó'=>'o','ò'=>'o','ö'=>'o','ô'=>'o','õ'=>'o',
            'ú'=>'u','ù'=>'u','ü'=>'u','û'=>'u','ñ'=>'n','ç'=>'c','ý'=>'y',
            'Á'=>'a','À'=>'a','Ä'=>'a','Â'=>'a','É'=>'e','È'=>'e','Ë'=>'e','Ê'=>'e',
            'Í'=>'i','Ì'=>'i','Ï'=>'i','Î'=>'i','Ó'=>'o','Ò'=>'o','Ö'=>'o','Ô'=>'o',
            'Ú'=>'u','Ù'=>'u','Ü'=>'u','Û'=>'u','Ñ'=>'n','Ç'=>'c'];

$norm = function (string $s) use ($HONORIFICS, $ACCENTS) {
    $s = strtr($s, $ACCENTS);
    $s = mb_strtolower($s);
    $s = str_replace(['&'], [' and '], $s);
    $s = preg_replace('/[^a-z0-9\s]/', ' ', $s);
    $s = preg_replace('/\s+/', ' ', trim($s));
    $words = array_values(array_filter(explode(' ', $s), function ($w) use ($HONORIFICS) {
        return $w !== '' && $w !== 'the' && !in_array($w, $HONORIFICS, true);
    }));
    return $words;
};

/* ── gather the records ───────────────────────────────────────── */

$records = [];

foreach ($SECTIONS as $handle => $aliasHandle) {
    foreach (\craft\elements\Entry::find()->section($handle)->status(null)->all() as $entry) {
        $layout = [];
        foreach ($entry->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }

        $alias = '';
        if ($aliasHandle && in_array($aliasHandle, $layout, true)) {
            try { $alias = trim((string)$entry->getFieldValue($aliasHandle)); } catch (\Throwable $e) { $alias = ''; }
        }

        // fullName is shown as a fact of its own, and only when it says
        // something the title does not
        $fullName = '';
        if (in_array('fullName', $layout, true)) {
            try { $fullName = trim((string)$entry->getFieldValue('fullName')); } catch (\Throwable $e) { $fullName = ''; }
        }

        // how many things point at this record
        $mentions = (new \craft\db\Query())
            ->from('{{%relations}}')
            ->where(['targetId' => $entry->id])
            ->count();

        $title = (string)($entry->title ?: $entry->slug);
        $words = $norm($title);

        $records[] = [
            'id'       => $entry->id,
            'section'  => $handle,
            'sectionName' => $entry->section->name,
            'title'    => $title,
            'slug'     => $entry->slug,
            'aliasField' => $aliasHandle && in_array($aliasHandle, $layout, true) ? $aliasHandle : null,
            'alias'    => $alias,
            'fullName' => ($fullName !== '' && $fullName !== $title) ? $fullName : '',
            'mentions' => (int)$mentions,
            'url'      => $entry->getUrl(),
            '_words'   => $words,
            '_norm'    => implode(' ', $words),
        ];
    }
}

/* ── pair them up ─────────────────────────────────────────────── */

$candidates = [];
$seen = [];

$isInitialsOf = function (array $short, array $long) {
    // "h m" against "henry mayo": every short token is one letter and matches
    if (count($short) === 0 || count($short) !== count($long)) { return false; }
    $any = false;
    foreach ($short as $i => $tok) {
        if (mb_strlen($tok) === 1) {
            if ($tok !== mb_substr($long[$i], 0, 1)) { return false; }
            $any = true;
        } elseif ($tok !== $long[$i]) {
            return false;
        }
    }
    return $any;
};

for ($i = 0; $i < count($records); $i++) {
    for ($j = $i + 1; $j < count($records); $j++) {
        $a = $records[$i]; $b = $records[$j];
        if ($a['section'] !== $b['section']) { continue; }
        if ($a['id'] === $b['id']) { continue; }

        $aw = $a['_words']; $bw = $b['_words'];
        if (!$aw || !$bw) { continue; }

        $reason = null; $confidence = null;

        if ($a['_norm'] === $b['_norm']) {
            $reason = 'normalised forms match';
            $confidence = 'high';
        } else {
            $aSur = end($aw); $bSur = end($bw);
            $shortW = count($aw) <= count($bw) ? $aw : $bw;
            $longW  = count($aw) <= count($bw) ? $bw : $aw;

            if ($aSur === $bSur && $isInitialsOf($shortW, $longW)) {
                $reason = 'same surname, initials expand to the given names';
                $confidence = 'high';
            } elseif (strpos(' ' . $b['_norm'] . ' ', ' ' . $a['_norm'] . ' ') !== false
                   || strpos(' ' . $a['_norm'] . ' ', ' ' . $b['_norm'] . ' ') !== false) {
                $reason = 'one title appears inside the other at a word boundary';
                $confidence = count($shortW) >= 2 ? 'medium' : 'low';
            } elseif ($aSur === $bSur && count($aw) > 1 && count($bw) > 1) {
                $reason = 'same surname, different given names';
                $confidence = 'medium';
            } elseif ($aSur === $bSur) {
                $reason = 'same surname';
                $confidence = 'low';
            }
        }

        if (!$reason) { continue; }

        $key = min($a['id'], $b['id']) . '-' . max($a['id'], $b['id']);
        if (isset($seen[$key])) { continue; }
        $seen[$key] = true;

        // the better attested record is offered as the survivor first
        $keepFirst = $a['mentions'] >= $b['mentions'];
        $first = $keepFirst ? $a : $b;
        $second = $keepFirst ? $b : $a;

        $candidates[] = [
            'aId' => $first['id'], 'aTitle' => $first['title'],
            'bId' => $second['id'], 'bTitle' => $second['title'],
            'section' => $a['section'],
            'sectionName' => $a['sectionName'],
            'reason' => $reason,
            'confidence' => $confidence,
        ];
    }
}

usort($candidates, function ($x, $y) {
    $rank = ['high' => 0, 'medium' => 1, 'low' => 2];
    return [$rank[$x['confidence']], $x['sectionName'], $x['aTitle']]
       <=> [$rank[$y['confidence']], $y['sectionName'], $y['aTitle']];
});

foreach ($records as &$r) { unset($r['_words'], $r['_norm']); }
unset($r);

$payload = [
    '_generated' => 'Built by scripts/import/export_entity_candidates.php. Read only; nothing is merged here.',
    '_built' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('c'),
    'records' => $records,
    'candidates' => $candidates,
];

$path = \Craft::getAlias('@webroot') . '/review';
if (!is_dir($path)) { mkdir($path, 0775, true); }
file_put_contents($path . '/entities.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$bySection = []; $byConf = [];
foreach ($records as $r) { $bySection[$r['sectionName']] = ($bySection[$r['sectionName']] ?? 0) + 1; }
foreach ($candidates as $c) {
    $byConf[$c['confidence']] = ($byConf[$c['confidence']] ?? 0) + 1;
}

echo 'records exported:' . PHP_EOL;
foreach ($bySection as $s => $n) { echo '  ' . str_pad($s, 16) . $n . PHP_EOL; }
echo 'candidate pairs: ' . count($candidates) . PHP_EOL;
foreach (['high', 'medium', 'low'] as $c) {
    if (isset($byConf[$c])) { echo '  ' . str_pad($c, 16) . $byConf[$c] . PHP_EOL; }
}
echo PHP_EOL;
foreach ($candidates as $c) {
    echo sprintf('  [%-6s] %-14s %s  <->  %s   (%s)', $c['confidence'], $c['section'], $c['aTitle'], $c['bTitle'], $c['reason']) . PHP_EOL;
}
echo PHP_EOL . 'wrote web/review/entities.json' . PHP_EOL;
echo 'open https://scvhistory.ddev.site/review/entities.html' . PHP_EOL;
