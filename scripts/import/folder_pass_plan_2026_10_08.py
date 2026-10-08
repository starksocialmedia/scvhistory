# The folder pass, plan (8 October 2026; Nathan: "Then the second pass, dry run first"). Read-only: writes
# storage/runtime/photo-import/plan2.json and inventory/review/folder-pass-dry-run-2026-10-08.md.
# Input: folder_census_2026_10_08.py's rows (storage/runtime/photo-import/folder-census.json): every content item in a
# record's own folder on Reggie that Craft does not hold for the record, read from the folder itself.
# Rules:
#   - A folder is given to a record when it is named for the record (its page name or legacyKey), or when that record is
#     the only one to claim it. A folder two records claim goes to the one it is named for; where both carry its name
#     (one legacy page made into two records), to the photograph if one is a photograph, otherwise it is held.
#   - One file per item, the largest (a TIFF before a JPEG, _large before the plain size).
#   - Left out: a thumbnail (name ending t) of a picture the record holds or this pass adds.
#   - Held for Nathan: a picture named for another record's code (a neighbour's picture in this folder); the Internet
#     Archive's raw page scans (.jp2) of a book whose PDF the record holds; a PDF named -orig or -ebook (a master or a
#     second version of a PDF the record holds); audio and video (no field takes them).
#   - Pictures go after what the record holds, in folder order: featuredImage only if the record has none, then
#     recordImages. PDFs and office files go to recordDocuments (documentFiles on a document).
#   - Web copies by the import's rule (2,400 long side, strips by pixel count, q82, never enlarged; PNG stays PNG; GIF,
#     TIFF and JPEG 2000 become JPEG). A generic file name (page_001, 001, a camera or hash name) takes the folder as a
#     prefix so it cannot collide in legacy/.
# Run inside the container: python3 scripts/import/folder_pass_plan_2026_10_08.py
import json, os, re, collections
RT = '/var/www/html/storage/runtime/photo-import/'
ROOT = '/mnt/reggie/scvhistory.com'
c = json.load(open(RT + 'folder-census.json'))
dump = json.load(open(RT + 'folder-dump.json'))
ent = {e['id']: e for e in dump['entries']}
links = json.load(open(RT + 'folder-links.json'))
inv = collections.Counter(f for fs in links.values() for f in fs)
shared = {f for f, n in inv.items() if n > 3}
man = {}
for mf in ['/var/www/html/inventory/raw/scvhistory-manifest-2026-08-20.sha256', '/mnt/reggie/scvhistory-manifest-addendum-2026-10-04.sha256']:
    if os.path.exists(mf):
        for l in open(mf, encoding='utf-8', errors='replace'):
            h, _, p = l.rstrip('\n').partition('  ./')
            if p: man['/' + p.rstrip('?')] = h
existing_names = {a['file'].lower() for a in dump['assets'] if a['folder'] == 'legacy/'}

def natural(s): return [int(t) if t.isdigit() else t for t in re.split(r'(\d+)', s)]
def onreggie(rel):
    for p in (ROOT + rel, ROOT + rel + '?'):
        if os.path.exists(p): return p
    return None
def rank(rel):
    f = rel.lower(); score = 0
    if re.search(r'\.tiff?$', f): score += 4
    if '_large' in f or '_orig' in f: score += 2
    if '/data1/images/' in f: score -= 1
    p = onreggie(rel); return (score, os.path.getsize(p) if p else 0)
claimants = collections.defaultdict(set)
for r in c['rows']: claimants[r['folder']].add(r['id'])
def own(e, folder):
    keys = {os.path.splitext(os.path.basename(e['legacyUrl']))[0].lower(), (e['legacyKey'] or '').lower()}
    if claimants[folder] == {e['id']}: return True
    if folder.lower() not in keys: return False
    named = [x for x in claimants[folder] if folder.lower() in {os.path.splitext(os.path.basename(ent[x]['legacyUrl']))[0].lower(), (ent[x]['legacyKey'] or '').lower()}]
    if len(named) == 1: return True
    photos = [x for x in named if ent[x]['section'] == 'photographs']
    return photos == [e['id']]

