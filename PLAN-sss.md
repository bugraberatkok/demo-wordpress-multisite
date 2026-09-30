# SSS (FAQ) mimarisi + toplu silme incelemesi — Plan

Fable 5.1 (mimar), 30 Eylül 2026. Onay bekliyor; kod değişmedi.

## 1. Bulgular

### Eklenti
FAQ altyapısı hazır:
- `seo.php:863-881`: FAQPage JSON-LD yalnızca cevaplı satır varsa yazılır.
- `nwcs_seo_faq()` (`:1024-1045`)
- llms.txt (`:1355-1372`)
- SEO paneli GEO puanı: "Sık sorulan sorular" 5 puan (`admin/seo.php:369-388`)

### Siteler

| Site | Durum |
|---|---|
| WOOD KOCIST | ✅ `/sss/`, 26 soru, 7 grup |
| İthal / Kavak | ✅ `/sik-sorulan-sorular/`, 5'er soru |
| Koçist | ⚠️ 5 soruluk `product.faq` 283 ürün sayfasında aynen basılıyor (`content-manifest.php:1336-1372`, `product-detail.php:404`, `page.php:56`). Cevaplar kümese göre yazılmış. FAQPage işareti yok. |
| İstanbul Paletçi, İstanbul Keresteci, Sanayi Palet, Ahşap Ambalaj, Ahşap Kasa, Palet Çivi | ❌ SSS yok |

### Ek bulgular
- **Teslimat metni:** Koçist `delivery_text` (`:1264`) "kendi araçlarımızla" iddiasını 283 ürünün akordeonunda da tekrarlıyor.
- **"Aynı gün" / garanti ifadeleri:**
  - Koçist: `:527`, `:529` (footer şeridi), `:802`, `:815`, `:1399`.
  - **İstanbul Keresteci (canlı):** `:393`, `:531`, `:539` (form başarı mesajı dahil).
  - Kaynak doğrulanamadı.

### Toplu silme (peer "8a") incelemesi
- **Doğru bulunanlar:**
  - yetki ve nonce kontrolleri;
  - çöp kutusu kapalıysa silme yapılmıyor;
  - yalnızca çöp durumundaki ürünler işleniyor;
  - medya kullanım kontrolü çöpteki ürünleri de sayıyor;
  - kullanımdaki görseller sunucuda da atlanıyor.
- **Peer'e iletilecek notlar:**
  1. **Toplu kalıcı silmede zaman aşımı riski:** her ürün için 10 site geçişi ve 2 option yazımı yapılıyor. Çözüm ya site başına tek geçiş ya da istek başına 50 ürün sınırı.
  2. `bulk_trashed` bildirimi gerçekte çöpe atılan ürün sayısını göstermeli.
  3. Çöpe atma sonrası `ara` / `kategori` süzgeci korunmalı.
  4. **Kalanlar:** tarayıcı kontrolü, README satırları, sürüm 0.22.2, bağımsız inceleme, commit.
- **Dosya sahipliği:** `pool.php`, `media.php`, `admin.css` ve `check-bulk-delete.php` peer'de. SSS işi bu dosyalara dokunmaz.

## 2. Mimari (10 sitede tek model)

### Manifest sayfası
- Anahtar: `faq`.
- Adres: `/sss/`. Kereste siteleri `/sik-sorulan-sorular/` adresinde kalır.
- `seo_source` FAQPage.
- Bileşenler:
  - `head` (başlık + giriş);
  - `items.rows` tekrarlayıcı: `group`, `question`, `answer`;
  - `more` (kapanış satırı).

### Ortak yardımcı
- Eklentide `nwcs_faq_rows($page_key)`. Yalnızca soru ve cevabı dolu satırları döndürür, satırın panel sırası (`_row`) da içinde.
- Panel ipucu: **cevabı boş soru sitede ve arama verisinde görünmez.**

