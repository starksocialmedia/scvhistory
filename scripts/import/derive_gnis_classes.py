#!/usr/bin/env python3
"""
Derives inventory/legacy/gnis-classes.json from the USGS California gazetteer.

WHAT THIS IS FOR

placeType wants a default, and where a place carries a GNIS id the federal
gazetteer's own feature class is a better answer than anything we would guess
from the name. The class is not something we hold: gnisId stores the number
alone. This turns the download into the lookup table that add_place_type_field
reads.

WHAT IS COMMITTED

Only the derived JSON. The download is 8 MB of pipe-delimited text carrying
coordinates in two formats, map sheet names and Board on Geographic Names
decision columns, none of which this project has a use for. It is reproducible
from the URL in the provenance block, so committing it would be storing
somebody else's file to avoid typing a curl command.

The derived file keeps feature_id, feature_name, feature_class and county, for
every named feature in California rather than only the four we can use today.
The four will become more as places are created from the review screen, and
re-running this against a moving federal dataset to answer the fifth lookup
would mean the fifth answer came from a different edition than the first four.

USAGE

    curl -o /tmp/gnis.zip \\
      https://prd-tnm.s3.amazonaws.com/StagedProducts/GeographicNames/DomesticNames/DomesticNames_CA_Text.zip
    unzip -o /tmp/gnis.zip -d /tmp/gnis
    python3 scripts/import/derive_gnis_classes.py /tmp/gnis/Text/DomesticNames_CA.txt

Writes inventory/legacy/gnis-classes.json and prints what changed.
"""

import csv
import datetime
import json
import os
import sys
import collections

URL = ('https://prd-tnm.s3.amazonaws.com/StagedProducts/GeographicNames/'
       'DomesticNames/DomesticNames_CA_Text.zip')

OUT = os.path.join(os.path.dirname(__file__), '..', '..',
                   'inventory', 'legacy', 'gnis-classes.json')


def main(src: str) -> int:
    if not os.path.exists(src):
        print('no such file: ' + src)
        return 1

    rows = {}
    classes = collections.Counter()

    # utf-8-sig: the USGS file carries a byte order mark, and without this the
    # first column name arrives as "﻿feature_id" and every lookup misses.
    with open(src, encoding='utf-8-sig') as f:
        for x in csv.DictReader(f, delimiter='|'):
            fid = (x.get('feature_id') or '').strip()
            if not fid:
                continue
            cls = (x.get('feature_class') or '').strip()
            rows[fid] = {
                'name': (x.get('feature_name') or '').strip(),
                'class': cls,
                'county': (x.get('county_name') or '').strip(),
            }
            classes[cls] += 1

    out = {
        'provenance': {
            'source': 'USGS Geographic Names Information System, Domestic Names, California',
            'url': URL,
            'downloaded': datetime.date.today().isoformat(),
            'file': os.path.basename(src),
            'derived_by': 'scripts/import/derive_gnis_classes.py',
            'features': len(rows),
            'note': ('feature_id, feature_name, feature_class and county only. The '
                     'download is not committed: it is 8 MB of coordinates, map sheets '
                     'and BGN decision columns this project has no use for, and it is '
                     'reproducible from the url above.'),
        },
        'classes': rows,
    }

    path = os.path.normpath(OUT)
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(out, f, separators=(',', ':'), sort_keys=True)

    print('features: %d, distinct classes: %d' % (len(rows), len(classes)))
    print('wrote %s  %.1f MB' % (path, os.path.getsize(path) / 1e6))
    for cls, n in classes.most_common(10):
        print('   %-18s %d' % (cls, n))
    return 0


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(__doc__)
        sys.exit(1)
    sys.exit(main(sys.argv[1]))
