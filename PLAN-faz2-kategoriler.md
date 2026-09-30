# 2. Faz — Kategori ↔ Site Yerleşimi, Panel Bilgi Mimarisi ve İş Akışları

Fable 5.1 (mimar), 30 Eylül 2026. Onay bekliyor; kod değişmedi.

Dayanak: 0.21.0'ın gerçek kodu, iki temanın katalog kodu, QA ekran görüntüleri (scratchpad qa-newcat) ve WP-CLI durumu.

**Kabul ölçütü (kullanıcı):** Müşteri hiçbir koda dokunmadan panelden:
1. yeni kategori ekler;
2. bu kategoriye ürün girer;
3. ürünlerin detay başlıklarını dilediği gibi seçer;
4. bunları WOOD KOCIST ve Koçist'te doğru yerde görür (menü, kategori sayfası, breadcrumb, mağaza/katalog).

Sistem 20-30 yıl sonra, bugünkü kimse yokken de kullanılabilir olmalı.

## 0. Kök neden (QA 1-3 aynı kökten)

"Bir kategori hangi sitede, hangi üst başlığın altında görünür" bilgisi **veri değil**:

- **WK**
  - Ağaç, ürünün hem seri kategorisinde (WOODGarden) hem yaprak kategoride olmasından türetiliyor (`inc/catalog.php:54-107` `wk_category_tree`, `:177-193` `wk_product_place`).
  - Yaprak sayfa metinleri manifestteki sabit `$categories` listesinden (7 kayıt, `content-manifest.php:38-80`) geliyor.
- **Koçist**
  - `$kocist_catalog` (manifest :28-105; 4 grup, 52 alt, 2 takma ad listesi) hem `kat-/grp-` panel sayfalarını hem `kocist_catalog_pool_map()` (`inc/catalog.php:321-346`) eşlemesini üretiyor.
  - Aynı liste bir de `menu_*` varsayılanlarında tekrar ediyor.
  - Listede olmayan kategori hiçbir gruba düşmüyor.
- **Görünürlük** ürün bazlı seçim listesinden geliyor (`nwcs_products_selected`). "Eşlik eden site" kuralı, kategoride ürün yokken çalışamıyor (`sync.php:801-832`).
- **DB:**
  - Menü/seri satırları iki sitede de hiç kaydedilmemiş; manifest varsayılanları geçerli.
  - Kayıtlı `kat-/grp-` içerik: Koçist 0, WK 1 (`kat-kedi-yuvalari`).
  - Sonuç: migrasyon yükü hafif.

İlke: **yerleşim veriye dönüşür, tek yerden (Kategoriler sayfası) yönetilir. Temalar yerleşimi okur, kendi listelerini taşımaz.**

## 1. Veri modeli: "Yerleşim"

- **Yerleşim kuralı.** Her havuz kategorisi, havuzu tüketen her site için ya gösterilmez ya da o sitenin bir **üst başlığı** altına yerleşir.
  - Üst başlıklar: WK'da seri (WOODPets/WOODGarden/WOODLiving), Koçist'te grup (Kereste/Ambalaj/Dekorasyon/Hırdavat).
  - Ağaç her sitede iki seviyelidir. Aynı kategori sitelerde farklı üst başlıkta olabilir.
- **Saklama.** Kategori term meta'sı `_nwcs_placement` = `{ site_key => parent_key }`. Taksonomide hiyerarşi kullanılmaz; tüm terimler `parent=0` kalır.
- **Üst başlık listesi siteden gelir** (panel verisi, zaten düzenlenebilir). Manifest yalnızca nereden okunacağını söyler:
  - WK: `'catalog' => array( 'parents' => array( 'rows' => array('home','catalog','lines'), 'key' => 'category', 'label' => 'label' ), ... )`.
  - Koçist: `'parents' => array( 'rows' => array('global','header','menu'), 'key' => 'key', 'label' => 'label' )`.
    - Yalnızca `key` dolu satırlar grup sayılır: kereste, ambalaj, dekorasyon, hirdavat. Kurumsal grup değildir.
    - `key` alanının etiketi: "Ürün grubu anahtarı (boşsa bu satır ürün grubu değildir)".
  - Yardımcı: `nwcs_site_parents(int $blog_id)`. Önce `nwcs_content` okunur, yoksa manifest varsayılanı kullanılır.
