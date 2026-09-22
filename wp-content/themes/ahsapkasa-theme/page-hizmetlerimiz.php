<?php
/**
 * Hizmetlerimiz. Dort urun tek bir karenin dort bolmesi olarak durur;
 * bolmelerin kesistigi noktada tek bir teklif dugmesi oturur.
 *
 * Bolme icerikleri disa dogru hizalanir (ust sira yukari, alt sira asagi),
 * boylece ortadaki dugme hicbir metnin uzerine binmez.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$items = nwcs_rows( 'services', 'grid', 'items' );

// Ust sira yukari, alt sira asagi hizalanir; ortadaki dugmeye pay birakilir.
// md: kademesi ayrica yazilmalidir, yoksa 'md:p-9' kisayolu bu payi ezer.
$cell_align = array(
	0 => 'justify-start sm:pb-32 md:pb-40',
	1 => 'justify-start sm:pb-32 md:pb-40',
	2 => 'justify-end sm:pt-32 md:pt-40',
	3 => 'justify-end sm:pt-32 md:pt-40',
);
?>
<article>

	<header class="mx-auto max-w-[76rem] px-6 pt-14 md:pt-20">
		<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'services', 'head', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'services', 'head', 'title' ) ); ?>
		</h1>

		<p class="reading mt-8 text-xl leading-[1.55] md:text-2xl md:leading-[1.5]" <?php nwcs_edit_attr( 'services', 'head', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'services', 'head', 'lead' ) ); ?>
		</p>
	</header>

	<section class="mx-auto max-w-[76rem] px-6 pt-14 md:pt-20">

		<div class="relative mx-auto max-w-[64rem]">

			<div class="grid gap-px overflow-hidden rounded-sm bg-timber/40 p-px sm:grid-cols-2" <?php nwcs_edit_attr( 'services', 'grid', 'items' ); ?>>
				<?php foreach ( $items as $index => $item ) :
					$thumb = nwcs_image_by_id( $item['image'] ?? 0, 'medium' );
					?>
					<div class="flex min-h-[19rem] flex-col bg-bone p-7 md:min-h-[22rem] md:p-9 <?php echo esc_attr( $cell_align[ $index ] ?? 'justify-start' ); ?>">

						<?php if ( ! empty( $thumb['url'] ) ) : ?>
							<img src="<?php echo esc_url( $thumb['url'] ); ?>"
								alt="<?php echo esc_attr( $thumb['alt'] ?: ( $item['title'] ?? '' ) ); ?>"
								class="mb-5 h-28 w-40 rounded-sm object-cover md:h-32 md:w-48"
								loading="lazy" decoding="async" />
						<?php endif; ?>

						<h2 class="font-display text-2xl font-semibold md:text-[1.75rem]">
							<?php echo esc_html( $item['title'] ?? '' ); ?>
						</h2>

						<p class="mt-3 max-w-[30rem] text-base text-ink/75">
							<?php echo esc_html( $item['text'] ?? '' ); ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'services', 'grid', 'center_url' ) ) ); ?>"
				class="quadbtn absolute left-1/2 top-1/2 z-10 hidden h-[9.5rem] w-[9.5rem] -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center rounded-full bg-forest text-center font-display text-lg font-semibold leading-tight text-bone ring-[10px] ring-bone hover:bg-forest-deep sm:flex"
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
		<div class="grid gap-4 border-t border-timber/35 py-10 md:grid-cols-[17rem_1fr] md:gap-12 md:py-12">
			<h2 class="font-display text-xl font-semibold md:text-2xl" <?php nwcs_edit_attr( 'services', 'note', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'note', 'title' ) ); ?>
			</h2>
			<p class="reading text-base text-ink/85" <?php nwcs_edit_attr( 'services', 'note', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'services', 'note', 'text' ) ); ?>
			</p>
		</div>
	</section>
</article>
<?php
get_footer();
