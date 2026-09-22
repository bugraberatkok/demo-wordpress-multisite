<?php
/**
 * Hizmetlerimiz. Dort urun tek bir karenin dort bolmesi olarak durur;
 * bolmelerin kesistigi noktada tek bir teklif dugmesi oturur.
 *
 * Bolme icerikleri ortalanir ve disa dogru hizalanir (ust sira yukari,
 * alt sira asagi); boylece ortadaki dugme hicbir metnin uzerine binmez.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$items = nwcs_rows( 'services', 'grid', 'items' );

// md: kademesi ayrica yazilmalidir, yoksa 'md:p-10' kisayolu bu payi ezer.
$cell_align = array(
	0 => 'justify-start sm:pb-28 md:pb-36',
	1 => 'justify-start sm:pb-28 md:pb-36',
	2 => 'justify-end sm:pt-28 md:pt-36',
	3 => 'justify-end sm:pt-28 md:pt-36',
);
?>
<article>

	<header class="mx-auto max-w-[76rem] px-6 pt-14 text-center md:pt-20">
		<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'services', 'head', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'services', 'head', 'title' ) ); ?>
		</h1>

		<p class="mx-auto mt-7 max-w-[42rem] text-xl leading-[1.55] md:text-2xl md:leading-[1.5]" <?php nwcs_edit_attr( 'services', 'head', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'services', 'head', 'lead' ) ); ?>
		</p>
	</header>

	<section class="mx-auto max-w-[80rem] px-6 pt-14 md:pt-20">

		<div class="relative mx-auto max-w-[74rem]">

			<div class="grid gap-px overflow-hidden rounded-sm bg-timber/45 p-px sm:grid-cols-2" <?php nwcs_edit_attr( 'services', 'grid', 'items' ); ?>>
				<?php foreach ( $items as $index => $item ) :
					$thumb = nwcs_image_by_id( $item['image'] ?? 0, 'medium_large' );
					?>
					<div class="flex min-h-[21rem] flex-col items-center bg-bone-deep p-7 text-center md:min-h-[24rem] md:p-10 <?php echo esc_attr( $cell_align[ $index ] ?? 'justify-start' ); ?>">

						<?php if ( ! empty( $thumb['url'] ) ) : ?>
							<img src="<?php echo esc_url( $thumb['url'] ); ?>"
								alt="<?php echo esc_attr( $thumb['alt'] ?: ( $item['title'] ?? '' ) ); ?>"
								class="mb-6 h-40 w-full max-w-[22rem] rounded-sm object-cover md:h-48"
								loading="lazy" decoding="async" />
						<?php endif; ?>

						<h2 class="font-display text-2xl font-semibold md:text-[1.75rem]">
							<?php echo esc_html( $item['title'] ?? '' ); ?>
						</h2>

						<p class="mx-auto mt-3 max-w-[26rem] text-[1.0625rem] leading-[1.7] text-ink/75">
							<?php echo esc_html( $item['text'] ?? '' ); ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'services', 'grid', 'center_url' ) ) ); ?>"
				class="quadbtn absolute left-1/2 top-1/2 z-10 hidden h-[10.5rem] w-[10.5rem] -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center rounded-full bg-forest text-center font-display text-lg font-semibold leading-tight text-bone ring-[12px] ring-bone-deep hover:bg-forest-deep sm:flex"
				<?php nwcs_edit_attr( 'services', 'grid', 'center_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'grid', 'center_label' ) ); ?>
			</a>
		</div>

		<p class="mt-6 text-center font-display text-sm text-moss" <?php nwcs_edit_attr( 'services', 'grid', 'center_note' ); ?>>
			<?php echo esc_html( nwcs_field( 'services', 'grid', 'center_note' ) ); ?>
		</p>

		<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'services', 'grid', 'center_url' ) ) ); ?>"
			class="mt-6 block rounded-pill bg-forest px-7 py-4 text-center font-display text-base font-semibold text-bone transition-colors hover:bg-forest-deep sm:hidden">
			<?php echo esc_html( nwcs_field( 'services', 'grid', 'center_label' ) ); ?>
		</a>
	</section>

	<section class="mx-auto max-w-[76rem] px-6 pt-16 md:pt-24">
		<div class="panel p-7 text-center md:p-10">
			<h2 class="font-display text-xl font-semibold md:text-2xl" <?php nwcs_edit_attr( 'services', 'note', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'note', 'title' ) ); ?>
			</h2>
			<p class="mx-auto mt-4 max-w-[46rem] text-[1.0625rem] leading-[1.7] text-ink/80" <?php nwcs_edit_attr( 'services', 'note', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'note', 'text' ) ); ?>
			</p>
		</div>
	</section>
</article>
<?php
get_footer();
