# Candidate review research (39 entities)

- Date: 2026-09-16 PT
- Agent: Grok Bot (research)
- Source: inventory/canonical_entities.json `candidate_review` (39 items: 29 place, 10 person). CANDIDATES.md missing; used /tmp/canonical_entities.json from origin/grok-build inventory.
- Cross-check: PLACES-RESEARCH.md Place hubs (newhall, pico-canyon, rancho-camulos, san-francisquito-canyon, tejon, tejon-ranch, lake-hughes, soledad-canyon, towsley-canyon, etc.); canonical_sample Craft Places/Persons; GRAVE-AUDIT.md / existing Persons (del Valle family, Henry Mayo Newhall). No scvhistory.com crawl.
- Method: name + type + mentionCount + note from JSON; alias/typo/duplicate check against existing hubs; Wikidata via enwiki pageprops and Special:EntityData (wbsearchentities heavily rate-limited). QID only when live English label clearly matches the SCV entity.
- Rules: one paragraph max per candidate; no invented URIs; no Craft import recommendations; no sacred-site coordinates; no em dashes; war memorial names are not Persons (these 10 are not war memorials).

## Summary counts

- Likely recurring (new Place/Person worth Nathan/Leon review): 26 (16 place, 10 person)
- Likely alias / typo / compound of existing hub or sibling candidate: 13
- QID assigned (live EntityData English label match): 12
- QID NEEDS_VERIFICATION: 2 (newhall-ranch org vs Place; tejon-area ambiguous)
- QID none: 25 (including all pure aliases that should not get their own QID)

## Places

**pico-wiley-canyons** (place, mentions 38). Compound editorial label covering Pico Canyon and Wiley Canyon together, not a single discrete Place. Likely alias/compound of existing hub **pico-canyon** (PLACES-RESEARCH Q7191006 Pico Canyon Oilfield) plus candidate **wiley-canyon**. QID: none (do not invent a compound item).

**sand-canyon** (place, mentions 13). Canyon Country / Santa Clarita neighborhood and canyon corridor (Sand Canyon Road area). Likely recurring Place distinct from existing hubs. Wikidata Q27989170 live label "Sand Canyon" (aliases include Sand Canyon, Santa Clarita, California); enwiki sitelink Sand Canyon, Santa Clarita, California. QID: Q27989170.

**ravenna** (place, mentions 12). Historic Southern Pacific railroad and mining stop in Soledad Canyon near Acton (often spelled Ravena historically). Likely recurring Place; not an alias of acton. No confirmable Wikidata item under live enwiki title lookup. QID: none.

**castaic-junction** (place, mentions 11). Unincorporated community at the junction area south of Castaic, distinct from CDP hub **castaic**. Likely recurring Place. Wikidata Q5049575 live label "Castaic Junction". QID: Q5049575.

**los-angeles-aqueduct** (place, mentions 11). City of Los Angeles aqueduct system (Owens Valley water); recurring SCV/regional infrastructure Place, not in the current 39 Craft Place hubs. Likely recurring. Wikidata Q2859225 live label "Los Angeles Aqueduct". QID: Q2859225. Sibling candidate **l-a-aqueduct** is the short alias of this same entity.

**downtown-newhall** (place, mentions 10). Downtown / Old Town Newhall commercial core; part of existing Place hub **newhall** (Q7018086). enwiki Old Town Newhall redirects to Newhall, Santa Clarita. Likely alias / neighborhood facet of **newhall**, not a separate Place. QID: none for this slug (use newhall).

**honby** (place, mentions 10). Historic locality in the Mint Canyon / Canyon Country area (topo and local maps still name Honby), largely absorbed into Canyon Country. Likely recurring or weak historic Place; not a typo of an existing hub. No Wikidata/enwiki item found. QID: none.

**l-a-aqueduct** (place, mentions 9). Short form of Los Angeles Aqueduct. Likely alias of candidate **los-angeles-aqueduct** (Q2859225), not a second Place. QID: none for this slug.

**newhall-ranch** (place, mentions 9). Large master-planned development west of Santa Clarita along the Santa Clara River (Specific Plan / FivePoint Valencia branding), historically tied to Newhall Land. Likely recurring Place concept, distinct from district **newhall**. Closest Wikidata item Q7018089 is labeled "Newhall Land and Farming Company" (business), not "Newhall Ranch"; do not assign company QID to this Place. QID: NEEDS_VERIFICATION.

**camulos** (place, mentions 8). Short form of Rancho Camulos. Likely alias of existing Craft Place hub **rancho-camulos** (PLACES-RESEARCH Q7290871, live label Rancho Camulos). QID: none for this slug (use rancho-camulos).

**nehwall** (place, mentions 8). Clear typo of Newhall. Likely alias/typo of existing hub **newhall** (Q7018086). QID: none for this slug.

**san-francisquito** (place, mentions 8). Short form of San Francisquito Canyon. Likely alias of existing hub **san-francisquito-canyon** (Q7414194, live label San Francisquito Canyon). Not the Sonora, Mexico town of the same name. QID: none for this slug.

