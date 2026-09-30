# Ürün Havuzu — Kategori Bazlı Excel Senkronu: Revize Mimari Plan

Hazırlayan: Fable 5.1 (mimar, Role.md 1. adım), 29 Eylül 2026.
Durum: onay bekliyor, kod değişmedi.
İlk planın (serbest Excel ve sütun eşleştirme sihirbazı) yerini alır.

## 0. Öncekine göre ne değişti

| Konu | Önceki | Yeni |
|---|---|---|
| Excel kaynağı | Müşterinin serbest dosyası ve eşleştirme sihirbazı | Panelden kategori bazlı indirilen taslak; eşleştirme yok |
| Boş hücre | Silmez | **Boş hücre = temizle.** Zorunlu başlık boşsa satır hatası |
| Silme | Yok | Dosyada olmayan ürün çöp kutusuna gider (önizleme, onay, geri alma) |
| Ürün açıklaması | Aynı dosyada | Ayrı "Ürün Açıklamaları" ekranı, ürün koduyla eşleşir |
| Opsiyonel başlıklar | Global liste | Kategoriye göre türetilir |
| Serbest sihirbaz | Geliştirilecekti | Kaldırılacak (öneri) |

Veri modeli (`_nwcs_details`, türetilmiş `_nwcs_spec`, `nwcs_product_headings` kaydı) aynen korunuyor.

## 1. Doğrulanmış bulgular

### 1.1 Havuz (WP-CLI, blog 1)
- Havuzda 321 ürün ve 63 kategori var; hiyerarşi yok.
- 61 ürün birden çok kategoride. WK'nın 56 ürününün hepsi iki kategoride: bir yaprak kategori (Köpek Kulübeleri, Çardaklar…) ve bir seri (WOODPets, WOODGarden, WOODLiving).
- **Çardaklar, Kamelyalar ve Adirondack kategorileri WK ve Koçist ürünlerini birlikte içeriyor.**
- Spec durumu:
  - 228 üründe boş (Koçist);
  - 8 üründe serbest not;
  - kalanlarda 44 farklı etiket var. "•", "0", "1" gibi anlamsız etiketler de bunlara dahil; tohumlamada ayıklanacaklar.
- Site seçimleri: blog 13'te 55 ürün, blog 8'de 283 ürün seçili; override yok.
- Ortam: PHP 8.3, ZipArchive var, WP 7.1.2.

### 1.2 Kategori bazında başlık değişkenliği (56 WK ürünü)

| Kategori | Tüm ürünlerde ortak başlık | Opsiyonel başlıklar |
|---|---|---|
| Köpek | 10 | Yüzey Koruması 11/13, Kapı Ölçüleri 10/13 |
| Çardak | 13 | Havalandırma 1/5, Taşıyıcı Donanım 3/5 |
| Kamelya | 11 | Zemin Ölçüsü, Çatı Ölçüsü, Güvenlik Donanımı, Havalandırma |
| Adirondack | 10 | yok |
| Kedi | — | Gövde Rengi 4/7 |
| Piknik | — | Zemin Yapısı 2/3 |

- **56 ürünün hepsinde ortak olan başlıklar:** Ahşap Cinsi, Boyut Sınıfı, Yapı Özelliği, Kurulum, Teslimat.
- Başlıklar kategori içinde de tutarsız. Bu yüzden zorunlu küme kategori bazlı olursa geçerli satırlar da reddedilir.

### 1.3 Fiyat hatasının kök nedeni
- `import.php:298-299` "boş hücre silmez" kuralını tanımlıyor. `import.php:346-348` bu yüzden boş fiyatı yazmıyor; eski fiyat kalıyor.
- Aynı kural kısa açıklama, spec, body ve kategoriye de uygulanıyor.
- Hata yalnızca Excel yolunda. Havuz formu koşulsuz yazdığı için orada boşaltma çalışıyor.

### 1.4 Çöp kutusu
- `post_status 'any'` çöpteki ürünleri bulmuyor.
  - `nwcs_product_id_by_code()` çöpe atılmış bir ürünün kodu yeniden gelirse kopya ürün oluşturur.
  - Senkron eşleşmesi bu yüzden çöpü de açıkça aramalı.
- Çöpe atılan ürün sitelerden kendiliğinden kalkar. Geri getirilince, site seçim listesi değişmediği için aynı sıradaki yerine döner. WordPress çöpü 30 gün sonra kendisi temizler.

