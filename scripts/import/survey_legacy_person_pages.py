#!/usr/bin/env python3
"""
READ ONLY. Every page on the legacy site that is substantially about one person
(Nathan, 3 October 2026: "The legacy site has biographical pages for people and
we have been writing profiles without knowing which ones exist. Find them all").

Reads the legacy map (inventory/legacy/scvhistory-map.json, every page with its
title) and, for the text of a page, the Reggie mirror. A page is a person page
when its title has one of the shapes below and names one person as its subject:

  council      "Santa Clarita City Council | Name, years" (SC1310 and its kin)
  obituary     obituary_*.htm and the Obituaries category; eulogies
  mwoty        SCV Man or Woman of the Year (one honoree, not a nominee list)
  newsmaker    Newsmaker of the Week (The Signal's interview profile)
  biography    Biography / Pen Pictures (1889) / "Biography During Life" /
               Profile / In Memoriam / Remembering / Tribute / About
  walk         Newhall Walk of Western Stars inductee pages
  people       the People category, titled with one person's name (not a
               family, a couple, a class or a photograph of an event)

The subject's name is taken from the title and matched to the archive's person
records by title, full name and aliases, with middle names and initials, and
suffixes, set aside. Pages naming two people, a family or a group are left out.

Input: the person export (persons.json, made by craft exec from the persons
section: id, title, fullName, aliases, whether a biography shows) and the 108
people with no published biography on 2 October
(inventory/review/person-profile-sources-2026-10-02.json).
Writes inventory/review/legacy-person-pages-2026-10-03.md and .json.
Run: python3 scripts/import/survey_legacy_person_pages.py <persons.json>
"""
import json, re, sys, html, collections, unicodedata, os

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
persons = json.load(open(sys.argv[1]))
empty108 = {t['id']: t['title'] for t in json.load(open(f'{ROOT}/inventory/review/person-profile-sources-2026-10-02.json'))}
showing = {p['id'] for p in persons if p['shows']}

pages = {}
def walk(o):
    if isinstance(o, dict):
        if isinstance(o.get('title'), str) and o.get('path'): pages[o['path']] = o
        for v in o.values(): walk(v)
    elif isinstance(o, list):
        for v in o: walk(v)
walk(json.load(open(f'{ROOT}/inventory/legacy/scvhistory-map.json')))

def prose(path):
    """the page's prose: lines of twelve words or more above RETURN TO TOP, without the rights notice"""
    f = MIRROR + path
    if not os.path.isfile(f): return 0, ''
    t = open(f, 'rb').read().decode('cp1252', errors='replace')
    t = re.sub(r'(?is)<(script|style|select)[^>]*>.*?</\1>', '', t); t = re.sub(r'(?i)<br\s*/?>|</p>|</tr>|</div>|</td>|</li>|</h\d>', '\n', t)
    t = html.unescape(re.sub(r'<[^>]+>', ' ', t)).split('[ RETURN TO TOP ]')[0]
    keep = [re.sub(r'\s+', ' ', l).strip() for l in t.split('\n')]
    keep = [l for l in keep if len(l.split()) >= 12 and not re.search(r'SCVTV|SOLELY RESPONSIBLE|additional restrictions|Personal or Research use|ownership of any original', l)]
    body = ' '.join(keep)
    return len(body.split()), body

def fold(s):
    s = unicodedata.normalize('NFKD', s).encode('ascii', 'ignore').decode()
    return re.sub(r'[^a-z ]', ' ', s.lower())

SUFFIX = {'jr', 'sr', 'ii', 'iii', 'iv', '3rd', 'rd', 'dr', 'rev', 'judge', 'mayor', 'capt', 'gen', 'col', 'sen', 'state', 'mrs', 'mr', 'ms', 'councilman', 'councilwoman', 'supervisor', 'brig', 'lt', 'sgt', 'cpl', 'pvt', 'father', 'fr', 'don', 'dona', 'sister', 'reverend', 'doctor', 'ed', 'phd', 'md', 'esq'}
def key(name):
    """first and last word of a name, with titles, initials, suffixes and quoted nicknames set aside"""
    name = re.sub(r'\([^)]*\)|"[^"]*"|\'\'[^\']*\'\'|“[^”]*”', ' ', name)
    w = [x for x in fold(name).split() if x not in SUFFIX and len(x) > 1]
    return (w[0], w[-1]) if len(w) >= 2 else None

index = collections.defaultdict(set)
for p in persons:
    for n in [p['title'], p['full']] + re.split(r'[;\n|]', p['aliases'] or ''):
        k = key(n.strip()) if n and n.strip() else None
        if k: index[k].add(p['id'])
byid = {p['id']: p for p in persons}

