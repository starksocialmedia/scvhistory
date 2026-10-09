/**
 * The bylines still not captured (Nathan, 8 October 2026, item 7: "The 356 bylines still not captured. Run the same pass as
 * the 746, with basis recorded. Hold the writes.").
 *
 * The same pass as link_authors_2026_10_05.php and authorship_basis_2026_10_07.php: a piece takes a person as writtenBy only
 * when its own page prints that person's name as its author and the person already has a record; every link carries
 * authorshipBasis (printed-byline, series-attribution, closing-tagline, derived) and authorshipBasisNote quoting what the page
 * shows. No person record is created here: a name with no record is listed, not linked (the 5 October census rule for which
 * names get a record is Nathan's to approve). Letters, editor's notes, an index page and a host credit are held for Nathan.
 *
 * Every row below was read by hand from the legacy page on Reggie on 8 October 2026. The script re-reads each page and refuses
 * a row whose quoted words are not on it, so the notes cannot drift from the source. It also sweeps every record of a type
 * with writtenBy that has no author for a byline-shaped line in sourceLine or at the head of the body, so a byline outside
 * this table is reported, never dropped.
 *
 * Rows come from two places: the 36 of the 5 October census's 362 B and C records that are still unlinked (325 were linked on
 * 5 and 6 October, #27374 is in the trash, merged into #1434), and records made since the census that print a byline
 * (sg110185's pieces, #31962, the Saugus High School sources, the Scott Newhall oral history).
 *
 * Writes, under $APPLY only: writtenBy, authorshipBasis and authorshipBasisNote on the rows whose outcome is would-link.
 * Never overwrites a writtenBy or a basis already set; reports it. Idempotent: a second run sets nothing.
 * Dry run by default. Hold the writes (Nathan): $APPLY stays false.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/bylines_remaining_2026_10_08.php'))"
 */
use craft\elements\Entry;

$root = \Craft::getAlias('@root');
$MIRROR = '/mnt/reggie/scvhistory.com';
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the legacy pages on Reggie named in the rows below, as text', $MIRROR],
  ['file', 'the 5 October authorship census', "$root/inventory/review/authorship-census-2026-10-05.json"],
  ['record', 'Craft fields writtenBy, authorshipBasis, authorshipBasisNote, sourceLine, legacyUrl, body', 'the legacy pages they were imported from', 'read'],
]);

/* The flag sits after reads(), which must print first (check_census_reads.php). */
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }


/* outcome: would-link (a person record exists, the page prints the name), no-record (the page prints a name with no person
 * record), pen-name, held (Nathan decides), no-byline (the page prints none for this piece), not-author (a credit that is not
 * authorship). basis: what the link would rest on, also for no-record rows, so the basis is ready when a record exists. */
