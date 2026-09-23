# İçerik Envanteri

Ağdaki her sitede **ekranda görünen her alanın** sayfa → bileşen → alan anahtarı listesi.
Bu dosyanın alan tabloları elle yazılmadı; her temanın `content-manifest.php` dosyasından
üretildi:

```bash
docker compose --profile cli run --rm --entrypoint sh wpcli -c \
  "wp --path=/var/www/html eval-file /scripts/inventory.php --url=http://localhost:8080/istanbulpaletci/ --quiet"
```

Manifest tek doğruluk kaynağıdır: tema alanları buradan çizer, İçerik Stüdyosu düzenleme
formlarını yine buradan üretir.

Her sayfadaki **Arama ve Paylaşım** (`seo`) bileşeni ve gizli **Site Geneli**
(`site_seo`) sayfası temadan değil, eklentiden gelir (bkz. README → SEO ve GEO); bu
yüzden her sitede aynıdır.

## Koçist · ahsapkasa.com (`ahsapkasa`)

### Sayfa: Tüm Sayfalar (Üst Menü, Alt Bilgi)  `global`

**Üst Menü** — `global / header`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `logo_image` | Logo Görseli | görsel (medya kaydı + alt metin) |
| `logo_text` | Logo Yazısı | tek satır metin |
| `logo_sub` | Logo Alt Yazısı | tek satır metin |
| `menu` | Menü Öğeleri | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `whatsapp_label` | WhatsApp Düğmesi Metni | tek satır metin |
| `whatsapp_url` | WhatsApp Bağlantısı | bağlantı |
| `cta_label` | Menü Düğmesi Metni | tek satır metin |
| `cta_url` | Menü Düğmesi Adresi | bağlantı |

**Alt Bilgi** — `global / footer`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `tagline` | Alt Bilgi Metni | çok satırlı metin |
| `phone_label` | Telefon Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `email_label` | E-posta Metni | tek satır metin |
| `email_url` | E-posta Bağlantısı | bağlantı |
| `address` | Adres | çok satırlı metin |
| `nav_title` | Menü Başlığı | tek satır metin |
| `external_note` | Diğer Ürünler Metni | tek satır metin |
| `external_label` | Diğer Ürünler Bağlantı Metni | tek satır metin |
| `external_url` | Diğer Ürünler Adresi | bağlantı |
| `copyright` | Telif Satırı | tek satır metin |

### Sayfa: Ana Sayfa  `home`

Sıralanabilir bölümler: `intro`, `family`, `ctaband` (header ve footer sabittir).

**Giriş (Hero)** — `home / hero`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | çok satırlı metin |
| `measure_label` | Ölçü Çizgisi Yazısı | tek satır metin |
| `primary_label` | Birinci Düğme Metni | tek satır metin |
| `primary_url` | Birinci Düğme Adresi | bağlantı |
| `secondary_label` | İkinci Düğme Metni | tek satır metin |
| `secondary_url` | İkinci Düğme Adresi | bağlantı |
| `slides` | Arka Plan Slaytları | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Giriş Paragrafı** — `home / intro`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `text` | Paragraf | çok satırlı metin |

**Ürün Grubu Şeridi** — `home / family`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `note` | Bölüm Notu | tek satır metin |
| `items` | Ürünler | tekrarlı satırlar → `label` (tek satır metin), `note` (tek satır metin), `image` (görsel (medya kaydı + alt metin)), `url` (bağlantı) |

**Teklif Şeridi** — `home / ctaband`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Düğme Metni | tek satır metin |
| `button_url` | Düğme Adresi | bağlantı |

**Arama ve Paylaşım** — `home / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Hakkımızda  `about`

**Sayfa Başlığı** — `about / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `image` | Arka Plan Fotoğrafı | görsel (medya kaydı + alt metin) |

**Firma Metni** — `about / story`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `lead` | Bölüm Alt Metni | tek satır metin |
| `p1` | Birinci Paragraf | çok satırlı metin |
| `p2` | İkinci Paragraf | çok satırlı metin |

**Amacımız** — `about / purpose`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |

**Kalite Politikamız** — `about / quality`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |

**Arama ve Paylaşım** — `about / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Hizmetlerimiz  `services`

**Sayfa Başlığı** — `services / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Dörtlü Ürün Karesi** — `services / grid`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `cta_label` | Ürün Düğmesi Metni | tek satır metin |
| `cta_url` | Ürün Düğmesi Adresi | bağlantı |
| `items` | Ürünler | tekrarlı satırlar → `title` (tek satır metin), `text` (çok satırlı metin), `image` (görsel (medya kaydı + alt metin)), `image_2` (görsel (medya kaydı + alt metin)), `image_3` (görsel (medya kaydı + alt metin)), `image_4` (görsel (medya kaydı + alt metin)) |

**Sayfa Sonu Notu** — `services / note`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |

**Arama ve Paylaşım** — `services / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: İletişim  `contact`

**Sayfa Başlığı** — `contact / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**İletişim Bilgileri** — `contact / details`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `phone_title` | Telefon Başlığı | tek satır metin |
| `phone_icon` | Telefon İkonu | ikon (sınırlı listeden) |
| `phone_label` | Telefon | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `email_title` | E-posta Başlığı | tek satır metin |
| `email_icon` | E-posta İkonu | ikon (sınırlı listeden) |
| `email_label` | E-posta | tek satır metin |
| `email_url` | E-posta Bağlantısı | bağlantı |
| `address_title` | Adres Başlığı | tek satır metin |
| `address_icon` | Adres İkonu | ikon (sınırlı listeden) |
| `address` | Adres | çok satırlı metin |
| `hours_title` | Çalışma Saatleri Başlığı | tek satır metin |
| `hours_icon` | Çalışma Saatleri İkonu | ikon (sınırlı listeden) |
| `hours` | Çalışma Saatleri | tek satır metin |

**Teklif Formu** — `contact / form`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Form Başlığı | tek satır metin |
| `note` | Form Açıklaması | çok satırlı metin |
| `name_label` | Ad Soyad Etiketi | tek satır metin |
| `company_label` | Firma Etiketi | tek satır metin |
| `email_label` | E-posta Etiketi | tek satır metin |
| `phone_label` | Telefon Etiketi | tek satır metin |
| `product_label` | Ürün Etiketi | tek satır metin |
| `size_label` | Ölçü Etiketi | tek satır metin |
| `size_hint` | Ölçü Alanı İpucu | tek satır metin |
| `message_label` | Mesaj Etiketi | tek satır metin |
| `submit_label` | Gönder Düğmesi Metni | tek satır metin |
| `privacy_note` | Gizlilik Notu | tek satır metin |
| `success_title` | Başarı Başlığı | tek satır metin |
| `success_text` | Başarı Metni | çok satırlı metin |

