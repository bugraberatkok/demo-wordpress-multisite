<?php
/**
 * Hizmetlerimiz. Dort bagimsiz kutu, aralarinda bosluk; birlikte karemsi bir
 * dizilim olusturur. Her kutuda solda yazi, sagda gorsel. Kutularin kesistigi
 * bosluga tek bir teklif dugmesi oturur.
 *
 * Kutu iceriginin ic tarafinda dugmeye pay birakilir. 'md:' kademesi ayrica
 * yazilmalidir, yoksa 'md:p-8' kisayolu bu payi ezer.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$items = nwcs_rows( 'services', 'grid', 'items' );

$cell_align = array(
	0 => 'sm:pb-16 md:pb-20',
	1 => 'sm:pb-16 md:pb-20',
	2 => 'sm:pt-16 md:pt-20',
	3 => 'sm:pt-16 md:pt-20',
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

	<section class="mx-auto max-w-[80rem] px-6 pt-12 md:pt-16">

		<div class="relative mx-auto max-w-[72rem]">

			<div class="grid gap-7 sm:grid-cols-2 sm:gap-10" <?php nwcs_edit_attr( 'services', 'grid', 'items' ); ?>>
				<?php foreach ( $items as $index => $item ) :
					$thumb = nwcs_image_by_id( $item['image'] ?? 0, 'medium_large' );
					?>
					<div class="card flex items-start gap-5 p-7 md:gap-7 md:p-8 <?php echo esc_attr( $cell_align[ $index ] ?? '' ); ?>">

						<div class="min-w-0 flex-1">
							<h2 class="font-display text-2xl font-semibold md:text-[1.625rem]">
								<?php echo esc_html( $item['title'] ?? '' ); ?>
							</h2>

							<p class="mt-3 text-[1.0625rem] leading-[1.7] text-ink/75">
								<?php echo esc_html( $item['text'] ?? '' ); ?>
							</p>
						</div>

						<?php if ( ! empty( $thumb['url'] ) ) : ?>
							<img src="<?php echo esc_url( $thumb['url'] ); ?>"
								alt="<?php echo esc_attr( $thumb['alt'] ?: ( $item['title'] ?? '' ) ); ?>"
								class="h-32 w-32 shrink-0 rounded-sm object-cover md:h-40 md:w-40"
								loading="lazy" decoding="async" />
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<a href="<?php echo esc_url( ahsapkasa_link( nwcs_field( 'services', 'grid', 'center_url' ) ) ); ?>"
				class="quadbtn absolute left-1/2 top-1/2 z-10 hidden h-[8.5rem] w-[8.5rem] -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center rounded-full bg-forest text-center font-display text-base font-semibold leading-tight text-bone ring-8 ring-bone hover:bg-forest-deep sm:flex"
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