SHAPES = [
    ('council', lambda t, p: re.search(r'\| Santa Clarita City Council \|', t)),
    ('obituary', lambda t, p: 'obituary' in p.lower() or re.search(r'\| Obituaries \||Eulogy', t)),
    ('mwoty', lambda t, p: re.search(r'(Man|Woman) of the Year', t) and not re.search(r'Nominees|Program|Dinner|Man & Woman of the Year\s*$|Man & Woman of the Year \|?$|\d{4} & \d{4}', t)),
    ('newsmaker', lambda t, p: re.search(r'Newsmaker of the', t)),
    ('biography', lambda t, p: re.search(r'Biograph|Pen Pictures|Profile:|In Memoriam|Remembering |Tribute to|Tribute:|\| About ', t)),
    ('walk', lambda t, p: re.search(r'\| Walk of Western Stars \|', t)),
    ('people', lambda t, p: re.search(r'\| People \|', t)),
    ('portrait', lambda t, p: True),
]
NOTNAME = re.compile(r'\b(Map|School|Schools|Ranch|Canyon|Station|Hotel|Mine|Mines|Oil|Well|Road|Pass|Dam|Park|Church|Mission|House|Home|Building|Store|Company|Co|Corp|Inc|Railroad|Tunnel|Fire|Flood|Earthquake|Parade|Festival|Fair|Rodeo|Club|Society|Association|Council|Board|District|County|City|Town|Valley|Airport|Speedway|Stadium|Library|Museum|Theatre|Theater|Lobby|Card|Poster|Postcard|Film|Movie|Clip|Video|Trailer|Yearbook|Book|Chapter|Part|Program|Brochure|Newsletter|Ad|Advertisement|Letter|Deed|Check|Receipt|Note|Report|Photo|Photos|Gallery|Album|View|Views|Aerial|Plaque|Monument|Marker|Sign|Medal|Token|Coin|Cent|Quarter|Dollar|Award|Day|Days|Week|Year|Class|Team|Rally|Election|Results|History|Story|Stories|Diary|Journal|Index|Collection|Census|Survey|Edition|Issue|Interview)\b', re.I)
GROUP = re.compile(r'\b(Family|Families|Brothers|Sisters|Couple|Class of|Children|Kids|Students|Members|Team|Crew|Party|Club|Society|Grads?|Graduates|Nominees|Council-Elect|Wedding|Reunion|Picnic|Parade|Gallery|Collection|Album|Funeral of|Grave Markers|Headstone|Cemetery)\b|&| and |, [A-Z][a-z]+ [A-Z][a-z]+, [A-Z][a-z]+ [A-Z]', re.I)

def subject(title):
    t = title.replace('SCVHistory.com', '').replace('SCVNewsmaker.com', '')
    parts = [x.strip() for x in t.split('|') if x.strip()]
    s = parts[-1] if parts else ''
    if len(parts) >= 2 and re.match(r'^(Obituaries|People|Santa Clarita City Council|Walk of Western Stars)$', parts[-1]): s = parts[-2]
    if re.match(r'^(Obituaries|People|Santa Clarita City Council|Walk of Western Stars)$', parts[1] if len(parts) > 2 else '') or (len(parts) >= 3 and re.match(r'^[A-Z]{1,4}\d', parts[0])):
        s = ' | '.join(parts[2:]) if len(parts) > 2 else s
    s = re.sub(r'^(Newsmaker of the (Week|Year)( \d{4})?:|Biography of|Biography:|In Memoriam:|Remembering|Tribute to|Tribute:|Profile:|About|Obituary:|Video:|Mayor|Councilman|Councilwoman|Judge|Dr\.|Rev\.|Pvt\.|Sgt\.|Capt\.|Gen\.|State Sen\.|Sen\.|Brig\. Gen\.|Supervisor)\s*', '', s.strip(), flags=re.I)
    s = re.split(r',|:|;| \(| – | - | \| | Dies| Honored| Named| Crowned| Receives| Wins| Gets| at | in the | with | on | of [A-Z]|\. ', s)[0]
    return s.strip(' .\'"')

# Given names, from the obituaries' subjects and the person records: a portrait page's subject must begin with one.
GIVEN = {fold(p['title']).split()[0] for p in persons if fold(p['title']).split()}
for path, o in pages.items():
    if 'obituary' in path.lower() or '| Obituaries |' in o['title']:
        w = fold(re.sub(r'^.*Obituaries \|', '', o['title'])).split()
        if w: GIVEN.add(w[0])
GIVEN -= {'the', 'a', 'an', 'old', 'new', 'mr', 'mrs', 'dr', 'st', 'san', 'santa', 'los', 'el', 'la', 'del'}