- **Görünürlük kuralı.** Kategori bir siteye yerleştiyse ürünleri o sitede görünür.
  - `selected/all` kipi değişmez.
  - Şu olaylarda ürün kimliği `nwcs_products_selected` listesinin sonuna eklenir ("hepsi" kipinde hiçbir şey yazılmaz):
    - yerleşim yapıldığında;
    - ürün kategoriye eklendiğinde;
    - ürün yaratıldığında (form, Excel, paket).
  - Yerleşim kaldırılırsa, o sitede başka yerleşik kategorisi olmayan ürünler seçimden çıkar. Onay metninde sayı yazar.
  - Sürekli senkron yok: Stüdyo'dan çıkarılan ürün geri eklenmez.
  - Sıra, siteye özel ad/fiyat ve gizleme İçerik Stüdyosu'nda aynen kalır.
  - **Canlı seçimler (blog 8: 283, blog 13: 55) migrasyonda değişmez**; md5 ile doğrulanır.
- **"Eşlik eden" kuralları kalkar.** `sync.php` içindeki eşlik eden kategori/site kuralları (`nwcs_sync_companions`) kaldırılır.
  - Yeni ürün yalnızca indirilen kategoriye girer.
  - WK ürünlerinin seri kategorisinde de olması (mevcut veri) zararsızdır; tema artık bunu şart koşmaz.
- **Kategori sayfaları eklenti tarafından üretilir.**
  - Manifest bir şablon verir. `nwcs_manifest_for_blog()` her yerleşik kategori için `kat-<havuz-slug>` sayfasını, üst başlıklar için `grp-<key>` / `seri-<key>` sayfasını ekler. Mevcut anahtar biçimi korunur.
  - Şablon:
```
'catalog' => array(
  'parents'     => ...,
  'page'        => array( 'path' => '/urun-kategori/{parent}/{slug}/', 'label' => '{name} (Mağaza)', 'hidden' => false,
                          'fields' => array( 'name' => …, 'title' => …, 'lead' => … ), 'seo_source' => array(...) ),
  'parent_page' => array( 'path' => '/urun-kategori/{parent}/', 'label' => 'Seri: {name}', 'hidden' => true, 'fields' => array( 'lead' => … ) ),
  'defaults'    => array( 'kopek-kulubeleri' => array( 'title' => …, 'lead' => … ), … ),   // eski $categories / $kocist_catalog metinleri
  'seed'        => array( 'kopek-kulubeleri' => 'WOODPets', … ),                            // tek seferlik yerleşim tohumu
)
```
  - `products` alanı sayfaya eklenti tarafından eklenir (`'category' => {name}`).
  - Koçist'in 50+ sayfası `hidden` kalır.
  - Manifest sonucu blog başına istek içi önbelleğe alınır; yerleşimler havuz transient'i ile gelir.
- **Koçist**
  - Alt kategori = yerleşik havuz kategorisi; adres `/kategoriler/<grup>/<havuz-slug>/`.
  - 52 alt kategorinin 48'inin slug'ı zaten aynı. 4'ü farklı; bunlar için 301 eklenir:

    | Eski slug | Yeni slug |
    |---|---|
    | kamelya | kamelyalar |
    | cardak | cardaklar |
    | ahsap-sezlong | ahsap-sezlonglar |
    | adirondack-sandalye | adirondack |

  - Havuzda karşılığı olmayan iki metin alt kategori (klipsler, baglama-telleri) menüde düz bağlantı olarak kalır.
  - **Takma adlar kalkar.** "Hayvan Barınakları" (WOODPets/Köpek/Kedi'yi kapsıyordu) ve "Ahşap Bank" (Piknik Masaları'nı kapsıyordu) yerine Köpek Kulübeleri, Kedi Yuvaları ve Piknik Masaları Dekorasyon altında kendi alt başlığı olur.
  - Grup sayfasının kendi havuz kategorisi yerleşim almaz. Migrasyon, yalnızca o kategoride olan ürünleri raporlar.
  - Üst menü açılır listeleri yerleşik kategorilerden çizilir. `menu_*` bileşenleri yalnızca kategori olmayan bağlantılar için kalır.

