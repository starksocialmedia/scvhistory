"""The title plan (Nathan, 7 October 2026): "Photographs: the title is the headline the page prints. Leon's catalogue caption goes in
its own field and is kept, every word, never discarded." "The 58 articles: restore the printed headlines." "Documents: drop the
suffix. A title is what the thing was called. Author, publication and date are fields."
Reads inventory/review/title-census-2026-10-07.json (the headline each legacy page prints, found by the rule in its .md).
One typographic rule: a single full stop closing a headline is the page's house style and is dropped ("Scott Newhall." is
Scott Newhall), unless it closes an abbreviation; every other character stays as printed.
Writes inventory/review/title-plan-2026-10-07.json for retitle_2026_10_07.php.
Run: python3 scripts/import/build_title_plan_2026_10_07.py"""
import json, re, collections
rows = json.load(open('inventory/review/title-census-2026-10-07.json'))
ABBR = re.compile(r'(\b(?:Jr|Sr|Co|Inc|St|Ave|Bros|No|Mrs|Mr|Dr|Ltd|Corp|Mt|Ft|Rd|etc|U\.S|D\.C|[A-Z]))\.$')
def clean(h):
    h = re.sub(r'\s+', ' ', h).strip()
    if h.endswith('.') and not h.endswith('..') and not ABBR.search(h): h = h[:-1].rstrip()
    return h
HOLD_ARTICLES = {
  12534: 'the "headline" found is the letter\'s first line ("Eureka. You have found it."), not a headline',
  12854: 'the page heads both the biography and the tributes "RICHARD \\"DOC\\" RIOUX"; what tells them apart is the subhead ("Biographical Sketch")',
  12852: 'the page heads both the biography and the tributes "RICHARD \\"DOC\\" RIOUX"; what tells them apart is the subhead ("Tributes")',
  12796: 'the page\'s heading is the book\'s, "SUNRISES, SUNSETS AND IN BETWEEN", and its title tag says "\'IMAGES\' by Richard H. Rioux": which is the piece\'s title is not clear',
}
def catalogue(r):
    # Leon's catalogue entry is the page's title tag: site, code, subject, caption. Every word is kept but the site's own name.
    t = re.sub(r'\s+', ' ', (r.get('titleTag') or '')).strip()
    return re.sub(r'^(?:SCV ?History(?:\.com)?|SaugusSpeedway\.net(?:\.com)?)\s*(?=\S)', '', t).lstrip('| ').strip()
plan = {'photographs': [], 'articles': [], 'held': [], 'unchanged': collections.Counter()}
for r in rows:
    sec, k, h = r['section'], r['kind'], r.get('headline')
    if sec == 'photographs':
        if not h or k in ('multi-piece page', 'page not on mirror', 'no discernible printed title'):
            plan['held'].append({'id': r['id'], 'section': sec, 'title': r['recordTitle'], 'why': k if h else 'no headline found'}); continue
        new = clean(h)
        plan['photographs'].append({'id': r['id'], 'old': r['recordTitle'], 'new': new, 'caption': catalogue(r),
                                    'oldInCaption': r['recordTitle'].rstrip('.') in catalogue(r), 'kind': k})
    elif sec == 'articles' and k == 'different':
        if r['id'] in HOLD_ARTICLES:
            plan['held'].append({'id': r['id'], 'section': sec, 'title': r['recordTitle'], 'headline': h, 'why': HOLD_ARTICLES[r['id']]}); continue
        plan['articles'].append({'id': r['id'], 'old': r['recordTitle'], 'new': clean(h)})
# Documents: the "(Author, Publication, Date)" suffix dropped; where the suffix held something no field holds, it moves to sourceLine.
DOCS = {
  31723: ("A Newspaper Editor's Voyage Across San Francisco Bay: San Francisco Chronicle, 1935-1971, and Other Adventures", None),
  28055: ('John Lang: Biography During Life', ('prefix', 'Pen Pictures. ')),
  28310: (None, ('prefix', 'By Laurel Suomisto. ')),
  28295: (None, ('prefix', 'By Karina Lutz. ')),
  31306: (None, ('prefix', 'From the Muehlberger family. ')),
  28303: (None, ('set-if-empty', 'Excerpt.')),
  28301: (None, ('set-if-empty', 'Excerpt.')),
  28283: (None, ('set-if-empty', 'Excerpt.')),
}
STRIP = [31412, 31336, 31334, 31332, 31326, 31314, 31312, 31308, 31306, 28310, 28305, 28303, 28301, 28295, 28291, 28283, 28201, 26966,
         26583, 31330, 31328, 31324, 31322, 31320, 31318, 31316, 31310, 31723, 28055]
plan['documents'] = [{'id': i, 'title': DOCS.get(i, (None, None))[0], 'sourceLine': DOCS.get(i, (None, None))[1]} for i in STRIP]
json.dump(plan, open('inventory/review/title-plan-2026-10-07.json', 'w'), indent=1, ensure_ascii=False)
ph = plan['photographs']
print('photographs', len(ph), 'title changes', sum(1 for p in ph if p['new'] != p['old']), 'old title not within the catalogue entry', sum(1 for p in ph if not p['oldInCaption']), '| articles', len(plan['articles']), '| held', len(plan['held']))
