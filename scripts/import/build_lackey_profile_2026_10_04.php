/**
 * Tom Lackey: a living person, public life only (Nathan, 4 October 2026: "Lead with the valley, which for him means the
 * part of it he represents rather than the whole. His record should be clear that he has always held a sliver ... He is
 * also termed out this year, so note when his service ends").
 *
 * THE SOURCES: the Statements of Vote (2014, 2022, 2024; the June 2026 primary, where he is not a candidate); the Record
 * of Members of the Assembly ("2015-2026"); the shares from the archive's census-block count (templates/_data/valley-districts.json);
 * his office's biography, read in the Wayback Machine's capture of 23 March 2026 because the live page answers
 * automated reads with a bot check (inventory/news/lackey-2026-10-04/). The biography says he has represented the 34th
 * "since 2014"; he sat for the 36th until 2022, which the text says from the returns. What only the biography says is
 * attributed; his family and home are left out. The portrait, tom-lackey.jpg, supplied by Nathan.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_lackey_profile_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$p = Entry::find()->section('persons')->status(null)->title('Tom Lackey')->one();
$IMG = "$root/inventory/incoming/tom-lackey.jpg"; if (!is_file($IMG)) { $IMG = "$root/inventory/incoming/done/tom-lackey.jpg"; }
$bad = []; if (!$p) { $bad[] = 'no Tom Lackey'; } if (!is_file($IMG)) { $bad[] = 'portrait missing'; }
$BODY = "Tom Lackey has represented a small part of the Santa Clarita Valley in the California State Assembly since 1 December 2014, never the whole of it. From 2014 to 2022 he sat for the 36th District, which under the 2011 lines held the valley's northeastern edge, the unincorporated land of the Plum, Bouquet, Sand and Mint canyon areas and Green Valley: about 7 per cent of the valley's people.[1][2][3] Most of the valley was then in the 38th, so through those years it had two Assembly members at once: Lackey, and Scott Wilk until 2016, then Dante Acosta, Christy Smith and Suzette Martinez Valladares.[3]\n\nSince 5 December 2022 he has sat for the 34th District, which under the 2021 lines holds Agua Dulce and about 1,700 people in the unincorporated land north of the City: about 2 per cent of the valley, the rest being in Pilar Schiavo's 40th.[3][4][5] Most of both of his districts lies in the Antelope Valley.\n\nHe won the 36th in 2014 from the sitting member, Steve Fox, 42,107 votes to 27,866,[1] and the 34th in 2022 over Thurston \"Smitty\" Smith, 63,840 to 49,183, and in 2024 over Ricardo Ortega, 117,751 to 72,152.[4][5] His twelve years in the Legislature, the limit since 2012, end with his term in December 2026; he is not a candidate in 2026.[2][6]\n\nHis office's biography says that before the Assembly he served on the Palmdale Elementary School District board and the Palmdale City Council, taught special education, and worked for the California Highway Patrol for 28 years.[7]";
$NOTES = [
    'California Secretary of State, Statement of Vote, General Election, November 4, 2014, State Assembly, 36th Assembly District: Tom Lackey (REP) 42,107 (60.2 per cent), Steve Fox (DEM, incumbent) 27,866 (39.8 per cent), https://elections.cdn.sos.ca.gov/sov/2014-general/pdf/64-state-assemblymember.pdf.',
    'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79, read 4 October 2026: "Lackey, Tom, R, Kern, Los Angeles, San Bernardino, 2015-2026."',
    'Census blocks assigned to districts from the official block files and counted by the archive (Statewide Database and Census Bureau): under the 2011 lines the 36th held 18,781 of the valley\'s people on the 2010 Census, 7 per cent, and the 38th 93 per cent; under the 2021 lines the 34th holds 5,198 on the 2020 Census, 2 per cent, and the 40th 98 per cent.',
    'California Secretary of State, Statement of Vote, General Election, November 8, 2022, State Assembly, 34th Assembly District: Tom Lackey (REP) 63,840 (56.5 per cent), Thurston "Smitty" Smith (REP) 49,183 (43.5 per cent), https://elections.cdn.sos.ca.gov/sov/2022-general/sov/65-state-assemblymember.pdf.',
    'California Secretary of State, Statement of Vote, General Election, November 5, 2024, State Assembly, 34th Assembly District: Tom Lackey (REP, incumbent) 117,751 (62.0 per cent), Ricardo Ortega (DEM) 72,152 (38.0 per cent), https://elections.cdn.sos.ca.gov/sov/2024-general/sov/42-state-assembly.pdf.',
    'California Constitution, article IV, section 2(c), as amended by Proposition 28 (2012): twelve years in the Legislature. California Secretary of State, Statement of Vote, Primary Election, June 2, 2026, State Assembly, 34th Assembly District: he is not among the candidates, https://elections.cdn.sos.ca.gov/sov/2026-primary/sov/95-state-assembly.pdf.',
    'Assemblymember Tom Lackey, "Biography," https://ad34.asmrc.org/biography/, as captured by the Wayback Machine on 23 March 2026, https://web.archive.org/web/20260323171827/https://ad34.asmrc.org/biography/. His own office\'s page: what rests on it alone is attributed in the text. It says he has represented the 34th District "since 2014"; until 2022 his district was the 36th.',
];
echo "Tom Lackey #{$p?->id}" . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$fn = fn(array $n) => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($n), $n);
$h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
$p->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'State Assemblymember',
    /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
    'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [28271]))),
    'editorNotes' => [['heading' => 'About the sources', 'position' => 'bottom', 'note' => 'His biography is published by his own office. What rests on it alone is attributed in the text; his family and home are left out.']],
    'recordProvenance' => 'record_valley_legislators_2026_10_04.php and build_lackey_profile_2026_10_04.php, 4 October 2026'], array_flip($h)));
if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); } $n = 1;
if (!$p->featuredImage->exists()) {
    $FN = 'tom-lackey.jpg'; $a = Asset::find()->filename($FN)->one();
    if (!$a) { $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
        $tmp = sys_get_temp_dir() . "/$FN"; copy($IMG, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($FN); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
        $a = Asset::find()->id($a->id)->one(); $a->title = 'Tom Lackey, portrait'; $a->alt = 'Portrait of Tom Lackey';
        $v = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'rightsNote' => 'No permission to republish is established.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $IMG),
            'source' => 'Supplied by Nathan Imhoff on 4 October 2026. Where the photograph was first published is not recorded with it, and permission to republish is not established.'];
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++; }
    $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException('portrait'); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('build_lackey_profile_2026_10_04.php', $n, 'verified', 'Tom Lackey: profile, leading with the part of the valley he holds; portrait');
echo "done: $n writes" . PHP_EOL;
