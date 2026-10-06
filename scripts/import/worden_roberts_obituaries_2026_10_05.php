/**
 * Connie Worden-Roberts's two obituaries, Carl Goldman, and the Choppé portrait (Nathan, 5 October 2026: "Two obituaries
 * for the same person ... Work out whether they are two genuine pieces or one imported twice ... The second is Carl
 * Goldman's. Set him as its author and create his record if he has none ... There is an image for these on Reggie. Find
 * it and bring it in.").
 *
 * The verdict: two pieces, not one imported twice. #28045 is the formal obituary at /scvhistory/obituary_conniewordenroberts.htm,
 * whose header prints her life dates in the byline slot and "Eternal Valley Memorial Park & Mortuary" as the dateline and
 * names no writer; the legacy obituaries index credits it "Eternal Valley, 8-12-2014". #28047 is Carl Goldman's tribute at
 * /scvhistory/khts081314.htm, printed "By Carl Goldman" / "AM-1220 KHTS | Wednesday, August 13, 2014", first person, about the
 * cross-valley connector. The two bodies share no run of six words. A third piece, Perry Smith's KHTS obituary of 12 August
 * 2014 (khts081214.htm), is in Craft as document #28305. Every one of the legacy pages lists all three in its sidebar.
 *
 * What this script does:
 *  A. Relating the two: nothing is written. The obituary entry type has no field that relates an obituary to another record
 *     of its kind (relatedArticles takes articles only; footnotesOn is a citation; derivedImageLinks is for images). Both
 *     already share obitSubject #16418, so her person page holds them together. The dry run says so.
 *  B. Carl Goldman: creates his person record if none exists (title, fullName, occupation, a three-sentence body and three
 *     footnotes, all from archive pages), and sets him as #28047's author through writtenBy. The obituary entry type does not
 *     carry writtenBy, so the author link waits on $ADD_WRITTENBY_TO_OBITUARY, which adds the existing writtenBy field to the
 *     obituary layout (a project-config change, Nathan's call). The obituary template does not render writtenBy yet either.
 *     No KHTS organization record exists, so no affiliation is made.
 *  C. The first obituary's author: none is printed, so no record is made. $FIX_FORMAL_PUBLICATION replaces #28045's
 *     publicationDetails "SCVHistory.com, August 2014" (not on the page) with the dateline as printed; off by default.
 *  D. The image: lw9501_large.jpg, Connie Worden-Roberts in a red suit, arms folded, before freeway light trails, signed in
 *     the image "Photo by Gary Choppe'" and credited on the formal page "Photo by Gary Choppé / Creative Image Photography".
 *     It leads the formal obituary and stands in the text of Goldman's tribute ("Photo by Gary Choppe | Click to enlarge.").
 *     Imported to archiveMedia/legacy/ from inventory/incoming/lw9501_large.jpg (copied from the Reggie mirror, sha256
 *     checked against the evidence file), with the legacy-mirror provenance fields, and put first in recordImages on both
 *     obituaries. Not set on her person record: that is Nathan's call.
 *
 * Evidence: inventory/review/worden-roberts-obituaries-evidence-2026-10-05.json (page and image sha256, header lines as
 * printed). Report: inventory/review/worden-roberts-obituaries-dry-run-2026-10-05.md.
 * Idempotent: a person matched by title, an asset matched by checksum or filename, a relation already set, are skipped and
 * nothing is saved. Writes no em dash. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/worden_roberts_obituaries_2026_10_05.php'))"
 */
use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$ADD_WRITTENBY_TO_OBITUARY = false;
$FIX_FORMAL_PUBLICATION = false;

$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $svc = Craft::$app->getEntries(); $fields = Craft::$app->getFields();
$ev = json_decode((string)@file_get_contents("$root/inventory/review/worden-roberts-obituaries-evidence-2026-10-05.json"), true);
$bad = []; $plan = []; $n = 0;
if (!$ev) { throw new \RuntimeException('evidence file missing or unreadable'); }

$FORMAL = 28045; $KHTS = 28047; $SUBJECT = 16418;
$IMG = "$root/inventory/incoming/lw9501_large.jpg"; $IMG_NAME = 'lw9501_large.jpg';
$hasF = function ($e): array { $h = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; } return $h; };
$val = function ($e, string $h) use ($hasF) { if (!isset($hasF($e)[$h])) { return null; } try { return $e->getFieldValue($h); } catch (\Throwable $t) { return null; } };

