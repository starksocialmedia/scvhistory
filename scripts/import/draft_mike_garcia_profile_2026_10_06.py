#!/usr/bin/env python3
"""Mike Garcia #29334: the profile draft (Nathan, 6 October 2026, on the Wikipedia census: "build profiles from the
primary sources those articles point at. Label anything resting on Wikipedia alone"). Claude Code, research subagent.

Writes inventory/review/mike-garcia-profile-draft-2026-10-06.json (read by
scripts/import/build_mike_garcia_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

He is living: public life only. No birth fields, no family beyond what places him in the valley.

Every quotation below was read in the source named, on 6 October 2026. The outside sources are saved in
inventory/sources/mike-garcia-2026-10-06/ (manifest.json there); the Biographical Directory capture, the Secretary of
State's special election pages and the Statements of Vote are those already saved in
inventory/sources/legislative-districts-2026-10-04/. The check at the foot of this file confirms each quotation against
the saved copy, and each vote count against the text of its Statement of Vote. No quotation joins two clauses with an
ellipsis, and none quotes a passage that holds an em dash.
Run on the host: python3 scripts/import/draft_mike_garcia_profile_2026_10_06.py
"""
import html, json, os, re, subprocess

ROOT = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
OUT = os.path.join(ROOT, 'inventory', 'review', 'mike-garcia-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'mike-garcia-2026-10-06')
LD = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')

NOTES = {
    'TERMS': 'His terms are the archive\'s office holdings #29380 (United States House of Representatives, 25th District, 2011 lines, from 12 May 2020, the day of the special election, to 3 January 2023) and #29382 (27th District, 2021 lines, 3 January 2023 to 3 January 2025). Biographical Directory of the United States Congress, "GARCIA, Mike," as captured by the Wayback Machine on 24 November 2020, https://web.archive.org/web/20201124080951id_/https://bioguideretro.congress.gov/Home/MemberDetails?memIndex=G000061: "elected as a Republican to the One Hundred Sixteenth Congress, by special election, to fill the vacancy caused by the resignation of United States Representative Katie Hill (May 12, 2020-present)."',
    'DIST': 'The archive\'s count of the valley\'s people by district, templates/_data/valley-districts.json, United States House of Representatives. Under the "2011 lines, drawn by the Citizens Redistricting Commission", in force "January 2013 to January 2023", the 25th held "the whole valley": "The whole valley, with Palmdale, eastern Lancaster and part of Simi Valley". Under the "2021 lines, drawn by the Citizens Redistricting Commission", in force "January 2023 to January 2027", the 27th held "the whole valley": "The whole valley, with Lancaster, Palmdale and part of the City of Los Angeles".',
    'BIO': 'His official biography, "Biography | U.S. Representative Mike Garcia," https://mikegarcia.house.gov/about/ (read in the Wayback Machine\'s capture of 13 December 2024): "Congressman Garcia was born in Granada Hills and moved to Saugus in 1983 with his mother and stepfather at the age of seven."; "A top graduate of Saugus High School, Congressman Garcia was nominated to attend the United States Naval Academy in Annapolis by former U.S. Representative Howard" (Buck) McKeon; "His superb flying performance earned him the honor of becoming one of the first F/A-18 Super Hornet strike fighter pilots in the Navy."; "While on active duty, Congressman Garcia flew over 30 combat missions during Operation Iraqi Freedom in the skies above Baghdad, Fallujah, and Tikrit."; "He subsequently joined the Raytheon Company as an executive."; "Throughout eleven years as an executive with Raytheon"; "He lives in Santa Clarita"; "Congressman Garcia currently serves on three House committees: House Committee on Appropriations, House Permanent Select Committee on Intelligence, and House Committee on Science, Space, and Technology." The page lists a "Santa Clarita Valley" office at "27200 Tourney Rd Suite 300".',
    'BIOGUIDE': 'Biographical Directory of the United States Congress, "GARCIA, Mike" (the capture of 24 November 2020 cited above): "graduated from Saugus High School, Santa Clarita, Calif., 1994; B.S., United States Naval Academy, Annapolis, Md., 1998; M.A., Georgetown University, Washington, DC, 1998; United States Navy, 1999-2009; United States Navy Reserve, 2009-2012; business executive; real estate developer".',
    'MCKEON': 'Buck McKeon\'s office holdings #26980 (25th District, 1991 lines, 3 January 1993 to 3 January 2003), #29372 (2001 lines, to 3 January 2013) and #29374 (2011 lines, to 3 January 2015), each from the Secretary of State\'s Statements of Vote.',
    'SPECIAL': 'California Secretary of State, "Final Official Election Results - Congressional District 25," Special Primary Election, March 3, 2020, https://www.sos.ca.gov/elections/prior-elections/special-elections/2019-cd25/official-results-primary: "Christy Smith, DEM" 58,920, "36.2%"; "Mike Garcia, REP" 41,365, "25.4%"; "Steve Knight, REP" 27,911, "17.1%"; nine others. Special General Election, May 12, 2020, https://www.sos.ca.gov/elections/prior-elections/special-elections/2019-cd25/official-results-general: "Christy Smith, DEM" 78,721, "45.14%"; "Mike Garcia, REP" 95,667, "54.86%". Both pages: "Vacancy resulting from the resignation of Katie Hill." Katie Hill\'s office holding #29378 ends with her resignation on 3 November 2019.',
    'G2020': 'California Secretary of State, Statement of Vote, General Election, November 3, 2020, United States Representative, 25th Congressional District, https://elections.cdn.sos.ca.gov/sov/2020-general/sov/24-us-reps.pdf: Christy Smith (DEM) Los Angeles 138,441, Ventura 30,864, District Totals 169,305; Mike Garcia (REP) Los Angeles 133,066, Ventura 36,572, District Totals 169,638; Percent 50.0% each. The difference, 333 votes, is the archive\'s subtraction.',
    'G2022': 'California Secretary of State, Statement of Vote, General Election, November 8, 2022, United States Representative, 27th Congressional District, https://elections.cdn.sos.ca.gov/sov/2022-general/sov/48-congress.pdf: Christy Smith (DEM) 91,892, 46.8%; Mike Garcia (REP) 104,624, 53.2%.',
    'G2024': 'California Secretary of State, Statement of Vote, General Election, November 5, 2024, United States Representative, 27th Congressional District, https://elections.cdn.sos.ca.gov/sov/2024-general/sov/25-us-rep-congress.pdf: George Whitesides (DEM) 154,040, 51.3%; Mike Garcia* (REP, the asterisk marking the incumbent) 146,050, 48.7%. George Whitesides\'s office holding #29384 (27th District, from 3 January 2025).',
    'ELECT': 'Congressional Record, House, January 6, 2021, "Counting Electoral Votes: Joint Session of the House and Senate Held Pursuant to the Provisions of Senate Concurrent Resolution 1," 167 Cong. Rec. H76 and following, https://www.govinfo.gov/content/pkg/CREC-2021-01-06/html/CREC-2021-01-06-pt1-PgH76-4.htm. The first objection: "object to the counting of the electoral votes of the State of Arizona on the ground that they were not, under all of the known circumstances, regularly given." The second, from Representative Scott Perry: "I object to the electoral votes of my beloved Commonwealth of Pennsylvania". Roll No. 10, on the Arizona objection, "yeas 121, nays 303, not voting 7"; Roll No. 11, on the Pennsylvania objection, "yeas 138, nays 282, not voting 11"; "Garcia (CA)" is among the yeas in both. Clerk of the House, roll calls 10 (6 January 2021, 11:08 PM) and 11 (7 January 2021, 3:08 AM), "On Agreeing to the Objection," "Failed," https://clerk.house.gov/evs/2021/roll010.xml and https://clerk.house.gov/evs/2021/roll011.xml: "Garcia (CA)", party "R", vote "Yea", in both.',
}

BODY = [
    'Mike Garcia represented the whole Santa Clarita Valley in the United States House of Representatives from May 2020 to January 2025.{TERMS} Until January 2023 he sat for the 25th District, which under the 2011 lines joined the valley to Palmdale, eastern Lancaster and part of Simi Valley; from then he sat for the 27th, which under the 2021 lines joined it to Lancaster, Palmdale and part of the City of Los Angeles.{DIST} By his official biography he moved to Saugus in 1983, at the age of seven, and he graduated from Saugus High School in 1994.{BIO}{BIOGUIDE} Buck McKeon, who represented the valley in Congress from 1993 to 2015, nominated him to the United States Naval Academy.{BIO}{MCKEON} In Congress he kept a Santa Clarita Valley office on Tourney Road, and he gave Santa Clarita as his home.{BIO}',
    'He took a bachelor\'s degree at the Naval Academy and a master\'s at Georgetown University, both in 1998, and served in the Navy from 1999 to 2009 and in the Navy Reserve until 2012.{BIOGUIDE} By his official biography he was one of the Navy\'s first F/A-18 Super Hornet pilots and flew more than 30 combat missions in Operation Iraqi Freedom, and after leaving the Navy he spent eleven years as an executive of the Raytheon Company.{BIO}',
    'He came to the House through the special election held after Katie Hill resigned the seat in November 2019. In the special primary of 3 March 2020 he finished second of twelve candidates, with 41,365 votes, 25.4 per cent, behind Christy Smith, a Democrat, with 58,920, and ahead of Steve Knight, the district\'s member from 2015 to 2019, with 27,911. On 12 May 2020 he beat Smith by 95,667 votes to 78,721, 54.86 per cent.{SPECIAL} He beat her again in the general election that November, by 169,638 votes to 169,305, a margin of 333,{G2020} and a third time in 2022, in the new 27th District, by 104,624 to 91,892.{G2022} In November 2024 George Whitesides, a Democrat, defeated him, by 154,040 votes to 146,050.{G2024}',
    'In his last term he sat on the House Appropriations Committee, the Permanent Select Committee on Intelligence and the Committee on Science, Space, and Technology.{BIO} At the count of the presidential electoral votes on 6 and 7 January 2021 he voted for the objections to counting the electoral votes of Arizona and of Pennsylvania; both failed, by 121 votes to 303 and 138 to 282.{ELECT}',
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


VD = re.sub(r'\s+', ' ', open(os.path.join(ROOT, 'templates', '_data', 'valley-districts.json'), encoding='utf-8').read())
CHECK = {
    'TERMS': plain(os.path.join(LD, 'house', 'bioguide-G000061-wayback.html')),
    'BIOGUIDE': plain(os.path.join(LD, 'house', 'bioguide-G000061-wayback.html')),
    'BIO': plain(os.path.join(SRC, 'wayback-garcia-house-about-20241213185553.html')),
    'DIST': VD,
    'SPECIAL': plain(os.path.join(LD, 'sos', 'special-elections', '2019-cd25', 'official-results-primary.html')) + plain(os.path.join(LD, 'sos', 'special-elections', '2019-cd25', 'official-results-general.html')),
    'ELECT': plain(os.path.join(SRC, 'crec-2021-01-06-H76-counting-electoral-votes.htm')),
}
TITLES = ('GARCIA, Mike,', 'Biography | U.S. Representative Mike Garcia,', 'Final Official Election Results - Congressional District 25,',
          'Counting Electoral Votes: Joint Session of the House and Senate Held Pursuant to the Provisions of Senate Concurrent Resolution 1,',
          'On Agreeing to the Objection,', 'Failed,', '2011 lines, drawn by the Citizens Redistricting Commission', '2021 lines, drawn by the Citizens Redistricting Commission')
# Vote counts that must appear in the text of each Statement of Vote (and the two roll calls' XML).
FIGS = {
    'G2020': (pdf(os.path.join(LD, 'sos', 'general-election-november-3-2020', '24-us-reps.pdf')), ['25th Congressional District', '138,441', '30,864', '169,305', '133,066', '36,572', '169,638']),
    'G2022': (pdf(os.path.join(LD, 'sos', 'general-election-nov-8-2022', '48-congress.pdf')), ['27th Congressional District', '91,892', '104,624', '46.8%', '53.2%']),
    'G2024': (pdf(os.path.join(LD, 'sos', 'general-election-nov-5-2024', '25-us-rep-congress.pdf')), ['27th Congressional District', 'Whitesides', 'Garcia*', '154,040', '146,050', '51.3%', '48.7%']),
    'SPECIAL': (CHECK['SPECIAL'], ['58,920', '41,365', '27,911', '78,721', '95,667']),
    'ELECT': (open(os.path.join(SRC, 'clerk-roll-2021-010.xml'), encoding='utf-8').read() + open(os.path.join(SRC, 'clerk-roll-2021-011.xml'), encoding='utf-8').read(),
              ['<rollcall-num>10</rollcall-num>', '<rollcall-num>11</rollcall-num>', 'name-id="G000061"', '>Garcia (CA)</legislator>', '11:08 PM', '3:08 AM']),
}
missing = []
for k, text in CHECK.items():
    for q in re.findall(r'"([^"]*)"', NOTES[k]):
        q2 = re.sub(r'\s+', ' ', q)
        if len(q2) < 12 or q2 in TITLES:
            continue
        if q2.rstrip('.,') not in text:
            missing.append(f'{k}: {q2[:90]}')
for k, (text, figs) in FIGS.items():
    for f in figs:
        if f not in text:
            missing.append(f'{k}: figure {f} not in its source')
# each Garcia (CA) vote in the roll call XML is a Yea
for r in ('010', '011'):
    x = open(os.path.join(SRC, f'clerk-roll-2021-{r}.xml'), encoding='utf-8').read()
    m = re.search(r'name-id="G000061"[^>]*>Garcia \(CA\)</legislator>\s*<vote>([^<]+)</vote>', x)
    if not m or m.group(1) != 'Yea':
        missing.append(f'roll {r}: Garcia (CA) is not Yea')
for p in BODY:
    for q in [q for q in re.findall(r'"([^"]*)"', p) if len(q) >= 8]:
        if not any(q.rstrip('.') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_mike_garcia_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29334, 'title': 'Mike Garcia'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'Navy pilot; business executive; congressman',
        'wikidataId': 'Q94236068', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Mike_Garcia_(politician)',
        'bioguideId': 'G000061',
        'rolesAdd': [],
        'relatedPersonsAdd': [29332, 29336],
    },
    'keepNotes': [],
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Mike Garcia #29334: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_mike_garcia_profile_2026_10_06.php) reads the .json beside this file. He is living: public life only. Sources saved in inventory/sources/mike-garcia-2026-10-06/ (manifest.json there) and inventory/sources/legislative-districts-2026-10-04/.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'relatedPersons gains Katie Hill #29332 (his predecessor in the 25th) and George Whitesides #29336 (his successor in the 27th). Christy Smith #25389, his opponent three times, is named in the body but not related: an opponent is not a relation the archive has made for anyone else. No birth fields (living). wikidataId Q94236068 and the Wikipedia URL are finding aids, from the census (inventory/review/wikipedia-census-2026-10-06.json); bioguideId G000061 is from the Biographical Directory itself.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- His full name "Michael Joseph Garcia" and birth date "April 24, 1976" (the Biographical Directory gives the year, 1976, and Granada Hills; a living person\'s birth is not recorded here in any case).',
       '- That his parents "had immigrated from Mexico in 1959" (his official biography says only "A first-generation American citizen").',
       '- That he was "commissioned an ensign in the United States Navy in May 1998" and trained at Naval Air Station Pensacola (the Biographical Directory gives Navy service from 1999), and deployed from the USS Nimitz "During the 2003 invasion of Iraq" (the official biography says he "deployed as an F/A-18 strike fighter pilot aboard the USS Nimitz" "On the heels of 9/11"; the body keeps only the combat missions, as the biography gives them).',
       '- That he was a "business development manager at Raytheon Intelligence, Information and Services" (the biography says "an executive").',
       '- That his 2020 special election win was the first Republican capture of a Democratic-held California House seat since 1998, and that he was "the first Hispanic Republican representative to serve from California since Romualdo Pacheco"; endorsements (Club for Growth, Susan B. Anthony List); campaign themes ("defeat socialism", "build the wall"); that Smith "conceded to Garcia on November 30, 2020" and that he "raised $3 million more than Smith". The 333-vote margin is in the body, from the Statement of Vote, not from the article.',
       '- Everything in the article after his election on his votes and positions, except the two objections of January 2021, which the body takes from the Congressional Record and the Clerk\'s roll calls.', '',
       '## What the mirror holds on him', '',
       'Searched 6 October 2026 with grep over every .htm, .html and .txt file in /Volumes/Reggie/SCVHistory/scvhistory.com for "Mike Garcia", "Rep. Garcia" and "Congressman Garcia": no page. The mirror was taken before his election.', '',
       '## Not done', '',
       '- No succession note on his holdings: #29380 already cites the special primary and the special general (which name Katie Hill\'s resignation), and #29382 already says "He lost the 2024 election to George Whitesides."',
       '- His legislation was not read. The body says nothing about bills he wrote.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources, figures against {len(FIGS)}; written {os.path.relpath(OUT, ROOT)}.json and .md')
