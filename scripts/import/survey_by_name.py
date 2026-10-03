#!/usr/bin/env python3
"""
READ ONLY. Every page on the legacy site substantially about each record, by
name rather than by page shape (Nathan, 3 October 2026: "For all 134 people,
search the mirror for every page substantially about them, by name and by
alias, not by page shape ... Leon spent thirty years writing about these
people and we have been counting what we could pattern-match rather than what
he wrote").

For each record (people; then places and organizations), its names are its
title, its full name and its aliases. Every page in the Reggie mirror is read
as folded text (accents, case and punctuation set aside, so a date or name
split across markup still matches). A page is about the record when:

  titled   its title carries one of the names, and its prose (lines of twelve
           words or more, without the site's rights notice) names it again
  filed    its title does not carry the name, but the prose names it in full
           three times or more and the key word (a person's surname) comes at
           least once in every 200 words: Leon's biographies filed under a
           topic ("Early California", "Rancho Camulos") are found this way.
           A surname is not counted where it is part of a place ("Pico
           Canyon", "Beale's Cut", "Frémont Pass") or is the town ("in
           Newhall").

For people, a name is matched as written or as first and last word with up to
two words between ("Jo Anne Darcy", "George Ludvig Pederson"); for places and
organizations, as written.

Per page: its key, its kind (from its path and title), how many words of its
prose are about the record (all of it for a page titled for the record;
for a page filed under a topic, the sentences that name it), and whether the archive
already holds it (the page's path among the records' legacyUrl, legacyKey and
sourcePath). It would support a profile when 250 words or more are about the
record and the page is about them, not by them (a byline: their own column),
not an object or film (a lobby card, a medal, a cartoon), and not a place
named for them (their home, their shop), which are listed apart; 100 to 249
words is "some".

Run: python3 scripts/import/survey_by_name.py <kind> <records.json> <paths.json>
  kind is persons, places or organizations; records.json is an export of the
  section (id, title, full, aliases, shows); paths.json the held legacy paths.
Writes inventory/review/by-name-<kind>-2026-10-03.md and .json.
"""
import json, re, os, sys, html, unicodedata, collections

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
KIND, RECS, PATHS = sys.argv[1], json.load(open(sys.argv[2])), json.load(open(sys.argv[3]))

def fold(s):
    s = unicodedata.normalize('NFKD', html.unescape(s)).encode('ascii', 'ignore').decode().lower()
    return re.sub(r'\s+', ' ', re.sub(r'[^a-z0-9]', ' ', s)).strip()

RIGHTS = re.compile(r'SCVTV|SOLELY RESPONSIBLE|additional restrictions|Personal or Research use|ownership of any original|enable JavaScript', re.I)
pages = []
for root, _, files in os.walk(MIRROR):
    if '/files/' in root + '/' or '/galleries/' in root + '/': continue
    for f in files:
        if not f.endswith(('.htm', '.html')): continue
        p = os.path.join(root, f); path = p.replace(MIRROR, '')
        if re.search(r'(^|/)(index|obits|people|whatsnew|sitemap|search|bibliography|photocredits|timeline)\.html?$', path): continue
        try: raw = open(p, 'rb').read().decode('cp1252', errors='replace')
        except OSError: continue
        m = re.search(r'(?is)<title>(.*?)</title>', raw); title = html.unescape(re.sub(r'\s+', ' ', m.group(1))).strip() if m else ''
        t = re.sub(r'(?is)<(script|style|select)[^>]*>.*?</\1>', ' ', raw)
        t = re.sub(r'(?i)<br\s*/?>|</p>|</tr>|</div>|</td>|</li>|</h\d>', '\n', t)
        t = html.unescape(re.sub(r'<[^>]+>', ' ', t)).split('[ RETURN TO TOP ]')[0]
        lines = [re.sub(r'\s+', ' ', l).strip() for l in t.split('\n')]
        prose = [l for l in lines if len(l.split()) >= 12 and not RIGHTS.search(l)]
        sents = [s for l in prose for s in re.split(r'(?<=[.!?])\s+', l)]
        pages.append({'path': path, 'title': title, 'ftitle': fold(title), 'fprose': fold(' '.join(prose)), 'words': sum(len(l.split()) for l in prose), 'sents': [(fold(s), len(s.split())) for s in sents]})
print(len(pages), 'pages read', file=sys.stderr)

held = {}
for r in PATHS:
    for v in r['v']:
        v = v.strip()
        m = re.search(r'(/[\w./\-~%]+\.html?)$', v)
        if m: held.setdefault(m.group(1).lower(), []).append((r['s'], r['id'], r['t']))
        elif re.match(r'^[a-z]{1,4}\d+[a-z]?$', v, re.I): held.setdefault('/scvhistory/' + v.lower() + '.htm', []).append((r['s'], r['id'], r['t']))

