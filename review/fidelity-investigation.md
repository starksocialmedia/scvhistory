# Where the added text came from

Generated 19 September 2026, 11:54am by `scripts/import/investigate_added_text.php`. Read only.

24 records carry lines the legacy page does not. Each of those lines is tested
against the body of the matching WordPress post in `inventory/wp_content.json`,
matched by slug. A line found there was written in WordPress before the Craft
migration and is content. A line found in neither entered during one of our own
passes and is a bug.

Comparison is on words alone, case, spacing and punctuation ignored, against the
whole WordPress body flattened to one line, because WordPress wraps paragraphs
differently from the extraction. Lines longer than fourteen words are probed on
their first fourteen, so a line the editor lightly rewrote still matches.

## The answer

| | records | lines |
|---|---|---|
| every added line is in the WordPress body | 21 | 71 |
| no added line is in the WordPress body | 3 | 8 |
| some of each | 0 | 0 |
| **total** | **24** | **79** |

Lines in the WordPress body: **71**. Lines in neither: **8**.

3 of the records have no WordPress post at all under their slug, so for
those the test can only say the text is not in WordPress, not that it never was:
- `warMemorials/terror-rudyacosta` (warmemorial)
- `warMemorials/ww2-augustrubel` (warmemorial)
- `warMemorials/ww2-robertcone` (warmemorial)

## CONTENT: written in WordPress — 21 records

### `articles/chapter-9-the-trail-blazer` #837

- legacy: `reynolds-full / part09`
- WordPress post: 421 Chapter 9. The Trail Blazer
- added lines: 6 (6 in WordPress, 0 in neither)

In the WordPress body:

- captain pedro fages c 1734 1796 catalonian soldier and governor of alta california he named agua dulce and soledad during his 1772 expedition through the santa clarita valley
- capt pedro fages was the commanding officer of the presidio of san diego before heading to monterey in 1770 to assume command as governor of alta california in that capacity he made several expeditions into the hinterlan
- a tall aristocratic spaniard in his early thirties fages was a lieutenant in charge of the twenty five catalonian soldiers during the portolá march to monterey he was considered a trailblazer possessed of an inquisitive 
- he must have had mixed feelings that spring morning in 1772 when it was discovered that six of his soldados de cuero had deserted with an equal number of comely young indian maidens fages was upset by the loss of his lan
- don pedro renewed his acquaintance with the venerable kika or chief of the tataviam community and was led to believe that the men he was looking for were somewhere up in castaic canyon
- capt fages returned to san diego after the epic trek and filed a detailed account of his pursuit adding thousands of acres to previously blank maps in 1777 he fought apaches on the sonoran frontier returning as a lieuten

### `articles/chapter-12-staking-claim` #843

- legacy: `reynolds-full / part12`
- WordPress post: 447 Chapter 12. Staking Claim
- added lines: 6 (6 in WordPress, 0 in neither)

In the WordPress body:

- early in 1804 the padres at mission san fernando learned that a santa barbaran named francisco avila had claimed several thousand acres east of piru creek along the banks of the rio santa clara and named his holdings cam
- the padres protested vigorously notifying governor josé arrillaga at monterey that these lands belonged to the church after due study governor arrillaga acknowledged the mission s title and rescinded avila s grant to for
- st francis san francisco xavier lived from 1506 to 1552 the patron of foreign missions and apostle of the indies he helped found the society of jesus the jesuit order with st ignatius in 1537 the jesuits had been expelle
- exactly when the granary was raised to the status of an asistencia or submission is not known it probably happened when the new wing and tiled sacristy were authorized but when that occurred is a mystery
- during september 1821 father ibarra wrote from san fernando that rabbits and hares and worms have done great damage to the crop at the rancho san francisco in spite of these depradations fifteen pack mules left the ranch
- while the padres and the tataviam neophytes quietly tended to their cattle and crops at the lonely isolated asistencia de san francisco revolution was raging in new spain if they knew what was going on they probably didn

### `articles/chapter-14-lord-and-master` #847

- legacy: `reynolds-full / part14`
- WordPress post: 456 Chapter 14. Lord and Master
- added lines: 6 (6 in WordPress, 0 in neither)

In the WordPress body:

- diseño map of the rancho san francisco santa clarita valley drawn by pablo de la guerra of santa barbara ca 1843 the rancho comprised eleven leagues 48 829 acres granted to antonio del valle by governor juan alvarado on 
- antonio seferino del valle had not seen his son ygnacio for six years so when the lad of seventeen stepped off the ship at monterey on july 27 1825 the reunion must have been joyful but the differences of opinion between
- at antonio s request a map or diseño was drawn by pablo de la guerra of santa barbara it showed the rancho san francisco comprising eleven leagues or 48 829 acres one citizen protested that the land should go to the indi
- ignoring the protests alvarado sat at his desk in santa barbara on january 22 1839 and with his signature made antonio del valle virtual lord and master of the upper santa clara river valley the new ranchero joined the l
- dr nicholas den the family physician was sent with a personal message to santa barbara where young ygnacio was in residence if the son would settle down and marry don antonio would give him a half interest in a house at 
- by the time poor dr den could return with a response for the fifty three year old ranchero don antonio had died intestate

### `articles/chapter-16-golden-dreams` #853

- legacy: `reynolds-full / part16`
- WordPress post: 567 Chapter 16. Golden Dreams
- added lines: 6 (6 in WordPress, 0 in neither)

In the WordPress body:

- ygnacio del valle 1808 1880 owner of rancho san francisco and rancho camulos in the santa clarita valley photo courtesy los angeles public library el pueblo collection public domain
- spring was the time of year when friends and relatives of the great landowners would gather for the annual cattle roundup it was a colorful spectacle of barbecues dashing vaqueros on richly caparisoned steeds and flirtat
- within a few months california s first gold rush was underway most of the two thousand miners came from the mexican state of sonora they literally tore up the canyon which was by then renamed placerita
- just as during the gold strike at sutter s mill six years later there were claim jumpers shoot outs and swindlers on may 3 1842 governor santiago arguello named ygnacio del valle encargado de justicia del placer de ranch
- by november two hundred ounces had been exported from rancho san francisco del valle kept accurate records sorted out the often conflicting claims and recorded that 125 pounds of ore were quarried before the mines petere
- the placerita rush was overshadowed by the better known find on the american river yet its place in history as the first gold rush in california is secure mfn archaeologists tend to agree that while the lopez find was th

### `articles/preface-history-of-the-santa-clarita-valley` #817

- legacy: `reynolds-full / preface`
- WordPress post: 301 Preface
- added lines: 5 (5 in WordPress, 0 in neither)

In the WordPress body:

- jerry reynolds 1937 1996 founding curator of the scv historical society and author of santa clarita valley of the golden dream 1992
- but it is more than that much more the consummate storyteller reynolds brings the valley s history to life so that the reader just knows the author must have been there witnessing the events as they unfolded prepare to r
- the stories in this volume formed the backbone of reynolds original santa clarita valley of the golden dream published in 1992 by the santa clarita valley chamber of commerce while the two versions are similar in many re
- other works to reynolds credit include a heritage to keep 1976 pico canyon chronicles 1985 and various magazine articles
- born gerald g reynolds in torrance california on july 16 1937 jerry studied art history at long beach state college and worked as a private investigator until landing his dream job as a tour guide at william randolph hea

### `articles/chapter-18-the-pathfinder` #857

- legacy: `reynolds-full / part18`
- WordPress post: 605 Chapter 18. The Pathfinder
- added lines: 5 (5 in WordPress, 0 in neither)

In the WordPress body:

- campo de cahuenga
- campo de cahuenga memorial to the treaty of cahuenga by which the united states acquired california fremont and pico signed the treaty jan 13 1847 at this location now 3919 lankershim blvd across the street from universa
- most californios were dissatisfied with mexican rule and were willing to hand over the province peacefully until frémont fortified hawk s peak in the gavilan mountains the bear flaggers made things worse when they kidnap
- edwin bryant a member of the battalion wrote that we encamped this afternoon at a rancho situated on the edge of a fertile and finely watered plain of considerable extent where we found corn wheat and frijoles in great a
- on january 10 young bryant continues crossing the plain we encamped about 2 p m in the mouth of a cañada through which we ascended over a difficult pass in a range of elevated hills between us and the plain of san fernan

### `articles/chapter-17-yanks-infiltrate` #855

- legacy: `reynolds-full / part17`
- WordPress post: 584 Chapter 17. Yanks Infiltrate
- added lines: 4 (4 in WordPress, 0 in neither)

In the WordPress body:

