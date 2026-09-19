/**
 * Replaces a generated placeholder body on a place record with prose the corpus
 * supports.
 *
 * Twelve of the eighteen places carry a body of the form "X is a named place in
 * the Santa Clarita Valley historical archive." That is a placeholder written by
 * an importer, not a sentence anybody meant, and it is what a reader gets today
 * on Fort Tejon, Rancho Camulos and Vasquez Rocks.
 *
 * This fills them, one at a time, from sentences that exist in inventory/legacy.
 * Nothing is composed from outside the corpus and every entry names the articles
 * it draws on.
 *
 * It will only ever overwrite a body that still matches the placeholder exactly.
 * A body somebody has written, or already filled by an earlier run, is never
 * touched: the guard is the point of the script, not a precaution around it.
 *
 * All twelve are filled here, in order of how much the corpus carries: Heritage
 * Junction first, then Rancho Camulos, Fort Tejon, Vasquez Rocks, Lang Station,
 * the Estancia, the Ridge Route, Melody Ranch, the Harry Carey Ranch, Saugus
 * Speedway, Lake Hughes and Six Flags Magic Mountain.
 *
 * Where the corpus is thin the entry is short. Lake Hughes gets one sentence,
 * because one sentence is what Reynolds wrote about it, and a paragraph that
 * reached would be worse than a line that does not.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fill_place_stub_bodies.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* The exact shape an importer left behind. Anything else is somebody's writing. */
$STUB = '~^.{0,60}\s+is a named place in the Santa Clarita Valley historical archive\.?$~u';