## 2. Panel bilgi mimarisi

Ürün Havuzu alt sayfaları, bu sırayla:
1. **Ürünler**: bugünkü liste ve form, "Çöp kutusu (N)" süzgeci, toplu işlemler. Kategori kutusu ve Excel buradan kalkar.
2. **Kategoriler** (yeni): kategori gezgini, kategori ayarları (ad, sitelerde yerleşim), Excel indir/yükle, ürün önizleme listesi (son yüklemede değişenler vurgulu), önizleme/onay, son işlemler. Yeni kategori burada açılır.
3. **Detay başlıkları**: mevcut ekran (+ QA 5).
4. **Ürün açıklamaları**: mevcut ekran (+ Son işlemler kartı).
5. **Havuz Paketi**: en sonda, "Kurulumlar arası taşıma (ileri düzey)".

Diğer değişiklikler:
- Ürün formundaki "Kategoriler" etiket kutusu kalır.
- Yardım şeridi: "1 Kategoriyi aç ve sitelere yerleştir · 2 Excel'i indir, doldur, yükle · 3 Görselleri Medya Havuzu'ndan ekle". "İçerik Stüdyosu'ndan seçin" cümlesi kalkar.

### Tasarım yönü (frontend-design)
- **Renk ve ölçü:** mevcut panel dili korunur: `--nwcs-brand #4f46e5`, teal, amber, soft; 12px köşe; sistem yazı tipi; punto 13/14/16/20/26.
  - Yeni renk yok.
  - "Değişti" = amber-soft zemin; "silinecek/çöp" = kırmızı ton.
- **Akılda kalacak tek öğe:** kategori başlığı altındaki "Nerede görünüyor" cümlesi, örneğin "WOOD KOCIST'te WOODGarden altında · Koçist'te gösterilmiyor". Geri kalan her şey sessiz.
- **Yazım ve numaralandırma:** tablo başlıkları ve rozetler cümle düzeninde (BÜYÜK HARF değil). Numara yalnızca gerçek sıralı adımlarda.
- **Boş durumlar yön verir:** "Bu kategoride henüz ürün yok. Excel'i indirin, doldurun, yükleyin." ve tek düğme.
- **Düğmeler:** her ekranda tek birincil düğme; metin akış boyunca aynı kalır ("Değişiklikleri uygula" → "Değişiklikler uygulandı").
- **Erişilebilirlik:** klavye odağı görünür, JS olmadan çalışır.

## 3. Ekranlar

