<?php
/**
 * Gorsel yer tutucu modu.
 *
 * Acik oldugu sitede sayfa ciktisindaki her fotograf ayni boyutta bir
 * "Gorsel gelecek" kutusuyla degistirilir. Fotograflar silinmez; mod
 * kapatilinca hepsi geri gelir. Her kutu farkli: desen ve aci gorselin
 * adresinden turetilir (ayni gorsel her yerde ayni kutu olur).
 *
 * Dokunulmayanlar: logolar (sinif ya da dosya adinda "logo"), SVG ve 64
 * pikselden kucuk simgeler, yonetim ekranlari, beslemeler, REST. Paylasim
 * onizlemesi gorseli (og:image) degismez; ziyaretci onu sayfada gormez.
 *
 * Ac/kapa: Ag Yonetimi -> Icerik Studyosu -> Gorsel Yer Tutucu (site basina).
 */

defined( 'ABSPATH' ) || exit;

const NWCS_PLACEHOLDER_OPTION = 'nwcs_image_placeholders';

function nwcs_placeholders_enabled( ?int $blog_id = null ): bool {
	$value = null === $blog_id ? get_option( NWCS_PLACEHOLDER_OPTION, '0' ) : get_blog_option( $blog_id, NWCS_PLACEHOLDER_OPTION, '0' );

	return '1' === (string) $value;
}

add_action( 'template_redirect', 'nwcs_placeholders_start', 999 );
function nwcs_placeholders_start(): void {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! nwcs_placeholders_enabled() ) {
		return;
	}

	ob_start( 'nwcs_placeholders_filter_html' );
}

