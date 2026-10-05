#!/usr/bin/env python3
"""
Governance table for /civic: for eight places in the valley, which bodies govern
that spot, which district or area of each, and how the seat is filled.

Claude (research subagent), 4 October 2026, for Nathan ("Rows as places, columns
as the bodies governing that spot, each marked elected or appointed").
Pure Python 3 standard library. Reads saved files only; no network, no database.

POINTS (one per row)
  USGS Geographic Names Information System, DomesticNames_CA (saved zip), by
  feature id. Valencia is the exception: the GNIS "Valencia" populated-place
  point (1661608) lies outside the City, west of Interstate 5, so the row uses
  the Census Bureau 2020 internal point of ZCTA 91355 (Valencia), which the
  archive already uses for the Valencia community outline.

LAYERS (point in polygon, even-odd over every ring)
  web/data/seats/*.geojson                     City council districts, school
                                               trustee areas, SCV Water divisions
                                               (LA County RR/CC layers)
  lacounty/rrcc-division-boundaries-acton-agua-dulce-usd.geojson
  trustee-areas-2026-10-04/santa-clarita-valley-water/scvwa-service-boundary.geojson
  lacounty/supervisorial-districts-2021.json   LA County Supervisorial District (2021)
  web/data/valley-districts/{assembly-2021,senate-2021,house-2021,house-2025}.geojson
  tigerweb/pl_2020_*.json                      Census 2020 place polygons (check only)

OUTPUT
  templates/_data/governance-table.json

Run on the host:
    python3 scripts/import/build_governance_table.py
"""
import csv
import io
import json
import os
import zipfile

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
LD = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')
SEATS = os.path.join(ROOT, 'web', 'data', 'seats')
VD = os.path.join(ROOT, 'web', 'data', 'valley-districts')
OUT = os.path.join(ROOT, 'templates', '_data', 'governance-table.json')

GNIS_ZIP = os.path.join(LD, 'gnis', 'DomesticNames_CA_Text.zip')
ZCTA = os.path.join(LD, 'tigerweb', 'zcta_2020_internal_points_scv.json')

# (place, GNIS feature id or ('zcta', code))
ROWS = [
    ('Valencia', ('zcta', '91355')),
    ('Newhall', 272642),
    ('Canyon Country', 1946238),
    ('Sand Canyon', 273524),
    ('Stevenson Ranch', 2583151),
    ('Castaic', 270335),
    ('Val Verde', 1661607),
    ('Agua Dulce', 1660235),
]

ROW_NOTES = {
    'Valencia': ('The ZCTA 91355 internal point falls in southern Valencia, in the Newhall School District. Much of '
                 'Valencia is in the Saugus Union School District instead (the internal point of ZCTA 91354, northern '
                 'Valencia, tests in Saugus Union Trustee Area 2); the point stands for one spot only.'),
    'Sand Canyon': ('GNIS records Sand Canyon as a valley, not a populated place. Its point lies inside the City '
                    '(Council District 5). Much of the Sand Canyon community is unincorporated: the County\'s "Sand Canyon" '
                    'statistical area is the unincorporated part, and this point is not in it.'),
    'Agua Dulce': ('Outside SCV Water: neither in its divisions nor in its service area. Its kindergarten to grade 12 '
                   'schools are the Acton-Agua Dulce Unified School District, which takes the place of both school columns.'),
}

ELEMENTARY = [
    ('newhall-school-district', 'Newhall School District'),
    ('saugus-union-school-district', 'Saugus Union School District'),
    ('sulphur-springs-union-school-district', 'Sulphur Springs Union School District'),
    ('castaic-union-school-district', 'Castaic Union School District'),
]

AT_LARGE_NOTE = ('No member for this district until December 2026: Ordinance 23-4 first elects Districts 2, 4 and 5 in '
                 'November 2026. Until then the three members elected at large in 2022 (Laurene Weste, Marsha McLean, '
                 'Bill Miranda) sit for the whole City.')
BY_DISTRICT_NOTE = ('Elected by district in November 2024; the three members elected at large in 2022 also sit for the '
                    'whole City until December 2026.')


def pip(x, y, rings):
    inside = False
    for r in rings:
        j = len(r) - 1
        for i in range(len(r)):
            xi, yi = r[i][0], r[i][1]
            xj, yj = r[j][0], r[j][1]
            if (yi > y) != (yj > y) and x < (xj - xi) * (y - yi) / (yj - yi) + xi:
                inside = not inside
            j = i
    return inside


