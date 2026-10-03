/**
 * The 25: batch 1 of 3 (Nathan, 3 October 2026: "import and source the 26 in
 * batches of ten"; the name-by-name survey, inventory/review/by-name-persons-
 * 2026-10-03.md, found 26 of the people with no text who have a legacy page to
 * write from; Tom Frew left the list when his pages proved to be another Tom
 * Frew's, so 25).
 *
 * Batch 1: Connie Worden, George Caravalho, Ruth Newhall, Gary Murr, Dan Hon,
 * Randy Wicks, Henry Clay Wiley, Vincent Gelcich, William Mulholland, John
 * Lang. All are dead; the profiles are full lives. Remi Nadeau waits: record
 * #339 is titled Remi Allen Nadeau but its WordPress post was "Rémi Nadeau
 * (I)", and the legacy site has a page called "Remi Nadeau: Which is Which?".
 *
 * IMPORTED FIRST, so the profiles cite records a reader can follow:
 *   obituaries  Connie Worden-Roberts (SCVHistory.com, 2014); Carl Goldman,
 *               KHTS, August 13, 2014; George A. Caravalho (Stephen K.
 *               Peeples, SCVNews.com, January 6, 2020); Ruth Newhall
 *               (Patricia Farrell Aidem, L.A. Daily News, November 25, 2003);
 *               Gary Murr (Sarah Donner, The Signal, June 25, 2005)
 *   documents   John Lang's biography in Pen Pictures (1889), with Leon
 *               Worden's note; Lang's own letter to the Los Angeles Herald,
 *               July 28, 1875, with Leon's introduction
 *   photographs LW2452 Gelcich, LW2054 Mulholland (1924), SC9020 Caravalho,
 *               each with Leon's text as its caption and its scan as the
 *               portrait
 * Every page and scan was read from the Reggie mirror and matches its
 * manifest; copies are in inventory/legacy/fetched. A record's body is the
 * page's text as Leon published it.
 *
 * Wiley's WordPress body is replaced, as Pico's and Reynolds's were; it stays
 * in withheld history (inventory/wp_content.json and the entry's revisions).
 * Connie Worden was Leon Worden's mother; her profile says so.
 * Differences shown: Ruth Newhall's birthplace and place of death (the Daily
 * News against the 2004 tribute); Caravalho's San Clemente and Bakersfield
 * years, which overlap as the obituary gives them; Lang's bear (the 1889
 * biography against his own letter of 1875) and his wife's name.
 * Fills empty fields only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_profiles_batch3.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$F = "$root/inventory/legacy/fetched"; $GIF = $F; /* the scans were copied here from the mirror; the mirror is not visible inside DDEV */
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$RIGHTS = '~SCVTV|SOLELY RESPONSIBLE|additional restrictions|Personal or Research use|ownership of any original|enable JavaScript|Click to enlarge|Click image|Download original|^SCVHistory\.com~';
/* A page's prose: lines of eight words or more above RETURN TO TOP, without the rights notice, captions-to-click or the title. */
$prose = function ($k, $from = null, $to = null) use ($F, $RIGHTS) {
    $t = explode('[ RETURN TO TOP ]', (string)@file_get_contents("$F/$k.txt"))[0];
    $L = array_values(array_filter(array_map('trim', explode("\n", $t)), fn($l) => str_word_count($l) >= 8 && !preg_match($RIGHTS, $l)));
    if ($from !== null) { $i = null; foreach ($L as $j => $l) { if (str_starts_with($l, $from)) { $i = $j; break; } } $L = $i === null ? [] : array_slice($L, $i); }
    if ($to !== null) { foreach ($L as $j => $l) { if (str_starts_with($l, $to)) { $L = array_slice($L, 0, $j); break; } } }
    return $L;
};
$txt = fn($k) => $ws(@file_get_contents("$F/$k.txt"));
/* Hashes checked against the mirror's manifest on the MacBook (batch3-sha.json); here each file is checked against them. */
$VERIFIED = json_decode((string)@file_get_contents("$F/batch3-sha.json"), true)['files'] ?? [];
$okFile = fn($name) => is_file("$F/$name") && isset($VERIFIED[$name]) && hash_file('sha256', "$F/$name") === $VERIFIED[$name];