/* The two records, as read on 5 October 2026. */
$F = Entry::find()->id($FORMAL)->section('obituaries')->status(null)->one();
$K = Entry::find()->id($KHTS)->section('obituaries')->status(null)->one();
if ((string)$val($F, 'legacyKey') !== 'obituary_conniewordenroberts') { $bad[] = "#$FORMAL is not obituary_conniewordenroberts"; }
if ((string)$val($K, 'legacyKey') !== 'khts081314') { $bad[] = "#$KHTS is not khts081314"; }
foreach ([$F, $K] as $o) { if ($o && $val($o, 'obitSubject')?->status(null)->ids() !== [$SUBJECT]) { $bad[] = "#{$o->id}: obitSubject is not #$SUBJECT"; } }

$out = [];
$out[] = '# Connie Worden-Roberts obituaries: dry run, 5 October 2026';
$out[] = '';
$out[] = 'Script: `scripts/import/worden_roberts_obituaries_2026_10_05.php`. Evidence: `inventory/review/worden-roberts-obituaries-evidence-2026-10-05.json`. Mode: ' . ($APPLY ? 'APPLYING' : 'DRY RUN') . '.';
$out[] = '';

/* ---------- A. Two pieces, and the link between them ---------- */
$out[] = '## A. Two pieces or one';
$out[] = '';
$w6 = function (string $t): array { preg_match_all('~\w+~u', mb_strtolower(strip_tags($t)), $m); $w = $m[0]; $g = []; for ($i = 0; $i + 6 <= count($w); $i++) { $g[implode(' ', array_slice($w, $i, 6))] = 1; } return $g; };
$gF = $w6((string)$val($F, 'body')); $gK = $w6((string)$val($K, 'body'));
$shared = count(array_intersect_key($gF, $gK));
$hF = $ev['pages']['scvhistory/obituary_conniewordenroberts.htm']['header']; $hK = $ev['pages']['scvhistory/khts081314.htm']['header'];
$out[] = "Two genuine pieces. Shared six-word runs between the bodies: **$shared** (of " . count($gF) . ' and ' . count($gK) . ').';
$out[] = '';
$out[] = '| | #' . $FORMAL . ' | #' . $KHTS . ' |';
$out[] = '| --- | --- | --- |';
$out[] = '| Legacy page | /scvhistory/obituary_conniewordenroberts.htm | /scvhistory/khts081314.htm |';
$out[] = '| Header as printed | `' . implode('` `', $hF) . '` | `' . implode('` `', $hK) . '` |';
$out[] = '| Obituaries index | Eternal Valley, 8-12-2014 | by Carl Goldman, AM-1220 KHTS, 8-13-2014 |';
$out[] = '| Kind | formal obituary, third person, life and survivors | personal tribute, first person, the cross-valley connector |';
$out[] = '| publicationDetails now | ' . $val($F, 'publicationDetails') . ' | ' . $val($K, 'publicationDetails') . ' |';
$out[] = '';
$relFields = [];
foreach ($F->getFieldLayout()->getCustomFields() as $f) {
    if (!$f instanceof \craft\fields\Entries) { continue; }
    $srcs = [];
    foreach ((array)$f->sources as $s) { $srcs[] = str_starts_with($s, 'section:') ? ($svc->getSectionByUid(substr($s, 8))?->handle ?? $s) : $s; }
    $relFields[] = $f->handle . ' (' . implode(',', $srcs) . ')';
}
$out[] = 'Relating them: **not written.** The obituary layout\'s relation fields are ' . implode(', ', $relFields) . '. None relates an obituary to a companion piece: obitPublishedIn takes organizations, obitRelatedPersons persons, obitRelatedMilitary war memorials, footnotesOn is a citation and derivedImageLinks is for derived images. `relatedArticles` takes articles only and is not on the obituary type. Both already carry obitSubject #' . $SUBJECT . ', so her person page lists them together. A companion link needs a field and a template line, Nathan\'s decision.';
$out[] = '';

