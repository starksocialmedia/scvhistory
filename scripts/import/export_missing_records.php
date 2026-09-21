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

$SECTION_GUESS_OK = ['person' => 1, 'place' => 1, 'organization' => 1];

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

/* The place subtype, guessed from the name's last word for the screen's
   selector. Same idea as the type guess and the same rule about showing it:
   the reviewer sees what was chosen and changes it in one click. "Site" is the
   fallback rather than empty, because a named place in the corpus that is not a
   river, a road or a building is almost always somewhere a thing stood. */
$SUBTYPE_TAIL = [
    'river' => 'natural', 'creek' => 'natural', 'canyon' => 'natural', 'arroyo' => 'natural',
    'spring' => 'natural', 'springs' => 'natural', 'lake' => 'natural', 'mesa' => 'natural',
    'hill' => 'natural', 'hills' => 'natural', 'mountain' => 'natural', 'mountains' => 'natural',
    'valley' => 'natural', 'pass' => 'natural', 'ridge' => 'natural', 'peak' => 'natural',
    'flat' => 'natural', 'flats' => 'natural', 'wash' => 'natural', 'cienega' => 'natural',
    'bend' => 'natural', 'basin' => 'natural', 'cut' => 'natural', 'grade' => 'natural',

    'road' => 'road', 'street' => 'road', 'avenue' => 'road', 'boulevard' => 'road',
    'highway' => 'road', 'lane' => 'road',

    'ranch' => 'ranch', 'rancho' => 'ranch',
    'park' => 'park', 'plaza' => 'park',
    'trail' => 'trail',

    'station' => 'building', 'depot' => 'building', 'mill' => 'building', 'refinery' => 'building',
    'works' => 'building', 'hotel' => 'building', 'saloon' => 'building', 'store' => 'building',
    'school' => 'building', 'church' => 'building', 'theatre' => 'building', 'theater' => 'building',
    'tunnel' => 'building', 'bridge' => 'building', 'dam' => 'building', 'museum' => 'building',

    'mine' => 'site', 'camp' => 'site', 'junction' => 'site', 'crossing' => 'site',
    'cemetery' => 'site', 'reservoir' => 'site',
];

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
        /* A canon ruling overrides the guess outright, and says so on the card
           so a reviewer sees it was decided rather than inferred. */
        $canonType = $lead['canonType'] ?? null;
        if ($canonType !== null && isset($SECTION_GUESS_OK[$canonType])) {
            if ($g['type'] !== $canonType) {
                $g['signals'][] = 'the canon says ' . $canonType . ', overriding the guess of ' . $g['type'];
            } else {
                $g['signals'][] = 'the canon says ' . $canonType;
            }
            $g['type'] = $canonType;
            $g['confidence'] = 'decided';
            $g['disagrees'] = false;
        }
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
            'parked'      => $lead['parked'] ?? null,
            'placeType'   => (function () use ($title, $words, $SUBTYPE_TAIL, $lead) {
                if (!empty($lead['canonPlaceType'])) { return (string)$lead['canonPlaceType']; }
                $w = $words($title);
                if (!$w) { return 'site'; }
                $tail = end($w);
                if (isset($SUBTYPE_TAIL[$tail])) { return $SUBTYPE_TAIL[$tail]; }
                /* "Lake Hughes" and "Rancho San Francisco" name their type
                   first, which is the Spanish and the gazetteer habit both. */
                $head = $w[0];
                if (isset($SUBTYPE_TAIL[$head])) { return $SUBTYPE_TAIL[$head]; }
                return 'site';
            })(),
        ];
    }
}

/* PAGE FURNITURE, REMOVED BEFORE THE QUEUE IS RANKED.
 *
 * "Post Office Box" is on fifteen articles and "Gazette Archive" on fifteen,
 * which puts both inside the top fifty by article count, which is where a batch
 * approval would create records for them. Neither is a thing. One is the
 * contact line at the foot of every Old Town Newhall page and the other is a
 * link in the navigation bar.
 *
 * The words do not give them away: "Gazette Archive" is shaped exactly like a
 * name. What gives them away is that furniture appears in the SAME SENTENCE
 * every time, because it is the same sentence repeated across pages, while a
 * person appears in a different sentence in every article. The sample sentences
 * for "Post Office Box" are identical; those for Jan Heidt, a real mayor, are
 * 42 per cent alike, and real names run from 14 per cent up.
 *
 * The threshold is 85, which is well clear of the highest real name measured.
 * Two samples are required: one sentence cannot be compared with itself, and a
 * name appearing in a single article is not what this is for.
 *
 * An earlier version of this test asked whether the name carried a given name,
 * an honorific or a nearby pronoun. It flagged Doc Rioux, Jan Heidt and Sanford
 * Lyon, who are real people, and a check that wrong teaches a reviewer to
 * ignore it.
 *
 * They are parked, not deleted, and written to the file so the rule can be
 * audited against what it removed. */
