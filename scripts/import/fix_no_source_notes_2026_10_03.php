/**
 * The "no source" notes of 26 September to 3 October 2026: correct the wrong
 * ones and record the search behind every one (Nathan, 3 October 2026,
 * approving "the 16-note correction script"; and his rule, docs/PROFILES.md, "A
 * note that says no source exists records the search").
 *
 * WHY. A faulty search (the agent shell's grep, which silently skips the
 * mirror's latin-1 pages) put false "no source" notes on public records this
 * week. Every such note was re-searched on 3 October with a Python script
 * reading all 84,849 text files of the mirror as cp1252, and against the
 * archive's records (inventory/review/no-source-notes-2026-10-03.md and .json):
 * 80 notes; 59 survive, 7 were wrong, 9 partly wrong, 5 concern outside federal
 * registers the mirror cannot test.
 *
 * WHAT IT DOES
 *   1. Corrects 14 of the 16 wrong or partly wrong notes, each now citing the
 *      source found (exact-match replacement: a note or sentence that is not
 *      exactly as read on 3 October is refused). With #339's, the same name
 *      error on #18869 (the freighter called "Remi Allen Nadeau"). The other
 *      two are done by their own scripts: Couts (#323) by widen_couts_profile.php,
 *      the California Battalion (#946) by restore_california_battalion.php.
 *   2. Adds to each record whose note was re-searched one editorNotes row, "How
 *      the archive was searched": what was searched, where, how and when, one
 *      sentence per note. The public note keeps its plain sentence.
 *   3. removed-claims.json: the search beside the three retirements that rested
 *      on "nothing found" (De Anza Expedition, the two missions), and an entry
 *      for Ward Connerly (#16411, removed 1 October), whose removal reason ("the
 *      archive's only mention") was wrong though the decision stands. Written
 *      only on apply, after the database commits.
 *   4. Lists, without touching them, other notes on this week's records that
 *      say something was not found and are not covered here.
 * Quotations are checked against byte copies of the mirror pages
 * (_source_texts.php) or the archive records they cite.
 * One transaction; read-back; apply log. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_no_source_notes_2026_10_03.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $els = Craft::$app->getElements();
$SRC = require "$root/scripts/import/_source_texts.php"; $ws = $SRC['ws'];
$bad = $SRC['bad'];
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$rowsOf = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$fnOf = fn($e) => array_values(array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? 'editorial-2026')], array_filter($e->footnotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$DATA = json_decode(<<<'JSON'
{
 "pre": "Searched 3 October 2026 by a Python script reading every one of the 84,849 text pages of the legacy SCVHistory.com site (its pages, the flipbook books' text and their search files, each read as cp1252 so no page is skipped), and the archive's own records.",
 "search": {
  "339": [
   "The name Allen: \"Remi Allen\", \"Allen Nadeau\" and \"R. A. Nadeau\" on pages naming Nadeau. One page gives it, to the freighter's great-great-grandson, the author born in 1920 (caption to record #3695); none gives it to the freighter.",
   "His burial place: \"buried\", \"cemetery\", \"interred\", \"entombed\" and \"funeral\" within 300 characters of \"Nadeau\". His 1887 obituary gives only the funeral from his home; nothing gives the grave."
  ],
  "18869": [
   "The name of his grandfather, the freighter: \"Remi Allen\", \"Allen Nadeau\". The only page with the name Allen gives it to the author born in 1920, not to the freighter."
  ],
  "315": [
   "Carson in the valley: every one of the 50 pages naming \"Kit Carson\" read. Leon Worden's column of June 24, 1998 says he and Frémont \"rode through the area in the mid-1840s\"; no other page places him here (Cullimore has him at Tejon Pass, outside the valley).",
   "His dates and burial: Dr. Alan Pollack's story read in full; \"1868\", \"buried\" and \"Taos\" near \"Carson\". Pollack gives the day of death and the burial at Taos; no page gives December 24, 1809 or Madison County."
  ],
  "343": [
   "Acosta in the valley and his dates: all 8 pages naming \"Rodolfo Acosta\" read, and \"Rudy Acosta\". His own legacy page (LW3399) gives his birth from his Texas birth certificate, his death, and films made in the valley. Burial: \"Forest Lawn\", \"buried\" within 200 characters of his name; nothing."
  ],
  "2588": [
   "Others describing him: all 109 pages naming \"Tim Whyte\" read, and the 10 archive records. Pauline Harte's 1997 columns, the Citizen of 1988 as annotated, and his Signal bylines of 1990 to 2003 describe or name him."
  ],
  "285": [
   "His burial place: \"buried\", \"cemetery\", \"interred\", \"grave\" and \"Santa Clara Mission\" within 200 characters of \"Tiburcio\" or \"Vasquez\" (25 pages). Dick Cox (Real West, 1965) gives the Catholic Cemetery at Santa Clara.",
   "A birth on April 7, 1835: \"April 7, 1835\", \"7 April 1835\", \"Apr. 7\" within 300 characters of \"Vasquez\". Nothing."
  ],
  "297": [
   "His dates and burial: \"1782\", \"1721\", \"March 1\", \"buried\", \"interred\", \"Carmel\", \"died\", \"death\" within 200 characters of \"Crespí\" or \"Crespi\" (11 pages). Leon Worden's Carmel Mission galleries give the day of death and the crypt; nothing gives March 1, 1721."
  ],
  "299": [
   "His burial place: \"buried\", \"interred\", \"entombed\", \"grave\", \"tomb\" within 200 characters of \"Serra\" (37 pages). Leon Worden's Carmel Mission galleries give it."
  ],
  "317": [
   "His dates, burial and offices: \"1810\", \"November 18\", \"born\", \"1876\", \"buried\", \"interred\", \"cemetery\", \"funeral\", \"Assembly\", \"senator\" near \"Andrés Pico\", \"Andres Pico\", \"Gen. Pico\", \"Don Andrés\". His death date and his Senate seat are found; his birthplace (San Diego) but not the date; nothing on his burial."
  ],
  "287": [
   "The town of Guissona: \"Guissona\" anywhere, and \"born\", \"birth\", \"native of\" within 150 characters of \"Fages\". Nothing."
  ],
  "303": [
   "His birth: \"Santa Barbara\", \"1841\" and \"born\" within 200 characters of \"Juventino\" (9 pages). The year 1841 only; nothing on the place or the months before the marriage."
  ],
  "307": [
   "His burial place: \"buried\", \"interred\", \"Rockland\", \"Trinity\", \"grave\", \"cemetery\" within 200 characters of \"Frémont\" or \"Fremont\" (20 pages). Nothing on his grave."
  ],
  "309": [
   "The day: \"February 9\", \"Feb. 9\", \"9 February\", \"1798\" within 200 characters of \"Stearns\". The year only."
  ],
  "327": [
   "His burial place: \"buried\", \"interred\", \"Rock Creek\", \"cemetery\", \"entombed\" within 200 characters of \"Beale\" (39 pages), and Bonsal's 1912 biography read at his death. It tells of the interment but not where."
  ],
  "333": [
   "The middle name Burnett: \"Arthur Burnett\", \"Burnett Perkins\", \"A. Burnett\". Nothing."
  ],
  "341": [
   "A Sacramento birthplace: \"Sacramento\" and \"born\" within 250 characters of \"Dante Acosta\". The City's biography (born and raised in Southern California) only."
  ],
  "311": [
   "Larkin in the valley: \"Santa Clara\", \"Newhall\", \"San Feliciano\", \"Placerita\", \"San Francisquito\", \"Piru\", \"Castaic\" within 300 characters of \"Larkin\". Perkins has him writing from Monterey; nothing places him here.",
   "His dates: \"1802\", \"1858\", \"Charlestown\", \"born\", \"died\" within 200 characters of \"Larkin\". Nothing."
  ],
  "281": [
   "His burial place: \"Eternal Valley\", \"buried\", \"interred\", \"cemetery\", \"ashes\", \"scattered\", \"services\" within 300 characters of \"Jerry Reynolds\" (13 pages), and his memorial page read. Nothing."
  ],
  "16356": [
   "The Lindbergh story: \"Lindbergh\" within 400 characters of \"Hart\". Leon Worden's captions repeat it as \"unproved rumors\"; no source for it.",
   "A birth record: \"birth certificate\", \"birth record\", \"baptism\" within 200 characters of \"Hart\". Leon Worden writes that the Newburgh records were lost to fire."
  ],
  "21584": [
   "The middle name and the birth date: \"Gustavus\" anywhere; \"1843\", \"January 28\", \"born\" within 200 characters of \"Scofield\". Nothing."
  ],
  "291": [
   "A record of his death: \"1841\", \"died\", \"death\", \"burial\" within 200 characters of \"Antonio del Valle\" (42 pages). Dates given; no record of the death itself."
  ],
  "21582": [
   "Her professional career: every page naming \"Gutzeit\" (15) read. Board minutes, rosters and sponsor lists; nothing on her profession."
  ],
  "25391": [
   "Other sources on him: \"Brian Walters\", \"Brian D. Walters\", and \"WiSH\" or \"Hart District Education Foundation\" near \"Walters\". No page of the legacy site names him."
  ],
  "28264": [
   "Other sources on him: \"Brian Walters\", \"Brian D. Walters\", and \"WiSH\" or \"Hart District Education Foundation\" near \"Walters\". No page of the legacy site names him."
  ],
  "28208": [
   "The date she left the board: \"Christy Smith\" within 250 characters of \"board\", \"trustee\", \"resign\". Only the October 2017 video naming her board president."
  ],
  "25409": [
   "A 2016 contest for his seat: \"Trunkey\" anywhere. No page of the legacy site names him; the absence rests on the County's returns as compiled by the California Elections Data Archive."
  ],
  "394": [
   "An official source for 39.5 square miles: \"39.5 square\", \"39.5-square\", \"39.79\". Leon Worden's cityhood pages give 39.5 as the commission's figure; no official document gives it."
  ],
  "16039": [
   "A source of 1876 for 16 June: \"June 16, 1876\", \"16 June 1876\". Two later sources give it (Standard Oil's Among Ourselves, 1929, and Leon Worden's stock-certificate caption); neither is of the time.",
   "Where the oldest-in-the-world claim is made: \"oldest existing refinery\", \"oldest refinery\" and variants (42 pages). The City's 1991 General Plan and Leon Worden's captions say it, citing nothing.",
   "Scofield's testimony: \"750 gallons\". Only the ASME text that quotes it."
  ],
  "12290": [
   "Where Hart criticized Ford: \"lead horses\", \"Remington\" within 300 characters of \"Ford\". Only this column and copies of it, which credit the 1929 autobiography the note rules out."
  ],
  "12370": [
   "Where Hart criticized Ford: \"lead horses\", \"Remington\" within 300 characters of \"Ford\". Only this column and copies of it, which credit the 1929 autobiography the note rules out."
  ],
  "512": [
   "His name and its variants (\"Eugene Darr\", \"Gene Darr\", \"Darr\") on every page, leaving out the memorial pages themselves. Only the casualty index and later Darrs in Hart yearbooks. No source ties him to the valley beyond the legacy page.",
   "His burial place: \"Darr\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "522": [
   "His name and its variants (\"Albert Lee Moore\", \"Albert L. Moore\") on every page, leaving out the memorial pages themselves. Only the casualty index. No source ties him to the valley beyond the legacy page."
  ],
  "560": [
   "His name and its variants (\"Garry Wingfield\", \"Wingfield\") on every page, leaving out the memorial pages themselves. Other Wingfields only. No source ties him to the valley beyond the legacy page."
  ],
  "566": [
   "His name and its variants (\"James Bartlett\", \"Jim Bartlett\") on every page, leaving out the memorial pages themselves. Nothing. No source ties him to the valley beyond the legacy page."
  ],
  "574": [
   "His name and its variants (\"Ozal\", \"O. R. Smart\") on every page, leaving out the memorial pages themselves. Only the casualty index. No source ties him to the valley beyond the legacy page."
  ],
  "520": [
   "His name and its variants (\"Archibald Beall\", \"Archie Beall\", \"Beall\") on every page, leaving out the memorial pages themselves. Other Bealls only. No source ties him to the valley beyond the legacy page."
  ],
  "546": [
   "His name and its variants (\"Brian Prosser\", \"Brian Cody Prosser\") on every page, leaving out the memorial pages themselves. Nothing. No source ties him to the valley beyond the legacy page."
  ],
  "542": [
   "His name and its variants (\"Dean Todd\", \"Dean Glenn Todd\") on every page, leaving out the memorial pages themselves. Nothing; no home is given anywhere."
  ],
  "562": [
   "His name and its variants (\"Jack Harland\", \"Jack L. Harland\", \"Harland\") on every page, leaving out the memorial pages themselves. The Newhall School commencement program of 1929 lists a Jack Harland among its graduates."
  ],
  "548": [
   "His name and its variants (\"Whisler\") on every page, leaving out the memorial pages themselves. The day of birth: nothing beyond the federal files."
  ],
  "554": [
   "His name and its variants (\"Gilbert Montenegro\") on every page, leaving out the memorial pages themselves. The day of birth: nothing beyond the federal files."
  ],
  "556": [
   "His name and its variants (\"Morissett\") on every page, leaving out the memorial pages themselves. The day of birth: nothing beyond the federal files."
  ],
  "558": [
   "His name and its variants (\"Albert Thomas\", \"Albert Edward Thomas\") on every page, leaving out the memorial pages themselves. The day of birth: nothing beyond the federal files."
  ],
  "1356": [
   "His burial place: \"Bruce St. Louis\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1358": [
   "His burial place: \"Radtke\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1360": [
   "His burial place: \"Charles Clarence Smith\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1362": [
   "His burial place: \"David Reeder\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1364": [
   "His burial place: \"Ables\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1366": [
   "His burial place: \"Frank Ortega\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1368": [
   "His burial place: \"Monteleone\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1370": [
   "His burial place: \"Gary Turnbull\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1372": [
   "His burial place: \"Henry Klinger\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1374": [
   "His burial place: \"John Borders\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1376": [
   "His burial place: \"Joseph Godwin\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1378": [
   "His burial place: \"Michael Fay\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1380": [
   "His burial place: \"Stephen Peterson\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "1382": [
   "His burial place: \"Gemas\" within reach of \"buried\", \"cemetery\", \"interred\", \"burial\", \"laid to rest\", and his memorial page read. Nothing."
  ],
  "568": [
   "A record of promotion: \"Private\", \"PFC\", \"rank\" within 300 characters of \"Johnny Cordova\" (25 pages). The timeline calls him PFC; no record of a promotion."
  ],
  "514": [
   "The note is about a federal register the legacy site does not hold; the register is as the note says. \"Kenaston\" on every page of the legacy site: only the memorial pages name him."
  ],
  "576": [
   "The note is about a federal register the legacy site does not hold; the register is as the note says. \"Robert Cone\", \"Robert Russell Cone\" on every page of the legacy site: only the memorial pages name him."
  ],
  "580": [
   "The note is about a federal register the legacy site does not hold; the register is as the note says. \"Thomas Milton Ross\", \"Tom Ross\" on every page of the legacy site: only the memorial pages name him."
  ],
  "1395": [
   "The note is about a federal register the legacy site does not hold; the register is as the note says. \"Jimmie Ball\", \"James Robert Ball\" on every page of the legacy site: only the memorial pages name him."
  ]
 },
 "reg": {
  "942": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"De Anza Expedition\", \"Anza Expedition\", and \"Anza\" within 300 characters of \"Santa Clara\", \"Newhall\", \"Castaic\", \"Tejon\", \"San Fernando\", \"Soledad\" or \"valley\". Anza Drive, the Anza trail on trail maps and a library title; Reynolds has the 1776 expedition at the Colorado River only. Nothing puts it in the valley.",
  "16515": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"Mission San Francisco de Asís\", \"Mission Dolores\" within 400 characters of \"Santa Clar\", \"Newhall\", \"San Fernando\", \"Castaic\", \"Tataviam\", \"Camulos\". A list of missions and a library title only.",
  "16517": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"Mission Santa Cruz\". One page, about Santa Cruz County remains. Nothing ties it to the valley."
 },
 "connerly": {
  "record": 16411,
  "title": "Ward Connerly",
  "section": "persons",
  "why": "Removed 1 October 2026 (Nathan: \"remove unless he has a valley connection\"). Four of Leon Worden's 1996 columns mention him, all on Proposition 209 (January 31, September 4, October 9 and November 13), and none places him in the valley. The removal script's reason, that the January 31 column was the archive's only mention, was wrong; the decision stands.",
  "removed": "2026-10-01",
  "by": "scripts/import/remove_insignificant_persons.php; this entry added by scripts/import/fix_no_source_notes_2026_10_03.php",
  "search": "Searched 3 October 2026 (Python over all 84,849 text pages of the legacy site, read as cp1252, and the archive's records): \"Connerly\". Six pages: the four columns and two copies of the column index. None places him in the valley."
 }
}
JSON, true);
$HEAD = 'How the archive was searched';

/* 1. The corrections. 'note' replaces a substring of one editorNotes row,
   'body' a substring of the body, 'fnadd' appends a footnote, 'fn' replaces a
   substring of one footnote. */
