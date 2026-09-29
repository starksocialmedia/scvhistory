/**
 * Organizations and places made ready to group by kind (Nathan, 29 September
 * 2026, all five answers given).
 *
 *   orgType      Newhall Hardware -> business; SCVHistory.com -> nonprofit (a
 *                service of SCVTV, a 501(c)(3), and the publisher of the
 *                archive's editorial content)
 *   placeType    a new option, cemetery; and 17 places typed, the approved list.
 *                Melody Ranch is typed ranch, and its record says the studio
 *                outlived the ranch by decades, so the type does not carry the
 *                whole claim.
 *   retired      Rancho Camulos the organization (#384), into the place (#631):
 *                one record per subject. Nothing points at the organization.
 *                Its body is unsourced prose from the WordPress import; the place
 *                has its own, and the organization's stays in the trash,
 *                restorable, as the other hidden WordPress bodies stay hidden.
 *                Lake Hughes the place (#607), into the community (#196): its
 *                nine photographs take the community instead, its one sentence
 *                and picture move to the community where it has none, and the
 *                one derived image link, which cannot point at a community, is
 *                dropped.
 * Both retired addresses redirect (config/redirects.php, same commit).
 *
 * Changes only what is named. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/organize_orgs_places.php'))"
 */

use craft\elements\Entry;
use craft\elements\Category;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements(); $fs = Craft::$app->getFields();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ops = []; $refused = [];

/* orgType */
foreach ([15823 => ['Newhall Hardware', 'business'], 378 => ['SCVHistory.com — Santa Clarita Valley History', 'nonprofit']] as $id => [$t, $v]) {
    $e = $get($id);
    if (!$e || $e->title !== $t) { $refused[] = "#$id is not $t"; continue; }
    if ((string)$e->orgType->value === $v) { continue; }
    echo "orgType  #$id $t: " . ($e->orgType->value ?: '(none)') . " -> $v" . PHP_EOL;
    $ops[] = function () use ($get, $id, $v, $elements) { $e = $get($id); $e->setFieldValue('orgType', $v); return $elements->saveElement($e) ? '' : "#$id"; };
}

/* the cemetery option */
$pt = $fs->getFieldByHandle('placeType');
$needCemetery = !in_array('cemetery', array_map(fn($o) => $o['value'], $pt->options), true);
if ($needCemetery) { echo 'placeType: add option cemetery' . PHP_EOL; }

/* 17 place types */
$TYPES = [2540 => ['Felton School', 'building'], 2538 => ['Newhall Ranch House', 'building'], 16364 => ['Saugus Cafe', 'building'],
    635 => ['Ridge Route', 'road'], 934 => ['Newhall Pass interchange', 'road'], 932 => ["Beale's Cut Stagecoach Pass", 'road'],
    599 => ['Harry Carey Ranch', 'ranch'], 615 => ['Melody Ranch Motion Picture Studio', 'ranch'],
    605 => ['Heritage Junction Historic Park', 'park'], 613 => ['Six Flags Magic Mountain', 'park'],
    926 => ['Lyons Station Stagecoach Stop', 'site'], 609 => ['Lang Station', 'site'], 643 => ['Saugus Speedway', 'site'], 595 => ['Estancia de San Francisco Xavier', 'site'],
    2536 => ['Ruiz Cemetery', 'cemetery'], 15949 => ['Downtown Newhall', 'district'], 16184 => ['San Francisquito', 'settlement']];
$typePlan = [];
foreach ($TYPES as $id => [$t, $v]) {
    $e = $get($id);
    if (!$e || $e->title !== $t) { $refused[] = "#$id is not $t"; continue; }
    if ((string)$e->placeType->value === $v) { continue; }
    echo "placeType #$id $t: " . ($e->placeType->value ?: '(none)') . " -> $v" . PHP_EOL;
    $typePlan[$id] = $v;
}
$MELODY_NOTE = ['heading' => 'A studio typed as a ranch', 'position' => 'bottom',
    'note' => 'This place is filed under Ranches because it began as one. It has been a motion picture studio for far longer than it was a working ranch, and most of its history, and most of what the archive holds about it, is the studio\'s.'];
$melody = $get(615);
$needMelody = !array_filter($melody->editorNotes ?? [], fn($r) => is_array($r) && ($r['heading'] ?? '') === $MELODY_NOTE['heading']);
if ($needMelody) { echo 'editor note on #615 Melody Ranch: ' . $MELODY_NOTE['note'] . PHP_EOL; }

