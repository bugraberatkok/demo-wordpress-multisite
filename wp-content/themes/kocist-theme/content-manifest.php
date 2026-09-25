<?php
/**
 * Kocist temasi alan manifesti.
 *
 * Bu dosya saf bir dizi dondurur; hicbir WordPress fonksiyonuna bagimli degildir.
 * Boylece ag paneli, siteye gecmeden dosya sisteminden okuyabilir.
 *
 * Alan turleri: text, textarea, url, image, icon, repeater, products
 */

/*
 * Urun kategorileri (/kategoriler/<grup>/<kategori>/). Anahtarlar menudeki
 * capalarla aynidir (#ahsap-kalas); menude ad degisse de sayfa adresi degismez.
 *
 * Her satir: array( sayfa etiketi, Urun Havuzu'ndaki kategori adi, [ek adlar] ).
 * Havuz kategorisindeki urunler o sayfada listelenir ve panelde o sayfanin
 * "Ürünler" listesinden secilir. Ek adlar: baska sitenin kategorisindeki ortak
 * urunler de bu sayfada gorunur (orn. WOOD KOCIST'in WOODPets urunleri); onlar
 * ana sayfadaki urun listesinden secilir. Havuz adi bos ise yalnizca metin.
 *
 * Havuz ortaktir: WOOD KOCIST'in urunleri (kamelya, cardak...) burada kendi
 * havuz kategorileriyle eslenir; o urunlere Kocist icin yeni kategori
 * eklenmez (WOOD KOCIST menusunu degistirirdi).
 */
$kocist_catalog = array(
	'kereste'    => array(
		'label' => 'Kereste',
		'pool'  => 'Kereste',
		'subs'  => array(
			'ahsap-kalas'               => array( 'Ahşap Kalas', 'Ahşap Kalas' ),
			'ahsap-cita'                => array( 'Ahşap Çıta', 'Ahşap Çıta' ),
			'ahsap-rabita'              => array( 'Ahşap Rabıta', 'Ahşap Rabıta' ),
			'osb-plaka-levha'           => array( 'OSB Plaka Levha', 'OSB Plaka Levha' ),
			'kontrplak-levha'           => array( 'Kontrplak Levha', 'Kontrplak Levha' ),
			'plywood-levha'             => array( 'Plywood Levha', 'Plywood Levha' ),
			'maden-diregi-kereste'      => array( 'Maden Direği Kereste', 'Maden Direği Kereste' ),
			'ahsap-takoz'               => array( 'Ahşap Takoz', 'Ahşap Takoz' ),
			'insaatlik-catilik-kereste' => array( 'İnşaatlık & Çatılık Kereste', 'İnşaatlık ve Çatılık Kereste' ),
		),
	),
	'ambalaj'    => array(
		'label' => 'Ambalaj',
		'pool'  => 'Ahşap Ambalaj',
		'subs'  => array(
			'ahsap-palet'        => array( 'Ahşap Palet', 'Ahşap Palet' ),
			'ahsap-sandik'       => array( 'Ahşap Sandık', 'Ahşap Sandık' ),
			'ahsap-kafes'        => array( 'Ahşap Kafes', 'Ahşap Kafes' ),
			'triplex-bariyer'    => array( 'Triplex Bariyer', 'Triplex Bariyer' ),
			'cam-tasima-sehpasi' => array( 'Cam Taşıma Sehpası', 'Cam Taşıma Sehpası' ),
		),
	),
	'dekorasyon' => array(
		'label' => 'Dekorasyon',
		'pool'  => 'Dekorasyon',
		'subs'  => array(
			'ahsap-ev'            => array( 'Ahşap Ev', 'Ahşap Ev' ),
			'kamelya'             => array( 'Kamelya', 'Kamelyalar' ),
			'cardak'              => array( 'Çardak', 'Çardaklar' ),
			'ahsap-bank'          => array( 'Ahşap Bank', 'Ahşap Bank', array( 'Piknik Masaları' ) ),
			'saksi-sebze-yatagi'  => array( 'Saksı & Sebze Yatağı', 'Saksı ve Sebze Yatağı' ),
			'ahsap-salincak'      => array( 'Ahşap Salıncak', 'Ahşap Salıncak' ),
			'ahsap-pergola'       => array( 'Ahşap Pergola', 'Ahşap Pergola' ),
			'ahsap-sezlong'       => array( 'Ahşap Şezlong', 'Şezlonglar' ),
			'ahsap-tahterevalli'  => array( 'Ahşap Tahterevalli', 'Ahşap Tahterevalli' ),
			'ahsap-yatak'         => array( 'Ahşap Yatak', 'Ahşap Yatak' ),
			'ahsap-ayak-alti'     => array( 'Ahşap Ayak Altı', 'Ahşap Ayak Altı' ),
			'hayvan-barinaklari'  => array( 'Hayvan Barınakları', 'Hayvan Barınakları', array( 'WOODPets', 'Köpek Kulübeleri', 'Kedi Yuvaları' ) ),
			'adirondack-sandalye' => array( 'Adirondack Sandalye', 'Adirondack' ),
			'celik-yapi'          => array( 'Çelik Yapı', 'Çelik Yapı' ),
		),
	),
	'hirdavat'   => array(
		'label' => 'Hırdavat',
		'pool'  => 'Hırdavat',
		'subs'  => array(
			'civiler'                   => array( 'Çiviler', 'Çiviler' ),
			'zimba-telleri'             => array( 'Zımba Telleri', 'Zımba Telleri' ),
			'klipsler'                  => array( 'Klipsler', 'Klipsler' ),
			'baglama-telleri'           => array( 'Bağlama Telleri', 'Bağlama Telleri' ),
			'civi-tabancalari'          => array( 'Zımba & Çivi Tabancaları', 'Zımba ve Çivi Tabancaları' ),
			'sartlandiricilar'          => array( 'Şartlandırıcılar', 'Şartlandırıcılar' ),
			'kompresorler'              => array( 'Kompresörler', 'Kompresörler' ),
			'somun-sokme-aletleri'      => array( 'Somun Sökme Aletleri', 'Somun Sökme Aletleri' ),
			'yuzey-gelistirme-diskleri' => array( 'Yüzey Geliştirme Diskleri', 'Yüzey Geliştirme Diskleri' ),
			'maket-bicagi'              => array( 'Maket Bıçağı', 'Maket Bıçağı' ),
			'kulplar'                   => array( 'Kulplar', 'Kulplar' ),
			'menteseler'                => array( 'Menteşeler', 'Menteşeler' ),
			'surguler'                  => array( 'Sürgüler', 'Sürgüler' ),
			'gonyeler'                  => array( 'Gönyeler', 'Gönyeler' ),
			'aski-sabitleme'            => array( 'Askı & Sabitleme', 'Askı ve Sabitleme Elemanları' ),
			'vida-grubu'                => array( 'Vida Grubu', 'Vida Grubu' ),
			'civata-grubu'              => array( 'Civata Grubu', 'Civata Grubu' ),
			'mandallar'                 => array( 'Mandallar', 'Mandallar' ),
			'pergola-ayaklari'          => array( 'Pergola Ayakları', 'Pergola Ayakları' ),
			'matkap-uclari'             => array( 'Matkap & Tornavida Uçları', 'Matkap ve Tornavida Uçları' ),
			'purmuzler'                 => array( 'Pürmüzler', 'Pürmüzler' ),
			'shingle-cati'              => array( 'Shingle Çatı', 'Shingle Çatı Kaplamaları' ),
			'likit-membran'             => array( 'Likit Membran', 'Likit Membran' ),
			'su-yalitim-membranlari'    => array( 'Su Yalıtım Membranları', 'Su Yalıtım Membranları' ),
		),
	),
);

/*
 * Her grup ve kategori icin panelde gizli bir sayfa: sekme listesinde
 * gorunmez, onizlemede kategori sayfasinda tiklaninca acilir. Ortak metinler
 * gorunur "Kategoriler" sekmesindedir.
 */
$kocist_category_pages = array();

$kocist_category_page = static function ( string $label, string $path, string $group, string $sub, string $pool, array $aliases = array() ): array {
	$components = array(
		'head' => array(
			'label'  => 'Sayfa Başlığı',
			'fields' => array(
				'lead' => array(
					'label'   => 'Alt Metin',
					'type'    => 'textarea',
					'default' => '',
					'hint'    => 'Boş bırakılırsa Kategoriler sekmesindeki ortak metin görünür. Sayfa başlığı menüdeki addır.',
				),
			),
		),
	);

	if ( '' !== $pool ) {
		$components['products'] = array(
			'label'  => 'Ürünler',
			'fields' => array(
				'pool' => array(
					'label'    => 'Bu sayfadaki ürünler (Ürün Havuzu: ' . $pool . ')',
					'type'     => 'products',
					'category' => $pool,
				),
			),
		);
	}

	return array(
		'label'      => $label,
		'path'       => $path,
		'hidden'     => true,
		'catalog'    => array( 'group' => $group, 'sub' => $sub, 'aliases' => $aliases ),
		'components' => $components,
	);
};

foreach ( $kocist_catalog as $kocist_group => $kocist_row ) {
	$kocist_category_pages[ 'grp-' . $kocist_group ] = $kocist_category_page( 'Grup: ' . $kocist_row['label'], '/kategoriler/' . $kocist_group . '/', $kocist_group, '', $kocist_row['pool'] );

	foreach ( $kocist_row['subs'] as $kocist_sub => $kocist_sub_row ) {
		$kocist_category_pages[ 'kat-' . $kocist_sub ] = $kocist_category_page( 'Kategori: ' . $kocist_sub_row[0], '/kategoriler/' . $kocist_group . '/' . $kocist_sub . '/', $kocist_group, $kocist_sub, $kocist_sub_row[1], $kocist_sub_row[2] ?? array() );
	}
}