rows = [r for r in c['rows'] if r['folder'] not in shared and r['state'] != 'held on the record']
by = collections.defaultdict(list)
for r in rows: by[r['id']].append(r)
all_items = collections.defaultdict(set)
for r in c['rows']: all_items[r['id']].add(r['item'])
held = []; records = []; used_names = set(existing_names)
for i, rr in sorted(by.items()):
    e = ent[i]; items = []
    owner_folders = {r['folder'] for r in rr if own(e, r['folder'])}
    has_pdf_held = any(r['state'] == 'held on the record' and r['kind'] == 'pdf' for r in c['rows'] if r['id'] == i)
    for r in sorted(rr, key=lambda r: (r['folder'], natural(r['item']))):
        best = max(r['files'], key=rank); name = os.path.basename(best).rstrip('?'); low = name.lower()
        why = None
        if r['folder'] not in owner_folders:
            why = 'a folder another record also claims, named for neither or for the other'
        elif r['kind'] in ('audio', 'video'):
            why = 'audio or video: no field on the record takes it'
        elif r['kind'] == 'picture' and r['item'].endswith('t') and r['item'][:-1] in all_items[i]:
            why = 'a thumbnail (name ending t) of a picture the record holds or this pass adds'
        elif low.endswith('.jp2') and has_pdf_held:
            why = "the Internet Archive's raw page scan of a book whose PDF the record holds"
        elif r['kind'] == 'pdf' and re.search(r'-(orig|ebook)\.pdf$', low):
            why = 'a master or second version of a PDF (-orig, -ebook)'
        else:
            m = re.match(r'^([a-z]{2}\d{4})', low); code = (e['legacyKey'] or os.path.basename(e['legacyUrl'])).lower()
            if m and not code.startswith(m.group(1)) and m.group(1) != r['folder'].lower()[:6]:
                why = f'named for another record ({m.group(1).upper()})'
        if why:
            held.append(dict(id=i, title=e['title'], folder=r['folder'], file=best, why=why)); continue
        stem, ext = os.path.splitext(low)
        ext = ext if ext in ('.png', '.pdf', '.docx', '.xls', '.rtf', '.pptx', '.txt') else '.jpg'
        web = re.sub(r'[^a-z0-9._-]', '-', stem)
        if not web.startswith(r['folder'].lower()[:4]) or re.fullmatch(r'(page)?_?\d+|img_\d+|\d+_[0-9a-f]+_o|[0-9a-f]{20,}.*', web):
            web = r['folder'].lower() + '-' + web
        web = web + ext; n = 2
        while web in used_names: web = re.sub(r'(-\d+)?(\.\w+)$', f'-{n}\\2', web); n += 1
        used_names.add(web)
        sha = man.get(best.rstrip('?'), '')
        items.append(dict(src=onreggie(best), legacySourcePath=best.rstrip('?'), sha256=sha, bytes=os.path.getsize(onreggie(best)) if onreggie(best) else 0,
                          web=web, kind='pdf' if r['kind'] == 'pdf' else ('office' if r['kind'] == 'office' else 'image'), role='document' if r['kind'] != 'picture' else 'page', reuse=None))
    if items:
        records.append(dict(id=i, section=e['section'], title=e['title'], legacyUrl=e['legacyUrl'], items=items))
json.dump(dict(records=records, held=held), open(RT + 'plan2.json', 'w'), indent=1)
nf = sum(len(r['items']) for r in records)
print(len(records), 'records,', nf, 'files to add;', len(held), 'held for Nathan')
print(collections.Counter(it['kind'] for r in records for it in r['items']), round(sum(it['bytes'] for r in records for it in r['items']) / 1e9, 2), 'GB of masters')
print(collections.Counter(h['why'] for h in held))
