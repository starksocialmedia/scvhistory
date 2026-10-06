#!/usr/bin/env python3
"""Cathie Wright #29458: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent.

Writes inventory/review/cathie-wright-profile-draft-2026-10-06.json (read by
scripts/import/build_cathie_wright_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the mirror pages from the Reggie mirror
(read-only), the outside sources from the copies saved in inventory/sources/cathie-wright-2026-10-06/ (manifest.json
there), the Record of Members and Record of Senators and the Statements of Vote of 1992 and 1996 from
inventory/sources/legislative-districts-2026-10-04/, the district figures from templates/_data/valley-districts.json.
The 1996 Senate and 1994 Lieutenant Governor Statements of Vote are page scans: their figures were read by eye and are
listed in the .md. The check at the foot of this file confirms each other quotation against the saved copy.
No quotation joins two clauses with an ellipsis, and none quotes a passage that holds an em dash.
Run on the host: python3 scripts/import/draft_cathie_wright_profile_2026_10_06.py
"""
import html, json, os, re, shutil, subprocess

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'cathie-wright-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'cathie-wright-2026-10-06')
LEG = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'SENATE': 'Her Senate term is the archive\'s office holding #29511 (Senate, 19th District, 7 December 1992 to 4 December 2000, under the 1991 lines). California Secretary of State, Statements of Vote, 19th State Senate District: 3 November 1992, Cathie Wright (Rep.) 148,116 votes to Hank Starr (Dem.) 108,052, https://elections.cdn.sos.ca.gov/sov/1992-general/state-senator.pdf; 5 November 1996, Cathie Wright (Rep., incumbent) 160,130 votes, 62.24%, to John Birke (Dem.) 97,133, 37.76% (a page scan, read by eye), https://elections.cdn.sos.ca.gov/sov/1996-general/state-senator.pdf. Secretary of the Senate, Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88: "Wright, Cathie", R, "Los Angeles, Ventura", regular sessions 1993 to 2000.',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, State Senate. Under the "1991 lines, drawn by the court\'s Special Masters", in force for the Senate from "December 1992 to December 2004", the 19th held "16.3%" of the valley, 33,449 people at the 2000 Census: "Castaic, Val Verde and Stevenson Ranch, with eastern Ventura County". The rest, 83.7%, was in the 17th. The shares are counted from census blocks assigned to districts in the Statewide Database\'s block files.',
    'WORDEN96': 'Leon Worden, "A politician by any other name...," column, Wednesday, October 23, 1996, legacy page /scvhistory/signal/worden/old/lw102396.htm (not yet a record in the archive): "West of Interstate 5, Cathie Wright is running for Senate and Tom McClintock is running for Assembly."',
    'LAT12': 'Dennis McLellan, "Cathie Wright dies at 82; former assemblywoman and state senator," Los Angeles Times, April 17, 2012 (read in the Wayback Machine\'s capture of 17 April 2012 of the Times\'s printable page): "The blunt-talking Wright, a maverick Republican who represented the 37th Assembly District and the 19th Senate District, died Saturday at her daughter\'s home in Simi Valley."; "She was 82 and had dementia"; "A Pennsylvania native who moved from Los Angeles to Simi Valley in 1965"; "after being elected to the Simi Valley City Council in 1978"; "She was first elected to the Assembly in 1980 and elected to the Senate in 1992."; "She won the Republican nomination for lieutenant governor in 1994 but lost in the general election to Gray Davis."; "Term limits forced her out of office in 2000."; "including refusing to go along with Republicans when they tried to dump Democratic Assembly Speaker Willie Brown in 1988"; "She also fought with most of the Republicans in the Legislature in 1997 when she voted for the Democratic-backed welfare reform bill opposed by then-Gov. Pete Wilson."; "The daughter of a house maid and a handyman, Wright was born May 18, 1929, in Old Forge, Pa."; "She moved to Los Angeles in 1961."; "She was a Democrat until switching parties in 1976."; "who is challenging Republican Rep. Howard P."',
    'MCCL': 'Tom McClintock\'s office holding #29513 (Senate, 19th District, from 4 December 2000, under the 1991 lines).',
    'ASSEMBLY': 'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79: "Wright, Cathie", R, "Los Angeles, Ventura", regular sessions 1981 to 1982; R, "Los Angeles, Santa Barbara, Ventura", 1983 to 1992. The archive holds no office holding for her Assembly terms.',
    'LW3499': 'SCVHistory.com LW3499, "Walk of Western Stars: Iron Eyes Cody (1985): Commendation from Assemblywoman Cathie Wright," legacy page /scvhistory/lw3499.htm (not yet a record in the archive), from the original certificate bought in 2019 by Leon Worden. The resolution: "By the Honorable Cathie Wright Thirty-seventh Assembly District; relative to commending IRON EYES CODY"; "Members Resolution No. 1504 Dated: August 15, 1985". The page\'s note: "Wright, R-Simi Valley, represented the Santa Clarita Valley in the state Assembly from 1980-1992 and the state Senate from 1992-2000."',
    'PRISON85': 'Legacy page /scvhistory/sg110185.htm, "L.A. Mayor\'s Saugus State Prison Plan Fans Flames of Cityhood, 1985" (one of its articles, Karina Lutz\'s, is the archive\'s document #28295; the three below are not yet records). Joseph Kehoe, "Convoluted Chronology Of \'Prison That Would Not Die\'," The Signal, Friday, November 1, 1985, on the 1984 proposal for a prison at Rancho Valle Escondido in Agua Dulce: "Assemblywoman Cathie Wright and Supervisor Mike Antonovich, both of whom represent the area, along with state Sen. Ed Davis, joined the residents in their fight." Laurel Suomisto, "Legislators Lambaste Mayor\'s Prison Plan," The Signal, Friday, November 1, 1985, on Mayor Tom Bradley\'s offer of the city-owned land above Bouquet Canyon Road in Saugus for a state prison: "Assemblywoman Cathie Wright labeled the mayor\'s proposal"; "plain stupidity"; "He couldn\'t get a vote out of that area if he wanted to," Wright said. Thomas Omestad, "Reaction to Plan to Put Prison at Saugus: Cityhood Rally Shows Surge in Interest," Los Angeles Times, Tuesday, November 5, 1985: "At the rally Monday night at William S. Hart High School in Newhall, the two state legislators who represent the Santa Clarita Valley criticized Bradley\'s plan and lent some support to the concept of cityhood."; "Assemblywoman Cathie Wright (R-Simi Valley) endorsed the cityhood movement outright".',
    'INAUG87': 'BW8703, "A Premiere Evening. The Historic Inauguration of the City Council," City of Santa Clarita, College of the Canyons, December 15, 1987, program book, Bob Weber Collection, legacy page /scvhistory/bw8703.htm (not yet a record; the archive\'s event Santa Clarita Cityhood, #31359, cites it). Page 3, the order of the evening, under "PRESENTATIONS": "The Honorable Ed Davis", "The Honorable Cathie Wright", "The Honorable Michael D. Antonovich".',
    'SCVN12': 'Leon Worden, "Cathie Wright, Former State Senator, Dies at 82," SCVNews.com, Saturday, April 14, 2012, https://scvnews.com/cathie-wright-former-state-senator-dies-at-82/: "Wright, who represented the Santa Clarita Valley in the California Legislature for 20 years, died Saturday at about 9 a.m. from complications related to dementia."; "During her time in the Assembly, Wright carried several pieces of legislation that impacted the Santa Clarita Valley, including a bill that added the valley\'s four water purveyors to the board of the Castaic Lake Water Agency."; Murphy, "now the city of Santa Clarita\'s intergovernmental relations officer"; "noted that Wright was an early and ardent supporter of the creation of the city of Santa Clarita, which incorporated in 1987."; "She was one of the first elected officials to really get on board with cityhood," Murphy said; "her former district director Michael P. Murphy said Saturday"; "As a senator she was instrumental in bringing funding to College of the Canyons, particularly for a new library and Media & Fine Arts building, which were constructed in the mid-1990s."; "Wright was Simi Valley\'s mayor when she ran for and won an Assembly seat that was vacated in 1980 when its previous occupant, Bob Cline, mounted an unsuccessful bid for state Senate."; "who is running for Congress locally under her middle name of Cathie".',
    'LTGOV': 'California Secretary of State, Statement of Vote, General Election, November 8, 1994, Lieutenant Governor by County, https://elections.cdn.sos.ca.gov/sov/1994-general/lt-governor.pdf (a page scan, read by eye), State Totals: Gray Davis (Dem.) 4,441,429, 52.42%; Cathie Wright (Rep.) 3,412,777, 40.28%.',
}

