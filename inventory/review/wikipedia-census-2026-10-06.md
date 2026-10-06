# Wikipedia census of person records, 6 October 2026

Read-only pass. Nothing in Craft was changed. Companion data: `inventory/review/wikipedia-census-2026-10-06.json` (every person, every field below, the full reference lists).

Method: every entry in the persons section, any status, was exported from Craft. Where a record carried a wikidataId, the English Wikipedia sitelink came from Wikidata. Every record was also searched on English Wikipedia by its title (plain, and again with Santa Clarita / Newhall / California), and candidates were kept only when the name, dates and the record's roles agreed. Lead images are the article's page image (the infobox image), with licence read from the file description page (extmetadata and the licence templates themselves).

## Summary

| | Count |
|---|---|
| Person records (all live) | 294 |
| With an English Wikipedia article (MATCH) | 58 |
| UNCERTAIN | 3 |
| No article | 233 |
| Records with a profile body already (all persons) | 95 |
| Matched records that already have a profile body | 33 |
| Matched records with no profile body | 25 |
| Matched records with no portrait (featuredImage) | 16 |
| Articles with a lead image | 51 |
| Lead image FREE | 50 |
| Lead image NON-FREE | 0 |
| Lead image UNCLEAR | 1 |
| Articles with no lead image | 7 |
| Matches whose record lacks the wikidataId the match supplies | 35 |
| Matches whose record lacks a personWikipediaUrl | 33 |
| Record wikidataId that disagrees with the article | 0 |

So 58 of 294 people (about one in five) have an article. They fall into three groups: state, federal and county office holders (30: members of Congress, state legislators, supervisors, plus Christy Smith, McKeon, George Runner, Cameron Smyth, Wilk and Dante Acosta); the Spanish, Mexican and early American figures (19, from Anza and Serra to Henry Mayo Newhall); and film, business and pioneer figures (9: Hart, Harry Carey, Tom Mix, Rodolfo Acosta, Scofield, Mulholland, Crocker, Banning, Bard). Almost none of the local people (school, college and water boards, city council, city staff, columnists, Signal writers) have one.

Portrait gaps the articles could fill: 16 matched records have no featuredImage. For 12 of them the article's lead image is FREE (Kevin McCarthy, Bill Thomas, Henry Stern, Fran Pavley, Cathie Wright, Jeff Gorell, Audra Strickland, Tom McClintock, George Whitesides, Mike Garcia, Phineas Banning, Cephas L. Bard); Tony Strickland's is UNCLEAR; Steve Fox, Paula Boland and Don Rogers have no lead image.

## People with an article

