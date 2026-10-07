import json, os, re, time, urllib.request
w = json.load(open('storage/runtime/wayback223.json'))
out = 'storage/runtime/wayback137'
UA = 'SCVHistory-archive-research/1.0 (Wayback reading)'
n = 0
for legacy, url in w.items():
    if not url: continue
    raw = re.sub(r'/web/(\d+)/', r'/web/\1id_/', url)
    fn = os.path.join(out, legacy.strip('/').replace('/', '__'))
    if os.path.exists(fn) and os.path.getsize(fn) > 0: continue
    for attempt in range(3):
        try:
            req = urllib.request.Request(raw, headers={'User-Agent': UA})
            data = urllib.request.urlopen(req, timeout=60).read()
            import gzip
            data = gzip.decompress(data) if data[:2] == b"\x1f\x8b" else data
            open(fn, 'wb').write(data); n += 1; break
        except Exception as e:
            print('ERR', legacy, e); time.sleep(5)
    time.sleep(1.5)
print('fetched', n)
