<?php
/**
 * Kurumsal metin bolumu.
 */

defined( 'ABSPATH' ) || exit;

$image  = nwcs_image( 'inner', 'story', 'image' );
$points = nwcs_rows( 'inner', 'story', 'points' );
?>
<section class="k-section" data-nwcs-section="story">
	<div class="k-wrap k-story">
		<div>
			<div class="k-section-head">
				<h2 class="k-section-title"><?php echo esc_html( nwcs_field( 'inner', 'story', 'title' ) ); ?></h2>
			</div>
			<div class="k-story__body"><?php echo esc_html( nwcs_field( 'inner', 'story', 'body' ) ); ?></div>

			<?php if ( $points ) : ?>
				<ul class="k-points">
					<?php foreach ( $points as $point ) : ?>
						<li>
							<?php nwcs_the_icon( $point['icon'] ?? '', 'k-icon', 20 ); ?>
							<span><?php echo esc_html( $point['text'] ?? '' ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="k-story__media">
			<?php echo kocist_image_tag( $image, '', 'Örnek görsel — kurumsal' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>
	</div>
</section>
