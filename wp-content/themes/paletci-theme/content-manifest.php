<?php
/**
 * Paletci temasi alan manifesti.
 *
 * Saf dizi dondurur; WordPress fonksiyonlarina bagimli degildir. Ag paneli bu
 * dosyayi siteye gecmeden dosya sisteminden okur.
 *
 * Alan turleri: text, textarea, url, image, icon, repeater
 */

return array(
	'site_key'   => 'paletci',
	'site_label' => 'İstanbul Paletçi',
	'pages'      => array(

		'global' => array(
			'label'      => 'Tüm Sayfalar (Menü ve Footer)',
			'path'       => '/',
			'components' => array(

				'header' => array(
					'label'  => 'Üst Menü / Başlık',
					'fields' => array(
						'logo_image'     => array( 'label' => 'Logo Görseli', 'type' => 'image', 'default' => 0 ),
						'logo_text'      => array( 'label' => 'Logo Yazısı', 'type' => 'text', 'default' => 'İstanbul Paletçi' ),
						'logo_sub'       => array( 'label' => 'Logo Alt Yazısı', 'type' => 'text', 'default' => 'Ahşap palet ve orman ürünleri' ),
						'menu'           => array(
							'label'   => 'Menü Öğeleri',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'label' => array( 'label' => 'Menü Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Anasayfa', 'url' => '/' ),
								array( 'label' => 'Ürünlerimiz', 'url' => '/urunlerimiz/' ),
								array( 'label' => 'Üretim Süreci', 'url' => '#surec' ),
								array( 'label' => 'Neden Biz', 'url' => '#neden-biz' ),
								array( 'label' => 'İletişim', 'url' => '#teklif' ),
							),
						),
						'phone_icon'     => array( 'label' => 'Telefon İkonu', 'type' => 'icon', 'default' => 'phone' ),
						'phone_label'    => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0212 648 10 90' ),
						'phone_url'      => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'whatsapp_icon'  => array( 'label' => 'WhatsApp İkonu', 'type' => 'icon', 'default' => 'whatsapp' ),
						'whatsapp_label' => array( 'label' => 'WhatsApp Buton Metni', 'type' => 'text', 'default' => 'WhatsApp’tan Yaz' ),
						'whatsapp_url'   => array( 'label' => 'WhatsApp Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905323749832' ),
					),
				),

				'footer' => array(
					'label'  => 'Footer',
					'fields' => array(
						'about_title'   => array( 'label' => 'Tanıtım Başlığı', 'type' => 'text', 'default' => 'İstanbul Paletçi' ),
						'about_text'    => array( 'label' => 'Tanıtım Metni', 'type' => 'textarea', 'default' => 'Ahşap palet, euro palet, sandık ve kafes üretimi. Bu bir yerel demo kurulumudur; görseller örnektir.' ),
						'links_title'   => array( 'label' => 'Bağlantılar Başlığı', 'type' => 'text', 'default' => 'Ürünler' ),
						'links'         => array(
							'label'   => 'Footer Bağlantıları',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'label' => array( 'label' => 'Bağlantı Metni', 'type' => 'text' ),
								'url'   => array( 'label' => 'Bağlantı', 'type' => 'url' ),
							),
							'default' => array(
								array( 'label' => 'Ahşap Palet', 'url' => '/urunlerimiz/' ),
								array( 'label' => 'Euro Palet', 'url' => '/urunlerimiz/' ),
								array( 'label' => 'Ahşap Sandık', 'url' => '/urunlerimiz/' ),
								array( 'label' => 'İkinci El Palet', 'url' => '/urunlerimiz/' ),
							),
						),
						'contact_title' => array( 'label' => 'İletişim Başlığı', 'type' => 'text', 'default' => 'Bize Ulaşın' ),
						'address'       => array( 'label' => 'Adres', 'type' => 'textarea', 'default' => 'Şahintepe, Eski İstanbul Cd. No:176, Başakşehir / İstanbul' ),
						'phone_label'   => array( 'label' => 'Telefon Metni', 'type' => 'text', 'default' => '0212 648 10 90' ),
						'phone_url'     => array( 'label' => 'Telefon Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
						'mobile_label'  => array( 'label' => 'Cep Telefonu Metni', 'type' => 'text', 'default' => '0532 374 98 32' ),
						'mobile_url'    => array( 'label' => 'Cep Telefonu Bağlantısı', 'type' => 'url', 'default' => 'tel:+905323749832' ),
						'email_label'   => array( 'label' => 'E-posta Metni', 'type' => 'text', 'default' => 'info@istanbulpaletci.com' ),
						'email_url'     => array( 'label' => 'E-posta Bağlantısı', 'type' => 'url', 'default' => 'mailto:info@istanbulpaletci.com' ),
						'copyright'     => array( 'label' => 'Telif Satırı', 'type' => 'text', 'default' => '© 2026 İstanbul Paletçi — yerel demo kurulumu' ),
						'demo_note'     => array( 'label' => 'Demo Uyarısı', 'type' => 'text', 'default' => 'Görseller örnek yer tutuculardır; gerçek marka görseli değildir.' ),
					),
				),
			),
		),

		'home' => array(
			'label'             => 'Anasayfa',
			'path'              => '/',
			'sortable_sections' => array( 'products', 'process', 'why', 'quote' ),
			'components'        => array(

				'hero' => array(
					'label'  => 'Hero (Giriş Bölümü)',
					'fields' => array(
						'badge'           => array( 'label' => 'Rozet Metni', 'type' => 'text', 'default' => '40 yılı aşkın tecrübe' ),
						'title'           => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ölçünüze göre ahşap palet, sandık ve kafes' ),
						'subtitle'        => array( 'label' => 'Alt Metin', 'type' => 'textarea', 'default' => 'Standart euro palet stoktan, özel ölçü üretim ise siparişe göre. Ölçü ve adedi iletin, fiyatı aynı gün paylaşalım.' ),
						'primary_label'   => array( 'label' => 'Birincil Buton Metni', 'type' => 'text', 'default' => 'Fiyat Teklifi İste' ),
						'primary_url'     => array( 'label' => 'Birincil Buton Bağlantısı', 'type' => 'url', 'default' => '#teklif' ),
						'secondary_label' => array( 'label' => 'İkincil Buton Metni', 'type' => 'text', 'default' => 'Ürünleri İncele' ),
						'secondary_url'   => array( 'label' => 'İkincil Buton Bağlantısı', 'type' => 'url', 'default' => '#urunler' ),
						'image'           => array( 'label' => 'Hero Görseli', 'type' => 'image', 'default' => 0 ),
						'chips'           => array(
							'label'   => 'Öne Çıkan Etiketler',
							'type'    => 'repeater',
							'max'     => 5,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'label' => array( 'label' => 'Etiket', 'type' => 'text' ),
							),
							'default' => array(
								array( 'icon' => 'ruler', 'label' => 'Özel ölçü üretim' ),
								array( 'icon' => 'clock', 'label' => 'Zamanında teslim' ),
								array( 'icon' => 'recycle', 'label' => 'İkinci el palet alım-satım' ),
							),
						),
					),
				),

				'products' => array(
					'label'  => 'Ürünler',
					'fields' => array(
						'title'    => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Ürünlerimiz' ),
						'subtitle' => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Beş ana ürün grubunda üretim ve tedarik yapıyoruz.' ),
						'pool'     => array(
							'label' => 'Ürünler (merkezî havuzdan)',
							'type'  => 'products',
						),
						'cta_label' => array( 'label' => 'Kart Bağlantı Metni', 'type' => 'text', 'default' => 'Teklif iste' ),
					),
				),

				'process' => array(
					'label'  => 'Üretim Süreci',
					'fields' => array(
						'title'    => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Sipariş nasıl ilerliyor' ),
						'subtitle' => array( 'label' => 'Bölüm Alt Başlığı', 'type' => 'textarea', 'default' => 'Talebin alınmasından sevkiyata kadar dört adım.' ),
						'steps'    => array(
							'label'   => 'Adımlar',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'number' => array( 'label' => 'Adım No', 'type' => 'text' ),
								'title'  => array( 'label' => 'Adım Başlığı', 'type' => 'text' ),
								'text'   => array( 'label' => 'Adım Metni', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'number' => '01', 'title' => 'Ölçü ve adet', 'text' => 'Kullanım amacını, ölçüyü ve adedi alıyoruz.' ),
								array( 'number' => '02', 'title' => 'Fiyat ve termin', 'text' => 'Aynı gün fiyat ve teslim tarihi paylaşıyoruz.' ),
								array( 'number' => '03', 'title' => 'Üretim', 'text' => 'Onay sonrası kesim, montaj ve gerekiyorsa ısıl işlem.' ),
								array( 'number' => '04', 'title' => 'Sevkiyat', 'text' => 'Planlanan tarihte yükleme ve teslim.' ),
							),
						),
					),
				),

				'why' => array(
					'label'  => 'Neden Biz',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Neden İstanbul Paletçi' ),
						'image' => array( 'label' => 'Bölüm Görseli', 'type' => 'image', 'default' => 0 ),
						'items' => array(
							'label'   => 'Maddeler',
							'type'    => 'repeater',
							'max'     => 6,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'  => array( 'label' => 'Metin', 'type' => 'textarea' ),
							),
							'default' => array(
								array( 'icon' => 'tree', 'title' => 'Uzun yıllara dayanan üretim', 'text' => 'Orman ürünleri alanında kuşaklar boyu süren bir işletme.' ),
								array( 'icon' => 'ruler', 'title' => 'Ölçüye göre imalat', 'text' => 'Standart dışı taleplerde kendi atölyemizde üretim.' ),
								array( 'icon' => 'truck', 'title' => 'İstanbul içi sevkiyat', 'text' => 'Planlı yükleme ve zamanında teslim.' ),
							),
						),
					),
				),

				'quote' => array(
					'label'  => 'Teklif Bölümü (Demo Form)',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Teklif isteyin' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Ölçü, adet ve kullanım amacını yazın; en kısa sürede dönüş yapalım.' ),
						'name_label'   => array( 'label' => 'Ad Alanı Etiketi', 'type' => 'text', 'default' => 'Ad Soyad' ),
						'phone_label'  => array( 'label' => 'Telefon Alanı Etiketi', 'type' => 'text', 'default' => 'Telefon' ),
						'detail_label' => array( 'label' => 'Detay Alanı Etiketi', 'type' => 'text', 'default' => 'Ölçü, adet ve not' ),
						'button_label' => array( 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'Gönder (demo)' ),
						'demo_notice'  => array( 'label' => 'Demo Uyarısı', 'type' => 'textarea', 'default' => 'Bu form demo amaçlıdır: gönderilen bilgi hiçbir yere iletilmez ve kaydedilmez. Gerçek talep için telefon veya WhatsApp kullanın.' ),
						'call_label'   => array( 'label' => 'Arama Butonu Metni', 'type' => 'text', 'default' => 'Hemen ara: 0212 648 10 90' ),
						'call_url'     => array( 'label' => 'Arama Butonu Bağlantısı', 'type' => 'url', 'default' => 'tel:+902126481090' ),
					),
				),
			),
		),

		'inner' => array(
			'label'      => 'Ürünlerimiz (Örnek Alt Sayfa)',
			'path'       => '/urunlerimiz/',
			'components' => array(

				'page_head' => array(
					'label'  => 'Sayfa Başlığı',
					'fields' => array(
						'badge'    => array( 'label' => 'Rozet', 'type' => 'text', 'default' => 'Ürün kataloğu' ),
						'title'    => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Ürünlerimiz' ),
						'subtitle' => array( 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Üretimini yaptığımız ve stoktan sunduğumuz ahşap ambalaj ürünleri.' ),
					),
				),

				'intro' => array(
					'label'  => 'Giriş Metni',
					'fields' => array(
						'title' => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Standart ve özel ölçü üretim' ),
						'body'  => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Euro palet gibi standart ölçüler stoktan karşılanır. Bunun dışındaki ölçüler atölyemizde siparişe göre üretilir. Bu demo kurulumdaki metinler örnektir ve panelden düzenlenebilir.' ),
						'image' => array( 'label' => 'Görsel', 'type' => 'image', 'default' => 0 ),
					),
				),

				'list' => array(
					'label'  => 'Ürün Listesi',
					'fields' => array(
						'title' => array( 'label' => 'Bölüm Başlığı', 'type' => 'text', 'default' => 'Ürün grupları' ),
						'items' => array(
							'label'   => 'Satırlar',
							'type'    => 'repeater',
							'max'     => 8,
							'fields'  => array(
								'icon'  => array( 'label' => 'İkon', 'type' => 'icon' ),
								'title' => array( 'label' => 'Başlık', 'type' => 'text' ),
								'text'  => array( 'label' => 'Metin', 'type' => 'textarea' ),
								'meta'  => array( 'label' => 'Ölçü / Not', 'type' => 'text' ),
							),
							'default' => array(
								array( 'icon' => 'box', 'title' => 'Ahşap Palet', 'text' => 'Depo ve sevkiyat için genel amaçlı palet.', 'meta' => 'Talebe göre ölçü' ),
								array( 'icon' => 'box', 'title' => 'Euro Palet', 'text' => 'Avrupa standardı, ihracata uygun palet.', 'meta' => '80 × 120 cm' ),
								array( 'icon' => 'shield', 'title' => 'Ahşap Sandık', 'text' => 'Kapalı sandık; hassas yük taşımaya uygun.', 'meta' => 'Özel ölçü' ),
								array( 'icon' => 'recycle', 'title' => 'İkinci El Palet', 'text' => 'Kontrollü ve onarılmış paletler.', 'meta' => 'Stok durumuna göre' ),
							),
						),
					),
				),

				'cta' => array(
					'label'  => 'Sayfa Sonu Çağrısı',
					'fields' => array(
						'title'        => array( 'label' => 'Başlık', 'type' => 'text', 'default' => 'Aradığınız ölçü listede yok mu?' ),
						'text'         => array( 'label' => 'Metin', 'type' => 'textarea', 'default' => 'Ölçüyü iletin, üretilebilirliğini ve fiyatını paylaşalım.' ),
						'button_label' => array( 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'WhatsApp’tan yaz' ),
						'button_url'   => array( 'label' => 'Buton Bağlantısı', 'type' => 'url', 'default' => 'https://wa.me/905323749832' ),
					),
				),
			),
		),
	),
);