### 1.5 Excel yazma
- Bugün yazıcı yok.
- `scripts/dev/make-test-xlsx.php` kütüphane olmadan (ZipArchive ile) geçerli bir xlsx üretilebildiğini kanıtlıyor. Gizli sütun, gizli sayfa, dondurulmuş başlık ve kalın yazı küçük XML parçalarıyla eklenebilir.

### 1.6 Tema bağımlılığı
- WK temasında `wk_product_place()` ürünün seri kategorisinde (WOODPets…) olmasını şart koşuyor. Serisi olmayan yeni ürün seri sayfalarında ve breadcrumb'da (sayfa yolu) eksik kalır; çözüm 2.4'teki "eşlik eden kategori" kuralı.
- Koçist teması kategorileri adıyla eşliyor; kategori adı değiştirilirse eşleşme bozulur. Bu planda kategori adı değişmiyor.

## 2. Veri modeli
1. **Değerler:** `_nwcs_details` = sıralı `[{label, value}]`.
   - `_nwcs_spec` bundan türetilir; tek yazıcı `nwcs_product_write_details()`.
   - Okuma tembel: meta yoksa spec ayrıştırılır, migrasyon gerekmez.
   - Cache sürümü v3 olur.
2. **Başlık kaydı:** `nwcs_product_headings` = `{anahtar: {label, required, order}}`, yeni dosya `includes/product-headings.php`.
3. **Zorunlu başlıklar global kalır.**
   - İndirilen taslak zorunlu sütunları zaten içerir.
   - Veri kategori içinde de tutarsız.
   - Kural yalnızca Excel yüklemesini etkiler; formda eksik zorunlu başlık sadece uyarı verir.
4. **Kategoriye özgü opsiyonel başlıklar saklanmaz, her seferinde türetilir.**
   - Küme: zorunlu başlıklar + kategorinin yayındaki ürünlerinden en az birinde dolu olan opsiyoneller.
   - Ürünü olmayan kategoride yalnızca zorunlu başlıklar çıkar.
   - Excel'e eklenen ve en az bir hücresi dolu olan yeni sütun kayda opsiyonel olarak eklenir. Bir sonraki indirmede o kategoride sütun olarak gelir.
5. **Kategori kuralları:**

   | Durum | Ne olur |
   |---|---|
   | Mevcut ürün | Kategorileri korunur |
   | Yeni satır | İndirilen kategoriye ve "eşlik eden kategorilere" eklenir: kategorideki tüm ürünlerin ortak olduğu diğer kategoriler (Köpek Kulübeleri → WOODPets) |
   | Silinen satır | Ürün bütünüyle çöpe gider. Önizlemede ürünün diğer kategorileri gösterilir |

## 3. Excel taslağı

Dosya adı: `urunler-<kategori>-<tarih>.xlsx`. Üç sayfa var:

1. **Ürünler**
   - Sütunlar: `KİMLİK` (gizli) | `ÜRÜN KODU` | `ÜRÜN ADI *` | `FİYAT` | `KISA AÇIKLAMA` | zorunlu başlıklar (`*` ile) | kategoriye özgü opsiyoneller.
   - Sütunlar kayıttaki sırayla gelir; başlık satırı dondurulmuş ve kalın.
   - Hücreler metin olarak yazılır; fiyat ve kodlar bozulmaz.
2. **Nasıl kullanılır:** 8-10 satırlık düz Türkçe talimat.
3. **_bilgi** (gizli): kategori, indirme zamanı, sürüm, kaynak.
   - Yükleme kategoriyi buradan okur; kullanıcı hiçbir şey seçmez.
   - Bu sayfa yoksa şu hata verilir: "Bu dosya panelden indirilen taslak değil."

Yeni dosya `includes/xlsx-writer.php`. Okuyucu sayfa adına göre okuyacak şekilde genişletilir.

## 4. Senkron algoritması
- **Eşleşme sırası:**
  1. KİMLİK (çöptekiler dahil);
  2. yoksa ÜRÜN KODU (yayındakiler ve çöptekiler);
  3. ikisi de yoksa yeni ürün.
