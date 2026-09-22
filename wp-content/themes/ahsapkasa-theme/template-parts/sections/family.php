<?php
/**
 * Urun grubu seridi: dort urun adi, aralarinda ahsap tonunda ince cizgiler.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'family', 'items' );
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 md:pt-28">

	<div class="flex flex-wrap items-baseline justify-between gap-x-8 gap-y-2">
		<h2 class="font-display text-2xl font-semibold md:text-3xl" <?php nwcs_edit_attr( 'home', 'family', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'family', 'title' ) ); ?>
		</h2>
		<p class="text-base text-moss" <?php nwcs_edit_attr( 'home', 'family', 'note' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'family', 'note' ) ); ?>
		</p>
	</div>

	<ul class="mt-8 border-t border-timber/35" <?php nwcs_edit_attr( 'home', 'family', 'items' ); ?>>
		<?php foreach ( $items as $item ) : ?>
			<li class="border-b border-timber/35">
				<a href="<?php echo esc_url( ahsapkasa_link( $item['url'] ?? '' ) ); ?>"
					class="block py-5 font-display text-2xl font-medium text-ink transition-all duration-200 ease-out hover:translate-x-2 hover:text-forest md:text-3xl">
					<?php echo esc_html( $item['label'] ?? '' ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