/* retire Rancho Camulos the organization */
$rco = $get(384);
$inRco = $rco ? (int)(new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->where(['r.targetId' => 384, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->count() : 0;
if ($rco && ($rco->title !== 'Rancho Camulos' || $rco->section->handle !== 'organizations')) { $refused[] = '#384 is not the organization Rancho Camulos'; }
elseif ($rco && $inRco) { $refused[] = "#384 has $inRco inbound relation(s) now"; }
elseif ($rco) { echo 'retire #384 Rancho Camulos (organization) to the trash; /organizations/rancho-camulos redirects to /places/rancho-camulos' . PHP_EOL; }

/* retire Lake Hughes the place */
$lh = $get(607); $com = Category::find()->id(196)->one();
$lhPlan = null;
if ($lh) {
    if ($lh->title !== 'Lake Hughes' || !$com || $com->title !== 'Lake Hughes') { $refused[] = 'Lake Hughes records are not as seen'; }
    else {
        $in = (new \craft\db\Query())->select(['r.sourceId', 'f.handle'])->from(['r' => '{{%relations}}'])->innerJoin(['f' => '{{%fields}}'], 'f.id = r.fieldId')->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')
            ->where(['r.targetId' => 607, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->all();
        $photos = array_values(array_unique(array_map(fn($r) => (int)$r['sourceId'], array_filter($in, fn($r) => $r['handle'] === 'photoPlaces'))));
        $derived = array_values(array_unique(array_map(fn($r) => (int)$r['sourceId'], array_filter($in, fn($r) => $r['handle'] === 'derivedImageLinks'))));
        $other = array_filter($in, fn($r) => !in_array($r['handle'], ['photoPlaces', 'derivedImageLinks'], true));
        if ($other) { $refused[] = 'Lake Hughes has relations not seen: ' . json_encode(array_values($other)); }
        $comBodyEmpty = trim((string)$com->body) === '';
        $img = $lh->featuredImage->one();
        echo 'retire #607 Lake Hughes (place) into community #196: ' . count($photos) . ' photographs take the community; ' . count($derived) . ' derived image link dropped'
            . ($comBodyEmpty ? '; its sentence moves to the community' : '; the community already has a body, kept') . ($img ? '; its picture ' . $img->filename . ' joins the community\'s images' : '')
            . '; /places/lake-hughes redirects to /communities/lake-hughes' . PHP_EOL;
        $lhPlan = [$photos, $derived, $comBodyEmpty, $img];
    }
}
echo PHP_EOL . 'REFUSED: ' . ($refused ? implode('; ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$short = [];
foreach ($ops as $op) { if ($e = $op()) { $short[] = "orgType $e"; } }
if ($needCemetery) {
    $pt->options = array_merge($pt->options, [['label' => 'Cemetery', 'value' => 'cemetery', 'default' => false]]);
    if (!$fs->saveField($pt)) { throw new \RuntimeException('placeType: ' . json_encode($pt->getFirstErrors())); }
}
foreach ($typePlan as $id => $v) { $e = $get($id); $e->setFieldValue('placeType', $v); if (!$elements->saveElement($e)) { $short[] = "placeType #$id"; } }
if ($needMelody) { $e = $get(615); $e->setFieldValue('editorNotes', array_merge(array_values(array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')), [$MELODY_NOTE])); if (!$elements->saveElement($e)) { $short[] = 'Melody note'; } }
if ($rco && !$refused) { if (!$elements->deleteElement($get(384))) { $short[] = 'retire #384'; } }
if ($lhPlan) {
    [$photos, $derived, $comBodyEmpty, $img] = $lhPlan;
    foreach ($photos as $pid) {
        $p = $get($pid);
        $p->setFieldValue('photoPlaces', array_values(array_diff(array_map('intval', $p->photoPlaces->status(null)->ids()), [607])));
        $p->setFieldValue('neighborhood', array_values(array_unique(array_merge(array_map('intval', $p->neighborhood->ids()), [196]))));
        if (!$elements->saveElement($p)) { $short[] = "photo #$pid"; }
    }
    foreach ($derived as $pid) { $p = $get($pid); $p->setFieldValue('derivedImageLinks', array_values(array_diff(array_map('intval', $p->derivedImageLinks->status(null)->ids()), [607]))); $elements->saveElement($p); }
    $com = Category::find()->id(196)->one();
    if ($comBodyEmpty) { $com->setFieldValue('body', (string)$get(607)->body); }
    if ($img) { $com->setFieldValue('recordImages', array_values(array_unique(array_merge(array_map('intval', $com->recordImages->ids()), [$img->id])))); }
    if (!$elements->saveElement($com)) { $short[] = 'community #196'; }
    $left = (int)(new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')->where(['r.targetId' => 607, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->count();
    if ($left) { $short[] = "$left relation(s) still point at #607; NOT retired"; } elseif (!$elements->deleteElement($get(607))) { $short[] = 'retire #607'; }
}
foreach ($typePlan as $id => $v) { if ((string)$get($id)->placeType->value !== $v) { $short[] = "#$id type reads back " . $get($id)->placeType->value; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('organize_orgs_places.php', count($ops) + count($typePlan) + 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'types for grouping; cemetery option; Rancho Camulos org and Lake Hughes place retired');
if ($short) { throw new \RuntimeException('organize_orgs_places: ' . implode('; ', $short)); }
