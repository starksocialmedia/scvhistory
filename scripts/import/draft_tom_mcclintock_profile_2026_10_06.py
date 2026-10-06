#!/usr/bin/env python3
"""Tom McClintock #29446: the profile draft (Nathan, 6 October 2026, on the Wikipedia census: "build profiles from the
primary sources those articles point at. Label anything resting on Wikipedia alone"). Claude Code, research subagent.

Writes inventory/review/tom-mcclintock-profile-draft-2026-10-06.json (read by
scripts/import/build_tom_mcclintock_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

He is living: public life only. No birth fields.

Every quotation below was read in the source named, on 6 October 2026: the mirror pages from the Reggie mirror (latin-1;
the City's 2007 book from its flipbook text, page 19), the Records of Members and Senators and the Statements of Vote
from inventory/sources/legislative-districts-2026-10-04/, the Biographical Directory capture and the 2003 Statement of
Vote from inventory/sources/tom-mcclintock-2026-10-06/ (manifest.json there). The check at the foot of this file confirms
each quotation against the saved copy and each vote count against the text of its Statement of Vote. No quotation joins
two clauses with an ellipsis, and none quotes a passage that holds an em dash.
Run on the host: python3 scripts/import/draft_tom_mcclintock_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
OUT = os.path.join(ROOT, 'inventory', 'review', 'tom-mcclintock-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'tom-mcclintock-2026-10-06')
LD = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'TERMS': 'His terms for the valley are the archive\'s office holdings #29495 (Assembly, 38th District, 1991 lines, 2 December 1996 to 4 December 2000), #29513 (State Senate, 19th District, 1991 lines, 4 December 2000 to 6 December 2004) and #29515 (19th District, 2001 lines, 6 December 2004 to 1 December 2008), each from the Secretary of State\'s Statements of Vote. Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79: "McClintock, Tom", R, "Ventura", regular sessions 1983 to 1992, and R, "Los Angeles, Ventura", 1997 to 2000. Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88: R, "Los Angeles, Ventura", 2001 to 2004, and R, "Los Angeles, Santa Barbara, Ventura", 2005 to 2008.',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json. Under the "1991 lines, drawn by the court\'s Special Masters", the 38th Assembly District held "16.3%" of the valley, 33,449 people at the 2000 Census, "The unincorporated west side: Castaic, Val Verde and Stevenson Ranch", and the 19th Senate District the same people, "Castaic, Val Verde and Stevenson Ranch, with eastern Ventura County". Under the "2001 lines, drawn by the Legislature", the 19th held "25.8%", 70,081 people at the 2010 Census: "Stevenson Ranch and the City\'s western neighborhoods, Valencia west of the river". The file\'s note: "A senator serves four years and half the Senate is elected every two, so new lines reach the valley\'s Senate seat at its next election: the 2001 lines in 2004, the 2021 lines in 2024."',
    'W98': 'Leon Worden, "Local Republicans split over school bonds," The Signal, Wednesday, October 21, 1998, on SCVHistory.com, /scvhistory/signal/worden/old/lw102198.htm (not yet a record in the archive): "McClintock, who represents Stevenson Ranch, Castaic and other areas west of Interstate 5, has built a career on his reputation as the consummate tax fighter."; "This year he went so far as to co-author the official argument against Proposition 1A, a $9.2 billion school construction bond measure that would cost the state about $15.2 billion to repay over 25 years."; "George Runner, who represents the bulk of this valley and the barren one to the north, shares many of McClintock\'s concerns."; Runner: "It\'s a fair compromise for getting schools built in California".',
    'VLF': 'Leon Worden, "Sorting out the bull on the City Council," The Signal, Wednesday, August 12, 1998, on SCVHistory.com, /scvhistory/signal/worden/old/lw081298.htm (not yet a record in the archive): "The debate over The Old Road is not unlike the situation a couple of months ago when Assemblymen Tom McClintock and George Runner asked the council to support their repeal of California\'s vehicle license fee."',
    'GAZ': 'Leon Worden, editorial, "North Newhall, Home Inspections And Eminent Domain," Old Town Newhall Gazette, March-April 2006, on SCVHistory.com, /oldtownnewhall/gazette/gazette1202-editorial.htm: "Three initiatives have entered circulation to limit the use of eminent domain. Two are sponsored by state Sen. Tom McClintock, R-Thousand Oaks; Orange County Supervisor Chris Norby; and Jon Coupal, president of the Howard Jarvis Taxpayers Association."',
    'CITY': 'Gail Ortiz and Diana Sevanian, editors, City of Santa Clarita 1987-2007: Celebrating 20 Years of Success (City of Santa Clarita/Pioneer Publications, 2007), on the archive page /scvhistory/sc19872007.htm (the book is not yet a record in the archive); page 19, a letter on the letterhead of Senator Thomas McClintock, Nineteenth Senatorial District, to the City of Santa Clarita: "Since its incorporation in December of 1987, Santa Clarita has grown to be a premier community, and I applaud the efforts of all of those involved in making the City a"; the letter is not dated on the page.',
    'BIOGUIDE': 'Biographical Directory of the United States Congress, "McCLINTOCK, Tom," as captured by the Wayback Machine on 24 November 2020, https://web.archive.org/web/20201124160335id_/https://bioguideretro.congress.gov/Home/MemberDetails?memIndex=M001177: "journalist; public policy analyst; member of the California state assembly, 1982-1992, 1996-2000; member of the California state senate, 2000-2008; unsuccessful candidate for election to the One Hundred Third Congress in 1992; unsuccessful candidate for election for Governor of California in 2003; elected as a Republican to the One Hundred Eleventh and to the five succeeding Congresses (January 3, 2009-present)."',
    'A1998': 'California Secretary of State, Statement of Vote, General Election, November 3, 1998, https://elections.cdn.sos.ca.gov/sov/1998-general/sov1998-general.pdf, 38th Assembly District: Tom McClintock, Rep-Inc, Los Angeles 44,689, Ventura 33,728, District Totals 78,417, 100.00%, no other candidate. He was first elected to the 38th on 5 November 1996 (Statement of Vote, https://elections.cdn.sos.ca.gov/sov/1996-general/assemblymember.pdf, a scanned page, cited by holding #29495).',
    'S2000': 'California Secretary of State, Statement of Vote, General Election, November 7, 2000, State Senate, 19th State Senate District, https://elections.cdn.sos.ca.gov/sov/2000-general/sen.pdf: Daniel R. Gonzalez (DEM) 121,893, 42.4%; Tom McClintock (REP) Los Angeles 47,254, Ventura 118,168, District Totals 165,422, 57.6%.',
    'S2004': 'California Secretary of State, Statement of Vote, Presidential General Election, November 2, 2004, State Senator, 19th State Senate District, https://elections.cdn.sos.ca.gov/sov/2004-general/formatted_st_sen_all_detail.pdf: Paul Graber (DEM) 151,085, 39.2%; Tom McClintock* (REP) Los Angeles 18,595, Santa Barbara 66,699, Ventura 148,071, District Totals 233,365, 60.8%.',
    'RECALL': 'California Secretary of State, Statement of Vote, Statewide Special Election, October 7, 2003, Governor (the candidates to succeed Gray Davis if he were recalled), https://elections.cdn.sos.ca.gov/sov/2003-special/gov.pdf, State Totals: Arnold Schwarzenegger 4,206,284, 48.6%; Cruz M. Bustamante 2,724,874; Tom McClintock (REP) 1,161,287, 13.5%.',
    'NEXT': 'The archive\'s office holdings for his predecessors and successors: in the 38th Assembly District, Paula Boland #29493 (7 December 1992 to 2 December 1996) and Keith Richman #29497 (from 4 December 2000); in the 19th Senate District, Cathie Wright #29511 (7 December 1992 to 4 December 2000) and Tony Strickland #29517 (from 1 December 2008).',
    'C2008': 'California Secretary of State, Statement of Vote, General Election, November 4, 2008, United States Representative, 4th Congressional District, https://elections.cdn.sos.ca.gov/sov/2008-general/23_34_us_reps.pdf: Charlie Brown (DEM) 183,990, 49.7%; Tom McClintock (REP) 185,790, 50.3%; the district\'s counties: Butte, El Dorado, Lassen, Modoc, Nevada, Placer, Plumas, Sacramento, Sierra.',
}

BODY = [
    'Tom McClintock represented the Santa Clarita Valley\'s west side in the State Assembly, for the 38th District, from December 1996 to December 2000, and in the State Senate, for the 19th District, from December 2000 to December 2008.{TERMS} Under the 1991 district lines both seats held Castaic, Val Verde and Stevenson Ranch, 16.3 per cent of the valley\'s people by the archive\'s count from the 2000 census. The 2001 lines, which reached the 19th at the election of 2004, gave it Stevenson Ranch and the City\'s western neighborhoods, Valencia west of the river: 25.8 per cent of the valley by the archive\'s count from the 2010 census.{DIST} Both districts joined the valley\'s west side to Ventura County, and from 2004 the 19th took in part of Santa Barbara County as well.{TERMS}{S2004} In 1998 the Signal\'s Leon Worden described him as the member who "represents Stevenson Ranch, Castaic and other areas west of Interstate 5," with "his reputation as the consummate tax fighter."{W98}',
    'In 1998 he and George Runner, who represented most of the valley in the Assembly, asked the Santa Clarita City Council to support their repeal of the state\'s vehicle license fee.{W98}{VLF} That fall the two parted over Proposition 1A, a $9.2 billion school construction bond: McClintock co-wrote the official argument against it, and Runner supported it as "a fair compromise for getting schools built in California."{W98} In 2006, by then a state senator from Thousand Oaks, he was a sponsor of two of the three initiatives then circulating to limit the use of eminent domain.{GAZ} For the City\'s book of its first twenty years, published in 2007, he wrote that "Santa Clarita has grown to be a premier community."{CITY}',
    'He had sat in the Assembly before, from 1982 to 1992, and had run for Congress in 1992 without success.{BIOGUIDE}{TERMS} He came to the 38th District in 1996, after Paula Boland, and was re-elected unopposed in 1998, with 78,417 votes.{NEXT}{A1998} In 2000 he succeeded Cathie Wright in the 19th Senate District, beating Daniel R. Gonzalez by 165,422 votes to 121,893, and in 2004 he was re-elected over Paul Graber by 233,365 to 151,085.{NEXT}{S2000}{S2004} In the recall election of 7 October 2003 he stood for governor and finished third, with 1,161,287 votes, 13.5 per cent, behind Arnold Schwarzenegger and Cruz Bustamante.{RECALL}{BIOGUIDE} Keith Richman followed him in the 38th Assembly District in 2000, and Tony Strickland in the 19th Senate District in 2008.{NEXT}',
    'He took his seat in Congress in January 2009, and the Biographical Directory still listed him as serving in November 2020.{BIOGUIDE} He won that first House election, in November 2008, in the 4th District, by 185,790 votes to 183,990 over Charlie Brown; the district\'s returns came from Butte, El Dorado, Lassen, Modoc, Nevada, Placer, Plumas, Sacramento and Sierra counties.{C2008}',
]

order = []
for p in BODY:
    for k in re.findall(r'\{([A-Z0-9]+)\}', p):
        if k not in order:
            order.append(k)
unused = set(NOTES) - set(order)
if unused:
    raise SystemExit('notes never cited: ' + ', '.join(sorted(unused)))
num = {k: i + 1 for i, k in enumerate(order)}
body = '\n\n'.join(re.sub(r'\{([A-Z0-9]+)\}', lambda m: f'[{num[m.group(1)]}]', p) for p in BODY)
footnotes = [{'number': str(num[k]), 'key': k, 'note': NOTES[k]} for k in order]
for t in [body] + [f['note'] for f in footnotes]:
    if '—' in t:
        raise SystemExit('an em dash in our own text: ' + t[:80])
    if re.search(r'\.\.\.|…', t):
        raise SystemExit('an ellipsis in our own text or a quotation: ' + t[:80])


def plain(path, enc='utf-8'):
    t = open(path, encoding=enc, errors='replace').read()
    t = re.sub(r'(?is)<(script|style).*?</\1>', ' ', t)
    t = html.unescape(re.sub(r'<[^>]+>', ' ', t))
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('\u0092', "'")
    return re.sub(r'\s+', ' ', t)


def pdfl(path):
    return re.sub(r'\s+', ' ', subprocess.run(['pdftotext', '-layout', path, '-'], capture_output=True, text=True).stdout)


def mp(rel):
    return plain(os.path.join(MIRROR, rel), 'latin-1')


VD = re.sub(r'\s+', ' ', open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read())
SEN = pdfl(os.path.join(LD, 'legislature', 'senate-record-of-senators-1849-2026.pdf'))
ASMT = pdfl(os.path.join(LD, 'legislature', 'assembly-record-of-members-1849-2026.pdf'))
CHECK = {
    'TERMS': SEN + ASMT, 'DIST': VD,
    'W98': mp('scvhistory/signal/worden/old/lw102198.htm'),
    'VLF': mp('scvhistory/signal/worden/old/lw081298.htm'),
    'GAZ': mp('oldtownnewhall/gazette/gazette1202-editorial.htm'),
    'CITY': plain(os.path.join(MIRROR, 'scvhistory', 'files', 'sc19872007', 'files', 'basic-html', 'page19.html')),
    'BIOGUIDE': plain(os.path.join(SRC, 'wayback-bioguide-M001177-20201124160335.html')),
}
TITLES = ('McClintock, Tom', 'Los Angeles, Ventura', 'Los Angeles, Santa Barbara, Ventura', '1991 lines, drawn by the court\'s Special Masters',
          '2001 lines, drawn by the Legislature', 'Local Republicans split over school bonds,', 'Sorting out the bull on the City Council,',
          'North Newhall, Home Inspections And Eminent Domain,', 'McCLINTOCK, Tom,')
FIGS = {
    'TERMS': (ASMT + SEN, ['McClintock, Tom R Ventura 1983', 'R Los Angeles, Ventura 1997', 'McClintock, Tom R Los Angeles, Ventura 2001', 'R Los Angeles, Santa Barbara, Ventura 2005']),
    'DIST': (VD, ['"pop": 33449', '"pop": 70081', '"share": "25.8%"']),
    'A1998': (pdfl(os.path.join(LD, 'sos', 'general-election-november-3-1998', 'sov1998-general.pdf')), ['38th Assembly District Tom Votes not McClintock Cast in Race Rep-Inc Los Angeles 44,689 25,744 Ventura 33,728 14,014 District Totals 78,417 39,758 Percent 100.00%']),
    'S2000': (pdfl(os.path.join(LD, 'sos', 'general-election-november-7-2000', 'sen.pdf')), ['19th State Senate District', 'Los Angeles 38,902 47,254', 'Ventura 82,991 118,168', 'District Totals 121,893 165,422', '42.4% 57.6%']),
    'S2004': (pdfl(os.path.join(LD, 'sos', 'presidential-general-election-november-2-2004', 'formatted_st_sen_all_detail.pdf')), ['Paul Graber Tom McClintock*', 'Los Angeles 8,979 18,595', 'Santa Barbara 60,199 66,699', 'Ventura 81,907 148,071', 'District Totals 151,085 233,365', '39.2% 60.8%']),
    'RECALL': (pdfl(os.path.join(SRC, 'sos-sov-2003-10-07-special-governor.pdf')), ['Tom McClintock - REP', '1,161,287', '13.5%', '4,206,284', '48.6%', '2,724,874']),
    'C2008': (pdfl(os.path.join(LD, 'sos', 'presidential-general-election-november-4-2008', '23_34_us_reps.pdf')), ['4th Congressional District', 'Charlie Brown McClintock', 'District Totals 183,990 185,790', '49.7% 50.3%', 'Butte', 'El Dorado', 'Lassen', 'Modoc', 'Nevada', 'Placer', 'Plumas', 'Sacramento', 'Sierra']),
}
missing = []
for k, text in CHECK.items():
    for q in re.findall(r'"([^"]*)"', NOTES[k]):
        q2 = re.sub(r'\s+', ' ', q)
        if len(q2) < 12 or q2 in TITLES:
            continue
        if q2.rstrip('.,') not in text:
            missing.append(f'{k}: {q2[:90]}')
for k, (text, figs) in FIGS.items():
    for f in figs:
        if f not in text:
            missing.append(f'{k}: figure {f} not in its source')
for p in BODY:
    for q in [q for q in re.findall(r'"([^"]*)"', p) if len(q) >= 8]:
        if not any(q.rstrip('.,') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_tom_mcclintock_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29446, 'title': 'Tom McClintock'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'State assemblyman and senator; congressman',
        'wikidataId': 'Q535887', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Tom_McClintock',
        'bioguideId': 'M001177',
        'rolesAdd': [18313],
        'relatedPersonsAdd': [29444, 29316, 29458, 29448, 18747],
    },
    'keepNotes': [],
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Tom McClintock #29446: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_tom_mcclintock_profile_2026_10_06.php) reads the .json beside this file. He is living: public life only. Sources: the Reggie mirror pages named in the notes, inventory/sources/legislative-districts-2026-10-04/ and inventory/sources/tom-mcclintock-2026-10-06/ (manifest.json there).', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'roles gains State Assemblymember #18313 (holding #29495). relatedPersons gains his predecessors and successors in the two seats, Paula Boland #29444, Keith Richman #29316, Cathie Wright #29458 and Tony Strickland #29448, and George Runner #18747, whom the Signal\'s columns of 1998 pair with him on the vehicle license fee and set against him on Proposition 1A. No birth fields (living).', '',
       'The valley was a small part of his career: at most a quarter of its people were ever in his districts, and his seat in Congress since 2009 is far to the north. The body says so by its proportions rather than in words.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- His full name "Thomas Miller McClintock II", birth date and place (the Biographical Directory gives Bronxville, Westchester County, New York, July 10, 1956; a living person\'s birth is not recorded here in any case), that his family moved to Thousand Oaks in 1965, his UCLA degree subject, chairmanship of the Ventura County Republican Party, his work for State Senator Ed Davis, the Center for the California Taxpayer and the Claremont Institute.',
       '- His Assembly district number (36th) and election margins of 1982 to 1990, and his 1996 margin over Jon Lauritzen ("56%-40%"; the 1996 Statement of Vote is a scanned page and was not read here).',
       '- That he "authored California\'s lethal injection" law; his vote and remark on Proposition 2 (2008); his part in the vehicle license fee cut of 2000 and in opposing its restoration in 2003 (the body has only the 1998 request to the City Council, from the Signal).',
       '- His campaigns for Controller in 1994 and 2002 and for Lieutenant Governor in 2006, with their margins.',
       '- The present name and extent of his House district (the 5th since 2023, with Yosemite).', '',
       '## What the mirror holds on him', '',
       'Searched 6 October 2026 with grep over every .htm, .html and .txt file in /Volumes/Reggie/SCVHistory/scvhistory.com for "McClintock": four pages, all used: lw102198.htm (Signal column, 21 October 1998), lw081298.htm (Signal column, 12 August 1998), gazette1202-editorial.htm (Old Town Newhall Gazette, March-April 2006) and the City\'s 2007 book, page 19 (his letter).', '',
       '## Not done', '',
       '- No succession notes on his holdings: each already cites its Statements of Vote and the Record of State Senators.',
       '- His legislation was not read.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources, figures against {len(FIGS)}; written {os.path.relpath(OUT, ROOT)}.json and .md')
