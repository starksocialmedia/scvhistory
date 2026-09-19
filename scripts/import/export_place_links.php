/**
 * Proposes relationships between places, from what the archive already holds.
 *
 * relatedPlaces exists on all fifteen places and is empty on all fifteen, so
 * the graph draws them as fifteen unconnected dots. That is geography rather
 * than extraction: the coordinates and the community terms are already here,
 * and they answer most of it without anybody reading a page.
 *
 * Three kinds of evidence, each recorded separately so the screen can show why:
 *
 *   proximity   the two sit within $NEAR_KM of each other, by haversine on the
 *               stored coordinates. Nearness is not a relationship on its own,
 *               which is why it is offered rather than applied.
 *   community   both belong to the same community term. Weak alone, since a
 *               community can be large, and useful beside anything else.
 *   mention     one body names the other, by title or by one of its aliases.
 *               This is the strongest of the three: somebody wrote it down.
 *
 * Distance is reported on every pair whatever proposed it, because "these two
 * are named together and are forty kilometres apart" is exactly the sort of
 * thing a reviewer should see. Beale's Cut and Lyons Station both name Fort
 * Tejon, and that is the stage road, not proximity.
 *
 * Read only. Writes web/review/place-links.json for the review screen and
 * touches nothing in Craft.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_place_links.php'))"
 */

$NEAR_KM = 2.0;
$out = \Craft::getAlias('@webroot') . '/review/place-links.json';

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

$haversine = function (float $lat1, float $lng1, float $lat2, float $lng2): float {
    $R = 6371.0088;
    $p1 = deg2rad($lat1); $p2 = deg2rad($lat2);
    $dp = $p2 - $p1; $dl = deg2rad($lng2 - $lng1);
    $h = sin($dp / 2) ** 2 + cos($p1) * cos($p2) * sin($dl / 2) ** 2;
    return 2 * $R * asin(min(1.0, sqrt($h)));
};

$aliasesOf = function (\craft\elements\Entry $e) use ($hasField): array {
    if (!$hasField($e, 'placeAliases')) { return []; }
    $out = [];
    foreach (preg_split('~\r\n|\n|\r|,~', (string)$e->placeAliases) as $a) {
        $a = trim($a);
        if ($a !== '') { $out[] = $a; }
    }
    return $out;
};

/* ---------------------------------------------------------------- gather */

$places = [];
foreach (\craft\elements\Entry::find()->section('places')->status(null)->orderBy('title asc')->limit(null)->all() as $e) {
    $community = $hasField($e, 'neighborhood') ? ($e->neighborhood->one() ?? null) : null;
    $existing = $hasField($e, 'relatedPlaces') ? $e->relatedPlaces->ids() : [];
    $lat = $hasField($e, 'placeLat') ? trim((string)$e->placeLat) : '';
    $lng = $hasField($e, 'placeLng') ? trim((string)$e->placeLng) : '';
    $places[] = [
        'id' => $e->id,
        'title' => (string)$e->title,
        'url' => (string)$e->url,
        'cp' => $e->getCpEditUrl(),
        'lat' => $lat === '' ? null : (float)$lat,
        'lng' => $lng === '' ? null : (float)$lng,
        'community' => $community ? (string)$community->title : null,
        'communityId' => $community ? $community->id : null,
        'aliases' => $aliasesOf($e),
        'body' => $hasField($e, 'body') ? (string)$e->body : '',
        'existing' => $existing,
    ];
}

echo 'places: ' . count($places) . PHP_EOL;
$noCoords = array_values(array_filter($places, fn($p) => $p['lat'] === null || $p['lng'] === null));
$noCommunity = array_values(array_filter($places, fn($p) => $p['community'] === null));
echo 'without coordinates: ' . count($noCoords) . ', without a community: ' . count($noCommunity) . PHP_EOL;
$alreadyLinked = 0;
foreach ($places as $p) { $alreadyLinked += count($p['existing']); }
echo 'relatedPlaces relations already recorded: ' . $alreadyLinked . PHP_EOL . PHP_EOL;

/* ---------------------------------------------------------------- propose */

