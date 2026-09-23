<?php
/**
 * Ana sayfa girisi: palet duvari fotografi, baslik, iki eylem ve yakik damga.
 *
 * Damga sitenin tek gosterisli ogesi; sayfa acilirken bir kez "basilir"
 * (hero.css). Ayni damga yalnizca belge bolumunde kucuk olarak tekrar eder.
 */

defined( 'ABSPATH' ) || exit;

$image = sanayi_palet_image( nwcs_image( 'home', 'hero', 'image', 'full' ), 'hero' );
?>
<section class="sp-hero" data-nwcs-section="hero">
	<?php if ( ! empty( $image['url'] ) ) : ?>
		<img class="sp-hero__photo" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" fetchpriority="high" <?php nwcs_edit_attr( 'home', 'hero', 'image' ); ?> />
	<?php endif; ?>

	<div class="sp-wrap sp-hero__inner">
		<div class="sp-hero__copy">
			<h1 class="sp-hero__title" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'title' ) ); ?></h1>

			<p class="sp-hero__text" <?php nwcs_edit_attr( 'home', 'hero', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'text' ) ); ?></p>

			<div class="sp-hero__actions">
				<a class="btn btn--solid" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'primary_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
				</a>
				<a class="btn btn--light" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'secondary_label' ); ?>>
					<?php sanayi_palet_icon( 'phone', 18 ); ?>
					<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary_label' ) ); ?>
				</a>
			</div>
		</div>

		<figure class="sp-hero__stamp">
			<?php
			sanayi_palet_stamp(
				nwcs_field( 'home', 'hero', 'stamp_code' ),
				nwcs_field( 'home', 'hero', 'stamp_standard' ),
				'sp-stamp--hero'
			);
			?>
			<figcaption class="sp-hero__stamp-caption" <?php nwcs_edit_attr( 'home', 'hero', 'stamp_caption' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'stamp_caption' ) ); ?>
			</figcaption>
		</figure>
	</div>
</section>
