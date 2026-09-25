<?php
/**
 * Hakkimizda: kisa firma metni, fotograf ve ilkeler.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$photo  = nwcs_image( 'about', 'story', 'image', 'large' );
$values = nwcs_rows( 'about', 'values', 'rows' );
?>
<article>
	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'about' ) ); ?>

	<div class="mx-auto grid max-w-[78rem] items-start gap-10 px-5 pt-12 md:px-8 md:pt-16 lg:grid-cols-[1fr_1fr] lg:gap-14">
		<div class="shot aspect-[16/10]" <?php nwcs_edit_attr( 'about', 'story', 'image' ); ?>>
			<?php echo kr_image_tag( $photo, '', 'Kereste sevkiyatı' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>

		<div class="reading text-lg leading-relaxed">
			<?php foreach ( array( 'p1', 'p2' ) as $key ) :
				$text = trim( (string) nwcs_field( 'about', 'story', $key ) );
				if ( '' === $text ) {
					continue;
				}
				?>
				<p <?php nwcs_edit_attr( 'about', 'story', $key ); ?>><?php echo esc_html( $text ); ?></p>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $values ) : ?>
		<ul class="mx-auto mt-16 grid max-w-[78rem] gap-8 px-5 md:mt-20 md:grid-cols-3 md:px-8" <?php nwcs_edit_attr( 'about', 'values', 'rows' ); ?>>
			<?php foreach ( $values as $index => $value ) : ?>
				<li class="border-t-4 border-mark pt-4">
					<h2 class="text-[1.6rem]" <?php nwcs_edit_attr( 'about', 'values', 'rows', (int) $index, 'title' ); ?>><?php echo esc_html( $value['title'] ?? '' ); ?></h2>
					<p class="mt-2 text-muted" <?php nwcs_edit_attr( 'about', 'values', 'rows', (int) $index, 'text' ); ?>><?php echo esc_html( $value['text'] ?? '' ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</article>
<?php
get_footer();
