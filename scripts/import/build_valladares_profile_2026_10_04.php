/**
 * Suzette Martinez Valladares: a living person, public life only (Nathan, 4 October 2026: "Build her profile. She is the
 * clearest test of the model: Assembly 38th from December 2020 to December 2022, lost the 40th to Schiavo by 522 votes in
 * 2022, then won the 23rd Senate District in 2024 ... Lead with the valley. Attribute anything resting only on her
 * office's biography"; "the relationship between the 21st and the 23rd ... Confirm whether the 23rd is the 21st's
 * territory renumbered under the 2021 plan, or a different seat, and say so on both their records").
 *
 * THE 21st AND THE 23rd. Measured on 2020 census blocks with the Census Bureau's block files for both plans
 * (inventory/review/sd21-sd23-2026-10-04.md): 91.95 per cent of the new 23rd's people lived in the old 21st, and 92.62
 * per cent of the old 21st's people went into the new 23rd; the number 21 passed to a district around Santa Barbara
 * County that shares no population with the old one. So she succeeded Wilk in the same territory under a new number.
 * Said in her text; on Wilk's record, whose text is an unsourced import and is not rewritten, as an editor's note; and
 * as a footnote on both their Senate terms.
 *
 * THE SOURCES: her Senate office's biography (inventory/news/valladares-2026-10-04/), attributed in the text where it
 * stands alone, family and home left out; the Statements of Vote for 2020, 2022 and 2024; the records of members.
 * The portrait, suzette-martinez-valladares.jpg, supplied by Nathan; where it was first published is not recorded.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_valladares_profile_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$p = Entry::find()->section('persons')->status(null)->title('Suzette Martinez Valladares')->one(); $w = Entry::find()->id(335)->status(null)->one();
$IMG = "$root/inventory/incoming/suzette-martinez-valladares.jpg"; if (!is_file($IMG)) { $IMG = "$root/inventory/incoming/done/suzette-martinez-valladares.jpg"; }
$sen = fn($person) => Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $person, 'field' => 'holdingPerson'], ['targetElement' => 28273, 'field' => 'holdingBody']]);
$vS = $p ? $sen($p)->seatLabel('23rd Senate District')->one() : null; $wS = $w ? $sen($w)->seatLabel('21st Senate District')->one() : null;
$bad = []; if (!$p || !$vS) { $bad[] = 'Valladares or her Senate term missing'; } if (!$w || !$wS || $w->title !== 'Scott Thomas Wilk Sr.') { $bad[] = 'Wilk or his 21st term missing'; } if (!is_file($IMG)) { $bad[] = 'portrait missing'; }
$SD = 'Census Bureau, 2020 Census block assignment file for California (state senate districts of the 2011 plan) and 2022 state senate block equivalency file (the 2021 plan), with 2020 block populations, as measured by the archive (2020 population): of the 1,036,642 people of the 23rd District under the 2021 lines, 953,201 (92 per cent) had lived in the 21st District under the 2011 lines; of the 21st\'s 1,029,111, the same 953,201 (93 per cent) went into the 23rd. The number 21 passed to a district around Santa Barbara County that shares no population with the old 21st. Citizens Redistricting Commission, Final Maps Report, 26 December 2021, pages 49 to 50: districts were numbered so that voters stayed in odd- or even-numbered districts, not so that each number kept its old ground.';
$PLAIN = 'Under the 2021 redistricting the 21st State Senate District\'s territory became, with small changes, the 23rd District: 92 per cent of the new 23rd\'s people had lived in the old 21st, and 93 per cent of the old 21st\'s people went into the new 23rd. The number 21 passed to an unrelated district around Santa Barbara County, so Suzette Martinez Valladares, elected for the 23rd in 2024, holds Scott Wilk\'s seat under a new number.';
$BODY = "Suzette Martinez Valladares has represented the Santa Clarita Valley in the California State Senate since 2 December 2024, as the senator for the 23rd District, which holds the whole valley under the lines drawn in 2021.[1][2] The 23rd is, with small changes, the old 21st District under a new number: 92 per cent of the 23rd's people in 2020 had lived in the 21st, which Scott Wilk held from 2016 to 2024, so she succeeded him in the same territory.[3] She won the seat in November 2024 over Kipp Mueller, 190,957 votes to 173,695.[4]\n\nBefore that she sat in the Assembly for the 38th District, the district then holding most of the valley, from 7 December 2020 to 5 December 2022. She was elected in 2020 with 76.1 per cent of the vote, against Lucie Lapointe Volotzky, another Republican.[5][6] When the 2021 lines replaced the 38th with the 40th, she stood for the new district in November 2022 and lost to Pilar Schiavo by 522 votes, 79,330 to 79,852.[7]\n\nHer Senate biography says she began her career at Six Flags Magic Mountain, studied at College of the Canyons and California State University, Northridge, and was executive director of Southern California Autism Speaks; that she manages an early childcare center; and that she is a founding member of the bipartisan Problem Solvers Caucus and of the California Hispanic Legislative Caucus.[8]";
$NOTES = [
    'Secretary of the Senate, Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88, read 4 October 2026: "Valladares, Suzette Martinez, R, Los Angeles, San Bernardino, 2025-2026." She took her seat on 2 December 2024, when the Senate elected that November convened.',
    'Citizens Redistricting Commission, Final Maps Report, 26 December 2021, https://wedrawthelines.ca.gov/wp-content/uploads/sites/64/2023/01/Final-Maps-Report-with-Appendices-12.26.21-230-PM-1.pdf, page 72: Senate District 23 includes "the whole Cities of Adelanto, Hesperia, Lancaster, Palmdale, Santa Clarita, and Victorville."',
    $SD,
    'California Secretary of State, Statement of Vote, General Election, November 5, 2024, State Senator, 23rd State Senate District: Suzette Martinez Valladares 190,957 (52.4 per cent), Kipp Mueller 173,695 (47.6 per cent), https://elections.cdn.sos.ca.gov/sov/2024-general/sov/37-state-senator.pdf.',
    'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79, read 4 October 2026: "Valladares, Suzette Martinez, R, Los Angeles, Ventura, 2021-2022."',
    'California Secretary of State, Statement of Vote, General Election, November 3, 2020, State Assembly, 38th Assembly District: Suzette Martinez Valladares (REP) 149,201 (76.1 per cent), Lucie Lapointe Volotzky (REP) 46,877 (23.9 per cent), https://elections.cdn.sos.ca.gov/sov/2020-general/sov/41-state-assembly.pdf.',
    'California Secretary of State, Statement of Vote, General Election, November 8, 2022, State Assembly, 40th Assembly District: Pilar Schiavo 79,852, Suzette Martinez Valladares 79,330, https://elections.cdn.sos.ca.gov/sov/2022-general/sov/65-state-assemblymember.pdf.',
    'California State Senate, "About Suzette," https://sr23.senate.ca.gov/about-suzette, read 4 October 2026. Her own office\'s page: what rests on it alone is attributed in the text.',
];
echo "Valladares #{$p?->id}, Senate term #{$vS?->id}; Wilk #{$w?->id}, 21st term #{$wS?->id}" . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$fn = fn(array $n) => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($n), $n);
$addNote = function ($e, $note) use ($el) { $rows = $e->footnotes ?? []; if (in_array($note, array_column($rows, 'note'), true)) { return 0; } $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $note, 'source' => 'editorial-2026']; $e->setFieldValue('footnotes', $rows); if (!$el->saveElement($e)) { throw new \RuntimeException('#' . $e->id); } return 1; };
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $p->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'State Senator',
        /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
        'roles' => array_values(array_unique(array_merge($p->roles->status(null)->ids(), [18313, 18387]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [28271, 28273]))),
        'editorNotes' => [['heading' => 'About the sources', 'position' => 'bottom', 'note' => 'Her biography is published by her own Senate office. What rests on it alone is attributed in the text; her family and home are left out.']],
        'recordProvenance' => 'record_valley_legislators_2026_10_04.php and build_valladares_profile_2026_10_04.php, 4 October 2026'], array_flip($h)));
    if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); } $n++;
    $n += $addNote($vS, $PLAIN . ' ' . $SD); $n += $addNote($wS, $PLAIN . ' ' . $SD);
    $wn = $w->editorNotes ?? []; if (!in_array('The 21st and the 23rd', array_column($wn, 'heading'), true)) { $wn = array_values(array_filter($wn, fn($r) => trim((string)($r['note'] ?? '')) !== '')); $wn[] = ['heading' => 'The 21st and the 23rd', 'position' => 'bottom', 'note' => $PLAIN . ' (Measured by the archive on 2020 census blocks with the Census Bureau\'s block files for both plans; the sources are in the footnote to his Senate term.)'];
        $w->setFieldValue('editorNotes', $wn); if (!$el->saveElement($w)) { throw new \RuntimeException('Wilk: ' . json_encode($w->getFirstErrors())); } $n++; }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK: ' . $t->getMessage() . PHP_EOL; throw $t; }
if (!$p->featuredImage->exists()) {
    $FN = 'suzette-martinez-valladares.jpg'; $a = Asset::find()->filename($FN)->one();
    if (!$a) { $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
        $tmp = sys_get_temp_dir() . "/$FN"; copy($IMG, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($FN); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
        $a = Asset::find()->id($a->id)->one(); $a->title = 'Suzette Martinez Valladares, portrait'; $a->alt = 'Portrait of Suzette Martinez Valladares';
        $v = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'rightsNote' => 'No permission to republish is established.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $IMG),
            'source' => 'Supplied by Nathan Imhoff on 4 October 2026. Where the photograph was first published is not recorded with it, and permission to republish is not established.'];
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++; }
    $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException('portrait'); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('build_valladares_profile_2026_10_04.php', $n, 'verified', 'Suzette Martinez Valladares: profile and portrait; the 21st and the 23rd stated on her record and Wilk\'s');
echo "done: $n writes" . PHP_EOL;
