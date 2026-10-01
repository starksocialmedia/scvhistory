#!/usr/bin/env python3
"""
Enrolment, year by year, for every school and district record with an NCES id,
from the NCES Common Core of Data through the Urban Institute's Education Data
Portal (the same service build_school_map.py reads the directory from).

  schools    /api/v1/schools/ccd/enrollment/summaries?...&ncessch={id}
  districts  /api/v1/school-districts/ccd/enrollment/summaries?...&leaid={id}

One request per record, every year at once, filtered to grade 99, race 99 and
sex 99, which is the total. (Year-by-year requests, 418 of them, drew the
portal's rate limit on 1 October 2026; answers are cached in
inventory/raw/nces-cache so a retry asks only for what it lacks.) "year" is the fall of the school
year: 2022 is 2022-23. Negative values are the portal's codes for missing or
not applicable and are left out, never stored as a count.

The ids are passed on the command line as section-id=ncesid pairs, read from the
archive by import_nces_enrolment.php's dry run, so this script has no list of
its own to drift. OUT: inventory/schools/nces-enrolment.json
Run: scripts/import/fetch_nces_enrolment.py 16052=064251006959 21588=0642510 ...
"""
import json, os, re, sys, time, urllib.request
from concurrent.futures import ThreadPoolExecutor

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
OUT = os.path.join(ROOT, 'inventory/schools/nces-enrolment.json')
YEARS = range(1986, 2024)
BASE = 'https://educationdata.urban.org/api/v1'

CACHE = os.path.join(ROOT, 'inventory/raw/nces-cache')
# Checked against figures read one year at a time on 1 October 2026 before the
# portal's rate limit closed: Hart High School, 1987 2,030; 2022 2,034; 2023 1,930.
CHECK = {'064251006959': {1987: 2030, 2022: 2034, 2023: 1930}}

def get(url):
    """One cached request: an answer is kept, so a retry after a rate limit
    fetches only what it has not got."""
    os.makedirs(CACHE, exist_ok=True)
    key = os.path.join(CACHE, re.sub(r'[^A-Za-z0-9]+', '_', url)[-180:] + '.json')
    if os.path.exists(key): return json.load(open(key))
    req = urllib.request.Request(url, headers={'User-Agent': 'SCVHistory archive enrolment'})
    data = json.load(urllib.request.urlopen(req, timeout=120))
    json.dump(data, open(key, 'w')); time.sleep(2)
    return data

def series(nces):
    """Every year in one request: the portal's summary endpoint, filtered to the
    total (grade 99, race 99, sex 99) so nothing is summed twice."""
    if len(nces) == 12:
        url = f'{BASE}/schools/ccd/enrollment/summaries?var=enrollment&stat=sum&by=ncessch&ncessch={nces}&grade=99&race=99&sex=99'
    else:
        url = f'{BASE}/school-districts/ccd/enrollment/summaries?var=enrollment&stat=sum&by=leaid&leaid={nces}&grade=99&race=99&sex=99'
    out = {}
    for r in get(url).get('results', []):
        y, n = r.get('year'), r.get('enrollment')
        if isinstance(y, int) and isinstance(n, (int, float)) and n >= 0: out[y] = int(n)
    return out, url

def main():
    pairs = [a.split('=') for a in sys.argv[1:]]
    if not pairs: sys.exit('usage: fetch_nces_enrolment.py id=ncesid ...')
    out = {r: {'nces': n, 'kind': 'school' if len(n) == 12 else 'district', 'years': {}} for r, n in pairs}
    for r, n in pairs:
        ys, url = series(n)
        for y, c in CHECK.get(n, {}).items():
            if ys.get(y) != c: sys.exit(f'REFUSING: {n} {y} reads {ys.get(y)}, expected {c}: the summary is not the total')
        out[r]['years'] = {str(y): {'count': c, 'url': url} for y, c in sorted(ys.items()) if y in YEARS}
    os.makedirs(os.path.dirname(OUT), exist_ok=True)
    json.dump({'source': 'NCES Common Core of Data, via the Urban Institute Education Data Portal (educationdata.urban.org)', 'fetched': time.strftime('%Y-%m-%d'),
               'yearMeans': 'the fall of the school year: 2022 is 2022-23', 'records': out}, open(OUT, 'w'), indent=1)
    for r, d in out.items():
        ys = sorted(d['years'], key=int)
        print(f"#{r} {d['nces']:13s} {len(ys):2d} years" + (f", {ys[0]} to {ys[-1]}, latest {d['years'][ys[-1]]['count']:,}" if ys else ''))

if __name__ == '__main__':
    main()
