#!/usr/bin/env python3
"""
Derives inventory/legacy/authorities/schools-ca.json: CDE public schools and
districts for Los Angeles County.

WHAT THIS IS FOR

The corpus names schools constantly and the archive holds almost none of them as
records. When one is approved from the review screen it should arrive with its
official name, its CDS code, its district as a parent, and its level, rather
than with whatever spelling the article used.

THE SOURCE, AND ITS LIMIT

CDE publishes the full directory as pubschls.txt, which carries open AND closed
schools with OpenDate and ClosedDate. That file is behind a WAF that rejects
every non-browser client: curl gets a 3 KB page headed "CA Department of
Education Block" whatever headers it carries.

So this reads the same department's data from the state open-data portal
instead, where it is published as an ArcGIS feature layer. The layer carries
CDSCode, FedID (the NCES id), district, school type, level, grade span, open
and close dates, and the address.

It carries only ACTIVE schools. Los Angeles County returns 2,140 rows and not
one of them has Status "Closed". For an archive of a valley whose schools are
mostly gone that is the wrong half of the data, and it is stated here rather
than discovered later: a school this file cannot match is not thereby a school
that never existed.

Two further limits worth knowing before trusting a miss:

  - CDE's directory begins in the 1980s. A schoolhouse that closed in 1910 is
    not in the full file either, so the WAF is not the only reason for a miss.
  - The layer is a single school year. A school closed last year is already
    gone from it.

To fill the closed half, a person with a browser can fetch
https://www.cde.ca.gov/schooldirectory/report?rid=dl1&tp=txt and run this
script against the saved file with --file.

USAGE

    python3 scripts/import/derive_schools_ca.py                 # from the portal
    python3 scripts/import/derive_schools_ca.py --file pubschls.txt

Writes the JSON and prints what it found. Read only as far as the project is
concerned: it writes one file under inventory/legacy/authorities and touches no
database.
"""

import argparse
import csv
import datetime
import json
import os
import sys
import urllib.parse
import urllib.request

LAYER = ('https://services3.arcgis.com/fdvHcZVgB2QSRNkL/arcgis/rest/services/'
         'SchoolSites2526/FeatureServer/0/query')
PORTAL = 'https://gis.data.ca.gov/datasets/CDEGIS::california-public-schools-2025-26'
CDE_FULL = 'https://www.cde.ca.gov/schooldirectory/report?rid=dl1&tp=txt'

OUT = os.path.join(os.path.dirname(__file__), '..', '..',
                   'inventory', 'legacy', 'authorities', 'schools-ca.json')

FIELDS = ['CDSCode', 'FedID', 'CDCode', 'SchoolName', 'DistrictName', 'SchoolType',
          'SchoolLevel', 'Status', 'OpenDate', 'ClosedDate', 'City', 'Zip',
          'Website', 'GradeLow', 'GradeHigh', 'Latitude', 'Longitude']

# CDE's own level vocabulary, mapped to the five the archive uses.
LEVEL = {
    'elementary': 'elementary', 'elem': 'elementary', 'preschool': 'elementary',
    'intermediate/middle/junior high': 'middle', 'middle': 'middle',
    'junior high': 'middle',
    'high school': 'high', 'high': 'high',
    'k-12 schools (public)': 'high', 'continuation high schools': 'high',
    'adult': 'college', 'college': 'college',
    'district': 'district', 'county office': 'district',
}


def level_of(school_level: str, school_type: str) -> str:
    for v in (school_level or '', school_type or ''):
        k = v.strip().lower()
        if k in LEVEL:
            return LEVEL[k]
    k = (school_level or school_type or '').lower()
    for needle, out in (('element', 'elementary'), ('middle', 'middle'),
                        ('junior', 'middle'), ('high', 'high'),
                        ('college', 'college'), ('district', 'district')):
        if needle in k:
            return out
    return ''


def fetch_portal(county: str) -> list:
    rows, offset = [], 0
    while True:
        q = urllib.parse.urlencode({
            'where': "CountyName='%s'" % county,
            'outFields': ','.join(FIELDS),
            'returnGeometry': 'false',
            'resultOffset': offset,
            'resultRecordCount': 2000,
            'f': 'json',
        })
        with urllib.request.urlopen(LAYER + '?' + q, timeout=120) as r:
            d = json.loads(r.read().decode('utf-8'))
        feats = d.get('features', [])
        rows += [f['attributes'] for f in feats]
        if not d.get('exceededTransferLimit') and len(feats) < 2000:
            break
        offset += len(feats)
        if not feats:
            break
    return rows


