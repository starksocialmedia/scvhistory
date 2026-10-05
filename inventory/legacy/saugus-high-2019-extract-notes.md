# Saugus High School shooting, 2019: extract notes

Claude Code, 5 October 2026. Step 2 of the survey's order of work
(`inventory/review/saugus-high-2019-survey-2026-10-05.md`), under Nathan's rulings of
5 October (D2, D3, D4, D6, D8). Read-only on the mirror and the database; nothing written
to Craft.

- Output: `inventory/legacy/saugus-high-2019-sources.json`
- Script: `scripts/import/extract_saugus_high_2019_sources.py` (re-runnable; reads the
  twelve pages by path, the images they reference, the gallery folder `files/sc1903/` and
  the two Reggie manifests; no sweep of the tree)

## Counts

| | Count | Notes |
|---|---|---|
| Pages | 12 | the 11 series pages and the April 2020 Ferry page. All 12 match the Reggie manifest. All declare UTF-8 and are plain ASCII with HTML entities. |
| Pieces | 19 | the 16 text pieces (Signal 3, SCVTV 2, L.A. Times 8, letter 1, district 2) and 3 video introductions (pages 3, 6, 7) |
| Body words | 15,782 | |
| Images listed | 120 | the survey's 43, plus 3 on the district pages and 74 in the City's vigil gallery (see surprises) |
| Document files | 1 | `hd20200112.pdf`, matches the manifest |
| Videos | 7 | all on scvtv.com, none in the mirror; the 2 Koegle files marked excluded (D4) |
| Disagreements | 8 | see the JSON for the exact quotes |
| Redactions | 7 | all in one piece (below) |
| Street or block occurrences | 2 | reported, not redacted (below) |

Image status, the survey's 43: never 6, held for rights 17, family (with the letter) 11,
signal 3, scvtv-city 6. The 17 held are the ruling's 16 plus `sg20191117shs04.jpg`, the
uncredited photograph of the two memorial crosses: D6 says it is held until its maker is
known. Outside the 43: the 74 City gallery photographs (scvtv-city), the Ferry photograph
and its large version (held for rights, proposed: no credit printed), and the district
logo (import, proposed). All 120 images and the PDF match the manifest. The 74 gallery
thumbnails (`data1/tooltips/`) and the 11 series thumbnails are derivatives and are not
listed.

The six "never" images are listed with path, checksum and size only, caption and credit
null and `captionNotCopied: true`. Their captions are not copied anywhere, the shooter's
portrait reused at the head of `lat20191116c` included.

## Verbatim check

The check tokenises each page on its own (strip script and style, join inline tags, strip
the rest, decode entities) and requires every body paragraph to appear as a contiguous run
of words in the page, in order. **Result: 19 of 19 pieces pass, 0 failed paragraphs.** For
the redacted piece, the check runs on the text before redaction, then confirms that the
output differs from it only where a placeholder stands, and that each replaced span is an
email address or a LinkedIn address (7 of 7).

Kept as printed: em dashes; soft hyphens (`&shy;`, U+00AD) in "Peter­son" (fund
piece, paragraph 2) and "re­mem­ber­ed" (`lat20191118a`, paragraph 12);
spaces before commas; every spelling slip noted in each piece's `notes`. Italics are not
marked: the "From the original story" line and the contributors line on page 1, the
memorial note and the text messages in `lat20191118b`, the Austin Dave credit line.
"Click to enlarge." after a caption is recorded as `printedAlso`, not as caption text.

## Redactions (D8)

