/**
 * Date fields that hold something other than a date (inventory/review/
 * date-fields-prose-2026-10-04.txt). Nathan, 4 October 2026: "Clear the 14
 * paragraphs and set from datelines where they exist, clear the 9 caption
 * fragments, move the 8 mastheads into the source line, leave the 23 program
 * pages, and fix your own #28307."
 *
 * The importer took the wrong line of the page for the date. Every value cleared
 * here is still in the record's body word for word (checked below), so nothing
 * printed is lost.
 *
 *   (a) 13 articles whose originalPublishDate holds a paragraph of the article.
 *       The column's own dateline ("By Leon Worden / Wednesday, May 6, 1998")
 *       is at the head of each body. originalPublishDate is set to it as
 *       printed, and originalPublishDateEdtf to the day, but only where the
 *       legacy file name (lwMMDDYY, prMMDDYY) gives the same day. #12168 does
 *       not: its page prints "Wednesday, April 24, 2002" (the date of
 *       lw042402b, A Third-Grade History Cheat Sheet) under the file name
 *       lw011399 and a column that calls the 1999 calendar "available now". It
 *       is cleared and held for Nathan; no date is chosen.
 *   (a/c) #12617 holds the Old Town Newhall Gazette masthead. The masthead goes
 *       to sourceLine as printed; the issue date, "March-April 2006", is the
 *       article's publication date and becomes originalPublishDate, with
 *       2006-03/2006-04 as the other Gazette issues have it.
 *   (b) 8 photographs whose photoDate holds a fragment of the caption. Cleared.
 *       photoDateEdtf is empty on all of them; nothing is set in its place.
 *   (c) 7 photographs whose photoDate holds the masthead of a news story
 *       transcribed in the body. Photographs have no sourceLine; the archive's
 *       line for a story's source on a photograph is the bottom editor's note
 *       ("News story courtesy of Tricia Lemon Putnam.", #5277, #4895), so the
 *       masthead goes there as "News story: <masthead as printed>". The
 *       masthead date is when the paper ran, not when the photograph was
 *       taken, so it is not copied into photoDate or photoDateEdtf; photoDate
 *       is cleared.
 *   (d) 25 Baker Ranch Rodeo program pages: left as they are.
 *   (e) #28307, Connie Worden-Roberts Memorial Bridge: "Tuesday, October 4,
 *       2016 (event); text undated" was written by Claude on 3 October. The
 *       release is undated, so originalPublishDate is cleared. The event date
 *       stays in the record's recordDates row, which held the same words at
 *       year precision; the row now reads "Tuesday, October 4, 2016", the day
 *       the body gives, as a day. originalPublishDateEdtf ("2016") is left.
 *
 * Exact-match guards: each field must read as it does now (sha1 of the value)
 * or already read as the target; anything else refuses. One transaction.
 * Idempotent: a record already corrected is skipped and not saved.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_date_field_prose_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$el = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5)));
$has = function ($e) { $h = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = $f; } return $h; };
$cut = fn($s, $n = 90) => mb_strlen($s) > $n ? mb_substr($s, 0, $n) . '...' : $s;
$bad = []; $plan = []; $held = []; $done = 0;

/* sha1 of each field as it reads on 4 October 2026. */
$WAS = [
    12656 => '35bc46071092748a4feec00070f2c821989f5e0d', 12617 => '903fff57641f9fc63a1e48aa909acb78dcf30581',
    12540 => '5af6da2882fbe21deb559f11bea59378df556332', 12458 => 'e6b16922a6eb5a51e6049f7466f592368104808c',
    12412 => '5af6da2882fbe21deb559f11bea59378df556332', 12292 => '908557fd578fdd502c65063ec594e1cc301e6d8e',
    12256 => 'c6ab6287bb05757a646e0edd62cd8b78c1152aab', 12244 => '4042bfbe9ce7b800975cab0c3db91244a9c5a1b0',
    12208 => '40b7f8e874bb150445af1481e5a23a36af81122b', 12206 => 'a847b1ab4f6daa2424affe8f008862f2aa89a593',
    12200 => 'a847b1ab4f6daa2424affe8f008862f2aa89a593', 12168 => 'cbbb6fcf5b15b7dcd64ce6d22f82f14484c4c66b',
    12152 => '40b7f8e874bb150445af1481e5a23a36af81122b', 12140 => '2414f057ac779b86dae1593dd98cd52dc48fcac4',
    28307 => '05996aa8de98130457c466b9473983aa506ba22f',
    5551 => '13d8c5a2a0ad51cb68f17f59944d6d69afc1e909', 5537 => '3d9e6fae5acf3e6151f286392c618151c6698a59',
    5459 => '0abe331f837d8b8ae58b07a8118da2ee955b1b67', 5141 => 'cb6f60f494dcecf11973a2506a2cda074b2bafa3',
    4625 => '5177864401a463e7f09474a4cab6672c107a0b37', 4623 => '5177864401a463e7f09474a4cab6672c107a0b37',
    4383 => '579522203731afa5d93c82560f82befb4250a8d7', 3129 => '95aac6c78cee0b5584d56dced29556dcc64af2f8',
    5511 => '6e007a87d3843e7f310317307bc4c2fb40afc00c', 5377 => 'f56fa3e99f6c78dfbed7785bb4bb95233e814b78',
    5369 => 'b9ddc701550724baea16570b9ed99b1ba6a8eef5', 5277 => 'dee66a32135118f96365d80fea5cec3b28253b7f',
    5047 => 'b35c7ff5c8be833728b0132aac7279503f87e225', 4895 => 'a20fec620c9c1202601051b5bed72d0c730248e1',
    2795 => '1ce0efa9e96a6437e0fd5bf846ad01bc7761c0c9',
];
/* (a) The dateline as printed at the head of the body, and its day. null: hold. */
$DATELINE = [
    12656 => ['November 15, 1997', '1997-11-15'],
    12540 => ['June 25, 1997', '1997-06-25'],
    12458 => ['October 15, 1997', '1997-10-15'],
    12412 => ['June 25, 1997', '1997-06-25'],
    12292 => ['November 8, 1996', '1996-11-08'],
    12256 => ['Wednesday, May 6, 1998', '1998-05-06'],
    12244 => ['Wednesday, November 11, 1998', '1998-11-11'],
    12208 => ['Wednesday, July 15, 1998', '1998-07-15'],
    12206 => ['August 20, 1997', '1997-08-20'],
    12200 => ['August 20, 1997', '1997-08-20'],
    12168 => null,
    12152 => ['Wednesday, July 15, 1998', '1998-07-15'],
    12140 => ['Thursday, Sept. 2, 2004', '2004-09-02'],
];
$HOLD_WHY = [12168 => 'the page prints "Wednesday, April 24, 2002" (the dateline of lw042402b) but the file is lw011399 (13 January 1999) and the column calls the 1999 calendar "available now"; no date chosen'];
/* (a/c) The Gazette masthead. */
$GAZ = [12617, 'Old Town Newhall Gazette | March-April 2006 | Year 12, Number 2.', 'March-April 2006', '2006-03/2006-04'];
/* (b) Caption fragments, and what the body says of the item's own date (reported, not set). */
$FRAG = [
    5551 => 'body: Hoover Art Co. photograph "circa 1918-1919", undated',
    5537 => 'body: British lobby card for the 1972 UK release; no date of its own',
    5459 => 'body: pinback "early-mid 1960s"',
    5141 => 'body: lobby card for the 1944 re-release',
    4625 => 'body: "photograph dated 5-19-1938"',
    4623 => 'body: "photograph dated 5-19-1938"',
    4383 => 'body: screenshot of an episode first aired November 5, 1982',
    3129 => 'body and title: "Sept. 18, 1991"',
];
/* (c) Photograph mastheads, and the item's date as its title gives it (reported, not set). */
$MAST = [
    5511 => 'title 1934; body: rodeo of April 22, 1934',
    5377 => 'title 4-27-1930 (the program\'s date)',
    5369 => 'title 1962; body: crowned July 1962',
    5277 => 'title 10-27-1956 (the derailment; the image is an example locomotive, not the one)',
    5047 => 'title 1949 and 1950 (pool and park)',
    4895 => 'title 1924 (Singer Jim McKee)',
    2795 => 'title 4-28-1935 (the rodeo the tickets are for)',
];
$PROV = 'fix_date_field_prose_2026_10_04: date field held prose, corrected (Nathan, 4 October 2026)';

