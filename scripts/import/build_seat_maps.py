#!/usr/bin/env python3
"""
The seat boundaries for the body hub's map, one file per body, from the Los Angeles County
Registrar-Recorder/County Clerk's public layers (Nathan, 4 October 2026: "Yes, use the County's layers
for every body"; "Show Castaic's letters, as the body itself uses them, with the County's numbers noted";
SCV Water: "Accept the gap with a note").

Source files, saved with their manifests: inventory/sources/trustee-areas-2026-10-04/<body>/rrcc-*.geojson
  School Districts Trustee Areas (item 56e294422060461bb9bf36c95aa4d262), TRUSTEE_AREA 1 to 5, and the
  district's own outline as the feature with no TRUSTEE_AREA.
  Division_Boundaries: SCV Water's divisions 1 to 3, the City's council districts 1 to 5.
Licence: the LA County GIS Terms of Use (copy, publish, adapt; no endorsement; as is). The layers say they
are for election purposes, not legal boundaries.

Writes web/data/seats/<body record slug>.geojson. Each seat feature carries:
  seat     the slug of the archive's seat place record, which the seat control uses
  label    the seat as the body names it ("Trustee Area C", "Division 2", "District 4")
  county   the County's own label ("TA3"), shown where it differs
Outline features carry seat "" and role "outline". Coordinates are rounded to 5 decimals (about a metre).
Run: python3 scripts/import/build_seat_maps.py
"""
import json, os, sys

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
SRC = os.path.join(ROOT, 'inventory', 'sources', 'trustee-areas-2026-10-04')
OUT = os.path.join(ROOT, 'web', 'data', 'seats')
SOURCE = ('Los Angeles County Registrar-Recorder/County Clerk, "School Districts Trustee Areas" and "Division_Boundaries" '
          '(ArcGIS Online), downloaded 4 October 2026. LA County GIS Terms of Use. For election purposes, not legal boundaries.')

def rnd(c):
    if isinstance(c[0], (int, float)):
        return [round(c[0], 5), round(c[1], 5)]
    return [rnd(x) for x in c]

def feat(f, seat, label, county, role='seat'):
    g = f['geometry']
    return {'type': 'Feature', 'properties': {'seat': seat, 'label': label, 'county': county, 'role': role},
            'geometry': {'type': g['type'], 'coordinates': rnd(g['coordinates'])}}

# body record slug -> (source folder, file, kind, seat slug prefix, label pattern, letters)
SCHOOL = {
    'william-s-hart-union-high-school-district': ('hart-union-high-school-district', 'rrcc-school-districts-trustee-areas-hart.geojson', 'william-s-hart-union-high-school-district-trustee-area-', None),
    'newhall-school-district': ('newhall-school-district', 'rrcc-school-districts-trustee-areas-newhall.geojson', 'newhall-school-district-trustee-area-', None),
    'saugus-union-school-district': ('saugus-union-school-district', 'rrcc-school-districts-trustee-areas-saugus.geojson', 'saugus-union-school-district-trustee-area-', None),
    'sulphur-springs-union-school-district': ('sulphur-springs-union-school-district', 'rrcc-school-districts-trustee-areas-sulphur-springs.geojson', 'sulphur-springs-union-school-district-trustee-area-', None),
    'castaic-union-school-district': ('castaic-union-school-district', 'rrcc-school-districts-trustee-areas-castaic.geojson', 'castaic-union-school-district-trustee-area-', 'ABCDE'),
}
DIVISION = {
    'santa-clarita-valley-water': ('santa-clarita-valley-water', 'rrcc-division-boundaries-scv-water.geojson', 'santa-clarita-valley-water-division-', 'Division',
                                   'The County\'s division layer stops at the Los Angeles County line, so the part of Division 3 in Ventura County is not drawn.'),
    'city-of-santa-clarita': ('city-of-santa-clarita', 'rrcc-division-boundaries-santa-clarita-council.geojson', 'santa-clarita-city-council-district-', 'District', ''),
}

os.makedirs(OUT, exist_ok=True)
for slug, (folder, fn, prefix, letters) in SCHOOL.items():
    g = json.load(open(os.path.join(SRC, folder, fn)))
    fs = []
    for f in g['features']:
        ta = f['properties'].get('TRUSTEE_AREA')
        if not ta:
            fs.append(feat(f, '', 'District boundary', '', 'outline'))
            continue
        n = int(ta)
        code = letters[n - 1].lower() if letters else str(n)
        label = 'Trustee Area ' + (letters[n - 1] if letters else str(n))
        fs.append(feat(f, prefix + code, label, 'TA' + str(n)))
    out = {'type': 'FeatureCollection', 'source': SOURCE, 'note': ('The County numbers these areas 1 to 5; the district letters them A to E.' if letters else ''), 'features': fs}
    json.dump(out, open(os.path.join(OUT, slug + '.geojson'), 'w'), separators=(',', ':'))
    print(slug, len(fs), 'features')
for slug, (folder, fn, prefix, word, note) in DIVISION.items():
    g = json.load(open(os.path.join(SRC, folder, fn)))
    fs = []
    for f in g['features']:
        code = f['properties']['Code']
        n = int(code[-3:])
        fs.append(feat(f, prefix + str(n), word + ' ' + str(n), f['properties']['DivisionName']))
    out = {'type': 'FeatureCollection', 'source': SOURCE, 'note': note, 'features': fs}
    json.dump(out, open(os.path.join(OUT, slug + '.geojson'), 'w'), separators=(',', ':'))
    print(slug, len(fs), 'features')

# The list templates read to know which bodies have a seat file (Twig cannot look in web/).
bodies = sorted(f[:-8] for f in os.listdir(OUT) if f.endswith('.geojson'))
json.dump({'_about': 'Bodies with a seat boundary file in web/data/seats/, written by scripts/import/build_seat_maps.py.', 'bodies': bodies},
          open(os.path.join(ROOT, 'templates', '_data', 'seat-maps.json'), 'w'), indent=1)
print('seat-maps.json:', len(bodies))
