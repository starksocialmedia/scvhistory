#!/usr/bin/env python3
"""BJ Atkins #28316: the profile draft and his water-board holdings (Nathan, 6 October 2026).

Writes inventory/review/bj-atkins-profile-draft-2026-10-06.json and .md. Every quotation in a note is checked
against the saved copy of its source (inventory/news/bj-atkins-2026-10-06/ and inventory/news/term-endings-2026-10-04/);
the script stops if one is not found. No em dashes in our own text. Reads nothing from the database.
Run: python3 scripts/import/draft_atkins_profile_2026_10_06.py
"""
import json, re, html, subprocess, sys, pathlib

ROOT = pathlib.Path(__file__).resolve().parents[2]
N1 = ROOT / 'inventory/news/bj-atkins-2026-10-06'
N0 = ROOT / 'inventory/news/term-endings-2026-10-04'


def text(p):
    p = pathlib.Path(p)
    if p.suffix == '.pdf':
        t = subprocess.run(['pdftotext', str(p), '-'], capture_output=True, text=True).stdout
    else:
        t = p.read_text(errors='replace')
        if p.suffix in ('.html', '.htm'):
            t = re.sub(r'(?is)<(script|style).*?</\1>', ' ', t)
            t = html.unescape(re.sub(r'<[^>]+>', ' ', t))
    return t


def norm(s):
    s = s.replace('ﬁ', 'fi').replace('ï¬\u0081', 'fi')  # the fi ligature, and its mojibake in the mirror's text
    s = s.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace(' ', ' ')
    return re.sub(r'\s+', ' ', s).strip().lower()


SRC = {
    'RES': N1 / 'scvwater-resolution-atkins-2022-08.pdf',
    'SV05': N1 / 'wb-smartvoter-2005-ncwd.txt',
    'SV09': N1 / 'wb-smartvoter-2009-ncwd.txt',
    'SN13': N1 / 'scvnews-election-results.txt',
    'SN12': N1 / 'gutzeit-atkins-top-slots.txt',
    'NM353': N1 / 'notw353.txt',
    'NCWD17': N1 / 'mirror-scvwa010218agenda-p5.txt',
    'CLWA12': N1 / 'mirror-clwa_scwd_2012-p6.txt',
    'CLWADIR': N1 / 'mirror-clwadirectors.txt',
    'SB634': N1 / 'mirror-scvwa_sb634-p10.txt',
    'SN20': N0 / 'atkins-plans-to-resign.txt',
    'SN21': N0 / 'atkins-delays-resigning.txt',
    'SG22': N0 / 'wb-signal-atkins-to-resign.txt',
    'MIN0719': N0 / 'scvwater-minutes-2022-07-19.txt',
    'REL0908': N0 / 'scvwater-2022-09-08-new-member-appointed.pdf',
    'SG0830': N0 / 'wb-signal-water-appoints-division-3.txt',
    'EHI': N1 / 'environmentalhelp-history.txt',
}
CACHE = {k: norm(text(v)) for k, v in SRC.items()}

