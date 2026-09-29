/**
 * Three of Nathan's decisions of 29 September 2026.
 *
 * 1. Stack's #20207 goes. It was made on 25 September to replace the
 *    organization wrongly made for Harvey Stack, but the firm is a New York coin
 *    dealer with no valley connection, and fails the locality rule the same way
 *    he did. Three coin articles hold it as subjectOrganization: #15076, #15444,
 *    #15450. They lose it; #15444 and #15450 keep Heritage Auction Galleries,
 *    #15076 is left with no organization subject, which is honest. Nothing else
 *    points at it: no body link, no redirect, no data file. The relations are
 *    removed first, through the element API, then the record is soft-deleted, so
 *    it sits in the trash and can be restored; it is not hard-deleted.
 *
 * 2. "SCV" comes off The City of Santa Clarita #394. SCV is the valley, and the
 *    city is a part of it. It is that record's only alias, so orgAliases becomes
 *    empty. The other three aliases containing SCV are names of valley bodies
 *    (SCV Historical Society, SCV Water, SCV Chamber) and are right; they are
 *    listed, not changed.
 *
 * 3. The Hart portrait, asset #21573. acquiredDate becomes 2026-09-28, the day
 *    Craft recorded it arriving, and the source field's closing clause says what
 *    is true: the Commons file matched the file as received. Craft re-encodes
 *    an image on import, so the stored copy is not that file.
 *
 * Every change checks the current value first and refuses if it is not what
 * was seen on 29 September, so a record someone has touched since is reported,
 * not overwritten. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_stacks_city_alias_hart.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$STACKS = 20207;
$STACKS_ARTICLES = [15076, 15444, 15450];
$CITY = 394;
$HART = 21573;
$HART_DATE = '2026-09-28';
$OLD_CLAUSE = 'the Commons file is byte-identical to this one.';
$NEW_CLAUSE = 'the Commons file is byte-identical to the file as received.';

$elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ops = []; $done = []; $refused = [];

/* 1. Stack's */
echo "1. STACK'S" . PHP_EOL;
$s = $get($STACKS);
if (!$s) { $done[] = "Stack's #$STACKS already gone"; }
elseif ($s->title !== "Stack's" || $s->section->handle !== 'organizations') { $refused[] = "#$STACKS is now \"{$s->title}\" in {$s->section->handle}, not Stack's"; }
else {
    $in = (new \craft\db\Query())->select(['r.sourceId', 'f.handle'])->from(['r' => '{{%relations}}'])->innerJoin(['f' => '{{%fields}}'], 'f.id = r.fieldId')
        ->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')
        ->where(['r.targetId' => $STACKS, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->all();
    $unexpected = array_filter($in, fn($r) => $r['handle'] !== 'subjectOrganization' || !in_array((int)$r['sourceId'], $STACKS_ARTICLES, true));
    if ($unexpected) { $refused[] = "Stack's has relations not seen on 29 September: " . json_encode(array_values($unexpected)); }
    else {
        foreach ($in as $r) {
            $a = $get((int)$r['sourceId']);
            $keep = array_values(array_diff($a->subjectOrganization->status(null)->ids(), [$STACKS]));
            echo '   #' . $a->id . ' ' . $a->title . ': subjectOrganization loses Stack\'s, keeps ' . ($keep ? implode(', ', array_map(fn($i) => '#' . $i . ' ' . $get($i)->title, $keep)) : 'nothing') . PHP_EOL;
        }
        echo "   then #$STACKS Stack's is soft-deleted (to the trash, restorable)" . PHP_EOL;
        $ops[] = [function () use ($in, $get, $STACKS, $elements) {
            foreach ($in as $r) {
                $a = $get((int)$r['sourceId']);
                $a->setFieldValue('subjectOrganization', array_values(array_diff($a->subjectOrganization->status(null)->ids(), [$STACKS])));
                if (!$elements->saveElement($a)) { return "#{$a->id} save failed: " . json_encode($a->getFirstErrors()); }
            }
            return $elements->deleteElement($get($STACKS)) ? '' : "Stack's delete failed";
        }, function () use ($STACKS) {
            $out = [];
            if (Entry::find()->id($STACKS)->status(null)->exists()) { $out[] = "Stack's #$STACKS is still live"; }
            $left = (new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['e' => '{{%elements}}'], 'e.id = r.sourceId')
                ->where(['r.targetId' => $STACKS, 'e.revisionId' => null, 'e.draftId' => null, 'e.dateDeleted' => null])->count();
            if ((int)$left) { $out[] = "$left relation(s) still point at Stack's"; }
            if (!Entry::find()->id($STACKS)->trashed()->status(null)->exists()) { $out[] = "Stack's is not in the trash"; }
            return implode('; ', $out);
        }, "Stack's removed from 3 articles and trashed"];
    }
}

/* 2. The city's alias */
echo PHP_EOL . '2. CITY ALIAS' . PHP_EOL;
$c = $get($CITY);
$alias = $c ? trim((string)$c->getFieldValue('orgAliases')) : null;
if (!$c || $c->title !== 'The City of Santa Clarita') { $refused[] = "#$CITY is not The City of Santa Clarita"; }
elseif ($alias === '') { $done[] = "#$CITY already has no alias"; }
elseif ($alias !== 'SCV') { $refused[] = "#$CITY orgAliases is now " . json_encode($alias) . ", not \"SCV\""; }
else {
    echo "   #$CITY The City of Santa Clarita: orgAliases \"SCV\" -> empty" . PHP_EOL;
    $ops[] = [function () use ($get, $CITY, $elements) {
        $c = $get($CITY); $c->setFieldValue('orgAliases', '');
        return $elements->saveElement($c) ? '' : 'city save failed: ' . json_encode($c->getFirstErrors());
    }, fn() => trim((string)$get($CITY)->getFieldValue('orgAliases')) === '' ? '' : "#$CITY still has an alias", 'city alias removed'];
}
echo '   left alone, SCV as part of a valley name:' . PHP_EOL;
foreach (Entry::find()->section('organizations')->status(null)->id(['not', $CITY])->all() as $o) {
    $v = (string)$o->getFieldValue('orgAliases');
    if (preg_match('~\bSCV\b~', $v)) { echo '      #' . $o->id . ' ' . $o->title . ': ' . $v . PHP_EOL; }
}

/* 3. Hart */
echo PHP_EOL . '3. HART PORTRAIT' . PHP_EOL;
$h = Asset::find()->id($HART)->one();
$src = $h ? (string)$h->getFieldValue('source') : '';
$acq = $h ? trim((string)$h->getFieldValue('acquiredDate')) : '';
$set = [];
if (!$h) { $refused[] = "asset #$HART not found"; }
else {
    if ($acq === $HART_DATE) { $done[] = "Hart acquiredDate already $HART_DATE"; }
    elseif ($acq === '2026-09-29') { $set['acquiredDate'] = $HART_DATE; echo "   acquiredDate 2026-09-29 -> $HART_DATE" . PHP_EOL; }
    else { $refused[] = "Hart acquiredDate is " . json_encode($acq) . ", not 2026-09-29"; }
    if (str_contains($src, $NEW_CLAUSE)) { $done[] = 'Hart source already says "as received"'; }
    elseif (substr_count($src, $OLD_CLAUSE) === 1) {
        $set['source'] = str_replace($OLD_CLAUSE, $NEW_CLAUSE, $src);
        echo '   source "... ' . $OLD_CLAUSE . '"' . PHP_EOL . '       -> "... ' . $NEW_CLAUSE . '"' . PHP_EOL;
        if (str_contains($src, 'on 29 September 2026')) { $set['source'] = str_replace('on 29 September 2026', 'on 28 September 2026', $set['source']); echo '   source "on 29 September 2026" -> "on 28 September 2026", to agree with acquiredDate' . PHP_EOL; }
    } else { $refused[] = 'Hart source does not end with the expected clause: ' . json_encode(mb_substr($src, -80)); }
}
if ($set) {
    $ops[] = [function () use ($HART, $set, $elements) {
        $h = Asset::find()->id($HART)->one();
        foreach ($set as $k => $v) { $h->setFieldValue($k, $v); }
        return $elements->saveElement($h) ? '' : 'Hart save failed: ' . json_encode($h->getFirstErrors());
    }, function () use ($HART, $set) {
        $h = Asset::find()->id($HART)->one(); $out = [];
        foreach ($set as $k => $v) { $got = trim((string)$h->getFieldValue($k)); if ($got !== trim($v)) { $out[] = "Hart $k reads back " . json_encode($got); } }
        return implode('; ', $out);
    }, 'Hart acquiredDate and source'];
}

echo PHP_EOL . 'ALREADY DONE: ' . ($done ? implode('; ', $done) : 'none') . PHP_EOL;
echo 'REFUSED: ' . ($refused ? implode('; ', $refused) : 'none') . PHP_EOL;
echo 'SUMMARY: ' . count($ops) . ' change(s) to make. Nothing was invented.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if (!$ops) { echo 'nothing to do; a second run is a no-op' . PHP_EOL; return; }

$short = [];
foreach ($ops as [$do, $check, $label]) {
    $err = $do();
    if ($err) { $short[] = "$label: $err"; continue; }
    $bad = $check();
    if ($bad) { $short[] = "$label: $bad"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . implode(', ', array_map(fn($o) => $o[2], $ops))) . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_stacks_city_alias_hart.php', count($ops), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', implode(', ', array_map(fn($o) => $o[2], $ops)));
if ($short) { throw new \RuntimeException('fix_stacks_city_alias_hart: ' . implode('; ', $short)); }
