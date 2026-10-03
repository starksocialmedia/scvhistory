/**
 * The 25: batch 3 (Nathan, 3 October 2026: "Keep Mix and Carey as planned.
 * Mix lived here and Carey's ranch has its own place record. Fages too";
 * "a person belongs in this archive for what they did here, and a profile
 * leads with that").
 *
 * Six profiles, each opening with what the person did in the valley:
 *   Pedro Fages (#287)        the 1772 pursuit through the valley; named Agua
 *                             Dulce and Soledad (Reynolds ch. 9; LW2504)
 *   Tom Mix (#18702)          his Newhall movie town and lodgings, 1916 to the
 *                             mid-1920s (Birchard via the 1916 page; AL3022)
 *   Harry Carey (#15919)      the San Francisquito Canyon ranch and trading
 *                             post, 1916-1945 (HABS CA-2712; DC2101)
 *   Tiburcio Vasquez (#285)   Elizabeth Lake and Soledad Canyon, 1873-74, and
 *                             the rocks named for him (Glenn 1974)
 *   Juventino del Valle (#303) Rancho Camulos, 1862-1886 (LW3664)
 *   John T. Gifford (#337)    Newhall's first railroad agent and telegrapher
 *                             (AP0622)
 * Fages's, Juventino's and Gifford's WordPress bodies are replaced. Where
 * sources disagree the profile shows both (Mix's first year in Newhall,
 * Vasquez's capture date). Fields: corrected to what a source says, with a
 * note giving the old value (Vasquez's birth date, Fages's and Juventino's
 * birthplaces, Juventino's birth date, Gifford's birthplace, which carried a
 * census comment); unsourced burial marked uncited. Every page was read from
 * the Reggie mirror and matches its manifest (batch5-sha.json).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_profiles_batch5.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$F = "$root/inventory/legacy/fetched";
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$RIGHTS = '~SCVTV|SOLELY RESPONSIBLE|additional restrictions|Personal or Research use|ownership of any original|enable JavaScript|Click to enlarge|Click image|Download original|^SCVHistory\.com~';
$prose = function ($k, $from = null, $to = null) use ($F, $RIGHTS) { return []; };
$txt = fn($k) => $ws(@file_get_contents("$F/$k.txt"));
$VERIFIED = json_decode((string)@file_get_contents("$F/batch5-sha.json"), true)['files'] ?? [];
$okFile = fn($name) => is_file("$F/$name") && isset($VERIFIED[$name]) && hash_file('sha256', "$F/$name") === $VERIFIED[$name];
$LEGACY = fn($k, $title) => "$title, as carried on SCVHistory.com, /scvhistory/$k.htm.";
$IMP = [];

$P = [];
$P[287] = ['title' => ['Pedro Fages'], 'replaceBody' => true,
    'correct' => ['birthplace' => ['Guissona, Catalonia, Spain', 'Catalonia']],
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective'],
    'notesAdd' => [['His birthplace', 'This record formerly gave his birthplace as Guissona, Catalonia, Spain. The sources here say only that he was a Catalonian soldier; no source for the town has been found.']],
    'must' => [837 => ['Captain Pedro Fages (c. 1734–1796), Catalonian soldier', 'He named Agua Dulce and Soledad during his 1772 expedition through the Santa Clarita Valley', 'heading to Monterey in 1770 to assume command as governor of Alta California', 'Several of his place names, such as Soledad and Agua Dulce, are still with us', 'Fages was a lieutenant in charge of the twenty-five Catalonian soldiers during the Portolá march to Monterey', 'six of his soldados de cuero had deserted', 'across the wide, reedy banks of the Mojave River, up into the Antelope Valley, then plunging into the Sierra Pelonas to emerge at the headwaters of the Rio Santa Clara', 'The party\'s first camp in this valley was probably made near Agua Dulce Springs, which Fages named for its supply of "sweet water."', 'At the point where Castaic Creek joins the Santa Clara River, Fages was back on familiar ground. Three years earlier, he had stood in this spot with Governor Portolá and Father Juan Crespí', 'the venerable kika, or chief, of the Tataviam community and was led to believe that the men he was looking for were somewhere up in Castaic Canyon', 'After resting men and animals for two days', 'They camped at Cienaga ("swamp"), where Fish Creek joins Castaic, then at Laguna (Lake Elizabeth)', 'which Fages had called "Cañada de las Uvas" for the wild grapes growing in the canyon', 'He never found the deserters', 'adding thousands of acres to previously blank maps', 'In 1777, he fought Apaches on the Sonoran frontier, returning as a lieutenant colonel to serve again as governor until 1791'],
        3929 => ['when he gazed upon the Santa Clarita Valley for the first time in August 1769 as a member of the Portolá expedition', 'When he returned in 1772 in search of some deserters, he did so as governor of Alta California', 'the marker still stands on Lebec Road', 'Top Of Grapveine Pass, Where Don Pedro Fages Passed In 1772']],
    'body' => [
        'Pedro Fages first saw the Santa Clarita Valley as a lieutenant with the Portolá expedition in August 1769, and came back in 1772 as governor of Alta California at the head of his own party. He named Agua Dulce and Soledad, and both names are still in use.[1][2]',
        'In the spring of 1772 six of his soldiers deserted, and Fages went after them by way of the Mojave River and the Antelope Valley, coming down through the Sierra Pelona to the head of the Santa Clara River. His first camp in the valley was probably near Agua Dulce Springs, which he named for its sweet water. Where Castaic Creek joins the Santa Clara, the spot where he had stood with Portolá and Crespí three years before, the chief of the Tataviam community led him to believe the men were up Castaic Canyon. After two days\' rest the party climbed the canyon, camping at the Cienaga where Fish Creek joins Castaic and at Laguna, which Jerry Reynolds identifies as Lake Elizabeth, and crossed Tejon Pass, which Fages called the Cañada de las Uvas for its wild grapes. He never found the deserters.[1]',
        'His account of the pursuit added thousands of acres to blank maps, and a state historical landmark on Lebec Road, at the top of the Grapevine, marks where he passed in 1772.[1][2] Born about 1734, a Catalonian, he had led the twenty-five Catalonian soldiers on the 1769 march; he later fought Apaches on the Sonoran frontier, served again as governor until 1791, and died in 1796.[1]',
    ],
    'notes' => ['Jerry Reynolds, "Chapter 9. The Trail Blazer," History of the Santa Clarita Valley, article #837 in this archive.', 'Leon Worden, "Don Pedro Fages: California Historical Landmark No. 263," LW2504, photograph #3929 in this archive.'],
];
$P[18702] = ['title' => ['Tom Mix'],
    'fields' => ['birthDate' => 'January 6, 1880', 'birthDateEdtf' => '1880-01-06', 'birthplace' => 'Mix Run, Pennsylvania', 'deathDate' => '1940', 'deathDateEdtf' => '1940', 'occupation' => 'Actor', 'birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective'],
    'must' => ['birchard1993_015' => ['relocated their operation from Las Vegas, N.M., to Newhall-Santa Clarita Valley in 1916', 'They erected a small Western movie town on the south side of Market Street between Newhall Avenue and today\'s Main Street', 'After Mix signed with William Fox in 1917 most filmmaking was done at the Fox (formerly Selig) lot at Edendale (Echo Park-Silver Lake) near Glendale, where Mix lived, although he continued to use Newhall occasionally through the mid-1920s', 'Surviving into the 21st Century are two bungalows at today\'s 24247 Main Street that were built in 1920', 'There is no evidence to suggest Mix ever set foot in Newhall prior to 1916'],
        'al3022' => ['Born in Mix Run, Penn., on Jan. 6, 1880, Tom Mix appeared in more than 300 films (counting "shorts") from 1909 to 1935', 'A part-time Newhall resident during that period, Mix lived across the street (probably on Walnut Street) from the Thibaudeau home, which was located at the southwest corner of Market Street and Newhall Avenue', 'said she observed Tom buying his sidekick "wonder horse" Tony on her family\'s property', 'In the late Teens, Mix established his most famous "Mixville" on Glendale Boulevard in the Silver Lake section of Los Angeles'],
        2143 => ['one early "Mixville" ran along Newhall Ave', 'When William S. Hart retired in 1925, his position as "King of Cowboys" was quickly appropriated by Tom Mix'],
        1452 => ['which was for some years the headquarters of Tom Mix and his early movie activities'],
        1438 => ['Tom Mix lived in a little outbuilding of the Pardee home for years, filming hereabout'],
        'lw2496' => ['Tom Mix stars in the 1918 Fox feature, "Western Blood," which was shot in the Santa Clarita Valley', 'he died in a 1940 car crash'],
        'lw2159a' => ['Tom Mix "jumps" over Beale\'s Cut in Newhall in the 1923 John Ford 5-reeler, "3 Jumps Ahead,"', 'insists the stunt was performed by Earl Simpson'],
        'aa2001' => ['driving recklessly through the streets of Newhall', 'Mix pleaded guilty. Powell fined him $50', 'Mix paid it on the spot', 'killed in a car wreck in Arizona'],
        'carey-bunse-ranch' => ['Tom Mix and Carey performed in Light of the Western Stars, shot on location in 1913', 'Mix may have first come to the area at the same time, reportedly in 1914 as a Selig Company actor']],
    'body' => [
        'Tom Mix, the cowboy star of silent Westerns, made Newhall his movie town from 1916 into the 1920s. He built one of his "Mixville" Western sets in downtown Newhall, lived there part of the time, and filmed in and around the valley.[1][2][3]',
        'In 1916 he moved his Selig Polyscope unit from Las Vegas, New Mexico, to Newhall and put up a small Western town on the south side of Market Street between Newhall Avenue and today\'s Main Street. After he signed with Fox in 1917 most of his filming moved to the Fox lot at Edendale, where he lived, and his best-known Mixville was later built in Silver Lake, but he kept using Newhall into the mid-1920s.[1][2] Two bungalows built about 1920 survive at 24247 Main Street.[1]',
        'His Newhall lodgings are remembered as a cabin behind the house later known as the Pardee home, which was for some years the headquarters of his early movie work, and as a place across the street from the Thibaudeau home at Market Street and Newhall Avenue. Gladys Thibaudeau Laney remembered watching him buy his horse Tony on her family\'s property.[4][5][2]',
        'The valley is in his films. Western Blood (1918) was shot here, and in John Ford\'s 3 Jumps Ahead (1923) Tony "jumps" Beale\'s Cut, although his biographer Robert S. Birchard says the stunt was performed by Earl Simpson.[6][7] In 1920 Mix pleaded guilty to driving recklessly through the streets of Newhall and paid a $50 fine on the spot.[8]',
        'Birchard finds no evidence that Mix was in Newhall before 1916; a 2001 survey of the Harry Carey ranch repeats reports that he filmed here as early as 1913 or 1914.[1][9] Born in Mix Run, Pennsylvania, on January 6, 1880, he appeared in more than 300 films from 1909 to 1935, took William S. Hart\'s place as "King of Cowboys" after Hart retired in 1925, and was killed in a car crash in Arizona in 1940.[2][3][6][8]',
    ],
    'notes' => [$LEGACY('birchard1993_015', '"Tom Mix and Selig Polyscope Crew Come to Newhall, 1916: News Reports," drawing on Robert S. Birchard, King Cowboy: Tom Mix and the Movies (1993)'), $LEGACY('al3022', '"Tom Mix On Location in Newhall, 1922," AL3022'), 'Jerry Reynolds, "59. Mixville," History of the Santa Clarita Valley, article #2143 in this archive.', '"Pardee House Has Seen Local History," article #1452 in this archive.', '"History of Downtown Newhall," article #1438 in this archive.', $LEGACY('lw2496', 'Leon Worden, "Tom Mix in \'Western Blood\' (Filmed in SCV, 1918)," LW2496'), $LEGACY('lw2159a', 'Leon Worden, "Tom Mix Jumps Over Beale\'s Cut (Or Not)," LW2159'), $LEGACY('aa2001', '"Actor Tom Mix Fined $50 for Reckless Driving in Newhall, 1920," AA2001'), $LEGACY('carey-bunse-ranch', 'JRP Historical Consulting Services, "Harry Carey Ranch (Clougherty Ranch)," Historic American Buildings Survey No. CA-2712, for the National Park Service, 2001')],
];
$P[15919] = ['title' => ['Harry Carey'],
    'fields' => ['birthDate' => 'January 16, 1878', 'birthDateEdtf' => '1878-01-16', 'birthplace' => 'The Bronx, New York City', 'deathDate' => 'September 1947', 'deathDateEdtf' => '1947-09', 'occupation' => 'Actor; rancher', 'birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective'],
    'must' => ['carey-bunse-ranch' => ['Harry Carey, Sr. was one of the first film actors to settle in the valley when he took over the homestead rights of a previous settler in 1916', 'He and his family lived there most of the time through the 1920s and 1930s, and they operated a tourist attraction and film set there as well', 'consistent with federal land records that show that Carey patented the land in 1925', 'Harry Carey Jr., was born in Saugus on May 16, 1921, in the Carey\'s first wood frame house on the ranch', 'the Careys hired about forty Navajo Indians to live and work at the Trading Post', 'The Indian employees made jewelry, raised sheep, and operated the stores and restaurant, "The Navahogan."', 'which was built in the early 1920s and successfully operated until 1928 when it was destroyed by flooding in the St. Francis Dam disaster', 'Carey creating the character Cheyenne Harry in John Ford\'s Straight Shooting, which may have been filmed on the Carey ranch property', 'John Ford used it as a backdrop in Straight Shooting', 'performing in well over 200 films between the 1910s and early 1940s', 'Harry Carey died in September 1947', 'Carey\'s only Oscar nomination, for Best Supporting Actor, came from such a role, as President of the Senate in Mr. Smith Goes to Washington (1939)', 'Carey married Olive Fuller Golden, also a native of New York and a film actress, in 1916'],
        'dc2101' => ['Actor Harry Carey (Sr.) acquired a homestead at the mouth of San Francisquito Canyon in 1916', 'The trading post washed away in the St. Francis Dam disaster of March 1928 and was not rebuilt', 'The ranch house was situated at a higher elevation and survived the flood, only to burn down in 1932', 'The Careys replaced it by building a Spanish adobe home, which they sold with the rancho in 1945', 'later became the centerpiece of the Tesoro Adobe Historic Park', 'Harry Carey was born Henry DeWitt Carey II on January 16, 1878 on 116th Street in the Bronx'],
        2143 => ['had its own post office in San Francisquito Canyon'],
        'lw3418' => ['John Ford\'s 1917 "Straight Shooting" (filmed in the Santa Clarita Valley with Harry Carey)']],
    'body' => [
        'Harry Carey, the Western film star, was one of the first film actors to settle in the Santa Clarita Valley. From 1916 he and his wife, the actress Olive Fuller Golden, made a ranch in San Francisquito Canyon above Saugus, where the family lived most of the time through the 1920s and 1930s and ran a trading post and film set.[1][2]',
        'He took over a previous settler\'s homestead claim at the mouth of the canyon in 1916 and patented the land in 1925.[1][2] The Harry Carey Trading Post, built in the early 1920s, had about forty Navajo people living and working there, making jewelry, raising sheep and running the stores and a restaurant, the Navahogan; for a while the place had its own post office.[1][3] The St. Francis Dam flood of March 1928 washed the trading post away, and it was not rebuilt. The ranch house, on higher ground, survived the flood and burned in 1932; the adobe that replaced it, sold with the ranch in 1945, is now the centerpiece of the Tesoro Adobe Historic Park.[2] Their son, the actor Harry Carey Jr., was born on the ranch on May 16, 1921.[1]',
        'He filmed here as well. John Ford\'s Straight Shooting (1917), in which Carey created the character Cheyenne Harry, was made in the valley, with Beale\'s Cut as a backdrop and perhaps scenes on the ranch itself.[4][1]',
        'Born Henry DeWitt Carey II in the Bronx on January 16, 1878, he made well over 200 films from the 1910s to the early 1940s, was nominated for an Academy Award for Mr. Smith Goes to Washington (1939), and died in September 1947.[2][1]',
    ],
    'notes' => [$LEGACY('carey-bunse-ranch', 'JRP Historical Consulting Services, "Harry Carey Ranch (Clougherty Ranch)," Historic American Buildings Survey No. CA-2712, for the National Park Service, 2001'), $LEGACY('dc2101', '"Harry Carey Sr. with Infant Dobe in Saugus, 1921," DC2101, Harry Carey Jr. Collection'), 'Jerry Reynolds, "59. Mixville," History of the Santa Clarita Valley, article #2143 in this archive.', $LEGACY('lw3418', 'Leon Worden, "Saugus Resident Harry Carey in \'Canyon of the Fools\' (1923)," LW3418')],
];
$P[285] = ['title' => ['Tiburcio Vasquez'],
    'correct' => ['birthDate' => ['April 7, 1835', 'April 10, 1835'], 'birthDateEdtf' => ['1835-04-07', '1835-04-10']],
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'contemporary', 'burialEvidence' => 'uncited'],
    'notesAdd' => [['His birth date', 'This record formerly gave his birth as April 7, 1835, with no source. Leon Worden gives April 10, 1835, citing John Boessenecker (2010), and notes that it is not August 11, the date John M. Glenn gave in 1974.'], ['His burial place', 'No source has been found for his burial place, given here as Santa Clara. The New York Tribune reported only that his body was given to his friends.']],
    'must' => ['vasquez011574glenn' => ['The infamous Tiburcio Vasquez made his appearance in a horse-stealing raid on a ranch on the Santa Clara River in Los Angeles County on July 15, 1857. It was the bandit\'s first official crime', 'Indicted in Los Angeles on August 11, 1857, he pleaded guilty and was sentenced to five years in San Quentin prison', 'It was from New Idria that the Vasquez gang launched one of its more bloody forays, the raid on Tres Pinos', 'After the raid Vasquez prevailed on Leiva to sell his ranch in La Cantua Canyon and move with the gang into southern California', 'Leiva then took his wife to Jim Heffner\'s ranch near Elizabeth Lake, a favorite hiding place of the Vasquez band', 'surrendered to Under-Sheriff W.W. Jenkins of Los Angeles at Lyon\'s Station and informed on Vasquez', 'A spirited gun fight followed but Vasquez and Chavez escaped unhurt', 'This is probably the gun battle said to have taken place at Vasquez Rocks in local folk tales. However, one source places it in Little Rock Creek Canyon[19] and another in Rock Creek Canyon in San Bernardino County', 'there can be no doubt that Vasquez operated extensively in the area around Elizabeth Lake and Soledad Canyon from the time of the Tres Pinos raid until his capture', 'near Cahuenga Pass on May 13, 1874', 'Vasquez apparently had a brother living at Soledad Canyon and was able to move freely about the area using the alias "Ricardo Cantuga."', 'it seems a safe assumption that at one time or another he was in the rocks that now bear his name as well as in nearby Vasquez Canyon', 'If Vasquez ever used the rocks as a hideout, it must have been for a very short time. He had access to too many ranches in the area for him to remain very long huddled in the rocks', 'Vasquez was hanged at San Jose on March 19, 1875, after being sent there for trial from Los Angeles', 'Vasquez was born August 11, 1835', 'Abdon Leiva', 'Leiva had a lovely wife, Rosaria', 'Cleovaro Chavez'],
        'nyt03201875' => ['The bandit Vasquez was executed to-day at San José', 'until the 14th of May last, when he was taken prisoner near Los Angeles', 'The body was given to his friends for interment'],
        'lw3137' => ['Tiburcio was born in the home on April 10, 1835', 'Not August 11. See Boessenecker 2010:13', '546 Dutra Street, Monterey']],
    'body' => [
        'Tiburcio Vasquez, the California bandit, gave his name to Vasquez Rocks. From the raid on Tres Pinos in August 1873 until his capture in May 1874 he ranged widely around Elizabeth Lake and Soledad Canyon, where a brother apparently lived, moving about under the name Ricardo Cantuga.[1]',
        'His first recorded crime was here too: a horse-stealing raid on a ranch on the Santa Clara River in Los Angeles County on July 15, 1857, which sent him to San Quentin.[1]',
        'After the bloody raid on Tres Pinos the gang moved south, and Jim Heffner\'s ranch near Elizabeth Lake became a favorite hiding place. When his lieutenant Abdon Leiva found Vasquez with his wife, Rosaria, Leiva gave himself up to the Los Angeles undersheriff and informed on him, and Vasquez and Cleovaro Chavez escaped unhurt from a gunfight with the posse that followed. Local tales put that fight at Vasquez Rocks; other accounts put it in Little Rock Creek Canyon or in San Bernardino County.[1] John M. Glenn, who wrote the county\'s history of the rocks in 1974, thought it a safe assumption that Vasquez was among them at one time or another, but if he hid there it was not for long: he had too many ranches to shelter him.[1]',
        'He was taken at a ranch near Cahuenga Pass in May 1874, on the 13th by Glenn\'s account and the 14th by a report of the time, was sent to San Jose for trial, and was hanged there on March 19, 1875.[1][2] He was born in Monterey on April 10, 1835.[3]',
    ],
    'notes' => [$LEGACY('vasquez011574glenn', 'John M. Glenn, "A History of Vasquez Rocks and Vicinity," Los Angeles County Department of Recreation and Parks, January 15, 1974'), $LEGACY('nyt03201875', '"Tiburcio Vasquez Hanged At San Jose," New York Tribune, March 20, 1875'), $LEGACY('lw3137', 'Leon Worden, "Tiburcio Vasquez Birthplace," LW3137')],
];
$P[303] = ['title' => ['Juventino del Valle'], 'replaceBody' => true,
    'correct' => ['birthDate' => ['1841 (born approximately six months before his parents\' January 1, 1842 marriage)', '1841'], 'birthDateEdtf' => [null, '1841'], 'birthplace' => ['Santa Barbara, California (probable)', '']],
    'fields' => ['deathDate' => '1919', 'deathDateEdtf' => '1919', 'birthEvidence' => 'retrospective', 'deathEvidence' => 'contemporary'],
    'notesAdd' => [['His dates', 'This record formerly gave his birth as "1841 (born approximately six months before his parents\' January 1, 1842 marriage)" and his birthplace as "Santa Barbara, California (probable)". The sources here give 1841 and no birthplace; no source for the rest has been found.']],
    'must' => ['lw3664' => ['Juventino (1841-1919) was the eldest child of Ygnacio del Valle', 'he served as ranch manager from 1862-1886, assuming more and more duties in the 1870s as his father inched closer to his death in 1880 (Triem & Stone 1996)', 'planted by Juventino del Valle circa 1870', 'Is this tree really only 50 years old?', 'signature black walnut tree of Rancho Camulos Museum that survived into the 21st Century'],
        293 => ['the section known as Camulos, near Piru along today\'s State Route 126, about 10 miles west of Interstate 5'],
        'lp_oxnardpresscourier061119' => ['Petition for the probate of the will of the late Juventino Del Valle, of the Camulos ranch', 'who passed away some days ago', 'one-fifth goes to the widow, one-fifth to the daughter Rose, one-fifth to the daughter Eliza, and one-fifth to the daughter, Mrs. John Kirby', 'The remaining fifth is to be divided among the widow and children of a son of the deceased, Juventino Jr.']],
    'body' => [
        'Juventino del Valle, the eldest child of Ygnacio del Valle, ran Rancho Camulos, the part of his father\'s land near Piru, about ten miles west of today\'s Interstate 5, as ranch manager from 1862 to 1886, taking on more as his father aged; Ygnacio died in 1880.[1][2]',
        'A 1996 historic resources report credits him with planting the ranch\'s great black walnut, which survived into the 21st century, about 1870. Leon Worden questions whether the tree is really as young as that.[1]',
        'He died in 1919, and his will divided his estate among his widow, his three daughters and the widow and children of his son Juventino Jr.[3]',
    ],
    'notes' => [$LEGACY('lw3664', 'Leon Worden, "Juventino del Valle, Black Walnut Tree, (5) Important Camulos Views, 1910s," LW3664'), 'Leon Worden, "Ygnacio del Valle," LW2052, the profile of person #293 in this archive.', $LEGACY('lp_oxnardpresscourier061119', '"Bulk of Del Valle Estate Left to Widow," Oxnard Press-Courier, June 11, 1919')],
];
$P[337] = ['title' => ['John Timothy Gifford'], 'replaceBody' => true,
    'correct' => ['birthplace' => [null, 'Cincinnati, Ohio']],
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective', 'burialEvidence' => 'retrospective'],
    'notesAdd' => [['His birthplace', 'The records disagree on his birthplace. The 1880 Census and his voter registrations give Ohio, the 1910 Census New York, and the 1920 Census Cincinnati, which the source here follows.']],
    'must' => ['ap0622' => ['John Timothy Gifford (Feb. 14, 1847 - Oct. 15, 1922) was Newhall\'s first railroad agent and telegraph operator', 'Born in Cincinnati, Ohio[1]', 'he\'s first known to us in 1871 when he was a line rider for the Western Union, covering the territory from Los Angeles to Lake Elizabeth', 'In 1875 he was placed in charge of the north-side crew that dug the Southern Pacific Railroad\'s 6,940-foot San Fernando train tunnel through Railroad Canyon', 'They met in the middle July 15, 1876', 'tapped out the news from Lyon\'s Station: "Daylight shines through the San Fernando Tunnel"', 'on Aug. 12, 1876', 'neighed long and loud his hearty greeting to the citizens of the Santa Clara Valley', 'The Southern Pacific Newhall Depot opened for business Sept. 6, 1876', 'at today\'s Bouquet Canyon Road and Magic Mountain Parkway', 'The Giffords\' home was a boxcar parked on a siding', 'Mabel, born Nov. 3, 1875, was the first child to live in Newhall', 'In January-February 1878 the townsfolk picked up sticks and replanted them at today\'s downtown Newhall', 'A bigger train station was set up at the northeast corner of Market Street and Railroad Avenue', 'moved into a new, board-and-batten home due east of the depot, across the tracks, on April 16, 1884', 'Gifford had taken the English-born Sarah Beckwith', 'the couple married in 1875 in San Diego', 'John Gifford retired as the SPRR\'s Newhall telegraph operator in 1912', 'He was simultaneously the local telegrapher for Wells, Fargo & Co.', 'Both Giffords are buried below an upright family headstone in the Garden of Pioneers at Eternal Valley Cemetery', 'almost the exact spot where John Gifford had used the telegraph 46 years earlier to announce the completion of the train tunnel', 'The 1880 Federal Census says John Gifford was born in Ohio', 'The 1910 Census says New York. The 1920 Census says Cincinnati'],
        'obituary_edwinegifford' => ['Died at Newhall, in this county, of scarlet fever, on December 2, 1885', 'will be remembered by many travelers and railroad men as the little boy who often could be seen rolling the large truck from the train into the depot office', 'the "little agent."']],
    'body' => [
        'John Timothy Gifford was Newhall\'s first railroad agent and telegraph operator, and the man who sent the news in 1876 that the Southern Pacific\'s San Fernando Tunnel had broken through.[1]',
        'He is first known in 1871 as a Western Union line rider between Los Angeles and Lake Elizabeth. In 1875 he took charge of the north-side crew digging the 6,940-foot tunnel through Railroad Canyon, and when the two crews met on July 15, 1876, he telegraphed from Lyon\'s Station: "Daylight shines through the San Fernando Tunnel." When the first locomotive came through, on August 12, he wired that the iron horse had "neighed long and loud his hearty greeting to the citizens of the Santa Clara Valley."[1]',
        'The Newhall depot opened on September 6, 1876, near today\'s Bouquet Canyon Road and Magic Mountain Parkway, and the Giffords lived in a boxcar on a siding; their daughter Mabel, born November 3, 1875, was the first child to live in Newhall. When the town moved to its present site early in 1878, a new station went up at Market Street and Railroad Avenue, and on April 16, 1884, the family moved into a board-and-batten house across the tracks, east of the depot.[1] Their son Edwin, remembered by travelers and railroad men as the "little agent" who rolled the big truck from the trains into the depot office, died of scarlet fever on December 2, 1885.[1][2]',
        'He married Sarah Beckwith, born in England, in San Diego in 1875. He was also the local telegrapher for Wells, Fargo & Co., and he retired from the railroad in 1912. Born in Cincinnati, Ohio, on February 14, 1847, he died on October 15, 1922, and is buried in the Garden of Pioneers at Eternal Valley Cemetery, close to where he had telegraphed the news of the tunnel.[1]',
    ],
    'notes' => [$LEGACY('ap0622', '"John & Sarah Gifford Home," AP0622'), $LEGACY('obituary_edwinegifford', '"Edwin E. \'Eddie\' Gifford, Son of John and Sarah Gifford, 1878-1885," obituary contributed by Tricia Lemon Putnam')],
];

/* ================================================================ checks */
$bad = []; $held = [];
foreach (array_keys($VERIFIED) as $name) { if (!$okFile($name)) { $bad[] = "$name: missing or not as verified against the manifest"; } }
foreach ($IMP as $k => $c) {
    $rec = Entry::find()->section($c['section'])->status(null)->legacyKey($k)->one(); $held[$k] = $rec?->id;
    if (count($prose($k, $c['from'], $c['to'])) < 2) { $bad[] = "$k: body not found"; }
    echo str_pad($k, 30) . ($rec ? "held as #{$rec->id}" : "import as {$c['section']}: " . count($prose($k, $c['from'], $c['to'])) . ' paragraphs') . PHP_EOL;
}
$read = function ($src) use ($ws, $txt) {
    if (is_string($src)) { return $txt($src); }
    $e = Entry::find()->id($src)->status(null)->one(); if (!$e) { return ''; }
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    return $ws($e->body . ' ' . (in_array('photoCaptionExt', $h, true) ? $e->photoCaptionExt : ''));
};
$plan = [];
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$p || !in_array($p->title, $c['title'], true)) { $bad[] = "#$id is not " . $c['title'][0]; continue; }
    foreach ($c['must'] as $src => $phrases) { $t = $read($src); foreach ($phrases as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "{$c['title'][0]}: " . (is_string($src) ? $src : "#$src") . ' does not read "' . mb_substr($ph, 0, 60) . '"'; } } }
    if (!empty($c['notesOnly'])) { $plan[$id] = ['done' => true]; echo str_pad($c['title'][0], 26) . "notes only\n"; continue; }
    $body = implode("\n\n", $c['body']); $cur = trim((string)$p->body); $done = $cur === trim($body);
    if ($cur !== '' && !$done && !(!empty($c['replaceBody']) && ($p->bodyAuthorship->value ?? '') === 'wordpress-import-unsourced')) { $bad[] = "{$c['title'][0]}: has a body this does not replace"; }
    foreach ($c['fields'] ?? [] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have !== '' && $have !== $v && $k !== 'occupation' && !str_ends_with($k, 'Evidence')) { $bad[] = "{$c['title'][0]}: $k is \"$have\", not \"$v\""; } }
    foreach ($c['correct'] ?? [] as $k => [$from, $to]) { $have = trim((string)$p->getFieldValue($k)); if ($have !== $to && $from !== null && $have !== $from) { $bad[] = "{$c['title'][0]}: $k is \"$have\", not \"$from\""; } }
    preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
    if (count($used) !== count($c['notes']) || max($used) > count($c['notes'])) { $bad[] = "{$c['title'][0]}: notes used " . json_encode(array_values($used)) . ' of ' . count($c['notes']); }
    if (preg_match('~\x{2014}~u', $body . implode('', $c['notes']) . json_encode($c['notesAdd'] ?? []))) { $bad[] = "{$c['title'][0]}: an em dash"; }
    $plan[$id] = ['body' => $body, 'done' => $done];
    echo str_pad($c['title'][0], 26) . ($done ? 'already written' : str_word_count($body) . ' words, ' . count($c['notes']) . ' notes') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ================================================================ writes */
