<?php
/**
 * Ornek blog yazilari: firmanin mevcut blogundaki basliklar, tarihler,
 * kategoriler ve ozetler.
 *
 * Saf dizi; WordPress'e bagimli degil. Ilk kurulum (inc/blog.php) yazilari
 * buradan olusturur, panel de her yazinin gizli sayfasini ('yazi-<slug>',
 * content-manifest.php) buradan kurar; alanlar yazinin bugunku metniyle dolu
 * gelir.
 *
 * Metin (body): bos satirla ayrilan her blok bir paragraf. Tam metinler
 * henuz yok; govdede ozet ve "hazirlaniyor" notu var. Not paragrafi sitede
 * soluk basilir (kocist_blog_body_html).
 *
 * Dosya birden cok kez okunur (kurulum, panel manifesti); not sabiti bir
 * kez tanimlanir.
 */

defined( 'KOCIST_BLOG_PENDING' ) || define( 'KOCIST_BLOG_PENDING', 'Bu yazının tam metni hazırlanıyor.' );

return array(
	array(
		'slug'     => 'ispm-15-belgeli-ahsap-sandik-ve-kafes-uretimi',
		'title'    => 'ISPM 15 Belgeli Ahşap Sandık ve Kafes Üretimi – Çatalca ve Çevresi',
		'excerpt'  => 'Ahşap ambalajda uluslararası ısıl işlem standardının kapsamı, işaretleme ve sevkiyat etkileri.',
		'body'     => 'Ahşap ambalajda uluslararası ısıl işlem standardının kapsamı, işaretleme ve sevkiyat etkileri.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-01-12 10:00:00',
		'category' => 'İhracat',
	),
	array(
		'slug'     => 'kontrplak-secim-rehberi',
		'title'    => 'Kontrplak Seçim Rehberi',
		'excerpt'  => 'Kalınlık, sınıf ve tutkal tipine göre projenize uygun kontrplak seçimi.',
		'body'     => 'Kalınlık, sınıf ve tutkal tipine göre projenize uygun kontrplak seçimi.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-02-03 10:00:00',
		'category' => 'Kontrplak',
	),
	array(
		'slug'     => 'osb-mi-kontrplak-mi',
		'title'    => 'OSB mi, Kontrplak mı?',
		'excerpt'  => 'Kalıp ve iskele uygulamalarında OSB-3/4 ile kontrplak karşılaştırması.',
		'body'     => 'Kalıp ve iskele uygulamalarında OSB-3/4 ile kontrplak karşılaştırması.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-02-18 10:00:00',
		'category' => 'OSB',
	),
	array(
		'slug'     => 'ispm-15-palet-uretiminde-dikkat-edilecekler',
		'title'    => 'ISPM-15 Palet Üretiminde Dikkat Edilecekler',
		'excerpt'  => 'Fırınlama, nem kontrolü ve standarda uygun işaretleme ile sorunsuz ihracat.',
		'body'     => 'Fırınlama, nem kontrolü ve standarda uygun işaretleme ile sorunsuz ihracat.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-03-05 10:00:00',
		'category' => 'Ahşap Palet',
	),
	array(
		'slug'     => 'ahsap-sandik-ile-guvenli-ambalaj-cozumleri',
		'title'    => 'Ahşap Sandık ile Güvenli Ambalaj Çözümleri',
		'excerpt'  => 'Hassas ekipman ve ihracat sevkiyatlarında sandık tasarım ipuçları.',
		'body'     => 'Hassas ekipman ve ihracat sevkiyatlarında sandık tasarım ipuçları.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-03-22 10:00:00',
		'category' => 'Ahşap Sandık',
	),
	array(
		'slug'     => 'lojistikte-ahsap-ambalajin-rolu',
		'title'    => 'Lojistikte Ahşap Ambalajın Rolü',
		'excerpt'  => 'Tedarik zincirinde palet ve sandık standardizasyonunun operasyonel etkileri.',
		'body'     => 'Tedarik zincirinde palet ve sandık standardizasyonunun operasyonel etkileri.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-04-10 10:00:00',
		'category' => 'Lojistik',
	),
	array(
		'slug'     => 'ahsap-palet-fiyatlari-ve-ispm-15-uygulamalari',
		'title'    => 'Ahşap Palet Fiyatları ve ISPM-15 Uygulamaları',
		'excerpt'  => 'Ahşap palet, palet fiyatları ve ISPM 15 ısıl işlem (HT) süreçleri; ihracat uyumu ve maliyet planı.',
		'body'     => 'Ahşap palet, palet fiyatları ve ISPM 15 ısıl işlem (HT) süreçleri; ihracat uyumu ve maliyet planı.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-04-20 10:00:00',
		'category' => 'Ahşap Palet',
	),
	array(
		'slug'     => 'ahsap-sandik-fiyatlari-ve-ambalaj-sandigi-secimi',
		'title'    => 'Ahşap Sandık Fiyatları ve Ambalaj Sandığı Seçimi',
		'excerpt'  => 'Ahşap sandık ve ambalaj sandığı fiyatlarını belirleyen etmenler; ISPM 15 uyumu ve koruma çözümleri.',
		'body'     => 'Ahşap sandık ve ambalaj sandığı fiyatlarını belirleyen etmenler; ISPM 15 uyumu ve koruma çözümleri.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-04-20 11:00:00',
		'category' => 'Ahşap Sandık',
	),
	array(
		'slug'     => 'kereste-fiyatlari-ve-cesitleri',
		'title'    => 'Kereste Fiyatları ve Çeşitleri',
		'excerpt'  => 'Kereste fiyatları ve kereste çeşitleri: inşaatlık, doğramalık, mobilyalık ve ısıl işlem kereste.',
		'body'     => 'Kereste fiyatları ve kereste çeşitleri: inşaatlık, doğramalık, mobilyalık ve ısıl işlem kereste.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-04-21 10:00:00',
		'category' => 'Kereste',
	),
	array(
		'slug'     => 'kontrplak-ve-osb-plaka-secim-kilavuzu',
		'title'    => 'Kontrplak ve OSB Plaka Seçim Kılavuzu',
		'excerpt'  => 'Kontrplak ile OSB plaka karşılaştırması: WBP tutkal, sınıf ve kalınlık seçimi; uygulama rehberi.',
		'body'     => 'Kontrplak ile OSB plaka karşılaştırması: WBP tutkal, sınıf ve kalınlık seçimi; uygulama rehberi.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-04-22 10:00:00',
		'category' => 'Kontrplak',
	),
	array(
		'slug'     => 'ahsap-dekorasyon-kamelya-salincak-ve-kopek-kulubesi',
		'title'    => 'Ahşap Dekorasyon: Kamelya, Salıncak ve Köpek Kulübesi',
		'excerpt'  => 'Ahşap kamelya, ahşap salıncak ve ahşap köpek kulübesi seçiminde malzeme, ölçü ve bakım ipuçları.',
		'body'     => 'Ahşap kamelya, ahşap salıncak ve ahşap köpek kulübesi seçiminde malzeme, ölçü ve bakım ipuçları.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-04-25 10:00:00',
		'category' => 'Ahşap Dekorasyon',
	),
	array(
		'slug'     => 'tomruk-fiyatlari-olculer-siniflar-ve-kalite',
		'title'    => 'Tomruk Fiyatları: Ölçüler, Sınıflar ve Kalite',
		'excerpt'  => 'Tomruk fiyatları; çap sınıfı, boy, tür (çam, ladin) ve kaliteye göre belirlenir.',
		'body'     => 'Tomruk fiyatları; çap sınıfı, boy, tür (çam, ladin) ve kaliteye göre belirlenir.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2025-12-09 10:00:00',
		'category' => 'Kereste',
	),
	array(
		'slug'     => 'ahsap-sandik-fiyatlari-2026',
		'title'    => 'Ahşap Sandık Fiyatları 2026 – ISPM 15 Belgeli Özel Üretim Çözümleri',
		'excerpt'  => '2026 yılında ahşap sandık fiyatları; ölçü, ahşap türü, ISPM 15 belgesi ve kullanım amacına göre değişkenlik göstermektedir. Bu yazıda Koçist Orman Ürünleri olarak fiyatları etkileyen tüm faktörleri ve doğru ahşap sandık seçimini detaylıca ele alıyoruz.',
		'body'     => '2026 yılında ahşap sandık fiyatları; ölçü, ahşap türü, ISPM 15 belgesi ve kullanım amacına göre değişkenlik göstermektedir. Bu yazıda Koçist Orman Ürünleri olarak fiyatları etkileyen tüm faktörleri ve doğru ahşap sandık seçimini detaylıca ele alıyoruz.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2026-01-20 10:00:00',
		'category' => 'Ahşap Ambalaj',
	),
	array(
		'slug'     => 'ahsap-cardak-modelleri-ve-fiyatlari',
		'title'    => 'Ahşap Çardak Modelleri ve Fiyatları – Bahçeniz İçin Doğal Çözümler',
		'excerpt'  => 'Ahşap çardak modelleri; bahçe, villa, site ve sosyal alanlarda hem estetik hem de fonksiyonel çözümler sunar. Bu yazıda ahşap çardak nedir, hangi modeller tercih edilir, fiyatları neler etkiler ve Çatalca çevresinde neden ahşap çardak daha avantajlıdır detaylıca ele alıyoruz.',
		'body'     => 'Ahşap çardak modelleri; bahçe, villa, site ve sosyal alanlarda hem estetik hem de fonksiyonel çözümler sunar. Bu yazıda ahşap çardak nedir, hangi modeller tercih edilir, fiyatları neler etkiler ve Çatalca çevresinde neden ahşap çardak daha avantajlıdır detaylıca ele alıyoruz.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2026-01-20 11:00:00',
		'category' => 'Ahşap Dekorasyon',
	),
	array(
		'slug'     => 'ihracat-sandigi-fiyatlari-2026-istanbul',
		'title'    => 'İhracat Sandığı Fiyatları 2026 İstanbul – ISPM 15 Belgeli Özel Üretim',
		'excerpt'  => 'İhracat sandığı fiyatları 2026 yılında; üretim maliyetleri, ihracat standartları ve taşıma gereksinimlerine bağlı olarak değişiklik göstermektedir.',
		'body'     => 'İhracat sandığı fiyatları 2026 yılında; üretim maliyetleri, ihracat standartları ve taşıma gereksinimlerine bağlı olarak değişiklik göstermektedir.' . "\n\n" . KOCIST_BLOG_PENDING,
		'date'     => '2026-01-26 10:00:00',
		'category' => 'Ahşap Sandık',
	),
);
