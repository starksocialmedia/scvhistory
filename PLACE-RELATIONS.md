# Place relations

Local DDEV, 2026-09-16. Only obvious same-place links. No new stubs. No Cloudways.

## Dump before wiring

- Persons: 33. `personOrganizations` empty on all.
- Organizations: 14.
- Places: 39. `placePeople`, `placeOrganizations`, `relatedPlaces` empty on all.
- Friends of Mentryville is not an Organization entry.
- `rancho-camulos` and `rancho-san-francisco` exist as both Place and Organization.

## Linked

### Place people (`placePeople`)

| Place | Person |
| --- | --- |
| newhall | henry-mayo-newhall, arthur-b-perkins, jerry-reynolds, leon-worden, john-gifford |
| saugus | henry-mayo-newhall |
| placerita-canyon | francisco-lopez, abel-stearns |
| rancho-camulos | ygnacio-del-valle, juventino-del-valle |
| rancho-san-francisco | antonio-del-valle, ygnacio-del-valle, juventino-del-valle, henry-mayo-newhall |
| pico-canyon | henry-clay-wiley, jerry-reynolds |
| mentryville | leon-worden |
| beales-cut | edward-fitzgerald-beale |
| vasquez-rocks | tiburcio-vasquez |
| fort-tejon | edward-fitzgerald-beale |

There is no Person field for Places. Reverse is Place `placePeople` only.

### Place organizations (`placeOrganizations`)

| Place | Organization |
| --- | --- |
| rancho-camulos | rancho-camulos (org) |
| rancho-san-francisco | rancho-san-francisco (org) |
| tejon-ranch | rancho-el-tejon |
| santa-clarita | city-of-santa-clarita |

### relatedPlaces

| Place | Related place |
| --- | --- |
| mentryville | pico-canyon |
| pico-canyon | mentryville |

### Person organizations (`personOrganizations` and `orgAssociatedPersons`)

| Person | Organization |
| --- | --- |
| ygnacio-del-valle | rancho-camulos, rancho-san-francisco |
| antonio-del-valle | rancho-camulos, rancho-san-francisco |
| juventino-del-valle | rancho-camulos, rancho-san-francisco |
| henry-mayo-newhall | rancho-san-francisco |
| leon-worden | scvhistory-com-santa-clarita-valley-history |

## Unlinked

| Item | Why skipped |
| --- | --- |
| Friends of Mentryville | Organization missing |
| dante-acosta, scott-wilk, rodolfo-acosta | Newhall / Valencia / Canyon Country mentions are civic or thin |
| andres-pico | Body does not name Pico Canyon |
| remi-nadeau-i | Vasquez mention is not clearly Vasquez Rocks |
| pedro-fages, jose-antonio-aguirre | Tejon mention, which Place is unclear |
| henry-mayo-newhall-memorial-hospital, Signal, Herald, Chamber, Water | Not a single Place |
| leon-worden to placerita-canyon or rancho-camulos | Historian coverage, not of the place |
| Acton, Valencia, Canyon Country Places | No unique Person or Org tie |

## Counts after save

- Persons: 33 (unchanged)
- Places: 39 (unchanged)
- Places with any people: 10
- Places with any orgs: 4
- relatedPlaces pairs: 1 (two-way)
- `/places/newhall` lists five people
- `/places/mentryville` lists Leon Worden and related place Pico Canyon
