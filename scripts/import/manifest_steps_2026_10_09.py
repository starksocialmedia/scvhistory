#!/usr/bin/env python3
"""
Every step of every Adobe manifest the archive's files point to, read from the manifest itself (Nathan, 9 October 2026,
after the scan that sorted Couts from the other sixteen by its own one-line summary of each chain: "If his credential
records text_to_image he is in that class whoever made him").

For each manifest kept in storage/runtime/manifests (fetched by _generated_scan.php from the provenance link in each
file), each action is listed as the manifest records it: the action (c2pa.opened, c2pa.edited, c2pa.created ...), when,
the software agent's name, the Firefly operation parameter where there is one (text_to_image, generative_fill ...), and
the digital source type. Whether the manifest names an ingredient with the relationship parentOf (an original opened) is
reported too. The manifest store can hold the manifests of earlier steps as well as the last one; every action is listed.

Joined to the assets by storage/runtime/generated-scan.json (build_withheld_media.php), which records which file was read
and which manifest it names. Writes storage/runtime/manifest-steps.json and prints a table. Reads only.
  python3 scripts/import/manifest_steps_2026_10_09.py
"""
import json
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from _reads import reads

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
MAN = os.path.join(ROOT, 'storage/runtime/manifests')
SCAN = os.path.join(ROOT, 'storage/runtime/generated-scan.json')

reads([
    ['record', 'every manifest in storage/runtime/manifests, as fetched from the provenance link in each file', 'the image files', 'read'],
    ['record', 'storage/runtime/generated-scan.json: which asset\'s files name which manifest, and where each asset is used', 'the image files', 'read'],
])


def text(b):
    return re.sub(r'[^\x20-\x7e]', '.', b.decode('latin-1'))


def ctext(raw, pos):
    """The CBOR text string that starts at pos (a manifest is CBOR inside JUMBF): its header byte gives the length."""
    if pos >= len(raw):
        return ''
    h = raw[pos]
    if 0x60 <= h <= 0x77:
        n, s = h - 0x60, pos + 1
    elif h == 0x78:
        n, s = raw[pos + 1], pos + 2
    elif h == 0x79:
        n, s = int.from_bytes(raw[pos + 1:pos + 3], 'big'), pos + 3
    else:
        return ''
    return raw[s:s + n].decode('utf-8', 'replace')


def after(seg, key):
    """The text value of a map key in seg: the key is itself a CBOR text string, so its header byte precedes it."""
    k = key.encode()
    i = seg.find(k)
    while i != -1:
        if i > 0 and ctext(seg, i - 1 if len(k) < 24 else i - 2) == key:
            return ctext(seg, i + len(k))
        i = seg.find(k, i + 1)
    return ''


def steps(raw):
    out = []
    marks = [m.start() for m in re.finditer(rb'faction', raw)]
    for i, s in enumerate(marks):
        seg = raw[s:(marks[i + 1] if i + 1 < len(marks) else s + 1500)][:1500]
        act = after(seg, 'action')
        if not act.startswith('c2pa.'):
            continue
        dst = after(seg, 'digitalSourceType')
        out.append({'action': act[5:], 'when': after(seg, 'when')[:16].replace('T', ' '), 'agent': after(seg, 'name'),
                    'operation': after(seg, 'com.adobe.firefly.operation'), 'sourceType': dst.rsplit('/', 1)[-1] if dst else ''})
    return out


scan = json.load(open(SCAN)) if os.path.exists(SCAN) else []
byman = {}
for a in scan:
    for r in a.get('read', []):
        m = re.search(r'manifests/(urn-c2pa-[\w-]+)', r)
        if m:
            byman.setdefault(m.group(1), []).append(a)

rows = []
for f in sorted(os.listdir(MAN)):
    raw = open(os.path.join(MAN, f), 'rb').read()
    st = steps(raw)
    parent = bool(re.search(rb'relationship.{0,3}parentOf', raw))
    for a in byman.get(f, [{'id': None, 'file': '(no asset in the scan)', 'usedBy': []}]):
        rows.append({'manifest': f, 'asset': a['id'], 'file': a['file'], 'used': bool(a.get('usedBy')), 'usedBy': a.get('usedBy', [])[:5],
                     'original': parent, 'steps': st,
                     'textToImage': any(x['operation'] == 'text_to_image' for x in st)})

