/**
 * Abel Stearns #309: the Santa Cruz Sentinel document, and the profile (Nathan,
 * 1 October 2026). Three sources, used as he set them:
 *
 *   Wikipedia, "Abel Stearns": a finding aid only. Already the record's
 *     personWikipediaUrl link; nothing here is cited to it.
 *   Paul R. Spitzzeri, "On This Day: The Probate of Abel Stearns, 27 September
 *     1871", The Homestead Blog (Homestead Museum), 27 September 2018: the
 *     career, the offices, the ranchos, the death and the probate.
 *   "Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream", the
 *     legacy page lp_santacruzsentinel082785: imported here as a document
 *     record and related to him. It carries Stearns's own letter of 8 July
 *     1867, as the Santa Cruz Sentinel printed it on 27 August 1885, and Leon
 *     Worden's note on it. Leon's note and the transcription are kept verbatim
 *     (inventory/legacy/fetched/lp_santacruzsentinel082785.json); the scan is
 *     the legacy mirror's file byte for byte. The page's HTML differs from the
 *     mirror's copy of 20 August (the site's frame, not the text: both are
 *     recorded).
 *
 * THE PROFILE replaces the unsourced WordPress body (it stays in the record's
 * revisions) with a sourced one, bodyAuthorship editorial-2026. Where the
 * sources disagree the primary one wins: the Homestead post says one shipment
 * "reputedly yielded $35,000"; Stearns's letter gives the one he sent in 1842,
 * twenty ounces valued at $344.75, and that is what the profile says.
 * The birth date, 9 February 1798, is on the record from the WordPress import;
 * the year is Leon's ("Stearns' (1798-1871)"), the day is in no source here,
 * so birthEvidence is uncited and a note says why. The death date is the
 * Homestead post's: deathEvidence retrospective.
 *
 * Refuses if the body has been edited since the WordPress import. Idempotent.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_stearns_profile.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$src = json_decode(file_get_contents("$root/inventory/legacy/fetched/lp_santacruzsentinel082785.json"), true);
$scanPath = "$root/inventory/legacy/fetched/lp_santacruzsentinel082785.jpg";
if (!$src || hash_file('sha256', $scanPath) !== $src['scan']['sha256'] || $src['scan']['sha256'] !== $src['scan']['mirrorSha256']) { echo 'REFUSING: the fetched page or scan is missing, or the scan is not the mirror\'s' . PHP_EOL; return; }
$ID = 309; $LOPEZ = 18834;
$stearns = $get($ID); $lopez = $get($LOPEZ);
if (!$stearns || $stearns->title !== 'Abel Stearns' || !$lopez || $lopez->title !== 'Francisco Lopez') { echo 'REFUSING: #309 or #18834 is not who it should be' . PHP_EOL; return; }
$OLD_START = 'Abel Stearns was born in Lunenburg, Massachusetts in 1798 and arrived in California in 1829';
$HOMESTEAD = 'Paul R. Spitzzeri, "On This Day: The Probate of Abel Stearns, 27 September 1871," The Homestead Blog, Homestead Museum, 27 September 2018, https://homesteadmuseum.blog/2018/09/27/on-this-day-the-probate-of-abel-stearns-27-september-1871/';
$DOC_TITLE = 'Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream';

$BODY = implode("\n\n", [
    'Abel Stearns was a Massachusetts merchant who settled in Mexican Los Angeles in 1829 and became the pueblo\'s leading merchant, a holder of offices under Mexico, the United States and the new state, and one of the largest landowners in Southern California.[1][2] He came from Lunenburg, Massachusetts.[1]',
    'He opened a store in the pueblo, kept a warehouse at San Pedro for imported goods and built an early flour mill north of the town.[1] He married Arcadia Bandini, who was fourteen to his forty-odd; they had no children, and lived in a large adobe on Main Street called El Palacio.[1] In 1842 he bought Rancho Los Alamitos, and by the mid-1860s he held Las Bolsas, La Bolsa Chica, Los Coyotes, La Habra and San Juan Cajón de Santa Ana, most of them in what is now Orange County, and Jurupa and La Sierra near Riverside.[1]',
    'His tie to the Santa Clarita Valley is gold. In March 1842 Francisco Lopez found placer gold at a place Stearns called San Francisquito, about thirty-five miles north-west of Los Angeles. On 22 November 1842 Stearns sent twenty ounces of it by Alfred Robinson to the United States mint at Philadelphia, where it was deposited on 8 July 1843 and valued at $344.75. He set this down in a letter of 8 July 1867 to the Society of California Pioneers, which also gives the find as Lopez told it: resting under some trees while looking for stray horses, Lopez "with his sheath knife dug up some wild onions, and in the dirt discovered a piece of gold."[2]',
    'That letter is the earliest account the archive holds, and two things are not in it: a dream, and any particular oak. Both enter the story in 1930. Nor is it settled what Stearns meant by "San Francisquito": San Francisquito Canyon, where placer gold is still found, or the wider valley, which went by that name before Placerita Canyon had its own.[3]',
    'He was alcalde of Los Angeles after the American seizure of the pueblo in 1847, a member of the ayuntamiento and then of the Common Council, a county supervisor and a member of the State Assembly, and the region elected him to the convention that wrote California\'s first constitution in 1849.[1]',
    'The droughts and floods that ended the cattle economy left him rich in land and short of money. His friend Alfred Robinson formed the Robinson Trust, which took over what were called the Stearns Ranchos and sold their nearly 180,000 acres, largely in forty-acre lots, as Los Angeles grew after 1865; it cleared his debts and made him another fortune.[1] He died in San Francisco, on a business trip, on 23 August 1871. His will of 12 March 1870, witnessed among others by Pío Pico, was admitted to probate in Los Angeles on 27 September 1871, and Arcadia was his sole heir.[1]',
]);
$NOTES = [
    $HOMESTEAD . '. The post quotes the probate papers of 27 September 1871 in the Homestead Museum\'s collection.',
    'Abel Stearns to Louis R. Lull, Secretary of the Society of Pioneers, San Francisco, Los Angeles, 8 July 1867, with Alfred Robinson\'s letter of 6 August 1843 quoting the mint\'s memorandum; printed in the Santa Cruz Sentinel, 27 August 1885. Archive record: "' . $DOC_TITLE . '."',
    'Leon Worden\'s note on the same record, which traces the dream to Francisca Lopez de Belderrain\'s telling of 1930 and the oak to the dedication of 1930, and sets out the two readings of "San Francisquito."',
];

/* ------------------------------------------------ plan */
$doc = Entry::find()->section('documents')->status(null)->sourcePath($src['url'])->one();
echo ($doc ? "#{$doc->id} exists: " : 'create document: ') . $DOC_TITLE . '; Santa Cruz Sentinel, 27 August 1885; Leon\'s note (' . count($src['webmasterNote']) . ' paragraphs) and the transcription (' . count($src['transcription']) . ') verbatim; the scan; about #309 Abel Stearns and #18834 Francisco Lopez' . PHP_EOL;
$curBody = (string)$stearns->body;
$isOld = str_starts_with(trim(strip_tags($curBody)), $OLD_START);
$isNew = trim($curBody) === trim($BODY);
if (!$isOld && !$isNew) { echo 'REFUSING: #309\'s body has been edited since the WordPress import' . PHP_EOL; return; }
echo '#309 body: ' . ($isNew ? 'already the sourced profile' : 'the unsourced WordPress body -> the sourced profile (' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes), bodyAuthorship editorial-2026') . PHP_EOL;
echo '#309 birthEvidence -> uncited (the day is in no source; the year is Leon\'s); deathEvidence -> retrospective' . PHP_EOL;
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { echo 'REFUSING: an em dash in the text' . PHP_EOL; return; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$doc) {
        $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $assets = Craft::$app->getAssets();
        $folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => 'documents/']) ?? $assets->getRootFolderByVolumeId($volume->id);
        $tmp = sys_get_temp_dir() . '/lp_santacruzsentinel082785.jpg'; copy($scanPath, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename('lp_santacruzsentinel082785.jpg'); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        if (!$elements->saveElement($a)) { throw new \RuntimeException('scan: ' . json_encode($a->getFirstErrors())); }
        $doc = new Entry(); $sec = $svc->getSectionByHandle('documents'); $doc->sectionId = $sec->id; $doc->setTypeId($svc->getEntryTypeByHandle('document')->id); $doc->title = $DOC_TITLE;
        $h = array_map(fn($f) => $f->handle, $doc->getFieldLayout()->getCustomFields());
        $vals = ['sourcePath' => $src['url'], 'legacyUrl' => '/scvhistory/lp_santacruzsentinel082785.htm', 'legacyKey' => 'lp_santacruzsentinel082785',
            'originallyPublishedTitle' => 'Bogus History. Angelenos the First Argonauts.', 'originalPublishDate' => 'August 27, 1885', 'originalPublishDateEdtf' => '1885-08-27',
            'sourceLine' => 'Santa Cruz Sentinel, August 27, 1885; correspondence of July 8, 1867', 'webmasterNoteTop' => implode("\n\n", $src['webmasterNote']),
            'body' => implode("\n\n", $src['transcription']) . "\n\n" . $src['credit'], 'documentFiles' => [$a->id], 'subjectPerson' => [$ID, $LOPEZ],
            'recordProvenance' => 'build_stearns_profile.php, 1 October 2026: ' . $src['url'] . ' fetched 1 October 2026, SHA-256 ' . $src['htmlSha256'] . ' (the legacy mirror of 20 August 2026 holds ' . $src['mirrorHtmlSha256'] . '); scan ' . $src['scan']['url'] . ', SHA-256 ' . $src['scan']['sha256'] . ', identical to the mirror\'s'];
        $doc->setFieldValues(array_intersect_key($vals, array_flip($h)));
        if (!$elements->saveElement($doc)) { throw new \RuntimeException('document: ' . json_encode($doc->getFirstErrors())); }
    }
    if (!$isNew) {
        $s = $get($ID);
        $h = array_map(fn($f) => $f->handle, $s->getFieldLayout()->getCustomFields());
        $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'birthEvidence' => 'uncited', 'deathEvidence' => 'retrospective',
            'editorNotes' => array_merge(array_values(array_filter($s->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')),
                [['heading' => 'Birth date', 'position' => 'bottom', 'note' => 'The year, 1798, is Leon Worden\'s. The day, 9 February, came with the WordPress import and has not been found in a source.']])];
        $s->setFieldValues(array_intersect_key($vals, array_flip($h)));
        if (!$elements->saveElement($s)) { throw new \RuntimeException('#309: ' . json_encode($s->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

$s = $get($ID); $d = Entry::find()->section('documents')->status(null)->sourcePath($src['url'])->one();
$ok = $d && trim((string)$s->body) === trim($BODY) && in_array($ID, $d->subjectPerson->ids()) && $d->documentFiles->one() && str_starts_with((string)$d->webmasterNoteTop, 'Much of what we know');
echo 'READ-BACK ' . ($ok ? 'OK: document #' . $d->id . ', profile ' . $s->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_stearns_profile.php', 2, $ok ? 'verified' : 'SHORT', 'Abel Stearns profile and the Santa Cruz Sentinel document');
if (!$ok) { throw new \RuntimeException('build_stearns_profile: read-back failed'); }
