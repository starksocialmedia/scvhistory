# TODO

## Waiting on Nathan

### Current (6 October 2026, evening)
- **Restore the 44 drafted trustees?** The person-record rule turned 45 trustees into rows whose profiles were drafted and waiting on your read (27 Hart, 18 College; thin only because the drafts were not applied). restore_drafted_trustees_2026_10_06.php (dry run: inventory/review/restore-drafted-trustees-dry-run-2026-10-06.txt; Lyon left out) puts them back; then the Hart and College profile dry runs apply. Surviving now without it: Hart 28 of 55, College 15 of 33.
- **Scott Newhall's lead** (fix_scott_newhall_2026_10_06.php, $WITH_LEAD): the wording is in the dry run; it carries "he and Ruth bought it". Also: writtenBy on the oral history (empty, so it stays in his "About" list).
- **Five event drafts:** the Powerhouse Fire, the Sylmar Earthquake (title: or Leon's "1971 Sylmar Earthquake"), the Great Flood of 1938, United Flight 34, Western Air Express Flight 7 (create_*_event_2026_10_06.php; each draft's forNathan list).
- **Mike Garcia's profile:** keep or cut the sentence on his January 2021 votes on the electoral-vote objections (build_mike_garcia_profile_2026_10_06.php, held).
- **Fran Pavley's portrait:** CC BY 2.0; the license field needs a cc-by-2.0 option (schema).
- **Cephas Bard:** no valley role could be sourced; under the person rule he may be a row.
- **Portrait search** (inventory/review/portrait-search-2026-10-06.md): crops needing your word on edited images: Cathie Wright now has a Commons portrait; John Boston (sg030506b-honby, a photo illustration), Michele Jenkins (CO1501c), Tom Frew IV (HS9019); three named portraits whose files never reached the drive (Murr, Taylor, Ellis).
- **Katie Hill:** the three corrections to the brief (the dates; "the first Democrat"; "unlawful") are in inventory/review/katie-hill-profile-draft-2026-10-06.md; read, then apply.
- **Stern's holding #29521** is marked reelected in 2024 though his district then held none of the valley.