/* ---------- B. Carl Goldman ---------- */
$out[] = '## B. Carl Goldman';
$out[] = '';
$pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
$G = Entry::find()->section('persons')->title('Carl Goldman')->status(null)->one();
if (!$G) { foreach (Entry::find()->section('persons')->status(null)->search('personAliases:Goldman')->all() as $c) { if (stripos((string)$val($c, 'personAliases'), 'Carl Goldman') !== false) { $G = $c; } } }
$GV = [
    'fullName' => 'Carl Goldman',
    'occupation' => 'Radio station co-owner (AM-1220, KBET and KHTS)',
    'body' => '<p>Carl Goldman co-owned AM-1220, the Santa Clarita Valley\'s own radio station. He bought it, as KBET, out of bankruptcy in 1990 with investor partners and sold it to Clear Channel in 1998; on October 24, 2003, he and his wife, Jeri Seratti-Goldman, bought it back, and it returned to the air as KHTS.[1] After the Northridge earthquake of January 17, 1994, KBET stayed on the air around the clock and the City Council declared it Santa Clarita\'s official emergency radio station; that July he was grand marshal of the Fourth of July Parade in Newhall, whose theme was Earthquake Heroes.[1] He was the 2008 SCV Man of the Year.[2] He wrote a tribute to Connie Worden-Roberts for KHTS on August 13, 2014.[3]</p>',
    'footnotes' => [
        ['number' => '1', 'note' => 'Leon Worden, "Carl Goldman, Grand Marshal, 1994 Fourth of July Parade," SCVHistory.com LW9450a, 2014, https://scvhistory.com/scvhistory/lw9450a.htm: "Carl Goldman, co-owner and the public face of KBET"; "Carl Goldman, who had purchased the radio station out of bankruptcy in 1990 for about $600,000 with investor partners, sold it in 1998 to Clear Channel for $3 million"; "On Oct. 24, 2003, Carl and wife Jeri Seratti-Goldman repurchased the radio station"; "It was still AM-1220 on the radio dial, but now it had new call letters"; "The City Council declared it Santa Clarita\'s official emergency radio station."', 'source' => 'editorial-2026'],
        ['number' => '2', 'note' => 'SCVHistory.com, "SCV Man & Woman of the Year," https://scvhistory.com/scvhistory/mwoty.htm: 2008, Carl Goldman and Judy Penman.', 'source' => 'editorial-2026'],
        ['number' => '3', 'note' => 'Carl Goldman, "We\'ve Lost Our Road Warrior," AM-1220 KHTS, Wednesday, August 13, 2014, as kept at https://scvhistory.com/scvhistory/khts081314.htm: "When Jeri and I purchased our radio station, AM-1220, in 1990".', 'source' => 'editorial-2026'],
    ],
    'bodyAuthorship' => 'editorial-2026',
    'recordProvenance' => 'worden_roberts_obituaries_2026_10_05.php, 5 October 2026: from SCVHistory.com LW9450a, mwoty.htm and khts081314.htm',
];
/* Every PlainText value against its limit, and no em dash anywhere. */
foreach ($GV as $h => $v) {
    $f = $fields->getFieldByHandle($h); $s = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v;
    if (preg_match('~\x{2014}~u', $s)) { $bad[] = "Goldman $h: em dash"; }
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen($v) > $f->charLimit) { $bad[] = "Goldman $h: " . mb_strlen($v) . " > {$f->charLimit}"; }
}
$orgKHTS = Entry::find()->section('organizations')->status(null)->title(['*KHTS*', '*KBET*', '*AM-1220*', '*AM 1220*'])->one();
if ($G) { $out[] = "- Person: **#{$G->id} {$G->title} exists**, not created."; }
else {
    $out[] = '- Person: none (searched persons by title and personAliases). **Would create** "Carl Goldman" (slug carl-goldman):';
    $out[] = '  - occupation: ' . $GV['occupation'];
    $out[] = '  - body: ' . strip_tags($GV['body']);
    foreach ($GV['footnotes'] as $r) { $out[] = "  - note {$r['number']}: {$r['note']}"; }
    $out[] = '  - bodyAuthorship editorial-2026; recordProvenance: ' . $GV['recordProvenance'];
}
$out[] = '- KHTS organization: ' . ($orgKHTS ? "#{$orgKHTS->id} {$orgKHTS->title}" : 'none, so no affiliation is made') . '. Spouse link to Jeri Seratti #29113: not made (spouse links never publish, docs/DATA-MODEL.md).';
$hasWB = isset($hasF($K)['writtenBy']);
$wbField = $fields->getFieldByHandle('writtenBy');
$curWB = $hasWB ? $K->getFieldValue('writtenBy')->status(null)->ids() : [];
if ($hasWB && $G && $curWB === [$G->id]) { $out[] = "- Author link: #$KHTS writtenBy already #{$G->id}."; }
elseif ($hasWB) { $out[] = "- Author link: **would set** #$KHTS writtenBy = Carl Goldman" . ($curWB ? ' (currently ' . json_encode($curWB) . ', refused: not overwritten)' : '') . '.'; if ($curWB) { $bad[] = "#$KHTS writtenBy already holds " . json_encode($curWB); } }
elseif ($ADD_WRITTENBY_TO_OBITUARY) { $out[] = "- Author link: the obituary type has no writtenBy. \$ADD_WRITTENBY_TO_OBITUARY is on: **would add** the existing writtenBy field (#{$wbField->id}, persons) to the obituary layout's first tab, then set #$KHTS writtenBy = Carl Goldman."; }
else { $out[] = "- Author link: **blocked.** The obituary entry type has no author field. writtenBy (persons, schema.org author) exists and is used by articles; adding it to the obituary layout is a project-config change. Set \$ADD_WRITTENBY_TO_OBITUARY = true to include it. templates/obituaries/_entry.twig does not render writtenBy, so the link will not show until the template does. Until then publicationDetails carries his name, as now."; }
$out[] = '';

