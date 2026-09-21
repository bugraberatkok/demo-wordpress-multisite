<?php
/**
 * Uretim sureci bolumu (numarali adimlar).
 */

defined( 'ABSPATH' ) || exit;

$steps = nwcs_rows( 'home', 'process', 'steps' );
?>
<section class="p-section p-section--forest" id="surec" data-nwcs-section="process">
	<div class="p-wrap">
		<div class="p-head">
			<h2 class="p-title" <?php nwcs_edit_attr( 'home', 'process', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'process', 'title' ) ); ?></h2>
			<p class="p-sub" <?php nwcs_edit_attr( 'home', 'process', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'process', 'subtitle' ) ); ?></p>
		</div>

		<div class="p-steps">
			<?php foreach ( $steps as $index => $step ) : ?>
				<div class="p-step" <?php nwcs_edit_attr( 'home', 'process', 'steps', $index, 'title' ); ?>>
					<div class="p-step__no"><?php echo esc_html( $step['number'] ?? '' ); ?></div>
					<h3 class="p-step__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
					<p class="p-step__text"><?php echo esc_html( $step['text'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
