# Claude Code'a verilecek görev: iki farklı WordPress sitesi, tek görsel içerik paneli

Bu dosyanın tamamını Claude Code'a gönder. Önce gereksinim kontrolünü yapıp gerekirse benden kurulum bekle. Her MVP bittiğinde dur ve çıktıyı bana göster; açık onayım olmadan sonraki MVP'ye geçme.

## Proje amacı

İki **tasarım açısından bağımsız** WordPress sitesi ve bu iki sitenin içeriklerini değiştiren **tek merkezî yönetim paneli** ile yerel bir demo yapıyoruz. Başlangıç örnekleri `kocist.com.tr` ve `istanbulpaletci.com`; bunlar şimdilik bilgisayarımdaki yerel Multisite alt siteleri olacak. Gerçek domainleri, DNS'i ve canlı siteleri değiştirme.

Bu siteler e-ticaret uygulaması değil. Sepet, stok, sipariş, havale/EFT ödeme akışı, müşteri hesabı, özel satış API'si ve WooCommerce geliştirme. WordPress'i yalnızca siteyi çalıştırmak, admin girişini ve içerik/görsel kaydını sağlamak için kullan. Yani iş tarafında özel bir backend istemiyorum; WordPress'in kendisi ve küçük içerik yönetimi eklentisi teknik olarak sunucu tarafı gerektirir. Ürün görünümü gerekiyorsa düzenlenebilir vitrin kartları veya basit içerik kayıtları yeterli.

Koçist sitesinin daha geniş kataloğunu demoda bütünüyle kopyalama. İki sitede de ana sayfa, örnek alt sayfa ve üst menü/footer yeterli. Tasarımları mevcut siteleri birebir klonlamadan, birbirinden görsel olarak açıkça farklı yap. Farklı sayfa düzenleri ve farklı bileşenler kullanabilirsin; ikisini tek bir tema varyantına zorlama.

Başlamadan önce iki mevcut sitenin ulaşılabilir sayfalarını kısaca incele ve **demo kapsamındaki sayfalar ile görünür içerik alanlarının envanterini** `CONTENT-INVENTORY.md` içinde çıkar. Erişemediğin sayfaları veya elimizde olmayan marka görsellerini tahmin edip gerçek içerikmiş gibi sunma; bunlar için açıkça örnek içerik/görsel kullan ve benden gerçek varlıkları daha sonra iste. Fotoğraf ve logoları yalnızca kullanım hakkımız olan kaynaklardan al. Demo sitelerini henüz mevcut alan adlarına bağlama.

## Kritik mimari

