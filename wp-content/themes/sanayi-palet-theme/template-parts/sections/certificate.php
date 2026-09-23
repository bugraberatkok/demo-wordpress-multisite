<?php
/**
 * Ihracat belgesi: ISPM 15 isaretleme izni. Damga burada tekrarlanmaz;
 * ana sayfada yalnizca hero'da durur, tekrar etkisini dusurmesin.
 */

defined( 'ABSPATH' ) || exit;

$image = sanayi_palet_image( nwcs_image( 'home', 'certificate', 'image', 'large' ), 'certificate' );
$full  = sanayi_palet_image( nwcs_image( 'home', 'certificate', 'image', 'full' ), 'certificate' );
?>
<section class="sp-section sp-cert" data-nwcs-section="certificate">
	<div class="sp-wrap sp-cert__grid">
		<figure class="sp-cert__doc" <?php nwcs_edit_attr( 'home', 'certificate', 'image' ); ?>>
			<?php if ( ! empty( $image['url'] ) ) : ?>
				<a href="<?php echo esc_url( $full['url'] ); ?>" class="sp-cert__doc-link">
					<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy" decoding="async" />
					<span class="screen-reader-text">Belgeyi büyük boyutta açın</span>
				</a>
			<?php else : ?>
				<?php echo sanayi_palet_image_tag( $image, '', 'Belge görseli' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			<?php endif; ?>
		</figure>

		<div class="sp-cert__body">
			<h2 class="sp-title" <?php nwcs_edit_attr( 'home', 'certificate', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'certificate', 'title' ) ); ?></h2>
			<p class="sp-cert__text" <?php nwcs_edit_attr( 'home', 'certificate', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'certificate', 'text' ) ); ?></p>

			<dl class="sp-cert__facts" <?php nwcs_edit_attr( 'home', 'certificate', 'facts' ); ?>>
				<?php foreach ( nwcs_rows( 'home', 'certificate', 'facts' ) as $fact ) : ?>
					<div class="sp-cert__fact">
						<dt><?php echo esc_html( $fact['label'] ?? '' ); ?></dt>
						<dd><?php echo esc_html( $fact['value'] ?? '' ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</div>
</section>
