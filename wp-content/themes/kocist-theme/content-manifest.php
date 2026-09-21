<?php
/**
 * Kocist temasi alan manifesti.
 *
 * Bu dosya saf bir dizi dondurur; hicbir WordPress fonksiyonuna bagimli degildir.
 * Boylece ag paneli, siteye gecmeden dosya sisteminden okuyabilir.
 *
 * Alan turleri: text, textarea, url, image, icon, repeater
 */

return array(
	'site_key'   => 'kocist',
	'site_label' => 'Koçist',
	'pages'      => array(

		'global' => array(
			'label'      => 'Tüm Sayfalar (Üst Bilgi, Menü, Footer)',
			'path'       => '/',
			'components' => array(

				'topbar' => array(
					'label'  => 'Üst Bilgi Şeridi',
					'fields' => array(
						'note'        => array( 'label' => 'Şerit Metni', 'type' => 'text', 'default' => 'Çatalca üretim tesisi · Toptan kereste ve ahşap ambalaj' ),
						'hours_icon'  => array( 'label' => 'Çalışma Saati İkonu', 'type' => 'icon', 'default' => 'clock' ),
						'hours'       => array( 'label' => 'Çalışma Saatleri', 'type' => 'text', 'default' => 'Hafta içi 08:00 - 19:00' ),
						'phone_icon'  => array( 'label' => 'Telefon İkonu', 'type' => 'icon', 'default' => 'phone' ),
						'phone_label' => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'phone_url'   => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
						'email_label' => array( 'label' => 'E-posta Metni', 'type' => 'text', 'default' => 'info@kocist.com.tr' ),
						'email_url'   => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@kocist.com.tr' ),
					),
				),

				'header' => array(
					'label'  => 'Üst Menü / Başlık',
					'fields' => array(
						'logo_image' => array( 'label' => 'Logo Görseli', 'type' => 'image', 'default' => 0 ),
						'logo_text'  => array( 'label' => 'Logo Yazısı', 'type' => 'text', 'default' => 'KOÇİST' ),
						'logo_sub'   => array( 'label' => 'Logo Alt Yazısı', 'type' => 'text', 'default' => 'Orman Ürünleri' ),
						'menu'       => array(
							'label'   => 'Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ana Sayfa', 'url' => '/' ),
								array( 'label' => 'Kereste', 'url' => '#kereste' ),
								array( 'label' => 'Ambalaj', 'url' => '#ambalaj' ),
								array( 'label' => 'Dekorasyon', 'url' => '#dekorasyon' ),
								array( 'label' => 'Hırdavat', 'url' => '#hirdavat' ),
								array( 'label' => 'Kurumsal', 'url' => '/kurumsal/' ),
							),
						),
						'cta_label'  => array( 'label' => 'Menü Buton Metni', 'type' => 'text', 'default' => 'Teklif Alın' ),
						'cta_url'    => array( 'label' => 'Menü Buton Bağlantısı', 'type' => 'url', 'default' => '#teklif' ),
					),
				),

				'footer' => array(
					'label'  => 'Footer',
					'fields' => array(
						'about_title'   => array( 'label' => 'Tanıtım Başlığı', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri' ),
						'about_text'    => array( 'label' => 'Tanıtım Metni', 'type' => 'textarea', 'default' => 'Kereste, ahşap ambalaj, dekorasyon ve hırdavat gruplarında toptan tedarik. Bu bir yerel demo kurulumudur.' ),
						'links_title'   => array( 'label' => 'Bağlantılar Başlığı', 'type' => 'text', 'default' => 'Ürün Grupları' ),
						'links'         => array(
							'label'   => 'Footer Bağlantıları',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Kereste', 'url' => '#kereste' ),
								array( 'label' => 'Ahşap Ambalaj', 'url' => '#ambalaj' ),
								array( 'label' => 'Dekorasyon', 'url' => '#dekorasyon' ),
								array( 'label' => 'Hırdavat', 'url' => '#hirdavat' ),
							),
						),
						'contact_title' => array( 'label' => 'İletişim Başlığı', 'type' => 'text', 'default' => 'İletişim' ),
						'address_icon'  => array( 'label' => 'Adres İkonu', 'type' => 'icon', 'default' => 'pin' ),
						'address'       => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => 'Kestanelik Mahallesi, Çatalca / İstanbul' ),
						'phone_label'   => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'phone_url'     => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
						'email_label'   => array( 'label' => 'E-posta Metni', 'type' => 'text', 'default' => 'info@kocist.com.tr' ),
						'email_url'     => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@kocist.com.tr' ),
						'copyright'     => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => '© 2026 Koçist — yerel demo kurulumu' ),
						'demo_note'     => array( 'label' => 'Demo Uyarısı', 'type' => 'text', 'default' => 'Bu sayfadaki görseller örnek yer tutuculardır; gerçek marka görseli değildir.' ),
					),
				),
			),
		),

		'home' => array(
			'label'             => 'Ana Sayfa',
			'path'              => '/',
			'sortable_sections' => array( 'capabilities', 'catalog', 'references', 'ctaband' ),
			'components'        => array(

				'hero' => array(
					'label'  => 'Hero (Giriş Bölümü)',
					'fields' => array(
						'eyebrow'         => array( 'label' => 'Üst Etiket', 'type' => 'text', 'default' => 'ENDÜSTRİYEL AHŞAP TEDARİK' ),
						'title'           => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Sanayinin ahşap ihtiyacını tek tedarikçiden karşılayın' ),
						'description'     => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Kereste, ihracat paleti, ahşap kafes ve sandık üretiminde stoklu çalışıyor; proje bazlı özel ölçü taleplerini kendi tesisimizde karşılıyoruz.' ),
						'primary_label'   => array( 'label' => 'Birincil Buton Metni', 'type' => 'text', 'default' => 'Teklif Formu' ),
						'primary_url'     => array( 'label' => 'Birincil Buton Bağlantısı', 'type' => 'url', 'default' => '#teklif' ),
						'secondary_label' => array( 'label' => 'İkincil Buton Metni', 'type' => 'text', 'default' => 'Ürün Gruplarını Gör' ),
						'secondary_url'   => array( 'label' => 'İkincil Buton Bağlantısı', 'type' => 'url', 'default' => '#katalog' ),
						'image'           => array( 'label' => 'Hero Görseli', 'type' => 'image', 'default' => 0 ),
						'stats'           => array(
							'label'   => 'Rakam Şeridi',
							'type'    => 'repeater',
							'max'     => 4,
							'fields'  => array(
								'value' => array( 'label' => 'Değer', 'type' => 'text' ),
								'label' => array( 'label' => 'Etiket', 'type' => 'text' ),
							),
							'default' => array(
								array( 'value' => '4', 'label' => 'ana ürün grubu' ),
								array( 'value' => 'ISPM-15', 'label' => 'ihracat ısıl işlem' ),
								array( 'value' => '08:00-19:00', 'label' => 'hafta içi sevkiyat' ),
							),
						),
					),
				),

				'capabilities' => array(
					'label'  => 'Yetkinlik Kartları',
					'fields' => array(
						'title'    => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Neden Koçist' ),
						'subtitle' => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Tedarik, üretim ve sevkiyatı aynı çatı altında yürütüyoruz.' ),
						'items'    => array(
							'label'   => 'Kartlar',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title' => array( 'label' => 'Kart Başlığı', 'type' => 'text' ),
								'text'  => array( 'label' => 'Kart Metni', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'icon' => 'factory', 'title' => 'Kendi tesisimizde üretim', 'text' => 'Kesim, ölçülendirme ve ambalaj hattı Çatalca tesisinde.' ),
								array( 'icon' => 'shield', 'title' => 'İhracata uygun işlem', 'text' => 'ISPM-15 ısıl işlem gerektiren gönderiler için uygun ambalaj.' ),
								array( 'icon' => 'ruler', 'title' => 'Özel ölçü', 'text' => 'Proje bazlı palet, kafes ve sandık ölçüleri.' ),
								array( 'icon' => 'truck', 'title' => 'Planlı sevkiyat', 'text' => 'Hafta içi günlük çıkış, İstanbul ve çevre iller.' ),
							),
						),
					),
				),

				'catalog' => array(
					'label'  => 'Ürün Grupları',
					'fields' => array(
						'title'    => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Ürün Grupları' ),
						'subtitle' => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Demo kapsamında dört ana grup gösteriliyor.' ),
						'items'    => array(
							'label'   => 'Grup Kartları',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'image'      => array( 'label' => 'Görsel', 'type' => 'image' ),
								'title'      => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'       => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
								'link_label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'link_url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'image' => 0, 'title' => 'Kereste', 'text' => 'Çam ve karaçam kereste, sunta ve kontrplak levha.', 'link_label' => 'Detay', 'link_url' => '#kereste' ),
								array( 'image' => 0, 'title' => 'Ahşap Ambalaj', 'text' => 'Palet, kafes, sandık ve ihracat ambalajı.', 'link_label' => 'Detay', 'link_url' => '#ambalaj' ),
								array( 'image' => 0, 'title' => 'Dekorasyon', 'text' => 'Kamelya, salıncak, pergola ve bahçe ürünleri.', 'link_label' => 'Detay', 'link_url' => '#dekorasyon' ),
								array( 'image' => 0, 'title' => 'Hırdavat', 'text' => 'Bağlantı elemanları ve tamamlayıcı hırdavat grubu.', 'link_label' => 'Detay', 'link_url' => '#hirdavat' ),
							),
						),
					),
				),

				'references' => array(
					'label'  => 'Kullanım Alanları',
					'fields' => array(
						'title'    => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Kullanım Alanları' ),
						'subtitle' => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Ürünlerimizin sahada karşılık bulduğu başlıca alanlar.' ),
						'items'    => array(
							'label'   => 'Satırlar',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'  => array( 'label' => 'Metin', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'icon' => 'box', 'title' => 'İhracat ambalajı', 'text' => 'Yurt dışı sevkiyatlarda ısıl işlemli palet ve sandık.' ),
								array( 'icon' => 'truck', 'title' => 'Lojistik ve depolama', 'text' => 'Depo içi taşıma ve raf düzeni için standart palet.' ),
								array( 'icon' => 'tree', 'title' => 'Şantiye keresteci', 'text' => 'Kalıp ve iskele işlerinde kereste tedariki.' ),
							),
						),
					),
				),

				'ctaband' => array(
					'label'  => 'Teklif Şeridi',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ölçü ve adet verin, aynı gün fiyatlayalım' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Talebinizi telefonla veya e-posta ile iletin.' ),
						'button_label' => array( 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'Hemen Arayın' ),
						'button_url'   => array( 'label' => 'Buton Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
						'note'         => array( 'label' => 'Alt Not', 'type' => 'text', 'default' => 'Demo kurulum: form gönderimi yapılmaz, telefon bağlantısı gerçektir.' ),
					),
				),
			),
		),

		'inner' => array(
			'label'      => 'Kurumsal (Örnek Alt Sayfa)',
			'path'       => '/kurumsal/',
			'components' => array(

				'page_head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'breadcrumb' => array( 'label' => 'Yol Göstergesi', 'type' => 'text', 'default' => 'Ana Sayfa / Kurumsal' ),
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Kurumsal' ),
						'subtitle'   => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Üretim kapasitemiz, çalışma biçimimiz ve tedarik yaklaşımımız.' ),
					),
				),

				'story' => array(
					'label'  => 'Kurumsal Metin',
					'fields' => array(
						'title'  => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Tedarikten sevkiyata tek muhatap' ),
						'body'   => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Koçist; kereste, ahşap ambalaj, dekorasyon ve hırdavat gruplarında kurumsal müşterilere tedarik yapar. Bu demo kurulumda metinler örnek amaçlıdır ve panelden düzenlenebilir.' ),
						'image'  => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
						'points' => array(
							'label'   => 'Maddeler',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon' => array( 'label' => 'İkon', 'type' => 'icon' ),
								'text' => array( 'label' => 'Metin', 'type' => 'text' ),
							),
							'default' => array(
								array( 'icon' => 'check', 'text' => 'Stoklu çalışma ve hızlı teyit' ),
								array( 'icon' => 'check', 'text' => 'Proje bazlı özel ölçü üretimi' ),
								array( 'icon' => 'check', 'text' => 'Kurumsal fatura ve sevk irsaliyesi' ),
							),
						),
					),
				),

				'values' => array(
					'label'  => 'Çalışma İlkeleri',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Çalışma İlkeleri' ),
						'items' => array(
							'label'   => 'Kartlar',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'  => array( 'label' => 'Metin', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'icon' => 'clock', 'title' => 'Zamanında teslim', 'text' => 'Teyit edilen sevk tarihine bağlı kalırız.' ),
								array( 'icon' => 'recycle', 'title' => 'Malzemenin izi', 'text' => 'Kullanılan ahşabın kaynağı ve işlemi kayıt altındadır.' ),
								array( 'icon' => 'shield', 'title' => 'Şartname uyumu', 'text' => 'İhracat ambalajında istenen belgelere uygun üretim.' ),
							),
						),
					),
				),
			),
		),
	),
);
