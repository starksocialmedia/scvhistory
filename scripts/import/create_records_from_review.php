/**
 * Creates the records approved on /review/records.html and links them to the
 * articles that name them.
 *
 * Input is records-decided.json, downloaded from the screen. With no decided
 * file it can dry-run the top N of the queue as though each had been approved
 * at its guessed type, which is what the screen's batch button does:
 *
 *   ddev craft exec "$TOP_N=50; $RECORDS_SUFFIX='-full'; eval(file_get_contents('scripts/import/create_records_from_review.php'))"
 *
 * $APPLY is false. Nothing is written until it is flipped locally.
 *
 * WHAT AN APPROVAL DOES
 *
 *   creates   one entry in persons, places or organizations, titled with the
 *             name, carrying every variant as an alias so "General Beale" and
 *             "Mr. Beale" find the record that is titled Beale
 *   relates   the entry to every article that names it, by appending to the
 *             article's existing relation rather than replacing it. Replacing
 *             is how a script quietly unlinks work somebody did by hand.
 *   records   where it came from, in recordProvenance
 *
 * A merge creates nothing. It adds the name to an existing record's alias list,
 * which is the whole of what "this name is that record" means here.
 *
 * THE PROVENANCE FIELD
 *
 * persons, places and organizations have no provenance field. The value has to
 * be queryable later, because "everything a batch created on one afternoon" is
 * exactly the set somebody will want to inspect or undo, and it must not print
 * on the page, which rules out webmasterNoteTop and editorNotes. Overloading
 * sourcePath or legacyCategory would cost those fields the meaning they already
 * carry. So this wants one new PlainText field, recordProvenance, on the three
 * record entry types.
 *
 * It does not exist yet. This script checks for it, reports its absence, and
 * refuses to apply without it rather than silently dropping the provenance on
 * the floor. The dry run works either way.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_records_from_review.php'))"
 */

$APPLY = false;

$REVIEW = \Craft::getAlias('@webroot') . '/review';
$SUFFIX = $RECORDS_SUFFIX ?? '';
$TOP    = isset($TOP_N) ? (int)$TOP_N : 0;
$PROV_FIELD = 'recordProvenance';

$SECTION_FOR = ['person' => 'persons', 'place' => 'places', 'organization' => 'organizations'];
$TYPE_FOR    = ['person' => 'person', 'place' => 'place', 'organization' => 'organization'];
$ALIAS_FOR   = ['person' => 'personAliases', 'place' => 'placeAliases', 'organization' => 'orgAliases'];
/* The article side owns the relation, which is the side the exporters read. */
$LINK_FOR    = ['person' => 'subjectPerson', 'place' => 'depictsPlace', 'organization' => 'subjectOrganization'];

$hasField = function ($el, string $handle): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

/* ------------------------------------------------------------ the decisions */

$decided = $REVIEW . '/records-decided.json';
$queue   = $REVIEW . '/records' . $SUFFIX . '.json';

$decisions = [];
$byKey = [];
$sourceNote = '';

