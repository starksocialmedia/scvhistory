/**
 * Nine council members from their legacy council pages, the way Kellar was
 * done (Nathan, 3 October 2026: "Yes, do all nine as one batch, the way you did
 * Kellar. Pederson and Koontz especially, since their portraits are already in
 * the archive and were never attached").
 *
 *   Boyer SC9010, Darcy SC9501, Heidt SC9611, Klajic SC9612, Ferry SC1312,
 *   Boydston SC1314, Ender SC0801: the page becomes a photograph record (its
 *   caption Leon Worden's note and the City's biography as the page gives it),
 *   the scan becomes the portrait.
 *   Pederson LW2529 (#4007) and LW2801 (#4513), Koontz LW2530 (#4009): the
 *   records and the scans were both in the archive, never joined. The scan is
 *   attached to its record and the record's scan becomes the portrait. LW2801
 *   is Pederson in Navy uniform, attached to its record but not his portrait.
 *
 * Every page, scan and obituary was read from the Reggie mirror and matches its
 * manifest of 20 August 2026; copies are in inventory/legacy/fetched.
 *
 * LIVING OR NOT. Boyer (d. 2019), Darcy (d. 2017) and Pederson (d. 2015) are
 * dead by obituaries on the legacy site (the Boyer obituary; Leon Worden's of
 * Darcy; the eulogy for Pederson): their profiles are full lives, with dates.
 * The other six are treated as living: public life only, birth year only, no
 * family, no addresses. Captions of new records drop the family passages and
 * the street addresses, and say so in brackets.
 *
 * SECOND SOURCES, where the archive has one: the account of the 1987 election
 * on photograph #4261; Jerry Reynolds, chapters 69 and 70; Leon Worden's
 * columns of 1996 and 1997; the obituaries. Where a claim rests on the City's
 * biography alone, the text says it is the City's account.
 *
 * DIFFERENCES SHOWN, NOT RESOLVED: Darcy's mayoral years (Leon: 1990, 1995,
 * 1999, 2000; the City in 1998: 1990 and 1994); her start in the Historical
 * Society (her obituary: a founder in 1975; the City: a member since 1985);
 * Koontz's fire department (his page: the Los Angeles City Fire Department;
 * the 1987 account: a retired county firefighter); Heidt's Navy dates
 * (1964 to 1969, in a biography that also puts her on duty in 1962).
 *
 * ALSO: birth evidence from Leon's notes is retrospective (written decades
 * after the birth); Kellar's and Weste's, set to contemporary on 1 and 3
 * October, are corrected to match.
 *
 * Fills empty fields only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_council_profiles_batch1.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$F = "$root/inventory/legacy/fetched";
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$CITY_BODY = 394;
$LIVING_NOTE = '[Leon Worden\'s note gives the full date of birth; this archive records the year only for living people.]';
$SHA = [
    'sc9010.htm' => 'c50b7eeb3673191348ad82ebbf46b8ef602903de29e35fc35945fd6acb0e65eb', 'sc9010.jpg' => '7a3b7d143a33e10c5d8aec0db452baaa3abce3c68d1a3faff4b59671bb884a39',
    'sc9501.htm' => '37c11c3887568582b3e94a4711c7e5d8257020561c8668b08973e5124ccbd129', 'sc9501.jpg' => '0d9b0881be1cf5eae0a28934d332626f7e3eaa387c55095e42c274d136352f6b',
    'sc9611.htm' => 'a63de2683ed5b69c84cb836b60c0a5db148db65e38394499a8a193f6080cc205', 'sc9611.jpg' => '218df63bb871bb55d0e57224666610978c2afedb9f07ab4a35d54742956ac091',
    'sc9612.htm' => '19a0820cd7205d84887dcea1483d87f2c4aae46ee3b54f99e662d84be11fdcb2', 'sc9612.jpg' => '46d1672ecd2f7a897c02f1bd2539fc7a99d782349105c673b627dcb7552a3e91',
    'sc1312.htm' => '11b7d421e3b1993e3d9b5c27da8481f4ed3c194fd3b513215f36f4c9d1a639ef', 'sc1312.jpg' => 'd9034183271dd8bdc4e42c9978a83fba9f2a13775e03492784f2b50c57fce464',
    'sc1314.htm' => 'b5a0c19c372a04810b208ffc4483f137c22407a66b359e1935e21d02136da3f8', 'sc1314.jpg' => 'd0a5368aafdad48246d3bbe2936c56af3e745f366b2f091ac8db18d2747f9b31',
    'sc0801.htm' => 'f9f7945da93549ea86ce30c82216227ceec3e1ae46fa5a7967042c02d7751dad', 'sc0801.jpg' => '10fc037601181773e9f36f403481f044ee66e24b79b59a7d3b6b46302b588df6',
    'obituary_carlboyer3rd.htm' => 'b4f4b32f73c23d91e97d934e9ecd93353277617169419b3dd81aa908d12517c0', 'obituary_joannedarcy.htm' => '083c59b103b2f919a440b5eff1dcb9f096344c5ab66080262bc15a3a94c125ca',
    'lw2802.htm' => 'f56decf5fcce5579ae69b4021dfe27c7164546ed26c4a58ffb479b99352254e5', 'lw2529.htm' => '91cae0a3eecd34032d24edd6d259019fcac61b9e3750561caf5426362a020c9f',
    'lw2801.htm' => 'c9115d64de0953b0bfbf097282372eadff5f91a58b194831d9dad7caa2097361', 'lw2530.htm' => 'e599c2d7609318142d564ee350e7717933ff2ee08dcd530b138607c3e05ba39c',
];
$MIRROR = fn($file) => "SCVHistory.com, $file, from the legacy mirror of 20 August 2026 (inventory/legacy/fetched/$file, SHA-256 " . substr($SHA[$file], 0, 16) . '...)';
$OBIT_BOYER = 'Obituary, "Carl Boyer 3rd, Santa Clarita City Founder, Former Mayor, 1937-2019," as carried on ' . $MIRROR('obituary_carlboyer3rd.htm') . '.';
$OBIT_DARCY = 'Leon Worden, "City Founder Jo Anne Darcy Dies at 86," SCVNews.com, October 30, 2017, as carried on ' . $MIRROR('obituary_joannedarcy.htm') . '.';
$EULOGY = 'Walter Breitinger, "Biography of Mayor George Pederson, LASD," delivered as a eulogy at Christ Lutheran Church, Valencia, June 6, 2015, as carried on ' . $MIRROR('lw2802.htm') . '.';
$C1987 = 'The account of the first council election, November 3, 1987, in the caption of photograph #4261 in this archive.';
$R69 = 'Jerry Reynolds, "69. Rebels With a Cause," History of the Santa Clarita Valley, in this archive (article #2163).';
$R70 = 'Jerry Reynolds, "70. Birth of a City," History of the Santa Clarita Valley, in this archive (article #2165).';
$RET = fn(string $dates) => "Archive records: the City Council elections of $dates, with their returns, and the office holdings for each term.";
$P = [];

/* ---------------------------------------------------------------- Boyer */
$P[15808] = ['title' => 'Carl Boyer', 'living' => false, 'page' => 'sc9010', 'photoTitle' => 'Carl Boyer III, Santa Clarita City Council, 1990', 'photoDate' => '1990',
    'credit' => 'SC9010: 19200 dpi jpeg from original print; photo 1990 by Gary Choppe for City of Santa Clarita.', 'city' => '[City of Santa Clarita 1998]',
    'alias' => 'Carl Boyer III', 'fields' => ['fullName' => 'Carl Boyer', 'birthDate' => 'September 22, 1937', 'birthDateEdtf' => '1937-09-22', 'birthEvidence' => 'retrospective', 'birthplace' => 'Philadelphia, Pennsylvania',
        'deathDate' => 'May 29, 2019', 'deathDateEdtf' => '2019-05-29', 'deathEvidence' => 'contemporary', 'occupation' => 'Teacher; city council member; mayor'],
    'returns' => ['1987-11-03' => [6585, 'elected', 4, 26], '1990-04-10' => [4042, 'elected', 2, 10], '1994-04-12' => [4216, 'elected', 2, 13]],
    'must' => [
        'sc9010.txt' => ['Carl Boyer III (b. September 22, 1937). Resident of Newhall. Santa Clarita City Council member, 1987-1998. Mayor in 1991 and 1996.', 'Served as an elected official on the Community College Board from 1973 to 1981', 'Served on the Castaic Lake Water Agency from 1982 to 1984', 'Treasurer of Healing the Children California', 'Earned a B.A. degree in History from Trinity University, 1959', 'Earned a Masters degree in Education from the University of Cincinnati, 1962', 'Studied at Edinburgh University in Scotland, 1956-57', 'Carl has written many books on family history'],
        'obituary_carlboyer3rd.txt' => ['September 22, 1937 — May 29, 2019', 'Born September 22, 1937, in Philadelphia, he succumbed to cancer on May 29, 2019', 'moved to the Santa Clarita Valley in 1966. A history and government teacher at San Fernando High School', 'The Board of Trustees for College of the Canyons and the former Castaic Lake Water Agency Board', 'In 1976 and 1978 he spearheaded breakaway efforts to form Canyon County', 'He chaired the City of Santa Clarita Formation Committee until he decided to run for a seat on the City Council', 'As a council member from 1987 to 1998 and mayor in 1991 and 1996', 'In 1994 Carl and Chris found themselves with the opportunity to be involved with the organization Healing The Children', 'over the next 15 years they fostered 11 International children', 'medical missions to Ecuador', 'As the administrator, Carl organized and led these mission trips'],
        2163 => ['Spearheading these efforts were Carl Boyer III', 'On the night of November 2, 1976', 'five people, including Boyer, were elected supervisors of the new county in the day\'s polling'],
        2165 => ['Two years and a thousand volunteer hours later, Carl Boyer was elected to chair the city formation committee', 'When Boyer decided to become a City Council candidate he was replaced as chairman'],
        4261 => ['Carl Boyer III, a high school civics teacher and former COC and CLWA board member who had chaired the city formation committee (6,585)'],
        5401 => ['"Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind" (2005)', 'Boyer published a second edition in 2015'],
    ],
    'body' => [
        'Carl Boyer III was a founder of the City of Santa Clarita. He chaired the committee that formed it, was elected to its first City Council in 1987, served on the council until 1998, and was the city\'s mayor in 1991 and 1996.[1][4][6]',
        'Born in Philadelphia on September 22, 1937, he came to the Santa Clarita Valley with his wife, Chris, in 1966, and taught history and government at San Fernando High School.[4][7] He had studied at Edinburgh University in 1956 and 1957, and took a B.A. in history at Trinity University in 1959 and a master\'s degree in education at the University of Cincinnati in 1962.[2]',
        'He served on the College of the Canyons board of trustees from 1973 to 1981 and on the Castaic Lake Water Agency board from 1982 to 1984.[2][4][7] He led the campaigns of 1976 and 1978 to break the valley away from Los Angeles County as Canyon County. The valley voted for it and the county at large against; on the night of the 1976 defeat he was among five people elected supervisors of a county that would not exist.[4][5]',
        'He then turned to cityhood, and after two years on the City Formation Committee was elected its chairman, a post he left to stand for the council.[6][4][7] In the first council election, on November 3, 1987, he was fourth of 26 candidates for five seats, with 6,585 votes. He was re-elected in April 1990, second of ten candidates for three seats, with 4,042, and in April 1994, second of thirteen with 4,216.[3]',
        'From 1994 he and Chris worked with Healing the Children, a Santa Clarita charity that brought children from abroad for medical treatment. Over fifteen years they fostered eleven children from other countries, and he organized and led its medical missions to Ecuador.[4] The City\'s biography of 1998 names him the charity\'s treasurer in California.[2] He wrote many books of family history, and a history of the city\'s founding, "Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind" (2005), with a second edition in 2015.[2][8]',
        'He died of cancer on May 29, 2019.[4]',
    ],
    'notes' => [null, 'City of Santa Clarita, his biography of 1998, as carried on SC9010 (photograph {PHOTO}).', $RET('November 3, 1987, April 10, 1990 and April 12, 1994'), $OBIT_BOYER, $R69, $R70, $C1987, 'Photograph #5401 in this archive, "Santa Clarita: The Book by Carl Boyer," December 2005, and its caption.'],
];

