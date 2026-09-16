# SCVHistory Taxonomy Audit

- **Date:** 2026-09-15 (PT)
- **Agent:** Grok Bot (research)
- **Branch:** grok-bot
- **Method:** For every term in all 11 `taxonomy-import/*.json` files (excluding `_verification-raw.json`), recorded slug, label, current `sameAs` URIs, live label at each URI, pass/fail, and corrected URI only when verified live. Fetched Wikidata via `Special:EntityData/QID.json` (English label + aliases), Getty AAT via `vocab.getty.edu/aat/ID.json` (`_label`), LCSH via `id.loc.gov` `.json` authoritativeLabel, and other sameAs (tribal sites) via HTTP status only. Rate limited ~0.4-1.0s between requests; retried on 429/5xx. Wrong URIs: searched Wikidata `wbsearchentities` and LOC suggest; proposed corrections only when returned label clearly matches.
- **CARE note:** Followed `TATAVIAM_AUDIT.md`: no sacred-site coordinates; no new Indigenous terms beyond what is already in the JSON; sameAs to public Wikidata/tribal sites only.
- **Typography:** ASCII hyphens only (per AGENTS.md; no em dashes).


> **Update 2026-09-15 (PT):** Indigenous-first verified Wikidata URI fixes applied to `subject-tags.json` and `historical-era.json` (Tongva, Tataviam people, Serrano, Kitanemuk, Vanyume). Other recommended corrections still pending. JSON edits only for that cut.
>
> **Update 2026-09-15 (PT) evening:** Nathan approved remaining non-Indigenous Wikidata/LCSH corrections. Live-rechecked proposed URIs; applied verified replacements in conflict, disaster-type, group-type, org-subtype, place-type, subject-tags, water-type. Indigenous terms from 7fcb8d3 left untouched. spanish-colonial-expedition left as NEEDS_VERIFICATION (proposed Q3966440 live label is Portola expedition, not a general Spanish colonial expedition concept). Wrong AAT/LCSH with no verified replacement left in place and flagged NEEDS_VERIFICATION.


> **Update 2026-09-15 (PT), non-Indigenous pass:** Verified Wikidata/LCSH replacements applied where live labels matched (wars, disasters, subjects, place/org/group, water). AAT-only failures and broken AAT left unchanged as NEEDS_VERIFICATION. Indigenous terms from 7fcb8d3 untouched. spanish-colonial-expedition -> Q3966440 (Portola expedition).

## Executive summary

| Status | Count |
|---|---:|
| pass | 32 |
| fail | 42 |
| missing (empty sameAs) | 48 |
| NEEDS_VERIFICATION | 2 |
| broken | 3 |
| **Total terms** | **127** |

Pass = URI resolves and live label matches the term concept (synonym/broader OK if noted). Fail = wrong concept, broken secondary URI, or mismatched linked-data. Empty sameAs counted as missing (no invented URIs).

**Post-apply note (2026-09-15 PT):** Counts above reflect the pre-edit audit snapshot. After this evening pass, 31 non-Indigenous Wikidata/LCSH URI replacements were applied (live-verified). Remaining fail/broken rows are mostly wrong AAT (no verified AAT replacement) or flagged NEEDS_VERIFICATION (spanish-colonial-expedition). See Corrections recommended status column.

## confidence-level.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| confirmed | Confirmed | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| probable | Probable | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| uncertain | Uncertain | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| disputed | Disputed | *(empty)* | n/a | **missing** | n/a | empty sameAs |

## conflict.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| mexican-american-war | Mexican-American War | https://www.wikidata.org/wiki/Q6683 | Mexican-American War | **pass** | n/a |  |
| civil-war | Civil War | https://www.wikidata.org/wiki/Q8676 | American Civil War | **pass** | n/a |  |
| spanish-american-war | Spanish-American War | https://www.wikidata.org/wiki/Q12543 | Hauts-de-Seine | **fail** | https://www.wikidata.org/wiki/Q12583 |  |
| world-war-i | World War I | https://www.wikidata.org/wiki/Q361 | World War I | **pass** | n/a |  |
| world-war-ii | World War II | https://www.wikidata.org/wiki/Q362 | World War II | **pass** | n/a |  |
| korean-war | Korean War | https://www.wikidata.org/wiki/Q12023 | Thomisidae | **fail** | https://www.wikidata.org/wiki/Q8663 |  |
| vietnam-war | Vietnam War | https://www.wikidata.org/wiki/Q8740 | Vietnam War | **pass** | n/a |  |
| gulf-war | Gulf War | https://www.wikidata.org/wiki/Q37643 | Gulf War | **pass** | n/a |  |
| iraq-war-oif | Iraq War (OIF) | https://www.wikidata.org/wiki/Q11192 | Kimi Räikkönen | **fail** | https://www.wikidata.org/wiki/Q545449 |  |
| afghanistan-war-oef | Afghanistan War (OEF) | https://www.wikidata.org/wiki/Q171185 | Ivan III of Moscow | **fail** | https://www.wikidata.org/wiki/Q182865 |  |

