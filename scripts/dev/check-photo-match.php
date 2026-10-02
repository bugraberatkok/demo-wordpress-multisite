<?php
/**
 * Yerel deneme: Fotograf Kutusu'nun dosya adi -> urun kodu eslemesi
 * (includes/photos.php). Sabit bir aday listesiyle calisir; veritabanina
 * dokunmaz. Sonda havuzun gercek kodlariyla birkac ornek de denenir.
 *
 *   docker compose exec -T wordpress php /scripts/dev/check-photo-match.php
 */

$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/';

require '/var/www/html/wp-load.php';

$fail = 0;
function check( string $label, bool $ok, string $got = '' ): void {
	global $fail;
	echo ( $ok ? 'OK   ' : 'FAIL ' ) . $label . ( $ok || '' === $got ? '' : '  (sonuç: ' . $got . ')' ) . "\n";
	$fail += $ok ? 0 : 1;
}

// Anahtar uretimi.
check( 'anahtar: uzanti atilir, buyuk harf', 'IMG-0001' === nwcs_photo_key( 'IMG_0001.JPG' ) );
check( 'anahtar: _ ve . tire olur', 'W-KAM-400DUB-1' === nwcs_photo_key( 'W_KAM_400DUB.1.jpg' ) );
check( 'anahtar: bosluk ve parantez', 'W-KAM-400DUB-2' === nwcs_photo_key( 'w_kam_400dub (2).JPEG' ) );
check( 'anahtar: Turkce harf ayrac olur', 'EZLONG-1' === nwcs_photo_key( 'Şezlong-1.jpg' ), nwcs_photo_key( 'Şezlong-1.jpg' ) );
check( 'kod anahtari uzanti atmaz', 'PAL-1-5' === nwcs_photo_code_key( 'pal-1.5' ) );

$candidates = array(
	'W-KAM-400DUB' => 563,
	'W-KAM-400'    => 900,
	'W-KAM-400-1'  => 901,
	'W-DOG-V06'    => 588,
	'URN-0006'     => 6,
	'W-SEZ-01'     => 700,
	'AB-12'        => -1, // iki urunde ayni anahtar
);

$cases = array(
	// ad => [ urun, sira, yol ]
	'W-KAM-400DUB-1.jpg'             => array( 563, 1, 'prefix' ),
	'w_kam_400dub (2).JPEG'          => array( 563, 2, 'prefix' ),
	'W-KAM-400DUB.webp'              => array( 563, PHP_INT_MAX, 'prefix' ),
	'woodpets-w-dog-v06-img010.webp' => array( 588, 10, 'contains' ),
	'W-KAM-400-1.png'                => array( 901, PHP_INT_MAX, 'prefix' ),
	'W-KAM-400-1-2.png'              => array( 901, 2, 'prefix' ),
	'W-KAM-400-3.png'                => array( 900, 3, 'prefix' ),
	'IMG_4412.jpg'                   => array( 0, PHP_INT_MAX, '' ),
	'URN-0006-3.jpg'                 => array( 6, 3, 'prefix' ),
	'Şezlong-1.jpg'                  => array( 0, PHP_INT_MAX, '' ),
	'W-KAM-400DUB-2-kopya.jpg'       => array( 563, 2, 'prefix' ),
	'kamelya-genel.jpg'              => array( 0, PHP_INT_MAX, '' ),
	'URN-00061.jpg'                  => array( 0, PHP_INT_MAX, '' ), // sinir: URN-0006 sonrasi rakam
	'x-urn-0006-1.jpg'               => array( 6, 1, 'contains' ),
	'AB-12-1.jpg'                    => array( -1, 1, 'prefix' ),
);

foreach ( $cases as $name => $want ) {
	$got = nwcs_photo_match( $name, $candidates );
	check(
		sprintf( '%-32s -> %s sira %s', $name, $want[0] ?: 'eşleşmez', PHP_INT_MAX === $want[1] ? 'sonda' : $want[1] ),
		$got['id'] === $want[0] && $got['seq'] === $want[1] && $got['via'] === $want[2],
		json_encode( $got )
	);
}

// Kisa kod icerme ile eslesmez (en az 5 karakter).
$short = nwcs_photo_match( 'foto-pp3-1.jpg', array( 'PP3' => 12 ) );
check( 'kisa kod (PP3) iceride eslesmez', 0 === $short['id'] );
check( 'kisa kod onekle eslesir', 12 === nwcs_photo_match( 'pp3-1.jpg', array( 'PP3' => 12 ) )['id'] );

// Havuzun gercek kodlari.
$real = nwcs_photo_candidates();
printf( "\nhavuz: %d kodlu urun, %d kodsuz\n", count( $real ), count( nwcs_pool_products() ) - count( $real ) );
foreach ( array( 'W-KAM-400DUB-1.jpg', 'woodpets-w-dog-v06-img010.webp', 'URN-0006-3.jpg', 'Şezlong-1.jpg' ) as $name ) {
	$got = nwcs_photo_match( $name, $real );
	printf( "     %-32s -> %s %s\n", $name, $got['code'] ?: '—', $got['id'] > 0 ? '#' . $got['id'] : '' );
}

echo $fail ? "\n$fail hata\n" : "\nHepsi gecti\n";
