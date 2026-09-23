<?php
/**
 * Siparis sureci: uc adim, ustlerinden gecen bir testere kesigi.
 *
 * Bolum gorunur olunca kesik soldan saga cizilir, eristigi her adimin numarasi
 * damga gibi basilir (assets/js/process.js). JS yoksa ya da hareket azaltma
 * tercihi aciksa her sey bastan gorunur. Numaralar gercek bir sirayi gosterir.
 */

defined( 'ABSPATH' ) || exit;

$steps = nwcs_rows( 'home', 'process', 'steps' );

if ( ! $steps ) {
	return;
}
?>
<section class="ik-section ik-section--dark ik-process" aria-labelledby="ik-process-title">
	<div class="ik-wrap">
		<div class="ik-section__head">
			<div>
				<h2 class="ik-title" id="ik-process-title" <?php nwcs_edit_attr( 'home', 'process', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'process', 'title' ) ); ?></h2>
				<p class="ik-lead" <?php nwcs_edit_attr( 'home', 'process', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'process', 'text' ) ); ?></p>
			</div>
			<a class="btn btn--solid" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'process', 'cta_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'process', 'cta_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'process', 'cta_label' ) ); ?>
			</a>
		</div>

		<div class="ik-process__body" data-ik-process style="--steps: <?php echo count( $steps ); ?>">
			<span class="ik-process__cut" aria-hidden="true"></span>
			<ol class="ik-process__steps" <?php nwcs_edit_attr( 'home', 'process', 'steps' ); ?>>
				<?php foreach ( $steps as $index => $step ) : ?>
					<li class="ik-step" style="--i: <?php echo (int) $index; ?>">
						<span class="ik-step__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<h3 class="ik-step__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<p class="ik-step__text"><?php echo esc_html( $step['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>
