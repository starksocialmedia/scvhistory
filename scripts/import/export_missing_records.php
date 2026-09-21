/**
 * Builds web/review/records.json: the names the corpus uses that have no record
 * behind them, ranked by how many articles use them.
 *
 * This is the queue that matters. The relations screen asks, one article at a
 * time, whether this article should link to that name, and the answer is nearly
 * always "yes, once the record exists". Asking it 2,018 times is asking the
 * wrong question 2,017 times. "Santa Clara River appears in 55 articles and we
 * do not hold it" is one decision that creates one record and links 55 pieces.
 *
 * Read only. It writes one JSON file and never touches an entry.
 *
 * THE TYPE GUESS
 *
 * Every row carries a guessed type and the signals that produced it, because a
 * guess a reviewer cannot see is a guess they cannot overrule. The extraction
 * already assigned a kind; that is treated as one signal among several rather
 * than as the answer, since it is the thing most likely to be wrong in the
 * cases that matter. Where the guess and the extraction disagree the row says
 * so and the screen shows it.
 *
 * The signals are lexical and contextual:
 *   suffix      "Santa Clara River", "San Fernando Road", "Newhall Ranch" name
 *               their own type in their last word
 *   corporate   "Company", "Mint", "Railroad", "Department" the same way
 *   honorific   "Dr.", "Mrs.", "Capt." can only precede a person
 *   pronoun     a he/she/his/her within the sample sentence, near the name
 *   preposition "at the", "in the", "near" before the name reads as a place
 *
 * No signal is decisive on its own. "Lyon's Station" is a place by suffix and
 * "Hart Park" is a place by suffix, but "Hart" alone is a person and the park
 * is named after him, which is exactly the case where a reviewer needs to see
 * what the machine was thinking.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_missing_records.php'))"
 * Full corpus: prefix with $RECORDS_SUFFIX='-full';
 */

$REVIEW = \Craft::getAlias('@webroot') . '/review';
$SUFFIX = $RECORDS_SUFFIX ?? '';
$in     = $REVIEW . '/entities' . $SUFFIX . '.json';
$out    = $REVIEW . '/records' . $SUFFIX . '.json';

if (!file_exists($in)) {
    echo 'no ' . $in . '. Run export_entity_candidates.php first.' . PHP_EOL;
    return;
}
$src = json_decode(file_get_contents($in), true);

/* ------------------------------------------------------------ the signals */

$PLACE_TAIL = ['river','creek','canyon','road','street','avenue','boulevard','highway','trail','lane',
    'station','park','mountain','mountains','valley','pass','lake','springs','spring','mesa','hill','hills',
    'ranch','rancho','camp','mine','tunnel','bridge','cut','reservoir','dam','wash','flat','flats',
    'junction','crossing','grade','peak','ridge','arroyo','cienega','plaza','cemetery','depot'];

$ORG_TAIL = ['company','co','inc','incorporated','corporation','corp','department','railroad','railway',
    'mint','bank','church','school','district','association','society','club','board','commission',
    'agency','bureau','union','institute','hospital','university','college','foundation','council',
    'committee','museum','library','theatre','theater','hotel','saloon','store','works','mill','refinery',
    'oil','gas','water','electric','telephone','press','signal','gazette','times','news'];

$ORG_HEAD = ['southern','pacific','union','standard','california','united','american','national','first',
    'los','san','santa'];

$HONORIFIC = '~^(mr|mrs|ms|miss|dr|doctor|fr|father|capt|captain|col|colonel|gen|general|lt|lieutenant|rev|reverend|sgt|sergeant|maj|major|don|dona|senor|senora|sister|judge|gov|governor|prof|professor|sheriff|deputy|chief|mayor)\b~i';

$GIVEN = ['john','james','william','george','charles','henry','thomas','robert','edward','frank','joseph',
    'richard','samuel','david','andrew','albert','arthur','walter','harry','fred','louis','carl','jesse',
    'mary','anna','elizabeth','margaret','sarah','helen','ruth','alice','ethel','clara','rose','martha',
    'ygnacio','jose','juan','antonio','maria','pedro','francisco','manuel','ramon','miguel','sol','leon'];

$words = function (string $s): array {
    $s = mb_strtolower($s);
    $s = preg_replace('~[^a-z0-9 ]+~', ' ', $s);
    return preg_split('~\s+~', trim($s), -1, PREG_SPLIT_NO_EMPTY) ?: [];
};

/* Scores the three types and reports what fired. A signal that fires for two
   types adds to both; the reviewer sees the contest rather than the verdict. */