# each note: key, the note, and the quotations it makes from which source
NOTES = [
    ('RES', 'Santa Clarita Valley Water Agency Board of Directors, "Resolution of the Board of Directors of the Santa Clarita Valley Water Agency Honoring and Commending B. J. Atkins for His Service and Dedication," revised text, board handout, Item 5.1, August 2022, https://www.YourSCVWater.com/sites/default/files/SCVWA/board-meetings/2022/SCV-Water-Board-Handout-082622-Item-5.1-Revised-Resoluiton-B.-J.-Atkins-2.pdf: "was elected to the Newhall County Water District Board of Directors in November of 2005 and served through December 2017"; "was appointed to the Castaic Lake Water Agency Board of Directors in November 2008 and served through December 2017"; "served on the Santa Clarita Valley Water Agency (Agency) Board of Directors from January 2018 to" (the revised text strikes out "December" and gives "July 19, 2022"); "voted and was active in his support for the vision of combining Castaic Lake Water Agency and Newhall County Water District to form the Santa Clarita Valley Water Agency". The number given the adopted resolution is not held.',
     ['was elected to the Newhall County Water District Board of Directors in November of 2005 and served through December 2017',
      'was appointed to the Castaic Lake Water Agency Board of Directors in November 2008 and served through December 2017',
      'served on the Santa Clarita Valley Water Agency (Agency) Board of Directors from January 2018 to',
      'July 19, 2022',
      'voted and was active in his support for the vision of combining Castaic Lake Water Agency and Newhall County Water District to form the Santa Clarita Valley Water Agency']),
    ('SV05', 'League of Women Voters of California Education Fund, Smart Voter, "Member, Board of Directors; Newhall County Water District," November 8, 2005 Election, Los Angeles County, "Results as of Nov 28 4:37pm, 100.00% of Precincts Reporting (15/15)," http://www.smartvoter.org/2005/11/08/ca/la/race/139/, as captured by the Wayback Machine on 13 October 2008: "(Vote for 3)"; Maria Gutzeit 4,268, Barbara Dore 4,099, "B. J. Atkins 4,061 votes 22.40%", Joan Dunn 2,431, Edwin A "Ed" Dunn, Jr 1,695, Trish Lester 1,576. His ballot designation: "Occupation: Environmental Consultant".',
     ['(Vote for 3)', 'B. J. Atkins 4,061 votes 22.40%', 'Occupation: Environmental Consultant', 'Results as of Nov 28 4:37pm, 100.00% of Precincts Reporting (15/15)']),
    ('SV09', 'League of Women Voters of California Education Fund, Smart Voter, "Board of Directors; Newhall County Water District," November 3, 2009 Election, Los Angeles County, "Results as of Nov 20 4:27pm, 100.00% of Precincts Reporting (35/35)," http://smartvoter.org/2009/11/03/ca/la/race/02086000/, as captured by the Wayback Machine on 4 November 2013: "(Vote for 3)"; "B. J. Atkins 1,743 votes 31.29%"; Maria A. Gutzeit 1,671, Kathryn R. Colley 1,230, Michael S. Cruz 927. His ballot designation: "Occupation: Water Board Director".',
     ['(Vote for 3)', 'B. J. Atkins 1,743 votes 31.29%', 'Results as of Nov 20 4:27pm, 100.00% of Precincts Reporting (35/35)', 'Occupation: Water Board Director']),
    ('SN13', 'SCVNews.com, "ELECTION RESULTS: Upset in Sulphur Springs," November 5, 2013, https://scvnews.com/election-results: "In the Newhall County Water District, newcomer Carl Puckett finished just 42 votes behind second-termer Kathy Colley, who keeps her seat along with Maria Gutzeit and B.J. Atkins, who finished first and second, respectively." Its table, made "With the absentee and election-day votes tabulated" on election night, gives Maria A Gutzeit 1,089, "B J ATKINS 1,047 26.36", Kathy Colley 939, Carl Puckett 897, three seats. The County\'s certified count for this contest is not held.',
     ['In the Newhall County Water District, newcomer Carl Puckett finished just 42 votes behind second-termer Kathy Colley, who keeps her seat along with Maria Gutzeit and B.J. Atkins, who finished first and second, respectively.',
      'With the absentee and election-day votes tabulated', 'B J ATKINS 1,047 26.36']),
    ('SN12', 'SCVNews.com Staff, "Gutzeit, Atkins Fill Top Slots on Water Board," SCVNews.com, January 18, 2012, https://scvnews.com/gutzeit-atkins-fill-top-slots-on-water-board: "named B.J. Atkins as vice president"; "was first elected to the NCWD board in 2005 and served as its president in 2008 and 2010. He is also the NCWD\'s liaison to the Castaic Lake Water Agency." SCVTV\'s "Newsmaker of the Week" of January 21, 2013 names him "BJ Atkins, Vice President" of the Newhall County Water District (https://scvtv.com/html/notw353.html), and the district\'s minutes of its regular meeting of December 14, 2017 list "B. J. Atkins, Vice President" (printed in the Santa Clarita Valley Water Agency\'s board agenda of January 2, 2018, legacy file /scvhistory/files/scvwa010218agenda/, page 5).',
     ['named B.J. Atkins as vice president', 'was first elected to the NCWD board in 2005 and served as its president in 2008 and 2010. He is also the NCWD\'s liaison to the Castaic Lake Water Agency.']),
    ('NM353', 'SCVTV, "SCV Newsmaker of the Week," January 21, 2013, https://scvtv.com/html/notw353.html (linked from the archive\'s Newsmaker index, /scvhistory/signal/newsmaker/index.htm, as "Episode 353 | Maria Gutzeit & BJ Atkins, NCWD"): "Maria Gutzeit, President"; "BJ Atkins, Vice President"; "Topic: CLWA Acquisition of Valencia Water Co."',
     ['Maria Gutzeit, President', 'BJ Atkins, Vice President', 'Topic: CLWA Acquisition of Valencia Water Co.']),
    ('CLWA12', 'Castaic Lake Water Agency and Santa Clarita Water Division, 2012 report, page 6 (legacy file /scvhistory/files/clwa_scwd_2012/): the board has "one Director appointed by three of the retail water purveyors (Los Angeles County Waterworks District #36, Newhall County Water District and Valencia Water Company)"; its table of directors gives "B.J. Atkins NCWD January 2013" (director, division, term expires). The archive\'s list of the agency\'s directors (/scvhistory/clwadirectors.htm) gives "B.J. ATKINS 2009". The Signal of July 13, 2022, citing the agency\'s website: "in January 2009, he also became a member of the Castaic Lake Water Agency board." When he was reappointed for the term after January 2013 is not held.',
     ['one Director appointed by three of the retail water purveyors (Los Angeles County Waterworks District #36, Newhall County Water District and Valencia Water Company)',
      'B.J. Atkins NCWD January 2013', 'B.J. ATKINS 2009', 'in January 2009, he also became a member of the Castaic Lake Water Agency board.']),
    ('SB634', 'Senate Bill 634 (Wilk), Statutes of 2017, Chapter 833, Section 8 (legacy file /scvhistory/files/scvwa_sb634/, page 10): "The agency shall be governed by a board of directors that shall initially consist of 15 members as follows: (1) The five members of the Newhall County Water District board of directors in office as of December 31, 2017."',
     ['The agency shall be governed by a board of directors that shall initially consist of 15 members as follows: (1) The five members of the Newhall County Water District board of directors in']),
    ('SOV20', 'Elected on November 3, 2020, first of 4 candidates for 2 seats in Division 3, with 16,883 votes (County of Los Angeles, Registrar-Recorder/County Clerk, Statement of Votes Cast and Official Election Returns, General Election, November 3, 2020: Santa Clarita Valley Water Agency).', []),
    ('SN20', 'Tammy Murga, The Signal, "SCV Water Board Director BJ Atkins Announces Plans to Resign," December 25, 2020, as carried by SCVNews.com, https://scvnews.com/scv-water-board-director-bj-atkins-plans-to-resign/: "has confirmed he plans to resign as early as May"; "will relocate out of Division 3, the area he represents"; "as chairman (of the SCV Groundwater Sustainability Agency), I\'ll do that until it is time for me to resign."',
     ['has confirmed he plans to resign as early as May', 'will relocate out of Division 3, the area he represents',
      "as chairman (of the SCV Groundwater Sustainability Agency), I'll do that until it is time for me to resign."]),
    ('SN21', 'Kev Kurdoghlian, The Signal, "Atkins Delays Resigning from SCV Water Board," July 23, 2021, as carried by SCVNews.com, https://scvnews.com/atkins-delays-resigning-from-scv-water-board: "he has delayed his plan to resign from the board, due to construction delays on a house he is building outside of the agency\'s jurisdiction."',
     ["he has delayed his plan to resign from the board, due to construction delays on a house he is building outside of the agency's jurisdiction."]),
    ('SG22', 'The Signal, "Atkins to resign from SCV Water board," July 13, 2022, as captured by the Wayback Machine, https://web.archive.org/web/20250718005029/https://signalscv.com/2022/07/atkins-to-resign-from-scv-water-board/: "My letter of resignation went in two days ago," he told The Signal on Wednesday. "It becomes effective midnight on the 20th." The board\'s approved minutes of July 19, 2022 record him absent and direct staff "to start the process for appointment for the Division 3 seat vacated by Director Atkins".',
     ['My letter of resignation went in two days ago," he told The Signal on Wednesday. "It becomes effective midnight on the 20th.']),
    ('REL0908', 'SCV Water, "New SCV Water Board Member Appointed to Represent Division 3," news release, September 8, 2022: "During a special board meeting held on August 29, 2022, the SCV Water Board interviewed eleven applicants to fill the vacated seat"; "He was sworn in on September 8, 2022." Caleb Lunetta, The Signal, "Water board appoints new director for Division 3," August 30, 2022: "Petersen replaces former board member B.J. Atkins, who resigned because he moved out of the Santa Clarita Valley." The Signal dates the vote to "Tuesday" (August 30), the agency\'s release puts the meeting on August 29.',
     ['During a special board meeting held on August 29, 2022, the SCV Water Board interviewed eleven applicants to fill the vacated seat',
      'He was sworn in on September 8, 2022.', 'Petersen replaces former board member B.J. Atkins, who resigned because he moved out of the Santa Clarita Valley.']),
    ('EHI', 'Environmental HELP, Inc., "History," https://environmentalhelp.net/history/, read 6 October 2026 (his own company\'s account): "BJ Atkins is the Founder of Environmental HELP Inc."; "Upon graduation from Humboldt State University in 1978 with a degree in Oceanography & Economics"; "On May 5, 1989, Mr. Atkins founded Environmental HELP, Inc."',
     ['BJ Atkins is the Founder of Environmental HELP Inc.', 'Upon graduation from Humboldt State University in 1978 with a degree in Oceanography & Economics',
      'On May 5, 1989, Mr. Atkins founded Environmental HELP, Inc.']),
]
EXTRA = {  # quotations inside a note that come from a second saved source
    'SN12': [('NM353', 'BJ Atkins, Vice President'), ('NCWD17', 'B. J. Atkins, Vice President')],
    'SG22': [('MIN0719', 'to start the process for appointment for the Division 3 seat vacated by Director Atkins'.replace('Director Atkins', 'Director Atkins'))],
    'REL0908': [('SG0830', 'appointed a new director for Division 3 on Tuesday')],
}