/* ---------------------------------------------------------------- Darcy */
$P[16140] = ['title' => 'Jo Anne Darcy', 'living' => false, 'page' => 'sc9501', 'photoTitle' => 'Jo Anne Darcy, Santa Clarita City Council', 'photoDate' => '',
    'credit' => 'SC9501: 19200 dpi jpeg from original BW print | Archival scan on file.', 'city' => '[City of Santa Clarita 1998]',
    'fields' => ['fullName' => 'Jo Anne Darcy', 'birthDate' => 'May 2, 1931', 'birthDateEdtf' => '1931-05-02', 'birthEvidence' => 'retrospective', 'birthplace' => 'San Angelo, Texas',
        'deathDate' => 'October 29, 2017', 'deathDateEdtf' => '2017-10-29', 'deathEvidence' => 'contemporary', 'occupation' => 'Chamber of commerce executive; county field deputy; city council member; mayor'],
    'returns' => ['1987-11-03' => [7601, 'elected', 3, 26], '1990-04-10' => [3548, 'elected', 3, 10], '1994-04-12' => [5460, 'elected', 1, 13], '1998-04-14' => [7129, 'elected', 1, 15]],
    'must' => [
        'sc9501.txt' => ['Jo Anne Darcy (b. May 2, 1931). Resident of Saugus. Santa Clarita City Council member, 1987-2002. Mayor in 1990, 1995, 1999, 2000. (As of 2014, still the only person to be selected as mayor in consecutive years.)', 'Senior Deputy to Board of Supervisors (15 years)', 'Present Councilwoman (since 12/15/87). Mayor (1990 and 1994).', 'Santa Clarita Valley Chamber of Commerce Executive Vice President / Manager (7 years)', 'Appointed to California State Film Commission in 1989 by Governor Deukmajian, reappointed in 1993 by Governor Pete Wilson', 'Founding Officer of the Friends of the Libraries of SCV in 1978', 'Co-chaired and is Executive Director of the Western Walk of Fame and Newhall Walk of Western Stars, 1981 - present', 'Zonta Club of SCV (International Women\'s Organization) Founding Officer, Past President', 'SCV Historical Society Founding Member, Secretary 1988-1989, member since 1985', 'Association to Aid Victims of Domestic Violence, Founding Member', '"Woman of the Year" award from the SCV Chamber of Commerce in 1984', 'First woman to receive "Citizen of the Year Award" in 1990 from Santa Clarita Elks Lodge'],
        'obituary_joannedarcy.txt' => ['May 2, 1931 — October 29, 2017', 'By Leon Worden, SCVNews.com | Monday, October 30, 2017', 'She was 86.', 'She\'s the only person ever to serve back-to-back terms as Santa Clarita\'s mayor', 'Jo Anne Hall was born May 2, 1931, in San Angelo, Texas. Her mother brought her to California as a child', 'by 1967 the couple had moved to Acton where they reopened the Acton \'49er saloon', 'In the early 1970s the family moved to Saugus. Jo Anne took a job as assistant manager of the Newhall-Saugus-Valencia Chamber of Commerce. In no time at all, she was running the chamber', 'by the time she left in 1980 to serve as the Santa Clarita Valley field deputy to the 5th District\'s newly elected supervisor, Michael D. Antonovich', 'Jo Anne ran the biggest-ever Newhall Fourth of July Parade in 1976', 'She was one of the founders of the SCV Historical Society in 1975', 'She helped launch the local Friends of the Libraries group, started the Newhall Walk of Western Stars in 1981 and served as president of Zonta', 'In 1990 she was the first woman to be named Citizen of the Year by the Santa Clarita Elks Lodge', 'Jo Anne started or helped start the Domestic Violence Center, the annual Wine Auction for the Senior Center and the Celebrity Waiter dinner for the American Heart Association', 'In 2001 the county of Los Angeles emblazoned her name on the Jo Anne Darcy Canyon Country Library', 'final council meeting on April 23, 2002', 'Mayoral rotation, Darcy to Boyer, December 1995.'],
        2165 => ['Joining the city formation committee soon after it was organized were Jo Anne Darcy, Carl Boyer, Jan Heidt and Jill Klajic.'],
        4261 => ['Jo Anne Darcy, field deputy to the SCV\'s county supervisor, Michael D. Antonovich, and former executive director of the Newhall-Saugus-Valencia Chamber of Commerce (7,601)'],
    ],
    'body' => [
        'Jo Anne Darcy served on the Santa Clarita City Council from its first meeting in December 1987 until April 2002, and was the city\'s mayor four times. As of 2017 she was the only person to have been mayor in consecutive years.[1][2][3][4]',
        'She was born Jo Anne Hall in San Angelo, Texas, on May 2, 1931, and came to California as a child. From 1967 she and her husband, Curtis Darcy, ran the Acton \'49er saloon, and in the early 1970s the family moved to Saugus.[4]',
        'She joined the Newhall-Saugus-Valencia Chamber of Commerce as assistant manager and was soon running it; in 1980 she left to become the Santa Clarita Valley field deputy to the newly elected county supervisor, Michael D. Antonovich.[4][5] The City\'s biography of 1998 gives her seven years as the chamber\'s executive vice president and manager and fifteen as a senior deputy to the Board of Supervisors.[2] She ran the 1976 Newhall Fourth of July Parade, helped found the Friends of the Libraries in 1978, started the Newhall Walk of Western Stars in 1981, and was a founding officer and a president of the Zonta Club of the Santa Clarita Valley.[4][2] She was a founding member of the Santa Clarita Valley Historical Society, which her obituary dates to 1975; the City\'s biography gives her membership from 1985 and her term as its secretary in 1988 and 1989.[4][2]',
        'She joined the City Formation Committee soon after it was organized.[6] In the first council election, on November 3, 1987, she was third of 26 candidates for five seats, with 7,601 votes. She was re-elected in April 1990, third of ten candidates for three seats, with 3,548; in 1994, first of thirteen with 5,460; and in 1998, first of fifteen with 7,129.[3] Leon Worden gives her years as mayor as 1990, 1995, 1999 and 2000; the City\'s biography of 1998 gives 1990 and 1994, and a photograph of the mayoral rotation from her to Carl Boyer is dated December 1995.[1][2][4] Her last council meeting was on April 23, 2002.[4]',
        'Governor George Deukmejian appointed her to the California State Film Commission in 1989, and Governor Pete Wilson reappointed her in 1993.[2] She helped start the Domestic Violence Center, the annual wine auction for the Senior Center and the American Heart Association\'s Celebrity Waiter dinner.[4][2] She was the Chamber of Commerce\'s Woman of the Year in 1984 and, in 1990, the first woman named Citizen of the Year by the Santa Clarita Elks Lodge.[2][4] In 2001 Los Angeles County named the Jo Anne Darcy Canyon Country Library for her.[4]',
        'She died on October 29, 2017, at 86.[4]',
    ],
    'notes' => [null, 'City of Santa Clarita, her biography of 1998, as carried on SC9501 (photograph {PHOTO}).', $RET('November 3, 1987, April 10, 1990, April 12, 1994 and April 14, 1998'), $OBIT_DARCY, $C1987, $R70],
];

