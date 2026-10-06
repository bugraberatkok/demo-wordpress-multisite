// assets/site.css'i uretir: keresteci sitelerinin (ithalkeresteci,
// kavakkeresteci) ortak CSS dosyasi. Sira, eskiden ayri yuklenen dosyalarin
// sirasiyla ayni:
//   1. assets/fonts/archivo.css  (yerel Archivo @font-face; eskiden Google Fonts)
//   2. assets/tailwind.css       (build/ klasorunde Tailwind ile derlenir)
// Cocuk temanin style.css'i (yalnizca site renk degiskenleri, ~1 KB) ayri
// istek olmasin diye functions.php'de satir ici eklenir; bu dosyaya girmez,
// boylece cocuk tema degisince yeniden derleme gerekmez.
//
//   node wp-content/themes/kereste-base/scripts/build-css.mjs
// (build/ klasorundeki "npm run build:kereste" Tailwind'den sonra bunu da calistirir.)
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
].join('\n') + '\n';

writeFileSync(resolve(theme, 'assets/site.css'), css);
console.log(`assets/site.css yazildi (${css.length} bayt)`);
