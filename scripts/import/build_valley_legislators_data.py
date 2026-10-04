#!/usr/bin/env python3
"""
The valley's members in the Assembly, the State Senate and the House, as terms, and the districts that
covered the valley in each plan (Nathan, 4 October 2026, approving the model: "continuous seat following
the district holding most of the valley, partial districts listed on the body page without term records,
partial-district members getting terms only where they already have records").

Reads inventory/review/legislative-districts-2026-10-04.json and the sources' manifest. Writes:
  scripts/import/data/valley-legislators-2026-10-04.json   the terms, each with its citations, for
                                                           record_valley_legislators_2026_10_04.php
  templates/_data/valley-districts.json                    the districts covering the valley, by plan, for
                                                           _partials/civic/valley-districts.twig
Run: python3 scripts/import/build_valley_legislators_data.py
"""
import json, os, re
ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
R = json.load(open(os.path.join(ROOT, 'inventory/review/legislative-districts-2026-10-04.json')))
MAN = {f['file']: f for f in json.load(open(os.path.join(ROOT, 'inventory/sources/legislative-districts-2026-10-04/manifest.json')))['files']}
MON = 'January February March April May June July August September October November December'.split()

CH = ''
def cite(key):
    f = MAN[key]; u = f['url']
    if key.startswith('sos/'):
        folder, fn = key.split('/')[-2], key.split('/')[-1]
        m = re.match(r'(.*?)-(?:nov|november)-(\d+)-(\d{4})', folder)
        kind = (m.group(1) if m else folder).replace('-', ' ').strip().title() if m else folder
        when = f'November {m.group(2)}, {m.group(3)}' if m else ''
        office = {'assembly': 'State Assembly', 'senate': 'State Senate', 'house': 'United States Representative'}[CH]
        if 'special-elections' in key:
            return f'California Secretary of State, special election records, {f["what"]}, {u}.'
        return f'California Secretary of State, Statement of Vote, {kind}, {when}, {office}, {u}.'
    if key.startswith('legislature/assembly'):
        return f'Secretary of the Senate, Record of Members of the Assembly, 1849 to 2026, {u}, read 4 October 2026.'
    if key.startswith('legislature/senate'):
        return f'Secretary of the Senate, Record of State Senators, 1849 to 2026, {u}, read 4 October 2026.'
    if key.startswith('house/bioguide'):
        return f'Biographical Directory of the United States Congress, as captured by the Wayback Machine, {u}.'
    return f'{f["what"]}, {u}.'

def srcs(chamber, district, surname, plan_prefix):
    for cy in R['chambers'][chamber]['cycles']:
        if not cy['plan'].startswith(plan_prefix): continue
        for d in cy['districts']:
            if d['district'] != district: continue
            for m in d.get('members', []):
                if isinstance(m, dict) and surname in m.get('member', ''):
                    return [s.split('legislative-districts-2026-10-04/')[-1] for s in m.get('sources', [])]
    raise SystemExit(f'no sources for {chamber} {district} {surname} {plan_prefix}')

def us(s):
    m = re.match(r'(\d+) (\w+) (\d{4})', s or '')
    return f'{m.group(2)} {int(m.group(1))}, {m.group(3)}' if m else s

def ed(s):
    m = re.match(r'(\d+) (\w+) (\d{4})', s)
    return f'{m.group(3)}-{MON.index(m.group(2)) + 1:02d}-{int(m.group(1)):02d}' if m else s

