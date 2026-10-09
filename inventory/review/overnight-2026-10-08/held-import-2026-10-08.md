# The held photographs and the held records from the import (overnight, 8 October 2026)

Claude, overnight run, read-only. Nothing written to Craft. For Nathan.

## Which lists these are

TODO.md carries two "held" lists from the import of 7 October:

1. **The 15 photograph records held from the image import** (inventory/review/photo-import-held-2026-10-07.md): the census's picture did not carry the record's code. TODO line 38 marks them done (8 October, afternoon); line 45 still lists them as open.
2. **The "20 photographs left as they are" by the title plan** (TODO line 58; inventory/review/title-plan-2026-10-07.json, "held"): "7 not on Reggie, 4 on multi-piece pages, 9 with no headline found". These are **13 records, not 20**: the 9 "no headline found" include the 7 whose page is not on the mirror (7 + 4 + 2 = 13). The plan's own held list has 13 photographs and the 4 articles.

## What was read

- Craft, as of tonight: every field and every image asset on the 28 records (a scratch script, its reads declared; Craft fields are records about the files, not the files).
- The pictures themselves, by eye, from web/uploads/archive-media/legacy: both envelopes, both 1987 city papers, the 1930 rodeo program cover, the Fort Tejon camels, and a spot check of 5 of the 15 done on 8 October (#5347, #2741, #2939, #2873, #2875). The other 10 were checked by eye on a contact sheet before the write on 8 October; tonight they were checked only by their asset names and paths in Craft.
- The legacy pages on Reggie (/mnt/reggie inside the container), and where a page is not on Reggie, the Internet Archive's captures (CDX index and the captured page, generic User-Agent).

## 1. The 15 held from the image import: all done, nothing open

Every one of the 15 has its pictures in Craft tonight, as the 8 October section of photo-import-held-2026-10-07.md says:

| ID | In Craft tonight |
|---|---|
| 5649 | lw3792a, then b to j (10) |
| 5463 | lw3618b, then c to o (14) |
| 5377 | lw3531 page001, then pages 002 to 016 (16), lw3531.pdf under Documents |
| 5347 | lw3505-p1 (the PDF's cover: Cine-Romanzo, "RAMONA", Dolores del Rio), lw3505.pdf under Documents |
| 4951 | lw3135a_large, then b to f (6) |
| 4893 | lw3086a, then b to d (4), lw3086.pdf under Documents |
| 4413 | johnlscott_2014.jpg |
| 3339 | lw2353.jpg |
| 3321 | lw2342.jpg |
| 2939 | lw2158.jpg (aerial of the golf course), code LW2158 |
| 2875, 2873 | lw2142bb_large, lw2142ba_large (two views of the caretaker's house) |
| 2741 | lw2042.jpg, code LW2042 |
| 2721 | lw20191207scvhs_large.jpg |
| 2703 | lw1501a_orig, then b to h with eb and fb (10) |

The five pictures viewed tonight show what each title says. **TODO line 45 can come off:** it is the same list as line 38, which is done.

One thing to know, not a fault: #2741 "Vasquez Rocks, 1941." is a colour picture with a figure in a yellow jacket. It looks like a later colour postcard. The date is Leon's and stays; it is a lead to check if the date is ever questioned.

## 2. The 13 photographs the title plan left as they are

None of the 13 got a catalogue entry on 7 October: the retitle skipped held records, so catalogueCaption is empty on 12 of them (#4909 got one in batch 2). **Under the 7 October rule, Leon's catalogue entry goes into catalogueCaption before any of these is retitled**, as batch 2 did for #4909.

| ID | Code | Title now | What the source shows | Answer |
|---|---|---|---|---|
| 4909 | LW3097 | India Never Had It So Good | Retitled in titles batch 2 (8 October), with its catalogue entry filled | **Done** |
| 5377 | LW3531 | Souvenir Program: Baker Ranch Rodeo, Under Direction of Hoot Gibson, 4-27-1930. | The cover prints FREE SOUVENIR PROGRAM / BAKER RANCH RODEO / SUNDAY, APRIL 27, 1930 / SAUGUS, CALIF. The census called it multi-piece because the page also transcribes a news report ("Rodeo Draws Great Throng") | **Obvious:** "Baker Ranch Rodeo", by the batch 1 rule (ephemera take the printed cover title; "Program" names the kind of record). It is already in titles-vs-scans-2026-10-07.md ("shortened or extended"), so it belongs in batch 3 or 4 |
| 5671 | LW3808 | Interrupted Mail: Letter (Envelope) Recovered from Fatal Plane Crash, 11-18-1930. | An envelope: Bank of Italy, San Diego Office corner card, postmarked San Diego Nov 17, "AIR MAIL", addressed to Bank of America, San Francisco, stamped "Received in bad condition at San Francisco". It prints no headline. The census's "multi-piece" comes from the news reports transcribed on the same page | **Obvious:** keep the title (batch 1 rule: envelopes, letters and inner pages keep a description); fill the catalogue entry |
| 4791 | LW2998 | the same title as #5671 | A different envelope: Security-First National Bank of Los Angeles corner card, postmarked Los Angeles Nov 17 1930, to United States National Bank, Portland, with the typed postmaster's note about the crash. No headline | **Obvious:** keep, as #5671. The two records carry the same title for two different envelopes. Identical titles already stand elsewhere (#2873 and #2875; #5737 and #5739), so this is not a fault. If Nathan wants them told apart, that is his call; no rule asks for it |
| 4861 | LW3060 | City Formation Committee Kicks Off Voter Registration Program to Get More Funds From State, 12-1-1987. | The scan (lw3060a) prints the head "City Formation Committee Kicks-off Voter Registration Program to Get More Funds From State", under the City of Santa Clarita letterhead and FOR IMMEDIATE RELEASE, December 1, 1987. The census said "no discernible printed title" because it read the page's HTML, which has no heading element, not the scan | **Obvious:** "City Formation Committee Kicks-off Voter Registration Program to Get More Funds From State", as printed (the hyphen is the release's), by the batch 1 rule for ephemera with a printed title |
| 4859 | LW3059 | Arthur Young CPAs Predict 22% Budget Windfall for Proposed City of Santa Clarita, 9-18-1987. | The scan (lw3059a) is a letter: Arthur Young letterhead, September 18, 1987, to the Santa Clarita Incorporation Committee. It prints no headline; the title is Leon's description | **Obvious:** keep (batch 1: letters keep a description); fill the catalogue entry |
| 3023 | LW2248a | Photo Gallery: 1876 Golden Spike. | The census said "page not on mirror": it looked for lw2248.htm, the record's legacyUrl. The page is on Reggie as **lw2248a.htm** (title tag "LW2248a \| Lang \| Photo Gallery: 1876 Golden Spike."), and the Internet Archive's 2019 to 2021 captures of lw2248.htm are the same page. It prints the headline "1876 Lang Station Golden Spike" | **Obvious:** "1876 Lang Station Golden Spike", by the 7 October rule (the title is the headline the page prints). legacyUrl stays lw2248.htm: that was the live address |
| 2987 | LW2214 | Photo Gallery: Sandberg's Summit Hotel Site, 2006. | As #3023: on Reggie as **lw2214a.htm**; the 2019 capture of lw2214.htm is the same page. Headline "Sandberg's Summit Hotel Site" | **Obvious:** "Sandberg's Summit Hotel Site". legacyUrl stays |
| 2913 | LW2152a | Fort Tejon Camels</ | Not on Reggie: lw2152b.htm on Reggie is a 107-byte redirect to lw2152a.htm, which is missing. The only capture of lw2152a.htm is 7 May 2002; it prints "'Fort Tejon Camels'" over "United States Camel Corps", for Vischer's painting of the camels descending into Carson Valley (the picture, lw2152a.jpg, is in Craft and shows it). The "</" in the title is a stray tag fragment | **Obvious:** "'Fort Tejon Camels'", as printed, single quotes kept (7 October typographic rule: every character but a closing full stop stays). The page is known only from a 2002 capture; if Nathan prefers the quotes dropped because they mark a painting's title, that is his call |
| 5735 | LW9410b | Cronan Home on Via Onda, Valencia Hills | Not on Reggie. Captured by the Internet Archive on 15 January 2025 and 19 May 2026; prints "EARTHQUAKES / Cronan Home on Via Onda, Valencia Hills", demolition, January 19, 1994 | **Obvious:** the title is already the printed headline; nothing to change. The picture is item 1 of the Leon request |
| 5737 | LW9410c | Cronan Home Site on Via Onda, Valencia Hills | As #5735; prints "Cronan Home Site on Via Onda, Valencia Hills"; the caption: Patricia and Stuart Cronan in front of the lot | **Obvious:** already the printed headline |
| 5739 | LW9410d | Cronan Home Site on Via Onda, Valencia Hills | As #5735; same headline; the caption: the empty lot, Connie Worden's house at right | **Obvious:** already the printed headline |
| 4475 | LW2763b | Apartments Under Construction, 24514 Kansas Street (Ex-Newhall School Building Site), 2015. | Not on Reggie, and the Internet Archive's index returns no capture of lw2763b.htm tonight (two queries; only the thumbnail lw2763bt.jpg, November 2025). The neighbouring page lw2763a.htm on Reggie carries the same title | **Obvious:** the title stands until the page is found; the picture is item 4 of the Leon request |

So of the 13: 1 done; 3 titles already right; 3 kept as descriptions; 5 retitles with an obvious answer (#5377, #4861, #3023, #2987, #2913); 1 kept until the page turns up. **Nothing here needs a ruling from Nathan beyond his word to apply**, except two optional calls: telling the two envelopes apart (#5671, #4791), and whether #2913 keeps its quotation marks.

## Read from a description

| Record | What was said | What the source shows |
|---|---|---|
| #3023, #2987 | title-census-2026-10-07.json: "page not on mirror" (found by looking up the record's legacyUrl, lw2248.htm and lw2214.htm) | The pages are on Reggie as lw2248a.htm and lw2214a.htm, with the same content as the Internet Archive's captures of the legacyUrl addresses. Each prints a headline |
| #4861 | title census: "no discernible printed title" (read from the page's HTML headings) | The scan prints a headline: "City Formation Committee Kicks-off Voter Registration Program to Get More Funds From State" |
| #2913 | Title "Fort Tejon Camels</" (from a source that carried a broken tag) | The page (2002 capture) prints "'Fort Tejon Camels'" |
| TODO line 58 | "20 photographs left as they are (7 not on Reggie, 4 on multi-piece pages, 9 with no headline found)" | 13 records: the 9 include the 7 |
| photo-import-plan-2026-10-07.md | The Internet Archive holds the Cronan pages "captured as late as December 2025" | The index has captures of lw9410b, c and d dated 19 May 2026 |