$pairs = [];
$n = count($places);
for ($i = 0; $i < $n; $i++) {
    for ($j = $i + 1; $j < $n; $j++) {
        $a = $places[$i]; $b = $places[$j];

        $km = null;
        if ($a['lat'] !== null && $a['lng'] !== null && $b['lat'] !== null && $b['lng'] !== null) {
            $km = round($haversine($a['lat'], $a['lng'], $b['lat'], $b['lng']), 2);
        }

        $evidence = [];

        if ($km !== null && $km <= $NEAR_KM) {
            $evidence[] = ['kind' => 'proximity', 'detail' => $km . ' km apart'];
        }

        if ($a['communityId'] && $a['communityId'] === $b['communityId']) {
            $evidence[] = ['kind' => 'community', 'detail' => 'both in ' . $a['community']];
        }

        /* One body naming the other, by title or alias. A short alias is not
           used: "Tejon" would match half the valley. */
        foreach ([[$a, $b], [$b, $a]] as [$from, $to]) {
            $names = array_merge([$to['title']], $to['aliases']);
            $w = preg_split('~\s+~', $to['title']);
            if (count($w) > 2) { $names[] = implode(' ', array_slice($w, 0, 2)); }
            foreach (array_unique($names) as $name) {
                if (mb_strlen($name) < 5) { continue; }
                if (mb_stripos($from['body'], $name) === false) { continue; }
                $evidence[] = [
                    'kind' => 'mention',
                    'detail' => $from['title'] . ' names ' . $to['title'] . ' as "' . $name . '"',
                    'sentence' => sentenceAround($from['body'], $name),
                ];
                break 1;
            }
        }

        if (!$evidence) { continue; }

        $already = in_array($b['id'], $a['existing'], true) || in_array($a['id'], $b['existing'], true);

        $pairs[] = [
            'a' => ['id' => $a['id'], 'title' => $a['title'], 'url' => $a['url'], 'cp' => $a['cp'], 'community' => $a['community']],
            'b' => ['id' => $b['id'], 'title' => $b['title'], 'url' => $b['url'], 'cp' => $b['cp'], 'community' => $b['community']],
            'km' => $km,
            'evidence' => $evidence,
            'kinds' => array_values(array_unique(array_column($evidence, 'kind'))),
            'strength' => count(array_unique(array_column($evidence, 'kind'))),
            'alreadyRelated' => $already,
        ];
    }
}

/* A mention is worth more than nearness, and nearness more than a shared
   community, so the reviewer meets the best-evidenced pairs first. */
$rank = ['mention' => 4, 'proximity' => 2, 'community' => 1];
usort($pairs, function ($x, $y) use ($rank) {
    $sx = 0; foreach ($x['kinds'] as $k) { $sx += $rank[$k] ?? 0; }
    $sy = 0; foreach ($y['kinds'] as $k) { $sy += $rank[$k] ?? 0; }
    return [$sy, $y['strength'], -($y['km'] ?? 9999)] <=> [$sx, $x['strength'], -($x['km'] ?? 9999)];
});

/* ---------------------------------------------------------------- report */

$byKind = [];
foreach ($pairs as $p) { foreach ($p['kinds'] as $k) { $byKind[$k] = ($byKind[$k] ?? 0) + 1; } }
arsort($byKind);

echo 'pairs proposed: ' . count($pairs) . ' of ' . ($n * ($n - 1) / 2) . ' possible' . PHP_EOL;
foreach ($byKind as $k => $c) { echo '  ' . str_pad($k, 12) . $c . PHP_EOL; }
echo PHP_EOL;
foreach ($pairs as $p) {
    echo str_pad(implode('+', $p['kinds']), 26)
        . str_pad($p['a']['title'], 34) . str_pad($p['b']['title'], 34)
        . ($p['km'] === null ? '' : $p['km'] . ' km')
        . ($p['alreadyRelated'] ? '  ALREADY RELATED' : '') . PHP_EOL;
    foreach ($p['evidence'] as $e) {
        if (!empty($e['sentence'])) { echo '      "' . mb_substr($e['sentence'], 0, 130) . '"' . PHP_EOL; }
    }
}

file_put_contents($out, json_encode([
    'meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/export_place_links.php',
        'nearKm' => $NEAR_KM,
        'places' => count($places),
        'pairs' => count($pairs),
        'byKind' => $byKind,
    ],
    'places' => array_map(fn($p) => [
        'id' => $p['id'], 'title' => $p['title'], 'url' => $p['url'],
        'community' => $p['community'], 'aliases' => $p['aliases'],
        'lat' => $p['lat'], 'lng' => $p['lng'], 'related' => count($p['existing']),
    ], $places),
    'pairs' => $pairs,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo PHP_EOL . 'wrote web/review/place-links.json' . PHP_EOL;
echo 'Nothing in Craft was changed. Decide at /review/place-links.html' . PHP_EOL;

function sentenceAround(string $body, string $needle): string
{
    $flat = preg_replace('~\s+~u', ' ', $body);
    $at = mb_stripos($flat, $needle);
    if ($at === false) { return ''; }
    $bytes = strpos($flat, mb_substr($flat, $at, mb_strlen($needle)));
    if ($bytes === false) { $bytes = 0; }
    $before = substr($flat, 0, $bytes);
    $start = 0;
    foreach (['. ', '! ', '? '] as $mark) {
        $e = strrpos($before, $mark);
        if ($e !== false && $e + 2 > $start) { $start = $e + 2; }
    }
    $tail = substr($flat, $start, 320);
    $cut = preg_split('~(?<=[.!?])\s~u', $tail);
    return trim($cut[0] ?? $tail);
}
