#!/usr/bin/env python3
"""
Derives inventory/legacy/authorities/nonprofits-scv.json from the IRS Exempt
Organizations Business Master File for California.

WHAT THIS IS FOR

Half the organizations the corpus names are nonprofits: the historical society,
the chamber, the Boys and Girls Club, the church that held the first school.
When one is approved from the review screen it should arrive with its legal name
and its EIN rather than with the spelling an article used in 1987.

THE FILTER, AND WHY IT IS BY PLACE AND NOT BY NAME

The California file is 35 MB and around 200,000 organizations. Filtering it by
name against the corpus would find the ones we already know about and miss
every one we do not, which is backwards: the point of an authority table is to
know more than the corpus does. So it filters by place, on city and on ZIP, and
keeps everything in the valley whether the corpus mentions it or not.

The city list is the valley's own names plus the ones the post office uses for
it. The ZIP list matters more than the city list, because a Santa Clarita
organization files under half a dozen city names, and because an organization
in Valencia may write its city as Santa Clarita, Valencia or neither.

Piru, Gorman and Lebec are included deliberately. Piru is in Ventura County and
Gorman and Lebec are in Kern, and all three are in the archive's territory: the
Rancho Camulos pieces, the Ridge Route pieces and the Fort Tejon pieces are
about places the county line runs through.

WHAT IS KEPT

EIN, name, sort name, city, ZIP, NTEE code and the subsection, which is the
paragraph of 501(c) an organization is exempt under and is how a church is told
from a chamber of commerce. The financial columns are dropped: this is an
identity table, and an archive has no use for an organization's 1987 revenue.

USAGE

    curl -o /tmp/eo_ca.csv https://www.irs.gov/pub/irs-soi/eo_ca.csv
    python3 scripts/import/derive_nonprofits_scv.py /tmp/eo_ca.csv
"""

import csv
import datetime
import json
import os
import sys
import collections

URL = 'https://www.irs.gov/pub/irs-soi/eo_ca.csv'
OUT = os.path.join(os.path.dirname(__file__), '..', '..',
                   'inventory', 'legacy', 'authorities', 'nonprofits-scv.json')

CITIES = {
    'santa clarita', 'newhall', 'saugus', 'valencia', 'canyon country',
    'castaic', 'stevenson ranch', 'agua dulce', 'acton', 'val verde',
    'lake hughes', 'green valley', 'elizabeth lake', 'leona valley',
    'piru', 'gorman', 'lebec', 'ravenna', 'sand canyon', 'placerita canyon',
    'bouquet canyon', 'forrest park', 'mint canyon', 'solemint',
}

ZIPS = {
    '91321',                                # Newhall
    '91350', '91351', '91387',              # Canyon Country / Saugus
    '91354', '91355',                       # Valencia
    '91381',                                # Stevenson Ranch
    '91384',                                # Castaic, Val Verde
    '91390',                                # Saugus, Agua Dulce
    '91310',                                # Castaic
    '91322', '91380', '91383', '91385', '91386',   # Santa Clarita PO boxes
    '93510',                                # Acton
    '93532',                                # Lake Hughes
    # 93551/93552 are deliberately NOT here. They cover Leona Valley, which is
    # in the archive's territory, and also most of west Palmdale, which is not.
    # Including them brought in 361 Palmdale organizations against a table of
    # 1,822, so Leona Valley is kept by city name alone and the handful filed
    # under a Palmdale ZIP is a miss worth taking.
    '93040',                                # Piru
    '93243',                                # Gorman, Lebec
}

# 501(c) subsection to something a person can read.
SUBSECTION = {
    '03': 'charitable, religious or educational (501(c)(3))',
    '04': 'social welfare (501(c)(4))',
    '05': 'labor or agricultural (501(c)(5))',
    '06': 'business league or chamber (501(c)(6))',
    '07': 'social or recreational club (501(c)(7))',
    '08': 'fraternal beneficiary society (501(c)(8))',
    '10': 'domestic fraternal society (501(c)(10))',
    '19': "veterans' organization (501(c)(19))",
}

