<?php
/**
 * Siparis adimlari gostergesi: Sepet → Bilgiler ve ödeme → Onay.
 * Gercek bir sira oldugu icin numarali. $args['step']: 1, 2 ya da 3.
 */

defined( 'ABSPATH' ) || exit;

$step  = (int) ( $args['step'] ?? 1 );
$steps = array(
	1 => array( 'Sepet', home_url( '/sepet/' ) ),
	2 => array( 'Bilgiler ve ödeme', home_url( '/odeme/' ) ),
	3 => array( 'Onay', '' ),
);
?>
<ol class="wk-steps-bar" aria-label="Sipariş adımları">
	<?php foreach ( $steps as $number => $item ) : ?>
		<li class="<?php echo $number < $step ? 'is-done' : ( $number === $step ? 'is-current' : '' ); ?>" <?php echo $number === $step ? 'aria-current="step"' : ''; ?>>
			<span class="wk-steps-bar__num"><?php echo $number < $step ? wk_icon( 'check' ) : (int) $number; // phpcs:ignore WordPress.Security.EscapingOutput ?></span>
			<?php if ( $number < $step && '' !== $item[1] && 3 !== $step ) : ?>
				<a href="<?php echo esc_url( $item[1] ); ?>"><?php echo esc_html( $item[0] ); ?></a>
			<?php else : ?>
				<span><?php echo esc_html( $item[0] ); ?></span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