/* ---------- C. The formal obituary's author ---------- */
$out[] = '## C. Who wrote the formal obituary';
$out[] = '';
$out[] = 'The page prints no writer. Its header, exactly: `' . implode('` / `', $hF) . '`. The byline slot holds her life dates; the dateline names the mortuary, and a Dignity Memorial logo links to Eternal Valley. The obituaries index (obits.htm) credits it "Eternal Valley, 8-12-2014", the form it uses for funeral-home notices (243 index entries are credited to Eternal Valley). So the source is Eternal Valley Memorial Park & Mortuary, an organization, not a person; no person record is made and none is inferred.';
$curPD = (string)$val($F, 'publicationDetails'); $newPD = 'Eternal Valley Memorial Park & Mortuary';
if ($curPD === $newPD) { $out[] = "- publicationDetails already reads \"$newPD\"."; }
elseif ($curPD === 'SCVHistory.com, August 2014') { $out[] = "- publicationDetails is \"$curPD\", which the page does not print. " . ($FIX_FORMAL_PUBLICATION ? "**Would set** \"$newPD\" (the dateline as printed)." : "\$FIX_FORMAL_PUBLICATION is off: not changed. On, it would set \"$newPD\" (the dateline as printed)."); }
else { $out[] = "- publicationDetails is \"$curPD\", not the value read on 5 October; left alone."; }
$out[] = '';

