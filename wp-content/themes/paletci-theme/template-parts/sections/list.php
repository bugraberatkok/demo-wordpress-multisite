<?php
/**
 * Alt sayfa urun listesi.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'inner', 'list', 'items' );
?>
<section class="p-section p-section--cream" data-nwcs-section="list">
	<div class="p-wrap">
		<div class="p-head p-head--left">
			<h2 class="p-title" <?php nwcs_edit_attr( 'inner', 'list', 'title' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'list', 'title' ) ); ?></h2>
		</div>

		<div class="p-list">
			<?php foreach ( $items as $index => $item ) : ?>
				<article class="p-list__row" <?php nwcs_edit_attr( 'inner', 'list', 'items', $index, 'title' ); ?>>
					<span class="p-list__icon"><?php nwcs_the_icon( $item['icon'] ?? '', 'p-icon', 24 ); ?></span>
					<div>
						<h3 class="p-list__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
						<p class="p-list__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					</div>
					<?php if ( ! empty( $item['meta'] ) ) : ?>
						<span class="p-list__meta"><?php echo esc_html( $item['meta'] ); ?></span>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
