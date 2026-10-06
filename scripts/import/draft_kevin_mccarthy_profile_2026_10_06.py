#!/usr/bin/env python3
"""Kevin McCarthy #29466: the profile draft (Nathan, 6 October 2026, on the Wikipedia census: "Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent.

Writes inventory/review/kevin-mccarthy-profile-draft-2026-10-06.json (read by
scripts/import/build_kevin_mccarthy_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the House's own biography (History, Art &
Archives, which prints the Biographical Directory entry) and the Wayback capture of the older Biographical Directory
page, the Clerk's roll call 519 of 2023, all saved in inventory/sources/kevin-mccarthy-2026-10-06/ (manifest.json
there); the Statements of Vote of 2006, 2008 and 2010 from inventory/sources/legislative-districts-2026-10-04/sos/;
the district figures from templates/_data/valley-districts.json. The check at the foot confirms each quotation against
the saved copy and each vote figure against the text of the saved PDF (pdftotext -layout). No quotation joins two
clauses with an ellipsis, and none holds an em dash. The Wikipedia article is a finding aid only.
Living person: public life only.
Run on the host: python3 scripts/import/draft_kevin_mccarthy_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
SLUG = 'kevin-mccarthy'
OUT = os.path.join(ROOT, 'inventory', 'review', SLUG + '-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', SLUG + '-2026-10-06')
SOS = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04', 'sos')

NOTES = {
    'HOLD': 'His term for the valley is the archive\'s office holding #29525 (House of Representatives, 22nd Congressional District, 3 January 2007 to 3 January 2013, under the 2001 lines). California Secretary of State, Statements of Vote, United States Representative, 22nd Congressional District: 7 November 2006, Kevin McCarthy 133,278 votes, 70.8%, to Sharon M. Beery 55,226, 29.2% (in the district\'s Los Angeles County part, 10,091 to 5,924), https://elections.cdn.sos.ca.gov/sov/2006-general/congress.pdf; 4 November 2008, unopposed, 224,549 votes (Los Angeles County part 18,836), https://elections.cdn.sos.ca.gov/sov/2008-general/23_34_us_reps.pdf; 2 November 2010, 173,490 votes, 98.8%, to John Uebersax, a write-in candidate, 2,173 (Los Angeles County part 14,953), https://elections.cdn.sos.ca.gov/sov/2010-general/58-united-states-representative.pdf. In each year most of the district\'s votes were cast in Kern County (94,160 of 133,278 for him in 2006). The Los Angeles County part of the district was larger than Green Valley alone; the Statements of Vote do not count Green Valley separately.',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, House of Representatives. Under the "2001 lines, drawn by the Legislature", in force "January 2003 to January 2013", the 25th District held "99.6%" of the valley, "All the valley but Green Valley", and the 22nd held "0.4%": "Green Valley only, about 1,000 people" (1,065 at the 2010 Census). Under the "2011 lines, drawn by the Citizens Redistricting Commission", in force "January 2013 to January 2023", the 25th held "the whole valley". The shares are counted from census blocks assigned to districts in the Statewide Database\'s block files.',
    'BIO': 'History, Art & Archives, U.S. House of Representatives, "MCCARTHY, Kevin," https://history.house.gov/People/Listing/M/MCCARTHY,-Kevin-(M001165)/ (read 6 October 2026), the entry of the Biographical Directory of the United States Congress: "staff, United States Representative William Thomas of California, 1987-2002"; "member of the California state assembly, 2002-2007, minority leader, 2004-2006"; "elected as a Republican to the One Hundred Tenth and to the eight succeeding Congresses, and served until his resignation on December 31, 2023 (January 3, 2007-December 31, 2023)"; "majority whip (One Hundred Twelfth and One Hundred Thirteenth Congresses)"; "majority leader (One Hundred Thirteenth through One Hundred Fifteenth Congresses)"; "Speaker of the House (One Hundred Eighteenth Congress)". The older Directory page (Wayback Machine capture of 17 December 2019, http://bioguide.congress.gov/scripts/biodisplay.pl?index=M001165) gives the same staff and Assembly lines.',
    'THOMAS': 'Bill Thomas\'s office holding #29523 (22nd Congressional District, 3 January 2003 to 3 January 2007). Biographical Directory of the United States Congress, "THOMAS, William Marshall" (Wayback Machine capture of 26 October 2019, http://bioguide.congress.gov/scripts/biodisplay.pl?index=T000188): "not a candidate for reelection to the One Hundred Tenth Congress in 2006."',
    'ROLL': 'Office of the Clerk, U.S. House of Representatives, Roll Call 519, 118th Congress, 1st Session, https://clerk.house.gov/Votes/2023519 (read 6 October 2026): "Bill Number: H. Res. 757"; "Oct 03, 2023"; "On Agreeing to the Resolution Declaring the office of Speaker of the House of Representatives to be vacant."; "yea: 216 nay: 210".',
}

BODY = [
    'Kevin McCarthy was the member of Congress for a small corner of the Santa Clarita Valley from January 2007 to January 2013. Under the district lines drawn in 2001, the valley lay almost wholly in the 25th Congressional District; McCarthy\'s 22nd District, most of whose voters were in Kern County, took in only Green Valley, about 1,000 people and 0.4 per cent of the valley by the archive\'s count from the 2010 census.{DIST}{HOLD} He won the seat in November 2006, when Bill Thomas, who had held it under the same lines since January 2003, did not run again, and he was re-elected in 2008 and 2010.{HOLD}{THOMAS} He had worked on Thomas\'s staff from 1987 to 2002, and served in the State Assembly from 2002 to 2007, as its Republican minority leader from 2004 to 2006.{BIO}',
    'The lines drawn by the Citizens Redistricting Commission in 2011, first used in the election of 2012, put the whole valley in the 25th District, and McCarthy\'s district held none of it after January 2013.{DIST} He stayed in the House, where he was majority whip, majority leader, minority leader and, in the 118th Congress, Speaker of the House. On 3 October 2023 the House voted, 216 to 210, to declare the office of Speaker vacant, and he resigned his seat on 31 December 2023.{BIO}{ROLL}',
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
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"')
    return re.sub(r'\s+', ' ', t)


def pdf(path):
    return re.sub(r'\s+', ' ', subprocess.run(['pdftotext', '-layout', path, '-'], capture_output=True, text=True).stdout)


CHECK = {
    'BIO': plain(os.path.join(SRC, 'house-history-M001165.html')) + plain(os.path.join(SRC, 'wayback-bioguide-M001165-20191217051939.html'), 'latin-1'),
    'THOMAS': plain(os.path.join(ROOT, 'inventory', 'sources', 'bill-thomas-2026-10-06', 'wayback-bioguide-T000188-20191026111541.html'), 'latin-1'),
    'ROLL': plain(os.path.join(SRC, 'clerk-roll-2023-519.html')),
}
TITLES = ('MCCARTHY, Kevin,', 'THOMAS, William Marshall')
missing = []
for k, text in CHECK.items():
    for q in re.findall(r'"([^"]{12,})"', NOTES[k]):
        q2 = re.sub(r'\s+', ' ', q)
        if q2 in TITLES:
            continue
        if q2.rstrip('.,') not in text:
            missing.append(f'{k}: {q2[:90]}')
# vote figures, read from the saved Statements of Vote
FIGS = [
    (os.path.join(SOS, 'general-election-november-7-2006', 'congress.pdf'), '22nd Congressional District', ['55,226', '133,278', '29.2%', '70.8%', '10,091', '5,924', '94,160']),
    (os.path.join(SOS, 'presidential-general-election-november-4-2008', '23_34_us_reps.pdf'), '22nd Congressional District', ['224,549', '18,836', '100.0%']),
    (os.path.join(SOS, 'general-election-november-2-2010', '58-united-states-representative.pdf'), '22nd Congressional District', ['173,490', '2,173', '98.8%', '14,953', 'Uebersax']),
]
for path, head, figs in FIGS:
    t = pdf(path)
    i = t.find(head)
    seg = t[i:i + 700] if i >= 0 else ''
    for f in figs:
        if f not in seg:
            missing.append(f'{os.path.basename(path)}: {f} not under {head}')
vd = json.load(open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json')))
house = vd['bodies']['united-states-house-of-representatives']['plans']
p01 = next(p for p in house if p['plan'] == '2001'); p11 = next(p for p in house if p['plan'] == '2011')
d22 = next(d for d in p01['districts'] if d['n'] == 22); d25 = next(d for d in p01['districts'] if d['n'] == 25)
if not (d22['share'] == '0.4%' and d22['pop'] == 1065 and d22['covers'] == 'Green Valley only, about 1,000 people' and d25['share'] == '99.6%' and p01['years'] == 'January 2003 to January 2013'
        and [d['n'] for d in p11['districts']] == [25] and p11['districts'][0]['share'] == 'the whole valley'):
    missing.append('valley-districts.json does not hold the figures the DIST note quotes')
for p in BODY:
    for q in re.findall(r'"([^"]{8,})"', p):
        if not any(q.rstrip('.') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations or figures not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_kevin_mccarthy_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29466, 'title': 'Kevin McCarthy'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'Congressman; Speaker of the House',
        'wikidataId': 'Q766866', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Kevin_McCarthy',
        'relatedPersonsAdd': [29464],
    },
    'holdingNotes': {
        '29525': 'Succeeded Bill Thomas, who was "not a candidate for reelection to the One Hundred Tenth Congress in 2006" (Biographical Directory of the United States Congress, T000188; office holding #29523). Under the 2001 lines the 22nd District held only Green Valley of the Santa Clarita Valley, 0.4% of its people (templates/_data/valley-districts.json); from January 2013 the whole valley was in the 25th District.',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Kevin McCarthy #29466: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_kevin_mccarthy_profile_2026_10_06.php) reads the .json beside this file. Living person: public life only; no birth fields are set.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'Wikidata Q766866 was read on 6 October 2026 (wbgetentities, saved as wikidata-Q766866.json): label "Kevin McCarthy", English Wikipedia sitelink "Kevin McCarthy". Both the Wikidata id and the Wikipedia link are finding aids, not sources.', '',
       '## Note added to an office holding', '']
md += [f'- #{k}: {v}' for k, v in draft['holdingNotes'].items()]
md += ['', '## What rests on Wikipedia alone (not in the body)', '',
       '- Nothing in the article ties him to the Santa Clarita Valley beyond the district; the article never names the valley, Santa Clarita or Green Valley.',
       '- The fifteen ballots of his election as Speaker in January 2023 and the date he took office, 7 January 2023; the 2023 debt-ceiling settlement; his 2015 withdrawal from the race for Speaker. Each is cited there to news reports, not read here; the body says only "Speaker of the House (One Hundred Eighteenth Congress)", from the House\'s own biography.',
       '- His Assembly district (the 32nd) and his Young Republican offices. The House biography gives the Assembly years and the minority leadership only.',
       '- Personal life (family, religion). Living person: left out.', '',
       '## What the archive holds on him', '',
       'The Reggie mirror of scvhistory.com was searched on 6 October 2026 for "McCarthy" (every .htm and .html file): every hit is another McCarthy (James McCarthy of the Friends of Mentryville, Karl McCarthy at an auction, John P. McCarthy the film director, the McCarthy ranch near Acton, school yearbooks). Nothing on Kevin McCarthy. In Craft his only incoming relation is his office holding #29525.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources and {len(FIGS)} Statements of Vote; written {os.path.relpath(OUT, ROOT)}.json and .md')
