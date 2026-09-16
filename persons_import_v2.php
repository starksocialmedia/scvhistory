$svc = Craft::$app->getEntries();

// Leon Worden
$existing = \craft\elements\Entry::find()->section('persons')->slug('leon-worden')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Leon Worden\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Leon Worden';
$entry->slug = 'leon-worden';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Contemporary (2010–Present)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1960-1969')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1970-1979')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1980-1989')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1990-1999')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('2000-2009')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('2010-2019')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('2020-Present')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('leon-worden-cowboy-hat.webp')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Early Life &amp; Beginnings Leon John Worden II was born in December 1962 in New Jersey to Constance Alice and Harvey Edward Worden — arriving into a world being reshaped by satellite communications, the same month Telstar I launched and orbited the Earth. It was a fittingly symbolic start for a man who would spend his life dedicated to communication. By eighteen months, his mother was already noting in his baby book: "He says everything, talks incessantly and in full sentences." By age two, he had taught himself to write every letter of the alphabet. His love of books and reading came early and never left. Coming to Santa Clarita When Leon turned eight, the Worden family relocated to Santa Clarita, where his mother Connie became a prominent community activist — championing road improvements, toxic waste cleanup, and civic causes that earned her the 1975 SCV Woman of the Year honor. From her, Leon learned both the art of volunteering and the deep value of service. He became a Boy Scout, a Red Cross volunteer, and a regular helper at Boys &amp; Girls Club auctions. A Young Collector, Musician &amp; Historian Among Leon\'s early passions was numismatics — coin collecting. What began as a hobby grew into a nationally recognized pursuit. He wrote for Coinage magazine (the largest U.S. numismatic monthly) and won multiple Numismatic Literary Guild awards, including Best Numismatic Article in Any Medium, Worldwide. Music was another thread running through his youth. Leon played piano and clarinet before moving to alto saxophone at Placerita Junior High and Hart High School. In 1977, he performed with the Hart Chieftains at the Hollywood Bowl when the group won the Battle of the Bands. His love of jazz ran deep — as a boy he attended performances by Ella Fitzgerald and Count Basie, and attended workshops with Benny Goodman and Dizzy Gillespie. His passion for history ignited just as early. At age seven, Leon accompanied his mother on an archaeological dig at a site that would soon be submerged beneath Castaic Lake. A beginner\'s stroke of luck yielded Native American mortars, pestles, and arrowpoints — and launched a lifelong dedication to indigenous history and cultural preservation. Journalist, Editor &amp; Community Voice After graduating from UCLA, Leon joined The Signal newspaper, where he held positions as a columnist, special sections editor, city editor, and senior editor, and eventually served on the editorial board. Over the years, he earned awards from the Associated Press, the California Newspaper Publishers Association, and the National Newspaper Association. His hundreds of articles and editorials consistently championed the betterment of the Santa Clarita community. In 1994, Leon established the Old Town Newhall Gazette, a publication dedicated to the history and current happenings of downtown Newhall — one of his enduring passions. He remained editor of The Signal until 2007, when he stepped away to focus on restructuring SCVTV. Preserving Santa Clarita\'s History Recognizing that the community needed a dedicated historical resource, Leon built and managed SCVHistory.com — a sweeping, continually growing digital archive of Santa Clarita history, filled with photographs, first-person accounts, and hundreds of original articles he wrote himself. Without his initiative, no such resource would exist. Leon has been a longtime member — and past president — of the SCV Historical Society, helping guide the organization\'s direction and mission. In 1995, he became a founding member of the Friends of Mentryville, a volunteer group dedicated to preserving the historic oil town and compiling its historical record. In 1997, he became a founding member of the City of Santa Clarita\'s Newhall Redevelopment Committee, serving as chairman for many years until the committee was dissolved by state law in 2012. The Fourth of July Parade Few community traditions carry more meaning for Leon than Newhall\'s Fourth of July Parade. He incorporated the Santa Clarita Parade Committee and has served as chairman of its board since the early 1990s. Under his leadership, the parade has grown into one of the largest Independence Day celebrations in the country — drawing roughly 20,000 attendees annually while retaining its cherished hometown character. SCVTV — President &amp; CEO Leon\'s most expansive undertaking has been his work with SCVTV. Arriving at the station fifteen years ago to pitch a history show, he found a do-it-yourself operation and rose to meet it — quickly mastering television production and beginning to shoot, edit, and create programs that captured vital Santa Clarita oral histories. He also launched Newsmaker of the Week, a current-events interview program with more than 400 episodes aired to date. Today, Leon serves as President and CEO of SCVTV, the 501(c)(3) nonprofit that operates Santa Clarita Valley\'s public television channels and delivers multimedia services to the City of Santa Clarita, College of the Canyons, the William S. Hart Union High School District, and more than 120 local nonprofit organizations. The channel produces original programming — some of which has been nominated for Emmy Awards — serving schools, arts organizations, and civic groups across the valley. He additionally manages SCVNews.com, SCVHistory.com, and associated daily newsletters that keep residents informed about local news, events, and history. Leon also serves on the Hart School District\'s Measure SA Bond Oversight Committee, the CTE Advisory Group, the Friends of Hart Park board, and the Rancho Camulos Museum board. Family &amp; Life on the Road Leon became a family man in 2000 when he and Wilma Worden welcomed their son, John Jake Worden. A devoted father, Leon has made a tradition of summer road trips with Jake — traveling America\'s back roads, collecting rocks and minerals, and exploring local history along the way. Their adventures, shared through photos on social media, reflect the same curiosity and love of discovery that have defined Leon\'s entire life. Legacy Born in the month that global satellite communications began, Leon Worden has spent more than half a century ensuring that the story of Santa Clarita — its people, its places, and its past — is never lost. He was named the 2015 SCV Man of the Year in recognition of a lifetime of journalism, preservation, civic leadership, and service to the community he has called home since 1970.',
    'fullName' => 'Leon Worden',
    'birthDate' => 'December 1962',
    'birthplace' => 'New Jersey, USA',
    'deathDate' => '',
    'burialPlace' => '',
    'occupation' => 'President & CEO, SCVTV; Journalist; Historian; Civic Leader; Television Producer',
    'authorBio' => 'Leon Worden is a Santa Clarita Valley journalist, historian, and civic leader who has dedicated his career to documenting and preserving the history of the community he has called home since 1970. A UCLA graduate and award-winning journalist, he served as editor of The Signal newspaper, founded SCVHistory.com, and today serves as President and CEO of SCVTV, the nonprofit public television service for the Santa Clarita Valley. Named the 2015 SCV Man of the Year, Worden remains one of the valley\'s most tireless advocates for local history, civic engagement, and community media.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Leon Worden\n";
} else {
    echo "FAILED: Leon Worden - " . json_encode($entry->getErrors()) . "\n";
}

// Jerry Reynolds
$existing = \craft\elements\Entry::find()->section('persons')->slug('jerry-reynolds')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Jerry Reynolds\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Jerry Reynolds';
$entry->slug = 'jerry-reynolds';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Contemporary (2010–Present)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1970-1979')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1980-1989')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1990-1999')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('jerry-reynolds.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Born Gerald G. Reynolds in Torrance, California, on July 16, 1937, Jerry Reynolds became the Santa Clarita Valley\'s most important and beloved local historian. He studied art history at Long Beach State College and worked as a private investigator before landing what he considered his dream job — a tour guide at William Randolph Hearst\'s castle at San Simeon. He ultimately built a career with the California Department of Water Resources, serving as director of the Castaic Lake Visitors Center and later the Vista del Lago Visitors Center at Lake Pyramid. Reynolds arrived in Newhall in 1971 with his wife, Myrna, and their three sons, James, David, and Jason. He quickly stepped into the role vacated by the aging A.B. Perkins — the valley\'s original town historian — collecting historical photographs and committing old-timers\' oral histories to paper before they were lost forever. By the time of Perkins\' death in 1977, Reynolds had fully assumed the mantle of valley historian. He helped organize the Santa Clarita Valley Historical Society and served as its first and only museum curator until his death. He wrote for The Signal newspaper off and on for two decades, producing a complete series of historical columns in the mid-1970s and mid-1980s. His landmark book, Santa Clarita: Valley of the Golden Dream , was published in 1992 by the Santa Clarita Valley Chamber of Commerce and remains the most complete history of the valley ever written. Other published works include A Heritage to Keep (1976) and Pico Canyon Chronicles (1985). Jerry Reynolds died on February 26, 1996, from cancer at the age of 58. As Leon Worden wrote in the preface to the digital edition of his work: "A part of him, like our history, lives forever."',
    'fullName' => 'Jerry Reynolds',
    'birthDate' => 'July 16, 1937',
    'birthplace' => 'Torrance, California',
    'deathDate' => 'February 26, 1996',
    'burialPlace' => 'Eternal Valley Memorial Park, Newhall, California',
    'occupation' => 'Historian, Author, Museum Curator, Private Investigator',
    'authorBio' => 'Gerald G. "Jerry" Reynolds (1937–1996) was the Santa Clarita Valley\'s preeminent local historian, whose decades of research and writing established the documentary foundation for all subsequent study of the valley\'s history. A longtime Newhall resident, he helped organize the Santa Clarita Valley Historical Society, served as its founding museum curator, and contributed historical columns to The Signal newspaper for over two decades. His landmark 1992 volume, *Santa Clarita: Valley of the Golden Dream*, remains the most comprehensive history of the Santa Clarita Valley ever written.',
    'personLegacyUrl' => '/scvhistory/lw2184.htm',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/14716645/jerry-hugh-reynolds',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Jerry Reynolds\n";
} else {
    echo "FAILED: Jerry Reynolds - " . json_encode($entry->getErrors()) . "\n";
}

// Henry Mayo Newhall
$existing = \craft\elements\Entry::find()->section('persons')->slug('henry-mayo-newhall')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Henry Mayo Newhall\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Henry Mayo Newhall';
$entry->slug = 'henry-mayo-newhall';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Railroad &amp; Oil Era (1876–1910)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Saugus')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('henry-mayo-newhall-2.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Born May 13, 1825, in the industrial town of Saugus, Massachusetts, Henry Mayo Newhall sought his fortune in the California Gold Rush of 1849. He failed as a miner but succeeded as an auctioneer in brawling San Francisco. A keen businessman and shrewd investor, Newhall parlayed his auctioneering fortune into an even greater fortune in railroads, ultimately becoming one of the founders of the Southern Pacific Railroad Company. Between 1872 and 1875, Newhall purchased five of the old Mexican land-grant ranchos throughout California, including 46,460 acres of the 48,000-acre Rancho San Francisco, which covered the western half of the Santa Clarita Valley and eastern Ventura County. He sold a railroad right-of-way to Southern Pacific for a dollar, and a plot of land for another dollar, and a town sprang up bearing his name. Two miles to the north, a second town bore the name of his birthplace. The Rancho San Francisco was used for farming and ranching during Newhall\'s lifetime. Newhall experimented with a wide variety of crops, from corn and alfalfa to sugar cane and citrus fruits. The staple, however, was wheat, which was harvested with great success until the early 1880s, when the climate worsened. Newhall split his time between San Francisco, where he tended to the business of his H.M. Newhall &amp; Co. auction house, and the Rancho San Francisco, where he planned to focus his attention after 1880. In the summer of 1880, however, while attending a Knights Templar event in an encampment in Chicago, he contracted malaria, which took the form of blood poisoning. He was never quite the same again. In March of 1882, he was thrown from his horse while riding on the Rancho and fell ill from the poison oak virus. He was taken by train to San Francisco and diagnosed with erysipelas, from which he died on March 13 at the age of 56. On June 1, 1883, H.M. Newhall\'s five sons incorporated the family-owned Newhall Land and Farming Company. Agriculture, cattle ranching, and vineyards in Central California remained the company\'s focus well into the 20th Century, even after oil was discovered on the Rancho San Francisco (by then called the Newhall Ranch) in December of 1936. In the mid-1950s, however, change came very rapidly as the company went public and its directors took note of America\'s great westward migration. The New Town of Valencia — designed as a self-contained community where home, employment, schools, recreation, shopping, and the outdoors interrelate — was born in 1965 and was still growing thirty years later. Citrus orchards and vineyards continue to produce revenue for the company, but housing is by far its biggest concern today as The Newhall Land and Farming Company develops New Towns in California and Arizona. Information from "A California Legend: The Newhall Land and Farming Company" by Ruth Waldo Newhall (1992).',
    'fullName' => 'Henry Mayo Newhall',
    'birthDate' => 'May 13, 1825',
    'birthplace' => 'Saugus, Massachusetts',
    'deathDate' => 'March 13, 1882',
    'burialPlace' => 'Cypress Lawn Memorial Park Colma, San Mateo County, California',
    'occupation' => 'Auctioneer; Land Developer; Railroad Investor',
    'authorBio' => 'Henry Mayo Newhall (1825–1882) was a Massachusetts-born entrepreneur who became one of the most consequential figures in early California history. After failing as a Gold Rush miner, he built a fortune as a San Francisco auctioneer before investing heavily in railroads, becoming a founder of the Southern Pacific Railroad. Between 1872 and 1875 he purchased five Mexican land grant ranchos throughout California, including 46,460 acres of Rancho San Francisco — the western half of what is now the Santa Clarita Valley. The town of Newhall bears his name; the town of Saugus bears the name of his Massachusetts birthplace.',
    'personLegacyUrl' => '/scvhistory/ap1335a.htm',
    'personGraveUrl' => 'http://findagrave.com/memorial/5308/henry_mayo-newhall',
    'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Henry_Newhall',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Henry Mayo Newhall\n";
} else {
    echo "FAILED: Henry Mayo Newhall - " . json_encode($entry->getErrors()) . "\n";
}