if ($TOP > 0) {
    if (!file_exists($queue)) { echo 'no ' . $queue . PHP_EOL; return; }
    $q = json_decode(file_get_contents($queue), true);
    foreach (array_slice($q['names'] ?? [], 0, $TOP) as $r) {
        $decisions[] = [
            'action' => 'approved', 'key' => $r['key'], 'name' => $r['name'],
            'type' => $r['guess'], 'articles' => array_column($r['articles'], 'id'),
            'variants' => $r['variants'] ?? [], 'confidence' => $r['confidence'],
            'signals' => $r['signals'] ?? [], 'context' => $r['context'] ?? [],
            'placeType' => $r['placeType'] ?? '',
        ];
    }
    $sourceNote = 'top ' . $TOP . ' of ' . basename($queue) . ', each at its guessed type';
} elseif (file_exists($decided)) {
    $d = json_decode(file_get_contents($decided), true);
    /* Keyed twice, and the second one matters.
     *
     * A decision carries the key the queue had when it was reviewed. Regenerate
     * the queue between the review and the apply and those keys move: the name
     * canon folded "hart park" into "william s hart park" and "lyons" into
     * "lyons avenue", so four of the fifty-two decisions in this file point at
     * keys that no longer exist.
     *
     * Nothing important is lost when that happens, because the name, type,
     * articles, aliases and placeType all come from the decision itself. What
     * is lost is the backfill: confidence, context, and the comparison that
     * detects a hand-edited title. Falling back to the name keeps that working
     * across a regeneration. */
    $byKey = []; $byName = [];
    $nrm = fn(string $v) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $v)));
    if (file_exists($queue)) {
        foreach ((json_decode(file_get_contents($queue), true)['names'] ?? []) as $r) {
            $byKey[$r['key']] = $r;
            $byName[$nrm($r['name'])] = $r;
        }
    }
    $stale = 0;
    foreach ($d['decisions'] ?? [] as $x) {
        $q = $byKey[$x['key'] ?? ''] ?? null;
        if ($q === null) {
            $stale++;
            $q = $byName[$nrm((string)($x['name'] ?? ''))] ?? [];
        }
        $x['variants'] = $q['variants'] ?? [];
        $x['confidence'] = $q['confidence'] ?? '';
        $x['context'] = $q['context'] ?? [];
        $x['_queueName'] = $q['name'] ?? '';
        $x['signals'] = $q['signals'] ?? [];
        if (!isset($x['placeType'])) { $x['placeType'] = $q['placeType'] ?? ''; }
        $decisions[] = $x;
    }
    $sourceNote = basename($decided) . ', decided ' . ($d['generated'] ?? '?')
        . ($stale ? '  [' . $stale . ' of ' . count($d['decisions'] ?? []) . ' keys no longer in the queue, matched by name]' : '');
} else {
    echo 'no ' . $decided . ' and no $TOP_N set. Nothing to do.' . PHP_EOL;
    return;
}

$PROVENANCE = 'created from review, ' . (new DateTime())->format('Y-m-d');

/* ------------------------------------------------------------- the checks */

$sample = \craft\elements\Entry::find()->section('persons')->status(null)->one();
$hasProv = $sample ? $hasField($sample, $PROV_FIELD) : false;

echo '=== create_records_from_review ===' . PHP_EOL;
echo 'source:     ' . $sourceNote . PHP_EOL;
echo 'provenance: ' . $PROVENANCE . PHP_EOL;
echo 'mode:       ' . ($APPLY ? 'APPLY' : 'DRY RUN, nothing is written') . PHP_EOL;
echo $PROV_FIELD . ': ' . ($hasProv ? 'present' : 'MISSING, a schema change is needed before apply') . PHP_EOL;
if ($APPLY && !$hasProv) {
    echo 'refusing to apply without somewhere to record provenance.' . PHP_EOL;
    return;
}
echo PHP_EOL;

/* Titles already held, so a decision that would collide is caught before it
   creates a second record for a thing the archive has. */
$held = [];
foreach ($SECTION_FOR as $type => $section) {
    foreach (\craft\elements\Entry::find()->section($section)->status(null)->limit(null)->all() as $e) {
        $held[$type][mb_strtolower(trim((string)$e->title))] = $e;
    }
}

/* ------------------------------------------------------------- the work */

$plan = ['create' => [], 'merge' => [], 'skip' => [], 'collision' => [], 'external' => []];
$deferred = [];          /* merges pointing at another decision in this file */
$overrides = [];         /* names edited by hand away from the queue's spelling */
$linkTotal = 0; $articlesTouched = [];

