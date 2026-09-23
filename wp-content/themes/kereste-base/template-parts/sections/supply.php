<?php
/**
 * Modul: toptan tedarik ve sevkiyat (ithalkeresteci). Koyu bant; solda
 * metin ve one cikanlar, sagda yukleme fotograflari.
 */

defined( 'ABSPATH' ) || exit;

$points = nwcs_rows( 'home', 'supply', 'points' );
$photos = array_values(
	array_filter(
		array_map(
			static fn( $row ): array => nwcs_image_by_id( (int) ( $row['image'] ?? 0 ), 'large' ),
			nwcs_rows( 'home', 'supply', 'photos' )
		),
		static fn( array $image ): bool => '' !== $image['url']
	)
);
?>
<section class="mt-20 bg-ink text-paper md:mt-28">
	<div class="mx-auto grid max-w-[78rem] gap-10 px-5 py-16 md:px-8 md:py-20 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:gap-14">

		<div>
			<h2 class="text-[2.25rem] md:text-[3rem]" <?php nwcs_edit_attr( 'home', 'supply', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'supply', 'title' ) ); ?>
			</h2>
			<p class="mt-4 max-w-[30rem] text-lg leading-relaxed text-paper/75" <?php nwcs_edit_attr( 'home', 'supply', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'supply', 'text' ) ); ?>
			</p>

			<?php if ( $points ) : ?>
				<dl class="mt-10 grid gap-6 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3" <?php nwcs_edit_attr( 'home', 'supply', 'points' ); ?>>
					<?php foreach ( $points as $point ) : ?>
						<div class="border-t-2 border-mark pt-3">
							<dt class="sr-only"><?php echo esc_html( $point['label'] ?? '' ); ?></dt>
							<dd>
								<span class="block font-slab text-[1.75rem] font-bold leading-none"><?php echo esc_html( $point['value'] ?? '' ); ?></span>
								<span class="mt-1.5 block text-[0.9375rem] text-paper/70"><?php echo esc_html( $point['label'] ?? '' ); ?></span>
							</dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>

		<?php if ( $photos ) : ?>
			<div class="grid grid-cols-2 gap-3" <?php nwcs_edit_attr( 'home', 'supply', 'photos' ); ?>>
				<?php foreach ( $photos as $index => $photo ) : ?>
					<div class="shot <?php echo 0 === $index ? 'col-span-2 aspect-[16/9]' : 'aspect-[4/3]'; ?>">
						<?php echo kr_image_tag( $photo, '', 'Kereste sevkiyatı' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
