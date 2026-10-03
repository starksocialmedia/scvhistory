#!/usr/bin/env python3
"""
READ ONLY. Where did the WordPress import's facts come from? (Nathan,
3 October 2026: "The WordPress import was a temporary staging step and the
facts in it came from somewhere. For every unsourced claim that arrived that
way, search the legacy site for where it came from before writing it off.")

For every post in inventory/wp_content.json (people first; organizations,
groups, places and the rest after), two kinds of fact are looked for in the
Reggie mirror's text:

  fields    birth date, birthplace, death date, burial place, full name: found
            when a page names the subject and gives the value within 400
            characters of the name (dates in any of the usual forms: "February
            26, 1996", "Feb. 26, 1996", "2-26-1996", "26 February 1996")
  sentences each sentence of the body, reduced to its checkable parts (dates,
            years, numbers, proper names other than the subject's): found when
            one page that names the subject holds every date and number and at
            least two-thirds of the names. A sentence with nothing checkable
            (opinion, summary) is counted apart.

Matching is on folded text (accents, case and punctuation removed), because
the legacy pages break dates across markup ("Feb. 26, 1996" was missed by a
plain grep of the HTML). A match is a lead to a source, read before it is
cited.

Writes inventory/review/wordpress-facts-traced-2026-10-03.md and .json.
Run: python3 scripts/import/trace_wordpress_facts.py
"""
import json, re, os, html, unicodedata, collections

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
posts = json.load(open(f'{ROOT}/inventory/wp_content.json'))['posts']

def fold(s):
    s = unicodedata.normalize('NFKD', html.unescape(s)).encode('ascii', 'ignore').decode().lower()
    return re.sub(r'\s+', ' ', re.sub(r'[^a-z0-9]', ' ', s)).strip()

# The mirror, as folded text.
pages = {}
for root, _, files in os.walk(MIRROR):
    if '/files/' in root + '/': continue
    for f in files:
        if not f.endswith(('.htm', '.html')): continue
        p = os.path.join(root, f)
        try: t = open(p, 'rb').read().decode('cp1252', errors='replace')
        except OSError: continue
        t = re.sub(r'(?is)<(script|style)[^>]*>.*?</\1>', ' ', t)
        t = re.sub(r'<[^>]+>', ' ', t).split('[ RETURN TO TOP ]')[0]
        pages[p.replace(MIRROR, '')] = fold(t)
print(len(pages), 'pages read')

MONTHS = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']
ABBR = {m: m[:3] for m in MONTHS}; ABBR['september'] = 'sept'
def date_forms(s):
    """folded forms of a date string; a bare year gives itself"""
    s = fold(s)
    m = re.match(r'^([a-z]+) (\d{1,2}) (\d{4})$', s)
    if m and m.group(1) in MONTHS:
        mo, d, y = m.group(1), str(int(m.group(2))), m.group(3); n = str(MONTHS.index(mo) + 1)
        return {f'{mo} {d} {y}', f'{ABBR[mo]} {d} {y}', f'{mo[:3]} {d} {y}', f'{n} {d} {y}', f'{n} {d} {y[2:]}', f'{d} {mo} {y}', f'{d} {mo[:3]} {y}'}
    return {s} if s else set()

def near(text, names, forms, span=400):
    for n in names:
        for m in re.finditer(r'\b' + re.escape(n).replace('\\ ', r'\s(?:\w+\s){0,2}') + r'\b', text):
            w = text[max(0, m.start() - span): m.end() + span]
            if any(re.search(r'\b' + re.escape(f) + r'\b', w) for f in forms): return True
    return False

STOP = set('the a an and of in on at to for from by with as his her he she it its was were is are be been this that which who whom their they them there when where while after before during between into over under about also later first second last one two three four five six seven eight nine ten many most more some all both each other such its jr sr st mr mrs dr rev don dona fr father saint san santa los las el la de del y'.split())
def checkable(sentence, own):
    s = html.unescape(re.sub(r'<[^>]+>', ' ', sentence))
    dates = [fold(m.group(0)) for m in re.finditer(r'\b(?:' + '|'.join(m.capitalize() for m in MONTHS) + r')\.? \d{1,2},? \d{4}', s)]
    rest = re.sub(r'\b(?:' + '|'.join(m.capitalize() for m in MONTHS) + r')\.? \d{1,2},? \d{4}', ' ', s)
    years = re.findall(r'\b1[5-9]\d\d\b|\b20[0-2]\d\b', rest)
    nums = [n for n in re.findall(r'\b\d[\d,]*\b', rest) if n not in years and len(n.replace(',', '')) >= 2]
    names = []
    for m in re.finditer(r"\b[A-Z][a-zA-Z'\-]+(?:\s+(?:de|del|la|y|[A-Z][a-zA-Z'\-]+))*", s):
        w = [x for x in fold(m.group(0)).split() if x not in STOP and x not in own and len(x) > 2]
        if w: names.append(' '.join(w))
    return dates, years, [fold(n) for n in nums], list(dict.fromkeys(names))

def surname_keys(title, full):
    w = [x for x in fold(re.sub(r'\([^)]*\)', ' ', full or title)).split() if x not in STOP and len(x) > 2]
    t = [x for x in fold(re.sub(r'\([^)]*\)', ' ', title)).split() if x not in STOP and len(x) > 2]
    keys = set()
    if t: keys.add(t[-1])
    if w: keys.add(w[-1])
    return keys, set(w) | set(t)