/* ---------------------------------------------------------------- Heidt */
$P[15737] = ['title' => 'Jan Heidt', 'living' => true, 'page' => 'sc9611', 'photoTitle' => 'Jan Heidt, Santa Clarita City Council, 1996', 'photoDate' => '1996',
    'credit' => 'SC9611: 19200 dpi jpeg from original print; photo 1996 by Gary Choppe for City of Santa Clarita.', 'city' => '[City of Santa Clarita 1998]',
    'birth' => ['January 27, 1939', '1939'],
    'fields' => ['birthDate' => '1939', 'birthDateEdtf' => '1939', 'birthEvidence' => 'retrospective'],
    'returns' => ['1987-11-03' => [8402, 'elected', 2, 26], '1992-04-14' => [6748, 'elected', 1, 16], '1996-04-09' => [3422, 'elected', 2, 13], '2002-04-09' => [5111, 'not-elected', 4, 12]],
    'must' => [
        'sc9611.txt' => ['Janice H. "Jan" Heidt (b. January 27, 1939). Resident of Sand Canyon/Canyon Country. Santa Clarita City Council member, 1987-2000. Mayor in 1989, 1993 and 1998.', 'City Formation Committee member and original member of the Council, serving as the City\'s second Mayor in 1989', 'creation of a City-wide recycling program; oak tree preservation ordinance; actively opposed the siting of the proposed Elsmere Canyon landfill', 'Santa Clarita Valley Homeowners Coalition 1986', 'Spokesperson for this group of 26 homeowner associations', '"Dump the Dump" Task Force 1978', 'Co-founder of this task force which implemented a plan that was successful in preventing two toxic waste dumps from being located in the Santa Clarita Valley', 'Santa Clarita Valley Hazardous Waste Committee 1984', 'Graduate, Michigan State University, B.A. Psychology 1961', 'Navy Officer 1964-1969', 'Communications Officer at Navy Communication Center in the Pentagon for two years', 'On duty during Cuban Missile Crisis and Kennedy assassination', 'Business Owner and Manager, "One for the Books" 1982', 'Own and operate local bookstore in Santa Clarita', '"Zonta Woman of the Year," 1984 & 1988'],
        2163 => ['Art Evans and Jan Heidt rounded up financial backing', 'The new entity was to be called Canyon County'],
        2165 => ['Joining the city formation committee soon after it was organized were Jo Anne Darcy, Carl Boyer, Jan Heidt and Jill Klajic.', 'except for Sand Canyon, whose inclusion was championed by three cityhood leaders who lived there', 'Heidt, with 6,091, became mayor pro tempore'],
        4261 => ['Jan Heidt, a bookstore owner and community activist (8,402)'],
        12370 => ['local bookseller Jan Heidt (One for the Books'],
    ],
    'body' => [
        'Jan Heidt was elected to Santa Clarita\'s first City Council in 1987 and served until 2000. She was the city\'s second mayor, in 1989, and its mayor again in 1993 and 1998.[1][2][3]',
        'In the 1970s she raised money for the campaign to make the valley Canyon County, and she joined the City Formation Committee soon after it was organized; Jerry Reynolds counts her among the three cityhood leaders who won Sand Canyon its place in the new city.[5][6][2]',
        'In the first council election, on November 3, 1987, she was second of 26 candidates for five seats, with 8,402 votes, and became mayor pro tempore.[3][6] She was re-elected in April 1992, first of sixteen candidates for two seats, with 6,748, and in 1996, second of thirteen with 3,422. She stood again in 2002 and came fourth of twelve for three seats, with 5,111.[3]',
        'The City\'s biography of 1998 records her as a co-founder of the "Dump the Dump" task force of 1978, which kept two toxic waste dumps out of the valley, and of the Santa Clarita Valley Hazardous Waste Committee of 1984, and as spokesperson in 1986 for the Santa Clarita Valley Homeowners Coalition, 26 homeowner associations. On the council it credits her with the city-wide recycling program and the oak tree preservation ordinance, and with opposing the Elsmere Canyon landfill proposed just outside the city.[2]',
        'By the same account she graduated from Michigan State University in 1961 and was a Navy officer from 1964 to 1969, two years of it at the Navy Communication Center in the Pentagon; it also puts her on duty there during the Cuban Missile Crisis, which was in 1962.[2] From 1982 she owned and ran a Santa Clarita bookstore, One for the Books. The account of the 1987 election calls her a bookstore owner and community activist, and a column of 1997 a local bookseller.[2][4][7] The Zonta Club named her its Woman of the Year in 1984 and 1988.[2]',
    ],
    'notes' => [null, 'City of Santa Clarita, her biography of 1998, as carried on SC9611 (photograph {PHOTO}).', $RET('November 3, 1987, April 14, 1992, April 9, 1996 and April 9, 2002'), $C1987, $R69, $R70, 'Leon Worden, "Movie trivia from Beale\'s Cut," April 9, 1997, in this archive (article #12370).'],
];