$FILL = [
    'heritage-junction' => [
        'body' => "A section of William S. Hart Park in Newhall where the Santa Clarita Valley "
            . "Historical Society has gathered buildings saved from demolition and moved to the "
            . "site. The Saugus Train Station stands there as a museum, joined over the years by "
            . "the Kingsburry House, moved in July 1990; a two-story Victorian dating from 1893 "
            . "that stood surrounded by the Magic Mountain parking lot until it was moved in the "
            . "same year; the house of constable Ed Pardee, who ran the livery stable; and a "
            . "structure bought by the Pacific Telephone Company in 1946, later used by the Santa "
            . "Clarita Valley Boys Club and the Newhall-Saugus-Valencia Chamber of Commerce, "
            . "moved again in 1992. The adobe bricks of Martha Mitchell's home in Soledad Canyon, "
            . "the valley's first school house, were moved here in 1986 and the school "
            . "reassembled. The Historical Society operates the park rent-free on Hart Park land. "
            . "It is open to visitors at weekends.",
        'from' => [
            'Jerry Reynolds, "71. Requiem": the Saugus Station, Martha Mitchell\'s adobe, '
                . '"Heritage Junction is open for visitors on weekends"',
            'Jerry Reynolds, "49. Reflections": the 1893 Victorian moved from the Magic Mountain '
                . 'parking lot in 1990',
            'Jerry Reynolds, "47. Dry Colony": the Pacific Telephone structure, 1946 and 1992',
            'Jerry Reynolds, "55. Fights and Feuds": Ed Pardee, constable and livery stable keeper',
            'Jerry Reynolds, "About the Namesakes of the Kingsburry House": the Kingsburry House, '
                . 'moved July 1990',
            'A.B. Perkins, "Tales of Lang and Soledad", Editor\'s Note: the adobe bricks moved in 1986',
            'Leon Worden, "A Third-Grade History Cheat Sheet": the Saugus Train Station Museum and '
                . '"buildings that were saved from the bulldozer"',
            'Leon Worden, "Preservation Group Hammers City": the Society operating the park rent-free '
                . 'on a section of Hart Park',
        ],
    ],

    'rancho-camulos' => [
        'body' => "The del Valle family seat, and the westernmost portion of the Rancho San Francisco "
            . "grant. Don Ignacio del Valle retired here after serving as county registrar, alcalde of "
            . "Los Angeles and a member of the state Assembly; in 1861 a twenty-room adobe rose on the "
            . "right bank of the Santa Clara, long and low in the Californio style, U-shaped around a "
            . "patio with a mission-style fountain. The family chapel stood across the flower garden, "
            . "and beside it hung three bells dating from Spanish rule. When Thomas R. Bard bought the "
            . "rest of Rancho San Francisco in 1865, Camulos was whittled from the 16,599 acres "
            . "inherited from Don Antonio to 1,340, enough to carry on: grapes, citrus and walnuts "
            . "were planted, wine was pressed, and sheep and cattle roamed the estate. Don Ignacio and "
            . "his second wife, Isabel Varela, raised eleven children here and a number of orphans. He "
            . "died on March 30, 1880 and was buried in the family crypt a mile north of the house, "
            . "under a tall cross on a high hill. Helen Hunt Jackson arrived on January 23, 1882, "
            . "spent a few hours taking notes and interviewing the staff, and made Doña Isabel the "
            . "model for Señora Moreno in Ramona. The novel was an instant best seller and Camulos "
            . "became, in Reynolds's phrase, perhaps the best known rancho in California.",
        'from' => [
            'Jerry Reynolds, "29. Barbaric Elegance": the 1861 adobe, the fountain, the chapel and bells, '
                . 'the Bard purchase and the reduction to 1,340 acres, Isabel Varela and the eleven '
                . 'children, Don Ignacio\'s death and burial, Jackson\'s visit of January 23, 1882 and '
                . 'Señora Moreno',
            'A.B. Perkins, "Rancho San Francisco: A Study of a California Land Grant": Camulos as the '
                . 'westernmost portion of the grant, and "As the background of \'Ramona,\' Camulos may '
                . 'have been the best known rancho of California"',
            'A.B. Perkins, "2. Rancho San Francisco": Camulos as an integral part of Rancho San '
                . 'Francisco and the seat of the del Valle hacienda',
        ],
    ],
    'fort-tejon' => [
        'body' => "An army post established atop Grapevine Pass on August 10, 1854, at the northern "
            . "edge of the country this archive covers. Lieutenant Castor laid out barracks, officers' "
            . "quarters, storehouses and parade grounds. The road south to San Fernando was improved "
            . "with an appropriation of eight thousand dollars from the Los Angeles Board of "
            . "Supervisors as part of the Great Southern, or OxBow, route, and the fort became the far "
            . "end of the stage and freight road that ran through the Santa Clarita Valley: both "
            . "Beale's Cut and Lyon's Station existed to serve traffic bound for it. The earthquake of "
            . "1857 was centred here and tossed the barracks about like children's toys.",
        'from' => [
            'Jerry Reynolds, "22. Breaching the Pass": the establishment atop Grapevine Pass on August 10',
            'Jerry Reynolds, "23. The Beasts of Tejon": Lieutenant Castor laying out the post',
            'Jerry Reynolds, "25. Rest Stop": the eight thousand dollar road appropriation and the '
                . 'Great Southern or OxBow route',
            'Jerry Reynolds, "26. Twilight of the Dons": the earthquake centred at Fort Tejon and the '
                . 'barracks "tossed around like children\'s toys"',
        ],
    ],
    'vasquez-rocks' => [
        'body' => "A great sandstone outcropping in Agua Dulce, named for the bandit Tiburcio Vasquez, "
            . "who is said to have used it as a hideout. It is on the National Register of Historic "
            . "Places, but for the rock art rather than the outlaw: several panels in the county park "
            . "carry curious figures resembling lizards with rake-like hands, among the finest "
            . "collections of Native American rock art in the state. Pot holes and small caves at the "
            . "site were used to hold calcined bones and beads, indicating cremation. The painter "
            . "Claude Ellis lived in a packing-crate house at the base of the rocks, painting faces "
            . "and small landscapes on the bedrock until his death in 1941.",
        'from' => [
            'Jerry Reynolds, "71. Requiem": on the National Register "not because it was the hideout of '
                . 'the notorious outlaw Tiburcio Vasquez, but because it sports one of the finest '
                . 'collections of Native American rock art in the state"',
            'Jerry Reynolds, "5. Tribal Relics": the lizard-like figures with rake-like hands',
            'Jerry Reynolds, "56. Eureka": Claude Ellis and the packing-crate house, to his death in 1941',
            'A.B. Perkins, "1. Early Inhabitants": the pot holes and caves holding calcined bones and beads',
        ],
    ],
    'lang' => [
        'body' => "The Southern Pacific station in Soledad Canyon where the golden spike was driven on "
            . "September 5, 1876, joining San Francisco and Los Angeles by rail. Company president "
            . "Charles Crocker was handed a silver mallet and a spike made by a Los Angeles jeweller "
            . "from ore out of the San Gabriel Mountains. Before the railroad the site was a stop for "
            . "mule teams, as many as twenty to a wagon, hauling high-grade ore down the canyon. The "
            . "station was the last remnant of a community, ranch and health spa that dominated "
            . "Soledad Canyon for a hundred years; the Southern Pacific demolished it in 1971. The "
            . "spike site is a quarter-mile east of the Shadow Pines exit off State Route 14, in what "
            . "is now Canyon Country.",
        'from' => [
            'A.B. Perkins, "4. Early Transportation": the driving of the golden spike at Lang',
            'A.B. Perkins, Editor\'s Notes: the date of September 5, 1876, Crocker, and the site a '
                . 'quarter-mile east of the Shadow Pines exit',
            'Jerry Reynolds, "39. Ribbons of Steel": the silver mallet and the spike made from San '
                . 'Gabriel Mountains ore',
            'Jerry Reynolds, "33. Song of the Soledad": the mule teams, the community, ranch and health '
                . 'spa, and the 1971 demolition',
        ],
    ],
    'estancia' => [
        'body' => "A mission outpost at Castaic Junction, built by Father Dumetz and described by "
            . "Perkins as the first building in the valley. It served Mission San Fernando's interest "
            . "in the grazing land to the north, and the Rancho San Francisco was run from it before "
            . "the del Valle family moved the seat to Camulos. Perkins writes of it as an asistencia, "
            . "a sub-mission; a webmaster's note added in 2006 records that modern archaeologists do "
            . "not believe the estancia was ever raised to that status. The ruins were still "
            . "identifiable when treasure-hunting expeditions searched the site.",
        'from' => [
            'A.B. Perkins, "2. Rancho San Francisco": Fr. Dumetz building it as "the first building in '
                . 'our valley area", the Mission\'s interest in an outpost on Rancho San Francisco, the '
                . 'rancho being run first from the Asistencia and then from Camulos, the ruins, and the '
                . '2006 webmaster\'s note that "modern archaeologists do not believe the estancia '
                . '(mission outpost) at Castaic Junction was elevated to asistencia (sub-mission) status"',
        ],
    ],
    'ridge-route' => [
        'body' => "The road north over the mountains, built to move motorists and water through the "
            . "Santa Clarita Valley quickly rather than to serve the people living in it. Together "
            . "with the Mint Canyon road and the Newhall Tunnel it ended the valley's isolation. It "
            . "brought few permanent residents but cut the travelling time to the markets of Los "
            . "Angeles, and businesses grew up along it to serve the traffic: at Castaic, Sam Parson "
            . "got a post office established in his general store on April 3, 1917.",
        'from' => [
            'A.B. Perkins, "6. Oil and Newhall": the Ridge Route, the Mint Canyon road and the Newhall '
                . 'Tunnel ending the isolation of the area',
            'Jerry Reynolds, "52. Servicing the Traveler": the road designed to move water and motorists '
                . 'through rather than to benefit local residents, the cut in travel time, and Sam '
                . 'Parson\'s post office of April 3, 1917',
        ],
    ],
    'melody-ranch' => [
        'body' => "The best known film studio in the Santa Clarita Valley, a standing Western town "
            . "east of the present Placerita Canyon Road exit off State Route 14. Ernie Hickson came "
            . "to California in 1922 to work as an assistant technical director for the film maker "
            . "Trem Carr, and in 1930 built an entire Western town at Carr's Rancho Placeritos. Carr "
            . "was later forced to sell. The place became known as the Monogram Ranch and then, under "
            . "Gene Autry, as Melody Ranch. Buck Jones, Ken Maynard, Bob Steele, Tom Tyler, Johnny "
            . "Mack Brown and Harry Carey all worked its street.",
        'from' => [
            'Jerry Reynolds, "60. Melody": Hickson\'s arrival in 1922, the Western town built at Rancho '
                . 'Placeritos in 1930, Carr forced to sell, and "the great Monogram western town that '
                . 'came to be known as Gene Autry\'s Melody Ranch"',
            'Jerry Reynolds, "60. Melody" and "62. Suddenly Searchlight": the actors who filmed there '
                . 'and Hickson at the Monogram Ranch',
        ],
    ],
    'harry-carey-ranch' => [
        'body' => "The ranch, trading post and part-time film set of the actor Harry Carey, seven "
            . "miles up San Francisquito Canyon Road from the present Copper Hill Drive. Carey and "
            . "Hoot Gibson both settled in the valley after appearing in Straight Shooting, which was "
            . "shot at Beale's Cut and other local landmarks. The ranch and trading post were taken "
            . "out by the collapse of the St. Francis Dam in March 1928. Carey died in 1947, and the "
            . "ranch changed hands several times before the Clougherty family bought it in the early "
            . "1950s.",
        'from' => [
            'Jerry Reynolds, "59. Mixville": Straight Shooting shot at Beale\'s Cut, Carey and Gibson '
                . 'settling locally, and Carey owning "a ranch, trading post and part-time movie set"',
            'Leon Worden, "Future Destination: Historic Sites": the location seven miles up San '
                . 'Francisquito Canyon Road from Copper Hill Drive',
            'Leon Worden, "Spooky Happenings at Ruiz Cemetery": the dam flood taking out "the popular '
                . 'Harry Carey Ranch and Trading Post"',
            'Leon Worden, "Wal-Mart\'s in Valencia": Carey\'s death in 1947 and the Clougherty purchase',
        ],
    ],
    'saugus-speedway' => [
        'body' => "A rodeo arena before it was a speedway. Roy Baker bought the forty-acre tract east "
            . "of Bouquet Junction in 1923 and built the arena in 1924. The cowboy actor Hoot Gibson "
            . "bought it in 1930, and it was later taken over by William Bonelli, a professor of "
            . "economics at Occidental College, whose family subdivided their Seco Canyon holdings in "
            . "1947 to build the valley's first tract housing.",
        'from' => [
            'Jerry Reynolds, "59. Mixville": Baker\'s purchase in 1923, the arena built in 1924, Hoot '
                . 'Gibson\'s purchase of the forty-acre tract in 1930, and Bonelli',
            'Jerry Reynolds, "58. Pistoleros": the arena identified as "Saugus Speedway today"',
            'Jerry Reynolds, "65. Transitions": the Bonelli family subdividing Seco Canyon in 1947',
        ],
    ],
    'lake-hughes' => [
        'body' => "Formerly West Elizabeth, renamed for Patrick Hughes, who trailed a flock of sheep "
            . "down from the San Joaquin Valley in 1873 and built a small house on the shore.",
        'from' => [
            'Jerry Reynolds, "30. The North Forty": "In 1873 Patrick Hughes trailed a flock of sheep '
                . 'down from the San Joaquin Valley and built a small house on the shores of West '
                . 'Elizabeth, which is now Lake Hughes."',
        ],
    ],
    'magic-mountain' => [
        'body' => "The best known and most visited landmark in the Santa Clarita Valley. The Newhall "
            . "Land and Farming Company, expert in petroleum, wheat, cattle and new towns but not in "
            . "entertainment, partnered with Sea World, Inc. and engaged Randall Duell and Associates "
            . "to design a theme park on hillsides the Spanish had called the sterile hills. Magic "
            . "Mountain opened on Memorial Day, May 29, 1971 with thirty-three rides, among them the "
            . "Gold Rusher roller coaster and the 384-foot Sky Tower, and with more than seven "
            . "thousand trees and thirty thousand shrubs planted on the bare slopes. From five hundred "
            . "employees on opening day it became the valley's largest employer. Newhall Land sold the "
            . "park to the Six Flags Corporation in 1979; Time Warner bought Six Flags in 1990, and "
            . "Hurricane Harbor was added in 1995.",
        'from' => [
            'Jerry Reynolds, "67. Magic": the Newhall Land partnership with Sea World and Randall Duell '
                . 'and Associates, the opening of May 29, 1971, the thirty-three rides, the Gold Rusher, '
                . 'the 384-foot Sky Tower, the planting on the "sterile hills", the growth from five '
                . 'hundred employees, the 1979 sale to Six Flags, the 1990 Time Warner purchase and '
                . 'Hurricane Harbor in 1995',
        ],
    ],
];