// Tiburcio Vasquez
$existing = \craft\elements\Entry::find()->section('persons')->slug('tiburcio-vasquez')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Tiburcio Vasquez\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Tiburcio Vasquez';
$entry->slug = 'tiburcio-vasquez';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Soledad Canyon')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('tiburcio-vasquez.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'A Brief Sketch of the Notorious Bandit. Vasquez is now busily engaged in chronicling his own history, by giving it in detail to all who visit him. He was born in Monterey County, in this State, August 11th, 1835 [sic: April 7*], and is therefore 39 years of age. He has three brothers and two sisters living. He has never married, but has one child in this county. He attended school in Monterey County, and can read and write. He became first incensed against Americans, and was led into fights, which placed him under the ban of the law, through jealousy at fandangos, in early days, when the Americans would be more successful with the señoritas than his own class. After an arrest for one of these fights, he gathered a gang of cattle and went to Mendocino County, where the officers sought him out and tried to arrest him. But he resisted, and escaped. He then went home, and asked his mother for her blessing, for he was going out into the world to suffer and take the chances — that is live off the world and take his chances of suffering at his hands. His first exploit was the robbing of some peddlers in Monterey County; the next, the capture and robbery of a stage. He had confederates from the start, and was always recognized as a leader. He continued robbing until 1857, when he was sent to San Quentin from this city to five years for grand larceny. On his liberation, he made a faint unsuccessful effort at reform; but soon fell in with Procopio and Soto, and marauded with those notorious highwaymen, the latter of whom was killed by Morse in his celebrated fight with the gang in Alameda County. From this time until the affair of the Tres Pinos nothing of note transpired. His version of this affair is as follows: He sent three of his party, Chavez, Leiva and another into the hamlet to take a few drinks, reconnoitre [sic] the situation and report. When he arrived at the place, he found that his subordinates had engaged in a carnival of blood, contrary to his orders; that they had killed three men, two by the hands of Leiva, as he was informed. Finding it a bad business, and not having secured as much money as he wanted, he went to a woman whose husband was tied, and told her she must get him a certain sum of money or her husband would be dispatched. The money was procured, and they packed up the goods they wanted and started for the Elizabeth Lake country. Here he seduced Leiva\'s wife, and their liaison being discovered by the husband, the latter determined on revenge. Leiva left the band, and shortly after allowed himself to be arrested by Sheriff Rowland, of this county, so as to turn State\'s evidence, and help to deliver the seducer of his wife into the hands of the law. Vasquez, finding that his love affair was about to get him into trouble, made a raid on Firebaugh\'s Ferry to raise money to send the woman to her parents. The raid was successful, and Madame Leiva was sent home. The next notable affair of Vasquez was at King\'s river, where, with a party of eight men, he tied up thirty-five, and robbed them. But the people of the place began to show fight, and he retired with eight hundred dollars and considerable jewelry. His next exploit was the robbery of the stage and teams at Coyote Holes station, on the Owens river road. Chavez and himself were alone engaged in this affair, and they captured sixteen men, got two hundred dollars in money, and pistols, watches, jewelry, etc. A quantity of mining stock taken from James Craig was thrown away. This affair was followed, at quite an interval, by the robbery of Repetto at the Mission of San Gabriel, the story of which is fresh in the minds of our readers. After his escape up the Arroyo Seco, he wandered for some time in the mountains, and was at times hid in the dense brush near enough to his pursuers to have killed them in detail, if he had been so disposed. For the past few weeks he has camped in the neighborhood of the Cahuenga, and went frequently to meals at Greek George\'s house. He says he was fairly surprised when Johnston and his party came to the house, and if he had had the slightest warning, he would have made it hot for them. Before Leiva\'s defection, he and Chavez were his counselors in arranging plans; but since that time Chavez alone has been consulted. But, usually, he had no confidants, and never had any difficulty in raising a party when he wanted one. When the job was finished, his volunteers would return into the bosom of society until again wanted. In this way he had men engaged in ordinary occupations all over the country. He does not believe he would have had much trouble in "revolutionizing" Southern California, if he had had fifty or sixty thousand dollars to procure the necessary arms, and he was doubtless "laying" for a chance to make a "strike" to carry out this revolutionary project. In the meantime he was gathering arms all the while, and four hundred rounds of ammunition were seized at the house where he was captured. Following is his prison record: Tiburcio Vasquez at State Prison August 29th, 1857, from Los Angeles County. Crime, grand larceny; sentence, five years. He escaped June 25th, 1859. Returned August 16th, 1859, on new charge, from Amador County. Crime, grand larceny; sentence, one year. Term expired and discharged August 13th, 1863. Arrived at State Prison January 18th, 1867, from Sonoma County. Crime, grand larceny; sentence, four years. Discharged June 14th, 1870. Los Angeles May 19th, 1874.',
    'fullName' => 'Tiburcio Vasquez',
    'birthDate' => 'April 7, 1835',
    'birthplace' => 'Monterey County, California',
    'deathDate' => 'March 19, 1875',
    'burialPlace' => 'Santa Clara, California',
    'occupation' => 'Outlaw; Bandit; Cattle Rustler',
    'authorBio' => 'Tiburcio Vasquez (1835–1875) was a California-born outlaw and bandit whose raids through the Santa Clarita Valley made him one of the most notorious figures in Southern California history. His name lives on in Vasquez Rocks County Park near Agua Dulce, a favored hideout. He was hanged in San Jose on March 19, 1875.',
    'personLegacyUrl' => '/scvhistory/je4001.htm',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/5334/tiburcio-vasquez',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Tiburcio Vasquez\n";
} else {
    echo "FAILED: Tiburcio Vasquez - " . json_encode($entry->getErrors()) . "\n";
}

// Pedro Fages
$existing = \craft\elements\Entry::find()->section('persons')->slug('pedro-fages')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Pedro Fages\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Pedro Fages';
$entry->slug = 'pedro-fages';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Spanish Colonial (1769–1821)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('capt-pedro-fages.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Pedro Fages y Beleta was born around 1734 in Guissona, Catalonia, Spain. He served as a lieutenant in the Catalonian Volunteers under Governor Gaspar de Portolá during the landmark 1769 expedition from San Diego to Monterey — the first European overland journey through the Santa Clarita Valley. Following the expedition, Fages was appointed commanding officer of the Presidio of San Diego before assuming command as governor of Alta California in 1770. In the spring of 1772, six of his soldados de cuero deserted with six Tataviam women, prompting Fages to lead a pursuit expedition that took him through the Santa Clara River Valley. He named Agua Dulce for its sweet water springs, camped at the confluence of Castaic Creek and the Santa Clara River, and ascended Castaic Canyon before crossing what he called Cañada de las Uvas — now Tejon Pass — into the San Joaquin Valley. He never found the deserters. His detailed account of the expedition added thousands of acres to previously blank maps of Alta California. Fages served as governor of Alta California from 1782 to 1791. He retired either to Mexico or to his family estate in Seville — accounts differ. He died in 1796.',
    'fullName' => 'Pedro Fages',
    'birthDate' => 'c. 1734',
    'birthplace' => 'Guissona, Catalonia, Spain',
    'deathDate' => '1796',
    'burialPlace' => '',
    'occupation' => 'Military Officer; Colonial Governor',
    'authorBio' => 'Pedro Fages (c. 1734–1796) was a Catalonian soldier who served as military commander and governor of Alta California. He participated in the Portolá expedition of 1769 and in 1772 led an expedition through the Santa Clarita Valley in pursuit of deserters, naming several landmarks still in use today including Agua Dulce and Soledad.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Pedro Fages\n";
} else {
    echo "FAILED: Pedro Fages - " . json_encode($entry->getErrors()) . "\n";
}

// Father Francisco Garcés
$existing = \craft\elements\Entry::find()->section('persons')->slug('father-francisco-garces')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Father Francisco Garcés\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Father Francisco Garcés';
$entry->slug = 'father-francisco-garces';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Spanish Colonial (1769–1821)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('San Francisquito Canyon')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('father-francisco-garces-statute.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Francisco Tomás Hermenegildo Garcés was born April 12, 1738 in Zaragoza, Aragon, Spain. He entered the Franciscan order at age twenty-five and was assigned to Mission San Xavier de Bac in present-day Arizona in 1768. Almost immediately he began a series of solo explorations that crisscrossed the American Southwest. He served as pathfinder for Captain Juan Bautista de Anza\'s 1774 expedition proving the overland land route from Mexico to Alta California. During the 1776 De Anza expedition to found San Francisco, Garcés remained with the Yuma Indians on the Colorado River before setting off alone with four Indian companions into the Mojave Desert. Abandoned near present-day Victorville, he crossed Cajon Pass alone on foot, visited Mission San Gabriel, then climbed into the Santa Clara River Valley — following the old Portolá route down into a plain of cottonwoods and oaks. He arrived in the middle of a war between the local Tataviam and the coastal Chumash. He spent ten days settling the dispute before continuing up San Francisquito Canyon to Lago Kashtuk — the original Castaic Lake — then crossed the Kern River, visited Tulare Lake, and returned via Tehachapi Pass and the Mojave River to the Colorado River. Garcés spent the next five years nurturing his mission colony at the Yuma crossing. On July 18, 1781, the Yuma rose in revolt against Spanish soldiers stationed there and killed every Spaniard in sight. Garcés was clubbed to death. The Yuma chief had him carefully buried while a native woman planted chamomile flowers on his grave. He was beatified by Pope John Paul II on September 22, 1787.',
    'fullName' => 'Father Francisco Garcés',
    'birthDate' => 'April 12, 1738',
    'birthplace' => 'Morata de Jalón, Aragon, Spain',
    'deathDate' => 'July 18, 1781',
    'burialPlace' => 'Yuma, Arizona',
    'occupation' => '',
    'authorBio' => 'Father Francisco Garcés (1738–1781) was a Spanish Franciscan missionary and explorer who walked alone across the American Southwest, visiting the Santa Clarita Valley in 1776 where he settled a war between the Tataviam and Chumash. He was martyred at the Yuma Crossing on July 18, 1781.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Father Francisco Garcés\n";
} else {
    echo "FAILED: Father Francisco Garcés - " . json_encode($entry->getErrors()) . "\n";
}