rows = []
for path, o in pages.items():
    if '/files/' in path or (o.get('word_count') or 0) < 150: continue
    t = o['title']
    shape = next((n for n, f in SHAPES if f(t, path)), None)
    if not shape: continue
    s = subject(t)
    if not s or len(s.split()) < 2 or len(s.split()) > 7 or GROUP.search(s) or GROUP.search(t.split('|')[-1]): continue
    if shape == 'portrait' and (not s.split() or fold(s).split()[:1] and fold(s).split()[0] not in GIVEN or re.search(r"Stars? in|Co-?stars?|Starring| in ''| in '|Lobby|Poster|Still", t)): continue
    if shape == 'portrait' and (not re.match(r'^(SCVHistory\.com )?[A-Z]{1,4}\d', t) or NOTNAME.search(s) or not re.match(r"^[A-Z][a-zA-Z.'’\-]+( (de|del|la|van|von|y|[A-Z][a-zA-Z.'’\-]+)){1,4}$", s)): continue
    if shape == 'people' and re.search(r'\b(Lobby Card|Poster|Postcard|Map|Truck|Car|Automobile|Store|House|Home|Ranch|Building|Hotel|Station|Mine|Well|School|Church|Grave|Letter|Check|Receipt|Note|Deed|Advertisement|Ad|Sign|Plaque|Monument|Award|Ribbon|Badge|Button)\b', t): continue
    k = key(s)
    if not k: continue
    n, body = prose(path)
    hits = len(re.findall(r'\b' + re.escape(k[1]) + r'\b', fold(body)))
    if shape in ('portrait', 'people', 'biography', 'walk') and (n < 150 or hits < (3 if shape == 'portrait' else 2)): continue
    ids = sorted(index.get(k, set()))
    rows.append({'path': path, 'url': 'https://scvhistory.com' + path, 'title': re.sub(r'^SCVHistory\.com\s*', '', t).strip(' |'), 'shape': shape, 'subject': s, 'words': n, 'mentions': hits, 'person': ids[0] if len(ids) == 1 else None, 'ambiguous': ids if len(ids) > 1 else None})

matched = [r for r in rows if r['person']]
unmatched = [r for r in rows if not r['person']]
cover = collections.defaultdict(list)
for r in matched: cover[r['person']].append(r)

shapes = collections.Counter(r['shape'] for r in rows)
lines = ['# Person pages on the legacy site, 3 October 2026', '',
         'Generated by scripts/import/survey_legacy_person_pages.py (read only) from the legacy map and the Reggie mirror. A row is a lead: the title names one person as its subject in one of the shapes below. Pages about two people, a family or a group are left out, and so are photographs of things.', '',
         f'{len(rows)} person pages; {len(matched)} name a person who has a record in the archive ({len(cover)} people), {len(unmatched)} name someone who has none.', '',
         '## Shapes', '', '| shape | pages | matched to a record |', '|---|---|---|']
for sh, n in shapes.most_common():
    lines.append(f'| {sh} | {n} | {sum(1 for r in matched if r["shape"] == sh)} |')
lines += ['', '## The 108 with no biography on 2 October', '']
done_today = [i for i in empty108 if i in showing]
lines.append(f'{len(done_today)} of them have a profile since (3 October). Of the {len(empty108) - len(done_today)} still empty:')
lines.append('')
still = [i for i in empty108 if i not in showing]
covered = [i for i in still if i in cover]
lines.append(f'- {len(covered)} have at least one person page on the legacy site;')
lines.append(f'- {len(still) - len(covered)} have none.')
lines.append('')
for i in sorted(covered, key=lambda i: empty108[i]):
    lines.append(f'### {empty108[i]} (#{i})')
    for r in sorted(cover[i], key=lambda r: r['shape']): lines.append(f'- {r["shape"]}: [{r["title"]}]({r["url"]}) ({r["words"]} words of prose)')
    lines.append('')
lines += ['### Still empty, no person page found', '']
lines += [f'- {empty108[i]} (#{i})' for i in sorted(still, key=lambda i: empty108[i]) if i not in cover]
lines += ['', '### Done since 2 October, with the pages that would have served', '']
for i in sorted(done_today, key=lambda i: empty108[i]):
    lines.append(f'- {empty108[i]} (#{i}): ' + ('; '.join(f'{r["shape"]} {r["path"].split("/")[-1]}' for r in cover.get(i, [])) or 'none found'))
lines += ['', '## People with pages and a record that already shows a biography', '']
for i in sorted([i for i in cover if i not in empty108], key=lambda i: byid[i]['title']):
    lines.append(f'- {byid[i]["title"]} (#{i}): ' + '; '.join(f'{r["shape"]} {r["path"].split("/")[-1]}' for r in cover[i]))
lines += ['', '## Subjects with no record in the archive', '', 'By shape. The obituaries are mostly residents the archive has never recorded; the others are the likelier missing subjects.', '']
for sh, _ in shapes.most_common():
    us = sorted([r for r in unmatched if r['shape'] == sh], key=lambda r: r['subject'])
    if not us: continue
    lines.append(f'### {sh} ({len(us)})'); lines.append('')
    for r in us: lines.append(f'- {r["subject"]}: [{r["title"]}]({r["url"]})' + (f' (ambiguous: {r["ambiguous"]})' if r['ambiguous'] else ''))
    lines.append('')
open(f'{ROOT}/inventory/review/legacy-person-pages-2026-10-03.md', 'w').write('\n'.join(lines) + '\n')
json.dump({'rows': rows, 'covered108': covered, 'still108': still, 'done': done_today}, open(f'{ROOT}/inventory/review/legacy-person-pages-2026-10-03.json', 'w'), indent=1)
print(f'{len(rows)} person pages ({dict(shapes)}); matched {len(matched)} to {len(cover)} people; unmatched {len(unmatched)}')
print(f'108: done {len(done_today)}, still {len(still)}, of which covered {len(covered)}')