**Arama ve Paylaşım** — `contact / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Arama ve Paylaşım: Site Geneli  `site_seo`

**Firma Bilgisi** — `site_seo / org`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Firma adı | tek satır metin |
| `legal_name` | Resmî unvan | tek satır metin |
| `description` | Firma tanımı | çok satırlı metin |
| `logo` | Logo | görsel (medya kaydı + alt metin) |
| `phone` | Telefon | tek satır metin |
| `email` | E-posta | tek satır metin |
| `street` | Açık adres | tek satır metin |
| `district` | İlçe | tek satır metin |
| `city` | İl | tek satır metin |
| `postal_code` | Posta kodu | tek satır metin |
| `country` | Ülke kodu | tek satır metin |
| `latitude` | Enlem | tek satır metin |
| `longitude` | Boylam | tek satır metin |
| `same_as` | Firmanın diğer adresleri | tekrarlı satırlar → `url` (bağlantı) |
| `parent_name` | Bağlı olduğu grup | tek satır metin |
| `parent_url` | Grubun web adresi | bağlantı |

**Varsayılanlar** — `site_seo / defaults`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `share_image` | Varsayılan paylaşım görseli | görsel (medya kaydı + alt metin) |

_Toplam düzenlenebilir alan (tekrarlı satır alanları dahil): 127._


## Koçist · istanbulpaletci.com (`istanbulpaletci`)

### Sayfa: Tüm Sayfalar (Üst Menü, Alt Bilgi)  `global`

**Üst Menü** — `global / header`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `logo_image` | Logo | görsel (medya kaydı + alt metin) |
| `logo_text` | Firma Adı (logo yoksa görünür) | tek satır metin |
| `menu` | Menü Öğeleri | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `all_products` | Açılır Menüde "Tüm Ürünler" Metni | tek satır metin |
| `phone_label` | Telefon Düğmesi Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `whatsapp_label` | WhatsApp Düğmesi Metni | tek satır metin |
| `whatsapp_url` | WhatsApp Bağlantısı | bağlantı |

**Alt Bilgi** — `global / footer`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `logo_image` | Koyu Zemin Logosu | görsel (medya kaydı + alt metin) |
| `tagline` | Alt Bilgi Metni | çok satırlı metin |
| `pages_title` | Sayfalar Başlığı | tek satır metin |
| `products_title` | Ürünler Başlığı | tek satır metin |
| `contact_title` | İletişim Başlığı | tek satır metin |
| `phone_label` | Sabit Telefon | tek satır metin |
| `phone_url` | Sabit Telefon Bağlantısı | bağlantı |
| `mobile_label` | Cep Telefonu | tek satır metin |
| `mobile_url` | Cep Telefonu Bağlantısı | bağlantı |
| `email_label` | E-posta | tek satır metin |
| `email_url` | E-posta Bağlantısı | bağlantı |
| `address` | Adres | çok satırlı metin |
| `external_label` | Kardeş Site Bağlantı Metni (boşsa gizlenir) | tek satır metin |
| `external_url` | Kardeş Site Adresi | bağlantı |
| `copyright` | Telif Satırı | tek satır metin |

### Sayfa: Ana Sayfa  `home`

Sıralanabilir bölümler: `products`, `process`, `export`, `blog`, `quote` (header ve footer sabittir).

**Giriş (Hero)** — `home / hero`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | çok satırlı metin |
| `lead` | Alt Metin | çok satırlı metin |
| `primary_label` | Birinci Düğme Metni | tek satır metin |
| `primary_url` | Birinci Düğme Adresi | bağlantı |
| `secondary_label` | İkinci Düğme Metni | tek satır metin |
| `secondary_url` | İkinci Düğme Adresi | bağlantı |
| `bg_image` | Arka Plan Fotoğrafı (boşsa düz indigo) | görsel (medya kaydı + alt metin) |
| `bg_note` | Arka Plan Fotoğrafı Notu (örnek görselse "Örnek görsel" yazın) | tek satır metin |
| `sheet_image` | Föy Görseli | görsel (medya kaydı + alt metin) |
| `dim_x` | Yatay Ölçü Yazısı | tek satır metin |
| `dim_y` | Dikey Ölçü Yazısı | tek satır metin |
| `sheet_rows` | Föy Bilgi Satırları | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Bilgi Şeridi** — `home / facts`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `items` | Bilgiler | tekrarlı satırlar → `value` (tek satır metin), `label` (tek satır metin) |

**Ürün Çeşitlerimiz** — `home / products`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `text` | Bölüm Metni | çok satırlı metin |
| `link_label` | Bağlantı Metni | tek satır metin |

**Üretim Süreci** — `home / process`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `lead` | Bölüm Metni | çok satırlı metin |
| `steps` | Adımlar | tekrarlı satırlar → `title` (tek satır metin), `text` (çok satırlı metin) |

**İhracat Bandı** — `home / export`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `image` | Fotoğraf | görsel (medya kaydı + alt metin) |
| `button_label` | Düğme Metni | tek satır metin |
| `button_url` | Düğme Adresi | bağlantı |

**Blogdan Yazılar** — `home / blog`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `text` | Bölüm Metni | çok satırlı metin |
| `link_label` | Bağlantı Metni | tek satır metin |

**Teklif Bölümü** — `home / quote`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |

**Arama ve Paylaşım** — `home / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Hakkımızda  `about`

**Sayfa Başlığı** — `about / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `image` | Fotoğraf | görsel (medya kaydı + alt metin) |

**Firma Metni** — `about / story`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `p1` | Birinci Paragraf | çok satırlı metin |
| `p2` | İkinci Paragraf | çok satırlı metin |
| `p3` | Üçüncü Paragraf | çok satırlı metin |

**Çalışma İlkeleri** — `about / values`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `image` | Fotoğraf | görsel (medya kaydı + alt metin) |
| `items` | İlkeler | tekrarlı satırlar → `title` (tek satır metin), `text` (çok satırlı metin) |

**Teklif Şeridi** — `about / cta`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `button_label` | Düğme Metni | tek satır metin |
| `button_url` | Düğme Adresi | bağlantı |

**Arama ve Paylaşım** — `about / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürünlerimiz  `products`

