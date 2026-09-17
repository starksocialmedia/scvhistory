# SCVHistory.com: How the Data Is Organized

The "where does this go?" guide. Read with PHILOSOPHY.md. If something does not fit cleanly, add it to Open Questions in CONTENT-MODEL.md for Nathan. Never guess. Confirm exact handles against `config/project/` before writing code.

---

## 1. The big picture

The archive has four kinds of building blocks:

1. **Entries (records):** things with their own page and their own story: people, organizations, places, articles, collections.
2. **Categories:** fixed lists used to *locate and filter* records: communities, historical periods, historical eras.
3. **Relations:** the links between records (Person → Place, Article → Person), which are the point of the whole rebuild.
4. **Tags:** everything worth noting that does not deserve a record.

The most common mistake is mixing these up. A community is not a place. A one-off name is not a person record. An era is not a tag.

---

## 2. Communities vs. Places (the current problem)

### Communities are CATEGORIES

A **Community** is a named area of the valley that content is *located in*. It is a filter, not a record. Every Person, Place, Organization, and Article can be assigned one or more Communities.

- In Craft: the existing **`neighborhood` category group**. Display it as "Communities" if Nathan wants, but **keep the handle `neighborhood`** so existing fields and imports do not break.
- **Never create a Place entry for a community.** Acton, Newhall, Saugus, Valencia, Canyon Country, Castaic, and the like are categories.

