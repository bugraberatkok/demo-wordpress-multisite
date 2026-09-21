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

## Açık bırakılanlar

- Panel arayüzü (MVP 2) ve önizleme/sıralama (MVP 3) henüz yok; veri katmanı ve
  `nwcs_section_order` altyapısı hazır.
- Taslak/revizyon akışı kapsam dışı: kaydetme davranışı "hemen yayınla" olacak.
- MCP entegrasyonu MVP 3'te değerlendirilecek; en az yetkili alan okuma/güncelleme
  yetenekleri eklenti içinde tanımlanacak.
