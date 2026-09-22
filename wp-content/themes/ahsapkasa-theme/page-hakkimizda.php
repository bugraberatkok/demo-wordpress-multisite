<?php
/**
 * Hakkimizda. Solda metin, sagda tam boy gorsel; altta Amacimiz ve
 * Kalite Politikamiz yan yana iki kutu.
 *
 * Metinler ahsapkasa.com'daki icerigin aynisidir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$image = nwcs_image( 'about', 'head', 'image', 'large' );

$blocks = array( 'purpose', 'quality' );
?>
<article>

	<section class="mx-auto max-w-[76rem] px-6 pt-14 md:pt-20">
		<div class="grid items-start gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">

			<div>
				<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?>
				</h1>

				<p class="mt-7 text-xl leading-[1.5] md:text-2xl" <?php nwcs_edit_attr( 'about', 'head', 'lead' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'head', 'lead' ) ); ?>
				</p>

				<p class="mt-8 text-[1.0625rem] leading-[1.75] text-ink/85" <?php nwcs_edit_attr( 'about', 'story', 'p1' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'story', 'p1' ) ); ?>
				</p>

				<p class="mt-5 text-[1.0625rem] leading-[1.75] text-ink/85" <?php nwcs_edit_attr( 'about', 'story', 'p2' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'story', 'p2' ) ); ?>
				</p>
			</div>

			<figure class="lg:sticky lg:top-28" <?php nwcs_edit_attr( 'about', 'head', 'image' ); ?>>
				<?php echo ahsapkasa_image_tag( $image, 'h-[22rem] w-full rounded-sm object-cover md:h-[30rem] lg:h-[36rem]', 'Koçist üretim alanı' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</figure>
		</div>
	</section>

	<section class="mx-auto max-w-[76rem] px-6 pt-16 md:pt-24">
		<div class="grid items-start gap-6 md:grid-cols-2 md:gap-8">
			<?php foreach ( $blocks as $component ) : ?>
				<div class="panel p-7 md:p-9">
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
