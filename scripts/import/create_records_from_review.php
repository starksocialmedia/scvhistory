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
    $byKey = [];
    if (file_exists($queue)) {
        foreach ((json_decode(file_get_contents($queue), true)['names'] ?? []) as $r) { $byKey[$r['key']] = $r; }
    }
    foreach ($d['decisions'] ?? [] as $x) {
        $q = $byKey[$x['key']] ?? [];
        $x['variants'] = $q['variants'] ?? [];
        $x['confidence'] = $q['confidence'] ?? '';
        $x['context'] = $q['context'] ?? [];
        $x['signals'] = $q['signals'] ?? [];
        if (!isset($x['placeType'])) { $x['placeType'] = $q['placeType'] ?? ''; }
        $decisions[] = $x;
    }
    $sourceNote = basename($decided) . ', decided ' . ($d['generated'] ?? '?');
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

$plan = ['create' => [], 'merge' => [], 'skip' => [], 'collision' => []];
$linkTotal = 0; $articlesTouched = [];

foreach ($decisions as $x) {
    $act  = (string)($x['action'] ?? '');
    $name = trim((string)($x['name'] ?? ''));
    $type = (string)($x['type'] ?? '');
    if ($name === '' || !isset($SECTION_FOR[$type])) { continue; }

    if ($act === 'skipped') { $plan['skip'][] = $x; continue; }

    if ($act === 'merged') {
        $into = \craft\elements\Entry::find()->id((int)($x['into'] ?? 0))->status(null)->one();
        $plan['merge'][] = ['name' => $name, 'into' => $into ? $into->title : '(missing #' . ($x['into'] ?? '?') . ')',
                            'intoId' => $x['into'] ?? null, 'ok' => (bool)$into];
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

    $plan['create'][] = [
        'name' => $name, 'type' => $type, 'section' => $SECTION_FOR[$type],
        'aliases' => array_values(array_filter((array)($x['variants'] ?? []))),
        'articles' => $ids, 'confidence' => (string)($x['confidence'] ?? ''),
        'signals' => (array)($x['signals'] ?? []),
        'context' => (array)($x['context'] ?? []),
        'placeType' => $type === 'place' ? (string)($x['placeType'] ?? '') : '',
        'linkField' => $LINK_FOR[$type],
    ];
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
$containment = [];
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
        $containment[] = [
            'short' => $sub === 'a' ? $a : $b,
            'long'  => $sub === 'a' ? $b : $a,
        ];
    }
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
if ($plan['merge']) {
    echo PHP_EOL . 'ALIAS ONTO AN EXISTING RECORD (' . count($plan['merge']) . '):' . PHP_EOL;
    foreach ($plan['merge'] as $m) { echo '  ' . str_pad($m['name'], 34) . '-> ' . $m['into'] . ($m['ok'] ? '' : '  TARGET MISSING') . PHP_EOL; }
}
if ($plan['skip']) { echo PHP_EOL . 'SKIPPED: ' . count($plan['skip']) . PHP_EOL; }

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
    if (!$m['ok']) { continue; }
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
foreach (array_slice($failed, 0, 20) as $f) { echo '  FAILED ' . $f . PHP_EOL; }
