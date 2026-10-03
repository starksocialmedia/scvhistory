/**
 * READ ONLY against the database. Where each of the 69 live errors in Grok's
 * consolidated list stands in the archive (Nathan, 3 October 2026: "Work
 * through the 69 live errors"). Reads inventory/review/live-errors-consolidated.json
 * (entries CE01 to CE69) and writes live-errors-consolidated-status.json and .md.
 *
 * The live site is Leon Worden's and is never edited, and his prose, and that of
 * the other authors the archive carries, is never rewritten (AGENTS.md). So for
 * each error this finds, rather than assumes:
 *   class   IN CRAFT          the wrong text is in a Craft record now
 *           RECORD, NOT FOUND a record carries the page, the text is not in it
 *                             (a title tag, an index label, an image code)
 *           LEGACY ONLY       no record carries the page
 *           ARCHIVE DATA      an error in the archive's own records
 *   records the page's records (legacyUrl, sourcePath, personLegacyUrl) and every
 *           record holding the wrong text, with the field it sits in and who
 *           wrote that text (bodyAuthorship, where the record has it)
 *   done    whether fix_live_errors_in_craft.php (2 October) already put a
 *           Correction note on those records, matched on a phrase of that note
 *   action  CORRECTION NOTE  an error in an author's text: a dated, sourced note
 *           FIELD FIX        an error in data the archive derived: fixed in the field
 *           NONE             nothing to do in Craft (legacy only, or a spelling
 *                            slip kept as printed); listed for Leon
 *           HOLD             care items: Indigenous sites or ethnography
 *                            (TATAVIAM_AUDIT.md); medium or NEEDS_VERIFICATION
 *                            corrections; a correction that rests on Perkins and
 *                            Reynolds together, which count as one source
 *                            (docs/PROFILES.md, the Perkins reliability rule)
 *           ALREADY DONE     corrected on 2 October, or settled since
 * The action is a decision, written in $DECIDE with its reason; the class, the
 * records and "done" are found. A decision that disagrees with what is found
 * (a note proposed where the text is not in Craft) is printed as MISMATCH.
 *
 * Photograph titles came from Leon's captions and title tags, so a title is his
 * text and is corrected by note. No "Oustanding" here sits in a link label the
 * archive rebuilt: it is Leon's sidebar text carried in the body, and #3003's
 * title is his title tag.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_live_errors_consolidated.php'))"
 */

ini_set('memory_limit', '2048M');
$root = \Craft::getAlias('@root');
$src = json_decode((string)file_get_contents("$root/inventory/review/live-errors-consolidated.json"), true);
if (!$src || count($src['entries'] ?? []) !== 69) { echo 'cannot read the 69 entries' . PHP_EOL; return; }

$CAMULOS = [3743, 3745, 3747, 3749, 3751, 3753, 3755, 3757, 3759, 3761, 3763, 4301, 4303, 4305, 4307, 4309, 4311, 4313, 4315, 4317, 4319, 4321, 4323, 4325, 4327, 4329, 4331, 4333, 4335, 4337, 4389, 4391, 5675];