/* ---------------------------------------------------------------- Klajic */
$P[15874] = ['title' => 'Jill Klajic', 'living' => true, 'page' => 'sc9612', 'photoTitle' => 'Jill Klajic, Santa Clarita City Council', 'photoDate' => '',
    'credit' => 'SC9612: 19200 dpi jpeg.', 'city' => '[City of Santa Clarita 1998]',
    'birth' => ['June 14, 1946', '1946'],
    'replace' => ['Cal Coast Recycling, 20833 Santa Clara Street, Santa Clarita, CA 91351' => 'Cal Coast Recycling [address omitted]', 'TCB International, 19748 Collins Road, Santa Clarita, CA 91351-4825' => 'TCB International [address omitted]', 'Precision Technical Services, 7430 Valjean Avenue, Van Nuys, CA 91406-2918' => 'Precision Technical Services, Van Nuys [address omitted]'],
    'fields' => ['birthDate' => '1946', 'birthDateEdtf' => '1946', 'birthEvidence' => 'retrospective'],
    'returns' => ['1990-04-10' => [4081, 'elected', 1, 10], '1994-04-12' => [3788, 'not-elected', 4, 13], '1996-04-09' => [3584, 'elected', 1, 13]],
    'must' => [
        'sc9612.txt' => ['Mary Jillene "Jill" Klajic (b. June 14, 1946). Resident of Canyon Country, Newhall and Valencia during council service. Santa Clarita City Council member, 1990-1994 and 1996-2000. Mayor in 1992.', 'Job Description: General Manager / Recycling Coordinator', 'Developing and organizing training programs an special events for small businesses', 'Job Description: Co-Owner of calibration business', 'First Steps Pre-School, Lancaster, CA', 'Job Description: Founder and president of pre-school', '-El Camino Junior College', '-University of Southern California', '-Golden Oak School', '-College of the Canyons', 'Save the Angeles Foundation', 'Local Government Commission', 'S.C.V. Mayor\'s Committee For Employment of Individuals With Disabilities'],
        2165 => ['Joining the city formation committee soon after it was organized were Jo Anne Darcy, Carl Boyer, Jan Heidt and Jill Klajic.', 'Jill Klajic, who\'d been paid staff to the cityhood campaign'],
    ],
    'body' => [
        'Jill Klajic served two terms on the Santa Clarita City Council, from 1990 to 1994 and from 1996 to 2000, and was mayor in 1992.[1][3]',
        'She came to the council from the cityhood campaign. Jerry Reynolds writes that she joined the City Formation Committee soon after it was organized and was paid staff to the campaign.[4]',
        'Elected in April 1990, first of ten candidates for three seats, with 4,081 votes, she came fourth of thirteen in 1994, with 3,788, and lost her seat. In 1996 she was elected again, first of thirteen candidates for two seats, with 3,584.[3]',
        'The City\'s biography of 1998 lists her as general manager and recycling coordinator of Cal Coast Recycling; a consultant to TCB International, organizing training for small businesses; co-owner of a calibration business, Precision Technical Services, in Van Nuys; and founder and president of First Steps Pre-School in Lancaster. It lists studies at El Camino Junior College, the University of Southern California, Golden Oak School and College of the Canyons, and her part in the Santa Clarita Valley Chamber of Commerce, the Local Government Commission, the Save the Angeles Foundation and the Mayor\'s Committee for Employment of Individuals with Disabilities.[2]',
    ],
    'notes' => [null, 'City of Santa Clarita, her biography of 1998, as carried on SC9612 (photograph {PHOTO}).', $RET('April 10, 1990, April 12, 1994 and April 9, 1996'), $R70],
];

/* ---------------------------------------------------------------- Pederson */
$P[18726] = ['title' => 'George Pederson', 'living' => false, 'existing' => [4007 => [13623, 'lw2529.jpg'], 4513 => [13378, 'lw2801.jpg']], 'portraitPhoto' => 4007,
    'alias' => 'George Ludvig Pederson', 'fields' => ['fullName' => 'George Pederson', 'birthDate' => 'December 4, 1924', 'birthDateEdtf' => '1924-12-04', 'birthEvidence' => 'retrospective', 'birthplace' => 'Madagascar',
        'deathDate' => 'May 9, 2015', 'deathDateEdtf' => '2015-05-09', 'deathEvidence' => 'contemporary', 'occupation' => 'Sheriff\'s captain; city council member; mayor'],
    'returns' => ['1992-04-14' => [5693, 'elected', 2, 16]],
    'must' => [
        'lw2529.txt' => ['George Pederson (b. December 4, 1924). Resident of Valencia. Santa Clarita City Council member, 1992-1996. Mayor in 1994.', 'the last child of a family of Lutheran missionaries', 'In the 1970s, Pederson helped establish what became the Santa Clarita Valley Boys and Girls Club', 'In 1986 he helped gather signatures for the successful city of Santa Clarita formation effort, and he won a City Council seat in 1992 on his first try', 'Santa Clarita\'s "earthquake mayor"', 'in 1996 he ran unsuccessfully for state Assembly. (He garnered the most votes in Santa Clarita but lost to George Runner.)', 'he was named Santa Clarita Valley Man of the Year in 1997', 'A resident of Valencia, he died at age 90 on May 9, 2015', 'where he would finish his law enforcement career in 1984 as commander'],
        'lw2801.txt' => ['served in the U.S. Navy during and after World War II (1943 to 1946)'],
        'lw2802.txt' => ['Dec. 4, 1924 — May 9, 2015', 'Delivered as a Eulogy by Walter Breitinger, His Son-In-Law', 'He was the youngest of nine children', 'started high school at Augustana Academy in Canton, S.D.', 'He served on four different ships and visited seven South Pacific ports from April 1943 to February 1946. His rank was Radioman 3rd Class at war\'s end.', 'In February of 1954, George joined the Los Angeles County Sheriff\'s Department. He started his career at the Wayside Honor Ranch in Castaic', 'He later worked in the North Hollywood Division as a deputy and sergeant until 1964', 'He rose to the rank of lieutenant and transferred back to the Newhall Station', 'they started what is now known as the Boys and Girls Club of the Santa Clarita Valley', 'In 1980, George was promoted to the rank of captain and commanded the Pitchess Detention Center', 'in 1984, at the age of 60, he had to retire in accordance with the sheriff department\'s mandatory retirement rule', 'George managed to obtain a master\'s and doctoral degree in Administration of Justice', 'He taught at College of the Canyons', 'He was mayor during the 1994 earthquake'],
    ],
    'body' => [
        'George Pederson served one term on the Santa Clarita City Council, from 1992 to 1996, and was mayor in 1994, the year of the Northridge earthquake. He was known as the city\'s "earthquake mayor."[1][2][3]',
        'He was born on Madagascar on December 4, 1924, the youngest of nine children in a family of Lutheran missionaries, and went to high school at Augustana Academy in Canton, South Dakota.[1][2] He served in the Navy from April 1943 to February 1946, on four ships, and was a radioman third class at the war\'s end.[2][4]',
        'In February 1954 he joined the Los Angeles County Sheriff\'s Department, starting at the Wayside Honor Ranch in Castaic. He was a deputy and sergeant in North Hollywood until 1964 and returned to the valley as a lieutenant at the Newhall station, where he helped found what became the Boys and Girls Club of the Santa Clarita Valley.[2][1] In 1980, as a captain, he took command of the Pitchess Detention Center, as the Wayside ranch had become, and in 1984 he retired under the department\'s mandatory retirement rule.[2][1] He earned a master\'s degree and a doctorate in the administration of justice, and taught at College of the Canyons.[2]',
        'In 1986 he helped gather signatures for the cityhood petition.[1] Elected to the council on his first try in April 1992, second of sixteen candidates for two seats, with 5,693 votes, he served one term.[1][3] In 1996 he ran for the State Assembly and lost to George Runner, though he had the most votes in Santa Clarita. In 1997 he was named Santa Clarita Valley Man of the Year.[1]',
        'He died in Valencia on May 9, 2015, at 90.[1][2]',
    ],
    'notes' => ['Leon Worden\'s note on George Pederson\'s City Council portrait, LW2529, in this archive (photograph #4007).', $EULOGY, $RET('April 14, 1992'), 'Leon Worden\'s note on LW2801, George Pederson in the Navy, in this archive (photograph #4513).'],
];

