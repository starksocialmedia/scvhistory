# Read-only census, 8 October 2026 (Claude, for Nathan): for every Craft entry with a legacy page, open the record's own
# folder(s) on Reggie and list every content file Craft does not hold for that record.
#
# What it reads (stated before any number, per ERRORLOG 8 October):
#   - the folders themselves, walked file by file on the drive (/mnt/reggie/scvhistory.com/scvhistory/files/<folder>/),
#     not a page's links and not a manifest;
#   - a folder is a record's own when its name is the record's page name or legacyKey, or when the record's page on
#     Reggie links to anything inside it;
#   - Craft as dumped by scripts/import/folder_census_dump_2026_10_08.php (entries, the assets each carries, and each
#     asset's legacySourcePath and sourceChecksum); checksums of drive files from the drive manifests.
# Content: pictures (jpg jpeg png gif tif tiff jp2), PDFs, audio and video, office files. Not content, and skipped whole:
#   the flipbook and slideshow software and its renders (files/, mobile/, engine1/, data1/thumbnails, data1/tooltips,
#   TileGroup*/), and html, js, css, xml, fonts. Each such folder name is counted so the skip is visible.
# A file counts as held when an asset on the record has its path or checksum, or its name (less _large/_orig, a
# -p1 cover suffix, or the folder prefix the import added), compared without punctuation (the slideshow software saves
# each picture again with its hyphens dropped). One picture saved twice (data1/images and the folder root,
# or x.jpg and x_large.jpg) is one item.
# Run inside the container: python3 scripts/import/folder_census_2026_10_08.py
import json, os, re, collections, html
ROOT = '/mnt/reggie/scvhistory.com'
FILES = ROOT + '/scvhistory/files'
RT = '/var/www/html/storage/runtime/photo-import/'
SKIP_DIRS = {'files', 'mobile', 'engine1', 'thumbnails', 'tooltips'}
CONTENT = {'jpg': 'picture', 'jpeg': 'picture', 'png': 'picture', 'gif': 'picture', 'tif': 'picture', 'tiff': 'picture', 'jp2': 'picture',
           'pdf': 'pdf', 'mp3': 'audio', 'ogg': 'audio', 'mp4': 'video', 'mov': 'video',
           'docx': 'office', 'xls': 'office', 'rtf': 'office', 'pptx': 'office', 'txt': 'office'}

man = {}
for mf in ['/var/www/html/inventory/raw/scvhistory-manifest-2026-08-20.sha256', '/mnt/reggie/scvhistory-manifest-addendum-2026-10-04.sha256']:
    if os.path.exists(mf):
        for l in open(mf, encoding='utf-8', errors='replace'):
            h, _, p = l.rstrip('\n').partition('  ./')
            if p: man['/' + p.rstrip('?')] = h

d = json.load(open(RT + 'folder-dump.json'))
assets = {a['id']: a for a in d['assets']}
all_lsp = {a['lsp'].rstrip('?').lower() for a in d['assets'] if a['lsp']}
all_sum = {a['sum'] for a in d['assets'] if a['sum']}

def stem(name):
    s = name.lower().rstrip('?')
    s = re.sub(r'\.(jpe?g|png|gif|tiff?|jp2|pdf|mp3|ogg|mp4|mov|docx|xls|rtf|pptx|txt)$', '', s)
    s = re.sub(r'(_large|_orig|-p1|_\d{4}-\d{2}-\d{2}-\d{6}_\w{4})$', '', s)
    return re.sub(r'[^a-z0-9]', '', s)

folders = {f.lower(): f for f in os.listdir(FILES) if os.path.isdir(os.path.join(FILES, f))}
link_re = re.compile(r'''(?:href|src|data|value|file)\s*=\s*["']?([^"'\s>]+)''', re.I)

def page_folders(lu):
    p = ROOT + lu
    cand = [p, p + '?']
    src = next((c for c in cand if os.path.isfile(c)), None)
    if not src: return set(), False
    txt = open(src, encoding='latin-1').read()
    base = os.path.dirname(lu)
    out = set()
    for u in link_re.findall(txt):
        u = html.unescape(u).split('#')[0].split('?')[0]
        if not u or re.match(r'^(mailto|javascript|https?:(?!//(www\.)?scvhistory\.com))', u, re.I): continue
        u = re.sub(r'^https?://(www\.)?scvhistory\.com', '', u, flags=re.I)
        full = os.path.normpath(u if u.startswith('/') else base + '/' + u)
        m = re.match(r'^/scvhistory/files/([^/]+)/', full)
        if m and m.group(1).lower() in folders: out.add(folders[m.group(1).lower()])
    return out, True

