<?php
/**
 * Calisma ilkeleri bolumu.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'inner', 'values', 'items' );
?>
<section class="k-section k-section--alt" data-nwcs-section="values">
	<div class="k-wrap">
		<div class="k-section-head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'inner', 'values', 'title' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'values', 'title' ) ); ?></h2>
		</div>

		<div class="k-cards">
			<?php foreach ( $items as $index => $item ) : ?>
				<article class="k-card" <?php nwcs_edit_attr( 'inner', 'values', 'items', $index, 'title' ); ?>>
					<div class="k-card__icon"><?php nwcs_the_icon( $item['icon'] ?? '', 'k-icon', 28 ); ?></div>
					<h3 class="k-card__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p class="k-card__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