/* ---------------------------------------------------------------- Koontz */
$P[23081] = ['title' => 'Dennis Koontz', 'living' => true, 'existing' => [4009 => [13622, 'lw2530.jpg']], 'portraitPhoto' => 4009,
    'trimCaption' => [4009, 'Dennis Michael Koontz (b. November 19, 1939).', 'Dennis Michael Koontz (b. 1939).'],
    'fields' => ['birthDate' => '1939', 'birthDateEdtf' => '1939', 'birthEvidence' => 'retrospective'],
    'returns' => ['1987-11-03' => [6164, 'elected', 5, 26], '1990-04-10' => [2155, 'not-elected', 7, 10]],
    'must' => [
        'lw2530.txt' => ['Dennis Michael Koontz (b. November 19, 1939). Resident of Valencia during council service. Santa Clarita City Council member, 1987-1990.', 'Dennis retired from the Los Angeles City Fire Department as a Fire Captain. He worked on the City Formation Committee and was elected to the first City Council', 'worked as the Health Assistant for more than 15 years. He "retired" in 2010', 'They also began selling real estate in the early 1990s and became Realtors', 'Founding Member of the United Firefighters of Los Angeles City, Local 112', 'Captain of a paramedic engine company, Los Angeles City Fire Department', 'CSEA President, Chapter 349', 'Classified Personnel Commission, William S. Hart Union High School District'],
        4261 => ['Dennis Koontz, a retired county firefighter (6,164)'],
    ],
    'body' => [
        'Dennis Koontz was elected to Santa Clarita\'s first City Council in 1987 and served until 1990.[1][2]',
        'He worked on the City Formation Committee.[1] In the first council election, on November 3, 1987, he was fifth of 26 candidates for the five seats, with 6,164 votes. He stood again in April 1990 and came seventh of ten, with 2,155.[2]',
        'The biography on his council page says he retired from the Los Angeles City Fire Department as a fire captain, captain of a paramedic engine company, and was a founding member of the United Firefighters of Los Angeles City, Local 112.[1] The account of the 1987 election in this archive calls him a retired county firefighter.[3]',
        'By the same biography he was later health assistant at Valencia High School for more than fifteen years, retiring in 2010; president of Chapter 349 of the California School Employees Association; and a member of the William S. Hart Union High School District\'s classified personnel commission. From the early 1990s he sold real estate.[1]',
    ],
    'notes' => ['Dennis Koontz\'s City Council page, LW2530, in this archive (photograph #4009): Leon Worden\'s note and a biography of 2013.', $RET('November 3, 1987 and April 10, 1990'), $C1987],
];

/* ---------------------------------------------------------------- Ferry */
$P[23083] = ['title' => 'Frank Ferry', 'living' => true, 'page' => 'sc1312', 'photoTitle' => 'Frank Ferry, Santa Clarita City Council', 'photoDate' => '',
    'credit' => 'SC1312: 19200 dpi jpeg.', 'city' => '[City of Santa Clarita 2013]', 'birth' => ['Sept. 4, 1965', '1965'],
    'omit' => ['Frank Ferry has two sons'],
    'fields' => ['birthDate' => '1965', 'birthDateEdtf' => '1965', 'birthEvidence' => 'retrospective'],
    'returns' => ['1996-04-09' => [3208, 'not-elected', 3, 13], '1998-04-14' => [6583, 'elected', 2, 15], '2002-04-09' => [6684, 'elected', 1, 12], '2006-04-11' => [5500, 'elected', 2, 11], '2010-04-13' => [6510, 'elected', 3, 11]],
    'must' => [
        'sc1312.txt' => ['Frank Ferry (b. Sept. 4, 1965). Resident of Valencia and Saugus (during council service). Santa Clarita City Council member, 1998-2014. Mayor in 2002, 2009, part of 2012.', 'He holds a Bachelor\'s degree in Governmental Communications from California State University, Northridge as well as a Bachelor\'s degree in Law, a Juris Doctorate, and a California Teaching Credential', 'served as Mayor that same year, in addition to serving as Mayor in 2009 and 2012', 'getting roads built and alleviating traffic problems', 'more after school programs offered at every elementary school campus', 'more "active" park space', 'the California Contract Cities Committee, Regional Planning Committee, William S. Hart Education Committee, and 2000 Census Committee', 'Ferry is also Principal at Alemany High School. Formerly, he taught United States history and government'],
        12404 => ['Frank Ferry, a Valencia High School teacher'],
    ],
    'body' => [
        'Frank Ferry served four terms on the Santa Clarita City Council, from 1998 to 2014, and was mayor in 2002, 2009 and part of 2012.[1][2][3]',
        'He first stood in April 1996 and came third of thirteen candidates for two seats, with 3,208 votes. Elected in April 1998, second of fifteen candidates for three seats, with 6,583, he was re-elected in 2002, first of twelve with 6,684; in 2006, second of eleven with 5,500; and in 2010, third of eleven with 6,510.[3]',
        'By the City\'s account he holds a bachelor\'s degree in governmental communications from California State University, Northridge, a bachelor\'s degree in law, a juris doctorate and a California teaching credential.[2] A column of November 1997 on the coming council race calls him a Valencia High School teacher. The City says he taught United States history and government, and by 2013 he was principal of Alemany High School.[4][2]',
        'The City says he first ran to get roads built and relieve traffic, and that on the council he wanted after-school programs at every elementary school and more active park space. It lists his service on the California Contract Cities committee, the Regional Planning Committee, the William S. Hart education committee and the 2000 Census committee.[2]',
    ],
    'notes' => [null, 'City of Santa Clarita, his biography of 2013, as carried on SC1312 (photograph {PHOTO}).', $RET('April 9, 1996, April 14, 1998, April 9, 2002, April 11, 2006 and April 13, 2010'), 'Leon Worden, "Curious about homeless, City Council hopefuls," November 19, 1997, in this archive (article #12404).'],
];

