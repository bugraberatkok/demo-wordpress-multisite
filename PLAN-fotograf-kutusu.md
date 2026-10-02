# Fotoğraf Kutusu ve "Neden görünmüyor?" — Plan (0.23.0)

Fable 5.1 (mimar), 2 Ekim 2026. Onay bekliyor; kod değişmedi. Fikir turu: `PLAN-urun-girisi-fikirler.md` (seçenek B + A, kullanıcı onayı 2 Ekim).

**Kullanıcı kararları (bağlayıcı):** dosya adı ayracı serbest; mevcut galeriye varsayılan "sona ekle", seçenek "galeriyi değiştir"; İçerik Stüdyosu'ndaki ürün satırının istisna formuna dokunulmaz; "Kitaplıktan ekle" açılır listesi yerine arama kutusu.

**Ölçülen gerçek:** WK 55 üründen 54'ünde, Koçist 283 üründen 278'inde fotoğraf yok. Fotoğraf eklemek bugün ürün başına bir form turu (Ürünler → Düzenle → Görseller → Kaydet); "Kitaplıktan ekle" listesi son 200 görselle sınırlı (`nwcs_pool_media()`, products.php:300). "Ürün var ama sitede yok" sorusunun dört nedeni var, hiçbiri tek yerde yazmıyor, düzeltme düğmesi yok.

**İlke:** Excel'in anahtarı ürün kodu; fotoğrafın anahtarı da ürün kodu. Kategori sayfası tek merkez: 1 Sitelerde → 2 Excel → 3 Fotoğraflar → 4 Sitede gör. Yeni veri modeli yok; galeri aynı `_nwcs_gallery` meta'sı. Önizle → uygula → geri al zinciri Excel'dekiyle aynı dil.

Menü (d09bc46): İçerik Stüdyosu · Ürün Havuzu (Ürünler, Kategoriler, Ürün açıklamaları, Detay başlıkları) · Medya Havuzu; Geliştirici ayrı. Bu iş menüye bir şey eklemez.

---

## 1. Fotoğraf Kutusu — kullanıcı ne görür

Kategoriler → kategori → "Ürünleri Excel ile girin" kartının hemen altına üçüncü kart:

```
─ Fotoğrafları yükleyin ─────────────────────────────────────────── id="nwcs-fotograf"
  Dosya adı ürün koduyla başlasın: W-KAM-400DUB-1.jpg, W-KAM-400DUB-2.jpg …
  İlk numara kartta kullanılır. JPG, PNG, WebP ve iPhone HEIC olur; dosya başına en fazla 10 MB.
  ┌──────────────────────────────────────────────────────┐
  │  Fotoğrafları buraya bırakın ya da [Dosya seçin]     │   (JS yokken: çoklu dosya alanı + "Yükle")
  └──────────────────────────────────────────────────────┘
  Yükleniyor 14 / 20  ▓▓▓▓▓▓▓░░░        kamelya-genel.jpg: kod bulunamadı
```

Yükleme bitince sayfa önizlemeyle açılır (Excel önizlemesiyle aynı yer: `#nwcs-fotograf-sonuc`):

```
Önizleme: Kamelyalar · 20 dosya
Henüz hiçbir ürüne fotoğraf bağlanmadı. Aşağıyı kontrol edin, doğruysa "Fotoğrafları uygula"ya basın.
[18 fotoğraf eklenecek] [7 ürün] [2 dosya eşleşmedi]
Mevcut galerisi olan ürünlerde:  (•) sona ekle   ( ) galeriyi değiştir (eski fotoğraflar Medya Havuzu'nda kalır)

W-KAM-400DUB  Master Seri, Çift Katlı…        3 yeni (mevcut 0)      [▢][▢][▢]
W-KAM-600YK   Prestij Seri, Yarı Kapalı…      2 yeni (mevcut 1)      [▣][▢][▢]
URN-0231      Ahşap Palet  — başka kategoride (Ahşap Palet)  1 yeni
Eşleşmeyen (uygulanınca silinir): kamelya-genel.jpg (kod bulunamadı) · IMG_4412.heic (sunucu HEIC açamadı)

[Fotoğrafları uygula]  [Vazgeç]
Uygularsanız geri alma sırası: önce bu yükleme, sonra … (mevcut nwcs_history_order_note)
```

Uygulandıktan sonra: yeşil sonuç kartı ("7 ürüne 18 fotoğraf eklendi") + "Son işlemler"de yeni satır (Geri al) + kategori ürün tablosunda "fotoğraf" rozeti. Kart başlıkları ve tonu Excel kartıyla aynı; tek birincil düğme (önizlemede "Fotoğrafları uygula"; yükleme kartında birincil düğme yok, bırakma alanı kendisi eylemdir; JS yokken "Yükle" birincil).

## 2. Dosya adı → ürün kodu eşleme (kesin kurallar)

Yeni saf yardımcılar `includes/photos.php` (eklenti çekirdeği; temalar kullanmaz, admin'e de WP-CLI'ye de açık):

1. **Anahtar üretimi** `nwcs_photo_key( string $text ): string`
   - Uzantı atılır (`pathinfo` FILENAME; "IMG_0001.JPG" → "IMG_0001").
   - `nwcs_normalize_product_code()` uygulanır (büyük harf; `[^A-Z0-9\-_.]` → `-`; ardışık `-` tek; uçlardaki `-` atılır). Türkçe harfler, boşluk, parantez, Türkçe büyük İ gibi çok baytlı karakterler bu kuralla ayraç olur ("şezlong (2)" → "-EZLONG-2-" → "EZLONG-2"); kodlar zaten bu fonksiyonla kaydedildiği için Türkçe harf içermez, eşleşmeyi bozmaz.
   - Ek olarak `_` ve `.` de `-` olur ve ardışıklar tekrar toplanır. Böylece `W_KAM_400DUB.1` = `W-KAM-400DUB-1`. Aynı dönüşüm ürün kodlarına da uygulanır; iki taraf aynı anahtarla karşılaştırılır.