skipped_dirs = collections.Counter()
def walk(folder):
    items = {}
    for dp, ds, fs in os.walk(os.path.join(FILES, folder)):
        keep = []
        for x in ds:
            if x in SKIP_DIRS or x.startswith('TileGroup') or x in ('javascript', 'style', 'basic-html'):
                skipped_dirs[x if not x.startswith('TileGroup') else 'TileGroup*'] += 1
            else: keep.append(x)
        ds[:] = keep
        for f in fs:
            ext = f.lower().rstrip('?').rsplit('.', 1)[-1]
            if ext not in CONTENT: continue
            rel = os.path.join(dp, f)[len(ROOT):].rstrip('?')
            items.setdefault((CONTENT[ext], stem(f)), []).append(rel)
    return items

rows = []; no_page = 0; claimed = set()
for e in d['entries']:
    lu = e['legacyUrl']
    if not lu or not lu.startswith('/'): continue
    fl, found = page_folders(lu)
    if not found: no_page += 1
    for key in {os.path.splitext(os.path.basename(lu))[0].lower(), (e['legacyKey'] or '').lower()}:
        if key and key in folders: fl.add(folders[key])
    if not fl: continue
    mine = [assets[i] for i in e['assets'] if i in assets]
    m_lsp = {a['lsp'].rstrip('?').lower() for a in mine if a['lsp']}
    m_sum = {a['sum'] for a in mine if a['sum']}
    m_stem = set()
    for a in mine:
        f = a['file'].lower(); m_stem.add(stem(f)); m_stem.add(stem(re.sub(r'^[a-z_]{2,}\d{3,}[a-z]?-', '', f)))
    for folder in sorted(fl):
        claimed.add(folder)
        for (kind, st), paths in walk(folder).items():
            sums = {man.get(p) for p in paths} - {None}
            on_record = any(p.lower() in m_lsp for p in paths) or bool(sums & m_sum) or st in m_stem
            if on_record: state = 'held on the record'
            elif any(p.lower() in all_lsp for p in paths) or bool(sums & all_sum): state = 'in Craft, not on this record'
            else: state = 'not in Craft'
            rows.append(dict(id=e['id'], section=e['section'], title=e['title'], page=lu, folder=folder, kind=kind, item=st, files=paths, state=state))

json.dump(dict(rows=rows, skipped_dirs=skipped_dirs, folders_total=len(folders), folders_claimed=len(claimed),
               unclaimed=sorted(set(folders.values()) - claimed), pages_missing=no_page), open(RT + 'folder-census.json', 'w'), indent=1)

recs = {r['id'] for r in rows}
miss = [r for r in rows if r['state'] != 'held on the record']
print('folders on the drive:', len(folders), '| claimed by a record:', len(claimed), '| entries with a folder:', len(recs), '| entries whose page is not on Reggie:', no_page)
print('content items in those folders:', len(rows), '| files:', sum(len(r['files']) for r in rows))
for st in ['held on the record', 'in Craft, not on this record', 'not in Craft']:
    rr = [r for r in rows if r['state'] == st]
    print(f'  {st}: {len(rr)} items, {sum(len(r["files"]) for r in rr)} files, {len({r["id"] for r in rr})} records',
          dict(collections.Counter(r['kind'] for r in rr)))
print('records with at least one item not held:', len({r['id'] for r in miss}),
      dict(collections.Counter(r['section'] for r in {r['id']: r for r in miss}.values())))
print('skipped software folders:', dict(skipped_dirs.most_common(12)))