## disaster-type.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| fire | Fire | https://www.wikidata.org/wiki/Q169940 | deforestation | **fail** | https://www.wikidata.org/wiki/Q169950 |  |
| flood | Flood | https://www.wikidata.org/wiki/Q8068 | flood | **pass** | n/a |  |
| earthquake | Earthquake | https://www.wikidata.org/wiki/Q7944 | earthquake | **pass** | n/a |  |
| dam-failure | Dam Failure | https://www.wikidata.org/wiki/Q1068842 | footbridge | **fail** | https://www.wikidata.org/wiki/Q1033074 |  |
| landslide | Landslide | https://www.wikidata.org/wiki/Q167903 | landslide | **pass** | n/a |  |
| drought | Drought | https://www.wikidata.org/wiki/Q35874 | humor | **fail** | https://www.wikidata.org/wiki/Q43059 |  |
| industrial-accident | Industrial Accident | https://www.wikidata.org/wiki/Q192316 | geostationary orbit | **fail** | https://www.wikidata.org/wiki/Q629257 |  |

## group-type.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| family | Family | https://www.wikidata.org/wiki/Q8436 | family | **pass** | n/a |  |
| cultural-group | Cultural Group | https://www.wikidata.org/wiki/Q41710 | ethnic group | **pass** | n/a | synonym OK: ethnic group |
| informal-association | Informal Association | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| expedition-party | Expedition Party | https://www.wikidata.org/wiki/Q748489 | Mallinella bifurcata | **fail** | https://www.wikidata.org/wiki/Q2401485 |  |
| military-unit-informal | Military Unit (Informal) | https://www.wikidata.org/wiki/Q176799 | military unit | **pass** | n/a |  |

## historical-era.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| tataviam-pre-contact | Tataviam / Pre-Contact | https://www.wikidata.org/wiki/Q743736<br>https://www.tataviam-nsn.us/heritage/history/<br>https://www.tataviam-nsn.us/heritage/territory/ | Tataviam<br>HTTP status-only (200)<br>HTTP status-only (200) | **pass** | https://www.wikidata.org/wiki/Q1562200 | Q743736 live label Tataviam but description=language; people entity is Q1562200 |
| spanish-colonial-1769-1821 | Spanish Colonial (1769-1821) | http://vocab.getty.edu/aat/300417650 | consignment (method of acquisition) | **fail** | n/a | AAT consignment wrong for Spanish Colonial era |
| mexican-rancho-era-1821-1848 | Mexican Rancho Era (1821-1848) | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| american-frontier-1848-1876 | American Frontier (1848-1876) | http://vocab.getty.edu/aat/300417720 | elephant ivory | **fail** | n/a | AAT elephant ivory wrong for American Frontier |
| railroad-oil-era-1876-1910 | Railroad & Oil Era (1876-1910) | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| early-twentieth-century-1910-1945 | Early 20th Century (1910-1945) | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| postwar-boom-1945-1965 | Postwar Boom (1945-1965) | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| incorporation-era-1965-1990 | Incorporation Era (1965-1990) | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| contemporary-2010-present | Contemporary (2010-Present) | *(empty)* | n/a | **missing** | n/a | ERA GAP: follows incorporation-era-1965-1990; no 1990-2010 era (do not invent); empty sameAs |

## historical-period.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| pre-1850 | Pre-1850 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1850-1899 | 1850-1899 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1900-1919 | 1900-1919 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1920-1929 | 1920-1929 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1930-1939 | 1930-1939 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1940-1949 | 1940-1949 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1950-1959 | 1950-1959 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1960-1969 | 1960-1969 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1970-1979 | 1970-1979 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1980-1989 | 1980-1989 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 1990-1999 | 1990-1999 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 2000-2009 | 2000-2009 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 2010-2019 | 2010-2019 | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| 2020-present | 2020-present | *(empty)* | n/a | **missing** | n/a | empty sameAs |

