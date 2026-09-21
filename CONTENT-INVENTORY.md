# İçerik Envanteri

Demo kapsamında **ekranda görünen her alanın** sayfa → bileşen → alan anahtarı listesi.
Bu dosyanın alan tabloları elle yazılmadı; her temanın `content-manifest.php` dosyasından
üretildi:

```bash
docker compose --profile cli run --rm --entrypoint sh wpcli -c \
  "wp --path=/var/www/html eval-file /scripts/inventory.php --url=http://localhost:8080/kocist/ --quiet"
```

Manifest tek doğruluk kaynağıdır: tema alanları buradan çizer, merkezî panel (MVP 2)
düzenleme formlarını yine buradan üretir.

## Demo kapsamındaki sayfalar

| Site | Sayfa | Yerel adres |
| --- | --- | --- |
| Koçist | Ana Sayfa | http://localhost:8080/kocist/ |
| Koçist | Kurumsal (örnek alt sayfa) | http://localhost:8080/kocist/kurumsal/ |
| İstanbul Paletçi | Anasayfa | http://localhost:8080/paletci/ |
| İstanbul Paletçi | Ürünlerimiz (örnek alt sayfa) | http://localhost:8080/paletci/urunlerimiz/ |
| İstanbul Paletçi | Euro Palet (örnek ürün detayı) | http://localhost:8080/paletci/urun/euro-palet/ |

Toplam düzenlenebilir alan: **Koçist 77**, **İstanbul Paletçi 80** (tekrarlı satırların
alt alanları dahil). Her iki sitede üst menüden footer'a kadar görünen tüm metinler,
bağlantı metin ve hedefleri, görseller (alt metinleriyle) ve ikonlar bu listededir.

## Ürünler bu listede değil — merkezî havuzda

Ürün kartları artık sayfaya gömülü değil; **Ağ Yönetimi → Ürün Havuzu**'nda tek yerde
durur. Her ürünün havuzdaki alanları: ad, kart açıklaması, fiyat, ölçü/not, kategoriler,
görsel, detay sayfası metni.

Her site kendi panelinde yalnızca şunu belirler:

| Ayar | Anlamı |
| --- | --- |
| Kip: **Hepsi** | Havuzdaki tüm ürünler görünür; yeni ürün otomatik çıkar (Koçist böyle) |
| Kip: **Seçilenler** | Yalnızca işaretlenenler, verilen sırada (İstanbul Paletçi böyle) |
| İstisna: ürün adı | Bu sitede farklı ad |
| İstisna: açıklama | Bu sitede farklı kart metni |
| İstisna: görsel | Bu sitede farklı görsel (havuzun medya kitaplığından) |
| İstisna: fiyat | Bu sitede farklı fiyat; **boş bırakılırsa "Teklif al"** |
| İstisna: gizle | Ürün bu sitede hiç görünmez |

**Fiyat kuralı:** fiyat serbest metindir (`450 TL`, `1.250 TL'den başlayan`). Boşsa site
fiyat yerine **"Teklif al"** gösterir. Demoda İkinci El Palet bunun örneğidir: Koçist'te
`180 TL`, İstanbul Paletçi'de site istisnasıyla "Teklif al".

## Bilinçli olarak sabit bırakılanlar

| Sabit parça | Neden |
| --- | --- |
| Header ve footer'ın **sayfadaki konumu** | Site iskeleti; yalnızca ana sayfa bölümleri sıralanabilir (MVP 3). |
| Hero bölümünün ana sayfadaki **ilk sırası** | İki temada da giriş bölümü sabit; içindeki tüm alanlar düzenlenebilir. |
| İkon **çizimleri** (SVG yolları) | Güvenlik: panelden serbest SVG/HTML girilemez, yalnızca 16 ikonluk sınırlı listeden seçim yapılır. |
| CSS/tipografi/renkler | Bu demo bir tasarım editörü değil; iki temanın görsel dili koda aittir. |
| Demo formunun davranışı | Form bilinçli olarak mesaj iletmez; metinleri düzenlenebilir ama "gönderildi" başarısı üretilmez. |
| Site adı/slug (`/kocist/`, `/paletci/`) | Multisite ağ yapısı; alan adı eşlemesi bu demonun dışında. |

## Örnek (gerçek olmayan) içerik uyarısı

- **Tüm görseller** yer tutucudur: `scripts/seed.php` içinde GD ile üretilir ve
  üzerlerinde "ÖRNEK GÖRSEL" yazar. Hiçbiri gerçek marka görseli değildir.
  Gerçek fotoğraf ve logolar sizden gelmeli.
- **Metinler** iki sitenin herkese açık sayfalarından anlaşılan iş tanımına göre
  yazılmış örnek metinlerdir; birebir kopya değildir.
- **İletişim bilgileri** (telefon, e-posta, adres, WhatsApp) ilgili sitelerin kendi
  herkese açık sayfalarından alınmıştır ve siteye özgüdür; uydurma numara kullanılmadı.
