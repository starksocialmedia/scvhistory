#!/usr/bin/env python3
"""
READ ONLY. The 23 person bodies withheld as "wordpress-import-unsourced" were
generated, not written: their markup carries the class of a chatbot's answer
(font-claude-response-body). Nathan, 3 October 2026: "Treat every one of them
as suspect rather than merely unsourced. For each: how much traces to a real
source in the archive, and how much traces to nothing."

Every checkable sentence (one with a date, year, number or two names other
than the subject's) is graded against the Reggie mirror and the archive's own
texts (every published body, caption, footnote and note; the withheld bodies
themselves left out):

  strict   a page that names the subject holds every date and number in the
           sentence and two-thirds of its names (trace_wordpress_facts.py)
  loose    any page holds every date and number and two-thirds of the names
  partial  any page holds every date and number, or (with none) half the names
  none     nothing anywhere holds the sentence's dates and numbers together

A sentence graded "none" is one the archive gives no ground for. It may still
be true (general history the archive never set out to hold), but nothing here
supports it, and some contradict what the archive does hold.

Input: scratchpad export of the archive's texts (archive_text.json) and
inventory/review/wordpress-facts-traced-2026-10-03.json.
Writes inventory/review/generated-bodies-2026-10-03.md and .json.
Run: python3 scripts/import/audit_generated_bodies.py <archive_text.json> <wp_map.json>
"""
import json, re, os, sys, html, unicodedata, collections

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
WITHHELD = ['Rodolfo Acosta', 'Rémi Nadeau (I)', 'John Gifford', 'Scott Wilk', 'Henry Clay Wiley', 'José Antonio Aguirre', 'Edward Fitzgerald Beale', 'Juan Bandini', 'William Lewis Manly', 'James W. Marshall', 'Kit Carson', 'Edwin Bryant', 'Thomas O. Larkin', 'John C. Frémont', 'Francisco Lopez', 'Juventino del Valle', 'Juan Bautista de Anza', 'Junípero Serra', 'Juan Crespí', 'Gaspar de Portolá', 'Ygnacio del Valle', 'Father Francisco Garcés', 'Pedro Fages']

def fold(s):
    s = unicodedata.normalize('NFKD', html.unescape(s)).encode('ascii', 'ignore').decode().lower()
    return re.sub(r'\s+', ' ', re.sub(r'[^a-z0-9]', ' ', s)).strip()

texts = []
for root, _, files in os.walk(MIRROR):
    if '/files/' in root + '/': continue
    for f in files:
        if f.endswith(('.htm', '.html')):
            p = os.path.join(root, f)
            try: t = open(p, 'rb').read().decode('cp1252', errors='replace')
            except OSError: continue
            t = re.sub(r'(?is)<(script|style)[^>]*>.*?</\1>', ' ', t)
            texts.append(('mirror ' + p.replace(MIRROR, ''), fold(re.sub(r'<[^>]+>', ' ', t))))
# Every record that came from the WordPress file is left out of the corpus, so that one
# generated text cannot vouch for another (the Rancho El Tejon place record repeats Beale's
# body; the Portola Expedition group record repeats Serra's).
WPIDS = {x[2] for x in json.load(open(sys.argv[2]))['found']} if len(sys.argv) > 2 else set()
for a in json.load(open(sys.argv[1])):
    if a['id'] in WPIDS: continue
    texts.append((f'archive {a["s"]} #{a["id"]} {a["t"][:50]}', fold(a['x'])))
print(len(texts), 'texts')

MONTHS = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']
def date_forms(s):
    s = fold(s); m = re.match(r'^([a-z]+) (\d{1,2}) (\d{4})$', s)
    if m and m.group(1) in MONTHS:
        mo, d, y = m.group(1), str(int(m.group(2))), m.group(3); n = str(MONTHS.index(mo) + 1)
        dd = d.zfill(2)  # Leon's lists pad the day ("May 04, 1852")
        return {f'{mo} {d} {y}', f'{mo[:3]} {d} {y}', f'{mo[:4]} {d} {y}', f'{n} {d} {y}', f'{d} {mo} {y}', f'{mo} {dd} {y}', f'{mo[:3]} {dd} {y}'}
    return {s}