def read_full_file(path: str, county: str) -> list:
    """The real CDE directory, tab separated, if somebody has fetched it."""
    out = []
    with open(path, encoding='utf-8-sig', errors='replace') as f:
        for x in csv.DictReader(f, delimiter='\t'):
            if (x.get('County') or '').strip().lower() != county.lower():
                continue
            out.append({
                'CDSCode': (x.get('CDSCode') or '').strip(),
                'FedID': (x.get('NCESDist') or '') + (x.get('NCESSchool') or ''),
                'SchoolName': (x.get('School') or '').strip(),
                'DistrictName': (x.get('District') or '').strip(),
                'SchoolType': (x.get('SOCType') or '').strip(),
                'SchoolLevel': (x.get('EILName') or '').strip(),
                'Status': (x.get('StatusType') or '').strip(),
                'OpenDate': (x.get('OpenDate') or '').strip(),
                'ClosedDate': (x.get('ClosedDate') or '').strip(),
                'City': (x.get('City') or '').strip(),
                'Zip': (x.get('Zip') or '').strip(),
                'Website': (x.get('Website') or '').strip(),
            })
    return out


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--file', help='a saved pubschls.txt, which includes closed schools')
    ap.add_argument('--county', default='Los Angeles')
    a = ap.parse_args()

    if a.file:
        raw = read_full_file(a.file, a.county)
        source = 'CDE public school directory (pubschls.txt), fetched by hand'
        url = CDE_FULL
        closed_included = True
    else:
        raw = fetch_portal(a.county)
        source = 'California Public Schools 2025-26, CDE via the state open data portal'
        url = PORTAL
        closed_included = False

    schools, districts = [], {}
    for x in raw:
        name = (x.get('SchoolName') or '').strip()
        dist = (x.get('DistrictName') or '').strip()
        cds = (x.get('CDSCode') or '').strip()
        if dist and dist not in districts:
            districts[dist] = {
                'name': dist,
                'cdsCode': (cds[:7] + '0000000') if len(cds) >= 7 else '',
                'level': 'district',
            }
        if not name or name.lower() == 'no data':
            continue
        schools.append({
            'cdsCode': cds,
            'ncesId': (x.get('FedID') or '').strip(),
            'name': name,
            'district': dist,
            'type': (x.get('SchoolType') or '').strip(),
            'level': level_of(x.get('SchoolLevel'), x.get('SchoolType')),
            'status': (x.get('Status') or '').strip(),
            'opened': (x.get('OpenDate') or '').strip(),
            'closed': (x.get('ClosedDate') or '').strip(),
            'city': (x.get('City') or '').strip(),
            'zip': (x.get('Zip') or '').strip(),
            'website': (x.get('Website') or '').strip(),
        })

    out = {
        'provenance': {
            'source': source,
            'url': url,
            'downloaded': datetime.date.today().isoformat(),
            'derived_by': 'scripts/import/derive_schools_ca.py',
            'county': a.county,
            'schools': len(schools),
            'districts': len(districts),
            'closed_schools_included': closed_included,
            'note': (
                'Active schools only unless closed_schools_included is true. The full CDE '
                'directory carries closed schools with their close dates, but it sits behind '
                'a WAF that rejects non-browser clients, so this is the same department\'s '
                'data taken from the state open data portal. A school absent from this table '
                'is not thereby a school that never existed: CDE\'s directory also begins in '
                'the 1980s, so anything that closed before then is in neither source.'),
        },
        'schools': sorted(schools, key=lambda s: s['name']),
        'districts': sorted(districts.values(), key=lambda d: d['name']),
    }

    path = os.path.normpath(OUT)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(out, f, separators=(',', ':'), sort_keys=True)

    print('schools: %d, districts: %d' % (len(schools), len(districts)))
    print('closed included: %s' % closed_included)
    print('wrote %s  %.2f MB' % (path, os.path.getsize(path) / 1e6))
    lv = {}
    for s in schools:
        lv[s['level'] or '(none)'] = lv.get(s['level'] or '(none)', 0) + 1
    for k, v in sorted(lv.items(), key=lambda x: -x[1]):
        print('   %-12s %d' % (k, v))
    return 0


if __name__ == '__main__':
    sys.exit(main())