All in `scvtv20191115-fund-peeples` (page 4, Stephen K. Peeples's fund story). Each was
replaced with exactly `[contact details withheld]`. No detail is copied into any file.

| # | Body paragraph | Position | Kind |
|---|---|---|---|
| 1 | 5 | 1st of 3 | personal email address (free webmail) |
| 2 | 5 | 2nd of 3 | personal email address (free webmail) |
| 3 | 5 | 3rd of 3 | personal email address (free webmail) |
| 4 | 19 | 1st of 2 | LinkedIn profile address (first organizer) |
| 5 | 19 | 2nd of 2 | work email address, school district domain (first organizer) |
| 6 | 20 | 1st of 2 | LinkedIn profile address (second organizer) |
| 7 | 20 | 2nd of 2 | work email address, school district domain (second organizer) |

The survey counted five; the page holds seven. The survey's own description lists all
seven kinds (two work email addresses, two LinkedIn addresses, three personal email
addresses), so all seven were replaced. One of the three personal addresses belongs to a
student who was 17 (the survey's finding). If Nathan wants the LinkedIn addresses kept,
paragraphs 19 and 20 are the places to restore.

**Other contact details:** none. All 19 bodies, headlines, bylines and captions were
scanned for email addresses, LinkedIn addresses and phone numbers; nothing else was found.
The public links left in the texts are the support-services form and the GoFundMe page
(fund piece), a Signal archive article (fund piece), the Muehlberger family's GoFundMe page
(letter), the district's safety-planning page (district release) and SaugusStrong.org
(Signal vigil story).

## The family's street and block (not redacted, for Nathan)

1. `lat20191116d-search-for-answers` (L.A. Times, 16 November, page 5): body paragraph 22,
   its only sentence, names the street of the family home (no house number).
2. `sg20191114-signal-holt` (The Signal, 14 November, page 1): the caption printed under
   `sg20191114shs04.jpg`, a D6 "never" image, gives the hundred-block and the street. That
   caption is not copied into the extract, so this one is already out of the archive's
   text if the never images stay out.

No other piece names a street, block or house number for anyone.

## The Muehlberger letter

Extracted as printed: 36 paragraphs, 1,551 words, from "As Cindy and I struggle" to
"#GracieStrong, #DominicStrong, and #SaugusStrong", the GoFundMe line included. Points
for Nathan:

- **No signature line is printed.** The letter ends with the hashtags. Who wrote it and the
  release statement appear only in the site's italic introduction, kept verbatim in
  `siteIntroduction`: "... written by Gracie Anne Muehlberger's father, Bryan Muehlberger,
  for inclusion in the #SaugusStrong Vigil on Sunday, November 17, 2019. The letter and
  accompanying photographs were released for publication by the family on November 19,
  2019."
- No byline element on the page; the dateline is "#SaugusStrong Vigil | Sunday, November 17, 2019."
- **The ten photographs have no captions, no credits and empty alt text.** They follow the
  letter in a two-column grid, in the order 01, 02, 05, 04, 03, 06, 07, 08, 09, 10, and are
  listed in that order. The eleventh family photograph (`shs2019-graciemuehlberger_full.jpg`,
  "Courtesy of Muehlberger family") is on page 5 in an L.A. Times piece; its checksum and
  size match none of the ten.
- The Signal quoted the letter at the vigil with different wording ("barely 15" against
  the letter's "barely over 15"; "too short of a time" against "too short of time"). The
  letter as printed is the family's text; the difference is in `disagreements`.

## Disagreements recorded

1. The count of the wounded (four, five, six victims, three), with page 1's title and
   Leon's timeline line.
2. Gracie's age: 16 in the Signal of 14 November **and in the L.A. Times of 15 November
   (Gerber, page 2)**; 15 in the coroner's identification, the vigil coverage and the letter.
3. The letter as quoted by the Signal and as printed.
4. Where the wounded were taken: the L.A. Times of 15 November says all to Henry Mayo; the
   Signal has two airlifted to Providence Holy Cross.
5. Whether the shooter was taken into custody (the morning story) or found in the quad.
6. The injured student's vigil message: "video" (Signal) or "audio" (L.A. Times), quoted
   differently. Recorded only; D4 applies.
7. The principal's line at the vigil, quoted differently by the two papers.
8. Name variants: the two hospitals, and "Vince" and "Vincent" Ferry.

## Surprises

1. **The vigil page embeds a City of Santa Clarita photo gallery** (`files/sc1903/sc1903.htm`,
   an iframe on page 6, credited "Photos: City of Santa Clarita."): 74 full-size
   photographs (10 named `_01` to `_11`, 64 with Flickr original filenames), all in the
   manifest. The survey did not count them. They are listed as scvtv-city, uncaptioned.
   They may show identifiable minors; nobody has looked at them yet.
2. The survey's statement that the L.A. Times of the 15th makes Gracie 15 holds for the
   "Shooting Victims Identified" piece only; the Gerber narrative of the same day says 16.
3. Seven contact details, not five (above).
4. The Koegle page introduction (the site's text, 59 words) is kept in the extract,
   marked `status: excluded (D4 ...)`, so the record of what the page held is complete.
   It names the shooter. If Nathan prefers it out of the repo, delete that one piece.
5. On page 1, each caption sits in the HTML block of the image it follows: "Photo:
   Two-8-Nine Media." only under `07`, and "Above: Students are evacuated to Santa
   Clarita Central Park." under `06`. Captions are assigned by that structure. The
   survey's note that captions sit out of step with the images was not re-checked by eye.
6. The two district pages carry images outside the survey's 43 (the district logo; the
   Ferry photograph and its 2400 by 3000 original). Their statuses are proposals.

## Rulings honoured in the extract's own text

The shooter's name appears only inside verbatim source bodies; none of the quotations in
`disagreements` contains it. No field written for this extract uses it; a script check over every notes, summary, status and location field
found none, and found no em dash in any of them.