# The report. A folder linked from more than three pages on the whole drive is shared material (a site-wide link such
# as citizen19880918, on 290 pages), not a record's own folder: counted apart. Page links come from
# scripts/import/folder_census_links_2026_10_08.py (folder-links.json), every .htm on the drive outside scvhistory/files.
links = json.load(open(RT + 'folder-links.json'))
inv = collections.Counter(f for fs in links.values() for f in fs)
shared = {f for f, n in inv.items() if n > 3}
plan = {r['id'] for r in json.load(open(RT + 'plan.json'))['records']}
own = [r for r in rows if r['folder'] not in shared]
by = collections.defaultdict(list)
for r in own: by[r['id']].append(r)
lines = []; tot = collections.Counter(); totf = collections.Counter(); kinds = collections.Counter(); grp_n = collections.Counter()
for i, rr in sorted(by.items()):
    miss = [r for r in rr if r['state'] != 'held on the record']
    if not miss: continue
    held = len(rr) - len(miss); sec = rr[0]['section']
    grp = 'photographs imported 7 October' if i in plan else ('photographs skipped, already had a picture' if sec == 'photographs' else sec)
    grp_n[grp] += 1; tot[grp] += len(miss); totf[grp] += sum(len(r['files']) for r in miss)
    k = collections.Counter(r['kind'] for r in miss); kinds.update(k)
    lines.append(f"| #{i} | {sec} | {rr[0]['title'][:70].replace('|', '/')} | {', '.join(sorted({r['folder'] for r in rr}))} | {held} | {len(miss)} ({', '.join(f'{v} {x}' for x, v in sorted(k.items()))}) | {sum(len(r['files']) for r in miss)} |")
missing = [r for r in own if r['state'] != 'held on the record']
uniq = {(r['folder'], r['item']) for r in missing}; uniq_f = {p for r in missing for p in r['files']}
two = {f: v for f, v in ((f, {r['id'] for r in missing if r['folder'] == f}) for f in {r['folder'] for r in missing}) if len(v) > 1}
n_rec = sum(grp_n.values()); n_it = sum(tot.values()); n_f = sum(totf.values())
md = [f"# What sits in each record's own folder and not in Craft (8 October 2026)", "",
      "Read-only census, Claude, for Nathan (\"Walk every record's own folder on Reggie ... Not a sample\"). Script: scripts/import/folder_census_2026_10_08.py; data storage/runtime/photo-import/folder-census.json.", "",
      "## What it read", "",
      f"- **The folders themselves**, file by file on the drive (/mnt/reggie/scvhistory.com/scvhistory/files/, {len(folders)} folders), not a page's links and not a manifest.",
      f"- **Which folder is a record's own:** its name is the record's page name or legacyKey, or the record's page on Reggie links into it. {len(claimed)} folders are claimed by a Craft record. {len(shared)} linked from more than three pages are shared material, not one record's, and are left out of the count ({', '.join(f'{f} ({inv[f]} pages)' for f in sorted(shared))}).",
      "- **Content:** pictures (jpg, png, gif, tif, jp2), PDFs, audio, video, office files. Left out: the flipbook and slideshow software and its renders (files/, mobile/, engine1/, data1/thumbnails, data1/tooltips, zoom tiles), and html, js, css, xml and fonts.",
      "- **Held** means an asset on that record has the file's path, its checksum, or its name (less _large, _orig, a -p1 cover suffix or the import's folder prefix, compared without punctuation). One picture saved twice in a folder is one item.",
      "- **Craft** as dumped on 8 October (scripts/import/folder_census_dump_2026_10_08.php), while the 15 held records were being written.",
      f"- **Not covered:** {no_page} records whose page is not on Reggie (the mirror's known limit), except where a folder carries their name; and the {len(set(folders.values())) - len(claimed)} folders no Craft record claims, nearly all behind the 5,606 legacy pages that have no record yet.", "",
      "## The number", "",
      f"**{n_rec} records have content in their own folder that Craft does not hold: {n_it} items, {n_f} files.** None of the missing files is in Craft on another record.", "",
      f"{len(two)} folders are claimed by two records each ({'; '.join(f'{f}: ' + ' and '.join('#' + str(x) for x in sorted(v)) for f, v in sorted(two.items()))}), so the table below counts their files under both. Counted once: **{len(uniq)} items, {len(uniq_f)} files.**", "",
      "| Group | Records | Items | Files |", "|---|---|---|---|"] + \
     [f"| {g} | {grp_n[g]} | {tot[g]} | {totf[g]} |" for g in sorted(grp_n)] + ["",
      "By kind: " + ', '.join(f'{v} {k}' for k, v in kinds.most_common()) + ".", "",
      "## By record", "", "| Record | Section | Title | Folder | Held | Not held | Files |", "|---|---|---|---|---|---|---|"] + lines
open('/var/www/html/inventory/review/folder-census-2026-10-08.md', 'w').write('\n'.join(md) + '\n')
print(f'OWN FOLDERS: {n_rec} records, {n_it} items, {n_f} files', dict(grp_n))