- Erişilemeyen sayfalar ve elde olmayan marka varlıkları tahmin edilmedi; demo kapsamı
  ana sayfa + bir örnek alt sayfa + menü/footer ile sınırlı tutuldu.

---
## Koçist (`kocist`)

### Sayfa: Tüm Sayfalar (Üst Bilgi, Menü, Footer)  `global`

**Üst Bilgi Şeridi** — `global / topbar`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `note` | Şerit Metni | tek satır metin |
| `hours_icon` | Çalışma Saati İkonu | ikon (sınırlı listeden) |
| `hours` | Çalışma Saatleri | tek satır metin |
| `phone_icon` | Telefon İkonu | ikon (sınırlı listeden) |
| `phone_label` | Telefon Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `email_label` | E-posta Metni | tek satır metin |
| `email_url` | E-posta Bağlantısı | bağlantı |

**Üst Menü / Başlık** — `global / header`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `logo_image` | Logo Görseli | görsel (medya kaydı + alt metin) |
| `logo_text` | Logo Yazısı | tek satır metin |
| `logo_sub` | Logo Alt Yazısı | tek satır metin |
| `menu` | Menü Öğeleri | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `cta_label` | Menü Buton Metni | tek satır metin |
| `cta_url` | Menü Buton Bağlantısı | bağlantı |

**Footer** — `global / footer`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `about_title` | Tanıtım Başlığı | tek satır metin |
| `about_text` | Tanıtım Metni | çok satırlı metin |
| `links_title` | Bağlantılar Başlığı | tek satır metin |
| `links` | Footer Bağlantıları | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `contact_title` | İletişim Başlığı | tek satır metin |
| `address_icon` | Adres İkonu | ikon (sınırlı listeden) |
| `address` | Adres | çok satırlı metin |
| `phone_label` | Telefon Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `email_label` | E-posta Metni | tek satır metin |
| `email_url` | E-posta Bağlantısı | bağlantı |
| `copyright` | Telif Satırı | tek satır metin |
| `demo_note` | Demo Uyarısı | tek satır metin |

### Sayfa: Ana Sayfa  `home`

Sıralanabilir bölümler: `capabilities`, `catalog`, `references`, `ctaband` (header ve footer sabittir).

**Hero (Giriş Bölümü)** — `home / hero`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `eyebrow` | Üst Etiket | tek satır metin |
| `title` | Başlık | tek satır metin |
| `description` | Açıklama | çok satırlı metin |
| `primary_label` | Birincil Buton Metni | tek satır metin |
| `primary_url` | Birincil Buton Bağlantısı | bağlantı |
| `secondary_label` | İkincil Buton Metni | tek satır metin |
| `secondary_url` | İkincil Buton Bağlantısı | bağlantı |
| `image` | Hero Görseli | görsel (medya kaydı + alt metin) |
| `stats` | Rakam Şeridi | tekrarlı satırlar → `value` (tek satır metin), `label` (tek satır metin) |

**Yetkinlik Kartları** — `home / capabilities`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `subtitle` | Bölüm Alt Başlığı | çok satırlı metin |
| `items` | Kartlar | tekrarlı satırlar → `icon` (ikon (sınırlı listeden)), `title` (tek satır metin), `text` (çok satırlı metin) |

**Ürün Grupları** — `home / catalog`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `subtitle` | Bölüm Alt Başlığı | çok satırlı metin |
| `pool` | Ürünler (merkezî havuzdan) | merkezî ürün havuzundan seçim (kip + site istisnaları) |
| `cta_label` | Kart Bağlantı Metni | tek satır metin |

**Kullanım Alanları** — `home / references`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `subtitle` | Bölüm Alt Başlığı | çok satırlı metin |
| `items` | Satırlar | tekrarlı satırlar → `icon` (ikon (sınırlı listeden)), `title` (tek satır metin), `text` (çok satırlı metin) |

**Teklif Şeridi** — `home / ctaband`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Buton Metni | tek satır metin |
| `button_url` | Buton Bağlantısı | bağlantı |
| `note` | Alt Not | tek satır metin |

### Sayfa: Kurumsal (Örnek Alt Sayfa)  `inner`

