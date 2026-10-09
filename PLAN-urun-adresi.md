# Ürün sayfası adresi: site başına düzenlenebilir — Plan

8 Ekim 2026. Durum: öneri, onay bekliyor.

## İstek
- Ürün adresi `/urun/<uzanti>/` ilk eklendiğinde ürün adından otomatik oluşur (bugünkü gibi).
- Sonradan **her site için ayrı ayrı** panelden değiştirilebilir.
- Excel'e eklenmez.

## Bugün
- Adres = havuz ürününün `post_name`'i. İki sitede de (WK ve Koçist) aynı. Bir kez oluşur, hiç değişmez.
- Rotalama `nwcs_product_template()` (products.php ~880) içinde: `/urun/<slug>/` → `nwcs_site_product_by_slug()`, yani sitenin ürünleri içinde `slug` eşleşmesi.
- Bütün bağlantılar şu iki kaynaktan geliyor:
  - `nwcs_product_url( $product['slug'] )`
  - site ürün satırındaki `url` alanı (kartlar, menüler, site haritası, canonical, JSON-LD, Koçist teklif formu).
- Site başına istisnalar zaten var: `nwcs_products_overrides` (ad, kısa açıklama, fiyat, görsel, gizle). Bunlar ürün formundaki "Özelleştirmeler" kutusundan düzenleniyor (`includes/admin/overrides.php`).

## Tasarım
Yeni bir sistem kurulmuyor; mevcut site istisnalarına bir alan daha ekleniyor.

1. **Veri:** `nwcs_products_overrides[urun_id]` kaydına `slug` alanı eklenir. Boş bırakılırsa otomatik adres (havuzdaki) kullanılır.
   - Ayrıca `slug_old` listesi tutulur: bu sitede daha önce kullanılmış adresler, en fazla 20.
2. **Uygulama:** `nwcs_site_products()` istisnayı uygularken `slug` doluysa ürün satırının `slug` ve `url` alanlarını bununla değiştirir.
   - Havuzdaki asıl adres `pool_slug` olarak satırda kalır.
   - Böylece kartlar, menüler, site haritası, canonical, JSON-LD ve formlar **kendiliğinden** yeni adresi kullanır. Temalarda değişiklik gerekmez.
3. **Eski adres → yeni adres (301):** `nwcs_product_template()` bir adresi bulamazsa, 404 vermeden önce sitenin ürünlerinde o adresi arar: `pool_slug` ya da `slug_old` içinde var mı?
   - Varsa ürünün güncel adresine **301** yönlendirir.
   - Google kaydı ve eski paylaşılan bağlantılar kaybolmaz. Yönlendirme listesine elle kayıt eklemek gerekmez.
4. **Kurallar (kayıtta sunucu kontrolü):**
   - Adres temizlenir: Türkçe harfler sadeleşir (ş→s, ı→i…), boşluklar `-` olur, küçük harfe çevrilir (`sanitize_title` + Türkçe katlama).
   - Aynı sitede başka bir ürünün adresiyle (otomatik ya da özel) çakışamaz. Sitenin bir sayfasının adresiyle de çakışamaz (örneğin `iletisim`).
   - Hata mesajı düz Türkçe olur: "Bu adres bu sitede başka bir ürünün adresi: <ad>. Başka bir adres yazın."
   - Bir başka ürünün eski adreslerinden biri yeni adres olarak alınırsa, o eski adres diğer üründen düşer. Böylece yönlendirme karışmaz.
5. **Panel (tek yer):** Ürün Havuzu → ürün formu → "Özelleştirmeler" kutusunda her site için yeni bir **"Sayfa adresi"** satırı:

```
WOOD KOCIST   woodkocist.com.tr/urun/ [verandali-kopek-kulubesi      ]  Otomatik: w-dog-v05-...  [Otomatiğe dön]
              Eski adres (w-dog-v05-...) yeni adrese yönlenir.
```

   - Kaydedince ürün formunun üstündeki durum şeridindeki "Sitede gör ↗" bağlantısı yeni adresi gösterir.
   - "Otomatiğe dön" düğmesi özel adresi siler. Özel adres de `slug_old`'a eklenir, o da yönlenir.
6. **Önbellek:** kayıttan sonra `nwcs_pool_flush_cache()` ve site önbelleği temizliği çalışır (mevcut akış).

## Kapsam dışı
- Excel'e sütun eklenmez (kullanıcı kararı).
- `/urun/` kökü değişmez.
- İçerik Stüdyosu'ndaki ürün satırında ayrı bir adres alanı olmaz. Orada yalnızca adres gösterilir ve "değiştir ↗" bağlantısı ürün formuna götürür; aynı veri için ikinci form olmaz.

## Etkilenen dosyalar
- `includes/products.php`: istisna temizleme, `nwcs_site_products` slug/url, `pool_slug`, `nwcs_product_template` içinde 301.
- `includes/admin/overrides.php`: formdaki "Sayfa adresi" alanı, doğrulama, çakışma kontrolü, `slug_old` yönetimi.
- `assets/overrides.js` (varsa) ve `admin.css`: alan ve hata gösterimi.
- `includes/admin/fields.php`: Stüdyo ürün satırında adres gösterimi ve "değiştir ↗" bağlantısı.
- README ve DECISIONS.

## Doğrulama
1. Özel adres verilir → kartlar, site haritası, canonical ve JSON-LD yeni adresi gösterir, sayfa 200 döner.
2. Eski adres yeni adrese 301 ile yönlenir.
3. Diğer sitede ürünün adresi değişmez.
4. Çakışan adres reddedilir; sayfa adresiyle çakışma da reddedilir.
5. "Otomatiğe dön" → otomatik adres 200 döner, özel adres 301 ile yönlenir.
6. Koçist teklif formu (ürün adresiyle eşleşiyor) yeni adresle çalışır.
7. Ürün çöpe gidip geri gelince adres korunur.
8. E_ALL temiz, `php -l` temiz.

## İş büyüklüğü
Küçük-orta (yarım gün civarı). Yeni veri modeli yok; mevcut istisna ve yönlendirme akışı kullanılıyor.

## Roller
Mimar ve inceleme ana oturum (Fable kotası nedeniyle). Uygulama Opus. Commit kullanıcı onayıyla.
