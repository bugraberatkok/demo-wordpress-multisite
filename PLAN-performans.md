# 7 canlı site — performans (görünüm aynı kalır)

6 Ekim 2026. Mimar ve inceleme: ana oturum (Opus). Uygulama: Opus ajanları. Kullanıcı onayladı ("başla, atlama, bana kontrol ettir"). Ekran görüntüsü karşılaştırması istenmedi.

**Kapsam:** 7 site, 6 tema.

| Site | Tema | Yerel blog |
|---|---|---|
| İstanbul Paletçi | istanbulpaletci-theme | 5 |
| İstanbul Keresteci | istanbul-keresteci-theme | 9 |
| Ahşap Ambalaj | ahsapambalaj-theme | 11 |
| Sanayi Palet | sanayi-palet-theme | 10 |
| Ahşap Kasa | ahsapkasa-theme | 4 |
| İthal Keresteci | kereste-base (+ ithalkeresteci-theme) | 6 |
| Kavak Keresteci | kereste-base (+ kavakkeresteci-theme) | 7 |

**Kapsam dışı:** Koçist, WOOD KOCIST, Palet Çivi.

**Ölçüm (PSI mobil):** sanayi 64, ik 67, kasa 69, ambalaj 70-79, paletci 79, ithal 83, kavak 85. TTFB iyi.

## Ortak kurallar
- **Görünür değişiklik yok.** Renk, yazı tipi, boşluk ve düzen aynı kalır. Tek istisna kontrast (madde 6).
- Her tema yalnızca kendi dosyalarına dokunur. Eklentiye ve WK / Koçist / Palet Çivi'ye dokunulmaz.
- **Sürümleme:** CSS/JS dosyaları filemtime'a dayalı `ver` ile yüklenir. Mevcut kalıp korunur; yoksa eklenir.
- Commit ve push yok.

## 1. Yazı tipi: Archivo yerelden
- Google Fonts isteği şu: `family=Archivo:wdth,wght@62..125,300..900&display=swap`. Google'ın bu isteğe verdiği CSS'ten değişken woff2 dosyaları indirilir: latin + latin-ext alt kümeleri, normal stil, italik kullanılıyorsa italik de.
- **Dosya konumu:** her temada `assets/fonts/`. Tema bağımsız deploy edildiği için her temada kendi kopyası bulunur.
- **@font-face:** Google'ın verdiği tanımların birebir aynısı kullanılır. Değişmeyecekler: `font-weight 300 900`, `font-stretch 62% 125%`, `unicode-range`, `font-display: swap`.
- Ana alt küme (latin) için `<link rel="preload" as="font" type="font/woff2" crossorigin>`.
- `fonts.googleapis.com` / `fonts.gstatic.com` enqueue'ları ve preconnect'ler kaldırılır.

## 2. CSS birleştirme
- Tema CSS'leri site başına mümkün olduğunca tek dosyada toplanır. Sıra aynı kalır, içerik aynı kalır.
- **En basit güvenli yol:** derlenmiş bir `assets/dist/site.css` dosyası. Kaynak dosyalar repoda kalır; küçük bir yeniden üretme betiği temada `scripts/` altında ya da belgede durur.
- Koşullu yüklenen sayfa CSS'leri (yalnız bazı sayfalarda kullanılanlar) ya ayrı kalır ya da ihtiyatla birleştirilir. Tek sayfaya özgü ve büyük olanlar ayrı kalabilir.
- `wp_add_inline_style` ve handle bağımlılıkları bozulmamalı. Panel önizleme sınıfları da bozulmamalı.

## 3. Görseller
- **Tema içindeki JPG/PNG:** `assets/img` gibi klasörlerdeki dosyalar Pillow ile WebP'ye çevrilir (quality ~80-82, aynı piksel boyutu). Referanslar `.webp`'ye çekilir: PHP, CSS, manifest varsayılanları.
  - Panelde kayıtlı içerik bu tema yollarını tutuyorsa (manifest varsayılanı ve tema dosyası yolu) kontrol edilir. Eski JPG dosyaları silinmez; kayıtlı içerik onlara işaret ediyor olabilir.
- **Hero / ilk ekran (LCP) görseli:**
  - `fetchpriority="high"` eklenir, `loading="lazy"` kaldırılır.
  - Gerekirse `<link rel="preload" as="image">`.
  - Ekran dışındakiler `loading="lazy"` + `decoding="async"`.
  - `width/height` (ya da aspect-ratio) eksik olanlara eklenir. Ahşap Kasa'daki logo buna dahil.
- **Mevcut yüklemeler (wp-content/uploads):** ayrı iş (aşağıda), temalarda değil.

## 4. CLS
- **İstanbul Keresteci:** hero `img.ik-hero__image` ve `<main id="icerik">` kayıyor. Hero kutusuna yükseklik ya da oran sabitlenir (mevcut görünümle aynı ölçü).
- **Sanayi Palet:** `#sp-menu .sp-header__panel` ilk boyamada yer değiştirip main'i itiyor. Panelin başlangıç durumu CSS'te sabitlenir (JS gelmeden de aynı).

## 5. Önbellek başlıkları
- Kök `.htaccess` repoda değil. Bu yüzden canlıda Cache-Control için bir talimat yazılır; kod değişikliği yok.
- Canlı için talimat: LiteSpeed Cache → Tarayıcı → TTL 31557600; ya da `.htaccess` mod_expires kuralları. DEVAM'a eklenir.

## 6. Kontrast ve dokunma alanı (kullanıcı onayladı)
- **Sanayi Palet, Ahşap Kasa, Kavak:** PSI / axe'nin işaret ettiği metin ve zemin çiftleri bulunur. Metin rengi, aynı tonda, WCAG AA (4.5:1; büyük metinde 3:1) eşiğine yetecek **en küçük** miktarda koyulaştırılır.
- **Ahşap Kasa'da küçük dokunma alanları:** görünür boyut değişmeden padding ya da ::before ile 24×24 (tercihen 44) yapılır.
- Her değişiklik listelenir: eski renk → yeni renk, oran.

## Doğrulama
- Sayfalar 200 döner. `php -l` temiz. Konsolda JS hatası yok.
- `fonts.googleapis` HTML'de geçmez. CSS istek sayısı düşer. Font dosyaları 200 döner.
- WebP referansları 200 döner.
- E_ALL ile PHP bildirimi yok.
- Mümkünse yerelde Lighthouse (`npx lighthouse` ya da Chrome DevTools protokolü) mobil ölçümü yapılır: önce / sonra.
