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
- **MVP 2 — sırada**: Network Admin içinde "İçerik Stüdyosu" paneli; sağda dikey site seçici,
  sayfa/bileşen listesi, tüm alanların düzenlenmesi, hemen yayınla + vazgeç.
- **MVP 3 — sonra**: panel ortasında canlı önizleme ve tıkla-düzenle, bölüm sıralama
  (Yukarı/Aşağı), Claude Code ↔ WordPress MCP ile kontrollü alan güncelleme.

Bu demonun kapsamı dışında bırakılanlar (canlıya geçişte ayrı iş): taslak/revizyon akışı,
gerçek e-posta teslimi ve spam koruması, alan adı eşlemesi, e-ticaret.