STOP = set('jr sr ii iii iv 3rd dr rev mr mrs ms fr father fray don dona sister saint st capt gen col sen judge mayor the of de del la y los las el'.split())
def forms(rec):
    names = [rec['title'], rec.get('full', '')] + re.split(r'[;\n|]', rec.get('aliases') or '')
    out = set(); keys = set()
    for n in names:
        n = re.sub(r'\([^)]*\)|"[^"]*"|\'\'[^\']*\'\'', ' ', n or '')
        f = fold(n)
        if not f or len(f) < 4: continue
        w = [x for x in f.split() if x not in STOP]
        if KIND == 'persons':
            if len(w) < 2: continue  # a one-word alias ("Beale") matches every "Beale's Cut"
            if len(w) >= 2:
                out.add(r'\b' + re.escape(w[0]) + r'(?: \w+){0,2} ' + re.escape(w[-1]) + r'\b'); keys.add(w[-1])
            out.add(r'\b' + re.escape(f) + r'\b')
        else:
            out.add(r'\b' + re.escape(f) + r'\b'); keys.add(f)
    return [re.compile(x) for x in out], keys

PLACEWORD = r'(?:s|s s)? (?:canyon|cut|pass|ranch|rancho|station|park|springs|spring|land|avenue|ave|street|st|road|rd|boulevard|blvd|school|elementary|junior|high|junction|county|hotel|house|mansion|adobe|tunnel|oil|field|signal|creek|grade|crossing|memorial|library|theatre|theater|hall|museum|parkway|lake|valley|hills|peak|mountain|mine|mines|well|wells|city|town|village|district|trail|bridge|dam|camp|fort|air|depot|post|cemetery|church|company|co|club|plaza|square|center|centre|mall|ranchos|grant|tract|subdivision|community|stage|stop|crossing|homestead|reservoir)\b'
def key_count(text, k):
    """a key word's uses as a name: not as part of a place ("Pico Canyon", "Beale's Cut") nor as the town ("in Newhall")"""
    n = 0
    for m in re.finditer(r'\b' + re.escape(k) + r'\b', text):
        after = text[m.end():m.end() + 30]; before = text[max(0, m.start() - 22):m.start()]
        if re.match(PLACEWORD, after): continue
        if re.search(r'\b(?:in|at|of|to|from|near|downtown|old town|through|into|around|north|south|east|west)\s$', before): continue
        n += 1
    return n

def kind_of(path, title):
    p, t = path.lower(), title
    if 'obituar' in p or '| Obituaries |' in t: return 'obituary'
    if 'Santa Clarita City Council |' in t: return 'council portrait'
    if 'penpictures' in p or 'Pen Pictures' in t: return 'Pen Pictures, 1889'
    if 'newsmaker' in p or 'Newsmaker' in t: return 'Newsmaker of the Week'
    if 'mwoty' in p or re.search(r'(Man|Woman) of the Year', t): return 'Man or Woman of the Year'
    if '/signal/worden/' in p: return 'Leon Worden column'
    if '/signal/reynolds/' in p or re.search(r'/part\d\d', p): return 'Reynolds history'
    if 'perkins' in p: return 'Perkins'
    if '/oldtownnewhall/' in p: return 'Old Town Newhall column'
    if '/signal/' in p or re.search(r'/sg\d', p): return 'Signal story'
    if re.search(r'\b[A-Z]{1,4}\d{3,4}[a-z]?\s*\|', t): return 'portrait or photo page'
    return 'page'