$FIX = [
 ['WRONG', 339, 'note', 'This record formerly called him Remi Allen Nadeau. No source for the middle name Allen has been found; the sources call him Remi or Rémi Nadeau.',
   'This record formerly called him Remi Allen Nadeau. That name belongs to his great-great-grandson, the historian Remi Allen Nadeau, born in 1920 (Leon Worden, caption to record #3695); no source in the archive gives it to the freighter, whom the sources call Remi or Rémi Nadeau.'],
 ['WRONG (same error)', 18869, 'note', 'the grandson of the Los Angeles freighter Remi Allen Nadeau (1821-1887), who is linked here.', 'the grandson of the Los Angeles freighter Rémi Nadeau (1821-1887), who is linked here.'],
 ['WRONG', 315, 'body', 'No source in this archive places him in the Santa Clarita Valley.',
   'Leon Worden wrote in 1998 that Frémont and Carson "rode through the area in the mid-1840s," meaning the pass between the San Fernando and Santa Clarita valleys; he gives no date, and no other source in the archive places Carson in the valley.[3]'],
 ['WRONG', 315, 'fnadd', '3', 'Leon Worden, "Beale\'s Cut, parade, the \'Marsha question\'," The Signal, June 24, 1998, article #12334 in this archive: "Explorers John C. Fremont and Kit Carson rode through the area in the mid-1840s."'],
 ['PARTLY', 315, 'note', 'No source has been found for the day of his death, May 23, 1868, or for his burial place.',
   'Dr. Alan Pollack gives his death at Fort Lyons, Colorado, on May 23, 1868, and his burial near his old home at Taos, New Mexico, where the grave is in Kit Carson Memorial State Park (note 1).'],
 ['PARTLY', 343, 'body', 'No source in this archive places him in the Santa Clarita Valley.',
   'Leon Worden notes that a few of the films and television episodes he appeared in were made in the Santa Clarita Valley, among them Apache Warrior (1957), which used Vasquez Rocks; no source in the archive places him here otherwise.[3]'],
 ['PARTLY', 343, 'fnadd', '3', 'Leon Worden, "Rodolfo Acosta Co-stars in \'Apache Warrior\' (1957)," LW3399, as carried on SCVHistory.com, /scvhistory/lw3399.htm: "A few of the motion pictures and television episodes that featured Acosta were made in the Santa Clarita Valley"; "According to his Texas birth certificate, Rodolfo entered the world as a U.S. citizen on July 29, 1920"; "Acosta succumbed to cancer November 7, 1974, at the Motion Picture Home in Woodland Hills."'],
 ['PARTLY', 343, 'note', 'This record formerly gave his birth as July 29, 1920, in El Paso, Texas, and his death as November 7, 1974. The source here gives the years only. No source has been found for his burial place.',
   'This record gives the years only, but Leon Worden, citing his Texas birth certificate, gives his birth as July 29, 1920, in El Paso, and his death on November 7, 1974, at the Motion Picture Home in Woodland Hills (note 3). No source has been found for his burial place.'],
 ['WRONG', 2588, 'body', 'No other source in the archive describes him, so these columns are this record\'s only source.',
   'Pauline Harte\'s columns of 1997 call him the managing editor of The Signal, and his byline is on Signal news stories in the archive from 1990 to 2003; the introduction to The Citizen of 1988 on SCVHistory.com recalls him joining The Signal as a cub reporter, and notes he was back there in 2018.[3][4]'],
 ['WRONG', 2588, 'fnadd', '3', 'Pauline Harte, "Little glitches make the best memories," July 22, 1997, article #12745 in this archive: Tim Whyte, "the managing editor of The Mighty Signal"; and her column of April 29, 1997, article #12757: "the illustrious, multi-talented managing editor of this provocatively unique newspaper."'],
 ['WRONG', 2588, 'fnadd', '4', 'The Santa Clarita Valley Citizen, September 18, 1988, with SCVHistory.com\'s introduction, as carried there, /scvhistory/citizen19880918.htm: "a cub reporter named Tim Whyte"; "Tim is back as of 2018 after a decade-long vacation." His Signal bylines in the archive: May 2, 1990 (/scvhistory/jd9002.htm), September 24 and 29, 1991 (/scvhistory/hs_vtc_art_1992.htm), November 18, 1991 (/scvhistory/gt8703.htm), and October 29, 2003 (/scvhistory/1003-fire-index.htm).'],
 ['WRONG', 285, 'note', 'No source has been found for his burial place, given here as Santa Clara. The New York Tribune reported only that his body was given to his friends.',
   'Dick Cox (Real West, November 1965, as carried on SCVHistory.com, /scvhistory/vasquez-cox.htm) writes that his body was taken to Santa Clara and lies in "the Catholic Cemetery at Santa Clara," where his sister guarded the grave for nearly a week. The New York Tribune reported only that his body was given to his friends.'],
 ['PARTLY', 297, 'note', 'No source has been found for the day of his death, January 1, 1782, or for his burial place.',
   'Leon Worden\'s galleries of the Carmel Mission basilica (as carried on SCVHistory.com, /gif/galleries/lw2654/ and lw2655/) give his death on January 1, 1782, and his entombment with Serra and Lasuén in crypts beneath the headstones next to the altar.'],
 ['WRONG', 299, 'note', 'No source in this archive gives his burial place.',
   'Leon Worden\'s galleries of the Carmel Mission basilica (as carried on SCVHistory.com, /gif/galleries/lw2654/ and lw2655/) record that "at his request" he "was buried beside Padre Crespí before the main altar," where Serra, Crespí and Lasuén lie in crypts beneath the headstones.'],
 ['PARTLY', 317, 'note', 'No source has yet been found for his dates of birth and death, his burial place or his later public offices, so the profile does not repeat them.',
   'Leon Worden\'s timeline gives his death at his home at 203 Main Street, Los Angeles, on February 14, 1876, and Vernette Snyder Ripley (1948) quotes the Los Angeles Evening Express\'s editorial on his death that day; Laurance Landreth Hill\'s La Reina (1929) calls him "Senator Andres Pico" in 1859. The sources here say he was born in San Diego but give no date, and none gives his burial place.'],
 ['PARTLY', 16039, 'body', 'That it is the oldest surviving refinery in the world is said in City of Santa Clarita tourism copy and in online encyclopedias, with no source; none of the landmark bodies says it.[11]',
   'That it is the oldest surviving refinery in the world is said in the City of Santa Clarita\'s General Plan of 1991 and its tourism copy, in captions on SCVHistory.com ("believed to be the oldest existing refinery in the world") and in online encyclopedias, none with a source; none of the landmark bodies says it.[11]'],
 ['PARTLY', 16039, 'fn', '11', 'City of Santa Clarita, Old Town Newhall walking tour: "believed to be the oldest existing refinery in the world," no source given.',
   'City of Santa Clarita, General Plan, Open Space and Conservation Element, Historic Resources, adopted June 25, 1991, as carried on SCVHistory.com, /scvhistory/city-historic-resources-91.htm: "This is the oldest existing oil refinery in the world." City of Santa Clarita, Old Town Newhall walking tour: "believed to be the oldest existing refinery in the world," no source given. Leon Worden, caption to AP2522 and others, /scvhistory/ap2522.htm: "believed to be the oldest existing refinery in the world."'],
 ['PARTLY', 562, 'note', 'no source tying him to the valley has been found beyond the legacy page, which does not give his home.',
   'a Jack Harland is among the graduates in the Newhall School\'s commencement program of 1929 (as carried on SCVHistory.com, /scvhistory/ku2902a.htm); that he is this man, who was 28 in 1944, is likely but not established.'],
 ['PARTLY', 562, 'note', 'No connection to the Santa Clarita Valley has been found in the archive.',
   'The only possible connection found in the archive is the Newhall School\'s commencement program of 1929, which lists a Jack Harland among its graduates.'],
];
$QUOTES = [
 ['lw2449', 'the author Remi Allen Nadeau (aka Remi Nadeau III), born Aug. 30, 1920, is the great-great-grandson of the L.A. freighter'],
 ['lw062498', 'Explorers John C. Fremont and Kit Carson rode through the area in the mid-1840s'], ['lw062498', 'the mountains separating the San Fernando and Santa Clarita valleys'],
 ['pollack1114kitcarson', 'Carson died of a ruptured aortic aneurysm at Fort Lyons, Colo., on May 23, 1868'], ['pollack1114kitcarson', 'His remains were taken for burial near his old home at Taos'], ['pollack1114kitcarson', 'his grave can be visited at Kit Carson Memorial State Park'],
 ['lw3399', 'A few of the motion pictures and television episodes that featured Acosta were made in the Santa Clarita Valley'], ['lw3399', 'which used Vasquez Rocks'],
 ['lw3399', 'According to his Texas birth certificate , Rodolfo entered the world as a U.S. citizen on July 29, 1920'], ['lw3399', 'El Paso'], ['lw3399', 'Acosta succumbed to cancer November 7, 1974, at the Motion Picture Home in Woodland Hills'],
 ['ph072297', 'the managing editor of The Mighty Signal'], ['ph042997', 'The illustrious, multi-talented managing editor of this provocatively unique newspaper'],
 ['citizen19880918', 'a cub reporter named Tim Whyte'], ['citizen19880918', 'Tim is back as of 2018 after a decade-long vacation'],
 ['jd9002', 'By Tim Whyte, Signal staff writer. The Signal | Friday, May 2, 1990'], ['hs_vtc_art_1992', 'By Tim Whyte. The Newhall Signal & Saugus Enterprise | Tuesday, September 24, 1991'], ['hs_vtc_art_1992', 'Sunday, September 29, 1991'],
 ['gt8703', 'By Tim Whyte. The Newhall Signal and Saugus Enterprise | Monday, November 18, 1991'], ['fire-index-2003', 'By Tim Whyte | The Signal, 10-29-2003'],
 ['vasquez-cox', 'Since then Vasquez\'s corpse has been allowed to rest peacefully in the Catholic Cemetery at Santa Clara'], ['vasquez-cox', 'His sister, Maria'], ['vasquez-cox', 'She guarded the grave night and day for nearly a week'], ['vasquez-cox', 'Real West magazine, November 1965'],
 ['lw2655', 'On Jan. 1, 1782, Padre Juan Crespí, friend and co-worker with Serra, passed to his reward'], ['lw2654', 'Frs. Serra, Crespi and Lasuen are entombed in crypts beneath the headstones next to the altar'],
 ['lw2655', 'at his request was buried beside Padre Crespí before the main altar'],
 ['timeline', 'February 14: Gen. Andres Pico dies at his home at 203 Main St., Los Angeles'], ['ripley14', '1876, February 14. Los Angeles Evening Express. Editorial: "General Andres Pico. On the death of Don Andres Pico'],
 ['lareina1929-p42', 'in 1859 Senator Andres Pico'], ['glossary', 'San Diego-born Gen. Andres Pico'],
 ['city-historic-resources-91', 'This is the oldest existing oil refinery in the world'], ['city-historic-resources-91', 'Adopted by the City Council June 25, 1991'], ['ap2522', 'believed to be the oldest existing refinery in the world'],
 ['ku2902a', 'Harland, Jack'], ['ku2902a', 'Commencement Program, 1929'], ['ww2_jackharland', 'Age at Loss: 28'],
 ['lw013196', 'Ward Connerly'], ['lw090496', 'Ward Connerly'], ['lw100996', 'Ward Connerly'], ['lw111396', 'Ward Connerly'],
];
foreach ($QUOTES as [$k, $q]) { $ok = $SRC['has']($k, $q); echo ($ok ? 'quote ok   ' : 'QUOTE MISSING ') . "$k: \"" . mb_substr($q, 0, 70) . '"' . PHP_EOL; if (!$ok) { $bad[] = "$k does not read \"" . mb_substr($q, 0, 60) . '"'; } }
foreach ([[3695, 'Remi Allen Nadeau'], [12334, 'Explorers John C. Fremont and Kit Carson rode through the area in the mid-1840s'], [12745, 'the managing editor of The Mighty Signal'], [12757, 'multi-talented managing editor']] as [$rid, $q]) {
    $r = $get($rid); $t = ''; if ($r) { foreach (['body', 'photoCaptionExt', 'webmasterNoteTop', 'webmasterNoteBottom'] as $h) { if ($r->getFieldLayout()->getFieldByHandle($h)) { $t .= ' ' . strip_tags((string)$r->getFieldValue($h)); } } }
    $ok = str_contains($ws($t), $ws($q)); echo ($ok ? 'record ok  ' : 'RECORD MISSING ') . "#$rid: \"$q\"" . PHP_EOL; if (!$ok) { $bad[] = "#$rid does not read \"$q\""; }
}