$guess = function (array $row) use ($words, $PLACE_TAIL, $ORG_TAIL, $ORG_HEAD, $HONORIFIC, $GIVEN): array {
    $name = (string)$row['name'];
    $w = $words($name);
    $score = ['person' => 0, 'place' => 0, 'organization' => 0];
    $fired = [];

    if ($w) {
        $tail = end($w);
        $head = $w[0];
        if (in_array($tail, $PLACE_TAIL, true)) {
            $score['place'] += 6; $fired[] = 'ends in "' . $tail . '", a place word';
        }
        if (in_array($tail, $ORG_TAIL, true)) {
            $score['organization'] += 6; $fired[] = 'ends in "' . $tail . '", an organisation word';
        }
        if (in_array($head, $ORG_HEAD, true) && count($w) > 1 && !in_array($tail, $PLACE_TAIL, true)) {
            $score['organization'] += 1; $fired[] = 'begins "' . $head . '"';
        }
        if (in_array($head, $GIVEN, true)) {
            $score['person'] += 3; $fired[] = '"' . $head . '" is a given name';
        }
    }
    if (preg_match($HONORIFIC, $name)) {
        $score['person'] += 7; $fired[] = 'carries an honorific';
    }
    if (count($w) === 2 && !in_array(end($w), $PLACE_TAIL, true) && !in_array(end($w), $ORG_TAIL, true)) {
        $score['person'] += 2; $fired[] = 'two words, neither a type word';
    }

    /* Context. A pronoun in the same sentence is the strongest thing here, and
       a preposition immediately before the name is the next strongest. */
    $pron = 0; $prep = 0; $det = 0;
    foreach (($row['context'] ?? []) as $c) {
        $t = ' ' . mb_strtolower((string)$c['text']) . ' ';
        $n = mb_strtolower(trim((string)($c['match'] ?? $name)));
        if (preg_match('~\b(he|she|his|her|him|himself|herself|was born|died|married)\b~', $t)) { $pron++; }
        if ($n !== '' && preg_match('~\b(at|in|near|from|to|along|across|up|down|over)\s+(the\s+)?' . preg_quote($n, '~') . '~', $t)) { $prep++; }
        if ($n !== '' && preg_match('~\bthe\s+' . preg_quote($n, '~') . '~', $t)) { $det++; }
    }
    if ($pron) { $score['person'] += min(4, $pron * 2); $fired[] = 'a personal pronoun in ' . $pron . ' sample' . ($pron > 1 ? 's' : ''); }
    if ($prep) { $score['place'] += min(4, $prep * 2); $fired[] = 'preceded by a preposition in ' . $prep . ' sample' . ($prep > 1 ? 's' : ''); }
    if ($det) { $score['organization'] += 1; $score['place'] += 1; $fired[] = 'takes "the"'; }

    /* The extraction's own kind, as one voice and not the deciding one. */
    $k = (string)($row['kind'] ?? '');
    if (isset($score[$k])) { $score[$k] += 3; $fired[] = 'the extraction called it ' . $k; }

    arsort($score);
    $best = array_key_first($score);
    $vals = array_values($score);
    $margin = $vals[0] - ($vals[1] ?? 0);
    return [
        'type' => $best,
        'confidence' => $margin >= 5 ? 'clear' : ($margin >= 2 ? 'likely' : 'close'),
        'scores' => $score,
        'signals' => $fired,
        'disagrees' => ($k !== '' && $k !== $best),
    ];
};

/* ------------------------------------------------------------------ rows */

/* GROUP BY THE STRIPPED KEY FIRST.
 *
 * The export carries one row per name per kind, so the same thing arrives more
 * than once in two ways. "Philadelphia Mint" comes through as an organisation
 * on 34 articles and as a person on 1, because the extraction disagreed with
 * itself. "Phineas Banning" and "General Phineas Banning" come through
 * separately because one wears a rank. Both are one record.
 *
 * Left ungrouped they are two cards, two approvals and two records for one
 * thing, and the article counts that drive the whole ranking are split between
 * them. The honorific-stripped key is the identity, which is what it was built
 * to be: 144 of these groups exist in the corpus.
 *
 * The surviving title is the most-cited variant with its rank or honorific
 * taken off, so "General Beale" on six articles becomes Beale, and "General
 * Beale", "Colonel Beale" and "Mr. Beale" all become aliases of it. A reviewer
 * who wants the rank in the title can type it; a reviewer who never sees the
 * three variants cannot know to. */
$stripRaw = function (string $s) use ($HONORIFIC): string {
    do { $b = $s; $s = preg_replace('~^\s*(?:' . trim($HONORIFIC, '~^\b i') . ')\.?\s+~i', '', $s); } while ($s !== $b);
    return trim($s) !== '' ? trim($s) : $s;
};

