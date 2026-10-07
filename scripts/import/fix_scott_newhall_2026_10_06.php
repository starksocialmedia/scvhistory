/**
 * Scott Newhall #31431, the second pass (Nathan, 6 October 2026, approving items 1, 2 and 4 to 7 of the review of
 * inventory/review/scott-newhall-draft-2026-10-06.md):
 *   1. "Rewrite the lead around both roles": editor of the San Francisco Chronicle (executive editor from 1952, later
 *      editor, resigned 1971) and publisher of The Signal. "Wording must be shown to Nathan before applying", so the new
 *      body, and the renumbered footnotes that go with it, are behind $WITH_LEAD, off. $APPLY alone does everything else.
 *   2. "Create Edwin White Newhall and Almer Mayo Newhall (CREATE_LINE) and link the descent": Scott childOf Almer, Almer
 *      childOf Edwin, Edwin childOf Henry Mayo Newhall #283, from the oral history pp. 1, 4 and 318, quoted in footnotes; the
 *      p. 318 footnote carries the full passage and a note that its "granddaughter ... grandson" is a generation off.
 *   4. A documents record for the oral history UC8901, legacy URL /scvhistory/uc8901.htm, the PDF attached as other document
 *      PDFs are (documentFiles, as cw9901), Leon Worden's introduction verbatim (webmasterNoteTop, as cw9901 and sg042504),
 *      about Scott (subjectPerson, which lists it on his page), the PDF in his Documents box (recordDocuments). Footnotes
 *      citing the oral history name the record ("document #N", which the footnote partial turns into a link).
 *      TN1968 (gif/tn1968_large.jpg, "Scott Newhall in his office at the San Francisco Chronicle, 1968") as his portrait.
 *      His old-site page /scvhistory/rn1000.htm in personLegacyUrl, the field the person template reads.
 *   5. "Owned" becomes "he and Ruth bought it". The sentence is in the lead, so it moves with $WITH_LEAD.
 *   6. The Davis Bynum disagreement in a bottom editor's note: The Signal of October 31, 1963; the 1994 publishers list; his
 *      own 1968 words (as John Luce reported them); and his own account on p. 441.
 *   7. A minimal organization record "San Francisco Chronicle", shaped like the LAPD (#29794) and Burbank (#29796) records,
 *      in Scott's personOrganizations; role Journalist #18307 in his roles.
 * Under $APPLY alone his footnotes keep their numbers, with three corrections the standing rules require: notes 1 and 4 lose
 * an ellipsis that joined two sentences, note 2 and note 6 name the oral history's record, and note 6 gains the full p. 318
 * passage. The old-numbered set is written only while the old body stands, so a later run never undoes $WITH_LEAD.
 * Sources were read from the Reggie mirror; the PDF, the page and the photograph were copied to inventory/raw/uc8901/ and
 * inventory/incoming/ and are checked here against the mirror's manifest (inventory/raw/scvhistory-manifest-2026-08-20.sha256).
 * Every quotation below was checked against the mirror page or the PDF's text layer.
 * Idempotent: matched by title (persons, the organization), legacyKey uc8901 (the document) and filename (the two files).
 * Dry run by default. Set $APPLY = true; then $WITH_LEAD = true once Nathan has read the lead.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_scott_newhall_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$WITH_LEAD = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($WITH_LEAD ? ' (with the new lead)' : ' (the lead is shown, not written: $WITH_LEAD is off)') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $bad = [];
$SCRIPT = 'fix_scott_newhall_2026_10_06.php'; $PROV = "$SCRIPT, 6 October 2026";

/* ------------------------------------------------------------------ checks */
$expect = [31431 => 'Scott Newhall', 283 => 'Henry Mayo Newhall', 15477 => 'Ruth Newhall', 376 => 'The Santa Clarita Valley Signal', 15691 => 'Newhall Land and Farming Company', 18307 => 'Journalist'];
$E = [];
foreach ($expect as $id => $t) { $E[$id] = Entry::find()->id($id)->status(null)->one(); if ($E[$id]?->title !== $t) { throw new \RuntimeException("#$id is not \"$t\""); } }
if ($E[18307]->section->handle !== 'roles') { throw new \RuntimeException('#18307 is not a role'); }
$S = $E[31431];
$FILES = ['pdf' => ["$root/inventory/raw/uc8901/uc8901.pdf", './scvhistory/files/uc8901/uc8901.pdf'], 'page' => ["$root/inventory/raw/uc8901/uc8901.htm", './scvhistory/uc8901.htm'],
  'photo' => [is_file("$root/inventory/incoming/tn1968_large.jpg") ? "$root/inventory/incoming/tn1968_large.jpg" : "$root/inventory/incoming/done/tn1968_large.jpg", './gif/tn1968_large.jpg']];
$manifest = [];
foreach (file("$root/inventory/raw/scvhistory-manifest-2026-08-20.sha256", FILE_IGNORE_NEW_LINES) as $l) { $p = explode('  ', $l, 2); if (count($p) === 2) { $manifest[$p[1]] = $p[0]; } }
$SHA = [];
foreach ($FILES as $k => [$f, $m]) {
  if (!is_file($f)) { throw new \RuntimeException("missing $f"); }
  $SHA[$k] = hash_file('sha256', $f);
  echo "  $k: " . basename($f) . ', ' . round(filesize($f) / 1e6, 1) . ' MB, ' . (($manifest[$m] ?? '') === $SHA[$k] ? 'matches the mirror manifest' : 'DOES NOT MATCH the manifest') . PHP_EOL;
  if (($manifest[$m] ?? '') !== $SHA[$k]) { $bad[] = "$k does not match the manifest"; }
}

/* Leon Worden's introduction, verbatim from the page: the paragraphs from "Scott Newhall led two lives" to the rule above
   "Download individual pages". Tags out (one link, on "The Citizen lasted less than nine months"), entities decoded. */
