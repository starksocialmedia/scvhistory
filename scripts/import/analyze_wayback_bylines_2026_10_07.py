import json, os, re, html, collections
d = {x['legacyUrl']: x for x in json.load(open('storage/runtime/bylines320.json'))}
w = json.load(open('storage/runtime/wayback223.json'))
out = []
def text(b):
    t = b.decode('utf-8', 'replace') if b[:3] == b'\xef\xbb\xbf' or b'charset=utf-8' in b[:2000].lower() else b.decode('cp1252', 'replace')
    t = re.sub(r'<(script|style)\b.*?</\1>', ' ', t, flags=re.S | re.I)
    t = re.sub(r'<[^>]+>', ' | ', t)
    t = html.unescape(re.sub(r'\s+', ' ', t))
    return re.sub(r'(\s*\|\s*)+', ' | ', t)
for legacy, url in w.items():
    x = d[legacy]
    row = {'entryId': x['entryId'], 'title': x['title'], 'legacyUrl': legacy, 'person': x['personTitle'], 'capture': url}
    if not url: row['status'] = 'no capture'; out.append(row); continue
    fn = os.path.join('storage/runtime/wayback137', legacy.strip('/').replace('/', '__'))
    if not os.path.exists(fn): row['status'] = 'not fetched'; out.append(row); continue
    s = text(open(fn, 'rb').read())
    by = [m.group(0).strip(' |') for m in re.finditer(r'\bBy\s+(?:\|\s*)?(?:Dr\.\s+)?[A-Z][\w.\'-]+(?:\s+[A-Z][\w.\'-]+){0,3}', s)]
    row['bylines'] = by
    row['taylorBylines'] = sum(1 for b in by if 'Taylor' in b)
    row['otherBylines'] = [b for b in by if 'Taylor' not in b and x['personTitle'].split()[-1] not in b]
    tnorm = lambda z: re.sub(r'[^a-z0-9]', '', z.lower())
    row['titleOnPage'] = tnorm(x['title']) in tnorm(s)
    pos = s.find(by[0]) if by else -1
    row['bylinePos'] = pos; row['pageLen'] = len(s)
    row['head'] = s[:400]
    m = re.search(r'Making Cents', s); row['makingCentsMentions'] = len(re.findall(r'Making Cents', s))
    row['status'] = 'fetched'
    out.append(row)
json.dump(out, open('storage/runtime/wayback137-analysis.json', 'w'), indent=1)
c = collections.Counter(r['status'] for r in out); print(c)
f = [r for r in out if r['status'] == 'fetched']
print('taylor byline count per page', collections.Counter(r['taylorBylines'] for r in f if r['person'] == 'Sol Taylor'))
print('title found on page', collections.Counter(r['titleOnPage'] for r in f))
print('pages with other bylines', [(r['entryId'], r['otherBylines'][:3]) for r in f if r['otherBylines']][:20])