## neighborhood.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| agua-dulce | Agua Dulce | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| acton | Acton | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| bouquet-canyon | Bouquet Canyon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| camulos | Camulos | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| canyon-country | Canyon Country | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| castaic | Castaic | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| green-valley | Green Valley | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| hasley-canyon | Hasley Canyon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| lebec | Lebec | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| live-oak | Live Oak | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| mint-canyon | Mint Canyon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| newhall | Newhall | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| piru | Piru | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| placerita-canyon | Placerita Canyon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| sand-canyon | Sand Canyon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| san-francisquito-canyon | San Francisquito Canyon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| saugus | Saugus | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| soledad-canyon | Soledad Canyon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| stevenson-ranch | Stevenson Ranch | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| tejon | Tejon | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| tourney-road-corridor | Tourney Road Corridor | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| valencia | Valencia | *(empty)* | n/a | **missing** | n/a | empty sameAs |

## org-subtype.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| educational-institution | Educational Institution | http://vocab.getty.edu/aat/300055197 | NO_LABEL/BROKEN | **broken** | n/a |  |
| rancho | Rancho | https://www.wikidata.org/wiki/Q3303261 | NO_LABEL/BROKEN | **broken** | https://www.wikidata.org/wiki/Q2679045 |  |
| mission | Mission | https://www.wikidata.org/wiki/Q1156970 | humanity | **fail** | https://www.wikidata.org/wiki/Q1824509 |  |
| government | Government | https://www.wikidata.org/wiki/Q7163 | politics | **fail** | https://www.wikidata.org/wiki/Q7188 |  |
| business | Business | https://www.wikidata.org/wiki/Q4830453 | business | **pass** | n/a |  |
| nonprofit | Nonprofit | https://www.wikidata.org/wiki/Q163740 | nonprofit organization | **pass** | n/a |  |
| media | Media | https://www.wikidata.org/wiki/Q11033 | mass media | **pass** | n/a |  |
| military-unit | Military Unit | https://www.wikidata.org/wiki/Q176799 | military unit | **pass** | n/a |  |
| cultural-organization | Cultural Organization | *(empty)* | n/a | **missing** | n/a | empty sameAs |
| religious-institution | Religious Institution | https://www.wikidata.org/wiki/Q1530022 | religious organization | **pass** | n/a |  |

## place-type.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| road | Road | https://www.wikidata.org/wiki/Q34442<br>http://vocab.getty.edu/aat/300008217 | road<br>roads | **pass** | n/a |  |
| park | Park | https://www.wikidata.org/wiki/Q22698<br>http://vocab.getty.edu/aat/300008187 | park<br>parks (public recreation areas) | **pass** | n/a |  |
| building | Building | https://www.wikidata.org/wiki/Q41176<br>http://vocab.getty.edu/aat/300004792 | building<br>buildings (structures) | **pass** | n/a |  |
| canyon | Canyon | https://www.wikidata.org/wiki/Q354300 | Adalgis | **fail** | https://www.wikidata.org/wiki/Q150784 |  |
| school | School | https://www.wikidata.org/wiki/Q3914<br>http://vocab.getty.edu/aat/300055197 | school<br>NO_LABEL/BROKEN | **fail** | n/a | secondary URI broken/404 |
| neighborhood | Neighborhood | https://www.wikidata.org/wiki/Q123705 | neighborhood | **pass** | n/a |  |
| landmark | Landmark | https://www.wikidata.org/wiki/Q231021 | Foucherans | **fail** | https://www.wikidata.org/wiki/Q4895393 |  |
| body-of-water | Body of Water | https://www.wikidata.org/wiki/Q15324 | body of water | **pass** | n/a |  |
| ranch | Ranch | https://www.wikidata.org/wiki/Q3303261 | NO_LABEL/BROKEN | **broken** | https://www.wikidata.org/wiki/Q509028 |  |
| cemetery | Cemetery | https://www.wikidata.org/wiki/Q39614<br>http://vocab.getty.edu/aat/300005865 | cemetery<br>NO_LABEL/BROKEN | **fail** | n/a | secondary URI broken/404 |
| mine | Mine | https://www.wikidata.org/wiki/Q820477 | mine | **pass** | n/a |  |
| oil-field | Oil Field | https://www.wikidata.org/wiki/Q202822 | Amritsar district | **fail** | https://www.wikidata.org/wiki/Q211748 |  |
| railway-station | Railway Station | https://www.wikidata.org/wiki/Q55488<br>http://vocab.getty.edu/aat/300007301 | railway station<br>palaestrae | **fail** | n/a |  |

