/**
 * The St. Francis Dam: a place record for the dam site, then the event record for the failure and the flood
 * (Nathan, 5 October 2026: "The St Francis Dam record, with the most care. 411 as Leon's 2018 revision of
 * Stansell's 431, with the reason. One record for the failure and the flood.").
 *
 * IN ORDER, EACH PART REFUSING ON ANY MISMATCH
 *   (a) PLACE "St. Francis Dam": the site in San Francisquito Canyon, placeType site, California Historical
 *       Landmark No. 919 in placeChlNumber, completed May 1926 (the Historical Society's plaque of 1978, as the
 *       dedication program HS7801 prints it, and LW2054), five sourced footnotes. Fields as the other place
 *       records (#605 Heritage Junction): body, footnotes, placeType, neighborhood, plus the landmark number,
 *       aliases, era and theme. No coordinates, address or CHL link: no source in hand gives them.
 *   (b) EVENT "St. Francis Dam Disaster" from inventory/review/st-francis-dam-draft-v2-2026-10-05.json, the
 *       draft whose every quotation was rechecked against the mirror pages on 5 October 2026. The file's SHA-256
 *       is pinned below: an edit after that check refuses the run until it is checked again. Relations only
 *       where the draft ties the target to a footnote, and the footnote names it; the place made in (a); the
 *       draft's three photographs take the event in photoEvents (appended, never replacing).
 *       The body gives no construction start: the plaque's August 1924 and the timeline's December 1, 1924
 *       disagree (v2 research lead 9).
 *   (c) Nothing is written to William Mulholland #16432 (his "431" is a separate decision, forNathan 5).
 * Part (b) never runs if (a) refused. Idempotent: the place is matched on title plus this script's
 * provenance, the event on title plus landmark number; a photograph already carrying the event is skipped.
 *
 * Writes the record as it would read to inventory/review/st-francis-dam-loader-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_st_francis_dam_2026_10_05.php'))"
 */

use craft\elements\{Entry, Category};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$SCRIPT = 'create_st_francis_dam_2026_10_05.php';
$root = \Craft::getAlias('@root');
$V2 = 'inventory/review/st-francis-dam-draft-v2-2026-10-05.json';
$V2_SHA = '82d551c74d6927b9a10d331f26a910bfbca343e4391f668a6375c1a0fc8cc11e';
$OUT = 'inventory/review/st-francis-dam-loader-dry-run-2026-10-05.md';
$DO_NOT_TOUCH = [16432 => 'William Mulholland'];
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', (string)$s));

/* Shared checks. */
$textFaults = function (string $label, string $s): array {
    $b = [];
    if (preg_match('~\x{2014}~u', $s)) { $b[] = "$label: an em dash"; }
    if (preg_match('~\.\.\.|\x{2026}~u', $s)) { $b[] = "$label: an ellipsis (none survives the v2 check)"; }
    if (str_contains($s, '\\')) { $b[] = "$label: a literal backslash"; }
    return $b;
};
$refFaults = function (string $label, string $body, int $n): array {
    preg_match_all('~\[(\d+)\]~', $body, $m); $cited = array_map('intval', $m[1]); $b = [];
    foreach ($cited as $c) { if ($c < 1 || $c > $n) { $b[] = "$label: [$c] has no footnote"; } }
    foreach (range(1, $n) as $i) { if (!in_array($i, $cited, true)) { $b[] = "$label: footnote $i is never cited"; } }
    return $b;
};
$layoutFaults = function (string $label, $et, array $handles): array {
    $have = []; foreach ($et->getFieldLayout()->getCustomFields() as $f) { $have[$f->handle] = $f; }
    $b = []; foreach ($handles as $h) { if (!isset($have[$h])) { $b[] = "$label: no field $h on the {$et->handle} type"; } }
    return $b;
};
$catFault = function (int $id, string $group, string $titleStart): ?string {
    $c = Category::find()->id($id)->one();
    if (!$c) { return "category #$id missing"; }
    if ($c->group->handle !== $group || !str_starts_with($c->title, $titleStart)) { return "category #$id is {$c->group->handle} / {$c->title}, expected $group / $titleStart"; }
    return null;
};
$entryFault = function (int $id, string $section, string $title, bool $prefix = false): ?string {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e) { return "#$id missing"; }
    if ($e->section->handle !== $section) { return "#$id is in {$e->section->handle}, expected $section"; }
    if ($prefix ? !str_starts_with($title, $e->title) : $e->title !== $title) { return "#$id is \"{$e->title}\", expected \"$title\""; }
    return null;
};