$html = file_get_contents($FILES['page'][0]);
$a0 = strpos($html, '<p>Scott Newhall led two lives'); $a1 = strpos($html, '<hr>', $a0);
$INTRO = implode("\n\n", array_values(array_filter(array_map(fn($p) => trim(preg_replace('~\s+~', ' ', html_entity_decode(strip_tags($p), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), preg_split('~<p>~i', substr($html, $a0, $a1 - $a0))))));
$paras = explode("\n\n", $INTRO);
if (count($paras) !== 9 || !str_starts_with($paras[0], 'Scott Newhall led two lives') || !str_ends_with($paras[8], 'there were new stories to be told."')) { $bad[] = 'the introduction did not extract as nine paragraphs'; }

/* ------------------------------------------------------------- the sources */
$M = 'SCVHistory.com';
$OHT = 'Scott Newhall, interviewed by Suzanne B. Riess, "A Newspaper Editor\'s Voyage Across San Francisco Bay: San Francisco Chronicle, 1935-1971, and Other Adventures," Regional Oral History Office, The Bancroft Library, University of California, Berkeley, 1988-1989 (published 1990), document #DOC in this archive (UC8901, /scvhistory/files/uc8901/uc8901.pdf)';
$OH = 'The oral history, document #DOC in this archive (note %s)';
$F = [
  'TN' => $M . ', "Scott Newhall with Sons Skip, Tony, Jon and Stinson Beach, 1944," TN4401, /scvhistory/tn4401.htm (Leon Worden; the same text is on TN1968 and RN1000): "The fourth-generation San Franciscan and great-grandson of merchant-turned-land baron Henry Mayo Newhall"; "Scott was born Jan. 21, 1914, in San Francisco and joined the staff of the Chronicle in 1934 as a summer replacement photographer"; "When Scott Newhall became executive editor of the San Francisco Chronicle in 1952"; "Scott and Ruth purchased it, commuting to Newhall to run it"; "Scott and Ruth rented, then purchased the Warring mansion in Piru in 1968"; "In 1971 he ran for mayor of San Francisco"; "After that, Piru and the Santa Clarita Valley became the Newhalls\' permanent home"; "Scott and Ruth asked to stay on as editors in 1978"; "acute pancreatitis landed Scott at Henry Mayo Newhall Memorial Hospital in Valencia, where he died Oct. 26 of that year. He was 78."',
  'UCI' => $M . ', "Scott Newhall: A Newspaper Editor\'s Voyage (Oral History, 1988-1989)," UC8901, /scvhistory/uc8901.htm (Leon Worden\'s introduction, on document #DOC in this archive): "They\'d purchased the paper in 1963 and stayed on after they sold a controlling interest in 1978 to the Morris Newspaper Corp."; "The minute their 10-year noncompete agreement expired in 1988, they marched down the street (Valencia Boulevard) and launched a rival newspaper, the thrice-weekly Santa Clarita Valley Citizen"; "The Citizen lasted less than nine months (September 11, 1988 - May 3, 1989)".',
  'CB' => 'Elliot Blair Smith, "A Razor\'s Edge: The Life and Times of Scott Newhall, California Publishing\'s Brilliant Barnum," California Business, July 1989, as carried on ' . $M . ', /scvhistory/califbusiness1989july.htm: "For 25 years, as editor and publisher of The Signal, he represented the contrary forces of law and disorder in a land he called Jackass Gulch. Before that, as editor of the San Francisco Chronicle, he buried William Randolph Hearst\'s famous Examiner"; "as his newspaper grew from a small weekly to a 42,000-circulation daily"; "Family members still own 38 percent of the company, including a small stake held by Scott Newhall, a Newhall Land director"; "Then in November 1963, looking for new challenges, he acquired The Signal for $60,000".',
  'SG63' => '"Scott Newhall Buys The Signal," The Newhall Signal and Saugus Enterprise, October 31, 1963, as carried on ' . $M . ', /scvhistory/sg19631031scottnewhall.htm: "Scott Newhall, executive editor of the San Francisco Chronicle and great-grandson of the man for whom the town of Newhall was named, has purchased the Newhall Signal"; "The transaction, several weeks in the making, will become effective November 1"; "He began his career as a photographer on the San Francisco Chronicle in 1935 and prior to becoming executive editor in 1952, served that newspaper as editor of This World Magazine, a Sunday supplement, as Sunday Editor and as Operations Manager."',
  'DN03' => 'Patricia Farrell Aidem, "Ruth Newhall dies at 93," L.A. Daily News, November 25, 2003, obituary #28051 in this archive: "Scott, who died in 1992, was the great-grandson of pioneer Henry Mayo Newhall and a member of the Newhall Land board."',
  'OH' => $OHT . '. Page 1: "The survivor was my grandfather, Edwin Newhall, who was buried the day my younger brother was born"; "My grandfather had gone east to school, I suppose to Yale, and had married Fanny Hall, a young lady from one of those academies for young ladies in New England. She died in childbirth when my father was born." Page 4: "My great-grandfather on my father\'s side, Henry Mayo Newhall, was not what I could describe as a devoutly religious man". Page 318: "He went into the auction business with another great-grandfather of mine, whose name was Almer Ives Hall. Almer Halls\' granddaughter married Henry Mayo Newhall\'s grandson. And my father Almer Newhall was the result of that. And that\'s where I come from. Fanny Hall Newhall died in childbirth when my father was born." On page 318 "granddaughter" and "grandson" are each a generation off: in the same passage he calls Almer Ives Hall his great-grandfather, and on pages 1 and 4 he names his grandfather as Edwin Newhall, who married Fanny Hall, and his great-grandfather as Henry Mayo Newhall. So Fanny Hall was Almer Hall\'s daughter, and Edwin was Henry Mayo Newhall\'s son, as the Cypress Lawn records also give them (LW3325).',
  'LW3325' => $M . ', "Colma Cemeteries: Henry Mayo Newhall & Heirs," LW3325, /scvhistory/lw3325.htm (Leon Worden, 2018; photographs 31 October 2015), "Newhall family members interred at Cypress Lawn": "(4) Scott Newhall 21 Jan 1914 - 26 Oct 1992 Son of Almer Mayo Newhall and Anna Nicholson Scott Newhall Husband of Ruth Waldo Newhall"; "(3) Almer Mayo Newhall 14 May 1881 - 14 Jan 1933 Son Edwin White Newhall and Fannie Silliman Hall Newhall"; "(2) Edwin White Newhall 7 May 1856 - 28 Oct 1915 Son of Henry Mayo Newhall and Sarah Ann White Newhall".',
  'WIRE' => 'Wire photograph caption, April 6, 1936, "Scott & Ruth Newhall Prepare to Sail Around World, 1936," photograph #4583 in this archive (LW2849): "The seafaring pair are Scott Newhall, 23-year-old photographer, and his wife Ruth, 24. He is a son of the late Almer M. Nehwall, San Francisco Chamber of Commerce president." ("Nehwall" as printed.)',
  'LW2915' => $M . ', "HMN Grandson Almer M. Newhall Makes First Long-Distance Call from S.F. to Buenos Aires, 1930," /scvhistory/lw2915.htm: "Almer M. Newhall (1881-1933) was a grandson of town founder Henry Mayo Newhall, father of San Francisco and Santa Clarita Valley newspaperman Scott Newhall".',
  'VF94' => '"Vigilance Forever: Our 75th, The Signal 1919-1994," The Signal\'s anniversary edition of May 1, 1994, p. 4, as carried on ' . $M . ', /scvhistory/files/sg_vigilanceforever1994/files/basic-html/page4.html. Under OWNERS: "Ray Brooks (1963)", "Scott Newhall (1963-1978)", "Charles Morris (1978-present)". Under PUBLISHERS: "Ray Brooks (1963)", "Scott Newhall (1963-1977)", "Tony Newhall (1977-1988)", "Darell Phillips (1988-present)".',
  /* New with the lead. */
  'OBIT' => '"Scott Newhall, 1914-1992," California Monthly, December 1992, reprinted on an unnumbered page (the ninth of the PDF) in the front matter of ' . $OHT . ': "Former San Francisco Chronicle editor Scott Newhall, who spent 20 years turning a struggling daily into a media powerhouse, died October 26"; "He joined the Chronicle as a photographer in 1935, rose to become executive editor in 1952, and later editor, and stepped down in 1971."',
  'OHCHRON' => 'The oral history, document #DOC in this archive. Page 103: "I became Sunday editor, and then Paul got in all this trouble and I became executive editor. Then for nineteen years I was trying to be very helpful in putting out the Chronicle, and I left the Chronicle in 1971." And: "So then when I left the Chronicle we sort of moved south to put out the Signal." Page 266, under the heading "Resignation, 1971", the interviewer: "You resigned in 1971."',
  'LUCE' => 'Karl Kortum, introduction (March 1990) to the oral history, document #DOC in this archive, p. xxii, quoting John Luce, "My Search for Scott Newhall," San Francisco Magazine, July and August 1968, written "at this time when Scott had just bought the Newhall Signal but was still editor of the Chronicle": "\'I am Scott Newhall,\' he told one reporter. \'I am Executive Editor of the San Francisco Chronicle, Publisher of the Newhall Signal and a drag racer--class D, modified.\'"',
  'OH441' => 'The oral history, document #DOC in this archive, p. 441, "The Newhall Boys," an addition he dictated: "You see, I bought the Signal in 1963, shortly before John Kennedy was assassinated"; "Tony took over as publisher of the Signal, and Ruth became managing editor. I wrote the editorials from the early \'70s on. Ruth handled the writing staff and produced the paper. Tony organized the advertising, circulation and printing until we finally all pulled out in mid-1988."',
];
/* The OCR prints "Newhall Signalbut"; the quotation above restores the space, and only that. */

/* ------------------------------------------------------------ the bodies */
$OLD_BODY = (string)$S->getFieldValue('body');
$OLD_ORDER = ['TN', 'UCI', 'CB', 'SG63', 'DN03', 'OH', 'LW3325', 'WIRE', 'LW2915', 'VF94'];
$NEW_TEXT = <<<'TXT'
Scott Newhall was the editor of the San Francisco Chronicle and the publisher of The Signal in Newhall.{OBIT}{CB} He joined the Chronicle as a photographer, in 1934 by one account and 1935 by another, became its Sunday editor, and was its executive editor from 1952 and later its editor, until he resigned in 1971, the year he ran for mayor of San Francisco.{TN}{OBIT}{OHCHRON}{SG63} In 1963, while still at the Chronicle, he and his wife, Ruth Newhall, bought The Newhall Signal and Saugus Enterprise, effective November 1.{SG63}{TN}{UCI} In 1968 he introduced himself to a reporter as "Executive Editor of the San Francisco Chronicle, Publisher of the Newhall Signal."{LUCE} The Signal's own history lists him as its publisher from 1963 to 1977 and its owner until 1978; other accounts differ, as the note at the foot of this page shows.{VF94}

In 1968 he and Ruth moved to the Piru Mansion, and after he left the Chronicle the Santa Clarita Valley became their home.{TN}{OHCHRON} With Ruth he ran the paper until 1988, writing its front-page editorials from the early 1970s, while it grew from a small weekly into a daily.{UCI}{OH441}{CB} They sold a controlling interest to the Morris Newspaper Corp. in 1978 and stayed on as editors; when their ten-year noncompete expired in 1988 they started the Santa Clarita Valley Citizen, which closed on May 3, 1989.{UCI}{TN} He was a director of The Newhall Land and Farming Company.{CB}{DN03}

He was a great-grandson of Henry Mayo Newhall, the town's founder. His father was Almer Mayo Newhall, and his grandfather was Edwin White Newhall, Henry Mayo Newhall's son by his first wife, Sarah Ann White.{OH}{LW3325}{WIRE}{LW2915} He was born in San Francisco on January 21, 1914, and died on October 26, 1992, at Henry Mayo Newhall Memorial Hospital in Valencia.{TN}
TXT;
/* Number the notes in order of first use; the oral history's later notes point back to the note that gives its full title. */
$NEW_ORDER = [];
preg_match_all('~\{([A-Z0-9]+)\}~', $NEW_TEXT, $mm);
foreach ($mm[1] as $k) { if (!in_array($k, $NEW_ORDER, true)) { $NEW_ORDER[] = $k; } }
$NEW_BODY = preg_replace_callback('~\{([A-Z0-9]+)\}~', fn($m) => '[' . (array_search($m[1], $NEW_ORDER, true) + 1) . ']', $NEW_TEXT);
$unused = array_diff(array_keys($F), $NEW_ORDER); if ($unused) { $bad[] = 'notes the new body does not cite: ' . implode(', ', $unused); }
$OBITN = array_search('OBIT', $NEW_ORDER, true) + 1;
$rows = fn(array $order) => array_map(fn($i, $k) => ['number' => (string)($i + 1), 'note' => $F[$k], 'source' => 'editorial-2026'], array_keys($order), $order);
$OLD_ROWS = $rows($OLD_ORDER); $NEW_ROWS = $rows($NEW_ORDER);
$CURRENT_ROWS = array_values(array_filter(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], $S->getFieldValue('footnotes') ?? []), fn($r) => trim($r['note']) !== ''));

