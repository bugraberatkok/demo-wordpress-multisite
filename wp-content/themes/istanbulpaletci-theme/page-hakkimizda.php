<?php
/**
 * Hakkimizda: firma metni yaninda saha fotografi, altinda calisma ilkeleri.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$photo  = nwcs_image( 'about', 'head', 'image', '1536x1536' );
$second = nwcs_image( 'about', 'values', 'image', 'large' );
$values = nwcs_rows( 'about', 'values', 'items' );
?>
<article>

	<div class="mx-auto grid max-w-[80rem] items-start gap-12 px-5 pt-14 md:px-8 md:pt-20 lg:grid-cols-[1fr_0.85fr] lg:gap-16">

		<div>
			<h1 class="text-[3rem] font-bold leading-[0.95] md:text-[4.5rem]" <?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?>
			</h1>

			<p class="mt-6 max-w-[36rem] text-xl font-medium leading-snug md:text-2xl" <?php nwcs_edit_attr( 'about', 'head', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'head', 'lead' ) ); ?>
			</p>

			<div class="mt-12 border-t border-line pt-10">
				<h2 class="text-[2rem] font-semibold md:text-[2.5rem]" <?php nwcs_edit_attr( 'about', 'story', 'title' ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'story', 'title' ) ); ?>
				</h2>

				<div class="reading mt-5 text-lg leading-relaxed text-ink/85">
					<?php foreach ( array( 'p1', 'p2', 'p3' ) as $key ) :
						$text = trim( (string) nwcs_field( 'about', 'story', $key ) );

						if ( '' === $text ) {
							continue;
						}
						?>
						<p <?php nwcs_edit_attr( 'about', 'story', $key ); ?>><?php echo esc_html( $text ); ?></p>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<figure class="sheet p-2 lg:sticky lg:top-28">
			<div class="shot aspect-[4/5]" <?php nwcs_edit_attr( 'about', 'head', 'image' ); ?>>
				<?php echo ip_image_tag( $photo, '', 'Atölye önünde ahşap sandık', true ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>
		</figure>
	</div>

	<section class="mx-auto grid max-w-[80rem] items-center gap-12 px-5 pt-24 md:px-8 md:pt-32 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">

		<figure class="sheet order-2 p-2 lg:order-1">
			<div class="shot aspect-[4/3]" <?php nwcs_edit_attr( 'about', 'values', 'image' ); ?>>
				<?php echo ip_image_tag( $second, '', 'Atölyede istiflenmiş ahşap sandıklar' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>
		</figure>

		<div class="order-1 lg:order-2">
			<h2 class="text-[2.5rem] font-semibold md:text-5xl" <?php nwcs_edit_attr( 'about', 'values', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'values', 'title' ) ); ?>
			</h2>

			<ul class="mt-10 grid gap-x-8 gap-y-8 sm:grid-cols-2" <?php nwcs_edit_attr( 'about', 'values', 'items' ); ?>>
				<?php foreach ( $values as $item ) : ?>
					<li class="border-t-2 border-ink pt-4">
						<h3 class="text-lg font-semibold"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
						<p class="mt-2 text-[0.9375rem] leading-relaxed text-steel"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<?php get_template_part( 'template-parts/cta-band', null, array( 'page' => 'about', 'component' => 'cta' ) ); ?>
</article>
<?php
get_footer();
