<?php
/**
 * Insan Kaynaklari: politika. Solda baslik ve giris, sagda ilkeler.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'hr', 'policy', 'items' );
?>
<section class="k-section" data-nwcs-section="policy">
	<div class="k-wrap k-split">
		<div class="k-split__head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'hr', 'policy', 'title' ); ?>><?php echo esc_html( nwcs_field( 'hr', 'policy', 'title' ) ); ?></h2>
			<p class="k-split__intro" <?php nwcs_edit_attr( 'hr', 'policy', 'intro' ); ?>><?php echo esc_html( nwcs_field( 'hr', 'policy', 'intro' ) ); ?></p>
		</div>

		<?php if ( $items ) : ?>
			<ul class="k-rules">
				<?php foreach ( $items as $index => $item ) : ?>
					<li class="k-rules__item" <?php nwcs_edit_attr( 'hr', 'policy', 'items', $index, 'text' ); ?>>
						<?php nwcs_the_icon( 'check', 'k-rules__icon', 22 ); ?>
						<span><?php echo esc_html( $item['text'] ?? '' ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
