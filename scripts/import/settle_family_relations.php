/**
 * Settles the family convention on one stored direction and opens it across
 * sections.
 *
 * childOf is the only direction anyone types: a person points up at their
 * parents. Children, siblings and grandparents are derived at render, in
 * _layouts/base.twig, and feed both the Family box and the JSON-LD. Storing
 * parentOf as well meant two places to type the same fact and two places for it
 * to disagree, so it comes off the layout.
 *
 * This does four things:
 *   1. Widens childOf, siblingOf and spouseOf to accept people, war memorial
 *      casualties and military profiles. Today they accept people only, so a
 *      casualty cannot be recorded as anyone's son.
 *   2. Adds childOf, siblingOf and spouseOf to the war memorial and military
 *      profile layouts, in a Family tab, so both ends of the relation can be
 *      typed at all.
 *   3. Retires the fields that say the same thing a second way: parentOf on the
 *      person type, and mpParents, mpChildren, mpSiblings and mpSpouse on the
 *      military profile type, which were a parallel convention of their own.
 *      The fields and their data are left in place; only the layout entries go,
 *      so nothing is destroyed and every step is reversible from the CP.
 *   4. Writes instructions on all of them, saying which way round to type, that
 *      children are derived, and where a retired field's job has gone.
 *
 * Before retiring any field it checks that every relation that field holds is
 * already present the other way round under the convention that replaces it,
 * and refuses to retire it if any is not, so a fact cannot disappear from the
 * site because a layout changed. Revisions and drafts are excluded from that
 * check: the relations table carries a row per revision, which makes 1 real
 * relation look like 13.
 *
 * Idempotent. A second run reports every step as already done.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/settle_family_relations.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

$SECTIONS = ['persons', 'warMemorials', 'militaryProfiles'];
$WIDEN = ['childOf', 'siblingOf', 'spouseOf'];
/* handle => [entry type it sits on, the field that now holds this fact,
   how to read one against the other] */
$RETIRE = [
    'parentOf'    => ['person',          'childOf',   'inverse',
        'Children are derived from the childOf field on the child: open the child\'s '
      . 'record and point it at this person.'],
    'mpParents'   => ['militaryProfile', 'childOf',   'same',
        'Use childOf, which every person-like record now shares.'],
    'mpChildren'  => ['militaryProfile', 'childOf',   'inverse',
        'Children are derived from childOf on the child.'],
    'mpSiblings'  => ['militaryProfile', 'siblingOf', 'either',
        'Siblings who share a recorded parent appear by themselves; use siblingOf only '
      . 'for one the parents do not reach.'],
    'mpSpouse'    => ['militaryProfile', 'spouseOf',  'either',
        'Use spouseOf, which every person-like record now shares.'],
];

$INSTRUCTIONS = [
    'childOf'   => 'This person\'s parents. This is the only direction anyone types: '
                 . 'point up at the parents and the site works out the rest. Children, '
                 . 'siblings and grandparents are derived from this field and are not '
                 . 'entered anywhere. Accepts a person, a war memorial casualty or a '
                 . 'military profile.',
    'siblingOf' => 'Only for a sibling the parents do not reach, such as a half-brother '
                 . 'where no parent record exists. Siblings who share a recorded parent '
                 . 'appear by themselves. Reads both ways, so enter it once, on either record.',
    'spouseOf'  => 'Spouse. Reads both ways, so enter it once, on either record.',
];
foreach ($RETIRE as $h => [$onType, $replacedBy, $dir, $where]) {
    $INSTRUCTIONS[$h] = 'Not in use. ' . $where . ' This field is kept only so its old '
        . 'data is not lost, and is off the layout.';
}

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* ------------------------------------------------- 0. what is really there */

