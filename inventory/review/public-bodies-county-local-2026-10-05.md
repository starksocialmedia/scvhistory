# Public bodies, batch county-local: sanitation, the unincorporated valley, town councils, the court

5 October 2026, for Nathan. Research only: nothing written to the database. The proposed records are in
`public-bodies-county-local-2026-10-05.json`; every source read is saved, with its sha256, under
`inventory/news/public-bodies-2026-10-05/county-local/` (`manifest.json` lists them, with the legacy pages read in place).

## The records proposed

| Record | Type, role | Key facts | Sources |
|---|---|---|---|
| Santa Clarita Valley Sanitation District | government, valley, administers | Name verified. Formed 2005 by consolidating District No. 26 (1962, east, Saugus plant) and No. 32 (1965, west, Valencia plant), joined by a 1984 cost-sharing agreement. Board: the Mayor of Santa Clarita, a second council member, the chair of the Board of Supervisors. Saugus plant 1962, 6.5 mgd; Valencia 1967, 21.6 mgd. Chloride: 100 mg/L objective; Measure S, 4 Nov 2008, softener ban, 8,000+ removed; compliance project approved Oct 2013, EIR litigation to Feb 2022; UV 2021, reverse osmosis at Valencia Oct 2023 | the Sanitation Districts' own pages (Internet Archive captures, 2012 to 2026; lacsd.org refuses direct fetches) |
| Los Angeles County Department of Regional Planning | government, county, administers; parent County #29279 | planning department for all unincorporated land; from the Commission's staff, independent 1974; Santa Clarita Valley Area Plan adopted by the Board 27 Nov 2012 (effective 27 Dec), the County half of One Valley One Vision; the City's General Plan 14 June 2011; lead agency on valley EIRs | planning.lacounty.gov; County EIRs on the legacy site |
| Los Angeles County Regional Planning Commission | government, county, advises; parent County #29279 | five commissioners, Board-appointed, four-year terms; founded 1923; heard Princess Park 1964; recommended Skyline Ranch's plan amendment; applicants sent by it to the Agua Dulce council (2005 finding) | planning.lacounty.gov; Signal 1969; Skyline Ranch package; 2005 judgment |
| Castaic Area Town Council | nonprofit, advises | nonprofit public benefit corporation; ten directors, two per region, elected by residents, four-year terms; bylaws 19 July 2000; IRS 501(c)(3), 2001; County lists it as a reviewing agency (2007) | its bylaws (scanned PDF on the legacy site, OCR'd), its site, IRS data, County NOP |
| Agua Dulce Town Council | nonprofit, advises; founded 21 Sept 1991 | created by residents' approval of a charter 21 Sept 1991; incorporated 1994; seven members, two-year terms, annual November election; 501(c)(4); Superior Court, 1 Aug 2005: a Brown Act legislative body serving "in an advisory capacity ... to the County"; seat on the Cemex advisory committee 2004 | charter, bylaws, the 2005 judgment, County Cemex permit |
| Acton Town Council | nonprofit, advises | The Acton Town Council, LLC, sole member the seated council as "The Town of Acton Advisory Board"; up to seven, two-year terms, annual November election; 501(c)(4); second election 1990 | its 2025 bylaws, its site, IRS data, Cemex permit |
| Val Verde Civic Association | nonprofit, **none** | a private residents' association, 501(c)(4) since 1990; saved the Dixon clinic 1990-91; party to the 1997 Chiquita landfill agreement; not a council (on Manzer's word only) | IRS data; Canty 2000; the 1997 agreement page; Manzer 2014 |

Not proposed: a Stevenson Ranch or West Ranch Town Council (below), the Castaic Lake Recreation Area (a place,
not a governing body; not researched), the Newhall Ranch Sanitation District (open question in the JSON),
the Val Verde Community Advisory Committee (open question), and the court (below).

## Corrections to the request's assumptions

