#!/usr/bin/env python3
"""
The publisher-edit census (Nathan, 9 October 2026: "how many of the archive's images are in that position,
publisher-edited rather than ours, as far as the credentials can tell ... I would rather know the shape now").

For every image asset in storage/runtime/overnight/pub-targets.json (publisher_edits_targets_2026_10_09.php), the master
is opened where it can be found (Reggie at legacySourcePath; a held file whose SHA-256 or SHA-1 is the recorded checksum)
and its own metadata read with exiftool in batches: the software that wrote it (Software, CreatorTool, the XMP history's
software agents and actions), the camera (Make, Model), IPTC/XMP digital source type, and a provenance link; its bytes are
searched in 64 MB chunks for an embedded C2PA claim. Where no master is found, the stored copy is read and the row says so
(Craft re-saves an upload, which can drop metadata, so a bare stored copy says little).

What it can tell and what it cannot: a credential (C2PA) says what was done; an editing program named in the metadata says
only that the program saved the file, not what it changed; a file with no metadata says nothing either way. A composite like
the City's council portraits is visible in the picture, not in its metadata. So the counts below are an upper bound on
"touched by software", a lower bound on "edited", and say nothing about files stripped of metadata.

Runs in the web container: ddev exec python3 scripts/import/publisher_edits_census_2026_10_09.py
Writes storage/runtime/overnight/pub-census.json. Reads only.
"""
import hashlib
import json
import os
import re
import subprocess
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from _reads import reads

ROOT = '/var/www/html'
REGGIE = '/mnt/reggie/scvhistory.com'
ET = ['perl', f'{ROOT}/storage/runtime/tools/et/exiftool']
HELD = ['inventory', 'storage/masters', 'storage/runtime/photo-import', 'storage/runtime/titles-vs-scans']
EXT = re.compile(r'\.(jpe?g|png|gif|tiff?|webp|heic|avif|svg)$', re.I)
EDITORS = re.compile(r'photoshop|lightroom|gimp|capture one|affinity|pixelmator|photoscape|picasa|snapseed|fotor|canva|picmonkey|'
                     r'paint ?shop|corel|acdsee|iphoto|luminar|topaz|remini|facetune|faceapp|meitu|firefly|photopea|darktable|'
                     r'paint\.net|photo ?director|photos? [0-9]|aperture|camera raw|dxo|on1', re.I)
TAGS = ['-Software', '-CreatorTool', '-HistorySoftwareAgent', '-HistoryAction', '-Make', '-Model', '-DateTimeOriginal',
        '-DigitalSourceType', '-XMP-dcterms:Provenance', '-Credit', '-Artist', '-Copyright', '-ImageSize']

reads([
    ['file', 'each image master: on Reggie at its legacySourcePath, or a held file whose hash is the recorded checksum', REGGIE],
    ['file', 'every image under ' + ', '.join(HELD) + ', hashed to find the masters held in the repo', ROOT],
    ['file', 'the stored copy, where no master is found', f'{ROOT}/web/uploads'],
    ['record', 'the target list (legacySourcePath, sourceChecksum, provenanceKind, enhancementMethod, enhancedBy, contentCredentials from Craft)',
     'which file is each master, and whether the archive itself edited it', 'read'],
])

T = json.load(open(f'{ROOT}/storage/runtime/overnight/pub-targets.json'))
want = {t['sum'] for t in T if t['sum'] and not t['lsp']}
index = {}
for top in HELD:
    for d, _, fs in os.walk(os.path.join(ROOT, top)):
        for f in fs:
            if EXT.search(f):
                p = os.path.join(d, f)
                try:
                    raw = open(p, 'rb').read()
                except OSError:
                    continue
                for k in ('sha256:' + hashlib.sha256(raw).hexdigest(), 'sha1:' + hashlib.sha1(raw).hexdigest()):
                    if k in want:
                        index.setdefault(k, p)
print('held files hashed; masters matched:', len(index), flush=True)

for t in T:
    p = REGGIE + '/' + t['lsp'].lstrip('/') if t['lsp'] else None
    if p and os.path.isfile(p):
        t['path'], t['read'] = p, 'master on Reggie'
    elif t['sum'] in index:
        t['path'], t['read'] = index[t['sum']], 'master held in the repo'
    elif os.path.isfile(t['stored']):
        t['path'], t['read'] = t['stored'], 'stored copy only (no master found)'
    else:
        t['path'], t['read'] = None, 'nothing readable'

meta = {}
paths = sorted({t['path'] for t in T if t['path']})
for i in range(0, len(paths), 300):
    chunk = paths[i:i + 300]
    r = subprocess.run(ET + ['-j', '-q', '-q', '-api', 'LargeFileSupport=1'] + TAGS + chunk, capture_output=True, text=True, errors='replace')
    try:
        for m in json.loads(r.stdout or '[]'):
            meta[m['SourceFile']] = m
    except json.JSONDecodeError:
        pass
    print(f'... metadata {min(i + 300, len(paths))} of {len(paths)}', flush=True)


def claim(path):
    tail = b''
    with open(path, 'rb') as fh:
        while True:
            c = fh.read(64 << 20)
            if not c:
                return False
            low = tail + c
            if b'c2pa.claim' in low or b'urn:c2pa:' in low:
                return True
            tail = low[-32:]


def flat(v):
    return ' | '.join(map(str, v)) if isinstance(v, list) else ('' if v is None else str(v))


out, tally = [], {}
for n, t in enumerate(T):
    m = meta.get(t['path'], {}) if t['path'] else {}
    soft = ' | '.join(filter(None, [flat(m.get(k)) for k in ('Software', 'CreatorTool', 'HistorySoftwareAgent')]))
    camera = ' '.join(filter(None, [flat(m.get('Make')), flat(m.get('Model'))]))
    c2pa = bool(t['path']) and claim(t['path'])
    ours = bool(t['em'] or t['by'] or 'firefly' in t['cc'].lower())
    if ours:
        cls = 'our own edit (recorded on the asset)'
    elif c2pa or m.get('XMP-dcterms:Provenance') or m.get('Provenance') or m.get('DigitalSourceType'):
        cls = 'publisher credential (C2PA or IPTC source type)'
    elif EDITORS.search(soft):
        cls = 'editing software named in the metadata'
    elif camera:
        cls = 'camera metadata, no editing software named'
    elif soft:
        cls = 'other software named (not an editor)'
    elif t['read'].startswith('master'):
        cls = 'master read, no metadata about its making'
    else:
        cls = 'stored copy only, no metadata (says nothing)'
    row = {k: t[k] for k in ('id', 'file', 'kind', 'used', 'read')}
    row.update({'class': cls, 'software': soft[:200], 'camera': camera, 'c2pa': c2pa, 'history': flat(m.get('HistoryAction'))[:120],
                'credit': flat(m.get('Credit'))[:80], 'size': flat(m.get('ImageSize'))})
    out.append(row)
    key = (t['kind'] or 'none', cls)
    tally[key] = tally.get(key, 0) + 1
    if n % 500 == 0:
        print(f'... classified {n} of {len(T)}', flush=True)

json.dump(out, open(f'{ROOT}/storage/runtime/overnight/pub-census.json', 'w'), indent=1)
print('read:', json.dumps({r: sum(1 for t in T if t['read'] == r) for r in sorted({t['read'] for t in T})}))
for (kind, cls), v in sorted(tally.items()):
    print(f'{v:6d}  {kind:17s} {cls}')
