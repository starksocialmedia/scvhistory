# Retype dry run: flagged photographs and documents (7 October 2026)

Read-only. Claude. Nothing has been moved or changed. For Nathan to read and mark. Data beside this file in `retype-dry-run-2026-10-07.json`; scripts in `storage/runtime/retype/`.

Rule (Nathan): "a scanned newspaper page is an article that survives as an image. The scan is how we hold it, not what it is. Type follows the thing."

## How the two lists were rebuilt

The census JSON does not store the 39 or the 25 as lists, so both were recomputed from the census working files (`storage/runtime/type-census/dump.json`, `signals2.json`).

- **Photographs, 39.** The `[lines]` header has a "By ..." line or a "Publication | year" line; the title has none of the ephemera words (program, menu, brochure, ad, letter, catalog, comic, ticket, map, portrait, painting, oil on, press release, poll, report, petition, exhibit, instructions, book, intro); the header is not SCVHistory.com's own byline. No PDF or flipbook condition. Result: exactly 39, the same as the census. Adding the PDF or flipbook condition gives the census's strict 21.
- **Documents, 25.** `sourceLine` has "... | date with a year", or the title names a newspaper inside parentheses (the census `title_pub` pattern). Result: exactly 25 (24 from sourceLine, plus 31412 from its title), with 24 articles and the false positive 31306, as the census says.
- Not in the 25 although the census counted them articles: 28057 (Los Angeles Herald 1875 letter; newspaper named in the title but not in parentheses), 26983 (Santa Cruz Sentinel), 27374 (Perkins 1957; also duplicated as article 1434), 28281 and 28287 (Connie Worden-Roberts essays). Not in the 39 although the census counted them articles: 2963 and 3049 (no byline or "Publication | date" line in the header). These are not reviewed here.

## Counts by verdict

| List | Article | Unsure | Not an article | Total |
| --- | --- | --- | --- | --- |
| Photographs | 17 | 4 | 18 (9 of them the Land of Sunshine pictures) | 39 |
| Documents | 24 | 0 | 1 | 25 |

## Read this first

- **The headline at the top of a legacy page is often Leon's, not the publication's.** Leon says so on the Land of Sunshine pages ("The headline ... is ours ... the story was originally published under the nondescript headline, 'Miles of Untold Wealth.'"). Checked against the PDF or the scan where the mirror has one: 4783, 4909 and 20107 printed a different headline; 5145, 5205, 5385 and 4967 probably did. The proposed title follows the printed head where it could be read, and says so under "Headline check". The rest are unchecked and are marked.
- **Every proposed article lacks a writtenBy match** except 31412 (Leon Worden, already set). None of the other bylines has a person record. Leave writtenBy empty and keep the byline as printed; the person rule decides later whether any author earns a record.
- **publishedBy:** only Los Angeles Herald (390), Los Angeles Times (30518), SCVTV (30520) and The Santa Clarita Valley Signal (376) exist. None of the magazines has an organization record.
- **Eight Los Angeles Times documents are disabled** (31310, 31316, 31318, 31320, 31322, 31324, 31328, 31330). A retype must keep them disabled.
- **Field mapping a retype would need.** Photographs carry photoPlaces, photoOrganizations, photoEvents, photoArticles and derivedImageLinks; articles have depictsPlace, subjectOrganization, articleEvents, relatedArticles and derivedImageLinks. The photograph-only credit fields (creditRaw, creditKind, photoSourceCode and the rest) have no home on an article (census section 3). Documents to articles map one to one for sourceLine, originallyPublishedTitle, originalPublishDate, publishedBy, writtenBy, subjectPerson and partOfCollection; documentFiles has no article field (articles use recordDocuments).
- **Eighteen documents are pointed at by an event's sourceDocuments** (the Saugus High School Shooting, 31338, and Santa Clarita Cityhood, 31359), and 28305 by two obituaries' obitCompanions. A retype has to repoint those relations or the event and obituary pages lose their sources.

## Special cases

### A. The nine Land of Sunshine pictures (3051, 3053, 3055, 3057, 3059, 3061, 3063, 3065, 3067)

What they are: nine photographs printed in F.A. Pattee's article in The Land of Sunshine, November 1900, scanned one by one. Each page carries the same header and the full transcription of the article below Leon's caption and notes. Leon says the head "Piru Rancho: The Next Great Oil Field" is his; the magazine printed "Miles of Untold Wealth."

Recommendation: the nine stay photographs. Make one new article, title "Miles of Untold Wealth." (By F.A. Pattee, The Land of Sunshine, Vol. XII No. 5, November 1900, pp. 389-401), holding the transcription once, with Leon's head kept as a legacy or webmaster note. Each picture then points at it with photoArticles. The repeated transcription stays in the photograph bodies until Nathan says otherwise (Leon's prose is not edited). They are already joined to each other and to 5209 by derivedImageLinks.

### B. Placerita Oil Field, 3957, 3959 and 3961 (not in the 39)

What they are: three photographs Leon took on November 17, 2013 (creditKind "digital image by Leon Worden"), on three legacy pages (lw2518a, b, c) that carry the same text below the picture: two SCVNews.com articles by Leon Worden ("Placerita Oil Field Has New Owners", Tuesday, December 17, 2013, and "A New Owner for Placerita Oil Field on Sierra Highway", Thursday, February 21, 2013) and an extract of a Berry Petroleum Form 8-K filed with the SEC (February 12, 1999). There are no scans. The census called the three "one article"; more exactly, they are three photographs that share two born-digital articles.

Recommendation: the three stay photographs. Make two articles from the shared text (By Leon Worden; SCVNews.com, publishedBy SCVTV (30520), as 31314 does; writtenBy Leon Worden (279)), and point the three photographs at both with photoArticles. They already point at article 2151 ("63. Confusion Hill"); keep that. No article for either SCVNews piece exists yet (searched articles for "Placerita"). The 8-K extract can stay in body text or become a document.

#### 3957 | NOT AN ARTICLE | Placerita Oil Field 2013

- Section: photographs. Census: web-article-on-photo-page. Legacy URL: `/scvhistory/lw2518a.htm`
- Printed header in the record (`[lines]`):

> Placerita Oil Field
> Sierra Highway, Newhall

- Legacy page prints:

> Placerita Oil Field
> Sierra Highway, Newhall
> Placerita Oil Field Has New Owners
> By Leon Worden
> | SCVNews.com | Tuesday, December 17, 2013
> Berry Petroleum Co. Form 8-K
> (Filed with) Securities and Exchange Commission
> | February 12, 1999
> A New Owner for Placerita Oil Field on Sierra Highway
> By Leon Worden
> | SCVNews.com | Thursday, February 21, 2013

