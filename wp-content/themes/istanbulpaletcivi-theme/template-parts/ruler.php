<?php
/**
 * Sari cetvel seridi: sitenin tek belirgin ogesi. Ust kenarinda milimetre
 * centikleri ve santimetre rakamlari; civi boy boy satilan bir urun. Cetvel
 * susleme degil olcu dili: sari renk sitede yalnizca burada ve odakta.
 *
 * $args: text, label, url (bos ise dugme yok), page/component (duzenleme
 * isaretleri icin).
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array( 'text' => '', 'label' => '', 'url' => '', 'page' => '', 'component' => '' ) );
?>
<div class="pc-ruler">
	<div class="pc-ruler__scale" aria-hidden="true">
		<?php for ( $cm = 0; $cm <= 60; $cm++ ) : ?>
			<span><?php echo (int) $cm; ?></span>
		<?php endfor; ?>
	</div>

	<?php if ( '' !== $args['text'] ) : ?>
		<div class="pc-wrap pc-ruler__row">
			<p class="pc-ruler__text" <?php $args['page'] && nwcs_edit_attr( $args['page'], $args['component'], 'text' ); ?>>
				<?php echo esc_html( $args['text'] ); ?>
			</p>
			<?php if ( '' !== $args['label'] && '' !== $args['url'] ) : ?>
				<a href="<?php echo esc_url( $args['url'] ); ?>" class="pc-btn pc-btn--navy" <?php $args['page'] && nwcs_edit_attr( $args['page'], $args['component'], 'label' ); ?>>
					<?php echo esc_html( $args['label'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
