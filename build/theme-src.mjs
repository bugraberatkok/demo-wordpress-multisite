// Kaynagi tema icinde duran temalari (ornegin ahsapambalaj-theme/assets/src/
// tailwind.css) bu klasordeki Tailwind ile derler. Kaynak dosya `@import
// "tailwindcss"` ile baslar; Tailwind paketi yalnizca burada kurulu oldugu
// icin kaynak, @source yolu duzeltilerek gecici bir dosyaya kopyalanir,
// derlenir ve gecici dosya silinir. Tema kaynagi degismez.
//
//   node theme-src.mjs <tema-klasoru>        ornek: node theme-src.mjs ahsapambalaj-theme
import { readFileSync, writeFileSync, rmSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import { resolve, relative, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const theme = process.argv[2];

if (!theme) {
	console.error('Kullanim: node theme-src.mjs <tema-klasoru>');
	process.exit(1);
}

const themeDir = resolve(here, '../wp-content/themes', theme);
const source = resolve(themeDir, 'assets/src/tailwind.css');
const output = resolve(themeDir, 'assets/tailwind.css');
const temp = resolve(here, `.tmp-${theme}.css`);

// @source yollari kaynak dosyaya goreli; gecici dosyaya gore yeniden yazilir.
const css = readFileSync(source, 'utf8').replace(/@source\s+"([^"]+)"/g, (_, path) => {
	const absolute = resolve(dirname(source), path);
	return `@source "${relative(here, absolute).replace(/\\/g, '/')}"`;
});

writeFileSync(temp, css);

try {
	// Windows'ta npx bir .cmd dosyasi; kabukla calistirilir. Yollar tirnakli.
	const result = spawnSync(`npx tailwindcss -i "${temp}" -o "${output}" --minify`, { cwd: here, stdio: 'inherit', shell: true });
	process.exitCode = result.status ?? 1;
} finally {
	rmSync(temp, { force: true });
}