### Çizim
- Yapı: `h1` + giriş → grup başlığı (`h2`) → `<details>` / `<summary>` soru listesi → kapanış satırı (sitenin kendi telefonu).
- JavaScript yok. Koçist'in mevcut `.k-faq` akordeonu taşınır.

### Bağlantılar
- Menü ve footer'a "Sık sorulanlar".
- Canlıda menü panelden kaydedilmişse yeni satırı panelden eklemek gerekir.
- Koçist ürün sayfasında SSS bloğu yerine tek bağlantı: "Kereste ürünleriyle ilgili sık sorulan sorular" → `/sss/#sss-kereste`.

### İçerik politikası
- Cevap yalnızca sitenin mevcut, firmadan gelmiş metninden türetilebiliyorsa doldurulur.
- Diğer sorular cevapsız kalır ve görünmez. Süre, rakam ve vaat yalnızca firmadan gelir.
- Sayfa en az 4 cevaplı satırla açılır. Ahşap Ambalaj ve Ahşap Kasa ertelenir.

### Tasarım
- Her sitenin kendi jetonları kullanılır.
- Soru listesi tipografik bir nesne gibi durur: kart, numara, büyük harf etiket yok; satırlar ince çizgiyle ayrılır; açma kapama "+ / −".
- Cevap satırı en fazla 70ch.
- 390px'de yatay taşma yok.

## 3. Koçist — hemen (öncelik 1)
1. `product-detail.php:404` ve `page.php:56`: ürün SSS bölümü kaldırılır.
2. `product.faq` silinir; yerine `faq` sayfası gelir. Gruplar: Genel, Kereste, Ambalaj, Dekorasyon, Hırdavat.
3. `sections/product-faq.php` → `sections/faq.php` (`nwcs_faq_rows`). `page.php`'ye `sss` dalı eklenir.
4. `KOCIST_PAGES_VERSION` 5 olur; sayfa listesine `sss` eklenir.
5. Menü `menu_kurumsal` ve footer'a `/sss/`; ürün sayfasına grup bağlantısı.
6. Cevaplar yalnızca mevcut metinlerden: ISPM-15, havale/EFT, adres, "ölçü ve adede göre", `price_reason`. Kümese özgü 5 cevap Dekorasyon grubunda cevapsız bekler (firma onayı).
7. **"Aynı gün" metinleri:**
   - **Seçenek A (varsayılan):** nötr ifade.
     - "AYNI GÜN FİYAT" → "YAZILI FİYAT TEKLİFİ"
     - "TOPTAN FİYAT GARANTİSİ" → "TOPTAN SATIŞ"
     - `:802`, `:815`, `:1399` → "…ölçü ve adedi yazın, yazılı teklif gönderelim"
     - `delivery_text` → "Sevkiyat şekli ve süresi teklifle birlikte yazılı olarak bildirilir."
   - **Seçenek B:** dokunma; canlıya almadan önce firma cevabı şart.
   - **Seçenek C:** firma onaylarsa aynen kalsın.
   - **İstanbul Keresteci (canlı)** `:393`, `:531`, `:539` için aynı seçenekler.

## 4. Soru taslakları
K = mevcut metinden cevap var, F = firma cevabı gerekli.

### Koçist
- **Genel:**
  - Fiyat teklifi nasıl alırım? (K)
  - Ödeme nasıl yapılır? (K: havale/EFT, IBAN telefonla doğrulanır)
  - Özel ölçü üretim yapıyor musunuz? (K)
  - Tesisiniz nerede, ziyaret edebilir miyim? (K)
  - Sevkiyat süresi nedir? (F)
  - Teslimat hangi illere, nasıl? (F)
  - Minimum sipariş var mı? (F)
  - Toptan fiyat uygulanıyor mu? (F)
- **Kereste:**
  - Hangi kereste ve levha türleri var? (K)
  - Metreküp nasıl hesaplanır? (K)
  - İstediğim ölçüde kesim yapılıyor mu? (F)
  - Emprenyeli / fırınlanmış kereste var mı? (F)
  - Levha ölçüleri nelerdir? (F)
