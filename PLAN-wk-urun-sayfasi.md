# WOOD KOCIST ürün sayfası: canlı düzene birebir geçiş — Plan

Fable 5.1 (mimar), 30 Eylül 2026. Onay bekliyor; kod değişmedi.

**Brief:** canlıdaki düzen (örnek: /urun/w-sez-l01-…-kopya/) sadık şekilde kopyalanır; yeni tasarım üretilmez. Tüm WK ürün sayfaları aynı iskelete geçer.

**Kaynaklar:** `scratchpad\wkparity\` klasöründe:
- ekran görüntüleri: `live-sez-1440.png`, `live-sez-1440-desc.png`, `live-sez-390.png`, `live-dog-1440.png`, `local-*.png`;
- stiller: `live-sez-*.styles.json`;
- ham HTML: `live-sez.html`;
- tema CSS: `ecomlab-style.css`;
- `shot.mjs` yardımcısı.

## 1. Canlı sayfanın anatomisi

Şezlong, köpek kulübesi ve çardak sayfaları aynı şablonu kullanıyor (WooCommerce + ecomlab product-style2).

### Masaüstü (1440; içerik 1280, kenar 80px)
1. **Konum satırı:** `Ana Sayfa / WOODGarden / Şezlonglar / <tam ürün adı>`. 14px #222, ayraç "/", padding 20px 0, alt boşluk 30px. Son öğe ürün adıdır.
2. **İki sütun, sticky değil:**
   - **Sol sütun (472px, %37):**
     - **h1:** "W-SEZ-L01 Kompakt Seri…". Kod adın başında. 32/40px, ağırlık 400, #292b2c.
     - **Fiyat:** "₺5.500,00 + KDV". Tutar 32px/500, ₺ 18px, "+ KDV" 12.8px. Altında "· · ·" süsü; üstte ve altta 30px boşluk.
     - **Kısa açıklama:** 15/25.6px, #1c1d1f.
     - **Varyasyon seçimi:** yalnızca WooCommerce'te var, bizde yok.
     - **Adet ve sepet:** adet kutusu 128×50, yanında "Sepete Ekle" düğmesi (50px).
     - **Hızlı Sipariş:** tam genişlik 50px, WhatsApp yeşili #0b6f31, beyaz 16px/500 yazı, WhatsApp ikonu.
     - **Etiketler:** yalnızca "Etiketler" satırı.
     - **AKORDEON (masaüstünde de):**
       - Başlıklar: **Teknik Detaylar** (varsayılan açık), **Ürün Açıklaması**, **Lojistik ve Teslimat**, **Müşteri Görüşleri**. Aynı anda tek panel açık.
       - Başlık stili: 18/26px, ağırlık 400, #b65545, padding 25px 0, üstte ve altta 1px rgba(0,0,0,.12) çizgi.
       - Teknik Detaylar: tablo. Satır 40px; th 15px/600 #333 (165px), td 15px #555.
       - Ürün Açıklaması: kalın paragraf başlıkları ve 15px paragraflar.
       - Lojistik ve Teslimat: blockquote. Metin ürüne göre değişiyor.
       - Müşteri Görüşleri: "Henüz değerlendirme yapılmadı."
   - **Sağ sütun (728px, %57; sütunlar arası 80px):**
     - Büyük görsel, çerçevesiz, köşe yuvarlatması yok, kendi oranında.
     - Küçük resimler görselin üstüne bindirilmiş: üstten 40px, ortalı sıra, 60px yuvarlak, pasif olanlar %30 opak.
     - Fareyle üzerine gelince yakınlaştırma ve tıklayınca tam ekran (PhotoSwipe).
3. **İlgili ürünler:** "İLGİLİ ÜRÜNLER" başlığı (12px/700), 4 kart.
4. **Site geneli bloklar ve footer:** kapsam dışı.

### Mobil (390)
Sıra: konum → galeri (küçük resimler bindirmeli) → h1 24/30px → fiyat → kısa açıklama → adet + Sepete Ekle → Hızlı Sipariş → aynı akordeon → ilgili ürünler (2 sütun). Sabit alt çubuk yok.

### Canlıda olup bizde karşılığı olmayanlar

| Canlıdaki | Karar |
|---|---|
| Varyasyon seçimi (renk görselleri) | Bu işte yok; renk, Teknik Detaylar'da metin olarak kalır |
| "Ön Sipariş" (stok) | Fiyatlıda "Sepete ekle", fiyatsızda "Fiyat sor" |
| Favorilere ekle | Atlanır |
| Etiketler | Atlanır |
| Yorum sistemi | §3'te |

## 2. Yerel sayfayla farklar

| Parça | Yapılacak |
|---|---|
| Konum satırının son öğesi (yerelde kod) | Tam ürün adı |
| "Ürün kodu" satırı | Kaldırılır; kod h1'in başına taşınır |
| h1 (38px/750) | 32px/500 (mobil 24px) |
| Fiyat kartı ("KDV dahil") | Kart kaldırılır; canlıdaki gibi çıplak satır + "· · ·" (KDV notu: soru 6) |
| Adet + Sepete ekle | Korunur; boyutlar canlıya eşitlenir |
| WhatsApp bağlantısı | Tam genişlik yeşil "Hızlı Sipariş" düğmesi |
| Fiyatsız ürün | "Fiyat için sorun" + Hızlı Sipariş + "Formla sor" |
| Güven satırları | Özetten kalkar; teslimat notu Lojistik'e gider, "özel ölçü" bağlantısı Lojistik panelinin altına taşınır |
| Sticky bilgi sütunu | Kaldırılır (soru 3) |
| 4'lü özellik şeridi, hikâye bölümü (rich/medium/table/brief) | Kaldırılır; yerine tek, sabit akordeon |
| Teknik özellikler föyü | Akordeondaki tabloya taşınır |
| Metindeki tablolar | Ürün Açıklaması panelinin sonunda gösterilir |
| Küçük resimler | Görselin üstüne bindirilmiş yuvarlak sıra (mobilde dokunma hedefi ≥44px) |
| Hover zoom, tıkla-tam ekran + ikinci kademe zoom, çerçevesiz büyük görsel | **Korunur** (canlıda da var) |
| Görsel köşe yuvarlatması ve zemin rengi | Kaldırılır |
| İlgili ürünler başlığı | Canlı stiline çekilir; kartlar tema kartı olarak kalır (soru 4) |
| Mobil alt WhatsApp çubuğu | Korunur |

**Veri engeli:** yerel havuzda 55 ürünün 54'ünde fotoğraf yok. Fotoğraf aktarımı ayrı bir iş; bu şablon fotoğrafsız ürünlerde de (kod plakasıyla) doğru çalışacak.

## 3. Veri eşlemesi

| Canlı alan | Kaynak |
|---|---|
| h1 ve konum satırının son öğesi | `wk_product_heading($p)` = kod + ad (ad kodla başlamıyorsa); SEO `name` de bunu kullanır |
| Fiyat | `price` + `wk_price_note()` |
| Kısa açıklama | `short` |
| Sepete ekle | `wk_add_to_cart_form` |
| Hızlı Sipariş | `wk_whatsapp( wk_order_text($p) )`; etiket panel alanı `wa_order` |
| Teknik Detaylar | `nwcs_product_pairs` (ilk satır "Ürün kodu"). Detay yoksa panelden düzenlenebilen boş-durum metni |
| Ürün Açıklaması | `body` (düz `wpautop`) + `tables`. Boşsa "Açıklama henüz girilmedi." (panel alanı) |
| Lojistik ve Teslimat | Site geneli metin `delivery_text` (panel). Üründe "Lojistik ve Teslimat" adlı detay satırı varsa o gösterilir ve tablodan düşer |
| Müşteri Görüşleri | "Henüz değerlendirme yapılmadı." + "Görüşünüzü WhatsApp'tan ya da formla iletebilirsiniz." Sahte yorum yok, olmayan bir mekanizma vaat edilmez |
| Galeri | `wk_gallery` |
| İlgili ürünler | Mevcut `$related`; başlık "İlgili ürünler" |

## 4. Tutarlılık
- Tek şablon, koşulsuz iskelet: konum → [özet | galeri] → 4 başlıklı akordeon → ilgili ürünler. Boş panellerde kısa, dürüst boş-durum metni gösterilir.
- Seviye ve bölümleme yardımcıları WK şablonundan çıkarılır; eklentiden silinmez (Koçist kullanıyor).
- Yalnızca `woodkocist-theme/**` değişir. Eklentiye, Koçist'e ve diğer sitelere dokunulmaz.

## 5. Dosyalar, adımlar, doğrulama

### Dosyalar

| Dosya | Değişiklik |
|---|---|
| `template-parts/product-detail.php` | Yeniden yazılır |
| `functions.php` | `wk_product_heading`, `wk_product_delivery_text`, Lojistik satırını tablodan ayıklama, SEO adı |
| `content-manifest.php` | `product.labels` altına yeni alanlar: `tab_desc`, `tab_delivery`, `tab_reviews`, `wa_order`, `delivery_text`, `specs_empty`, `desc_empty`, `reviews_empty`, `reviews_note`. `specs_title` → "Teknik Detaylar", `related_title` → "İlgili ürünler" |
| `assets/site.css` | Ürün bölümü (406–616) yeniden: ızgara 37/57, 80px boşluk, 1100px altında tek sütun; bindirmeli küçük resimler; akordeon; tablo; fiyat satırı; yeşil düğme (yalnızca ürün sayfasında); ilgili ürünler başlığı |
| `assets/site.js` | Galeri/lens/lightbox aynen kalır. Yeni ~30 satırlık akordeon: tek panel açık, `aria-expanded`/`aria-controls`/`hidden`; JS kapalıyken tüm paneller açık; reduced-motion'a uyar |
| Belgeler | DECISIONS, DEVAM, README |

### Adımlar
0. Başlangıç ekran görüntüleri: 3 WK ürünü 1440 + 390, ayrıca Koçist'ten 1 ürün.
1. Manifest alanları.
2. `functions.php` yardımcıları ve SEO adı.
3. Şablon.
4. CSS; canlıyla yan yana karşılaştırma.
5. JS akordeonu ve klavye kullanımı.
6. Akışlar: sepet, Hızlı Sipariş, "Formla sor", özel üretim.
7. Koçist değişmedi kanıtı.
8. Belgeler.

### Doğrulama
- 55 WK ürün adresi 200 döner.
- 3 ürün × 2 genişlik canlıyla karşılaştırılır.
- **Erişilebilirlik:**
  - akordeon klavyeyle kullanılabilir, odak halkaları görünür;
  - 390'da yatay kaydırma yok;
  - dokunma hedefleri ≥44px.
- PHP notice yok.
- JSON-LD, `<title>`, canonical ve sitemap bozulmadı.
- Sepet, Hızlı Sipariş ve form akışları çalışıyor.
- Panel önizlemesinde yeni alanlar doğru açılıyor.

### Riskler
- Panelde daha önce kaydedilmiş `specs_title` / `related_title` değerleri yeni varsayılanları ezer; panelden bir kez güncellenmeleri gerekir.
- h1'e kod eklenince `<title>` da değişir (canlıyla aynı olur).
- 54 fotoğrafsız ürün plakayla görünür; müşteri bunu düzen farkı sanabilir.

## 6. Açık sorular (varsayılan öneriyle)
1. **Yazı tipi:** Archivo kalır; ölçüler canlıya çekilir.
2. **h1 ve SEO adı:** kod + ad, canlıdaki gibi.
3. **Sticky sütun:** kaldırılır (canlıya sadık).
4. **İlgili ürün kartı:** tema kartı kalır, başlık canlıdaki gibi olur.
5. **Renk seçimi:** bu işte yok, Teknik Detaylar satırı olarak kalır.
6. **KDV:** canlıda "+ KDV", yerelde "KDV dahil". Kod değişmez; firma onayıyla panelden `vat_mode` ayarlanır.
7. **Müşteri Görüşleri:** sekme gösterilir, dürüst boş metinle.
8. **Lojistik:** site geneli tek metin; üründe "Lojistik ve Teslimat" detay satırı varsa o gösterilir. Canlıdaki metinlerin toplanması ayrı içerik işi.
9. **Hızlı Sipariş düğmesi:** WhatsApp yeşili #0b6f31.

**Kullanıcı kararı (30 Eylül 2026):** 7 sorunun hepsine EVET (varsayılanlar). KDV kipi firma kararı; koda dokunulmaz.

## 7. Roller
1. **Fable 5.1 HIGH:** mimar (bu belge).
2. **Opus 5.5 MEDIUM:** uygulama, frontend-design becerisiyle. Brief: canlıyı sadık kopyala. Yalnızca woodkocist-theme dosyaları değişir; commit yok.
3. **Yeni Fable 5.1 HIGH:** bağımsız inceleme.
4. **Opus 5.5 MEDIUM:** düzeltme ve doğrulama.

## 8. Uygulama notları (Opus 5.5, 30 Eylül 2026)

Adımlar 0–8 yapıldı; commit yok. Değişen dosyalar: `woodkocist-theme/` altında
`template-parts/product-detail.php`, `functions.php`, `content-manifest.php`, `assets/site.css`,
`assets/site.js`; belgeler DECISIONS, DEVAM, README. Eklenti, Koçist ve öteki temalar aynı.

**Plandan sapmalar ve kararlar**
- **Küçük resimler:** plan "60px yuvarlak" diyor. Canlının hesaplanmış stili ise 60×60, köşe
  6px, 2px iç boşluk ve #faf6f5 zemin (tema CSS'indeki 100px'i alt tema eziyor). Canlıdaki
  uygulandı. Telefonda 44px; sığmazsa tek satırda yana kayar.
- **Konum satırı:** "Mağaza" öğesi kaldı. Plan §2 yalnızca son öğeyi değiştiriyor; canlıda
  "Mağaza" yok. İnceleme karar versin.
- **Panelden kalkan alanlar:** `wa_question`, `wa_price`, `ask_why` şablonda kullanılmadığı için
  manifestten çıkarıldı. `related_all` da "İlgili ürünler" oldu (plan yalnızca `related_title`
  diyordu). `related_title` artık `{kategori}` gerektirmiyor; ipucunda isteğe bağlı olduğu yazıyor.
- **`delivery_text` varsayılanı** mevcut panel metinlerinden kuruldu (`shipping_note`,
  `lead_time`, fabrikadan teslim). Yeni vaat eklenmedi.
- **Fiyatsız ürün sırası:** "Fiyat için sorun" → kısa açıklama → Hızlı Sipariş → Formla sor.
- **Genişlik:** sitenin sarmalayıcısı (1280, kenar 32px) korundu, içerik alanı 1216px. Sütun oranı
  472:728 ve 80px boşluk canlıyla aynı; üst menüyle hizayı bozmamak için sayfaya özel genişlik
  açılmadı.
- **Açık başlık tekrar basılınca kapanır:** hiç açık panel kalmayabilir. Aynı anda en fazla bir
  panel açık.
- **JSON-LD:** `additionalProperty` "Lojistik ve Teslimat" satırını içermez (tabloyla aynı).
- **Panelde kayıtlı değer:** yerelde `product.labels.*` için kayıt yok, yeni varsayılanlar
  görünüyor. Canlıda kayıtlı `specs_title` ya da `related_title` varsa panelden güncellenmeli.

**Doğrulama özeti:**
- 55/55 ürün adresi 200 döndü. Her sayfada 1 h1, 4 akordeon başlığı ve tek `aria-expanded=true` var; `aria-controls` hedefleri mevcut. JSON-LD Product `name` h1 ile aynı, `sku`, `offers` (fiyatlıda) ve `additionalProperty` tamam. `<title>` h1 ile başlıyor, canonical doğru.
- Hızlı Sipariş wa.me mesajında ürün kodu var. Özel üretim ve (fiyatsızda) "Formla sor" bağlantıları `?urun=<id>` taşıyor; formlar ürünle dolu açılıyor.
- E_ALL ile 55 sayfada 0 PHP bildirimi. Geçici mu-plugin kaldırıldı.
- Sepete ekleme iki yolla çalışıyor: JavaScript'le panel + /sepet/, JavaScript'siz form → /sepet/.
- Klavye: Enter ve Space paneli açıyor, odak halkası görünür. JavaScript kapalıyken dört panel de açık.
- 390 genişlikte `scrollWidth` 390, taşma yok. Dokunma hedefleri ≥44px; tek istisna adet kutusu (30×48, canlıyla aynı).
- Koçist ürün sayfası HTML'i (nonce'lar normalize edilerek) değişmedi.

**İnceleme sonrası düzeltmeler (30 Eylül 2026):**
- **(1) Önizleme:** önizleme kipinde (`nwcs_is_preview()`) dört panel açık başlıyor; JS başlangıçta `aria-expanded`'a uyuyor. Ön yüzde yalnızca Teknik Detaylar açık.
- **(2) Tablet:** plaka tablette `100svh - 220px` ile sınırlı, oran korunuyor. 900px'te plaka 680px yüksekliğinde.
- **(3) Ölü CSS:** `.wk-add--lg` kuralları, `.wk-btn--wa` ve form çağrısındaki `wk-add--lg` sınıfı silindi.
- **(9) Kontrast:** akordeon başlığı #ad4d3e oldu (4.89:1).
- **(10) Küçük resimler:** 1100–1279px'te 50px ve 8px aralık; tek satırda kalıyorlar.
- **(13) Arama açıklaması:** kısa açıklama ve metin boşsa ad + ilk üç teknik detay kullanılıyor.