/* Plan every record: the fixes first, then the search row. */
$plan = []; $nFix = 0; $nFixDone = 0;
$ids = array_values(array_unique(array_merge(array_column($FIX, 1), array_map('intval', array_keys($DATA['search'])))));
foreach ($ids as $id) {
    $e = $get($id); if (!$e) { $bad[] = "#$id not found"; continue; }
    $L = $e->getFieldLayout(); $body = $L->getFieldByHandle('body') ? (string)$e->body : null; $rows = $rowsOf($e); $fns = $fnOf($e); $changed = [];
    foreach (array_filter($FIX, fn($f) => $f[1] === $id && $f[2] !== 'fn') as [$cls, , $kind, $old, $new]) {
        $nFix++;
        if ($kind === 'body') {
            if (str_contains($body, $new)) { $nFixDone++; continue; }
            if (substr_count($body, $old) !== 1) { $bad[] = "#$id body: the sentence is not exactly as read"; continue; }
            $body = str_replace($old, $new, $body); $changed['body'] = true; echo "#$id {$e->title} [$cls] BODY" . PHP_EOL . "  OLD: $old" . PHP_EOL . "  NEW: $new" . PHP_EOL;
        } elseif ($kind === 'note') {
            $hits = array_keys(array_filter($rows, fn($r) => str_contains($r['note'], $old)));
            if (array_filter($rows, fn($r) => str_contains($r['note'], $new))) { $nFixDone++; continue; }
            if (count($hits) !== 1 || substr_count($rows[$hits[0]]['note'], $old) !== 1) { $bad[] = "#$id editorNotes: the note is not exactly as read (\"" . mb_substr($old, 0, 50) . '")'; continue; }
            $i = $hits[0]; echo "#$id {$e->title} [$cls] EDITOR NOTE \"{$rows[$i]['heading']}\"" . PHP_EOL . '  OLD: ' . $rows[$i]['note'] . PHP_EOL;
            $rows[$i]['note'] = str_replace($old, $new, $rows[$i]['note']); $changed['editorNotes'] = true; echo '  NEW: ' . $rows[$i]['note'] . PHP_EOL;
        } elseif ($kind === 'fnadd') {
            $at = array_filter($fns, fn($r) => $r['number'] === $old);
            if ($at) { if (array_values($at)[0]['note'] === $new) { $nFixDone++; continue; } $bad[] = "#$id already has a different footnote $old"; continue; }
            if (count($fns) !== (int)$old - 1) { $bad[] = "#$id has " . count($fns) . " footnotes, expected " . ((int)$old - 1); continue; }
            $fns[] = ['number' => $old, 'note' => $new, 'source' => 'editorial-2026']; $changed['footnotes'] = true; echo "#$id {$e->title} [$cls] FOOTNOTE ADD [$old] $new" . PHP_EOL;
        }
    }
    foreach (array_filter($FIX, fn($f) => $f[1] === $id && $f[2] === 'fn') as [$cls, , , $num, $o, $n]) {
        $nFix++; $k = array_keys(array_filter($fns, fn($r) => $r['number'] === $num));
        if ($k && str_contains($fns[$k[0]]['note'], $n)) { $nFixDone++; continue; }
        if (count($k) !== 1 || substr_count($fns[$k[0]]['note'], $o) !== 1 || !str_starts_with($fns[$k[0]]['note'], $o)) { $bad[] = "#$id footnote $num is not exactly as read"; continue; }
        echo "#$id {$e->title} [$cls] FOOTNOTE [$num]" . PHP_EOL . "  OLD: $o" . PHP_EOL . "  NEW: $n" . PHP_EOL;
        $fns[$k[0]]['note'] = str_replace($o, $n, $fns[$k[0]]['note']); $changed['footnotes'] = true;
    }
    if (isset($DATA['search'][(string)$id])) {
        $note = $DATA['pre'] . ' ' . implode(' ', $DATA['search'][(string)$id]);
        $have = array_values(array_filter($rows, fn($r) => $r['heading'] === $HEAD));
        if (!$have) { $rows[] = ['heading' => $HEAD, 'position' => 'bottom', 'note' => $note]; $changed['editorNotes'] = true; echo "#$id {$e->title} SEARCH NOTE ADD: $note" . PHP_EOL; }
        elseif ($have[0]['note'] !== $note) { $bad[] = "#$id already has a different \"$HEAD\" note"; }
    }
    if ($changed) { $plan[$id] = ['body' => isset($changed['body']) ? $body : null, 'editorNotes' => isset($changed['editorNotes']) ? $rows : null, 'footnotes' => isset($changed['footnotes']) ? $fns : null]; }
}