**Sayfa Başlığı** — `inner / page_head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `breadcrumb` | Yol Göstergesi | tek satır metin |
| `title` | Başlık | tek satır metin |
| `subtitle` | Alt Başlık | çok satırlı metin |

**Kurumsal Metin** — `inner / story`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `body` | Metin | çok satırlı metin |
| `image` | Görsel | görsel (medya kaydı + alt metin) |
| `points` | Maddeler | tekrarlı satırlar → `icon` (ikon (sınırlı listeden)), `text` (tek satır metin) |

**Çalışma İlkeleri** — `inner / values`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `items` | Kartlar | tekrarlı satırlar → `icon` (ikon (sınırlı listeden)), `title` (tek satır metin), `text` (çok satırlı metin) |

_Toplam düzenlenebilir alan (tekrarlı satır alanları dahil): 77._


## İstanbul Paletçi (`paletci`)

### Sayfa: Tüm Sayfalar (Menü ve Footer)  `global`

**Üst Menü / Başlık** — `global / header`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `logo_image` | Logo Görseli | görsel (medya kaydı + alt metin) |
| `logo_text` | Logo Yazısı | tek satır metin |
| `logo_sub` | Logo Alt Yazısı | tek satır metin |
| `menu` | Menü Öğeleri | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `phone_icon` | Telefon İkonu | ikon (sınırlı listeden) |
| `phone_label` | Telefon Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `whatsapp_icon` | WhatsApp İkonu | ikon (sınırlı listeden) |
| `whatsapp_label` | WhatsApp Buton Metni | tek satır metin |
| `whatsapp_url` | WhatsApp Bağlantısı | bağlantı |

**Footer** — `global / footer`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `about_title` | Tanıtım Başlığı | tek satır metin |
| `about_text` | Tanıtım Metni | çok satırlı metin |
| `links_title` | Bağlantılar Başlığı | tek satır metin |
| `links` | Footer Bağlantıları | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `contact_title` | İletişim Başlığı | tek satır metin |
| `address` | Adres | çok satırlı metin |
| `phone_label` | Telefon Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `mobile_label` | Cep Telefonu Metni | tek satır metin |
| `mobile_url` | Cep Telefonu Bağlantısı | bağlantı |
| `email_label` | E-posta Metni | tek satır metin |
| `email_url` | E-posta Bağlantısı | bağlantı |
| `copyright` | Telif Satırı | tek satır metin |
| `demo_note` | Demo Uyarısı | tek satır metin |

### Sayfa: Anasayfa  `home`

Sıralanabilir bölümler: `products`, `process`, `why`, `quote` (header ve footer sabittir).

**Hero (Giriş Bölümü)** — `home / hero`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `badge` | Rozet Metni | tek satır metin |
| `title` | Başlık | tek satır metin |
| `subtitle` | Alt Metin | çok satırlı metin |
| `primary_label` | Birincil Buton Metni | tek satır metin |
| `primary_url` | Birincil Buton Bağlantısı | bağlantı |
| `secondary_label` | İkincil Buton Metni | tek satır metin |
| `secondary_url` | İkincil Buton Bağlantısı | bağlantı |
| `image` | Hero Görseli | görsel (medya kaydı + alt metin) |
| `chips` | Öne Çıkan Etiketler | tekrarlı satırlar → `icon` (ikon (sınırlı listeden)), `label` (tek satır metin) |

**Ürünler** — `home / products`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `subtitle` | Bölüm Alt Başlığı | çok satırlı metin |
| `pool` | Ürünler (merkezî havuzdan) | merkezî ürün havuzundan seçim (kip + site istisnaları) |
| `cta_label` | Kart Bağlantı Metni | tek satır metin |

**Üretim Süreci** — `home / process`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `subtitle` | Bölüm Alt Başlığı | çok satırlı metin |
| `steps` | Adımlar | tekrarlı satırlar → `number` (tek satır metin), `title` (tek satır metin), `text` (çok satırlı metin) |

**Neden Biz** — `home / why`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `image` | Bölüm Görseli | görsel (medya kaydı + alt metin) |
| `items` | Maddeler | tekrarlı satırlar → `icon` (ikon (sınırlı listeden)), `title` (tek satır metin), `text` (çok satırlı metin) |

**Teklif Bölümü (Demo Form)** — `home / quote`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `name_label` | Ad Alanı Etiketi | tek satır metin |
| `phone_label` | Telefon Alanı Etiketi | tek satır metin |
| `detail_label` | Detay Alanı Etiketi | tek satır metin |
| `button_label` | Buton Metni | tek satır metin |
| `demo_notice` | Demo Uyarısı | çok satırlı metin |
| `call_label` | Arama Butonu Metni | tek satır metin |
| `call_url` | Arama Butonu Bağlantısı | bağlantı |

### Sayfa: Ürünlerimiz (Örnek Alt Sayfa)  `inner`

**Sayfa Başlığı** — `inner / page_head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `badge` | Rozet | tek satır metin |
| `title` | Başlık | tek satır metin |
| `subtitle` | Alt Başlık | çok satırlı metin |

**Giriş Metni** — `inner / intro`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `body` | Metin | çok satırlı metin |
| `image` | Görsel | görsel (medya kaydı + alt metin) |

**Ürün Listesi** — `inner / list`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `items` | Satırlar | tekrarlı satırlar → `icon` (ikon (sınırlı listeden)), `title` (tek satır metin), `text` (çok satırlı metin), `meta` (tek satır metin) |

**Sayfa Sonu Çağrısı** — `inner / cta`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Buton Metni | tek satır metin |
| `button_url` | Buton Bağlantısı | bağlantı |

_Toplam düzenlenebilir alan (tekrarlı satır alanları dahil): 80._

