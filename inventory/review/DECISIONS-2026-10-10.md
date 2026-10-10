# Every decision waiting on you, on one page (10 October 2026, morning)

Claude, for Nathan. Ordered by what each unblocks. Times are rough reading times, with the linked file open.

## Read first (2 minutes)

**The seventh instance was found tonight, and I stopped there.**
- **Bill Pecsi's "resigned" (#28421)** came from a search-result snippet of an article that the 4 October review never opened. The resolution the record cites says nothing about how his service ended. The article, read tonight, does print the resignation, so the claim stands; for six days it rested on a snippet. Not fixed. Written up in ERRORLOG and in how-ended-vs-sources-2026-10-10.md.
- **Smaller candidates of the same shape, all written up and none fixed:**
  - #28057's headline field holds Leon's citation, not the printed headline.
  - Camulos's 1,800 acres rest on a family manuscript known only through del Castillo's description, and his correction on the same page was left out.
  - #283's text was called Leon's on a label; the pages carry no byline.
  - Four photographs show a gallery link label ("Earliest Known 11-10-1929") as their date.
  - "No metadata at all" restated a census label that meant 13 tags.
  - #26573, #26576 and #26579, the County's returns for a whole general election, name one water agency as what they concern. The value was computed from the election records that link to them, not read from the PDFs.

## The decisions

| # | Decision | What it unblocks | Time | Where |
|---|---|---|---|---|
| 1 | **The rule for the 22 enhanced pairs**, by kind of claim: A face redrawn (3), B face enlarged out of a smaller original (2, maybe 5), C content added beyond the frame (2), D content removed (5), E tone and sharpness (11). Also whether rr1, Nadeau and Chrisman, and Brathwaite move from E to B. | The 15 pairs held in check_generated_files; the audit's IMG-1 and IMG-2; what staging shows | 25 min | the-22-pairs-by-claim-2026-10-10.md, the sheets |
| 2 | **The four Firefly-edited marks** (#29696, #29439, #29400, #29298) | They go to staging as they are unless you rule | 5 min | archive-images-2026-10-09.md |
| 3 | **Retyping 54 documents and photographs to articles**, in nine one-word batches: four mechanical (WEB 7, MAGAZINE 13, CLIPPING 5, SAUGUS 12) and five to read first. Before any photograph batch: the article page would lead with Leon's category line ("> INDIAN DUNES") on 28 of 30. | 54 records in the right section | 15 min | retype-sure-55-dry-run-2026-10-10.md |
| 4 | **The archive's own false sentences:** 7 false and 4 stale claims in records (Rioux "holds 32" has 34; #665 "largest single-author series" is tied; two collections that hold 0). 4 false lines of page furniture ("Every record is cited", "94 school board contests"). 7 hard-coded numbers that should be computed. | Public text that is wrong today | 10 min | archive-self-claims-2026-10-10.md |
| 5 | **Titles:** the four calls (#4567, #21936, #5481, #20099), the printed dates on three programs (#5377, #3191, #4943), and the #28055 correction ("Pen Pictures" is a different book) | 8 held retitles; the #28055 family of citations | 10 min | titles-calls-2026-10-09.md |
| 6 | **The three held photographs:** #2913 (no catalogue entry of Leon's found), #5377 (its date is in 5), #4519 (holds the wrong file; the real program is lw2805.pdf on Reggie) | 3 photograph records | 5 min | held-import-2026-10-08.md, titles-calls-2026-10-09.md |
| 7 | **Bylines:** withdraw census rule (b) or keep it as a flag; whether writing often about the valley counts as a role (9 names turn on it); a field for a byline with no record; the drafts for Pollack, Saletore and Jacobs (and whether Jacobs passes "what they did here") | 3 records; 9 names; every unlinked byline | 20 min | bylines-rule-and-names-2026-10-09.md, byline-drafts-pollack-saletore-jacobs-2026-10-10.md, bylines-unclear-nine-2026-10-10.md |
| 8 | **The eight disputed points:** A1 Henry Mayo Newhall's birthday; A3 Camulos's five; A7 Beale's Cut 90 feet; A8 Stearns's oak; A9 Lopez's miners; A10 the Redevelopment Committee's terms; A11 whether McGrath's 2009 holding should stand at all; A12 Ygnacio del Valle's birthplace and burial | 8 records stating a disputed point as fact | 25 min | disputed-settled-2026-10-09.md |
| 9 | **Four second sources that differ** (no resolution offered): del Valle's division of the rancho and Camulos's acreage (38, 45), Frémont's route and numbers (32), the year of Fages's pursuit (48). Claim 44, the heirs, is held with them. | 5 claims | 15 min | second-source-disagreements-2026-10-10.md |
| 10 | **Endings cited to the wrong source:** Olsen #28481 and Love #28473 cite announcements, and the sources that print the act are saved; Pecsi #28421 cites a resolution that does not say it (the seventh instance). Approve citing the right articles. Their dates are held. | 3 holdings | 5 min | how-ended-vs-sources-2026-10-10.md |
| 11 | **Henry Mayo Newhall #283:** published with no attribution. The text is the legacy site's, credited on its page to Ruth Waldo Newhall's 1992 book, with no byline of Leon's. Attribute it, and to whom. | One person page | 5 min | person-bodies-ours-vs-leon-2026-10-10.md |
| 12 | **"© Friends of Hart Park" as photoCredit** on 20 assets (35 in the 5 October script's count), which is the Hart biography's line, not the image's | Credits on 3 live records | 5 min | no-metadata-images-provenance-2026-10-10.md |
| 13 | **John Boston's profile draft**, and which title Haskell's obituary takes | His person page | 15 min | overnight-2026-10-08/boston-profile-draft-2026-10-08.md |
| 14 | **What no longer complies after the rule changes.** Of 4,137 elements changed in 48 hours, the real failures are: Wicks #38450 and Kellar #38452 with no enhancedBy or enhancedDate; Gibbs #38460 and Ayala #38462, the City's composites, not disclosed under the publisher-edited rule; 8 election documents with the body still after a colon in the title. DATA-MODEL still carries three older sentences that contradict the new rules ("ordinary archival practice"; "where the original is not held, enhancedFrom stays empty"; "it never blocks"). Approve the fixes and the DATA-MODEL wording. | 6 records; DATA-MODEL | 15 min | changed-48h-vs-rules-2026-10-10.md |

## Only you can do these

- **Push.** The commit from tonight is unpushed.
- **Staging:** the /review/ exposure check with the basic-auth credentials; removing Wiley's #1658 and Vasquez's #28816 files from staging's disk (the rsync never deletes).
- **Leon's catalogue entry for #2913,** if he has one.

## Not decisions, for reference

- **Done tonight:** item numbers in brackets are your brief's; the detail is in CHANGELOG.
  - Jenkins's death certificate and obituary imported, and his record corrected (7).
  - Second sources added for 19 claims on ten records, every quotation checked in its source (4).
  - McGrath's howEnded set to unknown, the announcement footnoted (8).
  - The census count fixed: 50 to 41 on Perkins or Reynolds alone (6).
- **Reports:** the 22 by claim, and the three guesses settled by measurement (2, 3); the 43 recorded endings checked against their sources (8); the ratio of our writing to Leon's, about 24 to 1 in person bodies (13); the 2,445 images (14); the archive's claims about itself (15); the week (16); staging (17).