/* 3. removed-claims.json, shown; written only on apply. */
$REGF = "$root/scripts/import/removed-claims.json"; $reg = json_decode((string)file_get_contents($REGF), true); $regChanged = false;
foreach ($DATA['reg'] as $rid => $s) {
    $k = array_keys(array_filter($reg['removedRecords'], fn($x) => ($x['record'] ?? 0) === (int)$rid));
    if (count($k) !== 1) { $bad[] = "removed-claims.json has no single entry for #$rid"; continue; }
    if (($reg['removedRecords'][$k[0]]['search'] ?? '') === $s) { continue; }
    if (isset($reg['removedRecords'][$k[0]]['search'])) { $bad[] = "removed-claims.json #$rid has a different search"; continue; }
    $reg['removedRecords'][$k[0]]['search'] = $s; $regChanged = true; echo "removed-claims.json #$rid {$reg['removedRecords'][$k[0]]['title']}: ADD \"search\": $s" . PHP_EOL;
}
$C = $DATA['connerly'];
if (!array_filter($reg['removedRecords'], fn($x) => ($x['record'] ?? 0) === $C['record'])) {
    if (Entry::find()->section('persons')->status(null)->title('Ward Connerly')->exists()) { $bad[] = 'Ward Connerly is live; the registry entry would fail check_removed_claims.php'; }
    $reg['removedRecords'][] = $C; $regChanged = true; echo 'removed-claims.json ADD ' . json_encode($C, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
}

/* 4. Other notes this week saying something was not found, not covered here. */
$covered = array_merge($ids, [323, 946]); $other = [];
foreach (Entry::find()->status(null)->dateUpdated('>= 2026-09-26')->limit(null)->each(200) as $e) {
    if (in_array($e->id, $covered, true) || !$e->getFieldLayout() || !$e->getFieldLayout()->getFieldByHandle('editorNotes')) { continue; }
    foreach ($rowsOf($e) as $r) { if (preg_match('~(has|have) (not )?been (found|seen)|no source|not found|no record of~i', $r['note']) && !preg_match('~^(Searched|Correction)~', $r['note'])) { $other[] = "#{$e->id} {$e->title} \"{$r['heading']}\": " . mb_substr(strip_tags($r['note']), 0, 160); } }
}
echo PHP_EOL . 'NOT COVERED (other notes this week that say something was not found; listed, not changed): ' . count($other) . PHP_EOL . ($other ? '  ' . implode(PHP_EOL . '  ', $other) . PHP_EOL : '');

$text = json_encode([$FIX, $DATA['search']], JSON_UNESCAPED_UNICODE);
if (preg_match('~\x{2014}|inventory/|\.json~u', $text)) { $bad[] = 'an em dash or a repository path in the public text'; }
echo PHP_EOL . "Corrections: $nFix (" . ($nFix - $nFixDone) . ' to make, ' . $nFixDone . ' already made). Records to save: ' . count($plan) . '. Records with a search note: ' . count($DATA['search']) . '. Registry: ' . ($regChanged ? 'to write' : 'unchanged') . '.' . PHP_EOL;
echo 'Mirror mounted for a live hash check: ' . ($SRC['mirror'] ? 'yes' : 'no (checked against the byte copies only)') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$PROV = '; fix_no_source_notes_2026_10_03.php, 3 Oct 2026: no-source notes re-searched';
$tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $id => $v) {
        $e = $get($id); $L = $e->getFieldLayout();
        foreach (['body', 'editorNotes', 'footnotes'] as $h) { if ($v[$h] !== null) { $e->setFieldValue($h, $v[$h]); } }
        if ($L->getFieldByHandle('recordProvenance') && !str_contains((string)$e->recordProvenance, 'fix_no_source_notes_2026_10_03.php')) {
            $p = trim((string)$e->recordProvenance . $PROV); if (mb_strlen($p) <= 255) { $e->setFieldValue('recordProvenance', $p); }
        }
        if (!$els->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
if ($regChanged) { file_put_contents($REGF, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"); }
$short = [];
foreach ($plan as $id => $v) {
    $e = $get($id);
    if ($v['body'] !== null && (string)$e->body !== $v['body']) { $short[] = "#$id body"; }
    if ($v['editorNotes'] !== null && $rowsOf($e) != $v['editorNotes']) { $short[] = "#$id editorNotes"; }
    if ($v['footnotes'] !== null && count($fnOf($e)) !== count($v['footnotes'])) { $short[] = "#$id footnotes"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK, ' . count($plan) . ' records') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_no_source_notes_2026_10_03.php', count($plan), $short ? 'SHORT' : 'verified', "no-source notes: $nFix corrections; the search recorded on " . count($DATA['search']) . ' records; registry ' . ($regChanged ? 'updated' : 'unchanged'));
if ($short) { throw new \RuntimeException('fix_no_source_notes_2026_10_03: read-back failed'); }