foreach ($decisions as $x) {
    $act  = (string)($x['action'] ?? '');
    $name = trim((string)($x['name'] ?? ''));
    $type = (string)($x['type'] ?? '');
    if ($name === '' || !isset($SECTION_FOR[$type])) { continue; }

    if ($act === 'skipped') { $plan['skip'][] = $x; continue; }

    /* External is a ruling, not an omission, and the difference matters when
       somebody reads this report in six months. Skip means the queue has not
       been settled; external means it has, and the answer was that the archive
       holds no record because the valley has no claim on the subject. It
       creates nothing here either way, but it is counted and named. */
    if ($act === 'external') { $plan['external'][] = $x; continue; }

    if ($act === 'merged') {
        /* Two shapes of merge.
         *
         *   into      an existing record's id. The name becomes an alias of a
         *             record the archive already holds.
         *   intoKey   the key of another decision in THIS file, which is the
         *             shape the containment pairs write: "Newhall Land" merges
         *             into "Newhall Land and Farming Company", and that target
         *             does not exist yet because this same run is about to
         *             create it.
         *
         * The second cannot be resolved here, because the target's record has
         * no id until the creates have run. It is deferred and resolved after
         * the plan is built. */
        if (!empty($x['intoKey'])) {
            $deferred[] = ['name' => $name, 'key' => (string)($x['key'] ?? ''),
                           'intoKey' => (string)$x['intoKey'],
                           'intoName' => (string)($x['intoName'] ?? ''),
                           'articles' => array_values(array_unique(array_filter((array)($x['articles'] ?? []))))];
            continue;
        }
        $into = \craft\elements\Entry::find()->id((int)($x['into'] ?? 0))->status(null)->one();
        $plan['merge'][] = ['name' => $name, 'into' => $into ? $into->title : '(missing #' . ($x['into'] ?? '?') . ')',
                            'intoId' => $x['into'] ?? null, 'ok' => (bool)$into, 'via' => 'id'];
        continue;
    }

    if ($act !== 'approved') { continue; }

    $existing = $held[$type][mb_strtolower($name)] ?? null;
    if ($existing) {
        $plan['collision'][] = ['name' => $name, 'type' => $type, 'id' => $existing->id];
        continue;
    }

    $ids = array_values(array_unique(array_filter((array)($x['articles'] ?? []))));
    $linkTotal += count($ids);
    foreach ($ids as $i) { $articlesTouched[$i] = true; }

    /* A hand-edited title wins, and the spelling the corpus actually uses
       becomes an alias. "Anne Darcy" corrected to "Jo Anne Darcy" is still the
       string forty articles contain, and a record that cannot be found under
       the name the text uses has lost the thing the rename was for. */
    $aliases = array_values(array_filter((array)($x['variants'] ?? [])));
    $queueName = trim((string)($x['_queueName'] ?? ''));
    if ($queueName !== '' && $queueName !== $name) {
        $overrides[] = ['from' => $queueName, 'to' => $name];
        if (!in_array($queueName, $aliases, true)) { $aliases[] = $queueName; }
    }
    foreach ((array)($x['aliases'] ?? []) as $a) {
        $a = trim((string)$a);
        if ($a !== '' && $a !== $name && !in_array($a, $aliases, true)) { $aliases[] = $a; }
    }

    $plan['create'][] = [
        'name' => $name, 'type' => $type, 'section' => $SECTION_FOR[$type],
        'aliases' => $aliases,
        'key' => (string)($x['key'] ?? ''),
        'articles' => $ids, 'confidence' => (string)($x['confidence'] ?? ''),
        'signals' => (array)($x['signals'] ?? []),
        'context' => (array)($x['context'] ?? []),
        'placeType' => $type === 'place' ? (string)($x['placeType'] ?? '') : '',
        'linkField' => $LINK_FOR[$type],
    ];
}

/* ------------------------------------------- resolve the same-file merges

   A merge whose target is another decision in this file can only be resolved
   once the create plan exists, because the target has no record yet. The name
   becomes an alias on the target's record, and the merged row's articles are
   unioned into the target's links: the screen already does that union when it
   writes the pair, but a hand-edited file need not have, and a union applied
   twice is the same union.

   Chains are followed, so A into B into C lands on C, with a visited guard
   because a file edited by hand can say A into B and B into A. */
$createByKey = [];
foreach ($plan['create'] as $i => $c) { if ($c['key'] !== '') { $createByKey[$c['key']] = $i; } }

