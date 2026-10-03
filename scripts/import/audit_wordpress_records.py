#!/usr/bin/env python3
"""
READ ONLY. Every record that came from the WordPress file, sentence by
sentence, and where its text went (Nathan, 3 October 2026: "audit every
WordPress-origin record the same way, sentence by sentence, and withhold the
suspect ones. The Rancho El Tejon place and Portola Expedition group carrying
the same generated text means it propagated across record types. Find every
instance").

For each of the 90 posts in inventory/wp_content.json and the archive record
it became:

  rewritten    the record's body is no longer the WordPress text (under half
               its sentences are); nothing to withhold
  legacy copy  the body is a copy of one legacy page (80 per cent or more of
               its eight-word runs on that page): someone's real text, to be
               attributed and cited, as Ygnacio del Valle's was to LW2052
  suspect      anything else: graded sentence by sentence against the mirror
               and the archive, every WordPress-origin record left out

Propagation: every ten-word run of every WordPress body is looked for in every
other archive record. A run found in another record and nowhere in the mirror
is generated text that spread; a run also in the mirror is a shared source.

Input: scratchpad exports (archive_text.json: every published text in the
archive; wp_current.json: the current bodies of the WordPress-origin records;
wp_map.json).
Writes inventory/review/wordpress-records-2026-10-03.md and .json.
Run: python3 scripts/import/audit_wordpress_records.py <archive_text.json> <wp_current.json> <wp_map.json>
"""
import json, re, os, sys, html, unicodedata, collections

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
ARCH, CUR, WMAP = json.load(open(sys.argv[1])), json.load(open(sys.argv[2])), json.load(open(sys.argv[3]))['found']
WP = json.load(open(f'{ROOT}/inventory/wp_content.json'))['posts']
WPIDS = {x[2] for x in WMAP}

def fold(s):
    s = unicodedata.normalize('NFKD', html.unescape(s)).encode('ascii', 'ignore').decode().lower()
    return re.sub(r'\s+', ' ', re.sub(r'[^a-z0-9]', ' ', s)).strip()
def strip(s): return re.sub(r'\s+', ' ', html.unescape(re.sub(r'<[^>]+>', ' ', s or ''))).strip()
def sents(s): return [x.strip() for x in re.split(r'(?<=[.!?])\s+(?=[A-Z"\'(])', strip(s)) if len(x.split()) >= 6]
def grams(s, n):
    w = fold(s).split(); return {' '.join(w[i:i + n]) for i in range(len(w) - n + 1)}

mirror = []
for root, _, files in os.walk(MIRROR):
    if '/files/' in root + '/': continue
    for f in files:
        if f.endswith(('.htm', '.html')):
            p = os.path.join(root, f)
            try: t = open(p, 'rb').read().decode('cp1252', errors='replace')
            except OSError: continue
            t = re.sub(r'(?is)<(script|style)[^>]*>.*?</\1>', ' ', t)
            mirror.append((p.replace(MIRROR, ''), ' ' + fold(re.sub(r'<[^>]+>', ' ', t)) + ' '))
arch = [(a['id'], a['s'], a['t'], ' ' + fold(a['x']) + ' ') for a in ARCH if a['id'] not in WPIDS]
print(len(mirror), 'mirror pages;', len(arch), 'archive texts (WordPress records left out)', file=sys.stderr)

def in_mirror(g): return [p for p, t in mirror if ' ' + g + ' ' in t]

# grading, as in audit_generated_bodies.py
MONTHS = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']
MON = r'\b(?:' + '|'.join(m.capitalize() for m in MONTHS) + r')\.? \d{1,2},? \d{4}'
STOP = set('the a an and of in on at to for from by with as his her he she it its was were is are be been this that which who whom their they them there when where while after before during between into over under about also later first second last one two three four five six seven eight nine ten many most more some all both each other such jr sr st mr mrs dr rev don dona fr father saint san santa los las el la de del y'.split())
def date_forms(s):
    s = fold(s); m = re.match(r'^([a-z]+) (\d{1,2}) (\d{4})$', s)
    if m and m.group(1) in MONTHS:
        mo, d, y = m.group(1), str(int(m.group(2))), m.group(3); n = str(MONTHS.index(mo) + 1); dd = d.zfill(2)
        return {f'{mo} {d} {y}', f'{mo[:3]} {d} {y}', f'{mo[:4]} {d} {y}', f'{n} {d} {y}', f'{d} {mo} {y}', f'{mo} {dd} {y}', f'{mo[:3]} {dd} {y}'}
    return {s}
def parts(s, own):
    dates = [fold(m.group(0)) for m in re.finditer(MON, s)]
    rest = re.sub(MON, ' ', s)
    years = re.findall(r'\b1[5-9]\d\d\b|\b20[0-2]\d\b', rest)
    nums = [fold(n) for n in re.findall(r'\b\d[\d,]*\b', rest) if n not in years and len(n.replace(',', '')) >= 2]
    names = []
    for m in re.finditer(r"\b[A-Z][a-zA-Z'\-]+(?:\s+(?:de|del|la|y|[A-Z][a-zA-Z'\-]+))*", s):
        w = [x for x in fold(m.group(0)).split() if x not in STOP and x not in own and len(x) > 2]
        if w: names.append(' '.join(w))
    return dates, years, nums, list(dict.fromkeys(names))