/* ------------------------------------------------------------------ (a) the place */
$badA = [];
$PLACE_TITLE = 'St. Francis Dam';
$PLACE_PROV = "$SCRIPT, 5 October 2026: the dam site, made before the event from the v2 draft";
$PLACE_BODY = implode("\n\n", [
    'The St. Francis Dam stood at the head of San Francisquito Canyon, above Saugus. The City of Los Angeles built it to store an emergency water supply, as a unit of the Los Angeles Aqueduct, and completed it in May 1926: a concrete dam 185 feet high.[1][2][5] It failed on the night of March 12, 1928, and its flood ran 54 miles down the Santa Clara River Valley to the sea.[1][2]',
    'The site was registered as California Historical Landmark No. 919, the St. Francis Dam Disaster Site, on April 26, 1978. The state\'s plaque, placed at Power House No. 2 on November 5, 1979, says the dam "stood a mile and a half north of this spot."[3][2] In 2019 Congress designated the dam site a national monument and national memorial, the Saint Francis Dam Disaster National Memorial and National Monument of section 1111 of S.47.[3][4]',
]);
$PLACE_NOTES = [
    'A.M. [Almer M.] Newhall and Geo. A. Newhall Jr., "Report on St. Francis Dam Flood for The Newhall Land and Farming Company," San Francisco, March 24, 1928, as carried on SCVHistory.com, /scvhistory/nlf-stfrancis.htm: the dam was "Completed about two years ago by the City of Los Angeles for the storage of an emergency water supply"; the flood followed "the breaking of the St. Francis dam at the head of the San Francisquito Canyon on or about 11:30 P.M. Monday, March 12th, 1928."',
    'Santa Clarita Valley Historical Society, "Program: Dedication of California Historical Landmark No. 919, St. Francis Dam Disaster Site," May 21, 1978, HS7801, as carried on SCVHistory.com, /gif/galleries/hs7801/index.html. The Society\'s plaque: "On this site in August of 1924 construction started on the St. Francis Dam, a unit of the Los Angeles Aqueduct. When it was completed in May of 1926, this concrete Dam stood 185 feet above Streambed, impounding a 610 surface-acre lake." "At least 425 lives were lost in the 5½ hours that it took the released water to travel 54 miles down the Santa Clara River Valley to the sea at Ventura." The state\'s text for No. 919, as the same page gives it from the Office of Historic Preservation: "The 185-foot concrete St. Francis Dam, part of the Los Angeles aqueduct system, stood a mile and a half north of this spot." Its location: "San Francisquito Power Plant No. 2, 32300 N San Francisquito Canyon Rd., 9.2 mi N of Saugus".',
    'The notes to "Historical Society\'s Dam Marker Placed, Stolen, 1978; State Marker Placed, 1979," as carried on SCVHistory.com, /scvhistory/sg19780522dam.htm: "April 26, 1978: Registration of dam site as State Historical Landmark No. 919." "November 5, 1979: Placement of State Historical Landmark plaque at Power House No. 2." "About the same time, again at the urging of the Historical Society and the Community Hiking Club, Congress designated the dam site a National Monument and National Memorial."',
    '"Saint Francis Dam Disaster National Memorial and National Monument," section 1111 of S.47, John D. Dingell Jr. Conservation, Management and Recreation Act, 116th Congress, 2019, as carried on SCVHistory.com, /scvhistory/s47_2019.htm. SCVHistory.com timeline, /scvhistory/timeline.htm, 2019: "March 12: On 91st anniversary, President Trump signs S.47 lands package into law, establishing St. Francis Dam Disaster National Memorial & Monument."',
    'Caption to LW2054, "William Mulholland, St. Francis Dam Builder," photograph #28063 in this archive (/scvhistory/lw2054.htm): the dam stood "high above Saugus in San Francisquito Canyon"; "Dam construction started in August 1924; water began to fill the reservoir on March 1, 1926. Two months later the dam was completed."',
];
$PLACE = [
    'placeType' => 'site', 'placeChlNumber' => '919',
    'dateEstablished' => 'May 1926', 'dateEstablishedEdtf' => '1926-05',
    'placeAliases' => "St. Francis Dam Disaster Site\nSaint Francis Dam",
    'neighborhood' => [209], 'historicalEra' => [163], 'recordTags' => [18942],
    'recordProvenance' => $PLACE_PROV,
];
$placeSec = Craft::$app->getEntries()->getSectionByHandle('places');
$placeType = null;
if (!$placeSec) { $badA[] = 'no places section'; }
else {
    foreach ($placeSec->getEntryTypes() as $et) { if ($et->handle === 'place') { $placeType = $et; } }
    if (!$placeType) { $badA[] = 'no place entry type'; }
}
if ($placeType) {
    $badA = array_merge($badA, $layoutFaults('place', $placeType, array_merge(['body', 'footnotes'], array_keys($PLACE))));
    $opts = array_column(Craft::$app->getFields()->getFieldByHandle('placeType')->options, 'value');
    if (!in_array($PLACE['placeType'], $opts, true)) { $badA[] = 'placeType "site" is not an option'; }
    $lim = Craft::$app->getFields()->getFieldByHandle('recordProvenance')->charLimit;
    if ($lim && mb_strlen($PLACE_PROV) > $lim) { $badA[] = "recordProvenance is " . mb_strlen($PLACE_PROV) . " characters, the limit $lim"; }
    $lim = Craft::$app->getFields()->getFieldByHandle('dateEstablishedEdtf')->charLimit;
    if ($lim && mb_strlen($PLACE['dateEstablishedEdtf']) > $lim) { $badA[] = 'dateEstablishedEdtf over its limit'; }
}
$badA = array_merge($badA, $textFaults('place body', $PLACE_BODY), $refFaults('place body', $PLACE_BODY, count($PLACE_NOTES)));
foreach ($PLACE_NOTES as $i => $t) { $badA = array_merge($badA, $textFaults('place note ' . ($i + 1), $t)); }
foreach ([[209, 'neighborhood', 'San Francisquito Canyon'], [163, 'historicalEra', 'St. Francis Dam Era'], [18942, 'theme', 'St. Francis Dam']] as [$cid, $g, $t]) {
    if ($f = $catFault($cid, $g, $t)) { $badA[] = $f; }
}
$placeHave = null;
foreach (Entry::find()->section('places')->status(null)->title($PLACE_TITLE)->all() as $p) {
    if (str_starts_with((string)$p->recordProvenance, $SCRIPT)) { $placeHave = $p; }
    else { $badA[] = "place #{$p->id} \"$PLACE_TITLE\" exists and this script did not make it"; }
}

