#!/usr/bin/env python3
"""
The valley's water board elections, 2016 to 2024, from the County's statements
of votes cast (Nathan, 30 September 2026: "yes to water 2016 to 2024 now").

The County of Los Angeles, Registrar-Recorder/County Clerk, publishes each
general election's statement of votes cast as a zip of spreadsheets, one per
contest, precinct by precinct. These are the certified returns. Read here:

  3496  November 8, 2016   Castaic Lake Water Agency: at large, Divisions 1-3
  4193  November 3, 2020   Santa Clarita Valley Water: Divisions 1-3
  4300  November 8, 2022   Santa Clarita Valley Water: Divisions 1-3
  4324  November 5, 2024   Santa Clarita Valley Water: Divisions 1-2, and
                           Division 3 twice: a full term and a two-year term

2018 had no water contest (the agency was formed that January, its first
directors carried over by SB 634). The zips are in inventory/raw/county-svc
(ignored, 70 MB); each contest's own spreadsheet is copied to
inventory/elections/county/svc/, small enough to keep and to attach to the
document records, and every zip and spreadsheet is checksummed in
inventory/elections/county/svc-manifest.json.

Each spreadsheet: row 1 the date, row 2 the contest and "VOTE FOR: N", row 3
the columns (REGISTRATION, TYPE, BALLOTS CAST, then one per candidate), then
three rows a precinct: POLLING PLACE, VBM PORTION, TOTAL. The contest's figures
are the sums of the TOTAL rows; the script checks that polling place plus mail
equals the total for every candidate and for ballots cast, and refuses if not.

OUT: inventory/elections/county-svc-water.json
Run: scripts/import/parse_county_svc.py   (pandas and xlrd: the scratchpad venv)
"""
import hashlib, json, os, re, shutil, sys, urllib.request, zipfile
import pandas as pd

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
RAW = os.path.join(ROOT, 'inventory/raw/county-svc')
KEEP = os.path.join(ROOT, 'inventory/elections/county/svc')
MANIFEST = os.path.join(ROOT, 'inventory/elections/county/svc-manifest.json')
OUT = os.path.join(ROOT, 'inventory/elections/county-svc-water.json')
ZIPS = {
    '3496': ('2016-11-08', 'https://www.lavote.gov/documents/SVC/3496_SVC_Excel.zip', r'^CASTAIC_LAKE_WTR_ACY_(AT_LG|DIV_\d)_', 'castaic-lake-water-agency'),
    '4193': ('2020-11-03', 'https://www.lavote.gov/docs/rrcc/svc/4193_Final_SVC_Excel.zip', r'^SANTA_CLARITA_-DIVISION_(\d)_', 'santa-clarita-valley-water'),
    '4300': ('2022-11-08', 'https://content.lavote.gov/docs/rrcc/svc/4300_svc_excel.zip', r'^SANTA_CLARITA_VALLEY-D(\d)_', 'santa-clarita-valley-water'),
    '4324': ('2024-11-05', 'https://content.lavote.gov/docs/rrcc/svc/4324_final_svc_excel_v2.zip', r'^SANTA_CLARITA_(?:VALLEY-D(\d)_|WTER-D(3)_FULL_|D(3)_-_2-YEAR_)', 'santa-clarita-valley-water'),
}