- a year later the ship enterprise put into port laden with shoes and boots made from california leather thus was born the hide and tallow trade that turned patrons and priests alike into smugglers occasionally a sailor wo
- aristocratic californios would not grub about in the muck and mire so it was the sonorans again who did most of the work one exception was josé salazár doña jacoba s second husband who placered 4 300 in a year even more 
- richard henry dana in his two years before the mast writes we also carried a small quantity of gold dust which mexicans or indians brought to us from san feliciano canyon not far from mission san fernando
- mexico unwittingly made the process easier people like abel stearns william wolfskill and john temple were allowed to migrate convert to catholicism become citizens marry the daughters of rancheros and become a small arm

### `articles/prologue-history-of-the-santa-clarita-valley` #819

- legacy: `reynolds-full / prologue`
- WordPress post: 347 Prologue
- added lines: 3 (3 in WordPress, 0 in neither)

In the WordPress body:

- while the communities of newhall valencia canyon country saugus agua dulce acton castaic and val verde may seem new they are built upon a heritage that dates back hundreds indeed thousands of years
- unfortunately no one has ever sat down to write the full story of santa clarita bits and pieces can be found in some books other parts may be gleaned from recollections of longtime residents occasionally some letter or n
- this chronicle is rich for it is a microcosm of the saga of the american west through the passes 28 000 years ago flowed hunters in search of mammoths and camels over the hills rode blue coated explorers sent by the king

### `articles/chapter-4-children-of-nature` #827

- legacy: `reynolds-full / part04`
- WordPress post: 381 Chapter 4: Children of Nature
- added lines: 3 (3 in WordPress, 0 in neither)

In the WordPress body:

- around the time the roman empire was crumbling in europe a mass migration began across the upper great plains of north america why they left their homes amid tall prairie grass and bison herds is unknown how they happene
- they hunted small game such as rabbits squirrels and snakes and exploited larger animals deer antelope and mountain goat which were found in abundance they chewed or smoked tree tobacco and took yerba santa as a painkill
- home was a wikiup resembling an upside down basket made of arched sycamore poles and thatched with grass they also constructed dwellings partially underground with domed adobe roofs and raised sleeping platforms

### `articles/chapter-5-tribal-relics` #829

- legacy: `reynolds-full / part05`
- WordPress post: 384 Chapter 5: Tribal Relics
- added lines: 3 (3 in WordPress, 0 in neither)

In the WordPress body:

- the last full blooded tataviam died on the camulos ranch in 1921 mfn reynolds wrote that juan josé fustero died in 1916 however his death certificate maintained by the county of ventura places his date of death as june 3
- most probably depict the visions of shamans who were central to tataviam religious practices and used hallucinogens to communicate with the supernatural world mfn david s whitley ph d a guide to rock art sites pg 10 mfn 
- the treasure was sold for 1 500 to steven bowers who did some additional excavation packed off scores of items and peddled them to collectors around the world most of the treasures wound up at the peabody museum of ameri

### `articles/chapter-10-solitary-hiker` #839

- legacy: `reynolds-full / part10`
- WordPress post: 440 Chapter 10: Solitary Hiker
- added lines: 3 (3 in WordPress, 0 in neither)

In the WordPress body:

- on march 22 1774 the padres of mission san gabriel arcangel were surprised to find a motley crew of thirty four leather jacket soldiers camped at their door led by a spanish captain and a franciscan priest they had spent
- the pathfinder for de anza was father francisco hermenegildo tomás garcés one of the most remarkable explorers in history born april 12 1738 in zaragoza spain he entered the priesthood at age twenty five and was assigned
- during the 1776 de anza expedition father garcés remained with the yuma indians on the colorado river while settlers went on ahead to establish the village of san francisco becoming intrigued with the extensive trading r

### `articles/chapter-19-paradise-found` #859

- legacy: `reynolds-full / part19`
- WordPress post: 627 Chapter 19. Paradise Found
- added lines: 3 (3 in WordPress, 0 in neither)

In the WordPress body:

- a succession of military governors controlled the affairs of california for two years after the capitulation of cahuenga one of these was john charles frémont he was appointed by commodore robert field stockton and was p
- william lewis manly death valley survivor and author of death valley in 49 photographer unknown public domain
- vaqueros of rancho san francisco brought the pair to the main house on january 1 1850 there they were fed clothed and sent back with supplies doña jacoba pressed oranges into their hands as they left for the little ones 

### `articles/chapter-21-buttons-and-bows` #863

