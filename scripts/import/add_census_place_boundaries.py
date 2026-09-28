"""
Census place boundaries for four communities that had only a centre point:
Fillmore (an incorporated city), Piru, Frazier Park and Lebec (Census
Designated Places).

Nathan, 28 September 2026: these are real boundaries, public domain, and worth
having. The canyons stay centre points; a watershed is not a community.

SOURCE. U.S. Census Bureau, TIGERweb, Census 2020 layers (the same vintage as the
ZCTA shapes already in the file): layer 25, Incorporated Places, and layer 26,
Census Designated Places. Queried by GEOID, which is unique, with the same
generalisation as every other shape in the file (maxAllowableOffset 0.0002,
geometryPrecision 5). Public domain, a work of the U.S. federal government.

WHAT IT WRITES, both generated files, the way the existing shapes were done:
  web/data/communities.geojson           the four features, method "census-place",
                                         plus a sources entry in metadata
  templates/_data/community-neighbors.json  method, methodNote, withBoundary and
                                         neighbours for them
Neighbours use the file's own rule: a boundary vertex of one within 250 m of a
boundary segment of the other. Only pairs involving the four are computed, and
added both ways; no existing pair is changed.

Rerunnable: the four are replaced, nothing else is touched. Runs on the host
(it fetches from census.gov):
    python3 scripts/import/add_census_place_boundaries.py
"""
import json
import math
import os
import sys
import urllib.parse
import urllib.request
from datetime import date

ROOT = os.path.join(os.path.dirname(__file__), '..', '..')
GEOJSON = os.path.join(ROOT, 'web', 'data', 'communities.geojson')
NEIGH = os.path.join(ROOT, 'templates', '_data', 'community-neighbors.json')
BASE = 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb/Places_CouSub_ConCity_SubMCD/MapServer'

# slug -> (layer, GEOID, kind)
PLACES = {
    'fillmore':     (25, '0624092', 'Incorporated city'),
    'piru':         (26, '0657372', 'Census Designated Place'),
    'frazier-park': (26, '0625534', 'Census Designated Place'),
    'lebec':        (26, '0640956', 'Census Designated Place'),
}
QUERY = {'outSR': '4326', 'maxAllowableOffset': '0.0002', 'geometryPrecision': '5', 'f': 'geojson'}


def fetch(layer, geoid):
    q = dict(QUERY, where=f"GEOID='{geoid}'", outFields='NAME,BASENAME,GEOID,LSADC,FUNCSTAT')
    url = f'{BASE}/{layer}/query?' + urllib.parse.urlencode(q)
    req = urllib.request.Request(url, headers={'User-Agent': 'SCVHistory-Archive-Boundaries/1.0 (Claude Code, for Nathan Imhoff)'})
    with urllib.request.urlopen(req, timeout=60) as r:
        g = json.load(r)
    feats = g.get('features') or []
    if len(feats) != 1:
        sys.exit(f'GEOID {geoid} on layer {layer}: expected one feature, got {len(feats)} {g.get("error", "")}')
    return feats[0]


def rings(geom):
    c = geom['coordinates']
    return c if geom['type'] == 'Polygon' else [r for p in c for r in p]


def metres(a, b):
    lat = math.radians((a[1] + b[1]) / 2)
    return math.hypot((a[0] - b[0]) * 111320 * math.cos(lat), (a[1] - b[1]) * 110540)


def point_seg(p, a, b):
    lat = math.radians(p[1])
    kx, ky = 111320 * math.cos(lat), 110540
    px, py = p[0] * kx, p[1] * ky
    ax, ay, bx, by = a[0] * kx, a[1] * ky, b[0] * kx, b[1] * ky
    dx, dy = bx - ax, by - ay
    t = 0 if dx == dy == 0 else max(0, min(1, ((px - ax) * dx + (py - ay) * dy) / (dx * dx + dy * dy)))
    return math.hypot(px - (ax + t * dx), py - (ay + t * dy))


def near(g1, g2, limit=250):
    segs = [(r[i], r[i + 1]) for r in rings(g2) for i in range(len(r) - 1)]
    for r in rings(g1):
        for p in r:
            for a, b in segs:
                if point_seg(p, a, b) <= limit:
                    return True
    return False


def main():
    geo = json.load(open(GEOJSON))
    nb = json.load(open(NEIGH))
    keep = [f for f in geo['features'] if f['properties'].get('slug') not in PLACES]
    added = []
    for slug, (layer, geoid, kind) in PLACES.items():
        f = fetch(layer, geoid)
        p = f['properties']
        added.append({'type': 'Feature', 'geometry': f['geometry'], 'properties': {
            'slug': slug, 'name': p['BASENAME'], 'source_name': p['NAME'], 'city_type': kind,
            'method': 'census-place', 'geoid': geoid, 'neighbors': []}})
        print(f'{slug:14} {p["NAME"]:22} GEOID {geoid}  {sum(len(r) for r in rings(f["geometry"]))} vertices')

    feats = keep + added
    by = {f['properties']['slug']: f for f in feats}
    new = set(PLACES)
    for f in feats:
        f['properties'].setdefault('neighbors', [])
        f['properties']['neighbors'] = [n for n in f['properties']['neighbors'] if n not in new]
    for s in new:
        for t, g in by.items():
            if t == s:
                continue
            if near(by[s]['geometry'], g['geometry']) or near(g['geometry'], by[s]['geometry']):
                by[s]['properties']['neighbors'].append(t)
                if s not in by[t]['properties']['neighbors']:
                    by[t]['properties']['neighbors'].append(s)
    for f in feats:
        f['properties']['neighbors'] = sorted(set(f['properties']['neighbors']))
    geo['features'] = sorted(feats, key=lambda f: f['properties']['slug'])

    m = geo['metadata']
    m['description'] = m['description'].rstrip('.') + ', "census-place" for a 2020 Census incorporated place or Census Designated Place.'
    m['sources'] = [s for s in m['sources'] if s.get('method') != 'census-place'] + [{
        'method': 'census-place',
        'name': 'U.S. Census Bureau, TIGERweb, Census 2020 Incorporated Places (layer 25) and Census Designated Places (layer 26)',
        'service': BASE, 'attribution': 'U.S. Census Bureau',
        'license': 'Public domain, a work of the U.S. federal government.',
        'query': "GEOID='...', outSR=4326, maxAllowableOffset=0.0002, geometryPrecision=5",
        'retrieved': date.today().isoformat(),
        'geoids': {s: v[1] for s, v in PLACES.items()},
    }]
    json.dump(geo, open(GEOJSON, 'w'), ensure_ascii=False, separators=(',', ':'))

    for s, (layer, geoid, kind) in PLACES.items():
        nb['method'][s] = 'census-place'
        nb['methodNote'][s] = ('Boundary from the 2020 Census incorporated place (city limits).' if layer == 25
                               else 'Boundary from the 2020 Census Designated Place, a statistical area the Census Bureau draws for an unincorporated community.')
    nb['neighbors'] = {f['properties']['slug']: f['properties']['neighbors'] for f in geo['features']}
    nb['withBoundary'] = sorted(by)
    nb['method'] = dict(sorted(nb['method'].items()))
    nb['methodNote'] = dict(sorted(nb['methodNote'].items()))
    json.dump(nb, open(NEIGH, 'w'), ensure_ascii=False, indent=1)
    print(f'{len(geo["features"])} features; neighbours of the four: ' +
          json.dumps({s: by[s]['properties']['neighbors'] for s in PLACES}))


if __name__ == '__main__':
    main()