- **The sanitation district's name and origin are as Nathan guessed**: the Santa Clarita Valley Sanitation District,
  consolidated from Districts 26 and 32, in 2005.
- **One Valley One Vision: two adoption dates for the County.** The County's own page: the Board of Supervisors
  adopted the Area Plan on **27 November 2012**. The note on the legacy page /scvhistory/laco_ovov_final_2010.htm says
  "Feb. 28, 2012". The record follows the County and says so in an editor's note.
- **Unification was 2000, not 1998.** Proposition 220 passed in 1998, but Los Angeles County's municipal and superior
  courts unified on **22 January 2000** (Judicial Council table of effective dates).
- **Agua Dulce and Acton may not be under the Santa Clarita Valley Area Plan at all**, but the County's Antelope Valley
  Area Plan (2015). Not settled from a County source: NEEDS_VERIFICATION, and the drafts do not say either way.

## Who governs the unincorporated valley

For Castaic, Stevenson Ranch, Val Verde, Agua Dulce, Acton and the County's parts of Newhall, Saugus, Valencia,
Canyon Country, Sand Canyon and Placerita Canyon, the government is the County. In the County's own words (Fifth District
guide, 2024): "Each Supervisor is essentially mayor of the unincorporated communities they are elected to represent," and
"the Board of Supervisors serves as their 'city council' and County departments provide municipal services like fire and
paramedic services, animal care and control, water, road maintenance and trash collection." Land use is the Department of
Regional Planning's and, at hearing, the Regional Planning Commission's, under the Santa Clarita Valley Area Plan, with
the Board deciding. Sewage is the Sanitation District's; water is SCV Water's (Val Verde: Waterworks District 36).

