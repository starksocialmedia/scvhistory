# The valley, one definition for every census

Claude (research subagent), 4 October 2026, for Nathan. Research and generated data files only: nothing was written to the database or templates, nothing committed.

Nathan's decision (4 October 2026): "include the unincorporated land between the City and Agua Dulce so every census uses the same valley. Recompute every share and tell me which changed."

- Builder: `scripts/import/build_valley_district_maps.py` (new function `add_between`, constant `BETWEEN_TRACTS_2020`).
- Regenerated: `web/data/valley-districts/*.geojson` (13 files) and `index.json`; the builder also rewrites its tallies file `inventory/review/valley-district-maps-2026-10-04.json` (new region `between`).
- This note supersedes the definition, the valley totals, the share table and the "Map note" of `inventory/review/valley-district-maps-2026-10-04.md`, and its remark that about 8,800 unincorporated residents of 2000 drop out (they are back in, see below).

## The definition in plain words

For a public method note:

> The Santa Clarita Valley, as counted here, is the Census Bureau's Newhall census county division, the whole City of Santa Clarita, Agua Dulce, and the unincorporated land between the City and Agua Dulce. Acton is not included. The City is counted as each census drew it. The land between the City and Agua Dulce is fixed once, on the 2020 census map, and the same ground is used for the 2000 and 2010 censuses: an earlier census block is counted when its centre lies inside it.

Technical form (as in the builder and `index.json`):

- Newhall CCD (county subdivision 92110), plus Agua Dulce CDP (place 00450; on 2000 blocks, the blocks whose interior point lies inside the 2010 CDP), plus every City of Santa Clarita block (place 69088) of that census,
- plus the **between ground**: on 2020 blocks, every block of tracts **9108.07, 9108.08, 9108.09, 9108.10 and 9108.14** outside Acton CDP. These are the five 2020 tracts that hold the City's eastern edge and Agua Dulce.
- On 2000 and 2010 blocks, a block not already in is added when its TIGER interior point lies inside the 2020 footprint of those five tracts (outside Acton), unless it is in Acton (the Acton CDP of that census; for 2000 blocks, also inside the 2010 Acton CDP). The builder stops if any incorporated place other than the City falls inside the footprint; none does.

## What was left out, and why

Tract 9108 as a whole is far larger than the land between the City and Agua Dulce. Two parts of it are not in:

- **2020 tract 9108.15** (1,321 unincorporated residents outside Acton, plus 7 in Palmdale): the national forest and Soledad Canyon country south and east of Acton, running east toward Palmdale. It touches the City and Agua Dulce only at corners and does not lie between them. On 2000 blocks it is tracts 9108.05 (outside Acton) and 9108.06; on 2010 blocks 9108.05, 9108.11 and 9108.12.
- **2020 tract 9108.04**: Acton itself, with 8 people outside the CDP on its northern edge.

Included with a flag for Nathan: **tract 9108.14 outside Agua Dulce and Acton (223 people in 2020)**. It is the fringe around the Agua Dulce CDP, on its west (between Agua Dulce and the Newhall CCD), north and southeast. Strictly only its western part lies between the valley and Agua Dulce; I took the whole tract outside Acton because leaving the fringe out leaves notches between Agua Dulce and the rest. Leaving it out would take 223 people (2020) out of AD 34 (2021 plan) and CD 30 (2025 map) and would move no rounded share except AD 34, 1.9% back to 1.8%.

I also tested adding the 2020 footprint of the City outside both the CCD and tract 9108 (City blocks in tracts 9304.00, 9203.12 and others, 66 residents in 2020) to the earlier censuses. It added 30 people on 2010 blocks along the Newhall Pass and the forest edge, put two new one-block districts on the 2011 maps (AD 39, 30 people; CD 29, 11 people), and opened a hole and an island in the 2010 outline. Those blocks are not between the City and Agua Dulce, so they are not added: the City outside the CCD and those five tracts counts only as each census drew it.

## The added ground

