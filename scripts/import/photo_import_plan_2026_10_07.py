# Plan for the photograph import (7 October 2026). Runs in the DDEV container against /mnt/reggie (Reggie's SCVHistory folder,
# read-only). Read-only everywhere except storage/runtime/photo-import/plan.json. Replicates the census's choice of files
# (storage/runtime/photo-images/scan2.py and final.py): class A the best file, class B the flipbook page set or largest PDF,
# class C the file found by code, with #2987 taking lw2214a.jpg (the page's own picture, by checksum the file the Internet
# Archive holds as gif/lw2214.jpg) and #4435 taking its two pictures from Internet Archive captures.
import json,os,re,hashlib
R='/var/www/html'; M='/mnt/reggie'
cen=json.load(open(R+'/inventory/review/photo-images-census-2026-10-07.json'))['records']
man={}
for l in open(R+'/inventory/raw/scvhistory-manifest-2026-08-20.sha256',encoding='utf-8',errors='replace'):
    h,_,p=l.rstrip('\n').partition('  ./'); man['scvhistory.com/'+p]=h
for l in open(M+'/scvhistory-manifest-addendum-2026-10-04.sha256',encoding='utf-8',errors='replace'):
    h,_,p=l.rstrip('\n').partition('  ./')
    if p: man.setdefault('scvhistory.com/'+p,h)
existing={}
for a in json.load(open(R+'/storage/runtime/assets-dump.json')):
    if (a['folder'] or '')=='legacy/': existing[a['file'].lower()]=a['id']
def files(fd,pat):
    if not os.path.isdir(fd): return []
    return sorted(os.path.join(fd,f) for f in os.listdir(fd) if re.search(pat,f,re.I) and os.path.isfile(os.path.join(fd,f)) and not re.search(r'(^shot|t\.jpg$|thumb)',f,re.I))
SETS={'tif':('',r'\.tiff?$'),'jpg':('',r'\.jpe?g$'),'pdf':('',r'\.pdf\??$'),'data1-images':('/data1/images',r'\.jpe?g$'),'files-large':('/files/large',r'\.jpe?g$'),'large':('/large',r'\.jpe?g$')}
def best_per_page(fs):
    # one file per page: lw3686a.jpg and lw3686a_large.jpg are the same page; keep the largest file
    g={}
    for f in fs:
        k=re.sub(r'_(large|orig|super|full)(?=\.\w+$)','',os.path.basename(f).lower())
        if k not in g or os.path.getsize(f)>os.path.getsize(g[k]): g[k]=f
    return sorted(g.values(), key=lambda f: re.sub(r'_(large|orig|super|full)(?=\.\w+$)','',os.path.basename(f).lower()))
def item(abs_path, role, prefix=None):
    rel=abs_path[len(M)+1:]                     # scvhistory.com/...
    b=os.path.basename(rel).rstrip('?'); ext=os.path.splitext(b)[1].lower()
    web=re.sub(r'\.tiff?$','.jpg',b,flags=re.I)
    if prefix and not web.lower().startswith(prefix.lower()): web=prefix.lower()+'-'+web
    return {'src':abs_path,'legacySourcePath':'/'+rel.split('/',1)[1].rstrip('?'),'sha256':man.get(rel),'bytes':os.path.getsize(abs_path),
            'web':web,'kind':'pdf' if ext=='.pdf' else ('copy' if ext=='.gif' else 'image'),'role':role,'reuse':existing.get(web.lower())}
# The census's pick does not carry the record's code and may be decoration or a neighbour's picture: held for a read by hand.
HOLD={5649:'fredrobertwilson1996.jpg',5463:'circlejoverlay_on.jpg',5377:'sg19300501rodeo.jpg',5347:'ranchocamulosheader.jpg (site header)',
 4951:'veteranshistoricalplaza_animated.gif',4893:'lat083145.jpg',4413:'johnlscott_2014.jpg',3339:'lw2352_large.jpg (LW2352, not LW2353)',
 3321:'lw2341a.jpg',2939:'lw2158.jpg',2875:'lw2142bb_large.jpg (no code on the record)',2873:'lw2142ba_large.jpg (no code on the record)',
 2741:'lw2042.jpg',2721:'lw20191207scvhs_large.jpg (no code on the record)',2703:'ranchocamulosheader.jpg (site header)'}
