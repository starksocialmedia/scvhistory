/**
 * Provenance on the canonical portrait, and the two real duplicate files.
 *
 * WHAT THE BRIEF ASKED FOR, AND WHY IT IS NOT DONE HERE
 *
 * The brief was to treat the 21 history-santa-clarita-*-jerry-reynolds.jpg
 * files as duplicate imports of one portrait, repoint their relations onto a
 * canonical asset and delete them. They are not duplicates. Every one has a
 * different hash, and looking at them says why: each is a per-chapter title
 * card, the same drawing of Reynolds over the same map with that chapter's
 * number and title lettered into it. "Chapter 1 / A Valley Takes Shape",
 * "Chapter 3 / Man Arrives". Merging them would destroy 21 distinct pieces of
 * artwork and leave 21 chapters sharing one card.
 *
 * Hashing the whole store found exactly two byte-identical pairs, and they are
 * the two Craft-rename collisions already known:
 *
 *   legacy/perkins_ab.jpg  =  legacy/perkins_ab_2026-09-18-071146_nitl.jpg
 *   legacy/jj2003a.jpg     =  legacy/jj2003a_2026-09-18-071338_lure.jpg
 *
 * Nothing else in 570 files is a duplicate of anything, and no file anywhere is
 * imported more than once under chapter-specific names. The pattern does not
 * exist because it was never a duplication pattern.
 *
 * SO THIS SCRIPT DOES THE TWO THINGS THAT ARE REAL
 *
 * 1. Provenance on the canonical portrait. persons/jerry-reynolds.jpg is
 *    byte-identical to /mnt/reggie/scvhistory.com/gif/lw2184.jpg, md5
 *    f5434e986278c9d45d7e5f4979065d83, so it is lw2184 unmodified from the
 *    mirror. It gets photoSourceCode lw2184 and the legacy path, so the
 *    provenance survives the file being replaced by an enhanced version later.
 *
 * 2. The two duplicate pairs. Relations are repointed from the renamed copy to
 *    the original and the renamed copy is listed for deletion by hand. It is
 *    not deleted here: a script that removes files is a different risk from one
 *    that moves references, and the second is reversible.
 *
 * legacySourcePath does not exist on the asset layout yet, so this adds it.
 * That is a schema change and it is reported as one.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_asset_provenance.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$VOLUME     = 'archiveMedia';
$PATH_FIELD = 'legacySourcePath';
$MIRROR     = '/mnt/reggie/scvhistory.com';

/* filename => [legacy path under the mirror root, source code] */
$PROVENANCE = [
    'jerry-reynolds.jpg' => ['/gif/lw2184.jpg', 'lw2184'],
];

/* renamed copy => original, both byte-identical */
$DUPES = [
    'perkins_ab_2026-09-18-071146_nitl.jpg' => 'perkins_ab.jpg',
    'jj2003a_2026-09-18-071338_lure.jpg'    => 'jj2003a.jpg',
];

$fs = Craft::$app->getFields();
$vs = Craft::$app->getVolumes();
$elements = Craft::$app->getElements();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* ------------------------------------------------------- the field, first */

$field = $fs->getFieldByHandle($PATH_FIELD);
if ($field === null) {
    echo $PATH_FIELD . '  would create, PlainText' . PHP_EOL;
    echo '   SCHEMA CHANGE: the asset layout has photoSourceCode but no field for' . PHP_EOL;
    echo '   the path the file came from.' . PHP_EOL;
    if ($APPLY) {
        $new = new \craft\fields\PlainText();
        $new->name = 'Legacy Source Path';
        $new->handle = $PATH_FIELD;
        $new->instructions =
            'Where this file was on the legacy site, as a path from the site root: '
          . '/gif/lw2184.jpg. The mirror holds it at the same path under '
          . $MIRROR . '. This is provenance and must not change when the file is '
          . 'replaced by an enhanced or rescanned version: it records what the '
          . 'archive originally took, not what is currently stored.';
        if (!$fs->saveField($new)) { echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL; return; }
        $field = $fs->getFieldByHandle($PATH_FIELD);
        echo '   created' . PHP_EOL;
    }
} else {
    echo $PATH_FIELD . '  exists already' . PHP_EOL;
}

$volume = $vs->getVolumeByHandle($VOLUME);
if (!$volume) { echo 'volume ' . $VOLUME . ' NOT FOUND' . PHP_EOL; return; }
$layout = $volume->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
$hasPathField = in_array($PATH_FIELD, $present, true);