| Census | Blocks added | People added | By tract (people) | Valley before | Valley now |
|---|---|---|---|---|---|
| 2000 (1991 plan) | 81 | **3,128** | 9108.10: 1,572; 9108.07: 530; 9108.08: 433; 9108.03 (around Agua Dulce): 345; 9108.09: 248 | 202,423 | **205,551** |
| 2010 (2001 and 2011 plans) | 123 | **1,169** | 9108.07: 458; 9108.09: 303; 9108.13 (around Agua Dulce): 233; 9108.08: 110; 9108.10: 65 | 270,450 | **271,619** |
| 2020 (2021 plan, 2025 map) | 78 | **1,186** | 9108.07: 513; 9108.14 (around Agua Dulce): 223; 9108.09: 322; 9108.08: 88; 9108.10: 40 | 290,281 | **291,467** |

- The 2000 figure is larger because much of that ground was unincorporated in 2000 and inside the City by 2010 (tract 9108.10 alone: 1,572 unincorporated residents in 2000). The ground is the same in all three; what moved was the City line.
- The 2020 strip that separated Agua Dulce from the rest (963 residents in tracts 9108.07 to 9108.10) is now in, and **the outline is one piece with no holes on 2000, 2010 and 2020 blocks** (it was two pieces on 2010 and 2020 blocks).
- Outline areas: 1,433 km² (2000 blocks), 1,383 km² (2010), 1,391 km² (2020). The 2000 outline is about 45 km² larger because 2000 blocks at the forest edges are larger; this was so before the change.

## Old and new shares

Old = the files as of commit c1cb794 (City included, between ground not). Flags: **whole** = the share rounded to a whole percent changed (as written in prose); **tenth** = the one-decimal share in `index.json` and on the pages changed.

| Chamber, plan | District | Old pop | Old share | New pop | New share | Added | Flag |
|---|---|---|---|---|---|---|---|
| Assembly 1991 | **36** | 168,974 | 83.5% | 172,102 | **83.7%** | 3,128 | **whole** (83% to 84%), tenth |
| | 38 | 33,449 | 16.5% | 33,449 | **16.3%** | 0 | **whole** (17% to 16%), tenth |
| | 44 | 0 | 0 | 0 | 0 | 0 | |
| Assembly 2001 | **38** | 235,419 | 87.0% | 236,355 | 87.0% | 936 | |
| | 37 | 35,031 | 13.0% | 35,264 | 13.0% | 233 | |
| | 36 | 0 | 0 | 0 | 0 | 0 | |
| Assembly 2011 | **38** | 251,669 | 93.1% | 252,560 | **93.0%** | 891 | tenth |
| | 36 | 18,781 | 6.9% | 19,059 | **7.0%** | 278 | tenth |
| Assembly 2021 | **40** | 285,083 | 98.2% | 286,046 | **98.1%** | 963 | tenth |
| | 34 | 5,198 | 1.8% | 5,421 | **1.9%** | 223 | tenth |
| Senate 1991 | **17** | 168,974 | 83.5% | 172,102 | **83.7%** | 3,128 | **whole** (83% to 84%), tenth |
| | 19 | 33,449 | 16.5% | 33,449 | **16.3%** | 0 | **whole** (17% to 16%), tenth |
| | 21 | 0 | 0 | 0 | 0 | 0 | |
| Senate 2001 | **17** | 200,369 | 74.1% | 201,538 | **74.2%** | 1,169 | tenth |
| | 19 | 70,081 | 25.9% | 70,081 | **25.8%** | 0 | tenth |
| Senate 2011 | **21** | 215,375 | 79.6% | 216,544 | **79.7%** | 1,169 | tenth |
| | 27 | 55,075 | 20.4% | 55,075 | **20.3%** | 0 | tenth |
| Senate 2021 | **23** | 290,281 | 100% | 291,467 | 100% | 1,186 | |
| House 1991 | **25** | 202,423 | 100% | 205,551 | 100% | 3,128 | |
| | 27 | 0 | 0 | 0 | 0 | 0 | |
| House 2001 | **25** | 269,385 | 99.6% | 270,554 | 99.6% | 1,169 | |
| | 22 | 1,065 | 0.4% | 1,065 | 0.4% | 0 | |
| | 27 | 0 | 0 | 0 | 0 | 0 | |
| House 2011 | **25** | 270,450 | 100% | 271,619 | 100% | 1,169 | |
| House 2021 | **27** | 290,281 | 100% | 291,467 | 100% | 1,186 | |
| House 2025 | **27** | 255,552 | 88.0% | 256,376 | 88.0% | 824 | |
| | 26 | 22,621 | 7.8% | 22,621 | 7.8% | 0 | |
| | 30 | 12,108 | 4.2% | 12,470 | **4.3%** | 362 | tenth |