### Current (6 October 2026)
- **Push** templates-batch-9 (the agent's push was refused by the permission check): MacBook, `git push origin templates-batch-9` after predeploy passes.
- **Duplicate article pairs** (Leon Worden's Signal columns): #12206/#12200, #12204/#12188, #12280/#12258 are word-for-word the same, one copy from /signal/worden/old/; #12152 (869 words) and #12208 (761) are two versions of the Piru column. Which to keep.
- **Letters as documents with a writer:** John Lang's (#28057), Abel Stearns's (#26983): does a letter's writer count as its author.
- **The redirect map at cutover:** sg20191114shs.htm to the Saugus High event (D12), the video pages (D11), chp-newhall-incident.htm to the Newhall Incident.
- **The 356 bylines:** 320 linked on 5 October, 5 documents on 6 October; what remains is names with no record (25), a pen name, and the held cases.

### Current (5 October 2026, late night)
- Search names: applied 6 October. Was: the alias removal cut "A.B. Perkins", "Bill Hart", "Joseph Messina" and others out of search; a hidden search field puts 177 back and 4 maiden names return as shown aliases.
- **Portrait batch** (inventory/review/portrait-batch-dry-run-2026-10-05.md): read before apply. Chico López has a likeness (US8502), so his "no likeness" note is held: import it instead?
- **Events, dry runs to read:** the Saugus High sources then event (the event needs the sources applied first; whether events get a sourceDocuments field); the St. Francis Dam loader (its decision list); Cityhood, Placerita, the golden spike, the Newhall Incident (naming the gunmen once, as the sources do).
- **War memorial sources** (inventory/review/war-memorial-unsourced-dry-run-2026-10-05.md): Kenaston via the VA locator (approve its use for him), Colley via the 2003 yearbook; Wilson, Todd, Conant, Ross searched and still short.
- **Authors:** documents need a writtenBy field (11 waiting); the Ellis Gazette bylines linked on two SCVNews pieces; the four duplicate article pairs; #2173 and #12852 held.
- **Tom Frew II:** a record of his own?

### Current (5 October 2026, night)
- **Appointed terms:** done 5 October (unopposed, the splits, Messina, the four notes, Gibbs and Plambeck). Still open: the 2 partly right runs (Talley 2016, Moore 2017); DeFigueiredo 2007's "no election held", which rests only on a contest missing from CEDA; the 18 RISKY quotations and the #394 attribution in inventory/review/spliced-quotations-audit-2026-10-05.md.
- **The portrait census** is running again (5 October night); its report writes as it goes.
- **Same-name aliases** (inventory/review/aliases-same-name-dry-run-2026-10-05.txt): 186 lines on 135 people, dry run; Messina's applied. Four kept although the test matched (McKeon's Howard, Knight's William, Weinstein's Rochelle, Tichenor's Jr.). Removing "Bill Hart" and the like also removes them from search.
- **Authorship** (inventory/review/authorship-census-2026-10-05.md): approve writtenBy on documents and a bylineText field; then about 329 easy links; the person page to name a person's columns.
- **Silent faults, the dry runs:** war memorial narratives (8, restore_war_memorial_narratives_2026_10_05.php, with extract_war_memorial_narratives_2026_10_05.py run first on the MacBook); Hart Park captions (13), place legacy links (7), place image alt (6) (inventory/review/*-dry-run-2026-10-05.md); the Mentry credit is right and stays. Decisions in each.
- **Connie Worden-Roberts:** restore Goldman's two dropped paragraphs (#28047)? correct #28045's publication line to the mortuary's dateline? Choppé's portrait on her person record? Perry Smith's piece (#28305) as an obituary?
- **Collections:** the three topic pages (Newsmaker, Iraq, Mentryville) and the Gazette catalogue still take their own layouts; whether they too take the standard one.

### Current (4 October 2026, overnight)
- Measure U and the fourteen council elections: applied 5 October. **The five County-sourced elections, 2016 to 2024** (the same script, extended): dry run ready.
- **The silent-faults audit** (inventory/review/silent-faults-audit-2026-10-05.md): 29 findings; the empty-note template fault is fixed; the other visible ones wait on Nathan.
- **The Saugus High source records** (inventory/review/saugus-high-2019-sources-dry-run-2026-10-05.md): read, then apply create_saugus_2019_sources_2026_10_05.php. The 74 City vigil photographs wait for Nathan's look.
- **The St. Francis Dam** (inventory/review/st-francis-dam-dry-run-2026-10-05.md): read the record; the decisions at its foot (a dam place record first, Mulholland's 431, the Ruiz count, the Newhall Land report as a document).
- **Northridge:** rebuild plan in the report of 5 October; Stearns's mint date, a disagreement between his letter and the voucher, waits for the Reggie drive.
- **Reggie** dismounted on 5 October: reconnect it and restart DDEV (docs/DEPLOY-RUNBOOK.md).
- **The college district trustees' profiles** (inventory/review/coc-trustees-profiles-dry-run-2026-10-05.md, 33): read, then apply with build_coc_trustee_profiles_2026_10_05.php. Tichenor's closing paragraph and Johnson's Saugus runs are in the drafts; the Hoskinson and Lynch leads go to researchLeads, not the page.
- Notes to ourselves, the legacy-page wording, Mentry's source fault: applied 5 October.
- Sub-body marks: built 5 October (the parent's mark in the Parent organization box).
- **The Darren Harris interview:** the recording, its date, who asked, and his title then. It becomes a document record with a verbatim transcript, and Harris a person record with the Public Information Officer role (#30506).
- Johnson is Duzick: applied 5 October.
- Leads off the page, the sweep: applied 5 October.
- **Send the note to the college district** about Don Allen (inventory/review/coc-don-allen-note-2026-10-05.md).
- **Fallen officers:** read the fourteen (disabled; inventory/review/fallen-officers-draft-2026-10-05.md, or in the control panel), then enable them (re-run create_fallen_officers_2026_10_05.php with $ENABLE = true).
- **The Hart trustees dry run** (inventory/review/hart-trustees-profiles-dry-run-2026-10-05.md): read the 55 and the "For Nathan" items under each; then apply.
- **The nav** (inventory/review/nav-proposal-2026-10-05.md): six decisions; then the build, and the body pages' breadcrumb with it.
- **The district map key** on the Senate and Assembly: look, then the House.
- **The Sheriff** (inventory/review/lasd-contract-office-memorial-2026-10-05.md): whether to build an officers' memorial section (a schema plan first) and who counts; whether any sheriff gets a person record; the first contract's date needs the City Clerk's or the Board of Supervisors' records of December 1987 to 1988.
- **Hart before 1995** (inventory/review/hart-pre1995-terms-2026-10-05.md): Aliano's appointment as May 1994; Loberg and King ran and lost in 1993 (a "defeated" ending needs a new option); Warren's resignation, March or 6 April 1994.
- **The 51 people:** which 51 (inventory/review/people-without-profile-2026-10-05.md lists 167).
- **Two portraits may be generated, not edited:** Patti Rasmussen (#29122) and Brian Walters (#29118). Their files' content credentials record Firefly text_to_image steps. Keep them as edited photographs (with the edit recorded), or take them down. Nothing was changed.
- **Seven portraits with a Firefly edit now recorded** (Knight, Sharon Runner, George Runner, Messina, Jensen, Moore, Erin Wilson): who made the edit (enhancedBy is empty), and whether the unedited originals exist, to be held beside them under the new keep-both rule.
- **War memorial differences, shown on the records and not changed** (inventory/review/war-memorial-sourcing-2026-10-04.md): Cone is U.S. Navy, Seaman Second Class, missing August 10, 1943 by ABMC and the Navy's 1946 list (the record says Army, March 13, 1945; middle name Russel in both); ranks at death per the Defense Department (Sellen, Gelig, Acosta: Specialist or Private First Class against Sergeant or SP4); Acosta was 19, not 20; Suter's release gives Los Angeles; Todd appears as Spc. Dean Todd-Eckard of Canyon Country; Ross's ABMC date is September 30, 1944; Rubel reenlisted November 1942, a driver; Ball's draft registration was 1942, and his January 15, 1946 date has no source; Conant is not in the VA locator, so "Punchbowl" is unsupported. Kenaston: may the VA locator be used for him (a lead puts him at Los Angeles National Cemetery)?
- **The 490 date decisions:** review/dates.html, in Nathan's browser; no confirmed.json has been exported.
- **The photo form on the server:** the four Cloudways steps in docs/DEPLOY-RUNBOOK.md section 11, before it goes to staging.
- The Newhall Redevelopment Committee (#16290): how its terms read ("without term limits", 2002, or four-year terms, 2005). Its end is now March 1, 2012, from the chronology.
- Acton-Agua Dulce Unified (#29691): which high school district Acton and Agua Dulce left in 1993, and the County Committee's 2025 trustee-area resolution (inventory/review/aadusd-redevelopment-2026-10-04.md).

### Current (3 October 2026, evening)
- Seat boundary files (trustee areas, council districts, SCV Water divisions): Nathan is asking the Hart district and the City. Nothing is drawn until they are in hand.
- The research list of 10 first-win incumbents (inventory/review/board-holdings-dry-run-2026-10-03.txt, section 4), with the districts' online minutes archives as the first place to look.
- The next staging refresh carries what was applied after the 3 October refresh: the 19 no-source corrections, Couts, the California Battalion, Smyth's former portrait, the Hart 2022 Trustee Area 2 records.
- Done 3 October, evening: the three held dry runs, Smyth's former portrait, Jensen's certified 11,639 and the missing Hart 2022 Area 2 election and candidacies.

### Carried from the 18 September handoff (not rechecked since, unless marked)
- 6 community coordinates: fair-oaks-ranch, haskell-canyon, mint-canyon, potrero-canyon, ravenna, towsley-canyon
- 8 site page bodies; About and Permissions matter most
- 35 community write-ups
- 9 Find A Grave links
- Confirm the LA County GIS licence and add attribution before launch (now also needed for any seat boundaries taken from the County, 3 October)
- Story of Our Valley band has pseudo-text on the map; replacement requested from CD
- Perkins collection order: the Introduction should precede The Birth of Newhall
- Rudy Acosta's age at loss reads 20 on Leon's page; born May 2 1991, died March 19 2011, so he was 19. Worth telling Leon rather than changing silently.
- Research, not import: 20 articles with no publish date (Reynolds chapters carry no printed dateline), 19 war memorial narratives, 12 places with no establishment date, 15 casualties with no portrait.
- Done since, checked 3 October: the jerry-reynolds and dante-acosta bios (both full profiles now); RECORD-CHECKLIST.md already says legacyKey is required only if migrated.

### Older
Do not change the database until Nathan names the Place slugs to remove and whether to add missing community terms.

### What created the 52 empty titles

IDs 583 to 685 were created 2026-09-16 07:02:58 to 07:03:00 by `scripts/import/import_places_and_series.php` in local DDEV (commit `220809b`). That script set `$entry->title` from `places-candidates.json` and the hardcoded series list.

Craft still saved empty titles because both entry types have `hasTitleField: false` and `titleFormat: null` (`config/project/entryTypes/place--*.yaml`, `collection--*.yaml`). Craft 5 ignores `Entry->title` on save in that configuration. Same class of bug as the 2026-04-14 org titles (`titleFormat` / title field mismatch). Slugs, body, and ingest fields were stored; titles were not.

Fix when executing: enable the title field on Place and Collection (or set a real `titleFormat`), then write titles. Do not rely on `$entry->title` while `hasTitleField` is false.

Undo of the import itself: the 52 entries are only in local DDEV. A JSON snapshot of id, section, slug, and field values should be written before any delete so they can be recreated.

### 1. Convert 22 community Places to neighborhood categories

These Place slugs are communities, not sites:

acton, agua-dulce, bouquet-canyon, canyon-country, castaic, hasley-canyon, haskell-canyon, lebec, mentryville, mojave-desert, newhall, pico-canyon, piru, placerita-canyon, potrero-canyon, san-francisquito-canyon, saugus, soledad-canyon, tejon, towsley-canyon, val-verde, valencia

Neighborhood group `neighborhood` today:

| Slug | Term exists? |
| --- | --- |
| acton, agua-dulce, bouquet-canyon, canyon-country, castaic, hasley-canyon, lebec, newhall, pico-canyon, piru, placerita-canyon, san-francisquito-canyon, saugus, soledad-canyon, tejon, val-verde, valencia | yes |
| haskell-canyon | **missing** |
| mentryville | **missing** |
| mojave-desert | **missing** |
| potrero-canyon | **missing** |
| towsley-canyon | **missing** |

Do not add the five missing terms unless Nathan approves each one.

Relations on those Places now (move to Person/Org `neighborhood`, then clear Place relations):

| Place slug | Move |
| --- | --- |
| newhall | people: henry-mayo-newhall, arthur-b-perkins, jerry-reynolds, leon-worden, john-gifford → category `newhall` |
| saugus | people: henry-mayo-newhall → category `saugus` |
| placerita-canyon | people: francisco-lopez, abel-stearns → category `placerita-canyon` |
| pico-canyon | people: henry-clay-wiley, jerry-reynolds → category `pico-canyon`; drop relatedPlaces `mentryville` |
| mentryville | people: leon-worden. Cannot assign category until a `mentryville` term exists |

Keep existing Person-Org links (del Valle ranchos, Leon Worden → SCVHistory.com). Those are not Place records.

Then remove the 22 Place entries (hard delete only after snapshot). Community index 301s (`/scvhistory/acton.htm`) must not point at `/places/acton`. Target URL is still open.

**Undo:** restore from the pre-delete JSON snapshot (id, slug, title once fixed, body, legacyUrl, sourcePath, legacyCategory, neighborhood, placePeople, placeOrganizations, relatedPlaces). Re-save as Places. Re-apply placePeople from the snapshot. Category assignments on Persons can stay; they are correct even if the Place is restored.

### 2. Keep these 10 as Places: titles, SEO titles, community

Place entry type has no separate SEO title field. SEOmatic will use the entry title. Proposed title is the display name. Proposed SEO title is the same string until SEOmatic is configured.

| Slug | Proposed title | Proposed SEO title | Community (`neighborhood`) |
| --- | --- | --- | --- |
| vasquez-rocks | Vasquez Rocks | Vasquez Rocks | agua-dulce |
| beales-cut | Beale's Cut | Beale's Cut | newhall |
| ridge-route | Ridge Route | Ridge Route | castaic |
| saugus-speedway | Saugus Speedway | Saugus Speedway | saugus |
| melody-ranch | Melody Ranch | Melody Ranch | newhall |
| magic-mountain | Magic Mountain | Magic Mountain | valencia |
| harry-carey-ranch | Harry Carey Ranch | Harry Carey Ranch | saugus |
| heritage-junction | Heritage Junction | Heritage Junction | newhall |
| fort-tejon | Fort Tejon | Fort Tejon | tejon |
| estancia | Estancia | Estancia | valencia |

Ridge Route, Harry Carey Ranch, Estancia, and Melody Ranch sit near more than one community. Confirm before save.

Execution also requires turning `hasTitleField` on (or a non-empty titleFormat) so titles persist.

### 4. Collection titles from Jordy index pages

Copied from `<title>` or the visible series heading on the collection index. Not invented.

| Slug | Proposed title | Source |
| --- | --- | --- |
| perkins | SCVHistory.com \| The Story Of Our Valley by A.B. Perkins | `scvhistory/signal/perkins/index.html` `<title>` |
| reynolds | History of the Santa Clarita Valley by Jerry Reynolds | `scvhistory/signal/reynolds/index.html` `<title>` |
| worden | Selections From Leon Worden | `scvhistory/signal/worden/index.htm` `<title>` |
| boston | SCVHistory.com \| John Boston \| Santa Clarita History | `scvhistory/signal/boston/jbindex.htm` `<title>` |
| manzer | Darryl Manzer: 'Way Back When' in the Santa Clarita Valley | `scvhistory/signal/manzer/index.htm` `<title>` |
| newsmaker | SCV Newsmaker of the Week | Visible `<h2>` on `scvhistory/signal/newsmaker/index.htm`. The `<title>` is `SCVTV.com \| Local Television for Santa Clarita` (site chrome, not the series) |
| iraq | Abu Ghraib Prison Abuse Scandal Hits Home | `scvhistory/signal/iraq/index.htm` `<title>` |
| coins | NEEDS_TITLE | No index page under `scvhistory/signal/coins/` |
| otn-gazette | NEEDS_TITLE | `oldtownnewhall/index.htm` is a redirect with empty title. No `gazette/index.htm` |
| otn-patti | 'Open Book' - Santa Clarita Valley School Issues with Patti Rasmussen | `oldtownnewhall/patti/index.html` `<title>` |
| otn-pauline | Pauline Harte | `oldtownnewhall/pauline/index.htm` `<title>` |
| otn-rioux | Richard 'Doc' Rioux At Large | `oldtownnewhall/rioux/index.htm` `<title>` |
| otn-whyte | Black 'N' Whyte | `oldtownnewhall/whyte/index.html` `<title>` |

Same title-field bug as Places: titles will not stick until Collection `hasTitleField` is true.

## Deferred by decision

Parked on purpose, with the reason, so the next pass that opens the area finds them.

- **Rancho Camulos: merge the organization into the place by hand.** Organization #384 and place #631 hold two different bodies about the same rancho (the organization's 1,450 characters begin "Rancho Camulos is a historic rancho located along"; the place's begin "The del Valle family seat, and the westernmost") and two different images (#34 on the organization, #1195 on the place). convert_orgs_to_places.php carried everything else on 25 September and held the organization live. Nathan, 25 September: two bodies about one subject is a merge that needs a human read, not a script choice. When it is done, disable #384; the redirect goes in config/redirects.php.
- **Derived image links from the Walk of Western Stars.** Eleven photographs reach the SCV Chamber of Commerce #396 through derivedImageLinks, among them a Clint Walker lobby card (lw3689), Bob Hope in 'Alias Jesse James' and Montie Montana photographs, most likely because their captions name the Walk of Western Stars, which the Chamber ran. The records are not wrong; the derivation rule is. Revisit when the derived-links pass is next opened: an event the Chamber ran is not a link from every inductee's photograph to the Chamber.

## Open Questions

Leave these 7 Place entries untouched until Nathan decides:

- sleepy-valley
- lake-hughes
- santa-clarita
- lang
- rancho-san-francisco
- rancho-camulos
- tejon-ranch

Also still open: 301 target for community indexes; whether to add the five missing neighborhood terms; Ridge Route / Harry Carey Ranch / Estancia / Melody Ranch community assignment; coins and OTN Gazette collection titles.

## Deploy pipeline (blocking production)
- Updated 3 October 2026: deploy.yml was rewritten (commit 584a868) and is disabled. Its only trigger is workflow_dispatch and the job carries `if: false`, so pushing main deploys nothing. It now targets staging on templates-batch-9 and, when enabled, runs a backup, `composer install`, `craft up` and a cache clear (see its header).
- Staging is refreshed by hand: DEPLOY-RUNBOOK.md section 10. Check the server's branch (`git branch --show-current`) before choosing to push main or only the branch.
- To enable automatic deploys (Nathan only): add CLOUDWAYS_SSH_KEY, remove `if: false`, restore the push trigger, as the workflow header says.
- Production is still on the old build. Its first deploy must also copy web/uploads/archive-media/site (logos, seals, now tracked) and the Craft assets volume.

## Data gaps noted this session
- 20 communities have no polygon; coordinates come from set_community_coords.php (batch 4).
- Newhall, Saugus, Valencia, Canyon Country polygons are unincorporated fragments; sub-city boundaries task pending (city GIS, then ZCTA fallback).
- Community terms have no body, aliases, or type content yet.
- 12 places have no featured image until the legacy site images are pulled.
- GeoJSON licence marked NEEDS_VERIFICATION; confirm LA County GIS terms and add attribution before launch.
- militaryProfiles section has no template.
- Obituary body still carries WordPress artifacts; run clean_bodies.php again after adding obituaries to the field list.
