<?php
/**
 * kocist.com.tr eski adreslerinden yeni Kocist sitesine yonlendirme listesi uretir.
 *
 * Calistirma (Kocist sitesinde; havuza kocist.com.tr urunleri aktarilmis olmali):
 *   wp eval-file /scripts/build-kocist-redirects.php --url=http://localhost:8080/kocist/
 *
 * Cikti: panelin "SEO ve GEO -> Yonlendirmeler" kutusuna yapistirilacak metin
 * (bir satir bir kural). scripts/data/redirects.php'deki 'kocist' listesi bu
 * ciktidir.
 *
 * Eslemeler:
 * - Urun: /urun/<eski>/<n> ve /urunlerimiz/<eski> -> /urun/<yeni>/. Yeni urun,
 *   aktarimdaki kaynak adresiyle (_nwcs_source) bulunur; ortak (WOOD KOCIST ya da
 *   daha once havuzda olan) urunler ve kaynakta iki kez gecenler aktarimdaki
 *   kuralla (kod, sonra ad) eslenir.
 * - Kategori: /urunlerimiz/<kategori> -> /kategoriler/<grup>/<kategori>/.
 * - Blog: /blog/<eski> -> ayni yazi (baslik benzerligi), bulunamazsa /blog/.
 * - Sayfalar: /hakkimizda -> /kurumsal/ vb. Karsiligi olmayan sayfa listede yok.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 'Yalnizca WP-CLI ile calistirilir.' );
}

$data = json_decode( (string) file_get_contents( __DIR__ . '/data/kocist-products.json' ), true );

if ( ! is_array( $data ) ) {
	WP_CLI::error( 'Veri dosyasi okunamadi.' );
}

$fold = static function ( string $text ): string {
	$text = mb_strtolower( $text, 'UTF-8' );
	$text = strtr( $text, array( 'ı' => 'i', 'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ö' => 'o', 'ç' => 'c', 'â' => 'a', 'î' => 'i', 'û' => 'u' ) );

	return trim( (string) preg_replace( '/[^a-z0-9]+/', ' ', $text ) );
};

// Kategori eslemesi (import-kocist.php ile ayni) ve grup adresleri.
$groups = array(
	'kereste'          => 'kereste',
	'ambalaj'          => 'ambalaj',
	'ahsap-dekorasyon' => 'dekorasyon',
	'hirdavat'         => 'hirdavat',
);
$subs = array(
	'kalas' => 'kereste/ahsap-kalas', 'cita' => 'kereste/ahsap-cita', 'ahsap-rabita' => 'kereste/ahsap-rabita', 'osb' => 'kereste/osb-plaka-levha',
	'kontrplak' => 'kereste/kontrplak-levha', 'plywood' => 'kereste/plywood-levha', 'maden-diregi' => 'kereste/maden-diregi-kereste', 'takoz' => 'kereste/ahsap-takoz',
	'insaatlik-catilik-kereste' => 'kereste/insaatlik-catilik-kereste', 'ahsap-palet' => 'ambalaj/ahsap-palet', 'ahsap-sandik' => 'ambalaj/ahsap-sandik',
	'ahsap-kafes' => 'ambalaj/ahsap-kafes', 'triplex-bariyer' => 'ambalaj/triplex-bariyer', 'ahsap-cam-tasima-sehpasi' => 'ambalaj/cam-tasima-sehpasi',
	'ahsap-ev' => 'dekorasyon/ahsap-ev', 'kamelya' => 'dekorasyon/kamelya', 'cardak' => 'dekorasyon/cardak', 'ahsap-oturma-grubu' => 'dekorasyon/ahsap-bank',
	'ahsap-saksi' => 'dekorasyon/saksi-sebze-yatagi', 'ahsap-salincak' => 'dekorasyon/ahsap-salincak', 'ahsap-pergola' => 'dekorasyon/ahsap-pergola',
	'ahsapsezlong' => 'dekorasyon/ahsap-sezlong', 'ahsap-tahterevalli' => 'dekorasyon/ahsap-tahterevalli', 'ahsap-yatak' => 'dekorasyon/ahsap-yatak',
	'ahsap-ayak-alti' => 'dekorasyon/ahsap-ayak-alti', 'hayvan-barinaklari' => 'dekorasyon/hayvan-barinaklari', 'adirondack' => 'dekorasyon/adirondack-sandalye',
	'celikyapi' => 'dekorasyon/celik-yapi', 'civiler' => 'hirdavat/civiler', 'zimba-igneleri' => 'hirdavat/zimba-telleri', 'klipsler' => 'hirdavat/klipsler',
	'teller' => 'hirdavat/baglama-telleri', 'tabancalar' => 'hirdavat/civi-tabancalari', 'sartlandiricilar' => 'hirdavat/sartlandiricilar',
	'kompresorler' => 'hirdavat/kompresorler', 'somun-sokme' => 'hirdavat/somun-sokme-aletleri', 'yuzey-gelistirme-diskleri' => 'hirdavat/yuzey-gelistirme-diskleri',
	'maket-bicagi-ve-civili-krose' => 'hirdavat/maket-bicagi', 'kulplar' => 'hirdavat/kulplar', 'menteseler' => 'hirdavat/menteseler', 'surguler' => 'hirdavat/surguler',
	'gonyeler' => 'hirdavat/gonyeler', 'askilar' => 'hirdavat/aski-sabitleme', 'vida-grubu' => 'hirdavat/vida-grubu', 'civata-grubu' => 'hirdavat/civata-grubu',
	'mandallar' => 'hirdavat/mandallar', 'pergola-ayaklari' => 'hirdavat/pergola-ayaklari', 'uclar' => 'hirdavat/matkap-uclari',
	'purmuz-gazli-aletler' => 'hirdavat/purmuzler', 'shingle-cati-kaplamalari' => 'hirdavat/shingle-cati', 'likit-membran-surme-yalitim' => 'hirdavat/likit-membran',
	'su-yalitim-membranlari' => 'hirdavat/su-yalitim-membranlari',
	// Menude olmayan eski kategori sayfalari.
	'ispm15' => 'ambalaj', 'tomruk' => 'kereste', 'kontraplak' => 'kereste/kontrplak-levha', 'osb-plaka' => 'kereste/osb-plaka-levha', 'lambiri' => 'kereste/ahsap-rabita',
);

$category_url = static function ( string $slug ) use ( $groups, $subs ): string {
	if ( isset( $groups[ $slug ] ) ) {
		return '/kategoriler/' . $groups[ $slug ] . '/';
	}

	return isset( $subs[ $slug ] ) ? '/kategoriler/' . $subs[ $slug ] . '/' : '';
};

// Havuz: kaynak adresi, kod ve ad dizinleri; sitede secili olanlar.
$selected = array_flip( nwcs_site_product_settings()['selected'] );
$by_url   = array();
$by_code  = array();
$by_title = array();

foreach ( nwcs_pool_products() as $id => $product ) {
	if ( ! isset( $selected[ $id ] ) ) {
		continue;
	}

	switch_to_blog( nwcs_pool_blog_id() );
	$source = (string) get_post_meta( $id, '_nwcs_source', true );
	restore_current_blog();

	// Detay metni olmayan urunun sayfasi yok (404): o urun kategori sayfasina yonlenir.
	if ( '' === trim( (string) $product['body'] ) ) {
		continue;
	}

	$path = '/urun/' . $product['slug'] . '/';

	if ( '' !== $source ) {
		$by_url[ $source ] = $path;
	}

	if ( '' !== $product['code'] ) {
		$by_code[ preg_replace( '/^W-/', '', strtoupper( $product['code'] ) ) ] = $path;
	}

	$title_key = str_replace( ' ', '', trim( (string) preg_replace( '/\bahsap\b/', '', $fold( preg_replace( '/^W-[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*\s+/u', '', $product['title'] ) ) ) ) );
	$by_title[ $title_key ] ??= $path;
}

$product_url = static function ( array $item ) use ( $by_url, $by_code, $by_title, $fold ): string {
	if ( isset( $by_url[ $item['url'] ] ) ) {
		return $by_url[ $item['url'] ];
	}

	$codes = array_merge( array( (string) ( $item['product_code'] ?? '' ) ), (array) ( $item['product_codes'] ?? array() ) );

	if ( preg_match( '/^([A-Z]{2,4}-[A-Z0-9]+(?:-[A-Z0-9]+)*)\s/u', (string) $item['name'], $m ) ) {
		$codes[] = $m[1];
	}

	foreach ( $codes as $code ) {
		$code = preg_replace( '/^W-/', '', strtoupper( trim( (string) $code ) ) );

		if ( '' !== $code && isset( $by_code[ $code ] ) ) {
			return $by_code[ $code ];
		}
	}

	$name = trim( (string) preg_replace( '/\s+\|\s+.*$/u', '', (string) $item['name'] ) );

	if ( mb_strlen( $name ) > 90 && preg_match( '/^(.{10,80}?)(?:,| Profesyonel | Model Serisi )/u', $name, $m ) ) {
		$name = trim( $m[1] );
	}

	$key = str_replace( ' ', '', trim( (string) preg_replace( '/\bahsap\b/', '', $fold( $name ) ) ) );

	return $by_title[ $key ] ?? '';
};

// Kaynakta ayni urunun ikinci kaydi: ilk kaydin yeni adresine gider.
$same_as = array(
	'catili-piknik-masasi-8-kisilik' => 'catili-ahsap-piknik-masasi-8-kisilik',
	'kpek-kulbesi-byk-verandal'      => 'ahsap-kopek-kulubesi-buyuk-verandali',
);

// Adi yeni yaziyla tutmayan eski blog adresleri (elle bakildi).
$blog_same_as = array(
	'/blog/ispm15-palet-uretimi' => '/ispm-15-palet-uretiminde-dikkat-edilecekler/',
);

$lines   = array( '# kocist.com.tr eski adresleri (build-kocist-redirects.php ile uretildi)' );
$missing = array();

// Sayfalar (ayni adreste olanlar listede yok: /iletisim, /katalog, /insan-kaynaklari, /banka-bilgilerimiz).
$lines[] = '';
$lines[] = '# Sayfalar';
$lines[] = '/hakkimizda              /kurumsal/';
$lines[] = '/referanslar             /kurumsal/';
$lines[] = '/urunlerimiz             /kategoriler/';

// Kategoriler.
$lines[] = '';
$lines[] = '# Kategoriler';
$category_slugs = array();

foreach ( $data['categories'] as $group ) {
	$category_slugs[ $group['slug'] ] = true;

	foreach ( $group['subcategories'] as $sub ) {
		$category_slugs[ $sub['slug'] ] = true;
	}
}

foreach ( (array) ( $data['other_category_pages'] ?? array() ) as $extra ) {
	$category_slugs[ $extra['slug'] ] = true;
}

foreach ( array_keys( $category_slugs ) as $slug ) {
	$target = $category_url( (string) $slug );

	if ( '' === $target ) {
		$missing[] = '/urunlerimiz/' . $slug;
		continue;
	}

	$lines[] = sprintf( '%-60s %s', '/urunlerimiz/' . $slug, $target );
}

// Urunler.
$lines[] = '';
$lines[] = '# Urunler (/urun/<eski>/<sira> ve /urunlerimiz/<eski>)';
$seen = array();

$item_by_slug = array_column( $data['products'], null, 'slug' );

foreach ( $data['products'] as $item ) {
	$slug   = (string) $item['slug'];
	$target = $product_url( $item );

	if ( '' === $target && isset( $same_as[ $slug ], $item_by_slug[ $same_as[ $slug ] ] ) ) {
		$target = $product_url( $item_by_slug[ $same_as[ $slug ] ] );
	}

	// Sayfasi olmayan (ortak, detay metni bos) urun: eski sitedeki kategorisi.
	if ( '' === $target ) {
		foreach ( (array) ( $item['category_paths'] ?? array() ) as $cat_path ) {
			foreach ( array_reverse( (array) ( $cat_path['slugs'] ?? array() ) ) as $cat_slug ) {
				$target = $category_url( (string) $cat_slug );

				if ( '' !== $target ) {
					break 2;
				}
			}
		}
	}

	if ( '' === $target ) {
		$missing[] = '/urun/' . $slug . '/';
		continue;
	}

	if ( isset( $seen[ $slug ] ) ) {
		continue;
	}

	$seen[ $slug ] = true;

	// Yeni adres eskiyle ayniysa (/urun/<slug>/) sira numarali eski adres yine yonlenir.
	$lines[] = sprintf( '%-60s %s', '/urun/' . $slug . '/*', $target );

	if ( ! isset( $category_slugs[ $slug ] ) ) {
		$lines[] = sprintf( '%-60s %s', '/urunlerimiz/' . $slug, $target );
	}
}

// Blog: eski yazi adresi -> baslik/adres benzerligi en yuksek yeni yazi.
$lines[] = '';
$lines[] = '# Blog';
$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1 ) );
$words = static function ( string $text ) use ( $fold ): array {
	$text = strtr( $fold( str_replace( '-', ' ', $text ) ), array( 'kontraplak' => 'kontrplak', 'ispm15' => 'ispm 15' ) );

	return array_values( array_diff( array_filter( explode( ' ', $text ) ), array( 've', 'ile', 'nedir', 'mi', 'ne' ) ) );
};

foreach ( file( dirname( __DIR__ ) . '/scripts/data/kocist-old-blog.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) ?: array() as $old ) {
	$old  = trim( $old );
	$want = $words( basename( $old ) );
	$best = array( 0.0, '' );

	foreach ( $posts as $post ) {
		$have  = $words( $post->post_name );
		$inter = count( array_intersect( $want, $have ) );
		$score = $inter / max( 1, count( array_unique( array_merge( $want, $have ) ) ) );

		if ( $score > $best[0] ) {
			$best = array( $score, wp_make_link_relative( get_permalink( $post ) ) );
		}
	}

	$target = $blog_same_as[ $old ] ?? ( $best[0] >= 0.5 ? preg_replace( '#^/kocist#', '', $best[1] ) : '/blog/' );
	$lines[] = sprintf( '%-60s %s', $old, $target );
}

echo implode( "\n", $lines ), "\n";

foreach ( $missing as $path ) {
	WP_CLI::warning( 'Karsiligi bulunamadi: ' . $path );
}