foreach ($deferred as $d) {
    $seen = [];
    $targetKey = $d['intoKey'];
    while ($targetKey !== '' && !isset($seen[$targetKey])) {
        $seen[$targetKey] = true;
        $next = '';
        foreach ($deferred as $e) {
            if ($e['key'] === $targetKey && $e['intoKey'] !== '') { $next = $e['intoKey']; break; }
        }
        if ($next === '') { break; }
        $targetKey = $next;
    }

    if (isset($createByKey[$targetKey])) {
        $idx = $createByKey[$targetKey];
        $t =& $plan['create'][$idx];
        if (!in_array($d['name'], $t['aliases'], true)) { $t['aliases'][] = $d['name']; }
        $before = count($t['articles']);
        $t['articles'] = array_values(array_unique(array_merge($t['articles'], $d['articles'])));
        $added = count($t['articles']) - $before;
        $linkTotal += $added;
        foreach ($t['articles'] as $i) { $articlesTouched[$i] = true; }
        $plan['merge'][] = ['name' => $d['name'], 'into' => $t['name'], 'intoId' => null,
                            'ok' => true, 'via' => 'intoKey', 'added' => $added,
                            'chained' => $targetKey !== $d['intoKey']];
        unset($t);
        continue;
    }

    /* The target might already be a record rather than a pending creation. */
    $hit = null;
    foreach ($SECTION_FOR as $type => $section) {
        foreach (($held[$type] ?? []) as $t => $e) {
            if ($t === mb_strtolower(trim($d['intoName'])) || $t === $targetKey) { $hit = $e; break 2; }
        }
    }
    if ($hit) {
        $plan['merge'][] = ['name' => $d['name'], 'into' => $hit->title, 'intoId' => $hit->id,
                            'ok' => true, 'via' => 'intoKey -> existing record'];
        continue;
    }

    $plan['merge'][] = ['name' => $d['name'],
                        'into' => '(no decision or record with key "' . $d['intoKey'] . '")',
                        'intoId' => null, 'ok' => false, 'via' => 'intoKey'];
}

/* ------------------------------------------------ against the curated canon

   The canon and a decisions file are two people answering the same question,
   and where they disagree the disagreement is the finding. A decision that
   creates a record under a name the canon lists as an ALIAS is the one that
   matters: it would put back the row the canon exists to fold away. */
$canonPath = \Craft::getAlias('@root') . '/inventory/legacy/name-canon.json';
$canon = file_exists($canonPath) ? (json_decode(file_get_contents($canonPath), true) ?: []) : [];
$conflicts = [];
if ($canon) {
    $norm = fn(string $v) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $v)));
    $canonOf = []; $typeOf = [];
    foreach (($canon['canon'] ?? []) as $c) {
        $typeOf[$norm($c['canonical'])] = $c['type'];
        foreach (($c['aliases'] ?? []) as $a) { $canonOf[$norm($a)] = $c; }
    }
    foreach (($canon['splits'] ?? []) as $sp) {
        foreach (($sp['splits'] ?? []) as $x) { $typeOf[$norm($x['canonical'])] = $x['type']; }
        $canonOf[$norm($sp['name'])] = ['canonical' => $sp['name'] . ' (splits)', 'type' => '', 'split' => true];
    }

    /* THE NAME POLICY, enforced.
     *
     * docs/DATA-MODEL.md says a title never enters a record's name: the bare
     * name is the title and the titled form is an alias. A decision made
     * before that was written can have it exactly backwards, and this file has
     * one: "Councilwoman Jill Klajic" as the record with "Jill Klajic" as its
     * alias, while Jill Klajic already exists as #15874. Approving it would
     * create a second record for her under a job she held for four years. */
    $TITLES = 'mr|mrs|ms|miss|dr|doctor|fr|father|capt|captain|col|colonel|gen|general|lt|lieutenant|'
            . 'rev|reverend|sgt|sergeant|maj|major|judge|gov|governor|prof|professor|'
            . 'congressman|congresswoman|councilman|councilwoman|councilmember|mayor|supervisor|'
            . 'sheriff|senator|assemblyman|assemblywoman|chief|president|secretary|commissioner';
    foreach ($plan['create'] as $c) {
        if (!preg_match('~^(' . $TITLES . ')\.?\s+(.+)$~i', trim($c['name']), $m)) { continue; }
        $bare = trim($m[2]);
        /* Mrs, Miss, Ms and Sister are never stripped: she is not her husband. */
        if (preg_match('~^(mrs|miss|ms|sister)$~i', $m[1])) { continue; }
        $held = \craft\elements\Entry::find()->section($SECTION_FOR[$c['type']] ?? 'persons')
            ->title($bare)->status(null)->one();
        $conflicts[] = '"' . $c['name'] . '" carries a title. The policy is that the name is the '
            . 'record and the title is the alias, so this should be "' . $bare . '"'
            . ($held ? ', which already exists as #' . $held->id : '')
            . '. As decided it would create the person under the job.';
    }

    foreach ($plan['create'] as $c) {
        $n = $norm($c['name']);
        if (isset($canonOf[$n])) {
            $conflicts[] = $c['name'] . ' is a canon alias of "' . $canonOf[$n]['canonical']
                . '"' . (!empty($canonOf[$n]['split']) ? ' and the canon splits it by context' : '')
                . '; the decision would create it as its own record';
            continue;
        }
        if (isset($typeOf[$n]) && $typeOf[$n] !== $c['type']) {
            $conflicts[] = $c['name'] . ': decided as ' . $c['type'] . ', canon says ' . $typeOf[$n];
        }
    }
}

