#!/usr/bin/env python3
"""
Valley district maps: the Santa Clarita Valley's share of each Assembly, State
Senate and U.S. House district, for every plan since 1991, with geometry for maps.

Claude (research subagent), 4 October 2026. Pure Python 3 standard library.

VALLEY DEFINITION (corrected 4 October 2026; widened the same day, Nathan's decision)
  Census Newhall CCD (county subdivision 92110)
  + Agua Dulce CDP (place 00450)
  + every block inside the City of Santa Clarita (place 69088) as that census drew it
  + the unincorporated ground between the City and Agua Dulce: on 2020 blocks, the
    blocks of tracts 9108.07, 9108.08, 9108.09 and 9108.10 outside Acton CDP, and
    the Agua Dulce CDP blocks of tract 9108.14. The rest of 9108.14 (the fringe
    around Agua Dulce, 223 people in 2020) was dropped on Nathan's decision of
    4 October 2026: only its western part lies between, and it does not separate
    cleanly from the northern and southeastern fringe.
  Acton (place 00212, in the South Antelope Valley CCD) stays out.
  So that every census covers the same ground, a 2000 or 2010 block not already in
  is added when its interior point lies inside the 2020 footprint of that ground
  (and it is not in Acton; for 2000 blocks, not inside the
  2010 Acton CDP either). See inventory/review/valley-definition-2026-10-04.md.
  The first pass used Newhall CCD + Agua Dulce CDP only, which left out about
  13,000 City residents in eastern Canyon Country (tracts 9108.07 to 9108.10).

  2000 blocks: Newhall CCD and City from the 2000 PL 94-171 geographic header.
    Agua Dulce was not a CDP in 2000, so 2000 blocks are counted as Agua Dulce when
    their interior point falls inside the 2010 Agua Dulce CDP boundary.
  2010 and 2020 blocks: Newhall CCD as in analysis/scv_blocks.json (block interior
    point inside the CCD), City and Agua Dulce from the Census Block Assignment
    Files (INCPLACE_CDP) of that census.

PLANS AND BLOCK ASSIGNMENTS (all official block equivalency files)
  1991 plan, 2000 blocks: Statewide Database block00_district.txt
  2001 plan, 2010 blocks: Statewide Database 2010block_2001district_equivalency.dbf
  2011 plan, 2010 blocks: Statewide Database 2011_*_state_equiv.dbf
  2021 plan, 2020 blocks: Census Bureau 2022 SLDL and SLDU BEFs; CD118 BEF
  2025 congressional map (Proposition 50, AB 604), 2020 blocks: SELC AB 604 CSV

POPULATION: block POP100 of the census the blocks come from (2000 PL header;
2010 and 2020 from the TIGERweb Census Blocks layers, saved as JSON).

GEOMETRY: TIGER/Line tabblock shapefiles for Los Angeles County (2000 blocks in
the TIGER 2010 edition, 2010 blocks, 2020 PL blocks). No valley block lies in
Ventura County. Each district's part of the valley is the dissolve of its blocks:
every ring edge is collected, edges that appear twice are cancelled, and the rest
are chained into rings (face tracing, sharpest right turn, so parts that touch at
a vertex come out as separate rings). Simplification is Douglas-Peucker at 0.0003
degrees, done once per shared border (arc between junction nodes) so neighbouring
districts and the outline keep identical borders; no ring drops below 4 points.
Coordinates are rounded to 5 decimals.

INPUTS: inventory/sources/legislative-districts-2026-10-04/ (gitignored except
manifest.json). Run with --fetch to download any missing input.

OUTPUTS
  web/data/valley-districts/<chamber>-<plan>.geojson   (13 files)
  web/data/valley-districts/index.json
  inventory/review/valley-district-maps-2026-10-04.json  (tallies and checks)

Run on the host:
    python3 scripts/import/build_valley_district_maps.py [--fetch]
"""
import argparse
import bisect
import collections
import csv
import io
import json
import math
import os
import struct
import sys
import urllib.parse
import urllib.request
import zipfile

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
S = os.path.join(ROOT, 'inventory', 'sources', 'legislative-districts-2026-10-04')
OUT = os.path.join(ROOT, 'web', 'data', 'valley-districts')
REVIEW_JSON = os.path.join(ROOT, 'inventory', 'review', 'valley-district-maps-2026-10-04.json')

TOL = 0.0003
NDIGITS = 5

CITY, AGUA_DULCE, ACTON, NEWHALL_CCD = '69088', '00450', '00212', '92110'
# 2020 tracts holding the City's eastern edge and Agua Dulce; their unincorporated
# land outside Acton is the ground between the City and Agua Dulce (4 October 2026)
BETWEEN_TRACTS_2020 = ('910807', '910808', '910809', '910810', '910814')
# of tract 9108.14 only its Agua Dulce CDP blocks count (the fringe dropped, Nathan, 4 October 2026)
AD_ONLY_TRACTS_2020 = ('910814',)

