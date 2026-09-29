#!/usr/bin/env python3
"""
The Santa Clarita City Council elections, 1987 to 2024, read out of the nine City
Clerk documents into one structured file for import_elections.php.

The documents (inventory/elections/, checked against the SHA-256 recorded in
inventory/legacy/elections-manifest.json on 25 September 2026):

  historical-results-7.pdf     the City's summary of the April elections, 1990 to
                               2012, and the incorporation election of 3 November
                               1987. A text layer; parsed.
  resolutionNo129-6.pdf        Resolution 12-9, declaring the April 2012 result.
                               A scan; read by OCR. The only document that declares
                               winners.
  2014ElectionResultsbyPreci-5 the April 2014 results by precinct. A text layer,
                               surnames only; parsed.
  2016StatementofVotesCast-4   the County's statements of votes cast, November 2016,
  LACountyFinalVoteCount-3     2018, 2020 (inside the canvass resolution), 2022
  Final-Election-Canvass-Res-2 (inside the certificate of canvass) and 2024
  Final-Certficate-of-Canvas-1 (District 1). The grand totals are read from the
  10880.pdf                    text layer where it is clean (2016, 2024) and from
                               the page images where it is not; the candidates'
                               names, printed sideways, are read from the page
                               images. Every November reading records its page.

Numbers are kept as printed (votesAsPrinted) and as read (votes). Where the
printed figure has a decimal point for a thousands separator ("5.461"), the
reading is recorded as a source fault, not silently corrected.

Seats: no document before 2024 states how many seats were filled, except
Resolution 12-9 (two). The council's four-year staggered terms alternate two and
three seats; anchored on 2012 that gives 3 in 2014 and 2018 and 2022, and 2 in
2016 and 2020, and backwards 3, 2, 3, 2... to 1990. The derivation agrees with
every independent fact the archive holds (Clyde Smyth elected third in 1994;
two winners in 2000 and 2008; three candidates for two seats in 2004). It is
recorded as uncited, never as the document's.

Writes inventory/elections/elections.json. Reads only.

  python3 scripts/import/parse_election_results.py
"""
import json
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
T = os.path.join(ROOT, 'inventory/elections/text')
MONTHS = {m: i for i, m in enumerate(['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'], 1)}
DOCS = {
    'summary': 'historical-results-7.pdf', 'res129': 'resolutionNo129-6.pdf', 'r2014': '2014ElectionResultsbyPreci-5.pdf',
    'sov2016': '2016StatementofVotesCast-4.pdf', 'sov2018': 'LACountyFinalVoteCount-3.pdf', 'sov2020': 'Final-Election-Canvass-Res-2.pdf',
    'sov2022': 'Final-Certficate-of-Canvas-1.pdf', 'sov2024': '10880.pdf',
}


def num(s):
    """Read a printed count. Returns (value, fault) where fault names a decimal point used as a separator."""
    s = s.strip()
    if re.fullmatch(r'\d{1,3}\.\d{3}', s):
        return int(s.replace('.', '')), 'a decimal point printed where a thousands separator belongs'
    return int(s.replace(',', '')), None


def iso(printed):
    m = re.match(r'([A-Z]+) (\d{1,2}), (\d{4})', printed.upper())
    return f"{m.group(3)}-{MONTHS[m.group(1)]:02d}-{int(m.group(2)):02d}"


