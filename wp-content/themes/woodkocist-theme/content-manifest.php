<?php
/**
 * woodkocist temasi alan manifesti (YEREL DENEME).
 *
 * Urunler burada tanimlanmaz: merkezi Urun Havuzu'ndan gelir. Hangi urunun
 * sitede gorunecegi Icerik Studyosu -> Ana Sayfa -> Urunler alanindan secilir
 * (urun ekle / cikar / sirala / siteye ozel ad-fiyat). Kod gerekmez.
 *
 * Iletisim: woodkocist.com.tr'nin kendi sayfasinda gorunen WhatsApp numarasi
 * (0549 648 19 19). Adres grubun ortak adresi (Kestanelik / Catalca). Sitenin
 * kendi sayfasinda telefon ve e-posta gorunmedigi icin bos; panelden girilince
 * gorunur.
 */

$address = "Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1\nÇatalca, İstanbul";

return array(
	'site_key'          => 'woodkocist',
	'site_label'        => 'Koçist · woodkocist.com.tr',

	'seo_site_defaults' => array(
		'name'        => 'WOOD KOCIST',
		'legal_name'  => '',
		'description' => 'Ahşap bahçe mobilyası, ev ürünleri ve evcil hayvan yuvaları: Adirondack sandalye, çardak, kamelya, piknik masası, şezlong, kedi yuvası ve köpek kulübesi. Koçist Grup kuruluşu.',
		'phone'       => '+90 549 648 19 19',
		'email'       => '',
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

		'global' => array(
			'label'      => 'Tüm Sayfalar (Üst Menü, Alt Bilgi)',
			'path'       => '/',
			'components' => array(
				'header' => array(
					'label'  => 'Üst Menü',
					'fields' => array(
						'logo_text'      => array( 'label' => 'Marka', 'type' => 'text', 'default' => 'WOOD KOCIST' ),
						'menu'           => array(
							'label'   => 'Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı Adresi', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ürünler', 'url' => '/#urunler' ),
								array( 'label' => 'Nasıl sipariş verilir', 'url' => '/#siparis-adimlari' ),
								array( 'label' => 'Hakkımızda', 'url' => '/hakkimizda/' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905496481919' ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Numarası (görünen)', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'phone_label'    => array( 'label' => 'Telefon (boşsa gizlenir)', 'type' => 'text', 'default' => '' ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => '' ),
						'email'          => array( 'label' => 'E-posta (boşsa gizlenir)', 'type' => 'text', 'default' => '' ),
					),
				),
				'footer' => array(
					'label'  => 'Alt Bilgi',
					'fields' => array(
						'tagline'   => array( 'label' => 'Alt Bilgi Metni', 'type' => 'textarea', 'default' => 'Ahşap bahçe mobilyası, ev ürünleri ve evcil hayvan yuvaları. Koçist Grup kuruluşudur.' ),
						'address'   => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => $address ),
						'copyright' => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => 'WOOD KOCIST · Koçist Grup' ),
					),
				),
			),
		),

		'home' => array(
			'label'      => 'Ana Sayfa',
			'path'       => '/',
			'seo_source' => array(
				'title'       => 'hero.title',
				'description' => 'hero.lead',
			),
			'components' => array(
				'hero' => array(
					'label'  => 'Giriş',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Bahçeye, eve ve dostlarınıza ahşap' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Adirondack sandalye, çardak, kamelya, piknik masası ve şezlong; kedi yuvası ve köpek kulübesi. Beğendiğiniz ürünü WhatsApp’tan ya da formla sorun, fiyat ve teslim bilgisiyle dönelim.' ),
					),
				),
				'catalog' => array(
					'label'  => 'Ürünler',
					'fields' => array(
						'title'     => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Ürünler' ),
						'all_label' => array( 'label' => '"Tümü" Düğmesi', 'type' => 'text', 'default' => 'Tümü' ),
						'pool'      => array(
							'label' => 'Sitede görünen ürünler (Ürün Havuzu’ndan seçin, sıralayın)',
							'type'  => 'products',
						),
						'lines'     => array(
							'label'   => 'Seriler (üstteki büyük seçici)',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'label'    => array( 'label' => 'Seri Adı', 'type' => 'text' ),
								'category' => array( 'label' => 'Havuzdaki Kategori Adı (birebir)', 'type' => 'text' ),
								'text'     => array( 'label' => 'Kısa Açıklama', 'type' => 'text' ),
							),
							'default' => array(
								array( 'label' => 'WOODGarden', 'category' => 'WOODGarden', 'text' => 'Bahçe ve teras' ),
								array( 'label' => 'WOODLiving', 'category' => 'WOODLiving', 'text' => 'Ev ve balkon' ),
								array( 'label' => 'WOODPets', 'category' => 'WOODPets', 'text' => 'Kedi ve köpek yuvaları' ),
							),
						),
						'empty'     => array( 'label' => 'Ürün Yokken Görünen Metin', 'type' => 'text', 'default' => 'Bu seride şu an gösterilen ürün yok. Aradığınızı WhatsApp’tan sorun.' ),
					),
				),
				'steps' => array(
					'label'  => 'Nasıl Sipariş Verilir',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Nasıl sipariş verilir' ),
						'items' => array(
							'label'   => 'Adımlar',
							'type'    => 'repeater',
							'max'     => 4,
							'fields'  => array(
								'title' => array( 'label' => 'Adım', 'type' => 'text' ),
								'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'title' => 'Ürünü seçin', 'text' => 'Ürün kodunu not edin ya da ürün sayfasındaki düğmeye basın; kod mesaja kendiliğinden eklenir.' ),
								array( 'title' => 'Bize yazın', 'text' => 'WhatsApp’tan ya da sipariş formundan adedi ve teslim adresinin ilini yazın.' ),
								array( 'title' => 'Fiyat ve teslim bilgisi', 'text' => 'Ürünün güncel fiyatı ve teslim seçenekleriyle size dönüş yapalım.' ),
							),
						),
					),
				),
				'contact' => array(
					'label'  => 'Sipariş Bölümü',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Sipariş ve soru' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Ürün kodunu, adedi ve teslim ilini yazın; size dönelim.' ),
					),
				),
			),
		),

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
						'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => 'WOOD KOCIST, Koçist Grup’un bahçe, ev ve evcil hayvan ürünleri markasıdır.' ),
					),
				),
				'story' => array(
					'label'  => 'Metin',
					'fields' => array(
						'text' => array( 'label' => 'Metin (paragrafları boş satırla ayırın)', 'type' => 'textarea', 'default' => "Koçist Grup 50 yıldır orman ürünleri alanında çalışıyor. WOOD KOCIST bu deneyimi bahçeye, eve ve evcil hayvanlara taşıyor: Adirondack sandalyeden çardağa, kamelyadan köpek kulübesine ahşap ürünler.\n\nÜrünlerin ahşap cinsi, boyutu, taşıma kapasitesi ve kurulum bilgisi her ürün sayfasında yazıyor. Aradığınızı bulamazsanız bize yazın." ),
					),
				),
			),
		),

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
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İletişim' ),
						'lead'  => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Ürün kodunu ve adedi yazın ya da WhatsApp’tan ulaşın; size dönelim.' ),
					),
				),
				'details' => array(
					'label'  => 'Bilgiler',
					'fields' => array(
						'address' => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => $address ),
					),
				),
			),
		),
	),
);