// Antonio del Valle
$existing = \craft\elements\Entry::find()->section('persons')->slug('antonio-del-valle')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Antonio del Valle\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Antonio del Valle';
$entry->slug = 'antonio-del-valle';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Mexican Rancho Era (1821–1848)')->one(); if($cat) $eraIds[] = $cat->id;
$featuredImageIds = [];
$entry->setFieldValues([
    'body' => 'Antonio del Valle was born in 1788 at Composilla, Mexico, son of Antonio and María del Valle. He married María Josepha Carillo and had two children — Ygnacio and María — before his wife\'s death led him to join the San Blas Company as a lieutenant. Arriving in Monterey in 1819, Del Valle participated in the suppression of the 1824 Indian revolt at Santa Ynez, marching captive neophytes down the Santa Clara River to Ventura and north to Santa Barbara. Convicted of insubordination in 1825, he spent six months imprisoned and summoned his son Ygnacio to join him. In October 1834, Governor José Figueroa appointed Del Valle administrator of Mission San Fernando Rey at an annual salary of eight hundred dollars, tasking him with overseeing the secularization of the mission\'s vast holdings including Rancho San Francisco in the Santa Clarita Valley. Antonio del Valle died in 1841, leaving his son Ygnacio to consolidate the family\'s landholdings into one of the great ranchos of Southern California.',
    'fullName' => 'Antonio del Valle',
    'birthDate' => '1788',
    'birthplace' => 'Composilla, Mexico',
    'deathDate' => '1841',
    'burialPlace' => 'Unknown',
    'occupation' => 'Military Officer; Rancho Administrator; Land Grantee',
    'authorBio' => 'Antonio del Valle (1788–1841) was a Mexican military officer who arrived at Mission San Fernando in 1834 as the government-appointed administrator of secularization. He later acquired Rancho San Francisco, establishing the del Valle family dynasty that would dominate the Santa Clarita Valley for decades.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Antonio del Valle\n";
} else {
    echo "FAILED: Antonio del Valle - " . json_encode($entry->getErrors()) . "\n";
}

// Ygnacio del Valle
$existing = \craft\elements\Entry::find()->section('persons')->slug('ygnacio-del-valle')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Ygnacio del Valle\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Ygnacio del Valle';
$entry->slug = 'ygnacio-del-valle';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Mexican Rancho Era (1821–1848)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Camulos')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Piru')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('ygnacio-del-valle-portrait-c1850s.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Ygnacio Ramón de Jesus del Valle (sometimes spelled Ignacio) was born July 1, 1808 in Jalisco, Mexico, to Lt. Antonio Seferino del Valle and María Josepha (Carillo) del Valle. In March 1828, Ygnacio was commissioned as a second lieutenant in the Mexican Army and rose to the rank of captain in command of the presidio at San Diego and chief customs house officer. Antonio was at Monterey; in a power play between their commanders in 1832, the two lieutenants, Del Valle, faced each other. Ygnacio won, and father and son would never speak to each other again. After Mexico won its war for independence against Spain, Antonio was assigned to inventory former mission properties. He quit the army and petitioned Gov. Juan B. Alvarado to grant him some of the mission property: the 48,829-acre Rancho San Francisco, consisting of the western Santa Clarita Valley and portions of Ventura County easterly to the Piru area. Antonio took formal possession on Jan. 22, 1839 (although it may have been considered "his" as early as 1824) and became one of fewer than 400 private landowners in California. Antonio moved his new family (he remarried, to the much younger Jacoba Feliz) and moved into the rancho, where he lived until his death on June 21, 1841. With a widow and eight children to battle over the estate, Antonio, on his deathbed, dispatched a message to his number-one son, Ygnacio, offering him various properties, including the Rancho San Francisco. Before Ygnacio could reply, Antonio died without having left a will. Jacoba married José Salazár and sued; a judge awarded 13,599 acres to Ygnacio (the westernmost section), 21,307 acres to Jacoba, and 4,684 acres to each of Jacoba\'s six children. Although he occasionally visited, it was 1861 before Ygnacio would move permanently into the adobe home on his property (the section known as Camulos, near Piru along today\'s State Route 126, about 10 miles west of Interstate 5). Instead, Ygnacio lived in Los Angeles, where he was mayor. Actually, Ygnacio was a California state legislator, too: When statehood came in 1850, Ygnacio served a short stint in the first Legislature. But this was just a part-time job at the time, and most landowners didn\'t stick it out for more than a year or two. Ygnacio\'s real interests lay in Los Angeles. Ygnacio\'s tenure at the helm of Los Angeles may best be remembered for his attempt to clean up the city. In the early 1850s, Ygnacio formed the California Rangers. Headed by Major Horace Bell, the captains were William W. Jenkins (who would later own a large ranch in Castaic); William Reader, and Cyrus Lyon (twin brother of Sanford, an early Newhall entrepreneur. For these two would Lyons Avenue be named — although misspelled). The Rangers cleaned up, all right, meting out frontier justice (constitutional rights and probable cause be damned) so effectively that in two years the Rangers disbanded. Back at the ranch, things weren\'t going so well. Flooding ravaged the ranches of Southern California ranchers in the late 1850s and early \'60s; cattle died, and ranchers mortgaged their properties to the hilt. William Wolfskill held the paper to the properties owned by Jacoba Feliz and José Salazár. In 1861, Wolfskill cut a deal with Ygnacio that paid off the Salazárs\' debts and gave Ygnacio the western five-elevenths of the Rancho San Francisco. Gen. Edward F. Beale had already picked up the deed to Rancho Tejon to satisfy some of the Del Valle creditors. But the reprieve was short-lived. Drought came in 1862 and would last three years. Most of the cattle died, and again, the Rancho San Francisco was deep in debt. Ygnacio lost the ranch to his financiers, who sold it to some oil speculators. They didn\'t find what they were looking for (oil wasn\'t discovered on the ranch until 1933), and after changing hands a few times, the land ultimately went in 1875 to an auctioneer-turned-railroad tycoon from San Francisco ... named Henry Mayo Newhall. City of Los Angeles Offices Held by Ygnacio del Valle Listed chronologically by term in office • Source: City of Los Angeles City Council: Member Elected: May 04, 1852 • Completed: May 03, 1853 Vaccination Committee: Member Appointed: June 08, 1852 • Completed: May 03, 1853 Health Committee: Member Appointed: August 12, 1852 • Completed: May 03, 1853 Land and Lots Committee: Member Appointed: October 02, 1852 • Completed: May 03, 1853 Canal Improvement And Repairs Committee: Member Appointed: January 17, 1853 • Completed: May 03, 1853 Investigation Committee (Water Claims): Member Appointed: March 03, 1853 • Completed: May 03, 1853 Examination Committee: Member Appointed: April 16, 1853 • Completed: May 03, 1853 City of Los Angeles (Appointed): Election Inspector Appointed: June 09, 1853 • Completed: May 04, 1854 City Council: Member Elected: May 07, 1856 • Resigned: December 15, 1856 Streets Committee: Member Appointed: May 10, 1856 • Transferred: June 23, 1856 Lands Committee: Member Appointed: May 10, 1856 • Resigned: December 15, 1856 Police Committee: Member Added: June 23, 1856 • Resigned: December 15, 1856 Legal Investigation Committee (Of city\'s contract with Mr. Brent): Member Appointed: December 05, 1856 • Resigned: December 15, 1856 City Square Improvement Committee: Member Appointed: January 03, 1859 • Completed: May 09, 1859',
    'fullName' => 'Ygnacio del Valle',
    'birthDate' => 'July 1, 1808',
    'birthplace' => 'Jalisco, Mexico',
    'deathDate' => '1880',
    'burialPlace' => 'Camulos Ranch, Ventura County, California',
    'occupation' => 'Military Officer; Rancher; Land Owner',
    'authorBio' => 'Ygnacio del Valle (1808–1880) was the son of Antonio del Valle and the builder of the del Valle rancho empire in the Santa Clarita Valley. He inherited Rancho San Francisco and Rancho Camulos after his father\'s death in 1841, becoming one of the most powerful landowners in Southern California.',
    'personLegacyUrl' => '/scvhistory/lw2052.htm',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/136176316/ignacio_ramn_de_jess-del_valle',
    'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Ygnacio_del_Valle',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Ygnacio del Valle\n";
} else {
    echo "FAILED: Ygnacio del Valle - " . json_encode($entry->getErrors()) . "\n";
}

// Gaspar de Portolá
$existing = \craft\elements\Entry::find()->section('persons')->slug('gaspar-de-portola')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Gaspar de Portolá\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Gaspar de Portolá';
$entry->slug = 'gaspar-de-portola';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Spanish Colonial (1769–1821)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('gaspar-de-portola-portrait.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Gaspar de Portolá y Rovira (January 1, 1716 – October 10, 1786) was a Spanish Army officer and colonial administrator who served as the first governor of the Californias from 1767 to 1770. Born in Os de Balaguer, Catalonia, into a family of minor Catalan nobility, he is best known for leading the 1769 expedition that made the first European overland journey through Alta California — including the first recorded European contact with the Santa Clarita Valley and its Tataviam inhabitants. Portolá joined the Spanish Army as a young man, serving in Italy during the War of the Austrian Succession and in Portugal during the Fantastic War before being assigned to New Spain. In May 1768, Visitor-General José de Gálvez appointed him governor of the Californias and placed him in overall command of the Sacred Expedition to colonize Alta California. The expedition departed in two land parties and two sea parties. Portolá\'s land party, which included Father Juan Crespí as diarist and Lieutenant Pedro Fages commanding the Catalonian Volunteers, set out from Velicatá on May 15, 1769. The parties converged at San Diego, where Junípero Serra established Mission San Diego de Alcalá on July 16, 1769. Portolá then pressed north overland with sixty-four men toward Monterey. On August 8, 1769 the expedition descended through Frémont Pass into the Santa Clara River Valley, making camp at the Tataviam village of Chaguayabit at the confluence of Castaic Creek and the Santa Clara River. Father Crespí described the welcome as one of "cordial welcome and quiet acceptance" and estimated at least 500 Tataviam present. The expedition reached Monterey Bay — which Portolá failed to recognize from Vizcaíno\'s description — and pressed north to discover San Francisco Bay on November 4, 1769. Returning to San Diego, Portolá made a second overland journey to Monterey in 1770, formally establishing the presidio there on June 3, 1770 alongside Father Serra. Portolá returned to Mexico in 1770, was appointed governor of Puebla in 1777, and retired to Spain. He died in Barcelona on October 10, 1786. Several California landmarks bear his name including the city of Portola in Plumas County, Portola Valley in San Mateo County, and Portola Parkway in Irvine.',
    'fullName' => 'Gaspar de Portolá',
    'birthDate' => 'c. 1727',
    'birthplace' => 'Balaguer, Catalonia, Spain',
    'deathDate' => 'c. 1786',
    'burialPlace' => '',
    'occupation' => 'Military Officer; Colonial Governor',
    'authorBio' => 'Gaspar de Portolá (c. 1727–1786) was a Spanish military officer appointed first governor of the Californias in 1767. He led the landmark 1769 expedition that made the first European overland journey through Alta California, entering the Santa Clarita Valley on August 8, 1769.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Gaspar de Portolá\n";
} else {
    echo "FAILED: Gaspar de Portolá - " . json_encode($entry->getErrors()) . "\n";
}

// Juan Crespí
$existing = \craft\elements\Entry::find()->section('persons')->slug('juan-crespi')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Juan Crespí\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Juan Crespí';
$entry->slug = 'juan-crespi';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Spanish Colonial (1769–1821)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('juan-crespi-portrait.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Father Juan Crespí (March 1, 1721 – January 1, 1782) was a Franciscan friar from Palma, Mallorca, Spain, best known as the chief diarist of the Portolá Expedition of 1769 — the first European overland journey through Alta California. His journals provide the earliest written descriptions of the California landscape, its peoples, and its geography, and constitute one of the most important primary sources in California history. Crespí entered the Franciscan order as a young man and studied under Father Junípero Serra at the convent of San Bernardino in Palma before following Serra to the New World. He served at missions in Baja California before being selected to accompany the Sacred Expedition to Alta California in 1769. During the Portolá Expedition, Crespí traveled with the advance party under Captain Fernando Rivera y Moncada, keeping a meticulous daily journal of the journey from San Diego northward to Monterey and San Francisco Bay. He was the first European to write a description of the Santa Clarita Valley, encountering the Tataviam at their village of Chaguayabit on August 8, 1769. His description of the valley — which he named Santa Clara, for Saint Clare — is remarkable in its detail. He described large round houses roofed with grass and underground dwellings entered by ladders, the Tataviam diet of sage, pinole gruel, pine nuts, and boiled almonds, and their exquisitely fine shell bead jewelry. He estimated at least 500 people at the encampment and recommended the site for a future mission. Crespí\'s original hand-written account of the expedition was heavily edited before its first publication. The unexpurgated manuscript was later discovered in dusty archives in Madrid, revealing for the first time the full richness of his observations. The complete edition, translated by Alan K. Brown and published in 2001 as A Description of Distant Roads, is considered the definitive account of the Portolá Expedition. Crespí spent the remainder of his life at Mission San Carlos Borromeo in Carmel, where he died on January 1, 1782. He is buried beneath the floor of the mission church alongside Father Serra.',
    'fullName' => 'Juan Crespí',
    'birthDate' => 'March 1, 1721',
    'birthplace' => 'Palma, Mallorca, Spain',
    'deathDate' => 'January 1, 1782',
    'burialPlace' => 'Mission San Carlos Borromeo, Carmel, California',
    'occupation' => 'Franciscan Missionary; Diarist; Explorer',
    'authorBio' => 'Father Juan Crespí (1721–1782) was a Franciscan friar and the chief diarist of the Portolá Expedition of 1769. His journal contains the first European written description of the Santa Clarita Valley and the Tataviam people. His unexpurgated original manuscript was later discovered in archives in Madrid.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Juan Crespí\n";
} else {
    echo "FAILED: Juan Crespí - " . json_encode($entry->getErrors()) . "\n";
}

