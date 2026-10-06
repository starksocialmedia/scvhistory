/**
 * The first City Council's photographs (Nathan, 4 October 2026: "lead with SC8801 and flag the caption's left-to-right order
 * as unconfirmed. Import BW8702 as well, since the council-elect in November 1987 is its own moment"), from the legacy site
 * (inventory/review/city-photo-and-communities-2026-10-04.md).
 *  SC8801: the first council at the dais of City Hall, 23920 Valencia Boulevard, late 1988, "probably the day in December 1988
 *    when Jan Heidt was named mayor"; photographs by Gary Choppé for the City. The colour view (sc8801_large.jpg) is the image;
 *    the page's caption is kept as written. Its left-to-right order, "McKeon, Heidt, Koontz, Darcy, Boyer", is unconfirmed:
 *    compared with BW8702 the faces read Koontz, Heidt, McKeon, Darcy, Boyer. An editor's note says so.
 *  BW8702: the council-elect soon after the election of 3 November 1987, Bob Weber Collection, bw8702_orig.jpg.
 * Both tagged with the five members and the City. SC8801 leads the City's related images, so it leads the History tab.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_first_council_photos_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $svc = Craft::$app->getEntries(); $GIF = '/var/www/html/storage/legacy-gif';
$FIVE = [18791, 15737, 23081, 16140, 15808];
$P = [
  'sc8801' => ['title' => 'First Santa Clarita City Council, 1987-1990', 'scan' => 'sc8801_large.jpg', 'date' => 'December 1988', 'edtf' => '1988-12?',
    'credit' => 'SC8801: 9600 dpi jpeg from original print | Photographs by Gary Choppé for City of Santa Clarita',
    'body' => "First Santa Clarita City Council, 12-15-1987 to April 1990. From left: Howard P. \"Buck\" McKeon, Janice H. Heidt, Dennis Koontz, Jo Anne Darcy, and Carl Boyer III. As the top vote-getters (respectively), McKeon and Heidt served initial 4-year terms; in order to begin staggering terms, Darcy, Boyer and Koontz came up for re-election after just 2 years. Darcy and Boyer won the April 1990 election, but Koontz lost to activist Jill Klajic, making him the only one of the original five City Council members who did not serve as mayor. Although later councils selected among themselves each year to fill the largely ceremonial post, according to Boyer the initial council used a strict rotation. Darcy was the third mayor, Boyer the fourth.\n\nThe inaugural group is seen here in front of the council dias at City Hall, 23920 W. Valencia Blvd., in late 1988, a few months after the city relocated to the building from a storefront on Soledad Canyon Road.",
    'note' => 'The order of names in the caption above, from left, has not been confirmed. Compared with the council-elect photographed in November 1987 (BW8702), the faces here read from left: Dennis Koontz, Jan Heidt, Buck McKeon, Jo Anne Darcy and Carl Boyer.'],
  'bw8702' => ['title' => 'First City Council-Elect: Darcy, Boyer, Heidt, Koontz, McKeon; November 1987', 'scan' => 'bw8702_orig.jpg', 'date' => 'November 1987', 'edtf' => '1987-11',
    'credit' => 'BW8702: 9600 dpi jpeg, Bob Weber Collection.',
    'body' => "First Santa Clarita City Council members (elect), soon after their election on November 3, 1987. Standing, from left: Jo Anne Darcy, Carl Boyer III, Dennis Koontz, Howard \"Buck\" McKeon. Seated: Jan Heidt.", 'note' => ''],
];
$bad = []; foreach ($P as $k => $c) { if (!is_file("$GIF/{$c['scan']}")) { $bad[] = "$GIF/{$c['scan']} missing (copy it from the mirror first)"; } }
foreach ($FIVE as $id) { if (!Entry::find()->id($id)->status(null)->exists()) { $bad[] = "#$id missing"; } }
foreach ($P as $k => $c) { echo "$k: " . (Entry::find()->section('photographs')->status(null)->legacyKey($k)->exists() ? 'exists' : 'import') . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']); $ids = [];
foreach ($P as $k => $c) {
    $e = Entry::find()->section('photographs')->status(null)->legacyKey($k)->one();
    if (!$e) {
        $tmp = sys_get_temp_dir() . "/{$c['scan']}"; copy("$GIF/{$c['scan']}", $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($c['scan']); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        $a->setFieldValues(['provenanceKind' => 'legacy-mirror', 'legacySourcePath' => "gif/{$c['scan']}", 'acquiredDate' => '2026-10-04', 'source' => "SCVHistory.com, gif/{$c['scan']}, as published on the original site.", 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$GIF/{$c['scan']}")]);
        if (!$el->saveElement($a)) { throw new \RuntimeException("$k scan " . json_encode($a->getFirstErrors())); } $n++;
        $e = new Entry(); $e->sectionId = $svc->getSectionByHandle('photographs')->id; $e->setTypeId($svc->getEntryTypeByHandle('photograph')->id); $e->title = $c['title'];
        $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
        $v = ['body' => $c['body'], 'legacyKey' => $k, 'legacyUrl' => "/scvhistory/$k.htm", 'sourcePath' => "https://scvhistory.com/scvhistory/$k.htm", 'featuredImage' => [$a->id], 'photoSourceCode' => strtoupper($k), 'creditRaw' => $c['credit'],
            'photoPeople' => $FIVE, 'photoOrganizations' => [394], 'photoDate' => $c['date'], 'photoDateEdtf' => $c['edtf'], 'recordProvenance' => 'import_first_council_photos_2026_10_04.php, 4 October 2026'];
        if ($c['note']) { $v['editorNotes'] = [['heading' => 'The order of names', 'position' => 'top', 'note' => $c['note']]]; }
        $e->setFieldValues(array_intersect_key($v, array_flip($h)));
        if (!$el->saveElement($e)) { throw new \RuntimeException("$k " . json_encode($e->getFirstErrors())); } $n++;
    }
    $ids[$k] = $e->featuredImage->one()?->id ?? $e->featuredImage->ids()[0] ?? null;
}
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
$City = Entry::find()->id(394)->one(); $cur = $City->recordImages->status(null)->ids(); $want = array_values(array_unique(array_merge([$ids['sc8801'], $ids['bw8702']], $cur)));
if ($cur != $want) { $City->setFieldValue('recordImages', $want); if (!$el->saveElement($City)) { throw new \RuntimeException('City'); } $n++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('import_first_council_photos_2026_10_04.php', $n, 'verified', 'SC8801 and BW8702, the first council and the council-elect; SC8801 leads the City\'s images');
echo "done: $n writes" . PHP_EOL;