/* ------------------------------------------------------------- the report */

echo 'WOULD CREATE ' . count($plan['create']) . ' records, ' . $linkTotal . ' article links across '
   . count($articlesTouched) . ' distinct articles' . PHP_EOL . PHP_EOL;

$byType = [];
foreach ($plan['create'] as $c) { $byType[$c['type']] = ($byType[$c['type']] ?? 0) + 1; }
foreach ($byType as $t => $n) { echo '  ' . str_pad($t, 15) . $n . PHP_EOL; }
echo PHP_EOL;

printf("  %-34s %-20s %-9s %6s  %s\n", 'TITLE', 'SECTION', 'CONFIDENCE', 'LINKS', 'ALIASES');
foreach ($plan['create'] as $c) {
    printf("  %-34s %-20s %-9s %6d  %s\n",
        mb_substr($c['name'], 0, 33), $c['section'] . ($c['placeType'] !== '' ? '/' . $c['placeType'] : ''),
        $c['confidence'], count($c['articles']),
        $c['aliases'] ? mb_substr(implode(', ', $c['aliases']), 0, 44) : '');
}

/* NAMES IN THIS BATCH THAT ARE PROBABLY ONE THING.
 *
 * The queue is deduplicated on the honorific-stripped name, which catches
 * "General Beale" against "Beale" and nothing else. It does not catch "Southern
 * Pacific" against "Southern Pacific Railroad", or "Hart Park" against "William
 * S. Hart Park", because those differ by a real word rather than a rank. Every
 * one of those pairs approved as it stands is two records for one thing, and
 * the second one is discovered a year later by somebody wondering why half the
 * articles link to the wrong one.
 *
 * Deliberately not merged automatically. "Newhall Ranch" and "Newhall Land and
 * Farming Company" are a place and the company that owned it; containment is
 * evidence, not a verdict. It is printed so the batch can be fixed before it
 * runs rather than audited after. */
/* Pairs already answered. A containment pair a person has looked at and called
   two different things must not come back as an open question every run: the
   gate would then be unsatisfiable, and an unsatisfiable gate gets switched
   off. Verdicts live in the decisions file beside everything else. */
$answeredPairs = [];
foreach ($decisions as $x) {
    if (($x['type'] ?? '') !== 'pair') { continue; }
    $k = mb_strtolower(trim((string)($x['short'] ?? '')) . '|' . trim((string)($x['long'] ?? '')));
    $answeredPairs[$k] = (string)($x['action'] ?? '');
    $rk = mb_strtolower(trim((string)($x['long'] ?? '')) . '|' . trim((string)($x['short'] ?? '')));
    $answeredPairs[$rk] = (string)($x['action'] ?? '');
}

