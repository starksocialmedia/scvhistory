import json, hashlib, re, os, sys

D = os.path.dirname(os.path.abspath(__file__))

def norm(s):
    s = s.replace('‘', "'").replace('’', "'").replace('“', '"').replace('”', '"')
    s = s.replace(' ', ' ')
    return re.sub(r'\s+', ' ', s).strip()

# url, published, title, byline, ext
SRC = {
 'plambeck-resolution-scv-321': ('https://www.YourSCVWater.com/sites/default/files/SCVWA/approved-resolutions/SCV-Water-Approved-Resolution-122022-Resolution-SCV-321.pdf', '2022-12-20', 'Resolution No. SCV-321: Honoring and Commending Lynne Plambeck for Her Service and Dedication', 'Santa Clarita Valley Water Agency Board of Directors', 'pdf'),
 'plambeck-4-more-years': ('https://scvnews.com/4-more-years-for-plambeck-mortensen-on-water-board/', None, '4 More Years for Plambeck, Mortensen on Water Board', 'Leon Worden, SCVNews.com', 'html'),
 'scv-water-board-member-ed-colley-resigns-seat': ('https://scvnews.com/scv-water-board-member-ed-colley-resigns-seat', None, 'SCV Water Board Member Ed Colley Resigns Seat', 'Press Release (SCV Water)', 'html'),
 'clwa-candidates-file': ('https://scvnews.com/candidates-file-for-clwa-governing-board-election', None, 'Candidates File For CLWA Governing Board Election', 'KHTS, Hometownstation.com', 'html'),
 'scvwater-board': ('https://yourscvwater.com/governance/board-directors', None, 'Board of Directors | Santa Clarita Valley Water', 'Santa Clarita Valley Water Agency (official page)', 'html'),
 'scv-water-2019-leadership': ('https://www.yourscvwater.com/sites/default/files/SCVWA/newscenter/Press%20Release/2019/Press-Release-SCV-Water-Board-Leadership-in-2019-020719.pdf', '2019-02-07', 'SCV Water Board Leadership in 2019 (news release)', 'SCV Water', 'pdf'),
 'scv-water-2020-leadership': ('https://www.yourscvwater.com/sites/default/files/SCVWA/newscenter/Press%20Release/2020/2020.02.12-SCV-Water-2020-Board-Leadership.pdf', '2020-02-12', 'SCV Water 2020 Board Leadership: Gary Martin Elected as Board President (news release)', 'SCV Water', 'pdf'),
 'scv-water-2021-leadership': ('https://www.yourscvwater.com/sites/default/files/SCVWA/newscenter/Press%20Release/2021/SCV-Water-Seats-2021-Board-Leadership.pdf', '2021-01-08', 'SCV Water Seats 2021 Board Leadership (news release)', 'SCV Water', 'pdf'),
 'scv-water-2023-leadership': ('https://www.YourSCVWater.com/sites/default/files/SCVWA/newscenter/Press%20Release/2023/SCV-Water-2023-Board-Leadership-5th-Anniversary-011923.pdf', '2023-01-19', 'SCV Water Seats 2023 Board Leadership and Celebrates its Fifth Anniversary (news release)', 'SCV Water', 'pdf'),
 'dcdca-martin': ('https://dcdca.org/?p=4605', None, 'Gary Martin - DCA', 'Delta Conveyance Design and Construction Authority (official page)', 'html'),
 'kavounas-quits-clwa': ('https://scvnews.com/kavounas-quits-clwa-agency-taking-names-to-fill-seat', None, 'Kavounas Quits CLWA; Agency Taking Names to Fill Seat', 'Perry Smith, Hometownstation.com', 'html'),
 'clwa-elects-2017-officers': ('https://scvnews.com/clwa-elects-board-president-vice-president', None, 'CLWA Elects Board President, Vice President', 'Press Release (CLWA)', 'html'),
 'longtime-susd-board-members-farewell': ('https://scvnews.com/longtime-susd-governing-board-members-bid-farewell', None, 'Longtime SUSD Governing Board Members Bid Farewell', 'Press Release (Saugus Union SD)', 'html'),
 'de-la-cerda-head-saugus': ('https://scvnews.com/de-la-cerda-selected-to-head-saugus-school-board/', None, 'De La Cerda Selected to Head Saugus School Board', 'Press Release (Saugus Union SD)', 'html'),
 'bryce-saugus-trustee-leaving-sept-30-for-san-diego': ('https://scvnews.com/bryce-saugus-trustee-leaving-sept-30-for-san-diego', None, 'Bryce, Saugus Trustee, Leaving Sept. 30 for San Diego', 'Press Release (Saugus Union SD)', 'html'),
 'bryce-moving-to-san-diego': ('https://scvnews.com/bryce-of-saugus-school-board-moving-to-san-diego', None, 'Bryce, of Saugus School Board, Moving to San Diego', 'SCVNews.com', 'html'),
 'trunkey-to-fill-vacancy': ('https://scvnews.com/trunkey-to-fill-vacancy-on-saugus-school-board/', None, 'Trunkey to Fill Vacancy on Saugus School Board', 'Perry Smith, Hometownstation.com', 'html'),
 'scvnews-election-results': ('https://scvnews.com/election-results', None, 'ELECTION RESULTS: Upset in Sulphur Springs', 'SCVNews.com', 'html'),
 'strickland-moving-away-quits-hart-board-arts-commission': ('https://scvnews.com/strickland-moving-away-quits-hart-board-arts-commission', None, 'Strickland Moving Away; Quits Hart Board, Arts Commission', 'Hart School District (release)', 'html'),
 'hart-taps-chris-fall': ('https://scvnews.com/hart-board-taps-chris-fall-to-fill-strickland-seat', None, 'Hart Board Taps Chris Fall to Fill Strickland Seat', 'Perry Smith, Hometownstation.com', 'html'),
 'hart-vacancy-agenda': ('https://scvnews.com/vacancy-teacher-honorees-on-agenda-for-hart-board', None, 'Vacancy, Teacher Honorees on Agenda for Hart Board', 'Perry Smith, Hometownstation.com', 'html'),
 'chris-fall-quits': ('https://scvnews.com/chris-fall-quits-hart-board-due-to-business-conflict', None, 'Chris Fall Quits Hart Board Due to Business Conflict', 'Hart School District (release)', 'html'),
 'messina-second-term': ('https://scvnews.com/messina-hart-school-board-prez-to-seek-2nd-term', None, 'Messina, Hart School Board Prez, to Seek 2nd Term', 'Press Release (Messina campaign)', 'html'),
 'ballotpedia-messina': ('https://ballotpedia.org/Joe_Messina', None, 'Joe Messina - Ballotpedia', 'Ballotpedia', 'html'),
 'hart': ('https://www.hartdistrict.org/apps/pages/governing-board-members', None, 'Governing Board Members - William S. Hart Union High School District', 'Hart district (official page)', 'html'),
 'susd-new-president-clerk-sworn-in': ('https://scvnews.com/susd-appoints-new-president-clerk-new-members-sworn-in', None, 'SUSD Appoints New President, Clerk; New Members Sworn In', 'Press Release (Saugus Union SD)', 'html'),
 'signal-arrowsmith-resigns': ('https://signalscv.com/2022/09/saugus-district-board-member-resigns/', None, 'Saugus district board member resigns', 'Jose Herrera, The Signal', 'html'),
 'signal-arrowsmith-hands-of-voters': ('https://signalscv.com/2022/09/in-the-hands-of-the-voters/', None, 'In the hands of the voters', 'Jose Herrera, The Signal', 'html'),
 'ballotpedia-arrowsmith': ('https://ballotpedia.org/Laura_Arrowsmith', None, 'Laura Arrowsmith - Ballotpedia', 'Ballotpedia', 'html'),
 'newhall-members': ('https://www.newhallschooldistrict.com/governing-board-members', None, 'Newhall School District - Governing Board Members', 'Newhall School District (official page)', 'html'),
 'newhall-officers-2014': ('https://scvnews.com/newhall-school-board-selects-officers-for-2014', None, 'Newhall School Board Selects Officers for 2014', 'KHTS, Hometownstation.com', 'html'),
 'castaic-board': ('https://www.castaicusd.com/apps/pages/index.jsp?uREC_ID=799367&type=d&pREC_ID=1188877', None, 'Meet Our Board of Trustees - Castaic Union School District', 'Castaic Union SD (official page)', 'html'),
 'signal-castaic-2017-officers': ('https://signalscv.com/2017/12/castaic-governing-board-elects-new-president-clerk/', None, 'Castaic Governing Board elects new president, clerk', 'Christina Cox, The Signal', 'html'),
 'signal-castaic-2018-elections': ('https://signalscv.com/2018/04/castaic-union-to-hold-elections-for-four-seats-in-november/', None, 'Castaic Union to hold elections for four seats in November', 'Perry Smith, The Signal', 'html'),
 'huffaker-castaic': ('https://scvnews.com/huffaker-takes-helm-of-castaic-school-board', None, 'Huffaker Takes Helm of Castaic School Board', 'Allison Pari, Hometownstation.com', 'html'),
 'castaic-seeks-area-d': ('https://scvnews.com/castaic-school-district-seeks-new-board-member-for-area-d/', None, 'Castaic School District Seeks New Board Member for Area D', 'Press Release (Castaic Union SD)', 'html'),
 'sulphur-board': ('https://www.sssd.k12.ca.us/members', None, 'Sulphur Springs Union School District - Members', 'Sulphur Springs Union SD (official page)', 'html'),
 'clegg-2014-president': ('https://scvnews.com/clegg-to-lead-sulphur-springs-school-board-in-2014', None, 'Clegg to Lead Sulphur Springs School Board in 2014', 'Allison Pari, Hometownstation.com', 'html'),
 'signal-clegg-goodbye': ('https://signalscv.com/2018/12/community-says-goodbye-to-longtime-board-member-kerry-clegg/', None, 'Community says goodbye to longtime board member Kerry Clegg', 'Brennon Dixson, The Signal', 'html'),
 'winkler-wont-fight': ('https://scvnews.com/winkler-says-he-wont-fight-boards-decision-to-oust-him', None, "Winkler Says He Won't Fight Ouster from School Board", 'Perry Smith, Hometownstation.com', 'html'),
 'canyons-mercado-fortine-bio': ('https://www.canyons.edu/community/womensconference/bio-gloriamercado-fortine.php', None, 'Gloria Mercado-Fortine (Women\'s Conference speaker bio)', 'College of the Canyons (official page)', 'html'),
 'scmag-2015-hart-candidates': ('https://santaclarita.mydigitalpublication.com/article/Meet+The+Candidates+Hart+District+Governing+Board+Election+Heats+Up/2275233/273663/article.html', '2015-10', 'Meet The Candidates: Hart District Governing Board Election Heats Up (Santa Clarita Magazine, October 2015)', 'Candidate statements, Santa Clarita Magazine', 'html'),
 'boyer2015ch14-page5': ('https://scvhistory.com/scvhistory/files/boyer2015ch14/files/basic-html/page5.html', '2015', 'Carl Boyer, Santa Clarita (2015), chapter 14, p. 204 (flipbook page 5)', 'Carl Boyer', 'html'),
}

