<?php
/**
 * ahsapkasa temasi alan manifesti.
 *
 * Bu dosya saf bir dizi dondurur; hicbir WordPress fonksiyonuna bagimli degildir.
 * Boylece ag paneli, siteye gecmeden dosya sisteminden okuyabilir.
 *
 * Alan turleri: text, textarea, url, image, icon, repeater
 */

return array(
	'site_key'          => 'ahsapkasa',
	'site_label'        => 'Koçist · ahsapkasa.com',
	// Panelde gorunen kisa ad (alan adi olmadan).
	'panel_label'       => 'Ahşap Kasa',

	// SEO ve GEO sekmesindeki firma bilgisinin ilk degerleri: sitenin kendi
	// iletisim ve alt bilgi alanlarindaki bilgiler. Konum bilinmedigi icin bos.
	'seo_site_defaults' => array(
		'name'        => 'Koçist Orman Ürünleri',
		'legal_name'  => 'Koçist Orman Ürünleri İnş. ve İnş. Yap. Malz. San. Tic. Ltd. Şti.',
		'description' => 'Koçist Orman Ürünleri, İstanbul Çatalca’da 50 yıllık tecrübeyle istenen ölçü ve ebatlarda ahşap sandık, ahşap kafes, ahşap palet ve ahşap ambalaj üretir; demonte paketler hâlinde sevk eder.',
		'phone'       => '+90 212 648 10 90',
		'email'       => 'info@kocist.com.tr',
		'street'      => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1',
		'district'    => 'Çatalca',
		'city'        => 'İstanbul',
		'country'     => 'TR',
		// Bu site Koçist'in kendisi: kocist.com.tr ayni kurumun adresi. Kardes
		// siteler (istanbulpaletci vb.) ayni kurum degil; sameAs'e yazilmaz.
		'same_as'     => array(
			array( 'url' => 'https://www.kocist.com.tr' ),
		),
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
						'logo_image' => array( 'label' => 'Logo Görseli', 'type' => 'image', 'default' => 0 ),
						'logo_text'  => array( 'label' => 'Logo Yazısı', 'type' => 'text', 'default' => 'Koçist' ),
						'logo_sub'   => array( 'label' => 'Logo Alt Yazısı', 'type' => 'text', 'default' => 'Orman Ürünleri' ),
						'menu'       => array(
							'label'   => 'Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı Adresi', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ana sayfa', 'url' => '/' ),
								array( 'label' => 'Hakkımızda', 'url' => '/hakkimizda/' ),
								array( 'label' => 'Hizmetlerimiz', 'url' => '/hizmetlerimiz/' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),
						'whatsapp_label' => array( 'label' => 'WhatsApp Düğmesi Metni', 'type' => 'text', 'default' => 'WhatsApp' ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => '' ),
						'cta_label'  => array( 'label' => 'Menü Düğmesi Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'cta_url'    => array( 'label' => 'Menü Düğmesi Adresi', 'type' => 'url', 'default' => '/iletisim/' ),
					),
				),

				'footer' => array(
					'label'  => 'Alt Bilgi',
					'fields' => array(
						'tagline'        => array( 'label' => 'Alt Bilgi Metni', 'type' => 'textarea', 'default' => 'Elli yıldır Çatalca’da, ölçüye göre ahşap palet, sandık, kafes ve ambalaj üretiyoruz.' ),
						'phone_label'    => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0 212 648 10 90' ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'mobile_label'   => array( 'label' => 'Cep Telefonu Metni', 'type' => 'text', 'default' => '0532 374 98 32' ),
						'mobile_url'     => array( 'label' => 'Cep Telefonu Bağlantısı', 'type' => 'url', 'default' => 'tel:+905323749832' ),
						'email_label'    => array( 'label' => 'E-posta Metni', 'type' => 'text', 'default' => 'info@kocist.com.tr' ),
						'email_url'      => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@kocist.com.tr' ),
						'address'        => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => "Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1\nÇatalca, İstanbul" ),
						'nav_title'      => array( 'label' => 'Menü Başlığı', 'type' => 'text', 'default' => 'Sayfalar' ),
						'external_note'  => array( 'label' => 'Diğer Ürünler Metni', 'type' => 'text', 'default' => 'Diğer ürün gruplarımız için' ),
						'external_label' => array( 'label' => 'Diğer Ürünler Bağlantı Metni', 'type' => 'text', 'default' => 'www.kocist.com.tr' ),
						'external_url'   => array( 'label' => 'Diğer Ürünler Adresi', 'type' => 'url', 'default' => 'https://www.kocist.com.tr' ),
						'copyright'      => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Ana sayfa
		 * ---------------------------------------------------------- */
		'home' => array(
			'label'             => 'Ana Sayfa',
			'path'              => '/',
			'sortable_sections' => array( 'intro', 'family', 'ctaband' ),
			'components'        => array(

				'hero' => array(
					'label'  => 'Giriş (Hero)',
					'fields' => array(
						'title'           => array( 'label' => 'Başlık', 'type' => 'textarea', 'default' => "Ölçüyü siz verin,\nambalajı biz üretelim." ),
						'measure_label'   => array( 'label' => 'Ölçü Çizgisi Yazısı', 'type' => 'text', 'default' => 'istediğiniz her ölçü ve ebatta' ),
						'primary_label'   => array( 'label' => 'Birinci Düğme Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'primary_url'     => array( 'label' => 'Birinci Düğme Adresi', 'type' => 'url', 'default' => '/iletisim/' ),
						'secondary_label' => array( 'label' => 'İkinci Düğme Metni', 'type' => 'text', 'default' => 'Ürünlerimizi görün' ),
						'secondary_url'   => array( 'label' => 'İkinci Düğme Adresi', 'type' => 'url', 'default' => '/hizmetlerimiz/' ),
						'slides'          => array(
							'label'   => 'Arka Plan Slaytları',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'image' => array( 'label' => 'Fotoğraf', 'type' => 'image' ),
							),
							'default' => array(
								array( 'image' => 0 ),
								array( 'image' => 0 ),
								array( 'image' => 0 ),
							),
						),
					),
				),

				'intro' => array(
					'label'  => 'Giriş Paragrafı',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Elli yıldır aynı işi yapıyoruz' ),
						'lead' => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Koçist Orman Ürünleri; sektördeki 50 yıllık tecrübesi ile istediğiniz ölçü ve ebatlarda ahşap sandık, ahşap kafes, ahşap palet üretimi yapmaktadır.' ),
						'text' => array( 'label' => 'Paragraf', 'type' => 'textarea', 'default' => 'Elinizdeki ölçüleri, proje çizimini ya da hâlihazırda kullandığınız ambalajı iletin; ürününüze uygun paleti, sandığı veya kafesi tasarlayıp üretelim. Tüm parçaları içeren demonte paketler hâlinde sevk ediyoruz.' ),
					),
				),

				'family' => array(
					'label'  => 'Ürün Grubu Şeridi',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Ürün grubumuz' ),
						'note'  => array( 'label' => 'Bölüm Notu', 'type' => 'text', 'default' => 'Bunlarla sınırlı değil elbette.' ),
						'items' => array(
							'label'   => 'Ürünler',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'label' => array( 'label' => 'Ürün Adı', 'type' => 'text' ),
								'note'  => array( 'label' => 'Kısa Not', 'type' => 'text' ),
								'image' => array( 'label' => 'Görsel', 'type' => 'image' ),
								'url'   => array( 'label' => 'Bağlantı Adresi', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ahşap palet', 'note' => 'Standart dışı ölçülerde, kardonlu seçenekle', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
								array( 'label' => 'Ahşap sandık', 'note' => 'İhracata uygun, demonte sevk edilebilir', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
								array( 'label' => 'Ahşap kafes', 'note' => 'Ürününüzün ölçüsüne göre kafes iskeleti', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
								array( 'label' => 'Ahşap ambalaj', 'note' => 'Şartnamenize uygun paketleme çözümleri', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
							),
						),
					),
				),

				'ctaband' => array(
					'label'  => 'Teklif Şeridi',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ölçülerinizi gönderin, teklifinizi hazırlayalım.' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Ürünün eni, boyu, yüksekliği ve tahmini adedi yeterli. Çiziminiz varsa daha da hızlı ilerleriz.' ),
						'button_label' => array( 'label' => 'Düğme Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'button_url'   => array( 'label' => 'Düğme Adresi', 'type' => 'url', 'default' => '/iletisim/' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Hakkimizda
		 * ---------------------------------------------------------- */
		'about' => array(
			'label'      => 'Hakkımızda',
			'path'       => '/hakkimizda/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Hakkımızda' ),
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Elli yıllık tecrübeyle, istediğiniz ölçü ve ebatta ahşap sandık, kafes ve palet üretiyoruz. Kaliteli, dürüst ve hızlı.' ),
						'image' => array( 'label' => 'Arka Plan Fotoğrafı', 'type' => 'image', 'default' => 0 ),
					),
				),

				'story' => array(
					'label'  => 'Firma Metni',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Nasıl çalışıyoruz' ),
						'lead'  => array( 'label' => 'Bölüm Alt Metni', 'type' => 'text', 'default' => 'Ölçüden sevkiyata kadar izlenen yol.' ),
						'p1' => array( 'label' => 'Birinci Paragraf', 'type' => 'textarea', 'default' => 'Yeni bir ürün için ya da kullandığınız ambalajın yerine; her ölçüde kereste, palet, sandık ve kafesi ihtiyacınıza göre tasarlar, size sunarız.' ),
						'p2' => array( 'label' => 'İkinci Paragraf', 'type' => 'textarea', 'default' => 'Ölçülerinizi, spesifikasyonları veya proje çizimini alır, en hızlı şekilde üretime geçeriz. Tüm parçaları içeren demonte paketler hâlinde sevk ederiz.' ),
					),
				),

				'purpose' => array(
					'label'  => 'Amacımız',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Amacımız' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'En kaliteli malzemeyi en kısa zamanda müşterimizin hizmetine sunmaktır. Bu bağlamda firmamızın çalışmaları daha kaliteli ve daha hesaplı malzemeyi tüketicinin hizmetine sunma ilkesiyle devam etmektedir.' ),
					),
				),

				'quality' => array(
					'label'  => 'Kalite Politikamız',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Kalite Politikamız' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Ürünlerimizi zamanında ve uygun fiyatla teslim ederek müşteri ihtiyaç ve beklentilerini karşılamak; işi ilk seferinde ve her seferinde doğru yapmak; çalışanlarımızın sağlığını ve çevreyi gözetmek; tedarikçilerimizle birlikte sürekli gelişmek.' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Hizmetlerimiz - dortlu kare, ortasinda teklif dugmesi
		 * ---------------------------------------------------------- */
		'services' => array(
			'label'      => 'Hizmetlerimiz',
			'path'       => '/hizmetlerimiz/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Hizmetlerimiz' ),
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Dört ürün grubunun hiçbirinde standart ölçü yok. Her iş, sizin verdiğiniz ölçüye göre üretilir.' ),
					),
				),

				'grid' => array(
					'label'  => 'Dörtlü Ürün Karesi',
					'fields' => array(
						'cta_label'    => array( 'label' => 'Ürün Düğmesi Metni', 'type' => 'text', 'default' => 'Bu ürün için teklif al' ),
						'cta_url'      => array( 'label' => 'Ürün Düğmesi Adresi', 'type' => 'url', 'default' => '/iletisim/' ),
						'items'        => array(
							'label'   => 'Ürünler',
							'type'    => 'repeater',
							'max'     => 4,
							'fields'  => array(
								'title'   => array( 'label' => 'Ürün Adı', 'type' => 'text' ),
								'text'    => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
								'image'   => array( 'label' => 'Kapak Görseli', 'type' => 'image' ),
								'image_2' => array( 'label' => '2. Görsel', 'type' => 'image' ),
								'image_3' => array( 'label' => '3. Görsel', 'type' => 'image' ),
								'image_4' => array( 'label' => '4. Görsel', 'type' => 'image' ),
							),
							'default' => array(
								array( 'title' => 'Ahşap palet', 'text' => 'Standart dışı, istediğiniz ölçü ve ebatlarda ahşap palet üretimi yapmaktayız. Kardonlu palet ihtiyaçlarınızda da bizimle irtibat kurabilirsiniz.', 'image' => 0, 'image_2' => 0, 'image_3' => 0, 'image_4' => 0 ),
								array( 'title' => 'Ahşap sandık', 'text' => 'Standart dışı, istediğiniz ölçü ve ebatlarda ahşap sandık üretimi yapmaktayız. Fiyatlarımız ve cazip tekliflerimiz için lütfen bizimle irtibat kurunuz.', 'image' => 0 ),
								array( 'title' => 'Ahşap kafes', 'text' => 'Standart dışı, istediğiniz ölçü ve ebatlarda ahşap kafes üretimi yapmaktayız. Size özel teklif ve cazip fiyat seçeneklerimiz için lütfen bizimle irtibat kurunuz.', 'image' => 0 ),
								array( 'title' => 'Ahşap ambalaj', 'text' => 'İhracat şartnamenize uygun, ölçüye göre ahşap ambalaj çözümleri üretiyoruz. Parçaları içeren demonte paketler hâlinde sevk ediyoruz.', 'image' => 0 ),
							),
						),
					),
				),

				'note' => array(
					'label'  => 'Sayfa Sonu Notu',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ürün grubumuz bunlarla sınırlı değil.' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Özel boy ve ebatta kereste ile aradığınız başka bir ahşap ambalaj çözümü varsa yazın; üretilebilirliğini birlikte değerlendirelim.' ),
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
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İletişim' ),
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Ürünün ölçülerini ve tahmini adedi yazın, teklifinizi hazırlayalım.' ),
					),
				),

				'details' => array(
					'label'  => 'İletişim Bilgileri',
					'fields' => array(
						'phone_title'   => array( 'label' => 'Telefon Başlığı', 'type' => 'text', 'default' => 'Müşteri hizmetleri' ),
						'phone_icon'    => array( 'label' => 'Telefon İkonu', 'type' => 'icon', 'default' => 'phone' ),
						'phone_label'   => array( 'label' => 'Telefon', 'type' => 'text', 'default' => '0 212 648 10 90' ),
						'phone_url'     => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'mobile_title'  => array( 'label' => 'Cep Telefonu Başlığı', 'type' => 'text', 'default' => 'Cep ve WhatsApp' ),
						'mobile_icon'   => array( 'label' => 'Cep Telefonu İkonu', 'type' => 'icon', 'default' => 'whatsapp' ),
						'mobile_label'  => array( 'label' => 'Cep Telefonu', 'type' => 'text', 'default' => '0532 374 98 32' ),
						'mobile_url'    => array( 'label' => 'Cep Telefonu Bağlantısı', 'type' => 'url', 'default' => 'tel:+905323749832' ),
						'email_title'   => array( 'label' => 'E-posta Başlığı', 'type' => 'text', 'default' => 'E-posta' ),
						'email_icon'    => array( 'label' => 'E-posta İkonu', 'type' => 'icon', 'default' => 'mail' ),
						'email_label'   => array( 'label' => 'E-posta', 'type' => 'text', 'default' => 'info@kocist.com.tr' ),
						'email_url'     => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@kocist.com.tr' ),
						'address_title' => array( 'label' => 'Adres Başlığı', 'type' => 'text', 'default' => 'Merkez ofis ve atölye' ),
						'address_icon'  => array( 'label' => 'Adres İkonu', 'type' => 'icon', 'default' => 'pin' ),
						'address'       => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => "Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1\nÇatalca, İstanbul" ),
						'hours_title'   => array( 'label' => 'Çalışma Saatleri Başlığı', 'type' => 'text', 'default' => 'Çalışma saatleri' ),
						'hours_icon'    => array( 'label' => 'Çalışma Saatleri İkonu', 'type' => 'icon', 'default' => 'clock' ),
						'hours'         => array( 'label' => 'Çalışma Saatleri', 'type' => 'text', 'default' => 'Hafta içi 08:00 – 18:00' ),
					),
				),

				'form' => array(
					'label'  => 'Teklif Formu',
					'fields' => array(
						'title'         => array( 'label' => 'Form Başlığı', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'note'          => array( 'label' => 'Form Açıklaması', 'type' => 'textarea', 'default' => 'Ölçü alanına en, boy ve yükseklik yazmanız yeterli. Çiziminiz varsa e-posta ile de gönderebilirsiniz.' ),
						'name_label'    => array( 'label' => 'Ad Soyad Etiketi', 'type' => 'text', 'default' => 'Ad soyad' ),
						'company_label' => array( 'label' => 'Firma Etiketi', 'type' => 'text', 'default' => 'Firma' ),
						'email_label'   => array( 'label' => 'E-posta Etiketi', 'type' => 'text', 'default' => 'E-posta' ),
						'phone_label'   => array( 'label' => 'Telefon Etiketi', 'type' => 'text', 'default' => 'Telefon' ),
						'product_label' => array( 'label' => 'Ürün Etiketi', 'type' => 'text', 'default' => 'Hangi ürün' ),
						'size_label'    => array( 'label' => 'Ölçü Etiketi', 'type' => 'text', 'default' => 'Ölçüler ve adet' ),
						'size_hint'     => array( 'label' => 'Ölçü Alanı İpucu', 'type' => 'text', 'default' => 'Örn. 120 × 80 × 100 cm, yaklaşık 50 adet' ),
						'message_label' => array( 'label' => 'Mesaj Etiketi', 'type' => 'text', 'default' => 'Eklemek istedikleriniz' ),
						'submit_label'  => array( 'label' => 'Gönder Düğmesi Metni', 'type' => 'text', 'default' => 'Teklif isteğini gönder' ),
						'privacy_note'  => array( 'label' => 'Gizlilik Notu', 'type' => 'text', 'default' => 'Bilgileriniz yalnızca teklifinizi hazırlamak için kullanılır.' ),
						'success_title' => array( 'label' => 'Başarı Başlığı', 'type' => 'text', 'default' => 'Teklif isteğiniz bize ulaştı.' ),
						'success_text'  => array( 'label' => 'Başarı Metni', 'type' => 'textarea', 'default' => 'En kısa sürede dönüş yapacağız. Acele bir işse 0 212 648 10 90 numaralı telefondan da ulaşabilirsiniz.' ),
					),
				),
			),
		),
	),
);