$short = []; $ids = [];
foreach ($IMP as $k => $c) {
    if ($held[$k]) { $ids[$k] = $held[$k]; continue; }
    $e = new Entry(); $e->sectionId = $svc->getSectionByHandle($c['section'])->id; $e->setTypeId($svc->getEntryTypeByHandle($c['type'])->id); $e->title = $c['title'];
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    $vals = ['body' => implode("\n\n", $prose($k, $c['from'], $c['to'])), 'legacyKey' => $k, 'legacyUrl' => "/scvhistory/$k.htm", 'sourcePath' => "https://scvhistory.com/scvhistory/$k.htm", 'obitSubject' => [$c['subject']], 'obitLegacyUrl' => "/scvhistory/$k.htm"] + $c['fields'];
    $e->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($e)) { throw new \RuntimeException("$k: " . json_encode($e->getFirstErrors())); }
    $ids[$k] = $e->id; echo "imported $k as {$c['section']} #{$e->id}" . PHP_EOL;
}
$n = 0;
$noteRows = fn($e) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = [];
    if (!empty($c['notesAdd'])) { $rows = $noteRows($p); $add = false; foreach ($c['notesAdd'] as [$hd, $nt]) { if (!in_array($nt, array_column($rows, 'note'), true)) { $rows[] = ['heading' => $hd, 'position' => 'bottom', 'note' => $nt]; $add = true; } } if ($add) { $vals['editorNotes'] = $rows; } }
    if (empty($c['notesOnly'])) {
        foreach ($c['fields'] ?? [] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have === '' || ($k === 'occupation' && $have !== $v) || (str_ends_with($k, 'Evidence') && $have !== $v)) { $vals[$k] = $v; } }
        foreach ($c['correct'] ?? [] as $k => [$from, $to]) { if (trim((string)$p->getFieldValue($k)) !== $to) { $vals[$k] = $to; } }
        if (!empty($c['fullName']) && (string)$p->fullName !== $c['fullName']) { $vals['fullName'] = $c['fullName']; }
        if (!empty($c['alias'])) { $al = array_filter(array_map('trim', preg_split('~[;\n]~', (string)$p->personAliases))); if (!in_array($c['alias'], $al, true)) { $vals['personAliases'] = implode("\n", array_merge($al, [$c['alias']])); } }
        if (!$plan[$id]['done']) {
            $notes = array_map(fn($x) => preg_replace_callback('~\{OBIT:([\w-]+)\}~', fn($m) => (string)$ids[$m[1]], $x), $c['notes']);
            $vals += ['body' => $plan[$id]['body'], 'footnotes' => $fn($notes), 'bodyAuthorship' => 'editorial-2026'];
            $prov = trim((string)$p->recordProvenance . '; build_profiles_batch5.php, 3 Oct 2026', '; '); if (mb_strlen($prov) <= 255) { $vals['recordProvenance'] = $prov; }
        }
    }
    $vals = array_intersect_key($vals, array_flip($h));
    if ($vals) { $p->setFieldValues($vals); if (!$elements->saveElement($p)) { $short[] = "{$c['title'][0]} " . json_encode($p->getFirstErrors()); continue; } }
    $r = Entry::find()->id($id)->status(null)->one();
    $ok = !empty($c['notesOnly']) ? count(array_filter($c['notesAdd'], fn($x) => in_array($x[1], array_column($r->editorNotes ?? [], 'note'), true))) === count($c['notesAdd'])
        : (trim((string)$r->body) === trim($plan[$id]['body']) && ($r->bodyAuthorship->value ?? '') === 'editorial-2026');
    $ok ? $n++ : $short[] = $c['title'][0];
    echo ($ok ? 'OK    ' : 'SHORT ') . $r->title . ' ' . $r->url . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_profiles_batch5.php', $n, $short ? 'SHORT' : 'verified', 'the 25, batch 3, valley first: Fages, Mix, Carey, Vasquez, Juventino del Valle, Gifford');
if ($short) { throw new \RuntimeException('build_profiles_batch5: ' . implode(', ', $short)); }
