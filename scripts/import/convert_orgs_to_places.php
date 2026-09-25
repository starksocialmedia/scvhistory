/**
 * Organizations that are places, moved to places; and the duplicates the first
 * move left behind, retired. Replaces move_orgs_to_places.php, which is spent.
 *
 * NATHAN'S RULING, 25 SEPTEMBER. An organization acts; a place is located.
 *   To places: Acton Hotel, Southern Hotel, Pioneer Oil Refinery, Porta Bella,
 *   Valencia Marketplace, Felton School (into the existing place #2540).
 *   Retired, their data carried first: Rancho San Francisco, Rancho Camulos,
 *   Rancho El Tejon, Mission San Gabriel, Mission San Francisco de Asís,
 *   Mission Santa Cruz, into the places move_orgs_to_places.php made.
 *
 * WHAT move_orgs_to_places.php GOT WRONG, AND THIS DOES NOT
 *   - It copied nothing into a place that already existed, which is why place
 *     #16446 Rancho San Francisco has an empty body while the organization
 *     holds 1,949 characters. This carries every field that has somewhere to
 *     go: scalars into empty fields, relations merged, table rows appended.
 *   - It predated the EDTF, image, era and community fields. All carried.
 *   - It read only the relations pointing AT the organization, never its own:
 *     orgFoundedBy was lost. Outgoing relations are carried here too.
 *   - It never retired the organization, so six duplicates are still public.
 *     This disables the organization, and only when the place has everything
 *     and every relation re-pointed saved.
 *
 * A FIELD THAT DISAGREES IS NOT OVERWRITTEN. Where the place already holds a
 * different value, the place's value stays and the conflict is printed. A
 * field with nowhere to go on a place (orgWebsite, orgType, ein) is printed,
 * never silently dropped. The place layout has no founder field, so a founder
 * goes to placePeople, and the dry run says so.
 *
 * Disabling is reversible from the CP; nothing is deleted. The organization's
 * URL goes 404 when it is disabled: see the report for the redirect list.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/convert_orgs_to_places.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* org id => [target place id or null to create, placeType for a new place] */
$MOVES = [
    18876 => [null, 'building'],   /* Acton Hotel */
    16260 => [null, 'building'],   /* Southern Hotel */
    16201 => [null, 'building'],   /* Pioneer Oil Refinery */
    18599 => [null, 'site'],       /* Porta Bella: a development on a site */
    16425 => [null, 'site'],       /* Valencia Marketplace: a shopping centre */
    18827 => [2540, null],         /* Felton School */
    382   => [16446, null],        /* Rancho San Francisco */
    /* 384 Rancho Camulos: carried on the 03:51 run and held live. Nathan, 25
       September: two different bodies about one subject is a merge for a
       human read, not a script. Out of this script; see TODO.md. */
    388   => [16506, null],        /* Rancho El Tejon */
    386   => [16511, null],        /* Mission San Gabriel Arcángel */
    398   => [16515, null],        /* Mission San Francisco de Asís */
    400   => [16517, null],        /* Mission Santa Cruz */
];

/* organization field => place field. Everything else on the organization is
   checked and, if it holds a value, reported as having nowhere to go. */