/* ---------- D. The image ---------- */
$out[] = '## D. The image';
$out[] = '';
$imgEv = $ev['image'];
if (!is_file($IMG)) { $bad[] = "$IMG missing: copy gif/lw9501_large.jpg from the mirror"; $sha = ''; }
else { $sha = hash_file('sha256', $IMG); if ($sha !== $imgEv['sha256']) { $bad[] = "lw9501_large.jpg sha256 $sha is not the mirror's {$imgEv['sha256']}"; } }
$A = $sha ? Asset::find()->sourceChecksum('sha256:' . $sha)->one() : null;
$A = $A ?: Asset::find()->sourceChecksum('sha256:' . $imgEv['web']['sha256'])->one();
$A = $A ?: Asset::find()->filename(['*lw9501*'])->one();
$credit = 'Photo by Gary Choppé / Creative Image Photography';
$AV = [
    'provenanceKind' => 'legacy-mirror',
    'legacySourcePath' => 'gif/lw9501_large.jpg',
    'acquiredDate' => '2026-10-05',
    'source' => 'SCVHistory.com, gif/lw9501_large.jpg, as published on the original site: the lead image of /scvhistory/obituary_conniewordenroberts.htm and in the text of /scvhistory/khts081314.htm.',
    'sourceChecksum' => 'sha256:' . $sha,
    'photoSourceCode' => 'LW9501',
    'photoCredit' => $credit,
    'creator' => 'Gary Choppé',
    'photoPeople' => [$SUBJECT],
];
$ATitle = 'Connie Worden-Roberts, portrait by Gary Choppé';
$AAlt = 'Connie Worden-Roberts in a red suit, arms folded, in front of the light trails of freeway traffic at night';
foreach ($AV as $h => $v) {
    if (is_array($v)) { continue; } $f = $fields->getFieldByHandle($h);
    if (!$f) { $bad[] = "asset field $h does not exist"; continue; }
    if (preg_match('~\x{2014}~u', $v)) { $bad[] = "asset $h: em dash"; }
    if ($f instanceof \craft\fields\PlainText && $f->charLimit && mb_strlen($v) > $f->charLimit) { $bad[] = "asset $h: " . mb_strlen($v) . " > {$f->charLimit}"; }
}
$out[] = "- File: Reggie `gif/lw9501_large.jpg` ({$imgEv['px'][0]} x {$imgEv['px'][1]}, sha256 " . substr($imgEv['sha256'], 0, 16) . '...), the enlargement the formal page links; copied to `inventory/incoming/lw9501_large.jpg`' . ($sha === $imgEv['sha256'] ? ', checksum matches' : ', CHECKSUM MISMATCH') . '. The web size, gif/lw9501.jpg (800 x 1019), is the same picture.';
$out[] = '- What it shows: Connie Worden-Roberts, waist up, in a red suit with arms folded, against a night view of freeway traffic in light trails. Signed in the image, lower left, "Photo by Gary Choppe\'". No date is printed anywhere; none is set.';
$out[] = '- Credit as printed: formal page `' . trim(strip_tags(html_entity_decode($ev['pages']['scvhistory/obituary_conniewordenroberts.htm']['photoCredit']))) . '`; Goldman\'s page caption `' . $ev['pages']['scvhistory/khts081314.htm']['lw9501Caption'] . '`.';
$out[] = '- In Craft: ' . ($A ? "**#{$A->id} {$A->filename} exists**, not imported again." : 'not held (no asset with this checksum, the web-size checksum, or a filename containing lw9501). **Would import** to archiveMedia/legacy/ as ' . $IMG_NAME . ', title "' . $ATitle . '", alt "' . $AAlt . '".');
if (!$A) { foreach ($AV as $h => $v) { $out[] = "  - $h: " . (is_array($v) ? json_encode($v) : $v); } $out[] = '  - license: left unset, as for the other legacy-mirror images; the rights are not established.'; }
foreach ([$F, $K] as $o) {
    $cur = $o->getFieldValue('recordImages')->status(null)->ids();
    if ($A && ($cur[0] ?? null) === $A->id) { $out[] = "- #{$o->id} recordImages: already leads with it."; }
    else { $out[] = "- #{$o->id} recordImages: **would put it first**" . ($cur ? ' (keeping ' . json_encode($cur) . ')' : ' (empty now)') . '. The template shows the first record image as the page\'s portrait.'; }
}
$out[] = "- Person #$SUBJECT (Connie Worden): featuredImage and recordImages are empty. Not set here; the source attaches the portrait to the obituaries, and her portrait is Nathan's call.";
$out[] = '';

/* ---------- Faults found, not fixed here ---------- */
$out[] = '## Found on the way, not changed';
$out[] = '';
$out[] = "- #$KHTS is missing two of Goldman's paragraphs that the page prints: \"Connie's son, Leon, followed in her footsteps with his community involvement. Leon now heads up our valley's public access channel SCVTV and its news website.\" (before \"He has been devoted to his mom...\", which now has no antecedent) and the closing line \"I will dearly miss Connie.\" The 2008 groundbreaking caption (lw0801) is also not carried.";
$out[] = '- Perry Smith\'s KHTS obituary of the same day (khts081214.htm) is document #28305, while the two pieces here are obituaries.';
$out[] = '- The photo credit printed on the formal page is not carried on #' . $FORMAL . '; this import puts it on the asset.';
$out[] = '';
$out[] = '## Refused';
$out[] = '';
$out[] = $bad ? '- ' . implode("\n- ", $bad) : 'none';
$out[] = '';

if (!$APPLY || $bad) {
    $out[] = $bad ? 'REFUSING: nothing was written.' : 'Nothing was written. Set $APPLY = true to apply. A second run after an apply is a no-op.';
    echo implode(PHP_EOL, $out) . PHP_EOL; return;
}

