# Valley district maps and corrected shares

Claude (research subagent), 4 October 2026, for Nathan. Research and generated data files only: nothing was written to the database or templates, nothing committed.

- Builder: `scripts/import/build_valley_district_maps.py` (Python 3 standard library; re-runnable from the saved inputs; `--fetch` downloads any missing input).
- Maps: `web/data/valley-districts/<chamber>-<plan>.geojson` (13 files) and `web/data/valley-districts/index.json`.
- Tallies and checks (by district, region and place, per plan): `inventory/review/valley-district-maps-2026-10-04.json`.
- `S/` below is `inventory/sources/legislative-districts-2026-10-04/`. New downloads are listed in `S/manifest.json`.

## What changed in the valley definition

Old: Census Newhall CCD + Agua Dulce CDP (and, on 2000 blocks only, the City plus all of tract 9108 outside the City and Acton).

New, for every plan: **Newhall CCD + Agua Dulce CDP + every census block inside the City of Santa Clarita as that census drew it. Acton stays out.**

| Census | Newhall CCD | City outside the CCD | Agua Dulce | Valley | Old valley |
|---|---|---|---|---|---|
| 2000 (1991 plan) | 189,172 | 10,239 | 3,012 | 202,423 | 211,220 |
| 2010 (2001 and 2011 plans) | 254,330 | 12,778 | 3,342 | 270,450 | 257,672 |
| 2020 (2021 plan, 2025 map) | 273,466 | 13,364 | 3,451 | 290,281 | 276,917 |

- 2010 and 2020: the City and Agua Dulce come from the Census Block Assignment Files (INCPLACE_CDP) of that census. The City totals match the census counts: 176,320 (2010) and 228,673 (2020).
- 2000: the City (151,088, the 2000 count) and the Newhall CCD come from the 2000 PL 94-171 geographic header. Agua Dulce was not a CDP in 2000, so 2000 blocks are counted as Agua Dulce when their interior point lies inside the 2010 Agua Dulce CDP boundary (3,012 people).
- **The 2000 valley gets smaller, not larger.** The old 1991-plan pass counted all of tract 9108 outside the City and Acton (11,809 people) as "Agua Dulce and other". Under the new definition only the Agua Dulce part (3,012) stays; about 8,800 unincorporated residents east of the 2000 City drop out. This is the definition applied as written. Whether Nathan wants those 2000 unincorporated residents (much of that land was later annexed) counted is his call.

## Method

1. **Blocks.** Valley blocks per census as above.
2. **Districts.** Each block assigned by the official block equivalency file for the plan:
   - 1991 plan, 2000 blocks: Statewide Database `block00_district.txt` (`S/swdb/block00_district_txt.zip`).
   - 2001 plan, 2010 blocks: Statewide Database `2010block_2001district_equivalency.dbf`.
   - 2011 plan, 2010 blocks: Statewide Database `2011_*_state_equiv.dbf`.
   - 2021 plan, 2020 blocks: Census Bureau 2022 SLDL and SLDU block equivalency files (`S/census-baf/sldl_2022.zip`, new; `sldu_2022.zip`) and the CD118 file (`S/bef/cd118.zip`). The earlier pass used TIGERweb district polygons for this plan; the equivalency files give the same counts.
   - 2025 congressional map: AB 604 equivalency CSV (`S/bef/AB604_selc_equivalency.csv`).
   - Every valley block was found in every file: 0 unassigned blocks in all 13 plans.
3. **Population.** Block POP100 of that census: 2000 from the PL header; 2010 from TIGERweb tigerWMS_Census2010 layer 18 (`S/census-baf2010/tigerweb_2010_block_pop100_06037.json`, new; county total 9,818,605, which matches the 2010 count); 2020 from the existing TIGERweb file.
4. **Geometry.** TIGER/Line block polygons for Los Angeles County (`S/tiger/`, new: 2000 blocks in the TIGER 2010 edition, 2010 blocks, 2020 PL blocks). No valley block lies in Ventura County. For each district, every ring edge of its valley blocks is collected, edges that appear twice are cancelled, and the rest are chained into rings by face tracing (sharpest right turn, so parts that touch at one vertex come out as separate rings). Outer rings and holes are told apart by orientation, and each hole is placed in the smallest outer ring that contains it (no orphan holes in any file).
5. **Simplification.** Douglas-Peucker at 0.0003 degrees, run once per shared border (the arc between two junction points), so neighbouring districts and the outline keep exactly the same simplified border. No ring falls below 4 points. Coordinates rounded to 5 decimals; GeoJSON outer rings counter-clockwise, holes clockwise. No ring was dropped as degenerate.
6. **Files.** Each GeoJSON is a FeatureCollection: one feature per district (`district`, `label`, `pop`, `share`, `main`) and one `{"role": "outline"}` feature. `index.json` holds the definition, method and per-file district populations and shares.

## Old and new shares

