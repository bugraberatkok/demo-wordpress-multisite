<?php
/**
 * Hakkimizda: baslik, genis fotograf, hikaye, calisma bicimi, siparis sureci.
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/page-head', null, array( 'page' => 'about' ) );

$image = ik_image_or_default( nwcs_image( 'about', 'head', 'image', 'full' ), 'hero-orman.jpg', 'Orman yolunun kenarına istiflenmiş tomruklar' );
$items = nwcs_rows( 'about', 'values', 'items' );
?>
<?php if ( ! empty( $image['url'] ) ) : ?>
	<div class="ik-wrap ik-about-page__media">
		<?php echo ik_image_tag( $image, 'ik-about-page__image', null, 'eager', ik_edit_attrs( 'about', 'head', 'image' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
	</div>
<?php endif; ?>

<section class="ik-section ik-story" aria-labelledby="ik-story-title">
	<div class="ik-wrap ik-story__grid">
		<h2 class="ik-title" id="ik-story-title" <?php nwcs_edit_attr( 'about', 'story', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'story', 'title' ) ); ?></h2>
		<div class="ik-prose" <?php nwcs_edit_attr( 'about', 'story', 'text' ); ?>>
			<?php ik_paragraphs( nwcs_field( 'about', 'story', 'text' ) ); ?>
		</div>
	</div>
</section>

<?php if ( $items ) : ?>
	<section class="ik-section ik-values" aria-labelledby="ik-values-title">
		<div class="ik-wrap">
			<h2 class="ik-title" id="ik-values-title" <?php nwcs_edit_attr( 'about', 'values', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'values', 'title' ) ); ?></h2>
			<ul class="ik-values__list" <?php nwcs_edit_attr( 'about', 'values', 'items' ); ?>>
				<?php foreach ( $items as $item ) : ?>
					<li class="ik-values__item">
						<span class="ik-values__icon"><?php ik_icon( (string) ( $item['icon'] ?? '' ), 28 ); ?></span>
						<h3 class="ik-values__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
						<p><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php
ik_section( 'process' );
ik_section( 'ctaband' );

get_footer();