### 3.1 Kategoriler
```
┌ Ürün Havuzu › Kategoriler                                      [+ Yeni kategori] ┐
│ ┌ Kategori ara… ┐   ┌──────────────────────────────────────────────────────────┐ │
│ │ Adirondack  13 │   │ Köpek Kulübeleri                          [Adı değiştir] │ │
│ │ Ahşap Bank  14 │   │ 13 ürün · WOOD KOCIST'te WOODPets altında ·             │ │
│ │ …              │   │ Koçist'te Dekorasyon altında                            │ │
│ │ ▸ Köpek Kul. 13│   ├─ Sitelerde ────────────────────────────────────────────┤ │
│ │ Kedi Yuval.  7 │   │ [✓] WOOD KOCIST   üst başlık [WOODPets      ▾]          │ │
│ │ …              │   │ [✓] Koçist        üst başlık [Dekorasyon    ▾]          │ │
│ │ Salıncaklar  0 │   │ İşaret kaldırılırsa ürünler o sitede görünmez.          │ │
│ │                │   │ Sayfa metni: WOOD KOCIST'te düzenle ↗ · Koçist'te ↗     │ │
│ │ Hiç sitede     │   │                                         [Kaydet]        │ │
│ │ görünmeyen: 3  │   ├─ Excel ────────────────────────────────────────────────┤ │
│ └────────────────┘   │ [Excel indir]  Düzenlenmiş dosya: [Dosya seç] [Yükle]   │ │
│                      │ Son yükleme: 30.09 09:12 · 2 güncellendi · 1 yeni [Geri al]│
│                      ├─ Ürünler (13) ── [Yalnızca son yüklemede değişenler] ──┤ │
│                      │ ▮ W-DOG-V05  Verandalı Telli…   48.500 ₺  11 detay  değişti│
│                      │   W-DOG-V04  Verandalı Mega…    29.000 ₺  11 detay        │ │
│                      └──────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────┘
```
- Sol liste: arama, ürün sayıları, "Hiçbir sitede görünmeyen" sayısı (tıklanınca süzer).
- Adres: `?page=nwcs-pool-categories&kategori=<slug>`.

### 3.2 Yeni kategori (satır içi form)
```
Yeni kategori
Adı: [Salıncaklar            ]   Benzer ad var: "Ahşap Salıncak" (12 ürün). Onu kullanmak için tıklayın.
Sitelerde: [✓] WOOD KOCIST → [WOODGarden ▾]   [ ] Koçist → [Dekorasyon ▾]
[Kategoriyi aç]   Açılınca bu sayfada kalırsınız: Excel'i indirip ürünleri girersiniz.
```
Benzer ad kontrolü `nwcs_search_fold` ile yapılır ve yalnızca uyarı verir, engellemez (QA 6d).

### 3.3 Excel önizlemesi (Kategoriler sayfasında)
- Mevcut önizleme kartı buraya taşınır.
- Yeni ürün satırlarında fiyat ve zorunlu başlık değerleri görünür (QA 6e).
- Site cümlesi yerleşimden gelir: "Bu ürünler WOOD KOCIST (WOODGarden) ve Koçist (Dekorasyon) sitelerinde görünecek."
- Kategori hiçbir siteye yerleşmemişse amber uyarı: "Bu kategori hiçbir siteye yerleşmedi; ürünler yüklenir ama sitede görünmez. Yükledikten sonra yukarıdaki 'Sitelerde' kutusundan yerleştirin."
- Hatalı satırların hepsi aynı zorunlu başlıkta takıldıysa: "13 satırın hepsinde 'Kullanım Alanı' boş. Bu başlık bu kategoride kullanılmıyorsa Detay başlıkları'ndan zorunlu işaretini kaldırın ↗" (QA 5b).

### 3.4 Son işlemler (Kategoriler ve Ürün açıklamaları'nda ortak kart)
```
Son işlemler (en yeniden eskiye geri alınır)
● 09:12 Köpek Kulübeleri · ürün Excel'i · 2 güncellendi, 1 yeni          [Geri al]
  09:03 Adirondack · ürün Excel'i · 2 güncellendi                         (önce üsttekini geri alın)
  08:50 Adirondack · açıklamalar · 10 güncellendi
```
- QA 4 çözümü: tek kayıt yerine `nwcs_pool_history` yığını tutulur.
  - Son 10 işlem saklanır; her kayıt bugünkü `nwcs_sync_last` / `nwcs_desc_last` yapısı + `kind`.
  - Yalnızca en üstteki geri alınır (LIFO). Sonraki işlemler önce geri alındığından "elle düzenlendi" denetimi yanlış alarm vermez. Elle düzenleme denetimi korunur.
- Önizlemede sessiz ezme kalkar: "Uygularsanız geri alma sırası: önce bu yükleme, sonra Adirondack (09:03)."