// Junípero Serra
$existing = \craft\elements\Entry::find()->section('persons')->slug('junipero-serra')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Junípero Serra\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Junípero Serra';
$entry->slug = 'junipero-serra';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Spanish Colonial (1769–1821)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('junipero_serra.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Father Junípero Serra (November 24, 1713 – August 28, 1784) was a Spanish Franciscan friar who founded nine of California\'s twenty-one missions and served as the first Father-Presidente of the California mission system. Born Miguel José Serra in Petra, Mallorca, Spain, he took the name Junípero upon entering the Franciscan order in honor of Brother Juniper, a companion of Saint Francis of Assisi. Serra studied theology and philosophy at the Franciscan convent in Palma, where he taught Juan Crespí — who would later serve as diarist of the Portolá Expedition. Ordained in 1737, Serra became a professor of philosophy before volunteering for the missionary frontier of New Spain in 1749. He served at missions in Baja California before Visitor-General José de Gálvez selected him to lead the spiritual component of the Sacred Expedition to Alta California. Serra established Mission San Diego de Alcalá on July 16, 1769 — the first of the California missions — then remained at San Diego to nurse the struggling colony while Governor Gaspar de Portolá pressed north on the overland expedition that would pass through the Santa Clarita Valley on August 8, 1769. Serra founded Mission San Carlos Borromeo de Carmelo in 1770, which became his headquarters for the remainder of his life. Over the next fifteen years Serra founded seven more missions along El Camino Real, traveling thousands of miles on foot despite a chronic leg infection that caused him constant pain. His mission system brought European agriculture, crafts, and Christianity to California\'s indigenous peoples but also displaced them from their ancestral lands and exposed them to devastating epidemics. The mission Serra\'s system established at San Fernando in 1797 — thirteen years after his death — would prove decisive for the Santa Clarita Valley. Mission San Fernando Rey de España\'s vast rancho holdings covering the Santa Clara River basin displaced the Tataviam from their homeland and set the stage for the rancho era that followed secularization. Serra died at Mission San Carlos Borromeo on August 28, 1784 and is buried beneath the floor of the mission church alongside Juan Crespí. He was beatified by Pope John Paul II in 1988 and canonized by Pope Francis on September 23, 2015, amid significant controversy over his role in the suppression of California\'s indigenous peoples.',
    'fullName' => 'Junípero Serra',
    'birthDate' => 'November 24, 1713',
    'birthplace' => 'Petra, Mallorca, Spain',
    'deathDate' => 'August 28, 1784',
    'burialPlace' => 'Mission San Carlos Borromeo, Carmel, California',
    'occupation' => 'ranciscan Missionary; Father-Presidente',
    'authorBio' => 'Father Junípero Serra (1713–1784) was the Franciscan friar appointed Father-Presidente of the California mission system in 1769. While Serra remained at San Diego during the Portolá Expedition, the mission system he established transformed the Santa Clarita Valley over the following decades. He was canonized by Pope Francis in 2015.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Junípero Serra\n";
} else {
    echo "FAILED: Junípero Serra - " . json_encode($entry->getErrors()) . "\n";
}

// Juan Bautista de Anza
$existing = \craft\elements\Entry::find()->section('persons')->slug('juan-bautista-de-anza')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Juan Bautista de Anza\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Juan Bautista de Anza';
$entry->slug = 'juan-bautista-de-anza';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Spanish Colonial (1769–1821)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('juan-bautista-de-anza-portrait-1774.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Juan Bautista de Anza (July 7, 1736 – December 19, 1788) was a Spanish colonial military officer born in Fronteras, Sonora, New Spain, who led the two overland expeditions that established the land route from Mexico to Alta California and made possible the permanent colonization of California. Anza was born into a military family on the frontier of New Spain — his father and grandfather had both served as military commanders in Sonora. He followed family tradition, rising through the ranks of the Spanish colonial army to become commander of the presidio at Tubac in present-day southern Arizona. Known for his skill in frontier warfare and his ability to navigate unmapped terrain, Anza proposed to Visitor-General Antonio Bucareli y Ursúa an overland route to the struggling California missions, which were then accessible only by sea. The first De Anza Expedition departed Tubac on January 8, 1774, with thirty-four leather-jacket soldiers and Father Francisco Garcés as pathfinder. After three months crossing the Sonoran and Mojave deserts, the party arrived at Mission San Gabriel on March 22, 1774, proving for the first time that an overland land route from Mexico to the California coast was feasible. Anza continued north to Monterey before returning to Mexico City, where he was promoted to lieutenant colonel. The second and larger expedition departed Tubac on October 23, 1775, carrying 240 colonists — soldiers, their families, missionaries, and servants. Father Garcés again served as pathfinder but remained at the Yuma Crossing while Anza pressed north with the colonists. By the time the party reached San Francisco Bay in March 1776, three babies had been born along the way, raising the count to 242 despite one death. Anza selected the sites for the Presidio of San Francisco and Mission Dolores before returning to Mexico, leaving Lieutenant José Joaquín Moraga to formally establish both in the fall of 1776. While Anza led the colonists north, Father Garcés set out alone from the Yuma Crossing on an extraordinary solo journey through the Mojave Desert, crossing Cajon Pass on foot and entering the Santa Clarita Valley — the most direct connection between the De Anza Expeditions and the Santa Clarita Valley\'s history. Anza was appointed governor of New Mexico in 1777, where he served until 1788, defeating the Comanche chief Cuerno Verde in 1779 and negotiating a lasting peace with the Comanche nation. He died in Arizpe, Sonora, on December 19, 1788. The Anza Trail — the overland route he pioneered — is today commemorated as the Juan Bautista de Anza National Historic Trail, a 1,200-mile route from Nogales, Arizona to San Francisco designated by Congress in 1990.',
    'fullName' => 'Juan Bautista de Anza',
    'birthDate' => 'July 7, 1736 Death Date: December 19, 1',
    'birthplace' => 'ronteras, Sonora, New Spain',
    'deathDate' => 'December 19, 1788',
    'burialPlace' => 'Cathedral of Nuestra Señora de la Asunción, Arizpe, Sonora, Mexico',
    'occupation' => 'Military Officer; Explorer; Colonial Governor',
    'authorBio' => 'Juan Bautista de Anza (1736–1788) was a Spanish colonial officer who led two expeditions establishing the overland route from Sonora to Alta California in 1774 and 1775–76. His second expedition founded San Francisco. Father Francisco Garcés served as his pathfinder and visited the Santa Clarita Valley during the 1776 expedition.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Juan Bautista de Anza\n";
} else {
    echo "FAILED: Juan Bautista de Anza - " . json_encode($entry->getErrors()) . "\n";
}

// Juventino del Valle
$existing = \craft\elements\Entry::find()->section('persons')->slug('juventino-del-valle')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Juventino del Valle\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Juventino del Valle';
$entry->slug = 'juventino-del-valle';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Mexican Rancho Era (1821–1848)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1900-1919')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Camulos')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Piru')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('juventino-del-valle-black-walnut-tree-camulos-1910s.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Juventino del Valle was born circa 1841, the son of Ygnacio del Valle and María Los Angeles Carrillo. His parents\' delayed marriage — exchanging vows before Bishop Narciso Durán on January 1, 1842, some six months after Juventino\'s birth — reflected the complicated romantic history of his father\'s early years in Santa Barbara. Through his father, Juventino was heir to one of the most significant land holdings in the Santa Clarita Valley, encompassing portions of Rancho San Francisco and what would become the storied Rancho Camulos. Juventino grew up as a son of the California rancho era, a world defined by vast cattle operations, Californio social life, and the sweeping legal and political changes that followed American statehood in 1850. The del Valle family maintained a large home on the Plaza in Los Angeles while treating Camulos as a seasonal residence, and it was not until 1861 that Don Ygnacio took up permanent residence there. As a del Valle heir, Juventino\'s life was shaped by that transition — from a family that straddled Los Angeles society and the open rancho lands of the Santa Clarita Valley, to one increasingly rooted at Camulos as the center of family life.',
    'fullName' => 'Juventino del Valle',
    'birthDate' => '1841 (born approximately six months before his parents\' January 1, 1842 marriage)',
    'birthplace' => 'Santa Barbara, California (probable)',
    'deathDate' => '',
    'burialPlace' => '',
    'occupation' => 'Rancher',
    'authorBio' => 'Juventino del Valle was the son of Ygnacio del Valle and María Los Angeles Carrillo, born out of wedlock approximately six months before his parents exchanged wedding vows before Bishop Narciso Durán on January 1, 1842. As a son of Ygnacio, he was an heir to Rancho Camulos and the del Valle land holdings in the Santa Clarita Valley.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Juventino del Valle\n";
} else {
    echo "FAILED: Juventino del Valle - " . json_encode($entry->getErrors()) . "\n";
}

// Francisco Lopez
$existing = \craft\elements\Entry::find()->section('persons')->slug('francisco-lopez')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Francisco Lopez\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Francisco Lopez';
$entry->slug = 'francisco-lopez';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Placerita Canyon')->one(); if($cat) $hoodIds[] = $cat->id;
$featuredImageIds = [];
$entry->setFieldValues([
    'body' => 'Juan José Francisco de Gracia Lopez — known familiarly as "Chico" — was a man of unusual education and ambition for the California rancho era. Educated at the university in Sonora, Mexico, where he studied mineralogy, Lopez was no simple cattle rancher. He leased a section of land on Rancho San Francisco from his niece\'s family, the del Valles, and systematically searched for gold throughout the rancho\'s vast territory. His scientific background was the real engine behind the discovery — the dream legend notwithstanding. On the morning of March 9, 1842, Lopez and two companions, Manuel Cota and Domingo Bermudez, rode out into Cañon de los Encinos in search of range cattle. At midday, resting under a large oak, Lopez pulled wild onions from the earth near a sycamore grove and found gold glittering in the roots. He rode first to Mission San Fernando, where his brother Pedro Lopez served as majordomo, and together they presented the sample to Los Angeles merchant Abel Stearns, who confirmed it as a legitimate gold placer. Stearns forwarded the sample via Alfred Robinson to the United States Mint in Philadelphia, where it was assayed at .926 fine gold. Within months, California\'s first gold rush was underway, drawing some two thousand miners — most from the Mexican state of Sonora — to what was quickly renamed Placerita Canyon. Governor Santiago Arguello named Ygnacio del Valle encargado de justicia of the mining district, and del Valle\'s records show 125 pounds of ore quarried before the mines petered out in 1848. Lopez petitioned the Mexican government for rights to his claim and was eventually awarded Rancho Temescal, a land grant running through the mountains from present-day Castaic to Piru. Of the estimated $616,000 in gold mined at Placerita, the discoverer\'s share amounted to less than $100.',
    'fullName' => 'Juan José Francisco de Gracia ("Chico") Lopez',
    'birthDate' => 'c. 1800s (exact date unknown)',
    'birthplace' => 'Sonora, Mexico (probable)',
    'deathDate' => '',
    'burialPlace' => '',
    'occupation' => 'Rancher; Miner',
    'authorBio' => 'Francisco Lopez discovered gold in Placerita Canyon on March 9, 1842, launching California\'s first gold rush six years before the famous strike at Sutter\'s Mill. Educated in mineralogy at the university in Sonora, Mexico, Lopez was the uncle of Doña Jacoba Felix del Valle and a frequent visitor to Rancho San Francisco.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Francisco Lopez\n";
} else {
    echo "FAILED: Francisco Lopez - " . json_encode($entry->getErrors()) . "\n";
}

// John C. Frémont
$existing = \craft\elements\Entry::find()->section('persons')->slug('john-c-fremont')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: John C. Frémont\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'John C. Frémont';
$entry->slug = 'john-c-fremont';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('john-c-fremont-portrait-california-state-library-scaled.png')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'John Charles Frémont was born in Savannah, Georgia in 1813 and rose through the U.S. Army Corps of Topographical Engineers to become one of the most celebrated — and controversial — figures of the American West. His early expeditions into the Oregon Territory and the Rocky Mountains earned him the popular nickname "The Pathfinder" and made him a national hero. It was his third expedition, in 1845–1846, that brought him to California at a pivotal moment. Arriving ostensibly on a mapping mission, Frémont inserted himself into the volatile politics of Mexican California with characteristic boldness. His presence emboldened American settlers to launch the Bear Flag Revolt in June 1846, and when war between the United States and Mexico was formally declared, Frémont assumed command of the California Battalion, a volunteer force that helped secure the territory for the United States. His methods, however, were consistently insubordinate. He defied orders from Commodore Robert Stockton and General Stephen Kearny — both of whom claimed authority over California — and was ultimately court-martialed for mutiny and disobedience in 1847. President Polk remitted his sentence, but Frémont resigned his commission in protest. He later became one of California\'s first two U.S. Senators following statehood in 1850, and in 1856 he was the Republican Party\'s first presidential candidate, losing to James Buchanan. His connection to the Santa Clarita Valley deepens through the rancho-era political network in which Ygnacio del Valle and other leading Californios were drawn into the American orbit Frémont helped expand.',
    'fullName' => 'John C. Frémont',
    'birthDate' => 'John C. Frémont',
    'birthplace' => 'Savannah, Georgia',
    'deathDate' => 'July 13, 1890',
    'burialPlace' => 'Trinity Church Cemetery, New York City, New York',
    'occupation' => 'Military Officer; Explorer; Politician',
    'authorBio' => 'ohn C. Frémont was a U.S. Army captain and explorer whose aggressive intervention in California during the Mexican-American War accelerated — and complicated — American acquisition of the state. Known as "The Pathfinder," he later served as U.S. Senator from California and was the Republican Party\'s first presidential candidate in 1856.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/834/john-charles-fremont',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: John C. Frémont\n";
} else {
    echo "FAILED: John C. Frémont - " . json_encode($entry->getErrors()) . "\n";
}

