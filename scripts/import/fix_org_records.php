/**
 * The organization fixes Nathan approved on 25 September, from the audit.
 *
 *   1. #18862 "California Petroleum Company" is a mangled name. Three sources
 *      say "Philadelphia and California Petroleum Company" (Perkins twice,
 *      as "Philadelphia &", and Reynolds chapter 37). Reynolds chapter 29
 *      says "Pennsylvania and California Petroleum Company"; that is not made
 *      an alias, because an alias would assert the two are one firm.
 *   2. orgType on the eleven that have none and are organizations.
 *   3. Harvey Stack #18806 is a person filed as an organization, a New York
 *      coin dealer with no valley connection. Deleted (soft: restorable from
 *      the CP trash). Three of his five articles are about the firm, Stack's,
 *      so Stack's is created as a business and those three point at it. The
 *      other two are about Harvey the man and lose the subject.
 *   4. The three pairs that need both records, linked through placeOrganizations
 *      on the place: Pioneer Oil Refinery and California Star Oil Works (the
 *      refinery becomes a place in convert_orgs_to_places.php; run that first),
 *      Downtown Newhall and its merchants association, Heritage Junction and
 *      the Santa Clarita Valley Historical Society.
 *   5. (added after the first apply) Unlink the 1969 Golden State Memorial
 *      Hospital advertisement, photograph #5683, from Henry Mayo #380.
 *
 * Only empty fields are set; a relation is added, never replaced. Idempotent.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_org_records.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$RENAME = [18862 => ['title' => 'Philadelphia and California Petroleum Company',
    'aliases' => 'California Petroleum Company, Philadelphia & California Petroleum Company']];

/* id => [orgType, schoolLevel or null, why] */
$TYPES = [
    16039 => ['business',   null,      'California Star Oil Works'],
    16101 => ['business',   null,      'Southern Pacific Railroad'],
    15461 => ['business',   null,      'Standard Oil Company'],
    18855 => ['business',   null,      'Union Oil Company'],
    16217 => ['business',   null,      'Heritage Auction Galleries'],
    18519 => ['business',   null,      'Valencia National Bank'],
    16052 => ['school',     'high',    'William S. Hart High School'],
    16347 => ['school',     'college', 'California State University, Northridge'],
    16339 => ['club',       null,      'Downtown Newhall Merchants Association: a merchants\' association; no type fits exactly'],
    16290 => ['government', null,      'Newhall Redevelopment Committee: formed by the city'],
    402   => ['government', null,      'Santa Clarita Valley Water: a public water agency'],
];

$STACK = 18806;
$STACKS_ARTICLES = [15444, 15076, 15450];   /* about the firm; 15166 and 14944 are about Harvey */