$SCALAR = [
    'body' => 'body', 'webmasterNoteTop' => 'webmasterNoteTop', 'webmasterNoteBottom' => 'webmasterNoteBottom',
    'dateFounded' => 'dateEstablished', 'dateFoundedEdtf' => 'dateEstablishedEdtf',
    'orgAddress' => 'placeAddress', 'orgLat' => 'placeLat', 'orgLng' => 'placeLng',
    'orgLegacyUrl' => 'placeLegacyUrl', 'wikidataId' => 'wikidataId',
    'hauntedStatus' => 'hauntedStatus', 'hauntedAccount' => 'hauntedAccount', 'hauntedSource' => 'hauntedSource',
    'culturalSensitivityNote' => 'culturalSensitivityNote',
    'legacyKey' => 'legacyKey', 'legacyUrl' => 'legacyUrl', 'sourcePath' => 'sourcePath',
    'legacyHtml' => 'legacyHtml', 'legacyCategory' => 'legacyCategory',
];
$LINK = ['orgWikipediaUrl' => 'placeWikipediaUrl', 'orgWebsite' => 'placeWebsite'];   /* placeWebsite: add_place_website_field.php */
$ALIAS = ['orgAliases' => 'placeAliases'];
$REL = [
    'featuredImage' => 'featuredImage', 'recordImages' => 'recordImages', 'recordDocuments' => 'recordDocuments',
    'bandImage' => 'bandImage', 'recordTags' => 'recordTags', 'historicalEra' => 'historicalEra',
    'historicalPeriod' => 'historicalPeriod', 'neighborhood' => 'neighborhood',
    'orgFoundedBy' => 'placePeople', 'orgAssociatedPersons' => 'placePeople',
    'orgEvents' => 'placeEvents', 'derivedImageLinks' => 'derivedImageLinks', 'footnotesOn' => 'footnotesOn',
];
$TABLE = ['footnotes' => 'footnotes', 'editorNotes' => 'editorNotes', 'recordDates' => 'recordDates'];
$SINGLE = ['featuredImage', 'bandImage'];   /* one-asset fields: fill only if empty */
$IGNORE = ['orgType', 'schoolLevel', 'recordProvenance', 'hasParentOrg'];

/* Stored values known to be broken, carried corrected. #382's orgAliases is two
   names run together with no separator. */
$FIX = [382 => ['orgAliases' => 'Rancho de San Francisco, Rancho San Francisco Xavier']];

/* A relation pointing AT the organization, by field => [the field it becomes on
   that same source record, or '@placePeople' to be reversed onto the place]. */
$INBOUND = [
    'subjectOrganization' => 'depictsPlace', 'photoOrganizations' => 'photoPlaces',
    'eventOrganizations' => 'eventPlaces', 'mpOrganizations' => 'mpPlaces',
    'placeOrganizations' => 'relatedPlaces', 'derivedImageLinks' => 'derivedImageLinks',
    'footnotesOn' => 'footnotesOn', 'fixRecord' => 'fixRecord',
    'personOrganizations' => '@placePeople',
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$placeSection = $svc->getSectionByHandle('places');
$placeType = $svc->getEntryTypeByHandle('place');
$placeLayout = $placeType->getFieldLayout();
$fieldsSvc = Craft::$app->getFields();

$handles = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$val = function ($e, string $h) {
    $v = $e->getFieldValue($h);
    if ($v instanceof \craft\elements\db\ElementQuery) { return $v->status(null)->ids(); }
    if ($v instanceof \craft\fields\data\SingleOptionFieldData) { return (string)$v->value; }
    if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl(); }
    if (is_array($v)) { return array_values(array_filter($v, fn($r) => is_array($r) ? count(array_filter($r, fn($c) => $c !== null && $c !== '' && $c !== false)) > 0 : (bool)$r)); }
    return trim((string)$v);
};
$isEmpty = fn($v) => is_array($v) ? count($v) === 0 : $v === '';
$rowKey = fn(array $r) => md5(json_encode(array_map(fn($c) => $c instanceof \DateTimeInterface ? $c->format('Y-m-d') : $c, $r)));
$aliasList = fn(string $s) => array_values(array_filter(array_map('trim', preg_split('~[,\n]~', $s))));