**Sayfa Başlığı** — `products / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Ürün Sayfalarının Ortak Metinleri** — `products / shared`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `detail_label` | İncele Düğmesi | tek satır metin |
| `quote_label` | Teklif Düğmesi | tek satır metin |
| `quote_url` | Teklif Düğmesi Adresi | bağlantı |
| `specs_title` | Şartname Başlığı | tek satır metin |
| `body_title` | Açıklama Başlığı | tek satır metin |
| `related_title` | Diğer Ürünler Başlığı | tek satır metin |
| `call_note` | Telefon Notu | tek satır metin |

**Özel Ölçü Şeridi** — `products / custom`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Düğme Metni | tek satır metin |
| `button_url` | Düğme Adresi | bağlantı |

**Arama ve Paylaşım** — `products / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Ahşap Palet  `urun_ahsap_palet`

**Ürün Kartı (listelerde görünür)** — `urun_ahsap_palet / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `size` | Ölçü Satırı | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_ahsap_palet / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_ahsap_palet / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_ahsap_palet / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Euro Palet  `urun_euro_palet`

**Ürün Kartı (listelerde görünür)** — `urun_euro_palet / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `size` | Ölçü Satırı | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_euro_palet / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_euro_palet / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_euro_palet / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Ahşap Kafes  `urun_ahsap_kafes`

**Ürün Kartı (listelerde görünür)** — `urun_ahsap_kafes / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `size` | Ölçü Satırı | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_ahsap_kafes / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_ahsap_kafes / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_ahsap_kafes / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Ahşap Sandık  `urun_ahsap_sandik`

**Ürün Kartı (listelerde görünür)** — `urun_ahsap_sandik / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `size` | Ölçü Satırı | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_ahsap_sandik / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_ahsap_sandik / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_ahsap_sandik / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: İkinci El Palet  `urun_ikinci_el_palet`

**Ürün Kartı (listelerde görünür)** — `urun_ikinci_el_palet / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `size` | Ölçü Satırı | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_ikinci_el_palet / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_ikinci_el_palet / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_ikinci_el_palet / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Blog  `blog`

**Sayfa Başlığı** — `blog / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `read_label` | Devamını Oku Metni | tek satır metin |
| `back_label` | Bloga Dön Metni | tek satır metin |

**Yazı Sayfası Yan Sütunu** — `blog / aside`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `button_label` | Düğme Metni | tek satır metin |
| `button_url` | Düğme Adresi | bağlantı |

**Arama ve Paylaşım** — `blog / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: İletişim  `contact`

**Sayfa Başlığı** — `contact / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**İletişim Bilgileri** — `contact / details`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `phone_title` | Sabit Hat Başlığı | tek satır metin |
| `phone_label` | Sabit Hat | tek satır metin |
| `phone_url` | Sabit Hat Bağlantısı | bağlantı |
| `mobile_title` | GSM Başlığı | tek satır metin |
| `mobile_label` | GSM | tek satır metin |
| `mobile_url` | GSM Bağlantısı | bağlantı |
| `whatsapp_title` | WhatsApp Başlığı | tek satır metin |
| `whatsapp_label` | WhatsApp Metni | tek satır metin |
| `whatsapp_url` | WhatsApp Bağlantısı | bağlantı |
| `email_title` | E-posta Başlığı | tek satır metin |
| `email_label` | E-posta | tek satır metin |
| `email_url` | E-posta Bağlantısı | bağlantı |
| `address_title` | Adres Başlığı | tek satır metin |
| `address` | Adres | çok satırlı metin |
| `map_label` | Yol Tarifi Metni | tek satır metin |
| `map_url` | Harita Bağlantısı | bağlantı |
| `hours_title` | Çalışma Saatleri Başlığı | tek satır metin |
| `hours` | Çalışma Saatleri (boşsa gizlenir) | tek satır metin |