| Chamber, plan | Census | District | Old pop | Old share | New pop | New share |
|---|---|---|---|---|---|---|
| Assembly 1991 | 2000 | **36** | 177,771 | 84.2% | 168,974 | 83.5% |
| | | 38 | 33,449 | 15.8% | 33,449 | 16.5% |
| | | 44 | 0 | 0 | 0 | 0 (land only, 1.0 km²) |
| Assembly 2001 | 2010 | **38** | 222,641 | 86.4% | 235,419 | 87.0% |
| | | 37 | 35,031 | 13.6% | 35,031 | 13.0% |
| | | 36 | 0 | 0 | 0 | 0 (land only, under 0.01 km²) |
| Assembly 2011 | 2010 | **38** | 238,891 | 92.7% | 251,669 | 93.1% |
| | | 36 | 18,781 | 7.3% | 18,781 | 6.9% |
| Assembly 2021 | 2020 | **40** | 271,719 | 98.1% | 285,083 | 98.2% |
| | | 34 | 5,198 | 1.9% | 5,198 | 1.8% |
| Senate 1991 | 2000 | **17** | 177,771 | 84.2% | 168,974 | 83.5% |
| | | 19 | 33,449 | 15.8% | 33,449 | 16.5% |
| | | 21 | 0 | 0 | 0 | 0 (land only, 1.0 km²) |
| Senate 2001 | 2010 | **17** | 187,591 | 72.8% | 200,369 | 74.1% |
| | | 19 | 70,081 | 27.2% | 70,081 | 25.9% |
| Senate 2011 | 2010 | **21** | 202,597 | 78.6% | 215,375 | 79.6% |
| | | 27 | 55,075 | 21.4% | 55,075 | 20.4% |
| Senate 2021 | 2020 | **23** | 276,917 | 100% | 290,281 | 100% |
| House 1991 | 2000 | **25** | 211,220 | 100% | 202,423 | 100.0% |
| | | 27 | 0 | 0 | 0 | 0 (land only, 1.0 km²) |
| House 2001 | 2010 | **25** | 256,607 | 99.6% | 269,385 | 99.6% |
| | | 22 | 1,065 | 0.4% | 1,065 | 0.4% |
| | | 27 | 0 | 0 | 0 | 0 (land only, 0.6 km²) |
| House 2011 | 2010 | **25** | 257,672 | 100% | 270,450 | 100% |
| House 2021 | 2020 | **27** | 276,917 | 100% | 290,281 | 100% |
| House 2025 | 2020 | **27** | 246,532 | 89.0% | 255,552 | 88.0% |
| | | 26 | 22,621 | 8.2% | 22,621 | 7.8% |
| | | 30 | 7,764 | 2.8% | 12,108 | 4.2% |

Where the added City residents went: on 2010 blocks all 12,778 were in AD 38, SD 17 (2001 plan), SD 21 (2011 plan) and CD 25. On 2020 blocks all 13,364 were in AD 40, SD 23 and CD 27 (2021 plan); under the 2025 map 9,020 are in CD 27 and 4,344 in CD 30.

The zero-population districts are slivers of land in the official files (forest-edge blocks with no residents). They are kept as features so the districts tile the outline; a map may hide features with `pop` 0.

## Geometry check

| File | Bytes | Outline km² (raw) | Districts km² (raw) | Outline km² (final) | Districts km² (final) | Grid points inside outline | Gaps | Overlaps |
|---|---|---|---|---|---|---|---|---|
| assembly-1991 | 46,627 | 1381.145 | 1381.145 | 1381.150 | 1381.150 | 42,994 | 0 | 0 |
| assembly-2001 | 41,807 | 1334.794 | 1334.794 | 1334.817 | 1334.817 | 47,670 | 0 | 0 |
| assembly-2011 | 45,247 | 1334.794 | 1334.794 | 1334.999 | 1334.999 | 47,676 | 0 | 0 |
| assembly-2021 | 46,989 | 1342.457 | 1342.457 | 1342.429 | 1342.429 | 47,938 | 0 | 0 |
| senate-1991 | 46,617 | 1381.145 | 1381.145 | 1381.150 | 1381.150 | 42,994 | 0 | 0 |
| senate-2001 | 42,381 | 1334.794 | 1334.794 | 1334.843 | 1334.843 | 47,671 | 0 | 0 |
| senate-2011 | 38,157 | 1334.794 | 1334.794 | 1334.859 | 1334.859 | 47,672 | 0 | 0 |
| senate-2021 | 35,757 | 1342.457 | 1342.457 | 1342.568 | 1342.568 | 47,944 | 0 | 0 |
| house-1991 | 37,581 | 1381.145 | 1381.145 | 1381.204 | 1381.204 | 42,992 | 0 | 0 |
| house-2001 | 37,198 | 1334.794 | 1334.794 | 1334.991 | 1334.991 | 47,674 | 0 | 0 |
| house-2011 | 35,920 | 1334.794 | 1334.794 | 1334.910 | 1334.910 | 47,672 | 0 | 0 |
| house-2021 | 35,762 | 1342.457 | 1342.457 | 1342.568 | 1342.568 | 47,944 | 0 | 0 |
| house-2025 | 46,807 | 1342.457 | 1342.457 | 1342.485 | 1342.485 | 47,941 | 0 | 0 |

