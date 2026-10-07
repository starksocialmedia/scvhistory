"""Builds inventory/review/authorship-basis-2026-10-07.json for authorship_basis_2026_10_07.php: the basis of each of the 320
author links made on 5 October, and a note saying from what. Reads the link census (storage/runtime/bylines320.json), the legacy
pages on Reggie, and the Internet Archive captures fetched for the 223 links read from a byline kept in the body
(storage/runtime/wayback137/, analysed in storage/runtime/wayback137-analysis.json). Captures only; the live site is not read.
Run: python3 scripts/import/build_authorship_basis_2026_10_07.py"""
import json, os, re, html, collections
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
rows320 = json.load(open('storage/runtime/bylines320.json'))
wb = {r['legacyUrl']: r for r in json.load(open('storage/runtime/wayback137-analysis.json'))}
def page_text(path):
    p = MIRROR + path
    if not os.path.exists(p): return None
    t = open(p, 'rb').read().decode('cp1252', 'replace')
    t = re.sub(r'<(script|style)\b.*?</\1>', ' ', t, flags=re.S | re.I)
    t = html.unescape(re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' | ', t)))
    return re.sub(r'(\s*\|\s*)+', ' | ', t)
def capdate(url):
    m = re.search(r'/web/(\d{4})(\d{2})(\d{2})', url or '')
    months = 'January February March April May June July August September October November December'.split()
    return f'{int(m.group(3))} {months[int(m.group(2)) - 1]} {m.group(1)}' if m else None
out, flags = [], []
for x in rows320:
    b = re.sub(r'\s+', ' ', (x.get('bylinePrinted') or '').strip())
    body = x['readFrom'].startswith('body')
    url = x['legacyUrl']
    basis, note = None, None
    if b.upper().startswith('HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS'):
        s = page_text(url) or ''
        own = [m.group(0) for m in re.finditer(r'\bBy (?:\| )?Jerry Reynolds', s, flags=re.I) if not s[max(0, m.start() - 40):m.start()].upper().endswith('SANTA CLARITA VALLEY ')]
        if own: flags.append((x['entryId'], 'Reynolds page prints its own byline too', own[:2]))
        basis = 'series-attribution'
        note = 'The series heading on the original page reads "History of the Santa Clarita Valley by Jerry Reynolds"; the piece itself carries no byline of its own.'
        if 'Chapter adapted from material originally developed by Jerry Reynolds' in s:
            note += ' At the end the page adds: "Chapter adapted from material originally developed by Jerry Reynolds and the Santa Clarita Valley Chamber of Commerce."'
    elif x['entryId'] == 12200 or b.startswith('Leon Worden ·') or b.startswith('Richard "Doc" Rioux ·'):
        form = 'Leon Worden · August 20, 1997' if x['personTitle'] == 'Leon Worden' else b
        basis = 'printed-byline'
        note = f'Printed at the head of the piece as "{form}": the name and date, without "By".'
    else:
        basis = 'printed-byline'
        note = f'Printed byline "{b.split(" | ")[0].rstrip(".")}" at the head of the piece.'
    if body:
        w = wb.get(url, {})
        cap = open(os.path.join('storage/runtime/wayback137', url.strip('/').replace('/', '__')), 'rb').read().decode('cp1252', 'replace') if w.get('status') == 'fetched' else ''
        if w.get('status') == 'fetched' and (w.get('taylorBylines') or re.search(r'\bby\s+(?:dr\.\s+)?' + re.escape(x['personTitle'].split()[0]) + r'\s+' + re.escape(x['personTitle'].split()[-1]), re.sub(r'<[^>]+>|\s+', ' ', cap), re.I)):
            note += f' Read from the byline kept at the head of the text and confirmed on the original page as captured by the Internet Archive on {capdate(w["capture"])}.'
        elif w.get('status') == 'fetched':
            flags.append((x['entryId'], 'capture fetched but byline not found', w.get('bylines')))
            note += ' Read from the byline kept at the head of the text; the Internet Archive capture of the original page does not show it, so it is not confirmed there.'
        else:
            note += ' Read from the byline kept at the head of the text; the original page could not be consulted (the Internet Archive holds no capture), so it is not confirmed there.'
    else:
        s = page_text(url)
        if s is None: flags.append((x['entryId'], 'page row but page not on Reggie', url))
        note += ' Read from the original page.'
    out.append({'entryId': x['entryId'], 'title': x['title'], 'personId': x['personId'], 'person': x['personTitle'], 'legacyUrl': url, 'basis': basis, 'note': note})
json.dump({'built': '2026-10-07', 'by': 'scripts/import/build_authorship_basis_2026_10_07.py', 'rows': out, 'flags': flags}, open('inventory/review/authorship-basis-2026-10-07.json', 'w'), indent=1, ensure_ascii=False)
print(collections.Counter(r['basis'] for r in out))
print(collections.Counter((r['person'], r['basis'], 'confirmed' if 'confirmed on' in r['note'] else 'not confirmed' if 'not confirmed' in r['note'] else 'page') for r in out))
for f in flags: print('FLAG', f)
