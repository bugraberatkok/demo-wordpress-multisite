<?php
/**
 * Urun gruplari bolumu (gorselli kartlar).
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'catalog', 'items' );
?>
<section class="k-section k-section--alt" id="katalog" data-nwcs-section="catalog">
	<div class="k-wrap">
		<div class="k-section-head">
			<h2 class="k-section-title"><?php echo esc_html( nwcs_field( 'home', 'catalog', 'title' ) ); ?></h2>
			<p class="k-section-sub"><?php echo esc_html( nwcs_field( 'home', 'catalog', 'subtitle' ) ); ?></p>
		</div>

		<div class="k-catalog">
			<?php
			foreach ( $items as $item ) :
				$image = nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'medium_large' );
				?>
				<article class="k-cat">
					<div class="k-cat__media">
						<?php echo kocist_image_tag( $image, '', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</div>
					<div class="k-cat__body">
						<h3 class="k-cat__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
						<p class="k-cat__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
						<a class="k-cat__link" href="<?php echo esc_url( kocist_link( $item['link_url'] ?? '' ) ); ?>">
							<?php echo esc_html( $item['link_label'] ?? '' ); ?>
							<?php nwcs_the_icon( 'arrow', 'k-icon', 16 ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
