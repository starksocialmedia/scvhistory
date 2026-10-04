# Trustee area and division boundaries: what geometry exists (4 October 2026)

Agent: Claude (research subagent). Read only: nothing in the database or templates was changed. Everything fetched is saved under `inventory/sources/trustee-areas-2026-10-04/<body>/`, each folder with a `manifest.json` (URL, date, sha256, bytes, content type, what it is, licence). No file was over 50 MB; the largest is the City's final map PDF at 16 MB. The machine-readable version of this report is `trustee-area-boundaries-2026-10-04.json`.

## Short answer

Yes. Usable polygon geometry exists for every body asked about, and it all downloads from public REST endpoints with no login.

The best single source is the **Los Angeles County Registrar-Recorder/County Clerk (RR/CC)**. It runs two public ArcGIS layers covering all the valley's bodies with one schema and one licence:

- **Registrar Recorder School Districts Trustee Areas** (item `56e294422060461bb9bf36c95aa4d262`, last update September 2026): Saugus, Hart, Newhall, Sulphur Springs, Castaic. Each district has one outline feature plus one feature per trustee area, numbered in `TRUSTEE_AREA`.
- **Division_Boundaries** (precinct derived, features re-created 9 September 2026): the same five school districts, plus **SCV Water Divisions 1 to 3**, **Santa Clarita City Council Districts 1 to 5**, and the **Santa Clarita Community College District** trustee areas. The division name is in `DivisionName` and the RR/CC code is in `Code`.

The licence is the LA County GIS Terms of Use. It grants a licence "to copy, publish, distribute and/or transmit the Data, to adapt the Data and to exploit the Data for commercial and/or personal use". Use must not suggest County endorsement, and the data comes as is, with no warranty. The layer notes say the data is "for election purposes only and not a representation of the school districts' legal boundaries". The County recommends a citation format (see Licences).

Each body's own published geometry, where there is any, agrees with the RR/CC polygons to within about 1 to 2 percent of area per district (table below). The RR/CC lines follow parcel lines, as the County requires for elections.

## Per body

### Saugus Union School District
- **Published:** a static PDF map and an interactive **Google My Maps** map, both linked from https://www.saugususd.org/governing-board.
  - PDF: https://wsos-cdn.s3.us-west-2.amazonaws.com/uploads/sites/109/Trustee-Area-Map.pdf. Titled "Approved Trustee Areas, May 2022"; ArcMap export; no geometry.
  - Interactive: https://www.google.com/maps/d/u/0/edit?mid=1BizBoI6gCjAlAKh1h9b_GoSz5_jH-6Jv&usp=sharing
- **Geometry downloaded: yes, two ways.**
  - My Maps KML export (`https://www.google.com/maps/d/kml?mid=1BizBoI6gCjAlAKh1h9b_GoSz5_jH-6Jv&forcekml=1`): 6 polygons, the district outline plus "Trustee Area No. 1" to "No. 5". The area number is in the Placemark name. Coordinates are WGS84 lon/lat, all rings are closed, and the five areas sum to the outline within 0.1 percent.
  - RR/CC: 6 features (outline plus TA 1 to 5).
- **Licence:** none stated on the My Maps map. RR/CC terms as above.
- **1 February 2022 redistricting:** the exact wording is saved in `saugus-union-school-district/redistricting-2022-text.txt`. It cites Education Code 5019.51 and 5019.2, the 27 September 2021 census release, the 10 percent variance, the 28 February 2022 deadline, the recommendations of Orbach, Huff & Henderson, LLP and Cooperative Strategies, and that "each current member represents the same schools". **The page links no resolution or demographer's report.** A web search did not find the resolution either. It would be in the 1 February 2022 board agenda packet, which the page does not link.