/* ================================================================ records to import */
$IMP = [
    'obituary_conniewordenroberts' => ['section' => 'obituaries', 'type' => 'obituary', 'title' => 'Connie Worden-Roberts, Cityhood Pioneer and Road Warrior, 1930-2014', 'subject' => 16418,
        'fields' => ['obitDateOfDeath' => 'August 12, 2014', 'publicationDetails' => 'SCVHistory.com, August 2014'], 'from' => 'Connie Worden-Roberts, a driving force', 'to' => 'Photo by Gary'],
    'khts081314' => ['section' => 'obituaries', 'type' => 'obituary', 'title' => 'Connie Worden Roberts: We\'ve Lost Our Road Warrior', 'subject' => 16418,
        'fields' => ['obitDateOfDeath' => 'August 12, 2014', 'obitDatePublished' => 'August 13, 2014', 'publicationDetails' => 'Carl Goldman, AM-1220 KHTS, Wednesday, August 13, 2014'], 'from' => 'We lost an anchor', 'to' => 'SCV Woman of the Year, with'],
    'obituary_georgeacaravalho' => ['section' => 'obituaries', 'type' => 'obituary', 'title' => 'George A. Caravalho, Santa Clarita\'s First Permanent City Manager, 1938-2020', 'subject' => 16396,
        'fields' => ['obitDateOfDeath' => 'January 5, 2020', 'obitDatePublished' => 'January 6, 2020', 'publicationDetails' => 'Stephen K. Peeples, SCVNews.com, Monday, January 6, 2020'], 'from' => 'George A. Caravalho, Santa Clarita\'s first permanent city manager serving', 'to' => null],
    'dn112503' => ['section' => 'obituaries', 'type' => 'obituary', 'title' => 'Ruth Newhall dies at 93', 'subject' => 15477,
        'fields' => ['obitDateOfDeath' => 'November 24, 2003', 'obitDatePublished' => 'November 25, 2003', 'publicationDetails' => 'Patricia Farrell Aidem, Staff Writer, L.A. Daily News, Tuesday, November 25, 2003'], 'from' => 'Santa Clarita\'s matriarch', 'to' => null],
    'sg062505' => ['section' => 'obituaries', 'type' => 'obituary', 'title' => 'Gary Murr, Saugus School Board President', 'subject' => 25399,
        'fields' => ['obitDateOfDeath' => 'June 2005', 'obitDatePublished' => 'June 25, 2005', 'publicationDetails' => 'Sarah Donner, Signal Staff Writer, The Signal, Saturday, June 25, 2005. ©2005, The Signal; used by permission.'], 'from' => 'uneral services', 'to' => '©2005'],
    'penpictures_johnlang' => ['section' => 'documents', 'type' => 'document', 'title' => 'John Lang: Biography During Life (Pen Pictures, 1889)', 'subject' => 18820,
        'fields' => ['originallyPublishedTitle' => 'Pen Pictures From the Garden of the World: An Illustrated History of Los Angeles County, Calif.', 'originalPublishDate' => '1889', 'originalPublishDateEdtf' => '1889', 'sourceLine' => 'Chicago: The Lewis Publishing Co., 1889, pp. 539-540.'],
        'from' => 'JOHN LANG is a native', 'to' => 'Webmaster\'s note', 'noteFrom' => 'This pay-to-play', 'noteTo' => 'JOHN LANG is a native'],
    'tlp_laherald072875pg3' => ['section' => 'documents', 'type' => 'document', 'title' => 'John Lang\'s Letter on the Grizzly Bear, Los Angeles Herald, July 28, 1875', 'subject' => 18820,
        'fields' => ['originallyPublishedTitle' => 'Los Angeles Herald, Wednesday, July 28, 1875, page 3', 'originalPublishDate' => 'July 28, 1875', 'originalPublishDateEdtf' => '1875-07-28', 'sourceLine' => 'Los Angeles Herald, Wednesday, July 28, 1875, pg 3.'],
        'from' => 'A monster bear has been ravaging', 'to' => null, 'noteFrom' => 'Bear stories are like fish stories', 'noteTo' => 'Below: Text is unaltered'],
    'lw2452' => ['section' => 'photographs', 'type' => 'photograph', 'title' => 'Dr. Vincent Gelcich, Early Pico Oil Field Promoter & Investor', 'subject' => 16439, 'scan' => 'lw2452.jpg',
        'credit' => 'LW2452: 9600 dpi jpeg from book page ("Formative Years in the Far West," White 1962).', 'from' => 'Vincent Gelcich was a medical doctor', 'to' => 'LW2452:'],
    'lw2054' => ['section' => 'photographs', 'type' => 'photograph', 'title' => 'William Mulholland, St. Francis Dam Builder', 'subject' => 16432, 'scan' => 'lw2054.jpg', 'date' => '1924',
        'credit' => 'William Mulholland, 1924. Los Angeles Herald Examiner photograph.', 'from' => 'It can be fairly said', 'to' => 'Aqueduct Memorial Garden'],
    'sc9020' => ['section' => 'photographs', 'type' => 'photograph', 'title' => 'George Caravalho, City Manager 1988-2002', 'subject' => 16396, 'scan' => 'sc9020.jpg',
        'credit' => 'SC9020: 19200 dpi jpeg from original 5x7-inch BW photograph.', 'from' => 'George A. Caravalho (b. Aug. 1, 1938)', 'to' => 'SC9020:'],
];

