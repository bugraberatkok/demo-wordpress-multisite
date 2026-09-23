<?php
/**
 * Ic sayfa basligi: buyuk baslik ve altinda one cikan cumle.
 * $args: page, component (alan adlari: title, lead).
 */

defined( 'ABSPATH' ) || exit;

$page      = $args['page'];
$component = $args['component'] ?? 'head';
?>
<header class="mx-auto max-w-[80rem] px-5 pt-14 md:px-8 md:pt-20">
	<h1 class="text-[3rem] font-bold leading-[0.95] md:text-[4.5rem]" <?php nwcs_edit_attr( $page, $component, 'title' ); ?>>
		<?php echo esc_html( nwcs_field( $page, $component, 'title' ) ); ?>
	</h1>

	<p class="mt-5 max-w-[42rem] text-xl leading-snug text-steel md:text-2xl" <?php nwcs_edit_attr( $page, $component, 'lead' ); ?>>
		<?php echo esc_html( nwcs_field( $page, $component, 'lead' ) ); ?>
	</p>
</header>
