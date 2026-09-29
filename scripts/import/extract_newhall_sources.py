"""
The Henry Mayo Newhall sources, extracted verbatim from the Reggie mirror.

Nine legacy pages, confirmed by Nathan on 29 September 2026 from Grok's read of
the two hub pages (inventory/review/newhall-hub-sources.md) and the inventory:

  hs6501a, hs6501b    one 1865 S.F. & S.J. Railroad receipt, two views
  lw3583              a railroad pass Newhall signed, undated, 1860s
  al1870              an S.F. & S.J. Railroad document whose Newhall signature
                      lw3583 questions
  lw2182              billhead, H.M. Newhall & Co., 15 April 1870
  al1873              billhead, H.M. Newhall & Co., 1873
  lw3756              advertisement, H.M. Newhall & Co., 1867-1874
  rn7301              photograph of his five sons, 1873
  al1882              obituary, California Spirit of the Times, 18 March 1882
  sfexaminer18820321hmn  reading of the will, S.F. Examiner, March 1882

plus ap1335, the hub, whose biography every page repeats as furniture.

Writes inventory/legacy/newhall-sources.json for import_newhall_sources.php.

VERBATIM. Nothing is retyped, corrected or summarised. Whitespace is closed up,
and that is the only change.

NOT YET SPLIT. The Mentry extractor was written with the pages open and cut
each one by hand into `text` (what the source says) and `framing` (what the
webmaster says about it). These pages have not been read on the mirror yet, so
this extractor does not guess the cut: it records every content line of each
page, in order, with the hub biography removed, and the import holds a body
until someone has read the lines and written the cut into the import's
$SPLITS. What it does find mechanically, it reports:

  every date the page prints, parsed where it parses
  every image the page shows, and whether a _large or _orig master exists
  every line naming a scan (CODE: 9600 dpi ...)
  from al1882, every line about when he came to California, so the
    "July 1850" reading can be checked against the obituary itself
  from lw3583, every line about the al1870 signature
  from sfexaminer18820321hmn, the embedded sfexaminer18740708hmn.jpg: its
    caption, whether the image and a page of that name exist, and every
    other mirror page that mentions it. A LEAD: nothing is dated 1874 on the
    strength of a filename.

Run on the host, where Reggie is mounted:
    python3 scripts/import/extract_newhall_sources.py
"""
import glob
import hashlib
import html
import json
import os
import re
import sys
from datetime import date

MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
OUT = os.path.join(os.path.dirname(__file__), '..', '..', 'inventory', 'legacy', 'newhall-sources.json')

if not os.path.isdir(MIRROR):
    sys.exit('Reggie is not connected: ' + MIRROR + ' is missing. This extractor reads the legacy mirror on the host; plug the drive in.')

KEYS = ['hs6501a', 'hs6501b', 'lw3583', 'al1870', 'lw2182', 'al1873', 'lw3756', 'rn7301', 'al1882', 'sfexaminer18820321hmn']
HUB = 'ap1335'
LEAD = 'sfexaminer18740708hmn'

MONTHS = {m: i for i, m in enumerate(['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august',
                                       'september', 'october', 'november', 'december'], 1)}
MON_ABBR = {k[:3]: v for k, v in MONTHS.items()}


def expect(cond, what):
    if not cond:
        sys.exit(f'EXTRACTION FAILED: {what}')


