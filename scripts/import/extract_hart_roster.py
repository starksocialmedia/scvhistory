#!/usr/bin/env python3
"""Leon Worden's roster of the Hart board (hartschoolboardmembers.htm), verbatim, to JSON.

Reads the copy in inventory/legacy/fetched/ (checked against the Reggie manifest when the drive
is mounted), via `textutil -convert txt`, and writes inventory/legacy/hart-board-roster.json:
every board as printed (label, parsed EDTF), every row as printed (raw text kept), and per
member the rows, the events read from the notes, and the terms those imply.

Terms: a member's consecutive boards make one run of service. A note "(elected Y)" or
"(reelected Y)" on a board seated in December Y starts a term in December Y (Y >= 1979, when
the board moved to November elections); "(appointed D ...)" starts one on D; a bare date starts
one on that date. A run ends at "resigned eff. D", at "did not seek reelection in Y" or
"defeated Y" (December Y), or, with no note, when the next board does not list the member
(howEnded unknown). A run that reaches the last board printed (12-11-2013 to 11-30-2014) is
open: the roster stops there. Rows that say "did not assume office" are not service.

Members are grouped by surname and first initial (T.F. Hanson = Thomas Hanson, R.E. Kelley =
Ruth Kelley, C.E. Word = Carroll E. Word, R. Crozier = Robert Crozier), and the one printing
"D.R. Huntsinger" (1968, between four C.R. Huntsinger boards) is grouped with C.R. Huntsinger.
Student board members are kept as rows and left out of the terms.

Run: python3 scripts/import/extract_hart_roster.py
"""
import hashlib, json, os, re, subprocess, sys

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
HTM = os.path.join(ROOT, 'inventory/legacy/fetched/hartschoolboardmembers.htm')
OUT = os.path.join(ROOT, 'inventory/legacy/hart-board-roster.json')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com/scvhistory/hartschoolboardmembers.htm'
MANIFEST = '/Volumes/Reggie/SCVHistory/scvhistory-manifest-2026-08-20.sha256'
MANIFEST_SHA = 'fd7447bf36f07946dad9828ae094e3277a0d42d443ecd5c175fff6fd77fc5c0e'   # read 3 October 2026

sha = hashlib.sha256(open(HTM, 'rb').read()).hexdigest()
if sha != MANIFEST_SHA:
    sys.exit(f'the fetched copy differs from the manifest: {sha}')
mirror_checked = None
if os.path.exists(MANIFEST):
    line = [l for l in open(MANIFEST, encoding='latin-1') if l.rstrip().endswith('./scvhistory/hartschoolboardmembers.htm')]
    mirror_checked = bool(line) and line[0].split()[0] == sha and hashlib.sha256(open(MIRROR, 'rb').read()).hexdigest() == sha

text = subprocess.run(['textutil', '-convert', 'txt', '-stdout', HTM], capture_output=True, check=True).stdout.decode('utf-8')
lines = [l.strip() for l in text.split('\n')]
start = next(i for i, l in enumerate(lines) if l.startswith('March 9, 1945'))
stop = next(i for i, l in enumerate(lines) if l.startswith('Source documents'))
lines = [l for l in lines[start:stop] if l]

