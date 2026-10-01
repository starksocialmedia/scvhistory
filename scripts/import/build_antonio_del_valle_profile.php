/**
 * Antonio del Valle #291, grantee of Rancho San Francisco (Nathan, asked
 * 29 September and twice since). Replaces the unsourced WordPress body.
 *
 * THE SOURCES (the mirror's copies in inventory/legacy/fetched/, each matching
 * the manifest of 20 August 2026; the marker in inventory/sources/)
 *   [PERKINS] A.B. Perkins, "Rancho San Francisco: A Study of a California Land
 *             Grant," HSSC Quarterly, June 1957. Imported here as a document,
 *             with a note on reading it: careful, but his figures need checking,
 *             and two of them are wrong or inconsistent in this very text.
 *   [REYNOLDS] Jerry Reynolds, History of the SCV, web edition 1998, part 14.
 *   [ENGELHARDT] Zephyrin Engelhardt, San Fernando Rey (1927), chapters 5 and 6,
 *             with Leon Worden's 2014 introduction, which notes Engelhardt's
 *             church-versus-state bias. Stated as his where it is his view.
 *   [MARKER]  The Rancho Camulos Museum's marker (hmdb 220571).
 *   [SIGNAL]  The Signal, 1 September 1999: cited only for its error. It names
 *             a living descendant, who is not named here.
 *   Wikipedia on Rancho San Francisco was a finding aid only. Hometown Station's
 *   "June 21, 1841" piece could not be read and is not cited.
 *
 * CHECKS MADE: the Figueroa appointment. The October 1834 commission and the
 * appointment as mayordomo on 29 May 1835 both fall within Figueroa's term (he
 * died in September 1835); the 1839 grant is Alvarado's. The old body ran the
 * two appointments together. Acreage: 48,829 is the grant's nominal size (eleven
 * leagues on the diseño), 48,611.88 the patented figure. Death: 21 June
 * (Reynolds) against 12 June (Perkins), both shown.
 *
 * ALSO: corrects footnote 7 on #333 Perkins, which cited Reynolds part 15 for
 * the 21 June date; it is part 14. Relates #291 to the Estancia (#595).
 *
 * Replaces the body only while it is still the WordPress text, and recordDates
 * only while every row is derived from it. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_antonio_del_valle_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $F = "$root/inventory/legacy/fetched";
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$layout = fn($e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$bad = [];
$MANIFEST = ['perkins-rsf-1957.htm' => null, 'reynolds-part14.html' => '', 'engelhardt_sanfernando56.htm' => '', 'sg090199a.htm' => ''];
$doc = json_decode((string)@file_get_contents("$F/perkins-rsf-1957.json"), true);
if (!$doc || hash_file('sha256', "$F/perkins-rsf-1957.htm") !== $doc['htmlSha256'] || $doc['htmlSha256'] !== $doc['mirrorManifestSha256']) { $bad[] = 'the Perkins 1957 page is missing or not the mirror\'s'; }
$src = ['perkins' => $ws(@file_get_contents("$F/perkins-rsf-1957.txt")), 'reynolds' => $ws(@file_get_contents("$F/reynolds-part14.txt")),
    'engelhardt' => $ws(@file_get_contents("$F/engelhardt_sanfernando56.txt")), 'signal' => $ws(@file_get_contents("$F/sg090199a.txt")),
    'marker' => json_decode((string)@file_get_contents("$root/inventory/sources/antonio-del-valle-2026-10-01.json"), true)['sources']['hmdb220571']['passage'] ?? ''];
$ID = 291; $YGNACIO = 293; $PERKINS = 333; $RSF = 16446; $ESTANCIA = 595;
$p = $get($ID);
if (!$p || $p->title !== 'Antonio del Valle') { $bad[] = '#291 is not Antonio del Valle'; }
if (!$get($YGNACIO) || !in_array($ID, $get($YGNACIO)->childOf->status(null)->ids())) { $bad[] = '#293 is not recorded as his son'; }
if ($get($ESTANCIA)?->title !== 'Estancia de San Francisco Xavier' || $get($RSF)?->title !== 'Rancho San Francisco') { $bad[] = 'the place records are not who they should be'; }

$MUST = [
    'perkins' => ['came to California from Dept. of Jalisco, Mexico, as Lieut. in Company of San Blas, 1819', 'joined his father in California in 1825', 'In 1833, the Mexican Congress passed the bill for secularization of the missions', 'In October, 1834, Lieut. Antonio del Valle was commissioned to take over Mission San Fernando by inventory', 'was succeeded by Antastasio Carrillo', 'petitioned Governor Alvarado for the Rancho January 22, 1839', 'An unsuccessful protest was filed by Fr. Narciso Duran, Prefect of the Missions of the South', 'the Asistencia of 1804, repaired and returned to use by Don Antonio del Valle as his rancho home in 1839', 'June 12, 1841, Don Antonio del Valle died, mourned by his widow, two children from his first marriage, and four children from his second marriage', 'Married, for second time, Jacopa Feliz, by whom five children', 'the estate distributed by undivided portions of the rancho to the heirs', 'In 1869, the Soledad School District', 'The following year, Rancho San Francisco was finally partitioned', 'Its 1,340 acres were permanently separated'],
    'reynolds' => ['Antonio was forty-six when he was assigned to inventory the property of Mission San Fernando', 'eleven leagues, or 48,829 acres', 'Alvarado sat at his desk in Santa Barbara on January 22, 1839', 'moved his family into the Asistencia de San Francisco Xavier on the bluff overlooking the junction of Castaic Creek and the Santa Clara River', 'he died on June 21, 1841', 'and no will', 'fifty-three-year-old ranchero', 'leaving a total of eight children and a widow', 'when the lad of seventeen stepped off the ship at Monterey on July 27, 1825'],
    'engelhardt' => ['Lieutenant Antonio del Valle, in October 1834, was commissioned to "secularize" the establishment', 'took charge of the Mission estates by inventory from Fr. Ibarra', 'On May 29, 1835, Valle was appointed mayordomo or administrator, at $800 salary', 'The inventory drawn up by Antonio del Valle, on July 26, 1835', 'Buildings of the Mission — 15,511.00', 'the Indians, who had taken refuge at the Mission, were the thieves', 'it would be necessary to put in the Rancho of San Francisco a corporal', 'accusing the resident missionary, Fr. Francisco Ibarra, of absconding with a chest of silver', 'on September 22nd, only five days before his death', 'In March 1837, Antonio del Valle was succeeded as mayordomo by Anastasio Carrillo', 'had been taken from them and given to Antonio del Valle, the former administrator', 'Their anger was so violent that Del Valle feared to trust himself and family on the ranch', 'against the will of the neophytes', 'bias in matters of church vs. state', 'the young Juan B. Alvarado'],
    'signal' => ['a Mexican-born missionary who owned 48,000 acres', 'September 1, 1999'],
    'marker' => ['Antonio\'s son Ygnacio inherited the land after his father\'s death in 1841'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k], $ph)) { $bad[] = "$k does not read \"$ph\""; } } }

$BODY = implode("\n\n", [
    'Antonio del Valle was a Mexican army lieutenant who came to California in 1819, took charge of Mission San Fernando when it was secularized, and in 1839 received Rancho San Francisco, the grant that took in most of the Santa Clarita Valley. He died on the rancho two years later without a will, and his heirs held it in undivided shares until it was partitioned in 1870.[1][2]',
    'Perkins, drawing on Bancroft, says he came from Jalisco as a lieutenant in the Company of San Blas in 1819. His son Ygnacio, from his first marriage, joined him at Monterey in 1825, at seventeen.[1][2] His birth year is given as 1788: Reynolds has him forty-six in 1834 and fifty-three when he died.[2]',
    'Mexico\'s Congress ordered the missions secularized in 1833. In October 1834 del Valle was commissioned to secularize Mission San Fernando and took over its estates by inventory from the missionary, Fr. Francisco Ibarra; on 29 May 1835 he was appointed its mayordomo, or administrator, at $800 a year. Both dates fall within the term of Governor José Figueroa, who died that September.[1][3] His inventory of 26 July 1835 valued the mission buildings at $15,511. He reported that horses were being stolen and blamed Indians who had taken refuge at the mission, asked for a corporal to be posted at the Rancho San Francisco, and accused Fr. Ibarra of carrying off a chest of silver.[3] He gave up the post in March 1837 and was succeeded by Anastasio Carrillo.[1][3]',
    'He then sought the rancho for himself. Governor Juan B. Alvarado granted it on 22 January 1839, over a protest from Fr. Narciso Durán, prefect of the southern missions.[1][2] The diseño, the hand-drawn map that accompanied the grant, showed eleven leagues, which Reynolds gives as 48,829 acres; that is the grant\'s nominal size. The United States patent of 1875, issued after the heirs\' claim was confirmed, was for 48,611.88 acres.[2][4] According to Engelhardt, when the inspector of missions, William Hartnell, visited San Fernando in June 1839 he found the Indians angry that the rancho "had been taken from them and given to Antonio del Valle," so angry that del Valle feared to trust himself and his family on it.[3] He moved them into the old Estancia de San Francisco Xavier, the mission outpost of 1804 above the junction of Castaic Creek and the Santa Clara River, which became the rancho house.[1][2]',
    'His second wife was Jacoba Feliz. He died in June 1841 without a will: on 21 June according to Reynolds, on 12 June according to Perkins, and no record of the death itself has been seen.[2][1] The heirs were his widow, Ygnacio and the children of both marriages, though the sources count them differently.[1][2] The probate court distributed the rancho to them in undivided portions, and it was not partitioned until 1870, when Camulos, 1,340 acres, was set apart for Ygnacio.[1] The Rancho Camulos Museum\'s marker at the house says that Ygnacio "inherited the land" on his father\'s death; he inherited a share of it.[5][1]',
    'Writers have judged him as differently as they have dated him. Engelhardt, writing in 1927 from the Franciscan side, presents the del Valles as taking the rancho "against the will of the neophytes," and Leon Worden\'s introduction to the passage warns of his bias in matters of church and state.[3] A 1999 newspaper feature called Antonio "a Mexican-born missionary," which he was not.[6]',
]);
$DOC_TITLE = 'Rancho San Francisco: A Study of a California Land Grant, by A.B. Perkins (1957)';
$NOTES = [
    'Arthur B. Perkins, "Rancho San Francisco: A Study of a California Land Grant," Historical Society of Southern California Quarterly, June 1957, as transcribed at https://scvhistory.com/scvhistory/perkins-rsf-1957.htm, with his notes 26 (Antonio del Valle) and 85 (the partition). Archive record: "' . $DOC_TITLE . '."',
    'Jerry Reynolds, History of the Santa Clarita Valley, web edition edited by Leon Worden for the SCV Historical Society, 1998, part 14, "Lord and Master," https://scvhistory.com/scvhistory/signal/reynolds/part14.html.',
    'Zephyrin Engelhardt, San Fernando Rey (Chicago, 1927), chapters 5 and 6, with an introduction by Leon Worden (2014), https://scvhistory.com/scvhistory/engelhardt_sanfernando56.htm. Engelhardt quotes Hartnell and the mission and government records.',
    'United States patent to Rancho San Francisco, 12 February 1875, 48,611.88 acres; Land Case 303 SD, Bancroft Library. As read for the archive\'s del Valle source review of 29 September 2026; the patent itself is not in the archive.',
    'Rancho Camulos Museum, marker "The Del Valle Family Home," Rancho Camulos, recorded in the Historical Marker Database, https://www.hmdb.org/m.asp?m=220571, read 1 October 2026.',
    'Marci Wormser, "Del Valle descendant pursues her roots," The Signal, 1 September 1999, https://scvhistory.com/scvhistory/sg090199a.htm.',
];
$EDITOR = [['heading' => 'Birthplace', 'position' => 'bottom', 'note' => 'Perkins says he came from Jalisco. This record came from WordPress with "Composilla, Mexico," for which no source has been found; one genealogy gives Compostela, then in the same province.']];
$DOC_NOTE = [['heading' => 'Reading this document', 'position' => 'top', 'note' => 'Perkins worked from the land-case files, deeds, probate records and the Spanish archives, and cited them, and this archive prefers his figures to later retellings until an original is seen. They still need checking. In this text he dates Antonio del Valle\'s death 12 June 1841, where Jerry Reynolds gives 21 June; and he counts four children of Antonio\'s second marriage in the text and five in his own note 26.']];
$row = fn(string $printed, string $iso, string $gran, string $label): array => ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];
$DATES = [
    $row('1788', '1788-01-01', 'year', 'born, by the ages Reynolds gives'),
    $row('October 1834', '1834-10-01', 'month', 'commissioned to secularize Mission San Fernando'),
    $row('May 29, 1835', '1835-05-29', 'day', 'appointed mayordomo of Mission San Fernando'),
    $row('January 22, 1839', '1839-01-22', 'day', 'Governor Alvarado grants Rancho San Francisco'),
    $row('June 21, 1841', '1841-06-21', 'day', 'died, per Reynolds; Perkins gives June 12'),
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES) . json_encode([$EDITOR, $DOC_NOTE, $DATES], JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
if (preg_match('~Hanson|Markey~', $BODY . implode('', $NOTES))) { $bad[] = 'a living descendant is named'; }

/* ------------------------------------------------ plan */
$OLD_START = 'Antonio del Valle was born in 1788 at Composilla';
$cur = trim((string)$p?->body); $isOld = str_starts_with(trim(strip_tags($cur)), $OLD_START); $isNew = $cur === trim($BODY);
if (!$isOld && !$isNew) { $bad[] = '#291\'s body has been edited since the WordPress import'; }
$oldPlain = $ws(strip_tags($cur));
$rows = array_values(array_filter($p?->recordDates ?? [], fn($r) => is_array($r) && trim((string)($r['printed'] ?? '')) !== ''));
$datesNew = array_column($rows, 'label') === array_column($DATES, 'label');
$datesDerived = !$rows || !array_filter($rows, fn($r) => !str_contains($oldPlain, trim(mb_substr($ws($r['label']), 3, 60))));
$d = Entry::find()->section('documents')->status(null)->sourcePath($doc['url'] ?? '-')->one();
/* Rows are rebuilt as number/note/source: a row as read also carries col1..col3, and the save keeps those over an edited "note". */
$pk = $get($PERKINS); $pkNotes = array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? '')], $pk?->footnotes ?? []); $pkFix = false;
foreach ($pkNotes as $i => $r) { if (str_contains((string)($r['note'] ?? ''), 'part 15 (Antonio del Valle\'s death, 21 June 1841)')) { $pkNotes[$i]['note'] = str_replace('part 15 (Antonio', 'part 14 (Antonio', $r['note']); $pkFix = true; } }
echo ($d ? "#{$d->id} exists: " : 'create document: ') . $DOC_TITLE . '; Leon\'s note (' . count($doc['webmasterNote'] ?? []) . ' paragraphs) and Perkins\'s text with his 109 notes (' . count($doc['transcription'] ?? []) . ' paragraphs) verbatim; a note on reading it; about #291 and #293' . PHP_EOL;
echo '#291 body: ' . ($isNew ? 'already the sourced profile' : 'the unsourced WordPress body -> the sourced profile (' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes)') . '; every quoted phrase checked in its source (' . array_sum(array_map('count', $MUST)) . ')' . PHP_EOL;
echo '#291 birthplace Composilla -> Jalisco, Mexico, with a note; recordDates ' . ($datesNew ? 'already rebuilt' : ($datesDerived ? count($rows) . ' derived rows -> ' . count($DATES) : 'NOT all derived: kept')) . PHP_EOL;
echo '#595 Estancia de San Francisco Xavier: placePeople ' . (in_array($ID, $get($ESTANCIA)->placePeople->status(null)->ids()) ? 'already has #291' : '+ #291') . '; #16446 Rancho San Francisco already has him; #293 Ygnacio already his son' . PHP_EOL;
echo '#333 Perkins footnote 7: ' . ($pkFix ? 'Reynolds part 15 -> part 14' : 'already part 14') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$d) {
        $d = new Entry(); $d->sectionId = $svc->getSectionByHandle('documents')->id; $d->setTypeId($svc->getEntryTypeByHandle('document')->id); $d->title = $DOC_TITLE;
        $vals = ['sourcePath' => $doc['url'], 'legacyUrl' => '/scvhistory/perkins-rsf-1957.htm', 'legacyKey' => 'perkins-rsf-1957',
            'originallyPublishedTitle' => $doc['title'], 'originalPublishDate' => 'June 1957', 'originalPublishDateEdtf' => '1957-06',
            'sourceLine' => 'A.B. Perkins, The Historical Society of Southern California Quarterly, June 1957', 'webmasterNoteTop' => implode("\n\n", $doc['webmasterNote']),
            'body' => implode("\n\n", $doc['transcription']), 'subjectPerson' => [$ID, $YGNACIO], 'editorNotes' => $DOC_NOTE];
        $d->setFieldValues(array_intersect_key($vals, array_flip($layout($d))));
        if (!$elements->saveElement($d)) { throw new \RuntimeException('document: ' . json_encode($d->getFirstErrors())); }
    }
    $s = $get($ID); $vals = [];
    if (!$isNew) {
        $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'birthplace' => 'Jalisco, Mexico',
            'birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective',
            'editorNotes' => array_merge(array_values(array_filter($s->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')), $EDITOR),
            'recordProvenance' => trim((string)$s->recordProvenance . '; build_antonio_del_valle_profile.php, 1 Oct 2026: sourced profile; Perkins 1957 imported as a document', '; ')];
    }
    if ($datesDerived && !$datesNew) { $vals['recordDates'] = $DATES; }
    if ($vals) { $s->setFieldValues(array_intersect_key($vals, array_flip($layout($s)))); if (!$elements->saveElement($s)) { throw new \RuntimeException('#291: ' . json_encode($s->getFirstErrors())); } }
    $e = $get($ESTANCIA); $ids = $e->placePeople->status(null)->ids();
    if (!in_array($ID, $ids)) { $e->setFieldValue('placePeople', array_merge($ids, [$ID])); if (!$elements->saveElement($e)) { throw new \RuntimeException('#595'); } }
    if ($pkFix) { $pk->setFieldValue('footnotes', $pkNotes); if (!$elements->saveElement($pk)) { throw new \RuntimeException('#333'); } }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

$short = []; $s = $get($ID); $d = Entry::find()->section('documents')->status(null)->sourcePath($doc['url'])->one();
if (trim((string)$s->body) !== trim($BODY)) { $short[] = 'body'; }
if (!$d || !in_array($ID, $d->subjectPerson->status(null)->ids()) || !str_contains((string)$d->body, '109. Ventura Weekly Free Press')) { $short[] = 'document'; }
if (!in_array($ID, $get($ESTANCIA)->placePeople->status(null)->ids())) { $short[] = '#595'; }
if (!array_filter($get($PERKINS)->footnotes ?? [], fn($r) => str_contains((string)($r['note'] ?? ''), 'part 14 (Antonio'))) { $short[] = '#333 note 7'; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . $s->url . ' and ' . $d->url) . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_antonio_del_valle_profile.php', 4, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Antonio del Valle profile; Perkins 1957 as a document; Estancia relation; Perkins note 7 corrected');
if ($short) { throw new \RuntimeException('build_antonio_del_valle_profile: ' . implode('; ', $short)); }