**gorman** (place, mentions 7). Unincorporated community in northwestern Los Angeles County on the Ridge Route / I-5 corridor north of the SCV. Likely recurring Place (peripheral but editorial). Wikidata Q5586620 live label "Gorman". QID: Q5586620.

**lake-communities** (place, mentions 7). Informal regional label for the Lake Hughes and Elizabeth Lake area, not a single gazetteer Place. Likely alias/compound of existing hub **lake-hughes** (Q6476239) plus candidate **elizabeth-lake**. QID: none.

**soledad** (place, mentions 7). Ambiguous short name; in SCV editorial paths it tracks Soledad Canyon, not the Monterey County city of Soledad. Likely alias of existing hub **soledad-canyon** (Q7557525). QID: none for this slug.

**tick-canyon** (place, mentions 7). Canyon in the Canyon Country / Lang area; site of the Sterling Borax Works (Lang Mine). Likely recurring Place; related topic **borax** is not itself a Place. No confirmable Wikidata item. QID: none.

**mint-canyon** (place, mentions 6). Canyon and historic community corridor in Canyon Country (SR-14 / Soledad Canyon adjacency). Likely recurring Place, distinct from **soledad-canyon**. No confirmable Wikidata/enwiki item. QID: none.

**elizabeth-lake** (place, mentions 5). Unincorporated community and namesake lake in the Sierra Pelona / lakes area north of the SCV; pairs with **lake-hughes**. Likely recurring Place. Prefer community item Q5363096 live label "Elizabeth Lake" (CDP); lake-body item Q5363098 also labeled "Elizabeth Lake" exists if Nathan wants the water feature instead. QID: Q5363096.

**tejon-area** (place, mentions 5). Vague regional phrase spanning Fort Tejon, Tejon Ranch, and Tejon Pass. Likely alias/vague of existing hubs **tejon** (already NEEDS_VERIFICATION in PLACES-RESEARCH), **tejon-ranch** (Q7695260), and/or **fort-tejon**, not a clean new Place. Tejon Pass Q477468 live label "Tejon Pass" does not match "Tejon Area". QID: NEEDS_VERIFICATION (do not auto-map).

**borax** (place, mentions 4). Editorial topic token for borax mining (Sterling Borax / Tick Canyon / Lang), not a gazetteer Place. Wikidata Q5319 live label "borax" is the chemical compound; reject as Place QID. Likely non-Place / alias into **tick-canyon** topic. QID: none.

**disney-golden-oak-ranch** (place, mentions 4). Disney Golden Oak Ranch movie ranch in Placerita Canyon / Newhall area. Likely recurring Place. Wikidata Q3030313 live label "Golden Oak Ranch" (movie ranch in Los Angeles County); clear match despite Disney prefix in the candidate name. QID: Q3030313. Related: **jauregui-ranch** was a predecessor/neighbor film ranch later associated with this property.

**jauregui-ranch** (place, mentions 4). Historic Andy Jauregui movie ranch in Placerita Canyon (formerly Fat Jones Ranch); later tied into Golden Oak Ranch / Disney holdings. Likely recurring historic Place; possible eventual alias of **disney-golden-oak-ranch** after Nathan review, but names and eras differ. No Wikidata item found. QID: none.