/* ------------------------------------------------------------------ (b) the event */
$badB = [];
$raw = @file_get_contents("$root/$V2");
if ($raw === false) { $badB[] = "cannot read $V2"; $v = []; }
else {
    if (hash('sha256', $raw) !== $V2_SHA) { $badB[] = "$V2 changed since its quotations were checked (SHA-256 differs); recheck, then pin the new hash"; }
    $v = json_decode($raw, true) ?: [];
}
$F = $v['fields'] ?? [];
$EVENT_TITLE = (string)($v['title'] ?? '');
if ($EVENT_TITLE !== 'St. Francis Dam Disaster') { $badB[] = "v2 title is \"$EVENT_TITLE\""; }
$qc = $v['quotationCheck']['counts'] ?? null;
if (!$qc || ($qc['fail'] ?? 1) !== 0) { $badB[] = 'v2 does not record a quotation check with no failures'; }
$BODY = (string)($F['body'] ?? '');
$NOTES = $F['footnotes'] ?? [];
$N = count($NOTES);
$noteOf = fn(int $n) => (string)($NOTES[$n - 1] ?? '');
$badB = array_merge($badB, $textFaults('event body', $BODY), $refFaults('event body', $BODY, $N), $textFaults('significance', (string)($F['eventSignificance'] ?? '')));
foreach ($NOTES as $i => $t) { $badB = array_merge($badB, $textFaults('event note ' . ($i + 1), $t)); }
$EDITOR = $F['editorNotes'] ?? [];
foreach ($EDITOR as $i => $r) { $badB = array_merge($badB, $textFaults('editor note ' . ($i + 1), $r['heading'] . ' ' . $r['note'])); }
if (($EDITOR[0]['heading'] ?? '') !== 'Content advisory' || ($EDITOR[0]['position'] ?? '') !== 'top') { $badB[] = 'the first editor note is not the content advisory in the top position (DATA-MODEL, Content advisories)'; }