The Board of Supervisors (#28275) and the County (#29279) have empty bodies. A paragraph for #28275, from the sources
above, if Nathan wants it (notes as in the Regional Planning record's style):

> For the unincorporated Santa Clarita Valley, Castaic, Stevenson Ranch, Val Verde, Agua Dulce, Acton and the County's
> parts of Newhall, Saugus, Valencia and Canyon Country, the Board of Supervisors is the local government. The County calls
> each supervisor "essentially mayor of the unincorporated communities they are elected to represent," and the Board
> their "city council," with County departments providing municipal services. The valley is in the Fifth District.
> [County of Los Angeles, Fifth Supervisorial District, Guide to Unincorporated Area Services, 2024, pp. 4, 13]

The archive's list of the valley's supervisors (Dodge 1917-1921, Dorn 1956-1974, Ward 1974-1980, Antonovich 1980-2016,
Barger 2016 on) is on /scvhistory/aguadulcetowncouncil080105.htm and is Leon's, not the County's.

## Town councils: what they are

Nathan asked whether they are real bodies. The honest answer from the documents:

- **All three councils are private nonprofit organizations, not public agencies.** Castaic's is a nonprofit public
  benefit corporation (501(c)(3)); Agua Dulce's a nonprofit (501(c)(4)); Acton's a limited liability company (501(c)(4)).
  None can make law or tax; Agua Dulce's bylaws say so outright.
- **But they are elected by residents, in their own elections, and the County uses them.** Each holds a secret-ballot
  election of its own each November (Castaic's and Agua Dulce's under an election committee; candidates must be
  registered voters resident in the area). The County lists Castaic's council as a reviewing agency on project notices,
  put Agua Dulce's and Acton's on the Cemex mine's advisory committee, and, by the 2005 court's findings, sent
  Agua Dulce applicants to the council before going further.
- **In 2005 a court held Agua Dulce's council to the Brown Act** as a body created by charter that "serves in an
  advisory capacity to the community of Agua Dulce and to the County of Los Angeles." That turns on its charter, adopted
  by the town's voters in 1991; Castaic and Acton have bylaws, and whether the ruling reaches them was not established.
- So: orgType "nonprofit", civicRole "advises" for the three councils. orgLevel left empty (a council is not a level of
  government); Nathan's call.
- **The Val Verde Civic Association is a different thing**, a residents' association. Role "none" proposed. Darryl
  Manzer's 2014 commentary says the County's own advisory body there is a Val Verde Community Advisory Committee appointed
  by the Fifth District supervisor; that committee may be what the archive wants for Val Verde. Not researched.
- **Stevenson Ranch.** Richard "Doc" Rioux (#2585) founded the Stevenson Ranch Town Council and was its first president
  (his biographical sketch, /oldtownnewhall/rioux/rrbio.htm; Rep. McKeon's tribute, /oldtownnewhall/rioux/hmtrib.htm;
  Leon Worden's column of 22 April 1998). It was active in December 1996 (Rioux's column of 1 December 1996, on its wish to
  name the main street Stevenson Ranch Parkway). A "West Ranch Town Council" was active in 2006 and held an election on
  4 July 2006 at which, Manzer wrote, 54 people voted (Signal columns of April to September 2006). Whether these are one
  body, its legal form, and whether it exists today were not found. **Recommendation: no record yet**; a line on
  Rioux's profile and on the Stevenson Ranch community page instead, until a contemporary news source establishes
  more.
- **Others.** No other unincorporated-area council in the valley turned up. The Canyon Country Advisory Committee is
  inside the City and out of this batch.

## The court: a recommendation

### The facts

- **The justice court.** The valley's court was a township justice court for most of its history: dockets from the
  1850s; John F. Powell justice of the peace to 8 January 1923, succeeded by Port C. Miller (/scvhistory/sg031503.htm);
  A.B. Perkins (#333) sat as justice in 1926 and 1928 (Adrian Adams, "Tales of the Newhall Court," The Signal,
  12 April 2003, /scvhistory/sg041203.htm). Adams: "Originally known as the Soledad Judicial District, the name was
  changed to the Newhall Judicial District in 1952," and "Until the 1960s when the Justice Court became a Municipal Court,
  a layman presided." The Signal still called it the "Newhall Justice Court" in January 1963 (/scvhistory/sg19630117depot.htm).
- **The courthouse on Railroad Avenue.** The County court occupied the ground floor of the Masonic building at 24307
  Railroad Avenue, built 1931-32, the lodge above (City of Santa Clarita historic resources survey,
  /scvhistory/newhall-historic-structures.htm). The building is a City Point of Historical Interest.
- **The Newhall Municipal Court.** C.M. MacDougall was its judge (and was "Judge MacDougall" in 1956-57,
  /scvhistory/hs0880.htm). Adrian W. Adams (#28667) was appointed to a new second seat on 27 January 1970 (Signal,
  28 January 1970, /scvhistory/aa7001.htm) and retired in 1991, by which time there was a third seat. Jack Clark retired in
  1982; H. Keith Byram won the seat at a contested election that November, was re-elected in 1988 and 1994, and left the
  bench in February 1998; Alan Rosenfield sat from 1990 (Byram's obituary, /scvhistory/sg101303.htm). Municipal judges
  were elected by the judicial district's voters, so these were valley elections.
- **The move to Valencia: three dates.** The City survey says the County moved the court to Valencia in 1968. Leon's
  caption on a photograph of 19 December 1970 (/scvhistory/lw2312a.htm) says the new courthouse at the County Civic Center,
  Valencia Boulevard and Magic Mountain Parkway, "was under construction in during 1970." The Judicial Council (notice
  of preparation, 23 October 2025) says the Santa Clarita Courthouse "was constructed in 1972." Unresolved.
- **Unification.** 22 January 2000 for Los Angeles County (Judicial Council). Adams in 2003: "As of now, Newhall
  Municipal Court is only history. With the recent unification of the courts, it has become part of the Los Angeles
  County Superior Courts."
- **Today.** The Superior Court of Los Angeles County, North Valley District. The Santa Clarita Courthouse, 23747 West
  Valencia Boulevard, is County-owned, about 32,000 square feet, and has three courtrooms hearing only criminal
  misdemeanors, so other cases from the valley are heard elsewhere (Judicial Council NOP). Its jurisdiction is the city
  and the unincorporated valley (County locator page). The Judicial Council proposes a 24-courtroom courthouse at 26501
  McBean Parkway to replace it and the Sylmar juvenile court (NOP, 23 October 2025).

### Recommendation

A court does not govern, represent, police, advise or administer the valley in the sense civicRole means, and stretching
"administers" to cover it would blur that field. But the valley had its own court for a century, with its own judges,
elected by its own voters from the 1960s to 2000, and the archive already holds a judge and a run of pages about it.

1. **One organization record: the Newhall Municipal Court**, the valley's own court, government, orgLevel valley,
   dissolved 22 January 2000 (unification). Its body carries the justice court of the Soledad and Newhall judicial
   district as its earlier history (Powell, Miller, Perkins) rather than a second record, unless the township court has
   enough of its own. civicRole **"none"**, which keeps it off /civic, unless Nathan wants the courts there; in that case
   a new value, "judges" or "adjudicates", is better than borrowing "administers". Office holdings for Adams (1970-1991),
   MacDougall, Clark and Byram then have a body to attach to, as the Adams dossier proposed.
2. **No record for the Superior Court as a body.** It is a county-wide state trial court; nothing in the archive is about
   it as an institution. Name it in the Municipal Court's body as the successor.
3. **Two place records for the buildings**: the 1932 Masonic courthouse at 24307 Railroad Avenue (a City Point of
   Historical Interest, with photographs on the legacy site) and the Santa Clarita Courthouse on Valencia Boulevard
   (with its date conflict as an editor's note, and the planned replacement).

## What was searched, for the "not found" statements

All on 5 October 2026. The mirror: Python over the bytes of every .htm, .html, .txt page and every flipbook
`bookText.xml` under /Volumes/Reggie/SCVHistory/scvhistory.com (htm and txt decoded cp1252, bookText utf-8), never the
shell's grep. Terms: "Sanitation District", "Water Reclamation", "chloride", "SCVSD", "Joint Sewerage", "District No. 26",
"District No. 32", "Nos. 26 and 32"; "Town Council", "Castaic Area Town Council", "Castaic Town Council", "Agua Dulce Town
Council", "Acton Town Council", "West Ranch Town Council", "Stevenson Ranch Town Council", "Val Verde Civic", "Civic
Association", "Canyon Country Advisory", "Sand Canyon Homeowners"; "Regional Planning Commission", "Regional Planning",
"Area Plan", "One Valley", "laco_ovov", "Planning Advisory", "Hillside Management"; "Municipal Court", "Superior Court",
"courthouse", "Santa Clarita Courthouse", "Newhall Justice Court", "Newhall Judicial District", "Soledad Township",
"unification", "23747". The web: lacsd.org (403 to direct requests; read through Internet Archive captures and the
Archive's CDX index of lacsd.org), planning.lacounty.gov, lacounty.gov, the councils' own sites, courts.ca.gov,
locator.lacounty.gov, ProPublica's Nonprofit Explorer API for IRS data, and web searches for each body.
signalscv.com refused direct requests (403); its articles were not read.

Not found: a founding document for the Castaic Area Town Council before its 2000 bylaws; a founding date for Acton's
council; the legal form, dates or present existence of the Stevenson Ranch or West Ranch Town Council; a County statement
on the Val Verde Civic Association; the date the justice court became the Newhall Municipal Court; the date of the
Commission's Skyline Ranch resolution. These searches belong in `inventory/source-searches.json` when the records are
made (not written now: this batch writes only its own files).

## For Nathan

1. Approve the seven drafts, or cut: the Commission and the Department could be one record.
2. Acton and Agua Dulce: on /civic or not (they are outside the Santa Clara River's valley; their school district is on).
3. The court: the Municipal Court record with civicRole "none", or a new role value.
