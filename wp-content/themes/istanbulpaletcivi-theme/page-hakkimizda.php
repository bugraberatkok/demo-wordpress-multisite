<?php
/**
 * Hakkimizda: profil, kalite politikasi (madde madde), Kocist Grup.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$quality = nwcs_rows( 'about', 'quality', 'items' );
$image   = pc_img( array(), 'hero.jpg', 'Palet üretiminde çivi tabancasıyla çakım' );

pc_part(
	'page-head',
	array(
		'title' => (string) nwcs_field( 'about', 'head', 'title' ),
		'lead'  => (string) nwcs_field( 'about', 'head', 'lead' ),
		'page'  => 'about',
	)
);
?>

<section class="pc-section">
	<div class="pc-wrap pc-split">
		<div>
			<h2 class="pc-split__title" <?php nwcs_edit_attr( 'about', 'profile', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'profile', 'title' ) ); ?></h2>
			<div class="pc-prose" <?php nwcs_edit_attr( 'about', 'profile', 'text' ); ?>>
				<?php echo pc_paragraphs( (string) nwcs_field( 'about', 'profile', 'text' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>
		</div>
		<figure class="pc-split__figure">
			<?php echo pc_img_tag( $image, 'pc-split__img' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			<span class="pc-sample"<?php nwcs_edit_attr( 'global', 'common', 'sample_note' ); ?>><?php echo esc_html( nwcs_field( 'global', 'common', 'sample_note' ) ); ?></span>
		</figure>
	</div>
</section>

<?php if ( $quality ) : ?>
	<section class="pc-section pc-section--steel" aria-labelledby="pc-quality">
		<div class="pc-wrap pc-uses">
			<header class="pc-head pc-uses__head">
				<h2 id="pc-quality" <?php nwcs_edit_attr( 'about', 'quality', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'quality', 'title' ) ); ?></h2>
			</header>
			<ul class="pc-ticks pc-ticks--wide" <?php nwcs_edit_attr( 'about', 'quality', 'items' ); ?>>
				<?php foreach ( $quality as $index => $item ) : ?>
					<li<?php nwcs_edit_attr( 'about', 'quality', 'items', (int) $index, 'text' ); ?>><?php echo esc_html( $item['text'] ?? '' ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<section class="pc-section">
	<div class="pc-wrap pc-narrow">
		<h2 class="pc-split__title" <?php nwcs_edit_attr( 'about', 'group', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'group', 'title' ) ); ?></h2>
		<div class="pc-prose" <?php nwcs_edit_attr( 'about', 'group', 'text' ); ?>>
			<?php echo pc_paragraphs( (string) nwcs_field( 'about', 'group', 'text' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>
	</div>
</section>

<?php
get_footer();