/* ------------------------------------------------------------ the notes */
$BYNUM = 'The sources do not agree on who was The Signal\'s publisher after Scott Newhall bought it. The Signal of October 31, 1963, reporting the purchase, says: "Newhall immediately named Davis Bynum, a member of his Chronicle staff, to become the Signal\'s publisher. Bynum is a former staff writer and currently assistant promotion director of The Chronicle. He will assume his new office here November 15." (/scvhistory/sg19631031scottnewhall.htm). The Signal\'s 75th anniversary edition, "Vigilance Forever," May 1, 1994, p. 4, lists among its publishers "Scott Newhall (1963-1977)" and "Tony Newhall (1977-1988)", and does not name Davis Bynum. In 1968, by John Luce\'s account in San Francisco Magazine, Scott Newhall told a reporter: "I am Executive Editor of the San Francisco Chronicle, Publisher of the Newhall Signal" (quoted in Karl Kortum\'s introduction to the oral history, document #DOC in this archive, p. xxii). In the oral history itself, p. 441, he says that "early in the game my son Jon came down to Newhall and at first sold ads, but then became editor and also functioned as publisher," and that later "Tony took over as publisher of the Signal." This record follows the 1994 list and leaves the question open.';
$BYNUM_HEAD = 'Who was publisher of The Signal';

