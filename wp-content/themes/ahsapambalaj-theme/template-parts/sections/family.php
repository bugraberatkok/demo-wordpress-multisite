<?php
/**
 * Urun grubu: dort kompakt kart. Ayrinti hizmetler sayfasinda.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'family', 'items' );
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 md:pt-28">

	<div class="text-center" data-reveal>
		<h2 class="font-display text-3xl font-semibold md:text-4xl" <?php nwcs_edit_attr( 'home', 'family', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'family', 'title' ) ); ?>
		</h2>
		<p class="mt-3 text-base text-moss" <?php nwcs_edit_attr( 'home', 'family', 'note' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'family', 'note' ) ); ?>
		</p>
	</div>

	<ul class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-reveal-stagger <?php nwcs_edit_attr( 'home', 'family', 'items' ); ?>>
		<?php foreach ( $items as $item_index => $item ) :
			$thumb = ahsapambalaj_image( nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'medium_large' ), 'family', $item_index );
			?>
			<li data-reveal>
				<a href="<?php echo esc_url( ahsapambalaj_link( $item['url'] ?? '' ) ); ?>"
					class="card lift group block h-full overflow-hidden hover:border-forest/45">

					<span class="block aspect-[4/3] overflow-hidden bg-dust">
						<?php if ( ! empty( $thumb['url'] ) ) : ?>
							<img src="<?php echo esc_url( $thumb['url'] ); ?>"
								alt="<?php echo esc_attr( $thumb['alt'] ?: ( $item['label'] ?? '' ) ); ?>"
								class="h-full w-full object-cover"
								loading="lazy" decoding="async" />
						<?php endif; ?>
					</span>

					<span class="block px-5 py-6 text-center">
						<span class="block font-display text-xl font-semibold text-ink transition-colors duration-200 group-hover:text-forest">
							<?php echo esc_html( $item['label'] ?? '' ); ?>
						</span>
						<span class="mt-2 block text-sm leading-relaxed text-moss">
							<?php echo esc_html( $item['note'] ?? '' ); ?>
						</span>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
