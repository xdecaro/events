#!/usr/bin/env python3
from pathlib import Path
import re
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
    'component/admin/sql/updates/mysql/1.1.0.sql',
    'component/admin/sql/updates/mysql/1.1.1.sql',
    'component/admin/services/provider.php',
    'component/admin/src/Helper/CoreUiHelper.php',
    'component/admin/src/Model/InformationModel.php',
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

if v != '1.1.1':
    errs.append('unexpected release version ' + v)
if f'<version>{v}</version>' not in (R / 'component/decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('component version mismatch')
if f'<version>{v}</version>' not in (R / 'package/pkg_decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('package version mismatch')
if f'<version>{v}</version>' not in (R / 'updates/pkg_decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('update feed version mismatch')

sql = (R / 'component/admin/sql/install.mysql.utf8mb4.sql').read_text(encoding='utf-8').upper()
if 'DROP TABLE' in sql:
    errs.append('destructive SQL')

for root in (R / 'component', R / 'package'):
    for path in root.rglob('*.php'):
        text = path.read_text(encoding='utf-8')
        if re.search(r'Xdecaro\\+Core', text):
            errs.append('legacy Core namespace in ' + str(path.relative_to(R)))

runtime = {
    'CoreUiHelper': (R / 'component/admin/src/Helper/CoreUiHelper.php').read_text(encoding='utf-8'),
    'InformationModel': (R / 'component/admin/src/Model/InformationModel.php').read_text(encoding='utf-8'),
    'CoreIntegrationService': (R / 'component/admin/src/Service/CoreIntegrationService.php').read_text(encoding='utf-8'),
    'package installer': (R / 'package/script.php').read_text(encoding='utf-8'),
}

for marker in ['xdecaro\\Core\\Asset\\AssetService', 'xdecaro\\Core\\Version', "'1.3.0'"]:
    if marker not in runtime['CoreUiHelper']:
        errs.append('Core UI contract missing ' + marker)
for marker in ['xdecaro\\Core\\Integration\\EntityReference', 'xdecaro\\Core\\Integration\\RelationReference', "MINIMUM_CORE = '1.3.0'"]:
    if marker not in runtime['CoreIntegrationService']:
        errs.append('Core integration contract missing ' + marker)
for marker in ['pkg_xdecarocore', "MINIMUM_CORE = '1.3.0'", 'xdecaro\\Core\\Version']:
    if marker not in runtime['package installer']:
        errs.append('Core installer contract missing ' + marker)

site = (R / 'component/site/src/Service/RegistrationService.php').read_text(encoding='utf-8')
for marker in ['FOR UPDATE', 'transactionStart', 'waitlist']:
    if marker not in site:
        errs.append('registration concurrency contract missing ' + marker)

if errs:
    print('\n'.join(errs))
    sys.exit(1)
print('Events validation OK')