- Çöpteki bir ürün eşleşirse geri getirilir. Kod değiştiyse yeniden adlandırılır; slug hiç değişmez. Kodu boş yeni ürüne otomatik kod verilir.
- **Satır hataları** (hatalı satır yazılmaz, ürün olduğu gibi kalır):
  - ad boş;
  - zorunlu başlık boş;
  - kod dosyada iki kez geçiyor;
  - kod başka bir üründe kullanılıyor;
  - KİMLİK geçersiz ya da iki satırda.
- **Boş hücre = temizle:**
  - boş fiyat "Teklif al"a döner;
  - boş kısa açıklama temizlenir;
  - boş opsiyonel başlık üründen çıkarılır.
- **Excel'de olmayan, dokunulmayan alanlar:** görseller, uzun açıklama, tablolar, sıra, diğer kategoriler, site özelleştirmeleri.
- **Fiyat:** yazıldığı gibi alınır. Excel sayıya çevirdiyse "3.500 ₺" biçimine getirilir.
- **Silme:** yükleme anında kategoride yayında olup dosyada bulunmayan ürünler çöpe gider (onay kutusu işaretliyse).

## 5. Akış
1. Ürün Havuzu → Kategoriler: her satırda "Excel indir" var, üstte tek bir "Excel yükle" alanı.
2. Dosya yüklenince önce plan hesaplanır, hiçbir şey yazılmaz. Önizleme kartı gösterilir:
   - özet: "5 güncellenecek · 2 yeni · 1 çöp kutusuna · 1 hata";
   - ayrıntılı listeler, yeni başlıklar ve bayat dosya uyarısı ("İndirdikten sonra panelde değişen 2 ürün var; Excel kazanacak");
   - seçenekler: [x] "Dosyada olmayan N ürünü çöp kutusuna taşı", Uygula, Vazgeç.
3. Uygula ile değişiklikler yazılır; geri alma kaydı tutulur, cache temizlenir. Önizlemeden sonra değişen ürün atlanır ve raporlanır.
4. **Geri alma** tek seviyelidir (son yükleme):
   - yeni ürünler çöpe gider;
   - güncellemeler eski haline döner;
   - çöpe gidenler geri gelir;
   - bu yüklemede doğan başlıklar kaldırılır.
5. **Çöp kutusu görünümü:** her üründe Geri getir ve Kalıcı sil. Formdaki "Ürünü sil" de artık çöpe atar.

Mimari: admin-post, nonce ve transient kullanılır (Havuz Paketi kalıbı). JavaScript zorunlu değil.

## 6. Ürün Açıklamaları ekranı
- Ekran bir kategori (veya "Tüm ürünler") seçip Excel indirmeye izin verir: `KİMLİK` (gizli) | `ÜRÜN KODU` | `ÜRÜN ADI` (bilgi amaçlı) | `ÜRÜN AÇIKLAMASI`.
- **HTML → metin (indirme):**
  - kalın alt başlıklar kendi satırına yazılır;
  - paragraflar boş satırla ayrılır;
  - karmaşık HTML içeren hücreler "[HTML içerik — panelden düzenleyin]" ile işaretlenir; değişmemişse yüklemede atlanır.
- **Metin → HTML (yükleme):**
  - boş satır yeni paragraf olur;
  - noktasız kısa tek satır alt başlık olur.
- **Önizleme:** güncellenecek / değişmedi / boşaltılacak / hata sayıları.
- Boş hücre açıklamayı siler; açıklaması boş ürünün sayfası kapanır (açık soru 3).

## 7. Yönetim
- Kategoriler kutusu aynen kalır; her satıra yalnızca "Excel indir" eklenir.
- **Detay başlıkları ekranı:** tek tablo.
  - Sütunlar: Başlık, [x] Zorunlu, kaç üründe ve hangi kategorilerde, sıra ↑↓, Yeniden adlandır / Birleştir / Sil (0 üründe).
  - Bir başlık zorunlu yapılırken "N üründe boş" gösterilir.
- **Ürün formu:** "Detay başlıkları" bloğu. Zorunlular üstte, eksik rozeti var, "+ Başlık ekle" ile yeni satır eklenir.

## 8. Serbest Excel sihirbazı → kaldırılır
- Gerekçeler:
  - iki ayrı yükleme yolu kafa karıştırır;
  - kurulumlar arası taşıma için Havuz Paketi yeter.
- Ortak yardımcılar `includes/admin/sync.php` dosyasına taşınır.

## 9. Paket
- Paket sürümü v2 olur ve `details` ile `headings` taşır; v1 de kabul edilir.
- Snapshot'a `details` eklenir.