$containment = [];
$answeredPairsUsed = [];
$tok = function (string $s): array {
    $s = mb_strtolower(preg_replace('~[^a-zA-Z0-9 ]~', ' ', $s));
    $t = preg_split('~\s+~', trim($s), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    return array_values(array_diff($t, ['the', 'of', 'and', 'a']));
};
foreach ($plan['create'] as $i => $a) {
    foreach ($plan['create'] as $j => $b) {
        if ($i >= $j) { continue; }
        $ta = $tok($a['name']); $tb = $tok($b['name']);
        if (!$ta || !$tb || $ta === $tb) { continue; }
        $sub = !array_diff($ta, $tb) ? 'a' : (!array_diff($tb, $ta) ? 'b' : null);
        if (!$sub) { continue; }
        $short = $sub === 'a' ? $a : $b;
        $long  = $sub === 'a' ? $b : $a;
        $key = mb_strtolower($short['name'] . '|' . $long['name']);
        if (isset($answeredPairs[$key])) { $answeredPairsUsed[] = $short['name'] . ' / ' . $long['name']; continue; }
        $containment[] = ['short' => $short, 'long' => $long];
    }
}
if ($answeredPairsUsed) {
    echo PHP_EOL . 'CONTAINMENT PAIRS ALREADY ANSWERED (' . count($answeredPairsUsed) . '):' . PHP_EOL;
    foreach ($answeredPairsUsed as $x) { echo '  ' . $x . PHP_EOL; }
}
if ($containment) {
    echo PHP_EOL . 'ONE NAME CONTAINS ANOTHER, probably one record (' . count($containment) . '):' . PHP_EOL;
    foreach ($containment as $c) {
        printf("  %-34s (%s, %d links)  inside  %s (%s, %d links)\n",
            $c['short']['name'], $c['short']['type'], count($c['short']['articles']),
            $c['long']['name'], $c['long']['type'], count($c['long']['articles']));
    }
}

/* PAGE FURNITURE READ AS A NAME.
 *
 * "Post Office Box" occurs on fifteen articles and "Gazette Archive" on
 * fifteen, and neither is a thing. One is the contact line at the foot of the
 * Old Town Newhall pages and the other is a link in the navigation bar. By
 * article count they sit in the top fifty, which is precisely where an
 * unchecked batch would create records for them.
 *
 * What separates them from a real name is not the words. It is that furniture
 * appears in the same sentence every time, because it IS the same sentence
 * repeated across pages, while a person turns up in a different sentence in
 * every article. The sample sentences for "Post Office Box" are identical to
 * each other; those for "Jan Heidt", who is a real mayor, are 42 per cent
 * alike, and the spread runs down to 14 per cent.
 *
 * An earlier version of this check tested whether the name carried a given
 * name, an honorific or a nearby pronoun. It flagged Doc Rioux, Jan Heidt and
 * Sanford Lyon, who are real, and would have taught a reviewer to ignore it. */
$furniture = [];
foreach ($plan['create'] as $c) {
    $texts = [];
    foreach (($c['context'] ?? []) as $x) {
        if (trim((string)($x['text'] ?? '')) !== '') { $texts[] = (string)$x['text']; }
    }
    if (count($texts) < 2) { continue; }
    $sum = 0; $n = 0;
    for ($i = 0; $i < count($texts); $i++) {
        for ($j = $i + 1; $j < count($texts); $j++) {
            similar_text($texts[$i], $texts[$j], $pc);
            $sum += $pc; $n++;
        }
    }
    $avg = $n ? $sum / $n : 0;
    if ($avg >= 85) { $furniture[] = ['c' => $c, 'sim' => $avg, 'eg' => $texts[0]]; }
}
if ($furniture) {
    echo PHP_EOL . 'THE SAME SENTENCE EVERY TIME, probably page furniture (' . count($furniture) . '):' . PHP_EOL;
    foreach ($furniture as $f) {
        printf("  %-30s %d links, samples %d%% alike\n    %s\n",
            $f['c']['name'], count($f['c']['articles']), round($f['sim']),
            mb_substr(trim($f['eg']), 0, 88));
    }
}

if ($plan['collision']) {
    echo PHP_EOL . 'ALREADY HELD, would not create (' . count($plan['collision']) . '):' . PHP_EOL;
    foreach ($plan['collision'] as $c) { echo '  ' . str_pad($c['name'], 34) . $c['type'] . ' #' . $c['id'] . PHP_EOL; }
}
if ($overrides) {
    echo PHP_EOL . 'NAME OVERRIDES, the queue spelling kept as an alias (' . count($overrides) . '):' . PHP_EOL;
    foreach ($overrides as $o) { printf("  %-34s -> %s\n", $o['from'], $o['to']); }
}
if ($plan['merge']) {
    echo PHP_EOL . 'MERGES (' . count($plan['merge']) . '):' . PHP_EOL;
    foreach ($plan['merge'] as $m) {
        printf("  %-30s -> %-38s %s%s%s\n", $m['name'], $m['into'],
            $m['via'] ?? '',
            isset($m['added']) ? ', +' . $m['added'] . ' links' : '',
            ($m['ok'] ? '' : '   TARGET MISSING') . (!empty($m['chained']) ? '   (chained)' : ''));
    }
}
if ($conflicts) {
    echo PHP_EOL . 'CONFLICTS WITH THE CANON (' . count($conflicts) . '):' . PHP_EOL;
    foreach ($conflicts as $x) { echo '  ' . $x . PHP_EOL; }
} elseif ($canon) {
    echo PHP_EOL . 'no conflicts with the canon.' . PHP_EOL;
}
if ($plan['skip']) { echo PHP_EOL . 'SKIPPED: ' . count($plan['skip']) . PHP_EOL; }
if ($plan['external']) {
    echo PHP_EOL . 'EXTERNAL, no record created (' . count($plan['external']) . '):' . PHP_EOL;
    foreach ($plan['external'] as $x) {
        printf("   %-34s %-12s %s\n", mb_substr((string)$x['name'], 0, 33), $x['type'] ?? '',
            ($x['wikidataId'] ?? '') !== '' ? $x['wikidataId'] : 'no Wikidata id yet');
    }
    $noQid = count(array_filter($plan['external'], fn($x) => ($x['wikidataId'] ?? '') === ''));
    if ($noQid) {
        echo '   ' . $noQid . ' carry no Wikidata id. The ruling stands without one, but the '
           . 'name then points at nothing.' . PHP_EOL;
    }
}

/* THE GATE.
 *
 * This ran once with five conflicts open and wrote seven wrong records: a
 * second Jill Klajic under a job she held for four years, a Lyons beside the
 * Lyons Avenue it is an alias of, a festival as a person. The conflicts were
 * printed. Printing is not refusing.
 *
 * An unresolved conflict, an unanswered containment pair or a title in a name
 * each means the file disagrees with itself or with the canon, and a batch that
 * writes 36 records while disagreeing with itself produces exactly the kind of
 * damage that takes a database restore to undo. So it stops, names what is
 * open, and writes nothing. There is no flag to override this: the way past it
 * is to settle the decisions, which is the point. */
$blockers = [];
if ($conflicts) { $blockers[] = count($conflicts) . ' unresolved conflict' . (count($conflicts) === 1 ? '' : 's'); }
if ($containment) { $blockers[] = count($containment) . ' containment pair' . (count($containment) === 1 ? '' : 's') . ' nobody has answered'; }
/* A merge pointing at nothing is the file disagreeing with itself: somebody
   decided a name belongs to a record that this run does not create and the
   archive does not hold. It writes nothing, so it is not damage, but it is a
   decision nobody has finished making. */
$dangling = array_values(array_filter($plan['merge'], fn($m) => !$m['ok']));
if ($dangling) { $blockers[] = count($dangling) . ' merge' . (count($dangling) === 1 ? '' : 's') . ' pointing at a target that does not exist'; }

if ($blockers) {
    echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
    echo 'REFUSING TO APPLY: ' . implode(', ', $blockers) . '.' . PHP_EOL;
    foreach ($conflicts as $x) { echo '  conflict   ' . $x . PHP_EOL; }
    foreach ($dangling as $m) {
        printf("  merge      %s -> %s\n", $m['name'], $m['into']);
    }
    foreach ($containment as $c) {
        printf("  pair       %s (%d links) sits inside %s (%d links); decide it on the screen\n",
            $c['short']['name'], count($c['short']['articles']),
            $c['long']['name'], count($c['long']['articles']));
    }
    echo PHP_EOL . 'Settle these and run again. Nothing was written'
       . ($APPLY ? ', although APPLY was on.' : '.') . PHP_EOL;
    return;
}

if (!$APPLY) {
    echo PHP_EOL . 'DRY RUN. Nothing was written.' . PHP_EOL;
    return;
}

/* ------------------------------------------------------------- the apply */

$made = 0; $linked = 0; $failed = [];

foreach ($plan['create'] as $c) {
    $e = new \craft\elements\Entry();
    $e->sectionId = \Craft::$app->entries->getSectionByHandle($c['section'])->id;
    foreach (\Craft::$app->entries->getSectionByHandle($c['section'])->getEntryTypes() as $et) {
        if ($et->handle === $TYPE_FOR[$c['type']]) { $e->typeId = $et->id; break; }
    }
    $e->title = $c['name'];
    $e->enabled = true;

    $set = [$PROV_FIELD => $PROVENANCE];
    if ($c['placeType'] !== '' && $hasField($e, 'placeType')) {
        $set['placeType'] = $c['placeType'];
    }
    if ($c['aliases'] && $hasField($e, $ALIAS_FOR[$c['type']])) {
        $set[$ALIAS_FOR[$c['type']]] = implode("\n", $c['aliases']);
    }
    $e->setFieldValues($set);

    if (!\Craft::$app->elements->saveElement($e)) {
        $failed[] = $c['name'] . ': ' . json_encode($e->getErrors());
        continue;
    }
    $made++;

    /* Append, never replace. */
    foreach ($c['articles'] as $aid) {
        $a = \craft\elements\Entry::find()->id($aid)->status(null)->one();
        if (!$a || !$hasField($a, $c['linkField'])) { continue; }
        $cur = array_map(fn($x) => $x->id, $a->{$c['linkField']}->all());
        if (in_array($e->id, $cur, true)) { continue; }
        $cur[] = $e->id;
        $a->setFieldValue($c['linkField'], $cur);
        if (\Craft::$app->elements->saveElement($a)) { $linked++; }
        else { $failed[] = 'link ' . $c['name'] . ' -> #' . $aid; }
    }
}

foreach ($plan['merge'] as $m) {
    /* A merge resolved to a pending creation needed no work of its own: the
       alias and the articles were folded into that creation's plan above, and
       writing them again here would be writing them twice. */
    if (!$m['ok'] || $m['intoId'] === null) { continue; }
    $t = \craft\elements\Entry::find()->id($m['intoId'])->status(null)->one();
    $type = array_search($t->section->handle, $SECTION_FOR, true);
    if ($type === false || !$hasField($t, $ALIAS_FOR[$type])) { continue; }
    $cur = trim((string)$t->getFieldValue($ALIAS_FOR[$type]));
    if (stripos($cur, $m['name']) !== false) { continue; }
    $t->setFieldValue($ALIAS_FOR[$type], trim($cur . "\n" . $m['name']));
    \Craft::$app->elements->saveElement($t);
}

/* Read back, because a save that reports success and stores nothing is the
   failure this project has hit more than once. */
$verified = 0;
foreach ($plan['create'] as $c) {
    $e = \craft\elements\Entry::find()->section($c['section'])->title($c['name'])->status(null)->one();
    if (!$e) { continue; }
    $p = $hasField($e, $PROV_FIELD) ? trim((string)$e->getFieldValue($PROV_FIELD)) : '';
    if ($p === $PROVENANCE) { $verified++; }
}

echo PHP_EOL . 'created: ' . $made . '  links written: ' . $linked
   . '  provenance verified on read-back: ' . $verified . ' of ' . $made . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('create_records_from_review.php', $made, 'provenance verified ' . $verified . ' of ' . $made,
    $linked . ' article links; source ' . $sourceNote);
foreach (array_slice($failed, 0, 20) as $f) { echo '  FAILED ' . $f . PHP_EOL; }
