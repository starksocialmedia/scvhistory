/**
 * The Smyth father and son: the family relation, the roles, and Cameron's
 * Wikidata item.
 *
 * THE RELATION IS ALREADY THERE
 *
 * Cameron #16380 already carries childOf -> Clyde #15985, and Clyde's page
 * already shows Cameron, so the inverse derivation is working. This script
 * checks it and writes it only if it is missing; it does not rewrite a correct
 * relation, because a save that changes nothing still bumps dateUpdated and
 * makes the record look touched.
 *
 * ROLES, AND WHAT THE ARTICLES ACTUALLY SAY
 *
 * Clyde: "the City Council voted last night to name Clyde Smyth our new mayor",
 * "elected to the council in 1994", "councilman Clyde Smyth", "Santa Clarita's
 * mayor and former superintendent of the William S. [Hart district]".
 *
 * Cameron: "Cameron Smyth will be elected to the Santa Clarita City Council",
 * "endorsed Frank Ferry over Cameron Smyth for City Council four years ago",
 * and "Smyth minces few words about his desire to serve in the state Assembly
 * or Senate one day".
 *
 * So Mayor is NOT written for Cameron. The brief said Mayor if the articles
 * support it and they do not: the only mayor in his articles is his father,
 * and "the mayor's son" is the phrase that puts them both in one sentence.
 * State Assemblymember is written on the strength of the Wikidata item, which
 * is an authority; the corpus has only his stated wish to run.
 *
 * Clyde's Mayor and his school superintendency are held back as PROPOSALS
 * rather than written, because the brief named City Council Member for him and
 * nothing else. The evidence is printed so the call can be made on it.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/smyth_family_and_roles.php'))"
 */

$APPLY = false;

$CLYDE = 15985;
$CAMERON = 16380;

/* Written. Role titles, resolved to the vocabulary by exact title. */
$ROLES = [
    /* Mayor and School Superintendent added on Nathan's ruling, 21 September,
       after the evidence below was put to him. They were held back on the
       first pass because the brief named City Council Member and nothing
       else. */
    $CLYDE   => ['City Council Member', 'Mayor', 'School Superintendent'],
    $CAMERON => ['City Council Member', 'State Assemblymember'],
];

/* Proposed, not written. */
$PROPOSED = [];   /* both of Clyde's were accepted and are written above */

$WIKIDATA = [
    $CAMERON => ['qid' => 'Q5026379', 'desc' => 'American politician; member of the California '
        . 'State Assembly Q18180908, 4 December 2006 to 30 November 2012; born 19 August 1971'],
];
/* The vocabulary has Mayor and City Council Member but no superintendency, so
   the term is created here rather than silently dropped. Wikidata's own item
   for the occupation, not a local coinage: the archive will hold more Hart
   district superintendents than this one. */
$NEW_ROLES = [
    'School Superintendent' => ['qid' => 'Q7643464',
        'desc' => 'administrator in charge of multiple schools, a school district or entity '
                . 'with school oversight'],
];