/* ---------------------------------------------------------------- Boydston */
$P[21946] = ['title' => 'TimBen Boydston', 'living' => true, 'page' => 'sc1314', 'photoTitle' => 'TimBen Boydston, Santa Clarita City Council', 'photoDate' => '',
    'credit' => 'SC1314: 19200 dpi jpeg.', 'city' => '[City of Santa Clarita 2013]', 'birth' => ['Aug. 12, 1955', '1955'],
    'omit' => ['TimBen has been married for over 20 years'],
    'fields' => ['birthDate' => '1955', 'birthDateEdtf' => '1955', 'birthEvidence' => 'retrospective'],
    'returns' => ['1996-04-09' => [282, 'not-elected', 12, 13], '2010-04-13' => [5863, 'not-elected', 5, 11], '2012-04-10' => [6145, 'elected', 2, 5], '2016-11-08' => [17108, 'not-elected', 3, 11], '2018-11-06' => [12857, 'not-elected', 7, 15], '2020-11-03' => [17724, 'not-elected', 5, 9]],
    'must' => [
        'sc1314.txt' => ['Timothy Ben "TimBen" Boydston (b. Aug. 12, 1955). Resident of Newhall (Princess Homes). Santa Clarita City Council member, 2006-2008 (appointed) and 2012 to present (elected).', 'TimBen Boydston came to the Santa Clarita Valley as a boy with his family in 1960. He attended Sulphur Springs Elementary school, Placerita Jr. High school, and graduated from Canyon High School. After serving his country in the United States Air Force for four years', 'He then transferred to CSUN where he earned a Bachelor\'s degree in Theatre', 'owned and operated a candy kitchen in Venice Beach, which he sold after he began working in commercial property management', 'in 1998 he became the Executive Director of the Canyon Theatre Guild', 'purchased and renovated its first permanent home, located on Main Street in Old Town Newhall', 'When the people of the Santa Clarita Valley were trying to get their own local political control by forming Canyon County in 1978, he ran for his first political office', 'in 2006 he applied for and was appointed to fill out then-Councilmember Cameron Smyth\'s term when Mr. Smyth was elected to the State Assembly', 'to not run in the 2008 election', 'he formed the Santa Clarita Neighborhood Coalition'],
        12520 => ['Timothy Ben Boydston, president of the Canyon Theatre Guild'],
        22420 => ['TimBen Boydston was elected as member of the City Council for the full term of four years'],
    ],
    'body' => [
        'TimBen Boydston served on the Santa Clarita City Council twice: by appointment from 2006 to 2008, finishing Cameron Smyth\'s term after Smyth was elected to the State Assembly, and by election from 2012 to 2016.[1][2][3]',
        'By the City\'s account he came to the valley as a boy in 1960, went to Sulphur Springs Elementary, Placerita Junior High and Canyon High, served four years in the United States Air Force, and earned a bachelor\'s degree in theatre at CSUN. He ran a candy kitchen in Venice Beach and worked in commercial property management, and in 1998 became executive director of the Canyon Theatre Guild, which under him bought and renovated a permanent home on Main Street in Newhall.[2] A column of May 1996 already calls him the Guild\'s president.[5]',
        'He first ran for office in 1978, in the election for the proposed Canyon County, and for the City Council in 1996, when he came twelfth of thirteen.[2][3] After his appointment he kept a promise not to run in 2008, and formed the Santa Clarita Neighborhood Coalition.[2] He came fifth of eleven in 2010, and in April 2012 was elected, second of five candidates for two seats, with 6,145 votes, a result the council certified in Resolution No. 12-9.[3][4] He came third of eleven in 2016 and was not re-elected, and stood again in 2018 and 2020 without winning a seat.[3]',
    ],
    'notes' => [null, 'City of Santa Clarita, his biography of 2013, as carried on SC1314 (photograph {PHOTO}).', $RET('April 9, 1996, April 13, 2010, April 10, 2012, November 8, 2016, November 6, 2018 and November 3, 2020'), 'Resolution No. 12-9, declaring the results of the General Municipal Election of April 10, 2012, Section 4, in this archive (document #21936).', 'Leon Worden, "Creating a Theater District in Old Town Newhall," May 15, 1996, in this archive (article #12520).'],
];

/* ---------------------------------------------------------------- Ender */
$P[23087] = ['title' => 'Laurie Ender', 'living' => true, 'page' => 'sc0801', 'photoTitle' => 'Laurie Ender, Santa Clarita City Council', 'photoDate' => '',
    'credit' => 'SC0801: 19200 dpi jpeg.', 'city' => '[City of Santa Clarita 2008]', 'birth' => ['Feb. 19. 1964', '1964'],
    'replace' => ['Prior to leaving the workplace to raise her three children and become a community advocate, Laurie was a producer for the popular news magazine "Entertainment Tonight" and was one of the original producers for NBC\'s nationally syndicated television series "Access Hollywood." She is a graduate of Pepperdine University with a degree in Broadcasting. Laurie and her husband, Chris, are 16-year residents of Santa Clarita and live in Valencia with their three children, Jason, Griffin and Davis.'
        => 'Prior to leaving the workplace [...], Laurie was a producer for the popular news magazine "Entertainment Tonight" and was one of the original producers for NBC\'s nationally syndicated television series "Access Hollywood." She is a graduate of Pepperdine University with a degree in Broadcasting. [...]'],
    'fields' => ['birthDate' => '1964', 'birthDateEdtf' => '1964', 'birthEvidence' => 'retrospective'],
    'returns' => ['2008-04-08' => [6180, 'elected', 1, 5], '2012-04-10' => [5408, 'not-elected', 3, 5]],
    'must' => [
        'sc0801.txt' => ['Laurie H. Ender (b. Feb. 19. 1964). Resident of Valencia. Santa Clarita City Council member, 2008-2012. Mayor from December 2011 to April 2012.', 'having served as a Parks, Recreation and Community Services Commissioner since 2003 and as chairman of the Commission during 2006-2007', 'Todd Longshore Park, Veterans Memorial Plaza and Valencia Heritage Park', 'Laurie served as the president of the Santa Clarita Valley Council PTA for two years', 'the SCV Council PTA was awarded the top honor in the state from the California State PTA in 2005', 'Laurie was a producer for the popular news magazine "Entertainment Tonight" and was one of the original producers for NBC\'s nationally syndicated television series "Access Hollywood." She is a graduate of Pepperdine University with a degree in Broadcasting'],
    ],
    'body' => [
        'Laurie Ender served one term on the Santa Clarita City Council, from April 2008 to April 2012, and was mayor from December 2011 to April 2012.[1][3]',
        'Elected in April 2008, first of five candidates for two seats, with 6,180 votes, she came third of five in 2012, with 5,408, and was not re-elected.[3]',
        'Before the council, by the City\'s account, she had been a Parks, Recreation and Community Services commissioner since 2003 and chaired the commission in 2006 and 2007, working on Todd Longshore Park, Veterans Memorial Plaza and Valencia Heritage Park. She was president of the Santa Clarita Valley Council PTA for two years, during which it received the California State PTA\'s top honor, in 2005. She had been a producer of "Entertainment Tonight" and one of the original producers of "Access Hollywood," and is a graduate of Pepperdine University in broadcasting.[2]',
    ],
    'notes' => [null, 'City of Santa Clarita, her biography of 2008, as carried on SC0801 (photograph {PHOTO}).', $RET('April 8, 2008 and April 10, 2012')],
];

