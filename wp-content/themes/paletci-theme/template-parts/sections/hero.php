<?php
/**
 * Anasayfa hero bolumu (ortalanmis duzen).
 */

defined( 'ABSPATH' ) || exit;

$image = nwcs_image( 'home', 'hero', 'image' );
$chips = nwcs_rows( 'home', 'hero', 'chips' );
?>
<section class="p-hero" id="hero" data-nwcs-section="hero">
	<div class="p-wrap">
		<span class="p-badge">
			<?php nwcs_the_icon( 'star', 'p-icon', 16 ); ?>
			<?php echo esc_html( nwcs_field( 'home', 'hero', 'badge' ) ); ?>
		</span>

		<h1 class="p-hero__title"><?php echo esc_html( nwcs_field( 'home', 'hero', 'title' ) ); ?></h1>
		<p class="p-hero__sub"><?php echo esc_html( nwcs_field( 'home', 'hero', 'subtitle' ) ); ?></p>

		<div class="p-hero__actions">
			<a class="p-btn p-btn--clay" href="<?php echo esc_url( paletci_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>">
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
				<?php nwcs_the_icon( 'arrow', 'p-icon', 18 ); ?>
			</a>
			<a class="p-btn p-btn--outline" href="<?php echo esc_url( paletci_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>">
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary_label' ) ); ?>
			</a>
		</div>

		<?php if ( $chips ) : ?>
			<div class="p-chips">
				<?php foreach ( $chips as $chip ) : ?>
					<span class="p-chip">
						<?php nwcs_the_icon( $chip['icon'] ?? '', 'p-icon', 17 ); ?>
						<?php echo esc_html( $chip['label'] ?? '' ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="p-hero__media">
			<?php echo paletci_image_tag( $image, '', 'Örnek görsel — palet üretimi' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>
	</div>
</section>