## subject-tags.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| gold-rush | Gold Rush | https://www.wikidata.org/wiki/Q202191<br>http://vocab.getty.edu/aat/300055547 | Amaya<br>legal concepts | **fail** | https://www.wikidata.org/wiki/Q273182 | NEEDS_VERIFICATION: AAT ID is legal concepts, not gold rush; NEEDS_VERIFICATION for correct AAT |
| water-history | Water History | https://id.loc.gov/authorities/subjects/sh85145505 | Christianity | **fail** | https://id.loc.gov/authorities/subjects/sh85145648 |  |
| indigenous-history | Indigenous History | https://www.wikidata.org/wiki/Q743736<br>https://www.wikidata.org/wiki/Q24251468<br>TONGVA_QID_PENDING_VERIFICATION<br>https://www.wikidata.org/wiki/Q745474<br>https://www.tataviam-nsn.us/<br>https://chumash.gov/<br>https://www.gabrieleno-nsn.us/<br>https://sanmanuel-nsn.gov/ | Tataviam<br>Chumash people<br>PLACEHOLDER<br>117712 Podmaniczky<br>HTTP status-only (200)<br>HTTP status-only (200)<br>HTTP status-only (200)<br>HTTP status-only (200) | **NEEDS_VERIFICATION** | https://www.wikidata.org/wiki/Q1479279; https://www.wikidata.org/wiki/Q617532 | Q743736 live label Tataviam but description=language; people entity is Q1562200; placeholder present; verified correction Q1479279 available |
| railroads | Railroads | https://www.wikidata.org/wiki/Q22667<br>http://vocab.getty.edu/aat/300008187 | railway<br>parks (public recreation areas) | **fail** | n/a | AAT parks URI wrong for railroads |
| oil-industry | Oil Industry | https://www.wikidata.org/wiki/Q11002 | sugar | **fail** | https://www.wikidata.org/wiki/Q862571 |  |
| film-industry | Film Industry | https://www.wikidata.org/wiki/Q11424 | film | **fail** | https://www.wikidata.org/wiki/Q1415395 | Q11424 film is related but not film industry |
| agriculture | Agriculture | https://www.wikidata.org/wiki/Q11451<br>http://vocab.getty.edu/aat/300054258 | agriculture<br>metalinguistics | **fail** | n/a | AAT metalinguistics wrong |
| architecture | Architecture | https://www.wikidata.org/wiki/Q12271<br>http://vocab.getty.edu/aat/300054197 | architecture<br>architectural drawing (process) | **fail** | n/a | AAT is architectural drawing (process), not architecture discipline |
| military-history | Military History | https://www.wikidata.org/wiki/Q104787 | Albert Florath | **fail** | https://www.wikidata.org/wiki/Q192781 |  |
| politics-government | Politics & Government | https://www.wikidata.org/wiki/Q7163 | politics | **pass** | n/a |  |
| law-enforcement | Law Enforcement | https://www.wikidata.org/wiki/Q44554 | law enforcement | **pass** | n/a |  |
| education | Education | https://www.wikidata.org/wiki/Q8434 | education | **pass** | n/a |  |
| religion | Religion | https://www.wikidata.org/wiki/Q9174 | religion | **pass** | n/a |  |
| commerce-trade | Commerce and Trade | https://www.wikidata.org/wiki/Q8374 | cimbalom | **fail** | https://www.wikidata.org/wiki/Q601401 |  |
| land-grants | Land Grants | https://www.wikidata.org/wiki/Q1000743<br>https://id.loc.gov/authorities/subjects/sh85074242 | Budde<br>Lampman family | **fail** | https://www.wikidata.org/wiki/Q3217027; https://id.loc.gov/authorities/subjects/sh85074296 |  |
| natural-disasters | Natural Disasters | https://www.wikidata.org/wiki/Q8060 | Romani people | **fail** | https://www.wikidata.org/wiki/Q8065 |  |
| conservation | Conservation | https://www.wikidata.org/wiki/Q62832 | observatory | **fail** | https://www.wikidata.org/wiki/Q20113959 |  |
| arts-culture | Arts and Culture | https://www.wikidata.org/wiki/Q735 | art | **pass** | n/a |  |
| tataviam-history | Tataviam History | https://www.wikidata.org/wiki/Q743736<br>https://www.tataviam-nsn.us/heritage/history/<br>https://www.tataviam-nsn.us/heritage/territory/ | Tataviam<br>HTTP status-only (200)<br>HTTP status-only (200) | **pass** | https://www.wikidata.org/wiki/Q1562200 | Q743736 live label Tataviam but description=language; people entity is Q1562200 |
| californio-history | Californio History | https://www.wikidata.org/wiki/Q1051342 | Cattini | **fail** | https://www.wikidata.org/wiki/Q2285219 |  |
| chumash-history | Chumash History | https://www.wikidata.org/wiki/Q24251468<br>https://chumash.gov/chumash-history<br>https://chumash.gov/<br>https://northernchumash.org/ | Chumash people<br>HTTP status-only (200)<br>HTTP status-only (200)<br>HTTP status-only (202) | **pass** | n/a |  |
| tongva-history | Tongva History | TONGVA_QID_PENDING_VERIFICATION<br>https://www.gabrieleno-nsn.us/<br>https://gabrielinotongva.org/<br>https://gabrielenoindians.org/ | PLACEHOLDER<br>HTTP status-only (200)<br>HTTP status-only (200)<br>HTTP status-only (200) | **NEEDS_VERIFICATION** | https://www.wikidata.org/wiki/Q1479279 | placeholder present; verified correction Q1479279 available |
| serrano-history | Serrano History | https://www.wikidata.org/wiki/Q745474<br>https://sanmanuel-nsn.gov/<br>https://serranonationofindians.org/ | 117712 Podmaniczky<br>HTTP status-only (200)<br>HTTP status-only (None) | **fail** | https://www.wikidata.org/wiki/Q617532 | serranonationofindians.org did not resolve in this audit |
| kitanemuk-history | Kitanemuk History | https://www.wikidata.org/wiki/Q6422453 | Knights of Father Mathew | **fail** | https://www.wikidata.org/wiki/Q6417841 |  |
| vanyume-history | Vanyume History | https://www.wikidata.org/wiki/Q7915068 | Vankov | **fail** | https://www.wikidata.org/wiki/Q11216568 |  |
| spanish-colonial-expedition | Spanish Colonial Expedition | https://www.wikidata.org/wiki/Q723198<br>https://id.loc.gov/authorities/subjects/sh85011234 | Roberto de Assis Moreira<br>Ballads, Danish | **fail** | https://www.wikidata.org/wiki/Q3966440 | NEEDS_VERIFICATION: Wrong LCSH; NEEDS_VERIFICATION for Spanish expeditions / Portolá LCSH |