$canonical = function (string $handle) use ($fs): array {
    $f = $fs->getFieldByHandle($handle);
    if (!$f) { return []; }
    return (new \craft\db\Query())
        ->select(['r.sourceId', 'r.targetId'])
        ->from(['r' => '{{%relations}}'])
        ->innerJoin(['se' => '{{%elements}}'], '[[se.id]] = [[r.sourceId]]')
        ->innerJoin(['te' => '{{%elements}}'], '[[te.id]] = [[r.targetId]]')
        ->where(['r.fieldId' => $f->id])
        ->andWhere(['se.draftId' => null, 'se.revisionId' => null, 'se.dateDeleted' => null])
        ->andWhere(['te.draftId' => null, 'te.revisionId' => null, 'te.dateDeleted' => null])
        ->all();
};
$name = function ($id) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    return $e ? $e->title . ' [' . $e->section->handle . ']' : '#' . $id;
};

echo 'relations in use, canonical entries only, revisions excluded:' . PHP_EOL;
foreach (array_merge($WIDEN, array_keys($RETIRE)) as $h) {
    echo '  ' . str_pad($h, 14) . count($canonical($h)) . PHP_EOL;
}
echo PHP_EOL;

/* ------------------------------------ 1. sources: people are not the only kind */

$sourceKeys = [];
foreach ($SECTIONS as $sh) {
    $sec = $svc->getSectionByHandle($sh);
    if (!$sec) { echo 'section ' . $sh . ': NOT FOUND, skipped' . PHP_EOL; continue; }
    $sourceKeys[$sh] = 'section:' . $sec->uid;
}

echo '--- 1. relation sources ---' . PHP_EOL;
foreach ($WIDEN as $h) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) { echo str_pad($h, 12) . 'MISSING' . PHP_EOL; continue; }
    $have = is_array($f->sources) ? $f->sources : [];
    $want = array_values($sourceKeys);
    $add  = array_values(array_diff($want, $have));
    $names = [];
    foreach ($add as $k) { $names[] = array_search($k, $sourceKeys, true); }
    if (!$add) { echo str_pad($h, 12) . 'already accepts ' . implode(', ', array_keys($sourceKeys)) . PHP_EOL; }
    else {
        echo str_pad($h, 12) . 'would add ' . implode(', ', $names)
            . ' (accepts ' . count($have) . ' source' . (count($have) === 1 ? '' : 's') . ' today)' . PHP_EOL;
        if ($APPLY) {
            $f->sources = array_values(array_unique(array_merge($have, $want)));
            echo '             ' . ($fs->saveField($f) ? 'widened' : 'FAILED: ' . implode('; ', $f->getFirstErrors())) . PHP_EOL;
        }
    }
}

/* ------------------------------------------- 2. instructions on all four */

echo PHP_EOL . '--- 2. instructions ---' . PHP_EOL;
foreach ($INSTRUCTIONS as $h => $text) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) { echo str_pad($h, 12) . 'MISSING' . PHP_EOL; continue; }
    if (trim((string)$f->instructions) === trim($text)) { echo str_pad($h, 12) . 'already set' . PHP_EOL; continue; }
    echo str_pad($h, 12) . 'would set: ' . mb_substr($text, 0, 96) . '…' . PHP_EOL;
    if ($APPLY) {
        $f->instructions = $text;
        echo '             ' . ($fs->saveField($f) ? 'set' : 'FAILED: ' . implode('; ', $f->getFirstErrors())) . PHP_EOL;
    }
}

/* ------------------------- 3. the casualty end of the relation needs fields */

echo PHP_EOL . '--- 3. layouts that need the shared fields ---' . PHP_EOL;
foreach (['warMemorial', 'militaryProfile'] as $th) {
    $type = $svc->getEntryTypeByHandle($th);
    if (!$type) { echo str_pad($th, 18) . 'NOT FOUND' . PHP_EOL; continue; }
    $layout = $type->getFieldLayout();
    $present = [];
    foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
    $missing = array_values(array_diff($WIDEN, $present));
    if (!$missing) { echo str_pad($th, 18) . 'already carries ' . implode(', ', $WIDEN) . PHP_EOL; continue; }

    echo str_pad($th, 18) . 'would add ' . implode(', ', $missing) . ' to a "Family" tab' . PHP_EOL;
    if (!$APPLY) { continue; }

    $tabs = $layout->getTabs();
    $tab = null;
    foreach ($tabs as $t) { if ($t->name === 'Family') { $tab = $t; break; } }
    if ($tab === null) {
        $tab = new \craft\models\FieldLayoutTab(['name' => 'Family', 'layout' => $layout]);
        $tab->setElements([]);
        $tabs[] = $tab;
    }
    $els = $tab->getElements();
    foreach ($missing as $h) {
        $f = $fs->getFieldByHandle($h);
        if ($f) { $els[] = new \craft\fieldlayoutelements\CustomField($f); }
    }
    $tab->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo '  ' . ($svc->saveEntryType($type) ? 'added' : 'FAILED: ' . implode('; ', $type->getFirstErrors())) . PHP_EOL;
}