/* A relation is made only where the draft ties it to footnotes and at least one of them names the target. */
$tie = function (string $kind, array $row, string $section, array $terms) use ($noteOf, $N, $entryFault): array {
    $b = []; $nums = $row['footnotes'] ?? [];
    if ($f = $entryFault((int)$row['id'], $section, (string)$row['title'])) { $b[] = "$kind: $f"; }
    if (!$nums) { $b[] = "$kind #{$row['id']}: no footnote ties it"; return $b; }
    $named = false;
    foreach ($nums as $n) {
        if ($n < 1 || $n > $N) { $b[] = "$kind #{$row['id']}: footnote $n does not exist"; continue; }
        foreach ($terms as $t) { if (str_contains($noteOf($n), $t)) { $named = true; } }
    }
    if (!$named) { $b[] = "$kind #{$row['id']}: none of footnotes " . implode(', ', $nums) . ' names it'; }
    return $b;
};
$TERMS = [
    16432 => ['Mulholland'], 16356 => ['Hart'], 15919 => ['Carey'],
    599 => ['Carey ranch', 'Harry Carey'], 2536 => ['Ruiz Cemetery'], 15596 => ['Santa Clara River'],
    15691 => ['Newhall Land'], 16101 => ['S.P.'], 15493 => ['Historical Society'],
];
$REL = ['eventPersons' => ['persons', $F['eventPersons'] ?? []], 'eventPlaces' => ['places', $F['eventPlaces'] ?? []], 'eventOrganizations' => ['organizations', $F['eventOrganizations'] ?? []]];
$relIds = [];
foreach ($REL as $h => [$sec, $rows]) {
    $relIds[$h] = [];
    foreach ($rows as $row) {
        $id = (int)$row['id'];
        if (!isset($TERMS[$id])) { $badB[] = "$h #$id: not in the reviewed list of ties; add its terms after checking its footnotes"; continue; }
        $badB = array_merge($badB, $tie($h, $row, $sec, $TERMS[$id]));
        $relIds[$h][] = $id;
    }
}
/* Articles: only those the draft says a footnote cites, and that footnote gives the record number. */
$articles = []; $articlesHeld = [];
foreach ($F['eventArticles'] ?? [] as $row) {
    $id = (int)$row['id'];
    if (preg_match('~cited in footnote (\d+)~', (string)($row['note'] ?? ''), $m)) {
        if (!str_contains($noteOf((int)$m[1]), "#$id")) { $badB[] = "article #$id: footnote {$m[1]} does not name #$id"; }
        if ($f = $entryFault($id, 'articles', (string)$row['title'], true)) { $badB[] = "article: $f"; }
        $articles[] = $id;
    } else { $articlesHeld[] = $row; }
}
/* Photographs: each takes the event in photoEvents, appended. */
$photos = [];
foreach ($F['photographs'] ?? [] as $row) {
    $id = (int)$row['id'];
    if ($f = $entryFault($id, 'photographs', (string)$row['title'])) { $badB[] = "photograph: $f"; continue; }
    $p = Entry::find()->id($id)->status(null)->one();
    $has = []; foreach ($p->getFieldLayout()->getCustomFields() as $fl) { $has[$fl->handle] = true; }
    if (!isset($has['photoEvents'])) { $badB[] = "photograph #$id has no photoEvents field"; continue; }
    if (!isset($has['photoSourceCode']) || (string)$p->getFieldValue('photoSourceCode') !== $row['photoId']) { $badB[] = "photograph #$id is " . ($p->photoSourceCode ?? '?') . ", expected {$row['photoId']}"; }
    $named = false; foreach ($row['footnotes'] ?? [] as $n) { if (str_contains($noteOf((int)$n), $row['photoId'])) { $named = true; } }
    if (!$named) { $badB[] = "photograph #$id: no footnote names {$row['photoId']}"; }
    $photos[$id] = ['row' => $row, 'el' => $p, 'current' => $p->photoEvents->status(null)->ids()];
}
foreach (array_keys($DO_NOT_TOUCH) as $id) { if (isset($photos[$id])) { $badB[] = "#$id is on the do-not-touch list"; } }

