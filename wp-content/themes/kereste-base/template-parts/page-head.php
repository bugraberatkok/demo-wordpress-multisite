<?php
/**
 * Ic sayfa basligi: tas zeminli serit, slab baslik ve one cikan cumle.
 * $args['page']: manifest sayfa anahtari (alanlar: head.title, head.lead).
 */

defined( 'ABSPATH' ) || exit;

$page = $args['page'];
?>
<header class="bg-stone">
	<div class="mx-auto max-w-[78rem] px-5 pb-12 pt-12 md:px-8 md:pb-16 md:pt-16">
		<h1 class="max-w-[18ch] text-[2.75rem] md:text-[4rem]" <?php nwcs_edit_attr( $page, 'head', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( $page, 'head', 'title' ) ); ?>
		</h1>
		<p class="mt-4 max-w-[40rem] text-xl leading-snug text-muted" <?php nwcs_edit_attr( $page, 'head', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( $page, 'head', 'lead' ) ); ?>
		</p>
	</div>
</header>