$elements = Craft::$app->getElements();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$filled = 0; $blocked = 0;

foreach ($FILL as $slug => $spec) {
    $e = \craft\elements\Entry::find()->section('places')->slug($slug)->status(null)->one();
    if (!$e) { echo $slug . ': no place record' . PHP_EOL; continue; }

    $current = trim((string)$e->body);
    echo PHP_EOL . $e->title . '  (#' . $e->id . ')' . PHP_EOL;

    if (!preg_match($STUB, $current)) {
        echo '  BLOCKED: the body is not the placeholder any more, so it is left alone.' . PHP_EOL;
        echo '  it reads: "' . mb_substr($current, 0, 90) . '…"' . PHP_EOL;
        $blocked++;
        continue;
    }

    echo '  was:  "' . $current . '"' . PHP_EOL;
    echo '  now:  ' . mb_substr($spec['body'], 0, 104) . '…' . PHP_EOL;
    echo '  ' . strlen($spec['body']) . ' characters, drawn from:' . PHP_EOL;
    foreach ($spec['from'] as $f) { echo '      ' . $f . PHP_EOL; }
    $filled++;

    if ($APPLY) {
        $e->setFieldValue('body', $spec['body']);
        echo '  ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
    }
}

/* ------------------------------------------------- the rest of the same job */

$remaining = [];
foreach (\craft\elements\Entry::find()->section('places')->status(null)->orderBy('title asc')->limit(null)->all() as $e) {
    if (isset($FILL[$e->slug])) { continue; }
    if (preg_match($STUB, trim((string)$e->body))) { $remaining[] = $e->title; }
}

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
echo 'bodies that would be filled: ' . $filled . PHP_EOL;
echo 'blocked because somebody had written one: ' . $blocked . PHP_EOL;

if ($remaining) {
    echo PHP_EOL . 'STILL PLACEHOLDERS, ' . count($remaining) . '. Same job, not yet done.' . PHP_EOL;
    echo 'Each needs somebody to read the corpus passages and write the paragraph;' . PHP_EOL;
    echo 'a script cannot compose these, it can only refuse to invent them.' . PHP_EOL;
    foreach ($remaining as $r) { echo '  ' . $r . PHP_EOL; }
}

if (!$APPLY) { echo PHP_EOL . 'nothing written.' . PHP_EOL; }
