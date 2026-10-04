# Duplicate records: slugs ending in -2, -3 (or -N)

Generated 4 October 2026 by Claude (read-only audit; nothing in the database was changed).

Scope: all 3,770 entries in every section. 492 slugs end in -N with N from 2 to 20. Of those, 54 have no record at the base slug (election contests by division or trustee area, places named by trustee area, "Part 2" and "Part 3" coin articles, "Full Set of 8" lobby cards, "Westbound 210 Transition to I-5"): numbered names, not duplicates. One more, the person **#18834 francisco-lopez-2** (Francisco López, the 1842 Placerita gold discoverer), has no live base record: the francisco-lopez slug belonged to #305, trashed on 3 October 2026 when Chico López got his own record (#28132 chico-lopez). They are different men, so #18834 is not a duplicate; its slug can drop the -2 now that only trashed records hold the base. The other 438 were compared with the base record on title, legacyUrl, sourcePath, legacyKey, body length and similarity, writtenBy, dates, collection membership (partOfCollection and articlesInCollection), recordImages and featuredImage, and every relation pointing at each record.

## Result

| | pairs |
|---|---:|
| True duplicates (same legacy column or page) | 13 |
| Distinct records sharing a title: photographs | 422 |
| Distinct records sharing a title: articles | 3 |
| Unclear | 0 |

All 13 true duplicates are articles: the Country Fair pair and 12 Leon Worden columns that exist twice because the legacy Worden folder holds the same column at /signal/worden/ and /signal/worden/old/ (or as lw122596.htm and lw122596a.htm), and the importer took both paths. In every pair the two records carry identical relations and both sit in the collection, so the collection counts them twice.

Recommendation for all 13: keep the record with the clean base slug, put the canonical legacy URL on it (the page on the mirror that the legacy index links), keep the other path as a redirecting alias, merge what is listed, then delete the -2 record. Relations pointing at the losers are only the collection's articlesInCollection (and its saved revisions), so removing them from the collection list is the only repointing needed.

## 1. The Country Fair pair (full detail)

| | #12702 santa-clarita-valley-country-fair | #12704 santa-clarita-valley-country-fair-2 |
|---|---|---|
| legacyUrl / legacyKey | /oldtownnewhall/patti/fair1197.htm | /oldtownnewhall/patti/fair.htm |
| sourcePath | https://scvhistory.com/oldtownnewhall/patti/fair1197.htm | https://scvhistory.com/oldtownnewhall/patti/fair.htm |
| what the file is | 107-byte meta refresh to fair.htm (on the mirror) | the real page (not on the Reggie mirror; crawl text only) |
| title | Santa Clarita Valley Country Fair | same |
| body | 1,122 chars | 1,122 chars, identical (similarity 1.0) |
| writtenBy | none | none |
| originalPublishDate | empty | empty |
| partOfCollection | Open Book #679 | Open Book #679 |
| in Open Book articlesInCollection | yes | yes |
| subjectOrganization | Newhall Elementary School #15958, Hart High School #16052 | same |
| depictsPlace | Placerita Canyon Road #18841 | same |
| recordImages / featuredImage | none | none |
| other relations pointing at it | only Open Book and its revision | same |
| created | 2026-09-21 08:02 | same run |

Classification: true duplicate. The crawler followed the redirect, so one page became two records.

Keep #12702 (clean slug). Merge from #12704: its legacyUrl, sourcePath and legacyKey (/oldtownnewhall/patti/fair.htm is the real page), keeping fair1197.htm as the redirecting alias. Nothing else differs. Then delete #12704.

Two further points for the survivor. It carries no Patti Rasmussen byline and is not a column (see folder-attribution-audit-2026-10-04.md), so it comes out of Open Book. And depictsPlace Placerita Canyon Road looks wrong: the page puts the fair at Newhall Park, corner of Newhall Ave. and Dalbey Drive. The filename fair1197 points at the November 1997 fair Patti wrote about on 15 November 1997 ("as one of many committee members"), but the page text is the 2000 to 2001 version.