/* -------------------------------------------- the San Francisco Chronicle */
$CHR_BODY = 'The San Francisco Chronicle is a daily newspaper in San Francisco. Scott Newhall, later the publisher of The Signal in Newhall, joined it as a photographer in the 1930s and was its executive editor from 1952 and later its editor, until 1971.[1][2]';
$CHR_FN = [
  $M . ', "Scott Newhall, San Francisco Chronicle, 1968," TN1968, /scvhistory/tn1968.htm (Leon Worden): "Scott Newhall in his office at the San Francisco Chronicle, 1968"; "When Scott Newhall became executive editor of the San Francisco Chronicle in 1952"; "Scott was born Jan. 21, 1914, in San Francisco and joined the staff of the Chronicle in 1934 as a summer replacement photographer".',
  $F['OBIT'],
];

/* ------------------------------------------------------- Edwin and Almer */
$LW3325H = $M . ', "Colma Cemeteries: Henry Mayo Newhall & Heirs," LW3325, /scvhistory/lw3325.htm (Leon Worden, 2018; photographs 31 October 2015), "Newhall family members interred at Cypress Lawn": ';
$LINE = [
  'Edwin White Newhall' => ['slug' => 'edwin-white-newhall', 'b' => ['May 7, 1856', '1856-05-07'], 'd' => ['October 28, 1915', '1915-10-28'], 'occ' => 'Incorporator, The Newhall Land and Farming Company',
    'body' => 'Edwin White Newhall was a son of Henry Mayo Newhall and his first wife, Sarah Ann White.[1][2] With his brothers he incorporated The Newhall Land and Farming Company on June 1, 1883, the year after their father\'s death.[3] His first wife, Fannie Silliman Hall, died after the birth of their son, Almer Mayo Newhall; he then married Virginia Whiting.[1][2][4] He died on October 28, 1915.[1]',
    'fn' => [$LW3325H . '"(2) Edwin White Newhall 7 May 1856 - 28 Oct 1915 Son of Henry Mayo Newhall and Sarah Ann White Newhall Husband of (1) Fannie Silliman Hall Newhall and (2) Virginia Whiting Newhall"; "(3) Almer Mayo Newhall 14 May 1881 - 14 Jan 1933 Son Edwin White Newhall and Fannie Silliman Hall Newhall".',
      $M . ', "Stock Certificate: California Bank (of Los Angeles), Signed by H.G. Newhall," LW3564, /scvhistory/lw3564.htm: "Almer Hall\'s daughter, Fannie Silliman Hall, married Henry Newhall\'s third son, Edwin White Newhall"; "The latter marriage ended in tragedy when Fannie died of puerperal fever, aka childbed fever, three days after giving birth to their only child together".',
      $M . ', "Henry Mayo Newhall\'s 5 Sons," RN7301, /scvhistory/rn7301.htm: "Henry Mayo Newhall\'s five sons, photographed in 1873"; "All were at least 21 years old when they incorporated The Newhall Land and Farming Co. in 1883"; and Henry Mayo Newhall #283: "On June 1, 1883, H.M. Newhall\'s five sons incorporated the family-owned Newhall Land and Farming Company."',
      $OHT . '. These are the words of his grandson, Scott Newhall. Page 1: "The survivor was my grandfather, Edwin Newhall, who was buried the day my younger brother was born"; "My grandfather had gone east to school, I suppose to Yale, and had married Fanny Hall, a young lady from one of those academies for young ladies in New England. She died in childbirth when my father was born. So my grandfather married her best friend, and schoolmate, who was Virginia Whiting Newhall, who came from Martha\'s Vineyard".'],
    'parent' => 283],
  'Almer Mayo Newhall' => ['slug' => 'almer-mayo-newhall', 'b' => ['May 14, 1881', '1881-05-14'], 'd' => ['January 14, 1933', '1933-01-14'], 'occ' => 'Businessman; officer, The Newhall Land and Farming Company',
    'body' => 'Almer Mayo Newhall, a grandson of Henry Mayo Newhall, was the son of Edwin White Newhall and Fannie Silliman Hall, who died three days after his birth.[1][2][4] At the time of the St. Francis Dam disaster in 1928 he was assistant to the president of The Newhall Land and Farming Company, his cousin George A. Newhall Jr., and in 1930, as president of the San Francisco Chamber of Commerce, he placed the first long-distance call from San Francisco to Buenos Aires.[3] He married Anna Nicholson Scott; their sons included Scott Newhall, later publisher of The Signal.[1][3][4] He died on January 14, 1933.[1]',
    'fn' => [$LW3325H . '"(3) Almer Mayo Newhall 14 May 1881 - 14 Jan 1933 Son Edwin White Newhall and Fannie Silliman Hall Newhall Husband of Anna Nicholson Scott Newhall"; "(4) Scott Newhall 21 Jan 1914 - 26 Oct 1992 Son of Almer Mayo Newhall and Anna Nicholson Scott Newhall".',
      'LW3564 (note 2 on Edwin White Newhall\'s record), /scvhistory/lw3564.htm: "Fannie died of puerperal fever, aka childbed fever, three days after giving birth to their only child together"; "Almer Mayo was the father of 20th-century Signal newspaper editor Scott Newhall."',
      $F['LW2915'] . ' And: "Two years earlier, at the time of the 1928 St. Francis Dam Disaster, Almer was assistant to the president of The Newhall Land and Farming Co."; the president was "his cousin George A. Newhall Jr. (1904-1958)"; "Almer M. Newhall, president of the San Francisco Chamber of Commerce, places the first long-distance phone call from San Francisco to Buenos Aires, Argentina, April 1930."',
      $OHT . '. These are the words of his son, Scott Newhall. Page 1: "My grandfather had gone east to school, I suppose to Yale, and had married Fanny Hall, a young lady from one of those academies for young ladies in New England. She died in childbirth when my father was born." Page 318: "And my father Almer Newhall was the result of that. And that\'s where I come from. Fanny Hall Newhall died in childbirth when my father was born."'],
    'parent' => 'Edwin White Newhall'],
];