## water-type.json

| slug | label | current URIs | live label(s) | pass/fail | corrected URI | notes |
|---|---|---|---|---|---|---|
| dam | Dam | https://www.wikidata.org/wiki/Q12323<br>http://vocab.getty.edu/aat/300006088 | dam<br>cofferdams | **fail** | n/a | AAT cofferdams too narrow/wrong vs dams |
| reservoir | Reservoir | https://www.wikidata.org/wiki/Q134166 | Mahakala | **fail** | https://www.wikidata.org/wiki/Q131681 |  |
| aqueduct | Aqueduct | https://www.wikidata.org/wiki/Q43197<br>http://vocab.getty.edu/aat/300006138 | river delta<br>NO_LABEL/BROKEN | **fail** | https://www.wikidata.org/wiki/Q474 |  |
| canal | Canal | https://www.wikidata.org/wiki/Q12284<br>http://vocab.getty.edu/aat/300006138 | canal<br>NO_LABEL/BROKEN | **fail** | n/a | secondary URI broken/404 |
| spring | Spring | https://www.wikidata.org/wiki/Q188504 | alternative medicine | **fail** | https://www.wikidata.org/wiki/Q1881858 |  |
| well | Well | https://www.wikidata.org/wiki/Q43483 | water well | **pass** | n/a |  |
| treatment-plant | Treatment Plant | https://www.wikidata.org/wiki/Q769626 | Hokkekō | **fail** | https://www.wikidata.org/wiki/Q9341055 |  |

## Special Indigenous QID checks

Live-checked the QIDs called out in the audit brief and `TATAVIAM_AUDIT.md`.

| QID | Live English label | Verdict |
|---|---|---|
| Q1479279 | Tongva people | PASS - correct Tongva/Gabrielino/Kizh people entity |
| Q24251468 | Chumash people | PASS - Chumash people (not Tongva) |
| Q2424671 | (none/unresolved) | FAIL - does not resolve to Tongva (EntityData returns unrelated/missing; redirect path saw Vion Food Group Q495334). Do not use. |
| Q743736 | Tataviam | PARTIAL - label Tataviam but Wikidata description is language. People/ethnic group is Q1562200. |
| Q1562200 | Tataviam | PASS - Tataviam ethnic group (preferred for people/history terms) |
| Q745474 | 117712 Podmaniczky | FAIL - asteroid, not Serrano. Correct: Q617532 Serrano people |
| Q617532 | Serrano people | PASS - verified correction for Serrano |
| Q6422453 | Knights of Father Mathew | FAIL - not Kitanemuk. Correct: Q6417841 |
| Q6417841 | Kitanemuk | PASS - indigenous Californian people |
| Q7915068 | Vankov | FAIL - not Vanyume. Correct: Q11216568 |
| Q11216568 | Vanyume | PASS - Vanyume people |