def in_geojson(geom, x, y):
    if not geom:
        return False
    polys = [geom['coordinates']] if geom['type'] == 'Polygon' else geom['coordinates']
    return any(pip(x, y, p) for p in polys)


def hits(path, x, y, role='seat'):
    d = json.load(open(path))
    return [f['properties'] for f in d['features']
            if f['properties'].get('role', 'seat') == role and in_geojson(f['geometry'], x, y)]


def gnis_points():
    want = {fid for _, fid in ROWS if isinstance(fid, int)}
    out = {}
    with zipfile.ZipFile(GNIS_ZIP) as z:
        name = [n for n in z.namelist() if n.endswith('.txt')][0]
        for row in csv.DictReader(io.TextIOWrapper(z.open(name), encoding='utf-8-sig'), delimiter='|'):
            fid = int(row['feature_id'])
            if fid in want:
                out[fid] = row
    return out


def main():
    G = gnis_points()
    Z = {f['attributes']['ZCTA5']: f['attributes'] for f in json.load(open(ZCTA))['features']}
    sup = json.load(open(os.path.join(LD, 'lacounty', 'supervisorial-districts-2021.json')))['features']
    aad = json.load(open(os.path.join(LD, 'lacounty', 'rrcc-division-boundaries-acton-agua-dulce-usd.geojson')))['features']
    scvw_area = json.load(open(os.path.join(ROOT, 'inventory', 'sources', 'trustee-areas-2026-10-04',
                                            'santa-clarita-valley-water', 'scvwa-service-boundary.geojson')))['features']
    census_places = {}
    for fn in sorted(os.listdir(os.path.join(LD, 'tigerweb'))):
        if fn.startswith('pl_2020_') and fn.endswith('.json'):
            f = json.load(open(os.path.join(LD, 'tigerweb', fn)))['features'][0]
            census_places[f['attributes']['NAME']] = f['geometry']['rings']

    rows = []
    for place, key in ROWS:
        if isinstance(key, tuple):
            a = Z[key[1]]
            lat, lng = float(a['INTPTLAT']), float(a['INTPTLON'])
            src = (f'Census Bureau, 2020 ZIP Code Tabulation Area {key[1]} internal point (TIGERweb). The GNIS '
                   f'"Valencia" populated-place point (feature 1661608, 34.4436063, -118.6095321) lies outside the City, '
                   f'west of Interstate 5, so it is not used.')
        else:
            g = G[key]
            lat, lng = float(g['prim_lat_dec']), float(g['prim_long_dec'])
            src = f'USGS GNIS feature {key}, "{g["feature_name"]}" ({g["feature_class"]}), primary point'
        x, y = lng, lat
        cells = {}

        s = [f['attributes']['LABEL'] for f in sup if pip(x, y, f['geometry']['rings'])]
        assert len(s) == 1, (place, s)
        cells['supervisor'] = {'label': s[0], 'filled': 'elected by district',
                               'bodySlug': 'los-angeles-county-board-of-supervisors'}

        c = hits(os.path.join(SEATS, 'city-of-santa-clarita.geojson'), x, y)
        assert len(c) <= 1
        in_city = bool(c)
        census_city = pip(x, y, census_places['Santa Clarita city'])
        if in_city:
            n = c[0]['label']
            if n in ('District 1', 'District 3'):
                cells['cityCouncil'] = {'label': n, 'filled': 'elected by district', 'seatSlug': c[0]['seat'],
                                        'bodySlug': 'city-of-santa-clarita', 'note': BY_DISTRICT_NOTE}
                if n == 'District 3':
                    cells['cityCouncil']['note'] = ('Jason Gibbs, sole nominee in 2024, appointed in lieu of election '
                                                    '(Elections Code 10229), serving as if elected. ' + BY_DISTRICT_NOTE)
            else:
                cells['cityCouncil'] = {'label': n, 'filled': 'elected at large until December 2026, then by district',
                                        'seatSlug': c[0]['seat'], 'bodySlug': 'city-of-santa-clarita',
                                        'note': AT_LARGE_NOTE}
        else:
            cells['cityCouncil'] = {'label': 'not in the City', 'filled': None}

        el = []
        for slug, name in ELEMENTARY:
            for pr in hits(os.path.join(SEATS, slug + '.geojson'), x, y):
                el.append({'label': pr['label'], 'filled': 'elected by trustee area', 'seatSlug': pr['seat'],
                           'bodySlug': slug, 'body': name})
        for f in aad:
            if in_geojson(f['geometry'], x, y):
                ta = f['properties']['DivisionName'].rsplit('TA', 1)[1]
                el.append({'label': f'Trustee Area {ta}', 'filled': 'elected by trustee area', 'bodySlug': None,
                           'body': 'Acton-Agua Dulce Unified School District',
                           'note': ('A unified district (kindergarten to grade 12), so it also takes the place of the '
                                    'Hart district here. No record in the archive. Trustee area from the County '
                                    'Registrar-Recorder\'s Division_Boundaries layer; NEEDS_VERIFICATION that every '
                                    'seat is now elected by trustee area.')})
        assert len(el) == 1, (place, el)
        cells['elementary'] = el[0]

        h = hits(os.path.join(SEATS, 'william-s-hart-union-high-school-district.geojson'), x, y)
        if h:
            cells['hart'] = {'label': h[0]['label'], 'filled': 'elected by trustee area', 'seatSlug': h[0]['seat'],
                             'bodySlug': 'william-s-hart-union-high-school-district'}
        else:
            cells['hart'] = {'label': 'not in the district', 'filled': None,
                             'note': 'High school is in the Acton-Agua Dulce Unified School District.'}

        w = hits(os.path.join(SEATS, 'santa-clarita-valley-water.geojson'), x, y)
        in_area = any(in_geojson(f['geometry'], x, y) for f in scvw_area)
        if w:
            cells['water'] = {'label': w[0]['label'], 'filled': 'elected by division', 'seatSlug': w[0]['seat'],
                              'bodySlug': 'santa-clarita-valley-water'}
        else:
            cells['water'] = {'label': 'not in the agency', 'filled': None,
                              'note': ('Outside all three SCV Water divisions and '
                                       + ('inside' if in_area else 'outside') + ' the agency\'s own service-area polygon.')}

        for key2, fn, word in (('assembly', 'assembly-2021', 'Assembly'), ('senate', 'senate-2021', 'Senate'),
                               ('houseNow', 'house-2021', 'Congressional'), ('house2027', 'house-2025', 'Congressional')):
            d = hits(os.path.join(VD, fn + '.geojson'), x, y)
            assert len(d) == 1, (place, fn, d)
            cells[key2] = {'label': d[0]['label'], 'district': d[0]['district'], 'filled': 'elected by district',
                           'bodySlug': {'assembly': 'california-state-assembly', 'senate': 'california-state-senate'}.get(
                               key2, 'united-states-house-of-representatives')}

        cdp = [n for n, r in census_places.items() if n != 'Santa Clarita city' and pip(x, y, r)]
        rows.append({
            'place': place, 'inCity': in_city,
            'point': {'lat': round(lat, 7), 'lng': round(lng, 7), 'source': src},
            'checks': {'inCensus2020City': census_city, 'census2020Cdp': cdp[0] if cdp else None},
            'note': ROW_NOTES.get(place),
            'cells': cells,
        })

    data = {
        '_about': ('Who governs each spot in the valley: for eight places, the body, the district or area, and how the '
                   'seat is filled. Built by scripts/import/build_governance_table.py on 4 October 2026 (Claude, research '
                   'subagent) for Nathan. Report: inventory/review/governance-table-2026-10-04.md.'),
        'method': ('One representative point per place (USGS Geographic Names Information System; for Valencia the Census '
                   'Bureau 2020 internal point of ZCTA 91355, because the GNIS point lies outside the City). Each cell is '
                   'a point-in-polygon test of that point against the saved layers: LA County Registrar-Recorder/County '
                   'Clerk City council districts, school trustee areas and SCV Water divisions (web/data/seats), the '
                   'Registrar\'s Acton-Agua Dulce Unified trustee areas, LA County Supervisorial District (2021) from the '
                   'County\'s Political_Boundaries map service, and the valley district maps for the 2021 Assembly, '
                   'Senate and House plans and the 2025 congressional map (web/data/valley-districts). A point is one '
                   'spot: a community that straddles a line has neighbours in another district. inCity comes from the '
                   'council-district layer and agrees with the Census 2020 place polygon for every row.'),
        'asOf': '2026-10-04',
        'sources': [
            {'what': 'USGS GNIS DomesticNames, California', 'url': 'https://prd-tnm.s3.amazonaws.com/StagedProducts/GeographicNames/DomesticNames/DomesticNames_CA_Text.zip',
             'saved': 'inventory/sources/legislative-districts-2026-10-04/gnis/DomesticNames_CA_Text.zip'},
            {'what': 'Census Bureau TIGERweb, 2020 ZCTA internal points', 'url': 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb/PUMA_TAD_TAZ_UGA_ZCTA/MapServer/7',
             'saved': 'inventory/sources/legislative-districts-2026-10-04/tigerweb/zcta_2020_internal_points_scv.json'},
            {'what': 'LA County, Supervisorial District (2021), adopted 15 December 2021', 'url': 'https://public.gis.lacounty.gov/public/rest/services/LACounty_Dynamic/Political_Boundaries/MapServer/26',
             'saved': 'inventory/sources/legislative-districts-2026-10-04/lacounty/supervisorial-districts-2021.json'},
            {'what': 'LA County Registrar-Recorder/County Clerk, Division_Boundaries (Acton-Agua Dulce Unified trustee areas)', 'url': 'https://services.arcgis.com/RmCCgQtiZLDCtblq/arcgis/rest/services/Division_Boundaries/FeatureServer/0',
             'saved': 'inventory/sources/legislative-districts-2026-10-04/lacounty/rrcc-division-boundaries-acton-agua-dulce-usd.geojson'},
            {'what': 'City council districts, school trustee areas, SCV Water divisions (RR/CC)', 'saved': 'web/data/seats/*.geojson'},
            {'what': 'Valley district maps (2021 plans, 2025 congressional map)', 'saved': 'web/data/valley-districts/*.geojson'},
            {'what': 'SCV Water service area', 'saved': 'inventory/sources/trustee-areas-2026-10-04/santa-clarita-valley-water/scvwa-service-boundary.geojson'},
            {'what': 'City Council: Ordinance 23-4 and the 2024 transition', 'saved': 'inventory/review/council-transition-2026-10-04.md'},
        ],
        'columns': [
            {'key': 'supervisor', 'label': 'County Board of Supervisors', 'bodySlug': 'los-angeles-county-board-of-supervisors',
             'filled': 'elected by district (five supervisorial districts)'},
            {'key': 'cityCouncil', 'label': 'City Council', 'bodySlug': 'city-of-santa-clarita',
             'filled': ('elected by district (five districts, Ordinance 23-4); Districts 1 and 3 since December 2024; '
                        'the three members elected at large in 2022 serve until December 2026, when Districts 2, 4 and 5 '
                        'are first filled')},
            {'key': 'elementary', 'label': 'Elementary school district', 'bodySlug': None,
             'filled': 'elected by trustee area (the body varies by row; see each cell\'s bodySlug)'},
            {'key': 'hart', 'label': 'Hart Union High School District', 'bodySlug': 'william-s-hart-union-high-school-district',
             'filled': 'elected by trustee area'},
            {'key': 'water', 'label': 'SCV Water', 'bodySlug': 'santa-clarita-valley-water',
             'filled': 'elected by division (three divisions, three directors each)'},
            {'key': 'assembly', 'label': 'State Assembly', 'bodySlug': 'california-state-assembly', 'filled': 'elected by district'},
            {'key': 'senate', 'label': 'State Senate', 'bodySlug': 'california-state-senate', 'filled': 'elected by district'},
            {'key': 'houseNow', 'label': 'U.S. House, now (2021 map)', 'bodySlug': 'united-states-house-of-representatives',
             'filled': 'elected by district'},
            {'key': 'house2027', 'label': 'U.S. House, from 3 January 2027 (Proposition 50 map)',
             'bodySlug': 'united-states-house-of-representatives',
             'filled': 'elected by district (2025 map, AB 604, enacted by Proposition 50; first elected November 2026)'},
        ],
        'rows': rows,
    }
    for r in rows:
        assert r['inCity'] == r['checks']['inCensus2020City'], r['place']
    with open(OUT, 'w') as f:
        json.dump(data, f, indent=1, ensure_ascii=False)
        f.write('\n')
    for r in rows:
        c = r['cells']
        print(r['place'], r['inCity'], r['point']['lat'], r['point']['lng'], r['checks']['census2020Cdp'], '|',
              ' | '.join(f"{k}: {c[k].get('body', '')} {c[k]['label']} ({c[k]['filled']})" for k in c))


if __name__ == '__main__':
    main()
