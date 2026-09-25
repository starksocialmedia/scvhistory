#!/usr/bin/env python3
"""
Matches the archive's own occupation terms to Wikidata occupation items.

WHY FROM THE CORPUS AND NOT FROM WIKIDATA

Wikidata holds some thousands of occupations. Starting there and picking the
ones that look relevant would produce a vocabulary that describes an imagined
archive. Starting from the 56 terms the 66 person records actually use, plus
the 25 civic and military titles the honorific strip removes from names,
produces one that describes this one.

The titles matter as much as the occupations. A record's name must not carry
"Congressman", and until now the title survived only as an alias, which says
what he was called and never says what he did or when. A role does.

HOW A MATCH IS MADE

Each term is searched against Wikidata, and a candidate is kept only if it is
an instance of, or a subclass of, occupation (Q12737077) or position (Q4164871).
"Rancher" must come back as the occupation and not as a surname, an album or a
racehorse, and without that filter it does.

Where several candidates survive, the one whose label matches exactly wins, and
the rest are recorded so a person can see what was passed over.

Nothing is written to the database. This produces a table to read.

Run: python3 scripts/import/derive_roles_wikidata.py
"""

import json
import os
import sys
import time
import urllib.parse
import urllib.request

UA = 'scvhistory-archive/1.0 (https://scvhistory.com; archive research)'
API = 'https://www.wikidata.org/w/api.php'
SPARQL = 'https://query.wikidata.org/sparql'
ROOT = os.path.join(os.path.dirname(__file__), '..', '..')
TERMS = os.path.normpath(os.path.join(ROOT, 'review', 'role-terms.json'))
OUT = os.path.normpath(os.path.join(ROOT, 'inventory', 'legacy', 'authorities', 'roles-wikidata.json'))

# Hand-set where a plain search will not find the right sense. These are the
# ones a search gets wrong, checked by eye.
PINNED = {
    'Alcalde': 'Q3621491',
    'Colonial Governor': 'Q1250464',
    'Rancher': 'Q10841764',
    'Civic Leader': '',            # no occupation item; stays local
    'Father-Presidente': '',       # a post within the mission system; local
    'Rancho Administrator': '',    # local
    'Land Grantee': '',            # local
    'Political Staffer': '',       # local
    'Small Business Owner': '',    # local

    # Matched exactly by label and wrongly by sense. A label match is not a
    # meaning match, and these three would have entered the vocabulary looking
    # like the most confident rows in it.
    'Scout': 'Q1350189',           # frontier scout, not a member of the Scouting movement
    'City Council Member': 'Q708492',   # council member, not the Portuguese office
    'Assemblyman': 'Q13218630',    # member of a state assembly, not of Bronx County
    'Assemblywoman': 'Q13218630',
    'State Assemblymember': 'Q13218630',
    'Chief': '',                   # too many senses to pin; a person chooses

    # Nathan's rulings on the nearest matches, 21 September.
    'Lawman': 'Q384593',           # law enforcement officer, not a Scandinavian lawspeaker
    'Political Agent': '',         # not an election agent; local
    # Q1423891 is "Christian minister", which is not what a Franciscan
    # missionary was and is the sort of pin that looks checked because a human
    # typed it. Local.
    'Franciscan Missionary': '',
    'Catholic Priest': 'Q250867',
    'Hotelier': 'Q105756071',      # hotel owner: Nadeau owned rather than managed
    'Cattle Rustler': '',          # local
    'College Trustee': '',         # local
    'Freighter': '',               # local: a hauler of goods by wagon
    'Railroad Investor': '',       # local
    'Water Company Manager': '',   # local
    'President & CEO': '',         # local
    'SCVTV': '',                   # not a role at all; a split artefact, see below
}


def search(term, limit=6):
    q = urllib.parse.urlencode({
        'action': 'wbsearchentities', 'search': term, 'language': 'en',
        'type': 'item', 'limit': limit, 'format': 'json'})
    req = urllib.request.Request(API + '?' + q, headers={'User-Agent': UA})
    with urllib.request.urlopen(req, timeout=40) as r:
        return json.loads(r.read().decode('utf-8')).get('search', [])


def are_occupations(qids):
    """Which of these QIDs are an occupation or a position, by P31/P279*."""
    if not qids:
        return set()
    values = ' '.join('wd:' + q for q in qids)
    query = """
    SELECT DISTINCT ?item WHERE {
      VALUES ?item { %s }
      ?item wdt:P31/wdt:P279* ?cls .
      VALUES ?cls { wd:Q12737077 wd:Q4164871 wd:Q28640 wd:Q216353 }
    }""" % values
    data = urllib.parse.urlencode({'query': query}).encode()
    req = urllib.request.Request(SPARQL, data=data, headers={
        'Accept': 'application/sparql-results+json', 'User-Agent': UA,
        'Content-Type': 'application/x-www-form-urlencoded'})
    for attempt in range(3):
        try:
            with urllib.request.urlopen(req, timeout=90) as r:
                d = json.loads(r.read().decode('utf-8'))
            return {b['item']['value'].rsplit('/', 1)[-1] for b in d['results']['bindings']}
        except Exception:
            time.sleep(4 * (attempt + 1))
    return set()