plan=[];problems=[];held=[]
for r in cen:
    c=r['class'][0]
    if c not in 'ABC' and r['id']!=4435: continue
    p={'id':r['id'],'code':r['code'],'class':c,'legacyUrl':r['legacyUrl'],'kindCensus':r['kind'],'items':[]}
    if r['id']==4435:
        for f in ('lw2724a.jpg','lw2724b.jpg'):
            p['items'].append({'src':R+'/storage/runtime/photo-import/wayback/'+f,'legacySourcePath':'/gif/'+f,'sha256':None,'bytes':os.path.getsize(R+'/storage/runtime/photo-import/wayback/'+f),'web':f,'kind':'image','role':'page','reuse':existing.get(f),'wayback':True})
    elif r['id'] in HOLD:
        p['held']=HOLD[r['id']]; held.append(p); continue
    elif r['id']==4621:
        p['items'].append(item(M+'/scvhistory.com/scvhistory/lw2875.pdf?','page'))
    elif r['id']==2987:
        p['items'].append(item(M+'/scvhistory.com/scvhistory/files/lw2214/lw2214a.jpg','page'))
    elif c in 'AC' and r.get('best'):
        p['items'].append(item(M+'/'+r['best']['path'],'page'))
    elif c=='B':
        for d in r['document']:
            if r['id']==2773: fs=sorted(os.path.join(M,'scvhistory.com/gif',f) for f in os.listdir(M+'/scvhistory.com/gif') if re.fullmatch(r'lw2083[a-q]\.jpg',f))
            elif 'folder' not in d: fs=[M+'/'+d['file']]; d={'files':1}
            elif d['set']=='pdf' and d.get('file'): fs=[M+'/'+d['file']]
            else:
                fd=M+'/'+d['folder']; sub,pat=SETS[d['set']]; fs=files(fd+sub,pat)
                if d['set']=='pdf': fs=sorted(fs,key=os.path.getsize)[-1:]
            if len(fs)!=d['files'] and r['id']!=2773: problems.append((r['id'],d.get('folder'),d.get('set'),'census %d, now %d'%(d['files'],len(fs))))
            if d.get('set') in ('jpg','data1-images','files-large','large') or r['id']==2773: fs=best_per_page(fs)
            pre=os.path.basename(d['folder']) if d.get('folder') and r['id']!=2773 else None
            for f in fs: p['items'].append(item(f,'page',pre))
    if r['id']==5639:
        for f in ('lw3786_centerville.pdf?','lw3786_munsell.pdf?'): p['items'].append(item(M+'/scvhistory.com/scvhistory/'+f,'document'))
    if not p['items']: problems.append((r['id'],'no files'))
    for it in p['items']:
        if not os.path.isfile(it['src']): problems.append((r['id'],'missing',it['src']))
    plan.append(p)
json.dump({'records':plan,'held':held,'problems':problems},open(R+'/storage/runtime/photo-import/plan.json','w'),indent=1)
from collections import Counter
print('held',len(held)); print('records',len(plan),Counter(p['class'] for p in plan))
its=[i for p in plan for i in p['items']]
print('files',len(its),Counter(i['kind'] for i in its),'GB on Reggie',round(sum(i['bytes'] for i in its)/1e9,2))
print('tif sources',sum(1 for i in its if re.search(r'\.tiff?$',i['src'],re.I)))
print('reuse an existing asset',sum(1 for i in its if i['reuse']),'records with any reuse',sum(1 for p in plan if any(i['reuse'] for i in p['items'])))
print('no manifest checksum',sum(1 for i in its if not i['sha256'] and not i.get('wayback')))
dup=Counter(i['web'].lower() for i in its); print('web names used by more than one record/item',sum(1 for k,v in dup.items() if v>1))
print('problems',len(problems)); [print(' ',x) for x in problems[:30]]