$NO_ITEM = [
    $CLYDE => 'no Wikidata item; a search by name returns nothing at all. A Santa Clarita '
        . 'mayor and Hart district superintendent is below Wikidata\'s threshold, and the '
        . 'archive is the source for him.',
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$clyde = \craft\elements\Entry::find()->id($CLYDE)->status(null)->one();
$cam   = \craft\elements\Entry::find()->id($CAMERON)->status(null)->one();
if (!$clyde || !$cam) { echo 'one of the records is missing' . PHP_EOL; return; }

/* ------------------------------------------------------------ the family */

$held = array_map(fn($e) => $e->id, $cam->childOf->all());
$relationOk = in_array($clyde->id, $held, true);
echo 'FAMILY' . PHP_EOL;
printf("   %-30s %s\n", 'Cameron childOf Clyde',
    $relationOk ? 'already written, left alone' : 'would write');
printf("   %-30s %s\n", 'inverse on Clyde',
    'derived at render; Cameron already shows on #' . $clyde->id);

/* ------------------------------------------------------------- the roles */

$vocab = [];
foreach (\craft\elements\Entry::find()->section('roles')->status(null)->limit(null)->all() as $r) {
    $vocab[mb_strtolower(trim((string)$r->title))] = $r;
}

echo PHP_EOL . 'VOCABULARY' . PHP_EOL;
$toCreate = [];
foreach ($NEW_ROLES as $t => $spec) {
    if (isset($vocab[mb_strtolower($t)])) {
        printf("   %-26s exists as #%d\n", $t, $vocab[mb_strtolower($t)]->id);
        continue;
    }
    printf("   %-26s would create, %s (%s)\n", $t, $spec['qid'], $spec['desc']);
    $toCreate[$t] = $spec;
}
if ($APPLY && $toCreate) {
    $sec = \Craft::$app->getEntries()->getSectionByHandle('roles');
    $et = $sec->getEntryTypes()[0];
    foreach ($toCreate as $t => $spec) {
        $e = new \craft\elements\Entry();
        $e->sectionId = $sec->id;
        $e->typeId = $et->id;
        $e->title = $t;
        $e->setFieldValue('wikidataId', $spec['qid']);
        if (\Craft::$app->elements->saveElement($e)) {
            $vocab[mb_strtolower($t)] = $e;
            echo '   created ' . $t . ' #' . $e->id . PHP_EOL;
        } else {
            echo '   FAILED creating ' . $t . ': ' . json_encode($e->getErrors()) . PHP_EOL;
        }
    }
}

echo PHP_EOL . 'ROLES' . PHP_EOL;
$plan = [];
foreach ([$CLYDE => $clyde, $CAMERON => $cam] as $id => $e) {
    $want = [];
    $missing = [];
    foreach ($ROLES[$id] as $t) {
        $r = $vocab[mb_strtolower($t)] ?? null;
        if ($r) { $want[] = $r; } else { $missing[] = $t; }
    }
    $has = array_map(fn($x) => $x->id, $e->roles->all());
    /* Union, never replace: a role set already on a record was put there by
       somebody and this brief is not a reason to drop it. */
    $ids = array_values(array_unique(array_merge($has, array_map(fn($r) => $r->id, $want))));
    $plan[$id] = ['entry' => $e, 'ids' => $ids, 'want' => $want, 'changed' => $ids != $has];
    printf("   #%-7d %-16s %s\n", $e->id, $e->title,
        implode(', ', array_map(fn($r) => $r->title . ' #' . $r->id, $want)));
    if ($missing) { printf("      NOT IN THE VOCABULARY: %s\n", implode(', ', $missing)); }
    if ($has) { printf("      already carries %d, union keeps them\n", count($has)); }
}

echo PHP_EOL . 'NOT WRITTEN, for the reason given' . PHP_EOL;
printf("   %-24s %s\n", 'Cameron / Mayor',
    'the articles do not support it; the only mayor in them is his father');
foreach ($PROPOSED as $id => $set) {
    foreach ($set as $t => $why) {
        printf("   %-24s PROPOSED: %s\n", 'Clyde / ' . $t, $why);
    }
}

/* ---------------------------------------------------------- the identity */

echo PHP_EOL . 'WIKIDATA' . PHP_EOL;
foreach ($WIKIDATA as $id => $w) {
    $e = $id === $CLYDE ? $clyde : $cam;
    $cur = trim((string)$e->wikidataId);
    printf("   #%-7d %-16s %-12s %s\n", $e->id, $e->title, $w['qid'],
        $cur === '' ? 'was empty' : ($cur === $w['qid'] ? 'already set' : 'WAS ' . $cur));
    printf("      %s\n", $w['desc']);
}
foreach ($NO_ITEM as $id => $why) {
    $e = $id === $CLYDE ? $clyde : $cam;
    printf("   #%-7d %-16s %-12s %s\n", $e->id, $e->title, '(none)', 'left empty');
    printf("      %s\n", $why);
}

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* A relation field handed a bare integer saves "ok" and stores nothing; it
   wants an array. */
if (!$relationOk) {
    $cam->setFieldValue('childOf', array_values(array_unique(array_merge($held, [$clyde->id]))));
    if (!\Craft::$app->elements->saveElement($cam)) {
        echo 'FAILED childOf: ' . json_encode($cam->getErrors()) . PHP_EOL;
    }
}

$wrote = 0;
foreach ($plan as $p) {
    if (!$p['changed']) { continue; }
    $e = \craft\elements\Entry::find()->id($p['entry']->id)->status(null)->one();
    $e->setFieldValue('roles', $p['ids']);
    if (\Craft::$app->elements->saveElement($e)) { $wrote++; }
    else { echo 'FAILED roles #' . $e->id . ': ' . json_encode($e->getErrors()) . PHP_EOL; }
}
foreach ($WIKIDATA as $id => $w) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    $e->setFieldValue('wikidataId', $w['qid']);
    if (!\Craft::$app->elements->saveElement($e)) {
        echo 'FAILED wikidataId #' . $id . ': ' . json_encode($e->getErrors()) . PHP_EOL;
    }
}

/* Read back from fresh queries. */
$bCam = \craft\elements\Entry::find()->id($CAMERON)->status(null)->one();
$bCly = \craft\elements\Entry::find()->id($CLYDE)->status(null)->one();
$relBack = in_array($CLYDE, array_map(fn($x) => $x->id, $bCam->childOf->all()), true);
$rCly = count($bCly->roles->all());
$rCam = count($bCam->roles->all());
$qid = trim((string)$bCam->wikidataId);

echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-26s %-18s %s\n", 'Cameron childOf Clyde', $relBack ? 'yes' : 'no', $relBack ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'Clyde roles', (string)$rCly, $rCly >= count($ROLES[$CLYDE]) ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'Cameron roles', (string)$rCam, $rCam >= count($ROLES[$CAMERON]) ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'Cameron wikidataId', $qid !== '' ? $qid : '(empty)',
    $qid === $WIKIDATA[$CAMERON]['qid'] ? 'pass' : 'FAIL');

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('smyth_family_and_roles.php', $wrote + 1,
    ($relBack && $rCly && $rCam && $qid === $WIKIDATA[$CAMERON]['qid'] ? 'verified: ' : 'FAILED: ')
        . 'relation ' . ($relBack ? 'ok' : 'no') . ', roles ' . $rCly . '/' . $rCam . ', qid ' . ($qid ?: 'none'),
    'Clyde has no Wikidata item; Mayor not written for Cameron');