# The 2016 and 2022 spreadsheets do not print "VOTE FOR". The County's Official
# Election Returns do, and mark contests "(SHARED W/VENTURA CO)": part of their
# electorate voted in Ventura County, whose figures are not in these files. Both
# returns are scans, read by OCR and by eye on 30 September 2026 and kept in
# inventory/elections/county. Names are taken from the spreadsheets, which are
# text; the returns' vote totals must equal the spreadsheet sums, or this refuses
# (the first run caught an OCR misreading of a name, which is why names are not
# compared).
OER = {
    ('3496', ''): {'seats': 1, 'ventura': True, 'votes': [43841, 35623], 'returns': 'OER-3496-11082016.pdf', 'page': 5},
    ('3496', '1'): {'seats': 1, 'ventura': False, 'votes': [14600, 9838], 'returns': 'OER-3496-11082016.pdf', 'page': 5},
    ('3496', '2'): {'seats': 1, 'ventura': False, 'votes': [13651, 12233], 'returns': 'OER-3496-11082016.pdf', 'page': 6},
    ('3496', '3'): {'seats': 1, 'ventura': True, 'votes': [14116, 12004], 'returns': 'OER-3496-11082016.pdf', 'page': 6},
    ('4193', '1'): {'seats': 2, 'ventura': True, 'votes': [19135, 15643, 12127, 11122], 'returns': 'OER-4193-11032020.pdf', 'page': 18},
    ('4193', '2'): {'seats': 2, 'ventura': True, 'votes': [22703, 20417, 12725, 11074], 'returns': 'OER-4193-11032020.pdf', 'page': 18},
    ('4193', '3'): {'seats': 2, 'ventura': True, 'votes': [16883, 14382, 14271, 13632], 'returns': 'OER-4193-11032020.pdf', 'page': 18},
    ('4300', '1'): {'seats': 1, 'ventura': False, 'votes': [15251, 4743, 3877], 'returns': 'OER-4300-11082022.pdf', 'page': 28},
    ('4300', '2'): {'seats': 1, 'ventura': False, 'votes': [15967, 8949, 3820], 'returns': 'OER-4300-11082022.pdf', 'page': 27},
    ('4300', '3'): {'seats': 1, 'ventura': True, 'votes': [13633, 12846], 'returns': 'OER-4300-11082022.pdf', 'page': 27},
}
RETURNS = {'OER-4193-11032020.pdf': 'https://www.lavote.gov/docs/rrcc/official-returns/OER-4193-11032020.pdf',
           'OER-3496-11082016.pdf': 'https://www.lavote.gov/docs/rrcc/official-returns/OER-3496-11082016.pdf',
           'OER-4300-11082022.pdf': 'https://www.lavote.gov/docs/rrcc/official-returns/OER-4300-11082022.pdf'}

def sha(p):
    h = hashlib.sha256()
    with open(p, 'rb') as f:
        for b in iter(lambda: f.read(1 << 20), b''): h.update(b)
    return h.hexdigest()

def num(v):
    s = str(v).strip()
    return int(float(s)) if s not in ('', 'nan') else 0