/* The wrong text, as phrases found in the records (case and spacing ignored). */
$FIND = [
    'CE01' => ['In 1962 he wrote a series of articles'],
    'CE02' => ['Oustanding Citizen 1964'],
    'CE03' => ['In 1870 he bought the Decatur House'],
    'CE04' => ['Iron Horse'],
    'CE05' => ['1925-1926', 'circa 1926'],
    'CE06' => ['Newly Completed Hart Mansion'],
    'CE07' => ['DO5001'],
    'CE08' => ['FH2101'],
    'CE09' => ['purchased the 254-acre Horseshoe Ranch'],
    'CE10' => ['Completed in 1927'],
    'CE11' => ['Radio Show, 12-13-1934'],
    'CE12' => ['you would be trespassing', 'Trespassing is prohibited'],
    'CE13' => ["Ygnacio's stepfather"],
    'CE14' => ['conculsion'],
    'CE15' => ["sheriff's sale in 1875"],
    'CE16' => ['for good in 1854'],
    'CE17' => ['Prior to the Great Drought of 1864-65'],
    'CE18' => ['13,599 acres'],
    'CE19' => ['where he was mayor'],
    'CE20' => ['served a short stint in the first Legislature', 'Legislature at statehood in 1850'],
    'CE21' => ['which operates the Hart Museum', 'operates the William S. Hart Museum'],
    'CE22' => ['Defiance, in Texas', 'Fort Defiance, Texas'],
    'CE23' => ['Oustanding Citizen 1964', 'In 1962 he wrote a series of articles'],
    'CE24' => ['Gift of Buffalo Coat to Rudy Vallee'],
    'CE25' => [],
    'CE26' => ['the ranch was divided among his widow and children'],
    'CE27' => ['for Sunday, July 11'],
    'CE28' => ['3-28-1928'],
    'CE29' => ['just two families since the end of war'],
    'CE30' => ['1,500-acre Camulos section'],
    'CE31' => ['1930 or earlier'],
    'CE32' => ['Carrera Marble', 'arrived in Newhall in 1918'],
    'CE33' => ['Date unkonwn'],
    'CE34' => ['Mary Pickford and William S. Hart filmed'],
    'CE35' => ['When the hotel burned in 1887'],
    'CE36' => ['was bured in the family plot'],
    'CE37' => ['Served as assemblyman in 1852 and 1856'],
    'CE38' => ['(Unratified), 1852', '(Unratified) 1852'],
    'CE39' => ['Narragansut', ' limted ', 'plausable'],
    'CE40' => ['Mexican-born missionary'],
    'CE41' => ['railroad tunnel in July 1875'],
    'CE42' => ['uncle to Jacopa Feliz', 'ownership was transfered'],
    'CE43' => ['Smithsonlan'],
    'CE44' => ['one mile each side of the Pass', 'California and Nevada in 1852', "about '86, that the franchise expired"],
    'CE45' => ['one of the daughters of Andres Pico'],
    'CE46' => ['Burrows, D.H.'],
    'CE47' => ['Pacific Railroad Survey. Washington, DC 1952'],
    'CE48' => ['Van Valkenberg, R.'],
    'CE49' => ['The mayor of the town, Don Ygnacio'],
    'CE50' => ['He had already sold Rancho Tejon to General Beale'],
    'CE51' => ['appointed to the U.S. Naval Academy by President Andrew Jackson'],
    'CE52' => ['got five thousand dollars', 'with five thousand dollars to do the work'],
    'CE53' => ['who was then a state senator'],
    'CE54' => ['46: Surrey'],
    'CE55' => ['met the president at Saugus'],
    'CE56' => ['Joseph H. Tolfree started the Saugus Eating House'],
    'CE57' => ['married seventeen-year-old Winifred Westover'],
    'CE58' => ['yawning chasm'],
    'CE59' => ["inside Lloyd Houghton's Hap-A-Lan dance hall", 'in a little cowboy suit'],
    'CE60' => ['The section at the top in italics is inaccurate'],
    'CE61' => ['For The Signal date?'],
    'CE62' => [],
    'CE63' => ['Henry Newhall Builds an Empire', 'Wednesday, April 24, 2002'],
    'CE64' => ['Dec. 16, 1903'],
    'CE65' => ['took a crack at the famous director in his autobiography'],
    'CE66' => ['in 1962 when he wrote'],
    'CE67' => ['Beale dies at the age of 72'],
    'CE68' => ['died in 1880, at age 72', 'at the age of seventy-two years'],
    'CE69' => [],
];
/* Short or common phrases, searched only in the page's own records. */
$PAGE_ONLY = ['CE04', 'CE05', 'CE07', 'CE08', 'CE10', 'CE11', 'CE28', 'CE31', 'CE54', 'CE61'];
/* Data checks: [record, field, the wrong value]. */
$DATA = [
    'CE23' => [3003, 'photoSourceCode', 'AP0828'],
    'CE25' => [3215, 'photoSourceCode', 'LW2311l'],
];
/* Hits that are not the error: the archive quoting the error in order to
   correct it, or a phrase that is right where it stands. */
$NOT_ERROR = [
    'CE40' => [291 => 'the archive\'s own profile of Antonio del Valle quotes the 1999 feature to correct it'],
    'CE63' => [12484 => 'the date of "A Third-Grade History Cheat Sheet", which is that column\'s own'],
];
/* Notes put on by fix_live_errors_in_craft.php on 2 October: [its code, a
   phrase of the note as it reads now (reword_public_notes.php and
   fix_research_wording.php reworded some on 3 October)]. */
$DONE = [
    'CE20' => ['D3', '3 September 1851'],
    'CE21' => ['L1', 'no longer operates the Hart Museum'],
    'CE22' => ['B6', 'Fort Defiance was in New Mexico Territory'],
    'CE26' => ['D4', 'partitioned only in 1870'],
    'CE27' => ['L7', 'the Sunday was 12 July 1936'],
    'CE29' => ['D5', 'grant of 22 January 1839'],
    'CE30' => ['D5', 'about 1,340 acres'],
    'CE46' => ['R2', 'H.D. Barrows'],
    'CE47' => ['R2', 'Pacific Railroad Reports'],
    'CE48' => ['R2', 'Richard Van Valkenburgh'],
    'CE53' => ['D7', 'nominated for the State Senate in 1882'],
    'CE55' => ['R5', 'Benjamin Harrison did not stop at Saugus'],
    'CE56' => ['R5', 'James Herbert Tolfree'],
    'CE57' => ['L12', 'Winifred Westover was about 22'],
    'CE58' => ['L13', 'baffled ones that remain'],
    'CE64' => ['R9', 'died on 16 December 1902'],
    'CE65' => ['B7', 'My Life East and West'],
];

