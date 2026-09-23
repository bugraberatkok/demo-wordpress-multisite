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
						'bank_url'    => array( 'label' => 'Banka Bilgileri Bağlantısı', 'type' => 'url', 'default' => '#banka-bilgileri' ),
						'phone_icon'  => array( 'label' => 'Telefon İkonu', 'type' => 'icon', 'default' => 'phone' ),
						'phone_label' => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '05496481919' ),
						'phone_url'   => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
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
								array( 'label' => 'Katalog', 'url' => '/#katalog', 'submenu' => '' ),
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
								array( 'label' => 'Hayvan Barınakları', 'url' => '/urun/' ),
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
								array( 'label' => 'İnsan Kaynakları', 'url' => '#insan-kaynaklari' ),
								array( 'label' => 'Referanslar', 'url' => '#referanslar' ),
								array( 'label' => 'Blog – Haberler', 'url' => '#blog' ),
							),
						),
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
								array( 'icon' => 'instagram', 'label' => 'Instagram', 'url' => '#instagram' ),
								array( 'icon' => 'facebook', 'label' => 'Facebook', 'url' => '#facebook' ),
								array( 'icon' => 'linkedin', 'label' => 'LinkedIn', 'url' => '#linkedin' ),
								array( 'icon' => 'whatsapp', 'label' => 'WhatsApp', 'url' => '#whatsapp' ),
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
								array( 'label' => 'Belgelerimiz', 'url' => '/kurumsal/#belgeler' ),
								array( 'label' => 'Referanslar', 'url' => '#referanslar' ),
								array( 'label' => 'Katalog', 'url' => '/#katalog' ),
								array( 'label' => 'İletişim', 'url' => '/iletisim/' ),
							),
						),

						// Iletisim sutunu
						'contact_title'   => array( 'label' => 'İletişim Başlığı', 'type' => 'text', 'default' => 'İletişim' ),
						'address_icon'    => array( 'label' => 'Adres İkonu', 'type' => 'icon', 'default' => 'pin' ),
						'address'         => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => 'Kestanelik Mahallesi, Çatalca / İstanbul' ),
						'map_label'       => array( 'label' => 'Harita Bağlantı Metni', 'type' => 'text', 'default' => 'Haritada göster' ),
						'map_url'         => array( 'label' => 'Harita Bağlantısı', 'type' => 'url', 'default' => '#harita' ),
						'phone_label'     => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'phone_url'       => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
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
			'sortable_sections' => array( 'catalog', 'products', 'capabilities', 'references', 'ctaband' ),
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
									'cta_url'   => '#dekorasyon',
								),
								array(
									'image'     => 0,
									'title'     => 'Plywood ve Kontrplak Levha',
									'text'      => 'Huş ve çam plywood, kontrplak ve OSB levha gruplarında stoklu çalışıyoruz.',
									'cta_label' => 'Levha Grubunu Gör',
									'cta_url'   => '#plywood-levha',
								),
								array(
									'image'     => 0,
									'title'     => 'Ahşap Salıncak ve Oyun Grupları',
									'text'      => 'Emprenyeli ahşaptan, dış mekâna dayanıklı çocuk oyun ve bahçe grupları.',
									'cta_label' => 'Ürünleri İnceleyin',
									'cta_url'   => '#ahsap-salincak',
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
								array( 'image' => 0, 'title' => 'Kereste', 'link_label' => 'Keşfet', 'link_url' => '#kereste' ),
								array( 'image' => 0, 'title' => 'Ambalaj', 'link_label' => 'Keşfet', 'link_url' => '#ambalaj' ),
								array( 'image' => 0, 'title' => 'Ahşap Dekorasyon', 'link_label' => 'Keşfet', 'link_url' => '#dekorasyon' ),
								array( 'image' => 0, 'title' => 'Hırdavat Grubu', 'link_label' => 'Keşfet', 'link_url' => '#hirdavat' ),
							),
						),
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
						'cta_label' => array( 'label' => 'Kart Bağlantı Metni', 'type' => 'text', 'default' => 'Teklif Al' ),
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
			'label'      => 'Ürün Sayfası',
			'path'       => '/urun/',
			'components' => array(

				'main' => array(
					'label'  => 'Ürün Başlığı ve Galeri',
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

				'specs' => array(
					'label'  => 'Teknik Özellikler',
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
					'label'  => 'Sık Sorulan Sorular',
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
						'phone_label'    => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0549 648 19 19' ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+905496481919' ),
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
	),
);
