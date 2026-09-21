<?php
/**
 * Kullanim alanlari bolumu.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'references', 'items' );
?>
<section class="k-section" id="kullanim" data-nwcs-section="references">
	<div class="k-wrap">
		<div class="k-section-head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'home', 'references', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'references', 'title' ) ); ?></h2>
			<p class="k-section-sub" <?php nwcs_edit_attr( 'home', 'references', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'references', 'subtitle' ) ); ?></p>
		</div>

		<div class="k-refs">
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="k-ref" <?php nwcs_edit_attr( 'home', 'references', 'items', $index, 'title' ); ?>>
					<span class="k-ref__icon"><?php nwcs_the_icon( $item['icon'] ?? '', 'k-icon', 26 ); ?></span>
					<div>
						<h3 class="k-ref__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
						<p class="k-ref__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