- legacy: `reynolds-full / part21`
- WordPress post: 666 Chapter 21. Buttons and Bows
- added lines: 3 (3 in WordPress, 0 in neither)

In the WordPress body:

- among the forty eight delegates eleven were californios or property owning spanish mexicans their views were so radically different from those of the yankees that splitting the territory into two separate states was seri
- meanwhile hispanic dons were cashing in on their new found wealth extracted by frenzied gold diggers they thought nothing of laying out two thousand dollars for electrum bridles or hand tooled saddles trimmed with silver
- on july 12 the couts brothers arrived at san jose to find fifteen thousand cattle milling about the grass burned and vast swarms of mosquitoes making life more unpleasant but rustlers flooded streams withered grass and c

### `articles/the-birth-of-newhall-continued` #869

- legacy: `perkins / sg19470102perkins`
- WordPress post: 766 The Birth of Newhall
- added lines: 2 (2 in WordPress, 0 in neither)

In the WordPress body:

- there is a canyon taking off up in the soledad canyon known as mill canyon most people have forgotten that its original name was paper mill canyon where davis jorchin of santa cruz had erected a mill for baling the yucca
- in december 1876 the ventura free press reports chinese buying mining supplies and the discovery of a 3 oz nugget at this same time miners at san feliciano were seriously considering diverting the waters of elizabeth lak

### `articles/chapter-6-winds-of-change` #831

- legacy: `reynolds-full / part06`
- WordPress post: 401 Chapter 6. Winds of Change
- added lines: 2 (2 in WordPress, 0 in neither)

In the WordPress body:

- the name california was coined by the mind of one garcí ordóñez de montalvo a spanish novelist who in 1510 published a book titled the exploits of esplandián it was very popular at the time even read by the great conquis
- juan rodríguez cabrillo set sail from the port of navidád on june 27 1542 with two small ships the san salvador and the victoria the ships were poorly built poorly manned and even more poorly provisioned yet they managed

### `articles/chapter-7-spain-reconnoiters` #833

- legacy: `reynolds-full / part07`
- WordPress post: 414 Chapter 7. Spain Reconnoiters
- added lines: 2 (2 in WordPress, 0 in neither)

In the WordPress body:

- it was august 8 1769 when a strange cavalcade appeared atop what is now called frémont pass a quarter mile east of beale s cut in the newhall pass these were not the conquistadores of old clad in chain mail and glitterin
- the longest part of the column was a pack train of one hundred mules followed by spare horses then the company of mounted soldados de cuero leather jacket soldiers carrying long lances

### `articles/chapter-11-ferdinands-grasp` #841

- legacy: `reynolds-full / part11`
- WordPress post: 444 Chapter 11. Ferdinand's Grasp
- added lines: 2 (2 in WordPress, 0 in neither)

In the WordPress body:

- the mission site was found to be occupied by don francisco reyes a man of considerable political clout inasmuch as he was then the alcalde or mayor of the pueblo of los angeles the padres proved that the land was in fact
- twenty four years after it was established mission san fernando claimed 12 800 head of cattle 7 800 sheep 780 horses and 144 mules an impressive list it was a busy trade center in hides and tallow and was famous for its 

### `articles/chapter-13-insurrection` #845

- legacy: `reynolds-full / part13`
- WordPress post: 450 Chapter 13. Insurrection
- added lines: 2 (2 in WordPress, 0 in neither)

In the WordPress body:

- almost immediately a group of california citizens began to apply pressure on the mexican congress to divide the mission properties as provided by law these citizens or californios as they liked to call themselves believe
- mfn often spelled ignacio by modern writers the y was used in del valle s day as evidenced by his grave marker mfn

### `articles/chapter-15-family-squabbles` #851

- legacy: `reynolds-full / part15`
- WordPress post: 552 Chapter 15. Family Squabbles
- added lines: 2 (2 in WordPress, 0 in neither)

In the WordPress body:

- seven months after don antonio died on january 1 1842 ygnacio and maría carrillo exchanged wedding vows before bishop narciso durán the couple hustled down to los angeles and presented the old don s missive to his son as
- the legal battle was joined and at last the juéz judge awarded 13 599 acres to ygnacio del valle while doña jacoba got 21 307 acres and each of her six children received 4 684 acres

### `articles/chapter-20-an-eager-market` #861

