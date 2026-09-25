<?php
/**
 * woodkocist temasi alan manifesti.
 *
 * Urunler burada tanimlanmaz: merkezi Urun Havuzu'ndan gelir. Hangi urunun
 * sitede gorunecegi Icerik Studyosu -> Magaza -> Tum Urunler alanindan, ya da
 * kategori sekmelerinden (Kopek Kulubeleri...) yalnizca o kategori icin secilir
 * (urun ekle / cikar / sirala / siteye ozel ad-fiyat). Kod gerekmez.
 *
 * Metinler woodkocist.com.tr'nin kendi sayfalarindan (25 Eylul 2026): ana sayfa,
 * Sirketimiz, Cozum Merkezi (/iletisim/), Ozel Uretim, SSS ve odeme adimi.
 * Iletisim: sitenin kendi /iletisim/ sayfasi (WhatsApp 0549 648 19 19, kurumsal
 * hat 0212 648 19 19, info@woodkocist.com.tr). Adres ve "50 yil" butun
 * sitelerde ayni (kullanici karari, 25 Eylul 2026): Kestanelik ... 2125/1.
 *
 * Yasal ve kurumsal metinler (KVKK, mesafeli satis...) WordPress sayfasidir:
 * content/pages.php ilk metni verir, sonrasi WordPress duzenleyicisinden.
 */

$address = "Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1\nÇatalca, İstanbul";

$head = static function ( string $title, string $lead ): array {
	return array(
		'label'  => 'Sayfa Başı',
		'fields' => array(
			'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => $title ),
			'lead'  => array( 'label' => 'Öne Çıkan Cümle', 'type' => 'textarea', 'default' => $lead ),
		),
	);
};

/*
 * Kategori sayfalari (/urun-kategori/<seri>/<kategori>/): panelde her biri ayri
 * sekme. Onizleme o kategorinin sayfasini acar; "Ürünler" bolumunde yalnizca o
 * kategorinin urunleri listelenir (secim, sira ve bu siteye ozel ad/fiyat).
 * 'category' havuzdaki kategori adiyla birebir ayni olmali.
 */
$categories = array(
	// kisaltma => array( havuzdaki ad, seri kisaltmasi, sayfa basligi, one cikan cumle )
	'kopek-kulubeleri' => array( 'Köpek Kulübeleri', 'woodpets', 'Ahşap köpek kulübeleri', 'Verandalı, bölmeli ve farklı boylarda ahşap köpek kulübeleri. Ölçü, ahşap cinsi ve KDV dahil fiyatlarıyla.' ),
	'kedi-yuvalari'    => array( 'Kedi Yuvaları', 'woodpets', 'Ahşap kedi yuvaları', 'Tekli, üçlü ve altılı ahşap kedi yuvaları; farklı renk seçenekleriyle.' ),
	'kamelyalar'       => array( 'Kamelyalar', 'woodgarden', 'Ahşap kamelyalar', 'Klasik, Prestij ve Master seri ahşap kamelyalar; zemin platformlu ve kuşluklu modeller.' ),
	'cardaklar'        => array( 'Çardaklar', 'woodgarden', 'Ahşap çardaklar', 'Kompakt, orta ve büyük boy ahşap çardaklar.' ),
	'piknik-masalari'  => array( 'Piknik Masaları', 'woodgarden', 'Ahşap piknik masaları', 'Bahçe ve teras için oturaklı ahşap piknik masaları.' ),
	'ahsap-sezlonglar' => array( 'Şezlonglar', 'woodgarden', 'Ahşap şezlonglar', 'Bahçe, havuz başı ve teras için ahşap şezlonglar.' ),
	'adirondack'       => array( 'Adirondack', 'woodliving', 'Adirondack ahşap sandalyeler', 'Katlanır ve sabit, küçük ve büyük boy Adirondack sandalyeler.' ),
);

$category_pages = array();

