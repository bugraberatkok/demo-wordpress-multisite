<?php
/**
 * Modul: kullanim alanlari (kavakkeresteci). Kavak keresteyi alan kisi
 * genelde ne yapacagini bilir; bu bant "bu isime uyar mi?" sorusunu yanitlar.
 * Solda baslik, sagda alanlar; fotograf eklenmis alan fotografla gorunur.
 */

defined( 'ABSPATH' ) || exit;

$items = nwcs_rows( 'home', 'uses', 'items' );

if ( ! $items ) {
	return;
}
?>
<section class="mt-20 bg-stone md:mt-28">
	<div class="mx-auto grid max-w-[78rem] gap-10 px-5 py-16 md:px-8 md:py-20 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">

		<div>
			<h2 class="text-[2.25rem] md:text-[3rem]" <?php nwcs_edit_attr( 'home', 'uses', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'uses', 'title' ) ); ?>
			</h2>
			<p class="mt-4 max-w-[26rem] text-lg leading-relaxed text-muted" <?php nwcs_edit_attr( 'home', 'uses', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'uses', 'text' ) ); ?>
			</p>
		</div>

		<ul class="grid gap-x-8 gap-y-9 sm:grid-cols-2" <?php nwcs_edit_attr( 'home', 'uses', 'items' ); ?>>
			<?php foreach ( $items as $index => $item ) :
				$photo = nwcs_image_by_id( (int) ( $item['image'] ?? 0 ), 'medium_large' );
				?>
				<li class="border-t-2 border-mark pt-4">
					<?php if ( $photo['url'] ) : ?>
						<div class="shot mb-4 aspect-[4/3]" <?php nwcs_edit_attr( 'home', 'uses', 'items', (int) $index, 'image' ); ?>>
							<?php echo kr_image_tag( $photo, '', (string) ( $item['title'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
						</div>
					<?php endif; ?>
					<h3 class="text-[1.5rem]" <?php nwcs_edit_attr( 'home', 'uses', 'items', (int) $index, 'title' ); ?>><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p class="mt-1.5 text-muted" <?php nwcs_edit_attr( 'home', 'uses', 'items', (int) $index, 'text' ); ?>><?php echo esc_html( $item['text'] ?? '' ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