/* Guard: the field reads as recorded, or already reads as the target. */
$guard = function ($e, $field, $target) use ($WAS, &$bad) {
    $v = (string)$e->getFieldValue($field);
    if ($v === $target) { return 'done'; }
    if (sha1($v) !== ($WAS[$e->id] ?? '')) { $bad[] = "#{$e->id} $field does not read as recorded: " . mb_substr($v, 0, 60); return 'bad'; }
    return 'todo';
};
$inBody = function ($e, $v) use ($ws, &$bad) {
    if ($v !== '' && !str_contains($ws($e->body), $ws($v))) { $bad[] = "#{$e->id}: the value is not in the body, clearing it would lose it"; }
};
$slugDay = function ($e) {
    if (!preg_match('~/(?:lw|pr)(\d\d)(\d\d)(\d\d)\.htm$~', (string)$e->legacyUrl, $m)) { return null; }
    $y = (int)$m[3] < 30 ? 2000 + (int)$m[3] : 1900 + (int)$m[3];
    return sprintf('%04d-%s-%s', $y, $m[1], $m[2]);
};

echo '(a) articles: paragraph in originalPublishDate' . PHP_EOL;
foreach ($DATELINE as $id => $dl) {
    $e = $get($id); $h = $has($e);
    if (!isset($h['originalPublishDate'], $h['originalPublishDateEdtf'])) { $bad[] = "#$id lacks the publish date fields"; continue; }
    $old = (string)$e->originalPublishDate;
    $new = $dl ? $dl[0] : '';
    $g = $guard($e, 'originalPublishDate', $new);
    if ($g === 'bad') { continue; }
    if ($g === 'done' && (!$dl || (string)$e->originalPublishDateEdtf === $dl[1])) { echo "  #$id already done" . PHP_EOL; $done++; continue; }
    $inBody($e, $old);
    $vals = ['originalPublishDate' => $new];
    if ($dl) {
        $head = mb_substr($ws($e->body), 0, 200);
        if (!str_contains($head, $dl[0])) { $bad[] = "#$id: dateline \"{$dl[0]}\" not at the head of the body"; continue; }
        if ($slugDay($e) !== $dl[1]) { $bad[] = "#$id: file name gives " . ($slugDay($e) ?? 'no day') . ", dateline {$dl[1]}"; continue; }
        if (preg_match('~^(\w+day),~', $dl[0], $m) && date('l', strtotime($dl[1])) !== $m[1]) { $bad[] = "#$id: {$dl[1]} is not a {$m[1]}"; continue; }
        $edtfOld = (string)$e->originalPublishDateEdtf;
        if ($edtfOld !== '' && $edtfOld !== $dl[1]) { $bad[] = "#$id: originalPublishDateEdtf already \"$edtfOld\""; continue; }
        $vals['originalPublishDateEdtf'] = $dl[1];
    } else {
        $held[] = "#$id {$e->title}: cleared, no date set. " . $HOLD_WHY[$id] . '. originalPublishDateEdtf left as is ("' . $e->originalPublishDateEdtf . '")';
    }
    $plan[$id] = [$e, $vals, 'a'];
    echo "  #$id {$cut($e->title, 50)}" . PHP_EOL . "     originalPublishDate: \"{$cut($old)}\" -> \"$new\"" . PHP_EOL;
    if (isset($vals['originalPublishDateEdtf'])) { echo "     originalPublishDateEdtf: \"{$e->originalPublishDateEdtf}\" -> \"{$vals['originalPublishDateEdtf']}\" (file name " . basename((string)$e->legacyUrl) . ' agrees)' . PHP_EOL; }
    else { echo '     HELD: ' . $HOLD_WHY[$id] . PHP_EOL; }
}

