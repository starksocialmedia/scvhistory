# Public bodies (/civic): an audit, 5 October 2026

Claude Code, for Nathan. Every record /civic showed on 5 October, read from the database with the page's own rule, and
the groups as the page rendered them. A body of another body is folded into its parent's card; a dissolved body into its
successor's.

## What is there

| Record | What it is | Its role toward the valley | Serves the valley | Where it sits |
|---|---|---|---|---|
| The City of Santa Clarita #394 | the city government | governs | yes | Valley government |
| Planning Commission #15939 | a City commission | advises | yes | folded under the City |
| Parks, Recreation and Community Services Commission #29088 | a City commission | advises | yes | folded under the City |
| Arts Commission #29090 | a City commission | advises | yes | folded under the City |
| Redevelopment Agency of the City of Santa Clarita #29699 (1989 to 2012) | a public agency, the Council sitting as its board | administers | yes, Newhall | folded under the City |
| Newhall Redevelopment Committee #16290 (1996 to 2012) | a citizens' committee | advises | yes, Newhall | folded under the City |
| Santa Clarita Valley Water #402 | the water agency, an elected board | administers | yes | Valley government |
| Castaic Lake Water Agency #26563 (to 2018) | the wholesale water agency | administers | yes | folded under SCV Water |
| Newhall County Water District #27534 (1953 to 2018) | a water district | administers | yes | folded under SCV Water |
| Valencia Water Company #29565 (1954 to 2018) | **a private company** | supplied water | yes | folded under SCV Water |
| Santa Clarita Valley Sheriff's Station #29682 | the County Sheriff's station | polices | yes | Valley government |
| Los Angeles County Sheriff's Department #29282 | a county department | polices | yes | folded under the County |
| County of Los Angeles #29279 | the county | governs, and for the unincorporated communities is the government | yes | County, state and federal |
| Los Angeles County Board of Supervisors #28275 | the county's governing board | governs | yes | folded under the County |
| Los Angeles County Township Constables #29798 | historical township officers | policed | yes, the Newhall Township | County, state and federal |
| California Highway Patrol #29792 | the state's highway police | polices | yes, the highways and the unincorporated roads | County, state and federal |
| California State Senate #28273, Assembly #28271 | the Legislature's chambers | represents | yes | County, state and federal |
| United States Congress #29393, House of Representatives #28269 | Congress | represents | yes | County, state and federal; the House folded under Congress |
| William S. Hart Union High, Newhall, Saugus Union, Sulphur Springs Union, Castaic Union school districts | school districts, elected boards | governs | yes | School districts |
| Acton-Agua Dulce Unified School District #29691 | a school district | governs | yes, Agua Dulce | School districts |
| Ventura County Township Constables #29800 | historical township officers | none | **no** | was County, state and federal |

## 1. What should not be there

- **Ventura County Township Constables** (made today for McCoy Pyle). They never served this valley; Pyle was killed here
  holding prisoners from a Fillmore case. Now role "none", off the page. Fixed.
- **Valencia Water Company.** A private company, not a public body. It shows only folded into SCV Water's card, as one of
  the four bodies SB 634 merged. Nathan's call: keep it there as part of the water story (it is labelled a company on its
  card), or take it off and leave the link on SCV Water's record.
- Nothing else. The State Senate, Assembly and Congress are there because they represent the valley; the CHP polices its
  highways and unincorporated roads; the township constables policed the Newhall Township until the county's later
  arrangements.

## 2. What is missing

None of these has a record in the archive.

| Body | What it does here | Note |
|---|---|---|
| Santa Clarita Community College District (College of the Canyons) | governs the valley's community college; an elected board | the clearest gap: a public body with elected trustees, like the school districts |
| Consolidated Fire Protection District of Los Angeles County (the County Fire Department) | fire and paramedic service for the City and the unincorporated valley | that the City is in the district rather than contracting for it is from general knowledge, NEEDS_VERIFICATION |
| Santa Clarita Public Library | the City's library; that the City left the County system (about 2011) is from general knowledge, NEEDS_VERIFICATION | a City department |
| County of Los Angeles Public Library | libraries in the unincorporated valley | |
| Department of Regional Planning and the Regional Planning Commission | land use for Castaic, Stevenson Ranch, Val Verde and the rest of the unincorporated valley | with the Board of Supervisors and the Fifth District, this is what governs the unincorporated communities |
| Town councils: Castaic Area, Agua Dulce, Acton (and the Val Verde Civic Association) | advise the Supervisor | elected by residents but not public bodies in law: role "advises" |
| Santa Clarita Valley Sanitation District (of the County Sanitation Districts) | sewers; the board includes City Council members | |
| Superior Court of Los Angeles County, Santa Clarita courthouse (and the Newhall Municipal Court before 1998) | the courts | Judge Adrian Adams's Newhall court is in a Hart trustee's dossier |

Each needs research before a record; the college district first.

## 3. Type and place, kept apart

Done, as proposed: a new field, **civicRole**, "Role toward this valley": governs, represents, polices, advises,
administers, or "Does not serve this valley". Organization type says what a body is; civicRole says how it stands to the
valley, and /civic shows a government or school district unless its role is "none". Every record above carries its role;
the Los Angeles Police and Burbank Police Departments are now typed government, with role "none", as is Ventura County's
constables. The level field is left to mean the level of government (valley, county, state, federal), which it does now.
