# Kararlar

Kısa karar günlüğü: ne seçildi, neden.

## MVP 1

**Docker Desktop + Compose (WordPress 6.8 + MariaDB 11 + WP-CLI)**
En az kurulum gerektiren, tekrarlanabilir yöntem. Bilgisayarda Docker zaten kurulu ve
çalışır durumdaydı; PHP/MySQL/WP-CLI host'a kurulmadı. Host'ta 3306'da başka bir MySQL
çalıştığı için veritabanı portu dışarı açılmadı.

**Alt dizin tabanlı Multisite (`/kocist/`, `/paletci/`)**
Aynı origin altında çalışır; MVP 3'teki gömülü önizlemede `postMessage`/iframe
kısıtlarına takılmamak için tercih edildi. Alt alan adı moduna veya gerçek alan adlarına
geçiş, `DOMAIN_CURRENT_SITE` ve site kayıtları değiştirilerek sonradan yapılabilir.

**Multisite sabitleri `.multisite-ready` işaret dosyasıyla devreye giriyor**
`MULTISITE` sabiti ağ tabloları oluşmadan tanımlanırsa WordPress hata verir. Bu yüzden
`docker-compose.yml` içindeki `WORDPRESS_CONFIG_EXTRA`, `/var/www/html/.multisite-ready`
dosyası varsa ağ sabitlerini, yoksa yalnızca `WP_ALLOW_MULTISITE` tanımlar; dosyayı
`install.sh` ağ kurulumundan sonra oluşturur. Böylece kurulum tek komutla ve
tekrarlanabilir şekilde çalışır.

- Yol **mutlak** yazıldı: bu blok `wp-config.php` içinde `ABSPATH` tanımlanmadan önce
  çalışıyor, ayrıca WP-CLI `wp-config.php`'yi kendi dosya bağlamında `eval` ettiği için
  `__DIR__` de WP-CLI'nin yolunu veriyordu. İkisi de denendi, ikisi de hatalıydı.
- Aynı `WORDPRESS_CONFIG_EXTRA` bloğu YAML capasi (anchor) ile hem web hem WP-CLI
  container'ına veriliyor; yalnızca web'e verildiğinde WP-CLI "this is not a multisite
  installation" hatası veriyordu.

**Manifest dosya sisteminden okunuyor, `switch_to_blog()`'a güvenilmiyor**
`switch_to_blog()` başka sitenin tema kodunu yüklemez. Her tema kökündeki
`content-manifest.php` hiçbir WordPress fonksiyonuna bağımlı olmayan saf bir dizi
döndürür; eklenti, sitenin `stylesheet` option'ından tema slug'ını alıp dosyayı doğrudan
okur. Ağ panelinden (MVP 2) site bağlamına girmeden alan listesi üretmek bu sayede güvenli.

**Değerler `option` kaydında, sayfa içeriğinde değil**
Her alt sitenin kendi `nwcs_content` option'ı: `[sayfa][bileşen][alan]`. Tek bir kayıt
olduğu için okuma ucuz, site başına tamamen izole ve `docker compose down`/`up` sonrası
korunuyor. Temalarda gömülü metin bırakılmadı — "baştan sona düzenlenebilir" iddiası
ancak böyle ölçülebilir.

**Yazma tek yoldan: `nwcs_update_field()`**
Tür bazlı temizleme (text/textarea/url/image/icon/repeater) tek yerde. Seed betiği de
panel de aynı fonksiyonu kullanır; ikinci bir bağımsız yazma yolu üretilmedi.

**İkonlar sabit listeden**
16 ikonluk liste eklenti içinde satır içi SVG olarak duruyor. Panelden serbest SVG/HTML
girilemez; `nwcs_sanitize_icon()` liste dışını boşa düşürür.

**Örnek görseller GD ile yerel üretiliyor**
Kullanım hakkı sorunu olmasın ve gerçek marka görseli sanılmasın diye, `seed.php` her
görseli "ÖRNEK GÖRSEL" yazısıyla üretip gerçek WordPress medya kaydı olarak ekliyor
(alt metin dahil). Dışarıdan tek bir fotoğraf indirilmedi. Gerçek görseller sizden
geldiğinde aynı alanlara panelden yüklenecek.

**Demo formu bilinçli olarak "gönderildi" demiyor**
Paletçi'deki teklif formu gönderimi engelliyor ve "bilgileriniz hiçbir yere gönderilmedi"
uyarısı gösteriyor. Sahte başarı durumu üretilmedi; gerçek iletişim için telefon/WhatsApp
bağlantıları veriliyor. Gerçek e-posta teslimi ve spam koruması canlıya geçişte ayrı iş.

**İki tema ortaklaştırılmadı**
Koçist: koyu lacivert/amber, sıkışık grid, büyük harf başlıklar, keskin köşeler, rakam
şeridi. Paletçi: krem/orman yeşili/kiremit, serif başlıklar, ortalanmış hero, yuvarlak
kartlar, numaralı süreç adımları. Paylaşılan tek şey veri katmanı ve ikon listesi.

**Doğrulama gerçek tarayıcıyla yapıldı**
Bilgisayarda kurulu Chrome `--headless --screenshot` ile dört sayfanın ekran görüntüsü
alındı ve incelendi; ayrıca HTTP durum kodları, görsel yüklenmesi ve PHP hata günlüğü
kontrol edildi. Ek bir araç kurulmadı.

## MVP 2

**Panel sunucu tarafında çiziliyor, JavaScript yalnızca yardımcı**
Formlar normal HTML; JS sadece satır ekle/sil/taşı, kaydedilmemiş değişiklik uyarısı ve
ikon/görsel önizlemesi için var. JS kapalıyken de alanlar düzenlenip kaydedilebilir.
Panelde içerik yazan tek yol form gönderimidir; ikinci bir AJAX yazma yolu açılmadı.

**Form `admin_url('admin-post.php')` adresine gönderiliyor**
`admin-post.php` yalnızca `wp-admin/` kökünde bulunur; `wp-admin/network/` altında yoktur.
İlk denemede ağ adresine gönderildiği için 404 alındı, düzeltildi.

**Güvenlik zinciri**
`manage_network_options` yetkisi → bileşene özel nonce (`nwcs_save_<site>_<sayfa>_<bileşen>`)
→ hedef sitenin ağ içinde ve manifestli olduğunun doğrulanması → `switch_to_blog()` →
tür bazlı temizleme → tek `update_option` → `restore_current_blog()`. POST'tan gelen,
manifestte karşılığı olmayan anahtarlar sessizce yok sayılır; çıktı tarafında her değer
`esc_html`/`esc_url`/`esc_attr` ile basılır.

**Medya için wp.media yerine sunucu tarafı yükleme**
Ağ yönetiminde `wp.media` modalı admin-ajax'ı ana site bağlamında çağırır; dosya yanlış
sitenin medya kitaplığına düşer. Bunun yerine form içinde dosya girdisi kullanıldı ve
`media_handle_upload()` `switch_to_blog()` içinde çalıştırıldı — dosya doğru alt sitenin
`uploads/sites/<id>/` klasörüne gidiyor. Mevcut görseller de yine o sitenin kitaplığından
listeleniyor. Alt metin gerçek medya kaydına (`_wp_attachment_image_alt`) yazılıyor.

**Sıralama gizli `_sort` alanıyla**
PHP, form dizilerini DOM sırasına göre değil anahtarlara göre kurar; bu yüzden ↑/↓
düğmeleri satırı DOM'da taşırken her satırın gizli `_sort` değerini de günceller, sunucu
da satırları buna göre sıralayıp yeniden indeksler. Sürükle bırak eklenmedi (demo şartı
değil), sayfa yenilemeden çalışır.

**"Vazgeç" sayfayı yeniden yüklüyor**
`form.reset()` JS ile eklenen satırları geri almaz. Vazgeç, kaydedilmemiş değişiklik varsa
onay sorup sayfayı yeniden yükler; böylece her zaman veritabanındaki son kayıtlı hâl gelir.

**Tamamen boş tekrarlı satırlar kaydedilmiyor**
Kullanıcı satır ekleyip doldurmazsa sitede boş kart çıkmasın diye, hiçbir alanı dolu
olmayan satırlar yazılmadan eleniyor.

**Panel gerçek kaydetmelerle sınandı**
Oturum açıp formu ayrıştıran ve gönderen bir test betiğiyle: hero başlığı, footer metni,
ikon değişimi, menü sırası, ürün kartı silme ve gerçek görsel yükleme denendi; her
seferinde yalnızca hedef sitenin değiştiği, diğerinin değişmediği ve `docker compose
down && up` sonrasında değerlerin korunduğu doğrulandı. Sonrasında demo içeriği seed ile
temiz hâle getirildi.

## MVP 3

**Önizleme aynı origin'de iframe; tıklama postMessage ile geliyor**
Alt dizin tabanlı Multisite sayesinde panel ve siteler aynı origin'de. Tema şablonları
`nwcs_edit_attr()` ile her düzenlenebilir öğeye `data-nwcs-edit="sayfa.bileşen.alan"`
basar; önizleme içindeki küçük script tıklamayı yakalayıp panele `postMessage` gönderir.
İki taraf da mesajın origin'ini doğrular. Gömülü tıklama çalıştığı için belgelenmiş
yedek (yalnızca bölüm listesinden seçim) devreye girmedi; JavaScript kapalıyken zaten
o yedek davranış geçerli.

