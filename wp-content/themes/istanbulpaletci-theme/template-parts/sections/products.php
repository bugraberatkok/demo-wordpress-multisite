<?php
/**
 * Ana sayfa: urun cesitlerimiz.
 *
 * Bes urun + altinci hucrede "ozel olcu" karti; boylece 3 + 3 izgara dolar
 * ve bos hucre kalmaz.
 */

defined( 'ABSPATH' ) || exit;

$hub = ip_link( ip_manifest()['pages']['products']['path'] ?? '/urunlerimiz/' );
?>
<section class="mx-auto max-w-[80rem] px-5 pt-24 md:px-8 md:pt-32">

	<?php get_template_part( 'template-parts/section-head', null, array( 'page' => 'home', 'component' => 'products', 'url' => $hub ) ); ?>

	<ul class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
		<?php foreach ( ip_products() as $product ) : ?>
			<li><?php get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) ); ?></li>
		<?php endforeach; ?>

		<li>
			<div class="flex h-full flex-col justify-between gap-8 rounded-[4px] bg-indigo-wash p-6 md:p-8">
				<div>
					<h3 class="text-xl font-semibold text-indigo-deep" <?php nwcs_edit_attr( 'products', 'custom', 'title' ); ?>>
						<?php echo esc_html( nwcs_field( 'products', 'custom', 'title' ) ); ?>
					</h3>
					<p class="mt-3 text-[0.9375rem] leading-relaxed text-ink/80" <?php nwcs_edit_attr( 'products', 'custom', 'text' ); ?>>
						<?php echo esc_html( nwcs_field( 'products', 'custom', 'text' ) ); ?>
					</p>
				</div>

				<a href="<?php echo esc_url( ip_link( nwcs_field( 'products', 'custom', 'button_url' ) ) ); ?>"
					class="btn btn--md btn--solid self-start" <?php nwcs_edit_attr( 'products', 'custom', 'button_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'products', 'custom', 'button_label' ) ); ?>
				</a>
			</div>
		</li>
	</ul>
</section>