No district entered or left any plan, and no main seat changed.

## Descriptions that change

`templates/_data/valley-districts.json` still carries the old figures. It is a template data file, so I did not edit it. What it needs:

1. **method**: "the Census Bureau's Newhall division, Agua Dulce and the whole City of Santa Clarita as each census drew it (Acton is left out)" becomes "the Census Bureau's Newhall division, the whole City of Santa Clarita as each census drew it, Agua Dulce, and the unincorporated land between the City and Agua Dulce, fixed on the 2020 census and used for every census (Acton is left out)".
2. **Assembly 1991, 36th**: share 83.5% to **83.7%**, pop 168,974 to **172,102**; covers "The City of Santa Clarita as it then stood, and Agua Dulce" becomes "The City of Santa Clarita as it then stood, Agua Dulce and the unincorporated land between them". **38th**: 16.5% to **16.3%** (pop unchanged).
3. **Senate 1991, 17th**: the same share and pop as the 36th; covers becomes "The City of Santa Clarita as it then stood, Agua Dulce and the unincorporated land between them, joined to the Antelope Valley, Inyo County and parts of Kern and San Bernardino counties". **19th**: 16.5% to **16.3%**.
4. **Assembly 2001**: 38th pop 235,419 to **236,355**, 37th 35,031 to **35,264**; shares unchanged; covers unchanged (the 38th's 936 are the unincorporated strip east of the City, the 37th's 233 the fringe around Agua Dulce).
5. **Assembly 2011**: 38th 93.1% to **93.0%** (pop **252,560**), 36th 6.9% to **7.0%** (pop **19,059**). Covers hold. The "together" text ("93 per cent", "northeastern 7 per cent") holds.
6. **Assembly 2021**: 40th 98.2% to **98.1%** (pop **286,046**; its 963 are the strip between the City and Agua Dulce). 34th 1.8% to **1.9%** (pop **5,421**); covers "Agua Dulce and about 1,700 people in the unincorporated land north of the City" becomes "Agua Dulce and about 2,000 people in the unincorporated land around it and north of the City" (1,747 north of the City and 223 around Agua Dulce).
7. **Senate 2001**: 17th 74.1% to **74.2%** (pop **201,538**), 19th 25.9% to **25.8%**. Covers hold.
8. **Senate 2011**: 21st 79.6% to **79.7%** (pop **216,544**), 27th 20.4% to **20.3%**. Covers hold.
9. **House 2001**: 25th pop 269,385 to **270,554**; shares and covers hold.
10. **House 2025**: 27th pop **256,376** (88.0% holds), 30th 4.2% to **4.3%**, pop **12,470**. The 30th's covers ("Agua Dulce and the City's eastern and southeastern edge, about 8,600 City residents") holds: its City part is still 8,554; its unincorporated part rises from 103 to 465. The House note ("the 27th keeps 88 per cent") holds.
11. **Whole-valley pops**: Senate 2021 23rd and House 2021 27th 290,281 to **291,467**; House 1991 25th 202,423 to **205,551**; House 2011 25th 270,450 to **271,619**.

## Geometry check