echo PHP_EOL . '(a/c) article with the Gazette masthead' . PHP_EOL;
[$gid, $gMast, $gDate, $gEdtf] = $GAZ;
$e = $get($gid); $h = $has($e);
if (!isset($h['sourceLine'])) { $bad[] = "#$gid has no sourceLine"; }
else {
    $g = $guard($e, 'originalPublishDate', $gDate);
    if ($g === 'done' && (string)$e->sourceLine === $gMast && (string)$e->originalPublishDateEdtf === $gEdtf) { echo "  #$gid already done" . PHP_EOL; $done++; }
    elseif ($g !== 'bad') {
        if ((string)$e->originalPublishDate !== $gMast && $g === 'todo') { $bad[] = "#$gid masthead differs"; }
        if (!in_array((string)$e->sourceLine, ['', $gMast], true)) { $bad[] = "#$gid sourceLine already \"{$e->sourceLine}\""; }
        if (!in_array((string)$e->originalPublishDateEdtf, ['', $gEdtf], true)) { $bad[] = "#$gid Edtf already \"{$e->originalPublishDateEdtf}\""; }
        $plan[$gid] = [$e, ['sourceLine' => $gMast, 'originalPublishDate' => $gDate, 'originalPublishDateEdtf' => $gEdtf], 'a/c'];
        echo "  #$gid {$e->title}" . PHP_EOL . "     sourceLine: \"{$e->sourceLine}\" -> \"$gMast\"" . PHP_EOL
            . "     originalPublishDate: \"{$e->originalPublishDate}\" -> \"$gDate\"" . PHP_EOL
            . "     originalPublishDateEdtf: \"{$e->originalPublishDateEdtf}\" -> \"$gEdtf\"" . PHP_EOL;
    }
}