**newhall-pass** (place, mentions 4). Mountain pass separating the San Gabriel and Santa Susana ranges (also historically Fremont Pass / Beale's Cut corridor context). Likely recurring Place, related to but not identical with **beales-cut**. Wikidata Q8557400 live label "Newhall Pass". QID: Q8557400.

**stevenson-ranch** (place, mentions 4). Census-designated place / master-planned community west of I-5 near Newhall. Likely recurring Place. Wikidata Q577997 live label "Stevenson Ranch". QID: Q577997.

**towsley-wiley-canyon** (place, mentions 4). Compound label for Towsley Canyon and Wiley Canyon. Likely alias/compound of existing hub **towsley-canyon** (PLACES-RESEARCH NEEDS_VERIFICATION) plus candidate **wiley-canyon**. QID: none.

**wiley-canyon** (place, mentions 4). Canyon south of Newhall (Wiley Canyon Road / Elsmere-Towsley open space adjacency); namesake of Henry Clay Wiley context but this slug is the Place, not the Person. Likely recurring Place. No Wikidata/enwiki item found. QID: none.

**hughes-elizabeth-lakes** (place, mentions 3). Compound label for Lake Hughes and Elizabeth Lake. Likely alias/compound of **lake-hughes** and **elizabeth-lake**. QID: none.

**pico** (place, mentions 3). Ambiguous short token; as a Place slug in this inventory it tracks Pico Canyon, not Person Andrés Pico (already in Craft as andres-pico). Likely alias of existing hub **pico-canyon**. QID: none for this slug.

**pyramid-lake** (place, mentions 3). Pyramid Lake reservoir in northwestern Los Angeles County (Castaic / I-5 corridor), not Pyramid Lake Nevada. Likely recurring Place. Wikidata Q7263254 live label "Pyramid Lake" (enwiki Pyramid Lake (Los Angeles County, California)). QID: Q7263254.

## Persons

**judge-adrian-w-adams** (person, mentions 7). Adrian W. Adams, Newhall attorney (practice from 1953), Newhall Municipal Court judge appointed 1970 by Gov. Reagan, retired 1991; first president of the Henry Mayo Newhall Memorial Hospital board; arraigned Newhall Incident suspects. Likely recurring Person. Not in current Craft Persons list. No Wikidata item found. QID: none.

**scott-newhall** (person, mentions 7). Scott Newhall (1914-1992), San Francisco Chronicle executive editor and later owner/editor of The Newhall Signal with wife Ruth; great-grandson of Henry Mayo Newhall. Likely recurring Person; distinct from Craft Person scott-wilk and from henry-mayo-newhall. Wikidata Q55418203 live label "Scott Newhall" (American newspaper editor). QID: Q55418203.

**louise-courtemanche** (person, mentions 6). Louise Courtemanche (b. ~1901), daughter of Alfred and Emma Courtemanche of the Newhall French Village / Ridge Route National Forest Inn family; appears as a recurring family photo subject. Likely recurring Person within that family cluster. No Wikidata item. QID: none.

**ruth-newhall** (person, mentions 6). Ruth Waldo Newhall, journalist and Signal editor; partner of Scott Newhall; author of Newhall Land / SCV history. Likely recurring Person. No enwiki/Wikidata item found under live title lookup. QID: none.

**emma-courtemanche** (person, mentions 4). Emma (Dault) Courtemanche, wife of Alfred Richard "Fred" Courtemanche; French Canadian immigrant family that ran National Forest Inn and Newhall French Village. Likely recurring Person in that family cluster. No Wikidata item. QID: none.

**joyce-wayman** (person, mentions 4). Joyce Wayman, Newhall resident from 1956; first volunteer director/coordinator at Henry Mayo Newhall Memorial Hospital (and earlier Hillside); 1976 SCV Woman of the Year. Likely recurring community Person. No Wikidata item. QID: none.

**nelson-courtemanche** (person, mentions 4). Narcisse "Nelson" Courtemanche (1836-1916), earlier-generation family member (father of Alfred) in Courtemanche photo sets. Likely recurring Person in that family cluster; not a war memorial. No Wikidata item. QID: none.

**arminta-guthrie** (person, mentions 3). Arminta Guthrie (d. 2016), who lived with husband James "Bob" Guthrie (last Southern Pacific Saugus station agent) and family in the Saugus Depot until closure (1962-1978); later revisited the depot at Heritage Junction. Likely recurring Person. No Wikidata item. QID: none.

**bob-walk** (person, mentions 3). Bob Walk, William S. Hart High School class of 1974 and College of the Canyons; MLB pitcher (Phillies, Braves, Pirates) and later broadcaster; SCV native with Newhall family roots. Likely recurring Person (local sports). Wikidata Q4934288 live label "Bob Walk" (American baseball player); matches this SCV figure, not a different namesake. QID: Q4934288.

**reginaldo-del-valle** (person, mentions 3). Reginaldo Francisco del Valle (1854-1938), son of Ygnacio del Valle; raised partly at Rancho Camulos; California Assembly and Senate member; water/power commissioner; UCLA Normal School advocate. Likely recurring Person; related to but not duplicate of Craft Persons antonio-del-valle, ygnacio-del-valle, juventino-del-valle. Wikidata Q7308933 live label "Reginaldo Francisco del Valle". QID: Q7308933.

## Questions for Nathan/Leon

1. Alias cleanup: merge l-a-aqueduct into los-angeles-aqueduct; camulos into rancho-camulos; san-francisquito into san-francisquito-canyon; nehwall and downtown-newhall into newhall; pico into pico-canyon; soledad into soledad-canyon; compounds pico-wiley-canyons, towsley-wiley-canyon, hughes-elizabeth-lakes, lake-communities as multi-Place tags rather than entities?
2. newhall-ranch: create Place without Wikidata, or wait for a Place-labeled item (do not use Q7018089 company)?
3. elizabeth-lake: accept CDP Q5363096, or lake body Q5363098?
4. jauregui-ranch vs disney-golden-oak-ranch: keep both historic Places, or treat Jauregui as predecessor alias of Golden Oak?
5. tejon-area: drop as vague, or map under tejon / tejon-ranch / fort-tejon after tejon hub decision from PLACES-RESEARCH?
6. borax: drop as non-Place topic token pointing at tick-canyon / Sterling Borax?
7. Courtemanche cluster (louise, emma, nelson): import as Persons, or keep as mention-only photo subjects until Alfred Courtemanche is modeled?
8. scott-newhall / ruth-newhall: proceed as Persons (Scott has QID; Ruth none)?

## Raw helper (optional)

See also /tmp/wiki_title_qids.json and /tmp/wd_entitydata.json on the box from this research session (not committed).
