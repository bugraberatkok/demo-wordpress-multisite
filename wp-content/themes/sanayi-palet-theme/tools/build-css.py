#!/usr/bin/env python3
"""
assets/css kaynaklarini sayfa turune gore tek dosyada birlestirir (assets/css/site-*.css).

Kaynak dosyalar duzenlenir, sonra bu betik calistirilir:

    python tools/build-css.py          # assets/css/site-*.css yeniden uretilir
    python tools/build-css.py --check  # birlesik dosyalar guncel degilse cikis kodu 1

Sira, eski wp_enqueue_style sirasinin aynisidir (functions.php:
sanayi_palet_assets); icerik degistirilmez, yalnizca arka arkaya eklenir.
faq.css (/sss/) ayri yuklenmeye devam eder.
"""
import pathlib
import sys

THEME = pathlib.Path(__file__).resolve().parent.parent

CORE = [
    'assets/css/fonts.css',
    'assets/css/tokens.css',
    'style.css',
    'assets/css/base.css',
    'assets/css/header.css',
    'assets/css/footer.css',
]

BUNDLES = {
    # Ana sayfa: damga (hero.css) + bolumler.
    'assets/css/site-front.css': CORE + ['assets/css/hero.css', 'assets/css/home.css'],
    # Hakkimizda: damga + ic sayfa stilleri.
    'assets/css/site-about.css': CORE + ['assets/css/hero.css', 'assets/css/home.css', 'assets/css/pages.css'],
    # Diger butun sayfalar (urunler, iletisim, blog, yazi, sss, 404).
    'assets/css/site-inner.css': CORE + ['assets/css/home.css', 'assets/css/pages.css'],
}


def build(files):
    parts = ['/* Uretilmis dosya: elle duzenlemeyin. Kaynak: assets/css, betik: tools/build-css.py */\n']
    for rel in files:
        text = (THEME / rel).read_text(encoding='utf-8-sig')
        parts.append('\n/* ---- %s ---- */\n' % rel)
        parts.append(text if text.endswith('\n') else text + '\n')
    return ''.join(parts)


def main():
    check = '--check' in sys.argv
    stale = []
    for out, files in BUNDLES.items():
        path = THEME / out
        css = build(files)
        current = path.read_text(encoding='utf-8') if path.exists() else None
        if current == css:
            continue
        stale.append(out)
        if not check:
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_text(css, encoding='utf-8', newline='\n')
            print('yazildi:', out)
    if check and stale:
        print('guncel degil:', ', '.join(stale))
        return 1
    if not stale:
        print('birlesik css guncel')
    return 0


if __name__ == '__main__':
    sys.exit(main())
