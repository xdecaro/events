#!/usr/bin/env python3
from pathlib import Path
import sys
import xml.etree.ElementTree as ET

R = Path(__file__).resolve().parents[1]
v = (R / 'VERSION').read_text(encoding='utf-8').strip()
errs = []

required = [
    'component/decaroevents.xml',
    'package/pkg_decaroevents.xml',
    'package/script.php',
    'component/admin/sql/install.mysql.utf8mb4.sql',
    'component/admin/services/provider.php',
    'component/admin/src/Service/CoreIntegrationService.php',
    'component/site/src/Service/RegistrationService.php',
    'updates/pkg_decaroevents.xml',
]
for p in required:
    if not (R / p).is_file():
        errs.append('missing ' + p)

xmls = [
    'component/decaroevents.xml',
    'package/pkg_decaroevents.xml',
    'component/admin/access.xml',
    'component/admin/config.xml',
    'component/admin/forms/event.xml',
    'component/admin/forms/session.xml',
    'component/admin/forms/registration.xml',
    'updates/pkg_decaroevents.xml',
]
for p in xmls:
    try:
        ET.parse(R / p)
    except Exception as exc:
        errs.append(f'xml {p}: {exc}')

if f'<version>{v}</version>' not in (R / 'component/decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('component version mismatch')
if f'<version>{v}</version>' not in (R / 'package/pkg_decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('package version mismatch')

sql = (R / 'component/admin/sql/install.mysql.utf8mb4.sql').read_text(encoding='utf-8').upper()
if 'DROP TABLE' in sql:
    errs.append('destructive SQL')

texts = []
for p in R.rglob('*'):
    if p.is_file() and p.suffix in {'.php', '.md', '.xml', '.sql'}:
        texts.append(p.read_text(encoding='utf-8', errors='ignore'))
alltext = '\n'.join(texts)

for bad in ['com_decarocompetitions', '#__dcl_']:
    if bad in alltext:
        errs.append('forbidden coupling ' + bad)
for good in ['com_decarodcl', 'Xdecaro\\Core\\Asset\\AssetService', 'EntityReference', 'RelationReference']:
    if good not in alltext:
        errs.append('missing contract ' + good)

if errs:
    print('\n'.join(errs))
    sys.exit(1)
print('Events validation OK')
