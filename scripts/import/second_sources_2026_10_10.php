/**
 * The single-source claims with an agreeing second source: the second source added to the footnote each claim cites
 * (Nathan's overnight brief of 10 October 2026, item 4; the claims are inventory/review/single-source-second-sources-2026-10-09.md).
 * Of the 32 "found, agrees", 7 were already in the record's footnote (Bell for Banning's claims 11 and 15, Kreider and Walling
 * for Lyon's 8, Pollack and the obituary for Lyon's 9, the Warren paper, the Standard Oiler and the Times for Mentry's 17 and 18,
 * the ECPP burial for del Valle's 43): nothing to add. Jenkins's 5 are added by jenkins_certificate_obituary_2026_10_10.php,
 * which imports the two documents they cite. This adds the other 20 less claim 44, on ten records.
 * Every quotation was read in the source on 10 October 2026, not taken from the 9 October report: the Archive.org full texts
 * (saved in storage/runtime/scratch/ia), the Reggie pages, and the archive's own records. Where the second source covers only
 * part of a sentence, the footnote says which part. A footnote shared by several sentences gets one addition, labelled by point.
 * The two claims whose second source differs (38 and 45, del Valle's division of the rancho) are not added. Claim 44 (the heirs)
 * is held with them: its only second source, the Camulos nomination, draws on del Castillo, whose 1,800-acre reading was found
 * tonight to rest on a family manuscript he describes and corrects (a candidate seventh instance, written up, not fixed). So 19.
 * #333 footnote 7 also loses "checked in the archive's review of Reynolds's sources", which named a review, not the page; it now
 * quotes the bibliography itself (the 9 October report's "Read from a description", 6).
 * Only bodies written by us (bodyAuthorship editorial-2026) are touched. Idempotent: an addition already present is skipped.
 * Dry run by default; $APPLY = true writes.
 */
use craft\elements\Entry;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $bad = [];
$IA = "$root/storage/runtime/scratch/ia/"; $R = '/mnt/reggie/scvhistory.com/scvhistory/';
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'Bancroft, History of California, vol. I (Archive.org full text)', $IA . 'historyofcalifor01banc.txt'],
  ['file', 'Bancroft, vol. III', $IA . 'historyofcalifor03banc.txt'],
  ['file', 'Bancroft, vol. IV', $IA . 'historyofcal04bancroft.txt'],
  ['file', 'Bancroft, vol. V (with the Pioneer Register)', $IA . 'worksofhuberthow0022unse.txt'],
  ['file', 'Hoffman, Reports of Land Cases (1862)', $IA . 'reportslandcase00distgoog.txt'],
  ['file', 'Bryant, What I Saw in California', $IA . 'whatisawincalifoin00brya.txt'],
  ['file', 'Bell, Reminiscences of a Ranger', $IA . 'reminiscencesofr00bellrich.txt'],
  ['file', 'Biographical Directory of the American Congress (1950)', $IA . 'biographicaldire00unit.txt'],
  ['file', 'Remi Nadeau\'s obituary page', $R . 'obit-nadeauremi.htm'],
  ['file', 'Reynolds\'s bibliography', $R . 'signal/reynolds/bibliography.html'],
  ['file', 'the Rancho Camulos National Register nomination, part 3', $R . 'camulos-nrhp3.htm'],
  ['record', 'Craft body text of #12304, #4261 and #28281, quoted as second sources', 'the column, the caption and the history', 'read'],
  ['record', 'Craft field footnotes on ten person records', 'their sources', 'read'],
]);
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
/* Each quotation must still be in its source as read: whitespace and line-break hyphens normalised, as the reading was. */
$norm = fn($s) => preg_replace('/\s+/', ' ', preg_replace('/-\s*\n\s*/', '', $s));
$src = [];
$in = function ($file, $needle, $latin = false) use (&$src, $norm, &$bad) { if (!isset($src[$file])) { $t = (string)@file_get_contents($file); if ($latin) { $t = html_entity_decode(strip_tags(iconv('ISO-8859-1', 'UTF-8', $t)), ENT_QUOTES | ENT_HTML5); } $src[$file] = $norm($t); }
  if (!str_contains($src[$file], $needle)) { $bad[] = 'not found in ' . basename($file) . ": \"$needle\""; } };