$plans = [];
$blocked = [];
foreach ($MOVES as $orgId => [$targetId, $newType]) {
    $org = \craft\elements\Entry::find()->id($orgId)->section('organizations')->status(null)->one();
    if (!$org) { $blocked[] = "#$orgId: not found"; continue; }
    /* Already retired by an earlier run: nothing to carry, and re-saving the
       place would append its provenance note a second time. */
    if (!$org->enabled) { echo 'skip #' . $orgId . ' "' . $org->title . '": already retired' . PHP_EOL; continue; }
    $place = $targetId ? \craft\elements\Entry::find()->id($targetId)->section('places')->status(null)->one() : null;
    if ($targetId && !$place) { $blocked[] = "#$orgId: target place #$targetId not found"; continue; }
    $clash = !$targetId ? \craft\elements\Entry::find()->section('places')->status(null)->slug($org->slug)->one() : null;
    if ($clash) { $blocked[] = "#$orgId: a place with slug {$org->slug} already exists (#{$clash->id}); map it explicitly"; continue; }

    $set = []; $notes = []; $unplaced = [];
    $orgH = $handles($org);
    foreach ($orgH as $h) {
        if (in_array($h, $IGNORE, true)) { continue; }
        $v = $FIX[$orgId][$h] ?? $val($org, $h);
        if (isset($FIX[$orgId][$h])) { $notes[] = "$h carried corrected: " . json_encode($val($org, $h)) . ' -> ' . json_encode($v); }
        if ($isEmpty($v)) { continue; }
        $dest = $SCALAR[$h] ?? $LINK[$h] ?? $ALIAS[$h] ?? $REL[$h] ?? $TABLE[$h] ?? null;
        /* A destination the place layout does not carry is nowhere to go, and
           holds the organization, rather than being filtered out at save. */
        if ($dest && !$placeLayout->getFieldByHandle($dest)) { $unplaced[$h] = (is_array($v) ? count($v) . ' value(s)' : mb_substr((string)$v, 0, 60)) . " ($dest is not on the place layout)"; continue; }
        if (isset($SCALAR[$h]) || isset($LINK[$h])) {
            $to = $SCALAR[$h] ?? $LINK[$h];
            $have = $place ? $val($place, $to) : '';
            if ($have === '') { $set[$to] = isset($LINK[$h]) ? ['type' => 'url', 'value' => $v] : $v; }
            elseif ((string)$have !== (string)$v) { $notes[] = "CONFLICT $h -> $to: place keeps " . json_encode(mb_substr((string)$have, 0, 50)) . ', organization had ' . json_encode(mb_substr((string)$v, 0, 50)); }
        } elseif (isset($ALIAS[$h])) {
            $have = $place ? $aliasList($val($place, $ALIAS[$h])) : [];
            $merged = array_values(array_unique(array_merge($have, $aliasList($v), $place && $place->title !== $org->title ? [$org->title] : [])));
            if ($merged != $have) { $set[$ALIAS[$h]] = implode(', ', $merged); }
        } elseif (isset($REL[$h])) {
            $to = $REL[$h];
            $have = $place ? $val($place, $to) : [];
            $have = array_merge(is_array($have) ? $have : [], $set[$to] ?? []);
            if (in_array($to, $SINGLE, true)) {
                if (!$have) { $set[$to] = array_slice($v, 0, 1); }
                elseif ($have != array_slice($v, 0, 1)) { $notes[] = "CONFLICT $h: place keeps its own image #" . $have[0] . ', organization had #' . $v[0]; }
            } else {
                $merged = array_values(array_unique(array_merge($have, $v)));
                if (count($merged) > count($have)) { $set[$to] = $merged; }
                if ($h === 'orgFoundedBy') { $notes[] = 'founder #' . implode(', #', $v) . ' goes to placePeople: the place layout has no founder field'; }
            }
        } elseif (isset($TABLE[$h])) {
            $have = $place ? $val($place, $TABLE[$h]) : [];
            $have = is_array($have) ? $have : [];
            $known = array_map($rowKey, $have);
            $add = array_values(array_filter($v, fn($r) => !in_array($rowKey($r), $known, true)));
            if ($add) { $set[$TABLE[$h]] = array_merge($have, $add); }
        } else {
            $unplaced[$h] = is_array($v) ? count($v) . ' value(s)' : mb_substr((string)$v, 0, 60);
        }
    }
    if (!$place) { $set['placeType'] = $newType; }
    $had = $place ? $val($place, 'recordProvenance') : '';
    if (!str_contains($had, 'carried from organization #' . $orgId)) {
        $set['recordProvenance'] = trim($had . '; carried from organization #' . $orgId . ' by convert_orgs_to_places.php, 2026-09-25', '; ');
    }

    /* Inbound relations, excluding drafts and revisions. */
    $inbound = [];
    $rows = (new \craft\db\Query())->select(['r.sourceId', 'f.handle'])->from(['r' => '{{%relations}}'])
        ->innerJoin(['f' => '{{%fields}}'], 'f.id = r.fieldId')
        ->innerJoin(['s' => '{{%elements}}'], 's.id = r.sourceId')
        ->where(['r.targetId' => $orgId, 's.revisionId' => null, 's.draftId' => null, 's.dateDeleted' => null])->distinct()->all();
    foreach ($rows as $r) {
        if (!isset($INBOUND[$r['handle']])) { $blocked[] = "#$orgId: inbound {$r['handle']} from #{$r['sourceId']} has no place equivalent"; continue; }
        $inbound[] = [(int)$r['sourceId'], $r['handle'], $INBOUND[$r['handle']]];
    }
    /* Retire nothing until the place holds what the organization held: a
       conflict or a value with nowhere to go keeps the organization live. */
    $hold = array_merge(array_filter($notes, fn($n) => str_starts_with($n, 'CONFLICT')), array_map(fn($h) => "$h has no place field", array_keys($unplaced)));
    $plans[$orgId] = compact('org', 'place', 'set', 'notes', 'unplaced', 'inbound', 'newType', 'hold');
}

