# Title census: record titles against the printed title on the legacy page

7 October 2026. Claude, read-only. Asked by Nathan: how many records have a title that differs from the one Leon's page prints, and what kinds of difference. Nothing in the database or in any existing file was changed.

Full per-record results (2,507 records, every field used below) are in `inventory/review/title-census-2026-10-07.json`. Scripts are in `storage/runtime/titles-census/` (`export.php`, `extract.py`, `classify.py`, `run.py`, `report.py`).

## Scope

* Every live or disabled entry (no drafts, revisions or trashed elements) whose field layout carries a legacy-page field: `legacyUrl`, `sourcePath`, or a section-specific one (`personLegacyUrl`, `placeLegacyUrl`, `obitLegacyUrl`, `groupLegacyUrl`, `orgLegacyUrl`, `eventLegacyUrl`). Field handles were read from each entry type's field layout. 2,509 entries; the 2 Fix entries are left out, leaving 2,507 (2,486 live, 21 disabled).
* The page is the mirror copy at `/Volumes/Reggie/SCVHistory/scvhistory.com` + the path. `legacyUrl` and `sourcePath` agree on every record that has both (two differ only as `dir/` against `dir/index.htm`). A meta-refresh stub is followed one hop (one case, `signal/worden/old/lw030597.htm`).
* Entity records (persons, places, groups, organizations, events, fallen officers) are counted in a separate table. Their title is the entity's name by design; the legacy page is evidence about the entity, not the entity's title. They are classified the same way, for completeness, but are not "wrong titles".

## The rule used to find the printed title

The printed title is the headline a reader sees at the top of the piece, not the `<title>` tag. Legacy pages carry the site and series in `<title>` (for example `SCVHistory.com LW3158 | 1971 Earthquake | Collapsed 210 Freeway Bridge in Newhall Pass, 2-9-1971.`) and print a shorter headline in the body.

1. Read only the content area: from the `XWP-BEGIN-CONTENT` marker to `XWP-END-CONTENT` when present, otherwise from `<body>`. This drops the nav bar, search box and ad banner.
2. Collect heading-like elements in document order: any element whose class ends in `headline` (`headline`, `altheadline`, `otnheadline`, `fancyheadline`); the `<!--START:SLUG-->` block; `<h1>` to `<h3>`; `<font size>` of 4 or more (or +1 and up); inline `font-size` of 20px or more. Skip breadcrumb lines (`> EARTHQUAKES`, `[NEXT]`), "Click image to enlarge", and fragments under three letters. A one-letter drop cap followed at once by the rest of the word is joined (Reynolds `N` + `OTES` reads as `NOTES`).
3. The printed title is the most prominent of the first three heading-like elements (class headline, SLUG or h1 rank highest, then font size 6, h2, font 5, h3, font 4). This picks the chapter heading over a sidebar box that comes first in source order (Reynolds part 6). If there is no heading at all, the first bold line in the first 800 characters is used (Tim Whyte columns print their headline in size-1 bold).
4. The subtitle is the next `subhed` element, or the next smaller font or heading, close after the headline. Photo pages print `headline` (the caption title) and then `subhed` (the place or series).

By page type: photo pages (`lwNNNN.htm` and similar) print `<div class="headline">` plus `<div class="subhed">`; the newer article, document and memorial pages print `class="altheadline"`; the Signal column pages (Worden 1995 to 2005, coins) print a `<font size=6>` block wrapping the SLUG; the 1996 to 1998 Worden columns print `<h1>` or `<h2>`; the Old Town Newhall Gazette prints `<font size=4>` under a smaller masthead; Reynolds and Perkins chapters print `<font size=6>` or `altheadline`.

## How titles were compared

* **identical**: the same after collapsing whitespace (a browser collapses it too).
* **case**: the same apart from letter case.
* **punctuation**: the same once curly and straight quotes, `''` and `"`, dashes, ellipses, accents, `&` and `and`, and all punctuation and spacing are set aside (case also ignored). Accent-only differences are tagged "diacritics differ" (9 records).
* **truncation or extension**: one title's words appear, in order and unbroken, inside the other (it drops a subtitle, or adds a date, a series word or a prefix). For documents, a citation the record appends by convention, `(Author, Publication, Date)`, is set aside first; a document whose title is the headline plus that citation is counted here and tagged "appended citation".
* **different**: anything else. Split further into "minor wording" (one or two words changed, added or removed somewhere in the middle) and "substantially different".
* **multi-piece page**: the page prints two or more headlines at the same level, or two or more non-entity records point at the same page, **and** the record does not match the first headline. The report then gives the best match among all the page's headings (no forced match). Where the record matches the first headline, it is classed normally.

## Counts

Records about a page (articles, photographs, documents, obituaries, war memorials, collections):

|section|identical|case|punct|trunc_ext|different|multi-piece page|no discernible printed title|page not on mirror|offsite source (not a legacy page)|no legacy page|total|
|---|---|---|---|---|---|---|---|---|---|---|---|
|articles|400|11|17|36|58|2|0|240|0|0|764|
|photographs|240|0|37|926|347|4|2|7|0|0|1563|
|documents|1|0|2|17|9|14|3|0|18|0|64|
|obituaries|2|0|1|4|0|0|0|0|0|0|7|
|warMemorials|43|0|9|0|2|0|0|0|0|0|54|
|collections|1|0|0|4|4|1|3|1|0|0|14|
|total|687|11|66|987|420|21|8|248|18|0|2466|

