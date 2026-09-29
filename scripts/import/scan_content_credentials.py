#!/usr/bin/env python3
"""
Content credentials and generative-AI markers, looked for in the files as they
arrived, not as Craft stores them.

Craft re-encodes an image on upload (the Hart portrait arrived a progressive
JPEG and is stored a baseline one, different bytes), so a C2PA manifest or an
IPTC digital-source-type in the file that arrived is not in the file stored.
Scanning web/uploads/ proves nothing. This scans the originals:

  inventory/incoming/*        every file waiting to come in, always
  the mirror                  every asset whose legacySourcePath names a mirror
                              file, when Reggie is mounted at /Volumes/Reggie
  the volume copy             for the assets with no known original, reported
                              as such: a clean result there is not evidence

What counts as a marker, in the metadata exiftool reads:
  a JUMBF or C2PA block (a content-credentials manifest, embedded)
  XMP dcterms:provenance pointing at a manifest (a manifest held elsewhere;
    this is how the Firefly file, Santa-Clarita-City-Hall.png, carries it)
  IPTC DigitalSourceType trainedAlgorithmicMedia or
    compositeWithTrainedAlgorithmicMedia (the IPTC term for generated media)
  a generator named in Software, CreatorTool or a PNG text chunk
A manifest held elsewhere is fetched (one request every two seconds) and its
declared source type reported: generated, generated in part, or neither.
An Adobe manifest alone only means Adobe software touched the file.
    (Firefly, DALL-E, Midjourney, Stable Diffusion, Imagen, and so on)
A raw byte search for the same strings runs as well, and a byte hit that
exiftool does not confirm is reported separately, never counted as a finding.

The WordPress originals (85 files) are on the WordPress host. They are not
fetched unless --wordpress is given, because that is a request to an outside
host and Nathan decides it. One request every two seconds.

Needs storage/runtime/asset_origins.json from export_asset_origins.php.
Writes storage/runtime/content_credentials.json. Reads only.

  python3 scripts/import/scan_content_credentials.py [--wordpress]
"""
import json
import os
import re
import subprocess
import sys
import tempfile
import time
import urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'
GENERATORS = r'\b(?:firefly|dall[- ]?e|midjourney|stable ?diffusion|imagen|openai|chatgpt|gpt-4o|leonardo\.ai|ideogram|flux\.1|nano ?banana|gemini)\b'
TAG = re.compile(r'jumbf|c2pa|trainedalgorithmicmedia|' + GENERATORS, re.I)
BYTES = re.compile(rb'c2pa|jumb|trainedAlgorithmicMedia|' + GENERATORS.encode(), re.I)


def read_manifest(url):
    """Fetch a manifest held elsewhere and say what it declares about the image."""
    time.sleep(2)
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'SCVHistory archive provenance check'})
        m = urllib.request.urlopen(req, timeout=30).read()
    except Exception as e:
        return f'could not fetch {url}: {e}'
    if b'compositeWithTrainedAlgorithmicMedia' in m:
        kind = 'GENERATED IN PART (compositeWithTrainedAlgorithmicMedia)'
    elif b'trainedAlgorithmicMedia' in m:
        kind = 'GENERATED (trainedAlgorithmicMedia)'
    else:
        kind = 'no generative source type declared'
    agents = sorted({a.decode('latin-1') for a in re.findall(rb'(Adobe Firefly|Adobe Photoshop|Lightroom|DALL-E|OpenAI|Midjourney)', m)})
    ops = sorted({o.decode('latin-1') for o in re.findall(rb'text_to_image|generative_fill|generative_expand', m)})
    return kind + (', by ' + ', '.join(agents) if agents else '') + (', ' + ', '.join(ops) if ops else '')


