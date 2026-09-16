# Places hub

Nathan decides. No Leon gate. No Craft import in this session. Place stubs are specified here and listed in `places-candidates.json`.

## Live Places section

| | |
| --- | --- |
| Section handle | `places` |
| Section type | channel |
| Entry type | `place` |
| URI | `places/{slug}` |
| Configured template | `_entries/places` |
| Entries in local DDEV | 0 |

### Fields on the live layout

Identity: `body`, `placeAddress`, `placeLat`, `placeLng`, `dateEstablished`, `placeAliases`, `placeChlNumber`, `placeFeatured`, `placeScvhlCheckbox`, `culturalSensitivityNote`

Connections: `placePeople`, `placeOrganizations`, `relatedPlaces`, `placeEvents`, `placeArticles`

External: `placeWikipediaUrl`, `placeChlUrl`, `placeScvhlUrl`, `placeLegacyUrl`

Taxonomy: `historicalEra`, `historicalPeriod`, `neighborhood`

Ingest (Task 3): `legacyKey`, `legacyUrl`, `sourcePath`, `legacyHtml`, `legacyCategory`

`hasTitleField` is false. Title is still the Place name on save. `featuredImage` is not on this layout.

Do not paste thumbnail grids into `body`. The index page is a 301 target and a stub, not a copy of the old HTML.

## Neighborhood category vs Place entry

Neighborhood is a Craft category group (`neighborhood`). Local DDEV has 27 terms, including Acton, Agua Dulce, Bouquet Canyon, Canyon Country, Castaic, Newhall, Pico Canyon, Piru, Placerita Canyon, San Francisquito Canyon, Santa Clarita, Saugus, Soledad Canyon, Tejon, Val Verde, Valencia.

Use **neighborhood** as a filter tag on photographs, articles, and Place entries when the term already exists. Do not invent neighborhoods.

Use **Place** as the hub entry: the page at `/places/{slug}` that later collects related photographs and articles.

A Place may also be tagged with a matching neighborhood (Newhall Place plus Newhall neighborhood). Ridge Route, Melody Ranch, Vasquez Rocks, Heritage Junction, and similar named sites are Places even if they are not neighborhood terms.

## CARE and Tataviam

Follow `TATAVIAM_AUDIT.md`. No sacred-site coordinates in Place `placeLat` / `placeLng` or anywhere public. Do not fill geo on these stubs.

Tataviam Culture stays `legacyCategory` on photographs and articles. It is not a Place. Do not create `/places/tataviam` from the object-page category.

## What is not a Place

Topic indexes: `people.htm`, `film.htm`, `maps.htm`, `general.htm`, `new.htm`. Skip. Do not 301 these to Places.

Classifier noise: about 501 of the 554 "indexes" are item-ID pages titled `History In Pictures - AL1941b` and similar. Those are not place indexes. Do not create Place stubs from them.

Not Places: People, Film-Arts, William S. Hart, St. Francis Dam Disaster (Event), Powerhouse Fire (Event), Tataviam Culture.

St. Francis Dam lives on San Francisquito Canyon as a Place, plus an Event later. The index `sp-dam-index.htm` is not a Place stub.

## Mapping: place-named indexes

Create a Place stub if missing. 301 the old URL. Do not copy the thumbnail grid.

| Legacy path | Place slug | Title |
| --- | --- | --- |
| `/scvhistory/acton.htm` | acton | Acton |
| `/scvhistory/asistencia.htm` | estancia | Estancia |
| `/scvhistory/bealescut.htm` | beales-cut | Beale's Cut |
| `/scvhistory/bouquet.htm` | bouquet-canyon | Bouquet Canyon |
| `/scvhistory/carey.htm` | harry-carey-ranch | Harry Carey Ranch |
| `/scvhistory/castaic.htm` | castaic | Castaic |
| `/scvhistory/haskell.htm` | haskell-canyon | Haskell Canyon |
| `/scvhistory/hasley.htm` | hasley-canyon | Hasley Canyon |
| `/scvhistory/melody.htm` | melody-ranch | Melody Ranch |
| `/scvhistory/mojave.htm` | mojave-desert | Mojave Desert |
| `/scvhistory/newhalldt.htm` | newhall | Newhall |
| `/scvhistory/newhall4th.htm` | newhall | Newhall |
| `/scvhistory/newhallsch.htm` | newhall | Newhall |
| `/scvhistory/pico.htm` | pico-canyon | Pico Canyon |
| `/scvhistory/piru.htm` | piru | Piru |
| `/scvhistory/potrero.htm` | potrero-canyon | Potrero Canyon |
| `/scvhistory/rancho.htm` | rancho-san-francisco | Rancho San Francisco |
| `/scvhistory/saugus.htm` | saugus | Saugus |
| `/scvhistory/soledad.htm` | soledad-canyon | Soledad Canyon |
| `/scvhistory/towsley.htm` | towsley-canyon | Towsley Canyon |
| `/scvhistory/valencia.htm` | valencia | Valencia |
| `/scvhistory/valverde.htm` | val-verde | Val Verde |

`pico.htm` is titled Pico Canyon / Mentryville. Mentryville is a separate Place (`mentryville`). Pico Canyon is `pico-canyon`.

## Mapping: object-page title categories

These categories use the same Place slugs. Photographs imported later relate to the Place. They do not each need their own 301.

Newhall (including Newhall Schools / 4th of July variants), Saugus, Acton, Pico Canyon (including Pico-Wiley Canyons), Valencia, Canyon Country, Ridge Route, Rancho Camulos, Melody Ranch, Mojave Desert, San Francisquito Canyon, Soledad Canyon, Castaic, Heritage Junction, Agua Dulce, Placerita Canyon, Saugus Speedway, Mentryville, Lang, Magic Mountain, Piru, Vasquez Rocks, Lake Hughes, City of Santa Clarita, Harry Carey Ranch, Lebec, Bouquet Canyon, Towsley Canyon, Tejon Ranch, Tejon, Fort Tejon, Sleepy Valley, Hasley Canyon.

## Missing templates

| Template | URI | What it lists |
| --- | --- | --- |
| `templates/places/index.twig` | `/places` | All Place entries, title A-Z. Optional neighborhood filter later |
| `_entries/places` | `places/{slug}` | Configured on the section but the file is missing. Add `templates/_entries/places.twig` or change the section to `places/_entry` |

Do not build a map page.

## Candidates

See `places-candidates.json`. 39 unique slugs. Local Craft has 0 Place entries, so none were skipped. No body dumps. No geo.