ORD = lambda n: f'{n}{"th" if 10 <= n % 100 <= 20 else {1: "st", 2: "nd", 3: "rd"}.get(n % 10, "th")}'
LBL = {'assembly': 'Assembly District', 'senate': 'Senate District', 'house': 'Congressional District'}
# person, chamber, district, plan, start, end ('' = serving), howEnded, sources-lookup (surname, plan prefix), extra note
T = [
    ('Pete Knight', 'assembly', 36, '1991', '7 December 1992', '2 December 1996', 'left', ('Knight', '1991'), 'He left the Assembly on his election to the State Senate in 1996.'),
    ('George Runner', 'assembly', 36, '1991', '2 December 1996', '2 December 2002', 'expired', ('Runner', '1991'), ''),
    ('Keith Richman', 'assembly', 38, '2001', '2 December 2002', '4 December 2006', 'expired', ('Richman', '2001'), 'He had held the 38th District since 4 December 2000; under the 1991 lines it held only the west side of the valley, and from 2 December 2002 most of it.'),
    ('Cameron Smyth', 'assembly', 38, '2001', '4 December 2006', '3 December 2012', 'expired', ('Smyth', '2001'), ''),
    ('Scott Wilk', 'assembly', 38, '2011', '3 December 2012', '5 December 2016', 'left', ('Wilk', '2011'), 'He left the Assembly on his election to the State Senate in 2016.'),
    ('Dante Acosta', 'assembly', 38, '2011', '5 December 2016', '3 December 2018', 'expired', ('Acosta', '2011'), 'He lost the 2018 election to Christy Smith.'),
    ('Christy Smith', 'assembly', 38, '2011', None, None, None, None, ''),
    ('Suzette Martinez Valladares', 'assembly', 38, '2011', '7 December 2020', '5 December 2022', 'expired', ('Valladares', '2011'), 'In 2022 she stood in the new 40th District and lost to Pilar Schiavo, 79,852 votes to 79,330.'),
    ('Pilar Schiavo', 'assembly', 40, '2021', '5 December 2022', '', 'serving', ('Schiavo', '2021'), ''),
    ('Don Rogers', 'senate', 17, '1991', '15 December 1992', '2 December 1996', 'expired', ('Rogers', '1991'), 'He resigned the 16th Senate District to be seated in the 17th on 15 December 1992, as the Record of State Senators notes.'),
    ('Pete Knight', 'senate', 17, '1991', '2 December 1996', '7 May 2004', 'died', ('Knight', '1991'), 'He died in office on 7 May 2004 (Record of State Senators).'),
    ('George Runner', 'senate', 17, '2001', '6 December 2004', '21 December 2010', 'resigned', ('Runner', '2001'), 'He resigned on 21 December 2010 on his election to the Board of Equalization, 2nd District (Record of State Senators).'),
    ('Sharon Runner', 'senate', 17, '2001', '2011', '3 December 2012', 'expired', ('Sharon Runner', '2001'), 'Elected at the special primary election of 15 February 2011, with 65.6 per cent of the vote, in George Runner\'s place (Record of State Senators). The day she was sworn in is not yet recorded.'),
    ('Steve Knight', 'senate', 21, '2011', '3 December 2012', '5 January 2015', 'resigned', ('Knight', '2011'), 'He resigned on 5 January 2015 on his election to Congress (Record of State Senators).'),
    ('Sharon Runner', 'senate', 21, '2011', '2015', '14 July 2016', 'died', ('Sharon Runner', '2011'), 'Elected at the special primary election of 17 March 2015, with 94.1 per cent of the vote, in Steve Knight\'s place; she died in office on 14 July 2016 (Record of State Senators). The day she was sworn in is not yet recorded.'),
    ('Scott Wilk', 'senate', 21, '2011', '5 December 2016', '2 December 2024', 'expired', ('Wilk', '2011'), ''),
    ('Suzette Martinez Valladares', 'senate', 23, '2021', '2 December 2024', '', 'serving', ('Valladares', '2021'), ''),
    ('Buck McKeon', 'house', 25, '1991', None, '3 January 2003', 'reelected', ('McKeon', '1991'), ''),
    ('Buck McKeon', 'house', 25, '2001', '3 January 2003', '3 January 2013', 'reelected', ('McKeon', '1991'), ''),
    ('Buck McKeon', 'house', 25, '2011', '3 January 2013', '3 January 2015', 'expired', ('McKeon', '1991'), 'He did not stand for re-election in 2014.'),
    ('Steve Knight', 'house', 25, '2011', '3 January 2015', '3 January 2019', 'expired', ('Knight', '2011'), 'He lost the 2018 election to Katie Hill.'),
    ('Katie Hill', 'house', 25, '2011', '3 January 2019', '3 November 2019', 'resigned', ('Hill', '2011'), 'She resigned on 3 November 2019.'),
    ('Mike Garcia', 'house', 25, '2011', '12 May 2020', '3 January 2023', 'reelected', ('Garcia', '2011'), 'Elected at the special election of 12 May 2020 to fill Katie Hill\'s seat; the Biographical Directory dates his service from that day. The day he was sworn in is not yet recorded.'),
    ('Mike Garcia', 'house', 27, '2021', '3 January 2023', '3 January 2025', 'expired', ('Garcia', '2021'), 'He lost the 2024 election to George Whitesides.'),
    ('George Whitesides', 'house', 27, '2021', '3 January 2025', '', 'serving', ('Whitesides', '2021'), ''),
    # Districts holding only part of the valley (Nathan, 4 October 2026: "every district that held part of the valley, with
    # its share and members ... Yes, slivers count. The rule is any part of the valley, with the share shown").
    ('Paula Boland', 'assembly', 38, '1991', '7 December 1992', '2 December 1996', 'expired', ('Boland', '1991'), 'She had sat in the Assembly since December 1990; the term here begins when the 1991 lines took effect. Under them the 38th held the west side of the valley: Castaic, Val Verde and Stevenson Ranch.'),
    ('Tom McClintock', 'assembly', 38, '1991', '2 December 1996', '4 December 2000', 'left', ('McClintock', '1991'), 'He left the Assembly on his election to the State Senate, 19th District, in 2000.'),
    ('Keith Richman', 'assembly', 38, '1991', '4 December 2000', '2 December 2002', 'reelected', ('Richman', '1991'), 'Under the 1991 lines the 38th held the west side of the valley; from 2 December 2002, under the 2001 lines, it held most of it, and his service went on in the term that follows.'),
    ('Tony Strickland', 'assembly', 37, '2001', '2 December 2002', '6 December 2004', 'expired', ('Strickland', '2001'), 'He had sat in the Assembly since December 1998; under the 1991 lines his district held no part of the valley, and the term here begins with the 2001 lines.'),
    ('Audra Strickland', 'assembly', 37, '2001', '6 December 2004', '6 December 2010', 'expired', ('Audra', '2001'), ''),
    ('Jeff Gorell', 'assembly', 37, '2001', '6 December 2010', '3 December 2012', 'reelected', ('Gorell', '2001'), 'From 2012 he sat for the 44th District, which held no part of the valley; the term here ends with the 2001 lines.'),
    ('Steve Fox', 'assembly', 36, '2011', '3 December 2012', '1 December 2014', 'expired', ('Fox', '2011'), 'He lost the 2014 election to Tom Lackey.'),
    ('Tom Lackey', 'assembly', 36, '2011', '1 December 2014', '5 December 2022', 'reelected', ('Lackey', '2011'), ''),
    ('Tom Lackey', 'assembly', 34, '2021', '5 December 2022', '', 'serving', ('Lackey', '2021'), 'His term ends in December 2026; he is not on the 2026 ballot.'),
    ('Cathie Wright', 'senate', 19, '1991', '7 December 1992', '4 December 2000', 'expired', ('Wright', '1991'), ''),
    ('Tom McClintock', 'senate', 19, '1991', '4 December 2000', '6 December 2004', 'reelected', ('McClintock', '1991'), ''),
    ('Tom McClintock', 'senate', 19, '2001', '6 December 2004', '1 December 2008', 'expired', ('McClintock', '2001'), ''),
    ('Tony Strickland', 'senate', 19, '2001', '1 December 2008', '3 December 2012', 'expired', ('Strickland', '2001'), ''),
    ('Fran Pavley', 'senate', 27, '2011', '3 December 2012', '5 December 2016', 'expired', ('Pavley', '2011'), ''),
    ('Henry Stern', 'senate', 27, '2011', '5 December 2016', '2 December 2024', 'reelected', ('Stern', '2011'), 'He was re-elected in 2024 for a redrawn 27th District that holds no part of the valley; the term here ends with the 2011 lines.'),
    ('Bill Thomas', 'house', 22, '2001', '3 January 2003', '3 January 2007', 'expired', ('Thomas', '2001'), 'Under the 2001 lines the 22nd held only Green Valley, about 1,000 people of the valley.'),
    ('Kevin McCarthy', 'house', 22, '2001', '3 January 2007', '3 January 2013', 'reelected', ('McCarthy', '2001'), 'Under the 2001 lines the 22nd held only Green Valley, about 1,000 people of the valley; from 2013 his district held none of it.'),
]
terms = []
for p, ch, n, plan, s, e, how, look, note in T:
    keys = srcs(ch, n, *look) if look else []
    CH = ch
    # A Statement of Vote belongs to the term it opened or renewed: an election from the year before the term began to the year before it ended.
    sy = int((ed(s) if s else '1992')[:4]); ey = (int(ed(e)[:4]) - (1 if ed(e)[5:7] in ('01', '02') else 0)) if e else 9999
    def yr(k): m = re.search(r'(\d{4})', k.split('/')[1] if k.startswith('sos/') else ''); return int(m.group(1)) if m else None
    keys = [k for k in keys if not k.startswith('sos/') or yr(k) is None or sy - 1 <= yr(k) < ey]
    terms.append({'person': p, 'chamber': ch, 'seatLabel': f'{ORD(n)} {LBL[ch]}', 'districtPlan': plan,
                  'termStart': us(s), 'termStartEdtf': ed(s) if s else None, 'termEnd': us(e), 'termEndEdtf': ed(e) if e else e,
                  'howEnded': how, 'note': note, 'citations': [cite(k) for k in keys]})
os.makedirs(os.path.join(ROOT, 'scripts/import/data'), exist_ok=True)
json.dump({'_about': 'Built by build_valley_legislators_data.py from inventory/review/legislative-districts-2026-10-04.json.', 'terms': terms},
          open(os.path.join(ROOT, 'scripts/import/data/valley-legislators-2026-10-04.json'), 'w'), indent=1, ensure_ascii=False)
for t in terms: print(t['person'], t['seatLabel'], t['districtPlan'], t['termStartEdtf'], t['termEndEdtf'], len(t['citations']))
print(terms[0]['citations'])