/* ------------------------------------------------- text checks, all of ours */
$ours = array_merge(array_values($F), [$NEW_BODY, $BYNUM, $CHR_BODY], $CHR_FN, array_merge(...array_map(fn($d) => array_merge([$d['body']], $d['fn']), array_values($LINE))));
foreach ($ours as $t) {
  if (preg_match('~\x{2014}~u', $t)) { $bad[] = 'an em dash: ' . mb_substr($t, 0, 80); }
  if (preg_match('~\.\.\.|\x{2026}~u', $t)) { $bad[] = 'an ellipsis: ' . mb_substr($t, 0, 80); }
  if (preg_match('~legacy mirror|sha256~i', $t)) { $bad[] = 'process words: ' . mb_substr($t, 0, 80); }
}
if (preg_match('~\bowned\b~', $NEW_BODY)) { $bad[] = '"owned" is still in the lead'; }

/* ======================================================= the plan, printed */
$show = fn(string $t) => str_replace('#DOC', 'document-to-be-created', $t);
echo PHP_EOL . '== 1. The new lead (written only with $WITH_LEAD; "owned" becomes "he and Ruth bought"):' . PHP_EOL . PHP_EOL . $NEW_BODY . PHP_EOL . PHP_EOL;
echo '   Its footnotes, renumbered (' . count($NEW_ROWS) . '):' . PHP_EOL;
foreach ($NEW_ROWS as $r) { echo "   [{$r['number']}] " . $show($r['note']) . PHP_EOL; }
$bodyNow = $OLD_BODY === $NEW_BODY ? 'new' : 'old';
echo PHP_EOL . "   the body now: $bodyNow" . PHP_EOL;

echo PHP_EOL . '== Footnotes under $APPLY alone, old numbering kept (written only while the old body stands): changes from what is stored:' . PHP_EOL;
foreach ($OLD_ROWS as $i => $r) { $was = $CURRENT_ROWS[$i]['note'] ?? ''; if ($was !== $r['note']) { echo "   [{$r['number']}] now: " . $show($r['note']) . PHP_EOL; } }

/* ------------------------------------------- 7. the San Francisco Chronicle */
$chr = Entry::find()->section('organizations')->status(null)->title('San Francisco Chronicle')->one();
echo PHP_EOL . '== 7. Organization "San Francisco Chronicle": ' . ($chr ? "exists #{$chr->id}" : 'create (orgType media, civicRole none)') . PHP_EOL . '   ' . $CHR_BODY . PHP_EOL;
foreach ($CHR_FN as $i => $t) { echo '   [' . ($i + 1) . '] ' . $show($t) . PHP_EOL; }