return array(
	'site_key'   => 'kocist',
	'site_label' => 'Koçist',
	// Havuz urun sayfalarinin (/urun/<urun>/) ortak metinleri bu panel sayfasinda.
	'product_page' => 'product',
	// SEO ve GEO firma bilgisi (Network Content Studio). Degerler bu sitenin
	// kendi sayfalarinda yazanlardir; panelin SEO ve GEO sekmesinden duzeltilir.
	'seo_site_defaults' => array(
		'name'        => 'Koçist Orman Ürünleri',
		'legal_name'  => 'Koçist Orman Ürünleri İnş. ve İnş. Yap. Malz. San. Tic. Ltd. Şti.',
		'description' => 'Koçist Orman Ürünleri: özel ölçüde kereste, palet, sandık ve kafes üretimi. Tedarik, üretim ve sevkiyat aynı çatı altında; İstanbul teslim.',
		'phone'       => '+90 549 648 19 19',
		'email'       => 'info@kocist.com.tr',
		'street'      => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1',
		'district'    => 'Çatalca',
		'city'        => 'İstanbul',
		'postal_code' => '', // 34494 Catalca'nin kodu degil; tek adres kurali (Toplu Guncelleme)
		'country'     => 'TR',
	),
	'pages'      => array(

		'global' => array(
			'label'      => 'Tüm Sayfalar (Üst Bilgi, Menü, Footer)',
			'path'       => '/',
			'components' => array(

				/*
				 * Ust serit: solda teslim notu + calisma saati ve banka
				 * bilgileri baglantisi, sagda telefon ve e-posta.
				 *
				 * 'hours' ayri bir alan olarak duruyor (notun icine gomulmedi)
				 * cunku shared_map uzerinden panelden dagitilan kanonik
				 * "calisma saatleri" degeri buraya yaziliyor. Ikisi ekranda
				 * ince bir ayrac ile yan yana basiliyor.
				 *
				 * Ikon alanlari kaldirildi: serit artik sade metin.
				 */
				'topbar' => array(
					'label'  => 'Üst Bilgi Şeridi',
					'fields' => array(
						'note'        => array( 'label' => 'Şerit Metni', 'type' => 'text', 'default' => 'İstanbul Teslim' ),
						'hours'       => array( 'label' => 'Çalışma Saatleri', 'type' => 'text', 'default' => 'Çalışma: 08:00-19:00' ),
						'bank_label'  => array( 'label' => 'Banka Bilgileri Metni', 'type' => 'text', 'default' => 'Banka Bilgilerimiz' ),
						'bank_url'    => array( 'label' => 'Banka Bilgileri Bağlantısı', 'type' => 'url', 'default' => '/banka-bilgilerimiz/' ),
						'phone_icon'  => array( 'label' => 'Telefon İkonu', 'type' => 'icon', 'default' => 'phone' ),
						'phone_label' => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0212 648 19 19' ),
						'phone_url'   => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481919' ),
						'mobile_label' => array( 'label' => 'Cep Telefonu Metni', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'mobile_url'  => array( 'label' => 'Cep Telefonu Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
						'email_icon'  => array( 'label' => 'E-posta İkonu', 'type' => 'icon', 'default' => 'mail' ),
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

						/*
						 * Ust seviye menu. Bir satirin acilir menusu olacaksa
						 * 'submenu' alanina alt menu bileseninin anahtari yazilir
						 * (menu_kereste, menu_hirdavat...). Bos birakilirsa oge
						 * duz bir baglanti olur.
						 *
						 * Neden ayri bilesenler: sema bir repeater'a en fazla 50
						 * satir veriyor (schema.php, NWCS_REPEATER_MAX_CAP) ve
						 * repeater icinde repeater'i reddediyor. Tum menu tek
						 * listeye sigmadigi gibi, 60 satirlik tek form panelde
						 * duzenlenemez hale gelirdi. Her acilir menu kendi karti
						 * olarak duruyor.
						 */
						'menu'       => array(
							'label'   => 'Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 12,
							'fields'  => array(
								'label'   => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'     => array( 'label' => 'Bağlantı', 'type' => 'url' ),
								'submenu' => array( 'label' => 'Alt Menü Anahtarı (boşsa açılır menü yok)', 'type' => 'text' ),
							),
							'default' => array(
								array( 'label' => 'Ana Sayfa', 'url' => '/', 'submenu' => '' ),
								array( 'label' => 'Kereste', 'url' => '/#katalog', 'submenu' => 'menu_kereste' ),
								array( 'label' => 'Ambalaj', 'url' => '/#katalog', 'submenu' => 'menu_ambalaj' ),
								array( 'label' => 'Dekorasyon', 'url' => '/#katalog', 'submenu' => 'menu_dekorasyon' ),
								array( 'label' => 'Hırdavat', 'url' => '/#katalog', 'submenu' => 'menu_hirdavat' ),
								array( 'label' => 'Kurumsal', 'url' => '/kurumsal/', 'submenu' => 'menu_kurumsal' ),
								array( 'label' => 'Katalog', 'url' => '/katalog/', 'submenu' => '' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/', 'submenu' => '' ),
							),
						),
					),
				),

				'menu_kereste' => array(
					'label'  => 'Açılır Menü: Kereste',
					'fields' => array(
						'items' => array(
							'label'   => 'Alt Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 20,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ahşap Kalas', 'url' => '#ahsap-kalas' ),
								array( 'label' => 'Ahşap Çıta', 'url' => '#ahsap-cita' ),
								array( 'label' => 'Ahşap Rabıta (Lambiri & Döşeme)', 'url' => '#ahsap-rabita' ),
								array( 'label' => 'OSB Plaka Levha', 'url' => '#osb-plaka-levha' ),
								array( 'label' => 'Kontrplak Levha', 'url' => '#kontrplak-levha' ),
								array( 'label' => 'Plywood Levha', 'url' => '#plywood-levha' ),
								array( 'label' => 'Maden Direği Kereste', 'url' => '#maden-diregi-kereste' ),
								array( 'label' => 'Ahşap Takoz', 'url' => '#ahsap-takoz' ),
								array( 'label' => 'İnşaatlık & Çatılık Kereste', 'url' => '#insaatlik-catilik-kereste' ),
							),
						),
					),
				),

				'menu_ambalaj' => array(
					'label'  => 'Açılır Menü: Ambalaj',
					'fields' => array(
						'items' => array(
							'label'   => 'Alt Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 20,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ahşap Palet (İhracat & Endüstriyel)', 'url' => '#ahsap-palet' ),
								array( 'label' => 'Ahşap Sandık & İhracat Kasası', 'url' => '#ahsap-sandik' ),
								array( 'label' => 'Ahşap Kafes & Sarma Kafes', 'url' => '#ahsap-kafes' ),
								array( 'label' => 'Triplex Bariyer & Şantiye Bariyeri', 'url' => '#triplex-bariyer' ),
								array( 'label' => 'Ahşap Cam Taşıma Sehpası', 'url' => '#cam-tasima-sehpasi' ),
							),
						),
					),
				),

				'menu_dekorasyon' => array(
					'label'  => 'Açılır Menü: Dekorasyon',
					'fields' => array(
						'items' => array(
							'label'   => 'Alt Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 24,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ahşap Ev', 'url' => '#ahsap-ev' ),
								array( 'label' => 'Kamelya', 'url' => '#kamelya' ),
								array( 'label' => 'Çardak', 'url' => '#cardak' ),
								array( 'label' => 'Ahşap Oturma Grubu - Ahşap Bank', 'url' => '#ahsap-bank' ),
								array( 'label' => 'Saksı & Sebze Yatağı', 'url' => '#saksi-sebze-yatagi' ),
								array( 'label' => 'Ahşap Salıncak', 'url' => '#ahsap-salincak' ),
								array( 'label' => 'Ahşap Pergola', 'url' => '#ahsap-pergola' ),
								array( 'label' => 'Ahşap Şezlong', 'url' => '#ahsap-sezlong' ),
								array( 'label' => 'Ahşap Tahterevalli', 'url' => '#ahsap-tahterevalli' ),
								array( 'label' => 'Ahşap Yatak', 'url' => '#ahsap-yatak' ),
								array( 'label' => 'Ahşap Ayak Altı', 'url' => '#ahsap-ayak-alti' ),
								array( 'label' => 'Hayvan Barınakları', 'url' => '#hayvan-barinaklari' ),
								array( 'label' => 'Adirondack Sandalye', 'url' => '#adirondack-sandalye' ),
								array( 'label' => 'Çelik Yapı', 'url' => '#celik-yapi' ),
							),
						),
					),
				),

				'menu_hirdavat' => array(
					'label'  => 'Açılır Menü: Hırdavat',
					'fields' => array(
						'items' => array(
							'label'   => 'Alt Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 36,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Çiviler', 'url' => '#civiler' ),
								array( 'label' => 'Zımba Telleri', 'url' => '#zimba-telleri' ),
								array( 'label' => 'Klipsler', 'url' => '#klipsler' ),
								array( 'label' => 'Bağlama Telleri & Çelik Teller', 'url' => '#baglama-telleri' ),
								array( 'label' => 'Zımba & Çivi Tabancaları', 'url' => '#civi-tabancalari' ),
								array( 'label' => 'Şartlandırıcılar', 'url' => '#sartlandiricilar' ),
								array( 'label' => 'Kompresörler', 'url' => '#kompresorler' ),
								array( 'label' => 'Somun Sökme Aletleri', 'url' => '#somun-sokme-aletleri' ),
								array( 'label' => 'Yüzey Geliştirme Diskleri', 'url' => '#yuzey-gelistirme-diskleri' ),
								array( 'label' => 'Maket Bıçağı ve Çivili Kroşe', 'url' => '#maket-bicagi' ),
								array( 'label' => 'Kulplar', 'url' => '#kulplar' ),
								array( 'label' => 'Menteşeler', 'url' => '#menteseler' ),
								array( 'label' => 'Sürgüler', 'url' => '#surguler' ),
								array( 'label' => 'Gönyeler', 'url' => '#gonyeler' ),
								array( 'label' => 'Askı & Sabitleme Elemanları', 'url' => '#aski-sabitleme' ),
								array( 'label' => 'Vida Grubu', 'url' => '#vida-grubu' ),
								array( 'label' => 'Civata Grubu', 'url' => '#civata-grubu' ),
								array( 'label' => 'Ahşap & Endüstriyel Mandallar', 'url' => '#mandallar' ),
								array( 'label' => 'Pergola Ayakları', 'url' => '#pergola-ayaklari' ),
								array( 'label' => 'Matkap & Tornavida Uçları', 'url' => '#matkap-uclari' ),
								array( 'label' => 'Pürmüzler & Gazlı Aletler', 'url' => '#purmuzler' ),
								array( 'label' => 'Shingle Çatı Kaplamaları', 'url' => '#shingle-cati' ),
								array( 'label' => 'Likit Membran ve Sürme Yalıtım', 'url' => '#likit-membran' ),
								array( 'label' => 'Su Yalıtım Membranları', 'url' => '#su-yalitim-membranlari' ),
							),
						),
					),
				),

				'menu_kurumsal' => array(
					'label'  => 'Açılır Menü: Kurumsal',
					'fields' => array(
						'items' => array(
							'label'   => 'Alt Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 12,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Hakkımızda', 'url' => '/kurumsal/' ),
								array( 'label' => 'İnsan Kaynakları', 'url' => '/insan-kaynaklari/' ),
								array( 'label' => 'Referanslar', 'url' => '/kurumsal/' ),
								array( 'label' => 'Blog – Haberler', 'url' => '/blog/' ),
							),
						),
					),
				),

				/*
				 * Sitenin her yerinde gecen kisa metinler.
				 */
				'texts' => array(
					'label'  => 'Ortak Metinler',
					'fields' => array(
						'image_soon' => array( 'label' => 'Görseli Olmayan Alandaki Yazı', 'type' => 'text', 'default' => 'Görsel yakında' ),
						'home_crumb' => array( 'label' => 'Yol Göstergesi: Ana Sayfa', 'type' => 'text', 'default' => 'Ana Sayfa' ),
					),
				),

				/*
				 * Footer ve ustundeki hareketli serit.
				 *
				 * Serit ogeleri saga dogru kesintisiz akar. Ayni liste
				 * sablonda iki kez basilir; kopya oldugu icin ekran
				 * okuyuculardan gizlenir (footer.php).
				 *
				 * Sosyal ikonlar eklentinin ikon kutuphanesinden secilir
				 * (instagram, facebook, linkedin, youtube, whatsapp);
				 * panelden keyfi SVG girilemez.
				 */
				'footer' => array(
					'label'  => 'Footer',
					'fields' => array(

						// Ustteki hareketli serit
						'ticker'          => array(
							'label'   => 'Hareketli Şerit Öğeleri',
							'type'    => 'repeater',
							'max'     => 12,
							'fields'  => array(
								'text' => array( 'label' => 'Şerit Metni', 'type' => 'text' ),
							),
							'default' => array(
								array( 'text' => 'ÇATALCA ÜRETİM TESİSİ' ),
								array( 'text' => 'İHRACATA UYGUN ISIL İŞLEM' ),
								array( 'text' => 'ÖZEL ÖLÇÜ ÜRETİM' ),
								array( 'text' => 'AYNI GÜN FİYAT' ),
								array( 'text' => 'İSTANBUL VE ÇEVRE İLLERE SEVKİYAT' ),
								array( 'text' => 'TOPTAN FİYAT GARANTİSİ' ),
							),
						),

						// Sol sutun
						'about_text'      => array( 'label' => 'Tanıtım Metni', 'type' => 'textarea', 'default' => 'Kereste, ahşap ambalaj, dekorasyon ve hırdavat gruplarında üretim ve toptan tedarik. Kendi tesisimizde üretiyor, planlı sevkiyatla teslim ediyoruz.' ),
						'social'          => array(
							'label'   => 'Sosyal Medya',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'label' => array( 'label' => 'Ad (ekran okuyucu için)', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'icon' => 'instagram', 'label' => 'Instagram', 'url' => '' ),
								array( 'icon' => 'facebook', 'label' => 'Facebook', 'url' => '' ),
								array( 'icon' => 'linkedin', 'label' => 'LinkedIn', 'url' => '' ),
								array( 'icon' => 'whatsapp', 'label' => 'WhatsApp', 'url' => 'https://wa.me/905496481919' ),
							),
						),

						// Birinci baglanti sutunu
						'links_title'     => array( 'label' => '1. Sütun Başlığı', 'type' => 'text', 'default' => 'Ürün Grupları' ),
						'links'           => array(
							'label'   => '1. Sütun Bağlantıları',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Kereste', 'url' => '/#katalog' ),
								array( 'label' => 'Ambalaj', 'url' => '/#katalog' ),
								array( 'label' => 'Ahşap Dekorasyon', 'url' => '/#katalog' ),
								array( 'label' => 'Hırdavat Grubu', 'url' => '/#katalog' ),
							),
						),

						// Ikinci baglanti sutunu
						'corporate_title' => array( 'label' => '2. Sütun Başlığı', 'type' => 'text', 'default' => 'Kurumsal' ),
						'corporate'       => array(
							'label'   => '2. Sütun Bağlantıları',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Hakkımızda', 'url' => '/kurumsal/' ),
								array( 'label' => 'İnsan Kaynakları', 'url' => '/insan-kaynaklari/' ),
								array( 'label' => 'Blog – Haberler', 'url' => '/blog/' ),
								array( 'label' => 'Belgelerimiz', 'url' => '/kurumsal/' ),
								array( 'label' => 'Referanslar', 'url' => '/kurumsal/' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),

						// Iletisim sutunu
						'contact_title'   => array( 'label' => 'İletişim Başlığı', 'type' => 'text', 'default' => 'İletişim' ),
						'address_icon'    => array( 'label' => 'Adres İkonu', 'type' => 'icon', 'default' => 'pin' ),
						'address'         => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => 'Kestanelik Mahallesi, Çatalca / İstanbul' ),
						'map_label'       => array( 'label' => 'Harita Bağlantı Metni', 'type' => 'text', 'default' => 'Haritada göster' ),
						'map_url'         => array( 'label' => 'Harita Bağlantısı', 'type' => 'url', 'default' => '/iletisim/#harita' ),
						'phone_label'     => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0212 648 19 19' ),
						'phone_url'       => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481919' ),
						'mobile_label'    => array( 'label' => 'Cep Telefonu Metni', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'mobile_url'      => array( 'label' => 'Cep Telefonu Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
						'email_label'     => array( 'label' => 'E-posta Metni', 'type' => 'text', 'default' => 'info@kocist.com.tr' ),
						'email_url'       => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@kocist.com.tr' ),

						// Alt serit
						'copyright'       => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => '© 2026 Koçist Orman Ürünleri — yerel demo kurulumu' ),
						'legal'           => array(
							'label'   => 'Alt Şerit Bağlantıları',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Gizlilik Politikası', 'url' => '#gizlilik' ),
								array( 'label' => 'KVKK Metni', 'url' => '#kvkk' ),
								array( 'label' => 'Çerez Politikası', 'url' => '#cerez' ),
							),
						),
						'demo_note'       => array( 'label' => 'Demo Uyarısı', 'type' => 'text', 'default' => 'Bu bir demo kurulumudur.' ),
					),
				),
			),
		),

		'home' => array(
			'label'             => 'Ana Sayfa',
			'path'              => '/',
			'sortable_sections' => array( 'catalog', 'latest', 'process', 'products', 'blog' ),
			'components'        => array(

				/*
				 * Hero iki kartlidir: solda buyuk gorsel karti, sagda ok
				 * tuslariyla ilerleyen slayt. Referans tuin.co.uk ana sayfasi.
				 *
				 * Sol kart temiz bir atolye fotografi tasir (assets/img/atolye.jpg):
				 * kadrajin ortasi bos acik ahsap, yazi oraya oturuyor. Zemin acik
				 * oldugu icin metin beyaz degil koyu yesil; okunaklilik boyle
				 * saglaniyor, uzerine koyu perde atmaya gerek kalmiyor.
				 *
				 * Onemli: buraya ustunde hazir yazi/buton bulunan bir banner
				 * gorseli konursa metin katmani ikili gorunume yol acar.
				 * Bu alan temiz fotograf bekler.
				 *
				 * Slaytlar ve guven seridi ayri repeater'lar; ayni bilesende
				 * yan yana durmalari sorun degil, yasak olan repeater ICINDE
				 * repeater (schema.php).
				 */
				'hero' => array(
					'label'  => 'Hero (Giriş Bölümü)',
					'fields' => array(

						// Sol buyuk kart: atolye fotografi + logo ve slogan.
						// Logo ayri bir alan degil; global.header.logo_image okunuyor
						// ki marka gorseli tek yerden yonetilsin.
						'image'     => array( 'label' => 'Büyük Kart Görseli', 'type' => 'image', 'default' => 0 ),
						'title'     => array( 'label' => 'Büyük Kart Başlığı', 'type' => 'text', 'default' => 'Ahşaptan ilham aldık' ),
						'cta_label' => array( 'label' => 'Büyük Kart Buton Metni', 'type' => 'text', 'default' => 'Ürün Gruplarımız' ),
						'cta_url'   => array( 'label' => 'Büyük Kart Buton Bağlantısı', 'type' => 'url', 'default' => '#katalog' ),

						// Sag kart: slaytlar
						'slides'    => array(
							'label'   => 'Sağ Kart Slaytları',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'image'     => array( 'label' => 'Slayt Görseli', 'type' => 'image' ),
								'title'     => array( 'label' => 'Slayt Başlığı', 'type' => 'text' ),
								'text'      => array( 'label' => 'Slayt Metni', 'type' => 'textarea' ),
								'cta_label' => array( 'label' => 'Slayt Buton Metni', 'type' => 'text' ),
								'cta_url'   => array( 'label' => 'Slayt Buton Bağlantısı', 'type' => 'url' ),
							),
							'default' => array(
								array(
									'image'     => 0,
									'title'     => 'Bahçe ve Dekorasyon Ürünleri',
									'text'      => 'Kamelya, pergola, salıncak ve bank gruplarında kendi üretimimiz; montaja hazır teslim.',
									'cta_label' => 'Dekorasyon Ürünlerini Gör',
									'cta_url'   => '/#katalog',
								),
								array(
									'image'     => 0,
									'title'     => 'Plywood ve Kontrplak Levha',
									'text'      => 'Huş ve çam plywood, kontrplak ve OSB levha gruplarında stoklu çalışıyoruz.',
									'cta_label' => 'Levha Grubunu Gör',
									'cta_url'   => '/#katalog',
								),
								array(
									'image'     => 0,
									'title'     => 'Ahşap Salıncak ve Oyun Grupları',
									'text'      => 'Emprenyeli ahşaptan, dış mekâna dayanıklı çocuk oyun ve bahçe grupları.',
									'cta_label' => 'Ürünleri İnceleyin',
									'cta_url'   => '/#katalog',
								),
							),
						),

						// Hero altindaki guven seridi
						'trust'     => array(
							'label'   => 'Güven Şeridi',
							'type'    => 'repeater',
							'max'     => 5,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'label' => array( 'label' => 'Metin', 'type' => 'text' ),
							),
							'default' => array(
								array( 'icon' => 'truck', 'label' => 'İstanbul ve çevre illere planlı sevkiyat' ),
								array( 'icon' => 'factory', 'label' => 'Çatalca tesisinde kendi üretimimiz' ),
								array( 'icon' => 'shield', 'label' => 'İhracata uygun ısıl işlem' ),
								array( 'icon' => 'check', 'label' => 'Toptan fiyat garantisi' ),
							),
						),
					),
				),

				/*
				 * Ana urun gamlari: dort kart yan yana. Her kart tam kaplama
				 * gorsel + ustune ortalanmis baslik, alt baslik ve Kesfet
				 * butonu.
				 *
				 * Not: musterinin kategori gorsellerinin bir kismi hazir
				 * banner; kosede kendi etiketini tasiyor. Uzerine bizim
				 * basligimiz da bindiginde kosede ikinci bir etiket gorunur.
				 * Bu bilincli bir tercih (gercek sitedeki durumla ayni);
				 * temiz foto geldiginde tek yapilacak sey gorseli degistirmek.
				 */
				'catalog' => array(
					'label'  => 'Ürün Grupları',
					'fields' => array(
						'title'    => array( 'label' => 'Bölüm Etiketi', 'type' => 'text', 'default' => 'Ürün Grupları' ),
						'items'    => array(
							'label'   => 'Grup Kartları',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'image'      => array( 'label' => 'Görsel', 'type' => 'image' ),
								'title'      => array( 'label' => 'Başlık', 'type' => 'text' ),
								'link_label' => array( 'label' => 'Buton Metni', 'type' => 'text' ),
								'link_url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'image' => 0, 'title' => 'Kereste', 'link_label' => 'Keşfet', 'link_url' => '/#katalog' ),
								array( 'image' => 0, 'title' => 'Ambalaj', 'link_label' => 'Keşfet', 'link_url' => '/#katalog' ),
								array( 'image' => 0, 'title' => 'Ahşap Dekorasyon', 'link_label' => 'Keşfet', 'link_url' => '/#katalog' ),
								array( 'image' => 0, 'title' => 'Hırdavat Grubu', 'link_label' => 'Keşfet', 'link_url' => '/#katalog' ),
							),
						),
					),
				),

				/*
				 * Son eklenen urunler: tek satir, saga dogru kayan serit.
				 * Kartlar Urun Havuzu'ndan gelir (bu siteye secilen urunlerden
				 * havuza en son eklenenler); burada yalnizca metinler ve adet.
				 */
				'latest' => array(
					'label'  => 'Son Eklenen Ürünler',
					'fields' => array(
						'title'      => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Son eklenen ürünler' ),
						'subtitle'   => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Kataloğa yeni giren ürünler. Ölçü ve adet için teklif isteyin.' ),
						'link_label' => array( 'label' => 'Kart Bağlantı Metni', 'type' => 'text', 'default' => 'Teklif alın' ),
						'count'      => array( 'label' => 'Gösterilecek Ürün Sayısı', 'type' => 'text', 'default' => '8', 'hint' => 'Ürünler Ürün Havuzu’ndan gelir: bu siteye seçilenlerden havuza en son eklenenler.' ),
					),
				),

				/*
				 * Siparis sureci: uc adim. Her adimin cizimi sabittir (olcu, teklif,
				 * sevkiyat); panelden yalnizca metinler degisir.
				 */
				'process' => array(
					'label'  => 'Sipariş Süreci',
					'fields' => array(
						'title'     => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Siparişiniz nasıl ilerler' ),
						'subtitle'  => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Ölçüyü siz verin; üretimi ve sevkiyatı biz planlayalım.' ),
						'items'     => array(
							'label'   => 'Adımlar',
							'type'    => 'repeater',
							'max'     => 3,
							'fields'  => array(
								'title' => array( 'label' => 'Adım Başlığı', 'type' => 'text' ),
								'text'  => array( 'label' => 'Adım Metni', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'title' => 'Ölçü ve adedi iletin', 'text' => 'Ürünü, ölçüyü, adedi ve teslim adresini telefonla, e-postayla ya da iletişim formundan gönderin.' ),
								array( 'title' => 'Yazılı teklifinizi alın', 'text' => 'Fiyatı ve teslim tarihini yazılı olarak iletiyoruz. Onayınızla sipariş üretime girer.' ),
								array( 'title' => 'Üretim ve sevkiyat', 'text' => 'Siparişiniz Çatalca tesisinde hazırlanır, hafta içi planlı sevkiyatla İstanbul ve çevre illere teslim edilir.' ),
							),
						),
						'cta_label' => array( 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'cta_url'   => array( 'label' => 'Buton Bağlantısı', 'type' => 'url', 'default' => '/iletisim/' ),
					),
				),

				/*
				 * Merkezi urun havuzundan gelen urunler. Kategori kartlarindan
				 * (catalog) ayri bir bolum: orada elle girilen dort grup var,
				 * burada panelden bu site icin secilen tekil urunler.
				 * Fiyat bos ise kartta "Teklif al" gorunur.
				 */
				'products' => array(
					'label'  => 'Ürünler (Havuzdan)',
					'fields' => array(
						'title'     => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Öne Çıkan Ürünler' ),
						'subtitle'  => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Stoktan hızlı teslim edilen ürünlerimizden bir seçki.' ),
						'pool'      => array(
							'label' => 'Ürünler (merkezî havuzdan)',
							'type'  => 'products',
						),
						'cta_label'    => array( 'label' => 'Kart Bağlantı Metni (detay sayfası yoksa)', 'type' => 'text', 'default' => 'Teklif Al' ),
						'detail_label' => array( 'label' => 'Kart Bağlantı Metni (detay sayfası varsa)', 'type' => 'text', 'default' => 'Ürün detayı' ),
						'count'        => array( 'label' => 'Gösterilecek Ürün Sayısı', 'type' => 'text', 'default' => '8', 'hint' => 'Yukarıdaki listenin ilk ürünleri gösterilir; sırayı oklarla değiştirin.' ),
						'all_label'    => array( 'label' => 'Tüm Ürünler Buton Metni', 'type' => 'text', 'default' => 'Tüm ürünler' ),
						'empty_text'   => array( 'label' => 'Ürün Seçilmemişse (yalnızca panel önizlemesinde)', 'type' => 'text', 'default' => 'Bu site için henüz ürün seçilmedi.' ),
					),
				),

				/*
				 * Son uc blog yazisi. Yazi yoksa bolum hic basilmaz.
				 */
				'blog' => array(
					'label'  => 'Blog – Son Yazılar',
					'fields' => array(
						'title'      => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Blog – Haberler' ),
						'subtitle'   => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Koçist’ten haberler; orman ürünlerinde güncel trendler ve bilgilendirmeler.' ),
						'link_label' => array( 'label' => 'Tüm Yazılar Bağlantı Metni', 'type' => 'text', 'default' => 'Tüm yazılar' ),
					),
				),
			),
		),

		/*
		 * Urun kategorileri: /kategoriler/ ve tum grup/kategori sayfalarinda
		 * ortak metinler. {suslu parantez} icindeki kelimeler sayfada
		 * gercek degerle degisir (grup adi, urun sayisi...).
		 */
		'kategoriler' => array(
			'label'      => 'Kategoriler',
			'path'       => '/kategoriler/',
			'components' => array(
				'index' => array(
					'label'  => 'Kategoriler Sayfası',
					'fields' => array(
						'crumb' => array( 'label' => 'Yol Göstergesindeki Adı', 'type' => 'text', 'default' => 'Kategoriler' ),
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ürün kategorileri' ),
						'lead'  => array( 'label' => 'Alt Metin — {grup} grup sayısı, {kategori} kategori sayısı olur', 'type' => 'textarea', 'default' => '{grup} ürün grubunda {kategori} kategori. Aradığınızı listede görmüyorsanız ölçüsünü yazın, fiyatlandıralım.' ),
					),
				),
				'texts' => array(
					'label'  => 'Grup ve Kategori Sayfaları (ortak)',
					'fields' => array(
						'lead_products'      => array( 'label' => 'Alt Metin: ürün varsa — {urun} ürün sayısı', 'type' => 'textarea', 'default' => '{urun} ürün listeleniyor. Fiyatlar ölçü ve adede göre değişir; aynı gün teklif veriyoruz.' ),
						'lead_sub_empty'     => array( 'label' => 'Alt Metin: ürünsüz kategori — {grup} grup adı', 'type' => 'textarea', 'default' => '{grup} grubunda, ölçü ve adede göre üretim ve tedarik.' ),
						'lead_group_empty'   => array( 'label' => 'Alt Metin: ürünsüz grup — {kategori} kategori sayısı', 'type' => 'textarea', 'default' => '{kategori} kategoride ölçü ve adede göre üretim ve tedarik.' ),
						'group_all'          => array( 'label' => 'Grubun Tümü Bağlantısı — {grup} grup adı', 'type' => 'text', 'default' => 'Tüm {grup} ürünleri' ),
						'meta_subs'          => array( 'label' => 'Kategori Sayısı — {kategori}', 'type' => 'text', 'default' => '{kategori} kategori' ),
						'meta_subs_products' => array( 'label' => 'Kategori ve Ürün Sayısı — {kategori}, {urun}', 'type' => 'text', 'default' => '{kategori} kategori, {urun} ürün' ),
						'category_label'     => array( 'label' => 'Kategori Listesi Başlığı (telefonda)', 'type' => 'text', 'default' => 'Kategori' ),
						'all_label'          => array( 'label' => 'Listede "Tümü"', 'type' => 'text', 'default' => 'Tümü' ),
						'others_title'       => array( 'label' => 'Diğer Gruplar Başlığı', 'type' => 'text', 'default' => 'Diğer ürün grupları' ),
						'price_quote'        => array( 'label' => 'Fiyatı Olmayan Ürün Kartında', 'type' => 'text', 'default' => 'Fiyat teklifle' ),
						'go_detail'          => array( 'label' => 'Kart Bağlantısı (detay sayfası varsa)', 'type' => 'text', 'default' => 'İncele' ),
						'go_quote'           => array( 'label' => 'Kart Bağlantısı (detay sayfası yoksa)', 'type' => 'text', 'default' => 'Teklif iste' ),
						'empty_title'        => array( 'label' => 'Ürün Yoksa Başlık — {ad} sayfa adı', 'type' => 'text', 'default' => '{ad} için listelenmiş ürün yok' ),
						'empty_text'         => array( 'label' => 'Ürün Yoksa Metin', 'type' => 'textarea', 'default' => 'Bu kategorideki ürünleri siparişe göre hazırlıyoruz. Ölçü ve adedi yazın, aynı gün fiyat verelim.' ),
						'empty_cta'          => array( 'label' => 'Ürün Yoksa Buton', 'type' => 'text', 'default' => 'Teklif isteyin' ),
					),
				),
			),
		),

		/*
		 * Katalog (/katalog/): belgeler ve sertifikalar. Iki sekme: sertifika
		 * gorselleri (tiklayinca buyur) ve indirilebilir belgeler.
		 *
		 * Varsayilan dosyalar temada (assets/katalog/); '/tema/' ile baslayan
		 * baglanti o klasore cozulur. Yeni belge: dosyayi Ortam Kutuphanesi'ne
		 * yukleyip adresini Baglanti alanina yapistirin.
		 */
		'katalog' => array(
			'label'      => 'Katalog',
			'path'       => '/katalog/',
			'components' => array(

				'page_head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'breadcrumb' => array( 'label' => 'Yol Göstergesi', 'type' => 'text', 'default' => 'Ana Sayfa / Katalog' ),
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Belgeler ve Sertifikalar' ),
						'subtitle'   => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'ISPM-15 ve HT yetki belgelerimiz, ısıl işlem tesisimiz ve kurumsal dokümanlarımız.' ),
					),
				),

				'certificates' => array(
					'label'  => 'Sertifikalar',
					'fields' => array(
						'tab_label'  => array( 'label' => 'Sekme Adı', 'type' => 'text', 'default' => 'Sertifikalar' ),
						'empty_text' => array( 'label' => 'Görsel Yoksa Gösterilecek Metin', 'type' => 'text', 'default' => 'Henüz sertifika eklenmedi.' ),
						'items'     => array(
							'label'   => 'Görseller',
							'type'    => 'repeater',
							'max'     => 30,
							'fields'  => array(
								'image' => array( 'label' => 'Görsel', 'type' => 'image' ),
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
							),
							'default' => array(
								array( 'image' => 0, 'title' => 'Ahşap Ambalaj Malzemesi İşaretleme İzin Belgesi' ),
								array( 'image' => 0, 'title' => 'Isıl İşlem Operatör Belgesi' ),
								array( 'image' => 0, 'title' => 'ISPM-15 ısıl işlem fırını' ),
								array( 'image' => 0, 'title' => 'ISPM-15 ısıl işlem fırını, kapalı' ),
								array( 'image' => 0, 'title' => 'HT damgalı kereste' ),
								array( 'image' => 0, 'title' => 'HT damgalı palet takozu' ),
								array( 'image' => 0, 'title' => 'Sandık üretim atölyesi' ),
								array( 'image' => 0, 'title' => 'HT damgalı kereste istifi' ),
								array( 'image' => 0, 'title' => 'Koçist damgalı kereste paketi' ),
								array( 'image' => 0, 'title' => 'TR-1080 HT damgası' ),
							),
						),
					),
				),

				'documents' => array(
					'label'  => 'Belgeler',
					'fields' => array(
						'tab_label'  => array( 'label' => 'Sekme Adı', 'type' => 'text', 'default' => 'Belgeler' ),
						'open_label' => array( 'label' => 'Aç Bağlantısı Metni', 'type' => 'text', 'default' => 'Aç' ),
						'file_label' => array( 'label' => 'Uzantısı Bilinmeyen Dosya Etiketi', 'type' => 'text', 'default' => 'DOSYA' ),
						'empty_text' => array( 'label' => 'Belge Yoksa Gösterilecek Metin', 'type' => 'text', 'default' => 'Henüz belge eklenmedi.' ),
						'items'     => array(
							'label'   => 'Belgeler',
							'type'    => 'repeater',
							'max'     => 30,
							'fields'  => array(
								'title' => array( 'label' => 'Belge Adı', 'type' => 'text' ),
								'url'   => array( 'label' => 'Dosya Bağlantısı (PDF, JPG…)', 'type' => 'url' ),
							),
							'default' => array(
								array( 'title' => 'Ahşap Ambalaj Malzemesi İşaretleme İzin Belgesi', 'url' => '/tema/isaretleme-izin-belgesi.jpg' ),
								array( 'title' => 'Kapasite Raporu', 'url' => '/tema/belgeler/kapasite-raporu.pdf' ),
								array( 'title' => 'Sanayi Sicil Belgesi', 'url' => '/tema/belgeler/sanayi-sicil-belgesi.pdf' ),
								array( 'title' => 'Vida, Somun ve Bits Uç Listesi 2025', 'url' => '/tema/belgeler/vida-somun-bits-uc-listesi-2025.pdf' ),
							),
						),
					),
				),
			),
		),

		/*
		 * Banka Bilgilerimiz (/banka-bilgilerimiz/): havale ve EFT hesaplari.
		 * Ust seritteki "Banka Bilgilerimiz" baglantisi buraya gelir.
		 *
		 * Hesaplar panelden girilir; varsayilan liste bos, cunku firmanin
		 * gercek hesap bilgisi henuz yok. Liste bosken sayfa, ziyaretciyi
		 * hesap bilgisini telefonla ya da WhatsApp'tan istemeye yonlendirir.
		 */
		'banka' => array(
			'label'      => 'Banka Bilgilerimiz',
			'path'       => '/banka-bilgilerimiz/',
			'components' => array(

				'page_head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'breadcrumb' => array( 'label' => 'Yol Göstergesi', 'type' => 'text', 'default' => 'Ana Sayfa / Banka Bilgilerimiz' ),
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Banka Bilgilerimiz' ),
						'subtitle'   => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Havale ve EFT ile ödeme yapabileceğiniz hesaplarımız.' ),
					),
				),

				'accounts' => array(
					'label'  => 'Hesaplar',
					'fields' => array(
						'items' => array(
							'label'   => 'Banka Hesapları',
							'type'    => 'repeater',
							'max'     => 12,
							'fields'  => array(
								'bank'     => array( 'label' => 'Banka Adı', 'type' => 'text' ),
								'holder'   => array( 'label' => 'Hesap Sahibi (Unvan)', 'type' => 'text' ),
								'branch'   => array( 'label' => 'Şube (adı ve kodu)', 'type' => 'text' ),
								'account'  => array( 'label' => 'Hesap No', 'type' => 'text' ),
								'currency' => array( 'label' => 'Para Birimi (TL, USD, EUR)', 'type' => 'text' ),
								'iban'     => array( 'label' => 'IBAN', 'type' => 'text' ),
							),
							'default' => array(),
						),
						'note'  => array( 'label' => 'Ödeme Notu', 'type' => 'textarea', 'default' => 'Havale ve EFT açıklamasına firma adınızı ya da teklif numaranızı yazın. Ödeme yapmadan önce IBAN\'ı telefonla bizden doğrulayın; hesap değişikliğini e-posta ile bildirmeyiz.' ),
					),
				),

				'empty' => array(
					'label'  => 'Hesap Girilmemişken',
					'fields' => array(
						'title'    => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Hesap bilgilerini bizden isteyin' ),
						'text'     => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Banka ve IBAN bilgilerimizi telefonla ya da WhatsApp\'tan hemen iletiyoruz.' ),
						'wa_label' => array( 'label' => 'WhatsApp Düğmesi', 'type' => 'text', 'default' => 'WhatsApp\'tan isteyin' ),
						'wa_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905496481919?text=Merhaba%2C%20banka%20hesap%20bilgilerinizi%20alabilir%20miyim%3F' ),
					),
				),
			),
		),

		/*
		 * Kurumsal sayfasi (/kurumsal/). Metinler firmanin kendi kurumsal
		 * metnidir; yazim hatalari duzeltildi, anlam korundu.
		 *
		 * Eski 'story' ve 'values' bilesenleri yerine yeni anahtarlar
		 * kullaniliyor: panel deposunda eski demo metinleri kayitli olan
		 * sitelerde de yeni icerik varsayilandan gorunsun diye.
		 */
		'inner' => array(
			'label'      => 'Kurumsal',
			'path'       => '/kurumsal/',
			'components' => array(

				'page_head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'breadcrumb' => array( 'label' => 'Yol Göstergesi', 'type' => 'text', 'default' => 'Ana Sayfa / Kurumsal' ),
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Kurumsal' ),
						'subtitle'   => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Özel ölçüde kereste, palet, sandık ve kafes üretiminde kalite, dürüstlük ve hız.' ),
					),
				),

				'profile' => array(
					'label'  => 'Profilimiz',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Profilimiz' ),
						'body'  => array(
							'label'   => 'Metin (paragraflar arasında boş satır bırakın)',
							'type'    => 'textarea',
							'default' => "Firmamız, yeni bir ürün için yaptıracağınız veya mevcut kullanımınızın yerini alacak her ölçüde kereste, palet, sandık ve kafesi sizin çıkarlarınız doğrultusunda en uygun şekilde dizayn eder ve size sunar; ya da mevcut kullandığınız palet, sandık ve kafes ölçülerini, şartnamelerini veya proje çizimlerini alarak istekleriniz doğrultusunda en hızlı şekilde üretime geçer. Tüm palet, sandık ve kafes elemanlarını içeren demonte paketleri hazırlayarak size sevk eder.\n\nKaliteli, dürüst ve hızlı bir pazarlama ilkesi benimsemiş olan firmamız, müşterilerini memnun etmek amacıyla özel boy ve ebatta kereste, palet, sandık, kafes vb. ahşap ambalaj malzemeleri üretmektedir. Bu amaçla yola çıkan firmamız, kalitesinden ve dürüstlüğünden taviz vermeden hizmetlerine devam etmektedir.",
						),
						'image' => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
					),
				),

				'aim' => array(
					'label'  => 'Amacımız',
					'fields' => array(
						'title'     => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Amacımız' ),
						'statement' => array( 'label' => 'Ana Cümle', 'type' => 'textarea', 'default' => 'En kaliteli malzemeyi en kısa zamanda müşterimizin hizmetine sunmak.' ),
						'body'      => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Bu bağlamda firmamızın çalışmaları, daha kaliteli ve daha hesaplı malzemeyi tüketicinin hizmetine sunma ilkesiyle devam etmektedir.' ),
					),
				),

				'quality' => array(
					'label'  => 'Kalite Politikamız',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Kalite Politikamız' ),
						'intro' => array( 'label' => 'Giriş', 'type' => 'textarea', 'default' => 'Aşağıdaki ilkeleri kalite politikamız olarak belirledik ve hedefledik.' ),
						'items' => array(
							'label'   => 'İlkeler',
							'type'    => 'repeater',
							'max'     => 10,
							'fields'  => array(
								'text' => array( 'label' => 'İlke', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'text' => 'Ürünlerimizi zamanında teslim ederek, uygun fiyat avantajı sağlayarak ve kaliteli ürün ve ekipmanlarımızı sunarak müşteri ihtiyaç ve beklentilerini karşılamak, sürekli gelişmelerini sağlamak.' ),
								array( 'text' => 'Kalite bilincini yerleştirerek, çalışanlarımızın sağlığı ve çevrenin korunması için maddi ve insan kaynaklarımızı seferber etmek ve tüm çalışanların gelişmelerini sağlamak.' ),
								array( 'text' => 'Kuruluş olarak her alanda sürekli iyileşme ve gelişme sağlamak.' ),
								array( 'text' => 'Yaptığımız işi ilk seferinde ve her seferinde doğru yapmak.' ),
								array( 'text' => 'Müşteri istek ve görüşlerini esas almak; işimizi standartlara ve kalite yönetim sistemimize uygun olarak yapmak.' ),
								array( 'text' => 'Kalite bilincinin artması için tedarikçilerimiz ile birlikte koordineli çalışma içinde bulunmak.' ),
							),
						),
					),
				),

				'group' => array(
					'label'  => 'Koçist Grup',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Koçist Grup' ),
						'body'  => array(
							'label'   => 'Metin (paragraflar arasında boş satır bırakın)',
							'type'    => 'textarea',
							'default' => "Koçist Grup, orman ürünleri alanındaki yatırımı ile 37 yıldır; yapı ve inşaat sektörünün malzeme tedarikçisi olarak 2 yıldır hizmet vermektedir. 37 yıllık tecrübe ile Koçist Grup; doğal ortamları daha güzel ve yaşanabilir ortamlara dönüştürme becerisine sahip olan, insan eliyle oluşan yapılar inşa etmeye karar vermiştir.\n\nYapı ve inşaat sektörü; zaman içinde gelişim göstererek, ilkel yaşam alanlarından akıllı binalara, tozlu patikalardan peyzaj ve çevre düzenlemesi yapılmış huzurlu yaşam alanlarına dönüşerek çeşitli teknik ve estetik evrimlere uğramıştır. Dolayısıyla sektör temsilcileri, insanlık gelişim tarihini ve kültür evrimini günümüze taşıyan ve geleceği inşa eden yatırımcılardır.\n\nKoçist Yapı İnşaat, sizleri mutlu edecek yaşam ve çalışma alanları inşa etmekte ve sizler için geleceği inşa eden bir yatırımcı olmayı hedeflemektedir. Koçist Grup, 2016 yılından itibaren Koçist Teknoloji markası ile bilişim alanında da yatırım yapmıştır.",
						),
						'facts' => array(
							'label'   => 'Öne Çıkan Bilgiler',
							'type'    => 'repeater',
							'max'     => 4,
							'fields'  => array(
								'value' => array( 'label' => 'Değer', 'type' => 'text' ),
								'label' => array( 'label' => 'Açıklama', 'type' => 'text' ),
							),
							'default' => array(
								array( 'value' => '37 yıl', 'label' => 'orman ürünleri alanında' ),
								array( 'value' => '2 yıl', 'label' => 'yapı ve inşaat malzemesi tedarikinde' ),
								array( 'value' => '2016', 'label' => 'Koçist Teknoloji ile bilişim yatırımı' ),
							),
						),
						'image' => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
					),
				),
			),
		),

		/*
		 * Insan Kaynaklari (/insan-kaynaklari/). Ise alim gercek bir sirali
		 * surec oldugu icin adimlar numarali basilir; politika ilkeleri sira
		 * bildirmedigi icin numarasiz liste.
		 */
		'hr' => array(
			'label'      => 'İnsan Kaynakları',
			'path'       => '/insan-kaynaklari/',
			'components' => array(

				'page_head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'breadcrumb' => array( 'label' => 'Yol Göstergesi', 'type' => 'text', 'default' => 'Ana Sayfa / Kurumsal / İnsan Kaynakları' ),
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İnsan Kaynakları' ),
						'subtitle'   => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Başarımıza çalışanlarımızın gücü ve desteğiyle ulaştık; en önemli değerimiz insan kaynağımız.' ),
					),
				),

				'policy' => array(
					'label'  => 'İnsan Kaynakları Politikamız',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İnsan Kaynakları Politikamız' ),
						'intro' => array( 'label' => 'Giriş', 'type' => 'textarea', 'default' => 'Sahip olduğu başarıya çalışanlarının gücü ve desteği ile ulaşan ve en önemli değerinin insan kaynağı olduğuna inanan Koçist Grup, aşağıdaki ilkeleri insan kaynakları politikası olarak benimsemiştir.' ),
						'items' => array(
							'label'   => 'İlkeler',
							'type'    => 'repeater',
							'max'     => 10,
							'fields'  => array(
								'text' => array( 'label' => 'İlke', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'text' => 'Görevin gerektirdiği bilgi ve beceriye sahip kaliteli insan gücünü bünyesine kazandırmak.' ),
								array( 'text' => 'Çalışanlarının yaratıcılıklarını kullanabilecekleri ve fikirlerini dile getirebilecekleri etkin bir iletişim ve motivasyon ortamı sağlamak.' ),
								array( 'text' => 'Farklı bakış açıları ve bilgi birikimlerini bir arada barındıran katılımcı yönetim politikası izlemek.' ),
								array( 'text' => 'Çalışanlarının kişisel ve mesleki gelişimlerini ön planda tutarak sürekli öğrenme ve gelişimi desteklemek.' ),
								array( 'text' => 'Çalışan performanslarını objektif kriterlerle değerlendirerek yüksek performansı ödüllendirmek ve teşvik etmek.' ),
								array( 'text' => 'Yenilikçi insan kaynakları uygulamalarını hayata geçirerek çalışanlarına daima en iyiyi sunmak.' ),
							),
						),
					),
				),

				'hiring' => array(
					'label'  => 'İşe Alım Süreci',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'İşe Alım Süreci' ),
						'intro' => array( 'label' => 'Giriş', 'type' => 'textarea', 'default' => 'İşe alım sürecimizin amacı, doğru adayı uygun pozisyonla eşleştirmektir.' ),
						'steps' => array(
							'label'   => 'Adımlar (sırayla)',
							'type'    => 'repeater',
							'max'     => 10,
							'fields'  => array(
								'title' => array( 'label' => 'Adım', 'type' => 'text' ),
								'text'  => array( 'label' => 'Açıklama', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'title' => 'Başvuru', 'text' => 'Açık pozisyonlar için Koçist Grup’un ihtiyaç ve istihdam çalışmaları ile bireysel başvurular değerlendirilir.' ),
								array( 'title' => 'Ön görüşme', 'text' => 'Uygun adaylar İnsan Kaynakları Departmanı ile ön görüşmeye davet edilir.' ),
								array( 'title' => 'Testler', 'text' => 'Ön görüşmede uygun görülen adaylara, pozisyonun gerekliliğine göre dikkat testi, genel yetenek testi, yabancı dil testi, kişilik envanteri ve vaka çalışmaları uygulanır.' ),
								array( 'title' => 'Yönetici görüşmesi', 'text' => 'Kısa listeye kalan adaylar ilgili bölüm yöneticileri ile görüşür.' ),
								array( 'title' => 'Referans ve teklif', 'text' => 'Bölüm yöneticisi tarafından onaylanan adaylara, referans ve ücret çalışmaları sonrasında teklif sunulur.' ),
								array( 'title' => 'İşe başlama ve oryantasyon', 'text' => 'Teklifi kabul eden aday işe başlar; oryantasyon ve İSG eğitimleri ile göreve adaptasyonu sağlanır.' ),
							),
						),
					),
				),

				'performance' => array(
					'label'  => 'Performans Değerlendirme',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Performans Değerlendirme' ),
						'body'  => array(
							'label'   => 'Metin (paragraflar arasında boş satır bırakın)',
							'type'    => 'textarea',
							'default' => "Koçist Grup, hedeflerle yönetim prensipleri doğrultusunda çalışanlarının performanslarını ölçer. Çalışanların performansı, hedeflerin yanı sıra yetkinliklerin de kullanıldığı bir sistem ile ölçülür.\n\nKoçist Grup yetkinlikleri, tüm grup şirketlerinin yöneticileri ile düzenlenen çalıştaylarda belirlenmiş olup, yetkinlikler Koçist Grup’un işlerinde başarıyı getiren davranış örnekleri üzerine belirlenmiştir. Genel esasları Koçist Grup tarafından belirlenmiş olan Performans Yönetim Sistemi, şirket bazında uygulamada farklı ihtiyaçlara cevap verecek esnekliğe sahiptir.\n\nPerformans Değerlendirme Sistemi, ara dönem ve yıl sonu olmak üzere 2 dönemde değerlendirilir. Performans Yönetim Sistemi; Eğitim ve Gelişim, Kariyer Yönetimi, Ödül Yönetimi ve Potansiyel Değerlendirme süreçlerine girdi sağlar.",
						),
					),
				),

				'apply' => array(
					'label'  => 'Başvuru Çağrısı',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ekibimize katılmak ister misiniz?' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Özgeçmişinizi ve ilgilendiğiniz pozisyonu e-posta ile gönderin; İnsan Kaynakları Departmanımız başvurunuzu değerlendirip size dönüş yapar.' ),
						'button_label' => array( 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'Özgeçmiş gönderin' ),
						'button_url'   => array( 'label' => 'Buton Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@kocist.com.tr?subject=%C4%B0%C5%9F%20ba%C5%9Fvurusu' ),
						'email'        => array( 'label' => 'Görünen E-posta', 'type' => 'text', 'default' => 'info@kocist.com.tr' ),
					),
				),
			),
		),

		/*
		 * Blog (/blog/, kategori arsivleri ve tekil yazi). Yazilarin kendisi
		 * WordPress yazisidir (tema ilk kurulumda bir kez olusturur); burada
		 * yalnizca sayfa metinleri duruyor.
		 */
		'blog' => array(
			'label'      => 'Blog',
			'path'       => '/blog/',
			'components' => array(

				'page_head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'breadcrumb' => array( 'label' => 'Yol Göstergesi', 'type' => 'text', 'default' => 'Ana Sayfa / Blog' ),
						'title'      => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Blog – Haberler' ),
						'subtitle'   => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Koçist’ten haberler; orman ürünlerinde güncel trendler ve bilgilendirmeler.' ),
					),
				),

				'listing' => array(
					'label'  => 'Yazı Listesi',
					'fields' => array(
						'all_label'  => array( 'label' => 'Filtre: Tümü Metni', 'type' => 'text', 'default' => 'Tüm yazılar' ),
						'filter_label' => array( 'label' => 'Filtre Başlığı (ekran okuyucu için)', 'type' => 'text', 'default' => 'Kategoriye göre süz' ),
						'read_label' => array( 'label' => 'Kart Bağlantı Metni', 'type' => 'text', 'default' => 'Yazıyı okuyun' ),
						'empty_text' => array( 'label' => 'Yazı Yoksa Gösterilecek Metin', 'type' => 'text', 'default' => 'Bu kategoride henüz yazı yok.' ),
						'prev_label' => array( 'label' => 'Sayfalama: Önceki', 'type' => 'text', 'default' => 'Önceki' ),
						'next_label' => array( 'label' => 'Sayfalama: Sonraki', 'type' => 'text', 'default' => 'Sonraki' ),
					),
				),

				'single' => array(
					'label'  => 'Yazı Sayfası',
					'fields' => array(
						'back_label'    => array( 'label' => 'Geri Bağlantı Metni', 'type' => 'text', 'default' => 'Tüm yazılar' ),
						'related_title' => array( 'label' => 'İlgili Yazılar Başlığı', 'type' => 'text', 'default' => 'İlgili yazılar' ),
					),
				),
			),
		),

		/*
		 * Urun sayfasi. Solda galeri, sagda urun bilgileri; altinda teknik
		 * ozellik tablosu ve sikca sorulan sorular.
		 *
		 * Demo kapsaminda tek urun var (ahsap tavuk kumesi). Gercek bir
		 * katalogda bunun yerine bir icerik turu (CPT) gelir; burada amac
		 * sayfa tasariminin panelden yonetilebildigini gostermek.
		 *
		 * Fiyat ve sepet alani bilincli olarak yok: bu bir e-ticaret degil,
		 * teklif toplayan bir kurumsal site.
		 */
		'product' => array(
			'label'      => 'Ürün Sayfaları',
			'path'       => '/urun/',
			'components' => array(

				'main' => array(
					'label'  => 'Butonlar (tüm ürünler) ve Örnek Ürün',
					'fields' => array(
						'image'           => array( 'label' => 'Ana Görsel', 'type' => 'image', 'default' => 0 ),
						'gallery'         => array(
							'label'   => 'Galeri Görselleri',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'image' => array( 'label' => 'Görsel', 'type' => 'image' ),
								'alt'   => array( 'label' => 'Görsel Açıklaması', 'type' => 'text' ),
							),
							'default' => array(
								array( 'image' => 0, 'alt' => 'Bahçede ahşap tavuk kümesi' ),
								array( 'image' => 0, 'alt' => 'Ahşap tavuk kümesi yandan görünüm' ),
								array( 'image' => 0, 'alt' => 'Beyaz ahşap tavuk kümesi' ),
							),
						),
						'category'        => array( 'label' => 'Kategori Etiketi', 'type' => 'text', 'default' => 'Ahşap Dekorasyon' ),
						'title'           => array( 'label' => 'Ürün Adı', 'type' => 'text', 'default' => 'Ahşap Tavuk Kümesi' ),
						'subtitle'        => array( 'label' => 'Ürün Alt Başlığı', 'type' => 'text', 'default' => 'Emprenyeli ahşaptan, dış mekâna dayanıklı, montaja hazır' ),
						'description'     => array( 'label' => 'Ürün Açıklaması', 'type' => 'textarea', 'default' => 'Kümes, birinci sınıf emprenyeli çam kerestesinden üretilir. Gezinti alanı galvanizli tel örgü ile kaplanır, yuva bölümü yalıtımlıdır. Yumurta toplama kapağı dışarıdan açılır; temizlik için taban tablası çekmece şeklinde çıkar.' ),
						'features'        => array(
							'label'   => 'Öne Çıkan Özellikler',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'icon' => array( 'label' => 'İkon', 'type' => 'icon' ),
								'text' => array( 'label' => 'Özellik', 'type' => 'text' ),
							),
							'default' => array(
								array( 'icon' => 'tree', 'text' => 'Birinci sınıf emprenyeli çam kerestesi' ),
								array( 'icon' => 'shield', 'text' => 'Dış mekâna dayanıklı, yalıtımlı yuva bölümü' ),
								array( 'icon' => 'tools', 'text' => 'Kurulu teslim, ek montaj gerektirmez' ),
								array( 'icon' => 'ruler', 'text' => 'Talebe göre özel ölçü üretim' ),
							),
						),
						'cta_label'       => array( 'label' => 'Birincil Buton Metni', 'type' => 'text', 'default' => 'Teklif Alın' ),
						'cta_url'         => array( 'label' => 'Birincil Buton Bağlantısı', 'type' => 'url', 'default' => '/iletisim/' ),
						'secondary_label' => array( 'label' => 'İkincil Buton Metni', 'type' => 'text', 'default' => 'WhatsApp’tan Sorun' ),
						'secondary_url'   => array( 'label' => 'İkincil Buton Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905496481919' ),
						'note'            => array( 'label' => 'Buton Altı Not', 'type' => 'text', 'default' => 'Fiyatlar ölçü ve adede göre değişir; aynı gün fiyat veriyoruz.' ),
					),
				),

				/*
				 * Havuzdan gelen her urunun sayfasinda (/urun/<urun>/) ortak
				 * metinler. Urunun kendi bilgileri Urun Havuzu'ndadir.
				 */
				'detail' => array(
					'label'  => 'Havuz Ürün Sayfaları (ortak metinler)',
					'fields' => array(
						'related_sub'   => array( 'label' => 'Benzer Ürünler Başlığı (kategori) — {kategori} kategorinin adı olur', 'type' => 'text', 'default' => '{kategori} kategorisinde diğer ürünler' ),
						'related_group' => array( 'label' => 'Benzer Ürünler Başlığı (grup) — {grup} grubun adı olur', 'type' => 'text', 'default' => '{grup} grubunda diğer ürünler' ),
						'see_all'       => array( 'label' => 'Tümünü Gör Bağlantısı', 'type' => 'text', 'default' => 'Tümünü gör' ),
					),
				),

				'specs' => array(
					'label'  => 'Teknik Özellikler (yalnızca örnek ürün)',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Teknik Özellikler' ),
						'name'  => array( 'label' => 'Tablo Başlığı (ürün modeli)', 'type' => 'text', 'default' => 'Ahşap Tavuk Kümesi — Standart Model' ),
						'rows'  => array(
							'label'   => 'Özellik Satırları',
							'type'    => 'repeater',
							'max'     => 16,
							'fields'  => array(
								'label' => array( 'label' => 'Özellik Adı', 'type' => 'text' ),
								'value' => array( 'label' => 'Değer', 'type' => 'text' ),
							),
							'default' => array(
								array( 'label' => 'Genişlik', 'value' => '~150 cm' ),
								array( 'label' => 'Uzunluk', 'value' => '~200 cm (modele göre değişir)' ),
								array( 'label' => 'Yükseklik', 'value' => '~160 cm' ),
								array( 'label' => 'Kapasite', 'value' => '8 - 12 tavuk' ),
								array( 'label' => 'Dış Kaplama', 'value' => 'Emprenyeli çam, su bazlı boya' ),
								array( 'label' => 'Çatı Kaplaması', 'value' => 'Shingle (antrasit / kırmızı / yeşil)' ),
								array( 'label' => 'Gezinti Alanı', 'value' => 'Galvanizli tel örgü' ),
								array( 'label' => 'Zemin', 'value' => 'Çekmece tablalı, temizlenebilir' ),
								array( 'label' => 'Montaj Durumu', 'value' => 'KURULU TESLİM' ),
							),
						),
					),
				),

				'faq' => array(
					'label'  => 'Sık Sorulan Sorular (tüm ürün sayfaları)',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Sık Sorulan Sorular' ),
						'items' => array(
							'label'   => 'Sorular',
							'type'    => 'repeater',
							'max'     => 12,
							'fields'  => array(
								'question' => array( 'label' => 'Soru', 'type' => 'text' ),
								'answer'   => array( 'label' => 'Cevap', 'type' => 'textarea' ),
							),
							'default' => array(
								array(
									'question' => 'Sevkiyat süresi nedir?',
									'answer'   => 'Stoktaki modellerde normal koşullarda 3-5 iş günü içinde sevkiyat yapılır. Özel ölçü üretimlerde süre ölçüye göre değişir, sipariş onayında net tarih verilir.',
								),
								array(
									'question' => 'Montaj dahil mi?',
									'answer'   => 'Standart modeller kurulu teslim edilir; sahada ek montaj gerekmez. Büyük ölçülü üretimlerde parçalı sevkiyat ve yerinde kurulum yapılır.',
								),
								array(
									'question' => 'Özel ölçü üretim yapıyor musunuz?',
									'answer'   => 'Evet. Genişlik, uzunluk ve kapasite talebe göre değiştirilebilir. Ölçülerinizi ilettiğinizde aynı gün fiyat veriyoruz.',
								),
								array(
									'question' => 'Ahşap dış koşullara dayanıklı mı?',
									'answer'   => 'Üretimde emprenyeli çam kerestesi kullanılır ve yüzey su bazlı boya ile korunur. Yılda bir kez bakım boyası önerilir.',
								),
								array(
									'question' => 'Teslimat hangi illere yapılıyor?',
									'answer'   => 'İstanbul ve çevre illere kendi araçlarımızla, diğer illere anlaşmalı nakliye ile gönderim yapılır.',
								),
							),
						),
					),
				),
			),
		),

		/*
		 * Iletisim sayfasi.
		 *
		 * Harita adresten uretiliyor: panelde iframe adresi degil yalnizca
		 * konum metni tutuluyor, gomme adresini tema kuruyor. Boylece iframe
		 * kaynagi her zaman google.com kaliyor; panele girilen bir deger
		 * sayfaya keyfi bir site gomemiyor.
		 *
		 * Form bilincli olarak DEMO: hicbir yere gonderilmiyor, kaydedilmiyor
		 * ve "gonderildi" basarisi gosterilmiyor (paletci temasindaki teklif
		 * formuyla ayni yaklasim). Gercek iletisim icin telefon, WhatsApp ve
		 * e-posta baglantilari veriliyor.
		 */
		'contact' => array(
			'label'      => 'İletişim Sayfası',
			'path'       => '/iletisim/',
			'components' => array(

				'head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'eyebrow'  => array( 'label' => 'Üst Etiket', 'type' => 'text', 'default' => 'İLETİŞİM' ),
						'title'    => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Bize ulaşın' ),
						'subtitle' => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Ölçülerinizi iletin, aynı gün fiyat verelim. Çatalca’daki tesisimize ziyarete de bekleriz.' ),
					),
				),

				'info' => array(
					'label'  => 'İletişim Bilgileri',
					'fields' => array(
						'title'          => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'İletişim Bilgileri' ),
						'address_icon'   => array( 'label' => 'Adres İkonu', 'type' => 'icon', 'default' => 'pin' ),
						'address_title'  => array( 'label' => 'Adres Başlığı', 'type' => 'text', 'default' => 'Adres' ),
						'address'        => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1, 34494 Çatalca/İstanbul' ),
						'phone_icon'     => array( 'label' => 'Telefon İkonu', 'type' => 'icon', 'default' => 'phone' ),
						'phone_title'    => array( 'label' => 'Telefon Başlığı', 'type' => 'text', 'default' => 'Telefon' ),
						'phone_label'    => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0212 648 19 19' ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481919' ),
						'whatsapp_icon'  => array( 'label' => 'WhatsApp İkonu', 'type' => 'icon', 'default' => 'whatsapp' ),
						'whatsapp_title' => array( 'label' => 'WhatsApp Başlığı', 'type' => 'text', 'default' => 'WhatsApp' ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Metni', 'type' => 'text', 'default' => 'Hızlı fiyat için yazın' ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905496481919' ),
						'email_icon'     => array( 'label' => 'E-posta İkonu', 'type' => 'icon', 'default' => 'mail' ),
						'email_title'    => array( 'label' => 'E-posta Başlığı', 'type' => 'text', 'default' => 'E-posta' ),
						'email_label'    => array( 'label' => 'E-posta Metni', 'type' => 'text', 'default' => 'info@kocist.com.tr' ),
						'email_url'      => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@kocist.com.tr' ),
						'hours_icon'     => array( 'label' => 'Çalışma Saati İkonu', 'type' => 'icon', 'default' => 'clock' ),
						'hours_title'    => array( 'label' => 'Çalışma Saatleri Başlığı', 'type' => 'text', 'default' => 'Çalışma Saatleri' ),
						'hours'          => array( 'label' => 'Çalışma Saatleri', 'type' => 'text', 'default' => 'Hafta içi 08:00 - 19:00 · Cumartesi 08:00 - 16:00' ),
					),
				),

				'form' => array(
					'label'  => 'İletişim Formu',
					'fields' => array(
						'title'        => array( 'label' => 'Form Başlığı', 'type' => 'text', 'default' => 'Teklif ve bilgi talebi' ),
						'text'         => array( 'label' => 'Form Açıklaması', 'type' => 'textarea', 'default' => 'Aradığınız ürünü ve ölçüleri yazın; en kısa sürede dönüş yapalım.' ),
						'demo_notice'  => array( 'label' => 'Demo Uyarısı', 'type' => 'text', 'default' => 'Bu form demo amaçlıdır; gönderim yapılmaz. Lütfen telefon veya WhatsApp üzerinden ulaşın.' ),
						'name_label'    => array( 'label' => 'Ad Soyad Etiketi', 'type' => 'text', 'default' => 'Ad Soyad' ),
						'name_ph'       => array( 'label' => 'Ad Soyad İpucu', 'type' => 'text', 'default' => 'Adınız ve soyadınız' ),
						'phone_label'   => array( 'label' => 'Telefon Etiketi', 'type' => 'text', 'default' => 'Telefon' ),
						'phone_ph'      => array( 'label' => 'Telefon İpucu', 'type' => 'text', 'default' => '05XX XXX XX XX' ),
						'email_label'   => array( 'label' => 'E-posta Etiketi', 'type' => 'text', 'default' => 'E-posta' ),
						'email_ph'      => array( 'label' => 'E-posta İpucu', 'type' => 'text', 'default' => 'ornek@firma.com' ),
						'subject_label' => array( 'label' => 'Konu Etiketi', 'type' => 'text', 'default' => 'Konu' ),
						'subject_ph'    => array( 'label' => 'Konu İpucu', 'type' => 'text', 'default' => 'Hangi ürün grubu?' ),
						'detail_label'  => array( 'label' => 'Mesaj Etiketi', 'type' => 'text', 'default' => 'Mesajınız' ),
						'detail_ph'     => array( 'label' => 'Mesaj İpucu', 'type' => 'text', 'default' => 'Ürün, ölçü ve adet bilgisini yazın; örneğin “5x10 cm kalas, 100 adet, 3 metre”.' ),
						'submit_label'  => array( 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'Gönder' ),
					),
				),

				'map' => array(
					'label'  => 'Harita',
					'fields' => array(
						'title'      => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Tesisimiz' ),
						'query'      => array( 'label' => 'Harita Konumu (adres olarak yazın)', 'type' => 'text', 'default' => 'Kestanelik Mahallesi, Çatalca, İstanbul' ),
						'coords'     => array( 'label' => 'Koordinat (enlem,boylam — doldurulursa adres yerine bu kullanılır)', 'type' => 'text', 'default' => '' ),
						'link_label' => array( 'label' => 'Yol Tarifi Buton Metni', 'type' => 'text', 'default' => 'Yol tarifi al' ),
					),
				),
			),
		),

		/*
		 * Bulunamayan sayfa (404). Onizleme adresi bilerek olmayan bir sayfa.
		 */
		'notfound' => array(
			'label'      => '404 Sayfası',
			'path'       => '/bulunamadi-404/',
			// Gizli: sekme listesinde yok, "Sayfa bul" ile acilir; SEO listelerine (llms.txt) girmez.
			'hidden'     => true,
			'components' => array(
				'head' => array(
					'label'  => 'Sayfa Metinleri',
					'fields' => array(
						'crumb'  => array( 'label' => 'Üst Etiket', 'type' => 'text', 'default' => '404' ),
						'title'  => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Sayfa bulunamadı' ),
						'text'   => array( 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Aradığınız sayfa taşınmış ya da hiç var olmamış olabilir.' ),
						'button' => array( 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'Ana sayfaya dön' ),
					),
				),
			),
		),
	) + $kocist_category_pages,
);
