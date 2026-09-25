<?php
/**
 * Sanayi Palet temasi.
 *
 * Gorunur metinler sabit yazilmaz; degerler Network Content Studio veri
 * katmanindan okunur. Eklenti yoksa yedek fonksiyonlar tema manifestinin
 * varsayilanlarini dondurur: site eklentisiz de ayni icerikle acilir, yalnizca
 * panelden duzenlenemez.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'nwcs_field' ) ) {
	function sanayi_palet_manifest_default( $page, $component, $field ) {
		static $manifest = null;

		if ( null === $manifest ) {
			$manifest = include get_theme_file_path( 'content-manifest.php' );
		}

		return $manifest['pages'][ $page ]['components'][ $component ]['fields'][ $field ]['default'] ?? '';
	}
	function nwcs_field( $page, $component, $field, $fallback = null ) {
		return null === $fallback ? sanayi_palet_manifest_default( $page, $component, $field ) : $fallback;
	}
	function nwcs_rows( $page, $component, $field ) {
		$rows = sanayi_palet_manifest_default( $page, $component, $field );

		return is_array( $rows ) ? array_values( $rows ) : array();
	}
	function nwcs_image( $page, $component, $field, $size = 'large' ) {
		return array( 'id' => 0, 'url' => '', 'alt' => '' );
	}
	function nwcs_image_by_id( $id, $size = 'large' ) {
		return array( 'id' => 0, 'url' => '', 'alt' => '' );
	}
	function nwcs_the_icon( $key, $class = '', $size = 24 ) {}
	function nwcs_edit_attr( $page, $component, $field = '', $row = null, $sub = '' ) {}
	function nwcs_section_order( $page = 'home' ) {
		$manifest = include get_theme_file_path( 'content-manifest.php' );

		return $manifest['pages'][ $page ]['sortable_sections'] ?? array();
	}
}

// Tema etkinlestirildiginde sayfalari, okuma ayarlarini ve blog yazilarini bir kez kurar.
require_once get_theme_file_path( 'inc/setup.php' );

add_action( 'after_setup_theme', 'sanayi_palet_setup' );
function sanayi_palet_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
}

add_action( 'wp_enqueue_scripts', 'sanayi_palet_assets' );
function sanayi_palet_assets(): void {
	// Surum = tema surumu + varliklarin en son degisim zamani: CSS/JS her
	// degistiginde adres degisir, tarayici ve onbellek eski dosyayi gostermez.
	$files   = array_merge( glob( get_theme_file_path( 'assets/*.{css,js}' ), GLOB_BRACE ) ?: array(), glob( get_theme_file_path( 'assets/*/*.{css,js}' ), GLOB_BRACE ) ?: array() );
	$version = wp_get_theme()->get( 'Version' ) . '.' . ( $files ? max( array_map( 'filemtime', $files ) ) : 0 );

	/*
	 * Ag geneli ortak font Archivo (baslik ve damga dar kesimde, bkz.
	 * tokens.css). Onceki: Big Shoulders, Big Shoulders Stencil, Instrument Sans.
	 * Surum null: URL'e ?ver eklenmesin, Google Fonts onbellegi bozulmasin.
	 */
	wp_enqueue_style(
		'sanayi-palet-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,300..900&display=swap',
		array(),
		null
	);

	// Renkler tek dosyada: tokens.css. Diger butun stiller ona baglidir.
	wp_enqueue_style( 'sanayi-palet-tokens', get_theme_file_uri( 'assets/css/tokens.css' ), array( 'sanayi-palet-fonts' ), $version );
	wp_enqueue_style( 'sanayi-palet-style', get_stylesheet_uri(), array( 'sanayi-palet-tokens' ), $version );
	wp_enqueue_style( 'sanayi-palet-base', get_theme_file_uri( 'assets/css/base.css' ), array( 'sanayi-palet-style' ), $version );
	wp_enqueue_style( 'sanayi-palet-header', get_theme_file_uri( 'assets/css/header.css' ), array( 'sanayi-palet-base' ), $version );
	wp_enqueue_style( 'sanayi-palet-footer', get_theme_file_uri( 'assets/css/footer.css' ), array( 'sanayi-palet-base' ), $version );

	wp_enqueue_script( 'sanayi-palet-nav', get_theme_file_uri( 'assets/js/nav.js' ), array(), $version, true );

	// Damga (hero.css) ana sayfada ve Hakkimizda'da kullanilir.
	if ( is_front_page() || is_page( 'hakkimizda' ) ) {
		wp_enqueue_style( 'sanayi-palet-hero', get_theme_file_uri( 'assets/css/hero.css' ), array( 'sanayi-palet-base' ), $version );
	}

	// Ana sayfa bolumleri; teklif seridi ve blog kartlari ic sayfalarda da kullanilir.
	wp_enqueue_style( 'sanayi-palet-home', get_theme_file_uri( 'assets/css/home.css' ), array( 'sanayi-palet-base' ), $version );

	// Ic sayfalar: Hakkimizda, Iletisim, Blog listesi, yazi.
	if ( ! is_front_page() ) {
		wp_enqueue_style( 'sanayi-palet-pages', get_theme_file_uri( 'assets/css/pages.css' ), array( 'sanayi-palet-home' ), $version );
	}
}

