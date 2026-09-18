# Mega menu: design to data mapping

`menu-source.html` holds the header markup and menu data exactly as designed.
Reproduce that markup faithfully. This file says what each menu item points at
and where its count comes from.

Rule: if the Target column says OMIT, leave the item out entirely, both the
title and the number. Do not invent a page and do not show a placeholder count.
Report every item you omitted.

## PEOPLE — kicker "BIOGRAPHY & FAMILY RECORDS"

| Column | Item | Target | Count |
|---|---|---|---|
| RECORDS | All people | /persons | persons count |
| RECORDS | Pioneer families | /groups | groups count |
| RECORDS | Groups & rosters | OMIT, duplicate of the row above | |
| MEMORIAL | Obituaries | /obituaries | obituaries count |
| MEMORIAL | War Memorial | /war-memorial | warMemorials count |
| MEMORIAL | Cemeteries | OMIT, no such record type | |
| BY ROLE | Ranchers & landowners | OMIT until tags exist | |
| BY ROLE | Civic leaders | OMIT until tags exist | |
| BY ROLE | Film & television | OMIT until tags exist | |

Drop the BY ROLE column entirely while it is empty; the column grid is auto-fit
so two columns lay out correctly.

Feature: most recently updated person. Kicker RECENTLY ADDED, title their name,
meta their occupation, cta "Open the record", link to the record.

## PLACES — kicker "LAND, SITES & COMMUNITIES"

| Column | Item | Target | Count |
|---|---|---|---|
| GEOGRAPHY | All places | /places | places count |
| GEOGRAPHY | Ranchos & land grants | OMIT until a place type field exists | |
| GEOGRAPHY | Canyons & waterways | OMIT until a place type field exists | |
| COMMUNITIES | the three communities holding the most records, by name | /communities/{slug} | records related to that term |
| INSTITUTIONS | Organizations | /organizations | organizations count |
| INSTITUTIONS | Schools | OMIT | |
| INSTITUTIONS | Landmarks | OMIT | |

Feature: Beale's Cut Stagecoach Pass if it exists, otherwise any place with both
coordinates and a featured image. Kicker FEATURED PLACE, title the place name,
meta the first sentence of its body or its community, cta "Visit the place".

## COLLECTIONS — kicker "MULTI-PART WORKS, READ IN ORDER"

| Column | Item | Target | Count |
|---|---|---|---|
| MAJOR WORKS | every collection flagged collectionIsMajor, by title | its url | its article count |
| SERIES | the three largest collections not flagged major | its url | its article count |
| BROWSE | All collections | /collections | collections count |
| BROWSE | By author | OMIT until an author index exists | |
| BROWSE | By era | /articles?view=era | historicalEra term count |

Feature: the major collection with the most articles. Kicker NOW READING, title
its name, meta "{author} · {n} articles · {earliest era} to {latest era}", cta
"Start at the {first article title}", linking to that first article.

## ARTICLES — kicker "SINGLE PIECES & EVENTS"

| Column | Item | Target | Count |
|---|---|---|---|
| WRITING | All articles | /articles | articles count |
| WRITING | Newspaper archive | OMIT until a source field distinguishes them | |
| WRITING | Essays & research | OMIT | |
| EVENTS | All events | /events | events count |
| EVENTS | Disasters | OMIT until tags exist | |
| EVENTS | Openings & dedications | OMIT until tags exist | |
| MEDIA | Photo galleries | /photographs, only if the section has entries | photographs count |
| MEDIA | Maps | OMIT | |
| MEDIA | Documents | /documents, only if the section has entries | documents count |

Feature: the most recently updated article. Kicker RECENTLY PUBLISHED, title its
name, meta its author and original publish date, cta "Read the article".

## BY ERA — kicker "BROWSE THE TIMELINE"

Build the three columns from the historicalEra category group, in chronological
order, split into three roughly equal groups by era start year. Use the design's
column labels where they fit: BEFORE STATEHOOD for eras ending before 1850, THE
AMERICAN VALLEY for 1850 to 1929, MODERN for 1930 onward. Each item is the era
name with the count of records related to that term. Omit eras with no records.

Feature: kicker TIMELINE, title "Every record, in order", meta "Walk the archive
decade by decade, from the Tataviam villages to the city of Santa Clarita.", cta
"Open the timeline", linking to /articles?view=era.

## Counts

Every number is a live count, formatted with a thousands separator. Cache the
whole header fragment with Craft's cache tag keyed on the sections and category
groups it touches, so a page load does not run twenty queries. Report the query
count per page before and after.

## Active state

The nav button whose section matches the current page carries the gold bottom
border, as in the design. Map: persons, groups, obituaries, warMemorials and
militaryProfiles to PEOPLE; places, communities and organizations to PLACES;
collections to COLLECTIONS; articles, events, photographs and documents to
ARTICLES.

## Behaviour

Opens on hover and on keyboard focus, one at a time. Closes on Escape, on click
outside, and on mouse leave of the panel. Fully keyboard navigable with visible
focus, and the panel is not a keyboard trap. Below 980px the five buttons
collapse to a single toggle opening the same content stacked, and the search box
moves beneath the wordmark.

## Verify

The homepage, a record page, an index, and 390px wide. Confirm the rendered
header matches menu-source.html in structure, spacing and colour, and report any
place you had to deviate and why.