### 3.5 Detay başlıkları (QA 5)
- Satıra "Boş olduğu kategoriler" eklenir: "Köpek Kulübeleri 13/13, Kedi Yuvaları 7/7 ve 40 kategori daha" (`nwcs_heading_gaps()`).
- Başlık zorunlu yapılınca bildirim: "'Kullanım Alanı' zorunlu yapıldı. Şu kategorilerin Excel'i bu sütun dolmadan yüklenmez: …"
- Zorunluluk global kalır.

### 3.6 Ürünler
- Fiyat hücresi `white-space: nowrap` (QA 6b).
- Toplu işlem sonrası `kategori` / `ara` süzgeci korunur (QA 6a).
- Satırdaki detay özeti: "11 detay: Ahşap Cinsi, Boyut Sınıfı, …" (ilk 3).

## 4. Kullanıcı iş akışları

**A. Yeni kategori → iki sitede görünür (kabul ölçütü)**
1. Ürün Havuzu › Kategoriler › "+ Yeni kategori" açılır.
   - Ad: "Salıncaklar".
   - Siteler: WOOD KOCIST [✓] → WOODGarden, Koçist [✓] → Dekorasyon.
   - "Kategoriyi aç".
2. Sayfa Salıncaklar'da kalır. "Nerede görünüyor" satırı iki siteyi söyler. Liste boş: "Excel'i indirin, doldurun, yükleyin."
3. "Excel indir" ile `urunler-salincaklar-<tarih>.xlsx` iner (yalnızca zorunlu başlıklar ve sistem sütunları).
4. Dosya doldurulur. "Nasıl kullanılır" sayfası numaralı 1-8 adım; açıklama dosyasında da numaralı (QA 6c).
5. "Yükle" → önizleme: "2 yeni ürün · WOOD KOCIST (WOODGarden) ve Koçist (Dekorasyon) sitelerinde görünecek · yeni başlık: Oturma Kapasitesi (opsiyonel)" → "Değişiklikleri uygula".
6. Şerit "Değişiklikler uygulandı" der. Ürünler "yeni" rozetiyle listelenir, Son işlemler'de kayıt oluşur.
7. Sitede kontrol:

   | Site | Kontrol edilecekler |
   |---|---|
   | WK | Üst menüde WOODGarden altında "Salıncaklar 2"; `/urun-kategori/woodgarden/salincaklar/`; breadcrumb Anasayfa / Mağaza / WOODGarden / Salıncaklar; kenar liste; `?kategori=` süzgeci |
   | Koçist | Menüde Dekorasyon › Salıncaklar; `/kategoriler/dekorasyon/salincaklar/`; katalog; kartta kategori etiketi |

8. Tanıtım cümlesi "Sayfa metni: … düzenle ↗" bağlantısından düzenlenir. Bağlantı İçerik Stüdyosu'nda `kat-salincaklar` sekmesini açar.

**B. Mevcut kategoride güncelleme**
1. Kategoriler › kategori › Excel indir.
2. Dosya düzenlenir, Yükle.
3. Önizlemede değişen alanlar tablosu → uygula.
4. Listede "değişti" rozetleri görünür. Yanlışsa Son işlemler › Geri al.

**C. Ürün açıklaması yükleme**
1. Ürün açıklamaları › kategori › Excel indir.
2. Dosya doldurulur, Yükle.
3. Önizleme → uygula. Son işlemler kartı aynı, geri alma LIFO.

**D. Detay başlığı yönetimi**
- Yeni başlık açmanın iki yolu:
  - Excel'de yeni sütun (en az bir hücre dolu);
  - Detay başlıkları › "Başlık ekle".
- Zorunlu yapmadan önce satırdaki "boş olduğu kategoriler" kontrol edilir.
- Yeniden adlandır ve birleştir mevcut.

**E. Geri alma**
- Son işlemler'de en üstteki "Geri al".
- Özet şerit çıkar, bir sonraki kayıt üste çıkar.

