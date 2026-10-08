# Read-only audit, 8 October 2026 (Claude, for Nathan: "Run that check across every asset that records a master checksum:
# does the stored checksum match the master, or the file itself? Tell me how many are self-referential.")
#
# What it reads, before any number:
#   - every asset's recorded sourceChecksum, legacySourcePath, sourceUrl and provenanceKind, from Craft
#     (checksum_audit_dump_2026_10_08.php);
#   - the master each names: for a legacy path, the drive manifests (inventory/raw/scvhistory-manifest-2026-08-20.sha256
#     and the addendum on Reggie), which were made from Reggie, not from Craft; for an outside file, any original kept
#     outside Craft (inventory/incoming, inventory/sole-copies, inventory/elections, storage/masters), hashed here;
#   - the stored file itself, hashed here from the volume.
# A recorded checksum is SELF-REFERENTIAL when it equals the stored file's own hash and no independent master confirms it:
# a comparison of the file with itself, which cannot fail. Where the master is independently known and also equals the
# file (a PDF copied as it is), that is a match, not self-reference.
# Run inside the container: python3 scripts/import/checksum_audit_2026_10_08.py
import json, os, hashlib, collections
R = '/var/www/html/'
D = json.load(open(R + 'storage/runtime/photo-import/checksum-dump.json'))
man = {}
for mf in [R + 'inventory/raw/scvhistory-manifest-2026-08-20.sha256', '/mnt/reggie/scvhistory-manifest-addendum-2026-10-04.sha256']:
    if os.path.exists(mf):
        for l in open(mf, encoding='utf-8', errors='replace'):
            h, _, p = l.rstrip('\n').partition('  ./')
            if p: man.setdefault(p.rstrip('?').lower(), set()).add(h)
man_all = {h for s in man.values() for h in s}
def sha(p):
    h = hashlib.sha256()
    with open(p, 'rb') as f:
        for b in iter(lambda: f.read(1 << 20), b''): h.update(b)
    return h.hexdigest()
held = {}
for root in ['inventory/incoming', 'inventory/sole-copies', 'inventory/elections', 'storage/masters']:
    for d, ds, fs in os.walk(R + root):
        for f in fs:
            if not f.startswith('.'): held.setdefault(sha(os.path.join(d, f)), os.path.join(d, f)[len(R):])
rows = []; c = collections.Counter()
for a in D:
    if not a['sum']: continue
    s = a['sum'].split(':', 1)[-1].lower()
    p = a['path'] if a['path'].startswith('/') else R + a['path']
    fh = sha(p) if os.path.isfile(p) else None
    lsp = a['lsp'].lstrip('/').rstrip('?').lower()
    named = man.get(lsp, set()) if lsp else set()
    if named and s in named: st = 'matches its master (drive manifest)' + (', file identical' if fh == s else '')
    elif named and fh == s: st = 'SELF-REFERENTIAL and wrong: the file\'s own hash, not the master named (manifest differs)'
    elif named: st = 'matches neither the master named nor the file'
    elif fh is None: st = 'file missing from the volume'
    elif s in held and fh != s: st = 'matches an original kept outside Craft (' + held[s] + ')'
    elif fh == s and s in held: st = 'SELF-REFERENTIAL: the file\'s own hash; an identical copy is kept at ' + held[s] + ', which is the same bytes, not an independent master'
    elif fh == s and s in man_all: st = 'matches a file on Reggie under another path (file identical)'
    elif fh == s: st = 'SELF-REFERENTIAL: the file\'s own hash, and no master is held to check it against'
    elif s in man_all: st = 'matches a file on Reggie under another path'
    else: st = 'matches nothing held: master not held, not the file'
    c[st.split(' (')[0].split(':')[0] if st.startswith('SELF') else st.split(' (')[0]] += 1
    rows.append(dict(id=a['id'], path=a['path'].replace(R, ''), lsp=a['lsp'], kind=a['kind'], url=a['url'], used=a['used'], state=st))
json.dump(rows, open(R + 'storage/runtime/photo-import/checksum-audit.json', 'w'), indent=1)
print(len(rows), 'assets record a checksum')
for k, v in c.most_common(): print(f'  {v:5}  {k}')
self_ = [r for r in rows if r['state'].startswith('SELF')]
print('self-referential:', len(self_), '| on a record:', sum(r['used'] for r in self_), '| by provenance kind:', dict(collections.Counter(r['kind'] for r in self_)))