def page(key):
    path = f'{MIRROR}/scvhistory/{key}.htm'
    expect(os.path.isfile(path), f'{key}: {path} is not on the mirror')
    raw = open(path, 'rb').read()
    text = raw.decode('latin-1')
    title = html.unescape(re.search(r'(?is)<title>(.*?)</title>', text).group(1)).strip()
    body = text[text.lower().find('<body'):]
    imgs = []
    for m in re.finditer(r'(?is)<img[^>]*?src\s*=\s*["\']?([^"\'\s>]+)[^>]*>', body):
        tag = m.group(0)
        alt = re.search(r'(?is)alt\s*=\s*["\']([^"\']*)', tag)
        imgs.append({'src': m.group(1), 'alt': html.unescape(alt.group(1)).strip() if alt else ''})
    links = sorted(set(re.findall(r'(?is)href\s*=\s*["\']?([^"\'\s>]+)', body)))
    body = re.sub(r'(?is)<(script|style).*?</\1>', '', body)
    body = re.sub(r'(?s)<!--.*?-->', '', body)
    body = re.sub(r'\s+', ' ', body)
    body = re.sub(r'(?i)<br\s*/?>|</p>|<p[^>]*>|</div>|</tr>|</h\d>|<h\d[^>]*>|</td>', '\n', body)
    body = html.unescape(re.sub(r'<[^>]+>', '', body))
    lines = [re.sub(r'\s+', ' ', l).strip() for l in body.split('\n')]
    return {
        'path': path.replace(MIRROR, ''),
        'sha256': hashlib.sha256(raw).hexdigest(),
        'title': title,
        'lines': [l for l in lines if l],
        'images': imgs,
        'links': links,
    }


def dates(lines):
    """Every date printed, with an ISO form where it parses. Nothing inferred."""
    out = []
    for i, l in enumerate(lines):
        for m in re.finditer(r'\b([A-Z][a-z]{2,8})\.?\s+(\d{1,2}),?\s+(1[89]\d\d)\b', l):
            mon = MONTHS.get(m.group(1).lower()) or MON_ABBR.get(m.group(1).lower()[:3])
            if mon:
                out.append({'line': i, 'printed': m.group(0), 'iso': f'{m.group(3)}-{mon:02d}-{int(m.group(2)):02d}', 'granularity': 'day'})
        for m in re.finditer(r'\b(\d{1,2})[-/](\d{1,2})[-/](1[89]\d\d)\b', l):
            out.append({'line': i, 'printed': m.group(0), 'iso': f'{m.group(3)}-{int(m.group(1)):02d}-{int(m.group(2)):02d}', 'granularity': 'day'})
        for m in re.finditer(r'\b([A-Z][a-z]{2,8})\.?,?\s+(1[89]\d\d)\b', l):
            mon = MONTHS.get(m.group(1).lower()) or MON_ABBR.get(m.group(1).lower()[:3])
            if mon:
                out.append({'line': i, 'printed': m.group(0), 'iso': f'{m.group(2)}-{mon:02d}', 'granularity': 'month'})
        for m in re.finditer(r'\b(18[4-9]\d)s?\b', l):
            out.append({'line': i, 'printed': m.group(0), 'iso': m.group(1), 'granularity': 'decade' if m.group(0).endswith('s') else 'year'})
    return out


def image(src):
    """A page image as a mirror path, and its master if the mirror holds one."""
    rel = '/' + re.sub(r'^(\.\./|/)+', '', src.split('?')[0])
    if not rel.startswith('/gif/') and not rel.startswith('/scvhistory/'):
        rel = '/scvhistory' + rel
    base, ext = os.path.splitext(rel)
    out = {'file': rel, 'onMirror': os.path.isfile(MIRROR + rel)}
    for suffix in ('_large', '_orig'):
        if os.path.isfile(f'{MIRROR}{base}{suffix}{ext}'):
            out['master'] = f'{base}{suffix}{ext}'
    return out


hub = page(HUB)
furniture = set(hub['lines'])
pages = {HUB: {k: hub[k] for k in ('path', 'sha256', 'title')}, }
pages[HUB]['bio'] = hub['lines']

items = []
for key in KEYS:
    p = page(key)
    lines = [l for l in p['lines'] if l not in furniture]
    cut = len(p['lines']) - len(lines)
    own = [i for i in p['images'] if key.lower() in i['src'].lower()]
    items.append({
        'key': key,
        'path': p['path'], 'sha256': p['sha256'], 'title': p['title'],
        'lines': lines,
        'furniture_removed': cut,
        'scan_lines': [l for l in lines if re.match(rf'(?i)^{re.escape(key)}\s*:', l) or re.search(r'(?i)\b\d{2,6}\s*dpi\b', l)],
        'dates': dates(lines),
        'images': [dict(image(i['src']), alt=i['alt']) for i in own],
        'all_images': [i['src'] for i in p['images']],
    })
    expect(lines, f'{key}: no content left after the hub biography was removed')
    expect(own, f'{key}: the page shows no image named for it')