- **Ambalaj:**
  - İhracat için ısıl işlem (ISPM 15) yapıyor musunuz? (K)
  - Sandık ve kafes ölçüye göre yapılıyor mu? (K)
  - Euro palet ölçüsü ve yük kapasitesi? (F)
  - İkinci el palet alıyor / satıyor musunuz? (F)
  - Palet damgası teklifte belirtilir mi? (F)
- **Dekorasyon:** montaj, dayanıklılık/bakım, özel ölçü, sevkiyat süresi, teslimat (hepsi F).
- **Hırdavat:**
  - Çivi / zımba teli hangi tabancalara uyar? (F)
  - Katalog var mı? (K)
  - Koli / paket adetleri? (F)
  - Yalnızca toptan mı? (F)

### İstanbul Paletçi (canlı)
- Hangi ölçülerde palet üretiyorsunuz? (K)
- Euro palet ile standart palet farkı? (K)
- İhracat için damgalı palet var mı? (K)
- İkinci el palet satıyor musunuz? (K)
- Teklif için ne göndermeliyim? (K)
- Teslimat / nakliye nasıl? (F)
- Sevkiyat süresi? (F)
- Minimum adet? (F)

### İstanbul Keresteci (canlı)
- Hangi ürünleri satıyorsunuz? (K)
- Yumuşak ve sert kereste farkı? (K)
- OSB ile kontrplak farkı? (K)
- Plywood nerede kullanılır? (K)
- Özel ölçü çıta / kalas hazırlanır mı? (K)
- Tomruk hangi ağaçlardan? (K)
- Fiyat neye göre belirlenir? (K)
- Sevkiyat süresi ve teslimat? (F)

### Palet Çivi
- Rulo, tele dizili ve dökme çivi farkı? (K)
- Tabancama hangi çivi uyar? (K)
- Çivi boyu tahta kalınlığına göre nasıl seçilir? (K kısmi / F)
- Fiyat ve stok nasıl öğrenilir? (K)
- Koli / kutu adetleri? (F)
- Minimum sipariş / toptan? (F)
- Teslimat? (F)

### Sanayi Palet (canlı)
- ISPM 15 ısıl işlem yapıyor musunuz? (K)
- Hangi ölçülerde üretiyorsunuz? (K)
- Teklif için ne göndermeliyim? (K)
- Sipariş nasıl ilerler? (K)
- Teslimat nasıl? (K)
- Sandık mı kafes mi? (K)
- Üretim / teslim süresi? (F)
- Ödeme? (F)

### Ertelenenler
Ahşap Ambalaj ve Ahşap Kasa.

Bu liste aynı zamanda firmaya gidecek "cevap bekleyen sorular" listesi; DEVAM.md'ye site site eklenecek.

## 5. Dosyalar, adımlar, doğrulama

### Dosyalar
- **Eklenti:** `includes/seo.php` (yalnızca `nwcs_faq_rows`) + sürüm. Sürüm artışı peer commit'inden sonra.
- **Koçist:** manifest, functions, page.php, product-detail.php, sections/faq.php.
- **İstanbul Paletçi:** manifest, page-sss.php, functions (sayfa oluşturma), gerekirse Tailwind derlemesi.
- **İstanbul Keresteci, Palet Çivi, Sanayi Palet:** manifest, page-sss.php, inc/setup.php (sayfa listesi + sürüm).
- **Belgeler:** README, DECISIONS, DEVAM.
- **Dokunulmayanlar:** peer'in 4 dosyası, WK ve kereste temaları, Koçist ana sayfa.

### Adımlar
1. Eklenti yardımcısı
2. Koçist (+ "aynı gün" kararı)
3. İstanbul Paletçi
4. İstanbul Keresteci (+ "aynı gün")
5. Palet Çivi
6. Sanayi Palet
7. Belgeler, DEVAM'a firma listesi

