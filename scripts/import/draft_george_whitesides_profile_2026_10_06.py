#!/usr/bin/env python3
"""George Whitesides #29336: the profile draft (Nathan, 6 October 2026, on the Wikipedia census: "Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent.

Writes inventory/review/george-whitesides-profile-draft-2026-10-06.json (read by
scripts/import/build_george_whitesides_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the Clerk of the House's member page, the
House's own biography (History, Art & Archives), NASA's biography of 2010 (Wayback capture), and Virgin Galactic
Holdings' Form 8-K of 15 July 2020 (SEC EDGAR), all saved in inventory/sources/george-whitesides-2026-10-06/
(manifest.json there); the Statements of Vote of 5 November 2024 and 2 June 2026 from
inventory/sources/legislative-districts-2026-10-04/sos/; the district figures from templates/_data/valley-districts.json.
The check at the foot confirms each quotation against the saved copy and each vote figure against the text of the
saved PDF. No quotation joins two clauses with an ellipsis, and none holds an em dash. The Wikipedia article is a
finding aid only. Living person, in office: public life only.
Run on the host: python3 scripts/import/draft_george_whitesides_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
SLUG = 'george-whitesides'
OUT = os.path.join(ROOT, 'inventory', 'review', SLUG + '-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', SLUG + '-2026-10-06')
SOS = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04', 'sos')

NOTES = {
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, House of Representatives. Under the "2021 lines, drawn by the Citizens Redistricting Commission", in force "January 2023 to January 2027", the 27th District holds "the whole valley": "The whole valley, with Lancaster, Palmdale and part of the City of Los Angeles" (291,244 people at the 2020 Census). Under the "2025 congressional lines, Proposition 50", in force "from 3 January 2027", the 27th holds "88.0%": "The City of Santa Clarita except its eastern and southeastern edge, Stevenson Ranch and Green Valley"; the 26th takes "Castaic, Val Verde and Hasley Canyon" (7.8%) and the 30th "Agua Dulce and the City\'s eastern and southeastern edge, about 8,600 City residents" (4.3%). The file\'s note: "Proposition 50, passed on 4 November 2025 with 64.4 per cent of the vote, replaced the 2021 congressional lines with a map drawn by the Legislature."',
    'HOLD': 'His term is the archive\'s office holding #29384 (House of Representatives, 27th Congressional District, from 3 January 2025, under the 2021 lines). California Secretary of State, Statement of Vote, General Election, 5 November 2024, United States Representative, 27th Congressional District, https://elections.cdn.sos.ca.gov/sov/2024-general/sov/25-us-rep-congress.pdf: George Whitesides (DEM) 154,040 votes, 51.3%; "Mike Garcia*" (REP) 146,050, 48.7%. The asterisk marks the incumbent; Garcia\'s term is office holding #29382.',
    'CLERK': 'Office of the Clerk, U.S. House of Representatives, member page W000830, https://clerk.house.gov/members/W000830 (read 6 October 2026): "California (CA) - 27th, Democrat"; "Hometown: Agua Dulce"; "Oath of Office: Jan. 03, 2025"; committee assignments "Committee on Armed Services" and "Committee on Science, Space, and Technology".',
    'BIO': 'History, Art & Archives, U.S. House of Representatives, "WHITESIDES, George," https://history.house.gov/People/Listing/W/WHITESIDES,-George-(W000830)/ (read 6 October 2026), the entry of the Biographical Directory of the United States Congress: "business executive; non-profit executive"; "executive director, National Space Society, Washington, D.C., 2004-2008"; "staff, transition team for President-elect Barack Obama, 2008-2009"; "chief of staff, National Aeronautical and Space Administration, 2009-2010"; "elected as a Democrat to the One Hundred Nineteenth Congress (January 3, 2025-present)".',
    'NASA': 'NASA, "George T. Whitesides, NASA\'s Chief of Staff," May 2010, https://www.nasa.gov/about/highlights/whitesides_bio.html (read in the Wayback Machine\'s capture of 25 April 2023): "Editor\'s Note: Mr. Whitesides left the agency on May 7, 2010."; "George T. Whitesides serves as NASA Chief of Staff for Administrator Charles Bolden."',
    'SEC': 'Virgin Galactic Holdings, Inc., Form 8-K, filed with the Securities and Exchange Commission on 15 July 2020, Item 5.02, https://www.sec.gov/Archives/edgar/data/1706946/000119312520193434/d46161d8k.htm (read 6 October 2026): "appointed Michael Colglazier as the Company\'s Chief Executive Officer to succeed George Whitesides, who the Board appointed as the Company\'s Chief Space Officer, in each case, effective July 20, 2020."',
    'SOV26': 'California Secretary of State, Statement of Vote, Primary Election, 2 June 2026, United States Representative, 27th Congressional District, https://elections.cdn.sos.ca.gov/sov/2026-primary/sov/76-us-rep.pdf: Jason Gibbs (REP) 62,758 votes, 41.0%; George Whitesides (DEM) 62,214, 40.6%; Roberto Ramos (DEM) 14,868, 9.7%; Caleb Norwood (DEM) 13,293, 8.7%. The valley-districts file: "In the 27th the June primary\'s top two were Jason Gibbs and George Whitesides."',
}

BODY = [
    'George Whitesides has been the Santa Clarita Valley\'s member of Congress since 3 January 2025, for the 27th Congressional District, which under the lines drawn by the Citizens Redistricting Commission in 2021 holds the whole valley with Lancaster, Palmdale and part of the City of Los Angeles.{DIST}{HOLD} The Clerk of the House gives his hometown as Agua Dulce.{CLERK} A Democrat, he won the seat on 5 November 2024 from the Republican incumbent, Mike Garcia, by 154,040 votes to 146,050.{HOLD} In the House he sits on the Committee on Armed Services and the Committee on Science, Space, and Technology.{CLERK}',
    'He came to Congress from the space business. He was executive director of the National Space Society from 2004 to 2008, worked on President-elect Barack Obama\'s transition team, and was chief of staff of NASA under Administrator Charles Bolden until 7 May 2010.{BIO}{NASA} He was then chief executive of Virgin Galactic until July 2020, when its board named Michael Colglazier to succeed him and made Whitesides the company\'s Chief Space Officer.{SEC}',
    'Proposition 50, passed in November 2025, replaced the 2021 congressional lines. From 3 January 2027 the 27th District keeps 88 per cent of the valley\'s people; Castaic, Val Verde and Hasley Canyon go to the 26th District, and Agua Dulce with the eastern and southeastern edge of the City of Santa Clarita to the 30th.{DIST} In the primary of 2 June 2026 for the redrawn 27th, Whitesides finished second to the Republican Jason Gibbs, 62,214 votes to 62,758, and the two go on to the general election of 3 November 2026.{SOV26}',
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
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('–', '-').replace('\xa0', ' ')
    return re.sub(r'\s+', ' ', t)


def pdf(path):
    return re.sub(r'\s+', ' ', subprocess.run(['pdftotext', '-layout', path, '-'], capture_output=True, text=True).stdout)


CHECK = {
    'CLERK': plain(os.path.join(SRC, 'clerk-members-W000830.html')),
    'BIO': plain(os.path.join(SRC, 'house-history-W000830.html')),
    'NASA': plain(os.path.join(SRC, 'wayback-nasa-whitesides-bio-20230425034506.html')),
    'SEC': plain(os.path.join(SRC, 'sec-virgin-galactic-8k-2020-07-15.htm')),
}
vdtext = open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read()
CHECK['DIST'] = vdtext
CHECK['SOV26'] = vdtext
TITLES = ('WHITESIDES, George,', "George T. Whitesides, NASA's Chief of Staff,")
missing = []
for k, text in CHECK.items():
    for q in re.findall(r'"([^"]{12,})"', NOTES[k]):
        q2 = re.sub(r'\s+', ' ', q)
        if q2 in TITLES:
            continue
        if q2.rstrip('.,') not in text and q2 not in text:
            missing.append(f'{k}: {q2[:90]}')
FIGS = [
    (os.path.join(SOS, 'general-election-nov-5-2024', '25-us-rep-congress.pdf'), '27th Congressional District', ['Whitesides', 'Garcia*', '154,040', '146,050', '51.3%', '48.7%']),
    (os.path.join(SOS, 'primary-election-june-2-2026', '76-us-rep.pdf'), '27th Congressional District', ['Gibbs', '62,758', '62,214', '14,868', '13,293', '41.0%', '40.6%', '9.7%', '8.7%']),
]
for path, head, figs in FIGS:
    t = pdf(path)
    i = t.find(head)
    seg = t[i:i + 700] if i >= 0 else ''
    for f in figs:
        if f not in seg:
            missing.append(f'{os.path.basename(path)}: {f} not under {head}')
vd = json.loads(vdtext)
house = vd['bodies']['united-states-house-of-representatives']['plans']
p21 = next(p for p in house if p['plan'] == '2021'); p25 = next(p for p in house if p['plan'] == '2025')
d27 = next(d for d in p25['districts'] if d['n'] == 27)
if not ([d['n'] for d in p21['districts']] == [27] and p21['districts'][0]['pop'] == 291244 and d27['share'] == '88.0%'):
    missing.append('valley-districts.json does not hold the figures the DIST note quotes')
for p in BODY:
    for q in re.findall(r'"([^"]{8,})"', p):
        if not any(q.rstrip('.') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations or figures not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_george_whitesides_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29336, 'title': 'George Whitesides'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'Space executive; congressman',
        'wikidataId': 'Q3511804', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/George_T._Whitesides',
        'personSearchNames': 'George T. Whitesides',
        'relatedPersonsAdd': [29334],
    },
    'holdingNotes': {},
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# George Whitesides #29336: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_george_whitesides_profile_2026_10_06.php) reads the .json beside this file. Living person, in office: public life only; no birth fields are set, though the House biography gives his birth (Massachusetts, 3 March 1974).', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'personSearchNames: "George T. Whitesides" is how NASA\'s 2010 biography names him. relatedPersons gains Mike Garcia (#29334), the incumbent he defeated in 2024. Wikidata Q3511804 read on 6 October 2026 (saved as wikidata-Q3511804.json): label "George T. Whitesides", English Wikipedia sitelink "George T. Whitesides". Both are finding aids, not sources.', '',
       'No note is added to his office holding #29384: it is open (serving), and Mike Garcia\'s holding #29382 already says he lost the 2024 election to Whitesides.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- His wildfire work: that in his first week he returned to help with outreach after the Hurst Fire, that he co-founded a wildfire prevention organisation, and that he co-sponsored the Fix Our Forests Act. Cited there to the Los Angeles Times, not read here. A lead worth reading for the valley.',
       '- The Republican attack advertisements of 2024 about Equality California and Senate Bill 145 (cited to the Los Angeles Times, not read here); the 2024 primary figures (74,245 for Garcia, 44,391 for Whitesides, 16,525 for Steve Hill; the March 2024 Statement of Vote was not read).',
       '- That Virgin Galactic operated at the Mojave Air and Space Port under him; that he co-founded Yuri\'s Night (NASA\'s biography does say this) and the AstroAccess flight of 2022; his vice-ranking place on the Science Committee, his caucuses, the Begich bill.',
       '- Personal life (marriage to Loretta Hidalgo Whitesides, children, religion, his father the chemist George M. Whitesides, his great-grandfather James Breasted). Living person: left out.', '',
       '## What the archive holds on him', '',
       'The Reggie mirror of scvhistory.com was searched on 6 October 2026 for "Whitesides": no hits (the mirror predates his candidacy). In Craft his only incoming relation is his office holding #29384. The Signal\'s coverage of him since 2023 was not searched (no web search was available to this pass); it is the natural next source for his work in the valley.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources and {len(FIGS)} Statements of Vote; written {os.path.relpath(OUT, ROOT)}.json and .md')