$CATS = ['recordTags' => [18942, 'theme', 'St. Francis Dam'], 'historicalEra' => [163, 'historicalEra', 'St. Francis Dam Era'],
         'historicalPeriod' => [175, 'historicalPeriod', '1920-1929'], 'neighborhood' => [209, 'neighborhood', 'San Francisquito Canyon']];
foreach ($CATS as $h => [$cid, $g, $t]) { if ($f = $catFault($cid, $g, $t)) { $badB[] = "$h: $f"; } }
foreach (['historicalEra', 'recordTags', 'neighborhood'] as $h) {
    if (($F[$h][0]['id'] ?? null) !== $CATS[$h][0]) { $badB[] = "$h in v2 is not #{$CATS[$h][0]}"; }
}

$EVENT = [
    'body' => $BODY,
    'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($NOTES), $NOTES),
    'editorNotes' => array_map(fn($r) => ['heading' => $r['heading'], 'note' => $r['note'], 'position' => $r['position']], $EDITOR),
    'researchLeads' => implode("\n\n", $v['researchLeads'] ?? []),
    'eventDate' => (string)($F['eventDate'] ?? ''), 'eventDateEdtf' => (string)($F['eventDateEdtf'] ?? ''),
    'eventDateEnd' => (string)($F['eventDateEnd'] ?? ''), 'startEvidence' => (string)($F['startEvidence'] ?? ''),
    'eventChlNumber' => (string)($F['eventChlNumber'] ?? ''), 'eventSignificance' => (string)($F['eventSignificance'] ?? ''),
    'recordTags' => [18942], 'historicalEra' => [163], 'historicalPeriod' => [175], 'neighborhood' => [209],
    'eventPersons' => $relIds['eventPersons'], 'eventPlaces' => $relIds['eventPlaces'],
    'eventOrganizations' => $relIds['eventOrganizations'], 'eventArticles' => $articles,
];
if ($EVENT['eventDate'] !== 'March 12, 1928' || $EVENT['eventDateEdtf'] !== '1928-03-12' || $EVENT['eventChlNumber'] !== '919') { $badB[] = 'v2 date or landmark number moved'; }
$evSec = Craft::$app->getEntries()->getSectionByHandle('events'); $evType = null;
if ($evSec) { foreach ($evSec->getEntryTypes() as $et) { if ($et->handle === 'event') { $evType = $et; } } }
if (!$evType) { $badB[] = 'no events/event type'; }
else {
    $badB = array_merge($badB, $layoutFaults('event', $evType, array_keys($EVENT)));
    $opts = array_column(Craft::$app->getFields()->getFieldByHandle('startEvidence')->options, 'value');
    if (!in_array($EVENT['startEvidence'], $opts, true)) { $badB[] = "startEvidence \"{$EVENT['startEvidence']}\" is not an option"; }
    $lim = Craft::$app->getFields()->getFieldByHandle('eventDateEdtf')->charLimit;
    if ($lim && mb_strlen($EVENT['eventDateEdtf']) > $lim) { $badB[] = 'eventDateEdtf over its limit'; }
}
$eventHave = null;
foreach (Entry::find()->section('events')->status(null)->title($EVENT_TITLE)->all() as $e) {
    if ((string)$e->eventChlNumber === '919') { $eventHave = $e; } else { $badB[] = "event #{$e->id} \"$EVENT_TITLE\" exists without landmark 919"; }
}