NOTES_SRC = {
 'signal-arrowsmith-resigns': 'signalscv.com returns 403 to curl. Article paragraphs were copied verbatim from the page rendered in Chrome on 2026-10-03 into a minimal HTML file; htmlSha256 is of that file, not of the live page.',
 'signal-arrowsmith-hands-of-voters': 'As signal-arrowsmith-resigns: browser-extracted paragraphs, some omitted.',
 'signal-castaic-2017-officers': 'Browser-extracted paragraphs (Signal blocks curl).',
 'signal-castaic-2018-elections': 'Browser-extracted paragraphs (Signal blocks curl).',
 'signal-clegg-goodbye': 'Browser-extracted first paragraph only (Signal blocks curl).',
 'scvwater-board': 'Copied from the 2026-10-03 recount scratchpad. Ed Colley\'s biography ("has served on the CLWA Board of Directors since 2003") is in this HTML only inside an HTML comment, so it is not in the .txt and is not quoted.',
 'boyer2015ch14-page5': 'Copied from the read-only legacy mirror (/Volumes/Reggie/SCVHistory/scvhistory.com), not fetched live.',
 'hart': 'Copied from the 2026-10-03 recount scratchpad (fetched that day).',
 'newhall-members': 'Copied from the 2026-10-03 recount scratchpad.',
 'sulphur-board': 'Copied from the 2026-10-03 recount scratchpad.',
 'castaic-board': 'Copied from the 2026-10-03 recount scratchpad.',
 'scv-water-board-member-ed-colley-resigns-seat': 'Copied from the 2026-10-03 recount scratchpad.',
 'bryce-saugus-trustee-leaving-sept-30-for-san-diego': 'Copied from the 2026-10-03 recount scratchpad.',
 'strickland-moving-away-quits-hart-board-arts-commission': 'Copied from the 2026-10-03 recount scratchpad.',
}