/* The decision for each entry: [action, reason, the records it applies to]. */
$LEON = 'for Leon: the live page';
$DECIDE = [
    'CE01' => ['CORRECTION NOTE', 'Leon\'s caption, carried on #27368 (AP0828) and #3003 (LW2232): a note that "The Story of Our Valley" ran in The Signal from April 1954 to January 1955, from the series\' own introduction (#1418) and the Los Angeles Times profile of 1977 (cited on #333). The series date rests on the series itself, not on Perkins\'s word.', [27368, 3003]],
    'CE02' => ['NONE', '"Oustanding" in #3003, #4931 and #5399 is Leon\'s sidebar label text carried in the body, not a link label the archive rebuilt: kept as printed. #3003\'s title is corrected under CE23. ' . $LEON . 's ap0828, lw3113, lw3550.', []],
    'CE03' => ['NONE', 'Legacy only (bealeafb, Beale AFB text). ' . $LEON . ': an editor\'s note, 1871.', []],
    'CE04' => ['NONE', 'The mislabelled link is on the legacy Beale\'s Cut index only; #932 cites LW2159 for the Tom Mix item, correctly. ' . $LEON . '.', []],
    'CE05' => ['NONE', 'Legacy only (cp1702, mu9067: NHMLA dates). ' . $LEON . '.', []],
    'CE06' => ['NONE', 'Legacy only (cp1703). Medium. ' . $LEON . '.', []],
    'CE07' => ['NONE', 'Legacy title tag only (di5001); low, the code NEEDS_VERIFICATION. ' . $LEON . '.', []],
    'CE08' => ['NONE', 'Legacy only (fh2701 credit line). ' . $LEON . '.', []],
    'CE09' => ['CORRECTION NOTE', 'The error page (fh2701) is legacy only, but the same claim is in Reynolds\'s chapter 53 (#2131): a note from Tom Sitton\'s 1989 survey of the deeds (MU8901), which the archive\'s Hart profile (#16356) already follows. fh2701 itself ' . $LEON . '.', [2131]],
    'CE10' => ['NONE', 'Legacy only (fh2701). Medium. ' . $LEON . '.', []],
    'CE11' => ['NONE', 'Legacy only (film.htm index link). ' . $LEON . '.', []],
    'CE12' => ['HOLD', 'Leon\'s dated 2005 column (#12166) was right when written; the change is the City\'s purchase approved 28 October 2025, and close of escrow and today\'s access rules are NEEDS_VERIFICATION. An update note waits on those. Not an error of fact; hb1806 is legacy only.', []],
    'CE13' => ['NONE', 'Legacy only (hl7101). ' . $LEON . '.', []],
    'CE14' => ['NONE', 'Legacy only (hs9032 webmaster\'s note typo). ' . $LEON . '.', []],
    'CE15' => ['NONE', 'Legacy only (lastar18640514wolfskill webmaster\'s note); the 46,460 acres NEEDS_VERIFICATION. ' . $LEON . '.', []],
    'CE16' => ['NONE', 'Legacy only (lo8801, lo8802 captions). ' . $LEON . '.', []],
    'CE17' => ['NONE', 'Legacy only (lp_oaklandtrib110418 webmaster\'s note). The "Francsico" in the Lang photo captions in Craft is a different sentence. ' . $LEON . '.', []],
    'CE18' => ['CORRECTION NOTE', 'Leon\'s text on #293 and Reynolds\'s chapter 15 (#851): a note that the three figures exceed the patented rancho (the 1875 patent, 48,611.88 acres, cited on #291) and that the heirs held undivided shares until 1870 (Perkins citing the partition decree). The archive\'s OWN prose repeats the figures as fact on #915 (del Valle Family) and #16446 (Rancho San Francisco): that is archive text, to be rewritten rather than noted, and is not done here (Nathan).', [293, 851]],
    'CE19' => ['CORRECTION NOTE', 'Leon\'s text on #293: alcalde in 1850 (Pen Pictures 1889) and city council 1852 and 1856 (the City of Los Angeles record of his offices on LW2052). "Acutally" is a spelling slip, kept as printed.', [293]],
    'CE20' => ['CORRECTION NOTE', '#293 was corrected on 2 October (D3). The same claim is in Leon\'s caption on #5617 (LW3768), which 2 October missed: the D3 note goes there too. lw2532 is legacy only, ' . $LEON . '.', [5617]],
    'CE21' => ['ALREADY DONE', '#2759 corrected on 2 October (L1). fh2701 and the NHMLA boilerplate captions are legacy only, ' . $LEON . '.', []],
    'CE22' => ['CORRECTION NOTE', 'Seven records corrected on 2 October (B6). Leon\'s map caption on #2983 (LW2208) reads "Fort Defiance, Texas", a wording 2 October did not match: the B6 note goes there too. lw2161 is legacy only.', [2983]],
    'CE23' => ['FIELD FIX', '#3003 (lw2232): photoSourceCode AP0828 -> LW2232. The code was read from the legacy title tag, which carries the wrong code; the page\'s own image is lw2232.jpg and its credit line reads LW2232, and the code is what the record\'s image is found by (AP0828 finds the other photograph). Its title "Oustanding" is Leon\'s title tag: corrected by note, with the 1962 sentence (CE01).', [3003]],
    'CE24' => ['ALREADY DONE', 'Settled on 2 October (L2): the label survives in Craft only as text from the legacy sidebar, with no link. The wrong link ' . $LEON . 's lw2298a, lat19360719hart.', []],
    'CE25' => ['FIELD FIX', '#3215 (lw2311m, page 13): photoSourceCode LW2311l -> LW2311m. The code came from the legacy title tag, which repeats page 12\'s code; LW2311l finds page 12\'s image (#3213 is page 12). lw2311m.jpg is not yet in the volume, so the plate will say so, which is true. The title tag ' . $LEON . '.', [3215]],
    'CE26' => ['ALREADY DONE', '#4461 corrected on 2 October (D4).', []],
    'CE27' => ['ALREADY DONE', '#5333 corrected on 2 October (L7): photoDate 8 July 1936, the AP\'s date, with a note that the paper ran it on Sunday 12 July.', []],
    'CE28' => ['NONE', 'Legacy only (lw3629 title, film.htm). ' . $LEON . '.', []],
    'CE29' => ['ALREADY DONE', 'The 33 Camulos records corrected on 2 October (D5). #631 Rancho Camulos does not carry the sentence. Medium on the live site.', []],
    'CE30' => ['ALREADY DONE', 'The 33 Camulos records corrected on 2 October (D5).', []],
    'CE31' => ['NONE', 'Legacy only (mu8491 partly fixed by Leon; film.htm entry still reads 1930 or earlier). ' . $LEON . '.', []],
    'CE32' => ['NONE', 'Legacy only (perkins-desmond is not in Craft). Carrara NEEDS_VERIFICATION. ' . $LEON . '.', []],
    'CE33' => ['NONE', '"Date unkonwn" on #1450 is a spelling slip in the web edition\'s byline line: kept as printed, as the archive keeps verbatim typos. ' . $LEON . '.', []],
    'CE34' => ['CORRECTION NOTE', 'Perkins\'s 1958 text on #1438: a film story, unverified under the Perkins rule; the AFI Catalog\'s cast lists for Rags (1915) do not include Hart (as #16356 already says). Only the Hart part is noted; "The Virginians" and "The Light of the Eastern Star" are low confidence and left alone.', [1438]],
    'CE35' => ['CORRECTION NOTE', 'Perkins on #1446: a year slip on a lesser event. Leon\'s note on "The Birth of Newhall" (#869) and LW2273 (#3151) give 23 October 1888.', [1446]],
    'CE36' => ['NONE', '"bured" is in Leon\'s editor\'s note to Perkins (#1434, #27374): a spelling slip, kept as printed. ' . $LEON . '.', []],
    'CE37' => ['CORRECTION NOTE', 'Perkins\'s footnote on #1434 and #27374: an institutional label (Assembly against Common Council), unverified under the Perkins rule. MEDIUM-HIGH, flagged: the note states only what the sources hold (JoinCalifornia\'s one election, 1851; the City\'s record of his 1856 council term) and does not assert he was not in the Assembly in 1856 (Assembly Journal NEEDS_VERIFICATION).', [1434, 27374]],
    'CE38' => ['HOLD', 'Not in Craft (#913 Tataviam does not carry the label); legacy ridge.htm and tataviam.htm. The fix (1851) is harmless to pass to Leon, but anything on the Tataviam pages migrates under TATAVIAM_AUDIT.md.', []],
    'CE39' => ['NONE', 'The typos are in Leon\'s notes on sauguscafe, legacy only. "Narragansut" in Reynolds\'s chapter 46 (#2117) is his spelling, kept as printed. ' . $LEON . '.', []],
    'CE40' => ['NONE', 'Legacy only (sg090199a, a 1999 Signal feature). The archive\'s own profile of Antonio del Valle (#291) already says he was not a missionary. ' . $LEON . '.', []],
    'CE41' => ['HOLD', 'Perkins 1947 (#869). The correction (July 1876) rests on Perkins\'s own 1954 sequence and Reynolds part 39, which under the Perkins rule are one source, and the day is NEEDS_VERIFICATION: it needs a source independent of both (a railroad report or a newspaper of 1876).', []],
    'CE42' => ['NONE', '"Jacopa" and "transfered" on #1432 are in Leon\'s editor\'s notes; "Jacopa" is also Perkins\'s own spelling throughout (#1422, #1434, #27374). Kept as printed. ' . $LEON . '.', []],
    'CE43' => ['NONE', '"Smithsonlan" on #1420 is a typing or OCR slip in Perkins\'s chapter on the first inhabitants: kept as printed, and the chapter is under TATAVIAM_AUDIT.md in any case. ' . $LEON . '.', []],
    'CE44' => ['HOLD', 'Perkins part 4 (#1426), three claims, MEDIUM-HIGH: the right of way (two miles, statute text NEEDS_VERIFICATION), the franchise\'s end (1883-84, from Ripley), and the superintendency (Superintendent for California, appointed 3 March 1853). The archive disagrees with itself on the last: its own Beale profile (#327) says "California and Nevada from 1853" and the Tejon Ranch timeline says 1852. Settle #327 first.', []],
    'CE45' => ['CORRECTION NOTE', 'Perkins part 6 (#1430): a family-relationship label, unverified under the Perkins rule. Leon\'s note c to Perkins\'s "History of Pico Canyon Oil Production" (#1440) gives the marriage: a niece, 1863.', [1430]],
    'CE46' => ['ALREADY DONE', '#2171 corrected on 2 October (R2).', []],
    'CE47' => ['ALREADY DONE', '#2171 corrected on 2 October (R2).', []],
    'CE48' => ['ALREADY DONE', '#2171 corrected on 2 October (R2).', []],
    'CE49' => ['CORRECTION NOTE', 'Reynolds part 25 (#2075): same sources as CE19 (Pen Pictures 1889; the City\'s record of his offices on LW2052), both independent of Perkins and Reynolds.', [2075]],
    'CE50' => ['CORRECTION NOTE', 'Reynolds part 26 (#2077): Beale bought Rancho El Tejon in 1865 and Rancho de Castac in 1866 (Tejon Ranch Company timeline, at SCVHistory.com). Note: #327\'s calendar dates still carry "In 1855 he acquired Rancho El Tejon", from its earlier text (Nathan).', [2077]],
    'CE51' => ['CORRECTION NOTE', 'Reynolds part 28 (#2081): the Naval Academy opened in 1845; Beale graduated from the Naval School in Philadelphia in 1842 (Pollack, 2014, at SCVHistory.com; #327 says the same).', [2081]],
    'CE52' => ['HOLD', 'Reynolds part 28 (#2081). The LA Star of 4 April 1863, as Perkins transcribes it (#1426), estimates "the additional work" at $16,000 to $18,000; Reynolds\'s $5,000 is what Beale "got" from the supervisors. The two figures may measure different things, so this is not yet a contradiction: it needs the Board\'s minutes. The archive\'s own prose repeats the $5,000 on #327 (Beale) and #932 (Beale\'s Cut).', []],
    'CE53' => ['ALREADY DONE', '#2083 corrected on 2 October (D7).', []],
    'CE54' => ['NONE', 'Legacy title tag only; #2107\'s title is right ("41. Baron of Casteca"). ' . $LEON . '.', []],
    'CE55' => ['ALREADY DONE', '#2117 corrected on 2 October (R5).', []],
    'CE56' => ['ALREADY DONE', '#2117 corrected on 2 October (R5). Medium on the live site.', []],
    'CE57' => ['ALREADY DONE', '#2131 corrected on 2 October (L12).', []],
    'CE58' => ['ALREADY DONE', '#2131 corrected on 2 October (L13).', []],
    'CE59' => ['CORRECTION NOTE', 'Reynolds part 54 (#2133), in part. The burial is noted: Leon\'s "Requiem to a Little Soldier" (2003, from the Newhall Signal of 29 March and 5 April 1928) has the boy buried at Oakwood Cemetery, Chatsworth, and calls the cowboy outfit "an oft-repeated local legend that hasn\'t been refuted" (quoted, not called false). HOLD the morgue: Leon puts it in "the Masonic lodge in Newhall, normally a happy and popular dance hall", which may be the same building as the Hap-A-Lan; not noted until that is settled.', [2133]],
    'CE60' => ['NONE', 'Leon\'s webmaster\'s note on #2141 still calls the top "inaccurate" after the top was corrected: his wording, kept. Note: #2141\'s calendar dates carry "December 17, 1925", taken from "(Reynolds said December 17, 1925)" (Nathan). ' . $LEON . '.', []],
    'CE61' => ['HOLD', 'Reynolds\'s "Ethnography of Castaic" (#2175): ethnography, under TATAVIAM_AUDIT.md. The "date?" placeholder did not come into Craft; originalPublishDate is empty.', []],
    'CE62' => ['HOLD', 'Bowers Cave (#2177): tribal consultation before anything else; the record is disabled (disable_bowers_cave.php). Not a factual error.', []],
    'CE63' => ['HOLD', 'Craft\'s title is already right ("E.F. Beale and the Beasts of Tejon"). The byline date, April 24, 2002, is in Leon\'s body; the file name lw011399 and the "1999 calendar ... available now" fit 13 January 1999, MEDIUM. Also: #12168\'s originalPublishDate holds a paragraph of the column\'s text, not a date: a field fix waiting on the right date (Nathan).', []],
    'CE64' => ['ALREADY DONE', '#12168 corrected on 2 October (R9).', []],
    'CE65' => ['ALREADY DONE', '#12290 and #12370 corrected on 2 October (B7).', []],
    'CE66' => ['CORRECTION NOTE', 'Leon\'s 1996 column, both copies (#12542 and #12574, the old version): a note that the series ran April 1954 to January 1955.', [12542, 12574]],
    'CE67' => ['NONE', 'Legacy only (tejonranchtimeline, Tejon Ranch Co. text). ' . $LEON . ': an editor\'s note, 71.', []],
    'CE68' => ['NONE', 'Legacy only (times111101, penpictures_rdelvalle). ' . $LEON . ': [sic] notes, 71.', []],
    'CE69' => ['ALREADY DONE', 'ARCHIVE DATA: #15908 "Edward F. Beale" is in the trash and #327 is the record (settled before this list).', []],
];