## 10. Temalar
- Eklentiye `nwcs_product_pairs()` eklenir: önce details, yoksa spec; başlık kaydındaki sıraya göre.
- WK ve Koçist bu fonksiyona geçer.
- Tutarlılık veri tarafında sağlanır: aynı kategori aynı sütun setini kullanır, zorunlu başlıklar her sayfada bulunur, sıra kayıttan gelir.
- cc9d3bb düzeni korunur. Teknik Detaylar föyü frontend-design ile elden geçer: ilk satır "Ürün kodu", geniş ekranda iki sütun.

## 11. Dosyalar ve adım sırası

**Dosyalar**
- Yeni: `product-headings.php`, `xlsx-writer.php`, `admin/sync.php`, `admin/pool-headings.php`, `admin/pool-descriptions.php`, `assets/pool-details.js`.
- Değişen: `products.php`, `xlsx-reader.php`, `pool.php`, `import.php` (küçülür), `import.js`, `admin.css`, `panel.php`, `pool-package.php`, `network-content-studio.php` (0.21.0).
- Temalar: woodkocist `product-detail.php` ve `functions.php`; kocist `product-detail.php` ve `inc/catalog.php`.
- Belgeler: `README.md`, `DECISIONS.md`.

**Adım sırası**
1. Başlık kaydı, details okuma, cache v3, tohumlama
2. Tek yazıcı ve form bloğu (frontend-design)
3. Detay başlıkları ekranı (frontend-design)
4. xlsx yazıcı ve kategori "Excel indir"
5. Senkron: yükleme → önizleme → uygula → geri alma, çöp kutusu (frontend-design)
6. Ürün Açıklamaları ekranı
7. Sihirbazın kaldırılması, README
8. Paket v2
9. Temalar ve föy (frontend-design)
10. DECISIONS, sürüm

## 12. Doğrulama

**Köpek Kulübeleri test dosyası:** indirilen taslaktan türetilir.

| Senaryo | Beklenen |
|---|---|
| Fiyat boşaltıldı | "Teklif al" |
| Satır silindi | Ürün çöpe gider |
| Kodsuz yeni satır | Otomatik kod; Köpek Kulübeleri + WOODPets |
| Yeni sütun "Isıtma" | Opsiyonel başlık olarak eklenir |
| Zorunlu başlık boş | Satır hatası |
| Kod değişikliği | Kod güncellenir, slug aynı kalır |
| Çakışan kod | Satır hatası |
| `_bilgi` sayfası yok | "Taslak değil" hatası |

**Diğer testler**
- Çardaklar indirmesi (paylaşılan kategori).
- Boş "Salıncak" kategorisi: sadece zorunlu sütunlar.
- WP-CLI kontrolleri, tarayıcı kontrolleri.
- Regresyon: Koçist'in 283 ürünü, arama, SEO, teklif formu.

## 13. Riskler
- **Paylaşılan kategoriler:** WK'nın Çardaklar Excel'inde Koçist çardakları da görünür. Silinirlerse Koçist'ten de gider (çöpe; geri alınabilir).
- **Excel'in otomatik dönüştürmeleri:** tarihe dönen "1/2" gibi değerler. Talimat sayfası uyarır.
- **Tek yazıcı disiplini:** details/spec'i her yol aynı fonksiyonla yazmalı.
- Diğerleri:
  - bayat dosyada son yazan kazanır;
  - her admin-post yolunda nonce, yetki ve `restore_current_blog` bulunmalı.

## 14. Açık sorular (varsayılan öneriyle)
1. Serbest sihirbaz kaldırılsın mı? → Evet
2. Excel'den silinen ürün bütünüyle çöpe mi gitsin (yalnızca kategoriden çıkarılmak yerine)? → Evet, onay kutusu ile
3. Açıklama ekranında boş hücre açıklamayı silsin mi? → Evet, önizlemede açıkça gösterilir
4. Excel satır sırası sitedeki sırayı belirlesin mi? → Hayır
5. Yeni ürüne "eşlik eden kategori" kuralı uygulansın mı? → Evet

## 14b. Kullanıcı kararları (29 Eylül 2026, onaylandı)
1. Zorunlu başlıklar global: EVET.
2. Serbest Excel sihirbazı kaldırılır: EVET.
3. Excel'de olmayan ürün çöpe gider, ama önce net bir uyarı gösterilir.
   - Önizlemede hangi ürünlerin çöpe gideceği ve ürünün diğer kategorileri görünür.
   - Onay kutusu ve açık bir uyarı metni bulunur.