bad = []
for key, note, quotes in NOTES:
    for q in quotes:
        src = {'in January 2009': 'SG22', 'B.J. ATKINS 2009': 'CLWADIR', 'Petersen replaces': 'SG0830'}.get(next((p for p in ('in January 2009', 'B.J. ATKINS 2009', 'Petersen replaces') if q.startswith(p)), None), key)
        if norm(q) not in CACHE[src]:
            bad.append(f'{key}: not found in {SRC[src].name}: {q}')
    for src, q in EXTRA.get(key, []):
        if norm(q) not in CACHE[src]:
            # the minutes break the line inside the quotation; accept a match across the break
            if norm(q).replace(' ', '') not in CACHE[src].replace(' ', ''):
                bad.append(f'{key}: not found in {SRC[src].name}: {q}')
# the mirror's list of CLWA directors and the 2012 table live in their own files
for src, q in [('CLWADIR', 'B.J. ATKINS 2009'), ('CLWA12', 'B.J. Atkins NCWD January 2013'), ('SB634', 'shall initially consist of 15 members')]:
    if norm(q) not in CACHE[src]:
        bad.append(f'{src}: not found: {q}')

order = [k for k, _, _ in NOTES]
num = {k: i + 1 for i, k in enumerate(order)}
R = lambda *ks: ''.join(f'[{num[k]}]' for k in ks)

