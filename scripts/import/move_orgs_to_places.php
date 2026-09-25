/**
 * Moves the ranchos and the missions out of organizations and into places.
 *
 * Three ranchos and three missions sit in the organizations section because the
 * extraction read them as bodies. In a legal sense they were: a rancho was a
 * land grant to a person and a mission was a religious corporation holding
 * property. In the archive's sense they are not. A reader looking for Rancho
 * San Francisco wants the land, its boundaries and what happened on it, and
 * finds it filed beside the Chamber of Commerce.
 *
 * The canon already rules on this: ^Rancho and ^Mission are place. This moves
 * the records that predate the rule.
 *
 * HOW A MOVE WORKS, AND WHY NOT AN EDIT
 *
 * Craft cannot change an entry's section. So a place is created carrying
 * everything the organization held, every relation pointing at the organization
 * is repointed at the place, and the organization is left in place for a person
 * to delete in the control panel.
 *
 * It is NOT deleted here. Deleting the old record would take its id out of the
 * database, and that id is in the review decisions, in the image links, in
 * anything that referenced it during the import. Listing it for a person to
 * remove, once they have looked at the new one, is slower and recoverable.
 *
 * FIELD MAPPING
 *
 * Organization and place do not carry the same fields, so this is a translation
 * rather than a copy, and the ones that do not translate are reported rather
 * than dropped silently:
 *
 *   orgAliases     -> placeAliases
 *   orgLat, orgLng -> placeLat, placeLng
 *   dateFounded    -> dateEstablished
 *   orgAddress     -> placeAddress
 *   orgLegacyUrl   -> placeLegacyUrl
 *   orgWikipediaUrl-> placeWikipediaUrl
 *   ein, ncesId, cdsCode, orgType, schoolLevel, parentOrganization
 *                  -> nothing. A place has no EIN. Reported if a value exists.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/move_orgs_to_places.php'))"
 */

$APPLY = false;

/* id => the placeType it should carry. Ranchos are ranches; the missions are
   sites, which is what the canon rule says and what they are to a visitor. */
$MOVE = [
    382 => 'ranch',   /* Rancho San Francisco */
    384 => 'ranch',   /* Rancho Camulos */
    388 => 'ranch',   /* Rancho El Tejon */
    386 => 'site',    /* Mission San Gabriel Arcangel */
    398 => 'site',    /* Mission San Francisco de Asis */
    400 => 'site',    /* Mission Santa Cruz */
];

$SAME = ['body', 'recordProvenance', 'wikidataId', 'legacyKey', 'legacyUrl', 'sourcePath',
         'legacyHtml', 'legacyCategory', 'culturalSensitivityNote', 'webmasterNoteTop',
         'webmasterNoteBottom', 'editorNotes', 'footnotes'];

$RENAME = [
    'orgAliases' => 'placeAliases',
    'orgLat' => 'placeLat',
    'orgLng' => 'placeLng',
    'dateFounded' => 'dateEstablished',
    'orgAddress' => 'placeAddress',
    'orgLegacyUrl' => 'placeLegacyUrl',
    'orgWikipediaUrl' => 'placeWikipediaUrl',
];

/* Relations pointing AT an organization, by the field that holds them. */
$INBOUND = ['subjectOrganization', 'publishedBy', 'personOrganizations', 'orgAssociatedPersons',
            /* subBoards was retired on 25 September 2026: it was the inverse of
               parentOrganization kept in a second place, and sub-bodies are read
               from the nesting now. */
            'placeOrganizations', 'photoOrganizations', 'parentOrganization',
            'orgFoundedBy', 'derivedImageLinks', 'relatedArticles'];

/* Where a place takes the same relation, so the repoint has somewhere to go. */
$INBOUND_PLACE = ['subjectOrganization' => 'depictsPlace', 'photoOrganizations' => 'photoPlaces',
                  'placeOrganizations' => 'relatedPlaces',
                  'derivedImageLinks' => 'derivedImageLinks', 'relatedArticles' => 'relatedArticles'];

/* personOrganizations is a person saying "I belong to this body". A place has
   no such field, so the first version of this dropped ten of them: Ygnacio del
   Valle's connection to Rancho San Francisco, Beale's to Rancho El Tejon,
   Garces's to Mission San Gabriel. Those are the facts the records exist to
   carry, and losing them is the move failing at the only thing it is for.

   The place holds the reverse instead. placePeople is the place naming its
   people, which says the same thing from the other end and is where a reader
   looking at the rancho expects to find them. */
$REVERSE = ['personOrganizations' => 'placePeople', 'orgAssociatedPersons' => 'placePeople'];