Primary refs counts the article references that look primary or official (government, legislature, court, official biography, newspapers, newspaper archives, vital and census records, archival collections, books published in the subject's lifetime). The count comes from pattern-matching the citations. It is a finding aid, not a judgement on each source. Several articles cite scvhistory.com itself; those are counted separately (SCVH) because they would be circular as sources.

| id | Record | Article | Bytes | QID | Body | Portrait | Lead image | Licence | Primary refs | SCVH |
|---|---|---|---|---|---|---|---|---|---|---|
| 29466 | Kevin McCarthy | [Kevin McCarthy](https://en.wikipedia.org/wiki/Kevin_McCarthy) | 139698 | Q766866 (new) | - | no | Kevin_McCarthy,_official_portrait,_speaker.jpg | FREE (Public domain) | 157 | 0 |
| 29464 | Bill Thomas | [Bill Thomas](https://en.wikipedia.org/wiki/Bill_Thomas) | 20610 | Q132302 (new) | - | no | Bill_Thomas,_official_photo_portrait_color.jpg | FREE (Public domain) | 17 | 0 |
| 29462 | Henry Stern | [Henry Stern (California politician)](https://en.wikipedia.org/wiki/Henry_Stern_%28California_politician%29) | 14166 | Q27967376 (new) | - | no | Official_portrait_of_Henry_Stern.jpg | FREE (Public domain) | 12 | 0 |
| 29460 | Fran Pavley | [Fran Pavley](https://en.wikipedia.org/wiki/Fran_Pavley) | 12414 | Q5478183 (new) | - | no | Fran_Pavley_2012.jpg | FREE (CC BY 2.0) | 6 | 0 |
| 29458 | Cathie Wright | [Cathie Wright](https://en.wikipedia.org/wiki/Cathie_Wright) | 8331 | Q5053032 (new) | - | no | Cathie_Wright,_2000.jpg | FREE (Public domain) | 0 | 0 |
| 29456 | Tom Lackey | [Tom Lackey](https://en.wikipedia.org/wiki/Tom_Lackey) | 23134 | Q19664484 (new) | 1493 | yes | Tom_Lackey,_2022.jpg | FREE (Public domain) | 22 | 0 |
| 29454 | Steve Fox | [Steve Fox (politician)](https://en.wikipedia.org/wiki/Steve_Fox_%28politician%29) | 15495 | Q7612581 (new) | - | no | - | - | 8 | 0 |
| 29452 | Jeff Gorell | [Jeff Gorell](https://en.wikipedia.org/wiki/Jeff_Gorell) | 21369 | Q6173918 (new) | - | no | California_State_Assembly_Member_Jeff_Gorell.jpg | FREE (Public domain) | 26 | 0 |
| 29450 | Audra Strickland | [Audra Strickland](https://en.wikipedia.org/wiki/Audra_Strickland) | 4704 | Q4820075 (new) | - | no | Audra_Strickland.jpg | FREE (Public domain) | 2 | 0 |
| 29448 | Tony Strickland | [Tony Strickland](https://en.wikipedia.org/wiki/Tony_Strickland) | 45968 | Q16213249 (new) | - | no | Senator_Tony_Strickland_-_California_State_Senate_Official_Headshot.jpg | UNCLEAR (Public domain) | 46 | 0 |
| 29446 | Tom McClintock | [Tom McClintock](https://en.wikipedia.org/wiki/Tom_McClintock) | 77155 | Q535887 (new) | - | no | Tom_McClintock_portrait_(118th_Congress).jpg | FREE (Public domain) | 86 | 0 |
| 29444 | Paula Boland | [Paula Boland](https://en.wikipedia.org/wiki/Paula_Boland) | 4254 | Q7154643 (new) | - | no | - | - | 1 | 0 |
| 29336 | George Whitesides | [George T. Whitesides](https://en.wikipedia.org/wiki/George_T._Whitesides) | 24072 | Q3511804 (new) | - | no | George_T._Whitesides,_official_portrait_(119th_Congress)_(1).jpg | FREE (Public domain) | 18 | 0 |
| 29334 | Mike Garcia | [Mike Garcia (politician)](https://en.wikipedia.org/wiki/Mike_Garcia_%28politician%29) | 60107 | Q94236068 (new) | - | no | Mike_Garcia,_official_portrait,_116th_Congress_(cropped1).jpg | FREE (Public domain) | 63 | 0 |
| 29332 | Katie Hill | [Katie Hill](https://en.wikipedia.org/wiki/Katie_Hill) | 50496 | Q58416634 (new) | - | yes | Katie_Hill,_official_portrait,_116th_Congress.jpg | FREE (Public domain) | 66 | 0 |
| 29328 | Steve Knight | [Steve Knight (politician)](https://en.wikipedia.org/wiki/Steve_Knight_%28politician%29) | 58265 | Q7613060 (new) | - | yes | Steve_Knight,_official_portrait,_114th_Congress.jpeg | FREE (Public domain) | 70 | 0 |
| 29324 | Sharon Runner | [Sharon Runner](https://en.wikipedia.org/wiki/Sharon_Runner) | 19611 | Q7490197 (new) | - | yes | SR_medium_shot_color.jpg | FREE (Public domain) | 16 | 0 |
| 29322 | Don Rogers | [Don Rogers (politician)](https://en.wikipedia.org/wiki/Don_Rogers_%28politician%29) | 3238 | Q21176260 (new) | - | no | - | - | 1 | 0 |
| 29320 | Pilar Schiavo | [Pilar Schiavo](https://en.wikipedia.org/wiki/Pilar_Schiavo) | 7893 | Q115668597 (new) | 969 | yes | Schiavo_Assembly_Portrait.jpg | FREE (Public domain) | 8 | 0 |
| 29318 | Suzette Martinez Valladares | [Suzette Martinez Valladares](https://en.wikipedia.org/wiki/Suzette_Martinez_Valladares) | 14203 | Q102264304 (new) | 1353 | yes | Suzette_Martinez_Valladares,_2024_(2).jpg | FREE (Public domain) | 11 | 0 |
| 29316 | Keith Richman | [Keith Richman](https://en.wikipedia.org/wiki/Keith_Richman) | 5615 | Q6384943 (new) | - | yes | - | - | 1 | 0 |
| 29314 | Pete Knight | [William J. Knight](https://en.wikipedia.org/wiki/William_J._Knight) | 15940 | Q974431 (new) | - | yes | Pete_Knight.jpg | FREE (Public domain) | 6 | 0 |
| 29288 | Kathryn Barger | [Kathryn Barger](https://en.wikipedia.org/wiki/Kathryn_Barger) | 17910 | Q28086178 (new) | - | yes | Kathryn_Barger,_2020.jpg | FREE (Public domain) | 24 | 1 |
| 29284 | Michael D. Antonovich | [Michael D. Antonovich](https://en.wikipedia.org/wiki/Michael_D._Antonovich) | 20749 | Q6829617 (new) | - | yes | Supervisor_Antonovich.jpg | FREE (CC BY-SA 4.0) | 15 | 0 |
| 25389 | Christy Smith | [Christy Smith (politician)](https://en.wikipedia.org/wiki/Christy_Smith_%28politician%29) | 28446 | Q60190997 (new) | 1086 | yes | Christy_Smith_CA_Assembly_official_photo.jpg | FREE (Public domain) | 28 | 0 |
| 21584 | Demetrius G. Scofield | [D. G. Scofield](https://en.wikipedia.org/wiki/D._G._Scofield) | 3911 | Q5203599 (new) | 3104 | yes | DemetriusGScofield.jpg | FREE (Public domain) | 0 | 4 |
| 18791 | Buck McKeon | [Buck McKeon](https://en.wikipedia.org/wiki/Buck_McKeon) | 33220 | Q461981 | 1580 | yes | Buck_McKeon_2011.jpeg | FREE (Public domain) | 24 | 0 |
| 18747 | George Runner | [George Runner](https://en.wikipedia.org/wiki/George_Runner) | 21610 | Q5544109 (new) | - | yes | George_Runner_2011.jpg | FREE (Public domain) | 21 | 0 |
| 18714 | Phineas Banning | [Phineas Banning](https://en.wikipedia.org/wiki/Phineas_Banning) | 13351 | Q7186337 (new) | - | no | Phineas_Banning.jpg | FREE (Public domain) | 4 | 0 |
| 18702 | Tom Mix | [Tom Mix](https://en.wikipedia.org/wiki/Tom_Mix) | 29319 | Q345468 (new) | 1835 | yes | Tom_Mix_by_Witzel.jpg | FREE (Public domain) | 8 | 0 |
| 16432 | William Mulholland | [William Mulholland](https://en.wikipedia.org/wiki/William_Mulholland) | 42461 | Q1347401 (new) | 968 | yes | William_Mulholland,_1924.jpg | FREE (Public domain) | 16 | 2 |
| 16388 | Charles Crocker | [Charles Crocker](https://en.wikipedia.org/wiki/Charles_Crocker) | 18720 | Q2958817 (new) | - | yes | Charles_C_Crocker_by_Stephen_W_Shaw.jpg | FREE (Public domain) | 17 | 0 |
| 16380 | Cameron Smyth | [Cameron Smyth](https://en.wikipedia.org/wiki/Cameron_Smyth) | 4379 | Q5026379 | 2138 | yes | Mayor_Cameron_Smyth_(cropped).jpg | FREE (CC BY 3.0) | 1 | 0 |
| 16356 | William S. Hart | [William S. Hart](https://en.wikipedia.org/wiki/William_S._Hart) | 26121 | Q636680 | 5598 | yes | Williamshart.jpg | FREE (Public domain) | 18 | 2 |
| 15919 | Harry Carey | [Harry Carey (actor)](https://en.wikipedia.org/wiki/Harry_Carey_%28actor%29) | 14138 | Q1344801 (new) | 1524 | yes | Harry_Carey_in_Angel_and_the_Badman_1947.jpg | FREE (Public domain) | 7 | 0 |
| 2532 | Cephas L. Bard | [Cephas L. Bard](https://en.wikipedia.org/wiki/Cephas_L._Bard) | 7880 | Q113799875 (new) | - | no | Cephas_L._Bard,_MD.jpg | FREE (Public domain) | 10 | 0 |
| 343 | Rodolfo Acosta | [Rodolfo Acosta](https://en.wikipedia.org/wiki/Rodolfo_Acosta) | 18425 | Q596071 | 490 | yes | Rodolfo_Acosta_Death_Valley_Days_1965.JPG | FREE (Public domain) | 8 | 1 |
| 341 | Dante Acosta | [Dante Acosta](https://en.wikipedia.org/wiki/Dante_Acosta) | 8840 | Q27916203 | 1952 | yes | - | - | 6 | 1 |
| 335 | Scott Thomas Wilk Sr. | [Scott Wilk](https://en.wikipedia.org/wiki/Scott_Wilk) | 11326 | Q7437510 (new) | 3178 | yes | Scott_Wilk,_2022.jpg | FREE (Public domain) | 7 | 0 |
| 327 | Edward Fitzgerald Beale | [Edward Fitzgerald Beale](https://en.wikipedia.org/wiki/Edward_Fitzgerald_Beale) | 24968 | Q1292161 | 1496 | yes | EFBeale.jpg | FREE (Public domain) | 2 | 0 |
| 323 | Cave Johnson Couts | [Cave Johnson Couts](https://en.wikipedia.org/wiki/Cave_Johnson_Couts) | 27041 | Q58333053 | 4782 | yes | US_Army_Lieutenant_Cave_J._Couts.jpg | FREE (Public domain) | 8 | 0 |
| 321 | William Lewis Manly | [William L. Manly](https://en.wikipedia.org/wiki/William_L._Manly) | 11770 | Q4020072 | 1850 | yes | William_Lewis_Manly.jpg | FREE (Public domain) | 5 | 1 |
| 319 | James Wilson Marshall | [James W. Marshall](https://en.wikipedia.org/wiki/James_W._Marshall) | 14587 | Q253373 | 1669 | yes | James_Marshall2.jpg | FREE (Public domain) | 4 | 0 |
| 317 | Andrés Pico | [Andrés Pico](https://en.wikipedia.org/wiki/Andr%C3%A9s_Pico) | 18485 | Q2806243 | 1997 | yes | Andres_Pico_circa_1850.jpg | FREE (Public domain) | 1 | 1 |
| 315 | Christopher Houston Carson | [Kit Carson](https://en.wikipedia.org/wiki/Kit_Carson) | 104586 | Q379673 | 537 | yes | Kit_Carson_photograph_restored.jpg | FREE (Public domain) | 35 | 0 |
| 313 | Edwin Bryant | [Edwin Bryant (alcalde)](https://en.wikipedia.org/wiki/Edwin_Bryant_%28alcalde%29) | 12842 | Q5346273 | 1219 | yes | - | - | 1 | 0 |
| 311 | Thomas O. Larkin | [Thomas O. Larkin](https://en.wikipedia.org/wiki/Thomas_O._Larkin) | 31803 | Q12061100 | 268 | yes | ThomasOLarkin.jpg | FREE (Public domain) | 3 | 0 |
| 309 | Abel Stearns | [Abel Stearns](https://en.wikipedia.org/wiki/Abel_Stearns) | 11078 | Q4666626 | 2707 | yes | Portrait_of_a_drawing_of_Abel_Stearns,_ca.1840-1860_(CHS-1805).jpg | FREE (Public domain) | 1 | 0 |
| 307 | John C. Frémont | [John C. Frémont](https://en.wikipedia.org/wiki/John_C._Fr%C3%A9mont) | 157469 | Q169011 | 1051 | yes | John_Charles_Fremont_Oval.png | FREE (Public domain) | 29 | 0 |
| 301 | Juan Bautista de Anza | [Juan Bautista de Anza](https://en.wikipedia.org/wiki/Juan_Bautista_de_Anza) | 22648 | Q727612 | 2727 | yes | Portrait_of_Juan_Bautista_de_Anza_(Painted_by_Fray_Orci;_1774,_Mexico_City).jpg | FREE (Public domain) | 3 | 0 |
| 299 | Junípero Serra | [Junípero Serra](https://en.wikipedia.org/wiki/Jun%C3%ADpero_Serra) | 135673 | Q522107 | 473 | yes | Engraving_of_Junípero_Serra_(1787)_(cropped).jpg | FREE (Public domain) | 39 | 0 |
| 297 | Juan Crespí | [Juan Crespí](https://en.wikipedia.org/wiki/Juan_Cresp%C3%AD) | 7664 | Q2712954 | 1305 | yes | Cenotafio_Serra_04.JPG | FREE (CC BY-SA 4.0) | 2 | 0 |
| 295 | Gaspar de Portolá | [Gaspar de Portolá](https://en.wikipedia.org/wiki/Gaspar_de_Portol%C3%A1) | 21460 | Q461023 | 2280 | yes | Retrat_Gaspar_de_Portolà_(Lleida).jpg | FREE (Public domain) | 3 | 0 |
| 293 | Ygnacio del Valle | [Ygnacio del Valle](https://en.wikipedia.org/wiki/Ygnacio_del_Valle) | 12043 | Q8053412 | 5375 | yes | Ygnacio_del_Valle,_1860.jpg | FREE (Public domain) | 1 | 3 |
| 289 | Father Francisco Garcés | [Francisco Garcés](https://en.wikipedia.org/wiki/Francisco_Garc%C3%A9s) | 10723 | Q3622310 | 1569 | yes | Francisco_Garcés.jpg | FREE (Public domain) | 0 | 0 |
| 287 | Pedro Fages | [Pedro Fages](https://en.wikipedia.org/wiki/Pedro_Fages) | 29710 | Q389243 (new) | 1243 | yes | - | - | 4 | 0 |
| 285 | Tiburcio Vasquez | [Tiburcio Vásquez](https://en.wikipedia.org/wiki/Tiburcio_V%C3%A1squez) | 22709 | Q6222916 | 1441 | yes | Tiburcio_Vásquez.jpg | FREE (Public domain) | 3 | 1 |
| 283 | Henry Mayo Newhall | [Henry Newhall](https://en.wikipedia.org/wiki/Henry_Newhall) | 6317 | Q15458611 | 2873 | yes | Henry_Mayo_Newhall.jpg | FREE (Public domain) | 0 | 2 |

"(new)" means the record has no wikidataId and the match supplies it. No record's existing wikidataId disagrees with its article.

## Lead images by licence class

### FREE (50)

- **Kevin McCarthy** (29466): `Kevin_McCarthy,_official_portrait,_speaker.jpg`, Wikimedia Commons, Public domain [PD-USGov-Congress-Speaker]; author: US House Photography. Record has no portrait.
- **Bill Thomas** (29464): `Bill_Thomas,_official_photo_portrait_color.jpg`, Wikimedia Commons, Public domain [PD-USGov-Congress]; author: Unknown author. Record has no portrait.
- **Henry Stern** (29462): `Official_portrait_of_Henry_Stern.jpg`, Wikimedia Commons, Public domain [PD-CAGov, personality rights]; author: Government of California. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record has no portrait.
- **Fran Pavley** (29460): `Fran_Pavley_2012.jpg`, Wikimedia Commons, CC BY 2.0 [Flickrreview, cc-by-2.0]; author: Edward Headington from Granada Hills, USA. Note: CC BY 2.0 via Flickr; attribution required. Record has no portrait.
- **Cathie Wright** (29458): `Cathie_Wright,_2000.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Senate. Note: PD-CAGov; file sourced from scvnews.com, credited to the California State Senate. Confirm against a Senate original. Record has no portrait.
- **Tom Lackey** (29456): `Tom_Lackey,_2022.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Assembly. Note: PD-CAGov; file sourced from a Facebook post, credited to the California State Assembly. Confirm against an Assembly original. Record already has a portrait.
- **Jeff Gorell** (29452): `California_State_Assembly_Member_Jeff_Gorell.jpg`, Wikimedia Commons, Public domain [PD-CAGov, Personality rights]; author: California State Assembly. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record has no portrait.
- **Audra Strickland** (29450): `Audra_Strickland.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Assembly. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record has no portrait.
- **Tom McClintock** (29446): `Tom_McClintock_portrait_(118th_Congress).jpg`, Wikimedia Commons, Public domain [PD-USGov-Congress]; author: U.S. House of Representatives. Record has no portrait.
- **George Whitesides** (29336): `George_T._Whitesides,_official_portrait_(119th_Congress)_(1).jpg`, Wikimedia Commons, Public domain [PD-USGov-Congress]; author: Ike Hayman. Record has no portrait.
- **Mike Garcia** (29334): `Mike_Garcia,_official_portrait,_116th_Congress_(cropped1).jpg`, Wikimedia Commons, Public domain [PD-USGov-Congress]; author: House Creative Services. Record has no portrait.
- **Katie Hill** (29332): `Katie_Hill,_official_portrait,_116th_Congress.jpg`, Wikimedia Commons, Public domain [PD-USGov-Congress]; author: US House of Representatives. Record already has a portrait.
- **Steve Knight** (29328): `Steve_Knight,_official_portrait,_114th_Congress.jpeg`, Wikimedia Commons, Public domain [PD-USGov-Congress]; author: United States Congress. Record already has a portrait.
- **Sharon Runner** (29324): `SR_medium_shot_color.jpg`, Wikimedia Commons, Public domain [PD-CAGov, cc-by-sa-3.0]; author: California State Assembly. Note: PD-CAGov and CC BY-SA 3.0 both given. Record already has a portrait.
- **Pilar Schiavo** (29320): `Schiavo_Assembly_Portrait.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Assembly. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record already has a portrait.
- **Suzette Martinez Valladares** (29318): `Suzette_Martinez_Valladares,_2024_(2).jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Senate. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record already has a portrait.
- **Pete Knight** (29314): `Pete_Knight.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Senate. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record already has a portrait.
- **Kathryn Barger** (29288): `Kathryn_Barger,_2020.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: Los Angeles County Board of Supervisors. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record already has a portrait.
- **Michael D. Antonovich** (29284): `Supervisor_Antonovich.jpg`, Wikimedia Commons, CC BY-SA 4.0 [self]; author: Tbell5thd. Note: CC BY-SA 4.0 own work by a Wikipedia user; attribution and share-alike required. Record already has a portrait.
- **Christy Smith** (25389): `Christy_Smith_CA_Assembly_official_photo.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Assembly. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record already has a portrait.
- **Demetrius G. Scofield** (21584): `DemetriusGScofield.jpg`, Wikimedia Commons, Public domain [PD-US-1923]; author: Standard Oil of California (Life time: 1911). Note: PD-US-1923 (published 1911). A copy (DemetriusGScofield-commons.jpg) already sits in inventory/incoming. Record already has a portrait.
- **Buck McKeon** (18791): `Buck_McKeon_2011.jpeg`, Wikimedia Commons, Public domain [PD-USGov-Congress]; author: United States Congress. Note: PD-USGov-Congress. The same file (Buck_McKeon_2011.jpeg) already sits in inventory/incoming. Record already has a portrait.
- **George Runner** (18747): `George_Runner_2011.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: Office of Senator George Runner. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record already has a portrait.
- **Phineas Banning** (18714): `Phineas_Banning.jpg`, Wikimedia Commons, Public domain [PD-US]; author: Unknown photographer. Note: PD-US; 19th-century photo, file sourced from a Press-Enterprise web page. Record has no portrait.
- **Tom Mix** (18702): `Tom_Mix_by_Witzel.jpg`, Wikimedia Commons, Public domain [PD-old-auto]; author: Albert Witzel. Record already has a portrait.
- **William Mulholland** (16432): `William_Mulholland,_1924.jpg`, Wikimedia Commons, Public domain [PD-US-expired]; author: Los Angeles Herald-Examiner. Note: PD-US-expired 1924 photo; the Commons file was taken from scvhistory.com (lw2054). Record already has a portrait.
- **Charles Crocker** (16388): `Charles_C_Crocker_by_Stephen_W_Shaw.jpg`, Wikimedia Commons, Public domain [PD-OLD, PD-old-100]; author: Stephen William Shaw. Record already has a portrait.
- **Cameron Smyth** (16380): `Mayor_Cameron_Smyth_(cropped).jpg`, Wikimedia Commons, CC BY 3.0 [LicenseReview]; author: Canyons News. Note: CC BY 3.0 via YouTube (Canyons News), licence-reviewed; attribution required. Record already has a portrait.
- **William S. Hart** (16356): `Williamshart.jpg`, Wikimedia Commons, Public domain [PD-US]; author: Chircosta. Note: PD-US, Library of Congress. The same file (Williamshart.jpg) already sits in inventory/incoming. Record already has a portrait.
- **Harry Carey** (15919): `Harry_Carey_in_Angel_and_the_Badman_1947.jpg`, Wikimedia Commons, Public domain [PD-US-not renewed]; author: film screenshot. Note: PD-US-not renewed: a frame from Angel and the Badman (1947). Record already has a portrait.
- **Cephas L. Bard** (2532): `Cephas_L._Bard,_MD.jpg`, Wikimedia Commons, Public domain [PD-US]; author: unknown. Record has no portrait.
- **Rodolfo Acosta** (343): `Rodolfo_Acosta_Death_Valley_Days_1965.JPG`, Wikimedia Commons, Public domain [PD-Pre1978]; author: Death Valley Days--the program apparently did their own publicity.. Note: PD-Pre1978 (published without notice): a 1965 Death Valley Days publicity still, sourced from a WorthPoint listing. Record already has a portrait.
- **Scott Thomas Wilk Sr.** (335): `Scott_Wilk,_2022.jpg`, Wikimedia Commons, Public domain [PD-CAGov]; author: California State Senate. Note: PD-CAGov: California government work; Commons accepts the tag, but the State has not formally dedicated these photos to the public domain. Record already has a portrait.
- **Edward Fitzgerald Beale** (327): `EFBeale.jpg`, Wikimedia Commons, Public domain [PD-USGov-Military-Air Force]; author: not given. Note: Tagged PD-USGov-Military-Air Force (taken from the Beale AFB website); the portrait is 19th-century and public domain by age anyway. Record already has a portrait.
- **Cave Johnson Couts** (323): `US_Army_Lieutenant_Cave_J._Couts.jpg`, Wikimedia Commons, Public domain [PD-USGov-Military-Army]; author: US Army. Record already has a portrait.
- **William Lewis Manly** (321): `William_Lewis_Manly.jpg`, English Wikipedia (local), Public domain [PD-old]; author: not given. Note: File is local to English Wikipedia, not on Commons, but tagged PD-old (sourced via Find a Grave). Free, though the source chain is thin. Record already has a portrait.
- **James Wilson Marshall** (319): `James_Marshall2.jpg`, Wikimedia Commons, Public domain [Fairuse, PD-US-expired]; author: Unknown author. Note: PD-US-expired, c.1884; the original enwiki upload had been tagged fair use before it was moved to Commons as PD. Record already has a portrait.
- **Andrés Pico** (317): `Andres_Pico_circa_1850.jpg`, Wikimedia Commons, Public domain [PD-old-70]; author: Unknown author. Note: PD-old-70; c.1850 photo, though the file's stated source is Pinterest. Record already has a portrait.
- **Christopher Houston Carson** (315): `Kit_Carson_photograph_restored.jpg`, Wikimedia Commons, Public domain [PD-old]; author: Mathew Brady or Levin C. Handy. Record already has a portrait.
- **Thomas O. Larkin** (311): `ThomasOLarkin.jpg`, Wikimedia Commons, Public domain [PD-1923]; author: Unknown author. Record already has a portrait.
- **Abel Stearns** (309): `Portrait_of_a_drawing_of_Abel_Stearns,_ca.1840-1860_(CHS-1805).jpg`, Wikimedia Commons, Public domain [PD-US]; author: Unknown author. Record already has a portrait.
- **John C. Frémont** (307): `John_Charles_Fremont_Oval.png`, Wikimedia Commons, Public domain [PD-old-70]; author: Unknown author. Record already has a portrait.
- **Juan Bautista de Anza** (301): `Portrait_of_Juan_Bautista_de_Anza_(Painted_by_Fray_Orci;_1774,_Mexico_City).jpg`, Wikimedia Commons, Public domain [pd-old]; author: Fray Orci. Record already has a portrait.
- **Junípero Serra** (299): `Engraving_of_Junípero_Serra_(1787)_(cropped).jpg`, Wikimedia Commons, Public domain [PD-Art-two]; author: Unknown author. Record already has a portrait.
- **Juan Crespí** (297): `Cenotafio_Serra_04.JPG`, Wikimedia Commons, CC BY-SA 4.0 [self]; author: Miguel Hermoso Cuesta. Note: CC BY-SA 4.0, but the image is a photograph of the Serra cenotaph, not a portrait of Crespí. Record already has a portrait.
- **Gaspar de Portolá** (295): `Retrat_Gaspar_de_Portolà_(Lleida).jpg`, Wikimedia Commons, Public domain [PD-old]; author: Unknown painter. Record already has a portrait.
- **Ygnacio del Valle** (293): `Ygnacio_del_Valle,_1860.jpg`, Wikimedia Commons, Public domain [PD-US]; author: University of Southern California Libraries / California Historical Society. Record already has a portrait.
- **Father Francisco Garcés** (289): `Francisco_Garcés.jpg`, Wikimedia Commons, Public domain [pd-old]; author: Unknown author. Record already has a portrait.
- **Tiburcio Vasquez** (285): `Tiburcio_Vásquez.jpg`, Wikimedia Commons, Public domain [PD-US-expired]; author: Unknown author. Record already has a portrait.
- **Henry Mayo Newhall** (283): `Henry_Mayo_Newhall.jpg`, Wikimedia Commons, Public domain [PD-US]; author: Unknown author. Note: PD-US; the Commons file was taken from scvhistory.com (ap1335). Record already has a portrait.

### NON-FREE (0)

None.

### UNCLEAR (1)

- **Tony Strickland** (29448): `Senator_Tony_Strickland_-_California_State_Senate_Official_Headshot.jpg`, Wikimedia Commons, Public domain [PD-CAGov, self]; author: Jacqui Nguyen. Note: Tagged both 'own work' by a Wikipedia user and PD-CAGov as the Senate's official headshot. The two claims conflict: a private uploader's own photo is not a state work. Unclear until the Senate original is found. Record has no portrait.

No lead image: Steve Fox (29454), Paula Boland (29444), Don Rogers (29322), Keith Richman (29316), Dante Acosta (341), Edwin Bryant (313), Pedro Fages (287).

Every FREE image is on Wikimedia Commons except William L. Manly's, which is local to English Wikipedia but tagged PD-old. No article's lead image is a non-free (fair use) file. Thirteen of the FREE images rest on the PD-CAGov tag (California state legislature and county portraits). Commons accepts that tag on the reasoning that California government works are public records, but the State has never formally put these photos in the public domain; if Nathan wants a stricter standard, those thirteen are the ones to weigh. Congressional portraits (PD-USGov-Congress) are federal works and plainly free.

## Uncertain

- **Edward Muhl** (30201) vs [Edward Muhl](https://en.wikipedia.org/wiki/Edward_Muhl) (Q18206595). Record is a Santa Clarita Community College District trustee (elected 1967, 1971). The article is Edward Muhl (1907-2001), head of production at Universal Pictures 1953-1973. Same name and plausible era, but nothing in the record or the article ties the studio executive to the Santa Clarita Valley or the college board. Needs a source (a 1967 or 1971 candidate statement or Signal story) before it is treated as the same man.
- **Patrick Shaughnessy** (28699) vs [Patrick Shaughnessy](https://en.wikipedia.org/wiki/Patrick_Shaughnessy) (Q7147629). Record is a Hart district school board member with no dates. The article is Patrick 'Spark' Shaughnessy, a media executive who spent seven years as a Los Angeles radio general manager before moving to Dallas. Nothing places him in the SCV or on the Hart board. Unlikely, but not excluded.
- **Charles Barber** (18689) vs [Charles E. Barber](https://en.wikipedia.org/wiki/Charles_E._Barber) (Q3666389). Record has no dates, body or role; it is linked only from coin-collecting articles (Mr. Brenner's Lincoln, 50-State Quarters, Answers to Your Coin Questions). That context points strongly at Charles E. Barber (1840-1917), sixth chief engraver of the U.S. Mint, but the record itself carries nothing that confirms it. Probable, not confirmed. Also a question whether he belongs in the persons section at all, since his role in SCV history is a passing mention in coin columns. Lead image: `CharlesEBarber-painting_01.jpg`, UNCLEAR.

## No article: the namesake traps and other notes

- **Tom Frew II** (31354): No article (nor for Tom Frew III / Thomas M. Frew Jr. or Tom Frew IV).
- **Carl Goldman** (30546): No article of his own; the nearest article is KHTS (AM), the station.
- **Steven D. Zimmer** (30247): No article. Not Steve Zimmer of the LAUSD board (who also lacks an article).
- **William G. Bonelli Jr.** (30199): No article for Bill Bonelli Jr. The article 'William G. Bonelli' (Q8009505) is his father, William George Bonelli (1895-1970), Board of Equalization member, owner of the Saugus rodeo grounds (Bonelli Stadium). The father has no person record of his own; the article is a lead for one, not a match for Jr. Its lead image (1935 Los Angeles Times photo, UCLA collection, CC BY 4.0) is of the father.
- **Jeri Seratti** (29113): No article of her own; the nearest article is KHTS (AM), the station.
- **Tim Burkhart** (29104): No article. 'Timothy Burkhart' on Wikipedia is an unrelated criminal.
- **Susan Shapiro** (29100): No article. 'Susan Shapiro' on Wikipedia is a New York author.
- **Thomas M. Frew Jr.** (28647): No article.
- **Francisco "Chico" López** (28132): No article. Do not confuse with Francisco López the 1842 gold discoverer (also no article).
- **Piotr Orzechowski** (26591): No article. 'Piotr Orzechowski' on Wikipedia is a Polish jazz pianist (born 1990, Krakow), not the SCV Water director.
- **Remi Nadeau** (18869): No article. 'Remi Nadeau' on Wikipedia is the historian (1920-2016), not this Rémi Nadeau (1867-1941).
- **Francisco Lopez** (18834): No article. Francisco López the 1842 Placerita gold discoverer is not on the 'Francisco López' disambiguation page; he is covered inside the articles 'Placerita Canyon State Park' and 'Rancho San Francisco'.
- **John Lang** (18820): No article. Not on the 'John Lang' disambiguation page.
- **Tom Frew IV** (18783): No article.
- **Tom Campbell** (18774): No article for this Tom Campbell (Newhall water district context). 'Tom Campbell (California politician)' is a Silicon Valley congressman, not this person.
- **Michael White** (18765): No article. The record is linked from U.S. Mint and 50-State Quarters coin columns; no Wikipedia article matches that context.
- **Val Thomas** (18756): No article.
- **Ruth Newhall** (15477): No article of her own; she appears in the article on her husband, 'Scott Newhall'.
- **Rémi Nadeau** (339): No article. 'Remi Nadeau' on Wikipedia (Q7311774) is the historian Remi A. Nadeau (1920-2016), a descendant, not the freighter (1821-1887).
- **Antonio del Valle** (291): No article. The 'Antonio del Valle' disambiguation lists two modern Mexicans only.
- **Jerry Reynolds** (281): No article. The 'Jerry Reynolds' disambiguation lists athletes only.

The other people with no article are local office holders, staff, writers and residents with no English Wikipedia article in their name. The full list is in the JSON (status NONE).

## Caveats for the profile work to come

- Wikipedia is a finding aid here, not a source. Any fact in a profile that rests on the article alone must be labelled as an unsourced lead, per the rule.
- The primary-reference counts come from pattern-matching. For modern politicians most "newspaper" references are current news reporting, which is contemporary for them; for colonial and Mexican-era figures most newspaper references are modern commentary, and the primary material is in the books published in or near their lifetimes (listed as contemporary-publication) and the archival links.
- Several articles cite scvhistory.com (Scofield 4, Ygnacio del Valle 3, Hart 2, Mulholland 2, Henry Newhall 2 and others). Those are our own legacy pages and add nothing independent.
- Two Commons images (Mulholland, Henry Newhall) were themselves taken from scvhistory.com, and three of the files (Hart, McKeon, Scofield) already sit in inventory/incoming.