/* -------------------------------------------- 4. the document and its PDF */
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$doc = Entry::find()->section('documents')->status(null)->legacyKey('uc8901')->one();
$byUrl = Entry::find()->status(null)->legacyUrl('/scvhistory/uc8901.htm')->one();
if (!$doc && $byUrl) { $bad[] = "/scvhistory/uc8901.htm is already held as #{$byUrl->id} ({$byUrl->section->handle})"; }
$pdf = Asset::find()->volumeId($vol->id)->filename('uc8901.pdf')->one();
$DOC_TITLE = 'Scott Newhall: A Newspaper Editor\'s Voyage (Oral History, 1988-1989)';
$DOC = [
  'webmasterNoteTop' => $INTRO,
  'originallyPublishedTitle' => 'A Newspaper Editor\'s Voyage Across San Francisco Bay: San Francisco Chronicle, 1935-1971, and Other Adventures',
  'originalPublishDate' => '1990', 'originalPublishDateEdtf' => '1990',
  'sourceLine' => 'Scott Newhall, "A Newspaper Editor\'s Voyage Across San Francisco Bay: San Francisco Chronicle, 1935-1971, and Other Adventures," an oral history conducted in 1988-1989 by Suzanne B. Riess, Regional Oral History Office, The Bancroft Library, University of California, Berkeley, 1990. With an introduction by Karl Kortum.',
  'webmasterNoteBottom' => 'UC8901. The original page also linked a page-turning view of the volume (/scvhistory/files/uc8901/uc8901.html) and its pages one by one (/scvhistory/files/uc8901/index.html); the PDF attached here is the one it linked as "Open original .pdf". Besides the interviews, the volume holds an obituary from California Monthly, December 1992; Karl Kortum\'s introduction of March 1990; and appendices that include John Luce\'s "My Search for Scott Newhall," San Francisco Magazine, July and August 1968, and the California Business article of July 1989.',
  'subjectPerson' => [31431], 'legacyKey' => 'uc8901', 'legacyUrl' => '/scvhistory/uc8901.htm', 'sourcePath' => 'https://scvhistory.com/scvhistory/uc8901.htm',
];
echo PHP_EOL . '== 4. Document "' . $DOC_TITLE . '": ' . ($doc ? "exists #{$doc->id}" : 'create, published') . PHP_EOL;
foreach ($DOC as $h => $v) { if ($h !== 'webmasterNoteTop') { echo "   $h: " . (is_array($v) ? implode(',', $v) : $v) . PHP_EOL; } }
echo '   webmasterNoteTop (Leon Worden\'s introduction, verbatim, ' . count($paras) . ' paragraphs):' . PHP_EOL . '   | ' . str_replace("\n\n", "\n   | ", $INTRO) . PHP_EOL;
echo '   documentFiles: uc8901.pdf ' . ($pdf ? "exists #{$pdf->id}" : 'import to archiveMedia/legacy/') . PHP_EOL;
echo '   writtenBy and publishedBy left empty: the archive holds no record for Suzanne B. Riess or the Bancroft Library.' . PHP_EOL;

/* ------------------------------------------------------- 4. the portrait */
$ph = Asset::find()->volumeId($vol->id)->filename('tn1968_large.jpg')->one();
$PH = ['provenanceKind' => 'legacy-mirror', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established.', 'legacySourcePath' => 'gif/tn1968_large.jpg',
  'source' => 'SCVHistory.com, gif/tn1968_large.jpg, the enlargement of the photograph on /scvhistory/tn1968.htm, "Scott Newhall, San Francisco Chronicle, 1968". Taken from the original site\'s files on 6 October 2026.',
  'photoCaptionExt' => 'Scott Newhall in his office at the San Francisco Chronicle, 1968.', 'photoSourceCode' => 'TN1968', 'dateAsPrinted' => '1968', 'dateEdtf' => '1968'];
echo PHP_EOL . '== 4. Portrait tn1968_large.jpg (1600 x 1990): ' . ($ph ? "exists #{$ph->id}" : 'import to archiveMedia/legacy/') . '; title "Scott Newhall, 1968"' . PHP_EOL;
foreach ($PH as $h => $v) { echo "   $h: $v" . PHP_EOL; }
$PDF = ['legacySourcePath' => '/scvhistory/files/uc8901/uc8901.pdf', 'photoSourceCode' => 'UC8901', 'provenanceKind' => 'legacy-mirror', 'acquiredDate' => '2026-10-06', 'license' => 'unknown',
  'rightsNote' => 'Copyright 1990 by The Regents of the University of California, as printed in the volume. No permission to republish is established.',
  'source' => 'SCVHistory.com, /scvhistory/files/uc8901/uc8901.pdf, linked as "Open original .pdf" from /scvhistory/uc8901.htm. Taken from the original site\'s files on 6 October 2026.'];

/* Field limits, from the asset layout. */
$probe = $ph ?? $pdf ?? Asset::find()->volumeId($vol->id)->kind('image')->one();
foreach ($probe->getFieldLayout()->getCustomFields() as $f) {
  if (!$f instanceof \craft\fields\PlainText || !$f->charLimit) { continue; }
  foreach (['portrait' => $PH, 'pdf' => $PDF] as $k => $vals) { if (isset($vals[$f->handle]) && mb_strlen($vals[$f->handle]) > $f->charLimit) { $bad[] = "$k {$f->handle} is " . mb_strlen($vals[$f->handle]) . " chars, limit {$f->charLimit}"; } }
}
if (mb_strlen($PROV) > 255) { $bad[] = 'recordProvenance over 255'; }

