#!/usr/bin/env python3
"""
Derives inventory/legacy/authorities/orgs-wikidata.json: organizations Wikidata
places in or around the Santa Clarita Valley.

WHAT THIS IS FOR

Wikidata is the only one of the three authorities that carries the historical
organizations. CDE's directory starts in the 1980s and the IRS file holds only
bodies that were granted exemption and kept it, so between them they miss the
Southern Pacific, the Newhall Land and Farming Company as it was in 1883, and
every mission and rancho. Wikidata has those, with an inception date and a
sitelink, and a QID that links the archive to everything else that cites it.

HOW IT SELECTS

Three ways, unioned, because no single property catches a historical body:

  P131  located in the administrative territorial entity, which catches
        anything filed under Santa Clarita or one of its communities
  P159  headquarters location, which catches a company whose offices were here
        even where nobody has filed it under P131
  P276  location, a looser property that catches the ones filed neither way

Each is followed one hop up through P131 so an organization in Newhall is found
by way of Newhall being in Santa Clarita.

The result is filtered to things that are organizations: instance of
organization, business, company, school, government agency, religious
organization or a subclass of any of them. Without that filter the query
returns every building, road and creek in the valley.

WHAT IS KEPT

QID, label, description, inception, dissolution, the organization's type QIDs
and labels, and any identifier Wikidata already holds that the archive uses:
the EIN, the CDS code, the NCES id and the GNIS id. Where Wikidata has an
identifier the archive is about to look up elsewhere, having both is how the
two tables get joined.

USAGE

    python3 scripts/import/derive_orgs_wikidata.py
"""

import datetime
import json
import os
import sys
import time
import urllib.parse
import urllib.request

ENDPOINT = 'https://query.wikidata.org/sparql'
UA = 'scvhistory-archive/1.0 (https://scvhistory.com; archive research)'
OUT = os.path.join(os.path.dirname(__file__), '..', '..',
                   'inventory', 'legacy', 'authorities', 'orgs-wikidata.json')

# The valley and its communities, looked up with wbsearchentities and checked
# by hand. Santa Clarita is the city; the rest are districts and CDPs that an
# organization may be filed under instead.
PLACES = {
    'Q491132': 'Santa Clarita',
    'Q7018086': 'Newhall',
    'Q3242084': 'Castaic',
    'Q2308667': 'Acton',
    'Q2268355': 'Val Verde',
}

QUERY = """
SELECT DISTINCT ?item ?itemLabel ?itemDescription ?inception ?dissolved
                ?typeLabel ?ein ?cds ?nces ?gnis ?article
WHERE {
  VALUES ?place { %s }
  {
    ?item wdt:P131 ?place .
  } UNION {
    ?item wdt:P131/wdt:P131 ?place .
  } UNION {
    ?item wdt:P159 ?place .
  } UNION {
    ?item wdt:P159/wdt:P131 ?place .
  } UNION {
    ?item wdt:P276 ?place .
  }

  ?item wdt:P31 ?type .
  ?type wdt:P279* ?org .
  VALUES ?org { wd:Q43229 wd:Q4830453 wd:Q783794 wd:Q3918 wd:Q3914
                wd:Q327333 wd:Q1530022 wd:Q11032 wd:Q163740 wd:Q7075 }

  OPTIONAL { ?item wdt:P571 ?inception }
  OPTIONAL { ?item wdt:P576 ?dissolved }
  OPTIONAL { ?item wdt:P1297 ?ein }
  OPTIONAL { ?item wdt:P2183 ?cds }
  OPTIONAL { ?item wdt:P2696 ?nces }
  OPTIONAL { ?item wdt:P590 ?gnis }
  OPTIONAL { ?article schema:about ?item ; schema:isPartOf <https://en.wikipedia.org/> }

  SERVICE wikibase:label { bd:serviceParam wikibase:language "en" }
}
"""


def run(query: str, tries: int = 3) -> dict:
    data = urllib.parse.urlencode({'query': query}).encode()
    req = urllib.request.Request(
        ENDPOINT, data=data,
        headers={'Accept': 'application/sparql-results+json',
                 'User-Agent': UA,
                 'Content-Type': 'application/x-www-form-urlencoded'})
    last = None
    for i in range(tries):
        try:
            with urllib.request.urlopen(req, timeout=180) as r:
                return json.loads(r.read().decode('utf-8'))
        except Exception as e:                      # noqa: BLE001
            last = e
            # The public endpoint rate limits and times out under load; a
            # failure here is usually worth one more try rather than a report
            # that the valley has no organizations.
            time.sleep(4 * (i + 1))
    raise SystemExit('wikidata query failed after %d tries: %s' % (tries, last))


NAME_QUERY = """
SELECT DISTINCT ?item ?itemLabel ?itemDescription ?inception ?dissolved
                ?typeLabel ?ein ?cds ?nces ?gnis ?article
WHERE {
  VALUES ?name { %s }
  { ?item rdfs:label ?name } UNION { ?item skos:altLabel ?name }

  ?item wdt:P31 ?type .
  ?type wdt:P279* ?org .
  VALUES ?org { wd:Q43229 wd:Q4830453 wd:Q783794 wd:Q3918 wd:Q3914
                wd:Q327333 wd:Q1530022 wd:Q11032 wd:Q163740 wd:Q7075 }

  OPTIONAL { ?item wdt:P571 ?inception }
  OPTIONAL { ?item wdt:P576 ?dissolved }
  OPTIONAL { ?item wdt:P1297 ?ein }
  OPTIONAL { ?item wdt:P2183 ?cds }
  OPTIONAL { ?item wdt:P2696 ?nces }
  OPTIONAL { ?item wdt:P590 ?gnis }
  OPTIONAL { ?article schema:about ?item ; schema:isPartOf <https://en.wikipedia.org/> }

  SERVICE wikibase:label { bd:serviceParam wikibase:language "en" }
}
"""