| File | Bytes | Outline km² (raw) | Districts km² (raw) | Outline km² (final) | Districts km² (final) | Grid points inside | Gaps | Overlaps |
|---|---|---|---|---|---|---|---|---|
| assembly-1991 | 41,433 | 1433.389 | 1433.389 | 1433.468 | 1433.468 | 44,612 | 0 | 0 |
| assembly-2001 | 36,852 | 1383.499 | 1383.499 | 1383.516 | 1383.516 | 49,368 | 0 | 0 |
| assembly-2011 | 44,440 | 1383.499 | 1383.499 | 1383.649 | 1383.649 | 49,365 | 0 | 0 |
| assembly-2021 | 42,480 | 1390.532 | 1390.532 | 1390.471 | 1390.471 | 49,610 | 0 | 0 |
| senate-1991 | 41,423 | 1433.389 | 1433.389 | 1433.468 | 1433.468 | 44,612 | 0 | 0 |
| senate-2001 | 37,042 | 1383.499 | 1383.499 | 1383.491 | 1383.491 | 49,367 | 0 | 0 |
| senate-2011 | 32,936 | 1383.499 | 1383.499 | 1383.516 | 1383.516 | 49,368 | 0 | 0 |
| senate-2021 | 30,774 | 1390.532 | 1390.532 | 1390.606 | 1390.606 | 49,616 | 0 | 0 |
| house-1991 | 32,254 | 1433.389 | 1433.389 | 1433.518 | 1433.518 | 44,612 | 0 | 0 |
| house-2001 | 31,850 | 1383.499 | 1383.499 | 1383.639 | 1383.639 | 49,373 | 0 | 0 |
| house-2011 | 30,620 | 1383.499 | 1383.499 | 1383.563 | 1383.563 | 49,372 | 0 | 0 |
| house-2021 | 30,779 | 1390.532 | 1390.532 | 1390.606 | 1390.606 | 49,616 | 0 | 0 |
| house-2025 | 45,021 | 1390.532 | 1390.532 | 1390.494 | 1390.494 | 49,611 | 0 | 0 |

- Every file: the districts tile the outline (raw and final areas equal; 300 by 300 test grid with no gaps, no overlaps, nothing covered outside the outline), no unassigned blocks, no chain anomalies, no orphan holes, no dropped rings.
- Largest file 45 KB; index.json 7,296 bytes. All far under 400 KB.
- Every outline is now a single Polygon with no holes.

## Change, 4 October 2026: the 9108.14 fringe dropped

Nathan's decision: drop the fringe of tract 9108.14 around Agua Dulce (only its western part lies between the City and Agua Dulce; if it cannot be separated cleanly, drop all of 9108.14 outside Agua Dulce).

**It does not separate cleanly, so all of 9108.14 outside Agua Dulce is dropped.** The fringe is 39 blocks (223 people in 2020). The blocks west of Agua Dulce that touch the Newhall CCD or tract 9108.08 hold 30 people, but they run without a break into the blocks north of Agua Dulce (134 people), which touch tract 9200.50 in the Antelope Valley rather than the valley. No line through them follows a census, tract or place boundary.

- Builder: `BETWEEN_TRACTS_2020` keeps its five tracts, and the new constant `AD_ONLY_TRACTS_2020 = ('910814',)` keeps only the Agua Dulce CDP blocks of 9108.14 in the footprint. The 2000 and 2010 blocks are again counted when their interior point lies in that footprint.
- **One enclave filled (new function `fill_enclaves`).** With the fringe out, the 2000 outline had a hole: six 2000 blocks (11 people, tract 9108.03) west of Agua Dulce, wholly enclosed by the 2000 Agua Dulce (drawn from the 2010 CDP) and the Newhall CCD. On 2020 blocks this is the western fringe (blocks 9108.14 2071 and 2072). The builder now counts any unincorporated block the valley wholly encloses, and stops if one is in an incorporated place. Only these six blocks qualify; no 2010 or 2020 block is enclosed. I recommend keeping this rule: without it the 1991 maps show a hole near Agua Dulce. Remove `fill_enclaves(V)` from `valley_blocks()` to undo it (the 1991 valley would then be 205,243).
- Regenerated: `web/data/valley-districts/*.geojson` (13 files), `index.json` (7,294 bytes), and the tallies in `inventory/review/valley-district-maps-2026-10-04.json` (new region `enclave`).

