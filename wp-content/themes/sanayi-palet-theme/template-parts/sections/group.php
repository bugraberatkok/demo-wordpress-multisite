<?php
/**
 * Kocist Grup tanitimi; 45 yil bir rakam seridi olarak degil, cumle icinde.
 */

defined( 'ABSPATH' ) || exit;

$image = sanayi_palet_image( nwcs_image( 'home', 'group', 'image', 'large' ), 'group' );
?>
<section class="sp-section sp-group" data-nwcs-section="group">
	<div class="sp-wrap sp-group__grid">
		<div class="sp-group__body">
			<h2 class="sp-title" <?php nwcs_edit_attr( 'home', 'group', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'group', 'title' ) ); ?></h2>
			<p class="sp-group__text" <?php nwcs_edit_attr( 'home', 'group', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'group', 'text' ) ); ?></p>
			<a class="btn btn--outline" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'group', 'button_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'group', 'button_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'group', 'button_label' ) ); ?>
			</a>
		</div>
		<div class="sp-group__media" <?php nwcs_edit_attr( 'home', 'group', 'image' ); ?>>
			<?php echo sanayi_palet_image_tag( $image, 'sp-group__image' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>
	</div>
</section>
