# WordPress siteleri, tek içerik paneli — yerel geliştirme ortamı

Tek bir WordPress **Multisite** ağı altında, tasarımları birbirinden bağımsız, aynı
firmaya (Koçist Orman Ürünleri) ait gerçek siteler. Hedef: 10 site, tek panel.

| Ne | Yerel adres |
| --- | --- |
| Koçist / ahsapkasa.com yeniden tasarımı (Tailwind) | http://localhost:8080/ahsapkasa/ |
| — Hakkımızda | http://localhost:8080/ahsapkasa/hakkimizda/ |
| — Hizmetlerimiz | http://localhost:8080/ahsapkasa/hizmetlerimiz/ |
| — İletişim (çalışan teklif formu) | http://localhost:8080/ahsapkasa/iletisim/ |
| Koçist / istanbulpaletci.com yeniden tasarımı (Tailwind) | http://localhost:8080/istanbulpaletci/ |
| — Ürünlerimiz (+ 5 ürün sayfası) | http://localhost:8080/istanbulpaletci/urunlerimiz/ |
| — Blog (WordPress yazıları) | http://localhost:8080/istanbulpaletci/blog/ |
| — İletişim (çalışan teklif formu) | http://localhost:8080/istanbulpaletci/iletisim/ |
| ithalkeresteci.com yeniden tasarımı (kuzen tema) | http://localhost:8080/ithalkeresteci/ |
| — Ürünler (+ 4 ürün sayfası) | http://localhost:8080/ithalkeresteci/urunler/ |
| kavakkeresteci.com yeniden tasarımı (kuzen tema) | http://localhost:8080/kavakkeresteci/ |
| — Ürünler (+ 4 ürün sayfası) | http://localhost:8080/kavakkeresteci/urunler/ |
| Koçist (kocist.com.tr; yapım aşamasında, Emirhan) | http://localhost:8080/kocist/ |
| İstanbul Keresteci (Emirhan) | http://localhost:8080/istanbul-keresteci/ |
| Sanayi Palet (Emirhan) | http://localhost:8080/sanayi-palet/ |
| Ahşap Ambalaj Sanayi (Emirhan) | http://localhost:8080/ahsapambalaj/ |
| Ağ yönetimi (Network Admin) | http://localhost:8080/wp-admin/network/ |
| — İçerik Stüdyosu | http://localhost:8080/wp-admin/network/admin.php?page=nwcs-studio |
| — SEO ve GEO | http://localhost:8080/wp-admin/network/admin.php?page=nwcs-seo |
| Giriş | http://localhost:8080/wp-login.php |