1. Yerelde **tek WordPress Multisite ağı**, altında iki ayrı site kur. Geliştirmede aynı origin üzerinde path tabanlı adresler tercih et (örneğin `/kocist/`, `/paletci/`); mevcut kurulum engel çıkarırsa nedeni ve alternatifini bana bildir. İleride ayrı alan adlarına eşlenebilir şekilde kur.
2. Her sitenin **ayrı teması** olsun. `kocist-theme` ve `paletci-theme` birbirinden farklı component düzeni, CSS ve görsel dil içersin. Gerekli küçük yardımcılar paylaşılabilir, fakat site düzenlerini zorla ortaklaştırma.
3. `network-content-studio` adlı **tek, ağ genelinde etkin içerik eklentisi** oluştur. Tek giriş noktası WordPress Network Admin içindeki “İçerik Stüdyosu” olsun. Panelin **sağında dikey site seçici** bulunmalı; seçili sitenin sayfaları ve o sayfaya ait bileşenler kendi Türkçe adlarıyla listelenmeli.
4. Merkez panelin veri katmanı siteye göre ayrılmalı. Metin, görsel, bağlantı ve ikon değişiklikleri yalnızca seçili alt siteye yazılmalı. Ağ panelinden alt siteye yazarken yetki kontrolü, nonce, girdi doğrulama/çıktı escaping ve site bağlamını geri yüklemeyi uygula. Giriş yalnızca WordPress yönetici hesabıyla yapılır; ayrı kullanıcı/kayıt sistemi kurma.
5. Her sitede düzenlenebilecek alanları açıkça tanımlayan bir **siteye özgü alan manifesti** kullan: sayfa -> bileşen -> alan adı, Türkçe etiketi, türü (`text`, `textarea`, `image`, `icon`, `url`, `repeater` gibi). Merkez panel seçili sitenin manifestinden alanları üretmeli. Her tema kendi tasarımını çizerken aynı kayıtlı alan değerlerini göstermeli. Multisite'da yalnızca `switch_to_blog()` çağırmanın başka sitenin tema kodunu otomatik yüklediğini varsayma; manifest keşfini buna güvenmeden tasarla.
6. Düzenlenen gerçek değerleri siteye ait WordPress verisinde tut. Hardcoded PHP/CSS metinlerini değiştirmeyi editör sayma. Değerler sayfa yenilenince ve sunucu yeniden başlayınca korunmalı. Medya WordPress medya kayıtlarında tutulmalı; görsellerde alt metin değiştirilebilmeli. İkonlar güvenilir sınırlı ikon listesinden seçilebilmeli. Yönetilen içerik için panel tek açık yazma yolu olsun; ikinci bağımsız bir düzenleme yolu üretip çakışma yaratma.
7. Panelin hedefi: **sağda site seçici, ortada seçili sayfanın çalışan önizlemesi, seçilen bileşen/alan için düzenleyici panel.** Aynı origin üzerindeki yerel önizlemede güvenli, yalnızca yöneticiye açık `postMessage`/işaretli alanlarla tıklayıp seçme mümkünse uygula. Önizlemenin içine WordPress admin araçları veya gizli veriler sızmamalı. Bileşeni tıklamak düzenleyiciyi açmalı; kaydetme sonrası ilgili önizleme yenilenmeli veya canlı güncellenmeli. Eğer gerçek önizlemeye gömülü tıklama uygulanamazsa sebebini belgeleyip aynı panelde önizleme + basit alan formu kullan. **MCP önizlemenin teknik şartı değildir.**
8. Claude Code'un `/design` özelliği varsa yalnızca iki sitenin ve panelin tasarımını keşfetmek/üretmek için kullanabilirsin. `/design` komutunu WordPress'e gömülü bir editör sanma. Panelde doğal dille “hero'yu değiştir” sohbeti, model API anahtarı veya ayrı AI servisi bu demonun gereği değildir.

## İçerik kapsamı ve kaydetme davranışı

- `CONTENT-INVENTORY.md` içinde demo olarak çizdiğin **her görünür alanın** sayfa, bileşen ve alan anahtarını listele: masaüstü/mobil menü metinleri, logo, hero, başlıklar, paragraflar, kartlar, buton metinleri ve hedefleri, görseller/alt metinleri, ikonlar, iletişim bilgileri ve footer. Bir alanın gerçekten sabit kalması gerekiyorsa nedenini yaz. Böylece “baştan sona düzenlenebilir” kabulü ölçülebilir olsun.
- Kaydetme davranışı demoda **hemen yayınla** olsun; panelde bunu açıkça göster. Düzenleme formundan çıkmadan önce `Vazgeç` ile kaydedilmemiş değişikliklerden dönebilmeliyim. Daha gelişmiş taslak/revizyon akışını bu demoya ekleme; README'de sonraki aşama olarak belirt.
- İletişim/teklif formu gösteriyorsan **demo formu** olarak işaretle ve gerçekten teslim edilmeyen mesaj için “gönderildi” başarısı gösterme. Gerçek e-posta teslimi, spam koruması ve bildirimler canlıya geçişte ayrı iş olur. Telefon, WhatsApp ve CTA bağlantıları geçerli ve siteye özgü olsun; örnek numara kullanıyorsan açıkça örnek olarak belirt.
- Sıralama serbest CSS düzenleme anlamına gelmez. Her site kendi izin verdiği **ana sayfa bölümlerini** ve menü öğelerini yukarı/aşağı taşıyabilir; alan manifesti hangi parçaların taşınabileceğini açıkça söylesin. Kaydedilen sıra sayfa yenilenince ve sunucu yeniden başlatılınca korunsun.

