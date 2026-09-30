# Koçist ürün sayfası: canlıdaki görsel + form, altta WK akordeonu, dürüst fiyatsız durum — Plan

Fable 5.1 (mimar), 30 Eylül 2026. Kod değişmedi; depo dosyasına dokunulmadı.

**Brief:** Üst bölüm (görsel ve teklif formu) https://kocist.com.tr/urun/cardak-25x25mt/1 gibi; alt bölüm WOOD KOCIST'teki akordeon. Birebir kopya değil; havuz verisinin sayfadaki yerleri (“veri yuvaları”) iki sitede aynı. Fiyatsız üründe form var, ama fiyatın gösterilmeme nedeni ürünün stok durumu / sipariş üzerine üretim olduğu açıkça yazılır; kişiye göre fiyat izlenimi verilmez. Aynı ilke WOOD KOCIST'te de.

**Kaynaklar** (`scratchpad\kcparity\`): `live-cardak-1440.png`, `live-cardak-390.png` (canlı), `local-cardak-1440/390.png`, `local-kc-car200s-1440.png` (14 detaylı fiyatsız ürün), `local-kc-bungalov-1440.png` (metninde tablo olan ürün), `local-wk-car200s-1440.png` (WK fiyatsız), ham HTML `live-cardak.html`, canlı betik `site.min.js`, `shot.mjs`, sayım betiği `counts.php`.

**Veri gerçeği (yerel havuz, `counts.php`):** Koçist 283 ürün: 246 fiyatsız, 232 detaysız (yalnızca kod), 183'ünün metninde tablo, 278'inde görsel yok, 3'ünde metin yok (ahsap-kafes, ahsap-palet, ahsap-sandik), 5'inde serbest ölçü notu (spec çift değil). WK 55 ürün: 16 fiyatsız, hepsinde detay var, 54'ünde görsel yok. Fiyat metinleri serbest: "450 TL", "1.250 TL’den başlayan", "450 tl".

---

## 1. Canlı Koçist ürün sayfasının anatomisi

Canlı site WordPress değil (Bootstrap 5 + özel PHP; Inter/Poppins; yeşil `btn-primary`, beyaz kartlar, açık zemin). Tüm gruplarda aynı şablon; **hiçbir yerde fiyat ve stok bilgisi yok** (kategori sayfası: “Şu anda online mağazamız kapalıdır. Ürünlerimizi WhatsApp ya da telefon yoluyla, ölçüler dahilinde sipariş verebilirsiniz”).

### Masaüstü (1440)
1. **Konum:** `Ana Sayfa / Ürünlerimiz / cardak / <tam ürün adı>` (son öğe ürün adı; kategori adı küçük harfli slug).
2. **İki sütun (8/12 + 4/12, ~880 / 440 px):**
   - **Sol “Medya” kartı:** kart başlığında “Medya” ve sağda küçük düğme grubu **Fotoğraf | Video | Büyüt**. Gövdede **16:9** Bootstrap carousel (7 görsel; kırpılarak sığdırılır), sol/sağ ok, küçük resim yok. Video sekmesi `<video>` (havuzda video alanı yok → kapsam dışı). Büyüt: lightbox.
   - **Sağ “Teklif Al” kartı (form):** alanlar sırayla **Ürün** (readonly, ürün adı + “Model 1”), **Ad**, **Şirket**, **Telefon**, **E-posta** (type=email), **Adet/Ölçü**; **KVKK** onay kutusu (`required`, “KVKK Aydınlatma Metni’ni [okudum], onaylıyorum.”); düğmeler **Gönder** (yeşil dolu) ve **WhatsApp** (yeşil çerçeveli). Etiketler üstte, yer tutucu yok, zorunlu işareti yok.
     - **Gönder** doğrudan göndermez: “Teklif Talep Formu” modalını açar (Ad Soyad*, Şirket, Telefon*, E-posta, İl, Ürün/Kategori (readonly), Adet, Mesaj, KVKK*). Modal `fetch('/pages/api/send_quote.php', POST JSON)` + reCAPTCHA v3; başarıda “Talebiniz alındı, e-posta…” bildirimi.
     - **WhatsApp:** `wa.me/905496481919?text=Merhaba, <ürün> Model 1 için teklif talep ediyorum. … Sayfa: <url>`.
   - **Sayfada `<h1>` yok** (ürün adı yalnızca konumda ve form alanında). Biz h1'i koruyoruz.
3. **“Ürün Açıklaması” kartı:** emoji başlıklı düz paragraflar (Ölçü:, Malzeme:, Çatı Kaplama: … satırları metnin içinde; tablo değil).
4. **Kod kartı:** “Çardak 2.5x2.5mt” başlığı + boş tablo.
5. **İlgili sayfalar** (rozet bağlantılar) + tekrar **Teklif Al / WhatsApp**; **İlgili bloglar**; **Sık Sorulan Sorular** (2 soru, akordeon); footer.

### Mobil (390)
Konum → Medya kartı (16:9) → Ürün Açıklaması → kod kartı → İlgili sayfalar → İlgili bloglar → SSS → **Teklif Al formu en altta** (masaüstü formu `d-none d-lg-block`; mobil için ikinci kopya) → footer. Sabit alt çubuk yok.

### “Görsel ve form” olarak alınacaklar / alınmayacaklar

| Canlıdaki | Karar |
|---|---|
| Sol geniş görsel, sağ dar form kartı | **Alınır** (ana düzen) |
| 16:9 kırpma | Alınmaz: firmanın önceki isteği “görsel kendi oranında, kırpılmadan” (product.css başlığı, 29 Eylül). **Yer tutucu** 16:9 olur (278 üründe görsel yok; canlıdaki gibi yatay kutu) — soru 1 |
| Sol/sağ ok, büyüt | Alınır: oklar ve sayaç (product.js zaten `[data-k-gallery-prev/next]` bekliyor, şablonda yoktu); büyütme + fare merceği zaten var |
| Fotoğraf/Video sekmesi | Alınmaz (havuzda video yok) |
| Form alanları Ad, Şirket, Telefon, E-posta, Adet/Ölçü, KVKK, Gönder + WhatsApp | **Alınır**; “Ürün” readonly alanı yerine kartın üstünde ürün adı/kodu görünür ve gizli `kc_product` gider (soru 2) |
| Modal + reCAPTCHA + JSON API | Alınmaz: form doğrudan `admin-post.php?action=kc_quote`'a POST eder (mevcut `inc/form.php`), bot tuzağı ve nonce ile; sahte başarı yok, kayıt `kc_quote` + e-posta bildirimi |
| Mobilde form en altta | Alınmaz: 390'da form başlığın hemen altında (asıl eylem görünür kalsın) — soru 3 |
| Ürün Açıklaması kartı, kod kartı | Akordeona taşınır (§2) |
| İlgili sayfalar, SSS | Bizdeki “kategorisinde diğer ürünler” kartları ve panelden gelen SSS **kalır** |
| İlgili bloglar | Alınmaz (blog ilişkisi verisi yok) |

### Yerel sayfa bugün (local-cardak-1440.png) ve fark
Solda yapışkan bilgi sütunu (kategori etiketi, h1, kısa açıklama, “Teklif al” rozeti, **Teklif Alın** → /iletisim/?urun=…, **WhatsApp’tan Sorun**, not “Fiyatlar ölçü ve adede göre değişir; aynı gün fiyat veriyoruz.”); sağda büyük yer tutucu; altta ayrıntı seviyesine göre “Ürün Detayı” hikâyesi (k-pstory: 4'lü şerit, iki sütun metin, yeşil föy, tablolar); kategori kartları; SSS.
Fark: form sayfada değil (tek tık uzakta), görsel/form sütunları ters, fiyatsız durumun nedeni yok, alt bölüm akordeon değil, detaysız üründe (232) föy hiç çıkmıyor, oklar yok.

---

## 2. Ortak “veri yuvası” modeli (Koçist = WK)

Tek kaynak havuz ürünü (`nwcs_site_products()` satırı: `title, code, short, price/has_price, body, spec, details, images, tables, url, categories`). Her yuva iki sitede aynı veriden dolar; yalnızca görsel dil temaya aittir.

| Yuva | Havuz verisi | Koçist | WOOD KOCIST (mevcut) |
|---|---|---|---|
| Başlık (h1, konum son öğesi, SEO adı) | `title` (+`code`) | `title` (canlıda böyle); kod tabloda | `wk_product_heading` = kod + ad (canlısında böyle) |
| Ürün kodu | `code` | Teknik Detaylar 1. satırı “Ürün kodu” | aynı |
| Fiyat | `price` / `has_price` | fiyat varsa `price` metni; yoksa **fiyatsız durum bloğu** (§3) | fiyat + KDV notu; yoksa §3 |
| Kısa açıklama | `short` | h1 altı | fiyat altı |
| Galeri | `images` (ilk = ana) | büyük sahne + küçük resimler + oklar + mercek + tam ekran | bindirmeli küçük resimler + mercek + tam ekran |
| **Teknik Detaylar** | `nwcs_product_pairs($p)` − “Lojistik ve Teslimat” satırı; başa kod; **serbest ölçü notu** (spec çift değil, 5 üründe) “Ölçü” satırı olur | akordeon 1 (`k-specs-list`) | akordeon 1 (`wk-attrs`) |
| **Ürün Açıklaması** | `wpautop(body)` → `nwcs_product_split_tables()`: metin + metindeki tablolar; ardından havuz tabloları `$p['tables']` (yalnızca Koçist'te açık) | akordeon 2; tablolar `k-ptable` görünümünde, yatay kayar | akordeon 2 (`wk-tabs__table`) |
| **Lojistik ve Teslimat** | üründe “Lojistik ve Teslimat” detay satırı varsa o; yoksa **site geneli panel metni** | akordeon 3; Koçist'e yeni alan `product.detail.delivery_text` | akordeon 3 (`product.labels.delivery_text`) |
| Müşteri Görüşleri | veri yok | **gösterilmez** (soru 4) | boş durum metni |
| İlgili ürünler | aynı kategori | mevcut kartlar | mevcut kartlar |
| SSS | Koçist panel `product.faq` | kalır (canlıda da var) | yok |
| WhatsApp | grup numarası + şablon (`kocist_product_wa_url`) | formun yanındaki düğme | Hızlı Sipariş |
| Teklif / soru formu | — | **sayfanın içinde** (`kc_quote`) | ayrı sayfa (`?urun=id`), değişmez |

**Kural farkları bilerek tek:** “Lojistik ve Teslimat” yalnızca tam bu etiketle eşleşen satırdır; “Teslimat: Ağır Nakliye…” gibi satırlar tabloda kalır (WK'daki kural, ikisinde de aynı). Boş metin → panelden düzenlenen dürüst boş-durum metni (“Açıklama henüz girilmedi.”). Hiçbir şey uydurulmaz.

**Koçist'e özgü rendering kuralları** (yuva aynı, davranış veriye göre):
- Varsayılan açık panel: **Teknik Detaylar**; tabloda koddan başka satır yoksa (232 ürün) **Ürün Açıklaması** açık başlar (WK: hep Teknik Detaylar; orada hepsinde detay var).
- 183 üründe metin içi tablo: Ürün Açıklaması panelinin sonunda `k-ptable__scroll` (mevcut `role="region" tabindex=0` kalıbı).
- Metinsiz 3 ürün: Ürün Açıklaması paneli `desc_empty`; sayfa açık kalır (`product_page_always`).
- Panel önizlemesinde (`nwcs_is_preview()`) tüm paneller açık (WK'daki karar).

**Kod paylaşımı — karar: küçük, saf veri yardımcısı eklentide; işaretleme temada.** `includes/product-headings.php`'ye (detay verisinin evi) eklenir:
- `const NWCS_DELIVERY_LABEL = 'Lojistik ve Teslimat'`
- `nwcs_product_is_delivery_pair( array $pair ): bool`
- `nwcs_product_table_specs( array $product ): array` (çiftler − lojistik; serbest not → `['Ölçü', not]`)
- `nwcs_product_delivery_row( array $product ): string` (satır değeri ya da `''`)
WK'daki `wk_is_delivery_pair / wk_product_table_specs / wk_product_delivery_text` bu üçüne devreder (çıktı birebir aynı; 55 sayfanın HTML'i diff ile kanıtlanır). Neden ortak kod: kural (“hangi satır lojistiğe gider”) iki sitede aynı olmak zorunda; iki kopya zamanla ayrışır. Neden tam bir “slots” dizisi/partial değil: iki temanın işaretleme dili farklı, ortak partial iki temayı da bağlar; 4 satırlık yardımcı yeterli. Akordeon JS'i temaya özel (~30 satır; Koçist'te zaten `[data-k-faq]` kalıbı var, aynı kalıp `[data-k-acc]` için).

---

## 3. Fiyatsız durum: dürüst metin ve veri modeli

**İlke:** Fiyatın olmaması ziyaretçiye değil ürüne bağlıdır. Metin nedeni söyler (stok durumu, ölçüye/adede göre üretim), fiyatın nasıl belirlendiğini söyler, kişiye özel fiyat ima eden hiçbir şey (“size özel”, “size uygun fiyat”, “fiyat için arayın”) kullanılmaz; teslim süresi, indirim gibi veride olmayan vaat eklenmez.

### Metin önerileri (firma onayına)
- **Kısa rozet (kartlar ve sayfa, iki sitede aynı):** “Fiyat teklifle” (Koçist kartında zaten bu; WK kartı ve sayfası “Fiyat için sorun” → “Fiyat teklifle”).
- **Koçist site geneli neden metni (varsayılan):** “Bu ürün için sitede sabit fiyat yok: fiyat stok durumuna, ölçüye ve adede göre belirlenir. Ölçü ve adedi yazın; yazılı teklif gönderelim.”
- **WK site geneli neden metni (varsayılan):** “Bu ürün sipariş üzerine üretildiği için sitede sabit fiyat gösterilmiyor; fiyat ölçü, donanım ve teslim şekline göre belirlenir. Hızlı Sipariş’ten ya da formla sorun.” (“sipariş üzerine üretim” WK'nın mevcut panel metinleriyle uyumlu: özel üretim formu, “üretim ve kalite kontrol 7–14 iş günü”.)
- Fiyatlı üründe neden metni **çıkmaz**; Koçist'te fiyat + form (canlıda form her üründe), WK'da fiyat + sepet.

### Veri modeli — seçenekler ve karar
| Seçenek | Maliyet | Doğruluk |
|---|---|---|
| **A. Site geneli tek metin** (İçerik Stüdyosu alanı, iki temada) | küçük: manifest alanı + şablon satırı | Genel ifade; tüm fiyatsız ürünler için doğru olacak biçimde yazılmalı (yukarıdaki metin bunu sağlıyor: “stok durumuna, ölçüye ve adede göre”) |
| **B. Koçist'te grup bazlı ezme** (`kereste/ambalaj/hirdavat/dekorasyon`, WhatsApp numaraları gibi 4 alan; boşsa A) | küçük: 4 manifest alanı + `kocist_price_reason($product)` | Kereste “ölçüye göre kesim”, hırdavat “adet ve stok”, dekorasyon “sipariş üzerine üretim” gibi daha isabetli; havuz/Excel'e dokunmaz |
| **C. Ürün bazlı “Fiyat gösterilmeme nedeni” seçimi** (Stokta yok / Özel ölçü üretim / Sipariş üzerine üretim / boş=site metni), ürün formu + Excel sistem sütunu “FİYAT NOTU” | orta: `nwcs_sync_system_columns` + `nwcs_heading_reserved_keys` (‘fiyatnotu’) + taslak yazımı (sync.php ~278) + başlık ayrıştırma (~430) + plan/karşılaştırma (~690–720) + uygulama/geri alma (history) + “Nasıl kullanılır” satırı + `import.php` önizleme etiketi + `pool.php` form/save + `nwcs_pool_product_data`/`nwcs_site_products` geçişi + iki tema; ~8 dosya. 262 fiyatsız ürün için veri girişi gerekir; girilmeyen ürün yine A'ya düşer | En isabetli, ama bugün girilecek bilgi yok |

**Karar: A + B şimdi; C ertelenir.** Faz-1/2 Excel sistemi sistem sütunlarını sabit bir listede ve büyük harf başlıkla tanıyor; yeni sütun eklemek mümkün ama plan/uygula/geri al zincirinin her halkasına dokunuyor ve “Nasıl kullanılır” sayfasını değiştiriyor. Nedeni ürün başına yazacak veri henüz yokken bu maliyet gereksiz. Grup ezmesi Koçist'in mevcut kalıbıyla (grup anahtarı, WhatsApp numaraları) sıfır veri modeli değişikliğiyle isabeti artırır. C gerekirse `kocist_price_reason()` tek noktadan ürün alanını önce okuyacak şekilde tasarlanır (öncelik: ürün > grup > site).
Grup alanlarının **varsayılanı boş** (site metni geçerli); firma isterse şu metinler yapıştırılır (öneri, uydurma bilgi değil; mevcut kategori metinlerindeki “ölçü ve adede göre üretim ve tedarik” ifadesinden): Kereste “Kereste ölçüye göre kesildiği için sabit liste fiyatı yok; fiyat ölçü, adet ve stok durumuna göre belirlenir.” · Ambalaj “Palet ve sandıklar ölçüye ve adede göre üretilir; fiyat bu bilgilerle hesaplanır.” · Hırdavat “Fiyat adede ve stok durumuna göre belirlenir.” · Dekorasyon “Bu ürün sipariş üzerine üretilir; fiyat ölçü, donanım ve teslim şekline göre belirlenir.”

### Nerede görünür
- **Koçist ürün sayfası:** form kartının üstünde fiyat yuvası: fiyatlı → fiyat metni; fiyatsız → rozet “Fiyat teklifle” + neden paragrafı; form başlığı her iki durumda “Teklif isteyin” (soru 5). Mevcut `product.main.note` (“…aynı gün fiyat veriyoruz.”) şablondan **çıkar** (vaat; neden metniyle çakışır).
- **Koçist kartlar:** kategori/ilgili kartlar `kategoriler.texts.price_quote` (“Fiyat teklifle”) — değişmez; **ana sayfa kartları** (`sections/products.php`) eklentinin “Teklif al” `price_label`'ı yerine aynı panel alanını kullanır (tutarlılık). `latest.php` fiyat basmıyor, değişmez.
- **WK ürün sayfası:** `price_ask` satırının altına `<p class="wk-product__reason">` (`global.card.price_reason`); Hızlı Sipariş + Formla sor aynen. WK kartı: `price_ask` varsayılanı “Fiyat teklifle”, “Fiyat sor” düğmesi kalır.
- Eklenti yönetim ekranlarındaki “Teklif al” (fields.php, import.php, pool.php) yönetici dili; **değişmez**.

---

## 4. Koçist tasarım notu (frontend-design; brief'in sözü üstün)

- **Tokenlar mevcut:** `--forest #17542f`, `--leaf #7ab648`, `--leaf-soft`, `--paper-2 #f4f6f3`, `--line`, Archivo (tek aile). Yeni renk yok.
- **Düzen (≥1024):** `k-wrap` (1220) içinde `grid-template-columns: minmax(0,1fr) 400px; gap: 40px` — canlının 8/12–4/12 oranı. Sol: galeri, altında akordeon (WK'da akordeon bilgi sütununun altında; burada sol sütun). Sağ: form kartı `position: sticky; top: 88px` (bugünkü bilgi sütunu gibi), akordeon kayarken form gözde kalır. 1024 altı tek sütun.
- **Form kartı:** beyaz, `border: 1px solid var(--line)`, 16px köşe (tema kartlarıyla aynı, `k-ptable` gibi); içinde sırayla kategori etiketi (mevcut `k-product__category`), h1 (28–32px, sütun dar), fiyat yuvası, kısa açıklama, form. Alanlar `k-field` (contact.css'teki kalıp), gönder `k-contact__submit` (forest, hover leaf), WhatsApp yeşil çerçeveli `k-product__btn--ghost` üstüne WhatsApp ikonu. Tek vurgu: koyu yeşil Gönder; kartta başka dekor yok.
- **Akordeon:** `k-acc` başlık düğmeleri 18px/700 forest, üst-alt 1px `--line`, sağda artı/eksi (leaf), panel 24px iç boşluk; Teknik Detaylar `k-specs-list` (var), tablolar `k-ptable` (var), lojistik düz paragraf. Büyük harf etiket, numara, gölge yok. Tıklama dışı hareket yok; `prefers-reduced-motion` mevcut FAQ kuralına eklenir.
- **Galeri:** sahne çerçevesiz (mevcut), oklar `k-product__arrow` (CSS var), sayaç; yer tutucu 16:9 yatay kutu.
- Kaldırılan: `k-pstory*`, `k-facts*`, `k-pstory__inline/strip/rows` kuralları (ölü CSS temizlenir), `data-k-pstory-open` JS'i.

---

## 5. Dosyalar, adımlar, doğrulama

### Dosyalar
| Dosya | Değişiklik |
|---|---|
| **Eklenti** `includes/product-headings.php` | `NWCS_DELIVERY_LABEL`, `nwcs_product_is_delivery_pair`, `nwcs_product_table_specs`, `nwcs_product_delivery_row` (0.22.x; Excel/sync'e dokunulmaz) |
| **Koçist** `template-parts/product-detail.php` | Yeniden yazılır: konum → [galeri + akordeon \| form kartı] → ilgili ürünler → SSS. Form durumu `kocist_quote_state()` (başarı/hata kartın içinde, `id="teklif-formu"`), ön dolgu `kocist_quote_prefill()` yerine ürünün kendisi |
| `inc/form.php` | `kc-company`, `kc-qty` (isteğe bağlı; `_kc_company`, `_kc_qty`, post_content'e satır), `kc-consent` zorunlu (“Onay kutusunu işaretleyin.”), üründen gelen formda mesaj hatası “Adet ya da ölçü yazın.” (`kc_product` doluysa); yönlendirme zaten referer'a (ürün sayfası) |
| `inc/quote.php` | `kocist_price_reason( array $product ): string` (grup alanı → site alanı); `kocist_quote_url` aynı (sayfasız ürün kartları hâlâ /iletisim/'e) |
| `inc/catalog.php` | SEO ürün kaydına `sku` ve yalnızca düz tutar ayrıştırılıyorsa `price` (`kocist_price_number`: “450 TL”/“450 tl”/“3.500 ₺” → sayı; “…’den başlayan” → 0 → `offers` yok) |
| `functions.php` | `kocist_price_number()`; `kocist_product_pairs` yerine tabloda `nwcs_product_table_specs`; product sayfasında `assets/css/form.css` yüklenir; ölü `nwcs_product_detail_level` çağrıları kalkar (eklentiden silinmez) |
| `template-parts/sections/contact-main.php` | KVKK onay kutusu (ürün formuyla aynı alan adı; KVKK bağlantısı yalnızca `kocist_link` gerçek adrese çözülüyorsa, `#kvkk` ölü çapa iken düz metin) |
| `template-parts/sections/products.php` | fiyatsız kartta `kategoriler.texts.price_quote` |
| `content-manifest.php` | `product.detail`: `tab_specs` “Teknik Detaylar”, `tab_desc` “Ürün Açıklaması”, `tab_delivery` “Lojistik ve Teslimat”, `delivery_text` (varsayılan mevcut panel metinlerinden: süreç 3. adım + SSS “Teslimat hangi illere”; yeni vaat yok), `desc_empty`, `form_title` “Teklif isteyin”, `company_label` “Şirket”, `qty_label` “Adet / Ölçü”, `qty_ph`, `consent_label`, `price_badge` “Fiyat teklifle”, `price_reason` (site geneli); `product.whatsapp`: `reason_kereste/ambalaj/hirdavat/dekorasyon` (boş); `product.main.note` kaldırılır; `body_title` kaldırılır (başlık artık akordeon) |
| `assets/css/product.css` | Yeni üst düzen, form kartı, akordeon; `k-pstory/k-facts` blokları silinir; `k-specs-list`, `k-ptable`, `k-faq`, related kalır |
| `assets/css/form.css` (yeni) | `contact.css`'ten `k-field*`, `k-contact__submit*`, `k-field__error`, `k-contact__notice*`, `k-hp` taşınır (kopya değil, taşıma); `contact.css` bunu `wp_enqueue_style` bağımlılığıyla alır |
| `assets/js/product.js` | Akordeon (`[data-k-acc]`, `aria-expanded/aria-controls`, `hidden`, tek panel açık, tekrar basınca kapanır, JS yoksa hepsi açık `is-ready` kalıbı, önizlemede hepsi açık); `data-k-pstory-open` kalkar; oklar/sayaç zaten var |
| **WK** `content-manifest.php` | `global.card.price_ask` varsayılanı “Fiyat teklifle”; yeni `global.card.price_reason` (textarea) |
| `template-parts/product-detail.php` | fiyatsız dalda `price_ask` altına `wk-product__reason` (≈5 satır) |
| `functions.php` | üç yardımcı eklentiye devreder (çıktı aynı) |
| `assets/site.css` | `.wk-product__reason` tek kural |
| Belgeler | DECISIONS (veri yuvası modeli, fiyatsız durum kararı, C'nin ertelenmesi), DEVAM, README (Koçist ürün sayfası paragrafı) |

### Adımlar (her biri tek başına doğrulanır)
0. Başlangıç kanıtı: 3 Koçist ürünü (cardak-25x25mt, w-car-200s…, ahsap-bungalov-ev) + 1 WK fiyatsız ürün, 1440 ve 390; 55 WK sayfasının HTML'i (nonce normalize) referans olarak kaydedilir.
1. Eklenti yardımcıları; WK devretmesi; **55 WK HTML diff = 0**.
2. Koçist manifest alanları (panelde görünür, önizleme açılıyor).
3. `form.css` taşıma; iletişim sayfası görsel olarak aynı (ekran görüntüsü diff).
4. `inc/form.php` + `contact-main.php` onay kutusu; iletişim formu Mailpit'e düşüyor, onay boşsa hata.
5. `inc/quote.php` neden yardımcısı, `catalog.php` SEO price/sku; JSON-LD kontrolü (fiyatlı 37 üründe `offers` yalnızca düz tutarda, fiyatsız 246'da yok).
6. Şablon + CSS + JS; canlıyla üst bölüm yan yana (1440/390).
7. Ürün formu akışı: Gönder → kayıt `kc_quote` (ürün adı + kategori) → Mailpit'te bildirim (alıcı info@kocist.com.tr) → sayfa `?kc=…#teklif-formu` ile başarı kutusu; hatalı gönderimde değerler korunur; bot alanı doluysa sessiz dönüş.
8. WK fiyatsız neden metni; kartlar.
9. Belgeler.

### Doğrulama
- 283 Koçist + 55 WK ürün adresi 200; her sayfada 1 h1; Koçist'te 3 akordeon başlığı, tek `aria-expanded=true` (önizlemede hepsi), `aria-controls` hedefleri var; klavye Enter/Space; odak halkası görünür; JS kapalıyken üç panel açık.
- 390'da `scrollWidth` = 390 (183 üründeki tablolar kendi içinde kayıyor); dokunma hedefleri ≥44px.
- E_ALL ile 283 + 55 sayfada 0 PHP bildirimi (geçici mu-plugin, sonra kaldırılır) — özellikle metinsiz 3 ürün, serbest notlu 5 ürün, W-CAR-200S (14 detay).
- Form: ürün adı ön dolu, kayıt ve Mailpit e-postası doğru, sahte başarı yok; WhatsApp bağlantısı grubun numarasına (dekorasyon 0549…, diğerleri 0532…) ve mesajda ürün adı.
- JSON-LD: `Product.name` = h1, `sku`, `offers` yalnızca fiyatlı ve düz tutarda; `<title>`, canonical, sitemap değişmedi.
- WK: 55 HTML diff yalnızca fiyatsız 16 üründe `wk-product__reason` satırı (ve kartlarda etiket); başka fark yok.
- Panel: yeni alanlar önizlemede tıklanıp açılıyor; `nwcs_product_attr` işaretleri (Ürün adı, Fiyat, Teknik özellikler, Detay metni, Görseller) yerinde.
- Ekran görüntüleri 1440/390: canlı üst bölümle yan yana; kcparity klasörüne.

### Riskler
- Panelde daha önce kaydedilmiş `product.main.note`, `body_title`, WK `price_ask` değerleri: yerelde kayıt yok; canlıda varsa alan panelden bir kez güncellenmeli (note/body_title kaldırılınca kayıt yok sayılır).
- 278 Koçist ürününde görsel yok: sayfanın sol yarısı yer tutucu; canlıdaki gibi görünmesi fotoğraf aktarımına bağlı (ayrı iş).
- Neden metinleri firma onayı ister; onaysız yayına çıkarsa site geneli varsayılan geçerli.
- KVKK sayfası yok (`#kvkk` ölü çapa; metin firmadan bekleniyor): onay kutusu bağlantısız kalır, metin “Bilgilerimin bu talebe dönüş için kullanılmasını kabul ediyorum.” Sayfa gelince bağlantı kendiliğinden açılır.
- Sticky form kartı 700 px'ten kısa ekranlarda kaydırırken kayabilir; `top:88px` + kısa kart; gerekirse `max-height: calc(100svh - 100px); overflow:auto` (WK tablet düzeltmesindeki kalıp).
- Form hata durumu ürün sayfasına döner: `wp_validate_redirect(referer)`; referer yoksa /iletisim/ (mevcut davranış).
- Fiyat ayrıştırma: “1.250 TL’den başlayan” gibi metinlerde offers bilerek yazılmaz; “450 tl” küçük harf eşleşmeli.

## 6. Açık sorular (varsayılan öneriyle)
1. **Görsel oranı:** kendi oranında (kırpma yok), yer tutucu 16:9. — Evet.
2. **“Ürün” readonly alanı:** kaldırılır; ürün adı kartın başlığı, gizli `kc_product`. — Evet.
3. **Mobil sıra:** konum → galeri → başlık/fiyat-neden → form → akordeon → ilgili → SSS (canlıda form en altta). — Evet.
4. **Müşteri Görüşleri:** Koçist'te yok (veri yok; SSS var). — Evet.
5. **Form başlığı** fiyatlıda da “Teklif isteyin” mi, “Sipariş / teklif” mi? — “Teklif isteyin”.
6. **Neden metinleri** (§3) firma onayı; grup metinleri boş başlar. — Evet.
7. **WK kart etiketi** “Fiyat için sorun” → “Fiyat teklifle”. — Evet.
8. **Eklenti yardımcısı** (küçük ortak kod) mı, Koçist'te kopya mı? — Eklenti (0.22.x, Excel'e dokunmadan).
9. **`product.main.note`** kaldırılsın mı (“aynı gün fiyat” vaadi)? — Kaldırılır; “aynı gün” ifadesi temanın başka metinlerinde kalıyor, ayrı içerik kararı.

## 6b. Kullanıcı kararları (30 Eylül 2026) — bağlayıcı
1. **Görsel oranı WOOD KOCIST'teki gibi olur.**
   - Görsel kendi oranında gösterilir, kırpılmaz.
   - Fotoğrafsız üründe yer tutucu WK'daki plakayla aynı oranda olur (1280/1714, dikey).
   - Tablet için yükseklik sınırı WK'daki kalıpla aynıdır. 16:9 yer tutucu KULLANILMAZ.
2. "Ürün" readonly alanı kaldırılır: EVET.
3. **Mobil sıra (1024 altı):** konum → galeri → başlık/fiyat-neden/kısa açıklama → AKORDEON → FORM.
   - Formun aşağıda olduğu müşteriye üst kısımda açıkça belirtilir. Örnek: başlık/fiyat bölümünde forma kaydıran bir "Teklif formuna git" bağlantısı/düğmesi ve kısa not.
   - Masaüstünde form sağda, sticky (plandaki gibi).
4. Müşteri Görüşleri Koçist'te yok: EVET.
5. Form başlığı "Teklif isteyin": şimdilik EVET.
6. Neden metinleri: EVET; grup metinleri boş başlar.
7. WK kart etiketi "Fiyat teklifle": EVET.
8. Ortak eklenti yardımcısı: EVET.
9. `product.main.note` ("aynı gün fiyat") şimdilik kaldırılır.
10. **Fotoğraflar ve KVKK metni kapsam dışı; Koçist firması sonra halledecek.**
    - Hedef: site firmaya kullanılabilir, hazır halde teslim edilir.
    - KVKK onay kutusu bağlantısız metinle çalışır.

## 7. Roller
1. **Fable 5.1 HIGH** — mimar (bu belge).
2. **Opus 5.5 MEDIUM** — uygulama, `frontend-design` becerisiyle; §4 tasarım notu ve §3 metinleri bağlayıcı; yalnızca listelenen dosyalar; commit yok; plana ters düşen somut bulguda durup bildirir.
3. **Yeni Fable 5.1 HIGH** — bağımsız inceleme (gereksinim, plan, diff, 55 WK diff, a11y, dürüst metin denetimi).
4. **Opus 5.5 MEDIUM** — düzeltme + doğrulama listesi.

## 8. Uygulama notları (Opus 5.5, 30 Eylül 2026)

Adımlar 0–9 yapıldı; commit yok. Veritabanı yedeği scratchpad `kcimpl/before.sql`.

**Değişen dosyalar:** eklenti `includes/product-headings.php` (üç yardımcı + sabit),
`network-content-studio.php` (sürüm 0.22.1); Koçist `template-parts/product-detail.php` (yeniden),
`inc/form.php`, `inc/quote.php`, `inc/catalog.php`, `functions.php`, `content-manifest.php`,
`template-parts/sections/contact-main.php`, `template-parts/sections/products.php`,
`assets/css/product.css`, `assets/css/contact.css`, `assets/css/form.css` (yeni), `assets/js/product.js`;
WK `functions.php`, `content-manifest.php`, `template-parts/product-detail.php`, `assets/site.css`;
belgeler DECISIONS, DEVAM, README.

**Plandan sapmalar ve kararlar**
- **Ad bloğu form kartının dışında (masaüstü):** §4 "kartın içinde kategori, h1, fiyat, kısa
  açıklama, form" diyor. İkisi birlikte ~950px; 900px ekranda yapışık kartın Gönder düğmesi
  görünmüyordu. Ayrıca §6b-3 telefon sırası (ad → akordeon → form) ad ile formu ayırıyor. Kaynak
  sırası galeri → ad → akordeon → form; masaüstünde grid alanları `"gallery head" "gallery form"
  "details form"`: form adın hemen altından başlar ve akordeon boyunca yapışık kalır (760px'ten
  kısa ekranda yapışmaz). Okuma/klavye sırası görsel sırayla aynı (display:contents ya da order yok).
- **"Teklif formuna git"** (§6b-3): iki yeni panel alanı `form_jump`, `form_jump_note`; yalnızca
  1024 altında görünür; tıklanınca forma kaydırır, "Ad Soyad" alanını odaklar (JS yoksa çapa).
- **Yeni alan `specs_empty`** ("Teknik detay henüz girilmedi."): kodu da detayı da olmayan ürün için
  (yerelde yok) boş panel kalmasın.
- **Formda ürün satırı:** mobilde form sayfanın altında kaldığı için kartta "Teklif istediğiniz
  ürün: <ad> (<kod>)" satırı var (mevcut `product.whatsapp.quote_label` alanı).
- **Meta adları:** planın `_kc_qty` yerine `_kc_size` (eklentinin e-posta etiketi "Ölçü ve adet"),
  `_kc_company` "Firma" olarak çıkıyor. Yalnızca doluysa yazılır; post_content'e "Şirket:",
  "Adet / Ölçü:" satırları ve "Onay kutusu: işaretli" eklendi.
- **Ürün formunu ayırt etme:** `kc_product` dolu ve `kc-detail` alanı yoksa (iletişim sayfası
  `?urun=` ile gelince `kc-detail` gönderir, eski davranış korunur).
- **`kocist_price_reason()`** metin yerine `{text, field}` döndürür (önizlemede grubun alanı mı
  site alanı mı vurgulansın diye).
- **Fiyat ayrıştırma:** veride "$" fiyatlar var (170 $, 24,50 $, 2.80 $). Düz tutar sayıldı,
  `priceCurrency` USD (`kocist_price_currency`). "1.250 TL’den başlayan" ve "2,1000 / 2,7000 / …"
  → offers yok. 37 fiyatlı üründen 35'inde offers.
- **JSON-LD `properties`** değiştirilmedi (plan yalnızca sku/price diyor; yerelde Koçist'te
  "Lojistik ve Teslimat" satırı yok).
- **Silinen ölü CSS:** `k-pstory*`, `k-facts*` (product.css içindekiler; pages.css'teki `k-facts`
  Hakkımızda'da kullanılıyor, dokunulmadı) ve kullanılmayan `k-pdetail*`. Stripe ve kod satırı
  kuralları genel bölüme taşındı. Örnek ürün sayfasının (/urun/) tablet kuralı
  `:not(.k-product--pool)` ile sınırlandı.
- **`product.main.note` manifestten kalktı:** örnek ürün sayfası (`sections/product-main.php`)
  bu notu koşulsuz basıyordu (ilk turda "if koruyor" yazılmıştı, yanlıştı); satır düzeltme
  turunda silindi.
- **Ana sayfa kartları (`sections/products.php`):** ilk turda "Fiyat teklifle" yapılmıştı;
  "Koçist ana sayfasına dokunma" kuralı gereği düzeltme turunda geri alındı (kullanıcı onayına).

**Doğrulama özeti** (ayrıntı ve ekran görüntüleri scratchpad `kcimpl/`):
- 283 Koçist + 55 WK ürün adresi 200. Koçist: her sayfada 1 h1, 3 akordeon başlığı, tek
  `aria-expanded=true` (227'de Ürün Açıklaması, 56'da Teknik Detaylar), `aria-controls` hedefleri
  var (düzeltme turundan sonra 229 / 54), `kc_product` = slug, onay kutusu var, WhatsApp numarası grubuna göre (dekorasyon 0549…,
  diğerleri 0532…) ve mesajda ürün adı; JSON-LD `name` = h1, `sku` = kod, offers yalnızca 35 düz
  tutarda (USD/TRY doğru). `<title>`, canonical, açıklama, robots 283 sayfada aynı.
- Önizlemede üç panel açık, yeni alanlar `data-nwcs-edit` ile işaretli, Gönder `type=button`.
- 390: `scrollWidth` 390 (tablolu ve notlu ürünler dahil, paneller açıkken), dokunma hedefleri ≥44px,
  sıra galeri → ad → akordeon → form, "Teklif formuna git" forma kaydırıp `kc-name`'i odaklıyor.
  Klavye Enter/Space çalışıyor, odak halkası 3px yaprak yeşili. JS kapalıyken üç panel açık.
- E_ALL (geçici mu-plugin, kaldırıldı): 283 + 55 ürün sayfası, ana sayfalar, iletişim, form
  gönderimleri, önizleme → 0 bildirim.
- Form: eksik gönderimde "Adet ya da ölçü yazın." ve "Onay kutusunu işaretleyin.", değerler korunuyor,
  `aria-invalid`; bot alanı dolu → sessiz dönüş, kayıt yok; geçerli gönderim → `kc_quote` (ürün adı +
  kategori), Mailpit'te info@kocist.com.tr'ye e-posta (Firma, Ürün, Ölçü ve adet), sayfada başarı
  kutusu. İletişim formu onaysız reddediliyor, onaylı gidiyor. Test kayıtları ve e-postaları silindi.
- WK: 55 HTML farkı yalnızca fiyatsız 16 sayfada "Fiyat teklifle" + `wk-product__reason` satırı ve
  kartlarda "Fiyat için sorun" → "Fiyat teklifle" (39 kart). Yardımcı devri tek başına 0 fark.
  (Not: havuz önbelleği yeniden kurulunca bazı havuz görsel adresleri `/kocist/…/uploads` ↔
  `/woodkocist/…/uploads` arasında değişiyor; ikisi de 200, bu işle ilgisiz, karşılaştırmada
  normalize edildi.)
- İletişim sayfası: form.css taşımasından sonra ekran görüntüsü farkı yalnızca kayan şerit ve
  asistan balonunda (aynı kodla iki çekim arasında da var); onay kutusu eklenince sayfa 60px uzadı.

**Düzeltme turu (bağımsız inceleme sonrası, 30 Eylül 2026)**
- **#1 Yapışık form:** alanlar iki sütun (Ad | Şirket, Telefon | E-posta; 600 altında tek sütun),
  kart 729 → 551px (hata durumunda 676px). Masaüstünde `max-height: calc(100svh - 100px)` +
  `overflow-y: auto` (fazlası kartın içinde kayar); kısa ekranda yapışmayı kapatan kural kalktı.
  Ölçüm: 1366×768 ve 1280×800'de sayfa kaydırılınca Gönder görünür, hata durumunda da.
- **#3:** ürün SSS varsayılanındaki "Ölçülerinizi ilettiğinizde aynı gün fiyat veriyoruz."
  cümlesi çıktı. Dokunulmayanlar (kullanıcıya soruluyor): footer şeridi "AYNI GÜN FİYAT" ve
  "TOPTAN FİYAT GARANTİSİ" (`content-manifest.php` ~527/529), kategori sayfası
  `lead_products` "aynı gün teklif veriyoruz" (~802) ve `empty_text` "aynı gün fiyat verelim"
  (~815), iletişim alt başlığı "aynı gün fiyat verelim" (~1398), SSS'nin kümese özgü cevapları.
- **#4 Dönüş adresi:** ürün formu `kc_return` (ürün adresi) taşır; `wp_validate_redirect` ile
  yalnızca site içi. Referer'sız POST hata ve başarıda ürün sayfasına döndü; dış adres → /iletisim/.
- **#5:** ana sayfa kart yazısı geri alındı (`kocist_product_has_page` değişikliği duruyor).
- **#6:** `nwcs_product_table_specs`: tamamı numaralı parçalardan oluşan not ("0: …; 1: …")
  satır olmaz (bos-kulp, gizli-pence-aski: Teknik Detaylar boş durum metni, Ürün Açıklaması açık).
  JSON-LD `properties` artık aynı yardımcıdan (`kocist_product_table_specs`).
- **#7:** `product-main.php`'deki boş `k-product__note` satırı silindi.
- **#8 Odak:** dönüşte (`?kc=`) ilk `aria-invalid` alan, yoksa sonuç bandı (`tabindex=-1`)
  odaklanır; çapa odağı ezmesin diye `load` sonrası. İletişim formunda hata satırına `id`,
  alana `aria-invalid` + `aria-describedby`.
- **#9:** `wp_head`'de erken `k-js` sınıfı; `.k-js .k-acc__panel:not(.is-open)` gizli (ilk
  boyamada sıçrama yok). `is-open` artık CSS'te kullanılıyor; ölü `is-ready` kaldırıldı.
- **#12:** durum transient'i okununca siliniyor; ürün formu `kc_form=product` ile ayrılıyor;
  tablette form ortada; "Ürün kodu" panel alanı `product.detail.code_label`. Hız sınırlama yok (parite).
- **#11:** DECISIONS'a USD offers, KDV ve `availability` notu.
- **Yapılmayanlar:** #2 (W-KAM-400DUB fiyatı havuzda boşalmış: kayıt 563, değişiklik 14:19:57;
  bu turda veriye dokunulmadı), #10 (metinlerin firma onayı).

