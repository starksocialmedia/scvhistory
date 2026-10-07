import json, os, re, html, collections
M = '/Volumes/Reggie/SCVHistory/scvhistory.com'
d = json.load(open('storage/runtime/basis410/rows.json'))
def path(x):
    p = x['legacyUrl'] or re.sub(r'^https?://(www\.)?scvhistory\.com', '', x['sourcePath'] or '')
    return p.split('#')[0]
def text(p):
    f = M + p
    if os.path.isdir(f): f = os.path.join(f, 'index.html')
    if not os.path.exists(f) and '/old/' in f: f = f.replace('/old/', '/')
    if not os.path.exists(f): return None
    t = open(f, 'rb').read().decode('cp1252', 'replace')
    m = re.search(r'http-equiv="refresh"[^>]*URL=([^"\s>]+)', t, re.I)
    if m and len(t) < 400:
        f = os.path.normpath(os.path.join(os.path.dirname(f), m.group(1)))
        if not os.path.exists(f): return None
        t = open(f, 'rb').read().decode('cp1252', 'replace')
    i = t.find('XWP-BEGIN-CONTENT'); t = t[i:] if i > 0 else t
    t = re.sub(r'<(script|style)\b.*?</\1>', ' ', t, flags=re.S | re.I)
    t = html.unescape(re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' | ', t)))
    return re.sub(r'(\s*\|\s*)+', ' | ', t)
def norm(s): return re.sub(r'\s+', ' ', s)
out = []
for x in d:
    a = x['authors'][0]['title']; last = a.split()[-1].rstrip('.'); first = a.split()[0]
    p = path(x); s = text(p) if p else None
    src = 'page' if s else 'body'
    if not s: s = norm(re.sub(r'\[/?lines\]', ' | ', x['body'] + ' ... ' + x['bodyEnd']))
    t = norm(x['title'])
    ti = s.lower().find(t.lower()[:30]) if t else -1
    head = s[max(ti, 0): max(ti, 0) + 500] if ti >= 0 else s[:700]
    name = r'(?:' + re.escape(first) + r'\b[^|]{0,25}?|(?:[A-Z][\w."\']*\.?\s+){1,3})?' + re.escape(last)
    kind, ev = None, None
    m = re.search(r'\bby\s*\|?\s*(?:dr\.\s+)?' + name, head, re.I) or re.search(r'\bby\s*\|?\s*(?:dr\.\s+)?' + name, s[:600], re.I)
    if m: kind, ev = 'printed-byline', (head if m.re.pattern and m.string is head else s[:600])[max(0, m.start() - 60): m.end() + 40]
    if not kind:
        m = re.search(r'(?<!photo )(?<!photos )\bby\s*\|?\s*(?:dr\.\s+)?' + name, s[:6000], re.I)
        if m and not re.search(r'photo\w*\s*\|?\s*$', s[max(0, m.start() - 12):m.start()], re.I): kind, ev = 'printed-byline', s[max(0, m.start() - 60): m.end() + 40]
    if not kind:
        m = re.search(name + r'\s*\|?\s*[·•]\s*\|?\s*\w+ \d', head, re.I)
        if m: kind, ev = 'printed-byline', head[max(0, m.start() - 40): m.end() + 30]
    sh = re.search(r"([A-Z][A-Z' &.,-]{6,}\sBY\s[A-Z][A-Z. '\"]*" + re.escape(last.upper()) + r")", s[:900])
    if sh and (not kind or sh.group(1).upper() in (ev or '').upper()):
        kind, ev = 'series-attribution', sh.group(1)
    if not kind:
        ms = [m for m in re.finditer(name, s, re.I)]
        if ms:
            m = ms[-1]; pos = m.start() / max(len(s), 1)
            kind = 'tail' if pos > 0.6 else 'name-elsewhere'; ev = s[max(0, m.start() - 80): m.end() + 80]
        else: kind, ev = 'not-on-page', ''
    out.append({**{k: x[k] for k in ('entryId', 'section', 'title')}, 'person': a, 'personId': x['authors'][0]['id'], 'path': p, 'src': src, 'kind': kind, 'ev': ev, 'collection': x['collection'], 'head': head[:300]})
json.dump(out, open('storage/runtime/basis410/classified.json', 'w'), indent=1, ensure_ascii=False)
print(collections.Counter((o['kind'], o['src']) for o in out))
print(collections.Counter((o['person'], o['kind']) for o in out if o['kind'] != 'printed-byline'))
