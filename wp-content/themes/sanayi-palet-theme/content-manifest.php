<?php
/**
 * Sanayi Palet temasi alan manifesti.
 *
 * Saf dizi dondurur; WordPress fonksiyonlarina bagimli degildir. Ag paneli bu
 * dosyayi siteye gecmeden dosya sisteminden okur.
 *
 * Metinlerin kaynagi sanayipalet.com; hero, surec ve belge aciklamalari
 * bu tasarim icin yazildi (DECISIONS.md).
 *
 * Alan turleri: text, textarea, url, image, icon, repeater
 */

return array(
	'site_key'   => 'sanayi-palet',
	'site_label' => 'Sanayi Palet',
	// SEO ve GEO firma bilgisi (Network Content Studio). Degerler bu sitenin
	// kendi sayfalarinda yazanlardir; panelin SEO ve GEO sekmesinden duzeltilir.
	'seo_site_defaults' => array(
		'name'        => 'Sanayi Palet',
		'legal_name'  => 'Koçist Grup Dış Ticaret Sanayi Ltd. Şti.',
		'description' => 'Sanayi Palet, Koçist Grup’un ahşap palet ve sandık markası. Başakşehir’deki tesiste palet, sandık ve kafes istenen ölçüde üretilir; ihracat için ISPM 15 ısıl işlem ve damga.',
		'phone'       => '+90 212 648 10 90',
		'email'       => 'info@sanayipalet.com',
		'street'      => 'Şahintepe, Eski İstanbul Cd. No:176',
		'district'    => 'Başakşehir',
		'city'        => 'İstanbul',
		'postal_code' => '34494',
		'country'     => 'TR',
		'parent_name' => 'Koçist Orman Ürünleri',
		'parent_url'  => 'https://www.kocist.com.tr',
	),
	'pages'      => array(

		'global' => array(
			'label'      => 'Tüm Sayfalar (Menü ve Footer)',
			'path'       => '/',
			'components' => array(

				'header' => array(
					'label'  => 'Üst Menü',
					'fields' => array(
						'logo_image'   => array( 'label' => 'Logo Görseli', 'type' => 'image', 'default' => 0 ),
						'logo_text'    => array( 'label' => 'Logo Yazısı', 'type' => 'text', 'default' => 'sanayipalet' ),
						'logo_sub'     => array( 'label' => 'Logo Alt Yazısı', 'type' => 'text', 'default' => 'ahşap palet ve sandıklar' ),
						'menu'         => array(
							'label'   => 'Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 7,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ürünler', 'url' => '/urunler/' ),
								array( 'label' => 'Hakkımızda', 'url' => '/hakkimizda/' ),
								array( 'label' => 'Blog', 'url' => '/blog/' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),
						'phone_label'  => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0212 648 10 90' ),
						'phone_url'    => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'cta_label'    => array( 'label' => 'Teklif Düğmesi Metni', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'cta_url'      => array( 'label' => 'Teklif Düğmesi Bağlantısı', 'type' => 'url', 'default' => '/iletisim/' ),
						'whatsapp_url' => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905323749832' ),
					),
				),

				'footer' => array(
					'label'  => 'Footer',
					'fields' => array(
						'about_text'    => array( 'label' => 'Tanıtım Metni', 'type' => 'textarea', 'default' => 'Standart ve özel ölçüde ahşap palet, sandık ve kafes. Koçist Grup kuruluşudur.' ),
						'links'         => array(
							'label'   => 'Footer Bağlantıları',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ürünler', 'url' => '/urunler/' ),
								array( 'label' => 'Hakkımızda', 'url' => '/hakkimizda/' ),
								array( 'label' => 'Blog', 'url' => '/blog/' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),
						'address'       => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => "Şahintepe, Eski İstanbul Cd. No:176\n34494 Başakşehir / İstanbul" ),
						'phone_label'   => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0212 648 10 90' ),
						'phone_url'     => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Metni', 'type' => 'text', 'default' => '0532 374 98 32' ),
						'whatsapp_url'  => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905323749832' ),
						'email_label'   => array( 'label' => 'E-posta Metni', 'type' => 'text', 'default' => 'info@sanayipalet.com' ),
						'email_url'     => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@sanayipalet.com' ),
						'copyright'     => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => '© Koçist Grup Dış Ticaret Sanayi Ltd. Şti.' ),
					),
				),
			),
		),

		'home' => array(
			'label'             => 'Ana Sayfa',
			'path'              => '/',
			'sortable_sections' => array( 'intro', 'range', 'process', 'certificate', 'group', 'blog', 'ctaband' ),
			'components'        => array(

				'hero' => array(
					'label'  => 'Giriş (Hero)',
					'fields' => array(
						'image'           => array( 'label' => 'Arka Plan Fotoğrafı', 'type' => 'image', 'default' => 0 ),
						'title'           => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İhracata hazır ahşap palet ve sandık' ),
						'text'            => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Standart ölçülerin yanında istediğiniz ebatta üretiyoruz. Isıl işlemli, damgalı, Başakşehir’den teslim.' ),
						'primary_label'   => array( 'label' => 'Birinci Düğme Metni', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'primary_url'     => array( 'label' => 'Birinci Düğme Bağlantısı', 'type' => 'url', 'default' => '/iletisim/' ),
						'secondary_label' => array( 'label' => 'İkinci Düğme Metni', 'type' => 'text', 'default' => '0212 648 10 90’u arayın' ),
						'secondary_url'   => array( 'label' => 'İkinci Düğme Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'stamp_code'      => array( 'label' => 'Damga: İşaret Numarası', 'type' => 'text', 'default' => 'TR-1080-HT' ),
						'stamp_standard'  => array( 'label' => 'Damga: Standart', 'type' => 'text', 'default' => 'ISPM 15' ),
						'stamp_caption'   => array( 'label' => 'Damga Açıklaması', 'type' => 'text', 'default' => 'Paletlerimize basılan ısıl işlem işareti' ),
					),
				),

				'intro' => array(
					'label'  => 'Kısaca Biz',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ahşap ambalajı ölçünüze göre üretiyoruz' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Sanayi Palet, Koçist Grup’un ahşap palet ve sandık markası. Başakşehir’deki tesisimizde palet, sandık ve kafesi istediğiniz ölçüde üretiyor, ihracat siparişlerini ısıl işlemden geçirip damgalıyoruz.' ),
						'facts' => array(
							'label'   => 'Öne Çıkanlar',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'icon' => 'ruler', 'title' => 'İstenen ölçüde', 'text' => 'Standart ölçülerin yanında ürününüze göre özel ebat.' ),
								array( 'icon' => 'shield', 'title' => 'ISPM 15 damgalı', 'text' => 'Isıl işlem ve işaretleme izniyle ihracata hazır.' ),
								array( 'icon' => 'truck', 'title' => 'Adrese teslim', 'text' => 'Adresinize getiriyor ya da aracınıza yüklüyoruz.' ),
								array( 'icon' => 'recycle', 'title' => 'Yerli ve geri dönüştürülebilir', 'text' => 'Ürünlerimiz yerli üretim, ahşabı yeniden değerlendirilebilir.' ),
							),
						),
					),
				),

				'range' => array(
					'label'  => 'Ürün Gamı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ne üretiyoruz' ),
						'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Sevkiyat, depolama ve ihracat için üç ürün grubu. Hepsi ölçünüze göre.' ),
						'items' => array(
							'label'   => 'Ürün Grupları',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'image' => array( 'label' => 'Görsel', 'type' => 'image' ),
								'name'  => array( 'label' => 'Ad', 'type' => 'text' ),
								'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
								'spec'  => array( 'label' => 'Teknik Not', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array(
									'image' => 0,
									'name'  => 'Ahşap palet',
									'text'  => 'Euro palet ve standart ölçülerin yanında istenen ebatta özel palet.',
									'spec'  => '1.500 kg statik yük',
									'url'   => '/urunler/#paletler',
								),
								array(
									'image' => 0,
									'name'  => 'Ahşap sandık',
									'text'  => 'Farklı boy ve ebatta; ağır yükte sevkiyat, yükleme ve depolama için.',
									'spec'  => 'Ağır yük ve deniz aşırı sevkiyat',
									'url'   => '/urunler/#sandiklar',
								),
								array(
									'image' => 0,
									'name'  => 'Ahşap kafes',
									'text'  => 'Makine ve malzemenin hızlı, pratik sevkiyatı için açık gövdeli kafes.',
									'spec'  => 'Açık gövde, hızlı yükleme',
									'url'   => '/urunler/#kafesler',
								),
							),
						),
						'link_label' => array( 'label' => 'Kart Bağlantı Metni', 'type' => 'text', 'default' => 'Ayrıntılar' ),
					),
				),

				'process' => array(
					'label'  => 'Sipariş Süreci',
					'fields' => array(
						'title'           => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Sipariş nasıl ilerler' ),
						'text'            => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Talebinizden teslime kadar dört adım. Her adımda kimin ne yaptığı aşağıda.' ),
						'steps'           => array(
							'label'   => 'Adımlar',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'     => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title'    => array( 'label' => 'Adım Başlığı', 'type' => 'text' ),
								'actor'    => array( 'label' => 'Kim Yapar (ör. Sizden / Bizden)', 'type' => 'text' ),
								'text'     => array( 'label' => 'Adım Açıklaması', 'type' => 'textarea' ),
								'duration' => array( 'label' => 'Süre (boşsa gösterilmez)', 'type' => 'text' ),
							),
							'default' => array(
								array(
									'icon'     => 'ruler',
									'title'    => 'Ölçüyü ve adedi bildirin',
									'actor'    => 'Sizden',
									'text'     => 'Telefonla, WhatsApp’tan ya da formdan ürün tipini, ölçüyü, adedi ve taşınacak yükü iletin. Ölçüden emin değilseniz ürününüzü anlatmanız yeterli.',
									'duration' => '',
								),
								array(
									'icon'     => 'check',
									'title'    => 'Yazılı teklif gönderiyoruz',
									'actor'    => 'Bizden',
									'text'     => 'Fiyatı ve teslim tarihini yazılı olarak iletiyoruz. Onayınız gelince siparişiniz üretime alınıyor.',
									'duration' => '',
								),
								array(
									'icon'     => 'factory',
									'title'    => 'Üretim ve ısıl işlem',
									'actor'    => 'Bizden',
									'text'     => 'Başakşehir’deki tesisimizde ölçünüze göre üretiyoruz. İhracat siparişleri ısıl işlemden geçip ISPM 15 damgasıyla işaretleniyor.',
									'duration' => '',
								),
								array(
									'icon'     => 'truck',
									'title'    => 'Teslim',
									'actor'    => 'Bizden',
									'text'     => 'Adresinize teslim ediyoruz ya da aracınıza tesisimizde yüklüyoruz.',
									'duration' => '',
								),
							),
						),
						'checklist_title' => array( 'label' => 'Liste Başlığı', 'type' => 'text', 'default' => 'Teklif için bize gönderin' ),
						'checklist'       => array(
							'label'   => 'Teklif Listesi',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'item' => array( 'label' => 'Madde', 'type' => 'text' ),
								'hint' => array( 'label' => 'Örnek / Not', 'type' => 'text' ),
							),
							'default' => array(
								array( 'item' => 'Ürün tipi', 'hint' => 'Palet, sandık, kafes' ),
								array( 'item' => 'Ölçü', 'hint' => 'Örneğin 1200 × 800 mm' ),
								array( 'item' => 'Adet', 'hint' => '' ),
								array( 'item' => 'Taşınacak yük', 'hint' => 'Kilogram olarak' ),
								array( 'item' => 'İhracat mı?', 'hint' => 'Öyleyse ISPM 15 damgası gerekir' ),
								array( 'item' => 'Teslim yeri', 'hint' => 'Adres ya da tesisten araca yükleme' ),
							),
						),
						'button_label'    => array( 'label' => 'Düğme Metni', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'button_url'      => array( 'label' => 'Düğme Bağlantısı', 'type' => 'url', 'default' => '/iletisim/' ),
						'whatsapp_label'  => array( 'label' => 'WhatsApp Bağlantı Metni', 'type' => 'text', 'default' => 'Listeyi WhatsApp’tan gönderin' ),
						'whatsapp_url'    => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905323749832' ),
					),
				),

				'certificate' => array(
					'label'  => 'İhracat Belgesi',
					'fields' => array(
						'image'    => array( 'label' => 'Belge Görseli', 'type' => 'image', 'default' => 0 ),
						'title'    => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Paletleriniz gümrükte beklemez' ),
						'text'     => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'İhracatta kullanılan ahşap ambalajın ısıl işlemden geçmiş ve işaretlenmiş olması gerekir. Tarım ve Orman Bakanlığı’nın verdiği işaretleme izniyle ürünlerimizi ISPM 15’e uygun olarak işleyip damgalıyoruz.' ),
						'facts'    => array(
							'label'   => 'Belge Bilgileri',
							'type'    => 'repeater',
							'max'     => 5,
							'fields'  => array(
								'label' => array( 'label' => 'Bilgi Adı', 'type' => 'text' ),
								'value' => array( 'label' => 'Değer', 'type' => 'text' ),
							),
							'default' => array(
								array( 'label' => 'Belge', 'value' => 'Ahşap Ambalaj Malzemesi İşaretleme İzin Belgesi' ),
								array( 'label' => 'Belge numarası', 'value' => '1080' ),
								array( 'label' => 'İşaret türü', 'value' => 'HT (ısıl işlem)' ),
								array( 'label' => 'Veren kurum', 'value' => 'T.C. Tarım ve Orman Bakanlığı' ),
							),
						),
					),
				),

				'group' => array(
					'label'  => 'Koçist Grup',
					'fields' => array(
						'image'        => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Koçist Grup’un palet ve sandık markası' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => '1980’lerden bu yana orman ürünleri üretiyor ve dünyaya ihraç ediyoruz. 45 yıldır aynı ilkeyle çalışıyoruz: kaliteli ürün, kaliteli işçilik, müşteriyi dinleyen hizmet.' ),
						'button_label' => array( 'label' => 'Düğme Metni', 'type' => 'text', 'default' => 'Hakkımızda' ),
						'button_url'   => array( 'label' => 'Düğme Bağlantısı', 'type' => 'url', 'default' => '/hakkimizda/' ),
					),
				),

				'blog' => array(
					'label'  => 'Blogdan',
					'fields' => array(
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Palet rehberi' ),
						'link_label' => array( 'label' => 'Tüm Yazılar Bağlantı Metni', 'type' => 'text', 'default' => 'Bütün yazılar' ),
						'link_url'   => array( 'label' => 'Tüm Yazılar Bağlantısı', 'type' => 'url', 'default' => '/blog/' ),
					),
				),

				'ctaband' => array(
					'label'  => 'Teklif Şeridi',
					'fields' => array(
						'title'           => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ölçünüzü gönderin, fiyatını çıkaralım' ),
						'text'            => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => '08.00–19.00 arası telefonla ya da WhatsApp’tan ulaşabilirsiniz.' ),
						'primary_label'   => array( 'label' => 'Birinci Düğme Metni', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'primary_url'     => array( 'label' => 'Birinci Düğme Bağlantısı', 'type' => 'url', 'default' => '/iletisim/' ),
						'secondary_label' => array( 'label' => 'İkinci Düğme Metni', 'type' => 'text', 'default' => 'WhatsApp’tan yazın' ),
						'secondary_url'   => array( 'label' => 'İkinci Düğme Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905323749832' ),
					),
				),
			),
		),

		/*
		 * Hakkimizda. Metin refs/sanayi-palet/ana-site/hakkımzda.md'den; uzun tek
		 * paragraf okunur olsun diye uce bolundu, cumleler degismedi.
		 */
		'about' => array(
			'label'      => 'Hakkımızda',
			'path'       => '/hakkimizda/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'kicker' => array( 'label' => 'Üst Satır', 'type' => 'text', 'default' => 'Sanayi Palet hakkında' ),
						'title'  => array( 'label' => 'Başlık', 'type' => 'text', 'default' => '45 yıllık tecrübemiz ile sanayi palette iddialıyız' ),
						'image'  => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
					),
				),

				'story' => array(
					'label'  => 'Firma Metni',
					'fields' => array(
						'lead' => array( 'label' => 'Giriş Paragrafı', 'type' => 'textarea', 'default' => 'Ahşap ve orman ürünleri alanında 45 yıllık deneyimimiz neticesinde hizmet verdiğimiz alanlarda müşteri memnuniyeti ve kaliteyi göz önüne alarak ilerlemekteyiz.' ),
						'body' => array( 'label' => 'Metin (boş satırla paragraf ayrılır)', 'type' => 'textarea', 'default' => "Ahşap ürünler alanında; çeşitli paletler, ahşap kafes ve ahşap sandık ürünlerinin üretimini gerçekleştirmekteyiz. Geniş ürün yelpazemizle günden güne ülke çapında öncü firma haline gelerek, sektörde ihtiyaç duyduğunuz ahşap malzemelerin tedarikçisi olarak hizmet anlayışımızı sürdürüyoruz.\n\nMüşteri ilişkilerimizi güven esasına dayalı olarak kurmakta ve ürünlerimizin her zaman arkasında durmaktayız. Son teknolojiyi kullanarak üretimini gerçekleştirdiğimiz ahşap paletleri, isteğiniz doğrultusunda imal ederek sizlere ürün teslimatını gerçekleştirmekteyiz.\n\nEndüstriyel alanda iş kolaylığı ve iş güvenliği temellerini esas aldığımız bu yolda yerli ve milli üretimi destekliyor, geri dönüşüme uygun ürün hizmetleri sunuyoruz." ),
					),
				),

				'values' => array(
					'label'  => 'İlkelerimiz',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Nasıl çalışıyoruz' ),
						'items' => array(
							'label'   => 'İlkeler',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'icon' => 'star', 'title' => 'Önce kalite', 'text' => 'Her işte müşteri memnuniyetini ve kaliteyi göz önünde tutuyoruz.' ),
								array( 'icon' => 'check', 'title' => 'Ürünümüzün arkasındayız', 'text' => 'Müşteri ilişkilerimizi güvene dayandırıyoruz.' ),
								array( 'icon' => 'ruler', 'title' => 'İsteğinize göre üretim', 'text' => 'Paletleri son teknolojiyle, istediğiniz ölçüde üretiyoruz.' ),
								array( 'icon' => 'shield', 'title' => 'İş güvenliği', 'text' => 'Endüstride iş kolaylığı ve iş güvenliği temel ölçümüz.' ),
								array( 'icon' => 'recycle', 'title' => 'Yerli ve geri dönüşüme uygun', 'text' => 'Yerli üretimi destekliyor, geri dönüştürülebilir ürünler sunuyoruz.' ),
							),
						),
					),
				),

				'group' => array(
					'label'  => 'Koçist Grup',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => '1980’li yıllardan beri faaliyet gösteren Koçist Orman Ürünleri, orman ürünlerini dünyanın dört bir yanına ihraç ediyor. Koçist Grup Dış Ticaret Sanayi Limited Şirketi olarak kaliteli ürün, kaliteli işçilik ve müşteri odaklı hizmeti ilke edindik. Sanayi Palet, grubun ahşap palet ve sandık markasıdır.' ),
						'image'        => array( 'label' => 'Belge Görseli', 'type' => 'image', 'default' => 0 ),
						'image_note'   => array( 'label' => 'Belge Açıklaması', 'type' => 'text', 'default' => 'Ahşap Ambalaj Malzemesi İşaretleme İzin Belgesi, TR-1080-HT' ),
					),
				),
			),
		),

		/*
		 * Urunler. Palet metinleri refs/sanayi-palet/ürünşer/ürünler.md'den;
		 * sandik ve kafes metinleri sanayipalet.com urun sayfalarindan.
		 * Olcu alanlari bossa cizimde olcu cizgisi basilmaz; olcu uydurulmaz.
		 */
		'products' => array(
			'label'      => 'Ürünler',
			'path'       => '/urunler/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ürünlerimiz' ),
						'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Koçist olarak ahşap palet çeşitleri, ahşap sandık çeşitleri ve ahşap kafes çeşitleri üretmekteyiz.' ),
					),
				),

				'pallets' => array(
					'label'  => 'Ahşap Paletler',
					'fields' => array(
						'title'       => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ahşap paletler' ),
						'lead'        => array( 'label' => 'Giriş', 'type' => 'textarea', 'default' => 'Mevcut standart ölçülerin yanı sıra istenilen ebatlarda özel ahşap palet üretimi gerçekleştirmekteyiz. 1500 kg statik yük kapasitesi mevcuttur ve sanayi palet olarak kullanıma uygundur.' ),
						'text'        => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Kullanımı kolay, ekonomik ve birçok alanda kullanıma uygun olmasından ötürü en çok tercih edilen palet çeşitlerindendir. Üretim standartlarına uygun olarak imalatı gerçekleştirilmektedir. Yük durumu, yolculuk süresi ve taşınacak yükün ağırlığı baz alınarak tasarlanır ve üretim gerçekleştirilir. Uzun süreli kullanıma uygundur. Doğaya tekrar kazandırılabilir nitelikte çevre dostu ürün olarak üretimi gerçekleştirilir.' ),
						'types'       => array(
							'label'   => 'Palet Tipleri',
							'type'    => 'repeater',
							'max'     => 12,
							'fields'  => array(
								'name'    => array( 'label' => 'Ad', 'type' => 'text' ),
								'length'  => array( 'label' => 'Uzunluk (cm, boşsa çizimde ölçü yok)', 'type' => 'text' ),
								'width'   => array( 'label' => 'Genişlik (cm, boşsa çizimde ölçü yok)', 'type' => 'text' ),
								'deck'    => array( 'label' => 'Üst Tabla (açık / kapalı)', 'type' => 'text' ),
								'summary' => array( 'label' => 'Kısa Açıklama', 'type' => 'textarea' ),
								'specs'   => array( 'label' => 'Teknik Bilgiler (her satır "Ad: değer")', 'type' => 'textarea' ),
								'text'    => array( 'label' => 'Ayrıntılı Metin', 'type' => 'textarea' ),
							),
							'default' => array(
								array(
									'name'    => 'Euro palet',
									'length'  => '120',
									'width'   => '80',
									'deck'    => 'açık',
									'summary' => 'Avrupa standartlarında, nakil için en çok tercih edilen dayanıklı palet.',
									'specs'   => "Ölçü: 80 × 120 cm\nÇivi: 78 adet\nAğırlık: yaklaşık 28 kg\nTaşıma: yaklaşık 1 ton",
									'text'    => 'Standart boyutu 80×120 cm olan Euro paletlerimiz 78 adet çivi kullanılarak sabitlenmiştir. Nakil için kullanıma uygun bir palettir. Avrupa Standartlarına uygun olarak üretimi gerçekleştirilir. Yük durumu, yolculuk süresi ve taşınacak yükün ağırlığı baz alınarak tasarlanır ve üretim gerçekleştirilir. Sanayi palet çeşitleri arasında çok tercih edilen dayanıklı bir palet çeşididir. Euro palet ağırlığı yaklaşık 28 kg’dır. Uzun süreli kullanıma uygundur. Doğaya tekrar kazandırılabilir nitelikte çevre dostu ürün olarak üretimi gerçekleştirilir. Euro palet üzerinde yaklaşık 1 tonluk ürün taşıma kapasitesi bulunmaktadır.',
								),
								array(
									'name'    => 'Kapalı palet',
									'length'  => '120',
									'width'   => '100',
									'deck'    => 'kapalı',
									'summary' => 'Taşıma ve nakliyatta sık tercih edilen, üst tablası kapalı palet.',
									'specs'   => "Ölçü: 100 × 120 cm\nYük: ortalama taşıma kapasitesi\nÖzel ölçü: istenen ebatta üretilir",
									'text'    => 'Ortalama yük taşıma kapasitesine sahip 100×120 boyutlarında imal edilen palet türüdür. Taşıma ve nakliyat sektörlerinde sıklıkla tercih edilen paletlerdir. Forklift eşliğinde kolay taşıma sağlanabilir. İsteğe bağlı ölçü ve ebatlarda üretimi gerçekleştirilebilir. Ağır tonajlı ürünlerin yere temas etmeden herhangi bir hasar almadan nakliyatı sağlanır.',
								),
								array(
									'name'    => 'CP palet',
									'length'  => '',
									'width'   => '',
									'deck'    => 'açık',
									'summary' => 'Endüstriyel kullanım için, tekrar kullanılabilen sanayi paleti.',
									'specs'   => "Ölçü: 9 farklı boyut\nKullanım: tekrar kullanılabilir, geri dönüştürülebilir",
									'text'    => 'Endüstriyel alanda kullanıma uygun bir sanayi paleti çeşididir. Tekrar kullanıma uygun, geri dönüştürülebilen paletlerdendir. 9 farklı boyutta CP palet çeşidi bulunmaktadır.',
								),
								array(
									'name'    => 'Türpal palet',
									'length'  => '',
									'width'   => '',
									'deck'    => 'açık',
									'summary' => 'İthalat ve ihracat için uluslararası standartlarda palet.',
									'specs'   => "Ağırlık: 25–30 kg\nTaşıma: 1 tondan fazla\nKullanım: ithalat ve ihracat",
									'text'    => 'İthalat ve ihracat alanlarında kullanıma uygundur. Taşıma ve istifleme işlemlerine uygun, standartlar neticesinde üretilmiştir. Uzun ömürlü türpal paletler kullanışlı ve dayanıklıdır. Ortalama bir türpal palet ağırlığı 25-30 kg’dır. Uluslararası standartlara uygun olarak üretilmiştir. Bir tondan fazla ürün taşıma kapasitesi bulunmaktadır.',
								),
								array(
									'name'    => 'İkinci el palet',
									'length'  => '',
									'width'   => '',
									'deck'    => 'açık',
									'summary' => 'Standart ölçülerde, dayanıklı ve ekonomik kullanılmış palet.',
									'specs'   => "Ölçü: standart ölçüler\nÖmür: sıfır palete göre daha kısa",
									'text'    => 'Uzun ömürlü olan ahşap paletler oldukça tercih edilen bir kategoride ürünlerdir. İkinci el paletlerin ömrü sıfır paletlere göre daha azdır. Dayanıklı ve ekonomik olarak ikinci el paletler de tercih sebebi haline gelmektedir. İkinci el paletler standart ölçülere göre imal edilip satışa sunulmaktadır. Çeşitli alanlarda kullanıma uygundur.',
								),
							),
						),
						'more_label'  => array( 'label' => 'Ayrıntı Aç Metni', 'type' => 'text', 'default' => 'Ayrıntıları okuyun' ),
						'quote_label' => array( 'label' => 'Kart Teklif Düğmesi', 'type' => 'text', 'default' => 'Bu palet için teklif isteyin' ),
						'custom_title' => array( 'label' => 'Özel Ölçü Başlığı', 'type' => 'text', 'default' => 'Ölçünüz listede yok mu?' ),
						'custom_text'  => array( 'label' => 'Özel Ölçü Metni', 'type' => 'textarea', 'default' => 'Yük durumu, yolculuk süresi ve taşınacak yükün ağırlığına göre istediğiniz ebatta palet üretiyoruz.' ),
						'custom_label' => array( 'label' => 'Özel Ölçü Düğmesi', 'type' => 'text', 'default' => 'Özel ölçü için teklif isteyin' ),
					),
				),

				'crates' => array(
					'label'  => 'Ahşap Sandıklar',
					'fields' => array(
						'title'       => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ahşap sandıklar' ),
						'image'       => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
						'text'        => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Farklı boy ve ebatlarda üretimi gerçekleştirilen ahşap sandıklar sevkiyat, yükleme ve depolama alanlarında kullanıma uygun ürünlerdir. Ağır yükleri korur, depolama giderlerini azaltır; dayanıklı ve uzun ömürlüdür.' ),
						'list_title'  => array( 'label' => 'Liste Başlığı', 'type' => 'text', 'default' => 'Nerede kullanılır' ),
						'list'        => array( 'label' => 'Liste (her satır bir madde)', 'type' => 'textarea', 'default' => "Sevkiyat, yükleme ve boşaltma\nDepolama\nAğır ürünlerin korunması\nYurt dışı taşımacılık\nEndüstri ve imalat sektörü" ),
						'quote_label' => array( 'label' => 'Teklif Düğmesi', 'type' => 'text', 'default' => 'Sandık için teklif isteyin' ),
					),
				),

				'cages' => array(
					'label'  => 'Ahşap Kafesler',
					'fields' => array(
						'title'       => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ahşap kafesler' ),
						'image'       => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
						'text'        => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Endüstri ve sanayi alanlarında malzeme veya ürünlerin kolay sevkiyatının sağlanmasında ahşap kafes ürünleri tercih edilmektedir.' ),
						'list_title'  => array( 'label' => 'Liste Başlığı', 'type' => 'text', 'default' => 'Ne sağlar' ),
						'list'        => array( 'label' => 'Liste (her satır bir madde)', 'type' => 'textarea', 'default' => "Pratik ve hızlı taşıma\nÜrünü dış etkenlerden korur\nNakliyedeki riski ve kaybı azaltır\nMalzemenin dağılmasını ve fazla yer kaplamasını önler" ),
						'quote_label' => array( 'label' => 'Teklif Düğmesi', 'type' => 'text', 'default' => 'Kafes için teklif isteyin' ),
					),
				),
			),
		),

		/*
		 * Iletisim. Bilgi basliklari ve form alanlari
		 * refs/sanayi-palet/ana-site/iletisim.md'den.
		 */
		'contact' => array(
			'label'      => 'İletişim',
			'path'       => '/iletisim/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İletişim' ),
						'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Teklif, sipariş ya da sorunuz için arayın, WhatsApp’tan yazın ya da formu doldurun.' ),
					),
				),

				'info' => array(
					'label'  => 'İletişim Bilgileri',
					'fields' => array(
						'address_label'  => array( 'label' => 'Adres Başlığı', 'type' => 'text', 'default' => 'Adresimiz' ),
						'address'        => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => "Şahintepe, Eski İstanbul Cd. No:176\n34494 Başakşehir / İstanbul" ),
						'phone_label'    => array( 'label' => 'Telefon Başlığı', 'type' => 'text', 'default' => 'Telefonumuz' ),
						'phone'          => array( 'label' => 'Telefon', 'type' => 'text', 'default' => '+90 212 648 10 90' ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Başlığı', 'type' => 'text', 'default' => 'WhatsApp' ),
						'whatsapp'       => array( 'label' => 'WhatsApp Numarası', 'type' => 'text', 'default' => '+90 532 374 98 32' ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905323749832' ),
						'email_label'    => array( 'label' => 'E-posta Başlığı', 'type' => 'text', 'default' => 'E-posta adresimiz' ),
						'email'          => array( 'label' => 'E-posta', 'type' => 'text', 'default' => 'info@sanayipalet.com' ),
						'email_url'      => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@sanayipalet.com' ),
					),
				),

				'form' => array(
					'label'  => 'Mesaj Formu',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Bize yazın' ),
						'text'         => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Teklif istiyorsanız ürün tipini, ölçüyü ve adedi mesajınıza ekleyin.' ),
						'button_label' => array( 'label' => 'Gönder Düğmesi', 'type' => 'text', 'default' => 'Mesajı gönderin' ),
						'success'      => array( 'label' => 'Gönderildi Mesajı', 'type' => 'textarea', 'default' => 'Mesajınız bize ulaştı. Size e-posta ya da telefonla dönüş yapacağız.' ),
					),
				),

				'map' => array(
					'label'  => 'Harita',
					'fields' => array(
						'query'            => array( 'label' => 'Haritada Aranacak Adres', 'type' => 'text', 'default' => 'Şahintepe, Eski İstanbul Cd. No:176, 34494 Başakşehir/İstanbul' ),
						'directions_label' => array( 'label' => 'Yol Tarifi Metni', 'type' => 'text', 'default' => 'Yol tarifi alın' ),
					),
				),
			),
		),

		'blog' => array(
			'label'      => 'Blog',
			'path'       => '/blog/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başı',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Palet rehberi' ),
						'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Palet, sandık ve ihracat ambalajı üzerine kısa yazılar.' ),
					),
				),

				'post' => array(
					'label'  => 'Yazı Sayfası',
					'fields' => array(
						'read_label'  => array( 'label' => 'Yazıyı Aç Bağlantı Metni', 'type' => 'text', 'default' => 'Yazıyı okuyun' ),
						'back_label'  => array( 'label' => 'Geri Dön Bağlantı Metni', 'type' => 'text', 'default' => 'Bütün yazılar' ),
						'more_title'  => array( 'label' => 'Diğer Yazılar Başlığı', 'type' => 'text', 'default' => 'Diğer yazılar' ),
					),
				),
			),
		),
	),
);