- Raw: the dissolved outline's area equals the sum of the block areas, and the dissolved districts sum to the outline exactly, in every vintage. No edge was shared by more than two blocks, and chaining closed every ring with no anomalies.
- Final (simplified and rounded): the districts sum to the outline in every file, and a 300 by 300 grid of test points over each outline found no point inside the outline outside every district (gaps), none in two districts (overlaps), and none covered outside the outline. Simplification moves the total area by at most 0.02%.
- Areas are planar estimates at latitude 34.45 degrees, good to about 0.5% in absolute terms; the equalities above are exact.
- index.json is 6,917 bytes. Every file is far under 400 KB (largest 47 KB).

**Map note.** From 2010 on, the valley as defined is in two parts: Agua Dulce CDP (59 km²) does not touch the Newhall CCD or the City. A strip of unincorporated land in tract 9108 (South Antelope Valley CCD; 963 residents in 2020 in tracts 9108.07 to 9108.10) lies between them. On 2000 blocks the Agua Dulce blocks, being larger, join the main part. The outline feature is therefore a MultiPolygon.

## Earlier statements, rechecked

Hold:
- 2021-plan **AD 34**: Agua Dulce (3,451) and 1,747 people in the Newhall CCD north of the City (Green Valley CDP 1,036, other unincorporated 711). It holds no City residents. It is also most of the valley's land: 829 km² of 1,342, the northern back country from north of Castaic to Agua Dulce. AD 40 holds the whole City (228,673), with Castaic, Stevenson Ranch, Val Verde and Hasley Canyon.
- 2011-plan **AD 36**: its 18,781 residents are the unincorporated northeastern edge (Green Valley 1,027; tracts 9200.28, 9200.16, 9200.15, 9200.20, 9200.32 and neighbours). Refinement for the map: by land it is the whole northern back country, 617 km² of 1,335, from north of Castaic east to Green Valley, so on a map it reads as a northern band, not only a northeastern corner.
- 2025 map **CD 30**: Agua Dulce, the City's eastern and southeastern edge, and 103 unincorporated residents. Acton is out of the valley as defined (it is in CD 30, as before). CD 26: Castaic, Val Verde, Hasley Canyon and 90 others; CD 27 the rest, Green Valley and Stevenson Ranch included.
- 2001-plan SD 19: about 50,000 City residents (50,153). 2011-plan SD 27: about 35,000 (34,930). 2001-plan CD 22: Green Valley only (1,065). Senate 2021, House 2011 and House 2021: one district holds the whole valley.

Change:
1. **House 2025 shares.** CD 27 89% becomes **88.0%**; CD 30 7,764 (2.8%) becomes **12,108 (4.2%)**; CD 26 8.2% becomes 7.8%. CD 30's City part is **8,554 residents**, not "tract 9200.43, about 4,200": it is the City blocks of tracts 9200.43 (4,210), 9108.09 (1,893), 9108.10 (2,394) and 9304.00 (57). The "Sand Canyon area" label stays NEEDS_VERIFICATION.
2. **Assembly 2011, AD 38 description.** "The City except its northeast edge" is not right: AD 38 held the **whole City as it stood in 2010** (176,320). AD 36's valley residents were all outside the City in 2010 (much of that land was annexed later, which is why AD 36 had about 22,700 on 2020 blocks).
3. **1991 plan.** The main seat's share is **83.5%** (168,974), not 84% of 211,220 (rounds the same); AD 38 and SD 19 rise to 16.5%. "Eastern unincorporated" should come out of the AD 36 and SD 17 description, because the unincorporated land east of the 2000 City outside Agua Dulce is no longer counted.
4. **Small share changes, same roles:** Assembly 2001 AD 38 86% to 87%; Senate 2001 SD 17 73% to 74% (SD 19 27% to 26%); Senate 2011 SD 21 79% to 80% (SD 27 21% to 20%); Assembly 2021 AD 40 stays 98%, AD 34 stays 2%.
5. **Valley totals** for the "Valley population" columns: 202,423 (2000), 270,450 (2010), 290,281 (2020).

The legislative-districts and congressional-split review files were not edited; the corrections above are for Nathan to carry into them and into the body pages.

## NEEDS_VERIFICATION

- "Sand Canyon area" as the name for the City's CD 30 part (from the congressional-split note; not checked street by street).
- The 2000 Agua Dulce approximation (interior point in the 2010 CDP boundary) is a method choice, not a census unit.