2. **Aday kodlar:** `nwcs_pool_products()` (yalnızca yayındaki ürünler) → `[ anahtar => id ]`. Boş kodlu ürün aday değildir (kod zaten her üründe `nwcs_ensure_product_code` ile var). Çöp kutusundaki ürün aday değildir: eşleşirse dosya "çöp kutusundaki bir ürünün kodu" notuyla eşleşmemiş sayılır (`nwcs_product_id_by_code` çöpü de bulur; bu ayrımı yapmak için kullanılır).
3. **Önek eşleşmesi (asıl kural):** dosya anahtarı `K` olsun. Aday `C` eşleşir ⇔ `K === C` ya da `K` `C . '-'` ile başlar (sınır kuralı: `W-KAM-400` kodu `W-KAM-400DUB-1`'i yakalamaz, çünkü `W-KAM-400` sonrası `D`). Birden çok aday eşleşirse **en uzun** kod kazanır (`W-KAM-400` ve `W-KAM-400-1` ikisi de kodsa `W-KAM-400-1-2` → `W-KAM-400-1`). Kalan kuyruk `K`'dan `C` çıkarılınca kalan ("-1", "-IMG010", "-2-KOPYA").
4. **İçerme eşleşmesi (yedek):** önek eşleşmesi yoksa, anahtarda `-C-` biçiminde ya da `C-`/`-C` uçlarda geçen en uzun kod; yalnızca `strlen(C) >= 5` olan kodlar için (kısa kodların her yerde geçmesine karşı). Mevcut canlı dosya adları bu yüzden çalışır: `woodpets-w-dog-v06-img010.webp` → `WOODPETS-W-DOG-V06-IMG010`, içerir `W-DOG-V06`. Kuyruk: koddan sonraki parça ("-IMG010").
5. **Sıra numarası:** kuyruktaki **ilk** rakam dizisi (`/\d+/`) → tam sayı ("-IMG010" → 10, "-2-KOPYA" → 2, "(3)" → 3). Rakam yoksa `PHP_INT_MAX` (numaralılardan sonra). Aynı ürünün dosyaları `[sıra, strnatcasecmp(dosya adı)]` ile sıralanır. Numara çakışması (iki dosya da 1) hata değildir; ad sırasıyla dizilir, önizlemede not düşülür.
6. **Kategori dışı eşleşme:** kod havuzda var ama ürün açık kategoride değilse yine eşleşir; önizlemede "başka kategoride (Kategori adı)" notuyla ayrı grupta listelenir ve uygulanır. (Kullanıcı o dosyayı o ürün için adlandırmıştır; reddetmek yardımcı olmaz.)
7. **Eşleşmeyen dosya:** "kod bulunamadı" (ya da "çöp kutusundaki ürün", "sunucu HEIC açamadı", "görsel değil") nedeniyle listelenir; **uygulanınca da vazgeçilince de silinir**, hiçbir yere bağlanmaz. Hiçbir şey sessizce atlanmaz.
8. **Aynı dosya iki kez** (aynı ad, aynı partide): ikisi de yüklenir, ikisi de eklenir; önizlemede "aynı adlı 2 dosya" notu. (Önizleme kaldırmaya izin verir: satırdaki "×" o dosyayı eşleşmeyenlere taşır — bu küçük JS; JS yoksa not yeterli.)
9. **Boyut/oran:** zorunluluk yok. Önizlemede her küçük resmin altında `G×Y`; yatay ya da 1280/1714 oranından %10'dan fazla sapan görselde kısa bilgi: "yatay; sitede kendi oranında, kırpılmadan gösterilir". Engellemez.

Birim testi mantığı (geliştirme betiği, `scripts/dev/`): `W-KAM-400DUB-1.jpg`, `w_kam_400dub (2).JPEG`, `W-KAM-400DUB.webp` (numarasız), `woodpets-w-dog-v06-img010.webp`, `W-KAM-400-1.png` (iki kod varken), `IMG_4412.jpg` (eşleşmez), `URN-0006-3.jpg` (Koçist üretilmiş kod), `Şezlong-1.jpg` (eşleşmez; önek değil, kod değil).

## 3. Yükleme mimarisi

**Karar: dosyalar doğrudan havuz sitesinin medya kitaplığına, "parti" işaretiyle yüklenir; geçici klasör yok.**

- Her dosya ayrı istek: `wp_ajax_nwcs_photo_upload` (`admin-ajax.php`, nonce `nwcsPanel.nonce` = `nwcs_panel`, yetki `NWCS_CAPABILITY`). Sunucu `switch_to_blog( nwcs_pool_blog_id() )` içinde `media_handle_upload( 'dosya', 0 )` çağırır (medya havuzuyla aynı yol, media.php:531). Yanıt: `{ id, name, key, match: {id, code, title, category_note} | null, seq, thumb, width, height, reason }`.
- **WebP:** `media_handle_upload` → `wp_handle_upload` → `images.php`'deki `wp_handle_upload` filtresi (images.php:44) devreye girer: `current_user_can('upload_files')` ağ yöneticisinde doğru, admin-ajax da yönetim bağlamı. Yani panel yüklemesiyle **aynı** dönüşüm (2560 px, yön düzeltme, WebP, orijinal silinir). `wp_handle_sideload` kullanılmaz; ileride kullanılırsa images.php'ye `add_filter('wp_handle_sideload', …)` eklenmesi gerektiği not düşülür (filtre adı eyleme göre değişir).
- **HEIC:** dönüşüm `wp_get_image_editor` HEIC açabiliyorsa (Imagick + libheif) olur. Açamazsa dosya HEIC kalır; sunucu yanıtında `type` hâlâ `image/heic|heif` ise ek silinir ve dosya "sunucu HEIC açamadı; JPG olarak kaydedip yükleyin" nedeniyle eşleşmeyenlere düşer. Tarayıcıda gösterilemeyen bir görsel asla galeriye girmez.
- **Parti işareti:** yüklenen ekin meta'ları `_nwcs_photo_batch` (parti kimliği, `bin2hex(random_bytes(6))`), `_nwcs_photo_user`, `_nwcs_photo_name` (tarayıcının gönderdiği özgün ad; `sanitize_file_name` dosyayı değiştirse de eşleme özgün adla yapılır), `_nwcs_photo_cat` (kategori slug). Parti kimliği kullanıcı başına site transient'ında (`nwcs_photos_batch_<user>`, 1 saat) ve istemcide tutulur; ilk dosyada sunucu üretir, sonrakiler geri gönderir.
- **Sınırlar:** istemci yüklemeden önce `file.size > nwcsPanel.uploadMax` ise dosyayı göndermez, "çok büyük (sınır 10 MB)" yazar. `uploadMax = wp_max_upload_size()` (çoklu sitede `fileupload_maxk` ile zaten sınırlı; yerelde 10240 KB yapıldı, canlıda Ağ Ayarları'nda 1500 → 10240 **şart**, DEVAM.md:215). Sunucu tarafında aynı denetim `check_upload_size` ile zaten yapılır; hata metni dosya satırında gösterilir. Kabul edilen türler: `image/jpeg, image/png, image/webp, image/heic, image/heif` (`accept` ve sunucuda `wp_check_filetype`); GIF/SVG "görsel türü uygun değil" ile reddedilir.
- **Eşzamanlılık:** istemci dosyaları **sırayla** gönderir (tek kuyruk; büyük partide sunucuyu boğmaz, ilerleme sayacı doğru kalır). Her istek tek dosya işlediği için 300 sn sınırına yaklaşmaz (WebP + ara boyutlar dosya başına ~1–3 sn).
- **JS yokken:** kart düz bir `<form enctype="multipart/form-data">`: `<input type="file" name="dosyalar[]" multiple>` + "Yükle" (admin-post `nwcs_photos_upload_all`, nonce `nwcs_photos_upload`). Sunucu aynı tek-dosya fonksiyonunu döngüyle çağırır (media.php:507 kalıbı), `set_time_limit(300)`, sonra önizlemeye yönlendirir. Formun ipucu `max_file_uploads` (yerelde 30) ve `post_max_size` (40 M) değerlerini yazar: "JavaScript kapalıyken bir seferde en fazla N dosya, toplam M". Toplam `post_max_size`'ı aşınca PHP `$_FILES`'ı boş bırakır → "Seçtiğiniz dosyaların toplamı sınırı aşıyor; daha az dosyayla deneyin" (media.php:506 kalıbı).
- **Önizleme** yükleme bitince istemcinin gönderdiği küçük POST formuyla açılır (`nwcs_photos_preview`, parti kimliği) → kategori sayfasına `?kategori=…&fotograf=onizleme#nwcs-fotograf-sonuc`. Plan **transient'ta saklanmaz**; her görüntülemede partideki eklerden ve havuzun o anki kodlarından yeniden hesaplanır (`nwcs_photos_plan( $batch )`). Bayat plan sorunu böylece yoktur: uygulama anında geçerli olan eşleşme kullanılır; "sona ekle" mevcut galeriye ekler, "galeriyi değiştir" kullanıcının açıkça seçtiği davranıştır.
- **Kilit / iki kez basma:** uygulama ilk iş olarak partinin eklerinden `_nwcs_photo_batch` meta'sını siler (ürün başına, yazmadan hemen önce); ikinci "Uygula" isteği partide ek bulamaz → "Bu parti zaten uygulandı" ile sonuç sayfasına döner. Gizli `parti` alanı transient'taki kimlikle eşleşmezse (başka sekmede yeni parti başlatıldı) → "Önizlemenin süresi doldu ya da başka bir yükleme başladı".
- **Yeni parti başlarken** bekleyen eski parti varsa: eski partinin ekleri silinir, transient yenilenir (kullanıcı başına tek parti). Bırakma alanı eski parti varken "Bekleyen 12 dosya var — önizlemeyi aç" bağlantısı gösterir.
- **Süpürme:** `nwcs_photos_sweep()` kategori sayfası açılırken (`nwcs_import_sweep` gibi): `_nwcs_photo_batch` meta'sı olup `post_date` 24 saatten eski ekler silinir (`wp_delete_attachment(id, true)`).
- **Medya Havuzu listesi** parti işaretli ekleri göstermez (`nwcs_media_query`'ye `meta_query NOT EXISTS _nwcs_photo_batch`; sayfa başına 24 kayıtta maliyet önemsiz). Böylece yarım kalan parti "kullanılmayan görsel" gibi görünüp elle silinmez.

## 4. Uygulama, geri alma, önbellek

**`nwcs_photos_apply( string $batch, string $mode )`** (`mode` ∈ `append|replace`, varsayılan `append`):

1. `set_time_limit(300)`; `switch_to_blog( nwcs_pool_blog_id() )`.
2. Kayıt (`nwcs_history_push`, `complete=false`): `{ id, kind:'fotograflar', slug, category, user, mode, time, products: { id => { code, title, before:[ek id'leri], added:[ek id'leri] } }, unmatched: n, counts:{ photos, products } }`. Her üründen sonra `nwcs_history_save` (Excel'deki `$save` kalıbı); yarıda kesilirse o ana kadar olanlar geri alınabilir.
3. Ürün başına (plan sırasıyla): ürün hâlâ yayında ve `nwcs_sync_product_post` ile bulunabiliyorsa → `before = _nwcs_gallery` (okuma `nwcs_pool_product_data` ile aynı: boşsa öne çıkan görsel); `added` = partideki o ürünün ekleri sıra numarasına göre; yeni galeri = `append` ? `before + added` : `added`; `update_post_meta('_nwcs_gallery')`; `set_post_thumbnail( ilk )`; eklerde: `_nwcs_photo_*` meta'ları silinir, `post_title = "<KOD> görsel NN"` (mevcut veri biçimi: "W-DOG-V06 görsel 01"), alt boşsa `_wp_attachment_image_alt = ürün adı`. Ürün bu arada çöpe gitmiş/silinmişse ekleri silinir, `skipped[]`'e yazılır.
4. Eşleşmeyen ekler `wp_delete_attachment( id, true )`.
5. `restore_current_blog()`; `$record['complete']=true`; `nwcs_pool_flush_cache()` (havuz transient'ı + `nwcs_cache_mark_dirty` → yönlendirmeden hemen önce LiteSpeed/Cloudflare temizliği, cache-purge.php:106; sonuç notu bir sonraki sayfada). Transient silinir; yönlendirme `?fotograf=uygulandi`.

**Geri alma** (`nwcs_photos_undo( $record )`, history.php `switch`'ine `case 'fotograflar'`): ürün başına, mevcut galeri beklenen hâldeyse (`append`: `before + added`; `replace`: `added`) → `before` geri yazılır, `set_post_thumbnail`/`delete_post_thumbnail`; değilse (sonradan elle düzenlendi) `kept[]` ve dokunulmaz. Geri alınan üründe `added` ekleri **başka hiçbir üründe kullanılmıyorsa** silinir (`nwcs_media_usage()` ile denetim; kullanılıyorsa yalnızca bu galeriden çıkar). Rapor `{ restored, kept, deleted_photos }`; `nwcs_render_history_result` biçim tablosuna `deleted_photos => '%d fotoğraf dosyası silindi'`. `nwcs_history_describe`: what = kategori adı, kind = "fotoğraflar", facts = "18 fotoğraf, 7 ürün (sona eklendi | galeri değişti)". Ürün sonradan silinmişse `nwcs_undo_gone` kalıbı yerine yalnızca o ürün `kept` sayılır (kayıt listeden düşmez; başka ürünler geri alınır).

**Ürün sayfasında sıra:** temalar `nwcs_site_products()['images']` sırasını kullanır (`wk_gallery`, Koçist `product-detail.php:41`); galeri meta sırası = site sırası. Site istisnasındaki "öne çıkan görsel" (`override.image`) varsa o yine önde kalır (products.php:716) — değişmez.

## 5. A — `nwcs_product_site_status()` ve tek tık düzeltme

**`nwcs_product_site_status( int $product_id ): array<int blog_id, array>`** (sync.php, `nwcs_product_site_labels`'ın yanında; aynı statik site önbelleğini kullanır). Site başına, sırayla ilk eşleşen kural:

| # | Koşul | state | reason (panel metni) | fix |
|---|---|---|---|---|
| 0 | tema ürün göstermiyor (`!nwcs_site_supports_products`) | — | listelenmez (mevcut davranış) | — |
| 1 | ürün çöp kutusunda (`nwcs_pool_trashed_ids`) | `trash` | "çöp kutusunda" | `untrash` |
| 2 | `overrides[id]['hidden']` | `hidden` | "bu sitede gizlenmiş (Ürün Havuzu toplu işlemi)" | `unhide` |
| 3 | kip `selected`, ürün listede değil, hiçbir kategorisi bu siteye yerleşmemiş (`nwcs_pool_categories()[slug]['placement'][site_key]` boş; üst başlık kategorisi `nwcs_parent_categories` de yerleşim sayılır) | `unplaced` | "kategorisi (Kamelyalar) bu siteye yerleşmemiş" | `place` → Kategoriler sayfasına bağlantı (üst başlık seçimi gerekir; otomatik yapılmaz) |
| 4 | kip `selected`, ürün listede değil, kategorisi yerleşik | `unselected` | "sitenin ürün listesinde değil" | `select` (`nwcs_selection_append`) |
| 5 | görünür ama `nwcs_product_place()` parent=null (WK'da Mağaza'da var, hiçbir kategori sayfasında yok) | `visible_unplaced` | "görünüyor; kategori sayfası yok (kategorisi yerleşmemiş)" | `place` bağlantısı |
| 6 | görünür | `visible` | "WOODGarden › Kamelyalar, 12 / 59" (`nwcs_product_place` + `nwcs_site_products` içindeki 1-tabanlı sıra) | — |

Ek alanlar: `label`, `url` (`nwcs_product_url` yalnızca `visible*` ve `nwcs_product_has_page` doğruysa), `category_url` (ilk kategorinin Kategoriler adresi). WK'nın "ürünü olmayan kategori menüde görünmez" kuralı ürün değil kategori durumudur; burada yalnızca #5'in metninde "WOOD KOCIST'te kategori sayfası ürün olunca açılır" notu.

**Form üstü durum şeridi** (`nwcs_render_pool_form`, başlık altı, yalnızca kayıtlı üründe):

```
WOOD KOCIST  ● görünüyor · WOODGarden › Kamelyalar · 12 / 59          Sitede gör ↗
Koçist       ○ görünmüyor: sitenin ürün listesinde değil               [Listeye ekle]
Eksik: fotoğraf · açıklama
```

Rozet sınıfları mevcut `nwcs-ovr__tag`, `--on`, `--off`; düğme `button button-small`. "Özelleştirmeler" bloğundaki "Bu sitede seçili değil" rozeti kalır (aynı veriden; tutarlı).

**Düzeltme ucu:** admin-post `nwcs_product_fix`, alanlar `urun`, `site`, `ne` ∈ `select|unhide|untrash`; nonce `nwcs_product_fix_<urun>`; yetki `NWCS_CAPABILITY`; site `nwcs_editable_sites()` içinde olmalı. `select`/`unhide` = toplu "göster" ile birebir aynı mantık → pool.php:1180–1200 bloğu `nwcs_product_show_on_site( int $blog_id, array $ids ): void` yardımcısına çıkarılır, toplu işlem de onu çağırır (tek yazıcı). `untrash` → `nwcs_untrash_product` (sync.php:846). Sonra `nwcs_pool_flush_cache()`; forma `nwcs_pool=fixed` ile dönüş ("Koçist'te görünür oldu").

**Eksikler** `nwcs_product_gaps( array $product ): array` (products.php; Stüdyo satırı ve liste aynı fonksiyon): `photo` = `images` boş; `body` = `trim(body)` boş; `details` = `details` boş (kod satırı sayılmaz; serbest not varsa `spec` dolu kabul). Örnek görseller (`ornek-urun-*.png`) fotoğraf sayılır (basit kural; soru 3).

**Liste:** "Sitelerde" sütunundan sonra "Eksik" sütunu; değer küçük gri çipler "fotoğraf · açıklama · detay" (yalnızca eksikler; hepsi tamsa "—"). Araç çubuğuna ikinci açılır liste `eksik`: "Hepsi" · "Fotoğrafı olmayan" · "Açıklaması olmayan" · "Teknik detayı olmayan" · "Hiçbir sitede görünmeyen" (`nwcs_product_site_labels` boş). `nwcs_pool_query` yeni `missing` argümanı; `$filters` dizisine `eksik` (sayfalama ve toplu işlem formu süzgeci korur, pool.php:62, 184). Sonuç sayısı "320 ürün · 278'inde fotoğraf yok" (süzgeç yokken küçük not; sayım `nwcs_pool_products` üzerinde tek döngü).

**Stüdyo satırı** (fields.php:339 `<small>` içinde, istisna formuna dokunmadan): kod ve fiyatın yanına üç nokta `●○●` (`nwcs-gaps`): sırasıyla fotoğraf, açıklama, teknik detay; dolu = brand, boş = outline; `title` ve ekran okuyucu metni "fotoğraf yok". Fotoğraf yoksa "Fotoğraf ekle ↗" bağlantısı → ürünün ilk kategorisinin Kategoriler sayfası `#nwcs-fotograf` (yeni sekme). Sayfa bulucu, kip seçimi, sıra, istisnalar aynen.

## 6. "Kitaplıktan ekle" → arama kutusu

- Havuz formunda `<select data-nwcs-gallery-add>` (pool.php:406) kalkar. Yerine: `<input type="search" data-nwcs-media-find placeholder="Kitaplıkta ara: dosya adı, başlık ya da ürün kodu">` + sonuç listesi (küçük resim, ad, "Ekle"). AJAX `wp_ajax_nwcs_media_find` (`q`, `haric[]` = galerideki kimlikler): havuz sitesinde iki sorgu birleştirilir — `s` ile başlık/alt (WP araması), `_wp_attached_file` LIKE ile dosya adı; en çok 30 sonuç, en yeni önce; `q` boşsa son 30. Türkçe katlama veritabanında yapılamaz; ipucu "büyük/küçük harf fark etmez" ve `q` hem özgün hem `nwcs_search_fold` hâliyle denenir.
- "Ekle": admin.js:773'teki galeri düğümü üreticisi fonksiyona çıkarılır (`appendGalleryItem(id, thumb)`), arama sonucu ve (varsa) eski seçenek aynı fonksiyonu kullanır. Enter formu göndermez.
- **JS yoksa:** aynı kutu `GET` formudur (`gorsel_ara`), form sayfası yeniden açılır, sonuçlar `gallery_add[]` onay kutuları olarak gelir; `nwcs_handle_pool_save` bunları `$uploaded` gibi sona ekler (pool.php:607). Böylece 200 sınırı iki yolda da kalkar.
- `nwcs_pool_media()` Stüdyo istisna görsel listesinde (fields.php:325 `template`) kalır — **kapsam dışı** (kullanıcı kararı); README "Sınırlar"a not: "Stüdyo'daki site görseli listesi son 200 görseli gösterir; ürün galerisi için havuz formundaki aramayı kullanın".

## 7. Metinler

- **Ürün Havuzu üst çizgisi** (pool.php:98): "3 Görselleri Medya Havuzu'ndan ekle" → "3 Fotoğrafları kategorinin sayfasından yükleyin (dosya adı = ürün kodu)" bağlantı Kategoriler'e.
- **Medya Havuzu üst çizgisi** (media.php:198): "1 Ürün fotoğrafları: Ürün Havuzu › Kategoriler › kategori › Fotoğrafları yükleyin — dosya adı ürün koduyla başlar · 2 Site görselleri (logo, hero, blog) buraya yüklenir · 3 Alt metni doldurun". Yükleme kartı ipucuna tek cümle: "Ürün fotoğrafı için Fotoğraf Kutusu'nu kullanın; buradan yüklenen görsel ürüne kendiliğinden bağlanmaz."
- **Excel "Nasıl kullanılır"** (sync.php:340): son satır değişir: "Fotoğraflar bu dosyada yok. Fotoğraf dosyalarını ürün koduyla adlandırın (W-KAM-400DUB-1.jpg, -2.jpg …) ve aynı kategori sayfasındaki "Fotoğrafları yükleyin" alanına bırakın. Uzun açıklamalar için "Ürün açıklamaları" ekranını kullanın."
- **Havuz formu Görseller ipucu** (pool.php:384): "Toplu fotoğraf için kategorinin sayfası: Fotoğrafları yükleyin ↗".
- Yönetici dili korunur: "Teklif al", "fotoğraf" (dosya için), "görsel" (medya kaydı için), cümle düzeni, büyük harf yok, ok yok; düğme adı = sonuç adı ("Fotoğrafları uygula" → "Fotoğraflar uygulandı").

## 8. Tasarım notu (frontend-design; panelin mevcut dili)

- Yeni renk, yeni yazı tipi yok; `--nwcs-brand`, `--nwcs-brand-soft`, `--nwcs-line`, `--nwcs-soft`, `--nwcs-muted`, `--nwcs-amber`, `--nwcs-radius` ve `nwcs-sync__chip--new/--error` kullanılır. Kart iskeleti `nwcs-cat__section` + `nwcs-section__title` + `nwcs-hint` (Excel kartıyla aynı).
- **Bırakma alanı** (`nwcs-drop`): 2 px kesikli `--nwcs-line`, `--nwcs-radius`, 36 px iç boşluk, ortada tek cümle + "Dosya seçin" düz düğme; `is-over` durumunda zemin `--nwcs-brand-soft`, kenar `--nwcs-brand`. Simge yok. Odak halkası mevcut `:focus-visible` kuralı. İlerleme: `<progress>` + "14 / 20" metni, `aria-live="polite"`. Hareket yalnızca `is-over` geçişi; `prefers-reduced-motion`'da geçiş kapalı.
- **Önizleme**: `nwcs-sync` başlık/çip/grup düzeni; ürün satırı = kod (tabular), ad, "3 yeni (mevcut 0)", küçük resimler 48 px karede kendi oranıyla (`object-fit: contain`, zemin `--nwcs-soft`, çerçeve 1 px `--nwcs-line`). Eşleşmeyenler `nwcs-sync__group--error` grubunda, nedeni satırda.
- **Tek birincil düğme** kuralı: önizlemede "Fotoğrafları uygula" (`button-primary`), "Vazgeç" düz. Yükleme kartında JS'li durumda birincil düğme yok.
- **390 px**: bırakma alanı tam genişlik, küçük resim sırası yatay kayar (`nwcs-cat__scroll`), çipler sarar; dokunma hedefleri ≥ 44 px (satırdaki "×" 44 px kutuda).
- **Durum şeridi** formda: iki satır, soldan hizalı, site adı sabit genişlik (120 px), rozet, metin, sağda düğme/bağlantı; düğme `button-small`. Stüdyo noktaları 8 px, 4 px aralık, metinle aynı satır.

## 9. Dosyalar

| Dosya | Değişiklik |
|---|---|
| `includes/photos.php` (yeni) | `nwcs_photo_key`, `nwcs_photo_candidates`, `nwcs_photo_match( name ) → {id, code, seq, tail, note}`, `nwcs_photos_plan( batch )`, `nwcs_photos_apply`, `nwcs_photos_undo`, `nwcs_photos_sweep`, `nwcs_photo_store( file ) ` (tek dosya: media_handle_upload + meta + HEIC/tür denetimi); `require_once` network-content-studio.php |
| `includes/admin/pool-photos.php` (yeni) | kart, önizleme, sonuç kartı; admin-post `nwcs_photos_upload_all`, `nwcs_photos_preview`, `nwcs_photos_apply`, `nwcs_photos_cancel`; ajax `nwcs_photo_upload`; `nwcs_photos_pending()` (transient) |
| `assets/pool-photos.js` (yeni, ~150 satır) | bırakma alanı, sıralı yükleme, ilerleme, satır hataları, bitince önizleme formunu gönderme; önizlemede "×" (eşleşmeyene taşı, `nwcs_photo_unlink` ajax ya da gizli alan listesi) |
| `assets/admin.css` | `.nwcs-drop*`, `.nwcs-photos__*`, `.nwcs-status*` (form şeridi), `.nwcs-gaps*`, `.nwcs-find*` (kitaplık araması) |
| `includes/admin/pool-categories.php` | `nwcs_render_category_detail`: Excel kartından sonra `nwcs_render_photo_box( $slug )`; `?fotograf=` adımı; ürün tablosuna "fotoğraf" rozeti; sayfa açılışında `nwcs_photos_sweep()` |
| `includes/admin/history.php` | `describe` ve `undo` `case 'fotograflar'`; sonuç biçimine `deleted_photos`; boş-durum ipucuna "fotoğraf yüklemeleri" |
| `includes/admin/sync.php` | `nwcs_product_site_status()`, `nwcs_product_show_on_site()` (toplu "göster"den çıkarılır); yardım satırı |
| `includes/products.php` | `nwcs_product_gaps()`; `nwcs_pool_query` `missing` süzgeci |
| `includes/admin/pool.php` | üst çizgi metni; araç çubuğu `eksik` seçimi + `$filters`; "Eksik" sütunu; formda durum şeridi + "Sitede gör ↗"; Görseller ipucu; `data-nwcs-gallery-add` yerine arama kutusu + JS'siz `gallery_add[]`; admin-post `nwcs_product_fix`; toplu "göster/gizle" yardımcıyı çağırır; `nwcs_render_pool_notices` yeni anahtarlar (`fixed`, `photos_*`) |
| `includes/admin/fields.php` | ürün satırında `nwcs-gaps` + "Fotoğraf ekle ↗" (istisna formu aynı) |
| `includes/admin/media.php` | üst çizgi metni; `nwcs_media_query` parti işaretlileri gizler; yükleme kartı ipucu; ajax `nwcs_media_find` |
| `assets/admin.js` | galeri düğümü üreticisi fonksiyona; `data-nwcs-media-find` davranışı |
| `includes/admin/panel.php` | `nwcsPanel.uploadMax` ve `uploadMaxText` (`size_format`); `pool-photos.js` yalnızca `NWCS_CATEGORIES_SLUG` sayfasında (`str_contains( $hook, NWCS_POOL_SLUG )` zaten kapsar; ayrıca `wp_enqueue_script` koşulu kategori sayfasıyla sınırlanır) |
| `network-content-studio.php` | sürüm 0.23.0; `includes/photos.php`, `includes/admin/pool-photos.php` |
| Belgeler | README (Fotoğraf Kutusu bölümü, dosya adı kuralı, Natro `fileupload_maxk` 10240, "Eksik" süzgeçleri, durum şeridi, kitaplık araması, Stüdyo 200 notu), DECISIONS (neden koda göre eşleme; neden parti = medya kitaplığı; neden plan transient'sız; C'nin ertelenmesi), DEVAM |

Dokunulmayan: temalar, Excel plan/uygula/geri alma (sync.php iç mantığı), Stüdyo istisna formu, Havuz Paketi, `nwcs_pool_media()`.

## 10. Adımlar (her biri tek başına doğrulanır; commit yok)

0. Başlangıç kanıtı: havuzdaki galeri meta'larının dökümü (`wp eval` → scratchpad JSON), WK ve Koçist'ten 1'er ürün sayfası 1440/390 ekran görüntüsü, Kategoriler ve Ürünler sayfası görüntüleri; veritabanı yedeği (scratchpad).
1. `includes/photos.php` eşleme yardımcıları + geliştirme betiği `scripts/dev/check-photo-match.php` (§2 örnek seti, hepsi beklenen sonucu verir).
2. `nwcs_photo_store` + ajax `nwcs_photo_upload`: tek dosya yüklenir, WebP olur, parti meta'ları yazılır, HEIC/tür/boyut hataları düz Türkçe döner. `check-webp-upload.php` kalıbıyla test.
3. Kart + JS'siz çoklu form + `nwcs_photos_plan` + önizleme ekranı (yalnızca okuma). JS kapalıyken uçtan uca.
4. `pool-photos.js`: sıralı yükleme, ilerleme, hata satırları, bitince önizleme.
5. `nwcs_photos_apply` + sonuç kartı + `nwcs_pool_flush_cache`; ürün sayfalarında galeri sırası.
6. History: `fotograflar` describe/undo + sonuç biçimi; geri al → galeri eski hâline, dosyalar silinir; "elle düzenlendi" durumunda `kept`.
7. Süpürme, vazgeç, Medya Havuzu'nda gizleme, bekleyen parti bağlantısı.
8. A: `nwcs_product_site_status`, `nwcs_product_gaps`, form şeridi, `nwcs_product_fix`, toplu "göster" yardımcıya devir (davranış birebir: önce/sonra option karşılaştırması).
9. Liste: "Eksik" sütunu, `eksik` süzgeci, sayfalama/toplu işlemde süzgecin korunması.
10. Stüdyo satırı noktaları + bağlantı (istisna formu HTML diff'i yalnızca `<small>` içi).
11. Kitaplık araması (ajax + JS'siz onay kutuları), eski select kaldırılır.
12. Metinler (§7), `nwcsPanel.uploadMax`, sürüm, belgeler.

## 11. Doğrulama

- **20 dosyalık parti** (Kamelyalar; 1280×1714 JPG, bir PNG, bir WebP): JS'li yükleme 20/20, önizleme 18 eşleşen + 2 eşleşmeyen (adsız + HEIC), uygula → 7 ürün; `_nwcs_gallery` sırası dosya numarasıyla aynı; ilk görsel `post_thumbnail`; ek başlıkları "<KOD> görsel NN"; parti meta'ları silinmiş; eşleşmeyen dosyalar diskte yok.
- **Eşleşmeyen ad**: `IMG_4412.jpg` → "kod bulunamadı", uygulanınca silinir; `Şezlong-1.jpg` eşleşmez; `woodpets-w-dog-v06-img010.webp` içerme kuralıyla `W-DOG-V06` sıra 10; `W-KAM-400-1.png` iki kod varken en uzun.
- **HEIC**: Imagick HEIC açıyorsa WebP olur; açamıyorsa dosya reddedilir ve nedeni yazar (yerelde ikisi de simüle: `nwcs_image_webp_supported` ve editor mime desteği geçici filtreyle kapatılır).
- **Mevcut galerili ürün**: "sona ekle" → eski 1 + yeni 2, sıra korunur; "galeriyi değiştir" → yalnızca yeni 2, eski ek Medya Havuzu'nda "kullanılmıyor" olarak durur.
- **Geri al**: üst kayıt → galeriler `before`'a döner, eklenen dosyalar silinir (başka üründe kullanılan dosya silinmez), ürün sonradan formdan düzenlendiyse `kept`; geri alma sırası notu doğru; yarıda kesilme simülasyonu (apply ortasında `wp_die`) → kayıt "yarıda kesildi", geri alma yapılanları geri alır.
- **İki kez Uygula**, eski sekmeden Uygula, başka partiyle Uygula → doğru hata metinleri; sweep 24 saat (zaman filtreyle öne alınır).
- **JS kapalı**: çoklu dosya alanı + Yükle → önizleme → uygula → geri al; kitaplık araması GET + onay kutuları; düzeltme düğmeleri (düz form).
- **390 px** (Kategoriler ve Ürünler sayfası): `scrollWidth` = 390, bırakma alanı, önizleme tablosu, çipler; dokunma hedefleri ≥ 44 px.
- **E_ALL** (geçici mu-plugin, sonra kaldırılır): Kategoriler (boş/dolu parti, önizleme, sonuç), Ürünler listesi (her `eksik` süzgeci, 16 sayfa), ürün formu (çöpteki, gizli, seçilmemiş, görünür ürünler), Stüdyo WK Mağaza ve Koçist Ana Sayfa ürün bölümü, Medya Havuzu, admin-ajax uçları → 0 bildirim.
- **Ürün sayfaları**: WK ve Koçist'te fotoğraf eklenen ürünlerde galeri sırası, küçük resimler, PhotoSwipe/lightbox; fotoğrafsız ürünlerde plaka aynen; 55 + 283 adres 200.
- **A**: her state için bir ürün (çöp, gizli, yerleşmemiş kategori, seçilmemiş, görünür): şerit metni ve düğme; `nwcs_product_fix` sonrası `nwcs_products_selected` / `overrides` beklenen; nonce/yetkisiz istek 403. Toplu "göster" devri: 3 ürünlük toplu işlem öncesi/sonrası option md5 eski kodla aynı.
- **Önbellek**: uygulama ve geri almadan sonra havuz transient'ı yok, `nwcs_cache_note` (LiteSpeed/Cloudflare tanımlıysa) notu bir kez.
- **Regresyon**: Excel indir/yükle/uygula/geri al değişmedi (kayıt yapısı aynı); Stüdyo istisna formu HTML'i `<small>` dışında aynı; Havuz Paketi dışa/içe aktarma aynı.

## 12. Riskler

- **Natro yükleme sınırı:** `fileupload_maxk` 1500 kalırsa her telefon fotoğrafı reddedilir; kart sınırı yazar, DEVAM'da canlı adımı açık. PHP `upload_max_filesize`/`post_max_size` cPanel'den.
- **HEIC:** Natro'da Imagick/libheif yoksa HEIC yüklenemez; dosya reddedilir, mesaj yönlendirir (iPhone'da "En uyumlu" ayarı ya da JPG dışa aktarma). Sessiz bozuk görsel yok.
- **Dosya adı disiplini:** önizleme eşleşmeyenleri gösterir; yine de kullanıcı "kodu başa yaz" kuralını öğrenmek zorunda — kural kartta, Excel yardım sayfasında ve README'de aynı cümleyle.
- **İçerme eşleşmesi yanlış pozitif:** ≥5 karakter sınırı ve en uzun kod kuralı; önizleme her eşleşmeyi ürün adıyla gösterir (kullanıcı görür).
- **Yarım parti çöpü:** Medya Havuzu'nda gizli, 24 saatte süpürülür; disk kullanımı geçici.
- **Geri almada dosya silme:** yalnızca bu partide yüklenen ve başka üründe kullanılmayan ekler; kullanıcı aynı dosyayı formdan ikinci ürüne eklediyse dosya kalır, yalnızca galeriden çıkar.
- **LiteSpeed/Cloudflare:** mevcut mekanizma; apply yönlendirmeyle bittiği için temizlik otomatik.
- **`nwcs_product_site_status` maliyeti:** liste süzgeci "Hiçbir sitede görünmeyen" 320 ürün × 2 site, statik önbellekli ayarlarla önemsiz; `nwcs_site_products` yalnızca formda (tek ürün) çağrılır.

## 13. Açık sorular (varsayılanla)

1. **Alt metin** uygulamada boşsa ürün adı yazılsın mı? — Evet (SEO için boştan iyi; Medya Havuzu'ndan değiştirilebilir).
2. **Ek başlığı** "<KOD> görsel NN" biçimi mi, özgün dosya adı mı? — Kod biçimi (mevcut veriyle aynı; aramada kod çalışır).
3. **"Eksik: fotoğraf"** örnek görselli ürünü (ornek-urun-*.png) fotoğraflı sayar. — Evet (basit; Koçist örnek görselleri zaten yer tutucu kuralıyla sayfada gizleniyor).
4. **İçerme eşleşmesi** (kod adın ortasında) açık mı? — Evet, ≥5 karakter kodlar için.
5. **Yatay/oransız fotoğraf** engellensin mi? — Hayır, yalnızca bilgi satırı.

## 14. Roller

1. **Fable 5.1 HIGH** — mimar (bu belge).
2. **Opus 5.5 MEDIUM** — uygulama, `frontend-design` becerisiyle; §2–§8 bağlayıcı; yalnızca §9'daki dosyalar; commit yok; plana ters düşen somut bulguda durup bildirir.
3. **Yeni Fable 5.1 HIGH** — bağımsız inceleme (eşleme kuralları, yükleme sınırları, geri alma bütünlüğü, a11y, metin dürüstlüğü, regresyon diff'leri).
4. **Opus 5.5 MEDIUM** — düzeltme + doğrulama listesi (§11).