### Doğrulama
- `/sss/` 200 döner. JSON-LD `["WebPage","FAQPage"]` içerir ve her soruda cevap dolu. Cevapsız soru ne HTML'de ne JSON-LD'de.
- Tüm sorular cevapsızken FAQPage yok.
- llms.txt'de SSS bölümü var.
- GEO puanı 5.
- Panelde satır ekle / sil / sırala çalışıyor; önizlemede tıklayınca doğru satır açılıyor.
- 390px'de yatay taşma yok.
- Koçist'te "aynı gün" geçmiyor (seçenek A ise) ve ürün sayfasında SSS bölümü yok.
- Uydurma rakam / vaat taraması.
- Peer dosyalarına dokunulmamış.

### Riskler
- Canlı menüler panelden kaydedilmişse yeni satırlar gelmez.
- `/sss/` sayfası panele ilk girişte oluşur.
- LiteSpeed ve Cloudflare temizliği gerekir.
- Koçist'te panelde kayıtlı `product.faq` içeriği varsa kaybolur.
- Tailwind derlemesi unutulabilir.
- README ve DEVAM'da peer ile çakışma olabilir.

## 6. Açık sorular (varsayılan)
1. "Aynı gün" / "garanti" metinleri (Koçist 6 yer, İstanbul Keresteci 3 yer) → A, nötrle.
2. Koçist SSS adresi → `/sss/`.
3. Ürün sayfasında grup bağlantısı → evet.
4. Sanayi Palet dahil → evet. Ahşap Ambalaj / Kasa → ertele.
5. Ana sayfaya seçme sorular → hayır.
6. Dekorasyon grubundaki eski kümes cevapları → firma onayına kadar boş.

## 6b. Kullanıcı kararları (30 Eylül 2026) — bağlayıcı
1. "Aynı gün" ve garanti metinleri **A: nötrle**. Koçist'te 6 yer, İstanbul Keresteci'de 3 yer.
2. Koçist `/sss/` açılır, ürün sayfasına grup bağlantısı konur: EVET.
3. **Sanayi Palet dahil.** Kullanıcı SSS'yi GEO açısından önemli buluyor, müşteri de bu konuya özen gösteriyor. Bu yüzden **Ahşap Ambalaj ve Ahşap Kasa da kapsama alınır**: sayfa ve sorular kurulur.
   - Cevaplar yalnızca mevcut metinlerden doldurulur.
   - Cevapsız sorular görünmez. Cevaplı soru sayısı 4'ün altında kalan sitelerde sayfa yine oluşur. Menü bağlantısı yalnızca cevaplı soru varsa gösterilir.
   - Bu siteler için hangi cevapların firmadan beklendiği DEVAM.md'ye yazılır.
4. Ana sayfaya seçme sorular: HAYIR.
5. **Koordinasyon:**
   - Peer 8a şunlara sahip: `network-content-studio.php` (sürüm satırları), README'deki toplu işlem / çöp kutusu / Medya bölümleri, `pool.php`, `media.php`, `admin.css`, `check-bulk-delete.php`.
   - SSS işi bunlara dokunmaz. **Eklenti sürümü SSS uygulamasında artırılmaz**; commit zamanında sırayla yapılır.

## 7. Koordinasyon ve roller
- **Peer (8a):** kendi 4 dosyası, README toplu-işlem satırları, sürüm 0.22.2 ve DEVAM notu; kendi commit'inde, önce.
- **SSS işi:** kendi dosyaları. README ve DEVAM'a yalnızca satır ekleme; commit peer'den sonra.
- **Roller:** Fable 5.1 HIGH (mimar) → Opus 5.5 MEDIUM (uygulama, frontend-design) → yeni Fable 5.1 HIGH (inceleme) → Opus 5.5 MEDIUM (düzeltme).