/* ------------------------------------------- 4. parentOf comes off the layout */

echo PHP_EOL . '--- 4. fields that say the same thing a second way ---' . PHP_EOL;

foreach ($RETIRE as $handle => [$onType, $replacedBy, $dir, $where]) {
    $rows = $canonical($handle);
    $mirror = $canonical($replacedBy);

    /* Index the replacement both ways so any direction can be checked. */
    $fwd = []; $rev = [];
    foreach ($mirror as $r) {
        $fwd[$r['sourceId'] . '|' . $r['targetId']] = true;
        $rev[$r['targetId'] . '|' . $r['sourceId']] = true;
    }

    $unmirrored = [];
    foreach ($rows as $r) {
        $ok = match ($dir) {
            'same'    => isset($fwd[$r['sourceId'] . '|' . $r['targetId']]),
            'inverse' => isset($fwd[$r['targetId'] . '|' . $r['sourceId']]),
            default   => isset($fwd[$r['sourceId'] . '|' . $r['targetId']])
                      || isset($rev[$r['sourceId'] . '|' . $r['targetId']]),
        };
        if (!$ok) { $unmirrored[] = $r; }
    }

    $type = $svc->getEntryTypeByHandle($onType);
    $onLayout = false;
    if ($type) {
        foreach ($type->getFieldLayout()->getCustomFields() as $c) {
            if ($c->handle === $handle) { $onLayout = true; break; }
        }
    }

    echo PHP_EOL . str_pad($handle, 14) . 'on ' . $onType . ', ' . count($rows)
        . ' relation' . (count($rows) === 1 ? '' : 's') . ' in use, replaced by ' . $replacedBy . PHP_EOL;
    foreach ($rows as $r) {
        $ok = !in_array($r, $unmirrored, true);
        echo '   ' . $name($r['sourceId']) . '  ->  ' . $name($r['targetId'])
            . ($ok ? '  (already stored as ' . $replacedBy . ', nothing lost)' : '  (NOT mirrored)') . PHP_EOL;
    }

    if (!$type) { echo '   entry type ' . $onType . ' NOT FOUND' . PHP_EOL; continue; }
    if (!$onLayout) { echo '   already off the ' . $onType . ' layout' . PHP_EOL; continue; }

    if ($unmirrored) {
        echo '   REFUSING to remove it: ' . count($unmirrored) . ' relation(s) above are not' . PHP_EOL;
        echo '   stored as ' . $replacedBy . ', so removing it would take a fact off the site.' . PHP_EOL;
        echo '   Record them as ' . $replacedBy . ' first, then run this again.' . PHP_EOL;
        continue;
    }

    echo '   would remove it from the ' . $onType . ' layout. The field and every row it' . PHP_EOL;
    echo '   holds stay in the database; only the layout entry goes.' . PHP_EOL;
    if (!$APPLY) { continue; }

    $layout = $type->getFieldLayout();
    $tabs = $layout->getTabs();
    foreach ($tabs as $t) {
        $els = [];
        foreach ($t->getElements() as $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && $el->getField()->handle === $handle) { continue; }
            $els[] = $el;
        }
        $t->setElements($els);
    }
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo '   ' . ($svc->saveEntryType($type) ? 'removed' : 'FAILED: ' . implode('; ', $type->getFirstErrors())) . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY
    ? 'applied. This is a project config change, so it lands in config/project/.' . PHP_EOL
    : 'nothing written.' . PHP_EOL;
