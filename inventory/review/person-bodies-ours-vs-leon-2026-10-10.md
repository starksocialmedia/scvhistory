# Person bodies: our writing against Leon Worden's, 10 October 2026

Claude, for Nathan. Read only: nothing in the database, templates or git was changed.

## What was read

- **The database (DDEV), read only.** Every entry in section `persons`, all statuses: its `body`, `bodyAuthorship`, `footnotes` rows, `editorNotes` rows, `authorBio`, `researchLeads`, `personWebmasterNoteTop`/`Bottom`, `personFinePrint`, `legacyHtml`, `personLegacyUrl`, `legacyUrl`, `sourcePath`, `recordProvenance`, `articlesAbout`. Trashed persons were counted separately. The persons field layout was read from the entry type. The bodies of the 270 articles, documents and collections whose `writtenBy` is Leon Worden were read for a second comparison.
- **The Reggie mirror** (`/mnt/reggie/scvhistory.com` in the web container): all 33,705 text pages (`.htm`, `.html`, `.shtml`, `.txt`, `.php`, `.asp`), each read as UTF-8 and, failing that, latin-1 (so the ISO-8859-1 pages are not skipped; DEPLOY-RUNBOOK section 8). By hand: lw2052.htm, ap1335.htm, ap1335a.htm, rn7301.htm, signal/reynolds/part15.html.
- **The repo**: `inventory/wp_content.json` (the 2026 WordPress site's 91 posts); the 97 scripts in `scripts/import` (and root `*.php`) that write both `body` and `bodyAuthorship`; `docs/PROFILES.md`; `templates/persons/_entry.twig`, `_partials/body-authorship.twig`, `_partials/record/source-link.twig`; CHANGELOG, ERRORLOG, `inventory/review/double-counting-audit-2026-10-04.md`.
- Not read: Leon's photograph captions held in Craft (only `writtenBy` records with a body were compared in Craft; the mirror pass covers the pages those captions came from). Wikipedia and other outside text were not compared.

Scratch: `storage/runtime/scratch/pb_*.php`, `pb_shingle.py`, `pb_detail.py`; outputs `pb_persons.json`, `pb_mirror_hits.json`, `pb_detail.json`, `pb_leon_hits.json`, `pb_scripts.json`.

## 1. How many person records

| | count |
|---|---|
| All statuses (not trashed) | 254 |
| Live | 254 |
| Disabled, pending, expired | 0 |
| Drafts | 0 |
| In the trash (not counted above) | 88, of which 3 carry a body, all `wordpress-import-unsourced` |

## 2. Which field is the body, and how many have one

The person type has one prose body field, **`body`** (PlainText, holding HTML). It is what the page prints, and only when `bodyAuthorship` is `legacy-leon` or `editorial-2026` (`_partials/body-authorship.twig`). Counted below: `body` only.

Other prose on the layout, not counted:

| field | records with text | words | renders? |
|---|---|---|---|
| `authorBio` | 30 | 1,555 | only as a fallback where `body` is empty; all 30 also have a body, so it never prints today. It is the WordPress site's short bio (add_body_authorship_field.php) |
| `editorNotes` (table) | 241 | not counted | yes, as notes on the page |
| `researchLeads` | 8 | 724 | no |
| `personWebmasterNoteBottom` | 2 | 51 | yes |
| `personFinePrint` | 1 | 7 | yes |
| `personWebmasterNoteTop`, `legacyHtml` | 0 | 0 | |
| `footnotes` (table) | on all 109 editorial bodies | not counted | yes |

**118 of 254 records have body text; 136 have none** (and no `bodyAuthorship` value).

## 3. Classification

### Signals used (each body was tested on all of them)

1. **Mirror match.** Every 12-word run of the body (tags, entities and footnote markers stripped, lower-cased, punctuation dropped) looked up in every 12-word run of all 33,705 mirror pages. For each body with a hit, the matched words were mapped back and each matched span marked as inside or outside quotation marks.
2. **Leon's records in Craft.** The same 12-word test against the 270 records with Leon as `writtenBy`.
3. **The `bodyAuthorship` label** and `recordProvenance`.
4. **Footnotes**: count of `footnotes` rows and `[n]` markers in the body.
5. **The writing script**: a script in `scripts/import` that writes `body` and `bodyAuthorship` and names the record's id, or the script named in `recordProvenance`.
6. **`legacyUrl`/`personLegacyUrl`**, and for the WordPress bodies a comparison with `inventory/wp_content.json`.

Rule for carry-over: an unquoted matched span of 25 words or more from a Leon page, or more than 20 percent of the body unquoted from one, would make a body "mixed" or "Leon's". Short fact-carrying phrases (a title, an office, a date and place) are noted, not counted as carried prose.

### Result

| class | records | words | label | footnotes | mirror carry-over |
|---|---|---|---|---|---|
| **Ours, written from sources** | 109 | 32,327 | all `editorial-2026` | every one (min. 1 row) | none above the line; see below |
| **Leon's prose, carried over** | 2 | 1,344 | `legacy-leon` | none | 99 to 100 percent |
| **Mixed** | 0 | 0 | | | |
| **Cannot tell (author not established)** | 7 | 2,313 | `wordpress-import-unsourced` | none | none (one 12-word phrase) |
| total | 118 | 35,984 | | | |

**Ratio, ours to Leon's: 32,327 to 1,344 words, about 24 to 1** (109 bodies to 2). Of the bodies that publish (111), Leon's are 4 percent of the words. Inside our 109 bodies, 285 matched words sit in quotation marks; some are Leon quoted with his name (Serra #299, Hart #16356), the rest are other writers. So quoted Leon text in our bodies is at most about 300 words, attributed.

Words are whitespace-separated tokens with tags stripped (footnote markers stay attached to their word).

### Ours, written from sources: 109

All four of the independent signals agree on every one: labelled `editorial-2026`; footnoted; a named writing script for every record (91 by id, the other 18 through the script in `recordProvenance`, all of which exist); and no carried-over prose on the mirror. 77 share no 12-word run with any mirror page. The 32 that do share only phrases: 693 matched words in all, 285 of them in quotation marks. The longest unquoted run is 22 words (Ruth Newhall #15477, from lw3031: "and one of the founders of the Santa Clarita Valley Historical Society ... born Ruth Waldo in Berkeley in 1910"). The highest unquoted share is Thomas O. Larkin #311, 14 of 51 words, an attributed paraphrase ("A.B. Perkins wrote that ...") of Perkins, not Leon. The other phrase overlaps are with City biographies carried on Leon's pages (McLean #23085, 51 words in four phrases from sc1313; Ferry, Kellar, Acosta), obituaries (Darcy #16140, Adams #28667), Glenn 1974 (Vasquez #285) and Leon's pages (Reynolds #281, 28 words from lw2184 in two phrases; Gelcich #16439; Koontz #23081; Pederson; Rasmussen). Jerry Reynolds #281, labelled `mixed` on 22 September, has since been rewritten: what is left of lw2184 is two phrases.

IDs: 279, 281, 285, 287, 291, 297, 299, 303, 307, 309, 311, 315, 317, 323, 327, 331, 333, 337, 339, 341, 343, 2585, 2588, 2591, 2594, 15477, 15737, 15808, 15874, 15919, 15929, 15985, 16140, 16356, 16380, 16396, 16418, 16432, 16439, 18616, 18648, 18663, 18702, 18714, 18726, 18747, 18791, 18820, 18834, 18869, 20224, 20226, 21582, 21584, 21944, 21946, 23081, 23083, 23085, 23087, 23089, 23091, 23093, 25191, 25389, 25391, 25399, 25407, 25409, 25449, 26946, 28132, 28316, 28322, 28336, 28558, 28667, 28928, 29100, 29104, 29109, 29113, 29203, 29208, 29214, 29284, 29288, 29314, 29316, 29318, 29320, 29328, 29332, 29334, 29336, 29446, 29450, 29452, 29456, 29458, 29460, 29462, 29464, 29466, 30546, 31354, 31431, 31728, 31730.

### Leon's prose, carried over: 2

- **Ygnacio del Valle #293**, 872 words. Every word on lw2052.htm (872 of 872). Set verbatim from LW2052 by relabel_ygnacio_del_valle.php on Nathan's word of 3 October; an "About this text" note names Leon and the page; four "Correction, 2026" notes correct it without rewriting it. No footnotes. Shares no 12-word run with Reynolds chapter 15: the partition figures are the same, the wording is not.
- **Henry Mayo Newhall #283**, 472 words (480 tokens as counted by the matcher). 473 of 480 on ap1335.htm, ap1335a.htm and rn7301.htm, the same text on all three. The WordPress import carried it in; relabel_person_bodies.php set `legacy-leon` on 23 September from a 93 percent six-word overlap with ap1335a. No footnotes, no note.

### Cannot tell: 7

Author not established, by the archive's own rule (docs/PROFILES.md: "Where authorship cannot be established, the body stays hidden"). None publishes. They are not Leon's: none shares more than one 12-word run with the mirror (Garcés #289, one phrase from Reynolds part 10). Six are byte-identical to their post in `inventory/wp_content.json`, the 2026 WordPress site (Manly #321, Marshall #319, Bryant #313, Anza #301, Portolá #295; Garcés #289 is identical but for one dropped sentence). Scott Wilk #335 is the WordPress text cut down by our own scripts (trim_wilk_to_public_life.php, fix_sb634_two_bodies_2026_10_04.php), 73 percent similar to the post. They are written in a narrative voice with no citations, and nothing records who wrote them. If they count as "ours" (written for Nathan's WordPress site), the ratio becomes 34,640 to 1,344, about 26 to 1.

IDs: 289, 295, 301, 313, 319, 321, 335.

## Spot-checks

- **Ours (10 drawn at random, seed 20261010):** Christy Smith #25389, Tim Burkhart #29104, Pauline Harte #2594, Junípero Serra #299, Kevin McCarthy #29466, Henry Clay Wiley #331, Tiburcio Vasquez #285, Adrian W. Adams #28667, Gary Murr #25399, Frank Ferry #23083. Read in full with their footnotes: all ten are our prose, claim by claim footnoted to records, CEDA rows, the City's pages or newspapers. Where Leon is used he is cited or quoted by name (Serra: 'As Leon Worden writes, "We have no reason to believe Serra ever set foot" here'; Harte, Ferry, Vasquez cite his pages in footnotes). Murr rests on one obituary and paraphrases it without sharing a 12-word run. **10 of 10 confirmed.**
- **Leon's (both, there being two):** read against lw2052 and ap1335a side by side. Both confirmed as the legacy site's text. On authorship, see the findings.
- **Mixed:** none to check. In its place, the ten editorial bodies with the most mirror overlap were read span by span (Couts, McLean, Darcy, Hart, Ruth Newhall, Adams, Reynolds, Gelcich, Connie Worden, Koontz). In each the overlap is quotation or a fact phrase inside our own sentences. **None is mixed.**
- **Cannot tell (all seven):** read. All are WordPress narrative, no citations, no byline. **7 of 7 confirmed.**

## Limits of the method

- 7 bodies, 2,313 words, are cannot-tell: they are not Leon's and not footnoted writing from sources, but nothing establishes who wrote them.
- A 12-word run catches copying, not close paraphrase. A body that reworded Leon sentence by sentence would pass as ours. The spot-checks and the footnotes are the guard here, not the matcher.
- The test finds Leon's prose only where it is on the mirror or in a Craft record he wrote. His Signal columns not on the mirror, and anything only in print, were not compared.
- "Leon's" here means "on Leon's site". Neither of the two legacy pages carries a byline; see the first finding.
- Text compared to the mirror includes pages Leon carried but did not write (City biographies, obituaries, Perkins, Reynolds). Overlaps with those were counted as overlap, not as Leon's.

## Findings (not fixed)

1. **Henry Mayo Newhall #283 publishes Leon's site's prose with no attribution in or beside it.** Nothing on the record names him or anyone: no "About this text" note (del Valle #293 has one), no byline, no footnote. The only pointer is the sidebar box "On the legacy site: Read the original page" (`source-link.twig`, from `personLegacyUrl` /scvhistory/ap1335a.htm). docs/PROFILES.md's queue says this text was to move to an article under Leon's byline, with the body becoming a profile that cites it; that has not been done. The page's own credit line, "Information from 'A California Legend: The Newhall Land and Farming Company' by Ruth Waldo Newhall (1992)", is not carried on the record either.

2. **Candidate seventh instance: #283's authorship taken from its label, not from its page.** relabel_person_bodies.php (23 September) measured where the text appears and set `legacy-leon`, defined in add_body_authorship_field.php as "the legacy SCVHistory site's own writing". docs/PROFILES.md then calls it "Leon's own, 2,869 chars" and plans "an article by Leon Worden". But the pages (ap1335, ap1335a, rn7301) carry no byline, and their one statement about the text says its information is from Ruth Waldo Newhall's 1992 book. The "Leon wrote this" point rests on a description of the source (the label), not on the source (the page). Leon may well have written it: unsigned text on his site usually is his, and "information from" suggests his summary of the book rather than her words. But no page says so, and the byline the plan would add is not on the page. Written up, not fixed. Del Valle #293 rests on less (an LW-numbered page, also unsigned, and Nathan's ruling of 3 October made with the page in hand), so it is not counted here.

3. **Side observation, checked and not an instance.** docs/PROFILES.md and double-counting-audit-2026-10-04 say LW2052 repeats Reynolds chapter 15 "word for word" on the del Valle partition. The figures (13,599, 21,307, 4,684 acres) are identical. The sentences are not. The audit says "on this point", so the claim holds as written.