## 5. Tema değişiklikleri
- **Eklenti:** yeni dosya `includes/product-categories.php`. İçerdiği fonksiyonlar:
  - `nwcs_category_placements()`
  - `nwcs_set_placement($slug, $site_key, $parent|null)`
  - `nwcs_site_parents($blog_id)`
  - `nwcs_site_category_tree($blog_id)`
  - `nwcs_product_place($product, $blog_id)`

  Ayrıca: manifest genişletme, görünürlük olayları ve tek seferlik tohum.
- **WK:**
  - `wk_category_tree` → `nwcs_site_category_tree`.
  - `wk_product_place` → `nwcs_product_place`.
  - `wk_lines()['count']` ve `wk_filter_products(seri)` "seride ya da altındaki bir kategoride" mantığıyla çalışır.
  - `$categories` → `catalog.defaults` + `catalog.seed`.
  - Ürün sayfası ve SEO'ya dokunulmaz.
- **Koçist:**
  - `kocist_catalog_groups()` alt başlıkları ve `kocist_catalog_pool_map()` yerleşimden gelir.
  - `kat-` anahtarı havuz slug'ı olur.
  - Breadcrumb (`kocist_catalog_trail`) yeni sayfalara bağlanır.
  - `$kocist_catalog` → `catalog.defaults` / `catalog.seed`; `menu_*` varsayılan çapaları kalkar.
  - Arama (isteğe bağlı): sonuç sayfası havuz ürünlerini de listeler (ad, kod, kısa açıklama).
- Breadcrumb, sitemap ve SEO eklentinin ürettiği `kat-/grp-/seri-` sayfaları üzerinden çalışır.
- Havuzu tüketmeyen 8 sitede `catalog` yok; hiçbir şey değişmez.

## 6. Geriye uyum ve migrasyon
- **Tohum (tek seferlik):** `admin_init` + `nwcs_placement_seed_version`, `catalog.seed`'den yerleşim yazar (WK 7 yaprak, Koçist 52 alt + takma adlar Dekorasyon'a).
- **Koçist slug geçişi:** kayıtlı `kat-<eski-slug>` değerleri yeni anahtara kopyalanır. 4 adres 301 ile yönlenir.
- **Site seçimleri değişmez;** tohum öncesi/sonrası md5 aynı olmalı.
- **Geri alma kayıtları:** `nwcs_sync_last` ve `nwcs_desc_last` → `nwcs_pool_history`.
- **Havuz Paketi v3:** `placements` taşınır; v1 ve v2 kabul edilir.
- **Belgeler:** README'nin "Hangi site neyi gösterir" bölümü yeniden yazılır.

## 7. Adım sırası (her biri doğrulanabilir)

