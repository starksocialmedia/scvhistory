#!/usr/bin/env python3
"""Henry Stern #29462: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent. He is living: public life only.

Writes inventory/review/henry-stern-profile-draft-2026-10-06.json (read by
scripts/import/build_henry_stern_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the mirror pages from the Reggie mirror
(read-only), the outside sources from the copies saved in inventory/sources/henry-stern-2026-10-06/ (manifest.json
there: his Senate office's biography page as it stood on 6 October 2026, the Signal's story of 3 July 2019 as carried
by SCVNews.com), the Record of Senators and the Statements of Vote of 2016 and 2020 from
inventory/sources/legislative-districts-2026-10-04/, the district figures from templates/_data/valley-districts.json.
The check at the foot of this file confirms each quotation against the saved copy. No quotation joins two clauses with
an ellipsis, and none quotes a passage that holds an em dash.
Run on the host: python3 scripts/import/draft_henry_stern_profile_2026_10_06.py
"""
import html, json, os, re, shutil, subprocess

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'henry-stern-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'henry-stern-2026-10-06')
LEG = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'SENATE': 'His term for the valley is the archive\'s office holding #29521 (Senate, 27th District, 5 December 2016 to 2 December 2024, under the 2011 lines). California Secretary of State, Statements of Vote, 27th State Senate District: 8 November 2016, Henry Stern (DEM) 218,655 votes, 55.9%, to Steve Fazio (REP) 172,827, 44.1%, https://elections.cdn.sos.ca.gov/sov/2016-general/sov/40-state-senators-formatted.pdf; 3 November 2020, Henry Stern* (DEM) 284,797, 60.2%, to Houman Salem (REP) 188,421, 39.8%, https://elections.cdn.sos.ca.gov/sov/2020-general/sov/36-state-senate.pdf. Secretary of the Senate, Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88: "Stern, Henry I.", D, "Los Angeles, Ventura", regular sessions 2017 to 2026.',
    'PAVLEY': 'Fran Pavley\'s office holding #29519 (Senate, 27th District, 3 December 2012 to 5 December 2016). She was not on the ballot in the 27th in November 2016 (Statement of Vote, as in the note above).',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, State Senate. Under the "2011 lines, drawn by the Citizens Redistricting Commission", in force for the Senate from "December 2012 to December 2024", the 27th held "20.3%" of the valley, 55,075 people at the 2010 Census: "Stevenson Ranch and the City\'s western and southwestern neighborhoods, with eastern Ventura County, Calabasas and Malibu". The rest, 79.7%, was in the 21st. Under the "2021 lines, drawn by the Citizens Redistricting Commission", in force for the Senate from "December 2024 to now", the 23rd held "the whole valley". The shares are counted from census blocks assigned to districts in the Statewide Database\'s block files.',
    'SIGNAL19': 'Brennon Dixson, "Newsom Signs SB 630, Stern\'s Human Trafficking Bill," The Signal, as carried by SCVNews.com, Wednesday, July 3, 2019, https://scvnews.com/newsom-signs-sb-630-sterns-human-trafficking-bill/: "Stern, whose district includes some western portions of the Santa Clarita Valley"; "SB 630 was authored by Henry Stern, D-Canoga Park"; "Gov. Gavin Newsom signed Senate Bill 630 Wednesday"; "make it clear that current laws do not prevent a local governing body from acting to prevent slavery or human trafficking"; "he authored SB 225 in 2017, which required the California Department of Justice to revise its model human trafficking notice to include the option of texting"; "empowering local governments to enact tailored ordinances ensuring compliance with posting requirements".',
    'COC18': 'College of the Canyons, 2017-18 Annual Report, page 6, "Community Connections," on the archive page /scvhistory/cocannualreport2018.htm (not yet a record in the archive). The page is set in columns, which the text layer interleaves; its pieces read: "STUDENTS MEET SENATOR STERN"; "The college welcomed Sena-"; "tor Henry Stern to the Valencia campus, where he toured the"; "welding, nursing and media"; "entertainment arts depart-"; "ments, and met with Civic"; "Engagement students."',
    'SCVWATER': 'Santa Clarita Valley Water Agency, "SCV Water Plan for Services," January 2018, draft as proposed to LAFCO, page 9 (flipbook page 14), legacy page /scvhistory/scvwa012918c.htm (not yet a record in the archive): "The Settlement Agreement and action to pursue a new district passed 14-1."; "Early on, NCWD and CLWA committed to a principle of meeting with anyone interested in this process and potential outcome."; "The agencies held dozens of briefings with individuals and organizations in the region, including the following"; among them "Office of Senator Henry Stern" and "Office of Senator Fran Pavley".',
    'BIO': 'Office of Senator Henry Stern, "Biography," https://sd27.senate.ca.gov/biography, as it stood on 6 October 2026: "First elected in November 2016"; "Most recently, Senator Stern authored SB 261 and co-authored SB 253, landmark climate laws that establish the nation\'s first requirements for large corporations to publicly disclose their greenhouse gas emissions, carbon embedded in supply chains, and climate risks."; "After the devastating 2018 Woolsey fire destroyed his home, Senator Stern authored the Wildfire Resilience through Community and Ecology Act (2021)."; "From 2018 to 2022, as Chair of the Senate Natural Resources & Water Committee"; "He led efforts to close down the Aliso Canyon Natural Gas Storage Facility, site of the largest recorded methane leak"; "A former educator and environmental attorney"; "received his undergraduate degree from Harvard University, and earned his law degree at UC Berkeley"; "Raised in Malibu, California".',
}