- legacy: `reynolds-full / part20`
- WordPress post: 642 Chapter 20. An Eager Market
- added lines: 2 (2 in WordPress, 0 in neither)

In the WordPress body:

- while the rest of the world poured into the northern mines in a mad quest for wealth the native californios generally stayed home and tended to their cattle
- such prices sent the rancheros into an orgy of buying and attracted suppliers from as far away as texas where ranchers such as w h snyder and ranger captain jack cureton trailed longhorns across mountains and deserts thr

### `articles/chapter-8-the-feast` #835

- legacy: `reynolds-full / part08`
- WordPress post: 417 Chapter 8. The Feast
- added lines: 1 (1 in WordPress, 0 in neither)

In the WordPress body:

- going three leagues we came to the meeting of these creeks and set up camp close to a very sizable village of very good well behaved tractable heathens who on our reaching here were camped within a large pen with only on

## BUG: in neither the page nor WordPress — 3 records

### `warMemorials/terror-rudyacosta` #526

- legacy: `warmemorial / terror_rudyacosta`
- WordPress post: **none under this slug**
- added lines: 6 (0 in WordPress, 6 in neither)

In neither:

- rudy alexander acosta may 2 1991 march 19 2011 was a canyon country native santa clarita christian school graduate and u s army combat medic whose death in afghanistan at age 19 left a lasting mark on the santa clarita v
- acosta graduated from santa clarita christian school in 2009 remembered by classmates as funny charismatic and irrepressibly spirited and enlisted in the army that same summer completing basic training at fort leonard wo
- on the morning of march 19 2011 as acosta and fellow soldiers prepared to go on patrol at forward operating base frontenac in shah wali kot kandahar province an afghan security guard opened fire with an ak 47 acosta and 
- he was posthumously promoted from private first class to specialist 4th class and awarded the bronze star medal purple heart combat medical badge and numerous additional citations his funeral motorcade traveled through t
- sp4 rudy alexander acosta is interred at eternal valley memorial park in newhall california he is survived by his parents dante and carolyn acosta and his brother doran and sister alexandra
- in his memory dante acosta founded the rudy a acosta memorial foundation and became a tireless advocate for military families and the accountability of private security contractors operating alongside u s forces overseas

### `warMemorials/ww2-augustrubel` #518

- legacy: `warmemorial / ww2_augustrubel`
- WordPress post: **none under this slug**
- added lines: 1 (0 in WordPress, 1 in neither)

In neither:

- augustus a august rubel american field service 1899 1943 war memorial home world war i world war ii korean war vietnam war war on terror afghanistan iraq augustus a august rübel american field service d april 28 1943 hom

### `warMemorials/ww2-robertcone` #576

- legacy: `warmemorial / ww2_robertcone`
- WordPress post: **none under this slug**
- added lines: 1 (0 in WordPress, 1 in neither)

In neither:

- notes robert cone was a member of the cone family that owned the saugus cafe for 86 years his death in world war ii is mentioned in his brother dick cone s obituary and in a newspaper article about tom ross who was missi

## BOTH: some of each — 0 records

None.

## What the one bug means for the 1,544 photographs

The single line that is neither content nor reflow is the legacy navigation trail
inside a body: `ww2-augustrubel` #518 is 500 characters and all of it is the page
title followed by `> WAR MEMORIAL HOME > WORLD WAR I > WORLD WAR II > ...`.

`clean_legacy_bodies.php` strips a breadcrumb by matching `^>` at the start of a
line. Where the trail sits after something else on the same line it is invisible
to that rule, which is why this one survived. So did the census: its
`legacy-nav-in-body` class uses the same anchored pattern.

That shape, counted across the inventories:

| inventory | pages | trail mid-line, escapes the cleaner | trail at line start, caught |
|---|---|---|---|
| lw-features | 1661 | 317 | 1340 |
| warmemorial | 54 | 0 | 0 |
| perkins | 20 | 11 | 11 |
| reynolds-full | 80 | 4 | 3 |
| worden | 219 | 1 | 3 |

Two things follow, and the second is the larger one.

1. The mid-line breadcrumb needs adding to `clean_legacy_bodies.php` and to the
   census class, or it stays invisible wherever it appears.

2. `clean_legacy_bodies.php` runs over `articles`, `warMemorials`, `obituaries` only.
   The LW features import as **photographs**, a section the cleaner does not touch,
   so they would arrive with all their chrome and not merely this one shape.
   That wants settling before the import, not after.