## Önce bilgisayarımı kontrol et — kurulum kapısı

Ben Windows kullanıyorum; bilgisayarımda WordPress kurulu olmayabilir. Kod yazmaya başlamadan önce mevcut ortamı kısa komutlarla kontrol et: `claude`, Git, Docker Desktop/Compose veya önerdiğin yerel WordPress alternatifi, kullanılabilir portlar ve gerekiyorsa Node/WP-CLI. Şifreleri, tokenları veya diğer gizli değerleri çıktı olarak gösterme.

En az kurulum gerektiren, **tekrarlanabilir WordPress Multisite** yöntemini seç; varsayılan öneri Docker Desktop + Compose + WordPress + veritabanı + gerektiğinde WP-CLI. Docker Desktop kurulu ve çalışırsa devam et. Büyük yazılımları, sistem servislerini veya alternatif araçları kendi kendine indirmeye çalışma. Bir eksik varsa:

- Tam olarak neyin eksik olduğunu ve niçin gerektiğini bir paragrafta söyle.
- Resmî indirme sayfasını ve benim yapacağım kısa kurulum/doğrulama adımlarını ver.
- **Dur ve benim “kurdum, devam et” yanıtımı bekle.** Bu bekleme sırasında varsayımla sistem kurulumunu sürdürme.
- Zorunlu olmayan araçları gerekçe göstermeden kurdurma.

Kurulum tamamlanınca projeyi repo içinde tekrarlanabilir kurulum komutlarıyla hazırla. WordPress core'unu Git'e kopyalama. Ortam değişkenleri için örnek dosya ver, gerçek şifreleri versiyon kontrolüne alma. İlk kurulumda ana WordPress sitesi ağ yönetimi için kullanılabilir; iki demo site ayrıca oluşturulmalı. Her siteye doğru temayı otomatik ata ve demo içeriğini tekrar çalıştırılabilir seed komutuyla üret.

## Üç MVP ve durma noktaları

### MVP 1 — İki gerçek ve farklı yerel site

- Multisite ağı, iki alt site, iki ayrı tema ve örnek içerik çalışsın.
- Koçist için daha kurumsal/endüstriyel bir ana sayfa, Paletçi için farklı tipografi, hiyerarşi ve yerleşime sahip ürün/teklif odaklı bir ana sayfa üret. Her ikisinde üst menü, hero, özgün en az iki içerik bileşeni ve footer olsun.
- İki yerel adres, Network Admin giriş adresi ve tekrarlanabilir çalıştırma komutlarını README'ye yaz.
- `CONTENT-INVENTORY.md` dosyasını seçilen iki örnek sayfanın görünür alanlarıyla tamamla; örnek görsel ve metinleri gerçek marka varlığı gibi sunma.
- Ekranda gerçekten göründüklerini kontrol et; tarayıcı/screenshot aracı varsa kullan. Yoksa benden hangi URL'leri açıp neyi kontrol etmemi istediğini açıkça belirt.

**Kabul:** İki ayrı tema ve iki bağımsız site, ortak Multisite ağı altında açılıyor. **Burada dur; benden geri bildirim iste.**

### MVP 2 — Tek panelde basit ama eksiksiz içerik düzenleme