def scan(path):
    """Return (confirmed markers, unconfirmed byte hits)."""
    out = subprocess.run(['exiftool', '-a', '-G1', '-s', '-api', 'LargeFileSupport=1', path],
                         capture_output=True, text=True, errors='replace').stdout
    found = []
    for line in out.splitlines():
        if line.startswith(('[System]', '[ExifTool]', '[File]')):
            continue
        if TAG.search(line) or ('Provenance' in line and 'manifest' in line.lower()):
            found.append(re.sub(r'\s+', ' ', line.strip())[:160])
    for url in sorted(set(re.findall(r'https://cai-manifests\.adobe\.com/manifests/[\w-]+', out))):
        found.append('manifest says: ' + read_manifest(url))
    with open(path, 'rb') as fh:
        raw = fh.read()
    hits = sorted({m.group(0).decode('latin-1').lower() for m in BYTES.finditer(raw)})
    return found, ([] if found else hits)


def main():
    fetch_wp = '--wordpress' in sys.argv
    origins = json.load(open(os.path.join(ROOT, 'storage/runtime/asset_origins.json')))
    mirror = os.path.isdir(MIRROR)
    report = {'incoming': [], 'assets': [], 'notScanned': [], 'byteOnly': []}
    tally = {}

    def record(bucket, key, path, how):
        found, hits = scan(path)
        row = {'key': key, 'path': path, 'scanned': how, 'markers': found}
        report[bucket].append(row)
        tally[how] = tally.get(how, 0) + 1
        if found:
            print(f'MARKER  {key}  ({how})')
            for f in found:
                print(f'          {f}')
        if hits:
            report['byteOnly'].append({'key': key, 'path': path, 'bytes': hits})

    inc = os.path.join(ROOT, 'inventory/incoming')
    for fn in sorted(os.listdir(inc)) if os.path.isdir(inc) else []:
        if not fn.startswith('.'):
            record('incoming', 'incoming/' + fn, os.path.join(inc, fn), 'incoming original')

    for a in origins:
        key = f"#{a['id']} {a['volumeFile'].split('archive-media/', 1)[-1]}"
        if a['origin'] == 'mirror':
            if not mirror:
                report['notScanned'].append({'key': key, 'why': 'Reggie not mounted'})
                continue
            p = os.path.join(MIRROR, a['original'].lstrip('/'))
            if os.path.isfile(p):
                record('assets', key, p, 'mirror original')
            else:
                report['notScanned'].append({'key': key, 'why': 'not on the mirror at ' + a['original']})
        elif a['origin'] == 'wordpress' and fetch_wp:
            with tempfile.NamedTemporaryFile(suffix=os.path.splitext(a['original'])[1]) as t:
                try:
                    time.sleep(2)  # rate limit: one request every two seconds
                    req = urllib.request.Request(a['original'], headers={'User-Agent': 'SCVHistory archive provenance check'})
                    t.write(urllib.request.urlopen(req, timeout=60).read())
                    t.flush()
                    record('assets', key, t.name, 'wordpress original')
                except Exception as e:
                    report['notScanned'].append({'key': key, 'why': f'fetch failed: {e}'})
        else:
            p = os.path.join(ROOT, a['volumeFile'])
            why = 'wordpress original not fetched (--wordpress)' if a['origin'] == 'wordpress' else 'no original known'
            if os.path.isfile(p):
                record('assets', key, p, 'volume copy only: ' + why)
            else:
                report['notScanned'].append({'key': key, 'why': 'volume file missing: ' + a['volumeFile']})

    json.dump(report, open(os.path.join(ROOT, 'storage/runtime/content_credentials.json'), 'w'), indent=1)
    marked = [r['key'] for b in ('incoming', 'assets') for r in report[b] if r['markers']]
    print()
    print('scanned by source:', json.dumps(tally))
    why = {}
    for x in report['notScanned']:
        k = 'not on the mirror' if x['why'].startswith('not on the mirror') else x['why']
        why[k] = why.get(k, 0) + 1
    print('not scanned:', len(report['notScanned']), json.dumps(why))
    print('byte hits exiftool did not confirm:', len(report['byteOnly']), [x['key'] + ' ' + ','.join(x['bytes']) for x in report['byteOnly']][:20])
    print('FILES WITH MARKERS:', len(marked), marked)
    if not mirror:
        print('Reggie is not mounted at /Volumes/Reggie: the mirror originals were not read, so this is not yet an answer for them.')


if __name__ == '__main__':
    main()