def summary():
    """The April elections 1990-2012 and November 1987, from the City's summary."""
    lines = open(os.path.join(T, 'historical-results-7.txt')).read().splitlines()
    out, cur, i = [], None, 0
    while i < len(lines):
        line = lines[i]
        m = re.match(r'\s*(APRIL \d{1,2}, \d{4})\s+([\d,.]+)\s+([\d,.]+)\s+([\d,.]+)\s+([\d,.]+)', line)
        if m:
            cur = {'printed': m.group(1).title(), 'date': iso(m.group(1)), 'kind': 'general', 'consolidatedWith': '', 'doc': 'summary',
                   'ballots': m.group(2), 'registered': m.group(3), 'absentee': m.group(4), 'precinct': m.group(5), 'candidates': [], 'measures': []}
            out.append(cur)
        elif cur and re.match(r'\s*Measure (A-92)', line):
            subj = re.sub(r'\s*Measure A-92\s*', '', line).strip()
            no = re.match(r'\s*NO\s+([\d,]+)', lines[i + 1]); yes = re.match(r'\s*YES\s+([\d,]+)', lines[i + 2])
            cur['measures'].append({'letter': 'A-92', 'subject': subj, 'yes': yes.group(1), 'no': no.group(1)})
            i += 2
        elif cur and cur['date'] >= '1990' and (c := re.match(r"\s*([A-Z][A-Za-z.’'“”\"() -]+?)\s+([\d,.]+)\s*$", line)) and not c.group(1).strip().startswith(('Candidates', 'Received', 'County')):
            cur['candidates'].append({'name': c.group(1).strip(), 'votes': c.group(2)})
        i += 1
    # November 3, 1987: the incorporation election, laid out differently.
    text = '\n'.join(lines)
    s87 = text[text.index('NOVEMBER 3, 1987'):]
    head = re.search(r'([\d,]+)\s+([\d,]+)\s+([\d,.]+)\s*\n', s87)
    e87 = {'printed': 'November 3, 1987', 'date': '1987-11-03', 'kind': 'general', 'consolidatedWith': 'County of Los Angeles consolidated election', 'doc': 'summary',
           'ballots': head.group(3), 'registered': '', 'absentee': head.group(2), 'precinct': head.group(1), 'candidates': [], 'measures': []}
    u = re.search(r'Yes\s+([\d,]+)\s+([\d,]+)\s+([\d,.]+)\s*\n\s*No\s+([\d,]+)\s+([\d,]+)\s+([\d,.]+)', s87)
    e87['measures'].append({'letter': 'U', 'subject': 'Incorporation of City of Santa Clarita', 'yes': u.group(3), 'no': u.group(6)})
    v = re.search(r'By District\s+([\d,]+)\s+([\d,]+)\s+([\d,.]+)\s*\n\s*At Large\s+([\d,]+)\s+([\d,]+)\s+([\d,.]+)', s87)
    e87['measures'].append({'letter': 'V', 'subject': 'Council elected by district or at large (Yes: by district; No: at large)', 'yes': v.group(3), 'no': v.group(6)})
    cand_block = s87[s87.index('Candidates'):]
    for c in re.finditer(r"\n([A-Z][A-Za-z.’'“”\"(), -]+?)\s{3,}([\d,]+)\s+([\d,]+)\s+([\d,.]+)", cand_block):
        e87['candidates'].append({'name': c.group(1).strip(), 'votes': c.group(4)})
    out.append(e87)
    return out


def r2014():
    t = open(os.path.join(T, '2014ElectionResultsbyPreci-5.txt')).read()
    names = re.search(r'Precinct\s+(Acosta.*?Wieczorek)\s+Precinct', t, re.S).group(1).split()
    names = [n.replace('Chowd-', 'Chowdhury') for n in names]
    # The header splits two names over lines: "Chowd- / hury", "Mercado- / Fortine".
    names = ['Acosta', 'Bull', 'Chowdhury', 'Conn', 'Daniels', 'Ferdman', 'Harper', 'Gutzeit', 'Harte', 'McLean', 'Mercado-Fortine', 'Weste', 'Wieczorek']
    tot = re.search(r'VOTE TOTALS:\s+([\d\s]+)', t).group(1).split()[:13]
    reg = re.search(r'Citywide Registration:\s*([\d,]+)', t).group(1)
    bal = re.search(r'Total Ballots Cast:\s*([\d,]+)', t).group(1)
    return {'printed': 'April 8, 2014', 'date': '2014-04-08', 'kind': 'general', 'consolidatedWith': '', 'doc': 'r2014', 'ballots': bal, 'registered': reg,
            'absentee': '', 'precinct': '', 'measures': [], 'candidates': [{'name': n, 'votes': v, 'surnameOnly': True} for n, v in zip(names, tot)]}


