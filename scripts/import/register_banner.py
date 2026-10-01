"""Register a received banner: copy it into web/banners/ and add its entry to
templates/_data/banners.json, in the form check_render.php enforces.

    python3 scripts/import/register_banner.py persons:323 "Cave Johnson Couts" \
        inventory/incoming/cave-johnson-couts-collection-banner.jpg cave-johnson-couts.jpg

Refuses to overwrite a different file or an existing entry. tool,
sourcePhotograph and generatedOn stay null until Nathan supplies them."""
import hashlib, json, shutil, sys, datetime, os
key, title, src, dest = sys.argv[1:5]
section, rid = key.split(':')
reg_path = 'templates/_data/banners.json'
reg = json.load(open(reg_path))
out = f'web/banners/{dest}'
sha = hashlib.sha256(open(src, 'rb').read()).hexdigest()
if key in reg:
    sys.exit(f'{key} already registered ({reg[key]["file"]}); nothing changed')
if os.path.exists(out) and hashlib.sha256(open(out, 'rb').read()).hexdigest() != sha:
    sys.exit(f'{out} exists and is a different file; nothing changed')
shutil.copyfile(src, out)
reg[key] = {'file': dest, 'sha256': sha, 'record': {'section': section, 'id': int(rid), 'title': title},
            'kind': 'ai-generated-illustration', 'style': 'engraved-style illustration on a map ground',
            'tool': None, 'sourcePhotograph': None, 'generatedOn': None,
            'commissionedBy': 'SCVHistory.com', 'commissionedByPerson': 'Nathan Imhoff',
            'received': datetime.date.today().isoformat(), 'receivedAs': src}
json.dump(reg, open(reg_path, 'w'), ensure_ascii=False, indent=1)
open(reg_path, 'a').write('\n')
print(f'registered {key} -> {out}, sha256 {sha[:16]}')