**Teklif Formu** — `contact / form`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Form Başlığı | tek satır metin |
| `note` | Form Açıklaması | çok satırlı metin |
| `name_label` | Ad Soyad Etiketi | tek satır metin |
| `company_label` | Firma Etiketi | tek satır metin |
| `email_label` | E-posta Etiketi | tek satır metin |
| `phone_label` | Telefon Etiketi | tek satır metin |
| `product_label` | Ürün Etiketi | tek satır metin |
| `size_label` | Ölçü Etiketi | tek satır metin |
| `size_hint` | Ölçü Alanı İpucu | tek satır metin |
| `message_label` | Mesaj Etiketi | tek satır metin |
| `submit_label` | Gönder Düğmesi Metni | tek satır metin |
| `privacy_note` | Gizlilik Notu | tek satır metin |
| `success_title` | Başarı Başlığı | tek satır metin |
| `success_text` | Başarı Metni | çok satırlı metin |

**Arama ve Paylaşım** — `contact / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Arama ve Paylaşım: Site Geneli  `site_seo`

**Firma Bilgisi** — `site_seo / org`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Firma adı | tek satır metin |
| `legal_name` | Resmî unvan | tek satır metin |
| `description` | Firma tanımı | çok satırlı metin |
| `logo` | Logo | görsel (medya kaydı + alt metin) |
| `phone` | Telefon | tek satır metin |
| `email` | E-posta | tek satır metin |
| `street` | Açık adres | tek satır metin |
| `district` | İlçe | tek satır metin |
| `city` | İl | tek satır metin |
| `postal_code` | Posta kodu | tek satır metin |
| `country` | Ülke kodu | tek satır metin |
| `latitude` | Enlem | tek satır metin |
| `longitude` | Boylam | tek satır metin |
| `same_as` | Firmanın diğer adresleri | tekrarlı satırlar → `url` (bağlantı) |
| `parent_name` | Bağlı olduğu grup | tek satır metin |
| `parent_url` | Grubun web adresi | bağlantı |

**Varsayılanlar** — `site_seo / defaults`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `share_image` | Varsayılan paylaşım görseli | görsel (medya kaydı + alt metin) |

_Toplam düzenlenebilir alan (tekrarlı satır alanları dahil): 232._


## Koçist · ithalkeresteci.com (`ithalkeresteci`)

### Sayfa: Tüm Sayfalar (Üst Menü, Alt Bilgi)  `global`

**Üst Menü** — `global / header`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `logo_image` | Logo görseli (boşsa çam ağaçlı yazı logosu kullanılır) | görsel (medya kaydı + alt metin) |
| `logo_word` | Logo: üst satır | tek satır metin |
| `logo_rest` | Logo: alt satır | tek satır metin |
| `menu` | Menü Öğeleri | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `phone_label` | Telefon Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `phone_note` | Telefon Kutusu Üst Yazısı | tek satır metin |
| `whatsapp_url` | WhatsApp Bağlantısı (boşsa düğme gizlenir) | bağlantı |
| `whatsapp_label` | WhatsApp Düğmesi Metni | tek satır metin |
| `cta_label` | Teklif Düğmesi Metni | tek satır metin |

**Alt Bilgi** — `global / footer`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `tagline` | Alt Bilgi Metni | çok satırlı metin |
| `hours` | Çalışma Saatleri | tek satır metin |
| `email` | E-posta | tek satır metin |
| `address` | Adres | çok satırlı metin |
| `family` | Kardeş Siteler | tekrarlı satırlar → `label` (tek satır metin), `note` (tek satır metin), `url` (bağlantı) |
| `copyright` | Telif Satırı | tek satır metin |

### Sayfa: Ana Sayfa  `home`

Sıralanabilir bölümler: `products`, `supply`, `cousin`, `faq`, `quote` (header ve footer sabittir).

**Giriş (Hero)** — `home / hero`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | çok satırlı metin |
| `lead` | Alt Metin | çok satırlı metin |
| `image` | Fotoğraf | görsel (medya kaydı + alt metin) |
| `image_note` | Fotoğraf Notu (gerçek fotoğraf yüklenince silin) | tek satır metin |
| `secondary` | İkinci Düğme Metni | tek satır metin |

**Kereste Çeşitleri** — `home / products`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `text` | Bölüm Metni | çok satırlı metin |
| `link_label` | Bağlantı Metni | tek satır metin |

**Toptan Tedarik** — `home / supply`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `points` | Öne Çıkanlar | tekrarlı satırlar → `value` (tek satır metin), `label` (tek satır metin) |
| `photos` | Fotoğraflar | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Kuzen Site Köprüsü** — `home / cousin`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Düğme Metni | tek satır metin |
| `button_url` | Düğme Adresi | bağlantı |

**Sık Sorulanlardan Seçme** — `home / faq`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `count` | Kaç soru gösterilsin | tek satır metin |
| `link_label` | Bağlantı Metni | tek satır metin |

**Teklif Şeridi** — `home / quote`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Düğme Metni | tek satır metin |

**Arama ve Paylaşım** — `home / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürünler  `products`

