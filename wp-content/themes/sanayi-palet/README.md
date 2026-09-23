# Sanayi Palet teması

sanayipalet.com'un (Koçist Grup'un ahşap palet ve sandık markası) tanıtım sitesi.
Ana sayfa, Ürünler, Hakkımızda, Blog ve İletişim sayfaları var. İçerik Network Content
Studio (İçerik Stüdyosu) panelinden düzenlenir.

## Kurulum

1. Ağ Yönetimi'nden yeni bir site açın ve bu temayı etkinleştirin.
2. İlk istekte (ya da yönetim paneline ilk girişte) tema siteyi **bir kez** hazırlar
   (`inc/setup.php`):
   - Sayfaları oluşturur: `anasayfa`, `urunler`, `hakkimizda`, `blog`, `iletisim`.
     Aynı slug'lı bir sayfa varsa ona dokunmaz.
   - Okuma ayarı: statik ön sayfa `anasayfa`, yazılar sayfası `blog`.
   - Kalıcı bağlantılar düz (`?p=`) ise `/%postname%/` yapar. Başka bir yapı seçiliyse
     değiştirmez.
   - Sitede WordPress'in örnek yazısı dışında yazı yoksa üç "palet rehberi" yazısı
     oluşturur. Kapak görselleri temanın `assets/img` klasöründen medya kitaplığına eklenir.
     Örnek yazı taslağa alınır, silinmez.
   - Sürüm `sanayi_palet_setup_version` option'ında tutulur. Kurulum tekrar çalışmaz;
     silinen sayfa ya da yazı geri gelmez.
3. Seed betiği gerekmez. Metinler manifest varsayılanlarından (`content-manifest.php`),
   görseller temanın kendi dosyalarından gelir. Panelden girilen her değer bunların önüne
   geçer.

Eklenti etkin değilse de site aynı içerikle açılır: `functions.php`'deki yedek fonksiyonlar
manifest varsayılanlarını okur. Yalnızca panelden düzenleme olmaz.

## Renkleri değiştirmek

Bütün renkler tek dosyada: `assets/css/tokens.css`. Şablonlarda ve diğer CSS dosyalarında
renk kodu yok.

| Jeton | Değer | İşi |
| --- | --- | --- |
| `--beton` | `#e3e4e0` | sayfa zemini (saha betonu) |
| `--beton-acik` | `#f4f4f1` | yükseltilmiş yüzey, bant |
| `--cam` | `#d9ae6c` | ahşap: damga, yapı çizgileri, adım numaraları, teklif şeridi |
| `--yanik` | `#2b2019` | metin, damga mürekkebi, footer |
| `--toz` | `#6e6660` | ikincil metin |
| `--yesil` | `#3c7f2b` | yalnızca eylem: düğme, bağlantı, odak |

Kural: yeşil tıklanabilir demektir, çam rengi ahşap ya da yapı demektir. Gölge ve degrade yok.

## Tasarım kararları

- **Yakık damga.** İhracat paletine yakılarak basılan ISPM 15 işareti (firmanın gerçek
  belgesi: TR-1080-HT). Sitenin tek gösterişli öğesi; ana sayfada yalnızca hero'da,
  açılışta bir kez "basılır" (`prefers-reduced-motion` açıksa hareketsiz). IPPC başak
  logosu tescilli bir işaret olduğu için çizilmedi.
- **Ölçekli palet çizimi** (Ürünler). Her palet tipinin üstten görünüşü aynı ölçekte
  SVG olarak çizilir (1 birim = 1 cm). Euro 80×120 ile Kapalı 100×120 yan yana gerçek
  farkı gösterir. Ölçüsü bilinmeyen tipler için ölçü uydurulmaz: kesik kenarlı genel
  biçim çizilir, ölçü çizgisi olmaz. Ölçü panelden girilince çizim ölçekli hâle gelir.
- **Tipografi.** Big Shoulders (başlık, menü, düğme), Big Shoulders Stencil (yalnızca
  damga), Instrument Sans (gövde). Hepsi Google Fonts'tan, Türkçe karakterleri tam.
- **İkonlar.** Phosphor (bold, MIT, `assets/icons/LICENSE`). Paneldeki ikon alanları
  eklentinin 16 anahtarını kullanır; `sanayi_palet_icon()` bunları aynı anlamdaki
  Phosphor çizimine eşler.
- **Bilinçli olarak kullanılmayanlar.** Krem zemin, tümü büyük harf etiketler, başlıkta
  tek kelime vurgusu, düğmede "→", gölgeli kart yığını, bölüm bölüm kayan animasyonlar,
  rakam şeridi. Numaralı gösterim yalnızca sipariş sürecinde var, çünkü o gerçek bir sıra.
- **Sizden / Bizden.** Sipariş adımlarında müşteri adımı çam, firma adımı yanık zeminli.
- **Mobil.** Hero'da sıra başlık > açıklama > damga > düğmeler; ana sayfadaki blog
  kartları yatay kaydırılan tek satır.

## İçerik kaynakları

- Metinler sanayipalet.com'dan ve müşterinin verdiği notlardan. Hakkımızda ve palet
  metinleri kelimesi kelimesine; kısa özetler bu metinlerden çıkarıldı.
- Tema için yazılan metinler: hero başlığı ve açıklaması, "Kısaca biz", sipariş süreci,
  belge bölümü başlığı ve açıklaması, teklif şeridi, blog yazılarının gövdesi.
- Mevcut sitede hem "37 yıl" hem "45 yıl" geçiyor; müşteri 45'i seçti.
- Sarf malzemeleri kaldırıldı: ürün listesi yok.
- Operatör belgesi kullanılmadı: kişisel veri taşıyor.
- Fotoğraflar `assets/img` altında. Müşteri kullanımını onayladı; sandık, kafes ve atölye
  fotoğrafları Koçist Grup'un kendi görselleri. En uzun kenar 1600px.

## İletişim formu

Tema içinde. `sp_message` kayıt türü, `admin-post.php` işleyicisi, nonce, zorunlu alan
kontrolü, bot tuzağı ve alan alan hata mesajı var. Mesajlar yönetimde "İletişim Mesajları"
altında görünür. E-posta gönderilmez. Ürün kartlarındaki teklif düğmeleri formu konusu
dolu açar (`/iletisim/?konu=Euro palet`).

Harita anahtarsız Google Maps gömme adresiyle açılır; aranacak adres panelden girilir.

## Bilinen eksikler

- Tam logo dosyası yok; logo yazıyla. Panelden görsel yüklenince o kullanılır.
- CP, Türpal ve İkinci el paletin ölçüleri bilinmiyor.
- Sipariş adımlarının süreleri boş (alan var, boşken gösterilmez).
- Site dili İngilizce kurulduğu için blog tarihleri Türkçe ay adlarıyla elle biçimlenir
  (`sanayi_palet_date()`).
