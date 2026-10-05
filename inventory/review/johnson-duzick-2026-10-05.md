# Sharlene Rose Johnson and Sharlene (Rose) Duzick: one person?

Claude, 5 October 2026, for Nathan. Read-only: no database writes, no git. Saved sources are in `inventory/news/johnson-duzick-2026-10-05/` with `manifest.json`.

## Conclusion: settled, same person

Two independent sources each name both names for one person:

1. **The State's real estate license record** (strongest). Salesperson license 01938103 is held by "Johnson, Sharlene Rose", and the record's "Former Name(s)" field reads "Duzick, Sharlene Rose". These are exactly the two ballot names in CEDA ("Sharlene Rose Duzick", Saugus Union 2022; "Sharlene Rose Johnson", college district 2024).
2. **The 2022 Saugus Union contest itself.** The Signal (5 August 2022) reports that "Sharlene Johnson" will run for Saugus Union seat No. 5 against the incumbent Christopher Trunkey. The County's returns (CEDA) show only one challenger to Trunkey in that contest: "Sharlene Rose Duzick". So she campaigned as Johnson and appeared on the ballot as Duzick in the same race.

Her agent profile on Redfin backs this up. It sits under one license number, is headed "Sharlene Duzick", and its biography reads "Sharlene Johnson ... Team Lead of The Sharlene Johnson Team" at RE/MAX Gateway, which is the employer her district biography gives.

Recommendation: link candidacies #25725 and #25755 to person #30263, drop the "(?)" from her two Duzick aliases, and replace the "probably" editor notes. The archive should say only that she stood as Sharlene Duzick in 2018 and 2022. It should not give or guess a reason for the change of name, since the sources do not give one and it would be a private family detail.

## The oddity: the dates (resolved, and an error found)

- **She was not on the college board in 2022.** Her only college term in the archive is office holding #30417: "College Trustee, Santa Clarita Community College District", Trustee Area 4, termStart December 2024. It rests on candidacy #30084 in election #30080 (5 November 2024): elected with 12,821 votes, ballot designation "Foundation Board Member" (CEDA2024Data.xlsx row 2163). CEDA 2022 has no college row for any Sharlene. She was not appointed in lieu, and she did not stand in two contests in 2022.
- **2018:** candidacy #25725 "Sharlene Duzick", election #25721, Saugus Union Trustee Area 5, 6 November 2018. She lost with 3,368 votes; Trunkey won with 3,561. Designation "Parent/Businesswoman" (CEDA2018Data.xlsx row 4922).
- **2022:** candidacy #25755 "Sharlene Rose Duzick", election #25751, Saugus Union Trustee Area 5, 8 November 2022. She lost with 2,984 votes; Trunkey won with 3,183. Designation "Parent/Businesswoman" (CEDA2022Data.xlsx row 5259).
- So the order is: Saugus Union as Duzick in 2018 and 2022 (both lost), then the college board as Johnson from December 2024. Nothing overlaps.
- **Error to fix:** the editor note already applied to #25725 and #25755 (`scripts/import/note_duzick_johnson_2026_10_05.php`, APPLIED.log 5 October 12:04) reads "a trustee of the Santa Clarita Community College District from 2022". It should say "from December 2024". The same "from 2022" is in Nathan's question and in the dry run at `coc-trustees-profiles-dry-run-2026-10-05.md`.
- Also: person #30263 has no editor note and an empty body in the database at present. The script header says Johnson's record carries the same note from `build_coc_trustee_profiles_2026_10_05.php`, but that note is not there yet. Her `personAliases` are "Sharlene Johnson", "Sharlene Rose Duzick (?)", "Sharlene Duzick (?)".

## Evidence

### A. Sources naming both names for one person

**A1. California Department of Real Estate, Public License Lookup, license 01938103.** Read 5 October 2026 (the record is stamped "taken from records of the Department of Real Estate on 10/5/2026").
URL: https://www2.dre.ca.gov/PublicASP/pplinfo.asp?License_id=01938103
> "License Type: SALESPERSON Name: Johnson, Sharlene Rose ... License ID: 01938103 ... Salesperson License Issued : 02/25/14 Former Name(s): Duzick, Sharlene Rose"

The broker history fits the Duzick-era sources: "Romeo Echo From 09/02/2020 to 05/01/2022" and "From 06/07/2022 to 04/14/2024". Experience.com and earlier search snippets call Sharlene Duzick a "Partner Agent" with Romeo Echo Real Estate under this same license number. The record is a public State licensing register, not a marriage or court record. It gives a former name and nothing about why. The saved copy has her licensee mailing address redacted. The broker business addresses are left in.

