# The 15 photographs held from the import of 7 October (inventory/review/photo-import-held-2026-10-07.md), as Nathan ruled on
# 8 October 2026: "take the 9 wrong picks from each record's own folder"; the six right picks as the census had them.
# Runs in the DDEV container; reads Reggie (/mnt/reggie, read-only), writes storage/runtime/photo-import/plan-held.json in
# plan.json's shape, for photo_held_copies and import_photograph_images_held_2026_10_08.php.
# The import's rules: one file per picture, the largest (_orig or _large over the plain file; the folder's own copy over its
# slideshow engine's data1/images); a flipbook's page images are the pictures and its PDF goes to the record's documents;
# a PDF with no page images gets its first page drawn as the cover. Generic page names take the record's code as a prefix.
import json,os,hashlib,re
R='/var/www/html'; M='/mnt/reggie/scvhistory.com'
man={}
for f in [R+'/inventory/raw/scvhistory-manifest-2026-08-20.sha256','/mnt/reggie/scvhistory-manifest-addendum-2026-10-04.sha256']:
    if os.path.exists(f):
        for l in open(f,encoding='utf-8',errors='replace'):
            h,_,p=l.rstrip('\n').partition('  ./'); man[p.rstrip('?')]=h
def item(rel,kind='image',role='page',web=None):
    src=M+'/'+rel
    if not os.path.exists(src) and os.path.exists(src+'?'): src+='?'
    assert os.path.exists(src),src
    h=man.get(rel) or hashlib.sha256(open(src,'rb').read()).hexdigest()
    w=web or os.path.basename(rel)
    if kind=='image' and re.search(r'\.tiff?$',w,re.I): w=re.sub(r'\.tiff?$','.jpg',w,flags=re.I)
    return dict(src=src,legacySourcePath='/'+rel,sha256=h,bytes=os.path.getsize(src),web=w,kind=kind,role=role,reuse=None)
def best(folder,stems):
    out=[]
    for s in stems:
        c=[f'{folder}/{s}{x}.jpg' for x in ('_orig','_large','')]
        c=[p for p in c if os.path.exists(M+'/'+p)]
        out.append(max(c,key=lambda p:os.path.getsize(M+'/'+p)))
    return out
S='scvhistory/files'
recs=[]
def rec(id,code,url,items): recs.append(dict(id=id,code=code,legacyUrl=url,items=items,**{'class':'held'}))
rec(5649,'LW3792','/scvhistory/lw3792.htm',[item(p) for p in best(S+'/lw3792',['lw3792'+c for c in 'abcdefghij'])])
rec(5463,'LW3618','/scvhistory/lw3618.htm',[item(p) for p in best('gif',['lw3618'+c for c in 'bcdefghijklmno'])])
rec(5377,'LW3531','/scvhistory/lw3531.htm',[item(f'{S}/lw3531/page{n:03d}.tif',web=f'lw3531-page{n:03d}.jpg') for n in range(1,17)]+[item(S+'/lw3531/lw3531.pdf','pdf','document')])
rec(5347,'LW3505','/scvhistory/lw3505.htm',[item(S+'/lw3505/lw3505.pdf','pdf','page')])
rec(4951,'LW3135','/scvhistory/lw3135.htm',[item(p) for p in best(S+'/lw3135',['lw3135'+c for c in 'abcdef'])])
rec(4893,'LW3086','/scvhistory/lw3086.htm',[item(p) for p in best(S+'/lw3086',['lw3086'+c for c in 'abcd'])]+[item(S+'/lw3086/lw3086.pdf','pdf','document')])
rec(3339,'LW2353','/scvhistory/lw2353.htm',[item(p) for p in best('gif',['lw2353'])])
rec(3321,'LW2342','/scvhistory/lw2342.htm',[item(p) for p in best('gif',['lw2342'])])
rec(2703,'LW1501','/scvhistory/lw1501.htm',[item(p) for p in best(S+'/lw1501',['lw1501'+c for c in ['a','b','c','d','e','eb','f','fb','g','h']])])
# the six the census had right
rec(4413,'LW2705','/scvhistory/lw2705.htm',[item('gif/mugs/johnlscott_2014.jpg')])
rec(2939,'LW2158','/scvhistory/lw2158.htm',[item(p) for p in best('gif',['lw2158'])])
rec(2741,'LW2042','/scvhistory/lw2042.htm',[item(p) for p in best('gif',['lw2042'])])
rec(2875,None,'/scvhistory/lw2142bb.htm',[item(p) for p in best('gif',['lw2142bb'])])
rec(2873,None,'/scvhistory/lw2142ba.htm',[item(p) for p in best('gif',['lw2142ba'])])
rec(2721,None,'/scvhistory/lw20191207scvhs.htm',[item(p) for p in best('gif',['lw20191207scvhs'])])
for r in recs:
    if not any(i['role']=='document' for i in r['items']) and not r['items']: raise SystemExit('empty '+str(r['id']))
json.dump(dict(records=recs,held=[]),open(R+'/storage/runtime/photo-import/plan-held.json','w'),indent=1)
for r in recs: print(r['id'],r['code'],len(r['items']),'|',', '.join(i['web']+('' if i['legacySourcePath'].endswith(i['web']) else ' <- '+i['legacySourcePath']) for i in r['items']))
