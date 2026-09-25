<?php
/**
 * istanbulpaletcivi temasi alan manifesti.
 *
 * Saf bir dizi dondurur; hicbir WordPress fonksiyonuna bagimli degildir.
 *
 * Metinler istanbulpaletcivi.com'un kendi sayfalarindan alindi. Teknik olarak
 * yanlis anlatimlar duzeltildi (dokme civi = dizisiz civi; tele dizili civi =
 * serit tabancasiyla cakilan tel dizili civi); firmaya ozel yeni bir iddia
 * (olcu, stok, kapasite, sertifika) eklenmedi. Grup bilgisi diger 7 siteyle
 * ayni: Kestanelik / Catalca adresi, 50 yil.
 *
 * Alan turleri: text, textarea, url, image, repeater
 */

/*
 * Uc urun sayfasi ayni alan yapisini paylasir.
 */
$product_page = static function ( string $label, string $path, array $card, array $detail ): array {
	return array(
		'label'      => $label,
		'path'       => $path,
		// Tema bu isaretle urun sablonunu secer.
		'template'   => 'product',
		'seo_source' => array(
			'title'       => 'card.name',
			'description' => 'detail.lead',
			'image'       => 'card.image',
			'type'        => 'Product',
		),
		'components' => array(

			'card' => array(
				'label'  => 'Ürün Kartı (listelerde görünür)',
				'fields' => array(
					'name'  => array( 'label' => 'Ürün Adı', 'type' => 'text', 'default' => $card['name'] ),
					'short' => array( 'label' => 'Kısa Açıklama', 'type' => 'textarea', 'default' => $card['short'] ),
					'tool'  => array( 'label' => 'Nasıl Çakılır (tek satır)', 'type' => 'text', 'default' => $card['tool'] ),
					'image' => array( 'label' => 'Ürün Görseli', 'type' => 'image', 'default' => 0 ),
				),
			),

			'detail' => array(
				'label'  => 'Ürün Sayfası',
				'fields' => array(
					'lead' => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => $detail['lead'] ),
					'body' => array( 'label' => 'Ürün Açıklaması (paragrafları boş satırla ayırın)', 'type' => 'textarea', 'default' => $detail['body'] ),
					'uses' => array(
						'label'   => 'Kullanıldığı İşler',
						'type'    => 'repeater',
						'max'     => 8,
						'fields'  => array(
							'text' => array( 'label' => 'İş', 'type' => 'text' ),
						),
						'default' => array_map( static fn( string $text ): array => array( 'text' => $text ), $detail['uses'] ),
					),
				),
			),
		),
	);
};

$phone_label  = '+90 212 648 10 90';
$phone_url    = 'tel:+902126481090';
$mobile_label = '+90 532 374 98 32';
$mobile_url   = 'tel:+905323749832';
$whatsapp_url = 'https://wa.me/905323749832';
$email        = 'info@kocist.com.tr';
$address      = "Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1\nÇatalca, İstanbul";
$address_line = 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1, Çatalca, İstanbul';
$price_note   = 'Ölçü, stok ve fiyat bilgisi için bizi arayın ya da WhatsApp’tan yazın. Tabancanızın modelini ve çakacağınız tahtaların kalınlığını söylemeniz yeterli.';

