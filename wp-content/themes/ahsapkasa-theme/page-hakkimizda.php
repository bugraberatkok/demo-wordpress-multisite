<?php
/**
 * Hakkimizda. Metinler ahsapkasa.com'daki icerigin aynisi; duzen okuma
 * rahatligi icin kurulmustur: dar Literata kolonu, basliklar solda.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$image = nwcs_image( 'about', 'head', 'image', 'full' );

$blocks = array(
	array( 'component' => 'purpose' ),
	array( 'component' => 'quality' ),
);
?>
<article>

	<header class="mx-auto max-w-[76rem] px-6 pt-14 md:pt-20">
		<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>>
			<?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?>
		</h1>

		<p class="reading mt-8 text-xl leading-[1.55] md:text-2xl md:leading-[1.5]" <?php nwcs_edit_attr( 'about', 'head', 'lead' ); ?>>
			<?php echo esc_html( nwcs_field( 'about', 'head', 'lead' ) ); ?>
		</p>
	</header>

	<figure class="mt-12 md:mt-16">
		<?php echo ahsapkasa_image_tag( $image, 'h-[34vh] max-h-[460px] w-full object-cover md:h-[44vh]', 'Koçist üretim alanı' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
	</figure>

	<section class="mx-auto max-w-[76rem] px-6 pt-16 md:pt-20">
		<div class="reading">
			<p class="text-base text-ink/85" <?php nwcs_edit_attr( 'about', 'story', 'p1' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'story', 'p1' ) ); ?>
			</p>
			<p class="mt-6 text-base text-ink/85" <?php nwcs_edit_attr( 'about', 'story', 'p2' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'story', 'p2' ) ); ?>
			</p>
		</div>
	</section>

	<section class="mx-auto max-w-[76rem] px-6 pt-16 md:pt-24">
		<?php foreach ( $blocks as $block ) :
			$component = $block['component'];
			?>
			<div class="grid gap-4 border-t border-timber/35 py-10 md:grid-cols-[17rem_1fr] md:gap-12 md:py-12">
				<h2 class="font-display text-xl font-semibold md:text-2xl" <?php nwcs_edit_attr( 'about', $component, 'title' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', $component, 'title' ) ); ?>
				</h2>

				<p class="reading text-base text-ink/85" <?php nwcs_edit_attr( 'about', $component, 'text' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', $component, 'text' ) ); ?>
				</p>
			</div>
		<?php endforeach; ?>
	</section>
</article>
<?php
get_footer();