# NTEE letter to the archive's orgType, where it maps cleanly.
NTEE_TO_ORGTYPE = {
    'A': 'nonprofit',   # arts, culture, humanities
    'B': 'school',      # education
    'C': 'nonprofit', 'D': 'nonprofit', 'E': 'nonprofit', 'F': 'nonprofit',
    'G': 'nonprofit', 'H': 'nonprofit', 'I': 'nonprofit', 'J': 'nonprofit',
    'K': 'nonprofit', 'L': 'nonprofit', 'M': 'nonprofit', 'N': 'club',
    'O': 'nonprofit', 'P': 'nonprofit', 'Q': 'nonprofit', 'R': 'nonprofit',
    'S': 'nonprofit', 'T': 'nonprofit', 'U': 'nonprofit', 'V': 'nonprofit',
    'W': 'government', 'X': 'church', 'Y': 'nonprofit', 'Z': 'nonprofit',
}


def main(src: str) -> int:
    if not os.path.exists(src):
        print('no such file: ' + src)
        return 1

    rows = []
    seen_cities = collections.Counter()
    total = 0

    with open(src, encoding='utf-8', errors='replace') as f:
        for x in csv.DictReader(f):
            total += 1
            city = (x.get('CITY') or '').strip().lower()
            zip5 = (x.get('ZIP') or '').strip()[:5]
            if city not in CITIES and zip5 not in ZIPS:
                continue

            ntee = (x.get('NTEE_CD') or '').strip()
            sub = (x.get('SUBSECTION') or '').strip()
            org_type = NTEE_TO_ORGTYPE.get(ntee[:1].upper(), '') if ntee else ''
            # A church is a church whatever its NTEE letter says.
            if sub == '03' and ntee[:1].upper() == 'X':
                org_type = 'church'
            if sub == '06':
                org_type = 'nonprofit'

            rows.append({
                'ein': (x.get('EIN') or '').strip(),
                'name': (x.get('NAME') or '').strip(),
                'sortName': (x.get('SORT_NAME') or '').strip(),
                'city': (x.get('CITY') or '').strip(),
                'zip': zip5,
                'ntee': ntee,
                'subsection': sub,
                'subsectionLabel': SUBSECTION.get(sub, ''),
                'orgType': org_type,
                'ruling': (x.get('RULING') or '').strip(),
            })
            seen_cities[(x.get('CITY') or '').strip()] += 1

    out = {
        'provenance': {
            'source': 'IRS Exempt Organizations Business Master File, California',
            'url': URL,
            'downloaded': datetime.date.today().isoformat(),
            'derived_by': 'scripts/import/derive_nonprofits_scv.py',
            'rows_in_source': total,
            'rows_kept': len(rows),
            'filter': 'city or ZIP in the Santa Clarita Valley and its edges',
            'cities': sorted(CITIES),
            'zips': sorted(ZIPS),
            'note': (
                'Filtered by place rather than by name, so the table knows more than the '
                'corpus does. Piru, Gorman and Lebec are in Ventura and Kern counties and are '
                'included on purpose: the Camulos, Ridge Route and Fort Tejon material is '
                'about places the county line runs through. Financial columns are dropped; '
                'this is an identity table. An organization absent from it may simply never '
                'have been exempt, or may have dissolved before the BMF began.'),
        },
        'organizations': sorted(rows, key=lambda r: r['name']),
    }

    path = os.path.normpath(OUT)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(out, f, separators=(',', ':'), sort_keys=True)

    print('rows in the California file: %d' % total)
    print('kept for the valley:         %d' % len(rows))
    print('wrote %s  %.2f MB' % (path, os.path.getsize(path) / 1e6))
    print()
    print('by city:')
    for c, n in seen_cities.most_common(12):
        print('   %-22s %d' % (c, n))
    print()
    ot = collections.Counter(r['orgType'] or '(none)' for r in rows)
    print('mapped orgType:')
    for k, v in ot.most_common():
        print('   %-12s %d' % (k, v))
    return 0


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(__doc__)
        sys.exit(1)
    sys.exit(main(sys.argv[1]))
