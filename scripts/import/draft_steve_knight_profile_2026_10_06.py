#!/usr/bin/env python3
"""Steve Knight #29328: the profile draft (Nathan, 6 October 2026, on the Wikipedia census: "build profiles from the
primary sources those articles point at. Label anything resting on Wikipedia alone"). Claude Code, research subagent.
The slug is steve-knight so the files do not collide with his father Pete Knight's (draft_knight_profile_2026_10_06.py).

Writes inventory/review/steve-knight-profile-draft-2026-10-06.json (read by
scripts/import/build_steve_knight_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

He is living: public life only. No birth fields.

The record already carries one footnote, written by build_knight_profile_2026_10_06.php: the source for his childOf link
to Pete Knight, which the page's kinship test (hist.publicKin in templates/_partials/record/historical.twig) reads from
this record's footnotes. It is carried here word for word as note FATHER, and the loader refuses if it is not.

Every quotation below was read in the source named, on 6 October 2026: the mirror pages from the Reggie mirror (latin-1),
the Biographical Directory capture, the Records of Members and Senators, the Secretary of State's special election pages
and the Statements of Vote from inventory/sources/legislative-districts-2026-10-04/, Public Law 116-9 from
inventory/sources/katie-hill-2026-10-06/. The check at the foot of this file confirms each quotation against the saved
copy and each vote count against the text of its Statement of Vote. No quotation joins two clauses with an ellipsis, and
none quotes a passage that holds an em dash.
Run on the host: python3 scripts/import/draft_steve_knight_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
OUT = os.path.join(ROOT, 'inventory', 'review', 'steve-knight-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'steve-knight-2026-10-06')
LD = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')
KH = os.path.join(ROOT, 'inventory', 'sources', 'katie-hill-2026-10-06')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

FATHER = 'Pete Knight, his father, also held office for the valley, in the Assembly and the State Senate (office holdings #29338 and #29357). SCVHistory.com, the introduction to its page of Edwards Air Force Base\'s film of 3 October 2017, /scvhistory/eafb100317.htm: "Pete Knight is the father of U.S. Rep. Steve Knight, R-Palmdale." Richard Fausset, Los Angeles Times, May 9, 2004 (/scvhistory/lat050904.htm): "Besides his son David, Knight is survived by his wife, Gail; sons Peter and Steven; four stepchildren, and 15 grandchildren." JoinCalifornia, "Steve Knight," https://www.joincalifornia.com/candidate/13281, read 6 October 2026: "Son of Pete Knight."'

NOTES = {
    'TERMS': 'His terms are the archive\'s office holdings #29363 (State Senate, 21st District, 2011 lines, 3 December 2012 to 5 January 2015, ended by resignation) and #29376 (United States House of Representatives, 25th District, 2011 lines, 3 January 2015 to 3 January 2019). Biographical Directory of the United States Congress, "KNIGHT, Steve," as captured by the Wayback Machine on 24 November 2020, https://web.archive.org/web/20201124000853id_/https://bioguideretro.congress.gov/Home/MemberDetails?memIndex=K000387: "member of the California state senate, 2012-2014; elected as a Republican to the One Hundred Fourteenth and to the succeeding Congress (January 3, 2015-January 3, 2019); was an unsuccessful candidate for reelection to the One Hundred Sixteenth Congress in 2018." Secretary of the Senate, Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88: "Knight, Steve", R, "Los Angeles, San Bernardino", regular sessions 2013 to 2015; note 112: "Resigned from office January 5, 2015, elected to Congress. Succeeded by Sharon Runner."',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, all under the "2011 lines, drawn by the Citizens Redistricting Commission". State Senate, in force "December 2012 to December 2024": the 21st held "79.7%" of the valley, 216,311 people at the 2010 Census, "Most of the valley, with the Antelope and Victor valleys"; the rest, Stevenson Ranch and the City\'s western and southwestern neighborhoods, was in the 27th. House of Representatives, in force "January 2013 to January 2023": the 25th held "the whole valley", "The whole valley, with Palmdale, eastern Lancaster and part of Simi Valley".',
    'FATHER': FATHER,
    'BIOGUIDE': 'Biographical Directory of the United States Congress, "KNIGHT, Steve" (the capture cited in note 1): "graduated from Palmdale High School; A.A., Antelope Valley College, Lancaster, Calif., 2006; United States Army, 1985-1987; United States Army Reserve, 1987-1993; police officer, Los Angeles Calif.; member of the Palmdale, Calif., city council, 2005-2008; member of the California state assembly, 2008-2012, assistant minority leader, 2010-2012; vice mayor of Palmdale, Calif."',
    'ASM': 'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79: "Knight, Steve", R, "Los Angeles, San Bernardino", regular sessions 2009 to 2012. His Assembly seat was the 36th District under the 2001 lines; templates/_data/valley-districts.json: "the 2001-plan 36th Assembly District held Acton and no part of the valley."',
    'S2012': 'California Secretary of State, Statement of Vote, General Election, November 6, 2012, State Senate, 21st State Senate District, https://elections.cdn.sos.ca.gov/sov/2012-general/13-state-senators.pdf: Star Moffatt (DEM) 112,780, 42.4%; Steve Knight (REP) 153,412, 57.6%.',
    'RUNNER': 'California Secretary of State, Special Primary Election, March 17, 2015, State Senate District 21, http://www.sos.ca.gov/elections/prior-elections/special-elections/2015-sd21/election-results: "Sharon Runner, REP" 26,360 votes, "94.1%". Her office holding #29365.',
    'MCKEON': 'Buck McKeon\'s office holdings for the 25th District: #26980 (1991 lines, from 3 January 1993), #29372 (2001 lines) and #29374 (2011 lines, to 3 January 2015). The Signal, on SCVHistory.com, "Rep. Knight Bill Honors Dam Victims, Protects Sacred Sites," press conference of 4 August 2015, /scvhistory/knight080415.htm: "Rep. Buck McKeon, Knight\'s predecessor, introduced a similar bill, H.R. 5357, the Saint Francis Dam Disaster National Memorial Act, nearly a year ago but retired before the bill went through."',
    'C2014': 'California Secretary of State, Statement of Vote, General Election, November 4, 2014, United States Representative, 25th Congressional District, https://elections.cdn.sos.ca.gov/sov/2014-general/pdf/43-congress.pdf: Steve Knight (REP) 60,847, 53.3%; Tony Strickland (REP) 53,225, 46.7%.',
    'C2016': 'California Secretary of State, Statement of Vote, General Election, November 8, 2016, United States Representative, 25th Congressional District, https://elections.cdn.sos.ca.gov/sov/2016-general/sov/26-us-reps-formatted.pdf: Bryan Caforio (DEM) 122,406, 46.9%; Steve Knight* (REP, the asterisk marking the incumbent) 138,755, 53.1%.',
    'C2018': 'California Secretary of State, Statement of Vote, General Election, November 6, 2018, United States Representative, 25th Congressional District, https://elections.cdn.sos.ca.gov/sov/2018-general/sov/48-congress.pdf: Katie Hill (DEM) 133,209, 54.4%; Steve Knight* (REP) 111,813, 45.6%. Katie Hill\'s office holding #29378.',
    'SPECIAL': 'California Secretary of State, "Final Official Election Results - Congressional District 25," Special Primary Election, March 3, 2020, https://www.sos.ca.gov/elections/prior-elections/special-elections/2019-cd25/official-results-primary: "Christy Smith, DEM" 58,920, "36.2%"; "Mike Garcia, REP" 41,365, "25.4%"; "Steve Knight, REP" 27,911, "17.1%"; "Vacancy resulting from the resignation of Katie Hill."',
    'DAM': 'SCVHistory.com\'s pages on the bills, all naming "U.S. Rep. Steve Knight, R-Palmdale": /scvhistory/knight_hr3153.htm, "Saint Francis Dam Disaster National Memorial and Castaic Wilderness Act As Introduced U.S. Rep. Steve Knight, R-Palmdale Wednesday, July 22, 2015."; /scvhistory/hr5244_051616.htm, "Saint Francis Dam Disaster National Memorial Act As Introduced U.S. Rep. Steve Knight, R-Palmdale Monday, May 16, 2016."; /scvhistory/hr2156_2017.htm, "U.S. Reps. Steve Knight, R-Palmdale, and Julia Brownley, D-Westlake Village. Introduced April 26, 2017." The Signal\'s report of the press conference of 4 August 2015 (/scvhistory/knight080415.htm): "U.S. Rep. Steve Knight held a press conference Tuesday morning to announce and present the Saint Francis Dam Disaster National Memorial and Castaic Wilderness Act."; "About 50 Santa Clarita Valley leaders, residents, Native Americans and St. Francis Dam historians attended the event at Tesoro Adobe Historic Park."; "the H.R. 3153 bill also seeks to designate about 69,000 acres of surrounding federal lands as wilderness".',
    'FLOOR': 'Congressional Record, House, July 11, 2017, the debate on H.R. 2156, on SCVHistory.com, /scvhistory/congress071117.htm. Mr. LaHood: "H.R. 2156, introduced by the gentleman from California (Mr. KNIGHT), my good friend, recognizes the incident\'s devastation and subsequent impacts on the residents of northern Los Angeles County by establishing a national memorial and monument to preserve the area for future generations." Mr. Knight: "This is something that has affected our community. It happened less than 20 miles from my house, almost 100 years ago, and today I rise in remembrance of the Saint Francis Dam and the bill I sponsored, which would establish a national memorial to honor those in this terrible tragedy." The record closes: "(two-thirds being in the affirmative) the rules were suspended and the bill was passed."',
    'LAW': 'SCVHistory.com, /scvhistory/s47_2019.htm, on section 1111 of S. 47: "S. 1926/H.R. 2156 (Sen. Harris-D, Rep. Knight-R)." Public Law 116-9, the John D. Dingell, Jr. Conservation, Management, and Recreation Act, approved March 12, 2019, https://www.govinfo.gov/content/pkg/PLAW-116publ9/html/PLAW-116publ9.htm (saved in inventory/sources/katie-hill-2026-10-06/), section 1111: "The Secretary may establish a memorial at the Saint Francis Dam site in the county of Los Angeles, California, for the purpose of honoring the victims of the Saint Francis Dam disaster of March 12, 1928."; the Monument: "comprising approximately 353 acres".',
    'SHERIFF': 'City of Santa Clarita, "Groundbreaking: SCV Sheriff\'s Station, Golden Valley Road," July 25, 2018, on SCVHistory.com, /scvhistory/sc1806.htm: "representatives from the offices of Congressman Steve Knight, State Senator Scott Wilk, State Assemblyman Dante Acosta and State Assemblyman Tom Lackey presented certificates honoring the groundbreaking event."',
}

BODY = [
    'Steve Knight represented most of the Santa Clarita Valley in the State Senate, for the 21st District, from December 2012 to January 2015, and the whole valley in the United States House of Representatives, for the 25th District, from January 2015 to January 2019.{TERMS} Under the 2011 lines the 21st held 79.7 per cent of the valley\'s people, with the Antelope and Victor valleys, and the 25th held all of it, with Palmdale, eastern Lancaster and part of Simi Valley.{DIST} He came from Palmdale, and he was the son of Pete Knight, who had represented the valley in the Assembly and the State Senate.{BIOGUIDE}{FATHER}',
    'He served in the Army from 1985 to 1987 and in the Army Reserve until 1993, and was a police officer in Los Angeles. He sat on the Palmdale City Council from 2005 to 2008 and in the Assembly from 2008 to 2012, where he was assistant minority leader from 2010.{BIOGUIDE} His Assembly district, the 36th, held Acton and none of the valley.{ASM}',
    'He won the 21st Senate District in November 2012, beating Star Moffatt, a Democrat, by 153,412 votes to 112,780.{S2012} Two years into his term he was elected to Congress, and he resigned from the Senate on 5 January 2015; Sharon Runner won the seat at a special election that March.{TERMS}{RUNNER} In the House he succeeded Buck McKeon, who had held the valley\'s seat since 1993. In the general election of November 2014 both candidates were Republicans, and he beat Tony Strickland by 60,847 votes to 53,225.{MCKEON}{C2014} He was re-elected in 2016, over Bryan Caforio, by 138,755 votes to 122,406,{C2016} and lost in 2018 to Katie Hill, by 111,813 to 133,209.{C2018} When Hill resigned in 2019 he stood in the special primary of 3 March 2020 and finished third, with 27,911 votes, 17.1 per cent, behind Christy Smith and Mike Garcia.{SPECIAL}',
    'In Congress he took up the national memorial at the site of the St. Francis Dam, in San Francisquito Canyon, that McKeon had proposed before he retired.{MCKEON} Knight introduced his first bill in July 2015, with a Castaic wilderness of about 69,000 acres, and presented it that August at Tesoro Adobe Historic Park to about 50 of the valley\'s leaders, residents, Native Americans and historians of the dam. He brought in a second in May 2016 and a third, H.R. 2156, with Julia Brownley, in April 2017.{DAM} Speaking for it in the House on 11 July 2017 he said, "It happened less than 20 miles from my house, almost 100 years ago," and the House passed it that day.{FLOOR} After he left office its terms became section 1111 of S. 47, approved on 12 March 2019, which authorized a memorial at the dam site to the victims of the disaster of 12 March 1928 and established a national monument of about 353 acres.{LAW} His office was among those that presented certificates at the groundbreaking of the new Santa Clarita Valley Sheriff\'s Station on Golden Valley Road on 25 July 2018.{SHERIFF}',
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
# a note cited before it is defined in the sentence order is fine; the reference to "note 1" in BIOGUIDE must stay true
if num['TERMS'] != 1:
    raise SystemExit('BIOGUIDE says "the capture cited in note 1": TERMS must be note 1')
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


def pdf(path):
    t = subprocess.run(['pdftotext', path, '-'], capture_output=True, text=True).stdout
    return re.sub(r'\s+', ' ', t)


def pdfl(path):
    return re.sub(r'\s+', ' ', subprocess.run(['pdftotext', '-layout', path, '-'], capture_output=True, text=True).stdout)


def mp(rel):
    return plain(os.path.join(MIRROR, 'scvhistory', rel), 'latin-1')


VD = re.sub(r'\s+', ' ', open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read())
BG = plain(os.path.join(LD, 'house', 'bioguide-K000387-wayback.html'))
SEN = pdfl(os.path.join(LD, 'legislature', 'senate-record-of-senators-1849-2026.pdf'))
ASMT = pdfl(os.path.join(LD, 'legislature', 'assembly-record-of-members-1849-2026.pdf'))
SP20 = plain(os.path.join(LD, 'sos', 'special-elections', '2019-cd25', 'official-results-primary.html'))
CHECK = {
    'TERMS': BG + SEN, 'BIOGUIDE': BG, 'DIST': VD, 'ASM': ASMT + VD,
    'RUNNER': plain(os.path.join(LD, 'sos', 'special-elections', '2015-sd21', 'election-results.html')),
    'SPECIAL': SP20,
    'FATHER': mp('eafb100317.htm') + mp('lat050904.htm'),
    'MCKEON': mp('knight080415.htm'),
    'DAM': mp('knight_hr3153.htm') + mp('hr5244_051616.htm') + mp('hr2156_2017.htm') + mp('knight080415.htm'),
    'FLOOR': mp('congress071117.htm'),
    'LAW': mp('s47_2019.htm') + plain(os.path.join(KH, 'plaw-116publ9.htm')),
    'SHERIFF': mp('sc1806.htm'),
}
TITLES = ('KNIGHT, Steve,', 'KNIGHT, Steve', 'Los Angeles, San Bernardino', '2011 lines, drawn by the Citizens Redistricting Commission',
          'December 2012 to December 2024', 'January 2013 to January 2023', 'Final Official Election Results - Congressional District 25,',
          'Rep. Knight Bill Honors Dam Victims, Protects Sacred Sites,', 'Groundbreaking: SCV Sheriff\'s Station, Golden Valley Road,',
          'U.S. Rep. Steve Knight, R-Palmdale', 'Steve Knight,', 'Son of Pete Knight.')
FIGS = {
    'S2012': (pdfl(os.path.join(LD, 'sos', 'general-election-november-6-2012', '13-state-senators.pdf')), ['21st State Senate District', 'Moffatt', '112,780', '153,412', '42.4%', '57.6%']),
    'C2014': (pdfl(os.path.join(LD, 'sos', 'general-election-november-4-2014', '43-congress.pdf')), ['25th Congressional District', 'Strickland', '60,847', '53,225', '53.3%', '46.7%']),
    'C2016': (pdfl(os.path.join(LD, 'sos', 'general-election-november-8-2016', '26-us-reps-formatted.pdf')), ['Caforio', 'Knight*', '122,406', '138,755', '46.9%', '53.1%']),
    'C2018': (pdfl(os.path.join(LD, 'sos', 'general-election-november-6-2018', '48-congress.pdf')), ['Knight*', '133,209', '111,813', '54.4%', '45.6%']),
    'SPECIAL': (SP20, ['58,920', '41,365', '27,911']),
    'RUNNER': (CHECK['RUNNER'], ['26,360', 'Special Primary Election, March 17, 2015']),
    'DIST': (VD, ['"pop": 216311']),
    'ASM': (ASMT, ['Knight, Steve R Los Angeles, San Bernardino 2009']),
    'TERMS': (SEN, ['Knight, Steve R Los Angeles, San Bernardino 2013']),
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
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_steve_knight_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29328, 'title': 'Steve Knight'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'Police officer; state senator; congressman',
        'wikidataId': 'Q7613060', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Steve_Knight_(politician)',
        'bioguideId': 'K000387',
        'rolesAdd': [18387],
        'relatedPersonsAdd': [18791, 29332, 29324],
    },
    'keepNotes': [FATHER],
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Steve Knight #29328: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_steve_knight_profile_2026_10_06.php) reads the .json beside this file. He is living: public life only. Sources: the Reggie mirror pages named in the notes, inventory/sources/legislative-districts-2026-10-04/, inventory/sources/katie-hill-2026-10-06/ (Public Law 116-9) and inventory/sources/steve-knight-2026-10-06/ (the Wikipedia wikitext, kept as a finding aid only; manifest.json there).', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'The one footnote the record has now (the source for childOf Pete Knight) is carried word for word as note ' + str(num['FATHER']) + '; the page\'s kinship test reads it. roles gains State Senator #18387 (holding #29363). relatedPersons gains Buck McKeon #18791 (his predecessor in the 25th), Katie Hill #29332 (his successor in the 25th) and Sharon Runner #29324 (his successor in the 21st). Tony Strickland #29448, his opponent in 2014, is named in the body but not related. No birth fields (living).', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- His full name "Stephen Thomas Knight" and birth date "December 17, 1966" (the Biographical Directory gives both the date and Edwards Air Force Base; a living person\'s birth is not recorded here in any case).',
       '- That he served "18 years with the Los Angeles Police Department" and on its CRASH team (the Biographical Directory says only "police officer, Los Angeles Calif."), and that he was an Army "tracked vehicle systems mechanic in Friedberg, Germany".',
       '- His Palmdale City Council dates "December 5, 2005, to December 1, 2008" and the names of his predecessor and successor there; that he succeeded Sharon Runner in the Assembly in 2008; his Assembly committees.',
       '- A 2014 bill on disabled veterans\' property tax exemptions signed by Governor Brown.',
       '- The April 2015 altercation with a protester, his response to the Aliso Canyon (Porter Ranch) gas leak of 2015 and 2016, endorsements, the National Republican Congressional Committee\'s Patriot Program, and his stance in the 2016 presidential election.',
       '- That he also stood in the regular 2020 primary for the next term (the body has only the special primary, from the Secretary of State).',
       '- A family detail about a brother and an uncle, which is private and is left out entirely.', '',
       '## What the mirror holds on him', '',
       'Searched 6 October 2026 with grep over every .htm, .html and .txt file in /Volumes/Reggie/SCVHistory/scvhistory.com for "Steve Knight", "Rep. Knight", "Congressman Knight", "Senator Knight" and "Sen. Knight". The pages used are the St. Francis Dam memorial set (knight_hr3153.htm, knight080415.htm, hr5244_051616.htm, hr2156_2017.htm, congress071117.htm, hr2156substitute20181002.htm, scvnews100517.htm, s47_2019.htm, s1926_2017.htm, stfrancis.htm), sc1806.htm (the Sheriff\'s Station groundbreaking) and eafb100317.htm (his father\'s X-15 flight). Also there and not used: College of the Canyons Annual Report 2017, page 7 ("U.S. Rep. Steve Knight participated in a student forum"; the scan\'s columns run together), and SCV Water\'s list of supporters of a 2018 application (files/scvwa012918c, page 14, "Office of Congressman Steve Knight"). hr2156substitute20181002.htm, an SCVNews story, says H.R. 2156 "cleared the House July 31"; the Congressional Record and s47_2019.htm both give 11 July 2017, which the body uses.', '',
       '## Not done', '',
       '- No succession notes on his holdings: #29363 ends by resignation and cites the Record of State Senators; #29376 cites the Statements of Vote.',
       '- His other legislation was not read.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources, figures against {len(FIGS)}; written {os.path.relpath(OUT, ROOT)}.json and .md')