report = []
for p in posts:
    meta = p.get('meta') or {}
    keys, own = surname_keys(p['title'], meta.get('full_name', ''))
    if p['type'] != 'person':
        keys = {fold(p['title'])}; own = set(fold(p['title']).split())
    if p['type'] == 'person':
        t0 = [x for x in fold(re.sub(r'\([^)]*\)', ' ', p['title'])).split() if x not in STOP and len(x) > 1]
        f0 = [x for x in fold(re.sub(r'\([^)]*\)', ' ', meta.get('full_name', '') or '')).split() if x not in STOP and len(x) > 1]
        keys = {' '.join([a[0], a[-1]]) for a in (t0, f0) if len(a) >= 2} | ({fold(p['title'])} if len(t0) < 2 else set())
    cands = [k for k, t in pages.items() if not re.search(r'(^|/)(index|obits|people|whatsnew|sitemap|search)\.html?$|/galleries/', k) and any(re.search(r'\b' + re.escape(x).replace('\\ ', r'\s(?:\w+\s){0,2}') + r'\b', t) for x in keys)]
    fields = {}
    for f in ['birth_date', 'death_date', 'birthplace', 'burial_place', 'full_name']:
        v = (meta.get(f) or '').strip()
        if not v: continue
        forms = date_forms(v) if 'date' in f else {fold(re.split(r',', v)[0])}
        where = [k for k in cands if near(pages[k], keys, forms)]
        fields[f] = {'value': v, 'found': where[:5], 'n': len(where)}
    body = html.unescape(re.sub(r'<[^>]+>', ' ', p.get('body') or ''))
    sents = [s.strip() for s in re.split(r'(?<=[.!?])\s+(?=[A-Z"])', re.sub(r'\s+', ' ', body)) if len(s.split()) >= 6]
    srows = []
    for s in sents:
        dates, years, nums, names = checkable(s, own)
        if not (dates or years or nums) and len(names) < 2:
            srows.append({'s': s, 'checkable': False, 'found': []}); continue
        found = []
        for k in cands:
            t = pages[k]
            if not all(any(re.search(r'\b' + re.escape(f) + r'\b', t) for f in date_forms(d)) for d in dates): continue
            if not all(re.search(r'\b' + y + r'\b', t) for y in years): continue
            if not all(re.search(r'\b' + re.escape(n) + r'\b', t) for n in nums): continue
            if names and sum(1 for n in names if n in t) < max(1, -(-2 * len(names) // 3)): continue
            found.append(k)
        srows.append({'s': s, 'checkable': True, 'found': found[:4], 'n': len(found), 'strong': bool(dates or years or nums)})
    report.append({'id': p['id'], 'type': p['type'], 'title': p['title'], 'fields': fields, 'sentences': srows, 'legacy_url': meta.get('legacy_url'), 'grave_url': meta.get('grave_url')})

# The report.
L = ['# Facts from the WordPress import, traced to the legacy site, 3 October 2026', '',
     'Generated by scripts/import/trace_wordpress_facts.py (read only) against the Reggie mirror. "Found" means a legacy page that names the subject also holds the fact (for a sentence: every date and number in it and two-thirds of its names). Each is a lead to a source and is read before it is cited.', '']
tot = collections.Counter()
for r in report:
    for f, v in r['fields'].items(): tot['field'] += 1; tot['field_found'] += bool(v['n'])
    for s in r['sentences']:
        tot['sent'] += 1; tot['sent_check'] += s['checkable']; tot['sent_found'] += bool(s['found']); tot['sent_found_strong'] += bool(s['found']) and s.get('strong', False)
by = collections.defaultdict(collections.Counter)
for r in report:
    c = by[r['type']]
    for f, v in r['fields'].items(): c['field'] += 1; c['field_found'] += bool(v['n'])
    for s in r['sentences']: c['sent'] += 1; c['sent_check'] += s['checkable']; c['sent_found'] += bool(s['found']); c['sent_found_strong'] += bool(s['found']) and s.get('strong', False)
L += ['| posts | fields | fields found | sentences | checkable | found |', '|---|---|---|---|---|---|']
for t, c in by.items(): L.append(f'| {t} ({sum(1 for r in report if r["type"] == t)}) | {c["field"]} | {c["field_found"]} | {c["sent"]} | {c["sent_check"]} | {c["sent_found"]} |')
L.append(f'| all | {tot["field"]} | {tot["field_found"]} | {tot["sent"]} | {tot["sent_check"]} | {tot["sent_found"]} |'); L.append('')
for r in sorted(report, key=lambda r: (r['type'] != 'person', r['title'])):
    nf = sum(1 for v in r['fields'].values() if v['n']); ns = sum(1 for s in r['sentences'] if s['found']); nc = sum(1 for s in r['sentences'] if s['checkable'])
    L.append(f'## {r["title"]} ({r["type"]})'); L.append('')
    L.append(f'Fields {nf} of {len(r["fields"])} found; sentences {ns} of {nc} checkable found ({len(r["sentences"])} in all).' + (f' WordPress legacy link: {r["legacy_url"]}.' if r['legacy_url'] else '') + (f' Grave link: {r["grave_url"]}.' if r['grave_url'] else ''))
    L.append('')
    for f, v in r['fields'].items(): L.append(f'- {f} "{v["value"]}": ' + (', '.join(v['found']) if v['n'] else 'not found'))
    for s in r['sentences']:
        if s['checkable'] and s['found']: L.append(f'- FOUND ({", ".join(x.split("/")[-1] for x in s["found"])}): {s["s"][:220]}')
    for s in r['sentences']:
        if s['checkable'] and not s['found']: L.append(f'- not found: {s["s"][:220]}')
    L.append('')
open(f'{ROOT}/inventory/review/wordpress-facts-traced-2026-10-03.md', 'w').write('\n'.join(L) + '\n')
json.dump(report, open(f'{ROOT}/inventory/review/wordpress-facts-traced-2026-10-03.json', 'w'), indent=1)
for t, c in by.items(): print(t, dict(c))
print('all', dict(tot))