DOWNLOADS = {
    'tiger/tl_2010_06037_tabblock00.zip': 'https://www2.census.gov/geo/tiger/TIGER2010/TABBLOCK/2000/tl_2010_06037_tabblock00.zip',
    'tiger/tl_2010_06037_tabblock10.zip': 'https://www2.census.gov/geo/tiger/TIGER2010/TABBLOCK/2010/tl_2010_06037_tabblock10.zip',
    'tiger/tl_2020_06037_tabblock20.zip': 'https://www2.census.gov/geo/tiger/TIGER2020PL/LAYER/TABBLOCK/2020/tl_2020_06037_tabblock20.zip',
    'census-baf2010/BlockAssign_ST06_CA.zip': 'https://www2.census.gov/geo/docs/maps-data/data/baf/BlockAssign_ST06_CA.zip',
    'census-baf/sldl_2022.zip': 'https://www2.census.gov/programs-surveys/decennial/rdo/mapping-files/2023/2022-state-legislative-bef/sldl_2022.zip',
    'census-baf/sldu_2022.zip': 'https://www2.census.gov/programs-surveys/decennial/rdo/mapping-files/2023/2022-state-legislative-bef/sldu_2022.zip',
    'bef/cd118.zip': 'https://www2.census.gov/programs-surveys/decennial/rdo/mapping-files/2023/118-congressional-district-bef/cd118.zip',
    'bef/AB604_selc_equivalency.csv': 'https://selc.senate.ca.gov/media/604',
    'swdb/block00_district_txt.zip': 'https://statewidedatabase.org/pub/data/SPECIAL/block00_district_txt.zip',
    'swdb/districts2001_2010blocks_equivalency.zip': 'https://statewidedatabase.org/pub/data/SPECIAL/districts/districts2001_2010blocks_equivalency.zip',
    'swdb/2011_assembly_state_equiv.dbf': 'https://statewidedatabase.org/pub/data/GEOGRAPHY/DECENNIAL2010/2011_assembly_state_equiv.dbf',
    'swdb/2011_senate_state_equiv.dbf': 'https://statewidedatabase.org/pub/data/GEOGRAPHY/DECENNIAL2010/2011_senate_state_equiv.dbf',
    'swdb/2011_congressional_state_equiv.dbf': 'https://statewidedatabase.org/pub/data/GEOGRAPHY/DECENNIAL2010/2011_congressional_state_equiv.dbf',
}
TIGERWEB = 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb'
POP_FILES = {
    '2010': ('census-baf2010/tigerweb_2010_block_pop100_06037.json', 'tigerWMS_Census2010', 18),
    '2020': ('census-baf/tigerweb_2020_block_pop100_06037_06071.json', 'tigerWMS_Census2020', 10),
}

PLACE_NAMES = {
    '69088': 'Santa Clarita city', '74130': 'Stevenson Ranch CDP', '11796': 'Castaic CDP',
    '81967': 'Val Verde CDP', '32385': 'Hasley Canyon CDP', '31092': 'Green Valley CDP',
    '00450': 'Agua Dulce CDP', '00212': 'Acton CDP',
}

CHAMBER_LABEL = {'assembly': 'Assembly District', 'senate': 'Senate District', 'house': 'Congressional District'}
PLANS = [
    # (chamber, plan, vintage, old tally key)
    ('assembly', '1991', '2000', '1991_AD'), ('assembly', '2001', '2010', '2001_AD'),
    ('assembly', '2011', '2010', '2011_AD'), ('assembly', '2021', '2020', 'tiger_sldl2024'),
    ('senate', '1991', '2000', '1991_SD'), ('senate', '2001', '2010', '2001_SD'),
    ('senate', '2011', '2010', '2011_SD'), ('senate', '2021', '2020', 'tiger_sldu2024'),
    ('house', '1991', '2000', '1991_CD'), ('house', '2001', '2010', '2001_CD'),
    ('house', '2011', '2010', '2011_CD'), ('house', '2021', '2020', 'tiger_cd119'),
    ('house', '2025', '2020', 'tiger_cd120'),
]
PLAN_NOTE = {
    '1991': '1991 plan (Special Masters; elections 1992 to 2000)',
    '2001': '2001 plan (Legislature; elections 2002 to 2010)',
    '2011': '2011 plan (Citizens Redistricting Commission; elections 2012 to 2020)',
    '2021': '2021 plan (Citizens Redistricting Commission; elections 2022 on)',
    '2025': '2025 congressional map (AB 604, enacted by Proposition 50; elections from 2026)',
}


def p(*a):
    print(*a, flush=True)


def src(rel):
    return os.path.join(S, rel)


# ---------------------------------------------------------------- fetching

def fetch_missing():
    for rel, url in DOWNLOADS.items():
        path = src(rel)
        if os.path.exists(path):
            continue
        os.makedirs(os.path.dirname(path), exist_ok=True)
        p('fetch', url)
        with urllib.request.urlopen(url, timeout=600) as r, open(path, 'wb') as f:
            f.write(r.read())
    rel, svc, layer = POP_FILES['2010']
    if not os.path.exists(src(rel)):
        out, off = {}, 0
        while True:
            q = {'f': 'json', 'where': "STATE='06' AND COUNTY='037'", 'outFields': 'GEOID,POP100',
                 'returnGeometry': 'false', 'orderByFields': 'GEOID', 'resultOffset': off,
                 'resultRecordCount': 5000}
            data = urllib.parse.urlencode(q).encode()
            d = json.loads(urllib.request.urlopen(f'{TIGERWEB}/{svc}/MapServer/{layer}/query', data=data, timeout=300).read())
            for ft in d['features']:
                out[ft['attributes']['GEOID']] = ft['attributes']['POP100']
            off += len(d['features'])
            if not d['features'] or not d.get('exceededTransferLimit'):
                break
        json.dump(out, open(src(rel), 'w'))


# ---------------------------------------------------------------- readers

def dbf_rows(b, keep=None):
    n, hl, rl = struct.unpack('<xxxxIHH20x', b[:32])
    fields, pos = [], 32
    while b[pos] != 0x0D:
        fields.append((b[pos:pos + 11].split(b'\0')[0].decode(), b[pos + 16]))
        pos += 32
    for i in range(n):
        r = b[hl + i * rl: hl + (i + 1) * rl]
        d, q = {}, 1
        for name, ln in fields:
            d[name] = r[q:q + ln].decode('latin-1').strip()
            q += ln
        if keep is None or keep(d):
            yield d


def pip(x, y, rings):
    inside = False
    for ring in rings:
        n = len(ring)
        j = n - 1
        for i in range(n):
            xi, yi = ring[i]
            xj, yj = ring[j]
            if ((yi > y) != (yj > y)) and (x < (xj - xi) * (y - yi) / (yj - yi) + xi):
                inside = not inside
            j = i
    return inside


