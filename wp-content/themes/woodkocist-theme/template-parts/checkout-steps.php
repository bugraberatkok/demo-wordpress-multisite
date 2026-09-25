<?php
/**
 * Siparis adimlari gostergesi: Sepet → Bilgiler ve ödeme → Onay.
 * Gercek bir sira oldugu icin numarali. $args['step']: 1, 2 ya da 3.
 */

defined( 'ABSPATH' ) || exit;

$step  = (int) ( $args['step'] ?? 1 );
// Numara => ad, adres, panel alani (Tum Sayfalar -> Sepet Sayfasi).
$steps = array(
	1 => array( (string) nwcs_field( 'global', 'cart', 'step_cart' ), home_url( '/sepet/' ), 'step_cart' ),
	2 => array( (string) nwcs_field( 'global', 'cart', 'step_details' ), home_url( '/odeme/' ), 'step_details' ),
	3 => array( (string) nwcs_field( 'global', 'cart', 'step_done' ), '', 'step_done' ),
);
?>
<ol class="wk-steps-bar" aria-label="Sipariş adımları">
	<?php foreach ( $steps as $number => $item ) : ?>
		<li class="<?php echo $number < $step ? 'is-done' : ( $number === $step ? 'is-current' : '' ); ?>" <?php echo $number === $step ? 'aria-current="step"' : ''; ?>>
			<span class="wk-steps-bar__num"><?php echo $number < $step ? wk_icon( 'check' ) : (int) $number; // phpcs:ignore WordPress.Security.EscapingOutput ?></span>
			<?php if ( $number < $step && '' !== $item[1] && 3 !== $step ) : ?>
				<a href="<?php echo esc_url( $item[1] ); ?>" <?php nwcs_edit_attr( 'global', 'cart', $item[2] ); ?>><?php echo esc_html( $item[0] ); ?></a>
			<?php else : ?>
				<span <?php nwcs_edit_attr( 'global', 'cart', $item[2] ); ?>><?php echo esc_html( $item[0] ); ?></span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
