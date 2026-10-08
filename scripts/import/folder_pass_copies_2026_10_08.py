# Web copies for the folder pass (8 October 2026), the photograph import's copier (photo_import_copies_2026_10_07.py) pointed at
# plan2.json and storage/runtime/photo-import/new2/; office files are copied as they are.
# Web copies for the photograph import (7 October 2026). Runs in the DDEV container. Reads the masters on /mnt/reggie
# (read-only) and the plan, writes only storage/runtime/photo-import/new/. The rule, as for the magnifier masters: 2,400 pixels
# on the long side at most, JPEG quality 82; a strip more than twice as long as it is wide gets the pixel count of a 2,400 by
# 1,800 picture instead. Never enlarged. PNG stays PNG; a PDF is copied as it is, and its first page is drawn as a cover.
# Skips a copy already made, so it resumes.
import json,os,subprocess,re,sys
from concurrent.futures import ThreadPoolExecutor
R='/var/www/html'; OUT=R+'/storage/runtime/photo-import/new2'; os.makedirs(OUT,exist_ok=True)
plan=json.load(open(R+'/storage/runtime/photo-import/plan2.json'))
LIM=['-limit','memory',os.environ.get('MEM','1500MiB'),'-limit','map','4GiB','-limit','disk',os.environ.get('DISK','6GiB'),'-limit','area','2GP','-limit','width','100KP','-limit','height','100KP']
def dims(p):
    o=subprocess.run(['identify','-ping','-format','%w %h\n',p+'[0]'],capture_output=True,text=True).stdout.split()
    return (int(o[0]),int(o[1])) if len(o)>=2 else (None,None)
def geom(w,h):
    if max(w,h)/max(1,min(w,h))>2: return '4320000@>'
    return '2400x2400>'
def make(it):
    dst=OUT+'/'+it['web']
    if os.path.exists(dst) and os.path.getsize(dst)>0: return (it['web'],'exists')
    src=it['src']; tmp=dst+'.part'
    try:
        if it['kind']=='office':
            subprocess.run(['cp',src,tmp],check=True); os.rename(tmp,dst); return (it['web'],'copied')
        if it['kind']=='pdf':
            subprocess.run(['cp',src,tmp],check=True); os.rename(tmp,dst)
            cov=OUT+'/'+re.sub(r'\.pdf$','',it['web'],flags=re.I)+'-p1.jpg'
            if not os.path.exists(cov):
                subprocess.run(['convert']+LIM+['-density','150',src+'[0]','-background','white','-alpha','remove','-colorspace','sRGB','-resize','2400x2400>','-quality','82',cov+'.part.jpg'],check=True,capture_output=True)
                os.rename(cov+'.part.jpg',cov)
            return (it['web'],'pdf+cover')
        w,h=dims(src)
        if not w: return (it['web'],'ERROR unreadable')
        if it['web'].lower().endswith('.png'):
            subprocess.run(['convert']+LIM+[src+'[0]','-resize',geom(w,h),'png:'+tmp],check=True,capture_output=True)
        else:
            pre=['-scale','4800x4800>'] if w*h>40e6 and geom(w,h)!='4320000@>' else []
            jd=['-define','jpeg:size=%dx%d'%(min(w,max(4800,w//8)),min(h,max(4800,h//8)))] if re.search(r'\.jpe?g$',src,re.I) and w*h>40e6 else []
            subprocess.run(['convert']+LIM+jd+[src+'[0]','-auto-orient']+pre+['-colorspace','sRGB','-resize',geom(w,h),'-quality','82','jpg:'+tmp],check=True,capture_output=True)
        os.rename(tmp,dst); return (it['web'],'made')
    except subprocess.CalledProcessError as e:
        if os.path.exists(tmp): os.remove(tmp)
        return (it['web'],'ERROR '+(e.stderr or b'').decode(errors='replace')[:200])
todo=[i for p in plan['records'] for i in p['items'] if not i['reuse']]
# pass 'small': everything but TIFFs, four at a time; pass 'tif': TIFFs one at a time with more memory
PASS=sys.argv[1] if len(sys.argv)>1 else 'small'
istif=lambda i: re.search(r'\.tiff?$',i['src'],re.I)
todo=[i for i in todo if (istif(i) if PASS=='tif' else not istif(i))]
WORKERS=1 if PASS=='tif' else 4
res=[]
with ThreadPoolExecutor(WORKERS) as ex:
    for k,(name,st) in enumerate(ex.map(make,todo),1):
        res.append((name,st))
        if st.startswith('ERROR'): print(name,st,flush=True)
        if k%100==0: print(k,'of',len(todo),flush=True)
from collections import Counter
print(Counter(s.split(' ')[0] for _,s in res))
json.dump(res,open(R+'/storage/runtime/photo-import/copies2-log-'+PASS+'.json','w'))