BODY = '\n\n'.join([
    f'B. J. Atkins served on the Santa Clarita Valley\'s water boards for more than sixteen years, from 2005 to 2022: on the board of the Newhall County Water District, on the board of the Castaic Lake Water Agency as the Newhall district\'s appointee, and on the board of the Santa Clarita Valley Water Agency that succeeded them both.{R("RES")}',
    f'He was first elected to the Newhall County Water District board on 8 November 2005, third of six candidates for three seats, with 4,061 votes; his ballot designation was "Environmental Consultant."{R("SV05")} He was re-elected on 3 November 2009, first of four candidates for three seats with 1,743 votes,{R("SV09")} and on 5 November 2013, second of four by the election-night count.{R("SN13")} The board made him its president in 2008 and 2010, and he was its vice president in 2012, 2013 and 2017.{R("SN12")} In January 2013 he and the board\'s president, Maria Gutzeit, appeared on SCVTV\'s "Newsmaker of the Week" to discuss the Castaic Lake Water Agency\'s acquisition of the Valencia Water Company.{R("NM353")}',
    f'In November 2008 he was appointed to the Castaic Lake Water Agency board in the seat the Newhall County Water District held on it as one of three retail water purveyors, each of which named a director, and he was the district\'s liaison to the agency. He sat on that board from January 2009 until the agency ended in December 2017.{R("RES", "CLWA12", "SN12")}',
    f'He supported the merger of the two agencies into the Santa Clarita Valley Water Agency,{R("RES")} and when it began on 1 January 2018 he was one of its fifteen founding directors, seated by the act that created it, which made the five sitting directors of the Newhall district members of its first board.{R("SB634")} He was re-elected for Division 3 on 3 November 2020, first of four candidates for two seats, with 16,883 votes.{R("SOV20")} He also chaired the Santa Clarita Valley Groundwater Sustainability Agency.{R("SN20")}',
    f'In December 2020 he said he would resign, as he would be moving out of Division 3;{R("SN20")} construction delays put the move back.{R("SN21")} He resigned in July 2022. "My letter of resignation went in two days ago," he told The Signal on 13 July. "It becomes effective midnight on the 20th."{R("SG22")} The agency\'s resolution honoring him gives his service on its board as ending on 19 July 2022.{R("RES")} At the end of August the board chose Kenneth Petersen to serve the rest of the term.{R("REL0908")}',
    f'Outside the water boards he is an environmental consultant. By his own company\'s account he graduated from Humboldt State University in 1978 with a degree in Oceanography and Economics, and founded Environmental HELP, Inc., on 5 May 1989.{R("EHI")}',
])

