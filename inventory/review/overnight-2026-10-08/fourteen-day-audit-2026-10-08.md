# The fourteen-day audit, 8 October 2026

For Nathan (item 10): "Every record created or changed in the last fourteen days, checked against the rule set as it now stands. Not a diff of what we did, an audit of whether the current state is right. We changed rules several times and earlier work was done under earlier rules."

Claude, overnight, read only. Nothing was saved. The rules are those in rules-in-force-2026-10-08.md, beside this file.

## What it read

- **The records, not the files.** Craft entries, assets, categories, their fields and relations, and Craft revisions (for titles and portraits as they stood before). For every image rule that means a record about a file: enhancementMethod, contentCredentials, enhancedFrom, source, sourceChecksum, provenanceKind, licence, assetRole and filename. No image file was opened. Whether those records describe their files truly is the separate census in inventory/review/unchecked-claims-2026-10-08.md (6,909 of 6,958 assets have a claim resting on a record).
- **Files read**: templates/_data/banners.json; inventory/source-searches.json; every script in scripts/import, as text. web/banners does not exist any more, so it was read as empty.
- **Pages read**: the 20 person pages with an enhanced portrait, fetched from DDEV, for the JSON-LD and the edit label (below).
- **The checks already in the repo** run alone: check_census_reads, check_note_wording, check_checksums, check_removed_claims, check_pasted_labels, check_data_model, check_incoming, check_handoff, check_relation_status. check_render as a whole was not run (it renders every section and clears the template cache).
- The script: scripts/import/audit_fourteen_days_2026_10_08.php (it calls _reads.php first and passes check_census_reads). Its full output, every id, is storage/runtime/overnight-audit/fourteen-day-audit.json.

**Another agent was writing while this ran.** A new script (boston_portrait_2026_10_08.php) appeared at 23:06 and changed between two of my runs. The counts below did not move between runs, but they are a snapshot of about midnight.

## The universe

Every live element (not a draft, revision or in the trash) created or updated on or after 24 September 2026, midnight Pacific: **4038 entries** (4015 enabled), **6968 assets**, **2 categories**. Nearly every asset is in it (6,968 of 6,978): the whole library was saved at least once this fortnight, and the dates alone do not say why. By section: photographs 1561, articles 767, candidacies 539, officeHoldings 423, persons 246, elections 127, organizations 76, places 75, documents 62, warMemorials 54, affiliations 30, events 14, fallenOfficers 14, sourceFaults 11, collections 11, educations 10, obituaries 7, groups 7, roles 4.

The two categories (Lake Hughes, Mentryville) changed under no rule of this week but the namesake rule; Mentryville's naming note cites the 1900 eulogy, and passes.

## In short

