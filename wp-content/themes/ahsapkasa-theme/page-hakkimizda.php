<?php
/**
 * Hakkimizda. Solda kutulara bolunmus metin, sagda cerceveli gorsel;
 * altta Amacimiz ve Kalite Politikamiz esit boyda iki kutu.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$image = nwcs_image( 'about', 'head', 'image', 'large' );

$story  = array( 'p1', 'p2' );
$blocks = array( 'purpose', 'quality' );
?>
<article>

	<section class="mx-auto max-w-[76rem] px-6 pt-14 md:pt-20">
		<div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-14">

			<div>
				<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?>
				</h1>

				<p class="mt-6 text-xl leading-[1.5] md:text-2xl" <?php nwcs_edit_attr( 'about', 'head', 'lead' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'head', 'lead' ) ); ?>
				</p>

				<?php // Uzun metin tek blok halinde yorucu; iki kutuya bolundu. ?>
				<div class="mt-8 space-y-5">
					<?php foreach ( $story as $key ) : ?>
						<p class="card px-6 py-6 text-[1.0625rem] leading-[1.75] text-ink/85 md:px-7"
							<?php nwcs_edit_attr( 'about', 'story', $key ); ?>>
							<?php echo esc_html( nwcs_field( 'about', 'story', $key ) ); ?>
						</p>
					<?php endforeach; ?>
				</div>
			</div>

			<figure class="frame lg:h-full" <?php nwcs_edit_attr( 'about', 'head', 'image' ); ?>>
				<?php echo ahsapkasa_image_tag( $image, 'h-[22rem] w-full object-cover md:h-[30rem] lg:h-full', 'Koçist üretim alanı' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</figure>
		</div>
	</section>

	<section class="mx-auto max-w-[76rem] px-6 pt-14 md:pt-20">
		<div class="grid gap-6 md:grid-cols-2 md:gap-8">
			<?php foreach ( $blocks as $component ) : ?>
				<div class="panel flex h-full flex-col p-7 md:p-9">
					<h2 class="font-display text-xl font-semibold md:text-2xl" <?php nwcs_edit_attr( 'about', $component, 'title' ); ?>>
						<?php echo esc_html( nwcs_field( 'about', $component, 'title' ) ); ?>
					</h2>

					<p class="mt-4 text-[1.0625rem] leading-[1.7] text-ink/80" <?php nwcs_edit_attr( 'about', $component, 'text' ); ?>>
						<?php echo esc_html( nwcs_field( 'about', $component, 'text' ) ); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</article>
<?php
get_footer();