**Sayfa Başlığı** — `products / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Ürün Sayfalarının Ortak Metinleri** — `products / shared`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `detail_label` | İncele Düğmesi | tek satır metin |
| `quote_label` | Teklif Düğmesi | tek satır metin |
| `specs_title` | Şartname Başlığı | tek satır metin |
| `related_title` | Diğer Ürünler Başlığı | tek satır metin |

**Arama ve Paylaşım** — `products / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: İthal Kereste  `urun_ithal_kereste`

**Ürün Kartı (listelerde görünür)** — `urun_ithal_kereste / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_ithal_kereste / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_ithal_kereste / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_ithal_kereste / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: İnşaatlık ve Çatılık Kereste  `urun_insaatlik`

**Ürün Kartı (listelerde görünür)** — `urun_insaatlik / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_insaatlik / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_insaatlik / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_insaatlik / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Kalas  `urun_kalas`

**Ürün Kartı (listelerde görünür)** — `urun_kalas / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_kalas / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_kalas / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_kalas / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Plywood (Kontrplak)  `urun_plywood`

**Ürün Kartı (listelerde görünür)** — `urun_plywood / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_plywood / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_plywood / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_plywood / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Sık Sorulan Sorular  `faq`

**Sayfa Başlığı** — `faq / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Sorular** — `faq / items`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Soru ve Cevaplar | tekrarlı satırlar → `question` (tek satır metin), `answer` (çok satırlı metin) |

**Arama ve Paylaşım** — `faq / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Hakkımızda  `about`

**Sayfa Başlığı** — `about / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Firma Metni** — `about / story`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `image` | Fotoğraf | görsel (medya kaydı + alt metin) |
| `p1` | Birinci Paragraf | çok satırlı metin |
| `p2` | İkinci Paragraf | çok satırlı metin |

**İlkeler** — `about / values`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | İlkeler | tekrarlı satırlar → `title` (tek satır metin), `text` (çok satırlı metin) |

**Arama ve Paylaşım** — `about / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: İletişim  `contact`

**Sayfa Başlığı** — `contact / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**İletişim Bilgileri** — `contact / details`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `phone_label` | Telefon | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `email` | E-posta | tek satır metin |
| `address` | Adres | çok satırlı metin |
| `hours` | Çalışma Saatleri | tek satır metin |
| `map_label` | Yol Tarifi Metni | tek satır metin |
| `map_url` | Harita Bağlantısı | bağlantı |

**Teklif Formu** — `contact / form`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Form Başlığı | tek satır metin |
| `note` | Form Açıklaması | çok satırlı metin |
| `size_hint` | Ölçü Alanı İpucu | tek satır metin |
| `submit_label` | Gönder Düğmesi | tek satır metin |
| `privacy_note` | Gizlilik Notu | tek satır metin |
| `success_title` | Başarı Başlığı | tek satır metin |
| `success_text` | Başarı Metni | çok satırlı metin |

**Arama ve Paylaşım** — `contact / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Arama ve Paylaşım: Site Geneli  `site_seo`

**Firma Bilgisi** — `site_seo / org`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Firma adı | tek satır metin |
| `legal_name` | Resmî unvan | tek satır metin |
| `description` | Firma tanımı | çok satırlı metin |
| `logo` | Logo | görsel (medya kaydı + alt metin) |
| `phone` | Telefon | tek satır metin |
| `email` | E-posta | tek satır metin |
| `street` | Açık adres | tek satır metin |
| `district` | İlçe | tek satır metin |
| `city` | İl | tek satır metin |
| `postal_code` | Posta kodu | tek satır metin |
| `country` | Ülke kodu | tek satır metin |
| `latitude` | Enlem | tek satır metin |
| `longitude` | Boylam | tek satır metin |
| `same_as` | Firmanın diğer adresleri | tekrarlı satırlar → `url` (bağlantı) |
| `parent_name` | Bağlı olduğu grup | tek satır metin |
| `parent_url` | Grubun web adresi | bağlantı |

