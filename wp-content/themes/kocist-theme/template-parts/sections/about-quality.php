<?php
/**
 * Kurumsal: Kalite Politikamiz. Uzun tek paragraf okunur olsun diye
 * ilkelere bolundu; ilkeler sira bildirmedigi icin numarasiz.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'inner', 'quality', 'items' );
?>
<section class="k-section k-section--alt" data-nwcs-section="quality">
	<div class="k-wrap">
		<div class="k-section-head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'inner', 'quality', 'title' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'quality', 'title' ) ); ?></h2>
			<p class="k-section-sub" <?php nwcs_edit_attr( 'inner', 'quality', 'intro' ); ?>><?php echo esc_html( nwcs_field( 'inner', 'quality', 'intro' ) ); ?></p>
		</div>

		<?php if ( $items ) : ?>
			<ul class="k-principles">
				<?php foreach ( $items as $index => $item ) : ?>
					<li class="k-principles__item" <?php nwcs_edit_attr( 'inner', 'quality', 'items', $index, 'text' ); ?>>
						<?php nwcs_the_icon( 'check', 'k-principles__icon', 22 ); ?>
						<span><?php echo esc_html( $item['text'] ?? '' ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
