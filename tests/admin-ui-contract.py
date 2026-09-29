#!/usr/bin/env python3
from pathlib import Path
import sys
import xml.etree.ElementTree as ET

R = Path(__file__).resolve().parents[1]
errors = []

required = [
    'component/admin/src/Model/DashboardModel.php',
    'component/admin/src/View/Dashboard/HtmlView.php',
    'component/admin/tmpl/dashboard/default.php',
    'component/admin/forms/filter_events.xml',
    'component/admin/forms/filter_sessions.xml',
    'component/admin/forms/filter_registrations.xml',
]

for rel in required:
    if not (R / rel).is_file():
        errors.append(f'missing admin UI file: {rel}')

manifest = ET.parse(R / 'component/decaroevents.xml').getroot()
submenu = manifest.find('./administration/submenu')
views = [item.get('view') for item in submenu.findall('menu')] if submenu is not None else []
if not views or views[0] != 'dashboard':
    errors.append('dashboard must be the first administrator submenu view')

controller = (R / 'component/admin/src/Controller/DisplayController.php').read_text(encoding='utf-8')
if "default_view = 'dashboard'" not in controller and "default_view='dashboard'" not in controller:
    errors.append('administrator default view must be dashboard')

version = (R / 'VERSION').read_text(encoding='utf-8').strip()
readme = (R / 'README.md').read_text(encoding='utf-8')
if f'- Versione: `{version}`' not in readme:
    errors.append('README version is not synchronized with VERSION')

information = (R / 'component/admin/src/Model/InformationModel.php').read_text(encoding='utf-8')
if f"'version' => '{version}'" not in information:
    errors.append('InformationModel version is not synchronized with VERSION')

list_contracts = {
    'events': ('filter_events', 'filter.published'),
    'sessions': ('filter_sessions', 'filter.event_id'),
    'registrations': ('filter_registrations', 'filter.status'),
}

for view, (filter_form, model_marker) in list_contracts.items():
    model_path = R / f'component/admin/src/Model/{view.capitalize()}Model.php'
    view_path = R / f'component/admin/src/View/{view.capitalize()}/HtmlView.php'
    template_path = R / f'component/admin/tmpl/{view}/default.php'
    filter_path = R / f'component/admin/forms/{filter_form}.xml'

    if model_path.is_file():
        model = model_path.read_text(encoding='utf-8')
        for marker in ("protected $filter_fields", model_marker, "list.ordering", "list.direction"):
            if marker not in model:
                errors.append(f'{view} model missing SearchTools/order contract: {marker}')

    if view_path.is_file():
        html_view = view_path.read_text(encoding='utf-8')
        for marker in ("get('FilterForm')", "get('ActiveFilters')", "get('State')"):
            if marker not in html_view:
                errors.append(f'{view} view missing SearchTools state: {marker}')

    if template_path.is_file():
        template = template_path.read_text(encoding='utf-8')
        for marker in ("searchtools.default", "searchtools.sort", "d-none d-md-block", "d-md-none"):
            if marker not in template:
                errors.append(f'{view} template missing responsive/filter contract: {marker}')
        for marker in ('name="cid[]"', 'mobile-cb<?= (int) $item->id ?>', 'Joomla.isChecked(this.checked)'):
            if marker not in template:
                errors.append(f'{view} mobile selection contract missing: {marker}')

    if filter_path.is_file():
        try:
            filter_root = ET.parse(filter_path).getroot()
            if filter_root.find('.//field[@name="search"]') is None:
                errors.append(f'{view} filter form missing search field')
            for field_name in ('fullordering', 'limit'):
                field = filter_root.find(f'.//field[@name="{field_name}"]')
                classes = (field.get('class', '') if field is not None else '').split()
                if field is None or 'js-select-submit-on-change' not in classes:
                    errors.append(f'{view} {field_name} must auto-submit')
            filter_xml = filter_path.read_text(encoding='utf-8')
            if 'js-select-submit-on-change' not in filter_xml:
                errors.append(f'{view} filter form missing auto-submit select contract')
        except Exception as exc:
            errors.append(f'{view} filter XML invalid: {exc}')

if errors:
    print('\n'.join(errors))
    sys.exit(1)

print('Events admin UI contract OK')