**Varsayılanlar** — `site_seo / defaults`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `share_image` | Varsayılan paylaşım görseli | görsel (medya kaydı + alt metin) |

_Toplam düzenlenebilir alan (tekrarlı satır alanları dahil): 170._


## Koçist · kavakkeresteci.com (`kavakkeresteci`)

### Sayfa: Tüm Sayfalar (Üst Menü, Alt Bilgi)  `global`

**Üst Menü** — `global / header`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `logo_image` | Logo görseli (boşsa çam ağaçlı yazı logosu kullanılır) | görsel (medya kaydı + alt metin) |
| `logo_word` | Logo: üst satır | tek satır metin |
| `logo_rest` | Logo: alt satır | tek satır metin |
| `menu` | Menü Öğeleri | tekrarlı satırlar → `label` (tek satır metin), `url` (bağlantı) |
| `phone_label` | Telefon Metni | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `phone_note` | Telefon Kutusu Üst Yazısı | tek satır metin |
| `whatsapp_url` | WhatsApp Bağlantısı (boşsa düğme gizlenir) | bağlantı |
| `whatsapp_label` | WhatsApp Düğmesi Metni | tek satır metin |
| `cta_label` | Teklif Düğmesi Metni | tek satır metin |

**Alt Bilgi** — `global / footer`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `tagline` | Alt Bilgi Metni | çok satırlı metin |
| `hours` | Çalışma Saatleri | tek satır metin |
| `email` | E-posta | tek satır metin |
| `address` | Adres | çok satırlı metin |
| `family` | Kardeş Siteler | tekrarlı satırlar → `label` (tek satır metin), `note` (tek satır metin), `url` (bağlantı) |
| `copyright` | Telif Satırı | tek satır metin |

### Sayfa: Ana Sayfa  `home`

Sıralanabilir bölümler: `products`, `uses`, `cousin`, `faq`, `quote` (header ve footer sabittir).

**Giriş (Hero)** — `home / hero`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | çok satırlı metin |
| `lead` | Alt Metin | çok satırlı metin |
| `image` | Fotoğraf | görsel (medya kaydı + alt metin) |
| `image_note` | Fotoğraf Notu (gerçek fotoğraf yüklenince silin) | tek satır metin |
| `secondary` | İkinci Düğme Metni | tek satır metin |

**Kereste Çeşitleri** — `home / products`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `text` | Bölüm Metni | çok satırlı metin |
| `link_label` | Bağlantı Metni | tek satır metin |

**Kullanım Alanları** — `home / uses`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `items` | Alanlar | tekrarlı satırlar → `title` (tek satır metin), `text` (tek satır metin), `image` (görsel (medya kaydı + alt metin)) |

**Kuzen Site Köprüsü** — `home / cousin`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Düğme Metni | tek satır metin |
| `button_url` | Düğme Adresi | bağlantı |

**Sık Sorulanlardan Seçme** — `home / faq`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Bölüm Başlığı | tek satır metin |
| `count` | Kaç soru gösterilsin | tek satır metin |
| `link_label` | Bağlantı Metni | tek satır metin |

**Teklif Şeridi** — `home / quote`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `text` | Metin | çok satırlı metin |
| `button_label` | Düğme Metni | tek satır metin |

**Arama ve Paylaşım** — `home / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürünler  `products`

**Sayfa Başlığı** — `products / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Ürün Sayfalarının Ortak Metinleri** — `products / shared`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `detail_label` | İncele Düğmesi | tek satır metin |
| `quote_label` | Teklif Düğmesi | tek satır metin |
| `specs_title` | Şartname Başlığı | tek satır metin |
| `related_title` | Diğer Ürünler Başlığı | tek satır metin |

**Arama ve Paylaşım** — `products / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Kavak Kereste  `urun_kavak`

**Ürün Kartı (listelerde görünür)** — `urun_kavak / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_kavak / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_kavak / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_kavak / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Çıta  `urun_cita`

**Ürün Kartı (listelerde görünür)** — `urun_cita / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_cita / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_cita / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_cita / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: Ahşap Takoz  `urun_takoz`

**Ürün Kartı (listelerde görünür)** — `urun_takoz / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_takoz / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_takoz / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_takoz / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Ürün: OSB Levha  `urun_osb`

