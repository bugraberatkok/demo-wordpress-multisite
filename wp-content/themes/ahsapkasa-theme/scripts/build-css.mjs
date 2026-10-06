// assets/site.css'i uretir: sitenin tek CSS dosyasi. Sira, eskiden ayri
// ayri yuklenen dosyalarin sirasiyla ayni (cascade degismez):
//   1. assets/fonts/fonts.css
//   2. assets/tailwind.css
//   3. style.css
// Icerik degismez; yalnizca goreli url() adresleri assets/'e gore yeniden
// yazilir (ornegin font dosyalari: url(archivo-latin.woff2) -> url(fonts/...)).
//
//   node wp-content/themes/ahsapkasa-theme/scripts/build-css.mjs
// (build/ klasorundeki "npm run build:ahsapkasa" Tailwind'den sonra bunu da calistirir.)
//
// assets/site.css yoksa functions.php kaynaklari ayri ayri yukler. Yerel
// ortamda (WP_ENVIRONMENT_TYPE=local) kaynaklardan biri site.css'ten yeniyse
// de ayri yukler; yani duzenleme yerelde hemen gorunur, canliya gitmeden once
// bu betik calistirilmalidir.
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { resolve, dirname, relative, sep } from 'node:path';
import { fileURLToPath } from 'node:url';

const theme = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const out = resolve(theme, 'assets/site.css');

// functions.php'deki listeyle ayni sira.
const sources = [ 'assets/fonts/fonts.css', 'assets/tailwind.css', 'style.css' ];

const rebase = (css, file) =>
	css.replace(/url\((['"]?)(?!(?:https?:|data:|\/|#))([^'")]+)\1\)/g, (_, quote, path) => {
		const target = relative(dirname(out), resolve(dirname(resolve(theme, file)), path)).split(sep).join('/');
		return `url(${quote}${target}${quote})`;
	});

const css = [
	'/* Uretilen dosya: elle duzenlemeyin. Kaynaklar ve sira: scripts/build-css.mjs */',
	...sources.map((file) => rebase(readFileSync(resolve(theme, file), 'utf8').trim(), file)),
].join('\n') + '\n';

mkdirSync(dirname(out), { recursive: true });
writeFileSync(out, css);
console.log(`assets/site.css yazildi (${css.length} bayt)`);
