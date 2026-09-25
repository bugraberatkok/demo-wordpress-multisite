<?php
/**
 * Eski sitenin 4 blog yazisi. Metinler content/blog-posts.php'de; panel de
 * ayni dosyadan okur (content-manifest.php: 'yazi-<slug>').
 *
 * Kurulum (inc/setup.php) yazilari yalnizca yoksa olusturur; sonradan
 * yapilan duzenlemelerin ustune yazmaz.
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int, array{slug:string, title:string, date:string, image:string, excerpt:string, content:string}>
 */
function pc_seed_posts(): array {
	return array_map(
		static fn( array $post ): array => $post + array( 'content' => pc_post_blocks( $post['body'] ) ),
		(array) include get_theme_file_path( 'content/blog-posts.php' )
	);
}

/**
 * Bos satirla ayrilmis metnin paragraflari; '## ' ile baslayan ara baslik.
 *
 * @return array<int, array{0:string, 1:string}> (etiket, metin)
 */
function pc_post_blocks_list( string $text ): array {
	$blocks = array();

	foreach ( preg_split( '/\R\s*\R/u', trim( $text ) ) ?: array() as $block ) {
		$block = trim( $block );

		if ( '' !== $block ) {
			$blocks[] = str_starts_with( $block, '## ' ) ? array( 'h2', substr( $block, 3 ) ) : array( 'p', $block );
		}
	}

	return $blocks;
}

/**
 * Yazi metni blok duzenleyici bicimiyle (ilk kurulumda post_content).
 */
function pc_post_blocks( string $text ): string {
	return implode(
		"\n\n",
		array_map(
			static fn( array $block ): string => 'h2' === $block[0]
				? "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( $block[1] ) . "</h2>\n<!-- /wp:heading -->"
				: "<!-- wp:paragraph -->\n<p>" . esc_html( $block[1] ) . "</p>\n<!-- /wp:paragraph -->",
			pc_post_blocks_list( $text )
		)
	);
}

/**
 * Panelde degistirilen yazi metni HTML olarak (yazi sayfasinda the_content).
 */
function pc_post_html( string $text ): string {
	$html = '';

	foreach ( pc_post_blocks_list( $text ) as $block ) {
		$html .= 'h2' === $block[0]
			? '<h2 class="wp-block-heading">' . esc_html( $block[1] ) . '</h2>'
			: '<p>' . pc_multiline( $block[1] ) . '</p>';
	}

	return $html;
}