**Önizleme modu yalnızca yetkili kullanıcıda açılır**
`?nwcs_preview=1` tek başına yetmez; her istekte `manage_network_options` kontrol edilir.
Ziyaretçi aynı adresi açtığında hiçbir işaret, script veya ek veri gönderilmez (doğrulandı:
yetkisiz istekte 0 işaret). Admin çubuğu önizlemeye sızmasın diye `show_admin_bar` filtresi
erken (init'ten önce) eklenir — `template_redirect` çok geç kalıyordu.

**Düzenleyici AJAX ile yükleniyor, form hâlâ klasik yolla da çalışıyor**
Önizlemeden gelen tıklama sayfayı yenilemeden ilgili formu açar ve alanı vurgular.
Aynı HTML, JavaScript kapalıyken sunucu tarafında basılır ve form `admin-post.php`'ye
gider. Tek yazma mantığı (`nwcs_save_component`) her iki yolda da ortaktır.

**Bölüm sırası anında kaydediliyor**
↑↓ okları DOM'da taşır ve sırayı hemen `nwcs_order` ucuna yazar; kaydet düğmesi
beklemez. Sıra manifestteki "sıralanabilir" listesiyle doğrulanır, header/footer/hero
dışarıdadır. Yeniden başlatma sonrası korunduğu doğrulandı.

**WordPress 6.8 → 7.1.1 yükseltmesi**
Resmî MCP yolu Abilities API'yi gerektiriyor; bu API WordPress **6.9** ile çekirdeğe
girdi (ayrı eklenti dönemi bitti, `wordpress/abilities-api` deposu arşivlendi). Bu yüzden
çekirdek güncellendi. Resmî imaj dolu bir kuruluma dokunmadığı için
[update-core.sh](scripts/update-core.sh) dosyaları imajın içindeki `/usr/src/wordpress`
sürümünden eşitler (indirme yok, deterministik), ardından `core update-db --network`
çalışır. Yükseltme öncesi veritabanı yedeği alındı.

**MCP: resmî adapter + kendi yeteneklerimiz**
Çekirdek yalnızca üç okuma yeteneği kaydediyor (`core/get-site-info`, `get-user-info`,
`get-environment-info`) — içerik yazma yeteneği yok. Adapter da her şeyi kendiliğinden
açmaz; sunucu oluştururken hangi yeteneklerin araç olacağı tek tek sayılır. Bu demoda
kendi dört yeteneğimiz tanımlandı ve yalnızca onlar sunuldu. Yazma tarafında: yetki
kontrolü, yalnızca manifestli demo siteleri, yalnızca yerel adresler, manifestte olmayan
alanın reddi, görsel/repeater alanlarının bu yüzeyden dışlanması. Site kimliği her
çağrıda açıkça geçirilir (`site: "paletci"` ya da sayısal blog kimliği).

**Kimlik doğrulama: WordPress uygulama parolası**
Ayrı bir token sistemi kurulmadı. `WP_ENVIRONMENT_TYPE=local` tanımlandığı için uygulama
parolaları HTTPS olmadan da üretilebiliyor; bu yalnızca yerel demo içindir.
`claude mcp add --scope local` kullanılır, böylece kimlik bilgisi depoya girmez.

**Yedek dosyası web kökünden çıkarıldı**
Yükseltme öncesi alınan SQL yedeği `/var/www/html` altına düştüğü için tarayıcıdan
indirilebilir hâldeydi (HTTP 200). Fark edilip `/var/backups` altına taşındı. Yedekler
hiçbir zaman web köküne yazılmamalı.

## MVP 4 — nasıl yapıldı

**Havuz ağın ana sitesinde bir custom post type**
`nwcs_product` (+ `nwcs_product_cat` taksonomisi) ağ ana sitesinde tutulur; alt siteler
`switch_to_blog()` ile okur, kopya çıkarmaz. Post type her sitede kayıtlıdır (PHP
sürecinde global olduğu için), kayıtlar yalnızca havuz sitesinde bulunur. `show_ui`
kapalı: tek arayüz Ağ Yönetimi'ndeki Ürün Havuzu sayfasıdır.

**Site tarafında yalnızca üç option**
`nwcs_products_mode` (all/selected), `nwcs_products_selected` (sıralı kimlik listesi) ve
`nwcs_products_overrides` (ürün başına istisna). Ürün metni/görseli tekrar edilmez;
site yalnızca "neyi, hangi sırayla, hangi istisnayla gösteriyorum" bilgisini tutar.

**Fiyat serbest metin, boşluk anlamlı**
`450 TL`, `1.250 TL'den başlayan` gibi yazılabilir. Boş fiyat "Teklif al" demektir.
Site istisnasında "hiç dokunulmadı" ile "bilerek boşaltıldı" ayrımı için ayrı bir
`price_override` bayrağı tutulur; olmasaydı bir sitede fiyatı kaldırmak mümkün olmazdı.

**Detay sayfası: rewrite kuralı, gerçek post değil**
Alt sitede `/urun/<slug>/` adresi `nwcs_product` sorgu değişkenine bağlanır; şablon
havuzdan okur ve site istisnalarını uygular. Detay metni boş olan ürünün sayfası yoktur
(404). İlk denemede eksik eşleşme ana sayfayı döndürüyordu; ayrıca temalarda `404.php`
olmadığı için yedek şablon zinciri (`404` → `index`) eklendi.

**Arayüz: site seçici üstte, önizleme büyük**
Brief'teki "sağda dikey site seçici" şartı müşteri isteğiyle değişti. Seçici üst şeritte
hap biçiminde duruyor (adlar görünür), sağ sütun tamamen kalktı ve önizleme o alanı
aldı. İkon ızgarası varsayılan olarak kapalı ("Değiştir" ile açılıyor) — asıl sıkışıklık
oradan geliyordu. Renk olarak wp-admin grisinden ayrışan indigo/teal bir palet kullanıldı.

**Panel varlıkları iki sayfada da yükleniyor**
İlk denemede CSS yalnızca İçerik Stüdyosu'na yükleniyordu; Ürün Havuzu sayfası biçimsiz
açıldı. Enqueue koşulu iki menü anahtarını da kapsayacak şekilde düzeltildi.

## MVP 4 kapsamı (müşteri kararları)

Müşteri kararları — 21 Eylül 2026:

**1. Ürün havuzu Ağ Yönetimi'nde durur.**
Havuz belirli bir alt sitenin sayfası değil; tüm siteleri yöneten en üst seviyedeki
yönetim panelinde, İçerik Stüdyosu'nun yanında kendi sayfası olur ("Ürün Havuzu").
Ürünler orada bir kez girilir, siteler oradan beslenir.

**2. Görseller WordPress medya kitaplığında durur.**
Ürün görselleri havuzun bulunduğu yerin (ağ ana sitesi) medya kitaplığına yüklenir,
oradan silinir; dış bir depo veya dosya sistemi kullanılmaz. Alt siteler görseli
referansla gösterir, kopya oluşmaz.

**3. Site bazlı istisnalar gerekli.**
Müşteri tarafından açıkça istendi. Havuzdaki kayıt kaynaktır; site kaydı onun üzerine
yazılan ince bir katmandır. Siteye göre ezilebilen alanlar:

| Alan | Site bazında ezilebilir mi |
| --- | --- |
| Ürün adı | evet |
| Ürün açıklaması | evet |
| Ürün görseli | evet |
| Ürün fiyatı | evet (boş bırakılabilir) |
| Gizle / göster | evet |
| Sıra | evet |
| Kategori | hayır — havuzdan gelir |

**Fiyat kuralı:** fiyat alanı doludur ya da boştur. Boş olduğunda site, fiyat yerine
**"Teklif al"** gösterir. Bu kural hem havuzdaki değer hem de site bazlı ezme için
geçerlidir; yani bir üründe genel fiyat yazılıp tek bir sitede boş bırakılarak o sitede
"Teklif al" gösterilebilir.

**4. Site seçimi her sitede üç kipten biri olur:**
"hepsi" (yeni ürün eklenince otomatik görünür), "seçilenler" (panelden işaretlenenler),
"kurallı" (ör. belirli kategori). Böylece bir sitede tüm katalog, diğerinde alt küme
gösterilebilir ve binlerce ürün her siteye elle girilmez.

**5. Panel arayüzü elden geçirilecek** (aşağıdaki istekler):
- Önizleme alanı belirgin şekilde büyütülecek; asıl odak orası olacak.
- Sol sütun daraltılıp sıkışıklık giderilecek; alanlar daha rahat nefes alacak.
- Site seçici sağdaki dikey sütundan **üst şeride** taşınacak; site adları görünür
  kaldığı sürece küçük olabilir. Böylece ortadaki önizlemeye daha çok yer kalır.
- Arayüz renklendirilecek; wp-admin'in gri düzeninden ayrışan, daha okunaklı ve
  kullanıcı dostu bir görünüm hedeflenecek.
- Teknik bilgisi olmayan site sahibinin tek başına kullanabilmesi ölçüt olacak.

Not: brief'teki "sağda dikey site seçici" şartı, müşterinin bu isteğiyle değişti;
seçici üst şeride taşınıyor.

## MVP 5 sonrası hot-fix'ler

- **"Hepsi" kipinde tüm ürünler işaretli geliyor.** Kip zaten hepsini gösteriyordu ama
  kutular boş duruyordu; artık hem sunucu tarafında hem de kipi değiştirdiğiniz anda
  işaretleniyor.
- **"Bu sitede gizle" kutusu İçerik Stüdyosu'ndan kaldırıldı.** Gizleme Ürün
  Havuzu'ndaki toplu işlemden yönetiliyor. Daha önce gizlenmiş bir ürünün değeri
  kaybolmasın diye form o bilgiyi gizli alanda taşıyor ve kullanıcıya bir satırla
  bildiriyor.
- **Üst şeritteki ☰ düğmesi kaldırıldı.** Menü katlaması duruyor ama artık
  WordPress'in kendi "Menüyü daralt" tercihine saygı gösteriyor: kullanıcı menüyü
  açtıysa (`mfold = o`) panel onu tekrar katlamıyor.
- **"İstisna" yerine "Özelleştirilmiş/Özelleştirmeler".** Panelde tek bir dil kullanılıyor.
- **CSV kutusu açılır pencereye alındı.** Sağ üstteki "CSV ile toplu giriş" düğmesi
  native `<dialog>` açıyor; tarayıcıda tıklama testiyle doğrulandı.
- **Ürün formuna "Özelleştirmeler" bölümü eklendi** (Kategoriler'in üstünde). Ürünün
  hangi sitede hangi alanının havuzdakinden farklı kaydedildiğini üstü çizili/vurgulu
  bir tabloda gösterir. Şimdilik salt okunur; düzenleme İçerik Stüdyosu'nda yapılıyor.
  Veri zaten site option'larında tutulduğu için buradan düzenlenebilir hâle getirmek
  ileride yalnızca form alanı eklemek olacak.

## MVP 5 — nasıl yapıldı

**Önizleme tıklamasında sayfa anahtarı (hata düzeltmesi)**
Önizleme `sayfa.bileşen.alan` biçiminde mesaj gönderiyordu ama panel ilk parçayı
yok sayıp bileşeni o an açık sayfada arıyordu; "Tüm Sayfalar"a ait üst menü/footer
tıklanınca "Bölüm yüklenemedi" çıkıyordu. Artık sayfa anahtarı mesajdan okunuyor,
sekme kendiliğinden değişiyor ve "← Bölümler" doğru sayfanın listesine dönüyor.
Tarayıcıda çalışan bir gerileme testiyle doğrulandı (`content_page=global` gidiyor).

**Kategoriler taksonomiye taşındı**
Virgüllü metin kutusu yerine kategori listesi: havuzda kategori oluşturulup
silinebiliyor, üründe etiket gibi işaretleniyor, filtre kutusuna besleniyor.
Ürün formundaki "yeni kategori" alanı hem kategoriyi oluşturur hem ürüne ekler.

**Çoklu görsel**
Ürün görselleri `_nwcs_gallery` meta'sında sıralı liste. İlk görsel kart görselidir;
galerinin ilki aynı zamanda öne çıkan görsel olarak da yazılır, böylece eski
kayıtlar ve tek görselli kod yolları çalışmaya devam eder. Site istisnası bir
görsel seçtiğinde o görsel başa geçer, galerinin kalanı korunur.

**Medya Havuzu ayrı sayfa**
Toplu yükleme (tek seferde birden fazla dosya, isteğe bağlı ortak alt metin),
arama, sayfalama, başlık/alt metin düzenleme ve silme. Bir ürüne bağlı görsel
silinemiyor; hangi üründe kullanıldığı kartta yazıyor.

**Ölçek işleri**
CSV dışa/içe aktarma (slug ile eşleşme: varsa günceller, yoksa oluşturur; görsel
sütununda medya kimliği veya dosya adı kabul edilir), arama + kategori filtresi +
sayfalama, toplu işlemler (siteye göster/gizle, kategori ata) ve havuz okumasının
transient ile önbelleğe alınması. Önbellek anahtarı sürümlü: veri şekli değişince
eski önbellek kendiliğinden geçersiz olur — ilk denemede bu yüzden uyarı almıştık.

**Arayüz**
Adım şeridi ortalandı. WordPress yönetim menüsü panel sayfalarında katlanmış
başlıyor, üst şeritteki düğmeyle açılıyor ve tercih tarayıcıda hatırlanıyor.
Göz yorgunluğu için zemin yumuşatıldı: sol sütun gri, alanlar beyaz kartlar hâline
geldi, aralarına boşluk kondu.

## MVP 5 kapsamı — demonun son iterasyonu

Müşteri istekleri (21 Eylül 2026):

**1. Kategoriler metin kutusu değil, gerçek liste olacak.**
Ürün Havuzu'nda kategori oluşturulacak, üründe listeden etiket gibi seçilecek
(çoklu seçim, yazarken filtreleme, yeni kategori ekleme). Şu anki virgüllü metin
kutusu yazım hatasıyla mükerrer kategori üretmeye açık.

**2. Bir ürüne birden fazla görsel.**
Galeri: sıralanabilir görsel listesi. Kartta ilk görsel, detay sayfasında galeri.
Site istisnasında hangi görselin kullanılacağı seçilebilir.

**3. Adım şeridi ortalanacak.**
İçerik Stüdyosu ve Ürün Havuzu'ndaki "1 … 2 … 3 …" şeridi şu an sola yapışık.

**4. Medya havuzu sayfası.**
Görsellerin toplu yüklendiği, arandığı, alt metninin düzenlendiği ve silindiği ayrı
bir sayfa. Ürün Havuzu'ndaki küçük medya bölümü bunun yerini tutmuyor.

**5. Önizlemede sayfa algısı düzeltilecek (hata).**
"Ana Sayfa" sekmesindeyken önizlemede üst menü/footer gibi *tüm sayfalara* ait bir
bileşene tıklanınca "Bölüm yüklenemedi" çıkıyor. Sebep: önizleme mesajı
`global.header.logo_text` biçiminde geliyor, panel ilk parçayı yok sayıp bileşeni
açık sayfada (`home`) arıyor. Düzeltme: sayfa anahtarı mesajdan okunacak ve panel
gerekirse o sayfaya kendiliğinden geçecek.

**6. WordPress sol menüsü katlanacak.**
Panel açıkken yönetim menüsü varsayılan olarak dar/kapalı olacak, gerektiğinde
açılabilecek. Böylece önizlemeye daha fazla yer kalır.

**7. Görsel yorgunluğu azaltılacak.**
Uzun formlarda her yer beyaz olduğu için göz yoruluyor. Daha yumuşak zemin, bölüm
gruplama, katlanabilir alan grupları ve daha net görsel hiyerarşi.

Ölçek için eklenenler (öneri üzerine kabul edildi):

**8. CSV içe/dışa aktarma.** Ürün listesi tek dosyayla havuza alınabilecek ve dışa
aktarılabilecek. "Binlerce ürünü elle girmeyelim" isteğinin asıl karşılığı budur.

**9. Havuzda arama, kategori filtresi ve sayfalama.** Tek listede 200 kayıt sınırı
50 üründen sonra kullanılamaz hâle geliyor.

**10. Toplu işlemler.** Çoklu seçimle "şu sitede göster/gizle" ve "kategori ata".

**11. Havuz okumasının önbelleğe alınması.** Her sayfa çiziminde havuza geçiliyor;
ürün sayısı büyüdüğünde yavaşlar.

**Yeni ürün alanı eklenmeyecek.** Stok durumu / teslim süresi / minimum sipariş
sorusu soruldu; mevcut alanlar (ad, açıklama, fiyat, ölçü/not, kategori, görseller,
detay metni) yeterli görüldü.

## Açık bırakılanlar

- Taslak/revizyon akışı kapsam dışı: kaydetme davranışı "hemen yayınla".
- Görsel yükleme ve tekrarlı satır ekleme MCP yüzeyinde yok; panelden yapılır.
- Ürün havuzu (MVP 4): ürünler tek merkezde tutulup her sitede "hepsi" / "seçilenler" /
  kurallı gösterim ile sunulacak. Havuzun yeri (ana site mi ayrı bir havuz sitesi mi),
  görsellerin hangi sitenin medya kitaplığında duracağı ve site bazlı istisnalar
  (farklı başlık/fiyat/gizleme) o aşamada kararlaştırılacak.

---

## ahsapkasa yeniden tasarımı — tasarım kararları

Kapsam: ahsapkasa.com (Koçist Orman Ürünleri) sitesinin yeniden tasarımı. Ağa
`ahsapkasa-theme` ile üçüncü alt site olarak eklendi. Dört sayfa: ana sayfa,
hakkımızda, hizmetlerimiz, iletişim. Üst menü ve alt bilgi dört sayfada da aynı.

### Taşıyıcı fikir: ölçü

Mevcut sitedeki her ürün açıklaması "istediğiniz ölçü ve ebatlarda" diye başlıyor.
Firmanın kimliği kalite ya da kıdem değil, **müşterinin verdiği ölçüye göre üretmek**.
Tasarımın tek gösterişli öğesi bu yüzden teknik çizimdeki **ölçü çizgisi**: uçlarında
çentik, ortasında etiket. Hero başlığının altında bir kez kullanılır ve sayfa açılışında
soldan sağa bir kez çizilir (`prefers-reduced-motion` seçiliyse çizilmez). Başka hiçbir
yerde tekrarlanmaz.

### Renk

Brief: gri taban, ahşap ve ağaç yeşili.

| Jeton | Değer | İşi |
| --- | --- | --- |
| `bone` | `#eff0ee` | sayfa zemini — yeşile çalan açık gri |
| `dust` | `#dde0dc` | ikincil zemin, kenarlık |
| `moss` | `#6b716b` | ikincil metin |
| `ink` | `#212a24` | metin ve alt bilgi zemini |
| `forest` | `#2e5e3b` | **eylem** |
| `timber` | `#b98a4b` | **ölçü / yapı** |

Renk kuralı bilgi taşır: yeşil her zaman "tıklanabilir" demektir (bağlantı, düğme, odak
halkası), ahşap tonu her zaman "ölçü veya yapı" demektir (çentik, bölme çizgisi, teknik
etiket). Gölge, degrade ve dekoratif renk kullanılmaz.

### Tipografi

- **Archivo** (değişken genişlik ekseni, `wdth 112`) — başlık, menü, düğme, etiket.
  Geniş eksende kurulan başlıklar, sandık üstüne şablonla vurulan sevkiyat yazısını
  andırır; konuyla doğrudan ilgili bir seçim.
- **Literata** — okuma metni. Uzun soluklu okuma için tasarlanmış bir serif; Hakkımızda
  sayfasındaki uzun paragrafların 19px/1.75 ve ~66 karakterlik satırla rahat okunmasını
  sağlıyor.

Yaygın kalıbın tersi tercih edildi: başlıkta grotesk, gövdede serif.

### Eleme

Plan, yazılmadan önce "her benzer brief'te çıkacak varsayılan" olup olmadığı açısından
gözden geçirildi ve üç şey değiştirildi:

1. Gövde için ilk seçenek Source Serif'ti; fazla alışıldık olduğu için Literata'ya geçildi.
2. Hero'ya konması düşünülen "40 yıl / 4 ürün" rakam şeridi çıkarıldı — büyük rakam +
   küçük etiket + istatistik dizilimi en bilindik kalıplardan biri.
3. Ölçü rakamları için düşünülen monospace yazı tipinden vazgeçildi; aynı kalıbın parçası.

Ayrıca bilinçli olarak kullanılmayanlar: krem zemin, tümü büyük harf etiketler,
başlıkta tek kelimeyi renklendirme, düğme metnine "→" ekleme, orta noktayla birleştirilmiş
meta satırları, her bölüme ayrı gölgeli kart.

### Dörtlü ürün karesi

Mevcut sitede de dört ürünün ortasında "Teklif Alın" düğmesi var; fikir korundu, yeniden
kuruldu. Tek bir kare, `gap-px` ile ahşap tonunda ince çizgilerle dört bölmeye ayrılıyor;
kesişim noktasında zemin rengiyle halkalanmış dairesel bir düğme oturuyor.

Bölme içerikleri **dışa** hizalanır (üst sıra yukarı, alt sıra aşağı) ve iç tarafta düğmeye
pay bırakılır; böylece düğme hiçbir metnin üstüne binmez. Bu payın `md:` kademesi ayrıca
yazılmak zorunda: Tailwind'de `md:p-9` kısayolu, daha önce gelen `sm:pb-32` uzun yazımını
ezer. Mobilde daire gizlenir, kare tek sütuna iner ve düğme listenin altında tam genişlikte
görünür.

### Üst menü

Tailwind sınıflarıyla, durum değişimi küçük bir betikle (`assets/nav.js`):

- Sayfa kaydırılınca şerit 88px'den 64px'e iner, zemin ve alt çizgi kazanır (`data-stuck`).
- Menü bağlantısının altında tam alt çizgi yerine kısa bir çentik; ortadan dışa doğru açılır.
  Bulunulan sayfada sabit durur (`aria-current="page"`).
- Mobilde hamburger düğmesi çarpıya dönüşür, panel kademeli olarak açılır (`data-open`),
  Esc ve panel dışına tıklama ile kapanır.

Logo görseli firma adını zaten taşıdığı için yanında ayrıca yazı gösterilmez; `logo_text`
ve `logo_sub` alanları görselin alt metnini ve alt bilgiyi besler.

### Metin

Hakkımızda ve Amacımız metinleri ahsapkasa.com'daki içeriğin aynısıdır.

**Kalite Politikamız kısaltıldı** (806 → 242 karakter), müşterinin isteği üzerine: özgün metin
tek bir uzun devrik cümleydi ve yan yana durduğu Amacımız kutusuyla boy tutmuyordu. Kısaltmada
maddelerin hepsi korundu (zamanında ve uygun fiyatla teslim, müşteri beklentisi, işi ilk
seferinde doğru yapmak, çalışan sağlığı ve çevre, tedarikçilerle birlikte gelişme); yalnızca
tekrarlar atıldı. Özgün metin bu dosyanın git geçmişinde durur.

Ayrıca üç açık yazım hatası düzeltildi: "doğrultusun da" → "doğrultusunda",
"sepeklerini" → "spesifikasyonlarını", "ahsap" → "ahşap". Sitede karşılığı olmayan
(dolayısıyla yazılan) metinler: hero başlığı, ana sayfa giriş paragrafı, "Ahşap ambalaj"
ürün açıklaması, teklif şeridi, iletişim sayfası açıklamaları ve form metinleri.

### Teklif formu

Mevcut sitede form yok; sıfırdan kuruldu. Demoda sahte başarı ekranı gösterilmemesi
kuralı gereği gönderim gerçekten kaydediliyor: nonce doğrulaması, zorunlu alan kontrolü
(ad, e-posta, ölçü), görünmez bot tuzağı, ürün seçeneklerinin manifeste göre doğrulanması,
ardından `ak_quote` kayıt türüne özel kayıt. Hata durumunda alan alan mesaj gösterilir ve
girilen değerler korunur.

### Görseller

`resources/` klasöründeki fotoğraflar firmanın kendi sitesinden geliyor; yer tutucu
üretilmedi. `background01.jpg` (sisli dağ) konuyla ilgisiz olduğu için kullanılmadı;
`background02.jpg` (çam ormanı) yalnızca Hakkımızda sayfasında, firmanın hammaddesine
işaret ettiği için kullanıldı.

### Revizyonlar

Müşteri geri bildirimi sonrası yapılan değişiklikler:

1. **Yönetim şeridi çakışması.** Sabit üst menü, WordPress yönetim şeridinin altına giriyordu.
   Kural katman dışına yazılmak zorunda: Tailwind'in `top-0` yardımcı sınıfı `@layer utilities`
   içinde durur ve `@layer base`'deki kuralı ezer; katmansız kural ikisini de geçer.
2. **Hero.** Tipografi bloğu + ayrı fotoğraf şeridi kurgusu bırakıldı. Yerine kenardan kenara
   fotoğraf, üzerinde nötr koyu perde, ortalanmış başlık ve düğmeler geldi. Hero koyulaştığı
   için üst menü artık her zaman zeminli.
3. **Kutulaşma.** Giriş paragrafı, Hakkımızda metinleri ve hizmet kalemleri çerçeveli kutulara
   alındı (`.card`, `.panel`, `.frame`). Gövde metninin tek blok hâlinde uzaması yoruyordu.
4. **Hizmetler.** Tek kare / dört bölme fikri bırakıldı. Dört bağımsız kutu, aralarında boşluk,
   her birinde solda yazı sağda görsel; teklif düğmesi kutuların kesiştiği boşlukta durmaya
   devam ediyor. Düğmeye pay bırakan iç dolgu `md:` kademesinde ayrıca yazılmalı, yoksa
   `md:p-8` kısayolu onu eziyor.
5. **Renk.** Alt bilgi yeşile çalan koyu tondan nötr koyuya (`night`) alındı; kullanılmayan
   `forest-light` jetonu silindi. Sayfada tek yeşil kaldı ve yalnızca eylem anlamına geliyor.

### Hero slayt gösterisi ve görsel büyütme

**Hero slaytı.** `home.hero.slides` bir tekrarlayıcıdır; her satırda bir fotoğraf. Geçiş
çapraz solma (opaklık), süre 6 saniye. Fare üzerindeyken, odak içerideyken ve sekme arka
plandayken durur; `prefers-reduced-motion` seçiliyse otomatik ilerleme hiç başlamaz, slaytlar
yalnızca noktalarla gezilir. Tek slayt varsa noktalar basılmaz.

**Görsel büyütme.** Hizmetler sayfasındaki her kutu `data-gallery` içinde kendi görsellerini
JSON olarak taşır; kapak ve küçük görseller `data-lightbox-open="<sıra>"` ile hangi görselden
açılacağını söyler. Pencere yerli `<dialog>` üzerine kuruludur: `showModal()` odak tuzağını ve
Esc ile kapanmayı kendi halleder, üzerine ok tuşları ve ileri/geri düğmeleri eklendi. Kitaplık
kullanılmadı.

Ürün başına dört görsel alanı var (`image`, `image_2`, `image_3`, `image_4`). Eklentinin
tekrarlayıcısı iç içe tekrarlayıcı desteklemediği için galeri bu şekilde düz alanlarla kuruldu;
hepsi İçerik Stüdyosu'ndan düzenlenebilir.

**Hizmetler dizilimi.** Satır arası boşluk, ortadaki teklif düğmesini tamamen içine alacak
kadar geniş tutuldu (`sm:gap-y-44`). Böylece dört kutu da birebir aynı yapıda kalıyor ve
görseller aynı hizada duruyor; düğme hiçbir kutunun üzerine binmiyor. Önceki sürümde kutulara
pay bırakılıyordu, bu da alt sıradaki görselleri aşağı kaydırıyordu.

**Hakkımızda hero.** Sayfa, ana sayfayla aynı kurguya geçti: fotoğraf + koyu perde + ortalanmış
başlık. Dağ fotoğrafı zaten açık ve düşük kontrastlı olduğu için perde burada daha hafif
(`night/66`, ana sayfada `night/78`). Firma metni kısaltıldı; uzun hâli git geçmişinde duruyor.

---

## Excel'den toplu ürün yükleme

### Neden kütüphanesiz

`.xlsx`, içinde XML dosyaları bulunan bir ZIP paketidir. PhpSpreadsheet her Excel özelliğini
doğru okur ama Composer kurulumu ve depoya ~10 MB'lık bir `vendor` klasörü getirir. Sunucuda
`ZipArchive`, `XMLReader` ve `SimpleXML` zaten bulunduğu için `includes/xlsx-reader.php` bu
işi ~200 satırda yapıyor: paketin ilk sayfası ve paylaşılan metin tablosu akışlı okunuyor,
atlanan hücreler sütun sırası korunarak boşla dolduruluyor.

Kapsam dışı bırakılanlar bilinçli: tarih biçimleri (hücrenin ham Excel seri numarası okunur),
formüller (Excel'in kaydettiği son değer okunur), birden çok sayfa (yalnızca ilki) ve `.xls`.
Ürün listesi için bunların hiçbiri gerekmiyor.

**Okuyucuda yakalanan hata:** `XMLReader` ile `readOuterXml()` + `next()` kullanırken dıştaki
`read()` de ilerlettiği için her ikinci öğe atlanıyordu — paylaşılan metinlerin ve satırların
yarısı kayboluyordu. Doğru kalıp, ilk öğeye kadar `read()` ile gidip sonrasında yalnızca
`next( 'row' )` ile yürümek.

### Eşleştirme

Başlıklar Türkçe karakterler sadeleştirilip (ı→i, ş→s…) harf/rakam dışı atılarak eş anlamlı
listeyle karşılaştırılıyor. Sıra önemli: "açıklama" hem Kısa Açıklama'ya hem Detay Metni'ne
benziyor, önce Kısa Açıklama deneniyor. Bir alan iki sütuna atanamıyor; kullanıcı ikinciyi
seçince birincisi serbest bırakılıyor.

### Geri alma

Her yükleme bir "parti" olarak ağ seçeneğinde saklanıyor: oluşturulan ürünlerin kimlikleri ve
**güncellenen ürünlerin yükleme öncesi tam hâli** (başlık, içerik, kısa ad, fiyat, ölçü,
kategoriler). Geri alma, eklenenleri siliyor ve güncellenenleri geri yazıyor.

Bir ürünün `post_modified_gmt` değeri parti zamanından sonraysa o ürün elle düzenlenmiş
demektir; geri alma ona dokunmuyor ve kullanıcıya hangilerini atladığını söylüyor. Sessizce
üzerine yazmak, kullanıcının yükleme sonrası yaptığı işi yok ederdi.

Yalnızca son yükleme saklanıyor. Birden fazla partiyi tutmak geri alma sırasını ve çakışan
düzenlemeleri yönetmeyi gerektirirdi; istenen "son yüklemeyi geri al" davranışı için gereksiz.

### Güvenlik ve sınırlar

- İki AJAX ucu da `manage_network_options` yetkisi ve panel nonce'u istiyor.
- Geçici dosyalar `uploads/nwcs-import/` altında, `.htaccess` ile dışarıya kapalı; iş bitince
  siliniyor, bir günden eski artıklar her yüklemede süpürülüyor.
- Oturum belirteci küçük harf onaltılık (`bin2hex`). İlk sürümde `wp_generate_password()`
  kullanılmıştı ama `sanitize_key()` büyük harfleri düşürdüğü için dosya adı tutmuyordu.
- Satır sınırı 20.000; üstü okunmuyor ve kullanıcıya bildiriliyor.
- Yükleme 100'erli parçalar hâlinde işleniyor. Beklenen ölçek birkaç yüz ürün olsa da parçalı
  yapı hem ilerleme çubuğunu besliyor hem de ölçek büyürse yeniden yazmayı gerektirmiyor.

---

## Ürün kodu, site bazlı özelleştirme ve medya görünümü

### Ürün kodu neden eklendi

Excel sihirbazının ilk sürümünde "Ürün Kodu" sütunu eşleştirilebiliyordu ama havuzda böyle
bir alan yoktu; kod yalnızca kısa ad (`post_name`) üretmek için kullanılıyordu. Yani arayüz
olmayan bir alana işaret ediyordu.

Alanı kaldırmak yerine gerçeğini ekledik: `_nwcs_code`. Boş bırakılırsa ürünün kimliğinden
`URN-0006` biçiminde üretilir, böylece her ürünün değişmeyen bir kimliği olur. Aynı kodun iki
üründe kullanılması engellenir. Excel yüklemesi önce bu kodla eşleştirir, bulamazsa kısa ada
düşer — müşterinin kendi stok kodunu kullanabilmesi için.

### Özelleştirmeler artık düzenlenebilir

Bölüm önce yalnızca bilgi veriyordu; düzenleme İçerik Stüdyosu'nda yapılıyordu. Aynı ürün için
iki ayrı ekran arasında gidip gelmek gereksizdi. Veri katmanı (`nwcs_products_overrides`)
zaten yerinde olduğu için eklenen tek şey arayüz ve iki AJAX ucu oldu.

Görünürlük bilerek dışarıda bırakıldı: "bu sitede gizle" anahtarı daha önce panelden
kaldırılmıştı, aynı işlevi ikinci bir yerden geri getirmek kafa karıştırırdı. Ürünün hangi
sitede görüneceğini sitenin kendi seçimi belirler; burada yalnızca durumu okunur olarak
gösteriliyor.

Fiyat için ayrı bir işaret kutusu var, çünkü boş fiyat anlamlı bir değer: "Teklif al" demek.
İşaret kutusu olmadan "fiyatı boşalt" ile "fiyatı özelleştirme" ayırt edilemezdi.

### Medya görünümü

Görünüm tercihi kullanıcı başına `nwcs_media_view` meta alanında saklanır; adres çubuğundaki
`gorunum` parametresi geldiğinde yazılır, gelmediğinde son tercih kullanılır. JavaScript
gerektirmez, yer imi verilebilir.

---

## Yüzey, düğme ve görsel standardı (revizyon)

Müşteri geri bildirimi: hizmet kutularının rengi kötü, yazılar sıkışık, görsel çerçevesiz ve
alakasız duruyor, düğmelerin standardı yok.

### Düğme sistemi

Denetim yapınca sitede **yedi farklı dolgu ve dört farklı punto** çıktı: `px-8/py-4`,
`px-7/py-3.5`, `px-5/py-2.5`, `px-6/py-3`, `px-5/py-3`, `px-10/py-5`, `px-3/py-1`. Her düğme
yazıldığı yerde elle ölçülendirilmiş. Eleştiri yerindeydi.

Yerine tek bir bileşen: `.btn` + üç boy (`sm` / `md` / `lg`) + dört çeşit (`solid`, `outline`,
`light`, `light-outline`). Şablonlarda elle dolgu ya da punto yazılmıyor. Sitedeki on bir
düğmenin tamamı bu sisteme bağlı.

Yuvarlak köşe artık bilgi taşıyor: **hap köşe yalnızca düğmelere ait** (yuvarlak = basılabilir),
kart ve çerçeveler 4px köşede kalıyor. Tek yarıçapı her şeye uygulamak yaygın bir yapay-zekâ
kalıbı; iki yarıçaplı ayrım bilinçli.

### Yüzey tonları

Kart (`#e6e8e4`) sayfadan (`#eff0ee`) **daha koyuydu**. Sayfadan koyu bir kart nesne gibi değil,
çukur gibi okunur. Mantık tersine çevrildi: sayfa biraz derinleşti (`#e9ebe7`), kart beyaza
yaklaştı (yeni `surface` = `#f7f8f5`). Ayrım gölgeyle değil, yükselme + saç çizgisiyle sağlanıyor
— gölgeli kart yığını da bir kalıp.

**Ahşap tonu kenarlık rengi olmaktan çıkarıldı.** Baştaki kural "timber = ölçü ve yapı" idi;
kart kenarına %26 saydamlıkla sürülünce hem anlamını kaybediyor hem gri üstünde çamurlaşıyordu.
Kenarlıklar nötr `line` (`#d3d8cf`) oldu; ahşap tonu yalnızca ölçü çizgisinde, `.panel` sol
çentiğinde ve galeri noktalarında kaldı.

### Görsel standardı

Tek kural: **3:4 dikey, çerçeve içinde.** Hizmet kartlarındaki bütün ürün görselleri aynı
`.frame` bileşenini kullanıyor — Hakkımızda sayfasındaki görselle aynı dil. Ana sayfadaki ürün
kartları farklı bir bağlam (görsel önde, kenardan kenara) olduğu için 4:3 oranında sabitlendi;
kendi içinde tutarlı, ayrı bir muamele.

### Mobilde yatay taşma

Yan yana dizilimde `.btn`'in `white-space: nowrap` özelliği, dar metin kolonunu düğmenin
genişliğine zorluyor ve sayfayı 2px taşırıyordu. Kart telefonda dikey diziliyor (görsel üstte,
metin tam genişlikte), 640px üstünde yan yana geçiyor. Oran her iki dizilimde de 3:4.

### İkinci tur düzeltmeler

- **Ana sayfa ürün kartları `.card` sistemine bağlanmamıştı**; doğrudan `bg-bone-deep`
  yazılmıştı ve o ton bu turda daha da koyulaşınca kartlar iyice koyu kaldı. Sistem sınıfına
  geçirildi. Ders: yüzey rengi bileşene ait, şablona değil.

- **Küçük çerçeveli önizleme pul gibi duruyordu.** Görsel artık kartın sağ yarısını kenardan
  kenara dolduruyor; çerçeve işini kartın kendi kenarı görüyor. `.frame` bileşeni yalnızca
  Hakkımızda'daki büyük görselde kaldı — orada boyut yeterli olduğu için çalışıyor.

- **Başlık ile açıklama arasındaki fark yetersizdi** (24px'e karşı 19px, oran 1,26). Başlık
  28px'e çıkarıldı, açıklama 16px'e indirildi; oran 1,75. Kart düğmesi de `sm`'den `md`'ye
  alındı, gövde metniyle aynı puntoda.

- **İletişim düzeni**: başlık solda, iletişim bilgileri sağda bir kutuda, teklif formu ikisinin
  altında ortada ayrı bir kutuda. Telefon ve e-posta 24px'ten 21px'e indi; adres satır
  ortasından bölünüyordu.

## istanbulpaletci yeniden tasarımı

İkinci gerçek site. ahsapkasa ile aynı şirket (Koçist Orman Ürünleri), bu yüzden
asıl soru "iki kardeş site nasıl birbirine benzemeden aynı sistemde durur" oldu.

### Renk: logodan ölçüldü, görevleri ayrıldı

- **İndigo `#3D34ED`** logodaki "İSTANBUL" yazısından piksel piksel ölçüldü; canlı
  sitede hiç kullanılmıyordu (tema demosunun donuk laciverti vardı). Tek görevi
  eylem: düğme, bağlantı, etkin menü, odak halkası.
- **İndigo açık `#A09BFF`** logonun koyu zemin sürümünden alındı; koyu yüzeyde
  indigo'nun yerine geçer.
- **Sinyal sarısı `#F2B705`** yalnızca ölçü ve teknik veri: hero'daki ölçü
  çizgileri, kartlardaki ölçü satırı, şartname föyünün üst çizgisi. Başka yerde yok.
- **Arayüzde ahşap tonu yok.** ahsapkasa'da ahşap ve yeşil arayüzün kendisiydi;
  burada sıcaklığı yalnızca fotoğraflar taşır. İki site böylece aynı şirketin iki
  ayrı yüzü olur.

### Tipografi

Barlow Condensed (yalnızca 32 px üstü başlıklar) + Barlow (metin). Tek süper aile,
iki belirgin genişlik; kamu altyapı tabelaları için çizilmiş, endüstriyel. Dar kesim
küçük boyda "spor kulübü" gibi okunduğu için kart başlıkları normal genişliktedir.
Ölçüler `tabular-nums` ile hizalanır; monospace kullanılmadı.

### Yüzeyler ve gölge

Bant (kenardan kenara dolu renk), föy (`.sheet`, keskin köşe) ve kart (`.card`,
4 px). Yüzeylerde gölge yok; ayrım renk ve çizgiyle. **Tek istisna** üst menünün
açılır ürün paneli: sayfanın üstünde süzülen bir katman olduğu ancak gölgeyle
anlaşılıyor (beyaz ürün görselinin üstüne binince kenarı kayboluyordu).

Düğmeler 3 px köşeli, sert; ahsapkasa'nın hap düğmelerinden bilinçli olarak farklı.
Boy ve çeşit sistemi aynı mantıkta (`.btn--sm/md/lg`, `--solid/outline/light/light-outline`).

### Tek gösterişli an

Hero'da indigo alan üzerinde beyaz bir şartname föyü; çevresindeki sarı ölçü
çizgileri sayfa açılışında bir kez çizilir. Bölüm bölüm kayarak gelen animasyonlar
yok. Ölçü çizgisinin uç çentikleri önce 1 px idi ve görünmüyordu; 2 px'e çıkarıldı,
çizim animasyonunun kırpma alanı da çentikleri kesmesin diye 20 px dışa taşırıldı.

### Ürün sayfaları tek şablon

Beş ürün sayfası manifestte `'template' => 'product'` ile işaretli ayrı sayfalardır
(panelde her biri ayrı düzenlenir) ama tek şablonla basılır (`page.php` →
`template-parts/product-page.php`). Ürün listesinin tek kaynağı manifesttir:
menü, ızgara, liste, alt bilgi, form seçenekleri ve "diğer ürünler" oradan beslenir.
Manifestteki beş sayfa aynı alan yapısını bir kurucu fonksiyonla paylaşır; dosya hâlâ
WordPress'e bağımlı olmayan saf bir dizi döndürür.

Ürün havuzu kullanılmadı: ürün sayfaları şartname, galeri ve uzun açıklama taşıyor,
havuz ise kart düzeyinde ürünler için tasarlandı. ahsapkasa'da da ürünler manifestte.

### Şartname tablosu ve içerik dürüstlüğü

Canlı sitenin ürün sayfalarında hiçbir teknik veri yoktu; B2B alıcı için en büyük
eksik buydu. Şartname tablosu eklendi ama **firmaya özel değer uydurulmadı**: Euro
palet verileri (ölçüler, 35 kg, 1500 kg, 45,234 dm³) firmanın kendi sayfasından,
diğer satırlar kendi ürün metinlerinden türetildi. Euro palet metnindeki "80 adet
tahtadan imal edilir" cümlesi kaldırıldı; standart bir Euro palet ~20 parçadan
oluşur, cümle büyük olasılıkla çivi sayısıyla karışmış.

### Görseller

- Ana sayfa slaytlarının üçü kolajdı; beyaz ayırıcı çizgiler ölçülerek içlerinden
  tek fotoğraf kırpıldı (kurulum betiğinde, koordinatlarıyla).
- `home1-welcome` ve `home1-contact-pic` tema demosundan kalma stok görseller
  ("DIGITAL MARKETING" panolu adam, işaret eden kadın); kullanılmadı.
- `background01` (sisli dağ) ahsapkasa'da kullanıldığı için, `background02` (tren
  yolu) marka dışı olduğu için kullanılmadı.
- Planda Ahşap Sandık ve İkinci El Palet için "ÖRNEK GÖRSEL" öngörülmüştü; gerek
  kalmadı: `featured02` gerçek bir OSB sandık, `palet_2` yıpranmış bir palet.
- Logo yalnızca 160×51 px; retina ekranda yumuşak görünür. SVG ya da yüksek
  çözünürlüklü sürümü gelince değiştirilmeli.

### Canlı sitede bulunan hata

WhatsApp bağlantısında numara boşluklu yazılmış (`phone=+9053237498%2032`), bu
yüzden çalışmıyor. Yeni sitede `https://wa.me/905323749832` kullanıldı.

### Teklif formunda durum anahtarı (ahsapkasa'da da düzeltildi)

Form, yönlendirmeden sonra hata/başarı durumunu geçici kayıtta tutar ve anahtarı
adresle taşır. ahsapkasa bu anahtarı büyük-küçük harf karışık üretiyor, okurken
`sanitize_key()` küçültüyordu. Yerelde MySQL harf duyarsız olduğu için çalışıyordu;
harf duyarlı nesne önbelleğinde (LiteSpeed, Redis) mesajlar kaybolurdu. İki temada
da anahtar artık küçük harfli onaltılık ve okurken biçimi doğrulanıyor. Excel içe
aktarmadaki hatanın aynısı.

### Dil

Blog yazıları firma tarafından WordPress yönetiminden düzenleneceği için bu siteye
Türkçe dil paketi kuruldu (tarihler "11 Mayıs 2022", `lang="tr-TR"`). Diğer siteler
hâlâ İngilizce; ahsapkasa canlıya alınmadan önce aynı işlem yapılmalı.

### Revizyon: hero zemini

Düz indigo hero alanı göz alıyordu. İlk denenen koyu lacivert + ışık + ızgara
beğenilmedi. Son hâl: logodaki indigo korunup üstüne %60 siyah perde çekildi
(`--hero-veil` değişkeni, tek yerden ayarlanır). Föy hafif iki katmanlı gölgeyle
zeminden kalkar (`.sheet--raised`); bu, gölge kuralının ikinci istisnası.

### WhatsApp düğmesi marka yeşilinde

WhatsApp düğmeleri (üst menü, mobil menü, ürün sayfası) yeşil zemin, beyaz ikon ve
yazı: `.btn--whatsapp`. Parlak marka yeşili `#25D366` üzerinde beyaz yazının
kontrastı 2:1, okunabilirlik sınırı 4.5:1; bu yüzden düğme zemini aynı yeşilin koyu
tonu `#157F48` (5:1). Parlak yeşil beyaz zemindeki ikonlarda kalır (iletişim
listeleri). Tek istisna renk: WhatsApp anında tanınsın diye.

## SEO ve GEO — kalıcı ilke

Kullanıcı kararı: ağdaki bütün siteler (hedef 10) **SEO ve GEO destekli** olacak.
GEO (Generative Engine Optimization), ChatGPT, Perplexity ve Google'ın yapay zekâ
özetleri gibi motorlarda kaynak olarak gösterilmek demek. Bu bölüm bundan sonraki her
tema ve eklenti değişikliği için kontrol listesidir; yeni bir site bu listeyi
karşılamadan "tamam" sayılmaz.

### Nerede yaşar

Temada değil, **eklentide**. On site için tek kod; panelde her sayfanın kendi SEO
bölümü, site düzeyinde varsayılanlar. Temanın görevi yalnızca anlamlı HTML üretmek.
Uygulama: ağ yönetiminde "SEO ve GEO" sekmesi (ayrıntı: "SEO ve GEO — uygulama").

### Her sayfada

- Tek H1, atlamayan başlık hiyerarşisi, `lang="tr"`, `header/nav/main/footer`.
- Arama başlığı ve meta açıklama. Boşsa sayfa başlığı ve öne çıkan cümleden üretilir.
- Kanonik adres; alan adı eşlemesinden sonra her site kendi alan adını kanonik gösterir.
- Paylaşım önizlemesi (Open Graph + Twitter): başlık, açıklama, görsel.
- Görsellerde anlamlı alternatif metin; boyutu belli, tembel yüklenen görseller.

### Yapılandırılmış veri (JSON-LD)

- Site geneli: `Organization` / `LocalBusiness` (ad, adres, telefon, e-posta,
  koordinat, `sameAs` ile kardeş siteler), `WebSite`.
- Ürün sayfaları: `Product` (ad, açıklama, görsel, şartname satırları
  `additionalProperty` olarak). **Uydurma fiyat, puan, yorum yok.** Fiyat yoksa
  `offers` hiç yazılmaz.
- Blog: `BlogPosting` (tarih, yazar olarak firma). Tüm iç sayfalar: `BreadcrumbList`.
- `FAQPage` yalnızca sayfada gerçekten soru-cevap varsa.

### GEO'ya özel

- Her sitede `/llms.txt`: sitenin ne olduğu, sayfalar ve kısa açıklamaları.
- Önemli bilgiler **metin olarak** durur: ölçüler, kapasiteler, adres. Görselin
  içine gömülü bilgi yapay zekâ tarafından okunmaz. Şartname tabloları bu yüzden
  gerçek `<dl>`.
- Firma adı, adres ve telefon (NAP) 10 sitede birebir aynı yazılır. Tutarsızlık hem
  yerel SEO'yu hem yapay zekânın firmayı tek varlık olarak tanımasını bozar.
- İçerik dürüstlüğü burada SEO konusu da: yapay zekâ motorları metni alıntılar.
  Uydurma bir rakam on sitede çoğalır ve firma adına tekrarlanır.

### Ağ düzeyinde

- Site başına site haritası (WordPress çekirdeği: `/wp-sitemap.xml`).
- `robots.txt` alt dizin multisite'ta yalnızca kök alan adında çalışır; her site
  kendi alan adına eşlendiğinde site başına üretilmeli.
- Kardeş siteler birbirine bağlantı verirken aynı içeriği kopyalamaz (yinelenen
  içerik). ahsapkasa ve istanbulpaletci aynı firmanın farklı metinleriyle durur.
- Hız (Core Web Vitals) sıralama sinyalidir: sayfa oluşturucu kullanılmaz, CSS
  derlenmiş tek dosya, yazı tipleri önceden bağlanır.

## SEO ve GEO — uygulama

### Alanlar manifeste eklentiden eklenir

Temalar SEO alanı tanımlamaz. `nwcs_manifest_for_theme()` manifesti okurken her
sayfaya (ortak `global` hariç) bir `seo` bileşeni ("Arama ve Paylaşım") ve gizli bir
`site_seo` sayfası (firma bilgisi, varsayılanlar) ekler. Sonuç: kaydetme, temizleme,
alan çizimi, canlı önizleme ve MCP yetenekleri SEO alanlarında **ek kod olmadan**
çalışır. Yeni SEO sekmesi de ayrı bir kaydetme yolu açmaz; İçerik Stüdyosu'nun AJAX
kaydetmesini ve aynı güvenlik zincirini kullanır. `seo` ve `site_seo` adları ayrılmıştır.

Gizli sayfa (`'hidden' => true`) Stüdyo'nun sekmelerinde görünmez; firma bilgisi yalnızca
SEO sekmesinden düzenlenir.

### Boş alan = otomatik

Site sahibi hiçbir şey girmese de her sayfa başlık, açıklama ve görselle çıkar: başlık
sayfanın kendi başlığından (`title`/`name` alanları), açıklama öne çıkan cümleden
(`lead`/`short`/`intro`/`text`…), görsel ilk dolu görsel alanından. Tema isterse
`seo_source` ile hangi alanın kullanılacağını açıkça söyler. Otomatik değer panelde gri
yer tutucu olarak görünür; içerik değişince kendiliğinden güncellenir. Ana sayfada marka
önce gelir ("İstanbul Paletçi – İsteğe özel…"), iç sayfalarda sayfa adı.

### Ürün verisi şartnameden

`seo_source.type = Product` olan sayfalarda şartname satırları `additionalProperty`
olarak ürün verisine ve `llms.txt`'ye düz metin olarak geçer; aynı veri iki kez
girilmez. **Fiyat, puan, yorum üretilmez**: Google yanlış ürün verisini cezalandırır,
yapay zekâ motorları da alıntılar.

### Arayüz: önce önizleme, gerekirse düzenleme

Sitenin sayfaları Google sonucu önizlemesi olarak listelenir; düzenleyici her kartın
altında kapalı durur. İlk sürümde bütün formlar açıktı ve sekme ~9000 piksel tutuyordu;
kapalı hâliyle ~3400. Başlıkta 60, açıklamada 160 karakter sayacı var; alan boşken
sayaç otomatik değerin uzunluğunu gösterir. Stüdyo'nun kaydet çubuğu tek form için
yapışıktır; bu sekmede çok form olduğu için yerinde durur. "Kaydedilmedi" göstergesi
de artık olayın geldiği forma bağlı (önceden sayfadaki ilk formu varsayıyordu).

### Siteler arası tutarlılık

Ağ özeti, sitelerin firma bilgisinde unvan, telefon ve adresi karşılaştırır (boşluk ve
büyük-küçük harf farkı sayılmaz). İlk çalıştırmada gerçek bir tutarsızlık buldu: iki
site aynı adresi farklı yazıyor ("Şahintepe Mh. … No. 176/A-B-C-D" / "Şahintepe, …
No: 176"). Varsayılanlar her sitenin **kendi yayındaki** bilgisinden alındı; hangisinin
doğru olduğuna firma karar verir.

### Güvenlik ve temizlik

Site haritasından kullanıcı listesi çıkarıldı: WordPress varsayılanı yönetici kullanıcı
adını herkese açık bir dosyada listeliyordu. Yazar, tarih, arama ve ek sayfası
arşivleri `noindex`. `llms.txt` arama motorlarına kapalı sitede verilmez.

### Kapsam dışı

`robots.txt` alt dizin multisite'ta yalnızca kök alan adında çalışır; site başına dosya
alan adı eşlemesinden sonra. Google Search Console kaydı canlıya geçişte. Blog
yazılarının açıklaması yazının WordPress "Özet" alanından gelir; SEO sekmesi yazıları
listeler ama düzenlemeyi WordPress'in yazı düzenleyicisine bırakır.

## Demo sitelerin kaldırılması

İlk aşamanın iki demo sitesi (`/kocist/`, `/paletci/`), temaları, `seed.php`,
`seed-only.sh` ve Docker bağlantıları kaldırıldı; artık yalnızca gerçek siteler var.
Silmeden önce veritabanının tamamı `backups/` altına alındı (Git dışı, web kökü dışı).
Ürün havuzu özelliği ve içindeki ürünler (kullanıcının denemeleri dahil) duruyor; şu an
havuzu kullanan bir site yok.

`install.sh` artık betiğin başındaki `SITES` listesinden gerçek siteleri kurar ve var
olan bir sitenin içeriğine dokunmaz (seed alanları varsayılana geri yazdığı için
yalnızca site ilk kez oluşurken çalışır).

### Tema klasörü tek başına site değildir

Tema = tasarım + düzenlenebilir alan listesi. Site ayrıca açılır (Ağ Yönetimi → Siteler),
tema ona atanır, manifestteki adresler için sayfalar oluşturulur. Silerken önce site,
sonra tema: tersi sırada site temasız kalır. Bu dört adımı tek ekrana indiren bir "Yeni
site" sihirbazı sıradaki adım olarak önerildi.

## ithalkeresteci — kuzen kereste sitelerinin ilki

### Bulgu: alan adlarının kendi içeriği yok

ithalkeresteci.com ve kavakkeresteci.com aynı sunucuya bakıyor ve yalnızca
kocist.com.tr'yi tam ekran bir çerçeve (iframe) içinde gösteriyor; HTTPS çalışmıyor.
Çerçeve içeriği Google'da kocist.com.tr'ye sayılır, alan adlarının kendi değeri sıfır.
kocist.com.tr'nin kendisi 604 adresli (vida, tabanca, kompresör, kulübe modelleri) bir
katalog. Yeniden tasarımda her alan adı **kendi adının karşılığı olan odaklı bir kereste
sitesi** oldu; 604 adres yerine 10 sayfa.

### Tek ana tema, iki çocuk tema

WordPress'in standart ana/çocuk tema yapısı: `kereste-base` bütün şablonları,
hesaplayıcıyı, formu ve stili taşır; çocuk tema yalnızca üç renk değişkeni ve içerik
manifestidir. Manifest, ana temadaki kurucuya (`manifest.php`, bir closure döndürür)
site ayarlarını verir; dosya panel tarafından birden çok kez okunabildiği için fonksiyon
tanımlamaz. Eklenti değişmedi: çocuk temanın manifestini, SEO alanlarıyla birlikte,
kendiliğinden tanıdı (12 sayfa, 153 alan). İki kuzeni ayıran modüller manifestteki
`sortable_sections` listesinden seçilir.

### Tasarım: "parti boyası"

Kereste depolarında paket ve tomruk uçları partiyi belli etmek için boyanır; iki site
aynı depodan iki parti gibi, farklı işaret rengiyle. Diğer iki siteden ayrışma:

- beyaz zemin, **Zilla Slab** başlıklar (ahşap matbaa harfi karakteri), Source Sans 3 metin;
- **Saira Stencil One** yalnızca kesit ölçüsü ve tür etiketlerinde (paketlere böyle püskürtülür);
- köşesiz düğmeler (ahsapkasa hap, istanbulpaletci 3 px köşeli);
- ürün kartları **paket etiketi**: delik, ip, şablon harfli ölçü;
- vurgu ithalde petrol `#0B5C6B` (beyazda 7.6:1); kavak için ilk plan pastı, kullanıcı
  isteğiyle yeşil-kahveye döndü (aşağıda, kavakkeresteci).

İmza öğe **hero'daki m³ hesaplayıcısı**: kereste metreküple satılır; hesaplanan ölçü
"teklif al" ile teklif penceresine yazılır. Sayfa açılışında animasyon yok.

Hero'da fotoğraf ızgara hücresini hem en-boy oranı hem tam yükseklikle alınca sütunu
itiyordu (hesaplayıcı 263 piksele sıkıştı, ölçülerek bulundu); masaüstünde fotoğraf
hücreyi mutlak konumla dolduruyor.

### İçerik kaynakları ve bilinçli boşluklar

Metinler kocist.com.tr'deki ürün sayfalarından kısaltıldı; teknik satırlar oradaki
verilerle sınırlı (inşaatlık kereste: sarıçam/karaçam/ladin, 5×10 ve 10×10, 1./2.
sınıf; plywood: aspen/huş, filmli/filmsiz; kalas: çam/gürgen, fırınlanmış). Ürün
listesi ve ölçüler firmadan teyit bekliyor. Fotoğraflar firmanın kendi fotoğrafları
(822 px, ortada filigran); örnek görsel üretmek gerekmedi.

Adres ve telefon, kullanıcı kararıyla kaynak sitedeki gibi (Çatalca, 0549 648 19 19)
bırakıldı; firma sahibi SEO ve GEO sekmesinden düzeltecek. Sekme şu an iki telefon ve
üç adres yazımını tutarsızlık olarak gösteriyor. Kaynakta WhatsApp numarası boş
(`wa.me/`); düğme sonradan kullanıcı isteğiyle telefon hattıyla dolduruldu (aşağıda). Kaynak Hakkımızda "37 yıl" diyor, diğer siteler
"40 yıl"; burada kaynak korundu. kocist.com.tr'deki kontrplak sayfasının metni
"ASDASDASD…" test yazısı.

## kavakkeresteci — kuzen sitenin ikincisi

### m³ hesaplayıcısı kaynakta yoktu

Kullanıcı sordu: kocist.com.tr'de hesaplayıcı yok; onaylanan tasarım planında imza öğe
olarak önerilen bir ekleme. Satıcı istemezse ana sayfadan tek bölüm olarak kaldırılır
(hero'daki hesaplayıcı ve Hacim hesaplama sayfası); başka hiçbir şey ona bağlı değil.

### Renk: yeşil ve kahve

Kullanıcı kavak için pas yerine yeşil-kahve istedi: "tahtayla çok haşır neşiriz ve logosu
da yeşil". İşaret rengi doğrudan Koçist logosunun yeşili `#508038`; beyaz zeminde ve
beyaz yazıyla 4,7:1, koyulaştırmak gerekmedi. Şablon harfler kahve `#7A4A26` (7,4:1),
metin ve alt bilgi koyu ahşap kahvesi `#2A221C`, bölüm zemini açık adaçayı `#EEF0E9`.
Bunun için ana temaya üç yeni isteğe bağlı değişken eklendi (`--site-stamp`,
`--site-ink`, `--site-stone`); tanımlamayan ithal eski haliyle kalır.

### Modül farkı: tedarik yerine kullanım alanları

ithal ana sayfasında "toptan tedarik, hızlı sevkiyat" bandı var (tır ve forklift
fotoğrafları). Kavakta sevkiyat fotoğrafı yok, alıcının sorusu da farklı: "bu işime uyar
mı?". Yeni `uses` modülü kaynaktaki kullanım alanlarını (ambalaj/sandık/palet, mobilya,
inşaat, iç dekorasyon) sıralar; fotoğraf alanı isteğe bağlı, eşleşen fotoğraf olmadığı
için boş. Manifest kurucusu artık ana sayfada yalnızca `home_sections` listesindeki
modüllerin alanlarını açar; panelde kullanılmayan alan görünmez.

### Ürünler

Kavak kereste, çıta, ahşap takoz, OSB levha. kocist.com.tr'de kavak ailesinden içeriği
ve fotoğrafı olan dört ürün bunlar; "paletlik kereste"nin metni olmadığı için OSB seçildi.
Teknik satırlar kaynaktaki bilgiyle sınırlı; ölçü yazılmadı ve hesaplayıcının hazır
kesitleri boş bırakıldı (ölçü listesi satıcıdan gelecek).

### Koçist logosu ve WhatsApp (iki sitede)

- Grup logosu (`global.header.parent_logo`): üst menüde site adının yanında (xl ve
  üstü; lg'de menü satırı dolu), mobil menünün altında ve alt bilgide beyaz plaka üzerinde
  (logo açık zemine göre çizilmiş). Bağlantısı kocist.com.tr.
- WhatsApp: kaynak sitedeki bağlantıda numara yok. Kullanıcı "butonu görelim" dedi;
  **telefonla aynı hat (0549 648 19 19) varsayıldı**, `wa.me/905496481919`. Firma başka
  bir hat verirse panelden değişir. Düğme istanbulpaletci'deki gibi yeşil zemin
  `#157F48`, beyaz simge. Üst menüde md'de yazılı, lg'de gizli (yer yok), xl'de yalnız
  simge, 2xl'de yazılı; telefon yazısı 2xl'e alındı. Ürün sayfasında üç büyük düğme
  satırı taşırdığı için "Hacim hesapla" düğmesi metin bağlantısına indi.

### Yerel bağlantılar

Kuzen köprüsü ve alt bilgideki kardeş siteler canlı alan adlarını gösterir. Yerel ağda
seed betikleri bunları buradaki kopyalara çevirir (`kr_localize_links`); sonradan
kurulan kuzen, öncekinin bağlantısını da çevirir. Canlıda kod değişmeden alan adları
geçerli.

## Kereste siteleri: hesaplayıcı kaldırıldı, hero yeniden tasarlandı

Kullanıcı kararıyla m³ hesaplayıcısı iki siteden de kaldırıldı: hero'daki kutu, Hacim
hesaplama sayfası (WordPress sayfası da silindi, adres 404), menü öğesi, ürün
sayfasındaki bağlantı, manifestteki `calc_*` alanları, `calc` sayfası ve hazır kesitler,
site.js'deki hesaplama kodu. SSS'deki "metreküp nasıl hesaplanır" sorusu kaynaktan
geldiği için kaldı; yalnızca hesaplama sayfasına yönlendiren cümle çıktı.

### Yeni hero: ipe asılı paket etiketi

Değerlendirmede eski hero'nun zayıf yanı: sayfanın ilk ekranı bir forma benziyordu,
fotoğraf küçük ve filigranlıydı (kavakta beyaz fonlu ürün kesimi). Yeni hero:

- **Tam genişlik fotoğraf**: kereste paketlerinin boyayla işaretlenmiş uçları. Tasarımın
  "parti boyası" fikri artık fotoğrafın içinde: ithalde petrol, kavakta yeşil çizgiler.
- **Perde**: metin tarafı koyu (masaüstünde soldan, mobilde alttan), `--color-ink`
  üzerinden; kavakta koyu kahve, ithalde kurşuni.
- **İmza öğe**: sağda, hero'nun üst kenarından inen ipe asılı büyük **paket etiketi**.
  Üstte sitenin rozeti ve adı (parti etiketi), altında şablon harfli dört ürün; her satır
  ürün sayfasına gider. Ürün kartlarındaki etiketin hero boyu. Mobilde gizli (kartlar
  hemen altta).
- **Tek hareket**: etiket açılışta bir kez ipinde yerine oturur (-9° → -2°);
  `prefers-reduced-motion` açıkken hareket yok.
- İkincil düğme koyu zemin için yeni `btn--ghost`.

### Yapay zekayla üretilen örnek görseller

Hero fotoğrafları Flux 2 Max ile üretildi (`resources/kereste/ornek-hero-ithal.jpg`,
`ornek-hero-kavak.jpg`, 2400 px). Proje kuralı gereği sitede **"Örnek görsel"** notuyla
görünür (`home.hero.image_note`; gerçek fotoğraf yüklenince silinir). Dört aday
üretildi; boyası harfe benzeyen (yapay zeka izi belli olan) aday elendi. Ürün
fotoğrafları firmanın gerçek fotoğrafları olarak kaldı: ürünü doğru gösteren gerçek
görsel, daha güzel ama uydurma bir görselden iyidir.

## PR #1 entegrasyonu: 8 site tek panelde

Emirhan'ın tema paketi (Koçist + İstanbul Keresteci, Sanayi Palet, Ahşap Ambalaj)
`feature/entegrasyon` dalında `feature/seo-kereste` üzerine birleştirildi.
Öncesinde yedek alındı: veritabanı ve yüklemeler `backups/oncesi-entegrasyon-*`,
git etiketleri `yedek/oncesi-entegrasyon-main` ve `yedek/oncesi-entegrasyon-pr`.

- **Koçist**: yerelde silinen demo tema yerine PR'daki tema bütünüyle alındı. Düz
  birleştirme PR'ın değiştirmediği 5 dosyayı (index.php dahil) silinmiş bırakıyordu;
  index.php olmadan WordPress temayı bozuk sayar.
- **Kurulum**: Emirhan'ın temaları seed betiği kullanmaz, sayfalarını tema etkinleşince
  kendileri açar (`after_switch_theme`). `install.sh` listesinde seed yerine `-`.
- **Güvenlik düzeltmesi**: temaların "kurulum çalışmadıysa çalıştır" yedek yolu
  `admin_init`'te (Koçist'te `init`) herkese açıktı; anonim form gönderimleri
  (`admin-post.php`) de tetikliyordu. Artık yalnızca yönetici ya da WP-CLI; Koçist
  `admin_init`'e alındı, Ahşap Ambalaj'a eşzamanlı çalışma kilidi eklendi.
- **ISPM 15 belgesi**: firmanın kendi açık sitelerinde yayımlı olduğu için kullanıcı
  onayıyla repoda kalıyor.
- `.gitattributes`: `*.sh` her zaman LF (Windows'ta CRLF'e dönen betik konteynerde
  çalışmaz).
- Duman testi: 8 sitede 92 adres 200, her sayfada tek `<title>`, PHP uyarısı yok.
  Bilinen SEO eksikleri (6. adım): yeni 4 temada firma bilgisi yok; İstanbul
  Keresteci'nin 10 ürün sayfası (`/urunlerimiz/<ürün>/`) ve Koçist blog sayfası
  açıklamasız.
