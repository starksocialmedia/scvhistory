# The Wikipedia census: portraits and profiles, 6 October 2026

Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."

All of this is dry runs only. Nothing has been written to Craft. Every script has `$APPLY = false`, and every dry run ends "REFUSED: none" except the one portrait row noted below. Source of the list: `inventory/review/wikipedia-census-2026-10-06.md`. Every census person named here still has a record; none was among the 85 thin records removed since the census.

Wikipedia was used as a finding aid only. No profile body rests on it. Each draft's .md lists, under "What rests on Wikipedia alone (not in the body)", every fact found only in the article. Those facts are left out of the body rather than carried with a label, so no editorNotes rows were needed.

## Part A: portraits

Script: `scripts/import/import_commons_portraits_2026_10_06.php`. Its dry run is printed to `inventory/review/commons-portraits-dry-run-2026-10-06.md`.

- Files are saved as `inventory/incoming/<slug>-commons.jpg`. Each is the Commons original, with its SHA-1 checked against the one Commons reports.
- The manifest, the Commons API response and the Whitesides copyright page are in `inventory/sources/commons-portraits-2026-10-06/`.
- Each file becomes an asset in `archiveMedia/outside/` and the record's featuredImage, with:
  - provenanceKind `outside`;
  - sourceUrl set to the Commons file page;
  - source naming the Commons page and its author;
  - sourceChecksum set to the file as received.
- The script works only on records that still have no portrait.

| Record | File | Licence set | Basis |
|---|---|---|---|
| #29466 Kevin McCarthy | kevin-mccarthy-commons.jpg (4178 x 5222) | public-domain | US House Photography, PD-USGov-Congress-Speaker |
| #29464 Bill Thomas | bill-thomas-commons.jpg (1530 x 1927) | public-domain | PD-USGov-Congress, from billthomas.house.gov. The photographer is not named; this is the weakest of the federal cases |
| #29462 Henry Stern | henry-stern-commons.jpg (1216 x 1824) | unknown | PD-CAGov on Commons. The file's own notice reads "Senate Rules (c)2016"; photographer Lorie Leilani Shelley. Treated like Christy Smith's portrait (Government Code 7920.540(a)) |
| #29460 Fran Pavley | fran-pavley-commons.jpg (1333 x 1871) | **REFUSED** | CC BY 2.0, Edward Headington, via Flickr. The license dropdown has no CC BY 2.0 option. Adding one is a schema change for Nathan to approve; the row will not enter under a different licence |
| #29458 Cathie Wright | cathie-wright-commons.jpg (964 x 1284) | unknown | PD-CAGov on Commons. Commons took the file from SCVNews.com's report of her death, not from a Senate original |
| #29452 Jeff Gorell | jeff-gorell-commons.jpg (1877 x 2201) | unknown | PD-CAGov, from the Assembly gallery. Treated like Christy Smith's portrait |
| #29446 Tom McClintock | tom-mcclintock-commons.jpg (335 x 410, small) | public-domain | House Clerk's member photo, PD-USGov-Congress |
| #29336 George Whitesides | george-whitesides-commons.jpg (1638 x 2048) | public-domain | Photographer Ike Hayman. His House site's copyright page: "all of the content of the website constitutes a work of the Federal government" |
| #29334 Mike Garcia | mike-garcia-commons.jpg (2659 x 3547) | public-domain | House Creative Services (the file names Kristie Baxter), PD-USGov-Congress |
| #18714 Phineas Banning | phineas-banning-commons.jpg (416 x 553, small) | public-domain | Photograph made before 1885, PD-US. Commons took the file from a Press-Enterprise page of 2013 |
| #2532 Cephas L. Bard | cephas-l-bard-commons.jpg (1559 x 2185) | public-domain | Men of California (1901), page 206, PD-US |

Skipped: #29450 Audra Strickland, who already has a portrait (audra_strickland_portrait_2026_10_06.php, applied).

## Part B: profiles

There are 14 profile builders: 10 for the twelve people with portraits and 4 more census people, within the cap of 15. Audra Strickland already has a profile (applied), and Cephas Bard is skipped (see below).