4. Açıklama ekranında boş hücre açıklamayı temizler. **Ancak ürün sayfası KAPANMAZ.** Açıklaması olmayan ürünün detay sayfası açık kalır; yalnızca açıklama bölümü görünmez.
   - Bugün `nwcs_product_template()` body boşsa 404 veriyor, kart bağlantıları da buna göre olabilir.
   - Uygulayıcı bu davranışı WK ve Koçist'te kontrol edip açıklamasız ürünün sayfasının da çalışmasını sağlar. Ürünün adı, görseli ve teknik detayları gösterilir.
   - Mevcut açıklamasız Koçist ürünlerini etkileyecek bir risk varsa durup bildirir.
5. Excel satır sırası site sırasını belirlemez.
6. Eşlik eden kategori kuralı uygulanır. İleride ayrıca iyileştirilebilir.
7. Paylaşılan kategoriler (WK + Koçist): plandaki gibi önizlemede uyarı verilir. Ayrı kategori açılmaz.
8. **Panel arayüzü öncelikli:** paneli yapan ve kullanacak kimse ortada yokken yoldan geçen biri bile anlayabilmeli. Sade, düz Türkçe, az seçenek. Tüm panel ekranlarında frontend-design becerisi kullanılır.

## 15. Roller

| Adım | Model | Görev |
|---|---|---|
| 1 | Fable 5.1 HIGH | Mimari plan (bu belge) ✔ |
| 2 | Opus 5.5 MEDIUM | Uygulama, adım 1-10. frontend-design: adım 2, 3, 5, 9 |
| 3 | Yeni Fable 5.1 HIGH | Bağımsız inceleme, kod değiştirmez. Bakılacaklar: boş = temizle, çöp dahil eşleşme, silme ve onay, geri alma bütünlüğü, `_bilgi`, paylaşılan kategori, details/spec senkronu, güvenlik, Koçist regresyonu |
| 4 | Opus 5.5 MEDIUM | Düzeltme ve doğrulama |

## 16. Uygulama notları (Opus 5.5, 29 Eylül 2026)

Plandan sapmalar ve nedenleri:
1. **Tohumlamada hiçbir başlık zorunlu yapılmadı.** Zorunluluk global; Koçist ürünlerinin çoğunda detay yok. Beş ortak başlık zorunlu olsaydı Koçist kategorilerinin Excel satırlarının hepsi reddedilirdi. Zorunluluk "Detay başlıkları" ekranından, "… üründe boş" sayısı görülerek verilir.
2. **Sıra (inceleme sonrası, orkestratör kararı b):** sitede her ürün kendi sırasıyla gösterilir; kayıt sırası yalnızca Excel sütunları ve formdaki eksik zorunlu satırların yeri içindir. Tohum çekirdek WK sırasıyla başlar, kalanlar ortalama yerle. Blog 13 ve 8'de eski sıra ile yeni gösterim arasında 0 fark.
3. **Yeni ürün sitelerde de seçilir:** eşlik eden kategori kuralının aynısı sitelere uygulandı (kategorinin bütün ürünlerini gösteren siteler). Önizlemede yazar; geri alma seçimden çıkarır.
4. **Ürün açıklamalarında tablolar korunur:** 182 Koçist açıklaması tablo içeriyor; "karmaşık HTML → işaret" kuralı hepsini kilitlerdi. Tablolar dosyaya girmez, yazılan metnin sonuna aynen eklenir ("EK İÇERİK (bilgi)" sütunu). Alıntı (blockquote) "> " ile taşınır.
5. **Açıklamasız sayfa** yalnızca manifestinde `product_page_always` olan temalarda (WK, Koçist) açılır; kural `nwcs_product_has_page()`. Diğer temalar eski kuralda.
6. Excel onay kutusu varsayılan olarak **işaretsiz** (işaretlenmezse çöpe atma yapılmaz, diğer değişiklikler uygulanır).
7. Açıklamasız üründe föy 3'ten az özellikle de gösterilir (yoksa 1-2 özellik hiç görünmezdi).
8. Hesap `admin/sync.php`, ekran/istekler `admin/import.php` (sihirbaz kodu tamamen silindi).