/* Field handles by layout element uid, for every entry type. */
$uidH = [];
foreach (Craft::$app->getEntries()->getAllEntryTypes() as $t) { foreach ($t->getFieldLayout()->getCustomFields() as $f) { $uidH[$f->layoutElement->uid] = $f->handle; } }
$rows = (new \craft\db\Query())->select(['es.elementId', 'es.title', 'es.content', 'sec' => 's.handle', 'el.enabled'])
    ->from(['es' => '{{%elements_sites}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = es.elementId')
    ->innerJoin(['en' => '{{%entries}}'], 'en.id = es.elementId')->innerJoin(['s' => '{{%sections}}'], 's.id = en.sectionId')
    ->where(['el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->all();
$norm = fn($t) => mb_strtolower(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$t), ENT_QUOTES | ENT_HTML5))));
$flat = function ($v) use (&$flat) { return is_array($v) ? implode(' | ', array_map($flat, $v)) : (string)$v; };
$docs = []; $byPath = [];
foreach ($rows as $r) {
    $id = (int)$r['elementId'];
    if (isset($docs[$id])) { continue; }
    $f = ['title' => $norm($r['title'])]; $raw = [];
    foreach (json_decode((string)$r['content'], true) ?: [] as $k => $v) {
        $h = $uidH[$k] ?? $k; $raw[$h] = $v;
        if (in_array($h, ['legacyHtml', 'editorNotes'], true)) { continue; }
        $f[$h] = $norm($flat($v));
        if (in_array($h, ['legacyUrl', 'sourcePath', 'personLegacyUrl'], true) && is_string($v) && preg_match('~(/scvhistory/[^\s?#]+)$~i', trim($v), $m)) { $byPath[strtolower($m[1])][$id] = true; }
    }
    $docs[$id] = ['sec' => $r['sec'], 'enabled' => (bool)$r['enabled'], 'title' => (string)$r['title'], 'f' => $f, 'auth' => is_string($raw['bodyAuthorship'] ?? null) ? $raw['bodyAuthorship'] : '', 'notes' => is_array($raw['editorNotes'] ?? null) ? $raw['editorNotes'] : [], 'raw' => $raw];
}
$corrNotes = function (int $id) use ($docs) {
    $out = [];
    foreach ($docs[$id]['notes'] ?? [] as $r) {
        if (!is_array($r)) { continue; }
        $h = (string)($r['col1'] ?? $r['heading'] ?? ''); $n = (string)($r['col2'] ?? $r['note'] ?? '');
        if (str_starts_with($h, 'Correction') && trim($n) !== '') { $out[] = $n; }
    }
    return $out;
};
$label = fn(int $id) => '#' . $id . ' ' . $docs[$id]['sec'] . ($docs[$id]['enabled'] ? '' : ' (disabled)') . ' "' . mb_substr($docs[$id]['title'], 0, 50) . '"';

