#!/usr/bin/env python3
"""Fran Pavley #29460: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent. She is living: public life only.

Writes inventory/review/fran-pavley-profile-draft-2026-10-06.json (read by
scripts/import/build_fran_pavley_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the mirror pages from the Reggie mirror
(read-only), the outside sources from the copies saved in inventory/sources/fran-pavley-2026-10-06/ (manifest.json
there: her Senate office's own pages in the Wayback Machine's captures of 2014, the chaptered bills from leginfo.ca.gov,
an SCVNews.com page), the Record of Members, the Record of Senators and the Statements of Vote of 2012 and 2016 from
inventory/sources/legislative-districts-2026-10-04/, the district figures from templates/_data/valley-districts.json.
The check at the foot of this file confirms each quotation against the saved copy. No quotation joins two clauses with
an ellipsis, and none quotes a passage that holds an em dash.
Run on the host: python3 scripts/import/draft_fran_pavley_profile_2026_10_06.py
"""
import html, json, os, re, shutil, subprocess

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'fran-pavley-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'fran-pavley-2026-10-06')
LEG = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'SENATE': 'Her term for the valley is the archive\'s office holding #29519 (Senate, 27th District, 3 December 2012 to 5 December 2016, under the 2011 lines). California Secretary of State, Statement of Vote, General Election, November 6, 2012, 27th State Senate District, https://elections.cdn.sos.ca.gov/sov/2012-general/13-state-senators.pdf: Fran Pavley* (DEM) 197,757 votes, 53.6%, to Todd Zink (REP) 171,438, 46.4%; Los Angeles County 136,053 to 92,092, Ventura County 61,704 to 79,346. The asterisk marks an incumbent senator: she already sat in the Senate for another district. Secretary of the Senate, Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88: "Pavley, Fran", D, Los Angeles and Ventura counties, regular sessions 2009 to 2016.',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, State Senate. Under the "2011 lines, drawn by the Citizens Redistricting Commission", in force for the Senate from "December 2012 to December 2024", the 27th held "20.3%" of the valley, 55,075 people at the 2010 Census: "Stevenson Ranch and the City\'s western and southwestern neighborhoods, with eastern Ventura County, Calabasas and Malibu". The rest, 79.7%, was in the 21st. The shares are counted from census blocks assigned to districts in the Statewide Database\'s block files.',
    'GALLERY': 'Office of Senator Fran Pavley, sd27.senate.ca.gov, photograph gallery "Santa Clarita" (one of the district\'s communities in the site\'s menu, with Agoura Hills, Calabasas, Hidden Hills, Malibu, the San Fernando Valley, Topanga, Ventura County and Westlake Village), read in the Wayback Machine\'s captures of 23 July 2014 (page 1) and 7 November 2014 (pages 2 and 3), http://sd27.senate.ca.gov/category/image-galleries/santa-clarita. Captions: "Sen. Pavley spoke about water issues at KHTS AM-1220\'s annual Sacramento Road Trip."; "Sen. Pavley toured the Henry Mayo Newhall Memorial Hospital in Santa Clarita."; "Sen. Pavley recently met with school superintendents in the Santa Clarita Valley."; "I met with student leaders from College of the Canyons and discussed the state budget."; "A visit to Santa Clarita City Hall to meet with city officials Frank Oviedo, Michael Murphy and Ken Striplin."; "Sen. Pavley learns how to use one of the college\'s laser welding workstations, during a recent tour of College of the Canyons."; "Dianne G. Van Hook, President of College of the Canyons and Eric Harnish visited Sen. Pavley in Sacramento."; "Bob Kellar is sworn-in as Mayor of the City of Santa Clarita."',
    'SB380': 'Senate Bill 380 (2015-16), Chapter 14, Statutes of 2016, chaptered text, https://www.leginfo.ca.gov/pub/15-16/bill/sen/sb_0351-0400/sb_380_bill_20160510_chaptered.html: "INTRODUCED BY Senator Pavley"; "(Coauthor: Assembly Member Wilk)"; "APPROVED BY GOVERNOR MAY 10, 2016"; "This bill would require the supervisor to continue the prohibition against Southern California Gas Company injecting any natural gas into the Aliso Canyon natural gas storage facility located in the County of Los Angeles". Press release, "Brown Signs Pavley, Wilk Natural Gas Bill," SCVNews.com, Tuesday, May 10, 2016, https://scvnews.com/brown-signs-pavley-wilk-natural-gas-bill/: "cowritten by Assemblyman Scott Wilk, R-Santa Clarita"; "Before being unanimously approved in the Senate, Assemblyman Wilk presented SB 380 on the floor of the Assembly on April 28, where it passed with overwhelming bipartisan support."',
    'SCVWATER': 'Santa Clarita Valley Water Agency, "SCV Water Plan for Services," January 2018, draft as proposed to LAFCO, page 9 (flipbook page 14), legacy page /scvhistory/scvwa012918c.htm (not yet a record in the archive): "The Settlement Agreement and action to pursue a new district passed 14-1."; "Early on, NCWD and CLWA committed to a principle of meeting with anyone interested in this process and potential outcome."; "The agencies held dozens of briefings with individuals and organizations in the region, including the following"; among them "Office of Senator Fran Pavley". The plan does not date the briefing.',
    'BILLS': 'Chaptered texts from leginfo.ca.gov. Assembly Bill 1493 (2001-02), Chapter 200, Statutes of 2002, https://www.leginfo.ca.gov/pub/01-02/bill/asm/ab_1451-1500/ab_1493_bill_20020722_chaptered.html: "INTRODUCED BY Assembly Member Pavley"; "APPROVED BY GOVERNOR JULY 22, 2002"; "AB 1493, Pavley. Vehicular emissions: greenhouse gases." Assembly Bill 32 (2005-06), Chapter 488, Statutes of 2006, https://www.leginfo.ca.gov/pub/05-06/bill/asm/ab_0001-0050/ab_32_bill_20060927_chaptered.html: "INTRODUCED BY Assembly Members Nunez and Pavley"; "APPROVED BY GOVERNOR SEPTEMBER 27, 2006"; "California Global Warming Solutions Act of 2006". Senate Bill 1168 (2013-14), Chapter 346, Statutes of 2014, https://www.leginfo.ca.gov/pub/13-14/bill/sen/sb_1151-1200/sb_1168_bill_20140916_chaptered.html: "INTRODUCED BY Senator Pavley"; "APPROVED BY GOVERNOR SEPTEMBER 16, 2014"; "This part shall be known, and may be cited, as the"; "Sustainable Groundwater Management Act". Senate Bill 32 (2015-16), Chapter 249, Statutes of 2016, https://www.leginfo.ca.gov/pub/15-16/bill/sen/sb_0001-0050/sb_32_bill_20160908_chaptered.pdf: "SB 32, Pavley."; "Approved by Governor September 8, 2016."; "This bill would require the state board to ensure that statewide greenhouse gas emissions are reduced to 40% below the 1990 level by 2030."',
    'BIO14': 'Office of Senator Fran Pavley, "Biography," http://sd27.senate.ca.gov/biography, read in the Wayback Machine\'s capture of 1 July 2014: "Senator Pavley serves as the Chair of the Senate Natural Resources and Water Committee."; "She also chairs the Select Committee on Climate Change and AB 32 Implementation"; "In 1982, Senator Pavley became the first mayor of the City of Agoura Hills, and served four terms on the city council."; "In 2000 she was elected to the California State Assembly, where she served three terms."; "taught middle school for 28 years and completed her teaching career in Moorpark, California."; "She received her Master\'s Degree in Environmental Planning at CSU Northridge"; "Currently, Senator Pavley represents approximately 931,000 people in the 27th district which includes parts of Los Angeles and Ventura Counties."',
    'ASSEMBLY': 'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79: "Pavley, Fran", D, "Los Angeles", regular sessions 2001 to 2002; "Los Angeles, Ventura", 2003 to 2006.',
    'BOYER': 'Carl Boyer 3rd, Santa Clarita: The Formation and Organization of the Largest Newly Incorporated City in the History of Humankind, second edition (Santa Clarita, 2015), chapter 14, page 206, on the archive page /scvhistory/boyer2015ch14.htm (the book is not yet a record in the archive): "I was appointed by Fran Pavley of Agoura Hills to the Regional Issues Task Force of the Los Angeles Division of the League of California Cities". The passage does not give the year.',
    'STERN': 'Henry Stern\'s office holding #29521 (Senate, 27th District, from 5 December 2016). California Secretary of State, Statement of Vote, General Election, November 8, 2016, 27th State Senate District, https://elections.cdn.sos.ca.gov/sov/2016-general/sov/40-state-senators-formatted.pdf: Henry Stern (DEM) 218,655 votes, 55.9%, to Steve Fazio (REP) 172,827, 44.1%; Pavley was not on the ballot.',
}

