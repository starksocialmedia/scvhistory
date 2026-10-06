# Connie Worden-Roberts obituaries: dry run, 5 October 2026

Script: `scripts/import/worden_roberts_obituaries_2026_10_05.php`. Evidence: `inventory/review/worden-roberts-obituaries-evidence-2026-10-05.json`. Mode: DRY RUN.

## A. Two pieces or one

Two genuine pieces. Shared six-word runs between the bodies: **0** (of 1003 and 473).

| | #28045 | #28047 |
| --- | --- | --- |
| Legacy page | /scvhistory/obituary_conniewordenroberts.htm | /scvhistory/khts081314.htm |
| Header as printed | `<font class="altheadline">Connie Worden-Roberts</font>` `<font class="subhed2">Cityhood Pioneer and Road Warrior</font>` `<font class="byline">November 19, 1930 &mdash; August 12, 2014</font>` `<font class="dateline">Eternal Valley Memorial Park &#38; Mortuary</font>` | `<font class="altheadline">We've Lost Our Road Warrior</font>` `<font class="byline">By Carl Goldman</font>` `<font class="dateline"><a href = "http://hometownstation.com/santa-clarita-news/editorial-we-lost-true-scv-leader-and-our-road-warrior-43222">AM-1220 KHTS</a> | Wednesday, August 13, 2014</font>` |
| Obituaries index | Eternal Valley, 8-12-2014 | by Carl Goldman, AM-1220 KHTS, 8-13-2014 |
| Kind | formal obituary, third person, life and survivors | personal tribute, first person, the cross-valley connector |
| publicationDetails now | SCVHistory.com, August 2014 | Carl Goldman, AM-1220 KHTS, Wednesday, August 13, 2014 |

Relating them: **not written.** The obituary layout's relation fields are footnotesOn (*), obitPublishedIn (organizations), obitSubject (persons), obitGroup (groups), obitRelatedPersons (persons), obitRelatedMilitary (section:15e98910-53c8-4cbc-9d54-06bdd1c492b2), derivedImageLinks (*). None relates an obituary to a companion piece: obitPublishedIn takes organizations, obitRelatedPersons persons, obitRelatedMilitary war memorials, footnotesOn is a citation and derivedImageLinks is for derived images. `relatedArticles` takes articles only and is not on the obituary type. Both already carry obitSubject #16418, so her person page lists them together. A companion link needs a field and a template line, Nathan's decision.

## B. Carl Goldman

- Person: none (searched persons by title and personAliases). **Would create** "Carl Goldman" (slug carl-goldman):
  - occupation: Radio station co-owner (AM-1220, KBET and KHTS)
  - body: Carl Goldman co-owned AM-1220, the Santa Clarita Valley's own radio station. He bought it, as KBET, out of bankruptcy in 1990 with investor partners and sold it to Clear Channel in 1998; on October 24, 2003, he and his wife, Jeri Seratti-Goldman, bought it back, and it returned to the air as KHTS.[1] After the Northridge earthquake of January 17, 1994, KBET stayed on the air around the clock and the City Council declared it Santa Clarita's official emergency radio station; that July he was grand marshal of the Fourth of July Parade in Newhall, whose theme was Earthquake Heroes.[1] He was the 2008 SCV Man of the Year.[2] He wrote a tribute to Connie Worden-Roberts for KHTS on August 13, 2014.[3]
  - note 1: Leon Worden, "Carl Goldman, Grand Marshal, 1994 Fourth of July Parade," SCVHistory.com LW9450a, 2014, https://scvhistory.com/scvhistory/lw9450a.htm: "Carl Goldman, co-owner and the public face of KBET"; "Carl Goldman, who had purchased the radio station out of bankruptcy in 1990 for about $600,000 with investor partners, sold it in 1998 to Clear Channel for $3 million"; "On Oct. 24, 2003, Carl and wife Jeri Seratti-Goldman repurchased the radio station"; "It was still AM-1220 on the radio dial, but now it had new call letters"; "The City Council declared it Santa Clarita's official emergency radio station."
  - note 2: SCVHistory.com, "SCV Man & Woman of the Year," https://scvhistory.com/scvhistory/mwoty.htm: 2008, Carl Goldman and Judy Penman.
  - note 3: Carl Goldman, "We've Lost Our Road Warrior," AM-1220 KHTS, Wednesday, August 13, 2014, as kept at https://scvhistory.com/scvhistory/khts081314.htm: "When Jeri and I purchased our radio station, AM-1220, in 1990".
  - bodyAuthorship editorial-2026; recordProvenance: worden_roberts_obituaries_2026_10_05.php, 5 October 2026: from SCVHistory.com LW9450a, mwoty.htm and khts081314.htm
