#!/usr/bin/env python3
"""Jeff Gorell #29452: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent. He is living: public life only, no birth fields.

Writes inventory/review/jeff-gorell-profile-draft-2026-10-06.json (read by
scripts/import/build_gorell_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026, in the copies saved in
inventory/sources/jeff-gorell-2026-10-06/ (manifest.json there): the Assembly's member pages and the Ventura County
Star and Moorpark Acorn articles from the Wayback Machine, the Los Angeles Times, Camarillo Acorn and Ojai Valley News
articles from their open pages. The Statements of Vote of 2004 and 2010 and the Record of Members of the Assembly are the
pdftotext copies in inventory/sources/audra-strickland-2026-10-06/; those of 2012 and 2014 are in
inventory/sources/legislative-districts-2026-10-04/sos/. The district figures are from templates/_data/valley-districts.json.
The mirror holds no page that names him (searched 6 October 2026). The check at the foot confirms each quotation against
the saved copy. No quotation joins two clauses with an ellipsis, and none crosses an em dash.
Run on the host: python3 scripts/import/draft_gorell_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'jeff-gorell-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'jeff-gorell-2026-10-06')
AUD = os.path.join(ROOT, 'inventory', 'sources', 'audra-strickland-2026-10-06')
SOS = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04', 'sos')

NOTES = {
    'LEGIS': 'His term is the archive\'s office holding #29503 (Assembly, 37th District, 6 December 2010 to 3 December 2012, under the 2001 lines). California Secretary of State, Statement of Vote, General Election, November 2, 2010, State Assembly, 37th Assembly District, https://elections.cdn.sos.ca.gov/sov/2010-general/73-state-assembly.pdf: Jeff Gorell, REP, 90,649 votes, 58.5%; Ferial Masry, DEM, 64,413, 41.5%; Los Angeles County, Gorell 13,270, Masry 9,115. Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79: "Gorell, Jeff", R, "Kern, Los Angeles, Ventura", regular sessions 2011 to 2012; R, "Los Angeles, Ventura", 2013 to 2014.',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, State Assembly. Under the "2001 lines, drawn by the Legislature", in force "December 2002 to December 2012", the 37th held "12.9%" of the valley, 35,031 people at the 2010 Census: "Castaic, Val Verde, Hasley Canyon, Agua Dulce and Green Valley, in a district that was mostly Ventura County"; the rest, 87.1%, was in the 38th. Under the 2011 lines, from December 2012, the valley lay in the 38th and the 36th only, so the 44th held none of it. The shares are counted from census blocks assigned to districts in the Statewide Database\'s block files.',
    'VCS': 'Timm Herdt, "Candidate for 37th Assembly District expects deployment to Afghanistan," Ventura County Star, October 30, 2010, http://www.vcstar.com/news/2010/oct/30/gorell-expects-deployment-to-afghanistan/ (read in the Wayback Machine\'s capture of 31 January 2012): "Jeff Gorell, Republican candidate in the 37th Assembly District, announced Saturday that he expects to be deployed to Afghanistan as a member of the Navy Reserve in March and would be unable to serve in Sacramento for 12 months, or half of the two-year term he is seeking."; "no California lawmaker has been called into duty since World War II"; "The 37th District, now represented by Audra Strickland, who is forced out by term limits, includes Thousand Oaks, Moorpark, Camarillo, Fillmore, Santa Paula, Ojai and much of Simi Valley."; "Gorell noted that, if elected, he would not receive his legislator\'s salary and benefits for the duration of his deployment."; "His mobilization orders require him to report for active duty on March 18."; "Gorell has been a reservist for nearly 12 years."',
    'LAT': 'Catherine Saillant, "New legislator must do his job while deployed in Afghanistan," Los Angeles Times, November 14, 2010, https://www.latimes.com/archives/la-xpm-2010-nov-14-la-me-assembly-deploy-20101114-story.html: "Gorell is the first legislator in California to be deployed for active duty since World War II"; "When it became clear that would violate the state\'s constitution, he decided instead to take a leave of absence, relying on staff and friendly Republican colleagues to attend to business in his Ventura County district"; "In 2004, Gorell lost a primary challenge to Assemblywoman Audra Strickland."; "While in his 20s, Gorell was a speechwriter for then-Gov. Pete Wilson and then worked seven years as a prosecutor in the Ventura County district attorney\'s office."',
    'PRIM04': 'California Secretary of State, Statement of Vote, Primary Election, March 2, 2004, Member of the State Assembly, 37th Assembly District, https://elections.cdn.sos.ca.gov/sov/2004-primary/assembly.pdf: Republican, Audra Strickland 17,845 votes, 35.6%; Jeff Gorell 16,086, 32.0%; Mike Robinson 15,262, 30.3%.',
    'BIO': 'California State Assembly, member page, "Jeff Gorell Biography," http://arc.asm.ca.gov/member/AD44/?p=bio (read in the Wayback Machine\'s capture of 27 September 2014): "Jeff Gorell was elected to the California State Assembly in 2010."; "A third generation navy man, Jeff currently serves as a Commander (intelligence officer) in the United States Navy Reserve."; "In 2002, Jeff led a combat camera team in Bagram, Afghanistan, and from 2011-2012, he commanded a targeting cell embedded with the U.S. Marines in Helmand Province, Afghanistan."; "From 1999-2006, Jeff Gorell was a Ventura County Deputy District Attorney where he served as a trial prosecutor in the major narcotics and violent felony units."; "In the early 1990s, Jeff Gorell served on the personal staff of Governor Pete Wilson in the State Capitol."; "Jeff Gorell is a member of the faculty at California Lutheran University in Thousand Oaks, CA where he has taught government and public policy since 2006."',
    'RET': 'California State Assembly, member page, press releases, http://arc.asm.ca.gov/member/37/?p=pr (read in the Wayback Machine\'s capture of 30 July 2012): dated 04/09/12, "Assembly Member Jeff Gorell Returns to the Legislature".',
    'SOV12': 'California Secretary of State, Statement of Vote, General Election, November 6, 2012, State Assembly, 44th Assembly District, https://elections.cdn.sos.ca.gov/sov/2012-general/pdf/14-state-assembly-1-80.pdf: Jeff Gorell, the incumbent, REP, 86,132 votes, 52.9%; Eileen MacEnery, DEM, 76,805, 47.1%.',
    'SOV14': 'California Secretary of State, Statement of Vote, General Election, November 4, 2014, United States Representative, 26th Congressional District, https://elections.cdn.sos.ca.gov/sov/2014-general/pdf/43-congress.pdf: Julia Brownley, the incumbent, DEM, 87,176 votes, 51.3%; Jeff Gorell, REP, 82,653, 48.7%.',
    'DM': 'Stephanie Sumell, "Gorell new deputy mayor of Los Angeles," Moorpark Acorn, June 19, 2015, http://www.mpacorn.com/news/2015-06-19/Front_Page/Gorell_new_deputy_mayor_of_Los_Angeles.html (read in the Wayback Machine\'s capture of 21 November 2016): "Gorell, 44, was appointed deputy mayor for homeland security and public safety for the City of Los Angeles earlier this month."; "The naval reserve officer and former county prosecutor was hand-picked by Los Angeles Mayor Eric Garcetti."',
    'VCBOS': 'Victoria Talbot, "Gorell makes new appointments for 2nd District," Camarillo Acorn, January 28, 2023, https://www.thecamarilloacorn.com/articles/gorell-makes-new-appointments-for-2nd-district/: "Ventura County Supervisor Jeff Gorell speaks during his first Board of Supervisors meeting at the Ventura County Government Center earlier this month."; "appointed in 2019 by Gorell\'s predecessor, Linda Parks". Grant Phillips, "Supervisor Gorell elected board chair; Supervisor Lopez elected vice chair," Ojai Valley News, January 16, 2026, https://www.ojaivalleynews.com/news/county/supervisor-gorell-elected-board-chair-supervisor-lopez-elected-vice-chair/article_593ed32b-399b-490e-b14b-a37a313cf576.html: "District 2 Supervisor Jeff Gorell, who represents areas of Thousand Oaks and Camarillo, was elected unanimously on Jan. 13 as chair of the Ventura County Board of Supervisors".',
}

BODY = [
    'Jeff Gorell represented part of the Santa Clarita Valley in the State Assembly for one term, from December 2010 to December 2012, for the 37th District.{LEGIS} Under the 2001 district lines the 37th took in Castaic, Val Verde, Hasley Canyon, Agua Dulce and Green Valley, 12.9 per cent of the valley\'s people by the archive\'s count from the 2010 census, in a district that was mostly Ventura County;{DIST} the Ventura County Star listed its main places as Thousand Oaks, Moorpark, Camarillo, Fillmore, Santa Paula, Ojai and much of Simi Valley.{VCS} He won the seat on 2 November 2010 with 58.5 per cent of the vote, 90,649 to 64,413 for the Democrat Ferial Masry; in the district\'s Los Angeles County part he had 13,270 votes to her 9,115.{LEGIS} He succeeded Audra Strickland, who could not run again under term limits.{VCS} Six years earlier he had lost to her in the Republican primary for the seat, 17,845 votes to 16,086.{LAT}{PRIM04}',
    'Three days before the election he announced that the Navy Reserve would send him to Afghanistan in March, for twelve months, half of the two-year term he was seeking.{VCS} The Los Angeles Times called him the first California legislator deployed for active duty since the Second World War. Finding that the state\'s constitution would not let him name a stand-in, he took a leave of absence and left his office to his staff and Republican colleagues.{LAT} His orders had him report on 18 March 2011, and he said he would take no legislator\'s pay while away.{VCS} He commanded a targeting cell with the Marines in Helmand Province, and was back in the Assembly by April 2012.{BIO}{RET}',
    'Before his election he had worked on the staff of Governor Pete Wilson in the early 1990s and had been a deputy district attorney in Ventura County from 1999 to 2006, prosecuting narcotics and violent felony cases. A Navy reservist for nearly twelve years by 2010,{VCS} he had served a first tour in Afghanistan in 2002, leading a combat camera team at Bagram. He taught government and public policy at California Lutheran University in Thousand Oaks.{BIO}{LAT}',
    'The district lines drawn in 2011 moved him to the 44th Assembly District, which held no part of the valley; he was re-elected there in November 2012 with 52.9 per cent.{DIST}{SOV12}{LEGIS} In 2014 he ran for Congress in the 26th District, almost wholly in Ventura County, and lost to Julia Brownley, 51.3 per cent to 48.7.{SOV14} In 2015 Mayor Eric Garcetti made him deputy mayor of Los Angeles for homeland security and public safety.{DM} He took his seat on the Ventura County Board of Supervisors for the 2nd District, succeeding Linda Parks, in January 2023, and the board elected him its chair in January 2026.{VCBOS}',
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


def textfile(path):
    return norm(open(path, encoding='utf-8', errors='replace').read())


CHECK = {
    'LEGIS': textfile(os.path.join(AUD, 'record-of-members-assembly.txt')),
    'DIST': open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read(),
    'VCS': plain(os.path.join(SRC, 'wayback-vcstar-2010-10-30-deployment-20120131070036.html')),
    'LAT': plain(os.path.join(SRC, 'latimes-2010-11-14-gorell-deploy.html')),
    'BIO': plain(os.path.join(SRC, 'wayback-asm-ad44-bio-20140927184853.html'), 'latin-1'),
    'RET': plain(os.path.join(SRC, 'wayback-asm-ad37-pr-20120730101452.html'), 'latin-1'),
    'DM': plain(os.path.join(SRC, 'wayback-mpacorn-2015-06-19-deputy-mayor.html'), 'latin-1'),
    'VCBOS': plain(os.path.join(SRC, 'acorn-2023-01-27-gorell-appointments.html')) + ' ' + plain(os.path.join(SRC, 'ojaivalleynews-2026-01-16-gorell-chair.html')),
}
TITLES = {'Kern, Los Angeles, Ventura', 'Los Angeles, Ventura', 'Gorell, Jeff', '2001 lines, drawn by the Legislature', 'December 2002 to December 2012',
          'Candidate for 37th Assembly District expects deployment to Afghanistan,', 'New legislator must do his job while deployed in Afghanistan,',
          'Jeff Gorell Biography,', 'Gorell new deputy mayor of Los Angeles,', 'Gorell makes new appointments for 2nd District,',
          'Supervisor Gorell elected board chair; Supervisor Lopez elected vice chair,'}
missing = []
for k, text in CHECK.items():
    for q in [x for x in re.findall(r'"([^"]*)"', NOTES[k]) if len(x) >= 8]:
        q2 = re.sub(r'\s+', ' ', q)
        if q2 in TITLES and k not in ('LEGIS', 'DIST'):
            continue
        if q2.rstrip('.,:') not in text:
            missing.append(f'{k}: {q2[:90]}')
for p in BODY:
    for q in [x for x in re.findall(r'"([^"]*)"', p) if len(x) >= 8]:
        if not any(q.rstrip('.,') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')


def sov(path):
    return re.sub(r'\s+', ' ', subprocess.run(['pdftotext', '-layout', path, '-'], capture_output=True, text=True).stdout)


# the vote figures, checked as figures in the Statements of Vote
FIG = {
    'LEGIS': (textfile(os.path.join(AUD, '2010-general_73-state-assembly.txt')), ['90,649', '64,413', '58.5%', '41.5%', '13,270', '9,115']),
    'PRIM04': (textfile(os.path.join(AUD, '2004-primary_assembly.txt')), ['17,845', '16,086', '15,262', '35.6%', '32.0%', '30.3%']),
    'SOV12': (sov(os.path.join(SOS, 'general-election-november-6-2012', '14-state-assembly-1-80.pdf')), ['86,132', '76,805', '52.9%', '47.1%']),
    'SOV14': (sov(os.path.join(SOS, 'general-election-november-4-2014', '43-congress.pdf')), ['87,176', '82,653', '51.3%', '48.7%']),
}
for k, (text, figs) in FIG.items():
    for f in figs:
        if f not in text:
            missing.append(f'{k}: figure {f} not in the Statement of Vote')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_gorell_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29452, 'title': 'Jeff Gorell'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'State assemblyman; Ventura County supervisor; Navy Reserve officer',
        'wikidataId': 'Q6173918', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Jeff_Gorell',
        'aliasesAdd': [],
        'relatedPersonsAdd': [29450],
    },
    'holdingNotes': {},
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Jeff Gorell #29452: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_gorell_profile_2026_10_06.php) reads the .json beside this file. He is living: public life only.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'relatedPersons: Audra Strickland #29450, his predecessor in the 37th (her profile already relates him; this makes it two-way). No holding note: holding #29503 already says (note 3) that from 2012 he sat for the 44th, which held no part of the valley.', '',
       '## Quotations checked', '', 'Checked against saved copies: ' + ', '.join(sorted(CHECK)) + '. Vote figures checked in the Statements of Vote: ' + ', '.join(sorted(FIG)) + '.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- Birth date and place, birth name, marriages and children (living person: left out in any case).',
       '- His rank of captain, the Defense Meritorious Service Medal and other decorations, and the 2023 command at Pearl Harbor (the Assembly page of 2014 makes him a commander; nothing later was read).',
       '- That he won the June 2010 primary with "89%" of the vote, the California Labor Federation endorsement, and Roll Call\'s description of him as a moderate.',
       '- His 2014 initiative to end high-speed rail and the drone privacy bill vetoed by Governor Brown; the Fox drama "City Hall" of 2019. The articles cited for these were not read, so they stay leads.',
       '- That he was Vice-Chairman of the Assembly Budget Committee: his own Assembly page of 2015 has a video titled "Assembly Budget Committee Vice-Chair Jeff Gorell speaks on the 2014-15 California State Budget", but it falls in his 44th District years, outside the valley; left out.',
       '- His election to the Ventura County board in 2022 by date and vote: the body says only that he took his seat in January 2023 (Camarillo Acorn).', '',
       '## Not found', '', 'The Reggie mirror of scvhistory.com holds no page naming Gorell (searched for "Gorell" in every .htm and .html file, 6 October 2026). Nothing in the sources ties him to a particular valley place or issue; the body says only what the district figures and the vote show.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources and {len(FIG)} Statements of Vote; written {os.path.relpath(OUT)}.json and .md')