BODY = [
    'Fran Pavley was the State Senator for the west side of the Santa Clarita Valley from December 2012 to December 2016, for the 27th District.{SENATE} Under the lines drawn in 2011 by the Citizens Redistricting Commission, the 27th took in Stevenson Ranch and the western and southwestern neighborhoods of the City of Santa Clarita, with eastern Ventura County, Calabasas and Malibu: 20.3 per cent of the valley\'s people by the archive\'s count from the 2010 census.{DIST} She won the seat in November 2012 over Todd Zink, 197,757 votes to 171,438.{SENATE}',
    'Her Senate office counted Santa Clarita among the district\'s communities, and its photographs of 2014 show her touring Henry Mayo Newhall Memorial Hospital and College of the Canyons, meeting the valley\'s school superintendents, visiting City Hall to meet the City\'s officials, talking with College of the Canyons student leaders about the state budget, and speaking about water at KHTS AM-1220\'s annual Sacramento Road Trip.{GALLERY} After the Aliso Canyon gas leak she wrote Senate Bill 380, which kept new gas out of the storage field until its wells were tested; Assemblyman Scott Wilk of Santa Clarita was its coauthor and presented it on the Assembly floor, and Governor Jerry Brown signed it on 10 May 2016.{SB380} When the Castaic Lake Water Agency and the Newhall County Water District set out to form a single water agency for the valley, her office was among those they briefed.{SCVWATER}',
    'Statewide she was best known for climate law. In the Assembly she wrote AB 1493 of 2002, on greenhouse gases from motor vehicles, and with Assembly Member Núñez was an author of AB 32, the California Global Warming Solutions Act of 2006. In the Senate she wrote SB 1168, the Sustainable Groundwater Management Act of 2014, and SB 32 of 2016, which required the state to cut its greenhouse gas emissions to 40 per cent below the 1990 level by 2030.{BILLS} She chaired the Senate Natural Resources and Water Committee and a select committee on climate change and putting AB 32 into effect.{BIO14}',
    'A middle school teacher for 28 years, she became the first mayor of Agoura Hills in 1982 and served four terms on its city council.{BIO14} Carl Boyer wrote in his history of the City of Santa Clarita\'s formation that "Fran Pavley of Agoura Hills" appointed him to the Regional Issues Task Force of the League of California Cities\' Los Angeles Division.{BOYER} She was elected to the Assembly in 2000 and served three terms, and sat in the Senate from 2008 to 2016.{BIO14}{ASSEMBLY}{SENATE} Henry Stern succeeded her in the 27th District in December 2016.{STERN}',
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
    return re.sub(r'\s+', ' ', t.replace('’', "'"))


S = lambda f: os.path.join(SRC, f)
CHECK = {
    'SENATE': [pdf(os.path.join(LEG, 'legislature', 'senate-record-of-senators-1849-2026.pdf')), pdf(os.path.join(LEG, 'sos', 'general-election-november-6-2012', '13-state-senators.pdf'))],
    'ASSEMBLY': [pdf(os.path.join(LEG, 'legislature', 'assembly-record-of-members-1849-2026.pdf'))],
    'DIST': [open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read()],
    'GALLERY': [plain(S('wayback-senate-sd27-gallery-santa-clarita-20140723112410.html')) + plain(S('wayback-senate-sd27-gallery-santa-clarita-page1-20141107153016.html')) + plain(S('wayback-senate-sd27-gallery-santa-clarita-page2-20141107152604.html'))],
    'SB380': [plain(S('leginfo-sb380-2016-chaptered.html'), 'latin-1'), plain(S('scvnews-brown-signs-pavley-wilk-natural-gas-bill.html'))],
    'BILLS': [plain(S('leginfo-ab1493-2002-chaptered.html'), 'latin-1') + plain(S('leginfo-ab32-2006-chaptered.html'), 'latin-1') + plain(S('leginfo-sb1168-2014-chaptered.html'), 'latin-1') + (pdf(S('leginfo-sb32-2016-chaptered.pdf')) or '')],
    'BIO14': [plain(S('wayback-senate-sd27-biography-20140701121457.html'))],
    'STERN': [pdf(os.path.join(LEG, 'sos', 'general-election-november-8-2016', '40-state-senators-formatted.pdf'))],
}
for k, p, enc in [('SCVWATER', os.path.join(MIRROR, 'scvhistory', 'files', 'scvwa012918c', 'files', 'basic-html', 'page14.html'), 'utf-8'),
                  ('BOYER', os.path.join(MIRROR, 'scvhistory', 'files', 'boyer2015ch14', 'files', 'basic-html', 'page7.html'), 'utf-8')]:
    if os.path.exists(p):
        CHECK[k] = [plain(p, enc).replace('â\u0080\u009c', '"').replace('â\u0080\u009d', '"')]
TITLES = ('2011 lines, drawn by the Citizens Redistricting Commission', 'December 2012 to December 2024', 'Brown Signs Pavley, Wilk Natural Gas Bill,', 'SCV Water Plan for Services,')
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
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_fran_pavley_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29460, 'title': 'Fran Pavley'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'Teacher; state assemblywoman and state senator',
        'wikidataId': 'Q5478183', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Fran_Pavley',
        'relatedPersonsAdd': [29462, 335],
    },
    'holdingNotes': {
        '29519': 'Succeeded in the 27th District by Henry Stern, elected on 8 November 2016 with 218,655 votes, 55.9% (Secretary of State, Statement of Vote, 8 November 2016, State Senate, https://elections.cdn.sos.ca.gov/sov/2016-general/sov/40-state-senators-formatted.pdf; office holding #29521). Pavley was not on the ballot.',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Fran Pavley #29460: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_fran_pavley_profile_2026_10_06.php) reads the .json beside this file. She is living: public life only, no birth fields.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'relatedPersons: Henry Stern #29462 (her successor in the 27th) and Scott Thomas Wilk Sr. #335 (coauthor and Assembly floor manager of SB 380).', '',
       '## Note added to an office holding', '']
