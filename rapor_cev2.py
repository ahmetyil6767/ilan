#!/usr/bin/env python3
import re, os
base = r"C:\wamp64\www\jaayquick.com"
files = {c: open(os.path.join(base,"lang",c+".php"), encoding="utf-8").read() for c in ["tr","en","ar","fr"]}
keys = {}
for c, content in files.items():
    pairs = re.findall(r"'([^']+)'\s*=>\s*'([^']*)'", content)
    keys[c] = sorted({p[0] for p in pairs})
print("TR:", len(keys["tr"]), "EN:", len(keys["en"]), "AR:", len(keys["ar"]), "FR:", len(keys["fr"]))
for c in ["en","ar","fr"]:
    miss = [k for k in keys["tr"] if k not in keys[c]]
    extra = [k for k in keys[c] if k not in keys["tr"]]
    print(f"\n=== {c.upper()} ===")
    print(f"Eksik (TR'de olup {c}'de olmayan): {len(miss)}")
    print("Ornek:", miss[:15])
    print(f"Fazla ({c}'de olup TR'de olmayan): {len(extra)}")
    print("Ornek:", extra[:10])