### William S. Hart Union High School District
- **Published:** the "Trustee Area Voting Map" page (https://www.hartdistrict.org/apps/pages/index.jsp?uREC_ID=739893&type=d&pREC_ID=1152469) says the map was approved in 2015 under the CVRA and most recently updated in January 2022. Its button opens an **ArcGIS Web AppBuilder** app built by Davis Demographics: https://ddp.maps.arcgis.com/apps/webappviewer/index.html?id=e8496474353c426b855f360b30633ef2, titled "Trustee Areas - Adopted January 2022".
- **ArcGIS chain:** app `e8496474353c426b855f360b30633ef2` leads to web map `0c526ac283dc4c6aba78d369d7a9b626`, which uses the feature service item `5876e532709849b09ae3e3f72d0b72d8`: https://services.arcgis.com/obpUicnfIYG1DOsR/arcgis/rest/services/Hart_Trustee_Areas/FeatureServer/14 (layer name "HartScenario2_NewTrusteeBoundaries").
- **Geometry downloaded: yes.** `.../FeatureServer/14/query?where=1%3D1&outFields=*&f=geojson` returns 5 polygons. The area is in `EQUIZONE` ("District 1" to "District 5"); the native CRS is EPSG:2229 and the GeoJSON is WGS84. The `BoardMember` attribute is stale, from 2022 (Area 1 says Linda Storli). The RR/CC layers also have Hart (outline plus TA 1 to 5).
- **Licence:** the demographer's items have empty licence fields. RR/CC terms as above.
- **Resolution:** not linked from the page. The Signal reported the January 2022 adoption. The board's documents are on Simbli (https://simbli.eboardsolutions.com/SB_Meetings/SB_MeetingListing.aspx?S=36030502); not searched.

### Newhall School District
- **Published:** a static PDF only, embedded on https://www.newhallschooldistrict.com/trustee-area-map: https://files.smartsites.parentsquare.com/7444/newhall_trusteeareasmap_wstlabels.pdf, titled "Approved Trustee Areas, April 2022". No geometry.
- **Geometry downloaded: yes, from RR/CC** (outline plus TA 1 to 5).
- **Also found, not linked from the district site:** a public National Demographics Corporation web map, "Newhall School District Trustee Areas" (`1f5f05fa09274b14a24f417bdec7da68`). It holds the **2015 adopted plan** as an embedded feature collection. I converted it to `ndc-newhall-2015-adopted-plan-derived.geojson`: 5 polygons, area in `DISTRICT`. It is historical, and its areas are close to the current RR/CC areas.
- **Resolution:** not found on the site. The Signal (November 2021, https://signalscv.com/2021/11/school-districts-to-adjust-trustee-areas) reported that Newhall readopted its previous map because population barely changed. That is a secondary source, not checked against a resolution.

### Sulphur Springs Union School District
- **Published:** a static PDF only, embedded on https://www.sssd.k12.ca.us/trustee-area-map: https://files.smartsites.parentsquare.com/8476/trustee_area_map_as_of_12182024.pdf. Titled "Approved Trustee Areas", created 3 February 2022, and relabelled with election years 2026 and 2028 (the file name says as of 18 December 2024). No geometry.
- **Geometry downloaded: yes, from RR/CC** (outline plus TA 1 to 5).
- **Resolution:** not linked. The Signal (December 2021) reports that Cooperative Strategies and Fagen Friedman & Fulfrost presented three scenarios.

### Castaic Union School District
- **Published:** https://www.castaicusd.com/apps/pages/index.jsp?uREC_ID=799402&type=d&pREC_ID=1383377 links four things:
  - An interactive map by National Demographics Corporation: http://www.arcgis.com/apps/View/index.html?appid=bbd023355c244e22b1a54e145ef2aee9
  - The board-approved map PDF ("Board-Adopted Red II, February 16, 2017")
  - A 2020 census data sheet
  - **Resolution 21/22-16**: https://4.files.edl.io/0582/02/01/22/204336-62d3ee39-834e-46bd-9972-0e45431c9df5.pdf. Adopted 13 January 2022, it readopts the 16 February 2017 map unchanged after the 2020 census, with National Demographics Corporation as demographer.
- **Geometry downloaded: yes, two ways.**
  - The NDC web map `1e600c98f1f5427394a5265b6c7b50b8` embeds "Board-Adopted Red II" as a feature collection. I converted it to `castaic-board-adopted-red-ii-derived.geojson`: 5 polygons, area letter A to E in `DISTRICT`.
  - RR/CC: outline plus TA 1 to 5.
- **Numbering:** the district letters its areas A to E, while RR/CC numbers them 1 to 5. By geometry, A=1, B=2, C=3, D=4, E=5.
- **Licence:** none stated on the NDC items.

### Santa Clarita Valley Water (SCV Water)
- **Published:** static PDFs and documents on https://www.yourscvwater.com/governance/redistricting and the board page. All are saved:
  - The current map, "Electoral Divisions 5/13/2024" (exported November 2024). Its notes say it is "provided without any warranty" and "Any resale of this information is prohibited".
  - Street-by-street boundary descriptions updated 13 May 2024.
  - The March 2022 map, the 16 March 2022 press release, and the January and March 2022 packets and presentations.
- **Geometry downloaded: yes, from RR/CC Division_Boundaries**: 3 features ("SANTA CLARITA VALLEY WATER AGENCY DIV 1/2", "SANTA CLARITA VLY WATER AGENCY DIV 3").
- **Gap:** the RR/CC layer stops at the Los Angeles County line. The three divisions total about 472 sq km, while the agency's own public service-area polygon (SCVWA GIS Dept., item `4651e9afd9e04e7ab3ed13eefca3d2da`, saved) is 507.6 sq km and reaches west into Ventura County. The Ventura part of Division 3 is therefore missing. SCV Water publishes no division layer of its own on its public ArcGIS org.

### City of Santa Clarita
- **Published:**
  - https://santaclarita.gov/district-elections/ shows the final map, linking https://filecenter.santa-clarita.com/GIS/citycouncildistricts.pdf (exported 5 December 2023).
  - Ordinance 23-4 (adopted 13 June 2023) is saved.
  - The draft-maps page links NDC's public app (`50f18095c40748ee9afbe749d6d440b3`), which holds the draft and joint maps.
  - The district-elections page says the final lines were "minor[ly] adjust[ed]" to follow parcel lines per the County Registrar's GIS specifications.
- **Geometry downloaded: yes, two ways.**
  - City GIS: https://maps.santa-clarita.com/arcgis/rest/services/Boundary/MapServer/17 ("City Council Districts", used by the City's "Mapping Your City" residency app). 5 features; district number in `District`, name in `DistrictName`; native EPSG:2229, saved as WGS84. District 2 has two parts.
  - RR/CC Division_Boundaries: 5 features. The two match to within 1 percent of area per district.
- **Licence:** the City service states none. The City's Website Terms and Conditions say the content is copyright of the City, "freely available for non-commercial, non-profit making use", and "may not be 'mirrored' without the written permission of Technology Services". The RR/CC copy carries the County's broader licence.
- Aside: the same City MapServer has a point layer named "SCV History" (layer 15, fields SITE, STATE_ID). It may be of interest for Places later; I did not download it.

### Also captured: Santa Clarita Community College District
RR/CC has its five trustee areas in Division_Boundaries. I saved them because they came with the same query; they were not requested.

### LA County data portals
- The County's public "Political_Boundaries" MapServer (public.gis.lacounty.gov) has school district outlines, supervisorial and legislative districts, and LA City council districts. It has **no** trustee area or local division layers.
- The trustee area and division layers are the RR/CC hosted services listed above, found through ArcGIS Online search. They are not on the eGIS hub's front page.

## Cross-check: area per district (sq km), body's own geometry vs RR/CC

| Body | Own source | Area 1 | Area 2 | Area 3 | Area 4 | Area 5 |
|---|---|---|---|---|---|---|
| Saugus | My Maps KML | 20.35 / 20.54 | 76.50 / 76.30 | 9.77 / 9.78 | 22.78 / 22.68 | 125.21 / 125.26 |
| Hart | Davis Demographics service | 449.19 / 449.31 | 120.01 / 121.12 | 61.66 / 61.72 | 189.89 / 189.99 | 201.05 / 203.47 |
| Castaic (A to E) | NDC 2017 plan | 268.06 / 271.89 | 2.45 / 2.60 | 6.25 / 6.28 | 75.53 / 74.64 | 51.34 / 49.89 |
| Newhall | NDC 2015 plan | 107.45 / 107.47 | 12.08 / 12.79 | 14.68 / 14.95 | 17.46 / 17.76 | 15.68 / 15.84 |
| City council | City GIS | 30.99 / 30.92 | 45.89 / 45.68 | 29.45 / 29.46 | 30.41 / 30.40 | 53.43 / 53.47 |

All polygons in all files are closed rings with bounding boxes inside the valley (longitude about -118.80 to -118.32, latitude about 34.32 to 34.62). I checked areas with a flat-earth approximation, which is good enough for a comparison but is not a survey measure.

## Licences

- **LA County RR/CC layers:** LA County GIS Terms of Use (https://egis-lacounty.hub.arcgis.com/pages/terms-of-use/; text read from hub page item `799d631faeae4703949b0061cef7a611`). The terms allow copying, publishing, adapting and commercial use; forbid implying County endorsement; give no warranty; and cap County liability at $100. The recommended citation is: Organization. Year. Title [dataset]. Repository. Accessed date. Link.
- **City of Santa Clarita GIS:** non-commercial use only, and no mirroring without written permission (website Terms and Conditions).
- **SCV Water PDFs:** no warranty; resale prohibited.
- **Demographer items (Davis Demographics, NDC) and the Saugus My Maps:** none stated.

## For Nathan to decide

1. **Source of record for the seat-control map.** I recommend the RR/CC layers for every body: one licence that allows republishing, one schema, kept current by the County (September 2026). The district and City copies would serve as cross-checks only.
2. **SCV Water's Ventura County slice.** The RR/CC Division 3 polygon stops at the county line. The options are to accept the LA-only shape with a note, or to ask SCV Water for its division GIS.
3. **City data terms.** If the City's own layer is ever used instead of the RR/CC copy, its non-commercial and no-mirroring terms apply. Using the RR/CC copy avoids the question.
4. **Historical boundaries.** For the 2015 to 2022 period, Newhall's 2015 plan (NDC) and Castaic's 2017 plan (still current) exist as geometry. Hart's and Saugus's pre-2022 plans and the at-large eras were not found as geometry.
5. **Castaic labels.** Castaic letters its areas A to E, while RR/CC numbers them 1 to 5 (A=1 to E=5). Which label shows in the seat control is Nathan's call.

## Not done

- Resolutions for Saugus (1 February 2022), Hart (January 2022), Newhall and Sulphur Springs: none is linked from the district sites, and I did not search the board agenda systems (Simbli and others). Only Castaic's resolution was found and saved.
- No geometry was imported into Craft. That waits on Nathan's choice of source.