**A2. Redfin agent profile.** Read 5 October 2026.
URL: https://www.redfin.com/real-estate-agents/jessica-sharlene
- Heading and schema name: "Sharlene Duzick"; "Cal DRE #: 01938103"; "Agent License #: 01938103 Brokerage: RE/MAX Gateway".
- Biography on the same page: "Sharlene Johnson has a gift for making people feel seen ..."; "As a Santa Clarita Valley Realtor with RE/MAX Gateway and Team Lead of The Sharlene Johnson Team ..."
- Her district biography gives "Real estate agent, REMAX Gateway" (canyons.edu profiles, saved in batch-b).
- This is an agent-supplied commercial listing. It is good for identity, but it is not a source to cite for any other facts. The saved copy has her e-mail address and one listing-broker phone number redacted.

### B. The same contest under each name (independent sources)

**B1. The Signal, "Trio of parents announce run for Saugus district board seats," a news release by "admin", 5 August 2022 (published 2022-08-06T04:53 UTC).** Read through the Wayback Machine, capture of 30 January 2023. signalscv.com itself returned 403.
URL: https://web.archive.org/web/20230130204930/https://signalscv.com/2022/08/trio-of-parents-announce-run-for-saugus-district-board-seats/
> "Sharlene Johnson, [...] real estate professional, will be running for seat No. 5, which includes Plum Canyon, Skyblue Mesa and Cedarcreek elementary schools, and held by incumbent Christopher Trunkey."

(The omitted words are family descriptors from the release and are left out of the quote. The saved page is unaltered.)

**B2. CEDA 2022, row 5259** (`inventory/raw/ceda/CEDA2022Data.xlsx`): Saugus Union, Area 5, 8 November 2022, two candidates, Trunkey and "Duzick | Sharlene Rose", "Parent/Businesswoman", 2,984 of 6,167. The same contest, so the Johnson of B1 is the Duzick on the ballot.

### C. The same unusual details under each name (supporting)

- **Measure SA oversight committee.** Duzick: a search snippet of her NextHome agent page (nexthome.com/agent/Sharlene-Duzick/14857, now 404, not saved) says she "served on the William S Hart School District Bond Measure SA Oversight Committee" and sits on the "WiSH Education Foundation, College of the Canyons Foundation Board, JCI Santa Clarita and the Valley Industrial Association's (VIA) education team". Johnson: her district biography says "Served as member, Wm. S. Hart Union High School District Bond Measure SA Oversight Committee". This is a lead only: the page was not read.
- **Experience.com, unclaimed agent profile "Sharlene Duzick, Partner Agent"** (read 5 October 2026, saved). URL: https://www.experience.com/reviews/sharlene-26579212
  > "Sharlene serves on The College of the Canyons Foundation, California Junior Chamber, Wish Education Foundation, Valley Industrial Association, 38th Assembly District and more."

  Johnson's district biography and the batch-b draft name the COC Foundation board, the California Jaycees presidency and the Wish Education Foundation.
- **JCI Santa Clarita.** As Duzick, she was the chapter's president in 2020 (SCVNews, 6 February 2020, saved in batch-b). As Johnson, she was on the chapter's 2022 team: Alexander Hafizi, "Message from JCI Santa Clarita Chapter President," SCVNews.com, 1 December 2022 (saved). URL: https://scvnews.com/message-from-jci-santa-clarita-chapter-president-3/
  > "I would like to thank my team who helped make all of this happen: ... Lindsey James, Sharlene Johnson, Justin Charles ..."
- Already held in batch-b (not re-read): the COC Foundation's new member "Sharlene Duzick", July 2019; the JCI 40 Under Forty 2019 program, "Sharlene Duzick of NextHome Luxe Group".

## What was searched

- Web searches, 5 October 2026:
  - "Trio of parents announce run for Saugus district board seats"
  - "Sharlene Duzick" OR "Sharlene Johnson" Saugus Union candidate 2022
  - "Sharlene Duzick Johnson" OR "Sharlene Johnson (Duzick)" OR "formerly Duzick" (no hits for any combined form)
  - "Sharlene Johnson" JCI Santa Clarita president Jaycees
  - California Jaycees state president Sharlene (did not confirm the state presidency under either name)
  - "Sharlene Duzick" real estate
  - ballotpedia Sharlene Saugus Union School District Trustee Area 5 2022 (Ballotpedia has pages for Anna Griese, but none found for Sharlene; Ballotpedia returned 202 with no content)
- Wayback CDX and availability API: "Temporarily Offline" and 429 on the first try. A direct capture URL worked on retry.
- Not reached and no longer needed: the County candidate statements and Smart Voter entries for 2018 and 2022. No Smart Voter URL was guessed.
- Archive probes (read-only `ddev craft exec`): #25725, #25755, #30084, #30263, and the entries related to #30263 (#30417, #30084).