// Abel Stearns
$existing = \craft\elements\Entry::find()->section('persons')->slug('abel-stearns')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Abel Stearns\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Abel Stearns';
$entry->slug = 'abel-stearns';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Mexican Rancho Era (1821–1848)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('abel-stearns-portrait-california-state-library-1840-1860-scaled.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Abel Stearns was born in Lunenburg, Massachusetts in 1798 and arrived in California in 1829 after years of trading along the Pacific coast of Mexico. He settled in Los Angeles, converted to Catholicism, became a Mexican citizen, and married Arcadia Bandini, daughter of the prominent Californio ranchero Juan Bandini — following the well-worn path of Yankee assimilation into Californio society that would later be recognized as deliberate political infiltration encouraged by Washington. His trading house became the commercial hub of Southern California, dealing in hides, tallow, and imported goods. Stearns accumulated enormous landholdings over time, eventually controlling hundreds of thousands of acres across Los Angeles and San Diego counties, earning him the title "El Rey" — the king — of Southern California ranching. His role in SCV history centers on the 1842 Placerita gold discovery: when Francisco Lopez and his brother brought their gold sample to Los Angeles, it was Stearns who ran the tests confirming the existence of a gold placer, then forwarded the sample via Alfred Robinson to the United States Mint in Philadelphia, where it was assayed at .926 fine gold. That confirmation launched California\'s first gold rush. Stearns also figures in the broader political story of American infiltration of Mexican California — he was among the naturalized Yankees who, whether by design or circumstance, helped ease the transition to American rule.',
    'fullName' => 'Abel Stearns',
    'birthDate' => 'February 9, 1798',
    'birthplace' => 'Lunenburg, Massachusetts',
    'deathDate' => 'August 23, 1871',
    'burialPlace' => 'Calvary Cemetery, Los Angeles, California',
    'occupation' => 'Merchant; Rancher; Politician',
    'authorBio' => 'Abel Stearns was a Massachusetts-born merchant who became one of the wealthiest landowners in Southern California. Based in Los Angeles, he played a key role in confirming Francisco Lopez\'s 1842 gold discovery at Placerita Canyon — California\'s first gold rush — by testing the ore sample and forwarding it to the U.S. Mint in Philadelphia.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Abel Stearns\n";
} else {
    echo "FAILED: Abel Stearns - " . json_encode($entry->getErrors()) . "\n";
}

// Thomas O. Larkin
$existing = \craft\elements\Entry::find()->section('persons')->slug('thomas-o-larkin')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Thomas O. Larkin\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Thomas O. Larkin';
$entry->slug = 'thomas-o-larkin';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('thomas-o-larkin-portrait-california-state-library.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Thomas Oliver Larkin was born in Charlestown, Massachusetts in 1802 and arrived in California in 1832, establishing himself as the leading merchant in Monterey. He became the United States Consul to Mexican California in 1843 — the only person ever to hold that title — and was simultaneously appointed by President James K. Polk as a confidential agent with a specific and sensitive mission: to cultivate support among California\'s leading Californio citizens for peaceful annexation to the United States. Larkin worked quietly within the existing social and political fabric, building relationships with figures including Mariano Vallejo, Pio Pico, and Ygnacio del Valle — men who had already begun to conclude that California\'s future lay with the Americans rather than the increasingly distant Mexican government. His approach was diplomatic rather than military: he sought to engineer a peaceful handover rather than a conquest. That careful strategy was upended by John C. Frémont\'s aggressive and unauthorized intervention in 1846, which provoked the Bear Flag Revolt and ultimately forced a military rather than political resolution. Larkin went on to play a significant role in California\'s early American period, accumulating considerable real estate holdings and serving as a delegate to the state constitutional convention of 1849. He died in San Francisco in 1858.',
    'fullName' => 'Thomas O. Larkin',
    'birthDate' => 'September 16, 1802',
    'birthplace' => 'Charlestown, Massachusetts',
    'deathDate' => 'October 27, 1858',
    'burialPlace' => 'Mountain View Cemetery, Oakland, California',
    'occupation' => 'Merchant; Diplomat; Political Agent',
    'authorBio' => 'Thomas O. Larkin was the only U.S. Consul to Mexican California and President Polk\'s secret agent in Monterey, tasked with encouraging California\'s leading citizens — including Ygnacio del Valle — to support peaceful annexation by the United States. His confidential diplomacy helped lay the groundwork for the American acquisition of California.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Thomas O. Larkin\n";
} else {
    echo "FAILED: Thomas O. Larkin - " . json_encode($entry->getErrors()) . "\n";
}

// Edwin Bryant
$existing = \craft\elements\Entry::find()->section('persons')->slug('edwin-bryant')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Edwin Bryant\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Edwin Bryant';
$entry->slug = 'edwin-bryant';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('edwin-bryant.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Edwin Bryant was born in Pelham, Massachusetts in 1805 and worked as a journalist in Louisville, Kentucky before embarking for California in 1846. He traveled the overland trail and arrived in California in time to join John C. Frémont\'s California Battalion during the American conquest of the territory. His account of the battalion\'s southward march in January 1847 provides one of the most valuable primary source records of Frémont\'s route through the Santa Clarita Valley. Bryant recorded that on January 9, 1847, the battalion camped at a rancho on the edge of a fertile plain where they found corn, wheat, and frijoles in abundance — almost certainly the property of José Salazár — and that the following day they crossed the plain and ascended through a difficult pass into the San Fernando Valley. Following the Capitulation of Cahuenga, Bryant was appointed alcalde of San Francisco under the American military government. He returned to Kentucky and published his California journal as What I Saw in California in 1848, one of the earliest and most widely read overland trail narratives. The book went through multiple editions and helped fuel the emigration that followed the California Gold Rush.',
    'fullName' => 'Edwin Bryant',
    'birthDate' => '1805',
    'birthplace' => 'Pelham, Massachusetts',
    'deathDate' => 'December 16, 1869',
    'burialPlace' => 'Cave Hill Cemetery, Louisville, Kentucky',
    'occupation' => 'Journalist; Author; Alcalde',
    'authorBio' => 'Edwin Bryant was a Massachusetts-born journalist and author who traveled overland to California in 1846 and served in Frémont\'s California Battalion. His firsthand account of the battalion\'s January 1847 march through Castaic Junction is one of the few primary sources documenting Frémont\'s route through the Santa Clarita Valley.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/8906/edwin-bryant',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Edwin Bryant\n";
} else {
    echo "FAILED: Edwin Bryant - " . json_encode($entry->getErrors()) . "\n";
}

// Kit Carson
$existing = \craft\elements\Entry::find()->section('persons')->slug('kit-carson')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Kit Carson\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Kit Carson';
$entry->slug = 'kit-carson';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Castaic')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('kit_carson_photograph_restored.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Christopher Houston Carson, universally known as Kit Carson, was born in Kentucky in 1809 and grew up on the Missouri frontier. He left home at sixteen to apprentice as a saddler but soon ran away to join a trading expedition to Santa Fe, beginning a life defined by the wilderness. Through the 1830s he worked as a trapper and hunter in the Rocky Mountains, acquiring an encyclopedic knowledge of western terrain, Native trails, and survival skills that would make him the most sought-after scout of his era. His association with John C. Frémont began in 1842 when the two met by chance on a Missouri River steamboat. Carson served as guide on three of Frémont\'s major expeditions, providing the practical knowledge of routes and terrain that made Frémont\'s celebrated maps possible. The press lionized Frémont as "The Pathfinder," but Reynolds notes wryly that it was Carson who found most of the paths. Carson\'s role in the California campaign of 1846–1847 extended beyond scouting — he carried dispatches between California and Washington, D.C., crossing the continent twice in a single year. He later served as a Union Army officer during the Civil War and as U.S. Indian Agent for New Mexico. His connection to the Santa Clarita Valley runs through his service with the California Battalion, which camped at Castaic Junction on January 9, 1847, days before the Capitulation of Cahuenga ended the war in California.',
    'fullName' => 'Christopher Houston Carson',
    'birthDate' => 'December 24, 1809',
    'birthplace' => 'Madison County, Kentucky',
    'deathDate' => 'May 23, 1868',
    'burialPlace' => 'Kit Carson Cemetery, Taos, New Mexico',
    'occupation' => 'Scout; Trapper; Military Officer',
    'authorBio' => 'Kit Carson was a Kentucky-born frontiersman, trapper, and scout whose intimate knowledge of the western wilderness made him the indispensable guide on John C. Frémont\'s mapping expeditions into Oregon, the Sierra Nevada, and California. Though Frémont received the fame, it was Carson who found most of the paths.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/1819/christopher-houston-carson',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Kit Carson\n";
} else {
    echo "FAILED: Kit Carson - " . json_encode($entry->getErrors()) . "\n";
}

// Andrés Pico
$existing = \craft\elements\Entry::find()->section('persons')->slug('andres-pico')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Andrés Pico\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Andrés Pico';
$entry->slug = 'andres-pico';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Mexican Rancho Era (1821–1848)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('andres_pico_circa_1850.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Andrés Pico was born in San Diego in 1810, the younger brother of Pío Pico, the last Mexican governor of California. A skilled horseman and military commander, he rose through the Californio militia to become one of the most effective military leaders of the Mexican-American War in California. On December 6, 1846, his lancers dealt U.S. General Stephen Kearny a stunning defeat at the Battle of San Pasqual near San Diego — the costliest engagement of the entire California campaign for American forces. Despite this victory, the broader military situation was hopeless for the Californios, and Pico recognized it. On January 13, 1847, he met with John C. Frémont\'s California Battalion at the home of María Jesus Lopez de Felíz near Cahuenga Pass and surrendered his sword, signing the Capitulation of Cahuenga that ended the war in California. Frémont\'s terms were generous — Californios who laid down arms were guaranteed full rights as American citizens — a settlement Reynolds characterized as "peace with honor." Following the war, Andrés Pico became a prominent landowner and politician in the new American state, eventually acquiring the former Mission San Fernando lands and serving in the California State Assembly. His burial at Mission San Fernando ties him permanently to the geography of the Santa Clarita Valley\'s doorstep.',
    'fullName' => 'Andrés Pico',
    'birthDate' => 'November 18, 1810',
    'birthplace' => 'San Diego, Alta California',
    'deathDate' => 'February 14, 1876',
    'burialPlace' => 'Mission San Fernando Rey de España, Mission Hills, California',
    'occupation' => 'Military Officer; Rancher; Politician',
    'authorBio' => 'Andrés Pico was a Mexican-Californio military commander who defeated U.S. General Stephen Kearny at the Battle of San Pasqual in December 1846 — the bloodiest battle of the California campaign — before signing the Capitulation of Cahuenga with John C. Frémont on January 13, 1847, ending the Mexican-American War in California. He later became one of the most significant landowners in the San Fernando Valley.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/5913/andres-pico',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Andrés Pico\n";
} else {
    echo "FAILED: Andrés Pico - " . json_encode($entry->getErrors()) . "\n";
}

// James W. Marshall
$existing = \craft\elements\Entry::find()->section('persons')->slug('james-w-marshall')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: James W. Marshall\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'James W. Marshall';
$entry->slug = 'james-w-marshall';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('james-w-marshall-portrait-1870s.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'James Wilson Marshall was born in Hope Township, New Jersey in 1810 and spent his early years as a carpenter and wheelwright before joining the westward migration to California in 1845. He settled at Sutter\'s Fort in the Sacramento Valley and went to work for Swiss entrepreneur John Sutter, who contracted him to build a sawmill on the American River at Coloma. On the cold, dreary morning of January 24, 1848, Marshall noticed something glittering in the millrace tailrace. He picked it up, tested it, and confirmed what he suspected — it was gold. Marshall rode immediately to Sutter\'s Fort to report the find. Sutter swore his workers to secrecy, but the news leaked within weeks and spread around the world within months. The California Gold Rush that followed transformed the state and the nation, drawing hundreds of thousands of emigrants from across the globe and accelerating California\'s path to statehood in 1850. Marshall\'s discovery came just nine days before the Treaty of Guadalupe Hidalgo was signed on February 2, 1848, formally transferring California from Mexico to the United States. Despite triggering one of the most consequential events in American history, Marshall himself profited little from the discovery. His mining claims were repeatedly jumped, his sawmill business collapsed, and he spent much of his later life in poverty. He died in 1885 at Kelsey, California. His connection to the Santa Clarita Valley runs through the chain of events his discovery set in motion — the Gold Rush that drew the Bennett-Arcan Party westward in 1849, leading to William Lewis Manly\'s rescue at Rancho San Francisco and the naming of Death Valley.',
    'fullName' => 'James Wilson Marshall',
    'birthDate' => 'October 8, 1810',
    'birthplace' => 'Hope Township, New Jersey',
    'deathDate' => 'August 10, 1885',
    'burialPlace' => 'Marshall Gold Discovery State Historic Park, Coloma, California',
    'occupation' => 'Carpenter; Millwright; Miner',
    'authorBio' => 'James Wilson Marshall was a New Jersey-born carpenter who on January 24, 1848, discovered gold at John Sutter\'s sawmill on the American River — triggering the California Gold Rush. His find came just nine days before the Treaty of Guadalupe Hidalgo formally transferred California to the United States.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/1144/james-wilson-marshall',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: James W. Marshall\n";
} else {
    echo "FAILED: James W. Marshall - " . json_encode($entry->getErrors()) . "\n";
}