echo PHP_EOL . '(b) photographs: caption fragment in photoDate' . PHP_EOL;
foreach ($FRAG as $id => $said) {
    $e = $get($id); $h = $has($e);
    if (!isset($h['photoDate'])) { $bad[] = "#$id has no photoDate"; continue; }
    $g = $guard($e, 'photoDate', '');
    if ($g === 'done') { echo "  #$id already done" . PHP_EOL; $done++; continue; }
    if ($g === 'bad') { continue; }
    $inBody($e, (string)$e->photoDate);
    if ((string)$e->photoDateEdtf !== '') { $bad[] = "#$id photoDateEdtf is \"{$e->photoDateEdtf}\": check it against the caption first"; continue; }
    $plan[$id] = [$e, ['photoDate' => ''], 'b'];
    echo "  #$id {$cut($e->title, 50)}" . PHP_EOL . "     photoDate: \"{$e->photoDate}\" -> \"\"   (photoDateEdtf empty; $said)" . PHP_EOL;
}

echo PHP_EOL . '(c) photographs: news-story masthead in photoDate' . PHP_EOL;
foreach ($MAST as $id => $said) {
    $e = $get($id); $h = $has($e);
    if (!isset($h['photoDate'], $h['webmasterNoteBottom'])) { $bad[] = "#$id lacks photoDate or webmasterNoteBottom"; continue; }
    $wnb = (string)$e->webmasterNoteBottom;
    $g = $guard($e, 'photoDate', '');
    if ($g === 'bad') { continue; }
    if ($g === 'done') {
        if (str_contains($wnb, 'News story: ')) { echo "  #$id already done" . PHP_EOL; $done++; } else { $bad[] = "#$id photoDate empty but no masthead in the note"; }
        continue;
    }
    $mast = (string)$e->photoDate;
    $inBody($e, $mast);
    if (!preg_match('~^(The )?Newhall Signal and Saugus Enterprise \| \w+day, \w+ \d{1,2}, \d{4}\.$~', $mast)) { $bad[] = "#$id is not a masthead: $mast"; continue; }
    if ((string)$e->photoDateEdtf !== '') { $bad[] = "#$id photoDateEdtf is \"{$e->photoDateEdtf}\""; continue; }
    $line = 'News story: ' . $mast;
    $newWnb = $wnb === '' ? $line : rtrim($wnb) . "\n" . $line;
    $plan[$id] = [$e, ['photoDate' => '', 'webmasterNoteBottom' => $newWnb], 'c'];
    echo "  #$id {$cut($e->title, 50)}" . PHP_EOL . "     photoDate: \"$mast\" -> \"\"" . PHP_EOL
        . '     webmasterNoteBottom: ' . json_encode($wnb, JSON_UNESCAPED_UNICODE) . ' -> ' . json_encode($newWnb, JSON_UNESCAPED_UNICODE) . PHP_EOL
        . "     photoDate left empty; the item's own date, for Nathan: $said" . PHP_EOL;
}

echo PHP_EOL . '(d) 25 Baker Ranch Rodeo program pages (#3191 to #3239): left as they are' . PHP_EOL;

