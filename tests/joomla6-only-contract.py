#!/usr/bin/env python3
from pathlib import Path
import sys
import xml.etree.ElementTree as ET

R = Path(__file__).resolve().parents[1]
errors = []

version = (R / 'VERSION').read_text(encoding='utf-8').strip()
if version != '1.3.2':
    errors.append(f'expected Events 1.3.2, found {version}')

component = ET.parse(R / 'component/decaroevents.xml').getroot()
package = ET.parse(R / 'package/pkg_decaroevents.xml').getroot()
feed = ET.parse(R / 'updates/pkg_decaroevents.xml').getroot().find('update')

for label, root in (('component', component), ('package', package), ('update feed', feed)):
    target = root.find('targetplatform') if root is not None else None
    if target is None or (target.get('name') or '') != 'joomla' or (target.get('version') or '') != '6.*':
        errors.append(f'{label} must target Joomla 6 only')

for label, root in (('component', component), ('package', package), ('update feed', feed)):
    php_min = root.find('php_minimum') if root is not None else None
    if php_min is None or (php_min.text or '').strip() != '8.3.0':
        errors.append(f'{label} must require PHP 8.3.0+')

installer = (R / 'package/script.php').read_text(encoding='utf-8')
for marker in ("MINIMUM_JOOMLA = '6.1.3'", 'JVERSION', 'version_compare'):
    if marker not in installer:
        errors.append(f'package installer missing Joomla 6.1.3 contract: {marker}')

ci = (R / '.github/workflows/ci.yml').read_text(encoding='utf-8')
editor_ci = (R / '.github/workflows/editor-runtime.yml').read_text(encoding='utf-8')
for label, text in (('CI', ci), ('Editor runtime', editor_ci)):
    if '5.4.8' in text or 'Joomla 5' in text:
        errors.append(f'{label} still contains Joomla 5 coverage')
    if '6.1.3' not in text:
        errors.append(f'{label} must cover Joomla 6.1.3')

readme = (R / 'README.md').read_text(encoding='utf-8')
if '- Joomla: 6.1.3+' not in readme:
    errors.append('README must declare Joomla 6.1.3+')
if '- PHP: 8.3+' not in readme:
    errors.append('README must declare PHP 8.3+')

if errors:
    print('\n'.join(errors))
    sys.exit(1)

print('Events Joomla 6-only contract OK')
