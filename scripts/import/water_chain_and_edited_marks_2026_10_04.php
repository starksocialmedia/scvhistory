/**
 * The water bodies' chain and their marks, and the edits on today's marks (Nathan, 4 October 2026: "NCWD.png and
 * CLWD.png ... historical marks, not current ones. Record them as such: the body's mark as it was, with the body's
 * dates, not as a currentMark on a live organization"; "Check whether Valencia Water Company and the Santa Clarita
 * Water Division have records. If not, they should").
 *
 *  1. assetRole gains "former-mark": a dissolved body's mark as it used it. It sits in the body's related images
 *     (recordImages), dated with the body's own years in dateEdtf; the record's header shows it, labelled as the
 *     mark the body used, when the body is dissolved and has no current mark.
 *  2. NCWD.png and CLWD.png, imported so: the Newhall County Water District's (1953 to 2017) and the Castaic Lake
 *     Water Agency's (named so from 1970 to 2017; the mark reads "Castaic Lake Water Agency", whatever the file's name
 *     says). Both carry Adobe content credentials recording an edit with Adobe Firefly made in Photoshop on 4 October
 *     2026 ("composite with trained algorithmic media"), so they enter on Nathan's word with the edit recorded and no
 *     original held, as the archive's rule for edited images requires.
 *  3. The same edit, missed on import this morning, recorded on three marks already in: Newhall School District
 *     (newhall-school-district-logo-2700.png), Canyon High School and the California State Senate.
 *  4. Valencia Water Company and the Santa Clarita Water Division, the two of the four bodies merged by SB 634 that had
 *     no record, created from the archive's own copies of the agencies' documents; every predecessor points to SCV
 *     Water, and SCV Water to all four.
 * Idempotent. Dry run by default. Set $APPLY = true to write. Writes project config (an option).
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/water_chain_and_edited_marks_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries();
$inc = fn($f) => is_file("$root/inventory/incoming/$f") ? "$root/inventory/incoming/$f" : "$root/inventory/incoming/done/$f";
$cc = fn($urn, $when) => "Adobe content credential (C2PA), https://cai-manifests.adobe.com/manifests/" . str_replace(':', '-', $urn) . "\nSteps recorded (UTC): $when created (Adobe Photoshop 27.6.0); edited with Adobe Firefly, digital source type compositeWithTrainedAlgorithmicMedia (composite with trained algorithmic media).";
$EDIT = [
    'newhall-school-district-logo-2700.png' => ['urn:c2pa:e246bd4f-816e-4eb2-87f6-0bb07ae1731c:adobe', '2026-10-04 18:57'],
    'canyon-high-school-logo.png' => ['urn:c2pa:eaff4a6e-f268-4fec-b313-009dc20654a8:adobe', '2026-10-04 16:30'],
    'california-state-senate-seal.png' => ['urn:c2pa:398ed146-8665-4e62-8199-d9fa75e7bc95:adobe', '2026-10-04 18:00'],
];
$METHOD = 'Edited in Adobe Photoshop 27.6.0 with Adobe Firefly (generative; the credential records a composite with trained algorithmic media)';
$MARKS = [
    ['NCWD.png', 'newhall-county-water-district-logo.png', 27534, 'Logo of the Newhall County Water District', '1953-01-13/2017-12-31', 'urn:c2pa:1a8841ca-09f6-4b13-8039-c34ba5b5ac7b:adobe', '2026-10-04 20:26', 'Newhall County Water District (its rights passed to the Santa Clarita Valley Water Agency)'],
    ['CLWD.png', 'castaic-lake-water-agency-logo.png', 26563, 'Logo of the Castaic Lake Water Agency', '1970/2017-12-31', 'urn:c2pa:f7f5554e-a5e4-4a52-8f82-5ae153bbf0cf:adobe', '2026-10-04 20:29', 'Castaic Lake Water Agency (its rights passed to the Santa Clarita Valley Water Agency)'],
];
$M = '/Volumes/Reggie/SCVHistory/scvhistory.com/scvhistory';
$SRC = [
    'scwd2012' => 'Castaic Lake Water Agency, "History & Overview of Santa Clarita Water Co./Division," August 2012, as carried on SCVHistory.com, /scvhistory/clwa_scwd_2012.htm: "In 1999, the Castaic Lake Water Agency purchased SCWC, and the name was changed to the Santa Clarita Water Division (SCWD) of Castaic Lake Water Agency"; it began with the Bonelli family\'s Bouquet Canyon Water Company (1949) and Solemint Water Company (1956), merged as the Santa Clarita Water Company.',
    'scwc2017' => 'Leon Worden, "Santa Clarita Water Company Dissolved," on the Castaic Lake Water Agency board\'s vote of October 25, 2017, as carried on SCVHistory.com, /scvhistory/clwa102517.htm: the agency "bought all assets of the private Santa Clarita Water Company for $63 million"; the company had been "inactive since August 31, 1998, when CLWA absorbed its assets"; it "officially ceased to exist October 31, 2017." The retail division the agency called "Santa Clarita" "had nothing legally to do with the old corporate entity."',
    'vwc2018' => 'Santa Clarita Valley Water Agency, "Resolution Dissolving Valencia Water Company," for the board meeting of January 9, 2018, as carried on SCVHistory.com, /scvhistory/scvwa010918.htm: SB 634 "directs SCV Water to take the appropriate steps together with the Board of Directors of Valencia Water Company (VWC) to authorize the dissolution of VWC no later than January 31, 2018, and the transfer of the company\'s assets, property, liabilities, and indebtedness to SCV Water"; the dissolution "shall be finalized no later than May 1, 2018."',
    'sb634' => 'Leon Worden, "Gov. Brown Signs Wilk Bill Merging SCV Water Agencies," SCVNews.com, October 15, 2017, as carried on SCVHistory.com, /scvhistory/scvnews101517.htm.',
];
$NEW = [
    'Valencia Water Company' => ['business', null, '', "The Valencia Water Company was the Newhall Land and Farming Company's private water company, one of the four retail water suppliers in the valley that bought their state water from the Castaic Lake Water Agency. The Castaic Lake Water Agency bought it in 2012, and the agency's purchase was one of the matters it and the Newhall County Water District took to court before they settled by merging.[1][2] Senate Bill 634 made the company one of the four bodies replaced by the Santa Clarita Valley Water Agency on 1 January 2018, and directed its dissolution: the new agency's board adopted the plan to dissolve it in January 2018, to be finished no later than 1 May 2018.[3]", ['sb634', 'scwd2012', 'vwc2018'], ''],
    'Santa Clarita Water Division' => ['government', 26563, '1999', "The Santa Clarita Water Division was the Castaic Lake Water Agency's retail water service for parts of the City of Santa Clarita and the unincorporated land of Saugus, Canyon Country and West Newhall. It began with the Bonelli family's Bouquet Canyon Water Company of 1949 and Solemint Water Company of 1956, merged as the private Santa Clarita Water Company, whose assets the agency bought for $63 million: in 1999 by the agency's own history, which says the name was then changed to the Santa Clarita Water Division, while Leon Worden's report of the company's dissolution dates the takeover of its assets to 31 August 1998.[1][2] The division had nothing legally to do with the old company, which was formally dissolved on 31 October 2017.[2] On 1 January 2018 the division was one of the four bodies merged by Senate Bill 634 into the Santa Clarita Valley Water Agency.[3]", ['scwd2012', 'scwc2017', 'sb634'], '2018-01-01'],
];
$bad = []; foreach ($MARKS as $m) { if (!is_file($inc($m[0]))) { $bad[] = "{$m[0]} missing"; } }
foreach ($EDIT as $fn => $x) { if (!Asset::find()->filename($fn)->one()) { $bad[] = "no asset $fn"; } }
echo 'assetRole former-mark: ' . (in_array('former-mark', array_column($fs->getFieldByHandle('assetRole')->options, 'value')) ? 'exists' : 'add') . PHP_EOL;
foreach ($NEW as $t => $x) { echo "$t: " . (Entry::find()->section('organizations')->status(null)->title($t)->exists() ? 'exists' : 'create') . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
$ar = $fs->getFieldByHandle('assetRole');
if (!in_array('former-mark', array_column($ar->options, 'value'))) { $o = $ar->options; $o[] = ['label' => 'Former mark (of a dissolved body)', 'value' => 'former-mark', 'default' => false]; $ar->options = $o; if (!$fs->saveField($ar)) { throw new \RuntimeException('assetRole'); } $n++; }
foreach ($EDIT as $fn => [$urn, $when]) { $a = Asset::find()->filename($fn)->one(); if (trim((string)$a->contentCredentials) === '') {
    $a->setFieldValues(['enhancementMethod' => $METHOD, 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => '2026-10-04', 'contentCredentials' => $cc($urn, $when)]); if (!$el->saveElement($a)) { throw new \RuntimeException($fn . json_encode($a->getFirstErrors())); } $n++; } }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'marks/']);
foreach ($MARKS as [$file, $fn, $oid, $title, $edtf, $urn, $when, $holder]) {
    $path = $inc($file); $a = Asset::find()->filename($fn)->one();
    if (!$a) { $tmp = sys_get_temp_dir() . "/$fn"; copy($path, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($fn); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } }
    /* Fields set whenever the asset lacks its role, so a run that stopped after the upload is repaired on the next. */
    if (($a->assetRole->value ?? '') !== 'former-mark') {
        $a = Asset::find()->id($a->id)->one(); $a->title = $title; $a->alt = $title;
        $v = ['assetRole' => 'former-mark', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'license' => 'identifying-use', 'rightsHolder' => $holder, 'dateEdtf' => $edtf,
            'source' => "$title, supplied by Nathan Imhoff on 4 October 2026 as the mark the body used; where it was taken from is not recorded with the file, and no unedited original is held. Its dates are the body's own years under that name, not the logo's, which are not recorded.",
            'rightsNote' => 'A dissolved body\'s own mark, shown only to identify the body on its own record, as a reference work does; it implies no endorsement.',
            'sourceChecksum' => 'sha256:' . hash_file('sha256', $path), 'enhancementMethod' => $METHOD, 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => '2026-10-04', 'contentCredentials' => $cc($urn, $when)];
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++; }
    /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
    $o = Entry::find()->id($oid)->status(null)->one(); $ids = $o->recordImages->status(null)->ids();
    if (!in_array($a->id, $ids)) { $o->setFieldValue('recordImages', array_merge([$a->id], $ids)); if (!$el->saveElement($o)) { throw new \RuntimeException("#$oid"); } $n++; }
}
$os = $svc->getSectionByHandle('organizations'); $fnotes = fn(array $keys) => array_map(fn($i, $k) => ['number' => (string)($i + 1), 'note' => $SRC[$k], 'source' => 'editorial-2026'], array_keys($keys), $keys);
$ids = [];
foreach ($NEW as $t => [$type, $parent, $founded, $body, $keys, $dissolved]) {
    $e = Entry::find()->section('organizations')->status(null)->title($t)->one();
    if (!$e) { $e = new Entry(); $e->sectionId = $os->id; $e->setTypeId($os->getEntryTypes()[0]->id); $e->title = $t;
        $v = ['orgType' => $type, 'body' => $body, 'footnotes' => $fnotes($keys), 'succeededBy' => [402], 'recordProvenance' => 'water_chain_and_edited_marks_2026_10_04.php, 4 October 2026: one of the four bodies merged by SB 634'];
        if ($type === 'government') { $v['orgLevel'] = 'valley'; } if ($parent) { $v['hasParentOrg'] = true; $v['parentOrganization'] = [$parent]; }
        if ($founded) { $v['dateFounded'] = $founded; $v['dateFoundedEdtf'] = $founded; } if ($dissolved) { $v['dateDissolved'] = 'January 1, 2018'; $v['dateDissolvedEdtf'] = $dissolved; }
        $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields()); $e->setFieldValues(array_intersect_key($v, array_flip($h)));
        if (!$el->saveElement($e)) { throw new \RuntimeException("$t: " . json_encode($e->getFirstErrors())); } $n++; }
    $ids[] = $e->id;
}
$S = Entry::find()->id(402)->one(); $want = array_values(array_unique(array_merge($S->precededBy->status(null)->ids(), [26563, 27534], $ids)));
if ($S->precededBy->status(null)->ids() != $want) { $S->setFieldValue('precededBy', $want); if (!$el->saveElement($S)) { throw new \RuntimeException('SCV Water'); } $n++; }
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('water_chain_and_edited_marks_2026_10_04.php', $n, 'verified', 'former-mark role; NCWD and CLWA marks with their edit recorded; the edit recorded on three marks imported earlier; Valencia Water Company and Santa Clarita Water Division; SCV Water preceded by all four');
echo "done: $n writes" . PHP_EOL;