if (!$hasPathField) {
    echo 'would add ' . $PATH_FIELD . ' to the ' . $VOLUME . ' layout' . PHP_EOL;
    if ($APPLY && $field !== null) {
        $tabs = $layout->getTabs();
        $tab = $tabs[0];
        $els = $tab->getElements();
        $els[] = new \craft\fieldlayoutelements\CustomField($field);
        $tab->setElements($els);
        $layout->setTabs($tabs);
        $volume->setFieldLayout($layout);
        if ($vs->saveVolume($volume)) { echo 'added to the layout' . PHP_EOL; $hasPathField = true; }
        else { echo 'FAILED: ' . implode('; ', $volume->getFirstErrors()) . PHP_EOL; }
    }
} else {
    echo 'the ' . $VOLUME . ' layout already carries ' . $PATH_FIELD . PHP_EOL;
}

/* ------------------------------------------------------------ provenance */

echo PHP_EOL;
$byName = [];
foreach (\craft\elements\Asset::find()->limit(null)->all() as $a) { $byName[strtolower($a->filename)] = $a; }

$provPlan = [];
foreach ($PROVENANCE as $fn => [$path, $code]) {
    $a = $byName[strtolower($fn)] ?? null;
    if (!$a) { echo 'NOT FOUND: ' . $fn . PHP_EOL; continue; }

    $mirror = $MIRROR . $path;
    $onMirror = @is_readable($mirror);
    /* The asset's bytes, read through Craft rather than by guessing at the
       filesystem path: a volume's filesystem is not always local. */
    $ours = null;
    try { $ours = md5($a->getContents()); } catch (\Throwable $e) { $ours = null; }
    $same = $onMirror && $ours !== null && @md5_file($mirror) === $ours;

    echo $fn . '  asset #' . $a->id . PHP_EOL;
    echo '   legacy path  ' . $path . PHP_EOL;
    echo '   mirror       ' . $mirror . '  ' . ($onMirror ? 'readable' : 'NOT READABLE')
       . ($onMirror ? ($same ? ', byte-identical to ours' : ', DIFFERS from ours') : '') . PHP_EOL;
    echo '   source code  ' . $code . PHP_EOL;
    echo '   current photoSourceCode: ' . (trim((string)$a->getFieldValue('photoSourceCode')) ?: '(empty)') . PHP_EOL;
    $provPlan[] = ['a' => $a, 'path' => $path, 'code' => $code];
}

/* ------------------------------------------------------ the two duplicates */

echo PHP_EOL;
$dupPlan = [];
foreach ($DUPES as $copy => $orig) {
    $c = $byName[strtolower($copy)] ?? null;
    $o = $byName[strtolower($orig)] ?? null;
    if (!$c || !$o) { echo 'skipping ' . $copy . ', one side is missing' . PHP_EOL; continue; }

    /* Every relation pointing at the renamed copy, in any field. */
    /* Canonical elements only. Craft keeps a relation row on every revision it
       made, and those rows are the record of what the entry looked like then.
       Repointing them would rewrite history to say the duplicate was never
       there, which is the opposite of what an archive should do. The revisions
       keep their rows and go stale honestly when the file is deleted. */
    $rels = (new \craft\db\Query())
        ->select(['r.id', 'r.fieldId', 'r.sourceId', 'r.sortOrder'])
        ->from(['r' => '{{%relations}}'])
        ->innerJoin(['e' => '{{%elements}}'], 'e.id = r.sourceId')
        ->where(['r.targetId' => $c->id, 'e.revisionId' => null, 'e.draftId' => null])
        ->all();

    $onRevisions = (new \craft\db\Query())
        ->from(['r' => '{{%relations}}'])
        ->innerJoin(['e' => '{{%elements}}'], 'e.id = r.sourceId')
        ->where(['r.targetId' => $c->id])
        ->andWhere(['not', ['e.revisionId' => null]])
        ->count();

    echo $copy . '  #' . $c->id . '  ->  ' . $orig . '  #' . $o->id . PHP_EOL;
    echo '   live relations pointing at the copy: ' . count($rels) . PHP_EOL;
    echo '   on revisions, left alone: ' . $onRevisions . PHP_EOL;
    foreach ($rels as $r) {
        $f = $fs->getFieldById($r['fieldId']);
        $src = \craft\elements\Entry::find()->id($r['sourceId'])->status(null)->one();
        echo '      ' . ($src ? $src->slug : '#' . $r['sourceId']) . '  [' . ($f ? $f->handle : '?') . ']' . PHP_EOL;
    }
    $dupPlan[] = ['copy' => $c, 'orig' => $o, 'rels' => $rels];
}

