import os,re,sys,pickle
ROOT='/Volumes/Reggie/SCVHistory/scvhistory.com'
CACHE='/private/tmp/claude-501/-Users-nathanimhoff-scvhistory/5d9d9eff-2231-41a5-bd94-c5470c596ae4/scratchpad/mirror.pkl'
def load():
    if os.path.exists(CACHE):
        return pickle.load(open(CACHE,'rb'))
    d={}
    for dp,dn,fn in os.walk(ROOT):
        for f in fn:
            if f.lower().endswith(('.htm','.html','.txt','.shtml','.php','.asp')):
                p=os.path.join(dp,f)
                try:
                    b=open(p,'rb').read()
                except Exception: continue
                t=b.decode('cp1252',errors='replace')
                t=re.sub(r'<[^>]+>',' ',t); t=re.sub(r'&nbsp;',' ',t); t=re.sub(r'\s+',' ',t)
                d[os.path.relpath(p,ROOT)]=t
    pickle.dump(d,open(CACHE,'wb'))
    return d
d=load()
pat=re.compile(sys.argv[1],re.I)
ctx=int(sys.argv[2]) if len(sys.argv)>2 else 150
mx=int(sys.argv[3]) if len(sys.argv)>3 else 60
n=0
for k,t in sorted(d.items()):
    ms=list(pat.finditer(t))
    if ms:
        n+=1
        if n<=mx:
            m=ms[0]
            print(f"## {k} ({len(ms)}): ...{t[max(0,m.start()-ctx):m.end()+ctx]}...")
print("FILES",n,"of",len(d))