CORPUS = [t for _, t in mirror] + [t for _, _, _, t in arch]
def has(t, x): return (' ' + x + ' ') in t
def grade(s, own):
    d, y, n, nm = parts(s, own)
    if not (d or y or n) and len(nm) < 2: return 'uncheckable'
    need = max(1, -(-2 * len(nm) // 3)) if nm else 0; best = 'none'
    for t in CORPUS:
        if not all(any(has(t, f) for f in date_forms(x)) for x in d): continue
        if not all(has(t, x) for x in y + n): continue
        k = sum(1 for x in nm if x in t)
        if k >= need: return 'found'
        if d or y or n or k * 2 >= len(nm): best = 'partial'
    return best

wpby = {(p['type'], p['title']): p for p in WP}
rows = []; spread = collections.defaultdict(set)
for c in CUR:
    p = wpby.get((c['type'], c['wptitle']))
    if not p: continue
    wps = sents(p['body']); cur = sents(c['body'])
    wpg = set().union(*[grams(s, 8) for s in wps]) if wps else set()
    still = [s for s in cur if len(grams(s, 8) & wpg) >= max(1, len(grams(s, 8)) // 2)]
    rec = {'id': c['id'], 'type': c['type'], 'section': c['section'], 'title': c['title'], 'status': c['status'], 'auth': c['auth'], 'url': c['url'], 'wp_sentences': len(wps), 'body_sentences': len(cur), 'still_wp': len(still)}
    # propagation of the original WordPress text
    for s in wps:
        for g in list(grams(s, 10))[::4][:6]:
            hits = [(i, sec, t) for i, sec, t, x in arch if (' ' + g + ' ') in x]
            if hits and not in_mirror(g):
                for h in hits: spread[(c['id'], c['title'])].add(h)
    if not cur or len(still) * 2 < len(cur):
        rec['verdict'] = 'rewritten' if cur else 'empty'; rows.append(rec); print(c['title'], rec['verdict'], file=sys.stderr); continue
    bg = set().union(*[grams(s, 8) for s in cur])
    best = (0, None)
    sample = list(bg)[::max(1, len(bg) // 10)][:12]
    cands = [(path, t) for path, t in mirror if any((' ' + g + ' ') in t for g in sample)]
    for path, t in cands:
        if len(bg) == 0: break
        k = sum(1 for g in bg if (' ' + g + ' ') in t) / len(bg)
        if k > best[0]: best = (k, path)
        if best[0] >= 0.95: break
    rec['legacy_match'] = [round(best[0], 2), best[1]]
    if best[0] >= 0.8:
        rec['verdict'] = 'legacy copy'
    else:
        own = set(fold(c['title']).split())
        g = [{'s': s, 'grade': grade(s, own)} for s in cur]
        rec['grades'] = g; rec['counts'] = dict(collections.Counter(x['grade'] for x in g))
        rec['verdict'] = 'suspect'
    rows.append(rec); print(c['title'], rec['verdict'], rec.get('counts', ''), rec['legacy_match'], file=sys.stderr)

L = ['# Every WordPress-origin record, 3 October 2026', '', 'Generated by scripts/import/audit_wordpress_records.py (read only). Verdicts are in the script\'s header.', '']
cnt = collections.Counter((r['section'], r['verdict']) for r in rows)
L += ['| section | verdict | records |', '|---|---|---|'] + [f'| {s} | {v} | {n} |' for (s, v), n in sorted(cnt.items())] + ['']
for v in ['suspect', 'legacy copy', 'rewritten', 'empty']:
    L.append(f'## {v}'); L.append('')
    for r in [r for r in rows if r['verdict'] == v]:
        L.append(f'### {r["title"]} ({r["section"]} #{r["id"]}, {r["status"]}{", " + r["auth"] if r["auth"] not in ("-", "") else ""})')
        L.append(f'{r["body_sentences"]} sentences, {r["still_wp"]} of them the WordPress text.' + (f' Best legacy page: {r["legacy_match"][1]} ({int(r["legacy_match"][0] * 100)} per cent).' if r.get('legacy_match') else '') + (f' Grades: {r["counts"]}.' if r.get('counts') else ''))
        for x in r.get('grades', []):
            if x['grade'] == 'none': L.append(f'- NO GROUND: {x["s"]}')
        L.append('')
L += ['## Where the WordPress text spread', '', 'Ten-word runs of a WordPress body found in another archive record and nowhere in the mirror.', '']
for (i, t), hs in sorted(spread.items(), key=lambda kv: -len(kv[1])):
    L.append(f'- {t} (#{i}) -> ' + '; '.join(f'{sec} #{hid} {ht}' for hid, sec, ht in sorted(hs)))
open(f'{ROOT}/inventory/review/wordpress-records-2026-10-03.md', 'w').write('\n'.join(L) + '\n')
json.dump({'rows': rows, 'spread': [{'from': [i, t], 'to': sorted([list(h) for h in hs])} for (i, t), hs in spread.items()]}, open(f'{ROOT}/inventory/review/wordpress-records-2026-10-03.json', 'w'), indent=1, ensure_ascii=False)
print(json.dumps({k[0] + '/' + k[1]: n for k, n in cnt.items()}), '| spread from', len(spread), 'records into', len({h for hs in spread.values() for h in hs}))