by = {it['key']: it for it in items}

# Findings for Nathan. Lines are quoted whole, never paraphrased.
findings = {}
findings['al1882_arrival'] = [l for l in by['al1882']['lines']
                              if re.search(r'(?i)\b(18[45]\d|arriv|came to|reached|landed|California|San Francisco|gold)\b', l)]
findings['al1882_birth'] = [l for l in by['al1882']['lines'] if re.search(r'(?i)\b(born|birth|1825|May 1[0-9]|May 2[0-9])\b', l)]
findings['lw3583_al1870'] = [l for l in by['lw3583']['lines']
                             if re.search(r'(?i)al1870|genuine|authentic|forg|signature|signed', l)]
findings['lw2182_arrival'] = [l for l in by['lw2182']['lines'] if re.search(r'(?i)\b(1849|1850|arriv|came to)\b', l)]
findings['hub_arrival'] = [l for l in hub['lines'] if re.search(r'(?i)\b(1849|1850|gold rush|arriv|came to)\b', l)]

sfx = page('sfexaminer18820321hmn')
lead_imgs = [i for i in sfx['images'] if LEAD in i['src'].lower()]
lead_lines = []
for i, l in enumerate(sfx['lines']):
    if re.search(r'(?i)1874|july 8|' + LEAD, l):
        lead_lines.append(l)
others = []
for f in glob.glob(f'{MIRROR}/scvhistory/*.htm'):
    if f.endswith('/sfexaminer18820321hmn.htm'):
        continue
    try:
        if LEAD.encode() in open(f, 'rb').read():
            others.append(f.replace(MIRROR, ''))
    except OSError:
        pass
findings['lead_1874'] = {
    'embedded': [dict(image(i['src']), alt=i['alt']) for i in lead_imgs],
    'lines_on_page_mentioning_1874': lead_lines,
    'page_of_that_name': os.path.isfile(f'{MIRROR}/scvhistory/{LEAD}.htm'),
    'other_pages_mentioning_it': sorted(others),
    'note': 'A lead only. The filename suggests 8 July 1874; nothing is dated from a filename.',
}

for it in items:
    for im in it['images']:
        expect(im['onMirror'] or im.get('master'), f"{it['key']}: image {im['file']} is not on the mirror")

out = {
    'meta': {
        'source': 'Reggie mirror, ' + MIRROR,
        'extracted': date.today().isoformat(),
        'extracted_by': 'scripts/import/extract_newhall_sources.py, Claude Code',
        'note': 'Verbatim, whitespace closed up. Lines are NOT yet split into text and framing: see $SPLITS in import_newhall_sources.php.',
    },
    'pages': pages,
    'items': items,
    'findings': findings,
}
os.makedirs(os.path.dirname(OUT), exist_ok=True)
with open(OUT, 'w') as fh:
    json.dump(out, fh, indent=1, ensure_ascii=False)

print(f'{len(items)} pages -> {os.path.relpath(OUT)}  (hub {HUB}: {len(hub["lines"])} lines treated as furniture)')
for it in items:
    print(f"\n== {it['key']}  {it['title']}")
    print(f"   {len(it['lines'])} content lines, {it['furniture_removed']} hub lines removed")
    for im in it['images']:
        print(f"   image {im.get('master') or im['file']}  alt: {im['alt']!r}")
    for s in it['scan_lines']:
        print(f'   scan  {s}')
    print('   dates ' + ', '.join(f"{d['printed']!r}={d['iso']}" for d in it['dates'][:12]))
    for n, l in enumerate(it['lines']):
        print(f'   {n:3d}  {l[:150]}')
print('\n== FINDINGS, quoted whole')
for k in ('al1882_arrival', 'al1882_birth', 'lw2182_arrival', 'hub_arrival', 'lw3583_al1870'):
    print(f'-- {k} ({len(findings[k])})')
    for l in findings[k]:
        print(f'   {l}')
print('-- lead_1874')
print('   ' + json.dumps(findings['lead_1874'], ensure_ascii=False, indent=1).replace('\n', '\n   '))