PERSON = 28316
NCWD, CLWA, SCVW, DIV3 = 27534, 26563, 402, 25369
FOUNDING = 'Seated on the founding board of the Santa Clarita Valley Water Agency on January 1, 2018 by SB 634 (2017), which made the sitting directors of the merged agencies its first board: neither an election nor an appointment.'
note = {k: n for k, n, _ in NOTES}
HOLDINGS_NEW = [
    {'body': NCWD, 'district': None, 'seatLabel': '', 'termStart': 'December 2005', 'termStartEdtf': '2005-12', 'termEnd': 'December 2009', 'termEndEdtf': '2009-12',
     'selectionMethod': 'elected', 'howEnded': 'reelected', 'startEvidence': 'roster', 'endEvidence': 'derived',
     'footnotes': ['Elected on November 8, 2005, third of 6 candidates for 3 seats, with 4,061 votes. ' + note['SV05'],
                   note['RES'].split(': "was elected')[0] + ': "was elected to the Newhall County Water District Board of Directors in November of 2005 and served through December 2017".',
                   'The district\'s directors took their seats in December after a November election ("was elected to the Newhall County Water District Board of Directors in November of 2003 and took her seat in December of 2003": Santa Clarita Valley Water Agency, Resolution No. SCV-189, honoring Maria Gutzeit, December 15, 2020), so the months of this term are derived.']},
    {'body': NCWD, 'district': None, 'seatLabel': '', 'termStart': 'December 2009', 'termStartEdtf': '2009-12', 'termEnd': 'December 2013', 'termEndEdtf': '2013-12',
     'selectionMethod': 'elected', 'howEnded': 'reelected', 'startEvidence': 'roster', 'endEvidence': 'derived',
     'footnotes': ['Re-elected on November 3, 2009, first of 4 candidates for 3 seats, with 1,743 votes. ' + note['SV09'], note['SN12']]},
    {'body': NCWD, 'district': None, 'seatLabel': '', 'termStart': 'December 2013', 'termStartEdtf': '2013-12', 'termEnd': 'December 31, 2017', 'termEndEdtf': '2017-12-31',
     'selectionMethod': 'elected', 'howEnded': 'left', 'startEvidence': 'contemporary', 'endEvidence': 'retrospective',
     'footnotes': ['Re-elected on November 5, 2013, second of 4 candidates for 3 seats, with 1,047 votes by the election-night count. ' + note['SN13'],
                   'He was the board\'s vice president at its last regular meeting of record, December 14, 2017 ("B. J. Atkins, Vice President": minutes of the regular meeting of the Board of Directors of Newhall County Water District, printed in the Santa Clarita Valley Water Agency\'s board agenda of January 2, 2018, legacy file /scvhistory/files/scvwa010218agenda/, page 5).',
                   'The agency ended on January 1, 2018, when it merged into the Santa Clarita Valley Water Agency.']},
    {'body': CLWA, 'district': None, 'seatLabel': 'Newhall County Water District', 'termStart': 'January 2009', 'termStartEdtf': '2009-01', 'termEnd': 'December 31, 2017', 'termEndEdtf': '2017-12-31',
     'selectionMethod': 'appointed', 'howEnded': 'left', 'startEvidence': 'retrospective', 'endEvidence': 'retrospective',
     'footnotes': [note['RES'].split(': "was elected')[0] + ': "was appointed to the Castaic Lake Water Agency Board of Directors in November 2008 and served through December 2017".',
                   note['CLWA12'],
                   'The agency ended on January 1, 2018, when it merged into the Santa Clarita Valley Water Agency. SB 634 carried only the purveyor seat of Los Angeles County Waterworks District No. 36 onto the new board; the Newhall County Water District\'s directors joined it directly.']},
    {'body': SCVW, 'district': DIV3, 'seatLabel': 'Division 3', 'termStart': 'January 1, 2018', 'termStartEdtf': '2018-01-01', 'termEnd': 'January 2021', 'termEndEdtf': '2021-01',
     'selectionMethod': 'succeeded', 'howEnded': 'reelected', 'startEvidence': 'retrospective', 'endEvidence': 'derived',
     'footnotes': [note['RES'].split(': "was elected')[0] + ': "served on the Santa Clarita Valley Water Agency (Agency) Board of Directors from January 2018".',
                   note['SB634'],
                   FOUNDING,
                   'The initial terms of the founding directors ran to the 2020 or the 2022 general election (SB 634, Section 8(d)). He stood for Division 3 in 2020 as a sitting director and was re-elected (see the next term); "He has served on the SCV Water board since its start" (Tammy Murga, The Signal, December 25, 2020, as carried by SCVNews.com, https://scvnews.com/scv-water-board-director-bj-atkins-plans-to-resign/).']},
]
for q in ['He has served on the SCV Water board since its start']:
    if norm(q) not in CACHE['SN20']:
        bad.append('SN20: ' + q)