QUEUE = os.path.join(os.path.dirname(__file__), '..', '..',
                     'web', 'review', 'records-full.json')

NAME_CAP = 400


def corpus_org_names() -> list:
    """The organization names the corpus actually uses.

    Geography alone misses the ones that matter most. The Southern Pacific ran
    through the valley for a century and is filed under no Santa Clarita
    property; Newhall Land and Farming owned most of it and is headquartered
    nowhere Wikidata records as here. Those are found by name or not at all,
    which is why the brief asked for a text match alongside the place query.
    """
    if not os.path.exists(QUEUE):
        return []
    d = json.load(open(QUEUE, encoding='utf-8'))
    names = [r['name'] for r in d.get('names', [])
             if r.get('guess') == 'organization' and r.get('articleCount', 0) >= 2]
    return names[:NAME_CAP]


def collect(bindings: list, rows: dict, how: str) -> None:
    for b in bindings:
        qid = b['item']['value'].rsplit('/', 1)[-1]
        r = rows.setdefault(qid, {
            'qid': qid,
            'name': b.get('itemLabel', {}).get('value', ''),
            'description': b.get('itemDescription', {}).get('value', ''),
            'inception': (b.get('inception', {}).get('value', '') or '')[:10],
            'dissolved': (b.get('dissolved', {}).get('value', '') or '')[:10],
            'types': [],
            'ein': b.get('ein', {}).get('value', ''),
            'cdsCode': b.get('cds', {}).get('value', ''),
            'ncesId': b.get('nces', {}).get('value', ''),
            'gnisId': b.get('gnis', {}).get('value', ''),
            'wikipedia': b.get('article', {}).get('value', ''),
            'foundBy': [],
        })
        if how not in r['foundBy']:
            r['foundBy'].append(how)
        t = b.get('typeLabel', {}).get('value', '')
        if t and t not in r['types']:
            r['types'].append(t)


def main() -> int:
    rows = {}

    values = ' '.join('wd:' + q for q in PLACES)
    d = run(QUERY % values)
    collect(d['results']['bindings'], rows, 'place')
    by_place = len(rows)
    print('by place: %d' % by_place)

    names = corpus_org_names()
    if names:
        # Chunked: a VALUES clause with four hundred literals times the public
        # endpoint out, and a timeout here would silently cost the whole pass.
        for i in range(0, len(names), 60):
            chunk = names[i:i + 60]
            lits = ' '.join('"%s"@en' % n.replace('\\', '').replace('"', '')
                            for n in chunk)
            try:
                dn = run(NAME_QUERY % lits, tries=2)
                collect(dn['results']['bindings'], rows, 'name')
            except SystemExit as e:
                print('  name chunk %d failed, continuing: %s' % (i // 60, e))
            time.sleep(1)
        print('after matching %d corpus names: %d' % (len(names), len(rows)))

    out = {
        'provenance': {
            'source': 'Wikidata, via the public SPARQL endpoint',
            'url': ENDPOINT,
            'downloaded': datetime.date.today().isoformat(),
            'derived_by': 'scripts/import/derive_orgs_wikidata.py',
            'places': PLACES,
            'properties': {
                'P131': 'located in the administrative territorial entity',
                'P159': 'headquarters location',
                'P276': 'location',
                'P571': 'inception', 'P576': 'dissolved',
                'P1297': 'EIN', 'P2183': 'CDS code', 'P2696': 'NCES id', 'P590': 'GNIS id',
            },
            'organizations': len(rows),
            'found_by_place': by_place,
            'corpus_names_matched': len(names),
            'name_cap': NAME_CAP,
            'note': (
                'The only one of the three authorities that carries historical bodies. CDE '
                'begins in the 1980s and the IRS file holds only organizations granted '
                'exemption, so the missions, the ranchos and the railroad are here or '
                'nowhere. Selection is P131, P159 and P276, each followed one hop up through '
                'P131, then filtered to organizations and their subclasses. Coverage is '
                'whatever volunteers have entered: an absence means nobody has written it '
                'down, not that it did not exist.'),
        },
        'organizations': sorted(rows.values(), key=lambda r: r['name']),
    }

    path = os.path.normpath(OUT)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(out, f, separators=(',', ':'), sort_keys=True)

    print('organizations: %d' % len(rows))
    print('wrote %s  %.2f MB' % (path, os.path.getsize(path) / 1e6))
    withid = sum(1 for r in rows.values() if r['ein'] or r['cdsCode'] or r['ncesId'])
    print('carrying an EIN, CDS or NCES id: %d' % withid)
    print('with an inception date: %d' % sum(1 for r in rows.values() if r['inception']))
    print()
    for r in sorted(rows.values(), key=lambda r: r['name'])[:25]:
        print('   %-10s %-44s %s' % (r['qid'], r['name'][:43], (r['types'] or [''])[0][:26]))
    return 0


if __name__ == '__main__':
    sys.exit(main())
