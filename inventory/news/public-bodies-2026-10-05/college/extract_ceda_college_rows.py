import json,os,re,sys,hashlib
import pandas as pd
RAW='/Users/nathanimhoff/scvhistory/inventory/raw/ceda'
man=json.load(open('/Users/nathanimhoff/scvhistory/inventory/elections/ceda-manifest.json'))
rows=[]; places={}
for y in range(1995,2025):
  name=f'CEDA{y}Data.xls'+('x' if y>=2011 else '')
  p=os.path.join(RAW,name)
  h=hashlib.sha256(open(p,'rb').read()).hexdigest()
  assert man['files'][name]['sha256']==h, name
  sheets=pd.read_excel(p,sheet_name=None,dtype=str)
  cand=[s for s in sheets if re.search('cand',s,re.I)][0]
  df=sheets[cand].fillna('')
  col={c.upper().strip():c for c in df.columns}
  g=lambda r,*ks: next((str(r[col[k]]).strip() for k in ks if k in col),'')
  for i,r in df.iterrows():
    co=g(r,'CO','CO#').split('.')[0]
    if co!='19': continue
    place=re.sub(r'\s+',' ',g(r,'PLACE').lower()).strip()
    if 'clarita' in place or 'canyons' in place:
      places[place]=places.get(place,0)+1
    if not re.search(r'clarita.*(college|comm)|canyons',place): continue
    num=lambda s: int(float(s)) if s not in ('','nan') else None
    rows.append({'year':y,'date':g(r,'DATE')[:10],'place':g(r,'PLACE'),'office':g(r,'OFFICE'),'area':g(r,'AREA').split('.')[0],'term':g(r,'TERM'),'seats':num(g(r,'VOTE#')),'first':g(r,'FIRST','FIRSTNAME'),'last':g(r,'LAST','LASTNAME'),'votes':num(g(r,'VOTES')),'total':num(g(r,'TOTVOTES')),'elected':g(r,'ELECTED'),'incumbent':g(r,'INCUMB','INC'),'designation':g(r,'BALDESIG'),'file':name,'row':int(i)+2})
print(json.dumps(places,indent=0),file=sys.stderr)
json.dump(rows,open(sys.argv[1],'w'),indent=1,ensure_ascii=False)
print(len(rows),file=sys.stderr)
