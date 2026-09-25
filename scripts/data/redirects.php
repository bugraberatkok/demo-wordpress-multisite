<?php
/**
 * Canli sitelerin eski adreslerinden yeni adreslere yonlendirme listeleri.
 *
 * Kaynak: 23.09.2026'da canli alan adlarinin site haritalari ve sayfa
 * baglantilari tarandi (yalnizca okuma). Yeni sitede ayni adreste karsiligi
 * olanlar listede yok (yonlendirme gerekmez). Eski hazir temanin demo
 * sayfalari (vize, kocluk, portfolyo, ekip; icerigi lorem ipsum) 410 alir.
 *
 * Yukleme: wp eval-file /scripts/load-redirects.php (bkz. o dosya). Liste
 * sonra panelden (SEO ve GEO -> Yonlendirmeler) duzenlenir.
 *
 * Bicim: includes/redirects.php basindaki aciklama.
 */

return array(

	// istanbulpaletci.com: 31 eski adres; 13'u yeni sitede ayni.
	'istanbulpaletci'    => <<<'TXT'
# Kategori arsivi blog sayfasina
/category/*              /blog/

# Eski temanin demo icerigi ve WordPress ornek sayfasi
/portfolio-item/*        410
/portfolio_category/*    410
/author/*                410
/ornek-sayfa/            410

# Eski site haritalari (Yoast): Search Console'da kayitli olabilir
/sitemap_index.xml       /wp-sitemap.xml
/post-sitemap.xml        /wp-sitemap.xml
/page-sitemap.xml        /wp-sitemap.xml
/category-sitemap.xml    /wp-sitemap.xml
TXT,

	// sanayipalet.com: 87 eski adres; 8'i yeni sitede ayni, 51'i demo icerik.
	'sanayi-palet'       => <<<'TXT'
# Urun alt sayfalari: yeni sitede tek urunler sayfasinin bolumleri
/urunler/ahsap-palet/        /urunler/#paletler
/urunler/ahsap-sandik/       /urunler/#sandiklar
/urunler/ahsap-kafes/        /urunler/#kafesler
/urunler/sarf-malzemeleri/   /urunler/

# Yeni sitede olmayan blog yazilari: en yakin konu. Yazilar yeni sitede ayni
# adresle yeniden yayimlanirsa bu satirlar kendiliginden devre disi kalir
# (kural yalnizca adres bulunamazsa calisir).
/ahsap-palet-fiyatlari-nedir/                        /urunler/#paletler
/palet-kullanmanin-avantajlari-nelerdir/             /ahsap-palet-nedir/
/sanayi-paletleri-hangi-alanlarda-kullanilir/        /ahsap-palet-nedir/
/ahsap-sanayi-paletlerinden-dekorasyon-fikirleri/    /blog/

# Etiket ve kategori arsivleri
/tag/*                   /blog/
/category/*              /blog/

# Eski temanin demo icerigi (vize, kocluk, ulke, ekip) ve ornek sayfa
/visa*                   410
/coaching*               410
/country*                410
/team/*                  410
/our-team-member/        410
/author/*                410
/faq/                    410
/ornek-sayfa/            410

# Eski statik sayfa (public_html/old) ve eski site haritalari
/old*                    /
/sitemap_index.xml       /wp-sitemap.xml
/post-sitemap.xml        /wp-sitemap.xml
/page-sitemap.xml        /wp-sitemap.xml
TXT,

	// istanbulkeresteci.com: 19 eski adres; 16'si yeni sitede ayni.
	'istanbul-keresteci' => <<<'TXT'
# Adi kisaltilan blog yazilari
/istanbul-kereste-fiyatlari-nedir/                    /istanbul-kereste-fiyatlari/
/mese-agaci-nedir-mese-agaci-ozellikleri-nelerdir/    /mese-agaci-nedir/

# Kategori arsivi blog sayfasina
/category/*              /blog/

# Eski site haritalari (Yoast): Search Console'da kayitli olabilir
/sitemap_index.xml       /wp-sitemap.xml
/post-sitemap.xml        /wp-sitemap.xml
/page-sitemap.xml        /wp-sitemap.xml
/category-sitemap.xml    /wp-sitemap.xml
TXT,

	// ahsapkasa.com ve ahsapambalajsanayi.com: tek sayfalik eski site.
	'ahsapkasa'          => <<<'TXT'
/index.html              /
TXT,

	'ahsapambalaj'       => <<<'TXT'
/index.html              /
TXT,

	// ithalkeresteci.com ve kavakkeresteci.com eski halde yalnizca kocist.com.tr'yi
	// cerceve icinde gosteriyordu; kendi adresleri yok, yonlendirme gerekmez.

	// kocist.com.tr: 604 eski adres (site haritasi, 25.09.2026). Liste
	// scripts/build-kocist-redirects.php ile uretildi; yerelde 604 adresin
	// 601'i dogru sayfaya gidiyor. Eksik: /kvkk, /gizlilik, /cerezler (yeni
	// sitede bu sayfalar henuz yok; sayfalar acilinca ayni adreste olacak).
	'kocist'             => (string) file_get_contents( __DIR__ . '/kocist-redirects.txt' ),

	// woodkocist.com.tr (WooCommerce): urun ve kategori adresleri yeni sitede
	// ayni; yalnizca kalkan urun, urun etiketleri, hesap ve Ingilizce WooCommerce
	// sayfalari. Yerelde 26 kural denendi; 56 eski urun adresinin hepsi 200.
	'woodkocist'         => (string) file_get_contents( __DIR__ . '/woodkocist-redirects.txt' ),
);
