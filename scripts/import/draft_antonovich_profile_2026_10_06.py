#!/usr/bin/env python3
"""Michael D. Antonovich #29284: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and
build profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude
Code, research subagent. He is living: public life only, no birth fields.

Writes inventory/review/michael-d-antonovich-profile-draft-2026-10-06.json (read by
scripts/import/build_antonovich_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the archive's own pages from the Reggie mirror
(read-only, latin-1); the Newsmaker of the Week of 28 September 2003 (not in the mirror) from the Wayback Machine's
capture of scvhistory.com; his official biography and the district page of his county website, and the South Coast Air
Quality Management District's biography, from the Wayback Machine (copies in
inventory/sources/michael-d-antonovich-2026-10-06/, manifest.json there); the Record of Members of the Assembly from
inventory/sources/audra-strickland-2026-10-06/. The check at the foot confirms each quotation against the saved copy.
No quotation joins two clauses with an ellipsis, and none crosses an em dash.
Run on the host: python3 scripts/import/draft_antonovich_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'michael-d-antonovich-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'michael-d-antonovich-2026-10-06')
AUD = os.path.join(ROOT, 'inventory', 'sources', 'audra-strickland-2026-10-06')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'HOLD': 'His term is the archive\'s office holding #29286 (Los Angeles County Board of Supervisors, 5th District, 1980 to 2016, years only), from the list of the valley\'s county supervisors on Leon Worden\'s pages, among them LW3109 (/scvhistory/lw3109.htm): "Baxter Ward 1974-1980 Michael D. Antonovich 1980-2016".',
    'LW3109': 'Leon Worden, "Kathryn Barger, Los Angeles County 5th District Supervisor, 2016-," archive page /scvhistory/lw3109.htm (not yet a record in the archive; its photograph is Kathryn Barger\'s portrait): "to succeed Michael D. Antonovich as supervisor for Los Angeles County\'s 5th District, which includes the Santa Clarita Valley."; "Antonovich held the office for 36 years and was forced into retirement under term limits that were approved by voters in 2002."; "Barger was his longtime deputy and chief of staff."; and, from the county\'s biography of her printed there, she "began her career in public service as a college student intern in the office of Supervisor Michael D. Antonovich and rose to become his Chief Deputy Supervisor in 2001". The page prints her first name "Kathyrn" in its opening sentence.',
    'DISTINFO': 'Supervisor Michael D. Antonovich, "District Information," http://antonovich.co.la.ca.us/districtinfo/districtinfo.html (read in the Wayback Machine\'s capture of 3 January 2008): "Population: 2,092,704* Square Miles: 2,838 (*7/05 County Estimate)". Its list of cities includes Santa Clarita, with Lancaster, Palmdale, Pasadena, Glendale and Burbank among 25 others, and its unincorporated areas include Agua Dulce, Bouquet Canyon, Canyon Country, Castaic, Green Valley, Placerita Canyon, Saugus, Stevenson Ranch, Val Verde, Valencia and West Ranch.',
    'AQMD': 'South Coast Air Quality Management District, "Michael Antonovich Biography," http://www.aqmd.gov/bios/bm_antonovich_michael.html ("This page updated: September 14, 2007"; read in the Wayback Machine\'s capture of 2 February 2008): "In 1980 Antonovich was elected to his first term as Supervisor, representing the northern half of Los Angeles County."; "He was elected to the California State Assembly in 1972 representing the communities of Glendale, Burbank, Sunland, Tujunga, Atwater, Griffith Park, Lakeview Terrace and Sun Valley."; "He served as a Republican Whip in the Assembly from 1976 to 1978."; "he was elected Chairman of the California Republican party in 1985".',
    'CITIZEN': 'Gary Johanson, "Supervisor Candidates Challenge Each Others\' Records," The Santa Clarita Valley Citizen, Sunday, September 18, 1988, on the archive page /scvhistory/files/citizen19880918/citizen19880918_ocr.htm (a computer reading of the printed page; not yet a record in the archive): "(Antonovich defeated Ward in 1980.)".',
    'BIO': 'Supervisor Michael D. Antonovich, "Biographical Information," http://antonovich.co.la.ca.us/bio/MDAbiography.pdf (read in the Wayback Machine\'s capture of 1 December 2007): "Member, Los Angeles County Board of Supervisors, 5th District"; "Elected in 1980."; "Member, California State Assembly 1972 - 1978"; "Member, Los Angeles Community College Board of Trustees 1969 - 1972"; "Member, Metropolitan Transportation Authority, since 1993"; among his honors, "In recognition of support in building Castaic Lake Senior Village" (from the Community Housing Development Group and the Santa Clarita Valley Committee on Aging) and a 2004 Humanitarian Award from the "Castaic Area Town Council".',
    'RECORD': 'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79: "Antonovich, Michael", R, "Los Angeles", regular sessions 1973 to 1978.',
    'CITY': 'Leon Worden, "Women have always run the SCV," The Signal, 2001, archive record #12300, legacy page /scvhistory/signal/worden/old/lw010501.htm: "Prior to cityhood in 1987, our contact with local government"; the sentence goes on to name Baxter Ward\'s office and then Antonovich\'s.',
    'DARCY': 'Leon Worden, "City Founder Jo Anne Darcy Dies at 86," SCVNews.com, Monday, October 30, 2017, archive page /scvhistory/obituary_joannedarcy.htm (not yet a record in the archive): "she left in 1980 to serve as the Santa Clarita Valley field deputy to the 5th District\'s newly elected supervisor, Michael D. Antonovich."; Darcy, in 2002: "He called me the Saturday after he got elected and asked if I\'d consider working for him,"; "Mike (Antonovich) believed in self-government, and he didn\'t stand in our way,". Leon Worden, "Preservation Group Hammers City But Misses the Nail\'s Head," The Signal, Thursday, Nov. 13, 2003, archive record #12142, legacy page /scvhistory/signal/worden/lw111303.htm: Darcy\'s "10 years on the City Council and 20 years as Supervisor Michael D. Antonovich\'s local representative". Jo Anne Darcy Collection page JD9002 (/scvhistory/jd9002.htm), on 1990: Darcy, "who was both senior field deputy to local county Supervisor Michael D. Antonovich and mayor of the city of Santa Clarita at the time".',
    'PRISON': 'The Signal, November 1, 1985, on the archive page /scvhistory/sg110185.htm, "L.A. Mayor\'s Saugus State Prison Plan Fans Flames of Cityhood, 1985" (one of its articles is archive document #28295): "Los Angeles Mayor Tom Bradley\'s proposal that a state prison be built in Bouquet Canyon"; Jo Anne Darcy, "field deputy to Fifth District Supervisor Mike Antonovich": "We\'re going to fight it to the death,"; Antonovich, at the site: "This is Tom Bradley\'s trick-or-treat,"; "Supervisor Antonovich said his office would lead the fight against the plan, and urged homeowners to write the governor in protest." Leon Worden\'s note on the page: "And it did die."',
    'HART': 'Timeline, "Operational Management of William S. Hart Museum (News Reports, 1958-1988)," archive page /scvhistory/hartmuseumtimeline19581988.htm (not yet a record in the archive), June 24, 1986: "Motion by Supervisor Michael D. Antonovich instructs County Chief Administrative Officer (CAO), Parks and NHMLA directors to conduct study of how best to conserve, preserve, interpret and offer a viable public program for Hart estate."; 1987: "At FOHP request, Antonovich reintroduces motion for funding and NHMLA operational management of Hart Museum. Board of Supervisors approves."; September 1, 1987: "NHMLA takes over operational management of Hart Museum." NHMLA is the Natural History Museum of Los Angeles County; FOHP, the Friends of Hart Park.',
    'HJ': 'Leon Worden, "Preservation Group Hammers City But Misses the Nail\'s Head," The Signal, Thursday, Nov. 13, 2003, archive record #12142, legacy page /scvhistory/signal/worden/lw111303.htm: "Supervisor Antonovich, who has enabled the society to operate Heritage Junction rent-free on a section of William S. Hart Park since 1980."; "Antonovich has stepped in to help the society protect the old Harry Carey Ranch buildings at Tesoro del Valle."',
    'JAIL': 'Jo Anne Darcy Collection page JD9002, "President George H.W. Bush Dedicates New North County Correctional Facility, 3-1-1990," /scvhistory/jd9002.htm (not yet a record in the archive), quoting the news report of the day: "Supervisor Mike Antonovich, who represents the SCV, stressed the need for jail facilities".',
    'NM03': 'Leon Worden, "Newsmaker of the Week: Supervisor Michael D. Antonovich," The Signal, Sunday, September 28, 2003 ("Television interview conducted Sept. 11, 2003"), legacy page /scvhistory/signal/newsmaker/sg092803.htm (not in the mirror and not yet a record in the archive; read in the Wayback Machine\'s capture of 22 October 2003). The Signal: "Castaic Lake was going to close"; "You\'ve allocated some contingency funds to keep Castaic Lake open in the meantime." Antonovich, on Castaic Lake: "The county has come forward, putting in funds from my office and from the county general fund, to supplement those dollars, and we\'re going to have a task force which is now meeting." On the Cemex mine in Soledad Canyon: "I opposed it for environmental reasons"; "the Board of Supervisors took action and we have a unanimous vote rejecting the proposal for environmental reasons." On Newhall Ranch: "Part of that project included half of it being open space, a recreational area, a park that\'s larger or just about the size of Griffith Park in Los Angeles County". The Signal: "Looking at Stevenson Ranch, Westridge, Newhall Ranch, do you see a separate city west of the freeway? Or do you see those areas coming into the city of Santa Clarita?" Antonovich: "That issue is going to be determined by the people who reside in those areas."; on State Route 14: "The 14 ought to be built out rapidly."',
    'VR': 'SCVTV, "Open House / Grand Opening of Vasquez Rocks Interpretive Center," archive page /scvhistory/vasquez053013.htm (not yet a record in the archive): "Vasquez Rocks Interpretive Center with Supervisor Michael D. Antonovich May 30, 2013".',
}

BODY = [
    'Michael D. Antonovich was the Santa Clarita Valley\'s member of the Los Angeles County Board of Supervisors for 36 years, for the 5th District, from his election in 1980 until term limits ended his service in 2016.{HOLD}{LW3109} His district, the northern half of the county, took in the City of Santa Clarita and the valley\'s unincorporated communities, Castaic, Val Verde, Stevenson Ranch, Valencia, Saugus, Canyon Country, Agua Dulce and others, with the Antelope Valley, Pasadena, Glendale and Burbank; by the county\'s estimate of 2005 it held 2,092,704 people.{AQMD}{DISTINFO} He took the seat from Baxter Ward in 1980.{CITIZEN} Until the City of Santa Clarita was formed in 1987, the supervisor\'s office was the valley\'s contact with local government, as Leon Worden put it.{CITY}',
    'He had been a teacher in the Los Angeles schools, a trustee of the Los Angeles Community College District from 1969, and a member of the State Assembly from 1973 to 1978 for Glendale, Burbank and the northeastern San Fernando Valley, and was a Republican whip there from 1976 to 1978.{BIO}{AQMD}{RECORD} He chaired the California Republican Party in 1985 and 1986.{AQMD}',
    'His Santa Clarita Valley field deputy from 1980 was Jo Anne Darcy, who stayed in the post for twenty years, through her ten years on the Santa Clarita City Council and her time as the City\'s mayor.{DARCY} "Mike (Antonovich) believed in self-government, and he didn\'t stand in our way," she said later.{DARCY} In November 1985 he went to Bouquet Canyon to oppose Los Angeles Mayor Tom Bradley\'s proposal for a state prison there, calling it "Tom Bradley\'s trick-or-treat," and his office led the fight against it; the plan was dropped.{PRISON}',
    'In 1986 and 1987 his motions before the board brought the William S. Hart Museum under the management of the Natural History Museum of Los Angeles County, which took it over on 1 September 1987.{HART} From 1980 he let the Santa Clarita Valley Historical Society run Heritage Junction rent-free on a section of Hart Park, and in 2003 he helped it protect the old Harry Carey Ranch buildings at Tesoro del Valle.{HJ} He spoke at the dedication of the North County Correctional Facility at Castaic by President George H.W. Bush in 1990.{JAIL} In 2003, when the Castaic Lake recreation area was to close, he put money from his office and the county\'s general fund toward keeping it open; he opposed the Cemex sand and gravel mine in Soledad Canyon "for environmental reasons," and said that whether Stevenson Ranch, Westridge and Newhall Ranch joined the City or became a city of their own was for the people who lived there to decide.{NM03} He was at the opening of the Vasquez Rocks Interpretive Center at Agua Dulce in May 2013.{VR}',
    'Term limits approved by the county\'s voters in 2002 ended his service in 2016. Kathryn Barger, who had begun in his office as a college intern and was his chief deputy and chief of staff, was elected that November to succeed him.{LW3109}',
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


def norm(t):
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('\u0092', "'")
    return re.sub(r'\s+', ' ', t)


def plain(path, enc='utf-8'):
    t = open(path, encoding=enc, errors='replace').read()
    t = re.sub(r'(?is)<(script|style).*?</\1>', ' ', t)
    return norm(html.unescape(re.sub(r'<[^>]+>', ' ', t)))


def mirror(*rel):
    p = os.path.join(MIRROR, *rel)
    return plain(p, 'latin-1') if os.path.exists(p) else None


CHECK = {
    'NM03': plain(os.path.join(SRC, 'wayback-sg092803-20031022081030.html'), 'latin-1'),
    'AQMD': plain(os.path.join(SRC, 'wayback-aqmd-bio-20080202042549.html'), 'latin-1'),
    'DISTINFO': plain(os.path.join(SRC, 'wayback-districtinfo-20080103214856.html'), 'latin-1'),
    'BIO': norm(subprocess.run(['pdftotext', '-layout', os.path.join(SRC, 'wayback-mdabiography-20071201142916.pdf'), '-'], capture_output=True, text=True).stdout),
    'RECORD': norm(open(os.path.join(AUD, 'record-of-members-assembly.txt'), encoding='utf-8').read()),
}
for k, rel in {'LW3109': ('scvhistory', 'lw3109.htm'), 'CITIZEN': ('scvhistory', 'files', 'citizen19880918', 'citizen19880918_ocr.htm'),
               'CITY': ('scvhistory', 'signal', 'worden', 'old', 'lw010501.htm'), 'PRISON': ('scvhistory', 'sg110185.htm'),
               'HART': ('scvhistory', 'hartmuseumtimeline19581988.htm'), 'HJ': ('scvhistory', 'signal', 'worden', 'lw111303.htm'),
               'JAIL': ('scvhistory', 'jd9002.htm'), 'VR': ('scvhistory', 'vasquez053013.htm')}.items():
    t = mirror(*rel)
    if t is not None:
        CHECK[k] = t
if 'LW3109' in CHECK:
    CHECK['HOLD'] = CHECK['LW3109']
t = mirror('scvhistory', 'obituary_joannedarcy.htm')
if t is not None:
    CHECK['DARCY'] = t + ' ' + (mirror('scvhistory', 'signal', 'worden', 'lw111303.htm') or '') + ' ' + (mirror('scvhistory', 'jd9002.htm') or '')

TITLES = {'Kathryn Barger, Los Angeles County 5th District Supervisor, 2016-,', 'District Information,', 'Michael Antonovich Biography,',
          'This page updated: September 14, 2007', 'Supervisor Candidates Challenge Each Others\' Records,', 'Biographical Information,',
          'Los Angeles', 'Women have always run the SCV,', 'City Founder Jo Anne Darcy Dies at 86,', 'Preservation Group Hammers City But Misses the Nail\'s Head,',
          'L.A. Mayor\'s Saugus State Prison Plan Fans Flames of Cityhood, 1985', 'field deputy to Fifth District Supervisor Mike Antonovich',
          'Operational Management of William S. Hart Museum (News Reports, 1958-1988),', 'President George H.W. Bush Dedicates New North County Correctional Facility, 3-1-1990,',
          'Newsmaker of the Week: Supervisor Michael D. Antonovich,', 'Television interview conducted Sept. 11, 2003', 'Open House / Grand Opening of Vasquez Rocks Interpretive Center,',
          'Antonovich, Michael', 'Castaic Area Town Council'}
missing = []
for k, text in CHECK.items():
    for q in [x for x in re.findall(r'"([^"]*)"', NOTES[k]) if len(x) >= 8]:
        q2 = re.sub(r'\s+', ' ', q)
        if q2 in TITLES and q2 not in ('Antonovich, Michael', 'Castaic Area Town Council', 'field deputy to Fifth District Supervisor Mike Antonovich'):
            continue
        if q2.rstrip('.,:') not in text:
            missing.append(f'{k}: {q2[:90]}')
for p in BODY:
    for q in [x for x in re.findall(r'"([^"]*)"', p) if len(x) >= 8]:
        if not any(q.rstrip('.,') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_antonovich_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29284, 'title': 'Michael D. Antonovich'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'Los Angeles County supervisor; state assemblyman',
        'wikidataId': 'Q6829617', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Michael_D._Antonovich',
        'aliasesAdd': ['Mike Antonovich'],
        'relatedPersonsAdd': [29288, 16140],
    },
    'holdingNotes': {
        '29286': 'Succeeded by Kathryn Barger, elected in November 2016 (office holding #29290). Leon Worden, LW3109 (/scvhistory/lw3109.htm): "Antonovich held the office for 36 years and was forced into retirement under term limits that were approved by voters in 2002." His own biography of 2007 gives the start: "Elected in 1980."',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Michael D. Antonovich #29284: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_antonovich_profile_2026_10_06.php) reads the .json beside this file. He is living: public life only.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'relatedPersons: Kathryn Barger #29288 (his successor, and his deputy and chief of staff, LW3109) and Jo Anne Darcy #16140 (his Santa Clarita Valley field deputy for twenty years). Alias "Mike Antonovich": the name the Signal used in 1985 and the Citizen in 1988; the record already has "Michael Antonovich" as a search name.', '',
       '## Note added to an office holding', '']
md += [f'- #{k}: {v}' for k, v in draft['holdingNotes'].items()]
md += ['', '## Quotations checked', '', 'Checked against saved copies: ' + ', '.join(sorted(CHECK)) + '.', '',
       '## Readings to weigh', '',
       '- Barger\'s start as a college intern in his office comes from the county\'s biography of her as printed on LW3109, not from a county page read directly.',
       '- "the plan was dropped": Leon Worden\'s note on /scvhistory/sg110185.htm says "And it did die." The page does not give the date.',
       '- His start in 1980: the archive\'s holding has years only. His own biography says "Elected in 1980"; Wikipedia gives a term start of December 1, 1980 (unsourced lead, not used).', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- Birth date, birth name (Wikipedia prints both "Michael Daniel" in the infobox and "Michael Dennis" in the lead), marriage and children (living person: left out in any case).',
       '- His term dates to the day (December 1, 1980 to November 30, 2016), his years as Mayor of Los Angeles County by date, and his runs for lieutenant governor in 1978 and the U.S. Senate in 1986 (the Signal of 1 November 1985 says he "recently announced his intention to run for U.S. Senate"; the outcome is not in the sources read).',
       '- His service in the California State Military Reserve, 2003 to 2008, and the Legion of Merit.', '',
       '## Left out on purpose', '',
       '- His views on illegal immigration and the Deputy David March case (Newsmaker 2003), and on state finances, which are about the county and the state more than the valley; the 1988 campaign against Baxter Ward (Citizen, 18 September 1988), whose outcome is not in the sources read.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources; written {os.path.relpath(OUT)}.json and .md')