def E(src, quote):
    return {'source': src, 'quote': quote}

def H(body, start, startEdtf, selection, end, endEdtf, howEnded, offices, evidence, confidence, **extra):
    h = {'body': body, 'start': start, 'startEdtf': startEdtf, 'selection': selection, 'end': end,
         'endEdtf': endEdtf, 'howEnded': howEnded, 'offices': offices, 'evidence': evidence, 'confidence': confidence}
    h.update(extra)
    return h

PEOPLE = [
 {'id': 15897, 'name': 'Lynne Plambeck', 'holdings': [
   H('Newhall County Water District', 'November 1993', '1993-11', 'elected', 'December 1997', '1997-12', 'expired', [],
     [E('plambeck-resolution-scv-321', 'Lynne Plambeck was elected to the Newhall County Water District (NCWD) Board of Directors in November of 1993 and served through December of 1997'),
      E('plambeck-4-more-years', 'Plambeck has served on the NCWD board since 1993 except for a two-year period (1998-1999) when a challenger unseated her')],
     'two sources', endDetail='lost reelection, November 1997'),
   H('Newhall County Water District', 'December 1999', '1999-12', 'elected', 'December 2017', '2017-12', 'left',
     ['vice president 2002', 'president 2004'],
     [E('plambeck-resolution-scv-321', 'was re-elected to the Newhall County Water District Board of Directors in November of 1999 and served through December of 2017'),
      E('plambeck-resolution-scv-321', 'Ms. Plambeck served as NCWD Board Vice President in 2002 and Board President in 2004'),
      E('plambeck-4-more-years', 'the "appointment in lieu of election" of Plambeck and Mortensen to another term on the Newhall County Water District board'),
      E('plambeck-4-more-years', 'Plambeck and Mortensen were last elected in 2007')],
     'two sources', endDetail='NCWD ceased to exist when it merged into SCV Water on Jan. 1, 2018; she moved to the SCV Water board. Re-elected 2003 and 2007; seated in lieu of election 2011 (Board of Supervisors appointment, no opponent). The resolution says "re-elected ... in November of 1999", so the startEdtf month of seating (December) is inferred from the usual December seating.'),
   H('Santa Clarita Valley Water Agency', 'January 2018', '2018-01', 'appointed', 'December 2022', '2022-12', 'expired', [],
     [E('plambeck-resolution-scv-321', 'Ms. Plambeck served on the Santa Clarita Valley Water Agency (Agency) Board of Directors from January 2018 to December 2022')],
     'one source', selectionDetail='Not elected or appointed in the usual sense: the inaugural SCV Water board was made up of the sitting directors of the merged agencies (CLWA, NCWD) under the enabling legislation (SB 634). Choose the closest value your schema allows; "appointed" is a placeholder.', endDetail='lost the November 2022 Division 3 election to Maria Gutzeit (county returns: Gutzeit 13,633, Plambeck 12,846; inventory/elections/county-svc-water.json)')],
  'notes': 'Correction to the claim: NCWD service was NOT continuous 1993 to 2017. She lost in 1997 and was off the board 1998 to 1999. So two NCWD holdings. The resolution totals 27 years of service (NCWD plus SCV Water).'},

 {'id': 26589, 'name': 'Ed Colley', 'holdings': [
   H('Castaic Lake Water Agency', '2003', '2003', 'elected', 'December 2017', '2017-12-31', 'left', [],
     [E('scv-water-board-member-ed-colley-resigns-seat', 'Colley was first elected to the Castaic Lake Water Agency Board of Directors in 2003. He continued as a board member when SCV Water was formed on Jan. 1, 2018.'),
      E('clwa-candidates-file', 'Brooks is scheduled to run against incumbent Ed Colley to represent division one')],
     'one source', endDetail='CLWA ceased to exist Jan. 1, 2018 (merged into SCV Water).', startDetail='"First elected ... in 2003" is the release\'s wording. CLWA elections were held in November of even-numbered years (clwa-elects-2017-officers), so this is probably the November 2002 election with seating in January 2003, or a 2003 appointment. Not resolved. Recognized in Aug 2024 for "nearly 22 years of service on water boards", which fits a start in late 2002 or early 2003. Division 1 incumbent in 2014.'),
   H('Santa Clarita Valley Water Agency', 'January 1, 2018', '2018-01-01', 'appointed', 'August 7, 2024', '2024-08-07', 'resigned', [],
     [E('scv-water-board-member-ed-colley-resigns-seat', 'He continued as a board member when SCV Water was formed on Jan. 1, 2018.'),
      E('scv-water-board-member-ed-colley-resigns-seat', 'resigned his seat effective Wednesday, Aug. 7. Colley\'s resignation was prompted by his planned move out of his district, and California, to Texas.')],
     'one source', selectionDetail='Inaugural director carried over from CLWA (not a fresh election). Re-elected Division 2 in November 2020 (county returns: ED COLLEY 22,703, first of two seats; inventory/elections/county-svc-water.json). "appointed" is a placeholder.')],
  'notes': 'Only one readable independent text for the dates (the SCV News release, Aug 8, 2024). The agency\'s own bio saying "has served on the CLWA Board of Directors since 2003" exists only inside an HTML comment in scvwater-board.html, so it agrees but cannot be quoted from the .txt. The 2014 CLWA article confirms he was an incumbent then. Do not confuse with Kathy Colley (NCWD, then SCV Water, lost 2022).'},

 {'id': 26587, 'name': 'Gary Martin', 'holdings': [
   H('Castaic Lake Water Agency', '2013', '2013', 'appointed', 'December 2017', '2017-12-31', 'left', ['vice president 2017'],
     [E('scv-water-2019-leadership', 'In 2013, he was initially selected as a member of the CLWA Board of Directors, where he filled an at-large vacancy.'),
      E('kavounas-quits-clwa', 'The process likely will be similar to the appointment process used in selecting board member Gary Martin, who was chosen for an at-large spot on the board.'),
      E('dcdca-martin', 'Gary also served on the board of SCV Water\'s predecessor agency, Castaic Lake Water Agency (CLWA) from 2013 to 2017.'),
      E('clwa-candidates-file', 'Fortner is expected to run against incumbents Gary Martin and Tom Campbell for a seat as a member at large'),
      E('scvwater-board', 'Mr. Martin was Vice President of the Castaic Lake Water Agency (CLWA) Board of Directors in 2017.')],
     'two sources', startDetail='Month not established: "February 2013" appears only in a search-engine summary, not in any text read. Appointed at-large 2013; ran as an at-large incumbent in the November 2014 election (result not in our data).', officesDetail='CLWA "vice president 2017" is from SCV Water\'s own bio, but the Jan 10, 2017 CLWA release (clwa-elects-2017-officers) names William Pecsi as vice president elected Jan 3, 2017. Possibly Martin succeeded Pecsi later in 2017; treat "vice president 2017" as one source in conflict.', conflictEvidence=[E('clwa-elects-2017-officers', 'the Board of Directors elected Robert J. DiPrimio as Board President and William Pecsi as Board Vice President')]),
   H('Santa Clarita Valley Water Agency', 'January 1, 2018', '2018-01-01', 'appointed', None, None, 'serving',
     ['vice president 2019', 'president 2020', 'president 2021', 'president 2022', 'president 2023', 'president 2024', 'vice president 2026'],
     [E('scv-water-2019-leadership', 'Mr. Martin was a member of the inaugural SCV Water Board of Directors.'),
      E('scv-water-2019-leadership', 'Maria Gutzeit and Gary Martin were appointed to serve as vice presidents.'),
      E('scv-water-2020-leadership', 'The SCV Water Board of Directors has selected Gary Martin to serve as SCV Water\'s president for its third year of operation.'),
      E('scv-water-2021-leadership', 'I am very honored to be re-elected by my colleagues to serve as the board president for the next two years'),
      E('scv-water-2023-leadership', 'He served as a Board Vice President in 2019 and as the Board President since January 2021.'),
      E('scv-water-2023-leadership', 'Gary Martin has been re-elected by the SCV Water Board of Directors to serve as board president for another two-year term.'),
      E('scvwater-board', 'Gary R. Martin - Vice President')],
     'two sources', selectionDetail='Inaugural director carried over from CLWA; elected Division 1 in November 2020 and re-elected November 2024 (county returns, inventory/elections/county-svc-water.json). "appointed" is a placeholder.', officesDetail='President from Feb 2020 (not 2021): the 2023 release says "since January 2021" but the Feb 12, 2020 release shows him elected president for 2020, and the 2021 release says "re-elected". Vice president as of the board page read 2026-10-03; when that began (2025?) is not established.')],
  'notes': 'Claim corrected: board president from 2020 (not 2021) through 2024; vice president now. The start month in 2013 is unverified.'},

 {'id': 25401, 'name': 'Judy Umeck', 'holdings': [
   H('Saugus Union School District', '1996', '1996', 'appointed', 'December 11, 2018', '2018-12-11', 'expired', ['president 2013'],
     [E('longtime-susd-board-members-farewell', 'Umeck, who was a member of the Saugus Board since first being appointed to fill a vacancy in 1996'),
      E('longtime-susd-board-members-farewell', 'Judy Egan Umeck, who served for 22 years, and Paul De La Cerda, who served for 13 years, left the Board following ceremonies at the Dec. 11, 2018 special meeting.'),
      E('de-la-cerda-head-saugus', 'Umeck begins her fifth term and was honored for her service as Board President for 2013.'),
      E('longtime-susd-board-members-farewell', 'She served the Governing Board as President and Clerk on multiple occasions')],
     'two sources', endDetail='lost the November 2018 Area 2 election to Laura Arrowsmith (CEDA: Arrowsmith 4,301, Umeck 3,198).', startDetail='"October 1996" is not in any text read; only the year 1996 is sourced. CEDA 1997 lists her designation as "SUSD Board Trustee", consistent with an appointed incumbent. Elected 1997, 2001, 2005, 2009, 2013 (fifth term from Dec 2013).')],
  'notes': 'Continuous 1996 to Dec 11, 2018. Month of appointment not established.'},

 {'id': 25403, 'name': 'Douglas Bryce', 'holdings': [
   H('Saugus Union School District', 'about 2001', '2001~', 'unknown', 'September 30, 2014', '2014-09-30', 'resigned', ['clerk 2014'],
     [E('bryce-saugus-trustee-leaving-sept-30-for-san-diego', 'Saugus Union School Board member Doug Bryce announced this week that he will be resigning from his position effective Sept. 30 due to a new career opportunity he has accepted in the San Diego area.'),
      E('bryce-saugus-trustee-leaving-sept-30-for-san-diego', 'Bryce has served as a Governing Board Trustee for the Saugus Union School District for more than 14 years.'),
      E('bryce-moving-to-san-diego', 'He has served on the Saugus School Board for 12 years'),
      E('de-la-cerda-head-saugus', 'Bryce begins his fourth term and was elected to the position of Board Clerk for 2014.'),
      E('trunkey-to-fill-vacancy', 'Bryce stepped down at the end of September in order to pursue a job opportunity in San Diego.')],
     'conflict', startDetail='Sources disagree on the start. May 2014: "12 years" (start about 2002). Sept 2014: "more than 14 years" (start before Sept 2000). Dec 2013: "fourth term" (terms from Dec 2001, 2005, 2009, 2013). The two that agree (12 years, fourth term) point to Dec 2001 (Saugus had no CEDA contest in 2001, so probably seated unopposed). The "more than 14 years" likely counts an EARLIER tenure too (see notes). selection unknown: elected unopposed in 2001 is likely but unsourced.')],
  'notes': 'CEDA evidence (inventory/elections/ceda-scv.json, not a .txt source): in the 1997 Saugus election "Douglas A. Bryce" ran with ballot designation "Incumbent" and LOST (1,131 votes; Murr, Myl, Umeck won). He also lost in 1999 as a non-incumbent. So he was on the board before November 1997 (probably appointed, dates unknown) until December 1997: a separate, earlier tenure not created here because no text gives its start. That earlier service would explain "more than 14 years". Trustee Chris Trunkey was appointed to his seat Nov 2014 (trunkey-to-fill-vacancy).'},

 {'id': 25425, 'name': 'Paul Strickland', 'holdings': [
   H('Sulphur Springs Union School District', 'December 1995', '1995-12', 'elected', None, None, 'unknown', [],
     [E('strickland-moving-away-quits-hart-board-arts-commission', 'Prior to serving on the Hart School District board, Strickland was an elected trustee for the Sulphur Springs School District.')],
     'one source', startDetail='Year from CEDA 1995 (won, 1,149 votes); seating month inferred.', endDetail='End not found. He ran for Hart in 1999 (lost) and won Hart in 2001; whether he left Sulphur Springs at the end of his term (Dec 1999) or later is unknown. Do not create an end date.'),
   H('William S. Hart Union High School District', 'December 2001', '2001-12', 'elected', 'May 1, 2013', '2013-05-01', 'resigned', ['president (year not stated)'],
     [E('strickland-moving-away-quits-hart-board-arts-commission', 'Since first elected to the school board in 2001'),
      E('strickland-moving-away-quits-hart-board-arts-commission', 'Hart School District elected school board member and past president, Paul Strickland, has tendered his resignation from the school board, with plans to move out of state in May.'),
      E('hart-taps-chris-fall', 'The seat was vacated May 1, when former board member Paul Strickland vacated his spot for a career opportunity in Florida.'),
      E('hart-vacancy-agenda', 'Fall was appointed by the Hart district to replace Paul Strickland, who resigned effective May 1 in order to pursue a career opportunity in Florida.')],
     'two sources', startDetail='Elected Nov 2001 (CEDA), re-elected 2005 and 2009 (CEDA; Ballotpedia).')],
  'notes': 'Resignation effective May 1, 2013 (two sources). Chris Fall was appointed June 2013 to the seat and resigned Aug 2013 (chris-fall-quits). Also chaired the City Arts Commission (served since Dec 2009), resigned April 2013. Ballotpedia still lists him among the 2013 Hart candidates, which is stale.'},

 {'id': 25415, 'name': 'Laura Arrowsmith', 'holdings': [
   H('Saugus Union School District', 'December 2018', '2018-12', 'elected', 'September 2022', '2022-09', 'resigned', ['president 2020', 'president 2021'],
     [E('susd-new-president-clerk-sworn-in', 'The three sworn in include two new trustees'),
      E('susd-new-president-clerk-sworn-in', 'Arrowsmith, a longtime history teacher in the Hart Union School District, joins the Board as Trustee for Area 2.'),
      E('signal-arrowsmith-resigns', 'Laura Arrowsmith, who represents Trustee Area No. 2 for the Saugus Union School District, announced her resignation Tuesday evening during a special session meeting, citing she would be moving away from the area.'),
      E('signal-arrowsmith-resigns', 'Superintendent Colleen Hawkins confirmed Arrowsmith resigned on Wednesday.'),
      E('signal-arrowsmith-resigns', 'Mrs. Arrowsmith served as board president during the remainder of Ms. (Julie) Olsen\'s term and also served as board president the following year'),
      E('signal-arrowsmith-hands-of-voters', 'It\'s too late for her name to be removed from the ballot'),
      E('signal-arrowsmith-hands-of-voters', 'We made the decision to move, as I later found out, after the deadline to remove my name from the ballot had passed.'),
      E('signal-arrowsmith-hands-of-voters', 'who indicated Arrowsmith\'s resignation occurred following the close of filing.')],
     'two sources', endDetail='Announced at a special meeting Tuesday Sept. 13, 2022; district confirmed she had resigned Wednesday Sept. 14 (article dated Sept. 14, 2022). Exact effective day not stated, so endEdtf is the month. Seat left vacant to the December organizational meeting.', officesDetail='President from mid-2020 (finishing Julie Olsen\'s presidential term; Olsen stepped down in July 2020 per a Signal headline not read) and for 2021. Year boundaries are approximate.', conflictDetail='Ballotpedia says "She left office on December 9, 2022", which is its default end-of-term date; the Signal reports a September resignation. Prefer the Signal.', conflictEvidence=[E('ballotpedia-arrowsmith', 'She left office on December 9, 2022.')])],
  'notes': 'Resolved: she resigned in September 2022 after the candidate filing deadline, so her name stayed on the November 2022 ballot. She did not campaign and lost to Anna Griese (CEDA: Arrowsmith 3,392, "incumbent", not elected). The CEDA row is hers and correct as a ballot fact; it is not a sign she served to December. The two Signal files are browser-copied article paragraphs (Signal blocks curl).'},

 {'id': 25385, 'name': 'Suzan Solomon', 'holdings': [
   H('Newhall School District', 'December 1999', '1999-12', 'elected', None, None, 'serving', ['clerk 2026', 'president (several years, not stated)'],
     [E('newhall-members', 'Mrs. Solomon was first elected to the Newhall School Board in 1999 and has served multiple times as school board president.'),
      E('newhall-members', 'Governing Board Clerk Trustee Area 5 Trustee Area 5 Current Term: 2024 - 2028'),
      E('newhall-officers-2014', 'Continuing service on the board is 10-year board member Mike Shapiro and 14-year veteran Suzan Solomon.')],
     'two sources', startDetail='Elected Nov 1999 (CEDA); re-elected 2003, 2007 (CEDA), 2011 and later without a CEDA contest (presumably uncontested), 2024 Area 5 (CEDA).')],
  'notes': 'Continuous since 1999. Her 2009 run for the Hart board (lost) was while she sat on the Newhall board and did not interrupt it. "14-year veteran" in Dec 2013 fits continuity.'},

 {'id': 25379, 'name': 'Laura Pearson', 'holdings': [
   H('Castaic Union School District', 'December 2005', '2005-12', 'elected', None, None, 'serving', ['clerk 2013', 'president 2018', 'president 2026'],
     [E('signal-castaic-2017-officers', 'This marks the third time Pearson, first elected to the board in 2005, will serve as the president of the CUSD Governing Board.'),
      E('signal-castaic-2017-officers', 'The Castaic Union School District elected Laura Pearson as its new president and Stacy Dobbs as its new clerk during its annual organizational meeting Thursday.'),
      E('huffaker-castaic', 'Board members Susan Christopher, Laura Pearson and Victor Torres, who were unopposed in this year\'s school board elections, were also sworn in for new four-year terms that end in 2017.'),
      E('huffaker-castaic', 'Steve Teeman, replacing Pearson as clerk'),
      E('castaic-board', 'Laura Pearson, President Trustee Area B Term: November 2022 to December 2026')],
     'two sources', startDetail='Elected Nov 2005 and re-elected 2009 (CEDA); unopposed 2013; 2018 and 2022 seats not in CEDA (presumably uncontested).', officesDetail='President three times by Dec 2017 (earlier years not stated); "president 2018" is the Dec 2017 election; president now per the board page. Clerk in 2013 (replaced by Teeman for 2014).')],
  'notes': 'Continuous since Dec 2005. Current term Nov 2022 to Dec 2026, Area B.'},

 {'id': 25433, 'name': 'Rochelle "Shelley" Weinstein', 'holdings': [
   H('Sulphur Springs Union School District', 'December 2003', '2003-12', 'elected', None, None, 'serving', ['clerk 2014', 'president 2026'],
     [E('sulphur-board', 'Mrs. Weinstein has served on the Board of Trustees since 2003.'),
      E('sulphur-board', 'Board President: Shelley Weinstein'),
      E('clegg-2014-president', 'Member Shelley Weinstein replaces Clegg and clerk.')],
     'two sources', startDetail='Elected Nov 2003 as "Rochelle \\"Shelly\\" Weinstein" (CEDA); re-elected 2011 (CEDA); other terms without a CEDA contest.')],
  'notes': 'Continuous since 2003. The second source (Dec 2013) shows her on the board, not her start; the start year rests on the district page plus CEDA 2003.'},

 {'id': 26549, 'name': 'Joe Messina', 'holdings': [
   H('William S. Hart Union High School District', 'December 2009', '2009-12', 'elected', None, None, 'serving', ['president 2013', 'president 2026'],
     [E('messina-second-term', 'When he was elected in 2009, Messina promised'),
      E('ballotpedia-messina', 'He assumed office in 2009. His current term ends on December 11, 2026.'),
      E('ballotpedia-messina', 'Joe Messina ran against two other candidates, including fellow incumbents Paul B. Strickland and Bob Jensen for three seats in the general election on November 5, 2013. The election has been canceled and will not appear on the official ballot.'),
      E('hart-vacancy-agenda', 'an oath of office will be administered Dec. 11 to board President Joe Messina and Bob Jensen, who are running unopposed'),
      E('chris-fall-quits', 'Hart School Board President Joe Messina announced the resignation Friday of recently appointed board member Chris Fall.'),
      E('hart', 'Joe Messina Trustee Area No. 5 representative President'),
      E('hart', 'current term 2022 - 2026')],
     'two sources', startDetail='Elected Nov 2009 (CEDA). Re-seated unopposed Dec 2013 (no contest held). The 2013 term ran to Dec 2018 because Hart moved elections to even years (Bob Jensen, elected 2009 with him, "was re-elected to the Hart Board in 2013 and 2018" per the hart page). Won Area 5 in 2018 and 2022 (CEDA).')],
  'notes': 'Resolved: continuous 2009 to now. There is no 2013 to 2018 gap: the 2013 seat was uncontested (cancelled election), so CEDA has no row. Board president 2013 and now (2026); other president years not established.'},

 {'id': 25381, 'name': 'Susan Christopher', 'holdings': [
   H('Castaic Union School District', 'December 2009', '2009-12', 'elected', 'December 2018', '2018-12', 'expired', ['president 2013'],
     [E('huffaker-castaic', 'She has been a CUSD board member since 2009 and is up for reelection in 2017.'),
      E('huffaker-castaic', 'Huffaker replaced Christopher as president'),
      E('signal-castaic-2018-elections', 'Susan Christopher, an attorney from Castaic who will have served nine years on the board as of this fall'),
      E('signal-castaic-2018-elections', 'I have had two full terms and this term was actually extended so we could move our elections to even-yeared terms'),
      E('castaic-seeks-area-d', 'Susan Christopher has decided not to run for re-election for her seat in Area D of the Castaic Union School District Board of Trustees')],
     'two sources', endDetail='Did not seek re-election in 2018; term ended at the December 2018 organizational meeting (exact day not sourced). Successor appointed in lieu of election (castaic-seeks-area-d).')],
  'notes': 'Continuous Dec 2009 to Dec 2018 (elected 2009 per CEDA, unopposed 2013, term extended to 2018). Board president for 2013.'},

 {'id': 25387, 'name': 'Michael Shapiro', 'holdings': [
   H('Newhall School District', 'December 2003', '2003-12', 'elected', None, None, 'unknown', ['clerk 2014'],
     [E('newhall-officers-2014', 'Shapiro has been a member of the board since 2003.'),
      E('newhall-officers-2014', 'Walters replaces Ellis as president of the Newhall School District board, and Shapiro takes Walters\' place as clerk.')],
     'one source', startDetail='Elected Nov 2003, re-elected 2007 (CEDA); 2011 seat not in CEDA (presumably uncontested).', endDetail='End not found. Lead, unverified: the Newhall page says Isaiah Talley "was first appointed to the Newhall School Board on September 6, 2016" to a "vacated seat"; that may be Shapiro\'s seat. Do not use without a source.')],
  'notes': 'Still on the board as clerk for 2014. When and how he left is unresolved.'},

 {'id': 25405, 'name': 'Rose Diaz', 'holdings': [
   H('Saugus Union School District', 'December 1999', '1999-12', 'elected', 'December 2011', '2011-12', 'expired', [],
     [E('scvnews-election-results', 'Two years earlier, Winkler ousted longtime board member Rose Diaz, who blamed herself for a lack of aggressive campaigning.'),
      E('winkler-wont-fight', 'Rose Diaz, the incumbent candidate who was defeated by Winkler in 2011')],
     'one source', startDetail='Elected Nov 1999 (CEDA, 2,740 votes, non-incumbent). 2003 and 2007 seats have no CEDA rows (Saugus had no contest those years), so three terms 1999, 2003, 2007 is an inference; no text read says "three-term".', endDetail='Lost as incumbent in Nov 2011 to Stephen Winkler (CEDA; two SCV News texts). Seat ended at the December 2011 organizational meeting (day not sourced).')],
  'notes': 'Start rests on CEDA only; texts confirm she was a longtime incumbent who lost in 2011. "one source" refers to the text support for continuity.'},

 {'id': 25431, 'name': 'Kerry Clegg', 'holdings': [
   H('Sulphur Springs Union School District', '1989', '1989', 'unknown', 'December 2018', '2018-12', 'expired', ['president 2014', 'president (four times by 2014)'],
     [E('clegg-2014-president', 'Clegg has served on the board continuously since 1989, including four times as president.'),
      E('clegg-2014-president', 'Clegg replaces Board Member Denis DeFigueiredo as president'),
      E('signal-clegg-goodbye', 'to bid a farewell to longtime board member Kerry Clegg, who is retiring after 29 years of community service.')],
     'two sources', endDetail='Retired; did not run in 2018. Farewell at the meeting of Wednesday Dec. 5, 2018 (article Dec. 6, 2018); successor seated then. "29 years" fits 1989 to 2018.', selectionDetail='How he first joined in 1989 (election or appointment) is not stated.')],
  'notes': 'Also a director of the California School Boards Association 1998 to 2012 (clegg-2014-president: "Clegg also served as director of the state School Boards Association from 1998 to 2012."), a separate office if wanted.'},

 {'id': 25445, 'name': 'Gloria Mercado-Fortine', 'holdings': [
   H('William S. Hart Union High School District', 'December 1997', '1997-12', 'elected', 'December 2001', '2001-12', 'expired', [],
     [E('boyer2015ch14-page5', 'Gloria Mercado served on the William S. Hart Union High School District board from 1997 to 2001.')],
     'one source', startDetail='CEDA 1997: "Gloria E. Mercado", designation Educator, elected (5,379 votes, 3 seats).', endDetail='CEDA 2001: "Gloria Mercado", designation Incumbent, not elected (9,510 votes, 3 seats).'),
   H('William S. Hart Union High School District', 'December 2003', '2003-12', 'elected', 'December 2015', '2015-12', 'expired', ['president (four times, years not stated)'],
     [E('scmag-2015-hart-candidates', 'It\'s been an honor and a privilege to serve as governing board member of the William S. Hart Union High School District for the past 16 years.'),
      E('scmag-2015-hart-candidates', 'incumbents Gloria Mercado-Fortine (Trustee Area No. 1) and Steve Sturgeon (Trustee Area No. 4) are up for re-election, and Linda Storli and Andrew Taban are challenging each seat'),
      E('scmag-2015-hart-candidates', 'I served four times as president'),
      E('canyons-mercado-fortine-bio', 'Gloria served four terms (16 years) as a publicly elected official on the Governing Board of the William S. Hart High School District.')],
     'two sources', startDetail='CEDA 2003: "Gloria Mercado", Educator, elected as a non-incumbent; re-elected 2007 and 2011 as "Gloria Mercado-Fortine".', endDetail='CEDA 2015 Area 1: "G Mercado-Fortine", incumbent, not elected (1,078 of 2,506); Linda Storli won. Term ended Dec 2015.')],
  'notes': 'Resolved, with evidence: the CEDA rows are hers and are right. "Gloria E. Mercado" (1997) and "Gloria Mercado" (2001, 2003) are the same person before the hyphenated name: Boyer (2015) independently says "Gloria Mercado served on the ... Hart ... board from 1997 to 2001", and her own statements count "four terms (16 years)", which equals 1997 to 2001 (one term) plus 2003 to 2015 (three terms). "2000 to 2016" in the recount was a misreading; she never served 2016. So two Hart holdings with a 2001 to 2003 gap, ending Dec 2015 after losing to Storli. Castaic Union board: her 2015 statement says "12 years", the canyons.edu bio says "ten years". No dates; CEDA (from 1995) has no Castaic Union row for her, so it was before 1995 or uncontested. Not created as a holding; needs a source. Her 2015 statement also says she was a founding member of the Castaic Town Council.'},
]