def main():
    terms = json.load(open(TERMS, encoding='utf-8'))

    # "ranciscan Missionary" is a typo in one record, missing its F. Folded
    # here so the vocabulary does not carry the mistake, and reported so the
    # record can be corrected.
    TYPOS = {'ranciscan Missionary': 'Franciscan Missionary'}
    seen = {}
    fixed = []
    for t in terms:
        name = TYPOS.get(t['term'], t['term'])
        if name != t['term']:
            fixed.append(t['term'] + ' -> ' + name)
        if name in seen:
            seen[name]['count'] += t['count']
            continue
        seen[name] = {**t, 'term': name}
    terms = list(seen.values())
    if fixed:
        print('typos folded: ' + '; '.join(fixed))

    rows = []

    for i, t in enumerate(terms):
        term = t['term']
        if term in PINNED and PINNED[term] == '':
            rows.append({**t, 'qid': '', 'label': '', 'description': '',
                         'confidence': 'local', 'alternates': [],
                         'why': 'no Wikidata occupation covers this; kept as a local term'})
            continue

        if term in PINNED:
            qid = PINNED[term]
            hits = search(term, 3)
            lab = next((h['label'] for h in hits if h['id'] == qid), term)
            desc = next((h.get('description', '') for h in hits if h['id'] == qid), '')
            rows.append({**t, 'qid': qid, 'label': lab, 'description': desc,
                         'confidence': 'pinned', 'alternates': [],
                         'why': 'set by hand; a plain search finds the wrong sense'})
            time.sleep(0.4)
            continue

        # Accepted by hand where the nearest match was judged right, so the
        # table records a decision rather than a guess that happened to stand.
        ACCEPTED = {'Museum Curator': 'Q674426', 'Congressman': 'Q18002923',
                    'Congresswoman': 'Q18002923',
                    'Financial Advisor': 'Q683476', 'Ship Owner': 'Q500251',
                    'Councilman': 'Q708492', 'Councilwoman': 'Q708492'}
        if term in ACCEPTED:
            hits = search(term, 4)
            lab = next((h['label'] for h in hits if h['id'] == ACCEPTED[term]), term)
            desc = next((h.get('description', '') for h in hits if h['id'] == ACCEPTED[term]), '')
            rows.append({**t, 'qid': ACCEPTED[term], 'label': lab, 'description': desc,
                         'confidence': 'accepted', 'alternates': [],
                         'why': 'the nearest match, accepted by hand'})
            time.sleep(0.4)
            continue

        hits = search(term)
        ok = are_occupations([h['id'] for h in hits])
        keep = [h for h in hits if h['id'] in ok]

        if not keep:
            rows.append({**t, 'qid': '', 'label': '', 'description': '',
                         'confidence': 'none', 'alternates': [],
                         'why': 'no candidate is an occupation or a position'})
        else:
            exact = [h for h in keep if h['label'].lower() == term.lower()]
            best = (exact or keep)[0]
            rows.append({**t, 'qid': best['id'], 'label': best['label'],
                         'description': best.get('description', ''),
                         'confidence': 'exact' if exact else 'nearest',
                         'alternates': [{'qid': h['id'], 'label': h['label'],
                                         'description': h.get('description', '')}
                                        for h in keep if h['id'] != best['id']][:3],
                         'why': 'label matches exactly' if exact
                                else 'the closest candidate that is an occupation'})
        time.sleep(0.4)
        if (i + 1) % 20 == 0:
            sys.stderr.write('  %d/%d\r' % (i + 1, len(terms)))

    out = {
        'provenance': {
            'source': 'Wikidata, matched against the occupation terms this archive uses',
            'url': API,
            'downloaded': __import__('datetime').date.today().isoformat(),
            'derived_by': 'scripts/import/derive_roles_wikidata.py',
            'terms': len(rows),
            'note': ('Built from the corpus outward: the occupation values on the person '
                     'records plus the civic and military titles the name policy strips. A '
                     'candidate is kept only if it is an occupation or a position by P31/P279*, '
                     'because a plain search for Rancher returns a surname and an album. Terms '
                     'marked local have no Wikidata equivalent and are the archive\'s own.'),
        },
        'roles': rows,
    }
    os.makedirs(os.path.dirname(OUT), exist_ok=True)
    json.dump(out, open(OUT, 'w', encoding='utf-8'), indent=1, ensure_ascii=False)

    by = {}
    for r in rows:
        by[r['confidence']] = by.get(r['confidence'], 0) + 1
    print('terms: %d' % len(rows))
    for k, v in sorted(by.items(), key=lambda x: -x[1]):
        print('  %-10s %d' % (k, v))
    print('wrote %s' % OUT)
    return 0


if __name__ == '__main__':
    sys.exit(main())