### The definition in plain words, as it now stands

> The Santa Clarita Valley, as counted here, is the Census Bureau's Newhall census county division, the whole City of Santa Clarita, Agua Dulce, and the unincorporated land between the City and Agua Dulce. Acton is not included. The City is counted as each census drew it. The land between the City and Agua Dulce is fixed once, on the 2020 census map, and the same ground is used for the 2000 and 2010 censuses: an earlier census block is counted when its centre lies inside it. Unincorporated land the valley wholly surrounds is counted.

Technical: on 2020 blocks, the between ground is tracts 9108.07, 9108.08, 9108.09 and 9108.10 outside Acton CDP, plus the Agua Dulce CDP blocks of 9108.14.

### The added ground, now

| Census | Between ground before | Between ground now | By tract now (people) | Valley before | Valley now | Change |
|---|---|---|---|---|---|---|
| 2000 (1991 plan) | 3,128 | **2,820 + 11 enclave** | 9108.10: 1,572; 9108.07: 530; 9108.08: 433; 9108.09: 248; 9108.03: 37 (+11 enclave) | 205,551 | **205,254** | -297 |
| 2010 (2001 and 2011 plans) | 1,169 | **936** | 9108.07: 458; 9108.09: 303; 9108.08: 110; 9108.10: 65 | 271,619 | **271,386** | -233 |
| 2020 (2021 plan, 2025 map) | 1,186 | **963** | 9108.07: 513; 9108.09: 322; 9108.08: 88; 9108.10: 40 | 291,467 | **291,244** | -223 |

The outline is one polygon with no holes on 2000, 2010 and 2020 blocks. Outline areas: 1,420 km² (2000 blocks; was 1,433), 1,360 km² (2010; was 1,383), 1,367 km² (2020; was 1,391).

### Every share that moves (one decimal)

Before = the table above ("Old and new shares", New columns). Every other district keeps its share at one decimal.

| Chamber, plan | District | Pop before | Share before | Pop now | Share now | Whole percent |
|---|---|---|---|---|---|---|
| Assembly 2001 | **38** | 236,355 | 87.0% | 236,355 | **87.1%** | holds (87%) |
| | 37 | 35,264 | 13.0% | 35,031 | **12.9%** | holds (13%) |
| Assembly 2011 | **38** | 252,560 | 93.0% | 252,557 | **93.1%** | holds (93%) |
| | 36 | 19,059 | 7.0% | 18,829 | **6.9%** | holds (7%) |
| Assembly 2021 | **40** | 286,046 | 98.1% | 286,046 | **98.2%** | holds (98%) |
| | 34 | 5,421 | 1.9% | 5,198 | **1.8%** | holds (2%) |

So the Assembly 2011 and 2021 shares return to what they were before the between ground was added (as of commit c1cb794). Assembly 2001 does not: it is now 87.1% and 12.9%, where it was 87.0% and 13.0% both before and after the between ground, because the 38th keeps the strip east of the City while the 37th loses the fringe. No whole-percent figure moves, no district enters or leaves a plan, and no main seat changes.

Populations that move with no share change:

| Chamber, plan | District | Pop before | Pop now |
|---|---|---|---|
| Assembly 1991 | 36 | 172,102 | 171,805 (83.7% holds) |
| Senate 1991 | 17 | 172,102 | 171,805 (83.7% holds) |
| Senate 2001 | 17 | 201,538 | 201,305 (74.2% holds) |
| Senate 2011 | 21 | 216,544 | 216,311 (79.7% holds) |
| Senate 2021 | 23 | 291,467 | 291,244 (100%) |
| House 1991 | 25 | 205,551 | 205,254 (100%) |
| House 2001 | 25 | 270,554 | 270,321 (99.6% holds) |
| House 2011 | 25 | 271,619 | 271,386 (100%) |
| House 2021 | 27 | 291,467 | 291,244 (100%) |
| House 2025 | 27 | 256,376 | 256,153 (88.0% holds) |