$groups = [];
foreach (($src['entities'] ?? []) as $kind => $list) {
    foreach ($list as $r) {
        if (!empty($r['existing'])) { continue; }          /* already a record */
        if (!($r['articles'] ?? [])) { continue; }
        $k = (string)($r['key'] ?? '') !== '' ? (string)$r['key'] : mb_strtolower((string)$r['name']);
        $r['kind'] = $kind;
        $groups[$k][] = $r;
    }
}

$rows = [];
foreach ($groups as $gkey => $members) {
    /* The most-cited variant leads, and its name minus the honorific is the
       title. Everything else in the group becomes an alias. */
    usort($members, fn($a, $b) => [count($b['articles'] ?? []), (int)($b['mentions'] ?? 0)]
                              <=> [count($a['articles'] ?? []), (int)($a['mentions'] ?? 0)]);
    $lead = $members[0];

    $arts = []; $mentions = 0; $ctx = []; $variants = []; $extractions = [];
    foreach ($members as $m) {
        foreach (($m['articles'] ?? []) as $a) {
            if (isset($a['id'])) { $arts[$a['id']] = $a; }
        }
        $mentions += (int)($m['mentions'] ?? 0);
        foreach (($m['context'] ?? []) as $c) { $ctx[] = $c; }
        foreach (($m['variants'] ?? []) as $v) { $variants[$v] = true; }
        $extractions[$m['kind']] = true;
        if ($m['name'] !== $lead['name']) { $variants[$m['name']] = true; }
    }

    $title = $stripRaw((string)$lead['name']);
    if ($title !== $lead['name']) { $variants[$lead['name']] = true; }

    /* The guess runs on the merged row, so pooled context and the leading
       variant's wording both count. */
    $merged = $lead;
    $merged['name'] = $title;
    $merged['context'] = array_slice($ctx, 0, 3);
    $merged['kind'] = array_key_first($extractions);
    {
        $r = $merged;
        $kind = $merged['kind'];
        $g = $guess($r);
        $rows[] = [
            'name'        => $title,
            'key'         => (string)$gkey,
            'extraction'  => implode(' + ', array_keys($extractions)),
            'guess'       => $g['type'],
            'confidence'  => $g['confidence'],
            'signals'     => $g['signals'],
            'scores'      => $g['scores'],
            'disagrees'   => $g['disagrees'],
            'articleCount' => count($arts),
            'mentions'    => $mentions,
            'context'     => array_slice($ctx, 0, 3),
            'articles'    => array_values(array_map(fn($a) => [
                'id' => $a['id'] ?? null, 'title' => $a['title'] ?? '', 'url' => $a['url'] ?? ''], $arts)),
            'variants'    => array_values(array_keys($variants)),
            'mergedFrom'  => count($members),
        ];
    }
}

/* Ranked by articles, because that is the ranking that says which missing
   record costs the archive most. Mentions break the tie: a name used nine
   times in three articles is more established than one used three times. */
usort($rows, function ($a, $b) {
    return [$b['articleCount'], $b['mentions'], $a['name']] <=> [$a['articleCount'], $a['mentions'], $b['name']];
});

/* Every record we hold, for the merge-into selector on the screen. */
$existing = [];
foreach (['persons' => 'person', 'places' => 'place', 'organizations' => 'organization'] as $section => $type) {
    foreach (\craft\elements\Entry::find()->section($section)->status(null)->limit(null)->all() as $e) {
        $existing[] = ['id' => $e->id, 'title' => (string)$e->title, 'type' => $type, 'url' => (string)$e->url];
    }
}
usort($existing, fn($a, $b) => strcmp($a['title'], $b['title']));

file_put_contents($out, json_encode([
    'meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/export_missing_records.php',
        'source' => basename($in),
        'names' => count($rows),
        'records_held' => count($existing),
    ],
    'records' => $existing,
    'names' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo 'names with no record: ' . count($rows) . PHP_EOL;
echo 'records held: ' . count($existing) . PHP_EOL;
$byGuess = [];
foreach ($rows as $r) { $byGuess[$r['guess']] = ($byGuess[$r['guess']] ?? 0) + 1; }
foreach ($byGuess as $k => $v) { echo '  guessed ' . str_pad($k, 14) . $v . PHP_EOL; }
echo 'guess disagrees with the extraction: ' . count(array_filter($rows, fn($r) => $r['disagrees'])) . PHP_EOL;
echo PHP_EOL . 'top 20 by articles:' . PHP_EOL;
foreach (array_slice($rows, 0, 20) as $r) {
    echo '  ' . str_pad(mb_substr($r['name'], 0, 32), 34)
       . str_pad($r['guess'], 14)
       . str_pad($r['confidence'], 8)
       . str_pad((string)$r['articleCount'], 5) . 'articles'
       . ($r['disagrees'] ? '   (extraction said ' . $r['extraction'] . ')' : '') . PHP_EOL;
}
echo PHP_EOL . 'wrote ' . $out . PHP_EOL;
