<?php
/**
 * Ana sayfa: kereste cesitleri, paket etiketi kartlariyla.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mx-auto max-w-[78rem] px-5 pt-20 md:px-8 md:pt-28">
	<div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
		<div class="max-w-[36rem]">
			<h2 class="text-[2.25rem] md:text-[3rem]" <?php nwcs_edit_attr( 'home', 'products', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'products', 'title' ) ); ?>
			</h2>
			<p class="mt-3 text-lg text-muted" <?php nwcs_edit_attr( 'home', 'products', 'text' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'products', 'text' ) ); ?>
			</p>
		</div>
		<a href="<?php echo esc_url( kr_link( kr_page_path( 'products', '/urunler/' ) ) ); ?>" class="btn btn--md btn--outline self-start md:self-auto"
			<?php nwcs_edit_attr( 'home', 'products', 'link_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'products', 'link_label' ) ); ?>
		</a>
	</div>

	<?php // Etiketlerin ipi yukari tasar; ust bosluk ona yer acar. ?>
	<ul class="mt-14 grid gap-x-5 gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
		<?php foreach ( kr_products() as $product ) : ?>
			<li><?php get_template_part( 'template-parts/product-tag', null, array( 'product' => $product, 'hole' => 'var(--color-paper)' ) ); ?></li>
		<?php endforeach; ?>
	</ul>
</section>
