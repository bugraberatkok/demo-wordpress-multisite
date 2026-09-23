<?php
/**
 * istanbulpaletci temasi alan manifesti.
 *
 * Saf bir dizi dondurur; hicbir WordPress fonksiyonuna bagimli degildir.
 * Ag paneli bu dosyayi siteye gecmeden dosya sisteminden okur.
 *
 * Varsayilan metinler istanbulpaletci.com'un kendi sayfalarindan alinmistir.
 * Firmaya ozel yeni bir iddia (tarih, sertifika, kapasite) uydurulmamistir.
 *
 * Alan turleri: text, textarea, url, image, icon, repeater
 */

/*
 * Bes urun sayfasi ayni alan yapisini paylasir. Bu kurucu yalnizca dizi
 * uretir; dosya panel tarafinda birden cok kez okunsa da sorun cikarmaz.
 */
$product_page = static function ( string $label, string $path, array $card, array $detail, array $specs ): array {
	return array(
		'label'      => $label,
		'path'       => $path,
		// Tema bu isaretle urun sablonunu secer.
		'template'   => 'product',
		// SEO: arama sonucu ve urun verisi hangi alanlardan uretilsin.
		'seo_source' => array(
			'title'       => 'card.name',
			'description' => 'detail.lead',
			'image'       => 'card.image',
			'type'        => 'Product',
			'properties'  => 'specs.rows',
		),
		'components' => array(

			'card' => array(
				'label'  => 'Ürün Kartı (listelerde görünür)',
				'fields' => array(
					'name'  => array( 'label' => 'Ürün Adı', 'type' => 'text', 'default' => $card['name'] ),
					'short' => array( 'label' => 'Kısa Açıklama', 'type' => 'textarea', 'default' => $card['short'] ),
					'size'  => array( 'label' => 'Ölçü Satırı', 'type' => 'text', 'default' => $card['size'] ),
					'image' => array( 'label' => 'Kart Görseli', 'type' => 'image', 'default' => 0 ),
				),
			),

			'detail' => array(
				'label'  => 'Ürün Sayfası',
				'fields' => array(
					'lead'    => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => $detail['lead'] ),
					'body'    => array( 'label' => 'Ürün Açıklaması', 'type' => 'textarea', 'default' => $detail['body'] ),
					'gallery' => array(
						'label'   => 'Galeri',
						'type'    => 'repeater',
						'max'     => 6,
						'fields'  => array(
							'image' => array( 'label' => 'Görsel', 'type' => 'image' ),
						),
						'default' => array(),
					),
				),
			),

			'specs' => array(
				'label'  => 'Şartname Tablosu',
				'fields' => array(
					'rows' => array(
						'label'   => 'Satırlar',
						'type'    => 'repeater',
						'max'     => 8,
						'fields'  => array(
							'label' => array( 'label' => 'Özellik', 'type' => 'text' ),
							'value' => array( 'label' => 'Değer', 'type' => 'text' ),
						),
						'default' => $specs,
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
// Canli sitedeki WhatsApp baglantisinda numara bosluklu yazildigi icin
// calismiyor; burada dogru bicim kullaniliyor.
$whatsapp_url = 'https://wa.me/905323749832';
$email        = 'info@istanbulpaletci.com';
$address      = "Şahintepe, Eski İstanbul Cd. No: 176\n34494 Başakşehir / İstanbul";

return array(
	'site_key'          => 'istanbulpaletci',
	'site_label'        => 'Koçist · istanbulpaletci.com',

	// SEO ve GEO sekmesindeki firma bilgisinin ilk degerleri: sitenin kendi
	// iletisim sayfasindaki bilgiler. Konum (enlem/boylam) bilinmedigi icin bos.
	'seo_site_defaults' => array(
		'name'        => 'İstanbul Paletçi',
		'legal_name'  => 'Koçist Orman Ürünleri İnş. ve İnş. Yap. Malz. San. Tic. Ltd. Şti.',
		'description' => 'İstanbul Başakşehir’de isteğe özel ve standart ölçülerde ahşap palet, Euro palet, ahşap kafes ve ahşap sandık üretimi. Orman ürünleri sektöründe 40 yıllık tecrübe, ihracata uygun ahşap ambalaj.',
		'phone'       => $phone_label,
		'email'       => $email,
		'street'      => 'Şahintepe, Eski İstanbul Cd. No: 176',
		'district'    => 'Başakşehir',
		'city'        => 'İstanbul',
		'postal_code' => '34494',
		'country'     => 'TR',
		'same_as'     => array(
			array( 'url' => 'https://ahsapkasa.com' ),
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
						'logo_image'     => array( 'label' => 'Logo', 'type' => 'image', 'default' => 0 ),
						'logo_text'      => array( 'label' => 'Firma Adı (logo yoksa görünür)', 'type' => 'text', 'default' => 'İstanbul Paletçi' ),
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
								array( 'label' => 'Ürünlerimiz', 'url' => '/urunlerimiz/' ),
								array( 'label' => 'Blog', 'url' => '/blog/' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),
						'all_products'   => array( 'label' => 'Açılır Menüde "Tüm Ürünler" Metni', 'type' => 'text', 'default' => 'Tüm ürünleri karşılaştırın' ),
						'phone_label'    => array( 'label' => 'Telefon Düğmesi Metni', 'type' => 'text', 'default' => $phone_label ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => $phone_url ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Düğmesi Metni', 'type' => 'text', 'default' => 'WhatsApp' ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => $whatsapp_url ),
					),
				),

				'footer' => array(
					'label'  => 'Alt Bilgi',
					'fields' => array(
						'logo_image'     => array( 'label' => 'Koyu Zemin Logosu', 'type' => 'image', 'default' => 0 ),
						'tagline'        => array( 'label' => 'Alt Bilgi Metni', 'type' => 'textarea', 'default' => 'İsteğe özel ve standart ölçülerde ahşap palet, kafes ve sandık. 40 yıldır orman ürünleri sektöründeyiz.' ),
						'pages_title'    => array( 'label' => 'Sayfalar Başlığı', 'type' => 'text', 'default' => 'Sayfalar' ),
						'products_title' => array( 'label' => 'Ürünler Başlığı', 'type' => 'text', 'default' => 'Ürünlerimiz' ),
						'contact_title'  => array( 'label' => 'İletişim Başlığı', 'type' => 'text', 'default' => 'İletişim' ),
						'phone_label'    => array( 'label' => 'Sabit Telefon', 'type' => 'text', 'default' => $phone_label ),
						'phone_url'      => array( 'label' => 'Sabit Telefon Bağlantısı', 'type' => 'url', 'default' => $phone_url ),
						'mobile_label'   => array( 'label' => 'Cep Telefonu', 'type' => 'text', 'default' => $mobile_label ),
						'mobile_url'     => array( 'label' => 'Cep Telefonu Bağlantısı', 'type' => 'url', 'default' => $mobile_url ),
						'email_label'    => array( 'label' => 'E-posta', 'type' => 'text', 'default' => $email ),
						'email_url'      => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:' . $email ),
						'address'        => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => $address ),
						'external_label' => array( 'label' => 'Kardeş Site Bağlantı Metni (boşsa gizlenir)', 'type' => 'text', 'default' => '' ),
						'external_url'   => array( 'label' => 'Kardeş Site Adresi', 'type' => 'url', 'default' => '' ),
						'copyright'      => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri İnş. ve İnş. Yap. Malz. San. Tic. Ltd. Şti.' ),
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
			'sortable_sections' => array( 'products', 'process', 'export', 'blog', 'quote' ),
			'components'        => array(

				'hero' => array(
					'label'  => 'Giriş (Hero)',
					'fields' => array(
						'title'           => array( 'label' => 'Başlık', 'type' => 'textarea', 'default' => "İsteğe özel ölçülerde\npalet üretimi" ),
						'lead'            => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Ahşap palet, Euro palet, ahşap kafes ve sandık. 40 yıldır orman ürünleri sektöründe, zamanında teslimat ve müşteri memnuniyetiyle çalışıyoruz.' ),
						'primary_label'   => array( 'label' => 'Birinci Düğme Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'primary_url'     => array( 'label' => 'Birinci Düğme Adresi', 'type' => 'url', 'default' => '/iletisim/#teklif' ),
						'secondary_label' => array( 'label' => 'İkinci Düğme Metni', 'type' => 'text', 'default' => 'Ürünleri inceleyin' ),
						'secondary_url'   => array( 'label' => 'İkinci Düğme Adresi', 'type' => 'url', 'default' => '/urunlerimiz/' ),
						'bg_image'        => array( 'label' => 'Arka Plan Fotoğrafı (boşsa düz indigo)', 'type' => 'image', 'default' => 0 ),
						'bg_note'         => array( 'label' => 'Arka Plan Fotoğrafı Notu (örnek görselse "Örnek görsel" yazın)', 'type' => 'text', 'default' => '' ),
						'sheet_image'     => array( 'label' => 'Föy Görseli', 'type' => 'image', 'default' => 0 ),
						'dim_x'           => array( 'label' => 'Yatay Ölçü Yazısı', 'type' => 'text', 'default' => '120 cm' ),
						'dim_y'           => array( 'label' => 'Dikey Ölçü Yazısı', 'type' => 'text', 'default' => '80 cm' ),
						'sheet_rows'      => array(
							'label'   => 'Föy Bilgi Satırları',
							'type'    => 'repeater',
							'max'     => 3,
							'fields'  => array(
								'label' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'value' => array( 'label' => 'Değer', 'type' => 'text' ),
							),
							'default' => array(
								array( 'label' => 'Ürün', 'value' => 'Euro palet' ),
								array( 'label' => 'Ölçü', 'value' => '80 × 120 cm' ),
								array( 'label' => 'Kapasite', 'value' => '1500 kg' ),
							),
						),
					),
				),

				'facts' => array(
					'label'  => 'Bilgi Şeridi',
					'fields' => array(
						'items' => array(
							'label'   => 'Bilgiler',
							'type'    => 'repeater',
							'max'     => 4,
							'fields'  => array(
								'value' => array( 'label' => 'Büyük Yazı', 'type' => 'text' ),
								'label' => array( 'label' => 'Açıklama', 'type' => 'text' ),
							),
							'default' => array(
								array( 'value' => '40 yıl', 'label' => 'orman ürünleri sektöründe' ),
								array( 'value' => '5', 'label' => 'ürün ailesi' ),
								array( 'value' => 'İsteğe özel', 'label' => 'ölçüde üretim' ),
								array( 'value' => 'İhracat', 'label' => 'tüm dünyaya ahşap ambalaj' ),
							),
						),
					),
				),

				'products' => array(
					'label'  => 'Ürün Çeşitlerimiz',
					'fields' => array(
						'title'      => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Ürün çeşitlerimiz' ),
						'text'       => array( 'label' => 'Bölüm Metni', 'type' => 'textarea', 'default' => 'İsteğe özel ve standart ölçülerde ahşap palet, ahşap kafes ve ahşap sandık üretiyoruz. Zamanında teslimat ve müşteri memnuniyeti çalışma kültürümüzün temeli.' ),
						'link_label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text', 'default' => 'Tüm ürünler' ),
					),
				),

				'process' => array(
					'label'  => 'Üretim Süreci',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Tomruktan palete' ),
						'lead'  => array( 'label' => 'Bölüm Metni', 'type' => 'textarea', 'default' => 'Palet üretiminde her aşama belirli prosedürler ve uzman ekiplerin kontrolünde ilerler.' ),
						'steps' => array(
							'label'   => 'Adımlar',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'title' => array( 'label' => 'Adım Başlığı', 'type' => 'text' ),
								'text'  => array( 'label' => 'Adım Açıklaması', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'title' => 'Ham madde seçimi', 'text' => 'Çam, kayın, meşe ve kavak tomrukları arasından işe uygun ahşap seçilir.' ),
								array( 'title' => 'Biçme', 'text' => 'Tomruklar biçilerek palet kerestesine dönüştürülür.' ),
								array( 'title' => 'Ayıklama', 'text' => 'Budaklı keresteler ayrılır, sağlam olanlar üretime alınır.' ),
								array( 'title' => 'Ölçüye göre işleme', 'text' => 'Keresteler istenen ölçüye uygun en ve kalınlıkta işlenir.' ),
								array( 'title' => 'Montaj', 'text' => 'Parçalar standartlara uygun şekilde çivilenerek palete dönüşür.' ),
							),
						),
					),
				),

				'export' => array(
					'label'  => 'İhracat Bandı',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Tüm dünyaya ahşap ihracatı' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Dış ticarette ürünün böceklenmeden, mantar tutmadan ve zarar görmeden varması doğru ambalaja bağlı. Damgalı Euro paletleri ve ihracat sandıklarını taşınacak yükün ağırlığına göre üretiyoruz.' ),
						'image'        => array( 'label' => 'Fotoğraf', 'type' => 'image', 'default' => 0 ),
						'button_label' => array( 'label' => 'Düğme Metni', 'type' => 'text', 'default' => 'İhracat sandıklarını görün' ),
						'button_url'   => array( 'label' => 'Düğme Adresi', 'type' => 'url', 'default' => '/urunlerimiz/ahsap-sandik/' ),
					),
				),

				'blog' => array(
					'label'  => 'Blogdan Yazılar',
					'fields' => array(
						'title'      => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Blogdan en yeniler' ),
						'text'       => array( 'label' => 'Bölüm Metni', 'type' => 'textarea', 'default' => 'Palet çeşitleri, palet fiyatları ve kullanım alanları üzerine yazılar.' ),
						'link_label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text', 'default' => 'Tüm yazılar' ),
					),
				),

				'quote' => array(
					'label'  => 'Teklif Bölümü',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Bize her zaman yazabilirsiniz' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Almak istediğiniz ürünü ve ölçüleri iletin; en kısa sürede dönüş yapalım. Acele işler için telefon ve WhatsApp hattımız açık.' ),
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
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Ağaç ve orman endüstrisinde 40 yıllık tecrübeyle, ahşap palet alanında sektörün öncü firmalarından biriyiz.' ),
						'image' => array( 'label' => 'Fotoğraf', 'type' => 'image', 'default' => 0 ),
					),
				),

				'story' => array(
					'label'  => 'Firma Metni',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri' ),
						'p1'    => array( 'label' => 'Birinci Paragraf', 'type' => 'textarea', 'default' => 'Ağaç ve orman endüstrisi alanında 40 yıllık tecrübemizle, ahşap ve ahşap palete dair hizmetlerimizde sektörde öncü firma olarak sürecimizi devam ettiriyoruz.' ),
						'p2'    => array( 'label' => 'İkinci Paragraf', 'type' => 'textarea', 'default' => 'Müşteri talepleri doğrultusunda palet çeşitlerimizin üretim ve tedarik sürecini baştan sona takip ediyor, memnuniyeti en üst seviyede tutuyoruz. Ağaç ve orman işleri pazarındaki değişimi yakından izleyerek sürdürülebilirlik oluşturuyoruz.' ),
						'p3'    => array( 'label' => 'Üçüncü Paragraf', 'type' => 'textarea', 'default' => 'Gün geçtikçe gelişen müşteri portföyümüzle güvenilir ve kaliteli ürünleri gönül rahatlığıyla sunuyoruz.' ),
					),
				),

				'values' => array(
					'label'  => 'Çalışma İlkeleri',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Nasıl çalışıyoruz' ),
						'image' => array( 'label' => 'Fotoğraf', 'type' => 'image', 'default' => 0 ),
						'items' => array(
							'label'   => 'İlkeler',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'title' => 'Baştan sona takip', 'text' => 'Siparişinizin üretim ve tedarik sürecini ilk ölçüden teslimata kadar biz izliyoruz.' ),
								array( 'title' => 'Uygun maliyet', 'text' => 'Uygun maliyeti ve müşteri memnuniyetini esas alıyoruz.' ),
								array( 'title' => 'Hızlı ve verimli çözüm', 'text' => 'Sürecinizi hızlandırmak ve iş verimliliğinizi artırmak iş prensibimiz.' ),
								array( 'title' => 'Sürdürülebilirlik', 'text' => 'Pazardaki değişimi takip ederek büyüyor, işimizi sürdürülebilir kılıyoruz.' ),
							),
						),
					),
				),

				'cta' => array(
					'label'  => 'Teklif Şeridi',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ölçünüzü iletin, teklifinizi hazırlayalım.' ),
						'button_label' => array( 'label' => 'Düğme Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'button_url'   => array( 'label' => 'Düğme Adresi', 'type' => 'url', 'default' => '/iletisim/#teklif' ),
					),
				),
			),
		),

		/* ---------------------------------------------------------- *
		 * Urunlerimiz: bes urunun karsilastirmali listesi
		 * ---------------------------------------------------------- */
		'products' => array(
			'label'      => 'Ürünlerimiz',
			'path'       => '/urunlerimiz/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ürünlerimiz' ),
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'İsteğe özel ve standart ölçülerde ahşap palet, kafes ve sandık. Ölçülerini ve kullanım alanlarını yan yana görün.' ),
					),
				),

				'shared' => array(
					'label'  => 'Ürün Sayfalarının Ortak Metinleri',
					'fields' => array(
						'detail_label'  => array( 'label' => 'İncele Düğmesi', 'type' => 'text', 'default' => 'Ürünü inceleyin' ),
						'quote_label'   => array( 'label' => 'Teklif Düğmesi', 'type' => 'text', 'default' => 'Bu ürün için teklif alın' ),
						'quote_url'     => array( 'label' => 'Teklif Düğmesi Adresi', 'type' => 'url', 'default' => '/iletisim/' ),
						'specs_title'   => array( 'label' => 'Şartname Başlığı', 'type' => 'text', 'default' => 'Şartname' ),
						'body_title'    => array( 'label' => 'Açıklama Başlığı', 'type' => 'text', 'default' => 'Ürün hakkında' ),
						'related_title' => array( 'label' => 'Diğer Ürünler Başlığı', 'type' => 'text', 'default' => 'Diğer ürünlerimiz' ),
						'call_note'     => array( 'label' => 'Telefon Notu', 'type' => 'text', 'default' => 'Hemen konuşmak isterseniz' ),
					),
				),

				'custom' => array(
					'label'  => 'Özel Ölçü Şeridi',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Aradığınız ölçü listede yok mu?' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'İsteğe özel ölçülerde üretim yapıyoruz. En, boy, yükseklik ve tahmini adedi yazın; teklifinizi hazırlayalım.' ),
						'button_label' => array( 'label' => 'Düğme Metni', 'type' => 'text', 'default' => 'Özel ölçü için teklif alın' ),
						'button_url'   => array( 'label' => 'Düğme Adresi', 'type' => 'url', 'default' => '/iletisim/#teklif' ),
					),
				),
			),
		),

		'urun_ahsap_palet' => $product_page(
			'Ürün: Ahşap Palet',
			'/urunlerimiz/ahsap-palet/',
			array(
				'name'  => 'Ahşap Palet',
				'short' => 'Nakliyat, lojistik ve depolamada forklift ya da transpaletle taşınan paletler.',
				'size'  => 'İsteğe özel ölçü',
			),
			array(
				'lead' => 'Lojistiğin her aşamasında yükleme ve boşaltmayı kolaylaştıran, ürünü taşırken koruyan ahşap paletler.',
				'body' => "Nakliyat, lojistik ve depolama gibi alanlarda kullanım imkânı oldukça geniş olan paletler, ahşaptan üretilmiş ürünlerdir. Lojistiğin tüm aşamalarında önemli bir yeri vardır.\n\nAhşap paletler, bir ürünü bir yerden bir yere taşırken ürüne zarar verilmemesi için özenle oluşturulan malzemelerdir. Yükleme ve boşaltma işlemlerinin kolay bir şekilde yapılması ahşap paletlerle sağlanır.\n\nDorse içinde veya depoda bulunan ürünlerin hem muhafazasına hem de forklift ya da transpaletle taşınmasına yardımcı olurlar. Kullanım alanı ve kullanılan ebatlar çeşitlidir.",
			),
			array(
				array( 'label' => 'Ölçü', 'value' => 'İsteğe özel' ),
				array( 'label' => 'Kullanım alanı', 'value' => 'Nakliyat, lojistik, depolama' ),
				array( 'label' => 'Taşıma', 'value' => 'Forklift ve transpalet' ),
				array( 'label' => 'Ham madde', 'value' => 'Çam, kayın, meşe, kavak' ),
			)
		),

		'urun_euro_palet' => $product_page(
			'Ürün: Euro Palet',
			'/urunlerimiz/euro-palet/',
			array(
				'name'  => 'Euro Palet',
				'short' => 'Dış ticarette kullanılan, damgalı ve standart ölçülü paletler.',
				'size'  => '80 × 120 cm',
			),
			array(
				'lead' => 'Uluslararası Demiryolları Birliği onayıyla ülkeler arası taşımada kullanılan, damgalı ve standart ölçülü paletler.',
				'body' => "Dış ticaret yapan firma ve kurumlar, ürün veya malzemeleri götürmek için bazı prosedürleri uygulamak zorundadır. Böceklenme, mantar oluşumu veya mevcut pisliklerin taşınmasının önüne geçmek için doğru ambalajlama ve koruma büyük önem taşır.\n\nUluslararası Demiryolları Birliği’nin onayıyla ülkeler arası taşımada kullanılan Euro paletlerde damga bulunur. Bu damga, Euro paleti diğer paletlerden ayıran noktadır.\n\nBelli ölçü ve standartları olan bu paletlerin en sık tercih edileni 80 × 120 cm’lik modeldir.",
			),
			array(
				array( 'label' => 'Standart ölçüler', 'value' => '80 × 120, 100 × 120, 120 × 100, 60 × 80 cm' ),
				array( 'label' => 'En çok tercih edilen', 'value' => '80 × 120 cm' ),
				array( 'label' => 'Ağırlık', 'value' => 'Yaklaşık 35 kg' ),
				array( 'label' => 'Hacim', 'value' => '45,234 dm³' ),
				array( 'label' => 'Yayılı yük kapasitesi', 'value' => '1500 kg' ),
				array( 'label' => 'İşaret', 'value' => 'Damgalı' ),
			)
		),

		'urun_ahsap_kafes' => $product_page(
			'Ürün: Ahşap Kafes',
			'/urunlerimiz/ahsap-kafes/',
			array(
				'name'  => 'Ahşap Kafes',
				'short' => 'Birden fazla ürünün taşınması ve istiflenmesi için dayanıklı, uzun ömürlü kafesler.',
				'size'  => 'İsteğe özel ölçü',
			),
			array(
				'lead' => 'Pratik ve hızlı işlem sağladığı için firmaların tercih ettiği, taşımaya ve istiflemeye uygun ahşap kafesler.',
				'body' => "Birden fazla ürünün depolanması ve taşınmasına yardımcı olmak amacıyla kullanılan ahşap kafesler, bu alanlarda oldukça işe yarayan gereçlerdir. Pratik ve hızlı işlem yapılmasına yardımcı olduğu için firmaların tercih sebebi hâline gelmişlerdir.\n\nAhşap kafes sandıklar oldukça dayanıklı ve uzun ömürlüdür. Farklı ebatlarda üretilerek pek çok alanda kolayca kullanılabilirler.\n\nİster taşıma ister istifleme alanında kullanıma uygundur. Lojistik işletmeleri için olmazsa olmaz bir ürün olan ahşap kafesler, lojistik dışında da birçok alanda kullanılır.",
			),
			array(
				array( 'label' => 'Ölçü', 'value' => 'Farklı ebatlarda, isteğe özel' ),
				array( 'label' => 'Kullanım alanı', 'value' => 'Taşıma ve istifleme' ),
				array( 'label' => 'Özellik', 'value' => 'Dayanıklı, uzun ömürlü' ),
			)
		),

		'urun_ahsap_sandik' => $product_page(
			'Ürün: Ahşap Sandık',
			'/urunlerimiz/ahsap-sandik/',
			array(
				'name'  => 'Ahşap Sandık',
				'short' => 'Makine ve ihracat sevkiyatı için OSB, kontrplak ve katlanabilir seçenekli sandıklar.',
				'size'  => 'Standart ve özel ölçü',
			),
			array(
				'lead' => 'Taşınacak ürünün ağırlığına göre dayanıklılığı belirlenen, ihracatta tercih edilen ahşap sandıklar.',
				'body' => "Ahşap sandıklar müşteri talebine göre boyut ve şekil açısından farklılık gösterse de standart ölçülü ahşap sandıklar da bulunur.\n\nİhracatta özellikle tercih edilen ahşap ihracat sandıkları, makine üretimi yapan şirketlerin sıkça kullandığı sandık çeşitlerindendir. OSB sandık ve tahta kasa gibi seçeneklerin yanında kontrplak ve katlanabilir seçenekler de vardır.\n\nSandıklar, taşınacak ürünün kapasitesine uygun olarak tercih edilir ve ürün değerlendirilerek monte edilir. İstenen ölçüye göre üretilir; dayanıklılık, taşınacak malzemenin ağırlığı esas alınarak belirlenir.",
			),
			array(
				array( 'label' => 'Ölçü', 'value' => 'Standart ve isteğe özel' ),
				array( 'label' => 'Seçenekler', 'value' => 'OSB, tahta kasa, kontrplak, katlanabilir' ),
				array( 'label' => 'Dayanıklılık', 'value' => 'Taşınacak yükün ağırlığına göre' ),
				array( 'label' => 'Kullanım alanı', 'value' => 'İhracat, makine sevkiyatı' ),
			)
		),

		'urun_ikinci_el_palet' => $product_page(
			'Ürün: İkinci El Palet',
			'/urunlerimiz/ikinci-el-palet/',
			array(
				'name'  => 'İkinci El Palet',
				'short' => 'Sıfır palete göre daha uygun fiyatlı ikinci el palet seçenekleri.',
				'size'  => 'Stok durumuna göre',
			),
			array(
				'lead' => 'Palet ihtiyacını daha uygun maliyetle karşılamak isteyenler için ikinci el palet seçenekleri.',
				'body' => "Palet fiyatları ebat ve malzeme çeşidine göre farklılık gösterir; uygun fiyatlı ikinci el paletler de bu seçenekler arasındadır. Sıfır üretim paletler ikinci el paletlere göre biraz daha yüksek fiyatlı olabilir.\n\nDilediğiniz ölçülerdeki paletleri sıfır veya ikinci el olarak temin edebilirsiniz. Güncel stok ve fiyat için bizimle iletişime geçin.",
			),
			array(
				array( 'label' => 'Durum', 'value' => 'İkinci el' ),
				array( 'label' => 'Ölçü', 'value' => 'Stok durumuna göre' ),
				array( 'label' => 'Fiyat', 'value' => 'Sıfır palete göre daha uygun' ),
			)
		),

		/* ---------------------------------------------------------- *
		 * Blog: yazilarin kendisi WordPress yazilaridir; burada yalnizca
		 * liste sayfasinin basligi yonetilir.
		 * ---------------------------------------------------------- */
		'blog' => array(
			'label'      => 'Blog',
			'path'       => '/blog/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Blog' ),
						'lead'       => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Ahşap palet çeşitleri, palet fiyatları ve kullanım alanları üzerine yazılar.' ),
						'read_label' => array( 'label' => 'Devamını Oku Metni', 'type' => 'text', 'default' => 'Yazıyı okuyun' ),
						'back_label' => array( 'label' => 'Bloga Dön Metni', 'type' => 'text', 'default' => 'Tüm yazılar' ),
					),
				),

				'aside' => array(
					'label'  => 'Yazı Sayfası Yan Sütunu',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ürünlerimiz' ),
						'button_label' => array( 'label' => 'Düğme Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'button_url'   => array( 'label' => 'Düğme Adresi', 'type' => 'url', 'default' => '/iletisim/#teklif' ),
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
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'Telefon, WhatsApp ya da aşağıdaki formla ulaşın; size en kısa sürede dönüş sağlarız.' ),
					),
				),

				'details' => array(
					'label'  => 'İletişim Bilgileri',
					'fields' => array(
						'phone_title'    => array( 'label' => 'Sabit Hat Başlığı', 'type' => 'text', 'default' => 'Sabit hat' ),
						'phone_label'    => array( 'label' => 'Sabit Hat', 'type' => 'text', 'default' => $phone_label ),
						'phone_url'      => array( 'label' => 'Sabit Hat Bağlantısı', 'type' => 'url', 'default' => $phone_url ),
						'mobile_title'   => array( 'label' => 'GSM Başlığı', 'type' => 'text', 'default' => 'GSM' ),
						'mobile_label'   => array( 'label' => 'GSM', 'type' => 'text', 'default' => $mobile_label ),
						'mobile_url'     => array( 'label' => 'GSM Bağlantısı', 'type' => 'url', 'default' => $mobile_url ),
						'whatsapp_title' => array( 'label' => 'WhatsApp Başlığı', 'type' => 'text', 'default' => 'WhatsApp' ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Metni', 'type' => 'text', 'default' => $mobile_label ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => $whatsapp_url ),
						'email_title'    => array( 'label' => 'E-posta Başlığı', 'type' => 'text', 'default' => 'E-posta' ),
						'email_label'    => array( 'label' => 'E-posta', 'type' => 'text', 'default' => $email ),
						'email_url'      => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:' . $email ),
						'address_title'  => array( 'label' => 'Adres Başlığı', 'type' => 'text', 'default' => 'Adres' ),
						'address'        => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => $address ),
						'map_label'      => array( 'label' => 'Yol Tarifi Metni', 'type' => 'text', 'default' => 'Yol tarifi alın' ),
						'map_url'        => array( 'label' => 'Harita Bağlantısı', 'type' => 'url', 'default' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'Şahintepe, Eski İstanbul Cd. No:176, 34494 Başakşehir/İstanbul' ) ),
						'hours_title'    => array( 'label' => 'Çalışma Saatleri Başlığı', 'type' => 'text', 'default' => 'Çalışma saatleri' ),
						'hours'          => array( 'label' => 'Çalışma Saatleri (boşsa gizlenir)', 'type' => 'text', 'default' => '' ),
					),
				),

				'form' => array(
					'label'  => 'Teklif Formu',
					'fields' => array(
						'title'         => array( 'label' => 'Form Başlığı', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'note'          => array( 'label' => 'Form Açıklaması', 'type' => 'textarea', 'default' => 'Ölçü alanına en, boy, yükseklik ve adet yazmanız yeterli.' ),
						'name_label'    => array( 'label' => 'Ad Soyad Etiketi', 'type' => 'text', 'default' => 'Ad soyad' ),
						'company_label' => array( 'label' => 'Firma Etiketi', 'type' => 'text', 'default' => 'Firma' ),
						'email_label'   => array( 'label' => 'E-posta Etiketi', 'type' => 'text', 'default' => 'E-posta' ),
						'phone_label'   => array( 'label' => 'Telefon Etiketi', 'type' => 'text', 'default' => 'Telefon' ),
						'product_label' => array( 'label' => 'Ürün Etiketi', 'type' => 'text', 'default' => 'Hangi ürün' ),
						'size_label'    => array( 'label' => 'Ölçü Etiketi', 'type' => 'text', 'default' => 'Ölçüler ve adet' ),
						'size_hint'     => array( 'label' => 'Ölçü Alanı İpucu', 'type' => 'text', 'default' => 'Örn. 80 × 120 cm, 200 adet' ),
						'message_label' => array( 'label' => 'Mesaj Etiketi', 'type' => 'text', 'default' => 'Eklemek istedikleriniz' ),
						'submit_label'  => array( 'label' => 'Gönder Düğmesi Metni', 'type' => 'text', 'default' => 'Teklif isteğini gönder' ),
						'privacy_note'  => array( 'label' => 'Gizlilik Notu', 'type' => 'text', 'default' => 'Bilgileriniz yalnızca teklifinizi hazırlamak için kullanılır.' ),
						'success_title' => array( 'label' => 'Başarı Başlığı', 'type' => 'text', 'default' => 'Teklif isteğiniz bize ulaştı.' ),
						'success_text'  => array( 'label' => 'Başarı Metni', 'type' => 'textarea', 'default' => 'En kısa sürede dönüş yapacağız. Acele bir iş için +90 212 648 10 90 numarasından da ulaşabilirsiniz.' ),
					),
				),
			),
		),
	),
);