$in($IA . 'worksofhuberthow0022unse.txt', 'Valle (Antonio del), 1819, Mex. lieut of the S. Blas infantry comp.');
$in($IA . 'worksofhuberthow0022unse.txt', 'In ’39 he was grantee of S. Francisco rancho, iii. 633, where he died in ’41');
$in($IA . 'worksofhuberthow0022unse.txt', 'son of the lieut and nat. of Jalisco, who came to Cal. with');
$in($IA . 'worksofhuberthow0022unse.txt', 'next morning, January 13th, it received the signa');
$in($IA . 'reportslandcase00distgoog.txt', 'granted January 22d, 1839, by Juan B. Alvarado');
$in($IA . 'historyofcalifor03banc.txt', 'Valle had not yet moved his family to the rancho.');
$in($IA . 'historyofcal04bancroft.txt', 'Larkin writes to N. Y. Sun that a common laborer can pick up $2 per day.');
$in($IA . 'historyofcalifor01banc.txt', 'Pedro Fages, a native of Catalonia, and first lieutenant of a company of the 1st battalion, 2d regiment');
$in($IA . 'historyofcalifor01banc.txt', 'twenty-five Catalan volunteers');
$in($IA . 'historyofcalifor03banc.txt', 'came back as gov. and com. gen. of Cal. Sept. \'82 to April \'91');
$in($IA . 'whatisawincalifoin00brya.txt', 'through which we ascend over a difficult pass in a range of elevated hills between us and the plain of San Fernando, or Couenga');
$in($IA . 'reminiscencesofr00bellrich.txt', 'stage that ever went out of the Valley of the Angels to astonish the aborigines in the mountain fastnesses beyond');
$in($IA . 'biographicaldire00unit.txt', 'unsuccessful as the first Republican candidate for President of the United States in 1856');
$in($IA . 'biographicaldire00unit.txt', 'Governor of Arizona Territory 1878-1881');
$in($IA . 'biographicaldire00unit.txt', 'died in New York City July 13, 1890');
$in($IA . 'biographicaldire00unit.txt', 'from September 9, 1850, to March 3, 1851');
$in($R . 'obit-nadeauremi.htm', 'purchased the home ranch at Soledad where he afterwards spent his life', true);
$in($R . 'obit-nadeauremi.htm', 'Their home ranches adjoined', true);
$in($R . 'signal/reynolds/bibliography.html', 'Perkins, A.B. Rancho San Francisco. Los Angeles, 1957.', true);
$in($R . 'camulos-nrhp3.htm', 'Antonio del Valle and his family lived at the eastern edge of the ranch near Castaic in the former San Fernando Mission granary adobe building', true);
foreach ([12304 => 'after ascertaining that Mr. Hart is agreeable to having the high school named after him', 4261 => '252-4947 was the home phone number of committee secretary Jill Klajic', 28281 => 'Among them were Jill Klajic and Allan Cameron'] as $id => $q) {
  $e = Entry::find()->id($id)->status(null)->one(); $t = ''; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $v = $e->getFieldValue($f->handle); if (is_string($v) || (is_object($v) && !$v instanceof \craft\elements\db\ElementQuery && method_exists($v, '__toString'))) { $t .= ' ' . (string)$v; } }
  if (!str_contains($norm($t), $q)) { $bad[] = "#$id does not hold \"$q\""; }
}
$BIO = 'Biographical Directory of the American Congress, 1774-1949 (1950), read in the Internet Archive\'s copy, https://archive.org/details/biographicaldire00unit';
/* [record id, title, footnote number, addition] ; or [.., .., .., ['replace' => old tail, 'with' => new tail]] */
$PLAN = [
  [18869, 'Remi Nadeau', 1, ' Second source for the ranch: his obituary, Los Angeles Times, 28 November 1941, also printed in The Signal that day, as transcribed at /scvhistory/obit-nadeauremi.htm: "33 years ago, he came to the valley of the Little Santa Clara, and purchased the home ranch at Soledad where he afterwards spent his life"; his and John W. Mitchell\'s "home ranches adjoined." It does not place the ranch at Whites Canyon, which still rests on the summary.'],
  [18714, 'Phineas Banning', 2, ' Bell himself (note 1), pages 322 to 323: Banning "drove the first stage that ever went out of the Valley of the Angels to astonish the aborigines in the mountain fastnesses beyond."'],
  [16356, 'William S. Hart', 17, ' Second source for the naming: The Signal of 9 August 1945, as quoted in Leon Worden\'s column "Keep Canyon, change Hart district\'s name" (article #12304): the board "initiated a move to have the name changed from Santa Clarita to William S. Hart. The board did this after ascertaining that Mr. Hart is agreeable to having the high school named after him." That shows the renaming begun in his lifetime; it does not say when the school opened.'],
  [15874, 'Jill Klajic', 4, ' Second sources for her part in the committee: Leon Worden\'s caption to its membership form (photograph #4261): "252-4947 was the home phone number of committee secretary Jill Klajic"; and Connie Worden-Roberts\'s history of the cityhood drives (document #28281): "A City Formation Committee was formed ... Among them were Jill Klajic and Allan Cameron." Neither says she was paid staff; the caption calls the committee "an open, not-for-profit volunteer group."'],
  [333, 'Arthur Buckingham Perkins', 7, ['replace' => ', and its bibliography, which lists Perkins\'s Rancho San Francisco; checked in the archive\'s review of Reynolds\'s sources, 2026.', 'with' => ', and its bibliography, /scvhistory/signal/reynolds/bibliography.html, read 10 October 2026: "Perkins, A.B. Rancho San Francisco. Los Angeles, 1957."']],
  [317, 'Andrés Pico', 3, ' Second source for the surrender: Hubert Howe Bancroft, History of California, vol. V (1886), page 404, read in the Internet Archive\'s copy: at Cahuenga, "next morning, January 13th, it received the signatures of the respective commandants, Frémont and Pico," and the articles are "made and entered into at the ranch of Cowenga this 13th day of Jan., A. D. 1847." Bancroft does not name the Feliz house.'],
  [311, 'Thomas O. Larkin', 1, ' Second source: Hubert Howe Bancroft, History of California, vol. IV (1886), in his annals of Los Angeles, read in the Internet Archive\'s copy: "June 30, 1846, Larkin writes to N. Y. Sun that a common laborer can pick up $2 per day. Larkin\'s Doc., MS., iv. 183." Bancroft gives the placer\'s name in 1846 as San Feliciano; he does not place it in a canyon off Piru Creek.'],
  [307, 'John C. Frémont', 2, ' Second sources, read 10 October 2026. The crossing: Edwin Bryant, who marched with the battalion, What I Saw in California (1849), under January 10, 1847, read in the Internet Archive\'s copy: "Crossing the plain we encamped ... in the mouth of a canada, through which we ascend over a difficult pass in a range of elevated hills between us and the plain of San Fernando, or Couenga"; and Hubert Howe Bancroft, History of California, vol. V (1886), page 404, on the signing at Cahuenga on January 13th. His life: ' . $BIO . ': "born in Savannah, Ga., January 21, 1813"; in the Senate "from September 9, 1850, to March 3, 1851"; "unsuccessful as the first Republican candidate for President of the United States in 1856"; "Governor of Arizona Territory 1878-1881"; "died in New York City July 13, 1890." Neither names the pass for him, and the directory does not use "the Pathfinder" or name Kit Carson.'],
  [291, 'Antonio del Valle', 1, ' Second sources, read 10 October 2026. His rank, arrival and San Fernando: Hubert Howe Bancroft, History of California, vol. V (1886), Pioneer Register, page 755, read in the Internet Archive\'s copy: "Valle (Antonio del), 1819, Mex. lieut of the S. Blas infantry comp."; "comisionado for the secularization of S. Fern., where he served also as majordomo to \'37"; "In \'39 he was grantee of S. Francisco rancho, iii. 633, where he died in \'41." The same register gives "nat. of Jalisco" to Ygnacio ("son of the lieut and nat. of Jalisco, who came to Cal. with Echeandía"), not to Antonio, and does not say Monterey. The grant: Ogden Hoffman, Reports of Land Cases (1862), appendix, Commission no. 318, District Court no. 305 S.D.: "granted January 22d, 1839, by Juan B. Alvarado to Antonio del Valle"; it does not mention Durán, and Bancroft, vol. III (1885), page 633, has the grant made "much against the wishes of the S. Fernando Ind." without naming him. The rancho house: the National Register of Historic Places nomination for Rancho Camulos (San Buenaventura Research Associates, 1996), /scvhistory/camulos-nrhp3.htm: "Antonio del Valle and his family lived at the eastern edge of the ranch near Castaic in the former San Fernando Mission granary adobe building"; and Bancroft, vol. III, on Hartnell\'s visit of June 1839: "Valle had not yet moved his family to the rancho."'],
  [291, 'Antonio del Valle', 8, ' Guadalajara is in Jalisco, the birthplace this record gives him; the register says he was of the city, not that he was born there.'],
  [287, 'Pedro Fages', 1, ' Second sources, read 10 October 2026, in the Internet Archive\'s copies of Hubert Howe Bancroft, History of California: vol. I (1884), page 118, "Lieutenant Fages arrived from Guaymas with twenty-five Catalan volunteers"; page 485, note: "Pedro Fages, a native of Catalonia, and first lieutenant of a company of the 1st battalion, 2d regiment, of the Catalan Volunteer Light Infantry"; vol. III (1885), Pioneer Register: "came back as gov. and com. gen. of Cal. Sept. \'82 to April \'91." They do not cover his Sonoran service.'],
];
$in($IA . 'historyofcalifor01banc.txt', 'Lieutenant Fages ariived from Guaymas with twenty-five Catalan volunteers');
$in($IA . 'historyofcalifor03banc.txt', 'much againsl the wishes of the S. Fernando Ind.');
$in($IA . 'worksofhuberthow0022unse.txt', 'comisionado for the secularization of S. Fern., where he served also as major');

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$todo = [];
foreach ($PLAN as [$id, $title, $k, $add]) {
  $e = $todo[$id] ?? Entry::find()->id($id)->section('persons')->status(null)->one();
  if ($e?->title !== $title) { $bad[] = "#$id is not $title"; continue; }
  if ((string)$e->bodyAuthorship !== 'editorial-2026') { $bad[] = "#$id body is not editorial-2026"; continue; }
  $rows = $e->getFieldValue('footnotes'); $rows = is_array($rows) ? $rows : array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? '')], iterator_to_array($rows));
  if (!isset($rows[$k - 1])) { $bad[] = "#$id has no footnote $k"; continue; }
  $cur = $rows[$k - 1]['note'];
  if (is_array($add)) {
    if (str_contains($cur, $add['with'])) { echo "#$id fn $k: done already" . PHP_EOL; continue; }
    if (substr_count($cur, $add['replace']) !== 1) { $bad[] = "#$id fn $k: the text to replace is not there once"; continue; }
    $rows[$k - 1]['note'] = str_replace($add['replace'], $add['with'], $cur); echo "#$id $title fn $k: \"..." . trim($add['replace']) . "\" -> \"..." . trim($add['with']) . '"' . PHP_EOL;
  } else {
    if (str_contains($cur, trim($add))) { echo "#$id fn $k: done already" . PHP_EOL; continue; }
    $rows[$k - 1]['note'] = $cur . $add; echo "#$id $title fn $k: +" . trim($add) . PHP_EOL;
  }
  if (preg_match('~\x{2014}~u', is_array($add) ? $add['with'] : $add)) { $bad[] = "#$id fn $k: em dash"; }
  $e->setFieldValue('footnotes', $rows); $todo[$id] = $e;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  - ' . implode(PHP_EOL . '  - ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { echo 'nothing written' . PHP_EOL; return; }
foreach ($todo as $id => $e) { if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('second_sources_2026_10_10.php', $n, 'verified', "second sources added to the footnotes of $n person records (19 claims)");
echo "done: $n records" . PHP_EOL;