// William Lewis Manly
$existing = \craft\elements\Entry::find()->section('persons')->slug('william-lewis-manly')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: William Lewis Manly\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'William Lewis Manly';
$entry->slug = 'william-lewis-manly';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Valencia')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('william-lewis-manly-portrait-1890s.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'William Lewis Manly was born in St. Albans, Vermont in 1820 and spent his early years as a frontier trapper and hunter before joining the westward emigration of 1849. Traveling with the Bennett-Arcan Party from Wisconsin, Manly followed John C. Frémont\'s so-called Pioneer\'s Bible overland guide until the party took a disastrous shortcut through southern Utah. Stranded in the brutal desert that would come to bear the name Death Valley, the party faced starvation as their wagons broke down amid sand, alkali sinks and sterile hills. On November 4, 1849, twenty-nine-year-old Manly and twenty-two-year-old John Rogers set out on foot with little more than a canteen of water and some jerky to find help. Over the following weeks they walked approximately 250 miles across the Mojave Desert, surviving on ravens, vultures, pack rats and whatever else they could find, until they stumbled into the fertile Santa Clara River Valley. On January 1, 1850, vaqueros of Rancho San Francisco brought them to the main house, where Doña Jacoba Felix del Valle fed and clothed them and sent them back into the desert with supplies and oranges for the children. Manly\'s account of first seeing the valley — describing a beautiful meadow of a thousand acres green with grass and shaded by wide oaks, with hundreds of cattle calmly at rest — remains one of the most evocative firsthand descriptions of the rancho-era Santa Clara River Valley. He and Rogers returned to rescue the surviving thirteen adults and seven children of the Bennett-Arcan Party. As the survivors crested the Telescope Mountains and looked back, young Sara Bennett whispered "Good-bye, Death Valley" — and so it has been known ever since. Manly later settled in San Jose and published his memoir Death Valley in \'49 in 1894, a primary source document of enduring historical value.',
    'fullName' => 'William Lewis Manly',
    'birthDate' => 'April 6, 1820',
    'birthplace' => 'St. Albans, Vermont',
    'deathDate' => 'February 5, 1903',
    'burialPlace' => 'Oak Hill Cemetery, San Jose, California',
    'occupation' => 'Pioneer; Author',
    'authorBio' => 'William Lewis Manly was a Vermont-born pioneer who in late 1849 walked approximately 250 miles across the Mojave Desert to find help for the stranded Bennett-Arcan Party. Rescued by vaqueros of Rancho San Francisco on January 1, 1850, he left one of the most vivid firsthand descriptions of the Santa Clara River Valley in the rancho era. His memoir Death Valley in \'49 gave Death Valley its enduring name.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/1979/william-lewis-manly',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: William Lewis Manly\n";
} else {
    echo "FAILED: William Lewis Manly - " . json_encode($entry->getErrors()) . "\n";
}

// Cave Johnson Couts
$existing = \craft\elements\Entry::find()->section('persons')->slug('cave-johnson-couts')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Cave Johnson Couts\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Cave Johnson Couts';
$entry->slug = 'cave-johnson-couts';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('cave-johnson-couts-portrait-us-army.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Cave Johnson Couts was born in Smith County, Tennessee in 1821 and graduated from West Point in 1843. He served in the Mexican-American War and came to California as part of the military forces that followed the American conquest. His personal life took a decisive turn when Ysidora Bandini, the lively daughter of prominent Southern California ranchero Juan Bandini, tumbled from a rooftop railing into his arms while watching a column of cavalrymen pass through San Diego. They were soon married, making Couts the son-in-law of one of California\'s wealthiest landowners. When Couts\'s brother William returned from the northern mines with news of the miners\' desperate hunger for fresh beef, the brothers devised a plan to capitalize on the opportunity. In the spring of 1849 they drove approximately 700 head of cattle north on consignment from Juan Bandini and John Temple, ultimately selling at $20 a head in San Jose — roughly ten times the old hide-and-tallow value. The success of that drive helped open the California cattle trade that would enrich Californio rancheros throughout the early 1850s. Couts later acquired Rancho Guajome near Vista in San Diego County, which became one of the finest rancho estates in Southern California. He died in 1874 and is buried at El Campo Santo Cemetery in San Diego.',
    'fullName' => 'Cave Johnson Couts',
    'birthDate' => 'November 6, 1821',
    'birthplace' => 'Smith County, Tennessee',
    'deathDate' => 'June 10, 1874',
    'burialPlace' => 'El Campo Santo Cemetery, San Diego, California',
    'occupation' => 'Military Officer; Rancher',
    'authorBio' => 'Cave Johnson Couts was a Tennessee-born U.S. Army lieutenant who married into the Bandini family of Southern California and became one of the most significant figures in the early American cattle trade. In 1849 he and his brother William drove 700 head of cattle north through Newhall Pass to Gold Rush miners, selling at prices ten times the old hide-and-tallow rate.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/9372/cave-johnson-couts',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Cave Johnson Couts\n";
} else {
    echo "FAILED: Cave Johnson Couts - " . json_encode($entry->getErrors()) . "\n";
}

// Juan Bandini
$existing = \craft\elements\Entry::find()->section('persons')->slug('juan-bandini')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Juan Bandini\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Juan Bandini';
$entry->slug = 'juan-bandini';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Mexican Rancho Era (1821–1848)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('juan-bandini-portrait-c1850s.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Juan Bandini was born in Lima, Peru in 1800 and came to California as a young man, where he established himself as one of the most prominent Californio landowners and political figures in the San Diego region. He accumulated vast cattle holdings and real estate across Southern California and was a central figure in the political life of Mexican California, serving in the territorial legislature and supporting American annexation. His household became one of the social centers of San Diego, and his daughters were among the most celebrated women in Californio society. His daughter Ysidora\'s marriage to U.S. Army Lieutenant Cave Johnson Couts connected Bandini directly to the emerging American ranching economy. In 1849 Bandini provided cattle on consignment to Cave and William Couts for their landmark drive north to the Gold Rush mining camps — one of the first organized commercial cattle drives in California history. The success of that venture helped establish the cattle trade that would sustain Southern California\'s ranchero economy through the early 1850s. Despite his wealth and prominence, Bandini spent his later years in financial difficulty as American land law challenged the validity of Mexican land grants. He died in Los Angeles in 1859.',
    'fullName' => 'Juan Bandini',
    'birthDate' => '1800',
    'birthplace' => 'Lima, Peru',
    'deathDate' => 'November 4, 1859',
    'burialPlace' => 'l Campo Santo Cemetery, San Diego, California',
    'occupation' => 'Rancher; Politician; Merchant',
    'authorBio' => 'Juan Bandini was a Peru-born Californio ranchero and one of the most influential figures in Southern California during the Mexican and early American periods. His daughter Ysidora married U.S. Army Lieutenant Cave Johnson Couts, and Bandini provided the cattle that launched the Couts brothers\' pioneering 1849 drive to the Gold Rush mining camps.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/9366/juan-bandini',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Juan Bandini\n";
} else {
    echo "FAILED: Juan Bandini - " . json_encode($entry->getErrors()) . "\n";
}

// Edward Fitzgerald Beale
$existing = \craft\elements\Entry::find()->section('persons')->slug('edward-fitzgerald-beale')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Edward Fitzgerald Beale\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Edward Fitzgerald Beale';
$entry->slug = 'edward-fitzgerald-beale';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('American Frontier (1848–1876)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Lebec')->one(); if($cat) $hoodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Tejon')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('edward-fitzgerald-beale-portrait.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Edward Fitzgerald Beale was born in Washington, D.C. in 1822 and entered the U.S. Navy as a midshipman at age fifteen. He rose to national fame during the Mexican-American War when he made a series of extraordinarily dangerous dispatch rides — including crawling through enemy lines at the Battle of San Pasqual with Kit Carson to bring reinforcements to the besieged forces of General Kearny. He carried the first confirmed samples of California gold to Washington in 1848, helping trigger the California Gold Rush, and made multiple overland crossings of the continent at a time when such journeys were genuinely life-threatening. In 1853 he was appointed Superintendent of Indian Affairs for California and Nevada, a role that brought him into close contact with the indigenous peoples of the San Joaquin Valley and the Tehachapi region. In 1855 he acquired Rancho El Tejon and surrounding land grants, eventually consolidating some 270,000 acres into what became known as the Tejon Ranch — one of the largest private landholdings in California history. Fort Tejon, established by the U.S. Army on adjacent land, served as the base for Beale\'s most colorful venture: the U.S. Army Camel Corps experiment of 1857–1858, in which he successfully demonstrated that camels could be used as pack animals across the Southwestern desert. He later surveyed the 35th Parallel route from Fort Defiance to the Colorado River, a path that would influence railroad planning for decades. Beale served as U.S. Minister to Austria-Hungary under President Grant and spent his later years between Washington and the Tejon Ranch. He died in 1893. His landholdings at El Tejon placed him squarely in the geography of the Santa Clarita Valley\'s northern frontier, and his road-building and military activities along the Tehachapi corridor directly shaped the development of the region.',
    'fullName' => 'Edward Fitzgerald Beale',
    'birthDate' => 'February 4, 1822',
    'birthplace' => 'Washington, D.C.',
    'deathDate' => 'April 22, 1893',
    'burialPlace' => 'Rock Creek Cemetery, Washington, D.C.',
    'occupation' => 'Naval Officer; Explorer; Surveyor; Rancher; Diplomat',
    'authorBio' => 'Edward Fitzgerald Beale was a Washington-born naval officer, explorer, and surveyor who acquired Rancho El Tejon in 1855 and consolidated it into one of the largest private landholdings in California history. He is remembered for his daring dispatch rides during the Mexican-American War, his role surveying the 35th Parallel route through the Southwest, and his management of the U.S. Army\'s celebrated Camel Corps experiment at Fort Tejon.',
    'personLegacyUrl' => '',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/5144/edward-fitzgerald-beale',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Edward Fitzgerald Beale\n";
} else {
    echo "FAILED: Edward Fitzgerald Beale - " . json_encode($entry->getErrors()) . "\n";
}

// José Antonio Aguirre
$existing = \craft\elements\Entry::find()->section('persons')->slug('jose-antonio-aguirre')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: José Antonio Aguirre\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'José Antonio Aguirre';
$entry->slug = 'jose-antonio-aguirre';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Mexican Rancho Era (1821–1848)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Tejon')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('jose-antonio-aguirre-portrait-san-diego-history.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'José Antonio Aguirre was born in Bilbao in the Basque Country of Spain in 1793 and arrived in California via the Pacific hide-and-tallow trade, establishing himself as one of the most prosperous merchants in San Diego. He operated a fleet of trading vessels along the California coast and accumulated significant landholdings during the Mexican period. In 1843 he was granted Rancho El Tejon jointly with Ygnacio del Valle, a vast tract at the Tehachapi Pass straddling the boundary between Southern California and the San Joaquin Valley. The rancho\'s strategic position on the main overland route made it valuable both as grazing land and as a waypoint for the cattle drives that would follow the Gold Rush. Aguirre\'s mercantile interests kept him primarily based in San Diego rather than on the rancho itself, and his connection to El Tejon was primarily financial. He died in San Diego in 1860.',
    'fullName' => 'José Antonio Aguirre',
    'birthDate' => '1793',
    'birthplace' => 'Bilbao, Basque Country, Spain',
    'deathDate' => 'June 4, 1860',
    'burialPlace' => '',
    'occupation' => 'Merchant; Rancher; Ship Owner',
    'authorBio' => 'José Antonio Aguirre was a Basque-born merchant and ship owner who became one of the wealthiest traders in Mexican California. He was co-grantee of Rancho El Tejon in 1843 alongside Ygnacio del Valle.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: José Antonio Aguirre\n";
} else {
    echo "FAILED: José Antonio Aguirre - " . json_encode($entry->getErrors()) . "\n";
}

