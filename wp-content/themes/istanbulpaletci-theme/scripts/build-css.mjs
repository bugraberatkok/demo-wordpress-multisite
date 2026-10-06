// assets/site.css'i uretir: sitenin tek CSS dosyasi. Sira, eskiden ayri
// yuklenen dosyalarin sirasiyla ayni:
//   1. assets/fonts/archivo.css  (yerel Archivo @font-face; eskiden Google Fonts)
//   2. assets/tailwind.css       (build/ klasorunde Tailwind ile derlenir)
//   3. style.css                 (tema basligi ve katmansiz urun sayfasi kurallari)
// Icerik degismez; yalnizca font adresleri assets/'e gore yeniden yazilir.
//
//   node wp-content/themes/istanbulpaletci-theme/scripts/build-css.mjs
// (build/ klasorundeki "npm run build:istanbulpaletci" Tailwind'den sonra bunu da calistirir.)
//
// style.css ya da tailwind.css site.css'ten yeniyse functions.php kaynaklari
// ayri ayri yukler; yani duzenleme yerelde hemen gorunur, ama canliya
// gitmeden once bu betik calistirilmalidir.
import { readFileSync, writeFileSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const theme = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const read = (file) => readFileSync(resolve(theme, file), 'utf8').trim();

const fonts = read('assets/fonts/archivo.css').replace(/url\((?!['"]?(?:https?:|data:|\/))(['"]?)/g, 'url($1fonts/');

const css = [
	'/* Uretilen dosya: elle duzenlemeyin. Kaynaklar ve sira: scripts/build-css.mjs */',
	fonts,
	read('assets/tailwind.css'),
	read('style.css'),
].join('\n') + '\n';

writeFileSync(resolve(theme, 'assets/site.css'), css);
console.log(`assets/site.css yazildi (${css.length} bayt)`);