STOP = set('the a an and of in on at to for from by with as his her he she it its was were is are be been this that which who whom their they them there when where while after before during between into over under about also later first second last one two three four five six seven eight nine ten many most more some all both each other such jr sr st mr mrs dr rev don dona fr father saint san santa los las el la de del y'.split())
CAP = r"\b[A-Z][a-zA-Z'\-]+(?:\s+(?:de|del|la|y|[A-Z][a-zA-Z'\-]+))*"
MON = r'\b(?:' + '|'.join(m.capitalize() for m in MONTHS) + r')\.? \d{1,2},? \d{4}'
def parts(s, own):
    dates = [fold(m.group(0)) for m in re.finditer(MON, s)]
    rest = re.sub(MON, ' ', s)
    years = re.findall(r'\b1[5-9]\d\d\b|\b20[0-2]\d\b', rest)
    nums = [fold(n) for n in re.findall(r'\b\d[\d,]*\b', rest) if n not in years and len(n.replace(',', '')) >= 2]
    names = []
    for m in re.finditer(CAP, s):
        w = [x for x in fold(m.group(0)).split() if x not in STOP and x not in own and len(x) > 2]
        if w: names.append(' '.join(w))
    return dates, years, nums, list(dict.fromkeys(names))

def has(t, x): return re.search(r'\b' + re.escape(x) + r'\b', t) is not None
def grade(s, own, subj):
    d, y, n, nm = parts(s, own)
    need = max(1, -(-2 * len(nm) // 3)) if nm else 0
    best, where = 'none', []
    for lab, t in texts:
        if not all(any(has(t, f) for f in date_forms(x)) for x in d): continue
        if not all(has(t, x) for x in y + n): continue
        k = sum(1 for x in nm if x in t)
        g = 'loose' if k >= need else ('partial' if (d or y or n or k * 2 >= len(nm)) else None)
        if g and any(has(t, x) for x in subj) and k >= need: g = 'strict'
        if g and ['none', 'partial', 'loose', 'strict'].index(g) > ['none', 'partial', 'loose', 'strict'].index(best): best, where = g, [lab]
        elif g == best and len(where) < 3: where.append(lab)
    return best, where, (d, y, n, nm)

R = json.load(open(f'{ROOT}/inventory/review/wordpress-facts-traced-2026-10-03.json'))
out = []
for r in R:
    if r['type'] != 'person' or r['title'] not in WITHHELD: continue
    tw = [x for x in fold(re.sub(r'\([^)]*\)', ' ', r['title'])).split() if x not in STOP and len(x) > 2]
    own = set(tw); subj = {' '.join([tw[0], tw[-1]])} if len(tw) >= 2 else set(tw)
    rows = []
    for s in r['sentences']:
        if not s['checkable']: rows.append({'s': s['s'], 'grade': 'uncheckable'}); continue
        g, w, p = grade(s['s'], own, subj)
        rows.append({'s': s['s'], 'grade': g, 'where': w, 'parts': p})
    out.append({'title': r['title'], 'rows': rows})
    print(r['title'], collections.Counter(x['grade'] for x in rows))

tot = collections.Counter(x['grade'] for o in out for x in o['rows'])
L = ['# The 23 generated person bodies, sentence by sentence, 3 October 2026', '',
     'Generated by scripts/import/audit_generated_bodies.py (read only). The bodies withheld as wordpress-import-unsourced came from the WordPress file. Of its 90 posts, six carry direct marks of a chat tool (interface markup or pasted citation labels): Reynolds, Dante Acosta, Wilk, the hospital, the Rudy Acosta memorial record and one place; of these 23, only Wilk. All 23 are treated as suspect. Every record that came from the WordPress file is left out of the corpus they are graded against. Each checkable sentence is graded against the Reggie mirror and the archive\'s own texts. Grades are in the script\'s header; "none" means nothing anywhere holds the sentence\'s dates and numbers together.', '',
     '| grade | sentences |', '|---|---|'] + [f'| {g} | {tot[g]} |' for g in ['strict', 'loose', 'partial', 'none', 'uncheckable']] + ['']
L += ['| body | strict | loose | partial | none | uncheckable |', '|---|---|---|---|---|---|']
for o in sorted(out, key=lambda o: -sum(1 for x in o['rows'] if x['grade'] == 'none')):
    c = collections.Counter(x['grade'] for x in o['rows'])
    L.append(f'| {o["title"]} | {c["strict"]} | {c["loose"]} | {c["partial"]} | {c["none"]} | {c["uncheckable"]} |')
L += ['', '## Sentences with no ground in the archive or the legacy site', '']
for o in out:
    none = [x for x in o['rows'] if x['grade'] == 'none']
    if not none: continue
    L.append(f'### {o["title"]}'); L.append('')
    for x in none: L.append(f'- {x["s"]}')
    L.append('')
L += ['## Partial matches (some of the sentence found, not all)', '']
for o in out:
    for x in o['rows']:
        if x['grade'] == 'partial': L.append(f'- {o["title"]}: {x["s"][:200]} ({"; ".join(x["where"][:2])})')
open(f'{ROOT}/inventory/review/generated-bodies-2026-10-03.md', 'w').write('\n'.join(L) + '\n')
json.dump(out, open(f'{ROOT}/inventory/review/generated-bodies-2026-10-03.json', 'w'), indent=1, ensure_ascii=False)
print(dict(tot))