$ROWS = [
  // The census's tracked set, still unlinked.
  [15406, '/scvhistory/signal/coins/sg011407-sayles.htm', 'no-record', 'Wayne G. Sayles', null, 'printed-byline', ['By Wayne G. Sayles', 'Executive Director, Ancient Coin Collectors Guild', 'Wayne G. Sayles of Gainesville, Mo., is the executive director'],
    'Printed byline "By Wayne G. Sayles", with "Executive Director, Ancient Coin Collectors Guild", at the head of the piece (a Guest Commentary in The Signal, Sunday, January 14, 2007); it closes "Wayne G. Sayles of Gainesville, Mo., is the executive director of the Ancient Coin Collectors Guild". Read from the original page.'],
  [12868, '/oldtownnewhall/rioux/rrvonnie.htm', 'no-record', 'Vonnie Wang', null, 'closing-tagline', ['VONNIE WANG', 'May Peace Be With You'],
    'Signed at the end "VONNIE WANG". The page prints no byline at its head; the name there is only in the page\'s title tag ("Tribute to Richard \'Doc\' Rioux · Vonnie Wang"). Read from the original page.'],
  [12862, '/oldtownnewhall/rioux/rrmiller.htm', 'no-record', 'James L. Miller', null, 'closing-tagline', ['JAMES L. MILLER'],
    'Signed at the end "JAMES L. MILLER". No byline at the head; the name there is only in the page\'s title tag. The Tributes index (/oldtownnewhall/rioux/tributes.htm) lists it "By James L. Miller, February 18, 1998". Read from the original page.'],
  [12860, '/oldtownnewhall/rioux/rboerner.htm', 'no-record', 'Rich Boerner', null, 'closing-tagline', ['RICH BOERNER'],
    'Signed at the end "RICH BOERNER". No byline at the head; the name there is only in the page\'s title tag. The Tributes index lists it "By Richard Boerner, April 6, 1998". Read from the original page.'],
  [12858, '/oldtownnewhall/rioux/chanson.htm', 'no-record', 'Christina Hanson', null, 'closing-tagline', ['CHRISTINA HANSON'],
    'Signed at the end "CHRISTINA HANSON". No byline at the head; the name there is only in the page\'s title tag. The Tributes index lists it "By Christina Hanson, August 3, 1998". Read from the original page.'],
  [12864, '/oldtownnewhall/rioux/miningco.htm', 'pen-name', 'Buddy T.', null, 'printed-byline', ['By Buddy T. @ The Mining Company', '©1997, THE MINING COMPANY - ALL RIGHTS RESERVED'],
    'Printed byline "By Buddy T. @ The Mining Company" at the head of the piece; it closes "©1997, THE MINING COMPANY - ALL RIGHTS RESERVED". A pen name: never a person record (census rule). Read from the original page.'],
  [12852, '/oldtownnewhall/rioux/tributes.htm', 'held', 'none of its own', null, null, ['By Congressman Howard P. "Buck" McKeon', 'By Christina Hanson, August 3, 1998', 'Signal Editorial, April 29, 1997'],
    'An index page, not a compilation: it lists each tribute\'s title with its byline line ("By Congressman Howard P. "Buck" McKeon", "By Christina Hanson, August 3, 1998" and so on, ending "Signal Editorial, April 29, 1997") and holds no tribute text of its own. Recommend no author. Read from the original page and the record\'s body (1,149 characters, the list only).'],
  [12649, '/oldtownnewhall/gazette/gazette1101-history.htm', 'no-record', 'Pat Saletore', null, 'printed-byline', ['By PAT SALETORE', 'Executive Director'],
    'Printed byline "By PAT SALETORE", with "Executive Director, Santa Clarita Valley Historical Society.", at the head of the piece (Old Town Newhall Gazette, November-December 2005). Read from the original page.'],
  [12623, '/oldtownnewhall/gazette/gazette1201-history.htm', 'no-record', 'Pat Saletore', null, 'printed-byline', ['By PAT SALETORE', 'Executive Director'],
    'Printed byline "By PAT SALETORE", with "Executive Director, Santa Clarita Valley Historical Society.", at the head of the piece (Old Town Newhall Gazette, January-February 2006). Read from the original page.'],
  [12647, '/oldtownnewhall/gazette/gazette1101-smisko.htm', 'no-record', 'Jason Smisko', null, 'printed-byline', ['By JASON SMISKO', 'Senior Planner'],
    'Printed byline "By JASON SMISKO", with "Senior Planner, City of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, November-December 2005). Read from the original page.'],
  [12625, '/oldtownnewhall/gazette/gazette1201-masters.htm', 'no-record', 'Jason Smisko', null, 'printed-byline', ['By JASON SMISKO', 'Senior Planner,'],
    'Printed byline "By JASON SMISKO", with "Senior Planner, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, January-February 2006). Read from the original page.'],
  [12615, '/oldtownnewhall/gazette/gazette1202-nn.htm', 'no-record', 'Jason Smisko; Jason Mikaelian', null, 'printed-byline', ['By JASON SMISKO', 'And JASON MIKAELIAN', 'Planners, City Of Santa Clarita.'],
    'Printed byline "By JASON SMISKO And JASON MIKAELIAN, Planners, City Of Santa Clarita." at the head of the piece (Old Town Newhall Gazette, March-April 2006): two authors. Read from the original page.'],
  [12637, '/oldtownnewhall/gazette/gazette1201-business.htm', 'no-record', 'Alex Hernandez', null, 'printed-byline', ['By ALEX HERNANDEZ', 'Administrative Analyst For Economic Development,'],
    'Printed byline "By ALEX HERNANDEZ", with "Administrative Analyst For Economic Development, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, January-February 2006). Read from the original page.'],
  [12633, '/oldtownnewhall/gazette/gazette1201-gateking.htm', 'no-record', 'Alex Hernandez', null, 'printed-byline', ['By ALEX HERNANDEZ', 'Administrative Analyst For Economic Development,'],
    'Printed byline "By ALEX HERNANDEZ", with "Administrative Analyst For Economic Development, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, January-February 2006). Read from the original page.'],
  [12635, '/oldtownnewhall/gazette/gazette1201-brotzman.htm', 'no-record', 'Paul Brotzman', null, 'printed-byline', ['By PAUL BROTZMAN', 'Director Of Community Development,'],
    'Printed byline "By PAUL BROTZMAN", with "Director Of Community Development, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, January-February 2006). Read from the original page.'],
  [12631, '/oldtownnewhall/gazette/gazette1201-arts.htm', 'no-record', 'Phil Lantis', null, 'printed-byline', ['By PHIL LANTIS', 'Arts And Events Supervisor,'],
    'Printed byline "By PHIL LANTIS", with "Arts And Events Supervisor, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, January-February 2006). Read from the original page.'],
  [12627, '/oldtownnewhall/gazette/gazette1201-events.htm', 'no-record', 'Andree Walper', null, 'printed-byline', ['By ANDREE WALPER', 'Economic Development Associate,'],
    'Printed byline "By ANDREE WALPER", with "Economic Development Associate, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, January-February 2006). Read from the original page.'],
  [12617, '/oldtownnewhall/gazette/gazette1202-jacobs.htm', 'no-record', 'Shelby Jacobs', null, 'printed-byline', ['By Shelby Jacobs, 1953 Class President, Hart High School.'],
    'Printed byline "By Shelby Jacobs, 1953 Class President, Hart High School." at the head of the piece (Old Town Newhall Gazette, March-April 2006). Read from the original page.'],
  [12611, '/oldtownnewhall/gazette/gazette1202-cowboy.htm', 'no-record', 'Michael Fleming', null, 'printed-byline', ['By MICHAEL FLEMING', 'Cowboy Festival Manager, City Of Santa Clarita.'],
    'Printed byline "By MICHAEL FLEMING", with "Cowboy Festival Manager, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, March-April 2006). Read from the original page.'],
  [12597, '/oldtownnewhall/gazette/gazette1401-pollack.htm', 'no-record', 'Alan Pollack', null, 'printed-byline', ['By DR. ALAN POLLACK', 'President, Santa Clarita Valley Historical Society.'],
    'Printed byline "By DR. ALAN POLLACK", with "President, Santa Clarita Valley Historical Society.", at the head of the piece (Old Town Newhall Gazette, February-March 2008). Read from the original page.'],
  [12591, '/oldtownnewhall/gazette/gazette1401-pricemain.htm', 'no-record', 'Chris Price', null, 'printed-byline', ['By CHRIS PRICE', 'Assistant City Engineer, City Of Santa Clarita.'],
    'Printed byline "By CHRIS PRICE", with "Assistant City Engineer, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, February-March 2008). Read from the original page.'],
  [12587, '/oldtownnewhall/gazette/gazette1402-library.htm', 'no-record', 'Chris Price', null, 'printed-byline', ['By CHRIS PRICE'],
    'Printed byline "By CHRIS PRICE", with "City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, Summer 2008). Read from the original page.'],
  [12583, '/oldtownnewhall/gazette/gazette1403-projects.htm', 'no-record', 'Chris Price', null, 'printed-byline', ['By CHRIS PRICE', 'Assistant City Engineer, City Of Santa Clarita.'],
    'Printed byline "By CHRIS PRICE", with "Assistant City Engineer, City Of Santa Clarita.", at the head of the piece (Old Town Newhall Gazette, September-October 2008). Read from the original page.'],
  [12534, '/scvhistory/signal/worden/old/paulallen.htm', 'held', 'Paul Allen (the letter); Leon Worden (the note above it)', null, 'closing-tagline', ['Sincerely,', 'LEON WORDEN', 'Opinion and Multimedia Editor', 'Paul Allen', 'Santa Clarita, Calif.'],
    'Two pieces on one page and in one record: Leon Worden\'s note, signed "Sincerely, LEON WORDEN, Opinion and Multimedia Editor", then the letter, signed "Paul Allen / Santa Clarita, Calif.". Neither prints a byline at its head. Allen has no record; Worden (#279) wrote the note, not the letter. Held with the letters question. Read from the original page.'],
  [2173, '/scvhistory/signal/reynolds/notes.html', 'held', 'Jerry Reynolds (series heading) or the editor', 281, 'series-attribution', ['HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS', 'Reynolds used "The Camulos Story" by Wally Smith (1958) as the basis for chapters 14 and 15.', '©1998, SANTA CLARITA VALLEY HISTORICAL SOCIETY'],
    'The series heading "HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS" is the only name; the notes speak of Reynolds in the third person ("Reynolds used "The Camulos Story" by Wally Smith (1958) as the basis for chapters 14 and 15."), so they are an editor\'s. Held for Nathan, as on 5 October. Read from the original page.'],
  [28053, '/scvhistory/sg062505.htm', 'no-record', 'Sarah Donner', null, 'printed-byline', ['By Sarah Donner', 'Signal Staff Writer', 'Saturday, June 25, 2005'],
    'Printed byline "By Sarah Donner, Signal Staff Writer" at the head of the obituary (The Signal, Saturday, June 25, 2005). Read from the original page.'],
  [28051, '/scvhistory/dn112503.htm', 'no-record', 'Patricia Farrell Aidem', null, 'printed-byline', ['By PATRICIA', 'FARRELL AIDEM, Staff Writer.', 'L.A. Daily News,'],
    'Printed byline "By PATRICIA FARRELL AIDEM, Staff Writer. L.A. Daily News, Tuesday, November 25, 2003." at the head of the obituary, the name broken over two lines. Read from the original page.'],
  [28049, '/scvhistory/obituary_georgeacaravalho.htm', 'no-record', 'Stephen K. Peeples', null, 'printed-byline', ['By Stephen K. Peeples, SCVNews.com | Monday, January 6, 2020.', 'By Kenneth R. Pulskamp.'],
    'Printed byline "By Stephen K. Peeples, SCVNews.com | Monday, January 6, 2020." at the head of the obituary. The page and the record also hold a eulogy under its own byline, "Eulogy. By Kenneth R. Pulskamp.", and a closing prayer: a second piece inside. Read from the original page.'],
  [28310, '/scvhistory/gt8702.htm', 'no-record', 'Laurel Suomisto', null, 'printed-byline', ['Cityhood Backers — Who Are They?', 'By Laurel Suomisto', 'The Newhall Signal and Saugus Enterprise | Sunday, January 4, 1987.'],
    'Printed byline "By Laurel Suomisto" under the headline "Cityhood Backers — Who Are They?" (The Newhall Signal and Saugus Enterprise, Sunday, January 4, 1987), the second of two pieces on the page. Read from the original page.'],
  [28305, '/scvhistory/khts081214.htm', 'no-record', 'Perry Smith', null, 'printed-byline', ['By Perry Smith, AM-1220 KHTS | Tuesday, August 12, 2014'],
    'Printed byline "By Perry Smith, AM-1220 KHTS | Tuesday, August 12, 2014" at the head of the piece. Read from the original page.'],
  [28295, '/scvhistory/sg110185.htm', 'no-record', 'Karina Lutz', null, 'printed-byline', ['City Backers Join Prison Furor.', 'By Karina Lutz.'],
    'Printed byline "By Karina Lutz." under the headline "City Backers Join Prison Furor." (The Signal, Friday, November 1, 1985), one of seven pieces on the page. Not Lauren Kay: "By Lauren Kay." heads "Bradley: Build Big House Here." (#32715) on the same page. Read from the original page.'],
  [28293, '/scvhistory/gt8702.htm', 'no-byline', null, null, null, ['[Brief.]', 'The Newhall Signal and Saugus Enterprise | Sunday, January 11, 1987.', 'The cityhood movement is going public.'],
    'No byline. The page prints the piece as "[Brief.]" with "The Newhall Signal and Saugus Enterprise | Sunday, January 11, 1987." and no name; "By Laurel Suomisto" below it belongs to the next piece, "Cityhood Backers — Who Are They?" (#28310). Read from the original page.'],
  [28057, '/scvhistory/tlp_laherald072875pg3.htm', 'held', 'John Lang', 18820, 'closing-tagline', ['EDITOR HERALD:', 'JOHN LANG.', 'Lang\'s Station, July 17th.'],
    'A letter to the editor, opening "EDITOR HERALD:" and signed at the end "JOHN LANG. Lang\'s Station, July 17th." (Los Angeles Herald, Wednesday, July 28, 1875, pg 3). The record\'s body ends before the signature. Held on the letters question (does a letter\'s writer count as its author). Read from the original page.'],
  [26983, '/scvhistory/lp_santacruzsentinel082785.htm', 'held', 'Abel Stearns (the letter inside)', 309, null, ['Santa Cruz Sentinel | August 27, 1885.', 'Los Angeles, July 8th, 1867.', 'Abel Stearns.'],
    'The record is the Santa Cruz Sentinel\'s article of August 27, 1885 ("Bogus History. Angelenos the First Argonauts."), unsigned, which reprints Stearns\'s letter dated "Los Angeles, July 8th, 1867." and signed "Abel Stearns.", and a second letter signed "A Robinson.". Stearns wrote the letter inside, not the article. Held on the letters question. Read from the original page.'],
  [671, '/scvhistory/signal/newsmaker/index.htm', 'not-author', 'Leon Worden (host)', 279, null, ['Host: Leon Worden'],
    'The page credits "Host: Leon Worden" for the television program. A host is not an author; no writtenBy. Read from the original page.'],
  [12135, '/mentryville/mstory.htm', 'would-link', 'Leon Worden', 279, 'printed-byline', ['By LEON WORDEN', 'With editorial assistance from Ruth Waldo Newhall', 'First Printing, March 1996'],
    'Printed byline "By LEON WORDEN" on the book page this collection stands for ("California\'s Pioneer Oil Town", first printing March 1996, revised July 1997), followed by "With editorial assistance from Ruth Waldo Newhall and research assistance from Paul R. Higgins". Read from the original page.'],

  // Made since the 5 October census.
  [32715, '/scvhistory/sg110185.htm', 'no-record', 'Lauren Kay', null, 'printed-byline', ['Bradley: Build Big House Here.', 'By Lauren Kay.'],
    'Printed byline "By Lauren Kay." under the headline "Bradley: Build Big House Here." (The Signal, Friday, November 1, 1985). Read from the original page.'],
  [32717, '/scvhistory/sg110185.htm', 'no-record', 'Joseph Kehoe', null, 'printed-byline', ['By Joseph Kehoe.'],
    'Printed byline "By Joseph Kehoe." under the piece\'s headline (The Signal, Friday, November 1, 1985). Read from the original page.'],
  [32719, '/scvhistory/sg110185.htm', 'no-record', 'Laurel Suomisto', null, 'printed-byline', ['By Laurel Suomisto.'],
    'Printed byline "By Laurel Suomisto." under the piece\'s headline (The Signal, Friday, November 1, 1985). Read from the original page.'],
  [32724, '/scvhistory/sg110185.htm', 'no-record', 'Simon-Jacques Ifergan', null, 'printed-byline', ['By Simon-Jacques Ifergan.'],
    'Printed byline "By Simon-Jacques Ifergan." under the piece\'s headline (The Signal, Friday, November 1, 1985). Read from the original page.'],
  [32726, '/scvhistory/sg110185.htm', 'no-record', 'Thomas Omestad', null, 'printed-byline', ['By Thomas Omestad.'],
    'Printed byline "By Thomas Omestad." under the piece\'s headline (Los Angeles Times, Tuesday, November 5, 1985). Read from the original page.'],
  [31962, '/scvhistory/signal/worden/lw062605.htm', 'no-record', 'Richard A. Patterson', null, 'printed-byline', ['Paving the Way For New Schools', 'By Richard A. Patterson', 'President, SCV Facilities Foundation'],
    'Printed byline "By Richard A. Patterson, President, SCV Facilities Foundation" under the headline "Paving the Way For New Schools" (The Signal, Sunday, June 26, 2005), the second piece on Leon Worden\'s column page. Read from the original page.'],
  [31308, '/scvhistory/sg20191114shs.htm', 'no-record', 'Jim Holt', null, 'printed-byline', ['By Jim Holt.', 'The Signal | Thursday, November 14, 2019.'],
    'Printed byline "By Jim Holt." with "The Signal | Thursday, November 14, 2019." at the head of the piece; it closes "Signal Staff Writers Caleb Lunetta and Tammy Murga and SCVTV/SCVNews.com Editor-Reporter Stephen K. Peeples contributed." Read from the original page.'],
  [31326, '/scvhistory/sg20191117shs.htm', 'no-record', 'Emily Alvarenga', null, 'printed-byline', ['By Emily Alvarenga.', 'The Signal | Sunday Evening, November 17, 2019.'],
    'Printed byline "By Emily Alvarenga." with "The Signal | Sunday Evening, November 17, 2019." at the head of the piece. Read from the original page.'],
  [31332, '/scvhistory/sg20191119shs.htm', 'no-record', 'Tammy Murga', null, 'printed-byline', ['By Tammy Murga.', 'The Signal | Tuesday, November 19, 2019.'],
    'Printed byline "By Tammy Murga." with "The Signal | Tuesday, November 19, 2019." at the head of the piece. Read from the original page.'],
  [31314, '/scvhistory/scvtv20191115shs.htm', 'no-record', 'Stephen K. Peeples', null, 'printed-byline', ['By Stephen K. Peeples.', 'SCVTV/SCVNews.com | Friday, November 15, 2019.'],
    'Printed byline "By Stephen K. Peeples." with "SCVTV/SCVNews.com | Friday, November 15, 2019." under the headline "Saugus Grads Set Up Fund to Aid Recovery, Healing.". Read from the original page.'],
  [31310, '/scvhistory/lat20191115shs.htm', 'no-record', 'Marisa Gerber; James Queally; Hannah Fry; Sarah Parvini', null, 'printed-byline', ['By Marisa Gerber, James Queally, Hannah Fry and Sarah Parvini'],
    'Printed byline "By Marisa Gerber, James Queally, Hannah Fry and Sarah Parvini" with "Los Angeles Times | Friday, November 15, 2019" at the head of the piece: four authors. Read from the original page.'],
  [31316, '/scvhistory/lat20191116shs.htm', 'no-record', 'Colleen Shalby; Alejandra Reyes-Velarde; Leila Miller; Soumya Karlamangla', null, 'printed-byline', ['By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla'],
    'Printed byline "By Colleen Shalby, Alejandra Reyes-Velarde, Leila Miller and Soumya Karlamangla" (Los Angeles Times, Saturday, November 16, 2019): four authors. Read from the original page.'],
  [31318, '/scvhistory/lat20191116shs.htm', 'no-record', 'Alejandra Reyes-Velarde; Colleen Shalby', null, 'printed-byline', ['By Alejandra Reyes-Velarde and Colleen Shalby'],
    'Printed byline "By Alejandra Reyes-Velarde and Colleen Shalby" (Los Angeles Times, Friday, November 15, 2019): two authors. Read from the original page.'],
  [31320, '/scvhistory/lat20191116shs.htm', 'no-record', 'Hannah Fry; Leila Miller; Richard Winton; Brittny Mejia', null, 'printed-byline', ['By Hannah Fry, Leila Miller, Richard Winton and Brittny Mejia'],
    'Printed byline "By Hannah Fry, Leila Miller, Richard Winton and Brittny Mejia" (Los Angeles Times, Saturday, November 16, 2019): four authors. Read from the original page.'],
  [31322, '/scvhistory/lat20191116shs.htm', 'no-record', 'Brittny Mejia; Ruben Vives; Richard Winton; Alejandra Reyes-Velarde', null, 'printed-byline', ['By Brittny Mejia, Ruben Vives, Richard Winton and Alejandra Reyes-Velarde'],
    'Printed byline "By Brittny Mejia, Ruben Vives, Richard Winton and Alejandra Reyes-Velarde" (Los Angeles Times, Saturday, November 16, 2019): four authors. Read from the original page.'],
  [31324, '/scvhistory/lat20191116shs.htm', 'no-record', 'Sandy Banks', null, 'printed-byline', ['Commentary by Sandy Banks.'],
    'Printed byline "Commentary by Sandy Banks." (Los Angeles Times, Saturday, November 16, 2019). Read from the original page.'],
  [31328, '/scvhistory/lat20191118shs.htm', 'no-record', 'Sandy Banks; Laura Newberry', null, 'printed-byline', ['By Sandy Banks and Laura Newberry'],
    'Printed byline "By Sandy Banks and Laura Newberry" (Los Angeles Times, Monday, November 18, 2019): two authors. Read from the original page.'],
  [31330, '/scvhistory/lat20191118shs.htm', 'no-record', 'Marisa Gerber', null, 'printed-byline', ['Facing a New Wave of Grief.', 'By Marisa Gerber'],
    'Printed byline "By Marisa Gerber" under the headline "Facing a New Wave of Grief." (Los Angeles Times, Monday, November 18, 2019). Read from the original page.'],
  [31334, '/scvhistory/hd20200112.htm', 'no-record', 'Mike Kuhlman', null, 'closing-tagline', ['Sincerely', 'Mike Kuhlman', 'Deputy Superintendent'],
    'A message signed at the end "Sincerely, Mike Kuhlman, Deputy Superintendent", which opens "This is Mike Kuhlman, Deputy Superintendent"; no byline at the head. Read from the original page.'],
  [31306, '/scvhistory/bryanmuehlberger20191117.htm', 'held', 'Bryan Muehlberger', null, null, ['The following letter was written by Gracie Anne Muehlberger\'s father, Bryan Muehlberger'],
    'A family letter: the page\'s note says "The following letter was written by Gracie Anne Muehlberger\'s father, Bryan Muehlberger, for inclusion in the #SaugusStrong Vigil". The writer is named by the note, not by a byline. Held on the letters question; the father of a victim is not a candidate for a record under the census rule. Read from the original page.'],
  [31723, '/scvhistory/uc8901.htm', 'held', 'Scott Newhall (narrator); Suzanne B. Riess (interviewer)', 31431, null, ['Scott Newhall.', 'Interviewer: Suzanne B. Riess.', 'The Bancroft Library, University of California, Berkeley.'],
    'An oral history: the page heads it "Scott Newhall." with the title, then "Interviewer: Suzanne B. Riess." and "1988-1989 | The Bancroft Library, University of California, Berkeley.". Whether a narrator counts as the author is Nathan\'s to decide; Scott Newhall has a record (#31431), Riess none. Read from the original page.'],
];