add_action( 'wp_head', 'nwcs_placeholders_css', 99 );
function nwcs_placeholders_css(): void {
	if ( is_admin() || ! nwcs_placeholders_enabled() ) {
		return;
	}
	?>
	<style id="nwcs-placeholders">
		/* Etiket sag altta: arka plan gorsellerinde (hero) basliklarla cakismaz. */
		.nwcs-ph{display:flex;align-items:flex-end;justify-content:flex-end;padding:.75rem;box-sizing:border-box;max-width:100%;overflow:hidden;background-color:#e7eae8;color:#55605a;--ph-line:rgba(40,50,45,.09)}
		.nwcs-ph[data-ph-w]{width:var(--ph-w)}
		.nwcs-ph__t{padding:.35rem .7rem;border-radius:3px;background:rgba(255,255,255,.82);font:600 .8125rem/1.2 system-ui,-apple-system,"Segoe UI",sans-serif;letter-spacing:.01em;white-space:nowrap}
		.nwcs-ph--p0{background-image:repeating-linear-gradient(var(--ph-a),var(--ph-line) 0 2px,transparent 2px 14px)}
		.nwcs-ph--p1{background-image:radial-gradient(var(--ph-line) 1.6px,transparent 1.8px);background-size:14px 14px}
		.nwcs-ph--p2{background-image:linear-gradient(var(--ph-line) 1px,transparent 1px),linear-gradient(90deg,var(--ph-line) 1px,transparent 1px);background-size:22px 22px}
		.nwcs-ph--p3{background-image:repeating-linear-gradient(var(--ph-a),var(--ph-line) 0 1px,transparent 1px 8px),repeating-linear-gradient(calc(var(--ph-a) + 90deg),var(--ph-line) 0 1px,transparent 1px 8px)}
		.nwcs-ph--p4{background-image:repeating-linear-gradient(var(--ph-a),var(--ph-line) 0 10px,transparent 10px 20px)}
		.nwcs-ph--p5{background-image:repeating-radial-gradient(circle at 30% 40%,var(--ph-line) 0 2px,transparent 2px 16px)}
		.nwcs-ph--t1{background-color:#e9e6df}
		.nwcs-ph--t2{background-color:#e3e8ee}
		.nwcs-ph--t3{background-color:#e6ebe2}
	</style>
	<?php
}

/**
 * Gorsel adresinden kararli bir cesit: desen (6), aci (4), ton (4).
 *
 * @return array{pattern:int, angle:int, tone:int}
 */
function nwcs_placeholder_variant( string $key ): array {
	$hash = crc32( $key );

	return array(
		'pattern' => $hash % 6,
		'angle'   => array( 45, -45, 0, 90 )[ intdiv( $hash, 6 ) % 4 ],
		'tone'    => intdiv( $hash, 24 ) % 4,
	);
}

/**
 * Tek bir img etiketi icin yer tutucu. Degistirilmeyecekse null.
 */
function nwcs_placeholder_for_img( string $tag ): ?string {
	$attr = static function ( string $name ) use ( $tag ): string {
		if ( ! preg_match( '/\s' . $name . '\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $m ) ) {
			return '';
		}

		// Cift tirnakta deger 2. grupta, tek tirnakta 3. grupta.
		$value = '"' === $m[1][0] ? $m[2] : ( $m[3] ?? '' );

		return html_entity_decode( $value, ENT_QUOTES );
	};

	$src   = $attr( 'src' );
	$class = $attr( 'class' );
	$w     = (int) $attr( 'width' );
	$h     = (int) $attr( 'height' );

	if ( '' === $src || str_starts_with( $src, 'data:' ) || preg_match( '/\.svg(\?|$)/i', $src )
		|| preg_match( '/logo/i', $class . ' ' . basename( (string) wp_parse_url( $src, PHP_URL_PATH ) ) )
		|| ( $w && $h && $w <= 64 && $h <= 64 ) ) {
		return null;
	}

	$variant = nwcs_placeholder_variant( preg_replace( '/-\d+x\d+(?=\.\w+$)/', '', (string) wp_parse_url( $src, PHP_URL_PATH ) ) );
	$style   = $attr( 'style' );
	$css     = '--ph-a:' . $variant['angle'] . 'deg;';

	if ( $w && $h ) {
		$css .= 'aspect-ratio:' . $w . ' / ' . $h . ';--ph-w:' . $w . 'px;';
	} elseif ( ! preg_match( '/(^|\s)(w-full|h-full)(\s|$)/', $class ) ) {
		$css .= 'aspect-ratio:4 / 3;';
	}

	$alt = trim( $attr( 'alt' ) );

	return sprintf(
		'<span class="nwcs-ph nwcs-ph--p%1$d nwcs-ph--t%2$d %3$s" role="img" aria-label="%4$s" style="%5$s"%6$s><span class="nwcs-ph__t">Görsel gelecek</span></span>',
		$variant['pattern'],
		$variant['tone'],
		esc_attr( $class ),
		esc_attr( '' !== $alt ? 'Görsel gelecek: ' . $alt : 'Görsel gelecek' ),
		esc_attr( $css . $style ),
		$w && $h ? ' data-ph-w' : ''
	);
}

/**
 * Sayfa ciktisi: img etiketleri yer tutucuya, <picture> icindeki <source>
 * etiketleri kaldirilir, satir ici background-image adresleri desene doner.
 */
function nwcs_placeholders_filter_html( string $html ): string {
	if ( '' === $html || false === stripos( $html, '<html' ) ) {
		return $html;
	}

	// Paylasim onizlemesi ve yapilandirilmis veri <head> icinde: dokunulmaz.
	$split = stripos( $html, '<body' );

	if ( false === $split ) {
		return $html;
	}

	$head = substr( $html, 0, $split );
	$body = substr( $html, $split );

	$body = (string) preg_replace( '#<source\b[^>]*\bsrcset=[^>]*>#i', '', $body );

	// Temalarin "Ornek gorsel" notu: gorsel gosterilmedigi icin anlamsiz ve
	// yer tutucu etiketinin ustune biner. Yalnizca bu metni tasiyan tek oge.
	$body = (string) preg_replace( '#<(span|p|div)\b[^>]*>\s*(?:Örnek|Ornek) görsel\s*</\1>#iu', '', $body );

	$body = (string) preg_replace_callback(
		'#<img\b[^>]*>#i',
		static fn( array $m ): string => nwcs_placeholder_for_img( $m[0] ) ?? $m[0],
		$body
	);

	// Satir ici arka plan fotograflari (hero bantlari): ayni desen dili.
	$body = (string) preg_replace_callback(
		'#background-image\s*:\s*url\(\s*([\'"]?)([^\'")]+)\1\s*\)#i',
		static function ( array $m ): string {
			if ( preg_match( '/\.svg(\?|$)|logo/i', $m[2] ) ) {
				return $m[0];
			}

			$angle = nwcs_placeholder_variant( $m[2] )['angle'];

			return 'background-image:repeating-linear-gradient(' . $angle . 'deg,rgba(40,50,45,.09) 0 2px,transparent 2px 14px);background-color:#e7eae8';
		},
		$body
	);

	return $head . $body;
}
