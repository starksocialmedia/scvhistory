# Site proposals, 2 October 2026

The items from the 24-item list that are proposals or are blocked, each with one recommendation.

## 14. The war memorial index

**What is there.** 54 records grouped by conflict in date order (World War I to the War on Terror), within a conflict by year of death and then by name, with no filter or search. Since today, 38 cards show a portrait, family photograph, grave or memorial-wall image; 16 records have no image at all.

**Why it is hard to use.** It is one long scroll. A reader looking for one name has to know the conflict, and nothing on a card says where the person was from or how old they were.

**Recommendation.** Keep the memorial reading in order, and add four things:

1. **A conflict bar** at the top: World War I (n), World War II (n), Korea (n), Vietnam (n), War on Terror (n). Each is a jump link. It is not a filter, because a memorial should be read whole.
2. **A find-a-name box** that scrolls to the card and highlights it, entirely client side.
3. **Community and age on each card**, from wmHomeOfRecord and wmAgeAtLoss. Most records hold both (4 and 3 records are missing them).
4. **A roll view**: one line per person, conflict, rank, branch, date of loss and community, for printing and for reading aloud on Memorial Day.

The audit (inventory/review/war-memorial-audit.md) shows what each record lacks. Every record lacks footnoted sources; 42 lack birthplace, high school and awards; 16 lack any image.

## 17. Electoral districts: under Places or Civic

**What is there.** The districts are place records (placeType district, with districtKind and districtNumber): city council districts, school trustee areas and water divisions.

**Recommendation.** Keep the records as places, because a district is a territory with a boundary, and give them a home in Civic: a **Districts** page under the Civic menu, grouped by body (City Council, each school board, SCV Water). Each body's page links to its districts. Remove the "Electoral districts" group from /places. This changes navigation, not the data model.

## 18 and 10. District boundary maps (and the election-page map)

**Blocked on data.** No public source has Santa Clarita's council-district boundaries. The County's political-boundaries service has supervisorial, congressional, state and Los Angeles City council districts, but not Santa Clarita's. The City's ArcGIS hub (data-santa-clarita.hub.arcgis.com) answers 401 without a login.

**What is needed.** The City's council-district file as GeoJSON or a shapefile. The school trustee areas and SCV Water divisions are needed the same way, from the districts or the County Registrar. With the files, a district page draws its boundary, and an election page draws its district, with a few hours of template work.

## 19. An address lookup (which council, school and water districts am I in?)

**What it would take.** The three boundary sets in 18, then a point-in-polygon lookup. Done client side, the page geocodes the address and tests the point against the polygons. Geocoding needs a service: the Census Bureau geocoder is free and needs no key, but it is slow and US-only, which is fine here. No server code is needed; the polygons ship as static GeoJSON.

**Recommendation.** Build it after 18, on the Census geocoder, and say plainly on the page that the result is a convenience, not an official determination. The County Registrar is the authority.

## 22. The Parks and Arts commissions

Only the Planning Commission has a record (now folded under the City on /organizations). Neither the Parks and Recreation Commission nor the Arts Commission exists. The only source the archive holds for the Parks commission is Laurene Weste's own biography (member and chair, 1987 to 1998). **Recommendation:** create the Parks and Recreation Commission with that service attributed to her biography, and leave the Arts Commission until a source is in hand.

## 24. /events

**What is there.** Two event records: the Pico Canyon drilling of the 1860s and the Northridge earthquake. The valley's defining events live as articles and collections, not as events: the gold discovery of 1842, the railroad and the founding of Newhall in 1876, Pico No. 4 in 1876, the St. Francis Dam in 1928, Hart Park's dedication in 1958, cityhood in 1987, and the SCV Water consolidation in 2018.

**Recommendation.** Treat /events as the valley's timeline:

1. **Create event records for the anchor events**, each relating the articles, photographs, people and places the archive already holds. Only the date and a short sourced summary are new.
2. **Organize the page by era**, using the same era categories the people now carry. Each era shows its events as a timeline, with "On this day" links where a day is known.
3. **Recurring events** (the Fourth of July parade, the Cowboy Festival) get their own group, since they have no single date.

The SCV Water consolidation is the natural first event, now that SB 634 and the three predecessor bodies are sourced.

## Other things found today that need you

- **The town of Newhall's namesake.** Nothing the archive holds says the town was named for Henry Mayo Newhall. Reynolds's railroad chapter probably does, but the mirror cannot be read (next item).
- **Mirror and Downloads access.** Since about midday, macOS refuses this session's reads of /Volumes/Reggie and ~/Downloads ("Operation not permitted"). Re-grant access in System Settings, under Privacy & Security, then Files and Folders (Removable Volumes and Downloads). The mirror copy in the container is also stale. One split drop cap (#15402, "N" + "ew New York") is held until the original can be read.
- **A portrait with no record.** jose-jesus-lopez-portrait.jpg (asset #75) is in the archive with no person record for José Jesús López.
- **3,358 unattached mirror images**, mostly thumbnails. They belong with the pending mirror work.
- **/articles takes 12 seconds to render**, long enough to time out the render check once today.
