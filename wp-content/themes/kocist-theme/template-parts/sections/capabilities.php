<?php
/**
 * Yetkinlik kartlari bolumu.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'capabilities', 'items' );
?>
<section class="k-section" id="yetkinlik" data-nwcs-section="capabilities">
	<div class="k-wrap">
		<div class="k-section-head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'home', 'capabilities', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'capabilities', 'title' ) ); ?></h2>
			<p class="k-section-sub" <?php nwcs_edit_attr( 'home', 'capabilities', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'capabilities', 'subtitle' ) ); ?></p>
		</div>

		<div class="k-cards">
			<?php foreach ( $items as $index => $item ) : ?>
				<article class="k-card" <?php nwcs_edit_attr( 'home', 'capabilities', 'items', $index, 'title' ); ?>>
					<div class="k-card__icon"><?php nwcs_the_icon( $item['icon'] ?? '', 'k-icon', 30 ); ?></div>
					<h3 class="k-card__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p class="k-card__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