- Each builder is `scripts/import/build_<slug>_profile_2026_10_06.php`.
- Each reads `inventory/review/<slug>-profile-draft-2026-10-06.json`, which is made by `scripts/import/draft_<slug>_profile_2026_10_06.py`. That script checks every quotation against a saved copy of its source.
- Each prints `inventory/review/<slug>-profile-dry-run-2026-10-06.md`.
- Saved sources are in `inventory/sources/<slug>-2026-10-06/`, each with a manifest.json.
- Living people: public life only, and no birth fields.

| Person | Builder slug | Words / notes | Opens with | Main sources |
|---|---|---|---|---|
| #29466 Kevin McCarthy | kevin_mccarthy | 219 / 5 | 22nd CD, 2007 to 2013: Green Valley only, 0.4% of the valley | House History biography; Biographical Directory; Clerk roll call 519 of 2023; Statements of Vote 2006 to 2010 |
| #29464 Bill Thomas | bill_thomas | 215 / 4 | 22nd CD, 2003 to 2007: Green Valley only | House History biography; Biographical Directory; Statements of Vote 2002 to 2006 |
| #29336 George Whitesides | george_whitesides | 260 / 7 | 27th CD since January 2025: the whole valley | Clerk member page; House History biography; NASA biography (2010); Virgin Galactic Form 8-K (2020); Statements of Vote 2024 and June 2026 |
| #29334 Mike Garcia | mike_garcia | 400 / 10 | 25th CD, then 27th, May 2020 to January 2025: the whole valley | Biographical Directory; his House biography; SOS special and general results 2020 to 2024; Congressional Record H76 and roll calls 10 and 11, 6 January 2021 |
| #29328 Steve Knight | steve_knight | 497 / 16 | 21st SD, 2012 to 2015 (79.7%); 25th CD, 2015 to 2019 | Biographical Directory; Records of Members and Senators; Statements of Vote 2012 to 2020; Congressional Record; nine mirror pages; Public Law 116-9 |
| #29446 Tom McClintock | tom_mcclintock | 461 / 13 | 38th AD, 1996 to 2000, and 19th SD, 2000 to 2008: the west side | Biographical Directory; Records; Statements of Vote including the 2003 recall; Worden columns lw102198 and lw081298; Gazette 2006; City 2007 book |
| #29462 Henry Stern | henry_stern | 374 / 7 | 27th SD, 2016 to 2024: the west side, 20.3% | Senate biography; Statements of Vote 2016 and 2020; Signal via SCVNews (2019); College of the Canyons annual report; SCV Water briefing list |
| #29460 Fran Pavley | fran_pavley | 431 / 10 | 27th SD, 2012 to 2016: the west side, 20.3% | Senate biography and its Santa Clarita gallery (Wayback 2014); bill texts AB 1493, AB 32, SB 1168, SB 32 and SB 380 (Wilk coauthor); Statements of Vote |
| #29458 Cathie Wright (died 2012) | cathie_wright | 548 / 11 | 19th SD, 1992 to 2000: the west side, 16.3% | Statements of Vote 1992, 1994 and 1996; Records; LA Times obituary 2012; Worden's SCVNews obituary; mirror lw3499, sg110185, bw8703 and lw102396 |
| #29452 Jeff Gorell | gorell | 455 / 11 | 37th AD, 2010 to 2012: Castaic to Green Valley, 12.9% | Statements of Vote 2004 to 2014; Record of Members; Ventura County Star 2010; LA Times 2010; Assembly biography; Acorn and Ojai Valley News |
| #18747 George Runner | george_runner | 737 / 16 | 36th AD, 1996 to 2002 (83.7%); 17th SD, 2004 to 2010 (74.2%) | Statements of Vote; Records; three Signal Newsmaker interviews (Wayback); Worden columns; mirror pages on the auditorium grant, Veterans Plaza and SR-126; BOE page |
| #29284 Michael D. Antonovich | antonovich | 493 / 15 | 5th District supervisor, 1980 to 2016 | Newsmaker 2003; County and AQMD biographies (Wayback); Record of Members; mirror LW3109, JD9002, sg110185, Hart Museum timeline, Citizen 1988 |
| #29288 Kathryn Barger | barger | 468 / 10 | 5th District supervisor since 2016 | LA County Registrar Statements of Votes Cast 2016, 2020 and 2024; County biography and district page; her office's releases (Newhall Ranch, The Old Road, Hart Park, Chiquita Canyon); LW3109 |
| #18714 Phineas Banning (died 1885) | banning | 472 / 10 | First stage over the San Fernando Pass, December 1854; Lang Station, 1876 | Horace Bell, Reminiscences of a Ranger (1881); Sacramento Daily Record-Union, 10 March 1885; Perkins; Worden; Pollack; 1976 centennial program |