def read_shapes(zpath, idfield, wanted):
    """Polygon rings (closed tuples of float pairs) for the wanted block GEOIDs."""
    z = zipfile.ZipFile(zpath)
    base = [n for n in z.namelist() if n.endswith('.shp')][0][:-4]
    dbf = z.read(base + '.dbf')
    shx = z.read(base + '.shx')
    shp = z.read(base + '.shp')
    out = {}
    for i, d in enumerate(dbf_rows(dbf)):
        g = d[idfield]
        if g not in wanted:
            continue
        off = struct.unpack('>i', shx[100 + 8 * i: 104 + 8 * i])[0] * 2
        c = off + 8
        stype = struct.unpack('<i', shp[c:c + 4])[0]
        if stype != 5:
            raise SystemExit(f'unexpected shape type {stype} for {g}')
        nparts, npts = struct.unpack('<ii', shp[c + 36:c + 44])
        parts = list(struct.unpack(f'<{nparts}i', shp[c + 44:c + 44 + 4 * nparts])) + [npts]
        ptbase = c + 44 + 4 * nparts
        flat = struct.unpack(f'<{2 * npts}d', shp[ptbase:ptbase + 16 * npts])
        pts = [(flat[2 * k], flat[2 * k + 1]) for k in range(npts)]
        out[g] = [pts[parts[k]:parts[k + 1]] for k in range(nparts)]
    return out


# ---------------------------------------------------------------- valley blocks

def valley_blocks():
    """{vintage: {geoid: {'pop', 'reg', 'place'}}}; reg in newhall_ccd, agua_dulce, city_outside_ccd, between, enclave."""
    V = {}
    # 2000
    ad = json.load(open(src('tigerweb/aguadulce_2010.json')))['features'][0]['geometry']['rings']
    xs = [q[0] for r in ad for q in r]
    ys = [q[1] for r in ad for q in r]
    adbb = (min(xs), min(ys), max(xs), max(ys))
    b = {}
    with zipfile.ZipFile(src('census2000/cageo.upl.zip')) as z:
        for line in io.TextIOWrapper(z.open('cageo.upl'), encoding='latin-1'):
            if line[8:11] != '750' or line[29:34] != '06037':
                continue
            cs, pl = line[36:41], line[45:50]
            if pl == ACTON:
                continue
            g = line[29:34] + line[55:61] + line[62:66]
            reg = None
            if cs == NEWHALL_CCD:
                reg = 'newhall_ccd'
            elif pl == CITY:
                reg = 'city_outside_ccd'
            else:
                lat = int(line[310:319]) / 1e6
                lon = int(line[319:329]) / 1e6
                if adbb[0] <= lon <= adbb[2] and adbb[1] <= lat <= adbb[3] and pip(lon, lat, ad):
                    reg = 'agua_dulce'
            if reg:
                b[g] = {'pop': int(line[292:301]), 'reg': reg, 'place': pl}
    V['2000'] = b
    # 2010 and 2020
    old = json.load(open(src('analysis/scv_blocks.json')))
    for v in ('2010', '2020'):
        pops = json.load(open(src(POP_FILES[v][0])))
        if v == '2010':
            with zipfile.ZipFile(src('census-baf2010/BlockAssign_ST06_CA.zip')) as z:
                lines = io.TextIOWrapper(z.open('BlockAssign_ST06_CA_INCPLACE_CDP.txt'), encoding='latin-1').read().splitlines()
            sep = ','
        else:
            lines = open(src('census-baf/BlockAssign_ST06_CA_INCPLACE_CDP.txt'), encoding='latin-1').read().splitlines()
            sep = '|'
        place = {}
        for ln in lines[1:]:
            g, pl = ln.split(sep)
            if g.startswith('06037'):
                place[g] = pl
        b = {}
        for g, a in old[v].items():
            if a['reg'] == 'newhall_ccd':
                b[g] = 'newhall_ccd'
        for g, pl in place.items():
            if g in b:
                continue
            if pl == AGUA_DULCE:
                b[g] = 'agua_dulce'
            elif pl == CITY:
                b[g] = 'city_outside_ccd'
        V[v] = {g: {'pop': pops.get(g, 0) or 0, 'reg': r, 'place': place.get(g, '')} for g, r in b.items()}
        # consistency with the earlier pass
        mism = sum(1 for g, a in old[v].items() if a['reg'] != 'acton' and g in V[v] and (a['pop'] or 0) != V[v][g]['pop'])
        if mism:
            p(f'warning: {mism} {v} blocks differ in population from scv_blocks.json')
        if v == '2020':
            place20 = place
    add_between(V, place20)
    fill_enclaves(V)
    return V


