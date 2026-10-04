# Collections built from legacy folders: does the page carry the byline?

Generated 4 October 2026 by Claude (read-only audit; nothing in the database was changed).

Nathan's test: anything in an author's collection should have that author's byline on the page itself, or come out. Attribution by file path is not attribution.

Method: Each article's legacyUrl was opened on the Reggie mirror (/Volumes/Reggie/SCVHistory/scvhistory.com); where the file is absent, the crawl's body_html in inventory/legacy/*.json was read instead (marked). A byline counts when the page itself names the author in a byline, dateline, running head or signature; the copyright footer and the author tagline were noted separately. Every page without a byline was then read by hand.

## Summary

| collection | built by | articles | byline on page | lacking a byline by the author |
|---|---|---:|---:|---:|
| Open Book (#679) | folder | 27 | 25 | 2 |
| Making Cents (#673) | folder | 259 | 219 | 40 |
| Richard 'Doc' Rioux At Large (#683) | folder | 44 | 34 | 10 |
| Pauline Harte (#681) | folder | 38 | 38 | 0 |
| Black 'N' Whyte (#685) | folder | 37 | 37 | 0 |
| Selections from Leon Worden (#665) | folder | 219 | 219 | 0 |
| Old Town Newhall Gazette (#677) | folder (no author) | 37 | 34 (various writers) | n/a |
| History of the Santa Clarita Valley (#871) | contents page + 4 picked | 80 | 80 | 0 |
| Story of Our Valley (#873) | contents page + 7 related | 20 | 20 | 0 |

The five empty collections (John Boston, Darryl Manzer, Newsmaker of the Week, Abu Ghraib, Mentryville) hold no articles and were not tested.

Three collections fail the test: Open Book (2), Making Cents (40) and Doc Rioux At Large (10). The other author collections pass page by page.

## 1. Open Book (#679)

**How it was built.** scripts/import/import_old_town_newhall.php ran the shared engine scripts/import/_series_import.php over every page in oldtownnewhall.json whose path matched #^/oldtownnewhall/patti/#, and set partOfCollection to otn-patti and appended each to articlesInCollection. Membership was decided by folder path alone; the script header says so ("The directory decides which"). The collection's author was set by scripts/import/set_collection_authors_2.php from a hand map of folder slug to name (otn-patti => Patti Rasmussen). Article writtenBy was set later by scripts/import/quality_byline_dates.php, only where the page's own dateline ("Patti Rasmussen · May 24, 1997") named the collection author; that part is page-based. The two Country Fair records got no writtenBy, but they still show "Patti Rasmussen" through the collection box on the article page and the collection listing, which read the collection's writtenBy.

**Count.** The archive holds 27 records in the collection (Nathan's 27): 25 columns plus the two Country Fair records. The legacy index lists 25 Open Book columns, 24 May to 15 November 1997, plus one Gazette piece. The profile's "24 pieces" is the number with writtenBy: the 15 November 1997 column (#12656, pr111597.htm) carries her byline on the page but has no writtenBy, because its originalPublishDate holds a sentence about "See How They Run" instead of the date, so the byline pass skipped it. The profile also ends her run at 8 November; the last column is 15 November. The collection stub body says "Twenty-six pieces". The right figure is 25.

**Every article, checked against its page.**

| id | page | on the page | writtenBy |
|---|---|---|---|
| #12704 | /oldtownnewhall/patti/fair.htm | no byline: Santa Clarita Valley Country Fair event page: Newhall Park, Sun. July 2 to Tues. July 4 (2000 calendar), "**2001 FAIR CANCELED**", rides, music, auctions, 4th of July parade and 5K links, image hamlet.gif. No author, no Open Book masthead (the columns carry pattitle.gif, "'Open Book' with Patti Rasmussen"). Not on the Reggie mirror; text is from the crawl | none |
| #12702 | /oldtownnewhall/patti/fair1197.htm | no byline: 107-byte redirect stub (meta refresh to /oldtownnewhall/patti/fair.htm). The record's text was crawled from the target page | none |
| #12674 | /oldtownnewhall/patti/pr052497.htm | byline on page: Patti Rasmussen · May 24, 1997 | Patti Rasmussen #2591 |
| #12696 | /oldtownnewhall/patti/pr053197.htm | byline on page: Patti Rasmussen · May 31, 1997 | Patti Rasmussen #2591 |
| #12694 | /oldtownnewhall/patti/pr060797.htm | byline on page: Patti Rasmussen · June 7, 1997 | Patti Rasmussen #2591 |
| #12692 | /oldtownnewhall/patti/pr061497.htm | byline on page: Patti Rasmussen · June 14, 1997 | Patti Rasmussen #2591 |
| #12700 | /oldtownnewhall/patti/pr062197.htm | byline on page: Patti Rasmussen · June 21, 1997 | Patti Rasmussen #2591 |
| #12690 | /oldtownnewhall/patti/pr062897.htm | byline on page: Patti Rasmussen · June 28, 1997 | Patti Rasmussen #2591 |
| #12672 | /oldtownnewhall/patti/pr070597.htm | byline on page: Patti Rasmussen · July 5, 1997 | Patti Rasmussen #2591 |
| #12688 | /oldtownnewhall/patti/pr071297.htm | byline on page: Patti Rasmussen · July 12, 1997 | Patti Rasmussen #2591 |
| #12666 | /oldtownnewhall/patti/pr071997.htm | byline on page: Patti Rasmussen · July 19, 1997 | Patti Rasmussen #2591 |
| #12670 | /oldtownnewhall/patti/pr072697.htm | byline on page: Patti Rasmussen · July 26, 1997 | Patti Rasmussen #2591 |
| #12664 | /oldtownnewhall/patti/pr080297.htm | byline on page: Patti Rasmussen · August 2, 1997 | Patti Rasmussen #2591 |
| #12686 | /oldtownnewhall/patti/pr080997.htm | byline on page: Patti Rasmussen · August 9, 1997 | Patti Rasmussen #2591 |
| #12654 | /oldtownnewhall/patti/pr082397.htm | byline on page: Patti Rasmussen · August 23, 1997 | Patti Rasmussen #2591 |
| #12668 | /oldtownnewhall/patti/pr083097.htm | byline on page: Patti Rasmussen · August 30, 1997 | Patti Rasmussen #2591 |
| #12684 | /oldtownnewhall/patti/pr090697.htm | byline on page: Patti Rasmussen · September 6, 1997 | Patti Rasmussen #2591 |
| #12698 | /oldtownnewhall/patti/pr091397.htm | byline on page: Patti Rasmussen · September 13, 1997 | Patti Rasmussen #2591 |
| #12682 | /oldtownnewhall/patti/pr092097.htm | byline on page: Patti Rasmussen · September 20, 1997 | Patti Rasmussen #2591 |
| #12680 | /oldtownnewhall/patti/pr092797.htm | byline on page: Patti Rasmussen · September 27, 1997 | Patti Rasmussen #2591 |
| #12678 | /oldtownnewhall/patti/pr100497.htm | byline on page: Patti Rasmussen · October 4, 1997 | Patti Rasmussen #2591 |
| #12662 | /oldtownnewhall/patti/pr101197.htm | byline on page: Patti Rasmussen · October 11, 1997 | Patti Rasmussen #2591 |
| #12660 | /oldtownnewhall/patti/pr101897.htm | byline on page: Patti Rasmussen · October 18, 1997 | Patti Rasmussen #2591 |
| #12676 | /oldtownnewhall/patti/pr102597.htm | byline on page: Patti Rasmussen · October 25, 1997 | Patti Rasmussen #2591 |
| #12652 | /oldtownnewhall/patti/pr110197.htm | byline on page: Patti Rasmussen · November 1, 1997 | Patti Rasmussen #2591 |
| #12658 | /oldtownnewhall/patti/pr110897.htm | byline on page: Patti Rasmussen · November 8, 1997 | Patti Rasmussen #2591 |
| #12656 | /oldtownnewhall/patti/pr111597.htm | byline on page: Patti Rasmussen · November 15, 1997 | none |

**Lacking her byline (come out):**

- #12704 santa-clarita-valley-country-fair-2, /oldtownnewhall/patti/fair.htm: Santa Clarita Valley Country Fair event page: Newhall Park, Sun. July 2 to Tues. July 4 (2000 calendar), "**2001 FAIR CANCELED**", rides, music, auctions, 4th of July parade and 5K links, image hamlet.gif. No author, no Open Book masthead (the columns carry pattitle.gif, "'Open Book' with Patti Rasmussen"). Not on the Reggie mirror; text is from the crawl
- #12702 santa-clarita-valley-country-fair, /oldtownnewhall/patti/fair1197.htm: 107-byte redirect stub (meta refresh to /oldtownnewhall/patti/fair.htm). The record's text was crawled from the target page

These two are also one record twice (see duplicate-slugs-2026-10-04.md). Of the 25 that stay, #12656 needs writtenBy and its date field fixed (the page reads "Patti Rasmussen · November 15, 1997").

Also: Patti Rasmussen also has two bylined Gazette pieces, "Newhall Hardware Quits." (#12595) and "Saletore: Young At Hart." (#12593), and the Open Book index links a third, "Marc Winger: New chief for Newhall schools" (Old Town Newhall Gazette, Sept. 1997). None of these has writtenBy.

## 2. Making Cents (#673)

**How it was built.** scripts/import/import_coins.php ran the same engine with pathFilter #^/scvhistory/signal/coins/#: membership by folder. The script header calls all 259 "weekly pieces by Dr. Sol Taylor, each with a byline"; that is not so for 40 of them. Collection author Sol Taylor by set_collection_authors_2.php. No article in the collection has writtenBy, so every one of the 259 is shown under Sol Taylor's name only through the collection.

**Count.** 259 articles: 219 carry "By Dr. Sol Taylor", 40 do not.

**Lacking Sol Taylor's byline (40):**

| id | page | what it is |
|---|---|---|
| #15446 | /scvhistory/signal/coins/nlg05winners.htm | Results of NLG Annual Writers' Competition for: Numismatic Literary Guild 2005 writers' competition results list; no author (not on the Reggie mirror; crawl text) (crawl text; not on the mirror) |
| #15448 | /scvhistory/signal/coins/nlg06winners.htm | Results of NLG Annual Writers' Competition for: Numismatic Literary Guild 2006 writers' competition results list; no author |
| #15406 | /scvhistory/signal/coins/sg011407-sayles.htm | 'Cultural Property': At Odds with Globalism: Signal guest piece "By Wayne G. Sayles", January 14, 2007 |
| #15362 | /scvhistory/signal/coins/sg080205.htm | Coin Columnist a Double Winner: Signal news story about Taylor's awards, "By Leon Worden" (crawl text; not on the mirror) |
| #15164 | /scvhistory/signal/coins/sg082606-awards.htm | Signal Coin Columnist a Triple Winner: Signal news story about Taylor's awards, "By Signal Staff" (crawl text; not on the mirror) |
| #15390 | /scvhistory/signal/coins/worden-coinage0106a.htm | National Treasure: COINage magazine article, "By Leon Worden" |
| #15408 | /scvhistory/signal/coins/worden-coinage0106b.htm | 1933 Double 'Legal': COINage magazine article, "By Leon Worden" |
| #15410 | /scvhistory/signal/coins/worden-coinage0107a.htm | Part I: The Greatest Treasure Ever Seized: COINage magazine article, "By Leon Worden" |
| #15412 | /scvhistory/signal/coins/worden-coinage0107b.htm | Part II: Sharing the Wealth: COINage magazine article, "By Leon Worden" |
| #15450 | /scvhistory/signal/coins/worden-coinage0107c.htm | Bowers: What Isn't In a Name?: COINage magazine article, "By Leon Worden" |
| #14938 | /scvhistory/signal/coins/worden-coinage0206a.htm | The Denver Mint Today: COINage magazine article, "By Leon Worden" |
| #15292 | /scvhistory/signal/coins/worden-coinage0206b.htm | Coin Blanking Might Be Privatized: COINage magazine article, "By Leon Worden" |
| #15452 | /scvhistory/signal/coins/worden-coinage0207.htm | Good Timing: COINage magazine article, "By Leon Worden" |
| #15006 | /scvhistory/signal/coins/worden-coinage0306a.htm | Art for Artists' Sake: COINage magazine article, "By Leon Worden" |
| #14942 | /scvhistory/signal/coins/worden-coinage0306b.htm | Faith and Fortune: COINage magazine article, "By Leon Worden" |
| #15414 | /scvhistory/signal/coins/worden-coinage0307.htm | 'Illegal' (Double) Eagles: COINage magazine article, "By Leon Worden" |
| #15416 | /scvhistory/signal/coins/worden-coinage0506.htm | 'Mr. ANA': COINage magazine article, "By Leon Worden" |
| #14940 | /scvhistory/signal/coins/worden-coinage0606.htm | Copper in the Hopper: COINage magazine article "Copper in the Hopper", "By Leon Worden"; Sol Taylor appears only in a photo caption and as a source |
| #15418 | /scvhistory/signal/coins/worden-coinage0706a.htm | Collecting Hobo Nickels: COINage magazine article, "By Leon Worden", July 2006 |
| #15420 | /scvhistory/signal/coins/worden-coinage0706b.htm | A Question of Legitimacy: COINage magazine article, "By Leon Worden" |
| #15422 | /scvhistory/signal/coins/worden-coinage07annual.htm | The Wonderful World of Coin Collecting: COINage magazine article, "By Leon Worden" |
| #15424 | /scvhistory/signal/coins/worden-coinage0806a.htm | The Young Numismatists: COINage magazine article, "By Leon Worden" |
| #15426 | /scvhistory/signal/coins/worden-coinage0806b.htm | Walter Ostromecki: Mr. Numismatics: COINage magazine article, "By Leon Worden" |
| #14954 | /scvhistory/signal/coins/worden-coinage0906-odyssey.htm | Odyssey Marine Exploration to Move New Orleans Shipwreck Attraction to New Market: Odyssey Marine Exploration press release, August 3, 2006, reproduced; no author |
| #15428 | /scvhistory/signal/coins/worden-coinage0906.htm | After the Storms: COINage magazine article, "By Leon Worden" |
| #15294 | /scvhistory/signal/coins/worden-coinage1005.htm | California's REAL First Gold: COINage magazine article, "By Leon Worden" |
| #15430 | /scvhistory/signal/coins/worden-coinage1006.htm | Coins + PR = Donn Pearlman: COINage magazine article, "By Leon Worden" |
| #14944 | /scvhistory/signal/coins/worden-coinage1105.htm | 'Canada 125' and the U.S. 50-State Quarters: COINage magazine article, "By Leon Worden" |
| #15432 | /scvhistory/signal/coins/worden-coinage1106a.htm | No Small Change at the Mint: COINage magazine article, "By Leon Worden" |
| #15402 | /scvhistory/signal/coins/worden-coinage1106b.htm | Ancient Coin Buyers, Beware: COINage magazine article, "By Leon Worden" |
| #15434 | /scvhistory/signal/coins/worden-coinage1106c.htm | 1933 Saints March Into National Coin Show: COINage magazine article, "By Leon Worden" |
| #15008 | /scvhistory/signal/coins/worden-coinage1107a.htm | Mastering the British Royal Mint: COINage magazine article, "By Leon Worden" |
| #15454 | /scvhistory/signal/coins/worden-coinage1107b.htm | Mr. Brenner's Lincoln: A Profile: COINage magazine article, "By Leon Worden" |
| #15436 | /scvhistory/signal/coins/worden-coinage1205a.htm | 50-State Quarters: Credit Where Credit Is Due: COINage magazine article, "By Leon Worden" |
| #15438 | /scvhistory/signal/coins/worden-coinage1205b.htm | 50-State Quarters: Quarter-ly Art: COINage magazine article, "By Leon Worden" |
| #15440 | /scvhistory/signal/coins/worden-coinage1206a.htm | What's New in the Universe: COINage magazine article, "By Leon Worden" |
| #15442 | /scvhistory/signal/coins/worden-coinage1206b.htm | Third-Party Independence?: COINage magazine article, "By Leon Worden" |
| #15444 | /scvhistory/signal/coins/worden-coinage1206c.htm | Stacks-ANR: Two for the Money: COINage magazine article, "By Leon Worden" |
| #15166 | /scvhistory/signal/coins/worden-coinage1206d.htm | Stack, Bowers: Paths that Keep Crossing: COINage magazine article, "By Leon Worden" |
| #15392 | /scvhistory/signal/coins/worden-coinage1207a.htm | Mr. Brenner's Lincoln: Forgotten Figures: COINage magazine article, "By Leon Worden" |

35 of the 40 are by Leon Worden: 34 of his COINage magazine articles (the worden-coinage pages) and his Signal story sg080205 about Taylor's awards. The coins folder held them, so the import filed them under Taylor. The other 5 are a guest piece by Wayne G. Sayles, a Signal Staff story, two NLG results lists and an Odyssey Marine press release (filed as worden-coinage0906-odyssey).

## 3. Richard 'Doc' Rioux At Large (#683)

**How it was built.** Same script and engine, folder /oldtownnewhall/rioux/; collection author by set_collection_authors_2.php. The folder is Leon's memorial site for Rioux, so it holds tributes by other people alongside the columns.

**Count.** 44 articles: 33 columns under "Richard "Doc" Rioux · date", plus the front matter of his book "Images" ("by Richard H. Rioux"), which is his but not a column. 10 are not by him.

**Lacking his byline (10):**

| id | page | what it is |
|---|---|---|
| #12858 | /oldtownnewhall/rioux/chanson.htm | Touched by the Web site: Letter by Christina Hanson, August 3, 1998 (signed CHRISTINA HANSON) |
| #12850 | /oldtownnewhall/rioux/hmtrib.htm | Tribute to Dr. Richard Rioux: Congressional Record tribute by Rep. Howard P. "Buck" McKeon, May 6, 1997 (signed HOWARD P. "BUCK" McKEON) |
| #12864 | /oldtownnewhall/rioux/miningco.htm | Giant Loss to the Recovery Community: Tribute by "Buddy T." of The Mining Company, May 14, 1997 (©1997 The Mining Company) |
| #12860 | /oldtownnewhall/rioux/rboerner.htm | Richard is still working on Earth: Tribute by Rich Boerner, April 6, 1998 (signed RICH BOERNER) |
| #12854 | /oldtownnewhall/rioux/rrbio.htm | Biography: "Biographical Sketch" of Rioux (BA/MA/PhD, career), unsigned; reads as the author note from his book. About him, not by him |
| #12866 | /oldtownnewhall/rioux/rrdarcy.htm | The Genius and Magic of "Doc": Tribute by Councilwoman Jo Anne Darcy, May 1997 (signed JO ANNE DARCY) |
| #12862 | /oldtownnewhall/rioux/rrmiller.htm | Richard provided friendship and comfort: Tribute by James L. Miller, February 18, 1998 (signed JAMES L. MILLER) |
| #12870 | /oldtownnewhall/rioux/rrsigtrb.htm | A Sad Farewell to "Doc" Rioux: Signal editorial, "A Sad Farewell to Doc Rioux", April 29, 1997 (tribute; signed "Signal Editorial", ©1997 The Signal) |
| #12868 | /oldtownnewhall/rioux/rrvonnie.htm | May Peace Be With You: Tribute by Vonnie Wang, May 2, 1997 (signed VONNIE WANG) |
| #12852 | /oldtownnewhall/rioux/tributes.htm | Tributes: Index page listing the tributes to Rioux by other writers; no text by Rioux |

None of the 10 has writtenBy, so the only attribution to Rioux is the collection's. Several (McKeon, Darcy, The Signal's editorial) are sources for his person record rather than pieces of his column.

## 4. Collections that pass

- **Pauline Harte (#681)**, 38 articles, all with the author's byline on the page. Same script and engine as Open Book, folder /oldtownnewhall/pauline/; collection author by set_collection_authors_2.php.
- **Black 'N' Whyte (#685)**, 37 articles, all with the author's byline on the page. Same script and engine, folder /oldtownnewhall/whyte/; collection author by set_collection_authors_2.php.
- **Selections from Leon Worden (#665)**, 219 articles, all with the author's byline on the page. scripts/import/import_worden_columns.php ran the same engine with pathFilter #^/scvhistory/signal/worden/#, so membership is by folder, including /old/. Collection author by set_collection_authors_2.php; article writtenBy (196) set from the page dateline by quality_byline_dates.php.
- **History of the Santa Clarita Valley (#871)**, 80 articles, all with the author's byline on the page. scripts/import/import_reynolds.php: the chapters as listed on the book's contents page, plus four pages picked by hand ($ALSO_BY). Not by folder. scripts/import/set_collection_authors.php then gave writtenBy Jerry Reynolds to every member that lacked one (by membership, not by page); 26 carry it. Every page passes anyway: each chapter carries the running head "HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS" and the four extra pieces carry "By Jerry Reynolds".
- **Story of Our Valley (#873)**, 20 articles, all with the author's byline on the page. scripts/import/import_perkins.php and attach_perkins_collection.php: the 13 series pages from the contents page, plus 7 "related pages" that perkins.json lists as his. Not by folder. writtenBy by set_collection_authors.php (membership). Every page passes: series pages carry "THE STORY OF OUR VALLEY BY A.B. PERKINS" and the related pages "By A.B. Perkins" or "By Arthur B. Perkins".

Worden: 218 pages carry his byline and the 219th (#12558) is a redirect stub to a bylined page. 23 of the 219 are second copies of a column (see duplicate-slugs-2026-10-04.md). #12534 "The Paul Allen LDS Letter" is a note signed LEON WORDEN that reprints a letter by Paul Allen; #12144 (lw062605) adds a companion commentary "By Richard A. Patterson" after Worden's column.

- **Old Town Newhall Gazette (#677)**, 37 articles, built by folder but with no collection author, so nothing is attributed by path. Gazette: 34 of 37 pages carry a byline (Leon Worden's editorials, Pat Saletore, Darryl Manzer, Chris Price, Patti Rasmussen and others); none of the 37 records has writtenBy.

## 5. writtenBy set without the page

No article in any collection has a writtenBy that contradicts its page. set_collection_authors.php gave writtenBy by collection membership for Perkins and Reynolds. Every one of those pages names the author, so no wrong byline came of it. set_collection_authors_2.php set only the collections' authors; no article's writtenBy contradicts its page in any collection checked.

Two records carry a writtenBy the page does not show:

- #1418 /scvhistory/signal/perkins/intro.html, writtenBy Leon Worden #279: Unsigned introduction under the running head "THE STORY OF OUR VALLEY BY A.B. PERKINS". It opens with a modern editor's note ("Nearly 50 years ago, between April 1954 and January 1955...") and then the 1954 Signal introduction. Worden is inferred, not on the page.
- #1432 /scvhistory/signal/perkins/notes.html, writtenBy Leon Worden #279: "Editor's Notes", unsigned, under the Perkins running head. Worden is inferred.

The live risk is the collection author rather than writtenBy: articles without writtenBy are shown under the collection's author in the collection box on the article page and on the articles index. That is how the Country Fair page, 40 Making Cents pieces and 10 Rioux tributes come to read as their work.