- **The image rules.** No generated image is on any record (IMG-1, pass). Every portrait that fails the written Firefly-edit rule is one of the 17 you put back on 8 October, evening (IMG-2, 17 of 20): the data follows your later word, and the rule has not been rewritten to match. Plain faults: Wicks's and Kellar's new enlargement assets lack enhancedBy and enhancedDate (IMG-3, 2); two Firefly-edited marks on bodies record no original (IMG-4). Unverified: 94 outside or hand-supplied files made since 24 September have no content credential recorded, 17 of them tonight's originals and Boston's mug (IMG-7). Not settled: 52 outside images shown with licence unknown or empty (IMG-9).
- **Authorship basis.** Clean: 753 of 753 records with an author link have a basis, and every basis other than a printed byline says from what. Three Connie Worden-Roberts documents hold her as author and subject (AUTH-4).
- **Titles.** No document keeps the "(Author, Publication, Date)" suffix; no title is empty. 53 of the 54 articles retitled on the evening of 7 October have no legacyHeadline (the evening and night rules disagree on whether they need one). #2913's title ends in an HTML fragment ("Fort Tejon Camels</"); "Father" is still in Garcés's title.
- **Types.** Two moved articles (#2689, #865) lack heldAs. The type rule itself cannot be checked by query.
- **People.** Two alias faults (Adams's alias repeats his title; Rémi Nadeau shows his grandson's title as an alias). 90 office-only people to read against the 6 October rule, which DATA-MODEL contradicts.
- **Notes.** 51 no-source notes have no recorded search; 1,194 records still hold blank footnote rows (hidden); the Newhall Pass interchange holds three unconfirmed calendar rows built from a Wikipedia-sourced paste.
- **Scripts.** check_relation_status fails on two scripts, one of them tonight's restore; it is not in check_render.

## The result, rule by rule

| Rule | Applies | Pass | Fail |
|---|---|---|---|
| **IMG-1** No generated image of a real person, place or event on any record (DATA-MODEL, 8 October): assets whose recorded method, credential or source names a text prompt, text-to-image, Grok or xAI, related to no live record; web/banners and the registry empty | 10 | 10 | 0 |
| **IMG-2** The Firefly-edit rule (8 October, late): no portrait where Firefly filled, removed, cleaned or made an edit the credential does not describe, nor an edit with no original held; the enlargements of López, Wicks and Kellar stay | 20 | 3 | 17 |
| **IMG-3** The enhanced pair (DATA-MODEL, 6 October; called 5 October elsewhere): an edited portrait or featured image links its original by enhancedFrom, the original is among the record's related images, and who, when, how and the credential are recorded | 24 | 22 | 2 |
| **IMG-4** An edited image records what was done, who, when and the credential, and links its original or says none is held (DATA-MODEL, 1 and 4 October) | 56 | 44 | 12 |
| **IMG-5** A replaced portrait stays on the record as a related image (3 October), except an image the 8 October rules took off every field | 71 | 71 | 0 |
| **IMG-6** An asset made since 24 September records the SHA-256 of the file as received, in sourceChecksum (DATA-MODEL, Provenance) | 2668 | 2644 | 24 |
| **IMG-7** A file from outside or supplied by hand has its content credential read and recorded before it becomes anything (ERRORLOG, 8 October; scan_content_credentials.py) | 145 | 51 | 94 unverified |
| **IMG-8** A body's current mark is role current-mark, licence identifying-use, and never its featured image (DATA-MODEL, 3 October) | 19 | 19 | 0 |
| **IMG-9** An image from outside shown on a record has a licence recorded ("an empty licence is not permission", DATA-MODEL, Provenance and rights) | 88 | 36 | 52 to read (not settled) |
| **AUTH-1** Every record with an author link carries an authorship basis (Nathan, 7 October: "add a basis to authorship ... Set it on all") | 753 | 753 | 0 |
| **AUTH-2** A basis other than a printed byline says from what, in authorshipBasisNote (7 October) | 102 | 102 | 0 |
| **AUTH-3** No authorship basis stands without an author link | 753 | 753 | 0 |
| **AUTH-4** Author wins when someone is both author and subject (2 October) | 753 | 750 | 3 |
| **TTL-1** A document's title carries no "(Author, Publication, Date)" suffix (Nathan, 7 October: "Author, publication and date are fields") | 62 | 62 | 0 |
| **TTL-2** A photograph with a legacy page keeps Leon's whole catalogue entry in catalogueCaption (7 October, evening: "kept, every word, never discarded") | 1561 | 1549 | 12 |
| **TTL-3** An article or document retitled since 24 September that had a legacy page keeps Leon's page headline in legacyHeadline (7 October, night) | 54 | 1 | 53 |
| **TTL-4** No honorific or civic title in a person's title (DATA-MODEL, the name policy) | 246 | 245 | 1 |
| **TTL-5** No record has an empty title (ERRORLOG, open since 16 September) | 4038 | 4038 | 0 |
| **TTL-6** An agency belongs in fields, not after a colon in the title (Nathan, 7 October, on #26573) | 62 | 62 | 0 |
| **TYPE-1** An article holding photograph details (once filed as a photograph) records heldAs (7 October, evening) | 2 | 0 | 2 |
| **PER-1** No person record for someone with nothing but losing candidacies (DATA-MODEL, 1 October) | 246 | 246 | 0 |
| **PER-2** An office held alone is a row, not a record, unless an exception holds (PROFILES, 6 October) | 246 | not judged | 90 to read |
| **PER-3** A living person's birth date is cut to the year (2 October) | 246 | 246 | 0 |
| **PER-4** Shown aliases differ from the title (5 and 6 October: same-name forms go to search names) | 246 | 245 | 1 |
| **PER-5** A name form that is another person's name is neither alias nor search name (DATA-MODEL, 6 October) | 246 | 245 | 1 |
| **OFF-1** A term with no person linked names its holder in holderName (6 October) | 423 | 423 | 0 |
| **OFF-2** One rule for current: a term marked serving has no end date already passed (4 October) | 423 | 423 | 0 |
| **EV-1** A record about killing, violent injury or a suicide carries the one-line advisory as a top editor note (DATA-MODEL, 5 October) | 35 | 21 | 14 |
| **EV-2** Each consequence on an event quotes the source that makes the link (DATA-MODEL, 7 October) | 5 | 5 | 0 |
| **EV-3** Fallen officers stay disabled until Nathan has read them (5 October) | 14 | 14 | 0 |
| **NOTE-1** A note saying no source exists or a fact was not found has its search recorded in inventory/source-searches.json (PROFILES, 3 October) | 88 | 37 | 51 |
| **NOTE-2** Wikipedia is a lead, never the citation (memory, 4 October; 6 October profiles: Wikipedia-only facts left out) | 8 | 7 | 1 (#934, not public) |
| **NOTE-3** An ellipsis never joins two provisions (PROFILES, 5 October) | 5 | not judged | 5 to read |
| **NOTE-4** No row builder writes an empty footnote row (ERRORLOG, 5 October) | 4038 | 2844 | 1194 |
| **MISC-1** recordTags (a Categories field) relates categories only (ERRORLOG, 7 October: check a relation field's type before relating) | 4038 | 4038 | 0 |
| **MISC-2** A WordPress body that is not sourced stays withheld (3 October): body empty, text in withheldBody | 0 | 0 | 0 |
| **MISC-3** A document that does not say whose it is names its body in subjectOrganization (8 October, on the election documents) | 14 | 14 | 0 |
| **SCR-1** A fetch's User-Agent names the project, never a person (ERRORLOG and memory, 6 October) | 730 | 730 | 0 |

Read the failures with the notes. Some are the contradictions on the rules page showing up in the data (IMG-2, TTL-3, PER-2), some are lists to read rather than faults (PER-2, IMG-9, NOTE-1), and some are plain faults (IMG-3, IMG-4's two on records, TTL-2's #2913, TTL-4, PER-4, PER-5, TYPE-1).

## The failures, with ids

### IMG-1. No generated image of a real person, place or event on any record (DATA-MODEL, 8 October): assets whose recorded method, credential or source names a text prompt, text-to-image, Grok or xAI, related to no live record; web/banners and the registry empty

Applies to 10; pass 10; fail 0. Applies to the changed assets whose own records name a generator; an asset whose record is silent is not caught (the 3 to 6 October Firefly downloads were caught only by reading the files). Relations as enhancedFrom are not counted as use.

Ten assets record a generator; none is on any live record. web/banners no longer exists and the registry holds no entry.

### IMG-2. The Firefly-edit rule (8 October, late): no portrait where Firefly filled, removed, cleaned or made an edit the credential does not describe, nor an edit with no original held; the enlargements of López, Wicks and Kellar stay

Applies to 20; pass 3; fail 17. Applies to person records changed in the window whose portrait asset records any edit other than a plain crop. The rule as written, not as amended by the evening restore: the 17 are listed and marked.

All 17 failures are the 17 put back on your word on 8 October, evening. No portrait fails the rule outside them. The three that pass are López, Wicks and Kellar, the enlargements the rule keeps. So the state is right by your later word and wrong by the written rule: the contradiction, not a fault in the data. Frémont (#307) is listed with "enlarge" only, because his asset records only the upsampler; the side-by-side sheet showed his corners painted in (see "Read from a description").

- #30219 Kevin Lynch [persons] (asset: 31404 obituary_kevingarylynch-enhanced.jpg; edits: model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #29316 Keith Richman [persons] (asset: 31447 obituary_keithrichman-enhanced.jpg; edits: fill, model-edit-undescribed; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #21584 Demetrius G. Scofield [persons] (asset: 27383 demetrius-g-scofield-1911-edited.jpg; edits: fill, removal-or-clean, model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #20224 Sanford Lyon [persons] (asset: 31387 ap1334-enhanced.jpg; edits: model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #18726 George Pederson [persons] (asset: 31395 lw2529-enhanced.jpg; edits: model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #18702 Tom Mix [persons] (asset: 31408 lw2317a_large-enhanced.jpg; edits: model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #16432 William Mulholland [persons] (asset: 31391 lw2054-enhanced.jpg; edits: model-edit-undescribed; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #16140 Jo Anne Darcy [persons] (asset: 31449 sc9501-enhanced.jpg; edits: model-edit-undescribed; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #15919 Harry Carey [persons] (asset: 31472 lw2178-enhance.jpg; edits: model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #15808 Carl Boyer [persons] (asset: 31427 sc9010-enhanced.jpg; edits: model-edit-undescribed; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #15477 Ruth Newhall [persons] (asset: 31398 rn3002_large-enhanced.jpg; edits: model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #2585 Richard Rioux [persons] (asset: 31423 rr1-enhanced.jpg; edits: model-edit-undescribed; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #333 Arthur Buckingham Perkins [persons] (asset: 27381 arthur-b-perkins-outstanding-citizen-1964-edited.jpg; edits: removal-or-clean, model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #323 Cave Johnson Couts [persons] (asset: 27387 cave-johnson-couts-us-army-edited.png; edits: fill, removal-or-clean, model-edit-undescribed, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #321 William Lewis Manly [persons] (asset: 31406 william-lewis-manly-portrait-1890s-enhanced.jpg; edits: model-edit-undescribed; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #307 John C. Frémont [persons] (asset: 28814 john-c-fremont-california-state-library-upscaled.jpg; edits: enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)
- #279 Leon Worden [persons] (asset: 27396 leon-worden-edited.jpg; edits: fill, enlarge; enhancedBy: Nathan Imhoff; original: linked; status: one of the 17 put back on Nathan's word, 8 October evening)

### IMG-3. The enhanced pair (DATA-MODEL, 6 October; called 5 October elsewhere): an edited portrait or featured image links its original by enhancedFrom, the original is among the record's related images, and who, when, how and the credential are recorded

Applies to 24; pass 22; fail 2. Applies to every entry changed in the window whose featured image records an edit, crops included (crops follow the same keep-both rule of 4 October).

Wicks's and Kellar's enlargements (#38450, #38452, made their own assets on 8 October, continued) have no enhancedBy or enhancedDate, though each source sentence says "Enhanced by Nathan Imhoff in Adobe Firefly on October 6, 2026". The fields were not filled when the assets were split.

- #21944 Bob Kellar [persons] (asset: 38452 sc1310-enhanced.jpg; edits: enlarge; problems: enhancedBy empty; enhancedDate empty)
- #18663 Randy Wicks [persons] (asset: 38450 randywicks1995_karzinphoto_large-enhanced.jpg; edits: enlarge; problems: enhancedBy empty; enhancedDate empty)

### IMG-4. An edited image records what was done, who, when and the credential, and links its original or says none is held (DATA-MODEL, 1 and 4 October)

Applies to 56; pass 44; fail 12. Applies to every asset changed in the window whose record names an edit. Assets on no record are listed too: the rule is on the asset, not its use.

Two on records: the Wicks and Kellar enlargements (above). Two marks on records, the State Senate seal (#29400) and Canyon High's (#29298), carry a Firefly edit with no original linked and no word on whether one exists (marks are not ruled, but the keep-both rule of 4 October covers any edited image). The rest are on no record: the Hart district and Runner portraits, enhancedBy empty (TODO, 4 October, asks who made the edit), and Vasquez's generated upscale (#28816).

- #38452 sc1310-enhanced.jpg (used: on 1 relation(s); problems: enhancedBy empty; enhancedDate empty)
- #38450 randywicks1995_karzinphoto_large-enhanced.jpg (used: on 1 relation(s); problems: enhancedBy empty; enhancedDate empty)
- #29400 california-state-senate-seal.png (used: on 1 relation(s); problems: no original linked and the record does not say none is held)
- #29330 steve-knight.jpg (used: on no record; problems: enhancedBy empty)
- #29326 sharon-runner.jpg (used: on no record; problems: enhancedBy empty)
- #29298 canyon-high-school-logo.png (used: on 1 relation(s); problems: no original linked and the record does not say none is held)
- #29124 george-runner.jpg (used: on no record; problems: enhancedBy empty)
- #28978 joe-messina-hart-district.jpg (used: on no record; problems: enhancedBy empty)
- #28976 bob-jensen-hart-district.jpg (used: on no record; problems: enhancedBy empty)
- #28974 cherise-moore-hart-district.jpg (used: on no record; problems: enhancedBy empty)
- #28972 erin-wilson-hart-district.jpg (used: on no record; problems: enhancedBy empty)
- #28816 tiburcio-vasquez-1874-upscaled.jpg (used: on no record; problems: no original linked and the record does not say none is held)

### IMG-5. A replaced portrait stays on the record as a related image (3 October), except an image the 8 October rules took off every field

Applies to 71; pass 71; fail 0. Applies to entries changed in the window whose featured image differs from one it held at any revision since 24 September (or the last before). Read from Craft revisions.

Passes. 29 former portraits are off their records, and every one is an image the 8 October rules took off (exempt list below). The exemption is how the two rules were reconciled in practice; it is written in neither.

Taken off under the 8 October rules (exempt from the 3 October rule in practice):

- #31354 Tom Frew II [persons]: old portrait #31402 tf1000-enhanced.jpg (text-prompt)
- #29450 Audra Strickland [persons]: old portrait #31465 audra-strickland.jpg (an undisclosed Firefly file or an unsourced download)
- #29328 Steve Knight [persons]: old portrait #29330 steve-knight.jpg (model-edit-undescribed)
- #29324 Sharon Runner [persons]: old portrait #29326 sharon-runner.jpg (model-edit-undescribed)
- #29314 Pete Knight [persons]: old portrait #31414 pete-knight.jpg (text-prompt)
- #28703 Louis Brathwaite [persons]: old portrait #31445 brathwaite-louis-enhance.png (text-prompt, fill, model-edit-undescribed, enlarge)
- #28560 Erin Wilson [persons]: old portrait #28972 erin-wilson-hart-district.jpg (enlarge)
- #28558 Cherise Moore [persons]: old portrait #28974 cherise-moore-hart-district.jpg (enlarge)
- #28336 Jerry Gladbach [persons]: old portrait #31417 jerry-gladbach-portrait.jpg (an undisclosed Firefly file or an unsourced download)
- #28322 Bob Jensen [persons]: old portrait #28976 bob-jensen-hart-district.jpg (model-edit-undescribed)
- #28316 BJ Atkins [persons]: old portrait #31443 bj-atkins.jpg (an undisclosed Firefly file or an unsourced download)
- #26946 Bill Cooper [persons]: old portrait #27389 bill-cooper-edited.jpg (enlarge)
- #26549 Joe Messina [persons]: old portrait #28978 joe-messina-hart-district.jpg (model-edit-undescribed)
- #25391 Brian Walters [persons]: old portrait #29118 brian-walters.png (an undisclosed Firefly file or an unsourced download)
- #25191 Alan Ferdman [persons]: old portrait #27391 alan-ferdman-edited.jpg (fill, enlarge)
- #23093 Patsy Ayala [persons]: old portrait #27394 patsy-ayala-edited.jpg (enlarge)
- #23091 Jason Gibbs [persons]: old portrait #27400 jason-gibbs-edited.jpg (enlarge)
- #23089 Bill Miranda [persons]: old portrait #27402 bill-miranda-edited.jpg (enlarge)
- #23085 Marsha McLean [persons]: old portrait #27398 marsha-mclean-edited.jpg (enlarge)
- #20226 William Wirt Jenkins [persons]: old portrait #31389 ap2222_large_enhanced.jpg (text-prompt, enlarge)
- #18869 Remi Nadeau [persons]: old portrait #31425 reminadeau-chrisman-enhance.jpg (text-prompt, enlarge)
- #18747 George Runner [persons]: old portrait #29124 george-runner.jpg (model-edit-undescribed)
- #16439 Vincent Gelcich [persons]: old portrait #31393 lw2452-enhanced.jpg (text-prompt, fill, enlarge)
- #16418 Connie Worden [persons]: old portrait #31429 lw9501_large_enhanced.jpg (text-prompt, enlarge)
- #15874 Jill Klajic [persons]: old portrait #31451 sc9612-enhanced.jpg (text-prompt, fill)
- #2591 Patti Rasmussen [persons]: old portrait #29122 patti-rasmussen.jpg (an undisclosed Firefly file or an unsourced download)
- #331 Henry Clay Wiley [persons]: old portrait #1658 henry-clay-wiley-portrait.jpg (an undisclosed Firefly file or an unsourced download)
- #317 Andrés Pico [persons]: old portrait #27385 andres-pico-commons-edited.jpg (fill, model-edit-undescribed, enlarge)
- #285 Tiburcio Vasquez [persons]: old portrait #28816 tiburcio-vasquez-1874-upscaled.jpg (text-prompt, model-edit-undescribed, enlarge)

### IMG-6. An asset made since 24 September records the SHA-256 of the file as received, in sourceChecksum (DATA-MODEL, Provenance)

Applies to 2668; pass 2644; fail 24. Applies to assets created in the window, not those only updated.

Fifteen are election PDFs (County returns, cancelled-election lists and the nine from inventory/elections that git status shows untracked: the files a checksum would be taken from are on disk, not in git). The others are seven Mentry source scans of 25 September, the Santa Cruz Sentinel scan behind the Stearns document (#26982) and the Library of Congress Hart photograph (#21573).

- #26982 lp_santacruzsentinel082785.jpg (kind: (none); used: on a record)
- #26578 OER-4300-11082022.pdf (kind: (none); used: on a record)
- #26575 OER-4193-11032020.pdf (kind: (none); used: on a record)
- #26572 OER-3496-11082016.pdf (kind: (none); used: on a record)
- #25150 cancelled-elections-november-2024.pdf (kind: (none); used: on a record)
- #25149 11082022_final-list-of-cancelled-elections.pdf (kind: (none); used: on a record)
- #25148 11032020_cancelled-elections.pdf (kind: (none); used: on a record)
- #21941 LOCAL-APPT-LIST_-082625.pdf (kind: (none); used: on a record)
- #21938 historical-results-7.pdf (kind: (none); used: on a record)
- #21935 resolutionNo129-6.pdf (kind: (none); used: on a record)
- #21932 2014ElectionResultsbyPreci-5.pdf (kind: (none); used: on a record)
- #21929 2016StatementofVotesCast-4.pdf (kind: (none); used: on a record)
- #21926 LACountyFinalVoteCount-3.pdf (kind: (none); used: on a record)
- #21923 Final-Election-Canvass-Res-2.pdf (kind: (none); used: on a record)
- #21920 Final-Certficate-of-Canvas-1.pdf (kind: (none); used: on a record)
- #21917 10880.pdf (kind: (none); used: on a record)
- #21573 william-s-hart-loc-cph-3c03842.jpg (kind: outside; used: on a record)
- #20106 lp_lat031754.jpg (kind: legacy-mirror; used: on a record)
- #20095 lp_warrenpatimesmirror020431.jpg (kind: legacy-mirror; used: on a record)
- #20092 sw_lat031799.jpg (kind: legacy-mirror; used: on a record)
- #20089 sw_herald031799.jpg (kind: legacy-mirror; used: on a record)
- #20086 sw_herald111186.jpg (kind: legacy-mirror; used: on a record)
- #20083 ch1040.jpg (kind: legacy-mirror; used: on a record)
- #20080 ch1030a.jpg (kind: legacy-mirror; used: on a record)

### IMG-7. A file from outside or supplied by hand has its content credential read and recorded before it becomes anything (ERRORLOG, 8 October; scan_content_credentials.py)

Applies to 145; pass 51; fail 94. Partly checkable: an empty contentCredentials can mean the scan found nothing as well as that no scan ran; the scan writes no "none found" note. So a listed asset is unverified, not proved unread.

The largest single group is tonight's: the 16 of the 19 originals put back on 8 October, evening, that came from outside (#38460 to #38490), and John Boston's mug (#38525), none with a content credential recorded. Under the lesson ERRORLOG wrote the same day, a supplied or downloaded file is read for its credential before it becomes anything. An empty field cannot show whether the scan ran and found nothing, so these are unverified, not proved unread. The cheapest check: run scan_content_credentials.py over inventory/incoming/done/originals-2026-10-08.

- #38525 boston-john-scvhistory-mug.jpg (kind: outside; used: on a record)
- #38490 brian-walters-scvnews-2013.jpg (kind: outside; used: on a record)
- #38488 patti-rasmussen-city.jpg (kind: outside; used: on a record)
- #38486 jerry-gladbach-acwa-2022.jpg (kind: outside; used: on a record)
- #38484 bj-atkins-scvnews-2012.jpg (kind: outside; used: on a record)
- #38482 audra-strickland-assembly.jpg (kind: outside; used: on a record)
- #38480 erin-wilson-hart-2023.jpg (kind: outside; used: on a record)
- #38478 cherise-moore-hart.jpg (kind: outside; used: on a record)
- #38476 joe-messina-hart-2016.jpg (kind: outside; used: on a record)
- #38474 bob-jensen-hart-2016.jpg (kind: outside; used: on a record)
- #38472 sharon-runner-assembly-2007.jpg (kind: outside; used: on a record)
- #38470 george-runner-boe-2011.jpg (kind: outside; used: on a record)
- #38468 steve-knight-congress-2015.jpg (kind: outside; used: on a record)
- #38466 alan-ferdman-khts-2020.jpg (kind: outside; used: on a record)
- #38464 bill-cooper-campaign-2026.png (kind: outside; used: on a record)
- #38462 patsy-ayala-city-2024.png (kind: outside; used: on a record)
- #38460 jason-gibbs-city-2023.png (kind: outside; used: on a record)
- #37328 lw2724b.jpg (kind: outside; used: on a record)
- #37327 lw2724a.jpg (kind: outside; used: on a record)
- #31938 john-boston-cropped-from-sg030506b-honby.jpg (kind: outside; used: on a record)
- #31935 tom-frew-iv-cropped-from-hs9019.jpg (kind: outside; used: on a record)
- #31932 michele-jenkins-cropped-from-co1501c.jpg (kind: outside; used: on a record)
- #31925 fran-pavley-commons.jpg (kind: outside; used: on a record)
- #31754 cephas-l-bard-commons.jpg (kind: outside; used: on no record)
- #31752 phineas-banning-commons.jpg (kind: outside; used: on a record)
- #31750 mike-garcia-commons.jpg (kind: outside; used: on a record)
- #31748 george-whitesides-commons.jpg (kind: outside; used: on a record)
- #31746 tom-mcclintock-commons.jpg (kind: outside; used: on a record)
- #31744 jeff-gorell-commons.jpg (kind: outside; used: on a record)
- #31742 cathie-wright-commons.jpg (kind: outside; used: on a record)
- #31740 henry-stern-commons.jpg (kind: outside; used: on a record)
- #31738 bill-thomas-commons.jpg (kind: outside; used: on a record)
- #31736 kevin-mccarthy-commons.jpg (kind: outside; used: on a record)
- #31465 audra-strickland.jpg (kind: outside; used: on no record)
- #31443 bj-atkins.jpg (kind: outside; used: on no record)
- #31417 jerry-gladbach-portrait.jpg (kind: outside; used: on no record)
- #30544 sharlene-rose-johnson.jpg (kind: outside; used: on a record)
- #29698 newhall-elementary-school-logo-original.jpg (kind: outside; used: on no record)
- #29694 santa-clarita-christian-school-logo.png (kind: outside; used: on a record)
- #29673 valencia-water-company-logo-2008.png (kind: outside; used: on a record)
- #29671 castaic-lake-water-agency-logo-2008.png (kind: outside; used: on a record)
- #29669 newhall-county-water-district-logo-2008.png (kind: outside; used: on a record)
- #29560 tom-lackey.jpg (kind: outside; used: on a record)
- #29552 suzette-martinez-valladares.jpg (kind: outside; used: on a record)
- #29442 pilar-schiavo.jpg (kind: outside; used: on a record)
- #29398 california-state-assembly-seal.webp (kind: outside; used: on a record)
- #29396 united-states-congress-seal.svg (kind: outside; used: on a record)
- #29306 lasd-star.png (kind: outside; used: on a record)
- #29304 city-of-santa-clarita-seal.svg (kind: outside; used: on a record)
- #29302 los-angeles-county-seal.svg (kind: outside; used: on a record)
- #29300 csun-seal.png (kind: outside; used: on a record)
- #29296 hart-high-school-logo.svg (kind: outside; used: on a record)
- #29294 castaic-high-school-logo.png (kind: outside; used: on a record)
- #29128 jeri-seratti-city-arts-commission.jpg (kind: outside; used: on a record)
- #29126 susan-shapiro-city-arts-commission.jpg (kind: outside; used: on a record)
- #29122 patti-rasmussen.jpg (kind: outside; used: on no record)
- #29120 anna-griese-schlickart-2022.jpg (kind: outside; used: on a record)
- #29118 brian-walters.png (kind: outside; used: on no record)
- #28980 michael-vierra-hart-district.jpg (kind: outside; used: on a record)
- #28280 cw9901.pdf (kind: (none); used: on a record)
- #28261 cameron-smyth-2017.jpg (kind: outside; used: on a record)
- #28210 christy-smith-assembly-portrait-2018.jpg (kind: outside; used: on a record)
- #28197 chris-trunkey-campaign-image.jpg (kind: outside; used: on a record)
- #28162 santa-clarita-history-center-logo.png (kind: outside; used: on a record)
- #28160 scv-water-logo.png (kind: outside; used: on a record)
- #28158 castaic-union-school-district-logo.png (kind: outside; used: on a record)
- #28156 sulphur-springs-union-school-district-logo.png (kind: outside; used: on a record)
- #28154 saugus-union-school-district-logo.png (kind: outside; used: on a record)
- #28152 newhall-school-district-logo.png (kind: outside; used: on no record)
- #28150 hart-district-logo.png (kind: outside; used: on a record)
- #27350 aakash-ahuja-campaign-image.jpg (kind: outside; used: on a record)
- #26982 lp_santacruzsentinel082785.jpg (kind: (none); used: on a record)
- #26976 buck-mckeon-official-portrait-2011.jpg (kind: outside; used: on a record)
- #26578 OER-4300-11082022.pdf (kind: (none); used: on a record)
- #26575 OER-4193-11032020.pdf (kind: (none); used: on a record)
- #26572 OER-3496-11082016.pdf (kind: (none); used: on a record)
- #25150 cancelled-elections-november-2024.pdf (kind: (none); used: on a record)
- #25149 11082022_final-list-of-cancelled-elections.pdf (kind: (none); used: on a record)
- #25148 11032020_cancelled-elections.pdf (kind: (none); used: on a record)
- #21941 LOCAL-APPT-LIST_-082625.pdf (kind: (none); used: on a record)
- #21938 historical-results-7.pdf (kind: (none); used: on a record)
- #21935 resolutionNo129-6.pdf (kind: (none); used: on a record)
- #21932 2014ElectionResultsbyPreci-5.pdf (kind: (none); used: on a record)
- #21929 2016StatementofVotesCast-4.pdf (kind: (none); used: on a record)
- #21926 LACountyFinalVoteCount-3.pdf (kind: (none); used: on a record)
- #21923 Final-Election-Canvass-Res-2.pdf (kind: (none); used: on a record)
- #21920 Final-Certficate-of-Canvas-1.pdf (kind: (none); used: on a record)
- #21917 10880.pdf (kind: (none); used: on a record)
- #21863 csun-oviatt-library-commons.jpg (kind: outside; used: on a record)
- #21861 santa-clarita-city-hall-2008-flickr.jpg (kind: outside; used: on a record)
- #21761 demetrius-g-scofield-commons-1911.jpg (kind: outside; used: on a record)
- #21581 maria-gutzeit-campaign-avatar.png (kind: outside; used: on a record)
- #21579 cameron-smyth-nathan-imhoff.jpg (kind: outside; used: on a record)
- #21573 william-s-hart-loc-cph-3c03842.jpg (kind: outside; used: on a record)

### IMG-8. A body's current mark is role current-mark, licence identifying-use, and never its featured image (DATA-MODEL, 3 October)

Applies to 19; pass 19; fail 0. Marks edited with Firefly are listed separately: not ruled.

Passes. Four marks on bodies carry a Firefly edit: the State Senate (#29400), Canyon High (#29298), Newhall School District (#29439), Newhall Elementary (#29696). Not ruled.

- #28273 California State Senate [organizations]: asset #29400 california-state-senate-seal.png
- #21779 Canyon High School [organizations]: asset #29298 canyon-high-school-logo.png
- #21590 Newhall School District [organizations]: asset #29439 newhall-school-district-logo-2700.png
- #15958 Newhall Elementary School [organizations]: asset #29696 newhall-elementary-school-logo.png

### IMG-9. An image from outside shown on a record has a licence recorded ("an empty licence is not permission", DATA-MODEL, Provenance and rights)

Applies to 88; pass 36; fail 52. Not a settled failure: the rule says an empty licence is not permission; it does not say such a file may not be shown. Listed for Nathan.

45 have licence "unknown" and no rights holder. 18 are tonight's (the outside originals and Boston's mug), 17 are the enhanced portraits (an enhanced file carries no licence of its own), and the rest are October's Commons, district and campaign portraits. The rule ("an empty licence is not permission") does not say whether such a file may be shown. Yours to rule.

- #38525 boston-john-scvhistory-mug.jpg (license: unknown; rightsHolder: empty)
- #38490 brian-walters-scvnews-2013.jpg (license: unknown; rightsHolder: empty)
- #38488 patti-rasmussen-city.jpg (license: unknown; rightsHolder: empty)
- #38486 jerry-gladbach-acwa-2022.jpg (license: unknown; rightsHolder: empty)
- #38484 bj-atkins-scvnews-2012.jpg (license: unknown; rightsHolder: empty)
- #38482 audra-strickland-assembly.jpg (license: unknown; rightsHolder: empty)
- #38480 erin-wilson-hart-2023.jpg (license: unknown; rightsHolder: empty)
- #38478 cherise-moore-hart.jpg (license: unknown; rightsHolder: empty)
- #38476 joe-messina-hart-2016.jpg (license: unknown; rightsHolder: empty)
- #38474 bob-jensen-hart-2016.jpg (license: unknown; rightsHolder: empty)
- #38472 sharon-runner-assembly-2007.jpg (license: unknown; rightsHolder: empty)
- #38470 george-runner-boe-2011.jpg (license: unknown; rightsHolder: empty)
- #38466 alan-ferdman-khts-2020.jpg (license: unknown; rightsHolder: empty)
- #38464 bill-cooper-campaign-2026.png (license: unknown; rightsHolder: empty)
- #38462 patsy-ayala-city-2024.png (license: unknown; rightsHolder: empty)
- #38460 jason-gibbs-city-2023.png (license: unknown; rightsHolder: empty)
- #38452 sc1310-enhanced.jpg (license: empty; rightsHolder: empty)
- #38450 randywicks1995_karzinphoto_large-enhanced.jpg (license: unknown; rightsHolder: empty)
- #37328 lw2724b.jpg (license: empty; rightsHolder: empty)
- #37327 lw2724a.jpg (license: empty; rightsHolder: empty)
- #31938 john-boston-cropped-from-sg030506b-honby.jpg (license: unknown; rightsHolder: empty)
- #31935 tom-frew-iv-cropped-from-hs9019.jpg (license: unknown; rightsHolder: empty)
- #31932 michele-jenkins-cropped-from-co1501c.jpg (license: unknown; rightsHolder: empty)
- #31744 jeff-gorell-commons.jpg (license: unknown; rightsHolder: empty)
- #31742 cathie-wright-commons.jpg (license: unknown; rightsHolder: empty)
- #31740 henry-stern-commons.jpg (license: unknown; rightsHolder: Senate Rules Committee, by the file's own notice ("Senate Rules (c)2016"))
- #31474 us8502_orig-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31472 lw2178-enhance.jpg (license: unknown; rightsHolder: empty)
- #31449 sc9501-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31447 obituary_keithrichman-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31427 sc9010-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31423 rr1-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31408 lw2317a_large-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31406 william-lewis-manly-portrait-1890s-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31404 obituary_kevingarylynch-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31398 rn3002_large-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31395 lw2529-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31391 lw2054-enhanced.jpg (license: unknown; rightsHolder: empty)
- #31387 ap1334-enhanced.jpg (license: unknown; rightsHolder: empty)
- #30544 sharlene-rose-johnson.jpg (license: unknown; rightsHolder: empty)
- #29560 tom-lackey.jpg (license: unknown; rightsHolder: empty)
- #29552 suzette-martinez-valladares.jpg (license: unknown; rightsHolder: empty)
- #29442 pilar-schiavo.jpg (license: unknown; rightsHolder: empty)
- #29128 jeri-seratti-city-arts-commission.jpg (license: unknown; rightsHolder: empty)
- #29126 susan-shapiro-city-arts-commission.jpg (license: unknown; rightsHolder: empty)
- #29120 anna-griese-schlickart-2022.jpg (license: unknown; rightsHolder: Lindsay Schlick (SchlickArt))
- #28980 michael-vierra-hart-district.jpg (license: unknown; rightsHolder: empty)
- #28261 cameron-smyth-2017.jpg (license: unknown; rightsHolder: empty)
- #28210 christy-smith-assembly-portrait-2018.jpg (license: unknown; rightsHolder: Jeff Walters, by the file's own notice)
- #28197 chris-trunkey-campaign-image.jpg (license: unknown; rightsHolder: Jennifer Emery)
- #27350 aakash-ahuja-campaign-image.jpg (license: unknown; rightsHolder: empty)
- #21581 maria-gutzeit-campaign-avatar.png (license: unknown; rightsHolder: empty)

### AUTH-4. Author wins when someone is both author and subject (2 October)

Applies to 753; pass 750; fail 3. A person in both writtenBy and subjectPerson on one record is listed; the rule may be read as allowing both on an autobiography, so read before acting.

Three of Connie Worden-Roberts's own pieces (#28281, #28285, #28287) name her as both author and subject. "Author wins" (2 October) says she should be author only; a memoir may be a fair exception. Yours.

- #28287 City Formation Committee Member Connie Worden-Roberts Remembers, 2007 [documents] (person: 16418)
- #28285 "A Look Back in Time": proposal for the Historical Society's first annual fund raiser, by Connie Worden-Roberts, 2003 [documents] (person: 16418)
- #28281 A Brief History of the Push for Self-Government in Santa Clarita [documents] (person: 16418)

### TTL-2. A photograph with a legacy page keeps Leon's whole catalogue entry in catalogueCaption (7 October, evening: "kept, every word, never discarded")

Applies to 1561; pass 1549; fail 12. The 20 left as they were on 7 October (7 not on Reggie, 4 multi-piece, 9 no headline) are expected among any listed.

Four are the records whose images are lost (#5735, #5737, #5739, #4475). #2913's title is "Fort Tejon Camels</": an HTML tag fragment in a title.

- #5739 Cronan Home Site on Via Onda, Valencia Hills [photographs]
- #5737 Cronan Home Site on Via Onda, Valencia Hills [photographs]
- #5735 Cronan Home on Via Onda, Valencia Hills [photographs]
- #5671 Interrupted Mail: Letter (Envelope) Recovered from Fatal Plane Crash, 11-18-1930. [photographs]
- #5377 Souvenir Program: Baker Ranch Rodeo, Under Direction of Hoot Gibson, 4-27-1930. [photographs]
- #4861 City Formation Committee Kicks Off Voter Registration Program to Get More Funds From State, 12-1-1987. [photographs]
- #4859 Arthur Young CPAs Predict 22% Budget Windfall for Proposed City of Santa Clarita, 9-18-1987. [photographs]
- #4791 Interrupted Mail: Letter (Envelope) Recovered from Fatal Plane Crash, 11-18-1930. [photographs]
- #4475 Apartments Under Construction, 24514 Kansas Street (Ex-Newhall School Building Site), 2015. [photographs]
- #3023 Photo Gallery: 1876 Golden Spike. [photographs]
- #2987 Photo Gallery: Sandberg's Summit Hotel Site, 2006. [photographs]
- #2913 Fort Tejon Camels</ [photographs]

### TTL-3. An article or document retitled since 24 September that had a legacy page keeps Leon's page headline in legacyHeadline (7 October, night)

Applies to 54; pass 1; fail 53. Read from Craft revisions: the title in the last revision before 24 September against the title now. The rule was made on 7 October, night; the 54 articles retitled that evening under the earlier rule are in this set, and for them a missing legacyHeadline may be what the earlier rule left, not a fault of the later.

These are the 54 articles retitled on 7 October, evening, from an extraction file's title to "the printed headline". The night rule keeps Leon's page headline in legacyHeadline; for these, the old title was usually the series index's link text, not Leon's page headline, so the field may simply never have been filled, not lost. Whether the night rule reaches back to them is the evening and night contradiction (rules page, 10).

- #12945 Curious timing of "Candle in the Wind" release [articles] (was: Elton John: Profiting on "Candle in the Wind" release?)
- #12943 Local actress: 'Norma Rae' of porn industry? [articles] (was: Local actress: 'Norma Rae' of the porn industry?)
- #12889 A long time ago, in a newsroom far, far away... [articles] (was: It's official: I'm a Star Wars geek)
- #12856 DENIAL impedes recovery from addiction [articles] (was: DENIAL impedes recovery from drug, alcohol addiction)
- #12781 Help! Columnist under attack from computers! [articles] (was: Help! Columnist under attack by computers!)
- #12779 Ritalin isn't always the best cure for misbehavior [articles] (was: Ritalin not always best cure for misbehavior)
- #12777 Cousteau sailed the Calypso into the conscience of mankind [articles] (was: Jacques Cousteau: Spokesman for those with no voice)
- #12775 July 4th parade did us proud [articles] (was: Fourth of July Parade did us proud)
- #12767 Skateboard park not necessarily a great idea [articles] (was: Most skateboarders aren't hoodlums)
- #12647 Shaping The Old Town Inside And Out [articles] (was: Shaping Newhall Inside and Out.)
- #12629 What Next For Veterans Historical Plaza? [articles] (was: What Next For Veterans Memorial Plaza?)
- #12603 Old Town Newhall: From Dream To Reality [articles] (was: Editorial: From Dream To Reality.)
- #12585 Redevelopment Bucks Are In The Bank. Now What? [articles] (was: Editorial: Redevelopment Bucks Are)
- #12581 City Council Takes Step To Preserve Historic Buildings [articles] (was: Editorial: City Council Takes Step)
- #12579 In Newhall Time, Change Is Happening Fast [articles] (was: Editorial: In Newhall Time,)
- #12577 Newhall Community Center Under Construction [articles] (was: Community Center To Open.)
- #12570 'Front Runner' is off and running [articles] (was: 36th Assembly District Nominee George Runner)
- #12568 New bilingual bill offers hope [articles] (was: Firestone Bilingual Education Bill)
- #12566 Move over, Washington, the states are coming! [articles] (was: Mint to produce 50 new quarters under House bill)
- #12564 S'Claritans like their trash service? [articles] (was: A look at the City's latest poll)
- #12562 Hirohito at root of SCV growth [articles] (was: Postwar growth in the Santa Clarita Valley)
- #12560 Inside look at a bizarre election [articles] (was: Americans to the rescue: Reviving Boris Yeltsin)
- #12556 Goodnight, Richard, my dear friend [articles] (was: Goodnight, Richard Rioux, my dear friend)
- #12554 Rising local star headed for Broadway [articles] (was: Matt Gould: Rising star headed for Broadway)
- #12550 Big party marks Newhall Hardware's 50th birthday [articles] (was: Newhall Hardware celebrates 50th anniversary)
- #12548 Parents irked over talk of drug testing [articles] (was: High school district considers drug testing for athletes)
- #12546 Lots on tap for kids this weekend [articles] (was: Harvest festival, Halloween haunts on tap this weekend)
- #12542 Historic photos surface after decades [articles] (was: Historic A.B. Perkins photos resurface)
- #12540 Revitalizing Newhall: An 80-year saga [articles] (was: Revitalizating Newhall: An 80-Year Saga)
- #12538 Big party marks Newhall Hardware's 50th birthday [articles] (was: Big Party Marks Newhall Hardware's 50th Anniversary)
- #12536 The proud, the mighty, the Indians of Hart High School [articles] (was: Why the Hart High 'Indians'?)
- #12528 The earth groaned as its bowels moved [articles] (was: Northridge Earthquake: a day in the life)
- #12526 Which candidates care about us? [articles] (was: 1996 Santa Clarita City Council race)
- #12524 1.1 billion reasons to vote April 9 [articles] (was: 1996 Santa Clarita City Council race)
- #12522 Menendez outcome wrong, juror says [articles] (was: Interview with a dissenting Menendez juror)
- #12520 Homeless thespians overtaking Newhall? [articles] (was: Creating a Theater District in Old Town Newhall)
- #12518 Desert living was never cheap or easy [articles] (was: Story of the Castaic Lake Water Agency)
- #12516 Help design tomorrow's city, tonight [articles] (was: Architectural Guidelines for Santa Clarita?)
- #12514 Gimme that old Frontier dirt anytime! [articles] (was: Frontier Days 1996)
- #12512 Worry about your own darned back yard [articles] (was: Crime isn't particular to one community)
- #12510 'A Perfect Affair' predictably unpredictable [articles] (was: MOVIE REVIEW: 'A Perfect Affair' holds few surprises)
- #12508 Sour grapes from crybaby election loser [articles] (was: Plambeck complaint is 'sour grapes')
- #12506 'Small Soldiers' a small victory for DreamWorks [articles] (was: MOVIE REVIEW: 'Small Soldiers' a small victory)
- #12504 Dismantling bilingual education is no simple task [articles] (was: Dismantling bilingual ed. is no simple task)
- #12502 SC Repertory makes much ado about authenticity [articles] (was: STAGE REVIEW: 'Rep' makes much ado about authenticity)
- #12500 Signs point to trouble in November 1999 [articles] (was: Signs point to trouble in Nov., 1999)
- #12498 Artistry in motion: Rigby is Pan-tastic at Pantages [articles] (was: STAGE REVIEW: Rigby is Pan-tastic at the Pantages)
- #12496 A real commitment to 'fixing up Newhall' [articles] (was: 'Fixing up Newhall' in wake of drive-by)
- #2179 90 Years Beneath the Cross [articles] (was: First Presbyterian Church)
- #1454 42nd Annual Newhall Old West July 4th Celebration [articles] (was: Newhall Fourth of July Parade History)
- #1452 Phone Business Office Has Seen Local History [articles] (was: Pardee House Has Seen Local History)
- #1436 Story of Little Santa Clara Valley [articles] (was: Manuscript: Colonization (~1940s))
- #849 Diseño ~1843 [articles] (was: Diseño Map of Rancho San Francisco, c. 1843)

### TTL-4. No honorific or civic title in a person's title (DATA-MODEL, the name policy)

Applies to 246; pass 245; fail 1. 

"Father Francisco Garcés": the name policy strips Father.

- #289 Father Francisco Garcés [persons]

### TYPE-1. An article holding photograph details (once filed as a photograph) records heldAs (7 October, evening)

Applies to 2; pass 0; fail 2. The type rule itself ("type follows the thing") cannot be checked by query; see the report.

#2689 (lw030597, moved to articles on 7 October) and #865 (lw2304a, a map, kept as an article by "keep the lower id").

- #2689 Story of Sulphur Springs School [articles]
- #865 Surveyor's Map Showing Lyon's Station [articles]

### PER-2. An office held alone is a row, not a record, unless an exception holds (PROFILES, 6 October)

Applies to 246; pass 156; fail 90. Only a candidate list: persons whose only inbound links are office holdings and candidacies and who have no body, portrait or other link. Whether "sources exist to write from", or whether they resigned, died in office, held higher office or sat on a first board, cannot be read by query. DATA-MODEL still says an office holding alone keeps a record: see the contradictions.

A list to read, not 90 failures. Most are people this week deliberately kept: the 33 college trustees of 5 October (restored or created with profiles drafted), the legislature's members (higher office) and the City's council. What the query cannot see is "sources exist to write from". DATA-MODEL would keep all 90 on its own wording (rules page, contradiction 9).

- #30261 Fred Arnold [persons]
- #30259 Darlene Trevino [persons]
- #30257 Carlos R. Guerrero [persons]
- #30255 Jerry K. Danielsen [persons]
- #30251 Sebastian C. M. Cazares [persons]
- #30249 Edel Alonso [persons]
- #30245 Michael D. Berger [persons]
- #30243 Ernest L. Tichenor [persons]
- #30241 Ronald E. Gillis [persons]
- #30239 Joan W. MacGregor [persons]
- #30237 Ernest Moreno [persons]
- #30235 John D. Hoskinson [persons]
- #30231 Mark A. Posner [persons]
- #30229 Richard G. Peoples [persons]
- #30227 Linda C. Cubbage [persons]
- #30225 Patricia R. Steele [persons]
- #30221 William J. Broyles [persons]
- #30217 James E. Rentz [persons]
- #30215 Louis J. Reiter [persons]
- #30213 Don Allen [persons]
- #30209 John K. Hackney [persons]
- #30207 Peter F. Huntsinger [persons]
- #30205 Bruce D. Fortine [persons]
- #30203 Sheila Dyer [persons]
- #30201 Edward Muhl [persons]
- #30199 William G. Bonelli Jr. [persons]
- #29454 Steve Fox [persons]
- #29322 Don Rogers [persons]
- #28866 Michael Owen Lambarth [persons]
- #28864 Stacy Dobbs [persons]
- #28723 Robert Hall [persons]
- #28721 Chris Fall [persons]
- #28719 Peter Warren [persons]
- #28717 William Dinsenbacher [persons]
- #28715 Sandra Loberg [persons]
- #28711 Robert Keysor [persons]
- #28709 Gerald Heidt [persons]
- #28707 James Putjenter [persons]
- #28705 Jim Shuman [persons]
- #28701 Sheldon Allen [persons]
- #28699 Patrick Shaughnessy [persons]
- #28697 Kenneth Wullschleger [persons]
- #28695 Robert Crozier [persons]
- #28693 Ruth Kelley [persons]
- #28691 S. A. Wright [persons]
- #28689 Carroll Word [persons]
- #28687 Thomas Hanson [persons]
- #28685 David Holden [persons]
- #28683 Emmett Carraher [persons]
- #28681 Edward Duarte [persons]
- #28679 Elisha Agajanian [persons]
- #28673 C. R. Huntsinger [persons]
- #28671 W. D. Ross [persons]
- #28669 Edith Palmer [persons]
- #28665 Ernest Malam [persons]
- #28663 Howard Gulley [persons]
- #28661 Julio Lombardi [persons]
- #28659 Charleton Hadley [persons]
- #28657 Howard Blackwell [persons]
- #28655 Chester Allen [persons]
- #28653 Walter Cook [persons]
- #28651 C. L. Dillenbeck [persons]
- #28649 Mildred Gilmour [persons]
- #28647 Thomas M. Frew Jr. [persons]
- #28645 S. S. Donaldson [persons]
- #28643 Charles Brown [persons]
- #28641 Mary Bonelli [persons]
- #28566 John Hassel [persons]
- #28564 James Webb [persons]
- #28562 George Aliano [persons]
- #28376 Victor Torres [persons]
- #28372 Stephen Winkler [persons]
- #28366 RJ Kelly [persons]
- #28362 Michael Kennick [persons]
- #28354 Lester Freeman [persons]
- #28346 Julie Olsen [persons]
- #28342 John Michael McGrath [persons]
- #28326 Dan Masnada [persons]
- #28320 Bill Pecsi [persons]
- #26587 Gary Martin [persons]
- #26540 Tom Caesar [persons]
- #25441 Steven Sturgeon [persons]
- #25439 Dennis King [persons]
- #25437 Patricia Hanrion [persons]
- #25431 Kerry Clegg [persons]
- #25415 Laura Arrowsmith [persons]
- #25403 Douglas Bryce [persons]
- #25397 Rosemarie Koscielny [persons]
- #25387 Michael Shapiro [persons]
- #25383 Ron Winkler [persons]

### PER-4. Shown aliases differ from the title (5 and 6 October: same-name forms go to search names)

Applies to 246; pass 245; fail 1. 

Adrian W. Adams carries his own title as a shown alias.

- #28667 Adrian W. Adams [persons] (alias: Adrian W. Adams; why: same as the title)

### PER-5. A name form that is another person's name is neither alias nor search name (DATA-MODEL, 6 October)

Applies to 246; pass 245; fail 1. Exact match on another person record's title only.

Rémi Nadeau (#339, the freighter) shows "Remi Nadeau" as an alias, the title of his grandson's record (#18869): the namesake trap the rule names.

- #339 Rémi Nadeau [persons] (alias: Remi Nadeau (shown alias); why: another person's title, #18869)

### EV-1. A record about killing, violent injury or a suicide carries the one-line advisory as a top editor note (DATA-MODEL, 5 October)

Applies to 35; pass 21; fail 14. Applies to fallen officers and to events changed in the window whose title names a shooting, killing, standoff, siege, incident, dam, crash or flight, with their source records. Whether each concerns killing or injury is a reading; an air crash or the dam may or may not be meant (the rule names the dam, the Newhall Incident and the Kuredjian standoff).

All 14 failures are the fallen officers, all disabled and waiting on your reading; Kuredjian, whom the rule names, is among them. The events that pass include the Saugus High shooting and its sources and the Newhall Incident.

- #29764 Deputy Constable J. Edward "Ed" Brown [fallenOfficers, disabled]
- #29760 Constable McCoy Pyle [fallenOfficers, disabled]
- #29780 Officer Clarence Wayne Dean [fallenOfficers, disabled]
- #29778 Deputy Hagop "Jake" Kuredjian [fallenOfficers, disabled]
- #29786 Officer Matthew Pavelka [fallenOfficers, disabled]
- #29784 Deputy David W. March [fallenOfficers, disabled]
- #29782 Deputy Shayne D. York [fallenOfficers, disabled]
- #29776 Deputy Arthur E. Pelino [fallenOfficers, disabled]
- #29774 Officer George M. Alleyn [fallenOfficers, disabled]
- #29772 Officer James E. Pence Jr. [fallenOfficers, disabled]
- #29770 Officer Roger D. Gore [fallenOfficers, disabled]
- #29768 Officer Walter C. Frago [fallenOfficers, disabled]
- #29766 Constable John S. "Jack" Pilcher [fallenOfficers, disabled]
- #29762 Deputy Constable Charles A. De Moranville [fallenOfficers, disabled]

### EV-3. Fallen officers stay disabled until Nathan has read them (5 October)

Applies to 14; pass 14; fail 0. A state rule: it lapses when Nathan reads them; TODO still lists the reading as open.

All 14 still disabled.

### NOTE-1. A note saying no source exists or a fact was not found has its search recorded in inventory/source-searches.json (PROFILES, 3 October)

Applies to 88; pass 37; fail 51. Matched by wording; a note phrased otherwise is missed, and a record listed in the file under another key reads as a failure. Counted per note.

Matched by wording, so read with care: some are quotations or notes that say a source was not found and name where they looked, in text, rather than in the file. Every one still lacks an entry in inventory/source-searches.json, which is what the rule asks for. 37 such notes do have one.

- #31904 Great Flood of 1938 [events] (where: editor note; text: d to March 4. The record gives March 2, Leon Worden's date, and February 27 as the start. No source on the mirror gives the day the flood ended in this valley,)
- #31904 Great Flood of 1938 [events] (where: editor note; text: d 62 the next day. None of these sources reports a death in the Santa Clarita Valley, and none was found on the mirror. Buildings lost: 5,601 destroyed (Leon Worden)
- #31893 Sylmar Earthquake [events] (where: footnote; text: earthquake (actually centered in Iron Canyon section of Sand Canyon)." The timeline names no source for the line.)
- #31891 Powerhouse Fire [events] (where: editor note; text: No source in the archive reports a death in the fire. The pages of th)
- #31374 Golden Spike at Lang Station [events] (where: footnote; text: n." "October 18: Southern Pacific begins subdividing town of Newhall." The timeline names no source for these lines and may rest on Reynolds.)
- #31370 The Placerita Gold Discovery [events] (where: footnote; text: fied copy is of "Voucher No. 350." The mint itself had answered on October 5, 1891, that "no record of such a deposit from 1841 to 1844 can be found here." The ar)
- #31359 Santa Clarita Cityhood [events] (where: editor note; text: es in the City's table of annexations, as cited on record #394, which notes that 39.5 has not been found in an official source.)
- #30537 deposited the 8th day of July, 1843 (the memorandum as Robinson copied it) to 8 July or 8 June 1843: not decided [sourceFaults] (where: footnote; text: r No. 150 [sic]"; L.W. Reid's certificate: "Voucher No. 350"; the mint, October 5, 1891: "no record of such a deposit from 1841 to 1844 can be found here.")
- #30383 Scott Thomas Wilk Sr.: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 2007 election was not found; his election then is unsourced. CEDA marks him the incumbe)
- #30381 Scott Thomas Wilk Sr.: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: No source here gives the date of his appointment or the 2007 election)
- #30371 Ronald E. Gillis: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1999 election was not found, so whether he was elected or seated unopposed is not known)
- #30369 Ronald E. Gillis: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1999 election was not found. He held the seat after it and stood as the incumbent in 20)
- #30353 Joan W. MacGregor: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1993 election was not found, nor the date she took her seat. That it was Seat No. 3 is)
- #30339 Michele R. Jenkins: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 2007 election was not found, so whether she was elected or seated unopposed is not know)
- #30337 Michele R. Jenkins: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 2007 election was not found. She held the seat after it and stood as the incumbent in 2)
- #30335 Michele R. Jenkins: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1999 election was not found, so whether she was elected or seated unopposed is not know)
- #30333 Michele R. Jenkins: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1999 election was not found. She held the seat after it and stood as the incumbent in 2)
- #30331 Michele R. Jenkins: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: 980s; neither is preferred here. How she joined, and the elections of 1987 and 1991, were not found.)
- #30327 Richard G. Peoples: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: own. The board history first lists him for 1984-1985; the return of the 1983 election was not found.)
- #30325 Linda C. Cubbage: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The returns of the 1985, 1989 and 1993 elections were not found. She was on the board through 1993, so it is read here that)
- #30319 Donald M. Benton: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: he 1981 count is Carl Boyer's account of the canvass. The return of the 1985 election was not found; his re-election rests on Boyer and the White House release)
- #30317 William J. Broyles: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: oard history does not list him between 1984 and 1987. The return of the 1987 election was not found, so how he returned is not known.)
- #30315 William J. Broyles: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The returns of the 1979 and 1983 elections were not found; the result of each is Carl Boyer's. The number of his seat)
- #30313 Kevin Lynch: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1977 election was not found. The 1981 count is Carl Boyer's account of the canvass.)
- #30311 James E. Rentz: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the election of March 4, 1975 was not found, and the continuation of The Canyon Call's report naming th)
- #30309 Louis J. Reiter: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The returns of the elections of 1975 and 1979 were not found. He was on the board after each, so it is read here that he)
- #30307 Carl Boyer: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The count is Carl Boyer's own account of the canvass; the County's return was not found.)
- #30305 Carl Boyer: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The full return was not found; The Canyon Call gives his vote and the number of ballots. No source says whose seat the election filled, and the date he took h)
- #30305 Carl Boyer: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1977 election was not found.)
- #30303 Don Allen: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the spring 1973 election was not found, and its exact date is not given; The Canyon Call reported)
- #30301 Francis T. Claffey: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The returns of the elections of 1975 and 1979 were not found. Whether he stood in 1979 is not known; he was not on the b)
- #30299 Francis T. Claffey: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the election of March 4, 1975 was not found. He stood as an incumbent and sat on the board that followe)
- #30295 Peter F. Huntsinger: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The returns of the elections of 1975 and 1979 were not found. He was on the board after each, so it is read here that he)
- #30293 Peter F. Huntsinger: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The 1971 return was not found; the date and result are the college history's.)
- #30293 Peter F. Huntsinger: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the election of March 4, 1975 was not found. He stood as an incumbent and sat on the board that followe)
- #30287 Bruce D. Fortine: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 2007 election was not found, so whether he was elected or seated unopposed is not known)
- #30285 Bruce D. Fortine: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 2007 election was not found. He held the seat after it and stood as the incumbent in 20)
- #30283 Bruce D. Fortine: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1999 election was not found, so whether he was elected or seated unopposed is not known)
- #30281 Bruce D. Fortine: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the 1999 election was not found. He held the seat after it and stood as the incumbent in 20)
- #30279 Bruce D. Fortine: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: y lists him again from 1992; the return of the 1991 election, the likeliest occasion, was not found. CEDA marks him the incumbent for Seat No. 4 in 1995.)
- #30277 Bruce D. Fortine: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the spring 1973 election was not found, and its exact date is not given; The Canyon Call reported)
- #30277 Bruce D. Fortine: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: No source here gives the date or manner of his leaving. Carl Boyer, a)
- #30271 Edward Muhl: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The 1971 return was not found; the date and result are the college history's.)
- #30271 Edward Muhl: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The return of the election of March 4, 1975 was not found. He stood as an incumbent and was not on the board that fol)
- #30267 William G. Bonelli Jr.: College Trustee, Santa Clarita Community College District [officeHoldings] (where: editor note; text: The 1971 return was not found; the date and result are the college history's.)
- #29659 Lynne Plambeck: Water Board Director, Newhall County Water District [officeHoldings] (where: footnote; text: pervisors as the supervising authority; the district's principal act is not checked here. No source held gives the month the term began or ended, so its years)
- #28531 Nora Emmons: School Board Member, Castaic Union School District [officeHoldings] (where: footnote; text: Nora Emmons stood as the incumbent. How Nora Emmons first joined the board is not known: no source here records an appointment, and the County's returns for t)
- #28511 Sheldon Wigdor: School Board Member, Sulphur Springs Union School District [officeHoldings] (where: footnote; text: on Wigdor stood as the incumbent. How Sheldon Wigdor first joined the board is not known: no source here records an appointment, and the County's returns for t)
- #28481 Julie Olsen: School Board Member, Saugus Union School District [officeHoldings] (where: footnote; text: Julie Olsen stood as the incumbent. How Julie Olsen first joined the board is not known: no source here records an appointment, and the County's returns for t)
- #28469 Steven Tannehill: School Board Member, Newhall School District [officeHoldings] (where: footnote; text: nnehill stood as the incumbent. How Steven Tannehill first joined the board is not known: no source here records an appointment, and the County's returns for t)
- #28217 Lynne Plambeck: Water Board Director, Newhall County Water District [officeHoldings] (where: footnote; text: in 2011, when she was appointed to the next one without an election (see the next term). No source held gives the month the term changed, so the boundary is w)

### NOTE-2. Wikipedia is a lead, never the citation (memory, 4 October; 6 October profiles: Wikipedia-only facts left out)

Applies to 8; pass 8; fail 0. Every record changed in the window whose text names Wikipedia outside a link, read by eye in the report; none is failed by the script.

Passes on reading, with one exception not yet public. Of the 8 records whose text names Wikipedia outside a link: two say Wikipedia and other accounts are wrong and give the sources (California Star Oil Works, Scofield), three say "Wikipedia was not consulted" (Sylmar, United 34, Western Air 7), two are leads in researchLeads (Couts, Cameron Smyth). One is not public but should not stand: the Newhall Pass interchange (#934) holds three unconfirmed On This Day rows (1971, 1971, 1973) whose context text is a chat tool's paste with "Wikipedia" and "Metroprimaryresources" citation chips in it. Confirmed, they would put Wikipedia-sourced dates on the calendar.

- #323 Cave Johnson Couts [persons]: "rce has been found for the burial place.","col3":"bottom"}],"03036e6a-4aab-49cb-b747-ae9816b258b6":"Wikipedia reports that he was tried on several charges, including murder, and acquitted. "
- #934 Newhall Pass interchange [places]: " interchange was still under construction when the February 9, 1971 San Fernando earthquake struck. Wikipedia A total collapse of the southbound I-5 to northbound SR","col5":false,"col6":nu // :null},{"col1":"in 1973","c"
- #16039 California Star Oil Works [organizations]: " often given for the incorporation, 16 June 1876, has not been found in a source of the time.[2]\n\nWikipedia and other popular accounts say that Demetrius G. Scofield founded the company a // ept it as a subsidiary.[12]"
- #16380 Cameron Smyth [persons]: "6e6a-4aab-49cb-b747-ae9816b258b6":"The exact dates of his council and Assembly terms appear only in Wikipedia. Settled by the City canvasses and the Assembly journal. (From the editor's not"
- #21584 Demetrius G. Scofield [persons]: " near Newhall in 1875; the papers of the time do not place him in the oil field before 1877.[15]\n\nWikipedia and other later accounts, including the note on the eulogy's legacy page, say t"
- #31893 Sylmar Earthquake [events]: ", as no note names them. LW2547a (#4089), the Doobie Brothers cover of 1973, is listed and held.\n\nWikipedia was not consulted.","0ed7085d-1523-4592-9a1f-2a0d29c7ef35":"February 9, 1971",""
- #31907 United Air Lines Flight 34 Crash in Rice Canyon [events]: "mirror. The other \"Rice Canyon\" hits are oil and mining pages about the canyon, not the crash.\n\nWikipedia was not consulted.","0ed7085d-1523-4592-9a1f-2a0d29c7ef35":"December 27, 1936","
- #31914 Western Air Express Flight 7 Crash [events]: "eau of Air Commerce, Los Pinetos, the Santa Clara Divide and the Olive View Sanitarium: no records. Wikipedia was not consulted.","0ed7085d-1523-4592-9a1f-2a0d29c7ef35":"January 12, 1937",""

### NOTE-3. An ellipsis never joins two provisions (PROFILES, 5 October)

Applies to 5; pass 5; fail 0. Cannot be judged by query. Quotations of statutes or ordinances with an ellipsis, on records changed in the window, listed for reading.

Five quotations, read in full. Four (#30363, #30389, #30395, #30397) use the ellipsis to join the columns of one row of a County candidate list (office ... name ... status): one entry, not two provisions, though the rule as written does not cover tables. #394 quotes Municipal Code section 2.04.005(B) as "In 2024 ... District 1 and District 3.": inside one subdivision, but it may join two sentences of it, and the splice audit of 5 October already listed #394 as risky (TODO). Not judged here; it needs the ordinance read beside it.

- #30397 Steven D. Zimmer: College Trustee, Santa Clarita Community College District [officeHoldings]
- #30395 Steven D. Zimmer: College Trustee, Santa Clarita Community College District [officeHoldings]
- #30389 Michael D. Berger: College Trustee, Santa Clarita Community College District [officeHoldings]
- #30363 Joan W. MacGregor: College Trustee, Santa Clarita Community College District [officeHoldings]
- #394 The City of Santa Clarita [organizations]

### NOTE-4. No row builder writes an empty footnote row (ERRORLOG, 5 October)

Applies to 4038; pass 2844; fail 1194. The template filters them, so none shows; the rule says not to write them.

1,194 records in the window hold at least one blank footnote row. The template has hidden them since 5 October, so no reader sees them; the rule is about writing them, and nothing has cleared the old ones. Ids in the JSON.

Ids: 38521, 38519, 38517, 38515, 38513, 38511, 32726, 32724, 32721, 32719, 32717, 32715, 32713, 31723, 31412, 31336, 31334, 31332, 31326, 31314, 31312, 31308, 31306, 30520, 30518, 30263, 30261, 30259, 30257, 30255, 30251, 30249, 30247, 30245, 30243, 30241, 30239, 30237, 30235, 30233, 30231, 30229, 30227, 30225, 30223, 30221, 30219, 30217, 30215, 30213, 30211, 30209, 30207, 30205, 30203, 30201, 30199, 29679, 29676, 29601, 29599, 29454, 29448, 29444, 29406, 29404, 29402, 29393, 29322, 29292, 29282, 29279, 29090, 29088, 29004, 29002, 29000, 28998, 28996, 28866, 28864, 28723, 28721, 28719, 28717, 28715, 28713, 28711, 28709, 28707, 28705, 28703, 28701, 28699, 28697, 28695, 28693, 28691, 28689, 28687, 28685, 28683, 28681, 28679, 28677, 28675, 28673, 28671, 28669, 28665, 28663, 28661, 28659, 28657, 28655, 28653, 28651, 28649, 28647, 28645, 28643, 28641, 28566, 28564, 28562, 28560, 28376, 28372, 28366, 28364, 28362, 28354, 28346, 28342, 28326, 28320, 28314, 28310, 28307, 28305, 28303, 28301, 28299, 28297, 28293, 28291, 28289, 28287, 28285, 28283, 28281, 28275, 28273, 28271, 28269, 28201, 28079, 28066, 28063, 28060, 28057, 28055, 28053, 28051, 28049, 28047, 28045, 27887, 27883, 27879, 27870, 27866, 27862, 27858, 27853, 27414, 27406, 27368, 27366, 26983, 26966, 26701, 26699, 26695, 26693, 26691, 26687, 26685, 26683, 26679, 26677, 26673, 26671, 26667, 26665, 26663, 26659, 26657, 26655, 26651, 26649, 26647, 26645, 26641, 26639, 26637, 26635, 26631, 26629, 26627, 26625, 26621, 26619, 26615, 26613, 26609, 26607, 26603, 26601, 26597, 26589, 26587, 26583, 26581, 26579, 26576, 26573, 26570, 26568, 26566, 26549, 26540, 25445, 25441, 25439, 25437, 25431, 25427, 25425, 25415, 25411, 25403, 25397, 25387, 25385, 25383, 25369, 25367, 25365, 25363, 25361, 25359, 25357, 25355, 25353, 25351, 25349, 25347, 25345, 25343, 25341, 25339, 25337, 25335, 25333, 25331, 25329, 25327, 25325, 25323, 25321, 25319, 25317, 25315, 25157, 25155, 25153, 25151, 25146, 22416, 22414, 22412, 22410, 22408, 22406, 22404, 22402, 22398, 22396, 22394, 22392, 22390, 22388, 22386, 22384, 22382, 22378, 22376, 22374, 22372, 22370, 22368, 22366, 22364, 22362, 22358, 22356, 22354, 22352, 22350, 22348, 22346, 22344, 22342, 22340, 22338, 22336, 22334, 22332, 22330, 22326, 22324, 22322, 22320, 22318, 22316, 22314, 22312, 22310, 22308, 22306, 22298, 22290, 22286, 22282, 22280, 22274, 22272, 22270, 22268, 22266, 22262, 22260, 22258, 22256, 22254, 22252, 22250, 22248, 22246, 22244, 22242, 22238, 22236, 22234, 22232, 22230, 22226, 22224, 22222, 22220, 22218, 22216, 22214, 22212, 22210, 22208, 22206, 22202, 22200, 22198, 22194, 22192, 22190, 22188, 22186, 22184, 22182, 22180, 22178, 22176, 22174, 22172, 22168, 22166, 22164, 22162, 22160, 22158, 22156, 22154, 22152, 22150, 22148, 22144, 22142, 22140, 22138, 22136, 22134, 22132, 22130, 22128, 22126, 22124, 22122, 22120, 22118, 22116, 22112, 22110, 22108, 22106, 22104, 22102, 22100, 22098, 22096, 22094, 22092, 22090, 22088, 22084, 22082, 22080, 22078, 22076, 22074, 22072, 22070, 22068, 22066, 22064, 22062, 22060, 22056, 22054, 22052, 22050, 22048, 22046, 22044, 22042, 22040, 22038, 22036, 22034, 22032, 22030, 22028, 22026, 22022, 22020, 22018, 22016, 22014, 22012, 22010, 22008, 22006, 22004, 22000, 21998, 21996, 21994, 21992, 21990, 21988, 21986, 21984, 21982, 21980, 21978, 21976, 21974, 21972, 21970, 21968, 21966, 21964, 21962, 21960, 21958, 21956, 21954, 21952, 21950, 21942, 21939, 21936, 21933, 21930, 21927, 21924, 21921, 21918, 20170, 20152, 20118, 20109, 20107, 20104, 20102, 20099, 20096, 20093, 20090, 20087, 20084, 20081, 18991, 18862, 18855, 18813, 18799, 18783, 18519, 16404, 16388, 16372, 16364, 16339, 16248, 16217, 16184, 16163, 16101, 15949, 15939, 15897, 15862, 15823, 15691, 15651, 15508, 15493, 15461, 15454, 15452, 15450, 15448, 15446, 15444, 15442, 15440, 15438, 15436, 15434, 15432, 15430, 15428, 15426, 15424, 15422, 15420, 15418, 15416, 15414, 15412, 15410, 15408, 15406, 15404, 15402, 15400, 15398, 15396, 15394, 15392, 15390, 15388, 15386, 15384, 15382, 15380, 15378, 15376, 15374, 15372, 15370, 15368, 15366, 15364, 15362, 15360, 15358, 15356, 15354, 15352, 15350, 15348, 15346, 15344, 15342, 15340, 15338, 15336, 15334, 15332, 15330, 15328, 15326, 15324, 15322, 15320, 15318, 15316, 15314, 15312, 15310, 15308, 15306, 15304, 15302, 15300, 15298, 15296, 15294, 15292, 15290, 15288, 15286, 15284, 15282, 15280, 15278, 15276, 15274, 15272, 15270, 15268, 15266, 15264, 15262, 15260, 15258, 15256, 15254, 15252, 15250, 15248, 15246, 15244, 15242, 15240, 15238, 15236, 15234, 15232, 15230, 15228, 15226, 15224, 15222, 15220, 15218, 15216, 15214, 15212, 15210, 15208, 15206, 15204, 15202, 15200, 15198, 15196, 15194, 15192, 15190, 15188, 15186, 15184, 15182, 15180, 15178, 15176, 15174, 15172, 15170, 15168, 15166, 15164, 15162, 15160, 15158, 15156, 15154, 15152, 15150, 15148, 15146, 15144, 15142, 15140, 15138, 15136, 15134, 15132, 15130, 15128, 15126, 15124, 15122, 15120, 15118, 15116, 15114, 15112, 15110, 15108, 15106, 15104, 15102, 15100, 15098, 15096, 15094, 15092, 15090, 15088, 15086, 15084, 15082, 15080, 15078, 15076, 15074, 15072, 15070, 15068, 15066, 15064, 15062, 15060, 15058, 15056, 15054, 15052, 15050, 15048, 15046, 15044, 15042, 15040, 15038, 15036, 15034, 15032, 15030, 15028, 15026, 15024, 15022, 15020, 15018, 15016, 15014, 15012, 15010, 15008, 15006, 15004, 15002, 15000, 14998, 14996, 14994, 14992, 14990, 14988, 14986, 14984, 14982, 14980, 14978, 14976, 14974, 14972, 14970, 14968, 14966, 14964, 14962, 14960, 14958, 14956, 14954, 14952, 14950, 14948, 14946, 14944, 14942, 14940, 14938, 12945, 12943, 12941, 12939, 12937, 12935, 12933, 12931, 12929, 12927, 12925, 12923, 12921, 12919, 12917, 12915, 12913, 12911, 12909, 12907, 12905, 12903, 12901, 12899, 12897, 12895, 12893, 12891, 12889, 12887, 12885, 12883, 12881, 12879, 12877, 12875, 12873, 12870, 12868, 12866, 12864, 12862, 12860, 12858, 12856, 12854, 12852, 12850, 12848, 12846, 12844, 12842, 12840, 12838, 12836, 12834, 12832, 12830, 12828, 12826, 12824, 12822, 12820, 12818, 12816, 12814, 12812, 12810, 12808, 12806, 12804, 12802, 12800, 12798, 12796, 12794, 12792, 12790, 12788, 12786, 12784, 12781, 12779, 12777, 12775, 12773, 12771, 12769, 12767, 12765, 12763, 12761, 12759, 12757, 12755, 12753, 12751, 12749, 12747, 12745, 12743, 12741, 12739, 12737, 12735, 12733, 12731, 12729, 12727, 12725, 12723, 12721, 12719, 12717, 12715, 12713, 12711, 12709, 12707, 12702, 12700, 12698, 12696, 12694, 12692, 12690, 12688, 12686, 12684, 12682, 12680, 12678, 12676, 12674, 12672, 12670, 12668, 12666, 12664, 12662, 12660, 12658, 12656, 12654, 12652, 12649, 12647, 12645, 12643, 12641, 12639, 12629, 12627, 12623, 12621, 12619, 12617, 12613, 12609, 12607, 12605, 12603, 12601, 12599, 12595, 12593, 12589, 12585, 12581, 12579, 12577, 31962, 12574, 12572, 12570, 12568, 12566, 12564, 12562, 12560, 12556, 12554, 12552, 12550, 12548, 12546, 12544, 12542, 12540, 12538, 12536, 12532, 12530, 12528, 12526, 12524, 12522, 12520, 12518, 12516, 12514, 12512, 12510, 12508, 12506, 12504, 12502, 12500, 12498, 12496, 12494, 12492, 12490, 12488, 12486, 12484, 12482, 12480, 12478, 12476, 12474, 12472, 12470, 12468, 12466, 12464, 12462, 12460, 12458, 12456, 12454, 12452, 12450, 12448, 12446, 12444, 12442, 12440, 12438, 12436, 12434, 12432, 12430, 12428, 12426, 12424, 12422, 12420, 12418, 12416, 12414, 12412, 12410, 12408, 12406, 12404, 12402, 12400, 12398, 12396, 12394, 12392, 12390, 12388, 12386, 12384, 12382, 12380, 12378, 12376, 12374, 12372, 12370, 12368, 12366, 12364, 12362, 12360, 12358, 12356, 12354, 12352, 12350, 12348, 12346, 12344, 12342, 12340, 12338, 12336, 12334, 12332, 12330, 12328, 12326, 12324, 12322, 12320, 12318, 12316, 12314, 12312, 12310, 12308, 12306, 12304, 12302, 12300, 12298, 12296, 12294, 12292, 12290, 12288, 12286, 12284, 12282, 12280, 12278, 12276, 12274, 12272, 12270, 12268, 12266, 12264, 12262, 12260, 12258, 12256, 12254, 12252, 12250, 12248, 12246, 12244, 12242, 12240, 12238, 12236, 12234, 12232, 12230, 12228, 12226, 12224, 12222, 12220, 12218, 12216, 12214, 12212, 12210, 12208, 12206, 12204, 12202, 12200, 12198, 12196, 12194, 12192, 12190, 12188, 12186, 12184, 12182, 12180, 12178, 12176, 12174, 12172, 12170, 12168, 12166, 12164, 12162, 12160, 12158, 12156, 12154, 12152, 12150, 12148, 12146, 12144, 12142, 12140, 12138, 31330, 31328, 31324, 31322, 31320, 31318, 31316, 31310, 32711

### MISC-2. A WordPress body that is not sourced stays withheld (3 October): body empty, text in withheldBody

Applies to 0; pass 0; fail 0. Records outside persons; a person's unsourced WordPress body is withheld by the template from bodyAuthorship, which this audit does not render.

Nothing outside persons in the window carries the WordPress flag; five persons do (Wilk, Manly, Bryant, de Anza, Garcés), and their bodies are withheld by the template, which this audit does not render.

## Rules that cannot be checked by query, and why

- **Type follows the thing** (7 October). Only the scan or the page says what a thing is. The retype read (39 photographs, 25 documents) is the check, and it waits on you ("Stop before the retype"). TYPE-1 checks only that moved articles say how they are held.
- **Printed matter takes what the scan prints** (7 October, night). Only the scan says what it prints. Batches 3 and 4 (37 ephemera) and the 20 photographs left as they were are unread.
- **No connection the sources do not make** (5 October). Whether a source makes a link is in the source's words, which no field records.
- **Wikipedia-only facts left out; a profile leads with what the person did here; the connection is the person's own.** A fact's basis is not stored fact by fact, and what a profile leads with is a reading.
- **Who keeps a record** (6 October). Resigned, died in office or removed could be read from howEnded, but "held a higher office", "sat on a first board", "something named for them" and above all "sources exist to write from" cannot. PER-2 is a list to read for that reason.
- **No generated image, and the Firefly-edit rule, where the record is silent.** A query sees only what an asset's record says. The five undisclosed Firefly portraits of 3 to 6 October were found only by reading the files. The check is the credential scan over the files, not over the fields.
- **"No edit recorded" is not "no edit"** (the 99 portraits). Same reason: the files must be read.
- **A supplied file is read before it becomes anything.** It is a rule about when something was done, and an empty contentCredentials field cannot say whether a scan ran (IMG-7 is unverified, not failed).
- **The census-reads rule for scripts before 8 October.** reads-baseline.txt exempts 650 scripts. Most of this fortnight's counts came from them, and nothing about a stored count says what was read to make it.
- **Scope on figures.** No figure rows exist yet. The figures in prose (Northridge's 57 dead and $13 to $50 billion in eventSignificance, the dam's dead, the 1938 flood's 113 to 115) are not structured, so a query cannot say whether each states its scope.
- **Crime out of the disaster comparison.** The comparison is not built.
- **An ellipsis never joins two provisions.** Only the source shows what an ellipsis dropped (NOTE-3 lists the five to read).
- **Elected unopposed or appointed.** Whether a term was elected unopposed or appointed in lieu depends on the statute and the County's list for that year. OFF-2 gives the counts of each method only.
- **Never rewrite Leon Worden's prose.** This could be checked by comparing each legacy-leon body with its revision of 24 September and with its page on Reggie. It was not done tonight.
- **Notes are for a reader** (3 October). check_note_wording catches the known phrasings and passes; a new phrasing of the same thing is invisible to it. The 17 restored assets' source sentences now narrate the 8 October removal and restore ("Taken off its record on 8 October 2026 under a rule on Firefly edits, and put back the same day on Nathan's word"), which the check does not catch.

## Other checks run tonight

- **The repo's own checks, run alone.** check_census_reads: pass, 22 scripts (boston_portrait_2026_10_08.php failed three ways at 23:06 and passed on a later run: its author fixed it while this ran). check_note_wording: pass. check_checksums: pass (4,280 match their master; 2 name a master not in the manifests; 126 are downloads with no master). check_removed_claims: pass. check_pasted_labels: pass. check_data_model: pass (337 handles). check_incoming: pass (48 files waiting, each with a reason). check_handoff: pass. **check_relation_status: fails** on fix_scott_newhall_2026_10_06.php line 298 and restore_enhanced_pairs_2026_10_08.php line 25 (a relation read without status(null) and then written back, which can drop a disabled target). It is not part of check_render, so commits that said "check_render no failures" did not run it.
- **The enhanced portraits on the page.** The 20 person pages with an enhanced portrait (the 17, López, Wicks, Kellar), fetched from DDEV: none emits the enhanced file in its JSON-LD ("An enhanced derivative is never emitted"), and every one shows the edit label with the way to the original.

## Read from a description

Things stated as fact that rest on a description rather than the source. Each is written up, none fixed.

1. **"The enhanced-pair rule of 5 October."** ERRORLOG (8 October), TODO ("the 5 October enhanced-pair rule"), restore_enhanced_pairs_2026_10_08.php, and the brief for this audit date the rule 5 October. The rule's own record dates it 6 October: DATA-MODEL quotes "Nathan, 6 October 2026", CHANGELOG has it on 6 October, and git first has it in commit 5c4b99c, 6 October 08:41. The date went from ERRORLOG's account into the restore script, and from there into the public source sentence of all 17 restored assets (#27381, #27383, #27387, #27396, #28814, #31387, #31391, #31395, #31398, #31404, #31406, #31408, #31423, #31427, #31447, #31449, #31472): "put back the same day on Nathan's word (the enhanced-pair rule of 5 October 2026)".
2. **"The 24 enhanced portraits taken off under the 8 October Firefly rule."** TODO and ERRORLOG say the Firefly rule took off 24. Seven of them (Connie Worden, Klajic, Brathwaite, Nadeau, Frew II, Gelcich, Jenkins) came off earlier that night under the no-generated-image rule (pull_generated_portraits_2026_10_08.php; generated-images-2026-10-08.md, section 2, "the ten text-prompt portraits"). The 24 is a sum taken from the summaries, not from the two pull scripts. It matters because the seven are held under a different rule, the one in DATA-MODEL (rules page, contradiction 5).
3. **"Every one is Nathan's own work in Adobe Firefly."** The restore script's header, ERRORLOG ("The 24 were all his own work, in Firefly") and TODO state it. What it rests on is `enhancedBy`, "Nathan Imhoff" on all 17, a field written from your word on 1 to 6 October. The script's own reads() says the files were not read ("matched by eye on 6 and 8 October"). It is probably right, and it is what the archivist-decides rule records, but it is a record of a statement, not a reading of the files.
4. **Frémont's portrait (#28814) is an enlargement only.** The asset records "Upscaled (Adobe Firefly creative upsampler)", and its credential lists one step, the upsampler. The side-by-side sheet of 8 October saw "the corners outside the oval were painted in" and filed it with the fills (generated-images-2026-10-08.md, section 4). The page labels it from the record. Either the upsampler painted the corners or the credential is not the whole story; which, nobody has settled. The restore went by the record.
5. **HANDOFF: "The rule and its reasons are in docs/DATA-MODEL.md ('Generated and edited images')."** DATA-MODEL holds the generated-image rule, but not the Firefly-edit rule HANDOFF's caution describes, and its "Edited photographs" bullet still says the opposite. The pointer was written from what the session meant to put in DATA-MODEL, not from DATA-MODEL. HANDOFF's "255 person records" is likewise stale: there are 254 since Marler became a row.

And this audit's own limit: every image check here (IMG-1 to IMG-9) reads what the asset records say about their files. No file was opened. A clean result means the records agree with the rules, not that the files do.
