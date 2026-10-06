/**
 * Acton-Agua Dulce Unified School District (Nathan, 4 October 2026: "create the Acton-Agua Dulce Unified School District record. It
 * governs part of the valley, it appears in the governance table, and a table row pointing at nothing is a gap"), from
 * inventory/review/aadusd-redevelopment-2026-10-04.md (sources in inventory/news/aadusd-redevelopment-2026-10-04/). Its trustees are
 * not recorded as terms here; the text says how they are elected. The district's own "established 1881" and Leon Worden's 1878 for
 * the forerunner are both given, attributed.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_aadusd_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $svc = Craft::$app->getEntries(); $T = 'Acton-Agua Dulce Unified School District';
$BODY = "The Acton-Agua Dulce Unified School District runs the public schools of Acton and Agua Dulce, from kindergarten through twelfth grade, in the hills between the Santa Clarita Valley and the Antelope Valley. Its office is at 32248 North Crown Valley Road in Acton.[1][2]\n\nIt became a unified district in 1993, out of the Soledad-Agua Dulce Union elementary district, whose three schools it took over; the federal school directory first lists it in 1993, and its high school, Vasquez High, in 1994.[3][4] The district dates its beginnings to 1881; Leon Worden finds its forerunner, the Soledad district, in existence by 1878. A school at Agua Dulce began in 1914, and the Soledad and Agua Dulce districts merged by a vote in 1947.[5][6] Its schools now are Meadowlark Elementary, High Desert, for grades five to eight, and Vasquez High.[2][4]\n\nIts five trustees were elected at large through 2024. In 2025 the board adopted a map of five trustee areas and asked the County Committee on School District Organization to approve elections by area, with Areas 1, 2 and 4 first filled by area in 2026 and Areas 3 and 5 in 2028.[7]";
$NOTES = [
  'California Department of Education, School Directory, Acton-Agua Dulce Unified, CDS code 19 75309 0000000, https://www.cde.ca.gov/SchoolDirectory/details?cdscode=19753090000000, read 4 October 2026: a unified district, kindergarten to grade 12, 32248 N. Crown Valley Rd., Acton.',
  'National Center for Education Statistics, Common Core of Data, 2022, district 0600001 and its schools (as served by the Urban Institute\'s Education Data Portal, https://educationdata.urban.org/api/v1/schools/ccd/directory/2022/?leaid=0600001): Vasquez High, Meadowlark Elementary, High Desert.',
  'Acton-Agua Dulce Unified School District, home page, as captured by the Wayback Machine on January 4, 2000, https://web.archive.org/web/20000104155156/http://aadusd.k12.ca.us:80/: "The District unified in 1993."',
  'National Center for Education Statistics, Common Core of Data, 1992 and 1993: the Soledad-Agua Dulce Union elementary district (0637080), kindergarten to grade 8, last listed in 1992; Acton-Agua Dulce Unified (0600001) first listed in 1993, with the same three schools, Vasquez High from 1994.',
  'Acton-Agua Dulce Unified School District, district information, https://www.aadusd.k12.ca.us/district-info/homepage, read 4 October 2026: "established 1881." The district\'s own account.',
  'Leon Worden, "Early History of the Acton-Agua Dulce Unified School District," 2020, as carried on SCVHistory.com, /scvhistory/pt9001.htm: the Soledad district by 1878; the Agua Dulce school from 1914; the merger by a vote of 1947.',
  'Acton-Agua Dulce Unified School District, presentation to the Los Angeles County Committee on School District Organization, June 30, 2025, https://www.aadusd.k12.ca.us/board-of-education/upcoming-board-elections/trustee-area-map; the board\'s trustee-area map adopted February 13, 2025. The County\'s approving resolution is not yet held.'];
$e = Entry::find()->section('organizations')->status(null)->title($T)->one();
echo "$T: " . ($e ? "#{$e->id} exists" : 'create') . PHP_EOL;
if (!$APPLY) { return; }
$n = 0; $os = $svc->getSectionByHandle('organizations');
if (!$e) { $e = new Entry(); $e->sectionId = $os->id; $e->setTypeId($os->getEntryTypes()[0]->id); $e->title = $T; }
if (!str_contains((string)$e->body, 'unified district in 1993')) {
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  $v = ['orgType' => 'school', 'schoolLevel' => 'district', 'ncesId' => '0600001', 'cdsCode' => '19753090000000', 'gradeSpan' => 'K to 12', 'orgAddress' => '32248 North Crown Valley Road, Acton, CA 93510', 'dateFounded' => '1993', 'dateFoundedEdtf' => '1993', 'foundedEvidence' => 'contemporary',
    'body' => $BODY, 'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($NOTES), $NOTES), 'recordProvenance' => 'create_aadusd_2026_10_04.php, 4 October 2026: governs Agua Dulce'];
  $e->setFieldValues(array_intersect_key($v, array_flip($h))); if (!$el->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); } $n++; }
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
foreach (Craft::$app->getCategories()->getGroupByHandle('neighborhood') ? craft\elements\Category::find()->group('neighborhood')->slug(['acton', 'agua-dulce'])->all() : [] as $c) { $ids = $e->neighborhood->status(null)->ids(); if (!in_array($c->id, $ids)) { $e->setFieldValue('neighborhood', array_merge($ids, [$c->id])); if (!$el->saveElement($e)) { throw new \RuntimeException('communities'); } $n++; } }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('create_aadusd_2026_10_04.php', $n, 'verified', 'Acton-Agua Dulce Unified School District');
echo "done: $n writes (#{$e->id} {$e->slug})" . PHP_EOL;
