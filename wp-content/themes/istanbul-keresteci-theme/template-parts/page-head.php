<?php
/**
 * Ic sayfa basligi: koyu zemin, buyuk baslik, kisa aciklama.
 *
 * Beklenen $args: page (manifest sayfa anahtari). Istege bagli: title ve
 * text (alan yerine dogrudan deger), crumbs ([ [etiket, adres, duzenleme], ... ];
 * duzenleme istege bagli nwcs_edit_attr argumanlari),
 * title_edit ve text_edit (dogrudan deger verildiginde onizlemede tiklaninca
 * acilacak alan: nwcs_edit_attr argumanlari).
 */

defined( 'ABSPATH' ) || exit;

$page      = (string) ( $args['page'] ?? '' );
$own_title = isset( $args['title'] );
$own_text  = isset( $args['text'] );
$title     = $own_title ? (string) $args['title'] : nwcs_field( $page, 'head', 'title' );
$text      = $own_text ? (string) $args['text'] : nwcs_field( $page, 'head', 'text' );
$crumbs    = $args['crumbs'] ?? array();

// Dogrudan verilen deger panelde baska bir alandan geliyorsa o alana isaret.
$edit = static function ( bool $own, string $field, array $args ) use ( $page ): void {
	if ( ! $own ) {
		nwcs_edit_attr( $page, 'head', $field );
	} elseif ( ! empty( $args[ $field . '_edit' ] ) ) {
		nwcs_edit_attr( ...$args[ $field . '_edit' ] );
	}
};
?>
<header class="ik-pagehead">
	<div class="ik-wrap">
		<?php if ( $crumbs ) : ?>
			<nav class="ik-crumbs" aria-label="Konum">
				<ol>
					<?php foreach ( $crumbs as $crumb ) : ?>
						<li><a href="<?php echo esc_url( $crumb[1] ); ?>" <?php if ( ! empty( $crumb[2] ) ) { nwcs_edit_attr( ...$crumb[2] ); } ?>><?php echo esc_html( $crumb[0] ); ?></a></li>
					<?php endforeach; ?>
				</ol>
			</nav>
		<?php endif; ?>

		<h1 class="ik-pagehead__title" <?php $edit( $own_title, 'title', $args ); ?>><?php echo esc_html( $title ); ?></h1>

		<?php if ( '' !== trim( $text ) ) : ?>
			<p class="ik-pagehead__text" <?php $edit( $own_text, 'text', $args ); ?>><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>
</header>