BODY = [
    'Cathie Wright was the State Senator for the west side of the Santa Clarita Valley from December 1992 to December 2000, for the 19th District.{SENATE} Under the district lines drawn in 1991, the 19th took in Castaic, Val Verde and Stevenson Ranch, joined to eastern Ventura County: 16.3 per cent of the valley\'s people by the archive\'s count from the 2000 census.{DIST} As Leon Worden put it in 1996, "West of Interstate 5, Cathie Wright is running for Senate."{WORDEN96} She won the seat in 1992 and again in 1996, and term limits ended her time in the Senate in 2000.{SENATE}{LAT12} Tom McClintock succeeded her.{MCCL}',
    'Before the Senate she spent twelve years in the Assembly, from 1980 to 1992, for the 37th District, which took in parts of Los Angeles and Ventura counties, and from 1983 part of Santa Barbara County as well.{ASSEMBLY}{LW3499}{LAT12} In the 1980s the newspapers counted her as one of the valley\'s two state legislators. In 1984 she joined the residents of Agua Dulce in fighting a proposed state prison there.{PRISON85} When Los Angeles Mayor Tom Bradley offered city land above Bouquet Canyon Road in Saugus for a prison in 1985, she called the plan "plain stupidity" and said, "He couldn\'t get a vote out of that area if he wanted to." At the rally at Hart High School in Newhall that followed, she "endorsed the cityhood movement outright."{PRISON85} When the City of Santa Clarita was inaugurated on 15 December 1987, she was one of the three officials who made presentations.{INAUG87} In 1985 she sent an Assembly resolution commending Iron Eyes Cody on his induction into the Western Walk of Fame in Newhall.{LW3499}',
    'At her death SCVNews.com said she had represented the Santa Clarita Valley in the Legislature for twenty years. In the Assembly she carried a bill that added the valley\'s four water purveyors to the board of the Castaic Lake Water Agency. Michael P. Murphy, her former district director and by then the City\'s intergovernmental relations officer, remembered her as an early supporter of cityhood: "She was one of the first elected officials to really get on board with cityhood." As a senator, by the same account, she was instrumental in bringing funding to College of the Canyons for its new library and its Media & Fine Arts building, built in the mid-1990s.{SCVN12}',
    'Wright was born on 18 May 1929 in Old Forge, Pennsylvania, moved to Los Angeles in 1961 and to Simi Valley in 1965. She was a Democrat until 1976. Elected to the Simi Valley City Council in 1978, she was the city\'s mayor when she won the Assembly seat in 1980.{LAT12}{SCVN12} The Los Angeles Times remembered her as a conservative who often broke with her own party: she refused to join the Republicans who tried to unseat Assembly Speaker Willie Brown in 1988, and in 1997 she voted for the Democratic welfare reform bill that Governor Pete Wilson opposed.{LAT12} In 1994 she won the Republican nomination for lieutenant governor and lost to Gray Davis, 4,441,429 votes to 3,412,777.{LAT12}{LTGOV}',
    'She died at her daughter\'s home in Simi Valley on 14 April 2012, aged 82, of complications related to dementia. That year her daughter, running under the name Cathie Wright, was challenging Representative Howard P. "Buck" McKeon for the valley\'s seat in Congress.{SCVN12}{LAT12}',
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
    if re.search(r'\.\.\.\s+[a-z]|…', t):
        raise SystemExit('an ellipsis joining clauses: ' + t[:80])


def plain(path, enc='utf-8'):
    t = open(path, encoding=enc, errors='replace').read()
    t = re.sub(r'(?is)<(script|style).*?</\1>', ' ', t)
    t = html.unescape(re.sub(r'<[^>]+>', ' ', t))
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('\u0092', "'").replace('﻿', '')
    return re.sub(r'\s+', ' ', t)


def pdf(path):
    if not shutil.which('pdftotext'):
        return None
    t = subprocess.run(['pdftotext', '-layout', path, '-'], capture_output=True, text=True).stdout
    return re.sub(r'\s+', ' ', t)


M = lambda *p: os.path.join(MIRROR, 'scvhistory', *p)
CHECK = {
    'LAT12': [plain(os.path.join(SRC, 'wayback-latimes-2012-04-17-wright-20120417134941.html'))],
    'SCVN12': [plain(os.path.join(SRC, 'scvnews-2012-cathie-wright-dies.html'))],
    'SENATE': [pdf(os.path.join(LEG, 'legislature', 'senate-record-of-senators-1849-2026.pdf')), pdf(os.path.join(LEG, 'sos', 'general-election-november-3-1992', 'state-senator.pdf'))],
    'ASSEMBLY': [pdf(os.path.join(LEG, 'legislature', 'assembly-record-of-members-1849-2026.pdf'))],
    'DIST': [open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read()],
}
for k, p, enc in [('WORDEN96', M('signal', 'worden', 'old', 'lw102396.htm'), 'cp1252'), ('LW3499', M('lw3499.htm'), 'cp1252'), ('PRISON85', M('sg110185.htm'), 'cp1252'),
                  ('INAUG87', M('files', 'bw8703', 'files', 'basic-html', 'page3.html'), 'utf-8')]:
    if os.path.exists(p):
        CHECK[k] = [plain(p, enc)]
        if k == 'INAUG87':
            CHECK[k].append(plain(M('bw8703.htm'), 'cp1252') + plain(M('files', 'bw8703', 'files', 'basic-html', 'page1.html')))
TITLES = ('Cathie Wright dies at 82; former assemblywoman and state senator,', 'Cathie Wright, Former State Senator, Dies at 82,', 'A politician by any other name...,',
          'Walk of Western Stars: Iron Eyes Cody (1985): Commendation from Assemblywoman Cathie Wright,', 'L.A. Mayor\'s Saugus State Prison Plan Fans Flames of Cityhood, 1985',
          'Convoluted Chronology Of \'Prison That Would Not Die\',', 'Legislators Lambaste Mayor\'s Prison Plan,', 'Reaction to Plan to Put Prison at Saugus: Cityhood Rally Shows Surge in Interest,', 'A Premiere Evening. The Historic Inauguration of the City Council,',
          '1991 lines, drawn by the court\'s Special Masters', 'December 1992 to December 2004', 'Castaic, Val Verde and Stevenson Ranch, with eastern Ventura County')
missing = []
for k, texts in CHECK.items():
    texts = [t for t in texts if t]
    if not texts:
        missing.append(f'{k}: source not readable on this host')
        continue
    for q in [x for x in re.findall(r'"([^"]*)"', NOTES[k]) if len(x) >= 12]:
        q2 = re.sub(r'\s+', ' ', q)
        if k != 'DIST' and q2 in TITLES:
            continue
        if not any(q2.rstrip('.,') in t or q2.rstrip('.,').upper() in t.upper() and k == 'INAUG87' for t in texts):
            missing.append(f'{k}: {q2[:90]}')
for p in BODY:
    for q in [x for x in re.findall(r'"([^"]*)"', p) if len(x) >= 8]:
        if not any(q.rstrip('.') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_cathie_wright_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29458, 'title': 'Cathie Wright'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'birthDate': 'May 18, 1929', 'birthDateEdtf': '1929-05-18', 'birthEvidence': 'retrospective', 'birthplace': 'Old Forge, Pennsylvania',
        'deathDate': 'April 14, 2012', 'deathDateEdtf': '2012-04-14', 'deathEvidence': 'contemporary',
        'occupation': 'State assemblywoman and state senator',
        'wikidataId': 'Q5053032', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Cathie_Wright',
        'relatedPersonsAdd': [29446],
    },
    'holdingNotes': {
        '29511': 'Succeeded in the 19th District by Tom McClintock (office holding #29513, from 4 December 2000). Wright could not stand again: Los Angeles Times, April 17, 2012, "Term limits forced her out of office in 2000."',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Cathie Wright #29458: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_cathie_wright_profile_2026_10_06.php) reads the .json beside this file.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'Living or dead: dead. SCVNews.com, dated Saturday, April 14, 2012, says she "died Saturday at about 9 a.m."; the Los Angeles Times of Tuesday, April 17, 2012, says she "died Saturday". deathEvidence is contemporary. Her birth date and place are from the Times obituary (retrospective); SCVNews gives the same date and "Forge, Penn.", which reads as a slip for Old Forge.', '',
       '## Note added to an office holding', '']
md += [f'- #{k}: {v}' for k, v in draft['holdingNotes'].items()]
md += ['', '## Figures read by eye', '',
       '- 1996 Statement of Vote, State Senator, page 16 (scan), 19th District: John Birke (Dem) 97,133, 37.76%; Cathie Wright (Rep-Inc) 160,130, 62.24%; Los Angeles 30,696 and 45,893; Ventura 66,437 and 114,237.',
       '- 1994 Statement of Vote, Lieutenant Governor, page 9 (scan), State Totals: Gray Davis 4,441,429, 52.42%; Cathie Wright 3,412,777, 40.28%.',
       '- 1992 Statement of Vote, State Senator, 19th District: the text layer gives the totals 108,052 and 148,116; its percentages are garbled by OCR ("53.24" is legible for Wright), so the note gives the votes only.', '',
       '## Her Assembly years: no office holding', '',
       'The archive has no office holding for her twelve years in the Assembly (37th District, December 1980 to December 1992). The Record of Members gives her sessions and counties; the district number is from her 1985 resolution (LW3499) and the Los Angeles Times. Whether, and how much of, the valley lay in the 37th under the 1981 lines is not in templates/_data/valley-districts.json, which starts with the 1991 lines. The 1985 newspapers (the Signal and the Los Angeles Times on sg110185) and LW3499 say she represented the valley, and the body says so in their words. No holding is created by this loader.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- The Assembly succession: the infobox gives her successor in the 37th as Nao Takasugi, while the succession box at the foot of the same article gives William J. Knight (Pete Knight, #29314). The two disagree; neither is used, and no relation to Pete Knight is made.',
       '- Ed Davis\'s nickname for her, "The Peroxide Princess of Simi Valley", and his recruiting of Marion W. La Follette against her in the 1992 primary (cited to the California Journal, July 1992, which was not read).',
       '- The 1992 primary shares (Wright 38%, La Follette 33%, Roger Campbell 29%) and the 1980 to 1990 Assembly results table (not checked against the Statements of Vote).',
       '- Her husband\'s name, Victor, is also in the Los Angeles Times; it is left out as private life.',
       '- Her middle initial "M." (Wikipedia and JoinCalifornia, which spells her "Cathi M. Wright").', '',
       '## Left out on purpose', '',
       '- The 1989 Ventura County district attorney\'s inquiry into her intervention over her daughter\'s traffic tickets (Los Angeles Times, 17 April 2012: Bradbury found "no clear-cut criminal violation"; Wikipedia also has it). It concerns a living private person, her daughter, and has no Santa Clarita Valley bearing. Nathan may want it in.',
       '- Doc Rioux\'s column of 24 October 1993 (/oldtownnewhall/rioux/rr102493.htm, the archive\'s article #12844) names "the energy of State Senator Cathie Wright" among the valley\'s remarkable women: opinion, not used.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} sources; written {os.path.relpath(OUT)}.json and .md')