### Tongva / Gabrielino / Kizh

- **Q1479279** live label: **Tongva people**. Aliases include Kizh, Gabrieleño, Fernandeño, Gabrielino Tongva. This is the correct people entity.
- **Q24251468** live label: **Chumash people**. Confirmed Chumash, not Tongva.
- **Q2424671** does **not** resolve to Tongva. Do not use.
- Current `tongva-history` and `indigenous-history` still contain placeholder `TONGVA_QID_PENDING_VERIFICATION`. Verified replacement: `https://www.wikidata.org/wiki/Q1479279`.

### Tataviam, Serrano, Kitanemuk, Vanyume

- **Tataviam Q743736**: label Tataviam, but Wikidata description is **language**. Ethnic group entity is **Q1562200**. Recommend switching people/history terms to Q1562200 (optionally keep Q743736 as language sameAs). Tribal site URLs resolve HTTP 200.
- **Chumash Q24251468**: correct.
- **Serrano Q745474**: **WRONG** (asteroid 117712 Podmaniczky). Correct: **Q617532** Serrano people. `sanmanuel-nsn.gov` HTTP 200; `serranonationofindians.org` failed to resolve in this run.
- **Kitanemuk Q6422453**: **WRONG** (Knights of Father Mathew). Correct: **Q6417841**.
- **Vanyume Q7915068**: **WRONG** (Vankov). Correct: **Q11216568**.

## Historical era gap (1965-1990 → 2010-present)

`historical-era.json` sequences:

1. incorporation-era-1965-1990 (Incorporation Era 1965-1990)
2. contemporary-2010-present (Contemporary 2010-Present)

**Gap:** years **1990-2010** have no era term. Flagged only. Per instructions, **do not invent** an era. Ask Leon whether to add a 1990-2010 era (and what to call it) or extend one of the adjacent eras.

## Corrections recommended (verified fixes only)

JSON URI edits applied 2026-09-15 (PT) after Nathan approval and live re-check. Status: **applied** or **NEEDS_VERIFICATION**.

