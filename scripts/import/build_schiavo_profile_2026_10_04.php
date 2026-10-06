/**
 * Pilar Schiavo: a living person, so public-life facts only, two citations where a second source exists, no birth
 * date, nothing about family or home (Nathan, 4 October 2026: "Build her profile from it, leading with the valley.
 * She beat Valladares by 522 votes in November 2022 and has held the seat since ... The biography is her own
 * office's, so attribute anything that rests on it alone, as we did with Walters's self-written district bio").
 *
 * THE SOURCES (inventory/news/schiavo-2026-10-04/, manifest.json with hashes; and the Statements of Vote in
 * inventory/sources/legislative-districts-2026-10-04/)
 *   Her office's biography, schiavo.asmdc.org, read 4 October 2026: published by her own office. What only it
 *     says is attributed in the text: the Assistant Majority Whip appointment and her work before office. Its
 *     claims of money brought to the district and returned through casework are her office's account of itself
 *     and are left out, as are her home and family.
 *   The Assembly Committee on Military and Veterans Affairs: the second source for her chairmanship.
 *   Statements of Vote: 2022 and 2024 general elections, the June 2026 primary. Record of Members of the Assembly.
 *   Citizens Redistricting Commission, 2021: the 40th holds the whole City of Santa Clarita.
 * The portrait, Schiavo-030-11-29-22.jpg, supplied by Nathan; where it was first published is not recorded.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_schiavo_profile_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$p = Entry::find()->section('persons')->status(null)->title('Pilar Schiavo')->one();
$IMG = "$root/inventory/incoming/Schiavo-030-11-29-22.jpg"; if (!is_file($IMG)) { $IMG = "$root/inventory/incoming/done/Schiavo-030-11-29-22.jpg"; }
$bad = []; if (!$p) { $bad[] = 'no record for Pilar Schiavo'; } if (!is_file($IMG)) { $bad[] = 'portrait missing'; }
$BODY = "Pilar Schiavo has represented the Santa Clarita Valley in the California State Assembly since 5 December 2022, as the member for the 40th Assembly District, which holds the whole City of Santa Clarita under the lines drawn in 2021.[1][2] She won the seat at its first election, in November 2022, beating Suzette Martinez Valladares, who had held the valley's seat as the 38th District, by 522 votes: 79,852 to 79,330.[3] She was re-elected in 2024 over Patrick Lee Gipson, 119,654 votes to 106,960,[4] and placed first in the primary of 2 June 2026, with 55.6 per cent, going on to the November election.[5] Her district office is in Santa Clarita.[6]\n\nShe chairs the Assembly's Committee on Military and Veterans Affairs.[6][7] Her office's biography says that on her election the Speaker appointed her Assistant Majority Whip, and describes her before office as a nurse advocate and small business owner who worked in the labor movement for more than twenty years.[6]";
$NOTES = [
    'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79, read 4 October 2026: "Schiavo, Pilar, D, Los Angeles, 2023-2026." She took her seat on 5 December 2022, when the Assembly elected that November convened.',
    'Citizens Redistricting Commission, Final Maps Report, 26 December 2021, https://wedrawthelines.ca.gov/wp-content/uploads/sites/64/2023/01/Final-Maps-Report-with-Appendices-12.26.21-230-PM-1.pdf: Assembly District 40 includes "the whole City of Santa Clarita and portions of the City of Los Angeles."',
    'California Secretary of State, Statement of Vote, General Election, November 8, 2022, State Assembly, 40th Assembly District: Pilar Schiavo 79,852, Suzette Martinez Valladares 79,330, https://elections.cdn.sos.ca.gov/sov/2022-general/sov/65-state-assemblymember.pdf.',
    'California Secretary of State, Statement of Vote, General Election, November 5, 2024, State Assembly, 40th Assembly District: Pilar Schiavo 119,654 (52.8 per cent), Patrick Lee Gipson 106,960 (47.2 per cent), https://elections.cdn.sos.ca.gov/sov/2024-general/sov/42-state-assembly.pdf.',
    'California Secretary of State, Statement of Vote, Primary Election, June 2, 2026, State Assembly, 40th Assembly District: Pilar Schiavo 74,496 (55.6 per cent), first of four, https://elections.cdn.sos.ca.gov/sov/2026-primary/sov/95-state-assembly.pdf.',
    'Office of Assemblymember Pilar Schiavo, "Biography," https://schiavo.asmdc.org/biography, read 4 October 2026. Her own office\'s page: what rests on it alone is attributed in the text. Its district office address is 27441 Tourney Road, Santa Clarita.',
    'Assembly Committee on Military and Veterans Affairs, https://avet.assembly.ca.gov/, read 4 October 2026: "Assembly Member Pilar Schiavo, Assembly District 40, Chair of the Military and Veterans Affairs Committee."',
];
echo "Pilar Schiavo #{$p?->id}: body " . strlen($BODY) . ' characters, ' . count($NOTES) . ' notes; portrait ' . ($p?->featuredImage->exists() ? 'present' : 'import') . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$fn = fn(array $n) => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($n), $n);
$h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
$vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'State Assemblymember',
    /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
    'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [28271]))),
    'editorNotes' => [['heading' => 'About the sources', 'position' => 'bottom', 'note' => 'Her biography is published by her own office. What rests on it alone is attributed in the text; its account of money brought to the district is left out.']],
    'recordProvenance' => 'record_valley_legislators_2026_10_04.php and build_schiavo_profile_2026_10_04.php, 4 October 2026'];
$p->setFieldValues(array_intersect_key($vals, array_flip($h)));
if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); } $n = 1;
if (!$p->featuredImage->exists()) {
    $FN = 'pilar-schiavo.jpg'; $a = Asset::find()->filename($FN)->one();
    if (!$a) { $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
        $tmp = sys_get_temp_dir() . "/$FN"; copy($IMG, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($FN); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
        $a = Asset::find()->id($a->id)->one(); $a->title = 'Pilar Schiavo, portrait'; $a->alt = 'Portrait of Pilar Schiavo';
        $v = ['license' => 'unknown', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'rightsNote' => 'No permission to republish is established.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $IMG),
            'source' => 'Supplied by Nathan Imhoff on 4 October 2026. Where the photograph was first published is not recorded with it, and permission to republish is not established.'];
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++; }
    $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException('portrait'); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('build_schiavo_profile_2026_10_04.php', $n, 'verified', 'Pilar Schiavo: profile, leading with the valley seat; portrait');
echo "done: $n writes" . PHP_EOL;