/* ------------------------------------------------------------------ the plan */
$t = fn($id) => Entry::find()->id($id)->status(null)->one()?->title ?? '?';
echo '(a) PLACE: ' . ($placeHave ? "#{$placeHave->id} \"$PLACE_TITLE\" exists (made by this script), nothing to do" : "create \"$PLACE_TITLE\": site, CHL 919, May 1926, " . count($PLACE_NOTES) . ' notes, ' . str_word_count($PLACE_BODY) . ' words') . PHP_EOL;
echo '    REFUSED: ' . ($badA ? implode(' | ', $badA) : 'none') . PHP_EOL;
echo '(b) EVENT: ' . ($eventHave ? "#{$eventHave->id} \"$EVENT_TITLE\" exists, not recreated" : "create \"$EVENT_TITLE\": March 12, 1928, CHL 919, $N notes, " . count($EDITOR) . ' editor notes, ' . str_word_count(preg_replace('~\[\d+\]~', '', $BODY)) . ' words') . PHP_EOL;
foreach (['eventPersons', 'eventPlaces', 'eventOrganizations', 'eventArticles'] as $h) {
    $list = array_map(fn($id) => "#$id " . $t($id), $EVENT[$h]);
    if ($h === 'eventPlaces') { array_unshift($list, $placeHave ? "#{$placeHave->id} $PLACE_TITLE" : "(new) $PLACE_TITLE"); }
    echo "    $h: " . implode('; ', $list) . PHP_EOL;
}
foreach ($photos as $id => $p) { echo "    photograph #$id {$p['row']['photoId']}: photoEvents " . ($eventHave && in_array($eventHave->id, $p['current']) ? 'has it' : 'append the event to ' . json_encode($p['current'])) . PHP_EOL; }
echo '    articles held back (no footnote ties them): ' . implode('; ', array_map(fn($r) => "#{$r['id']} {$r['title']}", $articlesHeld)) . PHP_EOL;
echo '    REFUSED: ' . ($badA ? 'part (a) refused, so (b) cannot run' . ($badB ? ' | ' : '') : '') . ($badB ? implode(' | ', $badB) : ($badA ? '' : 'none')) . PHP_EOL;
echo '(c) MULHOLLAND #16432: nothing written (a separate decision)' . PHP_EOL;

/* ------------------------------------------------------------------ the record as it would read */
$md = [];
$md[] = '# St. Francis Dam: the loader\'s dry run, 5 October 2026';
$md[] = '';
$md[] = "Written by `scripts/import/$SCRIPT` in " . ($APPLY ? 'an apply' : 'a dry run') . '. Nothing is written to Craft by a dry run. The event is read from `' . $V2 . '` (SHA-256 `' . substr($V2_SHA, 0, 16) . '...`).';
$md[] = '';
$md[] = '**Refusals.** Place: ' . ($badA ? implode('; ', $badA) : 'none') . '. Event: ' . ($badB ? implode('; ', $badB) : 'none') . '.';
$md[] = '';
$md[] = '## Quotation check';
$md[] = '';
$md[] = sprintf('%d quotations in the v1 draft (body, footnotes, editor notes, relation notes and the literature list) were checked word for word against the mirror pages on /Volumes/Reggie, and against Craft for the four that are Craft titles or record text: %d PASS, %d CORRECTED in v2, %d FAIL. Ellipses: %s.',
    $qc['checked'] ?? 0, $qc['pass'] ?? 0, $qc['corrected'] ?? 0, $qc['fail'] ?? 0, $v['quotationCheck']['ellipses'] ?? '');