- Verdict: **NOT AN ARTICLE**: a photograph (Leon Worden's own digital image, November 17, 2013). Not one of the 39. See special case B.
- Links to carry: derivedImageLinks from 3 records (2151, 3959, 3961); its own relations: photoArticles -> articles:2151:63. Confusion Hill.

#### 3959 | NOT AN ARTICLE | Placerita Oil Field 2013

- Section: photographs. Census: web-article-on-photo-page. Legacy URL: `/scvhistory/lw2518b.htm`
- Printed header in the record (`[lines]`):

> Placerita Oil Field
> Sierra Highway, Newhall

- Legacy page prints:

> Placerita Oil Field
> Sierra Highway, Newhall
> Placerita Oil Field Has New Owners
> By Leon Worden
> | SCVNews.com | Tuesday, December 17, 2013
> Berry Petroleum Co. Form 8-K
> (Filed with) Securities and Exchange Commission
> | February 12, 1999
> A New Owner for Placerita Oil Field on Sierra Highway
> By Leon Worden
> | SCVNews.com | Thursday, February 21, 2013

- Verdict: **NOT AN ARTICLE**: a photograph (Leon Worden's own digital image, November 17, 2013). Not one of the 39. See special case B.
- Links to carry: derivedImageLinks from 3 records (2151, 3957, 3961); its own relations: photoArticles -> articles:2151:63. Confusion Hill.

#### 3961 | NOT AN ARTICLE | Placerita Oil Field 2013

- Section: photographs. Census: web-article-on-photo-page. Legacy URL: `/scvhistory/lw2518c.htm`
- Printed header in the record (`[lines]`):

> Placerita Oil Field
> Sierra Highway, Newhall

- Legacy page prints:

> Placerita Oil Field
> Sierra Highway, Newhall
> Placerita Oil Field Has New Owners
> By Leon Worden
> | SCVNews.com | Tuesday, December 17, 2013
> Berry Petroleum Co. Form 8-K
> (Filed with) Securities and Exchange Commission
> | February 12, 1999
> A New Owner for Placerita Oil Field on Sierra Highway
> By Leon Worden
> | SCVNews.com | Thursday, February 21, 2013

- Verdict: **NOT AN ARTICLE**: a photograph (Leon Worden's own digital image, November 17, 2013). Not one of the 39. See special case B.
- Links to carry: derivedImageLinks from 3 records (2151, 3957, 3959); its own relations: photoArticles -> articles:2151:63. Confusion Hill.

## Photographs (39)

### Article (17)

#### 4435 | ARTICLE | New Indian-Frontier Village: Description of Callahan's Old West, 1965.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw2724.htm`
- Printed header in the record (`[lines]`):

> New Indian-Frontier Village
> [Description of Callahan's Old West Trading Post.]
> By Margaret Romer
> Desert Magazine | April 1965

- Legacy page prints:

> New Indian-Frontier Village
> By Margaret Romer
> Desert Magazine | April 1965

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "New Indian-Frontier Village"
- Byline: By Margaret Romer. Publication: Desert Magazine. Date as printed: April 1965.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: No PDF on the mirror; headline taken from the legacy header, not checked against the scan.
- Links to carry: derivedImageLinks from 8 records (2709, 2711, 2991, 2993, 2995, 4011, 4745, 5381).

#### 4691 | ARTICLE | The Terrifying Tale of the Runaway Drone (Pageant, May 1957).

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw2925.htm`
- Printed header in the record (`[lines]`):

> THE BATTLE OF PALMDALE
> The Terrifying Tale of the Runaway Drone
> High in the air, two rocket-firing jets vs. an old, pilotless World War II Hellcat. On the ground, 25 miles of fiery destruction.
> By Michael Frost | Pageant magazine Vol. 12 No. 11: May 1957.

- Legacy page prints:

> THE BATTLE OF PALMDALE
> The Terrifying Tale of the Runaway Drone
> High in the air, two rocket-firing jets vs. an old, pilotless World War II Hellcat. On the ground, 25 miles of fiery destruction.
> By Michael Frost | Pageant magazine Vol. 12 No. 11: May 1957.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "The Terrifying Tale of the Runaway Drone"
- Byline: By Michael Frost. Publication: Pageant magazine, Vol. 12 No. 11. Date as printed: May 1957.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: Printed with the kicker "THE BATTLE OF PALMDALE" above it (kept as a subheadline, not appended). No PDF on the mirror to check.
- Links to carry: derivedImageLinks from 1 records (5613).

#### 4783 | ARTICLE | The Bogus Story of Tom Vernon and the Sweetwater Incident, Golden West 1967.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw2992.htm`
- Printed header in the record (`[lines]`):

> The Bogus Story of Tom Vernon and the Sweetwater Incident.
> By Carl W. Breihan.
> Golden West magazine | July 1967.

- Legacy page prints:

> The Bogus Story of Tom Vernon and the Sweetwater Incident.
> By Carl W. Breihan.
> Golden West magazine | July 1967.
> ...
> Tragedy on the Sweetwater.
> By Carl W. Breihan
> Golden West magazine | July 1967.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "Tragedy on the Sweetwater."
- Byline: By Carl W. Breihan. Publication: Golden West magazine. Date as printed: July 1967.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: The page's first header, "The Bogus Story of Tom Vernon and the Sweetwater Incident.", is Leon's. The magazine printed "TRAGEDY ON THE SWEETWATER" (PDF text and a second header further down the legacy page).
- Links to carry: derivedImageLinks from 10 records (4599, 4643, 4645, 4677, 4689, 4693, 4727, 4771, 5081, 5321).

#### 4909 | ARTICLE | Fort Oghora Built for NBC's '77th Bengal Lancers' | TV Guide 1957.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3097.htm`
- Printed header in the record (`[lines]`):

> Fort Oghora Takes Shape for TV's "77th Bengal Lancers."
> Despite Complaints from British and Pakistani Governments, Screen Gems Builds "Composite" Fort.
> TV Guide | 1957.

- Legacy page prints:

> Fort Oghora Takes Shape for TV's "77th Bengal Lancers."
> Despite Complaints from British and Pakistani Governments, Screen Gems Builds "Composite" Fort.
> TV Guide | 1957.
> ...
> India Never Had It So Good.
> The Fort (the Outside Only, Sahib) and Incidentals Add Up to $182,000.

- Verdict: **ARTICLE**: magazine article (TV Guide); no byline.
- Would become: article. Proposed title: "India Never Had It So Good."
- Byline: none. Publication: TV Guide. Date as printed: 1957 (no day or issue printed in the header).
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: The page's first header, "Fort Oghora Takes Shape for TV's ..." with its deck, is Leon's. The magazine printed "... NEVER HAD IT SO GOOD" with deck "The Fort (The Outside Only, Sahib) And Incidentals Add Up To $182,000" (PDF text; also the second header on the legacy page).
- Links to carry: derivedImageLinks from 4 records (659, 4839, 5465, 5507).

#### 4919 | ARTICLE | The Move to Secede from Los Angeles County, by Bob Simmons, 1976.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3104.htm`
- Printed header in the record (`[lines]`):

> REVOLT OF THE BALKANS
> The Move to Secede from Los Angeles County.
> By Bob Simmons.
> California Journal | April 1976.

- Legacy page prints:

> REVOLT OF THE BALKANS
> The Move to Secede from Los Angeles County.
> By Bob Simmons.
> California Journal | April 1976.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "The Move to Secede from Los Angeles County."
- Byline: By Bob Simmons. Publication: California Journal. Date as printed: April 1976.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF confirms the printed two-deck head: "REVOLT OF THE BALKANS / THE MOVE TO SECEDE FROM LOS ANGELES COUNTY". Nathan to say which deck is the title; proposed keeps the main deck and puts "Revolt of the Balkans" as kicker.
- Links to carry: derivedImageLinks from 2 records (2163, 4921); its own relations: photoArticles -> articles:2163:69. Rebels With a Cause.

#### 4967 | ARTICLE | 1st City Council Members' New Year's Resolutions, SCV Magazine, Winter 1987-88.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3149.htm`
- Printed header in the record (`[lines]`):

> "We Hereby Resolve..."
> New Year's Resolutions of First City Council Members-Elect.
> Santa Clarita Valley Magazine | Winter 1987-88.

- Legacy page prints:

> "We Hereby Resolve..."
> New Year's Resolutions of First City Council Members-Elect.
> Santa Clarita Valley Magazine | Winter 1987-88.

- Verdict: **ARTICLE**: magazine feature (council members' resolutions, each in their own words); no single byline.
- Would become: article. Proposed title: ""We Hereby Resolve...""
- Byline: none (five council members-elect write in turn). Publication: Santa Clarita Valley Magazine. Date as printed: Winter 1987-88.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF text begins "City Council: ''We Hereby ..."; the printed head may be "City Council: 'We Hereby Resolve...'". Check the scan.
- Links to carry: derivedImageLinks from 2 records (4009, 5401).

#### 5073 | ARTICLE | Story: Southern Pacific's Historic Soledad Canyon Link, 1984.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3254.htm`
- Printed header in the record (`[lines]`):

> The Old Road to Los Angeles.
> Southern Pacific's Soledad Canyon Route.
> By Bruce Kelly.
> Pacific News No. 249 | April 1984.

- Legacy page prints:

> The Old Road to Los Angeles.
> Southern Pacific's Soledad Canyon Route.
> By Bruce Kelly.
> Pacific News No. 249 | April 1984.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "The Old Road to Los Angeles."
- Byline: By Bruce Kelly. Publication: Pacific News No. 249. Date as printed: April 1984.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF confirms: "SOUTHERN PACIFIC'S SOLEDAD CANYON ROUTE / THE OLD ROAD TO LOS ANGELES".
- Links to carry: derivedImageLinks from 4 records (2753, 5043, 5145, 5391); its own relations: photoOrganizations -> organizations:16101:Southern Pacific Railroad.

#### 5087 | ARTICLE | The Winged Monster of Elizabeth Lake (Old West, Fall 1969).

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3265.htm`
- Printed header in the record (`[lines]`):

> The Winged Monster of Elizabeth Lake.
> Except from "Our Country's Mysterious Monsters."
> By J.K. Parrish | Illustrated by Al Martin Napoletano.
> Old West, Fall 1969.

- Legacy page prints:

> The Winged Monster of Elizabeth Lake.
> Except from "Our Country's Mysterious Monsters."
> By J.K. Parrish | Illustrated by Al Martin Napoletano.
> Old West, Fall 1969.

- Verdict: **ARTICLE**: magazine piece with byline (a book excerpt reprinted in a magazine).
- Would become: article. Proposed title: "The Winged Monster of Elizabeth Lake."
- Byline: By J.K. Parrish | Illustrated by Al Martin Napoletano. Publication: Old West. Date as printed: Fall 1969.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF first page is the cover; headline not checked against the scan.
- Links to carry: derivedImageLinks from 1 records (5135); its own relations: photoPlaces -> places:15994:Elizabeth Lake.

#### 5137 | ARTICLE | Stock Car Racing Magazine, July 1981.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3312.htm`
- Printed header in the record (`[lines]`):

> The Story of Dan Press.
> By Larry Warren.
> Stock Car Racing magazine | July 1981.

- Legacy page prints:

> The Story of Dan Press.
> By Larry Warren.
> Stock Car Racing magazine | July 1981.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "The Story of Dan Press."
- Byline: By Larry Warren. Publication: Stock Car Racing magazine. Date as printed: July 1981.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF confirms "The story of DAN PRESS". Current title "Stock Car Racing Magazine, July 1981." names only the magazine.
- Links to carry: derivedImageLinks from 1 records (4787).

#### 5145 | ARTICLE | Railfanning on Southern Pacific's Saugus Line, November 1991.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3316.htm`
- Printed header in the record (`[lines]`):

> Commuting Soledad Canyon: Southern Pacific's Saugus Line.
> Railfanning in the '90s.
> By Randy Keller.
> Pacific Rail News No. 336, November 1991.

- Legacy page prints:

> Commuting Soledad Canyon: Southern Pacific's Saugus Line.
> Railfanning in the '90s.
> By Randy Keller.
> Pacific Rail News No. 336, November 1991.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "Commuting Southern Pacific's Saugus Line"
- Byline: By Randy Keller. Publication: Pacific Rail News No. 336. Date as printed: November 1991.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: Legacy header reads "Commuting Soledad Canyon: Southern Pacific's Saugus Line."; the PDF text reads "COMMUTING SOUTHERN PACIFIC'S SAUGUS LINE". Check the scan before choosing.
- Links to carry: derivedImageLinks from 4 records (2753, 5043, 5073, 5391); its own relations: photoOrganizations -> organizations:16101:Southern Pacific Railroad.

#### 5205 | ARTICLE | Will Rogers and Charlie Russell | Story by Arnold Marquis, 1967.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3361.htm`
- Printed header in the record (`[lines]`):

> Will Rogers and Charlie Russell.
> By Arnold Marquis.
> Frontier Times, Vol. 41 No. 4, June-July 1967.

- Legacy page prints:

> Will Rogers and Charlie Russell.
> By Arnold Marquis.
> Frontier Times, Vol. 41 No. 4, June-July 1967.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "Will and Charlie"
- Byline: By Arnold Marquis. Publication: Frontier Times, Vol. 41 No. 4. Date as printed: June-July 1967.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: Legacy header reads "Will Rogers and Charlie Russell."; the PDF text reads "WILL and CHARLIE By ARNOLD MARQUIS". Check the scan before choosing.
- Links to carry: derivedImageLinks from 1 records (3647); its own relations: featuredImage -> Asset:11773:Lw3361b large.

#### 5233 | ARTICLE | Backgrounder: Express Companies & Staging in California | True West, August 1966.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3390.htm`
- Printed header in the record (`[lines]`):

> The Boom Days of Staging.
> Backgrounder on Express Companies and Stage Lines in California.
> By Waddell F. Smith.
> True West | August 1966.

- Legacy page prints:

> The Boom Days of Staging.
> Backgrounder on Express Companies and Stage Lines in California.
> By Waddell F. Smith.
> True West | August 1966.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "The Boom Days of Staging."
- Byline: By Waddell F. Smith. Publication: True West. Date as printed: August 1966.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF first page is a photograph caption; headline not checked against the scan.
- Links to carry: derivedImageLinks from 5 records (827, 3659, 3661, 3663, 3665); its own relations: photoArticles -> articles:827:Chapter 4: Children of Nature.

#### 5245 | ARTICLE | J.C. Agajanian: Saugus Hog Rancher, Race Promoter | Biographical Profile, Car Life, November 1964.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3404.htm`
- Printed header in the record (`[lines]`):

> Hog Rancher, Garbage Collector, Race Promoter & Gentleman:
> J.C. Agajanian, the Haberdasher's Best Friend.
> By Bill Libby.
> Car Life magazine, November 1964.

- Legacy page prints:

> Hog Rancher, Garbage Collector, Race Promoter & Gentleman:
> J.C. Agajanian, the Haberdasher's Best Friend.
> By Bill Libby.
> Car Life magazine, November 1964.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "J.C. Agajanian, the Haberdasher's Best Friend."
- Byline: By Bill Libby. Publication: Car Life magazine. Date as printed: November 1964.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: Printed with the kicker "Hog Rancher, Garbage Collector, Race Promoter & Gentleman:" (kept as kicker, not appended). Headline not checked against the scan.
- Links to carry: derivedImageLinks from 1 records (4609).

#### 5361 | ARTICLE | Bermite's Patrick Lizza: He Sets the Sky On Fire | Saturday Evening Post, 10-13-1951.

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3517.htm`
- Printed header in the record (`[lines]`):

> He Sets the Sky On Fire!
> By Frank J. Taylor.
> Saturday Evening Post | October 13, 1951.

- Legacy page prints:

> He Sets the Sky On Fire!
> By Frank J. Taylor.
> Saturday Evening Post | October 13, 1951.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "He Sets the Sky On Fire!"
- Byline: By Frank J. Taylor. Publication: Saturday Evening Post. Date as printed: October 13, 1951.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF first page is the cover; headline not checked against the scan.
- Links to carry: derivedImageLinks from 7 records (2813, 4115, 4145, 4155, 4267, 4459, 5017).

#### 5383 | ARTICLE | Course of the Month: Indian Dunes (Dirt Bike, June 1973).

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3534.htm`
- Printed header in the record (`[lines]`):

> Course of the Month: Indian Dunes.
> Where half of L.A.'s riders thrash their dirt bikes.
> By the Staff of Dirt Bike.
> Dirt Bike magazine | June 1973.

- Legacy page prints:

> Course of the Month: Indian Dunes.
> Where half of L.A.'s riders thrash their dirt bikes.
> By the Staff of Dirt Bike.
> Dirt Bike magazine | June 1973.

- Verdict: **ARTICLE**: magazine article with staff byline and masthead line.
- Would become: article. Proposed title: "Course of the Month: Indian Dunes."
- Byline: By the Staff of Dirt Bike. Publication: Dirt Bike magazine. Date as printed: June 1973.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF first page is the cover; headline not checked against the scan.
- Links to carry: derivedImageLinks from 6 records (4995, 5273, 5327, 5417, 5425, 5453).

#### 5385 | ARTICLE | Southern Pacific and T&NO Class M-4 2-6-0s (Moguls).

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3536.htm`
- Printed header in the record (`[lines]`):

> Southern Pacific and T&NO Class M-4 2-6-0s (Moguls).
> Some of these turn-of-the-century Moguls served into the 1950s.
> By Andy Sperandeo | Illustrations by Ed Gebhardt.
> Model Railroader | August 1994.

- Legacy page prints:

> Southern Pacific and T&NO Class M-4 2-6-0s (Moguls).
> Some of these turn-of-the-century Moguls served into the 1950s.
> By Andy Sperandeo | Illustrations by Ed Gebhardt.
> Model Railroader | August 1994.

- Verdict: **ARTICLE**: magazine article with byline and masthead line.
- Would become: article. Proposed title: "Southern Pacific and T&NO Class M-4 2-6-0s (Moguls)."
- Byline: By Andy Sperandeo | Illustrations by Ed Gebhardt. Publication: Model Railroader. Date as printed: August 1994.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF text reads "Southern Pacific and Texas & ..." so the printed head may spell out Texas & New Orleans. Check the scan.
- Links to carry: derivedImageLinks from 4 records (4201, 5607, 5619, 5635); its own relations: photoOrganizations -> organizations:16101:Southern Pacific Railroad.

#### 5425 | ARTICLE | Indian Dunes' First Competitive Event (Story November 1970).

- Section: photographs. Census: article-sure. Legacy URL: `/scvhistory/lw3576.htm`
- Printed header in the record (`[lines]`):

> Indian Dunes' First Competitive Event.
> N.O.R.R.A. Sanctions Buggy and Bike event at Motor Playground.
> Road Test Dune Buggy, Vol. 2 No. 11 | November 1970.

- Legacy page prints:

> Indian Dunes' First Competitive Event.
> N.O.R.R.A. Sanctions Buggy and Bike event at Motor Playground.
> Road Test Dune Buggy, Vol. 2 No. 11 | November 1970.

- Verdict: **ARTICLE**: magazine news story with masthead line; no byline.
- Would become: article. Proposed title: "Indian Dunes' First Competitive Event."
- Byline: none. Publication: Road Test Dune Buggy, Vol. 2 No. 11. Date as printed: November 1970.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF text (poor OCR) agrees.
- Links to carry: derivedImageLinks from 6 records (4995, 5273, 5327, 5383, 5417, 5453).

### Unsure (4)

#### 4269 | UNSURE | Construction Begins on Castaic Dam, Summer 1966.

- Section: photographs. Census: article-likely. Legacy URL: `/scvhistory/lw2619.htm`
- Printed header in the record (`[lines]`):

> Construction Begins on Castaic Dam.
> Compass | Jerry Custis, Editor
> Pacific Telephone Co. Los Angeles North Area, Pasadena, Calif. | Vol. 3 No. 11 | August 8, 1966.

- Legacy page prints:

> Construction Begins on Castaic Dam.
> Compass | Jerry Custis, Editor
> Pacific Telephone Co. Los Angeles North Area, Pasadena, Calif. | Vol. 3 No. 11 | August 8, 1966.
> ...
> Castaic Dam Site Explodes Into Action

- Verdict: **UNSURE**: a company newsletter photo feature: two photographs with captions and a short paragraph under a headline. Article if a captioned photo spread counts; otherwise it stays a photograph credited to the newsletter.
- Would become: article, if Nathan agrees. Proposed title: "Castaic Dam Site Explodes Into Action"
- Byline: none (masthead names Jerry Custis, Editor). Publication: Compass (Pacific Telephone Co. Los Angeles North Area). Date as printed: August 8, 1966.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: The legacy page heads it "Construction Begins on Castaic Dam." (Leon's) and then prints "Castaic Dam Site Explodes Into Action" as a subhead; that looks like the newsletter's own headline. Not verified against the scan.
- Links to carry: derivedImageLinks from 4 records (3795, 3895, 4979, 5325).

#### 4735 | UNSURE | Edwin Carewe's 'Ramona' Serialized in French Movie Fanzine, 1928.

- Section: photographs. Census: article-unsure. Legacy URL: `/scvhistory/lw2958.htm`
- Printed header in the record (`[lines]`):

> Serialization of Edwin Carewe's "Ramona" (Chapters 4-5).
> Cine-Mirror | July 13, 1928.
> Publication Hebdomadaire Illustrée (Illustrated Weekly), Paris.

- Legacy page prints:

> Serialization of Edwin Carewe's "Ramona" (Chapters 4-5).
> Cine-Mirror | July 13, 1928.
> Publication Hebdomadaire Illustrée (Illustrated Weekly), Paris.

- Verdict: **UNSURE**: two pages of a serialized film novelization (chapters 4 and 5 of "Ramona") in a French weekly fan magazine. A periodical text piece, but fiction and an installment. Article only if serial fiction installments go in articles.
- Would become: article, if Nathan agrees. Proposed title: "Serialization of Edwin Carewe's "Ramona" (Chapters 4-5)."
- Byline: none printed on the legacy header. Publication: Cine-Mirror. Date as printed: July 13, 1928.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: The legacy headline is a description, not the printed French title; the scan would supply the real heading.
- Links to carry: derivedImageLinks from 4 records (4703, 5163, 5347, 5559).

#### 5199 | UNSURE | William S. Hart in 'Blixtens Broder' ('O'Malley of the Mounted'), Filmnyheter, Sweden, 1921.

- Section: photographs. Census: article-unsure. Legacy URL: `/scvhistory/lw3355.htm`
- Printed header in the record (`[lines]`):

> Filmnyheter (Film News).
> William S. Hart in "Blixtens Broder" ("O'Malley of the Mounted").
> Vol. 2 No. 40 | November 21, 1921 | Stockholm: Zetterlund & Thelanders Boktryckeri A.B.

- Legacy page prints:

> Filmnyheter (Film News).
> William S. Hart in "Blixtens Broder" ("O'Malley of the Mounted").
> Vol. 2 No. 40 | November 21, 1921 | Stockholm: Zetterlund & Thelanders Boktryckeri A.B.

- Verdict: **UNSURE**: a Swedish film-news issue given over to one Hart film, with the premiere program inside. Closer to a program or fan publication than to an article; the census put it with programs-adjacent text.
- Would become: article, if Nathan agrees. Proposed title: (no printed headline; see note)
- Byline: none. Publication: Filmnyheter (Film News), Vol. 2 No. 40. Date as printed: November 21, 1921.
- writtenBy candidate: none. publishedBy candidate: none.
- Links to carry: derivedImageLinks from 2 records (4175, 5591).

#### 5325 | UNSURE | Squadron of Giant Tillers Speed California's Castaic Dam, Towner Mfg. Co., ~1969.

- Section: photographs. Census: article-unsure. Legacy URL: `/scvhistory/lw3481.htm`
- Printed header in the record (`[lines]`):

> Squadron of Giant Tillers Speed California's Castaic Dam.
> By Ray Day.
> Towner Manufacturing Co., P.O. Box 6096, Santa Ana, Calif. 92706.

- Legacy page prints:

> Squadron of Giant Tillers Speed California's Castaic Dam.
> By Ray Day.
> Towner Manufacturing Co., P.O. Box 6096, Santa Ana, Calif. 92706.

- Verdict: **UNSURE**: a manufacturer's "Job Report" sales sheet (Towner Manufacturing Co.), "Photos and report by Ray Day, Western Editor for Construction Equipment Magazine" (PDF text). Trade promotional literature with a byline: an article reprint if Nathan counts it, otherwise ephemera.
- Would become: article, if Nathan agrees. Proposed title: "Squadron of Giant Tillers Speed California's Castaic Dam."
- Byline: By Ray Day. Publication: Towner Manufacturing Co. (Job Report). Date as printed: n.d. (~1969 by the current title).
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: PDF confirms the headline.
- Links to carry: derivedImageLinks from 4 records (3795, 3895, 4269, 4979).

### Not an article (18)

#### 4365 | NOT AN ARTICLE | USEPA: Health Effects in Saugus School Children Exposed to Vinyl Chloride, 1981

- Section: photographs. Census: official-or-business-document. Legacy URL: `/scvhistory/lw2661.htm`
- Printed header in the record (`[lines]`):

> Health Effects in Children Exposed to Vinyl Chloride
> Final Report, January 1981
> Science Applications, Inc. | 1801 Avenue of the Stars, Suite 1205 | Los Angeles, California 90067
> Richard A. Ziskind, Ph.D., Principal Investigator
> Daniel Smith, Gary Spivey, Michael Rogozen
> Prepared for: U.S. Environmental Protection Agency | Research Triangle Park, North Carolina 27711
> Contract No. 68-2986
> EPA Project Officers: Mr. John Acquavella, Dr. Gregg Wilkinson

- Legacy page prints:

> Health Effects in Children Exposed to Vinyl Chloride
> Final Report, January 1981 Science Applications, Inc. | 1801 Avenue of the Stars, Suite 1205 | Los Angeles, California 90067 Richard A. Ziskind, Ph.D., Principal Investigator Daniel Smith, Gary Spivey, Michael Rogozen Prepared for: U.S. Environmental Protection Agency | Research Triangle Park, North Carolina 27711 Contract No. 68-2986 EPA Project Officers: Mr. John Acquavella, Dr. Gregg Wilkinson

- Verdict: **NOT AN ARTICLE**: a government contractor's final report (EPA, 1981), not a periodical piece. If it moves at all it is a document, not an article.
- Links to carry: none.

#### 4429 | NOT AN ARTICLE | Boy, 6, at Center of MacCaulley Paternity Swindle (Part I: 1923).

- Section: photographs. Census: article-unsure. Legacy URL: `/scvhistory/lw2718.htm`
- Printed header in the record (`[lines]`):

> Boy, 6, at Center of MacCaulley Paternity Swindle (Part I: 1923)
> News Reports | March-June 1923
> [Paternity Swindle Part I (1923)] [Paternity Swindle Part II (1938-39)]

- Legacy page prints:

> Boy, 6, at Center of MacCaulley Paternity Swindle (Part I: 1923)
> News Reports | March-June 1923

- Verdict: **NOT AN ARTICLE**: the record is an original International Newsreel press photograph (Lucy Webb Furigo and son, 1923). The page below it transcribes about a dozen separate 1923 news reports from different papers. The photograph stays; each news report could become its own article later, but this record is not one article.
- Links to carry: derivedImageLinks from 11 records (3249, 3251, 4659, 4997, 5169, 5171, 5409, 5411, 5541, 5551, 5657); its own relations: featuredImage -> Asset:11102:Tlp hornelltribune052623 large; recordImages -> Asset:11103:Tlp ogdenstandard052723 large, Asset:11104:Tlp wilmingtonnewsjournal060123 large, Asset:14701:Miss Elizabeth MacCaulley, Who Brought Serious Charges Against William S, Asset:11105:Tlp kanerepublican060623 large.

#### 4437 | NOT AN ARTICLE | Beale's Cut with New Pipelines; Story by Jose Jesus Lopez (Photo 1933).

- Section: photographs. Census: article-unsure. Legacy URL: `/scvhistory/lw2725.htm`
- Printed header in the record (`[lines]`):

> "The Beale Cut."
> As told by José Jesús López.
> From "Saga of Rancho El Tejon" by Frank F. Latta | Santa Cruz: Bear State Books 1976 | pp. 195-197.

- Legacy page prints:

> "The Beale Cut."
> As told by José Jesús López.
> From "Saga of Rancho El Tejon" by Frank F. Latta | Santa Cruz: Bear State Books 1976 | pp. 195-197.

- Verdict: **NOT AN ARTICLE**: a book excerpt (Latta, "Saga of Rancho El Tejon", 1976, pp. 195-197) around a 1933 photograph by Latta. A book passage is not an article; the photograph stays a photograph. If the text needs its own record it is a document (book excerpt).
- Links to carry: derivedImageLinks from 1 records (5159).

#### 4471 | NOT AN ARTICLE | Ramona/Narrow Trail by N.C. Wyeth (1939) Sells for $665,000 (2014).

- Section: photographs. Census: picture-or-object. Legacy URL: `/scvhistory/lw2761.htm`
- Printed header in the record (`[lines]`):

> Ramona and Alessandro on The Narrow Trail
> By N.C. Wyeth

- Legacy page prints:

> Ramona and Alessandro on The Narrow Trail
> By N.C. Wyeth

- Verdict: **NOT AN ARTICLE**: a painting (N.C. Wyeth); "By N.C. Wyeth" names the artist, not an author.
- Links to carry: derivedImageLinks from 3 records (3649, 3701, 4743).

#### 4579 | NOT AN ARTICLE | Duane Harte Leads Cub Scout Pack 490 on Tour of Mentryville, 2004.

- Section: photographs. Census: picture-or-object. Legacy URL: `/scvhistory/lw2843.htm`
- Printed header in the record (`[lines]`):

> Cub Scout Pack 490 Tours Mentryville
> With Duane Harte | 2004

- Legacy page prints:

> Cub Scout Pack 490 Tours Mentryville
> With Duane Harte | 2004

- Verdict: **NOT AN ARTICLE**: snapshots of a 2004 Cub Scout tour; "With Duane Harte | 2004" is a caption line, not a byline.
- Links to carry: its own relations: featuredImage -> Asset:11340:Lw2843d large.

#### 4785 | NOT AN ARTICLE | Tepee Rock Shop (Soledad Cyn.): Rockhounding Guide for L.A. Gem Hunters, 1966.

- Section: photographs. Census: brochure-menu-catalog. Legacy URL: `/scvhistory/lw2993.htm`
- Printed header in the record (`[lines]`):

> Favorite Field Trips for Los Angeles Gem Hunters.
> 20 Detailed Maps: Desert, Mountains, Seashore.
> By Tepee Rock Shop.
> 9750 Soledad Canyon Road, Saugus, Calif. | 1966

- Legacy page prints:

> Favorite Field Trips for Los Angeles Gem Hunters.
> 20 Detailed Maps: Desert, Mountains, Seashore.
> By Tepee Rock Shop.
> 9750 Soledad Canyon Road, Saugus, Calif. | 1966

- Verdict: **NOT AN ARTICLE**: a rock shop's field-trip guide booklet (1966); "By Tepee Rock Shop" names the publisher.
- Links to carry: derivedImageLinks from 4 records (4291, 4295, 4297, 5363).

#### 4877 | NOT AN ARTICLE | The Case for Cityhood: Formation Committee Addresses LAFCO, 2-25-1987.

- Section: photographs. Census: official-or-business-document. Legacy URL: `/scvhistory/lw3071.htm`
- Printed header in the record (`[lines]`):

> The Case for Santa Clarita Cityhood.
> A Presentation to the Local Agency Formation Commission
> By the City of Santa Clarita Formation Committee.
> February 25, 1987.

- Legacy page prints:

> The Case for Santa Clarita Cityhood.
> A Presentation to the Local Agency Formation Commission By the City of Santa Clarita Formation Committee.
> February 25, 1987.

- Verdict: **NOT AN ARTICLE**: a formal presentation to LAFCO by the City Formation Committee; an official paper, not a published article. If it moves, it is a document.
- Links to carry: its own relations: photoEvents -> events:31359:Santa Clarita Cityhood.

#### 5347 | NOT AN ARTICLE | Story of Edwin Carewe's Version of ''Ramona'' in Italian | Cine-Romanzo, 8-4-1929.

- Section: photographs. Census: article-unsure. Legacy URL: `/scvhistory/lw3505.htm`
- Printed header in the record (`[lines]`):

> Ramona.
> Romanzo Completo di L.C. Machley.
> Milan, Italy: Cine-Romanzo | August 4, 1929.

- Legacy page prints:

> Ramona.
> Romanzo Completo di L.C. Machley.
> Milan, Italy: Cine-Romanzo | August 4, 1929.

- Verdict: **NOT AN ARTICLE**: a complete novelization filling a whole issue ("Romanzo Completo"): a book in magazine form, not an article. Stays as is or becomes a document; Nathan's call.
- Links to carry: derivedImageLinks from 4 records (4703, 4735, 5163, 5559).

#### 5427 | NOT AN ARTICLE | Movie Herald: ''Die Flamme von Arabien'' (''Flame of Araby''), West Germany, 1952.

- Section: photographs. Census: program. Legacy URL: `/scvhistory/lw3578.htm`
- Printed header in the record (`[lines]`):

> Filmprogrammheft: "Die Flamme von Arabien."
> (Movie Herald: "Flame of Araby.")
> Verlag Film-Bühne GmbH | n.d. (1952).

- Legacy page prints:

> Filmprogrammheft: "Die Flamme von Arabien."
> (Movie Herald: "Flame of Araby.")
> Verlag Film-Bühne GmbH | n.d. (1952).

- Verdict: **NOT AN ARTICLE**: a movie herald (program); the census rule missed "Filmprogrammheft" and "Herald" as ephemera words.
- Links to carry: derivedImageLinks from 11 records (2741, 4841, 4855, 5083, 5267, 5441, 5443, 5445, 5447, 5481, 5539).

#### The nine Land of Sunshine pictures (special case A)

All nine share this `[lines]` header and legacy header, verbatim:

> Piru Rancho: The Next Great Oil Field.
> Original Headline: "Miles of Untold Wealth."
> By F.A. Pattee.
> The Land of Sunshine, Vol. XII No. 5, November 1900, pp. 389-401. Charles F. Lummis, editor.

Verdict for each: **NOT AN ARTICLE**: a picture that illustrated the article. Census: picture-or-object. Links to carry: each is joined to the other eight and to 5209 by derivedImageLinks; neighborhood Piru.

| id | title | legacy URL |
| --- | --- | --- |
| 3051 | Piru Mansion, 1900. | `/scvhistory/lw2258a.htm` |
| 3053 | Anticlinal Geological Formation, 1900. | `/scvhistory/lw2258b.htm` |
| 3055 | Oil-Impregnated Sandstone, 1900. | `/scvhistory/lw2258c.htm` |
| 3057 | Oil Works, 1900. | `/scvhistory/lw2258d.htm` |
| 3059 | Oil Works, 1900. | `/scvhistory/lw2258e.htm` |
| 3061 | Grain Fields, 1900. | `/scvhistory/lw2258f.htm` |
| 3063 | Citrus Orchards, 1900. | `/scvhistory/lw2258g.htm` |
| 3065 | Orchards, 1900. | `/scvhistory/lw2258h.htm` |
| 3067 | Citrus Orchards, 1900. | `/scvhistory/lw2258i.htm` |

## Documents (25)

### Article (24)

#### 20087 | ARTICLE | A Missing Man

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/sw_petermentre.htm`
- sourceLine: "Los Angeles Herald | Thursday, November 11, 1886."
- originallyPublishedTitle: "A Missing Man."
- Legacy page prints:

> The Mysterious Disappearance and Death of Alec Mentry's Father.
> News reports courtesy of Stan Walker
> SCVHistory.com | March 1, 2014
> ...
> A Missing Man.
> Los Angeles Herald | Thursday, November 11, 1886.

- Verdict: **ARTICLE**: newspaper item transcribed with its scan.
- Would become: article. Proposed title: "A Missing Man."
- Byline: none. Publication: Los Angeles Herald. Date as printed: Thursday, November 11, 1886.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Herald (390).
- Headline check: Scan OCR confirms the printed head "A Missing Man."
- Links to carry: its own relations: featuredImage -> Asset:20086:Sw herald111186; publishedBy -> organizations:390:Los Angeles Herald.

#### 20090 | ARTICLE | Skeleton in the Mountains

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/sw_petermentre.htm`
- sourceLine: "Los Angeles Herald | Friday, March 17, 1899."
- originallyPublishedTitle: "Skeleton in the Mountains Remains of a Man Dead Twelve Years Are Found"
- Legacy page prints:

> The Mysterious Disappearance and Death of Alec Mentry's Father.
> News reports courtesy of Stan Walker
> SCVHistory.com | March 1, 2014
> ...
> Skeleton in the Mountains
> Remains of a Man Dead Twelve Years Are Found
> Los Angeles Herald | Friday, March 17, 1899.

- Verdict: **ARTICLE**: newspaper item transcribed with its scan.
- Would become: article. Proposed title: "Skeleton in the Mountains"
- Byline: none. Publication: Los Angeles Herald. Date as printed: Friday, March 17, 1899.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Herald (390).
- Headline check: Scan OCR confirms "SKELETON IN THE MOUNTAINS", deck "Remains of a Man Dead Twelve Years Are Found".
- Links to carry: its own relations: featuredImage -> Asset:20089:Sw herald031799; publishedBy -> organizations:390:Los Angeles Herald.

#### 20093 | ARTICLE | Mentre's Bones Found

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/sw_petermentre.htm`
- sourceLine: "Los Angeles Times | March 17, 1899."
- originallyPublishedTitle: "Mentre's Bones Found. Mysterious Disappearance of Twelve Years Ago Explained."
- Legacy page prints:

> The Mysterious Disappearance and Death of Alec Mentry's Father.
> News reports courtesy of Stan Walker
> SCVHistory.com | March 1, 2014
> ...
> Mentre's Bones Found.
> Mysterious Disappearance of Twelve Years Ago Explained.
> Los Angeles Times | March 17, 1899.

- Verdict: **ARTICLE**: newspaper item transcribed with its scan.
- Would become: article. Proposed title: "Mentre's Bones Found."
- Byline: none. Publication: Los Angeles Times. Date as printed: March 17, 1899.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Headline check: Scan OCR confirms "MENTRE'S BONES FOUND.", deck "Mysterious Disappearance of Twelve Years Ago Explained."
- Links to carry: its own relations: featuredImage -> Asset:20092:Sw lat031799.

#### 20096 | ARTICLE | First California Well

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/sw_petermentre.htm`
- sourceLine: "Warren (Penn.) Times Mirror | February 4, 1931."
- originallyPublishedTitle: "First California Well."
- Legacy page prints:

> The Mysterious Disappearance and Death of Alec Mentry's Father.
> News reports courtesy of Stan Walker
> SCVHistory.com | March 1, 2014
> ...
> First California Well.
> Warren (Penn.) Times Mirror | February 4, 1931.

- Verdict: **ARTICLE**: newspaper item transcribed with its scan.
- Would become: article. Proposed title: "First California Well."
- Byline: none. Publication: Warren (Penn.) Times Mirror. Date as printed: February 4, 1931.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: Scan OCR confirms "FIRST CALIFORNIA WELL".
- Links to carry: its own relations: featuredImage -> Asset:20095:Lp warrenpatimesmirror020431; subjectPerson -> persons:18648:Charles Alexander Mentry.

#### 20107 | ARTICLE | Death of Arthur Charles Mentry

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/lp_lat031754.htm`
- sourceLine: "Los Angeles Times | March 17, 1954."
- Legacy page prints:

> Death of Arthur Charles Mentry, Son of Oilman Alex Mentry.
> Los Angeles Times | March 17, 1954.

- Verdict: **ARTICLE**: newspaper item transcribed with its scan.
- Would become: article. Proposed title: "Son of Pioneer Oilman Dies"
- Byline: none. Publication: Los Angeles Times. Date as printed: March 17, 1954.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Headline check: The scan (lp_lat031754.jpg) prints "Son of Pioneer Oilman Dies". Neither the current title nor the legacy header ("Death of Arthur Charles Mentry, Son of Oilman Alex Mentry.") is the printed head.
- Links to carry: its own relations: featuredImage -> Asset:20106:Lp lat031754.

#### 28291 | ARTICLE | Rene Veluzat, Connie Worden Named 1975 SCV Man, Woman of the Year (Van Nuys Valley News)

- Section: documents. Census: article-likely. Legacy URL: `/scvhistory/vannuysvalleynews052775.htm`
- sourceLine: "Van Nuys Valley News | May 27, 1975."
- originallyPublishedTitle: "Peter Pitchess to Speak at Newhall CC Luncheon"
- Legacy page prints:

> Rene Veluzat, Connie Worden Named 1975 SCV Man, Woman of the Year.
> News reports, April-May 1975.
> ...
> Peter Pitchess to Speak at Newhall CC Luncheon.
> Van Nuys Valley News | May 27, 1975.

- Verdict: **ARTICLE**: newspaper item (photo caption plus a news brief) transcribed with its scans.
- Would become: article. Proposed title: "Peter Pitchess to Speak at Newhall CC Luncheon."
- Byline: none. Publication: Van Nuys Valley News. Date as printed: May 27, 1975.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: Scan OCR confirms the brief's head. The current title is Leon's page headline, and the honour named in it is in the photo caption, not the brief. Nathan may prefer two records (captioned photo, brief); proposed keeps one.
- Links to carry: its own relations: subjectPerson -> persons:16418:Connie Worden.

#### 28293 | ARTICLE | Cityhood forum announced, The Signal, January 11, 1987

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/gt8702.htm`
- sourceLine: "The Newhall Signal and Saugus Enterprise | Sunday, January 11, 1987."
- Legacy page prints:

> Santa Clarita Cityhood? First Public Forum
> Canyon Country, California
> [Brief.]
> The Newhall Signal and Saugus Enterprise | Sunday, January 11, 1987.
> ...
> [Brief.]
> The Newhall Signal and Saugus Enterprise | Sunday, January 11, 1987.

- Verdict: **ARTICLE**: newspaper brief.
- Would become: article. Proposed title: (no printed headline; see note)
- Byline: none. Publication: The Newhall Signal and Saugus Enterprise. Date as printed: Sunday, January 11, 1987.
- writtenBy candidate: none. publishedBy candidate: The Santa Clarita Valley Signal (376); name differs from the printed masthead.
- Headline check: No printed headline: the legacy page marks it "[Brief.]" under Leon's head "Santa Clarita Cityhood? First Public Forum". Proposed title: keep the current descriptive title; Nathan to confirm.
- Links to carry: pointed at by sourceDocuments <- events:31359:Santa Clarita Cityhood; its own relations: publishedBy -> organizations:376:The Santa Clarita Valley Signal; subjectPerson -> persons:16418:Connie Worden.

#### 28295 | ARTICLE | City Backers Join Prison Furor (Karina Lutz, The Signal, November 1, 1985)

- Section: documents. Census: article-sure. Legacy URL: `(none; sourcePath https://scvhistory.com/scvhistory/sg110185.htm)`
- sourceLine: "The Signal | Friday, November 1, 1985."
- originallyPublishedTitle: "City Backers Join Prison Furor"
- Legacy page prints:

> L.A. Mayor's Saugus State Prison Plan Fans Flames of Cityhood.
> The Signal, November 1, 1985. The Los Angeles Times, November 5, 1985.
> ...
> City Backers Join Prison Furor.
> By Karina Lutz.
> The Signal | Friday, November 1, 1985.

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "City Backers Join Prison Furor."
- Byline: By Karina Lutz. Publication: The Signal. Date as printed: Friday, November 1, 1985.
- writtenBy candidate: none. publishedBy candidate: The Santa Clarita Valley Signal (376); name differs from the printed masthead.
- Headline check: One of eight Signal pieces on the sg110185 page (Scott Newhall editorial, Lauren Kay, Joseph Kehoe, Laurel Suomisto, Ruth Newhall, Simon-Jacques Ifergan, and an LA Times follow-up); only this one is a record. The sg110185 front-page script in the working tree touches the same page; coordinate before any move.
- Links to carry: pointed at by sourceDocuments <- events:31359:Santa Clarita Cityhood; its own relations: publishedBy -> organizations:376:The Santa Clarita Valley Signal; subjectPerson -> persons:16418:Connie Worden; partOfCollection -> collections:32711:L.A. Mayor's Saugus State Prison Plan Fans Flames of Cityhood, 1985.

#### 28305 | ARTICLE | Connie Worden Roberts, City Co-Founder (Perry Smith, KHTS, August 12, 2014)

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/khts081214.htm`
- sourceLine: "By Perry Smith, AM-1220 KHTS | Tuesday, August 12, 2014"
- Legacy page prints:

> Connie Worden Roberts
> Co-Founder, City of Santa Clarita November 19, 1930 — August 12, 2014
> By Perry Smith, AM-1220 KHTS | Tuesday, August 12, 2014

- Verdict: **ARTICLE**: online news story (radio station website) with byline.
- Would become: article. Proposed title: "Connie Worden Roberts"
- Byline: By Perry Smith. Publication: AM-1220 KHTS. Date as printed: Tuesday, August 12, 2014.
- writtenBy candidate: none. publishedBy candidate: none.
- Headline check: The legacy header prints "Connie Worden Roberts" with the line "Co-Founder, City of Santa Clarita November 19, 1930 - August 12, 2014" beneath; whether KHTS used a longer headline is not on the mirror.
- Links to carry: pointed at by obitCompanions <- obituaries:28045:Connie Worden-Roberts, Cityhood Pioneer and Road Warrior, 1930-2014; obitCompanions <- obituaries:28047:Connie Worden Roberts: We've Lost Our Road Warrior; sourceDocuments <- events:31359:Santa Clarita Cityhood; its own relations: subjectPerson -> persons:16418:Connie Worden.

#### 28310 | ARTICLE | Cityhood Backers: Who Are They? (Laurel Suomisto, The Signal, January 4, 1987)

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/gt8702.htm`
- sourceLine: "The Newhall Signal and Saugus Enterprise | Sunday, January 4, 1987."
- originallyPublishedTitle: "Cityhood Backers — Who Are They?"
- Legacy page prints:

> Santa Clarita Cityhood? First Public Forum
> Canyon Country, California
> [Brief.]
> The Newhall Signal and Saugus Enterprise | Sunday, January 11, 1987.
> ...
> Cityhood Backers — Who Are They?
> By Laurel Suomisto
> The Newhall Signal and Saugus Enterprise | Sunday, January 4, 1987.

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Cityhood Backers — Who Are They?"
- Byline: By Laurel Suomisto. Publication: The Newhall Signal and Saugus Enterprise. Date as printed: Sunday, January 4, 1987.
- writtenBy candidate: none. publishedBy candidate: The Santa Clarita Valley Signal (376); name differs from the printed masthead.
- Headline check: The printed head carries an em dash; it is quoted as printed (originallyPublishedTitle already holds it).
- Links to carry: pointed at by sourceDocuments <- events:31359:Santa Clarita Cityhood; its own relations: subjectPerson -> persons:16418:Connie Worden.

#### 31308 | ARTICLE | 2 Students Killed, 4 Wounded in Saugus High School Shooting (Jim Holt, The Signal, November 14, 2019)

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/sg20191114shs.htm`
- sourceLine: "By Jim Holt. The Signal | Thursday, November 14, 2019."
- originallyPublishedTitle: "2 Students Killed, 4 Wounded in Saugus High School Shooting."
- Legacy page prints:

> 2 Students Killed, 4 Wounded in Saugus High School Shooting.
> By Jim Holt.
> The Signal | Thursday, November 14, 2019.

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "2 Students Killed, 4 Wounded in Saugus High School Shooting."
- Byline: By Jim Holt. Publication: The Signal. Date as printed: Thursday, November 14, 2019.
- writtenBy candidate: none. publishedBy candidate: The Santa Clarita Valley Signal (376); name differs from the printed masthead.
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:376:The Santa Clarita Valley Signal.

#### 31310 | ARTICLE | Campus Shooting Kills Two (Marisa Gerber and others, Los Angeles Times, November 15, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191115shs.htm`
- sourceLine: "By Marisa Gerber, James Queally, Hannah Fry and Sarah Parvini Los Angeles Times | Friday, November 15, 2019"
- originallyPublishedTitle: "Campus Shooting Kills Two."
- Legacy page prints:

> Campus Shooting Kills Two.
> Three others wounded at Saugus High; teen suspect in "grave condition."
> By Marisa Gerber, James Queally, Hannah Fry and Sarah Parvini
> Los Angeles Times | Friday, November 15, 2019

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Campus Shooting Kills Two."
- Byline: By Marisa Gerber, James Queally, Hannah Fry and Sarah Parvini. Publication: Los Angeles Times. Date as printed: Friday, November 15, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31312 | ARTICLE | Press Conference at SCV Sheriff Station (SCVTV, November 15, 2019)

- Section: documents. Census: article-likely. Legacy URL: `/scvhistory/scvtv20191115shs.htm`
- sourceLine: "SCVTV | Friday, November 15, 2019."
- originallyPublishedTitle: "Press Conference at SCV Sheriff Station."
- Legacy page prints:

> Press Conference at SCV Sheriff Station.
> Plus: Saugus Grads Set Up Fund.
> SCVTV | Friday, November 15, 2019.

- Verdict: **ARTICLE**: a short TV news report (bulleted points from a press conference), no byline. A news item, so an article; the census marked it likely.
- Would become: article. Proposed title: "Press Conference at SCV Sheriff Station."
- Byline: none. Publication: SCVTV. Date as printed: Friday, November 15, 2019.
- writtenBy candidate: none. publishedBy candidate: SCVTV (30520).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30520:SCVTV.

#### 31314 | ARTICLE | Saugus Grads Set Up Fund to Aid Recovery, Healing (Stephen K. Peeples, SCVNews.com, November 15, 2019)

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/scvtv20191115shs.htm`
- sourceLine: "By Stephen K. Peeples. SCVTV/SCVNews.com | Friday, November 15, 2019."
- originallyPublishedTitle: "Saugus Grads Set Up Fund to Aid Recovery, Healing."
- Legacy page prints:

> Press Conference at SCV Sheriff Station.
> Plus: Saugus Grads Set Up Fund.
> SCVTV | Friday, November 15, 2019.
> ...
> Saugus Grads Set Up Fund to Aid Recovery, Healing.
> By Stephen K. Peeples.
> SCVTV/SCVNews.com | Friday, November 15, 2019.

- Verdict: **ARTICLE**: online news article with byline.
- Would become: article. Proposed title: "Saugus Grads Set Up Fund to Aid Recovery, Healing."
- Byline: By Stephen K. Peeples. Publication: SCVTV/SCVNews.com. Date as printed: Friday, November 15, 2019.
- writtenBy candidate: none. publishedBy candidate: SCVTV (30520) (SCVNews.com is SCVTV's site; 31314 already uses it).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30520:SCVTV.

#### 31316 | ARTICLE | "This world lost a shining light" (Colleen Shalby and others, Los Angeles Times, November 16, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191116shs.htm`
- sourceLine: "By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla Los Angeles Times | Saturday, November 16, 2019"
- originallyPublishedTitle: ""This world lost a shining light.""
- Legacy page prints:

> "This world lost a shining light."
> Teenage victims of shooting at Saugus High School are remembered.
> By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla
> Los Angeles Times | Saturday, November 16, 2019

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: ""This world lost a shining light.""
- Byline: By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla. Publication: Los Angeles Times. Date as printed: Saturday, November 16, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31318 | ARTICLE | Shooting Victims Identified (Alejandra Reyes-Velarde and Colleen Shalby, Los Angeles Times, November 15, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191116shs.htm`
- sourceLine: "By Alejandra Reyes-Velarde and Colleen Shalby Los Angeles Times | Friday, November 15, 2019"
- originallyPublishedTitle: "Shooting Victims Identified."
- Legacy page prints:

> "This world lost a shining light."
> Teenage victims of shooting at Saugus High School are remembered.
> By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla
> Los Angeles Times | Saturday, November 16, 2019
> ...
> Shooting Victims Identified.
> Girl killed at Saugus High turned 15 a month ago; boy who died was 14.
> By Alejandra Reyes-Velarde and Colleen Shalby
> Los Angeles Times | Friday, November 15, 2019

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Shooting Victims Identified."
- Byline: By Alejandra Reyes-Velarde and Colleen Shalby. Publication: Los Angeles Times. Date as printed: Friday, November 15, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31320 | ARTICLE | Unregistered firearms seized from teenage shooter's home (Hannah Fry and others, Los Angeles Times, November 16, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191116shs.htm`
- sourceLine: "By Hannah Fry, Leila Miller, Richard Winton and Brittny Mejia Los Angeles Times | Saturday, November 16, 2019"
- originallyPublishedTitle: "Unregistered firearms seized from teenage shooter's home."
- Legacy page prints:

> "This world lost a shining light."
> Teenage victims of shooting at Saugus High School are remembered.
> By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla
> Los Angeles Times | Saturday, November 16, 2019
> ...
> Unregistered firearms seized from teenage shooter's home.
> The 16-year-old boy who opened fire at Saugus High School dies of his self-inflicted gunshot.
> By Hannah Fry, Leila Miller, Richard Winton and Brittny Mejia
> Los Angeles Times | Saturday, November 16, 2019

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Unregistered firearms seized from teenage shooter's home."
- Byline: By Hannah Fry, Leila Miller, Richard Winton and Brittny Mejia. Publication: Los Angeles Times. Date as printed: Saturday, November 16, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31322 | ARTICLE | School shooting stirs a search for answers (Brittny Mejia and others, Los Angeles Times, November 16, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191116shs.htm`
- sourceLine: "By Brittny Mejia, Ruben Vives, Richard Winton and Alejandra Reyes-Velarde Los Angeles Times | Saturday, November 16, 2019"
- originallyPublishedTitle: "School shooting stirs a search for answers."
- Legacy page prints:

> "This world lost a shining light."
> Teenage victims of shooting at Saugus High School are remembered.
> By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla
> Los Angeles Times | Saturday, November 16, 2019
> ...
> School shooting stirs a search for answers.
> Detectives have yet to find a motive; those who knew the teen are left stunned by his attack and suicide.
> By Brittny Mejia, Ruben Vives, Richard Winton and Alejandra Reyes-Velarde
> Los Angeles Times | Saturday, November 16, 2019

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "School shooting stirs a search for answers."
- Byline: By Brittny Mejia, Ruben Vives, Richard Winton and Alejandra Reyes-Velarde. Publication: Los Angeles Times. Date as printed: Saturday, November 16, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31324 | ARTICLE | Peace of mind on list of casualties at school (Sandy Banks, Los Angeles Times, November 16, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191116shs.htm`
- sourceLine: "Commentary by Sandy Banks. Los Angeles Times | Saturday, November 16, 2019"
- originallyPublishedTitle: "Peace of mind on list of casualties at school."
- Legacy page prints:

> "This world lost a shining light."
> Teenage victims of shooting at Saugus High School are remembered.
> By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla
> Los Angeles Times | Saturday, November 16, 2019
> ...
> Peace of mind on list of casualties at school.
> Commentary by Sandy Banks.
> Los Angeles Times | Saturday, November 16, 2019

- Verdict: **ARTICLE**: newspaper column (commentary) with byline.
- Would become: article. Proposed title: "Peace of mind on list of casualties at school."
- Byline: Commentary by Sandy Banks. Publication: Los Angeles Times. Date as printed: Saturday, November 16, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31326 | ARTICLE | Community Comes Together for Vigil (Emily Alvarenga, The Signal, November 17, 2019)

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/sg20191117shs.htm`
- sourceLine: "By Emily Alvarenga. The Signal | Sunday Evening, November 17, 2019."
- originallyPublishedTitle: "Community Comes Together for Vigil."
- Legacy page prints:

> #SaugusStrong Vigil.
> Santa Clarita Central Park.
> Sunday Evening, November 17, 2019.
> ...
> Community Comes Together for Vigil.
> By Emily Alvarenga.
> The Signal | Sunday Evening, November 17, 2019.

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Community Comes Together for Vigil."
- Byline: By Emily Alvarenga. Publication: The Signal. Date as printed: Sunday Evening, November 17, 2019.
- writtenBy candidate: none. publishedBy candidate: The Santa Clarita Valley Signal (376); name differs from the printed masthead.
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:376:The Santa Clarita Valley Signal.

#### 31328 | ARTICLE | Thousands Mourn Pair of Victims (Sandy Banks and Laura Newberry, Los Angeles Times, November 18, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191118shs.htm`
- sourceLine: "By Sandy Banks and Laura Newberry Los Angeles Times | Monday, November 18, 2019"
- originallyPublishedTitle: "Thousands Mourn Pair of Victims"
- Legacy page prints:

> Thousands Mourn Pair of Victims
> By Sandy Banks and Laura Newberry
> Los Angeles Times | Monday, November 18, 2019

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Thousands Mourn Pair of Victims"
- Byline: By Sandy Banks and Laura Newberry. Publication: Los Angeles Times. Date as printed: Monday, November 18, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31330 | ARTICLE | Facing a New Wave of Grief (Marisa Gerber, Los Angeles Times, November 18, 2019)

- Section: documents (disabled). Census: article-sure. Legacy URL: `/scvhistory/lat20191118shs.htm`
- sourceLine: "By Marisa Gerber Los Angeles Times | Monday, November 18, 2019"
- originallyPublishedTitle: "Facing a New Wave of Grief."
- Legacy page prints:

> Thousands Mourn Pair of Victims
> By Sandy Banks and Laura Newberry
> Los Angeles Times | Monday, November 18, 2019
> ...
> Facing a New Wave of Grief.
> Santa Clarita dealt with fire. A shooting brings more loss.
> By Marisa Gerber
> Los Angeles Times | Monday, November 18, 2019

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Facing a New Wave of Grief."
- Byline: By Marisa Gerber. Publication: Los Angeles Times. Date as printed: Monday, November 18, 2019.
- writtenBy candidate: none. publishedBy candidate: Los Angeles Times (30518).
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:30518:Los Angeles Times.

#### 31332 | ARTICLE | Last Shooting Victim Home from Hospital (Tammy Murga, The Signal, November 19, 2019)

- Section: documents. Census: article-sure. Legacy URL: `/scvhistory/sg20191119shs.htm`
- sourceLine: "By Tammy Murga. The Signal | Tuesday, November 19, 2019."
- originallyPublishedTitle: "Last Shooting Victim Home from Hospital."
- Legacy page prints:

> Last Shooting Victim Home from Hospital.
> By Tammy Murga.
> The Signal | Tuesday, November 19, 2019.

- Verdict: **ARTICLE**: newspaper article with byline.
- Would become: article. Proposed title: "Last Shooting Victim Home from Hospital."
- Byline: By Tammy Murga. Publication: The Signal. Date as printed: Tuesday, November 19, 2019.
- writtenBy candidate: none. publishedBy candidate: The Santa Clarita Valley Signal (376); name differs from the printed masthead.
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting; its own relations: publishedBy -> organizations:376:The Santa Clarita Valley Signal.

#### 31412 | ARTICLE | Newsmaker of the Week: State Sen. William J. "Pete" Knight (Leon Worden, The Signal, April 25, 2004)

- Section: documents. Census: article-likely. Legacy URL: `/scvhistory/signal/newsmaker/sg042504.htm`
- sourceLine: "Interview by Leon Worden, Signal City Editor. Sunday, April 25, 2004. (Television interview conducted April 1, 2004). ©2004 SCVTV."
- Legacy page prints:

> SCV NEWSMAKER OF THE WEEK:
> William J. "Pete" Knight
> State Senator
> Interview by Leon Worden
> Signal City Editor
> Sunday, April 25, 2004
> (Television interview conducted April 1, 2004)

- Verdict: **ARTICLE**: a published interview (Q and A) in The Signal's "Newsmaker of the Week" series. Already carries writtenBy, publishedBy and the Newsmaker collection.
- Would become: article. Proposed title: "SCV Newsmaker of the Week: William J. "Pete" Knight"
- Byline: Interview by Leon Worden, Signal City Editor. Publication: The Signal (and SCVTV). Date as printed: Sunday, April 25, 2004.
- writtenBy candidate: Leon Worden (279). publishedBy candidate: The Santa Clarita Valley Signal (376); name differs from the printed masthead; SCVTV (30520).
- Headline check: The legacy page prints "SCV NEWSMAKER OF THE WEEK: / William J. "Pete" Knight / State Senator" in plain text (no headline class). Proposed title follows that; Nathan to confirm the casing.
- Links to carry: its own relations: publishedBy -> organizations:376:The Santa Clarita Valley Signal, organizations:30520:SCVTV; writtenBy -> persons:279:Leon Worden; subjectPerson -> persons:29314:Pete Knight; partOfCollection -> collections:671:Newsmaker of the Week.

### Not an article (1)

#### 31306 | NOT AN ARTICLE | A Personal Letter from the Parents of Gracie Muehlberger (the Muehlberger family, November 17, 2019)

- Section: documents. Census: article-unsure. Legacy URL: `/scvhistory/bryanmuehlberger20191117.htm`
- sourceLine: "#SaugusStrong Vigil | Sunday, November 17, 2019."
- originallyPublishedTitle: "A Personal Letter from the Parents of Gracie Muehlberger."
- Legacy page prints:

> A Personal Letter from the Parents of Gracie Muehlberger.
> #SaugusStrong Vigil | Sunday, November 17, 2019.

- Verdict: **NOT AN ARTICLE**: a personal letter from Gracie Muehlberger's parents, shared at the vigil. "#SaugusStrong Vigil" names an occasion, not a publication. The census also counted this one a false positive. Stays a document.
- Links to carry: pointed at by sourceDocuments <- events:31338:Saugus High School Shooting.