/* ------------------------------------------------------------------ report */

foreach ($plans as $orgId => $p) {
    echo PHP_EOL . '#' . $orgId . ' "' . $p['org']->title . '"  ->  '
        . ($p['place'] ? 'place #' . $p['place']->id . ' "' . $p['place']->title . '"' : 'NEW place, ' . $p['newType'] . ', slug ' . $p['org']->slug) . PHP_EOL;
    foreach ($p['set'] as $h => $v) {
        $show = is_array($v) ? (isset($v['type']) ? $v['value'] : (is_array(reset($v)) ? count($v) . ' row(s)' : json_encode($v))) : (mb_strlen((string)$v) > 70 ? mb_strlen((string)$v) . ' chars: ' . mb_substr((string)$v, 0, 50) . '...' : $v);
        echo '   set ' . str_pad($h, 22) . $show . PHP_EOL;
    }
    foreach ($p['notes'] as $n) { echo '   note ' . $n . PHP_EOL; }
    foreach ($p['unplaced'] as $h => $v) { echo '   NOWHERE TO GO ' . str_pad($h, 16) . $v . PHP_EOL; }
    $by = [];
    foreach ($p['inbound'] as [$src, $from, $to]) { $by["$from -> $to"] = ($by["$from -> $to"] ?? 0) + 1; }
    foreach ($by as $k => $n) { echo '   re-point ' . $k . ': ' . $n . PHP_EOL; }
    echo $p['hold'] ? '   HELD: organization #' . $orgId . ' stays live, because ' . implode('; ', $p['hold']) . PHP_EOL
        : '   then disable organization #' . $orgId . ' (/organizations/' . $p['org']->slug . ' will 404)' . PHP_EOL;
}
foreach ($blocked as $b) { echo PHP_EOL . 'BLOCKED ' . $b; }
echo PHP_EOL . PHP_EOL . count($plans) . ' organizations planned, ' . count($blocked) . ' blocked' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($blocked) { echo 'refusing: resolve the blocked items first' . PHP_EOL; return; }

/* ------------------------------------------------------------------- apply */

