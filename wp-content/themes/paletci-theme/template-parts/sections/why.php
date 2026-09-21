<?php
/**
 * Neden biz bolumu.
 */

defined( 'ABSPATH' ) || exit;

$image = nwcs_image( 'home', 'why', 'image' );
$items = nwcs_rows( 'home', 'why', 'items' );
?>
<section class="p-section p-section--cream" id="neden-biz" data-nwcs-section="why">
	<div class="p-wrap p-why">
		<div class="p-why__media" <?php nwcs_edit_attr( 'home', 'why', 'image' ); ?>>
			<?php echo paletci_image_tag( $image, '', 'Örnek görsel — atölye' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>

		<div>
			<div class="p-head p-head--left">
				<h2 class="p-title" <?php nwcs_edit_attr( 'home', 'why', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'why', 'title' ) ); ?></h2>
			</div>

			<ul class="p-why__list">
				<?php foreach ( $items as $index => $item ) : ?>
					<li class="p-why__item" <?php nwcs_edit_attr( 'home', 'why', 'items', $index, 'title' ); ?>>
						<span class="p-why__icon"><?php nwcs_the_icon( $item['icon'] ?? '', 'p-icon', 22 ); ?></span>
						<div>
							<h3 class="p-why__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
							<p class="p-why__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
