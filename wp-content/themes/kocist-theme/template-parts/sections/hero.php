<?php
/**
 * Ana sayfa hero bolumu.
 */

defined( 'ABSPATH' ) || exit;

$image = nwcs_image( 'home', 'hero', 'image' );
$stats = nwcs_rows( 'home', 'hero', 'stats' );
?>
<section class="k-hero" id="hero" data-nwcs-section="hero">
	<div class="k-wrap">
		<div class="k-hero__grid">
			<div>
				<p class="k-hero__eyebrow" <?php nwcs_edit_attr( 'home', 'hero', 'eyebrow' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'eyebrow' ) ); ?></p>
				<h1 class="k-hero__title" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'title' ) ); ?></h1>
				<p class="k-hero__desc" <?php nwcs_edit_attr( 'home', 'hero', 'description' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'description' ) ); ?></p>
				<div class="k-hero__actions">
					<a class="k-btn k-btn--primary" href="<?php echo esc_url( kocist_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'primary_label' ); ?>>
						<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
					</a>
					<a class="k-btn k-btn--ghost" href="<?php echo esc_url( kocist_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'secondary_label' ); ?>>
						<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary_label' ) ); ?>
					</a>
				</div>
			</div>

			<div class="k-hero__media" <?php nwcs_edit_attr( 'home', 'hero', 'image' ); ?>>
				<?php echo kocist_image_tag( $image, 'k-hero__img', 'Örnek görsel — Koçist hero' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				<span class="k-hero__frame" aria-hidden="true"></span>
			</div>
		</div>

		<?php if ( $stats ) : ?>
			<div class="k-stats">
				<?php foreach ( $stats as $index => $stat ) : ?>
					<div class="k-stat" <?php nwcs_edit_attr( 'home', 'hero', 'stats', $index, 'value' ); ?>>
						<div class="k-stat__value"><?php echo esc_html( $stat['value'] ?? '' ); ?></div>
						<div class="k-stat__label"><?php echo esc_html( $stat['label'] ?? '' ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