MONTHS = {m: i + 1 for i, m in enumerate(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'])}


def edtf(s):
    """'12-8-1993' -> 1993-12-08; '11-1979' or '3-1977' -> 1979-11; '1951' -> 1951; 'March 9, 1945' -> 1945-03-09."""
    s = s.strip()
    if m := re.fullmatch(r'(\d{1,2})-(\d{1,2})-(\d{4})', s):
        return f'{m[3]}-{int(m[1]):02d}-{int(m[2]):02d}'
    if m := re.fullmatch(r'(\d{1,2})-(\d{4})', s):
        return f'{m[2]}-{int(m[1]):02d}'
    if m := re.fullmatch(r'(\d{4})', s):
        return s
    if m := re.fullmatch(r'([A-Z][a-z]+) (\d{1,2}), (\d{4})', s):
        return f'{m[3]}-{MONTHS[m[1]]:02d}-{int(m[2]):02d}'
    if m := re.fullmatch(r'([A-Z][a-z]+) (\d{4})', s):
        return f'{m[2]}-{MONTHS[m[1]]:02d}'
    return None


LABEL = re.compile(r'^(March 9, 1945 \(First Board Elected\)|\d{4}(-\d{4})?|\d{1,2}-\d{1,2}-\d{4} to \d{1,2}-(\d{1,2}-)?\d{4})$')
boards, cur = [], None
for l in lines:
    if LABEL.match(l):
        if l.startswith('March'):
            s, e = '1945-03-09', None
        elif ' to ' in l:
            a, b = l.split(' to ')
            s, e = edtf(a), edtf(b)
        elif '-' in l:
            a, b = l.split('-')
            s, e = a, b
        else:
            s, e = l, l
        cur = {'label': l, 'start': s, 'end': e, 'rows': [], 'notes': []}
        boards.append(cur)
        continue
    if l.startswith('Note:'):
        cur['notes'].append(l)
        continue
    cur['rows'].append(l)

OFFICE = re.compile(r'^(President|Clerk|Asst\. Clerk|Student Board Member|Student)(\s+\d{4})?$')


def parse_row(raw):
    s = raw.replace('{', '(')
    notes = re.findall(r'\(([^)]*)\)', s)
    s = re.sub(r'\s*\([^)]*\)', '', s).strip()
    s = re.sub(r'\s*\($', '', s)
    parts = [p.strip() for p in s.split(',')]
    name = parts[0]
    offices, other = [], []
    rest = parts[1:]
    if rest and re.fullmatch(r'Jr\.?', rest[0]):   # "Thomas M. Frew Jr." has no comma; others might
        name += ', ' + rest.pop(0)
    for p in rest:
        (offices if OFFICE.match(p) else other).append(p)
    student = any(o.startswith('Student') for o in offices)
    return {'raw': raw, 'name': name, 'offices': offices, 'other': other, 'notes': notes, 'student': student}


def member_key(name):
    n = re.sub(r'"[^"]*"', ' ', name)
    n = re.sub(r'\b(Dr\.|Jr\.?|,)', ' ', n)
    t = [w for w in re.split(r'[\s.]+', n) if w]
    last = t[-1].lower()
    first = t[0][0].upper()
    if last == 'huntsinger':
        first = 'C'
    return f'{last}|{first}'


def events_of(notes):
    ev = []
    for n in notes:
        lo = n.lower()
        if m := re.search(r'elected (\d{1,2}-\d{1,2}-\d{4})', lo):
            ev.append({'type': 'elected', 'date': edtf(m[1]), 'text': n})
        elif m := re.search(r'(?:\[re\]elected|reelected|relected)(?:/unopposed)? (\d{1,2}-\d{4}|\d{4})', lo):
            ev.append({'type': 'reelected', 'date': edtf(m[1]), 'unopposed': 'unopposed' in lo, 'text': n})
        elif m := re.search(r'\belected (\d{4})', lo):
            ev.append({'type': 'elected', 'date': m[1], 'text': n})
        if m := re.search(r'did not seek reelection in (\d{4})', lo):
            ev.append({'type': 'did not seek reelection', 'date': m[1], 'text': n})
        if m := re.search(r'defeated (\d{4})', lo):
            ev.append({'type': 'defeated', 'date': m[1], 'text': n})
        if m := re.search(r'resigned(?: eff\.)? (\d{1,2}-\d{1,2}-\d{4})', lo):
            ev.append({'type': 'resigned', 'date': edtf(m[1]), 'text': n})
        elif re.search(r'\bresigned\)?$', lo) and 'who resigned' not in lo:
            ev.append({'type': 'resigned', 'date': None, 'text': n})
        if m := re.search(r'appointed(?: (\d{1,2}-\d{1,2}-\d{4}))?', lo):
            ev.append({'type': 'appointed', 'date': edtf(m[1]) if m[1] else None, 'text': n})
        if m := re.search(r'recalled (\d{4})', lo):
            ev.append({'type': 'recalled', 'date': m[1], 'text': n})
        if 'did not assume office' in lo:
            ev.append({'type': 'did not assume office', 'date': None, 'text': n})
        if re.fullmatch(r'\d{1,2}-\d{1,2}-\d{4}', n.strip()):
            ev.append({'type': 'listed from', 'date': edtf(n), 'text': n})
    return ev


members = {}
for bi, b in enumerate(boards):
    for r in b['rows']:
        p = parse_row(r)
        b.setdefault('parsed', []).append(p)
        k = member_key(p['name'])
        m = members.setdefault(k, {'key': k, 'printings': [], 'student': p['student'], 'rows': []})
        if p['name'] not in m['printings']:
            m['printings'].append(p['name'])
        m['rows'].append({'board': bi, 'boardLabel': b['label'], 'boardStart': b['start'], 'boardEnd': b['end'],
                          'raw': r, 'name': p['name'], 'offices': p['offices'], 'other': p['other'], 'notes': p['notes'], 'events': events_of(p['notes'])})

LAST = len(boards) - 1


def year(d):
    return int(d[:4]) if d else None


def dec(y):
    return f'{y}-12'


for m in members.values():
    if m['student']:
        m['terms'] = []
        continue
    rows = [r for r in m['rows'] if not any(e['type'] == 'did not assume office' for e in r['events'])]
    runs, run = [], []
    for r in rows:
        if run and r['board'] != run[-1]['board'] + 1:
            runs.append(run); run = []
        run.append(r)
    if run:
        runs.append(run)
    terms = []
    for run in runs:
        first = run[0]
        ev = {e['type']: e for e in first['events']}
        t = {'start': None, 'startBasis': first['raw'], 'startBoard': first['boardLabel'], 'selection': None}
        if 'appointed' in ev:
            d = ev['appointed']['date']
            if not d:
                # "appointed to complete Warren's unexpired term": no date printed; the year of the
                # predecessor's resignation, if the roster gives it, else the board's first year
                pm = re.search(r"(?:complete (\w+)'s|unexpired term of (\w+))", ev['appointed']['text'])
                pk = (pm[1] or pm[2]).lower() if pm else None
                pr = [(e['date'], rr['raw']) for mm in members.values() if mm['key'].startswith(f'{pk}|') for rr in mm['rows'] for e in rr['events'] if e['type'] == 'resigned' and e['date']] if pk else []
                d = pr[0][0][:4] if pr else first['boardStart'][:4]
                if pr:
                    t['predecessorRow'] = pr[0][1]
                t['startNote'] = 'no date printed; the year of the predecessor\'s resignation' if pr else 'no date printed; the first year of the board that lists the appointee'
            t['start'] = d; t['selection'] = 'appointed'
        elif 'elected' in ev:
            d = ev['elected']['date']; t['start'] = d if len(d) > 4 else (dec(int(d)) if int(d) >= 1979 else d); t['selection'] = 'elected'
        elif 'reelected' in ev:
            d = ev['reelected']['date']; t['start'] = dec(int(d)) if len(d) == 4 else d; t['selection'] = 'elected'
        elif 'listed from' in ev:
            t['start'] = ev['listed from']['date']
        else:
            t['start'] = first['boardStart']
        for r in run[1:]:
            for e in r['events']:
                if e['type'] in ('elected', 'reelected') and e['date']:
                    d = e['date']; s = dec(int(d)) if len(d) == 4 and int(d) >= 1979 else d
                    if s == t['start']:
                        continue
                    t.update({'end': s, 'endBasis': r['raw'], 'endBoard': r['boardLabel'], 'howEnded': 'reelected'})
                    terms.append(t)
                    t = {'start': s, 'startBasis': r['raw'], 'startBoard': r['boardLabel'], 'selection': 'elected', 'unopposed': bool(e.get('unopposed'))}
        # a reelection on the run's own first row, after its start, splits too (Hanson 1977)
        for e in first['events']:
            if e['type'] == 'reelected' and t['start'] and e['date'] and e['date'] != t['start'] and len(e['date']) > 4 and e['date'] > t['start']:
                t.update({'end': e['date'], 'endBasis': first['raw'], 'endBoard': first['boardLabel'], 'howEnded': 'reelected'})
                terms.append(t)
                t = {'start': e['date'], 'startBasis': first['raw'], 'startBoard': first['boardLabel'], 'selection': 'elected'}
        last = run[-1]
        lev = {e['type']: e for e in last['events']}
        if 'resigned' in lev:
            t.update({'end': lev['resigned']['date'] or last['boardStart'][:4], 'howEnded': 'resigned'})
            if not lev['resigned']['date']:
                t['endNote'] = 'no date printed; the first year of the board that notes the resignation'
        elif 'did not seek reelection' in lev:
            t.update({'end': dec(int(lev['did not seek reelection']['date'])), 'howEnded': 'expired', 'endNote': 'did not seek reelection'})
        elif 'defeated' in lev:
            t.update({'end': dec(int(lev['defeated']['date'])), 'howEnded': 'expired', 'endNote': 'defeated'})
        elif 'recalled' in lev:
            t.update({'end': lev['recalled']['date'], 'howEnded': 'recalled'})
        elif last['board'] == LAST:
            t.update({'end': None, 'howEnded': None, 'endNote': 'listed on the last board printed; the roster stops'})
        else:
            nb = boards[last['board'] + 1]
            t.update({'end': nb['start'][:7], 'howEnded': 'unknown', 'endNote': f'not listed on the next board, {nb["label"]}'})
        t.setdefault('endBasis', last['raw']); t.setdefault('endBoard', last['boardLabel'])
        terms.append(t)
    m['terms'] = terms

out = {
    'meta': {
        'page': '/scvhistory/hartschoolboardmembers.htm',
        'title': 'SCVHistory.com | Wm. S. Hart High School Board Members, 1945 to Date.',
        'heading': 'Governing Board Members William S. Hart Union High School District 1945 to Date.',
        'author': 'Leon Worden',
        'sha256': sha,
        'manifest': '/Volumes/Reggie/SCVHistory/scvhistory-manifest-2026-08-20.sha256',
        'manifest_matched': sha == MANIFEST_SHA,
        'mirror_rechecked_this_run': mirror_checked,
        'fetched_copy': 'inventory/legacy/fetched/hartschoolboardmembers.htm (+ .txt, textutil)',
        'source_documents_printed': ['1948-1991 (Hart District, .pdf)', '1991-2013 (Hart District, .doc)'],
        'extracted': '2026-10-03',
        'extracted_by': 'Claude Code, scripts/import/extract_hart_roster.py',
        'note': 'Rows verbatim as printed (raw), typos kept ("relected", "{", "D.R. Huntsinger", "Mckeon"). The last governing board printed is 12-11-2013 to 11-30-2014. Terms are parsed from the boards and notes by the rules in the extractor header; they are an inference from the roster, and its raw rows are the evidence.',
        'board_count': len(boards),
        'member_count': sum(1 for m in members.values() if not m['student']),
        'student_member_count': sum(1 for m in members.values() if m['student']),
    },
    'boards': [{'label': b['label'], 'start': b['start'], 'end': b['end'], 'notes': b['notes'], 'rows': b['rows']} for b in boards],
    'members': sorted(members.values(), key=lambda m: (m['student'], m['rows'][0]['board'], m['key'])),
}
json.dump(out, open(OUT, 'w'), indent=1, ensure_ascii=False)
print(f"{len(boards)} boards, {out['meta']['member_count']} members, {out['meta']['student_member_count']} student members; mirror rechecked: {mirror_checked}")
for m in out['members']:
    if m['student']:
        continue
    print(m['key'], '|', '; '.join(m['printings']))
    for t in m['terms']:
        print('    ', t['start'], '->', t.get('end'), t.get('howEnded'), t.get('selection'), '|', t.get('endNote', ''))