## 2. The other 12 true duplicates (Leon Worden columns)

| keep | delete | canonical legacy URL | what the loser holds that the keeper lacks |
|---|---|---|---|
| #12454 renaissance-for-black-palm-springs | #12544 renaissance-for-black-palm-springs-2 | /scvhistory/signal/worden/lw072496.htm | legacyUrl, sourcePath, legacyKey (the canonical page); body: the canonical page ends "His commentary appears Wednesdays."; the keeper has the caption line "Academy Award winner Hattie McDaniel" the other lacks, and the -2 copy repeats the title as its first line. Prefer the canonical page's text without the repeated title |
| #12486 latins-invade-conquer-western-scv | #12532 latins-invade-conquer-western-scv-2 | /scvhistory/signal/worden/lw082896.htm | nothing (old/ path only, for a redirect) |
| #12292 sheriff-celebrating-70-years-in-newhall | #12456 sheriff-celebrating-70-years-in-newhall-2 | /scvhistory/signal/worden/lw110896.htm | writtenBy Leon Worden #279 (the keeper has none); body formatting: the keeper wraps the whole column in one [lines] block and still opens with the "By Leon Worden / November 8, 1996" lines; the -2 copy has clean paragraphs. Worth re-cutting the keeper's body, not copying the old/ text |
| #12378 july-4th-an-old-newhall-tradition | #12452 july-4th-an-old-newhall-tradition-2 | /scvhistory/signal/worden/lw070396.htm | two passages only the old/ text has, both Leon's: "They say there was a small parade through Newhall in 1926, but the one in 1932 set the tradition that would last to the present." and "-- then a two-room affair south of Tenth Street (Lyons)" after "Newhall School". Nathan to say whether these go into an editor note or are dropped as superseded. Do not edit the canonical prose |
| #12342 pointing-fingers-in-death-of-princess-di | #12374 pointing-fingers-in-death-of-princess-di-2 | /scvhistory/signal/worden/lw090397.htm | nothing |
| #12290 movie-trivia-from-beales-cut | #12370 movie-trivia-from-beales-cut-2 | /scvhistory/signal/worden/lw040997.htm | nothing |
| #12288 time-to-take-our-house-numbers-back | #12346 time-to-take-our-house-numbers-back-2 | /scvhistory/signal/worden/old/lw071697.htm (both files exist; the index links old/) | optionally the indexed path old/lw071697.htm as legacyUrl; otherwise nothing |
| #12266 santa-doesnt-live-at-the-north-pole | #12284 santa-doesnt-live-at-the-north-pole-2 | /scvhistory/signal/worden/old/lw122596.htm | nothing |
| #12274 ward-connerly-one-mans-passion-for-fairness | #12282 ward-connerly-one-mans-passion-for-fairness-2 | /scvhistory/signal/worden/lw013196.htm | nothing of substance; the keeper's body wraps its paragraphs in [lines] blocks where the old/ copy has clean paragraphs (a re-cut, not a copy) |
| #12258 car-guys-neednt-fear-newhall-revitalization | #12280 car-guys-neednt-fear-newhall-revitalization-2 | /scvhistory/signal/worden/old/lw110597.htm | nothing |
| #12200 dont-let-sign-ordinance-catch-you-off-guard | #12206 dont-let-sign-ordinance-catch-you-off-guard-2 | /scvhistory/signal/worden/old/lw082097.htm | nothing |
| #12188 youll-die-laughing-at-ctgs-drop-dead | #12204 youll-die-laughing-at-ctgs-drop-dead-2 | /scvhistory/signal/worden/old/lw080798eb.htm | nothing |

Evidence per pair:

- **renaissance-for-black-palm-springs**: keeper /scvhistory/signal/worden/old/lw072496.htm, loser /scvhistory/signal/worden/lw072496.htm. Same column (July 24, 1996), similarity 0.987. Only worden/lw072496.htm is on the mirror and it is the one the Worden index links; old/lw072496.htm is crawl only. Relations identical (writtenBy Leon Worden, depictsPlace #15596, Selections from Leon Worden #665).
- **latins-invade-conquer-western-scv**: keeper /scvhistory/signal/worden/lw082896.htm, loser /scvhistory/signal/worden/old/lw082896.htm. Identical body. Keeper holds the page on the mirror and in the index. Relations identical.
- **sheriff-celebrating-70-years-in-newhall**: keeper /scvhistory/signal/worden/lw110896.htm, loser /scvhistory/signal/worden/old/lw110896.htm. Same column (November 8, 1996), similarity 0.994. Keeper holds the page on the mirror and in the index. Both carry recordImages lasdnwhl.jpg #14928, subjectPerson #18726, depictsPlace #15508.
- **july-4th-an-old-newhall-tradition**: keeper /scvhistory/signal/worden/lw070396.htm, loser /scvhistory/signal/worden/old/lw070396.htm. Same column (July 3, 1996), similarity 0.971. Keeper is the page on the mirror and in the index, and is Leon's later text (adds "* San Fernando Road was renamed Main Street in 2007."). Relations identical.
- **pointing-fingers-in-death-of-princess-di**: keeper /scvhistory/signal/worden/lw090397.htm, loser /scvhistory/signal/worden/old/lw090397.htm. Identical body. Keeper is on the mirror and in the index. Relations identical.
- **movie-trivia-from-beales-cut**: keeper /scvhistory/signal/worden/lw040997.htm, loser /scvhistory/signal/worden/old/lw040997.htm. Identical body. Keeper is on the mirror and in the index. Relations identical.
- **time-to-take-our-house-numbers-back**: keeper /scvhistory/signal/worden/lw071697.htm, loser /scvhistory/signal/worden/old/lw071697.htm. Identical body. Both files are on the mirror; the Worden index links old/lw071697.htm. Relations identical.
- **santa-doesnt-live-at-the-north-pole**: keeper /scvhistory/signal/worden/old/lw122596.htm, loser /scvhistory/signal/worden/old/lw122596a.htm. Identical body (December 25, 1996). lw122596.htm is on the mirror and in the index; lw122596a.htm is crawl only and linked from nowhere in the Worden folder. Relations identical.
- **ward-connerly-one-mans-passion-for-fairness**: keeper /scvhistory/signal/worden/lw013196.htm, loser /scvhistory/signal/worden/old/lw013196.htm. Same column (January 31, 1996), similarity 0.995. Keeper is on the mirror and in the index. Relations identical.
- **car-guys-neednt-fear-newhall-revitalization**: keeper /scvhistory/signal/worden/old/lw110597.htm, loser /scvhistory/signal/worden/lw110597.htm. Identical body. Keeper is on the mirror and in the index; worden/lw110597.htm is crawl only. Relations identical. Neither has writtenBy although the page carries Leon Worden's byline.
- **dont-let-sign-ordinance-catch-you-off-guard**: keeper /scvhistory/signal/worden/old/lw082097.htm, loser /scvhistory/signal/worden/lw082097.htm. Identical body. Keeper is on the mirror and in the index. Relations identical. Neither has writtenBy.
- **youll-die-laughing-at-ctgs-drop-dead**: keeper /scvhistory/signal/worden/old/lw080798eb.htm, loser /scvhistory/signal/worden/lw080798eb.htm. Identical body (stage review, August 7, 1998). Keeper is on the mirror and in the index. Neither has writtenBy (page: "Stage Review by LEON WORDEN").

## 3. Distinct articles that share a title

- **results-of-nlg-annual-writers-competition-for / -2** (#15446, #15448): Two different lists: Numismatic Literary Guild results for 2005 (nlg05winners.htm) and 2006 (nlg06winners.htm), similarity 0.51. Both titles are cut off after "for": the year was dropped at import. Neither page is by Sol Taylor (see audit B).
- **collecting-jefferson-nickels / -2** (#15366, #15394): Two weekly Sol Taylor columns with the same headline, August 1 and August 8, 2009 (soltaylor080109.html, soltaylor080809.html), similarity 0.16. A two-part piece; titles could carry Part 1 and Part 2.
- **1996-santa-clarita-city-council-race / -2** (#12524, #12526): Two Worden columns, March 27 and April 3, 1996 (old/lw032796.htm, old/lw040396.htm), similarity 0.19. Same headline, different pieces.

## 4. Photographs: 422 pairs, no duplicates

Every pair is two different legacy pages with two different image files (416 confirmed by MD5 of the mirror files; 5 could not be hashed but carry different a/b codes; 1 hashed equal only because of the bug below). Titles and captions are shared because the legacy site captioned series alike.

One data error turned up: **#4911 lake-hughes-trading-post-the-rock-inn-rppc-late-1940s-2** (legacyKey lw3098) has photoSourceCode LW2823. The legacy page lw3098.htm shows lw3098.jpg, a different crop of the same postcard (its caption says "the same photograph as LW2823 but a different pan and scan"), but its page title reads "SCVHistory.com LW2823" and the import took the code from there. As stored, #4911 renders LW2823's image, the same as #4549. Fix: photoSourceCode LW3098. Keep both records.

The full list of 422 photograph pairs (ids, legacy keys, caption similarity, image-file comparison) is in the JSON.

## 5. Same shape without a -2 slug

Checking the Worden folder by filename found 11 more pairs: the same column imported from both /signal/worden/ and /signal/worden/old/, under different headlines so the slugs did not collide. Together with the 12 above, 23 of the 219 records in Selections from Leon Worden are second copies. Suggested keeper is the copy on the mirror that the index links.

| file | keep | delete | note |
|---|---|---|---|
| lw092596 | #12542 | #12574 | Identical. Keep worden/lw092596.htm (on the mirror, indexed); old/ copy is crawl only. Headlines "resurface" vs "reappear". |
| lw021997 | #12216 | #12562 | Similarity 0.995. Both files exist and the index links both. Old copy has the Signal headline "Hirohito at root of SCV growth" as a line. Unclear which Leon meant as primary; keeper suggested on the cleaner slug. |
| lw030597 | #12460 | #12558 | old/lw030597.htm is a meta-refresh stub pointing at ../lw030597.htm. Keep 12460. |
| lw081397 | #12550 | #12538 | Identical body, two headlines ("Newhall Hardware celebrates 50th anniversary" indexed, old/; "Big party marks..." worden/). Keep the indexed one; Nathan picks the headline. |
| lw062597 | #12412 | #12540 | Similarity 0.996. Keep old/ (indexed); the other slug misspells "revitalizating". Neither has writtenBy. |
| lw092497 | #12162 | #12536 | Similarity 0.995. Keep old/ (indexed). Two headlines. |
| lw080798ea | #12498 | #12530 | Identical. Keep old/ (on the mirror, indexed). |
| lw072498e | #12502 | #12278 | Similarity 0.994. Keep old/ (on the mirror, indexed). |
| lw101597 | #12262 | #12458 | Similarity 0.99, both files exist and both are indexed, under two headlines ("Q&A from the world of scvleon.com" and "Who was Darius Towsley?"). Unclear: Nathan decides whether Leon meant two entries. 12262 has writtenBy Leon Worden; 12458 has none. |
| lw070997 | #12170 | #12372 | Similarity 0.998. Keep worden/ (on the mirror, indexed); old/ is crawl only. |
| lw071598 | #12152 | #12208 | Similarity 0.938. Keep worden/ (on the mirror, indexed): Leon's corrected text (14,700 acres; footnote correcting the year Cook came to California). The old/ copy is the 1998 text (14,000 acres). Neither has writtenBy. |

Bodies were also compared by hash across articles, photographs, documents and obituaries. Nothing else turned up beyond these pairs, identical captions on distinct photographs, and three election-return documents with the same boilerplate.