| # | Adım | Doğrulama |
|---|---|---|
| 1 | `product-categories.php` + `products.php` (`nwcs_pool_categories()` yerleşimi döndürür, cache v4) | WP-CLI ile ağaç çıktısı |
| 2 | Manifest genişletme (`manifest.php`), WK ve Koçist manifestlerinde `catalog` bloğu | `kat-*` sayısı = yerleşik kategori sayısı; mevcut 7 WK anahtarı aynı |
| 3 | Tohum + Koçist slug kopyası + 4 yönlendirme | Seçim md5 aynı; yerleşim sayısı 7 + 52 |
| 4 | WK teması yerleşime geçer | Mevcut 7 kategori sayfası HTML diff'i aynı; Salıncaklar senaryosu |
| 5 | Koçist teması yerleşime geçer | 52 alt sayfa 200; 4 adet 301; menü aynı (+Köpek/Kedi/Piknik) |
| 6 | Görünürlük olayları; `nwcs_sync_companions` kaldırılır | Boş kategoriye Excel'den ürün → seçime girer; Stüdyo'dan çıkarılan geri eklenmez |
| 7 | Kategoriler sayfası (`admin/pool-categories.php`; kategori kutusu ve Excel `pool.php`'den taşınır; önizleme; `admin.css`) — frontend-design | — |
| 8 | Son işlemler yığını, LIFO geri alma | QA 4 senaryosu |
| 9 | Detay başlıkları: boş olduğu kategoriler + bildirim + önizleme ipucu (QA 5) — frontend-design | — |
| 10 | Küçükler (QA 6) + Ürünler yardım şeridi — frontend-design | — |
| 11 | Koçist araması (isteğe bağlı) | — |
| 12 | README, DECISIONS, sürüm 0.22.0 | — |

## 8. Doğrulama
- **Uçtan uca, kod dokunmadan:** Salıncaklar akışı (A), iki sitede menü, kategori sayfası, breadcrumb, katalog, sitemap, JSON-LD. İçerik Stüdyosu'nda tanıtım cümlesi düzenlenir ve sitede görünür.
- **Regresyon:**
  - blog 8/13 seçim md5 aynı;
  - WK'daki 7 kategori sayfası ve Koçist'teki 52 sayfanın HTML diff'i;
  - ürün sayfaları;
  - 4 adet 301;
  - Havuz Paketi v2 dosyası.
- **Diğer:** QA 4-6 senaryoları; PHP hata günlüğü boş.

## 9. Riskler
- **Manifest havuza bağımlı hale gelir.** Tema boş dönüşü zaten tolere ediyor.
- **Koçist menüsüne 3 öğe eklenir:** takma adlar kalkıyor (kullanıcı onayı).
- **Yalnızca grup kategorisinde olan ürün yetim kalır.** Migrasyon bunu raporlar.
- **Yerleşimi kaldırmak seçim listesine yazar.** Onay diyaloğu sayı gösterir, işlem Son işlemler'den geri alınabilir.
- **Performans:** yerleşimler havuz transient'inde, ağaç istek içi statikte tutulur.

## 10. Açık sorular (önerilen varsayılan)
1. Koçist takma adları kalksın; Köpek, Kedi ve Piknik kendi alt başlığı olsun mu? → Evet.
2. Koçist'teki 4 alt adres havuz slug'ına geçsin (301 ile) mi? → Evet.
3. Geçmiş derinliği ne olsun? → 10 işlem; yalnızca en üstteki geri alınır.
4. Kategori adı değiştirilebilsin mi? → Evet, yalnızca ad (slug sabit).
5. Yerleşim kaldırılınca ürünler o sitenin seçiminden çıksın mı? → Evet, onayda sayı gösterilerek.

**Kullanıcı kararı (30 Eylül 2026):** 5 sorunun hepsine EVET; geçmiş derinliği 10. Uygulama onaylandı.

## 11. Roller

| Adım | Model | Görev |
|---|---|---|
| 1 | Fable 5.1 HIGH | Mimari plan (bu belge) ✔ |
| 2 | Opus 5.5 MEDIUM | Uygulama, adım 1→12. frontend-design: adım 7, 9, 10 |
| 3 | Yeni Fable 5.1 HIGH | Bağımsız inceleme. Bakılacaklar: tohum idempotent ve seçimleri bozmuyor, sayfa anahtarları korunuyor, WK/Koçist HTML diff, görünürlük olayları yalnızca ekleme yapıyor, LIFO bütünlüğü, nonce/yetki/`restore_current_blog`, 301'ler |
| 4 | Opus 5.5 MEDIUM | Düzeltme ve 8. bölümün yeniden koşulması |

## 12. Uygulama notları (Opus 5.5, 30 Eylül 2026)

Adımlar 1-10 ve 12 uygulandı; 11 (Koçist araması, isteğe bağlı) yapılmadı. Plandan sapmalar:

1. **Tohum `init`'te, site başına.** `admin_init` yerine `init` (öncelik 30): canlıya
   dağıtımdan sonra yönetici girene kadar kategori sayfaları 404 olmasın. Sürüm tek sayı değil
   `{ site_key => 1 }`: geliştirme sırasında bir istek yalnızca WK manifesti hazırken tohumu
   çalıştırıp Koçist'i atlamıştı; site başına işaretle sonradan `catalog` kazanan tema da
   tohumlanır. Yalnızca boş yerleşim yazılır.
2. **Manifest `catalog` bloğuna eklenenler:** `parent_prefix` (seri-/grp-), `order`
   (`first_product`: WK'da alt kategori sırası mağazadaki ilk ürüne göre — eski davranış, HTML
   diff 0 için şart), `after` (üretilen sekmelerin yeri), `parent_pool` (Koçist grubunun kendi
   havuz kategorisi: yalnızca "Ahşap Ambalaj"da olan 6 ürün grup sayfasında kalsın diye),
   `parent_defaults`, `legacy` (4 eski slug → 301 ve kayıtlı içerik kopyası).
3. **Koçist menü adları korunur:** üretilen `kat-` sayfasına "Kategori adı" alanı eklendi;
   varsayılanı eski menü metni (`catalog.defaults`). Menü, kategori sayfası başlığı ve
   breadcrumb buradan; tıklayınca bu alan açılır. `menu_*` varsayılanlarında yalnızca Klipsler
   ve Bağlama Telleri kaldı; kayıtlı eski menüde kalmış kategori çapaları yok sayılır.
4. **Yerleşim kaldırma onayı sunucuda** (JS'siz): sayı varsa "Kaldırmadan önce" kutusu, Evet/Vazgeç.
5. **Görünürlük:** Excel'le çöp kutusundan geri gelen ürün de yeni ürün gibi seçime eklenir
   (yoksa "görünecek" cümlesi yanlış olurdu). Geri almada yalnızca gerçekten geri alınan
   ürünler seçimden çıkar (önceden "elle düzenlendi" diye atlanan ürün de çıkıyordu).
6. **QA 4 kök nedeni:** yığın tek başına yetmedi; üstteki işlemi geri almak ürünü yeniden
   yazıp `post_modified`'ı "şimdi" yapıyordu, alttaki geri alma ürünü "elle düzenlendi" sayıp
   atlıyordu. Geri alınan ürünün değişiklik zamanı işlem öncesine konur
   (`nwcs_restore_post_modified`); ürün ve açıklama kayıtları zamanı saklar.
7. **Excel bölümü kategori başına:** Kategoriler sayfasında her kategorinin kendi "Excel indir /
   Yükle"si var; başka kategorinin dosyası yüklenirse önizleme o kategorinin sayfasında açılır.
   Ürünler sayfasından Excel ve kategori kutusu kalktı; eski "Excel yükle" şeridi yok.
8. **Kategori silinince** sitelerdeki `kat-<slug>` sayfa metni (varsa) silinmez: yazılan metin
   habersiz kaybolmasın. Aynı slug'la yeniden açılırsa metin geri gelir.
9. `wp-header-end` işareti Ürünler, Kategoriler, Detay başlıkları sayfalarına eklendi
   (WordPress bildirimleri mor şeridin içine taşıyordu).
10. Kategoriler sayfasında form düğmeleri yapışkan değil (uzun sayfada satırların üstüne biniyordu).

Doğrulama özeti: WK 67 sayfa (7 kategori, 3 seri, mağaza, ana sayfa, 55 ürün) HTML birebir
aynı; Koçist'in 341 sayfasından 304'ünde fark yalnızca üst menüde (Dekorasyon'a 3 alt başlık, 4 adres),
37 sayfa beklenen farkla (Dekorasyon alt sayfalarının kenar listesi, Hayvan Barınakları'nın
takma ad ürünleri Köpek/Kedi/Piknik'e geçti, bu ürünlerin breadcrumb'ı). 4 × 301, 338/338
ürün sayfası 200, blog 8/13 seçim md5 değişmedi.

### İnceleme sonrası (Opus 5.5, 30 Eylül 2026)

İncelemenin Y1, O1-O4, D1-D7 bulguları işlendi (ayrıntı DECISIONS.md). Plandan ek sapmalar:
üst başlık anahtarı satır metninin slug'ı (havuz adına göre değil); kategori silme de "Son
işlemler"e girer (`kind = kategori`); WK manifestine `hide_empty`; aynı anahtarda elle tanımlı
`kat-` sayfası varsa üretilen kazanır.