def fill_enclaves(V):
    """Count any unincorporated block the valley wholly encloses (a hole in the outline).

    Added 4 October 2026 with the 9108.14 fringe dropped: on 2000 blocks the Agua
    Dulce of that census (the 2010 CDP) and the Newhall CCD close around six blocks
    (11 people) of the dropped western fringe. A valley with an enclave in it is not
    the valley; such blocks count. No 2010 or 2020 block is enclosed.
    """
    files = {'2000': ('tiger/tl_2010_06037_tabblock00.zip', 'BLKIDFP00', '00'),
             '2010': ('tiger/tl_2010_06037_tabblock10.zip', 'GEOID10', '10'),
             '2020': ('tiger/tl_2020_06037_tabblock20.zip', 'GEOID20', '20')}
    for v, (zp, fld, suf) in files.items():
        shapes = read_shapes(src(zp), fld, set(V[v]))
        e, _ = boundary(V[v], shapes)
        rings, _ = chain(e)
        if len(rings) < 2:
            continue
        rings.sort(key=lambda r: -abs(sarea(r)))
        holes = [r for r in rings[1:] if any(pip(r[0][0], r[0][1], [o]) for o in rings if o is not r and abs(sarea(o)) > abs(sarea(r)))]
        if not holes:
            continue
        cand = {g: pt for g, pt in interior_points(src(zp), suf, fld).items()
                if g not in V[v] and any(pip(pt[0], pt[1], [h]) for h in holes)}
        if v == '2000':
            info = {}
            with zipfile.ZipFile(src('census2000/cageo.upl.zip')) as z:
                for line in io.TextIOWrapper(z.open('cageo.upl'), encoding='latin-1'):
                    if line[8:11] == '750' and line[29:34] == '06037':
                        g = line[29:34] + line[55:61] + line[62:66]
                        if g in cand:
                            info[g] = (int(line[292:301]), '' if line[45:50] == '99999' else line[45:50])
        else:
            pops = json.load(open(src(POP_FILES[v][0])))
            if v == '2010':
                with zipfile.ZipFile(src('census-baf2010/BlockAssign_ST06_CA.zip')) as z:
                    pl = dict(ln.split(',') for ln in io.TextIOWrapper(z.open('BlockAssign_ST06_CA_INCPLACE_CDP.txt'), encoding='latin-1').read().splitlines()[1:] if ln.startswith('06037'))
            else:
                pl = dict(ln.split('|') for ln in open(src('census-baf/BlockAssign_ST06_CA_INCPLACE_CDP.txt'), encoding='latin-1').read().splitlines()[1:] if ln.startswith('06037'))
            info = {g: (pops.get(g, 0) or 0, pl.get(g, '')) for g in cand}
        for g, (pop, plc) in info.items():
            if plc:
                raise SystemExit(f'{v} block {g} in place {plc} is enclosed by the valley')
            V[v][g] = {'pop': pop, 'reg': 'enclave', 'place': ''}
        p(f'{v}: filled {len(info)} enclosed blocks, {sum(x[0] for x in info.values())} people')


def interior_points(zpath, suffix, idfield):
    """{geoid: (lon, lat)} for every block of the county shapefile."""
    with zipfile.ZipFile(zpath) as z:
        dbf = z.read([n for n in z.namelist() if n.endswith('.dbf')][0])
    return {d[idfield]: (float(d['INTPTLON' + suffix]), float(d['INTPTLAT' + suffix])) for d in dbf_rows(dbf)}


def add_between(V, place20):
    """The unincorporated ground between the City and Agua Dulce, the same ground for every census.

    Footprint F, drawn on 2020 blocks: every 2020 block of tracts 9108.07, 9108.08,
    9108.09 and 9108.10 outside Acton CDP, and the Agua Dulce blocks of 9108.14
    (the rest of 9108.14 dropped, Nathan, 4 October 2026) (City and Agua Dulce blocks of
    those tracts included). A 2000 or 2010 block not already in the valley is added when its
    interior point lies inside F and it is not in Acton (Acton CDP of that census;
    for 2000 blocks also the 2010 Acton CDP boundary). Incorporated places other
    than the City are never added.
    """
    fblocks = {g for g, pl in place20.items() if g[5:11] in BETWEEN_TRACTS_2020 and pl != ACTON
               and (g[5:11] not in AD_ONLY_TRACTS_2020 or pl == AGUA_DULCE)}
    pops20 = json.load(open(src(POP_FILES['2020'][0])))
    for g in fblocks:
        pl = place20.get(g, '')
        if g in V['2020']:
            continue
        if pl not in ('', AGUA_DULCE, CITY):
            raise SystemExit(f'2020 block {g} in place {pl} inside the between-ground footprint')
        V['2020'][g] = {'pop': pops20.get(g, 0) or 0, 'reg': 'between', 'place': pl}
    fshapes = read_shapes(src('tiger/tl_2020_06037_tabblock20.zip'), 'GEOID20', fblocks)
    e, _ = boundary(fblocks, fshapes)
    frings, _ = chain(e)
    xs = [q[0] for r in frings for q in r]
    ys = [q[1] for r in frings for q in r]
    fbb = (min(xs), min(ys), max(xs), max(ys))
    acton10 = json.load(open(src('tigerweb/acton_2010.json')))['features'][0]['geometry']['rings']

    def in_f(pt):
        return fbb[0] <= pt[0] <= fbb[2] and fbb[1] <= pt[1] <= fbb[3] and pip(pt[0], pt[1], frings)
    # 2010
    pops10 = json.load(open(src(POP_FILES['2010'][0])))
    with zipfile.ZipFile(src('census-baf2010/BlockAssign_ST06_CA.zip')) as z:
        place10 = dict(ln.split(',') for ln in io.TextIOWrapper(z.open('BlockAssign_ST06_CA_INCPLACE_CDP.txt'), encoding='latin-1').read().splitlines()[1:]
                       if ln.startswith('06037'))
    for g, pt in interior_points(src('tiger/tl_2010_06037_tabblock10.zip'), '10', 'GEOID10').items():
        if g in V['2010'] or not in_f(pt):
            continue
        pl = place10.get(g, '')
        if pl == ACTON:
            continue
        if pl:
            raise SystemExit(f'2010 block {g} in place {pl} inside the between-ground footprint')
        V['2010'][g] = {'pop': pops10.get(g, 0) or 0, 'reg': 'between', 'place': pl}
    # 2000 (place and population from the PL header, interior point from TIGER)
    pts00 = interior_points(src('tiger/tl_2010_06037_tabblock00.zip'), '00', 'BLKIDFP00')
    with zipfile.ZipFile(src('census2000/cageo.upl.zip')) as z:
        for line in io.TextIOWrapper(z.open('cageo.upl'), encoding='latin-1'):
            if line[8:11] != '750' or line[29:34] != '06037':
                continue
            g = line[29:34] + line[55:61] + line[62:66]
            if g in V['2000'] or g not in pts00 or not in_f(pts00[g]):
                continue
            pl = line[45:50]
            if pl == ACTON or pip(pts00[g][0], pts00[g][1], acton10):
                continue
            if pl != '99999':
                raise SystemExit(f'2000 block {g} in place {pl} inside the between-ground footprint')
            V['2000'][g] = {'pop': int(line[292:301]), 'reg': 'between', 'place': ''}