/* ------------------------------------------------ 2. Edwin, Almer, childOf */
echo PHP_EOL . '== 2. The line to Henry Mayo Newhall #283:' . PHP_EOL;
$made = [283 => $E[283], 'Scott Newhall' => $S];
foreach ($LINE as $t => $d) {
  $x = Entry::find()->section('persons')->status(null)->title($t)->one(); $made[$t] = $x;
  echo "   $t ({$d['b'][1]} to {$d['d'][1]}): " . ($x ? "exists #{$x->id}" : 'create') . PHP_EOL . '     ' . $d['body'] . PHP_EOL;
  foreach ($d['fn'] as $i => $fn) { echo '     [' . ($i + 1) . '] ' . $show($fn) . PHP_EOL; }
}
$KIN = [['Scott Newhall', 'Almer Mayo Newhall', 'oral history p. 318; LW3325; 1936 wire caption; LW2915'], ['Almer Mayo Newhall', 'Edwin White Newhall', 'oral history p. 1; LW3325; LW3564'], ['Edwin White Newhall', 283, 'LW3325; LW3564; RN7301']];
foreach ($KIN as [$c, $p, $src]) {
  $ce = $made[$c] ?? null; $pe = $made[$p] ?? null;
  $has = $ce && $pe && in_array($pe->id, $ce->getFieldValue('childOf')->status(null)->ids(), true);
  echo "   childOf $c -> " . ($pe?->title ?? $p) . "   [$src; all dead]  " . ($has ? 'present' : 'add') . PHP_EOL;
}

/* ------------------------------------------------------- Scott's fields */
$h = array_map(fn($f) => $f->handle, $S->getFieldLayout()->getCustomFields());
foreach (['roles', 'personOrganizations', 'personLegacyUrl', 'featuredImage', 'recordDocuments', 'editorNotes', 'footnotes', 'body', 'childOf'] as $need) { if (!in_array($need, $h, true)) { $bad[] = "Scott's layout has no $need"; } }
$orgs = $S->getFieldValue('personOrganizations')->status(null)->ids(); $roles = $S->getFieldValue('roles')->status(null)->ids();
$notes = array_values(array_filter(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position']], $S->getFieldValue('editorNotes') ?? []), fn($r) => trim($r['note']) !== ''));
$hasBynum = (bool)array_filter($notes, fn($r) => $r['heading'] === $BYNUM_HEAD);
echo PHP_EOL . '== Scott Newhall #31431:' . PHP_EOL;
echo '   personOrganizations: ' . ($chr && in_array($chr->id, $orgs, true) ? 'San Francisco Chronicle present' : 'add San Francisco Chronicle') . PHP_EOL;
echo '   roles: ' . (in_array(18307, $roles, true) ? 'Journalist present' : 'add Journalist #18307') . PHP_EOL;
$plu = (string)$S->getFieldValue('personLegacyUrl'); echo '   personLegacyUrl: ' . ($plu === '' ? 'set /scvhistory/rn1000.htm' : "has \"$plu\", left") . PHP_EOL;
$fi = $S->getFieldValue('featuredImage')->one(); echo '   featuredImage: ' . ($fi ? "has #{$fi->id}, left" : 'set tn1968_large.jpg') . PHP_EOL;
$rd = $S->getFieldValue('recordDocuments')->ids(); echo '   recordDocuments: ' . ($pdf && in_array($pdf->id, $rd, true) ? 'uc8901.pdf present' : 'add uc8901.pdf') . PHP_EOL;
echo '   editorNotes: ' . ($hasBynum ? 'the publisher note present' : 'add, at the bottom, after "Not to be confused with"') . PHP_EOL . "     [$BYNUM_HEAD] " . $show($BYNUM) . PHP_EOL;

echo PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo 'nothing written' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING to write' . PHP_EOL; return; }

/* ============================================================ the writes */
$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
$importAsset = function (string $file, string $name, string $title, string $alt, array $vals) use ($vol, $folder, $el) {
  $tmp = sys_get_temp_dir() . '/' . $name; copy($file, $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($name); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException("$name " . json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = $title; if ($alt !== '') { $a->alt = $alt; }
  $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($vals, array_flip($ah)));
  if (!$el->saveElement($a)) { throw new \RuntimeException("$name fields " . json_encode($a->getFirstErrors())); }
  return $a;
};

/* The PDF and the document first: every note that cites the oral history names it. */
if (!$pdf) { $pdf = $importAsset($FILES['pdf'][0], 'uc8901.pdf', 'Scott Newhall, oral history, 1988-1989 (PDF)', '', $PDF + ['sourceChecksum' => 'sha256:' . $SHA['pdf']]); $n++; echo "imported uc8901.pdf #{$pdf->id}" . PHP_EOL; }
if (!$doc) {
  $sec = Craft::$app->getEntries()->getSectionByHandle('documents');
  $doc = new Entry(); $doc->sectionId = $sec->id; $doc->setTypeId($sec->getEntryTypes()[0]->id); $doc->title = $DOC_TITLE; $doc->enabled = true;
  $lay = array_map(fn($f) => $f->handle, $doc->getFieldLayout()->getCustomFields());
  $missing = array_diff(array_merge(array_keys($DOC), ['documentFiles']), $lay); if ($missing) { throw new \RuntimeException('not on the document layout: ' . implode(', ', $missing)); }
  $doc->setFieldValues($DOC + ['documentFiles' => [$pdf->id]]);
  if (!$el->saveElement($doc)) { throw new \RuntimeException('uc8901 ' . json_encode($doc->getFirstErrors())); } $n++;
  echo "created document #{$doc->id}" . PHP_EOL;
}
$D = '#' . $doc->id; $fix = fn(string $t) => str_replace('#DOC', $D, $t);

if (!$chr) {
  $os = Craft::$app->getEntries()->getSectionByHandle('organizations');
  $chr = new Entry(); $chr->sectionId = $os->id; $chr->setTypeId($os->getEntryTypes()[0]->id); $chr->title = 'San Francisco Chronicle';
  $oh = array_map(fn($f) => $f->handle, $chr->getFieldLayout()->getCustomFields());
  $chr->setFieldValues(array_intersect_key(['orgType' => 'media', 'civicRole' => 'none', 'body' => $CHR_BODY,
    'footnotes' => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $fix($x), 'source' => 'editorial-2026'], array_keys($CHR_FN), $CHR_FN),
    'recordProvenance' => "$PROV: the newspaper Scott Newhall edited"], array_flip($oh)));
  if (!$el->saveElement($chr)) { throw new \RuntimeException('Chronicle ' . json_encode($chr->getFirstErrors())); } $n++;
  echo "created San Francisco Chronicle #{$chr->id}" . PHP_EOL;
}
if (!$ph) { $ph = $importAsset($FILES['photo'][0], 'tn1968_large.jpg', 'Scott Newhall, 1968', 'Scott Newhall in his office at the San Francisco Chronicle', $PH + ['sourceChecksum' => 'sha256:' . $SHA['photo']]); $n++; echo "imported tn1968_large.jpg #{$ph->id}" . PHP_EOL; }

$psec = Craft::$app->getEntries()->getSectionByHandle('persons');
foreach ($LINE as $t => $d) {
  if ($made[$t]) { continue; }
  $x = new Entry(); $x->sectionId = $psec->id; $x->setTypeId($psec->getEntryTypes()[0]->id); $x->slug = $d['slug'];
  $x->setFieldValues(['fullName' => $t, 'birthDate' => $d['b'][0], 'birthDateEdtf' => $d['b'][1], 'birthEvidence' => 'retrospective', 'deathDate' => $d['d'][0], 'deathDateEdtf' => $d['d'][1], 'deathEvidence' => 'retrospective',
    'burialPlace' => 'Cypress Lawn Memorial Park, Colma, California', 'burialEvidence' => 'retrospective', 'occupation' => $d['occ'], 'body' => $d['body'], 'bodyAuthorship' => 'editorial-2026',
    'footnotes' => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $fix($x), 'source' => 'editorial-2026'], array_keys($d['fn']), $d['fn']), 'recordProvenance' => $PROV]);
  if (!$el->saveElement($x)) { throw new \RuntimeException("$t " . json_encode($x->getFirstErrors())); } $n++;
  $made[$t] = $x; echo "created $t #{$x->id}" . PHP_EOL;
}
foreach ($KIN as [$c, $p]) {
  $ce = Entry::find()->id($made[$c]->id)->status(null)->one(); $pe = $made[$p];
  $ids = $ce->getFieldValue('childOf')->status(null)->ids();
  if (in_array($pe->id, $ids, true)) { continue; }
  $ce->setFieldValue('childOf', array_merge($ids, [$pe->id])); if (!$el->saveElement($ce)) { throw new \RuntimeException("childOf $c"); } $n++;
  echo "childOf $c -> {$pe->title}" . PHP_EOL;
}