| Term slug | Replace | With | Live label at corrected URI | Status (2026-09-15) |
|---|---|---|---|---|
| spanish-american-war | `https://www.wikidata.org/wiki/Q12543` | `https://www.wikidata.org/wiki/Q12583` | Spanish-American War | **applied** |
| korean-war | `https://www.wikidata.org/wiki/Q12023` | `https://www.wikidata.org/wiki/Q8663` | Korean War | **applied** |
| iraq-war-oif | `https://www.wikidata.org/wiki/Q11192` | `https://www.wikidata.org/wiki/Q545449` | Iraq War | **applied** |
| afghanistan-war-oef | `https://www.wikidata.org/wiki/Q171185` | `https://www.wikidata.org/wiki/Q182865` | War in Afghanistan (2001-2021) | **applied** |
| fire | `https://www.wikidata.org/wiki/Q169940` | `https://www.wikidata.org/wiki/Q169950` | wildfire | **applied** |
| dam-failure | `https://www.wikidata.org/wiki/Q1068842` | `https://www.wikidata.org/wiki/Q1033074` | dam failure | **applied** |
| drought | `https://www.wikidata.org/wiki/Q35874` | `https://www.wikidata.org/wiki/Q43059` | drought | **applied** |
| industrial-accident | `https://www.wikidata.org/wiki/Q192316` | `https://www.wikidata.org/wiki/Q629257` | work accident | **applied** |
| expedition-party | `https://www.wikidata.org/wiki/Q748489` | `https://www.wikidata.org/wiki/Q2401485` | expedition | **applied** |
| mission | `https://www.wikidata.org/wiki/Q1156970` | `https://www.wikidata.org/wiki/Q1824509` | Spanish missions in California | **applied** |
| government | `https://www.wikidata.org/wiki/Q7163` | `https://www.wikidata.org/wiki/Q7188` | government | **applied** |
| rancho | `https://www.wikidata.org/wiki/Q3303261` | `https://www.wikidata.org/wiki/Q2679045` | rancho of California | **applied** |
| canyon | `https://www.wikidata.org/wiki/Q354300` | `https://www.wikidata.org/wiki/Q150784` | canyon | **applied** |
| landmark | `https://www.wikidata.org/wiki/Q231021` | `https://www.wikidata.org/wiki/Q4895393` | landmark | **applied** |
| oil-field | `https://www.wikidata.org/wiki/Q202822` | `https://www.wikidata.org/wiki/Q211748` | oil field | **applied** |
| ranch | `https://www.wikidata.org/wiki/Q3303261` | `https://www.wikidata.org/wiki/Q509028` | ranch | **applied** |
| gold-rush | `https://www.wikidata.org/wiki/Q202191` | `https://www.wikidata.org/wiki/Q273182` | gold rush | **applied** (AAT still wrong: NEEDS_VERIFICATION) |
| oil-industry | `https://www.wikidata.org/wiki/Q11002` | `https://www.wikidata.org/wiki/Q862571` | petroleum industry | **applied** |
| film-industry | `https://www.wikidata.org/wiki/Q11424` | `https://www.wikidata.org/wiki/Q1415395` | film industry | **applied** |
| military-history | `https://www.wikidata.org/wiki/Q104787` | `https://www.wikidata.org/wiki/Q192781` | military history | **applied** |
| commerce-trade | `https://www.wikidata.org/wiki/Q8374` | `https://www.wikidata.org/wiki/Q601401` | trade | **applied** |
| land-grants | `https://www.wikidata.org/wiki/Q1000743` | `https://www.wikidata.org/wiki/Q3217027` | land grant | **applied** |
| land-grants | `https://id.loc.gov/authorities/subjects/sh85074242` | `https://id.loc.gov/authorities/subjects/sh85074296` | Land grants | **applied** |
| natural-disasters | `https://www.wikidata.org/wiki/Q8060` | `https://www.wikidata.org/wiki/Q8065` | natural disaster | **applied** |
| conservation | `https://www.wikidata.org/wiki/Q62832` | `https://www.wikidata.org/wiki/Q20113959` | nature conservation | **applied** |
| californio-history | `https://www.wikidata.org/wiki/Q1051342` | `https://www.wikidata.org/wiki/Q2285219` | Californio | **applied** |
| serrano-history | `https://www.wikidata.org/wiki/Q745474` | `https://www.wikidata.org/wiki/Q617532` | Serrano people | **applied** (prior Indigenous pass 7fcb8d3) |
| kitanemuk-history | `https://www.wikidata.org/wiki/Q6422453` | `https://www.wikidata.org/wiki/Q6417841` | Kitanemuk | **applied** (prior Indigenous pass 7fcb8d3) |
| vanyume-history | `https://www.wikidata.org/wiki/Q7915068` | `https://www.wikidata.org/wiki/Q11216568` | Vanyume | **applied** (prior Indigenous pass 7fcb8d3) |
| tongva-history | `TONGVA_QID_PENDING_VERIFICATION` | `https://www.wikidata.org/wiki/Q1479279` | Tongva people | **applied** (prior Indigenous pass 7fcb8d3) |
| indigenous-history | `TONGVA_QID_PENDING_VERIFICATION` | `https://www.wikidata.org/wiki/Q1479279` | Tongva people | **applied** (prior Indigenous pass 7fcb8d3) |
| indigenous-history | `https://www.wikidata.org/wiki/Q745474` | `https://www.wikidata.org/wiki/Q617532` | Serrano people | **applied** (prior Indigenous pass 7fcb8d3) |
| spanish-colonial-expedition | `https://www.wikidata.org/wiki/Q723198` | `https://www.wikidata.org/wiki/Q3966440` | Portola expedition | **NEEDS_VERIFICATION** (live label is specific Portola expedition, not general Spanish colonial expedition; old Wikidata+LCSH left unchanged) |
| water-history | `https://id.loc.gov/authorities/subjects/sh85145505` | `https://id.loc.gov/authorities/subjects/sh85145648` | Water-supply | **applied** |
| reservoir | `https://www.wikidata.org/wiki/Q134166` | `https://www.wikidata.org/wiki/Q131681` | reservoir | **applied** |
| aqueduct | `https://www.wikidata.org/wiki/Q43197` | `https://www.wikidata.org/wiki/Q474` | aqueduct | **applied** (AAT 404 still present: NEEDS_VERIFICATION) |
| spring | `https://www.wikidata.org/wiki/Q188504` | `https://www.wikidata.org/wiki/Q1881858` | spring water | **applied** |
| treatment-plant | `https://www.wikidata.org/wiki/Q769626` | `https://www.wikidata.org/wiki/Q9341055` | water treatment plant | **applied** |
| tataviam-history | `https://www.wikidata.org/wiki/Q743736` | `https://www.wikidata.org/wiki/Q1562200` | Tataviam (ethnic group) | **applied** (prior Indigenous pass 7fcb8d3) |
| tataviam-pre-contact | `https://www.wikidata.org/wiki/Q743736` | `https://www.wikidata.org/wiki/Q1562200` | Tataviam (ethnic group) | **applied** (prior Indigenous pass 7fcb8d3) |