$md[] = '';
$md[] = '| # | Where | Quotation | Result | Page | Note |';
$md[] = '| --- | --- | --- | --- | --- | --- |';
$cell = fn($s) => str_replace(['|', "\n"], ['\\|', ' '], (string)$s);
foreach ($v['quotationCheck']['results'] ?? [] as $i => $r) {
    $q = $r['quotation']; if (mb_strlen($q) > 140) { $q = mb_substr($q, 0, 140) . ' [cut here for the table]'; }
    $md[] = '| ' . ($i + 1) . ' | ' . $cell($r['where']) . ' | ' . $cell($q) . ' | ' . $r['status'] . ' | ' . $cell($r['page']) . ' | ' . $cell($r['note']) . ' |';
}
$md[] = '';
$md[] = '## Changes from v1 to v2';
$md[] = '';
foreach ($v['v2Changes'] ?? [] as $c) { $md[] = "{$c['n']}. **{$c['where']}.** Was: {$cell($c['was'])} Now: {$cell($c['now'])} Why: {$c['why']}"; }
$md[] = '';
$md[] = '## (a) Place: ' . $PLACE_TITLE;
$md[] = '';
$md[] = '- **Status:** ' . ($placeHave ? "exists as #{$placeHave->id}" : 'would be created');
foreach ($PLACE as $h => $val) {
    if (is_array($val)) { $val = implode('; ', array_map(fn($id) => "#$id " . (Category::find()->id($id)->one()?->title ?? '?'), $val)); }
    $md[] = "- **$h:** " . str_replace("\n", ' / ', (string)$val);
}
$md[] = '- **Left empty:** placeLat, placeLng, placeAddress, placeChlUrl, legacyUrl (no source in hand gives them)';
$md[] = '';
foreach (explode("\n\n", $PLACE_BODY) as $p) { $md[] = $p; $md[] = ''; }
foreach ($PLACE_NOTES as $i => $n) { $md[] = ($i + 1) . '. ' . $n; }
$md[] = '';
$md[] = '## (b) Event: ' . $EVENT_TITLE;
$md[] = '';
$md[] = '**Editor\'s note, ' . $EDITOR[0]['heading'] . ' (top):** ' . $EDITOR[0]['note'];
$md[] = '';
foreach (['eventDate', 'eventDateEdtf', 'eventDateEnd', 'startEvidence', 'eventChlNumber', 'eventSignificance'] as $h) { $md[] = "- **$h:** {$EVENT[$h]}"; }
foreach (['recordTags', 'historicalEra', 'historicalPeriod', 'neighborhood'] as $h) { $md[] = "- **$h:** " . implode('; ', array_map(fn($id) => "#$id " . (Category::find()->id($id)->one()?->title ?? '?'), $EVENT[$h])); }
$md[] = '- **featuredImage, bandImage:** empty (forNathan 3)';
$md[] = '';
foreach (explode("\n\n", $BODY) as $p) { $md[] = $p; $md[] = ''; }
foreach ($NOTES as $i => $n) { $md[] = ($i + 1) . '. ' . $n; }
$md[] = '';
foreach (array_slice($EDITOR, 1) as $r) { $md[] = "**Editor's note, {$r['heading']} ({$r['position']}):** {$r['note']}"; $md[] = ''; }
$md[] = '### Relations';
$md[] = '';
$ties = []; foreach (['eventPersons', 'eventPlaces', 'eventOrganizations'] as $h) { foreach ($F[$h] ?? [] as $r) { $ties[(int)$r['id']] = $r; } }
$md[] = '- **eventPlaces:** ' . ($placeHave ? "#{$placeHave->id}" : '(new)') . " $PLACE_TITLE, the dam itself (part a)";
foreach (['eventPersons', 'eventPlaces', 'eventOrganizations', 'eventArticles'] as $h) {
    foreach ($EVENT[$h] as $id) {
        $r = $ties[$id] ?? null;
        $md[] = "- **$h:** #$id " . $t($id) . ($r ? ' (notes ' . implode(', ', $r['footnotes']) . (isset($r['tie']) ? '; ' . $r['tie'] : '') . ')' : ' (cited in its footnote)');
    }
}
foreach ($photos as $id => $p) { $md[] = "- **photograph #$id {$p['row']['photoId']}** takes the event in photoEvents (notes " . implode(', ', $p['row']['footnotes']) . '; ' . $p['row']['tie'] . ')'; }
$md[] = '- **Held back, no footnote ties them:** ' . implode('; ', array_map(fn($r) => "#{$r['id']} {$r['title']}" . (isset($r['note']) ? " ({$r['note']})" : ''), $articlesHeld));
$md[] = '- **Not written:** #16432 William Mulholland (part c)';
$md[] = '';
$md[] = '### Research leads (researchLeads, not shown on the page)';
$md[] = '';
foreach ($v['researchLeads'] ?? [] as $l) { $md[] = '- ' . $l; }
$md[] = '';
$md[] = '## Waiting on Nathan';
$md[] = '';
foreach ($v['forNathan'] ?? [] as $l) { $md[] = '- ' . $l; }
$md[] = '';
file_put_contents("$root/$OUT", implode("\n", $md));
echo "wrote $OUT" . PHP_EOL;

