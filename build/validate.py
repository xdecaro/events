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
    'component/admin/sql/updates/mysql/1.1.2.sql',
    'component/admin/sql/updates/mysql/1.2.0.sql',
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

if v != '1.2.0':
    errs.append('unexpected release version ' + v)
if f'<version>{v}</version>' not in (R / 'component/decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('component version mismatch')
if f'<version>{v}</version>' not in (R / 'package/pkg_decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('package version mismatch')
if f'<version>{v}</version>' not in (R / 'updates/pkg_decaroevents.xml').read_text(encoding='utf-8'):
    errs.append('update feed version mismatch')

component_root = ET.parse(R / 'component/decaroevents.xml').getroot()
install_sql = component_root.find('./install/sql/file')
if install_sql is None or (install_sql.get('driver') or '') != 'mysql' or (install_sql.get('charset') or '') != 'utf8':
    errs.append('Joomla install SQL manifest must use driver="mysql" charset="utf8"')
if install_sql is not None and (install_sql.text or '').strip() != 'sql/install.mysql.utf8mb4.sql':
    errs.append('install SQL path changed unexpectedly')

form_root = ET.parse(R / 'component/admin/forms/event.xml').getroot()
description = form_root.find('.//field[@name="description"]')
if description is None:
    errs.append('event description field missing')
else:
    if (description.get('type') or '').lower() != 'editor':
        errs.append('event description must use Joomla editor form field')
    if (description.get('editor') or '') != 'decaroeditor|none':
        errs.append('event description editor fallback contract changed')
    if (description.get('filter') or '') != 'raw':
        errs.append('event description filter changed unexpectedly')

sql = (R / 'component/admin/sql/install.mysql.utf8mb4.sql').read_text(encoding='utf-8')
repair = (R / 'component/admin/sql/updates/mysql/1.1.2.sql').read_text(encoding='utf-8')
for text, label in ((sql, 'install schema'), (repair, '1.1.2 repair schema')):
    for marker in ('CREATE TABLE IF NOT EXISTS `#__decaroevents_events`', 'CREATE TABLE IF NOT EXISTS `#__decaroevents_sessions`', 'CREATE TABLE IF NOT EXISTS `#__decaroevents_registrations`', 'DEFAULT CHARSET=utf8mb4'):
        if marker not in text:
            errs.append(f'{label} missing {marker}')
    if re.search(r'\b(?:DROP\s+TABLE|TRUNCATE\s+TABLE)\b', text, re.I):
        errs.append('destructive SQL in ' + label)

for root in (R / 'component', R / 'package'):
    for path in root.rglob('*.php'):
        text = path.read_text(encoding='utf-8')
        if re.search(r'Xdecaro\\+Core', text):
            errs.append('legacy Core namespace in ' + str(path.relative_to(R)))
        if re.search(r'Xdecaro\\+Component\\+Decaroeditor|#__decaroeditor', text, re.I):
            errs.append('private Editor coupling in ' + str(path.relative_to(R)))

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
for marker in ['final class pkg_decaroeventsInstallerScript', 'pkg_xdecarocore', "MINIMUM_CORE = '1.3.0'", 'xdecaro\\Core\\Version', 'return false;']:
    if marker not in runtime['package installer']:
        errs.append('Core installer contract missing ' + marker)
if 'class PkgDecaroeventsInstallerScript' in runtime['package installer']:
    errs.append('incorrect legacy package installer class name remains')

site = (R / 'component/site/src/Service/RegistrationService.php').read_text(encoding='utf-8')
for marker in ['FOR UPDATE', 'transactionStart', 'waitlist']:
    if marker not in site:
        errs.append('registration concurrency contract missing ' + marker)

if errs:
    print('\n'.join(errs))
    sys.exit(1)
print('Events validation OK')