- KHTS organization: none, so no affiliation is made. Spouse link to Jeri Seratti #29113: not made (spouse links never publish, docs/DATA-MODEL.md).
- Author link: **blocked.** The obituary entry type has no author field. writtenBy (persons, schema.org author) exists and is used by articles; adding it to the obituary layout is a project-config change. Set $ADD_WRITTENBY_TO_OBITUARY = true to include it. templates/obituaries/_entry.twig does not render writtenBy, so the link will not show until the template does. Until then publicationDetails carries his name, as now.

## C. Who wrote the formal obituary

The page prints no writer. Its header, exactly: `<font class="altheadline">Connie Worden-Roberts</font>` / `<font class="subhed2">Cityhood Pioneer and Road Warrior</font>` / `<font class="byline">November 19, 1930 &mdash; August 12, 2014</font>` / `<font class="dateline">Eternal Valley Memorial Park &#38; Mortuary</font>`. The byline slot holds her life dates; the dateline names the mortuary, and a Dignity Memorial logo links to Eternal Valley. The obituaries index (obits.htm) credits it "Eternal Valley, 8-12-2014", the form it uses for funeral-home notices (243 index entries are credited to Eternal Valley). So the source is Eternal Valley Memorial Park & Mortuary, an organization, not a person; no person record is made and none is inferred.
- publicationDetails is "SCVHistory.com, August 2014", which the page does not print. $FIX_FORMAL_PUBLICATION is off: not changed. On, it would set "Eternal Valley Memorial Park & Mortuary" (the dateline as printed).

## D. The image

- File: Reggie `gif/lw9501_large.jpg` (2400 x 3058, sha256 ec3f4fb1118b5411...), the enlargement the formal page links; copied to `inventory/incoming/lw9501_large.jpg`, checksum matches. The web size, gif/lw9501.jpg (800 x 1019), is the same picture.
- What it shows: Connie Worden-Roberts, waist up, in a red suit with arms folded, against a night view of freeway traffic in light trails. Signed in the image, lower left, "Photo by Gary Choppe'". No date is printed anywhere; none is set.
- Credit as printed: formal page `Photo by Gary Choppé / Creative Image Photography`; Goldman's page caption `Photo by Gary Choppe | Click to enlarge.`.
- In Craft: not held (no asset with this checksum, the web-size checksum, or a filename containing lw9501). **Would import** to archiveMedia/legacy/ as lw9501_large.jpg, title "Connie Worden-Roberts, portrait by Gary Choppé", alt "Connie Worden-Roberts in a red suit, arms folded, in front of the light trails of freeway traffic at night".
  - provenanceKind: legacy-mirror
  - legacySourcePath: gif/lw9501_large.jpg
  - acquiredDate: 2026-10-05
  - source: SCVHistory.com, gif/lw9501_large.jpg, as published on the original site: the lead image of /scvhistory/obituary_conniewordenroberts.htm and in the text of /scvhistory/khts081314.htm.
  - sourceChecksum: sha256:ec3f4fb1118b541128de91e49fba540c25d231af1f4b619e3a010664ed1c850d
  - photoSourceCode: LW9501
  - photoCredit: Photo by Gary Choppé / Creative Image Photography
  - creator: Gary Choppé
  - photoPeople: [16418]
  - license: left unset, as for the other legacy-mirror images; the rights are not established.
- #28045 recordImages: **would put it first** (empty now). The template shows the first record image as the page's portrait.
- #28047 recordImages: **would put it first** (empty now). The template shows the first record image as the page's portrait.
- Person #16418 (Connie Worden): featuredImage and recordImages are empty. Not set here; the source attaches the portrait to the obituaries, and her portrait is Nathan's call.

## Found on the way, not changed

- #28047 is missing two of Goldman's paragraphs that the page prints: "Connie's son, Leon, followed in her footsteps with his community involvement. Leon now heads up our valley's public access channel SCVTV and its news website." (before "He has been devoted to his mom...", which now has no antecedent) and the closing line "I will dearly miss Connie." The 2008 groundbreaking caption (lw0801) is also not carried.
- Perry Smith's KHTS obituary of the same day (khts081214.htm) is document #28305, while the two pieces here are obituaries.
- The photo credit printed on the formal page is not carried on #28045; this import puts it on the asset.

## Refused

none

Nothing was written. Set $APPLY = true to apply. A second run after an apply is a no-op.


