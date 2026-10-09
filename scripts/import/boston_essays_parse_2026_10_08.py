# John Boston's six essays on the original site, read for import (Nathan, 8 October 2026: "Import Boston's six essays and put
# them in collection #667 ... Link him as author on all six, basis printed byline or whatever each page actually shows").
# Read only. Reads the six pages on Reggie and the series index (/scvhistory/signal/boston/jbindex.htm, which gives the three
# "Laying Down the Law" parts their dates; those pages print none). Text verbatim: the paragraphs as the page prints them,
# the headline, deck, byline and dateline as printed. Set aside, and listed: the picture boxes beside the text (other records'
# photographs and their captions), the series navigation, the copyright line (kept in the source line) and the site footer.
# A rule (<hr>) between sections is not kept. Writes inventory/review/boston-essays/boston-essays-2026-10-08.json.
# Run inside the container: python3 scripts/import/boston_essays_parse_2026_10_08.py
import sys, re, html, json, os
sys.path.insert(0, '/var/www/html/scripts/import')
from _reads import reads
R = '/mnt/reggie/scvhistory.com/scvhistory'
PAGES = ['signal/boston/jb061800.htm', 'signal/boston/jb062500.htm', 'signal/boston/jb070900.htm', 'sg082803.htm',
         'signal/boston/jb100401b.htm', 'signal/boston/jb070200.htm']
reads([('file', 'the six essay pages and the series index on Reggie', R + '/signal/boston/jbindex.htm')])
INDEX_DATES = {'jb061800.htm': 'June 18, 2000', 'jb062500.htm': 'June 25, 2000', 'jb070900.htm': 'July 9, 2000'}


def text(s):
    s = re.sub(r'<br\s*/?>', ' ', s, flags=re.I)
    s = re.sub(r'<[^>]+>', '', s)
    return re.sub(r'\s+', ' ', html.unescape(s)).strip()


idx = open(R + '/signal/boston/jbindex.htm', encoding='latin-1').read()
for f, d in INDEX_DATES.items():
    m = re.search(re.escape(f) + r'.{0,300}?\((\d+)-(\d+)-(\d{4})\)', idx, re.S)
    if not m:
        raise SystemExit(f'{f}: the index does not give its date')
out = []
for rel in PAGES:
    t = open(f'{R}/{rel}', encoding='latin-1').read()
    f = os.path.basename(rel)
    m = re.search(r'class="altheadline"[^>]*>(.*?)</(?:font|span)>', t, re.S)
    title = re.sub(r'^\d+\.\s*', '', text(m.group(1)))
    after = t[m.end():]
    sub = re.search(r'^\s*(?:<br\s*/?>\s*)?<font class="subhed2">(.*?)</font>', after, re.S)
    by = re.search(r'class="byline">(.*?)</font>', after[:800], re.S)
    dl = re.search(r'class="dateline">(.*?)</font>', after[:1200], re.S)
    note = re.search(r'Webmaster\'s note:\s*</font>\s*<font class="bodyserifbold">(.*?)</font>', after, re.S)
    epi = re.search(r'<font class="bodyserif">\s*<p align=right>(.*?)</p>', after, re.S)
    start = after.find('<div class="bodyserif">')
    body = after[start:]
    end = min([i for i in (body.find('&copy;'), body.find('&#169;'), body.find('\xa9'), body.find('RETURN TO TOP')) if i > 0])
    body = body[:end]
    pics = [(re.search(r'src\s*=\s*"([^"]+)"', b).group(1) if re.search(r'src\s*=\s*"([^"]+)"', b) else '', text(b)) for b in re.findall(r'<table.*?</table>', body, re.S | re.I)]
    body = re.sub(r'<table.*?</table>', ' ', body, flags=re.S | re.I)
    paras = [text(p) for p in re.split(r'<p[^>]*>|<hr[^>]*>', body, flags=re.I)]
    paras = [p for p in paras if p and not re.fullmatch(r'[\[\]\s|]*', p)]
    if epi:
        paras = [text(epi.group(1))] + paras
    cp = re.search(r'(?:&copy;|&#169;|\xa9)\s?(\d{4})\s+JOHN BOSTON (?:&amp;|&#38;|&) SCV HISTORICAL SOCIETY', t)
    out.append({'file': f, 'legacyUrl': '/scvhistory/' + rel, 'title': title, 'subheadline': text(sub.group(1)) if sub else '',
                'byline': text(by.group(1)) if by else '', 'dateline': text(dl.group(1)) if dl else '',
                'indexDate': INDEX_DATES.get(f, ''), 'webmasterNote': text(note.group(1)) if note else '',
                'copyright': f'©{cp.group(1)} John Boston & SCV Historical Society' if cp else '',
                'seriesHeading': 'JOHN BOSTON: LAYING DOWN THE LAW IN EARLY SANTA CLARITA' if 'LAYING DOWN THE LAW' in t[:t.find('altheadline')] else '',
                'paragraphs': paras, 'picturesSetAside': pics})
os.makedirs('/var/www/html/inventory/review/boston-essays', exist_ok=True)
json.dump(out, open('/var/www/html/inventory/review/boston-essays/boston-essays-2026-10-08.json', 'w'), indent=1, ensure_ascii=False)
for e in out:
    print(f"{e['file']}: \"{e['title']}\"" + (f" / {e['subheadline']}" if e['subheadline'] else '') + f" | byline: {e['byline'] or '(none printed)'} | date: {e['dateline'] or e['indexDate'] + ' (index)'} | "
          f"{len(e['paragraphs'])} paragraphs, {sum(len(p.split()) for p in e['paragraphs'])} words | pictures set aside: {len(e['picturesSetAside'])}"
          + (f" | webmaster's note" if e['webmasterNote'] else '') + (f" | {e['copyright']}" if e['copyright'] else ''))
    print('    first: ' + e['paragraphs'][0][:150])
    print('    last:  ' + e['paragraphs'][-1][:150])