# The November statements: names read from the page images, totals from the text layer or the page image.
NOVEMBER = [
    ('November 8, 2016', '2016-11-08', 'sov2016', '117972', '90947', 'grand total, text layer; names, page 1 image',
     ['Bob Kellar', 'Mark White', 'Paul J Wieczorek', 'Cameron Smyth', 'Kenneth Dean', 'David Ruelas', 'Sandra L Nichols', 'Brett Haddock', 'Matthew J Hargett', 'Alan Ferdman', 'TimBen Boydston'],
     ['32216', '3976', '2806', '30109', '10101', '3918', '5730', '3955', '5486', '12106', '17108']),
    ('November 6, 2018', '2018-11-06', 'sov2018', '125206', '85607', 'grand totals, page images 9 and 18',
     ['Ken Dean', 'Matthew J Hargett', 'Brett Haddock', 'Sankalp B Varma', 'Sandra L Nichols', 'Diane Trautman', 'Laurene Weste', 'Sean Weber', 'Paul J Wieczorek', 'Marsha McLean', 'Bill Miranda', 'Cherry E Ortega', 'Logan Smith', 'Jason Gibbs', 'TimBen Boydston'],
     ['14951', '7093', '11427', '2595', '5049', '16479', '25603', '5072', '4903', '25273', '18885', '6499', '12871', '10008', '12857']),
    ('November 3, 2020', '2020-11-03', 'sov2020', '142880', '121458', 'grand total, Exhibit A, page 7 image',
     ['TimBen Boydston', 'Aakash Ahuja', 'Selina Thomas', 'Jason Gibbs', 'Cameron M Smyth', 'Kenneth Dean', 'Kelvin Driscoll', 'Douglas Fraser', 'Chris Werthe'],
     ['17724', '14300', '13554', '29474', '56919', '2750', '26282', '871', '20194']),
    ('November 8, 2022', '2022-11-08', 'sov2022', '145251', '79288', 'grand total, page 10 image',
     ['Kody Amour', 'David Barlavi', 'Selina M Thomas', 'Bill Miranda', 'Jeffrey Malick', 'Marsha McLean', 'Douglas Fraser', 'Laurene Weste', 'Denise Lite'],
     ['1679', '12359', '14121', '32306', '14560', '28352', '3865', '32886', '25552']),
    ('November 5, 2024', '2024-11-05', 'sov2024', '23,054', '15,508', 'grand total, text layer; names, page 1',
     ['Bryce Jepsen', 'Patsy Ayala', 'Tim Burkhart'], ['4,142', '4,563', '4,108']),
]

# Seats: stated for 2012 (Resolution 12-9) and 2024 (one district); derived otherwise, uncited.
SEATS_STATED = {'2012-04-10': (2, 'certified'), '2024-11-05': (1, 'contemporary'), '1987-11-03': (5, 'retrospective')}


def seats_for(date):
    if date in SEATS_STATED:
        return SEATS_STATED[date]
    year = int(date[:4])
    return (2 if (year - 2012) % 4 == 0 else 3), 'uncited'


def main():
    elections = summary() + [r2014()]
    for printed, date, doc, reg, bal, where, names, votes in NOVEMBER:
        elections.append({'printed': printed, 'date': date, 'kind': 'general', 'consolidatedWith': 'County of Los Angeles general election', 'doc': doc,
                          'reading': where, 'ballots': bal, 'registered': reg, 'absentee': '', 'precinct': '', 'measures': [],
                          'district': 'District 1' if date == '2024-11-05' else '',
                          'candidates': [{'name': n, 'votes': v} for n, v in zip(names, votes)]})
    faults = []
    for e in elections:
        for k in ('ballots', 'registered', 'absentee', 'precinct'):
            if e[k]:
                val, fault = num(e[k])
                if fault:
                    faults.append({'record': 'election', 'date': e['date'], 'field': k, 'asPrinted': e[k], 'reading': str(val), 'basis': fault})
                e[k + 'Value'] = val
        for c in e['candidates']:
            c['value'], fault = num(c['votes'])
            if fault:
                faults.append({'record': 'candidacy', 'date': e['date'], 'name': c['name'], 'field': 'votesAsPrinted', 'asPrinted': c['votes'], 'reading': str(c['value']), 'basis': fault})
        for m in e['measures']:
            for k in ('yes', 'no'):
                val, fault = num(m[k])
                if fault:
                    faults.append({'record': 'measure', 'date': e['date'], 'measure': m['letter'], 'field': k, 'asPrinted': m[k], 'reading': str(val), 'basis': fault})
                m[k + 'Value'] = val
        seats, ev = seats_for(e['date'])
        e['seats'], e['seatsEvidence'] = seats, ev
        ranked = sorted(e['candidates'], key=lambda c: -c['value'])
        for rank, c in enumerate(ranked, 1):
            c['rank'] = rank
            c['topN'] = rank <= seats
    elections.sort(key=lambda e: e['date'])
    out = {'built_by': 'scripts/import/parse_election_results.py', 'documents': DOCS, 'elections': elections, 'faults': faults}
    json.dump(out, open(os.path.join(ROOT, 'inventory/elections/elections.json'), 'w'), indent=1, ensure_ascii=False)
    for e in elections:
        turnout = e['ballotsValue'] / e['registeredValue'] * 100 if e.get('registeredValue') else None
        print(f"{e['date']}  {len(e['candidates']):2d} candidates  seats {e['seats']} ({e['seatsEvidence']})  ballots {e.get('ballotsValue')}  registered {e.get('registeredValue')}  turnout {turnout:.1f}%" if turnout else f"{e['date']}  {len(e['candidates']):2d} candidates  seats {e['seats']}  ballots {e.get('ballotsValue')}")
    print(f"{len(faults)} source faults:", [(f['date'], f.get('name') or f.get('measure') or f['field'], f['asPrinted']) for f in faults])


if __name__ == '__main__':
    main()
