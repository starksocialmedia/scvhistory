#!/usr/bin/env python3
"""
Derives inventory/legacy/authorities/schools-ca.json from the NCES Common Core
of Data, read through the Urban Institute's Education Data API.

WHY NCES AND NOT CDE

CDE publishes the California directory with open and closed schools and their
close dates, which is exactly what an archive wants. It is behind a WAF that
refuses curl and, as it turns out, refuses a browser download too. So this uses
the federal source instead. NCES collects the same schools from the same
districts; the state's own CDS code comes through as the state school id, so
nothing is lost but the trip.

CLOSURE IS DERIVED FROM PRESENCE, NOT DECLARED

This is the part worth understanding before trusting a number. CCD is a yearly
census: each year lists the schools that existed that year. A school does not
carry a "closed in 1997" field. It is in the 1996 file and absent from 1998,
and that absence is the fact.

So every available year is pulled and each school is tracked across them. A
school whose last appearance is before the most recent year is treated as
closed, and its closure year is the year after it was last seen. Where CCD
states a status of Closed outright, that is used and preferred.

The consequence, stated plainly: a school that closed before 1987 is in none of
these files and cannot be found here. Most of the schoolhouses this archive
writes about closed long before then. This table is for the modern ones, and a
miss is not evidence of anything.

COLLEGES ARE NOT HERE

CCD covers public elementary and secondary schools. College of the Canyons and
The Master's University are in IPEDS, a different collection. The archive's
"college" level will stay empty from this source.

USAGE

    python3 scripts/import/derive_schools_nces.py                 # all years
    python3 scripts/import/derive_schools_nces.py --from 2000
"""

import argparse
import collections
import datetime
import json
import os
import sys
import time
import urllib.request

API = 'https://educationdata.urban.org/api/v1/schools/ccd/directory'
COUNTY = '06037'          # Los Angeles County
FIPS = '6'                # California
OUT = os.path.join(os.path.dirname(__file__), '..', '..',
                   'inventory', 'legacy', 'authorities', 'schools-ca.json')

# CCD school_status. 2 and 6 are the ones that mean "not operating".
STATUS = {
    1: 'open', 2: 'closed', 3: 'new', 4: 'added', 5: 'changed agency',
    6: 'inactive', 7: 'future', 8: 'reopened',
}

# CCD school_level, mapped to the five levels the archive uses.
LEVEL = {1: 'elementary', 2: 'middle', 3: 'high', 4: '', 5: ''}