# ---------------------------------------------------------------- assignments

def num(s):
    s = s.strip().strip('"')
    return int(s) if s.isdigit() and int(s) > 0 else None


def assignments(V):
    """{(chamber, plan): {geoid: district int or None}} for valley blocks."""
    A = {}
    w00, w10, w20 = set(V['2000']), set(V['2010']), set(V['2020'])
    m = {}
    with zipfile.ZipFile(src('swdb/block00_district_txt.zip')) as z:
        for ln in io.TextIOWrapper(z.open('block00_district.txt'), encoding='latin-1'):
            if ln.startswith('#'):
                continue
            k, ad, sd, cd = ln.rstrip('\n').split('\t')
            if k in w00:
                m[k] = (num(ad), num(sd), num(cd))
    for i, ch in enumerate(('assembly', 'senate', 'house')):
        A[(ch, '1991')] = {g: m.get(g, (None,) * 3)[i] for g in w00}
    with zipfile.ZipFile(src('swdb/districts2001_2010blocks_equivalency.zip')) as z:
        b = z.read('2010block_2001district_equivalency.dbf')
    m = {d['BLOCK2010']: (num(d['AD2001']), num(d['SD2001']), num(d['CD2001']))
         for d in dbf_rows(b, lambda d: d['COUNTY'] == '06037' and d['BLOCK2010'] in w10)}
    for i, ch in enumerate(('assembly', 'senate', 'house')):
        A[(ch, '2001')] = {g: m.get(g, (None,) * 3)[i] for g in w10}
    for ch, f in (('assembly', 'swdb/2011_assembly_state_equiv.dbf'), ('senate', 'swdb/2011_senate_state_equiv.dbf'),
                  ('house', 'swdb/2011_congressional_state_equiv.dbf')):
        b = open(src(f), 'rb').read()
        m = {d['BLOCK']: num(d['DISTRICTID']) for d in dbf_rows(b, lambda d: d['BLOCK'] in w10)}
        A[(ch, '2011')] = {g: m.get(g) for g in w10}

    def bef(zpath, member):
        out = {}
        with zipfile.ZipFile(src(zpath)) as z:
            for ln in io.TextIOWrapper(z.open(member), encoding='latin-1'):
                parts = ln.strip().split(',')
                if parts[0] in w20:
                    out[parts[0]] = num(parts[1])
        return out
    for ch, zp, mem in (('assembly', 'census-baf/sldl_2022.zip', '06_CA_SLDL22.txt'),
                        ('senate', 'census-baf/sldu_2022.zip', '06_CA_SLDU22.txt'),
                        ('house', 'bef/cd118.zip', '06_CA_CD118.txt')):
        m = bef(zp, mem)
        A[(ch, '2021')] = {g: m.get(g) for g in w20}
    m = {}
    for r in csv.reader(open(src('bef/AB604_selc_equivalency.csv'), encoding='utf-8-sig')):
        if r and r[0] in w20:
            m[r[0]] = num(r[1])
    A[('house', '2025')] = {g: m.get(g) for g in w20}
    return A


# ---------------------------------------------------------------- dissolve

def ekey(a, b):
    return (a, b) if a < b else (b, a)


def boundary(blocks, shapes):
    cnt = collections.Counter()
    directed = []
    for g in blocks:
        for ring in shapes[g]:
            for a, b in zip(ring, ring[1:]):
                if a == b:
                    continue
                cnt[ekey(a, b)] += 1
                directed.append((a, b))
    bad = sum(1 for c in cnt.values() if c > 2)
    return [(a, b) for a, b in directed if cnt[ekey(a, b)] == 1], bad


def chain(edges):
    """Face tracing with the sharpest right turn (shapefile rings keep the interior on the right)."""
    outs = collections.defaultdict(list)
    for a, b in edges:
        outs[a].append(b)
    succ_cache = {}

    def succ(prev, cur):
        cands = outs[cur]
        if len(cands) == 1:
            return cands[0]
        ra = math.atan2(prev[1] - cur[1], prev[0] - cur[0])
        best, bang = None, 9.0
        for c in cands:
            ang = (math.atan2(c[1] - cur[1], c[0] - cur[0]) - ra) % (2 * math.pi)
            if ang == 0:
                ang = 2 * math.pi
            if ang < bang:
                best, bang = c, ang
        return best
    seen = set()
    rings = []
    anomalies = 0
    for a, b in edges:
        if (a, b) in seen:
            continue
        ring = [a, b]
        seen.add((a, b))
        prev, cur = a, b
        while True:
            nxt = succ(prev, cur)
            if (cur, nxt) == (a, b) or cur == a and (cur, nxt) in seen:
                break
            if (cur, nxt) in seen:
                alt = [c for c in outs[cur] if (cur, c) not in seen]
                anomalies += 1
                if not alt:
                    break
                nxt = alt[0]
            seen.add((cur, nxt))
            ring.append(nxt)
            prev, cur = cur, nxt
        if ring[-1] != ring[0]:
            anomalies += 1
            ring.append(ring[0])
        rings.append(ring)
    return rings, anomalies


def sarea(ring):
    s = 0.0
    for (x1, y1), (x2, y2) in zip(ring, ring[1:]):
        s += x1 * y2 - x2 * y1
    return s / 2


# ---------------------------------------------------------------- simplification