foreach ( $categories as $slug => $c ) {
	$category_pages[ 'kat-' . $slug ] = array(
		'label'      => $c[0] . ' (Mağaza)',
		'path'       => '/urun-kategori/' . $c[1] . '/' . $slug . '/',
		'seo_source' => array(
			'title'       => 'head.title',
			'description' => 'head.lead',
		),
		'components' => array(
			'head'     => array(
				'label'  => 'Sayfa Başı ve Menü Adı',
				'fields' => array(
					'name'  => array( 'label' => 'Kategori adı (menü, kenar listesi, ana sayfa kutuları)', 'type' => 'text', 'default' => $c[0], 'hint' => 'Yalnızca bu sitede görünen ad; Ürün Havuzu’ndaki kategori adı değişmez.' ),
					'title' => array( 'label' => 'Sayfa başlığı', 'type' => 'text', 'default' => $c[2] ),
					'lead'  => array( 'label' => 'Öne çıkan cümle', 'type' => 'textarea', 'default' => $c[3] ),
				),
			),
			'products' => array(
				'label'  => 'Ürünler',
				'fields' => array(
					'pool' => array(
						'label'    => $c[0] . ': ürünler (seçin, sıralayın, bu siteye özel ad ve fiyat)',
						'type'     => 'products',
						'category' => $c[0],
					),
				),
			),
		),
	);
}