def fetch(year: int) -> list:
    url = '%s/%d/?fips=%s&county_code=%s&limit=10000' % (API, year, FIPS, COUNTY)
    for attempt in range(3):
        try:
            req = urllib.request.Request(url, headers={
                'User-Agent': 'scvhistory-archive/1.0 (archive research)'})
            with urllib.request.urlopen(req, timeout=180) as r:
                d = json.loads(r.read().decode('utf-8'))
            rows = d.get('results', [])
            # Page on, in the years where one request is not enough.
            nxt = d.get('next')
            while nxt:
                with urllib.request.urlopen(urllib.request.Request(nxt, headers={
                        'User-Agent': 'scvhistory-archive/1.0'}), timeout=180) as r2:
                    d2 = json.loads(r2.read().decode('utf-8'))
                rows += d2.get('results', [])
                nxt = d2.get('next')
            return rows
        except Exception as e:                       # noqa: BLE001
            if attempt == 2:
                print('  %d: failed after 3 tries (%s)' % (year, e))
                return []
            time.sleep(5 * (attempt + 1))
    return []


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--from', dest='start', type=int, default=1987)
    ap.add_argument('--to', dest='end', type=int,
                    default=datetime.date.today().year)
    a = ap.parse_args()

    schools = {}
    districts = {}
    years_seen = []

    for year in range(a.start, a.end + 1):
        rows = fetch(year)
        if not rows:
            continue
        years_seen.append(year)
        print('  %d: %d schools' % (year, len(rows)))

        for x in rows:
            nid = str(x.get('ncessch') or '').strip()
            if not nid:
                continue
            st = x.get('school_status')
            st = int(st) if isinstance(st, (int, float)) or (
                isinstance(st, str) and st.isdigit()) else None
            lv = x.get('school_level')
            lv = int(lv) if isinstance(lv, (int, float)) or (
                isinstance(lv, str) and str(lv).isdigit()) else None

            s = schools.get(nid)
            if s is None:
                s = schools[nid] = {
                    'ncesId': nid, 'stateId': '', 'name': '', 'district': '',
                    'leaid': '', 'level': '', 'city': '', 'zip': '',
                    'firstYear': year, 'lastYear': year,
                    'statuses': {}, 'closedStatusYear': None,
                }
            # The most recent year wins for the descriptive fields, so a school
            # is filed under the name it last carried rather than its first.
            if year >= s['lastYear']:
                s['name'] = (x.get('school_name') or s['name'] or '').strip()
                s['district'] = (x.get('lea_name') or s['district'] or '').strip()
                s['leaid'] = str(x.get('leaid') or s['leaid'] or '').strip()
                s['stateId'] = str(x.get('seasch') or x.get('school_id') or s['stateId'] or '').strip()
                s['city'] = (x.get('city_location') or s['city'] or '').strip()
                s['zip'] = str(x.get('zip_location') or s['zip'] or '').strip()
                if lv in LEVEL and LEVEL[lv]:
                    s['level'] = LEVEL[lv]
            s['firstYear'] = min(s['firstYear'], year)
            s['lastYear'] = max(s['lastYear'], year)
            if st is not None:
                s['statuses'][str(year)] = STATUS.get(st, str(st))
                if st in (2, 6) and s['closedStatusYear'] is None:
                    s['closedStatusYear'] = year

            lea = str(x.get('leaid') or '').strip()
            if lea and lea not in districts:
                districts[lea] = {
                    'leaid': lea,
                    'name': (x.get('lea_name') or '').strip(),
                    'stateId': str(x.get('state_leaid') or '').strip(),
                    'level': 'district',
                    'firstYear': year, 'lastYear': year,
                }
            elif lea:
                districts[lea]['lastYear'] = max(districts[lea]['lastYear'], year)
                districts[lea]['firstYear'] = min(districts[lea]['firstYear'], year)

    if not years_seen:
        print('no years returned any data.')
        return 1

    latest = max(years_seen)
    out_schools = []
    for s in schools.values():
        # Declared closure wins; otherwise absence from the latest census is
        # what closure means here.
        if s['closedStatusYear']:
            status, closed = 'closed', s['closedStatusYear']
        elif s['lastYear'] < latest:
            status, closed = 'closed', s['lastYear'] + 1
        else:
            status, closed = 'open', None
        out_schools.append({
            'ncesId': s['ncesId'],
            'stateId': s['stateId'],
            'name': s['name'],
            'district': s['district'],
            'leaid': s['leaid'],
            'level': s['level'],
            'status': status,
            'opened': s['firstYear'],
            'closed': closed,
            'lastSeen': s['lastYear'],
            'city': s['city'],
            'zip': s['zip'],
        })

    out = {
        'provenance': {
            'source': ('NCES Common Core of Data, public school universe, '
                       'via the Urban Institute Education Data API'),
            'url': API,
            'api_docs': 'https://educationdata.urban.org/documentation/schools.html',
            'downloaded': datetime.date.today().isoformat(),
            'derived_by': 'scripts/import/derive_schools_nces.py',
            'county': 'Los Angeles (FIPS 06037)',
            'years': [min(years_seen), max(years_seen)],
            'years_pulled': len(years_seen),
            'schools': len(out_schools),
            'districts': len(districts),
            'note': (
                'Used instead of the CDE directory, which carries the same schools with '
                'close dates but sits behind a WAF that refuses curl and browser downloads '
                'alike. The state CDS code comes through as stateId. Closure is derived from '
                'presence: CCD is a yearly census, a school does not carry a closed-in field, '
                'so a school absent from the latest year is treated as closed the year after '
                'it was last seen, and a declared status of Closed is preferred where it '
                'exists. A school that closed before %d is in none of these files: most of '
                'the schoolhouses this archive writes about closed long before then, and a '
                'miss here is not evidence of anything. Colleges are not in CCD at all; they '
                'are in IPEDS.' % min(years_seen)),
        },
        'schools': sorted(out_schools, key=lambda s: s['name']),
        'districts': sorted(districts.values(), key=lambda d: d['name']),
    }

    path = os.path.normpath(OUT)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(out, f, separators=(',', ':'), sort_keys=True)

    op = sum(1 for s in out_schools if s['status'] == 'open')
    cl = len(out_schools) - op
    print()
    print('years %d to %d (%d pulled)' % (min(years_seen), max(years_seen), len(years_seen)))
    print('schools: %d  open: %d  closed: %d' % (len(out_schools), op, cl))
    print('districts: %d' % len(districts))
    print('wrote %s  %.2f MB' % (path, os.path.getsize(path) / 1e6))
    lv = collections.Counter(s['level'] or '(none)' for s in out_schools)
    for k, v in lv.most_common():
        print('   %-12s %d' % (k, v))
    return 0


if __name__ == '__main__':
    sys.exit(main())
