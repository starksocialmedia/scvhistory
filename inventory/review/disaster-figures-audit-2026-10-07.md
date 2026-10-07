# Disaster figures audit

Claude, 7 October 2026. Read-only. Nothing was written to Craft. Sources: the Craft events section (DDEV, read through each entry's field layout) and the legacy mirror at `/Volumes/Reggie/SCVHistory/scvhistory.com` (paths below are relative to it). No live site was fetched.

This is the first job of the disasters design question (DATA-MODEL.md, "Disasters and their consequences: the figures"). DATA-MODEL points at `disaster-figures-audit-2026-10-06.md`; this file is that audit, dated the day it was done.

## How to read the tables

- One row per figure per source, quoted as printed. Nothing is converted, rounded or reconciled.
- **Scope**: what the figure counts as the source puts it. "Whole flood path", "Southland", "statewide", "county", a named community, or "this valley". "Scope not stated" where the source does not say.
- **Kind of source**: *primary* (an official or first-hand report by a party to the event: claims committee, ranch owner's report, fire agency incident report, accident board, a letter written at the time); *contemporary* (press of the day or within weeks); *retrospective* (a later local account by a named historian or by Leon Worden, Alan Pollack or the Historical Society); *secondary* (later press, proclamations, captions and catalog notes of unknown date).
- **As of**: the date the source states the figure, not the date of the event.
- Measures: deaths, injuries, acres (burned or flooded), structures destroyed, structures damaged, cost, displaced or evacuated.
- A **Craft** path means the figure was read from the Craft record (body, footnotes, editor notes or research leads) and the mirror page was not reread.

## Events audited

Craft holds 14 events. Seven are disasters: St. Francis Dam #31342, Great Flood of 1938 #31904, Sylmar Earthquake #31893, Northridge Earthquake #875, Powerhouse Fire #31891, United Air Lines Flight 34 #31907, Western Air Express Flight 7 #31914. The Saugus High School Shooting #31338 is a crime, not audited here (see the findings). The A and B disaster entries of `inventory/review/events-missed-2026-10-05.md` that are not yet events follow the Craft events.


## St. Francis Dam Disaster

- Status: Craft event #31342
- Date: 12-13 March 1928
- Kind: structural failure (dam failure) causing a flood


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | probably almost five hundred | persons | whole flood path (dam to sea; two counties) | 24 Mar 1928 | Newhall Land and Farming Co., Report on St. Francis Dam Flood | `/scvhistory/nlf-stfrancis.htm` | primary |  |
| 2 | Deaths | 200 TO 500 DIE AS DAM BREAKS | persons (headline) | whole flood path | 13 Mar 1928 | Albany Evening News | `/scvhistory/aen031328.htm` | contemporary |  |
| 3 | Deaths | 200 Dead | persons (headline) | whole flood path | 13 Mar 1928 | The Detroit News (AL2034) | `/scvhistory/al2034.htm` | contemporary |  |
| 4 | Deaths | SOME ESTIMATE DEATH TOLL MAY GO AS HIGH AS 400 | persons (headline) | whole flood path | 13 Mar 1928 | Topeka State Journal (AL2038) | `/scvhistory/al2038.htm` | contemporary | same headline: 'Dam Breaks, 200 Dead' |
| 5 | Deaths | Death List May Reach 400---100 Bodies Recovered | persons (headline) | whole flood path | 13 Mar 1928 | Lewiston Evening Journal (AL2042) | `/scvhistory/al2042.htm` | contemporary |  |
| 6 | Deaths | 251 DEAD IN FLOOD, 600 MISSING | persons (headline) | whole flood path | 14 Mar 1928 | Oakland Tribune (AL2055) | `/scvhistory/al2055.htm` | contemporary |  |
| 7 | Deaths | Flood-Swept Canyon Gives Up 274 Bodies; 800 Missing | bodies recovered; missing | whole flood path | 14 Mar 1928 | Dallas Morning News | `/scvhistory/dmn031428.htm` | contemporary |  |
| 8 | Deaths | More Than 700 Persons Are Reported As Missing | missing persons | whole flood path | 14 Mar 1928 | Omaha Morning Bee-News | `/scvhistory/ombn031428.htm` | contemporary |  |
| 9 | Deaths | FLOOD DEATHS NEAR 1000 | persons (headline) | whole flood path | 14 Mar 1928 | Chicago Daily Tribune (AL2048), as listed in the Craft editor note | `Craft #31342 editor note` | contemporary | quoted from Craft; page not reread |
| 10 | Deaths | FINAL DEATH TOLL MAY SOAR TO 500 | persons (headline) | whole flood path | 15 Mar 1928 | Los Angeles Examiner (AL3037b) | `/scvhistory/al3037b.htm` | contemporary |  |
| 11 | Deaths | 296 bodies recovered, half of which have not been identified | bodies recovered | whole flood path | 15 Mar 1928 | New York Times | `/scvhistory/nyt031528b.htm` | contemporary |  |
| 12 | Deaths | Up to March 18, 273 bodies had been recovered | bodies recovered | whole flood path | 18 Mar 1928 (catalog note of unknown date) | Los Angeles Times negative sleeve, UCLA, quoted on LW2715 | `/scvhistory/lw2715.htm` | secondary | same sleeve: 'more than 600 residents plus an unknown number of itinerant farm workers' |
| 13 | Deaths | more than 600 residents plus an unknown number of itinerant farm workers | persons | whole flood path | unknown (library catalog note) | Los Angeles Times negative sleeve, UCLA, quoted on LW2715 | `/scvhistory/lw2715.htm` | secondary | Craft researchLeads: a catalog note, not a 1928 statement |
| 14 | Deaths | Sono stati raccolti più di 300 cadaveri ('More than 300 corpses have been collected') | bodies recovered | whole flood path | 25 Mar 1928 | La Domenica del Corriere (LW3695) | `/scvhistory/lw3695.htm` | contemporary | Italian; translation is the page's |
| 15 | Deaths | Property loss and damage, and personal injury and loss of life, are placed at the stupendous total of $12,000,000 and 451 lives | persons | whole flood path | 1928 | Guy L. Jones, report to Arizona Gov. George W.P. Hunt | `/scvhistory/guyljones1928.htm` | contemporary |  |
| 16 | Deaths | Total Number of Persons Killed 306 ... It does not include the unidentified bodies, of which there are ... 64 | persons (claims basis) plus unidentified bodies | whole flood path (Los Angeles and Ventura counties) | 15 Jul 1929 | Citizens' Restoration Committee, Report on Death and Disability Claims | `/scvhistory/stfrancis-claims071529.htm` | primary | 306 counts claims-based dead and a few with no heirs; plus 64 unidentified; 20 'Entire Families Wiped Out'; 67 'reported missing, but on whom no inquiries were received or claims filed' |
| 17 | Deaths | Three hundred and forty-eight (348) wrongful death claims were presented, which covered two hundred and ninety-four (294) deaths | deaths covered by claims | whole flood path | 15 Jul 1929 | Citizens' Restoration Committee | `/scvhistory/stfrancis-claims071529.htm` | primary |  |
| 18 | Deaths | one hundred and seventy-seven men, of whom eighty-four were killed | persons | Edison construction camp, county disputed ('located in the town of Piru' per this report; Kemp, at the county line, per NLF and Pollack) | 15 Jul 1929 | Citizens' Restoration Committee | `/scvhistory/stfrancis-claims071529.htm` | primary | location of the camp disputed: LA side or Ventura side |
| 19 | Deaths | Ed Locke 'perished that night, along with 84 of the workers' | persons | Edison camp at Kemp | 2008 | Alan Pollack, St. Francis Dam Disaster: Victims and Heroes | `/scvhistory/pollack0308victims.htm` | retrospective |  |
| 20 | Deaths | claims the lives of 64 of the 67 workmen and their family members who lived nearby | persons | Powerhouse No. 2 community (this valley) | 2014 | Alan Pollack, extended timeline | `/scvhistory/stfrancistimeline_pollack2014.htm` | retrospective | Craft treats as lead; Stansell 2014 puts 73 in PP2 community |
| 21 | Deaths | Six members of the Ruiz family are killed in the flood | persons | Ruiz family, San Francisquito Canyon (this valley) | 2014 | Alan Pollack, extended timeline; also Ruiz Cemetery place #2536 | `/scvhistory/stfrancistimeline_pollack2014.htm` | retrospective | conflicts with 'eight members of the prominent Ruiz family had been buried' (Worden 2003, sg051703) |
| 22 | Deaths | Seven direct members of the Ruiz family ... were killed by floodwaters | persons | Ruiz family, San Francisquito Canyon (this valley) | 9 Mar 2002 (caption date) | Leon Worden, LW2154b/c/e captions | `/scvhistory/lw2154e.htm` | retrospective | seven (incl. married daughter Rosarita Erratchuo) against six (Pollack 2014; Craft #2536) and eight buried (Worden 2003) |
| 23 | Deaths | at one time there were 78 bodies in the little shack that had been converted into a morgue | bodies at Newhall morgue (not a death toll) | Newhall morgue | 8 May 1928 | William S. Hart to Wyatt Earp, quoted LA Times 15 Feb 2003 | `/scvhistory/lat021503.htm` | primary | LW2715 'At least 78'; FR2801 'some 70 bodies' |
| 24 | Deaths | Seven members of the Saugus Community Club | persons | Saugus Community Club members | 2014 (plaque, date not stated) | LW2731 gallery | `/gif/galleries/lw2731/index.html` | retrospective |  |
| 25 | Deaths | more than 400 persons | persons | whole flood path | 22 May 1978 | The Newhall Signal (J. Timothy Fives) | `/scvhistory/sg19780522dam.htm` | retrospective | same page's caption: 'nearly 450 lives' |
| 26 | Deaths | At least 425 lives | persons | whole flood path | 21 May 1978 | SCV Historical Society plaque (HS7801) | `/gif/galleries/hs7801/index.html` | retrospective | per Craft editor note |
| 27 | Deaths | Over 450 lives | persons | whole flood path | 1978 | State description of CHL No. 919 (HS7801) | `/gif/galleries/hs7801/index.html` | retrospective | per Craft editor note |
| 28 | Deaths | the bodies of as many as 1,000 men, women and children | persons | whole flood path | not stated | Peggy Kelly, Reopening the Books | `/scvhistory/peggykelly_cause.htm` | secondary |  |
| 29 | Deaths | killing 450 persons | persons | whole flood path | c. 1934 (press caption on Bouquet Dam, undated) | Wide World Photos caption (AL2063a) | `/scvhistory/al2063a.htm` | contemporary |  |
| 30 | Deaths | more than 450 lives had been lost | persons | whole flood path | 9 Feb 2003 | Los Angeles Times | `/scvhistory/lat020903.htm` | secondary |  |
| 31 | Deaths | It is estimated that more than 450 people were killed | persons | whole flood path | Mar 2018 | County of Los Angeles proclamation | `/scvhistory/bos031318.htm` | secondary |  |
| 32 | Deaths | An estimated 500 people were killed | persons | whole flood path | 6 Aug 2014 | KHTS news | `/scvhistory/khts080614.htm` | secondary |  |
| 33 | Deaths | wiped out 500 lives | persons | whole flood path | 1960s-70s? (reissue inscription, Leon's estimate) | LW3184 photo inscription | `/scvhistory/lw3184.htm` | secondary |  |
| 34 | Deaths | 450 people lay dead | persons | whole flood path | 1980 | Del Castillo, The Del Valle Family and the Fantasy Heritage | `/scvhistory/delcastillo1980.htm` | secondary |  |
| 35 | Deaths | more than 432 people | persons | whole flood path | 2007 | J. David Rogers (per Craft editor note) | `/scvhistory/ro2007asce.htm` | retrospective |  |
| 36 | Deaths | between 450 and 600 | persons | whole flood path | 2008 | Alan Pollack (per Craft editor note) | `/scvhistory/pollack0308victims.htm` | retrospective |  |
| 37 | Deaths | some 431 lives / The over 400 people who perished | persons | whole flood path | 2016 | Alan Pollack | `/scvhistory/pollack0716stfrancis.htm` | retrospective |  |
| 38 | Deaths | at least 431 people were dead | persons | whole flood path | 12 Mar 2016 | Dianne Erskine-Hellrigel | `/scvhistory/deh031216.htm` | secondary |  |
| 39 | Deaths | 306 recovered bodies, of which 240 were identified; plus 125 missing persons ... a grand total of 431 individuals | persons | whole flood path | 12 Mar 2014 | Ann Stansell roster, Leon Worden note | `/scvhistory/annstansell_damvictims022214.htm` | retrospective | Stansell's 'General Area' column allows a valley count (Craft researchLeads) |
| 40 | Deaths | the number of victims stands at 411 | persons | whole flood path | 17 Jan 2018 | Leon Worden update on Stansell roster | `/scvhistory/annstansell_damvictims022214.htm` | retrospective | Craft's adopted figure |
| 41 | Deaths | more than 450 / more than 450 dead bodies / over 450 lives / An estimated 450 people / An estimated 470, 431, 411 people | persons | whole flood path | various (timeline; lwhist; Rippens 1998; Worden 2003; captions) | Craft #31342 editor note 'The number of dead in the sources' | `Craft #31342` | retrospective | bundled here: each is its own row in a final model |
| 42 | Injuries | Number of Persons Injured 66 | persons | whole flood path | 15 Jul 1929 | Citizens' Restoration Committee | `/scvhistory/stfrancis-claims071529.htm` | primary | 65 injury claims filed |
| 43 | Injuries | sixty-five (65) claims were filed for personal injuries | claims | whole flood path | 15 Jul 1929 | Citizens' Restoration Committee | `/scvhistory/stfrancis-claims071529.htm` | primary |  |
| 44 | Injuries | eighty-four were killed and thirty-three injured | persons | Edison construction camp | 15 Jul 1929 | Citizens' Restoration Committee | `/scvhistory/stfrancis-claims071529.htm` | primary | 'although all of the injured did not file claims' |
| 45 | Acres | Totally destroyed 1720 acres | acres of farm and orchard land | Newhall Land's ranch (Castaic Junction west to the county line; this valley) | 24 Mar 1928 | Newhall Land and Farming Co. report | `/scvhistory/nlf-stfrancis.htm` | primary | the only valley-scope acreage |
| 46 | Acres | the total acres of ranch lands injured is 10,658 | acres of ranch land | Ventura County (Horticultural Commission survey) | 1928 | Guy L. Jones report | `/scvhistory/guyljones1928.htm` | contemporary | also '190 properties have been damaged' |
| 47 | Acres | approximately 10,000 acres of orchards had been swept over by the flood | acres of orchard | Ventura County (A. H. Call, county horticultural commissioner) | 15 Mar 1928 | New York Times | `/scvhistory/nyt031528c.htm` | contemporary |  |
| 48 | Acres | Twenty lineal miles of citrus orchard land buried | lineal miles | scope not stated (flood path) | 15 Mar 1928 | New York Times | `/scvhistory/nyt031528c.htm` | contemporary | not acres |
| 49 | Structures destroyed | Torrent Razes 800 Houses | houses | whole flood path | 14 Mar 1928 | Oakland Tribune (AL2055) | `/scvhistory/al2055.htm` | contemporary |  |
| 50 | Structures destroyed | Five hundred homes destroyed or greatly damaged | homes (destroyed or damaged, combined) | scope not stated (flood path) | 15 Mar 1928 | New York Times | `/scvhistory/nyt031528c.htm` | contemporary | also 'Ten important bridges destroyed' |
| 51 | Structures destroyed | wiping out 7000 homes | homes | whole flood path | c. 1934 | Wide World Photos caption (AL2063a) | `/scvhistory/al2063a.htm` | contemporary | outlier |
| 52 | Structures destroyed | destroyed 1,200 homes, washed out ten bridges | homes | whole flood path | 2003 to 2018 | LA Times 1995, 2003 (lat091695, lat020903, lat021603); LA County proclamation 2018 (bos031318) | `/scvhistory/lat021603.htm` | secondary | four pages, one figure; likely one lineage |
| 53 | Structures destroyed | only 3 cottages used by some of the hands and their families were carried away | cottages | Newhall Land Orchard Ranch (this valley) | 24 Mar 1928 | Newhall Land report | `/scvhistory/nlf-stfrancis.htm` | primary | also at Castaic Junction 'not a vestige of any building whatsoever' but a platform; Powerhouse No. 2 'swept away' |
| 54 | Structures damaged | 190 properties have been damaged in varying degrees | properties | Ventura County | 1928 | Guy L. Jones report | `/scvhistory/guyljones1928.htm` | contemporary |  |
| 55 | Structures damaged | probably 200 homes had been damaged 'in this vicinity' | homes | Santa Paula | 15 Mar 1928 | New York Times (C. C. Teague) | `/scvhistory/nyt031528d.htm` | contemporary |  |
| 56 | Structures damaged | 75 houses in the lower portion of Santa Paula now stand deserted | houses | Santa Paula | 1928 | Guy L. Jones report | `/scvhistory/guyljones1928.htm` | contemporary |  |
| 57 | Cost | destroying an immense amount of property the value of which undoubtedly will exceed $25,000,000 | USD 1928, property | whole flood path | 24 Mar 1928 | Newhall Land report | `/scvhistory/nlf-stfrancis.htm` | primary |  |
| 58 | Cost | Damage Estimated at $7,000,000 to $30,000,000 | USD 1928 | whole flood path | 14 Mar 1928 | Oakland Tribune | `/scvhistory/al2055.htm` | contemporary |  |
| 59 | Cost | Property Loss Between $10,000,000 and $30,000,000 | USD 1928 | whole flood path | 14 Mar 1928 | Dallas Morning News; Omaha Morning Bee-News | `/scvhistory/dmn031428.htm` | contemporary |  |
| 60 | Cost | Possible Property Loss of $15,000,000 Feared | USD 1928 | whole flood path | 15 Mar 1928 | Los Angeles Examiner; NYT 'may reach $15,000,000' | `/scvhistory/al3037a.htm` | contemporary |  |
| 61 | Cost | I danni superano i 30 milioni di dollari | USD 1928 | whole flood path | 25 Mar 1928 | La Domenica del Corriere | `/scvhistory/lw3695.htm` | contemporary |  |
| 62 | Cost | $12,000,000 (property loss and damage, and personal injury and loss of life) | USD 1928 | whole flood path | 1928 | Guy L. Jones report | `/scvhistory/guyljones1928.htm` | contemporary | separately '$3,625,000 ... economic loss due to the dam's failure' to Los Angeles, and '$200,000' to rebuild Power Plant No. 2 |
| 63 | Cost | Total Amount of Death & Injury Claims $3,674,207.56; Amount of Settlement ... 915,751.74; unsettled 1,376,251.35 | USD 1928-29, death and injury claims only | whole flood path | 15 Jul 1929 | Citizens' Restoration Committee | `/scvhistory/stfrancis-claims071529.htm` | primary | checks: settled death $883,665.60 + settled injury $32,086.14 = $915,751.74; unsettled death $1,200,344.05 + injury $175,907.50 = $1,376,251.55 against a printed $1,376,251.35 |
| 64 | Cost | Fifty Thousand Dollars ... immediate relief; One Million Dollars ... Harbor Commissioners; another Million Dollars ... Water and Power Commissioners | USD 1928, relief appropriations | Los Angeles city spending (whole flood path) | 15 Jul 1929 | Citizens' Restoration Committee | `/scvhistory/stfrancis-claims071529.htm` | primary | appropriations, not loss |
| 65 | Cost | The city of Los Angeles paid $4.8 million in damage claims | USD, base year not stated | whole flood path | 16 Sep 1995 | Los Angeles Times | `/scvhistory/lat091695.htm` | secondary |  |
| 66 | Cost | damage valued at $13 million at the time | USD 1928 ('at the time') | whole flood path | 6 Aug 2014 | KHTS | `/scvhistory/khts080614.htm` | secondary |  |
| 67 | Cost | property loss of about $20,000,000 | USD, base not stated | whole flood path | 1960s-70s? (reissue) | LW3184 inscription | `/scvhistory/lw3184.htm` | secondary |  |
| 68 | Cost | Damage to the land and structures approached 20 million dollars | USD, base not stated | whole flood path | 1980 | Del Castillo | `/scvhistory/delcastillo1980.htm` | secondary | Rancho Camulos alone 'well over 300,000 dollars' (Ventura County) |
| 69 | Displaced or evacuated | Fifteen hundred persons virtually homeless | persons | scope not stated (flood path; dispatch from Santa Paula area) | 15 Mar 1928 | New York Times | `/scvhistory/nyt031528c.htm` | contemporary | the only displacement figure |

**Cause and consequence the sources make**

- cause: failure of the dam (structural; coroner's jury verdict 12 Apr 1928, /scvhistory/sfdcoronersverdict.htm, places responsibility on the Bureau and Chief Engineer)
- consequence: the flood itself (the dam failure and the flood are one event in Craft; DATA-MODEL names 'the dam and its flood' as a cause-consequence pair)
- consequence: Citizens' Restoration Committee and claims settlement (1928-29)
- consequence: Bouquet Canyon Dam built as replacement storage, 1930 bond (LW3573, AL2063a)
- consequence: CHL No. 919 (1978); National Memorial and Monument (S.47, 2019)
- later: Copper Fire 2002 burned the Ruiz Cemetery where dam victims are buried (chronology)

**Disagreements and traps**

- Every headline toll is whole-path; the valley's own dead are only countable from Stansell's 'General Area' column (Craft researchLeads: PP2 73, San Francisquito Canyon 36, Newhall Ranch Land 24, Castaic Junction 9+17+2, Newhall 5, Saugus 2; Edison-Kemp 85 is on the county line).
- The Edison camp is placed 'in the town of Piru' by the 1929 claims report, west of the line by NLF, east of it by Pollack 2014: its 84 dead are the single largest block and their county is disputed.
- Structures: 500 (destroyed or damaged), 800, 1,200 and 7,000 homes, none with a stated scope; the 1,200 lineage (LA Times 1995, 2003; county 2018) has no 1928 source found in the mirror.

## Great Flood of 1938

- Status: Craft event #31904
- Date: 27 Feb to 4 Mar 1938; worst 1 to 2 Mar 1938
- Kind: flood (storm)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | 5,601 buildings had been destroyed and 113 to 115 Southland residents were killed | persons | Southland (Southern California) | 2013 | Leon Worden, 'About the Great Flood of 1938' (note under JN3801, AP3314, AP3101, DO3801, HB3802) | `/scvhistory/jn3801.htm` | retrospective | none in this valley (Craft editor note) |
| 2 | Deaths | killed at least 113 people in the greater Los Angeles area | persons | greater Los Angeles area | not stated | Leon Worden, LW3677 | `/scvhistory/lw3677.htm` | retrospective |  |
| 3 | Deaths | killed 113 to 115 people in the greater Los Angeles area | persons | greater Los Angeles area | not stated | Leon Worden, LW3223 | `/scvhistory/lw3223.htm` | retrospective |  |
| 4 | Deaths | Thirty Dead in Southland Floods | persons (headline) | Southland | 3 Mar 1938 | Los Angeles Times, as quoted by Pollack 2019 | `/scvhistory/pollack0719greatflood.htm` | contemporary | quoted second-hand |
| 5 | Deaths | By the next day, the death toll had risen to 62 | persons | Southland | 4 Mar 1938 | Los Angeles Times, as retold by Pollack 2019 | `/scvhistory/pollack0719greatflood.htm` | contemporary | retold second-hand |
| 6 | Deaths | Approximately 115 people perished in the floods of 1938 in Los Angeles | persons | Los Angeles | 2019 | Alan Pollack | `/scvhistory/pollack0719greatflood.htm` | retrospective |  |
| 7 | Deaths | None of these sources reports a death in the Santa Clarita Valley, and none was found on the mirror | (absence) | this valley | Oct 2026 | Craft #31904 editor note | `Craft #31904` | retrospective | the local figure is zero-known, not zero-sourced |
| 8 | Injuries | the engine of a work train ... was derailed and two men were injured slightly | persons | near Saugus (this valley); a consequence, 25 Mar 1938 | 26 Mar 1938 | Los Angeles Times, on LW3067 | `/scvhistory/lw3067.htm` | contemporary | Leon: 'Collateral damage from the Great Flood' |
| 9 | Structures destroyed | 5,601 buildings had been destroyed | buildings | Southland | 2013 | Leon Worden note (JN3801 etc.) | `/scvhistory/jn3801.htm` | retrospective |  |
| 10 | Structures destroyed | 5,600 homes and businesses were lost in the floods | homes and businesses | scope not stated (Los Angeles floods) | 2019 | Alan Pollack | `/scvhistory/pollack0719greatflood.htm` | retrospective |  |
| 11 | Structures destroyed | Fifteen hundred homes were declared uninhabitable | homes | scope not stated (Los Angeles region, from the Times) | Mar 1938 retold 2019 | Alan Pollack | `/scvhistory/pollack0719greatflood.htm` | contemporary | uninhabitable, not necessarily destroyed |
| 12 | Structures destroyed | The school at Honby lost all of its outbuildings; Most of the buildings at the Nadeau Deer Farm ... were destroyed; The home of the James Fryer family was washed away | named buildings (no count) | this valley | 2019 (from LA Times and Newhall Signal, Mar 1938) | Alan Pollack | `/scvhistory/pollack0719greatflood.htm` | retrospective | the only valley structures; no number |
| 13 | Structures destroyed | 200 feet of the bridge west of Castaic; half the Newhall Saugus highway | bridge length; highway | this valley | Apr 1938 | California Highways and Public Works (LW3356) | `/scvhistory/lw3356.htm` | primary | infrastructure, not a count |
| 14 | Cost | Damage was estimated at $78 million | USD 1938 | Los Angeles (scope as Pollack puts it) | 2019 | Alan Pollack | `/scvhistory/pollack0719greatflood.htm` | retrospective |  |
| 15 | Cost | damaged State highways and structures to the extent of $8,000,000 | USD 1937-38, state highways | state (Dec 11 to Mar 4 storms; north and south) | Apr 1938 | California Highways and Public Works | `/scvhistory/lw3356.htm` | primary |  |
| 16 | Cost | The total flood damage over the state during the winter and spring is estimated to be more than $60,000,000 | USD 1938 | state (winter and spring 1937-38) | Aug 1938 | F.W. Panhorst, Civil Engineering | `/scvhistory/panhorst0838.htm` | primary | also highways ~$8,000,000, public service companies ~$16,000,000 |
| 17 | Displaced or evacuated | Fifteen hundred homes were declared uninhabitable, resulting in 3,700 storm refugees | persons | scope not stated (Los Angeles region) | Mar 1938 retold 2019 | Alan Pollack (from LA Times) | `/scvhistory/pollack0719greatflood.htm` | contemporary |  |
| 18 | Displaced or evacuated | Thousands of families had to be evacuated from their flooded homes in the San Fernando Valley, Compton and Venice | families | San Fernando Valley, Compton, Venice | Mar 1938 retold 2019 | Alan Pollack | `/scvhistory/pollack0719greatflood.htm` | contemporary | not this valley |

**Cause and consequence the sources make**

- cause: the storm of 27 Feb to 4 Mar 1938 (weather; not a separate event)
- consequence: Southern Pacific work-train derailment near Saugus, 25 Mar 1938 (LW3067), 'collateral damage'
- consequence: Hill's rodeo grounds collapse; property lost to the bank; Bonelli's Saugus Speedway from 1939 (JN3801, LW3677; Pollack)
- consequence: Flood Control Act of 1941 (regional); Army Corps' 1968 Santa Clara River channel plan, never built (sg19680812armycorps)
- consequence: Placerita Creek moved south of Hickson's movie town (LW3577)

**Disagreements and traps**

- The standard trap: every figure (113 to 115 dead, 5,601 buildings, $78 million) is regional; Craft says none died in this valley, and the valley's losses are named buildings and bridges with no count.
- Deaths ran 30 (3 Mar), 62 (4 Mar), at least 113, 113 to 115, about 115: a toll that grew in the record; the 1938 final count is not on the mirror.
- Cost: $8,000,000 (state highways, three storms), over $60,000,000 (state, winter and spring), $78 million (Los Angeles, 2019): three scopes, none local. Panhorst's '$14,600,000' preliminary estimate on the same page is the northern (San Joaquin and Kings) flood of December 1937, not this one: a trap for anyone quoting the page.

## Sylmar (San Fernando) Earthquake

- Status: Craft event #31893
- Date: 9 Feb 1971
- Kind: earthquake


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | killed 65 people (primarily in the San Fernando Valley) | persons | region (San Fernando and Santa Clarita valleys) | not stated (caption note) | Leon Worden, note to LW2316c and LW2316 | `/scvhistory/lw2316c.htm` | retrospective |  |
| 2 | Deaths | killed 65 people and caused $500 million in property damage. Most deaths and injuries occurred in the San Fernando Valley | persons | region | 2012 | Leon Worden, Volunteers Pave the Way to Henry Mayo Hospital | `/scvhistory/hmnmhprehistory.htm` | retrospective |  |
| 3 | Deaths | claimed 65 lives | persons | region | not stated (unsigned page) | SCVHistory.com, USDA film page | `/scvhistory/sylmarquake1971_usda.htm` | secondary |  |
| 4 | Deaths | At least one confirmed fatality occurred in the Santa Clarita Valley | persons | this valley | not stated (unsigned page) | SCVHistory.com, USDA film page | `/scvhistory/sylmarquake1971_usda.htm` | secondary | unconfirmed: no mirror page names the person or place (Craft researchLeads) |
| 5 | Deaths | the loss of 49 lives in the collapse of the Veterans Administration Hospital in San Fernando | persons | San Fernando (VA hospital) | 2011 | Alan Pollack, 1857: The Big One Rocks Ft. Tejon | `/scvhistory/pollack0111tejon.htm` | retrospective | outside the valley |
| 6 | Deaths | Miraculously, no one was killed | persons | 5-14 interchange collapse, Newhall Pass (this valley) | 9 Feb 1971 | UPI cutline on LW2794 | `/scvhistory/lw2794.htm` | contemporary |  |
| 7 | Deaths | In total, 65 people were killed in the 1971 quake | persons | region | 2011 | Alan Pollack, 1857: The Big One Rocks Ft. Tejon | `/scvhistory/pollack0111tejon.htm` | retrospective |  |
| 8 | Injuries | injured thousands | persons | region | not stated | SCVHistory.com, USDA film page | `/scvhistory/sylmarquake1971_usda.htm` | secondary | the only injury figure, and not a number |
| 9 | Structures damaged | $5.3 million in local damage to 1,540 of the valley's 15,000 permanent buildings | buildings | this valley | 28 Aug 2003 | John Boston, Shake, Rattle & Roll (The Signal) | `/scvhistory/sg082803.htm` | retrospective | no source named; the one valley-scope damage count for any earthquake |
| 10 | Structures damaged | About 70 percent of the SCV's 2,200 mobile homes were damaged | mobile homes (percentage of) | this valley | 28 Aug 2003 | John Boston | `/scvhistory/sg082803.htm` | retrospective |  |
| 11 | Structures damaged | In all, 67 bridges on five major freeways were damaged | bridges | region | 3 Feb 1976 | Houston Chronicle cutline on LW3160 | `/scvhistory/lw3160.htm` | contemporary |  |
| 12 | Structures destroyed | the collapse of the fairly new freeway bridges in the Newhall Pass | bridges (no count) | Newhall Pass (this valley) | not stated | Leon Worden, LW2316c | `/scvhistory/lw2316c.htm` | retrospective |  |
| 13 | Structures destroyed | The red brick hotel building was destroyed ... when the second floor collapsed | one building | Acton | not stated | AP3113 caption | `/scvhistory/ap3113.htm` | retrospective | with the Swall Hotel/Newhall Pharmacy and First Presbyterian Church (Craft body): named buildings, no count |
| 14 | Cost | caused more than half a billion dollars in damage to both valleys | USD 1971 | San Fernando and Santa Clarita valleys | not stated | Leon Worden, LW2316c | `/scvhistory/lw2316c.htm` | retrospective |  |
| 15 | Cost | caused $500 million in property damage | USD 1971 | scope not stated (region) | 2012 | Leon Worden, hmnmhprehistory | `/scvhistory/hmnmhprehistory.htm` | retrospective |  |
| 16 | Cost | the quake caused $1 billion in Southern California damage | USD, base not stated | Southern California | 28 Aug 2003 | John Boston | `/scvhistory/sg082803.htm` | retrospective | doubles Worden's figure |
| 17 | Cost | $5.3 million in local damage | USD 1971 presumably | this valley | 28 Aug 2003 | John Boston | `/scvhistory/sg082803.htm` | retrospective | the only valley-scope cost for 1971 |
| 18 | Cost | Thatcher Glass. About a $3 million cleanup bill | USD | one firm, Saugus (this valley) | 28 Aug 2003 | John Boston | `/scvhistory/sg082803.htm` | retrospective | larger than half the valley total; inconsistent or a different measure |
| 19 | Displaced or evacuated | the evacuation of tens of thousands of residents living downstream | persons | San Fernando Valley below Van Norman Dam | not stated | SCVHistory.com, USDA film page | `/scvhistory/sylmarquake1971_usda.htm` | secondary | regional; not this valley |
| 20 | Displaced or evacuated | Several hundred residents put their homes up for sale | persons (left the valley, not evacuated) | this valley | 28 Aug 2003 | John Boston | `/scvhistory/sg082803.htm` | retrospective | flight after the quake; a different measure |

**Cause and consequence the sources make**

- consequence: collapse of the Newhall Pass interchange (I-210/I-5; 5-14 connectors) (Craft place #934). DATA-MODEL names 'Sylmar and the Newhall Pass' as a pair
- consequence: Newhall Pharmacy (ex-Swall Hotel) front collapsed 13 Mar 1971 (timeline)
- consequence: First Presbyterian Church rebuilt 1976-77 (HS8601)
- consequence: College of the Canyons and Henry Mayo Newhall Memorial Hospital plans strengthened (aa7401; hmnmhprehistory)
- consequence: Van Norman Dam crisis and evacuation (regional)
- consequence: Joint Committee on Seismic Safety special subcommittee, Jim Keysor; 1972 report

**Disagreements and traps**

- Cost: $500 million / more than half a billion (Worden, 'both valleys') against $1 billion (Boston, 'Southern California'): two scopes and a factor of two.
- The one-death-in-the-valley claim is unsigned and unconfirmed; the 65 are regional and are the figure that will be quoted.
- Boston 2003 gives the only valley-scope figures (1,540 buildings, 70 percent of 2,200 mobile homes, $5.3 million) with no source; his Thatcher Glass '$3 million' would be over half the valley total. Needs the 1971 Signal.

## Northridge Earthquake

- Status: Craft event #875
- Date: 17 Jan 1994
- Kind: earthquake


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | causing 57 deaths and over $20 billion in damage across the region | persons | region | not stated | Craft #875 eventSignificance (unsourced) | `Craft #875` | secondary | no footnote; origin unknown |
| 2 | Deaths | Sixty people died and more than 9,000 were injured, while property damage was estimated between $13 and $50 billion | persons | scope not stated (region) | not stated | Craft #875 withheldBody (unsourced, withheld from display) | `Craft #875` | secondary | withheld text; no source |
| 3 | Deaths | kills 53 and causes $11 billion in damage across Southern California | persons | Southern California | not stated | Leon Worden, SCV Chronology, 1994 | `/scvhistory/timeline.htm` | retrospective |  |
| 4 | Deaths | Sixty people were killed, more than 7,000 injured, 20,000 homeless and more than 40,000 buildings damaged in Los Angeles, Ventura, Orange and San Bernardino Counties | persons | four counties | 1996 (USGS fact sheet, quoted on every LW94xx page) | USGS, Response to an Urban Earthquake: Northridge '94 | `/scvhistory/lw3049.htm` | primary | repeated in the navigation box of 29 pages |
| 5 | Deaths | LAPD Motor Officer Clarence Wayne Dean, 46, was killed | persons | Newhall Pass (this valley) | not stated | Leon Worden, LW3049 | `/scvhistory/lw3049.htm` | retrospective | the one valley death in the sources; withheldBody also names him |
| 6 | Injuries | more than 7,000 injured | persons | four counties | 1996 | USGS | `/scvhistory/lw3049.htm` | primary |  |
| 7 | Injuries | more than 9,000 were injured | persons | scope not stated | not stated | Craft #875 withheldBody | `Craft #875` | secondary | unsourced |
| 8 | Structures damaged | more than 40,000 buildings damaged | buildings | four counties | 1996 | USGS | `/scvhistory/lw3049.htm` | primary | also 'freeways collapsed at seven sites, and 170 bridges sustained varying degrees of damage' |
| 9 | Structures damaged | At least ten commercial buildings are in bad shape ... 63 units apartment building in Newhall has been claimed unsafe and is posted | buildings; units | City of Santa Clarita | 18 Jan 1994 | City of Santa Clarita, Emergency Services meeting notes (SC9406) | `/scvhistory/files/sc9406/sc9406.pdf` | primary | day-after; 'Mobilehome parks have major damages' |
| 10 | Structures damaged | Haz-Mat team completes 950 inspections to homes and businesses | inspections (not damage) | City of Santa Clarita | Jan-Feb 1994 | City of Santa Clarita, Chronology of Events (SC9407) | `/scvhistory/files/sc9407/sc9407.pdf` | primary | also 'City completing 800 building inspections per day' (21 Jan) |
| 11 | Structures destroyed | Several homes in SCV are severely damaged and/or destroyed | homes (no count) | this valley | not stated | Leon Worden, SCV Chronology | `/scvhistory/timeline.htm` | retrospective |  |
| 12 | Cost | TOTAL: $430,796.227.35 (page title 'Total $431 Million') | USD 1994 | Santa Clarita Valley (city plus unincorporated) | Dec 1994 | City of Santa Clarita, Earthquake Damage/Costs Assessment Estimates (SC9405) | `/scvhistory/files/sc9405/sc9405.pdf` | primary | the only valley-wide cost for any event; private $200.231,884 in the PDF against $200,213,884 on the HTML page (transposed digits) |
| 13 | Cost | Caltrans $71,800,000; Southern California Edison $30,000,000; CalArts $34,400,000; Hospitals $16,531,500; unincorporated subtotal $28.662,000 | USD 1994 | Santa Clarita Valley, by line | Dec 1994 | City of Santa Clarita (SC9405) | `/scvhistory/files/sc9405/sc9405.pdf` | primary | line items; not separate rows in a final model |
| 14 | Cost | the Northridge earthquake did $30 million worth of damage to the hospital | USD, base not stated | Henry Mayo Newhall Memorial Hospital | 1998 (caption) | HM9801 caption | `/scvhistory/hm9801.htm` | secondary | against 'Hospitals $16,531,500' in the city's Dec 1994 estimate; 'Initial cost estimates were $20 million' |
| 15 | Cost | City Hall ... repair costs in excess of $4.5 million | USD | City Hall | not stated | Craft #875 withheldBody | `Craft #875` | secondary | unsourced |
| 16 | Cost | caused billions of dollars of damage throughout Los Angeles and Santa Clarita | USD (no figure) | Los Angeles and Santa Clarita | 2011 | Alan Pollack | `/scvhistory/pollack0111tejon.htm` | retrospective | also: '16 people were killed' at Northridge Meadows Apartments |
| 17 | Cost | Losses were estimated at $20 billion | USD 1994 | four counties | 1996 | USGS | `/scvhistory/lw3049.htm` | primary |  |
| 18 | Cost | causes $11 billion in damage across Southern California | USD 1994 | Southern California | not stated | SCV Chronology | `/scvhistory/timeline.htm` | retrospective |  |
| 19 | Cost | property damage was estimated between $13 and $50 billion | USD | scope not stated | not stated | Craft #875 withheldBody | `Craft #875` | secondary | unsourced |
| 20 | Cost | over $20 billion in damage across the region | USD | region | not stated | Craft #875 eventSignificance | `Craft #875` | secondary | unsourced |
| 21 | Displaced or evacuated | 20,000 homeless | persons | four counties | 1996 | USGS | `/scvhistory/lw3049.htm` | primary |  |
| 22 | Displaced or evacuated | Saugus had 100 people, Newhall 150, Canyon 240, 150 Sierra Vista Jr. High | persons in shelters | Santa Clarita shelters | 18 Jan 1994 | City of Santa Clarita (SC9406) | `/scvhistory/files/sc9406/sc9406.pdf` | primary | one night; not a total |
| 23 | Displaced or evacuated | Santa Clarita Transit evacuates Greenbriar Mobilehomes; Newhall Park becomes campground for displaced families | (no count) | Canyon Country; Newhall | 17 Jan 1994 | City of Santa Clarita (SC9407) | `/scvhistory/files/sc9407/sc9407.pdf` | primary |  |

**Cause and consequence the sources make**

- consequence: second collapse of the Newhall Pass interchange (SR-14/I-5), killing Officer Clarence Wayne Dean; renamed for him (LW3049). The 1971 collapse at the same place is the 'Sylmar and the Newhall Pass' pair
- consequence: Santa Clarita cut off; Metrolink and Sierra Highway as the only routes (SC9406, SC9407)
- consequence: City Hall red-tagged; tent City Hall (LW9401)
- consequence: Greenbrier mobile home fires from ruptured gas lines (LW9404): a fire caused by the earthquake
- consequence: Henry Mayo hospital reconstruction (HM9801); Rancho Camulos winery red-tagged (Ventura County)
- consequence: first Cowboy Festival moved to Melody Ranch (Craft body); George Pederson 'earthquake mayor'
- consequence (claimed, Signal 80-year timeline): the city's $1.1 billion redevelopment proposal 'Using earthquake damage as its impetus' (/scvhistory/sg19191999.htm)

**Disagreements and traps**

- Deaths: 53 (Worden's chronology), 57 (Craft significance), 60 (USGS; withheld body): three regional figures on one record, two of them unsourced in Craft.
- Cost: $11 billion (chronology), $13 to $50 billion (withheld body), $20 billion (USGS), 'over $20 billion' (Craft significance), all regional; against $430.8 million for the valley (city, Dec 1994), the only full valley cost estimate in the archive for any event.
- Henry Mayo: $16.5 million (city, Dec 1994, 'Hospitals'), $20 million (initial), $30 million (1998 caption).
- Craft's eventSignificance and withheldBody carry figures with no footnote and disagree with the footnoted sources; both should be treated as unsourced.

## Powerhouse Fire

- Status: Craft event #31891
- Date: 30 May to 11 Jun 2013
- Kind: fire (wildfire)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | No source in the archive reports a death in the fire | (absence) | whole fire | Oct 2026 | Craft #31891 editor note | `Craft #31891` | retrospective | a sourced zero would need an agency report |
| 2 | Injuries | In all, nine firefighters were injured | persons | whole fire | 2013 | Leon Worden, LW2382a | `/scvhistory/lw2382a.htm` | retrospective |  |
| 3 | Acres | By that evening the fire had consumed 1,000 acres | acres (first evening) | whole fire | 30 May 2013 | Leon Worden, LW2382a | `/scvhistory/lw2382a.htm` | retrospective | a progress figure |
| 4 | Acres | The image was taken when the fire had burned approximately 5,000 acres | acres (night of 31 May to 1 Jun) | whole fire | 2013 | NASA/Ames caption, LW2400 | `/scvhistory/lw2400.htm` | contemporary | a progress figure |
| 5 | Acres | the perimeter, which was estimated at 32,032 acres | acres (estimate, days 4-5) | whole fire | 3-4 Jun 2013 | LW2397g caption | `/scvhistory/lw2397g.htm` | contemporary | larger than the final figure |
| 6 | Acres | 30,274 acres (47.3 square miles) of brush were blackened | acres | whole fire (San Francisquito Canyon to Antelope Acres; mostly outside the Santa Clara River valley) | 2013 | Leon Worden, LW2382a | `/scvhistory/lw2382a.htm` | retrospective | Craft's adopted figure |
| 7 | Acres | The wildfire eventually blackened more than 30,000 acres | acres | whole fire | 2013 | NASA/Ames caption, LW2400 | `/scvhistory/lw2400.htm` | contemporary |  |
| 8 | Acres | the fire burned over 30,000 acres | acres | whole fire (Los Angeles County) | 12 Jul 2013 | Gov. Edmund G. Brown Jr., Proclamation of a State of Emergency | `/scvhistory/brown_powerhouseproclamation.htm` | primary |  |
| 9 | Structures destroyed | 30 homes were declared total losses, and another 28 outbuildings were destroyed | homes; outbuildings | whole fire (Lake Hughes and Elizabeth Lake chiefly) | 2013 | Leon Worden, LW2382a; SCV Chronology 2013 | `/scvhistory/lw2382a.htm` | retrospective |  |
| 10 | Structures destroyed | four or five single-family homes burned completely to the ground on the evening of Saturday, June 1 | homes | Lake Hughes, Newview Drive | 2013 | Leon Worden, LW2382a | `/scvhistory/lw2382a.htm` | retrospective | a part of the 30 |
| 11 | Structures destroyed | destroyed at least 24 homes | homes | whole fire | 2013 | NASA/Ames caption, LW2400 | `/scvhistory/lw2400.htm` | contemporary | against Worden's 30 |
| 12 | Structures damaged | destroyed or damaged at least 29 other structures | structures (destroyed or damaged combined) | whole fire | 2013 | NASA/Ames caption, LW2400 | `/scvhistory/lw2400.htm` | contemporary |  |
| 13 | Structures damaged | threatened hundreds of homes and other structures | homes threatened (not damaged) | whole fire | 12 Jul 2013 | Governor's proclamation | `/scvhistory/brown_powerhouseproclamation.htm` | primary | the governor's press release on the same page: 'threatened thousands of homes' |
| 14 | Cost | the cost of fighting the fire, preliminarily pegged at $23.4 million | USD 2013, suppression cost | whole fire | 2013 | Leon Worden, LW2382a | `/scvhistory/lw2382a.htm` | retrospective | FEMA 'agreed to pay up to 75 percent'; suppression, not property loss. No property-loss figure in the archive |
| 15 | Displaced or evacuated | Some 250 people were evacuated from the community of Green Valley | persons | Green Valley, San Francisquito Canyon (this valley) | 2 Jun 2013 | LW2393a caption | `/scvhistory/lw2393a.htm` | contemporary |  |
| 16 | Displaced or evacuated | residents were evacuated Friday night and Saturday, June 1, from Lake Hughes, Lake Elizabeth, Green Valley and Antelope Acres | (no count) | lake communities and Antelope Acres | Jun 2013 | LW2397g caption | `/scvhistory/lw2397g.htm` | contemporary |  |

**Cause and consequence the sources make**

- cause: not stated in any mirror page (Craft researchLeads); origin above LADWP Power House No. 1
- consequence: Governor's state of emergency (12 Jul 2013); FEMA suppression grant
- consequence: The Painted Turtle camp's 2013 season cancelled; boil-water advisory
- place link: Power House No. 1 and the former St. Francis reservoir (shared geography, not cause)

**Disagreements and traps**

- Homes: 30 total losses (Worden, chronology) against 'at least 24' (NASA); structures 28 outbuildings against 'at least 29 other structures destroyed or damaged'.
- Acres: 30,274 final against a 32,032-acre perimeter estimate mid-fire; both whole-fire. Most acreage and every lost home was in the lake communities and the Antelope Valley edge, not the Santa Clara River valley: a scope trap of the opposite kind, a fire named for a valley landmark whose losses were mostly outside it.
- Cost is suppression cost ($23.4 million preliminary), not damage: not comparable with the other events' damage figures.

## United Air Lines Flight 34 Crash in Rice Canyon

- Status: Craft event #31907
- Date: 27 Dec 1936
- Kind: air crash


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | with 13 persons aboard | persons aboard (while missing) | the aircraft | 28 Dec 1936 | Battle Creek Enquirer (LW2897) | `/scvhistory/lw2897.htm` | contemporary | same page's UP dispatch: '12 persons aboard' |
| 2 | Deaths | killing the 12 persons on board | persons | the aircraft (crash site in this valley) | Jan 1937 | Battle Creek Enquirer, funeral report (LW2897) | `/scvhistory/lw2897.htm` | contemporary |  |
| 3 | Deaths | killed 12 persons near Saugus | persons | the aircraft | 5 Jan 1937 | ACME cutline (LW2826a) | `/scvhistory/lw2826a.htm` | contemporary |  |
| 4 | Deaths | Accident Which Took Twelve Lives | persons | the aircraft | 6 Jan 1937 | United Press, Modesto Bee | `/scvhistory/lp_modestobee010637.htm` | contemporary |  |
| 5 | Deaths | the accident which killed 12 people | persons | the aircraft | 31 Dec 1936 | ACME cutline (LW2448a) | `/scvhistory/lw2448a.htm` | contemporary |  |
| 6 | Deaths | All 12 persons on board were killed | persons | the aircraft | 1937 (from the Bureau of Air Commerce report, as quoted by the unsigned page) | SCVHistory.com, 'Plane Crash in Rice Canyon Kills All 12' | `/scvhistory/ntsb122736.htm` | secondary | the report itself is an image-only PDF (ntsb122736.pdf), unread |
| 7 | Deaths | kills all 12 aboard (3 crew, 9 passengers) | persons | the aircraft | not stated | SCV Chronology, 1936 | `/scvhistory/timeline.htm` | retrospective |  |
| 8 | Deaths | All 12 persons on board the airplane died ... Pair of commercial airliners crash within three weeks, leaving 17 dead | persons | the aircraft; 17 = this crash plus Flight 7 | 2012 | Alan Pollack, Death from the Sky Over Newhall | `/scvhistory/pollack0312planes.htm` | retrospective | 17 implies 5 for Flight 7 |

**Cause and consequence the sources make**

- cause (Accident Board): 'an error on the part of the pilot for attempting to fly through the Newhall pass at an altitude lower than the surrounding mountains without first determining by radio the existing weather' (ntsb122736.htm)
- paired: Western Air Express Flight 7, 12 Jan 1937 (Pollack 2012) - a pairing, not cause and consequence
- suggestion, not a finding: uranium deposits affecting radios (AP, 18 Jan 1937, lp_sanberdocountysun011937)

**Disagreements and traps**

- The one disagreement is the first-day '13 persons aboard'; every later source says 12. Injuries, acres, structures, cost and displacement do not apply or are not recorded: an air crash is comparable with the other events on deaths only.

## Western Air Express Flight 7 Crash

- Status: Craft event #31914
- Date: 12 Jan 1937
- Kind: air crash


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | the crash of an air transport plane which killed one other person and injured 11 | persons (Johnson plus one) | the aircraft (crash site on the Santa Clara Divide above Placerita Canyon) | 13 Jan 1937 | Associated Press (LW2247) | `/scvhistory/lw2247.htm` | contemporary | headline 'Another Killed And 11 Injured In Air Tragedy' |
| 2 | Deaths | killing one man outright and injuring 12 other persons | persons | the aircraft | 13 Jan 1937 | ACME cutline (per Craft editor note) | `Craft #31914` | contemporary |  |
| 3 | Deaths | with the loss of two lives and the serious injuries of five others | persons | the aircraft | 14 Jan 1937 | ACME cutline (per Craft editor note) | `Craft #31914` | contemporary |  |
| 4 | Deaths | Another Airplane Crash Kills 2. Plane With 13 on Board Hits Iron Mountain. | persons (headline) | the aircraft | 14 Jan 1937 | The Newhall Signal and Saugus Enterprise | `/scvhistory/sg011437.htm` | contemporary |  |
| 5 | Deaths | 5 Killed in Plane Crash Including Adventurer Martin Johnson | persons (SCVHistory.com's page heading, not the Signal's) | the aircraft | not stated (webmaster) | SCVHistory.com heading on its copy of the Signal page | `/scvhistory/sg011437.htm` | secondary | the archive's heading contradicts the newspaper it presents |
| 6 | Deaths | Earl E. Spencer ... the fourth victim of the Western Air Express crash | persons (running count) | the aircraft | 18 Jan 1937 | United Press, San Bernardino County Sun | `/scvhistory/lp_sanberdocountysun011937.htm` | contemporary |  |
| 7 | Deaths | may have been responsible for two plane crashes and the loss of 15 lives within a month | persons (both crashes; 12 + 3 at that date) | Flight 34 and Flight 7 together | 18 Jan 1937 | Associated Press, San Bernardino County Sun | `/scvhistory/lp_sanberdocountysun011937.htm` | contemporary | combined figure |
| 8 | Deaths | the accident, which has taken five lives to date | persons | the aircraft | 21 Jan 1937 | Associated Press, Fresno Bee | `/scvhistory/sg011437.htm` | contemporary |  |
| 9 | Deaths | Total fatalities: 5 | persons | the aircraft | 12 May 1937 (report adopted); database summary | Accident Board, Bureau of Air Commerce, via Aircraft Crashes Record Office | `/scvhistory/nc13315.htm` | secondary | a private database's summary of the primary report |
| 10 | Deaths | 2 dead, 11 injured | persons | the aircraft | not stated | SCV Chronology, 1937 | `/scvhistory/timeline.htm` | retrospective | the first-day count, kept in the chronology |
| 11 | Deaths | took the lives of one crew member (co-pilot Owens) and four passengers / leaving 17 dead (both crashes) | persons | the aircraft | 2012 | Alan Pollack | `/scvhistory/pollack0312planes.htm` | retrospective |  |
| 12 | Deaths | A total of five people died; only one was killed on impact | persons | the aircraft | not stated | Leon Worden caption, LW2247 | `/scvhistory/lw2247.htm` | retrospective |  |
| 13 | Injuries | injured 11 | persons | the aircraft | 13 Jan 1937 | Associated Press (LW2247) | `/scvhistory/lw2247.htm` | contemporary |  |
| 14 | Injuries | injuring 12 other persons | persons | the aircraft | 13 Jan 1937 | ACME cutline (per Craft) | `Craft #31914` | contemporary |  |
| 15 | Injuries | the serious injuries of five others | persons (serious) | the aircraft | 14 Jan 1937 | ACME cutline (per Craft) | `Craft #31914` | contemporary |  |
| 16 | Injuries | 11 injured | persons | the aircraft | not stated | SCV Chronology | `/scvhistory/timeline.htm` | retrospective |  |
| 17 | Cost | Osa Johnson filed a $502,539 lawsuit | USD 1937, damages claimed (lost on appeal 1941) | one claimant | not stated (caption) | DS3701 caption | `/scvhistory/ds3701.htm` | retrospective | a claim, not a loss |

**Cause and consequence the sources make**

- cause (Accident Board): 'error on the part of the pilot for descending to a dangerously low altitude without positive knowledge of his position' (nc13315.htm)
- paired: United Air Lines Flight 34, three weeks earlier (Pollack 2012; AP 18 Jan 1937 '15 lives within a month')
- consequence: Osa Johnson's suit against Western Air Express and United Airports Co., lost on appeal 30 Jun 1941 (DS3701)

**Disagreements and traps**

- Deaths 1, 2, 4 (running), 5; injuries 5 (serious), 11, 12. The deaths grew from 2 to 5 over a week as the injured died; the injuries fell correspondingly. The chronology keeps the first-day '2 dead, 11 injured'; SCVHistory.com's own heading says 5 over a Signal page that says 2.
- A measure must say when it was counted (dead at the scene, dead within a week): DATA-MODEL's 'as of' is essential here.

## Placerita Canyon (Melody Ranch) and Hasley Canyon fires, 1962

- Status: Not an event (A-ranked in events-missed-2026-10-05); Craft photograph 4779
- Date: 28 to 30 Aug 1962
- Kind: fire (wildfire)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | No one was killed | persons | both fires | not stated (caption repeated on LW2588, LW2989, HB6201) | Leon Worden caption | `/scvhistory/lw2588.htm` | retrospective |  |
| 2 | Acres | The fires, 15 miles apart, have charred more than 7,600 acres | acres | both fires (Hasley Canyon and Placerita Canyon) | 29 Aug 1962 | Associated Press, Long Beach Independent | `/scvhistory/associatedpress082962.htm` | contemporary | a first-day figure |
| 3 | Acres | Firemen said it destroyed 3,500 acres | acres | Hasley Canyon fire (this valley) | 29 Aug 1962 | Associated Press | `/scvhistory/associatedpress082962.htm` | contemporary |  |
| 4 | Acres | it has burned 4,100 acres within an 18-mile perimeter | acres | Placerita Canyon fire (Newhall to Sylmar) | 29 Aug 1962 | Associated Press | `/scvhistory/associatedpress082962.htm` | contemporary |  |
| 5 | Acres | When the smoke cleared three days later, 17,200 acres had been scorched | acres | 'About the Fire(s)': both fires, combined | not stated | Leon Worden caption | `/scvhistory/lw2588.htm` | retrospective | whether 17,200 is one fire or both is not explicit |
| 6 | Structures destroyed | The fire already has destroyed three homes and 12 other structures as well as four oil-storage tanks | homes; structures; tanks | scope not stated (the fires) | 29 Aug 1962 | Associated Press | `/scvhistory/associatedpress082962.htm` | contemporary |  |
| 7 | Structures destroyed | the flames destroyed 75 per cent of the buildings on Gene Autry's Melody Ranch | percentage of buildings | Melody Ranch | 29 Aug 1962 | Associated Press | `/scvhistory/associatedpress082962.htm` | contemporary |  |
| 8 | Structures destroyed | 15 structures and numerous out-buildings were lost | structures | both fires | not stated | Leon Worden caption | `/scvhistory/lw2588.htm` | retrospective | agrees with AP's 3 + 12 |
| 9 | Cost | Firemen estimated the sanitarium damage was at least $100,000 | USD 1962 | Olive View Sanatorium, Sylmar (outside the valley) | 29 Aug 1962 | Associated Press | `/scvhistory/associatedpress082962.htm` | contemporary | LW2588: the rest of the facility 'was damaged beyond repair; it was demolished in 1973' (conflated with 1971 quake damage?) |
| 10 | Displaced or evacuated | Eight hundred patients fled a sanitarium / Some 800 persons were evacuated | persons | Olive View Sanatorium, Sylmar (outside the valley) | 29 Aug 1962 | Associated Press; LW2588 | `/scvhistory/associatedpress082962.htm` | contemporary | the headline figure is outside the valley |
| 11 | Displaced or evacuated | Scores of residents were evacuated from Placerita Canyon | (no count) | Placerita Canyon (this valley) | 29 Aug 1962 | Associated Press | `/scvhistory/associatedpress082962.htm` | contemporary |  |

**Cause and consequence the sources make**

- consequence: loss of Melody Ranch's Western street; Autry sold off portions; ranch rebuilt by the Veluzats from 1991 (autry.htm, lw3751)
- the AP and Leon treat two fires (Hasley Canyon, Placerita) as one story: the record must decide whether this is one event or two

**Disagreements and traps**

- The 800 evacuated were TB patients at Olive View in Sylmar, outside the valley: the headline displacement figure is not local.
- Acres: 7,600 (both, first day), 3,500 + 4,100 (each), 17,200 (final, both?).

## Clampitt Fire

- Status: Not an event (B-ranked); mentioned on place 932 and article 1430
- Date: 25 Sep 1970 onward
- Kind: fire (wildfire)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | the Clampitt Fire had scorched 107,103 acres of brush and forest, destroyed 80 structures killed four civilians | persons | Clampitt Fire, whole (regional: Newhall to Malibu) | not stated | Leon Worden gallery text, citing UC Division of Agriculture and Natural Resources | `/gif/galleries/fire092570/index.html` | retrospective | 'L.A. County's deadliest wildfire in modern history' |
| 2 | Deaths | twelve separate fires ... leaving 13 killed, 350 injured | persons | Southland, twelve fires over ten days | not stated (Ohio State Disaster Research Center report) | Disaster Research Center, The Ohio State University, quoted in the gallery | `/gif/galleries/fire092570/index.html` | secondary | regional |
| 3 | Deaths | Twelve fires over 10 days in Southland burn 525,000 acres, kill 13 | persons | Southland | not stated | SCV Chronology, 1970 | `/scvhistory/timeline.htm` | retrospective | the valley's chronology carries the regional figure |
| 4 | Injuries | 350 injured | persons | Southland, twelve fires | not stated | Ohio State Disaster Research Center | `/gif/galleries/fire092570/index.html` | secondary |  |
| 5 | Acres | 107,103 acres | acres | Clampitt Fire | not stated | Worden citing UCANR | `/gif/galleries/fire092570/index.html` | retrospective |  |
| 6 | Acres | the three fires burned 157,058 acres | acres | Clampitt, Wright and an Agua Dulce fire combined | not stated | Worden citing UCANR | `/gif/galleries/fire092570/index.html` | retrospective | Wright Fire: 27,925 acres |
| 7 | Acres | 525,000 acres | acres | Southland, twelve fires | not stated | Ohio State; SCV Chronology | `/gif/galleries/fire092570/index.html` | secondary | 'blackened more than 600,000' statewide Sep to Nov 1970 (gallery) |
| 8 | Structures destroyed | destroyed 80 structures | structures | Clampitt Fire | not stated | Worden citing UCANR | `/gif/galleries/fire092570/index.html` | retrospective | Wright Fire: 'destroying 103 more homes' |
| 9 | Structures destroyed | an estimated 1,500 buildings destroyed and damaged ... destruction of 400 houses | buildings (destroyed and damaged combined); houses destroyed | Southland, twelve fires | not stated | Ohio State Disaster Research Center | `/gif/galleries/fire092570/index.html` | secondary |  |
| 10 | Structures destroyed | destroy approx. 1,500 structures | structures | Southland | not stated | SCV Chronology | `/scvhistory/timeline.htm` | retrospective | the chronology drops 'and damaged' |
| 11 | Cost | Damage totaled $200 million | USD 1970 | Southland, twelve fires | not stated | Ohio State Disaster Research Center | `/gif/galleries/fire092570/index.html` | secondary |  |
| 12 | Displaced or evacuated | leaving 450 families homeless | families | Southland, twelve fires | not stated | Ohio State Disaster Research Center | `/gif/galleries/fire092570/index.html` | secondary |  |
| 13 | Displaced or evacuated | Thousands of persons were forced from their homes | persons | Newhall-to-Malibu fires | 28 Sep 1970 | newspaper cutline (unknown paper), LW2884 | `/scvhistory/lw2884.htm` | contemporary |  |

**Cause and consequence the sources make**

- the Clampitt (Newhall) and Wright (Malibu) fires 'joined' (chronology); part of the '1970 California Fire Siege'
- consequence locally: Mentryville saved by the Lagasses and an inmate crew (chronology; gallery)

**Disagreements and traps**

- The valley's own chronology carries only the Southland totals (13 dead, 525,000 acres, 1,500 structures) and turns '1,500 buildings destroyed and damaged' into '1,500 structures' destroyed: a regional figure read as local, and a damaged count read as destroyed.
- Even the Clampitt-only figures (4 dead, 80 structures) fell mostly in Chatsworth, Porter Ranch and Malibu, not this valley.

## Copper Fire

- Status: Not an event (B-ranked)
- Date: 5 Jun 2002
- Kind: fire (wildfire)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Acres | scorched more than 23,000 acres | acres | whole fire | Jun 2002 | news story on SC0201 | `/scvhistory/sc0201.htm` | contemporary |  |
| 2 | Acres | burned approximately 20,000 acres | acres | whole fire ('predominantly within the San Francisquito watershed') | 2016 | USDA Forest Service, Angeles National Forest | `/scvhistory/copperfire060602.htm` | primary |  |
| 3 | Structures destroyed | The fire reportedly claimed eight structures in the canyon including one home | structures | San Francisquito Canyon (this valley) | 2002 (caption) | Leon Worden caption, LW060502a-j | `/scvhistory/lw060502a.htm` | retrospective | 'reportedly' |

**Cause and consequence the sources make**

- cause: 'Construction equipment at Tesoro del Valle sparks brush fire' (chronology)
- consequence: burned the Ruiz Cemetery, burial place of St. Francis Dam victims (LW060502)
- consequence: 2005 and 2006 flooding and erosion in San Francisquito Canyon 'exacerbated by the loss of vegetation' (ANF 2016): a fire-then-flood chain the sources make

**Disagreements and traps**

- 23,000 (2002 news) against about 20,000 (Forest Service 2016).

## Simi Fire in Pico, Towsley and Stevenson Ranch

- Status: Not an event (B-ranked)
- Date: 28 Oct 2003
- Kind: fire (wildfire)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Acres | has burned more than 98,000 acres | acres | whole Simi Valley fire | 29 Oct 2003 | The Signal (Brandon Lowrey) | `/scvhistory/sg102903a.htm` | contemporary |  |
| 2 | Acres | The Simi Valley fire consumed 107,590 acres | acres | whole fire (Ventura and Los Angeles counties) | 2003/2013 | SCVTV note | `/scvhistory/PicoFire102803.htm` | retrospective | also Piru Fire 63,719 acres, Verdale Fire 8,680 |
| 3 | Structures destroyed | destroyed 37 homes | homes | whole fire (none in this valley in the sources) | 2003/2013 | SCVTV note | `/scvhistory/PicoFire102803.htm` | retrospective | in Pico Canyon 'All of the structures were saved' |
| 4 | Structures destroyed | We have not lost any structures, nor do we intend to | structures | Pico Canyon / Santa Clarita | 29 Oct 2003 | The Signal, quoting a fire official | `/scvhistory/sg102903a.htm` | contemporary | the local figure is zero |

**Cause and consequence the sources make**

- cause: 'The fire was sparked by the Val Verde fire' (Signal, 29 Oct 2003): one fire seeding another

**Disagreements and traps**

- The valley's losses were nil: the 107,590 acres and 37 homes are the whole fire's, mostly Ventura County. A section that ranks by acres would rank this fire high for a valley that lost nothing.

## Buckweed Fire

- Status: Not an event (B-ranked); Craft photograph 5275
- Date: 21 to 24 Oct 2007
- Kind: fire (wildfire)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | Fatalities: 0 | persons | this valley (Canyon Country, Agua Dulce) | 2008 (report) | CAL FIRE, California Fire Siege 2007 | `/scvhistory/lw3443.htm` | primary |  |
| 2 | Injuries | Firefighters Injured: 1 | persons | this valley (whole fire) | 2008 | CAL FIRE | `/scvhistory/lw3443.htm` | primary |  |
| 3 | Acres | Total Acres: 38,356 | acres | this valley (whole fire) | 2008 | CAL FIRE | `/scvhistory/lw3443.htm` | primary | chronology agrees |
| 4 | Structures destroyed | Structures Destroyed: 63 | structures | this valley (whole fire) | 2008 | CAL FIRE | `/scvhistory/lw3443.htm` | primary |  |
| 5 | Structures destroyed | destroys 21 homes (63 structures overall) | homes; structures | Canyon Country and Agua Dulce | not stated | SCV Chronology, 2007 | `/scvhistory/timeline.htm` | retrospective |  |
| 6 | Structures damaged | Structures Damaged: 30 | structures | this valley (whole fire) | 2008 | CAL FIRE | `/scvhistory/lw3443.htm` | primary |  |
| 7 | Cost | Direct Fire Suppression Cost: US Forest Service $5,810,000 CAL FIRE $2,135,148 | USD 2007, suppression | this valley (whole fire) | 2008 | CAL FIRE | `/scvhistory/lw3443.htm` | primary | suppression, not damage |
| 8 | Displaced or evacuated | It was estimated that 15,000 people were evacuated from 5,500 homes | persons; homes | this valley (Santa Clarita, Canyon Country, Agua Dulce) | 2008 | CAL FIRE | `/scvhistory/lw3443.htm` | primary | the largest valley-scope evacuation figure in the archive |

**Cause and consequence the sources make**

- cause: 'Undetermined' in the CAL FIRE report; '**It was a 10-year-old boy playing with matches. - Ed.' (Leon's note); chronology agrees
- part of the 2007 California Fire Siege (CAL FIRE: 'displaced nearly one million residents ... took the lives of 10 people', statewide)

**Disagreements and traps**

- The cleanest record in the archive: one primary agency report with every measure, and the chronology matches it. CAL FIRE's 'Cause: Undetermined' against Leon's annotation is a cause disagreement, not a figure one.
- Statewide siege figures (nearly one million displaced, 10 dead) sit on the same page as the Buckweed figures: a trap if read as Buckweed's.

## Station Fire

- Status: Not an event (B-ranked)
- Date: 26 Aug to Oct 2009
- Kind: fire (wildfire)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | L.A. County Fire Capt. Ted Hall, 47, and Firefighter Specialist Arnie Quinones, 34, are killed in the line of duty | persons | whole fire, regional (started outside the valley; tied to it by the Acton meeting and the 14 Freeway interchange, per events-missed-2026-10-05) | not stated | SCV Chronology, 2009 | `/scvhistory/timeline.htm` | retrospective |  |
| 2 | Deaths | killing two firefighters | persons | whole fire | 31 Aug 2014 | KHTS (Jessica Boyer) | `/scvhistory/khts083114.htm` | secondary |  |
| 3 | Acres | reaching more than 160,000 acres | acres | whole fire (Angeles National Forest) | 31 Aug 2014 | KHTS | `/scvhistory/khts083114.htm` | secondary |  |
| 4 | Structures destroyed | destroying more than 100 homes and structures | homes and structures | whole fire | 31 Aug 2014 | KHTS | `/scvhistory/khts083114.htm` | secondary |  |
| 5 | Cost | costing more than $80 million dollars to fight | USD 2009, suppression | whole fire | 31 Aug 2014 | KHTS | `/scvhistory/khts083114.htm` | secondary | suppression |

**Cause and consequence the sources make**

- consequence: Hall-Quinones memorial interchange (antonovich111510)

**Disagreements and traps**

- Entirely regional: none of the acreage or structures is shown to be in this valley; the event is local by its dead firefighters' memorial, not its losses.

## El Nino storms of 1997-98

- Status: Not an event (B-ranked)
- Date: 2 Feb to Mar 1998; worst 23 Feb 1998
- Kind: flood (storm)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Structures destroyed | Homes in Mint Canyon were destroyed in the torrential flooding | homes (no count) | Mint Canyon (this valley) | not stated (caption repeated on GT9801, GT9802, GT9804) | Leon Worden caption | `/scvhistory/gt9802.htm` | retrospective | GT9804: one mobile home on Garyford Road |
| 2 | Cost | Storm damage to Ventura, Los Angeles Counties on just one day, Feb. 23, was estimated at $20 million | USD 1998 | Ventura and Los Angeles counties, one day | not stated | Leon Worden caption | `/scvhistory/gt9802.htm` | retrospective |  |
| 3 | Cost | For the period of Feb. 9 to March 1, 1998, estimates exceeded $475 million statewide | USD 1998 | statewide | not stated (FEMA, 3 Mar 1998, cited as further reading) | Leon Worden caption | `/scvhistory/gt9802.htm` | retrospective |  |
| 4 | Displaced or evacuated | By March 1, fifteen thousand people had filed with FEMA for federal disaster relief | persons filing (not displaced) | statewide | not stated | Leon Worden caption | `/scvhistory/gt9802.htm` | retrospective | a claims count, not displacement |

**Cause and consequence the sources make**

- cause: El Nino winter; 'a month-long succession of devastating storms' (chronology)
- consequence: Beale's Cut 'went from a 90-foot-deep historic monument to a mud puddle' (GT9801)

**Disagreements and traps**

- Every figure is county or statewide; the valley's losses (Mint Canyon homes, Beale's Cut) have no count.

## 1857 Fort Tejon earthquake

- Status: Not an event (B-ranked)
- Date: 9 Jan 1857
- Kind: earthquake


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | Only two lives were lost in this quake | persons | scope not stated (the whole earthquake) | 2011 | Alan Pollack | `/scvhistory/pollack0111tejon.htm` | retrospective |  |
| 2 | Deaths | Severe earthquake felt in Los Angeles County; 2 killed | persons | Los Angeles County | not stated | SCV Chronology, dated '1856 January 9' | `/scvhistory/timeline.htm` | retrospective | the chronology carries this line under 1856 as well as 'Major earthquake decimates Fort Tejon' under 1857: probably the same quake misdated |
| 3 | Deaths | One Newhall woman was killed when her house collapsed on her | persons | unclear: 'Newhall' (the town did not exist until 1876) | 28 Aug 2003 | John Boston | `/scvhistory/sg082803.htm` | retrospective | anachronistic place name; unsourced |
| 4 | Structures damaged | Nearly all the buildings in the vicinity were seriously injured | buildings (no count) | Fort Tejon | 21 Feb 1857 (Harper's Weekly; also quoted from the Los Angeles Star) | Harper's Weekly | `/scvhistory/hw022157.htm` | contemporary |  |

**Cause and consequence the sources make**

- consequence: Fort Tejon barracks destroyed and rebuilt (Pollack 2011)

**Disagreements and traps**

- Two dead (Pollack; chronology) against Boston's single 'Newhall woman', in a place that was not yet Newhall. The chronology's 1856 line looks like a misdated duplicate of the 1857 quake: a source fault to log, not a second event.

## Newhall School fires (1890, 1914, 1939)

- Status: Not an event (B-ranked, proposed as one record)
- Date: 1890; 1914; 14 Feb 1939
- Kind: fire (structure)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | No one was injured | persons | Newhall School, 1939 | 14 Feb 1939 | Associated Press / Los Angeles Times | `/scvhistory/tlp_lat021539pg2.htm` | contemporary |  |
| 2 | Cost | Fire destroyed the Newhall elementary school today, with a loss estimated at $60,000 | USD 1939 | Newhall School, 1939 | 14 Feb 1939 | Associated Press (GR0221); 'Grammar Building Burns During Night With $60,000 Loss' | `/scvhistory/gr0221.htm` | contemporary |  |
| 3 | Cost | The building and contents were insured for $1,500 and were valued at $2,250 | USD 1890 | Newhall School, 1890 | Jun 1890 | Los Angeles Herald | `/scvhistory/lp_laherald060490school.htm` | contemporary |  |
| 4 | Structures destroyed | Newhall School burns to the ground (first, second, third time) | one building each time | Newhall | not stated | SCV Chronology 1890, 1914, 1939 | `/scvhistory/timeline.htm` | retrospective |  |

**Cause and consequence the sources make**

- consequence: rebuilding; 1940 bond for the auditorium passed 86 to 5 (events-missed)

**Disagreements and traps**

- Single-building fires: comparable only on cost, and the 1890 and 1939 figures are a value and a loss estimate.

## Other B-ranked air crashes (1932 Lebec, 1938 Agua Dulce, 1956 Battle of Palmdale, 1982 Twilight Zone)

- Status: Not events (B-ranked); grouped here because each has a single figure
- Date: 1932; 1938; 1956; 1982
- Kind: air crash


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | EIGHT DEAD! / All eight persons died instantly in the crash or nearly so in the fire | persons | Century-Pacific airliner near Lebec, 29 Jan 1932 | Feb 1932 | Los Angeles Times and others (LW3288) | `/scvhistory/lw3288.htm` | contemporary | Lebec is north of the valley |
| 2 | Deaths | Nine persons, including two children, died in the disaster / All nine occupants were killed | persons | Lockheed 14 (Northwest Airlines NC17394), Sierra Pelona near Agua Dulce, 16 May 1938 | May 1938 | press (LW2680a; San Mateo Times); Aviation Safety Network 'Total: Fatalities: 9 / Occupants: 9' | `/scvhistory/lw2680a.htm` | contemporary |  |
| 3 | Cost | the crash of an $80,000 Lockheed air transport | USD 1938, aircraft value | Lockheed crash 1938 | 18 May 1938 | United Press, San Mateo Times | `/scvhistory/lp_sanmateotimes051838.htm` | contemporary | aircraft value, not damage |
| 4 | Deaths | miraculously no one was injured | persons | Battle of Palmdale, 16 Aug 1956 | 2015 | Alan Pollack | `/scvhistory/pollack0115battleofpalmdale.htm` | retrospective |  |
| 5 | Acres | Placerita Canyon 75 to 100 acres; Ridge Route 50 to 75 acres; Soledad Canyon 'over 300 acres' then 'an estimated 350 acres' | acres | Battle of Palmdale fires (this valley) | 2015 | Alan Pollack | `/scvhistory/pollack0115battleofpalmdale.htm` | retrospective |  |
| 6 | Acres | They set fires that burned over 1,000 acres | acres | Battle of Palmdale fires | not stated | Leon Worden caption, LW3766 | `/scvhistory/lw3766.htm` | retrospective | against Pollack's three fires |
| 7 | Deaths | Vic Morrow and two child actors killed / All three were killed instantly | persons | Twilight Zone helicopter crash, Indian Dunes, 23 Jul 1982 | not stated; 2012 | SCV Chronology; Alan Pollack | `/scvhistory/pollack0512morrow.htm` | retrospective | date disputed (22 Jul, 23 Jul, '1-27-1982'), toll not |

**Cause and consequence the sources make**

- Battle of Palmdale: cause of the fires = the Navy's 208 rockets fired at a runaway drone (LW3766): an air incident whose damage was a fire
- Twilight Zone: NTSB probable cause, special-effects explosions too near the helicopter (ntsb103084); Landis acquitted 1987

**Disagreements and traps**

- Palmdale acres: Pollack's three fires (75 to 100, 50 to 75, about 350 acres) against Worden's 'over 1,000'.

## Bermite Powder Company explosion

- Status: Not an event (B-ranked); Craft photograph 5611
- Date: 28 Jan 1954
- Kind: other (industrial explosion)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | Arms Plant Blast Kills 1; 17 Injured | persons (headline) | Bermite plant, Saugus | 29 Jan 1954 | newspaper (LW3764) | `/scvhistory/lw3764.htm` | contemporary |  |
| 2 | Deaths | Eighteen women were injured, four fatally | persons | Bermite plant | not stated | Leon Worden, LW3764 | `/scvhistory/lw3764.htm` | retrospective | 4 dead within 18 hurt |
| 3 | Deaths | Four women killed, 14 injured | persons | Bermite plant | not stated | SCV Chronology, 1954 | `/scvhistory/timeline.htm` | retrospective |  |
| 4 | Injuries | Sixteen women were injured, three of them critically | persons | Bermite plant | 28 Jan 1954 | United Press photo cutline (LW3764) | `/scvhistory/lw3764.htm` | contemporary |  |
| 5 | Injuries | 17 Injured | persons | Bermite plant | 29 Jan 1954 | newspaper (LW3764) | `/scvhistory/lw3764.htm` | contemporary |  |
| 6 | Injuries | 14 injured | persons | Bermite plant | not stated | SCV Chronology | `/scvhistory/timeline.htm` | retrospective | 14 survivors of 18 hurt |

**Cause and consequence the sources make**

- later: 1969 second fatal flash (C); the Bermite site's perchlorate cleanup (events-missed)

**Disagreements and traps**

- Like Flight 7, the dead rose (1 to 4) and the injured fell as the hurt died: 16, 17, 18 (including the 4 dead), 14 (survivors). Whether an injury count includes those who later died has to be a field.

## COVID-19 in the Santa Clarita Valley

- Status: Not an event (B-ranked)
- Date: 13 Mar 2020 onward
- Kind: other (epidemic)


| # | Measure | Value as printed | Unit | Scope | As of | Source | Path | Kind of source | Note |
|---|---|---|---|---|---|---|---|---|---|
| 1 | Deaths | First reported death in the SCV from COVID-19 | persons (first death, no total) | this valley | 31 Mar 2020 | SCV Chronology | `/scvhistory/timeline.htm` | retrospective | no death total on the mirror |

**Cause and consequence the sources make**

- consequence: state and city emergencies, school closures (chronology 13 Mar 2020)

**Disagreements and traps**

- Cases (chronology: over 500 by 2 May, over 1,000 by 21 May, over 2,000 by 5 Jun 2020, 'including approx. 1,000 at Pitchess Detention Center/NCCF') are not injuries and are not entered as rows. An epidemic does not fit the measures: no acres, structures or displacement, and its 'injuries' are case counts. If it belongs in the section, it needs its own measure.

## Matrix: sourced figures per measure and event

Counts are rows in the tables above (one row per figure per source; a row bundling several sources counts once).

| Event | Deaths | Injuries | Acres | Structures destroyed | Structures damaged | Cost | Displaced or evacuated | Total |
|---|---|---|---|---|---|---|---|---|
| St. Francis Dam Disaster | 41 | 3 | 4 | 5 | 3 | 12 | 1 | 69 |
| Great Flood of 1938 | 7 | 1 | 0 | 5 | 0 | 3 | 2 | 18 |
| Sylmar (San Fernando) Earthquake | 7 | 1 | 0 | 2 | 3 | 5 | 2 | 20 |
| Northridge Earthquake | 5 | 2 | 0 | 1 | 3 | 9 | 3 | 23 |
| Powerhouse Fire | 1 | 1 | 6 | 3 | 2 | 1 | 2 | 16 |
| United Air Lines Flight 34 Crash in Rice Canyon | 8 | 0 | 0 | 0 | 0 | 0 | 0 | 8 |
| Western Air Express Flight 7 Crash | 12 | 4 | 0 | 0 | 0 | 1 | 0 | 17 |
| Placerita Canyon (Melody Ranch) and Hasley Canyon fires, 1962 | 1 | 0 | 4 | 3 | 0 | 1 | 2 | 11 |
| Clampitt Fire | 3 | 1 | 3 | 3 | 0 | 1 | 2 | 13 |
| Copper Fire | 0 | 0 | 2 | 1 | 0 | 0 | 0 | 3 |
| Simi Fire in Pico, Towsley and Stevenson Ranch | 0 | 0 | 2 | 2 | 0 | 0 | 0 | 4 |
| Buckweed Fire | 1 | 1 | 1 | 2 | 1 | 1 | 1 | 8 |
| Station Fire | 2 | 0 | 1 | 1 | 0 | 1 | 0 | 5 |
| El Nino storms of 1997-98 | 0 | 0 | 0 | 1 | 0 | 2 | 1 | 4 |
| 1857 Fort Tejon earthquake | 3 | 0 | 0 | 0 | 1 | 0 | 0 | 4 |
| Newhall School fires (1890, 1914, 1939) | 1 | 0 | 0 | 1 | 0 | 2 | 0 | 4 |
| Other B-ranked air crashes (1932 Lebec, 1938 Agua Dulce, 1956 Battle of Palmdale, 1982 Twilight Zone) | 4 | 0 | 2 | 0 | 0 | 1 | 0 | 7 |
| Bermite Powder Company explosion | 3 | 3 | 0 | 0 | 0 | 0 | 0 | 6 |
| COVID-19 in the Santa Clarita Valley | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 |
| **Rows** | 100 | 17 | 25 | 30 | 13 | 40 | 16 | 241 |
| **Events with any figure (of 19)** | 16 | 9 | 9 | 13 | 6 | 13 | 9 | |

### The same matrix, local scope only

Rows whose scope is this valley, a named place in it, the plant, or the aircraft (for crashes). Whole-path, county, regional and statewide rows are left out; so are 'whole fire' rows, since most fires named here burned largely outside the valley.

| Event | Deaths | Injuries | Acres | Structures destroyed | Structures damaged | Cost | Displaced or evacuated | Total |
|---|---|---|---|---|---|---|---|---|
| St. Francis Dam Disaster | 6 | 1 | 1 | 1 | 0 | 0 | 0 | 9 |
| Great Flood of 1938 | 1 | 1 | 0 | 2 | 0 | 0 | 0 | 4 |
| Sylmar (San Fernando) Earthquake | 2 | 0 | 0 | 2 | 2 | 2 | 1 | 9 |
| Northridge Earthquake | 1 | 0 | 0 | 1 | 2 | 4 | 2 | 10 |
| Powerhouse Fire | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 1 |
| United Air Lines Flight 34 Crash in Rice Canyon | 8 | 0 | 0 | 0 | 0 | 0 | 0 | 8 |
| Western Air Express Flight 7 Crash | 11 | 4 | 0 | 0 | 0 | 0 | 0 | 15 |
| Placerita Canyon (Melody Ranch) and Hasley Canyon fires, 1962 | 0 | 0 | 2 | 0 | 0 | 0 | 1 | 3 |
| Clampitt Fire | 0 | 0 | 1 | 0 | 0 | 0 | 1 | 2 |
| Copper Fire | 0 | 0 | 0 | 1 | 0 | 0 | 0 | 1 |
| Simi Fire in Pico, Towsley and Stevenson Ranch | 0 | 0 | 0 | 2 | 0 | 0 | 0 | 2 |
| Buckweed Fire | 1 | 1 | 1 | 2 | 1 | 1 | 1 | 8 |
| Station Fire | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| El Nino storms of 1997-98 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | 1 |
| 1857 Fort Tejon earthquake | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| Newhall School fires (1890, 1914, 1939) | 1 | 0 | 0 | 1 | 0 | 2 | 0 | 4 |
| Other B-ranked air crashes (1932 Lebec, 1938 Agua Dulce, 1956 Battle of Palmdale, 1982 Twilight Zone) | 3 | 0 | 2 | 0 | 0 | 1 | 0 | 6 |
| Bermite Powder Company explosion | 3 | 3 | 0 | 0 | 0 | 0 | 0 | 6 |
| COVID-19 in the Santa Clarita Valley | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 |
| **Rows** | 38 | 10 | 7 | 13 | 5 | 10 | 7 | 90 |
| **Events with any local figure (of 19)** | 11 | 5 | 5 | 9 | 3 | 5 | 6 | |

"Events with any figure" counts a sourced absence ("no one was killed", "none ... reports a death in the Santa Clarita Valley") as a figure, because a sourced zero is information. The local matrix is a reading of each row's scope, made for this audit; it treats a crash's toll (the people aboard) as local when the crash site is, and leaves out the Edison camp (county disputed) and every whole-fire row.

## Findings

### 1. Per measure: how many events have a figure

| Measure | Events with any figure (of 19) | Events with a local figure | Comparable across events? |
|---|---|---|---|
| Deaths | 16 | 11 | Yes, the one measure that is. Two conditions: each row must say when it was counted (Flight 7 rose from 1 to 5 in a week; Bermite from 1 to 4), and its scope (the dam's 411 are the whole 54-mile path; the 1938 flood's 113 to 115 and Sylmar's 65 are regional, with at most one sourced death in the valley each) |
| Injuries | 9 | 5 | No. Missing for the dam's valley reach, the 1938 flood, both earthquakes' valley figures and most fires; where present it mixes "serious" with any, and counts that include the later dead with counts that do not (Bermite 14, 16, 17, 18) |
| Acres | 9 | 5 | Among fires only, and only whole-fire against whole-fire. Flood acreage is a different thing (Newhall Land's 1,720 acres of farmland "totally destroyed"; Ventura County's 10,658 acres "injured"). Several fires' acres lie mostly outside the valley (Powerhouse, Simi, Clampitt, Station) |
| Structures destroyed | 13 | 9 | Weakly. The unit varies: homes, houses, buildings, structures, "homes and structures", "destroyed or greatly damaged" combined, percentages (Melody Ranch 75 per cent), and named buildings with no count (1938, Sylmar, El Nino) |
| Structures damaged | 6 | 3 | No. Six events, six units (properties, homes, buildings, mobile homes as a percentage, bridges, "destroyed or damaged" together) |
| Cost | 13 | 5 | No, as the archive holds it. The figures are of different kinds: property-loss estimates, claims claimed and claims settled, relief appropriations, fire-suppression costs, one aircraft's value, one lawsuit, one school's insured value. Only Northridge has a full valley estimate ($430,796,227.35, city of Santa Clarita, December 1994). Sylmar's only valley cost ($5.3 million) is an unsourced 2003 column |
| Displaced or evacuated | 9 | 6 | No. The heads are homeless, evacuated, sheltered on one night, families homeless, people filing with FEMA, patients moved, and people who sold their homes and left |

Effectively uncomparable: injuries, structures damaged, cost and displacement. Comparable with care: deaths (with as-of and scope), acres (fires only), structures destroyed (only where the unit matches).

### 2. Cost figures and their years

For a later conversion, the year each cost is in. "Base not stated" means a later source gives a figure without saying whose dollars.

| Event | Figure as printed | Kind | Dollars of | Source |
|---|---|---|---|---|
| St. Francis Dam | "will exceed $25,000,000" | property loss estimate | 1928 | Newhall Land, 24 Mar 1928 |
| St. Francis Dam | "$7,000,000 to $30,000,000"; "$10,000,000 and $30,000,000"; "$15,000,000"; "30 milioni di dollari" | property loss, press | 1928 | headlines 14 to 25 Mar 1928 |
| St. Francis Dam | "$12,000,000" (with "personal injury and loss of life") | loss estimate | 1928 | Guy L. Jones 1928 |
| St. Francis Dam | "$3,625,000" | loss to Los Angeles of water and power | 1928 | Guy L. Jones 1928 |
| St. Francis Dam | "$3,674,207.56" claimed; "915,751.74" settled; "1,376,251.35" unsettled | death and injury claims only | 1928-29 | Citizens' Restoration Committee, 15 Jul 1929 |
| St. Francis Dam | "Fifty Thousand Dollars"; "One Million Dollars"; "another Million Dollars" | relief appropriations | 1928 | same |
| St. Francis Dam | "$4.8 million in damage claims" | claims paid | base not stated | LA Times 1995 |
| St. Francis Dam | "$13 million at the time" | damage | 1928 (stated "at the time") | KHTS 2014 |
| St. Francis Dam | "about $20,000,000"; "approached 20 million dollars" | property loss | base not stated | LW3184 reissue; Del Castillo 1980 |
| Great Flood of 1938 | "$8,000,000" | state highways, Dec 1937 to Mar 1938 | 1937-38 | California Highways, Apr 1938 |
| Great Flood of 1938 | "more than $60,000,000" | statewide, winter and spring | 1938 | Panhorst, Aug 1938 |
| Great Flood of 1938 | "$78 million" | damage, Los Angeles | base not stated (presumably 1938) | Pollack 2019 |
| 1962 fires | "at least $100,000" | Olive View damage, Sylmar | 1962 | AP, 29 Aug 1962 |
| Clampitt and the 1970 fires | "$200 million" | damage, Southland, twelve fires | 1970 | Ohio State Disaster Research Center |
| Sylmar 1971 | "more than half a billion dollars"; "$500 million" | damage, both valleys | 1971 | Leon Worden |
| Sylmar 1971 | "$1 billion" | damage, Southern California | base not stated | John Boston 2003 |
| Sylmar 1971 | "$5.3 million"; "About a $3 million cleanup bill" (Thatcher Glass) | local damage | base not stated (presumably 1971) | John Boston 2003 |
| Northridge 1994 | "$430,796.227.35" | damage and cost estimate, the valley | Dec 1994 | City of Santa Clarita (SC9405) |
| Northridge 1994 | "$20 billion" | losses, four counties | 1994 | USGS |
| Northridge 1994 | "$11 billion" | damage, Southern California | base not stated | SCV Chronology |
| Northridge 1994 | "over $20 billion"; "between $13 and $50 billion"; "in excess of $4.5 million" (City Hall) | various | base not stated; unsourced | Craft #875 eventSignificance and withheldBody |
| Northridge 1994 | "$30 million" (hospital); "Initial cost estimates were $20 million" | damage and reconstruction | base not stated | HM9801 caption, 1998 |
| El Nino 1998 | "$20 million" (one day, two counties); "$475 million" (statewide) | damage | 1998 | Leon Worden caption |
| Buckweed 2007 | "US Forest Service $5,810,000 CAL FIRE $2,135,148" | suppression | 2007 | CAL FIRE |
| Station 2009 | "more than $80 million dollars to fight" | suppression | base not stated (2009 fire, 2014 article) | KHTS 2014 |
| Powerhouse 2013 | "$23.4 million" (preliminary) | suppression | 2013 | Leon Worden |
| Newhall School | "$2,250" value, "$1,500" insured (1890); "$60,000" loss (1939) | building value; loss | 1890; 1939 | LA Herald 1890; AP 1939 |
| Lockheed crash 1938 | "$80,000" | aircraft value | 1938 | United Press |
| Flight 7 1937 | "$502,539" | lawsuit claim (lost) | 1937 | DS3701 caption |

### 3. The worst disagreements

1. **St. Francis Dam, deaths.** From "200 Dead" (13 March 1928) to "FLOOD DEATHS NEAR 1000" (14 March), "probably almost five hundred" (Newhall Land, 24 March), "451 lives" (Jones 1928), "Total Number of Persons Killed 306" plus 64 unidentified bodies (claims committee 1929), 425, 432, 450, 470, 500, "between 450 and 600", 431 (Stansell 2014) and 411 (Worden 2018). The 1929 claims report is the only primary count, and it counts on a claims basis.
2. **St. Francis Dam, homes.** "Five hundred homes destroyed or greatly damaged" (NYT, 15 Mar 1928), "800 Houses" (Oakland Tribune, 14 Mar 1928), "1,200 homes" (LA Times 1995 and 2003, LA County 2018; no 1928 source found for it) and "7000 homes" (a press caption of about 1934). None states its scope.
3. **Northridge on one record.** 53 dead (chronology), 57 (Craft eventSignificance), 60 (USGS, and the withheld body); $11 billion, $13 to $50 billion, $20 billion, over $20 billion. Two of the four Craft-held figures have no footnote.
4. **Western Air Express Flight 7.** "Kills 2" (The Newhall Signal, 14 Jan 1937), "2 dead, 11 injured" (the chronology, still), "5 Killed" (SCVHistory.com's own heading over the Signal page that says 2), "Total fatalities: 5" (Accident Board). Not an error so much as a count taken at different times; the archive contradicts the newspaper it presents.
5. **Sylmar cost.** $500 million (Worden, "both valleys") against $1 billion (Boston, "Southern California"); and a local $5.3 million against one firm's $3 million cleanup in the same column.
6. **Bermite.** Dead 1 then 4; injured 16, 17, 18 (with the four dead) and 14 (without). The chronology's "Four women killed, 14 injured" and Leon's "Eighteen women were injured, four fatally" agree only once the reader knows one count contains the other.
7. **Smaller ones.** The Ruiz family dead, 6, 7 or 8. Powerhouse homes 30 against "at least 24". Henry Mayo $16.5 million, $20 million or $30 million. Battle of Palmdale acres: Pollack's three fires of 75 to 100, 50 to 75 and about 350 against "over 1,000" (Worden). The 1857 quake's "two lives" against John Boston's "One Newhall woman", in a place not yet named Newhall.

### 4. Regional figures likely to be read as local

1. **Great Flood of 1938**: 113 to 115 dead, 5,601 buildings, $78 million. All Southland or Los Angeles. No sourced death in the valley. The valley's losses are named buildings and bridges with no count.
2. **Sylmar 1971**: 65 dead, "injured thousands", $500 million to $1 billion, and "tens of thousands" evacuated below the Van Norman Dam. The epicenter was in the valley, which makes the regional figures easy to read as local. One valley death is claimed by an unsigned page and named by no source.
3. **Northridge 1994**: 53, 57 or 60 dead; 7,000 or 9,000 injured; 20,000 homeless; 40,000 buildings; $11 to $50 billion. The valley figures exist and are different: one death (Officer Clarence Wayne Dean), $430.8 million, shelters holding 100, 150, 240 and 150 people on 18 January.
4. **St. Francis Dam**: 411 (or any toll) is the whole path through two counties; the "1,500 persons virtually homeless", "Five hundred homes" and every cost are path-wide or Ventura County. Only Stansell's "General Area" column (Craft researchLeads) would give a valley count, and the 84 Edison camp dead sit on a disputed county line.
5. **Clampitt Fire 1970**: the valley's own chronology says "Twelve fires over 10 days in Southland burn 525,000 acres, kill 13 and destroy approx. 1,500 structures", and its source says "1,500 buildings destroyed and damaged". Regional, and damaged read as destroyed.
6. **The 1962 fires**: the headline "800 TB Patients Flee" is Olive View in Sylmar, outside the valley; so is the $100,000.
7. **Fires named for valley places whose losses were elsewhere**: Powerhouse (homes lost at Lake Hughes and Elizabeth Lake), Simi 2003 (107,590 acres and 37 homes, none in the valley; "We have not lost any structures"), Station 2009 (160,000 acres in the Angeles National Forest). Ranking these by acres would rank fires the valley barely lost to.
8. **Pages that carry two scopes**: Panhorst's "$14,600,000" sits on the 1938 flood page but is the northern flood of December 1937; CAL FIRE's "nearly one million residents" and "10 people" are the whole 2007 siege, on the Buckweed page; the USGS Northridge summary is repeated in the navigation box of 29 pages of the 1994 series, so it will be the figure a reader meets first.

### 5. Kinds, and the cause and consequence the sources make

| Event | Kind |
|---|---|
| St. Francis Dam Disaster | structural failure (dam) causing a flood |
| Great Flood of 1938 | flood (storm) |
| Sylmar Earthquake | earthquake |
| Northridge Earthquake | earthquake |
| Fort Tejon earthquake 1857 | earthquake |
| Powerhouse, 1962 Placerita and Hasley, Clampitt, Copper, Simi, Buckweed, Station fires | fire (wildfire) |
| Newhall School fires | fire (structure) |
| El Nino 1997-98 | flood (storm) |
| United 34, Western Air 7, Lebec 1932, Agua Dulce 1938, Twilight Zone 1982 | air crash |
| Battle of Palmdale 1956 | air crash or incident whose damage was fire |
| Bermite explosion 1954 | other (industrial explosion) |
| COVID-19 | other (epidemic) |
| Newhall Pass truck fire | not found: no page on the mirror and no Craft text names it (searched "truck tunnel", "tunnel fire", "truck fire", 2007 with tunnel) |

No rail accident reached B rank; the rail list in events-missed is all C.

Links the sources make, cause to consequence:

- The dam's failure and its flood (one Craft event; the coroner's verdict of 12 April 1928 places responsibility). The dam to the Citizens' Restoration Committee and its claims; to Bouquet Canyon Dam as replacement storage (1930 bond, LW3573); to CHL No. 919 (1978) and the National Memorial (2019).
- Sylmar 1971 to the first Newhall Pass interchange collapse; to the Newhall Pharmacy front falling on 13 March 1971; to the First Presbyterian rebuild; to stronger plans for College of the Canyons and Henry Mayo; to the Legislature's subcommittee. Northridge 1994 to the second collapse at the same place and Officer Dean's death; to the Greenbrier mobile home fires from ruptured gas lines; to the tent City Hall.
- The 1938 flood to the work-train derailment near Saugus on 25 March 1938 ("Collateral damage"); to the end of Hill's rodeo grounds and the Saugus Speedway; to the Flood Control Act of 1941 and the Corps' unbuilt 1968 Santa Clara River plan.
- The Copper Fire 2002 to the flooding and erosion of 2005 and 2006 in San Francisquito Canyon (Forest Service 2016): fire then flood. It also burned the Ruiz Cemetery, where dam victims lie (a place link, not a cause).
- The Val Verde fire to the Simi Fire's arm in Pico Canyon (The Signal, 29 Oct 2003).
- The Navy's 208 rockets to the Battle of Palmdale fires.
- Pilot error, by the Accident Board, for both 1936-37 crashes; the two are paired by every account but neither caused the other.

### 6. Other findings

- **Craft #875 holds unsourced figures.** Its eventSignificance (57 dead, over $20 billion) and its withheldBody (60 dead, 9,000 injured, $13 to $50 billion, City Hall over $4.5 million) carry no footnote and disagree with the footnoted sources.
- **The chronology is the most quoted source and the one that most often carries a regional figure on a local page**: 1938, Clampitt, Northridge, and the first-day "2 dead" for Flight 7. It also has a "1856 January 9 ... 2 killed" line that looks like a misdated duplicate of the 1857 quake.
- **SC9405, the city's Northridge estimate**: the HTML page prints private damage as "$200,213,884"; the PDF it transcribes prints "$200.231,884". Transposed digits.
- **The claims report places the Edison camp "in the town of Piru"**, against Newhall Land (west of the county line) and Pollack 2014 (east of it).
- **What the measures will need to carry**, from what the sources do: when a figure was counted (dead at once and dead within a week); whether a structures count is destroyed, damaged or both together; and which kind of money a cost is (loss estimate, claims, suppression, appropriation, insured value).

### 7. Not audited

- The Saugus High School Shooting (#31338): a crime, outside the kinds Nathan named. Whether the section takes it is a question for Nathan.
- Sand Fire 2016 and Tick Fire 2019: on Nathan's list held by another agent, not in the events-missed A and B entries.
- The C-ranked fires, floods and crashes of events-missed.
- Leads that would settle figures: Stansell's 2018 roster PDF (a valley count of the dam's dead), the Newhall Signal of March 1928 and of 3 March 1938 (not on the mirror), the Bureau of Air Commerce reports (one an image-only PDF), and a 1971 Signal for the valley's earthquake damage.

Wikipedia was not consulted.

