#!/usr/bin/env python3
"""
The schools map: the five Santa Clarita Valley school districts' boundaries and
every school the federal directory has listed in them, open and closed.

Nathan, 29 September 2026: a map of the district boundaries, and all the
schools, current and former, where that is possible.

THE BOUNDARIES are the Census Bureau's school district polygons (TIGERweb,
tigerWMS_Current: layer 16 secondary, layer 18 elementary), fetched by the
NCES district id the district records already carry, simplified to about 20 m.
The Census gives each its grade range: Hart 07-12, the four elementary
districts KG-06. For Castaic the Census says KG-06 where NCES says KG-8; both
are kept, the map says so, and the district record's footnote already gives
NCES.

THE SCHOOLS are every school the NCES Common Core of Data lists in those five
districts in any year from 1987 (inventory/legacy/authorities/schools-ca.json),
placed by the latitude and longitude of the last year each appears. Open or
closed is the directory's own status for the school. Three entries are the districts' own
offices (grades 0 to 0, the district's name) and are dropped; one has no valid
position and is listed without a point.

WHAT IT CANNOT SHOW: schools before 1987. NCES begins then, and the archive
holds only one earlier school, Felton School (1885-1932), which has no
coordinates yet. The map says so rather than implying the valley had no
schools before 1987.

Writes web/data/schools.geojson (the map) and templates/_data/school-directory.json
(the lists on /schools and the district pages). Reads the network; writes only
those two files.

  python3 scripts/import/build_school_map.py
"""
import collections
import json
import os
import time
import urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
TIGER = 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb/tigerWMS_Current/MapServer/{layer}/query?where=GEOID%3D%27{geoid}%27&outFields=GEOID,NAME,LOGRADE,HIGRADE&returnGeometry=true&maxAllowableOffset=0.0002&geometryPrecision=5&outSR=4326&f=geojson'
CCD = 'https://educationdata.urban.org/api/v1/schools/ccd/directory/{year}/?leaid={leaid}'
DISTRICTS = {
    '0642510': {'layer': 16, 'slug': 'william-s-hart-union-high-school-district', 'title': 'William S. Hart Union High School District', 'role': 'high'},
    '0627180': {'layer': 18, 'slug': 'newhall-school-district', 'title': 'Newhall School District', 'role': 'elementary'},
    '0635970': {'layer': 18, 'slug': 'saugus-union-school-district', 'title': 'Saugus Union School District', 'role': 'elementary'},
    '0638220': {'layer': 18, 'slug': 'sulphur-springs-union-school-district', 'title': 'Sulphur Springs Union School District', 'role': 'elementary'},
    '0607740': {'layer': 18, 'slug': 'castaic-union-school-district', 'title': 'Castaic Union School District', 'role': 'elementary'},
}
GRADE = lambda g: None if g is None or g < -1 else ('K' if g in (0, -1) else str(g))


def get(url):
    time.sleep(1)
    req = urllib.request.Request(url, headers={'User-Agent': 'SCVHistory archive school map'})
    return json.load(urllib.request.urlopen(req, timeout=90))


def main():
    features = []
    for leaid, d in DISTRICTS.items():
        g = get(TIGER.format(layer=d['layer'], geoid=leaid))['features'][0]
        p = g['properties']
        features.append({'type': 'Feature', 'geometry': g['geometry'], 'properties': {
            'kind': 'district', 'leaid': leaid, 'slug': d['slug'], 'title': d['title'], 'role': d['role'],
            'censusName': p['NAME'], 'censusGrades': f"{p['LOGRADE'].lstrip('0') or '0'}-{p['HIGRADE'].lstrip('0')}".replace('KG', 'K'),
            'source': 'U.S. Census Bureau, TIGERweb, school districts, GEOID ' + leaid}})
        print('boundary', leaid, p['NAME'], p['LOGRADE'], p['HIGRADE'])

    ca = json.load(open(os.path.join(ROOT, 'inventory/legacy/authorities/schools-ca.json')))
    schools = [s for s in ca['schools'] if s.get('leaid') in DISTRICTS]
    latest = max(s['lastSeen'] for s in schools)
    by_year = collections.defaultdict(list)
    for s in schools:
        by_year[(s['leaid'], s['lastSeen'])].append(s)
    directory = {leaid: {'title': d['title'], 'slug': d['slug'], 'open': [], 'closed': []} for leaid, d in DISTRICTS.items()}
    dropped, unplaced = [], []
    for (leaid, year), ss in sorted(by_year.items()):
        url, rows = CCD.format(year=year, leaid=leaid), []
        while url:
            j = get(url)
            rows += j['results']
            url = j.get('next')
        by_id = {r['ncessch']: r for r in rows}
        for s in ss:
            r = by_id.get(s['ncesId']) or {}
            lo, hi = r.get('lowest_grade_offered'), r.get('highest_grade_offered')
            if lo == 0 and hi == 0 and 'elementary' not in s['name'].lower().replace('elementary school', '') and s['name'].upper() == s['name']:
                dropped.append(s['name'])
                continue
            if s['name'].upper() in ('NEWHALL ELEMENTARY SCHOOL', 'SULPHUR SPRINGS UNION ELEM', 'WILLIAM S HART UNION HIGH') and lo == 0 and hi == 0:
                dropped.append(s['name'])
                continue
            is_open = s.get('status') == 'open'
            item = {'name': s['name'], 'nces': s['ncesId'], 'grades': f"{GRADE(lo)}-{GRADE(hi)}" if GRADE(lo) and GRADE(hi) else '',
                    'opened': s.get('opened'), 'lastListed': s['lastSeen'], 'charter': r.get('charter') == 1, 'city': s.get('city') or ''}
            directory[leaid]['open' if is_open else 'closed'].append(item)
            lat, lon = r.get('latitude'), r.get('longitude')
            if not lat or not lon or lat < 30 or lon > -100:
                unplaced.append(s['name'])
                continue
            features.append({'type': 'Feature', 'geometry': {'type': 'Point', 'coordinates': [round(lon, 5), round(lat, 5)]}, 'properties': {
                'kind': 'school', 'status': 'open' if is_open else 'closed', 'leaid': leaid, **item,
                'source': f'NCES Common Core of Data, school directory {year}, NCES school ID ' + s['ncesId']}})
    for d in directory.values():
        d['open'].sort(key=lambda x: x['name'])
        d['closed'].sort(key=lambda x: (x['lastListed'], x['name']))
    meta = {'built': time.strftime('%Y-%m-%d'), 'by': 'scripts/import/build_school_map.py', 'latestYear': latest,
            'note': 'Schools from 1987 on (NCES). Earlier schools are not in the federal directory; the archive holds Felton School (1885-1932), not yet located.',
            'dropped': dropped, 'unplaced': unplaced}
    with open(os.path.join(ROOT, 'web/data/schools.geojson'), 'w') as f:
        json.dump({'type': 'FeatureCollection', 'meta': meta, 'features': features}, f, separators=(',', ':'))
    with open(os.path.join(ROOT, 'templates/_data/school-directory.json'), 'w') as f:
        json.dump({'meta': meta, 'districts': directory}, f, indent=1)
    pts = [x for x in features if x['properties']['kind'] == 'school']
    print(f"{len(pts)} schools placed ({sum(1 for x in pts if x['properties']['status'] == 'open')} open), dropped {dropped}, unplaced {unplaced}")


if __name__ == '__main__':
    main()