/* ---------- APPLY ---------- */
if (!$G) {
    $G = new Entry(); $G->sectionId = $pSec->id; $G->setTypeId($pType->id); $G->title = 'Carl Goldman'; $G->slug = 'carl-goldman';
    $G->setFieldValues(array_intersect_key($GV, $hasF($G)));
    if (!$el->saveElement($G)) { throw new \RuntimeException('Goldman: ' . json_encode($G->getFirstErrors())); } $n++;
}
if (!$hasWB && $ADD_WRITTENBY_TO_OBITUARY) {
    $type = $svc->getEntryTypeByHandle('obituary'); $layout = $type->getFieldLayout(); $tabs = $layout->getTabs(); $els = $tabs[0]->getElements();
    $els[] = new \craft\fieldlayoutelements\CustomField($wbField); $tabs[0]->setElements($els); $layout->setTabs($tabs); $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { throw new \RuntimeException('obituary layout: ' . json_encode($type->getErrors())); } $n++;
    $K = Entry::find()->id($KHTS)->status(null)->one(); $hasWB = isset($hasF($K)['writtenBy']);
}
if ($hasWB && $K->getFieldValue('writtenBy')->status(null)->ids() !== [$G->id]) { $K->setFieldValue('writtenBy', [$G->id]); if (!$el->saveElement($K)) { throw new \RuntimeException("#$KHTS writtenBy: " . json_encode($K->getFirstErrors())); } $n++; }
if ($FIX_FORMAL_PUBLICATION && (string)$val($F, 'publicationDetails') === 'SCVHistory.com, August 2014') { $F->setFieldValue('publicationDetails', $newPD); if (!$el->saveElement($F)) { throw new \RuntimeException("#$FORMAL: " . json_encode($F->getFirstErrors())); } $n++; }
if (!$A) {
    $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
    $tmp = sys_get_temp_dir() . "/$IMG_NAME"; copy($IMG, $tmp);
    $A = new Asset(); $A->tempFilePath = $tmp; $A->setFilename($IMG_NAME); $A->newFolderId = $folder->id; $A->setVolumeId($vol->id); $A->setScenario(Asset::SCENARIO_CREATE); $A->avoidFilenameConflicts = false;
    if (!$el->saveElement($A)) { throw new \RuntimeException('asset: ' . json_encode($A->getFirstErrors())); }
    $A = Asset::find()->id($A->id)->one(); $A->title = $ATitle; $A->alt = $AAlt;
    $A->setFieldValues(array_intersect_key($AV, $hasF($A)));
    if (!$el->saveElement($A)) { throw new \RuntimeException('asset fields: ' . json_encode($A->getFirstErrors())); } $n++;
}
foreach ([$FORMAL, $KHTS] as $id) {
    $o = Entry::find()->id($id)->status(null)->one();
    /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
    $cur = $o->getFieldValue('recordImages')->status(null)->ids();
    if (($cur[0] ?? null) === $A->id) { continue; }
    $o->setFieldValue('recordImages', array_values(array_unique(array_merge([$A->id], $cur))));
    if (!$el->saveElement($o)) { throw new \RuntimeException("#$id recordImages: " . json_encode($o->getFirstErrors())); } $n++;
}

/* Read-back. */
$short = [];
$G2 = Entry::find()->section('persons')->title('Carl Goldman')->status(null)->one(); if (!$G2 || (string)$G2->getFieldValue('occupation') === '') { $short[] = 'Goldman record'; }
foreach ([$FORMAL, $KHTS] as $id) { if ((Entry::find()->id($id)->status(null)->one()->getFieldValue('recordImages')->status(null)->ids()[0] ?? null) !== $A->id) { $short[] = "#$id image"; } }
$K2 = Entry::find()->id($KHTS)->status(null)->one(); if (isset($hasF($K2)['writtenBy']) && $K2->getFieldValue('writtenBy')->status(null)->ids() !== [$G2?->id]) { $short[] = "#$KHTS writtenBy"; }
echo implode(PHP_EOL, $out) . PHP_EOL . 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n writes") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('worden_roberts_obituaries_2026_10_05.php', $n, $short ? 'SHORT' : 'verified', 'Carl Goldman record; lw9501 Choppé portrait on #28045 and #28047' . ($ADD_WRITTENBY_TO_OBITUARY ? '; writtenBy on obituaries, #28047 by Goldman' : '') . ($FIX_FORMAL_PUBLICATION ? '; #28045 publicationDetails as printed' : ''));
if ($short) { throw new \RuntimeException('worden_roberts_obituaries_2026_10_05: read-back short'); }