> Bu yerel bir geliştirme ortamıdır. Gerçek `kocist.com.tr` / `istanbulpaletci.com` /
> `ahsapkasa.com` alan adlarına, DNS'e veya canlı sitelere dokunulmaz.
>
> Sitelerdeki fotoğraflar firmanın kendi sitelerinden alınan gerçek fotoğraflardır
> (`resources/`); metinler de o sitelerdeki içeriğin kendisidir. İlk aşamadaki iki
> demo site (`/kocist/`, `/paletci/`) kaldırıldı; kaldırılmadan önceki veritabanının
> yedeği `backups/` klasöründedir (Git'e girmez).

## Gereksinimler

- **Docker Desktop** (Compose v2+) — çalışır durumda
- Git
- Boş portlar: **8080** (WordPress). Veritabanı host'a açılmaz, 3306 çakışması olmaz.

Başka bir şey kurmanız gerekmiyor: PHP, MySQL ve WP-CLI container içinde gelir.
WordPress **7.1.1** kullanılır (MCP için gereken Abilities API, 6.9'dan beri çekirdekte).

## Kurulum (sıfırdan)

```bash
cp .env.example .env          # PowerShell: Copy-Item .env.example .env
# .env içindeki şifreleri kendinize göre değiştirin

docker compose up -d
docker compose --profile cli run --rm --entrypoint sh wpcli /scripts/install.sh
```

`install.sh` tekrar çalıştırılabilir; var olanı bozmaz. Yaptıkları:

1. WordPress'i kurar, ardından **alt dizin tabanlı** Multisite ağına çevirir
2. `network-content-studio` eklentisini ağ genelinde etkinleştirir
3. Türkçe dil paketini kurar
4. Betiğin başındaki `SITES` listesindeki her site için (şu an `ahsapkasa`, `istanbulpaletci`,
   `ithalkeresteci`, `kavakkeresteci`): alt siteyi açar, temasını atar, dilini Türkçe yapar ve kendi
   seed betiğiyle içeriğini kurar. **Var olan bir siteye dokunmaz**: seed alanları
   varsayılana geri yazdığı için yalnızca site ilk kez oluşurken çalışır.

Yönetici kullanıcı adı ve şifresi `.env` dosyasındadır (`WP_ADMIN_USER`,
`WP_ADMIN_PASSWORD`). `.env` sürüm kontrolüne girmez.

## Sık kullanılan komutlar

```bash
# Başlat / durdur
docker compose up -d
docker compose stop

# Claude Code'u MCP ile bağla
bash scripts/mcp-setup.sh

# İmaj etiketi yükseltildiğinde WordPress çekirdeğini eşitle
docker compose exec wordpress sh /scripts/update-core.sh
docker compose --profile cli run --rm wpcli --path=/var/www/html core update-db --network

# İçerik envanterini manifestten yeniden üret
docker compose --profile cli run --rm --entrypoint sh wpcli -c \
  "wp --path=/var/www/html eval-file /scripts/inventory.php --url=http://localhost:8080/istanbulpaletci/ --quiet"

# Herhangi bir WP-CLI komutu (örn. site listesi)
docker compose --profile cli run --rm wpcli --path=/var/www/html site list

# Her şeyi sil ve sıfırdan kur (veritabanı ve WP core dahil)
docker compose down -v
docker compose up -d
docker compose --profile cli run --rm --entrypoint sh wpcli /scripts/install.sh
```

> Git Bash kullanıyorsanız `/scripts/...` yolunun Windows yoluna çevrilmesini
> engellemek için komutların başına `MSYS_NO_PATHCONV=1` ekleyin.

## İçerik Stüdyosu paneli

Giriş yaptıktan sonra **Ağ Yönetimi → İçerik Stüdyosu**:
http://localhost:8080/wp-admin/network/admin.php?page=nwcs-studio

Panelin düzeni:

- **Ortada sitenin çalışan önizlemesi.** Değiştirmek istediğiniz yazıya, görsele veya
  butona doğrudan tıklarsınız; ilgili alan solda açılır ve seçili alan vurgulanır.
  Üstte masaüstü/telefon genişliği arasında geçiş yapılır.
- **Solda sayfa sekmeleri ve bölüm listesi** — Türkçe adlarıyla (Hero, Ürün Kartları,
  Footer…). Bölümün yanındaki **↑ ↓** okları ana sayfa sırasını değiştirir; üst menü ve
  footer "sabit" olarak işaretlidir. Bir bölüme tıklandığında aynı sütun düzenleyiciye
  döner, **← Bölümler** ile geri dönülür.
- **Üst şeritte site seçici** — ağdaki manifestli her site (şu an ahsapkasa, istanbulpaletci,
  ithalkeresteci ve kavakkeresteci). Seçim değiştiğinde panel o sitenin manifestine göre yeniden kurulur.
- **Her sayfanın son bölümü "Arama ve Paylaşım"**: o sayfanın Google başlığı, açıklaması
  ve paylaşım görseli (bkz. [SEO ve GEO](#seo-ve-geo)).

Teknik bilgi gerekmez: manifest, alan anahtarı gibi kavramlar panelde görünmez;
her alan kendi Türkçe adıyla listelenir.

Yapabilecekleriniz:

| İşlem | Nasıl |
| --- | --- |
| Metin / bağlantı düzenleme | Alanı doldurun; bağlantılarda `/kurumsal/`, `#teklif`, `tel:`, `mailto:` kabul edilir |
| Görsel değiştirme | Medya kitaplığından seçin **veya** yeni dosya yükleyin (seçili sitenin medya kitaplığına gider) |
| Görsel kaldırma | Seçim kutusunda "— Görsel yok (kaldır) —" |
| Alt metin | Görselin altındaki alan; WordPress medya kaydına yazılır |
| İkon değiştirme | 16 ikonluk listeden seçim |
| Satır ekleme / silme | Tekrarlı bileşenlerde **+ Satır ekle** / **Sil** |
| Sıra değiştirme (menü dahil) | Satırlardaki **↑ ↓** düğmeleri |
| Yayınlama | **Kaydet ve Yayınla** — kaydettiğiniz anda sitede görünür |
| Vazgeçme | **Vazgeç** — kaydedilmemiş değişiklikleri atıp son kayıtlı hâle döner |
| Bölüm sırası | Bölüm listesindeki **↑ ↓** okları; kaydetme gerekmez, anında yayınlanır |
| Önizleme | Ortadaki canlı önizleme; kaydettikten sonra kendiliğinden yenilenir |

Panel başlığının altındaki uyarı taslak olmadığını açıkça söyler; formda değişiklik
yaptığınızda "kaydedilmedi" rozeti çıkar ve sayfadan ayrılmak isterseniz tarayıcı uyarır.

JavaScript kapalıyken de panel çalışır: bölüm bağlantıları normal sayfa geçişi olur ve
form klasik yolla gönderilir. Bu durumda tıkla-düzenle ve anlık yenileme devre dışı kalır.

## Claude Code ↔ WordPress (MCP)

WordPress 6.9 ile **Abilities API** çekirdeğe girdi; bu demo o API'yi kullanır ve
resmî [WordPress MCP Adapter](https://github.com/WordPress/mcp-adapter) eklentisiyle
MCP'ye açar. Adapter her şeyi kendiliğinden açmaz — hangi yeteneklerin sunulacağı
[mcp-server.php](wp-content/plugins/network-content-studio/includes/mcp-server.php)
içinde tek tek sayılır. Bu demoda açılan dört yetenek:

| Araç | Ne yapar |
| --- | --- |
| `nwcs-list-sites` | Demo sitelerini listeler (okuma) |
| `nwcs-describe-site` | Sitenin sayfa/bileşen/alan yapısını verir (okuma) |
| `nwcs-get-field` | Tek alanın değerini okur |
| `nwcs-update-field` | Tek alanı günceller (yazma) |

Yazma korumaları: `manage_network_options` yetkisi, yalnızca ağdaki manifestli demo
siteleri, yalnızca yerel adresler (`localhost`, `127.0.0.1`, `.test`, `.local`),
yalnızca manifestte tanımlı alanlar, panelle aynı tür bazlı temizleme. Görsel alanları
ve toplu değişiklikler bu yüzeyin dışındadır.

Bağlanmak için:

```bash
bash scripts/mcp-setup.sh
```

Windows'ta Git Bash'ten çalıştırın (proje klasöründe sağ tık → "Open Git Bash here").
Docker Desktop açık olmalı.

Betik MCP Adapter'ı kurar/etkinleştirir, `admin` için bir **uygulama parolası** üretir
(aynı isimli eskiler silinir) ve proje kökünde **`.mcp.json`** dosyasını yazar. Claude Code
bu dosyayı kendiliğinden okur; ayrıca `claude` CLI kurulu olması gerekmez (VS Code
eklentisiyle de çalışır). Dosya kimlik bilgisi taşıdığı için `.gitignore`'dadır; depoda
yalnızca [.mcp.json.example](.mcp.json.example) durur.

Sonra: VS Code penceresini yeniden başlatın → proje MCP sunucusu onayını kabul edin →
`/mcp` ile bağlantıyı doğrulayın.

Terminalde doğal dille:

> Paletçi ana sayfasındaki hero başlığını "Ölçüye özel ahşap palet" yap

Uç nokta: `http://localhost:8080/wp-json/nwcs/v1/mcp` (streamable HTTP; `initialize` →
`Mcp-Session-Id` → `tools/call`).

## Ürün Havuzu

**Ağ Yönetimi → Ürün Havuzu**
http://localhost:8080/wp-admin/network/admin.php?page=nwcs-pool

Ürünler burada **bir kez** girilir; siteler oradan beslenir. Her siteye tek tek ürün
girme ihtiyacı yoktur.

- Ürün alanları: ad, kart açıklaması, fiyat, ölçü/not, kategoriler, **görseller (galeri)**, detay metni.
- **Kategoriler** listeden seçilir; yeni kategori havuz sayfasından eklenir/silinir.
- **Arama, kategori filtresi ve sayfalama** listenin üstünde.
- **Toplu işlem**: birden çok ürünü seçip "şu sitede göster/gizle" ya da "kategoriye ekle".
- **CSV ile toplu giriş**: dışa aktarıp düzenleyin, geri yükleyin. Sütunlar
  `slug, ad, kisa_aciklama, fiyat, olcu_not, kategoriler, gorseller, detay_metni`;
  aynı `slug` varsa ürün güncellenir, yoksa oluşturulur.
- **Görseller** ağ ana sitesinin WordPress medya kitaplığına yüklenir ve oradan silinir.
  Bir ürüne bağlı görsel silinemez; önce ürünün görselini değiştirmeniz gerekir.
- **Fiyat boş bırakılırsa** sitede fiyat yerine **“Teklif al”** görünür.
- Tabloda her ürünün hangi sitelerde göründüğü yazar.

### Hangi site neyi gösterir

İçerik Stüdyosu'nda ürün bölümünü açtığınızda:

| Ayar | Ne yapar |
| --- | --- |
| **Hepsi** | Havuzdaki tüm ürünler görünür; yeni ürün eklenince otomatik çıkar |
| **Seçilenler** | Yalnızca işaretledikleriniz; sırayı ↑↓ ile siz verirsiniz |
| **İstisnalar** | Bu siteye özel ad, açıklama, görsel, fiyat; ya da ürünü bu sitede gizleme |

Şu an ağdaki iki gerçek site ürünlerini kendi manifestlerinde tutuyor; havuzu kullanan
bir site yok. Havuzu kullanacak bir tema, manifestine `products` türünde bir alan
ekler; ancak o zaman site havuzdan ürün gösterebilir. Havuzu örnek ürünlerle doldurmak
için (yalnızca geliştirmede): `wp eval-file /scripts/seed-products.php --url=http://localhost:8080/`.

## Ürün kodu

Her ürünün kendine ait bir kodu vardır. Havuz formundan yazabilir, boş bırakırsanız
`URN-0006` biçiminde kimliğinden üretilir. Aynı kod iki üründe kullanılamaz.

Kod, Excel yüklemesinde eşleştirme anahtarıdır: yüklenen satırın kodu havuzdaki bir ürünle
tutuyorsa o ürün güncellenir, tutmuyorsa yeni ürün eklenir. CSV dışa/içe aktarmada da
`urun_kodu` sütunu olarak yer alır.

## Site bazlı özelleştirme (Özelleştirmeler)

Bir ürün bütün sitelerde havuzdaki hâliyle görünür. Bir site için farklı bir ad, kart
açıklaması, fiyat ya da öne çıkan görsel isteniyorsa, Ürün Havuzu'nda ürünü düzenlerken
**Özelleştirmeler** bölümünden site site girilir. Sayfadan ayrılmadan kaydedilir.

- Boş bıraktığınız alan havuzdaki değeri kullanır.
- "Bu sitede farklı fiyat" işaretlenip boş bırakılırsa o sitede *"Teklif al"* görünür.
- **Özelleştirmeyi kaldır** o sitenin bütün istisnalarını siler, ürün havuzdaki hâline döner.
- Temasında ürün bölümü olmayan siteler burada listelenmez, ayrıca belirtilir.

Ürünün hangi sitede görüneceği burada değil, İçerik Stüdyosu'ndaki ürün bölümünden seçilir.

## Excel'den toplu ürün yükleme

Ürün Havuzu sayfasındaki **"Excel'den ürün yükle"** düğmesi üç adımlı bir pencere açar:

1. **Dosya Seç** — `.xlsx` dosyası seçilir. İlk sayfası okunur, satırlar geçici bir dosyaya
   alınır; havuza bu adımda hiçbir şey yazılmaz.
2. **Eşleştirme** — Başlık satırı sistem alanlarıyla otomatik eşleştirilir ("Stok Kodu" →
   Ürün Kodu, "Price" → Fiyat gibi), yanında dosyanın ilk satırından örnek değer gösterilir.
   Yanlışsa açılır listeden düzeltirsiniz. **Ürün Adı** eşleştirilmeden devam edilemez.
3. **Yükleme** — Satırlar 100'erli parçalar hâlinde işlenir, ilerleme çubuğu ilerler.
   Sonunda kaç ürün eklendiği, kaç ürünün güncellendiği ve atlanan satırların sebebi yazılır.

Eşleştirilebilen alanlar: Ürün Adı (zorunlu), Ürün Kodu, Fiyat, Kısa Açıklama, Ölçü/Not,
Kategoriler, Detay Metni. Eşleştirilmeyen sütunlar yoksayılır.

**Aynı ürün ikinci kez gelirse:** ürün kodundan (yoksa addan) türetilen kısa ad havuzdaki bir
ürünle eşleşirse o ürün güncellenir, yenisi eklenmez. Böylece aynı dosyayı tekrar yüklemek
ürünleri çiftlemez; fiyat listesi güncellemek için aynı dosyayı yeniden yüklemek yeterlidir.

### Yüklemeyi geri alma

Yükleme bitince havuz sayfasının üstünde bir şerit belirir:
**"Son Excel yüklemesi · dosya.xlsx · 342 yeni, 14 güncellenen · 22.09.2026 11:40"** ve yanında
**"Bu yüklemeyi geri al"** düğmesi. Basınca o yüklemede eklenen ürünler silinir, güncellenenler
yükleme öncesi hâline döner.

Bir ürün yüklemeden sonra elle düzenlendiyse geri alma ona dokunmaz ve hangi ürünlere
dokunmadığını size söyler. Yeni bir yükleme yapıldığında şerit yerini yeni yüklemeye bırakır,
yani her an yalnızca son yükleme geri alınabilir.

### Sınırlar

- Dosyanın **ilk sayfası** okunur, ilk satır başlık kabul edilir.
- En fazla **20.000 satır**; üstü okunmaz ve pencerede uyarı çıkar.
- Sunucu yükleme sınırı `docker/php-uploads.ini` ile 32 MB'a çıkarılmıştır (WordPress imajının
  varsayılanı 2 MB'dır ve birkaç bin satırlık dosyaya yetmez).
- Görsel sütunu bu sürümde aktarılmaz; görseller Medya Havuzu'ndan bağlanır.
- `.xls` (eski biçim) ve `.csv` bu pencereden yüklenmez. CSV için eski "CSV ile toplu giriş"
  penceresi yerinde duruyor.

## Medya Havuzu

**Ağ Yönetimi → Medya Havuzu**
http://localhost:8080/wp-admin/network/admin.php?page=nwcs-media

Görsellerin tek yerden yönetildiği sayfa: bir seferde birden fazla dosya yükleyin
(isteğe bağlı ortak alt metinle), arayın, başlık ve alt metni düzenleyin, silin.
Her görselin hangi üründe kullanıldığı kartında yazar; kullanımdaki görsel silinemez.

### Ürün detay sayfası

Bir ürünün **detay sayfası metni** doldurulursa o ürün kendi sayfasını kazanır:
`/<site>/urun/<ürün-adresi>/` — kart da oraya bağlanır. Metin boşsa
ürünün sayfası yoktur (adres 404 verir) ve kart doğrudan teklif bölümüne gider.
Detay sayfası da site istisnalarını uygular.

## Depoda ne var, ne yok

```
docker-compose.yml          WordPress + MariaDB + WP-CLI
docker/apache-wp.conf       .htaccess (mod_rewrite) izni
scripts/install.sh          Tekrarlanabilir kurulum (site listesi betiğin başında)
scripts/seed-<site>.php     Sitenin ilk içeriği (sayfalar, görseller, alan değerleri)
scripts/data/               Seed betiklerinin metin verisi (ör. blog yazıları)
scripts/seed-products.php   Ürün havuzuna örnek ürün (yalnızca geliştirme)
scripts/inventory.php       CONTENT-INVENTORY.md üreteci
build/                      Tailwind kaynakları (tema başına bir dosya)
wp-content/themes/ahsapkasa-theme/
wp-content/themes/istanbulpaletci-theme/
wp-content/themes/kereste-base/           kuzen kereste sitelerinin ana teması
wp-content/themes/ithalkeresteci-theme/   çocuk tema (renk + içerik)
wp-content/themes/kavakkeresteci-theme/   çocuk tema (renk + içerik)
wp-content/plugins/network-content-studio/
backups/                    Veritabanı yedekleri (Git'e girmez)
```

WordPress core Git'e **kopyalanmaz**; `wp_core` adlı Docker volume'unda durur.
Yüklenen medya da volume'dadır (`.gitignore`).

## ahsapkasa yeniden tasarımı (Tailwind)

`ahsapkasa-theme`, ahsapkasa.com'un (Koçist Orman Ürünleri) yeniden tasarımıdır.
Diğer iki temadan farkı, görünümünü elle yazılmış CSS yerine **Tailwind ile derlenmiş**
bir stil dosyasından almasıdır. Panele bağlanma biçimi aynıdır: kök dizinindeki
`content-manifest.php` sayesinde İçerik Stüdyosu bu siteyi de tanır, eklentide
hiçbir değişiklik gerekmez.

Tasarım kararları ve gerekçeleri: [DECISIONS.md](DECISIONS.md)

### CSS'i yeniden derleme

Şablonlarda yeni bir Tailwind sınıfı kullandığınızda CSS'i yeniden üretmek gerekir:

```bash
cd build
npm install          # yalnızca ilk seferde
npm run build        # iki temayı da derler
npm run build:ahsapkasa          # yalnızca bir tema
npm run build:istanbulpaletci
npm run watch:istanbulpaletci    # izleyerek sürekli derleme (tema başına)
```

Her temanın kendi kaynağı vardır (`build/ahsapkasa.css`, `build/istanbulpaletci.css`).
Çıktı ilgili temanın `assets/tailwind.css` dosyasına yazılır ve
depoya dahildir; yani siteyi çalıştırmak için Node kurmanız gerekmez, yalnızca
tasarımı değiştirecekseniz gerekir.

### Üst menüdeki WhatsApp düğmesi

"Teklif alın" düğmesinin yanında bir WhatsApp düğmesi vardır. Bağlantısı İçerik Stüdyosu'ndan
girilir (Tüm Sayfalar → Üst Menü → **WhatsApp Bağlantısı**), örneğin
`https://wa.me/905XXXXXXXXX`.

Bağlantı girilmediği sürece düğme görünür ama **tıklanamaz** (`aria-disabled`, klavye
sırasından çıkarılmış, soluk); kimseyi boş bir adrese götürmez. Bağlantı girilince
kendiliğinden normal bir bağlantıya döner ve yeni sekmede açılır.

### Ürün bazlı teklif düğmesi

Hizmetlerimiz sayfasındaki her kutuda kendi **"Bu ürün için teklif al"** düğmesi vardır.
Düğme iletişim sayfasına `?urun=<ürün adı>#teklif` adresiyle gider; form o ürünü kendiliğinden
seçili getirir ve doğrudan forma kaydırır. Hatalı gönderimden sonra kullanıcının kendi seçimi
adresteki değerin önüne geçer.

### Hero slaytı ve görsel büyütme

Ana sayfadaki hero, İçerik Stüdyosu'ndan yönetilen bir slayt gösterisidir (Ana Sayfa →
Giriş (Hero) → Arka Plan Slaytları). Slayt eklemek/çıkarmak için satır ekleyip görsel seçmek
yeterli; tek slayt kalırsa noktalar kendiliğinden gizlenir.

Hizmetler sayfasında her ürünün dört görsel alanı vardır. Kapak görseline tıklandığında
görsel büyür ve o ürünün diğer görselleri arasında ok tuşlarıyla veya düğmelerle gezilebilir.

Büyütülmüş görselde ayrıca **yakınlaştırma** vardır: fare tekerleği, alttaki −/+ düğmeleri,
görsele tıklama (%100 ↔ %200) ve klavyede `+` / `−` / `0`. Yakınken görsel sürüklenerek
gezilir. Görsel değiştirildiğinde veya pencere kapandığında yakınlaştırma sıfırlanır.

### Teklif formu

İletişim sayfasındaki form sahte bir başarı ekranı göstermez. Gönderim
`admin-post.php` üzerinden doğrulanır (nonce, zorunlu alanlar, bot tuzağı) ve
`Teklif İstekleri` kayıt türüne **özel** olarak yazılır. Gelen istekleri
`http://localhost:8080/ahsapkasa/wp-admin/edit.php?post_type=ak_quote`
adresinden görebilirsiniz. Demoda e-posta gönderimi yoktur; kayıt WordPress
içinde tutulur.

## istanbulpaletci yeniden tasarımı (Tailwind)

`istanbulpaletci-theme`, istanbulpaletci.com'un (yine Koçist Orman Ürünleri) yeniden
tasarımıdır. Canlı sitedeki 10 sayfa korunmuştur: Anasayfa, Hakkımızda, Ürünlerimiz,
beş ürün sayfası (`/urunlerimiz/ahsap-palet/` …), Blog ve İletişim. Eklentide hiçbir
değişiklik gerekmedi; İçerik Stüdyosu siteyi "Koçist · istanbulpaletci.com" adıyla,
11 düzenlenebilir sayfa ve 157 alanla kendiliğinden tanır.

Sıfırdan kurulum (ağ kuruluyken):

```bash
docker compose run --rm wpcli site create --slug=istanbulpaletci --title="İstanbul Paletçi"
docker compose run --rm wpcli theme enable istanbulpaletci-theme --network
docker compose run --rm wpcli theme activate istanbulpaletci-theme --url=http://localhost:8080/istanbulpaletci/
docker compose run --rm wpcli language core install tr_TR
docker compose run --rm wpcli site switch-language tr_TR --url=http://localhost:8080/istanbulpaletci/
docker compose run --rm wpcli eval-file /scripts/seed-istanbulpaletci.php --url=http://localhost:8080/istanbulpaletci/
```

Kurulum betiği tekrar çalıştırılabilir; sayfa, görsel ve yazıları ikinci kez
oluşturmaz, ama alan değerlerini manifest varsayılanlarına **geri yazar**. Site sahibi
panelden içerik girdikten sonra çalıştırmayın.

- **Ürün sayfaları tek şablondur.** Manifestte `'template' => 'product'` işaretli
  sayfalar ürün sayılır; üst menünün açılır paneli, ana sayfa ızgarası, Ürünlerimiz
  listesi, alt bilgi ve form seçenekleri bu listeden beslenir.
- **Blog yazıları** WordPress'in kendi yazılarıdır (site yönetiminde Yazılar). Panel
  yalnızca blog sayfasının başlığını ve düğme metinlerini yönetir.
- **Teklif formu** ana sayfada ve İletişim'de aynıdır; gönderimler
  `http://localhost:8080/istanbulpaletci/wp-admin/edit.php?post_type=ip_quote`
  altında görülür. Ad, ölçü ve e-posta **ya da** telefon zorunludur.
- **Görseller:** ana sayfa slaytları kolaj olduğu için (tek görselde 3–8 fotoğraf)
  kurulum betiği içlerinden tek fotoğraf kırpar. Tema demosundan kalan stok görseller
  ve ahsapkasa ile aynı manzara fotoğrafları kullanılmaz.

## Kuzen kereste siteleri (ithalkeresteci, kavakkeresteci)

İki alan adı bugün yalnızca kocist.com.tr'yi çerçeve içinde gösteriyor. Yeniden
tasarımda her biri **kendi adının karşılığı olan odaklı bir kereste sitesi** oluyor;
ikisi aynı tasarımı paylaşan kuzenler:

```
wp-content/themes/kereste-base/          ana tema: şablonlar, form, stil
wp-content/themes/ithalkeresteci-theme/  çocuk tema: renk (style.css) + içerik (manifest)
wp-content/themes/kavakkeresteci-theme/  çocuk tema: renk (style.css) + içerik (manifest)
```

- Çocuk temanın `style.css` dosyası yalnızca renk değişkenleri tanımlar ("parti
  boyası"): ithal petrol; kavak Koçist logosunun yeşili, kahve şablon
  harfler, koyu ahşap kahvesi alt bilgi. Tanımlanabilen değişkenler: `--site-mark`,
  `--site-mark-deep`, `--site-mark-wash`, `--site-stamp`, `--site-ink`, `--site-stone`.
- Çocuk temanın `content-manifest.php` dosyası ana temanın kurucusunu çağırır ve
  siteye özgü metinleri, dört ürünü ve ana sayfada hangi modüllerin görüneceğini verir.
  Ana sayfa modülleri: ithal → toptan tedarik bandı; kavak → kullanım alanları.
  Yalnızca listedeki modüllerin alanları panelde görünür.
- Üst menüde ve alt bilgide **Koçist logosu** (grup logosu alanı) ve yeşil
  **WhatsApp** düğmesi (üst menü, mobil menü, ürün sayfası, iletişim).
- Seed betikleri kardeş site bağlantılarını (kuzen köprüsü, alt bilgi) yerel ağda
  buradaki kopyalara çevirir; canlıda alan adları manifestteki haliyle kalır.
- Ana sayfanın hero'su tam genişlik paket ucu fotoğrafı ve ipe asılı **paket etiketi**
  (sitenin rozeti ve dört ürünü, her satır ürün sayfasına gider). Hero fotoğrafı yapay
  zekayla üretilmiş örnek görsel; köşesinde "Örnek görsel" notu var, gerçek fotoğraf
  yüklenince not panelden silinir. "Teklif al" her sayfadaki teklif penceresini açar. Teklif istekleri site
  yönetiminde **Teklif İstekleri** altında (`kr_quote`).
- Sayfalar: Anasayfa, Ürünler, 4 ürün, Sık sorulan sorular, Hakkımızda, İletişim.
  Kaynak sitenin 604 adresi yerine 9.
- Fotoğraflar firmanın kendi sitesinden (`resources/kereste/`); ortalarında firmanın
  filigranı var, filigransız asılları gelince panelden değiştirilir.

Kurulum: `install.sh` içindeki `SITES` listesinde; tek başına
`wp eval-file /scripts/seed-ithalkeresteci.php --url=http://localhost:8080/ithalkeresteci/`
(kavak için `seed-kavakkeresteci.php` ve `/kavakkeresteci/`).
Ana tema ayrıca `build/kereste.css` kaynağından derlenir (`npm run build:kereste`).
Ahşap Ambalaj'ın Tailwind kaynağı tema içindedir; `npm run build:ahsapambalaj` derler.

## SEO ve GEO

**Ağ Yönetimi → SEO ve GEO**
http://localhost:8080/wp-admin/network/admin.php?page=nwcs-seo

Ağdaki bütün sitelerin arama motoru (SEO) ve yapay zekâ araması (GEO) ayarları tek
sekmede. Temalar bu konuda hiçbir şey yapmaz; eklenti manifesti olan her siteye
kendiliğinden uygular.

**Ağ özeti** her sitenin durumunu (elle iyileştirilen sayfa sayısı, firma bilgisi
eksikleri, arama motorlarına açık/kapalı) ve **siteler arası tutarlılığı** gösterir:
aynı firmanın sitelerinde resmî unvan, telefon ve adres birebir aynı yazılmalı.

**Site görünümü**: her sayfanın Google sonucundaki hâli (önizleme), altında açılan
düzenleyici (arama başlığı, açıklama, paylaşım görseli; karakter sayacıyla) ve sağda
sitenin firma bilgisi. Aynı "Arama ve Paylaşım" alanları İçerik Stüdyosu'nda her
sayfanın son bölümü olarak da durur.

**Boş bırakılan her şey otomatik dolar**: başlık sayfanın kendi başlığından, açıklama
öne çıkan cümlesinden, görsel sayfanın ana görselinden gelir. Hiçbir şey girilmese de
her sayfa başlık, açıklama ve paylaşım görseliyle yayına çıkar.

Kendiliğinden üretilenler:

| Ne | Nerede |
| --- | --- |
| Belge başlığı (60 karakteri aşarsa site adı eklenmez), meta açıklama | her sayfa |
| Tek `canonical` adres (WordPress'in yazmadığı ana sayfa, blog listesi, arşivler dahil) | her sayfa |
| Paylaşım önizlemesi (Open Graph, X); görseli olmayan sayfada site görseli | her sayfa |
| Yapılandırılmış veri (JSON-LD): firma, web sitesi, sayfa, konum yolu | her sayfa |
| Grup ilişkisi (`parentOrganization`): kardeş siteler aynı grubun parçası | firma bilgisinde grup girilmişse |
| Ürün verisi, şartname satırları dahil (fiyat/puan uydurulmaz) | `seo_source.type = Product` sayfalar |
| Soru-cevap verisi (`FAQPage`) | `seo_source.type = FAQPage` sayfalar |
| Blog yazısı verisi | yazılar |
| `/llms.txt`: yapay zekâ için sitenin özeti, ürün ölçüleri ve sık sorulanlar dahil | her site |
| Arşivlerde (yazar, tarih, arama, kategori, etiket) `noindex` | her site |
| Site haritasından kullanıcı listesi ve kategori/etiket arşivlerinin çıkarılması | her site |

Tema tarafında isteğe bağlı iki ipucu (`content-manifest.php`):

```php
'seo_site_defaults' => array( 'name' => '…', 'phone' => '…', … ), // firma bilgisinin ilk değerleri
// sayfa başına:
'seo_source' => array(
	'title'       => 'card.name',     // otomatik başlık hangi alandan
	'description' => 'detail.lead',
	'image'       => 'card.image',
	'type'        => 'Product',       // ürün verisi üret
	'properties'  => 'specs.rows',    // şartname satırları (label/value)
),
// soru-cevap sayfası:
'seo_source' => array( 'type' => 'FAQPage', 'questions' => 'items.rows' ), // question/answer satırları
```

Manifestte olmayan ama temanın çizdiği sayfalar (tek şablonla çizilen ürün alt
sayfaları gibi) ve görselleri tema klasöründe tutan temalar için süzgeçler:

```php
add_filter( 'nwcs_seo_extra_pages', fn( $pages ) => … );   // url, name, description, image, type, properties
add_filter( 'nwcs_seo_default_image', fn() => nwcs_seo_theme_file_image( 'assets/img/hero.jpg', 'Açıklama' ) );
add_filter( 'nwcs_seo_default_logo', fn() => nwcs_seo_theme_file_image( 'assets/img/logo.png', 'Firma' ) );
```

**Kardeş siteler `sameAs` değildir.** `sameAs` "aynı kurum" demektir; firma bilgisindeki
"Firmanın diğer adresleri" yalnızca o firmanın kendi hesapları (sosyal medya, Google
İşletme) içindir. Siteler arasındaki bağ "Bağlı olduğu grup" alanıyla kurulur.

`seo` bileşen adı ve `site_seo` sayfa adı eklentiye ayrılmıştır; temalar kullanmaz.
Kararlar ve kontrol listesi: [DECISIONS.md](DECISIONS.md) → "SEO ve GEO — kalıcı ilke".

### Uyumluluk puanı

SEO ve GEO sekmesi her site için 0–100 puan gösterir (SEO ve GEO alt puanlarıyla).
Ağ özetinde sütun, site görünümünde kontrol listesi: eksik olan, kaç puan kaybettirdiği
ve nasıl düzeltileceği. 85 ve üstü "Güçlü", 60–84 "Orta", altı "Zayıf".

## Natro'ya taşıma paketi

```bash
DOMAIN=panel.kocist.com.tr ADMIN_EMAIL=ad@firma.com sh scripts/export-natro.sh
```

Yerel veritabanına dokunmaz; geçici bir kopya üzerinde adresleri `https://DOMAIN`'e
çevirir (serileştirilmiş veri korunur, yazıların `guid` sütunu bilerek değişmez), 8
sitenin hepsini **arama motorlarına kapatır** (deneme), MCP uygulama parolalarını siler
ve eklentisini kapatır, yönetici parolasını yeniler (ekrana yazılmaz; paketteki
`YONETICI-PAROLASI.txt`). Çıktı `dist/natro-<tarih>/` (Git'e girmez): `natro.sql`,
`wp-content.tar.gz` (temalar ve eklenti Git'teki sürümden), `wp-config-ek.php`,
`htaccess.txt`, `KURULUM.md` (cPanel adımları) ve dokunulmamış `yerel-yedek.sql`.

Deneme bitince site site: alan adı bağlanır, DNS çevrilir, eski adres yönlendirmeleri
test edilir, en son o sitenin "arama motorlarına açık" ayarı açılır.

### Canlıya çıkan siteler (domain mapping)

```bash
DOMAIN=panel.sanayipalet.com SCHEME=http MAP="istanbul-keresteci=istanbulkeresteci.com istanbulpaletci=istanbulpaletci.com" sh scripts/export-natro.sh
```

`MAP`'teki siteler pakette kendi alan adına bağlanır: adresleri (diğer sitelerden verilen
bağlantılar dahil) `https://alanadi` olur, arama motorlarına açılır; diğerleri panel altında
kapalı kalır. Alan adının SSL sertifikası sunucuda kurulu olmalı (panelin sertifikası
bağlanan alan adlarını kapsamaz). Geçiş, alan adının belge kökünün WordPress klasörüne
çevrilmesiyle olur; geri dönüş eski klasör yolunu geri yazmaktır.

### Kod güncellemesi: cPanel Git Version Control

Kök dizindeki `.cpanel.yml` yalnızca 9 temayı ve eklentiyi WordPress'in `wp-content`
klasörüne kopyalar (`rsync --delete`, yalnızca bu klasörlerde). İçerik (metin, görsel,
ayarlar) veritabanındadır ve panelden yönetilir; Git ile taşınmaz.

1. cPanel → Git Version Control → Create: "Clone a Repository" açık, adres
   `https://github.com/bugraberatkok/demo-wordpress-multisite.git`, depo yolu
   `/home/u7198936/repositories/demo-wordpress-multisite` (**web'e açık bir klasör değil**).
2. Güncellemede: Manage → Pull or Deploy → **Update from Remote**, sonra **Deploy HEAD Commit**.
   Dağıtım elle tetiklenir; main'e gelen her commit kendiliğinden canlıya çıkmaz.

## Eski adres yönlendirmeleri (301)

**Ağ Yönetimi → SEO ve GEO → Yönlendirmeler.** Her sitenin eski adres listesi: eski
sitenin Google'daki ve başka sitelerdeki adresleri yeni sayfalara taşınır (301) ya da
kaldırıldığı bildirilir (410). Kural yalnızca adres sitede bulunamadığında çalışır;
var olan sayfayı etkilemez. Yollar sitenin köküne göredir, alan adı değişince de geçerli.

```
/urunler/ahsap-palet/    /urunler/#paletler     301
/tag/*                   /blog/                 önekle eşleşen bütün adresler
/portfolio-item/*        410                    eski temanın demo sayfaları
```

İlk listeler canlı alan adları taranarak çıkarıldı (`scripts/data/redirects.php`) ve
`wp eval-file /scripts/load-redirects.php` ile yüklenir (`--dry-run` önce gösterir).
**Bir alan adının DNS'i yeni sunucuya çevrilmeden önce o sitenin listesi hazır
olmalı.** Koçist (kocist.com.tr, ~600 eski adres) listesi kendi geçişinden önce eklenecek.

## Formlar ve e-posta

Bütün sitelerin teklif/iletişim formları gönderimi WordPress'e kaydeder (site
yönetiminde "Teklif İstekleri" ya da "İletişim Mesajları"). Eklenti her yeni kayıtta
sitenin firma e-postasına (SEO ve GEO → Firma bilgisi → E-posta; boşsa yönetici
e-postası) bildirim gönderir; müşterinin e-postası "Yanıtla" adresindedir. Alıcı
`nwcs_form_recipient` süzgeciyle değiştirilebilir.

Yerelde posta sunucusu olmadığı için e-posta gitmez (günlüğe "E-posta gonderilemedi"
yazılır; form yine çalışır). Canlıda (Natro cPanel):

1. Her alan adı için bir posta kutusu açın (ör. `form@alanadi` ya da `info@alanadi`).
2. WP Mail SMTP (ya da FluentSMTP) kurun; SMTP ile bu kutudan gönderin (465 SSL /
   587 TLS). PHP `mail()`'e bırakmayın: `wordpress@alanadi` göndericisi çoğu zaman
   spama düşer.
3. cPanel → Email Deliverability: her alan adında SPF ve DKIM'i onarın, DMARC kaydı
   ekleyin (başta `p=none`). DNS Natro'da değilse kayıtları DNS sağlayıcısına girin.
4. Alıcı adreslerini firma ile teyit edin (SEO sekmesindeki e-postalar).
5. Canlıda her siteden bir deneme formu gönderin.

Dikkat: sayfa önbelleği (LiteSpeed vb.) açılırsa form içeren sayfalar (iletişim,
kereste sitelerinde her sayfa, istanbulpaletci ana sayfa) önbellek dışında tutulmalı;
yoksa formun güvenlik belirteci süresi dolar.

## Yeni site ekleme

Tema klasörünü yüklemek **tek başına yetmez**: tema, sitenin tasarımı ve düzenlenebilir
alanlarının listesidir (`content-manifest.php`); sitenin kendisi ayrıca açılır.

1. Tema klasörünü `wp-content/themes/` altına koyun (hostingde FTP/cPanel ile;
   yerelde ayrıca `docker-compose.yml`'e bir bağlama satırı).
2. Ağ Yönetimi → Siteler → **Yeni site**: adres ve başlık.
3. Ağ Yönetimi → Temalar → temayı **Ağda etkinleştir**; sitenin Görünüm menüsünden
   temayı etkinleştirin.
4. Manifestteki sayfa adresleri için WordPress sayfalarını açın (ör. `/hakkimizda/`).
5. İçerik Stüdyosu ve SEO ve GEO sekmesi siteyi kendiliğinden tanır; içerik oradan girilir.

Yerelde bu adımların hepsini `scripts/install.sh` içindeki `SITES` listesine bir satır
ve bir `seed-<site>.php` ekleyerek yaparsınız.

**Silme**: önce Ağ Yönetimi → Siteler'den siteyi silin, sonra tema klasörünü kaldırın.
Tersi sırada yapılırsa site temasız kalır ve boş sayfa gösterir.

## Mimari özet

- **Alan manifesti**: her tema kökünde `content-manifest.php` — sayfa → bileşen → alan
  (Türkçe etiket + tür). Saf PHP dizisi döndürür, WordPress'e bağımlı değildir; böylece
  ağ paneli `switch_to_blog()` ile tema kodu yüklemeye güvenmeden, dosya sisteminden
  okuyabilir.
- **Veri katmanı**: değerler ilgili alt sitenin kendi `nwcs_content` option kaydında
  tutulur. Temalarda gömülü metin yoktur; her şey bu kayıttan okunur ve sunucu yeniden
  başlatılınca da korunur.
- **Medya**: örnek görseller gerçek WordPress medya kayıtlarıdır (alt metin dahil),
  site bazında `uploads/sites/<id>/` altında durur.
- **İkonlar**: eklenti içinde sabit, 16 ikonluk güvenli liste. Panelden serbest
  SVG/HTML girilemez.
- **Bölüm sırası**: ana sayfa bölümleri `nwcs_section_order` kaydında tutulur; manifestte
  "sıralanabilir" işaretli bölümler dışına çıkılamaz. Sıralama arayüzü MVP 3'te gelecek.

Düzenlenebilir alanların tam listesi: [CONTENT-INVENTORY.md](CONTENT-INVENTORY.md)
Teknik kararlar ve gerekçeleri: [DECISIONS.md](DECISIONS.md)

## Durum

- **MVP 1 — tamamlandı**: Multisite ağı, iki bağımsız tema, demo içerik, örnek alt sayfalar.
- **MVP 2 — tamamlandı**: Network Admin içinde İçerik Stüdyosu; sağda dikey site seçici,
  sayfa/bileşen listesi, tüm metin/görsel/ikon/bağlantı alanlarının düzenlenmesi, satır
  ekle-sil-sırala, hemen yayınla + vazgeç.
- **MVP 3 — tamamlandı**: panel ortasında canlı önizleme ve tıkla-düzenle, ana sayfa
  bölümlerini ↑↓ ile sıralama, Abilities API + resmî MCP Adapter ile Claude Code
  bağlantısı (dört yetenek, en az yetkili).
- **MVP 5 — tamamlandı**: kategori listesi, çoklu ürün görseli, Medya Havuzu sayfası,
  önizlemede sayfa algısı düzeltmesi, katlanan yönetim menüsü, gözü yormayan arayüz;
  ayrıca CSV içe/dışa aktarma, arama-filtre-sayfalama, toplu işlemler ve önbellek.
- **MVP 4 — tamamlandı**: merkezî ürün havuzu (Ağ Yönetimi'nde kendi sayfası, WordPress
  medya kitaplığı, hepsi/seçilenler kipleri, site bazlı istisnalar, boş fiyatta
  "Teklif al", örnek ürün detay sayfası) ve panel arayüzünün elden geçirilmesi
  (site seçici üst şeritte, büyük canlı önizleme, renklendirilmiş düzen,
  katlanabilir ikon seçici).

Bu demonun kapsamı dışında bırakılanlar (canlıya geçişte ayrı iş): taslak/revizyon akışı,
gerçek e-posta teslimi ve spam koruması, alan adı eşlemesi, e-ticaret.

- **ahsapkasa yeniden tasarımı — tamamlandı**: ahsapkasa.com'un dört sayfalık yeniden
  tasarımı (ana sayfa, hakkımızda, hizmetlerimiz, iletişim), Tailwind ile derlenen
  stil, animasyonlu üst menü, dörtlü ürün karesi ve ortasındaki tek teklif düğmesi,
  çalışan teklif formu. Ağdaki üçüncü site olarak İçerik Stüdyosu'ndan yönetilir.
- **istanbulpaletci yeniden tasarımı — tamamlandı**: istanbulpaletci.com'un 10 sayfası
  (ana sayfa, hakkımızda, ürünlerimiz, beş ürün sayfası, blog, iletişim), şartname
  tabloları, görselli açılır ürün menüsü, çalışan teklif formu, Türkçe dil paketi.
- **Demo sitelerin kaldırılması**: `/kocist/` ve `/paletci/` siteleri, temaları ve demo
  seed betikleri kaldırıldı; kurulum betiği gerçek siteleri kurar. Öncesinin yedeği
  `backups/` altında.
- **SEO ve GEO — tamamlandı**: ağ yönetiminde yeni sekme; bütün sitelere otomatik meta,
  paylaşım önizlemesi, yapılandırılmış veri, `llms.txt`; siteler arası firma bilgisi
  tutarlılığı denetimi.
- **ithalkeresteci yeniden tasarımı — tamamlandı**: iframe'le kocist.com.tr'yi gösteren alan
  adı için odaklı kereste sitesi; ortak ana tema (`kereste-base`) + çocuk tema, paket
  etiketi ürün kartları, teklif penceresi. m³ hesaplayıcısı sonradan kaldırıldı; hero
  yeniden tasarlandı (DECISIONS.md).
- **kavakkeresteci yeniden tasarımı — tamamlandı**: aynı ana tema, yeşil-kahve parti
  boyası; kavak kereste, çıta, ahşap takoz, OSB levha; ana sayfada "kullanım alanları".
  İki siteye Koçist logosu ve WhatsApp düğmesi eklendi. Ürün listesi, ölçüler, adres ve
  fotoğraflar satıcının geri bildirimini bekliyor.
