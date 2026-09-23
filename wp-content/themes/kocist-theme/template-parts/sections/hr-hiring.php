<?php
/**
 * Insan Kaynaklari: ise alim sureci. Gercek bir sirali surec oldugu icin
 * adimlar numarali (ol) basilir; numara CSS sayacindan gelir.
 */

defined( 'ABSPATH' ) || exit;

$steps = nwcs_rows( 'hr', 'hiring', 'steps' );
?>
<section class="k-section k-section--alt" data-nwcs-section="hiring">
	<div class="k-wrap">
		<div class="k-section-head">
			<h2 class="k-section-title" <?php nwcs_edit_attr( 'hr', 'hiring', 'title' ); ?>><?php echo esc_html( nwcs_field( 'hr', 'hiring', 'title' ) ); ?></h2>
			<p class="k-section-sub" <?php nwcs_edit_attr( 'hr', 'hiring', 'intro' ); ?>><?php echo esc_html( nwcs_field( 'hr', 'hiring', 'intro' ) ); ?></p>
		</div>

		<?php if ( $steps ) : ?>
			<ol class="k-steps">
				<?php foreach ( $steps as $index => $step ) : ?>
					<li class="k-steps__item" <?php nwcs_edit_attr( 'hr', 'hiring', 'steps', $index, 'title' ); ?>>
						<h3 class="k-steps__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<p class="k-steps__text"><?php echo esc_html( $step['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
