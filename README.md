# İki WordPress sitesi, tek içerik paneli — yerel demo

Tek bir WordPress **Multisite** ağı altında, tasarımları birbirinden bağımsız iki demo sitesi:

| Ne | Yerel adres |
| --- | --- |
| Koçist (kurumsal / endüstriyel tema) | http://localhost:8080/kocist/ |
| — örnek alt sayfa | http://localhost:8080/kocist/kurumsal/ |
| İstanbul Paletçi (ürün / teklif odaklı tema) | http://localhost:8080/paletci/ |
| — örnek alt sayfa | http://localhost:8080/paletci/urunlerimiz/ |
| Ağ yönetimi (Network Admin) | http://localhost:8080/wp-admin/network/ |
| Giriş | http://localhost:8080/wp-login.php |

> Bu tamamen yerel bir demodur. Gerçek `kocist.com.tr` / `istanbulpaletci.com`
> alan adlarına, DNS'e veya canlı sitelere dokunulmaz. Tüm görseller "ÖRNEK GÖRSEL"
> yazılı yer tutuculardır; gerçek marka varlığı değildir.

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
