<?php
/**
 * Urun grubu: iki sutunlu yatay satirlar (solda fotograf, sagda ad ve not).
 * Ayrinti hizmetler sayfasinda.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'family', 'items' );
?>
<section class="mx-auto max-w-[76rem] px-6 pt-20 md:pt-28">

	<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between" data-reveal>
		<h2 class="title-condensed font-display text-[2.25rem] leading-[1.02] md:text-5xl" <?php nwcs_edit_attr( 'home', 'family', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'family', 'title' ) ); ?>
		</h2>
		<p class="text-base text-moss" <?php nwcs_edit_attr( 'home', 'family', 'note' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'family', 'note' ) ); ?>
		</p>
	</div>

	<ul class="mt-10 grid gap-4 md:grid-cols-2" data-reveal-stagger <?php nwcs_edit_attr( 'home', 'family', 'items' ); ?>>
		<?php foreach ( $items as $item_index => $item ) :
			$thumb = ahsapambalaj_image( nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'medium_large' ), 'family', $item_index );
			?>
			<li data-reveal>
				<a href="<?php echo esc_url( ahsapambalaj_link( $item['url'] ?? '' ) ); ?>"
					class="card group grid h-full grid-cols-[8.5rem_1fr] overflow-hidden border-l-4 border-l-transparent transition-colors hover:border-l-signal sm:grid-cols-[11rem_1fr]">

					<span class="block min-h-[8.5rem] overflow-hidden bg-dust">
						<?php if ( ! empty( $thumb['url'] ) ) : ?>
							<img src="<?php echo esc_url( $thumb['url'] ); ?>"
								alt="<?php echo esc_attr( $thumb['alt'] ?: ( $item['label'] ?? '' ) ); ?>"
								class="h-full w-full object-cover"
								loading="lazy" decoding="async" />
						<?php endif; ?>
					</span>

					<span class="flex flex-col justify-center px-5 py-5">
						<span class="title-condensed block font-display text-[1.6rem] leading-[1.05] text-ink transition-colors duration-200 group-hover:text-signal" <?php nwcs_edit_attr( 'home', 'family', 'items', $item_index, 'label' ); ?>>
							<?php echo esc_html( $item['label'] ?? '' ); ?>
						</span>
						<span class="mt-2 block text-sm leading-relaxed text-moss" <?php nwcs_edit_attr( 'home', 'family', 'items', $item_index, 'note' ); ?>>
							<?php echo esc_html( $item['note'] ?? '' ); ?>
						</span>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
