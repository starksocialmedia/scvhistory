#!/usr/bin/env python3
"""
The California Elections Data Archive (CEDA), read for the Santa Clarita Valley:
the City Council and the five school districts, 1995 to 2024.

CEDA is compiled each year by the Center for California Studies and the
Institute for Social Research at Sacramento State with the Secretary of State,
from the counties' returns. It is a compilation, not a certified return (Nathan,
29 September 2026): it is cited as such, and it lifts an outcome to "roster",
never to "certified".

THE FILES. The yearly candidate spreadsheets, unmodified, as downloaded from the
Sac State portal and kept in the repository github.com/justindbk/ceda (de
Benedictis-Kessner and Bernhard), pinned to commit 38705c7. The portal itself
(scholars.csus.edu) is a script-driven page this cannot download from. The
repository's corrections live in a separate R script and are not applied here;
none of them touches these bodies (checked 29 September 2026). The files are in
inventory/raw/ceda/ (ignored: 45 MB of statewide data); each is checked against
the SHA-256 in inventory/elections/ceda-manifest.json, written on first fetch.

OUT: inventory/elections/ceda-scv.json, one row per candidate per contest:
  body, year, date, office, area, term, seats (VOTE#), first, last, votes,
  total, elected (CEDA's own ELECTED field), incumbent, designation, file, row.

Run: scripts/import/parse_ceda.py   (needs pandas, openpyxl and xlrd; the
scratchpad venv has them)
"""
import hashlib, json, os, re, sys, urllib.request
import pandas as pd

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
RAW = os.path.join(ROOT, 'inventory/raw/ceda')
MANIFEST = os.path.join(ROOT, 'inventory/elections/ceda-manifest.json')
OUT = os.path.join(ROOT, 'inventory/elections/ceda-scv.json')
COMMIT = '38705c786417216d18272a0d4f10d33cdfe7c1d9'
URL = 'https://raw.githubusercontent.com/justindbk/ceda/' + COMMIT + '/{}'

# CEDA's PLACE, normalised, to the body. LA County only (CO 19).
BODIES = [
    (r'^santa clarita$', 'city-of-santa-clarita'),
    (r'^william s\.? hart union high', 'william-s-hart-union-high-school-district'),
    (r'^saugus union', 'saugus-union-school-district'),
    (r'^newhall( elementary)?$', 'newhall-school-district'),
    (r'^sulphur springs union', 'sulphur-springs-union-school-district'),
    (r'^castaic union', 'castaic-union-school-district'),
]

def sha(p):
    h = hashlib.sha256()
    with open(p, 'rb') as f:
        for b in iter(lambda: f.read(1 << 20), b''): h.update(b)
    return h.hexdigest()

def main():
    os.makedirs(RAW, exist_ok=True)
    man = json.load(open(MANIFEST)) if os.path.exists(MANIFEST) else {'source': 'https://github.com/justindbk/ceda', 'commit': COMMIT, 'files': {}}
    rows, seen_places = [], {}
    for y in range(1995, 2025):
        name = f'CEDA{y}Data.xls' + ('x' if y >= 2011 else '')
        path = os.path.join(RAW, name)
        if not os.path.exists(path):
            urllib.request.urlretrieve(URL.format(name), path)
        digest = sha(path)
        rec = man['files'].get(name)
        if rec and rec['sha256'] != digest:
            sys.exit(f'REFUSING: {name} differs from the copy recorded in the manifest')
        man['files'][name] = {'url': URL.format(name), 'sha256': digest, 'bytes': os.path.getsize(path)}
        sheets = pd.read_excel(path, sheet_name=None, dtype=str)
        cand = [s for s in sheets if re.search('cand', s, re.I)]
        if len(cand) != 1: sys.exit(f'REFUSING: {name} has {len(cand)} candidate sheets')
        df = sheets[cand[0]].fillna('')
        col = {c.upper().strip(): c for c in df.columns}
        g = lambda r, *ks: next((str(r[col[k]]).strip() for k in ks if k in col), '')
        for i, r in df.iterrows():
            co = g(r, 'CO', 'CO#').split('.')[0]
            if co != '19': continue
            place = re.sub(r'\s+', ' ', g(r, 'PLACE').lower()).strip()
            body = next((b for pat, b in BODIES if re.search(pat, place)), None)
            if not body:
                if re.search(r'clarita|hart|saugus|newhall|sulphur|castaic|water', place): seen_places[place] = seen_places.get(place, 0) + 1
                continue
            office = g(r, 'OFFICE')
            if body == 'city-of-santa-clarita' and not re.search('council', office, re.I): continue
            first = g(r, 'FIRST', 'FIRSTNAME'); last = g(r, 'LAST', 'LASTNAME')
            el = g(r, 'ELECTED').lower()
            elected = el in ('1', '1.0', 'yes', 'y')
            if el not in ('1', '1.0', '2', '2.0', 'yes', 'no', 'y', 'n'): sys.exit(f'REFUSING: {name} row {i}: ELECTED is "{el}"')
            num = lambda s: int(float(s)) if s not in ('', 'nan') else None
            rows.append({'body': body, 'year': y, 'date': g(r, 'DATE')[:10], 'office': office, 'area': g(r, 'AREA').split('.')[0],
                         'term': g(r, 'TERM'), 'seats': num(g(r, 'VOTE#')), 'first': first, 'last': last,
                         'votes': num(g(r, 'VOTES')), 'total': num(g(r, 'TOTVOTES')), 'elected': elected,
                         'incumbent': g(r, 'INCUMB', 'INC').lower() in ('y', 'yes', '1'), 'designation': g(r, 'BALDESIG'),
                         'file': name, 'row': int(i) + 2})
    man['checked'] = '2026-09-29'
    json.dump(man, open(MANIFEST, 'w'), indent=1)
    json.dump({'source': 'California Elections Data Archive (CEDA), Center for California Studies and Institute for Social Research, California State University, Sacramento, with the California Secretary of State; yearly candidate files, via github.com/justindbk/ceda at ' + COMMIT[:7],
               'rows': rows}, open(OUT, 'w'), indent=1, ensure_ascii=False)
    by = {}
    for r in rows: by.setdefault(r['body'], set()).add(r['date'])
    for b, d in sorted(by.items()): print(f'{b:45s} {len(d):3d} dates, {sum(1 for r in rows if r["body"] == b):4d} rows, {min(d)} to {max(d)}')
    if seen_places: print('LA County places not taken (check none is ours):', json.dumps(seen_places))

if __name__ == '__main__':
    main()
