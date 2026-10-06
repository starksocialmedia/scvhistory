#!/usr/bin/env python3
"""George Runner #18747: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent. He is living: public life only, no birth fields.

Writes inventory/review/george-runner-profile-draft-2026-10-06.json (read by
scripts/import/build_george_runner_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

Every quotation below was read in the source named, on 6 October 2026: the archive's own pages from the Reggie mirror
(read-only, latin-1), the three Newsmaker of the Week pages of 2004 to 2006 (not in the mirror) from the Wayback
Machine's captures of scvhistory.com, the Board of Equalization page from the Wayback Machine, the Statement of Vote
for the ballot measures of 7 November 2006 and the Official Voter Information Guide from the Secretary of State
(copies in inventory/sources/george-runner-2026-10-06/, manifest.json there), the Statements of Vote of 1996 to 2008
from inventory/sources/legislative-districts-2026-10-04/sos/ (the 1996 one is a scan, read by eye), the Record of
Members of the Assembly and the Record of State Senators from inventory/sources/audra-strickland-2026-10-06/
(pdftotext copies), the district figures from templates/_data/valley-districts.json. The check at the foot confirms
each quotation against the saved copy. No quotation joins two clauses with an ellipsis, and none crosses an em dash.
Run on the host: python3 scripts/import/draft_george_runner_profile_2026_10_06.py
"""
import html, json, os, re

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'george-runner-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'george-runner-2026-10-06')
AUD = os.path.join(ROOT, 'inventory', 'sources', 'audra-strickland-2026-10-06')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'ASM': 'His Assembly term is the archive\'s office holding #29340 (Assembly, 36th District, 2 December 1996 to 2 December 2002, under the 1991 lines). California Secretary of State, Statements of Vote, 36th Assembly District: 5 November 1996, George Runner Jr (so printed) 78,383 votes, 64.18%, to David Cochran 43,746 (the Statement is a scan, read by eye), https://elections.cdn.sos.ca.gov/sov/1996-general/assemblymember.pdf; 3 November 1998, George Runner 64,221 votes, 62.90%, to Paula L Calderon 34,697, https://elections.cdn.sos.ca.gov/sov/1998-general/sov1998-general.pdf; 7 November 2000, "George C. Runner*" 90,712 votes, 63.1%, to Paula L. Calderon 47,528, https://elections.cdn.sos.ca.gov/sov/2000-general/assemb.pdf. Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, https://secretary.senate.ca.gov/media/79: "Runner, George", R, "Los Angeles", regular sessions 1997 to 2002.',
    'SEN': 'His Senate term is the archive\'s office holding #29359 (Senate, 17th District, 6 December 2004 to 21 December 2010, under the 2001 lines). California Secretary of State, Statements of Vote, 17th State Senate District: 2 November 2004, "George C. Runner" 179,992 votes, 59.7%, to Jonathan Daniel Kraut 109,037, https://elections.cdn.sos.ca.gov/sov/2004-general/formatted_st_sen_all_detail.pdf; 4 November 2008, George Runner, marked as the incumbent, 182,295 votes, 54.9%, to Bruce David McFarland 150,060, https://elections.cdn.sos.ca.gov/sov/2008-general/35_39_state_senators.pdf. Secretary of the Senate, Record of State Senators, 1849 to 2026, https://secretary.senate.ca.gov/media/88: "Runner, George C.", R, "Kern, Los Angeles, San Bernardino, Ventura", 2005 to 2010; note 113, on Pete Knight: "Died in office May 7, 2004. Succeeded by George Runner."; note 188: "Resigned from office December 21, 2010. Elected to the Board of Equalization 2nd District. Succeeded by Sharon Runner."; note 189, on Sharon Runner: "Elected at a special primary election February 15, 2011, having received 65.6% of the vote. Vice George C. Runner, resigned."',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json. State Assembly, "1991 lines, drawn by the court\'s Special Masters", in force "December 1992 to December 2002": the 36th held "83.7%" of the valley, 171,805 people at the 2000 Census, "The City of Santa Clarita as it then stood, Agua Dulce and the unincorporated land between them"; the rest, 16.3%, was in the 38th. State Senate, "2001 lines, drawn by the Legislature", in force "December 2004 to December 2012": the 17th held "74.2%", 201,305 people at the 2010 Census, "The City except its western neighborhoods, Castaic, Val Verde, Hasley Canyon, Agua Dulce and Green Valley, with the Antelope Valley"; the rest, 25.8%, was in the 19th. The shares are counted from census blocks assigned to districts in the Statewide Database\'s block files.',
    'DEBATE': 'Leon Worden, "Newsmaker of the Week: 17th State Senate District Debate, George Runner, Republican Nominee, Jonathan Kraut, Democratic Nominee," The Signal, Sunday, October 24, 2004 ("Television interview conducted October 13, 2004"), legacy page /scvhistory/signal/newsmaker/sg102404.htm (not in the mirror and not yet a record in the archive; read in the Wayback Machine\'s capture of 26 February 2005). Runner: "I came here as a child to the area of the 17th District, up in the high desert area, grew up there and went to the local schools."; "I then was a part of founding Desert Christian Schools as a young adult"; "I was elected to the local government and served as city council member and then mayor for the city of Lancaster."; "when Sen. Knight first ran for the Senate and opened up the Assembly seat"; "at that time the seat included the Santa Clarita Valley and the Antelope Valley. So I represented the good citizens of Santa Clarita during my time there for six years."; "I served as vice chair of (the) Budget (Committee) for four of my six years up there in Sacramento, in the Assembly."; "Then I was termed out".',
    'FRONT': 'Leon Worden, "\'Front Runner\' is off and running," The Signal, Wednesday, April 10, 1996, archive record #12570 ("36th Assembly District Nominee George Runner"), legacy page /scvhistory/signal/worden/old/lw041096.htm: "George hit the ground running after winning the nomination to State Assembly a couple of Tuesdays ago."; "My main goal for the rest of this year is to spend a lot of time meeting with people in the Santa Clarita Valley and learning the local issues,"; "Runner attended last week\'s public hearing on the proposed Elsmere Canyon Landfill, which he opposes".',
    'AUD': 'Carol Rock, "Realizing a Dream. Community members join forces to restore Newhall Elementary\'s auditorium," The Signal, Wednesday, February 24, 1999, archive page /scvhistory/sg19990224auditorium.htm (not yet a record in the archive): "Linda Johnson, field representative for Assemblyman George Runner, who shepherded the funding request through Sacramento, said:"; "He saw the opportunity for funding during last year\'s budget and made a \'member\'s request.\'"; "Since community theater had come to the fore, that\'s what the city decided to fund."; "the City Council approved TAC\'s request four weeks ago"; "The actual amount received by the group was $344,750". TAC is Theatre Arts for Children.',
    'NSD': 'SCVTV, "Newhall School Auditorium Reborn," video premiered October 26, 2017, archive page /scvhistory/nsd102617.htm (not yet a record in the archive): "the Newhall School Auditorium has been reborn as the Newhall Family Theater for the Performing Arts"; among those featured, "Sen. George Runner, Member, California Board of Equalization".',
    'VET': 'City of Santa Clarita text on the archive\'s photograph pages LW3135 (/scvhistory/lw3135.htm, "Future Veterans Historical Plaza Site, 1998") and SC1711 (/scvhistory/sc1711.htm), headed "Source: City of Santa Clarita": "The City Council led an effort that included the support of then Assembly-member George Runner and State Senator Pete Knight, to secure $250,000 in State funding through the Department of Veterans Affairs for the land acquisition of one-half acre for this project."',
    'SR126': 'Eric Thayer, "San Fernando Changing Hands? Runner bill would transfer ownership of a portion of San Fernando Road to the city," The Signal, Saturday, August 25, 2001, archive page /oldtownnewhall/news/sg082501a.htm (not yet a record in the archive): "legislation that would turn over a portion of San Fernando Road to the city of Santa Clarita could help in the city\'s redevelopment efforts"; Runner: "The state is no longer obligated for the upkeep and maintenance and the city can make road improvements without being delayed by bureaucratic red tape." Leon Worden on the archive\'s photograph page LW3136 (/scvhistory/lw3136.htm): "the city persuaded the state in 2001 to relinquish the SR-126 designation within city limits, via legislation by then-Assemblyman George Runner."',
    'HOME': 'Leon Worden, "Drum still beats for home rule," The Signal, March 26, 1997, archive record #12426, legacy page /scvhistory/signal/worden/old/lw032697.htm: "the new bill from Assemblyman George Runner (R-Lancaster) to examine the way services are delivered in Los Angeles County and possibly split the behemoth county into two or more smaller counties".',
    'BONDS': 'Leon Worden, "Local Republicans split over school bonds," The Signal, Wednesday, October 21, 1998, archive record #12324, legacy page /scvhistory/signal/worden/old/lw102198.htm: "George Runner, who represents the bulk of this valley and the barren one to the north, shares many of McClintock\'s concerns."; Runner: "It\'s a fair compromise for getting schools built in California,"; "Ultimately there is a lot in 1A that parallels the deals that have already been done between school districts and developers in the Santa Clarita Valley,".',
    'KNIGHT': 'Leon Worden, "Newsmaker of the Week: State Sen. William J. \'Pete\' Knight," The Signal, Sunday, April 25, 2004 ("Television interview conducted April 1, 2004"), archive document #31412, legacy page /scvhistory/signal/newsmaker/sg042504.htm. Knight: "George Runner is going to be elected to the 17th Senate District in my stead, and I think he\'ll do a good job there."',
    'NM05': 'Leon Worden, "Newsmaker of the Week: George Runner, State Senator, 17th District," The Signal, Sunday, March 27, 2005 ("Television interview conducted February 25, 2005"), legacy page /scvhistory/signal/newsmaker/sg032705.htm (not in the mirror and not yet a record in the archive; read in the Wayback Machine\'s capture of 4 April 2005). The introduction: "This week\'s newsmaker is State Senator George Runner, R-Lancaster, who represents most of the Santa Clarita Valley." The Signal: "What\'s it like being half of California\'s first legislative couple?"; "As chairman of the Senate Republican caucus, you\'ve got to keep all of the good little Republican Senators in line."; "When the Castaic Lake Water Agency bought the Santa Clarita Water Co., you pushed through an Assembly bill making SCWC\'s seat on the CLWA board an elected position. It hasn\'t happened yet." Runner: "Our concern was that people needed a broader representation on that board"; "At some point, they\'re just going to have to bite the bullet and do the intent of the Legislature."; on the Cemex mine in Soledad Canyon, "this is an issue that, unfortunately, Santa Clarita faces a lot, when you have very little influence on the issues that are directly connected and surround your borders."',
    'NM06': 'Leon Worden, "Newsmaker of the Week: George Runner, State Senator, 17th District," The Signal, Sunday, February 5, 2006 ("Television interview conducted January 27, 2006"), legacy page /scvhistory/signal/newsmaker/sg020506-nm.htm (not in the mirror and not yet a record in the archive; read in the Wayback Machine\'s capture of 26 March 2006). The introduction: "This week\'s newsmaker is state Senator George Runner, cosponsor of the Jessica\'s Law initiative." Runner: "It\'s JessicasLaw2006.com."; asked where his Santa Clarita office was, "In City Hall, (on the) second floor."; on school funding, "most of the schools in my district were the ones that were getting less"; "I think we are going to see the schools in the Santa Clarita Valley better funded."',
    'PROP83': 'California Secretary of State, Statement of Vote, General Election, November 7, 2006, State Ballot Measures, https://elections.cdn.sos.ca.gov/sov/2006-general/measures.pdf: Proposition No. 83, "Sex Offenders/Residence Restrictions Monitoring," State Totals, For 5,926,800 (70.5%), Against 2,483,597 (29.5%). Official Voter Information Guide, General Election, November 7, 2006, https://vig.cdn.sos.ca.gov/2006/general/pdf/english.pdf, argument in favor of Proposition 83: "Proposition 83 is named after Jessica Lunsford"; "For more information, please visit www.JessicasLaw2006.com." The argument is signed by others, not by Runner.',
    'BOE': 'California State Board of Equalization, member page, http://www.boe.ca.gov/members/runner/ (read in the Wayback Machine\'s capture of 8 September 2016): "George Runner, Member, First District, California State Board of Equalization"; "I am thankful and honored to represent more than nine million Californians as an elected member of the State Board of Equalization."; its offices, Sacramento and "Lancaster Office". The Record of State Senators (note 188) has him elected to the board\'s 2nd District in 2010.',
}

