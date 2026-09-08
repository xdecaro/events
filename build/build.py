#!/usr/bin/env python3
from __future__ import annotations
import hashlib
import shutil
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
VERSION = (ROOT / 'VERSION').read_text(encoding='utf-8').strip()
DIST = ROOT / 'dist'
FIXED = (1980, 1, 1, 0, 0, 0)

def add(archive: zipfile.ZipFile, name: str, data: bytes) -> None:
    info = zipfile.ZipInfo(name, FIXED)
    info.compress_type = zipfile.ZIP_DEFLATED
    info.external_attr = 0o100644 << 16
    archive.writestr(info, data)

def zip_tree(source: Path, destination: Path) -> None:
    with zipfile.ZipFile(destination, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for path in sorted(p for p in source.rglob('*') if p.is_file()):
            add(archive, path.relative_to(source).as_posix(), path.read_bytes())

def sha256(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open('rb') as handle:
        for chunk in iter(lambda: handle.read(1024 * 1024), b''):
            digest.update(chunk)
    return digest.hexdigest()

def main() -> None:
    shutil.rmtree(DIST, ignore_errors=True)
    DIST.mkdir(parents=True)
    component = DIST / f'com_decaroevents_{VERSION}.zip'
    package = DIST / f'pkg_decaroevents_{VERSION}.zip'
    zip_tree(ROOT / 'component', component)
    entries = {
        'pkg_decaroevents.xml': (ROOT / 'package/pkg_decaroevents.xml').read_bytes(),
        'script.php': (ROOT / 'package/script.php').read_bytes(),
        'com_decaroevents.zip': component.read_bytes(),
    }
    with zipfile.ZipFile(package, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for name, data in sorted(entries.items()):
            add(archive, name, data)
    (DIST / 'SHA256SUMS.txt').write_text(
        f'{sha256(component)}  {component.name}\n{sha256(package)}  {package.name}\n',
        encoding='utf-8',
    )
    print('Package SHA-256:', sha256(package))

if __name__ == '__main__':
    main()