// Henry Clay Wiley
$existing = \craft\elements\Entry::find()->section('persons')->slug('henry-clay-wiley')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Henry Clay Wiley\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Henry Clay Wiley';
$entry->slug = 'henry-clay-wiley';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Railroad &amp; Oil Era (1876–1910)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('henry-clay-wiley-portrait.png')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Henry Clay Wiley was born in Lancaster, Pennsylvania in 1829 and received his early education there and later in Indianapolis, where his family relocated. His father was a merchant tailor and a prominent figure in Indianapolis society. At eighteen Wiley joined the commissary department of the U.S. Army and served through the Mexican-American War, afterward spending several years traveling and living along the Pacific coast states of Mexico. He arrived in San Diego in 1852 and served as a constable for San Diego County — though later accounts, including his own obituary in the Los Angeles Herald, incorrectly elevated this role to sheriff. As local historian Stan Walker notes, Wiley was not among the seven different sheriffs San Diego County had in the 1850s; he served instead as a constable. From 1858 to 1862 Wiley served as general manager of Mission San Fernando. In late 1852 he had already moved north to the Santa Clarita Valley and entered into a partnership with Ygnacio del Valle to establish Wiley Station at the crest of the pass between the San Fernando Valley and the Santa Clara River Valley. The station included a substantial wood-frame hotel, restaurant, and saloon, and Wiley\'s signature innovation was a massive windlass capable of lowering wagons and livestock down the steep grade — for a fee. The station became an essential and colorful waypoint for the heavy cattle drive traffic passing through Newhall Pass during the Gold Rush boom years. In the 1860s Wiley relocated permanently to Los Angeles County, where he served as Under-Sheriff under J.F. Burns from 1868 to 1872. In 1872 he formed a real estate partnership with D.M. Berry, and together they served as resident agents for the Indiana Colony — the settlement that became Pasadena. Wiley was a charter member of the Los Angeles Society of Veterans of the Mexican War, organized in 1873, and received a special bronze medal from the society in April 1876, cast from cannon captured in Mexico City during the war. He was also a charter member of the Southern California Pioneer Society and a Knight of Pythias. He owned considerable Los Angeles real estate including 120-foot frontage on Broadway between First and Second streets, and was recognized as one of the early civic boosters who foresaw Los Angeles\'s growth into a great city. He died October 25, 1898 at his residence at 309 South Hill Street, Los Angeles, survived by his widow and two daughters. Interment was at Rosedale Cemetery. Wiley Canyon in Newhall and Wiley Canyon Elementary School bear his name.',
    'fullName' => 'Henry Clay Wiley',
    'birthDate' => '1829',
    'birthplace' => 'Lancaster, Pennsylvania',
    'deathDate' => 'October 25, 1898',
    'burialPlace' => 'Rosedale Cemetery, Los Angeles, California',
    'occupation' => 'Entrepreneur; Lawman; Real Estate Agent',
    'authorBio' => 'Henry Clay Wiley was a Pennsylvania-born pioneer who arrived in California in 1852 and established Wiley Station at Newhall Pass in partnership with Ygnacio del Valle — a hotel, restaurant, saloon, and windlass operation that became a key waypoint on the cattle drive route between Southern California and the San Joaquin Valley. He later served as Under-Sheriff of Los Angeles County and helped found the Indiana Colony, now Pasadena.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Henry Clay Wiley\n";
} else {
    echo "FAILED: Henry Clay Wiley - " . json_encode($entry->getErrors()) . "\n";
}

// Arthur B. Perkins
$existing = \craft\elements\Entry::find()->section('persons')->slug('arthur-b-perkins')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Arthur B. Perkins\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Arthur B. Perkins';
$entry->slug = 'arthur-b-perkins';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Postwar Boom (1945–1965)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1920-1929')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1930-1939')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1940-1949')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1950-1959')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1960-1969')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1970-1979')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('arthur-b-perkins-outstanding-citizen-newhall-1964.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Arthur B. "Perk" Perkins was born in 1891 and arrived in Newhall in 1919 to manage the Newhall Water Company, a small private utility serving the growing community at the base of the San Fernando Pass. Under his management, the company expanded to meet the demands of a valley that was beginning its long transformation from ranching country to suburb. Beyond his work in water, Perkins embedded himself in the civic fabric of Newhall. He served as justice of the peace — the community\'s local judge — throughout the 1920s, organized Newhall\'s first Fourth of July parades, built homes, and sold real estate during the valley\'s early growth. He was the kind of man a small town runs on. The Newhall Water Company was eventually reorganized as the Newhall County Water District, a public agency that continues to serve the valley today. Perkins remained connected to its operations through much of his working life. Upon retirement, Perkins turned his full attention to history. He began collecting every document, photograph, deed, newspaper clipping, and personal account of Santa Clarita Valley life he could find — from the rancho era forward. In 1957, he published a manuscript on the history of the valley. In 1962, he wrote a series of articles for The Signal newspaper titled "Story of Our Valley," drawing on his decades of collecting. He was consulted on local historical matters until his death in 1977. What Perkins assembled became the foundation on which later historians — including Leon Worden — built. When Worden launched SCVHistory.com in 1997, Perkins\'s collected materials were among its earliest and most valuable sources. Without A.B. Perkins, much of what is documented on this site would simply not exist.',
    'fullName' => 'Arthur Burnett Perkins',
    'birthDate' => '1891',
    'birthplace' => '(unknown)',
    'deathDate' => '1977',
    'burialPlace' => '(unknown)',
    'occupation' => 'Historian · Water Company Manager · Justice of the Peace · Real Estate Agent · Civic Leader',
    'authorBio' => 'Arthur B. "Perk" Perkins (1891–1977) managed the Newhall Water Company beginning in 1919 and spent his retirement preserving the history of the Santa Clarita Valley. His collected documents and photographs form the backbone of SCVHistory.com.',
    'personLegacyUrl' => '/scvhistory/lw2232.htm',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Arthur B. Perkins\n";
} else {
    echo "FAILED: Arthur B. Perkins - " . json_encode($entry->getErrors()) . "\n";
}

// Scott Wilk
$existing = \craft\elements\Entry::find()->section('persons')->slug('scott-wilk')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Scott Wilk\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Scott Wilk';
$entry->slug = 'scott-wilk';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Contemporary (2010–Present)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('2000-2009')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('2010-2019')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('2020-Present')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Saugus')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('about-scott-wilk-california-assembly.png')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Scott Thomas Wilk Sr. was born on March 16, 1959 and raised in the Santa Clarita Valley — the same district he would go on to represent for more than a decade in the California State Legislature. Wilk came up through local civic life. He served on the staff of state legislators Tom McClintock, Paula Boland, and Howard McKeon before launching his own communications firm, Liaison Consulting. He was the first in his family to graduate from college, earning a B.A. in political science from California State University, Bakersfield. His path back to local public service ran through College of the Canyons — he was appointed to the Santa Clarita Community College District Board of Trustees on August 23, 2006, and subsequently won election to that seat in 2007 and 2011. He served on the boards of the Henry Mayo Newhall Memorial Hospital Foundation and the Santa Clarita Valley Chamber of Commerce\'s Legislative Committee. In 2012, Wilk won election to the California State Assembly representing the 38th District. Wikipedia. He served two terms there before winning an election to the State Senate in 2016 to represent the 21st District, which encompasses the Santa Clarita, Antelope, and Victor valleys. He was re-elected in 2020. On January 20, 2021, Wilk was elected Senate Republican Leader by his caucus, a recognition of his reputation for building coalitions across party lines in a chamber where Republicans held just nine of forty seats. Among his most significant legislative achievements for the Santa Clarita Valley was authoring Senate Bill 634. Following a multi-year public engagement process, SB 634 was signed by Governor Jerry Brown on October 15, 2017, and on January 1, 2018, formally consolidated the Castaic Lake Water Agency, Newhall County Water District, Santa Clarita Water Division, and Valencia Water Company into a single regional agency — the Santa Clarita Valley Water Agency. LinkedIn Unifying four separate bureaucracies into one coherent regional authority was a significant achievement that has already produced cost savings and improved water management for the roughly 300,000 residents SCV Water now serves. Wilk\'s most personal legislative cause has been the recognition of the Armenian Genocide. His wife, Vanessa Safoyan Wilk, is Armenian-American, and Wilk brought that family connection into his public service with moral clarity and consistency. He co-founded the California Armenian Legislative Caucus in 2015, the Santa Clarita Valley Signal, to ensure that Armenian-American voices had a platform in Sacramento. Year after year he introduced resolutions on the Senate floor commemorating the genocide — condemning the Turkish government\'s refusal to acknowledge their atrocities, raising awareness of ongoing threats to Armenia, and championing education to prevent future genocides. Santa Clarita Valley Signal. In 2022, Wilk and Senator Portantino introduced SB 457, requiring California\'s public employee retirement systems to divest from investments in the Turkish government, should federal sanctions be enacted. ANCA Western Region — legislation that was signed by Governor Newsom. For this work, the Armenian National Committee of America – Western Region honored Wilk with its Legislator of the Year Award in 2022 . In a political era defined by polarization, Wilk consistently drew endorsements from unlikely corners — including AFSCME Local 3299, the California Professional Firefighters, and the Armenian National Committee of America-Western Region Wikipedia — alongside more expected conservative organizations. In 2023 he was named Co-Chair of the Problem Solvers Caucus, a bicameral, bipartisan group of legislators who prioritize finding meaningful solutions over party ideology. CA The Signal named Wilk the most influential person in the Santa Clarita Valley in 2021. He and Vanessa reside in Santa Clarita and have two adult children.',
    'fullName' => 'Scott Thomas Wilk Sr.',
    'birthDate' => 'March 16, 1959',
    'birthplace' => 'Lancaster, California',
    'deathDate' => '',
    'burialPlace' => '',
    'occupation' => 'State Senator · State Assemblymember · College Trustee · Small Business Owner · Political Staffer',
    'authorBio' => 'Scott Wilk represented California\'s 21st Senate District from 2016 to 2024, serving as Senate Republican Leader and authoring the legislation that created the Santa Clarita Valley Water Agency. A co-founder of the California Armenian Legislative Caucus, he was a consistent voice for Armenian Genocide recognition throughout his tenure.',
    'personLegacyUrl' => '',
    'personGraveUrl' => '',
    'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Scott_Wilk',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Scott Wilk\n";
} else {
    echo "FAILED: Scott Wilk - " . json_encode($entry->getErrors()) . "\n";
}

// John Gifford
$existing = \craft\elements\Entry::find()->section('persons')->slug('john-gifford')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: John Gifford\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'John Gifford';
$entry->slug = 'john-gifford';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Railroad &amp; Oil Era (1876–1910)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1900-1919')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1920-1929')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Newhall')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('gifford-john-timothy-portrait-1870s-scvhs-scaled.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'John Timothy Gifford was born February 14, 1847, in Ohio, to parents of Pennsylvania origin. He first appears in local records in 1871 as a Western Union line rider covering the territory from Los Angeles to Lake Elizabeth. In 1875, Gifford was placed in charge of the north-side crew excavating the Southern Pacific Railroad\'s 6,940-foot San Fernando tunnel through Railroad Canyon. While his crew tunneled south, a second crew tunneled north. The two met in the middle on July 15, 1876, and Gifford — drawing on his Western Union experience — tapped out the news from Lyon\'s Station: "Daylight shines through the San Fernando Tunnel." When the first locomotive passed through on August 12, 1876, Gifford sent a second message: "The iron horse poke its head through the San Fernando Tunnel this evening at six o\'clock and neighed long and loud his hearty greeting to the citizens of the Santa Clara Valley." The Southern Pacific Newhall Depot opened for business on September 6, 1876, the day after Charles Crocker drove the golden spike at Lang Station to open the line. The depot — a small, yellow structure with brown trim — stood at what is today Bouquet Canyon Road and Magic Mountain Parkway, roughly where Saugus would be established twelve years later. The Gifford family\'s first home in Newhall was a boxcar parked on a siding. Gifford had married the English-born Sarah Beckwith in San Diego in 1875. Their daughter Mabel, born on November 3, 1875, was the first child to be born in Newhall. A son, Edwin E. "Eddie," was born December 3, 1878, but died one day before his seventh birthday on December 2, 1885, of scarlet fever. When drought dried up the wells at the original townsite in early 1878, the community relocated to today\'s downtown Newhall. A larger depot was built at the northeast corner of Market Street and Railroad Avenue — the same location where the Jan Heidt Newhall Metrolink Station would later stand. The Giffords purchased two lots for $125 and moved into a new board-and-batten home east of the depot on April 16, 1884. The house stood at the northern end of Pine Street and survived into the 1960s before being demolished to make way for an Anawalt Lumber parking lot. Today, the site is part of the Newhall Community Center parking lot. Gifford served simultaneously as the local agent for Wells, Fargo &amp; Co. and for the Telegraph Stage Company, both operating out of the depot. He retired as the SP\'s Newhall telegraph operator in 1912 and received a monthly railroad pension thereafter — $27.30 in September 1919, for example. John Gifford died on October 15, 1922. His wife, Sarah, remained active in civic life after his death. On December 6, 1944, she christened the S.S. Newhall Hills, a 16,000-ton Navy oil tanker. Sarah died on March 6, 1945. Both are buried beneath an upright family headstone in the Garden of Pioneers at Eternal Valley Cemetery — almost exactly where John Gifford had tapped out his tunnel telegraph dispatch 46 years earlier. Their daughter, Mabel, and her husband, Louis B. Taylor (Jan. 2, 1873 – Sept. 12, 1951), share the family plot.',
    'fullName' => 'John Timothy Gifford',
    'birthDate' => 'February 14, 1847',
    'birthplace' => 'Cincinnati, Ohio (1880 and 1920 Census; 1910 Census says New York — Ohio preferred as it appears most consistently)',
    'deathDate' => 'October 15, 1922',
    'burialPlace' => 'Garden of Pioneers, Eternal Valley Cemetery, Newhall',
    'occupation' => '',
    'authorBio' => 'Newhall\'s first Southern Pacific railroad agent and telegraph operator, who supervised the north-side crew during construction of the San Fernando tunnel and served the Newhall depot from its opening in 1876 until his retirement in 1912.',
    'personLegacyUrl' => '/scvhistory/hs2001.htm',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: John Gifford\n";
} else {
    echo "FAILED: John Gifford - " . json_encode($entry->getErrors()) . "\n";
}