/* Scott, last, from a fresh copy (the childOf save above touched him). */
$S = Entry::find()->id(31431)->status(null)->one(); $set = [];
$orgs = $S->getFieldValue('personOrganizations')->status(null)->ids(); if (!in_array($chr->id, $orgs, true)) { $set['personOrganizations'] = array_merge($orgs, [$chr->id]); }
$roles = $S->getFieldValue('roles')->status(null)->ids(); if (!in_array(18307, $roles, true)) { $set['roles'] = array_merge($roles, [18307]); }
if ((string)$S->getFieldValue('personLegacyUrl') === '') { $set['personLegacyUrl'] = '/scvhistory/rn1000.htm'; }
if (!$S->getFieldValue('featuredImage')->one()) { $set['featuredImage'] = [$ph->id]; }
$rd = $S->getFieldValue('recordDocuments')->ids(); if (!in_array($pdf->id, $rd, true)) { $set['recordDocuments'] = array_merge($rd, [$pdf->id]); }
if (!$hasBynum) { $set['editorNotes'] = array_merge($notes, [['heading' => $BYNUM_HEAD, 'note' => $fix($BYNUM), 'position' => 'bottom']]); }
$body = (string)$S->getFieldValue('body');
$wantRows = fn(array $rs) => array_map(fn($r) => ['number' => $r['number'], 'note' => $fix($r['note']), 'source' => $r['source']], $rs);
if ($WITH_LEAD) {
  if ($body !== $OLD_BODY && $body !== $NEW_BODY) { throw new \RuntimeException('the body has changed since the dry run; refusing to replace it'); }
  if ($body !== $NEW_BODY) { $set['body'] = $NEW_BODY; }
  $target = $wantRows($NEW_ROWS);
} else {
  $target = $body === $NEW_BODY ? $wantRows($NEW_ROWS) : $wantRows($OLD_ROWS);
}
if ($CURRENT_ROWS != $target) { $set['footnotes'] = $target; }
if ($set) {
  $S->setFieldValues($set); if (!$el->saveElement($S)) { throw new \RuntimeException('#31431 ' . json_encode($S->getFirstErrors())); } $n++;
  echo 'Scott Newhall: set ' . implode(', ', array_keys($set)) . PHP_EOL;
}

/* Read-back. */
$r = Entry::find()->id(31431)->status(null)->one();
$ok = $r->getFieldValue('featuredImage')->one()?->id === $ph->id && in_array($chr->id, $r->getFieldValue('personOrganizations')->status(null)->ids(), true)
  && in_array($made['Almer Mayo Newhall']->id, $r->getFieldValue('childOf')->status(null)->ids(), true) && (!$WITH_LEAD || (string)$r->getFieldValue('body') === $NEW_BODY)
  && !str_contains(json_encode($r->getFieldValue('footnotes')), '#DOC') && in_array(31431, Entry::find()->id($doc->id)->status(null)->one()->getFieldValue('subjectPerson')->status(null)->ids(), true);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog($SCRIPT, $n, $ok ? 'verified' : 'SHORT', "Scott Newhall: oral history document #{$doc->id}, Chronicle #{$chr->id}, portrait TN1968, Edwin and Almer, childOf line, publisher note" . ($WITH_LEAD ? ', the new lead' : ''));
echo "done: $n" . PHP_EOL;
