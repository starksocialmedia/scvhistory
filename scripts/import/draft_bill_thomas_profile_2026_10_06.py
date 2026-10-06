#!/usr/bin/env python3
"""Bill Thomas #29464: the profile draft (Nathan, 6 October 2026, on the Wikipedia census: "Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent.

Writes inventory/review/bill-thomas-profile-draft-2026-10-06.json (read by
scripts/import/build_bill_thomas_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the House's own biography (History, Art &
Archives, which prints the Biographical Directory entry) and the Wayback capture of the older Directory page, both
saved in inventory/sources/bill-thomas-2026-10-06/ (manifest.json there); the Statements of Vote of 2002, 2004 and
2006 from inventory/sources/legislative-districts-2026-10-04/sos/; the district figures from
templates/_data/valley-districts.json. The check at the foot confirms each quotation against the saved copy and each
vote figure against the text of the saved PDF. No quotation joins two clauses with an ellipsis, and none holds an em
dash. The Wikipedia article is a finding aid only. Living person: public life only.
Run on the host: python3 scripts/import/draft_bill_thomas_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
SLUG = 'bill-thomas'
OUT = os.path.join(ROOT, 'inventory', 'review', SLUG + '-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', SLUG + '-2026-10-06')
SOS = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04', 'sos')

NOTES = {
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, House of Representatives. Under the "2001 lines, drawn by the Legislature", in force "January 2003 to January 2013", the 25th District held "99.6%" of the valley, "All the valley but Green Valley", and the 22nd held "0.4%": "Green Valley only, about 1,000 people" (1,065 at the 2010 Census). Under the "1991 lines, drawn by the court\'s Special Masters", in force "January 1993 to January 2003", the 25th held "the whole valley". The shares are counted from census blocks assigned to districts in the Statewide Database\'s block files.',
    'HOLD': 'His term for the valley is the archive\'s office holding #29523 (House of Representatives, 22nd Congressional District, 3 January 2003 to 3 January 2007, under the 2001 lines). California Secretary of State, Statements of Vote, United States Representative, 22nd Congressional District: 5 November 2002, "Bill Thomas*" 120,473 votes, 73.4%, to Jaime A. Corvera 38,988, 23.7%, and Frank Coates 4,824 (in the district\'s Los Angeles County part, 8,960 to Corvera\'s 3,811), https://elections.cdn.sos.ca.gov/sov/2002-general/congress.pdf; 2 November 2004, unopposed, 209,384 votes (Los Angeles County part 18,094), https://elections.cdn.sos.ca.gov/sov/2004-general/us-reps-all-formatted.pdf. In both years most of the district\'s votes were cast in Kern County (85,662 of 120,473 for him in 2002). The Los Angeles County part of the district was larger than Green Valley alone; the Statements of Vote do not count Green Valley separately.',
    'BIO': 'History, Art & Archives, U.S. House of Representatives, "THOMAS, William Marshall," https://history.house.gov/People/Listing/T/THOMAS,-William-Marshall-(T000188)/ (read 6 October 2026), the entry of the Biographical Directory of the United States Congress: "faculty, Bakersfield Community College, Bakersfield, Calif., 1965-1974"; "member of the California state assembly, 1974-1978"; "elected as a Republican to the Ninety-sixth and to the thirteen succeeding Congresses (January 3, 1979-January 3, 2007)"; "chair, Committee on House Oversight (One Hundred Fourth and One Hundred Fifth Congresses)"; "chair, Committee on House Administration (One Hundred Sixth Congress)"; "chair, Committee on Ways and Means (One Hundred Seventh through One Hundred Ninth Congresses)"; "chair, Joint Committee on Taxation (One Hundred Seventh through One Hundred Ninth Congresses)"; "not a candidate for reelection to the One Hundred Tenth Congress in 2006." The older Directory page (Wayback Machine capture of 26 October 2019, http://bioguide.congress.gov/scripts/biodisplay.pl?index=T000188) gives the same lines.',
    'MCC': 'Kevin McCarthy\'s office holding #29525 (22nd Congressional District, from 3 January 2007). California Secretary of State, Statement of Vote, General Election, 7 November 2006, 22nd Congressional District, https://elections.cdn.sos.ca.gov/sov/2006-general/congress.pdf: Kevin McCarthy 133,278 votes, 70.8%; Thomas was not on the ballot. History, Art & Archives, "MCCARTHY, Kevin," https://history.house.gov/People/Listing/M/MCCARTHY,-Kevin-(M001165)/: "staff, United States Representative William Thomas of California, 1987-2002".',
}

BODY = [
    'Bill Thomas was the member of Congress for a small corner of the Santa Clarita Valley from January 2003 to January 2007. Under the district lines drawn in 2001, the valley lay almost wholly in the 25th Congressional District; Thomas\'s 22nd District, most of whose voters were in Kern County, took in only Green Valley, about 1,000 people and 0.4 per cent of the valley by the archive\'s count from the 2010 census.{DIST}{HOLD} He won the 22nd in November 2002 with 73.4 per cent of the vote and was re-elected unopposed in 2004.{HOLD}',
    'By then he had been in the House since January 1979. William Marshall Thomas taught at Bakersfield Community College from 1965 to 1974 and sat in the State Assembly from 1974 to 1978 before he was elected to Congress as a Republican. In the House he chaired the Committee on House Oversight in the 104th and 105th Congresses and the Committee on House Administration in the 106th, and from 2001 to 2007, through the 107th, 108th and 109th Congresses, the Committee on Ways and Means and the Joint Committee on Taxation.{BIO}',
    'He did not run again in 2006. Kevin McCarthy, who had worked on his staff from 1987 to 2002, won the 22nd District that November and succeeded him in January 2007.{BIO}{MCC}',
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
    'BIO': plain(os.path.join(SRC, 'house-history-T000188.html')),
    'MCC': plain(os.path.join(ROOT, 'inventory', 'sources', 'kevin-mccarthy-2026-10-06', 'house-history-M001165.html')),
}
OLD = plain(os.path.join(SRC, 'wayback-bioguide-T000188-20191026111541.html'), 'latin-1')
TITLES = ('THOMAS, William Marshall,', 'MCCARTHY, Kevin,')
missing = []
for k, text in CHECK.items():
    for q in re.findall(r'"([^"]{12,})"', NOTES[k]):
        q2 = re.sub(r'\s+', ' ', q)
        if q2 in TITLES:
            continue
        if q2.rstrip('.,') not in text:
            missing.append(f'{k}: {q2[:90]}')
# the older Directory page carries the same lines (the House History page adds the joint committees)
for q in ('faculty, Bakersfield Community College, Bakersfield, Calif., 1965-1974', 'member of the California state assembly, 1974-1978', 'not a candidate for reelection to the One Hundred Tenth Congress in 2006'):
    if q not in OLD:
        missing.append(f'old Directory page: {q}')
FIGS = [
    (os.path.join(SOS, 'general-election-november-5-2002', 'congress.pdf'), '22nd Congressional District', ['Bill Thomas*', '38,988', '120,473', '4,824', '23.7%', '73.4%', '8,960', '3,811', '85,662']),
    (os.path.join(SOS, 'presidential-general-election-november-2-2004', 'us-reps-all-formatted.pdf'), '22nd Congressional District', ['209,384', '18,094', '100.0%']),
    (os.path.join(SOS, 'general-election-november-7-2006', 'congress.pdf'), '22nd Congressional District', ['133,278', '70.8%']),
]
for path, head, figs in FIGS:
    t = pdf(path)
    i = t.find(head)
    seg = t[i:i + 700] if i >= 0 else ''
    for f in figs:
        if f not in seg:
            missing.append(f'{os.path.basename(path)}: {f} not under {head}')
    if 'general-election-november-7-2006' in path and 'Thomas' in seg[:400]:
        missing.append('2006: Thomas appears on the 22nd District ballot')
vd = json.load(open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json')))
house = vd['bodies']['united-states-house-of-representatives']['plans']
p91 = next(p for p in house if p['plan'] == '1991'); p01 = next(p for p in house if p['plan'] == '2001')
d22 = next(d for d in p01['districts'] if d['n'] == 22); d25 = next(d for d in p01['districts'] if d['n'] == 25)
if not (d22['share'] == '0.4%' and d22['pop'] == 1065 and d25['share'] == '99.6%' and [d['n'] for d in p91['districts']] == [25] and p91['years'] == 'January 1993 to January 2003'):
    missing.append('valley-districts.json does not hold the figures the DIST note quotes')
for p in BODY:
    for q in re.findall(r'"([^"]{8,})"', p):
        if not any(q.rstrip('.') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations or figures not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_bill_thomas_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29464, 'title': 'Bill Thomas'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'College teacher; congressman',
        'wikidataId': 'Q132302', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Bill_Thomas',
        'personSearchNames': 'William Marshall Thomas\nWilliam M. Thomas',
        'relatedPersonsAdd': [29466],
    },
    'holdingNotes': {
        '29523': 'Not a candidate in 2006 (Biographical Directory of the United States Congress, T000188: "not a candidate for reelection to the One Hundred Tenth Congress in 2006"). Succeeded in the 22nd District by Kevin McCarthy, his former staff member, elected 7 November 2006 with 133,278 votes, 70.8% (Statement of Vote; office holding #29525).',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Bill Thomas #29464: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_bill_thomas_profile_2026_10_06.php) reads the .json beside this file. Living person: public life only; no birth fields are set, though the House biography gives his birth (Wallace, Idaho, 6 December 1941).', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'personSearchNames: "William Marshall Thomas" is the House biography\'s heading; "William M. Thomas" is its short form, added so a search on either finds him. Wikidata Q132302 read on 6 October 2026 (saved as wikidata-Q132302.json): label "Bill Thomas", English Wikipedia sitelink "Bill Thomas". Both are finding aids, not sources.', '',
       '## Note added to an office holding', '']
md += [f'- #{k}: {v}' for k, v in draft['holdingNotes'].items()]
md += ['', '## What rests on Wikipedia alone (not in the body)', '',
       '- His earlier districts: the article gives the 18th (1979 to 1983), the 20th (1983 to 1993) and the 21st (1993 to 2003). Whether the 20th of the 1981 lines held any part of the valley is not established: the archive\'s district count begins with the 1991 lines, under which the whole valley was in the 25th, and the 1992 Statement of Vote shows his 21st in Kern and Tulare counties only. An open question, not a fact for the record.',
       '- His reasons for retiring (the House Republicans\' term limits on chairmen), his later criticism of McCarthy, his years at the American Enterprise Institute and Buchanan, Ingersoll & Rooney, and his appointment to the Kern Community College District board in 2016. Some are cited there to news reports or press releases not read here; none touches the valley.',
       '- The 1992 House banking overdrafts and the 2001 and 2003 controversies the article lists. Not read in their sources; outside the valley.',
       '- Marriage and family ("citation needed" in the article). Living person: left out.', '',
       '## What the archive holds on him', '',
       'The Reggie mirror of scvhistory.com was searched on 6 October 2026 for "Bill Thomas", "Congressman Thomas" and "William M. Thomas": one hit, lw3608.htm, which names a different Bill Thomas, William S. Thomas, Don Ray\'s journalism professor at CSUN. Nothing on the congressman. In Craft his only incoming relation is his office holding #29523.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK) + 1} saved sources and {len(FIGS)} Statements of Vote; written {os.path.relpath(OUT, ROOT)}.json and .md')