$out = []; $mismatch = [];
foreach ($src['entries'] as $e) {
    $code = $e['id'];
    $pageRecs = [];
    foreach (array_merge([$e['page']], $e['also_on']) as $u) {
        if (preg_match('~(/scvhistory/[^\s?#]+)$~i', $u, $m)) { foreach (array_keys($byPath[strtolower($m[1])] ?? []) as $id) { $pageRecs[$id] = true; } }
    }
    $hits = [];
    foreach ($FIND[$code] ?? [] as $ph) {
        $p = $norm($ph);
        foreach ($docs as $id => $d) {
            if (isset($NOT_ERROR[$code][$id]) || (in_array($code, $PAGE_ONLY, true) && !isset($pageRecs[$id]))) { continue; }
            foreach ($d['f'] as $h => $t) { if (str_contains($t, $p)) { $hits[$id][$h] = true; } }
        }
    }
    $data = [];
    if (isset($DATA[$code])) {
        [$id, $fld, $wrong] = $DATA[$code];
        $have = is_string($docs[$id]['raw'][$fld] ?? null) ? $docs[$id]['raw'][$fld] : '';
        $data = ['record' => $id, 'field' => $fld, 'now' => $have, 'wrong' => $wrong, 'still_wrong' => $have === $wrong];
        if ($have === $wrong) { $hits[$id][$fld] = true; }
    }
    if (str_starts_with($e['page'], 'CC migration')) { $class = 'ARCHIVE DATA'; }
    elseif ($hits) { $class = 'IN CRAFT'; }
    elseif ($pageRecs) { $class = 'RECORD, NOT FOUND'; }
    else { $class = 'LEGACY ONLY'; }
    /* Already corrected on 2 October: a Correction note carrying the phrase. */
    $done = [];
    if (isset($DONE[$code])) {
        [$c, $marker] = $DONE[$code];
        $pool = array_unique(array_merge(array_keys($hits), array_keys($pageRecs), $code === 'CE29' || $code === 'CE30' ? $CAMULOS : []));
        foreach ($pool as $id) { foreach ($corrNotes($id) as $n) { if (str_contains($n, $marker)) { $done[$id] = $c; } } }
    }
    $recs = [];
    foreach ($hits as $id => $fs) {
        $recs[] = ['id' => $id, 'label' => $label($id), 'fields' => array_keys($fs), 'authorship' => $docs[$id]['auth'], 'corrected_2_oct' => $done[$id] ?? ''];
    }
    [$action, $why, $targets] = $DECIDE[$code];
    /* Checks of the decision against what was found. */
    $miss = [];
    if (in_array($action, ['CORRECTION NOTE', 'FIELD FIX'], true)) {
        foreach ($targets as $t) { if (!isset($hits[$t])) { $miss[] = "#$t is a target but does not carry the wrong text"; } if (isset($done[$t])) { $miss[] = "#$t already corrected on 2 October ({$done[$t]})"; } }
    }
    if ($action === 'ALREADY DONE' && isset($DONE[$code])) {
        foreach ($hits as $id => $fs) { if (!isset($done[$id]) && !in_array($id, [12484], true) && $docs[$id]['auth'] !== 'editorial-2026') { $miss[] = "#$id carries the text and has no 2 October note"; } }
        if (!$done) { $miss[] = 'no 2 October note found'; }
    }
    if ($action === 'NONE' && $class === 'IN CRAFT' && !preg_match('~kept as printed|kept\.|his wording~i', $why)) { $miss[] = 'in Craft but no action'; }
    foreach ($hits as $id => $fs) { if ($docs[$id]['auth'] === 'editorial-2026' && !str_contains($why, "#$id")) { $miss[] = "#$id is the archive's own text and the decision does not name it"; } }
    if ($code === 'CE69' && (isset($docs[15908]) || !isset($docs[327]))) { $miss[] = '#15908 is not in the trash, or #327 is missing'; }
    if ($miss) { $mismatch[$code] = $miss; }
    $out[] = [
        'id' => $code, 'page' => $e['page'], 'also_on' => $e['also_on'], 'error' => $e['current_text'], 'correction' => $e['correction'], 'source' => $e['source'],
        'confidence' => $e['confidence'], 'grok_fix' => $e['fix'], 'live_status' => $e['live_status'], 'dossier_items' => $e['source_items'],
        'class' => $class, 'page_records' => array_map($label, array_keys($pageRecs)), 'records' => $recs, 'data' => $data,
        'already_corrected' => $done ? array_map(fn($id, $c) => "#$id ($c)", array_keys($done), $done) : [],
        'action' => $action, 'why' => $why, 'targets' => $targets, 'for_leon' => $class === 'LEGACY ONLY' || $class === 'RECORD, NOT FOUND' || str_contains($why, 'for Leon'), 'mismatch' => $miss,
    ];
}