/* place id or places slug => organization id */
$PAIRS = [
    ['pioneer-oil-refinery', 16039],
    [15949, 16339],
    [605, 15493],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$get = fn($id) => \craft\elements\Entry::find()->id($id)->status(null)->one();
$ops = [];     /* [description, closure that writes, closure that verifies] */
$skipped = [];

/* 1 */
foreach ($RENAME as $id => $r) {
    $e = $get($id);
    if ($e->title === $r['title']) { $skipped[] = "#$id already renamed"; continue; }
    echo 'rename #' . $id . ' "' . $e->title . '" -> "' . $r['title'] . '"; aliases: ' . $r['aliases'] . PHP_EOL;
    $ops[] = [function () use ($get, $id, $r, $elements) {
        $e = $get($id); $e->title = $r['title'];
        $have = trim((string)$e->orgAliases);
        $e->setFieldValue('orgAliases', $have ? $have . ', ' . $r['aliases'] : $r['aliases']);
        $e->setFieldValue('orgType', 'business');
        return $elements->saveElement($e);
    }, fn() => ($t = $get($id)->title) === $r['title'] ? '' : "#$id title reads \"$t\", expected \"{$r['title']}\"", "rename #$id"];
}

/* 2 */
foreach ($TYPES as $id => [$type, $level, $why]) {
    $e = $get($id);
    if (!$e) { $skipped[] = "#$id not found"; continue; }
    $cur = (string)$e->orgType->value;
    if ($cur !== '') { $skipped[] = "#$id already typed $cur"; continue; }
    echo 'orgType #' . str_pad($id, 6) . str_pad($type . ($level ? '/' . $level : ''), 18) . $why . PHP_EOL;
    $ops[] = [function () use ($get, $id, $type, $level, $elements) {
        $e = $get($id); $e->setFieldValue('orgType', $type);
        if ($level && $e->getFieldLayout()->getFieldByHandle('schoolLevel') && !(string)$e->schoolLevel->value) { $e->setFieldValue('schoolLevel', $level); }
        return $elements->saveElement($e);
    }, fn() => ($t = (string)$get($id)->orgType->value) === $type ? '' : "#$id orgType reads \"$t\", expected \"$type\"", "orgType #$id"];
}

/* 3 */
$harvey = $get($STACK);
if ($harvey) {
    $arts = \craft\elements\Entry::find()->status(null)->relatedTo(['targetElement' => $harvey, 'field' => 'subjectOrganization'])->all();
    $stacks = \craft\elements\Entry::find()->section('organizations')->status(null)->title("Stack's")->one();
    echo PHP_EOL . 'Harvey Stack #' . $STACK . ': ' . count($arts) . ' article(s) point at him; delete (soft)' . PHP_EOL;
    echo '   ' . ($stacks ? "Stack's exists, #" . $stacks->id : "create Stack's, business, New York coin dealer founded 1933 by Morton and Joseph Stack") . PHP_EOL;
    foreach ($arts as $a) {
        echo '   #' . $a->id . ' ' . $a->title . ' -> ' . (in_array($a->id, $STACKS_ARTICLES, true) ? "Stack's" : 'no subject: about Harvey the man') . PHP_EOL;
    }
    $ops[] = [function () use ($get, $STACK, $STACKS_ARTICLES, $elements, $svc, $arts) {
        $s = \craft\elements\Entry::find()->section('organizations')->status(null)->title("Stack's")->one();
        if (!$s) {
            $s = new \craft\elements\Entry();
            $s->sectionId = $svc->getSectionByHandle('organizations')->id;
            $s->setTypeId($svc->getEntryTypeByHandle('organization')->id);
            $s->title = "Stack's";
            $s->setFieldValues([
                'orgType' => 'business',
                'orgAliases' => "Stack's Rare Coins, Stack Brothers",
                'dateFoundedEdtf' => '1933',
                'dateFounded' => '1933',
                'recordProvenance' => 'fix_org_records.php, 2026-09-25: replaces the organization record wrongly made for Harvey Stack; founding from record #15076, "its founding in 1933 by brothers Morton and Joseph Stack"',
            ]);
            if (!$elements->saveElement($s)) { return false; }
        }
        foreach ($arts as $a) {
            $a = $get($a->id);
            $ids = array_values(array_diff($a->subjectOrganization->status(null)->ids(), [$STACK]));
            if (in_array($a->id, $STACKS_ARTICLES, true)) { $ids[] = $s->id; }
            $a->setFieldValue('subjectOrganization', array_values(array_unique($ids)));
            if (!$elements->saveElement($a)) { return false; }
        }
        return $elements->deleteElement($get($STACK));
    }, function () use ($STACK, $STACKS_ARTICLES) {
        $out = [];
        if (\craft\elements\Entry::find()->id($STACK)->status(null)->exists()) { $out[] = "Harvey Stack #$STACK is still live"; }
        $s = \craft\elements\Entry::find()->section('organizations')->status(null)->title("Stack's")->one();
        if (!$s) { return "no organization titled Stack's"; }
        /* count() comes back from MySQL as a string: "3" === 3 is false. This
           comparison failed the 03:51 apply with every write in place. */
        $got = \craft\elements\Entry::find()->status(null)->id($STACKS_ARTICLES)->relatedTo(['targetElement' => $s, 'field' => 'subjectOrganization'])->ids();
        $missing = array_diff($STACKS_ARTICLES, array_map('intval', $got));
        if ($missing) { $out[] = "Stack's #{$s->id} is missing articles #" . implode(', #', $missing) . ' (holds ' . count($got) . ' of ' . count($STACKS_ARTICLES) . ')'; }
        $left = (new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['e' => '{{%elements}}'], 'e.id = r.sourceId')
            ->where(['r.targetId' => $STACK, 'e.revisionId' => null, 'e.draftId' => null])->count();
        if ((int)$left) { $out[] = "$left relation(s) still point at Harvey Stack #$STACK"; }
        return implode('; ', $out);
    }, 'Harvey Stack deleted, Stack\'s holds 3 articles'];
} else { $skipped[] = "Harvey Stack #$STACK already gone"; }

/* 4 */
echo PHP_EOL;
foreach ($PAIRS as [$placeRef, $orgId]) {
    $place = is_int($placeRef) ? $get($placeRef) : \craft\elements\Entry::find()->section('places')->status(null)->slug($placeRef)->one();
    $org = $get($orgId);
    if (!$place) { echo 'pair WAITING: place ' . $placeRef . ' does not exist yet (run convert_orgs_to_places.php) <-> #' . $orgId . ' ' . $org->title . PHP_EOL; continue; }
    if (in_array($orgId, $place->placeOrganizations->status(null)->ids(), true)) { $skipped[] = "pair #{$place->id} <-> #$orgId already linked"; continue; }
    echo 'pair place #' . $place->id . ' ' . $place->title . '  <->  #' . $orgId . ' ' . $org->title . PHP_EOL;
    $pid = $place->id;
    $ops[] = [function () use ($get, $pid, $orgId, $elements) {
        $p = $get($pid);
        $p->setFieldValue('placeOrganizations', array_values(array_unique(array_merge($p->placeOrganizations->status(null)->ids(), [$orgId]))));
        return $elements->saveElement($p);
    }, fn() => in_array($orgId, array_map('intval', $get($pid)->placeOrganizations->status(null)->ids()), true) ? '' : "place #$pid placeOrganizations reads " . json_encode($get($pid)->placeOrganizations->status(null)->ids()) . ", expected it to include #$orgId", "pair #$pid <-> #$orgId"];
}

/* 5. Photograph #5683 (lw6901) is a 1969 advertisement for Golden State
   Memorial Hospital, linked to Henry Mayo Newhall Memorial Hospital #380, which
   was founded in 1975. Nothing in the archive makes the one the other's
   predecessor: a false connection. Nathan, 25 September: unlink. The photograph
   stays. */
$AD = 5683; $HOSPITAL = 380;
$ad = $get($AD);
if ($ad && in_array($HOSPITAL, array_map('intval', $ad->photoOrganizations->status(null)->ids()), true)) {
    echo PHP_EOL . 'unlink photograph #' . $AD . ' "' . $ad->title . '" from #' . $HOSPITAL . ' (photoOrganizations)' . PHP_EOL;
    $ops[] = [function () use ($get, $AD, $HOSPITAL, $elements) {
        $p = $get($AD);
        $p->setFieldValue('photoOrganizations', array_values(array_diff(array_map('intval', $p->photoOrganizations->status(null)->ids()), [$HOSPITAL])));
        return $elements->saveElement($p);
    }, fn() => in_array($HOSPITAL, array_map('intval', $get($AD)->photoOrganizations->status(null)->ids()), true)
        ? "photograph #$AD photoOrganizations still includes #$HOSPITAL" : '', "unlink #$AD from #$HOSPITAL"];
} else { $skipped[] = "photograph #$AD is not linked to #$HOSPITAL"; }

foreach ($skipped as $s) { echo 'skip ' . $s . PHP_EOL; }
echo PHP_EOL . count($ops) . ' operation(s)' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$short = [];
foreach ($ops as [$write, $check, $what]) {
    if (!$write()) { $short[] = "$what: save failed"; continue; }
    /* A check returns '' when the write holds, or says what it found and what
       it expected. "reads back short" alone is the fault the mirror import
       had; never again. */
    if (($why = $check()) !== '') { $short[] = "$what: $why"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT' : 'OK') . ': ' . (count($ops) - count($short)) . ' of ' . count($ops) . PHP_EOL;
foreach ($short as $s) { echo '   FAIL ' . $s . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_org_records.php', count($ops), $short ? 'SHORT: ' . implode('; ', $short) : 'verified ' . count($ops) . ' of ' . count($ops),
    'rename #18862; orgType x' . count($TYPES) . "; Harvey Stack deleted, Stack's created; org-place pairs linked");
if ($short) { throw new \RuntimeException('fix_org_records: read-back failed: ' . implode('; ', $short)); }