/* ---------------------------------------------------------------- writing */

$wrote = 0; $moved = 0; $failed = [];
if ($APPLY) {
    foreach ($provPlan as $p) {
        $p['a']->setFieldValue('photoSourceCode', $p['code']);
        if ($hasPathField) { $p['a']->setFieldValue($PATH_FIELD, $p['path']); }
        if ($elements->saveElement($p['a'])) { $wrote++; }
        else { $failed[] = $p['a']->filename . ': ' . json_encode($p['a']->getErrors()); }
    }

    foreach ($dupPlan as $d) {
        foreach ($d['rels'] as $r) {
            /* Repointed in place rather than by rewriting the owning element's
               field, because the copy may sit in a field this script does not
               know the shape of. A relation row is a relation row. */
            $exists = (new \craft\db\Query())->from('{{%relations}}')
                ->where(['fieldId' => $r['fieldId'], 'sourceId' => $r['sourceId'], 'targetId' => $d['orig']->id])
                ->exists();
            if ($exists) {
                Craft::$app->getDb()->createCommand()->delete('{{%relations}}', ['id' => $r['id']])->execute();
            } else {
                Craft::$app->getDb()->createCommand()
                    ->update('{{%relations}}', ['targetId' => $d['orig']->id], ['id' => $r['id']])->execute();
            }
            $moved++;
        }
    }
    echo PHP_EOL . 'provenance written: ' . $wrote . PHP_EOL;
    echo 'relations repointed: ' . $moved . PHP_EOL;
    foreach ($failed as $f) { echo '  FAILED ' . $f . PHP_EOL; }

    /* ------------------------------------------------- read the writes back */

    $short = [];
    foreach ($provPlan as $p) {
        $fresh = \craft\elements\Asset::find()->id($p['a']->id)->one();
        if (!$fresh) { $short[] = $p['a']->filename . ': gone after save'; continue; }
        if (trim((string)$fresh->getFieldValue('photoSourceCode')) !== $p['code']) {
            $short[] = $p['a']->filename . ': photoSourceCode reads back as "'
                     . trim((string)$fresh->getFieldValue('photoSourceCode')) . '"';
        }
        if ($hasPathField && trim((string)$fresh->getFieldValue($PATH_FIELD)) !== $p['path']) {
            $short[] = $p['a']->filename . ': ' . $PATH_FIELD . ' reads back as "'
                     . trim((string)$fresh->getFieldValue($PATH_FIELD)) . '"';
        }
    }
    foreach ($dupPlan as $d) {
        $left = (new \craft\db\Query())
            ->from(['r' => '{{%relations}}'])
            ->innerJoin(['e' => '{{%elements}}'], 'e.id = r.sourceId')
            ->where(['r.targetId' => $d['copy']->id, 'e.revisionId' => null, 'e.draftId' => null])
            ->count();
        if ($left > 0) { $short[] = $d['copy']->filename . ': ' . $left . ' live relations still point at the copy'; }
    }
    echo 'read back: ' . (count($provPlan) - count(array_filter($short, fn($m) => str_contains($m, 'photoSourceCode') || str_contains($m, $PATH_FIELD))))
       . ' of ' . count($provPlan) . ' assets carry their provenance' . PHP_EOL;
    if ($short) {
        echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
        foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }
        echo 'Do not delete anything and do not re-run until this is understood.' . PHP_EOL;
        return;
    }
    echo 'verified.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo 'DELETE BY HAND IN THE CONTROL PANEL, after this has been applied:' . PHP_EOL;
foreach ($DUPES as $copy => $orig) {
    $c = $byName[strtolower($copy)] ?? null;
    echo '   ' . ($c ? '#' . $c->id . '  ' : '') . $copy . '   (identical to ' . $orig . ')' . PHP_EOL;
}
echo PHP_EOL;
echo 'DO NOT DELETE the 21 history-santa-clarita-*-jerry-reynolds.jpg files.' . PHP_EOL;
echo 'They are per-chapter title cards, every one different.' . PHP_EOL;
echo $APPLY ? 'done' : PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