return array(
	'site_key'          => 'istanbulpaletcivi',
	'site_label'        => 'Koçist · istanbulpaletcivi.com',
	// Panelde gorunen kisa ad (alan adi olmadan).
	'panel_label'       => 'İstanbul Palet Çivi',

	'seo_site_defaults' => array(
		'name'        => 'İstanbul Palet Çivi',
		'legal_name'  => '',
		'description' => 'Rulo, tele dizili ve dökme palet çivisi. İstanbul başta olmak üzere tüm Türkiye’ye satış ve teslimat. Koçist Grup kuruluşu; grup 50 yıldır orman ürünleri alanında.',
		'phone'       => $phone_label,
		'email'       => $email,
		'street'      => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1',
		'district'    => 'Çatalca',
		'city'        => 'İstanbul',
		'postal_code' => '',
		'country'     => 'TR',
		'same_as'     => array(),
		'parent_name' => 'Koçist Orman Ürünleri',
		'parent_url'  => 'https://www.kocist.com.tr',
	),

	'pages'             => array(

		/* ---------------------------------------------------------- *
		 * Tum sayfalarda ortak: ust menu ve alt bilgi
		 * ---------------------------------------------------------- */
		'global' => array(
			'label'      => 'Tüm Sayfalar (Üst Menü, Alt Bilgi)',
			'path'       => '/',
			'components' => array(

				'header' => array(
					'label'  => 'Üst Menü',
					'fields' => array(
						'logo_image'     => array( 'label' => 'Logo', 'type' => 'image', 'default' => 0 ),
						'logo_text'      => array( 'label' => 'Firma Adı', 'type' => 'text', 'default' => 'İstanbul Palet Çivi' ),
						'menu'           => array(
							'label'   => 'Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 7,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı Adresi', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Anasayfa', 'url' => '/' ),
								array( 'label' => 'Hakkımızda', 'url' => '/hakkimizda/' ),
								array( 'label' => 'Palet Çivileri', 'url' => '/palet-civileri/' ),
								array( 'label' => 'Blog ve Haberler', 'url' => '/haberler-blog/' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),
						'address'        => array( 'label' => 'Üst Şerit Adresi', 'type' => 'text', 'default' => $address_line ),
						'phone_label'    => array( 'label' => 'Telefon (görünen)', 'type' => 'text', 'default' => $phone_label ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => $phone_url ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Düğmesi', 'type' => 'text', 'default' => 'WhatsApp' ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => $whatsapp_url ),
						'email'          => array( 'label' => 'E-posta', 'type' => 'text', 'default' => $email ),
					),
				),

				'footer' => array(
					'label'  => 'Alt Bilgi',
					'fields' => array(
						'tagline'        => array( 'label' => 'Alt Bilgi Metni', 'type' => 'textarea', 'default' => 'Rulo, tele dizili ve dökme palet çivisi. Koçist Grup kuruluşudur; Çatalca’dan Türkiye’nin her yerine teslim ediyoruz.' ),
						'mobile_label'   => array( 'label' => 'Cep Telefonu (görünen)', 'type' => 'text', 'default' => $mobile_label ),
						'mobile_url'     => array( 'label' => 'Cep Telefonu Bağlantısı', 'type' => 'url', 'default' => $mobile_url ),
						'address'        => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => $address ),
						'copyright'      => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => 'İstanbul Palet Çivi · Koçist Grup' ),
						'external_label' => array( 'label' => 'Grup Bağlantısı Metni', 'type' => 'text', 'default' => 'kocist.com.tr' ),
						'external_url'   => array( 'label' => 'Grup Bağlantısı', 'type' => 'url', 'default' => 'https://www.kocist.com.tr' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Ana sayfa
		 * ---------------------------------------------------------- */
		'home' => array(
			'label'      => 'Ana Sayfa',
			'path'       => '/',
			'seo_source' => array(
				'title'       => 'hero.title',
				'description' => 'hero.lead',
				'image'       => 'hero.image',
			),
			'components' => array(

				'hero' => array(
					'label'  => 'Giriş',
					'fields' => array(
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Palet çivisi' ),
						'lead'       => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Rulo, tele dizili ve dökme palet çivisi. İstanbul başta olmak üzere tüm Türkiye’ye satış ve teslimat yapıyoruz.' ),
						'call_label' => array( 'label' => 'Arama Düğmesi', 'type' => 'text', 'default' => 'Telefonla sipariş' ),
						'wa_label'   => array( 'label' => 'WhatsApp Düğmesi', 'type' => 'text', 'default' => 'WhatsApp’tan yazın' ),
						'image'      => array( 'label' => 'Fotoğraf', 'type' => 'image', 'default' => 0 ),
						'image_note' => array( 'label' => 'Fotoğraf Notu (gerçek fotoğraf gelince silin)', 'type' => 'text', 'default' => 'Örnek görsel' ),
					),
				),

				'ruler' => array(
					'label'  => 'Sarı Cetvel Şeridi',
					'fields' => array(
						'text'  => array( 'label' => 'Metin', 'type' => 'text', 'default' => 'Palet çivi ürünlerini inceleyin ve sipariş verin' ),
						'label' => array( 'label' => 'Düğme', 'type' => 'text', 'default' => 'Palet çivileri' ),
					),
				),

				'products' => array(
					'label'  => 'Ürünler',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Üç çivi tipi, tabancanıza göre' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Hangi çivinin size uyduğu, elinizdeki tabancaya ve yaptığınız işe bağlı. Ölçü ve fiyat için arayın.' ),
						'more'  => array( 'label' => 'Ürün Bağlantısı Metni', 'type' => 'text', 'default' => 'Ürünü inceleyin' ),
					),
				),

				'uses' => array(
					'label'  => 'Kullanım Alanları',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Kullanım alanları' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Çivilerin kullanım alanlarını inceleyin.' ),
						'items' => array(
							'label'   => 'Alanlar',
							'type'    => 'repeater',
							'max'     => 9,
							'fields'  => array(
								'title' => array( 'label' => 'Alan', 'type' => 'text' ),
								'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'title' => 'Ambalajlama', 'text' => 'Ahşap paletlerin ve sandıkların yapımında çiviler kullanılır. Bu çiviler yükleri taşımak ve ambalajı güçlendirmek için kullanılır.' ),
								array( 'title' => 'Ahşap işleri', 'text' => 'Ahşap palet, kafes ve diğer ahşap ürünlerin imalatında palet çivisi kullanılır. Kullanılacak çivi ürüne göre farklılık gösterebilir.' ),
								array( 'title' => 'İnşaat sektörü', 'text' => 'Çiviler yapıların inşasında sıkça kullanılır. Ahşap karkas, kalıp ve çatı işlerinde ahşap elemanları bir arada tutar.' ),
								array( 'title' => 'Mobilya sektörü', 'text' => 'Ahşap mobilya üretiminde çiviler yaygın bir bağlama elemanıdır. Parçaları bir arada tutar; çoğu zaman sonradan gizlenir ya da dolgu malzemesiyle kapatılır.' ),
								array( 'title' => 'Marangozluk', 'text' => 'Ahşap işleri ve marangozluk projelerinde ahşap parçaları bir araya getirmek ve sabitlemek için kullanılır.' ),
								array( 'title' => 'Sanat ve el işleri', 'text' => 'Bazı sanat projelerinde ve el işlerinde malzemeleri bir arada tutar. Örneğin iplik ya da sicimle birbirine bağlanan çivilerle duvar süslemeleri yapılır.' ),
							),
						),
					),
				),

				'group' => array(
					'label'  => 'Koçist Grup Bandı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri olarak tüm Türkiye’ye palet çivi tedarik ediyoruz.' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'İstanbul Palet Çivi, Koçist Grup kuruluşudur. Grup 50 yıldır orman ürünleri alanında çalışıyor; ahşap palet, sandık ve kafes üretiyor.' ),
					),
				),

				'blog' => array(
					'label'  => 'Blog Bölümü',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Palet çivisi hakkında bilmeniz gerekenler' ),
						'more'  => array( 'label' => 'Tüm Yazılar Bağlantısı', 'type' => 'text', 'default' => 'Tüm yazılar' ),
					),
				),

				'contact' => array(
					'label'  => 'Sipariş Bölümü',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Sipariş ve fiyat' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Çivi tipini, ölçüyü ve adedi yazın; size en kısa sürede dönüş yapalım. Acil siparişte arayın.' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Palet Civileri (urunler sayfasi)
		 * ---------------------------------------------------------- */
		'products' => array(
			'label'      => 'Palet Çivileri',
			'path'       => '/palet-civileri/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İstanbul palet çivileri' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Rulo, tele dizili ve dökme çivi. Hepsi palet, sandık ve kafes üretiminde kullanılır; aralarındaki fark, nasıl çakıldıkları.' ),
					),
				),
				'help' => array(
					'label'  => 'Yardım Kutusu',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Size nasıl yardımcı olabiliriz?' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Hangi çivinin size uyduğundan emin değilseniz arayın; tabancanızın modelini ve yaptığınız işi söyleyin, doğru çiviyi birlikte seçelim.' ),
					),
				),
				'shared' => array(
					'label'  => 'Ürün Sayfalarında Ortak',
					'fields' => array(
						'price_note' => array( 'label' => 'Fiyat ve Ölçü Notu', 'type' => 'textarea', 'default' => $price_note ),
						'others'     => array( 'label' => 'Diğer Ürünler Başlığı', 'type' => 'text', 'default' => 'Diğer çivi tipleri' ),
					),
				),
			),
		),

		'rulo' => $product_page(
			'Ürün: Rulo Palet Çiviler',
			'/civiler/rulo-palet-civiler/',
			array(
				'name'  => 'Rulo Palet Çiviler',
				'short' => 'İnce tellerle birbirine bağlanıp rulo hâline getirilmiş çiviler. Palet ve sandık üretiminde en yaygın tip.',
				'tool'  => 'Rulo çivi tabancasıyla çakılır',
			),
			array(
				'lead' => 'Rulo palet çivisi, çivilerin ince tellerle birbirine bağlanıp rulo (bobin) hâline getirildiği çivi tipidir. Rulo çivi tabancasıyla hızlı ve seri çakılır.',
				'body' => "Rulo çiviler çelik telden üretilir. Çiviler yan yana dizilir, ince tellerle birbirine tutturulur ve düz bir rulo hâlinde sarılır. Rulo tabancanın haznesine takılır; tabanca her tetikte bir çiviyi ayırıp çakar.\n\nPalet ve sandık üretiminde en çok kullanılan çivi tipidir. Tek tek çakmaya göre çok daha hızlıdır ve seri üretimde işçilik süresini kısaltır. Ahşap döşeme, çit, çatı kaplaması ve kasa yapımında da kullanılır.\n\nGövdesi düz, halkalı ya da vida dişli olabilir. Halkalı ve vida dişli gövde ahşapta daha sıkı tutunur, çivinin zamanla gevşemesini azaltır. Çıplak (parlak) ve galvaniz kaplamalı seçenekler vardır; nemli ortamda ve dış mekânda galvaniz tercih edilir.\n\nÇivinin boyu ve kalınlığı, birleştirilecek tahtaların kalınlığına ve tabancanızın kabul ettiği ölçüye göre seçilir. Çakarken çivinin doğru yerleştiğinden emin olun, koruyucu gözlük ve eldiven kullanın.",
				'uses' => array( 'Palet üretimi ve onarımı', 'Sandık ve kafes', 'Ahşap döşeme', 'Çit ve çatı kaplaması', 'Mobilya imalatı' ),
			)
		),

		'tele' => $product_page(
			'Ürün: Tele Dizili Çiviler',
			'/civiler/tele-dizili-civiler/',
			array(
				'name'  => 'Tele Dizili Çiviler',
				'short' => 'İnce tellerle yan yana şerit hâlinde dizilmiş çiviler. Uzun gövdeli çivilerde ve kalın ahşap birleştirmede yaygın.',
				'tool'  => 'Şerit çivi tabancasıyla çakılır',
			),
			array(
				'lead' => 'Tele dizili çivi, çivilerin ince tellerle yan yana şerit hâlinde dizildiği çivi tipidir. Şerit çivi tabancasıyla çakılır.',
				'body' => "Çiviler düz bir şerit boyunca yan yana dizilir ve ince tellerle birbirine bağlanır. Şerit tabancanın şarjörüne yerleştirilir; tabanca her tetikte bir çiviyi teli koparak çakar. Şeritler genellikle açılıdır; açı, tabancanın şarjörüne uygun seçilmelidir.\n\nUzun gövdeli çivilerde yaygındır. Ahşap karkas ve çatı iskeleti, kalıp işleri, kasa ve palet üretimi gibi kalın ahşabın birleştirildiği işlerde kullanılır. Çivilerin düzenli dizilmesi, her çakışta aynı derinlikte ve sağlam bir bağlantı sağlar.\n\nRulo çiviye göre şarjörde daha az çivi taşır, ama uzun ve kalın çivilerde tabancayı daha dengeli tutar. Hangi tipi kullanacağınız elinizdeki tabancaya bağlıdır.\n\nGövde tipi (düz, halkalı), kaplama (çıplak, galvaniz) ve ölçü seçimi için bizi arayın; tabancanızın modelini söylemeniz yeterli.",
				'uses' => array( 'Ahşap karkas ve çatı iskeleti', 'Kalıp işleri', 'Kasa yapımı', 'Palet üretimi', 'Ahşap zemin döşeme' ),
			)
		),

		'dokme' => $product_page(
			'Ürün: Dökme Çiviler',
			'/civiler/dokme-civiler/',
			array(
				'name'  => 'Dökme Çiviler',
				'short' => 'Tel ya da bantla dizilmemiş, toplu hâlde satılan çiviler. Elle çakımda ve onarım işlerinde pratik.',
				'tool'  => 'Çekiçle ya da dökme çivi kullanan makinelerde',
			),
			array(
				'lead' => 'Dökme çivi, herhangi bir tel ya da bantla dizilmemiş, toplu hâlde satılan çividir. Çekiçle elle çakılır ya da dökme çivi besleyen makinelerde kullanılır.',
				'body' => "Dökme çiviler çelik telden, başı ve ucu şekillendirilerek üretilir; dizme işleminden geçmez. Bu yüzden kilo ya da koli ile satılır.\n\nTabanca kullanılmayan işlerde, onarımda, küçük adetli üretimde ve dökme çivi kullanan çakım makinelerinde tercih edilir. Palet onarımı, sandık kapağı ve saha işlerinde pratiktir.\n\nBaşı düz, gövdesi düz ya da halkalı olabilir; çıplak ve galvaniz seçenekleri vardır. Dayanıklılık gereken işlerde halkalı gövde ahşapta daha iyi tutunur.\n\nİhtiyacınız olan boyu ve kalınlığı söyleyin, stok ve fiyat bilgisini iletelim.",
				'uses' => array( 'Palet onarımı', 'Sandık ve kafes kapağı', 'Elle çakım gereken işler', 'Küçük adetli üretim', 'Saha ve şantiye işleri' ),
			)
		),

		/* ---------------------------------------------------------- *
		 * Hakkimizda
		 * ---------------------------------------------------------- */
		'about' => array(
			'label'      => 'Hakkımızda',
			'path'       => '/hakkimizda/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Hakkımızda' ),
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'İstanbul Palet Çivi, Koçist Grup’un palet çivisi tedarik markasıdır. Palet ve ambalaj üreticilerine rulo, tele dizili ve dökme çivi sağlıyoruz.' ),
					),
				),
				'profile' => array(
					'label'  => 'Profilimiz',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Profilimiz' ),
						'text'  => array( 'label' => 'Metin (paragrafları boş satırla ayırın)', 'type' => 'textarea', 'default' => "Koçist Grup 50 yıldır orman ürünleri alanında çalışıyor; her ölçüde kereste, palet, sandık ve kafes üretiyor. İstanbul Palet Çivi bu deneyimle, palet ve ambalaj üreticilerinin çivi ihtiyacını karşılıyor.\n\nKaliteli, dürüst ve hızlı bir satış ilkesi benimsedik. Amacımız en kaliteli malzemeyi en kısa zamanda müşterimizin hizmetine sunmak; bu doğrultuda daha kaliteli ve daha hesaplı malzemeyi sunma ilkesiyle çalışıyoruz." ),
					),
				),
				'quality' => array(
					'label'  => 'Kalite Politikamız',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Kalite politikamız' ),
						'items' => array(
							'label'   => 'Maddeler',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'text' => array( 'label' => 'Madde', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'text' => 'Ürünlerimizi zamanında teslim etmek, uygun fiyat avantajı sağlamak ve kaliteli ürün sunarak müşteri ihtiyaç ve beklentilerini karşılamak.' ),
								array( 'text' => 'Kalite bilincini yerleştirerek çalışanlarımızın sağlığı ve çevrenin korunması için maddi ve insan kaynaklarımızı seferber etmek, tüm çalışanlarımızın gelişmesini sağlamak.' ),
								array( 'text' => 'Kuruluş olarak her alanda sürekli iyileşme ve gelişme sağlamak.' ),
								array( 'text' => 'Yaptığımız işi ilk seferinde ve her seferinde doğru yapmak; müşteri istek ve görüşlerini esas almak, standartlara ve kalite yönetim sistemimize uygun çalışmak.' ),
								array( 'text' => 'Kalite bilincinin artması için tedarikçilerimizle koordineli çalışmak.' ),
							),
						),
					),
				),
				'group' => array(
					'label'  => 'Koçist Grup',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Koçist Grup' ),
						'text'  => array( 'label' => 'Metin (paragrafları boş satırla ayırın)', 'type' => 'textarea', 'default' => "Koçist Grup, orman ürünleri alanındaki yatırımıyla 50 yıldır hizmet veriyor. Grup, bu tecrübeyle yapı ve inşaat sektöründe de malzeme tedarikçisi olarak çalışıyor.\n\nKoçist Yapı İnşaat, yaşam ve çalışma alanları inşa etmeyi hedefliyor. Koçist Grup 2016 yılından itibaren Koçist Teknoloji markasıyla bilişim alanında da yatırım yapıyor." ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Blog
		 * ---------------------------------------------------------- */
		'blog' => array(
			'label'      => 'Blog ve Haberler',
			'path'       => '/haberler-blog/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Blog ve haberler' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Palet çivisi, rulo çivi, tele dizili çivi ve dökme çivi hakkında bilmeniz gerekenler.' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Iletisim
		 * ---------------------------------------------------------- */
		'contact' => array(
			'label'      => 'İletişim',
			'path'       => '/iletisim/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Bize ulaşın' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Aşağıdaki iletişim bilgilerinden bize ulaşabilirsiniz; size en kısa sürede dönüş yaparız.' ),
					),
				),
				'details' => array(
					'label'  => 'İletişim Bilgileri',
					'fields' => array(
						'address'   => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => $address ),
						'map_query' => array( 'label' => 'Haritada Aranacak Adres', 'type' => 'text', 'default' => $address_line ),
						'hours'     => array( 'label' => 'Çalışma Saatleri (boşsa gizlenir)', 'type' => 'text', 'default' => '' ),
					),
				),
				'form' => array(
					'label'  => 'Form',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İletişim formu' ),
					),
				),
			),
		),
	),
);
