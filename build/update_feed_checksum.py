#!/usr/bin/env python3
from pathlib import Path
import hashlib,re
R=Path(__file__).resolve().parents[1];v=(R/'VERSION').read_text().strip();p=R/'dist'/f'pkg_decaroevents_{v}.zip';h=hashlib.sha256(p.read_bytes()).hexdigest();f=R/'updates/pkg_decaroevents.xml';s=f.read_text();s=re.sub(r'<sha256>[0-9a-f]{64}</sha256>',f'<sha256>{h}</sha256>',s);f.write_text(s);print(h)