BODY = [
    'Henry Stern was the State Senator for the west side of the Santa Clarita Valley from December 2016 to December 2024, for the 27th District, succeeding Fran Pavley.{SENATE}{PAVLEY} Under the lines drawn in 2011 by the Citizens Redistricting Commission, the 27th took in Stevenson Ranch and the western and southwestern neighborhoods of the City of Santa Clarita, with eastern Ventura County, Calabasas and Malibu: 20.3 per cent of the valley\'s people by the archive\'s count from the 2010 census.{DIST} The Signal described his district in 2019 as including "some western portions of the Santa Clarita Valley."{SIGNAL19} He won the seat in November 2016 over Steve Fazio, 218,655 votes to 172,827, and kept it in 2020 over Houman Salem, 284,797 to 188,421.{SENATE} Under the lines drawn in 2021, the whole valley passed to the 23rd District in December 2024; Stern stayed in the Senate for a 27th District that held none of it.{DIST}{SENATE}',
    'College of the Canyons welcomed him to its Valencia campus, where he toured the welding, nursing and media entertainment arts departments and met students of its civic engagement program; the college reported the visit in its annual report for 2017-18.{COC18} When the Castaic Lake Water Agency and the Newhall County Water District set out to form a single water agency for the valley, his office was among those they briefed.{SCVWATER}',
    'In Sacramento he wrote SB 225 of 2017, which had the state Department of Justice add a texting option to its model notice on human trafficking, and SB 630, signed by Governor Gavin Newsom in July 2019, which made clear that local governments may act to enforce the posting of those notices.{SIGNAL19} After the Woolsey fire of 2018 destroyed his home, he wrote the Wildfire Resilience through Community and Ecology Act of 2021. He chaired the Senate Natural Resources and Water Committee from 2018 to 2022, pressed for the closing of the Aliso Canyon natural gas storage field, and wrote SB 261 and coauthored SB 253, which require large companies to disclose their greenhouse gas emissions and climate risks.{BIO}',
    'A former educator and environmental attorney, Stern was raised in Malibu, graduated from Harvard University and took his law degree at UC Berkeley.{BIO} The Signal gave his home as Canoga Park in 2019.{SIGNAL19}',
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
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('\u0092', "'").replace('﻿', '')
    return re.sub(r'\s+', ' ', t)


def pdf(path):
    if not shutil.which('pdftotext'):
        return None
    t = subprocess.run(['pdftotext', '-layout', path, '-'], capture_output=True, text=True).stdout
    return re.sub(r'\s+', ' ', t)


S = lambda f: os.path.join(SRC, f)
CHECK = {
    'SENATE': [pdf(os.path.join(LEG, 'legislature', 'senate-record-of-senators-1849-2026.pdf')), pdf(os.path.join(LEG, 'sos', 'general-election-november-8-2016', '40-state-senators-formatted.pdf')), pdf(os.path.join(LEG, 'sos', 'general-election-november-3-2020', '36-state-senate.pdf'))],
    'PAVLEY': [pdf(os.path.join(LEG, 'sos', 'general-election-november-8-2016', '40-state-senators-formatted.pdf'))],
    'DIST': [open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read()],
    'SIGNAL19': [plain(S('scvnews-signal-2019-07-03-sb630.html'))],
    'BIO': [plain(S('senate-sd27-biography.html'))],
}
for k, p in [('COC18', os.path.join(MIRROR, 'scvhistory', 'files', 'cocannualreport2018', 'files', 'basic-html', 'page6.html')),
             ('SCVWATER', os.path.join(MIRROR, 'scvhistory', 'files', 'scvwa012918c', 'files', 'basic-html', 'page14.html'))]:
    if os.path.exists(p):
        CHECK[k] = [plain(p)]
TITLES = ('2011 lines, drawn by the Citizens Redistricting Commission', 'December 2012 to December 2024', '2021 lines, drawn by the Citizens Redistricting Commission', 'December 2024 to now',
          'Newsom Signs SB 630, Stern\'s Human Trafficking Bill,', 'SCV Water Plan for Services,', 'Community Connections,')
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
        if not any(q2.rstrip('.,') in t for t in texts):
            missing.append(f'{k}: {q2[:90]}')
for k in set(NOTES) - set(CHECK):
    missing.append(f'{k}: no saved source to check against')
for p in BODY:
    for q in [x for x in re.findall(r'"([^"]*)"', p) if len(x) >= 8]:
        if not any(q.rstrip('.') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_henry_stern_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29462, 'title': 'Henry Stern'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'Environmental attorney; state senator',
        'wikidataId': 'Q27967376', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Henry_Stern_(California_politician)',
        'relatedPersonsAdd': [29460],
    },
    'holdingNotes': {},
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Henry Stern #29462: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_henry_stern_profile_2026_10_06.php) reads the .json beside this file. He is living: public life only, no birth fields.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'relatedPersons: Fran Pavley #29460, his predecessor in the 27th (her own loader adds him in turn). No note is added to his office holding: the succession note sits on Pavley\'s.', '',
       '## For Nathan', '',
       '- His holding #29521 ends on 2 December 2024 with howEnded "reelected". The date is right for the valley (the 2021 lines moved the whole valley into the 23rd that day), but he was not re-elected to a seat that held any of the valley: he was re-elected in November 2024 for a 27th District under the 2021 lines that held none of it (the Record of Senators runs his sessions to 2026). The loader does not touch the holding; "redistricted" or a note may suit it better.',
       '- His Senate biography is the page as it stood on 6 October 2026, which describes his whole career to date; the earlier versions of the page were not read.',
       '- His Senate site\'s press releases (sd27.senate.ca.gov/press-releases, 299 titles read) and its Wayback captures (6,634 addresses, 2017 to 2024) were searched for the valley\'s place names: no release names the valley. The Signal\'s own site refused automated reading (403), so its stories are read only where SCVNews.com carried them.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- His birth date (12 April 1982), birthplace and full name Henry Isaac Stern, his parents and the Wonder Years anecdote: living, public life only, and not used.',
       '- That he was an environmental attorney and senior adviser to Fran Pavley before his election, counsel to Representative Henry Waxman on the House Energy and Commerce Committee, a law lecturer at UCLA and UC Berkeley, and "the first millennial elected to the California State Senate".',
       '- His candidacy for the Third District of the Los Angeles County Board of Supervisors in 2022 (24 per cent in the June primary, by the article; not checked against the county\'s results).',
       '- His endorsements in 2016, among them, by the article, "the Ventura County Star and Ventura County Signal".', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} sources; written {os.path.relpath(OUT)}.json and .md')
