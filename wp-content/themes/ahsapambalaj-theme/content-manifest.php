<?php
/**
 * ahsapambalaj temasi alan manifesti.
 *
 * Bu dosya saf bir dizi dondurur; hicbir WordPress fonksiyonuna bagimli degildir.
 * Boylece ag paneli, siteye gecmeden dosya sisteminden okuyabilir.
 *
 * Alan turleri: text, textarea, url, image, icon, repeater
 */

$manifest_data = array(
	'site_key'   => 'ahsapambalaj',
	'site_label' => 'Ahşap Ambalaj Sanayi · ahsapambalajsanayi.com',
	// SEO ve GEO firma bilgisi (Network Content Studio). Degerler bu sitenin
	// kendi sayfalarinda yazanlardir; panelin SEO ve GEO sekmesinden duzeltilir.
	'seo_site_defaults' => array(
		'name'        => 'Ahşap Ambalaj Sanayi',
		'description' => 'İstanbul Çatalca’da sanayi ve ihracat yükleri için ölçüye göre ahşap sandık, kafes ve palet üretimi. Makine, kalıp ve proje sevkiyatına uygun ahşap ambalaj.',
		'phone'       => '+90 212 648 10 90',
		'email'       => 'info@kocist.com.tr',
		'street'      => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1',
		'district'    => 'Çatalca',
		'city'        => 'İstanbul',
		'country'     => 'TR',
		'parent_name' => 'Koçist Orman Ürünleri',
		'parent_url'  => 'https://kocist.com.tr',
	),
	'pages'      => array(

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
						'logo_image' => array( 'label' => 'Logo Görseli', 'type' => 'image', 'default' => 0, 'hint' => 'Üst menüde görünen logo. Boş bırakılırsa temanın kendi logosu kullanılır.' ),
						'logo_text'  => array( 'label' => 'Firma Adı (logonun açıklaması)', 'type' => 'text', 'default' => 'Ahşap Ambalaj', 'hint' => 'Sayfada yazı olarak görünmez; logo görselinin açıklamasıdır (görme engelliler ve Google için).' ),
						'logo_sub'   => array( 'label' => 'Firma Adının Devamı (logonun açıklaması)', 'type' => 'text', 'default' => 'Sanayi', 'hint' => 'Sayfada görünmez; firma adının arkasına eklenir.' ),
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
						'whatsapp_label' => array( 'label' => 'WhatsApp Düğmesi Metni', 'type' => 'text', 'default' => 'WhatsApp', 'hint' => 'Düğme yalnızca aşağıdaki WhatsApp Bağlantısı doluyken görünür.' ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı (boşsa düğme gizlenir)', 'type' => 'url', 'default' => '' ),
						'cta_label'  => array( 'label' => 'Menü Düğmesi Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'cta_url'    => array( 'label' => 'Menü Düğmesi Adresi', 'type' => 'url', 'default' => '/iletisim/' ),
					),
				),

				'footer' => array(
					'label'  => 'Alt Bilgi',
					'fields' => array(
						'logo_image'     => array(
							'label'   => 'Alt bilgi logosu (koyu zemin için açık renk)',
							'type'    => 'image',
							'default' => 0,
							'hint'    => 'Alt bilgi koyu zeminlidir; açık renkli bir logo yükleyin. Boş bırakılırsa temanın varsayılan açık renkli logosu kullanılır.',
						),
						'tagline'        => array( 'label' => 'Alt Bilgi Metni', 'type' => 'textarea', 'default' => 'Sanayi ve ihracat yükleri için ölçüye göre ahşap sandık, kafes ve palet üretiyoruz.' ),
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
						'external_url'   => array( 'label' => 'Diğer Ürünler Adresi', 'type' => 'url', 'default' => 'https://kocist.com.tr' ),
						'copyright'      => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri' ),
						'contact_title'  => array( 'label' => 'İletişim Başlığı', 'type' => 'text', 'default' => 'İletişim' ),
						'rights_text'    => array( 'label' => 'Telif Satırı Sonu', 'type' => 'text', 'default' => 'Tüm hakları saklıdır.' ),
						'domain_label'   => array( 'label' => 'Alt Satırdaki Alan Adı', 'type' => 'text', 'default' => 'ahsapambalajsanayi.com' ),
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
			'sortable_sections' => array( 'intro', 'stats', 'family', 'ctaband' ),
			'components'        => array(

				'hero' => array(
					'label'  => 'Giriş (Hero)',
					'fields' => array(
						'title'           => array( 'label' => 'Başlık', 'type' => 'textarea', 'default' => "Ağır yük, uzun yol:\nsanayi için ahşap ambalaj." ),
						'measure_label'   => array( 'label' => 'Ölçü Çizgisi Yazısı', 'type' => 'text', 'default' => 'makine, kalıp ve proje sevkiyatı için' ),
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
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Yükü tanıyıp ambalajı ona göre kuruyoruz' ),
						'lead' => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Makine, kalıp ve yedek parça gibi ağır ve hassas yükler için taşıma koşuluna göre ahşap sandık, kafes ve palet üretiyoruz.' ),
						'text' => array( 'label' => 'Paragraf', 'type' => 'textarea', 'default' => 'Ürünün ölçüsünü, ağırlığını ve gideceği yeri iletin; taşıyıcı kızakları, iç sabitlemeyi ve kapak yapısını buna göre planlayalım. İhracat sevkiyatlarında ahşap ambalajın ISPM 15 kurallarına uygun olması gerekir; bunu işin başında hesaba katıyoruz.' ),
					),
				),

				'stats' => array(
					'label'  => 'Rakamlar Şeridi',
					'fields' => array(
						'items' => array(
							'label'   => 'Rakamlar',
							'type'    => 'repeater',
							'max'     => 3,
							'fields'  => array(
								'prefix' => array( 'label' => 'Ön Ek (ör. %)', 'type' => 'text' ),
								'value'  => array( 'label' => 'Değer (yalnızca rakamsa sayarak gelir)', 'type' => 'text' ),
								'suffix' => array( 'label' => 'Birim (ör. yıl)', 'type' => 'text' ),
								'label'  => array( 'label' => 'Açıklama', 'type' => 'text' ),
							),
							// Gercek rakamlar firmadan gelene kadar bos; bos satirlar sitede gorunmez.
							'default' => array(),
						),
					),
				),

				'family' => array(
					'label'  => 'Ürün Grubu Şeridi',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Ne üretiyoruz' ),
						'note'  => array( 'label' => 'Bölüm Notu', 'type' => 'text', 'default' => 'Her biri yüke göre ölçülendirilir.' ),
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
								array( 'label' => 'Ahşap palet', 'note' => 'Ağır ve standart dışı yükler için', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
								array( 'label' => 'Ahşap sandık', 'note' => 'Makine ve kalıp için kapalı koruma', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
								array( 'label' => 'Ahşap kafes', 'note' => 'Açık iskelet, yükün ölçüsünde', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
								array( 'label' => 'Ahşap ambalaj', 'note' => 'İhracat şartnamesine göre paketleme', 'image' => 0, 'url' => '/hizmetlerimiz/' ),
							),
						),
					),
				),

				'ctaband' => array(
					'label'  => 'Teklif Şeridi',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Yükün ölçüsünü ve ağırlığını gönderin.' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Eni, boyu, yüksekliği, ağırlığı ve gideceği yer yeterli; ambalaj önerimizle birlikte teklifimizi hazırlayalım.' ),
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
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Sanayi ve ihracat yükleri için ölçüye göre ahşap sandık, kafes ve palet. Arkasında Koçist Orman Ürünleri’nin 50 yıllık tecrübesi var.' ),
						'image' => array( 'label' => 'Arka Plan Fotoğrafı', 'type' => 'image', 'default' => 0 ),
					),
				),

				'story' => array(
					'label'  => 'Firma Metni',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Bir ambalaj işi nasıl ilerler' ),
						'lead'  => array( 'label' => 'Bölüm Alt Metni', 'type' => 'text', 'default' => 'Yükün bilgilerinden sevkiyata.' ),
						'p1' => array( 'label' => 'Birinci Paragraf', 'type' => 'textarea', 'default' => 'Önce yükü tanırız: ürünün ölçüsü, ağırlığı ve gideceği yer. Makine, kalıp ya da yedek parça için sandığı, kafesi veya paleti bu bilgilere göre tasarlarız.' ),
						'p2' => array( 'label' => 'İkinci Paragraf', 'type' => 'textarea', 'default' => 'Taşıyıcı kızakları, iç sabitlemeyi ve kapak yapısını planladıktan sonra üretime geçeriz. Büyük ambalajları bütün parçalarıyla demonte paketler hâlinde gönderebiliriz.' ),
					),
				),

				'purpose' => array(
					'label'  => 'Neyi önemsiyoruz',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Neyi önemsiyoruz' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Yükün yolda zarar görmeden yerine ulaşması. Bunun için doğru malzemeyi zamanında ve makul fiyatla sunmayı esas alıyoruz.' ),
					),
				),

				'quality' => array(
					'label'  => 'Kalite politikamız',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Kalite politikamız' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Teslim tarihine uymak, işi ilk seferinde doğru yapmak ve müşterinin isteğini esas almak. Bunu yaparken çalışanlarımızın sağlığını ve çevreyi gözetiyor, tedarikçilerimizle birlikte gelişmeye çalışıyoruz.' ),
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
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Her ambalaj taşıyacağı yüke göre tasarlanır. Ürünün ölçüsünü ve ağırlığını verin, gerisini birlikte planlayalım.' ),
					),
				),

				'grid' => array(
					'label'  => 'Dörtlü Ürün Karesi',
					'fields' => array(
						'cta_label'    => array( 'label' => 'Ürün Düğmesi Metni', 'type' => 'text', 'default' => 'Bu ürün için teklif al' ),
						'cta_url'      => array( 'label' => 'Ürün Düğmesi Adresi', 'type' => 'url', 'default' => '/iletisim/' ),
						'gallery_count' => array( 'label' => 'Görsel Sayısı Yazısı ({sayi} = görsel sayısı)', 'type' => 'text', 'default' => '{sayi} görsel' ),
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
								array( 'title' => 'Ahşap palet', 'text' => 'Ağır ve standart dışı yükler için ölçüye göre palet. Kardonlu palet seçeneği de var.', 'image' => 0, 'image_2' => 0, 'image_3' => 0, 'image_4' => 0 ),
								array( 'title' => 'Ahşap sandık', 'text' => 'Makineyi, kalıbı ya da yedek parçayı dört yandan koruyan kapalı sandık. İhracat sevkiyatına göre hazırlanır, demonte gönderilebilir.', 'image' => 0 ),
								array( 'title' => 'Ahşap kafes', 'text' => 'Açık iskeletli kafes, yükün ölçüsüne göre kurulur.', 'image' => 0 ),
								array( 'title' => 'Ahşap ambalaj', 'text' => 'İhracat şartnamenize göre, yükün ölçüsüne ve ağırlığına uygun paketleme.', 'image' => 0 ),
							),
						),
					),
				),

				'note' => array(
					'label'  => 'Sayfa Sonu Notu',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Yükünüz bu dört gruba uymuyor mu?' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Proje sevkiyatı ya da alışılmadık bir yük için ölçüleri ve bir fotoğraf gönderin; nasıl ambalajlanacağını birlikte değerlendirelim.' ),
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
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Yükün ölçüsünü, ağırlığını ve gideceği yeri yazın; ambalaj önerimizi ve teklifimizi hazırlayalım.' ),
					),
				),

				'details' => array(
					'label'  => 'İletişim Bilgileri',
					'fields' => array(
						'phone_title'   => array( 'label' => 'Telefon Başlığı', 'type' => 'text', 'default' => 'Telefon' ),
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
						'address_title' => array( 'label' => 'Adres Başlığı', 'type' => 'text', 'default' => 'Adres' ),
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
						'title'         => array( 'label' => 'Form Başlığı', 'type' => 'text', 'default' => 'Ambalaj teklifi isteyin' ),
						'note'          => array( 'label' => 'Form Açıklaması', 'type' => 'textarea', 'default' => 'Yükün ölçüsü (en × boy × yükseklik), ağırlığı ve gideceği yer yeterli. Çiziminiz ya da fotoğrafınız varsa e-postayla gönderin.' ),
						'name_label'    => array( 'label' => 'Ad Soyad Etiketi', 'type' => 'text', 'default' => 'Ad soyad' ),
						'company_label' => array( 'label' => 'Firma Etiketi', 'type' => 'text', 'default' => 'Firma' ),
						'email_label'   => array( 'label' => 'E-posta Etiketi', 'type' => 'text', 'default' => 'E-posta' ),
						'phone_label'   => array( 'label' => 'Telefon Etiketi', 'type' => 'text', 'default' => 'Telefon' ),
						'product_label' => array( 'label' => 'Ürün Etiketi', 'type' => 'text', 'default' => 'Hangi ürün' ),
						'size_label'    => array( 'label' => 'Ölçü Etiketi', 'type' => 'text', 'default' => 'Yükün ölçüsü, ağırlığı ve adedi' ),
						'size_hint'     => array( 'label' => 'Ölçü Alanı İpucu', 'type' => 'text', 'default' => 'Örn. 220 × 140 × 160 cm, 1.800 kg, 3 sandık' ),
						'message_label' => array( 'label' => 'Mesaj Etiketi', 'type' => 'text', 'default' => 'Varış yeri ve eklemek istedikleriniz' ),
						'submit_label'  => array( 'label' => 'Gönder Düğmesi Metni', 'type' => 'text', 'default' => 'Teklif isteğini gönder' ),
						'privacy_note'  => array( 'label' => 'Gizlilik Notu', 'type' => 'text', 'default' => 'Bilgileriniz yalnızca teklifinizi hazırlamak için kullanılır.' ),
						'success_title' => array( 'label' => 'Başarı Başlığı', 'type' => 'text', 'default' => 'Teklif isteğiniz bize ulaştı.' ),
						'success_text'  => array( 'label' => 'Başarı Metni', 'type' => 'textarea', 'default' => 'Yükün bilgilerini aldık; ambalaj önerimizle size döneceğiz. Acele bir iş için 0 212 648 10 90’ı arayabilirsiniz.' ),
						'product_placeholder' => array( 'label' => 'Ürün Seçimi Boş Seçenek', 'type' => 'text', 'default' => 'Seçin' ),
						'new_request_label'   => array( 'label' => 'Yeni İstek Düğmesi Metni', 'type' => 'text', 'default' => 'Yeni bir istek gönderin' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Bulunamayan sayfa (404). Onizleme adresi bilerek olmayan bir sayfa.
		 * ---------------------------------------------------------- */
		'notfound' => array(
			'label'      => '404 Sayfası',
			'path'       => '/bulunamadi-404/',
			// Gizli: sekme listesinde yok, "Sayfa bul" ile acilir; SEO listelerine (llms.txt) girmez.
			'hidden'     => true,
			'components' => array(
				'head' => array(
					'label'  => 'Sayfa Metinleri',
					'fields' => array(
						'code'       => array( 'label' => 'Üst Etiket', 'type' => 'text', 'default' => '404' ),
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Aradığınız sayfa burada değil.' ),
						'text'       => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Bağlantı eski olabilir. Ürünlerimize hizmetlerimiz sayfasından, teklif için iletişim sayfasından ulaşabilirsiniz.' ),
						'home_label' => array( 'label' => 'Ana Sayfa Düğmesi Metni', 'type' => 'text', 'default' => 'Ana sayfaya dön' ),
						'cta_label'  => array( 'label' => 'Teklif Düğmesi Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
					),
				),
			),
		),
	),
);

// Panel ipuclari: ayni bilginin sitede yazili oldugu diger yerler, baglanti yazimi.
$manifest_hints = require __DIR__ . '/content-manifest-hints.php';

return $manifest_hints(
	$manifest_data,
	array(
		'Bu telefon numarası' => array( 'global.footer.phone_label', 'contact.details.phone_label' ),
		'Bu cep numarası' => array( 'global.footer.mobile_label', 'contact.details.mobile_label' ),
		'Bu e-posta adresi' => array( 'global.footer.email_label', 'contact.details.email_label' ),
		'Bu adres' => array( 'global.footer.address', 'contact.details.address' ),
	)
);