- Network Admin'de paneli aç. Sağ dikey siteden site seç, sayfa ve kendi adlarıyla bileşenleri gör.
- İki sitede de üst menüden footera kadar demoda görünen **bütün yazılar ve görseller**, bağlantı metinleri/hedefleri, görünür ikonlar panelden değiştirilebilsin. Statik yasal/metinsel olmayan teknik UI parçaları hariç içerik dosyada gömülü kalmasın.
- Görsel ekle/değiştir/kaldır, alt metin gir; ikon seç/değiştir; kart gibi tekrarlı bileşenlerde satır ekle/sil. Basit önizleme bağlantısı veya panel içinde önizleme sağla.
- Menü öğelerinin sırasını değiştir. Panelde hemen yayınla ve kaydetmeden vazgeç davranışlarını açıkça göster; varsa demo formunun teslim etmediği mesaj için sahte başarı durumu üretme.
- Değişiklikleri seçili alt siteye kaydet; diğer site etkilenmesin. Alanları kaydetmek ve sonra sayfayı yenileyip görmek yeterli; karmaşık sürükle bırak şart değil.

**Kabul:** Koçist'in hero yazısı/görseli ve footer metni değişince yalnız Koçist değişir; Paletçi'nin menü öğesi/ikon/kartı değişince yalnız Paletçi değişir. Kaydedilen içerik yeniden başlatmadan sonra da durur. **Burada dur; benden geri bildirim iste.**

### MVP 3 — Görsel editör ve Claude Code ↔ WordPress MCP

- Panelin orta alanında seçili sitenin **çalışan sayfa önizlemesini** göster. Önizlemede işaretli bir metne/görsele/bileşene tıklayınca ilgili basit düzenleme alanı açılsın; kaydet ve gör akışı çalışsın. Sağ dikey site seçici sabit kalsın. Aynı origin içinde çalışmıyorsa açıklanmış fallback olarak önizleme + bileşen listesinden seçim sağla.
- Yerel WordPress'e Claude Code'dan bağlanmak için resmî WordPress MCP Adapter yaklaşımını değerlendir ve kurulumu belgeli, tekrarlanabilir şekilde yap. Eklentinin bütün WordPress işlemlerini otomatik açtığını varsayma: önce mevcut Abilities'i keşfet, bu demo için gereken **en az yetkili** site listeleme, alan okuma ve kontrollü alan güncelleme işlevlerini kendi eklentinde tanımla/erişime aç. İlgili sitenin kimliğini her işlemde açıkça geçir. Mutasyonlar sadece yerel demo siteleri için ve kayıtlı alanlarla sınırlı olsun.
- Claude Code terminalinde doğal dilli bir örnek görev göster: “Paletçi ana sayfasındaki hero başlığını X yap”; MCP aracılığıyla değişsin, ardından admin önizlemesi yeni değeri göstersin. Bu akış **Claude Code içinde** doğal dil kullanır; WordPress paneline Claude sohbet kutusu ekleme.
- İki sitenin ana sayfasındaki manifestte izin verilen bölümleri basit **Yukarı/Aşağı** kontrolüyle sırala. İki site farklı bölümlere sahip olabilir. Header/footer'ın konumunu değiştirme. Değişikliği kaydet, yenile ve yeni sıranın korunduğunu doğrula; sürükle bırak bu demonun şartı değil.

**Kabul:** Aynı panelden site seçimi + çalışan önizleme + tıkla/düzenle/kaydet işlemi gösteriliyor; her iki sitede izin verilen ana sayfa bölümleri sıraya konup yenileme sonrasında korunuyor; Claude Code MCP ile tek bir içerik değişikliğini yapıp sonuç panelde görülebiliyor. MCP kısmı kurulum engeline takılırsa çalışan görsel editörü teslim et, engeli ve gereken manuel adımı bana söyleyip bekle.

## Her aşamadan sonra raporla

Yalnızca tamamladığın MVP'yi raporla: açılacak URL'ler, çalışan özellikler, sınadığın senaryolar, eksik kalanlar ve benden beklediğin tek sonraki karar. Komut çıktısını gereksiz yere uzatma. Sonraki MVP'ye kendiliğinden geçme. Kod eklerken önce ilgili dosyaları incele, küçük değişiklikler yap, çalıştırıp kontrol et; kararları kısa bir `DECISIONS.md` dosyasında tut. Gereksiz MCP sunucuları ve pluginlerle projeyi büyütme.
