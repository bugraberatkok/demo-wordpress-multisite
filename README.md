# WordPress siteleri, tek içerik paneli — yerel demo

Tek bir WordPress **Multisite** ağı altında, tasarımları birbirinden bağımsız üç demo sitesi:

| Ne | Yerel adres |
| --- | --- |
| Koçist (kurumsal / endüstriyel tema) | http://localhost:8080/kocist/ |
| — örnek alt sayfa | http://localhost:8080/kocist/kurumsal/ |
| İstanbul Paletçi (ürün / teklif odaklı tema) | http://localhost:8080/paletci/ |
| — örnek alt sayfa | http://localhost:8080/paletci/urunlerimiz/ |
| Koçist / ahsapkasa.com yeniden tasarımı (Tailwind) | http://localhost:8080/ahsapkasa/ |
| — Hakkımızda | http://localhost:8080/ahsapkasa/hakkimizda/ |
| — Hizmetlerimiz | http://localhost:8080/ahsapkasa/hizmetlerimiz/ |
| — İletişim (çalışan teklif formu) | http://localhost:8080/ahsapkasa/iletisim/ |
| Ağ yönetimi (Network Admin) | http://localhost:8080/wp-admin/network/ |
| Giriş | http://localhost:8080/wp-login.php |

> Bu tamamen yerel bir demodur. Gerçek `kocist.com.tr` / `istanbulpaletci.com` /
> `ahsapkasa.com` alan adlarına, DNS'e veya canlı sitelere dokunulmaz.
>
> Koçist ve Paletçi sitelerindeki görseller "ÖRNEK GÖRSEL" yazılı yer tutuculardır.
> `ahsapkasa` sitesi bir yeniden tasarım çalışması olduğu için oradaki fotoğraflar
> firmanın kendi sitesinden alınan gerçek üretim fotoğraflarıdır (`resources/`);
> metinler de ahsapkasa.com'daki içeriğin kendisidir.

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
2. `/kocist/` ve `/paletci/` alt sitelerini oluşturur
3. İki temayı ağ genelinde etkinleştirip her siteye doğru temayı atar
4. `network-content-studio` eklentisini ağ genelinde etkinleştirir
5. Her iki site için demo içeriğini ve örnek görselleri üretir

Yönetici kullanıcı adı ve şifresi `.env` dosyasındadır (`WP_ADMIN_USER`,
`WP_ADMIN_PASSWORD`). `.env` sürüm kontrolüne girmez.

## Sık kullanılan komutlar

```bash
# Başlat / durdur
docker compose up -d
docker compose stop

# Demo içeriğini yeniden üret (kurulumu bozmadan)
docker compose --profile cli run --rm --entrypoint sh wpcli /scripts/seed-only.sh

# Claude Code'u MCP ile bağla
bash scripts/mcp-setup.sh

# İmaj etiketi yükseltildiğinde WordPress çekirdeğini eşitle
docker compose exec wordpress sh /scripts/update-core.sh
docker compose --profile cli run --rm wpcli --path=/var/www/html core update-db --network

# İçerik envanterini manifestten yeniden üret
docker compose --profile cli run --rm --entrypoint sh wpcli -c \
  "wp --path=/var/www/html eval-file /scripts/inventory.php --url=http://localhost:8080/kocist/ --quiet"

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
- **Üst şeritte site seçici** — Koçist / İstanbul Paletçi. Seçim değiştiğinde panel o
  sitenin manifestine göre yeniden kurulur.

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

Demoda: **Koçist** = Hepsi (8 ürün), **İstanbul Paletçi** = Seçilenler (5 palet ürünü),
iki istisna örneğiyle — Euro Palet bu sitede “Euro Palet (ihracat)” adıyla görünür ve
İkinci El Palet'in fiyatı bu sitede boş bırakıldığı için “Teklif al” yazar.

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
http://localhost:8080/paletci/urun/euro-palet/ — kart da oraya bağlanır. Metin boşsa
ürünün sayfası yoktur (adres 404 verir) ve kart doğrudan teklif bölümüne gider.
Detay sayfası da site istisnalarını uygular.

## Depoda ne var, ne yok

```
docker-compose.yml          WordPress + MariaDB + WP-CLI
docker/apache-wp.conf       .htaccess (mod_rewrite) izni
scripts/install.sh          Tekrarlanabilir kurulum
scripts/seed.php            Demo içerik + örnek görsel üretimi
scripts/inventory.php       CONTENT-INVENTORY.md üreteci
wp-content/themes/kocist-theme/
wp-content/themes/paletci-theme/
wp-content/plugins/network-content-studio/
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
npm run build        # tek seferlik derleme
npm run watch        # dosyaları izleyerek sürekli derleme
```

Çıktı `wp-content/themes/ahsapkasa-theme/assets/tailwind.css` dosyasına yazılır ve
depoya dahildir; yani siteyi çalıştırmak için Node kurmanız gerekmez, yalnızca
tasarımı değiştirecekseniz gerekir.

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