BODY = [
    'George Runner represented most of the Santa Clarita Valley in the Legislature for twelve years: in the State Assembly for the 36th District from December 1996 to December 2002, and in the State Senate for the 17th District from December 2004 until he resigned on 21 December 2010.{ASM}{SEN} Under the 1991 lines the 36th held the City of Santa Clarita as it then stood, Agua Dulce and the unincorporated land between them, 83.7 per cent of the valley\'s people by the archive\'s count from the 2000 census. Under the 2001 lines the 17th held all of the valley but Stevenson Ranch and the City\'s western neighborhoods, 74.2 per cent of the valley by the archive\'s count from the 2010 census, joined to the Antelope Valley.{DIST} In 2005 the Signal introduced him as the senator "who represents most of the Santa Clarita Valley."{NM05}',
    'A Lancaster man, he had served on the Lancaster City Council and as the city\'s mayor, and had helped found Desert Christian Schools, before he ran for the Assembly in 1996, when Pete Knight left the seat to run for the State Senate; by his own account the Assembly seat then "included the Santa Clarita Valley and the Antelope Valley."{DEBATE} After winning the Republican nomination that spring he told the Signal his goal was "to spend a lot of time meeting with people in the Santa Clarita Valley and learning the local issues," and he went to the hearing on the proposed Elsmere Canyon Landfill, which he opposed.{FRONT} He won the seat in November 1996 with 64.18 per cent of the vote and was re-elected in 1998 and 2000.{ASM} He was vice chairman of the Assembly Budget Committee for four of his six years there.{DEBATE}',
    'In the 1998 state budget he made a member\'s request for money that the City of Santa Clarita gave to Theatre Arts for Children, which received $344,750 toward restoring the auditorium of Newhall Elementary School; the restored hall reopened in 2017 as the Newhall Family Theater for the Performing Arts, and he appeared in the video made for its opening.{AUD}{NSD} With State Senator Pete Knight he supported the City\'s effort in 2000 to secure $250,000 in state money, through the Department of Veterans Affairs, to buy half an acre for the Veterans Historical Plaza in Newhall.{VET} His bill of 2001 turned the part of San Fernando Road that was State Route 126 within the City over to the City, so that it could rework the street through downtown Newhall "without being delayed by bureaucratic red tape," as he put it.{SR126} The Signal credited him with the Assembly bill that made the Santa Clarita Water Company\'s seat on the board of the Castaic Lake Water Agency an elected one after the agency bought the company; he said in 2005 that the agency had yet to act on it, and that "people needed a broader representation on that board."{NM05} In 1997 he introduced a bill to study how Los Angeles County delivered its services and whether to split it into smaller counties, and in 1998 he backed Proposition 1A, the state school construction bond, as "a fair compromise for getting schools built in California."{HOME}{BONDS}',
    'Term limits ended his Assembly service in 2002.{DEBATE} Pete Knight, before his death in office on 7 May 2004, said Runner would succeed him in the 17th Senate District, and Runner won the seat that November with 59.7 per cent of the vote; he was re-elected in 2008.{KNIGHT}{SEN} His wife, Sharon Runner, was then in the Assembly, and the Signal asked him in 2005 what it was like "being half of California\'s first legislative couple." The Signal described him then as chairman of the Senate Republican caucus; he kept his Santa Clarita office "In City Hall, (on the) second floor."{NM05}{NM06} The Signal named him in 2006 as a cosponsor of the Jessica\'s Law initiative on sex offenders, which the voters passed that November as Proposition 83, by 70.5 per cent to 29.5.{NM06}{PROP83} In the same interview he said he was working to even out state school funding and expected "the schools in the Santa Clarita Valley better funded."{NM06}',
    'He resigned from the Senate on 21 December 2010 on his election to the State Board of Equalization, and Sharon Runner succeeded him in the 17th District, winning a special primary on 15 February 2011.{SEN} By 2016 he sat for the board\'s First District, with offices in Sacramento and Lancaster.{BOE}',
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


def mirror(*rel):
    p = os.path.join(MIRROR, *rel)
    return plain(p, 'latin-1') if os.path.exists(p) else None


def textfile(path):
    return re.sub(r'\s+', ' ', open(path, encoding='utf-8', errors='replace').read().replace('’', "'"))


def sov(rel):
    import subprocess
    return re.sub(r'\s+', ' ', subprocess.run(['pdftotext', '-layout', os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04', 'sos', rel), '-'], capture_output=True, text=True).stdout)


CHECK = {
    'DEBATE': plain(os.path.join(SRC, 'wayback-sg102404-20050226033722.html'), 'latin-1'),
    'NM05': plain(os.path.join(SRC, 'wayback-sg032705-20050404044952.html'), 'latin-1'),
    'NM06': plain(os.path.join(SRC, 'wayback-sg020506-nm-20060326231758.html'), 'latin-1'),
    'BOE': plain(os.path.join(SRC, 'wayback-boe-members-runner-20160908213257.html'), 'utf-8'),
    'PROP83': textfile(os.path.join(SRC, 'sos-vig-2006-11-07-english.txt')) + ' ' + textfile(os.path.join(SRC, 'sos-sov-2006-11-07-measures.txt')),
    'SEN': textfile(os.path.join(AUD, 'record-of-senators.txt')) + ' ' + sov('presidential-general-election-november-2-2004/formatted_st_sen_all_detail.pdf') + ' ' + sov('presidential-general-election-november-4-2008/35_39_state_senators.pdf'),
    'ASM': textfile(os.path.join(AUD, 'record-of-members-assembly.txt')) + ' ' + sov('general-election-november-7-2000/assemb.pdf'),
    'DIST': open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read(),
}
for k, rel in {'FRONT': ('scvhistory', 'signal', 'worden', 'old', 'lw041096.htm'), 'AUD': ('scvhistory', 'sg19990224auditorium.htm'),
               'NSD': ('scvhistory', 'nsd102617.htm'), 'VET': ('scvhistory', 'lw3135.htm'), 'HOME': ('scvhistory', 'signal', 'worden', 'old', 'lw032697.htm'),
               'BONDS': ('scvhistory', 'signal', 'worden', 'old', 'lw102198.htm'), 'KNIGHT': ('scvhistory', 'signal', 'newsmaker', 'sg042504.htm')}.items():
    t = mirror(*rel)
    if t is not None:
        CHECK[k] = t
t = mirror('oldtownnewhall', 'news', 'sg082501a.htm')
if t is not None:
    CHECK['SR126'] = t + ' ' + (mirror('scvhistory', 'lw3136.htm') or '')
if 'VET' in CHECK:
    CHECK['VET'] += ' ' + (mirror('scvhistory', 'sc1711.htm') or '')

TITLES = {"'Front Runner' is off and running,", 'Drum still beats for home rule,', 'Local Republicans split over school bonds,',
          'Realizing a Dream. Community members join forces to restore Newhall Elementary\'s auditorium,', 'Newhall School Auditorium Reborn,',
          'San Fernando Changing Hands? Runner bill would transfer ownership of a portion of San Fernando Road to the city,',
          'Sex Offenders/Residence Restrictions Monitoring,', 'Kern, Los Angeles, San Bernardino, Ventura', 'Los Angeles',
          'Future Veterans Historical Plaza Site, 1998', 'Source: City of Santa Clarita', '36th Assembly District Nominee George Runner',
          'Television interview conducted October 13, 2004', 'Television interview conducted February 25, 2005', 'Television interview conducted January 27, 2006',
          'Television interview conducted April 1, 2004', 'Lancaster Office'}
missing = []
for k, text in CHECK.items():
    for q in [x for x in re.findall(r'"([^"]*)"', NOTES[k]) if len(x) >= 8]:
        q2 = re.sub(r'\s+', ' ', q)
        if q2 in TITLES or q2.startswith('Newsmaker of the Week'):
            continue
        if q2.rstrip('.,:') not in text:
            missing.append(f'{k}: {q2[:90]}')
for p in BODY:
    for q in [x for x in re.findall(r'"([^"]*)"', p) if len(x) >= 8]:
        if not any(q.rstrip('.,') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))
unchecked = sorted(set(NOTES) - set(CHECK))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_george_runner_profile_2026_10_06.py, Claude Code',
    'person': {'id': 18747, 'title': 'George Runner'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'State assemblyman and senator; member, State Board of Equalization',
        'wikidataId': 'Q5544109', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/George_Runner',
        'aliasesAdd': ['George C. Runner', 'George Runner Jr.'],
        'relatedPersonsAdd': [29314, 29324],
    },
    'holdingNotes': {
        '29359': 'Succeeded in the 17th District by Sharon Runner, elected at a special primary election on 15 February 2011 with 65.6% of the vote (Record of State Senators, notes 188 and 189: "Succeeded by Sharon Runner."; "Vice George C. Runner, resigned."; office holding #29361 is her term in the 17th).',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# George Runner #18747: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_george_runner_profile_2026_10_06.php) reads the .json beside this file. He is living: public life only.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'relatedPersons: Pete Knight #29314 (his predecessor in the 36th Assembly District and the 17th Senate District, by Runner\'s own account and the Record of State Senators, note 113) and Sharon Runner #29324 (his successor in the 17th, Record note 188, and his wife, Newsmaker of 27 March 2005: "our 32-year marriage"). No spouseOf relation is set: the marriage is stated in the text from the Signal\'s interview. Aliases: "George C. Runner" (Statements of Vote 2000 and 2004, Record of State Senators) and "George Runner Jr." (Statement of Vote 1996, printed "George Runner Jr" without the point).', '',
       '## Note added to an office holding', '']
md += [f'- #{k}: {v}' for k, v in draft['holdingNotes'].items()]
md += ['', '## Quotations checked', '', 'Checked against saved copies: ' + ', '.join(sorted(CHECK)) + '. Checked by figure only (Statements of Vote, the 1996 scan read by eye): ' + (', '.join(unchecked) if unchecked else 'none') + '.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- Birth date and place (living person: left out in any case).',
       '- That he was Assembly Republican caucus chair or vice chair of the Budget Committee "from 1998 to 2002"; the body uses only his own words ("vice chair of (the) Budget (Committee) for four of my six years").',
       '- That he and Sharon Runner were "co-authors" of Proposition 83 (Jessica\'s Law): the voter guide\'s argument in favor is signed by others; the body says only that the Signal named him a cosponsor of the initiative.',
       '- His Board of Equalization dates (January 2011 to January 2019), his 2014 re-election, and his later candidacies. The body gives only the Record of State Senators (elected 2010) and the board\'s own page of 2016.',
       '- His party offices, his Lancaster City Council and mayoral years, and his Desert Christian Schools dates: the body takes the offices from his own words in 2004 and gives no years.', '',
       '## Left out on purpose', '',
       '- His Senate votes and bills beyond what he or the Signal tied to the valley; the Vehicle License Fee repeal of 1998 (lw060398, archive record #12254), which is a dispute with the City Council more than his record; the banners over San Fernando Road his field representative carried to Caltrans in 2000 (lw010501, archive record #12300).', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources; written {os.path.relpath(OUT)}.json and .md')