$cClass = array_count_values(array_column($out, 'class'));
$cAction = array_count_values(array_column($out, 'action'));
$done2 = array_filter($out, fn($x) => $x['already_corrected']);
file_put_contents("$root/inventory/review/live-errors-consolidated-status.json", json_encode(['built' => date('c'), 'by' => 'scripts/import/build_live_errors_consolidated.php', 'total' => count($out), 'by_class' => $cClass, 'by_action' => $cAction, 'corrected_2_oct' => count($done2), 'entries' => $out], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$md = ['# The 69 live errors: where each stands in the archive', '',
    'Built ' . date('j F Y') . ' by `scripts/import/build_live_errors_consolidated.php` from Grok\'s `live-errors-consolidated.json`. Read only. The class, the records and the 2 October notes are found in the database; the action is a decision, with its reason. The live site is Leon Worden\'s and is not edited; authors\' text in Craft is not rewritten, so an error in it gets a dated, sourced Correction note (`fix_live_errors_consolidated.php`).', '',
    '| class | count |', '| --- | --- |'];
foreach (['IN CRAFT', 'RECORD, NOT FOUND', 'LEGACY ONLY', 'ARCHIVE DATA'] as $k) { $md[] = "| $k | " . ($cClass[$k] ?? 0) . ' |'; }
$md[] = '';
$md[] = '| action | count |'; $md[] = '| --- | --- |';
foreach (['CORRECTION NOTE', 'FIELD FIX', 'ALREADY DONE', 'HOLD', 'NONE'] as $k) { $md[] = "| $k | " . ($cAction[$k] ?? 0) . ' |'; }
$md[] = '';
$md[] = count($done2) . ' entries were already corrected, in whole or in part, by `fix_live_errors_in_craft.php` on 2 October: ' . implode(', ', array_column($done2, 'id')) . '.';
$md[] = '';
if ($mismatch) { $md[] = '**MISMATCH** between decision and data: ' . implode('; ', array_map(fn($k, $v) => "$k: " . implode(', ', $v), array_keys($mismatch), $mismatch)); $md[] = ''; }
$md[] = '## HOLD';
$md[] = '';
foreach ($out as $x) { if ($x['action'] === 'HOLD' || str_contains($x['why'], 'HOLD the')) { $md[] = '- **' . $x['id'] . '** (' . $x['confidence'] . '): ' . $x['why']; } }
$md[] = '';
$md[] = '## For Leon: errors on the live site with nothing to do in Craft';
$md[] = '';
$md[] = 'Legacy only, a title tag or index label, or a slip kept as printed in Craft. Nathan passes these to Leon; the archive does not edit his site.';
$md[] = '';
foreach ($out as $x) { if ($x['action'] === 'NONE') { $md[] = '- **' . $x['id'] . '** ' . preg_replace('~^https://scvhistory\.com~', '', $x['page']) . ': ' . mb_substr($x['error'], 0, 160) . ' -> ' . mb_substr($x['correction'], 0, 200); } }
$md[] = '';
$md[] = '## Every entry';
$md[] = '';
foreach ($out as $x) {
    $md[] = '### ' . $x['id'] . ': ' . $x['class'] . ' / ' . $x['action'];
    $md[] = '';
    $md[] = '- **Page:** ' . $x['page'] . ($x['also_on'] ? ' (also ' . implode(', ', array_map(fn($u) => preg_replace('~^https://scvhistory\.com~', '', $u), $x['also_on'])) . ')' : '');
    $md[] = '- **Error:** ' . ($x['id'] === 'CE62' ? 'a locational description of an archaeological site (not repeated here)' : $x['error']);
    $md[] = '- **Correction:** ' . $x['correction'] . ' (' . $x['confidence'] . ')';
    if ($x['page_records']) { $md[] = '- **Page records:** ' . implode('; ', $x['page_records']); }
    foreach ($x['records'] as $r) { $md[] = '- **Text in:** ' . $r['label'] . ' [' . implode(', ', $r['fields']) . ']' . ($r['authorship'] ? ' authorship ' . $r['authorship'] : '') . ($r['corrected_2_oct'] ? ' **corrected 2 Oct (' . $r['corrected_2_oct'] . ')**' : ''); }
    if ($x['data']) { $md[] = '- **Data:** #' . $x['data']['record'] . ' ' . $x['data']['field'] . ' = "' . $x['data']['now'] . '"' . ($x['data']['still_wrong'] ? ' (wrong)' : ''); }
    $md[] = '- **Action:** ' . $x['action'] . '. ' . $x['why'];
    if ($x['mismatch']) { $md[] = '- **MISMATCH:** ' . implode('; ', $x['mismatch']); }
    $md[] = '';
}
file_put_contents("$root/inventory/review/live-errors-consolidated-status.md", implode("\n", $md) . "\n");

echo 'total ' . count($out) . PHP_EOL . 'class  ' . json_encode($cClass) . PHP_EOL . 'action ' . json_encode($cAction) . PHP_EOL . 'already corrected 2 Oct: ' . count($done2) . PHP_EOL;
foreach ($out as $x) {
    echo str_pad($x['id'], 6) . str_pad($x['class'], 19) . str_pad($x['action'], 17) . implode(', ', array_map(fn($r) => '#' . $r['id'] . ($r['corrected_2_oct'] ? '*' : ''), $x['records'])) . ($x['mismatch'] ? '  MISMATCH: ' . implode('; ', $x['mismatch']) : '') . PHP_EOL;
}
echo '(* = corrected on 2 October)' . PHP_EOL . 'MISMATCH: ' . ($mismatch ? count($mismatch) : 'none') . PHP_EOL;
