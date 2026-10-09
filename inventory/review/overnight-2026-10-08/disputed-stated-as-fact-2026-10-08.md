# Disputed in our notes, stated as fact in the archive

Claude, 8 October 2026 (overnight, item 12). Read-only: nothing was written to Craft. Craft was read through read-only probes in DDEV; the notes read were TODO.md, HANDOFF.md, CHANGELOG.md, ERRORLOG.md, CONTENT-MODEL.md (Open Questions), docs/PROFILES.md and the review files named below (the HMN, Ygnacio, Beale, Hart, Reynolds and Perkins dossiers and the Perkins triage; the disaster figures, appointed terms, Hart before 1995, term endings, redevelopment, sheriff and Placerita drafts). The Reggie drive refused access ('Operation not permitted'), so no legacy page was reopened; where a record's text is said to come from a legacy page, that is from the record or its script, not from the page.

Nathan's request: "Any place where the archive states something as fact that our own notes record as disputed or undecided. The Stearns date was found by accident. Look for the rest."

## Headline

- **(a) Stated as plain fact, no hedge on the record: 12 items** on 13 records.
- **(b) One side stated, but the record flags the dispute: 11 items** (Stearns's mint date is here: its source fault shows on the page).
- (c) Both sides shown or neither stated: 16.
- (d) Not stated in published Craft text (absent, withheld or disabled): 11.
- (e) Legacy text as printed, one side, no correction note: 9 groups. Leon's, Perkins's and Reynolds's prose is not an archive error, but these are points our dossiers call disputed where no correction note has yet been added, as was done for the del Valle death date.
- Read from a description and stated as fact: 5 instances (section at the end).

The worst three: Henry Mayo Newhall's birthday goes out on On This Day as 13 May with no qualifier while the dossier calls it unresolved (A1); two public texts still state the 'prefer Perkins' rule that was narrowed on 4 October (A2); and the Rancho Camulos page, composed by an agent from Reynolds before the dossier, states five points the dossier calls disputed (A3).

Method: every item recorded as disputed, conflicting, unresolved, NEEDS_VERIFICATION or waiting on Nathan was collected, deduplicated and checked against later CHANGELOG entries (settled ones are left out: Stern's term, Chico López's likeness, Aliano's date, the Hanrion roster slip). Each was then looked for in Craft by a regex search across every live entry's fields (title, body, footnotes, editor notes, date fields, recordDates, factSources, significance, naming notes), with legacy article, document and photograph bodies set apart as (e). Confirmed recordDates rows and date fields were also checked in templates/_data/calendar.json, since On This Day publishes them without the record's notes. What this cannot see: a disputed point worded differently from every pattern searched; and dossier conflicts about pages not yet migrated.

## (a) Stated as plain fact, no hedge

### A1. Henry Mayo Newhall's birth day: May 13 or May 23, 1825

- **Note:** inventory/review/henry-mayo-newhall-sources.md:7 and :322 (C1): 'Born 1825-05-13 ... or 1825-05-23 (four 1882 newspapers) ... [conflicting (NEEDS_VERIFICATION; C1)]'; 'Evidence favors: Unresolved.'
- **Record #283** (persons), birthDate / birthDateEdtf: May 13, 1825 / 1825-05-13 (no qualifier; birthEvidence empty)
- **Record #283** (persons), recordDates (confirmed row): May 13, 1825, confirmed: On This Day lists 'Henry Mayo Newhall, Born' on 13 May (templates/_data/calendar.json)
- **Hedge:** None on the record. The legacy body (legacy-leon, Ruth Newhall 1992) also opens 'Born May 13, 1825'; no editor note mentions May 23.
- **Would settle it:** Per the dossier: a birth or baptismal record from Saugus, Massachusetts; the independence of the four 1882 papers is itself NEEDS_VERIFICATION.

### A2. The 'prefer Perkins' rule, narrowed on 4 October to his verbatim transcriptions

- **Note:** inventory/review/a-b-perkins-sources.md:652 (K9): 'The rule now trusts his verbatim transcriptions only'; :655 (K12): 'neither figure is preferred until the deed is seen'; docs/PROFILES.md:154.
- **Record #333** (persons), body: '...so where a later retelling departs from him on a figure, the archive prefers Perkins until an original is seen.'
- **Record #1434** (articles), editorNotes, 'Reading this document': '...this archive prefers his figures to later retellings until an original is seen.'
- **Hedge:** Both go on to say his figures 'still need checking', but both state as the archive's present policy the preference Nathan approved withdrawing (K9, K12; the Bard sale P32 is 'no default').
- **Would settle it:** Nathan's wording; the rule as stated in PROFILES.md and K9.

### A3. Rancho Camulos: adobe date, acreage inherited, children, burial, Jackson's visit

- **Note:** inventory/review/ygnacio-del-valle-sources.md:247 (C11: first four rooms 1853, about 20 by the 1860s-70s, exact date NEEDS_VERIFICATION); :204 (C7: the Reynolds/Smith figures 'cannot all be right'); :269 (C13: 12 born, 5 or 6 surviving); :280 (C14: Camulos burial and the 1905 Calvary re-interment NEEDS_VERIFICATION); :343 (C20: day agrees, year does not, NEEDS_VERIFICATION); jerry-reynolds-sources.md:226 (D13, 'conflicting').
- **Record #631** (places), body: 'in 1861 a twenty-room adobe rose'; 'Camulos was whittled from the 16,599 acres inherited from Don Antonio to 1,340'; 'raised eleven children here'; 'buried in the family crypt a mile north of the house'; 'Helen Hunt Jackson arrived on January 23, 1882'
- **Hedge:** None. No footnotes, no editor notes. The body was composed by the archive (fill_place_stub_bodies.php, 18 September 2026) from Reynolds and predates the dossier.
- **Would settle it:** Per the dossier: NRHP/Triem and Stone for the adobe; the 1870 partition decree; the sacramental registers; Calvary's records; Jackson's dated letters.

### A4. Northridge earthquake deaths and cost

- **Note:** TODO.md:68 ('its 57 dead and $13 to $50 billion sit in eventSignificance and the withheld body with no footnote'); inventory/review/disaster-figures-audit-2026-10-07.md:247 and :816 (53, 57 or 60 dead; $11 to $50 billion; 'both should be treated as unsourced').
- **Record #875** (events), eventSignificance: 'causing 57 deaths and over $20 billion in damage across the region'; also carried into On This Day for 17 January
- **Hedge:** None; no footnote. The figures disagree with the footnoted sources (USGS 60; Worden's chronology 53).
- **Would settle it:** The figures table the disasters design calls for, one row per figure per source.

### A5. Ruiz family dead in the St. Francis Dam flood: six, seven or eight

- **Note:** inventory/review/disaster-figures-audit-2026-10-07.md:50-51 and :771 ('The Ruiz family dead, 6, 7 or 8').
- **Record #2536** (places), body: 'Six members of the Ruiz family died when the dam broke on March 12, 1928: the parents, Rosaria and Enrique, and their four children'
- **Hedge:** None; no footnote. Composed by add_missing_places.php. Leon's LW2154 captions give seven (including a married daughter); his 2003 column, eight buried.
- **Would settle it:** Stansell's roster (annstansell_damvictims011718.pdf, not yet read) or the family's burial records.

### A6. Powerhouse Fire homes lost: 30 or 'at least 24'

- **Note:** inventory/review/disaster-figures-audit-2026-10-07.md:287 ('Homes: 30 total losses (Worden, chronology) against at least 24 (NASA)').
- **Record #31891** (events), eventSignificance: '...Lake Hughes and Elizabeth Lake, where 30 homes were lost. By Leon Worden's account it blackened 30,274 acres.'
- **Hedge:** The acreage is attributed; the homes figure is not.
- **Would settle it:** The agency's final incident report.

### A7. Beale's Cut depth: 90 feet

- **Note:** inventory/review/edward-f-beale-sources.md:319 (C11: ''90 feet' is the traditional figure ... but no survey is cited ... Measure it or find an engineering survey'); perkins-open-claims-triage-2026-10-04.md:32 (P27: class (e), nothing reachable would settle it).
- **Record #932** (places), namingNote: 'The cut was completed by Edward F. Beale's hired hands to its full 90-foot depth in 1864 (Leon Worden, "La Puerta," 2023).'
- **Hedge:** Cited to Worden 2023 but stated as fact. The body attributes 'a ninety-foot slash' to Reynolds, which is the better form.
- **Would settle it:** An engineering profile (County Surveyor or Road Department) or a measurement.

### A8. Stearns: the dream and the oak 'enter the story in 1930'

- **Note:** inventory/review/placerita-gold-discovery-draft-2026-10-05.md:169 (For Nathan, item 4): an oak is already in Prudhomme's 1922 account; recommends rewording #309. No ruling found in CHANGELOG or TODO.
- **Record #309** (persons), body: 'two things are not in it: a dream, and any particular oak. Both enter the story in 1930.'
- **Hedge:** None for the oak; the sentence rests on Leon's note on the Sentinel page (footnote 3).
- **Would settle it:** Nathan's word on the draft's proposed sentence.

### A9. Francisco Lopez: 'two thousand miners' and where the petition is

- **Note:** inventory/review/placerita-gold-discovery-draft-2026-10-05.md:171 (For Nathan, item 5): both rest on Leon's 2005 column repeating Reynolds ch. 16, 'uncorroborated' under the Reynolds and Leon-independence rules; the petition is 'in the National Archives' against Perkins's Sacramento.
- **Record #18834** (persons), body: 'Some two thousand miners, most from Sonora, worked the canyon in the years after.[1]'; 'the petition is in the National Archives.'
- **Hedge:** None.
- **Would settle it:** Nathan's word on the draft; attribution to Reynolds as recommended.

### A10. Newhall Redevelopment Committee terms: 'without term limits' or four-year terms

- **Note:** TODO.md:135 (waiting on Nathan); inventory/review/aadusd-redevelopment-2026-10-04.md:130ff, open question 4.
- **Record #16290** (organizations), body: 'Its members were appointed by the City Council, without term limits'
- **Hedge:** None visible; the 2005 source (Ellis) giving four-year terms is not mentioned.
- **Would settle it:** Nathan's choice, or the City's resolution creating the committee.

### A11. McGrath's 2009 term, never served

- **Note:** inventory/review/term-endings-2026-10-04.md:21 ('McGrath 2009 to 2013 (28455) was never served ... the holding may need removing rather than redating. Nathan's call.').
- **Record #28455** (officeHoldings), the record itself; termStart/termEnd/howEnded: December 2009 to December 8, 2009, howEnded resigned, enabled: the archive lists a term he held
- **Hedge:** Footnote 4 quotes the district: he 'would be unable to serve' and resigned effective 8 December, so the record's own note contradicts it. Footnote 2 says he 'did not stand at the next election ... November 5, 2013', a term-end note for a term that did not run.
- **Would settle it:** Nathan's call: remove or keep as a candidacy outcome only.

### A12. Ygnacio del Valle's birthplace and burial (record fields)

- **Note:** inventory/review/ygnacio-del-valle-sources.md:148 (C1: 'Use Nueva Galicia (now Jalisco/Nayarit) until a baptismal entry is seen: NEEDS_VERIFICATION'); :280 (C14: burial at the Camulos plot best supported; the parish entry and the 1905 Calvary re-interment NEEDS_VERIFICATION).
- **Record #293** (persons), birthplace: Jalisco, Mexico
- **Record #293** (persons), burialPlace: Camulos Ranch, Ventura County, California
- **Hedge:** Antonio's record (#291) carries a birthplace editor note (Jalisco, Composilla, Compostela); Ygnacio's does not. Lower stakes than the others.
- **Would settle it:** A baptismal entry; Calvary Cemetery's records.

## (b) One side stated, the dispute flagged on the record

### B1. Stearns's gold at the mint: 8 July or 8 June 1843

- **Note:** TODO.md:114 ('Stearns's mint date, a disagreement between his letter and the voucher'); scripts/import/source_fault_stearns_mint_2026_10_05.php ('Not decided').
- **Record #309** (persons), body: 'where it was deposited on 8 July 1843 and valued at $344.75'
- **Hedge, and whether a reader sees it:** Visible: source fault #30537 ('8 July or 8 June 1843: not decided') renders after the text on person pages; the gold event #31370 states both dates.
- **Would settle it:** The voucher itself (Treasury records).

### B2. Abel Stearns's birth day, 9 February

- **Note:** Record #309 editorNotes: 'No source has been found for the day, 9 February.' (birthEvidence uncited)
- **Record #309** (persons), birthDate / birthDateEdtf: February 9, 1798 / 1798-02-09
- **Record #309** (On This Day), calendar.json 02-09: 'Abel Stearns, Born', 1798-02-09
- **Hedge, and whether a reader sees it:** Visible on the record page; not on On This Day, which lists the unsourced day as a birthday.
- **Would settle it:** A source for the day.

### B3. Edward F. Beale's burial place

- **Note:** inventory/review/edward-f-beale-sources.md:210 (C1: 'buried Chester Rural Cemetery ... confirmed by the cemetery listing'); record #327 editor note says no source found.
- **Record #327** (persons), burialPlace: Rock Creek Cemetery, Washington, D.C. (burialEvidence uncited)
- **Hedge, and whether a reader sees it:** Visible editor note: 'No source has been found for his burial place.' But the field still prints a place the archive's own dossier contradicts.
- **Would settle it:** The dossier's Chester cemetery listing, already found.

### B4. Jerry Reynolds's burial place

- **Note:** Record #281 editor note: 'has no reliable source ... a Find a Grave memorial whose name, date and place of birth and spouse do not match his'.
- **Record #281** (persons), burialPlace: Eternal Valley Memorial Park, Newhall, California (burialEvidence empty)
- **Hedge, and whether a reader sees it:** Visible editor note; the field still prints the place.
- **Would settle it:** A cemetery record or obituary.

### B5. William S. Hart's birth year

- **Note:** inventory/review/william-s-hart-sources.md:422 (C1; evidence favors 1864).
- **Record #16356** (persons), birthDate / EDTF / confirmed recordDates: December 6, 1864 / 1864-12-06; On This Day lists him born 6 December 1864
- **Hedge, and whether a reader sees it:** Visible: body says the year is disputed, an editor note sets out 1862 to 1874, the recordDates label says 'the year disputed'. On This Day prints 'Born' with no qualifier.
- **Would settle it:** A birth record.

### B6. Antonio del Valle's birthplace

- **Note:** Record #291 editorNotes (Jalisco, Composilla, Compostela); ygnacio-del-valle-sources.md:148.
- **Record #291** (persons), birthplace: Jalisco, Mexico
- **Hedge, and whether a reader sees it:** Visible editor note.
- **Would settle it:** A baptismal entry.

### B7. Alec Mentry's birth year, 1847 or 1848

- **Note:** scripts/import/source_fault_mentry_birth_2026_10_05.php; record #18648 editor note 'Source fault: the birth year'.
- **Record #18648** (persons), birthDate: March 27, 1847 (EDTF 1847?-03-27)
- **Hedge, and whether a reader sees it:** Visible: editor note and body; the printed field carries no '?', the EDTF does. Not on On This Day (the '?' keeps it out).
- **Would settle it:** The certificate scan read against the legacy page's quotation.

### B8. Peter Warren's resignation: March or 6 April 1994

- **Note:** TODO.md:128; inventory/review/hart-pre1995-terms-2026-10-05.md:77.
- **Record #28805** (officeHoldings), termEnd: March 1994
- **Hedge, and whether a reader sees it:** Visible editor note: 'The Times is followed here, and the two disagree.'
- **Would settle it:** Board minutes, spring 1994.

### B9. Loberg and King, 1993: did not seek reelection, or stood and lost

- **Note:** TODO.md:128 ('a "defeated" ending needs a new option'); hart-pre1995-terms-2026-10-05.md:78.
- **Record #28801** (officeHoldings), howEnded: expired
- **Record #28592** (officeHoldings), howEnded: expired
- **Hedge, and whether a reader sees it:** Visible editor notes say the roster is wrong and they lost. 'expired' is the nearest existing option; the new option waits on Nathan.
- **Would settle it:** Nathan's word on a 'defeated' option.

### B10. War memorial facts that differ from federal sources (Cone, Acosta, Sellen, Gelig, Todd, Suter)

- **Note:** TODO.md:132 ('shown on the records and not changed').
- **Record #576** (warMemorials), wmBranch / deathDate: U.S. Army / March 13, 1945 (ABMC and the Navy: Seaman 2c, USN, a different date)
- **Record #526** (warMemorials), wmAgeAtLoss / wmRank: 20 / Specialist 4th Class (DoD: 19, PFC)
- **Record #540** (warMemorials), wmRank: Sergeant (DoD: Specialist)
- **Record #538** (warMemorials), wmRank: Sergeant (DoD: Specialist)
- **Record #542** (warMemorials), wmRank: Sergeant (Stars and Stripes: Specialist)
- **Record #536** (warMemorials), wmHomeOfRecord: Stevenson Ranch (DoD release: Los Angeles)
- **Hedge, and whether a reader sees it:** Visible: factSources 'Where the sources differ' column and editor notes on each. Kept on Nathan's decision; listed so the count is complete.
- **Would settle it:** Nathan's decision of 4 October stands.

### B11. St. Francis Dam death toll

- **Note:** inventory/review/disaster-figures-audit-2026-10-07.md:763ff ('The worst disagreements', item 1).
- **Record #31342** (events), eventSignificance: 'killing an estimated 411 people'
- **Hedge, and whether a reader sees it:** 'estimated' in the summary; the body explains Stansell's 431 and Worden's revision to 411 with notes. The whole-path scope is not said in the summary.
- **Would settle it:** The figures table, scope stated.

A pattern across B2, B5 and A1: On This Day (calendar.json) prints a date field or confirmed row with no qualifier, so a hedge that lives in an editor note never reaches the calendar.

## (c) Both sides shown, or neither stated

| Item | Where it stands |
|---|---|
| Scott Newhall joined the Chronicle 1934 or 1935 | #31431 body: 'in 1934 by one account and 1935 by another' |
| Placerita gold, the year (1834 to 1842) | #31370 body and editor note 'The date in the sources' set out every year; eventDate 1842-03-09 from the petition |
| Stearns mint date on the gold event | #31370 body: 'Both dates are kept.' |
| Hart: 254-acre purchase; Westover's age | #16356 body and #2131 correction notes give the deeds and 22 |
| Hart: middle name | #16356 editor note gives Surrey, Surry, Shakespeare |
| Beale: Navy resignation; ranch acreage | #327 body: 'May 1851 by one account and November 1852 by Reynolds's'; '297,000 acres by Reynolds's count, 270,000 by the Air Force's' |
| Rancho San Francisco partition figures | #16446 body and #851, #293 correction notes: 'cannot all be right' |
| Antonio del Valle's death (21 June / 12 June / on or before 3 June 1841) | #291 field 'on or before 3 June 1841'; correction notes on #293, #847, #1434, #3991 to #3999 |
| Western Air Express Flight 7, two or five dead | #31914 body explains the count grew; significance gives the final five |
| Santa Clarita courthouse: 1968, 1970 or 1972 | #29882 and #29886 editor notes: 'None is followed here.' |
| Newhall Elementary: third school 1911 or 1914; the 1914 fire | #15958 body gives both and calls the fire unconfirmed; the move to Walnut Street given as 1925 from contemporary reports |
| DeFigueiredo 2007: how he took the seat | #28497 footnote: 'not known'; selectionMethod left empty |
| Hanrion 1997: 'did not seek reelection' against 'reelected 1997' | #28616/#28618: CEDA 1997 shows her elected; the roster's slip is not repeated |
| Aliano's appointment | #28602 start May 1994 from the Times; footnote 2 explains the roster's silence |
| Talley 2016 and Moore 2017 appointments (partly right runs) | #29006 and #28938 give what the district pages say; Moore's footnote says the month and board action are not held (see Read from a description for their evidence level) |
| Couts and Helen Hunt Jackson, 1882 | #323 body attributes 1882 to Worden and the NRHP nomination |

## (d) Not stated in published Craft text

| Item | Where it stands |
|---|---|
| Southern Hotel built 1876, 1877 or 1878 (HMN C11) | #20118 has no body or date field; only legacy texts give a year |
| Bien's end date as interim city manager | #394 and #16396 state no end date |
| Acton-Agua Dulce: which high school district it left in 1993 (TODO.md:136) | #29691 does not say |
| The Sheriff's first contract date (TODO.md:127) | #29282 has no body |
| The fallen officers' conflicting 'firsts' (Brown 1924 or Pelino 1978) | the fallen officer records are disabled |
| The 490 date decisions (TODO.md:133) | unconfirmed recordDates rows; build_calendar_index.php reads only confirmed rows, so none publishes |
| Antonio del Valle's death, June 21, 1841, and 'eight children', in the del Valle Family group | #915 withheldBody, which does not publish |
| Beale's Cut: 'created in 1859', 'Chinese immigrant labor' | #932 withheldBody, which does not publish (and a recordDates row, unconfirmed) |
| Rancho El Tejon: Beale 'acquired the rancho' in 1855 | #16506 withheldBody, which does not publish; the published body is corrected |
| Tejon Indian Tribe reaffirmation date (Beale C23) | not in Craft |
| CONTENT-MODEL.md Open Questions (seven Place entries, community terms, collection titles) | classification and naming questions, not factual claims; nothing to check |

## (e) Source text as printed, one side, no correction note

| Point our notes call disputed | Where it is printed |
|---|---|
| HMN: 'one of the founders of the Southern Pacific' (C6 unsupported); malaria in Chicago and the cause of death (C12, C13 unresolved); five ranchos | #283 body, legacy-leon (Ruth Newhall 1992). No correction note. The same claims sit in #283 authorBio, which shows only where he is an author. |
| HMN acreage, 143,000 or 114,271 (C7 unresolved) | #3023, #12294 (143,000) and #2099 (114,271) |
| Ygnacio's mother 'María Josepha (Carillo)' (C2: 'Do not migrate a maiden name until a sacramental record is seen'); Antonio's 'eight children' (Reynolds D8 conflicting); 'Beale had already picked up the deed to Rancho Tejon' (C17 NEEDS_VERIFICATION) | #293 body, legacy-leon. Five correction notes on the record; none on these three. |
| Oil on Rancho San Francisco: 1933 or December 1936 | #293 body says 1933; #283 body says December 1936; neither noted |
| Bard's purchase: $53,320 on 29 April or $47,519.71 on 18 March 1865 (P32, 'no default') | #2083 (Reynolds 29) and #1434 (Perkins 1957); no note on either |
| Perkins's '2,000 ounces' that 'Bancroft says' (P29: not in Bancroft); Reynolds's '125 pounds' | #1424, #1434 (Perkins), #853 (Reynolds); no correction note |
| Camels landed at Indianola: 13 May, 10 February or 29 April 1856 (Reynolds B11 conflicting) | #2071 (Reynolds), #2941 and related Hi Jolly photographs, #12168 |
| Tom Dunn took over the Robbins interest in 1873 (P34, against Ripley's 1870) | #1426 (Perkins); no note |
| Andrés Pico distilled oil in 1855 (P36) | #1440 (Perkins), #2087 (Reynolds), #28060 (Leon's caption hedges it with an editor's aside, 'or maybe not') |

## Read from a description

Facts or ratings stated on a record where what was read was a description of the source (a legacy page's quotation, a dossier's report, a web page standing for a certificate), not the source. Listed, not fixed.

- **#18648** (persons), deathEvidence = certified; body (death place and cause): Rated certified and stated as fact ('He died at California Hospital ... of typhoid fever, with chronic nephritis contributing'), while footnote 18 says the certificate is quoted 'as the legacy page quotes it; the scan has not yet been read against the quotation.' The legacy page's description of the certificate, not the certificate, is what was read.
- **#291** (persons), body (the 1875 patent, 48,611.88 acres); footnote 4: Footnote 4: 'As read for the archive's del Valle source review of 29 September 2026; the patent itself is not in the archive.' The acreage is stated as fact from a dossier's report of the patent.
- **#16446** (places), body ('patented in 1875 at 48,611.88 acres'); footnote 5: Cites the patent 'as cited on Antonio del Valle's record' (#291 note 4): a description of a description.
- **#29006** (officeHoldings), startEvidence / endEvidence = certified: Isaiah Talley's appointment of 6 September 2016 rests on the district's web page ('Governing Board Members'), a description; DATA-MODEL reserves certified for a certificate the archive holds.
- **#28938** (officeHoldings), startEvidence = certified: Cherise Moore's start '2017' rests on the district's web page ('since 2017'); the footnote says the month, seat and board action are not held.

Disclosed on the record, so not counted above:
- #291 Antonio del Valle: the burial of 3 June 1841 and Magdalena's baptism are from ECPP's typed index, not the register pages; footnotes 7 and 8 say so.
- #309 Abel Stearns: the mint figures are from Robinson's copy as printed in 1885; footnote 2 says so.

JSON beside this file: disputed-stated-as-fact-2026-10-08.json.
