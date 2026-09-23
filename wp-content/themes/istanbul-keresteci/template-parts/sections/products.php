<?php
/**
 * Urun istifi (ana sayfa): on urun, kesim yuzu fotograflari yan yana.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="ik-section ik-stock" aria-labelledby="ik-stock-title">
	<div class="ik-wrap">
		<div class="ik-section__head">
			<div>
				<h2 class="ik-title" id="ik-stock-title" <?php nwcs_edit_attr( 'home', 'products', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'title' ) ); ?></h2>
				<p class="ik-lead" <?php nwcs_edit_attr( 'home', 'products', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'products', 'text' ) ); ?></p>
			</div>
			<a class="ik-textlink" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'products', 'all_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'products', 'all_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'products', 'all_label' ) ); ?>
				<?php ik_icon( 'arrow', 18 ); ?>
			</a>
		</div>

		<?php get_template_part( 'template-parts/product-grid', null, array( 'products' => ik_products() ) ); ?>
	</div>
</section>