// Rémi Nadeau (I)
$existing = \craft\elements\Entry::find()->section('persons')->slug('remi-nadeau-i')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Rémi Nadeau (I)\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Rémi Nadeau (I)';
$entry->slug = 'remi-nadeau-i';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Railroad &amp; Oil Era (1876–1910)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('1850-1899')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('Pre-1850')->one(); if($cat) $periodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('nadeau-remi-i-portrait-citymakers-1977-p65-scaled.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Rémi Nadeau was born on September 21, 1821, in Kamouraska, Quebec, Canada. He emigrated to New Hampshire, where he married Martha Flanders-Frye in 1844, then moved to Chicago and Minnesota, where he worked as a miller. In 1861, he crossed the plains, stopping in Salt Lake City to build one or two mills before settling in Los Angeles. With a loan from real estate agent Prudent Beaudry — future Los Angeles mayor (1874–1876) — Nadeau purchased mules and wagons and established a freighting company. His early major hauls were the 700-mile, 35-day trek to Salt Lake City. That route declined in 1868 with the arrival of the transcontinental railroad, and Nadeau pivoted to transporting silver ingots from the Cerro Gordo Mines near Keeler, on the east shore of Owens Lake. In December 1868, mining engineer Mortimer Belshaw and Victor Beaudry (Prudent\'s brother) contracted Nadeau to transport ore to Los Angeles. Thirty-two teams moved $50,000 worth of silver and lead daily — only half of peak production — down the Yellow Grade Road on a three-week journey to the city\'s refinery. Nadeau is credited with pioneering the use of 20-mule teams for heavy freight. His operation accounted for roughly a quarter of Los Angeles\' exports during the 1870s, making him the dominant freighter in Southern California from 1869 to 1882. His routes passed through Soledad Canyon, with teams stopping at Lang Station beginning in 1873. Teamsters had to watch for highwaymen, including Tiburcio Vasquez and his lieutenant Cleovaro Chavez, who preyed on freight traffic along the road. In June 1873, Nadeau agreed to return to Cerro Gordo on condition that mine owners invest $150,000 in new stations along the route. They accepted, building stops at Coyote Holes, Red Rock Canyon, Forks-of-the-Road, Cow Holes, and Barrel Springs. He became a full partner in the Cerro Gordo Freighting Company at John Lang\'s spread in Soledad Canyon. The operation also branched into borax transport from mining camps in Columbus, Nevada, and later extended into Tombstone and Wickenburg, Arizona. The Cerro Gordo Freighting Company dissolved in 1882 as mule-team transport became obsolete. Nadeau then invested in sugar beets, wine grapes, barley, and downtown Los Angeles real estate. In 1886 he built the four-story Nadeau Hotel — the most ambitious hotel the city had seen — at a cost of $165,000. Today the site is Times Mirror Square. Rémi Nadeau died January 15, 1887, survived by his wife Martha and four children: Joseph Frye (Los Angeles), Georges Albert (Florence), Minnie J.F. Tilton (Concord, New Hampshire), and Mary-Rose Bell (Los Angeles).',
    'fullName' => 'Remi Allen Nadeau',
    'birthDate' => 'September 21, 1821',
    'birthplace' => 'Kamouraska, Quebec, Canada',
    'deathDate' => 'January 15, 1887',
    'burialPlace' => 'Los Angeles, California',
    'occupation' => 'Freighter, Hotelier',
    'authorBio' => 'French-Canadian freighter who operated Southern California\'s largest mule-team freight operation between Los Angeles and the Cerro Gordo silver mines from 1869 to 1882. Later built the Nadeau Hotel in downtown Los Angeles.',
    'personLegacyUrl' => '/scvhistory/henriettenadeau20171119_en.htm',
    'personGraveUrl' => '',
    'personWikipediaUrl' => '',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Rémi Nadeau (I)\n";
} else {
    echo "FAILED: Rémi Nadeau (I) - " . json_encode($entry->getErrors()) . "\n";
}

// Dante Acosta
$existing = \craft\elements\Entry::find()->section('persons')->slug('dante-acosta')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Dante Acosta\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Dante Acosta';
$entry->slug = 'dante-acosta';
$eraIds = []; $periodIds = []; $hoodIds = [];
$cat = \craft\elements\Category::find()->group('historicalEra')->title('Contemporary (2010–Present)')->one(); if($cat) $eraIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('historicalPeriod')->title('2010-2019')->one(); if($cat) $periodIds[] = $cat->id;
$cat = \craft\elements\Category::find()->group('neighborhood')->title('Canyon Country')->one(); if($cat) $hoodIds[] = $cat->id;
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('dante-acosta.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Dante Acosta (b. January 1, 1963) is an American politician, businessman, and community leader who served on the Santa Clarita City Council from 2014 to 2016 and represented California\'s 38th Assembly District in the State Legislature from 2016 to 2018. A longtime Canyon Country resident, he is the son of Hollywood character actor Rodolfo Acosta and the father of Army Specialist Rudy Alexander Acosta, who was killed in action in Afghanistan in 2011. Born in Sacramento, Acosta was raised in Southern California and attended California State University, Northridge before earning a Bachelor of Business Administration from the University of Phoenix. His early career was in the automotive industry, where he worked his way up from sales to General Sales Manager at a San Fernando Valley Chevrolet dealership, leading his store to consistently top rankings in customer satisfaction across Southern California. He later transitioned to financial services, spending more than two decades as a financial advisor and manager with firms including Dean Witter, Wells Fargo Investments, Prudential Financial, The Hartford, ING Financial Services, and Ameriprise Financial, advising individuals and small business owners on insurance, investments, retirement, and estate planning. Acosta and his wife, Carolyn, moved to Santa Clarita more than 27 years ago, drawn by the community\'s reputation as a safe and family-oriented place to live. They raised three children — Rudy, Alexandra, and Doran — and became deeply involved in civic life. Dante served with the Santa Clarita Valley Rotary Club, the Old Town Newhall Association, Circle of Hope Inc., and the North Los Angeles County Republican National Hispanic Assembly. He and Carolyn volunteered as docents at the William S. Hart Museum and coached Little League and drama. He also served on the City Council\'s Economic Development Committee, Film and Tourism Committee, and as Santa Clarita\'s representative to the Southern California Association of Governments. Elected to the Santa Clarita City Council in November 2014, Acosta ran for the open 38th Assembly District seat in 2016 following Assemblyman Scott Wilk\'s move to the State Senate. The district encompassed the Santa Clarita Valley, far northern San Fernando Valley, and Simi Valley. Acosta won the November 2016 election and served one term in Sacramento, where he focused on bipartisan legislation and served as vice chair of the Natural Resources Committee. He worked to amend major environmental legislation and pursued bills addressing workforce development and elder care. He was narrowly defeated for reelection in 2018 by Democrat Christy Smith in a rematch of their 2016 race. Following his loss, Acosta was nominated by Los Angeles County Supervisor Kathryn Barger to the Santa Clarita Valley Water Agency board of directors, representing Val Verde. He resigned from that position in August 2019 upon relocating to El Paso, Texas, to serve as district director of the U.S. Small Business Administration. The defining personal experience of Dante Acosta\'s public life has been the loss of his eldest son. Specialist Rudy Alexander Acosta, serving as an Army Medic with the 4th Squadron, 2nd Stryker Cavalry Regiment in Kandahar Province, Afghanistan, was fatally shot by a rogue Afghan security soldier on March 19, 2011, at the age of 20. In his son\'s memory, Dante founded the Rudy A. Acosta Memorial Foundation and joined the fundraising committee for Portraits of the Fallen, a project honoring California\'s fallen service members from the Iraq and Afghanistan wars. He and Carolyn remain active as Gold Star Family members and advocates for military families throughout the Santa Clarita Valley.',
    'fullName' => 'Dante Acosta',
    'birthDate' => 'January 1, 1963',
    'birthplace' => 'Sacramento, California',
    'deathDate' => '',
    'burialPlace' => '',
    'occupation' => 'Politician; Financial Advisor; City Council Member; State Assemblymember',
    'authorBio' => 'Dante Acosta is a former Santa Clarita City Council member (2014–2016) and California State Assemblyman representing the 38th District (2016–2018). A Canyon Country resident and Gold Star father, he is the founder of the Rudy A. Acosta Memorial Foundation.',
    'personLegacyUrl' => '/scvhistory/sc1401.htm',
    'personGraveUrl' => '',
    'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Dante_Acosta',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Dante Acosta\n";
} else {
    echo "FAILED: Dante Acosta - " . json_encode($entry->getErrors()) . "\n";
}

// Rodolfo Acosta
$existing = \craft\elements\Entry::find()->section('persons')->slug('rodolfo-acosta')->one();
if($existing) { Craft::$app->getElements()->deleteElement($existing); echo "Deleted existing: Rodolfo Acosta\n"; }
$entry = new \craft\elements\Entry();
$entry->sectionId = $svc->getSectionByHandle('persons')->id;
$entry->typeId = Craft::$app->getEntries()->getEntryTypeByHandle('person')->id;
$entry->title = 'Rodolfo Acosta';
$entry->slug = 'rodolfo-acosta';
$eraIds = []; $periodIds = []; $hoodIds = [];
$asset = \craft\elements\Asset::find()->volume('archiveMedia')->filename('rodolfo_acosta_in_one-eyed_jacks.jpg')->one();
$featuredImageIds = \$asset ? [\$asset->id] : [];
$entry->setFieldValues([
    'body' => 'Rodolfo Pérez Acosta (July 29, 1920 – November 7, 1974) was an American actor who became known for his roles as Mexican outlaws and American Indians in Hollywood western films. Born in the El Segundo Barrio of El Paso, Texas, he was raised in Los Angeles where he graduated from Abraham Lincoln High School. He studied drama at Los Angeles City College and UCLA, appeared at the Pasadena Playhouse, and received a scholarship to the Palacio de Bellas Artes in Mexico City. During World War II he served as a Yeoman in U.S. Naval Intelligence. His film career began with a role in John Ford\'s 1947 film The Fugitive, and he earned an Ariel Award nomination for Best Supporting Actor for the 1949 film Salón México. Though considered a romantic screen idol in Mexico and South America, his burly build led to decades of roles as western antagonists in Hollywood. He was a regular as Vaquero on NBC\'s The High Chaparral from 1967–1969. In 2013 he was inducted posthumously into Newhall\'s Walk of Western Stars during the Santa Clarita Cowboy Festival. He died of liver cancer on November 7, 1974, at the Motion Picture and Television Country House in Woodland Hills, California.',
    'fullName' => 'Rodolfo Acosta',
    'birthDate' => 'July 29, 1920',
    'birthplace' => 'El Paso, Texas',
    'deathDate' => 'November 7, 1974',
    'burialPlace' => 'Forest Lawn Memorial Park, Hollywood Hills, California',
    'occupation' => 'Actor',
    'authorBio' => 'Rodolfo Acosta was a Mexican-American actor best known for playing villains and Native American warriors in Hollywood westerns. Despite being a romantic lead in Mexican cinema, his powerful physique typecast him in antagonist roles for American audiences. He appeared in over 50 films and dozens of television westerns including The High Chaparral, Bonanza, and Rawhide. He was inducted into Newhall\'s Walk of Western Stars in 2013.',
    'personLegacyUrl' => '/scvhistory/lw3399.htm',
    'personGraveUrl' => 'https://www.findagrave.com/memorial/19542/rodolfo-acosta',
    'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Rodolfo_Acosta',
    'historicalEra' => $eraIds,
    'historicalPeriod' => $periodIds,
    'neighborhood' => $hoodIds,
    'featuredImage' => $featuredImageIds,
]);
if (Craft::$app->getElements()->saveElement($entry)) {
    echo "Saved: Rodolfo Acosta\n";
} else {
    echo "FAILED: Rodolfo Acosta - " . json_encode($entry->getErrors()) . "\n";
}
echo "Done.\n";