out = []
for rec in RECS:
    pats, keys = forms(rec)
    if not pats: continue
    found = []
    for pg in pages:
        tl = any(p.search(pg['ftitle']) for p in pats)
        n_full = sum(len(p.findall(pg['fprose'])) for p in pats)
        if not tl and n_full < 3: continue
        n_key = max((key_count(pg['fprose'], k) for k in keys), default=0)
        if tl and not (n_full >= 1 or n_key >= 2): continue
        if not tl and (pg['words'] == 0 or n_key * 200 < pg['words']): continue
        # A page titled for the record is about it throughout ("she", "the mayor"); a page filed under a topic only where it names it.
        about = pg['words'] if tl else sum(w for s, w in pg['sents'] if any(p.search(s) for p in pats) or any(key_count(s, k) for k in keys))
        # What the page is to the record: about them, by them (a byline), an object or film, or a place named for them.
        tt = pg['title']; ft = pg['ftitle']
        names_t = [p for p in pats if p.search(ft)]
        raw = re.sub(r'^SCVHistory\.com\s*\|?\s*|^SCVNewsmaker\.com\s*\|?\s*', '', tt)
        segs = [x.strip() for x in raw.split('|') if x.strip()]
        lead = re.split(r':', segs[0], 1) if segs else ['']
        byline = re.search(r'\b[Bb]y\s+(.+)$', raw)
        if KIND == 'persons' and ((byline and any(p.search(fold(byline.group(1))) for p in pats))
                or (len(segs) > 1 and any(p.search(fold(segs[0])) for p in pats) and len(fold(segs[0]).split()) <= 3 and not any(p.search(fold(' '.join(segs[1:]))) for p in pats))
                or (len(lead) > 1 and any(p.search(fold(lead[0])) for p in pats) and len(fold(lead[0]).split()) <= 3 and not any(p.search(fold(lead[1])) for p in pats))
                or 'dmanzer' in ft):
            role = 'by them'
        elif re.search(r"\b(Lobby Cards?|Poster|Collection|Medal|Token|Cartoon|Card|Program Book|Program|Clip|Sculpture|Statue|Landmark|Survey|Pinback|Button|Matchbook|Bottle|Pitcher|Goblet|Glass|Ingot|Coin|Stars? in|Co-?stars?|Starring|Film|Movie|Video)\b", tt):
            role = 'object or film'
        elif KIND == 'persons' and re.search(r"(?:'s|’s)\s+(?:[A-Z][a-z]+\s+){0,2}(?:Home|House|Shop|Blacksmith|Ranch|Store|Station|Hotel|Park|Deer|Mine|Saloon|Cafe|Café|Garage|Adobe|Mansion|Building|Property|Land)\b|\b(?:Home|House|Ranch)\b", tt):
            role = 'place'
        else:
            role = 'about'
        h = held.get(pg['path'].lower(), [])
        found.append({'path': pg['path'], 'key': pg['path'].rsplit('/', 1)[-1].rsplit('.', 1)[0], 'title': re.sub(r'^SCVHistory\.com\s*\|?\s*', '', pg['title'])[:140], 'kind': kind_of(pg['path'], pg['title']), 'role': role, 'how': 'titled' if tl else 'filed', 'about': about, 'words': pg['words'], 'held': [f'{s} #{i}' for s, i, _ in h]})
    found.sort(key=lambda x: -x['about'])
    sup = [f for f in found if f['about'] >= 250 and f['role'] == 'about']; some = [f for f in found if 100 <= f['about'] < 250 and f['role'] == 'about']
    place = [f for f in found if f['about'] >= 250 and f['role'] == 'place']
    out.append({'id': rec['id'], 'title': rec['title'], 'shows': rec.get('shows'), 'pages': found, 'supports': len(sup), 'some': len(some), 'place': len(place), 'unheld_supports': sum(1 for f in sup if not f['held'])})
    print(rec['title'], len(found), len(sup), file=sys.stderr)

# The report.
n = len(out)
any_sup = [o for o in out if o['supports']]; any_some = [o for o in out if not o['supports'] and o['some']]
empty = [o for o in out if not o['shows']]
L = [f'# Legacy pages about each {KIND[:-1]}, by name, 3 October 2026', '',
     'Generated by scripts/import/survey_by_name.py (read only) from the Reggie mirror. Matching is by name and alias, not by page shape; the rules are in the script\'s header. "About" is the words of the page\'s prose in sentences that name the record. A page supports a profile at 250 words about the record; "some" is 100 to 249.', '',
     f'- {n} records; {len(any_sup)} have at least one page that would support a profile, {len(any_some)} more have only "some".',
     f'- Of the {len(empty)} that show no text yet: {sum(1 for o in empty if o["supports"])} have a supporting page, {sum(1 for o in empty if not o["supports"] and o["some"])} have only some, {sum(1 for o in empty if not o["supports"] and not o["some"])} have none.',
     f'- Supporting pages not yet held as a record in the archive: {sum(o["unheld_supports"] for o in out)}.', '',
     '| record | shows text | pages naming it | support a profile | some | place pages | of those supporting, not held |', '|---|---|---|---|---|---|---|']
for o in sorted(out, key=lambda o: (o['shows'], -o['supports'], o['title'])):
    L.append(f'| {o["title"]} | {"yes" if o["shows"] else "no"} | {len(o["pages"])} | {o["supports"]} | {o["some"]} | {o["place"]} | {o["unheld_supports"]} |')
L.append('')
for o in sorted(out, key=lambda o: (o['shows'], o['title'])):
    L.append(f'## {o["title"]} (#{o["id"]}){"" if o["shows"] else ", no text yet"}'); L.append('')
    if not o['pages']: L.append('No page on the legacy site is about this record by name.'); L.append(''); continue
    for f in o['pages']:
        verdict = ('supports a profile' if f['about'] >= 250 else ('some' if f['about'] >= 100 else 'little')) if f['role'] == 'about' else f['role']
        L.append(f'- {f["key"]} ({f["kind"]}, {f["how"]}, {f["role"]}): {f["title"]}. {f["about"]} of {f["words"]} words about it; {verdict}; ' + (('held: ' + ', '.join(f['held'])) if f['held'] else 'not held'))
    L.append('')
open(f'{ROOT}/inventory/review/by-name-{KIND}-2026-10-03.md', 'w').write('\n'.join(L) + '\n')
json.dump(out, open(f'{ROOT}/inventory/review/by-name-{KIND}-2026-10-03.json', 'w'), indent=1, ensure_ascii=False)
print(json.dumps({'records': n, 'with_support': len(any_sup), 'only_some': len(any_some), 'empty': len(empty), 'empty_with_support': sum(1 for o in empty if o['supports'])}))