/* ================================================================ checks */
$bad = []; $plan = [];
foreach ($SHA as $file => $sha) { if (!is_file("$F/$file") || hash_file('sha256', "$F/$file") !== $sha) { $bad[] = "$file missing or not the mirror's"; } }
$txt = fn($k) => $ws(@file_get_contents("$F/$k"));
$read = function ($src) use ($ws, $txt) {
    if (is_string($src)) { return $txt($src); }
    $e = Entry::find()->id($src)->status(null)->one(); if (!$e) { return ''; }
    if ($e->section->handle === 'officeHoldings') { return $ws(implode(' ', array_column($e->footnotes ?? [], 'note'))); }
    $h = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; }
    return $ws(($e->body ?? '') . ' ' . (isset($h['photoCaptionExt']) ? $e->photoCaptionExt : ''));
};
/* A caption from the page: Leon's note, then the City's text, family and addresses dropped for the living. */
$caption = function (array $c) use ($F, $LIVING_NOTE) {
    $lines = array_values(array_filter(array_map('trim', explode("\n", (string)@file_get_contents("$F/{$c['page']}.txt"))), fn($l) => $l !== ''));
    $cs = null; $ce = null; $ns = null;
    foreach ($lines as $i => $l) {
        if ($ns === null && preg_match('~\(b\. ~', $l)) { $ns = $i; }
        if ($cs === null && str_starts_with($l, $c['city'])) { $cs = $i; }
        if ($ce === null && str_starts_with($l, strtoupper($c['page']) . ':')) { $ce = $i; }
    }
    if ($ns === null || $cs === null || $ce === null || !($ns < $cs && $cs < $ce)) { return null; }
    $note = implode(' ', array_slice($lines, $ns, $cs - $ns));
    $city = array_slice($lines, $cs + 1, $ce - $cs - 1);
    $omitted = false;
    if (!empty($c['omit'])) { $city = array_values(array_filter($city, function ($l) use ($c, &$omitted) { foreach ($c['omit'] as $o) { if (str_starts_with($l, $o)) { $omitted = true; return false; } } return true; })); }
    foreach ($c['replace'] ?? [] as $from => $to) { foreach ($city as $i => $l) { if ($l === $from) { $city[$i] = $to; $omitted = true; } } }
    $out = [];
    if ($c['living']) { $note = str_replace('(b. ' . $c['birth'][0] . ')', '(b. ' . $c['birth'][1] . ')', $note); }
    $out[] = $note;
    $out[] = $c['city'] . ' ' . array_shift($city);
    $out = array_merge($out, $city);
    if ($c['living']) { $out[] = $LIVING_NOTE; }
    if ($omitted) { $out[] = '[Passages on family, and street addresses, are omitted; this archive records public life only for living people.]'; }
    return $out;
};

foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    /* The title is built from fullName; on 3 October a first run set the full forms there, which belong in the aliases. */
    if (!$p || !in_array($p->title, array_filter([$c['title'], $c['alias'] ?? null]), true)) { $bad[] = "#$id is not {$c['title']}"; continue; }
    foreach ($c['must'] as $src => $phrases) { $t = $read($src); foreach ($phrases as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "{$c['title']}: " . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
    $want = $c['returns'];
    foreach (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $id, 'field' => 'candidacyPerson'])->all() as $cd) {
        $e = $cd->candidacyElection->one(); $all = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->orderBy('votes desc')->ids();
        $got = [(int)$cd->votes, (string)$cd->outcome->value, array_search($cd->id, $all) + 1, count($all)];
        if (($want[$e->electionDateEdtf] ?? null) !== $got) { $bad[] = "{$c['title']}: the {$e->electionDateEdtf} candidacy reads " . json_encode($got); }
        unset($want[$e->electionDateEdtf]);
    }
    if ($want) { $bad[] = "{$c['title']}: no candidacy for " . implode(', ', array_keys($want)); }
    if (array_filter(Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $id, 'field' => 'holdingPerson'])->all(), fn($h) => $h->holdingBody->one()?->id !== $CITY_BODY)) { $bad[] = "{$c['title']}: a term not on the council"; }
    $cap = null;
    if (isset($c['page'])) {
        $cap = $caption($c);
        if (!$cap) { $bad[] = "{$c['title']}: the caption could not be read from {$c['page']}.txt"; }
        elseif (!str_contains($txt("{$c['page']}.txt"), $ws($c['credit']))) { $bad[] = "{$c['title']}: the credit line is not on the page"; }
        if ($cap && $c['living'] && str_contains(implode(' ', $cap), $c['birth'][0])) { $bad[] = "{$c['title']}: the full birth date is in the caption"; }
        foreach (array_merge(array_keys($c['replace'] ?? []), $c['omit'] ?? []) as $gone) { if ($cap && str_contains(implode(' ', $cap), mb_substr($gone, 0, 40))) { $bad[] = "{$c['title']}: the caption still has \"" . mb_substr($gone, 0, 40) . '"'; } }
    } else {
        foreach ($c['existing'] as $phId => [$aid, $fname]) {
            $ph = Entry::find()->id($phId)->status(null)->one(); $a = Asset::find()->id($aid)->one();
            if (!$ph || !$a || $a->filename !== $fname) { $bad[] = "{$c['title']}: photograph #$phId or asset #$aid ($fname) not as read"; }
            elseif (($cur = $ph->featuredImage->one()) && $cur->id !== $aid) { $bad[] = "{$c['title']}: #$phId already has an image ({$cur->filename})"; }
        }
    }
    $body = implode("\n\n", $c['body']);
    $cur = trim((string)$p->body);
    if ($cur !== '' && $cur !== trim($body)) { $bad[] = "{$c['title']}: has a body already"; }
    foreach ($c['fields'] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have !== '' && $have !== $v && !in_array($k, ['occupation', 'fullName'], true)) { $bad[] = "{$c['title']}: $k is \"$have\", not \"$v\""; } }
    preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
    if (max($used) > count($c['notes']) || count($used) !== count($c['notes'])) { $bad[] = "{$c['title']}: notes used " . json_encode(array_values($used)) . ' of ' . count($c['notes']); }
    if (preg_match('~\x{2014}~u', $body . implode('', array_filter($c['notes'])) . ($cap ? implode('', $cap) : ''))) { $bad[] = "{$c['title']}: an em dash"; }
    $plan[$id] = ['caption' => $cap, 'body' => $body, 'done' => $cur === trim($body)];
    echo str_pad($c['title'], 16) . (isset($c['page']) ? 'new photograph ' . strtoupper($c['page']) . ' (' . ($cap ? count($cap) : 0) . ' caption paragraphs)' : 'attach ' . implode(', ', array_map(fn($k, $v) => "#$k <- {$v[1]}", array_keys($c['existing']), $c['existing']))) . '; ' . ($c['living'] ? 'living, born ' . $c['fields']['birthDate'] : 'died ' . $c['fields']['deathDate']) . '; body ' . ($plan[$id]['done'] ? 'already written' : str_word_count($body) . ' words, ' . count($c['notes']) . ' notes') . PHP_EOL;
}
/* Leon's notes were written decades after the births they give. */
$fix = [];
foreach ([21944 => 'Bob Kellar', 15929 => 'Laurene Weste'] as $id => $t) { $e = Entry::find()->id($id)->status(null)->one(); if ($e?->title !== $t) { $bad[] = "#$id is not $t"; } elseif (($e->birthEvidence->value ?? '') === 'contemporary') { $fix[] = $id; } }
echo 'birth evidence contemporary -> retrospective: ' . ($fix ? implode(', ', array_map(fn($i) => "#$i", $fix)) : 'none') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!empty($SHOW)) { foreach ($P as $id => $c) { echo PHP_EOL . "## {$c['title']}" . PHP_EOL; foreach ($plan[$id]['caption'] ?? [] as $l) { echo "  CAP: $l" . PHP_EOL; } foreach ($c['body'] as $l) { echo "  $l" . PHP_EOL; } } }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ================================================================ writes */
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
$short = []; $n = 0;
foreach ($P as $id => $c) {
    $tx = Craft::$app->getDb()->beginTransaction();
    try {
        $p = Entry::find()->id($id)->status(null)->one();
        if (isset($c['page'])) {
            $k = $c['page']; $photo = Entry::find()->section('photographs')->status(null)->legacyKey($k)->one();
            if (!$photo) {
                $tmp = sys_get_temp_dir() . "/$k.jpg"; copy("$F/$k.jpg", $tmp);
                $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename("$k.jpg"); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
                $a->setFieldValues(['provenanceKind' => 'legacy-mirror', 'legacySourcePath' => "gif/$k.jpg", 'acquiredDate' => '2026-10-03',
                    'source' => "SCVHistory.com, gif/$k.jpg, from the legacy mirror of 20 August 2026, SHA-256 {$SHA["$k.jpg"]}, matching the manifest; the stored copy is re-encoded on import."]);
                if (!$elements->saveElement($a)) { throw new \RuntimeException("$k scan: " . json_encode($a->getFirstErrors())); }
                $photo = new Entry(); $photo->sectionId = $svc->getSectionByHandle('photographs')->id; $photo->setTypeId($svc->getEntryTypeByHandle('photograph')->id); $photo->title = $c['photoTitle'];
                $h = array_map(fn($f) => $f->handle, $photo->getFieldLayout()->getCustomFields());
                preg_match('~^\S+: (\d+) dpi~', $c['credit'], $dpi);
                $photo->setFieldValues(array_intersect_key(array_filter(['featuredImage' => [$a->id], 'body' => implode("\n\n", $plan[$id]['caption']), 'photoSourceCode' => strtoupper($k), 'creditRaw' => $c['credit'], 'creditDpi' => $dpi[1] ?? '',
                    'photoDate' => $c['photoDate'], 'photoDateEdtf' => $c['photoDate'], 'photoPeople' => [$id], 'legacyKey' => $k, 'legacyUrl' => "/scvhistory/$k.htm", 'sourcePath' => "https://scvhistory.com/scvhistory/$k.htm"], fn($v) => $v !== '' && $v !== []), array_flip($h)));
                if (!$elements->saveElement($photo)) { throw new \RuntimeException("$k photograph: " . json_encode($photo->getFirstErrors())); }
            }
            $portrait = $photo->featuredImage->one(); $photoRef = "#{$photo->id}";
        } else {
            foreach ($c['existing'] as $phId => [$aid, $fname]) {
                $ph = Entry::find()->id($phId)->status(null)->one(); $vals = [];
                if (!$ph->featuredImage->one()) { $vals['featuredImage'] = [$aid]; }
                /* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
                if (!in_array($id, $ph->photoPeople->status(null)->ids(), true)) { $vals['photoPeople'] = array_merge($ph->photoPeople->status(null)->ids(), [$id]); }
                if ($vals) { $ph->setFieldValues($vals); if (!$elements->saveElement($ph)) { throw new \RuntimeException("#$phId: " . json_encode($ph->getFirstErrors())); } }
            }
            if (!empty($c['trimCaption'])) {
                [$phId, $from, $to] = $c['trimCaption']; $ph = Entry::find()->id($phId)->status(null)->one(); $b = (string)$ph->body;
                if (str_contains($b, $from)) { $ph->setFieldValue('body', str_replace($from, $to, $b) . "\n\n" . $LIVING_NOTE); if (!$elements->saveElement($ph)) { throw new \RuntimeException("#$phId caption"); } }
            }
            $portrait = Asset::find()->id($c['existing'][$c['portraitPhoto']][0])->one(); $photoRef = '#' . $c['portraitPhoto'];
        }
        $notes = $c['notes'];
        if ($notes[0] === null) { $notes[0] = 'Leon Worden\'s note on ' . ($c['living'] ? 'the' : 'the') . ' City Council portrait, ' . strtoupper($c['page']) . ", in this archive (photograph $photoRef)."; }
        $notes = array_map(fn($x) => str_replace('{PHOTO}', $photoRef, $x), $notes);
        $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
        $vals = [];
        if (!$p->featuredImage->one()) { $vals['featuredImage'] = [$portrait->id]; }
        foreach ($c['fields'] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have === '' || ($k === 'occupation' && $have !== $v) || ($k === 'fullName' && $have !== $v)) { $vals[$k] = $v; } }
        if (!$plan[$id]['done']) { $vals += ['body' => $plan[$id]['body'], 'footnotes' => $fn($notes), 'bodyAuthorship' => 'editorial-2026', 'recordProvenance' => trim((string)$p->recordProvenance . '; build_council_profiles_batch1.php, 3 Oct 2026: ' . ($c['living'] ? 'public-life profile' : 'profile') . ' from the legacy council page', '; ')]; }
        if (!empty($c['alias'])) { $al = array_filter(array_map('trim', preg_split('~[;\n]~', (string)$p->personAliases))); if (!in_array($c['alias'], $al, true)) { $vals['personAliases'] = implode(str_contains((string)$p->personAliases, ';') ? '; ' : "\n", array_merge($al, [$c['alias']])); } }
        $vals = array_intersect_key($vals, array_flip($h));
        if ($vals) { $p->setFieldValues($vals); if (!$elements->saveElement($p)) { throw new \RuntimeException("#$id: " . json_encode($p->getFirstErrors())); } }
        $tx->commit();
    } catch (\Throwable $t) { $tx->rollBack(); echo "ROLLED BACK {$c['title']}: " . $t->getMessage() . PHP_EOL; $short[] = $c['title']; continue; }
    $r = Entry::find()->id($id)->status(null)->one();
    $ok = trim((string)$r->body) === trim($plan[$id]['body']) && $r->featuredImage->one()?->id === $portrait->id && (string)$r->birthDate === $c['fields']['birthDate'] && $r->title === $c['title'];
    if (!$ok) { $short[] = $c['title']; } else { $n++; }
    echo ($ok ? 'OK    ' : 'SHORT ') . $c['title'] . ' ' . $r->url . PHP_EOL;
}
foreach ($fix as $id) { $e = Entry::find()->id($id)->status(null)->one(); $e->setFieldValue('birthEvidence', 'retrospective'); if (!$elements->saveElement($e) || (Entry::find()->id($id)->status(null)->one()->birthEvidence->value ?? '') !== 'retrospective') { $short[] = "#$id evidence"; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records, " . count($fix) . ' evidence corrections') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_council_profiles_batch1.php', $n, $short ? 'SHORT' : 'verified', 'council batch: Boyer, Darcy, Heidt, Klajic, Pederson, Koontz, Ferry, Boydston, Ender from their legacy council pages; Kellar and Weste birth evidence retrospective');
if ($short) { throw new \RuntimeException('build_council_profiles_batch1: ' . implode(', ', $short)); }
