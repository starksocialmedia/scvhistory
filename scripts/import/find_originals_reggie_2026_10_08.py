# The 19 people whose portraits came off on 8 October: does the original site hold a photograph of any of them?
# (Nathan, 8 October 2026: "find the originals. Check Reggie ...".) Read only. Walks every page on Reggie, keeps the pages
# that name one of the 19 in full, and lists each picture on those pages with the text around it, so each can be looked
# at. Writes storage/runtime/photo-import/reggie-originals.json.
# Run inside the container: python3 scripts/import/find_originals_reggie_2026_10_08.py
import sys, os, re, json, html
sys.path.insert(0, '/var/www/html/scripts/import')
from _reads import reads
ROOT = '/mnt/reggie/scvhistory.com'
reads([('file', 'every .htm and .html page on Reggie, as text', ROOT),
       ('record', 'the pages\' img tags and the text near them', 'the pictures they name', 'not read: the pictures are listed here to be looked at, not opened')])
NAMES = {
  'Alan Ferdman': [r'alan\s+ferdman'], 'Audra Strickland': [r'audra\s+strickland'], 'BJ Atkins': [r'b\.?\s?j\.?\s+atkins'],
  'Bill Cooper': [r'bill\s+cooper', r'william\s+cooper'], 'Bill Miranda': [r'bill\s+miranda'], 'Bob Jensen': [r'bob\s+jensen'],
  'Brian Walters': [r'brian\s+walters'], 'Cherise Moore': [r'cherise\s+moore'], 'Erin Wilson': [r'erin\s+wilson'],
  'George Runner': [r'george\s+runner'], 'Henry Clay Wiley': [r'henry\s+c(lay|\.)\s+wiley', r'h\.\s?c\.\s+wiley'],
  'Jason Gibbs': [r'jason\s+gibbs'], 'Jerry Gladbach': [r'gladbach'], 'Joe Messina': [r'joe\s+messina'],
  'Marsha McLean': [r'marsha\s+mclean'], 'Patsy Ayala': [r'patsy\s+ayala'], 'Patti Rasmussen': [r'patti\s+rasmussen'],
  'Sharon Runner': [r'sharon\s+runner'], 'Steve Knight': [r'steve\s+knight', r'stephen\s+knight'],
}
RX = {k: re.compile('|'.join(v), re.I) for k, v in NAMES.items()}
IMG = re.compile(r'<img[^>]+src\s*=\s*["\']?([^"\'\s>]+)', re.I)
hits = {k: [] for k in NAMES}
for d, ds, fs in os.walk(ROOT):
    for f in fs:
        if not f.lower().endswith(('.htm', '.html')):
            continue
        p = os.path.join(d, f)
        try:
            t = open(p, encoding='latin-1').read()
        except OSError:
            continue
        found = [k for k, rx in RX.items() if rx.search(t)]
        if not found:
            continue
        title = re.search(r'<title>(.*?)</title>', t, re.I | re.S)
        imgs = []
        for m in IMG.finditer(t):
            src = m.group(1)
            if re.search(r'(spacer|logo|button|banner|header|arrow|icon|separator|nav)', src, re.I):
                continue
            near = re.sub(r'\s+', ' ', html.unescape(re.sub(r'<[^>]+>', ' ', t[max(0, m.start() - 400):m.end() + 400])))
            imgs.append({'src': src, 'near': near[:500]})
        for k in found:
            hits[k].append({'page': p[len(ROOT):], 'title': html.unescape(re.sub(r'\s+', ' ', title.group(1))).strip() if title else '', 'imgs': imgs})
json.dump(hits, open('/var/www/html/storage/runtime/photo-import/reggie-originals.json', 'w'), indent=1)
for k, v in hits.items():
    print(f'{k}: {len(v)} pages, {sum(len(x["imgs"]) for x in v)} pictures on them')
