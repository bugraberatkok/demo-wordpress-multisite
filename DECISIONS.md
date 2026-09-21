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

## MVP 4 kapsamı (kararlaştırıldı, henüz yapılmadı)

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
Müşteri tarafından açıkça istendi: aynı ürün bir sitede farklı başlık/açıklama/görsel
ile görünebilmeli, bir sitede gizlenebilmeli, sıralaması siteye göre değişebilmeli.
Havuzdaki kayıt kaynak, site kaydı üzerine yazılan ince bir katman olur.

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

## Açık bırakılanlar

- Taslak/revizyon akışı kapsam dışı: kaydetme davranışı "hemen yayınla".
- Görsel yükleme ve tekrarlı satır ekleme MCP yüzeyinde yok; panelden yapılır.
- Ürün havuzu (MVP 4): ürünler tek merkezde tutulup her sitede "hepsi" / "seçilenler" /
  kurallı gösterim ile sunulacak. Havuzun yeri (ana site mi ayrı bir havuz sitesi mi),
  görsellerin hangi sitenin medya kitaplığında duracağı ve site bazlı istisnalar
  (farklı başlık/fiyat/gizleme) o aşamada kararlaştırılacak.