md += [f'- #{k}: {v}' for k, v in draft['holdingNotes'].items()]
md += ['', '## Checks and readings', '',
       '- "from 2008 to 2016" in the body: the Record of Senators gives regular sessions 2009 to 2016, and a senator elected in November 2008 takes the seat that December. Her district before 2012 (the 23rd, under the 2001 lines) held none of the valley by templates/_data/valley-districts.json, so it is not named.',
       '- The Record of Senators prints her counties as "Los Angleles, Ventura" (sic); the note gives them in plain words rather than quoting the misprint.',
       '- SB 380\'s coauthors also include "Senators Allen, Hertzberg, and Runner"; the bill does not give Runner\'s first name, so no tie to the valley\'s senator of 2016, Sharon Runner (#29324), is drawn.',
       '- The SCV Water plan does not date its briefings; Pavley left office in December 2016, so her office\'s briefing came before then. The body does not date it.',
       '- Her Senate biography of 2014 says she taught "for 28 years"; Wikipedia says 29. The body follows the biography.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- Her birth date (11 November 1948, cited to the Vote Smart home page only) and birthplace, and her full name "Frances J." Not used: living, public life only.',
       '- Her Senate district before 2012, the 23rd (2008 to 2012), and her predecessors (Sheila Kuehl in both houses) and successor in the Assembly (Julia Brownley).',
       '- Her service on the California Coastal Commission (1995 to 2000), the presidency of the Los Angeles County Division of the League of California Cities (1996), and the American Planning Association\'s award of 1997.',
       '- That she was "the mother of California climate change policy" (Joe Mathews, 2016), Governing magazine\'s official of the year, the Rose Garden ceremony (her own 2014 biography also tells this one), her trips to Copenhagen and Paris.',
       '- Her work since 2016: Environmental Policy Director of the USC Schwarzenegger Institute, the Wildlife Conservation Board, the Santa Monica Mountains Conservancy board, the wildlife crossing over the 101 (marked "citation needed" in the article).',
       '- That the 27th took in "portions of the San Fernando and Santa Clarita Valleys": consistent with the archive\'s count, which the body uses instead.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} sources; written {os.path.relpath(OUT)}.json and .md')