if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written to Craft. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($badA) { echo 'REFUSING (a), and so (b)' . PHP_EOL; return; }

/* ------------------------------------------------------------------ apply (a) */
$els = Craft::$app->getElements(); $rows = 0;
if (!$placeHave) {
    $p = new Entry(); $p->sectionId = $placeSec->id; $p->setTypeId($placeType->id); $p->title = $PLACE_TITLE;
    $p->setFieldValues(array_merge($PLACE, ['body' => $PLACE_BODY,
        'footnotes' => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($PLACE_NOTES), $PLACE_NOTES)]));
    if (!$els->saveElement($p)) { throw new \RuntimeException('place: ' . json_encode($p->getFirstErrors())); }
    $rows++;
    $placeHave = Entry::find()->id($p->id)->status(null)->one();
    $okA = trim((string)$placeHave->body) === trim($PLACE_BODY) && $placeHave->placeChlNumber === '919' && count($placeHave->footnotes ?? []) === count($PLACE_NOTES)
        && $placeHave->neighborhood->ids() == [209];
    echo 'PLACE READ-BACK ' . ($okA ? "OK: #{$placeHave->id} {$placeHave->url}" : 'SHORT') . PHP_EOL;
    if (!$okA) { throw new \RuntimeException('place read-back failed; the event was not made'); }
}

/* ------------------------------------------------------------------ apply (b) */
if ($badB) { echo 'REFUSING (b); the place stands' . PHP_EOL; return; }
if (!$eventHave) {
    $EVENT['eventPlaces'] = array_merge([$placeHave->id], $EVENT['eventPlaces']);
    $e = new Entry(); $e->sectionId = $evSec->id; $e->setTypeId($evType->id); $e->title = $EVENT_TITLE;
    $e->setFieldValues($EVENT);
    if (!$els->saveElement($e)) { throw new \RuntimeException('event: ' . json_encode($e->getFirstErrors())); }
    $rows++;
    $eventHave = Entry::find()->id($e->id)->status(null)->one();
    /* status(null): keep unpublished targets when reading a relation back (silent-faults audit, 5 October 2026). */
    $okB = trim((string)$eventHave->body) === trim($BODY) && count($eventHave->footnotes ?? []) === $N && $eventHave->eventDateEdtf === '1928-03-12'
        && $eventHave->eventPlaces->status(null)->ids() == $EVENT['eventPlaces'] && count($eventHave->eventPersons->status(null)->ids()) === count($EVENT['eventPersons'])
        && count($eventHave->eventOrganizations->status(null)->ids()) === count($EVENT['eventOrganizations']) && ($eventHave->editorNotes[0]['position'] ?? '') === 'top';
    echo 'EVENT READ-BACK ' . ($okB ? "OK: #{$eventHave->id} {$eventHave->url}" : 'SHORT') . PHP_EOL;
    if (!$okB) { throw new \RuntimeException('event read-back failed; photographs not touched'); }
}
foreach ($photos as $id => $p) {
    if (isset($DO_NOT_TOUCH[$id])) { continue; }
    $el = Entry::find()->id($id)->status(null)->one();
    $cur = $el->photoEvents->status(null)->ids();
    if (in_array($eventHave->id, $cur)) { echo "photograph #$id has it" . PHP_EOL; continue; }
    $el->setFieldValue('photoEvents', array_merge($cur, [$eventHave->id]));
    if (!$els->saveElement($el)) { throw new \RuntimeException("photograph #$id: " . json_encode($el->getFirstErrors())); }
    $rows++;
    $back = Entry::find()->id($id)->status(null)->one()->photoEvents->status(null)->ids();
    echo "photograph #$id READ-BACK " . ($back == array_merge($cur, [$eventHave->id]) ? 'OK' : 'SHORT') . PHP_EOL;
}
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog($SCRIPT, $rows, 'verified', 'St. Francis Dam: place, event from v2 draft, photoEvents on the draft\'s photographs; Mulholland untouched');
echo "done: $rows saves. A second run is a no-op." . PHP_EOL;
