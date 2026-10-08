# Read-only: every page on Reggie (outside scvhistory/files) and the files/<folder> it links to.
import os,re,json,html
ROOT='/mnt/reggie/scvhistory.com'
folders={f.lower():f for f in os.listdir(ROOT+'/scvhistory/files') if os.path.isdir(ROOT+'/scvhistory/files/'+f)}
rx=re.compile(r'files/([^/"\'\s>]+)/',re.I)
links={}
for dp,ds,fs in os.walk(ROOT):
    if dp.startswith(ROOT+'/scvhistory/files'): ds[:]=[]; continue
    for f in fs:
        if not re.search(r'\.html?\??$',f,re.I): continue
        p=os.path.join(dp,f); t=open(p,encoding='latin-1').read()
        hit={folders[m.lower()] for m in rx.findall(html.unescape(t)) if m.lower() in folders}
        if hit: links[p[len(ROOT):].rstrip('?')]=sorted(hit)
json.dump(links,open('/var/www/html/storage/runtime/photo-import/folder-links.json','w'),indent=1)
print(len(links),'pages link into files/')