/* PARKED BY THE CANON.
 *
 * A name the canon has ruled must not become a record: a document, a phrase, a
 * thing that belongs to no section. It is removed here rather than left for a
 * reviewer to skip every time the queue is rebuilt, and it is written to the
 * file with its reason so the ruling is visible rather than just its effect. */
$parked = [];
$keepP = [];
foreach ($rows as $r) {
    if (!empty($r['parked'])) { $parked[] = $r; continue; }
    $keepP[] = $r;
}
$rows = $keepP;

$FURNITURE_AT   = 85;
$FURNITURE_MIN  = 3;        /* distinct articles the same sentence must span */
$furniture = [];
$keep = [];
foreach ($rows as $r) {
    /* One sentence per ARTICLE, not per sample. Two things were breaking this:
       a name in a single article can have the same sentence sampled twice, and
       the legacy site mirrors some pages at two paths, so
       /worden/old/lw080798ea.htm and /worden/lw080798ea.htm are one article
       under two names. Both produce a pair of identical sentences that have
       nothing to do with furniture. Keying on the basename collapses the
       mirror, and requiring three distinct articles means a name cannot be
       called furniture on the strength of one page. */
    $byArticle = [];
    foreach (($r['context'] ?? []) as $c) {
        $t = trim((string)($c['text'] ?? ''));
        if ($t === '') { continue; }
        $k = basename((string)($c['path'] ?? $t));
        if (!isset($byArticle[$k])) { $byArticle[$k] = $t; }
    }
    $texts = array_values($byArticle);

    if (count($texts) < 2 || $r['articleCount'] < $FURNITURE_MIN) { $keep[] = $r; continue; }

    $sum = 0; $n = 0;
    for ($i = 0; $i < count($texts); $i++) {
        for ($j = $i + 1; $j < count($texts); $j++) {
            similar_text($texts[$i], $texts[$j], $pc);
            $sum += $pc; $n++;
        }
    }
    $avg = $n ? $sum / $n : 0;
    $r['contextSimilarity'] = round($avg, 1);
    $r['contextArticles'] = count($texts);
    if ($avg >= $FURNITURE_AT) {
        $r['furnitureSample'] = $texts[0];
        $furniture[] = $r;
        continue;
    }
    $keep[] = $r;
}
$rows = $keep;

/* ALREADY DECIDED, carried through from the last session.
 *
 * A name approved last time has a record now and has already left this queue.
 * A name SKIPPED or MERGED has not: it still has no record of its own, so it
 * comes back at the top of the pile it was just dismissed from, and the next
 * fifty are the same fifty. Marking them lets the screen hide what has been
 * answered and show what has not.
 *
 * Matched on key and then on name, because the canon moves keys between a
 * review and the next export. */
$decidedPath = $REVIEW . '/records-decided.json';
$decidedBy = ['key' => [], 'name' => []];
if (file_exists($decidedPath)) {
    $dd = json_decode(file_get_contents($decidedPath), true) ?: [];
    foreach (($dd['decisions'] ?? []) as $x) {
        $act = (string)($x['action'] ?? '');
        if ($act === '') { continue; }
        if (!empty($x['key'])) { $decidedBy['key'][(string)$x['key']] = $act; }
        if (!empty($x['name'])) { $decidedBy['name'][mb_strtolower(trim((string)$x['name']))] = $act; }
    }
}
$decidedCount = 0;
foreach ($rows as $i => $r) {
    $act = $decidedBy['key'][$r['key']] ?? $decidedBy['name'][mb_strtolower($r['name'])] ?? null;
    if ($act !== null) { $rows[$i]['decidedAs'] = $act; $decidedCount++; }
}

/* Ranked by articles, because that is the ranking that says which missing
   record costs the archive most. Mentions break the tie: a name used nine
   times in three articles is more established than one used three times. */
usort($rows, function ($a, $b) {
    return [$b['articleCount'], $b['mentions'], $a['name']] <=> [$a['articleCount'], $a['mentions'], $b['name']];
});