$fs = Craft::$app->getFields(); $el = Craft::$app->getElements();
$noteField = $fs->getFieldByHandle('authorshipBasisNote');
$limit = $noteField ? ($noteField->charLimit ?? null) : null;
$pages = [];
$pageText = function (string $u) use ($MIRROR, &$pages): ?string {
  if (array_key_exists($u, $pages)) { return $pages[$u]; }
  $p = $MIRROR . $u;
  if (!is_file($p)) { return $pages[$u] = null; }
  $t = mb_convert_encoding((string)file_get_contents($p), 'UTF-8', 'Windows-1252');
  $t = preg_replace('~<(script|style|title)\b.*?</\1>~is', ' ', $t);
  $t = html_entity_decode(html_entity_decode(strip_tags($t), ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  return $pages[$u] = preg_replace('~\s+~u', ' ', str_replace("\u{00A0}", ' ', $t));
};
$norm = fn(string $s) => preg_replace('~\s+~u', ' ', $s);
$has = function ($e, string $h): bool { $l = $e->getFieldLayout(); return $l && $l->getFieldByHandle($h) !== null; };

$census = json_decode((string)file_get_contents("$root/inventory/review/authorship-census-2026-10-05.json"), true)['records'] ?? [];
$tracked = []; foreach ($census as $c) { if (in_array($c['class'], ['B', 'C'], true)) { $tracked[(int)$c['id']] = $c; } }

$count = []; $refused = 0; $n = 0; $listed = [];
echo PHP_EOL . 'THE ROWS (id, section, outcome, name as printed, basis):' . PHP_EOL;
foreach ($ROWS as [$id, $url, $outcome, $names, $pid, $basis, $quotes, $note]) {
  $listed[$id] = true;
  $e = Entry::find()->id($id)->status(null)->one();
  $tag = str_pad("#$id", 8) . str_pad($e ? $e->section->handle : '?', 12);
  if (!$e) { echo "$tag REFUSED: no such entry" . PHP_EOL; $refused++; continue; }
  $text = $pageText($url);
  if ($text === null) { echo "$tag REFUSED: page $url is not on Reggie" . PHP_EOL; $refused++; continue; }
  $missing = array_values(array_filter($quotes, fn($q) => mb_strpos($text, $norm($q)) === false));
  if ($missing) { echo "$tag REFUSED: not on $url: \"" . implode('", "', $missing) . '"' . PHP_EOL; $refused++; continue; }
  if ($limit && mb_strlen($note) > $limit) { echo "$tag REFUSED: note over the field's $limit characters" . PHP_EOL; $refused++; continue; }
  $w = $has($e, 'writtenBy') ? $e->getFieldValue('writtenBy')->status(null)->ids() : null;
  if ($w === null) { echo "$tag REFUSED: the entry type has no writtenBy" . PHP_EOL; $refused++; continue; }
  $origin = isset($tracked[$id]) ? 'census' : 'new';
  $state = $outcome;
  if ($w) { $state = 'already-linked'; }
  $count[$origin][$state] = ($count[$origin][$state] ?? 0) + 1;
  echo "$tag " . str_pad($state, 15) . str_pad($origin, 7) . mb_substr($names ?? '-', 0, 48) . ($basis ? " [$basis]" : '') . ($pid ? " person #$pid" : '') . PHP_EOL;
  if ($w) { echo '          writtenBy is already [' . implode(',', $w) . ']; left alone' . PHP_EOL; continue; }
  if ($outcome !== 'would-link') { continue; }
  $p = Entry::find()->id($pid)->section('persons')->status(null)->one();
  if (!$p) { echo "          REFUSED: person #$pid not found" . PHP_EOL; $refused++; continue; }
  foreach (['authorshipBasis', 'authorshipBasisNote'] as $h) { if (!$has($e, $h)) { echo "          REFUSED: no $h on this entry type" . PHP_EOL; $refused++; continue 2; } }
  $cur = (string)$e->getFieldValue('authorshipBasis')->value;
  if ($cur !== '') { echo "          REFUSED: a basis ($cur) is set with no writtenBy; left alone" . PHP_EOL; $refused++; continue; }
  echo "          WOULD SET writtenBy #$pid {$p->title}, authorshipBasis $basis, note: $note" . PHP_EOL;
  if ($APPLY) {
    $e->setFieldValue('writtenBy', [$pid]); $e->setFieldValue('authorshipBasis', $basis); $e->setFieldValue('authorshipBasisNote', $note);
    if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
  }
}

/* The census's tracked records not in the table: linked since, or gone. */
$since = ['linked' => 0, 'gone' => [], 'unlisted' => []];
foreach ($tracked as $id => $c) {
  if (isset($listed[$id])) { continue; }
  $e = Entry::find()->id($id)->status(null)->one();
  if (!$e) { $since['gone'][] = $id; continue; }
  if ($has($e, 'writtenBy') && $e->getFieldValue('writtenBy')->status(null)->ids()) { $since['linked']++; } else { $since['unlisted'][] = $id; }
}

/* The sweep: every record of a type with writtenBy, no author, not in the table, whose sourceLine or the head of its body
 * prints a byline-shaped line. Reported for a reader, never acted on. */
$types = [];
foreach (Craft::$app->getEntries()->getAllEntryTypes() as $t) { $l = $t->getFieldLayout(); if ($l && $l->getFieldByHandle('writtenBy')) { $types[] = $t->handle; } }
$sweep = [];
foreach (Entry::find()->type($types)->status(null)->all() as $e) {
  if (isset($listed[$e->id]) || $e->getFieldValue('writtenBy')->status(null)->ids()) { continue; }
  $sl = $has($e, 'sourceLine') ? trim((string)$e->getFieldValue('sourceLine')) : '';
  $body = $has($e, 'body') ? mb_substr(trim(preg_replace('~\s+~u', ' ', strip_tags((string)$e->getFieldValue('body')))), 0, 400) : '';
  if (preg_match('~(^|\s)(By|BY|Commentary by)\s+[A-Z][\w.\'-]+(\s+[A-Z][\w.\'-]+){0,3}~u', $sl . ' | ' . $body, $m)) {
    $sweep[] = str_pad("#{$e->id}", 8) . str_pad($e->section->handle, 12) . mb_substr($e->title, 0, 50) . '  "' . trim($m[0]) . '"';
  }
}

echo PHP_EOL . 'COUNTS (rows, by where the row came from and what the page shows):' . PHP_EOL . json_encode($count) . PHP_EOL;
echo 'refused: ' . $refused . PHP_EOL;
echo 'the 5 October census tracked set (B and C): ' . count($tracked) . '; linked since and outside this table: ' . $since['linked']
  . '; gone (trash): ' . (implode(', ', array_map(fn($i) => "#$i", $since['gone'])) ?: 'none')
  . '; unlinked and not in this table: ' . (implode(', ', array_map(fn($i) => "#$i", $since['unlisted'])) ?: 'none') . PHP_EOL;
echo PHP_EOL . 'SWEEP: records with no author, not in the table, with a byline-shaped line (' . count($sweep) . '):' . PHP_EOL;
foreach ($sweep as $s) { echo "  $s" . PHP_EOL; }
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('bylines_remaining_2026_10_08.php', $n, 'verified', 'writtenBy with its basis on the bylines left after 5 October'); }
echo ($APPLY ? "done: $n saved" : 'DRY RUN: nothing written; a second run after an apply sets nothing') . PHP_EOL;
