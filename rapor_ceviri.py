#!/usr/bin/env python3
import re, os

base = r"C:\wamp64\www\jaayquick.com"
files = {
  'tr': open(os.path.join(base, 'lang', 'tr.php'), encoding='utf-8').read(),
  'en': open(os.path.join(base, 'lang', 'en.php'), encoding='utf-8').read(),
  'ar': open(os.path.join(base, 'lang', 'ar.php'), encoding='utf-8').read(),
  'fr': open(os.path.join(base, 'lang', 'fr.php'), encoding='utf-8').read(),
}
keys = {}
for code, content in files.items():
    keys[code] = sorted(set(re.findall(r"=>\s*['\"]([^'\"]+)['\"]", content)))

print(f"TR total keys: {len(keys['tr'])}")
print(f"EN total keys: {len(keys['en'])}")
print(f"AR total keys: {len(keys['ar'])}")
print(f"FR total keys: {len(keys['fr'])}")

for c in ['en', 'ar', 'fr']:
    miss = [k for k in keys['tr'] if k not in keys[c]]
    extra = [k for k in keys[c] if k not in keys['tr']]
    print(f"\n=== {c.upper()} ===")
    print(f"Eksik anahtar sayisi: {len(miss)}")
    if miss:
        print("Ornek eksik:", miss[:20])
    print(f"Fazla anahtar sayisi: {len(extra)}")
    if extra:
        print("Ornek fazla:", extra[:10])