$hasField = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

$svc = Craft::$app->getEntries();
$placeSection = $svc->getSectionByHandle('places');
$placeType = null;
foreach ($placeSection->getEntryTypes() as $et) { if ($et->handle === 'place') { $placeType = $et; } }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$plan = []; $untranslatable = [];

foreach ($MOVE as $id => $pt) {
    $org = \craft\elements\Entry::find()->id($id)->status(null)->one();
    if (!$org) { echo 'MISSING #' . $id . PHP_EOL; continue; }

    $exists = \craft\elements\Entry::find()->section('places')->title($org->title)->status(null)->one();

    $vals = ['placeType' => $pt];
    foreach ($SAME as $h) {
        if ($hasField($org, $h)) {
            $v = $org->getFieldValue($h);
            if (is_string($v) && trim($v) !== '') { $vals[$h] = $v; }
        }
    }
    foreach ($RENAME as $from => $to) {
        if (!$hasField($org, $from)) { continue; }
        $v = $org->getFieldValue($from);
        if (is_string($v) && trim($v) !== '') { $vals[$to] = $v; }
        elseif (is_numeric($v)) { $vals[$to] = $v; }
    }

    /* Anything with a value and nowhere to go. */
    $lost = [];
    foreach (['ein', 'ncesId', 'cdsCode', 'orgType', 'schoolLevel'] as $h) {
        if (!$hasField($org, $h)) { continue; }
        $v = $org->getFieldValue($h);
        $v = is_object($v) ? ($v->value ?? '') : (string)$v;
        if (trim((string)$v) !== '') { $lost[] = $h . '=' . $v; }
    }
    if ($hasField($org, 'parentOrganization') && $org->parentOrganization->one()) {
        $lost[] = 'parentOrganization=' . $org->parentOrganization->one()->title;
    }
    if ($lost) { $untranslatable[$org->title] = $lost; }

    /* Who points at it. */
    $refs = [];
    foreach ($INBOUND as $fh) {
        foreach (\craft\elements\Entry::find()->relatedTo(['targetElement' => $org, 'field' => $fh])
                     ->status(null)->limit(null)->all() as $src) {
            if (isset($REVERSE[$fh])) {
                $refs[] = ['entry' => $src, 'field' => $fh, 'to' => $REVERSE[$fh],
                           'reverse' => true, 'ok' => true];
                continue;
            }
            $refs[] = ['entry' => $src, 'field' => $fh,
                       'to' => $INBOUND_PLACE[$fh] ?? null, 'reverse' => false,
                       'ok' => isset($INBOUND_PLACE[$fh]) && $hasField($src, $INBOUND_PLACE[$fh])];
        }
    }

    $plan[] = ['org' => $org, 'placeType' => $pt, 'vals' => $vals, 'refs' => $refs, 'exists' => $exists];
}

foreach ($plan as $p) {
    printf("%-34s #%-6d -> places/%s%s\n", $p['org']->title, $p['org']->id, $p['placeType'],
        $p['exists'] ? '   ALREADY A PLACE #' . $p['exists']->id : '');
    printf("   carries: %s\n", implode(', ', array_keys($p['vals'])));
    if ($p['refs']) {
        $byField = [];
        foreach ($p['refs'] as $r) {
            $arrow = $r['ok'] ? ' -> ' . $r['to'] . (!empty($r['reverse']) ? ' (held on the place)' : '') : ' -> NOWHERE';
            $byField[$r['field'] . $arrow][] = $r['entry']->title;
        }
        foreach ($byField as $k => $list) {
            printf("   %-44s %d: %s\n", $k, count($list), mb_substr(implode('; ', array_slice($list, 0, 3)), 0, 60));
        }
    } else {
        echo '   nothing points at it' . PHP_EOL;
    }
    echo PHP_EOL;
}

if ($untranslatable) {
    echo 'VALUES A PLACE CANNOT HOLD, which would be lost:' . PHP_EOL;
    foreach ($untranslatable as $t => $l) { printf("   %-34s %s\n", $t, implode(', ', $l)); }
    echo PHP_EOL;
}

$dead = array_values(array_filter($plan, fn($p) => (bool)array_filter($p['refs'], fn($r) => !$r['ok'])));
if ($dead) {
    echo 'RELATIONS WITH NOWHERE TO GO. A place has no equivalent field, so these' . PHP_EOL;
    echo 'would be dropped by the move and want a decision first:' . PHP_EOL;
    foreach ($dead as $p) {
        foreach ($p['refs'] as $r) {
            if ($r['ok']) { continue; }
            printf("   %-30s %-22s from %s\n", $p['org']->title, $r['field'], $r['entry']->title);
        }
    }
    echo PHP_EOL;
}