/**
 * Bos satirla ayrilmis metni paragraflara boler.
 */
function sanayi_palet_paragraphs( string $text, string $class = '' ): void {
	$blocks = preg_split( '/\R\s*\R/u', trim( $text ) );

	foreach ( $blocks as $block ) {
		$block = trim( $block );

		if ( '' === $block ) {
			continue;
		}

		printf( '<p%s>%s</p>', $class ? ' class="' . esc_attr( $class ) . '"' : '', nl2br( esc_html( $block ) ) );
	}
}

/**
 * Blog yazisinin kapak gorseli; yoksa bos dizi.
 */
function sanayi_palet_post_image( WP_Post $post, string $size = 'medium_large' ): array {
	$id = (int) get_post_thumbnail_id( $post );

	if ( ! $id ) {
		return array();
	}

	$src = wp_get_attachment_image_src( $id, $size );

	if ( ! $src ) {
		return array();
	}

	return array(
		'url' => $src[0],
		'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
	);
}

/* ======================================================================
 * Iletisim formu
 *
 * Sahte basari ekrani yok: gonderilen mesaj gercekten kaydedilir, yonetimde
 * "İletişim Mesajları" altinda gorulur. Demoda e-posta gonderilmez.
 * ====================================================================== */

add_action( 'init', 'sanayi_palet_register_message_cpt' );
function sanayi_palet_register_message_cpt(): void {
	register_post_type(
		'sp_message',
		array(
			'labels'          => array(
				'name'          => 'İletişim Mesajları',
				'singular_name' => 'İletişim Mesajı',
				'menu_name'     => 'İletişim Mesajları',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor', 'custom-fields' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}

add_action( 'admin_post_sanayi_palet_message', 'sanayi_palet_handle_message' );
add_action( 'admin_post_nopriv_sanayi_palet_message', 'sanayi_palet_handle_message' );
function sanayi_palet_handle_message(): void {
	$redirect = home_url( '/iletisim/' );

	// Bot tuzagi: gorunmeyen alan dolmussa sessizce geri don.
	if ( ! empty( $_POST['sp_website'] ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	$values = array(
		'name'    => sanitize_text_field( wp_unslash( $_POST['sp_name'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $_POST['sp_email'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $_POST['sp_phone'] ?? '' ) ),
		'subject' => sanitize_text_field( wp_unslash( $_POST['sp_subject'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['sp_message'] ?? '' ) ),
	);

	$errors = array();

	if ( ! isset( $_POST['sp_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['sp_nonce'] ), 'sanayi_palet_message' ) ) {
		$errors['form'] = 'Formun süresi dolmuş. Sayfayı yenileyip tekrar gönderin.';
	}

	if ( '' === $values['name'] ) {
		$errors['name'] = 'Adınızı ve soyadınızı yazın.';
	}

	if ( '' === $values['email'] || ! is_email( $values['email'] ) ) {
		$errors['email'] = 'Geçerli bir e-posta adresi yazın; size buradan dönüyoruz.';
	}

	if ( '' === $values['message'] ) {
		$errors['message'] = 'Mesajınızı yazın.';
	}

	// Kucuk harf onaltilik: sanitize_key() ile okununca degismesin.
	$token = bin2hex( random_bytes( 8 ) );
	$back  = add_query_arg( 'mesaj', $token, $redirect ) . '#form';

	if ( ! $errors ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'sp_message',
				'post_status'  => 'private',
				'post_title'   => sprintf( '%s — %s', $values['name'], $values['subject'] ?: 'Konu yok' ),
				'post_content' => sprintf(
					"E-posta: %s\nTelefon: %s\nKonu: %s\n\n%s",
					$values['email'],
					$values['phone'] ?: '—',
					$values['subject'] ?: '—',
					$values['message']
				),
				'meta_input'   => array(
					'_sp_name'    => $values['name'],
					'_sp_email'   => $values['email'],
					'_sp_phone'   => $values['phone'],
					'_sp_subject' => $values['subject'],
				),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			$errors['form'] = 'Mesaj kaydedilemedi. Lütfen telefonla ya da WhatsApp’tan ulaşın.';
		}
	}

	$state = $errors ? array( 'errors' => $errors, 'values' => $values ) : array( 'success' => true );

	set_transient( 'sanayi_palet_msg_' . $token, $state, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( $back );
	exit;
}

/**
 * Yonlendirmeden sonra formun durumu (hatalar, girilen degerler, basari).
 */
function sanayi_palet_message_state(): array {
	$empty = array( 'errors' => array(), 'values' => array(), 'success' => false );
	$token = isset( $_GET['mesaj'] ) ? sanitize_key( wp_unslash( $_GET['mesaj'] ) ) : '';

	if ( '' === $token ) {
		return $empty;
	}

	$state = get_transient( 'sanayi_palet_msg_' . $token );

	if ( ! is_array( $state ) ) {
		return $empty;
	}

	return array(
		'errors'  => $state['errors'] ?? array(),
		'values'  => $state['values'] ?? array(),
		'success' => ! empty( $state['success'] ),
	);
}

/**
 * Ikon basar: Phosphor (MIT, assets/icons/LICENSE), kalin agirlik.
 *
 * Paneldeki ikon alanlari eklentinin sabit 16 anahtarini kullanir; tema bu
 * anahtarlari ayni anlamdaki Phosphor cizimine esler. Boylece ikonlar panelden
 * secilebilir kalir, eklentiye dokunulmaz. Dosyalar temanin kendi klasorunde
 * durur, disaridan yuklenmez.
 */
function sanayi_palet_icon( string $key, int $size = 24, string $class = 'sp-icon' ): void {
	static $cache = array();

	$map = array(
		'truck'    => 'truck',
		'box'      => 'package',
		'factory'  => 'factory',
		'tools'    => 'wrench',
		'tree'     => 'tree',
		'recycle'  => 'recycle',
		'shield'   => 'shield-check',
		'ruler'    => 'ruler',
		'clock'    => 'clock',
		'phone'    => 'phone',
		'whatsapp' => 'whatsapp-logo',
		'mail'     => 'envelope-simple',
		'pin'      => 'map-pin',
		'check'    => 'check-circle',
		'star'     => 'star',
		'arrow'    => 'arrow-right',
	);

	$key = sanitize_key( $key );

	if ( ! isset( $map[ $key ] ) ) {
		return;
	}

	if ( ! isset( $cache[ $key ] ) ) {
		$file          = get_theme_file_path( 'assets/icons/' . $map[ $key ] . '.svg' );
		$cache[ $key ] = file_exists( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	if ( '' === $cache[ $key ] ) {
		return;
	}

	echo str_replace( // phpcs:ignore WordPress.Security.EscapingOutput -- temanin kendi sabit SVG dosyalari.
		'<svg ',
		sprintf( '<svg class="%s" width="%d" height="%d" aria-hidden="true" focusable="false" ', esc_attr( $class ), $size, $size ),
		$cache[ $key ]
	);
}

/**
 * Paletin ustten gorunusunu olcekli SVG olarak basar.
 *
 * Butun cizimler ayni olcekte (1 birim = 1 cm, sabit viewBox): 80x120 Euro ile
 * 100x120 kapali palet yan yana bakinca gercek farki gosterir. Olcu girilmemisse
 * paletin genel bicimi cizilir ama olcu cizgisi basilmaz; olcu uydurulmaz.
 * Acik tablada tahtalar arasi bosluk vardir, kapali tablada tahtalar bitisik.
 */
function sanayi_palet_pallet_drawing( string $length, string $width, string $deck, string $label ): void {
	$l = (float) str_replace( ',', '.', $length );
	$w = (float) str_replace( ',', '.', $width );

	$known = $l > 0 && $w > 0 && $l <= 200 && $w <= 200;

	// Olcu yoksa genel bicim: dogru olcek iddiasi tasimaz, olcu cizgisi de yok.
	$dl = $known ? $l : 120.0;
	$dw = $known ? $w : 90.0;

	$x0     = 14.0;
	$y0     = 24.0;
	$closed = str_starts_with( mb_strtolower( trim( $deck ) ), 'kapal' );
	$boards = $closed ? 9 : 5;
	$board  = $closed ? $dw / $boards : $dw * 0.145;
	$gap    = $closed ? 0 : ( $dw - $boards * $board ) / ( $boards - 1 );

	$title = $known
		? sprintf( '%s, üstten görünüş: %s × %s cm', $label, $width, $length )
		: sprintf( '%s, üstten görünüş (ölçü belirtilmemiş)', $label );
	?>
	<svg class="sp-draw<?php echo $known ? '' : ' sp-draw--generic'; ?>" viewBox="0 0 160 150" role="img" aria-label="<?php echo esc_attr( $title ); ?>" focusable="false">
		<?php // Acik tablada tahta aralarindan alttaki uc kiris gorunur; once onlar cizilir. ?>
		<?php if ( ! $closed ) : ?>
			<?php foreach ( array( 0.0, 0.5, 1.0 ) as $pos ) : ?>
				<rect class="sp-draw__block" x="<?php echo esc_attr( (string) round( $x0 + $pos * ( $dl - 10 ), 2 ) ); ?>" y="<?php echo esc_attr( (string) $y0 ); ?>" width="10" height="<?php echo esc_attr( (string) $dw ); ?>" />
			<?php endforeach; ?>
		<?php endif; ?>

		<?php for ( $i = 0; $i < $boards; $i++ ) : ?>
			<rect class="sp-draw__board" x="<?php echo esc_attr( (string) $x0 ); ?>" y="<?php echo esc_attr( (string) round( $y0 + $i * ( $board + $gap ), 2 ) ); ?>" width="<?php echo esc_attr( (string) $dl ); ?>" height="<?php echo esc_attr( (string) round( $board, 2 ) ); ?>" />
		<?php endfor; ?>

		<?php if ( $known ) : ?>
			<g class="sp-draw__dim">
				<?php // Uzunluk: ustte. ?>
				<line x1="<?php echo esc_attr( (string) $x0 ); ?>" y1="12" x2="<?php echo esc_attr( (string) ( $x0 + $dl ) ); ?>" y2="12" />
				<line x1="<?php echo esc_attr( (string) $x0 ); ?>" y1="7" x2="<?php echo esc_attr( (string) $x0 ); ?>" y2="17" />
				<line x1="<?php echo esc_attr( (string) ( $x0 + $dl ) ); ?>" y1="7" x2="<?php echo esc_attr( (string) ( $x0 + $dl ) ); ?>" y2="17" />
				<?php // Genislik: sagda. ?>
				<line x1="<?php echo esc_attr( (string) ( $x0 + $dl + 10 ) ); ?>" y1="<?php echo esc_attr( (string) $y0 ); ?>" x2="<?php echo esc_attr( (string) ( $x0 + $dl + 10 ) ); ?>" y2="<?php echo esc_attr( (string) ( $y0 + $dw ) ); ?>" />
				<line x1="<?php echo esc_attr( (string) ( $x0 + $dl + 5 ) ); ?>" y1="<?php echo esc_attr( (string) $y0 ); ?>" x2="<?php echo esc_attr( (string) ( $x0 + $dl + 15 ) ); ?>" y2="<?php echo esc_attr( (string) $y0 ); ?>" />
				<line x1="<?php echo esc_attr( (string) ( $x0 + $dl + 5 ) ); ?>" y1="<?php echo esc_attr( (string) ( $y0 + $dw ) ); ?>" x2="<?php echo esc_attr( (string) ( $x0 + $dl + 15 ) ); ?>" y2="<?php echo esc_attr( (string) ( $y0 + $dw ) ); ?>" />
			</g>
			<text class="sp-draw__label" x="<?php echo esc_attr( (string) ( $x0 + $dl / 2 ) ); ?>" y="9" text-anchor="middle"><?php echo esc_html( $length ); ?> cm</text>
			<text class="sp-draw__label" x="<?php echo esc_attr( (string) ( $x0 + $dl + 18 ) ); ?>" y="<?php echo esc_attr( (string) ( $y0 + $dw / 2 ) ); ?>" text-anchor="middle" dominant-baseline="middle" transform="rotate(90 <?php echo esc_attr( (string) ( $x0 + $dl + 18 ) ); ?> <?php echo esc_attr( (string) ( $y0 + $dw / 2 ) ); ?>)"><?php echo esc_html( $width ); ?> cm</text>
		<?php endif; ?>
	</svg>
	<?php
}

/**
 * "Ad: deger" satirlarini ciftlere boler. Iki nokta yoksa satir deger olur.
 */
function sanayi_palet_spec_rows( string $text ): array {
	$rows = array();

	foreach ( preg_split( '/\R/u', trim( $text ) ) as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			continue;
		}

		$parts  = explode( ':', $line, 2 );
		$rows[] = isset( $parts[1] )
			? array( 'label' => trim( $parts[0] ), 'value' => trim( $parts[1] ) )
			: array( 'label' => '', 'value' => $line );
	}

	return $rows;
}

/**
 * Her satiri bir madde olan metni diziye cevirir.
 */
function sanayi_palet_lines( string $text ): array {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', trim( $text ) ) ) ) );
}

/**
 * Iletisim formuna konu doldurularak giden teklif baglantisi.
 */
function sanayi_palet_quote_url( string $subject ): string {
	return add_query_arg( 'konu', rawurlencode( $subject ), home_url( '/iletisim/' ) ) . '#form';
}

/**
 * Yazi tarihini Turkce basar ("19 Nisan 2022").
 *
 * Demo sitelerinin dili Ingilizce kurulu; Turkce dil paketi indirilmeden
 * ay adlari dogru ciksin diye elle cevrilir.
 */
function sanayi_palet_date( WP_Post $post ): string {
	$months = array( 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık' );
	$time   = get_post_timestamp( $post );

	if ( ! $time ) {
		return '';
	}

	return wp_date( 'j', $time ) . ' ' . $months[ (int) wp_date( 'n', $time ) - 1 ] . ' ' . wp_date( 'Y', $time );
}

/**
 * Yakik damgayi basar: cerceve, isaret numarasi, ayirici, standart.
 *
 * Sitede iki yerde kullanilir (hero ve belge bolumu); ikisi de ayni
 * alanlardan (home.hero.stamp_*) beslenir.
 */
function sanayi_palet_stamp( string $code, string $standard, string $modifier = '' ): void {
	?>
	<div class="sp-stamp <?php echo esc_attr( $modifier ); ?>">
		<div class="sp-stamp__mark">
			<span class="sp-stamp__code" <?php nwcs_edit_attr( 'home', 'hero', 'stamp_code' ); ?>><?php echo esc_html( $code ); ?></span>
			<span class="sp-stamp__rule" aria-hidden="true"></span>
			<span class="sp-stamp__standard" <?php nwcs_edit_attr( 'home', 'hero', 'stamp_standard' ); ?>><?php echo esc_html( $standard ); ?></span>
		</div>
	</div>
	<?php
}

/**
 * Menu ogesi su an goruntulenen sayfayi mi isaret ediyor?
 *
 * Yalnizca sayfa yolu karsilastirilir; capa ve dis baglantilar hic aktif sayilmaz.
 * Blog yazisi acikken Blog ogesi de aktif sayilir.
 */
function sanayi_palet_is_current( string $url ): bool {
	$url = trim( $url );

	if ( '' === $url || str_starts_with( $url, '#' ) ) {
		return false;
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );

	if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
		return false;
	}

	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );

	// Alt dizin kurulumunda /sanayi-palet/ onekini at; iki taraf da siteye goreli olsun.
	$relative = static function ( string $path ) use ( $home ): string {
		$path = trim( $path, '/' );

		if ( '' !== $home && ( $path === $home || str_starts_with( $path, $home . '/' ) ) ) {
			$path = trim( substr( $path, strlen( $home ) ), '/' );
		}

		return $path;
	};

	$path    = $relative( (string) wp_parse_url( sanayi_palet_link( $url ), PHP_URL_PATH ) );
	$current = $relative( (string) wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ) );

	if ( '' === $path ) {
		return is_front_page();
	}

	if ( 'blog' === $path && is_singular( 'post' ) ) {
		return true;
	}

	return $path === $current;
}

/**
 * Ana sayfa bolumunu kayitli siraya gore basar.
 */
function sanayi_palet_section( string $key ): void {
	$file = get_theme_file_path( "template-parts/sections/{$key}.php" );

	if ( file_exists( $file ) ) {
		include $file;
	}
}

/**
 * nwcs_edit_attr() ciktisini metin olarak dondurur (dizi icinde kurulan
 * HTML icin). Onizleme disinda bos metindir.
 */
function sanayi_palet_edit_attr( string $page, string $component, string $field = '', ?int $row = null, string $sub = '' ): string {
	if ( ! function_exists( 'nwcs_edit_attr' ) ) {
		return '';
	}

	ob_start();
	nwcs_edit_attr( $page, $component, $field, $row, $sub );

	return (string) ob_get_clean();
}

/**
 * Blog yazisinin (ya da WordPress sayfasinin) basligi, tarihi, ozeti, metni:
 * onizlemede tiklaninca yazinin duzenleme ekrani acilir.
 */
function sanayi_palet_post_attr( int $post_id, string $label = 'Yazı başlığı' ): void {
	if ( function_exists( 'nwcs_post_attr' ) ) {
		nwcs_post_attr( $post_id, $label );
	}
}

/**
 * Panelden gelen baglantiyi adrese cevirir; cikti her zaman esc_url ile basilir.
 *
 * Bos deger '#' olur. '/urunler/' gibi kok-goreli yollar sitenin kendi
 * adresine baglanir: site alt dizinde (/sanayi-palet/) calistigi icin
 * dogrudan basilsa ag kokune giderdi.
 */
function sanayi_palet_link( $url ): string {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( str_starts_with( $url, '/' ) && ! str_starts_with( $url, '//' ) ) {
		return home_url( $url );
	}

	return $url;
}

/**
 * Panel gorseli yoksa temanin kendi gorseli (assets/img).
 *
 * Panelden secilen gorsel her zaman kazanir. Alan bossa (yeni kurulan sitede
 * oldugu gibi) temanin icindeki gercek fotograf gosterilir; site seed'siz de
 * eksiksiz gorunur.
 */
function sanayi_palet_image_or_default( array $image, string $file, string $alt = '' ): array {
	if ( ! empty( $image['url'] ) || '' === $file || ! file_exists( get_theme_file_path( 'assets/img/' . $file ) ) ) {
		return $image;
	}

	return array(
		'id'  => 0,
		'url' => get_theme_file_uri( 'assets/img/' . $file ),
		'alt' => '' !== ( $image['alt'] ?? '' ) ? $image['alt'] : $alt,
	);
}

/**
 * Temanin yedek gorselleri ve alt metinleri; sablonlar ve kurulum ayni listeyi kullanir.
 */
function sanayi_palet_default_images(): array {
	return array(
		'hero'        => array( 'palet-duvari.jpg', 'Üst üste istiflenmiş, farklı renklerde yüzlerce ahşap palet' ),
		'certificate' => array( 'ispm15-belgesi.jpg', 'Tarım ve Orman Bakanlığı Ahşap Ambalaj Malzemesi İşaretleme İzin Belgesi, belge no 1080, işaret TR-1080-HT' ),
		'group'       => array( 'atolye.jpg', 'Koçist atölyesinde ölçüye göre üretilen ahşap sandık' ),
		'about_head'  => array( 'forklift-saha.jpg', 'Yığılmış ahşap paletlerin arasında forklift' ),
		'range_0'     => array( 'euro-palet-istif.jpg', 'EUR damgalı euro paletlerin yakından görünümü' ),
		'range_1'     => array( 'ahsap-sandik.jpg', 'Sevkiyata hazır, kapalı ahşap sandık' ),
		'range_2'     => array( 'ahsap-kafes.jpg', 'Ölçüye göre üretilmiş açık gövdeli ahşap kafes' ),
	);
}

/**
 * Anahtara gore panel gorseli ya da temanin yedegi.
 */
function sanayi_palet_image( array $image, string $key ): array {
	$defaults = sanayi_palet_default_images();

	if ( ! isset( $defaults[ $key ] ) ) {
		return $image;
	}

	return sanayi_palet_image_or_default( $image, $defaults[ $key ][0], $defaults[ $key ][1] );
}

/**
 * Gorsel alani icin img etiketi; deger yoksa isaretli yer tutucu.
 */
function sanayi_palet_image_tag( array $image, string $class = '', string $placeholder = 'Örnek görsel' ): string {
	if ( ! empty( $image['url'] ) ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="lazy" decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( $image['alt'] ),
			esc_attr( $class )
		);
	}

	return sprintf(
		'<div class="sp-placeholder %1$s" role="img" aria-label="%2$s">%2$s</div>',
		esc_attr( $class ),
		esc_html( $placeholder )
	);
}

/**
 * SEO ve GEO: gorseller tema icinde oldugundan, gorseli olmayan sayfalar
 * paylasilinca ve arama sonucunda hero fotografi gorunur.
 */
add_filter(
	'nwcs_seo_default_image',
	static fn() => function_exists( 'nwcs_seo_theme_file_image' ) ? nwcs_seo_theme_file_image( 'assets/img/palet-duvari.jpg', 'Üst üste istiflenmiş, farklı renklerde yüzlerce ahşap palet' ) : 0
);

/**
 * Favicon: SVG (modern tarayicilar), 32px PNG yedegi ve iOS icin 180px ikon.
 * Panelde (Ozellestir > Site Kimligi) site ikonu secilirse WordPress'inki
 * gecerli olur; tema kendi ikonunu basmaz.
 */
add_action( 'wp_head', 'sanayi_favicon', 2 );
add_action( 'login_head', 'sanayi_favicon' );
function sanayi_favicon(): void {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" type="image/png" sizes="32x32" href="%s" />' . "\n" .
		'<link rel="icon" type="image/svg+xml" href="%s" />' . "\n" .
		'<link rel="apple-touch-icon" href="%s" />' . "\n",
		esc_url( get_theme_file_uri( 'assets/favicon-32.png' ) ),
		esc_url( get_theme_file_uri( 'assets/favicon.svg' ) ),
		esc_url( get_theme_file_uri( 'assets/apple-touch-icon.png' ) )
	);
}