**Ürün Kartı (listelerde görünür)** — `urun_osb / card`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Ürün Adı | tek satır metin |
| `short` | Kısa Açıklama | çok satırlı metin |
| `mark` | Etiket Yazısı (şablon harf; ölçü ya da tür) | tek satır metin |
| `image` | Kart Görseli | görsel (medya kaydı + alt metin) |

**Ürün Sayfası** — `urun_osb / detail`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |
| `body` | Ürün Açıklaması | çok satırlı metin |
| `gallery` | Galeri | tekrarlı satırlar → `image` (görsel (medya kaydı + alt metin)) |

**Şartname Tablosu** — `urun_osb / specs`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Satırlar | tekrarlı satırlar → `label` (tek satır metin), `value` (tek satır metin) |

**Arama ve Paylaşım** — `urun_osb / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Sık Sorulan Sorular  `faq`

**Sayfa Başlığı** — `faq / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Sorular** — `faq / items`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | Soru ve Cevaplar | tekrarlı satırlar → `question` (tek satır metin), `answer` (çok satırlı metin) |

**Arama ve Paylaşım** — `faq / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Hakkımızda  `about`

**Sayfa Başlığı** — `about / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**Firma Metni** — `about / story`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `image` | Fotoğraf | görsel (medya kaydı + alt metin) |
| `p1` | Birinci Paragraf | çok satırlı metin |
| `p2` | İkinci Paragraf | çok satırlı metin |

**İlkeler** — `about / values`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `rows` | İlkeler | tekrarlı satırlar → `title` (tek satır metin), `text` (çok satırlı metin) |

**Arama ve Paylaşım** — `about / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: İletişim  `contact`

**Sayfa Başlığı** — `contact / head`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Başlık | tek satır metin |
| `lead` | Öne Çıkan Cümle | çok satırlı metin |

**İletişim Bilgileri** — `contact / details`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `phone_label` | Telefon | tek satır metin |
| `phone_url` | Telefon Bağlantısı | bağlantı |
| `email` | E-posta | tek satır metin |
| `address` | Adres | çok satırlı metin |
| `hours` | Çalışma Saatleri | tek satır metin |
| `map_label` | Yol Tarifi Metni | tek satır metin |
| `map_url` | Harita Bağlantısı | bağlantı |

**Teklif Formu** — `contact / form`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Form Başlığı | tek satır metin |
| `note` | Form Açıklaması | çok satırlı metin |
| `size_hint` | Ölçü Alanı İpucu | tek satır metin |
| `submit_label` | Gönder Düğmesi | tek satır metin |
| `privacy_note` | Gizlilik Notu | tek satır metin |
| `success_title` | Başarı Başlığı | tek satır metin |
| `success_text` | Başarı Metni | çok satırlı metin |

**Arama ve Paylaşım** — `contact / seo`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `title` | Arama başlığı | tek satır metin |
| `description` | Arama açıklaması | çok satırlı metin |
| `image` | Paylaşım görseli | görsel (medya kaydı + alt metin) |

### Sayfa: Arama ve Paylaşım: Site Geneli  `site_seo`

**Firma Bilgisi** — `site_seo / org`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `name` | Firma adı | tek satır metin |
| `legal_name` | Resmî unvan | tek satır metin |
| `description` | Firma tanımı | çok satırlı metin |
| `logo` | Logo | görsel (medya kaydı + alt metin) |
| `phone` | Telefon | tek satır metin |
| `email` | E-posta | tek satır metin |
| `street` | Açık adres | tek satır metin |
| `district` | İlçe | tek satır metin |
| `city` | İl | tek satır metin |
| `postal_code` | Posta kodu | tek satır metin |
| `country` | Ülke kodu | tek satır metin |
| `latitude` | Enlem | tek satır metin |
| `longitude` | Boylam | tek satır metin |
| `same_as` | Firmanın diğer adresleri | tekrarlı satırlar → `url` (bağlantı) |
| `parent_name` | Bağlı olduğu grup | tek satır metin |
| `parent_url` | Grubun web adresi | bağlantı |

**Varsayılanlar** — `site_seo / defaults`

| Alan anahtarı | Türkçe etiket | Tür |
| --- | --- | --- |
| `share_image` | Varsayılan paylaşım görseli | görsel (medya kaydı + alt metin) |

_Toplam düzenlenebilir alan (tekrarlı satır alanları dahil): 169._