echo str_repeat('-', 78) . PHP_EOL;
echo 'would create: ' . count(array_filter($plan, fn($p) => !$p['exists'])) . ' places' . PHP_EOL;
echo 'relations to repoint: ' . array_sum(array_map(fn($p) => count(array_filter($p['refs'], fn($r) => $r['ok'])), $plan)) . PHP_EOL;
echo PHP_EOL . 'FOR DELETION IN THE CONTROL PANEL, once the new records are checked:' . PHP_EOL;
foreach ($plan as $p) { printf("   #%-6d %s\n", $p['org']->id, $p['org']->title); }

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$made = 0; $moved = 0;
foreach ($plan as $p) {
    /* A place of this name may already exist from the review batches. Then
       there is nothing to create and everything still to repoint: skipping
       outright would leave the relations on an organization record about to be
       deleted, which is how a move loses what it was moving. */
    $e = $p['exists'];
    if ($e) {
        echo 'place exists for ' . $p['org']->title . ' (#' . $e->id . '), repointing' . PHP_EOL;
        /* And correcting its type. Rancho Camulos already existed as a place
           carrying placeType site, set from the GNIS feature class Populated
           Place. The canon rule ^Rancho says ranch and is a judgement about
           what the archive means; the gazetteer's class is a default for when
           nobody has judged. The first version repointed and left the type
           alone, so the run verified 5 of 6 and said so. */
        $cur = $e->placeType?->value ?: '';
        if ($cur !== $p['placeType']) {
            echo '   placeType ' . ($cur ?: '(empty)') . ' -> ' . $p['placeType'] . ', the canon rule over the GNIS default' . PHP_EOL;
            $e->setFieldValue('placeType', $p['placeType']);
            \Craft::$app->elements->saveElement($e);
        }
    } else {
    $e = new \craft\elements\Entry();
    $e->sectionId = $placeSection->id;
    $e->typeId = $placeType->id;
    $e->title = $p['org']->title;
    $e->enabled = true;
    $e->setFieldValues($p['vals']);
    if (!\Craft::$app->elements->saveElement($e)) {
        echo 'FAILED ' . $p['org']->title . ': ' . json_encode($e->getErrors()) . PHP_EOL;
        continue;
    }
    $made++;
    }
    foreach ($p['refs'] as $r) {
        if (!$r['ok']) { continue; }
        $src = $r['entry'];
        if (!empty($r['reverse'])) {
            /* Written on the PLACE, naming the person, then removed from the
               person's organization list. */
            if ($hasField($e, $r['to'])) {
                $cur = array_map(fn($x) => $x->id, $e->{$r['to']}->all());
                if (!in_array($src->id, $cur, true)) { $cur[] = $src->id; }
                $e->setFieldValue($r['to'], $cur);
                \Craft::$app->elements->saveElement($e);
            }
            $old = array_values(array_filter(array_map(fn($x) => $x->id, $src->{$r['field']}->all()),
                fn($i) => $i !== $p['org']->id));
            $src->setFieldValue($r['field'], $old);
            if (\Craft::$app->elements->saveElement($src)) { $moved++; }
            continue;
        }
        $cur = array_map(fn($x) => $x->id, $src->{$r['to']}->all());
        if (!in_array($e->id, $cur, true)) { $cur[] = $e->id; }
        $src->setFieldValue($r['to'], $cur);
        /* And drop it from the organization field it came from. */
        $old = array_values(array_filter(array_map(fn($x) => $x->id, $src->{$r['field']}->all()),
            fn($i) => $i !== $p['org']->id));
        $src->setFieldValue($r['field'], $old);
        if (\Craft::$app->elements->saveElement($src)) { $moved++; }
    }
}

$verified = 0;
foreach ($plan as $p) {
    $e = \craft\elements\Entry::find()->section('places')->title($p['org']->title)->status(null)->one();
    if ($e && ($e->placeType->value ?? '') === $p['placeType']) { $verified++; }
}
echo PHP_EOL . 'created: ' . $made . '  relations repointed: ' . $moved
   . '  verified: ' . $verified . ' of ' . count($plan) . PHP_EOL;
if ($verified < count($plan)) { echo 'READ-BACK SHORT. Treat this run as failed.' . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('move_orgs_to_places.php', $made,
    ($verified < count($plan) ? 'FAILED: ' : '') . 'verified ' . $verified . ' of ' . count($plan),
    $moved . ' relations repointed');