/* ================================================================ the profiles */
$OBIT = fn($k) => "{OBIT:$k}"; $PHOTO = fn($k) => "{PHOTO:$k}"; $DOC = fn($k) => "{DOC:$k}";
$P = [];
$P[16418] = ['title' => 'Connie Worden', 'alias' => 'Connie Worden-Roberts',
    'fields' => ['birthDate' => 'November 19, 1930', 'birthDateEdtf' => '1930-11-19', 'birthEvidence' => 'retrospective', 'birthplace' => 'Fairmont, Minnesota', 'deathDate' => 'August 12, 2014', 'deathDateEdtf' => '2014-08-12', 'deathEvidence' => 'contemporary', 'occupation' => 'Civic leader; transportation advocate'],
    'must' => ['obituary_conniewordenroberts' => ['co-chaired the first of two efforts in the late 1970s to break the Santa Clarita Valley away from Los Angeles County', 'as vice-chair of the City of Santa Clarita Formation Committee, she handled a lot of the "mechanics" of city formation', 'negotiating with county supervisors and the Local Agency Formation Commission', 'verifying thousands of signatures needed to put cityhood on the November 1987 ballot', 'appointed to Santa Clarita\'s first Planning Commission', 'she organized the nonprofit SCV Transportation Management Association', 'She was named the 1975 SCV Woman of the Year', 'In May 2014, capping more than four decades of service, she was honored with a lifetime achievement award by Santa Clarita officials and presented with a key to the city', 'Connie was born Constance Alice Batterman on Nov. 19, 1930, in Fairmont, Minn.', 'studied English and archaeology at San Jose State College', 'In the fall of 1970, the family moved to the Valencia Hills neighborhood', 'In 1974 she was the first woman elected to the board of the Boys and Girls Club of Santa Clarita Valley, and from December 1974 to December 1979 she served on the William S. Hart Union High School District Governing Board', 'In 1979-80 she led the successful "Dump the Dump" effort to block the siting of a toxic landfill in the Santa Clara River bed in Sand Canyon', 'championing the development of the cross-valley connector', 'Connie is survived by her son, Leon Worden', 'died Tuesday, Aug. 12, 2014', 'She was 83.'],
        'khts081314' => ['Connie Worden-Roberts died at 5:50 p.m. last night', 'Connie was Woman of the Year in 1975', 'Cross Valley Connector'],
        2163 => ['Dan Hon, a local attorney, co-chaired the group with Connie Worden'], 2165 => ['while Connie Worden continued as vice chair']],
    'body' => [
        'Connie Worden-Roberts was one of the founders of the City of Santa Clarita. She co-chaired the first of the two campaigns of the 1970s to make the valley a county of its own, was vice chair of the committee that formed the city in 1987, and was known afterward as the valley\'s "road warrior" for her campaign for the cross-valley connector.[1][2][3]',
        'She was born Constance Alice Batterman in Fairmont, Minnesota, on November 19, 1930. She came to California in 1949, studied English and archaeology at San Jose State College, and in 1970 moved with her family to the Valencia Hills neighborhood of the Santa Clarita Valley.[1] She was the mother of Leon Worden, who built SCVHistory.com.[1]',
        'In 1974 she was the first woman elected to the board of the Boys and Girls Club of the Santa Clarita Valley, and from December 1974 to December 1979 she sat on the William S. Hart Union High School District board.[1] Jerry Reynolds writes that she co-chaired the Canyon County campaign with Dan Hon.[3] In 1979 and 1980 she led the "Dump the Dump" effort that kept a toxic landfill out of the Santa Clara River bed in Sand Canyon.[1]',
        'As vice chair of the City Formation Committee she negotiated with the county supervisors and the Local Agency Formation Commission and verified the signatures that put cityhood on the November 1987 ballot; she was then appointed to the city\'s first Planning Commission.[1][4] She chaired the chamber of commerce\'s transportation committee, organized the SCV Transportation Management Association, and is best remembered for championing the cross-valley connector, the east-west road between Interstate 5 and Highway 14.[1][2]',
        'She was the Santa Clarita Valley\'s Woman of the Year in 1975, and in May 2014 the city gave her a lifetime achievement award and a key to the city.[1][2] She died on August 12, 2014, at 83.[1][2]',
    ],
    'notes' => ['"Connie Worden-Roberts, Cityhood Pioneer and Road Warrior, 1930-2014," obituary #' . '{OBIT:obituary_conniewordenroberts}' . ' in this archive.', 'Carl Goldman, "Connie Worden Roberts: We\'ve Lost Our Road Warrior," AM-1220 KHTS, August 13, 2014, obituary #{OBIT:khts081314} in this archive.', 'Jerry Reynolds, "69. Rebels With a Cause," History of the Santa Clarita Valley, article #2163 in this archive.', 'Jerry Reynolds, "70. Birth of a City," History of the Santa Clarita Valley, article #2165 in this archive.'],
];
$P[16396] = ['title' => 'George Caravalho', 'portrait' => 'sc9020',
    'fields' => ['birthDate' => 'August 1, 1938', 'birthDateEdtf' => '1938-08-01', 'birthEvidence' => 'retrospective', 'birthplace' => 'Kohala, Hawaii', 'deathDate' => 'January 5, 2020', 'deathDateEdtf' => '2020-01-05', 'deathEvidence' => 'contemporary', 'occupation' => 'City manager'],
    'must' => ['obituary_georgeacaravalho' => ['Santa Clarita\'s first permanent city manager serving from 1988 to 2002, died Sunday, January 5, 2020. He was 81.', 'following interim manager Fred Bien, who set up the city\'s initial government structure following incorporation on Dec. 15, 1987', 'The George A. Caravalho Santa Clarita Sports Complex at 20840 Centre Pointe Parkway was dedicated in his honor by the city on December 5, 1998', 'Caravalho worked at the city of Bakersfield as city manager and executive director of its Redevelopment Agency from 1984-1988', 'he was city manager for the city of San Clemente from 1980-1985', 'Caravalho was named city manager for the city of Riverside, and in 2005, he was named director of Dana Point Harbor in Orange County, a post he held until 2007', 'retired from public life in 2008', 'earned his Bachelor\'s degree in Sociology and his Master\'s in Political Science and Government from San Jose State University', '1996 recipient of the International City/County Management Association Mark E. Kean Award for Excellence', 'adjunct professor of leadership and strategic management at California State University, Northridge', 'He will be laid to rest at Santa Cruz Memorial Park', 'On August 1, George Caravalho was born to Joe and Beatrice in Kohala, Hawaii', 'he joined the U.S. Marines in 1956'],
        'sc9020' => ['came to the City of Santa Clarita in 1988 as its first permanent city manager']],
    'body' => [
        'George Caravalho was the City of Santa Clarita\'s first permanent city manager, from 1988 to 2002. He followed the interim manager, Fred Bien, who had set up the new city\'s government after its incorporation on December 15, 1987.[1][2]',
        'He was born on August 1, 1938, in Kohala, Hawaii, and grew up on the Big Island; he joined the Marines in 1956. He took a bachelor\'s degree in sociology and a master\'s in political science at San Jose State University.[1]',
        'Before Santa Clarita he was city manager of San Clemente, from 1980 to 1985, and of Bakersfield, and director of its redevelopment agency, from 1984 to 1988; the obituary gives both spans as they stand, overlapping.[1] After Santa Clarita he was city manager of Riverside and, from 2005 to 2007, director of Dana Point Harbor, and he retired from public life in 2008.[1]',
        'In 1996 he received the International City/County Management Association\'s Mark E. Kean Award for Excellence, and he taught leadership and strategic management at California State University, Northridge.[1][2] The city dedicated the George A. Caravalho Santa Clarita Sports Complex to him on December 5, 1998.[1]',
        'He died on January 5, 2020, at 81; his burial was announced for Santa Cruz Memorial Park.[1]',
    ],
    'notes' => ['Stephen K. Peeples, "George A. Caravalho, Santa Clarita\'s First Permanent City Manager, 1938-2020," SCVNews.com, January 6, 2020, with the eulogies given at his funeral, obituary #{OBIT:obituary_georgeacaravalho} in this archive.', 'His portrait as city manager, SC9020, with Leon Worden\'s note and the City\'s biography, photograph #{PHOTO:sc9020} in this archive.'],
];
$P[15477] = ['title' => 'Ruth Newhall',
    'fields' => ['birthDate' => '1910', 'birthDateEdtf' => '1910', 'birthEvidence' => 'retrospective', 'birthplace' => 'Berkeley, California', 'deathDate' => 'November 24, 2003', 'deathDateEdtf' => '2003-11-24', 'deathEvidence' => 'contemporary', 'occupation' => 'Journalist; newspaper editor; historian'],
    'must' => ['dn112503' => ['she was editor and he was publisher of The Newhall Signal', 'In 1963, the Newhalls bought the Signal, which colleagues say she piloted during stints as editor in 1970-78 and 1985-88', 'Newhall was born in 1910 in Berkeley', 'died Monday in the same San Francisco hospital where she was born 93 years earlier', 'Newhall returned to Berkeley to teach journalism', 'The Newhalls left the Signal in 1988', 'At 78, she and Scott started The Citizen', '"Mimi" gossip column', 'She was a founding member of the Santa Clarita Valley Historical Society and director of the Historical Society of Southern California'],
        'lw3031' => ['Born Ruth Waldo in Berkeley in 1910, she graduated from UC Berkeley in 1931', 'They quit school and eloped to Reno in November 1933', 'Ruth worked for 20 years at the San Francisco Chronicle (1936-1956) as a reporter, editorial writer, and on the City Desk', 'taught journalism at UC and at Mills College', 'In 1958, Ruth authored "The Newhall Ranch," a history of the Newhall Land & Farming Company', '"The Folger Way" (a history of the Folger Coffee Company); "The History of the Spreckels Sugar Company"', 'in 1968 they moved from Berkeley to the Piru Mansion', 'Ruth was one of the founders of the Santa Clarita Valley Historical Society in 1975. In 1980 she served as president of the Society and led the rescue and move of the Saugus Train Station to its present site', 'In 1987, Ruth was named the Santa Clarita Valley\'s "Woman of the Year." In 1992, she completed the voluminous update of her first book by authoring "A California Legend: A History of the Newhall Land and Farming Company." Ruth died in Berkeley on November 24, 2003']],
    'body' => [
        'Ruth Newhall was editor of The Newhall Signal, which she and her husband, Scott Newhall, bought in 1963; a historian of California; and one of the founders of the Santa Clarita Valley Historical Society.[1][2]',
        'Born Ruth Waldo in Berkeley in 1910, she graduated from the University of California, Berkeley, in 1931, and in November 1933 she and Scott Newhall eloped to Reno.[2][1] She worked for twenty years at the San Francisco Chronicle, from 1936 to 1956, as a reporter, an editorial writer and on the city desk, and taught journalism at Berkeley and at Mills College.[2][1]',
        'Her books include "The Newhall Ranch" (1958), a history of the Newhall Land and Farming Company, which she rewrote in 1992 as "A California Legend: A History of the Newhall Land and Farming Company," and histories of the Folger coffee and Spreckels sugar companies.[2]',
        'In 1968 the Newhalls moved to the Piru Mansion. At The Signal she was editor from 1970 to 1978 and from 1985 to 1988, by the Daily News\'s account, and wrote the gossip column "Mimi." The Newhalls left the paper in 1988, and at 78 she and Scott started another, The Citizen.[1][2]',
        'She was a founder of the Historical Society in 1975 and its president in 1980, when it rescued the Saugus Train Station and moved it to its present site; she was a director of the Historical Society of Southern California, and in 1987 the valley\'s Woman of the Year.[2][1]',
        'She died on November 24, 2003, at 93. The tribute of 2004 gives her death in Berkeley; the Daily News says she died in the San Francisco hospital where she was born, though it also gives her birth in Berkeley.[2][1]',
    ],
    'notes' => ['Patricia Farrell Aidem, "Ruth Newhall dies at 93," L.A. Daily News, November 25, 2003, obituary #{OBIT:dn112503} in this archive.', '"A Tribute to Ruth Waldo Newhall," program book, Santa Clarita Valley Historical Society, Saugus Train Station, January 21, 2004, as carried on SCVHistory.com, /scvhistory/lw3031.htm.'],
];
$P[25399] = ['title' => 'Gary Murr',
    'fields' => ['deathDate' => 'June 2005', 'deathDateEdtf' => '2005-06', 'deathEvidence' => 'contemporary', 'occupation' => 'School board president; realtor'],
    'must' => ['sg062505' => ['Saugus Union School District board President Gary Murr, who died suddenly this week. He was 58.', 'He was stirred to champion education after his children began at Emblem Elementary because nearby Plum Canyon School had not yet been built', 'He had been involved in PTA since the oldest was in kindergarten', 'Murr co-chaired the first general obligation bond in the district in 1992 to build additional schools', 'A 28-year veteran of the garment industry, Murr left that work and began his service in the school district, choosing a new career in real estate', 'Murr would regularly attend site council meetings at all the district\'s schools']],
    'body' => [
        'Gary Murr was president of the Saugus Union School District board when he died suddenly in June 2005, at 58.[1]',
        'He came to the board through his children\'s schooling. He was active in the PTA from the time his eldest started kindergarten, and was moved to work for new schools because Plum Canyon School had not yet been built when his children began at Emblem Elementary. He co-chaired the district\'s first general obligation bond, in 1992, to build more schools.[1]',
        'A 28-year veteran of the garment industry, he left it for real estate when he began his service to the district. As a board member he went to site council meetings at every school in the district.[1]',
    ],
    'notes' => ['Sarah Donner, "Gary Murr, Saugus School Board President," The Signal, June 25, 2005, obituary #{OBIT:sg062505} in this archive.'],
];
$P[18616] = ['title' => 'Dan Hon',
    'fields' => ['deathDate' => 'December 1996', 'deathDateEdtf' => '1996-12', 'deathEvidence' => 'contemporary', 'occupation' => 'Attorney; newspaper columnist'],
    'must' => [12480 => ['I lost many friends this year, none closer than Dan Hon', 'going over plans for the formation of the ill-fated "Canyon County."', 'Earlier a frequent writer of letters to the editor, Dan started writing his weekly column in 1988, if memory serves', 'his piece in the Old Town Newhall Gazette two weekends ago would be his last published work', 'Dan cared deeply about downtown Newhall', 'The death of Randy Wicks in August hit him hard'],
        2163 => ['Dan Hon, a local attorney, co-chaired the group with Connie Worden']],
    'body' => [
        'Dan Hon was a Newhall attorney and a columnist for The Signal, and in the 1970s a leader of the campaign to make the Santa Clarita Valley a county of its own.[1][2]',
        'Jerry Reynolds writes that he co-chaired the Canyon County formation committee with Connie Worden. Leon Worden, her son, remembered him at the family\'s dining table in the mid-1970s, going over the plans for the new county.[2][1]',
        'A frequent writer of letters to the editor, he began a weekly column for The Signal about 1988, by Leon Worden\'s memory, and kept it for the rest of his life; he cared deeply about downtown Newhall, and his last published piece appeared in the Old Town Newhall Gazette in December 1996.[1]',
        'He died that December, four months after his friend the cartoonist Randy Wicks.[1]',
    ],
    'notes' => ['Leon Worden, "Dan Hon, Signal columnist," December 22, 1996, article #12480 in this archive.', 'Jerry Reynolds, "69. Rebels With a Cause," History of the Santa Clarita Valley, article #2163 in this archive.'],
];
$P[18663] = ['title' => 'Randy Wicks',
    'fields' => ['deathDate' => 'August 3, 1996', 'deathDateEdtf' => '1996-08-03', 'deathEvidence' => 'retrospective', 'occupation' => 'Editorial cartoonist'],
    'must' => ['lw3030' => ['Randy Wicks arrived in Santa Clarita from his hometown of Belmond, Iowa, in 1976', 'drew him to CalArts, where he graduated in 1980', 'He was originally hired by Scott and Ruth Newhall and served for 16 years as The Signal\'s editorial cartoonist', 'Wicks\' cartoons were also nationally syndicated. He received 19 national and regional awards during his career', 'This Wicks Memorial Wall, the Wicks Book Collection and the Wicks Cartoon Collection, are gifts in his memory to the people of this community and to the Friends of the Libraries of the Santa Clarita Valley'],
        12897 => ['Randy had died that morning -- one year ago today. It was a heart attack. He was just 41.', 'Randy\'s personalized license plate said, "PSN PEN,"', 'especially a Republican -- that pen could sting', 'his cartoons almost always contained some Bactine, too, to take the sting away'],
        12444 => ['we often stood on opposite sides of political fences', 'I always cared what Randy thought about me']],
    'body' => [
        'Randy Wicks was The Signal\'s editorial cartoonist for sixteen years. His cartoons were syndicated nationally and won nineteen national and regional awards.[1]',
        'He came to Santa Clarita from his hometown of Belmond, Iowa, in 1976, graduated from the California Institute of the Arts in 1980, and was hired at The Signal by Scott and Ruth Newhall.[1]',
        'His colleague Tim Whyte remembered that his licence plate read "PSN PEN": his pen could sting a politician, especially a Republican, but his cartoons almost always carried something to take the sting away.[2] Leon Worden, who often stood on the other side of the political fence, wrote that he always cared what Wicks thought of him.[3]',
        'He died of a heart attack on August 3, 1996, at 41.[2] In 1997 a Randy Wicks Memorial Wall was dedicated at the Valencia Library, with a Wicks book collection and the collection of his cartoons, given in his memory to the Friends of the Libraries of the Santa Clarita Valley.[1]',
    ],
    'notes' => ['"Dedication of Randy Wicks Memorial Wall at Valencia Library," program book, 1997, as carried on SCVHistory.com, /scvhistory/lw3030.htm.', 'Tim Whyte, "Randy Wicks: More than just a funny face," August 3, 1997, article #12897 in this archive.', 'Leon Worden, "Politics aside, Randy Wicks\' opinion counted," August 7, 1996, article #12444 in this archive.'],
];
$P[331] = ['title' => 'Henry Clay Wiley', 'replaceBody' => true,
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'contemporary', 'burialEvidence' => 'contemporary'],
    'must' => [867 => ['At 6:15 yesterday morning Henry Clay Wiley, who was in his 69th year, passed away at his home, 309 South Hill street', 'Deceased was born in Lancaster, Pa., in 1829. In 1852 he came to California, settling in San Diego, where he was elected sheriff of the county', 'From 1858 to 1862 he held the position of general manager of the San Fernando mission, and was undersheriff of Los Angeles county under J.F. Burns', 'The deceased was a charter member of the Southern California Pioneer society', 'the interment will be at Rosedale', 'He owned considerable real estate'],
        888 => ['Henry Clay Wiley, founder of Wiley Station at Newhall Pass', 'His father was a merchant tailor', 'at the age of 18 years, he joined the commissary department of the United States army in the campaign against Mexico', 'resided and traveled in all the coast States of Mexico, till 1852, when he arrived at San Diego'],
        20228 => ['Sanford Lyon, Henry Clay Wiley and William Wirt Jenkins, are said to have drilled a well together in Pico Canyon in the late 1860s', 'Wiley\'s own company running tunnels and a well in Wiley Canyon from 1865']],
    'body' => [
        'Henry Clay Wiley was a Los Angeles pioneer and the founder of Wiley Station at the Newhall Pass. He managed Mission San Fernando in the late 1850s and early 1860s, and from 1865 his oil company ran tunnels and a well in Wiley Canyon.[1][2][3]',
        'Born in Lancaster, Pennsylvania, in 1829, he grew up in Indianapolis, where his father was a merchant tailor. At eighteen he joined the commissary department of the United States Army for the war with Mexico, and after it he lived and travelled in Mexico until 1852.[1][2]',
        'He came to San Diego in 1852 and was elected sheriff of San Diego County. In 1858 he moved to Los Angeles, where he was general manager of Mission San Fernando from 1858 to 1862 and undersheriff of Los Angeles County under J.F. Burns.[1][2] He is said to have drilled a well in Pico Canyon with Sanford Lyon and William Wirt Jenkins in the late 1860s; the date is uncertain.[3]',
        'He was a charter member of the Southern California Pioneer Society and owned considerable real estate in Los Angeles.[1] He died at his home on South Hill Street on October 25, 1898, in his sixty-ninth year, and was buried at Rosedale.[1][2]',
    ],
    'notes' => ['"Another Pioneer Passes Away," Los Angeles Herald, October 26, 1898, article #867 in this archive.', '"In Memoriam: Henry Clay Wiley, 1829-1898," Historical Society of Southern California, obituary #888 in this archive.', '"Lyon, Wiley and Jenkins Drill at Pico Canyon," event #20228 in this archive.'],
];
$P[16439] = ['title' => 'Vincent Gelcich', 'portrait' => 'lw2452',
    'fields' => ['birthDate' => '1828', 'birthDateEdtf' => '1828', 'birthEvidence' => 'retrospective', 'birthplace' => 'Splitsko-Dalmatinska, Croatia', 'deathDate' => 'June 5, 1885', 'deathDateEdtf' => '1885-06-05', 'deathEvidence' => 'retrospective', 'burialPlace' => 'Calvary Cemetery, East Los Angeles', 'occupation' => 'Physician; oil promoter'],
    'must' => ['lw2452' => ['whose singular contribution to Santa Clarita Valley history', 'to bring Pico Canyon\'s oil seepages to the attention of L.A. and San Francisco venture capitalists who would subsequently finance the state\'s first successful petroleum operations', 'Born in 1828 in Splitsko-Dalmatinska, Croatia', 'studied medicine in Venice and Trieste', 'he helped Giuseppe Garibaldi lay siege to Rome in 1849', 'Gelcich went to San Francisco', 'opened a medical practice in 1856 and became a U.S. citizen in 1860', 'Gelcich married María Petra Celestina Pico y Bernal', 'in 1865, together with Andrés Pico, E.F. Beale and others, he formed the Los Angeles Asphaltum and Petroleum Mining District, which granted to Andrés the naphtha springs claim in Pico Canyon', 'enlisted June 2, 1864, as an assistant surgeon', 'transferred to the 4th California Infantry as chief surgeon; on Nov. 30, 1865, he mustered out', 'By 1872 he was writing glowing accounts in the L.A. newspapers', 'organized the Los Angeles Petroleum Refining Co.', 'Ground was broken at the Lyon\'s Station stagecoach stop for a refinery, which was completed in April 1874', 'It failed.', 'In 1879, Gelcich sold out to Scofield. Gelcich\'s Santa Clara Oil Co., which he\'d formed in his Moody Gulch days, was one of the companies that merged into Scofield\'s new Pacific Coast Oil Co.', 'Dr. Vincent Gelcich died at his Los Angeles home on June 5, 1885, at age 56', 'Gelcich\'s remains were relocated to New Calvary Cemetery (aka Calvary Cemetery) at 4201 Whittier Blvd. in East Los Angeles'],
        1440 => ['Gelcich shortly bought the claims of Rice and Leaming', 'The Mentry lease plus the claims of Pico, Wiley, Gelcich, Leaming, and Lyon']],
    'body' => [
        'Dr. Vincent Gelcich was a physician who brought the oil seeps of Pico Canyon to the attention of the Los Angeles and San Francisco investors who went on to finance California\'s first successful oil operations.[1]',
        'Born in 1828 in Splitsko-Dalmatinska, in Croatia, he studied medicine in Venice and Trieste, fought with Garibaldi at the siege of Rome in 1849, and then went to San Francisco, where he opened a medical practice in 1856 and became a citizen in 1860.[1]',
        'In 1863 he married Petra Pico, a niece of Andrés Pico. In 1865, with Pico, Edward F. Beale and others, he formed the Los Angeles Asphaltum and Petroleum Mining District, which granted Andrés Pico the naphtha springs claim in Pico Canyon. In the Civil War he served as an assistant surgeon and then chief surgeon of the 4th California Infantry, mustering out in November 1865.[1]',
        'By 1872 he was writing in the Los Angeles papers about the oil district\'s promise, and with investors\' money he organized the Los Angeles Petroleum Refining Company, whose small refinery at Lyon\'s Station, finished in April 1874, failed.[1] He bought the claims of Rice and Leaming, and his claims were among those taken over by Demetrius G. Scofield; in 1879 he sold out to Scofield, and his Santa Clara Oil Company merged into Scofield\'s Pacific Coast Oil Company.[2][1]',
        'He died at his home in Los Angeles on June 5, 1885, at 56, and is buried at Calvary Cemetery in East Los Angeles.[1]',
    ],
    'notes' => ['Leon Worden, "Dr. Vincent Gelcich, Early Pico Oil Field Promoter & Investor," LW2452, photograph #{PHOTO:lw2452} in this archive.', 'A.B. Perkins, "History of Pico Canyon Oil Production," 1958, article #1440 in this archive.'],
];
$P[16432] = ['title' => 'William Mulholland', 'portrait' => 'lw2054',
    'fields' => ['deathDate' => '1935', 'deathDateEdtf' => '1935', 'deathEvidence' => 'retrospective', 'occupation' => 'Chief engineer, Los Angeles water department'],
    'must' => ['lw2054' => ['Chief engineer for the city of Los Angeles Department of Water and Power', 'the Los Angeles Aqueduct', 'Mulholland designed and oversaw construction of the St. Francis Dam, a 600-foot-long, 185-foot-high curved, concrete gravity dam', 'high above Saugus in San Francisquito Canyon', 'The reservoir would meet the needs of Los Angeles for about a year', 'Dam construction started in August 1924; water began to fill the reservoir on March 1, 1926. Two months later the dam was completed', 'three minutes before midnight on March 12, 1928. Half of the dam suddenly collapsed', 'The communities of Piru, Fillmore, Santa Paula, Saticoy and much of Ventura lay in waste by the time the water, mud and debris completed a 54-mile journey to the ocean', 'an estimated 431 people lay dead', 'a hastily prepared government study released five days after the disaster, which attributed the failure to the construction of the west abutment', 'the east abutment was situated on top of an ancient paleo mega-landslide — something Mulholland did not know', 'The disaster that ended the career of the famous engineer was the second-worst in California history'],
        'pollack1107mulholland' => ['Mulholland took full responsibility and was never the same. He died in 1935 a broken man']],
    'body' => [
        'William Mulholland was chief engineer of the Los Angeles water department. He built the Los Angeles Aqueduct, which crosses the Santa Clarita Valley, and the St. Francis Dam in San Francisquito Canyon, whose collapse on March 12, 1928, ended his career and was the second-worst disaster in California\'s history.[1]',
        'The dam, a curved concrete wall 185 feet high and 600 feet long above Saugus, was built from August 1924 to the spring of 1926 to hold about a year\'s water for Los Angeles. At three minutes before midnight on March 12, 1928, half of it collapsed. The flood ran 54 miles down San Francisquito Canyon and the Santa Clara River to the ocean, through Piru, Fillmore, Santa Paula and Ventura, and an estimated 431 people died.[1]',
        'A government study released five days later blamed the dam\'s west abutment; later study found the east abutment stood on an ancient landslide, which Mulholland did not know.[1] He took full responsibility, and died in 1935.[2]',
    ],
    'notes' => ['Leon Worden, "William Mulholland, St. Francis Dam Builder," LW2054, photograph #{PHOTO:lw2054} in this archive.', 'Alan Pollack, "The Rise and Fall of William Mulholland," as carried on SCVHistory.com, /scvhistory/pollack1107mulholland.htm.'],
];
$P[18820] = ['title' => 'John Lang',
    'fields' => ['birthDate' => 'May 5, 1828', 'birthDateEdtf' => '1828-05-05', 'birthEvidence' => 'retrospective', 'birthplace' => 'Herkimer County, New York', 'deathDate' => 'January 20, 1909', 'deathDateEdtf' => '1909-01-20', 'deathEvidence' => 'retrospective', 'occupation' => 'Rancher; hotel keeper'],
    'must' => ['hs7530' => ['the tracks came together on John Lang\'s home­stead in 1876, linking Los Angeles with the rest of the country', 'Lang homesteaded two side-by-side, 160-acre tracts', 'His first land patent was issued February 1, 1882, and his second on July 18, 1898', 'Lang established a stagecoach', 'stop for the freighters', 'In that same year (1884), Lang built a 2-story hotel', 'until Lang sold the ranch in 1889', 'Born May 5, 1828, in Herkimer County, New York. Married in Sacramento to Mary E. Fletcher, May 3, 1862', 'Died January 20, 1909, in Los Angeles County', 'the "Pen Pictures" profile gives her maiden surname as "Floretta."', 'John and Mary Lang had six children'],
        'penpictures_johnlang' => ['JOHN LANG is a native of Herkimer County, New York, born May 5, 1828', 'In 1854 he crossed the plains to California', 'opened and operated two hotels, the Tremont and the American', 'He then located to Martinez, California, and engaged extensively in the dairy business', 'In 1862 he moved to Virginia City, Nevada', 'In 1870 he purchased 160 acres of land, forty miles north of Los Angeles', 'His ranch now comprises 1,200 acres', 'His weight was 2,350 pounds', 'It was the largest grizzly bear ever known', 'on July 7, 1873, Mr. Lang made up his mind'],
        'tlp_laherald072875pg3' => ['The death of the monster took place July 15th', 'So I got two men (F.O. Moore and Wm. Taylor) to go with me', 'He would, if in full fat, have weighed about sixteen hundred pounds', 'penned not 16 years but just two days after he shot the bear on July 15, 1875 (not 1873)']],
    'body' => [
        'John Lang was the Soledad Canyon pioneer whose homestead gave its name to Lang Station, where the Southern Pacific\'s tracks from north and south were joined in 1876, linking Los Angeles to the rest of the country by rail.[1]',
        'Born in Herkimer County, New York, on May 5, 1828, he crossed the plains to California in 1854, kept two hotels in Sacramento, went into the dairy business at Martinez, and in 1862 moved to Virginia City, Nevada, before coming back to Southern California.[2][1]',
        'In 1870 he bought 160 acres in Soledad Canyon, forty miles north of Los Angeles; his two homesteads there were confirmed by patents of 1882 and 1898. He kept a stage stop for the freighters on the road through his land, built a two-story hotel at the Sulphur Springs in 1884, and by 1889 held 1,200 acres; he sold the ranch that year.[1][2]',
        'He is best known for a bear. The biography of 1889 says that on July 7, 1873, he killed a grizzly of 2,350 pounds, "the largest grizzly bear ever known." His own letter to the Los Angeles Herald, written two days after the hunt, tells it otherwise: on July 15, 1875, with F.O. Moore and William Taylor, he killed a bear that had raided the district for years and would have weighed about 1,600 pounds in full fat.[2][3]',
        'He married Mary E. Fletcher in Sacramento on May 3, 1862 (the 1889 biography gives her name as Floretta); they had six children. He died in Los Angeles County on January 20, 1909.[1]',
    ],
    'notes' => ['Leon Worden, "Original Portrait, John Lang Family, ~1889," HS7530, as carried on SCVHistory.com, /scvhistory/hs7530.htm.', '"John Lang," Pen Pictures From the Garden of the World: An Illustrated History of Los Angeles County (1889), with Leon Worden\'s note, document #{DOC:penpictures_johnlang} in this archive.', 'John Lang, letter to the editor, Los Angeles Herald, July 28, 1875, with Leon Worden\'s introduction, document #{DOC:tlp_laherald072875pg3} in this archive.'],
];