Entity records (title is the entity's name by design):

|section|identical|case|punct|trunc_ext|different|multi-piece page|no discernible printed title|page not on mirror|offsite source (not a legacy page)|no legacy page|total|
|---|---|---|---|---|---|---|---|---|---|---|---|
|persons|6|0|1|3|2|0|0|0|0|0|12|
|places|2|0|0|4|6|0|0|0|0|0|12|
|groups|1|0|0|1|0|0|0|0|0|0|2|
|organizations|0|0|0|0|1|0|0|0|0|0|1|
|events|0|0|0|0|0|0|0|1|0|1|2|
|fallenOfficers|0|0|1|0|10|1|0|0|0|0|12|
|total|9|0|2|8|19|1|0|1|0|1|41|

So, of the 2,200 page records that could be checked against a page on the mirror, **687 match exactly, 77 differ only in case or punctuation, 987 are a truncation or extension, 420 carry a different title, 21 sit on multi-piece pages, and 8 pages print no discernible title.** 248 pages are not on the mirror, and 18 document records cite an off-site source (santaclarita.gov, lavote.gov and others), not a legacy page.

### Inside "truncation or extension" (987)

| section | record adds words, including a date | record adds other words | record = headline + subtitle | record drops words | headline + appended citation |
|---|---|---|---|---|---|
| photographs | 666 | 234 | 19 | 7 | 0 |
| articles | 2 | 31 | 0 | 3 | 0 |
| documents | 2 | 5 | 0 | 1 | 9 |
| obituaries | 3 | 1 | 0 | 0 | 0 |
| collections | 0 | 1 | 0 | 3 | 0 |

### Inside "different" (420)

| section | minor wording (1 or 2 words) | substantially different |
|---|---|---|
| photographs | 65 | 282 |
| articles | 10 | 48 |
| documents | 0 | 9 |
| warMemorials | 2 | 0 |
| collections | 0 | 4 |

## Where the differing titles came from

**Photographs: from the `<title>` tag, not the headline.** 1,509 of the 1,554 photograph records with a page on the mirror carry exactly the last segment of the page's `<title>` (after `SCVHistory.com LWnnnn | Topic |`). That segment is Leon's longer catalogue caption, usually with a date: `Pico Cottage, Garden, Barn, 1929` where the page prints `Pico Cottage, Garden, Barn`. Of the 347 photographs counted "different", 339 equal the `<title>` segment; of the 926 counted "truncation or extension", 892 do. Only 240 photograph titles equal the printed headline. So the photograph difference is systematic and has one source: the importer took the `<title>` caption. Whether the record should carry the headline or the catalogue caption is a decision for Nathan; the caption is Leon's own text either way.

**Articles: from an extraction file whose title came from Leon's index page, not the article page.** 57 of the 58 articles counted "different" carry the title recorded in an extraction file under `inventory/legacy/` (`worden.json`, `oldtownnewhall.json`, `perkins.json`, `reynolds-full.json`). For 44 of them that title is, word for word, the link text on the series index page (`/scvhistory/signal/worden/index.htm`, `/oldtownnewhall/pauline/index.htm`, `/oldtownnewhall/rioux/index.htm`, `/oldtownnewhall/whyte/index.html`), where Leon listed the piece under a descriptive name. In 13 the same wording also appears in the page's own `<title>` (the 1997 Tim Whyte, Pauline Harte and Worden columns), so the page prints one headline and names itself another. This is the known case Nathan mentioned, and it is the pattern for the whole group: for example record 12781 "Help! Columnist under attack **by** computers!" where the page prints "under attack **from** computers!", and record 12540 "Revitalizating Newhall", a typo on the index page that the article page does not have. The one article with no traced source is 849, "Diseño Map of Rancho San Francisco, c. 1843", against the printed "Diseño ~1843".

**Old Town Newhall Gazette: a section label added and some titles cut off.** The Gazette records add "Editorial:", "Events:", "History:" or "Gate-King:" to the printed headline (counted as extensions), and three are cut off mid-sentence: 12585 "Editorial: Redevelopment Bucks Are", 12581 "Editorial: City Council Takes Step", 12579 "Editorial: In Newhall Time,". Two coin records (15448 on the mirror, 15446 not on it) read "Results of NLG Annual Writers' Competition for", with the year missing. All five came from the extraction file as they stand.

**Reynolds chapters** add the word "Chapter" (`Chapter 1: A Valley Takes Shape` against the printed `1. A Valley Takes Shape`): 21 records are titled this way, 20 counted as extensions.

**Documents** follow a house convention, `Headline (Author, Publication, Date)`; 9 match the printed headline once the citation is set aside. 14 are pieces transcribed inside a larger page (the L.A. Times page of 16 November 2019, `lat20191116shs.htm`, holds five records; the Peter Mentre page holds four); for those the best match among the page's headings is given in the JSON.

**Pages not on the mirror** (248 page records: 240 articles, namely 222 Sol Taylor coin columns under `/signal/coins/`, 16 Worden columns, 1 Reynolds and 1 Patti Rasmussen; 7 photographs; 1 collection, the coins index; plus 1 event in the entity table). These could not be checked against a printed title. 239 of the 240 articles match their extraction-file title exactly; the extraction was crawled from the live site, so that title is its `<title>` or headline, not checked here.

## Examples

### Differs only in case

| id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|
| 12870 | articles | A Sad Farewell to "Doc" Rioux | A sad farewell to "Doc" Rioux | /oldtownnewhall/rioux/rrsigtrb.htm |  |
| 12434 | articles | 20 predictions for the New Year | 20 Predictions for the New Year | /scvhistory/signal/worden/old/lw010197.htm |  |
| 2173 | articles | Notes | NOTES | /scvhistory/signal/reynolds/notes.html |  |
| 12649 | articles | Where Do We Get the Name, Newhall? | Where Do We Get The Name, Newhall? | /oldtownnewhall/gazette/gazette1101-history.htm |  |

### Differs only in punctuation, whitespace, quote style, entities or diacritics (and case)

| id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|
| 12597 | articles | Mulholland: "There It Is, Take It." | Mulholland: 'There It Is, Take It.' | /oldtownnewhall/gazette/gazette1401-pollack.htm |  |
| 12350 | articles | Cowboys put the "Western" into Country-Western | Cowboys put the 'Western' into Country-Western | /scvhistory/signal/worden/old/lw040297.htm |  |
| 15444 | articles | Stacks-ANR: Two for the Money | Stack’s-ANR: Two for the Money | /scvhistory/signal/coins/worden-coinage1206c.htm |  |
| 1446 | articles | The Pico Ghost Camp | The Pico Ghost Camp. | /scvhistory/perkins-picocamp.htm |  |
| 28132 | persons | Francisco "Chico" López | Francisco "Chico" Lopez | /scvhistory/us8502.htm | diacritics differ |

### Truncation or extension of the printed title

| id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|
| 3257 | photographs | Pico Cottage, Garden, Barn, 1929 | Pico Cottage, Garden, Barn | /scvhistory/lw2323b.htm | record has extra suffix "1929" (a date); = last segment of <title> |
| 4215 | photographs | Butterfield Overland Mail Co.'s Sinks of Tejon Station | Sinks of Tejon Station | /scvhistory/lw2600c.htm | record has extra prefix "butterfield overland mail co s"; = last segment of <title> |
| 12641 | articles | Editorial: A Long Road to Recovery. | A Long Road to Recovery. | /oldtownnewhall/gazette/gazette1101-editorial.htm | record has extra prefix "editorial"; = extraction oldtownnewhall.json title |
| 821 | articles | Chapter 1: A Valley Takes Shape | 1. A Valley Takes Shape | /scvhistory/signal/reynolds/part01.html | record has extra prefix "chapter" |
| 31332 | documents | Last Shooting Victim Home from Hospital (Tammy Murga, The Signal, November 19, 2019) | Last Shooting Victim Home from Hospital. | /scvhistory/sg20191119shs.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| 28045 | obituaries | Connie Worden-Roberts, Cityhood Pioneer and Road Warrior, 1930-2014 | Connie Worden-Roberts | /scvhistory/obituary_conniewordenroberts.htm | record has extra suffix "cityhood pioneer and road warrior 1930 2014" (a date); = last segment of <title> |

### A different title

| id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|
| 12781 | articles | Help! Columnist under attack by computers! | Help! Columnist under attack from computers! | /oldtownnewhall/pauline/ph030497.htm | minor wording (1-2 words); = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/pauline/index.htm |
| 12540 | articles | Revitalizating Newhall: An 80-Year Saga | Revitalizing Newhall: An 80-year saga | /scvhistory/signal/worden/lw062597.htm | minor wording (1-2 words); = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| 12570 | articles | 36th Assembly District Nominee George Runner | 'Front Runner' is off and running | /scvhistory/signal/worden/old/lw041096.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| 12603 | articles | Editorial: From Dream To Reality. | Old Town Newhall: From Dream To Reality. | /oldtownnewhall/gazette/gazette1302-editorial.htm | substantially different; = extraction oldtownnewhall.json title |
| 3329 | photographs | S.P. Moore Pool Hall Token, 5c, 1890s | S.P. Moore Pool Hall 5¢ Token | /scvhistory/lw2348.htm | substantially different; = last segment of <title> |
| 4787 | photographs | Driver Walt Faulkner, Bonelli Stadium, 1940s-50s. | Walt Faulkner, Midget Driver | /scvhistory/lw2995.htm | substantially different; = last segment of <title> |

### Multi-piece page

| id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|
| 31318 | documents | Shooting Victims Identified (Alejandra Reyes-Velarde and Colleen Shalby, Los Angeles Times, November 15, 2019) | "This world lost a shining light." | /scvhistory/lat20191116shs.htm | phrase found in page text; best piece match: punct "Shooting Victims Identified." |
| 20090 | documents | Skeleton in the Mountains | The Mysterious Disappearance and Death of Alec Mentry's Father. | /scvhistory/sw_petermentre.htm | phrase found in page text; best piece match: identical "Skeleton in the Mountains" |
| 31962 | articles | Paving the Way For New Schools | SCV Facilities Foundation: Three Tests Would Muzzle the Doubters | /scvhistory/signal/worden/lw062605.htm | phrase found in page text; best piece match: identical "Paving the Way For New Schools" |
| 1448 | articles | Train Station Served Newhall Till 1933 | Abandoned SPRR Newhall Depot Burns Down. | /scvhistory/sg19630117depot.htm | = extraction perkins.json title; best piece match: trunc_ext "Station served Newhall till 1933" |

### No discernible printed title

| id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|
| 28287 | documents | City Formation Committee Member Connie Worden-Roberts Remembers, 2007 | (none) | /scvhistory/files/sc19872007/files/search/search71.xml |  |
| 679 | collections | Open Book | (none) | /oldtownnewhall/patti/index.html | inside <title> |
| 685 | collections | Black 'N' Whyte | (none) | /oldtownnewhall/whyte/index.html | = <title> |

### Page not on mirror

| id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|
| 15446 | articles | Results of NLG Annual Writers' Competition for | (none) | /scvhistory/signal/coins/nlg05winners.htm |  |
| 15404 | articles | Readers Write | (none) | /scvhistory/signal/coins/soltaylor030108.html |  |
| 15400 | articles | Twenty 'Centsible' Facts | (none) | /scvhistory/signal/coins/sg1205a-coins.htm |  |

## Every non-photograph record that does not match exactly

Photographs are left out here (1,323 rows); they are in the JSON with the same fields.

| kind | id | section | record title | printed title (headline) | page | note / where the record title came from |
|---|---|---|---|---|---|---|
| case | 2173 | articles | Notes | NOTES | /scvhistory/signal/reynolds/notes.html |  |
| case | 12276 | articles | Killers: 'Natural born' or made for TV? | Killers: 'natural born' or made for TV? | /scvhistory/signal/worden/old/lw062895.htm |  |
| case | 12386 | articles | What are city planners thinking? | What Are City Planners Thinking? | /scvhistory/signal/worden/old/lw051502b.htm |  |
| case | 12434 | articles | 20 predictions for the New Year | 20 Predictions for the New Year | /scvhistory/signal/worden/old/lw010197.htm |  |
| case | 12643 | articles | A Specific Plan for Newhall. | A Specific Plan For Newhall. | /oldtownnewhall/gazette/gazette1101-specific.htm |  |
| case | 12645 | articles | Redevelopment Committee At Work for Newhall. | Redevelopment Committee At Work For Newhall. | /oldtownnewhall/gazette/gazette1101-nrc.htm |  |
| case | 12649 | articles | Where Do We Get the Name, Newhall? | Where Do We Get The Name, Newhall? | /oldtownnewhall/gazette/gazette1101-history.htm |  |
| case | 12850 | articles | Tribute to Dr. Richard Rioux | TRIBUTE TO DR. RICHARD RIOUX | /oldtownnewhall/rioux/hmtrib.htm |  |
| case | 12864 | articles | Giant Loss to the Recovery Community | Giant loss to the recovery community | /oldtownnewhall/rioux/miningco.htm |  |
| case | 12866 | articles | The Genius and Magic of "Doc" | The genius and magic of "Doc" | /oldtownnewhall/rioux/rrdarcy.htm |  |
| case | 12870 | articles | A Sad Farewell to "Doc" Rioux | A sad farewell to "Doc" Rioux | /oldtownnewhall/rioux/rrsigtrb.htm |  |
| punct | 1426 | articles | 4. Early Transportation | 4. Early Transportation. | /scvhistory/signal/perkins/part04.html |  |
| punct | 1438 | articles | History of Downtown Newhall | History of Downtown Newhall. | /scvhistory/perkins-newhall-1958.htm |  |
| punct | 1440 | articles | History of Pico Canyon Oil Production | History of Pico Canyon Oil Production. | /scvhistory/perkins-pico-1958.htm |  |
| punct | 1442 | articles | Picture Story of Hart High School (and District) | Picture Story of Hart High School (and District). | /scvhistory/harthigh_may1952.htm |  |
| punct | 1446 | articles | The Pico Ghost Camp | The Pico Ghost Camp. | /scvhistory/perkins-picocamp.htm |  |
| punct | 2075 | articles | 25. Rest Stop | 25: Rest Stop | /scvhistory/signal/reynolds/part25.html |  |
| punct | 2181 | articles | About the Namesakes of the Kingsburry House | About the Namesakes of the Kingsburry House. | /scvhistory/dispatch1501reynolds.htm |  |
| punct | 12174 | articles | What can be done with Beale's Cut | What Can be Done with Beale's Cut? | /scvhistory/signal/worden/old/lw050500b.htm |  |
| punct | 12222 | articles | Why our city's trash problem is real | Why our city’s trash problem is real | /scvhistory/signal/worden/old/lw022002b.htm |  |
| punct | 12262 | articles | Q & A from the world of scvleon.com | Q&A from the world of scvleon.com | /scvhistory/signal/worden/old/lw101597.htm |  |
| punct | 12350 | articles | Cowboys put the "Western" into Country-Western | Cowboys put the 'Western' into Country-Western | /scvhistory/signal/worden/old/lw040297.htm |  |
| punct | 12358 | articles | A politician by any other name . . . | A politician by any other name... | /scvhistory/signal/worden/old/lw102396.htm |  |
| punct | 12597 | articles | Mulholland: "There It Is, Take It." | Mulholland: 'There It Is, Take It.' | /oldtownnewhall/gazette/gazette1401-pollack.htm |  |
| punct | 12617 | articles | Life Lessons From The 'Good Old Days.' | Life Lessons From the "Good Old Days." | /oldtownnewhall/gazette/gazette1202-jacobs.htm |  |
| punct | 12798 | articles | Ebonics, math scores and the way children learn | Ebonics, math scores, and the way children learn | /oldtownnewhall/rioux/rr011297.htm |  |
| punct | 12818 | articles | Let's tour "Old Town Newhall, USA" | Let's tour Old Town Newhall, USA | /oldtownnewhall/rioux/rr112794.htm |  |
| punct | 15444 | articles | Stacks-ANR: Two for the Money | Stack’s-ANR: Two for the Money | /scvhistory/signal/coins/worden-coinage1206c.htm |  |
| punct | 26983 | documents | Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream | Abel Stearns Tells of Lopez 1842 Gold Discovery; No Mention of Dream. | /scvhistory/lp_santacruzsentinel082785.htm |  |
| punct | 28281 | documents | A Brief History of the Push for Self-Government in Santa Clarita | A Brief History of the Push for Self-Government in Santa Clarita. | /scvhistory/cw9901.htm |  |
| punct | 29784 | fallenOfficers | Deputy David W. March | Deputy David W. March. | /scvhistory/obituary_davidmarch.htm |  |
| punct | 28051 | obituaries | Ruth Newhall dies at 93 | Ruth Newhall dies at 93. | /scvhistory/dn112503.htm |  |
| punct | 28132 | persons | Francisco "Chico" López | Francisco "Chico" Lopez | /scvhistory/us8502.htm | diacritics differ |
| punct | 518 | warMemorials | Augustus A. (August) Rubel | Augustus A. (August) Rübel | /warmemorial/ww2_augustrubel.htm | diacritics differ |
| punct | 520 | warMemorials | Archibald K. 'Archie' Beall | Archibald K. "Archie" Beall | /warmemorial/ww2_archibaldbeall.htm |  |
| punct | 532 | warMemorials | Jose Ricardo Flores-Mejia | José Ricardo Flores-Mejia | /warmemorial/terror_josefloresmejia.htm | diacritics differ |
| punct | 540 | warMemorials | Dennis Lee Sellen Jr | Dennis Lee Sellen Jr. | /warmemorial/terror_dennissellen.htm |  |
| punct | 542 | warMemorials | Dean Glenn Todd Jr | Dean Glenn Todd Jr. | /warmemorial/terror_deantodd.htm |  |
| punct | 1384 | warMemorials | Edward Guy North | Edward Guy North. | /scvhistory/ww1_edwardguynorth.htm |  |
| punct | 1395 | warMemorials | James Robert "Jimmie" Ball | James Robert "Jimmie" Ball. | /scvhistory/ww2_jamesrobertball.htm |  |
| punct | 1400 | warMemorials | Joseph B. Balsz | Joseph B. Balsz. | /scvhistory/ww2_josephbbalsz.htm |  |
| punct | 1407 | warMemorials | William Ernest Pineau | William Ernest Pineau. | /scvhistory/ww2_williamernestpineau.htm |  |
| trunc_ext | 821 | articles | Chapter 1: A Valley Takes Shape | 1. A Valley Takes Shape | /scvhistory/signal/reynolds/part01.html | record has extra prefix "chapter" |
| trunc_ext | 823 | articles | Chapter 2: Where Eagles Dare | 2. Where Eagles Dare | /scvhistory/signal/reynolds/part02.html | record has extra prefix "chapter" |
| trunc_ext | 825 | articles | Chapter 3: Man Arrives | 3. Man Arrives | /scvhistory/signal/reynolds/part03.html | record has extra prefix "chapter" |
| trunc_ext | 827 | articles | Chapter 4: Children of Nature | 4. Children of Nature | /scvhistory/signal/reynolds/part04.html | record has extra prefix "chapter" |
| trunc_ext | 829 | articles | Chapter 5: Tribal Relics | 5. Tribal Relics | /scvhistory/signal/reynolds/part05.html | record has extra prefix "chapter" |
| trunc_ext | 831 | articles | Chapter 6. Winds of Change | 6. Winds of Change | /scvhistory/signal/reynolds/part06.html | record has extra prefix "chapter" |
| trunc_ext | 833 | articles | Chapter 7. Spain Reconnoiters | 7. Spain Reconnoiters | /scvhistory/signal/reynolds/part07.html | record has extra prefix "chapter" |
| trunc_ext | 835 | articles | Chapter 8. The Feast | 8. The Feast | /scvhistory/signal/reynolds/part08.html | record has extra prefix "chapter" |
| trunc_ext | 837 | articles | Chapter 9. The Trail Blazer | 9. The Trail Blazer | /scvhistory/signal/reynolds/part09.html | record has extra prefix "chapter" |
| trunc_ext | 839 | articles | Chapter 10: Solitary Hiker | 10: Solitary Hiker | /scvhistory/signal/reynolds/part10.html | record has extra prefix "chapter" |
| trunc_ext | 841 | articles | Chapter 11. Ferdinand's Grasp | 11. Ferdinand's Grasp | /scvhistory/signal/reynolds/part11.html | record has extra prefix "chapter" |
| trunc_ext | 843 | articles | Chapter 12. Staking Claim | 12. Staking Claim | /scvhistory/signal/reynolds/part12.html | record has extra prefix "chapter" |
| trunc_ext | 845 | articles | Chapter 13. Insurrection | 13. Insurrection | /scvhistory/signal/reynolds/part13.html | record has extra prefix "chapter" |
| trunc_ext | 847 | articles | Chapter 14. Lord and Master | 14. Lord and Master | /scvhistory/signal/reynolds/part14.html | record has extra prefix "chapter" |
| trunc_ext | 851 | articles | Chapter 15. Family Squabbles | 15. Family Squabbles | /scvhistory/signal/reynolds/part15.html | record has extra prefix "chapter" |
| trunc_ext | 853 | articles | Chapter 16. Golden Dreams | 16. Golden Dreams | /scvhistory/signal/reynolds/part16.html | record has extra prefix "chapter" |
| trunc_ext | 857 | articles | Chapter 18. The Pathfinder | 18. The Pathfinder | /scvhistory/signal/reynolds/part18.html | record has extra prefix "chapter" |
| trunc_ext | 859 | articles | Chapter 19. Paradise Found | 19. Paradise Found | /scvhistory/signal/reynolds/part19.html | record has extra prefix "chapter" |
| trunc_ext | 861 | articles | Chapter 20. An Eager Market | 20. An Eager Market | /scvhistory/signal/reynolds/part20.html | record has extra prefix "chapter" |
| trunc_ext | 863 | articles | Chapter 21. Buttons and Bows | 21. Buttons and Bows | /scvhistory/signal/reynolds/part21.html | record has extra prefix "chapter" |
| trunc_ext | 869 | articles | The Birth of Newhall | The Birth of Newhall (Continued). | /scvhistory/sg19470102perkins.htm | page has extra suffix "continued"; phrase found in page text |
| trunc_ext | 1434 | articles | Rancho San Francisco: A Study of a California Land Grant (1957) | Rancho San Francisco: | /scvhistory/perkins-rsf-1957.htm | record has extra suffix "a study of a california land grant 1957" (a date); = extraction perkins.json title |
| trunc_ext | 1444 | articles | Tales of Lang and Soledad | Tales of Lang and Soledad: The Story of an Adobe | /scvhistory/perkins032361.htm | page has extra suffix "the story of an adobe"; inside <title>, = extraction perkins.json title |
| trunc_ext | 12494 | articles | MOVIE REVIEW: Lights out for 'Permanent Midnight' | Lights out for 'Permanent Midnight' | /scvhistory/signal/worden/old/lw100298e.htm | record has extra prefix "movie review"; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| trunc_ext | 12552 | articles | July 4th: Too bad we can't turn back the clock | Too bad we can't turn back the clock | /scvhistory/signal/worden/old/lw070297.htm | record has extra prefix "july 4th" (a date); = <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| trunc_ext | 12589 | articles | Editorial: A Long And Rutted Road. | A Long And Rutted Road. | /oldtownnewhall/gazette/gazette1401-editorial.htm | record has extra prefix "editorial"; = extraction oldtownnewhall.json title |
| trunc_ext | 12601 | articles | Editorial: Where Is Gate-King? | Where Is Gate-King? | /oldtownnewhall/gazette/gazette1304-editorial.htm | record has extra prefix "editorial"; = extraction oldtownnewhall.json title |
| trunc_ext | 12605 | articles | Editorial: Build The Future With Eye On Past. | Build The Future With Eye On Past. | /oldtownnewhall/gazette/gazette1203-editorial.htm | record has extra prefix "editorial"; = extraction oldtownnewhall.json title |
| trunc_ext | 12609 | articles | Editorial: North Newhall, Home Inspections And Eminent Domain. | North Newhall, Home Inspections And Eminent Domain. | /oldtownnewhall/gazette/gazette1202-editorial.htm | record has extra prefix "editorial"; = extraction oldtownnewhall.json title |
| trunc_ext | 12621 | articles | Editorial: A New Plan For An Old Town. | A New Plan For An Old Town. | /oldtownnewhall/gazette/gazette1201-editorial.htm | record has extra prefix "editorial"; = extraction oldtownnewhall.json title |
| trunc_ext | 12623 | articles | History: The Finest Hotel South Of San Francisco. | The Finest Hotel South Of San Francisco. | /oldtownnewhall/gazette/gazette1201-history.htm | record has extra prefix "history"; = extraction oldtownnewhall.json title |
| trunc_ext | 12627 | articles | Events: Everything Fun Happens In Newhall. | Everything Fun Happens In Newhall. | /oldtownnewhall/gazette/gazette1201-events.htm | record has extra prefix "events"; = extraction oldtownnewhall.json title |
| trunc_ext | 12633 | articles | Gate-King: Downtown Neighbor A Part Of Revitalization Cast. | Downtown Neighbor A Part Of Revitalization Cast. | /oldtownnewhall/gazette/gazette1201-gateking.htm | record has extra prefix "gate king"; = extraction oldtownnewhall.json title |
| trunc_ext | 12641 | articles | Editorial: A Long Road to Recovery. | A Long Road to Recovery. | /oldtownnewhall/gazette/gazette1101-editorial.htm | record has extra prefix "editorial"; = extraction oldtownnewhall.json title |
| trunc_ext | 12700 | articles | Graduation: Where did all those years go? | Where did all those years go? | /oldtownnewhall/patti/pr062197.htm | record has extra prefix "graduation"; = <title>, = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/patti/index.html |
| trunc_ext | 15448 | articles | Results of NLG Annual Writers' Competition for | Results of NLG Annual Writers' Competition for 2006 | /scvhistory/signal/coins/nlg06winners.htm | page has extra suffix "2006" (a date); = extraction coins.json title |
| trunc_ext | 665 | collections | Selections from Leon Worden | Leon Worden | /scvhistory/signal/worden/index.htm | record has extra prefix "selections from"; = <title>, link text on index page /scvhistory/signal/worden/index.html |
| trunc_ext | 677 | collections | Old Town Newhall Gazette | Old Town Newhall Gazette Archive | /oldtownnewhall/oldtownnews.htm | page has extra suffix "archive"; phrase found in page text |
| trunc_ext | 681 | collections | Pauline Harte | Pauline Harte is a Santa Clarita housewife whose charm and wit is surpassed only by her fear of computers. Go ahead and send her e-mail , but understand that it may have to be forwarded to her by carrier pidgeon. | /oldtownnewhall/pauline/index.htm | page has extra suffix "is a santa clarita housewife whose charm and wit is surpassed only by her fear of computers go ahead and send her e mail but understand that it may have to be forwarded to her by carrier pidgeon" (a date); = <title>, link text on index page /oldtownnewhall/pauline/index.html |
| trunc_ext | 12135 | collections | Mentryville | THE STORY OF MENTRYVILLE: California's Pioneer Oil Town | /mentryville/mstory.htm | page has extra prefix "the story of"; page has extra suffix "california s pioneer oil town"; link text on index page /index.htm |
| trunc_ext | 20107 | documents | Death of Arthur Charles Mentry | Death of Arthur Charles Mentry, Son of Oilman Alex Mentry. | /scvhistory/lp_lat031754.htm | page has extra suffix "son of oilman alex mentry"; phrase found in page text |
| trunc_ext | 27374 | documents | Rancho San Francisco: A Study of a California Land Grant, by A.B. Perkins (1957) | Rancho San Francisco: | /scvhistory/perkins-rsf-1957.htm | record has extra suffix "a study of a california land grant by a b perkins" |
| trunc_ext | 28055 | documents | John Lang: Biography During Life (Pen Pictures, 1889) | John Lang. | /scvhistory/penpictures_johnlang.htm | record has extra suffix "biography during life"; phrase found in page text |
| trunc_ext | 28291 | documents | Rene Veluzat, Connie Worden Named 1975 SCV Man, Woman of the Year (Van Nuys Valley News) | Rene Veluzat, Connie Worden Named 1975 SCV Man, Woman of the Year. | /scvhistory/vannuysvalleynews052775.htm | record has extra suffix "van nuys valley news" |
| trunc_ext | 28299 | documents | Resolution: Founders, Friends of Hart Park, 1986 | Founders, Friends of Hart Park | /scvhistory/tl8601.htm | record has extra prefix "resolution"; record has extra suffix "1986" (a date); = last segment of <title> |
| trunc_ext | 28301 | documents | William S. Hart Union High School District governing board members, 1974-1979 (excerpt) | Governing Board Members | /scvhistory/hartschoolboardmembers.htm | record has extra prefix "william s hart union high school district"; record has extra suffix "1974 1979" (a date) |
| trunc_ext | 28305 | documents | Connie Worden Roberts, City Co-Founder (Perry Smith, KHTS, August 12, 2014) | Connie Worden Roberts | /scvhistory/khts081214.htm | record has extra suffix "city co founder"; phrase found in page text |
| trunc_ext | 31306 | documents | A Personal Letter from the Parents of Gracie Muehlberger (the Muehlberger family, November 17, 2019) | A Personal Letter from the Parents of Gracie Muehlberger. | /scvhistory/bryanmuehlberger20191117.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31308 | documents | 2 Students Killed, 4 Wounded in Saugus High School Shooting (Jim Holt, The Signal, November 14, 2019) | 2 Students Killed, 4 Wounded in Saugus High School Shooting. | /scvhistory/sg20191114shs.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31310 | documents | Campus Shooting Kills Two (Marisa Gerber and others, Los Angeles Times, November 15, 2019) | Campus Shooting Kills Two. | /scvhistory/lat20191115shs.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31312 | documents | Press Conference at SCV Sheriff Station (SCVTV, November 15, 2019) | Press Conference at SCV Sheriff Station. | /scvhistory/scvtv20191115shs.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31316 | documents | "This world lost a shining light" (Colleen Shalby and others, Los Angeles Times, November 16, 2019) | "This world lost a shining light." | /scvhistory/lat20191116shs.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31328 | documents | Thousands Mourn Pair of Victims (Sandy Banks and Laura Newberry, Los Angeles Times, November 18, 2019) | Thousands Mourn Pair of Victims | /scvhistory/lat20191118shs.htm | record = printed headline (identical) + appended citation; phrase found in page text |
| trunc_ext | 31332 | documents | Last Shooting Victim Home from Hospital (Tammy Murga, The Signal, November 19, 2019) | Last Shooting Victim Home from Hospital. | /scvhistory/sg20191119shs.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31334 | documents | Hart District Actions in Light of Saugus High School Shooting (Mike Kuhlman, William S. Hart Union High School District, January 12, 2020) | Hart District Actions in Light of Saugus High School Shooting. | /scvhistory/hd20200112.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31336 | documents | Principal Vince Ferry, Saugus High, Honored by Council on School Culture (William S. Hart Union High School District, April 6, 2020) | Principal Vince Ferry, Saugus High, Honored by Council on School Culture. | /scvhistory/hd20200406.htm | record = printed headline (punct) + appended citation; phrase found in page text |
| trunc_ext | 31723 | documents | Scott Newhall: A Newspaper Editor's Voyage (Oral History, 1988-1989) | Scott Newhall. | /scvhistory/uc8901.htm | record has extra suffix "a newspaper editor s voyage"; inside <title> |
| trunc_ext | 913 | groups | Tataviam | Tataviam Culture | /scvhistory/tataviam.htm | page has extra suffix "culture" |
| trunc_ext | 28045 | obituaries | Connie Worden-Roberts, Cityhood Pioneer and Road Warrior, 1930-2014 | Connie Worden-Roberts | /scvhistory/obituary_conniewordenroberts.htm | record has extra suffix "cityhood pioneer and road warrior 1930 2014" (a date); = last segment of <title> |
| trunc_ext | 28047 | obituaries | Connie Worden Roberts: We've Lost Our Road Warrior | We've Lost Our Road Warrior | /scvhistory/khts081314.htm | record has extra prefix "connie worden roberts"; inside <title> |
| trunc_ext | 28049 | obituaries | George A. Caravalho, Santa Clarita's First Permanent City Manager, 1938-2020 | George A. Caravalho | /scvhistory/obituary_georgeacaravalho.htm | record has extra suffix "santa clarita s first permanent city manager 1938 2020" (a date); = last segment of <title> |
| trunc_ext | 28079 | obituaries | Remi Nadeau, L.A. Freighter and Hotelier, 1821-1887 | Remi Nadeau, L.A. Freighter and Hotelier | /scvhistory/obituary_reminadeau1887.htm | record has extra suffix "1821 1887" (a date); = last segment of <title> |
| trunc_ext | 293 | persons | Ygnacio del Valle | Ygnacio del Valle, Landowner | /scvhistory/lw2052.htm | page has extra suffix "landowner"; = last segment of <title> |
| trunc_ext | 339 | persons | Rémi Nadeau | Rémi Nadeau (I): From Miller to Hotelier. | /scvhistory/henriettenadeau20171119_en.htm | page has extra suffix "i from miller to hotelier"; inside <title> |
| trunc_ext | 343 | persons | Rodolfo Acosta | Rodolfo Acosta in "Apache Warrior" | /scvhistory/lw3399.htm | page has extra suffix "in apache warrior"; inside <title> |
| trunc_ext | 609 | places | Lang Station | Lang Station & Monument Sign | /scvhistory/hs1000.htm | page has extra suffix "and monument sign"; inside <title> |
| trunc_ext | 615 | places | Melody Ranch Motion Picture Studio | Melody Ranch | /scvhistory/melody.htm | record has extra suffix "motion picture studio" |
| trunc_ext | 635 | places | Ridge Route | Film: Ridge Route Alternate, Winter 1939 | /scvhistory/lw3795.htm | page has extra prefix "film"; page has extra suffix "alternate winter 1939" (a date); inside <title>, = extraction lw-features.json title_topic, link text on index page /index.htm |
| trunc_ext | 932 | places | Beale's Cut Stagecoach Pass | Beale's Cut | /scvhistory/bealescut.htm | record has extra suffix "stagecoach pass" |
| different | 849 | articles | Diseño Map of Rancho San Francisco, c. 1843 | Diseño ~1843 | /scvhistory/jj2003b.htm | substantially different |
| different | 1436 | articles | Manuscript: Colonization (~1940s) | Story of Little Santa Clara Valley. | /scvhistory/perkins_manuscript_ch2.htm | substantially different; = extraction perkins.json title |
| different | 1452 | articles | Pardee House Has Seen Local History | Phone Business Office Has Seen Local History. | /scvhistory/sentinel042865.htm | substantially different; inside <title>, = extraction perkins.json title |
| different | 1454 | articles | Newhall Fourth of July Parade History | 42nd Annual Newhall Old West July 4th Celebration. | /scvhistory/hs_parade19680704book.htm | substantially different; = extraction perkins.json title |
| different | 2179 | articles | First Presbyterian Church | 90 Years Beneath the Cross. | /scvhistory/reynolds_firstpresbyterian_0686.htm | substantially different; inside <title>, = extraction reynolds-full.json title |
| different | 12496 | articles | 'Fixing up Newhall' in wake of drive-by | A real commitment to 'fixing up Newhall' | /scvhistory/signal/worden/old/lw081998.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12498 | articles | STAGE REVIEW: Rigby is Pan-tastic at the Pantages | Artistry in motion: Rigby is Pan-tastic at Pantages | /scvhistory/signal/worden/old/lw080798ea.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12500 | articles | Signs point to trouble in Nov., 1999 | Signs point to trouble in November 1999 | /scvhistory/signal/worden/old/lw080598.htm | minor wording (1-2 words); = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12502 | articles | STAGE REVIEW: 'Rep' makes much ado about authenticity | SC Repertory makes much ado about authenticity | /scvhistory/signal/worden/old/lw072498e.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12504 | articles | Dismantling bilingual ed. is no simple task | Dismantling bilingual education is no simple task | /scvhistory/signal/worden/old/lw072298.htm | minor wording (1-2 words); = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12506 | articles | MOVIE REVIEW: 'Small Soldiers' a small victory | 'Small Soldiers' a small victory for DreamWorks | /scvhistory/signal/worden/old/lw071098e.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12508 | articles | Plambeck complaint is 'sour grapes' | Sour grapes from crybaby election loser | /scvhistory/signal/worden/old/lw061098.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12510 | articles | MOVIE REVIEW: 'A Perfect Affair' holds few surprises | 'A Perfect Affair' predictably unpredictable | /scvhistory/signal/worden/old/lw060598e.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12512 | articles | Crime isn't particular to one community | Worry about your own darned back yard | /scvhistory/signal/worden/old/lw052798.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12514 | articles | Frontier Days 1996 | Gimme that old Frontier dirt anytime! | /scvhistory/signal/worden/old/lw062696.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12516 | articles | Architectural Guidelines for Santa Clarita? | Help design tomorrow's city, tonight | /scvhistory/signal/worden/old/lw060596.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12518 | articles | Story of the Castaic Lake Water Agency | Desert living was never cheap or easy | /scvhistory/signal/worden/old/lw052296.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12520 | articles | Creating a Theater District in Old Town Newhall | Homeless thespians overtaking Newhall? | /scvhistory/signal/worden/old/lw051596.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12522 | articles | Interview with a dissenting Menendez juror | Menendez outcome wrong, juror says | /scvhistory/signal/worden/old/lw042496.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12524 | articles | 1996 Santa Clarita City Council race | 1.1 billion reasons to vote April 9 | /scvhistory/signal/worden/old/lw040396.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12526 | articles | 1996 Santa Clarita City Council race | Which candidates care about us? | /scvhistory/signal/worden/old/lw032796.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12528 | articles | Northridge Earthquake: a day in the life | The earth groaned as its bowels moved | /scvhistory/signal/worden/old/lw011796.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12534 | articles | The Paul Allen LDS Letter | Eureka. You have found it. | /scvhistory/signal/worden/old/paulallen.htm | substantially different; = last segment of <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12536 | articles | Why the Hart High 'Indians'? | The proud, the mighty, the Indians of Hart High School | /scvhistory/signal/worden/lw092497.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12538 | articles | Big Party Marks Newhall Hardware's 50th Anniversary | Big party marks Newhall Hardware's 50th birthday | /scvhistory/signal/worden/lw081397.htm | minor wording (1-2 words); = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12540 | articles | Revitalizating Newhall: An 80-Year Saga | Revitalizing Newhall: An 80-year saga | /scvhistory/signal/worden/lw062597.htm | minor wording (1-2 words); = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12542 | articles | Historic A.B. Perkins photos resurface | Historic photos surface after decades | /scvhistory/signal/worden/lw092596.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12546 | articles | Harvest festival, Halloween haunts on tap this weekend | Lots on tap for kids this weekend | /scvhistory/signal/worden/old/lw102297.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12548 | articles | High school district considers drug testing for athletes | Parents irked over talk of drug testing | /scvhistory/signal/worden/old/lw100197.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12550 | articles | Newhall Hardware celebrates 50th anniversary | Big party marks Newhall Hardware's 50th birthday | /scvhistory/signal/worden/old/lw081397.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12554 | articles | Matt Gould: Rising star headed for Broadway | Rising local star headed for Broadway | /scvhistory/signal/worden/old/lw061197.htm | substantially different; = <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12556 | articles | Goodnight, Richard Rioux, my dear friend | Goodnight, Richard, my dear friend | /scvhistory/signal/worden/old/lw043097.htm | minor wording (1-2 words); = <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12558 | articles | Story of the Sulphur Springs School | Story of Sulphur Springs School | /scvhistory/signal/worden/lw030597.htm | minor wording (1-2 words); = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12560 | articles | Americans to the rescue: Reviving Boris Yeltsin | Inside look at a bizarre election | /scvhistory/signal/worden/old/lw022697.htm | substantially different; = <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12562 | articles | Postwar growth in the Santa Clarita Valley | Hirohito at root of SCV growth | /scvhistory/signal/worden/old/lw021997.htm | substantially different; = <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12564 | articles | A look at the City's latest poll | S'Claritans like their trash service? | /scvhistory/signal/worden/old/lw021297.htm | substantially different; = <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12566 | articles | Mint to produce 50 new quarters under House bill | Move over, Washington, the states are coming! | /scvhistory/signal/worden/old/lw112096.htm | substantially different; = <title>, = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12568 | articles | Firestone Bilingual Education Bill | New bilingual bill offers hope | /scvhistory/signal/worden/old/lw041796.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12570 | articles | 36th Assembly District Nominee George Runner | 'Front Runner' is off and running | /scvhistory/signal/worden/old/lw041096.htm | substantially different; = extraction worden.json title, link text on index page /scvhistory/signal/worden/index.htm |
| different | 12577 | articles | Community Center To Open. | Newhall Community Center Under Construction | /oldtownnewhall/gazette/gazette1201-commctr.htm | substantially different; = extraction oldtownnewhall.json title |
| different | 12579 | articles | Editorial: In Newhall Time, | In Newhall Time, Change Is Happening Fast. | /oldtownnewhall/gazette/gazette1404-editorial.htm | substantially different; = extraction oldtownnewhall.json title |
| different | 12581 | articles | Editorial: City Council Takes Step | City Council Takes Step To Preserve Historic Buildings. | /oldtownnewhall/gazette/gazette1403-editorial.htm | substantially different; = extraction oldtownnewhall.json title |
| different | 12585 | articles | Editorial: Redevelopment Bucks Are | Redevelopment Bucks Are In The Bank. Now What? | /oldtownnewhall/gazette/gazette1402-editorial.htm | substantially different; = extraction oldtownnewhall.json title |
| different | 12603 | articles | Editorial: From Dream To Reality. | Old Town Newhall: From Dream To Reality. | /oldtownnewhall/gazette/gazette1302-editorial.htm | substantially different; = extraction oldtownnewhall.json title |
| different | 12629 | articles | What Next For Veterans Memorial Plaza? | What Next For Veterans Historical Plaza? | /oldtownnewhall/gazette/gazette1201-vets.htm | minor wording (1-2 words); = extraction oldtownnewhall.json title |
| different | 12647 | articles | Shaping Newhall Inside and Out. | Shaping The Old Town Inside And Out. | /oldtownnewhall/gazette/gazette1101-smisko.htm | substantially different; = extraction oldtownnewhall.json title |
| different | 12767 | articles | Most skateboarders aren't hoodlums | Skateboard park not necessarily a great idea | /oldtownnewhall/pauline/ph102197.htm | substantially different; = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/pauline/index.htm |
| different | 12775 | articles | Fourth of July Parade did us proud | July 4th parade did us proud | /oldtownnewhall/pauline/ph071597.htm | substantially different; = <title>, = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/pauline/index.htm |
| different | 12777 | articles | Jacques Cousteau: Spokesman for those with no voice | Cousteau sailed the Calypso into the conscience of mankind | /oldtownnewhall/pauline/ph070897.htm | substantially different; = <title>, = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/pauline/index.htm |
| different | 12779 | articles | Ritalin not always best cure for misbehavior | Ritalin isn't always the best cure for misbehavior | /oldtownnewhall/pauline/ph050697.htm | substantially different; = <title>, = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/pauline/index.htm |
| different | 12781 | articles | Help! Columnist under attack by computers! | Help! Columnist under attack from computers! | /oldtownnewhall/pauline/ph030497.htm | minor wording (1-2 words); = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/pauline/index.htm |
| different | 12796 | articles | "Images" | SUNRISES, SUNSETS AND IN BETWEEN | /oldtownnewhall/rioux/images.htm | substantially different; = extraction oldtownnewhall.json title |
| different | 12852 | articles | Tributes | RICHARD "DOC" RIOUX | /oldtownnewhall/rioux/tributes.htm | substantially different; = page subtitle, = extraction oldtownnewhall.json title |
| different | 12854 | articles | Biography | RICHARD "DOC" RIOUX | /oldtownnewhall/rioux/rrbio.htm | substantially different; = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/rioux/index.htm |
| different | 12856 | articles | DENIAL impedes recovery from drug, alcohol addiction | DENIAL impedes recovery from addiction | /oldtownnewhall/rioux/rr100696.htm | minor wording (1-2 words); = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/rioux/index.htm |
| different | 12889 | articles | It's official: I'm a Star Wars geek | A long time ago, in a newsroom far, far away... | /oldtownnewhall/whyte/tw042097.htm | substantially different; = <title>, = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/whyte/index.html |
| different | 12943 | articles | Local actress: 'Norma Rae' of the porn industry? | Local actress: 'Norma Rae' of porn industry? | /oldtownnewhall/whyte/tw111697.htm | minor wording (1-2 words); = <title>, = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/whyte/index.html |
| different | 12945 | articles | Elton John: Profiting on "Candle in the Wind" release? | Curious timing of "Candle in the Wind" release | /oldtownnewhall/whyte/tw100597.htm | substantially different; = <title>, = extraction oldtownnewhall.json title, link text on index page /oldtownnewhall/whyte/index.html |
| different | 667 | collections | Santa Clarita Valley History by John Boston | Contents | /scvhistory/signal/boston/jbindex.htm | substantially different; phrase found in page text |
| different | 669 | collections | Now and Then in the Santa Clarita Valley | DARRYL MANZER | /scvhistory/signal/manzer/index.htm | substantially different; phrase found in page text |
| different | 683 | collections | Richard 'Doc' Rioux At Large | "In all that I do I will try to remember the maxim that the final value society places on our lives will be measured by the extent to which we have served others." | /oldtownnewhall/rioux/index.htm | substantially different; = <title>, link text on index page /oldtownnewhall/rioux/index.html |
| different | 871 | collections | History of the Santa Clarita Valley | CONTENTS | /scvhistory/signal/reynolds/contents.html | substantially different; inside <title>, link text on index page /scvhistory/signal/reynolds/index.html |
| different | 18991 | documents | A Brief Sketch of the Notorious Bandit | Tiburcio Vasquez | /scvhistory/je4001.htm | substantially different; phrase found in page text |
| different | 20099 | documents | C.A. Mentry, in Pen Pictures From the Garden of the World | C.A. (Charles Alexander) Mentry. | /scvhistory/penpictures_mentry.htm | substantially different |
| different | 20104 | documents | Demetrius Scofield's Eulogy to Charles Alexander Mentry | Ode to the Man Behind Mentryville | /scvhistory/scofield.htm | substantially different; = last segment of <title> |
| different | 28057 | documents | John Lang's Letter on the Grizzly Bear, Los Angeles Herald, July 28, 1875 | John Lang and the 1,600-pound Grizzly Bear of 1875. | /scvhistory/tlp_laherald072875pg3.htm | substantially different |
| different | 28295 | documents | City Backers Join Prison Furor (Karina Lutz, The Signal, November 1, 1985) | L.A. Mayor's Saugus State Prison Plan Fans Flames of Cityhood. | /scvhistory/sg110185.htm | substantially different; phrase found in page text |
| different | 28297 | documents | Friends of Hart Park: original bylaws and initial directors, 1981 | Bylaws of Friends of Hart Park. | /scvhistory/fh8101.htm | substantially different; = last segment of <title> |
| different | 28303 | documents | City of Santa Clarita Planning Commission, 1988-1990 (excerpt) | City of Santa Clarita Commissioners, 1988-Present | /scvhistory/citycommissioners.htm | substantially different |
| different | 28307 | documents | Connie Worden-Roberts Memorial Bridge dedicated, Golden Valley Road at SR-14, 2016 | Construction of Connie Worden-Roberts Memorial Bridge | /scvhistory/sc1708.htm | substantially different |
| different | 31412 | documents | Newsmaker of the Week: State Sen. William J. "Pete" Knight (Leon Worden, The Signal, April 25, 2004) | William J. "Pete" Knight State Senator | /scvhistory/signal/newsmaker/sg042504.htm | substantially different; phrase found in page text |
| different | 29762 | fallenOfficers | Deputy Constable Charles A. De Moranville | Newhall Lawman Makes Ultimate Sacrifice, 1-4-1909. | /scvhistory/lasd053113demoranville.htm | substantially different; phrase found in page text |
| different | 29764 | fallenOfficers | Deputy Constable J. Edward "Ed" Brown | Saugus Deputy Ed Brown Dies in Gun Battle, 9-14-1924. | /scvhistory/lasd053113brown.htm | substantially different |
| different | 29766 | fallenOfficers | Constable John S. "Jack" Pilcher | Newhall Constable Jack Pilcher Dies in Line of Duty, 1925. | /scvhistory/lasd060414pilcher.htm | substantially different; phrase found in page text |
| different | 29768 | fallenOfficers | Officer Walter C. Frago | The Newhall Incident | /scvhistory/chp-newhall-incident.htm | substantially different |
| different | 29770 | fallenOfficers | Officer Roger D. Gore | The Newhall Incident | /scvhistory/chp-newhall-incident.htm | substantially different |
| different | 29772 | fallenOfficers | Officer James E. Pence Jr. | The Newhall Incident | /scvhistory/chp-newhall-incident.htm | substantially different |
| different | 29774 | fallenOfficers | Officer George M. Alleyn | The Newhall Incident | /scvhistory/chp-newhall-incident.htm | substantially different |
| different | 29776 | fallenOfficers | Deputy Arthur E. Pelino | Arthur E. Pelino, Deputy Sheriff. | /scvhistory/obituary_pelinoarthure.htm | substantially different; inside <title> |
| different | 29778 | fallenOfficers | Deputy Hagop "Jake" Kuredjian | 10th Anniversary Ceremony to Remember Deputy Jake Kuredjian | /scvhistory/kuredjian082811.htm | substantially different; inside <title> |
| different | 29786 | fallenOfficers | Officer Matthew Pavelka | Killer of SCV-Resident Cop Matthew Pavelka Gets Life Without Parole. | /scvhistory/scvnews072412.htm | substantially different; phrase found in page text |
| different | 396 | organizations | Santa Clarita Valley Chamber of Commerce | Walk of Western Stars Inductees | /scvhistory/lw2102.htm | substantially different; phrase found in page text |
| different | 333 | persons | Arthur Buckingham Perkins | Arthur B. Perkins | /scvhistory/lw2232.htm | minor wording (1-2 words) |
| different | 337 | persons | John Timothy Gifford | John & Sarah Gifford Home | /scvhistory/hs2001.htm | substantially different; phrase found in page text |
| different | 597 | places | Fort Tejon | Officers' Quarters | /scvhistory/lw3698.htm | minor wording (1-2 words); inside <title>, = page subtitle, = extraction lw-features.json title_topic |
| different | 605 | places | Heritage Junction Historic Park | SPRR Saugus Depot Screenshots | /scvhistory/lw3789.htm | substantially different |
| different | 613 | places | Six Flags Magic Mountain | Repurposed Coach from Original Colossus | /scvhistory/lw3790.htm | substantially different; phrase found in page text |
| different | 631 | places | Rancho Camulos | Liquor Tax Certificate and Coupons | /scvhistory/lw3903.htm | substantially different; inside <title>, = extraction lw-features.json title_topic |
| different | 643 | places | Saugus Speedway | Actor Leo Carillo at Hoot Gibson's Home | /scvhistory/lw3271.htm | substantially different; inside <title>, = extraction lw-features.json title_topic |
| different | 659 | places | Vasquez Rocks | Kathleen Crowley in "Tales of the 77th Bengal Lancers" | /scvhistory/lw3730.htm | substantially different; inside <title>, = extraction lw-features.json title_topic, link text on index page /index.htm |
| different | 538 | warMemorials | Ian Timothy D. Gelig | Ian Timothy Dagdagan Gelig | /warmemorial/terror_iangelig.htm | minor wording (1-2 words); inside <title> |
| different | 558 | warMemorials | Albert Edward Thomas | Albert Edward "Stud" Thomas | /warmemorial/korea_albertthomas.htm | minor wording (1-2 words); inside <title> |
| multi-piece page | 1448 | articles | Train Station Served Newhall Till 1933 | Abandoned SPRR Newhall Depot Burns Down. | /scvhistory/sg19630117depot.htm | = extraction perkins.json title; best piece match: trunc_ext "Station served Newhall till 1933" |
| multi-piece page | 31962 | articles | Paving the Way For New Schools | SCV Facilities Foundation: Three Tests Would Muzzle the Doubters | /scvhistory/signal/worden/lw062605.htm | phrase found in page text; best piece match: identical "Paving the Way For New Schools" |
| multi-piece page | 671 | collections | Newsmaker of the Week | YOU ARE HERE: Home > Local News > SCV Newsmaker | /scvhistory/signal/newsmaker/index.htm | phrase found in page text; best piece match: identical "Newsmaker of the Week" |
| multi-piece page | 20087 | documents | A Missing Man | The Mysterious Disappearance and Death of Alec Mentry's Father. | /scvhistory/sw_petermentre.htm | phrase found in page text; best piece match: punct "A Missing Man." |
| multi-piece page | 20090 | documents | Skeleton in the Mountains | The Mysterious Disappearance and Death of Alec Mentry's Father. | /scvhistory/sw_petermentre.htm | phrase found in page text; best piece match: identical "Skeleton in the Mountains" |
| multi-piece page | 20093 | documents | Mentre's Bones Found | The Mysterious Disappearance and Death of Alec Mentry's Father. | /scvhistory/sw_petermentre.htm | phrase found in page text; best piece match: punct "Mentre's Bones Found." |
| multi-piece page | 20096 | documents | First California Well | The Mysterious Disappearance and Death of Alec Mentry's Father. | /scvhistory/sw_petermentre.htm | phrase found in page text; best piece match: punct "First California Well." |
| multi-piece page | 28289 | documents | SCVHistory.com Key to Photos: the CW prefix | Photo Sources | /scvhistory/key.htm | best piece match: different "Photo Sources" |
| multi-piece page | 28293 | documents | Cityhood forum announced, The Signal, January 11, 1987 | Santa Clarita Cityhood? First Public Forum | /scvhistory/gt8702.htm | best piece match: different "Santa Clarita Cityhood? First Public Forum" |
| multi-piece page | 28310 | documents | Cityhood Backers: Who Are They? (Laurel Suomisto, The Signal, January 4, 1987) | Santa Clarita Cityhood? First Public Forum | /scvhistory/gt8702.htm | phrase found in page text; best piece match: punct "Cityhood Backers — Who Are They?" |
| multi-piece page | 31314 | documents | Saugus Grads Set Up Fund to Aid Recovery, Healing (Stephen K. Peeples, SCVNews.com, November 15, 2019) | Press Conference at SCV Sheriff Station. | /scvhistory/scvtv20191115shs.htm | phrase found in page text; best piece match: punct "Saugus Grads Set Up Fund to Aid Recovery, Healing." |
| multi-piece page | 31318 | documents | Shooting Victims Identified (Alejandra Reyes-Velarde and Colleen Shalby, Los Angeles Times, November 15, 2019) | "This world lost a shining light." | /scvhistory/lat20191116shs.htm | phrase found in page text; best piece match: punct "Shooting Victims Identified." |
| multi-piece page | 31320 | documents | Unregistered firearms seized from teenage shooter's home (Hannah Fry and others, Los Angeles Times, November 16, 2019) | "This world lost a shining light." | /scvhistory/lat20191116shs.htm | phrase found in page text; best piece match: punct "Unregistered firearms seized from teenage shooter's home." |
| multi-piece page | 31322 | documents | School shooting stirs a search for answers (Brittny Mejia and others, Los Angeles Times, November 16, 2019) | "This world lost a shining light." | /scvhistory/lat20191116shs.htm | phrase found in page text; best piece match: punct "School shooting stirs a search for answers." |
| multi-piece page | 31324 | documents | Peace of mind on list of casualties at school (Sandy Banks, Los Angeles Times, November 16, 2019) | "This world lost a shining light." | /scvhistory/lat20191116shs.htm | phrase found in page text; best piece match: punct "Peace of mind on list of casualties at school." |
| multi-piece page | 31326 | documents | Community Comes Together for Vigil (Emily Alvarenga, The Signal, November 17, 2019) | #SaugusStrong Vigil. | /scvhistory/sg20191117shs.htm | phrase found in page text; best piece match: punct "Community Comes Together for Vigil." |
| multi-piece page | 31330 | documents | Facing a New Wave of Grief (Marisa Gerber, Los Angeles Times, November 18, 2019) | Thousands Mourn Pair of Victims | /scvhistory/lat20191118shs.htm | phrase found in page text; best piece match: punct "Facing a New Wave of Grief." |
| multi-piece page | 29760 | fallenOfficers | Constable McCoy Pyle | McCoy Pyle Murder: Covetous Fillmore Man Fakes Robbery to Kill Popular Constable. | /scvhistory/lp_sfchronicle052597.htm | phrase found in page text; best piece match: different "McCoy Pyle Murder: Covetous Fillmore Man Fakes Robbery to Kill Popular Constable." |
| no discernible printed title | 679 | collections | Open Book | (none) | /oldtownnewhall/patti/index.html | inside <title> |
| no discernible printed title | 685 | collections | Black 'N' Whyte | (none) | /oldtownnewhall/whyte/index.html | = <title> |
| no discernible printed title | 873 | collections | Story of Our Valley | (none) | /scvhistory/signal/perkins/contents.html | inside <title>, link text on index page /scvhistory/signal/perkins/index.html |
| no discernible printed title | 28283 | documents | Santa Clarita Valley Historical Society board minutes, May 19, 2003 (excerpt) | (none) | /scvhistory/files/scvhs2000minutes/scvhs2000minutes.pdf |  |
| no discernible printed title | 28285 | documents | "A Look Back in Time": proposal for the Historical Society's first annual fund raiser, by Connie Worden-Roberts, 2003 | (none) | /scvhistory/files/scvhs2000minutes/scvhs2000minutes.pdf |  |
| no discernible printed title | 28287 | documents | City Formation Committee Member Connie Worden-Roberts Remembers, 2007 | (none) | /scvhistory/files/sc19872007/files/search/search71.xml |  |

## Caveats

* The headline rule is mechanical. It was checked by reading the extracted headline for every non-photograph mismatch and a sample of photographs; a page with an unusual layout can still yield the wrong element. Collection records point at index pages, which print an author name or a quotation rather than a title; treat the collections row as weak.
* "Truncation or extension" counts word sequences, so a date added to a caption and a dropped subtitle land in the same kind; the subtypes table separates them.
* "Minor wording" is a word count, not a judgement: "the Sulphur Springs School" against "Sulphur Springs School" and "Revitalizating" against "Revitalizing" both count as minor.
* The mirror is the August 2026 copy of the site plus the 4 October addendum; a page Leon edited after that is not reflected.
* The multi-piece rule only fires when the record does not match the first headline, so war-memorial pages that print the person's name and then transcribed clippings count as matches, which they are.
* Nothing here says which title should win. That is Nathan's decision; the counts only show where record and page disagree and where the record title came from.