/* NAMES THAT CONTAIN OTHER NAMES.
 *
 * "Southern Pacific" and "Southern Pacific Railroad" are one railroad.
 * "Hart Park" and "William S. Hart Park" are one park. "Newhall Land",
 * "Newhall Land and Farming" and "Newhall Land and Farming Company" are one
 * company three times. Approved as they stand that is eight records for four
 * things, and the second copy of each is found a year later by somebody
 * wondering why half the articles link to the wrong one.
 *
 * The stripped-name key does not catch these, and should not: it exists to
 * collapse "General Beale" into "Beale", where the difference is a rank. Here
 * the difference is a real word, and a real word sometimes means a real
 * difference. "Newhall Ranch" and "Newhall Land and Farming Company" are a
 * place and the company that owned it.
 *
 * So this decides nothing. It pairs them for the screen, which puts the two
 * side by side with their evidence and asks. Bounded to the top of the queue
 * because that is where a batch approval does damage, and because comparing
 * every name against every other name is four thousand squared.
 *
 * "the" and "of" are dropped before comparing, so "Santa Clarita Valley
 * Historical Society" contains "SCV Historical Society" only if the words
 * genuinely nest, which they do not. That pair is a different shape and the
 * screen will not raise it. */
$CONTAIN_TOP = 200;
$toks = function (string $s): array {
    $s = mb_strtolower(preg_replace('~[^a-zA-Z0-9 ]~', ' ', $s));
    $t = preg_split('~\s+~', trim($s), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    return array_values(array_diff($t, ['the', 'of', 'and', 'a']));
};
$containment = [];
$head = array_slice($rows, 0, $CONTAIN_TOP);
foreach ($head as $i => $a) {
    foreach ($head as $j => $b) {
        if ($i >= $j) { continue; }
        $ta = $toks($a['name']); $tb = $toks($b['name']);
        if (!$ta || !$tb || $ta === $tb) { continue; }
        $aExtra = array_diff($ta, $tb);
        $bExtra = array_diff($tb, $ta);
        if ($aExtra && $bExtra) { continue; }

        if (!$aExtra && !$bExtra) {
            /* Same words, different order: "Lake Elizabeth" against "Elizabeth
               Lake". Neither contains the other and "keep the longer form" has
               nothing to choose between them, so it is marked as its own shape
               and the screen asks which spelling wins rather than assuming. */
            $shape = 'reordered';
            $short = mb_strlen($a['name']) <= mb_strlen($b['name']) ? $a : $b;
            $long  = $short === $a ? $b : $a;
        } else {
            $shape = 'contains';
            $short = $aExtra ? $b : $a;
            $long  = $aExtra ? $a : $b;
        }
        $containment[] = [
            'shape' => $shape,
            'shortKey' => $short['key'], 'short' => $short['name'],
            'shortType' => $short['guess'], 'shortArticles' => $short['articleCount'],
            'longKey' => $long['key'], 'long' => $long['name'],
            'longType' => $long['guess'], 'longArticles' => $long['articleCount'],
            'shared' => count(array_intersect(
                array_column($short['articles'], 'id'), array_column($long['articles'], 'id'))),
        ];
    }
}

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
        'alreadyDecided' => $decidedCount,
        'furnitureRemoved' => count($furniture),
        'furnitureThreshold' => $FURNITURE_AT,
        'containmentPairs' => count($containment),
        'records_held' => count($existing),
    ],
    'records' => $existing,
    'names' => $rows,
    'furniture' => $furniture,
    'parked' => $parked,
    'containment' => $containment,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo 'names with no record: ' . count($rows) . PHP_EOL;
echo 'parked by the canon, never offered: ' . count($parked) . PHP_EOL;
foreach ($parked as $x) { printf("  %-38s %d articles\n", mb_substr($x['name'],0,37), $x['articleCount']); }
echo 'carried over as already decided: ' . $decidedCount . PHP_EOL;
echo 'removed as page furniture: ' . count($furniture) . PHP_EOL;
echo 'names containing other names, for the screen to ask about: ' . count($containment) . PHP_EOL;
foreach (array_slice($furniture, 0, 12) as $f) {
    printf("  %-30s %2d articles, samples %d%% alike\n     %s\n",
        mb_substr($f['name'], 0, 29), $f['articleCount'], round($f['contextSimilarity']),
        mb_substr(trim($f['furnitureSample']), 0, 84));
}
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
