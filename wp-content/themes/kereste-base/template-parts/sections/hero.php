<?php
/**
 * Hero: tam genislik paket ucu fotografi, solda baslik ve dugmeler, sagda
 * ipe asili buyuk paket etiketi. Etiket sitenin parti etiketidir: ustte
 * rozet ve ad, altinda sablon harfli urun listesi (her satir urun sayfasina
 * gider). Urun kartlarindaki etiket fikrinin hero boyu.
 *
 * Fotograf notu (image_note) doluysa fotografin kosesinde gorunur; yapay
 * zekayla uretilen ornek gorsel "Ornek gorsel" olarak isaretlenir, gercek
 * fotograf yuklenince not silinir.
 *
 * Mobilde etiket gizlenir: urun kartlari hemen asagida.
 */

defined( 'ABSPATH' ) || exit;

$image    = nwcs_image( 'home', 'hero', 'image', '2048x2048' );
$note     = trim( (string) nwcs_field( 'home', 'hero', 'image_note' ) );
$products = kr_products();
$srcset   = $image['id'] ? (string) wp_get_attachment_image_srcset( $image['id'], '2048x2048' ) : '';
?>
<section class="hero relative isolate overflow-hidden bg-ink text-paper">

	<div class="absolute inset-0 -z-10" <?php nwcs_edit_attr( 'home', 'hero', 'image' ); ?>>
		<?php if ( $image['url'] ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>"
				<?php if ( $srcset ) : ?>srcset="<?php echo esc_attr( $srcset ); ?>" sizes="100vw"<?php endif; ?>
				alt="<?php echo esc_attr( $image['alt'] ); ?>" class="h-full w-full object-cover" fetchpriority="high" decoding="async" />
		<?php endif; ?>
		<div class="hero__veil absolute inset-0" aria-hidden="true"></div>
	</div>

	<div class="mx-auto grid min-h-[34rem] max-w-[78rem] items-end gap-10 px-5 pb-14 pt-40 md:min-h-[40rem] md:px-8 md:pb-20 lg:min-h-[44rem] lg:grid-cols-[1fr_20rem] lg:pt-0">

		<div class="max-w-[42rem]">
			<h1 class="text-[2.75rem] leading-[1] text-paper sm:text-[3.75rem] lg:text-[4.75rem]" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>>
				<?php echo kr_multiline( nwcs_field( 'home', 'hero', 'title' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</h1>
			<p class="mt-6 max-w-[34rem] text-lg leading-relaxed text-paper/85 md:text-xl" <?php nwcs_edit_attr( 'home', 'hero', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'lead' ) ); ?>
			</p>
			<div class="mt-8 flex flex-wrap gap-3">
				<a href="<?php echo esc_url( kr_quote_fallback_url() ); ?>" data-kr-quote class="btn btn--lg btn--mark max-sm:w-full">
					<?php echo esc_html( nwcs_field( 'global', 'header', 'cta_label' ) ); ?>
				</a>
				<a href="<?php echo esc_url( kr_link( kr_page_path( 'products', '/urunler/' ) ) ); ?>" class="btn btn--lg btn--ghost max-sm:w-full"
					<?php nwcs_edit_attr( 'home', 'hero', 'secondary' ); ?>>
					<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary' ) ); ?>
				</a>
			</div>
		</div>

		<?php if ( $products ) : ?>
			<?php // Ip hero'nun ust kenarindan iner; etiket ipin ucunda hafif yatik durur. ?>
			<nav class="tag tag--hero hidden self-start text-ink lg:block" aria-label="Ürünler">
				<p class="flex items-center gap-3 border-b-[1.5px] border-dashed border-ink/25 px-6 pb-4 pt-12">
					<span class="flex h-9 w-9 items-center justify-center bg-mark font-stencil text-[0.95rem] leading-none text-paper" aria-hidden="true">
						<?php echo esc_html( nwcs_field( 'global', 'header', 'logo_mark' ) ); ?>
					</span>
					<span class="font-slab text-[1.25rem] font-bold leading-none">
						<?php echo esc_html( nwcs_field( 'global', 'header', 'logo_word' ) ); ?><span class="font-semibold text-muted"><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_rest' ) ); ?></span>
					</span>
				</p>
				<ul class="px-6 pb-5 pt-2">
					<?php foreach ( $products as $product ) : ?>
						<li class="border-b border-line last:border-b-0">
							<a href="<?php echo esc_url( $product['url'] ); ?>" class="group/row block py-3 text-ink no-underline">
								<span class="stencil block text-[1.15rem]"><?php echo esc_html( $product['mark'] ); ?></span>
								<span class="mt-1 block font-semibold leading-snug group-hover/row:text-mark"><?php echo esc_html( $product['name'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>

	<?php if ( $note ) : ?>
		<p class="absolute bottom-0 right-0 bg-ink/80 px-3 py-1.5 text-[0.8125rem] font-semibold text-paper/90" <?php nwcs_edit_attr( 'home', 'hero', 'image_note' ); ?>>
			<?php echo esc_html( $note ); ?>
		</p>
	<?php endif; ?>
</section>