json.dump(rows, open(os.path.join(ROOT, 'storage/runtime/manifest-steps.json'), 'w'), indent=1)
print(f'{len(rows)} asset-manifest pairs from {len(os.listdir(MAN))} manifests; '
      f'{sum(r["textToImage"] for r in rows)} with a text_to_image operation, {sum(r["textToImage"] and r["used"] for r in rows)} of them on a live record')
for r in sorted(rows, key=lambda r: (not r['used'], not r['textToImage'], r['asset'] or 0)):
    chain = ' > '.join(f"{x['action']}" + (f" [{x['agent']}]" if x['agent'] else '') + (f" op={x['operation']}" if x['operation'] else '')
                       + (f" {x['sourceType']}" if x['sourceType'] else '') for x in r['steps'])
    print(f"#{r['asset']} {r['file']} | {'ON ' + ','.join(map(str, r['usedBy'])) if r['used'] else 'no record'} | original {'yes' if r['original'] else 'NO'} | {chain}")


# The chain, manifest by manifest (Nathan, 9 October 2026, evening: "read the full action chain on each ... Does the chain
# start from an opened original, or does it start from nothing? Is the step the first action or a later one? Does the
# manifest name a parent file?"). A manifest store holds one manifest per save. Each manifest's ingredient either carries
# its own credential (activeManifest: the previous save in the chain) or carries none (a file brought in from outside the
# chain: at the root, the parent upload). The root manifest is the one whose ingredient has no credential, or none at all.
def chain(raw):
    starts = []
    seen = set()
    for m in re.finditer(rb'urn:c2pa:[0-9a-f-]{36}', raw):
        u = m.group(0)
        nxt = raw[m.end():m.end() + 260]
        if u not in seen and b'c2pa.ingredient' in nxt or (u not in seen and b'c2pa.actions' in nxt):
            seen.add(u)
            starts.append((m.start(), u.decode()))
    out = []
    for i, (s, u) in enumerate(starts):
        seg = raw[s:(starts[i + 1][0] if i + 1 < len(starts) else len(raw))]
        ings = []
        for r in re.finditer(rb'relationship', seg):
            rel = ctext(seg, r.end())
            win = seg[max(0, r.start() - 200):r.end() + 400]
            fi = win.find(b'dc:format')
            ings.append({'relationship': rel, 'format': ctext(win, fi + 9) if fi != -1 else '',
                         'credential': b'activeManifest' in win and b'validationResults' in win})
        out.append({'manifest': u, 'ingredients': ings, 'actions': steps(seg)})
    return out


def describe(ch):
    lines = []
    for n, m in enumerate(ch, 1):
        ing = '; '.join(f"{x['relationship']} {x['format'] or '?'} {'(an earlier save, credentialed)' if x['credential'] else '(NO credential: a file from outside the chain)'}" for x in m['ingredients']) or 'NO INGREDIENT'
        acts = ' > '.join(x['action'] + (f" [{x['agent']}]" if x['agent'] else '') + (f" op={x['operation']}" if x['operation'] else '') for x in m['actions'])
        lines.append(f"  manifest {n} {m['manifest'][:22]}: ingredient {ing}; actions {acts}")
    return lines


if '--chains' in sys.argv:
    want = [int(x) for x in sys.argv[sys.argv.index('--chains') + 1].split(',')]
    for r in rows:
        if r['asset'] in want and r['asset'] not in [w for w in []]:
            ch = chain(open(os.path.join(MAN, r['manifest']), 'rb').read())
            root = ch[0] if ch else None
            rootIng = [x for x in (root['ingredients'] if root else []) if not x['credential']]
            first = root['actions'][0]['action'] if root and root['actions'] else ''
            tti = [(n, k) for n, m in enumerate(ch, 1) for k, x in enumerate(m['actions'], 1) if x['operation'] == 'text_to_image']
            print(f"#{r['asset']} {r['file']}: {len(ch)} manifests; root opens {'a file with no credential (' + ', '.join(x['relationship'] + ' ' + x['format'] for x in rootIng) + ')' if rootIng else 'NOTHING'}; "
                  f"root's first action {first}; text_to_image at " + (', '.join(f'manifest {n} action {k}' for n, k in tti) or 'none'))
            print('\n'.join(describe(ch)))
            want.remove(r['asset'])