if norm('took her seat in December of 2003') not in norm(text(N1 / 'scvwater-resolution-scv-189.pdf')):
    bad.append('SCV-189 quotation not found')

# #28415: footnote 2 is stale ("when this term ended, and how, is not recorded"), footnote 4's appointment date carries no citation
H28415 = {
    'replace': {
        '2': 'He resigned with effect from July 20, 2022, before the end of the term. ' + note['SG22'],
        '4': 'The agency\'s resolution honoring him gives his service as ending on July 19, 2022. ' + note['RES'].split(' The number')[0] + ' The board filled the seat by appointment: ' + note['REL0908'],
    },
    'keep': ['1', '3'],
}

allown = BODY + json.dumps(NOTES) + json.dumps(HOLDINGS_NEW) + json.dumps(H28415)
if '—' in allown:
    bad.append('an em dash in our own text')

D = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_atkins_profile_2026_10_06.py, Claude Code',
    'person': {'id': PERSON, 'title': 'BJ Atkins'},
    'body': BODY,
    'footnotes': [{'number': str(num[k]), 'key': k, 'note': n} for k, n, _ in NOTES],
    'fields': {'bodyAuthorship': 'editorial-2026', 'occupation': 'Environmental consultant', 'aliasesAdd': ['B. J. Atkins', 'B.J. Atkins']},
    'holdingsNew': HOLDINGS_NEW,
    'holding28415': H28415,
    'bodies': {'NCWD': NCWD, 'CLWA': CLWA, 'SCVW': SCVW, 'DIV3': DIV3},
    'quoteCheck': bad or 'every quotation found in its saved source',
}
out = ROOT / 'inventory/review/bj-atkins-profile-draft-2026-10-06.json'
out.write_text(json.dumps(D, indent=1, ensure_ascii=False))