echo PHP_EOL . '(e) #28307' . PHP_EOL;
$e = $get(28307); $h = $has($e);
$OLD_ROW = 'Tuesday, October 4, 2016 (event); text undated';
$ROW = ['printed' => 'Tuesday, October 4, 2016', 'iso' => '2016-10-04 00:00:00', 'granularity' => 'day', 'label' => 'the dedication, as the text gives it; the release itself is undated; not yet checked against the original', 'confirmed' => false, 'rejected' => false];
if (!str_contains($ws($e->body), 'On Tuesday, October 4, 2016, the City of Santa Clarita hosted')) { $bad[] = '#28307 body does not give the date'; }
$rows = array_values(array_filter($e->recordDates ?? [], 'is_array'));
$g = $guard($e, 'originalPublishDate', '');
$rowDone = count($rows) === 1 && ($rows[0]['printed'] ?? '') === $ROW['printed'];
if ($g === 'done' && $rowDone) { echo '  #28307 already done' . PHP_EOL; $done++; }
elseif ($g !== 'bad') {
    if (count($rows) !== 1 || !in_array($rows[0]['printed'] ?? '', [$OLD_ROW, $ROW['printed']], true)) { $bad[] = '#28307 recordDates is not the one row recorded'; }
    $plan[28307] = [$e, ['originalPublishDate' => '', 'recordDates' => [$ROW]], 'e'];
    $isoWas = ($rows[0]['iso'] ?? null) instanceof \DateTimeInterface ? (clone $rows[0]['iso'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d') : '?';
    echo "  #28307 {$e->title}" . PHP_EOL . "     originalPublishDate: \"{$e->originalPublishDate}\" -> \"\"" . PHP_EOL
        . "     recordDates row: \"" . ($rows[0]['printed'] ?? '') . "\" $isoWas " . ($rows[0]['granularity'] ?? '') . " -> \"{$ROW['printed']}\" 2016-10-04 day" . PHP_EOL
        . "     originalPublishDateEdtf left as \"{$e->originalPublishDateEdtf}\"" . PHP_EOL;
}

/* recordProvenance, where the layout has it. */
foreach ($plan as $id => [$pe, $vals]) {
    if (!isset($has($pe)['recordProvenance'])) { continue; }
    $p = trim((string)$pe->recordProvenance);
    if (str_contains($p, 'fix_date_field_prose_2026_10_04')) { continue; }
    $p = $p === '' ? $PROV : "$p; $PROV";
    if (mb_strlen($p) > 255) { $bad[] = "#$id recordProvenance would be " . mb_strlen($p) . ' chars'; continue; }
    $plan[$id][1]['recordProvenance'] = $p;
}
$provN = count(array_filter($plan, fn($p) => isset($p[1]['recordProvenance'])));
if (preg_match('~\x{2014}~u', json_encode(array_map(fn($p) => $p[1], $plan), JSON_UNESCAPED_UNICODE) . $PROV)) { $bad[] = 'an em dash in a new value'; }

$by = array_count_values(array_column($plan, 2));
echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
echo 'to change: ' . count($plan) . ' records (' . implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($by), $by)) . "); already done: $done; recordProvenance on $provN (no layout here carries it)" . PHP_EOL;
echo 'datelines set: ' . count(array_filter($plan, fn($p) => isset($p[1]['originalPublishDateEdtf']))) . ' (incl. the Gazette issue)' . PHP_EOL;
echo 'HELD for Nathan: ' . ($held ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $held) : 'none') . PHP_EOL;
echo 'Nothing was invented: no photograph date is set; the candidates above are reported only.' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply. A second run after an apply is a no-op.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $id => [$pe, $vals]) { $pe->setFieldValues($vals); if (!$el->saveElement($pe)) { throw new \RuntimeException("#$id: " . json_encode($pe->getFirstErrors())); } $n++; }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$short = [];
foreach ($plan as $id => [$pe, $vals]) {
    $b = $get($id);
    foreach ($vals as $k => $v) {
        if ($k === 'recordDates') {
            $r = ($b->recordDates ?? [])[0] ?? [];
            $iso = ($r['iso'] ?? null) instanceof \DateTimeInterface ? (clone $r['iso'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d') : '';
            if (count($b->recordDates ?? []) !== 1 || ($r['printed'] ?? '') !== $v[0]['printed'] || $iso !== '2016-10-04' || ($r['granularity'] ?? '') !== 'day') { $short[] = "#$id recordDates"; }
        } elseif ((string)$b->getFieldValue($k) !== $v) { $short[] = "#$id $k"; }
    }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_date_field_prose_2026_10_04.php', $n, $short ? 'SHORT' : 'verified', 'date fields holding prose: article datelines set, caption fragments cleared, mastheads to the source line or bottom note, #28307 publish date cleared');
if ($short) { throw new \RuntimeException('fix_date_field_prose_2026_10_04: ' . implode(', ', $short)); }
