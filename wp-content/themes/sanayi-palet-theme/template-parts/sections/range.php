<?php
/**
 * Urun gami: dort urun grubu, sabit dortlu dizilim.
 */

defined( 'ABSPATH' ) || exit;

$items      = nwcs_rows( 'home', 'range', 'items' );
$link_label = nwcs_field( 'home', 'range', 'link_label' );
?>
<section class="sp-section sp-range" id="urunler" data-nwcs-section="range">
	<div class="sp-wrap">
		<header class="sp-section__head">
			<h2 class="sp-title" <?php nwcs_edit_attr( 'home', 'range', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'range', 'title' ) ); ?></h2>
			<p class="sp-lead" <?php nwcs_edit_attr( 'home', 'range', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'range', 'text' ) ); ?></p>
		</header>

		<ul class="sp-range__list" <?php nwcs_edit_attr( 'home', 'range', 'items' ); ?>>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php $image = sanayi_palet_image( nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'medium_large' ), 'range_' . $index ); ?>
				<li class="sp-range__item">
					<div class="sp-range__media" <?php nwcs_edit_attr( 'home', 'range', 'items', $index, 'image' ); ?>>
						<?php if ( ! empty( $image['url'] ) ) : ?>
							<?php echo sanayi_palet_image_tag( $image, 'sp-range__image' ); // phpcs:ignore WordPress.Security.EscapingOutput -- fonksiyon kendi icinde escape eder. ?>
						<?php else : ?>
							<?php // Fotograf yoksa gri yer tutucu yerine ahsap zeminli ikon blogu. ?>
							<span class="sp-range__noimage"><?php sanayi_palet_icon( 'box', 56 ); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="sp-range__name" <?php nwcs_edit_attr( 'home', 'range', 'items', $index, 'name' ); ?>><?php echo esc_html( $item['name'] ?? '' ); ?></h3>
					<p class="sp-range__text" <?php nwcs_edit_attr( 'home', 'range', 'items', $index, 'text' ); ?>><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					<?php if ( ! empty( $item['spec'] ) ) : ?>
						<p class="sp-range__spec" <?php nwcs_edit_attr( 'home', 'range', 'items', $index, 'spec' ); ?>><?php echo esc_html( $item['spec'] ); ?></p>
					<?php endif; ?>
					<a class="sp-link sp-range__link" href="<?php echo esc_url( sanayi_palet_link( $item['url'] ?? '' ) ); ?>">
						<?php echo esc_html( $link_label ); ?><span class="screen-reader-text">: <?php echo esc_html( $item['name'] ?? '' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