/* ================================================================ checks */
$bad = []; $held = [];
foreach (['obituary_conniewordenroberts', 'khts081314', 'obituary_georgeacaravalho', 'dn112503', 'sg062505', 'penpictures_johnlang', 'tlp_laherald072875pg3', 'lw2452', 'lw2054', 'sc9020', 'lw3030', 'lw3031', 'hs7530', 'pollack1107mulholland'] as $k) { if (!$okFile("$k.htm")) { $bad[] = "$k: missing or not as verified against the manifest"; } }
foreach ($IMP as $k => $c) {
    $rec = Entry::find()->section($c['section'])->status(null)->legacyKey($k)->one();
    $held[$k] = $rec?->id;
    $body = $prose($k, $c['from'], $c['to']);
    if (count($body) < 2) { $bad[] = "$k: body not found between \"{$c['from']}\" and \"" . ($c['to'] ?? 'the end') . '"'; }
    if (!empty($c['noteFrom']) && count($prose($k, $c['noteFrom'], $c['noteTo'])) < 1) { $bad[] = "$k: Leon's note not found"; }
    if (!empty($c['scan']) && !$okFile($c['scan'])) { $bad[] = "$k: scan missing or not as verified against the manifest"; }
    if (!empty($c['credit']) && !str_contains($txt($k), $ws($c['credit']))) { $bad[] = "$k: credit line not on the page"; }
    echo str_pad($k, 30) . ($rec ? "held as #{$rec->id}" : "import as {$c['section']}: " . count($body) . ' paragraphs') . PHP_EOL;
}
$read = function ($src) use ($ws, $txt) {
    if (is_string($src)) { return $txt($src); }
    $e = Entry::find()->id($src)->status(null)->one(); return $e ? $ws($e->body) : '';
};
$plan = [];
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$p || $p->title !== $c['title']) { $bad[] = "#$id is not {$c['title']}"; continue; }
    foreach ($c['must'] as $src => $phrases) { $t = $read($src); foreach ($phrases as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "{$c['title']}: " . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
    $body = implode("\n\n", $c['body']); $cur = trim((string)$p->body);
    $done = $cur === trim($body);
    if ($cur !== '' && !$done && empty($c['replaceBody'])) { $bad[] = "{$c['title']}: has a body already"; }
    if (!empty($c['replaceBody']) && $cur !== '' && !$done && ($p->bodyAuthorship->value ?? '') !== 'wordpress-import-unsourced') { $bad[] = "{$c['title']}: the body it replaces is not the WordPress import"; }
    foreach ($c['fields'] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have !== '' && $have !== $v && !in_array($k, ['occupation'], true) && !str_ends_with($k, 'Evidence')) { $bad[] = "{$c['title']}: $k is \"$have\", not \"$v\""; } }
    preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
    if (count($used) !== count($c['notes']) || max($used) > count($c['notes'])) { $bad[] = "{$c['title']}: notes used " . json_encode(array_values($used)) . ' of ' . count($c['notes']); }
    if (preg_match('~\x{2014}~u', $body . implode('', $c['notes']))) { $bad[] = "{$c['title']}: an em dash"; }
    $plan[$id] = ['body' => $body, 'done' => $done];
    echo str_pad($c['title'], 20) . ($done ? 'already written' : str_word_count($body) . ' words, ' . count($c['notes']) . ' notes') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!empty($SHOW)) { foreach ($P as $c) { echo PHP_EOL . "## {$c['title']}" . PHP_EOL; foreach ($c['body'] as $l) { echo "  $l" . PHP_EOL; } } }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ================================================================ writes: the records */
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
$short = []; $ids = [];
foreach ($IMP as $k => $c) {
    if ($held[$k]) { $ids[$k] = $held[$k]; continue; }
    $e = new Entry(); $e->sectionId = $svc->getSectionByHandle($c['section'])->id; $e->setTypeId($svc->getEntryTypeByHandle($c['type'])->id); $e->title = $c['title'];
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    $vals = ['body' => implode("\n\n", $prose($k, $c['from'], $c['to'])), 'legacyKey' => $k, 'legacyUrl' => "/scvhistory/$k.htm", 'sourcePath' => "https://scvhistory.com/scvhistory/$k.htm"] + ($c['fields'] ?? []);
    if ($c['section'] === 'obituaries') { $vals['obitSubject'] = [$c['subject']]; $vals['obitLegacyUrl'] = "/scvhistory/$k.htm"; }
    if ($c['section'] === 'documents') { $vals['subjectPerson'] = [$c['subject']]; $vals['webmasterNoteTop'] = implode("\n\n", $prose($k, $c['noteFrom'], $c['noteTo'])); }
    if ($c['section'] === 'photographs') {
        $tmp = sys_get_temp_dir() . "/{$c['scan']}"; copy("$GIF/{$c['scan']}", $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($c['scan']); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        $a->setFieldValues(['provenanceKind' => 'legacy-mirror', 'legacySourcePath' => "gif/{$c['scan']}", 'acquiredDate' => '2026-10-03', 'source' => "SCVHistory.com, gif/{$c['scan']}, as published on the original site.", 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$GIF/{$c['scan']}")]);
        if (!$elements->saveElement($a)) { $short[] = "$k scan " . json_encode($a->getFirstErrors()); continue; }
        $vals += ['featuredImage' => [$a->id], 'photoSourceCode' => strtoupper($k), 'creditRaw' => $c['credit'], 'photoPeople' => [$c['subject']]];
        if (!empty($c['date'])) { $vals += ['photoDate' => $c['date'], 'photoDateEdtf' => $c['date']]; }
    }
    $e->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($e)) { $short[] = "$k " . json_encode($e->getFirstErrors()); continue; }
    $ids[$k] = $e->id; echo "imported $k as {$c['section']} #{$e->id}" . PHP_EOL;
}
if ($short) { echo 'IMPORT SHORT: ' . implode(', ', $short) . PHP_EOL; throw new \RuntimeException('build_profiles_batch3: imports'); }

/* ================================================================ writes: the profiles */
$n = 0;
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    $notes = array_map(fn($x) => preg_replace_callback('~\{(OBIT|PHOTO|DOC):([\w-]+)\}~', fn($m) => (string)$ids[$m[2]], $x), $c['notes']);
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = [];
    foreach ($c['fields'] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have === '' || ($k === 'occupation' && $have !== $v)) { $vals[$k] = $v; } }
    if (!empty($c['portrait']) && !$p->featuredImage->one()) { $img = Entry::find()->id($ids[$c['portrait']])->one()?->featuredImage->one(); if ($img) { $vals['featuredImage'] = [$img->id]; } }
    if (!empty($c['alias'])) { $al = array_filter(array_map('trim', preg_split('~[;\n]~', (string)$p->personAliases))); if (!in_array($c['alias'], $al, true)) { $vals['personAliases'] = implode("\n", array_merge($al, [$c['alias']])); } }
    if (!$plan[$id]['done']) {
        $vals += ['body' => $plan[$id]['body'], 'footnotes' => $fn($notes), 'bodyAuthorship' => 'editorial-2026'];
        $prov = trim((string)$p->recordProvenance . '; build_profiles_batch3.php, 3 Oct 2026: profile from the legacy pages', '; ');
        if (mb_strlen($prov) <= 255) { $vals['recordProvenance'] = $prov; }
    }
    $vals = array_intersect_key($vals, array_flip($h));
    if ($vals) { $p->setFieldValues($vals); if (!$elements->saveElement($p)) { $short[] = "{$c['title']} " . json_encode($p->getFirstErrors()); continue; } }
    $r = Entry::find()->id($id)->status(null)->one();
    $ok = trim((string)$r->body) === trim($plan[$id]['body']) && ($r->bodyAuthorship->value ?? '') === 'editorial-2026';
    $ok ? $n++ : $short[] = $c['title'];
    echo ($ok ? 'OK    ' : 'SHORT ') . $c['title'] . ' ' . $r->url . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n profiles, " . count(array_filter($held, fn($x) => !$x)) . ' records imported') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_profiles_batch3.php', $n, $short ? 'SHORT' : 'verified', 'the 25, batch 1: Worden, Caravalho, R. Newhall, Murr, Hon, Wicks, Wiley, Gelcich, Mulholland, Lang; their obituaries, documents and portraits imported');
if ($short) { throw new \RuntimeException('build_profiles_batch3: ' . implode(', ', $short)); }
