<?php
/**
 * ithalkeresteci alan manifesti.
 *
 * Alan yapisi ana temadan (kereste-base/manifest.php) gelir; burada yalnizca
 * bu siteye ozgu varsayilan metinler, urunler ve ana sayfa modulleri var.
 *
 * Metinler kocist.com.tr'deki ithal kereste, insaatlik kereste, kalas ve
 * plywood sayfalarindan kisaltilarak turetildi. Adres, telefon ve e-posta
 * kocist.com.tr'de yazdigi gibidir (sahibi SEO ve GEO sekmesinden duzeltir).
 */

$build = require dirname( __DIR__ ) . '/kereste-base/manifest.php';

$address = "Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1\n34494 Çatalca / İstanbul";

return $build(
	array(
		'key'            => 'ithalkeresteci',
		'label'          => 'Koçist · ithalkeresteci.com',
		'logo'           => array( 'İthal', 'Keresteci' ),

		'contact'        => array(
			'phone_label'  => '0549 648 19 19',
			'phone_url'    => 'tel:+905496481919',
			// Kaynak sitedeki WhatsApp baglantisinda numara yok; telefonla ayni
			// hat varsayildi. Sahibi farkli bir hat verirse panelden degisir.
			'whatsapp_url' => 'https://wa.me/905496481919',
			'email'        => 'info@kocist.com.tr',
			'address'      => $address,
			'hours'        => '08:00 – 19:00',
			'map_url'      => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1 Çatalca İstanbul' ),
		),

		'footer_tagline' => 'İthal kereste, inşaatlık ve çatılık kereste, kalas ve plywood. Koçist Orman Ürünleri güvencesiyle, toptan alıma uygun.',

		'family'         => array(
			array( 'label' => 'Kavak Keresteci', 'note' => 'Hafif ve ekonomik kavak kereste', 'url' => 'https://kavakkeresteci.com' ),
			array( 'label' => 'Koçist Orman Ürünleri', 'note' => 'Kereste, ambalaj, dekorasyon', 'url' => 'https://www.kocist.com.tr' ),
			array( 'label' => 'İstanbul Paletçi', 'note' => 'Ahşap palet ve Euro palet', 'url' => 'https://istanbulpaletci.com' ),
			array( 'label' => 'ahsapkasa.com', 'note' => 'Ahşap sandık ve kafes', 'url' => 'https://ahsapkasa.com' ),
		),

		'home_sections'  => array( 'products', 'supply', 'cousin', 'faq', 'quote' ),

		'hero'           => array(
			'title'      => "Güçlü, dayanıklı,\nuzun ömürlü ithal kereste",
			'lead'       => 'İnşaat, çatı ve mobilya için farklı ölçü ve türlerde ithal kereste. Toptan alıma uygun, İstanbul teslim.',
		),

		'products_text'  => 'Yapı ve mobilya işleri için ithal ve yerli çam kereste, kalas ve plywood.',
		'products_lead'  => 'Tür ve ölçü seçenekleriyle ithal kereste, inşaatlık kereste, kalas ve plywood.',

		'supply'         => array(
			'title'  => 'Toptan tedarik, hızlı sevkiyat',
			'text'   => 'Güvenilir ithalat ve kalite kontrolünden geçen keresteyi stoktan hazırlayıp sevk ediyoruz. Büyük siparişlerde toptan fiyat avantajı.',
			'points' => array(
				array( 'value' => '3–5 iş günü', 'label' => 'normal koşullarda sevkiyat' ),
				array( 'value' => 'Toptan', 'label' => 'alıma uygun stok' ),
				array( 'value' => 'İstanbul', 'label' => 'teslim' ),
			),
		),

		'cousin'         => array(
			'title'  => 'Hafif ve ekonomik kereste mi gerekiyor?',
			'text'   => 'Ambalaj, sandık ve palet üretiminde kavak kereste daha hafif ve daha ekonomik bir seçenek.',
			'button' => 'Kavak keresteye göz atın',
			'url'    => 'https://kavakkeresteci.com',
		),

		'faq'            => array(
			array( 'question' => 'Sevkiyat süresi nedir?', 'answer' => 'Normal koşullarda 3–5 iş günü içinde sevkiyat yapılır.' ),
			array( 'question' => 'Ödeme seçenekleri nelerdir?', 'answer' => 'Banka havalesi, EFT ve gerekli durumlarda kapıda ödeme seçenekleri sunulur.' ),
			array( 'question' => 'Kereste metreküpü nasıl hesaplanır?', 'answer' => 'Kalınlık ve genişlik santimetreden metreye çevrilir, boy ve adetle çarpılır. Örneğin 5 × 10 cm kesitli, 4 m boyunda 100 adet kereste 0,05 × 0,10 × 4 × 100 = 2 m³ eder.' ),
			array( 'question' => 'İstediğim ölçüde kesim yapılıyor mu?', 'answer' => 'Evet. Projenize uygun ebatlarda kesim ve hazırlık yapılabilir; istenen ölçü birebir kesilir.' ),
			array( 'question' => 'Online sipariş verebilir miyim?', 'answer' => 'Şu anda online satış yok. Ölçü ve adetle birlikte telefonla ya da teklif formuyla sipariş verebilirsiniz.' ),
		),

		'about'          => array(
			'lead'   => 'Koçist Orman Ürünleri’nin kereste sitesi: ithal ve yerli kereste, kalas ve plywood tedariki.',
			'p1'     => 'Koçist Grup, orman ürünleri alanında 37 yıldır hizmet veriyor. Her ölçüde kereste, palet, sandık ve kafesi ihtiyacınıza göre hazırlıyor; mevcut ölçülerinizi ya da proje çiziminizi alarak en hızlı şekilde üretime geçiyoruz.',
			'p2'     => 'Kaliteli, dürüst ve hızlı bir satış ilkesiyle, en kaliteli malzemeyi en kısa zamanda ve en uygun fiyatla müşterimize ulaştırmayı amaçlıyoruz.',
			'values' => array(
				array( 'title' => 'Doğru ölçü', 'text' => 'İstenen ölçü birebir kesilir; 5 × 10 yerine 4,8 × 9,8 gelmez.' ),
				array( 'title' => 'Kalite kontrol', 'text' => 'İthal keresteler güvenilir tedarikçilerden, kalite kontrolünden geçerek gelir.' ),
				array( 'title' => 'Zamanında teslim', 'text' => 'Ürünleri zamanında ve uygun fiyatla teslim etmek kalite politikamızın temeli.' ),
			),
		),

		'seo'            => array(
			'name'        => 'İthal Keresteci',
			'legal_name'  => 'Koçist Orman Ürünleri İnş. ve İnş. Yap. Malz. San. Tic. Ltd. Şti.',
			'description' => 'İstanbul’da ithal kereste, inşaatlık ve çatılık kereste, kalas ve plywood tedariki. Koçist Orman Ürünleri’nin kereste sitesi; toptan alıma uygun, İstanbul teslim.',
			'phone'       => '+90 549 648 19 19',
			'email'       => 'info@kocist.com.tr',
			'street'      => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1',
			'district'    => 'Çatalca',
			'city'        => 'İstanbul',
			'postal_code' => '34494',
			'country'     => 'TR',
			// Kardes siteler sameAs degil; grup iliskisi parentOrganization ile.
			'same_as'     => array(),
			'parent_name' => 'Koçist Orman Ürünleri',
			'parent_url'  => 'https://www.kocist.com.tr',
		),

		'products'       => array(
			'ithal_kereste' => array(
				'slug'  => 'ithal-kereste',
				'name'  => 'İthal Kereste',
				'mark'  => 'İTHAL',
				'short' => 'Yüksek taşıma kapasitesi ve düzgün yüzey; yapı ve mobilya işleri için.',
				'lead'  => 'Seçkin ağaç türlerinden elde edilen, sağlam yapısı ve düzgün yüzeyiyle yapı ve mobilya işlerinin temel malzemesi.',
				'body'  => "İthal kereste; yüksek taşıma kapasitesi, düzgün yüzey yapısı ve işlenmeye uygun formu sayesinde profesyonel kullanım için idealdir.\n\nİnşaat projelerinde taşıyıcı ve destek uygulamalarında, mobilya üretiminde, iç dekorasyon ve dış mekân ahşap uygulamalarında kullanılır.",
				'specs' => array(
					array( 'label' => 'Standart', 'value' => 'Uluslararası kalite standartlarına uygun' ),
					array( 'label' => 'Ölçü ve tür', 'value' => 'Farklı kereste ölçüleri ve tür seçenekleri' ),
					array( 'label' => 'Kullanım', 'value' => 'İnşaat, mobilya, iç dekorasyon, dış mekân' ),
					array( 'label' => 'Satış', 'value' => 'Toptan alıma uygun' ),
				),
			),
			'insaatlik'     => array(
				'slug'  => 'insaatlik-catilik-kereste',
				'name'  => 'İnşaatlık ve Çatılık Kereste',
				'mark'  => '5×10 · 10×10',
				'short' => 'Çatı karkası, beton kalıbı ve yapı iskeleti için çam kereste.',
				'lead'  => 'Yapıların taşıyıcı iskeletini oluşturan, lif yapısı düzgün ve mukavemeti yüksek çam kereste.',
				'body'  => "Çatı karkaslarında, beton kalıplarında ve açık alan yapı elemanlarında kullanılır. Tomruktan seçilen, lif yapısı düzgün çam ağaçlarından elde edilir.\n\nReçineli yapısı sayesinde neme ve kurtlanmaya karşı doğal dirençlidir. Şerit testerede hassas kesildiği için dönme ve çarpılma en azdadır.",
				'specs' => array(
					array( 'label' => 'Ağaç türü', 'value' => 'Sarıçam, karaçam, ladin' ),
					array( 'label' => 'İthal seçenek', 'value' => 'Ukrayna ve Rus çamı' ),
					array( 'label' => 'Kesit', 'value' => '5 × 10, 10 × 10 cm, tahta' ),
					array( 'label' => 'Kalite', 'value' => '1. ve 2. sınıf' ),
					array( 'label' => 'Kurutma', 'value' => 'Doğal veya fırınlı' ),
					array( 'label' => 'Yüzey', 'value' => 'Ham veya silinmiş (planyalı)' ),
				),
			),
			'kalas'         => array(
				'slug'  => 'kalas',
				'name'  => 'Kalas',
				'mark'  => 'ÇAM · GÜRGEN',
				'short' => 'Fırınlanmış, nem dengesi sağlanmış çam ve gürgen kalas.',
				'lead'  => 'Doğal dokusu ve dayanıklılığıyla inşaat, mobilya ve dış mekân projelerinde kullanılan ahşap kalas.',
				'body'  => "Uzun ömürlü kullanım için fırınlanmış ve nem dengesi sağlanmıştır. Yüzeyi pürüzsüz, işlemesi kolaydır.\n\nPergola, kamelya, salıncak, mobilya ve dekoratif uygulamalarda kullanılır.",
				'specs' => array(
					array( 'label' => 'Malzeme', 'value' => 'Çam veya gürgen' ),
					array( 'label' => 'Kurutma', 'value' => 'Fırınlanmış, nem dengeli' ),
					array( 'label' => 'Yüzey', 'value' => 'Pürüzsüz, kolay işlenir' ),
					array( 'label' => 'Kullanım', 'value' => 'Pergola, kamelya, mobilya, dekoratif' ),
				),
			),
			'plywood'       => array(
				'slug'  => 'plywood-kontrplak',
				'name'  => 'Plywood (Kontrplak)',
				'mark'  => 'HUŞ · ASPEN',
				'short' => 'Çapraz katmanlı, eğilme ve çatlamaya dayanıklı ahşap levha.',
				'lead'  => 'İnce ahşap katmanlarının lif yönleri çapraz gelecek şekilde preslenmesiyle üretilen, yüksek mukavemetli levha.',
				'body'  => "Çapraz katmanlı yapısı sayesinde eğilme, çatlama ve dönmeye karşı dayanıklıdır. Film kaplı seçenekler suya dayanıklıdır.\n\nİnşaat kalıp sistemlerinde, mobilya ve endüstriyel uygulamalarda kullanılır.",
				'specs' => array(
					array( 'label' => 'Tür', 'value' => 'Aspen, huş, ithal kontrplak' ),
					array( 'label' => 'Yüzey', 'value' => 'Filmsiz veya film kaplı' ),
					array( 'label' => 'Dayanım', 'value' => 'Suya ve neme dayanıklı seçenekler' ),
					array( 'label' => 'Ölçü', 'value' => 'Standart ve projeye özel ebat' ),
					array( 'label' => 'Kullanım', 'value' => 'İnşaat kalıbı, mobilya, endüstri' ),
				),
			),
		),
	)
);