def main():
    texts = {}
    sources = {}
    for k, (url, pub, title, byline, ext) in SRC.items():
        hp = os.path.join(D, f'{k}.{ext}')
        tp = os.path.join(D, f'{k}.txt')
        assert os.path.exists(hp), hp
        assert os.path.exists(tp), tp
        raw = open(hp, 'rb').read()
        if pub is None and ext == 'html':
            m = re.search(rb'article:published_time" content="([^"]+)', raw)
            pub = m.group(1).decode() if m else None
        texts[k] = norm(open(tp, encoding='utf-8', errors='replace').read())
        s = {'url': url, 'published': pub, 'read': '2026-10-03', 'htmlSha256': hashlib.sha256(raw).hexdigest(),
             'file': f'{k}.{ext}', 'title': title, 'byline': byline}
        if k in NOTES_SRC: s['note'] = NOTES_SRC[k]
        sources[k] = s
    bad = 0
    used = set()
    for p in PEOPLE:
        for h in p['holdings']:
            for e in h['evidence'] + h.get('conflictEvidence', []):
                e['quote'] = norm(e['quote'])
                used.add(e['source'])
                if e['source'] not in texts:
                    print('NO SOURCE', e['source']); bad += 1; continue
                if e['quote'] not in texts[e['source']]:
                    print('MISS', p['name'], e['source'], '|', e['quote']); bad += 1
    unused = set(SRC) - used
    out = {'generated': '2026-10-03', 'method': 'Quotes are exact substrings of <source>.txt after collapsing whitespace and converting curly quotes to straight. HTML text via textutil; PDF text via pdftotext -layout. CEDA facts cited in startDetail/endDetail/notes come from inventory/elections/ceda-scv.json and county returns from inventory/elections/county-svc-water.json; they are not quoted evidence.',
           'sources': sources, 'people': PEOPLE}
    json.dump(out, open(os.path.join(D, 'facts.json'), 'w'), indent=1, ensure_ascii=False)
    print('bad', bad, 'unused sources', sorted(unused))

main()
