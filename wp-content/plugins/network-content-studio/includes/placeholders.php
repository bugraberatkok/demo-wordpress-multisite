<?php
/**
 * Gorsel yer tutucu modu.
 *
 * Acik oldugu sitede sayfa ciktisindaki her fotograf ayni boyutta bir
 * "Gorsel gelecek" kutusuyla degistirilir. Fotograflar silinmez; mod
 * kapatilinca hepsi geri gelir. Her kutu farkli: desen ve aci gorselin
 * adresinden turetilir (ayni gorsel her yerde ayni kutu olur).
 *
 * Kapsam (yalnizca <body>):
 *   - <img> etiketleri yer tutucuya, <picture> icindeki <source> kaldirilir;
 *   - satir ici ve <style> icindeki arka plan fotograflari (url(...)) desene;
 *   - buyutme penceresi verisindeki (data-full, data-gallery, baglantilar)
 *     fotograf adresleri "Gorsel gelecek" SVG'sine: buyutunce de fotograf acilmaz;
 *   - temalarin "Ornek gorsel" notu kaldirilir.
 * Dokunulmayanlar: <header> ve <footer> icindeki ilk gorsel (logo), adinda
 * "logo" gecen dosyalar, SVG ve 64 pikselden kucuk simgeler; <head> (og:image,
 * JSON-LD); yonetim, besleme, REST. Herhangi bir adim basarisiz olursa sayfa
 * hic degistirilmeden gonderilir.
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
		/* :where -> temanin sinif kurallari (hidden, md:block...) kazanir. Etiket
		   sol ustte: urun etiketleri ve Buyut dugmeleri alt kosede durur. */
		:where(.nwcs-ph){display:flex;align-items:flex-start;justify-content:flex-start;padding:.75rem;box-sizing:border-box;max-width:100%;overflow:hidden;container-type:inline-size}
		.nwcs-ph{background-color:#e7eae8;color:#55605a;--ph-line:rgba(40,50,45,.09)}
		.nwcs-ph[data-ph-w]{width:var(--ph-w)}
		.nwcs-ph__t{max-width:100%;overflow:hidden;text-overflow:ellipsis;padding:.35rem .7rem;border-radius:3px;background:rgba(255,255,255,.85);font:600 .8125rem/1.2 system-ui,-apple-system,"Segoe UI",sans-serif;letter-spacing:.01em;white-space:nowrap}
		@container (max-width:170px){.nwcs-ph__t{display:none}}
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
 * crc32 32 bit PHP'de negatif olabilir: isaretsiz okunur.
 *
 * @return array{pattern:int, angle:int, tone:int}
 */
function nwcs_placeholder_variant( string $key ): array {
	$hash = (int) sprintf( '%u', crc32( $key ) );

	return array(
		'pattern' => $hash % 6,
		'angle'   => array( 45, -45, 0, 90 )[ intdiv( $hash, 6 ) % 4 ],
		'tone'    => intdiv( $hash, 24 ) % 4,
	);
}

/**
 * Etiketin bir niteligi (tirnakli). Yalnizca bosluktan sonra gelen ad aranir.
 */
function nwcs_placeholder_attr( string $tag, string $name ): string {
	if ( ! preg_match( '/(?<=\s)' . preg_quote( $name, '/' ) . '\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $m ) ) {
		return '';
	}

	// Cift tirnakta deger 2. grupta, tek tirnakta 3. grupta.
	return html_entity_decode( '"' === $m[1][0] ? $m[2] : ( $m[3] ?? '' ), ENT_QUOTES );
}

/**
 * Fotograf adresi mi (logo, SVG ve veri adresi haric)?
 */
function nwcs_placeholder_is_photo_url( string $url ): bool {
	$path = (string) wp_parse_url( str_replace( '\\/', '/', $url ), PHP_URL_PATH );

	return (bool) preg_match( '/\.(jpe?g|png|webp|gif|avif)$/i', $path ) && ! preg_match( '/logo/i', basename( $path ) );
}

/**
 * Buyutme penceresinde gosterilecek "Gorsel gelecek" gorseli (SVG, base64:
 * nitelik ve JSON icinde guvenli).
 */
function nwcs_placeholder_svg_uri(): string {
	static $uri = null;

	if ( null === $uri ) {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900">'
			. '<defs><pattern id="p" width="28" height="28" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="4" height="28" fill="#28322d" fill-opacity=".09"/></pattern></defs>'
			. '<rect width="1200" height="900" fill="#e7eae8"/><rect width="1200" height="900" fill="url(#p)"/>'
			. '<rect x="470" y="420" width="260" height="60" rx="6" fill="#fff" fill-opacity=".85"/>'
			. '<text x="600" y="459" font-family="system-ui,Segoe UI,sans-serif" font-size="26" font-weight="600" fill="#55605a" text-anchor="middle">Görsel gelecek</text></svg>';
		$uri = 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- veri adresi.
	}

	return $uri;
}

/**
 * Tek bir img etiketi icin yer tutucu. Degistirilmeyecekse null.
 */
function nwcs_placeholder_for_img( string $tag ): ?string {
	$src   = nwcs_placeholder_attr( $tag, 'src' );
	$class = trim( nwcs_placeholder_attr( $tag, 'class' ) );
	$w     = (int) nwcs_placeholder_attr( $tag, 'width' );
	$h     = (int) nwcs_placeholder_attr( $tag, 'height' );

	if ( '' === $src || str_starts_with( $src, 'data:' ) || preg_match( '/\.svg(\?|$)/i', $src )
		|| preg_match( '/logo/i', $class . ' ' . basename( (string) wp_parse_url( $src, PHP_URL_PATH ) ) )
		|| ( $w && $h && $w <= 64 && $h <= 64 ) ) {
		return null;
	}

	$variant = nwcs_placeholder_variant( (string) preg_replace( '/-\d+x\d+(?=\.\w+$)/', '', (string) wp_parse_url( $src, PHP_URL_PATH ) ) );
	$css     = '--ph-a:' . $variant['angle'] . 'deg;';

	if ( $w && $h ) {
		$css .= 'aspect-ratio:' . $w . ' / ' . $h . ';--ph-w:' . $w . 'px;';
	} elseif ( ! preg_match( '/(^|\s)(w-full|h-full)(\s|$)/', $class ) ) {
		$css .= 'aspect-ratio:4 / 3;';
	}

	// Panel onizlemesindeki tikla-duzenle isaretleri korunur.
	$data = '';
	if ( preg_match_all( '/(?<=\s)(data-nwcs-[a-z0-9-]+)\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $pairs, PREG_SET_ORDER ) ) {
		foreach ( $pairs as $pair ) {
			$data .= sprintf( ' %s="%s"', esc_attr( strtolower( $pair[1] ) ), esc_attr( html_entity_decode( '"' === $pair[2][0] ? $pair[3] : ( $pair[4] ?? '' ), ENT_QUOTES ) ) );
		}
	}

	$alt = trim( nwcs_placeholder_attr( $tag, 'alt' ) );

	return sprintf(
		'<span class="%1$s" role="img" aria-label="%2$s" style="%3$s"%4$s%5$s><span class="nwcs-ph__t">Görsel gelecek</span></span>',
		esc_attr( trim( sprintf( 'nwcs-ph nwcs-ph--p%d nwcs-ph--t%d %s', $variant['pattern'], $variant['tone'], $class ) ) ),
		esc_attr( '' !== $alt ? 'Görsel gelecek: ' . $alt : 'Görsel gelecek' ),
		esc_attr( $css . nwcs_placeholder_attr( $tag, 'style' ) ),
		$w && $h ? ' data-ph-w' : '',
		$data
	);
}

/**
 * Bir govde parcasina (header/footer disi) donusumleri uygular. Herhangi
 * bir duzenli ifade basarisiz olursa null: cagiran sayfayi degistirmez.
 */
function nwcs_placeholders_transform( string $part ): ?string {
	$steps = array(
		static fn( string $s ) => preg_replace( '#<source\b[^>]*\bsrcset=[^>]*>#i', '', $s ),

		// Temalarin "Ornek gorsel" notu: gorsel gosterilmedigi icin anlamsiz.
		// Kalip UTF-8 bayt olarak yazili (/u yok): bozuk bir karakter sayfayi
		// dusurmesin.
		static fn( string $s ) => preg_replace( "#<(span|p|div)\\b[^>]*>\\s*(?:\xC3\x96rnek|Ornek) g\xC3\xB6rsel\\s*</\\1>#i", '', $s ),

		static fn( string $s ) => preg_replace_callback(
			'#<img\b[^>]*>#i',
			static fn( array $m ): string => nwcs_placeholder_for_img( $m[0] ) ?? $m[0],
			$s
		),

		// Arka plan fotograflari: yalnizca url(...) belirteci desene doner; ayni
		// bildirimdeki gradyan, konum ve boyut gecerli kalir (background kisa
		// yazimi, katmanli arka plan, <style> bloklari dahil).
		static fn( string $s ) => preg_replace_callback(
			'#url\(\s*([\'"]?)([^\'")]+)\1\s*\)#i',
			static function ( array $m ): string {
				if ( ! nwcs_placeholder_is_photo_url( $m[2] ) ) {
					return $m[0];
				}

				return 'repeating-linear-gradient(' . nwcs_placeholder_variant( $m[2] )['angle'] . 'deg,rgba(40,50,45,.12) 0 2px,rgba(231,234,232,.96) 2px 14px)';
			},
			$s
		),

		// Buyutme verisi (data-full, data-gallery JSON, gorsele giden baglanti):
		// fotograf adresi "Gorsel gelecek" SVG'sine. JSON icindeki \/ da yakalanir.
		static fn( string $s ) => preg_replace_callback(
			'#https?:(?:\\\\?/){2}[^\s"\'<>()]+?\.(?:jpe?g|png|webp|gif|avif)(?:\?[^\s"\'<>()]*)?(?=["\'\s)&])#i',
			static fn( array $m ): string => nwcs_placeholder_is_photo_url( $m[0] ) ? nwcs_placeholder_svg_uri() : $m[0],
			$s
		),
	);

	foreach ( $steps as $step ) {
		$result = $step( $part );

		if ( ! is_string( $result ) ) {
			return null;
		}

		$part = $result;
	}

	return $part;
}

/**
 * Sayfa ciktisi. <head> ve <header>/<footer> (logolar) oldugu gibi kalir.
 */
function nwcs_placeholders_filter_html( string $html ): string {
	if ( '' === $html || false === stripos( $html, '<html' ) ) {
		return $html;
	}

	$split = stripos( $html, '<body' );

	if ( false === $split ) {
		return $html;
	}

	$parts = preg_split( '#(<header\b.*?</header>|<footer\b.*?</footer>)#is', substr( $html, $split ), -1, PREG_SPLIT_DELIM_CAPTURE );

	if ( ! is_array( $parts ) ) {
		return $html;
	}

	foreach ( $parts as $index => $part ) {
		// Ust menu ve alt bilgide ilk gorsel logodur: o korunur, digerleri
		// (acilir menudeki urun gorselleri gibi) yer tutucuya doner.
		$logo = '';

		// Logo menuden (<nav>) once gelir; menunun icindeki ilk gorsel logo degildir.
		$nav = stripos( $part, '<nav' );

		if ( preg_match( '#^<(header|footer)\b#i', $part ) && preg_match( '#<img\b[^>]*>#i', $part, $first, PREG_OFFSET_CAPTURE )
			&& ( false === $nav || (int) $first[0][1] < $nav ) ) {
			$logo = $first[0][0];
			$part = substr_replace( $part, "\x00nwcs-logo\x00", (int) $first[0][1], strlen( $logo ) );
		}

		$done = nwcs_placeholders_transform( $part );

		if ( null !== $done && '' !== $logo ) {
			$done = str_replace( "\x00nwcs-logo\x00", $logo, $done );
		}

		if ( null === $done ) {
			return $html; // Bir adim basarisiz: sayfa hic degismeden gider.
		}

		$parts[ $index ] = $done;
	}

	return substr( $html, 0, $split ) . implode( '', $parts );
}
