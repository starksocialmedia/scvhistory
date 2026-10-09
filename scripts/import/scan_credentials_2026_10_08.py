#!/usr/bin/env python3
"""
The overnight credential scan (Nathan, 8 October 2026, overnight run, items 1 to 3): content credentials and generative
markers read from the files themselves, for the assets in storage/runtime/overnight/cred-targets.json
(credential_targets_2026_10_08.php): the 2,496 made after 4 October with masters on Reggie, the person portraits, and
every asset added since 4 October.

For each asset the master is opened where it can be found: on Reggie at its legacySourcePath, or among the files held in
inventory/ and storage/ whose SHA-256 (or SHA-1) is the recorded checksum. The stored copy is opened as well; Craft
re-saves an upload, so a clean stored copy is not evidence, and the output says which was read. Markers are what
scan_content_credentials.py counts (a C2PA or JUMBF block, an IPTC digital source type for generated media, a generator
named in the metadata, a manifest held elsewhere, which is fetched and its chain read); a byte hit exiftool does not
confirm is listed separately.

Runs inside the web container (Reggie is mounted there), with the Mac's exiftool 10.31 copied to storage/runtime/tools/et
and run by the container's perl. exiftool 10.31 predates C2PA, so an embedded manifest is found by its bytes
(c2pa.claim, urn:c2pa), as the 4 October scan did for Newhall Elementary's mark.

Writes storage/runtime/overnight/cred-scan.json. Reads only.

  ddev exec python3 scripts/import/scan_credentials_2026_10_08.py
"""
import hashlib
import json
import os
import re
import subprocess
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from _reads import reads
import scan_content_credentials as scc

ROOT = '/var/www/html'
REGGIE = '/mnt/reggie/scvhistory.com'
ET = ['perl', f'{ROOT}/storage/runtime/tools/et/exiftool']
HELD = ['inventory', 'storage/masters', 'storage/runtime/photo-import', 'storage/runtime/titles-vs-scans']
EXT = re.compile(r'\.(jpe?g|png|gif|tiff?|webp|pdf|heic|avif)$', re.I)

reads([
    ['file', 'each master: on Reggie at its legacySourcePath, or a held file whose hash is the recorded checksum', REGGIE],
    ['file', 'every image and PDF under ' + ', '.join(HELD) + ', hashed to find the masters held in the repo', ROOT],
    ['file', 'each asset\'s stored copy in web/uploads', f'{ROOT}/web/uploads'],
    ['record', 'the target list (legacySourcePath, sourceChecksum, contentCredentials, enhancementMethod from Craft)', 'which file is each asset\'s master, and what its record says',
     'read'],  # the path and checksum say where to look; the file found is opened, and a checksum match is what makes it the master
])

# exiftool 10.31 from the Mac, run by the container's perl
_run = subprocess.run
def run(cmd, **kw):
    if cmd and cmd[0] == 'exiftool':
        cmd = ET + cmd[1:]
    return _run(cmd, **kw)
scc.subprocess.run = run

TERMS = [b'c2pa', b'jumb', b'trainedalgorithmicmedia', b'firefly', b'dall-e', b'dalle', b'midjourney', b'stable diffusion',
         b'stablediffusion', b'openai', b'chatgpt', b'gpt-4o', b'leonardo.ai', b'ideogram', b'flux.1', b'nano banana']


def scan(path):
    """scan_content_credentials.scan, with the byte search done as fixed strings in 64 MB chunks: its regex over a whole
    master stalled on a large file (8 October, overnight)."""
    out = run(['exiftool', '-a', '-G1', '-s', '-api', 'LargeFileSupport=1', path], capture_output=True, text=True, errors='replace').stdout
    found = []
    for line in out.splitlines():
        if line.startswith(('[System]', '[ExifTool]', '[File]')):
            continue
        if scc.TAG.search(line) or ('Provenance' in line and 'manifest' in line.lower()):
            found.append(re.sub(r'\s+', ' ', line.strip())[:160])
    for url in sorted(set(re.findall(r'https://cai-manifests\.adobe\.com/manifests/[\w-]+', out))):
        found.append('manifest says: ' + scc.read_manifest(url))
    hits, claim, tail = set(), False, b''
    with open(path, 'rb') as fh:
        while True:
            chunk = fh.read(64 << 20)
            if not chunk:
                break
            low = tail + chunk.lower()
            hits.update(t.decode() for t in TERMS if t in low)
            claim = claim or b'c2pa.claim' in low or b'urn:c2pa:' in low
            tail = low[-64:]
    if not found and claim:
        found.append('embedded C2PA manifest (bytes; exiftool did not report it); source types and tools: read the file')
    return found, ([] if found else sorted(hits))


targets = json.load(open(f'{ROOT}/storage/runtime/overnight/cred-targets.json'))
want = {t['sum'] for t in targets if t['sum'] and not t['lsp']}
index = {}
if want:
    for top in HELD:
        for d, _, fs in os.walk(os.path.join(ROOT, top)):
            for f in fs:
                if not EXT.search(f):
                    continue
                p = os.path.join(d, f)
                try:
                    raw = open(p, 'rb').read()
                except OSError:
                    continue
                for k in ('sha256:' + hashlib.sha256(raw).hexdigest(), 'sha1:' + hashlib.sha1(raw).hexdigest()):
                    if k in want:
                        index.setdefault(k, p)

out, tally = [], {}
for i, t in enumerate(targets):
    row = dict(t)
    master, how = None, None
    if t['lsp']:
        p = REGGIE + '/' + t['lsp'].lstrip('/')
        if os.path.isfile(p):
            master, how = p, 'master on Reggie'
        else:
            how = 'master named on Reggie but not there'
    elif t['sum'] in index:
        master, how = index[t['sum']], 'master held in the repo, matched by checksum'
    else:
        how = 'no master found' + (' (checksum matches no held file)' if t['sum'] else ' (no checksum recorded)')
    row['masterRead'] = how
    row['master'] = master
    if master:
        row['masterSha256'] = hashlib.sha256(open(master, 'rb').read()).hexdigest()
        found, hits = scan(master)
        row['masterMarkers'], row['masterBytes'] = found, hits
    if os.path.isfile(t['stored']):
        found, hits = scan(t['stored'])
        row['storedMarkers'], row['storedBytes'] = found, hits
    else:
        row['storedMarkers'], row['storedBytes'] = None, None
    tally[how] = tally.get(how, 0) + 1
    if row.get('masterMarkers') or row.get('storedMarkers'):
        print(f"MARKER #{t['id']} {t['file']} ({how}; record says: {t['cc'][:80] or 'nothing'} / {t['em'][:60] or 'no edit'})")
        for f in (row.get('masterMarkers') or []) + (row.get('storedMarkers') or []):
            print('    ' + f)
    out.append(row)
    print(f'... {i + 1} of {len(targets)} #{t["id"]}', flush=True)

json.dump(out, open(f'{ROOT}/storage/runtime/overnight/cred-scan.json', 'w'), indent=1)
print('read:', json.dumps(tally))
print('assets with a marker in the master or the stored copy:', sum(1 for r in out if r.get('masterMarkers') or r.get('storedMarkers')))
print('byte-only hits (not counted as findings):', sum(1 for r in out if r.get('masterBytes') or r.get('storedBytes')))
