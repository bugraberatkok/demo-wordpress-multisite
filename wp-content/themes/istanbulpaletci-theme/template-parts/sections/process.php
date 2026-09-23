<?php
/**
 * Ana sayfa: uretim sureci.
 *
 * Numaralar burada bilgi tasir: adimlar gercekten sirali bir surectir.
 * Ustteki kesintisiz cizgi, adimlari tek bir hat uzerinde okutur.
 */

defined( 'ABSPATH' ) || exit;

$steps = nwcs_rows( 'home', 'process', 'steps' );
?>
<section class="mx-auto max-w-[80rem] px-5 pt-24 md:px-8 md:pt-32">

	<div class="max-w-[40rem]">
		<h2 class="text-[2.5rem] font-semibold md:text-5xl" <?php nwcs_edit_attr( 'home', 'process', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'process', 'title' ) ); ?>
		</h2>
		<p class="mt-4 text-lg leading-relaxed text-steel" <?php nwcs_edit_attr( 'home', 'process', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'process', 'lead' ) ); ?>
		</p>
	</div>

	<ol class="mt-12 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-5" <?php nwcs_edit_attr( 'home', 'process', 'steps' ); ?>>
		<?php foreach ( $steps as $index => $step ) : ?>
			<li class="border-t-2 border-ink pt-5">
				<span class="tabular block font-display text-[2rem] font-semibold leading-none text-steel/70" aria-hidden="true">
					<?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?>
				</span>
				<h3 class="mt-4 text-lg font-semibold"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
				<p class="mt-2 text-[0.9375rem] leading-relaxed text-steel"><?php echo esc_html( $step['text'] ?? '' ); ?></p>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