Community list (from the original site's taxonomy):
Newhall, Valencia, Canyon Country, Saugus, Agua Dulce, Acton, Castaic, Val Verde, Camulos, Piru, Soledad Canyon, San Francisquito Canyon, Placerita Canyon, Bouquet Canyon, Hasley Canyon, Haskell Canyon, Mentryville, Pico Canyon, Potrero Canyon, Ravenna, Tejon, Towsley Canyon, Mojave Desert, Lebec

Candidates flagged earlier as possibly missing (Nathan confirms before adding): Green Valley, Mint Canyon, Sand Canyon, Stevenson Ranch, Live Oak, Tourney Road corridor

### Places are ENTRIES

A **Place** is a specific site you could stand at or point to on a map, with its own history: a building, landmark, park, cemetery, mine, oil field, road, station, or body of water.

- Examples: William S. Hart Mansion, Hart Park, St. Francis Dam, Vasquez Rocks, Beale's Cut, Pioneer Oil Refinery, Eternal Valley cemetery
- Every Place **belongs to** one or more Communities (category field)
- Place Type values: road, park, building, canyon, school, landmark, body-of-water, ranch, cemetery, mine, oil-field, railway-station
- **"Neighborhood" and "town" are NOT Place Types.** Those are Communities.
- Fields that matter: coordinates, existed from / until (with date precision), status (standing, demolished, ruins), community

### The test

> Is it an **area** people live in or content happens in? → **Community** (category)
> Is it a **specific site** with its own story? → **Place** (entry)

Edge cases (a named canyon that is both a region and a site, a historic townsite that is now a park such as Mentryville) go to Open Questions. Default: if it is on the Community list above, it is a Community.

---

## 3. Every entry type and what belongs in it

| Entry type | What it is | Examples | Not this |
|---|---|---|---|
| **Person** | An individual with a meaningful, recurring role in SCV history | William S. Hart, Henry Mayo Newhall, Ygnacio del Valle, Leon Worden | Someone mentioned once (tag them) |
| **Organization** | Something that acts: owns, operates, employs, governs, publishes | Newhall Land and Farming Co., City of Santa Clarita, Wiley Station, Rancho San Francisco, missions | The physical building (that is a Place) |
| **Place** | A specific physical site | Hart Mansion, St. Francis Dam, Beale's Cut | A community or area |
| **Group** | A collective that is not a formal organization | Families (del Valle family), tribes (Tataviam), military units, expeditions, cohorts | A company or agency |
| **Article** | Historical articles, columns, essays | Jerry Reynolds' Signal columns | Photos or documents on their own |
| **Collection** | A curated set of articles, like book chapters | "History of the Santa Clarita Valley" (Reynolds) | A topic or tag |
| **Obituary** | Obituaries | | A Person record (not auto-created; manual editorial decision) |
| **Source** | Publications and media outlets that content comes from | | |

Military service is **not** its own type. It is a conditional field group on Person (settled decision).

Later-phase types (do not build until Nathan says so): Event, Photograph, Document, Film/Media Production, Disaster, Cemetery Record, Species, Archaeological Site, Yearbook.

### Organization or Place?

> A thing that **acts** is an Organization. A thing you can **stand in** is a Place.

Hart Mansion is a Place. The County agency that runs it is an Organization. If both sides matter and both recur, create two records and relate them. If only one side recurs, create only that one.

### Tribes and Indigenous peoples

Group type "Tribe." Follow TATAVIAM_AUDIT.md. Never merge distinct peoples (Tataviam, Chumash, Tongva, Serrano, Kitanemuk, Vanyume) into one record.

---

## 4. Categories (fixed lists)

| Category group | Handle | Purpose | Rule |
|---|---|---|---|
| **Communities** | `neighborhood` | Where the content is located | Section 2 |
| **Historical Period** | `historicalPeriod` | Decade buckets: Pre-1850, 1850–1899, 1900–1919, then by decade to 2020–Present | The period the content is **about**, not when it was written |
| **Historical Era** | `historicalEra` | Named eras (Rancho period, railroad era, etc.) | Use existing terms; propose new ones, do not add them |

Categories are controlled lists. Agents never add terms without Nathan's approval.

---

## 5. Relations (how records connect)

Relations are the whole point. Rules:

- **Relate, don't tag.** If a record exists, link it. Never tag something that already has a record.
- **Relations carry meaning** (founded, owned, lived at, parentOf, witnessed) and are **bidirectional** (Rodolfo parentOf Dante ↔ Dante childOf Rodolfo).
- **Entities before articles.** A relation can only point to a record that already exists.

Common relations:

- Person → Organization: Roles Matrix (title, organization, start year, end year, primary role)
- Person → Place: lived at, built, owned
- Person → Person: family and professional relationships
- Place → Community: category assignment
- Organization → Organization: parent organization (City Council → City of Santa Clarita)
- Article → Subject Person, Subject Organization, Depicts Place, Collection, Written By, Edited By, Published By

---


### The full link map (from the WordPress build)

These are the connections the pages actually render. Craft must support all of them.

**Article**
- Part of Collection, **with a sequence number**: collections are ordered, and pages show previous and next chapters plus the full chapter list
- Written By → Person. The page renders that Person's name, roles, bio, and a link to the profile
- Edited By → Person
- Published By → **one or more** Organizations or Sources (an article can credit both SCVHistory.com and The Signal; an obituary credits the newspaper that ran it)
- People in this article → Person (only those who meet the record threshold)
- Subject Organization, Depicts Place, Related Events
- Historical Era, Historical Period, Community categories
- Original publish date, which drives the citation, not the migration date
- Webmaster note, rendered as a visible correction callout
- Contributor credit ("courtesy of ...") → the donor and contributor registry from key.htm
- Tags for everything below the threshold

**Place**
- Aliases ("Also known as: Fremont Pass, San Fernando Pass, Newhall Cut")
- California Historical Landmark number and link
- Associated People, Related Events (bidirectional: the earthquake lists the interchange, the interchange lists the earthquake)
- Address, coordinates, established date, community

**Event**
- Date, Places, People, Organizations, Related Events, Historical Era and Period
- Cross-links inline in body text to other entries

**Person**
- Family relations (parentOf, childOf, spouseOf, siblingOf), each showing the other person's roles
- Organizations, Groups, Articles About, Obituaries, Military Profile
- Wikipedia and Find A Grave external links

**Military Profile**
- mpSubject → Person (this is what makes a parent's page show a child's rank)
- Conflict category, branch and school Organizations, burial Place, community

## 6. Tags

Tags are for things that matter in context but do not earn a record:

- People mentioned once or with no SCV role (e.g., Henry Clay in a Compromise of 1850 aside)
- Named historical events without a record (until an Event type exists)
- Sites too small or too passing for a Place record
- Concepts and themes (cattle drives, secularization of the missions)
- Referenced works and cultural terms

Rules: never tag something that has a record; every tag gets a description.

---

## 7. Article checklist

Every migrated article carries:

- Title (original)
- Written By, Edited By (if any), Published By
- Part of Collection (if any)
- Original Publish Date (with precision; "c. 1970s" stays approximate)
- Historical Period (what the article covers)
- Communities
- Subject Person(s), Subject Organization(s), Depicts Place
- Legacy URL (path only)
- Fine Print (copyright line, verbatim)
- Webmaster Note (verbatim, if present)
- SEO title and meta description
- Tags
- Body: Leon's text, unedited except for markup cleanup

## 8. Photo checklist

- Photo ID (donor prefix + 4 digits, e.g., `LW0891`), preserved exactly
- Title, alt text, caption with credit line, full archival description
- Date (with precision), credit, source/donor, location
- Depicts: Persons, Places, Organizations (relations)
- Communities

---

## 9. Worked examples

| Thing | Goes in | Why |
|---|---|---|
| Acton | Community category | An area, on the list |
| William S. Hart Mansion | Place (building), community Newhall | Specific site with its own history |
| William S. Hart | Person | Recurring, central figure |
| Newhall Land and Farming Co. | Organization (business) | Acts; recurs constantly |
| Wiley Station (1852) | Organization | Settled in earlier sessions as a business record |
| Newhall Pass | Open Question for Nathan | Treated as a neighborhood in earlier work but not on the list above; a pass is arguably a Place |
| Tataviam | Group (tribe) | Per TATAVIAM_AUDIT.md |
| del Valle family | Group (family) | Collective, not an organization |
| Henry Clay (in a Reynolds column) | Tag | No SCV role |
| 1928 St. Francis Dam collapse | St. Francis Dam = Place; the disaster = tag until an Event type exists | Event is later phase |
| Reynolds' Signal columns | Collection | Curated series |