def dp_keep(pts, tol):
    """Indices kept by Douglas-Peucker (endpoints always)."""
    n = len(pts)
    keep = {0, n - 1}
    stack = [(0, n - 1)]
    while stack:
        i, j = stack.pop()
        if j <= i + 1:
            continue
        (x1, y1), (x2, y2) = pts[i], pts[j]
        dx, dy = x2 - x1, y2 - y1
        L = math.hypot(dx, dy)
        best, bd = None, -1.0
        for k in range(i + 1, j):
            x, y = pts[k]
            d = abs(dy * (x - x1) - dx * (y - y1)) / L if L > 0 else math.hypot(x - x1, y - y1)
            if d > bd:
                best, bd = k, d
        if bd > tol:
            keep.add(best)
            stack.append((i, best))
            stack.append((best, j))
    return keep


def simplify_plan(dist_rings, outline_rings, tol):
    """Topology-preserving: one keep-set of vertices shared by every ring of the plan."""
    owners = collections.defaultdict(set)
    for d, rings in dist_rings.items():
        for r in rings:
            for a, b in zip(r, r[1:]):
                owners[ekey(a, b)].add(d)
    inc = collections.defaultdict(set)
    for k in owners:
        inc[k[0]].add(k)
        inc[k[1]].add(k)
    nodes = set()
    for v, ks in inc.items():
        if len(ks) != 2:
            nodes.add(v)
        else:
            k1, k2 = list(ks)
            if owners[k1] != owners[k2]:
                nodes.add(v)
    keep = set(nodes)
    done = set()
    all_rings = [r for rs in dist_rings.values() for r in rs] + list(outline_rings)
    for r in all_rings:
        pts = r[:-1]
        n = len(pts)
        idx = [i for i, v in enumerate(pts) if v in nodes]
        if not idx:
            if ekey(r[0], r[1]) in done:
                continue
            s = min(range(n), key=lambda i: pts[i])
            arc = pts[s:] + pts[:s] + [pts[s]]
            if arc[1] > arc[-2]:
                arc = arc[::-1]
            arcs = [arc]
        else:
            arcs = []
            for a_i, s in enumerate(idx):
                e = idx[(a_i + 1) % len(idx)]
                arc = pts[s:e + 1] if e > s else pts[s:] + pts[:e + 1]
                arcs.append(arc)
        for arc in arcs:
            k0 = ekey(arc[0], arc[1])
            if k0 in done:
                continue
            for a, b in zip(arc, arc[1:]):
                done.add(ekey(a, b))
            c = arc if (arc[0], arc[1]) <= (arc[-1], arc[-2]) else arc[::-1]
            for i in dp_keep(c, tol):
                keep.add(c[i])
    # never let a ring drop below 4 points (3 distinct)
    while True:
        added = 0
        for r in all_rings:
            pts = r[:-1]
            f = [v for v in pts if v in keep]
            if len(set(f)) < 3:
                n = len(pts)
                for i in (0, n // 3, (2 * n) // 3):
                    if pts[i] not in keep:
                        keep.add(pts[i])
                        added += 1
        if not added:
            break
    return keep


def finish_ring(r, keep):
    f = [v for v in r[:-1] if v in keep]
    out = []
    for x, y in f:
        q = (round(x, NDIGITS), round(y, NDIGITS))
        if not out or out[-1] != q:
            out.append(q)
    while len(out) > 1 and out[-1] == out[0]:
        out.pop()
    if len(set(out)) < 3 or sarea(out + [out[0]]) == 0:
        return None
    return out + [out[0]]


def to_polygons(rings):
    """Shapefile-oriented rings (outer clockwise) to GeoJSON polygon coordinate lists (outer CCW)."""
    outers, holes = [], []
    for r in rings:
        a = sarea(r)
        (outers if a < 0 else holes).append(r)
    polys = [[o[::-1]] for o in outers]
    obb = []
    for o in outers:
        xs = [q[0] for q in o]
        ys = [q[1] for q in o]
        obb.append((min(xs), min(ys), max(xs), max(ys), abs(sarea(o))))
    orphan = 0
    for h in holes:
        best, ba = None, None
        hs = set(h)
        for i, o in enumerate(outers):
            x0, y0, x1, y1, ar = obb[i]
            if not all(x0 <= q[0] <= x1 and y0 <= q[1] <= y1 for q in h[:3]):
                continue
            os_ = set(o)
            test = next((q for q in h if q not in os_), None)
            if test is None:
                continue
            if pip(test[0], test[1], [o]) and (ba is None or ar < ba):
                best, ba = i, ar
        if best is None:
            orphan += 1
            continue
        polys[best].append(h[::-1])
    return polys, orphan


def geometry(polys):
    if len(polys) == 1:
        return {'type': 'Polygon', 'coordinates': [[list(q) for q in r] for r in polys[0]]}
    return {'type': 'MultiPolygon', 'coordinates': [[[list(q) for q in r] for r in pl] for pl in polys]}


# ---------------------------------------------------------------- checks

LAT0 = 34.45
KX = 111.320 * math.cos(math.radians(LAT0))
KY = 110.574


def km2(rings):
    return sum(-sarea(r) for r in rings) * KX * KY  # shapefile: outer negative


def scan_check(features, outline, n=300):
    """Grid of n x n points over the outline's bounding box; counts districts covering each point."""
    xs = [q[0] for r in outline for q in r]
    ys = [q[1] for r in outline for q in r]
    x0, x1, y0, y1 = min(xs), max(xs), min(ys), max(ys)

    def edges(rings):
        return [(a, b) for r in rings for a, b in zip(r, r[1:])]
    fe = [edges(r) for r in features]
    oe = edges(outline)

    def crossings(E, y):
        c = []
        for (xa, ya), (xb, yb) in E:
            if (ya > y) != (yb > y):
                c.append(xa + (y - ya) * (xb - xa) / (yb - ya))
        c.sort()
        return c
    inside_pts = gaps = overlaps = outside_cover = 0
    for j in range(n):
        y = y0 + (j + 0.5) * (y1 - y0) / n
        oc = crossings(oe, y)
        fcs = [crossings(E, y) for E in fe]
        for i in range(n):
            x = x0 + (i + 0.5) * (x1 - x0) / n
            ino = bisect.bisect_left(oc, x) % 2 == 1
            cov = sum(1 for c in fcs if bisect.bisect_left(c, x) % 2 == 1)
            if ino:
                inside_pts += 1
                if cov == 0:
                    gaps += 1
                elif cov > 1:
                    overlaps += 1
            elif cov:
                outside_cover += 1
    return {'gridPoints': n * n, 'insideOutline': inside_pts, 'gaps': gaps, 'overlaps': overlaps,
            'coveredOutsideOutline': outside_cover}


# ---------------------------------------------------------------- main

def ordinal(n):
    if 10 <= n % 100 <= 20:
        s = 'th'
    else:
        s = {1: 'st', 2: 'nd', 3: 'rd'}.get(n % 10, 'th')
    return f'{n}{s}'


def old_tally(T, key, vintage):
    regs = ['scv_core', 'agua_dulce_and_other_tract9108'] if vintage == '2000' else ['scv_core', 'agua_dulce']
    c = collections.Counter()
    for r in regs:
        for d, v in T[key].get(r, {}).items():
            c[int(d)] += v
    return {d: v for d, v in c.items() if v}


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--fetch', action='store_true', help='download missing inputs first')
    args = ap.parse_args()
    if args.fetch:
        fetch_missing()
    os.makedirs(OUT, exist_ok=True)
    V = valley_blocks()
    for v in ('2000', '2010', '2020'):
        c = collections.Counter()
        for a in V[v].values():
            c[a['reg']] += a['pop']
        p(v, len(V[v]), 'blocks', dict(c), 'total', sum(c.values()))
    A = assignments(V)
    shapes = {}
    for v, (zp, fld) in {'2000': ('tiger/tl_2010_06037_tabblock00.zip', 'BLKIDFP00'),
                         '2010': ('tiger/tl_2010_06037_tabblock10.zip', 'GEOID10'),
                         '2020': ('tiger/tl_2020_06037_tabblock20.zip', 'GEOID20')}.items():
        shapes[v] = read_shapes(src(zp), fld, set(V[v]))
        miss = set(V[v]) - set(shapes[v])
        p(v, 'shapes', len(shapes[v]), 'missing', len(miss))
        if miss:
            raise SystemExit(f'{len(miss)} valley blocks have no geometry in {zp}')
    T = json.load(open(src('analysis/tally.json')))

    # outlines per vintage
    outline = {}
    for v in V:
        e, bad = boundary(V[v], shapes[v])
        rings, anom = chain(e)
        blk_area = sum(km2(shapes[v][g]) for g in V[v])
        outline[v] = {'rings': rings, 'km2': km2(rings), 'blocks_km2': blk_area, 'edges_over2': bad, 'anomalies': anom}
        p(v, 'outline rings', len(rings), 'area km2', round(km2(rings), 3), 'sum of blocks', round(blk_area, 3), 'anomalies', anom, bad)

    index = {
        '_about': ('The Santa Clarita Valley\'s share of each Assembly, State Senate and U.S. House district, '
                   'by plan, with one GeoJSON per chamber and plan for maps. Built by '
                   'scripts/import/build_valley_district_maps.py on 4 October 2026 (Claude, research subagent). '
                   'Reports: inventory/review/valley-district-maps-2026-10-04.md and '
                   'inventory/review/valley-definition-2026-10-04.md.'),
        'valleyDefinition': ('Census Newhall CCD (county subdivision 92110) plus Agua Dulce CDP plus every census block '
                             'inside the City of Santa Clarita as the census of that year drew it, plus the unincorporated land '
                             'between the City and Agua Dulce (on 2020 blocks: tracts 9108.07, 9108.08, 9108.09 and 9108.10 '
                             'outside Acton CDP; of tract 9108.14 only Agua Dulce itself). Acton is excluded. So that every census '
                             'covers the same ground, a 2000 or 2010 block is also counted when its interior point lies inside that '
                             '2020 footprint. Agua Dulce was not a CDP in 2000; on 2000 blocks it is the blocks whose '
                             'interior point lies inside the 2010 Agua Dulce CDP.'),
        'method': ('Each census block in the valley assigned to its district by the official block equivalency file '
                   'for the plan (1991 plan on 2000 blocks, Statewide Database; 2001 and 2011 plans on 2010 blocks, '
                   'Statewide Database; 2021 plan on 2020 blocks, Census Bureau 2022 SLD and CD118 equivalency files; '
                   '2025 congressional map on 2020 blocks, AB 604 equivalency file). Population is the block POP100 of '
                   'that census. Geometry: TIGER/Line block polygons dissolved by district, simplified with '
                   'Douglas-Peucker at 0.0003 degrees along shared borders, coordinates rounded to 5 decimals (WGS84 / NAD83 '
                   'longitude, latitude). share is percent of the valley population, one decimal; main marks the largest share.'),
        'files': {},
    }
    review = {'blocks': {}, 'plans': {}}
    for v in V:
        c = collections.Counter()
        for a in V[v].values():
            c[a['reg']] += a['pop']
        review['blocks'][v] = {'count': len(V[v]), 'byRegion': dict(c), 'total': sum(c.values()),
                               'outlineKm2': round(outline[v]['km2'], 3), 'sumOfBlocksKm2': round(outline[v]['blocks_km2'], 3)}

    for ch, plan, v, oldkey in PLANS:
        name = f'{ch}-{plan}'
        asg = dict(A[(ch, plan)])
        unassigned = [g for g, d in asg.items() if d is None]
        unassigned_pop = sum(V[v][g]['pop'] for g in unassigned)
        # geometry only: give an unassigned block the district it shares most border with
        filled = 0
        if unassigned:
            emap = collections.defaultdict(list)
            for g in V[v]:
                for r in shapes[v][g]:
                    for a, b in zip(r, r[1:]):
                        emap[ekey(a, b)].append(g)
            pending = set(unassigned)
            while pending:
                progress = False
                for g in list(pending):
                    score = collections.Counter()
                    for r in shapes[v][g]:
                        for a, b in zip(r, r[1:]):
                            for h in emap[ekey(a, b)]:
                                if h != g and asg.get(h) is not None:
                                    score[asg[h]] += math.hypot(b[0] - a[0], b[1] - a[1])
                    if score:
                        asg[g] = score.most_common(1)[0][0]
                        pending.discard(g)
                        filled += 1
                        progress = True
                if not progress:
                    break
        pops = collections.Counter()
        byreg = collections.defaultdict(collections.Counter)
        byplace = collections.defaultdict(collections.Counter)
        for g, a in V[v].items():
            d = A[(ch, plan)][g]
            if d is None:
                continue
            pops[d] += a['pop']
            byreg[a['reg']][d] += a['pop']
            pl = PLACE_NAMES.get(a['place'], 'other (unincorporated, no CDP)')
            if a['reg'] == 'agua_dulce' and v == '2000':
                pl = 'Agua Dulce CDP'
            byplace[pl][d] += a['pop']
        total = sum(pops.values())
        main_d = max(pops, key=lambda d: pops[d])
        groups = collections.defaultdict(list)
        for g, d in asg.items():
            if d is not None:
                groups[d].append(g)
        draw = {}
        checks = {'unassignedBlocks': len(unassigned), 'unassignedPop': unassigned_pop, 'filledForGeometry': filled}
        for d, gs in groups.items():
            e, bad = boundary(gs, shapes[v])
            rings, anom = chain(e)
            draw[d] = rings
            if anom or bad:
                checks.setdefault('chainAnomalies', {})[str(d)] = anom
        raw_dist_km2 = {d: km2(r) for d, r in draw.items()}
        checks['rawOutlineKm2'] = round(outline[v]['km2'], 4)
        checks['rawDistrictsKm2'] = round(sum(raw_dist_km2.values()), 4)
        keep = simplify_plan(draw, outline[v]['rings'], TOL)
        feats = []
        final_rings = {}
        dropped = 0
        for d in sorted(draw, key=lambda d: (-pops.get(d, 0), d)):
            fr = []
            for r in draw[d]:
                f = finish_ring(r, keep)
                if f is None:
                    dropped += 1
                else:
                    fr.append(f)
            final_rings[d] = fr
            polys, orphan = to_polygons(fr)
            if orphan:
                checks.setdefault('orphanHoles', {})[str(d)] = orphan
            pop = pops.get(d, 0)
            props = {'district': d, 'label': f'{ordinal(d)} {CHAMBER_LABEL[ch]}', 'pop': pop,
                     'share': round(100.0 * pop / total, 1), 'main': d == main_d}
            feats.append({'type': 'Feature', 'properties': props, 'geometry': geometry(polys)})
        ofr = []
        for r in outline[v]['rings']:
            f = finish_ring(r, keep)
            if f is None:
                dropped += 1
            else:
                ofr.append(f)
        opolys, orphan = to_polygons(ofr)
        feats.append({'type': 'Feature', 'properties': {'role': 'outline'}, 'geometry': geometry(opolys)})
        checks['droppedDegenerateRings'] = dropped
        checks['finalOutlineKm2'] = round(km2(ofr), 4)
        checks['finalDistrictsKm2'] = round(sum(km2(r) for r in final_rings.values()), 4)
        checks['districtKm2'] = {str(d): round(km2(r), 3) for d, r in final_rings.items()}
        checks['grid'] = scan_check(list(final_rings.values()), ofr)
        fc = {'type': 'FeatureCollection', 'name': name,
              'properties': {'chamber': ch, 'plan': plan, 'planNote': PLAN_NOTE[plan],
                             'populationBasis': f'{v} Census', 'valleyPop': total},
              'features': feats}
        path = os.path.join(OUT, f'{name}.geojson')
        with open(path, 'w') as f:
            json.dump(fc, f, separators=(',', ':'))
        size = os.path.getsize(path)
        old = old_tally(T, oldkey, v)
        oldtot = sum(old.values())
        index['files'][name] = {
            'file': f'{name}.geojson', 'chamber': ch, 'plan': plan, 'planNote': PLAN_NOTE[plan],
            'populationBasis': f'{v} Census', 'valleyPop': total,
            'districts': {str(d): {'pop': pops[d], 'share': round(100.0 * pops[d] / total, 1), 'main': d == main_d}
                          for d in sorted(pops, key=lambda d: -pops[d])},
        }
        review['plans'][name] = {
            'bytes': size, 'valleyPop': total, 'oldValleyPop': oldtot,
            'new': {str(d): pops[d] for d in sorted(pops, key=lambda d: -pops[d])},
            'old': {str(d): old[d] for d in sorted(old, key=lambda d: -old[d])},
            'byRegion': {r: dict(c) for r, c in sorted(byreg.items())},
            'byPlace': {pl: dict(c) for pl, c in sorted(byplace.items())},
            'checks': checks,
        }
        p(name, size, 'bytes', dict(pops), 'old', old, 'grid', checks['grid'], 'km2', checks['finalOutlineKm2'], checks['finalDistrictsKm2'],
          'unassigned', len(unassigned), unassigned_pop, 'dropped', dropped)
    with open(os.path.join(OUT, 'index.json'), 'w') as f:
        json.dump(index, f, indent=1)
    with open(REVIEW_JSON, 'w') as f:
        json.dump(review, f, indent=1)


if __name__ == '__main__':
    main()