### Points for Nathan

- **Cephas L. Bard (#2532): no profile.** No role in the valley could be sourced. His only link is Perkins's "1. Early Inhabitants" (#1420), which quotes his 1894 address on Indian medicine. The person-record rule may apply to him.
- **Fran Pavley's portrait** needs a `cc-by-2.0` license option first. That is a schema change.
- **Bill Thomas's portrait** is public domain on the Commons tag and its house.gov source alone. The photographer is not named.
- **Mike Garcia:** one sentence covers his votes for the January 2021 objections to the Arizona and Pennsylvania electoral votes, sourced from the Congressional Record and the roll calls. It is in for Nathan to keep or cut.
- **Cathie Wright:** left out on purpose is the 1989 district attorney inquiry into her daughter's tickets. It concerns a private living person.
  - The archive has no holding for her Assembly years (1980 to 1992). None was created.
  - The ellipsis in note 3 is the printed title of Worden's 1996 column, "A politician by any other name...". It does not join two clauses.
- **Henry Stern:** holding #29521 ends 2 December 2024, marked "reelected". His 2024 district under the 2021 lines holds none of the valley. The holding was not touched.
- **George Runner (#18747)** has no roles set, neither Assemblymember nor Senator. His builder reports this and does not set them.
- **Phineas Banning:** the death date of 8 March 1885 comes from the Record-Union's "died Sunday" of Tuesday 10 March. The 1858 and 1876 newspapers are quoted at second hand through Worden and Pollack, and the notes say so.
- **Antonovich:** the draft .md lists two "readings to weigh".
- **Relations added:**
  - Thomas and McCarthy are related to each other.
  - Whitesides and Garcia are related to each other.
  - Stern and Pavley are related to each other.
  - Barger and Antonovich are related to each other.
  - Others are listed in each dry run.
  - No spouse relation is made. The Runners' marriage is stated in the text only.

### Wikipedia-only facts left out (full lists in each draft's .md)

- **McCarthy:** the 15 Speaker ballots and the debt-ceiling deal.
- **Thomas:** his earlier districts and his retirement reasons. Whether his 1983 to 1993 20th District held any of the valley is open.
- **Whitesides:** Hurst Fire outreach. This is worth chasing as a valley lead.
- **Garcia:** his parents' 1959 arrival, Navy detail, Raytheon title, and "first Republican flip since 1998".
- **Steve Knight:** his LAPD years, Palmdale council dates, the 2015 protester incident and the Aliso Canyon gas leak.
- **McClintock:** his 1996 margin, Controller and Lieutenant Governor races, and the lethal injection law.
- **Stern:** "first millennial", his Waxman and Pavley staff roles, and the 2022 supervisor race.
- **Pavley:** her Coastal Commission seat, "mother of climate policy", and her earlier district number.
- **Wright:** her Assembly successor (the article contradicts itself), "Peroxide Princess", and her middle initial.
- **Gorell:** his rank and medals, the drone bill veto, and his budget vice-chair post.
- **Runner:** the years of his caucus and Budget roles, and "co-author" of Proposition 83 (the body says only that the Signal called him a cosponsor).
- **Antonovich:** his middle name (Daniel and Dennis both given), his Mayor years, and his State Military Reserve service.
- **Barger:** her swearing-in date of 5 December 2016, the name Barger-Leibrich, and the exact years she chaired the Board.
- **Banning:** his birth date of 19 August 1830, the accident, his burial, and his senate seat. The article says nothing about the valley.

## Run order for Nathan

1. **Server or MacBook:** run `import_commons_portraits_2026_10_06.php`. Ten rows; Pavley stays refused.
2. **Server or MacBook:** run each `build_<slug>_profile_2026_10_06.php`, in any order. They are independent; each writes only its own record and holdings.
3. After applying, run `scripts/import/check_rendered_bodies.php` (DEPLOY-RUNBOOK section 9).