$done = []; $failed = []; $held = [];
foreach ($plans as $orgId => $p) {
    $place = $p['place'];
    if (!$place) {
        $place = new \craft\elements\Entry();
        $place->sectionId = $placeSection->id;
        $place->setTypeId($placeType->id);
        $place->title = $p['org']->title;
        $place->slug = $p['org']->slug;
    }
    $set = array_filter($p['set'], fn($v, $h) => (bool)$placeLayout->getFieldByHandle($h), ARRAY_FILTER_USE_BOTH);
    $place->setFieldValues($set);
    if (!$elements->saveElement($place)) { $failed[] = "#$orgId place: " . json_encode($place->getFirstErrors()); continue; }

    $ok = true;
    foreach ($p['inbound'] as [$src, $from, $to]) {
        $s = \craft\elements\Entry::find()->id($src)->status(null)->one();
        if ($to === '@placePeople') {
            $pp = \craft\elements\Entry::find()->id($place->id)->status(null)->one();
            $pp->setFieldValue('placePeople', array_values(array_unique(array_merge($pp->placePeople->status(null)->ids(), [$src]))));
            if (!$elements->saveElement($pp)) { $ok = false; $failed[] = "#$orgId personOrganizations #$src: " . json_encode($pp->getFirstErrors()); }
            continue;
        }
        if (!$s->getFieldLayout()->getFieldByHandle($to)) { $ok = false; $failed[] = "#$orgId: #$src has no $to field"; continue; }
        $s->setFieldValue($to, array_values(array_unique(array_merge($s->getFieldValue($to)->status(null)->ids(), [$place->id]))));
        if ($from !== $to) { $s->setFieldValue($from, array_values(array_diff($s->getFieldValue($from)->status(null)->ids(), [$orgId]))); }
        else { $s->setFieldValue($from, array_values(array_diff($s->getFieldValue($from)->status(null)->ids(), [$orgId]))); $s->setFieldValue($to, array_values(array_unique(array_merge(array_diff($s->getFieldValue($to)->status(null)->ids(), [$orgId]), [$place->id])))); }
        if (!$elements->saveElement($s)) { $ok = false; $failed[] = "#$orgId re-point #$src ($from): " . json_encode($s->getFirstErrors()); }
    }

    /* Read the place back before the organization is touched. */
    $back = \craft\elements\Entry::find()->id($place->id)->status(null)->one();
    foreach ($set as $h => $v) {
        $got = $val($back, $h);
        if (is_array($v) && isset($v['type'])) { $v = $v['value']; }
        $short = is_array($v) ? count((array)$got) < count($v) : (string)$got !== (string)$v;
        if ($short) { $ok = false; $failed[] = "#$orgId place #{$back->id}: $h reads back short"; }
    }
    $still = (new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['s' => '{{%elements}}'], 's.id = r.sourceId')
        ->where(['r.targetId' => $orgId, 's.revisionId' => null, 's.draftId' => null, 's.dateDeleted' => null])->count();
    if ($still) { $ok = false; $failed[] = "#$orgId: $still relation(s) still point at the organization"; }

    if (!$ok) { echo 'NOT RETIRED #' . $orgId . ': the place is short; the organization stays live' . PHP_EOL; continue; }
    if ($p['hold']) { echo 'HELD #' . $orgId . ': carried, but the organization stays live: ' . implode('; ', $p['hold']) . PHP_EOL; $held[$orgId] = $back->id; continue; }
    $org = \craft\elements\Entry::find()->id($orgId)->status(null)->one();
    $org->enabled = false;
    if (!$elements->saveElement($org)) { $failed[] = "#$orgId disable: " . json_encode($org->getFirstErrors()); continue; }
    $done[$orgId] = $back->id;
    echo 'moved #' . $orgId . ' -> place #' . $back->id . ', organization disabled' . PHP_EOL;
}

$bad = count($failed);
echo PHP_EOL . 'READ-BACK ' . ($bad ? 'SHORT' : 'OK') . ': ' . count($done) . ' moved and retired, ' . count($held) . ' carried and held live, of ' . count($plans) . PHP_EOL;
foreach ($failed as $f) { echo '   FAIL ' . $f . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('convert_orgs_to_places.php', count($done), ($bad ? 'SHORT: ' : 'verified: ') . count($done) . ' of ' . count($plans) . ' moved and retired',
    json_encode($done) . ' retired; held live ' . json_encode(array_keys($held)) . '; organizations disabled, not deleted');
if ($bad) { throw new \RuntimeException('convert_orgs_to_places: ' . $bad . ' failure(s)'); }