def main():
    os.makedirs(RAW, exist_ok=True); os.makedirs(KEEP, exist_ok=True)
    man = json.load(open(MANIFEST)) if os.path.exists(MANIFEST) else {'by': 'County of Los Angeles, Registrar-Recorder/County Clerk', 'zips': {}, 'contests': {}}
    contests = []
    for eid, (date, url, pat, body) in ZIPS.items():
        zp = os.path.join(RAW, os.path.basename(url))
        if not os.path.exists(zp):
            req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
            with urllib.request.urlopen(req, timeout=300) as r, open(zp, 'wb') as f: shutil.copyfileobj(r, f)
        d = sha(zp)
        if eid in man['zips'] and man['zips'][eid]['sha256'] != d: sys.exit(f'REFUSING: {zp} differs from the manifest')
        man['zips'][eid] = {'url': url, 'sha256': d, 'bytes': os.path.getsize(zp), 'date': date}
        with zipfile.ZipFile(zp) as z:
            names = [n for n in z.namelist() if re.search(pat, os.path.basename(n))]
            if not names: sys.exit(f'REFUSING: no water contest in {eid}')
            for n in sorted(names):
                base = os.path.basename(n); keep = os.path.join(KEEP, base)
                with z.open(n) as src, open(keep, 'wb') as dst: shutil.copyfileobj(src, dst)
                man['contests'][base] = {'zip': eid, 'sha256': sha(keep), 'bytes': os.path.getsize(keep)}
                df = pd.read_excel(keep, header=None, dtype=str).fillna('')
                title = df.iat[1, 0].strip(); votefor = re.search(r'VOTE FOR:\s*(\d+)', ' '.join(df.iloc[1].tolist()))
                g = [x for x in re.search(pat, base).groups() if x]; m0 = g[0]; div0 = '' if m0 == 'AT_LG' else re.sub(r'\D', '', m0)
                term = 'two-year' if '2-YEAR' in base else 'full'
                # 2024's files print the number to elect in an unlabelled cell of row 2
                # (2 for Divisions 1 and 2, 1 for each Division 3 contest); the 2024
                # returns are not yet published, so it is checked against the agency's
                # own list of directors in import_water_boards.php instead.
                if not votefor and eid == '4324':
                    cell = str(df.iat[1, 4]).strip()
                    if not re.fullmatch(r'\d', cell): sys.exit(f'REFUSING: {base}: no number to elect')
                    votefor = re.match(r'(\d)', cell)
                oer = OER.get((eid, div0))
                if not votefor and not oer: sys.exit(f'REFUSING: {base} has no VOTE FOR')
                hdr = [h.strip() for h in df.iloc[2].tolist()]
                ti, ri, bi = hdr.index('TYPE'), hdr.index('REGISTRATION'), hdr.index('BALLOTS CAST')
                cand = [(i, h) for i, h in enumerate(hdr) if i > bi and h]
                rows = df.iloc[3:]
                tot = rows[rows[ti].str.strip() == 'TOTAL']; pp = rows[rows[ti].str.strip() == 'POLLING PLACE']; vbm = rows[rows[ti].str.strip() == 'VBM PORTION']
                for i, h in [(bi, 'BALLOTS CAST')] + cand:
                    if sum(num(v) for v in tot[i]) != sum(num(v) for v in pp[i]) + sum(num(v) for v in vbm[i]):
                        sys.exit(f'REFUSING: {base} {h}: polling place plus mail is not the total')
                div = div0
                sums = {h: sum(num(v) for v in tot[i]) for i, h in cand}
                # Where the returns exist their totals are the certified figures. The
                # 2022 spreadsheets are the County's PUBLIC versions and differ from the
                # returns by a vote or two (masking, it seems, of the smallest precincts):
                # paired by rank, never more than five apart, and the difference kept.
                diffs = {}
                if oer:
                    ranked = sorted(sums.items(), key=lambda kv: -kv[1]); cert = sorted(oer['votes'], reverse=True)
                    if len(ranked) != len(cert) or any(abs(v - c) > 5 for (_, v), c in zip(ranked, cert)): sys.exit(f'REFUSING: {base}: the spreadsheet {sums} is not the returns {oer["votes"]}')
                    diffs = {h: v for (h, v), c in zip(ranked, cert) if v != c}
                    sums = {h: c for (h, _), c in zip(ranked, cert)}
                if votefor and oer and int(votefor.group(1)) != oer['seats']: sys.exit(f'REFUSING: {base}: VOTE FOR disagrees with the returns')
                contests.append({'body': body, 'date': date, 'election': eid, 'division': div, 'contest': title, 'seats': int(votefor.group(1)) if votefor else oer['seats'],
                                 'seatsFrom': (oer['returns'] + ', page ' + str(oer['page'])) if oer else ('the spreadsheet, row 2 (unlabelled)' if eid == '4324' else 'the spreadsheet, "VOTE FOR"'), 'term': term, 'sharedWithVentura': bool(oer and oer['ventura']),
                                 'registered': sum(num(v) for v in tot[ri]), 'ballots': sum(num(v) for v in tot[bi]), 'precincts': len(tot),
                                 'candidates': sorted([{'name': h, 'votes': sums[h], **({'spreadsheetVotes': diffs[h]} if h in diffs else {})} for i, h in cand], key=lambda c: -c['votes']),
                                 'votesFrom': 'returns' if oer else 'spreadsheet',
                                 'file': base})
    man['returns'] = {f: {'url': u, 'sha256': sha(os.path.join(ROOT, 'inventory/elections/county', f))} for f, u in RETURNS.items()}
    man['checked'] = '2026-09-30'
    json.dump(man, open(MANIFEST, 'w'), indent=1)
    json.dump({'source': 'County of Los Angeles, Registrar-Recorder/County Clerk, statements of votes cast by precinct', 'contests': contests}, open(OUT, 'w'), indent=1)
    for c in contests:
        top = c['candidates'][:c['seats']]
        print(f"{c['date']} {c['body'][:14]:14s} {('Div ' + c['division']) if c['division'] else 'at large':8s} vote for {c['seats']}  reg {c['registered']:>7,} ballots {c['ballots']:>7,} ({c['ballots'] / c['registered'] * 100 if c['registered'] else 0:4.1f}%)  "
              + '; '.join(f"{x['name']} {x['votes']:,}" for x in c['candidates']))

if __name__ == '__main__':
    main()