Correction to the note above: the 223 fringe people of 2020 were in **CD 27** on the 2025 map, not CD 30. CD 30 keeps 12,470 (4.3%); the 27th loses them.

### Descriptions that change (templates/_data/valley-districts.json, not edited)

Against the list "Descriptions that change" above:

1. **method**: add "unincorporated land the valley surrounds is counted" if the enclave rule stays; the rest of the wording holds ("the unincorporated land between the City and Agua Dulce, fixed on the 2020 census").
2. **Assembly 1991 36th** and **Senate 1991 17th**: pop **171,805** (83.7% holds). **38th / 19th** unchanged.
3. **Assembly 2001**: 38th **87.1%** (pop 236,355), 37th **12.9%** (pop **35,031**). The 37th's covers no longer includes a fringe around Agua Dulce.
4. **Assembly 2011**: 38th **93.1%** (pop **252,557**), 36th **6.9%** (pop **18,829**).
5. **Assembly 2021**: 40th **98.2%** (pop 286,046), 34th **1.8%** (pop **5,198**); covers back to "Agua Dulce and about 1,700 people in the unincorporated land north of the City" (1,747).
6. **Senate 2001** 17th pop **201,305**; **Senate 2011** 21st pop **216,311**; shares hold.
7. **House 2001** 25th pop **270,321**; **House 2025** 27th pop **256,153** (88.0% holds), 30th unchanged (12,470, 4.3%; its covers holds).
8. **Whole-valley pops**: Senate 2021 23rd and House 2021 27th **291,244**; House 1991 25th **205,254**; House 2011 25th **271,386**.

### Geometry check, after the change

| File | Bytes | Outline km² (raw) | Districts km² (raw) | Outline km² (final) | Districts km² (final) | Grid points inside | Gaps | Overlaps |
|---|---|---|---|---|---|---|---|---|
| assembly-1991 | 43,181 | 1420.179 | 1420.179 | 1420.239 | 1420.239 | 44,209 | 0 | 0 |
| assembly-2001 | 39,071 | 1360.374 | 1360.374 | 1360.382 | 1360.382 | 48,589 | 0 | 0 |
| assembly-2011 | 43,861 | 1360.374 | 1360.374 | 1360.543 | 1360.543 | 48,593 | 0 | 0 |
| assembly-2021 | 44,771 | 1367.413 | 1367.413 | 1367.353 | 1367.353 | 48,828 | 0 | 0 |
| senate-1991 | 43,171 | 1420.179 | 1420.179 | 1420.239 | 1420.239 | 44,209 | 0 | 0 |
| senate-2001 | 39,430 | 1360.374 | 1360.374 | 1360.397 | 1360.397 | 48,588 | 0 | 0 |
| senate-2011 | 35,243 | 1360.374 | 1360.374 | 1360.419 | 1360.419 | 48,589 | 0 | 0 |
| senate-2021 | 33,113 | 1367.413 | 1367.413 | 1367.505 | 1367.505 | 48,832 | 0 | 0 |
| house-1991 | 34,005 | 1420.179 | 1420.179 | 1420.287 | 1420.287 | 44,207 | 0 | 0 |
| house-2001 | 34,284 | 1360.374 | 1360.374 | 1360.555 | 1360.555 | 48,592 | 0 | 0 |
| house-2011 | 33,008 | 1360.374 | 1360.374 | 1360.469 | 1360.469 | 48,589 | 0 | 0 |
| house-2021 | 33,118 | 1367.413 | 1367.413 | 1367.505 | 1367.505 | 48,832 | 0 | 0 |
| house-2025 | 44,862 | 1367.413 | 1367.413 | 1367.409 | 1367.409 | 48,831 | 0 | 0 |

- Every file: the districts tile the outline (raw and final areas equal; 300 by 300 test grid with no gaps, no overlaps, nothing covered outside the outline), no unassigned blocks, no chain anomalies, no orphan holes, no dropped rings.
- Every outline is a single Polygon with no holes. Largest file 45 KB.