return array(
	'site_key'          => 'woodkocist',
	'site_label'        => 'Koçist · woodkocist.com.tr',
	// Panelde gorunen kisa ad (alan adi olmadan).
	'panel_label'       => 'WOOD KOCIST',

	'seo_site_defaults' => array(
		'name'        => 'WOOD KOCIST',
		'legal_name'  => '',
		'description' => 'Ahşap bahçe mobilyası, ev ürünleri ve evcil hayvan yuvaları: Adirondack sandalye, çardak, kamelya, piknik masası, şezlong, kedi yuvası ve köpek kulübesi. Koçist Orman Ürünleri markası.',
		'phone'       => '+90 212 648 19 19',
		'email'       => 'info@woodkocist.com.tr',
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
			'label'      => 'Tüm Sayfalar (Üst Menü, Alt Bilgi, Mağaza Ayarları)',
			'path'       => '/',
			'components' => array(
				'header' => array(
					'label'  => 'Üst Menü',
					'fields' => array(
						'logo_text'      => array( 'label' => 'Marka', 'type' => 'text', 'default' => 'WOOD KOCIST' ),
						'menu'           => array(
							'label'   => 'Menü Öğeleri (adresi /magaza/ olana ürün menüsü, /sirketimiz/ olana Hakkımızda menüsü açılır)',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı Adresi', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Anasayfa', 'url' => '/' ),
								array( 'label' => 'Mağaza', 'url' => '/magaza/' ),
								array( 'label' => 'Hakkımızda', 'url' => '/sirketimiz/' ),
								array( 'label' => 'Çözüm Merkezi', 'url' => '/iletisim/' ),
							),
						),
						'about_menu'     => array(
							'label'   => 'Hakkımızda açılır menüsü (alt bilgideki "Kurumsal" listesi de bu)',
							'type'    => 'repeater',
							'max'     => 10,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı Adresi', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Hikayemiz', 'url' => '/sirketimiz/' ),
								array( 'label' => 'Sürdürülebilirlik', 'url' => '/surdurulebilirlik/' ),
								array( 'label' => 'Kurumsal Politikalar', 'url' => '/politikalar/' ),
								array( 'label' => 'Sıkça Sorulan Sorular', 'url' => '/sss/' ),
								array( 'label' => 'Çözüm Merkezi', 'url' => '/iletisim/' ),
								array( 'label' => 'Özel Üretim', 'url' => '/ozel-uretim-talep-formu/' ),
							),
						),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905496481919' ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Numarası (görünen)', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'phone_label'    => array( 'label' => 'Kurumsal Telefon (boşsa gizlenir)', 'type' => 'text', 'default' => '0212 648 19 19' ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481919' ),
						'email'          => array( 'label' => 'E-posta (boşsa gizlenir)', 'type' => 'text', 'default' => 'info@woodkocist.com.tr' ),
					),
				),
				'footer' => array(
					'label'  => 'Alt Bilgi',
					'fields' => array(
						'band'      => array( 'label' => 'Marka Şeridi', 'type' => 'text', 'default' => 'WOOD Koçist bir Koçist Orman Ürünleri markasıdır.' ),
						'tagline'   => array( 'label' => 'Alt Bilgi Metni', 'type' => 'textarea', 'default' => 'Sadece kendi evlerimizde ve bahçelerimizde görmek isteyeceğimiz, zanaatına ve kalitesine bizzat güvendiğimiz özel tasarımları üretiyoruz.' ),
						'address'   => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => $address ),
						'copyright' => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri' ),
					),
				),
				'shop'   => array(
					'label'  => 'Mağaza Ayarları (sepet ve ödeme)',
					'fields' => array(
						'vat_mode'       => array( 'label' => 'Fiyatlar KDV: "dahil" ya da "haric"', 'type' => 'text', 'default' => 'dahil', 'hint' => 'Ürün Havuzu’ndaki fiyatlar KDV’yi içeriyorsa "dahil" (woodkocist.com.tr’de fiyatlar KDV dahil). "haric" yazılırsa sepette KDV ayrıca eklenir.' ),
						'vat_rate'       => array( 'label' => 'KDV Oranı (%)', 'type' => 'text', 'default' => '20' ),
						'shipping_label' => array( 'label' => 'Kargo Satırında Yazan', 'type' => 'text', 'default' => 'Ücretsiz' ),
						'shipping_note'  => array( 'label' => 'Teslimat Notu (ürün sayfası ve sepet)', 'type' => 'text', 'default' => 'Küçük ürünler anlaşmalı lojistikle, büyük ve kurulum gerektiren ürünler saha ekibimizle gelir.' ),
						'lead_time'      => array( 'label' => 'Üretim ve Teslim Süresi', 'type' => 'text', 'default' => 'Standart modellerde üretim ve kalite kontrol ortalama 7–14 iş günü sürer.' ),
						'pickup_address' => array( 'label' => 'Fabrikadan Teslim Adresi', 'type' => 'textarea', 'default' => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1, Çatalca, İstanbul' ),
						'payment_text'   => array( 'label' => 'Havale / EFT Açıklaması', 'type' => 'textarea', 'default' => 'Ödemenizi doğrudan şirketimizin banka hesabına yapın. Lütfen ilgili sipariş numarasını ödeme açıklamanızda belirtin. Siparişiniz, ödemeniz onaylandıktan sonra üretim programına alınacaktır.' ),
						'bank_accounts'  => array( 'label' => 'Banka Hesapları (sipariş onayında görünür)', 'type' => 'textarea', 'default' => '', 'hint' => 'Her satıra bir bilgi: Banka adı, hesap sahibi, IBAN. Boşsa onay sayfası "hesap bilgilerini size ileteceğiz" der.' ),
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
						'title'     => array( 'label' => 'Başlık (iki satır: satır sonuyla ayırın)', 'type' => 'textarea', 'default' => "Doğanın Estetiği\nMühendisliğin Kusursuzluğu" ),
						'lead'      => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Yarım asırlık Koçist® tecrübesiyle, yaşam alanlarınızı zamansız tasarımlarla buluşturuyoruz. Koleksiyonumuzu keşfedin veya mimari projeleriniz için bizimle tasarım sürecini başlatın.' ),
						'cta'       => array( 'label' => 'Ana Düğme', 'type' => 'text', 'default' => 'Koleksiyonu keşfet' ),
						'cta_alt'   => array( 'label' => 'İkinci Düğme (Özel Üretim)', 'type' => 'text', 'default' => 'Özel üretim isteyin' ),
					),
				),
				'catalog' => array(
					'label'  => 'Seriler',
					'fields' => array(
						'lines' => array(
							'label'   => 'Seriler (menü, ana sayfa kutuları, mağaza)',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'label'    => array( 'label' => 'Seri Adı', 'type' => 'text' ),
								'category' => array( 'label' => 'Havuzdaki Kategori Adı (birebir)', 'type' => 'text' ),
								'text'     => array( 'label' => 'Kısa Açıklama', 'type' => 'text' ),
							),
							'default' => array(
								array( 'label' => 'WOODPets', 'category' => 'WOODPets', 'text' => 'Köpek kulübeleri ve kedi yuvaları' ),
								array( 'label' => 'WOODGarden', 'category' => 'WOODGarden', 'text' => 'Kamelyalar, çardaklar, piknik masaları ve şezlonglar' ),
								array( 'label' => 'WOODLiving', 'category' => 'WOODLiving', 'text' => 'Adirondack sandalyeler' ),
							),
						),
					),
				),
				'rows' => array(
					'label'  => 'Ürün Satırları',
					'fields' => array(
						'items' => array(
							'label'   => 'Satırlar (kategori adı havuzdakiyle birebir)',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'title'    => array( 'label' => 'Başlık', 'type' => 'text' ),
								'category' => array( 'label' => 'Havuzdaki Kategori Adı', 'type' => 'text' ),
							),
							'default' => array(
								array( 'title' => 'Ahşap Kamelyalar', 'category' => 'Kamelyalar' ),
								array( 'title' => 'Adirondack Sandalyeler', 'category' => 'Adirondack' ),
								array( 'title' => 'Ahşap Köpek Kulübeleri', 'category' => 'Köpek Kulübeleri' ),
								array( 'title' => 'Ahşap Çardaklar', 'category' => 'Çardaklar' ),
								array( 'title' => 'Ahşap Kedi Yuvaları', 'category' => 'Kedi Yuvaları' ),
							),
						),
					),
				),
				'banner' => array(
					'label'  => 'Tanıtım Bandı',
					'fields' => array(
						'title'    => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Bahçenize değer katan, her mevsim koşuluna dayanıklı el işçiliği lüks ahşap kamelya ve çardak tasarımları.' ),
						'button'   => array( 'label' => 'Düğme', 'type' => 'text', 'default' => 'Kamelyaları inceleyin' ),
						'category' => array( 'label' => 'Düğmenin Açtığı Kategori (havuzdaki ad)', 'type' => 'text', 'default' => 'Kamelyalar' ),
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
								array( 'title' => 'Ürünü sepete ekleyin', 'text' => 'Ürün sayfasında adedi seçip “Sepete ekle”ye basın. Fiyatı yazmayan ürünleri WhatsApp’tan sorun.' ),
								array( 'title' => 'Teslimat bilgilerinizi girin', 'text' => 'Üyelik gerekmez: ad, telefon, e-posta ve adres yeterli. İsterseniz fabrikadan teslim alın.' ),
								array( 'title' => 'Havale / EFT ile ödeyin', 'text' => 'Sipariş numaranızı açıklamaya yazın. Ödeme onaylanınca siparişiniz üretim programına alınır.' ),
							),
						),
					),
				),
				'about' => array(
					'label'  => 'Marka Bölümü',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'WOOD Koçist Orman Ürünleri' ),
						'text'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Sadece kendi evlerimizde ve bahçelerimizde görmek isteyeceğimiz, zanaatına ve kalitesine bizzat güvendiğimiz özel tasarımları üretiyoruz. Değerli ahşap yapıların taşımacılığında derin bir tecrübeye sahibiz; ürünlerimizi her yere korumalı ve güvenli nakliye standartlarıyla ulaştırıyoruz.' ),
						'items' => array(
							'label'   => 'Öne Çıkanlar',
							'type'    => 'repeater',
							'max'     => 4,
							'fields'  => array(
								'text' => array( 'label' => 'Metin', 'type' => 'text' ),
							),
							'default' => array(
								array( 'text' => '50 Yıllık Tecrübe' ),
								array( 'text' => 'Birinci Sınıf İşçilik' ),
								array( 'text' => 'Yüksek Kalite' ),
							),
						),
					),
				),
			),
		),

		'shop' => array(
			'label'      => 'Mağaza',
			'path'       => '/magaza/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head' => $head( 'Mağaza', 'Kamelya, çardak, Adirondack sandalye, piknik masası, şezlong, köpek kulübesi ve kedi yuvası. Fiyatlar KDV dahil; üyelik gerekmeden sipariş verin.' ),
				'pool' => array(
					'label'  => 'Tüm Ürünler',
					'fields' => array(
						'pool' => array(
							'label' => 'Sitede görünen ürünler (Ürün Havuzu’ndan seçin, sıralayın)',
							'type'  => 'products',
						),
					),
				),
			),
		),
	) + $category_pages + array(

		'about' => array(
			'label'      => 'Hakkımızda (Şirketimiz)',
			'path'       => '/sirketimiz/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'story.intro',
			),
			'components' => array(
				'head'  => $head( 'Doğanın dokusunu 50 yıllık tecrübeyle şekillendiriyoruz', 'Yarım asırlık Koçist® güvencesine hoş geldiniz.' ),
				'story' => array(
					'label'  => 'Hikayemiz',
					'fields' => array(
						'intro' => array( 'label' => 'Giriş', 'type' => 'textarea', 'default' => 'Koçist Grup olarak, orman ürünleri sektöründeki 50 yıllık köklü geçmişimizle, doğanın bize sunduğu değeri yaşam alanlarınızı güzelleştiren estetik dokunuşlara dönüştürüyoruz.' ),
						'items' => array(
							'label'   => 'Öne Çıkanlar',
							'type'    => 'repeater',
							'max'     => 4,
							'fields'  => array(
								'text' => array( 'label' => 'Metin', 'type' => 'text' ),
							),
							'default' => array(
								array( 'text' => '50 Yıllık Tecrübe' ),
								array( 'text' => 'Yüksek Standartlar' ),
								array( 'text' => 'Birinci Sınıf İşçilik' ),
							),
						),
						'title' => array( 'label' => 'İkinci Başlık', 'type' => 'text', 'default' => 'İhtiyacınıza özel uçtan uca üretim süreci' ),
						'text'  => array( 'label' => 'Metin (paragrafları boş satırla ayırın)', 'type' => 'textarea', 'default' => "Endüstriyel ahşap uzmanlığımızı bir adım öteye taşıyarak; ahşabın sıcaklığını yansıtan modern dekorasyon ürünleri ve sadık dostlarımız için köpek kulübeleri gibi özel tasarım ahşap yapılar üretiyoruz.\n\nAmacımız, yarım asırlık tecrübemiz ve işçiliğimizle, ilkelerimizden taviz vermeden ahşabın en doğal ve şık halini sizlere sunmaktır.\n\nKereste ve endüstriyel ambalaj çözümlerimizin yanı sıra; evinize, bahçenize ve evcil hayvanlarınıza özel ahşap projeleri hayata geçiriyoruz. Standart ölçülerin dışına çıkarak, tamamen sizin taleplerinize ve alanınıza uygun dekoratif ahşap yapılar dizayn ediyor, uzman ekibimizle en hızlı şekilde üreterek güvenle adresinize sevk ediyoruz." ),
					),
				),
			),
		),

		'faq' => array(
			'label'      => 'Sıkça Sorulan Sorular',
			'path'       => '/sss/',
			'seo_source' => array(
				'type'        => 'FAQPage',
				'questions'   => 'items.rows',
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head'  => $head( 'Sıkça sorulan sorular', 'Üretim ve malzeme, özel üretim, nakliye ve kurulum, sipariş ve ödeme, iade ve garanti hakkında en çok sorulanlar.' ),
				'items' => array(
					'label'  => 'Sorular',
					'fields' => array(
						'rows' => array(
							'label'   => 'Soru ve Cevaplar (aynı grup adı bir başlık altında toplanır)',
							'type'    => 'repeater',
							'max'     => 40,
							'fields'  => array(
								'group'    => array( 'label' => 'Grup', 'type' => 'text' ),
								'question' => array( 'label' => 'Soru', 'type' => 'text' ),
								'answer'   => array( 'label' => 'Cevap', 'type' => 'textarea' ),
							),
							'default' => require __DIR__ . '/content/faq.php',
						),
					),
				),
			),
		),

		'contact' => array(
			'label'      => 'Çözüm Merkezi (İletişim)',
			'path'       => '/iletisim/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head'    => $head( 'Çözüm Merkezi', 'Sorularınız, özel üretim talepleriniz veya kurumsal iş birlikleri için bize ulaşın; talebiniz ilgili departmana iletilir ve en kısa sürede dönüş yapılır.' ),
				'details' => array(
					'label'  => 'Bilgiler',
					'fields' => array(
						'lines'   => array(
							'label'   => 'Adresler',
							'type'    => 'repeater',
							'max'     => 5,
							'fields'  => array(
								'label' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'value' => array( 'label' => 'Adres', 'type' => 'text' ),
							),
							'default' => array(
								array( 'label' => 'Adres (fabrika ve showroom)', 'value' => 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1, Çatalca, İstanbul' ),
							),
						),
						'map'     => array( 'label' => 'Harita Araması (boşsa harita yok)', 'type' => 'text', 'default' => 'Koçist Orman Ürünleri' ),
					),
				),
			),
		),

		'custom' => array(
			'label'      => 'Özel Üretim',
			'path'       => '/ozel-uretim-talep-formu/',
			'seo_source' => array(
				'title'       => 'head.title',
				'description' => 'head.lead',
			),
			'components' => array(
				'head' => $head( 'Mekânlarınıza özel mimari ustalık ve mühendislik çözümleri', 'Koçist Grup’un yarım asırlık orman ürünleri tecrübesiyle, hayallerinizdeki yapıyı projelendiriyor, doğanın estetiğini mühendislik hassasiyetiyle birleştiriyoruz.' ),
				'form' => array(
					'label'  => 'Form',
					'fields' => array(
						'title' => array( 'label' => 'Form Başlığı', 'type' => 'text', 'default' => 'Özel proje talep formu' ),
						'lead'  => array( 'label' => 'Form Açıklaması', 'type' => 'textarea', 'default' => 'Mekânınızın teknik gereksinimlerini ve mimari beklentilerinizi bizimle paylaşın; uzman mühendislik ekibimiz projenizi hayata geçirmek üzere sizinle iletişime geçsin.' ),
					),
				),
			),
		),
	),
);