### AAT / LCSH left flagged (no verified replacement; not invented)

| Term slug | Issue | Status |
|---|---|---|
| spanish-colonial-1769-1821 | AAT 300417650 = consignment | **NEEDS_VERIFICATION** |
| american-frontier-1848-1876 | AAT 300417720 = elephant ivory | **NEEDS_VERIFICATION** |
| gold-rush | AAT 300055547 = legal concepts (Wikidata fixed) | **NEEDS_VERIFICATION** |
| agriculture | AAT 300054258 = metalinguistics | **NEEDS_VERIFICATION** |
| architecture | AAT 300054197 = architectural drawing (process) | **NEEDS_VERIFICATION** |
| railroads | AAT 300008187 = parks | **NEEDS_VERIFICATION** |
| railway-station | AAT 300007301 wrong/palaestrae | **NEEDS_VERIFICATION** |
| dam | AAT 300006088 = cofferdams | **NEEDS_VERIFICATION** |
| aqueduct | AAT 300006138 broken/404 | **NEEDS_VERIFICATION** |
| canal | AAT 300006138 broken/404 | **NEEDS_VERIFICATION** |
| educational-institution | AAT broken/wrong | **NEEDS_VERIFICATION** |
| cemetery | AAT 300005865 wrong/404 | **NEEDS_VERIFICATION** |
| spanish-colonial-expedition | LCSH sh85011234 = Ballads, Danish; Wikidata Q3966440 too specific | **NEEDS_VERIFICATION** |

Also remove or replace wrong AAT/LCSH URIs that fail live label checks only after a verified correct AAT/LCSH ID is found. Where no verified replacement AAT ID was found, mark **NEEDS_VERIFICATION** rather than inventing.

## Questions for Leon

1. **Era gap 1990-2010:** Add a new historical era, extend Incorporation Era through 2010, or pull Contemporary start back to 1990? What label?
2. **Tataviam QID:** Prefer people entity Q1562200 for Tataviam History / Pre-Contact, keep language Q743736 as additional sameAs, or keep Q743736 only as in prior audit?
3. **Tongva placeholder:** Approve replacing `TONGVA_QID_PENDING_VERIFICATION` with Q1479279 everywhere?
4. **Serrano / Kitanemuk / Vanyume:** Approve Q617532 / Q6417841 / Q11216568 replacements? Confirm San Manuel Band remains primary authority note for Serrano/Vanyume.
5. **Spanish colonial expedition:** Is Portolá expedition (Q3966440) the right primary Wikidata anchor, or a broader Spanish expeditions concept?
6. **Mission org-subtype:** Is Spanish missions in California (Q1824509) acceptable, or a more generic mission/station concept?
7. **Fire disaster:** Prefer wildfire (Q169950) or broader conflagration (Q168983)?
8. **Neighborhoods / periods / confidence / local eras:** Leave empty sameAs (Local-SCV), or invest in TGN/Wikidata lookups for major neighborhoods (Newhall, Saugus, Valencia, Castaic, etc.)?
9. **Broken tribal URL:** `https://serranonationofindians.org/` did not resolve here. Keep, replace, or drop?
10. **Getty AAT cleanup:** Many AAT IDs in the import files resolve to unrelated concepts or 404. Prioritize a dedicated AAT pass after Wikidata fixes?

## Explicit confirmation

- **2026-09-15 (PT) evening:** Non-Indigenous verified Wikidata/LCSH URI corrections applied after Nathan approval and live re-check (31 replacements across 7 JSON files).
- Indigenous terms fixed in 7fcb8d3 were **not** modified in this pass.
- spanish-colonial-expedition and wrong AAT URIs without verified replacements left unchanged and marked **NEEDS_VERIFICATION**.
- Craft/DDEV/Jordy not touched. No git commit/push in this pass (parent will commit).
- Raw verification trail remains in `taxonomy-import/_verification-raw.json` (audit-time fetch log).