md = ['# BJ Atkins #28316: profile draft (6 October 2026)\n',
      'For Nathan. Drafted by Claude Code from saved copies of every source (inventory/news/bj-atkins-2026-10-06/, inventory/news/term-endings-2026-10-04/). Living person: public life only. Quotation check: ' + (('FAILED: ' + '; '.join(bad)) if bad else 'every quotation found in its saved source') + '.\n',
      '## The board service, settled\n',
      '| Board | Seat | From | To | Began | Ended | Source |', '|---|---|---|---|---|---|---|',
      '| Newhall County Water District | at large | Dec 2005 | Dec 2009 | elected 8 Nov 2005, 3rd of 6 for 3 seats, 4,061 votes | re-elected | Smart Voter (County returns), 2005 |',
      '| Newhall County Water District | at large | Dec 2009 | Dec 2013 | elected 3 Nov 2009, 1st of 4 for 3 seats, 1,743 votes | re-elected | Smart Voter, 2009 |',
      '| Newhall County Water District | at large | Dec 2013 | 31 Dec 2017 | elected 5 Nov 2013, 2nd of 4 for 3 seats, 1,047 votes (election night) | district merged into SCV Water | SCVNews, 5 Nov 2013 |',
      '| Castaic Lake Water Agency | purveyor seat (NCWD) | Jan 2009 | 31 Dec 2017 | appointed Nov 2008 | agency merged into SCV Water | SCV Water resolution, 2022; CLWA 2012 report; mirror list |',
      '| Santa Clarita Valley Water | Division 3 | 1 Jan 2018 | Jan 2021 | founding director by SB 634 | re-elected 3 Nov 2020 | SCV Water resolution; SB 634 |',
      '| Santa Clarita Valley Water | Division 3 | Jan 2021 | 20 Jul 2022 | elected 3 Nov 2020, 1st of 4 for 2 seats, 16,883 votes | resigned, effective "midnight on the 20th" | County SOV 2020; The Signal 13 Jul 2022; SCV Water resolution (to 19 Jul 2022) |',
      '',
      '## Body\n', BODY, '',
      '## Notes\n'] + [f'{num[k]}. {n}' for k, n, _ in NOTES] + [
      '', '## Office holdings: five new\n'] + [
      f"- {h['termStartEdtf']} to {h['termEndEdtf']}, body #{h['body']}{' / district #' + str(h['district']) if h['district'] else ''}, {h['selectionMethod']}, ended {h['howEnded']}, evidence {h['startEvidence']}/{h['endEvidence']}" + ''.join(f"\n  - note {i+1}: {f}" for i, f in enumerate(h['footnotes'])) for h in HOLDINGS_NEW] + [
      '', '## #28415 (SCV Water, 2021 to 2022): two notes rewritten\n',
      '- note 2 (now: "He did not stand in 2024 ... when this term ended, and how, is not recorded", which the end already applied contradicts) becomes: ' + H28415['replace']['2'],
      '- note 4 (its Petersen date carries no citation) becomes: ' + H28415['replace']['4'],
      '- notes 1 and 3 stand; termEnd 2022-07-20, howEnded resigned, endEvidence contemporary stand.', '',
      '## Person fields\n', '- bodyAuthorship editorial-2026; occupation "Environmental consultant"; aliases B. J. Atkins, B.J. Atkins. Portrait not touched.', '',
      '## What the mirror holds on him\n',
      '- /scvhistory/clwadirectors.htm, "Castaic Lake Water Agency Directors, 1962 to Date": "B.J. ATKINS 2009" with an open end. Used (note 7).',
      '- /scvhistory/files/clwa_scwd_2012/ page 6, the CLWA and Santa Clarita Water Division 2012 report: "B.J. Atkins NCWD January 2013", the purveyor seats explained. Used (note 7).',
      '- /scvhistory/files/scvwa010218agenda/ pages 5 and 6, SCV Water board agenda of 2 January 2018, printing the NCWD minutes of 14 December 2017: "The following Directors were absent: B. J. Atkins, Vice President"; directors\' reports, "CLWA Board meeting report (Atkins)", "BJ Atkins: SCV-GSA Board Meeting November 8; VIA Monthly Luncheon November 21". Used (note 5).',
      '- /scvhistory/files/scvwa_sb634/ page 10, the text of SB 634: the founding board. Used (note 8).',
      '- /scvhistory/signal/newsmaker/index.htm: "Episode 353 | Maria Gutzeit & BJ Atkins, NCWD", linking to scvtv.com/html/notw353.html (21 January 2013, topic "CLWA Acquisition of Valencia Water Co."; the video page only, no transcript). Used (note 6).',
      '- /scvhistory/files/via20141108/ page 10, Valley Industry Association Winter Wonderland Bash program, 8 November 2014: "BJ Atkins (1)" in a list of names. Passing mention; not used.',
      '- Photograph credits, the Harold G. Deines collection: hg5601, hg5602, hg5603, hg5801, hg5901, hg5902, hg_lat19621118, each "courtesy of Myrna Deines Litt and B.J. Atkins" (or "Myrna Litt and B.J. Atkins"). Newhall School 1950s, Melody Ranch, Hart Park, Vasquez Rocks, an L.A. Times clipping of 1962. None of these pages is in Craft yet. Not used: nothing on the page says this B.J. Atkins is the water director, and the link to the Deines family would be family history. For Nathan.',
      '- No Signal story about him, no photograph of him, no page about him. The Signal and SCVNews pieces used here are from the live sites and the Wayback Machine, not the mirror. "Billy Atkins" and "Bill Atkins" in College of the Canyons\' Canyon Call (1973 to 1976) and "Atkins Billy H" in a 1969 directory are not shown to be him; not used.',
      '',
      '## Left out, by the brief\n',
      '- The company page\'s account of Sun Exploration and Production (from 1978), the Bakersfield consulting work (1988) and the firm\'s later work: supportable, attributed, but left out as the business narrative.',
      '- His reasons for moving and where (The Signal names San Diego County and his grandchildren): family and private life. The body says only that he was moving out of Division 3, which is why the seat fell vacant.',
      '- "A resident of the Santa Clarita Valley since 1966" (The Signal, 25 December 2020): private biography; held for Nathan.',
    ]
(ROOT / 'inventory/review/bj-atkins-profile-draft-2026-10-06.md').write_text('\n'.join(md) + '\n')
print('quote check:', 'OK' if not bad else 'FAILED')
for b in bad:
    print('  ', b)
sys.exit(1 if bad else 0)
